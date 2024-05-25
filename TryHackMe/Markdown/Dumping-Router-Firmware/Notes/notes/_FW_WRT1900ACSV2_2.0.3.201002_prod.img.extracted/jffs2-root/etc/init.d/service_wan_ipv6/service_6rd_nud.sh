#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="6rd_nud"
service_init ()
{
   eval `utctx_cmd get ipv6_enable 6rd_relay`
   if [ "1" != "$SYSCFG_ipv6_enable" ] ; then
      ulog ${SERVICE_NAME} status "IPv6 is not enabled."
      exit 0
   fi
   if [ -z "$SYSCFG_6rd_relay" ] ; then
      ulog ${SERVICE_NAME} status "No IPv6 6rd Border Relay provisioned"
      exit 0
   fi
   
   SYSEVENT_current_wan_ipv6address=`sysevent get current_wan_ipv6address`
   if [ -z "SYSEVENT_current_wan_ipv6address" ] ; then
      ulog ${SERVICE_NAME} status "No 6rd address has been discovered yet"
      exit 0
   fi
   SYSEVENT_default_router=`sysevent get default_router`
   if [ -z "$SYSEVENT_default_router" ] ; then
      ulog ${SERVICE_NAME} status "No default router found for wan"
      exit 0
   fi
   SYSEVENT_current_wan_ifname=`sysevent get current_wan_ifname`
   if [ -z "$SYSEVENT_current_wan_ifname" ] ; then
      ulog ${SERVICE_NAME} status "Unable to get ipv4 wan ifname using current_wan_ifname"
      exit 0
   fi
   NEXT_HOP_MAC=`arp -i $SYSEVENT_current_wan_ifname $SYSEVENT_default_router | cut -d' ' -f 4`
   if [ -z "$NEXT_HOP_MAC" -o "<incomplete>" = "$NEXT_HOP_MAC" ] ; then
      ulog ${SERVICE_NAME} status "Unable to calculate mac address of default router"
      exit 0
   fi
}   
service_start ()
{
   ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
   sysevent set 6rd_tunnel_status Disconnected
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
   
   ping -c 1 -W 10 $SYSCFG_6rd_relay -q 
   if [ "0" = "$?" ] ; then
      sysevent set 6rd_tunnel_status Undetermined
   fi
   /usr/sbin/6rd_nud_capture -t $SYSEVENT_current_wan_ipv6address -m $NEXT_HOP_MAC -6 $SYSCFG_6rd_relay -i $SYSEVENT_current_wan_ifname
   /usr/sbin/6rd_nud_injector -t $SYSEVENT_current_wan_ipv6address -m $NEXT_HOP_MAC -6 $SYSCFG_6rd_relay -i $SYSEVENT_current_wan_ifname
}
service_stop () 
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   killall 6rd_nud_capture
   killall 6rd_nud_injector
   sysevent set 6rd_tunnel_status
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_init
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
  *)
      echo "Usage: [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
