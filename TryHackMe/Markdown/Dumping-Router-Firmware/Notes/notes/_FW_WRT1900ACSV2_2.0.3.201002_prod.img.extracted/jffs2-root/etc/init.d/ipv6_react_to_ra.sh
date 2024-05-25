#!/bin/sh
if [ "`syscfg get 6rd_enable`" = "1" ]; then
	return
fi
WAN_IFNAME="`sysevent get current_wan_ipv6_ifname`"
if [ "$1" != "$WAN_IFNAME" -o "started" != "`sysevent get ipv6-status`" ] ; then
	return
fi
if [ "`syscfg get dhcpv6c_enable`" = "3" -a "`sysevent get dhcpv6c_bound`" = "0" ]; then
	SYSEVENT_dhcpv6_client_current_config=`sysevent get dhcpv6_client_current_config`
	
	if [ "$2" = "0" ] ; then
		if [ "$SYSEVENT_dhcpv6_client_current_config" != "1" ] ; then
			sysevent set dhcpv6_client_current_config 3
        	sysevent set dhcpv6_client-restart ACCEPT_INCOMPLETE_LEASE
        	echo "dhcpv6 client is restarted, M=$2, previous dhcpv6 client config is $SYSEVENT_dhcpv6_client_current_config!" > /dev/console
		fi
	else
		if [ -n "$SYSEVENT_dhcpv6_client_current_config" -a "$SYSEVENT_dhcpv6_client_current_config" = "1" ] ; then
			sysevent set dhcpv6_client_current_config
        	sysevent set dhcpv6_client-restart ACCEPT_INCOMPLETE_LEASE
        	echo "dhcpv6 client is restarted, M=$2, previous dhcpv6 client config is $SYSEVENT_dhcpv6_client_current_config!" > /dev/console
		fi
	fi
fi
CURRENT_WAN_MTU="`cat /proc/sys/net/ipv6/conf/$WAN_IFNAME/mtu`"
SYSEVENT_saved_wan_mtu="`sysevent get saved_wan_mtu`"
if [ -z "$SYSEVENT_saved_wan_mtu" -o "$SYSEVENT_saved_wan_mtu" != "$CURRENT_WAN_MTU" ] ; then
	sysevent set saved_wan_mtu $CURRENT_WAN_MTU
	sysevent set radvd-reload
fi
NEED_RESTART=0
ip -6 route show | grep default > /dev/null
NOW=$?
sysevent get ipv6_default_route_exist | grep 1
THEN=$? 
if [ "$NOW" != "$THEN" ] ; then
   NEED_RESTART=1
fi
ip -6 addr show dev $WAN_IFNAME | grep global > /dev/null
NOW=$?
sysevent get ipv6_wan_address_exist | grep 1
THEN=$? 
if [ "$NOW" != "$THEN" ] ; then
   NEED_RESTART=1
fi
if [ "1" = "$NEED_RESTART" ] ; then
   sysevent set radvd-reload
fi
