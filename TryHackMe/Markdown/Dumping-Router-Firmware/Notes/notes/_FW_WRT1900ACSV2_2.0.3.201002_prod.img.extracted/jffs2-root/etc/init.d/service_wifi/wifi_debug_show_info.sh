#!/bin/sh
source /etc/init.d/syscfg_api.sh
STA_ENABLED=`syscfg get wifi_bridge::mode`
if [ "1" = "$STA_ENABLED" ]; then
	echo "syscfg parameters for sta:"
	echo "`syscfg show | grep wifi_sta`"
	echo "`syscfg show | grep lan_wl_physical`"
	STA_IF=`syscfg get wifi_sta_vir_if`
	LINK_STATUS=`iwpriv $STA_IF getlinkstatus | awk -F":" '{print $2}'`
	if [ "1" = "$LINK_STATUS" ]; then
		echo "`iwconfig $STA_IF`"
	else
		echo "STA is not connected!"
		echo "`iwpriv $STA_IF stamode 6; iwconfig $STA_IF commit; iwpriv $STA_IF stascan 1; sleep 5; iwpriv $STA_IF getstascan`"
	fi
fi
IF24=`syscfg get wl0_physical_ifname`
IF24USER=`syscfg get wl0_user_vap`
IF24GUEST=`syscfg get wl0_guest_vap`
IF24STA=`syscfg get wl0_sta_vap`
IF5=`syscfg get wl1_physical_ifname`
IF5USER=`syscfg get wl1_user_vap`
IF5GUEST=`syscfg get wl0_guest_vap`
IF5STA=`syscfg get wl1_sta_vap`
echo "========================== Wi-Fi Developpement Debug Information =========================="
echo "Site Survey on 2.4GHz:"
echo "`iwpriv $IF24STA stamode 7; iwconfig $IF24STA commit; iwpriv $IF24STA stascan 1; sleep 5; iwpriv $IF24STA getstascan`"
echo ""
echo "Site Survey on 5GHz:"
echo "`iwpriv $IF5STA stamode 8; iwconfig $IF5STA commit; iwpriv $IF5STA stascan 1; sleep 5; iwpriv $IF5STA getstascan`"
echo ""
echo "----- Dumping all driver internal configurations -----"
echo "Dumping all driver internal configurations for 2.4GHz:"
echo "`cat /proc/ap8x/${IF24USER}_stats`"
echo "`iwpriv ${IF24USER} -a | grep -v passphrase | grep -v password`"
echo "Power table for 2.4GHz"
echo "`cat /etc/24G_power_table_FCC`"
echo ""
echo "Dumping all driver internal configurations for 5GHz:"
echo "`cat /proc/ap8x/${IF5USER}_stats`"
echo "`iwpriv ${IF5USER} -a | grep -v passphrase | grep -v password`"
echo "Power table for 5GHz"
echo "`cat /etc/5G_power_table_FCC`"
echo ""
echo "Dumping all driver internal configurations for Guest Access:"
echo "`iwpriv $IF24GUEST -a | grep -v passphrase | grep -v password`"
echo ""
echo "`iwpriv $IF5GUEST -a | grep -v passphrase | grep -v password`"
echo "========================== End Of Wi-Fi Developpement Debug Information =========================="
echo ""
