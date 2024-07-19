#!/bin/bash
#
# Creates a Full Backup of Nagios XI
# Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))
SBLOG="/usr/local/nagiosxi/var/components/scheduledbackups.log"
ts=`date +%s`

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

###############################
# USAGE / HELP
###############################
usage () {
    echo ""
    echo "Use this script to backup Nagios XI."
    echo ""
        echo " -n | --name              Set the name of the backup minus the .tar.gz"
        echo " -p | --prepend           Prepend a string to the .tar.gz name"
        echo " -a | --append            Append a string to the .tar.gz name"
        echo " -d | --directory         Change the directory to store the compressed backup"
    echo ""
}

###############################
# ADDING LOGIC FOR NEW BACKUPS
###############################
while [ -n "$1" ]; do
    case "$1" in
        -h | --help)
            usage
            exit 0
            ;;
        -n | --name)
            fullname=$2
            ;;
        -p | --prepend)
            prepend=$2"."
            ;;
        -a | --append)
            append="."$2
            ;;
        -d | --directory)
            rootdir=$2
            ;;
    esac
    shift
done

echo "\nStarting new backup....\n"

# Restart nagios to forcibly update retention.dat
$BASEDIR/manage_services.sh restart nagios
sleep 10

if [ -z "$rootdir" ]; then
    rootdir="/store/backups/nagiosxi"
fi

# Move to root dir to store backups
cd "$rootdir"

#############################
# SET THE NAME & TIME
#############################
name=$fullname

if [ -z "$fullname" ]; then
    name="$prepend$ts$append"
fi

# Clean the name
name=$(echo "$name" | sed -e 's/[^[:alnum:].|-]//g')

# Get current Unix timestamp as name
if [ -z "$name" ]; then
    name="$ts"
fi

# My working directory
mydir=$rootdir/$name

# Make directory for this specific backup
mkdir -p "$mydir"

##############################
# BACKUP DIRS
##############################

# Only backup NagiosQL if it exists
if [ -d "/var/www/html/nagiosql" ]; then
    echo "Backing up NagiosQL..."
    tar czfp "$mydir/nagiosql.tar.gz" /var/www/html/nagiosql
    tar czfp "$mydir/nagiosql-etc.tar.gz" /etc/nagiosql
fi

echo "Backing up Nagios Core..."
tar czfp "$mydir/nagios.tar.gz" /usr/local/nagios

# Backup ramdisk if it exists
if [ -f "/etc/sysconfig/nagios" ]; then
    echo "Copying ramdisk configuration..."
    cp /etc/sysconfig/nagios "$mydir/ramdisk.nagios"
fi

echo "Backing up Nagios XI..."
tar czfp "$mydir/nagiosxi.tar.gz" /usr/local/nagiosxi

echo "Backing up MRTG..."
tar czfp "$mydir/mrtg.tar.gz" /var/lib/mrtg
cp /etc/mrtg/mrtg.cfg "$mydir/"
cp -r /etc/mrtg/conf.d "$mydir/"

# SNMP configs and MIBS
echo "Backing up the SNMP directories"
tar czfp "$mydir/etc-snmp.tar.gz" /etc/snmp
tar czfp "$mydir/usr-share-snmp.tar.gz" /usr/share/snmp

echo "Backing up NRDP..."
tar czfp "$mydir/nrdp.tar.gz" /usr/local/nrdp

echo "Backing up Nagvis..." 
tar czfp "$mydir/nagvis.tar.gz" /usr/local/nagvis

echo "Backing up nagios user home dir..." 
tar czfp "$mydir/home-nagios.tar.gz" /home/nagios

##############################
# BACKUP DATABASES
##############################
echo "Backing up MySQL databases..."
mkdir -p "$mydir/mysql"
if [[ "$cfg__db_info__ndoutils__dbserver" == *":"* ]]; then
    ndoutils_dbport=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f2 -d":"`
    ndoutils_dbserver=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f1 -d":"`
else
    ndoutils_dbport='3306'
    ndoutils_dbserver="$cfg__db_info__ndoutils__dbserver"
fi
mysqldump -h "$ndoutils_dbserver" --port="$ndoutils_dbport" -u $cfg__db_info__ndoutils__user --password="$cfg__db_info__ndoutils__pwd" --add-drop-database -B $cfg__db_info__ndoutils__db > $mydir/mysql/nagios.sql
res=$?
if [ $res != 0 ]; then
    echo "Error backing up MySQL database 'nagios' - check the password in this script!"
    rm -r "$mydir"
    exit $res;
fi
if [[ "$cfg__db_info__nagiosql__dbserver" == *":"* ]]; then
    nagiosql_dbport=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f2 -d":"`
    nagiosql_dbserver=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f1 -d":"`
