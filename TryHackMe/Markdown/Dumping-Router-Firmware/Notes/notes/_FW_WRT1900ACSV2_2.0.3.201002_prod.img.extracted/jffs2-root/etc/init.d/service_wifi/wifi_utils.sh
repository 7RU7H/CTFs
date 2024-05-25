#!/bin/sh
export TZ=`sysevent get TZ`
source /etc/init.d/syscfg_api.sh
SERVICE_NAME="wifi"
WIFI_PHYSICAL="wifi_physical"
WIFI_VIRTUAL="wifi_virtual"
WIFI_USER="wifi_user"
WIFI_GUEST="wifi_guest"
WIFI_SIMPLETAP="wifi_simpletap"
SYSCFG_INDEX_LIST=`syscfg_get configurable_wl_ifs`
DEFAULT_PHYSICAL_IF_LIST=`syscfg_get lan_wl_physical_ifnames`
PHYSICAL_IF_LIST=$DEFAULT_PHYSICAL_IF_LIST
STA_PHY_IF=`syscfg_get wifi_sta_phy_if`
if [ "0" != "`syscfg_get bridge_mode`" ] && [ -f /etc/init.d/service_wifi/wifi_sta_setup.sh ] && [ "1" = "`syscfg_get wifi_bridge::mode`" ] && [ ! -z $STA_PHY_IF ]; then
	PHYSICAL_IF_LIST=`echo $DEFAULT_PHYSICAL_IF_LIST | sed 's/'"${STA_PHY_IF}"'//g' | sed 's/ //g'`
