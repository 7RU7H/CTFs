#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SELF_NAME="$(basename $0)"
SERVICE_NAME="detect_crash"
user_consented=`syscfg get user_consented_data_upload`
upload_enabled=`syscfg get diagnostics::auto_upload_enabled`
data_types=$(syscfg get diagnostics::auto_upload_data_types)
sysup_file=/tmp/var/config/sysup
wait_til_started()
{
   local svcName=$1
   local i=0
   while [ $i -lt 5 ] && [ "$(sysevent get ${svcName}-status)" != "started" ]; do
      echo "${SERVICE_NAME}: waiting $svcName to start" >>/dev/console
      sleep 1
      i=`expr $i + 1`
   done
   if [ $i -eq 5 ]; then
      echo "${SERVICE_NAME}: $svcName never started" >>/dev/console
      false
   else
      true
   fi
}
mtd_for_panic=`syscfg get mtd.for.panic`
mtd_offs=`syscfg get mtd.for.panic.offset`
if [ -n "$mtd_for_panic" ]; then
   mtd_num=`cat /proc/mtd | grep $mtd_for_panic | awk -F ":" '{print $1}'`
fi
skip_count=`expr $mtd_offs / 512`
case "$1" in
   ${SERVICE_NAME}-start)
      ;;
   ${SERVICE_NAME}-stop)
      ;;
   ${SERVICE_NAME}-restart)
      ;;
   wan-started)
      if [ -e /tmp/panic ] && [ "$mtd_num" != "" ]; then
         if [ "$user_consented" == "1" ] && [ "$upload_enabled" == "1" ]; then
             if (echo $data_types | grep crashinfo >/dev/null) && (wait_til_started data_uploader); then
                /etc/init.d/service_data_uploader.sh upload_crash_log
             fi
         else
            echo "Cannot upload crash report -- no user consent, or not enabled" >> /dev/console
         fi
         flash_erase -q /dev/$mtd_num $mtd_offs 1
         dd if=/dev/$mtd_num of=/tmp/panic skip=$skip_count count=256 &>/dev/null
         md5sum /tmp/panic > /tmp/var/config/panic.md5
         rm /tmp/panic
      elif [ "$user_consented" == "1" ] && [ "$upload_enabled" == "1" ]; then
         status=$(sysevent get ${SERVICE_NAME}-status)
         if [ "$status" != "started" ] && [ "$(cat $sysup_file)" == "0" ]; then
            if (echo $data_types | grep sysinfo >/dev/null) && (wait_til_started data_uploader); then
               /etc/init.d/service_data_uploader.sh upload_sysinfo ABNORMAL_SHUTDOWN
            fi
         fi
      fi
      sysevent set ${SERVICE_NAME}-status "started"
      echo 0 > $sysup_file
      ;;
   *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|wan-started]" >&2
      exit 3
      ;;
esac
