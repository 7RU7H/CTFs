#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wifi/wifi_utils.sh
SERVICE_NAME="wifi_monitor"
WIFI_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
for PHY_IF in $PHYSICAL_IF_LIST; do
	VENDOR_NAME=`syscfg get hardware_vendor_name`
	WL_SYSCFG=`get_syscfg_interface_name $PHY_IF`
	case "$VENDOR_NAME" in
		Broadcom)
			DRIVER_STATUS=`wl -i $PHY_IF isup`
			;;
		Marvell)
			VIR_IF=`syscfg get "$WL_SYSCFG"_user_vap`
			IF_STATE=`ifconfig $VIR_IF | grep UP | awk '{print $1}'`
			if [ "$IF_STATE" = "UP" ]; then
				DRIVER_STATUS=1
			else
				DRIVER_STATUS=0
			fi
			;;
		*)
			echo "wifi, error: unknow hardware vendor name"
			return 1
			;;
	esac
	if [ "`sysevent get ${WL_SYSCFG}_status`" = "up" ] && [ "$DRIVER_STATUS" = "0" ]; then
		ulog ${SERVICE_NAME} status "${SERVICE_NAME}, ERROR: why $PHY_IF is currently down... wifi monitor brings it back up (`date`)"
		echo "${SERVICE_NAME}, ERROR: why $PHY_IF is currently down... wifi monitor brings it back up (`date`)"
		sysevent set wifi-restart
		break
	fi	
done
return 0
