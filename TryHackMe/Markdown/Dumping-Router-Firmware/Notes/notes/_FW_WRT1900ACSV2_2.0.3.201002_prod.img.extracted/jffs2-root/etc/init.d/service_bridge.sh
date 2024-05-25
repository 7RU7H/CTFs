#!/bin/sh
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/resolver_functions.sh
if [ -f /etc/init.d/brcm_ethernet_helper.sh ]; then
    source /etc/init.d/brcm_ethernet_helper.sh
fi
if [ -f /etc/init.d/brcm_wlan.sh ]; then
    source /etc/init.d/brcm_wlan.sh
fi
SERVICE_NAME="bridge"
BRIDGE_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$BRIDGE_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
UDHCPC_PID_FILE=/var/run/bridge_udhcpc.pid
UDHCPC_SCRIPT=/etc/init.d/service_bridge/dhcp_link.sh
HANDLER="$UDHCPC_SCRIPT"
AUTO_BRIDGING=/usr/sbin/auto_bridging
unregister_dhcp_client_handlers() {
   asyncid=`sysevent get ${SERVICE_NAME}_async_id_1`;
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_async_id_1
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_async_id_2`;
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_async_id_2
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_async_id_3`;
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_async_id_3
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_async_id_4`;
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_async_id_4
   fi
   asyncid=`sysevent get ${SERVICE_NAME}_async_id_5`;
   if [ -n "$asyncid" ] ; then
      sysevent rm_async $asyncid
      sysevent set ${SERVICE_NAME}_async_id_5
   fi
}
register_dhcp_client_handlers() {
   unregister_dhcp_client_handlers
   asyncid=`sysevent async dhcp_client-start "$HANDLER"`;
   sysevent setoptions dhcp_client-start $TUPLE_FLAG_EVENT
   sysevent set ${SERVICE_NAME}_async_id_1 "$asyncid"
   asyncid=`sysevent async dhcp_client-stop "$HANDLER"`;
   sysevent setoptions dhcp_client-stop $TUPLE_FLAG_EVENT
   sysevent set ${SERVICE_NAME}_async_id_2 "$asyncid"
   asyncid=`sysevent async dhcp_client-restart "$HANDLER"`;
   sysevent setoptions dhcp_client-restart $TUPLE_FLAG_EVENT
   sysevent set ${SERVICE_NAME}_async_id_3 "$asyncid"
   asyncid=`sysevent async dhcp_client-release "$HANDLER"`;
   sysevent setoptions dhcp_client-release $TUPLE_FLAG_EVENT
   sysevent set ${SERVICE_NAME}_async_id_4 "$asyncid"
   asyncid=`sysevent async dhcp_client-renew "$HANDLER"`;
   sysevent setoptions dhcp_client-renew $TUPLE_FLAG_EVENT
   sysevent set ${SERVICE_NAME}_async_id_5 "$asyncid"
}
bringup_ethernet_interfaces() {
    ip link set $SYSCFG_lan_ethernet_physical_ifnames up
    ip link set $SYSCFG_wan_physical_ifname up
    return 0
}
teardown_ethernet_interfaces() {
   for loop in $SYSCFG_lan_ethernet_physical_ifnames
   do
      ip link set $loop down
   done
}
teardown_wireless_interfaces() {
    /etc/init.d/service_wifi/service_wifi.sh wifi-stop
}
register_handlers() {
    register_dhcp_client_handlers
    asyncid=`sysevent async phylink_wan_state /etc/init.d/service_bridge/dhcp_link.sh`
    sysevent set phylink_wan_state_asyncid "$asyncid"
}
do_start()
{
   ulog bridge status "bringing up lan interface in bridge mode"
   bringup_ethernet_interfaces
   
   brctl setfd $SYSCFG_lan_ifname 0
   brctl stp $SYSCFG_lan_ifname on
    which nvram > /dev/null
    if [ $? = 0 ] ; then
        if [ "$SYSCFG_wan_virtual_ifnum" != "" -a -n "`nvram get vlan${SYSCFG_wan_virtual_ifnum}ports`" ] ; then
            enslave_a_interface vlan$SYSCFG_wan_virtual_ifnum $SYSCFG_lan_ifname
        fi
    fi
    for loop in $LAN_IFNAMES
   do
      grep -q "root=/dev/nfs .*$loop" /proc/cmdline
      nfs=$?
      if [ ! \( $nfs = "0" \) ]; then
          enslave_a_interface $loop $SYSCFG_lan_ifname
      fi
   done
   ip link set $SYSCFG_lan_ifname up 
   ip link set $SYSCFG_lan_ifname allmulticast on 
    
   prepare_hostname
    start_broadcom_emf
   if [ "3" = "$SYSCFG_bridge_mode" ] ; then
      sysevent set lan-errinfo
      sysevent set lan-status starting
    elif [ "2" = "$SYSCFG_bridge_mode" ] ; then
        if [ -n "$SYSCFG_bridge_ipaddr_start" -a -n "$SYSCFG_bridge_ipaddr_range" ] ; then
            start_ip=$SYSCFG_bridge_ipaddr_start
            start=`echo $start_ip | cut -f 4 -d '.'`
            prefix=`echo $start_ip | cut -f 1-3 -d '.'`
            i=1
            sleep 5
            while [ $i -le $SYSCFG_bridge_ipaddr_range ]
            do
                arping -D -q -I $SYSCFG_lan_ifname -c 4 $prefix.$start
                DAD=`echo $?`
                if [ "$DAD" != "0" ] ; then
                    ulog dhcp_link status "Duplicated address detected $prefix.$start. Try the next one."
                    i=`expr $i + 1`
                    start=`expr $start + 1`
                else
                    SYSCFG_bridge_ipaddr=$prefix.$start
                    syscfg set bridge_ipaddr $SYSCFG_bridge_ipaddr
                    break
                fi
            done
        fi
      if [ -n "$SYSCFG_bridge_ipaddr" -a -n "$SYSCFG_bridge_netmask" -a -n "$SYSCFG_bridge_default_gateway" ]; then
          ip -4 addr add $SYSCFG_bridge_ipaddr/$SYSCFG_bridge_netmask broadcast + dev $SYSCFG_lan_ifname
          ip -4 route add default dev $SYSCFG_lan_ifname via $SYSCFG_bridge_default_gateway
          sysevent set ipv4_wan_ipaddr $SYSCFG_bridge_ipaddr
          sysevent set ipv4_wan_subnet $SYSCFG_bridge_netmask
          sysevent set default_router $SYSCFG_bridge_default_gateway
          sysevent set firewall-restart
          prepare_resolver_conf
          sysevent set lan-started
          sysevent set lan-errinfo
          sysevent set lan-status started
          sysevent set wan-status started
          sysevent set wan-started
      fi
   else
      if [ -n "$SYSCFG_bridge_ipaddr" -a  -n "$SYSCFG_bridge_netmask" ] ; then
         ip -4 addr add  $SYSCFG_bridge_ipaddr/$SYSCFG_bridge_netmask broadcast + dev $SYSCFG_lan_ifname
         sysevent set lan-errinfo
         sysevent set lan-status starting
      fi
        register_handlers
      udhcpc -S -b -i $SYSCFG_lan_ifname -h $SYSCFG_hostname -p $UDHCPC_PID_FILE  --arping -s $UDHCPC_SCRIPT $DHCPC_EXTRA_PARAMS
        sysevent set current_ipv4_wan_state up
   fi
   ulog bridge status "switch off bridge pkts to iptables (bridge-nf-call-arptables)"
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-arptables
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-iptables
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-ip6tables
   echo 0 > /proc/sys/net/bridge/bridge-nf-filter-vlan-tagged
   echo 0 > /proc/sys/net/bridge/bridge-nf-filter-pppoe-tagged
   
   if [ "$SYSCFG_guest_enabled" = "1" ] ; then
      echo 1 > /proc/sys/net/ipv4/ip_forward
   fi
   if [ "`syscfg get gmac3_enable`" = "1" ] ; then
      ip link set fwd0 up 
      ip link set fwd0 allmulticast on
      ip link set fwd0 promisc on
      ip link set fwd1 up 
      ip link set fwd1 allmulticast on
      ip link set fwd1 promisc on
   fi
   ulog bridge status "lan interface up"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status up
}
do_stop()
{
   unregister_dhcp_client_handlers
   if [ "0" != "`syscfg get bridge_mode`" ] && [ -f /etc/init.d/service_wifi/service_wifi_sta.sh ] ; then
      /etc/init.d/service_wifi/service_wifi_sta.sh wifi_sta-stop
   fi
   teardown_wireless_interfaces
   teardown_ethernet_interfaces
   ip link set $SYSCFG_lan_ifname down
   ip addr flush dev $SYSCFG_lan_ifname
   for loop in $LAN_IFNAMES
   do
      ip link set $loop down
      brctl delif $SYSCFG_lan_ifname $loop
   done
   ip link set $SYSCFG_wan_physical_ifname down
   ip link set $SYSCFG_lan_ifname down
   if [ "`syscfg get gmac3_enable`" = "1" ] ; then
      ip link set fwd0 down
      ip link set fwd1 down
   fi
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status down
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get bridge_mode lan_ifname lan_ethernet_virtual_ifnums lan_ethernet_physical_ifnames lan_wl_physical_ifnames lan_wl_virtual_ifnames wan_virtual_ifnum wan_physical_ifname bridge_ipaddr bridge_netmask bridge_default_gateway bridge_nameserver1 bridge_nameserver2 bridge_nameserver3 bridge_domain hostname hardware_vendor_name dhcpc_trusted_dhcp_server guest_enabled bridge_ipaddr_start bridge_ipaddr_range`
   eval $FOO
  if [ $SYSCFG_FAILED = 'true' ] ; then
     ulog bridge status "$PID utctx failed to get some configuration data"
     ulog bridge status "$PID BRIDGE CANNOT BE CONTROLLED"
     exit
  fi
  if [ -n "$SYSCFG_dhcpc_trusted_dhcp_server" ]
  then
     DHCPC_EXTRA_PARAMS="-X $SYSCFG_dhcpc_trusted_dhcp_server"
  fi
  if [ -z "$SYSCFG_hostname" ] ; then
     SYSCFG_hostname="Utopia"
  fi 
    case $SYSCFG_hardware_vendor_name in
        "Broadcom")
            LAN_IFNAMES=vlan$SYSCFG_lan_ethernet_virtual_ifnums
            ;;
        "Marvell")
            LAN_IFNAMES="$SYSCFG_lan_ethernet_physical_ifnames"
            ;;
        "MediaTek")
            LAN_IFNAMES="$SYSCFG_lan_ethernet_physical_ifnames"
            ;;
        "QCA")
            LAN_IFNAMES="$SYSCFG_lan_ethernet_physical_ifnames"
            ;;
    esac
}
service_start ()
{
   wait_till_end_state lan
   STATUS=`sysevent get lan-status`
   if [ "started" != "$STATUS" ] ; then
      do_start
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $? "Unable to bringup bridge"
      else
         sysevent set system_state-normal
      fi
   fi
}
service_stop ()
{
   wait_till_end_state lan
   STATUS=`sysevent get lan-status` 
   if [ "stopped" != "$STATUS" ] ; then
      do_stop
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $ERR "Unable to teardown bridge"
      else
         sysevent set lan-stopped
         sysevent set lan-errinfo
         sysevent set lan-status stopped
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
      sysevent set lan-restarting 1
      service_stop
      service_start
      sysevent set lan-restarting 0
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
