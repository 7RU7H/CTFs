#!/bin/sh 
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
SERVICE_NAME="ipv6_lan"
GUEST_LAN_CRON_FILE="/etc/cron/cron.everyminute/ipv6_lan_guest_lan_check.sh"
SELF_FILE="/etc/init.d/service_lan_ipv6.sh"
DELAYED_SYSEVENT_NAME=lan_ipv6_br1_started
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get ipv6_enable lan_ifname ula_enable lan_ula_prefix lo_ula_prefix dns_server_private_name private_domain ipv6_static_enable lan_ipv6_prefix guest_lan_ifname guest_enabled ipv6_verbose_logging`
   eval $FOO
  if [ $SYSCFG_FAILED = 'true' ] ; then
     ulog ipv6_lan status "$PID utctx failed to get some configuration data"
     ulog ipv6_lan status "$PID IPV6 LAN CANNOT BE CONTROLLED"
     exit
  fi
   SYSEVENT_current_lan_ipv6address=`sysevent get current_lan_ipv6address`
   SYSEVENT_current_guest_lan_ipv6address=`sysevent get current_guest_lan_ipv6address`
   SYSEVENT_current_lo_ipv6address=`sysevent get current_lo_ipv6address`
   if [ "1" = "$SYSCFG_ipv6_verbose_logging" ] ; then
      LOG=/var/log/ipv6.log
   else
      LOG=/dev/null
   fi
}
bring_static_lan_down() {
  
 sysevent set ipv6_delegated_prefix
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
   sysevent set radvd-reload
}
bring_static_lan_up() {
   if [ "1" -ne "$SYSCFG_ipv6_static_enable" ] ; then
      return
   fi
   clear_previous_lan_ipv6_prefix $SYSCFG_lan_ifname
   clear_previous_lan_ipv6_prefix $SYSCFG_guest_lan_ifname
   if [ -z "$SYSCFG_lan_ipv6_prefix" ] ; then
      echo "$SELF `date +%X`: Cannot start static IPv6 on LAN (address is not configured)" >> $LOG
      ulog static_ipv6_wan status "Cannot start static IPv6 on LAN (address is not configured)"
      return
   else
      LAN_PREFIX_LENGTH=`echo $SYSCFG_lan_ipv6_prefix | tr '/' ' ' | awk '{ print $2}'`
      if [ -z "$LAN_PREFIX_LENGTH" ] ;
      then
         LAN_PREFIX_LENGTH=64 # If no prefix length is specifed, then use the default /64 for Ethernet and other LAN
      else
         LAN_ADDRESS=`echo $SYSCFG_lan_ipv6_prefix | tr '/' ' ' | awk '{ print $1}'`
      fi
      eval `ip6calc -p $LAN_ADDRESS/$LAN_PREFIX_LENGTH`
      sysevent set ipv6_delegated_prefix "${PREFIX}/${LAN_PREFIX_LENGTH}"
      eval `ipv6_prefix_calc $PREFIX $LAN_PREFIX_LENGTH 0 0 3 64`
      if [ -z "$IPv6_PREFIX_1" ]
      then
         sysevent set ipv6_connection_state "static lan misconfigured"
         sysevent set ipv6-errinfo "Could not calculate lan prefix"
         sysevent set ipv6-status error
         return
      else
         if [ "1" = "$SYSCFG_guest_enabled" -a -n "$IPv6_PREFIX_2" ] ; then
            RC=`ip -4 addr show dev $SYSCFG_guest_lan_ifname` 
            if [ "0" != "$?" ] ; then
               ulog_debug ipv6 static "Guest LAN interface is not up yet. Deferring ipv6 provisioning on $SYSCFG_guest_lan_ifname"
               ASYNC=`sysevent get service_lan_ipv6_private_async`
               if [ -z "$ASYNC" ] ; then
                  echo "#! /bin/sh" > $GUEST_LAN_CRON_FILE
                  echo "sysevent set lan_ipv6_br1_started up" >> $GUEST_LAN_CRON_FILE
                  chmod 700 $GUEST_LAN_CRON_FILE
                  ASYNC=`sysevent async $DELAYED_SYSEVENT_NAME $SELF_FILE`
                  sysevent set service_lan_ipv6_private_async "$ASYNC"
               fi 
            else
               eval `ip6calc -p ${IPv6_PREFIX_2}`
               create_eui_64 $SYSCFG_guest_lan_ifname
               eval `ip6calc -o ${PREFIX} ::${EUI_64}`
               GUEST_LAN_ADDRESS=$OR
               sysevent set current_guest_lan_ipv6address $GUEST_LAN_ADDRESS
               SYSEVENT_current_guest_lan_ipv6address=$GUEST_LAN_ADDRESS
               ulog_debug ipv6 static "Guest LAN address is $SYSEVENT_current_guest_lan_ipv6address"
               echo "ip -6 addr add ${SYSEVENT_current_guest_lan_ipv6address}/64 dev $SYSCFG_guest_lan_ifname" >> $LOG
               ip -6 addr add ${SYSEVENT_current_guest_lan_ipv6address}/64 dev $SYSCFG_guest_lan_ifname >> $LOG 2>&1
               sysevent set ${SYSCFG_guest_lan_ifname}_ipv6_prefix_lifetime_default 1
               save_lan_ipv6_prefix $SYSCFG_guest_lan_ifname ${IPv6_PREFIX_2}
            fi
         fi
         if [ -n "$IPv6_PREFIX_3" ] ; then
            eval `ip6calc -p ${IPv6_PREFIX_3}`
            create_eui_64 lo
            eval `ip6calc -o ${PREFIX} ::${EUI_64}`
            LOOPBACK_ADDRESS=$OR
            sysevent set current_lo_ipv6address $LOOPBACK_ADDRESS
            SYSEVENT_current_lo_ipv6address=$LOOPBACK_ADDRESS
            ulog_debug ipv6 static "Loopback address is $LOOPBACK_ADDRESS"
            echo "ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev lo" >> $LOG
            ip -6 addr add ${LOOPBACK_ADDRESS}/64 dev lo >> $LOG 2>&1
         fi
         eval `ip6calc -p ${IPv6_PREFIX_1}`
         create_eui_64 $SYSCFG_lan_ifname
         eval `ip6calc -o ${PREFIX} ::${EUI_64}`
         LAN_ADDRESS=$OR
         sysevent set current_lan_ipv6address $LAN_ADDRESS
         SYSEVENT_current_lan_ipv6address=$LAN_ADDRESS
         ulog_debug ipv6 static "LAN address is $SYSEVENT_current_lan_ipv6address"
         echo "ip -6 addr add ${SYSEVENT_current_lan_ipv6address}/64 dev $SYSCFG_lan_ifname" >> $LOG
         ip -6 addr add ${SYSEVENT_current_lan_ipv6address}/64 dev $SYSCFG_lan_ifname >> $LOG 2>&1
         sysevent set ${SYSCFG_lan_ifname}_ipv6_prefix_lifetime_default 1
         save_lan_ipv6_prefix $SYSCFG_lan_ifname ${IPv6_PREFIX_1}
      fi
      sysevent set radvd-reload
   fi
}
prepare_ula()
{
   if [ "1" = "$SYSCFG_ula_enable" ]
   then
      if [ -z "$SYSCFG_lan_ula_prefix" ] 
      then
         PREFIX="fd"
         MAC=`ip link show $SYSCFG_lan_ifname | grep link | awk '{print $2}'`
         SN=`syscfg get device serial_number`
         RANDOM_STRING=`echo ${SN}${MAC} | sha1sum`
         RANDOM_STRING=`echo $RANDOM_STRING | cut -d '-' -f1`
         LEN=`expr length $RANDOM_STRING`
         OFFSET=`expr $LEN - 10`
         PREFIX=${PREFIX}`echo ${RANDOM_STRING:${OFFSET}:2}`
         OFFSET=`expr $OFFSET + 2`
         for i in $OFFSET `expr $OFFSET + 4`
         do
            NEXTi=`expr $i + 2`
            PREFIX=${PREFIX}":"`echo ${RANDOM_STRING:${i}:2}``echo ${RANDOM_STRING:${NEXTi}:2}`
         done
         PREFIX=${PREFIX}"::"
         eval `ipv6_prefix_calc $PREFIX 48 0 0 2 64`
         if [ -n "$IPv6_PREFIX_1" ]
         then
            syscfg set lan_ula_prefix $IPv6_PREFIX_1
            SYSCFG_lan_ula_prefix=$IPv6_PREFIX_1
         fi
         if [ -z "$SYSCFG_lo_ula_prefix" -a -n "$IPv6_PREFIX_2" ]
         then
            syscfg set lo_ula_prefix $IPv6_PREFIX_2
            SYSCFG_lan_ula_prefix=$IPv6_PREFIX_2
         fi
         syscfg commit
      fi
   fi
}
ula_networks_up ()
{
   if [ "1" != "$SYSCFG_ula_enable" ]
   then
      return
   fi
   if [ -n "$SYSCFG_lan_ula_prefix" ]
   then
      SYSEVENT_br0_ula_prefix=${SYSCFG_lan_ula_prefix}
      sysevent set br0_ula_prefix $SYSEVENT_br0_ula_prefix
      eval `ip6calc -p $SYSEVENT_br0_ula_prefix`
      create_eui_64 $SYSCFG_lan_ifname
      eval `ip6calc -o ${PREFIX} ::${EUI_64}`
      SYSEVENT_br0_ula_ipaddress=$OR
      sysevent set br0_ula_ipaddress $SYSEVENT_br0_ula_ipaddress
      ip -6 addr add $SYSEVENT_br0_ula_ipaddress/64 scope global dev ${SYSCFG_lan_ifname}
   fi
   if [ -n "$SYSCFG_lo_ula_prefix" ]
   then
      SYSEVENT_lo_ula_prefix=${SYSCFG_lo_ula_prefix}
      sysevent set lo_ula_prefix $SYSEVENT_lo_ula_prefix
      eval `ip6calc -p $SYSEVENT_lo_ula_prefix`
      create_eui_64 lo 
      eval `ip6calc -o ${PREFIX} ::${EUI_64}`
      SYSEVENT_lo_ula_ipaddress=$OR
      sysevent set lo_ula_ipaddress $SYSEVENT_lo_ula_ipaddress
      ip -6 addr add $SYSEVENT_lo_ula_ipaddress/64 scope global dev lo
   fi
}
ula_networks_down ()
{
   SYSEVENT_br0_ula_prefix=`sysevent get br0_ula_prefix`
   SYSEVENT_br0_ula_ipaddress=`sysevent get br0_ula_ipaddress`
   if [ -n "$SYSEVENT_br0_ula_prefix" ]
   then
      ip -6 addr del $SYSEVENT_br0_ula_ipaddress/64 scope global dev ${SYSCFG_lan_ifname}
   fi
   SYSEVENT_lo_ula_prefix=`sysevent get lo_ula_prefix`
   SYSEVENT_lo_ula_ipaddress=`sysevent get lo_ula_ipaddress`
   if [ -n "$SYSEVENT_lo_ula_prefix" ]
   then
      ip -6 addr del ${SYSEVENT_lo_ula_ipaddress}/64 scope global dev lo
   fi
  sysevent set br0_ula_prefix
  sysevent set br0_ula_ipaddress
  sysevent set lo_ula_prefix
  sysevent set lo_ula_ipaddress
}
do_start()
{
   ulog ipv6_lan status "bringing up lan ipv6 interface"
   prepare_ula
   ula_networks_up
   bring_static_lan_up
   ulog ipv6_lan status "lan ipv6 interface up"
}
do_stop()
{
   ulog ipv6_lan status "bringing down lan ipv6 interface"
   ula_networks_down
   bring_static_lan_down
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status starting
      do_start
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $? "Unable to bringup ipv6 lan"
      else
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status started
      fi
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status` 
   if [ "stopped" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status stopping
      do_stop
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $ERR "Unable to teardown ipv6 lan"
      else
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status stopped
      fi
   fi
}
service_init 
case "$1" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      service_start
      sysevent set ipv6_firewall-restart
      ;;
   ipv6-start)
      LAN_STATUS=`sysevent get lan-status` 
      if [ "started" = "$LAN_STATUS" ] 
      then
         service_start
      fi
      ;; 
   ipv6-restart)
      service_stop
      LAN_STATUS=`sysevent get lan-status` 
      if [ "started" = "$LAN_STATUS" ] 
      then
         service_start
      fi
      ;; 
   ipv6-stop)
      service_stop
      ;;
   lan-stopped)
      service_stop
      ;;
   lan-started)
      if [ "$SYSCFG_ipv6_enable" != "0" ]
      then
         service_start
      fi
      ;;
   lan_ipv6_br1_started)
      BR1_STATUS=`sysevent get $DELAYED_SYSEVENT_NAME`
      if [ "up"  = "$BR1_STATUS" ] ; then
         ASYNC=`sysevent get service_lan_ipv6_private_async`
         if [ -n "$ASYNC" ] ; then 
            sysevent rm_async $ASYNC
         fi
         rm -f $GUEST_LAN_CRON_FILE
         bring_static_lan_down
         bring_static_lan_up
         sysevent set $DELAYED_SYSEVENT_NAME 
      fi
      ;;
   *)
      echo "Usage: service_lan_ipv6 [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
