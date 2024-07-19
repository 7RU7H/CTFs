#!/bin/bash
#
# Repair MySQL Databases
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

# This script repairs one (or all) tables in a specific Nagios XI mysql database
# Usage:
# repairmysql.sh [database] [table]

BASEDIR=$(dirname $(readlink -f $0))

if [ $# -lt 1 ]; then
    echo "Usage: $0 [table]"
    echo ""
    echo "This script repairs one or more tables in a specific Nagios XI MySQL database."
    echo "Valid database names include:"
    echo " nagios";
    echo " nagiosql";
    echo " nagiosxi";
    echo ""
    echo "If the [table] option is omitted, all tables in the database will be repaired."
    echo ""
    echo "Example Usage:"
    echo " $0 nagios nagios_logentries"
    echo ""
    exit 1
fi

# Get user input as db & table
db=$1
table="";
if [ $# -eq 2 ]; then
    table=$2
fi
echo "DATABASE: $db"
echo "TABLE:    $table"

cmd="/usr/bin/myisamchk -r -f"

# Gather information to check for offloaded dbs
if [ -f /usr/local/nagiosxi/html/config.inc.php ]; then
    
    # Import Nagios XI and xi-sys.cfg config vars
    . $BASEDIR/../var/xi-sys.cfg
    eval $(php $BASEDIR/import_xiconfig.php)

    # if the db isn't one these, then there isn't anything we can do anyway
    if [ "$db" = "nagiosql" ] || [ "$db" = "nagiosxi" ] || [ "$db" = "ndoutils" ] || [ "$db" = "nagios" ]; then

        offloadeddb="$db"

        # rename nagios to ndoutils so we can look up the variable
        if [ "$db" = "nagios" ]; then
            offloadeddb="ndoutils"
        fi

        # try and get the db server and make sure it isn't set to localhost or loopback
        eval "dbserver=\$cfg__db_info__${offloadeddb}__dbserver"
        if [[ "$dbserver" == *":"* ]]; then
            dbport=`echo "$dbserver" | cut -f2 -d":"`
            dbserver=`echo "$dbserver" | cut -f1 -d":"`
        else
            dbport='3306'
        fi

        if [ "x$dbserver" != "x" ] && [ "$dbserver" != "localhost" ] && [ "$dbserver" != "127.0.0.1" ]; then

            eval "testdbtype=\$cfg__db_info__${offloadeddb}__dbtype"
            if [ "$testdbtype" = "mysql" ]; then

                eval "dbuser=\$cfg__db_info__${offloadeddb}__user"
                eval "dbpass=\$cfg__db_info__${offloadeddb}__pwd"
                eval "dbname=\$cfg__db_info__${offloadeddb}__db"
                if [ "x$dbuser" != "x" ] && [ "x$dbpass" != "x" ] && [ "x$dbname" != "x" ]; then

                    ARGS=(
                        -f
                        -r
                        -u "$dbuser"
                        -p"$dbpass"
                        -h "$dbserver"
                        --port="$dbport"
                        --databases "$dbname"
                    )
                    cmd="mysqlcheck ${ARGS[*]}"
                    mysqlcheck "${ARGS[@]}"
                    exit_code=$?
                    echo "Issued remote command '$cmd'"
                    if [ $exit_code -eq 0 ]; then
                        exit 13
                    else
                        echo "SOMETHING WENT WRONG. ATTEMPT MANUAL REPAIR"
                        exit 14
                    fi
                fi
            fi
        fi
    fi
fi

# Check status of MySQL and verify
ret=$($BASEDIR/manage_services.sh status mysqld)
mysqlstatus=$?
if [ ! $mysqlstatus -eq 0 ]; then
    rm -f /var/lib/mysql/mysql.sock
fi

# Enter directory, get tables, and run command
exit_code=0
dest="/var/lib/mysql/$db"
pushd $dest
ret=$?
if [ $ret -eq 0 ]; then

    # Check if there are any MYI files
    t="$table"
    if [ -z $t ] && ! ls *.MYI >/dev/null 2>&1; then
        exit 6
    fi

    $BASEDIR/manage_services.sh stop mysqld

    # We need to strip off the MYI because some MySQL versions don't like
    # that it is added on for the command
    if [ -z $t ]; then
        for tb in `ls *.MYI`; do
            t="$t ${tb%%.*}"
        done
    fi

    $cmd $t --sort_buffer_size=256M
    exit_code=$?
    
    $BASEDIR/manage_services.sh start mysqld
    
    popd

else
    echo "ERROR: Could not change to dir: $dest"
    exit 1
fi

echo " "
echo "==============="
echo "REPAIR COMPLETE"
echo "==============="

exit $exit_code
