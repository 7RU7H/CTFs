#!/bin/sh
CRON_TIMEOUT_DIR="/etc/cron/cron.everyminute/"
make_cron_timeout_file() {
   if [ -z "$3" ] ; then
      return
   fi
   sysevent set ${1}_ipv6_deprecated_but_valid_delegated_address_lifetime $3
   CRON_TIMEOUT_FILE="${CRON_TIMEOUT_DIR}${1}_deprecated_prefix_timeout.sh"
   echo "#!/bin/sh" > $CRON_TIMEOUT_FILE
   echo "INTERFACE_NAME=$1" >> $CRON_TIMEOUT_FILE
   echo "DEPR_ADDR=$2" >> $CRON_TIMEOUT_FILE
   echo "SELF_FILE=$CRON_TIMEOUT_FILE" >> $CRON_TIMEOUT_FILE
   echo "MINS_LEFT=\`sysevent get ${1}_ipv6_deprecated_but_valid_delegated_address_lifetime\`" >> $CRON_TIMEOUT_FILE
   echo "if [ -z \"\$MINS_LEFT\" ] ; then" >> $CRON_TIMEOUT_FILE
   echo "   MINS_LEFT=1" >> $CRON_TIMEOUT_FILE
   echo "fi" >> $CRON_TIMEOUT_FILE
   echo "MINS_LEFT=\`expr \$MINS_LEFT - 1\`" >> $CRON_TIMEOUT_FILE
   echo "if [ \"\$MINS_LEFT\" -gt \"0\" ] ; then"  >> $CRON_TIMEOUT_FILE
   echo "   sysevent set ${1}_ipv6_deprecated_but_valid_delegated_address_lifetime \$MINS_LEFT"  >> $CRON_TIMEOUT_FILE
   echo "   return" >> $CRON_TIMEOUT_FILE
   echo "else" >> $CRON_TIMEOUT_FILE
   echo "   sysevent set dhcpv6_client_expire_deprecated_address \"\$INTERFACE_NAME \$DEPR_ADDR\"" >> $CRON_TIMEOUT_FILE
   echo "   sysevent set ${1}_ipv6_deprecated_but_valid_delegated_address_lifetime " >> $CRON_TIMEOUT_FILE
   echo "   rm -f \$SELF_FILE" >> $CRON_TIMEOUT_FILE
   echo "fi" >> $CRON_TIMEOUT_FILE
   chmod 777 $CRON_TIMEOUT_FILE
}
release_ia_na() {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "release_ia_na: no interface provided. Ignoring release IA_NA"
      return
   fi
   IPV6_ADDR=`sysevent get ${interface}_dhcpv6_ia_na`
   if [ -z "$IPV6_ADDR" ] ; then
      return
   fi
   if [ "dhcp" = "`sysevent get current_wan_ipv6address_owner`" ] ; then
      ulog dhcpv6c status "release_ia_na: releasing $IPV6_ADDR as current_wan_ipv6address"
      sysevent set current_wan_ipv6address_owner
      sysevent set current_wan_ipv6address
   fi
   ulog dhcpv6c status "release_ia_na: releasing $IPV6_ADDR from $interface"
   ip -6 addr del $IPV6_ADDR dev $interface
   sysevent set ${interface}_dhcpv6_ia_na
   sysevent set wan_dhcpv6_lease $new_max_life
}
new_ia_na() {
   RETURN_CODE=0
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "new_ia_na: no interface provided. Ignoring IA_NA"
      return $RETURN_CODE
   fi
   if [ -z "$new_ip6_address" ] ; then
      ulog dhcpv6c status "new_ia_na: no new ip6 address provided. Ignoring IA_NA"
      return $RETURN_CODE
   fi
   if [ -n "$new_ip6_prefixlen" ] ; then
      TENTATIVE_ADDR="${new_ip6_address}/${new_ip6_prefixlen}"
   else
      TENTATIVE_ADDR="${new_ip6_address}/128"
   fi
   IPV6_ADDR=`sysevent get ${interface}_dhcpv6_ia_na`
   if [ -n "$IPV6_ADDR" ] ; then
      if [ "$IPV6_ADDR" = "$TENTATIVE_ADDR" ] ; then
         ulog dhcpv6c status "new_ia_na: $interface is already provisioned with $TENTATIVE_ADDR"
         return $RETURN_CODE
      else
         ulog dhcpv6c status "new_ia_na: $interface is provisioned with $IPV6_ADDR. Changing to $new_ip6_address"
         ip -6 addr del $IPV6_ADDR dev $interface
         sysevent set ${interface}_dhcpv6_ia_na
      fi
   fi
   ulog dhcpv6c status "new_ia_na: Provisioning $interface with $TENTATIVE_ADDR"
   ip -6 addr add $TENTATIVE_ADDR dev $interface scope global
   do_duplicate_address_detection $interface $TENTATIVE_ADDR
   if [ "$?" != "0" ] ; then
      ulog dhcpv6c status "new_ia_na: $TENTATIVE_ADDR failed DAD."
      ip -6 addr del $TENTATIVE_ADDR dev $interface scope global
      RETURN_CODE=3
   else
      sysevent set ${interface}_dhcpv6_ia_na $TENTATIVE_ADDR
      CHOPPED_ADDR=`echo $TENTATIVE_ADDR | cut -d'/' -f1`
      ulog dhcpv6c status "new_ia_na: making $CHOPPED_ADDR the current_wan_ipv6address"
      sysevent set current_wan_ipv6address $CHOPPED_ADDR
      sysevent set current_wan_ipv6address_owner dhcp
      if [ -n "$new_max_life" ] ; then
         sysevent set wan_dhcpv6_lease $new_max_life
      fi
   fi
   return $RETURN_CODE
}
is_deprecated_delegated_address()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   cur=`sysevent get ${1}_ipv6_deprecated_but_valid_delegated_address`
   
   if [ -n "$cur" ] ; then
      return 1
   fi
   return 0
}
abort_deprecated_delegated_address_tracking()
{
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   DEPS=`sysevent get ${SYSCFG_lan_ifname}_ipv6_deprecated_but_valid_delegated_address`
   if [ -n "$DEPS" ] ; then
      sysevent set ${SYSCFG_lan_ifname}_ipv6_deprecated_but_valid_delegated_address
      ulog dhcpv6c info "Removed timer for deprecated but still valid address on $SYSCFG_lan_ifname"
   fi
   DEPS=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_deprecated_but_valid_delegated_address`
   if [ -n "$DEPS" ] ; then
      sysevent set ${SYSCFG_guest_lan_ifname}_ipv6_deprecated_but_valid_delegated_address
      ulog dhcpv6c info "Removed timer for deprecated but still valid address on $SYSCFG_guest_lan_ifname"
   fi
}
add_deprecated_delegated_address()
{
   if [ -z "$1" -o -z "$2" ] ; then
      return 0
   fi
   sysevent set ${1}_ipv6_deprecated_but_valid_delegated_address  $2
}
remove_deprecated_delegated_address()
{
   if [ -z "$1" ] ; then
      return 0
   fi
   
   ulog dhcpv6c status "in remove_deprecated_delegated_address"
   DEPR_ADDR=`sysevent get ${1}_ipv6_deprecated_but_valid_delegated_address`
   if [ -z "$DEPR_ADDR" ] ; then
      return 0
   fi
   ulog dhcpv6c status "remove_deprecated_delegated_address: Removing $DEPR_ADDR from $1"
   ip -6 addr del $DEPR_ADDR dev $1
   sysevent set ${1}_ipv6_deprecated_but_valid_delegated_address
}
deprecate_ia_pd() {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "deprecate_ia_pd: no interface provided. Ignoring release IA_PD"
      return
   fi
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   LAN_OLD_ADDR=`sysevent get ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$LAN_OLD_ADDR" ] ; then
      ulog dhcpv6c status "deprecate_ia_pd: Deprecating $LAN_OLD_ADDR from $SYSCFG_lan_ifname"
      add_deprecated_delegated_address $SYSCFG_lan_ifname $LAN_OLD_ADDR
      ulog dhcpv6c status "deprecate_ia_pd: Clearing $LAN_OLD_ADDR from $SYSCFG_lan_ifname"
      sysevent set ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address
      TEST_PREFIX=`ip6calc -p $LAN_OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         save_lan_ipv6_prefix $SYSCFG_lan_ifname
         ulog dhcpv6c status "Deprecating $SYSCFG_lan_ifname prefix $SYSCFG_lan_ifname"
      fi
   fi
   GUEST_LAN_OLD_ADDR=`sysevent get ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$GUEST_LAN_OLD_ADDR" ] ; then
      ulog dhcpv6c status "deprecate_ia_pd: Deprecating $GUEST_LAN_OLD_ADDR from $SYSCFG_guest_lan_ifname"
      add_deprecated_delegated_address $SYSCFG_guest_lan_ifname $GUEST_LAN_OLD_ADDR
      ulog dhcpv6c status "deprecate_ia_pd: Clearing $GUEST_LAN_OLD_ADDR from $SYSCFG_lan_ifname"
      sysevent set ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address
      TEST_PREFIX=`ip6calc -p $GUEST_LAN_OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
         ulog dhcpv6c status "Deprecating $SYSCFG_guest_lan_ifname prefix $SYSCFG_lan_ifname"
      fi
   fi
   SYSCFG_loopback_ifname=lo
   LO_OLD_ADDR=`sysevent get ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$LO_OLD_ADDR" ] ; then
      ulog dhcpv6c status "deprecate_ia_pd: Removing $LO_OLD_ADDR from $SYSCFG_loopback_ifname"
      ip -6 addr del $LO_OLD_ADDR dev $SYSCFG_loopback_ifname scope global
      sysevent set ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address
   fi
   delete_unreachable_ipv6route
   sysevent set ipv6_delegated_prefix
   sysevent set radvd-reload
   LIFETIME=`sysevent get ipv6_delegated_prefix_lifetime`
   START=`sysevent get ipv6_delegated_prefix_time_received`
   NOW=`date +%s`
   EXPIRE_AT=`expr $START + $LIFETIME`
   REMAINING=`expr $EXPIRE_AT - $NOW`
   MINS_TO_EXPIRE=0
   if [ "$REMAINING" -gt "0" ] ; then
      MINS_TO_EXPIRE=`expr $REMAINING / 60`
   fi
   if [ "$MINS_TO_EXPIRE" -le "0" ] ; then
      MINS_TO_EXPIRE=1
   fi
   if [ -n "$LAN_OLD_ADDR" ] ; then
      ulog dhcpv6c status "deprecate_ia_pd: Setting cron for $LAN_OLD_ADDR from $SYSCFG_lan_ifname in $MINS_TO_EXPIRE minutes"
      make_cron_timeout_file $SYSCFG_lan_ifname $LAN_OLD_ADDR $MINS_TO_EXPIRE
   fi
}
destroy_ia_pd() {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "destroy_ia_pd: no interface provided. Ignoring release IA_PD"
      return
   fi
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   LAN_OLD_ADDR=`sysevent get ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$LAN_OLD_ADDR" ] ; then
      TEST_PREFIX=`ip6calc -p $LAN_OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         ulog dhcpv6c status "Quick Deprecating $SYSCFG_lan_ifname prefix $SYSCFG_lan_ifname"
         quick_deprecate_lan_ipv6_prefix ${SYSCFG_lan_ifname}
      fi
   fi
   GUEST_LAN_OLD_ADDR=`sysevent get ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$GUEST_LAN_OLD_ADDR" ] ; then
      TEST_PREFIX=`ip6calc -p $GUEST_LAN_OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         ulog dhcpv6c status "Quick Deprecating $SYSCFG_lan_ifname prefix $SYSCFG_lan_ifname"
         quick_deprecate_lan_ipv6_prefix ${SYSCFG_guest_lan_ifname}
      fi
   fi
   sysevent set radvd-reload
   ulog dhcpv6c status "destroy_ia_pd: Called radvd-reload"
}
release_ia_pd() {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "release_ia_pd: no interface provided. Ignoring release IA_PD"
      return
   fi
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   OLD_ADDR=`sysevent get ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$OLD_ADDR" ] ; then
      ulog dhcpv6c status "release_ia_pd: Removing $OLD_ADDR from $SYSCFG_lan_ifname"
      ip -6 addr del $OLD_ADDR dev $SYSCFG_lan_ifname scope global
      sysevent set ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address
      TEST_PREFIX=`ip6calc -p $OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         deprecate_lan_ipv6_prefix $SYSCFG_lan_ifname
         ulog dhcpv6c status "Releasing $SYSCFG_lan_ifname prefix"
      fi
   fi
   OLD_ADDR=`sysevent get ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$OLD_ADDR" ] ; then
      ulog dhcpv6c status "release_ia_pd: Removing $OLD_ADDR from $SYSCFG_guest_lan_ifname"
      ip -6 addr del $OLD_ADDR dev $SYSCFG_guest_lan_ifname scope global
      sysevent set ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address
      TEST_PREFIX=`ip6calc -p $OLD_ADDR`
      LAN_PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
      if [ -n "$LAN_PREFIX" ] ; then
         CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
      fi
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
         ulog dhcpv6c status "Releasing $SYSCFG_guest_lan_ifname prefix"
      fi
   fi
   SYSCFG_loopback_ifname=lo
   OLD_ADDR=`sysevent get ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address`
   if [ -n "$OLD_ADDR" ] ; then
      ulog dhcpv6c status "release_ia_pd: Removing $OLD_ADDR from $SYSCFG_loopback_ifname"
      ip -6 addr del $OLD_ADDR dev $SYSCFG_loopback_ifname scope global
      sysevent set ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address
   fi
   delete_unreachable_ipv6route
   sysevent set ipv6_delegated_prefix
   sysevent set radvd-reload
}
new_ia_pd() {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "new_ia_pd: no interface provided. Ignoring IA_PD"
      return
   fi
   if [ -z "$new_ip6_prefix" ] ; then
      ulog dhcpv6c status "new_ia_pd: no new prefix provided. Ignoring IA_PD"
      return
   fi
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_enabled=`syscfg get guest_enabled`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   delete_guest_lan_ipv6address
   delete_loopback_ipv6address
   delete_unreachable_ipv6route
   prefix=`echo $new_ip6_prefix | cut -d '/' -f1`
   prefix_length=`echo $new_ip6_prefix | cut -d '/' -f2`
   ulog dhcpv6c status "The DHCPv6 Server gave prefix ${prefix}/${prefix_length}"
   if [ "$prefix_length" -gt "62" ] ; then
      ulog dhcpv6c error "The ISP DHCPv6 Server gave a prefix of length $prefix_length, we expect /62 to fully provision lan."
      if [ "$prefix_length" -gt "64" ] ; then
         ulog dhcpv6c error "Prefix length is $prefix_length, we can not provision lan. $prefix/$prefix_length is ignored."
         exit
      fi
   fi
   sysevent set ipv6_delegated_prefix "$prefix/$prefix_length"
   sysevent set ipv6_delegated_prefix_lifetime $new_max_life
   sysevent set ipv6_delegated_prefix_time_received `date +%s`
   eval `ipv6_prefix_calc $prefix $prefix_length 0 0 3 64`
   if [ -n "$IPv6_PREFIX_1" ] ; then
      provision_interface_using_prefix $SYSCFG_lan_ifname $IPv6_PREFIX_1 
      save_lan_ipv6_prefix $SYSCFG_lan_ifname $IPv6_PREFIX_1 $new_max_life $new_preferred_life
   fi
   if [ -n "$IPv6_PREFIX_3" ] ; then
      loopback_ifname=lo
      eval `ip6calc -p ${IPv6_PREFIX_3}`
      eval `ip6calc -o ${PREFIX} ::1`
      LOOPBACK_ADDRESS=$OR
      ulog dhcpv6c status "Assigning $LOOPBACK_ADDRESS to $loopback_ifname"
      ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev $loopback_ifname scope global  2>&1
      sysevent set ${loopback_ifname}_dhcpv6_ia_pd_address ${LOOPBACK_ADDRESS}/64
   fi
   if [ -n "$IPv6_PREFIX_2" ] ; then
      if [ "1" = "$SYSCFG_guest_enabled" ] ; then
         provision_interface_using_prefix $SYSCFG_guest_lan_ifname $IPv6_PREFIX_2 
         save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname $IPv6_PREFIX_2 $new_max_life $new_preferred_life
      else
         if [ -z "$IPv6_PREFIX_3" ] ; then
            loopback_ifname=lo
            eval `ip6calc -p ${IPv6_PREFIX_2}`
            eval `ip6calc -o ${PREFIX} ::1`
            LOOPBACK_ADDRESS=$OR
            ulog dhcpv6c status "Assigning $LOOPBACK_ADDRESS to $loopback_ifname"
            sysevent set ${loopback_ifname}_dhcpv6_ia_pd_address ${LOOPBACK_ADDRESS}/64
            ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev $loopback_ifname scope global  2>&1
         fi
      fi
   fi
   sysevent set radvd-reload
}
renew_ia_pd () {
   if [ -z "$interface" ] ; then
      ulog dhcpv6c status "renew_ia_pd: no interface provided. Ignoring IA_PD"
      return
   fi
   if [ -z "$new_ip6_prefix" ] ; then
      ulog dhcpv6c status "renew_ia_pd: no new prefix provided. Ignoring IA_PD"
      return
   fi
   NOW=`date +%s`
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   if [ "$new_ip6_prefix" = "`sysevent get ipv6_delegated_prefix`" ] ; then
      sysevent set ipv6_delegated_prefix_lifetime $new_max_life
      sysevent set ipv6_delegated_prefix_time_received $NOW
      ulog dhcpv6c status "renew_ia_pd renewing delegated prefix"
    fi
   TEST_PREFIX=`ip6calc -p $new_ip6_prefix`
   LAN_PREFIX=`sysevent get ${SYSCFG_lan_ifname}_ipv6_prefix`
   if [ -n "$LAN_PREFIX" ] ; then
      CUR_PREFIX=`ip6calc -p $LAN_PREFIX`
   fi
   if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
      save_lan_ipv6_prefix $SYSCFG_lan_ifname  $LAN_PREFIX $new_max_life $new_preferred_life
      ulog dhcpv6c status "Updated $SYSCFG_lan_ifname prefix $LAN_PREFIX $new_max_life $new_preferred_life"
   fi
   LAN_PREFIX=`sysevent get ${SYSCFG_guest_lan_ifname}_ipv6_prefix`
   if [ -n "$LAN_PREFIX" ] ; then
      if [ "$TEST_PREFIX" = "$CUR_PREFIX" ] ; then
         save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname $LAN_PREFIX  $new_max_life $new_preferred_life
         ulog dhcpv6c status "Updated $SYSCFG_guest_lan_ifname prefix $LAN_PREFIX $new_max_life $new_preferred_life"
      fi
   fi
   sysevent set radvd-reload
}
release_sntp_info() {
   sysevent set ipv6_ntp_server 
}
new_sntp_info() {
   if [ -n "$new_dhcp6_sntp_servers" ]  ; then
      sysevent set ipv6_ntp_server "$new_dhcp6_sntp_servers"
   fi
}
release_dns_info() {
   OWNER=`sysevent get dhcpv6_dns_info_owner`
   if [ -n "$OWNER" -a -n "$old_ip6_prefix" -a "$OWNER" != "$old_ip6_prefix" ] ; then
      ulog dhcpv6c status "release_dns_info: requester is not data owner ( $old_ip6_prefix ,$OWNER ). Ignoring release"
      return 0
   else
      sysevent set dhcpv6_dns_info_owner
   fi
   NEED_PREPARE_RESOLV_CONF=0
   if [ -n "$old_dhcp6_name_servers" ] ; then
      T_FILE_1=/tmp/dhcpv6_${$}_temp1_nameserver
      DNS_SERVERS=`sysevent get ipv6_nameserver`
      echo "$DNS_SERVERS" > $T_FILE_1
      for loop in $old_dhcp6_name_servers
      do
         ulog dhcpv6c status "release_dns_info: releasing $loop as nameserver."
         sed -i "/$loop/d" $T_FILE_1
      done
   fi
   if [ -n "$old_dhcp6_domain_search" ] ; then
      T_FILE_2=/tmp/dhcpv6_${$}_temp2_nameserver
      DNS_SEARCH=`sysevent get ipv6_domain`
      echo "$DNS_SEARCH" > $T_FILE_2
      for loop in $old_dhcp6_domain_search
      do
         ulog dhcpv6c status "release_dns_info: releasing $loop as domain."
         sed -i "/$loop/d" $T_FILE_2
      done
   fi
   if [ -f "$T_FILE_1" ] ; then
      sysevent set ipv6_nameserver `cat $T_FILE_1`
      rm -f $T_FILE_1
      NEED_PREPARE_RESOLV_CONF=1
   fi
   if [ -f "$T_FILE_2" ] ; then
      sysevent set ipv6_domain `cat $T_FILE_2`
      rm -f $T_FILE_2
      NEED_PREPARE_RESOLV_CONF=1
   fi
   if [ "1" = "$NEED_PREPARE_RESOLV_CONF" ] ; then
      prepare_resolver_conf
   fi
}
renew_dns_info() {
   PREPARE=0
   if [ -z "`sysevent get ipv6_nameserver`" ] ; then 
      if [ -n "$new_dhcp6_name_servers" ] ; then
         ulog dhcpv6c status "renew_dns_info: Provisioning $interface nameservers with $new_dhcp6_name_servers"
         sysevent set ipv6_nameserver "${new_dhcp6_name_servers}"
         PREPARE=1
      fi
   fi
   if [ -z "`sysevent get ipv6_domain`" ] ; then 
      if [ -n "$new_dhcp6_domain_search" ] ; then
         ulog dhcpv6c status "renew_dns_info: Provisioning $interface search order with $new_dhcp6_domain_search"
         sysevent set ipv6_domain "$new_dhcp6_domain_search"
         PREPARE=1
      fi
   fi
   if [ -n "$new_ip6_prefix" ] ; then 
      sysevent set dhcpv6_dns_info_owner $new_ip6_prefix
   fi
   if [ "1" = "$PREPARE" ] ; then
      prepare_resolver_conf
   fi
}
new_dns_info() {
   if [ -n "$new_dhcp6_name_servers" ] ; then
      ulog dhcpv6c status "new_dns_info: Provisioning $interface nameservers with $new_dhcp6_name_servers"
      sysevent set ipv6_nameserver "${new_dhcp6_name_servers}"
   fi
   if [ -n "$new_dhcp6_domain_search" ] ; then
      ulog dhcpv6c status "new_dns_info: Provisioning $interface search order with $new_dhcp6_domain_search"
      sysevent set ipv6_domain "$new_dhcp6_domain_search"
   fi
   sysevent set dhcpv6_dns_info_owner $new_ip6_prefix
   prepare_resolver_conf
}
new_aftr() {
   ulog dhcpv6c status "new_aftr: Provisioning sysevent with aftr $new_dhcp6_aftr from dhcp option"
   SUBSTRS=`echo $new_dhcp6_aftr | \
               awk ' { z = split( $0, array, ":") ; } ; \
                     { for(i=1;i<=z;i++)  \
                        {  \
                           printf ("0x%s " , array[i]) ; \
                        } ; \
                     } ;' \
                 `
   AFTR=
   FIRST=
   while [ -n "$SUBSTRS" ] ; do
      COUNT=`echo $SUBSTRS | cut -f1 -d ' '`
      COUNT=`dc $COUNT 0 + p`
      if [ -n "$COUNT" -a "$COUNT" -gt "0" ] ; then
            FIELD=`expr $COUNT + 1`
            CHUNK=`echo $SUBSTRS | cut -f2-${FIELD} -d ' ' `
            if [ -n "$FIRST" ] ; then
               AFTR=${AFTR}.
            else   
               FIRST=1
            fi
            for loop in $CHUNK ; do
               AFTR=${AFTR}`echo $loop | awk '{printf "%c", $1}'`
            done
            FIELD=`expr $FIELD + 1` 
            SUBSTRS=`echo $SUBSTRS | cut -f${FIELD}- -d ' '`
      else
            SUBSTRS=
      fi
   done     
  
   ulog dhcpv6c status "new_aftr: Provisioning sysevent with dhcpv6 acquired aftr $AFTR"
   sysevent set dhcpv6_aftr $AFTR
}
release_aftr() {
      if [ -n "`sysevent get dhcpv6_aftr`" ] ; then
         ulog dhcpv6c status "release_aftr: Unprovisioning dhcpv6 acquired aftr"
         sysevent set dhcpv6_aftr
      fi
}
panic()
{
   SYSCFG_lan_ifname=`syscfg get lan_ifname`
   SYSCFG_guest_lan_ifname=`syscfg get guest_lan_ifname`
   SYSCFG_loopback_ifname=lo
   LAN_OLD_ADDR=`sysevent get ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address`
   GUEST_LAN_OLD_ADDR=`sysevent get ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address`
   LO_OLD_ADDR=`sysevent get ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address`
   ulog dhcpv6c info "panic received. destroying prefix"
   destroy_ia_pd
   ulog dhcpv6c info "panic. Giving a chance radvd to deprecate the prefix"
   sleep 1
   if [ -n "$LAN_OLD_ADDR" ] ; then
      ip -6 addr del $LAN_OLD_ADDR dev $SYSCFG_lan_ifname
      sysevent set ${SYSCFG_lan_ifname}_dhcpv6_ia_pd_address
   fi
   if [ -n "$GUEST_LAN_OLD_ADDR" ] ; then
      ip -6 addr del $GUEST_LAN_OLD_ADDR dev $SYSCFG_lan_ifname
      sysevent set ${SYSCFG_guest_lan_ifname}_dhcpv6_ia_pd_address
   fi
   if [ -n "$LO_OLD_ADDR" ] ; then
      ip -6 addr del $LO_OLD_ADDR dev $SYSCFG_loopback_ifname scope global
      sysevent set ${SYSCFG_loopback_ifname}_dhcpv6_ia_pd_address
   fi
   ulog dhcpv6c info "panic. Removed all addresses"
   sysevent set ipv6_delegated_prefix
   delete_unreachable_ipv6route
}
