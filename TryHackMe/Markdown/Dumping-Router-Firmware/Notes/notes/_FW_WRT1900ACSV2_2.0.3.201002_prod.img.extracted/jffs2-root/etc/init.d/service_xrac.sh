#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="xrac"
MASTER_MODE=2
CRON_FILE=/etc/cron/cron.everyminute/${SERVICE_NAME}_check
PID_FILE=/var/run/${SERVICE_NAME}.pid
_PID="cat $PID_FILE"
SERVICE_FILE=/usr/local/lib/lua/5.1/${SERVICE_NAME}.lua
_STATUS="sysevent get ${SERVICE_NAME}-status"
PMON=/etc/init.d/pmon.sh
ITERATION=$2
is_running()
{
   if [ -f "$PID_FILE" ] && [ -n "`$_PID`" ]; then
        if test -d "/proc/`$_PID`"; then
            return 0
        else
            rm -f $PID_FILE
            return 1
        fi
    fi
    return 1
}
service_kill() {
   ulog "[${SERVICE_NAME}][service_kill] PID:`$_PID`"
   if [ -n "`$_PID`" ]; then
      kill -9 `$_PID`;
   fi
   rm -f $PID_FILE
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_check()
{
   ulog "[${SERVICE_NAME}][service_check] checking status"
   STATUS=$(sysevent get ${SERVICE_NAME}-status)
   if [ "$STATUS" == "starting" ]; then
      PREV_STATUS=$(sysevent get ${SERVICE_NAME}-prev_status)
      if [ "$STATUS" == "$PREV_STATUS" ]; then
         ulog "[${SERVICE_NAME}][service_check] WARNING: stuck in the starting state, so restarting the service" 
         service_stop
         service_start
      else
         sysevent set ${SERVICE_NAME}-prev_status $STATUS
      fi
   else
      ulog "[${SERVICE_NAME}][service_check] service $STATUS, so removing cron job"
      rm -f $CRON_FILE
   fi
}
schedule_service_check()
{
   cat << EOF > $CRON_FILE
#!/bin/sh
$0 service_check
EOF
   chmod +x $CRON_FILE
}
service_start ()
{
   ulog "[${SERVICE_NAME}][service_start] $2"
   ulog "[${SERVICE_NAME}][service_start] [dhcp_server-status:`sysevent get dhcp_server-status`]"
   check_ondemand
   if is_running; then
      ulog "${SERVICE_NAME} is already running"
   else
      sysevent set ${SERVICE_NAME}-status "starting"
      sysevent set xrac_provision_error
      if [ -z $ITERATION ]; then 
        $SERVICE_FILE &
      else
        $SERVICE_FILE --retry $ITERATION &
      fi
      echo "$!" > $PID_FILE
      ulog "$! written into $PID_FILE"
      schedule_service_check
   fi
}
service_stop ()
{
   ulog "[${SERVICE_NAME}][service_stop]"
   if is_running; then
      sysevent set ${SERVICE_NAME}-status "stopping"
      service_kill
   else  
      ulog "${SERVICE_NAME} does not appear to be running"
   fi
   sysevent set ${SERVICE_NAME}-status stopped
   sysevent set ${SERVICE_NAME}-prev_status
   rm -f $CRON_FILE
}
service_status ()
{
   ulog "[${SERVICE_NAME}][service_status]: `$_STATUS`"
}
check_ondemand ()
{
   ulog "[${SERVICE_NAME}][check_ondemand]"
   PPP_PROTO=`syscfg get wan_proto|grep -E "l2tp|ppoe|pptp"`
   PPP_MODE=`syscfg get ppp_conn_method`
   if [ "demand" = "$PPP_MODE" ] && [ $PPP_PROTO ] ; then
      ulog "XRAC cannot start if ppp_conn_method is on demand."
      service_stop
      exit 3
   fi
}
ulog "[${SERVICE_NAME}][main]($1)($2)"
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
   ${SERVICE_NAME}-status)
      service_status
      ;;
   wan-started)
      service_start
      ;;
   wan-stopped)
      service_stop
      ;;
   ppp_configuration_change)
      check_ondemand
      ;;
   service_check)
      service_check
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart [iteration] | ${SERVICE_NAME}-status | wan-started | wan-stopped | ppp_configuration_change" > /dev/console
      exit 3
      ;;
esac
