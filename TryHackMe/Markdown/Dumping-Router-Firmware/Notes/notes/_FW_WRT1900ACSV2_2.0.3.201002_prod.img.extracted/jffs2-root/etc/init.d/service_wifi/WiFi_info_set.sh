#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/syscfg_api.sh
US_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11"
US_CH_LIST_5G="36,40,44,48,149,153,157,161,165"
EU_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11,12,13"
EU_CH_LIST_5G="36,40,44,48"
CA_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11"
CA_CH_LIST_5G="36,40,44,48,149,153,157,161,165"
PH_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11"
PH_CH_LIST_5G="36,40,44,48,149,153,157,161,165"
AP_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11,12,13"
AP_CH_LIST_5G="36,40,44,48,149,153,157,161,165"
AU_CH_LIST_2G="1,2,3,4,5,6,7,8,9,10,11,12,13"
AU_CH_LIST_5G="36,40,44,48,149,153,157,161,165"
NEED_RESTORE=FALSE
US_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
US_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11"
US_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11"
US_5G_CH_WIDTHS="0,20,40,80"
US_5G_CH_0="0,36,40,44,48,149,153,157,161,165"
US_5G_CH_20="0,36,40,44,48,149,153,157,161,165"
US_5G_CH_40="0,36,40,44,48,149,153,157,161"
US_5G_CH_80="0,36,40,44,48,149,153,157,161"
EU_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
EU_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
EU_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
EU_5G_CH_WIDTHS="0,20,40,80"
EU_5G_CH_0="0,36,40,44,48"
EU_5G_CH_20="0,36,40,44,48"
EU_5G_CH_40="0,36,40,44,48"
EU_5G_CH_80="0,36,40,44,48"
CA_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
CA_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11"
CA_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11"
CA_5G_CH_WIDTHS="0,20,40,80"
CA_5G_CH_0="0,36,40,44,48,149,153,157,161,165"
CA_5G_CH_20="0,36,40,44,48,149,153,157,161,165"
CA_5G_CH_40="0,36,40,44,48,149,153,157,161"
CA_5G_CH_80="0,36,40,44,48,149,153,157,161"
PH_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
PH_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11"
PH_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11"
PH_5G_CH_WIDTHS="0,20,40,80"
PH_5G_CH_0="0,36,40,44,48,149,153,157,161,165"
PH_5G_CH_20="0,36,40,44,48,149,153,157,161,165"
PH_5G_CH_40="0,36,40,44,48,149,153,157,161"
PH_5G_CH_80="0,36,40,44,48,149,153,157,161"
AP_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
AP_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
AP_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
AP_5G_CH_WIDTHS="0,20,40,80"
AP_5G_CH_0="0,36,40,44,48,149,153,157,161,165"
AP_5G_CH_20="0,36,40,44,48,149,153,157,161,165"
AP_5G_CH_40="0,36,40,44,48,149,153,157,161"
AP_5G_CH_80="0,36,40,44,48,149,153,157,161"
AU_2G_CH_WIDTHS="0,20" #40 is valid, but do not show 40 only in UI
AU_2G_CH_0="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
AU_2G_CH_20="0,1,2,3,4,5,6,7,8,9,10,11,12,13"
AU_5G_CH_WIDTHS="0,20,40,80"
AU_5G_CH_0="0,36,40,44,48,149,153,157,161,165"
AU_5G_CH_20="0,36,40,44,48,149,153,157,161,165"
AU_5G_CH_40="0,36,40,44,48,149,153,157,161"
AU_5G_CH_80="0,36,40,44,48,149,153,157,161"
SKU=`skuapi -g model_sku | awk -F"=" '{print $2}' | sed 's/ //g'`
PRODUCT=`echo $SKU | awk -F"-" '{print $1}'`
REGION_CODE=`skuapi -g cert_region | awk -F"=" '{print $2}' | sed 's/ //g'`
P_IF_INDEX_LIST=`syscfg_get lan_wl_physical_ifnames | wc -w`
for i in `seq 1 $P_IF_INDEX_LIST`
do
	WL_INDEX=wl`expr $i - 1`
        CH_LIST=`syscfg_get "$WL_INDEX"_available_channels`
        CH_W=`syscfg_get "$WL_INDEX"_supported_channel_widths`
	if [ -z "$CH_LIST" ] || [ -z "$CH_W" ]; then
		NEED_RESTORE=TRUE
		break
	fi
