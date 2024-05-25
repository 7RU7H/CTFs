#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_wan/wan_helper_functions
source /etc/init.d/resolver_functions.sh
PID="($$)"
DSLITE_TUNNEL_INTERFACE_NAME="dslite"
CRON_TIMEOUT_DIR="/etc/cron/cron.every5minute/"
CRON_TIMEOUT_FILE="${CRON_TIMEOUT_DIR}dslite_retry.sh"
CRON_MONITOR_FILE="${CRON_TIMEOUT_DIR}dslite_tunnel_monitor.sh"
DSLITE_NSLOOKUP_RETRY_ASYNC="dslite_nslookup_async"
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
   eval `utctx_cmd get dslite_aftr dslite_ipv4_address dslite_peer_ipv4_address`
}
make_cron_timeout_file() {
   echo "#!/bin/sh" > $CRON_TIMEOUT_FILE
   echo "VAL=\`sysevent get ${NAMESPACE}_current_ipv4_link_state\`" >> $CRON_TIMEOUT_FILE
   echo "if [ \"up\" = \"\$VAL\" ] ; then" >> $CRON_TIMEOUT_FILE
      echo "sysevent set ${NAMESPACE}_nslookup_retry ${NAMESPACE}" >> $CRON_TIMEOUT_FILE
   echo "fi" >> $CRON_TIMEOUT_FILE
   chmod 777 $CRON_TIMEOUT_FILE
}
start_dslite_interface_monitor() {
   if [ -f $CRON_MONITOR_FILE ] ; then
      return 0
   fi
   echo "#!/bin/sh" > $CRON_MONITOR_FILE
   echo "ifconfig dslite  &> /dev/null" >>  $CRON_MONITOR_FILE
   echo "if [ \"\$?\" = \"0\" ] ; then" >> $CRON_MONITOR_FILE
   echo "   return 0" >> $CRON_MONITOR_FILE
   echo "fi" >> $CRON_MONITOR_FILE
   echo "VAL=\`sysevent get ${NAMESPACE}_desired_ipv4_wan_state\`" >> $CRON_MONITOR_FILE
   echo "if [ \"up\" = \"\$VAL\" ] ; then" >> $CRON_MONITOR_FILE
      echo "sysevent set ${NAMESPACE}_desired_ipv4_wan_state down" >> $CRON_MONITOR_FILE
      echo "sleep 5" >> $CRON_MONITOR_FILE
      echo "sysevent set ${NAMESPACE}_desired_ipv4_wan_state up" >> $CRON_MONITOR_FILE
   echo "fi" >> $CRON_MONITOR_FILE
  
   chmod 777 $CRON_MONITOR_FILE
}
find_dslite_aftr ()
{
   DSLITE_AFTR=`sysevent get dhcpv6_aftr`
   if [ -n "$DSLITE_AFTR" ] ; then
      ulog dslite_wan status "$PID Using aftr received from dhcpv6: $DSLITE_AFTR"
   else
      DSLITE_AFTR=$SYSCFG_dslite_aftr
   fi 
   if [ -z "$DSLITE_AFTR" ] ; then
      ulog_error ipv4 dslite "dslite is misconfigured: No aftr"
      sysevent set ${NAMESPACE}-status error
      sysevent set ${NAMESPACE}-errinfo "No dslite aftr configured"
      exit 1
   fi
   if [ "`echo $DSLITE_AFTR | egrep -c '^[0-9a-fA-F]+:+'`" = "0" ] ; then
      ASYNC=`sysevent get $DSLITE_NSLOOKUP_RETRY_ASYNC`
      if [ -z "$ASYNC" ] ; then
         ASYNC=`sysevent async ${NAMESPACE}_nslookup_retry /etc/init.d/service_wan/dslite_wan.sh $NAMESPACE`
         sysevent set $DSLITE_NSLOOKUP_RETRY_ASYNC "$ASYNC"
         sysevent setoptions ${NAMESPACE}_nslookup_retry $TUPLE_FLAG_EVENT
      fi
      make_cron_timeout_file
      NEWAFTR=`nslookup $DSLITE_AFTR | awk '
            /^Name:/ { body=1 ; }
            /^Address .*: [0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/ { next ; }                                 
            /^Address .*: [0-9a-fA-F]{1,4}:.*/  { if ( body == 1 ) { print $3 ; exit ; } } 
             '`
      if [ -n "$NEWAFTR" ] ; then
         TEST_AFTR=$NEWAFTR
      else
            ulog dslite_wan status "$PID nslookup of $DSLITE_AFTR failed. Retrying later"
         TEST_AFTR=
      fi
      if [ -n "$TEST_AFTR" ] ; then
         DSLITE_AFTR=$TEST_AFTR
      else
         DSLITE_AFTR=
      fi
   fi
}
bring_wan_down() {
   ip route del default dev ${DSLITE_TUNNEL_INTERFACE_NAME}
   ip link set dev $DSLITE_TUNNEL_INTERFACE_NAME down
   ip -6 tunnel del ${DSLITE_TUNNEL_INTERFACE_NAME} 
   sysevent set ${NAMESPACE}_current_ipv4_wan_state down
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr
   sysevent set ${NAMESPACE}_ipv4_wan_subnet
   sysevent set ${NAMESPACE}_ipv4_default_router
   sysevent set ${NAMESPACE}_current_wan_ifname
   sysevent set ${NAMESPACE}_wan_start_time
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status stopped
   prepare_resolver_conf
}
bring_wan_up() {
   SYSEVENT_current_wan_ipv6address=`sysevent get current_wan_ipv6address`
   if [ -z "$SYSEVENT_current_wan_ipv6address" ] ; then
     ulog dslite_wan status "$PID No ipv6 address. Aborting"
      sysevent set ${NAMESPACE}-status error
      sysevent set ${NAMESPACE}-errinfo "No ipv6 address on default wan"
     return 0
   fi
   find_dslite_aftr
   if [ -z "$DSLITE_AFTR" ] ; then
      ulog dslite_wan status "$PID No dslite aftr ipv6 address found. Aborting"
      return 0
   else
      ulog dslite_wan status "$PID dslite aftr is $DSLITE_AFTR"
   fi
   if [ -z "$SYSCFG_dslite_ipv4_address" ] ; then
      ulog dslite_wan status "$PID No dslite ipv4 address provisioned. Using default 192.0.0.2"
      SYSCFG_dslite_ipv4_address="192.0.0.2"
   fi
   if [ -z "$SYSCFG_dslite_peer_ipv4_address" ] ; then
      ulog dslite_wan status "$PID No dslite peer ipv4 address provisioned. Using default 192.0.0.1"
      SYSCFG_dslite_peer_ipv4_address="192.0.0.1"
   fi
   ASYNC=`sysevent get $DSLITE_NSLOOKUP_RETRY_ASYNC`
   if [ -n "$ASYNC" ] ; then
      sysevent rm_async $ASYNC
      sysevent set $DSLITE_NSLOOKUP_RETRY_ASYNC 
   fi
   rm -f $CRON_TIMEOUT_FILE
   sysevent set ${NAMESPACE}_ipv4_wan_ipaddr ${SYSCFG_dslite_ipv4_address}
   sysevent set ${NAMESPACE}_ipv4_wan_subnet 255.255.255.0
   sysevent set ${NAMESPACE}_ipv4_default_router  $SYSCFG_dslite_peer_ipv4_address
   sysevent set ${NAMESPACE}_current_wan_ifname ${DSLITE_TUNNEL_INTERFACE_NAME}
   sysevent set ${NAMESPACE}_wan_start_time `cat /proc/uptime | cut -d'.' -f1`
   ip -6 tunnel add ${DSLITE_TUNNEL_INTERFACE_NAME} mode ipip6 remote ${DSLITE_AFTR} local ${SYSEVENT_current_wan_ipv6address} dev ${SYSCFG_wan_physical_ifname} 
   ip addr add ${SYSCFG_dslite_ipv4_address} peer ${SYSCFG_dslite_peer_ipv4_address} dev ${DSLITE_TUNNEL_INTERFACE_NAME}
   ip link set dev $DSLITE_TUNNEL_INTERFACE_NAME up
   PHYSICAL_MTU=`ip link show dev $SYSCFG_wan_physical_ifname | grep -o "mtu .*" | cut -f2 -d' '`
   DSLITE_MTU=`dc $PHYSICAL_MTU 40 - p`
   if [ -z "$DSLITE_MTU" ] ; then
      ulog dslite_wan status "$PID Calculated MTU is $DSLITE_MTU. Using 1460 instead"
      DSLITE_MTU=1460
   fi
   ip link set ${DSLITE_TUNNEL_INTERFACE_NAME} mtu $DSLITE_MTU
   ulog dslite_wan status "$PID Setting ${DSLITE_TUNNEL_INTERFACE_NAME} mtu $DSLITE_MTU"
   prepare_resolver_conf
   sysevent set ${NAMESPACE}_current_ipv4_wan_state up
   sysevent set ${NAMESPACE}-errinfo
   sysevent set ${NAMESPACE}-status started
}
if [ -z "$1" ] ; then
   return