else
    nagiosql_dbport='3306'
    nagiosql_dbserver="$cfg__db_info__nagiosql__dbserver"
fi
mysqldump -h "$nagiosql_dbserver" --port="$nagiosql_dbport" -u $cfg__db_info__nagiosql__user --password="$cfg__db_info__nagiosql__pwd" --add-drop-database -B $cfg__db_info__nagiosql__db > $mydir/mysql/nagiosql.sql
res=$?
if [ $res != 0 ]; then
    echo "Error backing up MySQL database 'nagiosql' - check the password in this script!"
    rm -r "$mydir"
    exit $res;
fi

# Only backup PostgresQL if we are still using it 
if [ $cfg__db_info__nagiosxi__dbtype == "pgsql" ]; then
    echo "Backing up PostgresQL databases..."
    mkdir -p "$mydir/pgsql"
    if [ -z $cfg__db_info__nagiosxi__dbserver ]; then
        cfg__db_info__nagiosxi__dbserver="localhost"
    fi
    pg_dump -h $cfg__db_info__nagiosxi__dbserver -c -U $cfg__db_info__nagiosxi__user $cfg__db_info__nagiosxi__db > "$mydir/pgsql/nagiosxi.sql"
    res=$?
    if [ $res != 0 ]; then
        echo "Error backing up PostgresQL database 'nagiosxi' !"
        rm -r "$mydir"
        exit $res;
    fi
else
    if [[ "$cfg__db_info__nagiosxi__dbserver" == *":"* ]]; then
        nagiosxi_dbport=`echo "$cfg__db_info__nagiosxi__dbserver" | cut -f2 -d":"`
        nagiosxi_dbserver=`echo "$cfg__db_info__nagiosxi__dbserver" | cut -f1 -d":"`
    else
        nagiosxi_dbport='3306'
        nagiosxi_dbserver="$cfg__db_info__nagiosxi__dbserver"
    fi
    mysqldump -h "$nagiosxi_dbserver" --port="$nagiosxi_dbport" -u $cfg__db_info__nagiosxi__user --password="$cfg__db_info__nagiosxi__pwd" --add-drop-database -B $cfg__db_info__nagiosxi__db > $mydir/mysql/nagiosxi.sql
    res=$?
    if [ $res != 0 ]; then
        echo "Error backing up MySQL database 'nagiosxi' - check the password in this script!"
        rm -r "$mydir"
        exit $res;
    fi
fi

##############################
# BACKUP CRONJOB ENTRIES
##############################
echo "Backing up cronjobs for Apache..."
mkdir -p "$mydir/cron"
if [[ "$distro" == "Ubuntu" ]] || [[ "$distro" == "Debian" ]]; then
    cp "/var/spool/cron/crontabs/$apacheuser" "$mydir/cron/apache"
else
    cp /var/spool/cron/apache "$mydir/cron/apache"
fi

##############################
# BACKUP SUDOERS
##############################
# Not necessary

##############################
# BACKUP LOGROTATE
##############################
echo "Backing up logrotate config files..."
mkdir -p "$mydir/logrotate"
cp -rp /etc/logrotate.d/nagiosxi "$mydir/logrotate"

##############################
# BACKUP APACHE CONFIG FILES
##############################
echo "Backing up Apache config files..."
mkdir -p "$mydir/httpd"
cp -rp "$httpdconfdir/nagios.conf" "$mydir/httpd"
cp -rp "$httpdconfdir/nagiosxi.conf" "$mydir/httpd"
cp -rp "$httpdconfdir/nagvis.conf" "$mydir/httpd"
cp -rp "$httpdconfdir/nrdp.conf" "$mydir/httpd"

if [ -d "/etc/apache2/sites-available" ]; then
    cp -rp /etc/apache2/sites-available/default-ssl.conf "$mydir/httpd"
else
    cp -rp "$httpdconfdir/ssl.conf" "$mydir/httpd"
fi

##############################
# COMPRESS BACKUP
##############################
echo "Compressing backup..."
tar czfp "$name.tar.gz" "$name"
rm -rf "$name"

# Change ownership
chown "$nagiosuser:$nagiosgroup" "$name.tar.gz"

if [ -s "$name.tar.gz" ];then

    echo " "
    echo "==============="
    echo "BACKUP COMPLETE"
    echo "==============="
    echo "Backup stored in $rootdir/$name.tar.gz"

    exit 0;
else
    echo " "
    echo "==============="
    echo "BACKUP FAILED"
    echo "==============="
    echo "File was not created at $rootdir/$name.tar.gz"
    rm -r "$mydir"
    exit 1;
fi
