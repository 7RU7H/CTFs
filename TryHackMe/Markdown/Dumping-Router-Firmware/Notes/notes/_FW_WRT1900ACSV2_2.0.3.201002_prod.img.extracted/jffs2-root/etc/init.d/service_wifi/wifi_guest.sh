#!/bin/sh
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
source /etc/init.d/service_wifi/wifi_utils.sh
wifi_guest_start ()
{
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	wait_till_end_state "$WIFI_GUEST"_"$PHY_IF"
	ulog wlan status "${SERVICE_NAME}, wifi_guest_start($PHY_IF)"
	echo "${SERVICE_NAME}, wifi_guest_start($PHY_IF)"
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	GUEST_VAP=`syscfg_get "$SYSCFG_INDEX"_guest_vap`
	REPEATER_DISABLED=`syscfg_get repeater_disabled`
	if [ -n "$REPEATER_DISABLED" ] && [ "1" = "$REPEATER_DISABLED" ]; then
		ulog guest status "${SERVICE_NAME}, Repeater disabled. service not start"
		sysevent set "$WIFI_GUEST"_"$PHY_IF"-errinfo "Repeater disabled. service not started"
		sysevent set "$WIFI_GUEST"_"$PHY_IF"-status stopped
		return 1
	fi
	BRIDGE_MODE=`syscfg_get bridge_mode`
	if [ "$BRIDGE_MODE" != "0" ]; then
		echo "${SERVICE_NAME}, Do not start guest network in bridge mode"
		return 1
	fi
	EXTENDER_RADIO_MODE=`syscfg_get extender_radio_mode`
	if [ ! -z "$EXTENDER_RADIO_MODE" -a "1" = "$EXTENDER_RADIO_MODE" ] ; then
		echo "${SERVICE_NAME}, guest is not supported on 5GHz wifi Extender" > /dev/console
		return 1
	fi
	GUEST_ENABLED=`syscfg_get guest_enabled`
	WLX_GUEST_ENABLED=`syscfg_get "$SYSCFG_INDEX"_guest_enabled`
	if [ "$GUEST_ENABLED" = "0" ] || [ "$WLX_GUEST_ENABLED" = "0" ]; then
		echo "${SERVICE_NAME}, guest $GUEST_VAP is disabled, do not start wifi guest"
		ulog wlan status "${SERVICE_NAME}, guest $GUEST_VAP is disabled, do not start wifi guest"
		return 1
	fi
	USER_STATE=`syscfg_get "$SYSCFG_INDEX"_state`
	if  [ "$USER_STATE" = "down" ]; then
		ulog wlan status "${SERVICE_NAME}, user vap is down, do not start guest $GUEST_VAP"
		return 1
	fi
	STATUS=`sysevent get "$WIFI_GUEST"_"$PHY_IF"-status`
	if [ "started" = "$STATUS" ] || [ "starting" = "$STATUS" ] ; then
		echo "${SERVICE_NAME}, "$WIFI_GUEST"_"$PHY_IF" is already starting/started, ignore the request"
		ulog wlan status "${SERVICE_NAME}, "$WIFI_GUEST"_"$PHY_IF" is already starting/started, ignore the request"
		return 1
	fi
	sysevent set "$WIFI_GUEST"_"$PHY_IF"-status starting
	guest_start $PHY_IF
	Is_GUEST_VAP_STATE=`ifconfig $GUEST_VAP | grep "UP" | awk {'print $1'}`
	if [ "$Is_GUEST_VAP_STATE" != "UP" ] ; then
          ifconfig $GUEST_VAP up
        else                                                                                                 
          iwconfig $GUEST_VAP commit
        fi
	sysevent set ${SYSCFG_INDEX}_guest_status "up"
	ulog ${SERVICE_NAME} status "${SERVICE_NAME}, guest AP: $GUEST_VAP is up"
	echo "${SERVICE_NAME}, guest AP: $GUEST_VAP is up " > /dev/console
	
	sysevent set "$WIFI_GUEST"_"$PHY_IF"-status started
	return 0
}
wifi_guest_stop ()
{
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	wait_till_end_state "$WIFI_GUEST"_"$PHY_IF"
	ulog wlan status "${SERVICE_NAME}, wifi_guest_stop($PHY_IF)"
	echo "${SERVICE_NAME}, wifi_guest_stop($PHY_IF)"
	STATUS=`sysevent get "$WIFI_GUEST"_"$PHY_IF"-status`
	if [ "stopped" = "$STATUS" ] || [ "stopping" = "$STATUS" ] || [ -z "$STATUS" ]; then
		echo "${SERVICE_NAME}, "$WIFI_GUEST"_"$PHY_IF" is already stopping/stopped, ignore this request"
		ulog wlan status "${SERVICE_NAME}, "$WIFI_GUEST"_"$PHY_IF" is already stopping/stopped, ignore this request"
		return 1
	fi
	sysevent set "$WIFI_GUEST"_"$PHY_IF"-status stopping
	guest_stop $PHY_IF
	sysevent set "$WIFI_GUEST"_"$PHY_IF"-status stopped
	return 0
}
wifi_guest_restart()
{
	PHY_IF=$1
	echo "${SERVICE_NAME}, wifi_guest_restart($PHY_IF)"
	wifi_guest_stop $PHY_IF
	wifi_guest_start $PHY_IF
	return 0
}
guest_start()
{
	PHY_IF=$1
	ulog guest status "${SERVICE_NAME}, guest_start($PHY_IF) "
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	GUEST_VAP=`syscfg_get "$SYSCFG_INDEX"_guest_vap`
	GUEST_BRIDGE=`syscfg_get guest_lan_ifname`
	add_interface_to_bridge $GUEST_VAP $GUEST_BRIDGE
	set_driver_mac_filter_enabled $GUEST_VAP
	configure_guest $PHY_IF
	add_guest_vlan_to_backhaul
	return 0
}
guest_stop()
{
	PHY_IF=$1
	ulog guest status "${SERVICE_NAME}, guest_stop($PHY_IF) "
	echo "${SERVICE_NAME}, guest_stop($PHY_IF) "
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	GUEST_VAP=`syscfg_get "$SYSCFG_INDEX"_guest_vap`
	GUEST_BRIDGE=`syscfg_get guest_lan_ifname`
	delete_guest_vlan_from_backhaul
	delete_interface_from_bridge $GUEST_VAP $GUEST_BRIDGE
	set_driver_mac_filter_disabled $GUEST_VAP
	ifconfig $GUEST_VAP down
	sysevent set ${SYSCFG_INDEX}_guest_status "down"
	ulog ${SERVICE_NAME} status "${SERVICE_NAME}, guest AP: $GUEST_VAP is down"
	echo "${SERVICE_NAME}, guest AP: $GUEST_VAP is down " > /dev/console
	return 0
}
set_guest_ssid()
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	USER_SSID=`syscfg_get "$SYSCFG_INDEX"_ssid`
	GUEST_VAP=`syscfg_get "$SYSCFG_INDEX"_guest_vap`
	MAX_SSID_LEN=32
	GUEST_SSID_SUFFIX=`syscfg_get guest_ssid_suffix`
	MAX_GUEST_SSID_PREFIX=`expr $MAX_SSID_LEN - ${#GUEST_SSID_SUFFIX}`
	if [ "$SYSCFG_INDEX" = "wl0" ]; then
		GUEST_SSID=`syscfg_get guest_ssid`
	else
		GUEST_SSID=`syscfg_get "$SYSCFG_INDEX"_guest_ssid`
	fi
	if [ -n "$USER_SSID"  ]; then
		GUEST_SSID="${USER_SSID:0:$MAX_GUEST_SSID_PREFIX}$GUEST_SSID_SUFFIX"
		ulog guest status "Guest SSID = $GUEST_SSID"
		ulog guest status "Syscfg Guest SSID = $GUEST_SSID"
		if [ "$GUEST_SSID" != "$GUEST_SSID" ]; then
			utctx_cmd set guest_ssid="$GUEST_SSID"
			return 1;
		fi
	fi
	return 0;
}
configure_guest()
{
	PHY_IF=$1
	ulog guest status "${SERVICE_NAME}, configure_guest($PHY_IF) "
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	GUEST_VAP=`syscfg_get "$SYSCFG_INDEX"_guest_vap`
	if [ "$SYSCFG_INDEX" = "wl0" ]; then
		GUEST_SSID=`syscfg_get guest_ssid`
		GUEST_BROADCAST=`syscfg_get guest_ssid_broadcast`
	else
		GUEST_SSID=`syscfg_get "$SYSCFG_INDEX"_guest_ssid`
		GUEST_BROADCAST=`syscfg_get "$SYSCFG_INDEX"_guest_ssid_broadcast`
	fi
	
	if [ "$GUEST_BROADCAST" = "0" ] ; then
		HIDE_SSID=1
	else
		HIDE_SSID=0 
	fi
	iwconfig $GUEST_VAP essid "$GUEST_SSID"
	iwpriv $GUEST_VAP hidessid $HIDE_SSID
	iwpriv $GUEST_VAP ampdutx 1
	iwpriv $GUEST_VAP amsdu 1
	iwpriv $GUEST_VAP mcastproxy 1
	OPMODE=`get_driver_network_mode "$PHY_IF"`
	iwpriv $GUEST_VAP opmode $OPMODE
	return 0
}
