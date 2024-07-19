#!/bin/bash
#
# Creates a Nagios Core NOM Checkpoint from Error
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../var/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

cfgdir="/usr/local/nagios/etc"
checkpointdir="/usr/local/nagiosxi/nom/checkpoints/nagioscore/errors"

pushd $checkpointdir

# What timestamp should we use for this files?
stamp=`date +%s`

# Get Nagios verification output
output=`/usr/local/nagios/bin/nagios -v /usr/local/nagios/etc/nagios.cfg > $stamp.txt`

# Copy a vesrion of the config into the directory
#cp -rf $cfgdir $stamp

# Create the tarball
tar czf $stamp.tar.gz $cfgdir

# Fix perms (if script run by root)
chown $nagiosuser:$nagiosgroup $stamp.txt
chown $nagiosuser:$nagiosgroup $stamp.tar.gz

popd
