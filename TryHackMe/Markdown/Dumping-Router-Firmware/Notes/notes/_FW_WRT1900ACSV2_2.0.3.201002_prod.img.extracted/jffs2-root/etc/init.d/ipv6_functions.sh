#!/bin/sh
create_eui_64 ()
{
   if [ -z "$1" ] ; then
      INT_NAME="`syscfg get lan_ifname`"
   else
      INT_NAME="$1"
   fi
   MAC_LINE=`ip  addr show dev $INT_NAME | grep link`
   MAC=`echo $MAC_LINE | cut -d" " -f 2`
   for i in 1 2 3 4 5 6
   do
      eval "PART_${i}=`echo $MAC |  cut -d ':' -f ${i}`"
   done
   EUI_64="${PART_1}${PART_2}:${PART_3}ff:fe${PART_4}:${PART_5}${PART_6}"
   eval `ip6calc -x ${EUI_64}:: 0200::`
   eval `ip6calc -r $XOR 64`
   EUI_64=${SHIFT:2:20} 
   
}
create_random_64 ()
{
   RANDOM_64=`cat /dev/urandom | tr -cd 'a-f0-9' | head -c 4`
   RANDOM_64=$RANDOM_64":`cat /dev/urandom | tr -cd 'a-f0-9' | head -c 4`"
   RANDOM_64=$RANDOM_64":`cat /dev/urandom | tr -cd 'a-f0-9' | head -c 4`"
   RANDOM_64=$RANDOM_64":`cat /dev/urandom | tr -cd 'a-f0-9' | head -c 4`"
   echo "above errors produced by ipv6_functions.sh::create_random_64 are not service affecting" > /dev/console
}
do_duplicate_address_detection ()
{
   if [ -z "$1" -o -z "$2" ] ; then
      return 254
   fi
   for i in $(seq 5); do
      sleep 1
      IPV6_FUNCTS_DAD_FAILED=`ip -6 addr show dev $1 dadfailed | grep $2`
      if [ -n "$IPV6_FUNCTS_DAD_FAILED" ] ; then
         return 255
         break
      fi
   done
   
   return 0
}
provision_interface_using_prefix ()
{
   if [ -z "$1" -o -z "$2" ] ; then
      return 
   fi
   eval `ip6calc -p $2`
   create_eui_64 $1
   eval `ip6calc -o ${PREFIX} ::${EUI_64}`
   IPV6_FUNCTS_LAN_ADDRESS=$OR
   ulog ipv6_funct status "Assigning $IPV6_FUNCTS_LAN_ADDRESS to $1"
   IPV6_LLADDR_DAD_FAILED=`ip -6 addr show dev $1 dadfailed | grep link`
   if [ -n "$IPV6_LLADDR_DAD_FAILED" ] ; then
      return
   fi
   ip -6 addr add ${IPV6_FUNCTS_LAN_ADDRESS}/64 dev $1 scope global  2>&1
   do_duplicate_address_detection $1 $IPV6_FUNCTS_LAN_ADDRESS
   IPV6_FUNCTS_RET_CODE=$?
   while [ "$IPV6_FUNCTS_RET_CODE" != "0" ] ; do
      ulog ipv6_funct status "$IPV6_FUNCTS_LAN_ADDRESS fails DAD. Removing from $1"
      ip -6 addr del ${IPV6_FUNCTS_LAN_ADDRESS}/64 dev $1 scope global  2>&1
      create_random_64 
      eval `ip6calc -o ${PREFIX} ::${RANDOM_64}`
      IPV6_FUNCTS_LAN_ADDRESS=$OR
      ulog ipv6_funct status "Assigning $IPV6_FUNCTS_LAN_ADDRESS to $1"
      ip -6 addr add ${IPV6_FUNCTS_LAN_ADDRESS}/64 dev $1 scope global  2>&1
      do_duplicate_address_detection $1 $IPV6_FUNCTS_LAN_ADDRESS
      IPV6_FUNCTS_RET_CODE=$?
   done
   sysevent set ${1}_dhcpv6_ia_pd_address ${IPV6_FUNCTS_LAN_ADDRESS}/64
}
deprecate_lan_ipv6_prefix ()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   CURRENT_PREFIX=`sysevent get ${1}_ipv6_prefix`
   CURRENT_VALID=`sysevent get ${1}_ipv6_prefix_valid_lifetime`
   CURRENT_PREFERRED=`sysevent get ${1}_ipv6_prefix_preferred_lifetime`
   if [ -z "$CURRENT_VALID" ] ; then 
      CURRENT_VALID=0
   fi
   if [ -z "$CURRENT_PREFERRED" ] ; then 
      CURRENT_PREFERRED=0
   fi
   if [ -n "$CURRENT_PREFIX" ] ; then
      REMAINING_TIME=0
      NOW=`date +%s`
      THEN=`sysevent get ${1}_ipv6_prefix_acquired_time`
      if [ -n "$NOW" -a -n "$THEN" ] ; then
         REMAINING_TIME=`expr $NOW - $THEN`
         REMAINING_TIME=`expr $CURRENT_VALID - $REMAINING_TIME`
         if [ "0" -lt "$REMAINING_TIME" ] ; then
            if [ "$REMAINING_TIME" -gt "7200" ] ; then
               REMAINING_TIME=7200
            fi 
            sysevent set previous_${1}_ipv6_prefix                    $CURRENT_PREFIX 
            sysevent set previous_${1}_ipv6_prefix_valid_lifetime     $REMAINING_TIME
            sysevent set previous_${1}_ipv6_prefix_preferred_lifetime 0
            sysevent set previous_${1}_ipv6_prefix_acquired_time $NOW
         else
            sysevent set previous_${1}_ipv6_prefix                    $CURRENT_PREFIX 
            sysevent set previous_${1}_ipv6_prefix_valid_lifetime     0
            sysevent set previous_${1}_ipv6_prefix_preferred_lifetime 0
            sysevent set previous_${1}_ipv6_prefix_acquired_time $NOW
         fi
      fi
      sysevent set ${1}_ipv6_prefix_valid_lifetime 0
      sysevent set ${1}_ipv6_prefix_preferred_lifetime 0
      sysevent set ${1}_ipv6_prefix_acquired_time
   fi
}
quick_deprecate_lan_ipv6_prefix ()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   CURRENT_PREFIX=`sysevent get ${1}_ipv6_prefix`
   CURRENT_VALID=`sysevent get ${1}_ipv6_prefix_valid_lifetime`
   CURRENT_PREFERRED=`sysevent get ${1}_ipv6_prefix_preferred_lifetime`
   if [ -n "$CURRENT_PREFIX" ] ; then
      REMAINING_TIME=0
      NOW=`date +%s`
      THEN=`sysevent get ${1}_ipv6_prefix_acquired_time`
      if [ -n "$NOW" -a -n "$THEN" ] ; then
         REMAINING_TIME=`expr $NOW - $THEN`
         REMAINING_TIME=`expr $CURRENT_VALID - $REMAINING_TIME`
         if [ "5" -lt "$REMAINING_TIME" ] ; then
            REMAINING_TIME=5
         fi
         sysevent set previous_${1}_ipv6_prefix                    $CURRENT_PREFIX 
         sysevent set previous_${1}_ipv6_prefix_valid_lifetime     $REMAINING_TIME
         sysevent set previous_${1}_ipv6_prefix_preferred_lifetime 0
         sysevent set previous_${1}_ipv6_prefix_acquired_time $NOW
      fi
      sysevent set ${1}_ipv6_prefix
      sysevent set ${1}_ipv6_prefix_valid_lifetime 0
      sysevent set ${1}_ipv6_prefix_preferred_lifetime 0
      sysevent set ${1}_ipv6_prefix_acquired_time
   fi
}
save_lan_ipv6_prefix ()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   if [ -n "$2" ] ; then
      PREFIX="`echo $2 | cut -d '/' -f1`/64"
   else
      PREFIX=
   fi
   NOW=`date +%s`
   CURRENT_PREFIX=`sysevent get ${1}_ipv6_prefix`
   CURRENT_VALID=`sysevent get ${1}_ipv6_prefix_valid_lifetime`
   CURRENT_PREFERRED=`sysevent get ${1}_ipv6_prefix_preferred_lifetime`
   if [ "$CURRENT_PREFIX" = "$PREFIX" -a "$CURRENT_VALID" = "$3" -a "$CURRENT_PREFERRED" = "$4" ] ; then
      sysevent set ${1}_ipv6_prefix_acquired_time $NOW
   else
      if [ "$PREFIX" != "$CURRENT_PREFIX" ] ; then
         deprecate_lan_ipv6_prefix $1
         IPV6_FIREWALL_RESTART=needed
      fi
      sysevent set ${1}_ipv6_prefix $PREFIX
      if [ -n "$PREFIX" ] ; then
         sysevent set ${1}_ipv6_prefix_valid_lifetime $3
         sysevent set ${1}_ipv6_prefix_preferred_lifetime $4
         sysevent set ${1}_ipv6_prefix_acquired_time $NOW
      else
         sysevent set ${1}_ipv6_prefix_valid_lifetime
         sysevent set ${1}_ipv6_prefix_preferred_lifetime
         sysevent set ${1}_ipv6_prefix_acquired_time
      fi
   fi
}
clear_previous_lan_ipv6_prefix ()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   sysevent set previous_${1}_ipv6_prefix
   sysevent set previous_${1}_ipv6_prefix_valid_lifetime
   sysevent set previous_${1}_ipv6_prefix_preferred_lifetime
   sysevent set previous_${1}_ipv6_prefix_acquired_time
}
get_current_lan_ipv6address ()
{
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   CANDIDATE=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix` 
   if [ -n "$CANDIDATE" ] 
   then
      CANDIDATE_PREFIX_LEN=`echo "$CANDIDATE" | cut -f2 -d'/'`
      eval `ip6calc -p $CANDIDATE`
      CANDIDATE=$PREFIX
      if [ -n "$CANDIDATE" ] ; then
         LAN_IPADDR=`ip -6 addr show dev $SYSCFG_lan_ifname scope global | grep inet6 | tr '/' ' ' | awk '{ print $2}'`
         for IP in $LAN_IPADDR ; do
            eval `ip6calc -p ${IP}/${CANDIDATE_PREFIX_LEN}`
            if [ "$CANDIDATE" = "$PREFIX" ] ; then
               echo "CURRENT_LAN_IPV6ADDRESS=${IP}"
               return
            fi
         done
      fi
   else
      CANDIDATE=`sysevent get ${SYSCFG_lan_ifname}_ula_ipaddress`
      if [ -n "$CANDIDATE" ]
      then 
         echo "CURRENT_LAN_IPV6ADDRESS=${CANDIDATE}"
         return
      else
         LAN_IPADDR=`ip -6 addr show dev $SYSCFG_lan_ifname scope global | grep inet6 | tr '/' ' ' | awk '{ print $2}'`
         for IP in $LAN_IPADDR ; do
            if [ -n "$IP" ] ; then
               echo "CURRENT_LAN_IPV6ADDRESS=${IP}"
               return
            fi
         done
      fi
   fi
   echo "CURRENT_LAN_IPV6ADDRESS="
}
get_current_guest_lan_ipv6address ()
{
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   CANDIDATE=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`   
   if [ -n "$CANDIDATE" ]  
   then
      CANDIDATE_PREFIX_LEN=`echo "$CANDIDATE" | cut -f2 -d'/'`
      eval `ip6calc -p $CANDIDATE`
      CANDIDATE=$PREFIX
      if [ -n "$CANDIDATE" ] ; then
         LAN_IPADDR=`ip -6 addr show dev $SYSCFG_lan_ifname scope global | grep inet6 | tr '/' ' ' | awk '{ print $2}'`
         for IP in $LAN_IPADDR ; do
            eval `ip6calc -p ${IP}/${CANDIDATE_PREFIX_LEN}`
            if [ "$CANDIDATE" = "$PREFIX" ] ; then
               echo "CURRENT_GUEST_LAN_IPV6ADDRESS=${IP}"
               return
            fi
         done
      fi
   else
      CANDIDATE=`sysevent get ${SYSCFG_guest_lan_ifname}_ula_ipaddress`
      if [ -n "$CANDIDATE" ]
      then 
         echo "CURRENT_GUEST_LAN_IPV6ADDRESS=${CANDIDATE}"
         return
      else
         LAN_IPADDR=`ip -6 addr show dev $SYSCFG_guest_lan_ifname scope global | grep inet6 | tr '/' ' ' | awk '{ print $2}'`
         for IP in $LAN_IPADDR ; do
            if [ -n "$IP" ] ; then
               echo "CURRENT_GUEST_LAN_IPV6ADDRESS=${IP}"
               return
            fi
         done
      fi
   fi
   echo "CURRENT_GUEST_LAN_IPV6ADDRESS="
}
delete_guest_lan_ipv6address ()
{
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   LAN_IPADDR=`ip -6 addr show dev $SYSCFG_guest_lan_ifname scope global | grep inet6 | awk '{ print $2}'`
   if [ "" != "$LAN_IPADDR" ] ; then
      ip -6 addr del $LAN_IPADDR dev $SYSCFG_guest_lan_ifname
      echo "ip -6 addr del $LAN_IPADDR dev $SYSCFG_guest_lan_ifname" > /dev/console
   else
      echo "${SYSCFG_guest_lan_ifname} global address not available to delete" > /dev/console
   fi
}
delete_loopback_ipv6address ()
{
   LAN_IPADDR=`ip -6 addr show dev lo scope global | grep inet6 | awk '{ print $2}'`
   if [ "" != "$LAN_IPADDR" ] ; then
      ip -6 addr del $LAN_IPADDR dev lo 
      echo "ip -6 addr del $LAN_IPADDR dev lo" > /dev/console
   else
      echo "lo global address not available to delete" > /dev/console
   fi
}
delete_unreachable_ipv6route ()
{
   LAN_ROUTE=`ip -6 route show | grep unreachable | awk '{ print $2 }'`
   for loop in $LAN_ROUTE
   do
       LINE=`ip -6 route show | grep unreachable | grep $loop`
       LINE=`echo $LINE | awk '{ print $2 " " $3 " " $4 }'`
       if [ -n "$LINE" ] ; then
          echo "delete unreachable route $LINE" > /dev/console
          ip -6 route del $LINE
       fi
   done
}
ipv6_prefix_calc()
{
   PREFIX=$1
   PREFIX_LENGTH=$2
   IP_ADDRESS=$3
   MASK_BITS=$4
   NUM_SUBNETS=$5
   MINIMUM_NETWORK_SIZE=$6
   if [ -z "$MINIMUM_NETWORK_SIZE" ] ; then
      MINIMUM_NETWORK_SIZE=0
   fi
   if [ -z "$NUM_SUBNETS" ] ; then
      NUM_SUBNETS=1
   fi
   if [ -z "$MASK_BITS" ] ; then
      MASK_BITS=0
   fi
   if [ -z "$IP_ADDRESS" ] ; then
      IP_ADDRESS=0
      MASK_BITS=0
   fi
   if [ "0" != "$PREFIX" -o "0" = "$PREFIX_LENGTH" ] ; then
      SHIFT_BY=`expr 128 - $PREFIX_LENGTH` 
      eval `ip6calc -r $PREFIX $SHIFT_BY`
      eval `ip6calc -l $SHIFT $SHIFT_BY`
      PREFIX=$SHIFT
   else
      PREFIX="::"
      PREFIX_LENGTH=0
   fi
   if [ "0" != "$IP_ADDRESS" ] ; then
      eval `ip6calc -4 $IP_ADDRESS`
      IP_ADDRESS=$IPv4_MAPPED
      if [ $MASK_BITS -gt 0 ] ; then
         eval `ip6calc -l $IP_ADDRESS $MASK_BITS`
         eval `ip6calc -a $SHIFT ::255.255.255.255`
         IP_ADDRESS=$AND
      fi
      SHIFT_BY=`expr 96 - $PREFIX_LENGTH`
      eval `ip6calc -l $IP_ADDRESS $SHIFT_BY`
      IP_ADDRESS=$SHIFT
      eval `ip6calc -o $PREFIX $IP_ADDRESS`
      PREFIX="$OR"
      PREFIX_LENGTH=`expr $PREFIX_LENGTH + 32 - $MASK_BITS`
   fi
   if [ "64" -lt "$PREFIX_LENGTH" ] ; then
      echo "IPv6_PREFIX_1=${PREFIX}/${PREFIX_LENGTH}"
      return
   else
      if [ "$NUM_SUBNETS" -le  "2" ] ; then
         SUBNET_BITS=1
      elif [ "$NUM_SUBNETS" -le  "4" ] ; then
         SUBNET_BITS=2
      elif [ "$NUM_SUBNETS" -le  "8" ] ; then
         SUBNET_BITS=3
      elif [ "$NUM_SUBNETS" -le  "16" ] ; then
         SUBNET_BITS=4
      elif [ "$NUM_SUBNETS" -le  "32" ] ; then
         SUBNET_BITS=5
      else 
         SUBNET_BITS=6
      fi
      if [ "$PREFIX_LENGTH" -ge "64" ] ; then
         NUM_SUBNETS=1
         SUBNET_BITS=0
      elif [ "$PREFIX_LENGTH" -eq "63" ] ; then
         if [ "$SUBNET_BITS" -gt "1" ] ; then
            NUM_SUBNETS=2
            SUBNET_BITS=1
         fi
      elif [ "$PREFIX_LENGTH" -eq "62" ] ; then
         if [ "$SUBNET_BITS" -gt "2" ] ; then
            NUM_SUBNETS=4
            SUBNET_BITS=2
         fi
      elif [ "$PREFIX_LENGTH" -eq "61" ] ; then
         if [ "$SUBNET_BITS" -gt "3" ] ; then
            NUM_SUBNETS=8
            SUBNET_BITS=3
         fi
      elif [ "$PREFIX_LENGTH" -eq "60" ] ; then
         if [ "$SUBNET_BITS" -gt "4" ] ; then
            NUM_SUBNETS=16
            SUBNET_BITS=4
         fi
      elif [ "$PREFIX_LENGTH" -eq "59" ] ; then
         if [ "$SUBNET_BITS" -gt "5" ] ; then
            NUM_SUBNETS=32
            SUBNET_BITS=5
         fi
      elif [ "$PREFIX_LENGTH" -eq "58" ] ; then
         if [ "$SUBNET_BITS" -gt "6" ] ; then
            NUM_SUBNETS=64
            SUBNET_BITS=6
         fi
      else  
         if [ "$SUBNET_BITS" -gt "6" ] ; then
            NUM_SUBNETS=64
            SUBNET_BITS=6
         fi
      fi
      SHIFTBY_OFFSET=`expr 128 - $PREFIX_LENGTH - $SUBNET_BITS`
      PREFIX_LENGTH=`expr $PREFIX_LENGTH + $SUBNET_BITS`
      OUTPUT_PREFIX_LENGTH=$PREFIX_LENGTH
      if [ "0" != "$MINIMUM_NETWORK_SIZE" ] ; then
         if [ "$MINIMUM_NETWORK_SIZE" -gt "$OUTPUT_PREFIX_LENGTH" ] ; then
            OUTPUT_PREFIX_LENGTH=$MINIMUM_NETWORK_SIZE
         fi
      fi
      index=0
      while [ "$index" -lt "$NUM_SUBNETS" ] ; do
         if [ "0" != "$index" ] ; then
            eval `ip6calc -4 0.0.0.${index}`
            eval `ip6calc -l $IPv4_MAPPED $SHIFTBY_OFFSET`
            eval `ip6calc -o $PREFIX $SHIFT`
            SUBNET="$OR"
         else
            SUBNET="$PREFIX"
         fi
         I=`expr $index + 1`
         echo -n "IPv6_PREFIX_${I}=${SUBNET}/${OUTPUT_PREFIX_LENGTH} "
         index=`expr $index + 1`
      done 
   fi 
}
get_SLAAC_addr ()
{
COUNT=0
while [ $COUNT -lt 3 ] 
do
   /usr/bin/solicitation $1
   sleep 4
   ROUTE=`ip -6 addr show dev $1 | grep global`
   if [ "0" == "$?" ]; then
      return 0
   fi
   COUNT=`expr $COUNT + 1`
done
return 1
}
