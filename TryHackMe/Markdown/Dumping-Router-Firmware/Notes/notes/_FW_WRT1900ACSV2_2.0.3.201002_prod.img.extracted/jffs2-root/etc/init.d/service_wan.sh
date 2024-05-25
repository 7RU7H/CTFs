#!/bin/sh
source /etc/init.d/event_flags
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_wan/ppp_helpers.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
if [ -f /etc/init.d/brcm_ethernet_helper.sh ]; then
    source /etc/init.d/brcm_ethernet_helper.sh
fi
SERVICE_NAME="wan"
PID="($$)"
WAN_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$WAN_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
echo "${SERVICE_NAME}, sysevent received: $1"  1>&2
WAN_STATUS_MONITOR_HANDLER="/etc/init.d/service_wan/wan_status_monitor.sh"
init_wan_namespace()
{
   if [ -z "$1" ] ; then
      return 255
   else
      wan_info_by_namespace $1
      RET=$?
      if [ 0 != "$RET" ] ; then
         return $RET
      fi
   fi
   NAMESPACE=$1
   return 0
}
install_wan_status_monitor () {
   STATUS=`sysevent get wan_status_monitor_installed`
   if [ "yes" = "$STATUS" ] ; then
      return 0
   fi
   NUM=`syscfg get max_wan_count`
   if [ -z "$NUM" ] ; then 
      NUM=1
   fi
   COUNT=1
   while [ "$COUNT" -le "$NUM" ] ; do
      async_id=`sysevent async wan_${COUNT}-status $WAN_STATUS_MONITOR_HANDLER`
      sysevent setoptions wan_${COUNT}-status $TUPLE_FLAG_EVENT
      ulog wan status "$PID starting wan_status monitoring for wan_${COUNT}-status"
      WAN_PROTO=`syscfg get wan_${COUNT} wan_proto`
      if [ "legacy" = "$WAN_PROTO" ] ; then
         WAN_PROTO=`syscfg get wan_proto`
      fi
      COUNT=`expr $COUNT + 1`
   done
   sysevent set wan_status_monitor_installed yes
}
set_wan_mtu() {
    WAN_MTU=$SYSCFG_wan_mtu
    if [ "" = "$WAN_MTU" ] || [ "0" = "$WAN_MTU" ] ; then
      case "$SYSCFG_wan_proto" in
        dhcp | static)
          WAN_MTU=1500
          DEF_WAN_MTU=$WAN_MTU
          ;;
        pppoe)
          WAN_MTU=1492
          DEF_WAN_MTU=`expr $WAN_MTU + 8`
          ;;
        pptp | l2tp)
          WAN_MTU=1460
          DEF_WAN_MTU=`expr $WAN_MTU + 40`
          ;;
        dslite)
          WAN_MTU=1460
          DEF_WAN_MTU=`expr $WAN_MTU + 40`
          ;;
        *)
          ulog wan status "$PID called with incorrect wan protocol $SYSCFG_wan_proto. Aborting"
          return 3
          ;;
        esac
    else
      case "$SYSCFG_wan_proto" in
        dhcp | static)
          if [ "$WAN_MTU" -gt 1500 ]; then
             WAN_MTU=1500
          fi
          DEF_WAN_MTU=$WAN_MTU
          ;;
        pppoe)
          if [ "$WAN_MTU" -gt 1492 ]; then
             WAN_MTU=1492
          fi
          DEF_WAN_MTU=`expr $WAN_MTU + 8`
          ;;
        pptp | l2tp)
          if [ "$WAN_MTU" -gt 1460 ]; then
             WAN_MTU=1460
          fi
          DEF_WAN_MTU=`expr $WAN_MTU + 40`
          ;;
        dslite)
          if [ "$WAN_MTU" -gt 1460 ]; then
             WAN_MTU=1460
          fi
          DEF_WAN_MTU=`expr $WAN_MTU + 40`
          ;;
        *)
          echo "[utopia] wanControl.sh error: called with incorrect wan protocol " $SYSCFG_wan_proto > /dev/console
          return 3
          ;;
      esac
    fi
    case "$SYSCFG_wan_proto" in
      pppoe | pptp | l2tp)
        WAN_BSS=`expr $WAN_MTU - 40`
        sysevent set ppp_clamp_mtu $WAN_BSS
        ;;
      dslite)
        echo "[utopia] Not setting ppp_clamp_mtu on dslite protocol" > /dev/console
        ;;
      *)
         echo "[utopia] Not setting ppp_clamp_mtu" > /dev/console
        ;;
    esac
   if [ -n "$SYSEVENT_current_wan_ifname" -a "dslite" != "$SYSEVENT_current_wan_ifname" ] ; then
      ip -4 link set $SYSEVENT_current_wan_ifname mtu $DEF_WAN_MTU
   fi
   return 0
}
clone_mac_addr ()
{
    if [ "$SYSCFG_def_hwaddr" != "" ] && [ "$SYSCFG_def_hwaddr" != "00:00:00:00:00:00" ]; then
        ulog wan status "$PID change wan mac addr for interface($SYSEVENT_current_wan_ifname) to $SYSCFG_def_hwaddr"
        if [ -n "$SYSCFG_hardware_vendor_name" -a "$SYSCFG_hardware_vendor_name" = "Broadcom" ] ; then
            /sbin/macclone $SYSEVENT_current_wan_ifname $SYSCFG_def_hwaddr
        else
            ip link set $SYSEVENT_current_wan_ifname down
            ip link set $SYSEVENT_current_wan_ifname address $SYSCFG_def_hwaddr
            ip link set $SYSEVENT_current_wan_ifname up
        fi
        sysevent set wan_mac_clone_active 1
    fi
}
restore_mac_addr ()
{
    MACCLONE_ACTIVE=`sysevent get wan_mac_clone_active`
    if [ -n "$MACCLONE_ACTIVE" -a "1" = "$MACCLONE_ACTIVE" ] ; then
        ulog wan status "$PID restore wan mac addr for interface($SYSEVENT_current_wan_ifname) to factory $WAN_IFNAME_MAC"
        if [ -n "$SYSCFG_hardware_vendor_name" -a "$SYSCFG_hardware_vendor_name" = "Broadcom" ] ; then
           WAN_IFNAME_MAC=`nvram get hw_mac_addr`
           if [ -n "$WAN_IFNAME_MAC" ] ; then
               /sbin/macclone $SYSEVENT_current_wan_ifname $WAN_IFNAME_MAC
           fi
        else
            if [ "`cat /etc/product`" = "viper" -o "`cat /etc/product`" = "audi" ] ; then
                WAN_IFNAME_MAC=`fw_printenv | grep eth1addr | cut -d'=' -f2`
            else
                WAN_IFNAME_MAC=`syscfg get wan_mac_addr`
            fi
            if [ -n "$WAN_IFNAME_MAC" ] ; then
                ip link set $SYSEVENT_current_wan_ifname address $WAN_IFNAME_MAC
            fi
        fi
        sysevent set wan_mac_clone_active 0
    fi
}
unregister_dhcp_client_handlers() {
   if [ -z "$1" ] ; then
      ulog wan status "$PID unregister_dhcp_client_handlers called without parameter. Ignoring"
      return
   else
      ulog wan status "$PID unregister_dhcp_client_handlers for wan $1"
   fi
   asyncid1=`sysevent get ${1}_async_id_1`;
   if [ -n "$asyncid1" ] ; then
      sysevent rm_async $asyncid1
      sysevent set ${1}_async_id_1
   fi
   asyncid2=`sysevent get ${1}_async_id_2`;
   if [ -n "$asyncid2" ] ; then
      sysevent rm_async $asyncid2
      sysevent set ${1}_async_id_2
   fi
   asyncid3=`sysevent get ${1}_async_id_3`;
   if [ -n "$asyncid3" ] ; then
      sysevent rm_async $asyncid3
      sysevent set ${1}_async_id_3
   fi
   asyncid4=`sysevent get ${1}_async_id_4`;
   if [ -n "$asyncid4" ] ; then
      sysevent rm_async $asyncid4
      sysevent set ${1}_async_id_4
   fi
}
register_dhcp_client_handlers() {
   if [ -z "$1" ] ; then
      ulog wan status "$PID register_dhcp_client_handlers called without parameters $1,$2. Ignoring"
      return
   else
      ulog wan status "$PID register_dhcp_client_handlers for wan $1"
   fi
   asyncid1=`sysevent async dhcp_client-restart /etc/init.d/service_wan/dhcp_link.sh $1`
   sysevent setoptions dhcp_client-restart $TUPLE_FLAG_EVENT
   sysevent set ${1}_async_id_1 "$asyncid1"
   asyncid2=`sysevent async dhcp_client-release /etc/init.d/service_wan/dhcp_link.sh $1`
   sysevent setoptions dhcp_client-release $TUPLE_FLAG_EVENT
   sysevent set ${1}_async_id_2 "$asyncid2"
   asyncid3=`sysevent async dhcp_client-renew /etc/init.d/service_wan/dhcp_link.sh $1`
   sysevent setoptions dhcp_client-renew $TUPLE_FLAG_EVENT
   sysevent set ${1}_async_id_3 "$asyncid3"
   asyncid4=`sysevent async dhcp_client-release_renew /etc/init.d/service_wan/dhcp_link.sh $1`
   sysevent setoptions dhcp_client-release_renew $TUPLE_FLAG_EVENT
   sysevent set ${1}_async_id_4 "$asyncid4"
}
unregister_handlers() {
   unregister_dhcp_client_handlers $1
   asyncid=`sysevent get dhcp_default_router_changed_asyncid`
   if [ -n "$asyncid" ] ; then
       sysevent rm_async $asyncid
       sysevent set dhcp_default_router_changed_asyncid
   fi
   if [ -z "$1" ] ; then
      ulog wan status "$PID unregister_handlers called without parameter. Ignoring"
      return
   else
      ulog wan status "$PID unregister_handlers for wan $1"
   fi
   asyncid=`sysevent get ${1}_phylink_wan_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${1}_phylink_wan_state_asyncid
   fi
   asyncid=`sysevent get ${1}_desired_ipv4_link_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${1}_desired_ipv4_link_state_asyncid
   fi
   asyncid=`sysevent get ${1}_desired_ipv4_wan_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${1}_desired_ipv4_wan_state_asyncid
   fi
   asyncid=`sysevent get ${1}_current_ipv4_link_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${1}_current_ipv4_link_state_asyncid
   fi
}
register_handlers() {
   if [ -z "$1" ] ; then
      ulog wan status "$PID register_handlers called without parameter. Ignoring"
      return
   else
      ulog wan status "$PID register_handlers for wan $1"
   fi
   sysevent setoptions ${1}_desired_ipv4_link_state $TUPLE_FLAG_EVENT
   sysevent setoptions ${1}_desired_ipv4_wan_state $TUPLE_FLAG_EVENT
   if [ "pppoe" = "$SYSCFG_wan_proto" -o "pptp" = "$SYSCFG_wan_proto" -o "l2tp" = "$SYSCFG_wan_proto" -o "dhcp" = "$SYSCFG_wan_proto" -o "static" = "$SYSCFG_wan_proto" ] ; then
      HW_VENDOR=`syscfg get hardware_vendor_name`
      if [ -n "$HW_VENDOR" -a "$HW_VENDOR" = "Broadcom" ] ; then
          nvram set wan_proto="$SYSCFG_wan_proto"
          nvram commit
      fi
   fi
   case "$SYSCFG_wan_proto" in
      dhcp)
         ulog wan status "$PID installing handlers for dhcp_link and dhcp_wan $1"
         if [ "1" = "$SYSCFG_default" ] ; then
            register_dhcp_client_handlers $1
            asyncid=`sysevent async dhcp_default_router_changed /etc/init.d/service_wan/wan_status_monitor.sh`
            sysevent setoptions dhcp_default_router_changed $TUPLE_FLAG_EVENT
            sysevent set dhcp_default_router_changed_asyncid "$asyncid"
         fi
         asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/dhcp_link.sh`
         sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/dhcp_link.sh`
         sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/dhcp_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/dhcp_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      static)
         ulog wan status "$PID installing handlers for static_link and static_wan $1"
         asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/static_link.sh`
         sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/static_link.sh`
         sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/static_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/static_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      pppoe)
         ulog wan status "$PID installing handlers for pppoe_link and pppoe_wan $1"
         asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/pppoe_link.sh`
         sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/pppoe_link.sh`
         sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/pppoe_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/pppoe_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      pptp)
         if [ "0" != "$SYSCFG_pptp_address_static" ] ; then
            ulog wan status "$PID installing handlers for static_link and pptp_wan $1"
            asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/static_link.sh`
            sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
            asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/static_link.sh`
            sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         else
            ulog wan status "$PID installing handlers for dhcp_link and pptp_wan"
            if [ "1" = "$SYSCFG_default" ] ; then
               register_dhcp_client_handlers $1 
            fi
            asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/dhcp_link.sh`
            sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
            asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/dhcp_link.sh`
            sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         fi
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/pptp_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/pptp_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      l2tp)
         if [ "0" != "$SYSCFG_l2tp_address_static" ] ; then
            ulog wan status "$PID installing handlers for static_link and l2tp_wan $1"
            asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/static_link.sh`
            sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
            asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/static_link.sh`
            sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         else
            ulog wan status "$PID installing handlers for dhcp_link and l2tp_wan"
            if [ "1" = "$SYSCFG_default" ] ; then
               register_dhcp_client_handlers $1 
            fi
            asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/dhcp_link.sh`
            sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
            asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/dhcp_link.sh`
            sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         fi
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/l2tp_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/l2tp_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      dslite)
         ulog wan status "$PID installing handlers for dslite_link and dslite_wan $1"
         asyncid=`sysevent async ${1}_phylink_wan_state /etc/init.d/service_wan/dslite_link.sh`
         sysevent set ${1}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_link_state /etc/init.d/service_wan/dslite_link.sh`
         sysevent set ${1}_desired_ipv4_link_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_desired_ipv4_wan_state /etc/init.d/service_wan/dslite_wan.sh`
         sysevent set ${1}_desired_ipv4_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async ${1}_current_ipv4_link_state /etc/init.d/service_wan/dslite_wan.sh`
         sysevent set ${1}_current_ipv4_link_state_asyncid "$asyncid"
         ;;
      telstra)
         ulog wan status "$PID telstra_wan is deprecated. Not setting up interface $1"
         ;;
   esac
}
ipv4_wan_down() {
   ulog wan status "$PID bringing wan service $1 down"
   sysevent set ${1}-status stopping
   sysevent set ${1}-errinfo
   ulog wan status "$PID setting ${1}_desired_ipv4_wan_state down"
   sysevent set ${1}_desired_ipv4_wan_state down
   ulog wan status "$PID setting ${1}_desired_ipv4_link_state down"
   sysevent set ${1}_desired_ipv4_link_state down
   if [ "pppoe" = "$SYSCFG_wan_proto" -o "pptp" = "$SYSCFG_wan_proto" -o "l2tp" = "$SYSCFG_wan_proto" ] ; then
      WMON_STATE=`sysevent get wmon_state`
      while [ "started" = "$WMON_STATE" ] ; do
         sleep 1
         WMON_STATE=`sysevent get wmon_state`
      done
   fi
   DONE=`sysevent get ${1}_current_ipv4_wan_state`
   while [ "up" = "$DONE" ]
   do
      sleep 1
      DONE=`sysevent get ${1}_current_ipv4_wan_state`
   done
   DONE=`sysevent get ${1}_current_ipv4_link_state`
   while [ "up" = "$DONE" ]
   do
      sleep 1
      DONE=`sysevent get ${1}_current_ipv4_link_state`
   done
   unregister_handlers $1
   ORIG_wan_ifname=`syscfg get ${1}::ifname`
   if [ -n "$ORIG_wan_ifname" -a "$ORIG_wan_ifname" != "$SYSEVENT_current_wan_ifname" ] ; then 
      SAVE=$SYSEVENT_current_wan_ifname
      SYSEVENT_current_wan_ifname=$ORIG_wan_ifname
      restore_mac_addr
      ip -4 addr flush dev $SYSEVENT_current_wan_ifname
      SYSEVENT_current_wan_ifname=$SAVE
   else
      if [ "" != "$SYSEVENT_current_wan_ifname" ] ; then
         restore_mac_addr
         ip -4 addr flush dev $SYSEVENT_current_wan_ifname
      fi
   fi
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set current_wan_ifname 
   fi
   sysevent set ${1}_current_wan_ifname
   syscfg get wan_proto | egrep -qi Static
   if [ "$?" -eq "0" ] ; then
      return 0
   fi
   WAN_TYPE=`syscfg get wan_proto`
   STATIC=`syscfg get ${WAN_TYPE}_address_static`
   if [ "$STATIC" = "1" ] ; then
      return 0
   fi
   if [ "$SYSCFG_default" = "1" ] ; then
      sysevent set dhcp_domain
      sysevent set wan_dynamic_dns
      syscfg unset nameserver1
      syscfg unset nameserver2
      syscfg unset nameserver3
   fi
   prepare_resolver_conf
   sysevent set ${SYSEVENT_current_wan_ifname}_syscfg_namespace
}
ipv4_wan_up() {
   install_wan_status_monitor
   ulog wan status "$PID Bringing up wan interface $SYSCFG_ifname"
   sysevent set interface-start $SYSCFG_ifname
   MAX_TRIES=20
   TRIES=1
   while [ "$MAX_TRIES" -gt "$TRIES" ] ; do
      STATUS=`sysevent get ${SYSCFG_ifname}-status`
      if [ "started" = "$STATUS" ] ; then
         TRIES=$MAX_TRIES
      else
         sleep .5
         TRIES=`expr $TRIES + 1`
      fi
   done
   ulog wan status "$PID interface $SYSCFG_ifname status: $STATUS"
   sysevent set ${1}-status starting
   S=`sysevent get ${1}_current_ipv4_wan_state`
   if [ -z "$S" ] ; then
      sysevent set ${1}_current_ipv4_wan_state down
   fi
   S=`sysevent get ${1}_current_ipv4_link_state`
   if [ -z "$S" ] ; then
      sysevent set ${1}_current_ipv4_link_state down
   fi
   if [ -z "$SYSCFG_wan_virtual_ifnum" -o "-1" = "$SYSCFG_wan_virtual_ifnum" ] ; then
      SYSCFG_wan_virtual_ifnum=
      SYSEVENT_current_wan_ifname=$SYSCFG_ifname
   else
        SYSEVENT_current_wan_ifname="vlan${SYSCFG_wan_virtual_ifnum}"
   fi
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set current_wan_ifname $SYSEVENT_current_wan_ifname
   fi
   sysevent set ${1}_current_wan_ifname $SYSEVENT_current_wan_ifname
   sysevent set ${1}_phylink_wan_state `sysevent get phylink_wan_state`
   register_handlers $1
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set ipv4_wan_ipaddr 0.0.0.0
   fi
   sysevent set ${1}_ipv4_wan_ipaddr 0.0.0.0
   set_wan_mtu
   if [ "$?" -ne "0" ] ; then
      return 3 
   fi
   clone_mac_addr
   sysevent set ${SYSEVENT_current_wan_ifname}_syscfg_namespace  $NAMESPACE
   ulog wan status "$PID setting ${1}_desired_ipv4_link_state up for $1"
   sysevent set ${1}_desired_ipv4_link_state up
   ulog wan status "$PID setting ${1}_desired_ipv4_wan_state up for $1"
   sysevent set ${1}_desired_ipv4_wan_state up
}
service_start ()
{
   MAX_TRIES=10
   TRIES=1
   while [ "$MAX_TRIES" -gt "$TRIES" ] ; do
      STATUS=`sysevent get ${NAMESPACE}-status`
      if [ "starting" = "$STATUS" -o "stopping" = "$STATUS" ] ; then
         ulog wan status "$PID service_start waiting for ${NAMESPACE}-status to change from $STATUS. Try ${TRIES} of ${MAX_TRIES}"
         sleep 1
         TRIES=`expr $TRIES + 1`
      else
         TRIES=$MAX_TRIES
      fi
   done
   STATUS=`sysevent get ${NAMESPACE}-status`
   if [ "started" != "$STATUS" ] ; then
      ipv4_wan_up $NAMESPACE
   fi
}
service_stop ()
{
   MAX_TRIES=10
   TRIES=1
   while [ "$MAX_TRIES" -gt "$TRIES" ] ; do
      STATUS=`sysevent get ${NAMESPACE}-status`
      if [ "starting" = "$STATUS" -o "stopping" = "$STATUS" ] ; then
         ulog wan status "$PID service_stop waiting for ${NAMESPACE}-status to change from $STATUS. Try ${TRIES} of ${MAX_TRIES}"
         sleep 1
         TRIES=`expr $TRIES + 1`
      else
         TRIES=$MAX_TRIES
      fi
   done
   STATUS=`sysevent get ${NAMESPACE}-status`
   ulog wan status "$PID service_stop read state $STATUS"
   if [ "stopped" != "$STATUS" ] ; then
      ipv4_wan_down $NAMESPACE
   fi
}
iterator ()
{
   if [ -n "$2" ] ; then
      init_wan_namespace $2
      if [ 0 = "$?" -a -n "$SYSCFG_wan_proto" -a "none" != "$SYSCFG_wan_proto" ] ; then
         case "$1" in
            start)
               service_start
               ;;
            stop)
               service_stop
               ;;
         esac
      fi
   else
      MAX_WAN_COUNT=`syscfg get max_wan_count`
      if [ -z "$MAX_WAN_COUNT" ] ; then
         MAX_WAN_COUNT=0
      fi
      CUR_WAN_COUNT=1
      while [ $MAX_WAN_COUNT -ge $CUR_WAN_COUNT ] ; do
         i="wan_"${CUR_WAN_COUNT}
         CUR_WAN_COUNT=`expr $CUR_WAN_COUNT + 1`
         init_wan_namespace $i
         if [ 0 = "$?" -a -n "$SYSCFG_wan_proto" -a "none" != "$SYSCFG_wan_proto" ] ; then
            case "$1" in
               start)
                  service_start
                  ;;
               stop)
                  service_stop
                  ;;
               phylink_wan_state)
                  sysevent set ${i}_phylink_wan_state `sysevent get phylink_wan_state`
                  ;;
            esac
         fi
      done
   fi
}
PARAM=
if [ -n "$2" -a "NULL" != "$2" ] ; then
   PARAM=$2  
fi
case "$1" in
   ${SERVICE_NAME}-start)
      ulog wan status "$PID Received request to $1 $2"
      iterator start $PARAM
      echo 1 > /proc/sys/net/ipv4/ip_forward
      ;;
   ${SERVICE_NAME}-stop)
      ulog wan status "$PID Received request to $1 $2"
      echo 0 > /proc/sys/net/ipv4/ip_forward
      iterator stop $PARAM
      ;;
   ${SERVICE_NAME}-restart)
      ulog wan status "$PID Received request to $1 $2"
      echo 0 > /proc/sys/net/ipv4/ip_forward
      ulog wan status "$PID Calling stop"
      iterator stop $PARAM
      ulog wan status "$PID Calling start"
      iterator start $PARAM
      echo 1 > /proc/sys/net/ipv4/ip_forward
      ;;
   phylink_wan_state)
      iterator phylink_wan_state
      ;;
    populate_etc_resolv_conf)
        prepare_resolver_conf
        ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
