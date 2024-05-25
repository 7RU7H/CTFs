#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SELF_NAME="$(basename $0)"
SERVICE_NAME="devidentd_download_devregex"
DEVREGEX_TMP_FILE=/tmp/downloaded_devregex.json
DEVREGEX_FILE=/tmp/var/config/devidentd/devregex.json
DEVREGEX_FILE_DIR=`dirname $DEVREGEX_FILE`
DEVIDENTD_CREATE_TIMESTAMP_FILE=/etc/init.d/service_devidentd_download_devregex/devidentd_create_timestamp.lua
PRUNE_CRON_MIN_DIR=/etc/cron/cron.everyminute
PRUNE_CRON_HOUR_DIR=/etc/cron/cron.hourly
PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE=${PRUNE_CRON_MIN_DIR}/devidentd_check_timestamp.sh
PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY=${PRUNE_CRON_HOUR_DIR}/devidentd_check_timestamp.sh
CERTS_ROOT=/etc/certs/root
CLOUD_HOST=`utctx_cmd get cloud::host`
eval $CLOUD_HOST
DEVREGEX_URL=https://$SYSCFG_cloud_host/ui/ustatic/devident/devregex_v2.json
DOWNLOAD_DEVREGEX_TIMESTAMP=devidentd_download_devregex_timestamp
generate_timestamp_cron_file ()
{
    cat <<EOF
/etc/init.d/service_devidentd_download_devregex.sh devidentd_download_devregex-check_timestamp
EOF
}
download_devregex ()
{
    eval `utctx_cmd get devidentd_download_devregex_disabled`
    if [ ! -z "$SYSCFG_devidentd_download_devregex_disabled" ] && [ $SYSCFG_devidentd_download_devregex_disabled == "true" ]; then
        ulog ${SERVICE_NAME} status "downloading devregex is disabled."
    else
        eval `utctx_cmd get devidentd_download_devregex_url`
        if [ ! -z "$SYSCFG_devidentd_download_devregex_url" ] ; then
            ulog ${SERVICE_NAME} status "downloading devregex from $SYSCFG_devidentd_download_devregex_url"
            curl -L $SYSCFG_devidentd_download_devregex_url --create-dirs -o $DEVREGEX_TMP_FILE --capath $CERTS_ROOT
        else
            ulog ${SERVICE_NAME} status "downloading devregex from $DEVREGEX_URL"
            curl -L $DEVREGEX_URL --create-dirs -o $DEVREGEX_TMP_FILE --capath $CERTS_ROOT
        fi
        if [ ! -d $DEVREGEX_FILE_DIR ] ; then
            mkdir -p $DEVREGEX_FILE_DIR
        fi
        if [ -e $DEVREGEX_TMP_FILE ] ; then
            /sbin/devidentd_lock "mv $DEVREGEX_TMP_FILE $DEVREGEX_FILE"
        fi
    fi
    clear_all
}
clear_all ()
{
    ulog ${SERVICE_NAME} status "clearing all schedules"
    if [ -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE} ] ; then
        rm -f $PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE
    fi
    if [ -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY} ] ; then
        rm -f ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY}
    fi
    sysevent set $DOWNLOAD_DEVREGEX_TIMESTAMP
}
schedule_hourly_cron ()
{
    ulog ${SERVICE_NAME} status "scheduling hourly cron"
    if [ ! -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY} ]; then
        generate_timestamp_cron_file > ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY}
        chmod +x ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_HOURLY}
    fi
}
schedule_everyminute_cron ()
{
    ulog ${SERVICE_NAME} status "scheduling every minute cron"
    if [ ! -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE} ]; then
        generate_timestamp_cron_file > ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE}
        chmod +x ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE}
    fi
}
check_timestamp ()
{
    ulog ${SERVICE_NAME} status "checking timestamp"
    SYSEVENT_devidentd_download_devregex_timestamp=`sysevent get $DOWNLOAD_DEVREGEX_TIMESTAMP`
    CURRENT_TIMESTAMP=`date +%s`
    if [ ! -z "$SYSEVENT_devidentd_download_devregex_timestamp" ] && [ $SYSEVENT_devidentd_download_devregex_timestamp -lt $CURRENT_TIMESTAMP ] ; then
        download_devregex
        ulog ${SERVICE_NAME} status "creating timestamp between now and a week"
        UPPER_LIMIT_SECS=`expr 7 \\* 24 \\* 60 \\* 60` # a week
        NEXT_DOWNLOAD_TIMESTAMP=`lua $DEVIDENTD_CREATE_TIMESTAMP_FILE $UPPER_LIMIT_SECS`
        sysevent set $DOWNLOAD_DEVREGEX_TIMESTAMP $NEXT_DOWNLOAD_TIMESTAMP
        schedule_hourly_cron
    fi
}
create_timestamp_cron ()
{
    check_timestamp
    if [ ! -e ${PRUNE_CRON_FILE_CHECK_TIMESTAMP_EVERY_MINUTE} ] ; then
        ulog ${SERVICE_NAME} status "creating timestamp between now and an hour"
        UPPER_LIMIT_SECS=`expr 1 \\* 60 \\* 60` # 1 hour
        NEXT_DOWNLOAD_TIMESTAMP=`lua $DEVIDENTD_CREATE_TIMESTAMP_FILE $UPPER_LIMIT_SECS`
        sysevent set $DOWNLOAD_DEVREGEX_TIMESTAMP $NEXT_DOWNLOAD_TIMESTAMP
    fi
}
service_restart ()
{
    clear_all
    create_timestamp_cron
    schedule_everyminute_cron
}
case "$1" in
  ${SERVICE_NAME}-start)
     create_timestamp_cron
     schedule_everyminute_cron
     ;;
  ${SERVICE_NAME}-stop)
     clear_all
     ;;
  ${SERVICE_NAME}-restart)
     service_restart
     ;;
  ${SERVICE_NAME}-check_timestamp)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} check timestamp, triggered by $1"
     check_timestamp
     ;;
  wan-started)
     ulog ${SERVICE_NAME} status "${SERVICE_NAME} cron, triggered by $1"
     create_timestamp_cron
     schedule_everyminute_cron
     ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart|${SERVICE_NAME}-check_timestamp]" >&2
      exit 3
      ;;
esac
