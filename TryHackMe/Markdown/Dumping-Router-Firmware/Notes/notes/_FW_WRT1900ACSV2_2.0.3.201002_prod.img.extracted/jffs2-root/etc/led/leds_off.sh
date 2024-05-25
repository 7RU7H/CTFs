#!/bin/sh
#
# Sets all LED to off
#

LED_CTRL_LAN=/sys/devices/platform/mv_switch/reg_w
LED_CTRL_WAN=/sys/class/leds/pca963x\:internet/trigger
LED_CTRL_INET_AMBER=/sys/class/leds/pca963x\:internet_amber/trigger
LED_CTRL_LED=/proc/bdutil-led/shelby-leds
IWPRIV=/usr/sbin/iwpriv

#Note: it is not require to check for ..INET_AMBER since CTRL_WAN covers it.
if [ ! -e $LED_CTRL_LAN ] && [ ! -e $LED_CTRL_WAN ];
then
    exit 0
fi

echo "1 22 2 80EE" > $LED_CTRL_LAN
echo "2 22 2 80EE" > $LED_CTRL_LAN
echo "3 22 2 80EE" > $LED_CTRL_LAN
echo "0 22 2 80EE" > $LED_CTRL_LAN

echo "none" > $LED_CTRL_WAN
echo "none" > $LED_CTRL_INET_AMBER
echo "esata-off" > $LED_CTRL_LED
echo "off" > $LED_CTRL_LED

if [ -f $IWPRIV ]; then
    $IWPRIV wdev0 setcmd "led off"
    $IWPRIV wdev1 setcmd "led off"
fi


