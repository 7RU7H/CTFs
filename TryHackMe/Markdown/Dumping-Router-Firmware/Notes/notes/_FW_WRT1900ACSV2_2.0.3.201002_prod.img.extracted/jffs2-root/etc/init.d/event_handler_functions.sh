source /etc/init.d/event_flags
check_err ()
{
   if [ "${1}" -ne "0" ] ; then
      ulog $SERVICE_NAME status "PID ($$) Error ($1) $2"
      sysevent set ${SERVICE_NAME}-status error
      sysevent set ${SERVICE_NAME}-errinfo "Error ($1) $2"
   fi
}
check_err_exit ()
{
   if [ "${1}" -ne "0" ] ; then
      ulog $SERVICE_NAME status "PID ($$) Error ($1) $2"
      sysevent set ${SERVICE_NAME}-status error
      sysevent set ${SERVICE_NAME}-errinfo "Error ($1) $2"
      exit ${1}
   fi
}
wait_till_end_state ()
{
  LSERVICE=$1
  TRIES=1
   while [ "20" -ge "$TRIES" ] ; do
      LSTATUS=`sysevent get ${LSERVICE}-status`
      if [ "starting" = "$LSTATUS" ] || [ "stopping" = "$LSTATUS" ] ; then
         sleep 1
         TRIES=`expr $TRIES + 1`
         if [ "$TRIES" -eq "19" ] ; then
            logger "wait_till_end_state: problem starting up ${LSERVICE}"
         fi
      else
         logger "wait_till_end_state: ${LSERVICE} - ${LSTATUS}"
         return
      fi
   done
}
fwup_updating()
{
    state=$1
    if [ -z "$state" ]; then
	state=$(sysevent get fwup_state)
    fi
    [ $state -gt 2 ] && return 0
    return 1
}
locking()
{
    local LOCK_FILE=$1
    if (set -o noclobber; echo "$$" > "$LOCK_FILE") 2> /dev/null
    then  # Try to lock a file
        trap 'rm -f "$LOCK_FILE"; exit $?' INT TERM EXIT;
        return 0;
    fi
    return 1
}
lock()
{
    local LOCK_FILE=$1
    local IS_DONE=1
    while [ $IS_DONE != 0 ]
    do
        locking $LOCK_FILE
        IS_DONE=$?
        if [ 0 != $IS_DONE ]
        then
            sleep 1
        fi
    done
}
unlock()
{
    local LOCK_FILE=$1
    rm -f "$LOCK_FILE"    # Remove the lock file
    trap - INT TERM EXIT
    return 0
}
