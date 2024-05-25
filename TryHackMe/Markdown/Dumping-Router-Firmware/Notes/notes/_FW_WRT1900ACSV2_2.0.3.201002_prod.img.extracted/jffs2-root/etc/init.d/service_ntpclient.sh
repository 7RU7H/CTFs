#!/bin/sh
exec 2> /dev/null 
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="ntpclient"
SELF_NAME="`basename $0`"
RETRY_SOON_FILENAME=/etc/cron/cron.everyminute/ntp_retry_soon.sh
SYSCFG_ntp_enabled=`syscfg get ntp_enabled`
prepare_retry_soon_file()
{
   echo "#! /bin/sh" > $RETRY_SOON_FILENAME;
   if [ "started" = "`sysevent get ${SERVICE_NAME}-status`" ] ; then
      echo "   sysevent set ntpclient-check" >> $RETRY_SOON_FILENAME;
   else
      echo "   sysevent set ntpclient-start" >> $RETRY_SOON_FILENAME;
   fi
   chmod 700 $RETRY_SOON_FILENAME;
}
contact_one_server ()
{
   INDEX_DHCP=1
   INDEX_STATIC=1
   while [ $INDEX_DHCP -le 3 ]; do
      NTP_SERVER=`sysevent get dhcpc_ntp_server$INDEX_DHCP`
      if [ "" = "$NTP_SERVER" -o "0.0.0.0" = "$NTP_SERVER" ]; then
         INDEX_DHCP=`expr $INDEX_DHCP + 1`
      else
         RESULT=`ntpclient -h $NTP_SERVER -i 10 -s`
         if [ "" = "$RESULT" ]; then
            INDEX_DHCP=`expr $INDEX_DHCP + 1`
         else
            `sysevent set ntp_pool_index $INDEX_DHCP`
            return 0
         fi
      fi
   done
   while [ $INDEX_STATIC -le 3 ]; do
      NTP_SERVER=`syscfg get ntp_server$INDEX_STATIC`
      if [ "" = "$NTP_SERVER" -o "0.0.0.0" = "$NTP_SERVER" ]; then
         INDEX_STATIC=`expr $INDEX_STATIC + 1`
      else
         RESULT=`ntpclient -h $NTP_SERVER -i 10 -s`
         if [ "" = "$RESULT" ]; then
            INDEX_STATIC=`expr $INDEX_STATIC + 1`
         else
            `sysevent set ntp_pool_index $INDEX_STATIC`
            return 0
         fi
      fi
   done
}
service_check ()
{
   if [ -n "$SYSCFG_ntp_enabled" ] && [ "0" = "$SYSCFG_ntp_enabled" ] ; then
      return 0
   fi
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      ulog ${SERVICE_NAME} status "check requested but ${SERVICE_NAME}-status is $STATUS" 
      return 0
   fi
   if [ "started" != "$CURRENT_WAN_STATUS" ] ; then
      ulog ${SERVICE_NAME} status "check requested but wan is down" 
      return 0
   fi
   if [ -f "$RETRY_SOON_FILENAME" ] ; then
      rm -f $RETRY_SOON_FILENAME;
   fi
   last_time="`date -u +%s`"
   contact_one_server
   
   if [ "" = "$RESULT" ] ; then
      if  [ ! -f "$RETRY_SOON_FILENAME" ] ; then
         NUM_ERRORS=`sysevent get ntpclient_num_errors`
         if [ -z "$NUM_ERRORS" ] ; then
            NUM_ERRORS=0
         fi
         if [ "started" = "`sysevent get ${SERVICE_NAME}-status`" -a "12" -ge "$NUM_ERRORS" ] ; then
            sysevent set ntpclient_num_errors=`expr $NUM_ERRORS + 1`
            ulog ${SERVICE_NAME} status "Unable to connect to NTP Server $NTP_SERVER. Ignoring." 
            return 0
         fi 
         sysevent set ntpclient_num_errors=`expr $NUM_ERRORS + 1`
         prepare_retry_soon_file
         sysevent set ${SERVICE_NAME}-status "error"
         sysevent set ${SERVICE_NAME}-errinfo "No result from NTP Server"
         ulog ${SERVICE_NAME} status "Unable to connect to NTP Server $NTP_SERVER. Retrying soon." 
         return 0
      else
         ulog ${SERVICE_NAME} status "Unable to connect to NTP Server $NTP_SERVER. Retry already scheduled" 
      fi
   else
      sysevent set ntpclient_num_errors=0
   fi
   sysevent set ecosystem_date `date -u +%Y%m%d%H%M.%S`
   syscfg set last_known_date `date -u +%Y%m%d%H%M`
   export TZ=$SYSCFG_TZ
   A=`date +%z`; B=`echo ${A:0:3}`; C=`echo ${A:3:2}`; Z=`echo $B:$C`
   syscfg set first_use_date `date +%FT%T$Z`
   if [ -n "$last_time" ] 
   then
      current_time=`date -u +%s`
      time_delta=`expr $current_time - $last_time`
      if [ "5400" -lt "$time_delta" ]
      then
         ulog ${SERVICE_NAME} status "Time skew is over 1 hour. Restarting wan" 
         syscfg commit
         sysevent set ${SERVICE_NAME}-status "stopped"
         sysevent set dhcp_client-restart
         sysevent set dhcpv6_client-restart
         sysevent set mcastproxy-restart
         sysevent set ${SERVICE_NAME}-status "started"
      fi
   fi
   if [ -n "$SYSCFG_TZ" ] ; then
      sysevent set TZ $SYSCFG_TZ
      sed  's%export setenv TZ=.*%export setenv TZ="'"$SYSCFG_TZ"'"%' /etc/profile > /tmp/ntpclient_profile
      cat /tmp/ntpclient_profile > /etc/profile
      rm -f /tmp/ntpclient_profile
   fi
}
service_start ()
{
   if [ -n "$SYSCFG_ntp_enabled" ] && [ "0" = "$SYSCFG_ntp_enabled" ] ; then
      return 0
   fi
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status "starting"
   fi
   if [ "started" != "$CURRENT_WAN_STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-status "error"
      sysevent set ${SERVICE_NAME}-errinfo "wan connection down"
      ulog ${SERVICE_NAME} status "error starting ${SERVICE_NAME} service - wan down" 
      return 0
   fi
   if [ -f "$RETRY_SOON_FILENAME" ] ; then
      rm -f $RETRY_SOON_FILENAME;
   fi
   last_time="`date -u +%s`"
   contact_one_server
   if [ "" = "$RESULT" ] && [ ! -f "$RETRY_SOON_FILENAME" ] ; then
      prepare_retry_soon_file
      sysevent set ${SERVICE_NAME}-status "error"
      sysevent set ${SERVICE_NAME}-errinfo "No result from NTP Server"
      ulog ${SERVICE_NAME} status "Unable to connect to NTP Server $NTP_SERVER" 
      return 0
   fi
   sysevent set ecosystem_date `date -u +%Y%m%d%H%M.%S`
   syscfg set last_known_date `date -u +%Y%m%d%H%M`
   export TZ=$SYSCFG_TZ
   A=`date +%z`; B=`echo ${A:0:3}`; C=`echo ${A:3:2}`; Z=`echo $B:$C`
   syscfg set first_use_date `date +%FT%T$Z`
   if [ -n "$last_time" ] 
   then
      current_time=`date -u +%s`
      time_delta=`expr $current_time - $last_time`
      if [ "5400" -lt "$time_delta" ]
      then
         ulog ${SERVICE_NAME} status "Time skew is over 1 hour. Restarting wan" 
         syscfg commit
         sysevent set dhcp_client-restart
         sysevent set dhcpv6_client-restart
         sysevent set mcastproxy-restart
      fi
   fi
   ulog ${SERVICE_NAME} status "updated time" 
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
   if [ -n "$SYSCFG_TZ" ] ; then
      sysevent set TZ $SYSCFG_TZ
      sed  's%export setenv TZ=.*%export setenv TZ="'"$SYSCFG_TZ"'"%' /etc/profile > /tmp/ntpclient_profile
      cat /tmp/ntpclient_profile > /etc/profile
      rm -f /tmp/ntpclient_profile
   fi
}
service_stop ()
{
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_init ()
{
    FOO=`utctx_cmd get ntp_server1 ntp_server2 ntp_server3 ntp_enabled TZ`
    eval $FOO
}
service_init
CURRENT_WAN_STATUS=`sysevent get wan-status`
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
  ${SERVICE_NAME}-check)
      service_check
      ;;
  wan-status)
      if [ "started" = "$CURRENT_WAN_STATUS" ] ; then
         service_start
      fi
      if [ "stopped" = "$CURRENT_WAN_STATUS" ] ; then
         service_stop
      fi
      ;;
  *)
      echo "Usage: $SELF_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart | ${SERVICE_NAME}-check | wan-status ]" >&2
      exit 3
      ;;
esac
