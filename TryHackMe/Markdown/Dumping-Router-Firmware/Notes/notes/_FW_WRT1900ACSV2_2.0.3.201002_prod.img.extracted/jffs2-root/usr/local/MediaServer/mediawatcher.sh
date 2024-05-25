#! /bin/sh

# run from cron

limit=55000

pid=`ps | grep [t]wonkymediaserver | awk '{print $1}' | head -1`
if [ X"$pid" = X ]; then
	# no process
	exit 0
fi

vm=`grep VmSize: /proc/$pid/status | awk '{print $2}'`
if [ $vm -gt $limit ]; then

	bridge_mode=`syscfg get bridge_mode`
	if [ "$bridge_mode" = 0 ]; then
		serv_loc_addr=`syscfg get lan_ipaddr`
	else
		serv_loc_addr=`sysevent get ipv4_wan_ipaddr`
	fi
	
	wget -O /dev/null http://${serv_loc_addr}:9999/rpc/restart
	
	sleep 10
	
	# still there?
	if kill -CONT $pid 2>/dev/null ; then
		kill -9 $pid
	fi
fi
