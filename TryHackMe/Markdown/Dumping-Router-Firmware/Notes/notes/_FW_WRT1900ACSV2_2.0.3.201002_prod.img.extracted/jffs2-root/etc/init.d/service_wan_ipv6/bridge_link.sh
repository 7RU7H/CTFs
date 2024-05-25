#!/bin/sh
source /etc/init.d/ulog_functions.sh
PID="($$)"
if [ "1" = "`syscfg get ipv6_verbose_logging`" ]
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
do_start_bridge() {
   sysevent set ipv6_connection_state "ipv6 link going up"
   sysevent set current_ipv6_link_state up
}
do_stop_bridge() {
   sysevent set ipv6_connection_state "ipv6 link going down"
   sysevent set current_ipv6_link_state down
}
CURRENT_STATE=`sysevent get current_ipv6_link_state`
DESIRED_STATE=`sysevent get desired_ipv6_link_state`
PHYLINK_STATE=`sysevent get phylink_wan_state`
if [ -z "$CURRENT_STATE" ] ; then
   sysevent set current_ipv6_link_state down
   CURRENT_STATE=down
fi
if [ "started" != "`sysevent get lan-status`" ] 
then
   ulog bridge_link status "$PID Deferring link up until lan-status is up."
   exit 0
fi
if [ "up" = "$DESIRED_STATE" -a "up" = "$PHYLINK_STATE" ] ; then
   if [ "down" = "$CURRENT_STATE" ] ; then
      ulog bridge_link status "$PID Starting bridge link."
      do_start_bridge
      exit 0
   fi
fi
if [ "down" = "$DESIRED_STATE" -o "down" = "$PHYLINK_STATE" ] ; then
   if [ "up" = "$CURRENT_STATE" ] ; then
      ulog bridge_link status "$PID Stopping bridge link."
      do_stop_bridge
      exit 0
   fi
fi
case "$1" in
   phylink_wan_state)
      ;;
   desired_ipv6_link_state)
      ;;
  lan-status|guest_access-status)
     ;;
  *)
        ulog bridge_link status "$PID Invalid parameter $1 "
        exit 3
        ;;
esac
