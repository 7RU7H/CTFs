#!/bin/sh
source /etc/init.d/date_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="ddns"
DDNS_TMP_DIR=/tmp/ddns
DDNS_PERSIST_DIR=/tmp/var/config/ddns
CONF_FILE=${DDNS_TMP_DIR}/ddns_update.conf
OUTPUT_FILE=${DDNS_TMP_DIR}/ddns_update.out
RETRY_1_FILENAME="/etc/cron/cron.everyminute/ddns_retry_soon.sh"
RETRY_5_FILENAME="/etc/cron/cron.every5minute/ddns_retry_soon.sh"
RETRY_10_FILENAME="/etc/cron/cron.every10minute/ddns_retry_soon.sh"
RETRY_30_FILENAME="/etc/cron/cron.every30minute/ddns_retry_soon.sh"
TZO_POLL_FILENAME="/etc/cron/cron.every5minute/tzo_polling.sh"
TZO_POLL_1_FILENAME="/etc/cron/cron.everyminute/tzo_polling.sh"
PID="($$)"
CACHE_FILE_P=${DDNS_PERSIST_DIR}/ddns_update.cache
CACHE_FILE=${DDNS_TMP_DIR}/ddns_update.cache
TZO_CACHE_P=${CACHE_FILE_P}.tzo
TZO_CACHE=${CACHE_FILE}.tzo
NOIP_CACHE_P=${CACHE_FILE_P}.noip
NOIP_CACHE=${CACHE_FILE}.noip
WAN_IFNAME=`sysevent get current_wan_ifname`
G_ddns_last_update=`sysevent get ddns_last_update`
WAN_PROTO=`syscfg get wan_proto`
EFFECTIVE_WAN_IPADDR=
THE_ACTION=$1
clear_all_retry_soon()
{
   rm -f $RETRY_1_FILENAME
   rm -f $RETRY_5_FILENAME
   rm -f $RETRY_10_FILENAME
   rm -f $RETRY_30_FILENAME
}
set_retry_soon ()
{
   MIN=$1
   RETRY_SOON_FILENAME=$RETRY_1_FILENAME
   
   ulog ddns status "$PID need to re-try in ($MIN)"
   if [ -n "$MIN" ]; then
   
      TO_WAIT=`expr $MIN / 5`
      
      if [ $TO_WAIT -gt 0 ]; then
      
         TO_WAIT=`expr $MIN / 30`
         if [ $TO_WAIT -gt 0 ]; then
            RETRY_SOON_FILENAME=$RETRY_30_FILENAME
         else 
            TO_WAIT=`expr $MIN / 10`
            if [ $TO_WAIT -gt 0 ]; then
               RETRY_SOON_FILENAME=$RETRY_10_FILENAME
            else
               RETRY_SOON_FILENAME=$RETRY_5_FILENAME  
            fi
         fi
      fi
   fi   
   ulog ddns status "$PID the cron job to use ($RETRY_SOON_FILENAME)"
   echo "#! /bin/sh" > $RETRY_SOON_FILENAME;
   echo "   source /etc/init.d/ulog_functions.sh" >> $RETRY_SOON_FILENAME;
   echo "   ulog ddns status \"Retrying ddns-start (Ref:$PID)\"" >> $RETRY_SOON_FILENAME;
   echo "   rm -f $RETRY_SOON_FILENAME" >> $RETRY_SOON_FILENAME;
   echo "   sysevent set ddns-start" >> $RETRY_SOON_FILENAME;
   chmod 700 $RETRY_SOON_FILENAME;
}
set_retry_later()
{
   MIN=$1
   MatchMin="0"
   NowMin=`date +%M`
   RETRY_SOON_FILENAME=$RETRY_1_FILENAME
   
   ulog ddns status "$PID need to re-try in ($MIN)"
   if [ -n "$MIN" ]; then
	if [ ${MIN} -gt "58" ];then
		return
	fi
	MatchMin=`expr ${NowMin}  + 1 + ${MIN}`
	if [ ${MatchMin} -ge "60" ]; then
		MatchMin=`expr ${MatchMin} - 60 `
	fi   
   else
	return
   fi   
   ulog ddns status "$PID the cron job to use ($RETRY_SOON_FILENAME), at min=${MatchMin}"
   echo "#! /bin/sh" > $RETRY_SOON_FILENAME;
   echo "   source /etc/init.d/ulog_functions.sh" >> $RETRY_SOON_FILENAME;
   echo "   if [ \`date +%M\` != ${MatchMin} ] ; then " >> $RETRY_SOON_FILENAME;
   echo "   	exit " >> $RETRY_SOON_FILENAME;
   echo "   fi " >> $RETRY_SOON_FILENAME;
   echo "   ulog ddns status \"Retrying ddns-start (Ref:$PID)\"" >> $RETRY_SOON_FILENAME;
   echo "   rm -f $RETRY_SOON_FILENAME" >> $RETRY_SOON_FILENAME;
   echo "   sysevent set ddns-start" >> $RETRY_SOON_FILENAME;
   chmod 700 $RETRY_SOON_FILENAME;
}
    
wait_for_wan_ifname()
{
   TO_WAIT=1
   while [ $TO_WAIT -gt 0 ]; do
      sleep $TO_WAIT
      WAN_IFNAME=`sysevent get current_wan_ifname`
      if [ -z $WAN_IFNAME ]; then
         TO_WAIT=`expr $TO_WAIT + 1`
         TO_WAIT=`expr $TO_WAIT % 5`
      else
         TO_WAIT=0
         ulog ddns status "$PID wan interface name is set: $WAN_IFNAME"
      fi
   done
}
            
