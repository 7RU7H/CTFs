#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
bring_wan_down() {
   ulog dhcp_wan status "$PID bring_wan_down"
   sysevent set ${NAMESPACE}_wan_start_time    
   sysevent set ${NAMESPACE}-errinfo        
   sysevent set ${NAMESPACE}-status stopped
   sysevent set ${NAMESPACE}_current_ipv4_wan_state down
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set default_router
      sysevent set wan_start_time
   fi
}
bring_wan_up() {
   ulog dhcp_wan status "$PID bring_wan_up"
   sysevent set ${NAMESPACE}-errinfo        
   sysevent set ${NAMESPACE}-status started
   sysevent set ${NAMESPACE}_current_ipv4_wan_state up
   sysevent set ${NAMESPACE}_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   fi
}
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
if [ -z "$1" ] ; then
   return
fi
service_init $1
ulog dhcp_wan status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog dhcp_wan status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog dhcp_wan status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   current_ipv4_link_state)
      ulog dhcp_wan status "$PID ipv4 link state is $SYSEVENT_current_ipv4_link_state"
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dhcp_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
         else
            ulog dhcp_wan status "$PID ipv4 link is down. Wan is already down. Bringing down again"
            bring_wan_down
         fi
         exit 0
      else 
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dhcp_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else 
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
               bring_wan_up
               exit 0
            else
               ulog dhcp_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then 
            ulog dhcp_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog dhcp_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dhcp_wan status "$PID wan is already down. Bringing down again."
            bring_wan_down
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog dhcp_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