done
SYSCFG_REGION_CODE=`syscfg_get device::cert_region`
if [ -z "$REGION_CODE" ]; then
	if [ "$SYSCFG_REGION_CODE" != "US" ]; then
		NEED_RESTORE=TRUE
	fi
else
	if [ "$SYSCFG_REGION_CODE" != "$REGION_CODE" ]; then
		NEED_RESTORE=TRUE
	fi
fi
if [ "TRUE" = "$NEED_RESTORE" ]; then
	echo "SKU is $SKU" > /dev/console
	if [ -z "$REGION_CODE" ]; then
		REGION_CODE="US"
	fi
	wl0_available_channels=`syscfg_get wl0_available_channels`
	wl1_available_channels=`syscfg_get wl1_available_channels`
	case "$REGION_CODE" in
		"US")
			syscfg_set device::cert_region "US"
			syscfg_set device::model_base "$PRODUCT"
			
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$US_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$US_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$US_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$US_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$US_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$US_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$US_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$US_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$US_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$US_5G_CH_80"
			syscfg_commit
			;;
		"EU")
			syscfg_set device::cert_region "EU"
			syscfg_set device::model_base "$PRODUCT"
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$EU_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$EU_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$EU_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$EU_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$EU_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$EU_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$EU_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$EU_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$EU_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$EU_5G_CH_80"
			syscfg_commit
			;;
		"CA")
			syscfg_set device::cert_region "CA"
			syscfg_set device::model_base "$PRODUCT"
			
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$CA_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$CA_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$CA_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$CA_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$CA_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$CA_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$CA_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$CA_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$CA_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$CA_5G_CH_80"
			syscfg_commit
			;;
		"PH")
			syscfg_set device::cert_region "PH"
			syscfg_set device::model_base "$PRODUCT"
			
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$PH_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$PH_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$PH_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$PH_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$PH_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$PH_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$PH_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$PH_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$PH_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$PH_5G_CH_80"
			syscfg_commit
			;;
		"AP")
			syscfg_set device::cert_region "AP"
			syscfg_set device::model_base "$PRODUCT"
			
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$AP_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$AP_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$AP_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$AP_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$AP_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$AP_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$AP_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$AP_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$AP_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$AP_5G_CH_80"
			syscfg_commit
			;;
		"AH")
			syscfg_set device::cert_region "AH"
			syscfg_set device::model_base "$PRODUCT"
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$AP_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$AP_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$AH_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$AH_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$AH_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$AH_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$AH_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$AH_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$AH_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$AH_5G_CH_80"
			syscfg_commit
			;;
		"AU")
			syscfg_set device::cert_region "AU"
			syscfg_set device::model_base "$PRODUCT"
			
			if [ -z "$wl0_available_channels" ]; then
				syscfg_set wl0_available_channels "$AU_CH_LIST_2G"
			fi
			if [ -z "$wl1_available_channels" ]; then
				syscfg_set wl1_available_channels "$AU_CH_LIST_5G"
			fi
			syscfg_set wl0_supported_channel_widths "$AU_2G_CH_WIDTHS"
			syscfg_set wl0_available_channels_0 "$AU_2G_CH_0"
			syscfg_set wl0_available_channels_20 "$AU_2G_CH_20"
			syscfg_set wl1_supported_channel_widths "$AU_5G_CH_WIDTHS"
			syscfg_set wl1_available_channels_0 "$AU_5G_CH_0"
			syscfg_set wl1_available_channels_20 "$AU_5G_CH_20"
			syscfg_set wl1_available_channels_40 "$AU_5G_CH_40"
			syscfg_set wl1_available_channels_80 "$AU_5G_CH_80"
			syscfg_commit
			;;
		*)
			ulog wlan status "wifi, Invalid region code, could not set on WiFi" > /dev/console
			;;
	esac
	ulog wlan status "wifi, Channel list and region code is set on syscfg" > /dev/console
else
	ulog wlan status "wifi, Channel list is available. Do nothing" > /dev/console
fi
