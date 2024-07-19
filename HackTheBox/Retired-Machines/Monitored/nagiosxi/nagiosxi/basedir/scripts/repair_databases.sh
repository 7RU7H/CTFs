#!/bin/bash
#
# Repair Databases Based on DB Type
# Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
#

PATH=$PATH:/sbin:/usr/sbin
BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

restart_mysql="true"
if [ "$1" = "offloaded" ]; then
    restart_msql="false"
fi

repair() {
    $BASEDIR/repairmysql.sh $1
    exit_code=$?
    if [ $exit_code -eq 0 ]; then
        exit_message+=$1$' database repair succeeded\n'
    elif [ $exit_code -eq 13 ]; then
        exit_message+=$1$' offloaded database repair succeeded\n'
    elif [ $exit_code -eq 6 ]; then
        #exit_message+=$1$' database repair skipped, not using MyISAM tables\n'
        true
    else
        exit_message+=$1$' database repair FAILED, please check output above!\n'
    fi
}

if [ ! -f "$BASEDIR/repair_databases.lock" ]; then
    touch "$BASEDIR/repair_databases.lock"

    if [ "$restart_msql" = "true" ]; then
        ret=$($BASEDIR/manage_services.sh status mysqld)
        mysqlstatus=$?
        if [ ! $mysqlstatus -eq 0 ]; then
            rm -f /var/lib/mysql/mysql.sock
            $BASEDIR/manage_services.sh start mysqld
        fi
    fi

    repair nagios
    repair nagiosql
    if [ "$cfg__db_info__nagiosxi__dbtype" == "mysql" ]; then
        repair nagiosxi
    fi

    $BASEDIR/manage_services.sh restart nagios
    rm -f "$BASEDIR/repair_databases.lock"
    echo ""
    echo "======================="
    echo "$exit_message"
else
    echo "$BASEDIR/repair_databases.lock already exists. Perhaps a repair is already in process ..aborting"
fi