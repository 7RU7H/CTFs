#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_wifi/wifi_physical.sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
source /etc/init.d/syscfg_api.sh
COMMAND=$1
print_help()
{
	echo "Usage: wifi_bridge_api.sh is_connected"
	echo "       			get_conn_ssid"
	echo "       			get_conn_bssid"
	echo "       			get_conn_radio"
	echo "       			get_conn_network_mode"
	echo "       			get_conn_channel_width"
	echo "       			get_conn_channel"
	echo "       			get_conn_signal_strength"
	echo "       			get_wireless_networks <2.4GHz|5GHz>"
	echo "       			check_connection <ssid> <security> <radio> <passphrase>"
	exit
}
is_sta_connected()
{
	STA_VIR_IF=`syscfg_get wifi_sta_vir_if`
	if [ -z "$STA_VIR_IF" ]; then
		echo "error: no STA interface specified"
		exit
	fi
	RESULT=`iwpriv $STA_VIR_IF getlinkstatus | awk -F':' '{print $2}' | awk '{print $1}'`
	if [ "1" = "$RESULT" ]; then
		echo "yes"
	else
		echo "no"
	fi
	exit
}
get_ssid()
{
	syscfg_get wifi_bridge::ssid
}
get_bssid()
{
	echo "xx:xx:xx:xx:xx:xx"
}
get_radio()
{
	syscfg_get wifi_bridge::radio
}
get_network_mode()
{
	STA_VIR_IF=`syscfg_get wifi_sta_vir_if`
	OPMODE=`iwpriv $STA_VIR_IF getopmode | awk -F':' '{print $2}'`
	case "`echo $OPMODE`" in
		"1")
		echo "11b"
		;;
		"2")
		echo "11g"
		;;
		"3")
		echo "11b 11g"
		;;
		"4")
		echo "11n"
		;;
		"6")
		echo "11g 11n"
		;;
		"7")
		echo "11b 11g 11n"
		;;
		"8")
		echo "11a"
		;;
		"12")
		echo "11a 11n"
		;;
		"13")
		echo "11n"
		;;
		"23")
		echo "11b 11g 11n 11ac"
		;;
		"28")
		echo "11a 11n 11ac"
		;;
		*)
		echo "error: unknown network mode"
	esac
}
get_channel_width()
{
	STA_VIR_IF=`syscfg_get wifi_sta_vir_if`
	HTBW=`iwpriv $STA_VIR_IF gethtbw | awk -F':' '{print $2}'`
	case "`echo $HTBW`" in
		"0")
		echo "auto"
		;;
		"2")
		echo "standard"
		;;
		"3")
		echo "wide"
		;;
		*)
		echo "error: unknown channel width"
	esac
	
}
get_channel()
{
	STA_VIR_IF=`syscfg_get wifi_sta_vir_if`
	iwconfig $STA_VIR_IF | grep "Channel:" | awk -F':' '{print $2}' | awk '{print $1}'
}
get_connection_signal_strength()
{
	STA_VIR_IF=`syscfg_get wifi_sta_vir_if`
	if [ -z "$STA_VIR_IF" ]; then
		echo "error: no STA interface specified"
		exit
	fi
	iwpriv $STA_VIR_IF getrssi | awk -F':' '{print $2}'
}
get_site_survey()
{
	RADIO=$1
	IF=""
	WLINDEX=""
	case "`echo $RADIO | tr [:upper:] [:lower:]`" in
		"2.4ghz")
		IF=`syscfg get wl0_physical_ifname`
		STA_MODE=7
		WLINDEX="wl0"
		;;
		"5ghz")
		IF=`syscfg get wl1_physical_ifname`
		STA_MODE=8
		WLINDEX="wl1"
		;;
		*)
		echo "Usage: wifi_bridge_api.sh get_wireless_networks <2.4GHz|5GHz>"
		exit
	esac
	STA_IF="$IF"sta0
	ifconfig $IF up
	iwpriv "$STA_IF" stamode "$STA_MODE"
	sleep 1
	iwconfig "$STA_IF" commit
	iwpriv "$STA_IF" stascan 1
	sleep 3
	iwpriv "$STA_IF" getstascan | grep "SSID=" | cut -d' ' -f 2- > /tmp/site_survey
	cat /tmp/site_survey | cut -c6-38 | sed -e "s/ \{1,\}$//" > /tmp/ss_ssids
	if [ "2.4GHz" = "$RADIO" ]; then
		cat /tmp/site_survey | cut -c39- | tr ' ' ';' | cut -d';' -f1,3,5 | awk '{print $0 ";2.4GHz"}' > /tmp/ss_data
	elif [ "5GHz" = "$RADIO" ]; then
		cat /tmp/site_survey | cut -c39- | tr ' ' ';' | cut -d';' -f1,3,5 | awk '{print $0 ";5GHz"}' > /tmp/ss_data
	fi
	sed -e s/";None;"/";disabled;"/ -e s/";WPA;"/";wpa-personal;"/ -e s/";WPA2;"/";wpa2-personal;"/ -e s/";WPA-WPA2;"/";wpa-mixed;"/ -i /tmp/ss_data
	awk '{getline var < "/tmp/ss_data"; print $0";" var}' /tmp/ss_ssids
	rm -f /tmp/ss_ssids
	rm -f /tmp/ss_data
	if [ "down" = `syscfg_get $WLINDEX"_state"` ]; then
		ifconfig $IF down
	fi
	exit
}
check_sta_connection()
{
	ulog wlan status "${SERVICE_NAME}, check_connection()"
	STA_SSID="$1"
	STA_SECURITY="$2"
	STA_RADIO="$3"	#2.4GHz or 5GHz
	STA_PASSPHRASE="$4"
	echo "${SERVICE_NAME}, check_connection(), this will disrupt the user and guest VAPs on $STA_RADIO"
	VIR_IF=`syscfg_get wifi_sta_vir_if`	#wdev0sta0 or wdev1sta0
	PHY_IF=`syscfg_get wifi_sta_phy_if`	#wdev0 or wdev1
	OPMODE=""
	STAMODE=""
	WLINDEX=""
	if [ "2.4GHz" = $STA_RADIO ]; then
		PHY_IF=`syscfg get wl0_physical_ifname`
		OPMODE=23
		STAMODE=7
		WLINDEX="wl0"
	elif [ "5GHz" = $STA_RADIO ]; then
		PHY_IF=`syscfg get wl1_physical_ifname`
		OPMODE=28
		STAMODE=8
		WLINDEX="wl1"
	fi
	VIR_IF=$PHY_IF"sta0"
	sysevent set wifi_bridge_conn_status "connecting"
	ifconfig $VIR_IF down
	ifconfig $PHY_IF"ap1" down
	ifconfig $PHY_IF"ap0" down
	ifconfig $PHY_IF down
	iwpriv $PHY_IF opmode $OPMODE
	iwpriv $PHY_IF wmm 1
	iwpriv $PHY_IF htbw 0
	iwpriv $PHY_IF autochannel 1
	ifconfig $PHY_IF up
	iwpriv $VIR_IF ampdutx 1
	iwpriv $VIR_IF amsdu 1
	iwpriv $VIR_IF stamode $STAMODE
	iwconfig $VIR_IF essid "$STA_SSID"
	wifi_sta_set_security $VIR_IF $STA_SECURITY "$STA_PASSPHRASE"
	ifconfig $VIR_IF up
	echo "${SERVICE_NAME}, check_connection, $VIR_IF attempting to connect to $STA_SSID..."
	COUNTER=0
	LINK_STATUS=0
	while [ $COUNTER -lt 3 ] && [ "0" = $LINK_STATUS ]
	do
		sleep 4
		if [ "1" = "`iwpriv $VIR_IF getlinkstatus | cut -d: -f2 | awk '{ print $1 }'`" ]; then
			LINK_STATUS=1
			echo "${SERVICE_NAME}, check_connection, $VIR_IF test connection to $STA_SSID was SUCCESSFUL"
			ifconfig $VIR_IF down
			sysevent set wifi_bridge_conn_status "success"
			restore_wifi_settings $PHY_IF $WLINDEX $STA_RADIO
			return 0
		fi
		COUNTER=`expr $COUNTER + 1`
	done
	echo "${SERVICE_NAME}, check_connection, $VIR_IF test connection to $STA_SSID was UNSUCCESSFUL"
	ifconfig $VIR_IF down
	sysevent set wifi_bridge_conn_status "failed"
	restore_wifi_settings $PHY_IF $WLINDEX $STA_RADIO
}
restore_wifi_settings()
{
	PHY_IF=$1
	WLINDEX=$2
	RADIO=$3
	echo "${SERVICE_NAME}, restoring user defined settings on $RADIO"
	if [ "down" = `syscfg_get $WLINDEX"_state"` ]; then
		ifconfig $PHY_IF down
		return 0
	fi
	set_driver_htbw $PHY_IF
	set_driver_extsubch $PHY_IF
	set_driver_channel $PHY_IF
	set_driver_wmm $PHY_IF
	set_driver_opmode $PHY_IF
	sleep 1
	iwconfig $PHY_IF commit
	echo "${SERVICE_NAME}, restore user vap"
	ifconfig $PHY_IF"ap0" up
	if [ "1" = `syscfg_get guest_enabled` ] && [ "1" = `syscfg_get $WLINDEX"_guest_enabled"` ]; then
		echo "${SERVICE_NAME}, restore guest vap"
		GUEST=`syscfg_get $WLINDEX"_guest_vap"`
		ifconfig $GUEST up
	fi
}
case "`echo $COMMAND`" in
	"is_connected")
	is_sta_connected
	;;
	"get_conn_ssid")
	get_ssid
	;;
	"get_conn_bssid")
	get_bssid
	;;
	"get_conn_radio")
	get_radio
	;;
	"get_conn_network_mode")
	get_network_mode
	;;
	"get_conn_channel_width")
	get_channel_width
	;;
	"get_conn_channel")
	get_channel
	;;
	"get_conn_signal_strength")
	get_connection_signal_strength
	;;
	"get_wireless_networks")
	get_site_survey "$2"
	;;
	"check_connection")
	check_sta_connection "$2" "$3" "$4" "$5"
	;;
	*)
	print_help
esac
