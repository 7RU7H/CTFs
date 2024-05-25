#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

#------------------------------------------------------------------
# RAINIER-5849 is fixed by moving vconfig to immediately after the Ethernet
# driver in the wait script. This is a Broadcom only issue.
# 
# This script is called from the wait script to setup the vlan
#------------------------------------------------------------------

source /etc/init.d/interface_functions.sh
vlan_id_10_to_16 ()
{
    vid=`printf '%03x\n' $1`
}

construct_VTU_data_register_ports_0_to_3 ()
{
    var=""
    for i in 3 2 1 0 ; do
        if [ $(echo $1 | cut -f `expr $i + 1` -d ' ') = 3 ] ; then
            var=${var}3;
        elif [ $(echo $1 | cut -f `expr $i + 1` -d ' ') = 1 ] ; then
            var=${var}1;
        elif [ $(echo $1 | cut -f `expr $i + 1` -d ' ') = 2 ] ; then
            var=${var}2;
        fi
    done
}

construct_VTU_data_register_ports_4_to_6 ()
{
    if [ "`syscfg get switch::router_2::port_tagging`" = "t" ] ; then
        if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
            # Mamba. Egress port 6 un-tagged, Block port 5.
            var="0132";
        elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
            # Cobra. Egress port 5 un-tagged, Block port 6.
            var="0312";
        fi
    else
        if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
            # Mamba
            var="0131";
        elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
            # Cobra
            var="0311";
        fi
    fi
}

get_prio ()
{
    prio=`expr 8 + $1`
    prio=`printf '%x\n' $prio`
}

update_priority_override_register ()
{
    echo $1 13 2 0x0400 > $reg_path
}

# get the relevant syscfg parameters
PARAM=`utctx_cmd get bridge_mode lan_ethernet_physical_ifnames lan_mac_addr`
eval $PARAM

if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
    # Mamba
    reg_path=/sys/devices/platform/neta/switch/reg_w
elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
    # Cobra
    reg_path=/sys/devices/platform/mv_switch/reg_w
