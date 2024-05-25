#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

# update auto recovery information

if [ -e /usr/sbin/recovery ]; then
	# This utility is dedicated to the platforms using U-Boot only.
	recovery -c
fi

if [ -e /usr/sbin/nvram ]; then
	# Broadcom platforms using CFE.
	nvram set partialboots=1
	nvram commit
fi

