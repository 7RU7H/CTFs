#!/bin/sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/util_functions.sh
VENDOR=`syscfg get hardware_vendor_name | sed s/[[:space:]]//g`
if [ "$VENDOR" != "Marvell" ]; then
	echo "error--this script is for Marvell, but syscfg get ${VENDOR}"
	exit 1
fi
SECTION=$(get_cgi_val "section")
if [ "$SECTION" == "" ] ; then
	SECTION=$@
fi
GLOBAL_AP_MODE="scan"
GLOBAL_SECTION_EN_BASIC="0"
GLOBAL_SECTION_EN_RADIO="0"
GLOBAL_SECTION_EN_CLIENT="0"
GLOBAL_SECTION_EN_AP="0"
GLOBAL_AP_MAX="30"
if [ "$SECTION" != "" ]; then
	for arg in ${SECTION}
	do
	    case $arg in 
		"basic") 
			GLOBAL_SECTION_EN_BASIC="1";;
		"radio") 
			GLOBAL_SECTION_EN_RADIO="1";;
		"client") 
			GLOBAL_SECTION_EN_CLIENT="1";;
		"ap")
			GLOBAL_SECTION_EN_AP="1";;	
		"ca")
			GLOBAL_SECTION_EN_CA="1";;	
		*)		
			NUM=`echo "$arg" | awk -F 'apmax=' '{print $2}'`
			if [ -n "${NUM}" ]; then
				GLOBAL_AP_MAX="$NUM"
			fi;;
	    esac
	done
