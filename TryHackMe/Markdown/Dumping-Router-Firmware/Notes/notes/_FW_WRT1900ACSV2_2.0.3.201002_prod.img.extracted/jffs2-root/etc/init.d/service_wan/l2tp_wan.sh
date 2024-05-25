#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_wan/ppp_helpers.sh
source /etc/init.d/service_wan/wan_helper_functions
LAN_IFNAME=`syscfg get lan_ifname`
PID="($$)"
SELF_NAME=l2tp_wan
L2TP_PEERS_DIRECTORY=/etc/ppp/peers
L2TP_CONF_DIR=/etc/l2tp
L2TP_CONF_FILE=$L2TP_CONF_DIR"/"l2tp.conf
L2TP_OPTIONS_DIR=/etc/ppp/peers
L2TP_OPTIONS_FILE=$L2TP_OPTIONS_DIR"/utopia-l2tp"
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
}
register_firewall_hooks() {
   IFNAME=`sysevent get ${NAMESPACE}_current_wan_ifname`
   unregister_firewall_hooks
   NAME=`sysevent setunique GeneralPurposeFirewallRule " -A INPUT -i $IFNAME -s $SYSCFG_wan_proto_server_address -p tcp -m tcp --sport 1701 -j xlog_accept_wan2self"`
   sysevent set ${SELF_NAME}_gp_fw_1 "$NAME"
   NAME=`sysevent setunique GeneralPurposeFirewallRule " -A INPUT -i $IFNAME -s $SYSCFG_wan_proto_server_address -p udp -m udp --sport 1701 -j xlog_accept_wan2self"`
   sysevent set ${SELF_NAME}_gp_fw_2 "$NAME"
   NAME=`sysevent setunique NatFirewallRule " -A PREROUTING -i $IFNAME -s $SYSCFG_wan_proto_server_address -p tcp -m tcp --sport 1701 -j RETURN"`
   sysevent set ${SELF_NAME}_nat_fw_1 "$NAME"
   NAME=`sysevent setunique NatFirewallRule " -A PREROUTING -i $IFNAME -s $SYSCFG_wan_proto_server_address -p udp -m udp --sport 1701 -j RETURN"`
   sysevent set ${SELF_NAME}_nat_fw_2 "$NAME"
   sysevent set firewall-restart
}
prepare_l2tp() {
   echo "[utopia][l2tp] Configuring l2tp <`date`>" > /dev/console
   prepare_pppd_ip_pre_up_script
   prepare_pppd_ip_up_script
   prepare_pppd_ip_down_script
   prepare_pppd_ipv6_up_script
   prepare_pppd_ipv6_down_script
   prepare_pppd_options
   prepare_pppd_secrets
   prepare_l2tp_peers
   LAN_IFNAME=`syscfg get lan_ifname`
}
prepare_l2tp_peers() {
   mkdir -p $L2TP_PEERS_DIRECTORY
   mkdir -p $L2TP_CONF_DIR
   echo -n > $L2TP_CONF_FILE
   echo "global" >> $L2TP_CONF_FILE
   echo "load-handler "sync-pppd.so"" >> $L2TP_CONF_FILE
   echo "load-handler "cmd.so"" >> $L2TP_CONF_FILE
   echo "listen-port 1701" >> $L2TP_CONF_FILE
   echo "section sync-pppd" >> $L2TP_CONF_FILE
   MODEL_NAME=`syscfg get device::model_base`
   if [ -z "$MODEL_NAME" ] ; then
        MODEL_NAME=`syscfg get device::modelNumber`
        MODEL_NAME=${MODEL_NAME%-*}
   fi
   if [ -n "$MODEL_NAME" ] ; then
       if [ "$MODEL_NAME" != "EA6500" -a "$MODEL_NAME" != "EA2700" -a "$MODEL_NAME" != "EA2700OQ" ] ; then
           if [ "`syscfg get kernel_mode_l2tp`" != "0" ];then
	           echo "kernel-mode 1" >> $L2TP_CONF_FILE
	       else
	           echo "kernel-mode 0" >> $L2TP_CONF_FILE
	       fi
	   fi
   fi
   echo "lac-pppd-opts \"file /etc/ppp/options\"" >> $L2TP_CONF_FILE
   echo "section peer" >> $L2TP_CONF_FILE
   L2TP_SERVER_IP=$SYSCFG_wan_proto_server_address
   echo "peer $L2TP_SERVER_IP" >> $L2TP_CONF_FILE
   echo "port 1701" >> $L2TP_CONF_FILE
   echo "lac-handler sync-pppd" >> $L2TP_CONF_FILE
   echo "lns-handler sync-pppd" >> $L2TP_CONF_FILE
   echo "hide-avps yes" >> $L2TP_CONF_FILE
   echo "section cmd" >> $L2TP_CONF_FILE
}
bring_wan_down() {
   ulog l2tp_wan status "$PID bring_wan_down"
   unregister_firewall_hooks
   do_stop_wan_monitor
   if [ "1" = "$SYSCFG_default" ] ; then
      sysevent set ipv4_wan_ipaddr
      sysevent set ipv4_wan_subnet 
      sysevent set wan_start_time
      sysevent set default_router
   fi
   sysevent set pppd_current_wan_ipaddr
   sysevent set pppd_current_wan_subnet
   sysevent set pppd_current_wan_ifname
   sysevent set ppp_status down
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr
   sysevent set ${NAMESPACE}_ipv4_wan_subnet
   sysevent set ${NAMESPACE}_ipv4_default_router
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
      ulog l2tp_wan status "$PID bring_wan_up already started. Exit"
      exit 0
   fi
   if [ "l2tp" = "$SYSCFG_wan_proto" ] ; then
      sysevent set wmon_state started
      ulog l2tp_wan status "$PID bring_wan_up"
      register_firewall_hooks
      WAN_ADDR=`sysevent get ipv4_wan_ipaddr`
      sysevent set pppd_current_wan_ipaddr $WAN_ADDR
      sysevent set pppd_current_wan_subnet
      sysevent set pppd_current_wan_ifname
      sysevent set ppp_status
      sysevent set firewall-restart
      prepare_l2tp
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
ulog l2tp_wan status "$PID ${NAMESPACE}_current_ipv4_link_state is $SYSEVENT_current_ipv4_link_state"
ulog l2tp_wan status "$PID ${NAMESPACE}_desired_ipv4_wan_state is $SYSEVENT_desired_ipv4_wan_state"
ulog l2tp_wan status "$PID ${NAMESPACE}_current_ipv4_wan_state is $SYSEVENT_current_ipv4_wan_state"
ulog l2tp_wan status "$PID wan_proto is $SYSCFG_wan_proto"
case "$EVENT" in
   current_ipv4_link_state)
      ulog l2tp_wan status "$PID ipv4 link state is $SYSEVENT_current_ipv4_link_state"
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog l2tp_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
         else
            ulog l2tp_wan status "$PID ipv4 link is down. Wan is already down"
            bring_wan_down
         fi
         exit 0
      else
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog l2tp_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
                  bring_wan_up
                  exit 0
            else
               ulog l2tp_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog l2tp_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog l2tp_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog l2tp_wan status "$PID wan is already down. Bringing down again."
            bring_wan_down
         else
            bring_wan_down
         fi
      fi
      ;;
   config_l2tp_peers)
      prepare_l2tp_peers
      ;;
   *)
      ulog l2tp_wan status "$PID Invalid parameter $1 "
      exit 3
      ;;
esac
