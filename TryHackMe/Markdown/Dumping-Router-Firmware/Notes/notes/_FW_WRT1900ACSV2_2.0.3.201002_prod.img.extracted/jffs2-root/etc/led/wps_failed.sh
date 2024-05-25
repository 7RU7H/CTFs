#!/bin/sh
#
# WPS failed state
#

LED_CTRL_AMBER=/sys/class/leds/pca963x:wps_amber/trigger
LED_CTRL=/sys/class/leds/pca963x:wps/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo none > $LED_CTRL
echo timer > $LED_CTRL_AMBER
sleep 60
echo none > $LED_CTRL_AMBER

