#!/bin/sh
source /etc/init.d/syscfg_api.sh
show_settings()
{
	VIR_IFNAME=$1
	
	echo "------------- ${VIR_IFNAME} Settings -------------"
	echo "Interface name: $VIR_IFNAME"
	echo "SSID: `iwconfig $VIR_IFNAME | head -1 | awk -F'ESSID:' '{print $2}'`"
	echo "Mac address: `iwpriv ${VIR_IFNAME} getbssid`"
	echo "Channel: `iwlist ${VIR_IFNAME} channel`"
	echo "Auto Channel: `iwpriv ${VIR_IFNAME} getautochannel`"
	echo "Banwidth (0=auto,2=20HMz,3=40MHz): `iwpriv ${VIR_IFNAME} gethtbw`"
	echo "Network Mode (1:b, 2:g, 3:b/g, 4:n[2.4GHz], 6:g/n, 7:b/g/n, 8:a, 12:a/n, 13:n[5GHz], 23:b/g/n/ac, 28:a/n/ac): `iwpriv ${VIR_IFNAME} getopmode`"
	echo "Guard Interval (0=auto,1=Short,2=Long): Current `iwpriv ${VIR_IFNAME} getguardint`"
	echo "Current connected client(s): `iwpriv ${VIR_IFNAME} getstalistext`"
	echo "Tx Antennas (0=auto i.e all antennas, 1=antenna A, 2=antenna B, 3=antenna AB, 7=antenna ABC, 0xF= antenna ABCD): `iwpriv ${VIR_IFNAME} gettxantenna`"
	echo "Rx Antennas (0=auto,1=1 antenna, 2=2 antennas, 3=3 antennas, 4=4 antennas): `iwpriv ${VIR_IFNAME} getrxantenna`"
	echo "WMM mode (0=AC_BE, 1=AC_BK, 2=AC_VI, 3=AC_VO): `iwpriv ${VIR_IFNAME} getwmm`"
	echo "Optimization Level (0=harmony mode, 1=high performance): `iwpriv ${VIR_IFNAME} getoptlevel`"
	echo "Multicast Proxy (0=disable, 1=enable ): `iwpriv ${VIR_IFNAME} getmcastproxy`"
	echo "Intra BSS bridging (0=disable wireless to wireless client bridging, 1=enable wireless to wireless client bridging): `iwpriv ${VIR_IFNAME} getintrabss`"
	echo "SSID broacast (0=show and respond to probe requests, 1=hide and do not respond to probe requests) `iwpriv ${VIR_IFNAME} gethidessid`"
	echo "Beacon interval (20-1000: interval in time units 1=1.024ms): `iwpriv ${VIR_IFNAME} getbcninterval`"
	echo "Power save DTIM (1-255): `iwpriv ${VIR_IFNAME} getdtim`"
	echo "11g protection (0=protection off, 1=protection auto): `iwpriv ${VIR_IFNAME} getgprotect`"
	echo "Preamble (0=auto, 1=short preamble, 2=long preamble): `iwpriv ${VIR_IFNAME} getpreamble`"
	echo "Idle aging time (60-86400 sec): `iwpriv ${VIR_IFNAME} getagingtime`"
	echo "Associations to AP BSS (0=disable, 1=enable ): `iwpriv ${VIR_IFNAME} getdisableassoc`"
	echo "WDS mode (0=disable, 1=enable ): `iwpriv ${VIR_IFNAME} getwdsmode`"
	echo "HT Greenfield mode (0=disable, 1=enable ): `iwpriv ${VIR_IFNAME} gethtgf`"
	echo "Tx Power: `iwpriv ${VIR_IFNAME} gettxpower`"
	echo ""
}
echo "========================== Wi-Fi General Information =========================="
PHYSICAL_IF_LIST=`syscfg_get lan_wl_physical_ifnames`
if [ "`syscfg_get wl0_state`" = "up" ]; then
	USER24_ENABLED=1
else
	USER24_ENABLED=0
