#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="wl_mac_filter"
SELF_NAME="`basename $0`"
IF_NAME_0=`syscfg get wl0_user_vap`
IF_NAME_1=`syscfg get wl1_user_vap`
IF_NAME_2=`syscfg get wl0_guest_vap`
IF_LIST="$IF_NAME_0 $IF_NAME_1 $IF_NAME_2"
CURRENT_SETTING=`syscfg get wl_access_restriction`
MAC_ENTRIES=`syscfg get wl_mac_filter`
service_start() {
	ulog ${SERVICE_NAME} status "mac filter service start"
	WIFI_SERVICE_STATUS=`sysevent get wifi_user-status`
	if [ "started" != "$WIFI_SERVICE_STATUS" ]; then
		return 0
	fi
	if [ "$CURRENT_SETTING" = "allow" ] || [ "$CURRENT_SETTING" = "deny" ]; then
		for if_name in $IF_LIST; do
			if [ "$CURRENT_SETTING" = "allow" ]; then
				iwpriv $if_name filter 1
			else
				iwpriv $if_name filter 2
			fi
			for mac in $MAC_ENTRIES; do
				macstring=`echo $mac | awk -F":" '{print $1$2$3$4$5$6}'`
			
				iwpriv $if_name filtermac "add $macstring"
			done
			iwconfig $if_name commit
		done
		sysevent set ${SERVICE_NAME}-errinfo "Succesful add MAC filter entries"
		sysevent set ${SERVICE_NAME}-status "started"
	else
		for if_name in $IF_LIST; do
			iwpriv $if_name filter 0
			iwpriv $if_name filtermac "deleteall"
			iwconfig $if_name commit
		done
		sysevent set ${SERVICE_NAME}-errinfo "MAC filter is disabled"
		sysevent set ${SERVICE_NAME}-status "started"
	fi
	return 0
}
service_stop () {
	ulog ${SERVICE_NAME} status "mac filter service stop" 
	for if_name in $IF_LIST; do
		iwpriv $if_name filter 0
		iwpriv $if_name filtermac "deleteall"
		iwconfig $if_name commit
	done
	sysevent set ${SERVICE_NAME}-errinfo
	sysevent set ${SERVICE_NAME}-status "stopped"
}
service_init() {
	ulog ${SERVICE_NAME} status "mac filter service init"
	SYSCFG_FAILED='false'
	FOO=`utctx_cmd get wl_mac_filter wl_access_restriction wl_client_list`
	eval $FOO
	if [ $SYSCFG_FAILED = 'true' ] ; then
		ulog ${SERVICE_NAME} status "$PID utctx failed to get some configuration data required by service_wl_mac_filter"
		sysevent set ${SERVICE_NAME}-status error
		sysevent set ${SERVICE_NAME}-errinfo "Unable to get crucial information from syscfg"
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
  mac_filter_changed)
      service_stop
      service_start
      ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]|mac_filter_changed" >&2
      exit 3
      ;;
esac
