#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="phylink"
SELF_NAME="`basename $0`"
IFPLUGD_DAEMON=ifplugd
PHYLINK_SCRIPT=$0
do_start_ifplugd() 
{
    ulog ${SERVICE_NAME} status "starting ifplugd" 
    $IFPLUGD_DAEMON -i $SYSCFG_wan_physical_ifname -a -I -q -u0 -d3 -r $PHYLINK_SCRIPT
}
do_stop_ifplugd()
{
    ulog ${SERVICE_NAME} status "stopping ifplugd" 
    IFPLUGD_PIDFILE=/var/run/ifplugd.${SYSCFG_wan_physical_ifname}.pid
    if [ -f $IFPLUGD_PIDFILE ]; then
        killall ifplugd > /dev/null 2>&1
    fi
}
do_start_linkmgr ()
{
    ulog ${SERVICE_NAME} status "starting linkmgr" 
    pidof linkmgr > /dev/null
    if [ $? != "0" ] ; then
        /usr/sbin/linkmgr
    fi
}
do_stop_linkmgr ()
{
    ulog ${SERVICE_NAME} status "stopping  linkmgr" 
    killall linkmgr 2> /dev/null
}
service_start () 
{
   ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
   if [ "Broadcom" = $SYSCFG_hardware_vendor_name -o \
        "Marvell" = $SYSCFG_hardware_vendor_name -o \
        "MediaTek" = $SYSCFG_hardware_vendor_name -o \
        "QCA" = $SYSCFG_hardware_vendor_name ] ; then
       do_start_linkmgr
   else 
       do_start_ifplugd
   fi
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
}
service_stop () 
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   if [ "Broadcom" = $SYSCFG_hardware_vendor_name -o \
        "Marvell" = $SYSCFG_hardware_vendor_name -o \
        "MediaTek" = $SYSCFG_hardware_vendor_name -o \
        "QCA" = $SYSCFG_hardware_vendor_name ] ; then
       do_stop_linkmgr
   else 
       do_stop_ifplugd
   fi
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_init()
{
    SYSCFG_FAILED='false'
    FOO=`utctx_cmd get wan_physical_ifname hardware_vendor_name`
    eval $FOO
    if [ $SYSCFG_FAILED = 'true' ] ; then
        ulog phylink status "$PID utctx failed to get some configuration data"
        exit
    fi
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
      service_stop
      service_start
      ;;
  ${SYSCFG_wan_physical_ifname})
      ulog ${SERVICE_NAME} status "processing ifplugd callback params - $1 $2 -"
      if [ $2 = "up" ] || [ $2 = "down" ] ; then
          sysevent set phylink_wan_state $2
      else
          ulog ${SERVICE_NAME} status "invalid callback from ifplugd"
      fi
      ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|<wan-ifname> <up|down>]" >&2
      exit 3
      ;;
esac
