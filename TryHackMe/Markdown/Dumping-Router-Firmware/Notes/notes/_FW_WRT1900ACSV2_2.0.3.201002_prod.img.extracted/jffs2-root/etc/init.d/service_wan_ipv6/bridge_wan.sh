#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
DESIRED_WAN_STATE=`sysevent get desired_ipv6_wan_state`
CURRENT_WAN_STATE=`sysevent get current_ipv6_wan_state`
CURRENT_LINK_STATE=`sysevent get current_ipv6_link_state`
PID="($$)"
service_init ()
{
   eval `utctx_cmd get ipv6_enable ipv6_verbose_logging lan_ifname`
   SYSEVENT_current_lan_ipv6address=`sysevent get current_lan_ipv6address`
   if [ "1" = "$SYSCFG_ipv6_verbose_logging" ] ; then
      LOG=/var/log/ipv6.log
   else
      LOG=/dev/null
   fi
}
bring_wan_down() {
   service_init
   sysevent set ipv6_connection_state "ipv6 bridge connection going down"
   sysevent set ipv6-errinfo
   sysevent set ipv6-status stopping
   echo 0 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra
   echo 0 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra_defrtr
   echo 0 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra_pinfo
   echo 0 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/autoconf
   sysevent set current_ipv6_wan_state down
   sysevent set ipv6-status stopped
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_connection_state "ipv6 bridge connection down"
}
bring_wan_up() {
   service_init
   if [ "0" = "$SYSCFG_ipv6_enable" ] 
   then
      exit 0
   fi
   sysevent set ipv6_connection_state "ipv6 bridge connection going up"
   echo 2 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra        # Accept RA even when forwarding is enabled
   echo 1 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra_defrtr # Accept default router (metric 1024)
   echo 1 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/accept_ra_pinfo  # Accept prefix information for SLAAC
   echo 1 > /proc/sys/net/ipv6/conf/$SYSCFG_lan_ifname/autoconf         # Do SLAAC
   sysevent set current_ipv6_wan_state up
   sysevent set ipv6-status started
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   sysevent set ipv6_connection_state "bridge connection up"
}
case "$1" in
   current_ipv6_link_state)
      ulog ipv6 bridge "$PID ipv6 link state is $CURRENT_LINK_STATE"
      if [ "up" != "$CURRENT_LINK_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 bridge "$PID ipv6 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog ipv6 bridge "$PID ipv6 link is down. Wan is already down"
            exit 0
         fi
      else
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 bridge "$PID ipv6 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$DESIRED_WAN_STATE" ] ; then
               bring_wan_up
               exit 0
            else
               ulog ipv6 bridge "$PID ipv6 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv6_wan_state)
      CURRENT_IPV6_STATUS=`sysevent get ipv6_wan-status`
      if [ "up" = "$DESIRED_WAN_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 bridge "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$CURRENT_LINK_STATE" ] ; then
               ulog ipv6 bridge "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 bridge "$PID wan is already down."
            if [ "stopped" != "$CURRENT_IPV6_STATUS" ] ; then
               sysevent set ipv6-status stopped
               sysevent set ipv6_firewall-restart
            fi
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog ipv6 bridge "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
