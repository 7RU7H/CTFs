#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
bring_wan_down() {
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set wan_start_time 
      sysevent set ipv4_wan_ipaddr 
      sysevent set ipv4_wan_subnet 
      sysevent set default_router
   fi
   sysevent set ${NAMESPACE}_current_ipv4_wan_state down
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr 
   sysevent set ${NAMESPACE}_ipv4_wan_subnet 
   sysevent set ${NAMESPACE}_ipv4_default_router
   sysevent set ${NAMESPACE}_wan_start_time 
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status stopped
   sysevent set firewall-restart
}
bring_wan_up() {
   
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set default_router $SYSCFG_wan_default_gateway
      sysevent set ipv4_wan_ipaddr $SYSCFG_wan_ipaddr
      sysevent set ipv4_wan_subnet $SYSCFG_wan_netmask
      sysevent set wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   fi
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr $SYSCFG_wan_ipaddr
   sysevent set ${NAMESPACE}_ipv4_wan_subnet $SYSCFG_wan_netmask
   sysevent set ${NAMESPACE}_ipv4_default_router $SYSCFG_wan_default_gateway
   sysevent set ${NAMESPACE}_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   sysevent set ${NAMESPACE}_current_ipv4_wan_state up
   prepare_resolver_conf
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status started
   sysevent set firewall-restart
}
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
service_init $1
ulog static_wan status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog static_wan status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog static_wan status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   current_ipv4_link_state)
      ulog static_wan status "$PID ipv4 link state is $SYSEVENT_current_ipv4_link_state"
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog static_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog static_wan status "$PID ipv4 link is down. Wan is already down"
            exit 0
         fi
      else
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog static_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
               bring_wan_up
               exit 0
            else
               ulog static_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog static_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog static_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog static_wan status "$PID wan is already down. Bringing down again."
            bring_wan_down
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog static_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
