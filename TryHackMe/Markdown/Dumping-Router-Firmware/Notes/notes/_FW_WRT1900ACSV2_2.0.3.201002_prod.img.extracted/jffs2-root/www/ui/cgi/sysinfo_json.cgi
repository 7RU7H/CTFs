#!/bin/sh

########################################################
# siq.sh ----> /www/siq.cgi
#
# BASIC JSON TEMPLATE BELOW 
#
# {
#  "title": "SectionName", 
#  "description": "This contains the SectionName settings", 
#  "data": [
#  {
#  "key1": "value1", 
#  "key2": "value2", 
#  "key3": "value3"
#  }
#  ]
# }
########################################################
# Sections we need for Vamsis design
#  CFE/Devinfo 
#  Manufacturer Data
#  Boot Data
#  Syscfg *
#  Sysevent *
#  /var/log/messages *
#  dmesg *
#  ps *
#  Disk Use *
#  Memory Use *
#  CPU information *
#  wifi information
#  ip information *
#  usb info
#  ping *
#
#########################################################

# get a cgi value - ( I ALWAYS use this function in shell cgi scripts )
#
# use: $(get_cgi_val "DATA")
# it will get the value of the DATA cgi varaible
get_cgi_val () {
  if [ "$1" == "" ] ; then
    echo ""
    return
  fi
  form_var="$1"
  var_value=`echo "$QUERY_STRING" | sed -n "s/^.*$form_var=\([^&]*\).*$/\1/p" | sed "s/%20/ /g" | sed "s/+/ /g" | sed "s/%2F/\//g"`
  echo -n "$var_value"
}

# jsonify_command
#
# use: jsonify_command "bootloader_info" "/www/bootloader_info.cgi"
# or
#      jsonify_command "bootloader_info" "/www/bootloader_info.cgi" "This is information from the bootloader"
# it will take the command and output a JSON object of it and it's output
jsonify_command() {
	name="$1"
	command="$2"
 	cmd_output=`$command | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'  | tr -d '"'`
	echo "var $name = { "
	echo " \"title\": \"$name\","
	if [ "$3" ] ; then
		echo " \"description\": \"$3\","
	else
		echo " \"description\": \"output from command $command\","
	fi
	echo " \"data\": ["
	echo "{"	
	echo " \"command\": \"$command\","
	echo " \"output\": \"$cmd_output\""
	echo " }"
	echo " ]"
	echo "};"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $name"
}


# use: dataify_command "wifi_debug_show_info" "sh /etc/init.d/service_wifi/wifi_debug_show_info.sh"
# it ill take the command and create a JSON DATA entry for a larger object
dataify_command() {
	key_name="$1"
	command="$2"
 	cmd_output=`$command | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r' | tr -d '"'`
 	cmd_output="`echo $cmd_output | tr -d '\n'`"
	echo " \"$key_name\": \"$cmd_output\""
}

# see if there is a section, or sections that the caller wants to see, and store 
# that informaton ( This is probably really not safe, but at this point, we've 
# already done a few shell evals as root. lighttpd runs as root!, there is not 
# much else we can do. and we're sure not going to sanitize cgi vars in the root
# shell, so lets just process this mofo and hope that nothing is maliscious ).
SECTION=$(get_cgi_val "section")

if [ "$SECTION" == "" ] ; then
	SECTION=$@
fi

if [ "$SECTION" == "" ] ; then
	# view all sections
	ALL_SET="1"
else
	# view only certain sections
	ALL_SET="0"
	ORIG_SECTION="$SECTION"
fi

JS_SECTIONS_VAR=""


# get the wan interface names from sysevent
wan_ifname=`sysevent get wan_ifname`

echo "var SiqSectionNames = \"BasicInfo,FirmwareInfo,BootData,MfgData,Syscfg,Sysevent,DebugInfo,Logs,Messages,Dmesg,Ps,DiskInfo,MemoryInfo,CpuInfo,SystemInfo,WifiInfo,UsbInfo,IPInfo,IPNetInfo,PingInfo,Conntrack\";"


