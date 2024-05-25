#!/bin/sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="wdtprimeit"
SELF_NAME="`basename $0`"
BIN=wdtprimeit
APP="/usr/sbin/${BIN}"
_PID="pidof ${BIN}"
PID_FILE=/var/run/${BIN}.pid
PMON=/etc/init.d/pmon.sh
service_kill() {
   if [ -n "`$_PID`" ]; then
      kill -9 `$_PID`;
   fi
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_start() {
   if [ -z "`$_PID`" ]; then
      ${APP} &
      sysevent set ${SERVICE_NAME}-status "started"
   fi
   echo "`$_PID`" > $PID_FILE
   $PMON setproc ${SERVICE_NAME} $BIN $PID_FILE "/etc/init.d/service_${SERVICE_NAME}.sh ${SERVICE_NAME}-restart"
}
service_stop () {
   if [ -n "`$_PID`" ]; then
      killall -15 $SERVICE_NAME
      sleep 1	# TODO: Check if process stopped.
      sysevent set ${SERVICE_NAME}-status "stopped"
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
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]" >&2
      ;;
esac
