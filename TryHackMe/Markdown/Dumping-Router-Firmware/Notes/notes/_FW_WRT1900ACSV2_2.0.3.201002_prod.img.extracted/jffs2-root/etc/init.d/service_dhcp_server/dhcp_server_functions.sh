#!/bin/sh
DHCP_CONF=/etc/dnsmasq.conf
DHCP_STATIC_HOSTS_FILE=/etc/dhcp_static_hosts
DHCP_OPTIONS_FILE=/etc/dhcp_options
LOCAL_DHCP_CONF=/tmp/dnsmasq.conf$$
LOCAL_DHCP_STATIC_HOSTS_FILE=/tmp/dhcp_static_hosts$$
LOCAL_DHCP_OPTIONS_FILE=/tmp/dhcp_options$$
RESOLV_CONF=/etc/resolv.conf
LOG_FILE=/tmp/udhcp.log
SLOW_START_NUM_TRIES_1=6
SLOW_START_NUM_TRIES_2=8
SLOW_START_NUM_TRIES_3=10
SYSCFG_lan_ifname=`syscfg get lan_ifname`
SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
SYSCFG_guest_enabled=`syscfg get guest_enabled`
DHCP_LEASE_FILE=/etc/dnsmasq.leases
DHCP_ACTION_SCRIPT=/etc/init.d/service_dhcp_server/dnsmasq_dhcp.script
CLOUD_DNS_NAMES_FILE=/etc/cloud_dns_names
SYSEVENT_lan_ipaddr=`sysevent get lan_ipaddr`
SYSEVENT_lan_prefix_len=`sysevent get lan_prefix_len`
if [ -n "${SYSEVENT_lan_ipaddr}" ] ; then
    eval `ipcalc -n ${SYSEVENT_lan_ipaddr}/${SYSEVENT_lan_prefix_len}`
fi
LAN_NETWORK=$NETWORK
SYSCFG_dhcp_num=`syscfg get dhcp_num`
if [ "" = "$SYSCFG_dhcp_num" ] ; then
   SYSCFG_dhcp_num=0
fi
SYSCFG_dhcp_start=`syscfg get dhcp_start`
SYSCFG_dhcp_end=`syscfg get dhcp_end`
if [ -z "$SYSCFG_dhcp_end" ] ; then
   SYSCFG_dhcp_end=`dc $SYSCFG_dhcp_start $SYSCFG_dhcp_num + 1 - p`
