#!/bin/sh
source /etc/init.d/service_wifi/wifi_utils.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/syscfg_api.sh
wifi_simpletap_start ()
{
	return 0
}
wifi_simpletap_stop ()
{
	return 0
}
wifi_simpletap_restart()
{
	wifi_simpletap_stop
	wifi_simpletap_start
	return 0
}
unsecure_page()
{
	return 0
}
set_driver_beamforming() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	BF=`syscfg_get "$SYSCFG_INDEX"_txbf_enabled`
	if [ "1" = "$BF" ]; then
		iwpriv $PHY_IF setcmd "SetBF 6"
	else
		iwpriv $PHY_IF setcmd "SetBF 5"
	fi
	return 0
}
set_driver_txantenna() 
{
	PHY_IF=$1
	SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
	ENABLED=`syscfg_get "$SYSCFG_INDEX"_txbf_3x3_only`
	if [ ! -z "$ENABLED" ] && [ "1" = "$ENABLED" ]; then
		echo "${SERVICE_NAME}, set transmit antenna on $PHY_IF to 3x3"
		iwpriv $PHY_IF txantenna 7
	else
		SETTING=`iwpriv $PHY_IF gettxantenna`
		if [ -z "$SETTING" ] || [ "0" != "$SETTING" ]; then
			iwpriv $PHY_IF txantenna 0
		fi
	fi
	return 0
}
load_power_table()
{
	for PHY_IF in $PHYSICAL_IF_LIST; do
		SYSCFG_INDEX=`syscfg_get "$PHY_IF"_syscfg_index`
		PWRFILE=`syscfg_get ${SYSCFG_INDEX}_power_table`
		if [ -n "$PWRFILE" ] && [ -s "$PWRFILE" ] ; then
			ulog wlan status "${SERVICE_NAME}, Loading $PWRFILE to $PHY_IF" > /dev/console
			iwpriv $PHY_IF setcmd "loadtxpwrtable $PWRFILE"
		else
			ERROR="${SERVICE_NAME}, Error! Missing $PWRFILE"
			ulog wlan status $ERROR > /dev/console
		fi
	done
}