fi
if [ "`syscfg_get wl1_state`" = "up" ]; then
	USER5_ENABLED=1
else
	USER5_ENABLED=0
fi
GUEST_ENABLED=`syscfg_get guest_enabled`
GUEST24_ENABLED=`syscfg_get wl0_guest_enabled`
GUEST5_ENABLED=`syscfg_get wl1_guest_enabled`
TC_ENABLED=`syscfg_get tc_vap_enabled`
WIFI_SCHEDULER_ENABLED=`syscfg_get wifi_scheduler::enabled`
echo "Wifi driver version           : `iwpriv wdev0 version`"
echo "Country code                  : `iwpriv wdev0 getregioncode`"
echo "Device Serial Number          : `syscfg_get device::serial_number`"
echo "Primary 2.4GHz enabled        : $USER24_ENABLED"
echo "Primary 5GHz enabled          : $USER5_ENABLED"
echo "Guest Master Switch enabled   : $GUEST_ENABLED"
echo "Guest 2.4GHz enabled          : $GUEST24_ENABLED"
echo "Guest 5GHz enabled            : $GUEST5_ENABLED"
echo "SimpleTap enabled             : $TC_ENABLED"
echo "Wi-Fi scheduler enabled       : $WIFI_SCHEDULER_ENABLED"
echo ""
echo "----- MAC address (from ifconfig) -----"
LAN_IF=`syscfg_get lan_ethernet_physical_ifnames`
WAN_IF=`syscfg_get wan_physical_ifname`
echo "LAN Mac Address			: `ifconfig ${LAN_IF} | grep HWaddr | awk '{print $5}'`"
echo "WAN Mac Address			: `ifconfig ${WAN_IF} | grep HWaddr | awk '{print $5}'`"
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg_get ${PHY_IF}_syscfg_index`
	USER_IF=`syscfg_get ${WL_SYSCFG}_user_vap`
	if [ "${WL_SYSCFG}" = "wl0" ]; then
		RADIO_NAME="2.4GHz"
	else
		RADIO_NAME="5GHz"
	fi
	
	echo "Primary Mac Address $RADIO_NAME	: `ifconfig ${USER_IF} | grep HWaddr | awk '{print $5}'`"
	if [ "$GUEST_ENABLED" = "1" ]; then
		if [ "`syscfg_get "${WL_SYSCFG}"_guest_enabled`" = "1" ]; then
			GUEST_IF=`syscfg_get ${WL_SYSCFG}_guest_vap`
			echo "Guest Mac Address $RADIO_NAME	: `ifconfig ${GUEST_IF}| grep HWaddr | awk '{print $5}'`"
		fi
	fi
	if [ "${WL_SYSCFG}" = "wl0" ] && [ "$TC_ENABLED" = "1" ]; then
		TC_IF=`syscfg_get tc_vap_user_vap`
		echo "SimpleTap Mac Address (2.4G)  : `ifconfig ${TC_IF}   | grep HWaddr | awk '{print $5}'`"
	fi
done
echo ""
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg_get ${PHY_IF}_syscfg_index`
	USER_IF=`syscfg_get ${WL_SYSCFG}_user_vap`
	
	if [ "`syscfg_get ${WL_SYSCFG}_state`" = "up" ]; then
		show_settings ${USER_IF}
		if [ "${WL_SYSCFG}" = "wl0" ] && [ "`syscfg_get guest_enabled`" = "1" ]; then
			GUEST_IF=`syscfg_get ${WL_SYSCFG}_guest_vap`
			show_settings ${GUEST_IF}
		fi
	fi
done
echo "------------- Wi-Fi syscfg section -------------"
echo "`syscfg show | grep wl0_ | grep -v passphrase | grep -v password | sort`"
echo ""
echo "`syscfg show | grep wl1_ | grep -v passphrase | grep -v password | sort`"
echo ""
echo "`syscfg show | grep guest_ | grep -v passphrase | grep -v password | sort`"
echo "========================== End Of Wi-Fi General Information =========================="
echo ""
echo ""
echo ""
echo ""
