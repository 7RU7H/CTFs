#!/bin/sh
#
# WPS success state
#

LED_CTRL=/sys/class/leds/pca963x:wps/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo default-on > $LED_CTRL
sleep 5
echo none > $LED_CTRL

