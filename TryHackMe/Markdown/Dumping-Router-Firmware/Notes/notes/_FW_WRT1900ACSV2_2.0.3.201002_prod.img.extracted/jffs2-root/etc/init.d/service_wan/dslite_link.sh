#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
PID="($$)"
DSLITE_WAITING_FOR_IPv6_WANADDR_ASYNC="dslite_ipv6_wanaddr_async"
DSLITE_WAITING_FOR_DHCP6_AFTR_ASYNC="dslite_dhcpv6_aftr_async"
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
do_start_dslite() {
   ASYNC=`sysevent get $DSLITE_WAITING_FOR_IPv6_WANADDR_ASYNC`
   if [ -z "$ASYNC" ] ; then
      ASYNC=`sysevent async current_wan_ipv6address /etc/init.d/service_wan/dslite_link.sh $NAMESPACE`
      sysevent set $DSLITE_WAITING_FOR_IPv6_WANADDR_ASYNC "$ASYNC"
   fi
   ASYNC=`sysevent get $DSLITE_WAITING_FOR_DHCP6_AFTR_ASYNC`
   if [ -z "$ASYNC" ] ; then
      ASYNC=`sysevent async dhcpv6_aftr /etc/init.d/service_wan/dslite_link.sh $NAMESPACE`
      sysevent set $DSLITE_WAITING_FOR_DHCP6_AFTR_ASYNC "$ASYNC"
   fi
   if [ -z "`sysevent get current_wan_ipv6address`" ] ; then
      ulog dslite_link status "$PID Deferring dslite until ipv6 address is configured on default ipv6 wan interface"
      return 0
   fi
   SYSCFG_dslite_aftr=`syscfg get dslite_aftr`
   SYSEVENT_dhcpv6_aftr=`sysevent get dhcpv6_aftr`
   if [ -z "$SYSEVENT_dhcpv6_aftr" -a -z "$SYSCFG_dslite_aftr" ] ; then
      ulog dslite_link status "$PID Deferring dslite until aftr is provisioned"
      return 0
   else
      if [ -z "$SYSEVENT_dhcpv6_aftr" ] ; then
         AFTR_STR="Using $SYSCFG_dslite_aftr provisioned in nvram"
      else
         AFTR_STR="Using $SYSEVENT_dhcpv6_aftr learned from dhcpv6"
      fi
      ulog dslite_link status "$PID $AFTR_STR"
   fi
   sysevent set ipv4_connection_state "ipv4 link going up"
   sysevent set ${NAMESPACE}_current_ipv4_link_state up
}
do_stop_dslite() {
   sysevent set ipv4_connection_state "ipv4 link going down"
   ASYNC=`sysevent get $DSLITE_WAITING_FOR_IPv6_WANADDR_ASYNC`
   if [ -n "$ASYNC" ] ; then
      sysevent rm_async $ASYNC
      sysevent set $DSLITE_WAITING_FOR_IPv6_WANADDR_ASYNC 
   fi
   ASYNC=`sysevent get $DSLITE_WAITING_FOR_DHCP6_AFTR_ASYNC`
   if [ -n "$ASYNC" ] ; then
      sysevent rm_async $ASYNC
      sysevent set $DSLITE_WAITING_FOR_DHCP6_AFTR_ASYNC 
   fi
   sysevent set ${NAMESPACE}_current_ipv4_link_state down
}
handle_ipv6_wan_address_change() {
   STATUS=`sysevent get ${1}_desired_ipv4_link_state`
   if [ "up" != "$STATUS" ] ; then
      exit 0
   fi
   CURRENT_IPV6_ADDRESS=`sysevent get current_wan_ipv6address`
   if [ -z "$STATUS" ] ; then
      exit 0
   fi
   ulog dslite_link status "$PID Detected change to ipv6 default wan address to $CURRENT_IPV6_ADDRESS"
   NAMESPACE=$1
   STATUS=`sysevent get ${1}_current_ipv4_link_state`
   if [ "up" = "$STATUS" ] ; then
      ulog dslite_link status "$PID tearing down tunnel in order to rebuild it"
      do_stop_dslite
      WAN_STATUS=`sysevent get ${1}_current_ipv4_wan_state`
      while [ "down" != "$WAN_STATUS" ] ; do
         sleep 1
         WAN_STATUS=`sysevent get ${1}_current_ipv4_wan_state`
      done
      ulog dslite_link status "$PID bringing up tunnel using new local address"
   fi
   INPUT_PARAM_1="${NAMESPACE}_desired_ipv4_link_state"
}
handle_dhcpv6c_aftr() {
   STATUS=`sysevent get ${1}_desired_ipv4_link_state`
   if [ "up" != "$STATUS" ] ; then
      exit 0
   fi
   CURRENT_IPV6_ADDRESS=`sysevent get current_wan_ipv6address`
   if [ -z "$STATUS" ] ; then
      exit 0
   fi
   SYSEVENT_AFTR=`sysevent get dhcpv6_aftr`
   if [ -z "$SYSEVENT_AFTR" ] ; then
      ulog dslite_link status "$PID Detected that dhcpv6 lost its lease to the aftr"
   else
      ulog dslite_link status "$PID Detected that dhcpv6 got an aftr at $SYSEVENT_AFTR"
   fi
   NAMESPACE=$1
   STATUS=`sysevent get ${1}_current_ipv4_link_state`
   if [ "up" = "$STATUS" ] ; then
      ulog dslite_link status "$PID tearing down tunnel in order to rebuild it"
      do_stop_dslite
      WAN_STATUS=`sysevent get ${1}_current_ipv4_wan_state`
      while [ "down" != "$WAN_STATUS" ] ; do
         sleep 1
         WAN_STATUS=`sysevent get ${1}_current_ipv4_wan_state`
      done
      ulog dslite_link status "$PID bringing up tunnel again"
   fi
   INPUT_PARAM_1="${NAMESPACE}_desired_ipv4_link_state"
}
if [ -z "$1" ] ; then
   return
