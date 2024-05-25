#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="syslog"
SELF_NAME="`basename $0`"
service_start()
{
   killall syslogd > /dev/null 2>&1
   if [ "" = "$SYSCFG_log_level" ] ; then
       SYSCFG_log_level=1
   fi
   if [ "" = "$SYSCFG_log_remote" ] ; then
       SYSCFG_log_remote=0
   fi
   case "$SYSCFG_log_level" in
       0) SYSLOG_LEVEL=4 ;;
       1) SYSLOG_LEVEL=5 ;;
       2) SYSLOG_LEVEL=6 ;;
       3) SYSLOG_LEVEL=7 ;;
       *) SYSLOG_LEVEL=5 ;;
   esac
   BB_SYSLOG_LEVEL=`expr $SYSLOG_LEVEL + 1`
   if [ "0" != "$SYSCFG_log_remote" ] ; then
       /sbin/syslogd -l $BB_SYSLOG_LEVEL -L -R $SYSCFG_log_remote
   else
      /sbin/syslogd -l $BB_SYSLOG_LEVEL
   fi
   if [ "1" = "$USE_SYSEVENT" ] ; then
      sysevent set ${SERVICE_NAME}-status "started"
   fi
}
service_stop ()
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   killall syslogd > /dev/null 2>&1
   rm -f $TARGET
   if [ "1" = "$USE_SYSEVENT" ] ; then
      sysevent set ${SERVICE_NAME}-status "stopped"
   fi
}
pidof syseventd > /dev/null
if [ $? -eq 0 ] ; then
    USE_SYSEVENT=1
else
    USE_SYSEVENT=0
fi
service_init ()
{
    FOO=`utctx_cmd get log_level log_remote`
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
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
