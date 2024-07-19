#!/bin/bash
#
# Remove Historical Data
# Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../var/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

mrtgdir="/var/lib/mrtg"
nagiosdir="/usr/local/nagios"

# Verify removal first
# ---------

fmt -s -w $(tput cols) <<-EOF
========================
        WARNING!
========================

This script will remove ALL historical perfdata on your system. This action is permanent.
We recommend running the backup script before proceeding.

EOF
read -p "Do you want to continue? [Y/n] " res

case "$res" in
    Y | y | "")
        echo "Proceeding..."
        ;;
    *)
        echo "Cancelled removal of historical perfdata data."
        exit 0
esac

# Stop nagios service
./manage_services.sh stop nagios

# Remove historical data from disk
# ---------
echo "Removing historical data from disk..."

# Remove nagios logs & perfdata
rm -rf $nagiosdir/var/nagios.log
rm -rf $nagiosdir/var/retention.dat
rm -rf $nagiosdir/var/objects.cache
rm -rf $nagiosdir/var/archives/*
rm -rf $nagiosdir/share/perfdata/*

# Remove MRTG data
rm -rf $mrtgdir/*

# Remove XI data
mv $proddir/var/xi-sys.cfg /tmp
rm -rf $proddir/var/*.log
rm -rf $proddir/var/components/*.log
rm -rf $proddir/var/upgrades/*
rm -rf $proddir/var/*.lock
rm -rf $proddir/var/*.data
rm -rf "$proddir/var/*.diff"
rm -rf $proddir/tmp/*
mv /tmp/xi-sys.cfg $proddir/var

# Remove historical data from database
# ---------

# Set ndoutils db config info
if [[ "$cfg__db_info__ndoutils__dbserver" == *":"* ]]; then
    ndoutils_dbport=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f2 -d":"`
    ndoutils_dbserver=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f1 -d":"`
else
    ndoutils_dbport='3306'
    ndoutils_dbserver="$cfg__db_info__ndoutils__dbserver"
fi

# Truncate ndoutils tables
tables=( "nagios_acknowledgements" "nagios_commenthistory" "nagios_comments" "nagios_downtimehistory" "nagios_flappinghistory" "nagios_logentries" "nagios_objects" "nagios_notifications" "nagios_processevents" "nagios_statehistory" "nagios_timedevents" )
for table in "${tables[@]}"; do
    mysql -h "$ndoutils_dbserver" --port="$ndoutils_dbport" -u "$cfg__db_info__ndoutils__user" --password="$cfg__db_info__ndoutils__pwd" -D nagios -e "TRUNCATE TABLE $table;"
    res=$?
    if [ $res != 0 ]; then
        echo "Error truncating table '$table' in MySQL database 'nagios'"
    fi
done

 # Set nagiosxi db config info
echo "Removing historical data in Nagios XI database..."
if [[ "$cfg__db_info__nagiosxi__dbserver" == *":"* ]]; then
    nagiosxi_dbport=`echo "$cfg__db_info__nagiosxi__dbserver" | cut -f2 -d":"`
    nagiosxi_dbserver=`echo "$cfg__db_info__nagiosxi__dbserver" | cut -f1 -d":"`
else
    nagiosxi_dbport='3306'
    if [ "x$cfg__db_info__nagiosxi__dbserver" == "x" ]; then
        nagiosxi_dbserver="localhost"
    else
        nagiosxi_dbserver="$cfg__db_info__nagiosxi__dbserver"
    fi
fi

# If PostgresQL
if [ "$cfg__db_info__nagiosxi__dbtype" == "pgsql" ]; then

    # Truncate meta data tables for XI
    tables=( "xi_auditlog" "xi_commands" "xi_eventqueue" "xi_events" "xi_meta" )
    for table in "${tables[@]}"; do
        psql -h "$nagiosxi_dbserver" -p "$nagiosxi_dbport" -U "$cfg__db_info__nagiosxi__user" --password="$cfg__db_info__nagiosxi__pwd" -d nagiosxi -c "truncate table $table;"
        res=$?
        if [ $res != 0 ]; then
            echo "Error truncating table '$table' in PostgresQL database 'nagiosxi'"
        fi
    done

# If MySQL (XI 5+)
else

    # Truncate meta data tables for XI
    tables=( "xi_auditlog" "xi_commands" "xi_eventqueue" "xi_events" "xi_meta" )
    for table in "${tables[@]}"; do
        mysql -h "$nagiosxi_dbserver" --port="$nagiosxi_dbport" -u "$cfg__db_info__nagiosxi__user" --password="$cfg__db_info__nagiosxi__pwd" -D nagiosxi -e "truncate table $table;"
        res=$?
        if [ $res != 0 ]; then
            echo "Error truncating table '$table' in MySQL database 'nagiosxi'"
        fi
    done

fi

# Start up nagios
./manage_services.sh start nagios

echo "Done."
