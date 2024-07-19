#!/bin/bash
#
# Restores the Default Configuration in the CCM/Nagios Core for Nagios XI
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))
VARDIR="$BASEDIR/../var"
NAGIOS_ETC=/usr/local/nagios/etc

DEFAULTS="$BASEDIR/nagiosql_defaults.sql"

HOST="localhost"
USER="root"
PASSWORD="nagiosxi"
DATABASE="nagiosql"

# Verify user wants to reset everything
fmt -s -w $(tput cols) <<-EOF
========================
Nagios XI Reset Defaults
========================
WARNING: This script will reset all of your configurations to
the defaults set after a clean install of Nagios XI. 
EOF

read -p "Are you sure you want to continue? [y/N] " res

if [ "$res" = "y" -o "$res" = "Y" ]; then
    echo "Proceeding with reset..."
else
    echo "Script cancelled"
    exit 1
fi

# Gather information to check for offloaded dbs
if [ -f $BASEDIR/../html/config.inc.php ]; then

    # Import Nagios XI and xi-sys.cfg config vars
    . $BASEDIR/../var/xi-sys.cfg
    eval $(php $BASEDIR/import_xiconfig.php)

    # Get the nagiosql info
    eval "CFG_HOST=\$cfg__db_info__nagiosql__dbserver"
    eval "CFG_USER=\$cfg__db_info__nagiosql__user"
    eval "CFG_PASSWORD=\$cfg__db_info__nagiosql__pwd"
    eval "CFG_DATABASE=\$cfg__db_info__nagiosql__db"

    # Overwrite if found
    if [ "x$CFG_HOST" != "x" ]; then
        HOST=$CFG_HOST
    fi
    if [ "x$CFG_USER" != "x" ]; then
        USER=$CFG_USER
    fi
    if [ "x$CFG_PASSWORD" != "x" ]; then
        PASSWORD=$CFG_PASSWORD
    fi
    if [ "x$CFG_DATABASE" != "x" ]; then
        DATABASE=$CFG_DATABASE
    fi

fi

# Backup the existing configuration
mysqldump -h "$HOST" -u "$USER" -p"$PASSWORD" "$DATABASE" > $VARDIR/nagiosql_backup."$(date +%s)".sql

# Restore the defaults
mysql -h "$HOST" -u "$USER" -p"$PASSWORD" "$DATABASE" < "$DEFAULTS"

# Generate a new password for nagiosql user
NEW_PW=$(cat /dev/urandom | tr -dc "A-Za-z0-9" | head -c 16)

# Update nagiosql db
mysql -h "$HOST" -u "$USER" -p"$PASSWORD" "$DATABASE" -e "UPDATE tbl_user SET  password = md5('$NEW_PW') WHERE username = 'nagiosxi';"

# Remove existing configuration files
rm -f $NAGIOS_ETC/commands.cfg
rm -f $NAGIOS_ETC/contactgroups.cfg
rm -f $NAGIOS_ETC/contacts.cfg
rm -f $NAGIOS_ETC/contacttemplates.cfg
rm -f $NAGIOS_ETC/hostdependencies.cfg
rm -f $NAGIOS_ETC/hostescalations.cfg
rm -f $NAGIOS_ETC/hostextinfo.cfg
rm -f $NAGIOS_ETC/hostgroups.cfg
rm -f $NAGIOS_ETC/hosts/*
rm -f $NAGIOS_ETC/hosttemplates.cfg
rm -f $NAGIOS_ETC/servicedependencies.cfg
rm -f $NAGIOS_ETC/serviceescalations.cfg
rm -f $NAGIOS_ETC/serviceextinfo.cfg
rm -f $NAGIOS_ETC/servicegroups.cfg
rm -f $NAGIOS_ETC/services/*
rm -f $NAGIOS_ETC/servicetemplates.cfg
rm -f $NAGIOS_ETC/timeperiods.cfg

# Regenerate configs and restart nagios
$BASEDIR/reconfigure_nagios.sh

sleep 5
echo ""
echo ""
