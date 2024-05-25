#!/bin/sh
#
#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------
#
# Manages WAN LED
#
# normal wan is white
# alternate wan is amber, i.e. altblink means amber blink
#
# If link is down, flash amber (physical connection problem)
# If link is up, but no protocol connectivity, flash white (establishing link)
# if link is up and protocol is up, solid white
#
# If Belkin ICC (Internet Connection Checking) is disabled, the as long as the
# WAN proto is up, then it will be solid white
#

#note: These are labels exactly as they show on the front panel.
LED_CTRL=/sys/class/leds/pca963x:internet/trigger
LED_CTRL1=/sys/class/leds/pca963x:internet_amber/trigger

#No need to test for other. They are all inclusive.
if [ ! -e $LED_CTRL ]
then
    exit 0
fi

#check if UI disable all led
LED_STATUS=`syscfg get led_ui_rearport`
if [ "0" == "${LED_STATUS}" ]; then
	echo none > $LED_CTRL
    echo none > $LED_CTRL1
	exit 0 
fi

bridge_mode=`syscfg get bridge_mode`
if [ $bridge_mode == "0" ]; then
    # ------------------------------------------------------------------------
    # Router mode 
    # - ICC is running
    # - phylink_wan_state indicates WAN physical Ethernet link
    # - wan_status indicates protocol up
    # - icc_internet_state indicates internet connectivity
    #
    # Note: Cobra internet LED and WPS LED are composite color.
    # ------------------------------------------------------------------------
    link=`sysevent get phylink_wan_state`
    if [ "$link" == "down" ]
    then
        # link down. Timer mode is blinking at 50% duty cycle.
        echo timer > $LED_CTRL1
        echo none > $LED_CTRL
        exit 0
    fi

    wan_status=`sysevent get wan-status`
    if [ "$wan_status" != "started" ]
    then
        # link up but protocol down
        #Make sure amber is off before we blink the white one. The reason is you can have composite color,amber and white altogether.
        echo none > $LED_CTRL1
        echo timer > $LED_CTRL
        exit 0
    fi

    icc_enabled=`syscfg get belkin_icc_enabled`
    if [ "$icc_enabled" == 1 ]; then
        state=`sysevent get icc_internet_state`
        if [ "$state" != "up" ]; then
            # link up, protocol up, but internet down
            echo none > $LED_CTRL
            echo default-on > $LED_CTRL1
            exit 0
        fi
    fi

    # link up, protocol up, and internet up/no internet checking
    echo default-on > $LED_CTRL
    echo none > $LED_CTRL1
else
    # --------------------------------------------------------------------
    # Bridge mode, we turn off internet LED
    # --------------------------------------------------------------------
    echo none > $LED_CTRL
    echo none > $LED_CTRL1
    exit 0
fi

