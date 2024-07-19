#!/bin/bash
#
# Restores a Full Backup of Nagios XI
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

# Change this password if your root password is different than "nagiosxi"
# MySQL root password
themysqlpass="nagiosxi"

# Tests mysql connection by opening a connection and selecting the DB we want to use
test_mysql_connection() {
    local db_host="$1"
    local db_port="$2"
    local db_username="$3"
    local db_password="$4"
    db_error=$(mysql -h "$db_host" --port="$db_port" -u $db_username --password=$db_password -e "show databases;" 2>&1)
    return $?
}

# Make sure we have the backup file
if [ $# != 1 ]; then
    echo "Usage: $0 <backupfile>"
    echo "This script restores your XI system using a previously made Nagios XI backup file."
    exit 1
fi
backupfile=$1

BASEDIR=$(dirname $(readlink -f $0))
SPECIAL_BACKUP=0

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../var/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

# Must be root
me=`whoami`
if [ $me != "root" ]; then
    echo "You must be root to run this script."
    exit 1
fi

rootdir=/store/backups/nagiosxi

##############################
# MAKE SURE BACKUP FILE EXIST
##############################
if [ ! -f $backupfile ]; then
    echo "Unable to find backup file $backupfile!"
    exit 1
fi

# Look inside the (nested) tarball to see what architecture the nagios
# executable is
if [ $backupfile == "/store/backups/nagiosxi-demo.tar.gz" ]; then
    backuparch="x86_64"
else
    backuparch=$(eval $(echo $(tar -xzOf $backupfile $(basename $backupfile .tar.gz)/nagiosxi.tar.gz | tar -xzOf - usr/local/nagiosxi/var/xi-sys.cfg |cat|grep ^arch\=));echo $arch)
fi

arch=$(uname -m)
case $arch in
    i*86 )   arch="i686" ;;
    x86_64 ) arch="x86_64" ;;
    * )      echo "Error detecting architecture."; exit 1
esac

if [ "$arch" != "$backuparch" ]; then
    echo "WARNING: you are trying to restore a $backuparch backup on a $arch system"
    echo "         Compiled plugins and other binaries will NOT be restored."
    echo
    read -r -p "Are you sure you want to continue? [y/N] " ok

    case "$ok" in
        Y | y ) : ;;
        * )     exit 1
    esac
fi

backupdist=$(eval $(echo $(tar -xzOf $backupfile $(basename $backupfile .tar.gz)/nagiosxi.tar.gz | tar -xzOf - usr/local/nagiosxi/var/xi-sys.cfg |cat|grep ^dist\=));echo $dist)

if [ "$dist" != "$backupdist" ]; then
    SPECIAL_BACKUP=1

    echo "WARNING: you are trying to restore a $backupdist backup on a $dist system"
    echo "         Compiled plugins and other binaries as well as httpd configurations"
    echo "         will NOT be restored."
    echo ""
    echo "         You will need to re-download the Nagios XI tarball, and re-install"
    echo "         the subcomponents for this system. More info here:"
    echo "         https://assets.nagios.com/downloads/nagiosxi/docs/Backing-Up-And-Restoring-Nagios-XI.pdf"
    echo ""
    read -r -p "Are you sure you want to continue? [y/N] " ok

    case "$ok" in
        Y | y ) : ;;
        * )     exit 1
    esac
fi

##############################
# MAKE TEMP RESTORE DIRECTORY
##############################
#ts=`echo $backupfile | cut -d . -f 1`
ts=`date +%s`
echo "TS=$ts"
mydir=${rootdir}/${ts}-restore
mkdir -p $mydir
if [ ! -d $mydir ]; then
    echo "Unable to create restore directory $mydir!"
    exit 1
fi


##############################
# UNZIP BACKUP
##############################
echo "Extracting backup to $mydir..."
tar xps -f "$backupfile" -C "$mydir"

# Change to subdirectory
subdir=$(tar tf "$backupfile" |head -1 |cut -f 1 -d /)
cd "$mydir/$subdir"

# Make sure we have some directories here...
backupdir=`pwd`
echo "In $backupdir..."
if [ ! -f nagiosxi.tar.gz ]; then
    echo "Unable to find files to restore in $backupdir"
    exit 1
fi

echo "Backup files look okay.  Preparing to restore..."


##############################
# SHUTDOWN SERVICES
##############################
echo "Shutting down services..."
$BASEDIR/manage_services.sh stop nagios
$BASEDIR/manage_services.sh stop npcd


