#!/bin/sh
export TZ=`sysevent get TZ`
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wifi_scheduler"
WIFI_DEBUG_SETTING=`syscfg_get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
service_init()
{
	return 0
}
service_start()
{
	wait_till_end_state ${SERVICE_NAME}
	wait_till_end_state "wifi"
	if [ -z "`syscfg_get wifi_scheduler::if_enabled`" ] || [ "`syscfg_get wifi_scheduler::enabled`" != "1" ]; then
		return 1
	fi
	STATUS=`sysevent get ${SERVICE_NAME}-status`
	if [ "started" = "$STATUS" ] || [ "starting" = "$STATUS" ]; then
		return 1
	fi
	sysevent set ${SERVICE_NAME}-status starting
	wifi_scheduler_changed_handler
	sysevent set ${SERVICE_NAME}-status started
	return 0
}
service_stop()
{
	wait_till_end_state ${SERVICE_NAME}
	if [ -z "`syscfg_get wifi_scheduler::if_enabled`" ] || [ "`syscfg_get wifi_scheduler::enabled`" != "1" ]; then
		return 1
	fi
	STATUS=`sysevent get ${SERVICE_NAME}-status`
	if [ "stopped" = "$STATUS" ] || [ "stopping" = "$STATUS" ] || [ -z "$STATUS" ]; then
		return 1
	fi
	
	sysevent set ${SERVICE_NAME}-status stopping
	sysevent set ${SERVICE_NAME}-status stopped
	return 0
}
service_restart()
{
	service_stop
	service_start
	return 0
}
wifi_scheduler_changed_handler()
{
	IF_ENABLED_LIST=`syscfg_get wifi_scheduler::if_enabled`
	if [ -z "$IF_ENABLED_LIST" ]; then
		return 1
	fi
	if [ "`syscfg_get wifi_scheduler::enabled`" != "1" ]; then
		sysevent set wifi-start
		return 0
	fi
	
	DAY=`date +%a`
	HOUR=`date +%k`
	MINUTE=`date +%M`
	case "$DAY" in
		Mon)
			DAY_RULES=`syscfg_get wifi_scheduler::monday_time_blocks`
			;;			
		Tue)
			DAY_RULES=`syscfg_get wifi_scheduler::tuesday_time_blocks`
			;;			
		Wed)
			DAY_RULES=`syscfg_get wifi_scheduler::wednesday_time_blocks`
			;;			
		Thu)
			DAY_RULES=`syscfg_get wifi_scheduler::thursday_time_blocks`
			;;			
		Fri)
			DAY_RULES=`syscfg_get wifi_scheduler::friday_time_blocks`
			;;			
		Sat)
			DAY_RULES=`syscfg_get wifi_scheduler::saturday_time_blocks`
			;;			
		Sun)
			DAY_RULES=`syscfg_get wifi_scheduler::sunday_time_blocks`
			;;			
		*)
			echo"${SERVICE_NAME}, ERROR: Invalid day of week=$DAY"
			return 1
			;;
	esac
	if [ "${#DAY_RULES}" != "48" ]; then
		echo "${SERVICE_NAME}, invalid rules format, day=${DAY}: rules=${DAY_RULES}"
		ulog ${SERVICE_NAME} status "${SERVICE_NAME}, invalid rules format, day=${DAY}: rules=${DAY_RULES}"
		return 1
	fi
	if [ "${MINUTE}" -lt "30" ]; then
		RULE_INDEX=`expr $HOUR \* 2`      # first byte
	else
		RULE_INDEX=`expr $HOUR \* 2 + 1`  # second byte
	fi
	RULE=`echo ${DAY_RULES:$RULE_INDEX:1}`
		
	if [ "${RULE}" = "0" ] && [ "`sysevent get wifi-status`" != "stopping" ] && [ "`sysevent get wifi-status`" != "stopped" ] ; then
		sysevent set wifi-stop
	fi
	if [ "${RULE}" = "1" ] && [ "`sysevent get wifi-status`" != "starting" ] && [ "`sysevent get wifi-status`" != "started" ] ; then
		sysevent set wifi-start
	fi
		
	return 0
}
service_init 
case "$1" in
	wifi_scheduler-start)
		service_start
		;;
	wifi_scheduler-stop)
		service_stop
		;;
	wifi_scheduler-restart)
		service_restart
		;;
	wifi_scheduler_changed)
		wifi_scheduler_changed_handler
		;;
	wifi-status)
		if [ "`sysevent get wifi-status`" = "started" ] && [ "`syscfg_get wifi_scheduler::enabled`" = "1" ]; then
			wifi_scheduler_changed_handler
		fi
		;;
	*)
	echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
		exit 3
		;;
esac
