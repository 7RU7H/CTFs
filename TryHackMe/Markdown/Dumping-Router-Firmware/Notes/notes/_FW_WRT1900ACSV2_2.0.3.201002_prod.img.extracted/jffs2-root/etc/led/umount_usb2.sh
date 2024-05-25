#!/bin/sh
#
# USB2 umount event
#
# USB2 is standalone USB port, 3.0, 2.0 and legacy
#

LED_CTRL=/proc/bdutil-led/shelby-leds
LED_CTRL1=/proc/bdutil-led/shelby-leds
#Mutually inclusive.

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo usb2-off > $LED_CTRL
echo usb2_1-off > $LED_CTRL1
        