fi
make_ip_using_subnet $LAN_NETWORK $SYSEVENT_lan_prefix_len $SYSCFG_dhcp_start
DHCP_START_ADDR=$CREATED_IP_ADDRESS
make_ip_using_subnet $LAN_NETWORK $SYSEVENT_lan_prefix_len $SYSCFG_dhcp_end
DHCP_END_ADDR=$CREATED_IP_ADDRESS
GUEST_MAX_ALLOWED_LIMIT=`syscfg get guest_max_allowed_limit`
GUEST_DHCP_NUM=`expr $GUEST_MAX_ALLOWED_LIMIT + 10`
prepare_well_known_dns () {
   FOO=`utctx_cmd get lan_ifname dot_local_domain dot_local_hostname ula_enable lan_ula_prefix ui::remote_host cloud::host routerstatus::host xmpp_host device::modelNumber private_domain`
   eval $FOO
   if [ -n "$SYSCFG_dot_local_hostname" -a -n "$SYSCFG_dot_local_domain" ]
   then
      DOT_LOCAL_NAME=${SYSCFG_dot_local_hostname}.${SYSCFG_dot_local_domain}
   fi
   if [ -n "$SYSCFG_device_modelNumber" -a -n "$SYSCFG_private_domain" ]
   then
      LOCAL_NAME=${SYSCFG_device_modelNumber}.${SYSCFG_private_domain}
   fi
   if [ -n "$SYSCFG_ui_remote_host" ]
   then
      echo "server=/${SYSCFG_ui_remote_host}/#" >> $1
   fi
   if [ -n "$SYSCFG_cloud_host" -a "$SYSCFG_cloud_host" != "$SYSCFG_ui_remote_host" ]
   then
      echo "server=/${SYSCFG_cloud_host}/#" >> $1
   fi
   if [ -n "$SYSCFG_routerstatus_host" ]
   then
      echo "server=/${SYSCFG_routerstatus_host}/#" >> $1
   fi
   if [ -z "${SYSCFG_cloud_host##*.cloud1*}" ] ; then
      echo "server=/${SYSCFG_cloud_host/.cloud1/}/#" >> $1
   else
      echo "server=/${SYSCFG_cloud_host/cloud1./cloud.}/#" >> $1
   fi
   if [ -n "$SYSCFG_xmpp_host" -a "$SYSCFG_xmpp_host" != "$SYSCFG_ui_remote_host" -a "$SYSCFG_xmpp_host" != "$SYSCFG_cloud_host" ]
   then
      echo "server=/${SYSCFG_xmpp_host}/#" >> $1
   fi
   if [ -n "$DOT_LOCAL_NAME" ] ; then
      echo "address=/${DOT_LOCAL_NAME}/${SYSCFG_lan_ipaddr}" >> $1
   fi
   if [ -n "$LOCAL_NAME" ] ; then
      echo "address=/${LOCAL_NAME}/${SYSCFG_lan_ipaddr}" >> $1
   fi
   if [ -f ${CLOUD_DNS_NAMES_FILE} ] ; then
      while read line ; do
         echo "address=/${line}/${SYSCFG_lan_ipaddr}" >> $1;
      done < ${CLOUD_DNS_NAMES_FILE}
   fi
   if [ "1" = "$SYSCFG_ula_enable" -a -n "$SYSCFG_lan_ula_prefix" ] ; then
      eval `ip6calc -p $SYSCFG_lan_ula_prefix`
      PREFIX_LEN=${#PREFIX}
      if [ 1 -lt "$PREFIX_LEN" ] ; then
         PREFIX_LEN=`expr $PREFIX_LEN - 1`
         PREFIX=${PREFIX:0:$PREFIX_LEN}
      fi
      LOCAL_IPS=`/sbin/ip -6 addr show dev $SYSCFG_lan_ifname  | grep "inet6 " | grep $PREFIX | awk '{split($2,foo, "/"); print(foo[1]); }'`
      if [ -n "$LOCAL_IPS" ] ; then
         for LOCAL_IP in $LOCAL_IPS ; do
            if [ -n "$DOT_LOCAL_NAME" ] ; then
               echo "address=/${DOT_LOCAL_NAME}/${LOCAL_IP}" >> $1
            fi
            if [ -n "$LOCAL_NAME" ] ; then
               echo "address=/${LOCAL_NAME}/${LOCAL_IP}" >> $1
            fi
            if [ -f ${CLOUD_DNS_NAMES_FILE} ] ; then
               while read line ; do
                  echo "address=/${line}/${LOCAL_IP}" >> $1;
               done < ${CLOUD_DNS_NAMES_FILE}
            fi
         done
      fi
   fi
}
prepare_dns_a_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_a`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_a $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         last_octet=`echo $value | cut -d, -f2`
         make_ip_using_subnet $LAN_NETWORK $SYSEVENT_lan_prefix_len $last_octet
         ip=$CREATED_IP_ADDRESS
         echo "address=/${name}/${ip}" >> $1
      fi
      iterator=`sysevent getiterator dns_a $iterator`
   done
}
prepare_dns_a_full_ip_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_a_full_ip`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_a_full_ip $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         ip=`echo $value | cut -d, -f2`
         echo "address=/${name}/${ip}" >> $1
      fi
      iterator=`sysevent getiterator dns_a_full_ip $iterator`
   done
}
prepare_dns_aaaa_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_aaaa`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_aaaa $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         ip=`echo $value | cut -d, -f2`
         echo "address=/${name}/${ip}" >> $1
      fi
      iterator=`sysevent getiterator dns_aaaa $iterator`
   done
}
prepare_dns_srv_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_srv`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_srv $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         target=`echo $value | cut -d, -f2`
         port=`echo $value | cut -d, -f3`
         prio=`echo $value | cut -d, -f4`
         if [ -z "$prio" ] ; then
            prio=0
         fi
         echo "srv-host=$name,$target,$port,$prio" >> $1
      fi
      iterator=`sysevent getiterator dns_srv $iterator`
   done
}
prepare_dns_ptr_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_ptr`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_ptr $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         txt=`echo $value | cut -d, -f2`
         echo "ptr-record=$name,\"${txt}\"" >> $1
      fi
      iterator=`sysevent getiterator dns_ptr $iterator`
   done
}
prepare_dns_txt_from_sysevent_pool () {
   iterator=`sysevent getiterator dns_txt`
   while [ "4294967295" != "$iterator" ]
   do
      value=`sysevent getunique dns_txt $iterator`
      if [ -n "$value" ] ; then
         name=`echo $value | cut -d, -f1`
         raw_rdata1=`echo $value | cut -d{ -f2`
         rdata=`echo $raw_rdata1 | cut -d} -f1`
         echo "txt-record=$name,${rdata}" >> $1
      fi
      iterator=`sysevent getiterator dns_txt $iterator`
   done
}
calculate_lease_time() {
   DHCP_SLOW_START_1_FILE=/etc/cron/cron.everyminute/dhcp_slow_start.sh
   DHCP_SLOW_START_2_FILE=/etc/cron/cron.every5minute/dhcp_slow_start.sh
   DHCP_SLOW_START_3_FILE=/etc/cron/cron.every10minute/dhcp_slow_start.sh
   DHCP_LEASE_TIME=
   SLOW_START=`syscfg get dhcp_server_slow_start`
   if [ "1" = "$SLOW_START" -a "started" != "`sysevent get ntpclient-status`" ] ; then
      DHCP_SLOW_START_NEEDED=1
   fi
   PROPAGATE_NS=`syscfg get dhcp_server_propagate_wan_nameserver`
   if [ "1" != "$PROPAGATE_NS" ] ; then
      PROPAGATE_NS=`syscfg get block_nat_redirection`
   fi
   if [ "1" = "$DHCP_SLOW_START_NEEDED" ] ; then
      DHCP_SLOW_START_QUANTA=`sysevent get dhcp_slow_start_quanta`
      if [ -z "$DHCP_SLOW_START_QUANTA" ] ; then
         TIME_FILE=$DHCP_SLOW_START_1_FILE
         sysevent set dhcp_slow_start_quanta 1
         DHCP_LEASE_TIME=1m
      else
         if [ "$DHCP_SLOW_START_QUANTA" -lt "$SLOW_START_NUM_TRIES_1" ] ; then
            TIME_FILE=$DHCP_SLOW_START_1_FILE
            DHCP_LEASE_TIME=1m
         elif [ "$DHCP_SLOW_START_QUANTA" -eq "$SLOW_START_NUM_TRIES_1" ] ; then
            TIME_FILE=$DHCP_SLOW_START_2_FILE
            sysevent set dhcp_slow_start_quanta 6
            DHCP_LEASE_TIME=5m
         elif [ "$DHCP_SLOW_START_QUANTA" -lt "$SLOW_START_NUM_TRIES_2" ] ; then
            TIME_FILE=$DHCP_SLOW_START_2_FILE
            DHCP_LEASE_TIME=5m
         elif [ "$DHCP_SLOW_START_QUANTA" -eq "$SLOW_START_NUM_TRIES_2" ] ; then
            TIME_FILE=$DHCP_SLOW_START_3_FILE
            sysevent set dhcp_slow_start_quanta 8
            DHCP_LEASE_TIME=10m
         elif [ "$DHCP_SLOW_START_QUANTA" -lt "$SLOW_START_NUM_TRIES_3" ] ; then
            TIME_FILE=$DHCP_SLOW_START_3_FILE
            DHCP_LEASE_TIME=10m
         elif [ "$DHCP_SLOW_START_QUANTA" -ge "$SLOW_START_NUM_TRIES_3" ] ; then
            DHCP_SLOW_START_NEEDED=
            TIME_FILE=
            sysevent set dhcp_slow_start_quanta
            DHCP_LEASE_TIME=
         fi
      fi
   else
      sysevent set dhcp_slow_start_quanta
   fi
   if [ -z "$DHCP_LEASE_TIME" ] ; then
      DHCP_LEASE_TIME=`syscfg get dhcp_lease_time`
   fi
   if [ -z "$DHCP_LEASE_TIME" ] ; then
      DHCP_LEASE_TIME=86400
   fi
   case $DHCP_LEASE_TIME in
       *h) DHCP_LEASE_TIME=$(expr ${DHCP_LEASE_TIME%%h} \* 3600)
           ;;
       *m) DHCP_LEASE_TIME=$(expr ${DHCP_LEASE_TIME%%m} \* 60)
           ;;
       *s) DHCP_LEASE_TIME=${DHCP_LEASE_TIME%%s}
           ;;
   esac
   sysevent set lan_dhcp_lease $DHCP_LEASE_TIME
}
create_slow_start_file () {
   echo -n > $1
   echo "#!/bin/sh" >> $1
   echo "TEST=\`sysevent get ntpclient-status\`" >> $1
   echo "if [ \"started\" = \"\$TEST\" ] ; then" >> $1
   echo "   sysevent set dhcp_slow_start_quanta" >> $1
   echo "   sysevent set dhcp_server-restart" >> $1
   echo "   rm -f $1" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "DHCP_SLOW_START_QUANTA=\`sysevent get dhcp_slow_start_quanta\`" >> $1
   echo "if [ -z \"\$DHCP_SLOW_START_QUANTA\" ] ; then" >> $1
   echo "   rm -f $1" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "if [ \"\$DHCP_SLOW_START_QUANTA\" -lt \"$SLOW_START_NUM_TRIES_1\" ] ; then" >> $1
   echo "   VAL=\`expr \$DHCP_SLOW_START_QUANTA + 1\`" >> $1
   echo "   sysevent set dhcp_slow_start_quanta \$VAL" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "if [ \"\$DHCP_SLOW_START_QUANTA\" -eq \"$SLOW_START_NUM_TRIES_1\" ] ; then" >> $1
   echo "   rm -f $1" >> $1
   echo "   sysevent set dhcp_server-restart" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "if [ \"\$DHCP_SLOW_START_QUANTA\" -lt \"$SLOW_START_NUM_TRIES_2\" ] ; then" >> $1
   echo "   VAL=\`expr \$DHCP_SLOW_START_QUANTA + 1\`" >> $1
   echo "   sysevent set dhcp_slow_start_quanta \$VAL" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "if [ \"\$DHCP_SLOW_START_QUANTA\" -eq \"$SLOW_START_NUM_TRIES_2\" ] ; then" >> $1
   echo "   rm -f $1" >> $1
   echo "   sysevent set dhcp_server-restart" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "if [ \"\$DHCP_SLOW_START_QUANTA\" -lt \"$SLOW_START_NUM_TRIES_3\" ] ; then" >> $1
   echo "   VAL=\`expr \$DHCP_SLOW_START_QUANTA + 1\`" >> $1
   echo "   sysevent set dhcp_slow_start_quanta \$VAL" >> $1
   echo "   exit" >> $1
   echo "fi" >> $1
   echo "   rm -f $1" >> $1
   echo "   sysevent set dhcp_server-restart" >> $1
}
calculate_guest_dhcp_range () {
   GUEST_DHCP_START=`syscfg get guest_dhcp_start`
   if [ "$GUEST_DHCP_START" -lt "2" ] ; then
      GUEST_DHCP_START=2
   fi
   GUEST_DHCP_END=`expr $GUEST_DHCP_START + $GUEST_DHCP_NUM`
   GUEST_DHCP_END=`expr $GUEST_DHCP_END - 1`
   GUEST_NETWORK_ADDR=`echo $GUEST_LAN_IPADDR | cut -f 1,2,3 -d '.'`
   GUEST_DHCP_START_ADDR=$GUEST_NETWORK_ADDR.$GUEST_DHCP_START
   GUEST_DHCP_END_ADDR=$GUEST_NETWORK_ADDR.$GUEST_DHCP_END
}
prepare_dhcp_conf_static_hosts() {
   SYSCFG_num_static_hosts=`syscfg get dhcp_num_static_hosts`
   echo -n > $LOCAL_DHCP_STATIC_HOSTS_FILE
   for N in $(seq 1 $SYSCFG_num_static_hosts)
   do
      HOST_LINE=`syscfg get dhcp_static_host_$N`
      if [ -n "$HOST_LINE" -a "none" != "$HOST_LINE" ] ; then
         MAC=""
         SAVEIFS=$IFS
         IFS=,
         set -- $HOST_LINE
         MAC=$1
         shift
         make_ip_using_subnet $LAN_NETWORK $SYSEVENT_lan_prefix_len $1
         IP=$CREATED_IP_ADDRESS
         shift
         FRIENDLY_NAME=$1
         IFS=$SAVEIFS
         echo "$MAC,$IP,$FRIENDLY_NAME" >> $LOCAL_DHCP_STATIC_HOSTS_FILE
      fi
   done
   HOST_LINE=`syscfg get lan_ipaddr`
   if [ -n "$HOST_LINE" ]
   then
      echo "lan_ipaddr, $HOST_LINE" >> $LOCAL_DHCP_STATIC_HOSTS_FILE
   fi
   HOST_LINE=`syscfg get guest_lan_ipaddr`
   if [ -n "$HOST_LINE" ]
   then
      echo "guest_lan_ipaddr, $HOST_LINE" >> $LOCAL_DHCP_STATIC_HOSTS_FILE
   fi
   cat $LOCAL_DHCP_STATIC_HOSTS_FILE > $DHCP_STATIC_HOSTS_FILE
   rm -f $LOCAL_DHCP_STATIC_HOSTS_FILE
}
prepare_dhcp_options_nameservers() {
   if [ -z "$1" ] ; then
     return
   fi
   DHCP_OPTION_NAMESERVER_STR="tag:$SYSCFG_lan_ifname,option:dns-server"
   SYSCFG_dhcp_server_propagate_wan_nameserver=`syscfg get dhcp_server_propagate_wan_nameserver`
   if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
      for i in wan_1 wan_2 wan_3
      do
         wan_info_by_namespace $i
         if [ "0" = "$?" ] ; then
            if [ "static" = "$SYSCFG_wan_proto" -a "1" = "$SYSCFG_forwarding" ] ; then
               if [ -n "$SYSCFG_nameserver1" -a "0.0.0.0" != "$SYSCFG_nameserver1" ] ; then
                  DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$SYSCFG_nameserver1
               fi
               if [ -n "$SYSCFG_nameserver2" -a "0.0.0.0" != "$SYSCFG_nameserver2" ] ; then
                  DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$SYSCFG_nameserver2
               fi
               if [ -n "$SYSCFG_nameserver3" -a "0.0.0.0" != "$SYSCFG_nameserver3" ] ; then
                  DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$SYSCFG_nameserver3
               fi
            fi
         fi
      done
      for i in wan_1 wan_2 wan_3
      do
         wan_info_by_namespace $i
         INTERFACE_DHCP_LOG_FILE="/tmp/"${i}"_udhcp.log"
         if [ -f "$INTERFACE_DHCP_LOG_FILE" ] ; then
            NS=` grep "dns server" $INTERFACE_DHCP_LOG_FILE | awk '{print $4}'`
            if [ -n "$NS" ] ; then
               DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$NS
            fi
            NS=` grep "dns server" $INTERFACE_DHCP_LOG_FILE | awk '{print $5}'`
            if [ -n "$NS" ] ; then
               DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$NS
            fi
            NS=` grep "dns server" $INTERFACE_DHCP_LOG_FILE | awk '{print $6}'`
            if [ -n "$NS" ] ; then
               DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$NS
            fi
         fi
      done
   fi
   NS=` syscfg get lan_ipaddr `
   if [ -n "$NS" ] ; then
      DHCP_OPTION_NAMESERVER_STR=$DHCP_OPTION_NAMESERVER_STR","$NS
   fi
   echo $DHCP_OPTION_NAMESERVER_STR >> $1
}
prepare_dhcp_options() {
   echo -n > $LOCAL_DHCP_OPTIONS_FILE
   WAN_PROTO=`syscfg get wan_proto`
   GW_IP=`syscfg get lan_ipaddr`
   if [ "" != $GW_IP -a "0.0.0.0" != $GW_IP ] ; then
      echo "tag:$SYSCFG_lan_ifname,option:router,$GW_IP" >> $LOCAL_DHCP_OPTIONS_FILE
   fi
   prepare_dhcp_options_nameservers $LOCAL_DHCP_OPTIONS_FILE
   WINS_SERVER=`syscfg get dhcp_wins_server`
   if [ "" != "$WINS_SERVER" ] && [ "0.0.0.0" != "$WINS_SERVER" ] ; then
      echo "option:netbios-ns,"$WINS_SERVER >> $LOCAL_DHCP_OPTIONS_FILE
   fi
}
prepare_guest_dhcp_options() {
   WAN_PROTO=`syscfg get wan_proto`
   DHCP_OPTION_STR=
   DNS_OPTION_STR="tag:$SYSCFG_guest_lan_ifname,option:dns-server"
   GW_IP=`syscfg get guest_lan_ipaddr`
   if [ "" != $GW_IP -a "0.0.0.0" != $GW_IP ] ; then
      echo "tag:$SYSCFG_guest_lan_ifname,option:router,$GW_IP" >> $LOCAL_DHCP_OPTIONS_FILE
   fi
   if [ "static" = "$WAN_PROTO" ] && [ "1" = "$PROPAGATE_NS" ] ; then
      NS=`syscfg get nameserver1`
      if [ "0.0.0.0" != "$NS" ]  && [ "" != "$NS" ] ; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
            DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
         fi
      fi
      NS=`syscfg get nameserver2`
      if [ "0.0.0.0" != "$NS" ] && [ "" != "$NS" ] ; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
            DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
         fi
      fi
      NS=`syscfg get nameserver3`
      if [ "0.0.0.0" != "$NS" ] && [ "" != "$NS" ] ; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
           DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
         fi
      fi
   else
      NAMESERVER1=`syscfg get dhcp_nameserver_1`
      NAMESERVER2=`syscfg get dhcp_nameserver_2`
      NAMESERVER3=`syscfg get dhcp_nameserver_3`
      if [ "0.0.0.0" != "$NAMESERVER1" ] && [ "" != "$NAMESERVER1" ] ; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
            DHCP_OPTION_STR="$DNS_OPTION_STR, "$NAMESERVER1
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NAMESERVER1
         fi
      fi
      if [ "0.0.0.0" != "$NAMESERVER2" ]  && [ "" != "$NAMESERVER2" ]; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
            DHCP_OPTION_STR="$DNS_OPTION_STR, "$NAMESERVER2
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NAMESERVER2
         fi
      fi
      if [ "0.0.0.0" != "$NAMESERVER3" ]  && [ "" != "$NAMESERVER3" ]; then
         if [ "" = "$DHCP_OPTION_STR" ] ; then
            DHCP_OPTION_STR="$DNS_OPTION_STR, "$NAMESERVER3
         else
            DHCP_OPTION_STR=$DHCP_OPTION_STR","$NAMESERVER3
         fi
      fi
      if [ "1" = "$PROPAGATE_NS" ] ; then
         NS=` grep "dns server" $LOG_FILE | awk '{print $4}'`
         if [ "" != "$NS" ] ; then
            if [ "" = "$DHCP_OPTION_STR" ] ; then
               DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
            else
               DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
            fi
         fi
         NS=` grep "dns server" $LOG_FILE | awk '{print $5}'`
         if [ "" != "$NS" ] ; then
            if [ "" = "$DHCP_OPTION_STR" ] ; then
               DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
            else
               DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
            fi
         fi
         NS=` grep "dns server" $LOG_FILE | awk '{print $6}'`
         if [ "" != "$NS" ] ; then
            if [ "" = "$DHCP_OPTION_STR" ] ; then
               DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
            else
               DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
            fi
         fi
      fi
   fi
   NS=$GW_IP
   if [ "" != "$NS" ] ; then
      if [ "" = "$DHCP_OPTION_STR" ] ; then
         DHCP_OPTION_STR="$DNS_OPTION_STR, "$NS
      else
         DHCP_OPTION_STR=$DHCP_OPTION_STR","$NS
      fi
   fi
   echo $DHCP_OPTION_STR >> $LOCAL_DHCP_OPTIONS_FILE
   WINS_SERVER=`syscfg get dhcp_wins_server`
   if [ "" != "$WINS_SERVER" ] && [ "0.0.0.0" != "$WINS_SERVER" ] ; then
      echo "option:netbios-ns,"$WINS_SERVER >> $LOCAL_DHCP_OPTIONS_FILE
   fi
}
prepare_guest_dhcp_conf () {
   GUEST_DHCP_LEASE_TIME=`syscfg get guest_dhcp_lease_time`
   GUEST_LAN_IPADDR=`syscfg get guest_lan_ipaddr`
   GUEST_LAN_NETMASK=`syscfg get guest_lan_netmask`
   CONNECTOR_GUEST_IP=`syscfg get guest_connector_lan_ipaddr`
   if [ -z "$GUEST_DHCP_LEASE_TIME" ]; then
      GUEST_DHCP_LEASE_TIME=3600
   fi
   if [ -n "$DHCP_LEASE_TIME" ] && [ $DHCP_LEASE_TIME -lt $GUEST_DHCP_LEASE_TIME ]; then
      GUEST_DHCP_LEASE_TIME=$DHCP_LEASE_TIME
   fi
   calculate_guest_dhcp_range
   echo "interface=$SYSCFG_guest_lan_ifname" >> $LOCAL_DHCP_CONF
   echo "dhcp-range=net:$SYSCFG_guest_lan_ifname,$GUEST_DHCP_START_ADDR,$GUEST_DHCP_END_ADDR,$GUEST_LAN_NETMASK,$GUEST_DHCP_LEASE_TIME" >> $LOCAL_DHCP_CONF
   if [ "`syscfg get bridge_mode`" != "0" ]; then
      echo "dhcp-leasefile=$DHCP_LEASE_FILE" >> $LOCAL_DHCP_CONF
      echo "dhcp-script=$DHCP_ACTION_SCRIPT" >> $LOCAL_DHCP_CONF
      echo "dhcp-lease-max=$GUEST_DHCP_NUM" >> $LOCAL_DHCP_CONF
      echo "dhcp-hostsfile=$DHCP_STATIC_HOSTS_FILE" >> $LOCAL_DHCP_CONF
      echo "dhcp-optsfile=$DHCP_OPTIONS_FILE" >> $LOCAL_DHCP_CONF
      if [ "$LOG_LEVEL" -gt 1 ] ; then
        echo "log-dhcp" >> $LOCAL_DHCP_CONF
      fi
   fi
   if [ ! -z $CONNECTOR_GUEST_IP ] && [ "" != "$CONNECTOR_GUEST_IP" ] &&
      [ "0.0.0.0" != "$CONNECTOR_GUEST_IP" ] ; then
      GUEST_GW_IP=$CONNECTOR_GUEST_IP
   else
      GUEST_GW_IP=$GUEST_LAN_IPADDR
   fi
}
prepare_dhcp_conf () {
   if [ "$3" = "dns_only" ] ; then
      PREFIX=#
   else
      PREFIX=
   fi
   DHCP_LEASE_MAX=`expr $SYSCFG_dhcp_num + $GUEST_DHCP_NUM`
   echo -n > $DHCP_STATIC_HOSTS_FILE
   calculate_lease_time
   echo -n > $LOCAL_DHCP_CONF
   echo "domain-needed" >> $LOCAL_DHCP_CONF
   echo "bogus-priv" >> $LOCAL_DHCP_CONF
   echo "resolv-file=$RESOLV_CONF" >> $LOCAL_DHCP_CONF
   echo "interface=$SYSCFG_lan_ifname" >> $LOCAL_DHCP_CONF
   echo "interface=tun*" >> $LOCAL_DHCP_CONF
   echo "expand-hosts" >> $LOCAL_DHCP_CONF
   echo "max-cache-ttl=3600" >> $LOCAL_DHCP_CONF
   echo "no-negcache" >> $LOCAL_DHCP_CONF
   echo "cache-size=4096" >> $LOCAL_DHCP_CONF
   ROUTER_DNS_DOMAIN=`syscfg get router_dns_domain`
   if [ "" = "$ROUTER_DNS_DOMAIN" ] ; then
      ROUTER_DNS_DOMAIN=`sysevent get dhcp_domain`
   fi
   if [ "" != "$ROUTER_DNS_DOMAIN" ] ; then
      echo "domain=$ROUTER_DNS_DOMAIN" >> $LOCAL_DHCP_CONF
   fi
   LOG_LEVEL=`syscfg get log_level`
   if [ "" = "$LOG_LEVEL" ] ; then
       LOG_LEVEL=1
   fi
   if [ "$3" = "dns_only" ] ; then
      echo "no-dhcp-interface=$SYSCFG_lan_ifname" >> $LOCAL_DHCP_CONF
      echo "no-dhcp-interface=$SYSCFG_guest_lan_ifname" >> $LOCAL_DHCP_CONF
   fi
   echo "dhcp-leasefile=$DHCP_LEASE_FILE" >> $LOCAL_DHCP_CONF
   echo "$PREFIX""dhcp-range=net:$SYSCFG_lan_ifname,$DHCP_START_ADDR,$DHCP_END_ADDR,$2,$DHCP_LEASE_TIME" >> $LOCAL_DHCP_CONF
   echo "$PREFIX""dhcp-script=$DHCP_ACTION_SCRIPT" >> $LOCAL_DHCP_CONF
   echo "$PREFIX""dhcp-lease-max=$DHCP_LEASE_MAX" >> $LOCAL_DHCP_CONF
   echo "$PREFIX""dhcp-hostsfile=$DHCP_STATIC_HOSTS_FILE" >> $LOCAL_DHCP_CONF
   echo "$PREFIX""dhcp-optsfile=$DHCP_OPTIONS_FILE" >> $LOCAL_DHCP_CONF
   OUI=`syscfg get OUI`
   echo "$PREFIX""dhcp-option-force=cpewan-id,vi-encap:3561,4,\"$OUI\"" >> $LOCAL_DHCP_CONF
   Serial=`syscfg get device::serial_number`
   echo "$PREFIX""dhcp-option-force=cpewan-id,vi-encap:3561,5,\"$Serial\"" >> $LOCAL_DHCP_CONF
   Product=`syscfg get device::product_class`
   echo "$PREFIX""dhcp-option-force=cpewan-id,vi-encap:3561,6,\"$Product\"" >> $LOCAL_DHCP_CONF
   if [ "$LOG_LEVEL" -gt 1 ] ; then
      echo "$PREFIX""log-dhcp" >> $LOCAL_DHCP_CONF
   fi
   if [ "dns_only" != "$3" ] ; then
      prepare_dhcp_conf_static_hosts
      prepare_dhcp_options
      prepare_guest_dhcp_conf
      prepare_guest_dhcp_options
   fi
   prepare_well_known_dns $LOCAL_DHCP_CONF
   prepare_dns_a_from_sysevent_pool $LOCAL_DHCP_CONF
   prepare_dns_a_full_ip_from_sysevent_pool $LOCAL_DHCP_CONF
   prepare_dns_aaaa_from_sysevent_pool $LOCAL_DHCP_CONF
   prepare_dns_srv_from_sysevent_pool $LOCAL_DHCP_CONF
   prepare_dns_ptr_from_sysevent_pool $LOCAL_DHCP_CONF
   prepare_dns_txt_from_sysevent_pool $LOCAL_DHCP_CONF
   cat $LOCAL_DHCP_CONF > $DHCP_CONF
   rm -f $LOCAL_DHCP_CONF
   [ -f $LOCAL_DHCP_OPTIONS_FILE ] && (cat $LOCAL_DHCP_OPTIONS_FILE > $DHCP_OPTIONS_FILE)
   rm -f $LOCAL_DHCP_OPTIONS_FILE
}
TEMP_DHCP_LEASE_FILE=/tmp/.temp_dhcp_lease_file
delete_dhcp_lease() {
   IP_ADDR=$1
   sed "/ $IP_ADDR /d" $DHCP_LEASE_FILE > $TEMP_DHCP_LEASE_FILE
   cat $TEMP_DHCP_LEASE_FILE > $DHCP_LEASE_FILE
   rm $TEMP_DHCP_LEASE_FILE
}
show_ipv4_dhcp_clients() {
   cat /tmp/dnsmasq.leases | awk '{print $3 " " $2 " " $4}'
}
sanitize_leases_file() {
   if [ ! -f "$DHCP_LEASE_FILE" ] ; then
      return
   fi
   SLF_OUTFILE_1="/tmp/sanitize_leases.${$}"
   if [ -z "$SYSCFG_dhcp_num" ] || [ "0" = "$SYSCFG_dhcp_num" ] ; then
      echo > $DHCP_LEASE_FILE
      return
   fi
   cat $DHCP_LEASE_FILE  > $SLF_OUTFILE_1
   while read line ; do
      CANDIDATE_IP=`echo $line | cut -f 3 -d ' '`
      if [ -n "$CANDIDATE_IP" ] ; then
         is_network_conflict $SYSEVENT_lan_ipaddr $SYSEVENT_lan_prefix_len $CANDIDATE_IP $SYSEVENT_lan_prefix_len
         if [ "0" = "$?" ] ; then
            delete_dhcp_lease $CANDIDATE_IP
         fi
      fi
   done < $SLF_OUTFILE_1
   rm -f $SLF_OUTFILE_1
}