# display the sections ( in JSON ) that the caller wants to see
if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'BasicInfo'`" ] ; then
	SECTION="BasicInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Basic Information\","
	echo " \"description\": \"This is some basic information about the unit, and this syscfg page\","
	echo " \"data\": ["
	echo " {"
	echo "  \"date\": \"`date`\"",
	echo "  \"uptime\": \"`uptime`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'FirmwareInfo'`" ] ; then
	SECTION="FirmwareInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Firmware Information\","
	echo " \"description\": \"This is some basic information about firmware\","
	echo " \"data\": ["
	echo "{"
	echo " \"FirmwareVersion\": \"`cat /etc/version`\","
	echo " \"FirmwareBuilddate\": \"`cat /etc/builddate`\","
	echo " \"ProductType\": \"`cat /etc/product.type`\","
	echo " \"LinuxVersion\": \"`cat /proc/version`\","
	echo " \"Board\": \"`cat /proc/bdutil/boardid`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

# TODO: port to JSON - mfmfmf???	
#	if [ -f bootloader_info.cgi ] ; then
#		./bootloader_info.cgi
#	fi
if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'BootData'`" ] ; then
	SECTION="BootData"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Boot Data\","
	echo " \"description\": \"This data is used to boot linux\","
	echo " \"data\": ["
	echo "{"
	echo " \"proc_cmdline\": \"`cat /proc/cmdline`\","
	echo " \"proc_mtd\": \"`cat /proc/mtd | sed 's/$/\\\n/g' | tr -d '"' | tr -d '\n'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'MfgData'`" ] ; then
	SECTION="MfgData"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Manufacturer Data\","
	echo " \"description\": \"This is used to manufacturer unit and in SKU API\","
	echo " \"data\": ["
	echo "{"
	echo " \"model_sku\": \"`/usr/sbin/skuapi -g model_sku`\","
	echo " \"hw_version\": \"`/usr/sbin/skuapi -g hw_version`\","
	echo " \"hw_mac_addr\": \"`/usr/sbin/skuapi -g hw_mac_addr`\","
	echo " \"date\": \"`/usr/sbin/skuapi -g date`\","
	echo " \"serial_number\": \"`/usr/sbin/skuapi -g serial_number`\","
	echo " \"uuid\": \"`/usr/sbin/skuapi -g uuid`\","
	echo " \"wps_pin\": \"`/usr/sbin/skuapi -g wps_pin | sed 's/= .*/= \*\*\*\*\*/g'`\","
	syscfgdev=`syscfg show | grep device::`
	for i in "$syscfgdev"; do
		item=$(echo "$i" | sed "s/::/_/g" | sed 's/=/": "/g' | sed 's/^/"/g' | sed 's/$/",/g')
		# For security purposes, redact the values of variables that contain sensitive information
		echo "$item" | sed 's/^\("\(.*device_recovery_key\|device_wps_pin\|device_admin_password\|device_default_passphrase\)":\).*/\1 "\*\*\*\*\*\*",/g'
	done
	echo " \"eeprom_version\": \"`/usr/sbin/skuapi -g eeprom_version`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Syscfg'`" ] ; then
	SECTION="Syscfg"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Syscfg variables\","
	echo " \"description\": \"Some values from syscfg\","
	echo " \"data\": ["
	echo "{"
	echo " \"ui_remote_disabled\": \"`syscfg get ui remote_disabled`\","
	echo " \"ui_remote_stunnel\": \"`syscfg get ui remote_stunnel`\","
	echo " \"ui_remote_host\": \"`syscfg get ui remote_host`\","
	echo " \"ui_remote_port\": \"`syscfg get ui remote_port`\","
	echo " \"ui_remote_stunnel_verify\": \"`syscfg get ui remote_stunnel_verify`\","
	echo " \"cloud_stunnel\": \"`syscfg get cloud stunnel`\","
	echo " \"cloud_host\": \"`syscfg get cloud host`\","
	echo " \"cloud_port\": \"`syscfg get cloud port`\","
	echo " \"cloud_stunnel_verify\": \"`syscfg get cloud stunnel_verify`\","
	echo " \"mgmt_http_enable\": \"`syscfg get mgmt_http_enable`\","
	echo " \"mgmt_https_enable\": \"`syscfg get ui mgmt_https_enable`\","
	echo " \"mgmt_wifi_access\": \"`syscfg get ui mgmt_wifi_access`\","
	echo " \"xmpp_enabled\": \"`syscfg get ui xmpp_enabled`\","
	echo " \"owned_network_id\": \"`syscfg get owned_network_id`\","
	echo " \"wan_auto_detect_enable\": \"`syscfg get wan_auto_detect_enable`\","
	echo " \"wan_proto\": \"`syscfg get wan_proto`\","
	echo " \"bridge_mode\": \"`syscfg get bridge_mode`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Sysevent'`" ] ; then
	SECTION="Sysevent"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Sysevent variables\","
	echo " \"description\": \"Some values from sysevent\","
	echo " \"data\": ["
	echo "{"
	echo " \"xrac_status\": \"`sysevent get xrac-status`\","
	echo " \"phylink_wan_state\": \"`sysevent get phylink_wan_state`\","
	echo " \"wan_status\": \"`sysevent get wan-status`\","
	echo " \"ipv4_wan_ipaddr\": \"`sysevent get ipv4_wan_ipaddr`\","
	echo " \"current_ipv6_wan_state\": \"`sysevent get current_ipv6_wan_state`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'DebugInfo'`" ] ; then
	SECTION="DebugInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Debug Information\","
	echo " \"description\": \"Information Helpful for debugging\","
	echo " \"data\": ["
	echo "{"
	echo " \"ui_remote_disabled\": \"`syscfg get ui remote_disabled`\","
	echo " \"ui_remote_stunnel\": \"`syscfg get ui remote_stunnel`\","
	echo " \"ui_remote_host\": \"`syscfg get ui remote_host`\","
	echo " \"ui_remote_port\": \"`syscfg get ui remote_port`\","
	echo " \"ui_remote_stunnel_verify\": \"`syscfg get ui remote_stunnel_verify`\","
	echo " \"cloud_stunnel\": \"`syscfg get cloud stunnel`\","
	echo " \"cloud_host\": \"`syscfg get cloud host`\","
	echo " \"cloud_port\": \"`syscfg get cloud port`\","
	echo " \"cloud_stunnel_verify\": \"`syscfg get cloud stunnel_verify`\","
	echo " \"mgmt_http_enable\": \"`syscfg get mgmt_http_enable`\","
	echo " \"mgmt_https_enable\": \"`syscfg get ui mgmt_https_enable`\","
	echo " \"mgmt_wifi_access\": \"`syscfg get ui mgmt_wifi_access`\","
	echo " \"xmpp_enabled\": \"`syscfg get ui xmpp_enabled`\","
	echo " \"xrac_status\": \"`sysevent get xrac-status`\","
	echo " \"owned_network_id\": \"`syscfg get owned_network_id`\","
	echo " \"phylink_wan_state\": \"`sysevent get phylink_wan_state`\","
	echo " \"wan_auto_detect_enable\": \"`syscfg get wan_auto_detect_enable`\","
	echo " \"wan_proto\": \"`syscfg get wan_proto`\","
	echo " \"bridge_mode\": \"`syscfg get bridge_mode`\","
	echo " \"wan_status\": \"`sysevent get wan-status`\","
	echo " \"ipv4_wan_ipaddr\": \"`sysevent get ipv4_wan_ipaddr`\","
	echo " \"current_ipv6_wan_state\": \"`sysevent get current_ipv6_wan_state`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Logs'`" ] ; then
	SECTION="Logs"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"logs\","
	echo " \"description\": \"Log output\","
	echo " \"data\": ["
	echo "{"
	#  there is some script-fu stuff going on below.  suffice to say, I'm just 
	#  replacing the newlines with '\n' and removing double quotes that might mess with my shell-foo
	#  sed and tr will do the job nicely
	echo " \"var_log_messages\": \"`tail -n 200 /var/log/messages | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r' | tr -d '"'`\","
	if [ -f "/var/log/ipv6.log" ] ; then
		echo " \"var_log_ipv6_log\": \"`cat /var/log/ipv6.log | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r' | tr -d '"'`\","
	else
		echo "\"var_log_ipv6_log\": \"\","
	fi
	echo " \"dmesg\": \"`dmesg | tail -200  | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r' | tr -d '"'`\""
	echo ""
	# TODO: JSONIFY SCRIPT mfmfmf???
	#if [ -f get_counter_info.cgi ]; then
	#	./get_counter_info.cgi
	#fi
	
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Messages'`" ] ; then
	SECTION="Messages"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"messages\","
	echo " \"description\": \"last 200 lines from /var/log/messages log file\","
	echo " \"data\": ["
	echo "{"
	#  there is some script-fu stuff going on below.  suffice to say, I'm just 
	#  replacing the newlines with '\n' and removing double quotes that might mess with my shell-foo
	#  sed and tr will do the job nicely
	echo " \"var_log_messages\": \"`tail -n 200 /var/log/messages | sed 's/$/\\\n/g' | tr -d '\n'  | tr -d '\r' | tr -d '"'`\""	
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Dmesg'`" ] ; then
	SECTION="Dmesg"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"dmesg\","
	echo " \"description\": \"The last 200 lines from dmesg\","
	echo " \"data\": ["
	echo "{"
	echo " \"dmesg\": \"`dmesg | tail -200  | sed 's/$/\\\n/g' | tr -d '\n'  | tr -d '\r' | tr -d '"'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Ps'`" ] ; then
	SECTION="Ps"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"ps\","
	echo " \"description\": \"ps command to show process usage\","
	echo " \"data\": ["
	echo "{"
	echo " \"ps\": \"`ps | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'DiskInfo'`" ] ; then
	SECTION="DiskInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"disk usage\","
	echo " \"description\": \"a breakdwn of the disk usage\","
	echo " \"data\": ["
	echo "{"
	echo " \"disk_use\": \"`df | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'MemoryInfo'`" ] ; then
	SECTION="MemoryInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"memory usage\","
	echo " \"description\": \"a snapshot of the memory usage\","
	echo " \"data\": ["
	echo "{"
	echo " \"memory_use\": \"`free | sed 's/$/\\\n/g' | tr -d '\n'`\","
	echo " \"proc_locks\": \"`cat /proc/locks | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_modules\": \"`cat /proc/modules | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_slabinfo\": \"`cat /proc/slabinfo | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_vmstat\": \"`cat /proc/vmstat | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'CpuInfo'`" ] ; then
	SECTION="CpuInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"CPU Information\","
	echo " \"description\": \"Information about the CPU\","
	echo " \"data\": ["
	echo "{"
	echo " \"proc_stat\": \"`cat /proc/stat | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"top_bn1\": \"`top -bn1 | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""	
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'SystemInfo'`" ] ; then
	SECTION="SystemInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"system\","
	echo " \"description\": \"System Information\","
	echo " \"data\": ["
	echo "{"
	# echo "section=system"
	# system running processes and uptime information
	echo " \"ps\": \"`ps | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"disk_use\": \"`df | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"memory_use\": \"`free | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_locks\": \"`cat /proc/locks | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_modules\": \"`cat /proc/modules | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_slabinfo\": \"`cat /proc/slabinfo | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_vmstat\": \"`cat /proc/vmstat | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"proc_stat\": \"`cat /proc/stat | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"top_bn1\": \"`top -bn1 | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""	
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'WifiInfo'`" ] ; then
	SECTION="WifiInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"WiFi Info\","
	echo " \"description\": \"WiFi network information\","
	echo " \"data\": ["
	echo "{"
    /etc/init.d/service_wifi/get_wifiInfo.sh basic radio client ap ca
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


################################################################################

if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'UsbInfo'`" ] ; then
	SECTION="UsbInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	if [ -f "/www/usbinfo.cgi" ] ; then
		echo "var $SECTION = {"
		echo " \"title\": \"USB Info\","
		echo " \"description\": \"USB information\","
		echo " \"data\": ["
		echo "{"
		if [ -f "/www/usbinfo.cgi" ] ; then
			echo "$(dataify_command "usbinfo" "/www/usbinfo.cgi 1")"
		else
			echo "\"usbinfo\": \"\""
		fi
		echo " }"
		echo " ]"
		echo "};"
		if [ "$ALL_SET" == "1" ] ; then
			SECTION=""
		else
			SECTION="$ORIG_SECTION"
		fi
	fi
