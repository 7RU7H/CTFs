#!/bin/sh
source /etc/init.d/service_wifi/wifi_physical.sh
source /etc/init.d/service_wifi/wifi_platform_specific_setting.sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wifi_repeater"
WIFI_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
VIR_IF=`syscfg_get wifi_sta_vir_if`	#wdev0sta0 or wdev1sta0
PHY_IF=`syscfg_get wifi_sta_phy_if`	#wdev0 or wdev1
HOSTNAME=`hostname`
BRIDGE_NAME=`syscfg_get lan_ifname`
STA_SSID=`syscfg_get wifi_bridge::ssid`
STA_RADIO=`syscfg_get wifi_bridge::radio`	#2.4GHz or 5GHz
STA_SECURITY=`syscfg get wifi_bridge::security_mode`
STA_PASSPHRASE=`syscfg get wifi_bridge::passphrase`
OPMODE=""
STAMODE=""
wifi_repeater_prepare()
{
	echo "${SERVICE_NAME}, prepare()"
	if [ "2.4GHz" = $STA_RADIO ]; then
		PHY_IF=`syscfg get wl0_physical_ifname`
		OPMODE=23
		STAMODE=7
	elif [ "5GHz" = $STA_RADIO ]; then
		PHY_IF=`syscfg get wl1_physical_ifname`
		OPMODE=28
		STAMODE=8
	fi
	VIR_IF=$PHY_IF"sta0"
	syscfg_set wifi_sta_phy_if $PHY_IF
	syscfg_set wifi_sta_vir_if $VIR_IF
	syscfg_commit
	ifconfig $PHY_IF down
	ifconfig $VIR_IF down
	return 0
}
wifi_repeater_init()
{
	echo "${SERVICE_NAME}, init()"
	iwpriv $PHY_IF opmode $OPMODE
	iwpriv $PHY_IF wmm 1
	iwpriv $PHY_IF htbw 0
	iwpriv $PHY_IF autochannel 1
	physical_setting $PHY_IF
	ifconfig $PHY_IF up
	iwpriv $VIR_IF ampdutx 1
	iwpriv $VIR_IF amsdu 1
	iwpriv $VIR_IF stamode $STAMODE
	iwconfig $VIR_IF essid "$STA_SSID"
	wifi_sta_set_security $VIR_IF $STA_SECURITY "$STA_PASSPHRASE"
}
wifi_repeater_connect()
{
	echo "${SERVICE_NAME}, connect()"
	ifconfig $VIR_IF up
}
wifi_repeater_post_connect()
{
	echo "${SERVICE_NAME}, post_connect()"
	COUNTER=0
	LINK_STATUS=0
	while [ $COUNTER -lt 30 ] && [ "0" = $LINK_STATUS ]
	do
		sleep 10
		if [ "1" = "`iwpriv $VIR_IF getlinkstatus | cut -d: -f2 | awk '{ print $1 }'`" ]; then
			LINK_STATUS=1
			brctl addif "$BRIDGE_NAME" "$VIR_IF"
			sysevent set wifi_sta_up 1
			echo "${SERVICE_NAME}, post_connect(), $VIR_IF connected to $STA_SSID successfully"
			return 0
		fi
		COUNTER=`expr $COUNTER + 1`
		echo "${SERVICE_NAME}, attempting to connect $VIR_IF to $STA_SSID"
	done
	sysevent set wifi_sta_up 0
	echo "${SERVICE_NAME}, post_connect(), $VIR_IF unable to connect to $STA_SSID"
	return 1
}
BRIDGE_MODE=`syscfg get bridge_mode`
if [ "1" = "$BRIDGE_MODE" ] || [ "2" = "$BRIDGE_MODE" ]; then
	WIFI_REPEATER_MODE=`syscfg get wifi_bridge::mode`
	if [ "1" = "$WIFI_REPEATER_MODE" ] || [ "2" = "$WIFI_REPEATER_MODE" ]; then
		REPEATER_UP=`sysevent get wifi_sta_up`
		if [ "1" != "$REPEATER_UP" ]; then
			wifi_repeater_prepare
			wifi_repeater_init
			wifi_repeater_connect
			wifi_repeater_post_connect
		fi
	fi
fi
exit
