#!/bin/sh
source /etc/init.d/syscfg_api.sh
echo "wifi, start_wps.sh"
HOSTAPD_IFNAMES=`ls /var/run/hostapd | xargs echo`
WPS_METHOD=
PIN=
if [ "wps_pin" != $1 ] && [ "wps_pbc" != $1 ];then
	display_help
else
	WPS_METHOD=$1
fi 
if [ "wps_pin" == $WPS_METHOD ]; then
	if [ ! -z $2 ]; then
		PIN_LEN=`expr length "$2"`
		if [ $PIN_LEN = 4 ] || [ $PIN_LEN = 8 ]; then
			PIN="$2"
		else
			display_help
		fi
	else
		display_help
	fi
fi
sysevent set wps_process incomplete
for if_name in $HOSTAPD_IFNAMES
do
	WL_INDEX=`echo $if_name | cut -c5`
	WPS_STATE=`syscfg_get wl"$WL_INDEX"_wps_state`
	if [ "$WPS_STATE" = "disabled" ]; then
		continue
	fi
	if [ "wps_pin" == $WPS_METHOD ]; then
		hostapd_cli -p/var/run/hostapd -i$if_name $WPS_METHOD any "$PIN" 120 > /dev/null
	fi
	if [ "wps_pbc" == $WPS_METHOD ]; then
		hostapd_cli -p/var/run/hostapd -i$if_name $WPS_METHOD > /dev/null
	fi
done
sysevent set wps-running
display()
{
	echo "Usage start wps pin or pbc"
	echo "start_wps wps_pin pin"
	echo "start_wps wps_pbc"
	exit
}
