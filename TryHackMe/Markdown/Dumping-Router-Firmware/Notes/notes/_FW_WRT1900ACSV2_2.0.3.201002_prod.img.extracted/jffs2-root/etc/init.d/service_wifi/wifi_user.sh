#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
source /etc/init.d/service_wifi/wifi_utils.sh
WPS_STATE=1
wifi_user_start()
{
	echo "${SERVICE_NAME}, wifi_user_start($1)"
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	USER_VAP=`syscfg_get "$SYSCFG_INDEX"_user_vap`
	wait_till_end_state ${WIFI_USER}_${PHY_IF}
	ulog wlan status "${SERVICE_NAME}, wifi_user_start($PHY_IF)"
	USER_STATE=`syscfg_get ${SYSCFG_INDEX}_state`
	if [ "$USER_STATE" = "down" ]; then
		return 1
	fi
	STATUS=`sysevent get ${WIFI_USER}_${PHY_IF}-status`
	if [ "started" = "$STATUS" ] ; then
		echo "${SERVICE_NAME}, ${WIFI_USER} is already starting/started, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} is already starting/started, ignore the request"
		return 1
	fi
	sysevent set ${WIFI_USER}_${PHY_IF}-status starting
	user_start $PHY_IF
	RET_CODE=$?
	Is_USER_VAP_STATE=`ifconfig $USER_VAP | grep "UP" | awk {'print $1'}`
        if [ "$Is_USER_VAP_STATE" != "UP" ] ; then
          ifconfig $USER_VAP up
        else
          iwconfig $USER_VAP commit
        fi
	sysevent set ${SYSCFG_INDEX}_status "up"
	ulog wlan status "${SERVICE_NAME}, primary AP: $USER_VAP is up"
	echo "${SERVICE_NAME}, primary AP: $USER_VAP is up"		
	
	sysevent set ${WIFI_USER}_${PHY_IF}-status started
	return $RET_CODE
}
wifi_user_stop()
{
	echo "${SERVICE_NAME}, wifi_user_stop($1)"
	PHY_IF=$1
	if [ -z "$PHY_IF" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} ERROR: invalid interface name, ignore the request"
		return 1
	fi
	wait_till_end_state ${WIFI_USER}_${PHY_IF}
	ulog wlan status "${SERVICE_NAME}, wifi_user_stop($PHY_IF)"
	STATUS=`sysevent get ${WIFI_USER}_${PHY_IF}-status`
	if [ "stopped" = "$STATUS" ] || [ -z "$STATUS" ]; then
		echo "${SERVICE_NAME}, ${WIFI_USER} is already stopping/stopped, ignore the request"
		ulog wlan status "${SERVICE_NAME}, ${WIFI_USER} is already stopping/stopped, ignore the request"
		return 1
	fi
	sysevent set ${WIFI_USER}_${PHY_IF}-status stopping
	user_stop $PHY_IF
	sysevent set ${WIFI_USER}_${PHY_IF}-status stopped
	return 0
}
wifi_user_restart()
{
	PHY_IF=$1
	echo "${SERVICE_NAME}, wifi_user_restart($PHY_IF)"
	wifi_user_stop $PHY_IF
	wifi_user_start $PHY_IF
	return 0
}
user_start()
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	VIR_IF=`syscfg_get "$SYSCFG_INDEX"_user_vap`
	
	if [ "true" = "$RECONFIGURE" ]; then
		return 1
	fi
	REPEATER_DISABLED=`syscfg_get repeater_disabled`
	if [ ! -z "$REPEATER_DISABLED" ] && [ "1" = "$REPEATER_DISABLED" ]; then
		syscfg_set ${SYSCFG_INDEX}_state "down"
		return 1
	fi
	LAN_IFNAME=`syscfg_get lan_ifname`
	add_interface_to_bridge $VIR_IF $LAN_IFNAME
	set_wps_state ${SYSCFG_INDEX}
	VIR_SSID=`syscfg_get "$SYSCFG_INDEX"_ssid`
	iwconfig $VIR_IF essid $VIR_SSID
	SEC_MODE=`get_security_mode "$SYSCFG_INDEX"_security_mode`
	USE_HOSTAPD=`syscfg_get wl_use_hostapd`
	if [ "1" = "$USE_HOSTAPD" ] && [ "8" != "$SEC_MODE" ]; then
		configure_hostapd $PHY_IF $VIR_IF
		ret=$?
	else
		configure_user $PHY_IF $VIR_IF
		ret=$?
	fi
	unsecure_page
	
	RET_CODE="0"
	if [ "true" = "$RECONFIGURE" ]; then
		ulog wlan status "$VIR_IF is preparing to reconfigure due to incompatible mode"
		RET_CODE="2"
	else
		RET_CODE="0"
	fi
	return $RET_CODE
}
user_stop() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	VIR_IF=`syscfg_get "$SYSCFG_INDEX"_user_vap`
	if [ -z "$VIR_IF" ]; then
		return 1
	fi
	set_driver_mac_filter_disabled $VIR_IF
	ulog wlan status "Explicily ifconfig $VIR_IF down"
	LAN_IFNAME=`syscfg_get lan_ifname`
	delete_interface_from_bridge $VIR_IF $LAN_IFNAME
	ifconfig $VIR_IF down
	sysevent set ${SYSCFG_INDEX}_status "down"
	ulog wlan status "${SERVICE_NAME}, primary AP: $VIR_IF is down"
	echo "${SERVICE_NAME}, primary AP: $VIR_IF is down"		
	return 0
}
configure_user() 
{
	PHY_IF=$1
	VIR_IF=$2
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	USER_SSID=`syscfg_get $SYSCFG_INDEX"_ssid"`
	SEC_MODE=`get_security_mode "$SYSCFG_INDEX"_security_mode`
	
	if [ -z $USER_SSID ]; then 
		ulog wlan status "User VAP ssid  $SYSCFG_INDEX is empty"
		return 1
	fi
	if [ "0" = $SEC_MODE ]; then
		USER_PASSPHRASE=""
	else
		USER_PASSPHRASE=`syscfg_get $SYSCFG_INDEX"_passphrase"`
	fi
	driver_update_extra_virtual_settings $PHY_IF $VIR_IF
	set_driver_mac_filter_enabled $VIR_IF
	iwconfig $VIR_IF essid "$USER_SSID"
	RET=0
	case "$SEC_MODE" in
	"0")
		iwpriv $VIR_IF wpawpa2mode 0
		syscfg_set $SYSCFG_INDEX"_encryption" ""
		;;
	"1")
		iwpriv $VIR_IF wpawpa2mode `expr $SEC_MODE`
		iwpriv $VIR_IF passphrase "wpa $USER_PASSPHRASE"
		syscfg_set $SYSCFG_INDEX"_encryption" "tkip"
		;;
	"2")
		iwpriv $VIR_IF wpawpa2mode `expr $SEC_MODE`
		iwpriv $VIR_IF passphrase "wpa2 $USER_PASSPHRASE"
		syscfg_set $SYSCFG_INDEX"_encryption" "aes"
		;;
	"3")
		iwpriv $VIR_IF wpawpa2mode `expr $SEC_MODE`
		iwpriv $VIR_IF passphrase "wpa $USER_PASSPHRASE"
		iwpriv $VIR_IF passphrase "wpa2 $USER_PASSPHRASE"
		syscfg_set $SYSCFG_INDEX"_encryption" "tkip+aes"
		;;
	"8")
		ENC_TYPE=`get_encryption ${SYSCFG_INDEX}"_encryption"` #64-bits or 128-bits
		if [ "$ENC_TYPE" = "64-bits" ] || [ "$ENC_TYPE" = "128-bits" ]; then
			WEP_KEY_1=`syscfg_get "$SYSCFG_INDEX"_key_0`
			WEP_KEY_2=`syscfg_get "$SYSCFG_INDEX"_key_1`
			WEP_KEY_3=`syscfg_get "$SYSCFG_INDEX"_key_2`
			WEP_KEY_4=`syscfg_get "$SYSCFG_INDEX"_key_3`
			TX_KEY=`syscfg_get "$SYSCFG_INDEX"_tx_key`
			if [ -z "$TX_KEY" ] || [ "0" = "$TX_KEY" ]; then
				TX_KEY=1 #Default if user for get to set
			fi
			AUTH_TYPE=`syscfg_get "$SYSCFG_INDEX"_authentication_type`
			MRVL_AUTH_TYPE=""
			if [ "open" = "$AUTH_TYPE" ]; then
				MRVL_AUTH_TYPE="open"
			else
				MRVL_AUTH_TYPE="restricted"
			fi
			KL_1=`echo $WEP_KEY_1 | wc -c`
			KL_2=`echo $WEP_KEY_2 | wc -c`
			KL_3=`echo $WEP_KEY_3 | wc -c`
			KL_4=`echo $WEP_KEY_4 | wc -c`
			if [ "$ENC_TYPE" = "64-bits" ]; then
				if [ 11 = `expr $KL_1` ]; then
					iwconfig $VIR_IF key $WEP_KEY_1 [1]
				elif [ 6 = `expr $KL_1` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_1 [1]
				fi
				if [ 11 = `expr $KL_2` ]; then
					iwconfig $VIR_IF key $WEP_KEY_2 [2]
				elif [ 6 = `expr $KL_2` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_2 [2]
				fi
				if [ 11 = `expr $KL_3` ]; then
					iwconfig $VIR_IF key $WEP_KEY_3 [3]
				elif [ 6 = `expr $KL_3` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_3 [3]
				fi
				if [ 11 = `expr $KL_4` ]; then
					iwconfig $VIR_IF key $WEP_KEY_4 [4]
				elif [ 6 = `expr $KL_4` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_4 [4]
				fi
				iwconfig $VIR_IF key [`expr $TX_KEY`] "$MRVL_AUTH_TYPE"
				syscfg_set $SYSCFG_INDEX"_encryption" "64-bits"
			elif [ "$ENC_TYPE" = "128-bits" ]; then
				if [ 27 = `expr $KL_1` ]; then
					iwconfig $VIR_IF key $WEP_KEY_1 [1]
				elif [ 14 = `expr $KL_1` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_1 [1]
				fi
				if [ 27 = `expr $KL_2` ]; then
					iwconfig $VIR_IF key $WEP_KEY_2 [2]
				elif [ 14 = `expr $KL_2` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_2 [2]
				fi
				if [ 27 = `expr $KL_3` ]; then
					iwconfig $VIR_IF key $WEP_KEY_3 [3]
				elif [ 14 = `expr $KL_3` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_3 [3]
				fi
				if [ 27 = `expr $KL_4` ]; then
					iwconfig $VIR_IF key $WEP_KEY_4 [4]
				elif [ 14 = `expr $KL_4` ]; then
					iwconfig $VIR_IF key "s:"$WEP_KEY_4 [4]
				fi
				iwconfig $VIR_IF key [`expr $TX_KEY`] "$MRVL_AUTH_TYPE"
				syscfg_set $SYSCFG_INDEX"_encryption" "128-bits"
			else
				RET=1
			fi
		else
			RET=1
		fi
		;;
	*)
		ulog wlan status "Security mode on  $VIR_IF is invalid"
		RET=1
	esac
	return $RET
}
configure_hostapd() 
{
	PHY_IF=$1
	VIR_IF=$2
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	USER_SSID=`syscfg_get $SYSCFG_INDEX"_ssid"`
	SEC_MODE=`get_security_mode "$SYSCFG_INDEX"_security_mode`
	USER_PASSPHRASE=""
	RADIUS_SERVER=""
	RADIUS_PORT=""
	RADIUS_SHARED=""
	ENC_MODE=""
	TX_KEY=""
	if [ -z "$USER_SSID" ]; then
		USER_SSID=`syscfg_get hostname`
	fi
	if [ "1" = $SEC_MODE ] || [ "2" = $SEC_MODE ] || [ "3" = $SEC_MODE ]; then
		WL_PASSPHRASE=`syscfg_get "$SYSCFG_INDEX"_passphrase`
		if [ ${#WL_PASSPHRASE} = 64 ]; then 
			USER_PASSPHRASE="wpa_psk=${WL_PASSPHRASE}"
		else
			USER_PASSPHRASE="wpa_passphrase=${WL_PASSPHRASE}"
		fi
	elif [ "4" = $SEC_MODE ] || [ "5" = $SEC_MODE ] || [ "6" = $SEC_MODE ]; then
		RADIUS_SERVER=`syscfg_get $SYSCFG_INDEX"_radius_server"`
		RADIUS_PORT=`syscfg_get $SYSCFG_INDEX"_radius_port"`
		RADIUS_SHARED=`syscfg_get $SYSCFG_INDEX"_shared"`
	elif [ "7" = $SEC_MODE ]; then
		RADIUS_SERVER=`syscfg_get $SYSCFG_INDEX"_radius_server"`
		RADIUS_PORT=`syscfg_get $SYSCFG_INDEX"_radius_port"`
		RADIUS_SHARED=`syscfg_get $SYSCFG_INDEX"_shared"`
	elif [ "8" = $SEC_MODE ]; then
		ENC_MODE=`syscfg_get $SYSCFG_INDEX"_encryption"`
		USER_PASSPHRASE=`syscfg_get $SYSCFG_INDEX"_passphrase"`
		TX_KEY=`syscfg_get $SYSCFG_INDEX"_tx_key"`
	fi
	if [ "1" = $SEC_MODE ] || [ "4" = $SEC_MODE ]; then
		syscfg_set $SYSCFG_INDEX"_encryption" "tkip"
	elif [ "2" = $SEC_MODE ] || [ "5" = $SEC_MODE ]; then
		syscfg_set $SYSCFG_INDEX"_encryption" "aes"
	elif [ "3" = $SEC_MODE ] || [ "6" = $SEC_MODE ]; then
		syscfg_set $SYSCFG_INDEX"_encryption" "tkip+aes"
	fi
	ulog wlan status "Bring up hostapd for $VIR_IF"
	USER_ENCRYPTION=`get_encryption $SYSCFG_INDEX"_encryption"`
	HOSTAPD_CONF="/tmp/hostapd-$VIR_IF.conf"
	wps_state=`syscfg_get "$SYSCFG_INDEX"_wps_state`
	if [ "configured" = "$wps_state" ]; then
		WPS_STATE=2
	elif [ "disabled" = "$wps_state" ]; then
		WPS_STATE=0
	else
		WPS_STATE=1
	fi
	if [ "4" = "$SEC_MODE" ] || [ "5" = "$SEC_MODE" ] || [ "6" = "$SEC_MODE" ]; then
		generate_hostapd_config_enterprise $VIR_IF "$USER_SSID" $SEC_MODE "$RADIUS_SERVER" "$RADIUS_PORT" "$RADIUS_SHARED"> $HOSTAPD_CONF
	else
		generate_hostapd_config $VIR_IF "$USER_SSID" "$USER_PASSPHRASE" $SEC_MODE "$USER_ENCRYPTION" "$RADIUS_SERVER" "$RADIUS_PORT" "$RADIUS_SHARED"> $HOSTAPD_CONF
	fi
	generate_hostapd_wps_section $SYSCFG_INDEX >> $HOSTAPD_CONF
	driver_update_extra_virtual_settings $PHY_IF $VIR_IF
	set_driver_mac_filter_enabled $VIR_IF
	return 0
}
generate_hostapd_config() 
{
    interface=$1
    ssid="$2"
    passphrase=$3
    wpa=$4
    auth_server_addr=""
    auth_server_port=""
    auth_server_shared_secret=""
    get_wl_index $interface
    CURRENT_INDEX=$?
    wl_index=wl$CURRENT_INDEX
    hw_mode=""
    eap_server=1
    WIFI_FREQ="2.4GHz and 5GHz"
    wpa2_pairwise="wpa_pairwise=CCMP"
    wpa_pairwise="wpa_pairwise=TKIP"
    wpa_mixed_pairwise="$wpa_pairwise"$'\n'"rsn_pairwise=CCMP"
    wpa_key_mgmt="wpa_key_mgmt=WPA-PSK"
    if [ "0" != "$wpa" ]; then
    	wpa_group_rekey="wpa_group_rekey=`syscfg_get $wl_index"_key_renewal"`"
    fi
    PMF=""
    if [ "enabled" = "`syscfg_get "$wl_index"_pmf`" ]; then
	PMF="ieee80211w=1"
    else
	PMF="ieee80211w=0"
    fi
    if [ "$wpa" = "0" ]; then
        security="wpa=0"
        ieee8021x_set=0  
    else
        if [ "$wpa" = "1" ]; then
            security="wpa=1"$'\n'"$wpa_pairwise"
        elif [ "$wpa" = "2" ]; then
            security="wpa=2"$'\n'"$wpa2_pairwise"
        else
            security="wpa=3"$'\n'"$wpa_mixed_pairwise"
        fi
        ieee8021x_set=0
        security="$security"$'\n'"$passphrase"$'\n'"$wpa_key_mgmt"$'\n'"$wpa_group_rekey"
    fi
	
    if [ "0" = "$CURRENT_INDEX" ]; then
		hw_mode="hw_mode=g"
    elif [ "1" = "$CURRENT_INDEX" ]; then
		hw_mode="hw_mode=a"
    else 
		hw_mode=""
    fi
    cat <<EOF
interface=$interface
bridge=br0
driver=marvell
logger_syslog=121
logger_syslog_level=3
logger_stdout=0
logger_stdout_level=4
dump_file=/tmp/hostapd.dump
ctrl_interface=/var/run/hostapd
ctrl_interface_group=0
ssid=$ssid
$hw_mode
macaddr_acl=0
auth_algs=3
ieee8021x=$ieee8021x_set
eapol_key_index_workaround=0
eap_server=$eap_server
$own_ip_addr
$auth_server_addr
$auth_server_port
$auth_server_shared_secret
$PMF
$security
EOF
}
generate_hostapd_wps_section() 
{
	UUID=`syscfg_get device::uuid`
	uuid="uuid=$UUID"
	upnp="upnp_iface=br0"
	AP_PIN=`syscfg_get device::wps_pin`
	SN=`syscfg_get device::serial_number`
	MODEL_BASE=`syscfg_get device::model_base`
        MODEL_DESC=`syscfg_get device::modelDescription`
        MANUFACTOURER=`syscfg_get device::manufacturer`
	REAL_DEVICE_NAME=`syscfg_get hostname`
	DEVICE_NAME_LEN=`echo "$REAL_DEVICE_NAME" | wc -c`
	if [ `expr $DEVICE_NAME_LEN` -gt 32 ]; then
		QN_DEVICE_NAME=`echo "$REAL_DEVICE_NAME" | cut -c1-32`
	else
		QN_DEVICE_NAME="$REAL_DEVICE_NAME"
	fi
	cat <<EOF
wps_state=$WPS_STATE
ap_setup_locked=0
wps_pin_requests=/var/run/hostapd_wps_pin_requests
device_name=$QN_DEVICE_NAME
manufacturer=$MANUFACTOURER
$uuid
model_name=$MODEL_BASE
model_number=$MODEL_BASE
device_type=6-0050F204-1
serial_number=$SN
config_methods=label display push_button virtual_display virtual_push_button physical_push_button
ap_pin=$AP_PIN
$upnp
friendly_name=$REAL_DEVICE_NAME
model_description=$MODEL_DESC
EOF
}
generate_hostapd_config_enterprise() 
{
	interface=$1
	ssid=$2
	wpa=$3
	get_wl_index $interface
	CURRENT_INDEX=$?
	wl_index=wl$CURRENT_INDEX
	ieee8021x_set=1
	eap_server=1
	own_ip_addr="own_ip_addr=`syscfg_get lan_ipaddr`"
	auth_server_addr="auth_server_addr=$4"
	auth_server_port="auth_server_port=$5"
	auth_server_shared_secret="auth_server_shared_secret=$6"
	wpa2_pairwise="wpa_pairwise=CCMP"
	wpa_pairwise="wpa_pairwise=TKIP"
	wpa_mixed_pairwise="$wpa_pairwise"$'\n'"rsn_pairwise=CCMP"
	wpa_key_mgmt="wpa_key_mgmt=WPA-EAP"
	wpa_group_rekey="wpa_group_rekey=`syscfg_get $wl_index"_key_renewal"`"
	PMF=""
	if [ "blk-mamba" != "`cat /etc/product`" ]; then
		if [ "enabled" = "`syscfg_get "$wl_index"_pmf`" ]; then
			PMF="ieee80211w=1"
		else
			PMF="ieee80211w=0"
		fi
	fi
	if [ "$wpa" = "4" ]; then
		security="wpa=1"$'\n'"$wpa_pairwise"
	elif [ "$wpa" = "5" ]; then
		security="wpa=2"$'\n'"$wpa2_pairwise"
	else
		security="wpa=3"$'\n'"$wpa_mixed_pairwise"
	fi
	security="$security"$'\n'"$passphrase"$'\n'"$wpa_key_mgmt"$'\n'"$wpa_group_rekey"
    cat <<EOF
interface=$interface
bridge=br0
driver=marvell
logger_syslog=121
logger_syslog_level=3
logger_stdout=0
logger_stdout_level=4
dump_file=/tmp/hostapd.dump
ssid=$ssid
ieee8021x=$ieee8021x_set
eapol_key_index_workaround=0
$own_ip_addr
$auth_server_addr
$auth_server_port
$auth_server_shared_secret
$PMF
$security
EOF
}
set_wps_state() 
{
	SYSCFG_INDEX=$1
	wps_state="unconfigured"
	sec_mode=`get_security_mode "$SYSCFG_INDEX"_security_mode`
	ssid_broadcast=`get_ssid_broadcast $SYSCFG_INDEX`
	if [ 1 = $sec_mode ] || [ 4 = $sec_mode ] ||  
	   [ 5 = $sec_mode ] || [ 6 = $sec_mode ] ||
	   [ 7 = $sec_mode ] || [ 8 = $sec_mode ] ||
	   [ 0 = $ssid_broadcast ]; then
		wps_state="disabled"
	fi
	if [ "disabled" != "$wps_state" ]; then
		ssid=`syscfg_get ${SYSCFG_INDEX}_ssid`
		
		if [ "$ssid" != "`syscfg get ${SYSCFG_INDEX}_default_ssid`" ] && [ "$ssid" != "`syscfg get wl_default_ssid`" ] ; then
			wps_state="configured"
		else
			if [ 0 != $sec_mode ]; then
				wps_state="configured"
			fi
		fi
	fi	
	WL_MACFILTER_ENABLED=`syscfg_get wl_access_restriction`
	if [ "$WL_MACFILTER_ENABLED" = "allow" ] || [ "$WL_MACFILTER_ENABLED" = "deny" ]; then
		wps_state="disabled"
	fi
	WPS_USER_SETTING=`syscfg_get wps_user_setting`
	if [ "disabled" = "$WPS_USER_SETTING" ]; then
		wps_state="disabled"
	fi
	sys_wps_state=`syscfg_get ${SYSCFG_INDEX}_wps_state`
	if [ "$sys_wps_state" != "$wps_state" ]; then
		syscfg_set ${SYSCFG_INDEX}_wps_state $wps_state
	fi
	sysevent set ${SYSCFG_INDEX}_wps_status $wps_state
}
driver_update_extra_virtual_settings() 
{
    PHY_IF=$1
	VIR_IF=$2
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	iwconfig $VIR_IF key off
	SSID_BROADCAST=`get_ssid_broadcast $SYSCFG_INDEX`
	if [ "1" = $SSID_BROADCAST ]; then
		iwpriv $VIR_IF hidessid 0
	else
		iwpriv $VIR_IF hidessid 1
	fi
	SEC_ENABLED="false"
	LOCAL_SEC_MODE=`syscfg_get $SYSCFG_INDEX"_security_mode"`
	if [ "wpa-personal" = "$LOCAL_SEC_MODE" ] || [ "wpa2-personal" = "$LOCAL_SEC_MODE" ] || [ "wpa-mixed" = "$LOCAL_SEC_MODE" ]; then
		SEC_ENABLED="true"
	fi
	 	
	if [ "true" = "$SEC_ENABLED" ]; then
		USE_HOSTAPD=`syscfg_get wl_use_hostapd`
		if [ "1" != "$USE_HOSTAPD" ]; then
			REKEY_TIME=`syscfg_get $SYSCFG_INDEX"_key_renewal"`
			iwpriv $VIR_IF grouprekey `expr $REKEY_TIME`
		fi
	fi
	AP_ISOLATION=`syscfg_get $SYSCFG_INDEX"_ap_isolation"`
	if [ "enabled" = "$AP_ISOLATION" ]; then
		INTRABSS=0
	else
		INTRABSS=1
	fi
	iwpriv $VIR_IF intrabss $INTRABSS
	DTIM_INTERVAL=`syscfg_get $SYSCFG_INDEX"_dtim_interval"`
	if [ -z "$DTIM_INTERVAL" ] || [ $DTIM_INTERVAL -lt 1 ] || [ $DTIM_INTERVAL -gt 255 ]; then
		ulog wlan status "invalid wifi dtim_interval $DTIM_INTERVAL"
		DTIM_INTERVAL=1
	fi
	iwpriv $VIR_IF dtim $DTIM_INTERVAL
	iwpriv $VIR_IF ampdutx 1
	AMSDU_SETTING=`syscfg_get $SYSCFG_INDEX"_amsdu_enabled"`
	iwpriv $VIR_IF amsdu $AMSDU_SETTING
	iwpriv $VIR_IF mcastproxy 1
	return 0
}
