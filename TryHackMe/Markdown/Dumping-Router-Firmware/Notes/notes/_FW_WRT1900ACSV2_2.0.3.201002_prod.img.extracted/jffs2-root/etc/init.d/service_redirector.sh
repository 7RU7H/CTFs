#!/bin/sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="redirector"
SELF_NAME="`basename $0`"
BIN=redirector
APP=/usr/bin/${BIN}
_STATUS="sysevent get ${SERVICE_NAME}-status"
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
      sysevent set ${SERVICE_NAME}-status "starting"
      ulimit -s 2048
      ${APP} &
      wait_till_end_state ${SERVICE_NAME}
      if [ "starting" == "`$_STATUS`" ]; then
         service_kill
         return 1
      fi
   fi
   echo "`$_PID`" > $PID_FILE
   $PMON setproc ${SERVICE_NAME} $BIN $PID_FILE "/etc/init.d/service_${SERVICE_NAME}.sh ${SERVICE_NAME}-restart"
}
service_stop () {
   if [ -n "`$_PID`" ]; then
      sysevent set ${SERVICE_NAME}-status "stopping"
      killall -15 $SERVICE_NAME
      wait_till_end_state ${SERVICE_NAME}
      if [ "stopping" == "`$_STATUS`" ]; then
         service_kill
      fi
   fi
   rm -f $PID_FILE
   $PMON unsetproc ${SERVICE_NAME}
}
case "$1" in
  ${SERVICE_NAME}-start)
      if [ "`syscfg get bridge_mode`" = "0" ] && [ "`sysevent get lan-status`" != "started" ]; then
          ulog wlan status "LAN is not started. So ignore the request"
          exit 0
      fi
      service_start
      ;;
  ${SERVICE_NAME}-stop)
      service_stop
      ;;
  ${SERVICE_NAME}-restart)
      if [ "`syscfg get bridge_mode`" = "0" ] && [ "`sysevent get lan-status`" != "started" ]; then
          ulog wlan status "LAN is not started. So ignore the request"
          exit 0
      fi
      service_stop
      service_start
      ;;
  lan-status)
      LAN_STATUS=`sysevent get lan-status`
      if [ "started" == "${LAN_STATUS}" ] ; then
          service_start
      elif [ "stopped" == "${LAN_STATUS}" ] ; then
          service_stop
      fi
      ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|lan-status]" >&2
      ;;
esac
