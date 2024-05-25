#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
source /etc/init.d/service_wifi/wifi_utils.sh
source /etc/init.d/service_wifi/wifi_virtual.sh
source /etc/init.d/service_wifi/wifi_platform_specific_setting.sh
wifi_physical_start()
{
	ulog wlan status "${SERVICE_NAME}, wifi_physical_start($1)"
	echo "${SERVICE_NAME}, wifi_physical_start($1)"
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	wait_till_end_state ${WIFI_PHYSICAL}_${PHY_IF}
	STATUS=`sysevent get ${WIFI_PHYSICAL}_${PHY_IF}-status`
	if [ "started" = "$STATUS" ] || [ "starting" = "$STATUS" ] ; then
		ulog wlan status "${SERVICE_NAME}, ${WIFI_PHYSICAL} is starting/started, ignore the request"
		return 1
	fi
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	USER_STATE=`syscfg_get ${SYSCFG_INDEX}_state`
	if [ "$USER_STATE" = "down" ]; then
		VIR_IF=`syscfg_get "$SYSCFG_INDEX"_user_vap`
		echo "${SERVICE_NAME}, ${SYSCFG_INDEX}_state=$USER_STATE, do not start physical $PHY_IF"
		return 1
	fi
	STA_PHY_IF=`syscfg_get wifi_sta_phy_if`
	if [ "2" = "`syscfg_get wifi_bridge::mode`" ] && [ "$PHY_IF" = "$STA_PHY_IF" ]; then
		echo "${SERVICE_NAME}, $PHY_IF is in repeater mode, do not start physical again"
		return 1
	fi
	sysevent set ${WIFI_PHYSICAL}_${PHY_IF}-status starting
	ifconfig $PHY_IF up
	physical_pre_setting $PHY_IF
	
	physical_setting $PHY_IF
	
	physical_post_setting $PHY_IF
	sleep 1
	echo "wifi_physical, iwconfig $PHY_IF commit"
	iwconfig $PHY_IF commit
	sysevent set ${WIFI_PHYSICAL}_${PHY_IF}-status started
	
	return 0
}
wifi_physical_stop()
{
	ulog wlan status "${SERVICE_NAME}, wifi_physical_stop($1)"
	echo "${SERVICE_NAME}, wifi_physical_stop($1)"
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	wait_till_end_state ${WIFI_PHYSICAL}_${PHY_IF}
	STATUS=`sysevent get ${WIFI_PHYSICAL}_${PHY_IF}-status`
	if [ "stopping" = "$STATUS" ] || [ "stopped" = "$STATUS" ] || [ -z "$STATUS" ]; then
		ulog wlan status "${SERVICE_NAME}, ${WIFI_PHYSICAL} is already stopping/stopped, ignore the request"
		return 1
	fi
	STA_PHY_IF=`syscfg_get wifi_sta_phy_if`
	if [ "2" = "`syscfg_get wifi_bridge::mode`" ] && [ "$PHY_IF" = "$STA_PHY_IF" ]; then
		echo "${SERVICE_NAME}, $PHY_IF is in repeater mode, do not stop this physical interface"
		return 1
	fi
        
	sysevent set ${WIFI_PHYSICAL}_${PHY_IF}-status stopping
	if [ "`sysevent get ldal_setup_vap-status`" = "started" ]; then
		sysevent set ldal_setup_vap-stop
		wait_till_end_state ldal_setup_vap
	fi
	
	if [ "`sysevent get ldal_infra_vap-status`" = "started" ]; then
		sysevent set ldal_infra_vap-stop
		wait_till_end_state ldal_infra_vap
	fi
	
	if [ "`sysevent get ldal_station_connect-status`" = "started" ]; then
		sysevent set ldal_station_connect-stop
		wait_till_end_state ldal_station_connect
	fi
	echo "wifi_physical, ifconfig $PHY_IF down"
	ifconfig $PHY_IF down
	
	sysevent set ${WIFI_PHYSICAL}_${PHY_IF}-status stopped
	return 0
}
wifi_physical_restart()
{
	ulog wlan status "${SERVICE_NAME}, wifi_physical_restart()"
	echo "${SERVICE_NAME}, wifi_physical_restart()"
	for PHY_IF in $PHYSICAL_IF_LIST; do
		wifi_physical_stop $PHY_IF
		wifi_physical_start $PHY_IF
	done
	return 0
}
physical_pre_setting()
{
	ulog wlan status "${SERVICE_NAME}, physical_pre_setting($1)"
	PHY_IF=$1
	initialize_physical_station $PHY_IF
	return 0
}
physical_post_setting()
{
	ulog wlan status "${SERVICE_NAME}, physical_post_setting($1)"
	PHY_IF=$1
	LDAL_ENABLED=`syscfg_get lego_enabled`
	LDAL_VSTA=`syscfg_get ldal_wl_vsta`
	if [ ! -z "$LDAL_ENABLED" ] && [ "$LDAL_ENABLED" = "1" ]; then
		if [ -z "$LDAL_VSTA" ]; then
			ulog wlan status "${SERVICE_NAME}, fire up the infra vap first" > /dev/console
			sysevent set ldal_infra_vap-stop
			sysevent set ldal_infra_vap-start
		else
			EDALSETTINGFILE="/var/config/ldal/edalsettingd.cfg"
			if [ ! -f $EDALSETTINGFILE ]; then
				ulog wlan status "${SERVICE_NAME}, force to edal setup on the first boot" > /dev/console
				syscfg_set ldal_wl_station_state unconfigured
			fi
			ulog wlan status "${SERVICE_NAME}, fire up the station connect first" > /dev/console
			sysevent set ldal_station_connect-stop
			sysevent set ldal_station_connect-start
		fi
	fi
	return 0
}
physical_setting()
{
	ulog wlan status "${SERVICE_NAME}, physical_setting($1)"
	PHY_IF=$1
	STA_PHY_IF=`syscfg_get wifi_sta_phy_if`
	
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		set_driver_opmode $PHY_IF
		set_driver_wmm $PHY_IF
	else
		set_driver_txantenna $PHY_IF
		set_driver_beamforming $PHY_IF
		set_driver_gprotect $PHY_IF
		set_driver_htprotect $PHY_IF
		set_driver_bcninterval $PHY_IF
		set_driver_rts_threshold $PHY_IF
		set_driver_htgreenfiled_preamble $PHY_IF
		set_driver_htstbc $PHY_IF
		set_driver_transmission_rate $PHY_IF
		if [ "" = "`syscfg_get wifi_bridge::mode`" ] || [ "0" = "`syscfg_get wifi_bridge::mode`" ] || [ "$PHY_IF" != "$STA_PHY_IF" ]; then
			set_driver_htbw $PHY_IF
			set_driver_extsubch $PHY_IF
			set_driver_channel $PHY_IF
			set_driver_wmm $PHY_IF
			set_driver_opmode $PHY_IF
		fi					
		set_driver_dfs $PHY_IF			
		set_driver_optlevel $PHY_IF			
	fi	
	return 0
}
initialize_physical_station()
{
	ulog wlan status "${SERVICE_NAME}, initialize_physical_station($1)"
	PHY_IF=$1
	CHANGE_STAMODE=false
	LDAL_STAMODE=`syscfg_get ldal_wl_stamode`
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			if [ "$LDAL_STAMODE" != "8" ]; then
				LDAL_STAMODE=8
				syscfg_set ldal_wl_stamode `expr $LDAL_STAMODE`
				CHANGE_STAMODE=true
			fi
		else 
			if [ "$LDAL_STAMODE" != "7" ]; then
				LDAL_STAMODE=7
				syscfg_set ldal_wl_stamode `expr $LDAL_STAMODE`
				CHANGE_STAMODE=true
			fi
		fi
		if [ "$CHANGE_STAMODE" = "true" ]; then
			LDAL_VSTA=`syscfg_get ldal_wl_vsta`
			iwpriv `echo $LDAL_VSTA | cut -c1-5` autochannel 1
		fi
		ulog wlan status "Bring down the vsta to avoid continue scanning" > /dev/console
		LDAL_VSTA=`syscfg_get ldal_wl_vsta`
		ifconfig "$LDAL_VSTA" down
	fi
	return 0
}
get_wl_name() 
{
	if [ "Extender" = "$DEVICE_TYPE" ]; then
		if [ ! -z "$EXTENDER_RADIO_MODE" ] && [ $EXTENDER_RADIO_MODE = "1" ]; then
			wlname="wl1"
		else
			wlname="wl0"
		fi
	else
		wlname=$1;
	fi
	echo "$wlname"
}
get_driver_bandwidth() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_BW=`syscfg_get "$SYSCFG_INDEX"_radio_band`
	if [ "standard" = "$SYSCFG_BW" ]; then
		HTBW="$HTBW_20MHZ"
	elif [ "wide" = "$SYSCFG_BW" ]; then
		HTBW="$HTBW_40MHZ"
	else
		HTBW="$HTBW_AUTO"
	fi
	
	echo $HTBW
}
get_driver_channel() 
{
	channel="$1"
	echo "$channel"
}
get_driver_trans_rate() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_TRANS_RATE=`syscfg_get "$SYSCFG_INDEX"_transmission_rate`
	if [ -n "$SYSCFG_TRANS_RATE" ] && [ "auto" = "$SYSCFG_TRANS_RATE" ]; then
		TRANS_RATE=0
	else
		case "$SYSCFG_TRANS_RATE" in
			"6000000")
				TRANS_RATE=12
				;;
			"9000000")
				TRANS_RATE=18
				;;
			"12000000")
				TRANS_RATE=24
				;;
			"18000000")
				TRANS_RATE=36
				;;
			"24000000")
				TRANS_RATE=48
				;;
			"36000000")
				TRANS_RATE=72
				;;
			"48000000")
				TRANS_RATE=96
				;;
			"54000000")
				TRANS_RATE=108
				;;
			*)
				TRANS_RATE=0
				ulog wlan status "invalid transmission_rate: $1"
				;;
		esac
	fi
			
	echo "$TRANS_RATE"
}
get_driver_n_trans_rate() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_NXRATE=`syscfg_get "$SYSCFG_INDEX"_n_transmission_rate`
	NXRATE=0 
	if [ -n "$SYSCFG_NXRATE" ]; then
		if [ "auto" = "$SYSCFG_NXRATE" ]; then
			NXRATE=0
		elif [ $SYSCFG_NXRATE -ge 0 ] && [ $SYSCFG_NXRATE -le 15 ]; then
			NXRATE=`expr $SYSCFG_NXRATE + 256`
		else
			ulog wlan status "invalid n_transmission_rate: $SYSCFG_NXRATE"
		fi
	fi
	echo "$NXRATE"
}
get_driver_beacon_interval() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_BCN_INTERVAL=`syscfg_get "$SYSCFG_INDEX"_beacon_interval`
	if [ -n "$SYSCFG_BCN_INTERVAL" ] && [ $SYSCFG_BCN_INTERVAL -ge 20 ] && [ $SYSCFG_BCN_INTERVAL -le 1000 ]; then
		BCN_INTERVAL=$SYSCFG_BCN_INTERVAL
	else
		BCN_INTERVAL=100
	fi
	echo "$BCN_INTERVAL"
}
get_driver_rts_threshold() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_RTS_THRESHOLD=`syscfg_get "$SYSCFG_INDEX"_rts_threshold`
	if [ -n "$SYSCFG_RTS_THRESHOLD" ] && [ $SYSCFG_RTS_THRESHOLD -ge 255 ] && [ $SYSCFG_RTS_THRESHOLD -le 2346 ]; then
		RTS_THRESHOLD=$SYSCFG_RTS_THRESHOLD
	else
		RTS_THRESHOLD=2347
	fi
	echo "$RTS_THRESHOLD"
}
get_driver_grn_field_pre() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_HTGREENFIELD_PREAMBLE=`syscfg_get "$SYSCFG_INDEX"_grn_field_pre`
	if [ "$SYSCFG_HTGREENFIELD_PREAMBLE" = "enabled" ]; then
		HTGREENFIELD_PREAMBLE=1
	else
		HTGREENFIELD_PREAMBLE=0
	fi
	echo "$HTGREENFIELD_PREAMBLE"
}
get_driver_stbc() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_STBC=`syscfg_get "$SYSCFG_INDEX"_stbc`
    if [ "$SYSCFG_STBC" = "enabled" ]; then
		STBC=1
    else
		STBC=0
    fi
    echo "$STBC"
}
set_vendor_sideband() 
{
	WL_INDEX=$1
	CH=$2
	PREFIX=wl"$WL_INDEX"
	if [ "0" = "$WL_INDEX" ]; then
		if [ "$CH" = "1" -o "$CH" = "2" -o "$CH" = "3" -o "$CH" = "4" ]; then
			syscfg_set "$PREFIX"_sideband upper
		else
			syscfg_set "$PREFIX"_sideband lower
	 	fi	
	elif [ "1" = "$WL_INDEX" ]; then
		if [ "$CH" = "36" -o "$CH" = "44" -o "$CH" = "149" -o "$CH" = "157" ]; then
			syscfg_set "$PREFIX"_sideband upper
		elif [ "$CH" = "40" -o "$CH" = "48" -o "$CH" = "153" -o "$CH" = "161" ]; then
			syscfg_set "$PREFIX"_sideband lower
		else
			syscfg_set "$PREFIX"_sideband auto
		fi
	fi
	return 0
}
set_transmission_rate()
{
	PHY_IF=$1
	OPMODE=$2
	GA_RATE=$3
	N_RATE=$4
	RATE_AUTO=0
	RATE_FIXED=2
	case $OPMODE in
		"$NET_B_ONLY")  # b mode, set to auto
			iwpriv $PHY_IF fixrate $RATE_AUTO 
			;;
		"$NET_G_ONLY"|"$NET_BG_MIXED"|"$NET_BGN_MIXED") # g, bg, bgn mode
			if [ "$GA_RATE" -eq "0" ]; then  # auto 
				iwpriv $PHY_IF fixrate $RATE_AUTO
			else
				iwpriv $PHY_IF fixrate $RATE_FIXED
				iwpriv $PHY_IF txrate "g $GA_RATE"
				if [ "$OPMODE" -eq "$NET_BG_MIXED" ]; then
					if [ "$GA_RATE" -le "18" ]; then  
						brate=11
					else
						brate=22
					fi
					iwpriv $PHY_IF txrate "b $brate"
				fi
				if [ "$OPMODE" -eq "$NET_BGN_MIXED" ] && [ "$N_RATE" -ne "0" ]; then
					iwpriv $PHY_IF txrate "n $N_RATE"
				fi
			fi 
			;;
		"$NET_N_ONLY_24G"|"$NET_N_ONLY_5G")  # 2.4 GH  or 5 GHz n mode
			if [ "$N_RATE" -eq "0" ]; then
				iwpriv $PHY_IF fixrate $RATE_AUTO
			else
				iwpriv $PHY_IF fixrate $RATE_FIXED
				iwpriv $PHY_IF txrate "n $N_RATE"
			fi 
			;;
		"$NET_A_ONLY"|"$NET_AN_MIXED")  # a  or an mode
			if [ "$GA_RATE" -eq "0" ]; then
				iwpriv $PHY_IF fixrate $RATE_AUTO
			else
				iwpriv $PHY_IF fixrate $RATE_FIXED
				iwpriv $PHY_IF txrate "a $GA_RATE"
				if [ "$OPMODE" -eq "$NET_AN_MIXED" ] && [ "$N_RATE" -ne "0" ]; then
					iwpriv $PHY_IF txrate "n $N_RATE"
				fi
			fi 
			;;
		"$NET_BGNAC_MIXED"|"$NET_ANAC_MIXED"|"$NET_AC_ONLY")  # ac mode just let it auto for now
			;;
		*)
			echo "${SERVICE_NAME}, ERROR: unsupported network mode"
			;;
	esac
	return 0
} 
set_driver_opmode() 
{
	PHY_IF=$1
	get_wifi_validation ${PHY_IF}
	VALID=$?
	if [ "$VALID" = "2" ]; then
		ulog wlan status "${SERVICE_NAME}, wifi setting is incompatible, reconfigure the network mode according to WPA mode on $SYSCFG_INDEX" > /dev/console
		current_bandwidth=`syscfg_get ${SYSCFG_INDEX}_radio_band`
		if [ "wl0" = "$SYSCFG_INDEX" ]; then
			syscfg_set ${SYSCFG_INDEX}_network_mode "11b 11g"
		else
			syscfg_set ${SYSCFG_INDEX}_network_mode "11a"
		fi
		
		if [ "wide" = "$current_bandwidth" ]; then
			syscfg_set ${SYSCFG_INDEX}_radio_band "auto"
		fi
	fi
	OPMODE=`get_driver_network_mode "$PHY_IF"`
	iwpriv $PHY_IF opmode $OPMODE
	return 0
}
set_driver_wmm() 
{
	PHY_IF=$1
    if [ "`syscfg_get wl_wmm_support`" = "enabled" ]; then
        iwpriv $PHY_IF wmm 1
    else
        iwpriv $PHY_IF wmm 0
    fi
	return 0
}
set_driver_htbw() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	HTBW=`get_driver_bandwidth "$PHY_IF"`
	LEGALCY=`is_legalcy_mode $PHY_IF`
	if [ "true" = "$LEGALCY" ]; then
		iwpriv $PHY_IF htbw "$HTBW_20MHZ"
		if [ "$HTBW_40MHZ" = "$HTBW" ]; then
			syscfg_set "$SYSCFG_INDEX"_radio_band "standard"
		fi
	else
		iwpriv $PHY_IF htbw $HTBW
	fi
	return 0
}
set_driver_gprotect() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_CTSPROTECTION=`syscfg_get "$SYSCFG_INDEX"_cts_protection_mode`
	GPROTECT=0
	if [ "auto" = "$SYSCFG_CTSPROTECTION" ]; then
		if [ "$SYSCFG_INDEX" = "wl0" ]; then
			GPROTECT=1
		fi
	fi
	iwpriv $PHY_IF gprotect $GPROTECT
	return 0
}
set_driver_htprotect() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_CTSPROTECTION=`syscfg_get "$SYSCFG_INDEX"_cts_protection_mode`
	HTPROTECT=0
	if [ "auto" = "$SYSCFG_CTSPROTECTION" ]; then
		HTPROTECT=4
	fi
	iwpriv $PHY_IF htprotect $HTPROTECT
	return 0
}
set_driver_bcninterval() 
{
	PHY_IF=$1
	BCNINTERVAL=`get_driver_beacon_interval "$PHY_IF"`
	iwpriv $PHY_IF bcninterval $BCNINTERVAL
	return 0
}
set_driver_rts_threshold() 
{
	PHY_IF=$1
	RTS_THRESHOLD=`get_driver_rts_threshold "$PHY_IF"`
	iwconfig $PHY_IF rts $RTS_THRESHOLD
	return 0
}
set_driver_htgreenfiled_preamble() 
{
	PHY_IF=$1
	GRNFIELDPRE=`get_driver_grn_field_pre "$PHY_IF"`
	iwpriv $PHY_IF htgf $GRNFIELDPRE
	return 0
}
set_driver_htstbc() 
{
	PHY_IF=$1
	STBC=`get_driver_stbc "$PHY_IF"`
	iwpriv $PHY_IF htstbc $STBC
	return 0
}
set_driver_transmission_rate() 
{
	PHY_IF=$1
	GAXRATE=`get_driver_trans_rate "$PHY_IF"`
	NXRATE=`get_driver_n_trans_rate "$PHY_IF"`
	OPMODE=`get_driver_network_mode "$PHY_IF"`
	set_transmission_rate $PHY_IF $OPMODE $GAXRATE $NXRATE		
	
	return 0
}
set_driver_channel() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	SYSCFG_CHANNEL=`syscfg_get "$SYSCFG_INDEX"_channel`
	if [ "`syscfg_get device::cert_region`" = "AP" ]; then
		if [ "$SYSCFG_INDEX" = "wl0" ]; then
			REGION_CODE="$REGION_ETSI"
		else
			REGION_CODE="$REGION_FCC"
		fi
		iwpriv $PHY_IF regioncode $REGION_CODE
	fi
	
	if [ "auto" = $SYSCFG_CHANNEL -o "0" = $SYSCFG_CHANNEL ]; then
		iwpriv $PHY_IF autochannel 1  #Let Driver decide it
	else
		iwpriv $PHY_IF autochannel 0  
		iwconfig $PHY_IF channel `expr $SYSCFG_CHANNEL`
	fi
	return 0
}
set_driver_extsubch() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	iwpriv $PHY_IF extsubch 0
	return 0
}
set_driver_optlevel() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	FRAME_BURST=`syscfg_get "$SYSCFG_INDEX"_frame_burst`
	if [ "$FRAME_BURST" = "enabled" ]; then
		OPTLEVEL=1
	else
		OPTLEVEL=0
	fi
	iwpriv $PHY_IF optlevel $OPTLEVEL
	return 0
}
