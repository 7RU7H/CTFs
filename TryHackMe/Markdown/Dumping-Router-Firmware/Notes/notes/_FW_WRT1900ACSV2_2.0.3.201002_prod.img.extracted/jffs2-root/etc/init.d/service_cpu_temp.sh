#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="cpu_temp"
CRON_TAB_FILE="/tmp/cron/cron.everyminute/cpu_temp.sh"
create_cron_file () {
(
cat <<'End-of-Text'
#!/bin/sh
LAST_TEMP="`sysevent get cpu-temp`"
CPU_TEMP=`cat /sys/class/hwmon/hwmon0/device/temp1_input`
CUR_TEMP=`echo $CPU_TEMP`
if [ "$LAST_TEMP" != "$CUR_TEMP" ] ; then
	if [ "$CUR_TEMP" -gt "105" ] ; then
		echo "cpu is too hot $CUR_TEMP" >> /dev/console
    logger "WARNING - cpu is too hot $CUR_TEMP"
  else
    logger "cpu temp $CUR_TEMP"
  fi
  sysevent set cpu-temp "$CUR_TEMP"
fi
End-of-Text
) > $CRON_TAB_FILE
   echo "cpu temp monitor created" > /dev/console
   return 0
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status starting
      create_cron_file
      chmod +x $CRON_TAB_FILE
      check_err $? "Couldnt handle start"
      sysevent set ${SERVICE_NAME}-status started
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status stopping
      rm -rf $CRON_TAB_FILE
      check_err $? "Couldnt handle stop"
      sysevent set ${SERVICE_NAME}-status stopped
   fi
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
	system-start)
      service_start
      ;;
   *)
			echo "Err: $1" > /dev/console
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
