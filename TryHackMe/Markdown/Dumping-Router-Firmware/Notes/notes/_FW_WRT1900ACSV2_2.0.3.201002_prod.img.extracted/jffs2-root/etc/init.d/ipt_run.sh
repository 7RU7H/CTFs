#!/bin/sh
IPT_LOCK_FILE=/tmp/ipt.lock
 
lock()
{
    LOCK_FILE=$1
    LOCK_WAIT=${2:-5}                               # Wait for 5 seconds by default
    
    for i in `seq 1 $LOCK_WAIT`; do
              if (set -o noclobber; echo "$$" > "$LOCK_FILE") 2> /dev/null; then 	# Try to lock a file
                  trap 'rm -f "$LOCK_FILE"; exit $?' INT TERM EXIT;      				# Remove a lock file in abnormal termination.
                  return 0;                                    # Locked
              fi
              sleep 1
    done
 
    return 1                                                   # Failure
}
 
unlock()
{
    LOCK_FILE=$1
 
    rm -f "$LOCK_FILE"                                  # Remove a lock file
    trap - INT TERM EXIT
 
    return 0
}
lock $IPT_LOCK_FILE
if [ $? == 0 ]; then
    $@
    RC=$?
    unlock $IPT_LOCK_FILE
    exit $RC
fi
exit 1
