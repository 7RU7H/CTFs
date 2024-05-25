#!/bin/sh
#
# system is restarting
#

LED_CTRL=/sys/class/leds/pwr/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo heartbeat > $LED_CTRL

