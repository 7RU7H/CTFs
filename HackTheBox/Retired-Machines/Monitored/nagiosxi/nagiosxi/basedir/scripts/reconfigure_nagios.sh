#!/bin/bash
#
# Re-Configure Nagios Core Using CCM Config
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#
# exit codes:
#   1   Config verification failed
#   2   CCM import failed
#   3   reset_config_perms.sh failed
#   4   ccm_export.php failed (write configs failed) 
#   6   /etc/init.d/nagios restart failed 
#   7   db_connect failed
#

BASEDIR=$(dirname $(readlink -f $0))

# Fix permissions on config files to make sure NagiosQL can write data
sudo $BASEDIR/reset_config_perms.sh
ret=$?
if [ $ret -gt 0 ]; then
    printf "\nResetting configuration permissions failed!\n"
    exit 3
fi

# Import all config files in import directory
/usr/bin/php -q $BASEDIR/ccm_import.php
ret=$?
if [ $ret -gt 0 ]; then
    printf "\nImporting into the CCM failed!\n"
    exit $ret
fi

# Write out the Nagios configs from the CCM and restart Nagios
$BASEDIR/restart_nagios_with_export.sh

ret=$?
if [ $ret -gt 0 ]; then
    exit $ret
fi

exit 0