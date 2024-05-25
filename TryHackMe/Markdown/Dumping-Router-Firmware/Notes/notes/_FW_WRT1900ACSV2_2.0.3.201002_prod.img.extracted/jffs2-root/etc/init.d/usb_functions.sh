#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/hotplug_functions.sh
MODULE_PATH=/lib/modules/`uname -r`/
PROD_NAME=`cat /etc/product`
USB_DEVICES_DIR="/sys/bus/usb/devices"
USB_STORAGE_DIR="/sys/bus/usb/drivers/usb-storage"
USB_SILEX_DIR="/sys/bus/usb/drivers/sxuptp_driver"
STORAGE_DEVICE_SCRIPT="/etc/init.d/service_usb/mountscript.sh"
USB1="usb1"         #EHCI
USB2="usb2"         #XHCI         
USB3="usb3"         #OHCI
get_syscfg_model_base()
{
    if [ -z "$SYSCFG_model_base" ]; then
        SYSCFG_model_base=`syscfg get device::model_base`
    fi
}
get_syscfg_hw_revision()
{
    if [ -z "$SYSCFG_hw_revision" ]; then
        SYSCFG_hw_revision=`syscfg get device::hw_revision`
    fi
}
get_syscfg_hardware_vendor_name()
{
    if [ -z "$SYSCFG_hardware_vendor_name" ]; then
        SYSCFG_hardware_vendor_name=`syscfg get hardware_vendor_name`
    fi
}
get_syscfg_ssd_trim_enabled()
{
    if [ -z "$SYSCFG_ssd_trim_enabled" ]; then
        SYSCFG_ssd_trim_enabled=$(syscfg get ssd_trim_enabled)
    fi
}
get_syscfg_UsbPortCount()
{
    if [ -z "$SYSCFG_UsbPortCount" ]; then
        SYSCFG_UsbPortCount=`syscfg get UsbPortCount`
    fi
}
utctx_batch_get()
{
    SYSCFG_FAILED='false'
    eval `utctx_cmd get $1`
    if [ $SYSCFG_FAILED = 'true' ] ; then
        echo "Call failed"
        return 1
    fi
}
get_usb_config_by_port_num()
{
    if [ -z "$1" ] ; then
      USB_friendly_name=
      USB_current_mode=
      return
    fi
    get_syscfg_UsbPortCount
    if [ "$1" -le "$SYSCFG_UsbPortCount" ] ; then
      USB="UsbPort_$1"
      eval `utctx_cmd get "$USB"`
      eval NS='$'SYSCFG_$USB
              ARGS="\
              $NS::friendly_name \
              $NS::current_mode" 
      utctx_batch_get "$ARGS"
      eval `echo USB_friendly_name='$'SYSCFG_${NS}_friendly_name`
      eval `echo USB_current_mode='$'SYSCFG_${NS}_current_mode`
      ulog usb manager "USB_current_mode = $USB_current_mode"
    else
      USB_friendly_name="System_${1}"
      USB_current_mode=unused
    fi
}
provisioned_mode_to_desired_port_mode()
{
    if [ -z "$1" ] ; then
      USB_desired_mode=
      return
    fi
    echo $* | grep -q virtualUSB;
    if [ "0" = "$?" ] ; then
      echo $* | grep -q storage
      if [ "0" = "$?" ] ; then
         USB_desired_mode="detect"
      else
         USB_desired_mode="virtualUSB"
      fi
    else
      echo $* | grep -q storage
      if [ "0" = "$?" ] ; then
         USB_desired_mode="storage"
      else 
         USB_desired_mode="$1"
      fi
    fi
}
set_usb_port_led_status()
{  
    if [ "$2" = "on" ] ; then
    	sysevent set usb_led_status on
    	sysevent set usb_led_index ${1}
    	sysevent set usbled_on
    else
    	sysevent set usb_led_status off
    	sysevent set usb_led_index ${1}
    fi
}
get_usb_port_info()
{
    if [ -n "$1" ] ; then
      get_usb_config_by_port_num $1
      SYSEVENT_usb_port_type=`sysevent get usb_port_${1}_type`
      SYSEVENT_usb_port_state=`sysevent get usb_port_${1}_state`
      SYSEVENT_usb_port_device=`sysevent get usb_port_${1}_device`
    else
      USB_friendly_name=
      USB_current_mode=
      SYSEVENT_usb_port_type=
      SYSEVENT_usb_port_state=
      SYSEVENT_usb_port_device=
    fi
}
remove_usb_drivers()
{
    get_syscfg_UsbPortCount
    USB_removed_port=`sysevent get usb_port_${1}_type`
    ulog usb manager "$PID remove_usb_drivers: port_count=$SYSCFG_UsbPortCount port type=$USB_removed_port"
    if [ "2" = "$SYSCFG_UsbPortCount" ] ; then
        if [ "1" = "$1" ] ; then
            USB_unremoved_port=`sysevent get usb_port_2_type`
        else
            USB_unremoved_port=`sysevent get usb_port_1_type`
        fi
    fi
    case $2 in
    6)
        ulog usb manager "$PID usb camera being removed from usb_port ${1}"
    ;;
    7)
        ulog usb manager "$PID usb printer being removed from usb_port ${1}"
        [ "1" = "$SYSCFG_UsbPortCount" ] && rm_virtualusb_drivers
        [ "2" = "$SYSCFG_UsbPortCount" ] && [ "printer" != "$USB_unremoved_port" ] && rm_virtualusb_drivers 
    ;;
    8)
        ulog usb manager "$PID usb storage being removed from usb_port ${1}"
        [ "1" = "$SYSCFG_UsbPortCount" ] && rm_storage_drivers
        [ "2" = "$SYSCFG_UsbPortCount" ] && [ "storage" != "$USB_unremoved_port" ] && rm_storage_drivers
    ;;
    9)
        ulog usb manager "$PID usb hub being removed from usb_port ${1}"
    ;;
    *)
        ulog usb manager "$PID Unsupported usb device of type (${2}) is removed"
    ;;
    esac
    sysevent set usb_port_${1}_type none
    sysevent set usb_port_${1}_state down
}
add_storage_drivers()
{
    MODULE_PATH=/lib/modules/`uname -r`/
    if [ "$PROD_NAME" = "impala" ]; then
        insmod ${MODULE_PATH}/usb-storage.ko
    else
        insmod -q ${MODULE_PATH}/usb-storage.ko
    fi
    if [ -f "${MODULE_PATH}/jnl.ko" ]; then
    	insmod -q ${MODULE_PATH}/jnl.ko
    fi
    if [ "$PROD_NAME" = "impala" -o "$PROD_NAME" = "wraith" -o "$PROD_NAME" = "macan" -o "$PROD_NAME" = "rango" ]; then
    	insmod ${MODULE_PATH}/nls_utf8.ko
    	insmod ${MODULE_PATH}/tuxera-procfs.ko
    	insmod ${MODULE_PATH}/thfsplus.ko
    	insmod ${MODULE_PATH}/tntfs.ko
    else
    	insmod -q ${MODULE_PATH}/ufsd.ko
    fi    
   
    ulog usb manager "add_storage_drivers()"
}
rm_storage_drivers()
{
    rmmod -f usb_storage 2> /dev/null
    lsmodOutput=`lsmod | grep "jnl"`
    if [ "$lsmodOutput" != "" ]; then
    	module=`echo "$lsmodOutput" | awk -F" " '{print $1}'`
    	ulog usb manager "mode: $module"
    	if [ "$module" = "jnl" ]; then
     	    rmmod -f jnl 2> /dev/null
    	fi
    fi
    if [ "$PROD_NAME" = "impala" -o "$PROD_NAME" = "wraith" -o "$PROD_NAME" = "macan" -o "$PROD_NAME" = "rango" ]; then
    	rmmod -f nls_utf8 2> /dev/null
    	rmmod -f thfsplus 2> /dev/null
    	rmmod -f tntfs 2> /dev/null
    	rmmod -f tuxera_procfs 2> /dev/null
    else
    	rmmod -f ufsd 2> /dev/null
    fi
   
    ulog usb manager "remove_storage_drivers()"
}
unmount_storage_drive()
{
    
    MOUNT_DIR=`sysevent get usb_device_mount_pt_${1}`
    
    if [ ! -z "$MOUNT_DIR" ] && [ -e ${MOUNT_DIR} ] ; then
      drv="`echo $MOUNT_DIR | sed "s/\/tmp\///g"`"
      ulog usb manager "unmount and remove existed storage $drv on usb_port $1"
      `$STORAGE_DEVICE_SCRIPT remove $drv $1`
    fi
}
get_count_usb_host()
{
    if [ -z "$USB_HOST_CNT" ]; then
        USB_HOST_CNT=`ls "$USB_DEVICES_DIR" | grep -c "usb[1-9]"`
    fi
}
bind_silex_from_storage()
{
    [ -z "$1" ] && return
    [ ! -d "$USB_STORAGE_DIR" ] && return
    USB_ID=`echo "$1" | awk '{FS="/"}{print $NF}'`
    ulog usb autodetect "$PID bind_silex_from_storage: USB_ID=$USB_ID"
    ls "$USB_STORAGE_DIR" | grep "$USB_ID"
    [ "0" != "$?" ] && return
    echo -n "$USB_ID" > /sys/bus/usb/drivers/usb-storage/unbind
    echo -n "$USB_ID" > /sys/bus/usb/drivers/sxuptp_driver/bind
    ulog usb autodetect "$PID bind_silex_from_storage: DONE"
}
bind_storage_from_silex()
{
    [ -z "$1" ] && return
    [ ! -d "$USB_SILEX_DIR" ] && return
    USB_ID=`echo "$1" | awk '{FS="/"}{print $NF}'`
    ulog usb autodetect "$PID bind_storage_from_silex: USB_ID=$USB_ID"
    ls "$USB_SILEX_DIR" | grep "$USB_ID"
    [ "0" != "$?" ] && return
    echo -n "$USB_ID" > /sys/bus/usb/drivers/sxuptp_driver/unbind
    echo -n "$USB_ID" > /sys/bus/usb/drivers/usb-storage/bind
    ulog usb autodetect "$PID bind_storage_from_silex: DONE"
}
