#!/bin/sh
#
# Sets all LED to default behavior
#

LED_CTRL_LAN=/sys/devices/platform/mv_switch/reg_w
LED_CTRL_WAN=/sys/class/leds/pca963x\:internet/trigger
LED_CTRL_INET_AMBER=/sys/class/leds/pca963x\:internet_amber/trigger
LED_CTRL_LED=/proc/bdutil-led/shelby-leds
IWPRIV=/usr/sbin/iwpriv

#note: it is not required to check for INET_AMBER, checking CTRL_WAN covers it.
if [ ! -e $LED_CTRL_LAN ] && [ ! -e $LED_CTRL_WAN ];
then
    exit 0
fi

echo "1 22 2 8030" > $LED_CTRL_LAN
echo "2 22 2 8030" > $LED_CTRL_LAN
echo "3 22 2 8030" > $LED_CTRL_LAN
echo "0 22 2 8030" > $LED_CTRL_LAN

echo "none" > $LED_CTRL_WAN
echo "on" > $LED_CTRL_LED
echo "esata-default" > $LED_CTRL_LED

USB_port1_state=`sysevent get usb_port_1_state`
if [ "up" = "$USB_port1_state" ] ; then
	echo usb1-on > $LED_CTRL_LED
fi

USB_port2_state=`sysevent get usb_port_2_state`
if [ "up" = "$USB_port2_state" ] ; then
	echo usb2-on > $LED_CTRL_LED
	
	speed=`sysevent get usb_port_2_speed`
	if [ "$speed" == "5000" ] ; then
            echo usb2_1-on > $LED_CTRL_LED
	fi
fi

if [ -f $IWPRIV ]; then
    $IWPRIV wdev0 setcmd "led on"
    $IWPRIV wdev1 setcmd "led on"
fi

/etc/led/manage_wan_led.sh&
