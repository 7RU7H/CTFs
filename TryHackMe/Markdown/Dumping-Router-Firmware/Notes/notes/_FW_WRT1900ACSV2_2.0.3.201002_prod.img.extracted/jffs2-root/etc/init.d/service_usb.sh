#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/usb_functions.sh
SERVICE_NAME="usb"
PID="($$)"
MODULE_PATH=/lib/modules/`uname -r`/
UDEVD_DIR="/etc/udev/rules.d"
UDEVD_FILE="${UDEVD_DIR}/10-local.rules"
prepare_udevd_conf()
{
   mkdir -p $UDEVD_DIR
        if [ "`cat /etc/product`" != "cobra" ] && [ "`cat /etc/product`" != "caiman" ] && [ "`cat /etc/product`" != "shelby" ]; then
(
cat <<'End-of-Text'
AUTODETECT_SCRIPT="/var/config/auto_detect.sh"
SUBSYSTEM=="usb", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_device %k"
SUBSYSTEM=="usb", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_device %k"
KERNEL=="sd[a-z][0-9]*", DRIVERS=="usb-storage", SUBSYSTEM=="block", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
KERNEL=="sd[a-z][0-9]*", SUBSYSTEM=="block", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
KERNEL=="sd[a-z]*", DRIVERS=="usb-storage", SUBSYSTEM=="block", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
KERNEL=="sd[a-z]*", SUBSYSTEM=="block", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
DEVPATH=="/devices/platform/msm_sata.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
DEVPATH=="/devices/platform/msm_sata.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
DEVPATH=="/devices/platform/sata_mv.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
DEVPATH=="/devices/platform/sata_mv.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
End-of-Text
) > $UDEVD_FILE
else
(
cat <<'End-of-Text'
AUTODETECT_SCRIPT="/var/config/auto_detect.sh"
SUBSYSTEM=="usb", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_device %k"
SUBSYSTEM=="usb", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_device %k"
KERNEL=="sd[a-z][0-9]*", DRIVERS=="usb-storage", SUBSYSTEM=="block", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
KERNEL=="sd[a-z][0-9]*", SUBSYSTEM=="block", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
KERNEL=="sd[a-z]*", DRIVERS=="usb-storage", SUBSYSTEM=="block", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
KERNEL=="sd[a-z]*", SUBSYSTEM=="block", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
DEVPATH=="/devices/soc.0/internal-regs.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="add", RUN+="/etc/init.d/service_usb/auto_detect.sh add_usb_storage %k"
DEVPATH=="/devices/soc.0/internal-regs.?/*/block/sd*", ENV{UDISKS_SYSTEM_INTERNAL}="0", ACTION=="remove", RUN+="/etc/init.d/service_usb/auto_detect.sh remove_usb_storage %k"
End-of-Text
) > $UDEVD_FILE
fi
}
start_udevd_for_usb()
{
   prepare_udevd_conf
   /sbin/udevadm control --reload-rules
}
stop_usb_port()
{
   [ -z "$1" ] && return
   sysevent set usb_port_${1}_type none
   sysevent set usb_port_${1}_state down
}
start_usb_port()
{
   [ -z "$1" ] && return
   get_usb_config_by_port_num $1
   SYSEVENT_usb_port_type=`sysevent get usb_port_${1}_type`
   SYSEVENT_usb_port_state=`sysevent get usb_port_${1}_state`
   ulog usb manager "$PID USB_current_mode = $USB_current_mode"
   provisioned_mode_to_desired_port_mode $USB_current_mode
   [ -z "$USB_desired_mode" ] && USB_desired_mode="detect"
   
   ulog usb manager "$PID USB_desired_mode = $USB_desired_mode"
   if [ "up" = "$SYSEVENT_usb_port_state" ] ; then
      if [ "used" = "$USB_desired_mode" ] ; then
         ulog usb service "$PID USB up: Configured for no special modes on usb port $1"
         if [ "storage" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode storage
            add_storage_drivers
         elif [ "printer" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode virtualUSB
            add_virtualusb_drivers
         elif [ "none" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode detect
         fi
         sysevent set usb_port_${1}_state up
         return
      fi
      case "$USB_desired_mode" in
         storage)
            ulog usb service "$PID USB up: desired mode is Storage"
            lsmod | grep "usb_storage" ; 
            if [ "1" = "$?" ] ; then
               add_storage_drivers
               ulog usb service "$PID Adding storage drivers on usb port $1"
            fi
            ;;
         virtualUSB)
            ulog usb service "$PID USB up: desired mode is VirtualUSB"
            lsmod | grep "sxuptp" ; 
            if [ "1" = "$?" ] ; then
               add_virtualusb_drivers
               ulog usb service "$PID Adding virtual usb drivers on usb port $1"
            fi
            ;;
         detect)
            ulog usb service "$PID USB up: desired mode is Detect"
            if [ "storage" = "$SYSEVENT_usb_port_type" ] ; then
               lsmod | grep "usb_storage" ;
               if [ "1" = "$?" ]; then
                  add_storage_drivers
                  ulog usb service "$PID Adding storage drivers on usb port $1"
               else
                  ulog usb service "$PID storage drivers already installed on usb port $1"
               fi
            elif [ "printer" = "$SYSEVENT_usb_port_type" ] ; then
               lsmod | grep "sxuptp" ;
               if [ "1" = "$?" ] ; then
                  add_virtualusb_drivers
                  ulog usb service "$PID Adding virtual usb drivers on usb port $1"
               else
                  ulog usb service "$PID virtualUSB drivers already installed on usb port $1"
               fi
            fi
            ;;
         *)
            ulog usb service "$PID USB up: Unhandled case statement for mode $USB_desired_mode"
            ;;
      esac
   else
      if [ "used" = "$USB_desired_mode" ] ; then
         ulog usb service "$PID USB down: Configured for no special modes on usb port $1"
         if [ "storage" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode storage
            add_storage_drivers 
            sysevent set usb_port_${1}_state up
         elif [ "printer" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode virtualUSB
            add_virtualusb_drivers
            sysevent set usb_port_${1}_state up
         elif [ "none" = "$SYSEVENT_usb_port_type" ] ; then
            syscfg set usb_${1}::current_mode detect
            sysevent set usb_port_${1}_state detecting
         fi
         return
      fi
      case "$USB_desired_mode" in
         storage)
            ulog usb service "$PID USB down: desired mode is Storage"
            add_storage_drivers
            sysevent set usb_port_${1}_state up
            ;;
         virtualUSB)
            ulog usb service "$PID USB down: desired mode is VirtualUSB"
            add_virtualusb_drivers
            sysevent set usb_port_${1}_state up
            ;;
         detect)
            ulog usb service "$PID USB down: desired mode is Detect"
            if [ "none" = "$SYSEVENT_usb_port_type" ] ; then
               ulog usb service "$PID USB down: There is no usb on usb port $1"
               sysevent set usb_port_${1}_state detecting
            elif [ "storage" = "$SYSEVENT_usb_port_type" ] ; then
               lsmod | grep "usb_storage" ;
               if [ "1" = "$?" ] ; then
                  add_storage_drivers
                  ulog usb service "$PID USB down: Adding storage drivers on usb port $1"
               else
                  ulog usb service "$PID USB down: storage drivers already installed on usb port $1"
               fi
               sysevent set usb_port_${1}_state up
            elif [ "printer" = "$SYSEVENT_usb_port_type" ] ; then
               lsmod | grep "sxuptp" ;
               if [ "1" = "$?" ] ; then
                  add_virtualusb_drivers
                  ulog usb service "$PID USB down: Adding virtual usb drivers on usb port $1"
               else
                  ulog usb service "$PID USB down: virtual usb drivers already installed on usb port $1"
               fi
               sysevent set usb_port_${1}_state up
            fi
            ;;
         *)
            ulog usb service "$PID USB down: Unhandled case statement 2 for mode $USB_desired_mode"
            ;;
      esac
   fi
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get UsbPortCount`
   eval $FOO
   if [ $SYSCFG_FAILED = 'true' ] ; then
      ulog usb status "$PID utctx failed to get some configuration data"
      exit
   fi
}
service_start ()
{
   [ "started" = "`sysevent get ${SERVICE_NAME}-status`" ] && return
   SYSCFG_UsbPortCount=`syscfg get UsbPortCount`
   [ -z "$SYSCFG_UsbPortCount" ] && return
   for i in `seq 1 $SYSCFG_UsbPortCount`
   do
      start_usb_port $i
   done
   start_udevd_for_usb
   MODULE_PATH=/lib/modules/`uname -r`/
   lsmod | grep usbcore 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/usbcore.ko ] && insmod $MODULE_PATH/usbcore.ko
   fi
  lsmod | grep xhci_hcd 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/xhci-hcd.ko ] && insmod $MODULE_PATH/xhci-hcd.ko
   fi
   lsmod | grep ehci_hcd 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/ehci-hcd.ko ] && insmod $MODULE_PATH/ehci-hcd.ko
   fi
   lsmod | grep ohci_hcd 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/ohci-hcd.ko ] && insmod $MODULE_PATH/ohci-hcd.ko
   fi
   lsmod | grep uhci_hcd 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/uhci-hcd.ko ] && insmod $MODULE_PATH/uhci-hcd.ko
   fi
   lsmod | grep usb_libusual 2>&1 > /dev/null
   if [ $? -eq 1 ]; then
       [ -f $MODULE_PATH/usb-libusual.ko ] && insmod $MODULE_PATH/usb-libusual.ko
   fi
   sleep 30 
   MODEL_NAME=`syscfg get device::model_base`
   if [ -z "$MODEL_NAME" ] ; then
       MODEL_NAME=`syscfg get device::modelNumber`
       MODEL_NAME=${MODEL_NAME%-*}	
   fi
   if [ "EA9200" = "$MODEL_NAME" ] ; then
		ulog usb service "here,enable usb port again" 
   		echo "low" > /proc/bdutil/usbhub
   fi
   
   sysevent set ${SERVICE_NAME}-status started
   ulog usb service "$PID : udevadm trigger "   
   /sbin/udevadm trigger --subsystem-match=usb --attr-match=bInterfaceClass=07 --action=add
   /sbin/udevadm trigger --subsystem-match=usb --attr-match=bInterfaceClass=08 --action=add
   /sbin/udevadm trigger --subsystem-match=block --action=add
}
service_stop ()
{
   SYSCFG_UsbPortCount=`syscfg get UsbPortCount`
   [ -z "$SYSCFG_UsbPortCount" ] && return
   for i in `seq 1 $SYSCFG_UsbPortCount`
   do
      unmount_storage_drive $i
      stop_usb_port $i
   done
   
   rm_storage_drivers
   rm_virtualusb_drivers
   sysevent set ${SERVICE_NAME}-status stopped
}
service_restart()
{
   service_stop
   sleep 1
   service_start
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
      service_restart
      ;;
   lan-started)
      service_start
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart] | lan-started" > /dev/console
      exit 3
      ;;
esac