else
   INPUT_PARAM_1=$1
fi
if [ "current_wan_ipv6address" = "$INPUT_PARAM_1" -a -n "$3" ] ; then
   handle_ipv6_wan_address_change $3
elif [ "dhcpv6_aftr" = "$INPUT_PARAM_1" -a -n "$3" ] ; then
   handle_dhcpv6c_aftr $3
fi
service_init $INPUT_PARAM_1
ulog dslite_link status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog dslite_link status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog dslite_link status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   phylink_wan_state)
      ulog dslite_link status "$PID physical link is $SYSEVENT_phylink_wan_state"
      if [ "up" != "$SYSEVENT_phylink_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_link_state" ] ; then
            ulog dslite_link status "$PID physical link is down. Setting link down."
            do_stop_dslite
            exit 0
         else
            ulog dslite_link status "$PID physical link is down. Link is already down."
            exit 0
         fi
      else
         if [ "up" = "SYSEVENT_current_ipv4_link_state" ] ; then
            ulog dslite_link status "$PID physical link is up. Link is already up."
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
                  ulog dslite_link status "$PID starting dslite link"
                  do_start_dslite
                  exit 0
            else
               ulog dslite_link status "$PID physical link is up, but desired link state is down."
               exit 0;
            fi
         fi
      fi
      ;;
   desired_ipv4_link_state)
      if [ "$SYSEVENT_current_ipv4_link_state" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         ulog dslite_link status "$PID wan is already is desired state $SYSEVENT_current_ipv4_link_state"
         exit
      fi
      if [ "up" = "$SYSEVENT_desired_ipv4_link_state" ] ; then
         if [ "down" = "$SYSEVENT_phylink_wan_state" ] ; then
            ulog dslite_link status "$PID up requested but physical link is still down"
            exit 0;
         fi
         ulog dslite_link status "$PID up requested"
         do_start_dslite
         exit 0
      else
         ulog dslite_link status "$PID down requested"
         do_stop_dslite
         exit 0
      fi
      ;;
  *)
        ulog dslite_link status "$PID Invalid parameter $INPUT_PARAM_1 "
        exit 3
        ;;
esac
