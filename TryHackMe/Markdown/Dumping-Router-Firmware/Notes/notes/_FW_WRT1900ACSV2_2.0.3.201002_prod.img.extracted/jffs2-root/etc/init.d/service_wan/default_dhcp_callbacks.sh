#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
DEFAULT_LOG_FILE="/tmp/udhcpc.log"
if [ -n "$interface" ] ; then
   NAMESPACE=`interface_to_syscfg_namespace $interface`
   if [ -n "$NAMESPACE" ] ; then
      wan_info_by_namespace $NAMESPACE
   fi
fi
BBVERSION=`/bin/busybox | head -1 | awk '{print $2}' | sed -e 's/v//' | awk -F'.' '{print $1*10000+$2*100+$3}'`
if [ -n "$ip6rd" -a $BBVERSION -gt 11502 ]; then
sixrd="$ip6rd"
echo "6rd = $sixrd" > /dev/console
fi
case "$1" in
   leasefail)
      ulog default_dhcp_cb status "$PID wan $interface dhcp lease has failed"
      ;;
   deconfig)
      ulog default_dhcp_cb status "udhcpc $PID - cmd $1 interface $interface ip $ip broadcast $broadcast subnet $subnet router $router" 
      if [ -z "$interface" ] ; then
         ulog default_dhcp_cb status "Received a deconfig event with no interface. Ignoring" 
         return
      fi
      if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] && [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
         ulog default_dhcp_cb status "$PID $interface dhcp lease has expired"
         rm -f $DEFAULT_LOG_FILE
         LOG_FILE="/tmp/"${NAMESPACE}"_udhcpc.log"
         rm -f $LOG_FILE
         sysevent set ${NAMESPACE}_current_ipv4_link_state down
         sysevent set dhcpc_ntp_server1
         sysevent set dhcpc_ntp_server2
         sysevent set dhcpc_ntp_server3
         sysevent set wan_dhcp_lease
         sysevent set wan_dynamic_dns
         sysevent set ${NAMESPACE}_ipv4_wan_ipaddr
         sysevent set ${NAMESPACE}_ipv4_wan_subnet
         sysevent set ${NAMESPACE}_ipv4_default_router
         sysevent set ${NAMESPACE}_wan_start_time
         STATIC_ROUTES=`sysevent get ${NAMESPACE}_static_routes`
         if [ -n "$STATIC_ROUTES" ] ; then
            delete_static_routes "$STATIC_ROUTES"
         fi
         sysevent set ${NAMESPACE}_static_routes
      else
         sysevent set ${NAMESPACE}_ipv4_default_router
         ulog default_dhcp_cb status "$PID deconfig does not require handling except unset default_router"
      fi
      ;;
   renew|bound)
      ulog default_dhcp_cb status "udhcpc $PID - cmd $1 interface $interface ip $ip broadcast $broadcast subnet $subnet router $router" 
      if [ -z "$interface" ] ; then
         ulog default_dhcp_cb status "Received a $1 event with no interface. Ignoring" 
         return
      fi
      if [ -f /etc/cron/cron.everyminute/start_autodetect.sh ] 
      then
         rm -f /etc/cron/cron.everyminute/start_autodetect.sh
         ulog default_dhcp_cb status "dhcp successful. Stopping wan auto detection"
      fi
      echo "interface     : $interface" > $DEFAULT_LOG_FILE
      echo "ip address    : $ip"        >> $DEFAULT_LOG_FILE
      echo "subnet mask   : $subnet"    >> $DEFAULT_LOG_FILE
      echo "broadcast     : $broadcast" >> $DEFAULT_LOG_FILE
      echo "lease time    : $lease"     >> $DEFAULT_LOG_FILE
      echo "router        : $router"    >> $DEFAULT_LOG_FILE
      echo "hostname      : $hostname"  >> $DEFAULT_LOG_FILE
      echo "domain        : $domain"    >> $DEFAULT_LOG_FILE
      echo "next server   : $siaddr"    >> $DEFAULT_LOG_FILE
      echo "server name   : $sname"     >> $DEFAULT_LOG_FILE
      echo "server id     : $serverid"  >> $DEFAULT_LOG_FILE
      echo "tftp server   : $tftp"      >> $DEFAULT_LOG_FILE
      echo "timezone      : $timezone"  >> $DEFAULT_LOG_FILE
      echo "time server   : $timesvr"   >> $DEFAULT_LOG_FILE
      echo "name server   : $namesvr"   >> $DEFAULT_LOG_FILE
      echo "ntp server    : $ntpsrv"    >> $DEFAULT_LOG_FILE
      echo "dns server    : $dns"       >> $DEFAULT_LOG_FILE
      echo "wins server   : $wins"      >> $DEFAULT_LOG_FILE
      echo "log server    : $logsvr"    >> $DEFAULT_LOG_FILE
      echo "cookie server : $cookiesvr" >> $DEFAULT_LOG_FILE
      echo "print server  : $lprsvr"    >> $DEFAULT_LOG_FILE
      echo "swap server   : $swapsvr"   >> $DEFAULT_LOG_FILE
      echo "boot file     : $boot_file" >> $DEFAULT_LOG_FILE
      echo "boot file name: $bootfile"  >> $DEFAULT_LOG_FILE
      echo "bootsize      : $bootsize"  >> $DEFAULT_LOG_FILE
      echo "root path     : $rootpath"  >> $DEFAULT_LOG_FILE
      echo "ip ttl        : $ipttl"     >> $DEFAULT_LOG_FILE
      echo "mtu           : $mtuipttl"  >> $DEFAULT_LOG_FILE
      echo "6rd           : $sixrd"     >> $DEFAULT_LOG_FILE
      echo "staticroutes  : $staticroutes" >> $DEFAULT_LOG_FILE
      LOG_FILE="/tmp/"${NAMESPACE}"_udhcpc.log"
      echo "interface     : $interface" > $LOG_FILE
      echo "ip address    : $ip"        >> $LOG_FILE
      echo "subnet mask   : $subnet"    >> $LOG_FILE
      echo "broadcast     : $broadcast" >> $LOG_FILE
      echo "lease time    : $lease"     >> $LOG_FILE
      echo "router        : $router"    >> $LOG_FILE
      echo "hostname      : $hostname"  >> $LOG_FILE
      echo "domain        : $domain"    >> $LOG_FILE
      echo "next server   : $siaddr"    >> $LOG_FILE
      echo "server name   : $sname"     >> $LOG_FILE
      echo "server id     : $serverid"  >> $LOG_FILE
      echo "tftp server   : $tftp"      >> $LOG_FILE
      echo "timezone      : $timezone"  >> $LOG_FILE
      echo "time server   : $timesvr"   >> $LOG_FILE
      echo "name server   : $namesvr"   >> $LOG_FILE
      echo "ntp server    : $ntpsrv"    >> $LOG_FILE
      echo "dns server    : $dns"       >> $LOG_FILE
      echo "wins server   : $wins"      >> $LOG_FILE
      echo "log server    : $logsvr"    >> $LOG_FILE
      echo "cookie server : $cookiesvr" >> $LOG_FILE
      echo "print server  : $lprsvr"    >> $LOG_FILE
      echo "swap server   : $swapsvr"   >> $LOG_FILE
      echo "boot file     : $boot_file" >> $LOG_FILE
      echo "boot file name: $bootfile"  >> $LOG_FILE
      echo "bootsize      : $bootsize"  >> $LOG_FILE
      echo "root path     : $rootpath"  >> $LOG_FILE
      echo "ip ttl        : $ipttl"     >> $LOG_FILE
      echo "mtu           : $mtuipttl"  >> $LOG_FILE
      echo "6rd           : $sixrd"     >> $LOG_FILE
      echo "staticroutes  : $staticroutes" >> $LOG_FILE
      if [ -n "$lease" ] ; then
         sysevent set wan_dhcp_lease $lease 
      fi
      if [ -z "$subnet" ] ; then
         subnet=255.255.255.0
         ulog default_dhcp_cb warning "no subnet provided. Assuming 255.255.255.0"
      fi
      PPP_STATUS=`sysevent get ppp_status`
      if [ "$PPP_STATUS" != "up" -a "$PPP_STATUS" != "preup" ] ; then
          sysevent set ipv4_wan_subnet $subnet 
          sysevent set ${NAMESPACE}_ipv4_wan_subnet $subnet 
      fi
      NETMASK="/$subnet"
      
      if [ -n "$broadcast" ] ; then
         BROADCAST="broadcast $broadcast"
      else
         BROADCAST="broadcast +"
      fi
      OLDIP=`ip -4 addr show dev $interface  | grep "inet " | awk '{split($2,foo, "/"); print(foo[1]);}'`
      if [ "$OLDIP" != "$ip" ] ; then
         RESULT=`arping -q -c 2 -w 3 -D -I $interface $ip`
         if [ "" != "$RESULT" ] &&  [ "0" != "$RESULT" ] ; then
            echo "[utopia][dhcp client script] duplicate address detected $ip on $interface." > /dev/console
            echo "[utopia][dhcp client script] ignoring duplicate ... hoping for the best" > /dev/console
         fi
         if [ -n "$OLDIP" ] ; then
            ip -4 addr show dev $interface | grep "inet " | awk '{system("/sbin/ip addr del " $2 " dev $interface")}'
         fi
         ip -4 addr add $ip$NETMASK $BROADCAST dev $interface 
      fi
      PPP_STATUS=`sysevent get ppp_status`
      if [ "$PPP_STATUS" != "up" -a "$PPP_STATUS" != "preup" ] ; then
          OLD_DEFAULT_ROUTER=`sysevent get ${NAMESPACE}_ipv4_default_router`
          if [ -n "$router" ] ; then
              DEFAULT_ROUTER=${router%% *}
              if [ "$SYSCFG_wan_proto" = "pptp" -o "$SYSCFG_wan_proto" = "l2tp" ]; then
                  route_host=`ip -4 route list $SYSCFG_wan_proto_server_address`
                  route_subnet=`ip -4 route list $SYSCFG_wan_proto_server_address/$subnet`
                  if [ -z "$route_host" -a -z "$route_subnet" ]; then
                      echo "add route to $SYSCFG_wan_proto server:$SYSCFG_wan_proto_server_address" > /dev/console
                      ip -4 route add $DEFAULT_ROUTER dev $interface
                      ip -4 route add $SYSCFG_wan_proto_server_address via $DEFAULT_ROUTER 
                  fi
              fi
              sysevent set ${NAMESPACE}_ipv4_default_router $DEFAULT_ROUTER
              ulog default_dhcp_cb status "old default gw : $OLD_DEFAULT_ROUTER new:$DEFAULT_ROUTER wan:$SYSCFG_wan_proto default:$SYSCFG_default"
              if [ -n "$OLD_DEFAULT_ROUTER" -a "$OLD_DEFAULT_ROUTER" != "$DEFAULT_ROUTER" ] ; then
                  if [ "$SYSCFG_wan_proto" = "dhcp" -a "$SYSCFG_default" = 1 ] ; then
                      ulog default_dhcp_cb status "issue a dhcp_default_router_changed"
                      sysevent set dhcp_default_router_changed
                  fi
              fi
           fi
      fi
      sysevent set dhcpc_ntp_server1 
      sysevent set dhcpc_ntp_server2 
      sysevent set dhcpc_ntp_server3 
      if [ -n "$domain" ] ; then
         sysevent set dhcp_domain $domain
      fi
      PPP=`sysevent get ppp_status`
      if [ "$PPP" != "up" ]; then
         if [ -n "$dns" ] ; then
            sysevent set wan_dynamic_dns "${dns}"
            n=0
            for ip_addr in ${dns}; do
               n=`expr $n + 1`
               syscfg set nameserver${n} ${ip_addr}
            done
         fi
         prepare_resolver_conf
      fi
      NTPSERVER1=
      NTPSERVER2=
      for ii in $ntpsrv ; do
         if [ "" = "$NTPSERVER1" ] ; then
            NTPSERVER1=$ii
            `sysevent set dhcpc_ntp_server1 $NTPSERVER1`
         elif [ "" = "$NTPSERVER2" ] ; then
            NTPSERVER2=$ii
            `sysevent set dhcpc_ntp_server2 $NTPSERVER2`
         else
            `sysevent set dhcpc_ntp_server3 $ii`
         fi
      done
      PPP_STATUS=`sysevent get ppp_status`
      if [ "$PPP_STATUS" != "up" -a "$PPP_STATUS" != "preup" ] ; then
          sysevent set ${NAMESPACE}_ipv4_wan_ipaddr $ip
      fi
      DIRTY=0
      SYSCFG_dhcp_option_6rd_enable=`syscfg get dhcp_option_6rd_enable`
      if [ -n "$sixrd" -a "1" = "$SYSCFG_dhcp_option_6rd_enable" ] ; then
         eval `utctx_cmd get 6rd_enable 6rd_zone 6rd_zone_length 6rd_common_prefix4 6rd_relay`
         if [ -z "$SYSCFG_6rd_enable" -o "0" = "$SYSCFG_6rd_enable" ] ; then
               syscfg set 6rd_enable 1
               DIRTY=1
               ulog default_dhcp_cb status "Enabling 6rd using option 212"
         fi
         TEMP_VALUE=`echo $sixrd | cut  -f 3 -d ' '`
         if [ -n "$SYSCFG_6rd_zone" ] ; then
            if [ -n "$TEMP_VALUE" -a "$SYSCFG_6rd_zone" != "$TEMP_VALUE" ] ; then
               syscfg set 6rd_zone $TEMP_VALUE
               DIRTY=1
               ulog default_dhcp_cb status "Provisioning 6rd_zone $TEMP_VALUE"
            fi
         else 
            syscfg set 6rd_zone $TEMP_VALUE
            DIRTY=1
            ulog default_dhcp_cb status "Provisioning 6rd_zone $TEMP_VALUE"
         fi
         TEMP_VALUE=`echo $sixrd | cut  -f 2 -d ' '`
         if [ -n "$SYSCFG_6rd_zone_length" ] ; then
            if [ -n "$TEMP_VALUE" -a "$SYSCFG_6rd_zone_length" != "$TEMP_VALUE" ] ; then
               syscfg set 6rd_zone_length $TEMP_VALUE
               DIRTY=1
               ulog default_dhcp_cb status "Provisioning 6rd_zone_length $TEMP_VALUE"
            fi
         else
            syscfg set 6rd_zone_length $TEMP_VALUE
            DIRTY=1
            ulog default_dhcp_cb status "Provisioning 6rd_zone_length $TEMP_VALUE"
         fi
         TEMP_VALUE=`echo $sixrd | cut  -f 1 -d ' '`
         if [ -n "$SYSCFG_6rd_common_prefix4" ] ; then
            if [ -n "$TEMP_VALUE" -a "$SYSCFG_6rd_common_prefix4" != "$TEMP_VALUE" ] ; then
               syscfg set 6rd_common_prefix4 $TEMP_VALUE
               DIRTY=1
               ulog default_dhcp_cb status "Provisioning 6rd_common_prefix4 $TEMP_VALUE"
            fi
         else
            syscfg set 6rd_common_prefix4 $TEMP_VALUE
            DIRTY=1
            ulog default_dhcp_cb status "Provisioning 6rd_common_prefix4 $TEMP_VALUE"
         fi
         TEMP_VALUE=`echo $sixrd | cut  -f 4 -d ' '`
         if [ -n "$SYSCFG_6rd_relay" ] ; then
            if [ -n "$TEMP_VALUE" -a "$SYSCFG_6rd_relay" != "$TEMP_VALUE" ] ; then
               syscfg set 6rd_relay $TEMP_VALUE
               DIRTY=1
               ulog default_dhcp_cb status "Provisioning 6rd_relay $TEMP_VALUE"
            fi
         else
            syscfg set 6rd_relay $TEMP_VALUE
            DIRTY=1
            ulog default_dhcp_cb status "Provisioning 6rd_relay $TEMP_VALUE"
         fi
         SYSCFG_ipv6_automatic=`syscfg get ipv6_automatic`
         SYSCFG_tunnel_mode=`syscfg get tunnel_mode`
         if [ -n "$SYSCFG_ipv6_automatic" -a "$SYSCFG_ipv6_automatic" = "1" ] ; then
            syscfg set ipv6_automatic 0
            DIRTY=1
            ulog default_dhcp_cb status "Unprovisioning ipv6_automatic for ipv6 gui"
         fi
         if [ -z "$SYSCFG_tunnel_mode" -o "$SYSCFG_tunnel_mode" = "0" ] ; then
            syscfg set tunnel_mode 1
            DIRTY=1
            ulog default_dhcp_cb status "Provisioning tunnel_mode for ipv6 gui"
         fi
         if [ "1" = "$DIRTY" ] ; then
            sysevent set ipv6-restart
         fi
      fi 
      if [ -n "$staticroutes" ] ; then
         sysevent set ${NAMESPACE}_static_routes "$staticroutes"
         add_static_routes "$staticroutes" "$interface"
      fi
      SYSCFG_wan_auto_detect_enable=`syscfg get wan_auto_detect_enable`
      if [ "1" = "$SYSCFG_wan_auto_detect_enable" ] ; then
         syscfg set wan_auto_detect_enable 0
         DIRTY=1
      fi
      if [ "1" = "$DIRTY" ] ; then
         syscfg commit 
      fi
      LINK_STATE=`sysevent get ${NAMESPACE}_current_ipv4_link_state`
      if [ "up" != "$LINK_STATE" ] ; then
         ulog default_dhcp_cb status "$PID setting ${NAMESPACE}_current_ipv4_link_state to up"
         sysevent set ${NAMESPACE}_current_ipv4_link_state up
      fi
      ;;
esac
exit 0
