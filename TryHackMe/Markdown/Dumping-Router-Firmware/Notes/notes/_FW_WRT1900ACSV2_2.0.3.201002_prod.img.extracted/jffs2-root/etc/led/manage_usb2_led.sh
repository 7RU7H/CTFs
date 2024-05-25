#!/bin/sh
#
# Manages USB2 LED
#
# USB2 is standalone USB port, 3.0, 2.0 and legacy
#
# $1 is usb_port_2_state
# $2 is the event value
#

LED_CTRL=/proc/bdutil-led/shelby-leds
LED_CTRL1=/proc/bdutil-led/shelby-leds

#They either appear together or not at all. No need to test for the other.
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
    type=`sysevent get usb_port_2_type`
    if [ "$type" == "storage" -o "$type" == "printer" ]
    then
        echo usb2-on > $LED_CTRL

        # USB 3.0 LED indicator
        speed=`sysevent get usb_port_2_speed`
        if [ "$speed" == "5000" ]
        then
            echo usb2_1-on > $LED_CTRL1
        else
            echo usb2_1-off > $LED_CTRL1
        fi

        exit 0
    fi
fi

sleep 1
echo usb2-off > $LED_CTRL
echo usb2_1-off > $LED_CTRL1
        
