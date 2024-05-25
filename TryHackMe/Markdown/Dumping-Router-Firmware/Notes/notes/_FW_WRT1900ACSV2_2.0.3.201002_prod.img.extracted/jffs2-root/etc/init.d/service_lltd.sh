#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="lltd"
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   BRIDGEMODE=`syscfg get bridge_mode`
   if [ "1" = "$BRIDGEMODE" ] || [ "2" = "$BRIDGEMODE" ] ; then
      ulog ${SERVICE_NAME} status "no ${SERVICE_NAME} in bridge mode."
      exit
   fi
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
       if [ "starting" != "$STATUS" ] ; then
          sysevent set ${SERVICE_NAME}-status starting
          LANIFNAME=`syscfg get lan_ifname`
          lld2d $LANIFNAME
          ulog ${SERVICE_NAME} status "start."
          check_err $? "Couldnt handle start"
          sysevent set ${SERVICE_NAME}-status started
       fi
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-status stopping
      killall lld2d > /dev/null 2>&1
      ulog ${SERVICE_NAME} status "stop."
      
      check_err $? "Couldnt handle stop"
      sysevent set ${SERVICE_NAME}-status stopped
   fi
}
ulog lltd status "event $1"
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
      if [ "`syscfg get bridge_mode`" = "0" ] && [ "`sysevent get lan-status`" = "started" ] ; then
            service_start
      else
            service_stop
      fi
      ;;
   *)
      echo "Usage: service_$SERVICE_NAME.sh [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart | lan-status ]" > /dev/console
      exit 3
      ;;
esac
