#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------


#------------------------------------------------------------------------------
#                   registration_template.sh
#
# This is a template for registering a lego service to the default events, as well
# as setting the service's default status.
#
# For services which only require the default events and default status, all that 
# needs to be done is specify:
#   1) the SERVICE_NAME
#   2) the SERVICE_DEFAULT_HANDLER
#   3) SERVICE_CUSTOM_EVENTS (optional) 
#
# The SERVICE_NAME is the name of the service and this is used to register for 
# service name specific events.
# The SERVICE_DEFAULT_HANDLER is the path and name of an executable that will be activated
# upon receipt of default events (start, stop, restart). The handler must not block.
# If you wish to register for notifications of other events then you define
# the events and the handlers for the events in SERVICE_CUSTOM_EVENTS
#
# For services which require more event registration, or which need to perform
# extra boot-time work, then the functions do_start() and do_stop() will need
# to be enhanced.
#------------------------------------------------------------------------------


source /etc/init.d/service_registration_functions.sh

##################################################################
# Name of this service
# --------------------
# You MUST set this to a globally unique string 
##################################################################

SERVICE_NAME="belkin_icc"


##################################################################
# Name of the default handler
# ---------------------------
# Name of the handler to invoke upon default events [start|stop|restart]
# If the value is set to NULL, then no default events will be installed
#
# It is your responsibility to ensure that the default handler code
# exists.
#
# Note 
#  When the handler is called, the 1st parameter ($1) will be the event name
#  and the 2nd parameter ($2) will be the event value (or NULL if no value) when the event occurred
#  and subsequent parameters if any are defined as extra parameters as described below
##################################################################

SERVICE_DEFAULT_HANDLER="/etc/init.d/service_${SERVICE_NAME}.sh"

##################################################################
# Custom Events
# -------------
# If the service should receive events other than start stop restart, then
# declare them. If there are no custom events then set to NULL
#     
# The format of each line of a custom event string is:
# name_of_event | path/filename_of_handler | activation_flags or NULL | tuple_flags or NULL | extra parameters
#
# Each line must be separated from the next with a ';'
# The last line does not need a separator
#
# Example of a custom event string containing several events
# 
# SERVICE_CUSTOM_EVENTS="\
#              foo|$SERVICE_DEFAULT_HANDLER|NULL|NULL|\$wan_proto @ipv4_wan_ipaddr; \
#              bar|/etc/init.d/barhandler.sh|$ACTION_FLAG_NOT_THREADSAFE; \
#              fubar|$SERVICE_DEFAULT_HANDLER|NULL|$TUPLE_FLAG_EVENT \
#             "
# (see lego_overlay/proprietary/init/init.d/service_registration_functions.sh for 
#  an explanation of the format of each event)
##################################################################

SERVICE_CUSTOM_EVENTS="\
        wan-status|$SERVICE_DEFAULT_HANDLER; \
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
