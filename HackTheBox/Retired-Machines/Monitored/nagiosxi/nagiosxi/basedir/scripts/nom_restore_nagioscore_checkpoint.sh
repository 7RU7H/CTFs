#!/bin/bash
#
# Restores Nagios Core NOM Checkpoint
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

cfgdir="/usr/local/nagios/etc"
checkpointdir="/usr/local/nagiosxi/nom/checkpoints/nagioscore"
lockfile="/var/run/nagios.lock"

# Find latest snapshot
latest=`ls -1r $checkpointdir/*.gz | head --lines=1`

if [ "x$latest" = "x" ]; then
    echo "NO NOM SNAPSHOT FOUND!"
    exit 1
fi

echo "LATEST NOM SNAPSHOT: $latest"

# Get the broker module we are using before we apply the old version of the nagios.cfg
broker=$(grep "^broker_module=.*" /usr/local/nagios/etc/nagios.cfg)
if [ -f /usr/local/nagios/etc/ndo.cfg ]; then
	cp -rf /usr/local/nagios/etc/ndo.cfg /tmp/ndo.cfg.backup
fi

# Delete the current Nagios core config files
#find /usr/local/nagios/etc/ -name "*.cfg" -exec ls -al {} \;

# Restore config files from checkpoint file
pushd / 
echo "RESTORING NOM SNAPSHOT : $latest"
#tar -p -s -xzf "$checkpointdir/$latest"
tar -p -s -xzf "$latest"
popd

# Verify the nagios.cfg lock file is set to proper location
sed -i "s|lock_file=.*|lock_file=$lockfile|" /usr/local/nagios/etc/nagios.cfg

# Verify broker module is proper
sed -i "s|^broker_module=.*|$broker|" /usr/local/nagios/etc/nagios.cfg
if [ ! -f /usr/local/nagios/etc/ndo.cfg ] && [ -f /tmp/ndo.cfg.backup ]; then
	mv -f /tmp/ndo.cfg.backup /usr/local/nagios/etc/ndo.cfg
fi

# Fix permissions on config files
sudo $BASEDIR/reset_config_perms.sh
