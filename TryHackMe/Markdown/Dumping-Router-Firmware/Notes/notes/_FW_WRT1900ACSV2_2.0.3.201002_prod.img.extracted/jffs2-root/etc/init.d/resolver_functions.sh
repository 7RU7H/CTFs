#!/bin/sh
HOSTS_FILE=/etc/hosts
HOSTNAME_FILE=/etc/hostname
RESOLV_CONF="/etc/resolv.conf"
wan_info_by_namespace_local()
{
   if [ -z "$1" ] ; then
      return 255
   fi
   NS=$1
   eval `utctx_cmd get ${NS}::wan_proto`
   eval `echo SYSCFG_LOCAL_wan_proto='$'SYSCFG_${NS}_wan_proto`
   if [ -n "$SYSCFG_LOCAL_wan_proto" ] ; then
      if [ "none" = "$SYSCFG_LOCAL_wan_proto" ] ; then
         return 1
      fi
   else
      return 255
   fi
   eval ARGS=\"\
        $NS::default \
        $NS::forwarding \
        $NS::service_name \
        $NS::pptp_address_static \
        $NS::l2tp_address_static \
        $NS::wan_ipaddr \
        $NS::wan_netmask \
        $NS::wan_default_gateway \
        $NS::nameserver1 \
        $NS::nameserver2 \
        $NS::nameserver3 \"
   eval `utctx_cmd get $ARGS`
   eval `echo SYSCFG_LOCAL_default='$'SYSCFG_${NS}_default`
   eval `echo SYSCFG_LOCAL_forwarding='$'SYSCFG_${NS}_forwarding`
   eval `echo SYSCFG_LOCAL_service_name='$'SYSCFG_${NS}_service_name`
   eval `echo SYSCFG_LOCAL_wan_proto='$'SYSCFG_${NS}_wan_proto`
   eval `echo SYSCFG_LOCAL_pptp_address_static='$'SYSCFG_${NS}_pptp_address_static`
   eval `echo SYSCFG_LOCAL_l2tp_address_static='$'SYSCFG_${NS}_l2tp_address_static`
   eval `echo SYSCFG_LOCAL_wan_ipaddr='$'SYSCFG_${NS}_wan_ipaddr`
   eval `echo SYSCFG_LOCAL_wan_netmask='$'SYSCFG_${NS}_wan_netmask`
   eval `echo SYSCFG_LOCAL_wan_default_gateway='$'SYSCFG_${NS}_wan_default_gateway`
   eval `echo SYSCFG_LOCAL_nameserver1='$'SYSCFG_${NS}_nameserver1`
   eval `echo SYSCFG_LOCAL_nameserver2='$'SYSCFG_${NS}_nameserver2`
   eval `echo SYSCFG_LOCAL_nameserver3='$'SYSCFG_${NS}_nameserver3`
   if [ "legacy" = "$SYSCFG_LOCAL_wan_proto" ] ; then
      SYSCFG_LOCAL_wan_proto=`syscfg get wan_proto`
      SYSCFG_LOCAL_pptp_address_static=`syscfg get pptp_address_static`
      SYSCFG_LOCAL_l2tp_address_static=`syscfg get l2tp_address_static`
      SYSCFG_LOCAL_wan_ipaddr=`syscfg get wan_ipaddr`
      SYSCFG_LOCAL_wan_netmask=`syscfg get wan_netmask`
      SYSCFG_LOCAL_wan_default_gateway=`syscfg get wan_default_gateway`
      SYSCFG_LOCAL_nameserver1=`syscfg get nameserver1`
      SYSCFG_LOCAL_nameserver2=`syscfg get nameserver2`
      SYSCFG_LOCAL_nameserver3=`syscfg get nameserver3`
   fi
   return 0
}
prepare_hostname () {
   HOSTNAME=`syscfg get hostname`
   LAN_IPADDR=`/sbin/ip  addr show dev br0  | grep "inet "| awk '{split($2,foo, "/"); print (foo[1]);}'`
   if [ "" != "$HOSTNAME" ] ; then
      echo "$HOSTNAME" > $HOSTNAME_FILE
      hostname $HOSTNAME
   fi
   if [ -n "$LAN_IPADDR" -a  -n "$HOSTNAME" ] ; then
      echo "$LAN_IPADDR     $HOSTNAME" > $HOSTS_FILE
   else
      echo -n > $HOSTS_FILE
   fi
   echo "127.0.0.1       localhost" >> $HOSTS_FILE
   echo "::1             localhost" >> $HOSTS_FILE
   echo "::1             ip6-localhost ip6-loopback" >> $HOSTS_FILE
   echo "fe00::0         ip6-localnet" >> $HOSTS_FILE
   echo "ff00::0         ip6-mcastprefix" >> $HOSTS_FILE
   echo "ff02::1         ip6-allnodes" >> $HOSTS_FILE
   echo "ff02::2         ip6-allrouters" >> $HOSTS_FILE
   echo "ff02::3         ip6-allhosts" >> $HOSTS_FILE
}
add_statically_provisioned_nameservers () {
    dhcp_dns_1=`syscfg get dhcp_nameserver_1`
    dhcp_dns_2=`syscfg get dhcp_nameserver_2`
    dhcp_dns_3=`syscfg get dhcp_nameserver_3`
   
    set_dhcp_dns="0"
    if [ -n "$dhcp_dns_1" -a "$dhcp_dns_1" != "0.0.0.0" ] ; then
        echo "nameserver $dhcp_dns_1" >> $TEMP_RESOLV_CONF
        set_dhcp_dns="1"
    fi
    if [ -n "$dhcp_dns_2" -a "$dhcp_dns_2" != "0.0.0.0" ] ; then
        echo "nameserver $dhcp_dns_2" >> $TEMP_RESOLV_CONF
        set_dhcp_dns="1"
    fi
    if [ -n "$dhcp_dns_3" -a "$dhcp_dns_3" != "0.0.0.0" ] ; then
        echo "nameserver $dhcp_dns_3" >> $TEMP_RESOLV_CONF
        set_dhcp_dns="1"
    fi
    if [ "$set_dhcp_dns" = "1" ] ; then
        return
    fi
   MAX_COUNT=`syscfg get max_wan_count`
   if [ -z "$MAX_COUNT" ] ; then
      MAX_COUNT=0
   fi
   CURCOUNT=1
   while [ $MAX_COUNT -ge $CURCOUNT ] ; do
      i="wan_"${CURCOUNT}
      CURCOUNT=`expr $CURCOUNT + 1`
      wan_info_by_namespace_local $i 
      if [ "0" = "$?" ] ; then
         DO_STATIC_NAMESERVERS=0
         case "$SYSCFG_LOCAL_wan_proto" in
            static)
               DO_STATIC_NAMESERVERS=1
               ;;
            pptp)
               if [ "1" = "$SYSCFG_LOCAL_pptp_address_static" ] ; then
                  DO_STATIC_NAMESERVERS=1
               fi
               ;;
            l2tp)
               if [ "1" = "$SYSCFG_LOCAL_l2tp_address_static" ] ; then
                  DO_STATIC_NAMESERVERS=1
               fi
               ;;
         esac
         if [ "1" = "$DO_STATIC_NAMESERVERS" ] ; then
            if [ -n "$SYSCFG_LOCAL_nameserver1" ]  && [ "0.0.0.0" !=  "$SYSCFG_LOCAL_nameserver1" ] ; then
               echo "nameserver $SYSCFG_LOCAL_nameserver1" >> $TEMP_RESOLV_CONF
            fi
            if [ -n "$SYSCFG_LOCAL_nameserver2" ]  && [ "0.0.0.0" !=  "$SYSCFG_LOCAL_nameserver2" ] ; then
               echo "nameserver $SYSCFG_LOCAL_nameserver2" >> $TEMP_RESOLV_CONF
            fi
            if [ -n "$SYSCFG_LOCAL_nameserver3" ]  && [ "0.0.0.0" !=  "$SYSCFG_LOCAL_nameserver3" ] ; then
               echo "nameserver $SYSCFG_LOCAL_nameserver3" >> $TEMP_RESOLV_CONF
            fi
         fi   
      fi
   done
}
prepare_resolver_conf () {
   eval `utctx_cmd get wan_proto router_dns_domain bridge_mode bridge_ipaddr bridge_netmask bridge_default_gateway bridge_nameserver1 bridge_nameserver2 bridge_nameserver3 bridge_domain nameserver1 nameserver2 nameserver3 ipv6_enable ipv6_domain dhcp_server_propagate_wan_nameserver`
   SYSEVENT_dhcp_domain=`sysevent get dhcp_domain`
   SYSEVENT_ipv6_domain=`sysevent get ipv6_domain`
   SYSEVENT_wan_dynamic_dns=`sysevent get wan_dynamic_dns`
   SYSEVENT_ipv6_nameserver=`sysevent get ipv6_nameserver`
   SYSEVENT_wan_ppp_dns=`sysevent get wan_ppp_dns`
   REAL_RESOLV_CONF="/etc/resolv.conf"
   TEMP_RESOLV_CONF="/tmp/resolv.conf.$$"
   echo -n  > $TEMP_RESOLV_CONF
   if [ "1" = "$SYSCFG_bridge_mode" -o "2" = "$SYSCFG_bridge_mode" ] ; then
      if [ "2" = "$SYSCFG_bridge_mode" ] ; then
         if [ -n "$SYSCFG_bridge_ipaddr" -a -n "$SYSCFG_bridge_netmask" -a -n "$SYSCFG_bridge_default_gateway" ] ; then
            if [ -n "$SYSCFG_bridge_domain" ] ; then
               echo "search $SYSCFG_bridge_domain" >> $TEMP_RESOLV_CONF
            fi
            if [ -n "$SYSCFG_bridge_nameserver1" ]  && [ "0.0.0.0" !=  "$SYSCFG_bridge_nameserver1" ] ; then
               echo "nameserver $SYSCFG_bridge_nameserver1" >> $TEMP_RESOLV_CONF
            fi
            if [ -n "$SYSCFG_bridge_nameserver2" ]  && [ "0.0.0.0" !=  "$SYSCFG_bridge_nameserver2" ] ; then
               echo "nameserver $SYSCFG_bridge_nameserver2" >> $TEMP_RESOLV_CONF
            fi
            if [ -n "$SYSCFG_bridge_nameserver3" ]  && [ "0.0.0.0" !=  "$SYSCFG_bridge_nameserver3" ] ; then
               echo "nameserver $SYSCFG_bridge_nameserver3" >> $TEMP_RESOLV_CONF
            fi
         else 
            echo "incorrect provisioning for bridge mode static $SYSCFG_bridge_ipaddr,$SYSCFG_bridge_netmask,$SYSCFG_bridge_default_gateway." > /dev/console
         fi
      else
         if [ -n "$SYSEVENT_dhcp_domain" ] ; then
            echo "search $SYSEVENT_dhcp_domain" >> $TEMP_RESOLV_CONF
         fi
         for i in $SYSEVENT_wan_dynamic_dns ; do
            echo nameserver $i >> $TEMP_RESOLV_CONF
         done
      fi
   else
      if [ -n "$SYSEVENT_dhcp_domain" ] ; then
         echo "search $SYSEVENT_dhcp_domain" >> $TEMP_RESOLV_CONF
      fi
      echo "nameserver 127.0.0.1" >> $TEMP_RESOLV_CONF
      add_statically_provisioned_nameservers
      if [ "static" = "$SYSCFG_wan_proto" ] ; then
         if [ "" != "$SYSCFG_router_dns_domain" ] ; then
            echo "search $SYSCFG_router_dns_domain" >> $TEMP_RESOLV_CONF
         fi
         echo "nameserver 127.0.0.1" >> $TEMP_RESOLV_CONF
         add_statically_provisioned_nameservers
      else
         case "$SYSCFG_wan_proto" in
           pppoe | pptp | l2tp)
              for i in $SYSEVENT_wan_ppp_dns ; do
                 echo nameserver $i >> $TEMP_RESOLV_CONF
              done
              ;;
           *)
              for i in $SYSEVENT_wan_dynamic_dns ; do
                 echo nameserver $i >> $TEMP_RESOLV_CONF
              done
              ;;
         esac
      fi
      RESTART_IPV4_DHCP_SERVER=0
 
      if [ -z "$SYSCFG_router_dns_domain" ] ; then
         for i in $SYSEVENT_dhcp_domain ; do
            TEST_NS=`grep " $i" $REAL_RESOLV_CONF`
            if [ "" = "$TEST_NS" ] ; then
               RESTART_IPV4_DHCP_SERVER=1
            fi
         done
      fi
      if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
         for i in $SYSEVENT_wan_dynamic_dns ; do
            TEST_NS=`grep  " $i" $REAL_RESOLV_CONF`
            if [ "" = "$TEST_NS" ] ; then
               RESTART_IPV4_DHCP_SERVER=1
            fi
         done
      fi
   fi
   if [ "1" = "$SYSCFG_ipv6_enable" ] ; then
      if [ -n "$SYSEVENT_ipv6_domain" ] ; then
         echo "search $SYSEVENT_ipv6_domain" >> $TEMP_RESOLV_CONF
      fi
      if [ "0" != "$SYSCFG_ipv6_enable" ] ; then
         for server in $SYSEVENT_ipv6_nameserver
         do
            echo "nameserver $server" >> $TEMP_RESOLV_CONF
         done
      fi
      if [  -z  "$SYSCFG_bridge_mode" -o "0" = "$SYSCFG_bridge_mode" ] ; then   
         RESTART_IPV6_DHCP_SERVER=0
         if [ -z "$SYSCFG_router_dns_domain" ] ; then
            for i in $SYSEVENT_ipv6_domain ; do
               TEST_NS=`grep $i $REAL_RESOLV_CONF`
               if [ "" = "$TEST_NS" ] ; then
                  RESTART_IPV6_DHCP_SERVER=1
               fi
            done
         fi
         if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
            for i in $SYSEVENT_ipv6_nameserver ; do
               TEST_NS=`grep $i $REAL_RESOLV_CONF`
               if [ "" = "$TEST_NS" ] ; then
                  RESTART_IPV6_DHCP_SERVER=1
               fi
            done
         fi
      fi
   fi
   if [ ! -s $TEMP_RESOLV_CONF ] ; then
      echo "nameserver 127.0.0.1" > $REAL_RESOLV_CONF
      rm -f $TEMP_RESOLV_CONF
   else
      cat $TEMP_RESOLV_CONF > $REAL_RESOLV_CONF
      rm -f $TEMP_RESOLV_CONF
      if [ "1" = "$RESTART_IPV6_DHCP_SERVER" ] ; then
         if [ "started" = `sysevent get dhcpv6_server-status` ] ; then
            sysevent set dhcpv6_server-restart
         fi
      fi
   fi
}
add_static_routes () {
count=0
for i in $1
do
   if [ 0 = `expr $count % 2` ]; then
      NET=$i
   else
      ROUTER=$i
      ip route add $NET via $ROUTER dev $2
   fi
   count=`expr $count + 1`
done
}
delete_static_routes () {
count=0
for i in $1
do
   if [ 0 = `expr $count % 2` ]; then
      NET=$i
   else
      ROUTER=$i
      ip route del $NET via $ROUTER
   fi
   count=`expr $count + 1`
done
}
is_platform_pinnacles()
{
    RET_VAL="1"
    MODEL=`syscfg get device::model_base`
    if [ -z "$MODEL" ] ; then
        MODEL=`syscfg get device::modelNumber`
        MODEL=${MODEL%-*}
    fi
    HW_REVISION=`syscfg get device::hw_revision`
    if [ "$MODEL" = "EA2700" ] || [ "$MODEL" = "EA2700OQ" ] || [ "$MODEL" = "EA6500" -a "$HW_REVISION" = "1" ] ; then
    	RET_VAL="0"
    fi
    return $RET_VAL
}
reset_ethernet_ports() {
    if [ "`syscfg get hardware_vendor_name`" = "Broadcom" ] ; then
        is_platform_pinnacles
        IS_PINNACLES=$?
        lan_ports=`syscfg get switch::router_1::port_numbers`
        if [ "$IS_PINNACLES" = "1" ] ; then       
            if [ -n "$lan_ports" ] ; then
                echo "Power cycle Ethernet ports." > /dev/console
                for i in $lan_ports; do
                    et -i eth0 robowr 0x1$i 0x00 0x1940
                done
                sleep 1.5
                for i in $lan_ports; do
                    et -i eth0 robowr 0x1$i 0x00 0x1140
                done
            fi
        else
            if [ -n "$lan_ports" ] ; then
                echo "Power cycle Ethernet ports." > /dev/console
                for i in $lan_ports; do
                    et phywr $i 0 0x1940
                done
                sleep 1.5
                for i in $lan_ports; do
                    et phywr $i 0 0x1140
                done
            fi
        fi
    elif [ "`syscfg get hardware_vendor_name`" = "Marvell" ] ; then
        if [ -d /sys/devices/platform/mv_switch ] ; then
            lan_ports=`syscfg get switch::router_1::port_numbers`
            if [ -n "$lan_ports" ] ; then
                echo "Power cycle Ethernet ports." > /dev/console
                for i in $lan_ports; do
                    echo $i 0 1 0x1940 > /sys/devices/platform/mv_switch/reg_w
                done
                sleep 1.5
                for i in $lan_ports; do
                    echo $i 0 1 0x1140 > /sys/devices/platform/mv_switch/reg_w
                done
            fi
        else
            if [ -d /sys/class/neta-switch/port0/power ] ; then
                lan_ports=/sys/class/neta-switch/port[0123]/power_config
            else
                lan_ports=/sys/class/neta-switch/port[0123]/power
            fi
        
            if [ -n "$lan_ports" ] ; then
                echo "Power cycle Ethernet ports." > /dev/console
	            for i in $lan_ports; do
	                echo 0 > $i
	            done
	            sleep 1.5
	            for i in $lan_ports; do
	                echo 1 > $i
	            done
            fi
        fi
    elif [ "`syscfg get hardware_vendor_name`" = "MediaTek" ] ; then
        if [ "`cat /etc/product`" = "taurus" ] ; then
            echo "Power cycle Ethernet ports." > /dev/console
            for i in 0 1 2 3 ; do
                mii_mgr -s -p $i -r 0 -v 0x3900 > /dev/null
            done
            sleep 1.5
            for i in 0 1 2 3 ; do
                mii_mgr -s -p $i -r 0 -v 0x3100 > /dev/null
            done
        else
            echo "Power cycle Ethernet ports." > /dev/console
            switch reg w 7014 1e0000c
            switch reg w 7014 2e0000c
            switch reg w 7014 3e0000c
            switch reg w 7014 4e0000c
            sleep 1.5
            switch reg w 7014 e0000c
        fi
    elif [ "`syscfg get hardware_vendor_name`" = "QCA" ] ; then
        echo "Power cycle Ethernet ports." > /dev/console
        for i in 0 1 2 3; do
            ssdk_sh debug phy set $i 0 0x1800 > /dev/null 2>&1
        done
        sleep 1.5
        for i in 0 1 2 3; do
            ssdk_sh debug phy set $i 0 0x1000 > /dev/null 2>&1
        done
    fi
}
