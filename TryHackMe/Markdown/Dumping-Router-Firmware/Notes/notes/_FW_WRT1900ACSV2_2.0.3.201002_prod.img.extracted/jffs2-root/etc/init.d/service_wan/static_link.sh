#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
do_start_static() {
   ip -4 addr add  $SYSCFG_wan_ipaddr/$SYSCFG_wan_netmask broadcast + dev $SYSEVENT_current_wan_ifname
   ip -4 link set $SYSEVENT_current_wan_ifname up
   if [ "1" = "$SYSCFG_default" ] ; then
      OLD_ROUTE=`ip -4 route show | grep default | grep dslite`
      if [ -n "$OLD_ROUTE" ] ; then
         ip -4 route del $OLD_ROUTE
      fi
      ip -4 route add $SYSCFG_wan_default_gateway dev $SYSEVENT_current_wan_ifname
      ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSCFG_wan_default_gateway
   else
      ip -4 route add $SYSCFG_wan_default_gateway dev $SYSEVENT_current_wan_ifname
      ip -4 route add ${SYSCFG_wan_ipaddr}/${SYSCFG_wan_netmask} dev $SYSEVENT_current_wan_ifname via $SYSCFG_wan_default_gateway
   fi
   ip -4 route flush cache
   ulog static_wan_link status "$PID setting ${NAMESPACE}_current_ipv4_link_state up"
   sysevent set ${NAMESPACE}_current_ipv4_link_state up
}
do_stop_static() {
   if [ "1" = "$SYSCFG_default" ] ; then
      ip -4 route del default dev $SYSEVENT_current_wan_ifname via $SYSCFG_wan_default_gateway
      ip -4 route del $SYSCFG_wan_default_gateway dev $SYSEVENT_current_wan_ifname
   else
      ip -4 route del ${SYSCFG_wan_ipaddr}/${SYSCFG_wan_netmask} dev $SYSEVENT_current_wan_ifname via $SYSCFG_wan_default_gateway
      ip -4 route del $SYSCFG_wan_default_gateway dev $SYSEVENT_current_wan_ifname
   fi
   ip -4 route flush cache
   ip -4 addr flush dev $SYSEVENT_current_wan_ifname
   ulog static_wan_link status "$PID setting ${NAMESPACE}_current_ipv4_link_state down"
   sysevent set ${NAMESPACE}_current_ipv4_link_state down
}
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
if [ -z "$1" ] ; then
   return
fi
service_init $1
ulog static_wan_link status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog static_wan_link status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog static_wan_link status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   phylink_wan_state)
      ulog static_wan_link status "$PID physical link is $SYSEVENT_phylink_wan_state"
      if [ "up" != "$SYSEVENT_phylink_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
            ulog static_wan_link status "$PID physical link is down. Setting link down."
            do_stop_static
            exit 0
         else
            ulog static_wan_link status "$PID physical link is down. Link is already down."
            exit 0
         fi
      else
         if [ "up" = "SYSEVENT_current_ipv4_link_state" ] ; then
            ulog static_wan_link status "$PID physical link is up. Link is already up."
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
                  ulog static_wan_link status "$PID starting static link"
                  do_start_static
                  exit 0
            else
               ulog static_wan_link status "$PID physical link is up, but desired link state is down."
               exit 0;
            fi
         fi
      fi
      ;;
   desired_ipv4_link_state)
      if [ "$SYSEVENT_current_ipv4_link_state" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         ulog static_wan_link status "$PID wan is already is desired state $SYSEVENT_current_ipv4_link_state"
         exit
      fi
      if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         if [ "down" = "$SYSEVENT_phylink_wan_state" ] ; then
            ulog static_wan_link status "$PID up requested but physical link is still down"
            exit 0;
         fi
         ulog static_wan_link status "$PID up requested"
         do_start_static
         exit 0
      else
         ulog static_wan_link status "$PID down requested"
         do_stop_static
         exit 0
      fi
      ;;
  *)
        ulog static_wan_link status "$PID Invalid parameter $1 "
        exit 3
        ;;
esac
