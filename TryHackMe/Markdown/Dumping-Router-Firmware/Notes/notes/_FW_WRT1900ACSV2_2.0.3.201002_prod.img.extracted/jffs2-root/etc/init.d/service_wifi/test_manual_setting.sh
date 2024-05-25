#!/bin/sh
WL0_SSID=$1
WL0_NETWORK_MODE=$2
WL1_SSID=$3
WL1_NETWORK_MODE=$4
RET_CODE="0"
echo ""
echo "You have entered $WL0_SSID, $WL0_NETWORK_MODE, $WL1_SSID, $WL1_NETWORK_MODE"
if [ -z "$WL0_SSID" ] && [ -z "$WL0_NETWORK_MODE" ] && [ -z "$WL1_SSID" ] && [ -z "$WL1_NETWORK_MODE" ]; then
	echo "Usage: test_manual_setting.sh 24g_ssid 24g_network_mode 5g_ssid 5g_network_mode"
	exit
fi
if [ ! -z "$WL0_SSID" ]; then
	syscfg set wl0_ssid $WL0_SSID
fi
if [ ! -z "$WL0_NETWORK_MODE" ]; then
	case "$WL0_NETWORK_MODE" in
		"11b"|"11g"|"11n"|"11b 11g"|"11b 11g 11n"|"11b 11g 11n 11ac")
			syscfg set wl0_network_mode "$WL0_NETWORK_MODE"
			;;
		*)
			echo ""
			echo "=== ERROR: invalid network mode for 2.4ghz ==="
			echo "=== Avalaible network modes are: <11b>, <11g>, <11n> , <11b 11g>, <11b 11g 11n> or <11b 11g 11n 11ac>"
			RET_CODE="1"
			;;
	esac
fi
if [ ! -z "$WL1_SSID" ]; then
	syscfg set wl1_ssid $WL1_SSID
fi
if [ ! -z "$WL1_NETWORK_MODE" ]; then
	case "$WL1_NETWORK_MODE" in
		"11a"|"11n"|"11a 11n"|"11a 11n 11ac")
			syscfg set wl1_network_mode "$WL1_NETWORK_MODE"
			;;
		*)
			echo ""
			echo "=== ERROR: invalid network mode for 5ghz ==="
			echo "Avalaible valid network modes are: <11a>, <11n>, <11a 11n> or <11a 11n 11ac>"
			RET_CODE="1"
			;;
	esac
fi
if [ "$RET_CODE" = "0" ]; then
	echo ""
	echo "=== Please wait while wifi restart ==="
	sleep 3
	sysevent set wifi_config_changed
fi
