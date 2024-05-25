#!/bin/sh 
source /etc/init.d/ulog_functions.sh
PID="($$)"
if [ "1" = "`syscfg get ipv6_verbose_logging`" ]
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
do_start_default() {
   sysevent set ipv6_connection_state "ipv6 link going up"
   sysevent set current_ipv6_link_state up
}
do_stop_default() {
   sysevent set ipv6_connection_state "ipv6 link going down"
   sysevent set current_ipv6_link_state down
}
CURRENT_STATE=`sysevent get current_ipv6_link_state`
DESIRED_STATE=`sysevent get desired_ipv6_link_state`
PHYLINK_STATE=`sysevent get phylink_wan_state`
WAN_STATUS=`sysevent get wan-status`
if [ -z "$CURRENT_STATE" ] ; then
   sysevent set current_ipv6_link_state down
   CURRENT_STATE=down
fi
SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ipv6_ifname`
if [ -z "$SYSEVENT_current_wan_ipv6_ifname" ] ; then
   SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ifname`
   if [ -n "$SYSEVENT_current_wan_ipv6_ifname" ] ; then
      if [ "dslite" = "$CUR_NAME" ] ; then
         ulog default_ipv6_link status "$PID current_wan_ipv6_name is not set. Cannot set to dslite. Aborting ipv6 init"
         exit 0
      else
         ulog default_ipv6_link status "$PID current_wan_ipv6_name is not set. Setting internally to current_wan_ifname $SYSEVENT_current_wan_ipv6_ifname"
      fi
   else
      SYSEVENT_current_wan_ipv6_ifname=`syscfg get wan_1 ifname`
      if [ -n "$SYSEVENT_current_wan_ipv6_ifname" ] ; then
         ulog default_ipv6_link status "$PID current_wan_ipv6_name and current_wan_ifname not set. Using wan_1 ifname $SYSEVENT_current_wan_ipv6_ifname. Setting sysevent"
         sysevent set current_wan_ipv6_ifname $SYSEVENT_current_wan_ipv6_ifname
      else
         ulog default_ipv6_link status "$PID current_wan_ipv6_name is not set.  Neither is current_wan_ifname. aborting ipv6 init"
         exit 0
      fi
   fi
fi
PPP_IFNAME=`sysevent get _wan_ppp_ifname`
if [ "$PPP_IFNAME" = "$SYSEVENT_current_wan_ipv6_ifname" ] 
then
   if [ "started" != "$WAN_STATUS" ]
   then
      ulog default_ipv6_link status "$PID $SYSEVENT_current_wan_ipv6_ifname is not started ($WAN_STATUS). Deferring ipv6"
      sysevent set current_ipv6_link_state down
      CURRENT_STATE=down
      exit 0
   fi
fi
if [ "started" != "`sysevent get lan-status`" ]
then
   ulog default_ipv6_link status "$PID Deferring link up until lan-status is up."
   exit 0
fi
if [ "up" = "$DESIRED_STATE" -a "up" = "$PHYLINK_STATE"  ] ; then
   if [ "down" = "$CURRENT_STATE" ] ; then
      ulog default_ipv6_link status "$PID Starting ipv6 link."
      do_start_default
      exit 0
   fi
fi
if [ "down" = "$DESIRED_STATE" -o "down" = "$PHYLINK_STATE" ] ; then
   if [ "up" = "$CURRENT_STATE" ] ; then
      ulog default_ipv6_link status "$PID Stopping ipv6 link."
      do_stop_default
      exit 0
   fi
fi
case "$1" in
   phylink_wan_state)
      ;;
   desired_ipv6_link_state)
      ;;
   lan-status)
      ;;
   guest_access-status)
      ;;
   wan-status)
     ;;
  *)
        ulog default_ipv6_link status "$PID Invalid parameter $1 "
        exit 3
        ;;
esac
