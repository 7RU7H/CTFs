#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_registration_functions.sh
SERVICE_NAME="dmz"
PID="($$)"
SERVICE_DEFAULT_HANDLER="$0"
SERVICE_HANDLER_FLAGS=$TUPLE_FLAG_EVENT
         
SERVICE_DETECT_EVENTS="\
          lan_dhcp_client_change|$SERVICE_DEFAULT_HANDLER|$ACTION_FLAG_COLLAPSE_PENDING_QUEUE|$SERVICE_HANDLER_FLAGS ;\
          lan_device_detected|$SERVICE_DEFAULT_HANDLER|$ACTION_FLAG_COLLAPSE_PENDING_QUEUE|$SERVICE_HANDLER_FLAGS ;\
          lan_nbtdevice_detected|$SERVICE_DEFAULT_HANDLER|$ACTION_FLAG_COLLAPSE_PENDING_QUEUE|$SERVICE_HANDLER_FLAGS ;\
          lan_arpdevice_detected|$SERVICE_DEFAULT_HANDLER|$ACTION_FLAG_COLLAPSE_PENDING_QUEUE|$SERVICE_HANDLER_FLAGS ;\
"
FILE_LEASES="/etc/dnsmasq.leases"
FILE_LANHOSTS="/tmp/lanhosts/lanhosts"
FILE_DETECTED="/tmp/detected_hosts"
DMZ_HOST_IPADDR=
DMZ_HOST_MACADDR=
service_stop ()
{
   FIREWALL_STATE=`sysevent get firewall-status`
   
   sysevent set ${SERVICE_NAME}-status stopping
   
   SAVEIFS=$IFS
   IFS=';'
   for detect in $SERVICE_DETECT_EVENTS ; do
      if [ -n "$detect" ] && [ " " != "$detect" ] ; then
         IFS=$SAVEIFS
         sm_rm_event $SERVICE_NAME "$detect"
         IFS=';'
      fi
   done
   IFS=$SAVEIFS
  
   if [ "$FIREWALL_STATE" != "starting" ]; then
      ulog $SERVICE_NAME status "$PID service_stop () restarts firewall"
      sysevent set firewall-restart
   fi
 
   sysevent set ${SERVICE_NAME}-status stopped
   ulog $SERVICE_NAME status "stopped"
}
lookup_ipv4_by_mac ()
{
   MAC_ADDR=$1
   ulog $SERVICE_NAME status "$PID lookup_ipv4_by_mac ($MAC_ADDR) -ENTER"
   if [ -f $FILE_FILE_LEASES ]; then
      DMZ_HOST_IPADDR=`cat $FILE_LEASES | grep $MAC_ADDR | awk '{ print $3 }'`     
   fi
   
   if [ -z $DMZ_HOST_IPADDR ] && [ -f $FILE_DETECTED ]; then
      DMZ_HOST_IPADDR=`cat $FILE_DETECTED | grep $MAC_ADDR | awk '{ print $3 }'`
   fi
   
   if [ -z $DMZ_HOST_IPADDR ] && [ -f $FILE_LANHOSTS ]; then
      DMZ_HOST_IPADDR=`cat $FILE_LANHOSTS | grep $MAC_ADDR | awk '{ print $1 }'`
   fi
   if [ -z $DMZ_HOST_IPADDR ]  ; then
      DMZ_HOST_IPADDR=` arp  | grep -i $DMZ_HOST_MACADDR | awk '{print $2}' | sed 's/(//' | sed 's/)//' | grep '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}' | sed 's/ //' `
   fi
   if [ -n $DMZ_HOST_IPADDR ]  ; then
      syscfg set dmz_dst_ip_addr ${DMZ_HOST_IPADDR}
      syscfg commit
   fi
   ulog $SERVICE_NAME status "$PID lookup_ipv4_by_mac ($DMZ_HOST_IPADDR) -EXIT"
   
   return
}
get_dmz_host ()
{
   DMZ_ENABLED=`syscfg get dmz_enabled`
   : ${DMZ_ENABLED:=0}
   
   ulog $SERVICE_NAME status "$PID get_dmz_host () -ENTER"
   if [ "$DMZ_ENABLED" == "1" ] ; then
      DMZ_HOST_IPADDR=`syscfg get dmz_dst_ip_addr`
      if [ -z $DMZ_HOST_IPADDR ] || [ "$DMZ_HOST_IPADDR" == "0" ] || [ "$DMZ_HOST_IPADDR" == "0.0.0.0" ]; then
         DMZ_HOST_MACADDR=`syscfg get dmz_dst_mac_addr`
         if [ -z $DMZ_HOST_MACADDR ]; then
            ulog $SERVICE_NAME error "$PID DMZ is enabled but not provisioned ($DMZ_HOST_IPADDR) ($DMZ_HOST_MACADDR)"
            DMZ_ENABLED=0
         else
            lookup_ipv4_by_mac  $DMZ_HOST_MACADDR
         fi
      fi
   fi
   ulog $SERVICE_NAME status "$PID get_dmz_host ($DMZ_ENABLED,$DMZ_HOST_IPADDR) -EXIT"
   return $DMZ_ENABLED
}
check_firewall_rules ()
{
   DMZ_HOST_IP=$1
   ulog $SERVICE_NAME status "$PID check_firewall_rules ($DMZ_HOST_IP) -ENTER"
   KEY=`echo $DMZ_HOST_IP | sed 's/\./\\\./g'`
   RULES=`iptables -L -nv | grep wan2lan.*$KEY`
   
   ulog $SERVICE_NAME status "$PID check_firewall_rules () -EXIT"
   
   if [ "$RULES" == "" ]; then
      return 0;
   fi
   return 1;
}
check_pre_conditions()
{
   FIREWALL_STATE=`sysevent get firewall-status`
   get_dmz_host
   RET=$?
   
   if [ "$RET" == "0" ]; then
      ulog $SERVICE_NAME warning "$PID DMZ is disabled. stop the dmz listener"
      service_stop
      return 0
   fi
   
   if [ "$FIREWALL_STATE" == "started" ] ; then
      return 1
   else   
      ulog $SERVICE_NAME warning "$PID check_pre_conditions not met ($FIREWALL_STATE,$RET)"
   fi
   return 0
}
provision_firewall()
{
   ulog $SERVICE_NAME status "$PID provision_firewall ($DMZ_HOST_IPADDR) -ENTER"
   check_firewall_rules $DMZ_HOST_IPADDR
   RET=$?
   if [ "$RET" == "0" ]; then
      ulog $SERVICE_NAME status "$PID provision_firewall is needed"
      sysevent set firewall-restart
   fi
   ulog $SERVICE_NAME status "$PID provision_firewall () -EXIT"
}
check_detected ()
{
   DETECTED=$1
   ulog $SERVICE_NAME status "$PID check_detected ($DETECTED) -ENTER"
   check_pre_conditions
   RET=$?
   
   if [ "$RET" == "1" ]; then
      if [ "$DETECTED" == "$DMZ_HOST_IPADDR" ]; then
         provision_firewall
      else
         ulog $SERVICE_NAME status "$PID check_detected () firewall provisionning is not needed"        
      fi
   fi
   ulog $SERVICE_NAME status "$PID check_detected () -EXIT"
}
service_start ()
{
   MY_STATE=`sysevent get ${SERVICE_NAME}-status`
   
   if [ "$MY_STATE" == "started" -o "$MY_STATE" == "starting" ]; then
         ulog $SERVICE_NAME status "$PID service_start () : but already started; just return"
         return
   fi
   
   sysevent set ${SERVICE_NAME}-status starting
   
   DMZ_ENABLED=`syscfg get dmz_enabled`
   : ${DMZ_ENABLED:=0}
   
   if [ $DMZ_ENABLED == "0" ]; then
      ulog $SERVICE_NAME status "$PID service_start () : dmz is disabled; just stop."
      sysevent set ${SERVICE_NAME}-status stopped
      return
   fi
   SAVEIFS=$IFS
   IFS=';'
   for detect in $SERVICE_DETECT_EVENTS ; do
      if [ -n "$detect" ] && [ " " != "$detect" ] ; then
         IFS=$SAVEIFS
         sm_register_one_event $SERVICE_NAME $detect
         IFS=';'
      fi
   done
   IFS=$SAVEIFS
     
   sysevent set ${SERVICE_NAME}-status started
      
   check_pre_conditions
   RET=$?
   
   if [ "$RET" == "1" ]; then
      provision_firewall
   fi
}
ulog $SERVICE_NAME status "event $1 ($2)"
case "$1" in
   ${SERVICE_NAME}-start|lan-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop|lan-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      service_start
      ;;
   lan_dhcp_client_change)
      opp=`echo $2 | cut -f 1 -d ' '`
      if [ "$opp" == "add" ] ; then
         detected_ipaddr=`echo $2 | cut -f 3 -d ' '`
         check_detected "$detected_ipaddr"
      fi
      ;;
   lan_nbtdevice_detected)
      detected_ipaddr=`echo $2 | cut -f 3 -d ' '`
      check_detected "$detected_ipaddr"
      ;;
   lan_device_detected|lan_arpdevice_detected)
      detected_ipaddr=`echo $2 | cut -f 2 -d ' '`
      check_detected "$detected_ipaddr"
      ;;
   *)
      echo "Usage: service_$SERVICE_NAME.sh [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart ]" > /dev/console
      exit 3
      ;;
esac
