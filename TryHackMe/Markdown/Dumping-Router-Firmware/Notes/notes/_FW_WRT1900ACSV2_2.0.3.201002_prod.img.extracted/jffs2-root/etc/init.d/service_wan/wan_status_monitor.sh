#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
NUM=`syscfg get max_wan_count`
if [ -z "$NUM" ] ; then 
   NUM=1
fi
COUNT=1
NUM_STOPPED=0
NUM_VALID=0
DEFAULT_WAN=
WAN_STATUS="stopped"
while [ "$COUNT" -le "$NUM" ] ; do
   STATUS=`sysevent get wan_${COUNT}-status`
   if [ -n "$STATUS" ] ; then
      if [ "$STATUS" = "starting" -a "`syscfg get wan_${COUNT} default`" = "1" ] ; then
	 WAN_STATUS=$STATUS
      fi
      if [ "started" = "$STATUS" -o "stopped" = "$STATUS" ] ; then
         if [ "stopped" = "$STATUS" ] ; then
            WAN_PROTO=`syscfg get wan_${COUNT} wan_proto`
            if [ "legacy" = "$WAN_PROTO" ] ; then
               WAN_PROTO=`syscfg get wan_proto`
            fi
         fi
         NUM_VALID=`expr $NUM_VALID + 1`
         if [ "stopped" = "$STATUS" ] ; then
            NUM_STOPPED=`expr $NUM_STOPPED + 1`
         else
            WAN_STATUS="started"
            if [ "1" = "`syscfg get wan_${COUNT} default`" ] ; then
               DEFAULT_WAN="wan_${COUNT}"
            elif [ -z "$DEFAULT_WAN" ] ; then
               DEFAULT_WAN="wan_${COUNT}"
            fi
         fi
      fi
   fi
   COUNT=`expr $COUNT + 1`
done
case "$1" in
	dhcp_default_router_changed)
        if [ -z "$DEFAULT_WAN" ] ; then
            return 0;
        fi
        wan_info_by_namespace $DEFAULT_WAN
        if [ "$SYSCFG_wan_proto" != "dhcp" -o "$SYSCFG_default" != 1 ] ; then
            return 0;
        fi
        if [ -z "$SYSEVENT_current_wan_ifname" ] ; then
            return 0;
        fi
        ROUTER=`ip -4 route list dev $SYSEVENT_current_wan_ifname | grep default`
        ROUTER=`echo $ROUTER | cut -f3 -d' '`
        if [ -n "$ROUTER" -a "$ROUTER" != "$SYSEVENT_ipv4_default_router" ] ; then
		    ip -4 route del $ROUTER dev $SYSEVENT_current_wan_ifname
            ip -4 route del default dev $SYSEVENT_current_wan_ifname via $ROUTER
            ip -4 route add $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname
            ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
        fi
        sysevent set default_router $SYSEVENT_ipv4_default_router
        sysevent set firewall-restart
	;;
	*) 
	if [ "started" != "$WAN_STATUS" ] ; then
	   if [ "0" -lt "$NUM_STOPPED" -a "$NUM_STOPPED" = "$NUM_VALID" ] ; then
		  WAN_STATUS="stopped"
	   fi
	fi
if [ "stopped" = "$WAN_STATUS" ] ; then
   if [ "stopped" != "`sysevent get wan-status`" ] ; then
      SYSEVENT_current_wan=`sysevent get current_wan`
      wan_info_by_namespace $SYSEVENT_current_wan
      if [ -z "$SYSEVENT_current_wan_ifname" ] ; then
         ulog wan_status_monitor status "wan-status stop requested but cannot find info for $SYSEVENT_current_wan"
         return 0
      fi
      ip -4 route del default dev $SYSEVENT_current_wan_ifname
      ROUTE=`ip -4  route list  $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname | grep -v kernel`
      if [ -n "$ROUTE" ] ; then
         ip -4 route del $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname
         ip -4 route del default dev $SYSEVENT_current_wan_ifname
         ulog wan_status_monitor status "deleting routes to default router dev $SYSEVENT_current_wan_ifname"
      fi
      ip -4 route flush cache
      sysevent set current_wan_ifname
      sysevent set ipv4_wan_ipaddr
      sysevent set ipv4_wan_subnet
      sysevent set default_router
      sysevent set wan_start_time
      sysevent set current_wan
      sysevent set firewall-restart
      sysevent set wan-stopped
      sysevent set wan-status stopped
      ulog wan_status_monitor status "wan-status stopped on all $NUM_STOPPED wan interfaces"
   fi
   return 0 
