#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
do_start_pppoe() {
   ulog pppoe_link status "$PID setting ${NAMESPACE}_current_ipv4_link_state up"
   sysevent set ${NAMESPACE}_current_ipv4_link_state up
}
do_stop_pppoe() {
   ulog pppoe_link status "$PID setting ${NAMESPACE}_current_ipv4_link_state down"
   sysevent set ${NAMESPACE}_current_ipv4_link_state down
}
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
service_init $1
ulog pppoe_link status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog pppoe_link status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog pppoe_link status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   phylink_wan_state)
      ulog pppoe_link status "$PID physical link is $SYSEVENT_phylink_wan_state"
      if [ "up" != "$SYSEVENT_phylink_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
            ulog pppoe_link status "$PID physical link is down. Setting link down."
            do_stop_pppoe
            exit 0
         else
            ulog pppoe_link status "$PID physical link is down. Link is already down."
            exit 0
         fi
      else
         if [ "up" = "SYSEVENT_current_ipv4_link_state" ] ; then
            ulog pppoe_link status "$PID physical link is up. Link is already up."
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
                  ulog pppoe_link status "$PID starting pppoe link"
                  do_start_pppoe
                  exit 0
            else
               ulog pppoe_link status "$PID physical link is up, but desired link state is down."
               exit 0;
            fi
         fi
      fi
      ;;
   desired_ipv4_link_state)
      if [ "$SYSEVENT_current_ipv4_link_state" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         ulog pppoe_link status "$PID wan is already is desired state $SYSEVENT_current_ipv4_link_state"
         exit
      fi
      if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         if [ "down" = "$SYSEVENT_phylink_wan_state" ] ; then
            ulog pppoe_link status "$PID up requested but physical link is still down"
            exit 0;
         fi
         ulog pppoe_link status "$PID up requested"
         do_start_pppoe
         exit 0
      else
         ulog pppoe_link status "$PID down requested"
         do_stop_pppoe
         exit 0
      fi
      ;;
  *)
        ulog pppoe_link status "$PID Invalid parameter $1 "
        exit 3
        ;;
esac
