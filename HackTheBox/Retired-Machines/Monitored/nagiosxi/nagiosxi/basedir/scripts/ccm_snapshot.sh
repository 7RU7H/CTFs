#!/bin/bash -e
#
# Manages CCM Snapshots (Create and Restore)
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../var/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

# Create NOM directory if it doesn't exist
NOMDIR="/usr/local/nagiosxi/nom/checkpoints/nagiosxi"
if [ ! -d "$NOMDIR" ];then
    mkdir -p "$NOMDIR"
    chown nagios:nagios "$NOMDIR"
fi

# Check arguments passed to the script and give out help if nothing passed
# or if the script is passed -h or --help
if [[ $# == 0 || "$1" == "-h" || "$1" == "--help" ]]; then
        echo "This script either creates or restores your Nagios XI CCM configuration database."
        echo "Usage: $0 <snapshot_timestamp> [restore]"
        echo ""
        echo "  Example to create a new snapshot:"
        echo "  ./ccm_snapshot.sh 1498158172"
        echo ""
        echo "  Example to restore a previous snapshot (regular or archived snapshot):"
        echo "  ./ccm_snapshot.sh 1498158172 restore"
        echo "  ./ccm_snapshot.sh 1498158172 restore archives"
        echo ""
        exit 1
fi
ts=$1
restore=$2

# Get the nagiosql database settings
if [[ "$cfg__db_info__nagiosql__dbserver" == *":"* ]]; then
    nagiosql_dbport=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f2 -d":"`
    nagiosql_dbserver=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f1 -d":"`
else
    nagiosql_dbport='3306'
    nagiosql_dbserver="$cfg__db_info__nagiosql__dbserver"
fi

# Do the actual restore or snapshot
if [[ "$restore" != "restore" ]]; then

    echo "taking snapshot"

    # Dump the database into the sql file
    mysqldump -h "$nagiosql_dbserver" --port="$nagiosql_dbport" -u $cfg__db_info__nagiosql__user --password="$cfg__db_info__nagiosql__pwd" $cfg__db_info__nagiosql__db | gzip > $NOMDIR/${ts}_nagiosql.sql.gz

    # Verify that the db dump was successful
    res=${PIPESTATUS[0]}
    if [ $res != 0 ]; then
        echo "Error backing up MySQL database 'nagiosql' - check nagiosql db configuration values in config.inc.php"
        exit $res;
    else
        chown nagios:nagios $NOMDIR/${ts}_nagiosql.sql.gz
    fi

    echo "Backup Complete."

else

    echo "Restoring CCMl snapshot"

    # If archives is set
    if [[ "$3" == "archives" ]]; then
        NOMDIR="$NOMDIR/archives"
        archives="archives/"
    fi

    # Look for NOM snapshot in directory
    if [[ ! -f $NOMDIR/${ts}_nagiosql.sql.gz ]]; then
        echo "Unable to find required snapshot files!"
        exit 1
    fi

    echo "Removing old files from /usr/local/nagios/etc"
    $BASEDIR/nom_restore_nagioscore_checkpoint_specific.sh ${ts} ${archives}

    echo "Restoring CCM databases..."
    gunzip < $NOMDIR/${ts}_nagiosql.sql.gz | mysql -h "$nagiosql_dbserver" --port="$nagiosql_dbport" -u $cfg__db_info__nagiosql__user --password="$cfg__db_info__nagiosql__pwd" $cfg__db_info__nagiosql__db
    res=$?
    if [ "$res" != 0 ]; then
        echo "Error restoring MySQL database 'nagiosql' - check nagiosql db configuration values in config.inc.php"
        exit 1;
    fi

    echo "Restore Complete."

fi

exit 0
