#!/bin/bash
#
# Create a Conditional NOM Checkpoint
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))
NAGIOS_DIR="/usr/local/nagios"

# Verify Nagios Core configuration
output=`$NAGIOS_DIR/bin/nagios -v $NAGIOS_DIR/etc/nagios.cfg`
ret=$?

if [ $ret -eq 0 ]; then
    $BASEDIR/nom_create_nagioscore_checkpoint.sh
    echo "Config test passed. Checkpoint created."
    exit 0
else
    echo "Config test failed. Checkpoint aborted."
    exit 1
fi
