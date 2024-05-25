#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
DEFAULT_LOG_FILE="/tmp/udhcpc.log"
BBVERSION=`/bin/busybox | head -1 | awk '{print $2}' | sed -e 's/v//' | awk -F'.' '{print $1*10000+$2*100+$3}'`
service_init_for_dhcp_callback ()
{
   FOO=`utctx_cmd get hostname dhcp_option_6rd_enable`
   eval $FOO
  if [ -z "$SYSCFG_hostname" ] ; then
     SYSCFG_hostname="Utopia"
  fi
}
do_stop_dhcp() {
   if [ -z "$1" ] ; then
      ulog dhcp_link status "do_stop_dhcp called with no syscfg namespace. Ignoring."
      return
   else
      wan_info_by_namespace $1
   fi
   UDHCPC_PID_FILE=/var/run/${NAMESPACE}_udhcpc.pid
   if [ -f "$UDHCPC_PID_FILE" ] ; then
      UDHCPC_PID=`cat $UDHCPC_PID_FILE`
   else
      UDHCPC_PID=
   fi
   ulog dhcp_link status "stopping dhcp client on wan"
   if [ -n "$UDHCPC_PID" ] ; then
      kill -USR2 $UDHCPC_PID && kill $UDHCPC_PID 
      rm -f $UDHCPC_PID_FILE
   fi
   /sbin/ip -4 addr flush dev $SYSCFG_ifname
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr 0.0.0.0
   sysevent set ${NAMESPACE}_ipv4_wan_subnet 0.0.0.0
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set dhcpc_ntp_server1
      sysevent set dhcpc_ntp_server2
      sysevent set dhcpc_ntp_server3
      sysevent set ipv4_wan_ipaddr 0.0.0.0
      sysevent set ipv4_wan_subnet 0.0.0.0
      sysevent set default_router
      sysevent set wan_dhcp_lease
      sysevent set wan_dynamic_dns
      rm -f $DEFAULT_LOG_FILE
   fi
   LOG_FILE="/tmp/"${NAMESPACE}"_udhcpc.log"
   rm -f $LOG_FILE
   sysevent set ${NAMESPACE}_current_ipv4_link_state down
}
do_start_dhcp() {
   if [ -z "$1" ] ; then
      ulog dhcp_link status "do_start_dhcp called with no syscfg namespace. Ignoring."
      return
   else
      wan_info_by_namespace $1
   fi
   UDHCPC_PID_FILE=/var/run/${NAMESPACE}_udhcpc.pid
   UDHCPC_EXTRA_PARAMS="-O staticroutes"
   if [ -f "$UDHCPC_PID_FILE" ] ; then
      UDHCPC_PID=`cat $UDHCPC_PID_FILE`
   else
      UDHCPC_PID=
   fi
   if [ "1" = "$SYSCFG_default" ] ; then
      UDHCPC_SCRIPT=/etc/init.d/service_wan/default_dhcp_callbacks.sh
   else
      UDHCPC_SCRIPT=/etc/init.d/service_wan/non_default_dhcp_callbacks.sh
   fi
      
   if [ "pppoe" != "$SYSCFG_wan_proto" ] ; then
      UDHCP_PID=`pidof udhcpc`
      service_init_for_dhcp_callback
      if [ "1" = "$SYSCFG_dhcp_option_6rd_enable" ] ; then
	if [ $BBVERSION -gt 11502 ]; then
         UDHCPC_OPTIONS="-O ip6rd"
	else
         UDHCPC_OPTIONS="-O sixrd"
 	fi
      fi
      if [ -z "$UDHCPC_PID" ] ; then
         ulog dhcp_link status "starting dhcp client on wan ($SYSCFG_ifname)"
         udhcpc -R -S -b -i $SYSCFG_ifname -h $SYSCFG_hostname -p $UDHCPC_PID_FILE --arping -s $UDHCPC_SCRIPT $UDHCPC_OPTIONS $UDHCPC_EXTRA_PARAMS &
      elif [ -n "$UDHCPC_PID" -a "$UDHCPC_PID" != "`pidof udhcpc`" ] ; then
         ulog dhcp_link status "dhcp client $UDHCPC_PID died"
         do_stop_dhcp $1
         ulog dhcp_link status "restarting dhcp client on wan ($SYSCFG_ifname)"
         udhcpc -R -S -b -i $SYSCFG_ifname -h $SYSCFG_hostname -p $UDHCPC_PID_FILE --arping -s $UDHCPC_SCRIPT $UDHCPC_OPTIONS $UDHCPC_EXTRA_PARAMS &
      else
         ulog dhcp_link status "dhcp client is already active on wan ($SYSCFG_ifname) as $UDHCPC_PID"
      fi
   fi
}
do_release_dhcp() {
   if [ -z "$1" ] ; then
      ulog dhcp_link status "do_release_dhcp called with no syscfg namespace. Ignoring."
      return
   else
      wan_info_by_namespace $1
   fi
   UDHCPC_PID_FILE=/var/run/${NAMESPACE}_udhcpc.pid
   if [ -f "$UDHCPC_PID_FILE" ] ; then
      UDHCPC_PID=`cat $UDHCPC_PID_FILE`
   else
      UDHCPC_PID=
   fi
   ulog dhcp_link status "releasing dhcp lease on wan"
   if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
      do_stop_dhcp $1
   fi
}
do_renew_dhcp() {
   if [ -z "$1" ] ; then
      ulog dhcp_link status "do_renew_dhcp called with no syscfg namespace. Ignoring."
      return
   else
      wan_info_by_namespace $1
   fi
   UDHCPC_PID_FILE=/var/run/${NAMESPACE}_udhcpc.pid
   UDHCPC_EXTRA_PARAMS="-O staticroutes"
   if [ -f "$UDHCPC_PID_FILE" ] ; then
      UDHCPC_PID=`cat $UDHCPC_PID_FILE`
   else
      UDHCPC_PID=
   fi
   if [ "1" = "$SYSCFG_default" ] ; then
      UDHCPC_SCRIPT=/etc/init.d/service_wan/default_dhcp_callbacks.sh
   else
      UDHCPC_SCRIPT=/etc/init.d/service_wan/non_default_dhcp_callbacks.sh
      return
   fi
   if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
      ulog dhcp_link status "renewing dhcp lease on wan"
      do_stop_dhcp $1
      do_start_dhcp $1
   else
      ulog dhcp_link status "renewing dhcp lease on wan requested, but disallowed"
   fi
}
[ -z "$1" ] && ulog dhcp_link status "$PID called with no parameters. Ignoring call" && exit 1
PHYLINK_STATUS=`sysevent get phylink_wan_state`
case "$1" in
   dhcp_client-restart)
      ulog dhcp_link status "$PID dhcp_client-restart called with syscfg namespace $3"
      if [ -z "$3" ] ; then
         ulog dhcp_link status "$PID dhcp_client-restart called with no syscfg namespace. Ignoring."
         exit
      fi
      NAMESPACE=$3
      do_start_dhcp $3
      ;;
   dhcp_client-release)
      ulog dhcp_link status "$PID dhcp_client-release called with syscfg namespace $3"
      if [ -z "$3" ] ; then
         ulog dhcp_link status "$PID dhcp_client-release called with no syscfg namespace. Ignoring."
         exit
      fi
      NAMESPACE=$3
      do_release_dhcp $3
      ;;
   dhcp_client-renew)
      ulog dhcp_link status "$PID dhcp_client-renew called with syscfg namespace $3"
      if [ -z "$3" ] ; then
         ulog dhcp_link status "$PID dhcp_client-renew called with no syscfg namespace. Ignoring."
         exit
      fi
      if [ "$PHYLINK_STATUS" = "down" ] ; then
	     ulog dhcp_link status "$PID dhcp_client-renew called with phylink down. Ignoring."
	     exit
      fi
      NAMESPACE=$3
      do_renew_dhcp $3
      ;;
   dhcp_client-release_renew)
      ulog dhcp_link status "$PID dhcp_client-release_renew called with syscfg namespace $3"
      if [ -z "$3" ] ; then
         ulog dhcp_link status "$PID dhcp_client-release_renew called with no syscfg namespace. Ignoring."
         exit
      fi
      NAMESPACE=$3
      do_release_dhcp $3
      do_renew_dhcp $3
      ;;
   *)
      parse_wan_namespace_sysevent $1
      wan_info_by_namespace $NAMESPACE
      ulog dhcp_link status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
      ulog dhcp_link status "$PID ${NAMESPACE}_desired_ipv4_link_state is $SYSEVENT_desired_ipv4_link_state"
      ulog dhcp_link status "$PID ${NAMESPACE}_phylink_wan_state is $SYSEVENT_phylink_wan_state"
      case "$EVENT" in
         desired_ipv4_link_state)
            ulog dhcp_link status "$PID $SYSEVENT_desired_ipv4_link_state requested"
            if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
               if [ "down" = "$SYSEVENT_phylink_wan_state" ] ; then
                  ulog dhcp_link status "$PID up requested but physical link is still down"
                  exit 0;
               fi
  
               do_start_dhcp $NAMESPACE
               exit 0
            elif [ "down" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
               do_stop_dhcp $NAMESPACE
               exit 0
            fi
          ;;
          phylink_wan_state)
             ulog dhcp_link status "$PID physical link is $SYSEVENT_phylink_wan_state"
             UDHCPC_PID_FILE=/var/run/${NAMESPACE}_udhcpc.pid
             if [ "up" != "$SYSEVENT_phylink_wan_state" ] ; then
                if [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
                   ulog dhcp_link status "$PID physical link is down. Setting link down."
                   do_stop_dhcp $NAMESPACE
                   exit 0
                else
                    if [ -f "$UDHCPC_PID_FILE" ] ; then
                        do_stop_dhcp $NAMESPACE
                    else
                        ulog dhcp_link status "$PID physical link is down. Link is already down."
                    fi
                    exit 0
                fi
             else
                if [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
                   ulog dhcp_link status "$PID physical link is up. Link is already up."
                else
                   if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
                      if [ -f "$UDHCPC_PID_FILE" ] ; then
                         ulog dhcp_link status "$PID dhcp client is active"
                         IP=`sysevent get ipv4_wan_ipaddr`
                         if [ -n "$IP" ] ; then
                            if [ "0.0.0.0" = "$IP" ] ; then
                               ulog dhcp_link status "$PID dhcp client has not acquired an ip address yet. No change to link state"
                               exit 0
                            else
                               ulog dhcp_link status "$PID dhcp client has acquired an ip address ($IP). Setting link state up"
                               sysevent set ${NAMESPACE}_current_ipv4_link_state up
                               exit 0
                            fi
                         else
                            ulog dhcp_link status "$PID dhcp client has no ip address yet. No change to link state"
                            exit 0
                         fi
                      else
                         ulog dhcp_link status "$PID starting dhcp client"
                         WAN_STATUS=`sysevent get "$NAMESPACE"-status`
                         if [ "$WAN_STATUS" = "stopped" ] ; then
                             sysevent set "$NAMESPACE"-status starting
                         fi
                         do_start_dhcp $NAMESPACE
                         exit 0
                      fi
                   else
                      ulog dhcp_link status "$PID physical link is up, but desired link state is down."
                      exit 0;
                   fi
                fi
             fi
             ;;
       esac
       ;;
   esac
exit 0
