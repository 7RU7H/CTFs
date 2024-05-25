#!/bin/sh
#
# WPS stopped state
#

LED_CTRL=/sys/class/leds/pca963x:wps/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo none > $LED_CTRL

