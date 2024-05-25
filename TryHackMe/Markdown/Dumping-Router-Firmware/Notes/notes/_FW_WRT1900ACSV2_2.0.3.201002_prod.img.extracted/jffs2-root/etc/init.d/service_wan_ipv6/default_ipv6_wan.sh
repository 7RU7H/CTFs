#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/ipv6_functions.sh
source /etc/init.d/event_handler_functions.sh
DESIRED_WAN_STATE=`sysevent get desired_ipv6_wan_state`
CURRENT_WAN_STATE=`sysevent get current_ipv6_wan_state`
CURRENT_LINK_STATE=`sysevent get current_ipv6_link_state`
PID="($$)"
SELF="$0[$$]"
SLAAC_MONITOR=slaac_monitor.sh
SLAAC_MONITOR_SCRIPT=/etc/init.d/service_wan_ipv6/${SLAAC_MONITOR}
service_init ()
{
    eval `utctx_cmd get router_adv_provisioning_enable dhcpv6c_enable lan_ifname ipv6_verbose_logging guest_enabled guest_lan_ifname wan_if_v6_forwarding`
    SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ipv6_ifname`
    if [ -z "$SYSEVENT_current_wan_ipv6_ifname" ] ; then 
      SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ifname`
      if [ -n "$SYSEVENT_current_wan_ipv6_ifname" ] ; then
         if [ "dslite" = "$CUR_NAME" ] ; then
            ulog default_ipv6_wan status "$PID current_wan_ipv6_name is not set. Cannot set to dslite. Aborting ipv6 init"
            exit 0
         else
            ulog default_ipv6_wan status "$PID current_wan_ipv6_name is not set. Setting internally to current_wan_ifname $SYSEVENT_current_wan_ipv6_ifname"
         fi
      else
         ulog default_ipv6_wan status "$PID current_wan_ipv6_name is not set.  Neither is current_wan_ifname. aborting ipv6 init"
         exit 0
      fi
    fi
   if [ "1" = "$SYSCFG_ipv6_verbose_logging" ] ; then
      LOG=/var/log/ipv6.log
   else
      LOG=/dev/null
   fi
}
bring_wan_down() {
   SLAAC_PID_FILE="/var/run/slaac_monitor.pid"
   if [ -f "$SLAAC_PID_FILE" ] ; then
      kill  `cat $SLAAC_PID_FILE`
      rm -f $SLAAC_PID_FILE
      ulog default_ipv6_wan status "$PID killing SLAAC Monitor"
      OWNER=`sysevent get current_wan_ipv6address_owner`
      if [ "$OWNER" = "slaac" ] ; then
         sysevent set current_wan_ipv6address
         sysevent set current_wan_ipv6address_owner
         sysevent set preferred_time_slaac
      fi
   fi
   sysevent set ipv6_connection_state "ipv6 connection going down"
   sysevent set ipv6-errinfo
   sysevent set ipv6-status stopping
   sysevent set dhcpv6_client-stop
   sleep 2
   if [ -d /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname} ] ; then
      echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra    # Never accept RA
      echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_defrtr # Accept default router (metric 1024)
      echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_pinfo # Accept prefix information for SLAAC
      echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/autoconf     # Do SLAAC
      ulog default_ipv6_wan status "$PID setting current_ipv6_wan_state down"
   fi
   sysevent set ipv6_wan-stopped
   if [ "slaac" = "`sysevent get current_wan_ipv6address_owner`" ] ; then
      sysevent set current_wan_ipv6address
      sysevent set current_wan_ipv6address_owner
   fi
   save_lan_ipv6_prefix $SYSCFG_lan_ifname
   save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
   sysevent set current_ipv6_wan_state down
   sysevent set ipv6-status stopped
   sysevent set ipv6_firewall-restart
   sysevent set radvd-reload
   sysevent set ipv6_connection_state "ipv6 connection down"
}
bring_wan_up() {
   sysevent set ipv6_connection_state "ipv6 connection going up"
   clear_previous_lan_ipv6_prefix $SYSCFG_lan_ifname
   clear_previous_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
   if [ "0" = "$SYSCFG_dhcpv6c_enable" ] ; then
      echo "$PID dhcpv6 client is not enabled. It will not be started by default_ipv6_wan.sh" >> $LOG 
   else
      echo "$PID default_ipv6_wan.sh starting dhcpv6 client." >> $LOG 
      sysevent set dhcpv6_client-restart
   fi
   if [ "1" -eq "$SYSCFG_router_adv_provisioning_enable" ] ; then
      if [ -d /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname} ] ; then
         echo "$SELF starting SLACC on wan." >> $LOG 
         echo 2 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra    # Accept RA even when forwarding is enabled
         if [ "pppoe" != "`syscfg get wan_proto`" ] ; then
         	echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_defrtr # Accept default router (metric 1024)
         fi
         echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_pinfo # Accept prefix information for SLAAC
         echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/autoconf     # Do SLAAC
         if [ "1" = "$SYSCFG_wan_if_v6_forwarding" ] ; then
      		echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/forwarding     # our WAN side should be like a router
      	 else
      		echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/forwarding     # our WAN side should be like a host
      	 fi
         ADDR_OK=`get_SLAAC_addr ${SYSEVENT_current_wan_ipv6_ifname}`
         if [ "0"="$ADDR_OK" ]; then
            ulog default_ipv6_wan status "$PID SLAAC Address acquired"
            echo "$SELF got SLAAC address" >> $LOG 
         else
            echo "$SELF fail to get SLACC address" >> $LOG
            ulog default_ipv6_wan status "$PID SLAAC Address not yet acquired"
         fi
      else
         ulog default_ipv6_wan status "$PID ipv6 /proc not provisioned due to not ready"
      fi
      sysevent set ipv6_connection_state "ipv6 connection up pending provisioning"
      if [ "pppoe" = "`syscfg get wan_proto`" ] ; then
      	SYSCFG_wan_ifname=`syscfg get wan_1 ifname`
      	if [ -z "$SYSCFG_wan_ifname" ] ; then
      	   ulog default_ipv6_wan status "$PID syscfg wan_1 ifname is not found. Assuming eth1"
      	   SYSCFG_wan_ifname="eth1"
      	fi
      	echo 0 > /proc/sys/net/ipv6/conf/${SYSCFG_wan_ifname}/accept_ra # Not accept RA
        ip -6 route flush dev ${SYSCFG_wan_ifname}    # flush old route on old wan interface
        ip -6 route add fe80::/64 dev ${SYSCFG_wan_ifname} proto kernel metric 256 mtu 1500 advmss 1440 hoplimit 0		# add link-local route back on old wan interface
      fi
      ulog default_ipv6_wan status "$PID Starting SLAAC Monitor on $SYSEVENT_current_wan_ipv6_ifname"
      exec $SLAAC_MONITOR_SCRIPT $SYSEVENT_current_wan_ipv6_ifname &
   else
      echo "$SELF SLACC on wan is not enabled. It will not be started by default_ipv6_wan.sh" >> $LOG 
      sysevent set ipv6_connection_state "ipv6 connection up without slaac"
   fi
   sysevent set ipv6_firewall-restart
   sysevent set current_ipv6_wan_state up
   sysevent set ipv6_wan-started
   sysevent set ipv6-status started
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   /usr/bin/solicitation ${SYSEVENT_current_wan_ipv6_ifname}
}
service_init
case "$1" in
   current_ipv6_link_state)
      ulog default_ipv6_wan status "$PID ipv6 link state is $CURRENT_LINK_STATE"
      if [ "up" != "$CURRENT_LINK_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog default_ipv6_wan status "$PID ipv6 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog default_ipv6_wan status "$PID ipv6 link is down. Wan is already down"
            exit 0
         fi
      else
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog default_ipv6_wan status "$PID ipv6 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$DESIRED_WAN_STATE" ] ; then
               bring_wan_up
               exit 0
            else
               ulog default_ipv6_wan status "$PID ipv6 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv6_wan_state)
      CURRENT_IPV6_STATUS=`sysevent get ipv6-status`
      if [ "up" = "$DESIRED_WAN_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog default_ipv6_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$CURRENT_LINK_STATE" ] ; then
               ulog default_ipv6_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$CURRENT_WAN_STATE" ] ; then
            ulog default_ipv6_wan status "$PID wan is already down."
            if [ "stopped" != "$CURRENT_IPV6_STATUS" ] ; then
               sysevent set ipv6_wan-stopped
               sysevent set ipv6-status stopped
               sysevent set ipv6_firewall-restart
            fi
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog default_ipv6_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