fi

################################################################################


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'IPInfo'`" ] ; then
	SECTION="IPInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"IP Info\","
	echo " \"description\": \"TCP/IP network information\","
	echo " \"data\": ["
	echo "{"
	echo " \"ifconfig\": \"`ifconfig | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"etc_resolv_conf\": \"`cat /etc/resolv.conf | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_link\": \"`ip link | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_neigh\": \"`ip neigh | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip6_addr_show\": \"`ip -6 addr show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_route\": \"`ip route | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip6_route_show\": \"`ip -6 route show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_tunnel_show\": \"`ip tunnel show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'IPNetInfo'`" ] ; then
	SECTION="IPNetInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"ipnet\","
	echo " \"description\": \"TCP/IP network information\","
	echo " \"data\": ["
	echo "{"
	echo " \"ifconfig\": \"`ifconfig | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"etc_resolv_conf\": \"`cat /etc/resolv.conf | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_link\": \"`ip link | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_neigh\": \"`ip neigh | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip6_addr_show\": \"`ip -6 addr show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_route\": \"`ip route | sed 's/$/\\\n/g' | tr -d '\n'`\","
	echo " \"ip6_route_show\": \"`ip -6 route show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ip_tunnel_show\": \"`ip tunnel show | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	#fix the key name //Edward
	#echo " \"rdisc6_r1_$wan_ifname\": \"`rdisc6 -r1 $wan_ifname | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"rdisc6_r1_wan\": \"`rdisc6 -r1 $wan_ifname | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"rdisc6_r1_ppp0\": \"`rdisc6 -r1 ppp0 | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ping_c2_www_ietf_org\": \"`ping -c2 www.ietf.org | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ping_c2_8888\": \"`ping -c2 8.8.8.8 | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"br0\": \"`ifconfig br0 | grep 'RX bytes:'`\","
	echo " \"eth0\": \"`ifconfig eth0 | grep 'RX bytes:'`\","
	echo " \"eth1\": \"`ifconfig eth1 | grep 'RX bytes:'`\","
	echo " \"eth2\": \"`ifconfig eth2 | grep 'RX bytes:'`\","
	echo " \"vlan1\": \"`ifconfig vlan1 | grep 'RX bytes:'`\","
	echo " \"vlan2\": \"`ifconfig vlan2 | grep 'RX bytes:'`\","
	echo " \"wl1_2\": \"`ifconfig wl1.2 | grep 'RX bytes:'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'PingInfo'`" ] ; then
	SECTION="PingInfo"
	JS_SECTIONS_VAR="$JS_SECTIONS_VAR $SECTION"
	echo "var $SECTION = {"
	echo " \"title\": \"Ping\","
	echo " \"description\": \"Ping results of various hosts\","
	echo " \"data\": ["
	echo "{"
	echo " \"ping_c2_www_ietf_org\": \"`ping -c2 www.ietf.org | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"ping_c2_8888\": \"`ping -c2 8.8.8.8 | sed 's/$/\\\n/g' | tr -d '\n' | tr -d '\r'`\","
	echo " \"br0\": \"`ifconfig br0 | grep 'RX bytes:'`\","
	echo " \"eth0\": \"`ifconfig eth0 | grep 'RX bytes:'`\","
	echo " \"eth1\": \"`ifconfig eth1 | grep 'RX bytes:'`\","
	echo " \"eth2\": \"`ifconfig eth2 | grep 'RX bytes:'`\","
	echo " \"vlan1\": \"`ifconfig vlan1 | grep 'RX bytes:'`\","
	echo " \"vlan2\": \"`ifconfig vlan2 | grep 'RX bytes:'`\","
	echo " \"wl1_2\": \"`ifconfig wl1.2 | grep 'RX bytes:'`\""
	echo " }"
	echo " ]"
	echo "};"
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi


if [ "$SECTION" == "" ] || [ "`echo $SECTION | grep 'Conntrack'`" ] ; then
	SECTION="Conntrack"
    # Conntrack may not be installed
    if [ -f /etc/conntrack2json.sh ]; then
	    /etc/conntrack2json.sh
    else
        echo "var Conntrack = {"
        echo "  \"title\": \"conntrack\"," 
        echo "  \"description\": \"1 minute snapshots of conntrack tables\"," 
        echo "  \"data\": ["
        echo "]"
        echo "};"
    fi
	if [ "$ALL_SET" == "1" ] ; then
		SECTION=""
	else
		SECTION="$ORIG_SECTION"
	fi
fi



# Example of how to quickly create a JSON variable from a command
# jsonify_command "bootloader_info" "/www/bootloader_info.cgi"

# jsonify_command "bootloader_info" "/www/bootloader_info.cgi" "This is information from the bootloader"


# echo "var sysinfoSections = \"$JS_SECTIONS_VAR\";"
