#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME=devidentd
DEVREGEX_FILE=devregex.json
DOWNLOADED_DEVREGEX_FILE=/tmp/var/config/devidentd/devregex.json
DEVICE_PRUNE_SCRIPT=/etc/init.d/service_devidentd/deviceprune.lua
PRUNE_CRON_FILE=/etc/cron/cron.daily/deviceprune_daily.lua
BIN=devidentd
PID_FILE=/var/run/${SERVICE_NAME}.pid
PMON=/etc/init.d/pmon.sh
service_start ()
{
    eval `utctx_cmd get bridge_mode`
    if [ "$SYSCFG_bridge_mode" != "0" ] ; then
        ulog ${SERVICE_NAME} status "not running ${SERVICE_NAME} service because router is in bridge mode."
        return 0
    fi
    if [ ! -e /tmp/${DEVREGEX_FILE} ] ; then
        ln -s /etc/${DEVREGEX_FILE} /tmp/${DEVREGEX_FILE}
    fi
    wait_till_end_state ${SERVICE_NAME}
    STATUS=`sysevent get ${SERVICE_NAME}-status`
    if [ "started" != "$STATUS" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo
        sysevent set ${SERVICE_NAME}-status starting
        ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service"
        eval `utctx_cmd get lan_ifname`
        ${BIN} -x ${DOWNLOADED_DEVREGEX_FILE} -r /tmp/${DEVREGEX_FILE} -p ${PID_FILE} ${SYSCFG_lan_ifname}
        echo 1 > /proc/sys/net/ipv4/conf/all/arp_accept
        check_err $? "Couldnt handle start"
        sysevent set ${SERVICE_NAME}-status started
    fi
    if [ ! -e ${PRUNE_CRON_FILE} ] ; then
        ln -s ${DEVICE_PRUNE_SCRIPT} ${PRUNE_CRON_FILE}
    fi
    $PMON setproc ${SERVICE_NAME} $BIN $PID_FILE "/etc/init.d/service_${SERVICE_NAME}.sh ${SERVICE_NAME}-restart"
}
service_stop ()
{
    if [ -e ${PRUNE_CRON_FILE} ] ; then
        rm ${PRUNE_CRON_FILE}
    fi
    wait_till_end_state ${SERVICE_NAME}
    STATUS=`sysevent get ${SERVICE_NAME}-status`
    if [ "stopped" != "$STATUS" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo
        sysevent set ${SERVICE_NAME}-status stopping
        ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service"
        kill -TERM $(cat $PID_FILE)
        echo 0 > /proc/sys/net/ipv4/conf/all/arp_accept
        check_err $? "Couldnt handle stop"
        sysevent set ${SERVICE_NAME}-status stopped
    fi
    rm -f $PID_FILE
    $PMON unsetproc ${SERVICE_NAME}
}
service_restart ()
{
    service_stop
    service_start
}
update_device ()
{
    /etc/init.d/service_devidentd/deviceupdate.lua
    if [ "$?" -ne "0" ] ; then
        ulog_error ${SERVICE_NAME} status "failed to update local device with error $?"
    fi
}
case "$1" in
    ${SERVICE_NAME}-start)
        service_start
        ;;
    ${SERVICE_NAME}-stop)
        service_stop
        ;;
    ${SERVICE_NAME}-restart)
        service_restart
        ;;
    lan-started)
        ulog ${SERVICE_NAME} status "${SERVICE_NAME} service, triggered by $1"
        service_restart
        update_device
        ;;
    lan-stopping)
        ulog ${SERVICE_NAME} status "${SERVICE_NAME} service, triggered by $1"
        service_stop
        ;;
    dns-restart)
        ulog ${SERVICE_NAME} status "${SERVICE_NAME} updating local device, triggered by $1"
        update_device
        ;;
    *)
        echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" >&2
        exit 3
        ;;
esac
