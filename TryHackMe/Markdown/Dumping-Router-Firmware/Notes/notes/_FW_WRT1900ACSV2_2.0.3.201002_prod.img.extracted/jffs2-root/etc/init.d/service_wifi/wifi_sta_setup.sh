#!/bin/sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
source /etc/init.d/service_wifi/wifi_platform_specific_setting.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wifi_sta_setup"
WIFI_DEBUG_SETTING=`syscfg_get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
HOSTNAME=`hostname`
BRIDGE_NAME=`syscfg_get lan_ifname`
BRIDGE_MODE=`syscfg_get bridge_mode`
SSID=`syscfg_get wifi_bridge::ssid`
RADIO=`syscfg_get wifi_bridge::radio`
CHANNEL=`syscfg_get wifi_sta_channel`
SECURITY=`syscfg get wifi_bridge::security_mode`
PASSPHRASE=`syscfg get wifi_bridge::passphrase`
if [ "2.4GHz" = "$RADIO" ]; then
	OPMODE=23
	STAMODE=7
	PHY_IF_MAC=`syscfg_get wl0_mac_addr | tr -d :`
	STA_MAC=`syscfg_get wl0_sta_mac_addr | tr -d :`
	PHY_IF=`syscfg get wl0_physical_ifname`
elif [ "5GHz" = "$RADIO" ]; then
	OPMODE=28
	STAMODE=8
	PHY_IF_MAC=`syscfg_get wl1_mac_addr | tr -d :`
	STA_MAC=`syscfg_get wl1_sta_mac_addr | tr -d :`
	PHY_IF=`syscfg get wl1_physical_ifname`
else
	echo "wifi_sta_setup: incorrect radio specified"
fi
STA_IF=$PHY_IF"sta0"
syscfg_set wifi_sta_phy_if $PHY_IF
syscfg_set wifi_sta_vir_if $STA_IF
syscfg_commit
wifi_sta_connect()
{
	ifconfig "$PHY_IF" down
	ifconfig "$STA_IF" down
	iwpriv "$PHY_IF" bssid "$PHY_IF_MAC"
	iwpriv "$PHY_IF" opmode "$OPMODE"
	iwpriv "$PHY_IF" wmm 1
	iwpriv "$PHY_IF" htbw 0
	iwpriv "$PHY_IF" autochannel 1
	set_driver_txantenna "$PHY_IF"
	if [ -n "$CHANNEL" ] && [ "0" != "$CHANNEL" ]; then
		iwconfig "$PHY_IF" channel "$CHANNEL" 
	fi
	ifconfig "$PHY_IF" up
	iwpriv "$STA_IF" macclone "0 $STA_MAC"
	iwpriv "$STA_IF" stamode "$STAMODE"
	iwpriv "$STA_IF" ampdutx 1
	iwpriv "$STA_IF" amsdu 1
	iwconfig "$STA_IF" essid "$SSID"
	wifi_sta_set_security "$STA_IF" $SECURITY "$PASSPHRASE"
	echo "${SERVICE_NAME}, attempting to connect $STA_IF to $SSID"
	ifconfig "$STA_IF" up
}
wifi_sta_post_connect()
{
	echo "${SERVICE_NAME}, post_connect()"
	COUNTER=0
	LINK_STATUS=0
	while [ $COUNTER -lt 30 ] && [ "0" = $LINK_STATUS ]
	do
		sleep 10
		if [ "1" = "`iwpriv $STA_IF getlinkstatus | cut -d: -f2 | awk '{ print $1 }'`" ]; then
			LINK_STATUS=1
			brctl addif "$BRIDGE_NAME" "$STA_IF"
			sysevent set wifi_sta_up 1
			echo "${SERVICE_NAME}, post_connect(), $STA_IF connected to $SSID successfully"
			return 0
		fi
		COUNTER=`expr $COUNTER + 1`
		echo "${SERVICE_NAME}, attempting to connect $STA_IF to $SSID"
	done
	sysevent set wifi_sta_up 0
	echo "${SERVICE_NAME}, post_connect(), $STA_IF unable to connect to $SSID"
	return 1
}
if [ "1" = "$BRIDGE_MODE" ] || [ "2" = "$BRIDGE_MODE" ]; then
	WIFI_STA_ENABLED=`syscfg_get wifi_bridge::mode`
	if [ "1" = "$WIFI_STA_ENABLED" ]; then
		STA_UP=`sysevent get wifi_sta_up`
		if [ "1" != "$STA_UP" ]; then
			wifi_sta_connect
			wifi_sta_post_connect
		fi
	fi
fi
exit