else
   INPUT_PARAM_1=$1
fi
service_init $INPUT_PARAM_1
if [ "${NAMESPACE}_nslookup_retry" = "$INPUT_PARAM_1" ] ; then
   if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" -a "up" = "$SYSEVENT_current_ipv4_link_state" -a "down" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
      ulog dslite_wan status "$PID Bringing wan up on behalf of nslookup_retry"
      bring_wan_up
   fi
   exit 0
fi
case "$EVENT" in
   current_ipv4_link_state)
      if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dslite_wan status "$PID ipv4 link is down. Tearing down wan"
            bring_wan_down
            exit 0
         else
            ulog dslite_wan status "$PID ipv4 link is down. Wan is already down. Nothing to do."
            exit 0
         fi
      else
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dslite_wan status "$PID ipv4 link is up. Wan is already up"
            exit 0
         else
            if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
            ulog dslite_wan status "$PID Ready to bring up wan."
               bring_wan_up
               exit 0
            else
               ulog dslite_wan status "$PID ipv4 link is up. Wan is not requested up"
               exit 0
            fi
         fi
      fi
      ;;
   desired_ipv4_wan_state)
      if [ "up" = "$SYSEVENT_desired_ipv4_wan_state" ] ; then
         if [ "up" = "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dslite_wan status "$PID wan is already up."
            exit 0
         else
            if [ "up" != "$SYSEVENT_current_ipv4_link_state" ] ; then
               ulog dslite_wan status "$PID wan up request deferred until link is up"
               exit 0
            else
               bring_wan_up
               exit 0
            fi
         fi
      else
         if [ "up" != "$SYSEVENT_current_ipv4_wan_state" ] ; then
            ulog dslite_wan status "$PID wan is already down. Bringing down again."
            bring_wan_down
         else
            bring_wan_down
         fi
      fi
      ;;
 *)
      ulog dslite_wan status "$PID Invalid parameter $INPUT_PARAM_1 "
      exit 3
      ;;
esac
