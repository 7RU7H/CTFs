#!/bin/bash

BASEDIR=$(dirname $(readlink -f $0))

# IMPORT ALL XI CFG VARS
. $BASEDIR/../var/xi-sys.cfg
successful=0

echo "This script will attempt to perform some basic MySQL Tuning for increased performance"
echo ""
echo "Enter the MySQL configuration file to continue..."
read -p "MySQL configuration file [/etc/my.cnf]: " file
file=${file:-/etc/my.cnf}


if [ -f $file ]; then
    if grep -q "query_cache\|table_size\|buffer_size\|open_cache" $file; then
        echo "Looks like $file has already been tuned, exiting!"
        exit 1
    else
        # Note: If you're changing the tuning options here, please also change init-mysql
        REPLACEMENT='s/\[mysqld\]/\[mysqld\]\ntmp_table_size=64M\nmax_heap_table_size=64M\nkey_buffer_size=32M\ntable_open_cache=32\ninnodb_file_per_table=1\nmax_allowed_packet=256M\nsort_buffer_size=32M\nmax_connections=1000\nopen_files_limit=4096\n'

        if [ "$dist" != "el7" ] && [ "$dist" != "debian9" ]; then
            # All of these distros have binlogs turned on by default. We don't currently do anything with failover replication but might in the future.
            REPLACEMENT="$REPLACEMENT\ndisable_log_bin\n"
        fi

        if [ "$dist" == "el8" ] || [ "$dist" == "el9" ] || [ "$dist" == "ubuntu22" ]; then
            # query cache is removed in MySQL 8 but not in any MariaDB (yet).
            REPLACEMENT="$REPLACEMENT\nsql_mode=NO_ENGINE_SUBSTITUTION\n"
        else
            REPLACEMENT="$REPLACEMENT\nquery_cache_size=16M\nquery_cache_limit=4M\n"
        fi

        REPLACEMENT="$REPLACEMENT/"

        if ! sed -i "$REPLACEMENT" $mycnf; then
            successful=0
        else
            successful=1
        fi

        if [ $successful -eq 0 ]; then
            echo "Could not tune $file"
            exit 1
        else
            echo "Tuned $file, please restart MySQL"
            exit 0
        fi
    fi
else
    echo "Could not locate $file, please try again!"
    exit 1
fi