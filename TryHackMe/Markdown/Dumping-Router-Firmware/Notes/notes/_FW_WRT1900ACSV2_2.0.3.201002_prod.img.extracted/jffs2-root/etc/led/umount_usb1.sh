#!/bin/sh
#
# USB1 umount event
#
# USB1 is not combo eSata/USB port for Cobra.
#

LED_CTRL=/proc/bdutil-led/shelby-leds

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo usb1-off > $LED_CTRL

