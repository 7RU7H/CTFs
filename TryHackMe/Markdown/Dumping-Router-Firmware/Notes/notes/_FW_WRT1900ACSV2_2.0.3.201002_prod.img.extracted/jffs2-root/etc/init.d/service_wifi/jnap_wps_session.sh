#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="jnap_side_effects-wps_session"
session_start()
{
    iface=$(syscfg get lan_wl_physical_ifnames | awk -F" " '{print $1}')
    pin=$(sysevent get ${SERVICE_NAME}-pin)
    if [ -n "$pin" ] && [ "NULL" != "$pin" ]; then
        ulog ${SERVICE_NAME} status "starting WPS session with PIN $pin"
        /sbin/wlancfg ${iface} wps-pin-start $pin > /dev/null 2>&1
    else
        ulog ${SERVICE_NAME} status "starting WPS PBC session"
        /sbin/wlancfg ${iface} wps-pbc-start > /dev/null 2>&1
    fi
}
session_stop()
{
    iface=$(syscfg get lan_wl_physical_ifnames | awk -F" " '{print $1}')
    ulog ${SERVICE_NAME} status "stopping WPS session"
    /sbin/wlancfg $iface wps-stop
}
case "$1" in
   ${SERVICE_NAME}-start)
      session_start $2
      ;;
   ${SERVICE_NAME}-stop)
      session_stop
      ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop ]" >&2
      exit 3
      ;;
esac
