#!/bin/sh
source /etc/init.d/date_functions.sh
source /etc/init.d/ulog_functions.sh
DDNS_HOSTNAME=`syscfg get ddns_hostname`
DDNS_SERVICE=`syscfg get ddns_service`
ulog ddns status "ddns client reports successful update of $DDNS_HOSTNAME with service $DDNS_SERVICE"
WAN_IFADDR=`sysevent get ipv4_wan_ipaddr`
syscfg set wan_last_ipaddr "$WAN_IFADDR"
CURRENT_TIME=`get_current_time`
syscfg set ddns_last_update "$CURRENT_TIME"
syscfg commit
