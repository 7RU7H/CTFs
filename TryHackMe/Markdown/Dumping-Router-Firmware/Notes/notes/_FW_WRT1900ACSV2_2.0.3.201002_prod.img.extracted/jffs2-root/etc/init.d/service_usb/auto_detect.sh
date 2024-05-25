#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/usb_functions.sh
PID="($$)"
USB_LOCK_FILE=/tmp/usb_auto_detect.lock
lock()
{
   if (set -o noclobber; echo "$$" > "$USB_LOCK_FILE") 2> /dev/null; then        # Try to lock a file
      trap 'rm -f "$USB_LOCK_FILE"; exit $?' INT TERM EXIT;                      # Remove a lock file in abnormal termination.
      return 0;                                                                  # Locked
   fi
   return 1                                                                      # Failure
}
unlock()
{
    rm -f "$USB_LOCK_FILE"    # Remove a lock file
    trap - INT TERM EXIT
    return 0
}
get_lock()
{
  IS_DONE=1
  while [ $IS_DONE != 0 ]
  do
    lock
    IS_DONE=$?
    if [ 0 != $IS_DONE ]
    then
       sleep 1
    fi
  done
}
add_sysevent_usb_port_class()
{
  cur=`sysevent get usb_port_${1}_class`
  if [ -z "$cur" ] ; then
    sysevent set usb_port_${1}_class $2
  else
    sysevent set usb_port_${1}_class "$cur $2"
  fi
}
is_detect_usb_port_class()
{
  cur=`sysevent get usb_port_${1}_class`
  
  echo "$cur" | grep -q "$2"
  
  if [ "$?" = "0" ] ; then
    return 0
  fi
  return 1
}
get_lock
case $1 in
add_usb_device)
    ulog usb autodetect "$PID add_usb_device $2: INTERFACE=$INTERFACE DEVPATH=$DEVPATH"
    Hotplug_GetIdFromDevpath "$DEVPATH"
    Hotplug_GetInfoFromDevpath "$DEVPATH"
    sysevent set usb_port_${DEVICE_PORT}_speed $DEVICE_SPEED
    get_usb_port_info $DEVICE_PORT
    provisioned_mode_to_desired_port_mode $USB_current_mode
    sysevent set cur_usb_port $DEVICE_PORT
    get_syscfg_UsbPortCount
    ulog usb autodetect "$PID USB_PortCount=$SYSCFG_UsbPortCount, type=$DEVICE_TYPE, port=$DEVICE_PORT"
    if [ -n "$INTERFACE" -a -n "$DEVPATH" ] ; then
        udevadmOutput=`udevadm info --attribute-walk --path=$DEVPATH`
          
        INTF_CLASS=`echo "$udevadmOutput" | grep "bInterfaceClass" | cut -d '"' -f 2`
        BEG_POS=`echo "$udevadmOutput" | grep -n "looking" | cut -d ':' -f 1 | sed -n "2,3"p | head -n1`
        END_POS=`echo "$udevadmOutput" | grep -n "looking" | cut -d ':' -f 1 | sed -n "2,3"p | tail -n1`
        INTF_NUMBER=`echo "$udevadmOutput" | sed -n "$BEG_POS,$END_POS"p | grep "bNumInterfaces" | cut -d '"' -f 2 | tr -d ' '`
        
        ulog usb autodetect "$PID INTF_CLASS=$INTF_CLASS, INTF_NUMBER=$INTF_NUMBER"
        ulog usb autodetect "$PID BEG_POS=$BEG_POS, END_POS=$END_POS"
        if [ -n "$INTF_NUMBER" -a "$INTF_NUMBER" -gt "1" ] ; then
            
            add_sysevent_usb_port_class $DEVICE_PORT $INTF_CLASS
            
            if [ "$INTF_CLASS" = "07" ] ; then
                if [ "virtualUSB" = "$USB_desired_mode" -o "detect" = "$USB_desired_mode" ] ; then
                    lsmod | grep "sxuptp" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID virtualUSB drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing virtual USB drivers on usb port $DEVICE_PORT"
                        add_virtualusb_drivers 
                    fi
                else
                    ulog usb autodetect "$PID detected printer on port $DEVICE_PORT, but provisioned for $USB_desired_mode"
                fi  
                if is_detect_usb_port_class $DEVICE_PORT "08" ; then
                    STORAGE_DEVPATH=`sysevent get usb_port_${DEVICE_PORT}_storage`
                    bind_silex_from_storage $STORAGE_DEVPATH
                    sysevent set usb_port_${DEVICE_PORT}_class ""
                    sysevent set usb_port_${DEVICE_PORT}_storage ""
                fi
                sysevent set usb_port_${DEVICE_PORT}_type printer
                sysevent set usb_port_${DEVICE_PORT}_state up
                set_usb_port_led_status $DEVICE_PORT "on"
                
            elif [ "$INTF_CLASS" = "08" ] ; then
                if is_detect_usb_port_class $DEVICE_PORT "07" ; then
                    bind_silex_from_storage $DEVPATH
                    sysevent set usb_port_${DEVICE_PORT}_class ""
                    sysevent set usb_port_${DEVICE_PORT}_storage ""
                else
                    lsmod | grep "usb_storage" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID storage drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing storage drivers on usb port $DEVICE_PORT"
                        add_storage_drivers 
                    fi
                    sysevent set usb_port_${DEVICE_PORT}_storage "$DEVPATH"
                    sysevent set usb_port_${DEVICE_PORT}_type storage
                    sysevent set usb_port_${DEVICE_PORT}_state up   
                    set_usb_port_led_status $DEVICE_PORT "on"               
                fi
            fi
        elif [ -n "$INTF_NUMBER" -a "$INTF_NUMBER" -eq "1" ] ; then
            case $INTF_CLASS in
            06)
                ulog usb autodetect "$PID usb camera detected and ignored on usb_port=$DEVICE_PORT"
                sysevent set usb_port_${DEVICE_PORT}_type camera
                sysevent set usb_port_${DEVICE_PORT}_state up
                ;;
            07)
                ulog usb autodetect "$PID usb printer detected on usb_port=$DEVICE_PORT" 
                if [ "virtualUSB" = "$USB_desired_mode" -o "detect" = "$USB_desired_mode" ] ; then
                    lsmod | grep "sxuptp" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID virtualUSB drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing virtual USB drivers on usb port $DEVICE_PORT"
                        add_virtualusb_drivers 
                    fi
                else
                    ulog usb autodetect "$PID detected printer on port $DEVICE_PORT, but provisioned for $USB_desired_mode"
                fi  
                sysevent set usb_port_${DEVICE_PORT}_type printer
                sysevent set usb_port_${DEVICE_PORT}_state up
                set_usb_port_led_status $DEVICE_PORT "on"
                ;;
            08)
                ulog usb autodetect "$PID usb storage detected on usb_port=$DEVICE_PORT"
                if [ "storage" = "$USB_desired_mode" -o "detect" = "$USB_desired_mode" ] ; then
                    lsmod | grep "usb_storage" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID storage drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing storage drivers on usb port $DEVICE_PORT"
                        add_storage_drivers 
                    fi
                else
                ulog usb autodetect "$PID detected storage on port $DEVICE_PORT, but provisioned for $USB_desired_mode"
                fi
                bind_storage_from_silex $DEVPATH
                sysevent set usb_port_${DEVICE_PORT}_type storage
                sysevent set usb_port_${DEVICE_PORT}_state up
                set_usb_port_led_status $DEVICE_PORT "on"
                ;;
            esac            
        fi
    fi
    ;;
