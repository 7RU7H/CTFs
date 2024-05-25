#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wps_pbc"
echo "wifi, ${SERVICE_NAME}, sysevent received: $1"
SELF_NAME="`basename $0`"
IF_NAME_0=`syscfg_get wl0_user_vap`
IF_NAME_1=`syscfg_get wl1_user_vap`
IF_LIST="$IF_NAME_0 $IF_NAME_1"
EVENT_HW_BUTTON="wps_hw_button"
EVENT_WPS_MONITOR="wl_wps_status"
WPS_HANDLER="/etc/init.d/service_wifi/start_wps.sh"
WPS_MONITOR="/etc/init.d/service_wifi/wps_monitor.sh"
WIFI_DEBUG_SETTING=`syscfg_get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
service_start() {
	ulog ${SERVICE_NAME} status "wps pbc service start"
	sysevent set wps_process incomplete
	killall -q wps_monitor.sh
	killall -q start_wps.sh
	$WPS_HANDLER wps_pbc &
	$WPS_MONITOR &
	sysevent set ${SERVICE_NAME}-errinfo "wps hw button is pressed"
	sysevent set ${SERVICE_NAME}-status "started"
	return 0
}
service_stop () {
	ulog ${SERVICE_NAME} status "wps pbc service stop" 
	sleep 2
	killall -q wps_monitor.sh
	killall -q start_wps.sh
	sysevent set ${SERVICE_NAME}-errinfo "wps hw button is released"
	sysevent set ${SERVICE_NAME}-status "stopped"
	sysevent set wl_wps_progress 0
}
service_init() {
	ulog ${SERVICE_NAME} status "wps pbc service init"
	SYSCFG_FAILED='false'
	FOO=`utctx_cmd get wl0_ssid wl1_ssid`
	eval $FOO
	if [ $SYSCFG_FAILED = 'true' ] ; then
		ulog ${SERVICE_NAME} status "$PID utctx failed to get some configuration data required by service $SERVICE_NAME"
		sysevent set ${SERVICE_NAME}-status error 
		sysevent set ${SERVICE_NAME}-errinfo "failed to get crucial information from syscfg"
		exit
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
	${EVENT_HW_BUTTON})
		if [ "disabled" = "`syscfg get wl0_wps_state`" ] && [ "disabled" = "`syscfg get wl1_wps_state`" ]; then
			exit
		fi
		STATUS=`sysevent get ${EVENT_HW_BUTTON}`
		if [ "pressed" = "$STATUS" ] && [ "disabled" != "`syscfg get wps_user_setting`" ]; then
			service_start
			sysevent set wl_wps_status running
		fi
		;;
	${EVENT_WPS_MONITOR})
		STATUS=`sysevent get ${EVENT_WPS_MONITOR}`
		if [ "success" = "$STATUS" ] || [ "failed" = "$STATUS" ]; then
			service_stop "$STATUS"
		fi
		;;
	*)
		echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]" >&2
		exit 3
		;;
esac
