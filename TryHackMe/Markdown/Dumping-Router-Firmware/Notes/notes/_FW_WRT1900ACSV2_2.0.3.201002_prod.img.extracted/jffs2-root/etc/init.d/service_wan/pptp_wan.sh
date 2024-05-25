#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/ppp_helpers.sh
source /etc/init.d/service_wan/wan_helper_functions
PPTP_PEERS_DIRECTORY=/etc/ppp/peers
PPTP_TUNNEL_NAME=utopia-pptp
PPTP_PEERS_FILE=$PPTP_PEERS_DIRECTORY"/"$PPTP_TUNNEL_NAME
PPTP_BIN=/usr/sbin/pptp
LAN_IFNAME=`syscfg get lan_ifname`
PID="($$)"
SELF_NAME=pptp_wan
PPTP_OPTIONS_FILE=/etc/ppp/options.pptp
unregister_firewall_hooks() {
   NAME=`sysevent get ${SELF_NAME}_gp_fw_1`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_gp_fw_1
   fi
   NAME=`sysevent get ${SELF_NAME}_gp_fw_2`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_gp_fw_2
   fi
   NAME=`sysevent get ${SELF_NAME}_gp_fw_3`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_gp_fw_3
   fi
   NAME=`sysevent get ${SELF_NAME}_nat_fw_1`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_nat_fw_1
   fi
   NAME=`sysevent get ${SELF_NAME}_nat_fw_2`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_nat_fw_2
   fi
   NAME=`sysevent get ${SELF_NAME}_nat_fw_3`
   if [ -n "$NAME" ] ; then
      sysevent set $NAME
      sysevent set ${SELF_NAME}_nat_fw_3
   fi
}
register_firewall_hooks() {
   IFNAME=`sysevent get ${NAMESPACE}_current_wan_ifname`
   unregister_firewall_hooks
   NAME=`sysevent setunique GeneralPurposeFirewallRule " -A INPUT -i $IFNAME -s $SYSCFG_wan_proto_server_address -p tcp -m tcp --sport 1723 -j xlog_accept_wan2self"`
   sysevent set ${SELF_NAME}_gp_fw_1 "$NAME"
   NAME=`sysevent setunique GeneralPurposeFirewallRule " -A INPUT -i $IFNAME -s $SYSCFG_wan_proto_server_address -p udp -m udp --sport 1723 -j xlog_accept_wan2self"`
   sysevent set ${SELF_NAME}_gp_fw_2 "$NAME"
   NAME=`sysevent setunique GeneralPurposeFirewallRule " -A INPUT -i $IFNAME -s $SYSCFG_wan_proto_server_address -p 0x2f -j xlog_accept_wan2self"`
   sysevent set ${SELF_NAME}_gp_fw_3 "$NAME"
   NAME=`sysevent setunique NatFirewallRule " -A PREROUTING -i $IFNAME -s $SYSCFG_wan_proto_server_address -p tcp -m tcp --sport 1723 -j RETURN"`
   sysevent set ${SELF_NAME}_nat_fw_1 "$NAME"
   NAME=`sysevent setunique NatFirewallRule " -A PREROUTING -i $IFNAME -s $SYSCFG_wan_proto_server_address -p udp -m udp --sport 1723 -j RETURN"`
   sysevent set ${SELF_NAME}_nat_fw_2 "$NAME"
   NAME=`sysevent setunique NatFirewallRule " -A PREROUTING -i $IFNAME -s $SYSCFG_wan_proto_server_address -p 0x2f -j RETURN"`
   sysevent set ${SELF_NAME}_nat_fw_3 "$NAME"
   sysevent set firewall-restart
}
prepare_pptp() {
   echo "[utopia][pptp] Configuring pptp <`date`>" > /dev/console
   prepare_pppd_ip_pre_up_script
   prepare_pppd_ip_up_script
   prepare_pppd_ip_down_script
   prepare_pppd_ipv6_up_script
   prepare_pppd_ipv6_down_script
   prepare_pppd_options
   prepare_pppd_secrets
   prepare_pptp_peers
}
prepare_pptp_peers() {
   mkdir -p $PPTP_PEERS_DIRECTORY
   echo -n > $PPTP_PEERS_FILE
           echo "plugin pptp.so" >> $PPTP_PEERS_FILE
           echo "pptp_server $SYSCFG_wan_proto_server_address" >> $PPTP_PEERS_FILE
   echo "name \"$SYSCFG_wan_proto_username\""  >> $PPTP_PEERS_FILE
   REMOTE_NAME=$SYSCFG_wan_proto_remote_name
   if [ "" != "$REMOTE_NAME" ] ; then
      echo "remotename \"$REMOTE_NAME\"" >> $PPTP_PEERS_FILE
   fi
   echo "ipparam $PPTP_TUNNEL_NAME"  >> $PPTP_PEERS_FILE
}
bring_wan_down() {
   ulog pptp_wan status "$PID bring_wan_down"
   unregister_firewall_hooks
   do_stop_wan_monitor
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set ipv4_wan_ipaddr
      sysevent set ipv4_wan_subnet
      sysevent set wan_default_router
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
   sysevent set ${NAMESPACE}_current_wan_ifname
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status stopped
   sysevent set ${NAMESPACE}_current_ipv4_wan_state down
   sysevent set wmon_state
   sysevent set firewall-restart
}
bring_wan_up() {
   WMON_STATE=`sysevent get wmon_state`
   if [ "started" = "$WMON_STATE" ] ; then
      ulog pptp_wan status "$PID bring_wan_up already started. Exit"
      exit 0
   fi
   if [ "pptp" = "$SYSCFG_wan_proto" ] ; then
      sysevent set wmon_state started
      ulog pptp_wan status "$PID bring_wan_up"
      register_firewall_hooks
      WAN_ADDR=`sysevent get ipv4_wan_ipaddr`
      sysevent set pppd_current_wan_ipaddr $WAN_ADDR
      sysevent set pppd_current_wan_subnet
      sysevent set pppd_current_wan_ifname
      sysevent set ppp_status
      sysevent set firewall-restart
      prepare_pptp
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
ulog pptp_wan status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog pptp_wan status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog pptp_wan status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
case "$EVENT" in
   current_ipv4_link_state)
      ulog pptp_wan status "$PID ipv4 link state is $SYSEVENT_current_ipv4_link_state"
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pptp_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
         else
            ulog pptp_wan status "$PID ipv4 link is down. Wan is already down"
            bring_wan_down
         fi
         exit 0
      else
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pptp_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
                  bring_wan_up
                  exit 0
            else
               ulog pptp_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pptp_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog pptp_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog pptp_wan status "$PID wan is already down. Bringing down again."
            bring_wan_down
         else
            bring_wan_down
         fi
      fi
      ;;
   config_pptp_peers)
      prepare_pptp_peers
      ;;
   *)
      ulog pptp_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
