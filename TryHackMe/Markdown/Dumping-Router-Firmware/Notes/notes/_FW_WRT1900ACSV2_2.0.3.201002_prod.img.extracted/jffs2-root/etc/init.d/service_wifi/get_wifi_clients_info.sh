#!/bin/sh
source /etc/init.d/syscfg_api.sh
show_clients()
{
	VIR_IFNAME=$1
	ret=`iwpriv $VIR_IFNAME getstalistext | sed  '1d'`
	echo "============================WiFi client on $VIR_IFNAME============================"
	i=1
	if [ "$ret" != "" ]; then
		echo "$ret"|while read line;
		do
			echo "$i $line"|awk {'print "Num:"$1" MAC:"$3" Mode:"$4" Rate:"$7" RSSI:"$10'}
			echo "iwpriv $VIR_IFNAME setcmd \"qstats txrate_histogram\" :"
			iwpriv $VIR_IFNAME setcmd "qstats txrate_histogram"
			ret=`dmesg |awk '{a[NR]=$0;j=0}END{for(i=NR;i>=1;i--) if(a[i]~/============================/) {j++;if(j~/2/) {print i;exit}}}'`
			echo "`dmesg |tail +$ret`"
			mac=`echo "$line"|awk {'print $2'}|sed 's/://g'`
			echo "iwpriv $VIR_IFNAME setcmd \"getratetable $mac\" :"
			iwpriv $VIR_IFNAME setcmd "getratetable $mac"
			ret=`dmesg |awk '{a[NR]=$0}END{for(i=NR;i>=1;i--) if(a[i]~/Client/) {print i;exit}}'`
			echo "`dmesg |tail +$ret`"
			iwpriv $VIR_IFNAME setcmd "qstats reset"
			echo ""
			i=`expr $i + 1`
		done 
	fi
	
}
PHYSICAL_IF_LIST=`syscfg_get lan_wl_physical_ifnames`
for PHY_IF in $PHYSICAL_IF_LIST; do
	WL_SYSCFG=`syscfg_get ${PHY_IF}_syscfg_index`
	USER_IF=`syscfg_get ${WL_SYSCFG}_user_vap`
	
	if [ "`syscfg_get ${WL_SYSCFG}_state`" = "up" ]; then
		show_clients ${USER_IF}
		if [ "`syscfg get ${WL_SYSCFG}_guest_enabled`" = "1" ] && [ "`syscfg_get guest_enabled`" = "1" ]; then
				GUEST_IF=`syscfg_get ${WL_SYSCFG}_guest_vap`
				show_clients ${GUEST_IF}
		fi
	fi
done
