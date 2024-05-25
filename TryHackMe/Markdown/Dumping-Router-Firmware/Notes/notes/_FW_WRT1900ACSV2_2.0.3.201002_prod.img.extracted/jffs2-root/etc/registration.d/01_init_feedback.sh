#!/bin/sh

#------------------------------------------------------------------
# © 2014 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------
source /etc/init.d/feedback_registration_functions.sh	 
###############################################################################
#	Feedback Events
#
#	You may add a new line for a new feedback event
#
# 	The format of each line of a feedback event string is:
# 	name_of_event | path/filename_of_handler ;\
#
#	Optionally if the handler takes a parameter
# 	name_of_event | path/filename_of_handler | parameter ;\
#
#
###############################################################################

FEEDBACK_EVENTS="\
           system_state-normal|/etc/led/system_ready.sh ;\
           wan-status|/etc/led/manage_wan_led.sh ;\
           phylink_wan_state|/etc/led/manage_wan_led.sh ;\
           icc_internet_state|/etc/led/manage_wan_led.sh ;\
           wps-running|/etc/led/wps_running.sh ;\
           wps-success|/etc/led/wps_success.sh ;\
           wps-failed|/etc/led/wps_failed.sh ;\
           wps-stopped|/etc/led/wps_stopped.sh ;\
           led_ethernet_on|/etc/led/leds_default.sh ;\
           led_ethernet_off|/etc/led/leds_off.sh ;\
           usb_port_1_state|/etc/led/manage_usb1_led.sh ;\
           usb_port_2_state|/etc/led/manage_usb2_led.sh ;\
           usb_port_1_umount|/etc/led/umount_usb1.sh ;\
           usb_port_2_umount|/etc/led/umount_usb2.sh ;\
          "

# these are the USB events
# usb_port_#_type printer
# usb_port_#_type storage
# usb_port_#_state up
# usb_port_#_state down or detecting
# usb_port_#_speed xxx

#FEEDBACK_EVENTS="\
#          system_state-normal|/etc/led/solid.sh ;\
#          system_state-error|/etc/led/blink_15_sec.sh ;\
#          system_state-heartbeat|/etc/led/pulsate.sh ;\
#          fwupd-start|/etc/led/pulsate.sh ;\
#          fwupd-success|/etc/led/solid.sh ;\
#          fwupd-failed|/etc/led/blink_15_sec.sh ;\
#          wps-running|/etc/led/pulsate.sh ;\
#          wps-success|/etc/led/solid.sh ;\
#          wps-failed|/etc/led/blink_15_sec.sh ;\
#          wps-stopped|/etc/led/solid.sh ;\
#          led_ethernet_on|/etc/led/rear_all_default.sh ;\
#          led_ethernet_off|/etc/led/rear_all_off.sh ; 
#         "


###############################################################################
#	No need to edit below
###############################################################################
	 	 
register_events_handler "$FEEDBACK_EVENTS"


