#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="firewall_trigger_monitor"
TRIGGER_HANDLER=trigger
service_start()
{
   PID=$$
   ulog triggers status "$PID starting trigger monitoring process"
   if [ -z `pidof trigger` ] ; then
      $TRIGGER_HANDLER
   fi
   sysevent set ${SERVICE_NAME}-status started
}
service_stop() {
   killall $TRIGGER_HANDLER
   while [ `pidof trigger` ]
   do
      usleep 300
   done
   sysevent set ${SERVICE_NAME}-status stopped
}
ulog triggers status "event $1"
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
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
