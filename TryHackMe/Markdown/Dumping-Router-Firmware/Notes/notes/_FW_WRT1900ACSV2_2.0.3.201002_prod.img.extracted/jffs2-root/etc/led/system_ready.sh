#!/bin/sh
#
# System ready
#

LED_CTRL=/sys/class/leds/pwr/trigger

if [ ! -e $LED_CTRL ]
then
    exit 0
fi

echo default-on > $LED_CTRL

#check if UI disable all led
LED_STATUS=`syscfg get led_ui_rearport`
IWPRIV=/usr/sbin/iwpriv
if [ "0" == "${LED_STATUS}" ]; then
	if [ -f $IWPRIV ]; then
    	$IWPRIV wdev0 setcmd "led off"
    	$IWPRIV wdev1 setcmd "led off"
	fi
fi
