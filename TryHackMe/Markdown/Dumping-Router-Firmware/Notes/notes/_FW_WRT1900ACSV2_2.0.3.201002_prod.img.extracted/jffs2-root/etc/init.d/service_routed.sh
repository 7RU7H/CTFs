#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
SERVICE_NAME="routed"
ZEBRA_PID_FILE=/var/run/zebra.pid
RIPD_PID_FILE=/var/run/ripd.pid
ZEBRA_CONF_FILE=/etc/zebra.conf
RIPD_CONF_FILE=/etc/ripd.conf
ZEBRA_BIN_NAME=/usr/sbin/zebra
RIPD_BIN_NAME=/usr/sbin/ripd
CRON_RETRY_FILE_1=/etc/cron/cron.everyminute/zebra_ra_retry.sh
CRON_RETRY_FILE_2=/etc/cron/cron.every5minute/zebra_ra_retry.sh
CRON_RETRY_FILE_3=/etc/cron/cron.every10minute/zebra_ra_retry.sh
CRON_RETRY_FILE_4=/etc/cron/cron.hourly/zebra_ra_retry.sh
LOG=/var/log/ipv6.log
utctx_batch_get() 
{
    SYSCFG_FAILED='false'
    eval `utctx_cmd get $1`
    if [ $SYSCFG_FAILED = 'true' ] ; then
        echo "Call failed"
        return 1
    fi
}
get_one_static_route_group()
{
   utctx_batch_get "$1"
  
   eval NS='$'SYSCFG_$1
      ARGS="\
      $NS::dest \
      $NS::interface \
      $NS::netmask \
      $NS::gw"
   utctx_batch_get "$ARGS"
   if [ "${?}" -ne "0" ] ; then
      return 1
   fi
   eval `echo DEST='$'SYSCFG_${NS}_dest`
   eval `echo MASK='$'SYSCFG_${NS}_netmask`
   eval `echo INTERFACE='$'SYSCFG_${NS}_interface`
   eval `echo GW='$'SYSCFG_${NS}_gw`
}
clean_cron_retry_file() {
   rm -f $CRON_RETRY_FILE_1
   rm -f $CRON_RETRY_FILE_2
   rm -f $CRON_RETRY_FILE_3
   rm -f $CRON_RETRY_FILE_4
}
make_cron_retry_file() {
   TRIES=`sysevent get zebra_ra_cron_reties`
   if [ -z "$TRIES" ] ; then
      TRIES=0
      sysevent set zebra_ra_cron_reties $TRIES
   fi
   if [ "5" -ge "$TRIES" ] ; then
      CRON_FILE=$CRON_RETRY_FILE_1
   elif [ "10" -ge "$TRIES" ] ; then
      CRON_FILE=$CRON_RETRY_FILE_2
   elif [ "15" -ge "$TRIES" ] ; then
      CRON_FILE=$CRON_RETRY_FILE_3
   else
      CRON_FILE=$CRON_RETRY_FILE_4
   fi
   echo "#!/bin/sh" > $CRON_FILE
   echo "/etc/init.d/`basename $0` cron_handler $TRIES" >> $CRON_FILE
   chmod 777 $CRON_FILE
}
cron_handler() {
   clean_cron_retry_file
   TRIES=$1
   TRIES=`expr $TRIES + 1`
   ip -6 route show | grep default
   RES=$?
   if [ "0" = "$RES" ] ; then
      sysevent set routed-restart
   else
      sysevent set zebra_ra_cron_reties $TRIES
      make_cron_retry_file
   fi
}
make_zebra_conf_file() {
   if [ -n "$SYSCFG_hostname" ] ; then
      echo "hostname $SYSCFG_hostname" >> $ZEBRA_CONF_FILE
   fi
   echo "!password admin" >> $ZEBRA_CONF_FILE
   echo "!enable password admin" >> $ZEBRA_CONF_FILE
   echo "!log stdout" >> $ZEBRA_CONF_FILE
   echo "!log syslog" >> $ZEBRA_CONF_FILE
   echo "!log file /var/log/zebra.log" >> $ZEBRA_CONF_FILE
   if [ "" != "$SYSCFG_StaticRouteCount" ] && [ "0" != "$SYSCFG_StaticRouteCount" ] ; then
      WAN_IFNAME=`sysevent get current_wan_ifname`
      for ct in `seq 1 $SYSCFG_StaticRouteCount`
      do
         SR=StaticRoute_${ct}
         get_one_static_route_group $SR
         if [ "${?}" -ne "0" ] ; then
            ulog routed status "Failure in extracting static route info for $SR"
         else
            if [ "" = "$DEST" ] || [ "" = "$MASK" ] || [ "" = "$INTERFACE" ] ; then
               ulog routed status "Bad parameter for $NS"
            elif [ "lan" = "$INTERFACE" ] ; then
               if  [ "" = "$GW" ] ; then
                  ulog routed status "Bad parameter for $NS on $INTERFACE"
               else
                  echo "ip route $DEST $MASK $GW" >> $ZEBRA_CONF_FILE 
               fi
            elif [ "wan" = "$INTERFACE" ] ; then
               if [ "" = "$GW" ] || [ "0.0.0.0" = "$GW" ] ; then
                  echo "ip route $DEST $MASK $WAN_IFNAME" >> $ZEBRA_CONF_FILE 
               else
                  echo "ip route $DEST $MASK $GW" >> $ZEBRA_CONF_FILE 
               fi
            fi
         fi
      done
   fi
}
make_ripd_conf_file() {
   WAN_IFNAME=`sysevent get current_wan_ifname`
   echo "router rip" >> $RIPD_CONF_FILE
   if [ "" = "$SYSCFG_rip_interface_wan" ] || [ "0" = "$SYSCFG_rip_interface_wan" ] ; then
      RIP_WAN_PREFIX="!"
   else
      RIP_WAN_PREFIX=
   fi
   if [ "" = "$SYSCFG_rip_interface_lan" ] || [ "0" = "$SYSCFG_rip_interface_lan" ] ; then
      RIP_LAN_PREFIX="!"
   else
      RIP_LAN_PREFIX=
   fi
   echo "version 2" >> $RIPD_CONF_FILE
   echo "redistribute kernel" >> $RIPD_CONF_FILE
   echo "redistribute static" >> $RIPD_CONF_FILE
   echo "default-information originate" >> $RIPD_CONF_FILE
   echo "${RIP_WAN_PREFIX} network $WAN_IFNAME" >> $RIPD_CONF_FILE
   echo "network $SYSCFG_lan_ifname" >> $RIPD_CONF_FILE
   echo "!password admin" >> $RIPD_CONF_FILE
   echo "!enable password admin" >> $RIPD_CONF_FILE
   echo "!log file /tmp/ripd.log" >> $RIPD_CONF_FILE
   echo "!debug rip packet" >> $RIPD_CONF_FILE
   echo "!debug rip events" >> $RIPD_CONF_FILE
   if [ "" != "$SYSCFG_rip_md5_passwd" ] ; then
      echo "key chain utopia" >> $RIPD_CONF_FILE
      echo "  key 1" >> $RIPD_CONF_FILE
      echo "     key-string $SYSCFG_rip_md5_passwd" >> $RIPD_CONF_FILE
   fi
   SPLIT_HORIZON=`syscfg get rip_no_split_horizon`
   echo "${RIP_WAN_PREFIX} interface $WAN_IFNAME" >> $RIPD_CONF_FILE
   echo "   ${RIP_WAN_PREFIX} ip rip send version 1 2" >> $RIPD_CONF_FILE 
   echo "   ${RIP_WAN_PREFIX} ip rip receive version 1 2" >> $RIPD_CONF_FILE 
   if [ "" != "$SYSCFG_rip_md5_passwd" ] ; then
      echo "   ${RIP_WAN_PREFIX} ip rip authentication mode md5" >> $RIPD_CONF_FILE
      echo "   ${RIP_WAN_PREFIX} ip rip authentication key-chain utopia" >> $RIPD_CONF_FILE
   else
      if [ "" != "$SYSCFG_rip_text_passwd" ]; then
         echo "   ${RIP_WAN_PREFIX} ip rip authentication string $SYSCFG_rip_text_passwd" >> $RIPD_CONF_FILE
         echo "   ${RIP_WAN_PREFIX} ip rip authentication mode text" >> $RIPD_CONF_FILE
      else
        echo "   ${RIP_WAN_PREFIX} no ip rip authentication mode md5" >> $RIPD_CONF_FILE
        echo "   ${RIP_WAN_PREFIX} no ip rip authentication mode text" >> $RIPD_CONF_FILE
      fi
   fi
   if [ "1" = "$SPLIT_HORIZON" ] ; then
      echo "   ${RIP_WAN_PREFIX} no ip rip split-horizon" >> $RIPD_CONF_FILE
   else
      echo "   ${RIP_WAN_PREFIX} ip rip split-horizon" >> $RIPD_CONF_FILE
   fi
   echo "${RIP_LAN_PREFIX} interface $SYSCFG_lan_ifname" >> $RIPD_CONF_FILE
   echo "   ${RIP_WAN_PREFIX} ip rip send version 1 2" >> $RIPD_CONF_FILE 
   echo "   ${RIP_WAN_PREFIX} ip rip receive version 1 2" >> $RIPD_CONF_FILE 
   echo "   ${RIP_LAN_PREFIX} no ip rip authentication mode text" >> $RIPD_CONF_FILE
   echo "   ${RIP_LAN_PREFIX} no ip rip authentication mode md5" >> $RIPD_CONF_FILE
   if [ "1" = "$SPLIT_HORIZON" ] ; then
      echo "   ${RIP_LAN_PREFIX} no ip rip split-horizon" >> $RIPD_CONF_FILE
   else
      echo "   ${RIP_LAN_PREFIX} ip rip split-horizon" >> $RIPD_CONF_FILE
   fi
}
do_stop_zebra() {
   if [ -f "$ZEBRA_PID_FILE" ] ; then
      kill -TERM `cat $ZEBRA_PID_FILE`
      rm -f $ZEBRA_PID_FILE
   fi
   echo -n > $ZEBRA_CONF_FILE
   ulog routed status "zebra stopped"
}
do_start_zebra() {
   if [ -f "$ZEBRA_PID_FILE" ] ; then
      do_stop_zebra
   fi
   make_zebra_conf_file
   $ZEBRA_BIN_NAME -d -f $ZEBRA_CONF_FILE 
   ulog routed status "zebra started"
}
do_stop_ripd() {
   if [ -f "$RIPD_PID_FILE" ] ; then
      kill -TERM `cat $RIPD_PID_FILE`
      rm -f $RIPD_PID_FILE
   fi
   echo -n > $RIPD_CONF_FILE
   ulog rip status "ripd stopped"
}
do_start_ripd() {
   if [ -f "$RIPD_PID_FILE" ] ; then
      do_stop_ripd
   fi
   make_ripd_conf_file
   $RIPD_BIN_NAME -d -f $RIPD_CONF_FILE 
   ulog rip status "ripd started"
}
calculate_services_to_start ()
{
   FW_RESTART_REQ=0
   ROUTED_REQ=0
   RIPD_REQ=0
   CURRENT_WAN_STATE=`sysevent get wan-status`
   CURRENT_LAN_STATE=`sysevent get lan-status`
   if [ "stopped" = "$CURRENT_WAN_STATE" ] && [ "stopped" = "$CURRENT_LAN_STATE" ] ; then
      return
   elif [ "stopping" = "$CURRENT_WAN_STATE" ] ; then
      return
   elif [ "stopping" = "$CURRENT_LAN_STATE" ] || [ "starting" = "$CURRENT_LAN_STATE" ] ; then
      return
   else
      ROUTED_REQ=1
   fi
   if [ "1" = "$SYSCFG_rip_enabled" ] ; then
      RIPD_REQ=1
      FW_RESTART_REQ=1
   fi
   if [ "" != "$SYSCFG_StaticRouteCount" ] && [ "0" != "$SYSCFG_StaticRouteCount" ] ; then
      FW_RESTART_REQ=1
   fi
}
start_all_required_services () 
{
   calculate_services_to_start
   if [ "1" = "$FW_RESTART_REQ" ] ; then
      sysevent set firewall-restart
   fi
   if [ "1" = "$ROUTED_REQ" ] ; then
      do_start_zebra
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status started
   fi
   if [ "1" = "$RIPD_REQ" ] ; then
      do_start_ripd
      sysevent set rip-errinfo
      sysevent set rip-status started
   fi
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      start_all_required_services
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      STATUS=`sysevent get rip-status`
      if [ "stopped" != "$STATUS" ] ; then
         do_stop_ripd
         sysevent set rip-errinfo
         sysevent set rip-status stopped
      fi
      do_stop_zebra
      sysevent set ${SERVICE_NAME}-status stopped
      sysevent set ${SERVICE_NAME}-errinfo
      start_all_required_services
   fi
}
service_init ()
{
   SYSCFG_FAILED='false'
   FOO=`utctx_cmd get hostname lan_ifname guest_enabled guest_lan_ifname router_adv_enable dhcpv6s_enable rip_enabled rip_interface_wan rip_interface_lan rip_md5_passwd get rip_text_passwd StaticRouteCount bridge_mode`
   eval $FOO
  if [ $SYSCFG_FAILED = 'true' ] ; then
     ulog routed status "$PID utctx failed to get some configuration data"
     exit
  fi
  if [ -z "$SYSCFG_hostname" ] ; then
      SYSCFG_hostname=`cat /etc/hostname`
  fi
}
service_init
if [ "$SYSCFG_bridge_mode" != "0" ] ; then
    exit 
fi
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
   wan-status)
      service_stop
      service_start
      ;;
   lan-status)
      service_stop
      service_start
      ;;
   ripd-restart)
      service_stop
      service_start
      ;;
   staticroute-restart)
      service_stop
      service_start
      ;;
   ipv6_nameserver)
      service_stop
      service_start
      ;;
   br0_ipv6_prefix)
      service_stop
      service_start
      ;;
   br0_ula_prefix)
      service_stop
      service_start
      ;;
   br1_ipv6_prefix)
      service_stop
      service_start
      ;;
   previous_br0_ipv6_prefix)
      service_stop
      service_start
      ;;
   previous_br1_ipv6_prefix)
      service_stop
      service_start
      ;;
   cron_handler)
      cron_handler $2
      ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