remove_usb_device)
    ulog usb autodetect "$PID remove_usb_device $2: INTERFACE=$INTERFACE, DEVPATH=$DEVPATH"
    Hotplug_GetIdFromDevpath "$DEVPATH"
        
    ulog usb autodetect "$PID remove_usb_device: DEVICE_PORT=$DEVICE_PORT"
    port1_type=`sysevent get usb_port_1_type`
    port2_type=`sysevent get usb_port_2_type`
    if [ "none" = "$port1_type" -a "none" = "$port2_type" ] ; then
        ulog usb autodetect "$PID remove_usb_device: remove all drivers and state is detecting"
        rm_virtualusb_drivers
        rm_storage_drivers
        sysevent set usb_port_1_state detecting
        sysevent set usb_port_2_state detecting
        unlock
        return
    fi
    USB_port_type=`sysevent get usb_port_${DEVICE_PORT}_type`
    if [ -z "$DEVICE_PORT" -o "$DEVICE_PORT" = "0" -o "$USB_port_type" = "none" ] ; then
        ulog usb autodetect "$PID remove_usb_device: No USB Port, or type none and return..."
        unlock
        return
    fi 
    bInterfaceClass=`echo $INTERFACE | cut -d'/' -f1`
    if [ "$USB_port_type" = "printer" -a "$bInterfaceClass" != "7" ]; then
        ulog usb autodetect "$PID remove_usb_device: printer, class=$bInterfaceClass"
        unlock
        return
    elif [ "$USB_port_type" = "storage" -a "$bInterfaceClass" != "8" ]; then
        ulog usb autodetect "$PID remove_usb_device: storage, class=$bInterfaceClass"
        unlock
        return
    fi
    ulog usb autodetect "$PID remove_usb_device: DEVICE_PORT=$DEVICE_PORT, INTF_CLASS=$bInterfaceClass"
    get_usb_port_info $DEVICE_PORT
    provisioned_mode_to_desired_port_mode $USB_current_mode
    remove_usb_drivers $DEVICE_PORT $bInterfaceClass
    set_usb_port_led_status $DEVICE_PORT "off"
    ulog usb autodetect "$PID remove_usb_device: USB_current_mode=$USB_current_mode, USB_desired_mode=$USB_desired_mode"
    ;;
add_usb_storage)
    ulog usb autodetect "$PID add_usb_storage $2: INTERFACE=$INTERFACE DEVPATH=$DEVPATH"
    Hotplug_GetIdFromDevpath "$DEVPATH"
    if [ -z "$DEVICE_PORT" ]; then
        Hotplug_GetId "$2"
    fi
    sysevent set usb_port_${DEVICE_PORT}_class ""
    sysevent set usb_port_${DEVICE_PORT}_storage ""
    if Hotplug_IsDeviceStorage "$2" ; then
        ulog usb autodetect "$PID add_usb_storage: $2 is $DEVICE_TYPE storage on $DEVICE_PORT"
        `$STORAGE_DEVICE_SCRIPT add $2 $DEVICE_PORT $DEVICE_TYPE`
        if [ "$DEVICE_TYPE" == "esata" ]; then
            sysevent set esata_${DEVICE_PORT}_mount
        fi
    fi
    ;;
remove_usb_storage)
    ulog usb autodetect "$PID remove_usb_storage $2: INTERFACE=$INTERFACE DEVPATH=$DEVPATH"
    Hotplug_GetIdFromDevpath "$DEVPATH"
    ulog usb autodetect "$PID remove_usb_storage: $2 on $DEVICE_PORT"
    `$STORAGE_DEVICE_SCRIPT remove $2 $DEVICE_PORT $DEVICE_TYPE`
    ;;
*)
    ulog usb autodetect "$PID Unsupported command $1"
    ;;
esac
unlock
