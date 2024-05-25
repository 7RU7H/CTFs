#!/bin/sh

########################################################
# sysinfo.sh ----> /www/sysinfo.cgi
#
# When adding new debug information into this script file
# do the following:
#    1)  create your debug script <your_debug_script.sh>
#    2)  call your debug script in this sysinfo.sh script
#        using the format:
#         if [ -f <your debug script> ]; then
#             ./<your debug script.cgi>
#         fi
########################################################
get_cgi_val () {
  if [ "$1" == "" ] ; then
    echo ""
    return
  fi
  form_var="$1"
  var_value=`echo "$QUERY_STRING" | sed -n "s/^.*$form_var=\([^&]*\).*$/\1/p" | sed "s/%20/ /g" | sed "s/+/ /g" | sed "s/%2F/\//g"`
  echo -n "$var_value"
}

# get interface names from sysevent
wan_ifname=`sysevent get wan_ifname`

SECTION=$(get_cgi_val "section")

echo Content-Type: text/plain
echo ""
echo "page generated on `date`"
echo ""
echo "UpTime:"
echo "`uptime`"
echo ""
if [ "$SECTION" == "" ] || [ "$SECTION" == "fwinfo" ] ; then
# echo "section=fwinfo"
# MFG DATA / Firmware information
echo "Firmware Version: `cat /etc/version`"
echo "Firmware Builddate: `cat /etc/builddate`"
echo "Product.type: `cat /etc/product.type`"
echo "Linux: `cat /proc/version`"
echo "Board: `cat /proc/bdutil/boardid`"
echo ""
if [ -f bootloader_info.cgi ] ; then
  ./bootloader_info.cgi
fi
echo ""
echo "-----Boot Data-----"
echo "cat /proc/cmdline: `cat /proc/cmdline`"
echo ""
echo "cat /proc/mtd: `cat /proc/mtd`"
echo ""
echo "----EPROM Manufacturer Data-----"
echo "`/usr/sbin/skuapi -g eeprom_version`"
echo "`/usr/sbin/skuapi -g model_sku`"
echo "`/usr/sbin/skuapi -g hw_version`"
echo "`/usr/sbin/skuapi -g hw_mac_addr`"
echo "`/usr/sbin/skuapi -g date`"
echo "`/usr/sbin/skuapi -g serial_number`"
echo "`/usr/sbin/skuapi -g uuid`"
echo "`/usr/sbin/skuapi -g wps_pin`"
echo ""
echo "----syscfg get device::xxxx ---Manufacturer Data-----"
echo "`syscfg show | grep device:: | grep recovery_key -v | grep pass -v`"
echo ""
fi
if [ "$SECTION" == "" ] || [ "$SECTION" == "debugInfo"] ; then
# echo "section=debugInfo"
# information helpful for debugging
#
echo "syscfg get ui remote_disabled:"
echo "`syscfg get ui remote_disabled`"
echo ""
echo "syscfg get ui remote_stunnel (use SSL):"
echo "`syscfg get ui remote_stunnel`"
echo ""
echo "syscfg get ui remote_host (remote ui host):"
echo "`syscfg get ui remote_host`"
echo ""
echo "syscfg get ui remote_port (remote ui port):"
echo "`syscfg get ui remote_port`"
echo ""
echo "syscfg get ui remote_stunnel_verify (verify remote ui stunnel):"
echo "`syscfg get ui remote_stunnel_verify`"
echo ""
echo "syscfg get cloud stunnel (use cloud SSL):"
echo "`syscfg get cloud stunnel`"
echo ""
echo "syscfg get cloud host (cloud host):"
echo "`syscfg get cloud host`"
echo ""
echo "syscfg get cloud port (cloud port):"
echo "`syscfg get cloud port`"
echo ""
echo "syscfg get cloud stunnel_verify (verify cloud stunnel):"
echo "`syscfg get cloud stunnel_verify`"
echo ""
echo "syscfg get mgmt_http_enable (can manage using http):"
echo "`syscfg get mgmt_http_enable`"
echo ""
echo "syscfg get mgmt_https_enable (can manage using https):"
echo "`syscfg get mgmt_https_enable`"
echo ""
echo "syscfg get mgmt_wifi_access (can manage wirelessly):"
echo "`syscfg get mgmt_wifi_access`"
echo ""
echo "syscfg get xmpp_enabled (can manage remotely)(true if not value not set):"
echo "`syscfg get xmpp_enabled`"
echo ""
echo "sysevent get xrac-status (xrac service state):"
echo "`sysevent get xrac-status`"
echo ""
echo "syscfg get owned_network_id:"
echo "`syscfg get owned_network_id`"
echo ""
echo "sysevent get phylink_wan_state (wanStatus):"
echo "`sysevent get phylink_wan_state`"
echo ""
echo "syscfg get wan_auto_detect_enable (isDetectingWANType):"
echo "`syscfg get wan_auto_detect_enable`"
echo ""
echo "syscfg get wan_proto (detectedWANType):"
echo "`syscfg get wan_proto`"
echo ""
echo "syscfg get bridge_mode (detectedWANType):"
echo "`syscfg get bridge_mode`"
echo ""
echo "sysevent get wan-status:"
echo "`sysevent get wan-status`"
echo ""
echo "sysevent get ipv4_wan_ipaddr:"
echo "`sysevent get ipv4_wan_ipaddr`"
echo ""
echo "sysevent get current_ipv6_wan_state:"
echo "`sysevent get current_ipv6_wan_state`"
echo ""
fi
if [ "$SECTION" == "" ] || [ "$SECTION" == "logs" ] ; then
# echo "section=logs"
# Generic log information information
echo "tail -200 /var/log/messages:"
echo ""
echo "`tail -200 /var/log/messages`"
echo ""
echo "/var/log/ipv6.log:"
echo ""
echo "`cat /var/log/ipv6.log`"
echo ""
echo "dmesg | tail -200:"
echo ""
echo "`dmesg | tail -200`"
echo ""
# More counter information
if [ -f get_counter_info.cgi ]; then
  ./get_counter_info.cgi
