#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/interface_functions.sh
if [ -f /etc/init.d/brcm_ethernet_helper.sh ]; then
    source /etc/init.d/brcm_ethernet_helper.sh
fi
SERVICE_NAME="interface"
PID="($$)"
is_nfsboot() {
   grep -q "root=/dev/nfs" /proc/cmdline
   return $?
}
physical_ifname_to_syscfg_namespace()
{
   if [ -n "$1" ] ; then
      MAX_COUNT=`syscfg get max_interface_count`
      if [ -z "$MAX_COUNT" ] ; then
         MAX_COUNT=0
      fi
      CURCOUNT=1
      while [ $MAX_COUNT -ge $CURCOUNT ] ; do
         NS="interface_"${CURCOUNT}
         CURCOUNT=`expr $CURCOUNT + 1`
        eval `utctx_cmd get ${NS}::ifname`
        eval `echo SYSCFG_ifname='$'SYSCFG_${NS}_ifname`
        if [ "$SYSCFG_ifname" = "$1" ] ; then
           echo $NS
           return 0
        fi
      done
   fi
}
interface_info_by_namespace()
{
   if [ -z "$1" ] ; then
      return 255
   fi
   NS=$1
   eval ARGS=\"\
        $NS::ifname \
        $NS::hardware_vendor_name \
        $NS::type \
        $NS::virtual_ifnum \
        $NS::dependency\"
   eval `utctx_cmd get $ARGS`
   eval `echo SYSCFG_ifname='$'SYSCFG_${NS}_ifname`
   if [ -z "$SYSCFG_ifname" ] ; then
      return 255
   fi
   eval `echo SYSCFG_hardware_vendor_name='$'SYSCFG_${NS}_hardware_vendor_name`
   eval `echo SYSCFG_type='$'SYSCFG_${NS}_type`
   eval `echo SYSCFG_virtual_ifnum='$'SYSCFG_${NS}_virtual_ifnum`
   eval `echo SYSCFG_dependency='$'SYSCFG_${NS}_dependency`
   return 0
}
init_interface_namespace()
{
   if [ -z "$1" ] ; then
      return 255
   else
      interface_info_by_namespace $1
      RET=$?
      if [ 0 != "$RET" ] ; then
         return $RET
      fi
   fi
   NAMESPACE=$1
   return 0
}
service_start ()
{
   MAX_TRIES=20
   TRIES=1
   while [ "$MAX_TRIES" -gt "$TRIES" ] ; do
      STATUS=`sysevent get ${SYSCFG_ifname}-status`
      if [ "starting" = "$STATUS" -o "stopping" = "$STATUS" ] ; then
         ulog interface status "$PID service_start waiting for status to change from $STATUS. Try ${TRIES} of ${MAX_TRIES}"
         sleep 1
         TRIES=`expr $TRIES + 1`
      else
         TRIES=$MAX_TRIES
      fi
   done
   STATUS=`sysevent get ${SYSCFG_ifname}-status`
   if [ "started" != "$STATUS" ] ; then
      if [ -n "$SYSCFG_dependency" -a "none" != "$SYSCFG_dependency" ] ; then
         ulog interface status "$PID service_start starting dependency $SYSCFG_dependency"
         `/etc/init.d/service_interface.sh ${SERVICE_NAME}-start ${SYSCFG_dependency}`
         TRIES=1
         while [ "$MAX_TRIES" -gt "$TRIES" ] ; do
            STATUS=`sysevent get ${SYSCFG_dependency}-status`
            if [ "started" = "$STATUS" ] ; then
               TRIES=$MAX_TRIES
            else
               sleep 1
               TRIES=`expr $TRIES + 1`
            fi
         done
         
         STATUS=`sysevent get ${SYSCFG_dependency}-status`
         if [ "started" != "$STATUS" ] ; then
            ulog interface status "$PID Unable to start dependency $SYSCFG_dependency. Cannot start $SYSCFG_ifname."
            return 255
         fi
      fi
      case "$SYSCFG_type" in
         switch)
            REPLACEMENT=`syscfg get wan_mac_addr`
            ip link set ${SYSCFG_ifname} addr $REPLACEMENT
            ip link set $SYSCFG_ifname up
         ;;
         vlan)
            is_nfsboot
            nfs=$?
            if [ $nfs = "0" ] ; then
               return;
            fi
            if [ -z "$SYSCFG_virtual_ifnum" ] ; then
               ulog interface status "$PID vlan declared but no virtual_ifnum. Ignoring" 
               return 0
            fi
               
            case "$SYSCFG_hardware_vendor_name" in 
               Broadcom)
                     config_vlan $SYSCFG_dependency $SYSCFG_virtual_ifnum
               ;;
               Marvell)
                    vconfig add $SYSCFG_dependency $SYSCFG_virtual_ifnum
                    product_name=`cat /etc/product`
                    if [ "$product_name" != "viper" -a "$product_name" != "audi" ] ; then
                        REPLACEMENT=`syscfg get wan_mac_addr`
                        ip link set ${SYSCFG_ifname} down
                        ip link set ${SYSCFG_ifname} addr $REPLACEMENT
                        sysevent set ${SYSCFG_ifname}_mac $REPLACEMENT
                    else
                        REPLACEMENT=`sysevent get ${SYSCFG_ifname}_mac`
                        if [ -n "$REPLACEMENT" ] ; then
                            ip link set ${SYSCFG_ifname} addr $REPLACEMENT
                        else
                            ip link set ${SYSCFG_ifname} up
                            INCR_AMOUNT=`expr $SYSCFG_virtual_ifnum`
                            OUR_MAC=`get_mac ${SYSCFG_dependency}`
                            REPLACEMENT=`incr_mac $OUR_MAC $INCR_AMOUNT`
                            ip link set ${SYSCFG_ifname} down
                            ip link set ${SYSCFG_ifname}  addr $REPLACEMENT
                            sysevent set ${SYSCFG_ifname}_mac $REPLACEMENT
                        fi
                    fi
                    ;;
            esac
            ip link set $SYSCFG_ifname up
         ;;
      esac
      sysevent set ${SYSCFG_ifname}-status started
   fi
}
service_stop ()
{
   sysevent set ${SYSCFG_ifname}-status stopped
   return 0
}
iterator ()
{
   if [ -n "$2" ] ; then
      init_interface_namespace $2
      if [ 0 = "$?" ] ; then
         case "$1" in
            start)
               service_start
               ;;
            stop)
               service_stop
               ;;
         esac
      fi
   else
      MAX_COUNT=`syscfg get max_interface_count`
      if [ -z "$MAX_COUNT" ] ; then
         MAX_COUNT=0
      fi
      CURCOUNT=1
      while [ $MAX_COUNT -ge $CURCOUNT ] ; do
         i="interface_"${CURCOUNT}
         CURCOUNT=`expr $CURCOUNT + 1`
         init_interface_namespace $i
         if [ 0 = "$?" ] ; then
            case "$1" in
               start)
                  service_start
                  ;;
               stop)
                  service_stop
                  ;;
            esac
         fi
      done
   fi
}
PARAM=
if [ -n "$2" -a "NULL" != "$2" ] ; then
   PARAM=`physical_ifname_to_syscfg_namespace $2`
fi
case "$1" in
   ${SERVICE_NAME}-start)
      ulog interface status "$PID Received request to $1 $2"
      iterator start $PARAM
      ;;
   ${SERVICE_NAME}-stop)
      ulog interface status "$PID Received request to $1 $2"
      iterator stop $PARAM
      ;;
   ${SERVICE_NAME}-restart)
      ulog interface status "$PID Received request to $1 $2"
      ulog interface status "$PID Calling stop"
      iterator stop $PARAM
      ulog interface status "$PID Calling start"
      iterator start $PARAM
      ;;
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