##############################
# RESTORE DIRS
##############################
rootdir=/
echo "Restoring directories to ${rootdir}..."

# Nagios Core
echo "Restoring Nagios Core..."
if [ "$arch" == "$backuparch" ] && [ $SPECIAL_BACKUP -eq 0 ]; then
    rm -rf /usr/local/nagios
    cd $rootdir && tar xzf $backupdir/nagios.tar.gz 
else
    rm -rf /usr/local/nagios/etc /usr/local/nagios/share /usr/local/nagios/var
    cd $rootdir && tar --exclude="usr/local/nagios/bin" --exclude="usr/local/nagios/sbin" --exclude="usr/local/nagios/libexec" -xzf $backupdir/nagios.tar.gz
    cd $rootdir && tar --wildcards 'usr/local/nagios/libexec/*' -xzf $backupdir/nagios.tar.gz
fi

# Restore ramdisk if it exists
if [ -f "$backupdir/ramdisk.nagios" ]; then
    echo "Updating ramdisk configuration..."
    cp  $backupdir/ramdisk.nagios /etc/sysconfig/nagios
fi

# Nagios XI
echo "Restoring Nagios XI..."
if [ "$arch" == "$backuparch" ] && [ $SPECIAL_BACKUP -eq 0 ]; then
    rm -rf /usr/local/nagiosxi
    cd $rootdir && tar xzfps $backupdir/nagiosxi.tar.gz 
else
    mv $BASEDIR/../var/xi-sys.cfg /tmp/xi-sys.cfg
    mv $BASEDIR/../var/certs /tmp/certs
    mv $BASEDIR/../var/keys /tmp/keys

    rm -rf /usr/local/nagiosxi
    cd $rootdir && tar xzfps $backupdir/nagiosxi.tar.gz 

    cp -r /tmp/xi-sys.cfg $BASEDIR/../var/xi-sys.cfg
    cp -r /tmp/xi-sys.cfg $BASEDIR/../etc/xi-sys.cfg
    chown root:$nagiosgroup $BASEDIR/../etc/xi-sys.cfg
    chmod 550 $BASEDIR/../etc/xi-sys.cfg
    rm -f /tmp/xi-sys.cfg

    # Check for certs
    mkdir -p $BASEDIR/../var/certs
    cp -r /tmp/certs $BASEDIR/../var/

    rm -rf /tmp/certs

    # Check for keys
    mkdir -p $BASEDIR/../var/keys
    if [ -f $BASEDIR/../var/keys/xi.key ]; then
        rm -f /tmp/keys/xi.key
    fi
    cp -r /tmp/keys $BASEDIR/../var/

    rm -rf /tmp/keys
fi

# NagiosQL
if [ -d "/var/www/html/nagiosql" ]; then

    echo "Restoring NagiosQL..."
    rm -rf /var/www/html/nagiosql
    cd $rootdir && tar xzfps $backupdir/nagiosql.tar.gz

    # NagiosQL etc
    echo "Restoring NagiosQL backups..."
    rm -rf /etc/nagiosql
    cd $rootdir && tar xzfps $backupdir/nagiosql-etc.tar.gz 

fi

# NRDP
echo "Restoring NRDP backups..."
rm -rf /usr/local/nrdp
cd $rootdir && tar xzfps $backupdir/nrdp.tar.gz

# MRTG
if [ -f $backupdir/mrtg.tar.gz ]; then
    echo "Restoring MRTG..."
    rm -rf /var/lib/mrtg
    cd $rootdir && tar xzfps $backupdir/mrtg.tar.gz 
    cp -rp $backupdir/conf.d /etc/mrtg/
    cp -p $backupdir/mrtg.cfg /etc/mrtg/
    chown $apacheuser:$nagiosgroup /etc/mrtg/conf.d /etc/mrtg/mrtg.cfg
fi
cd $backupdir

# SNMP configs and MIBS
if [ -f $backupdir/etc-snmp.tar.gz ]; then
    echo "Restoring SNMP configuration files..."
    cd $rootdir && tar xzfps $backupdir/etc-snmp.tar.gz
fi
if [ -f $backupdir/usr-share-snmp.tar.gz ]; then
    echo "Restoring SNMP MIBs..."
    cd $rootdir && tar xzfps $backupdir/usr-share-snmp.tar.gz
fi

# Nagvis 
if [ -f $backupdir/nagvis.tar.gz ]; then 
    echo "Restoring Nagvis backups..." 
    rm -rf /usr/local/nagvis 
    cd $rootdir && tar xzfps $backupdir/nagvis.tar.gz 
    chown -R $apacheuser:$nagiosgroup /usr/local/nagvis 
