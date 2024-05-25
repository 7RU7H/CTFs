#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="belkin_icc"
CRON_TAB_FILE="/tmp/cron/cron.everyminute/belkin_icc_everyminute.sh"
PING_LOCATION="heartbeat.belkin.com"
DNS1_LOCATION="www.belkin.com"
DNS2_LOCATION="a.root-servers.net"
SYSEVENT_NAME="icc_internet_state"
LOCK_FILE="/tmp/service_belkin_icc.lock"
try_lock()
{
    local try_again="1"
    while [ "$try_again" = "1" ]; do
        ( set -o noclobber; echo "$$" > $LOCK_FILE ) 2> /dev/null
        if [ "$?" = "0" ]; then
            trap 'rm -f $LOCK_FILE; exit $?' INT TERM EXIT
            return 0
        fi
        local pid=`cat $LOCK_FILE`
        if [ ! -d "/proc/$pid" ]; then
            rm -rf $LOCK_FILE
        else
            try_again="0"
        fi
    done
    return 1
}
lock()
{
    local has_lock="0"
    while [ "$has_lock" = "0" ]; do
        try_lock
        if [ "$?" = "0" ]; then
            has_lock="1"
        fi
        sleep 1
    done
}
unlock()
{
    rm -rf $LOCK_FILE
    trap - INT TERM EXIT
}
set_sysevent_internet_up()
{
    local state=`sysevent get $SYSEVENT_NAME`
    if [ "$state" != "up" ]; then
        sysevent set $SYSEVENT_NAME up
    fi
}
set_sysevent_internet_down()
{
    local state=`sysevent get $SYSEVENT_NAME`
    if [ "$state" != "down" ]; then
        sysevent set $SYSEVENT_NAME down
    fi
}
do_ping()
{
    ( ping -q -c1 -w5 $PING_LOCATION &> /dev/null ) &
    local pid=$!
    sleep 5
    if [ -d "/proc/$pid" ]; then
        ( kill -9 $pid ) 2> /dev/null
    fi
    wait $pid
    return $?
}
do_nslookup()
{
    ( nslookup "$1" &> /dev/null ) &
    local pid=$!
    sleep 5
    if [ -d "/proc/$pid" ]; then
        ( kill -9 $pid ) 2> /dev/null
    fi
    wait $pid
    return $?
}
do_dns_queries()
{
    do_nslookup "$DNS1_LOCATION"
    if [ "$?" = "0" ]; then
        return 0
    fi
    do_nslookup "$DNS1_LOCATION"
    if [ "$?" = "0" ]; then
        return 0
    fi
    do_nslookup "$DNS2_LOCATION"
    if [ "$?" = "0" ]; then
        return 0
    fi
    do_nslookup "$DNS2_LOCATION"
    if [ "$?" = "0" ]; then
        return 0
    fi
    return 1
}
is_wan_ip_private()
{
    local ip=`sysevent get ipv4_wan_ipaddr`
    expr match "$ip" "192.168." > /dev/null
    if [ "$?" = "0" ]; then
        return 0
    fi
    expr match "$ip" "10." > /dev/null
    if [ "$?" = "0" ]; then
        return 0
    fi
    return 1
}
get_nat_wan2lan_pkt_count()
{
    PKT_COUNT=`iptables -L FORWARD -vx | grep -e " wan2lan" | awk '{print $1}'`
}
has_incoming_nat_traffic()
{
    get_last_wan2lan_pkt_count
    local last_count=$PKT_COUNT
    get_net_wan2lan_pkt_count
    local count=$PKT_COUNT
    if [ "$count" -gt "$last_count" ]; then
        return 0
    fi
    return 1
}
run_ping_test()
{
    do_ping
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    local sysevent_set=0
    local link
    local i
    while true; do
        for i in 1 2 3 4 5 6 7 8 9 10 11 12; do
            do_ping
            if [ "$?" = "0" ]; then
                set_sysevent_internet_up
                return 0
            fi
            if [ "$sysevent_set" = "0" ]; then
                set_sysevent_internet_down
                sysevent_set=1
            fi
        done
        wan_status=`sysevent get wan-status`
        if [ "$wan_status" != "started" ]; then
            set_sysevent_internet_down
            return 1
        fi
    done
}
run_dns_test()
{
    do_dns_queries
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    set_sysevent_internet_down
    do_dns_queries
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    return 1
}
run_nat_plus_dns_test()
{
    has_incoming_nat_traffic
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    do_dns_queries
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    has_incoming_nat_traffic
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    set_sysevent_internet_down
    do_dns_queries
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    has_incoming_nat_traffic
    if [ "$?" = "0" ]; then
        set_sysevent_internet_up
        return 0
    fi
    return 1
}
belkin_icc_check()
{
    try_lock
    if [ "$?" != "0" ]; then
        return
    fi
    if [ "$1" != "started" ]; then
        set_sysevent_internet_down
        unlock
        exit
    fi
    internet=`sysevent get $SYSEVENT_NAME`
    if [ "$internet" != "up" ]; then
        run_ping_test
    else
        run_dns_test
        if [ "$?" != "0" ]; then
            run_ping_test
        fi
    fi
    unlock
}
check_belkin_icc_enable()
{
    local enabled=`syscfg get belkin_icc_enabled`
    if [ "$enabled" != "1" ]; then
        service_stop
        exit 0
    fi
    bridge_mode=`syscfg get bridge_mode`
    if [ "$bridge_mode" != "0" ]; then
        service_stop
        exit 0
    fi
}
create_cron_file ()
{
(
cat <<'End-of-Text'
#!/bin/sh
/etc/init.d/service_belkin_icc.sh cron &
End-of-Text
) > $CRON_TAB_FILE
    echo "Belkin ICC Cron job created" > /dev/console
    return 0
}
service_start ()
{
    wait_till_end_state ${SERVICE_NAME}
    STATUS=`sysevent get ${SERVICE_NAME}-status`
    if [ "started" != "$STATUS" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo 
        sysevent set ${SERVICE_NAME}-status starting
        create_cron_file
        chmod +x $CRON_TAB_FILE
        check_err $? "Couldnt handle start"
        sysevent set ${SERVICE_NAME}-status started
    fi
}
service_stop ()
{
    wait_till_end_state ${SERVICE_NAME}
    STATUS=`sysevent get ${SERVICE_NAME}-status`
    if [ "stopped" != "$STATUS" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo 
        sysevent set ${SERVICE_NAME}-status stopping
        rm -rf $CRON_TAB_FILE
        check_err $? "Couldnt handle stop"
        sysevent set ${SERVICE_NAME}-status stopped
    fi
    set_sysevent_internet_down
}
case "$1" in
    ${SERVICE_NAME}-start)
        check_belkin_icc_enable
        service_start
        ;;
    ${SERVICE_NAME}-stop)
        check_belkin_icc_enable
        service_stop
        ;;
    ${SERVICE_NAME}-restart)
        check_belkin_icc_enable
        service_stop
        service_start
        ;;
    cron)
        check_belkin_icc_enable
        wan_status=`sysevent get wan-status`
        belkin_icc_check "$wan_status"
        ;;
    wan-status)
        check_belkin_icc_enable
        if [ "$2" == "started" ]; then
            service_start
            belkin_icc_check "$2"
        else
            service_stop
        fi
        ;;
    *)
        echo "Err: $1" > /dev/console
        echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart] | cron | wan-status <wan-status>" > /dev/console
        exit 3
        ;;
esac
