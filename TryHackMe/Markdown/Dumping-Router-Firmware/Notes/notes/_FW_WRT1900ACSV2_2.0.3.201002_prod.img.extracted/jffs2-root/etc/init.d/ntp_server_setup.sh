#!/bin/sh
source /etc/init.d/syscfg_api.sh
MODEL=`syscfg get device::modelNumber`
COUNTRY=`echo $MODEL | awk -F"-" '{print $2}'`
if [ -z $COUNTRY ] || [ "US" = $COUNTRY ]; then
	echo "Updating NTP Servers if necessary"
	syscfg_set ntp_server1 0.pool.ntp.org
	syscfg_set ntp_server2 1.pool.ntp.org
	syscfg_set ntp_server3 2.pool.ntp.org
else
	echo "NTP Servers do not need to be updated"
fi
