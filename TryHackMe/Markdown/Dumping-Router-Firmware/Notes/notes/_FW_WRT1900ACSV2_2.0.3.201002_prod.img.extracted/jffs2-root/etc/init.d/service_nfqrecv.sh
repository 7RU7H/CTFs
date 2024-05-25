#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="nfqrecv"
BIN=nfqrecv
APP=/usr/bin/${BIN}
_PID="pidof ${BIN}"
PID_FILE=/var/run/${BIN}.pid
PMON=/etc/init.d/pmon.sh
do_start()
{
    nice -n -10 ${APP} &
    sysevent set $SERVICE_NAME-status started
}
do_stop()
{
    killall -15 ${BIN}
    sysevent set $SERVICE_NAME-status stopped
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      do_start
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $? "Unable to start $SERVICE_NAME"
      else
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status started
      fi
   fi
   echo "`$_PID`" > $PID_FILE
   $PMON setproc ${SERVICE_NAME} $BIN $PID_FILE "/etc/init.d/service_${SERVICE_NAME}.sh ${SERVICE_NAME}-restart"
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status` 
   if [ "stopped" != "$STATUS" ] ; then
      do_stop
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $ERR "Unable to stop $SERVICE_NAME"
      else
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status stopped
      fi
   fi
   rm -f $PID_FILE
   $PMON unsetproc ${SERVICE_NAME}
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
      service_start
      ;;
   lan-started)
      service_start
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart] | lan-status" > /dev/console
      exit 3
      ;;
esac
