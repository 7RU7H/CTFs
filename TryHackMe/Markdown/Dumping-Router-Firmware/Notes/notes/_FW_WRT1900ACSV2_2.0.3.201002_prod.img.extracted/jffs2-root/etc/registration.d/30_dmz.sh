#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

source /etc/init.d/service_registration_functions.sh

SELF_NAME="`basename $0`"

SERVICE_NAME="dmz"

SERVICE_DEFAULT_HANDLER="/etc/init.d/service_dmz.sh"
SERVICE_CUSTOM_EVENTS="\
          lan-start|$SERVICE_DEFAULT_HANDLER; \
          lan-stop|$SERVICE_DEFAULT_HANDLER; \
          "
# ------------------------------------------------------------------------------------
# function do_stop
# ------------------------------------------------------------------------------------
do_stop() {
#   echo "[utopia][registration] $SELF_NAME unregistering from sysevent"
   sm_unregister $SERVICE_NAME
}

# ------------------------------------------------------------------------------------
# function do_start
# ------------------------------------------------------------------------------------
do_start () {
#  echo "[utopia][registration] $SELF_NAME registered to $SERVICE_DEFAULT_HANDLER"
   sm_register $SERVICE_NAME $SERVICE_DEFAULT_HANDLER "$SERVICE_CUSTOM_EVENTS"
   
}

#-----------------------------------------------------------------------------------

case "$1" in
   start|"")
      do_start
      ;;
   restart|reload|force-reload)
      do_stop
      do_start
      ;;
   stop)
      do_stop
      ;;
   *)
      echo "Usage: $SERVICE_NAME [start|stop|restart]" >&2
      exit 3
      ;;
esac

