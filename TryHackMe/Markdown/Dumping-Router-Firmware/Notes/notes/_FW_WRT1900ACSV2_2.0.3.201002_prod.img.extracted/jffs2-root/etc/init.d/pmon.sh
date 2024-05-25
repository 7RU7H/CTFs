#!/bin/sh
CONF_FILE=/tmp/pmon.conf
BIN=pmon
do_check_syseventd() {
   for i in 1 2 3 4 6 8 1 1 1 1 1 1 1 1 1 1 
   do
      SYSTEM_STATUS=`sysevent ping`
      if [ "$SYSTEM_STATUS" = "SUCCESS" ] ; then
         return
      fi
      sleep $i
   done
   [ -f /usr/sbin/se_post.sh ] && /usr/sbin/se_post.sh &
   sleep 3
   reboot
   exit
}
do_check_process() {
    LOCAL_CONF_FILE=/tmp/pmon.conf$$
    rm -f $LOCAL_CONF_FILE
    do_check_syseventd
    COUNT=`sysevent get pmon_feature_count`
    if [ "" = "$COUNT" ] ; then
        COUNT=0
    fi
    for ct in `seq 1 $COUNT`
    do
        feature=`sysevent get pmon_feature_$ct`
        if [ "" != "$feature" ] ; then
            PROC_ENTRY=`sysevent get pmon_proc_$feature`
            if [ "" != "$PROC_ENTRY" ] ; then
                process_name=`echo $PROC_ENTRY | cut -d' ' -f1`
                pid=`echo $PROC_ENTRY | cut -d' ' -f2`
                restartcmd=`echo $PROC_ENTRY | cut -d' ' -f3-`
                if [ "" != "$process_name" ] && [ "" != "$pid" ] && [ "" != "$restartcmd" ] ; then
                    echo "$process_name $pid $restartcmd" >> $LOCAL_CONF_FILE
                fi
            fi
        fi
   done
   
   cat $LOCAL_CONF_FILE > $CONF_FILE
   rm -f $LOCAL_CONF_FILE
   $BIN $CONF_FILE
}
do_register()
{
    if [ "" = "$1" ] ; then
        echo "pmon-register: invalid parameter [$1]" > /dev/console
        return 1
    fi
    COUNT=`sysevent get pmon_feature_count`
    if [ "" = "$COUNT" ] ; then
        COUNT=0
    fi
    FREE_SLOT=0
    for ct in `seq 1 $COUNT`
    do
        FEATURE=`sysevent get pmon_feature_$ct`
        if [ "" = "$FEATURE" ] ; then
            FREE_SLOT=$ct
        else
            if [ "$FEATURE" = "$1" ] ; then
                return
            fi
        fi
    done
    if [ "0" != "$FREE_SLOT" ]; then
        SLOT=$FREE_SLOT
    else
        COUNT=`expr $COUNT + 1`
        SLOT=$COUNT
        sysevent set pmon_feature_count $COUNT
    fi
    sysevent set pmon_feature_$SLOT "$1"
}
do_unregister()
{
    if [ "" = "$1" ] ; then
        return 1
    fi
    COUNT=`sysevent get pmon_feature_count`
    if [ "" = "$COUNT" ] ; then
        COUNT=0
    fi
    for ct in `seq 1 $COUNT`
    do
        FEATURE=`sysevent get pmon_feature_$ct`
        if [ "" != "$FEATURE" ] && [ "$1" = "$FEATURE" ] ; then
            sysevent set pmon_feature_$ct 
            sysevent set pmon_proc_$feature 
            return
        fi
    done
}
do_setproc ()
{
    if [ "" = "$1" ] || [ "" = "$2" ] || [ "" = "$3" ] || [ "" = "$4" ] ; then
        echo "pmon-setproc: invalid parameter(s) " > /dev/console
        return 1
    fi
    sysevent set pmon_proc_$1 "$2 $3 $4"
}
do_unsetproc ()
{
    if [ "" = "$1" ]; then
        echo "pmon-unsetproc: invalid parameter " > /dev/console
        return 1
    fi
    sysevent set pmon_proc_$1 
}
case "$1" in
    register)
      do_register $2 $3 "$4"
      ;;
    unregister)
      do_unregister $2
      ;;
    setproc)
      do_setproc $2 $3 $4 "$5"
      ;;
    unsetproc)
      do_unsetproc $2
      ;;
    *)
      do_check_process
      ;;
esac
