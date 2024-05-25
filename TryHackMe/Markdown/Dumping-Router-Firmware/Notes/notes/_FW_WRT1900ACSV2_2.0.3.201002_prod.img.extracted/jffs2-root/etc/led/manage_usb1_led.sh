#!/bin/sh
#
# Manages USB1 LED
#
# Unlike Mamba, USB1 LED is not combo eSata/USB LED port. eSATA has its own.
#
# $1 is usb_port_1_state
# $2 is the event value
#

LED_CTRL=/proc/bdutil-led/shelby-leds
LED_ESATA=/proc/bdutil-led/shelby-leds

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

#check if UI disable all led
LED_STATUS=`syscfg get led_ui_rearport`
if [ "0" == "${LED_STATUS}" ]; then
	exit 0 
fi

if [ "$2" == "up" ]
then
    type=`sysevent get usb_port_1_type`
    if [ "$type" == "storage" -o "$type" == "printer" ]
    then
        echo usb1-on > $LED_CTRL
        # note: you might want to the same to LED_ESATA for case of eSATA.
        exit 0
    fi
fi

sleep 1
echo usb1-off > $LED_CTRL
