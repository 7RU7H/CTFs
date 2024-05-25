#!/bin/sh
source /etc/init.d/syscfg_api.sh
INTFS=`syscfg_get lan_wl_physical_ifnames`
for INTF in $INTFS
do
	WL_INDEX=`echo $INTF | cut -c5`
	TOKEN=`iwconfig $INTF | grep Channel | awk -F"Channel:" '/Channel/ {print $2}'`
	CURRENT_CH=`echo $TOKEN | awk '{print $1}'`
	if [ -z "$CURRENT_CH" ]; then
		CURRENT_CH=0
	else
		if [ "0" = "$WL_INDEX" ]; then
			if [ $CURRENT_CH -lt 1 ] || [ $CURRENT_CH -gt 14 ]; then
				CURRENT_CH=0 
			fi
		else
			if [ $CURRENT_CH -lt 36 ] || [ $CURRENT_CH -gt 165 ]; then
				CURRENT_CH=0 
			fi
		fi
	fi
	sysevent set wl"$WL_INDEX"_channel $CURRENT_CH
done
exit
