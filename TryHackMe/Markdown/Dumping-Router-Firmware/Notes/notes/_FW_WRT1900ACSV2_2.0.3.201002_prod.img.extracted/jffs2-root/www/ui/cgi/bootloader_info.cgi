#!/bin/sh

########################################################
# bootloader information 
######################################################

if [ -f /usr/sbin/nvram ]; then
	echo "================================================"
	echo "-----CFE version-------"
	echo "sw_version=`nvram get sw_version`"
	echo ""
	
	echo "-----Devinfo version-------"
	echo "devinfo_version=`nvram get devinfo_version`"
	echo ""

	echo "-----Manufacturer Data-------"
	echo "mfg_data_version=`nvram get mfg_data_version`"
	echo "modelNumber=`nvram get modelNumber`"
	echo "serial_number=`nvram get serial_number`"
	echo "uuid_key=`nvram get uuid_key`"
	echo "wps_device_pin=`nvram get wps_device_pin`"
	echo "hw_version=`nvram get hw_version`"
	echo "manufacturer_date=`nvram get manufacturer_date`"
	echo "hw_revision=`nvram get hw_revision`"
	echo "hw_mac_addr=`nvram get hw_mac_addr`"
#	echo "tc_ssid=`nvram get tc_ssid`"
#	echo "tc_passphrase=`nvram get tc_passphrase`"
	echo "default_ssid=`nvram get default_ssid`"
	echo "default_passphrase=`nvram get default_passphrase`"
	echo "boardnum=`nvram get boardnum`"
	echo ""

	echo "-----CFE Data-----"
	echo "nvram show | grep -i boot: `nvram show | grep -i boot`"
	echo ""

fi

if [ -f /usr/sbin/fw_printenv ]; then
	echo "-----U-Boot Data-----"
	echo "fw_printenv bootdelay: `fw_printenv bootdelay`"
	echo ""
	echo "fw_printenv mtdparts: `fw_printenv mtdparts`"
	echo ""
	echo "fw_printenv bootcmd: `fw_printenv bootcmd`"
	echo ""
	echo "fw_printenv boot_part: `fw_printenv boot_part`"
	echo ""
	echo "fw_printenv auto_recovery: `fw_printenv auto_recovery`"
	echo ""
	echo "fw_printenv mtdparts_version: `fw_printenv mtdparts_version`"
	echo ""
	echo "All fw_printenv:"
	echo "`fw_printenv`"
	echo ""

fi