fi 

# nagios user home
if [ -f $backupdir/home-nagios.tar.gz ]; then
    echo "Restoring nagios home dir..."
    cd $rootdir && tar xzfps $backupdir/home-nagios.tar.gz
fi

# RE-IMPORT ALL XI CFG VARS
. $BASEDIR/../var/xi-sys.cfg
php $BASEDIR/import_xiconfig.php > $BASEDIR/config.dat
. $BASEDIR/config.dat
rm -rf $BASEDIR/config.dat

# Overwrite the mysqlpass with the hardcoded one at the top
mysqlpass="$themysqlpass"

##############################
# RESTORE DATABASES
##############################

echo "Restoring MySQL databases..."
if [[ "$cfg__db_info__ndoutils__dbserver" == *":"* ]]; then
    ndoutils_dbport=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f2 -d":"`
    ndoutils_dbserver=`echo "$cfg__db_info__ndoutils__dbserver" | cut -f1 -d":"`
else
    ndoutils_dbport='3306'
    ndoutils_dbserver="$cfg__db_info__ndoutils__dbserver"
fi

# Test mysql and see if we can connect before continuing
x=1
while [ $x -le 5 ];
do
    test_mysql_connection $ndoutils_dbserver $ndoutils_dbport "root" $mysqlpass
    if [ $? == 1 ]; then
        echo "ERROR: Could not connect to $ndoutils_dbserver:$ndoutils_dbport with root password supplied."
        read -s -r -p "Please enter the MySQL root password: " mysqlpass
        echo ""
    else
        break
    fi
    if [ $x -eq 5 ]; then
        echo "ERROR: Aborting restore: Could not connect to MySQL."
        echo "$db_error"
        exit 1
    fi
    x=$(($x+1))
done

mysql -h "$ndoutils_dbserver" --port="$ndoutils_dbport" -u root --password="$mysqlpass" < "$backupdir/mysql/nagios.sql"
res=$?
if [ $res != 0 ]; then
    echo "Error restoring MySQL database 'nagios'"
    exit 1
fi

if [[ "$cfg__db_info__nagiosql__dbserver" == *":"* ]]; then
    nagiosql_dbport=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f2 -d":"`
    nagiosql_dbserver=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f1 -d":"`
else
    nagiosql_dbport='3306'
    nagiosql_dbserver="$cfg__db_info__nagiosql__dbserver"
fi

# Test mysql again and see if we can connect before continuing
x=1
while [ $x -le 5 ];
do
    test_mysql_connection "$nagiosql_dbserver" "$nagiosql_dbport" "root" "$mysqlpass"
    if [ $? == 1 ]; then
        echo "ERROR: Could not connect to $nagiosql_dbserver:$nagiosql_dbport with root password supplied."
        read -s -r -p "Please enter the MySQL root password: " mysqlpass
        echo ""
    else
        break
    fi
    if [ $x -eq 5 ]; then
        echo "ERROR: Aborting restore: Could not connect to MySQL."
        echo "$db_error"
        exit 1
    fi
    x=$(($x+1))
done

mysql -h "$nagiosql_dbserver" --port="$nagiosql_dbport" -u root --password="$mysqlpass" < "$backupdir/mysql/nagiosql.sql"
res=$?
if [ $res != 0 ]; then
    echo "Error restoring MySQL database 'nagiosql'"
    exit 1
fi

# Only restore PostgresQL if we are still using it 
if [ "$cfg__db_info__nagiosxi__dbtype" == "pgsql" ]; then
    
    service postgresql initdb &>/dev/null || true
    
    echo "Restoring Nagios XI PostgresQL database..."
    if [ -f /var/lib/pgsql/data/pg_hba.conf ]; then
        pghba="/var/lib/pgsql/data/pg_hba.conf"
        cp -pr $pghba $pghba.old
    else
        #Ubuntu/Debian
        pghba=$(find /etc/postgresql -name "*pg_hba.conf")
        cp -pr $pghba $pghba.old
    fi
    echo "local  all         all                   trust
host    all         all         127.0.0.1/32          trust
host    all         all         ::1/128               trust" > $pghba
    
    $BASEDIR/manage_services.sh start postgresql
    
    sudo -u postgres psql -c "create user nagiosxi with password 'n@gweb';"
    sudo -u postgres psql -c "create database nagiosxi owner nagiosxi;"
    
    $BASEDIR/manage_services.sh restart postgresql
    
    # Sleep a bit (required so Postgres finishes startup before we connect again)
    echo "Sleeping for a few seconds (please wait)..."
    sleep 7
    
    psql -U nagiosxi nagiosxi < "$backupdir/pgsql/nagiosxi.sql"
    res=$?
    if [ $res != 0 ]; then
        echo "Error restoring PostgresQL database 'nagiosxi' !"
        exit 1
    fi
    $BASEDIR/manage_services.sh restart postgresql
    if [ "$dist" == "el7" ] || [ "$dist" == "el8" ]; then
        systemctl enable postgresql.service
    elif [[ "$distro" == "Ubuntu" ]] || [[ "$distro" == "Debian" ]]; then
        update-rc.d postgresql enable
    else
        chkconfig postgresql on
    fi
    # Remove nagiosxi db from mysql if postgres is used instead
    if [[ "$cfg__db_info__nagiosql__dbserver" == *":"* ]]; then
        nagiosql_dbport=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f2 -d":"`
        nagiosql_dbserver=`echo "$cfg__db_info__nagiosql__dbserver" | cut -f1 -d":"`
    else
        nagiosql_dbport='3306'
        nagiosql_dbserver="$cfg__db_info__nagiosql__dbserver"
    fi
    mysql -h "$nagiosql_dbserver" --port="$nagiosql_dbport" -u root --password="$mysqlpass" <<< 'DROP TABLE IF EXISTS nagiosxi;'