fi
ShowBasicInfo()
{
echo "var BasicInfo={"
echo "\"title\": \"Basic Info\","
echo "\"description\": \"basic information about platform\","
echo "\"data\": [{"
echo "	\"vendor\": \"${VENDOR}\","
REGION_CODE=`skuapi -g cert_region | awk -F"=" '{print $2}' | sed 's/ //g'`
if [ -z "$REGION_CODE" ]; then
    REGION_CODE=`syscfg_get device::cert_region`
fi
if [ -z "$REGION_CODE" ]; then
    REGION_CODE="US"
fi
echo "	\"CountryCode\": \"${REGION_CODE}\","
echo "	\"WifiDriverVer\": \"`wl ver | sed 1d | awk '{print $7}'`\""
echo "}]"
echo "};"
}
ShowClientInfo()
{
CLIENTSNUMBER=0
PHYSICAL_IF_LIST=`syscfg get lan_wl_physical_ifnames`
for PHY_IF in $PHYSICAL_IF_LIST; do
	NUM=`wl -i ${PHY_IF} assoclist | wc -l`
	CLIENTSNUMBER=`expr $CLIENTSNUMBER + $NUM`
done
echo "var ClientInfo={"
echo "\"title\": \"clients\","
echo "\"description\": \"the information about connected clients\","
echo "\"number\": \"${CLIENTSNUMBER}\","
echo "\"data\": ["
FIRSTDONE=0
for PHY_IF in $PHYSICAL_IF_LIST; do
	STANUM=`wl -i ${PHY_IF} assoclist | wc -l`
	if [ "${STANUM}" = "0" ] ; then
		continue
	fi
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
	if [ "$FIRSTDONE" = "1" ]; then
        echo "	,"
	fi	
	if [ "$FIRSTDONE" = "0" ]; then
		FIRSTDONE=1
	fi
	TMP_FILE="/tmp/_get_wifiInfo_sta_info"
	wl -i ${PHY_IF} assoclist | awk '{print $2}' > "${TMP_FILE}"
	INDEX=1
	while read line
	do
        echo "	{\"mac\": \"${line}\","
        echo "	 \"type\": \"${RADIO_TYPE}\","
        echo "	 \"interface\": \"${PHY_IF}\","
        echo "	 \"rssi\": \"`wl -i ${PHY_IF} rssi ${line}`\","
        echo "	 \"APSSID\": \"\","
        echo "	 \"mode\": \"\","
        echo "	 \"rate\": \"\","
        echo "	 \"bandwidth\": \"\","
        echo "	 \"mumimo\": \"\","
        echo "	 \"channel\": \"\"}"
		if [ ${INDEX} -ne ${STANUM} ] ; then
            echo "	,"
		fi
		INDEX=`expr $INDEX + 1`
	done < "${TMP_FILE}"
    rm -f $TMP_FILE
done
echo "]};"
}
ShowAPWithScan() 
{
TOTAL_CNT=0
FILEPREFIEX="__TmpWifScan_"
APNUMBER=0
PHYSICAL_IF_LIST=`syscfg get lan_wl_physical_ifnames`
for PHY_IF in $PHYSICAL_IF_LIST; do
	wl -i ${PHY_IF} scan 
	sleep 3
	wl -i ${PHY_IF} scanresults > /tmp/${FILEPREFIEX}_${PHY_IF}
	NUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep '^SSID:' -n | wc -l`
	APNUMBER=`expr $APNUMBER + $NUM`
done
if [ "$GLOBAL_AP_MAX" != "0" ] && [ "$APNUMBER" -gt "$GLOBAL_AP_MAX" ] ; then
	APNUMBER="$GLOBAL_AP_MAX"
fi
echo "var APInfo={"
echo "\"title\": \"site survey\","
echo "\"description\": \"the detail about the adjacent AP\","
echo "\"number\": \"${APNUMBER}\","
echo "\"data\": ["
FIRSTDONE=0
DONE2G=0
DONE5G=0
for PHY_IF in $PHYSICAL_IF_LIST; do
	APNUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep '^SSID:' -n | wc -l`
	if [ "${APNUM}" = "0" ] ; then
		continue
	fi
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
	if [ "$RADIO_TYPE" = "2.4GHz" ] && [ "$DONE2G" = "1" ];then
		continue
	elif [ "$RADIO_TYPE" = "2.4GHz" ] && [ "$DONE2G" = "0" ];then
		DONE2G=1
	elif [ "$RADIO_TYPE" = "5GHz" ] && [ "$DONE5G" = "1" ];then
		continue
	elif [ "$RADIO_TYPE" = "5GHz" ] && [ "$DONE5G" = "0" ];then
		DONE5G=1
	fi
		
	if [ "$FIRSTDONE" = "1" ]; then
        echo "	,"
	fi	
	if [ "$FIRSTDONE" = "0" ]; then
		FIRSTDONE=1
	fi
	INDEX=1
	while [ ${INDEX} -le ${APNUM} ]
	do
		RESULTFILE="/tmp/${FILEPREFIEX}_${PHY_IF}_RESULT_"
		if [  ${INDEX} -eq ${APNUM} ] ; then
			STARTROWNUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep '^SSID:' -n | awk -F ':' '{print $1}' | sed -n ${INDEX}p`
			cat /tmp/${FILEPREFIEX}_${PHY_IF} | sed -n "${STARTROWNUM},$ p" > ${RESULTFILE}
		else
			
			STARTROWNUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep '^SSID:' -n | awk -F ':' '{print $1}' | sed -n ${INDEX}p`
			ENDROWNUM=`expr $INDEX + 1`
			ENDROWNUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep '^SSID:' -n | awk -F ':' '{print $1}' | sed -n ${ENDROWNUM}p`
			ENDROWNUM=`expr $ENDROWNUM - 1`
			cat /tmp/${FILEPREFIEX}_${PHY_IF} | sed -n ${STARTROWNUM},${ENDROWNUM}p > ${RESULTFILE}
		fi
		echo "	{\"ssid\": `cat ${RESULTFILE} | grep '^SSID' | awk '{print $2}'`,"
		echo "	 \"bssid\": \"`cat ${RESULTFILE} | grep '^BSSID' | awk '{print $2}'`\","
		echo "	 \"type\": \"${RADIO_TYPE}\","
		echo "	 \"channel\": \"`cat ${RESULTFILE} | grep 'Primary channel:' | awk -F ': ' '{print $2}'`\","
		echo "	 \"rssi\": \"`cat ${RESULTFILE} | grep 'RSSI:' | awk '{print $4}'`\","
		SECU_MODE=`cat ${RESULTFILE} | grep 'AKM Suites' | awk -F ': ' '{print $2}' | sed 's/ //'`
		if [ `cat ${RESULTFILE} | grep 'AKM Suites' | awk -F ': ' '{print $2}' | wc -l` -gt "1" ]; then
			SECU_MODE="WPA2/WPA Mixed"
		elif [ `cat ${RESULTFILE} | grep 'AKM Suites' | awk -F ': ' '{print $2}' | wc -l` -eq "0" ]; then
			SECU_MODE="off"
		fi
		echo "	 \"security\": \"${SECU_MODE}\","
		echo "	 \"mode\": \"(Not available)\"}"
		TOTAL_CNT=`expr $TOTAL_CNT + 1`
		if [ "$GLOBAL_AP_MAX" != "0" ] && [ "$TOTAL_CNT" -ge "$GLOBAL_AP_MAX" ]; then
			break
		fi
		if [ ${INDEX} -ne ${APNUM} ] ; then
            echo "	,"
		fi
		INDEX=`expr $INDEX + 1`
	done
	if [ "$GLOBAL_AP_MAX" != "0" ] && [ "$TOTAL_CNT" -ge "$GLOBAL_AP_MAX" ]; then
		break
	fi
done
echo "]};"
rm /tmp/${FILEPREFIEX}* -f
}
ShowCAInfo()
{
PHYSICAL_IF_LIST=`syscfg get lan_wl_physical_ifnames`
PHYSICAL_NUM=`syscfg get lan_wl_physical_ifnames | awk '{print NF}'`
echo "var CAInfo={"
echo "\"title\": \"channel analysis\","
echo "\"description\": \"the information about channel analysis\","
echo "\"band\": ["
INDEX=1
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
    echo "	{\"name\": \"${PHY_IF}\","
    echo "	\"type\":\"${RADIO_TYPE}\","
    echo "	\"band\": \"`syscfg get ${WL_SYSCFG}_radio_band`\"}"
	if [ ${INDEX} -ne ${PHYSICAL_NUM} ] ; then
        echo "	,"
	fi
	INDEX=`expr $INDEX + 1`
done
echo "],"
echo "\"channel\": ["
INDEX=1
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
    echo "	{\"name\": \"${PHY_IF}\","
    echo "	\"type\":\"${RADIO_TYPE}\","
    echo "	\"channel\": \"(Not Available)\"}"
	if [ ${INDEX} -ne ${PHYSICAL_NUM} ] ; then
        echo "	,"
	fi
	INDEX=`expr $INDEX + 1`
done
echo "],"
echo "\"ap\": ["
for PHY_IF in $PHYSICAL_IF_LIST; do
	if [ ! -f "/tmp/${FILEPREFIEX}_${PHY_IF}" ];then
	    touch /tmp/${FILEPREFIEX}_${PHY_IF}
	fi
done
FIRST_INTERFACE=1
for PHY_IF in $PHYSICAL_IF_LIST; do
	FIRSTDONE=0
	APNUM=`cat /tmp/${FILEPREFIEX}_${PHY_IF} | grep ' Address: ' -n | wc -l`
	if [ "${APNUM}" = "0" ] ; then
		continue
	fi
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
	if [ "$FIRST_INTERFACE" = "1" ];then
		FIRST_INTERFACE=0
	else
        echo "  ,"
	fi
    NUMBER=0
    echo "  {\"channel\": \"(Not available)}\","
    echo "   \"type\": \"${RADIO_TYPE}\","
    echo "   \"data\":["
    echo "  ],"
    echo "  \"number\": \"${NUMBER}\"}"
done
echo "],"
echo "\"power\": ["
INDEX=1
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
	if [ "$WL_SYSCFG" = "wl0" ] ; then
		RADIO_TYPE="2.4GHz"
	else
		RADIO_TYPE="5GHz"
	fi
    echo "	{\"name\": \"${PHY_IF}\","
    echo "	\"type\":\"${RADIO_TYPE}\","
    echo "	\"txpower\": \"(Not available)\"}"
	if [ ${INDEX} -ne ${PHYSICAL_NUM} ] ; then
        echo "	,"
	fi
	INDEX=`expr $INDEX + 1`
done
echo "]"
echo "};" 
rm /tmp/${FILEPREFIEX}* -f
}
ShowRadioInfo()
{
PHYSICAL_NUM=`syscfg get lan_wl_physical_ifnames | awk '{print NF}'`
PHYSICAL_IF_LIST=`syscfg get lan_wl_physical_ifnames`
echo "var RadioInfo={"
echo "\"title\": \"radio information\","
echo "\"description\": \"basic radio information\","
echo "\"number\": \"${PHYSICAL_NUM}\","
echo "\"data\": ["
INDEX=1
for PHY_IF in $PHYSICAL_IF_LIST; do
    GUEST_INTERFACE=""
    GUEST_SSID=""
    AP_NUM=1
    WL_SYSCFG=`syscfg get ${PHY_IF}_syscfg_index`
    if [ "$WL_SYSCFG" = "wl0" ] ; then
        RADIO_TYPE="2.4GHz"
        if [ "`syscfg get guest_enabled`" = "1" ] && [ "`syscfg get wl0_guest_enabled`" = "1" ] && [ `syscfg get guest_wifi_phy_ifname` = "$WL_SYSCFG" ]; then
            GUEST_INTERFACE="`syscfg get wl0_guest_vap`"
            GUEST_SSID=`syscfg get guest_ssid`
            AP_NUM=`expr $AP_NUM + 1`
        fi
    else
        RADIO_TYPE="5GHz"
        if [ "`syscfg get guest_enabled`" = "1" ] && [ "`syscfg get ${WL_SYSCFG}_guest_enabled`" = "1" ] && [ `syscfg get ${WL_SYSCFG}_guest_wifi_phy_ifname` = "$WL_SYSCFG" ]; then
            GUEST_INTERFACE="`syscfg get ${WL_SYSCFG}_guest_vap`"
            GUEST_SSID=`syscfg get ${WL_SYSCFG}_guest_ssid`
            AP_NUM=`expr $AP_NUM + 1`
        fi
    fi
    STA_EN="0"
    STA_MODE=""
    STA_BANDWIDTH=""
    STA_STATUS="disconnected"
    if [ "`syscfg get wifi_bridge::mode`" = "1" ] && [ "`syscfg get wifi_bridge::radio`" = "${RADIO_TYPE}" ];then
        if [ "${PHY_IF}" = "ath0" ];then
            STA_EN="1"
            STA_MODE="IEEE80211_MODE_11NG_HT40PLUS"
            STA_BANDWIDTH="40MHz"
        elif [ "${PHY_IF}" = "ath1" ];then
            STA_EN="1"
            STA_EN="1"
            STA_MODE="IEEE80211_MODE_11AC_VHT80"
            STA_BANDWIDTH="80MHz"
        fi
        if [ "`sysevent get wifi_sta_up`" = "1" ];then
            STA_STATUS="connected"
        fi
    fi
echo "  {\"type\":\"${RADIO_TYPE}\","
echo "   \"staEnabled\":\"${STA_EN}\","
    if [ "${STA_EN}" = "1" ];then
echo "   \"sta\":[{"
echo "      \"mac\":\"`syscfg get wl0_sta_mac_addr | tr -d :`\","
echo "      \"status\":\"${STA_STATUS}\","
echo "      \"ssid\":\"`syscfg get wifi_bridge::ssid`\","
echo "      \"channel\":\"`syscfg get wifi_sta_channel`\","
echo "      \"security\":\"`syscfg get wifi_bridge::security_mode`\","
echo "      \"mode\":\"${STA_MODE}\","
echo "      \"bandwidth\":\"${STA_BANDWIDTH}\"}],"
    fi
echo "   \"apNum\": \"${AP_NUM}\","
echo "   \"ap\": ["
echo "       {\"interface\": \"${PHY_IF}\","
echo "        \"ssid\": \"`syscfg get ${WL_SYSCFG}_ssid`\","
echo "        \"bssid\": \"`ifconfig ${PHY_IF} | grep HWaddr | awk '{print $5}'`\","
echo "        \"security\": \"`syscfg get ${WL_SYSCFG}_security_mode`\","
echo "        \"ClientNum\": \"(Not available)\"}"
    if [ "${AP_NUM}" -gt "1" ];then
echo "       ,"
echo "       {\"interface\": \"${GUEST_INTERFACE}\","
echo "        \"ssid\": \"${GUEST_SSID}\","
echo "        \"bssid\": \"`ifconfig ${GUEST_INTERFACE} | grep HWaddr | awk '{print $5}'`\","
echo "        \"security\": \"open\","
echo "        \"ClientNum\": \"(Not available)\"}"
    fi
echo "   ],"
echo "   \"channel\": \"(Not available)\","
echo "   \"band\": \"`syscfg get ${WL_SYSCFG}_radio_band`\","
echo "   \"ComponentID\": \"88W8864\","
echo "   \"beamformingEnable\": \"`syscfg get ${WL_SYSCFG}_txbf_enabled`\","
echo "   \"mumimoEnable\": \"`syscfg get wifi::${WL_SYSCFG}_mumimo_enabled`\"}"
    if [ ${INDEX} -ne ${PHYSICAL_NUM} ] ; then
echo "  ,"
    fi
    INDEX=`expr $INDEX + 1`
done
echo "]};"
}
LOCKFILE="/tmp/$(basename $0 .sh).lock"
lock $LOCKFILE
if [ "${GLOBAL_SECTION_EN_BASIC}" = "1" ]; then
	ShowBasicInfo
fi
if [ "${GLOBAL_SECTION_EN_RADIO}" = "1" ]; then
	ShowRadioInfo
fi
if [ "${GLOBAL_SECTION_EN_CLIENT}" = "1" ]; then
	ShowClientInfo
fi
if [ "${GLOBAL_SECTION_EN_AP}" = "1" ]; then
	ShowAPWithScan
fi
if [ "${GLOBAL_SECTION_EN_CA}" = "1" ]; then
	ShowCAInfo
fi
unlock $LOCKFILE
exit
