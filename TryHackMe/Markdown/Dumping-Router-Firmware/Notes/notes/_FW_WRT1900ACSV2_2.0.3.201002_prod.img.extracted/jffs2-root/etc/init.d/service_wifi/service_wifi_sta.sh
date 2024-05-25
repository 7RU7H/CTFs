#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_wifi/wifi_physical.sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wifi_sta"
WIFI_DEBUG_SETTING=`syscfg_get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
service_init()
{
	ulog wlan status "${SERVICE_NAME}, service_init()"
	SYSCFG_FAILED='false'
	FOO=`utctx_cmd get wifi_bridge::mode wifi_bridge::radio wifi_bridge::ssid bridge_mode`
	eval $FOO
	if [ $SYSCFG_FAILED = 'true' ] ; then
		ulog wlan status "${SERVICE_NAME}, $PID utctx failed to get some configuration data required by service-forwarding"
		DEBUG echo "[utopia] THE SYSTEM IS NOT SANE" > /dev/console
		sysevent set ${SERVICE_NAME}-status error
		sysevent set ${SERVICE_NAME}-errinfo "Unable to get crucial information from syscfg"
		return 0
	fi
}
service_start()
{
	ulog wlan status "${SERVICE_NAME}, service_start()"
	DEBUG echo "Start ${SERVICE_NAME}" > /dev/console
}
service_stop()
{
	ulog wlan status "${SERVICE_NAME}, service_stop()"
	bring_down_sta_link
	DEBUG echo "Stop ${SERVICE_NAME}" > /dev/console
}
service_restart()
{
	ulog wlan status "${SERVICE_NAME}, service_restart()"
	service_stop
	service_start
}
wifi_bridge_config_changed()
{
	echo "${SERVICE_NAME}, rebooting to enable wifi bridge mode"
	reboot
}
bring_down_sta_link()
{
	if [ "1" != "`syscfg_get wifi_bridge::mode`" ] || [ "started" != "`sysevent get lan-status`" ] ; then
		return
	fi
	PHY_IF=`syscfg_get lan_wl_physical_ifnames`
	STA_IF=`echo "wdev0 wdev1" | sed 's/'"${PHY_IF}"'//g' | sed 's/ //g'`
	STA_IF="$STA_IF"sta0
	ifconfig $STA_IF down
	LAN_IFNAME=`syscfg_get lan_ifname`
	brctl delif $LAN_IFNAME $STA_IF
	sysevent set wifi_sta_up 0
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
		service_restart
		;;
	wifi_bridge_config_changed)
		wifi_bridge_config_changed
		;;
	*)
	echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart | wifi_bridge_config_changed]" > /dev/console
		exit 3
		;;
esac
