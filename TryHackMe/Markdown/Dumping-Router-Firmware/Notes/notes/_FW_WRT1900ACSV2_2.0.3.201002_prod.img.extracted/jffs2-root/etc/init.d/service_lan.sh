#!/bin/sh
source /etc/init.d/interface_functions.sh
source /etc/init.d/network_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
if [ -f /etc/init.d/brcm_wlan.sh ]; then
    source /etc/init.d/brcm_wlan.sh
fi
if [ -f /etc/init.d/brcm_ethernet_helper.sh ]; then
    source /etc/init.d/brcm_ethernet_helper.sh
fi
SERVICE_NAME="lan"
LAN_DEBUG_SETTING=`syscfg get ${SERVICE_NAME}_debug`
DEBUG() 
{
    [ "$LAN_DEBUG_SETTING" = "1" ] && $@
}
DEBUG set -x
bringup_ethernet_interfaces() {
   if [ "" != "$SYSCFG_lan_ethernet_virtual_ifnums" ] ; then
       for loop in $SYSCFG_lan_ethernet_physical_ifnames
       do
         config_vlan $loop $SYSCFG_lan_ethernet_virtual_ifnums
       done
   fi
}
is_intf_used_for_nfsboot() {
   grep -q "root=/dev/nfs .*$1" /proc/cmdline
   return $?
}
teardown_ethernet_interfaces() {
   if [ "" = "$SYSCFG_lan_ethernet_virtual_ifnums" ] ; then
      for loop in $SYSCFG_lan_ethernet_physical_ifnames
      do
          is_intf_used_for_nfsboot $loop
          nfs=$?
          if [ $nfs != "0" ] ; then
             ip link set $loop down
          fi
      done
   else
      if [ "Broadcom" = $SYSCFG_hardware_vendor_name ] ; then
          for loop in $SYSCFG_lan_ethernet_virtual_ifnums
          do
              is_intf_used_for_nfsboot "vlan${loop}"
              nfs=$?
              if [ $nfs != "0" ] ; then
                 ip link set vlan${loop} down
              fi
          done
      else
          for loop in $SYSCFG_lan_ethernet_virtual_ifnums
          do
              unconfig_vlan $loop
          done
      fi
   fi
}
bringup_wireless_interfaces() {
   if [ "Broadcom" != $SYSCFG_hardware_vendor_name ] ; then		
       return 
   fi
   INCR_AMOUNT=10
   if [ "" != "$SYSCFG_lan_wl_physical_ifnames" ] ; then
       for loop in $SYSCFG_lan_wl_physical_ifnames
       do
           OUR_MAC=`get_mac "eth0"`
           REPLACEMENT=`incr_mac $OUR_MAC $INCR_AMOUNT`
           ip link set $loop addr $REPLACEMENT
           ip link set $loop allmulticast on
           ulog lan status "setting $loop hw address to $REPLACEMENT"
           INCR_AMOUNT=`expr $INCR_AMOUNT + 1`
           if [ "eth1" = $loop ] ; then
               WL_STATE=`syscfg get wl0_state`
           else
               WL_STATE=`syscfg get wl1_state`
           fi
           ulog lan status "wlancfg $loop $WL_STATE"
           wlancfg $loop $WL_STATE
           enslave_a_interface $loop $SYSCFG_lan_ifname
      done
   fi
   bringup_wireless_daemons
}
teardown_wireless_interfaces() {
   if [ "Broadcom" != $SYSCFG_hardware_vendor_name ] ; then		
       return 
   fi
   for loop in $SYSCFG_lan_wl_physical_ifnames
   do
   if [ "Broadcom" = "$SYSCFG_hardware_vendor_name" ] ; then
	  STR=`brctl show | grep $loop`
	  if [ ! -z "$STR" ]; then
	  	brctl delif $SYSCFG_lan_ifname $loop
	  fi
	  ifconfig $loop down
   else
	  wlancfg $loop down
   fi
   done
}
do_start()
{
   ulog lan status "bringing up lan interface"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status starting
   DNS_CONF=`cat /etc/resolv.conf`
   if [ -z "$DNS_CONF" ]; then
      echo "nameserver 127.0.0.1" >> /etc/resolv.conf
   fi
   if [ "Broadcom" != $SYSCFG_hardware_vendor_name ]; then
       bringup_ethernet_interfaces
   fi
   brctl setfd $SYSCFG_lan_ifname 0
   brctl stp $SYSCFG_lan_ifname off
   for loop in $LAN_IFNAMES
   do
      is_intf_used_for_nfsboot $loop
      nfs=$?
      if [ ! \( $nfs = "0" \) ]; then
          enslave_a_interface $loop $SYSCFG_lan_ifname
      fi
   done
      SYSEVENT_ipv4_wan_ipaddr=`sysevent get ipv4_wan_ipaddr`
      SYSEVENT_ipv4_wan_subnet=`sysevent get ipv4_wan_subnet`
      if [ -n "$SYSEVENT_ipv4_wan_ipaddr" ]
      then 
         if [ "0" = "$SYSEVENT_ipv4_wan_ipaddr" -o "0.0.0.0" = "$SYSEVENT_ipv4_wan_ipaddr" ]
         then
            SYSEVENT_ipv4_wan_ipaddr=
            SYSEVENT_ipv4_wan_subnet=
         else 
            if [ -z "$SYSEVENT_ipv4_wan_subnet" -o "0.0.0.0" = "$SYSEVENT_ipv4_wan_subnet" ] ; then
               SYSEVENT_ipv4_wan_subnet=255.255.255.0
            fi
         fi
      else
         SYSEVENT_ipv4_wan_subnet=
      fi
      calculate_lan_networks $SYSEVENT_ipv4_wan_ipaddr $SYSEVENT_ipv4_wan_subnet
      SYSCFG_lan_ipaddr=`syscfg get lan_ipaddr`
   ip addr add $SYSCFG_lan_ipaddr/$SYSCFG_lan_netmask broadcast + dev $SYSCFG_lan_ifname
   ip link set $SYSCFG_lan_ifname up 
   ip link set $SYSCFG_lan_ifname allmulticast on 
   ulog lan status "switch off bridge pkts to iptables (bridge-nf-call-arptables)"
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-arptables
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-iptables
   echo 0 > /proc/sys/net/bridge/bridge-nf-call-ip6tables
   echo 0 > /proc/sys/net/bridge/bridge-nf-filter-vlan-tagged
   echo 0 > /proc/sys/net/bridge/bridge-nf-filter-pppoe-tagged
   if [ -z "$SYSCFG_hardware_vendor_name" -o "Broadcom" = $SYSCFG_hardware_vendor_name ] ; then		
       vendor_block_dos_land_attack
   fi
   
    start_broadcom_emf   
   if [ "`syscfg get gmac3_enable`" = "1" ] ; then
      ip link set fwd0 up 
      ip link set fwd0 allmulticast on
      ip link set fwd0 promisc on
      ip link set fwd1 up 
      ip link set fwd1 allmulticast on
      ip link set fwd1 promisc on
   fi
   ulog lan status "lan interface up"
}
do_stop()
{
   sysevent set ${SERVICE_NAME}-stopping
   ulog lan status "tearing down lan interface"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status stopping
   /etc/init.d/service_wifi/service_wifi.sh wifi-stop
   /etc/init.d/service_qos.sh qos-stop   
   /etc/init.d/service_redirector.sh redirector-stop   
   /etc/init.d/service_guardian.sh guardian-stop
  
   teardown_wireless_interfaces
   teardown_ethernet_interfaces
   ip link set $SYSCFG_lan_ifname down
   ip addr flush dev $SYSCFG_lan_ifname
   for loop in $LAN_IFNAMES
   do
      is_intf_used_for_nfsboot $loop
      nfs=$?
      if [ ! \( $nfs = "0" \) ]; then
          ip link set $loop down
          brctl delif $SYSCFG_lan_ifname $loop
      fi
   done
   ip link set $SYSCFG_lan_ifname down
   if [ "`syscfg get gmac3_enable`" = "1" ] ; then
      ip link set fwd0 down
      ip link set fwd1 down
   fi
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get lan_ifname lan_ethernet_virtual_ifnums lan_ethernet_physical_ifnames lan_wl_physical_ifnames lan_wl_virtual_ifnames lan_ipaddr lan_netmask hardware_vendor_name bridge_mode`
   eval $FOO
  if [ $SYSCFG_FAILED = 'true' ] ; then
     ulog lan status "$PID utctx failed to get some configuration data"
     ulog lan status "$PID LAN CANNOT BE CONTROLLED"
     exit
  fi
   if [ "" = "$SYSCFG_lan_ethernet_virtual_ifnums" ] ; then
      LAN_IFNAMES="$SYSCFG_lan_ethernet_physical_ifnames"
   else
       for loop in $SYSCFG_lan_ethernet_physical_ifnames
       do
         LAN_IFNAMES="$LAN_IFNAMES vlan$SYSCFG_lan_ethernet_virtual_ifnums"
       done
   fi
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      SYSEVENT_ipv4_wan_ipaddr=`sysevent get ipv4_wan_ipaddr`
      SYSEVENT_ipv4_wan_subnet=`sysevent get ipv4_wan_subnet`
      if [ -n "$SYSEVENT_ipv4_wan_ipaddr" -a "0.0.0.0" != "$SYSEVENT_ipv4_wan_ipaddr" ] ; then
         if [ -z "$SYSEVENT_ipv4_wan_subnet" -o "0.0.0.0" = "$SYSEVENT_ipv4_wan_subnet" ] ; then
            SYSEVENT_ipv4_wan_subnet=255.255.255.0
         fi
         calculate_lan_networks $SYSEVENT_ipv4_wan_ipaddr $SYSEVENT_ipv4_wan_subnet
         if [ "$?" = "1" ] ; then
            ulog lan status "conflict between lan and wan ipv4 addresses detected and repaired"
            sysevent set lan-start
            exit
         fi
      fi
      do_start
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $? "Unable to bringup lan"
      else
         sysevent set ${SERVICE_NAME}-started
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status started
         sysevent set reboot-status lan-started
         ulog lan status "reboot-status:lan-started"
         MODEL_NAME=`syscfg get device::model_base`
         if [ -z "$MODEL_NAME" ] ; then
            MODEL_NAME=`syscfg get device::modelNumber`
            MODEL_NAME=${MODEL_NAME%-*}
         fi
         if [ "$MODEL_NAME" != "PE10" ] ; then
            sysevent set system_state-normal
         fi
      fi
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status` 
   if [ "stopped" != "$STATUS" ] ; then
      do_stop
      ERR=$?
      if [ "$ERR" -ne "0" ] ; then
         check_err $ERR "Unable to teardown lan"
      else
         sysevent set ${SERVICE_NAME}-stopped
         sysevent set ${SERVICE_NAME}-errinfo
         sysevent set ${SERVICE_NAME}-status stopped
         sysevent set igd-status stopped
         sysevent set reboot-status lan-stopped
         ulog lan status "reboot-status:lan-stopped"
      fi
   fi
}
shutdown_lan() {
    [ -d /sys/class/neta-switch ] && lan_ports=/sys/class/neta-switch/port[0123]/power
    [ -n "$lan_ports" ] && {
	for i in $lan_ports; do
	    echo 0 > $i
	done
    }
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
      sysevent set firewall-restart
      ;;
   ipv4_wan_ipaddr)
      if [ "$SYSCFG_bridge_mode" = "0" ] ; then
         SYSEVENT_ipv4_wan_ipaddr=`sysevent get ipv4_wan_ipaddr`
         SYSEVENT_ipv4_wan_subnet=`sysevent get ipv4_wan_subnet`
         if [ -n "$SYSEVENT_ipv4_wan_ipaddr" -a "0.0.0.0" != "$SYSEVENT_ipv4_wan_ipaddr" ] ; then
            if [ -z "$SYSEVENT_ipv4_wan_subnet" -o "0.0.0.0" = "$SYSEVENT_ipv4_wan_subnet" ] ; then
               SYSEVENT_ipv4_wan_subnet=255.255.255.0
            fi
            calculate_lan_networks $SYSEVENT_ipv4_wan_ipaddr $SYSEVENT_ipv4_wan_subnet
         fi
      fi
      ;;
    shutdown)
	shutdown_lan
	;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
