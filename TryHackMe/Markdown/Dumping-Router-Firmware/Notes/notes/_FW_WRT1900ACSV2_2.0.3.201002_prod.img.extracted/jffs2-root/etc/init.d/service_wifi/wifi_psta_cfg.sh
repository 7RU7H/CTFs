#!/bin/sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
RADIO=""
SSID=""
PASSPHRASE=""
INPUT_CHECK=0
STATUS=""
IF=""
SECURITY_TYPE=""
print_help()
{
	echo "Usage: command -r [5GHz | 2.4GHz] -s <SSID> -p <Passphrase>"
	echo "Return failed if SSID not exist, otherwise the security type"
	exit
}
if [ $# -lt 4 ]; then
	print_help
fi
while [ $# -gt 0 ]; do
	case "$1" in
		"-r")
			echo "Radio is" $2
			RADIO=$2
			INPUT_CHECK=1
			;;
		"-s")
			echo "SSID is" $2
			SSID=$2
			INPUT_CHECK=1
			;;
		"-p")
			echo "Passphrase is" $2
			PASSPHRASE=$2
			INPUT_CHECK=1
			;;
		*)
			INPUT_CHECK=0
			break
			;;
	esac
	shift	
	shift	
done
if [ "1" != "$INPUT_CHECK" ] || [ -z "$RADIO" ] || [ -z "$SSID" ]; then
	STATUS="failed"
	echo $STATUS
	print_help
fi
IF=`radio_to_mrvl_physical_ifname $RADIO`
if [ -z "$IF" ]; then
	STATUS="failed"
	echo $STATUS
	print_help
fi
if [ "$VENDOR_2G_DEFINED_PHY_IFNAME" != "$IF" ] && [ "$VENDOR_5G_DEFINED_PHY_IFNAME" != "$IF" ]; then
	STATUS="failed"
	echo $STATUS
	print_help
fi
SECURITY_TYPE=`syscfg get wifi_bridge::security_mode`
if [ -z "$SECURITY_TYPE" ]; then
	SECURITY_TYPE=`akm_type_detect "$IF" "$SSID"`
fi
if [ "wpa-personal" = "$SECURITY_TYPE" ] || [ "wpa2-personal" = "$SECURITY_TYPE" ] || [ "wpa-mixed" = "$SECURITY_TYPE" ]; then
	if [ -z "$PASSPHRASE" ]; then
		STATUS="failed"
		echo $STATUS
		print_help
	fi
fi
if [ "1" = "`syscfg get wifi_bridge::mode`" ] ; then
	sysevent set PSTA_REBOOT 0
else
	sysevent set PSTA_REBOOT 1
fi
CURR_BRIDGE_MODE=`syscfg get bridge_mode`
if [ -f /tmp/ap_channel.txt ]; then
	CHANNEL=`cat /tmp/ap_channel.txt`
else
	CHANNEL=0
fi
if [ -z "$CURR_BRIDGE_MODE" ] || [ "0" = "$CURR_BRIDGE_MODE" ] ; then
	syscfg set bridge_mode 1 #Use default DHCP 
fi
syscfg set wifi_bridge::mode 1
syscfg set wifi_bridge::radio $RADIO
syscfg set wifi_bridge::ssid $SSID
syscfg set wifi_sta_vir_if "$IF"sta0
if [ -n "$CHANNEL" ]; then
	syscfg set wifi_sta_channel "$CHANNEL"
fi	
sysevent set physical-one-time-setting FALSE
sysevent set system_state-heartbeat
if [ "wpa-personal" = "$SECURITY_TYPE" ] || [ "wpa2-personal" = "$SECURITY_TYPE" ] || [ "wpa-mixed" = "$SECURITY_TYPE" ]; then
	syscfg set wifi_bridge::security_mode $SECURITY_TYPE
	syscfg set wifi_bridge::passphrase $PASSPHRASE
else
	syscfg set wifi_bridge::security_mode open
fi
syscfg commit
if [ "1" = "`sysevent get PSTA_REBOOT`" ] ; then
	reboot
else
	sysevent set bridge-restart
fi
echo $STATUS
