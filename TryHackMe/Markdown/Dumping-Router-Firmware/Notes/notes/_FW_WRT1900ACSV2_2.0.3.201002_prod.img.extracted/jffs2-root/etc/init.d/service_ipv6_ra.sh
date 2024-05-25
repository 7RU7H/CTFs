#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
SELF="$0[$$]"
SERVICE_NAME="radvd"
RADVD_PID_FILE=/var/run/radvd.pid
RADVD_CONF_FILE=/etc/radvd.conf
RADVD_BIN_NAME=/usr/sbin/radvd
CRON_RETRY_FILE=/etc/cron/cron.everyminute/radvd_ra_retry.sh
if [ "1" = "`syscfg get ipv6_verbose_logging`" ]
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
utctx_batch_get() 
{
    SYSCFG_FAILED='false'
    eval `utctx_cmd get $1`
    if [ $SYSCFG_FAILED = 'true' ] ; then
        echo "Call failed"
        return 1
    fi
}
get_one_static_route_group()
{
   utctx_batch_get "$1"
  
   eval NS='$'SYSCFG_$1
      ARGS="\
      $NS::dest \
      $NS::interface \
      $NS::netmask \
      $NS::gw"
   utctx_batch_get "$ARGS"
   if [ "${?}" -ne "0" ] ; then
      return 1
   fi
   eval `echo DEST='$'SYSCFG_${NS}_dest`
   eval `echo MASK='$'SYSCFG_${NS}_netmask`
   eval `echo INTERFACE='$'SYSCFG_${NS}_interface`
   eval `echo GW='$'SYSCFG_${NS}_gw`
}
clean_cron_retry_file() {
   rm -f $CRON_RETRY_FILE
}
make_cron_retry_file() {
   CRON_FILE=$CRON_RETRY_FILE
   echo "#!/bin/sh" > $CRON_FILE
   echo "/etc/init.d/`basename $0` cron_handler" >> $CRON_FILE
   chmod 777 $CRON_FILE
}
cron_handler() {
   NEED_RESTART=0
   ip -6 route show | grep default > /dev/null
   NOW=$?
   sysevent get ipv6_default_route_exist | grep 1
   THEN=$? 
   if [ "$NOW" != "$THEN" ] ; then
      NEED_RESTART=1
   fi
   SYSEVENT_current_wan_if_name=`sysevent get current_wan_ipv6_ifname`
   ip -6 addr show dev $SYSEVENT_current_wan_if_name | grep global > /dev/null
   NOW=$?
   sysevent get ipv6_wan_address_exist | grep 1
   THEN=$? 
   if [ "$NOW" != "$THEN" ] ; then
      NEED_RESTART=1
   fi
   if [ "1" = "$NEED_RESTART" ] ; then
      echo "radvd cron handler: sysevent set radvd-restart" >> $LOG 
      sysevent set radvd-reload
   fi
}
make_radvd_conf_file() {
   rm -f /tmp/radvd.conf 
   echo -n > /tmp/radvd.conf
   chmod 644 /tmp/radvd.conf
   DefaultMaxRtrAdvInterval=600
   AdvDefaultLifetime=1800
   ROUTER_IS_DOWN=0
   if [ -z "$SYSCFG_wan_mtu" -o "0" = "$SYSCFG_wan_mtu" ] ; then
      case "$SYSCFG_wan_proto" in
         pppoe)
            AdvLinkMTU=1492
            ;;
         pptp | l2tp)
            AdvLinkMTU=1460
            ;;
         *)
            AdvLinkMTU=1500
            ;;
      esac
   else
         AdvLinkMTU=$SYSCFG_wan_mtu
   fi
   if [ "1" = "$SYSCFG_6rd_enable" ] ; then
      AdvLinkMTU=`expr $AdvLinkMTU - 20`
   else
	   if [ -n "`sysevent get saved_wan_mtu`" ] ; then
	      AdvLinkMTU=`sysevent get saved_wan_mtu`
	   fi
   fi
   if [ "1280" -gt "$AdvLinkMTU" ] ; then
      AdvLinkMTU=1280
   fi
   echo "$SELF SYSCFG_ula_enable=$SYSCFG_ula_enable" >> $LOG 
   if [ "" != "$SYSCFG_router_adv_enable" ] && [ "0" != "$SYSCFG_router_adv_enable" ] ; then
      if [ "1" != "$SYSCFG_ipv6_static_enable" ] ; then
         ip -6 route show | grep default > /dev/null
         RES=$?
         if [ "0" = "$RES" ] ; then
            sysevent set ipv6_default_route_exist 1 
         else
            ulog $SERVICE_NAME status "No ipv6 default route. Router lifetime is 0 on $SYSCFG_lan_ifname and $SYSCFG_guest_lan_ifname"
            AdvDefaultLifetime=0
            sysevent set ipv6_default_route_exist 0
         fi
         SYSEVENT_current_wan_if_name=`sysevent get current_wan_ipv6_ifname`
         ip -6 addr show dev $SYSEVENT_current_wan_if_name | grep global > /dev/null 
         RES=$?
         if [ "0" = "$RES" ] ; then
            sysevent set ipv6_wan_address_exist 1
         else
	    SE_ipv6_PD=`sysevent get ipv6_delegated_prefix`
	    if [ -z $SE_ipv6_PD ];then
	            ulog $SERVICE_NAME status "No ipv6 WAN global, nor PD. Router lifetime is 0 on $SYSCFG_lan_ifname and $SYSCFG_guest_lan_ifname"
        	    if [ "1" != "$SYSCFG_ipv6_ready" ] ; then
            		AdvDefaultLifetime=0
            	    fi
            else
		    ulog $SERVICE_NAME status "No ipv6 WAN global, but with valid PD, no need to reset lifetime"
	    fi
            sysevent set ipv6_wan_address_exist 0
         fi
      fi
      SYSEVENT_phylink_wan_state=`sysevent get phylink_wan_state`
      SYSEVENT_current_ipv6_wan_state=`sysevent get current_ipv6_wan_state`
      if [ "$SYSEVENT_phylink_wan_state" != "up" -o "$SYSEVENT_current_ipv6_wan_state" != "up" ] ; then
         ROUTER_IS_DOWN=1
      fi
      CURR_BR0_PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
      PREV_BR0_PREFIX=`sysevent get previous_${SYSCFG_lan_ifname}_ipv6_prefix`
      echo "$SELF CURR_BR0_PREFIX=$CURR_BR0_PREFIX" >> $LOG
      echo "$SELF PREV_BR0_PREFIX=$PREV_BR0_PREFIX" >> $LOG
      if [ -z "$CURR_BR0_PREFIX" -a -z "$PREV_BR0_PREFIX" ] ; then
         ROUTER_IS_DOWN=1
         AdvDefaultLifetime=0
      fi
      VALID_LIFETIME=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix_valid_lifetime`
      
      if [ "$VALID_LIFETIME" = "0" ] && [ "1" != "$SYSCFG_ipv6_static_enable" ] ; then
         AdvDefaultLifetime=0
      fi
      if [ "$ROUTER_IS_DOWN" = "1" ] ; then 
         AdvDefaultLifetime=0
      fi
      echo "interface $SYSCFG_lan_ifname" >> $RADVD_CONF_FILE
      echo "{" >> $RADVD_CONF_FILE
      AdvSendAdvert=on
      BR0_ULA=""
      if [ "1" = "$SYSCFG_ula_enable" ] ; then
         BR0_ULA=`sysevent get ${SYSCFG_lan_ifname}_ula_prefix`
         echo "$SELF BR0_ULA=$BR0_ULA" >> $LOG
      fi
      if [ -z "$CURR_BR0_PREFIX" -a -z "$PREV_BR0_PREFIX" -a -z "$BR0_ULA" ] ; then
         ulog $SERVICE_NAME status "No prefix and ULA for $SYSCFG_lan_ifname. Set AdvSendAdvert=off"
         echo "$SELF Set AdvSendAdvert=off" >> $LOG
         AdvSendAdvert=off
      fi
      echo "$SELF AdvSendAdvert=$AdvSendAdvert" >> $LOG
      echo "$SELF AdvDefaultLifetime=$AdvDefaultLifetime" >> $LOG
      echo "  AdvSendAdvert $AdvSendAdvert;" >> $RADVD_CONF_FILE
      echo "  AdvDefaultLifetime $AdvDefaultLifetime;" >> $RADVD_CONF_FILE
      MaxRtrAdvInterval=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix_valid_lifetime`
      if [ -z "$MaxRtrAdvInterval" ] || [ "$MaxRtrAdvInterval" -lt "4" ] || [ "$MaxRtrAdvInterval" -ge "1800" ] ; then
         MaxRtrAdvInterval=`expr $DefaultMaxRtrAdvInterval \/ 4`
      else
         MaxRtrAdvInterval=`expr $MaxRtrAdvInterval \/ 2`
      fi
      MinRtrAdvInterval=`expr $MaxRtrAdvInterval \/ 2`
      if [ "$MinRtrAdvInterval " -lt "3" ] ; then
         MinRtrAdvInterval=3
      fi
      if [ "0" = $AdvDefaultLifetime ] ; then
         MinRtrAdvInterval=120
         MaxRtrAdvInterval=180
      fi
      SYSEVENT_ipv6_delegated_prefix=`sysevent get ipv6_delegated_prefix`
      AdvRDNSSLifetime=`expr $MaxRtrAdvInterval \* 2`
      if [ "0" = "$AdvRDNSSLifetime" ] ; then
         AdvRDNSSLifetime=$DefaultMaxRtrAdvInterval
      fi
      echo "  MinRtrAdvInterval $MinRtrAdvInterval;" >> $RADVD_CONF_FILE
      echo "  MaxRtrAdvInterval $MaxRtrAdvInterval;" >> $RADVD_CONF_FILE
      echo "  AdvDefaultPreference medium;" >> $RADVD_CONF_FILE
      echo "  AdvHomeAgentFlag off;" >> $RADVD_CONF_FILE
      echo "  AdvManagedFlag off;" >> $RADVD_CONF_FILE
      if [ "$SYSCFG_dhcpv6s_enable" = "1" ] ; then
         echo "  AdvOtherConfigFlag on;" >> $RADVD_CONF_FILE
      fi
         echo "  AdvLinkMTU $AdvLinkMTU;" >> $RADVD_CONF_FILE
      PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
      LIFETIME_DEFAULT=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix_lifetime_default`
      if [ "1" = "$LIFETIME_DEFAULT" ] ; then
         VALID_LIFETIME=600
         PREFERRED_LIFETIME=600
      else
         VALID_LIFETIME=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix_valid_lifetime`
         PREFERRED_LIFETIME=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix_preferred_lifetime`
      fi
      if [ "1" = "$SYSCFG_ipv6_static_enable" ] ; then
         VALID_LIFETIME=2592000
         PREFERRED_LIFETIME=604800
      fi
      echo "$SELF prefix=$PREFIX lifetime $VALID_LIFETIME $PREFERRED_LIFETIME" >> $LOG
      if [ -n "$PREFIX" ] ; then
         if [ -z "$VALID_LIFETIME" -o "$ROUTER_IS_DOWN" = "1" ] ; then
            VALID_LIFETIME=0
            PREFERRED_LIFETIME=0
         fi
         echo "prefix $PREFIX" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
         echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
         echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
         echo "  AdvPreferredLifetime $PREFERRED_LIFETIME;" >> $RADVD_CONF_FILE
         echo "  AdvValidLifetime $VALID_LIFETIME;" >> $RADVD_CONF_FILE
         if [ "1" = "$LIFETIME_DEFAULT" -o "4294967295" = "$PREFERRED_LIFETIME" -o "4294967295" = "$VALID_LIFETIME" ] ; then
            echo "  DecrementLifetimes off;" >> $RADVD_CONF_FILE
         else 
            echo "  DecrementLifetimes on;" >> $RADVD_CONF_FILE
         fi
         echo "};" >> $RADVD_CONF_FILE
      fi 
      ULA=`sysevent get ${SYSCFG_lan_ifname}_ula_prefix`
      if [ -n "$ULA" -a "1" = "$SYSCFG_ula_enable" ] ; then
         echo "prefix $ULA" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
         echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
         echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
         echo "  AdvPreferredLifetime 604800;" >> $RADVD_CONF_FILE
         echo "  AdvValidLifetime 2592000;" >> $RADVD_CONF_FILE
         echo "};" >> $RADVD_CONF_FILE
      fi
      SYSEVENT_previous_br0_ipv6_prefix=`sysevent get previous_${SYSCFG_lan_ifname}_ipv6_prefix`
      ACQUIRED_TIME=`sysevent get previous_${SYSCFG_lan_ifname}_ipv6_prefix_acquired_time`
      PREV_VALID_LIFETIME=`sysevent get previous_${SYSCFG_lan_ifname}_ipv6_prefix_valid_lifetime`
      if [ -n "$SYSEVENT_previous_br0_ipv6_prefix" -a -n "$ACQUIRED_TIME" -a -n "$PREV_VALID_LIFETIME" ] ; then
         CURRENT_TIME=`date +%s`
         ELAPSED_TIME=`expr $CURRENT_TIME - $ACQUIRED_TIME`
         REMAIN_TIME=`expr $PREV_VALID_LIFETIME - $ELAPSED_TIME`
         CLEAR_PREFIX=0
         if [ "$REMAIN_TIME" -le "0" ] ; then
            CLEAR_PREFIX=1
            REMAIN_TIME=0
         fi
         if [ "$ROUTER_IS_DOWN" = "1" ] ; then
            REMAIN_TIME=0
         fi
      fi
      if [ -n "$SYSEVENT_previous_br0_ipv6_prefix" -a "$CURR_BR0_PREFIX" != "$PREV_BR0_PREFIX" ] ; then
         echo "prefix $SYSEVENT_previous_br0_ipv6_prefix" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
         echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
         echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
         echo "  AdvPreferredLifetime 0;" >> $RADVD_CONF_FILE
         echo "  AdvValidLifetime ${REMAIN_TIME:-0};" >> $RADVD_CONF_FILE
         echo "  DecrementLifetimes on;" >> $RADVD_CONF_FILE
         echo "};" >> $RADVD_CONF_FILE
      fi
      if [ "$CLEAR_PREFIX" = "1" ] ; then
         clear_previous_lan_ipv6_prefix ${SYSCFG_lan_ifname}
      fi
      RA_LIFETIME=$VALID_LIFETIME
      if [ "0" = "$RA_LIFETIME" ] || [ -z "$RA_LIFETIME" ] ; then
         RA_LIFETIME=0
      fi
      if [ -n "$SYSEVENT_ipv6_delegated_prefix" ] ; then
         echo "route $SYSEVENT_ipv6_delegated_prefix" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         echo "  AdvRoutePreference high;" >> $RADVD_CONF_FILE
         echo "  AdvRouteLifetime $RA_LIFETIME;" >> $RADVD_CONF_FILE
         echo "};" >> $RADVD_CONF_FILE
      fi
      if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
         for rdnss in `sysevent get ipv6_nameserver`
         do
            echo "RDNSS $rdnss" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvRDNSSLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         done
      fi
      eval `get_current_lan_ipv6address`
      if [ ! -z "$CURRENT_LAN_IPV6ADDRESS" ] ; then
         echo "RDNSS $CURRENT_LAN_IPV6ADDRESS" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         echo "  AdvRDNSSLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
         echo "};" >> $RADVD_CONF_FILE
      fi
      if [ "1" = "$SYSCFG_6rd_enable" ] ; then
         DOMAIN_NAMES=`sysevent get dhcp_domain`
      else
         DOMAIN_NAMES=`sysevent get ipv6_domain`
      fi
      if [ -n "$DOMAIN_NAMES" -a ! -z "$DOMAIN_NAMES" ] ; then
         for domain in $DOMAIN_NAMES 
         do
            echo "DNSSL $domain" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvDNSSLLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         done
      fi
      echo "};" >> $RADVD_CONF_FILE
      if [ "1" = "$SYSCFG_guest_enabled" ] ; then
         echo "interface $SYSCFG_guest_lan_ifname" >> $RADVD_CONF_FILE
         echo "{" >> $RADVD_CONF_FILE
         AdvSendAdvert=on
         CURR_BR1_PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
         PREV_BR1_PREFIX=`sysevent get previous_${SYSCFG_guest_lan_ifname}_ipv6_prefix`
         echo "$SELF CURR_BR1_PREFIX=$CURR_BR1_PREFIX" >> $LOG
         echo "$SELF PREV_BR1_PREFIX=$PREV_BR1_PREFIX" >> $LOG
         BR1_ULA=""
         if [ "1" = "$SYSCFG_ula_enable" ] ; then
            BR1_ULA=`sysevent get ${SYSCFG_guest_lan_ifname}_ula_prefix`
            echo "$SELF BR1_ULA=$BR1_ULA" >> $LOG
         fi
         if [ -z "$CURR_BR1_PREFIX" -a -z "$PREV_BR1_PREFIX" -a -z "$BR1_ULA" ] ; then
            ulog $SERVICE_NAME status "No prefix and ULA for $SYSCFG_guest_lan_ifname. Set AdvSendAdvert=off"
            echo "$SELF Set AdvSendAdvert=off" >> $LOG
            AdvSendAdvert=off
         fi
         echo "$SELF AdvSendAdvert=$AdvSendAdvert" >> $LOG
         echo "$SELF AdvDefaultLifetime=$AdvDefaultLifetime" >> $LOG
         echo "  AdvSendAdvert $AdvSendAdvert;" >> $RADVD_CONF_FILE
         echo "  AdvDefaultLifetime $AdvDefaultLifetime;" >> $RADVD_CONF_FILE
         if [ "0" = $AdvDefaultLifetime ] ; then
            MinRtrAdvInterval=120
            MaxRtrAdvInterval=180
         fi
         MaxRtrAdvInterval=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix_valid_lifetime`
         if [ -z "$MaxRtrAdvInterval" ] || [ "$MaxRtrAdvInterval" -lt "4" ] || [ "$MaxRtrAdvInterval" -ge "1800" ] ; then
            MaxRtrAdvInterval=$DefaultMaxRtrAdvInterval
         else
            MaxRtrAdvInterval=`expr $MaxRtrAdvInterval \/ 2`
         fi
         MinRtrAdvInterval=`expr $MaxRtrAdvInterval \/ 2`
         if [ "$MinRtrAdvInterval " -lt "3" ] ; then
            MinRtrAdvInterval=3
         fi
         SYSEVENT_ipv6_delegated_prefix=`sysevent get ipv6_delegated_prefix`
         AdvRDNSSLifetime=`expr $MaxRtrAdvInterval \* 2`
         if [ "0" = "$AdvRDNSSLifetime" ] ; then
            AdvRDNSSLifetime=$DefaultMaxRtrAdvInterval
         fi
         echo "  MinRtrAdvInterval $MinRtrAdvInterval;" >> $RADVD_CONF_FILE
         echo "  MaxRtrAdvInterval $MaxRtrAdvInterval;" >> $RADVD_CONF_FILE
         echo "  AdvDefaultPreference medium;" >> $RADVD_CONF_FILE
         echo "  AdvHomeAgentFlag off;" >> $RADVD_CONF_FILE
         echo "  AdvManagedFlag off;" >> $RADVD_CONF_FILE
         if [ "$SYSCFG_dhcpv6s_enable" = "1" ] ; then
            echo "  AdvOtherConfigFlag on;" >> $RADVD_CONF_FILE
         fi
         echo "  AdvLinkMTU $AdvLinkMTU;" >> $RADVD_CONF_FILE
         SYSEVENT_br1_ipv6_prefix=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
         if [ -n "$SYSEVENT_br1_ipv6_prefix" ] ; then
            PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
            LIFETIME_DEFAULT=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix_lifetime_default`
            if [ "1" = "$LIFETIME_DEFAULT" ] ; then
               VALID_LIFETIME=600
               PREFERRED_LIFETIME=600
            else
               VALID_LIFETIME=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix_valid_lifetime`
               PREFERRED_LIFETIME=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix_preferred_lifetime`
            fi
            if [ -n "$PREFIX" ] ; then
               if [ -z "$VALID_LIFETIME" ] ; then
                  VALID_LIFETIME=600
               fi
               if [ -z "$PREFERRED_LIFETIME" ] ; then
                  PREFERRED_LIFETIME=600
               fi
               echo "prefix $PREFIX" >> $RADVD_CONF_FILE
               echo "{" >> $RADVD_CONF_FILE
               echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
               echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
               echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
               echo "  AdvPreferredLifetime $PREFERRED_LIFETIME;" >> $RADVD_CONF_FILE
               echo "  AdvValidLifetime $VALID_LIFETIME;" >> $RADVD_CONF_FILE
               if [ "1" = "$LIFETIME_DEFAULT" ] ; then
                  echo "  DecrementLifetimes off;" >> $RADVD_CONF_FILE
               else 
                  echo "  DecrementLifetimes on;" >> $RADVD_CONF_FILE
               fi
               echo "};" >> $RADVD_CONF_FILE
            fi
         fi
         ULA=`sysevent get ${SYSCFG_guest_lan_ifname}_ula_prefix`
         if [ -n "$ULA" -a "1" = "$SYSCFG_ula_enable" ] ; then
            echo "prefix $ULA" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
            echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
            echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
            echo "  AdvPreferredLifetime 604800;" >> $RADVD_CONF_FILE
            echo "  AdvValidLifetime 2592000;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         fi
         SYSEVENT_previous_br1_ipv6_prefix=`sysevent get previous_${SYSCFG_guest_lan_ifname}_ipv6_prefix`
         if [ -n "$SYSEVENT_previous_br1_ipv6_prefix" ] ; then
            echo "prefix $SYSEVENT_previous_br1_ipv6_prefix" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvOnLink on;" >> $RADVD_CONF_FILE
            echo "  AdvAutonomous on;" >> $RADVD_CONF_FILE
            echo "  AdvRouterAddr off;" >> $RADVD_CONF_FILE
            echo "  AdvPreferredLifetime 0;" >> $RADVD_CONF_FILE
            echo "  AdvValidLifetime 0;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         fi
         RA_LIFETIME=$VALID_LIFETIME
         if [ "0" = "$RA_LIFETIME" ] || [ -z "$RA_LIFETIME" ] ; then
            RA_LIFETIME=0
         fi
         if [ -n "$SYSEVENT_ipv6_delegated_prefix" ] ; then
            echo "route $SYSEVENT_ipv6_delegated_prefix" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvRoutePreference high;" >> $RADVD_CONF_FILE
            echo "  AdvRouteLifetime $RA_LIFETIME;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         fi
         if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
            for rdnss in `sysevent get ipv6_nameserver`
            do
               echo "RDNSS $rdnss" >> $RADVD_CONF_FILE
               echo "{" >> $RADVD_CONF_FILE
               echo "  AdvRDNSSLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
               echo "};" >> $RADVD_CONF_FILE
            done
         fi
         eval `get_current_guest_lan_ipv6address`
         if [ ! -z "$CURRENT_GUEST_LAN_IPV6ADDRESS" ] ; then
            echo "RDNSS $CURRENT_GUEST_LAN_IPV6ADDRESS" >> $RADVD_CONF_FILE
            echo "{" >> $RADVD_CONF_FILE
            echo "  AdvRDNSSLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
            echo "};" >> $RADVD_CONF_FILE
         fi
         if [ "1" = "$SYSCFG_6rd_enable" ] ; then
            DOMAIN_NAMES=`sysevent get dhcp_domain`
         else
            DOMAIN_NAMES=`sysevent get ipv6_domain`
         fi
         if [ -n "$DOMAIN_NAMES" -a ! -z "$DOMAIN_NAMES" ] ; then
            for domain in $DOMAIN_NAMES
            do
               echo "DNSSL $domain" >> $RADVD_CONF_FILE
               echo "{" >> $RADVD_CONF_FILE
               echo "  AdvDNSSLLifetime $AdvRDNSSLifetime;" >> $RADVD_CONF_FILE
               echo "};" >> $RADVD_CONF_FILE
            done
         fi
         echo "};" >> $RADVD_CONF_FILE
      fi #end guest_eanbled
   fi #end route_adv_eanble
}
do_stop_radvd () {
   clean_cron_retry_file
   if [ -f "$RADVD_PID_FILE" ] ; then
      kill -TERM `cat $RADVD_PID_FILE`
      rm -f $RADVD_PID_FILE
   fi
   ulog $SERVICE_NAME status "radvd stopped"
}
do_start_radvd () {
   if [ -f "$RADVD_PID_FILE" ] ; then
      do_stop_radvd
   fi
   rm -f $RADVD_PID_FILE
   make_radvd_conf_file
   echo "$SELF do_start_radvd" >> $LOG
   $RADVD_BIN_NAME -C $RADVD_CONF_FILE >> $LOG 2>&1 
   ulog $SERVICE_NAME status "radvd started"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status started
   clean_cron_retry_file
   make_cron_retry_file
}
do_reload_radvd () {
   if [ -f "$RADVD_PID_FILE" ] ; then
      make_radvd_conf_file
      kill -SIGHUP `cat $RADVD_PID_FILE` >> $LOG 2>&1
      if [ "1" = "$?" ] ; then
         echo "$SELF do_start_radvd" >> $LOG
         do_start_radvd
      fi
      clean_cron_retry_file
      make_cron_retry_file
   fi
}
calculate_services_to_start ()
{
   FW_RESTART_REQ=0
   RADVDD_REQ=0
   CURRENT_WAN_STATE=`sysevent get wan-status`
   CURRENT_LAN_STATE=`sysevent get lan-status`
   if [ "stopped" = "$CURRENT_WAN_STATE" ] && [ "stopped" = "$CURRENT_LAN_STATE" ] ; then
      return
   elif [ "stopping" = "$CURRENT_WAN_STATE" ] || [ "starting" = "$CURRENT_WAN_STATE" ] ; then
      return
   elif [ "stopping" = "$CURRENT_LAN_STATE" ] || [ "starting" = "$CURRENT_LAN_STATE" ] ; then
      return
   else
      RADVD_REQ=1
   fi
   if [ "" != "$SYSCFG_StaticRouteCount" ] && [ "0" != "$SYSCFG_StaticRouteCount" ] ; then
      FW_RESTART_REQ=1
   fi
}
start_all_required_services () 
{
   calculate_services_to_start
   if [ "1" = "$FW_RESTART_REQ" ] ; then
      sysevent set firewall-restart
   fi
   if [ "1" = "$RADVD_REQ" ] ; then
      do_start_radvd
   fi
}
service_restart()
{
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" = "$STATUS" -a -f "$RADVD_PID_FILE" ] ; then
      echo "$SELF do_reload_radvd" >> $LOG
      do_reload_radvd
   else
      echo "$SELF do_start_radvd" >> $LOG
      do_start_radvd
   fi
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      start_all_required_services
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      do_stop_radvd
      sysevent set ${SERVICE_NAME}-status stopped
      sysevent set ${SERVICE_NAME}-errinfo
      start_all_required_services
   fi
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get hostname lan_ifname guest_enabled guest_lan_ifname router_adv_enable dhcpv6s_enable StaticRouteCount dhcp_server_propagate_wan_nameserver 6rd_enable ula_enable wan_proto wan_mtu ipv6_static_enable ipv6_ready`
   eval $FOO
  if [ $SYSCFG_FAILED = 'true' ] ; then
     ulog $SERVICE_NAME status "$PID utctx failed to get some configuration data"
     exit
  fi
  if [ -z "$SYSCFG_hostname" ] ; then
      SYSCFG_hostname=`cat /etc/hostname`
  fi
}
service_init
   echo "<`date`> $SERVICE_NAME $1" >> $LOG 
case "$1" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      service_start
      ;;
   staticroute-restart)
      service_restart
      ;;
   radvd-reload)
      service_restart
      ;;
   ipv6_nameserver)
      ;;
   br0_ula_prefix)
      service_restart
      ;;
   cron_handler)
      cron_handler
      ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
