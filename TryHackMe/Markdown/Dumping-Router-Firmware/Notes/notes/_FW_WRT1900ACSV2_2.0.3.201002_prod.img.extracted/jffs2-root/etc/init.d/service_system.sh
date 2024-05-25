#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/network_functions.sh
SERVICE_NAME="system"
PID="($$)"
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get wan_physical_ifname wan_virtual_ifnum lan_ifname lan_ipaddr last_known_date`
   eval $FOO
   if [ $SYSCFG_FAILED = 'true' ] ; then
      ulog system status "$PID utctx failed to get some configuration data required by service-system"
      ulog system status "$PID THE SYSTEM IS NOT SANE"
      echo "[utopia] utctx failed to get some configuration data required by service-system" > /dev/console
      echo "[utopia] THE SYSTEM IS NOT SANE" > /dev/console
      sysevent set ${SERVICE_NAME}-status error
      sysevent set ${SERVICE_NAME}-errinfo "Unable to get crucial information from syscfg"
      exit
   fi
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      ulog system status "$PID system is starting"
      sysevent set ${SERVICE_NAME}-status starting 
      sysevent set ${SERVICE_NAME}-errinfo 
      service_init
      if [ -n "$SYSCFG_last_known_date" ] 
      then
         date -s $SYSCFG_last_known_date
      fi
      sysevent set phylink-start
      sysevent set forwarding-start
      SYSCFG_led_ui_rearport=`syscfg get led_ui_rearport`
      if [ "$SYSCFG_led_ui_rearport" != "0" ] ; then
         sysevent set led_ethernet_on
      else
         sysevent set led_ethernet_off	  
      fi
      
      sysevent set ${SERVICE_NAME}-status started 
      ulog system status "$PID system is started"
   fi
}
service_stop ()
{
   no_syscfg_commit=`sysevent get system_stop_no_syscfg_commit`
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" = "$STATUS" -o "stopping" = "$STATUS" ] ; then
      ulog system status "$PID system is already $STATUS"
      return 0
   fi
   ulog system status "$PID system is stopping"
   sysevent set ${SERVICE_NAME}-status stopping 
   ulog system status "$PID system is thinking about committing syscfg"
   if [ "$no_syscfg_commit" != "1" ] ; then
      echo "committing syscfg" > /dev/console
      ulog system status "$PID system is committing syscfg"
      syscfg commit
   fi
   echo "system is going down" > /dev/console
   echo 1 > /tmp/var/config/sysup
   if [ -f /etc/led/system_restarting.sh ]; then
       /etc/led/system_restarting.sh
   fi
   sysevent set ipv6-stop
   sleep 2
   sysevent set lan-stop
   sysevent set bridge-stop
   sleep 1
   sysevent set wan-stop
   sleep 2
   /usr/bin/killall5
   sleep 1
   /bin/umount -a -r
   sleep 1
   echo "system is going down now" > /dev/console
   sysevent set ${SERVICE_NAME}-status stopped 
   ulog system status "$PID system is stopped"
}
case "$1" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