else
    echo "Restoring Nagios XI MySQL database..."
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

    # Test mysql again and see if we can connect before continuing
    x=1
    while [ $x -le 5 ];
    do
        test_mysql_connection "$nagiosxi_dbserver" "$nagiosxi_dbport" "root" "$mysqlpass"
        if [ $? == 1 ]; then
            echo "ERROR: Could not connect to $nagiosxi_dbserver:$nagiosxi_dbport with root password supplied."
            read -s -r -p "Please enter the MySQL root password: " mysqlpass
            echo ""
        else
            break
        fi
        if [ $x -eq 5 ]; then
            echo "ERROR: Aborting restore: Could not connect to MySQL."
            echo "$db_error"
            exit 1
        fi
        x=$(($x+1))
    done

    mysql -h "$nagiosxi_dbserver" --port="$nagiosxi_dbport" -u root --password="$mysqlpass" < "$backupdir/mysql/nagiosxi.sql"
    res=$?
    if [ $res != 0 ]; then
        echo "Error restoring MySQL database 'nagiosxi' !"
        exit 1
    fi
fi

echo "Restarting database servers..."
$BASEDIR/manage_services.sh restart mysqld

##############################
# RESTORE CRONJOB ENTRIES
##############################
echo "Restoring Apache cronjobs..."
if [[ "$distro" == "Ubuntu" ]] || [[ "$distro" == "Debian" ]]; then
    cp $backupdir/cron/apache /var/spool/cron/crontabs/$apacheuser
else
    cp $backupdir/cron/apache /var/spool/cron/apache
fi

##############################
# RESTORE SUDOERS
##############################
# Not necessary

##############################
# RESTORE LOGROTATE
##############################
echo "Restoring logrotate config files..."
cp -rp $backupdir/logrotate/nagiosxi /etc/logrotate.d

##############################
# RESTORE APACHE CONFIG FILES
##############################
if [ $SPECIAL_BACKUP -eq 0 ]; then
    echo "Restoring Apache config files..."
    cp -rp $backupdir/httpd/nagios.conf $httpdconfdir
    cp -rp $backupdir/httpd/nagiosxi.conf $httpdconfdir
    cp -rp $backupdir/httpd/nagvis.conf $httpdconfdir
    cp -rp $backupdir/httpd/nrdp.conf $httpdconfdir
    if [ -d "/etc/apache2/sites-available" ]; then
        cp -rp $backupdir/httpd/default-ssl.conf /etc/apache2/sites-available
    else
        cp -rp $backupdir/httpd/ssl.conf $httpdconfdir
    fi
else
    echo "Skipping Apache config files restoration"
fi

##############################
# RESTART SERVICES
##############################
$BASEDIR/manage_services.sh restart httpd
$BASEDIR/manage_services.sh start npcd
$BASEDIR/manage_services.sh start nagios

##############################
# DELETE TEMP RESTORE DIRECTORY
##############################
rm -rf $mydir

echo " "
echo "==============="
echo "RESTORE COMPLETE"
echo "==============="

exit 0
