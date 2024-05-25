#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_qos/qos_provision.sh
if [ -f /etc/init.d/brcm_ethernet_helper.sh ]; then
    source /etc/init.d/brcm_ethernet_helper.sh
fi
source /etc/init.d/service_wan/wan_helper_functions
SERVICE_NAME="qos"
WAN_BANDWIDTH_MONITOR_BIN="wan_bandwidth_monitor"
service_init ()
{
    eval `utctx_cmd get qos_enable wan_download_speed wan_upload_speed wan_upload_speed_unit lan_ethernet_virtual_ifnums lan_ethernet_physical_ifnames lan_wl_physical_ifnames wl0_user_vap wl1_user_vap wl0_guest_vap bridge_mode QoSEthernetPort_1 QoSEthernetPort_2 QoSEthernetPort_3 QoSEthernetPort_4 hardware_vendor_name QoSDefinedPolicyCount QoSUserDefinedPolicyCount QoSPolicyCount QoSMacAddrCount QoSVoiceDeviceCount`
    WAN_STATUS=`sysevent get wan-status`
    LAN_STATUS=`sysevent get lan-status`
   ORIGINAL_SYSCFG_qos_enable=$SYSCFG_qos_enable
   if [ -n "$SYSCFG_qos_enable" -a "0" != "$SYSCFG_qos_enable" ] ; then
      IMPLICIT_DISABLE=1
      if [ -n "$SYSCFG_QoSDefinedPolicyCount" -a "0" != "$SYSCFG_QoSDefinedPolicyCount" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ -n "$SYSCFG_QoSUserDefinedPolicyCount" -a "0" != "$SYSCFG_QoSUserDefinedPolicyCount" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ -n "$SYSCFG_QoSPolicyCount" -a "0" != "$SYSCFG_QoSPolicyCount" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ -n "$SYSCFG_QoSMacAddrCount" -a "0" != "$SYSCFG_QoSMacAddrCount" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ -n "$SYSCFG_QoSVoiceDeviceCount" -a "0" != "$SYSCFG_QoSVoiceDeviceCount" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ -n "$QoSEthernetPort_1" -o  -n "$QoSEthernetPort_2" -o  -n "$QoSEthernetPort_3" -o  -n "$QoSEthernetPort_4" ] ; then
        IMPLICIT_DISABLE=0 
      fi
      if [ "1" = "$IMPLICIT_DISABLE" ] ; then
      ulog ${SERVICE_NAME} status "No qos rules found. QoS has been implicitly disabled" 
      SYSCFG_qos_enable=0
      fi
   fi
   if [ -n "$SYSCFG_wan_upload_speed" ] ; then
      ZERO=`echo $SYSCFG_wan_upload_speed | cut -c1`
      if [ "0" = "$ZERO" ] ; then
         ZERO=`echo $SYSCFG_wan_upload_speed | cut -c2`
         if [ -n "$ZERO" ] ; then
            ZEROA=`echo $SYSCFG_wan_download_speed | cut -c3`
            if [ -z "$ZEROA" -o "0" = "$ZEROA" ] ; then
               ZEROB=`echo $SYSCFG_wan_download_speed | cut -c4`                   
               if [ -z "$ZEROB" -o "0" = "$ZEROB" ] ; then
                  ZEROC=`echo $SYSCFG_wan_download_speed | cut -c5`
                  if [ -z "$ZEROC" -o "0" = "$ZEROC" ] ; then    
                     ulog qos status "Implicitly changing wan_upload_speed from $SYSCFG_wan_upload_speed to 0"
                     SYSCFG_wan_upload_speed=0
                  fi
               fi
            fi
         fi
      fi
   fi
   if [ -n "$SYSCFG_wan_download_speed" ] ; then
      ZERO=`echo $SYSCFG_wan_download_speed | cut -c1`
      if [ "0" = "$ZERO" ] ; then
         ZERO=`echo $SYSCFG_wan_download_speed | cut -c2`
         if [ -n "$ZERO" ] ; then
            ZEROA=`echo $SYSCFG_wan_download_speed | cut -c3`
            if [ -z "$ZEROA" -o "0" = "$ZEROA" ] ; then
               ZEROB=`echo $SYSCFG_wan_download_speed | cut -c4`                   
               if [ -z "$ZEROB" -o "0" = "$ZEROB" ] ; then
                  ZEROC=`echo $SYSCFG_wan_download_speed | cut -c5`
                  if [ -z "$ZEROC" -o "0" = "$ZEROC" ] ; then   
                     ulog qos status "Implicitly changing wan_download_speed from $SYSCFG_wan_download_speed to 0"
                     SYSCFG_wan_download_speed=0
                  fi
               fi
            fi
         fi
      fi
   fi
   if [ -n "$SYSCFG_wan_download_speed" ] ; then
      TEST=`dc $SYSCFG_wan_download_speed .5 - p`
      RES=`echo $TEST | awk '{if (0 > $1) print 1; else print 0}'` 
      if [ "$RES" = "1" ] ; then
         ulog qos status "wan_download_speed too low ( $SYSCFG_wan_download_speed ). Resetting it"
         syscfg set wan_download_speed 0
         SYSCFG_wan_download_speed=0
      fi
   fi
   if [ -n "$SYSCFG_wan_upload_speed" ] ; then
      if [ "1" =  "$SYSCFG_wan_upload_speed_unit" ] ; then
         TEST=`dc $SYSCFG_wan_upload_speed 500 - p`
         RES=`echo $TEST | awk '{if (0 > $1) print 1; else print 0}'` 
         if [ "$RES" = "1" ] ; then
            ulog qos status "wan_upload_speed too low ( $SYSCFG_wan_upload_speed kbps). Resetting it"
            syscfg set wan_upload_speed 0
            SYSCFG_wan_upload_speed=0
         fi
      else
         TEST=`dc $SYSCFG_wan_upload_speed .500 - p`
         RES=`echo $TEST | awk '{if (0 > $1) print 1; else print 0}'` 
         if [ "$RES" = "1" ] ; then
            ulog qos status "wan_upload_speed too low ( $SYSCFG_wan_upload_speed mbps ). Resetting it"
            syscfg set wan_upload_speed 0
            SYSCFG_wan_upload_speed=0
         fi
      fi
   fi
   if [ "" = "$SYSCFG_lan_ethernet_virtual_ifnums" ] ; then
      LAN_IFNAMES=$SYSCFG_lan_ethernet_physical_ifnames
    else
       for loop in $SYSCFG_lan_ethernet_physical_ifnames
       do
         LAN_IFNAMES="$LAN_IFNAMES vlan$SYSCFG_lan_ethernet_virtual_ifnums"
       done
   fi
}
service_start_on_lan ()
{
   if [ "started" = "$LAN_STATUS" ] ; then
      for loop in br0
      do
         prepare_wan_shaping $loop
         ulog qos status "Enable Wan Shaping on $loop"
      done
        for loop in $LAN_IFNAMES
        do
           prepare_lan_qos_queue_disciplines $loop
           ulog qos status "Enable QoS on $loop"
        done
        for loop in ${SYSCFG_wl0_user_vap} ${SYSCFG_wl1_user_vap} ${SYSCFG_wl0_guest_vap}
        do
           prepare_wireless_lan_qos_queue_disciplines $loop
           ulog qos status "Enable QoS on $loop"
        done
      conntrack -F > /dev/null 2>&1
   fi
}
service_start_on_wan ()
{
   for i in wan_1 wan_2 wan_3
   do
      wan_info_by_namespace $i
      LOCAL_STATUS=`sysevent get ${i}-status`
      if [ "started" = "$LOCAL_STATUS" -a -n "$SYSEVENT_current_wan_ifname" ] ; then
         if [ -z "$SYSCFG_wan_download_speed" ] ; then
            SYSCFG_wan_download_speed=2097
         fi
         RES=`echo $SYSCFG_wan_download_speed 0 | awk '{if (0 >= $1) print 1; else print 0}'`
         if [ "$RES" = "1" ] ; then
            SYSCFG_wan_download_speed=2097
         fi
         if [ -z "$SYSCFG_wan_upload_speed" ]  ; then
            SYSCFG_wan_upload_speed=314
            SYSCFG_wan_upload_speed_unit=2 
         fi
         RES=`echo $SYSCFG_wan_upload_speed 0 | awk '{if (0 >= $1) print 1; else print 0}'`
         if [ "$RES" = "1" ] ; then
            SYSCFG_wan_upload_speed=314
            SYSCFG_wan_upload_speed_unit=2 
         fi
         if [ -z "$SYSCFG_wan_upload_speed_unit" ] ; then
            SYSCFG_wan_upload_speed_unit=2 
         fi
         if [ "$SYSCFG_wan_upload_speed_unit" = "1" ] ; then
            SYSCFG_wan_upload_speed=`dc $SYSCFG_wan_upload_speed 1000 \/ p`
            SYSCFG_wan_upload_speed_unit=2
         fi
         if [ "$SYSCFG_wan_upload_speed_unit" = "2" ] && [ $SYSCFG_wan_upload_speed -ge 314 ] ; then
             return
         else
             prepare_wan_qos_queue_disciplines $SYSEVENT_current_wan_ifname $SYSCFG_wan_download_speed $SYSCFG_wan_upload_speed
             ulog qos status "Enable QoS on $SYSEVENT_current_wan_ifname $SYSCFG_wan_download_speed $SYSCFG_wan_upload_speed"
         fi
      fi
   done
}
disable_ethernet_port_based_qos ()
{
   disable_port_qos_on_ethernet_switch
   for loop in 1 2 3 4
   do
      set_prio_on_ethernet_port $loop 0
   done
    set_prio_on_ethernet_port 0 0
    set_prio_on_ethernet_port 5 0
    set_prio_on_ethernet_port 8 0
}
apply_ethernet_port_based_qos ()
{
   if [ -z "$SYSCFG_QoSEthernetPort_1" ] && [ -z "$SYSCFG_QoSEthernetPort_2" ] && [ -z "$SYSCFG_QoSEthernetPort_3" ] && [ -z "$SYSCFG_QoSEthernetPort_4" ] ; then
      disable_ethernet_port_based_qos
      return 0
   fi
   IS_QOS=0
   for loop in 1 2 3 4
   do
      CURRENT_LOOP="SYSCFG_QoSEthernetPort_$loop"
      eval QOS='$'$CURRENT_LOOP
      if [ "\$HIGH" = "$QOS" ] ; then 
         set_prio_on_ethernet_port $loop 14
         IS_QOS=1
      elif [ "\$MEDIUM" = "$QOS" ] ; then                         
         set_prio_on_ethernet_port $loop 10
         IS_QOS=1
      elif [ "\$NORMAL" = "$QOS" ] ; then                         
         set_prio_on_ethernet_port $loop 6
         IS_QOS=1
      elif [ "\$LOW" = "$QOS" ] ; then                         
         set_prio_on_ethernet_port $loop 2
         IS_QOS=1
      else
         set_prio_on_ethernet_port $loop 6
      fi
   done
   set_prio_on_ethernet_port 0 14
   set_prio_on_ethernet_port 5 14
   set_prio_on_ethernet_port 8 14
   if [ "1" = "$IS_QOS" ] ; then
      enable_port_qos_on_ethernet_switch
   fi
}
service_start ()
{
   if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
      ulog ${SERVICE_NAME} status "QoS has been disabled (${SYSCFG_qos_enable})" 
      service_stop
      if [ -z "$ORIGINAL_SYSCFG_qos_enable" -o "0" != "$ORIGINAL_SYSCFG_qos_enable" ] ; then     
         if [ -z "$SYSCFG_bridge_mode" -o "0" = "$SYSCFG_bridge_mode" ] ; then 
            ulog ${SERVICE_NAME} status "QoS on WAN still applied" 
            service_start_on_wan                                                                   
         fi             
      fi
      return
   fi
   ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
   if [ -z "$SYSCFG_bridge_mode" -o "0" = "$SYSCFG_bridge_mode" ] ; then 
      service_start_on_wan                                                                                
   fi   
   service_start_on_lan
   if [ -z "$SYSCFG_hardware_vendor_name" -o "Broadcom" = "$SYSCFG_hardware_vendor_name" ] ; then  
      apply_ethernet_port_based_qos
   fi
   sysevent set firewall-restart
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
}
service_stop () 
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   for loop in $LAN_IFNAMES
   do
      remove_lan_qos $loop
   done
   for loop in ${SYSCFG_wl0_user_vap} ${SYSCFG_wl1_user_vap} ${SYSCFG_wl0_guest_vap}
   do
      remove_wireless_qos $loop
   done
 
   for loop in br0
   do
      remove_wan_shaping $loop
   done
   for i in wan_1 wan_2 wan_3
   do
      wan_info_by_namespace $i
      remove_wan_qos $SYSEVENT_current_wan_ifname
   done
   if [ -z "$SYSCFG_hardware_vendor_name" -o "Broadcom" = "$SYSCFG_hardware_vendor_name" ] ; then  
      disable_ethernet_port_based_qos
   fi
   sysevent set firewall-restart
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_restart () 
{
   service_stop
   service_start
}
start_wan_bandwidth_monitor()
{
   PID=`pidof $WAN_BANDWIDTH_MONITOR_BIN`
   if [ -z "$PID" -a -f /usr/sbin/$WAN_BANDWIDTH_MONITOR_BIN ] ; then
     /usr/sbin/$WAN_BANDWIDTH_MONITOR_BIN
   fi 
}
stop_wan_bandwidth_monitor()
{
   PID=`pidof $WAN_BANDWIDTH_MONITOR_BIN`
   if [ -n "$PID" ] ; then
      killall $WAN_BANDWIDTH_MONITOR_BIN
   fi
}
service_init
case "$1" in
  ${SERVICE_NAME}-start)
     if [ "`syscfg get bridge_mode`" = "0" ] && [ "`sysevent get lan-status`" != "started" ]; then
        ulog wlan status "LAN is not started. So ignore the request"
        exit 0
     fi
     service_start
     if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
        sysevent set qos_enabled 0
        stop_wan_bandwidth_monitor
     else 
        sysevent set qos_enabled 1
        start_wan_bandwidth_monitor
     fi
     ;;
  ${SERVICE_NAME}-stop)
     service_stop
     sysevent set qos_enabled 0
     stop_wan_bandwidth_monitor
     ;;
  ${SERVICE_NAME}-restart)
     if [ "`syscfg get bridge_mode`" = "0" ] && [ "`sysevent get lan-status`" != "started" ]; then
        ulog wlan status "LAN is not started. So ignore the request"
        exit 0
     fi
     service_restart
     if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
        sysevent set qos_enabled 0
        stop_wan_bandwidth_monitor
     else 
        sysevent set qos_enabled 1
        stop_wan_bandwidth_monitor
        start_wan_bandwidth_monitor
     fi
     ;;
  recalculate_wan_shaping)
     for loop in br0
      do
         recalculate_wan_shaping $loop
         ulog qos status "Recalculating Wan Shaping on $loop"
      done
     ;;
  lan-status)
        LAN_STATUS=`sysevent get lan-status`
        if [ "$SYSCFG_bridge_mode" = "0" ] && [ "$LAN_STATUS" = "started" ] ; then
            service_start
            if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
               sysevent set qos_enabled 0
               stop_wan_bandwidth_monitor
            else 
               sysevent set qos_enabled 1
               start_wan_bandwidth_monitor
            fi
        fi
        ;;
  wan-status)
        if [ "$SYSCFG_bridge_mode" = "0" ] ; then
            service_start
            if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
               sysevent set qos_enabled 0
               stop_wan_bandwidth_monitor
            else 
               sysevent set qos_enabled 1
               start_wan_bandwidth_monitor
            fi
        fi
        ;;
  current_wan_ifname)
     SERVICE_STATUS=`sysevent get ${SERVICE_NAME}-status`
     if [ "started" = "$SERVICE_STATUS" ] ; then
        service_start_on_wan
            if [ -n "$SYSCFG_qos_enable" -a "0" = "$SYSCFG_qos_enable" ] ; then
               sysevent set qos_enabled 0
               stop_wan_bandwidth_monitor
            else 
               sysevent set qos_enabled 1
               start_wan_bandwidth_monitor
            fi
     fi   
     ;;
  wifi_guest-status)
    if [ "`sysevent get wifi_guest-status`" = "started" ] ; then
	service_start
    fi
    ;;  
  wifi-status)
    if [ "`sysevent get wifi-status`" = "started" ] ; then
	service_start
    fi
    ;;  
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
