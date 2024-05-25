#!/bin/sh
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="guest_access"
SERVICE_FILE="/etc/init.d/service_guest_access.sh"
GUEST_ACCESS=guest-access
GA_PATH=/usr/sbin
GA_BIN=$GA_PATH/$GUEST_ACCESS
PMON=/etc/init.d/pmon.sh
PID_FILE=/var/run/$GUEST_ACCESS.pid
stop_guest_access()
{
	if [ -x $GA_BIN ] ; then
		pidof $GUEST_ACCESS > /dev/null
		if [ $? -eq 0  ] ; then
			ulog guest_access status "stopping $GUEST_ACCESS"
			$PMON unsetproc $SERVICE_NAME
			killall $GUEST_ACCESS > /dev/null 2>&1
			rm -f $PID_FILE
		fi
	fi
}
start_guest_access()
{
	if [ -x $GA_BIN ] ; then
		ulog guest_access status "starting $GUEST_ACCESS"
		$GA_BIN -d
		pidof $GUEST_ACCESS > $PID_FILE
		if [ $? -eq 0 ] ; then
			$PMON setproc $SERVICE_NAME $GUEST_ACCESS $PID_FILE "$SERVICE_FILE $SERVICE_NAME-restart"
		else
			ulog guest_access status "Failed to start $GUEST_ACCESS"
			rm -f $PID_FILE
		fi
	fi
}
do_start()
{
	ulog guest_access status "bringing up guest access control"
	ifconfig|grep -q $SYSCFG_guest_lan_ifname
	if [ $? = 1 -a "$SYSCFG_guest_enabled" = "1" ] ; then
		brctl addbr $SYSCFG_guest_lan_ifname
		brctl setfd $SYSCFG_guest_lan_ifname 0
		brctl stp $SYSCFG_guest_lan_ifname off
	fi
	brctl show|grep -q $SYSCFG_wl0_guest_vap
	if [ "$SYSCFG_wl0_guest_enabled" = "1" -a $? = 1 ] ; then
		ifconfig|grep -q $SYSCFG_wl0_guest_vap
		if [ $? = 0 ] ; then
			enslave_a_interface $SYSCFG_wl0_guest_vap $SYSCFG_guest_lan_ifname
		fi
	fi
	brctl show|grep -q $SYSCFG_wl1_guest_vap
	if [ "$SYSCFG_wl1_guest_enabled" = "1" -a $? = 1 ] ; then
		ifconfig|grep -q $SYSCFG_wl1_guest_vap
		if [ $? = 0 ] ; then
			enslave_a_interface $SYSCFG_wl1_guest_vap $SYSCFG_guest_lan_ifname
		fi
	fi
	if [ -n "$SYSCFG_guest_lan_ipaddr" -a -n "$SYSCFG_guest_lan_netmask" ]
	then
		ip addr add $SYSCFG_guest_lan_ipaddr/$SYSCFG_guest_lan_netmask broadcast + dev $SYSCFG_guest_lan_ifname
	fi
	ip link set $SYSCFG_guest_lan_ifname up 
	ip link set $SYSCFG_guest_lan_ifname allmulticast on 
	stop_guest_access 
	start_guest_access
	echo "Guest access control is up " > /dev/console
	
	STATUS=`sysevent get ipv6-status`
    if [ "started" = "$STATUS" ] ; then 
    	sysevent set ipv6-restart
    fi
}
cleanup_conntrack()
{
	while read LINE
	do
		IP=$(echo "$LINE"|awk '{print $3}')
		eval `ipcalc -n $IP $SYSCFG_guest_lan_netmask`
		if [ "$NETWORK" = "$SYSCFG_guest_subnet" ] ; then
			ulog guest_access status "cleanup conntrack for "$IP
			conntrack -D -s $IP
		fi
	done < /etc/dnsmasq.leases
}
do_stop()
{
	ulog guest_access status "bringing down guest access "
	ifconfig|grep -q $SYSCFG_guest_lan_ifname
	if [ $? = 0 ] ; then
		brctl delif $SYSCFG_guest_lan_ifname $SYSCFG_wl0_guest_vap
		brctl delif $SYSCFG_guest_lan_ifname $SYSCFG_wl1_guest_vap
		ip link set $SYSCFG_guest_lan_ifname down
		ip addr flush dev $SYSCFG_guest_lan_ifname
		brctl delbr $SYSCFG_guest_lan_ifname
	fi
	
	cleanup_conntrack
	stop_guest_access
	echo "Guest access control is down " > /dev/console
}
status_change()
{
	if [ "started" = "`sysevent get wan-status`" ] && [ "started" = "`sysevent get wifi_user-status`" ] ; then
		service_start 
	else
		service_stop
	fi
}
service_init()
{
	SYSCFG_FAILED='false'
	FOO=`utctx_cmd get guest_enabled wl0_guest_enabled wl1_guest_enabled wl0_state wl1_state guest_lan_netmask guest_subnet guest_lan_ifname guest_lan_ipaddr guest_lan_netmask wl0_guest_vap wl1_guest_vap bridge_mode`
	eval $FOO
	if [ $SYSCFG_FAILED = 'true' ] ; then
		ulog guest_access status "$PID utctx failed to get some configuration data"
		ulog guest_access status "$PID GUEST ACCESS CANNOT BE CONTROLLED"
		exit
	fi
}
service_start ()
{
    if [ "0" != "$SYSCFG_bridge_mode" ] ; then
        ulog guest_access status "don't start ${SERVICE_NAME} in bridge mode"
        return
    fi
	if [ "0" = "$SYSCFG_guest_enabled" ] ; then
		sysevent set guest_access-status disabled
		return
	fi
	if [ "down" = "$SYSCFG_wl0_state" ] && [ "down" = "$SYSCFG_wl1_state" ] ; then	
		sysevent set guest_access-status stopped
		return
	fi
	wait_till_end_state ${SERVICE_NAME}
	STATUS=`sysevent get ${SERVICE_NAME}-status`
	if [ "started" != "$STATUS" ] ; then
		sysevent set ${SERVICE_NAME}-status starting
		do_start
		sysevent set ${SERVICE_NAME}-status started
		if [ "0" != "$SYSCFG_guest_enabled" ] && [ "$SYSCFG_bridge_mode" != "0" ] ; then
			echo 1 > /proc/sys/net/ipv4/ip_forward
		fi
		sysevent set firewall-restart
	fi
}
service_stop ()
{
	wait_till_end_state ${SERVICE_NAME}
	STATUS=`sysevent get ${SERVICE_NAME}-status` 
	if [ "stopped" != "$STATUS" ] ; then
		sysevent set ${SERVICE_NAME}-status stopping
		do_stop
		sysevent set ${SERVICE_NAME}-status stopped
	fi
    sysevent set firewall-restart
}
ulog wlan status "${SERVICE_NAME}, sysevent received: $1"
service_init 
case "$1" in
	${SERVICE_NAME}-start)
		service_start
		;;
	${SERVICE_NAME}-stop)
		service_stop
		;;
	${SERVICE_NAME}-restart)
		service_stop
		service_start
		;;
	*)
        echo $1
		echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
		exit 3
		;;
esac
