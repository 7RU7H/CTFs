#!/bin/bash
#
# Changes the System's Timezone
# Copyright (c) 2014-2020 Nagios Enterprises, LLC. All rights reserved.
#

PATH=$PATH:/sbin:/usr/sbin
BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

###############################
# USAGE / HELP
###############################
usage () {
	echo ""
	echo "Use this script change your Timezone for your Nagios XI system. (PHP and Localtime)"
	echo ""
	echo " -z | --zone             	The Posix & PHP supported timezone you want to change to"
	echo "                                  Example Timezone: America/Chicago"
	echo ""
	echo " -h | --help             	Show the help section"
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
		-z | --zone)
			TZONE=$2
			;;
	esac
	shift
done

if [ "x$TZONE" == "x" ] || [ ! -e /usr/share/zoneinfo/$TZONE ]; then
	echo "You must enter a proper time zone to change to (i.e. America/Chicago)"
	exit 1
fi

# Set the sysconfig clock time
if [ -e /etc/sysconfig/clock ]; then
	echo 'ZONE="'$TZONE'"' > /etc/sysconfig/clock
fi

# Set the localtime
ln -sf /usr/share/zoneinfo/$TZONE /etc/localtime

# Set the PHP timezone
cp -f $phpini $phpini.backup
sed -ri "s~^;?date\.timezone *=.*~date.timezone = $TZONE~" $phpini

# sleep for 2 seconds
sleep 2

# Restart apache and databases to make sure timezone is properly set
$BASEDIR/manage_services.sh reload httpd

# Restart php-fom if we are on el8
if [ "$dist" == "el8" ]; then
	$BASEDIR/manage_services.sh restart php-fpm
fi

# Check for pgsql
if [ "x$cfg__db_info__nagiosxi__dbtype" == "xpgsql" ]; then
	$BASEDIR/manage_services.sh restart postgresql
fi

$BASEDIR/manage_services.sh restart mysqld

echo 'All timezone configurations updated to "'$TZONE'"'
