#!/bin/sh

########################################################
# qos information 
######################################################

wan_name=`sysevent get current_wan_ifname`
lan_name=`syscfg get lan_ifname`
wireless_names=`syscfg get lan_wl_physical_ifnames`
echo "================================================"
echo "----- QoS info -------"
echo " * syscfg QoS Info"
echo "`syscfg show | grep -i qos | sort`"
echo " * sysevent QoS info"
echo " committed_bitrate is `sysevent get committed_bitrate`"
echo " * QoS queues"
echo " -> queues on $wan_name"
echo " `tc -s -d class show dev $wan_name`"
echo " -> queues on $lan_name"
echo " `tc -s -d class show dev $lan_name`"
for loop in $wireless_names ; do
   echo " -> queues on $loop"
   echo " `tc -s -d class show dev $loop`"
done
