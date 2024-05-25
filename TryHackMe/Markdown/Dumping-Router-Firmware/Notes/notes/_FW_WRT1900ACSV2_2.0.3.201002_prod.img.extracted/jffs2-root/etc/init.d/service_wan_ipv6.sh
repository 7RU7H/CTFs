#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/ipv6_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
SERVICE_NAME="ipv6"
PID="($$)"
if [ "1" = "`syscfg get ipv6_verbose_logging`" ]
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
unregister_ipv6_handlers() {
   asyncid=`sysevent get ${SERVICE_NAME}_phylink_wan_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_wan-status_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_wan-status_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_desired_ipv6_link_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_lan-status_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_lan-status_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_guest_access-status_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_guest_access-status_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_current_ipv6_link_state_asyncid`
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid
   fi
}
register_ipv6_handlers() {
   sysevent setoptions desired_ipv6_link_state $TUPLE_FLAG_EVENT
   sysevent setoptions desired_ipv6_wan_state $TUPLE_FLAG_EVENT
   case "$IPV6_WAN_PROTO" in
      normal)
         ulog ipv6 status "$PID installing handlers for default_ipv6_link and default_ipv6_wan"
         asyncid=`sysevent async phylink_wan_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async wan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_wan-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_link_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid "$asyncid"
         asyncid=`sysevent async lan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_lan-status_asyncid "$asyncid"
         asyncid=`sysevent async guest_access-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_guest_access-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_wan_state /etc/init.d/service_wan_ipv6/default_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async current_ipv6_link_state /etc/init.d/service_wan_ipv6/default_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid "$asyncid"
         ;;
      bridge)
         ulog ipv6 status "$PID installing handlers for bridge_link and bridge_wan"
         asyncid=`sysevent async phylink_wan_state /etc/init.d/service_wan_ipv6/bridge_link.sh`
         sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_link_state /etc/init.d/service_wan_ipv6/bridge_link.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid "$asyncid"
         asyncid=`sysevent async lan-status /etc/init.d/service_wan_ipv6/bridge_link.sh`
         sysevent set ${SERVICE_NAME}_lan-status_asyncid "$asyncid"
         asyncid=`sysevent async guest_access-status /etc/init.d/service_wan_ipv6/bridge_link.sh`
         sysevent set ${SERVICE_NAME}_guest_access-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_wan_state /etc/init.d/service_wan_ipv6/bridge_wan.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async current_ipv6_link_state /etc/init.d/service_wan_ipv6/bridge_wan.sh`
         sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid "$asyncid"
         ;;
      6rd)
         ulog ipv6 status "$PID installing handlers for 6rd_ipv6_link and 6rd_ipv6_wan"
         asyncid=`sysevent async phylink_wan_state /etc/init.d/service_wan_ipv6/6rd_link.sh`
         sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async wan-status /etc/init.d/service_wan_ipv6/6rd_link.sh`
         sysevent set ${SERVICE_NAME}_wan-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_link_state /etc/init.d/service_wan_ipv6/6rd_link.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid "$asyncid"
         asyncid=`sysevent async lan-status /etc/init.d/service_wan_ipv6/6rd_link.sh`
         sysevent set ${SERVICE_NAME}_lan-status_asyncid "$asyncid"
         asyncid=`sysevent async guest_access-status /etc/init.d/service_wan_ipv6/6rd_link.sh`
         sysevent set ${SERVICE_NAME}_guest_access-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_wan_state /etc/init.d/service_wan_ipv6/6rd_wan.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async current_ipv6_link_state /etc/init.d/service_wan_ipv6/6rd_wan.sh`
         sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid "$asyncid"
         ;;
      static)
         ulog ipv6 status "$PID installing handlers for static_ipv6_link and static_ipv6_wan"
         asyncid=`sysevent async phylink_wan_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async wan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_wan-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_link_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid "$asyncid"
         asyncid=`sysevent async lan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_lan-status_asyncid "$asyncid"
         asyncid=`sysevent async guest_access-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_guest_access-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_wan_state /etc/init.d/service_wan_ipv6/static_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async current_ipv6_link_state /etc/init.d/service_wan_ipv6/static_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid "$asyncid"
         ;;
     *)
         ulog ipv6 status "$PID installing handlers for default_ipv6_link and default_ipv6_wan"
         asyncid=`sysevent async phylink_wan_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_phylink_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async wan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_wan-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_link_state /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_link_state_asyncid "$asyncid"
         asyncid=`sysevent async lan-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_lan-status_asyncid "$asyncid"
         asyncid=`sysevent async guest_access-status /etc/init.d/service_wan_ipv6/default_ipv6_link.sh`
         sysevent set ${SERVICE_NAME}_guest_access-status_asyncid "$asyncid"
         asyncid=`sysevent async desired_ipv6_wan_state /etc/init.d/service_wan_ipv6/default_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_desired_ipv6_wan_state_asyncid "$asyncid"
         asyncid=`sysevent async current_ipv6_link_state /etc/init.d/service_wan_ipv6/default_ipv6_wan.sh`
         sysevent set ${SERVICE_NAME}_current_ipv6_link_state_asyncid "$asyncid"
         ;;
   esac
}
ipv6_wan_down() {
   sysevent set ${SERVICE_NAME}-status stopping
   sysevent set ${SERVICE_NAME}-errinfo
   ulog ipv6 status "$PID tearing ipv6 wan down"
   ulog ipv6 status "$PID setting desired_ipv6_wan_state down"
   sysevent set desired_ipv6_wan_state down
   sleep 2
   ulog ipv6 status "$PID setting desired_ipv6_link_state down"
   sysevent set desired_ipv6_link_state down
   DONE=`sysevent get ipv6-status`
   while [ "stopped" != "$DONE" ]
   do
      sleep 1
      DONE=`sysevent get ipv6-status`
   done
   unregister_ipv6_handlers
   sysevent set ipv6_domain 
   sysevent set ipv6_nameserver
   prepare_resolver_conf
}
ipv6_wan_up() {
   sysevent set ${SERVICE_NAME}-status starting
   sysevent set ${SERVICE_NAME}-errinfo
   ulog ipv6 status "$PID bringing ipv6 wan up"
   if [ -z "$IPV6_WAN_PROTO" ] ; then
     sysevent set ${SERVICE_NAME}-status error
     sysevent set ${SERVICE_NAME}-errinfo "No ipv6 proto specified on wan"
   fi
   sysevent set dhcpv6_client_current_config
   WAN_IFNAME=`sysevent get current_wan_ifname`
   if [ "dslite" = "$WAN_IFNAME" ] ; then
      SYSCFG_hardware_vendor_name=`syscfg get hardware_vendor_name`
      if [ -n "$SYSCFG_hardware_vendor_name" -a "Broadcom" = "$SYSCFG_hardware_vendor_name" ] ; then
         SYSCFG_wan_virtual_ifnum=`syscfg get wan_virtual_ifnum`
         WAN_IFNAME="vlan${SYSCFG_wan_virtual_ifnum}"
      else
         SYSCFG_ifname=`syscfg get wan_physical_ifname`
         WAN_IFNAME="${SYSCFG_ifname}"
      fi
   fi
   if [ -z "$WAN_IFNAME" ] ; then
      WAN_IFNAME=`syscfg get wan_1 ifname`
   fi
   if [ -z "$WAN_IFNAME" ] ; then
      WAN_IFNAME=`syscfg get lan_ifname`
   fi
   
   if [ "0" = "$SYSCFG_6rd_enable" ] ; then
      sysevent set current_wan_ipv6_ifname $WAN_IFNAME 
   else
      sysevent set current_wan_ipv6_ifname tun6rd 
   fi
   unregister_ipv6_handlers
   register_ipv6_handlers
   sysevent set ipv6_connection_state "starting ipv6"
   ulog ipv6 status "$PID setting desired_ipv6_link_state up"
   sysevent set desired_ipv6_link_state up
   ulog ipv6 status "$PID setting desired_ipv6_wan_state up"
   sysevent set desired_ipv6_wan_state up
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get ipv6_enable wan_proto ipv6_bridging_enable ipv6_static_enable 6rd_enable ipv6_verbose_logging bridge_mode ipv6_ready`
   eval $FOO
   if [ $SYSCFG_FAILED = 'true' ] ; then
      ulog ipv6 status "$PID utctx failed to get some configuration data"
      ulog ipv6 status "$PID IPv6 wan cannot be controlled"
      exit
   fi
   if [ "1" = "$SYSCFG_ipv6_static_enable" ] ; then
      IPV6_WAN_PROTO=static
      sysevent set ipv6_connection_type Static
   elif [ "1" = "$SYSCFG_ipv6_bridging_enable" ] ; then
      IPV6_WAN_PROTO=bridge
   elif [ "1" = "$SYSCFG_bridge_mode" -o "2" = "$SYSCFG_bridge_mode" ] ; then
      IPV6_WAN_PROTO=bridge
      syscfg unset dhcpv6c_enable
      syscfg unset dhcpv6s_enable 
      syscfg unset router_adv_enable 
      sysevent set dhcpv6_server-stop
      sysevent set dhcpv6_client-stop
      sysevent set ipv6_connection_type Bridge
   elif [ "1" = "$SYSCFG_6rd_enable" ] ; then
      IPV6_WAN_PROTO=6rd
      sysevent set ipv6_connection_type "6rd Tunnel"
   else 
      IPV6_WAN_PROTO=normal
      sysevent set ipv6_connection_type "IPv6 - Automatic"
   fi
   ulog ipv6 status "$PID Using IPv6 wan protocol $IPV6_WAN_PROTO"
}
service_start ()
{
   echo "service_wan_ipv6::service_start called" >> $LOG
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      if [ "1" != "$SYSCFG_ipv6_ready" ] ; then
      	echo 1 > /proc/sys/net/ipv6/conf/all/forwarding
      fi
      ipv6_wan_up
   fi
   echo "service_wan_ipv6::service_start done" >> $LOG
}
service_stop ()
{
   echo "service_wan_ipv6::service_stop called" >> $LOG
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      echo 0 > /proc/sys/net/ipv6/conf/all/forwarding
      ipv6_wan_down
   fi
   echo "service_wan_ipv6::service_stop done" >> $LOG
}
if [ "0" = "`syscfg get ipv6_enable`" ] ; then
   service_stop
   exit 0
else
   echo "service_wan_ipv6.sh called with parameter $1"  >> $LOG
fi
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
   lan-start)
      service_start
      ;;
   lan-stop)
      service_stop
      ;;
   lan-restart)
      service_stop
      service_start
      ;;
   current_wan_ifname)
      CUR_IPV6_NAME=`sysevent get current_wan_ipv6_ifname`
      CUR_NAME=`sysevent get current_wan_ifname` 
      if [ "dslite" = "$CUR_NAME" ] ; then
         exit 0
      fi
      if [ -z "$CUR_NAME" ] ; then
         exit 0
      fi
      STATUS=`sysevent get ipv6-status`
      if [ -z "$CUR_IPV6_NAME" ] 
      then
         sysevent set current_wan_ipv6_ifname $CUR_NAME  
      elif [ "$CUR_IPV6_NAME" != "$CUR_NAME" ] 
      then
         if [ "started" != "$STATUS" ] 
         then
            sleep 15
         fi
            service_stop
            CUR_NAME=`sysevent get current_wan_ifname` 
            sysevent set current_wan_ipv6_ifname $CUR_NAME  
            service_start
      fi
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart] not ${1}" > /dev/console
      exit 3
      ;;
esac