elif [ "started" = "$WAN_STATUS" ] ; then
   wan_info_by_namespace $DEFAULT_WAN
   if [ -z "$SYSEVENT_current_wan_ifname" ] ; then
      ulog wan_status_monitor status "wan-status start requested but cannot find info for $DEFAULT_WAN"
      return 0
   fi
   NOTIFY=0
   if [ -n "$SYSEVENT_ipv4_default_router" -a "0.0.0.0" != "$SYSEVENT_ipv4_default_router" ] ; then
      ROUTE=`ip -4  route list  $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname`
      if [ -z "$ROUTE" ] ; then
         ip -4 route add $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname
         ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
         ulog wan_status_monitor status "adding routes to default router $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname"
         NOTIFY=1
      else
         ROUTER=`ip -4 route list dev $SYSEVENT_current_wan_ifname | grep default`
         ulog wan_status_monitor status "ROUTER is $ROUTER"
         if [ -n "$ROUTER" ] ; then
             VIA=`echo $ROUTER | cut -f2 -d ' '`
             ulog wan_status_monitor status "VIA is $VIA"
             if [ "$VIA" = "via" ] ; then
                 ulog wan_status_monitor status "with via" 
                 ROUTER=`echo $ROUTER | cut -f3 -d' '`
                 ulog wan_status_monitor status "ROUTER is $ROUTER"
                 if [ "$ROUTER" != "$SYSEVENT_ipv4_default_router" ] ; then
                     ip -4 route del $ROUTER dev $SYSEVENT_current_wan_ifname
                     ip -4 route del default dev $SYSEVENT_current_wan_ifname via $ROUTER
                     ulog wan_status_monitor status "deleting routes to default router $ROUTER dev $SYSEVENT_current_wan_ifname"
                     ip -4 route add $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname
                     ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
                     ulog wan_status_monitor status "adding routes to default router $SYSEVENT_ipv4_default_router dev $SYSEVENT_current_wan_ifname"
                     NOTIFY=1
                 fi
             else
                 ulog wan_status_monitor status "without via"
                 ulog wan_status_monitor status "no gateway is specified for default route"
                 ip -4 route del default dev $SYSEVENT_current_wan_ifname
                 ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
                 ulog wan_status_monitor status "gateway is updated for default route"
                 NOTIFY=1
             fi
         fi
      fi
   fi
   CURRENT_DEFAULT_DEV=`ip -4 route show | grep default`
   VIA=`echo $CURRENT_DEFAULT_DEV | cut -f2 -d' '`
   if [ "$VIA" = "via" ] ; then
      CURRENT_DEFAULT_DEV=`echo $CURRENT_DEFAULT_DEV | cut -f5 -d' '`
   else
      CURRENT_DEFAULT_DEV=`echo $CURRENT_DEFAULT_DEV | cut -f3 -d' '`
   fi
   if [ -z "$CURRENT_DEFAULT_DEV" ] ; then
      ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
      ip -4 route flush cache
      NOTIFY=1
      ulog wan_status_monitor status "wan-status adding default route dev $SYSEVENT_current_wan_ifname"
   elif [ -n "$CURRENT_DEFAULT_DEV" -a "$CURRENT_DEFAULT_DEV" != "$SYSEVENT_current_wan_ifname" ] ; then
      ip -4 route del default dev $CURRENT_DEFAULT_DEV
      ip -4 route add default dev $SYSEVENT_current_wan_ifname via $SYSEVENT_ipv4_default_router
      ip -4 route flush cache
      NOTIFY=1
      ulog wan_status_monitor status "wan-status changing default route dev $CURRENT_DEFAULT_DEV -> $SYSEVENT_current_wan_ifname"
   fi
   if [ "`sysevent get current_wan`" != "$DEFAULT_WAN" ] ; then
      sysevent set current_wan $DEFAULT_WAN
      NOTIFY=1
   fi
   sysevent set current_wan_ifname $SYSEVENT_current_wan_ifname 
   sysevent set default_router $SYSEVENT_ipv4_default_router
   sysevent set ipv4_wan_ipaddr $SYSEVENT_ipv4_wan_ipaddr
   sysevent set ipv4_wan_subnet $SYSEVENT_ipv4_wan_subnet
   sysevent set wan_start_time $SYSEVENT_wan_start_time 
   if [ "1" = "$NOTIFY" -o "started" != "`sysevent get wan-status`" ] ; then
      sysevent set firewall-restart
      sysevent set wan-started
      sysevent set wan-status started
      ulog wan_status_monitor status "wan-status started using $DEFAULT_WAN"
   fi
elif [ "starting" = "$WAN_STATUS" ] ; then
    sysevent set wan-status starting
fi
	;;
esac
