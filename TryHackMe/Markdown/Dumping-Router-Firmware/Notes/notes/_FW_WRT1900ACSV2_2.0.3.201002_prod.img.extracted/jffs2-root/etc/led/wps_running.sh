#!/bin/sh
#
# WPS running state
#

LED_CTRL=/sys/class/leds/pca963x:wps/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo timer > $LED_CTRL

