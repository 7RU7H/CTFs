#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
SIXRD_TUNNEL_INTERFACE_NAME="tun6rd"
DESIRED_WAN_STATE=`sysevent get desired_ipv6_wan_state`
CURRENT_WAN_STATE=`sysevent get current_ipv6_wan_state`
CURRENT_LINK_STATE=`sysevent get current_ipv6_link_state`
PID="($$)"
service_init ()
{
   eval `utctx_cmd get 6rd_zone 6rd_zone_length 6rd_relay 6rd_common_prefix4 ipv6_verbose_logging lan_ifname guest_enabled guest_lan_ifname`
   SYSEVENT_ipv4_wan_ipaddr=`sysevent get ipv4_wan_ipaddr`
   SYSEVENT_current_lan_ipv6address=`sysevent get current_lan_ipv6address`
   SYSEVENT_current_guest_lan_ipv6address=`sysevent get current_guest_lan_ipv6address`
   SYSEVENT_current_lo_ipv6address=`sysevent get current_lo_ipv6address`
   if [ "1" = "$SYSCFG_ipv6_verbose_logging" ] ; then
      LOG=/var/log/ipv6.log
   else
      LOG=/dev/null
   fi
}
service_init_2 ()
{
   SIXRD_PREFIX=`sysevent get 6rd_zone`
   SIXRD_PREFIX=${SIXRD_PREFIX:-$SYSCFG_6rd_zone}
   if [ -z "$SIXRD_PREFIX" ] ; then
      ulog_error ipv6 6rd "6rd is misconfigured: No 6rd prefix"
      sysevent set ipv6-status error
      sysevent set ipv6-errinfo "No 6rd prefix configured"
      exit 1
   fi
   SIXRD_PREFIX_LENGTH=`sysevent get 6rd_zone_length`
   SIXRD_PREFIX_LENGTH=${SIXRD_PREFIX_LENGTH:-$SYSCFG_6rd_zone_length}
   if [ -z "$SIXRD_PREFIX_LENGTH" ] ; then
      ulog_error ipv6 6rd "6rd is misconfigured: No 6rd prefix length"
      sysevent set ipv6-status error
      sysevent set ipv6-errinfo "No 6rd prefix length configured"
   fi
   eval `ip6calc -p $SIXRD_PREFIX\/$SIXRD_PREFIX_LENGTH`
   SIXRD_PREFIX=$PREFIX
   SIXRD_RELAY=$SYSCFG_6rd_relay
   if [ -z "$SIXRD_RELAY" ] ; then
      ulog_error ipv6 6rd "6rd is misconfigured: No 6rd relay"
      sysevent set ipv6-status error
      sysevent set ipv6-errinfo "No 6rd relay configured"
   else
      if [ "`echo $SIXRD_RELAY | egrep -c '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+'`" = "0" ] ; then
         SIXRD_RELAY=`nslookup $SIXRD_RELAY | awk '
                      /^Name:/ { body=1 ; }
                      /^Address .*: .*:.*/ { next ; }
                      /^Address .*:/ { if (body == 1) {print $3 ; exit ;} }
                        '`
         echo "`date +%X`: DNS was used to translate the 6rd_relay to $SIXRD_RELAY" >> $LOG
      fi
   fi
   SIXRD_IPV4_MASK_LEN=`sysevent get 6rd_common_prefix4`
   SIXRD_IPV4_MASK_LEN=${SIXRD_IPV4_MASK_LEN:-$SYSCFG_6rd_common_prefix4}
   if [ -z "$SIXRD_IPV4_MASK_LEN" ] ; then
      SIXRD_IPV4_MASK_LEN=0
   fi
}
bring_wan_down() {
   service_init
   SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ipv6_ifname`
   sysevent set ipv6_connection_state "6rd connection going down"
   sysevent set ipv6-errinfo
   sysevent set ipv6-status stopping
   ip -6 route del default via ::$SIXRD_RELAY dev ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   ip -6 route flush dev ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   ip link set dev ${SYSEVENT_current_wan_ipv6_ifname} down >> $LOG 2>&1
   ip tunnel del ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   if [ -n "${SYSEVENT_current_lo_ipv6address}" ] 
   then
      ip -6 addr del ${SYSEVENT_current_lo_ipv6address}/64 dev lo >> $LOG 2>&1
      sysevent set current_lo_ipv6address
      SYSEVENT_current_lo_ipv6address=
   fi
   if [ -n "${SYSEVENT_current_guest_lan_ipv6address}" ] 
   then
      ip -6 addr del ${SYSEVENT_current_guest_lan_ipv6address}/64 dev $SYSCFG_guest_lan_ifname >> $LOG 2>&1
      sysevent set ${SYSCFG_guest_lan_ifname}_ipv6_prefix_lifetime_default
      save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname 
      sysevent set current_guest_lan_ipv6address
      SYSEVENT_current_guest_lan_ipv6address=
   fi
   if [ -n "${SYSEVENT_current_lan_ipv6address}" ]
   then
      ip -6 addr del ${SYSEVENT_current_lan_ipv6address}/64 dev $SYSCFG_lan_ifname >> $LOG 2>&1
      sysevent set ${SYSCFG_lan_ifname}_ipv6_prefix_lifetime_default
      save_lan_ipv6_prefix $SYSCFG_lan_ifname 
      sysevent set current_lan_ipv6address
      SYSEVENT_current_lan_ipv6address=
   fi
   sysevent set ipv6_wan-stopped
   sysevent set ipv6_delegated_prefix
   if [ "6rd" = "`sysevent get current_wan_ipv6address_owner`" ] ; then
      sysevent set current_wan_ipv6address
   fi
   sysevent set current_ipv6_wan_state down
   sysevent set ipv6-status stopped
   sysevent set ipv6_firewall-restart
   sysevent set sixrd_state
   sysevent set radvd-reload
   sysevent set 6rd_tunnel_status
   sysevent set ipv6_connection_state "6rd connection down"
}
bring_wan_up() {
   SIXRD_STATE=`sysevent get sixrd_state`
   if [ "started" = "$SIXRD_STATE" ] ; then
      ulog ipv6 6rd "$PID bring_wan_up already started. Exit"
      exit 0
   fi
   sysevent set sixrd_state started
   service_init
   service_init_2
   sysevent set ipv6_connection_state "6rd connection going up"
   sysevent set current_wan_ipv6_ifname $SIXRD_TUNNEL_INTERFACE_NAME
   SYSEVENT_current_wan_ipv6_ifname="$SIXRD_TUNNEL_INTERFACE_NAME"
   clear_previous_lan_ipv6_prefix $SYSCFG_lan_ifname
   clear_previous_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
   eval `ipv6_prefix_calc $SIXRD_PREFIX $SIXRD_PREFIX_LENGTH $SYSEVENT_ipv4_wan_ipaddr $SIXRD_IPV4_MASK_LEN 3 64 `
   if [ -z "$IPv6_PREFIX_1" ] 
   then
      sysevent set ipv6_connection_state "6rd misconfigured"
      sysevent set ipv6-errinfo "Could not calculate lan prefix"
      sysevent set ipv6-status error
      exit 0
   else
      if [ -n "$IPv6_PREFIX_1" -a -n "$SIXRD_PREFIX_LENGTH" ] ; then
         if [ -n "$SIXRD_IPV4_MASK_LEN" ] ; then
            MASK_LEN=$SIXRD_IPV4_MASK_LEN
         else
            MASK_LEN=0
         fi
         PREF_LEN=`expr $SIXRD_PREFIX_LENGTH + 32`
         PREF_LEN=`expr $PREF_LEN - $MASK_LEN`
         if [ "$PREF_LEN" -gt "64" ] ; then
            PREF_LEN=64
         fi
         sysevent set ipv6_delegated_prefix ${IPv6_PREFIX_1}
      fi
      eval `ip6calc -p ${IPv6_PREFIX_1}`
      create_eui_64 $SYSCFG_lan_ifname
      eval `ip6calc -o ${PREFIX} ::${EUI_64}`
      TUN6RD_ADDRESS=$OR
      LAN_ADDRESS=$OR
      if [ "1" = "$SYSCFG_guest_enabled" -a -n "$IPv6_PREFIX_2" ] ; then
         eval `ip6calc -p ${IPv6_PREFIX_2}`
         create_eui_64 $SYSCFG_guest_lan_ifname
         eval `ip6calc -o ${PREFIX} ::${EUI_64}`
         GUEST_LAN_ADDRESS=$OR
      fi
      if [ -n "$IPv6_PREFIX_3" ] ; then
         eval `ip6calc -p ${IPv6_PREFIX_3}`
         create_eui_64 lo
         eval `ip6calc -o ${PREFIX} ::${EUI_64}`
         LOOPBACK_ADDRESS=$OR
      fi
   fi
   echo "ip tunnel add ${SYSEVENT_current_wan_ipv6_ifname} mode sit ttl 32 local $SYSEVENT_ipv4_wan_ipaddr"  >> $LOG
   ip tunnel add ${SYSEVENT_current_wan_ipv6_ifname} mode sit ttl 32 local $SYSEVENT_ipv4_wan_ipaddr  >> $LOG 2>&1
   eval `ipcalc -n $SIXRD_RELAY/$SIXRD_IPV4_MASK_LEN`
   SIXRD_RELAY_PREFIX="$NETWORK"
   echo "ip tunnel 6rd dev ${SYSEVENT_current_wan_ipv6_ifname} 6rd-prefix $SIXRD_PREFIX/$SIXRD_PREFIX_LENGTH 6rd-relay_prefix $SIXRD_RELAY_PREFIX/$SIXRD_IPV4_MASK_LEN" >> $LOG
   ip tunnel 6rd dev ${SYSEVENT_current_wan_ipv6_ifname} 6rd-prefix $SIXRD_PREFIX/$SIXRD_PREFIX_LENGTH 6rd-relay_prefix $SIXRD_RELAY_PREFIX/$SIXRD_IPV4_MASK_LEN >> $LOG 2>&1
   echo "ip link set dev ${SYSEVENT_current_wan_ipv6_ifname} up" >> $LOG
   ip link set dev ${SYSEVENT_current_wan_ipv6_ifname} up >> $LOG 2>&1
   echo "ip -6 addr del ::${SYSEVENT_ipv4_wan_ipaddr}/128 dev ${SYSEVENT_current_wan_ipv6_ifname}" >> $LOG
   ip -6 addr del ::${SYSEVENT_ipv4_wan_ipaddr}/128 dev ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   echo "ip -6 addr add ::${SYSEVENT_ipv4_wan_ipaddr}/96 dev ${SYSEVENT_current_wan_ipv6_ifname}" >> $LOG
   ip -6 addr add ::${SYSEVENT_ipv4_wan_ipaddr}/96 dev ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   echo "ip -6 addr add ${TUN6RD_ADDRESS}/128 dev ${SYSEVENT_current_wan_ipv6_ifname}" >> $LOG
   ip -6 addr add ${TUN6RD_ADDRESS}/128 dev ${SYSEVENT_current_wan_ipv6_ifname} >> $LOG 2>&1
   echo "ip -6 route add default via ::$SIXRD_RELAY dev ${SYSEVENT_current_wan_ipv6_ifname} metric 2048" >> $LOG
   ip -6 route add default via ::$SIXRD_RELAY dev ${SYSEVENT_current_wan_ipv6_ifname} metric 2048 >> $LOG 2>&1
   CURRENT_OWNER=`sysevent get current_wan_ipv6address_owner`
   if [ -z "$CURRENT_OWNER" -o "6rd" = "$CURRENT_OWNER"  ] ; then
      CHOPPED_ADDR=`echo ${TUN6RD_ADDRESS} | cut -d'/' -f1`
      sysevent set current_wan_ipv6address $CHOPPED_ADDR
   fi
   if [ "1" = "$SYSCFG_guest_enabled" -a -n "$GUEST_LAN_ADDRESS" ] ; then
      sysevent set current_guest_lan_ipv6address $GUEST_LAN_ADDRESS
      SYSEVENT_current_guest_lan_ipv6address=$GUEST_LAN_ADDRESS
      ulog_debug ipv6 6rd "Guest LAN address is $SYSEVENT_current_guest_lan_ipv6address"
      echo "ip -6 addr add ${SYSEVENT_current_guest_lan_ipv6address}/64 dev $SYSCFG_guest_lan_ifname" >> $LOG
      ip -6 addr add ${SYSEVENT_current_guest_lan_ipv6address}/64 dev $SYSCFG_guest_lan_ifname >> $LOG 2>&1
      sysevent set ${SYSCFG_guest_lan_ifname}_ipv6_prefix_lifetime_default 1
      save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname ${IPv6_PREFIX_2}/64
   fi
   sysevent set current_lan_ipv6address $LAN_ADDRESS
   SYSEVENT_current_lan_ipv6address=$LAN_ADDRESS
   ulog_debug ipv6 6rd "LAN address is $SYSEVENT_current_lan_ipv6address based on $SYSEVENT_ipv4_wan_ipaddr for 6RD"
   echo "ip -6 addr add ${SYSEVENT_current_lan_ipv6address}/64 dev $SYSCFG_lan_ifname" >> $LOG
   ip -6 addr add ${SYSEVENT_current_lan_ipv6address}/64 dev $SYSCFG_lan_ifname >> $LOG 2>&1
   sysevent set ${SYSCFG_lan_ifname}_ipv6_prefix_lifetime_default 1
   save_lan_ipv6_prefix $SYSCFG_lan_ifname ${IPv6_PREFIX_1}
   if [ -n "$LOOPBACK_ADDRESS=" ] ; then
      sysevent set current_lo_ipv6address $LOOPBACK_ADDRESS
      SYSEVENT_current_lo_ipv6address=$LOOPBACK_ADDRESS
      ulog_debug ipv6 6rd "Loopback $SYSEVENT_current_lo_ipv6address"
      echo "ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev lo" >> $LOG
      ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev lo >> $LOG 2>&1
   fi
   sysevent set ipv6_firewall-restart
   sysevent set current_ipv6_wan_state up
   sysevent set ipv6_wan-started
   sysevent set ipv6-status started
   sysevent set ipv6_firewall-restart
   sysevent set ipv6_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   sysevent set radvd-reload
   sysevent set 6rd_nud-start
   sysevent set ipv6_connection_state "6rd connection up"
}
case "$1" in
   current_ipv6_link_state)
      ulog ipv6 6rd "$PID ipv6 link state is $CURRENT_LINK_STATE"
      if [ "up" != "$CURRENT_LINK_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 6rd "$PID ipv6 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog ipv6 6rd "$PID ipv6 link is down. Wan is already down"
            exit 0
         fi
      else
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 6rd "$PID ipv6 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$DESIRED_WAN_STATE" ] ; then
               bring_wan_up
               exit 0
            else
               ulog ipv6 6rd "$PID ipv6 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv6_wan_state)
      CURRENT_IPV6_STATUS=`sysevent get ipv6_wan-status`
      if [ "up" = "$DESIRED_WAN_STATE" ] ; then
         if [ "up" = "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 6rd "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$CURRENT_LINK_STATE" ] ; then
               ulog ipv6 6rd "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$CURRENT_WAN_STATE" ] ; then
            ulog ipv6 6rd "$PID wan is already down."
            if [ "stopped" != "$CURRENT_IPV6_STATUS" ] ; then
               sysevent set ipv6_wan-stopped
               sysevent set ipv6-status stopped
               sysevent set ipv6_firewall-restart
            fi
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog ipv6 6rd "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
