#!/bin/sh
Hotplug_GetDevpath()
{
    DEVICE_PATH=
    local devname=${1::3}
    local devpath
    devpath=`udevadm info --query=path --name=$devname 2> /dev/null`
    if [ "$?" != 0 ]; then
        ulog usb autodetect "$PID Hotplug_GetDevpath: $devname not found"
        return
    fi
    DEVICE_PATH=$devpath
}
Hotplug_GetId()
{
    DEVICE_TYPE=
    DEVICE_PORT=
    local devname=$1
    Hotplug_GetDevpath "$devname"
    Hotplug_GetIdFromDevpath "$DEVICE_PATH"
}
Hotplug_GetIdFromDevpath()
{
    DEVICE_TYPE=
    DEVICE_PORT=
    local devpath=$1
    if echo "$devpath" | grep -q -e "soc.0/internal-regs.1/f1058000.usb/usb"; then
        DEVICE_TYPE="usb"
        DEVICE_PORT="1"
        ulog usb autodetect "$PID Hotplug_GetIdFromDevpath: $DEVICE_TYPE $DEVICE_PORT"
        return
    fi
    if echo "$devpath" | grep -q -e "/devices/soc.0/internal-regs.1/f10a8000.sata/ata"; then
        DEVICE_TYPE="esata"
        DEVICE_PORT="1"
        ulog usb autodetect "$PID Hotplug_GetIdFromDevpath: $DEVICE_TYPE $DEVICE_PORT"
        return
    fi
    if echo "$devpath" | grep -q -e "/devices/soc.0/internal-regs.1/f10f8000.usb3/usb"; then
        DEVICE_TYPE="usb"
        DEVICE_PORT="2"
        ulog usb autodetect "$PID Hotplug_GetIdFromDevpath: $DEVICE_TYPE $DEVICE_PORT"
        return
    fi
    ulog usb autodetect "$PID Hotplug_GetIdFromDevpath: unknown device - $devpath"
}
Hotplug_GetInfo()
{
    DEVICE_VENDOR=
    DEVICE_MODEL=
    DEVICE_SPEED=
    local devname=$1
    Hotplug_GetDevpath "$devname"
    Hotplug_GetInfoFromDevpath "$DEVICE_PATH"
}
Hotplug_GetInfoFromDevpath()
{
    DEVICE_VENDOR=
    DEVICE_MODEL=
    DEVICE_SPEED=
    local devpath=$1
    if echo $devpath | grep -q -e "usb"; then
        hotplug_get_usb_info $devpath
    else
        hotplug_get_esata_info $devpath
    fi
}
Hotplug_IsDeviceStorage()
{
    if [ ! -d "$USB_SILEX_DIR" ]; then
        return 0
    fi
    local devname=$1
    if [ -z "$devname" ]; then
        return 1
    fi
    Hotplug_GetDevpath "$devname"
    local usb_id=`echo $DEVICE_PATH | sed -n 's/.*\([1-9]-[1-9]:[0-9].[0-9]\).*/\1/p'`
    if [ -z "$usb_id" ]; then
        usb_id=`echo $DEVICE_PATH | sed -n 's/.*\([1-9]-[1-9].[1-9]\).*/\1/p'`
    fi
    ulog usb autodetect "$PID Hotplug_IsDeviceStorage - id: $usb_id"
    if [ -z "$usb_id" ]; then
        return 0
    fi
    ls -al "$USB_SILEX_DIR" | grep -q "$usb_id"
    if [ "$?" == "0" ]; then
        return 1
    fi
    return 0
}
hotplug_get_usb_info()
{
    local devpath=$1
    local dir=`echo $devpath | sed 's/^\/\(.*usb[0-9]*\/[^\/]*\).*/\1/'`
    if [ -f "/sys/$dir/manufacturer" ]; then
        DEVICE_VENDOR=`cat /sys/$dir/manufacturer`
    fi
    if [ -f "/sys/$dir/product" ]; then
        DEVICE_MODEL=`cat /sys/$dir/product`
    fi
    if [ -f "/sys/$dir/speed" ]; then
        DEVICE_SPEED=`cat /sys/$dir/speed`
    fi
}
hotplug_get_esata_info()
{
    local devpath=$1
    local dir=`dirname $1`
    
    while [ "$dir" != "/" ]; do
        if [ -f "/sys$dir/vendor" ]; then
            DEVICE_VENDOR=`cat /sys$dir/vendor`
            if [ -f "/sys/$dir/model" ]; then
                DEVICE_MODEL=`cat /sys/$dir/model`
            fi
            return
        fi
        
        dir=`dirname $dir`
    done
}
add_virtualusb_drivers()
{
    MODEL=`syscfg get device modelNumber`
    if [ -n "$MODEL" ] ; then 
      PRODUCT_STRING="product=\"${MODEL}\""
    fi
    MODULE_PATH=/lib/modules/`uname -r`/
    insmod -q ${MODULE_PATH}/sxuptp_wq.ko
    insmod -q ${MODULE_PATH}/sxuptp.ko
    insmod -q ${MODULE_PATH}/sxuptp_driver.ko
    /usr/sbin/jcpd -f /etc/jcpd.conf
    ulog usb manager "add_virtualusb_drivers() $PRODUCT_STRING"
}
rm_virtualusb_drivers()
{
    killall -9 jcpd
    rmmod -f sxuptp_driver 2> /dev/null
    rmmod -f sxuptp 2> /dev/null
    rmmod -f sxuptp_wq 2> /dev/null
    ulog usb manager "remove_virtualusb_drivers()"
}