make_tzo_conf_file()
{
   LOCAL_CONF_FILE=$1
   
   MODEL=`syscfg get device::modelNumber`
   HW_REV=`syscfg get device::hw_revision`
   FW_VERSION=`syscfg get fwup_firmware_version`
   
   cat << EOM > $LOCAL_CONF_FILE
PARTNER=linksys
AGENT=${MODEL}, Rev${HW_REV}/${FW_VERSION}
KEY=$SYSCFG_ddns_password
DOMAIN=$SYSCFG_ddns_hostname
EMAIL=$SYSCFG_ddns_username
PORT=80
IPFILE=$TZO_CACHE
EOM
}
make_ez_ipupdate_conf_file()
{
   LOCAL_CONF_FILE=$1
   DYNDNS_ADDR=$2
   
   MODEL=`syscfg get device::modelNumber`
   HW_REV=`syscfg get device::hw_revision`
   FW_VERSION=`syscfg get fwup_firmware_version`
   
   echo "service-type=$SYSCFG_ddns_service" > $LOCAL_CONF_FILE
   echo "user=${SYSCFG_ddns_username}:${SYSCFG_ddns_password}" >> $LOCAL_CONF_FILE
   echo "host=${SYSCFG_ddns_hostname}" >> $LOCAL_CONF_FILE
   echo "interface=$WAN_IFNAME" >> $LOCAL_CONF_FILE
   echo "agent=$MODEL" >> $LOCAL_CONF_FILE
   echo "address=$DYNDNS_ADDR" >> $LOCAL_CONF_FILE
   echo "max-interval=2073600" >> $LOCAL_CONF_FILE
   echo "cache-file=$CACHE_FILE" >> $LOCAL_CONF_FILE
   echo "retrys=1" >> $LOCAL_CONF_FILE
   if [ "" != "$SYSCFG_ddns_mx" ]; then
      echo "mx=$SYSCFG_ddns_mx" >> $LOCAL_CONF_FILE
      if [ "1" = "$SYSCFG_ddns_mx_backup" ] ; then
         echo "backmx=YES" >> $LOCAL_CONF_FILE
      fi
   fi
   if [ "1" = "$SYSCFG_ddns_wildcard" ] ; then
      echo "wildcard" >> $LOCAL_CONF_FILE
   fi
}
make_noip_conf_file()
{
   LOCAL_CONF_FILE=$1
   
   cat << EOM > $LOCAL_CONF_FILE
USERNAME=$SYSCFG_ddns_username
PASSWORD=$SYSCFG_ddns_password
HOSTNAME=$SYSCFG_ddns_hostname
EOM
}
ddns_state_persist()
{
   ulog ddns status "$PID told to persist the ddns state"
   NEED_TO_PERSIST=0
   if [ -f $CACHE_FILE ]; then
      PERSISTED=`cat $CACHE_FILE_P 2>/dev/null | md5sum`
      CURRENT=`cat $CACHE_FILE 2>/dev/null | md5sum`
      
      if [ "$PERSISTED" != "$CURRENT" ]; then
         ulog ddns status "$PID ez_ipupdate cache file changed $PERSISTED != $CURRENT"
         NEED_TO_PERSIST=1
      else
         ulog ddns status "$PID ez_ipupdate cache file didn't changed"
      fi
   fi
   if [ -f $TZO_CACHE ]; then
      PERSISTED=`cat $TZO_CACHE_P 2>/dev/null | md5sum`
      CURRENT=`cat $TZO_CACHE 2>/dev/null | md5sum`
      
      if [ "$PERSISTED" != "$CURRENT" ]; then
         ulog ddns status "$PID tzoupdate cache file changed $PERSISTED != $CURRENT"
         NEED_TO_PERSIST=1
      else
        ulog ddns status "$PID tzoupdate cache file didn't changed"
      fi   
   fi
   if [ -f $NOIP_CACHE ]; then
      PERSISTED=`cat $NOIP_CACHE_P 2>/dev/null | md5sum`
      CURRENT=`cat $NOIP_CACHE 2>/dev/null | md5sum`
      
      if [ "$PERSISTED" != "$CURRENT" ]; then
         ulog ddns status "$PID noip cache file changed $PERSISTED != $CURRENT"
         NEED_TO_PERSIST=1
      else
        ulog ddns status "$PID noip cache file didn't change"
      fi   
   fi
   if [ "$NEED_TO_PERSIST" == "1" ]; then
      if [ ! -d $DDNS_PERSIST_DIR ]; then
         mkdir $DDNS_PERSIST_DIR
      fi
      if [ -d $DDNS_TMP_DIR ]; then
         ulog ddns status "$PID persisting the ddns state"
         cp -pf ${DDNS_TMP_DIR}/*.cache* $DDNS_PERSIST_DIR
      else
         ulog ddns status "$PID no ddns state to persist"   
      fi
   fi   
}
get_effective_wan_ipaddr() 
{ 
   WAN_IPADDR="0.0.0.0"
   if [ "$WAN_PROTO" == "l2tp" ] || [ "$WAN_PROTO" == "pptp" ]; then 
      WAN_IPADDR=`sysevent get pppd_current_wan_ipaddr`
   else
      WAN_IPADDR=`sysevent get ipv4_wan_ipaddr`
   fi   
   
   if [ -n "$WAN_IPADDR" ] && [ "$WAN_IPADDR" != "0.0.0.0" ]; then  
      PPP_RETRY=0
      CURRENT_STATE=`sysevent get wan-status`
      if [ "started" != "$CURRENT_STATE" ]; then
         WAN_IPADDR="0.0.0.0"
         CURRENT_PPP_IPADDR=`sysevent get pppd_current_wan_ipaddr`    
         if [ X"$CURRENT_PPP_IPADDR" != X"10.64.64.64" ]; then
            ulog ddns status "$PID wan is not up just yet, let's wait."
            TO_WAIT=1
            while [ $TO_WAIT -gt 0 ]; do
               ulog ddns status "$PID waiting ($TO_WAIT)"
               sleep $TO_WAIT
               CURRENT_STATE=`sysevent get wan-status`
               if [ "$CURRENT_STATE" != "started" ]; then
                  TO_WAIT=`expr $TO_WAIT + 1`
                  TO_WAIT=`expr $TO_WAIT % 5`
               else
                  TO_WAIT=0
                  PPP_RETRY=1
                  ulog ddns status "$PID wan interface has started"
                  if [ "$WAN_PROTO" == "l2tp" ] || [ "$WAN_PROTO" == "pptp" ]; then 
                     WAN_IPADDR=`sysevent get pppd_current_wan_ipaddr`                    
                     ulog ddns status "$PID let's wait a second just in case underlying system has not switched yet to use new settings."
                     sleep 2
                  else
                     WAN_IPADDR=`sysevent get ipv4_wan_ipaddr`
                  fi   
               fi
            done
         fi
     fi
  fi   
  ulog ddns status "$PID effective wan_ipaddr($WAN_IPADDR)"
  echo "$WAN_IPADDR"
}
ddns_state_restore()
{
   DDNS_STATE_RESTORED=`sysevent get ddns_state_restored`
   if [ -z "$DDNS_STATE_RESTORED" ]; then
      ulog ddns status "$PID restoring the ddns state"
      if [ -d $DDNS_PERSIST_DIR ]; then
         ulog ddns status "$PID recovering the ddns state"
         cp -prf ${DDNS_PERSIST_DIR}/*.* $DDNS_TMP_DIR 
                
         if [ "$SYSCFG_ddns_service" == "tzo" ]; then
            ulog ddns status "$PID recovering the tzo conf file"
            make_tzo_conf_file $CONF_FILE
            sysevent set ddns_state_restored 1
         elif [ "$SYSCFG_ddns_service" == "noip" ]; then
            ulog ddns status "$PID recovering the noip conf file"
            make_noip_conf_file $CONF_FILE
            sysevent set ddns_state_restored 1
         else
            ddns_ipv4=`cat $CACHE_FILE | awk -F ',' '{print $2}'`
            if [ -z "$WAN_IFNAME" ]; then
               ulog ddns status "$PID wan_ifname is not set; let's wait"
               wait_for_wan_ifname
            fi
            if [ -n "$WAN_IFNAME" ]; then
               ulog ddns status "$PID recovering the dyndns conf file with ($ddns_ipv4)"
               make_ez_ipupdate_conf_file $CONF_FILE $ddns_ipv4
               sysevent set ddns_state_restored 1
            fi   
         fi   
      fi
   fi
}
stop_tzo_polling()
{
   ulog ddns status "$PID stopping TZO polling"
   rm -f $TZO_POLL_FILENAME
   rm -f $TZO_POLL_1_FILENAME
}
cancel_quiet_period()
{
   ulog ddns status "$PID cancel quiet period"
   syscfg unset ddns_failure_time
   clear_all_retry_soon
}
clear_ddns_status()
{
  ulog ddns status "$PID clear_ddns_status"
  sysevent set ddns_return_status
  sysevent set ${SERVICE_NAME}-errinfo
  sysevent set ddns_internet_ipv4  
}
clear_ddns_state()
{
   sysevent set ${SERVICE_NAME}-status
   sysevent set ${SERVICE_NAME}-errinfo
   
   sysevent set wan_last_ipaddr
   sysevent set ddns_last_update
   sysevent set ddns_internet_ipv4  
      
   cancel_quiet_period
   
}
reset_non_fatal_ddns_status()
{
   NEW_STATUS=$1
   NEW_INFO=$2
   ulog ddns status "$PID reset_non_fatal_ddns_status (${NEW_STATUS},${NEW_INFO})"
   PRIORERROR=`sysevent get ddns_return_status`
   case "$PRIORERROR" in
      error-hostname|error-auth|error-abuse)
      ;;
      *)
         sysevent set ddns_return_status "$NEW_STATUS"
         sysevent set ${SERVICE_NAME}-errinfo "$NEW_INFO"
         ;;
      esac
}
clear_connect_generic_error_only()
{
   PRIORERROR=`sysevent get ddns_return_status`
   case "$PRIORERROR" in
      error-connect|error|unknown)
         sysevent set ddns_return_status
         sysevent set ${SERVICE_NAME}-errinfo
         ;;
      esac
}
prepare_tzoupdate_config_file ()
{
  LOCAL_CONF_FILE=/tmp/tzo_update.conf$$
   
  make_tzo_conf_file $LOCAL_CONF_FILE
   
  IS_NEW=1
  if [ -e $CONF_FILE ] ; then
   cmp -s $LOCAL_CONF_FILE $CONF_FILE
   IS_NEW=$?
  fi 
  if [ "$IS_NEW" = "1" ] ; then 
     ulog ddns status "$PID tzo configuration changed removing $TZO_CACHE"
     rm -f $TZO_CACHE
     clear_ddns_status
     cat $LOCAL_CONF_FILE > $CONF_FILE
  fi
   
   rm -f $LOCAL_CONF_FILE       
   
   return $IS_NEW
}
prepare_noipupdate_config_file ()
{
   LOCAL_CONF_FILE=/tmp/noip_update.conf$$
   
   make_noip_conf_file $LOCAL_CONF_FILE
   
   IS_NEW=1
   if [ -e $CONF_FILE ] ; then
      cmp -s $LOCAL_CONF_FILE $CONF_FILE
      IS_NEW=$?
   fi 
   if [ "$IS_NEW" = "1" ] ; then 
      ulog ddns status "$PID noip configuration changed removing $NOIP_CACHE"
      rm -f $NOIP_CACHE
      clear_ddns_status
      cat $LOCAL_CONF_FILE > $CONF_FILE
   fi
   
   rm -f $LOCAL_CONF_FILE       
   
   return $IS_NEW
}
create_tzo_poll_handler()
{
   LOCAL_FILE=/tmp/tzo_poll.sh$$
   TIMEOUT=$1   
   TARGET=$2  
   echo "#! /bin/sh" > $LOCAL_FILE;
   echo "source /etc/init.d/date_functions.sh" >> $LOCAL_FILE;
   echo "SERVICE_NAME=\"ddns\"" >> $LOCAL_FILE;
   echo "PID=\"(\$\$)\"" >> $LOCAL_FILE;
   echo "LAST_POLL_TIME=\`sysevent get tzo_last_poll_time\`" >> $LOCAL_FILE;
   echo "if [ \"\" != \"\$LAST_POLL_TIME\" ] ; then" >> $LOCAL_FILE;
   echo "   NOW=\`get_current_time\`" >> $LOCAL_FILE;
   echo "   DELTA=\`delta_mins \$LAST_POLL_TIME \$NOW\`" >> $LOCAL_FILE;
   echo "   DELTA_DAYS=\`delta_days \$LAST_POLL_TIME \$NOW\`" >> $LOCAL_FILE;
   echo "   if [ \"\$DELTA_DAYS\" -gt \"0\" ]; then"  >> $LOCAL_FILE;
   echo "      X=\`expr \$DELTA_DAYS \\* 1440\`" >> $LOCAL_FILE;
   echo "      Y=\`expr \$DELTA + \$X\`" >> $LOCAL_FILE;
   echo "      DELTA=\$Y" >> $LOCAL_FILE;
   echo "   fi"  >> $LOCAL_FILE;
   echo "   if [ \"$TIMEOUT\" -gt \"\$DELTA\" ] ; then" >> $LOCAL_FILE;
   echo "      # too soon to poll" >> $LOCAL_FILE;
   echo "      exit 0" >> $LOCAL_FILE;
   echo "   fi" >> $LOCAL_FILE;
   echo "fi" >> $LOCAL_FILE;
   echo "# time to poll" >> $LOCAL_FILE;
   echo "sleep 1" >> $LOCAL_FILE;
   echo "ulog ddns status \"${SERVICE_NAME} \${PID} firing tzo polling\"" >> $LOCAL_FILE;
   echo "sysevent set ${SERVICE_NAME}-status" >> $LOCAL_FILE;
   echo "sysevent set ${SERVICE_NAME}-errinfo" >> $LOCAL_FILE;
   echo "sysevent set ddns_return_status" >> $LOCAL_FILE;
   echo "syscfg unset ddns_failure_time" >> $LOCAL_FILE;
   echo "sysevent set wan_last_ipaddr" >> $LOCAL_FILE;
   echo "sysevent set ddns_internet_ipv4" >> $LOCAL_FILE;
   echo "sysevent set ddns-start" >> $LOCAL_FILE;
   cat $LOCAL_FILE > $TARGET 
   rm -f $LOCAL_FILE   
   
   chmod 700 $TARGET;
}
ipcheck_dyndns()
{
   TO_WAIT=30
   ulog ddns status "$PID ipcheck_dyndns ENTER"
   echo > $OUTPUT_FILE
   wget -q http://checkip.dyndns.com/ -O ${OUTPUT_FILE} &
   WGET_PID=$!
   
   while [ $TO_WAIT -gt 0 ]; do
      if ! kill -s CONT $WGET_PID ; then
         break
      fi
      sleep 1
      TO_WAIT=`expr $TO_WAIT - 1`
   done
      
   if [ ! -z "`ps | grep [c]heckip`" ]; then
      ulog ddns status "$PID killing sleeping wget ($WGET_PID)"
      kill -9 $WGET_PID &> /dev/null
   fi
   
   DynDnsAddr=`cat ${OUTPUT_FILE} | sed -e 's/.*Current IP Address: //'| sed -e 's/<.*//'`
   ulog ddns status "$PID ddns dyndns ipcheck got [${DynDnsAddr}]"
   if [ -z "$DynDnsAddr" ]; then
      ulog ddns status "ddns warning unexpected return from checkip.dyndns.com [`cat ${OUTPUT_FILE}`]"
      set_retry_later 10
      reset_non_fatal_ddns_status error-connect "Failed to retreive remote ip address from checkip.dyndns.com"
   fi
   echo ${DynDnsAddr}
}
prepare_ezipupdate_config_file () 
{
   DYNDNS_ADDR=$1
   stop_tzo_polling  
   LOCAL_CONF_FILE=/tmp/ez_ipupdate.conf$$
   
   make_ez_ipupdate_conf_file $LOCAL_CONF_FILE $DYNDNS_ADDR
   
  IS_NEW=1
  if [ -e $CONF_FILE ] ; then
   cmp -s $LOCAL_CONF_FILE $CONF_FILE
   IS_NEW=$?
  else
   ulog ddns status "$PID ddns nothing to compare"
  fi 
  if [ "$IS_NEW" == "1" ] ; then 
     ulog ddns status "$PID dyndns configuration changed removing $CACHE_FILE"
     rm -f $CACHE_FILE
     cancel_quiet_period
     clear_ddns_status
     
     cat $LOCAL_CONF_FILE > $CONF_FILE
  fi
     
   rm -f $LOCAL_CONF_FILE       
   return $IS_NEW
}
is_parameter_changed()
{
   RET_CODE=0
   if [ $SYSCFG_ddns_service = "tzo" ] ; then
      prepare_tzoupdate_config_file 
      RET_CODE=$?
   elif [ $SYSCFG_ddns_service = "noip" ] ; then
      prepare_noipupdate_config_file
      RET_CODE=$?
   else
      if [ "" = "$SYSCFG_ddns_service" ] ; then
      	SYSCFG_ddns_service=dyndns   
      fi
      sleep 3
      DYNDNS_ADDR=`ipcheck_dyndns`
      if [ -n "$DYNDNS_ADDR" ]; then
         prepare_ezipupdate_config_file "$DYNDNS_ADDR"
         RET_CODE=$?
      fi   
   fi
   return $RET_CODE
}
prepare_extra_commandline_params () 
{
   EXTRA_PARAMS=""
   if [ "" != "$SYSCFG_ddns_server" ] ; then
      EXTRA_PARAMS="$EXTRA_PARAMS --server $SYSCFG_ddns_server"
   fi
   echo $EXTRA_PARAMS
}
     