fi
VIRTUAL_IF_LIST=`syscfg_get lan_wl_virtual_ifnames`
DEVICE_TYPE=`syscfg_get device::deviceType | awk -F":" '{print $4}'`
EXTENDER_RADIO_MODE=`syscfg_get extender_radio_mode`
HOSTNAME=`syscfg_get hostname`
CHANGED_FILE=/tmp/wl_changed_settings.conf
NET_B_ONLY=1
NET_G_ONLY=2
NET_BG_MIXED=3
NET_N_ONLY_24G=4
NET_GN_MIXED=6
NET_BGN_MIXED=7
NET_A_ONLY=8
NET_AN_MIXED=12
NET_N_ONLY_5G=13
NET_BGNAC_MIXED=23
NET_AC_ONLY=24
NET_ANAC_MIXED=28
HTBW_AUTO=0
HTBW_20MHZ=2
HTBW_40MHZ=3
REGION_FCC=0x10
REGION_IC=0x20
REGION_ETSI=0x30
REGION_SPAIN=0x31
REGION_FRANCE=0x32
REGION_MKK=0x40
REGION_MKK2=0x41
REGION_DGT=0x80
REGION_AUS=0x81
WL0_PHYSICAL="wl0_state wl0_channel wl0_radio_band wl0_sideband wl0_network_mode wl0_wmm_ps wl0_stbc emf_wmf wl0_cts_protection_mode wl0_transmission_rate wl0_n_transmission_rate wl0_transmission_power wl0_grn_field_pre wl0_ht_dup_mcs32 wl0_beacon_interval wl0_dtim_interval wl0_fragmentation_threshold wl0_rts_threshold wl_wmm_support wl_no_acknowledgement wl0_txbf_enabled wl0_dfs_enabled wl0_txbf_3x3_only"
WL1_PHYSICAL="wl1_state wl1_channel wl1_radio_band wl1_sideband wl1_network_mode wl1_wmm_ps wl1_stbc emf_wmf wl1_cts_protection_mode wl1_transmission_rate wl1_n_transmission_rate wl1_transmission_power wl1_grn_field_pre wl1_ht_dup_mcs32 wl1_beacon_interval wl1_dtim_interval wl1_fragmentation_threshold wl1_rts_threshold wl_wmm_support wl_no_acknowledgement wl1_txbf_enabled wl1_dfs_enabled wl1_txbf_3x3_only"
WL0_VIRTUAL="wl0_key_0 wl0_key_1 wl0_key_2 wl0_key_3 wl0_security_mode wps_user_setting wl_access_restriction wl_mac_filter wl0_ssid wl0_passphrase wl0_radius_port wl0_shared wl0_tx_key wl0_ap_isolation wl0_frame_burst wl0_radius_server wl0_ssid_broadcast wl0_pmf"
WL1_VIRTUAL="wl1_key_0 wl1_key_1 wl1_key_2 wl1_key_3 wl1_security_mode wps_user_setting wl_access_restriction wl_mac_filter wl1_ssid wl1_passphrase wl1_radius_port wl1_shared wl1_tx_key wl1_ap_isolation wl1_frame_burst wl1_radius_server wl1_ssid_broadcast wl1_pmf"
WL0_GUEST="guest_lan_ipaddr guest_lan_netmask guest_enabled wl0_guest_enabled guest_ssid guest_ssid_broadcast"
WL1_GUEST="guest_lan_ipaddr guest_lan_netmask guest_enabled wl1_guest_enabled wl1_guest_ssid wl1_guest_ssid_broadcast"
WL0_SIMPLETAP="tc_vap_enabled"
WL1_SIMPLETAP="tc_vap_enabled"
restart_required ()
{
	if [ "`syscfg_get ${SERVICE_NAME}_debug`" = "1" ]; then
		set +x
	fi
	MODE=$1
	SYSCFG_INDEX=$2
	RESTART=0
	PHY_INF=`get_phy_interface_name_from_syscfg $SYSCFG_INDEX`
	FILENAME=/tmp/"$SYSCFG_INDEX"_"$MODE"_settings.conf
	if [ ! -f $FILENAME ]; then
		create_files
		RESTART=1
	else
		INFO_NEEDED=`get_settings_list $MODE $SYSCFG_INDEX`
		for FIELD in $INFO_NEEDED; do
			INFO=`syscfg_get ${FIELD}`
			FIELD_DATA="$FIELD":" $INFO"
			FROM_FILE=`cat ${FILENAME} | grep "^$FIELD:"`
			if [ "$FROM_FILE" != "$FIELD_DATA" ] ; then
				RESTART=1
				echo "$FIELD" >> $CHANGED_FILE
			fi
		done
	fi
	if [ "`syscfg_get ${SERVICE_NAME}_debug`" = "1" ]; then
		set -x
	fi
	return $RESTART
}
get_settings_list()
{
	MODE=$1
	SYSCFG_INDEX=$2
	INFO_NEEDED=""
	if [ "wl0" = "$SYSCFG_INDEX" ]; then
		if [ "physical" = "$MODE" ]; then
			INFO_NEEDED=$WL0_PHYSICAL
		elif [ "virtual" = "$MODE" ]; then
			INFO_NEEDED=$WL0_VIRTUAL
		elif [ "guest" = "$MODE" ]; then
			INFO_NEEDED=$WL0_GUEST
		elif [ "simpletap" = "$MODE" ]; then
			INFO_NEEDED=$WL0_SIMPLETAP
		fi
	elif [ "wl1" = "$SYSCFG_INDEX" ]; then
		if [ "physical" = "$MODE" ]; then
			INFO_NEEDED=$WL1_PHYSICAL
		elif [ "virtual" = "$MODE" ]; then
			INFO_NEEDED=$WL1_VIRTUAL
		elif [ "guest" = "$MODE" ]; then
			INFO_NEEDED=$WL1_GUEST
		elif [ "simpletap" = "$MODE" ]; then
			INFO_NEEDED=$WL1_SIMPLETAP
		fi
	fi
	echo "$INFO_NEEDED"
}
create_files ()
{
	for MODE in physical virtual guest simpletap
	do
		SYSCFG_INDEX_LIST=`syscfg_get configurable_wl_ifs`
		for SYSCFG_INDEX in $SYSCFG_INDEX_LIST; do
			FILENAME=/tmp/"$SYSCFG_INDEX"_"$MODE"_settings.conf
			ulog wlan status "${SERVICE_NAME}, cache: saving $SYSCFG_INDEX $MODE settings"
			INFO_NEEDED=`get_settings_list $MODE $SYSCFG_INDEX`
			for FIELD in $INFO_NEEDED; do
				INFO=`syscfg_get ${FIELD}`
				FIELD_DATA="$FIELD":" $INFO"
				echo "$FIELD_DATA" >> $FILENAME
			done
		done
	done
}
update_wifi_cache ()
{
	if [ "`syscfg_get ${SERVICE_NAME}_debug`" = "1" ]; then
		set +x
	fi
	MODE=$1
	SYSCFG_INDEX_LIST=`syscfg_get configurable_wl_ifs`
	if [ "physical" != "$MODE" ] && [ "virtual" != "$MODE" ] && [ "guest" != "$MODE" ] ; then
		echo "Fatal error, the settings will not be saved" > /dev/console
		if [ "`syscfg_get ${SERVICE_NAME}_debug`" = "1" ]; then
			set -x
		fi
		return 1
	fi
	SYSCFG_INDEX_LIST=`syscfg_get configurable_wl_ifs`
	for SYSCFG_INDEX in $SYSCFG_INDEX_LIST; do
		FILENAME=/tmp/"$SYSCFG_INDEX"_"$MODE"_settings.conf
		ulog wlan status "${SERVICE_NAME}, cache: updating $SYSCFG_INDEX $MODE settings"
		INFO_NEEDED=`get_settings_list $MODE $SYSCFG_INDEX`
		for FIELD in $INFO_NEEDED; do
			INFO=`syscfg_get ${FIELD}`
			FIELD_DATA="$FIELD":" $INFO"
			sed -i 's/'"$FIELD"':.*/'"$FIELD_DATA"'/g' $FILENAME
		done
	done
	if [ "`syscfg_get ${SERVICE_NAME}_debug`" = "1" ]; then
		set -x
	fi
	return 0
}
wifi_refresh_interfaces()
{
	if [ "up" = "`syscfg_get wl0_state`" ]; then
		IF=`syscfg get wl0_physical_ifname`
		iwconfig $IF commit
		sleep 2
		if [ "1" = "`syscfg_get wl0_guest_enabled`" ] && [ "1" = "`syscfg_get guest_enabled`" ]; then
			sleep 2
		fi
	fi
	if [ "up" = "`syscfg_get wl1_state`" ]; then
		IF=`syscfg get wl1_physical_ifname`
		iwconfig $IF commit
		sleep 2
		if [ "1" = "`syscfg_get wl1_guest_enabled`" ] && [ "1" = "`syscfg_get guest_enabled`" ]; then
			sleep 2
		fi
	fi
}
set_driver_dfs() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	DFS=`syscfg_get "$SYSCFG_INDEX"_dfs_enabled`
	if [ "1" = "$DFS" ]; then
		iwpriv $PHY_IF 11hspecmgt $DFS
	else
		iwpriv $PHY_IF 11hspecmgt 0
	fi
}
get_phy_interface_name_from_syscfg()
{
	SYSCFG=$1
	INF=""
	if [ "$SYSCFG" = "`syscfg_get wdev0_syscfg_index`" ]; then
		INF="wdev0"
	else
		INF="wdev1"
	fi
	echo "$INF"
}
get_wifi_validation() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	NET=`get_driver_network_mode "$PHY_IF"`
	SEC=`get_security_mode ${SYSCFG_INDEX}_security_mode`
	RET=0 #0/1 for false/true, default is true
	case $NET in
		"$NET_N_ONLY_24G"|"$NET_GN_MIXED"|"$NET_BGN_MIXED"|"$NET_AN_MIXED"|"$NET_N_ONLY_5G"|"$NET_BGNAC_MIXED"|"$NET_AC_ONLY"|"$NET_ANAC_MIXED") # n-mode, ac-mode and mixed mode
		    case $SEC in
				"2"|"3"|"5"|"6")
				    RET=1
				    ;;
				"1"|"4")
				    RET=2
				    ;;
				"7"|"8")
				    RET=2
				    ;;
				"0")
				    RET=1
				    ;;
				*)
				    RET=0
				    ;;
		    esac
		    ;;
		"$NET_B_ONLY"|"$NET_G_ONLY"|"$NET_BG_MIXED"|"$NET_A_ONLY") # Legalcy support wpa, wpa2, wpa/wpa2 mixed, radius, wep
			case $SEC in
				"1"|"2"|"3"|"4"|"5"|"6")
					RET=1
					;;
				"7"|"8")
					RET=1
					;;
				"0")
					RET=1
					;;
				*)
					RET=0
					;;
			esac
			;;
		*)
			RET=0
			;;
	esac
	return "$RET"
}
get_driver_network_mode() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	OPMODE=0
	SYSCFG_NETWORK_MODE=""
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			SYSCFG_NETWORK_MODE=`syscfg_get wl1_network_mode`
		else
			SYSCFG_NETWORK_MODE=`syscfg_get wl0_network_mode`
		fi
		SYSCFG_INDEX=wl"$EXTENDER_RADIO_MODE"
	else
		SYSCFG_NETWORK_MODE=`syscfg_get "$SYSCFG_INDEX"_network_mode`
	fi
	case "$SYSCFG_NETWORK_MODE" in
		"11a")
			OPMODE="$NET_A_ONLY"
			;;
		"11b")
			OPMODE="$NET_B_ONLY"
			;;
		"11g")
			OPMODE="$NET_G_ONLY"
			;;
		"11n")
			if [ "$SYSCFG_INDEX" = "wl0" ]; then
				OPMODE="$NET_N_ONLY_24G"
			else
				OPMODE="$NET_N_ONLY_5G"
			fi
			;;
		"11b 11g")
			OPMODE="$NET_BG_MIXED"
			;;
		"11g 11n")
			OPMODE="$NET_GN_MIXED"
			;;
		"11a 11n")
			OPMODE="$NET_AN_MIXED"
			;;
		"11b 11g 11n")
			OPMODE="$NET_BGN_MIXED"
			;;
		"11b 11g 11n 11ac")
			OPMODE="$NET_BGNAC_MIXED"
			;;
		"11ac")
			OPMODE="$NET_AC_ONLY"
			;;
		"11a 11n 11ac")
			OPMODE="$NET_ANAC_MIXED"
			;;
		"Mixed" | "mixed" | "MIXED")
			if [ "$SYSCFG_INDEX" = "wl0" ]; then
				OPMODE="$NET_BGNAC_MIXED"
			else
				OPMODE="$NET_ANAC_MIXED"
			fi
			;;
		*)
			if [ "$SYSCFG_INDEX" = "wl0" ]; then
				OPMODE="$NET_BGNAC_MIXED"
			else
				OPMODE="$NET_ANAC_MIXED"
			fi
			;;
	esac
	
	echo "$OPMODE"
}
get_wl_index() 
{
	wl_index=0
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [" $EXTENDER_RADIO_MODE" = "1" ]; then
			wl_index=1
		else
			wl_index=0
		fi
	else
		PHY_IF=`echo $1 | cut -c 1-5`
		SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
		wl_index=`echo $SYSCFG_INDEX | cut -c3`
	fi
	return "$wl_index"
}
get_security_mode() 
{
	SECURITY_MODE=""
	INDEX=0
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			INDEX=1
		else
			INDEX=0
		fi
		MODE_STRING=`syscfg_get wl"$INDEX"_security_mode`
	else
		MODE_STRING=`syscfg_get $1`
	fi
	if [ "wpa-personal" = "$MODE_STRING" ]; then
		SECURITY_MODE=1	
	elif [ "wpa2-personal" = "$MODE_STRING" ]; then
		SECURITY_MODE=2	
	elif [ "wpa-mixed" = "$MODE_STRING" ]; then
		SECURITY_MODE=3	
	elif [ "wpa-enterprise" = "$MODE_STRING" ]; then
		SECURITY_MODE=4	
	elif [ "wpa2-enterprise" = "$MODE_STRING" ]; then
		SECURITY_MODE=5	
	elif [ "wpa-enterprise-mixed" = "$MODE_STRING" ]; then
		SECURITY_MODE=6	
	elif [ "radius" = "$MODE_STRING" ]; then
		SECURITY_MODE=7	
	elif [ "wep" = "$MODE_STRING" ]; then
		SECURITY_MODE=8	
	elif [ "wep-auto" = "$MODE_STRING" ]; then
		SECURITY_MODE=8	
	elif [ "wep-open" = "$MODE_STRING" ]; then
		SECURITY_MODE=8	
	elif [ "wep-shared" = "$MODE_STRING" ]; then
		SECURITY_MODE=8	
	elif [ "disabled" = "$MODE_STRING" ]; then
		SECURITY_MODE=0	
	else 
		SECURITY_MODE=0	
	fi
	echo "$SECURITY_MODE"	
}
get_encryption() 
{
	ENCRYPTION_MODE=""
	wl_index=""
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			wl_index=1
		else
			wl_index=0
		fi
		ENCRYPTION_STRING=`syscfg_get wl"$INDEX"_encryption`
	else
		wl_index=`echo $1 | cut -c3`
		ENCRYPTION_STRING=`syscfg_get $1`
	fi
	SEC_MODE=`syscfg_get wl"$wl_index"_security_mode`
	if [ "wep" = "$SEC_MODE" ] || [ "wep-auto" = "$SEC_MODE" ] || [ "wep-open" = "$SEC_MODE" ] || [ "wep-shared" = "$SEC_MODE" ]; then
		TX_KEY=`syscfg_get wl"$wl_index"_tx_key`
		INDEX_KEY=`expr $TX_KEY - 1`
		CURRENT_KEY=`syscfg_get wl"$wl_index"_key_"$INDEX_KEY"`
		CURRENT_KL=`echo $CURRENT_KEY | wc -c`
		if [ 11 = `expr $CURRENT_KL` ] || [ 6 = `expr $CURRENT_KL` ]; then
			ENCRYPTION_MODE="64-bits"
		elif [ 27 = `expr $CURRENT_KL` ] || [ 14 = `expr $CURRENT_KL` ]; then
			ENCRYPTION_MODE="128-bits"
		fi
	else
		case "$ENCRYPTION_STRING" in
		"aes")
			ENCRYPTION_MODE="CCMP"
			;;
		"tkip")
			ENCRYPTION_MODE="TKIP"
			;;
		"tkip+aes")
			ENCRYPTION_MODE="TKIP CCMP"
			;;
		esac
	fi
	echo "$ENCRYPTION_MODE"	
}
get_ssid_broadcast() 
{
	SYSCFG_INDEX=$1
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			SYSCFG_INDEX="wl1"
		else
			SYSCFG_INDEX="wl0"
		fi
	fi
	ssid_broadcast=`syscfg_get ${SYSCFG_INDEX}_ssid_broadcast`
	if [ -z "$ssid_broadcast" ]; then
		ssid_broadcast=1
	fi
	echo "$ssid_broadcast"
}
set_driver_mac_filter_enabled () 
{
	if_name=$1
	FILTER_OPTION=`syscfg_get wl_access_restriction`
	MAC_ENTRIES=`syscfg_get wl_mac_filter`
	if [ "$FILTER_OPTION" = "allow" ] || [ "$FILTER_OPTION" = "deny" ]; then
		if [ "$FILTER_OPTION" = "allow" ]; then
			iwpriv $if_name filter 1
		else
			iwpriv $if_name filter 2
		fi
		for MAC in $MAC_ENTRIES; do
			macstring=`echo $MAC | awk -F":" '{print $1$2$3$4$5$6}'`
			iwpriv $if_name filtermac "add $macstring"
		done
	fi
	return 0
}
set_driver_mac_filter_disabled() 
{
	if_name=$1
	iwpriv $if_name filter 0
	iwpriv $if_name filtermac "deleteall"
}
get_syscfg_interface_name()
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$1"_syscfg_index`
	echo "$SYSCFG_INDEX"
}
add_guest_vlan_to_backhaul()
{
	VID=`syscfg_get guest_vlan_id`
	GUEST_BRIDGE=`syscfg_get guest_lan_ifname`
	BACKHAUL_IF_LIST=`syscfg_get backhaul_ifname_list`
	for INTF in $BACKHAUL_IF_LIST; do
		vconfig set_name_type DEV_PLUS_VID_NO_PAD
		vconfig add $INTF $VID
		add_interface_to_bridge $INTF.$VID $GUEST_BRIDGE
		ebtables -t broute -I BROUTING -i $INTF -p 802.1Q --vlan-id $VID -j DROP
	done
	return 0
}
delete_guest_vlan_from_backhaul()
{
	VID=`syscfg_get guest_vlan_id`
	BACKHAUL_IF_LIST=`syscfg_get backhaul_ifname_list`
	for INTF in $BACKHAUL_IF_LIST; do
		vconfig rem $INTF.$VID
		ebtables -t broute -D BROUTING -i $INTF -p 802.1Q --vlan-id $VID -j DROP
	done
	return 0
}
add_interface_to_bridge()
{
	VAP=$1
	BRIDGE=$2
	if [ -z "$BRIDGE" ]; then	 
		ulog wlan status "${SERVICE_NAME}, add_interface_to_bridge(), bridge name is empty"
		return 1
	fi
	TEMP=`brctl show | grep ${BRIDGE} | awk '{print $1}'`
	if [ "$BRIDGE" = "$TEMP" ]; then
		MAC_1=`get_mac ${VAP}`
		if [ ! -z "$MAC_1" ]; then	 
			ip link set $VAP allmulticast on
			MAC_2=`brctl showmacs ${BRIDGE} | grep ${MAC_1} | awk '{print $2}'`
			if [ "${MAC_1}" = "${MAC_2}" ]; then
				brctl delif $BRIDGE $VAP
			fi
			brctl addif $BRIDGE $VAP
		fi
	fi 
	return 0
}
delete_interface_from_bridge()
{
	VAP=$1
	BRIDGE=$2
	ip link set $VAP allmulticast off
	if [ -z "$BRIDGE" ]; then	 
		ulog wlan status "${SERVICE_NAME}, delete_interface_to_bridge(), bridge name is empty"
		return 1
	fi
	TEMP=`brctl show | grep ${BRIDGE} | awk '{print $1}'`
	if [ "$BRIDGE" = "$TEMP" ]; then 
		MAC_1=`get_mac ${VAP}`
		if [ ! -z "$MAC_1" ]; then	 
			MAC_2=`brctl showmacs ${BRIDGE} | grep ${MAC_1} | awk '{print $2}'`
			if [ "${MAC_1}" = "${MAC_2}" ]; then
				brctl delif $BRIDGE $VAP
			fi
		fi
	fi 
	return 0
}
get_physical_interface_state() 
{
	PHY_IF=$1
	STATE=`ifconfig $PHY_IF | grep MTU | awk '/UP/ {print $1}'`
	if [ ! -z "$STATE" ] && [ "$STATE" = "UP" ]; then
		STATE="up"
	else
		STATE="down"
	fi
	echo "$STATE"
}
get_mac_address()
{
	PHY_IF=$1
	MAC=`ifconfig $PHY_IF | grep HWaddr | awk '{print $5}'`
	echo "$MAC"
}
set_driver_regioncode()
{
	for PHY_IF in $PHYSICAL_IF_LIST; do
		REGION=`syscfg_get device::cert_region`
		if [ "$REGION" = "EU" ]; then
			REGION_CODE="$REGION_ETSI"
		elif [ "$REGION" = "CA" ]; then
			REGION_CODE="$REGION_IC"
		elif [ "$REGION" = "PH" ]; then
			REGION_CODE="$REGION_FCC"
		elif [ "$REGION" = "AU" ]; then
			REGION_CODE="$REGION_AUS"
		elif [ "$REGION" = "AP" ] || [ "$REGION" = "AH" ]; then
			SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
			if [ "$SYSCFG_INDEX" = "wl0" ]; then
				REGION_CODE="$REGION_ETSI"
			else
				REGION_CODE="$REGION_FCC"
			fi
		else
			REGION_CODE="$REGION_FCC"
		fi
		iwpriv $PHY_IF regioncode $REGION_CODE
	done
	return 0
}
load_wifi_driver()
{
	ulog wlan status "${SERVICE_NAME}, loading Wi-Fi driver"
	MODULE_PATH=/lib/modules/`uname -r`/
	MODULE_NAME=${MODULE_PATH}ap8x.ko
	if [ -f $MODULE_NAME ]; then
		cd $MODULE_PATH
		insmod ap8x.ko
		INTNUM=`cat /proc/interrupts | grep wdev1 | sed -e "s/:.*$//" -e "s/^[ ]*//"`
		echo 2 > /proc/irq/$INTNUM/smp_affinity
		CWD=`pwd`
		cd $CWD
		ulog wlan status "${SERVICE_NAME}, loading $MODULE_NAME with GPL" > /dev/console
		return 0
	else
		ERROR="${SERVICE_NAME}, Error! Missing $MODULE_NAME"
		ulog wlan status $ERROR > /dev/console
		return 1
	fi
}
is_legalcy_mode()
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_NETWORK_MODE=""
	RET_STR="false"
	
	SYSCFG_NETWORK_MODE=`syscfg_get "$SYSCFG_INDEX"_network_mode`
	case "$SYSCFG_NETWORK_MODE" in
		"11a")
			RET_STR="true"
			;;
		"11b 11g")
			RET_STR="true"
			;;
		"11b")
			RET_STR="true"
			;;
		"11g")
			RET_STR="true"
			;;
		*)
			RET_STR="false"
			;;
	esac
	echo "$RET_STR"
}
set_macs()
{
	for PHY_IF in $PHYSICAL_IF_LIST; do
		set_driver_user_mac $PHY_IF
		set_driver_guest_mac $PHY_IF
		set_driver_tc_mac $PHY_IF
		set_driver_sta_mac $PHY_IF
	done
	return 0	
}
set_driver_sta_mac()
{
	PHY_IF=$1
	WL_SYSCFG=`get_syscfg_interface_name $PHY_IF`
	WLX_STA_IF=`syscfg_get "$WL_SYSCFG"_sta_vap`
	WLX_MAC_NEW=`syscfg_get "$WL_SYSCFG"_sta_mac_addr`
	if [ ! -z "$WLX_STA_IF" ] && [ ! -z "$WLX_MAC_NEW" ]; then
		STA_MAC=`echo $WLX_MAC_NEW | tr -d :`
		iwpriv $WLX_STA_IF macclone "0 $STA_MAC"
		sleep 1
		iwconfig $WLX_STA_IF commit
	fi
	return 0
}
set_driver_user_mac()
{
	PHY_IF=$1
	WL_SYSCFG=`get_syscfg_interface_name $PHY_IF`
	WLX_PHY_IF=`syscfg_get "$WL_SYSCFG"_physical_ifname`
	WLX_VIR_IF=`syscfg_get "$WL_SYSCFG"_user_vap`
	WLX_MAC_CURRENT=`ifconfig ${WLX_PHY_IF} | grep HWaddr | awk '{print $5}'`
	WLX_MAC_NEW=`syscfg_get "$WL_SYSCFG"_mac_addr`
	if [ ! -z "$WLX_MAC_NEW" ] && [ "$WLX_MAC_NEW" != "$WLX_MAC_CURRENT" ]; then
		WLX_MAC=`echo $WLX_MAC_NEW | tr -d :`
		iwpriv $WLX_PHY_IF bssid $WLX_MAC
		iwpriv $WLX_VIR_IF bssid $WLX_MAC
		sleep 1
		iwconfig $WLX_VIR_IF commit
	fi
	return 0
}
set_driver_guest_mac()
{
	PHY_IF=$1
	WL_SYSCFG=`get_syscfg_interface_name $PHY_IF`
	GUEST_IFNAME=`syscfg_get "$WL_SYSCFG"_guest_vap`
	GUEST_MAC_NEW=`syscfg_get "$WL_SYSCFG".1_mac_addr`
	if [ ! -z "$GUEST_IFNAME" ] && [ ! -z "$GUEST_MAC_NEW" ]; then
		GUEST_MAC=`echo $GUEST_MAC_NEW | tr -d :`
		iwpriv $GUEST_IFNAME bssid $GUEST_MAC
		sleep 1
		iwconfig $GUEST_IFNAME commit
	fi
	return 0
}
set_driver_tc_mac()
{
	PHY_IF=$1
	WL_SYSCFG=`get_syscfg_interface_name $PHY_IF`
	if [ "$WL_SYSCFG" = "wl0" ]; then
		TC_IFNAME=`syscfg_get tc_vap_user_vap`
		TC_MAC_NEW=`syscfg_get "$WL_SYSCFG".2_mac_addr`
		if [ ! -z "$TC_IFNAME" ] && [ ! -z "$TC_MAC_NEW" ]; then
			TC_MAC=`echo $TC_MAC_NEW | tr -d :`
			iwpriv $TC_IFNAME bssid $TC_MAC
			sleep 1
			iwconfig $TC_IFNAME commit
		fi
	fi
	return 0
}
