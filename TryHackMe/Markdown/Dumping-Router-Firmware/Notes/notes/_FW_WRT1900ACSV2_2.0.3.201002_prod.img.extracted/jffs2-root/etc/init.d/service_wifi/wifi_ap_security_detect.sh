#!/bin/sh
source /etc/init.d/service_wifi/wifi_sta_utils.sh
RADIO=""
SSID=""
INPUT_CHECK=0
STATUS=""
IF=""
print_help()
{
	echo "Usage: command -r [5GHz | 2.4GHz] -s <SSID>"
	echo "Return failed if SSID not exist, otherwise the security type"
	exit
}
if [ $# -lt 4 ]; then
	print_help
fi
while [ $# -gt 0 ]; do
	case "$1" in
		"-r")
			RADIO=$2
			if [ "5ghz" != "`echo $RADIO | tr [:upper:] [:lower:]`" ] && [ "2.4ghz" != "`echo $RADIO | tr [:upper:] [:lower:]`" ]; then
				INPUT_CHECK=0
				break
			else
				INPUT_CHECK=1
			fi
			;;
		"-s")
			SSID=$2
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
STATUS=`akm_type_detect "$IF" $SSID`
echo $STATUS