must_remain_quiet()
{
   MUST_WAIT=11
   LAST_FAIL_TIME=`syscfg get ddns_failure_time`
   if [ "" != "$LAST_FAIL_TIME" ] ; then
      NOW=`get_current_time`
      DELTA_DAYS=`delta_days $LAST_FAIL_TIME $NOW`
      DELTA=`delta_mins $LAST_FAIL_TIME $NOW`
      
      if [ "$DELTA_DAYS" -gt "0" ]; then
         X=`expr $DELTA_DAYS \* 1440`
         Y=`expr $DELTA + $X`
         DELTA=$Y
      fi
      ulog ddns status "$PID validating quiet period [$NOW, $LAST_FAIL_TIME, $DELTA, $DELTA_DAYS]"
      if [ -n "$DELTA" ] ; then
         if [ $DELTA -ge 0 ] ; then
           if [ $MUST_WAIT -gt $DELTA ] ; then
            ulog ddns status "$PID ddns update required but we are in a quiet period. Will retry later"
            sysevent set ${SERVICE_NAME}-errinfo "mandated quiet period"
            sysevent set ${SERVICE_NAME}-status error
            
            MUST_WAIT=`expr $MUST_WAIT - $DELTA`
            MUST_WAIT=`expr $MUST_WAIT + 1`
	    set_retry_later $MUST_WAIT
            
            return 1
           else 
            syscfg unset ddns_failure_time 
           fi
         else
           ulog ddns status "$PID ddns quiet period validation is ambiguous; overwrite."
           syscfg unset ddns_failure_time 
         fi
      fi 
   fi
   return 0
}
update_ddns_ez_ipupdate ()
{
   echo > $OUTPUT_FILE    
   EXTRA_PARAMS=`prepare_extra_commandline_params`
      
   must_remain_quiet
   RET_CODE=$?
   if [ "$RET_CODE" = "1" ] ; then
      return 2
   fi
          
   ez-ipupdate $EXTRA_PARAMS -q -c $CONF_FILE -e /etc/init.d/service_ddns/ddns_success.sh &> $OUTPUT_FILE
      
   RET_CODE=$?
   CURRENT_TIME=`get_current_time`
   ulog ddns status "$PID ez-ipupdate returned $RET_CODE @ $CURRENT_TIME"
      
   if [ "0" != "$RET_CODE" ]; then
      grep -q "error connecting" $OUTPUT_FILE
      if [ "0" = "$?" ]; then
         sysevent set ddns_return_status error-connect
         ulog ddns status "$PID ddns_return_status error-connect"
         sysevent set ${SERVICE_NAME}-errinfo "connection error"
         sysevent set ${SERVICE_NAME}-status error
	 set_retry_later 11
            
         return 2
      fi
      grep -q "authentication failure" $OUTPUT_FILE
      if [ "0" = "$?" ]; then
         syscfg set ddns_failure_time `get_current_time`
         sysevent set ddns_return_status error-auth
         ulog ddns status "$PID ddns_return_status error-auth"
         sysevent set ${SERVICE_NAME}-errinfo "authentication error"
         sysevent set ${SERVICE_NAME}-status error
            
            
         return 2
      fi
 
      egrep -q "invalid hostname|malformed hostname" $OUTPUT_FILE
      if [ "0" = "$?" ]; then
         syscfg set ddns_failure_time `get_current_time`
         sysevent set ddns_return_status error-hostname
         ulog ddns status "$PID ddns_return_status error; invalid host name"
         sysevent set ${SERVICE_NAME}-errinfo "invalid host name"
         sysevent set ${SERVICE_NAME}-status error
            
         return 2
      fi
      UNKNOWN_ERR=`cat ${OUTPUT_FILE}`
      ulog ddns status "$PID ddns update error $RET_CODE:$UNKNOWN_ERR"
      syscfg set ddns_failure_time `get_current_time`
      sysevent set ddns_return_status unknown
      sysevent set ${SERVICE_NAME}-errinfo "ddns error: $UNKNOWN_ERR"
      sysevent set ${SERVICE_NAME}-status error
      set_retry_later 11
         
      return 2
         
   else
      ddns_ipv4=`cat $CACHE_FILE | awk -F ',' '{print $2}'`
         
      ulog ddns status "$PID ddns_return_status success ($ddns_ipv4)"
      sysevent set ddns_internet_ipv4 $ddns_ipv4 
      sysevent set ddns_return_status success
      sysevent set ${SERVICE_NAME}-status started
         
      sysevent set wan_last_ipaddr "$EFFECTIVE_WAN_IPADDR"
      sysevent set ddns_last_update "$CURRENT_TIME"
                  
      return 0
  fi
}
schedule_tzo_polling()
{
   TIMEOUT=$1
   : ${TIMEOUT:=12}
   TARGET=$TZO_POLL_1_FILENAME
   rm -f $TARGET
   
   sysevent set tzo_last_poll_time `get_current_time`
   
   create_tzo_poll_handler $TIMEOUT $TARGET 
}
update_tzo_server ()
{
   CURRENT_TIME=`get_current_time`
   echo > $OUTPUT_FILE   
   tzoupdate -v -f ${CONF_FILE} > $OUTPUT_FILE
   RET_CODE=$?
   schedule_tzo_polling 12
   if [ "0" != "$RET_CODE" ]; then
      ulog ddns status "$PID tzoupdate returned $RET_CODE"
      if [ "$RET_CODE" = "1" ] ; then
         grep -q "TZO Server Error" $OUTPUT_FILE
         if [ "0" = "$?" ] ; then
            schedule_tzo_polling 11
            sysevent set ddns_return_status error-server
            ulog ddns status "$PID ddns_return_status error-server"
            sysevent set ${SERVICE_NAME}-errinfo "TZO Server maintenance"
            sysevent set ${SERVICE_NAME}-status error           
         else
            grep -q "TZO Error" $OUTPUT_FILE
            if [ "0" = "$?" ] ; then
               sysevent set ddns_return_status error-connect
               ulog ddns status "$PID ddns_return_status error-connect"
               sysevent set ${SERVICE_NAME}-errinfo "connection error"
               sysevent set ${SERVICE_NAME}-status error           
            else
               schedule_tzo_polling 2 
               
               sysevent set ddns_return_status error-abuse
               ulog ddns status "$PID ddns_return_status error abuse"
               sysevent set ${SERVICE_NAME}-errinfo "abuse warning, will retry after 1 min"
               sysevent set ${SERVICE_NAME}-status error
            fi
         fi   
         return 2
      fi
      if [ "$RET_CODE" = "2" ] ; then 
      
         sysevent set ddns_return_status error-auth
         ulog ddns status "$PID ddns_return_status error-auth"
         sysevent set ${SERVICE_NAME}-errinfo "TZO account expired warning"
         sysevent set ${SERVICE_NAME}-status error
         
         rm -f $TZO_CACHE
         stop_tzo_polling 
         return 1
      fi
      if [ "$RET_CODE" = "3" ] ; then 
      
         sysevent set ddns_return_status error-auth
         ulog ddns status "$PID ddns_return_status error-auth"
         sysevent set ${SERVICE_NAME}-errinfo "authentication error"
         sysevent set ${SERVICE_NAME}-status error
         rm -f $TZO_CACHE
         stop_tzo_polling
         
         return 1
      fi
      sysevent set ddns_return_status unknown
      UNKNOWN_ERR=`cat ${OUTPUT_FILE}`
      ulog ddns status "$PID tzoupdate unknown error:$UNKNOWN_ERR"
      sysevent set ${SERVICE_NAME}-errinfo "Minor/Usage error"
      sysevent set ${SERVICE_NAME}-status error
      rm -f $TZO_CACHE
               
      return 1
      
   else
      ddns_ipv4=`cat $TZO_CACHE`
      ulog ddns status "$PID ddns_return_status success ($ddns_ipv4)"
      sysevent set wan_last_ipaddr "$EFFECTIVE_WAN_IPADDR"
      sysevent set ddns_last_update "$CURRENT_TIME"
      sysevent set ddns_internet_ipv4 $ddns_ipv4 
      sysevent set ddns_return_status success              
      sysevent set ${SERVICE_NAME}-status started
      return 0
   fi     
}
update_noip_server ()
{
   CURRENT_TIME=`get_current_time`
    ascii_val=`printf "%x" "'@"`
    username=$(echo ${SYSCFG_ddns_username}|sed s/@/%$ascii_val/)
   local url="https://${username}:${SYSCFG_ddns_password}@dynupdate.no-ip.com/nic/update?hostname=${SYSCFG_ddns_hostname}&myip=$EFFECTIVE_WAN_IPADDR"
   MODEL=`syscfg get device::modelNumber`
   HW_REV=`syscfg get device::hw_revision`
   FW_VERSION=`syscfg get fwup_firmware_version`
   MANUFACTURER=`syscfg get device::manufacturer`
   USER_AGENT="${MANUFACTURER} ${MODEL}/${HW_REV} ddns@linksys.com"
   result=`curl -s --insecure --user-agent "${USER_AGENT}" $url` # The --insecure option allows the request without certificate validation
   RET_CODE=`echo $result | awk '{print $1}'`
   ulog ddns status "$PID noip-update returned $RET_CODE"
   if [ "good" != "$RET_CODE" ] && [ "nochg" != "$RET_CODE" ]; then
      if [ -z "$RET_CODE" ]; then	# a curl error occurred -- most likely a connection problem
         sysevent set ddns_return_status error-connect
         ulog ddns status "$PID ddns_return_status error-connect"
         sysevent set ${SERVICE_NAME}-errinfo "connection error"
         sysevent set ${SERVICE_NAME}-status error
         set_retry_later 11
         return 2
      fi
      if [ "nohost" = "$RET_CODE" ]; then
         sysevent set ddns_return_status error-hostname
         ulog ddns status "$PID ddns_return_status error; invalid host name"
         sysevent set ${SERVICE_NAME}-errinfo "invalid host name"
         sysevent set ${SERVICE_NAME}-status error
         rm -f $NOIP_CACHE
            
         return 1
      fi
      if [ "badauth" = "$RET_CODE" ]; then
         sysevent set ddns_return_status error-auth
         ulog ddns status "$PID ddns_return_status error-auth"
         sysevent set ${SERVICE_NAME}-errinfo "authentication error"
         sysevent set ${SERVICE_NAME}-status error
         rm -f $NOIP_CACHE
            
         return 1
      fi
      if [ "badagent" = "$RET_CODE" ] ; then
         sysevent set ddns_return_status error-badagent
         ulog ddns status "$PID ddns_return_status error badagent"
         sysevent set ${SERVICE_NAME}-errinfo "Bad agent, client disabled"
         sysevent set ${SERVICE_NAME}-status error           
         rm -f $NOIP_CACHE
         return 1
      fi
      if [ "abuse" = "$RET_CODE" ] ; then
         sysevent set ddns_return_status error-abuse
         ulog ddns status "$PID ddns_return_status error abuse"
         sysevent set ${SERVICE_NAME}-errinfo "abuse error, stop sending updates"
         sysevent set ${SERVICE_NAME}-status error
         rm -f $NOIP_CACHE
         return 1
      fi
      if [ "911" = "$RET_CODE" ] ; then
         sysevent set ddns_return_status error-server
         ulog ddns status "$PID ddns_return_status error-server"
         sysevent set ${SERVICE_NAME}-errinfo "No-IP server error"
         sysevent set ${SERVICE_NAME}-status error           
         set_retry_later 31
         return 2
      fi
      if [ "!donator" = "$RET_CODE" ] ; then
         sysevent set ddns_return_status error-feature
         ulog ddns status "$PID ddns_return_status error-feature"
         sysevent set ${SERVICE_NAME}-errinfo "Feature not available"
         sysevent set ${SERVICE_NAME}-status error           
         rm -f $NOIP_CACHE
         return 1
      fi
      sysevent set ddns_return_status unknown
      ulog ddns status "$PID noip-update unknown error: $RET_CODE"
      sysevent set ${SERVICE_NAME}-errinfo "Unknown error"
      sysevent set ${SERVICE_NAME}-status error
      rm -f $NOIP_CACHE
               
      return 1
   else
      ddns_ipv4=`echo $result | awk '{ print $2 }'`
      echo $ddns_ipv4 > $NOIP_CACHE
      ulog ddns status "$PID ddns_return_status success ($ddns_ipv4)"
      sysevent set wan_last_ipaddr "$EFFECTIVE_WAN_IPADDR"
      sysevent set ddns_last_update "$CURRENT_TIME"
      sysevent set ddns_internet_ipv4 $ddns_ipv4 
      sysevent set ddns_return_status success              
      sysevent set ${SERVICE_NAME}-status started
   fi 
}
update_ddns_server () 
{
   RET_CODE=
   if [ $SYSCFG_ddns_service = "tzo" ] ; then
      update_tzo_server
      RET_CODE=$?
   elif [ $SYSCFG_ddns_service = "noip" ] ; then
      update_noip_server
      RET_CODE=$?
   else
      if [ "" = "$SYSCFG_ddns_service" ] ; then
         SYSCFG_ddns_service=dyndns   
      fi
      update_ddns_ez_ipupdate
      RET_CODE=$?
   fi
   return $RET_CODE
}
do_start () 
{
   UPDATE_NEEDED=$1
   ulog ddns status "do_start ($UPDATE_NEEDED)"
   G_wan_last_ipaddr=`sysevent get wan_last_ipaddr`
   if [ -z $G_wan_last_ipaddr ] ; then
      WAN_LAST_IPADDR=0.0.0.0      
   else
      WAN_LAST_IPADDR=$G_wan_last_ipaddr
   fi
   
   if [ "0.0.0.0" = "$EFFECTIVE_WAN_IPADDR" ] ; then
      ulog ddns status "$PID effective wan ip addr is 0.0.0.0"
      return 0
   fi
   ulog ddns status "$PID ddns do_start: effective wan_ipaddr=$EFFECTIVE_WAN_IPADDR wan_last_ipaddr=$WAN_LAST_IPADDR"
   if [ "0.0.0.0" = "$WAN_LAST_IPADDR" ] ; then
      UPDATE_NEEDED=1
      ulog ddns status "$PID ddns update required due to no previous update"
   elif [ "$WAN_LAST_IPADDR" !=  "$EFFECTIVE_WAN_IPADDR" ] ; then  
        UPDATE_NEEDED=1
        ulog ddns status "$PID ddns update required due change in wan ip address from $WAN_LAST_IPADDR to $EFFECTIVE_WAN_IPADDR"
   fi
   if [ $SYSCFG_ddns_service = "tzo" ] ; then
         UPDATE_NEEDED=1
   fi
   if [ "0" = "$UPDATE_NEEDED" ] ; then
      if [ "" = "$G_ddns_last_update" ] || [ "0" = "$G_ddns_last_update" ] ; then
         UPDATE_NEEDED=1
         ulog ddns status "$PID ddns update required due to no previous update on record"
      else
         sleep 5
         CURRENT_TIME=`get_current_time`
         DELTA_DAYS=`delta_days $G_ddns_last_update  $CURRENT_TIME`
         DIFF=`expr $SYSCFG_ddns_update_days - $DELTA_DAYS`
         if [ "$DIFF" -le 0 ] ; then
            UPDATE_NEEDED=1
            ulog ddns status "$PID ddns update required due to no update in $SYSCFG_ddns_update_days days"
         fi
      fi
   fi 
   
   if [ "1" = "$UPDATE_NEEDED" ] ; then
      update_ddns_server
      
      RET_CODE=$?
      if [ "$RET_CODE" != "0" ]; then
         ulog ddns status "$PID clear sysevent wan_last_ipaddr"
         sysevent set wan_last_ipaddr
      fi
      case $RET_CODE in 
         0)
         ddns_state_persist
         ;;        
      esac
   else
      ulog ddns status "$PID No ddns update is required at this time"
      sysevent set ddns_return_status success
      sysevent set ${SERVICE_NAME}-errinfo
      sysevent set ${SERVICE_NAME}-status started
   fi
}
update_ddns_if_needed () 
{
   EFFECTIVE_WAN_IPADDR=`get_effective_wan_ipaddr`
   PRIORERROR=
   
   case "$EFFECTIVE_WAN_IPADDR" in
      0.0.0.0)
        ulog ddns status "$PID wan state is down. No ddns update possible"
        sysevent set ${SERVICE_NAME}-errinfo "wan is down. No update possible"
       
        reset_non_fatal_ddns_status error-connect "wan interface is down"
        sysevent set ${SERVICE_NAME}-status error
        stop_tzo_polling
        ;;
   *)
      ddns_state_restore
      clear_all_retry_soon
      
      if [ -z $WAN_IFNAME ]; then
            ulog ddns status "$PID wan interface is flacking ; need to wait a bit"
            wait_for_wan_ifname
      fi
      if [ -z $WAN_IFNAME ] || [ -z $EFFECTIVE_WAN_IPADDR ]; then
         ulog ddns status "$PID wan interface is still not set; will retry soon"
         set_retry_later 1
      else   
         is_parameter_changed
         flag=$?
         if [ "$flag" = "0" ]; then
            PRIORERROR=`sysevent get ddns_return_status`
         fi
         if [ "" = "$PRIORERROR" ] || 
            [ "success" = "$PRIORERROR" ] ||
            [ "error-server" = "$PRIORERROR" ] ||
            [ "error-connect" = "$PRIORERROR" ] ||
            [[ "error-abuse" = "$PRIORERROR" && "$SYSCFG_ddns_service" != "noip" ]] ; then
            sleep 5 
            ulog ddns status "$PID Effective wan ip address is [$EFFECTIVE_WAN_IPADDR]. Continuing"
            do_start $flag
          else
            ulog ddns status "$PID No ddns update due to prior ddns error (${PRIORERROR})"
            sysevent set ${SERVICE_NAME}-errinfo "No update possible due to prior ddns error (${PRIORERROR})"
            sysevent set ${SERVICE_NAME}-status error
          fi
      fi
      ;;
      
   esac
}
service_init ()
{
    if [ ! -d $DDNS_TMP_DIR ]; then
       mkdir $DDNS_TMP_DIR
    fi
    FOO=`utctx_cmd get ddns_enable ddns_update_days ddns_hostname ddns_username ddns_password ddns_service ddns_mx ddns_mx_backup ddns_wildcard ddns_server ddns_failure_time`
    eval $FOO
}
service_start () 
{
   if [ "0" != "$SYSCFG_ddns_enable" ] ; then
      clear_connect_generic_error_only           
      update_ddns_if_needed
   else
      ulog ddns status "$PID ddns is disabled clear last return and wan ipaddr"
      sysevent set ddns_return_status # to make sure we are not blocked by the previous error when enabled back
      sysevent set wan_last_ipaddr # to make sure we do check DDNS remote IP address when enabled back
   fi
}
service_stop ()
{
   clear_ddns_state
   stop_tzo_polling
   sysevent set ${SERVICE_NAME}-status stopped
}
service_init
ulog ddns status "$PID got an event $THE_ACTION"
case "$THE_ACTION" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop|wan-stopped)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      clear_ddns_status
      service_start
      ;;
   ipv4_wan_ipaddr)
      if [ "$WAN_PROTO" != "l2tp" ] && [ "$WAN_PROTO" != "pptp" ]; then 
         service_start
      fi   
      ;;
   pppd_current_wan_ipaddr)
     if [ "$WAN_PROTO" = "l2tp" ] || [ "$WAN_PROTO" = "pptp" ]; then 
      service_start
     fi   
     ;;
   ddns_state_persist)
      ddns_state_persist
     ;;
   ddns_state_restore)
     sysevent set ddns_state_restored
 
     ddns_state_restore
     ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
