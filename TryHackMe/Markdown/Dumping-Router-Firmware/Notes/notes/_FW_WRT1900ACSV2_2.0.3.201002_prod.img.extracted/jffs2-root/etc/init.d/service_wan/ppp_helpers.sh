#!/bin/sh
PPP_PEERS_DIRECTORY=/etc/ppp/peers
PPPOE_PEERS_FILE=$PPP_PEERS_DIRECTORY"/utopia-pppoe"
PPP_OPTIONS_FILE=/etc/ppp/options
PPP_CHAP_SECRETS_FILE=/etc/ppp/chap-secrets
PPP_PAP_SECRETS_FILE=/etc/ppp/pap-secrets
BP_CONNECTED_SCRIPT=/tmp/bp-connected
BP_DISCONNECTED_SCRIPT=/tmp/bp-disconnected
prepare_pppd_ip_pre_up_script() {
   IP_PRE_UP_FILENAME=/etc/ppp/ip-pre-up
   echo -n > $IP_PRE_UP_FILENAME
cat << EOM >> $IP_PRE_UP_FILENAME
#!/bin/sh
source /etc/init.d/ulog_functions.sh
echo "[utopia][pppd ip-pre-up] Parameter 1: \$1 Parameter 2: \$2 Parameter 3: \$3" > /dev/console
echo "[utopia][pppd ip-pre-up] Parameter 4: \$4 Parameter 5: \$5 Parameter 6: \$6" > /dev/console
PPP_IPADDR=\$4
PPP_SUBNET=255.255.255.255
sysevent set wan_ppp_ifname \$1
echo "[utopia][pppd ip-pre-up] sysevent set pppd_current_wan_ifname \$1" > /dev/console
sysevent set pppd_current_wan_ifname \$1
echo "[utopia][pppd ip-pre-up] sysevent set pppd_current_wan_subnet \$PPP_SUBNET" > /dev/console
sysevent set pppd_current_wan_subnet \$PPP_SUBNET
echo "[utopia][pppd ip-pre-up] sysevent set pppd_current_wan_ipaddr \$PPP_IPADDR" > /dev/console
sysevent set pppd_current_wan_ipaddr \$PPP_IPADDR
sysevent set ppp_status preup 2>&1 > /dev/console
ulog ip-preup event "sysevent set ppp_status preup"
echo "[utopia][pppd ip-pre-up] sysevent set ppp_status preup <\`date\`>" > /dev/console
EOM
   chmod 777 $IP_PRE_UP_FILENAME
}
prepare_pppd_ip_up_script() {
   IP_UP_FILENAME=/etc/ppp/ip-up
   PPP_RESOLV_CONF=/etc/ppp/resolv.conf
   echo -n > $IP_UP_FILENAME
cat << EOM >> $IP_UP_FILENAME
#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/resolver_functions.sh
echo "[utopia][pppd ip-up] Interface: \$1 tty device name: \$2 tty device speed: \$3" > /dev/console
echo "[utopia][pppd ip-up] Local IP address: \$4 Remote IP Address: \$5 ipparam value: \$6" > /dev/console
PPP_IPADDR=\$4
PPP_SUBNET=255.255.255.255
sysevent set wan_ppp_ifname \$1
sysevent set ppp_local_ipaddr \$4
sysevent set ppp_remote_ipaddr \$5
sysevent set wan_default_gateway \$5
echo "[utopia][pppd ip-up] sysevent set pppd_current_wan_ifname \$1" > /dev/console
sysevent set pppd_current_wan_ifname \$1
echo "[utopia][pppd ip-up] sysevent set pppd_current_wan_subnet \$PPP_SUBNET" > /dev/console
sysevent set pppd_current_wan_subnet \$PPP_SUBNET
echo "[utopia][pppd ip-up] sysevent set pppd_current_wan_ipaddr \$PPP_IPADDR" > /dev/console
sysevent set pppd_current_wan_ipaddr \$PPP_IPADDR
if [ -f $PPP_RESOLV_CONF ]; then
   NS=
   for i in \`egrep nameserver /tmp/resolv.conf | egrep -v 127.0.0.1 | cut -d ' ' -f2-\`
   do
   NS="\$NS \$i"
   done
   
   sysevent set wan_dynamic_dns "\$NS"
   PPP_DNS=\`awk '{ print \$2 }' /etc/ppp/resolv.conf\`
   sysevent set wan_ppp_dns "\$PPP_DNS"
   echo "[utopia][pppd ip-up] sysevent set wan_ppp_dns \$PPP_DNS" > /dev/console
   prepare_resolver_conf
fi
sysevent set ppp_status up 2>&1 > /dev/console
ulog ip-up event "sysevent set ppp_status up"
echo "[utopia][pppd ip-up] sysevent set ppp_status up <\`date\`>" > /dev/console
EOM
   chmod 777 $IP_UP_FILENAME
}
prepare_pppd_ipv6_up_script() {
   IPV6_UP_FILENAME=/etc/ppp/ipv6-up
   IPV6_LOG_FILE=/var/log/ipv6.log
   echo -n > $IPV6_UP_FILENAME
cat << EOM >> $IPV6_UP_FILENAME
#!/bin/sh
source /etc/init.d/ulog_functions.sh
echo "[utopia][pppd ipv6-up] Congratulations PPPoE IPv6 is up" > /dev/console
echo "[utopia][pppd ipv6-up] Interface: \$1 tty device name: \$2 tty device speed: \$3" > /dev/console
echo "[utopia][pppd ipv6-up] Local link local address: \$4 Remote link local address: \$5 ipparam value: \$6" > /dev/console
IPV6_ROUTER_ADV=\`syscfg get router_adv_provisioning_enable\`
if [ "1" = "\$IPV6_ROUTER_ADV" ] ; then
   echo 2 > /proc/sys/net/ipv6/conf/\$1/accept_ra    # Accept RA even when forwarding is enabled
   echo 0 > /proc/sys/net/ipv6/conf/\$1/accept_ra_defrtr # Do not accept default route from RA as we'll set it manually to ppp0
   echo 1 > /proc/sys/net/ipv6/conf/\$1/accept_ra_pinfo # Accept prefix information for SLAAC
   echo 1 > /proc/sys/net/ipv6/conf/\$1/autoconf     # Do SLAAC
fi
ip -6 route add default via \$5 dev \$1
ulog ip-up event "ip -6 route add default via \$5 dev \$1"
echo "[utopia][pppd ipv6-up] <\`date\`>" > /dev/console
EOM
   chmod 777 $IPV6_UP_FILENAME
}
prepare_pppd_ip_down_script() {
   IP_DOWN_FILENAME=/etc/ppp/ip-down
   echo -n > $IP_DOWN_FILENAME
cat << EOM >> $IP_DOWN_FILENAME
#!/bin/sh
source /etc/init.d/ulog_functions.sh
echo "[utopia][pppd ip-down] unset wan_ppp_ifname " > /dev/console
sysevent set wan_ppp_ifname
sysevent set wan_pppoe_acname
sysevent set wan_pppoe_session_id
sysevent set ppp_status down
sysevent set ppp_local_ipaddr
sysevent set ppp_remote_ipaddr
sysevent set wan_default_gateway
ulog ip-down event "sysevent set ppp_status down"
echo "[utopia][pppd ip-down] <\`date\`>" > /dev/console
EOM
   chmod 777 $IP_DOWN_FILENAME
}
prepare_pppd_ipv6_down_script() {
   IPV6_DOWN_FILENAME=/etc/ppp/ipv6-down
   echo -n > $IPV6_DOWN_FILENAME
cat << EOM >> $IPV6_DOWN_FILENAME
#!/bin/sh
echo "[utopia][pppd ipv6-down] <\`date\`>" > /dev/console
EOM
   chmod 777 $IPV6_DOWN_FILENAME
}
prepare_pppd_options() {
   SYSCFG_ppp_debug=`syscfg get ppp_debug`
   echo -n > $PPP_OPTIONS_FILE
   if [ "demand" = "$SYSCFG_ppp_conn_method" ]; then
     if [ "l2tp" != "$SYSCFG_wan_proto" ]; then
       echo "demand" >> $PPP_OPTIONS_FILE
     fi
     PPP_IDLE_TIME=`expr $SYSCFG_ppp_idle_time \* 60`
     echo "idle $PPP_IDLE_TIME" >> $PPP_OPTIONS_FILE
     echo "ipcp-accept-remote" >> $PPP_OPTIONS_FILE
     echo "ipcp-accept-local" >> $PPP_OPTIONS_FILE
     echo "connect true" >> $PPP_OPTIONS_FILE
     echo "ktune" >> $PPP_OPTIONS_FILE
     echo "active-filter \"outbound and not ip multicast\"" >> $PPP_OPTIONS_FILE
   else
     if [ "l2tp" != "$SYSCFG_wan_proto" ]; then
       echo "persist" >> $PPP_OPTIONS_FILE
     fi
     echo "ipv6 ," >> $PPP_OPTIONS_FILE
   fi
   if [ "pppoe" = "$SYSCFG_wan_proto" ]; then
     if [ -n "$SYSCFG_ppp_lcp_echo_failure" ]; then
       echo "lcp-echo-failure $SYSCFG_ppp_lcp_echo_failure" >> $PPP_OPTIONS_FILE
     else
       echo "lcp-echo-failure 5" >> $PPP_OPTIONS_FILE
     fi
   fi
   if [ "pptp" = "$SYSCFG_wan_proto" ]; then
       echo "lcp-echo-failure 3" >> $PPP_OPTIONS_FILE
   fi
   if [ -z "$SYSCFG_ppp_keepalive_interval" ] || [ "0" = "$SYSCFG_ppp_keepalive_interval" ]; then
     echo "lcp-echo-interval 30" >> $PPP_OPTIONS_FILE
   else
     echo "lcp-echo-interval $SYSCFG_ppp_keepalive_interval" >> $PPP_OPTIONS_FILE
   fi
   echo "noipdefault" >> $PPP_OPTIONS_FILE
   if [ "1" = "$SYSCFG_default" ] ; then
     echo "defaultroute" >> $PPP_OPTIONS_FILE
   fi
   echo "usepeerdns" >> $PPP_OPTIONS_FILE
   echo "user \"$SYSCFG_wan_proto_username\"" >> $PPP_OPTIONS_FILE
   if [ -z "$SYSCFG_wan_mtu" ] || [ "0" = "$SYSCFG_wan_mtu" ]; then
     case "$SYSCFG_wan_proto" in
       pppoe)
       echo "mtu 1492" >> $PPP_OPTIONS_FILE
       ;;
       pptp | l2tp)
       echo "mtu 1460" >> $PPP_OPTIONS_FILE
       ;;
     esac
   else
      echo "mtu $SYSCFG_wan_mtu" >> $PPP_OPTIONS_FILE
   fi
   if [ "pppoe" = "$SYSCFG_wan_proto" ]; then
       echo "mru 1492" >> $PPP_OPTIONS_FILE
   fi
   echo "default-asyncmap" >> $PPP_OPTIONS_FILE
   echo "nopcomp" >> $PPP_OPTIONS_FILE
   echo "noaccomp" >> $PPP_OPTIONS_FILE
   echo "noccp" >> $PPP_OPTIONS_FILE
   echo "novj" >> $PPP_OPTIONS_FILE
   echo "nobsdcomp" >> $PPP_OPTIONS_FILE
   echo "nodeflate" >> $PPP_OPTIONS_FILE
   echo "lock" >> $PPP_OPTIONS_FILE
   echo "noauth" >> $PPP_OPTIONS_FILE
   if [ "1" = "$SYSCFG_ppp_debug" ]; then
      echo "debug" >> $PPP_OPTIONS_FILE
      echo "logfile /var/log/pppd.log" >> $PPP_OPTIONS_FILE
   fi
   if [ "$SYSCFG_wan_proto" = "pptp" ]; then
       echo "refuse-eap" >> $PPP_OPTIONS_FILE
   fi
}
prepare_pppd_secrets() {
   echo -n > $PPP_PAP_SECRETS_FILE
   echo -n > $PPP_CHAP_SECRETS_FILE
   REMOTE_NAME=$SYSCFG_wan_proto_remote_name
   if [ "" = "$REMOTE_NAME" ] ; then
      REMOTE_NAME=*
   fi
   LINE="\"$SYSCFG_wan_proto_username\" $REMOTE_NAME \"$SYSCFG_wan_proto_password\" *"
   echo "$LINE" >> $PPP_PAP_SECRETS_FILE
   echo "$LINE" >> $PPP_CHAP_SECRETS_FILE
   chmod 600 $PPP_PAP_SECRETS_FILE
   chmod 600 $PPP_CHAP_SECRETS_FILE
}
prepare_telstra_connected_script() {
   echo -n > $BP_CONNECTED_SCRIPT
cat << EOM >> $BP_CONNECTED_SCRIPT
#!/bin/sh
source /etc/init.d/ulog_functions.sh
echo "[utopia][bpalogin connected]" > /dev/console
sysevent set ppp_status up 2>&1 > /dev/console
ulog bp-up event "sysevent set ppp_status up"
echo "[utopia][bpalogin connected] sysevent set ppp_status up <\`date\`>" > /dev/console
EOM
   chmod 777 $BP_CONNECTED_SCRIPT
}
prepare_telstra_disconnected_script() {
   echo -n > $BP_DISCONNECTED_SCRIPT
cat << EOM >> $BP_DISCONNECTED_SCRIPT
#!/bin/sh
source /etc/init.d/ulog_functions.sh
echo "[utopia][bpalogin connected]" > /dev/console
sysevent set ppp_status down 2>&1 > /dev/console
ulog bp-down event "sysevent set ppp_status down"
echo "[utopia][bpalogin connected] sysevent set ppp_status down <\`date\`>" > /dev/console
EOM
   chmod 777 $BP_DISCONNECTED_SCRIPT
}
do_stop_wan_monitor() {
   pidof wmon > /dev/null
   if [ $? -eq 0 ] ; then
      killall -SIGQUIT wmon
     LOOP=1
     while [ "10" -gt "$LOOP" ] ; do 
        pidof wmon > /dev/null
        if [ $? -eq 0 ] ; then
           sleep 1
           LOOP=`expr $LOOP + 1`
        else
        LOOP=10
        fi
     done
	 LOOP=1
     while [ "10" -gt "$LOOP" ] ; do
        pidof pppd > /dev/null
        if [ $? -eq 0 ] ; then
            sleep 1
            LOOP=`expr $LOOP + 1`
        else
            return 0
        fi
     done
   fi
}
do_start_wan_monitor() {
   do_stop_wan_monitor
   /sbin/wmon $LAN_IFNAME $1
}