fi
if [ -e $reg_path ] ; then
    # Since we load all of VTU polices as a batch it would be best to use the VTU flush commands before loading the new set of VLAN entries.
    # Bits 14:12, 001 = Flush all entries in the VTU and STU
    echo 0 5 3 0x9000 > $reg_path
    # RANGO-320, turn on downshift feature for WAN port 4
    echo 4 16 1 0x3b60 > $reg_path
    echo 4 0 1 0x9140 > $reg_path #Need to reset PHY to validate the changed settings
    if [ "`syscfg get vlan_tagging::enabled`" = "0" -o "$SYSCFG_bridge_mode" != "0" ] ; then
        # Bits 11:10 802.1Q Mode: 00 = disabled
        if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
            # Mamba
            for i in 0 1 2 3 4 6 ; do  
                echo $i 8 2 0x2080 > $reg_path 
            done
            if [ "$SYSCFG_bridge_mode" != "0" ]; then
                # bridge mode, set port 0 1 2 3 4 5 in the same group
                echo 0 6 2 0x3e > $reg_path
                echo 1 6 2 0x3d > $reg_path
                echo 2 6 2 0x3b > $reg_path
                echo 3 6 2 0x37 > $reg_path
                echo 4 6 2 0x2f > $reg_path
                echo 5 6 2 0x1f > $reg_path
                echo 6 6 2 0x0  > $reg_path
            fi
        elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
            # Caiman/Cobra
            for i in 0 1 2 3 4 5 ; do
                echo $i 8 2 0x2080 > $reg_path
            done
            # CAIMAN-80
            for i in 0 1 2 3 ; do
                echo $i 9 1 0x0a00 > $reg_path #"port type" field set to 0
                echo $i 16 1 0x3060 > $reg_path #"energy detect" field set to 0 (off)
                echo $i 0 1 0x9140 > $reg_path #Need to reset PHY to validate the changed settings
            done
            if [ "$SYSCFG_bridge_mode" != "0" ]; then
                # bridge mode, set port 0 1 2 3 4 6 in the same group
                echo 0 6 2 0x5e > $reg_path
                echo 1 6 2 0x5d > $reg_path
                echo 2 6 2 0x5b > $reg_path
                echo 3 6 2 0x57 > $reg_path
                echo 4 6 2 0x4f > $reg_path
                echo 5 6 2 0x0  > $reg_path
                echo 6 6 2 0x1f > $reg_path
            else
                # router mode, use forwarding database #1 for eth1, database #0 for eth0
                echo 4 6 2 0x1020 > $reg_path
                echo 5 6 2 0x1010 > $reg_path
            fi
        fi
    elif [ "`syscfg get vlan_tagging::enabled`" = "1" ] ; then
        # Load STU Dummy Entry for SID 0 */
        echo 0 3 3 0x0       > $reg_path 
        echo 0 6 3 0x1000    > $reg_path
        echo 0 7 3 0x0       > $reg_path
        echo 0 8 3 0x0       > $reg_path
        echo 0 9 3 0x0       > $reg_path
        echo 0 5 3 0xD000    > $reg_path
        #init VID priority override register to 0x0000
        echo 0 9 3 0x0000    > $reg_path
        #Enable 802.1Q VLANS allows 802.1q vlans to take prcedence over port based VLANS
        echo 0 29 4 0x8000   > $reg_path
 
        #Enable 802.1Q Fallback Mode on port 4,6 */
        echo 4 8 2 0x2480    > $reg_path 
        if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
            # Mamba
            echo 6 8 2 0x2480    > $reg_path
        elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
            # Cobra
            echo 5 8 2 0x2480    > $reg_path
        fi
    #=====
        port2_based_vlan_map=0x002b
        port3_based_vlan_map=0x0027
        for lan_no in 2 3 ; do
            c=1
            count=`syscfg get lan_$lan_no::vlan_count`
            if [ "$count" = "0" ] ; then
                # Disable 802.1Q Mode
                port_number=`syscfg get switch::router_1::port_numbers | cut -f1 -d' '`
                port_name=`syscfg get switch::router_1::port_names | cut -f1 -d' '`
                if [ `expr index $port_name $port_number` = 0 ] ; then
                    # mamba
                    if [ "$lan_no" = "2" ] ; then
                        echo 1 8 2 0x2080 > $reg_path 
                    else
                        echo 0 8 2 0x2080 > $reg_path 
                    fi
                else
                    #viper, audi
                    if [ "$lan_no" = "2" ] ; then
                        echo 2 8 2 0x2080 > $reg_path 
                    else
                        echo 3 8 2 0x2080 > $reg_path 
                    fi
                fi
            fi
            while [ $c -le $count ] ; do
                vid=`syscfg get lan_$lan_no::vlan_id_$c`
                port_numbers=`syscfg get lan_$lan_no::port_numbers_$c`
                prio=`syscfg get lan_$lan_no::prio_$c`
                
                vlan_id_10_to_16 $vid
                # 0 = is a member and egress unmodified
                # 1 = is a member and egress untagged
                # 2 = is a member and egress tagged
                # 3 = is not a member
                for port_no in 0 1 2 3 ; do
                    port_tagging=$(echo $port_numbers | cut -f `expr $port_no + 1` -d ' ')
                    if [ "$port_tagging" != "3" ] ; then
                        if [ "$port_no" = "0" ] ; then
                            port2_based_vlan_map=`echo | awk -v value=$port2_based_vlan_map '{ print and(value, 0xfe) }'`
                            port3_based_vlan_map=`echo | awk -v value=$port3_based_vlan_map '{ print and(value, 0xfe) }'`
                        fi
                        if [ "$port_no" = "1" ] ; then
                            port2_based_vlan_map=`echo | awk -v value=$port2_based_vlan_map '{ print and(value, 0xfd) }'`
                            port3_based_vlan_map=`echo | awk -v value=$port3_based_vlan_map '{ print and(value, 0xfd) }'`
                        fi
                         
                        if [ "$port_tagging" = "1" ] ; then
                            # Set default port VLAN ID
                            echo $port_no 7 2 0x0$vid > $reg_path 
                        fi
                        #Enable 802.1Q Secure Mode on port_no. Discard Ingress Membership violations and discard frames whose VID is not contained in the VTU. MAMBA-532.
                        echo $port_no 8 2 0x2c80 > $reg_path 
                        update_priority_override_register $port_no
                        update_priority_override_register 4
                    fi
                done
                
                port2_based_vlan_map=0x`printf '%04x\n' $port2_based_vlan_map`
                echo 2 6 2 $port2_based_vlan_map > /sys/devices/platform/neta/switch/reg_w

                port3_based_vlan_map=0x`printf '%04x\n' $port3_based_vlan_map`
                echo 3 6 2 $port3_based_vlan_map > /sys/devices/platform/neta/switch/reg_w

                # Add VTU entry for VoIP VID
                echo 0 6 3 0x1$vid > $reg_path 
                # Block the other ports, Egress port_no Un-Tagged
                construct_VTU_data_register_ports_0_to_3 "$port_numbers"
                echo 0 7 3 0x$var > $reg_path 
                # Block ports 6,5 Egress port 4 Tagged
                echo 0 8 3 0x0332 > $reg_path 
                # Set VID Frame priority un-comment if you want to set a VID priority override
                # Otherwise VID will be translated from difserver priority.
                get_prio $prio
                echo 0 9 3 0x${prio}000 > $reg_path 
                # Load Entry
                echo 0 5 3 0xB000 > $reg_path 
                c=`expr $c + 1`
            done
        done
    #=====
        # User traffic  VID
        # Set default VID for port 6 so that tag can be added to all internet frames from CPU
        vid=`syscfg get wan_1::vlan_id`
        vlan_id_10_to_16 $vid
        if [ -e /sys/devices/platform/neta/switch/reg_w ] ; then
            # Mamba
            echo 6 7 2 0x0$vid > $reg_path
            update_priority_override_register 6
        elif [ -e /sys/devices/platform/mv_switch/reg_w ] ; then
            # Cobra
            echo 5 7 2 0x0$vid > $reg_path
            update_priority_override_register 5
        fi 
        # Need to put the WAN VID in a different filter database.
        echo 0 2 3 0x0008 > $reg_path 
        # Add VTU entry for iptv VID
        echo 0 6 3 0x1$vid > $reg_path 
        # Block ports 3,2,1,0
        echo 0 7 3 0x3333 > $reg_path 
        construct_VTU_data_register_ports_4_to_6
        # Egress port 6 un-tagged, Block port 5 Egress Port 4 Tagged
        echo 0 8 3 0x$var > $reg_path 
        # Set VID Frame priority un-comment if you want to set a VID priority override
        # Otherwise VID will be translated from difserver priority.
        prio=`syscfg get wan_1::prio`
        get_prio $prio
        echo 0 9 3 0x${prio}000 > $reg_path 
        # Load Entry
        echo 0 5 3 0xB000 > $reg_path 
    fi
fi

ip link set $SYSCFG_lan_ethernet_physical_ifnames down
ip link set $SYSCFG_lan_ethernet_physical_ifnames addr $SYSCFG_lan_mac_addr
ip link set $SYSCFG_lan_ethernet_physical_ifnames up
