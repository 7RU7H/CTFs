#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/network_functions.sh
source /etc/init.d/service_dhcp_server/dhcp_server_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
SERVICE_NAME="dhcp_server"
DHCP_CONF=/etc/dnsmasq.conf
BIN=dnsmasq
SERVER=/sbin/${BIN}
PMON=/etc/init.d/pmon.sh
PID_FILE=/var/run/dnsmasq.pid
PID=$$
EVENT=$1
if [ "`syscfg get dns::debug`" == "1" ] ; then
    LOG=/var/log/dnsmasq.log
    DEBUG_OPTIONS=`syscfg get dns::debug_options`
    DEBUG_FLAG="-q $DEBUG_OPTIONS"
else
    LOG=daemon
    DEBUG_FLAG=""
fi
DHCP_SLOW_START_1_FILE=/etc/cron/cron.everyminute/dhcp_slow_start.sh
DHCP_SLOW_START_2_FILE=/etc/cron/cron.every5minute/dhcp_slow_start.sh
DHCP_SLOW_START_3_FILE=/etc/cron/cron.every10minute/dhcp_slow_start.sh
service_init ()
{
    FOO=`utctx_cmd get lan_ipaddr lan_netmask bridge_ipaddr bridge_netmask dhcp_server_enabled bridge_mode dhcp_nameserver_1 dhcp_nameserver_2 dhcp_nameserver_3`
    eval $FOO
    if [ -z "$SYSCFG_lan_ipaddr" ] ; then
       ulog dhcp_server status "Lan is not configured. Using Bridge"
       SYSCFG_lan_ipaddr=$SYSCFG_bridge_ipaddr
       SYSCFG_lan_netmask=$SYSCFG_bridge_netmask
    fi
    if [ -z "$SYSCFG_lan_ipaddr" ] ; then
       ulog dhcp_server status "Lan is not configured. Neither is Bridge. Cannot start dhcp server"
       sysevent set ${SERVICE_NAME}-errinfo "Bad Configuration"
       sysevent set ${SERVICE_NAME}-status Stopped
       exit 0
   fi
}
lan_status_change ()
{
   wait_till_end_state dns
   wait_till_end_state dhcp_server
   CURRENT_LAN_STATE=$1
   ulog dhcp_server status "$PID got event lan-status: $CURRENT_LAN_STATE"
   if [ "stopped" = "$CURRENT_LAN_STATE" ] ; then
      services_stop
   elif [ "started" = "$CURRENT_LAN_STATE" ] ; then
      DHCP_STATE=`sysevent get dhcp_server-status`
      DNS_STATE=`sysevent get dns-status`
      if [ "0" = "$SYSCFG_dhcp_server_enabled" -a "stopped" != "$DHCP_STATE" ] ; then
         services_stop
         wait_till_end_state dns
         wait_till_end_state dhcp_server
      fi
      if [ "0" != "$SYSCFG_dhcp_server_enabled" -a "started" != "$DHCP_STATE" ] ; then
         services_start
      elif [ "started" != "$DNS_STATE" ] ; then
         services_start
      fi
   fi
}
restart_request ()
{
   CURRENT_LAN_STATE=`sysevent get lan-status`
   if [ "started" != "$CURRENT_LAN_STATE" ] ; then
      exit 0
   fi
   wait_till_end_state dns
   wait_till_end_state dhcp_server
   DHCP_TMP_CONF="/tmp/dnsmasq.conf.orig"
   cp -f $DHCP_CONF $DHCP_TMP_CONF
   if [ "0" = "$SYSCFG_dhcp_server_enabled" ] ; then
      prepare_hostname
      prepare_dhcp_conf $SYSCFG_lan_ipaddr $SYSCFG_lan_netmask dns_only
   else
      prepare_hostname
      prepare_dhcp_conf $SYSCFG_lan_ipaddr $SYSCFG_lan_netmask
      sanitize_leases_file
   fi
   RESTART=0
   diff -q $DHCP_CONF $DHCP_TMP_CONF
   if [ "0" != "$?" ] ; then
      RESTART=1
   fi
   CURRENT_PID=`cat $PID_FILE`
   if [ -z "$CURRENT_PID" ] ; then
      RESTART=1
   else
      CURRENT_PIDS=`pidof dnsmasq`
      if [ -z "$CURRENT_PIDS" ] ; then
         RESTART=1
      else
         RUNNING_PIDS=`pidof dnsmasq`
         FOO=`echo $RUNNING_PIDS | grep $CURRENT_PID`
         if [ -z "$FOO" ] ; then
            RESTART=1
         fi
      fi 
   fi
   rm -f $DHCP_TMP_CONF
   killall -HUP `basename $SERVER`
   if [ "0" = "$RESTART" ] ; then
      reset_ethernet_ports
      sysevent set reboot-status dhcp-started
      ulog dhcp_server status "$PID reboot-status:dhcp-started, dhcp-server no need restart"
      exit 0
   else
      services_stop
      services_start
   fi
}
dhcp_server_stop ()
{
   wait_till_end_state dhcp_server
   DHCP_STATUS=`sysevent get dhcp_server-status`
   if [ "stopped" = "$DHCP_STATUS" ] ; then
      return 0
   fi
   services_stop
   wait_till_end_state dns
   prepare_hostname
   prepare_dhcp_conf $SYSCFG_lan_ipaddr $SYSCFG_lan_netmask dns_only
   $SERVER -u nobody -P 4096 -C $DHCP_CONF -8 $LOG
   sysevent set dns-status started
}
dns_stop ()
{
   sysevent set ${SERVICE_NAME}-errinfo
   ulog dhcp_server status "dns-stop requested but ignored"
}
services_start()
{
    if [ "$SYSCFG_bridge_mode" != "0" ] ; then
        return 0
    fi
   wait_till_end_state dns
   wait_till_end_state dhcp_server
   DHCP_STATE=`sysevent get dhcp_server-status`
   DNS_STATE=`sysevent get dns-status`
   if [ "0" = "$SYSCFG_dhcp_server_enabled" ]  
   then
      if [ "started" = "DNS_STATE" ]
      then
          return 0
      fi
   fi
   if [ "started" = "$DHCP_STATE" -a "started" = "$DNS_STATE" ] ; then
      return 0
   fi
   if [ "$DHCP_STATE" != "$DNS_STATE" ] ; then
      services_stop
      wait_till_end_state dhcp_server
      wait_till_end_state dns
   fi
   if [ "0" = "$SYSCFG_dhcp_server_enabled" ]
   then
      prepare_hostname
      prepare_dhcp_conf $SYSCFG_lan_ipaddr $SYSCFG_lan_netmask dns_only
      $SERVER -u nobody -P 4096 -C $DHCP_CONF -8 $LOG
      sysevent set dns-status started
      sysevent set reboot-status dhcp-started
      ulog dhcp_server status "$PID reboot-status:dhcp-started"
   else
      if [ -f "$DHCP_SLOW_START_1_FILE" ] ; then
         rm -f $DHCP_SLOW_START_1_FILE
      fi
      if [ -f "$DHCP_SLOW_START_2_FILE" ] ; then
         rm -f $DHCP_SLOW_START_2_FILE
      fi
      if [ -f "$DHCP_SLOW_START_3_FILE" ] ; then
         rm -f $DHCP_SLOW_START_3_FILE
      fi
      prepare_hostname
      if [ "$SYSCFG_bridge_mode" = "0" ] ; then
         prepare_dhcp_conf $SYSCFG_lan_ipaddr $SYSCFG_lan_netmask
      fi
      sanitize_leases_file
      LOG_LEVEL=`syscfg get fw_log_level`
      if [ "$LOG_LEVEL" = "0" ] ; then
          DISABLE_DHCP_LOG="1";
      else
          DISABLE_DHCP_LOG="0";
      fi
        if [ -n "$SYSCFG_dhcp_nameserver_1" -a "$SYSCFG_dhcp_nameserver_1" != "0.0.0.0" ] || [ -n "$SYSCFG_dhcp_nameserver_2" -a "$SYSCFG_dhcp_nameserver_2" != "0.0.0.0" ] || [ -n "$SYSCFG_dhcp_nameserver_3" -a "$SYSCFG_dhcp_nameserver_3" != "0.0.0.0" ] ; then
            ORDER="--strict-order"
        else
            ORDER=""
        fi
        $SERVER -u nobody --dhcp-authoritative --disable_dhcp_log $DISABLE_DHCP_LOG -P 4096 -C $DHCP_CONF -8 $LOG $ORDER
      if [ "1" = "$DHCP_SLOW_START_NEEDED" -a -n "$TIME_FILE" ] ; then
         create_slow_start_file $TIME_FILE
         chmod 700 $TIME_FILE
      fi
      reset_ethernet_ports
      $PMON setproc dhcp_server $BIN $PID_FILE "/etc/init.d/service_dhcp_server.sh dhcp_server-restart"
      sysevent set dns-status started
      sysevent set dhcp_server-status started
      sleep 20
      sysevent set reboot-status dhcp-started
      ulog dhcp_server status "$PID reboot-status:dhcp-started"
   fi
}
services_stop()
{
   wait_till_end_state dns
   wait_till_end_state dhcp_server
   DHCP_STATE=`sysevent get dhcp_server-status`
   DNS_STATE=`sysevent get dns-status`
   sysevent set dns-errinfo
   sysevent set dhcp_server_errinfo
   if [ "stopped" != "$DHCP_STATE" ]
   then 
      $PMON unsetproc dhcp_server
      killall -HUP `basename $SERVER`
   fi
   killall `basename $SERVER` > /dev/null 2>&1
   rm -f $PID_FILE
   if [ "stopped" != "$DHCP_STATE" ]
   then 
      sysevent set dhcp_server-status stopped
      sysevent set reboot-status dhcp-stopped
      ulog dhcp_server status "$PID reboot-status:dhcp-stopped"
   fi
   sysevent set dns-status stopped
}
dns_cache_flush ()
{
    if [ -f $PID_FILE ]; then
        kill -HUP `cat $PID_FILE`
    fi
}
service_init
case "$1" in
   ${SERVICE_NAME}-start)
      services_start
      ;;
   ${SERVICE_NAME}-stop)
      dhcp_server_stop
      ;;
   ${SERVICE_NAME}-restart)
      restart_request
      ;;
   dns-start)
      services_start
      ;;
   dns-stop)
      dns_stop
      ;;
   dns-restart)
      restart_request
      ;;
   lan-started)
      lan_status_change "started"
      ;;
   lan-stopped)
      lan_status_change "stopped"
      ;;
   dns-cache-flush)
      dns_cache_flush
      ;;
   delete_lease)
      ulog dnsmasq status "($PID) Called because of lease deleted command"
      delete_dhcp_lease $2
      ;;
   show_dhcp_clients)
      ulog dnsmasq status "($PID) Called because of show_ipv4_dhcp_clients command"
      show_ipv4_dhcp_clients 
      ;;
   *)
      echo "Usage: $SERVICE_NAME [start|stop|restart] dns-[start|stop|restart]" >&2
      exit 3
      ;;
esac
