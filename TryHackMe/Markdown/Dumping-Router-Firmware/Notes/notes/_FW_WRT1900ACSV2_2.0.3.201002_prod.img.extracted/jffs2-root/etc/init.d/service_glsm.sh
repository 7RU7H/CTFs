#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="glsm"
ARP_MONITOR_HOSTS=/tmp/arp.hosts
lan_status_change ()
{
    CURRENT_LAN_STATE=`sysevent get lan-status`
    ulog glsm status "lan status changing"
    if [ "stopped" = "$CURRENT_LAN_STATE" ] ; then
        service_stop
    elif [ "started" = "$CURRENT_LAN_STATE" ] ; then
	service_start
    fi
}
do_start()
{
    if [ -f $ARP_MONITOR_HOSTS ] ; then
    	rm -f $ARP_MONITOR_HOSTS
    fi
    ulog glsm status "bring up glsm"    
    sysevent set ${SERVICE_NAME}-status started
    /usr/sbin/generic_link_status_monitor &
}
do_stop()
{
    ulog glsm status "shutdown glsm"
    sysevent set ${SERVICE_NAME}-status stopped
    killall -q generic_link_status_monitor
}
service_init ()
{
     ulog glsm status "glsm service init!"
     sysevent set ${SERVICE_NAME}-status stopped
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   SYSCFG_glsm_enable=`syscfg get glsm_enable`
   if [ "$SYSCFG_glsm_enable" != "1" ]; then
	return
   fi
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
        killall -q generic_link_status_monitor
        sysevent set ${SERVICE_NAME}-status starting
        do_start
   fi       
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status` 
   if [ "stopped" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-status stopping
      do_stop
   fi 
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
   lan-status)
      lan_status_change
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
