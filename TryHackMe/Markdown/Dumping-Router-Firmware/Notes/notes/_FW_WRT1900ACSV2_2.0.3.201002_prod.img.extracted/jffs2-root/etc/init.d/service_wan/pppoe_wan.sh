#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/ppp_helpers.sh
source /etc/init.d/service_wan/wan_helper_functions
LAN_IFNAME=`syscfg get lan_ifname`
PID="($$)"
prepare_pppoe() {
   echo "[utopia][pppoe] Configuring pppoe <`date`>" > /dev/console
   prepare_pppd_ip_pre_up_script
   prepare_pppd_ip_up_script
   prepare_pppd_ip_down_script
   prepare_pppd_ipv6_up_script
   prepare_pppd_ipv6_down_script
   prepare_pppd_options
   prepare_pppd_secrets
   prepare_pppoe_peers
}
prepare_pppoe_peers() {
   mkdir -p $PPP_PEERS_DIRECTORY
   echo -n > $PPPOE_PEERS_FILE
   INTERFACE_NAME=`syscfg get ${NAMESPACE} ifname`
   
   echo "plugin /lib/pppd/rp-pppoe.so" >> $PPPOE_PEERS_FILE
   echo "# Ethernet interface name" >> $PPPOE_PEERS_FILE
   echo "$INTERFACE_NAME" >> $PPPOE_PEERS_FILE
   echo "user \"$SYSCFG_wan_proto_username\"" >> $PPPOE_PEERS_FILE
   REMOTE_NAME=$SYSCFG_wan_proto_remote_name
   if [ "" != "$REMOTE_NAME" ] ; then
      echo "remotename \"$REMOTE_NAME\"" >> $PPPOE_PEERS_FILE
   fi
   if [ "" != "$SYSCFG_pppoe_service_name" ] ; then
      echo "rp_pppoe_service \"$SYSCFG_pppoe_service_name\"" >> $PPPOE_PEERS_FILE
   fi
   if [ "" != "$SYSCFG_pppoe_access_concentrator_name" ] ; then
      echo "rp_pppoe_ac \"$SYSCFG_pppoe_access_concentrator_name\"" >> $PPPOE_PEERS_FILE
   fi
   MODEM_ENABLED=`syscfg get modem::enabled`
   MODEM_PROTOCOL=`syscfg get modem::protocol`
   MODEM_MAC=`sysevent get modem_mac`
   DEFAULT_SESSION_ID="154";
   if [ -n "$MODEM_ENABLED" -a "$MODEM_ENABLED" = "1" -a "$MODEM_PROTOCOL" = "pppoa" ] ; then
       echo "# pppoa exist session" >> $PPPOE_PEERS_FILE
       if [ -n "$DEFAULT_SESSION_ID" -a -n $MODEM_MAC ] ; then
           echo "rp_pppoe_sess $DEFAULT_SESSION_ID:$MODEM_MAC" >> $PPPOE_PEERS_FILE
       fi
   fi
   echo "noauth" >> $PPPOE_PEERS_FILE
   echo "hide-password" >> $PPPOE_PEERS_FILE
   echo "updetach" >> $PPPOE_PEERS_FILE
   echo "debug" >> $PPPOE_PEERS_FILE
   if [ "1" = "$SYSCFG_default" ] ; then
      echo "defaultroute" >> $PPP_PEERS_FILE
   fi
   echo "noipdefault" >> $PPPOE_PEERS_FILE
   echo "usepeerdns" >> $PPPOE_PEERS_FILE
}
bring_wan_down() {
   ulog pppoe_wan status "$PID bring_wan_down"
   do_stop_wan_monitor
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set ipv4_wan_ipaddr
      sysevent set ipv4_wan_subnet
      sysevent set default_router
      sysevent set wan_start_time
   fi
   sysevent set pppd_current_wan_ipaddr
   sysevent set pppd_current_wan_subnet
   sysevent set pppd_current_wan_ifname
   sysevent set ppp_status down
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr
   sysevent set ${NAMESPACE}_ipv4_wan_subnet
   sysevent set ${NAMESPACE}_ipv4_default_router
   sysevent set ${NAMESPACE}_wan_start_time
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status stopped
   sysevent set ${NAMESPACE}_current_ipv4_wan_state down
   sysevent set wmon_state
   sysevent set firewall-restart
}
bring_wan_up() {
   WMON_STATE=`sysevent get wmon_state`
   if [ "started" = "$WMON_STATE" ] ; then
      ulog pppoe_wan status "$PID bring_wan_up already started. Exit"
      exit 0
   fi
   if [ "pppoe" = "$SYSCFG_wan_proto" ] ; then
      sysevent set wmon_state started
      ulog pppoe_wan status "$PID bring_wan_up"
      sysevent set pppd_current_wan_ipaddr
      sysevent set pppd_current_wan_subnet
      sysevent set pppd_current_wan_ifname
      prepare_pppoe
      sysevent set ${NAMESPACE}-errinfo
      do_start_wan_monitor $NAMESPACE
      sysevent set ${NAMESPACE}_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
      if [ "1" = "$SYSCFG_default" ] ; then
         sysevent set wan_start_time `cat /proc/uptime | cut -d'.' -f1`
      fi
   fi
}
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
service_init $1
ulog pppoe_wan status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog pppoe_wan status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog pppoe_wan status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
ulog pppoe_wan status "$PID wan_proto is $SYSCFG_wan_proto"
case "$EVENT" in
   current_ipv4_link_state)
      ulog pppoe_wan status "$PID ipv4 link state is $SYSEVENT_current_ipv4_link_state"
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pppoe_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
         else
            ulog pppoe_wan status "$PID ipv4 link is down. Wan is already down"
            bring_wan_down
         fi
         exit 0
      else
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pppoe_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
                  bring_wan_up
                  exit 0
            else
               ulog pppoe_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pppoe_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog pppoe_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         bring_wan_down
      fi
      ;;
   config_pppoe_peers)
      prepare_pppoe_peers
      ;;
   *)
      ulog pppoe_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
