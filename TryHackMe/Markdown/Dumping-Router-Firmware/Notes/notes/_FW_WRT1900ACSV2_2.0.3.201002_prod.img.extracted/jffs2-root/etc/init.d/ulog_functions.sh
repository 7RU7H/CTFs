ulog () {
    COMP=$1
    SUBCOMP=$2
    MESG=$3
    UL_MESG="$COMP.$SUBCOMP $MESG"
    if [ -f "/usr/sbin/pticks" ] ; then
      logger -p local7.notice -t UTOPIA "$UL_MESG [ `pticks` ]"
    else
      logger -p local7.notice -t UTOPIA "$UL_MESG"
    fi
}
ulog_error () {
    COMP=$1
    SUBCOMP=$2
    MESG=$3
    UL_MESG="$COMP.$SUBCOMP $MESG"
    if [ -f "/usr/sbin/pticks" ] ; then
      logger -p local7.error -t UTOPIA "$UL_MESG [ `pticks` ]"
    else
      logger -p local7.error -t UTOPIA "$UL_MESG"
    fi
    
}
ulog_debug () {
    COMP=$1
    SUBCOMP=$2
    MESG=$3
    UL_MESG="$COMP.$SUBCOMP $MESG"
    if [ -f "/usr/sbin/pticks" ] ; then
      logger -p local7.debug -t UTOPIA "$UL_MESG [ `pticks` ]"
    else
      logger -p local7.debug -t UTOPIA "$UL_MESG"
    fi
    
}
