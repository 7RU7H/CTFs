#!/bin/sh
source /etc/init.d/ulog_functions.sh
SLACC_PROVISIONED_POLL_SECS=20
DHCP_PROVISIONED_POLL_SECS=300
CURRENT_POLL_SECS=$SLACC_PROVISIONED_POLL_SECS
OVERLAP_FACTOR=100
choose_addr()
{
   CHOSEN_IPv6_ADDRESS=
   CHOSEN_TIME=0
   CURRENT_PROVISIONED_ADDRESS=`sysevent get current_wan_ipv6address`"/64"
   ADDRS=`ip -6 -o -d addr show dev $INTERFACE > /tmp/out`
   grep global /tmp/out > /tmp/out2
   while read ln ; do
      IPv6_ADDRESS=`echo $ln | cut -f4 -d ' '`
      ADDR="$ln"
      TIME=`echo "$ADDR" | grep -o 'preferred_lft .*' | cut -f2 -d ' '`
      if [ "$CURRENT_PROVISIONED_ADDRESS" = "$IPv6_ADDRESS" ] ; then
          sysevent set preferred_time_slaac $TIME
      fi
      if [ "forever" = "$TIME" ] ; then
         if [ "forever" != "$CHOSEN_TIME" -o "$IPv6_ADDRESS" = "$CURRENT_PROVISIONED_ADDRESS" ] ; then
            CHOSEN_TIME="forever"
            CHOSEN_IPv6_ADDRESS=$IPv6_ADDRESS
         fi
      elif [ "$CHOSEN_TIME" != "forever" ] ; then
         SECS=`echo $TIME | cut -f1 -d's'`
         if [ "$SECS" -gt "$CHOSEN_TIME" ] ; then
            CHOSEN_TIME=$SECS
            CHOSEN_IPv6_ADDRESS=`echo $IPv6_ADDRESS | cut -f1 -d'/'`
         fi
      fi
   done < /tmp/out2
}
if [ -z "$1" ] ; then
   ulog default_ipv6_wan error "Slaac Monitor called with no parameter. Exiting"
   exit
else
   INTERFACE=$1
   echo "$$" > /var/run/slaac_monitor.pid
fi
while [ : ] ; do
   OWNER=`sysevent get current_wan_ipv6address_owner`
   if [ -z "$OWNER" -o "slaac" = "$OWNER" ] ; then
      choose_addr
      SYSEVENT_current_wan_ipv6address=`sysevent get current_wan_ipv6address`
      SYSEVENT_preferred_time_slaac=`sysevent get preferred_time_slaac`
      if [ "forever" = "$SYSEVENT_preferred_time_slaac" -a "$SYSEVENT_current_wan_ipv6address" != "$CHOSEN_IPv6_ADDRESS" ] ; then
        sysevent set current_wan_ipv6address $CHOSEN_IPv6_ADDRESS
        sysevent set preferred_time_slaac $CHOSEN_TIME
        SYSEVENT_current_wan_ipv6address=
        SYSEVENT_preferred_time_slaac=
      fi
 
      if [ -n "$SYSEVENT_current_wan_ipv6address" ] ; then 
         if [  "$SYSEVENT_current_wan_ipv6address" = "$CHOSEN_IPv6_ADDRESS" ] ; then
            :
         elif [ -z "$CHOSEN_IPv6_ADDRESS" ] ; then 
            sysevent set current_wan_ipv6address
            sysevent set current_wan_ipv6address_owner
            sysevent set preferred_time_slaac
            sysevent set ipv6_connection_state "ipv6 connection down"
         else 
            if [ -z "$SYSEVENT_preferred_time_slaac" ] ; then
               SYSEVENT_preferred_time_slaac=0
            fi
            if [ "forever" = "$SYSEVENT_preferred_time_slaac" ] ; then
               :
            else
               RANGE_TIME=`expr $SYSEVENT_preferred_time_slaac + $OVERLAP_FACTOR`
               if [ "$CHOSEN_TIME" = "forever" ] ; then
                  sysevent set current_wan_ipv6address $CHOSEN_IPv6_ADDRESS
                  sysevent set preferred_time_slaac $CHOSEN_TIME
                  ulog default_ipv6_wan status "Setting current_wan_ipv6address to $CHOSEN_IPv6_ADDRESS"
               elif [ "$CHOSEN_TIME" -gt "$RANGE_TIME" ] ; then
                  sysevent set current_wan_ipv6address $CHOSEN_IPv6_ADDRESS
                  sysevent set preferred_time_slaac $CHOSEN_TIME
                  ulog default_ipv6_wan status "Setting current_wan_ipv6address to $CHOSEN_IPv6_ADDRESS"
               fi
            fi
         fi
      else
         if [ -n "$CHOSEN_IPv6_ADDRESS" ] ; then
               sysevent set current_wan_ipv6address $CHOSEN_IPv6_ADDRESS
               sysevent set preferred_time_slaac $CHOSEN_TIME
               sysevent set current_wan_ipv6address_owner slaac
               sysevent set ipv6_connection_state "ipv6 connection up"
               ulog default_ipv6_wan status "Setting initial current_wan_ipv6address to $CHOSEN_IPv6_ADDRESS" 
         fi
      fi
      CURRENT_POLL_SECS=$SLACC_PROVISIONED_POLL_SECS
   else
      CURRENT_POLL_SECS=$DHCP_PROVISIONED_POLL_SECS
   fi
   sleep $CURRENT_POLL_SECS
done
