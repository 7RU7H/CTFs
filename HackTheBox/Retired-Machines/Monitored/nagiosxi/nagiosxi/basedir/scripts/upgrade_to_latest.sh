#!/bin/bash -e
#
# Upgrades Nagios XI to the Latest Version
# Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
#

PATH=$PATH:/sbin:/usr/sbin
TS=$(date +%s)
error=false
BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

###############################
# USAGE / HELP
###############################
function usage () {
    echo ""
    echo "Use this script to upgrade your Nagios XI instance to the latest version."
    echo ""
    echo " -t | --time      Send a timestamp for the log to be renamed as once finished"
    echo " -f | --file      The filename/url/location of the xi update"
    echo " -h | --help      Help usage information"
    echo ""
}

###############################
# GET THE VARIABLES
###############################
while [ -n "$1" ]; do
    case "$1" in
        -h | --help)
            usage
            exit 0
            ;;
        -t | --time)
            TS=$2
            ;;
        -f | --file)
            FILE=$2
            ;;
    esac
    shift
done

# Create a log file
rm -rf /usr/local/nagiosxi/tmp/upgrade.log
touch /usr/local/nagiosxi/tmp/upgrade.log
chown $nagiosuser:$nagiosgroup /usr/local/nagiosxi/tmp/upgrade.log

# Backup XI before upgrade
echo "---- Starting Nagios XI Backup ----" > /usr/local/nagiosxi/tmp/upgrade.log
cd /usr/local/nagiosxi/scripts
./backup_xi.sh -p autoupgrade_backup >> /usr/local/nagiosxi/tmp/upgrade.log 2>&1

# Perform upgrade
echo "" >> /usr/local/nagiosxi/tmp/upgrade.log
echo "---- Starting Nagios XI Upgrade ----" >> /usr/local/nagiosxi/tmp/upgrade.log
echo "Cleaning up temp directory..." >> /usr/local/nagiosxi/tmp/upgrade.log
cd /usr/local/nagiosxi/tmp
rm -rf nagiosxi
if [ ! -z ${FILE+x} ]; then
    rm -rf xi*.tar.gz nagiosxi-*.tar.gz
    echo "Downloading Latest Nagios XI Tarball..." >> /usr/local/nagiosxi/tmp/upgrade.log
    wget "$FILE"
fi
if [ -f xi-latest.tar.gz ]; then
    tar xzf xi-latest.tar.gz
else
    tar xzf xi-*.tar.gz
fi
cd nagiosxi
echo "Starting upgrade..."

./upgrade -n >> /usr/local/nagiosxi/tmp/upgrade.log 2>&1
if [ $? -ne 0 ]; then
    FN="failed.$TS"
else
    FN="success.$TS"
fi

# Make log directory if it doesnt exist and give proper permissions for apache to be able to read it
if [ ! -d /usr/local/nagiosxi/var/upgrades ]; then
    mkdir -p /usr/local/nagiosxi/var/upgrades
    chown $apacheuser:$nagiosgroup /usr/local/nagiosxi/var/upgrades
    chmod 754 /usr/local/nagiosxi/var/upgrades
    chmod +x /usr/local/nagiosxi/var/upgrades
fi

# Copy over the file and give error or not
cp /usr/local/nagiosxi/tmp/upgrade.log /usr/local/nagiosxi/var/upgrades/$FN.log
chown -R $apacheuser:$nagiosgroup /usr/local/nagiosxi/var/upgrades
chmod 644 /usr/local/nagiosxi/var/upgrades/$FN.log

# Clean up tmp directory
cd /usr/local/nagiosxi/tmp
rm -rf nagiosxi nagiosxi-*.tar.gz xi*.tar.gz