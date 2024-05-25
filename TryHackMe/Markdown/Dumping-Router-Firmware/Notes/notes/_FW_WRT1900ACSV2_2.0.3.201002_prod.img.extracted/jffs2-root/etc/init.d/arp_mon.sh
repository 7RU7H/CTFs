#!/bin/sh
lock=/var/run/`basename $0`.pid
if [ -f $lock ] && kill -CONT `cat $lock`; then
	if ps | grep 'D    [s]yscfg commit'; then
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
		echo "Rebooting due to stuck syscfg commit"
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
		reboot -n -f &
	fi
	exit
fi
echo $$ > $lock
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="arp_table_monitor"
ARP_HEADER=0
ARP_TABLE=/proc/net/arp
DHCP_LEASEFILE=/etc/dnsmasq.leases
DETECTED_HOSTS=/tmp/detected_hosts
STATICIP_HOSTS=/tmp/staticIP_hosts
ARP_MONITOR_HOSTS=/tmp/arp.hosts
INVALID_MAC="00:00:00:00:00:00"
ARP_FLAG=2
ARP_ECHO_NUM=1
PORT_ID=1
DEFUALT_RATE=1000mbs
eval `utctx_cmd get lan_ifname`
while read LINE                          
do                                       
    if [ "$ARP_HEADER" == "0" ] ; then
        ARP_HEADER=1                       
    else                                        
        match=0
        match_arp=1
        match_ip=1
        IF=$(echo "$LINE"|awk '{print $6}')
        if [ "$IF" == "$SYSCFG_lan_ifname" ] ; then 
            IP=$(echo "$LINE"|awk '{print $1}') 
            MAC=$(echo "$LINE"|awk '{print $4}')
            FLAG=$(echo "$LINE"|awk '{print and($3,2)}')
            if [ "$FLAG" == "$ARP_FLAG" ] ; then
                MAC_L=$(echo "$MAC" |tr '[A-Z]' '[a-z]')		
                PORT_FLAG=`brctl showmacs $IF | grep $MAC_L | awk -F" " '{print $1}'`
                LAN_PHY_MACADDR=`ifconfig $IF | grep HWaddr | awk -F" " '{print $5}'`
                if [ -f $ARP_MONITOR_HOSTS ] ; then
                    match_arp=$(grep -i $MAC_L $ARP_MONITOR_HOSTS | wc -l)
                    match_ip=$(grep -i $IP $ARP_MONITOR_HOSTS | wc -l)		    
                else
                    touch ${ARP_MONITOR_HOSTS}
                    if [ "$PORT_FLAG" == "$PORT_ID" ] ; then			
                        RESULT=`arping -f -I $IF -c 3 $IP |grep Received |awk -F" " '{print $2}'`
                        if [ "$RESULT" == "$ARP_ECHO_NUM" ] ; then	
                            ulog $SERVICE_NAME "this is online wired device mac=$MAC_L,result=$RESULT"    	
                            echo "$MAC_L $IP ON" >> $ARP_MONITOR_HOSTS
                            sysevent set link_status_changed "connected,$LAN_PHY_MACADDR,$IF,Ethernet,$MAC"
                        fi
                    fi                    
                fi
                if [ "$match_arp" == "0" ] || [ "$match_ip" == "0" ]; then	            
                   if [ "$PORT_FLAG" == "$PORT_ID" ] ; then			
                        RESULT=`arping -f -I $IF -c 3 $IP |grep Received |awk -F" " '{print $2}'`
                        if [ "$RESULT" == "$ARP_ECHO_NUM" ] ; then	
                            ulog $SERVICE_NAME "this is online wired device mac=$MAC_L,result=$RESULT"		    	
                            echo "$MAC_L $IP ON" >> $ARP_MONITOR_HOSTS
                            sysevent set link_status_changed "connected,$LAN_PHY_MACADDR,$IF,Ethernet,$MAC"
                        fi
                    fi              
                fi
                if [ -f $STATICIP_HOSTS ] ; then
                    match=$(grep -i $MAC $STATICIP_HOSTS | wc -l)
                fi
                if [ "$match" == "0" ] ; then
                    if [ -f $DHCP_LEASEFILE ] ; then
                        match=$(grep -i $MAC $DHCP_LEASEFILE | wc -l)
                    fi
                    if [ "$match" == "0" ] ; then
                        if [ -f $DETECTED_HOSTS ] ; then
                            match=$(grep -i $MAC $DETECTED_HOSTS | wc -l)
                        fi
                        if [ "$match" == "0" ] ; then
                            ulog $SERVICE_NAME "send sysevent lan_arpdevice_detected, device mac=$MAC"
                            sysevent set lan_arpdevice_detected "$MAC $IP *"
                            echo "$MAC $IP" >> $STATICIP_HOSTS
                        fi                
                    fi
                fi
            fi
        fi
    fi
done < $ARP_TABLE
rm -f $lock
