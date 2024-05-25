#!/bin/sh

source /etc/init.d/syscfg_api.sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------
SERVICE_NAME="mac_setup"
WIFI_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WIFI_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x

# This is a utility used to apply a mac address policy on Mamba
# This will populate all other MAC addesses based on the master MAC address
ETH0_MAC="$1"

echo "mac_setup.sh, setting up MAC addresses for all interfaces based on $ETH0_MAC"
#--------------------------------------------------------------
# displaying the use of the fucntion
#--------------------------------------------------------------
display_usage()
{
	echo "Please check switch mac address" > /dev/console
	exit
}

#--------------------------------------------------------------
# this function applies the mac policy to the device
#--------------------------------------------------------------
processing() 
{
	# LAN MAC
	LAN_MAC=$ETH0_MAC

	# WAN MAC
	WAN_MAC=$ETH0_MAC

	#2.4G MAC address = WAN_MAC + 1
	W24G_MAC=`apply_mac_inc -m "$WAN_MAC" -i 1`

	# 5G MAC address = WAN_MAC + 2
	W5G_MAC=`apply_mac_inc -m "$WAN_MAC" -i 2`

	# 24GHz Guest MAC = WAN_MAC + 3 (first byte is admin byte)
	W24G_GUEST_MAC=`apply_mac_inc -m "$WAN_MAC" -i 3`
	W24G_GUEST_MAC=`apply_mac_adbit -m "$W24G_GUEST_MAC"`

	# 24GHz SimpleTap MAC = WAN_MAC + 4 (first byte is admin byte)
	W24G_TC_MAC=`apply_mac_inc -m "$WAN_MAC" -i 4`
	W24G_TC_MAC=`apply_mac_adbit -m "$W24G_TC_MAC"`

	# 5GHz Guest MAC = WAN_MAC + 3 (first byte is admin byte)
	W5G_GUEST_MAC=`apply_mac_inc -m "$WAN_MAC" -i 5`
	W5G_GUEST_MAC=`apply_mac_adbit -m "$W5G_GUEST_MAC"`

	# 24GHz sta interface 
	W24G_STA_MAC=`apply_mac_inc -m "$WAN_MAC" -i 6`
	W24G_STA_MAC=`apply_mac_adbit -m "$W24G_STA_MAC"`

	# 5GHz sta interface
	W5G_STA_MAC=`apply_mac_inc -m "$WAN_MAC" -i 7`
	W5G_STA_MAC=`apply_mac_adbit -m "$W5G_STA_MAC"`

	# convert to upper case
	LAN_MAC=`echo $LAN_MAC | tr '[a-z]' '[A-Z]'`
	WAN_MAC=`echo $WAN_MAC | tr '[a-z]' '[A-Z]'`
	W24G_MAC=`echo $W24G_MAC | tr '[a-z]' '[A-Z]'`
	W5G_MAC=`echo $W5G_MAC | tr '[a-z]' '[A-Z]'`
	W24G_GUEST_MAC=`echo $W24G_GUEST_MAC | tr '[a-z]' '[A-Z]'`
	W24G_TC_MAC=`echo $W24G_TC_MAC | tr '[a-z]' '[A-Z]'`
	W5G_GUEST_MAC=`echo $W5G_GUEST_MAC | tr '[a-z]' '[A-Z]'`
	W24G_STA_MAC=`echo $W24G_STA_MAC | tr '[a-z]' '[A-Z]'`
	W5G_STA_MAC=`echo $W5G_STA_MAC | tr '[a-z]' '[A-Z]'`

	# This is common for all platforms
	syscfg_set lan_mac_addr $LAN_MAC
	syscfg_set wan_mac_addr $WAN_MAC
	syscfg_set wl0_mac_addr $W24G_MAC
	syscfg_set wl1_mac_addr $W5G_MAC
	syscfg_set wl0.1_mac_addr $W24G_GUEST_MAC
	syscfg_set wl0.2_mac_addr $W24G_TC_MAC
	syscfg_set wl1.1_mac_addr $W5G_GUEST_MAC
	syscfg_set wl0_sta_mac_addr $W24G_STA_MAC
	syscfg_set wl1_sta_mac_addr $W5G_STA_MAC
	syscfg_commit
	return 0
}

#--------------------------------------------------------------
# main entry
#--------------------------------------------------------------
if [ -z "$ETH0_MAC" ]; then
	display_usage
else
	VALIDATED=`syscfg get wl_params_validated`
	if [ "true" != "$VALIDATED" ]; then
		syscfg_set wl_params_validated true
	fi
	processing
fi
exit 0
