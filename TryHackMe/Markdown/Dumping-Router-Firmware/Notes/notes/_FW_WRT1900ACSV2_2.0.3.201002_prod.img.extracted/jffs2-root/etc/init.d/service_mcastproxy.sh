#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="mcastproxy"
SELF_NAME="`basename $0`"
BIN=igmpproxy
CONF_FILE=/tmp/igmpproxy.conf
do_start_igmpproxy () {
   LOCAL_CONF_FILE=/tmp/igmpproxy.conf$$
   killall $BIN > /dev/null 2>&1
   
   if [ "$SYSCFG_hardware_vendor_name" = "Broadcom" ] ; then
       if [ -f /usr/sbin/igmp ]; then
           ulog ${SERVICE_NAME} status "killall -q igmp" 
           killall -q igmp > /dev/null 2>&1
       fi
   fi
   rm -rf $LOCAL_CONF_FILE
   echo "quickleave" >> $LOCAL_CONF_FILE
   echo "phyint $WAN_IFNAME upstream" >> $LOCAL_CONF_FILE
    echo "blacklist 239.255.255.250/32" >> $LOCAL_CONF_FILE
   echo "phyint $SYSCFG_lan_ifname downstream" >> $LOCAL_CONF_FILE
   cat $LOCAL_CONF_FILE > $CONF_FILE
   rm -f $LOCAL_CONF_FILE 
   ulog ${SERVICE_NAME} status "start $BIN $CONF_FILE" 
   $BIN $CONF_FILE &
   sleep 2
   if [ "$SYSCFG_hardware_vendor_name" = "Broadcom" ] ; then
       if [ -f /usr/sbin/igmp ]; then
           ulog ${SERVICE_NAME} status "start igmp $WAN_IFNAME" 
           /usr/sbin/igmp $WAN_IFNAME 
       fi
   fi
}
service_init ()
{
   eval `utctx_cmd get igmpproxy_enabled lan_ifname wan_virtual_ifname  wan_proto block_multicast hardware_vendor_name`
   CURRENT_WAN_STATUS=`sysevent get wan-status`
   CURRENT_LAN_STATUS=`sysevent get lan-status`
   CURRENT_IGMPPROXY_STATUS=`sysevent get ${SERVICE_NAME}-status`	
   WAN_IFNAME=`sysevent get current_wan_ifname`
   START_SERVICE=0
   STOP_SERVICE=0
   if [ "$CURRENT_WAN_STATUS" = "started" ] && [ "$CURRENT_LAN_STATUS" = "started" ] && [ ! -z "$WAN_IFNAME" ] && [ "1" = "$SYSCFG_igmpproxy_enabled" ] && [ "0" = "$SYSCFG_block_multicast" ] ; then
      START_SERVICE=1
   elif [ "$CURRENT_IGMPPROXY_STATUS" = "started" ] ; then
      STOP_SERVICE=1
   fi
}
service_start () 
{
   ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
   if [ "$CURRENT_WAN_STATUS" = "started" ] && [ "$CURRENT_LAN_STATUS" = "started" ] && [ ! -z "$WAN_IFNAME" ] && [ "1" = "$SYSCFG_igmpproxy_enabled" ] && [ "0" = "$SYSCFG_block_multicast" ] ; then
      do_start_igmpproxy
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status "started"
   fi
}
service_stop () 
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   killall $BIN > /dev/null 2>&1
   rm -rf $CONF_FILE
   if [ "$SYSCFG_hardware_vendor_name" = "Broadcom" ]; then
       if [ -f /usr/sbin/igmp ]; then
           ulog ${SERVICE_NAME} status "killall -q igmp" 
           killall -q igmp > /dev/null 2>&1
       fi
   fi
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
  wan-status)
      if [ "1" == "$START_SERVICE" ] ; then
         service_start
      elif [ "1" == "$STOP_SERVICE" ] ; then
         service_stop
      fi 
      ;;
  lan-status)
      if [ "1" == "$START_SERVICE" ] ; then
         service_start
      elif [ "1" == "$STOP_SERVICE" ] ; then
         service_stop
      fi 
      ;;
  *)
      echo "Usage: $SELF_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart | wan-status | lan-status ]" >&2
      exit 3
      ;;
esac