fi
fi
if [ "$SECTION" == "" ] || [ "$SECTION" == "system" ] ; then
# echo "section=system"
# system running processes and uptime information
echo "ps:"
echo ""
echo "`ps`"
# Disk use information
echo "disk usage:"
echo ""
echo "`df`"
echo ""
# system memory and process use information
echo "Memory Use:"
echo "free"
echo "`free`"
echo ""
echo "cat /proc/locks"
cat /proc/locks
echo ""
echo "cat /proc/modules"
cat /proc/modules
echo ""
echo "cat /proc/slabinfo"
cat /proc/slabinfo
echo ""
echo "cat /proc/vmstat"
cat /proc/vmstat
echo ""
echo "CPU Information"
echo "cat /proc/stat"
cat /proc/stat
echo ""
#it may display the password when changing router password, so filter service_file_sharing.sh
echo "top -bn1"
echo "`top -bn1 | sed '/service_file_sharing/d'`"
echo ""
fi
if [ "$SECTION" == "" ] || [ "$SECTION" == "wifi" ] ; then

if [ -f "/etc/init.d/service_wifi/get_wifi_runtime_info.sh" ] ; then
  echo "`sh /etc/init.d/service_wifi/get_wifi_runtime_info.sh`"
  echo ""
fi

if [ -f "/etc/init.d/service_wifi/wifi_debug_suppliment.sh" ] ; then
  echo "`sh /etc/init.d/service_wifi/wifi_debug_suppliment.sh`"
fi

if [ -f "/etc/init.d/service_wifi/wifi_debug_show_info.sh" ] ; then
  echo "WiFi debug info:"
  echo "`sh /etc/init.d/service_wifi/wifi_debug_show_info.sh`"
fi

fi

if [ "$SECTION" == "" ] || [ "$SECTION" == "ipnet" ] ; then
# echo "section=ipnet"
# IP networking information
echo "ifconfig:"
echo ""
echo "`ifconfig`"
echo ""
echo "cat /etc/resolv.conf:"
echo ""
echo "`cat /etc/resolv.conf`"
echo ""
echo "ip link:"
echo ""
echo "`ip link`"
echo ""
echo "ip neigh:"
echo ""
echo "`ip neigh`"
echo ""
echo "ip -6 addr show:"
echo ""
echo "`ip -6 addr show`"
echo ""
echo "ip route:"
echo ""
echo "`ip route`"
echo ""
echo "ip -6 route show:"
echo ""
echo "`ip -6 route show`"
echo ""
echo "ip tunnel show:"
echo ""
echo "`ip tunnel show`"
echo ""
echo "rdisc6 -r1 $wan_ifname:"
echo ""
echo "`rdisc6 -r1 $wan_ifname`"
echo ""
echo "rdisc6 -r1 ppp0:"
echo ""
echo "`rdisc6 -r1 ppp0`"
echo ""
echo "ping www.ietf.org:"
echo ""
echo "`ping -c2 www.ietf.org`"
echo ""
echo "ping 8.8.8.8:"
echo ""
echo "`ping -c2 8.8.8.8`"
echo ""
# network counters information
echo "NIC Counters"
echo "  br0 : `ifconfig br0 | grep 'RX bytes:'`"
echo " eth0 : `ifconfig eth0 | grep 'RX bytes:'`"
echo " eth1 : `ifconfig eth1 | grep 'RX bytes:'`"
echo " eth2 : `ifconfig eth2 | grep 'RX bytes:'`"
echo "vlan1 : `ifconfig vlan1 | grep 'RX bytes:'`"
echo "vlan2 : `ifconfig vlan2 | grep 'RX bytes:'`"
echo "wl1.2 : `ifconfig wl1.2 | grep 'RX bytes:'`"
echo ""
fi
echo "NTP server from DHCP option 42:"
echo "`sysevent get dhcpc_ntp_server1`"
echo "`sysevent get dhcpc_ntp_server2`"
echo "`sysevent get dhcpc_ntp_server3`"
echo ""

if [ "$SECTION" == "" ] || [ "$SECTION" == "diskinfo" ] ; then
echo "usb messages to dmesg"
dmesg | grep '^sd\|^scsi'
# mounted disk information
echo ""
echo "disk information:"
echo ""
echo "`for n in /dev/sd?; do parted -s $n print 2>&1; done`"
echo ""
echo "mounted filesystems:"
echo ""
echo "`mount`"
fi

if [ -f qos_info.cgi ] ; then
  ./qos_info.cgi
fi

if [ -f speedtest_info.cgi ] ; then
  ./speedtest_info.cgi
fi

if [ -f usbinfo.cgi ]; then
  ./usbinfo.cgi 1
fi

echo "list of open files"
/usr/sbin/lsof
