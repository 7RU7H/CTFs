#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

source /etc/init.d/service_registration_functions.sh

SERVICE_NAME="devidentd_download_devregex"

SERVICE_DEFAULT_HANDLER="/etc/init.d/service_${SERVICE_NAME}.sh"

SERVICE_CUSTOM_EVENTS="\
             wan-started|$SERVICE_DEFAULT_HANDLER|NULL|$TUPLE_FLAG_EVENT; \
            "


#######################################################################
#             NOTHING MORE TO DO
# In general there is no need to change anything below
#######################################################################

# ------------------------------------------------------------------------------------
# function do_stop
# ------------------------------------------------------------------------------------
do_stop() {
   sm_unregister $SERVICE_NAME
}

# ------------------------------------------------------------------------------------
# function do_start
# ------------------------------------------------------------------------------------
do_start () {
   sm_register $SERVICE_NAME $SERVICE_DEFAULT_HANDLER "$SERVICE_CUSTOM_EVENTS"
}

#-----------------------------------------------------------------------------------
# This script is in the registration directory and will be called automatically at
# system boot. This allows the service to register for events of interest, and to
# set the service status.
#
# It could also be called during running system explicitly
#-----------------------------------------------------------------------------------
case "$1" in
   start|"")
      do_start
      ;;
   restart|reload|force-reload)
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
