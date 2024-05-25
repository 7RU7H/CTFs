#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

#------------------------------------------------------------------
# This script set the default LAN port LED behavior. The LAN port LEDs are
# controlled by the Marvell switch (as opposed to GPIO or I2C expansion bus).
# Each port as 2 LEDs.
#
# To control the LED, do the following:
# echo "PORT 22 2 80##" > /sys/devices/platform/neta/switch/reg_w
# - PORT is the port number (0-3)
# - ## is the register value as defined by the following tables
#
# LED0 - bits 0:3
# 0x00 off=link down, on=link up, blink=activity, blink rate=link speed
# 0x01 off=link down, on=100/1000 link up, blink=activity
# 0x02 off=link down, on=1000 link up, blink=activity
# 0x03 off=link down, on=link up, blink=activity
# 0x04 Port 0's special LED
# 0x05 Reserved
# 0x06 off=half-duplex, on=full-duplex, blink=collision
# 0x07 off=link down, on=10/1000 link up, blink=activity
# 0x08 off=link down, on=link up
# 0x09 off=link down, on=10 link up
# 0x0A off=link down, on=10 link up, blink=activity
# 0x0B off=link down, on=100/1000 link up
# 0x0C blink=PTP activity
# 0x0D force blink
# 0x0E force off
# 0x0F force on
#
# LED1 - bits 4:7
# 0x00 Port 2's special LED
# 0x01 off=link down, on=10/100 link up, blink=activity
# 0x02 off=link down, on=10/100 link up, blink=activity
# 0x03 off=link down, on=1000 link up
# 0x04 Port 1's special LED
# 0x05 Reserved
# 0x06 off=link down, on=10/1000 link up, blink=activity
# 0x07 off=link down, on=10/1000 link up
# 0x08 off=link down, blink=activity
# 0x09 off=link down, on=100 link up
# 0x0A off=link down, on=100 link up, blink=activity
# 0x0B off=link down, on=10/100 link up
# 0x0C blink=PTP activity
# 0x0D force blink
# 0x0E force off
# 0x0F force on
#
# Example
# echo "0 22 2 8030" > /sys/devices/platform/neta/switch/reg_w
# - sets port 0, LED0 is 0x00, LED1 is 0x03
#------------------------------------------------------------------

if [ -e /sys/devices/platform/neta/switch/reg_w ]
then
    echo "0 22 2 8030" > /sys/devices/platform/neta/switch/reg_w
    echo "1 22 2 8030" > /sys/devices/platform/neta/switch/reg_w
    echo "2 22 2 8030" > /sys/devices/platform/neta/switch/reg_w
    echo "3 22 2 8030" > /sys/devices/platform/neta/switch/reg_w
fi

