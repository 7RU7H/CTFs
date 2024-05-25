#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="data_uploader"
SELF_NAME="`basename $0`"
SYSINFO_FILE=/tmp/sysinfo_$$.json
LOCKFILE=/tmp/${SERVICE_NAME}.lock
CERTS_ROOT=/etc/certs/root
CLOUD_REQ_TIMEOUT=10
CLOUD_LSWF_DOMAIN="https://$(syscfg get cloud::host)"
CLOUD_CONTENT_TYPE_HEADER="Content-Type:application/xml; charset=UTF-8"
CLOUD_ACCEPT_HEADER="Accept:application/xml"
CLOUD_CLIENT_TYPE_ID=AA296AC6-61D1-4D3F-83FA-96D7486A09CF
CLOUD_CLIENT_TYPE_HEADER_KEY=X-Linksys-Client-Type-Id
CLOUD_CLIENT_TYPE_HEADER="${CLOUD_CLIENT_TYPE_HEADER_KEY}:${CLOUD_CLIENT_TYPE_ID}"
CLOUD_ERROR_CODE=UNKNOWN
CLOUD_ERROR_DESC="Unknown error"
is_supported_upload_type()
{
   local supportedTypes="CRASH_LOG SYSINFO_LOG DIAG_DATA"
   for i in ${supportedTypes}; do
      if [ "$1" == "$i" ]; then
         return 0
      fi
   done
   return 1
}
is_device_registered()
{
   [ -n "$(syscfg get device::linksys_token)" ]
}
cloud_upload_files()
{
   local uploadType=$1
   local reason=$2
   local fileList=$(echo $3 | sed "s/,/ /g")
   local requestId=$4
   local emailTo=$5
   local serialNumber=$(syscfg get device::serial_number)
   local metaFile="/tmp/cloud_meta_$$.json"
   local output
   local rc=0
   if [ $# -lt 3 ]; then
      CLOUD_ERROR_CODE=INVALID_PARAMETER
      CLOUD_ERROR_DESC="Invalid function parameters."
      rc=3
   elif (! is_supported_upload_type $uploadType); then
      CLOUD_ERROR_CODE=INVALID_PARAMETER
      CLOUD_ERROR_DESC="Invalid upload type (CRASH_LOG|SYSINFO_LOG|DIAG_DATA)"
      rc=3
   elif [ "$(sysevent get data_uploader-status)" != "started" ]; then
      CLOUD_ERROR_CODE=REQUEST_FAILED
      CLOUD_ERROR_DESC="Data Uploader service is not started"
      rc=3
   else
      local metaData=$(cat << EOF
{
"serialNo": "$serialNumber",
"modelNo": "$(syscfg get device::modelNumber)",
"macAddress": "$(syscfg get device::mac_addr)",
"hardwareVersion": "$(syscfg get device::hw_revision)",
"firmwareVersion": "$(cat /etc/version)",
"uploadType": "$uploadType",
"uploadSubType": "$reason"
EOF
)
      if [ -n "$requestId" ] && [ "$requestId" != "NULL" ]; then
        metaData="${metaData},\n\"uploadRequestUUID\": \"$requestId\""
      fi
      if [ -n "$emailTo" ]; then
        metaData="${metaData},\n\"emailTo\": \"$emailTo\""
      fi
      printf "${metaData}\n}" > $metaFile
      local cmd=$(cat << EOF
         curl --capath ${CERTS_ROOT} -s --max-time $CLOUD_REQ_TIMEOUT
         -H "X-Linksys-Token:$(syscfg get device::linksys_token)"
         -H "X-Linksys-SN:$serialNumber"
         -H "${CLOUD_ACCEPT_HEADER}"
         -H "Content-Type:multipart/form-data; boundary=$(basename $metaFile)"
         -H "${CLOUD_CLIENT_TYPE_HEADER}"
         -X POST "${CLOUD_LSWF_DOMAIN}/storage-service/rest/deviceuploads?uploadType=DEVICE_LOG"
         -F "meta=@$metaFile"
EOF
)
      local i=0
      for file in $fileList
      do
         i=$(expr $i + 1)
         cmd="$cmd -F \"log${i}=@$file\""
      done
      echo "$SERVICE_NAME: Uploading files (${reason}): $fileList" >> /dev/console
      output=$(eval $cmd)
      rc=$?
      rm -f $metaFile
      if [ $rc -ne 0 ]; then
         CLOUD_ERROR_DESC="curl error ($rc)"
         CLOUD_ERROR_CODE=REQUEST_FAILED
      else
         local found=$(echo $output | grep -c "<error>")
         if [ $found -gt 0 ]; then
            CLOUD_ERROR_CODE=$(echo $output | cut -d'<' -f5 | sed "s/code>//g")
            CLOUD_ERROR_DESC=$(echo $output | cut -d'<' -f7 | sed "s/message>//g")
            rc=3
         else
            found=$(echo $output | grep "<deviceUploadId>")
            if [ "$found" ]; then
               local uploadId=$(echo $found | cut -d'<' -f4 | sed "s/deviceUploadId>//g")
               echo "$SERVICE_NAME: Upload complete, id=$uploadId" >> /dev/console
               echo $uploadId
            else  # unknown error
               rc=3
            fi
         fi
      fi
   fi
   if [ $rc -ne 0 ]; then
      echo "$SERVICE_NAME: $CLOUD_ERROR_CODE $CLOUD_ERROR_DESC" >> /dev/console
      echo "CLOUD_ERROR_CODE=$CLOUD_ERROR_CODE CLOUD_ERROR_DESC=\"$CLOUD_ERROR_DESC\""
   fi
   return $rc
}
configure_cron_jobs()
{
   local svcStatus=$(sysevent get ${SERVICE_NAME}-status)
   local newCronFile
   rm -f $(find /etc/cron/ -name upload_sysinfo.sh)
   if [ $svcStatus == "starting" ]; then
      local uploadEnabled=$(syscfg get diagnostics::sysinfo_periodic_upload_enabled)
      if [ "$uploadEnabled" == "1" ]; then
         local runInterval=$(syscfg get diagnostics::sysinfo_upload_interval)
         newCronFile="/etc/cron/cron.${runInterval}/upload_sysinfo.sh"
         ulog $SERVICE_NAME status "Creating new cron job for ${runInterval} sysinfo uploads"
         cat > $newCronFile << EOF
#!/bin/sh
$0 upload_sysinfo SCHEDULED "$(syscfg get diagnostics::sysinfo_sections)"
EOF
         chmod 700 $newCronFile
      fi
   fi
}
service_start()
{
   wait_till_end_state ${SERVICE_NAME}
   if [ "$(sysevent get ${SERVICE_NAME}-status)" == "started" ] || ! is_device_registered; then
      return
   fi
   sysevent set ${SERVICE_NAME}-status "starting"
   configure_cron_jobs
   echo "${SERVICE_NAME} service started" >> /dev/console
   ulog ${SERVICE_NAME} status "${SERVICE_NAME} service started"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "started"
}
service_stop()
{
   wait_till_end_state ${SERVICE_NAME}
   if [ "$(sysevent get ${SERVICE_NAME}-status)" == "stopped" ]; then
      return
   fi
   sysevent set ${SERVICE_NAME}-status "stopping"
   configure_cron_jobs
   echo "${SERVICE_NAME} service stopped" >> /dev/console
   ulog ${SERVICE_NAME} status "${SERVICE_NAME} service stopped"
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_restart()
{
   lock $LOCKFILE
   service_stop
   service_start
   unlock $LOCKFILE
}
upload_crash_log()
{
   local output
   local rc=0
   local symFile=/tmp/kallsyms_$$
   local uploadFiles=""
   local dataTypes=$(syscfg get diagnostics::auto_upload_data_types)
   if (echo $dataTypes | grep crashinfo > /dev/null); then
      cp /proc/kallsyms $symFile 2>/dev/null
      uploadFiles="$symFile,/tmp/panic"
   fi
   if (echo $dataTypes | grep sysinfo > /dev/null); then
      /www/sysinfo_json.cgi > $SYSINFO_FILE 2>/dev/null
      uploadFiles="$uploadFiles,$SYSINFO_FILE"
   fi
   ulog $SERVICE_NAME status "Uploading crash log"
   output=$(cloud_upload_files CRASH_LOG SYSTEM_CRASH $uploadFiles)
   rc=$?
   if [ $rc -ne 0 ]; then
      eval $output
      ulog $SERVICE_NAME error "$CLOUD_ERROR_CODE $CLOUD_ERROR_DESC"
   else
      ulog $SERVICE_NAME status "Crash log upload complete, id=$output"
      echo $output
   fi
   rm -f $SYSINFO_FILE
   rm -f $symFile
   return $rc
}
upload_sysinfo()
{
   local reason=$1
   local sections=$2
   local requestId=$3
   local output
   local rc=0
   ulog $SERVICE_NAME status "Uploading sysinfo $sections"
   if [ -z "$reason" ]; then
      reason=USER_REQUEST
   fi
   /www/sysinfo_json.cgi $sections > $SYSINFO_FILE 2>/dev/null
   output=$(cloud_upload_files SYSINFO_LOG $reason $SYSINFO_FILE $requestId)
   rc=$?
   if [ $rc -ne 0 ]; then
      eval $output
      ulog $SERVICE_NAME error "$CLOUD_ERROR_CODE $CLOUD_ERROR_DESC"
   else
      ulog $SERVICE_NAME status "Sysinfo upload successful, id=$output"
      echo $output
   fi
   rm -f $SYSINFO_FILE
   return $rc
}
email_sysinfo()
{
   local emailTo=$1
   local output
   local rc=0
   ulog $SERVICE_NAME status "uploading sysinfo for email to: $emailTo"
   cd /www && ./sysinfo.cgi > $SYSINFO_FILE 2>/dev/null
   output=$(cloud_upload_files DIAG_DATA USER_REQUEST $SYSINFO_FILE "" $emailTo)
   rc=$?
   if [ $rc -ne 0 ]; then
      eval $output
      ulog $SERVICE_NAME error "$CLOUD_ERROR_CODE $CLOUD_ERROR_DESC"
   else
      ulog $SERVICE_NAME status "Sysinfo upload successful, id=$output"
      echo $output
   fi
   rm -f $SYSINFO_FILE
   return $rc
}
ulog ${SERVICE_NAME} status "Received event: $1"
case "$1" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_restart
      ;;
   wan-started)
      service_start
      ;;
   wan-stopped)
      service_stop
      ;;
   device_registered)
      service_start
      ;;
   upload_crash_log)
      upload_crash_log
      ;;
   upload_sysinfo)
      upload_sysinfo $2 $3
      ;;
   email_sysinfo)
      email_sysinfo $2
      ;;
   sysinfo::upload)
      upload_sysinfo USER_REQUEST "" $2
      ;;
   upload_files)
      OUTPUT=$(cloud_upload_files $2 $3 $4)
      if [ $? -eq 0 ]; then
         echo $OUTPUT
      else
         eval $OUTPUT
         ulog $SERVICE_NAME error "$CLOUD_ERROR_CODE $CLOUD_ERROR_DESC"
         exit 3
      fi
      ;;
   user_consented_data_upload)
      service_restart
      ;;
   *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|wan-started|wan-stopped|device_registered|upload_crash_log|upload_sysinfo <reason> [<sections>] | email_sysinfo [<address-list>]" >&2
      exit 3
      ;;
esac
