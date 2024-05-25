#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/ipv6_functions.sh
source /etc/init.d/event_handler_functions.sh
DESIRED_WAN_STATE=`sysevent get desired_ipv6_wan_state`
CURRENT_WAN_STATE=`sysevent get current_ipv6_wan_state`
CURRENT_LINK_STATE=`sysevent get current_ipv6_link_state`
PID="($$)"
service_init ()
{
    eval `utctx_cmd get router_adv_provisioning_enable wan_ipv6addr wan_ipv6_default_gateway ipv6_verbose_logging wan_if_v6_forwarding`
    SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ipv6_ifname`
   if [ "1" = "$SYSCFG_ipv6_verbose_logging" ] ; then
      LOG=/var/log/ipv6.log
   else
      LOG=/dev/null
   fi
}
bring_wan_down() {
   sysevent set ipv6_connection_state "ipv6 connection going down"
   sysevent set ipv6-errinfo
   sysevent set ipv6-status stopping
   sysevent set dhcpv6_client-stop
   echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra    # Never accept RA
   echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_defrtr # Accept default router (metric 1024)
   echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_pinfo # Accept prefix information for SLAAC
   echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/autoconf     # Do SLAAC
   ulog static_ipv6_wan status "$PID setting current_ipv6_wan_state down"
   sysevent set ipv6_wan-stopped
   if [ "static" = "`sysevent get current_wan_ipv6address_owner`" ] ; then
      sysevent set current_wan_ipv6address
   fi
   sysevent set current_ipv6_wan_state down
   sysevent set ipv6-status stopped
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_connection_state "ipv6 connection down"
}
bring_wan_up() {
   sysevent set ipv6_connection_state "ipv6 connection going up"
   if [ -z "$SYSCFG_wan_ipv6addr" -o -z "$SYSCFG_wan_ipv6_default_gateway" ] ; then
      ulog default_ipv6_wan status "Incomplete provisioning for static ipv6 ($SYSCFG_wan_ipv6addr, $SYSCFG_wan_ipv6_default_gateway)"
      ulog default_ipv6_wan status "Attempting SLAAC provisioning"
      sysevent set ipv6_connection_state "ipv6 static provisioning problem"
      sysevent set ipv6_connection_state "ipv6 connection up statically"
   else
      echo "$SELF `date +%X`: ip -6 addr add $SYSCFG_wan_ipv6addr dev $SYSEVENT_current_wan_ipv6_ifname" >> $LOG 2>&1
      ip -6 addr add $SYSCFG_wan_ipv6addr dev $SYSEVENT_current_wan_ipv6_ifname >> $LOG 2>&1
      CURRENT_OWNER=`sysevent get current_wan_ipv6address_owner`
      if [ -z "$CURRENT_OWNER" -o "static" = "$CURRENT_OWNER"  ] ; then
         CHOPPED_ADDR=`echo $SYSCFG_wan_ipv6addr | cut -d'/' -f1`
         sysevent set current_wan_ipv6address $CHOPPED_ADDR
      fi
      echo "$SELF `date +%X`: ip -6 route add default via $SYSCFG_wan_ipv6_default_gateway dev $SYSEVENT_current_wan_ipv6_ifname" >> $LOG 2>&1
      ip -6 route add default via $SYSCFG_wan_ipv6_default_gateway dev $SYSEVENT_current_wan_ipv6_ifname >> $LOG 2>&1
      sysevent set ipv6_connection_state "static addressing"
   fi
   if [ "1" -eq "$SYSCFG_router_adv_provisioning_enable" ] ; then
      echo "$PID static_ipv6_wan.sh starting SLACC on wan." >> $LOG 
      echo 2 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra    # Accept RA even when forwarding is enabled
      echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_defrtr # Accept default router (metric 1024)
      echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/accept_ra_pinfo # Accept prefix information for SLAAC
      echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/autoconf     # Do SLAAC
      if [ "1" = "$SYSCFG_wan_if_v6_forwarding" ] ; then
      	echo 1 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/forwarding     # our WAN side should be like a router
      else
      	echo 0 > /proc/sys/net/ipv6/conf/${SYSEVENT_current_wan_ipv6_ifname}/forwarding     # our WAN side should be like a host
      fi
   else
      echo "$PID SLACC on wan is not enabled. It will not be started by static_ipv6_wan.sh" >> $LOG 
   fi
   sysevent set ipv6_firewall-restart
   sysevent set current_ipv6_wan_state up
   sysevent set ipv6_wan-started
   sysevent set ipv6-status started
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
}
service_init
case "$1" in
   current_ipv6_link_state)
      ulog static_ipv6_wan status "$PID ipv6 link state is $CURRENT_LINK_STATE"
      if [ "up" != "$CURRENT_LINK_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog static_ipv6_wan status "$PID ipv6 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog static_ipv6_wan status "$PID ipv6 link is down. Wan is already down"
            exit 0
         fi
      else
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog static_ipv6_wan status "$PID ipv6 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$DESIRED_WAN_STATE" ] ; then
               bring_wan_up
               exit 0
            else
               ulog static_ipv6_wan status "$PID ipv6 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv6_wan_state)
      CURRENT_IPV6_STATUS=`sysevent get ipv6-status`
      if [ "up" = "$DESIRED_WAN_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog static_ipv6_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$CURRENT_LINK_STATE" ] ; then
               ulog static_ipv6_wan status "$PID bringing wan up deferred until wan link is up"
               ulog static_ipv6_wan status "$PID lan will be brought up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$CURRENT_WAN_STATE" ] ; then
            ulog static_ipv6_wan status "$PID wan is already down."
            if [ "started" = "`sysevent get ipv6_lan-status`" ] ; then
               ulog static_ipv6_wan status "$PID lan is up. Bringing down. "
            fi
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
      ulog static_ipv6_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
