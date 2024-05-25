#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="igd"
IGD_MONITOR_CRON_JOB="/etc/cron/cron.everyminute/igd_monitor.sh"
IGD_MONITOR_FILE="/tmp/igd_monitor.sh"
SELF_NAME="`basename $0`"
IGD=/usr/sbin/IGD
register_monitor (){
   WARN_AT=$1
   RESTART_AT=$2
   
   cat << EOM > $IGD_MONITOR_FILE
   #!/bin/sh
   source /etc/init.d/date_functions.sh
   source /etc/init.d/ulog_functions.sh
   PID="(\$\$)"
   TOP_OUT="\`top -b -n 1 | grep [I]GD\`"
   IGD_PID="\`echo \$TOP_OUT | awk '{print \$1}'\`"
   CPU_USED="\`echo \$TOP_OUT | awk '{print \$8}' | cut -d. -f1\`"
   
   if [ \$CPU_USED -gt $WARN_AT ]; then
      ARCHIVE_ID=\`date '+%m%d_%H-%M-%S'\`
      ulog igd warning "\$PID detected CPU ABUSE: \$CPU_USED on \$ARCHIVE_ID"
   
      if [ \$CPU_USED -gt $RESTART_AT ]; then
         ulog igd error "\$PID restarting the IGD service"
         sysevent set igd-restart
      fi 
   fi
EOM
   chmod 755 $IGD_MONITOR_FILE
   cp $IGD_MONITOR_FILE $IGD_MONITOR_CRON_JOB
}
unregister_monitor () {
    rm -f $IGD_MONITOR_CRON_JOB
}
stopigd()
{
   if [ -f /var/run/IGD.pid ]; then
	   kill `cat /var/run/IGD.pid`
	   for n in `seq 1 6`; do
		   kill -CONT `cat /var/run/IGD.pid` > /dev/null 2>&1 && sleep 1
	   done
   fi
   killall IGD > /dev/null 2>&1
   rm -f /var/run/IGD.pid
   rm -rf /var/IGD
   unregister_monitor 
}
service_start() {
   BRIDGEMODE=`syscfg get bridge_mode`
   if [ "1" = "$BRIDGEMODE" ] || [ "2" = "$BRIDGEMODE" ] ; then
      ulog ${SERVICE_NAME} status "${SERVICE_NAME} service should not start for bridge mode"
      exit
   fi
   if [ "started" = "`sysevent get igd-status`" ] ; then
      ulog ${SERVICE_NAME} status "${SERVICE_NAME} service already started"
      exit
   fi
   stopigd
   if [ "0" = "$SYSCFG_upnp_igd_enabled" ] ; then
      sysevent set ${SERVICE_NAME}-status disabled
      return 0
   fi
   ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
   mkdir -p /var/IGD
   (cd /var/IGD; ln -sf /etc/IGD/* .)
   ulimit -s 2048
	IGD_START_ONCE=`sysevent get igd_start_once`
	if [ "started" != "$IGD_START_ONCE" ]; then
		sleep 30
		sysevent set igd_start_once started
	fi
   $IGD &
   echo $! > /var/run/IGD.pid
   register_monitor 50 90
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
}
service_stop () {
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   stopigd
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_init() {
    FOO=`utctx_cmd get upnp_igd_enabled`
    eval $FOO
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
  lan-status)
      CURRENT_LAN_STATUS=`sysevent get lan-status`
      if [ "started" = "$CURRENT_LAN_STATUS" ] ; then
         service_start
      elif [ "stopped" = "$CURRENT_LAN_STATUS" ] ; then 
         service_stop
      fi
      ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|lan-status]" >&2
      exit 3
      ;;
esac
