#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SELF_NAME="$(basename $0)"
SERVICE_NAME="qos_auto_prioritize"
PRUNE_CRON_MIN_DIR=/etc/cron/cron.everyminute
PRUNE_CRON_DAY_DIR=/etc/cron/cron.daily
if [ "NULL" != "$2" ] ; then
    DEVICE_ID=$2
fi
PRUNE_CRON_FILE_ALL=${PRUNE_CRON_MIN_DIR}/qos_update_priorities.sh
PRUNE_CRON_FILE_DEVICE=${PRUNE_CRON_MIN_DIR}/qos_update_priorities_${DEVICE_ID}.sh
PRUNE_CRON_FILE_CHECK_TIMESTAMP=${PRUNE_CRON_DAY_DIR}/qos_check_timestamp.sh
if [ -n "$DEVICE_ID" ] ; then
    PRUNE_CRON_FILE=${PRUNE_CRON_FILE_DEVICE}
else
    PRUNE_CRON_FILE=${PRUNE_CRON_FILE_ALL}
fi
generate_cron_cron_file ()
{
    cat <<EOF
/etc/init.d/service_qos_auto_prioritize.sh qos_auto_prioritize-schedule_update ${DEVICE_ID}
EOF
}
generate_update_cron_file ()
{
    cat <<EOF
lua /etc/init.d/service_qos_auto_prioritize/qos_update_priorities.lua ${DEVICE_ID}
rm ${PRUNE_CRON_FILE}
EOF
}
generate_timestamp_cron_file ()
{
    cat <<EOF
/etc/init.d/service_qos_auto_prioritize.sh qos_auto_prioritize-check_timestamp
EOF
}
schedule_cron ()
{
    if [ ! -e ${PRUNE_CRON_FILE_ALL} -a ! -e ${PRUNE_CRON_FILE_DEVICE} ] ; then
        ulog ${SERVICE_NAME} status "scheduling cron for ${DEVICE_ID}"
        generate_cron_cron_file > ${PRUNE_CRON_FILE}
        chmod +x ${PRUNE_CRON_FILE}
    fi
}
clear_updates ()
{
    ulog ${SERVICE_NAME} status "clearing all pending device QoS priority updates"
    rm -f ${PRUNE_CRON_MIN_DIR}/qos_update_priorities*.sh
}
schedule_update ()
{
    if [ -z "$DEVICE_ID" ] ; then
        clear_updates
    fi
    ulog ${SERVICE_NAME} status "scheduling update for ${DEVICE_ID}"
    generate_update_cron_file > ${PRUNE_CRON_FILE}
    chmod +x ${PRUNE_CRON_FILE}
}
check_timestamp ()
{
    SYSCFG_FAILED='false'
    FOO=`utctx_cmd get qos_auto_prioritize_timestamp`
    eval $FOO
    if [ $SYSCFG_FAILED = 'true' ] ; then
        ulog lan status "$PID utctx_cmd failed to get qos_auto_prioritize_timestamp"
    else
        CURRENT_TIMESTAMP=`date +%s`
        if [ -z "$SYSCFG_qos_auto_prioritize_timestamp" ] || [ $SYSCFG_qos_auto_prioritize_timestamp -lt $CURRENT_TIMESTAMP ] ; then
            ulog ${SERVICE_NAME} status "deleting marked devices file"
            rm -rf /var/config/qos_marked_devices
            syscfg unset qos_auto_prioritize_timestamp
            syscfg commit
            schedule_cron
        fi
    fi
}
create_timestamp_cron ()
{
    check_timestamp
    if [ ! -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP} ] ; then
        ulog ${SERVICE_NAME} status "scheduling timestamp check"
        generate_timestamp_cron_file > ${PRUNE_CRON_FILE_CHECK_TIMESTAMP}
        chmod +x ${PRUNE_CRON_FILE_CHECK_TIMESTAMP}
    fi
}
service_restart ()
{
   clear_updates
   create_timestamp_cron
   schedule_cron
}
case "$1" in
  ${SERVICE_NAME}-start)
     create_timestamp_cron
     schedule_cron
     ;;
  ${SERVICE_NAME}-stop)
     clear_updates
     ;;
  ${SERVICE_NAME}-restart)
     service_restart
     ;;
  ${SERVICE_NAME}-schedule_update)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} update, triggered by $1"
     schedule_update
     ;;
  ${SERVICE_NAME}-check_timestamp)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} check timestamp, triggered by $1"
     check_timestamp
     ;;
  wan-started)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} cron, triggered by $1"
     create_timestamp_cron
     schedule_cron
     ;;
  devident_device_property_change)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} cron, triggered by $1"
     schedule_cron
     ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|${SERVICE_NAME}-schedule_update|${SERVICE_NAME}-check_timestamp]" >&2
      exit 3
      ;;
esac
