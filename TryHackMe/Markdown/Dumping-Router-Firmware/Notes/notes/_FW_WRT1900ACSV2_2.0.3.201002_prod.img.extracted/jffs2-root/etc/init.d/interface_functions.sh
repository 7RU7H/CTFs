get_network () {
   TEMP=""
   LAST=""
   SAVEIFS=$IFS
   IFS=.
   for p in $1
   do
       if [ "" = "$LAST" ] ; then
          LAST=$TEMP
       else
          LAST=$LAST"."$TEMP
       fi
       TEMP=$p
   done
   IFS=$SAVEIFS
   echo $LAST
}
get_mac () {
   OUR_MAC=`ip link show $1 | grep link | awk '{print $2}'`
   echo $OUR_MAC
}
incr_mac () {                                                    
   COUNTER=$2                                                                
   LAST_BYTE=`echo $1 | awk 'BEGIN { FS = ":" } ; { printf ("%d", "0x"$6) }'`
   while [ $COUNTER -gt 0 ]                                                  
   do                                                                        
      T_BYTE=`expr $LAST_BYTE + 1`                                           
      if [ "255" = "$T_BYTE" ] ; then                                        
         T_BYTE=00                                                           
      fi                                                                     
      LAST_BYTE=$T_BYTE              
      COUNTER=`expr $COUNTER - 1`    
   done                                                             
                                                                    
   INCREMENTED_BYTE=`echo $LAST_BYTE | awk '{printf ("%02X", $1) }'`
                                                                                
   TRUNCATED_MAC=`echo $1 | awk 'BEGIN { FS = ":" } ; { printf ("%02X:%02X:%02X:%02X:%02X:", "0x"$1, "0x"$2, "0x"$3, "0x"$4, "0x"$5) }'`
   REPLACED_MAC="$TRUNCATED_MAC""$INCREMENTED_BYTE"                             
   echo "$REPLACED_MAC"                                                         
}
convert_v4_2_v6 () {                                                    
   V6=`echo $1 | awk 'BEGIN { FS = "." } ; { printf ("%02x%02x:%02x%02x", $1, $2, $3, $4) }'`
   echo "$V6"                                                         
}
config_vlan () {
    ip link set $1 up
    vconfig set_name_type VLAN_PLUS_VID_NO_PAD
    if [ ! -e /proc/net/vlan/vlan$2 ] ; then
        vconfig add $1 $2
    fi
    vconfig set_ingress_map vlan$2 0 0
    vconfig set_ingress_map vlan$2 1 1
    vconfig set_ingress_map vlan$2 2 2
    vconfig set_ingress_map vlan$2 3 3
    vconfig set_ingress_map vlan$2 4 4
    vconfig set_ingress_map vlan$2 5 5
    vconfig set_ingress_map vlan$2 6 6
    vconfig set_ingress_map vlan$2 7 7
    ip link set vlan$2 mtu 1500
    ip link set vlan$2 up
    if [ "$2" != "1" ] ; then
        REPLACEMENT=`syscfg get wan_mac_addr`
    else
        REPLACEMENT=`syscfg get lan_mac_addr`
    fi
    ip link set vlan$2 down
    ip link set vlan$2 addr $REPLACEMENT
    `sysevent set vlan$2_mac $REPLACEMENT`
    if [ "`syscfg get modem::enabled`" = "1" ] ; then	
        if [ "`syscfg get switch::router_3::port_numbers`" = "4" ] ; then
            et -i eth0 robowr 0x34 0x18 0x0003 2
        elif [ "`syscfg get switch::router_3::port_numbers`" = "0" ] ; then
            et -i eth0 robowr 0x34 0x10 0x0003 2
        fi
    fi
}
unconfig_vlan () {
   ip link set vlan$1 down
   vconfig rem vlan$1
}
enslave_a_interface() {
   ip link set $1 up
   ip link set $1 allmulticast on
   brctl addif $2 $1
}
start_broadcom_emf() {
    if [ "`syscfg get hardware_vendor_name`" != "Broadcom" -o "1" = "`sysevent get emf_started`" ] ; then
        return
    fi 
    sleep 3
    echo "configuring interfaces for IGMP filtering" >> /dev/console
    emf add bridge br0
    igs add bridge br0
    emf add iface br0 vlan1
    emf add iface br0 eth1
    emf add iface br0 eth2
    MODEL_NAME=`syscfg get device::model_base`
    if [ -z "$MODEL_NAME" ] ; then
       MODEL_NAME=`syscfg get device::modelNumber`
       MODEL_NAME=${MODEL_NAME%-*}	
    fi
    if [ "EA9200" = "$MODEL_NAME" ] ; then
    	emf add iface br0 eth3
    fi
    sysevent set emf_started 1
}
