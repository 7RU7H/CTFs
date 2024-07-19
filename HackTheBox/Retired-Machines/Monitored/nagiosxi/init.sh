#!/bin/bash -e
#
# Sets up the xi-sys.cfg file on full install
#

xivar() {
    ./xivar "$1" "$2"
    eval "$1"=\"\$2\"
}

# Add a newline at end of file just in case there isn't one (thanks Git!)
printf "\n" >> xi-sys.cfg

# XI version
xivar xiver $(sed -n '/full/ s/.*=\(.*\)/\L\1/p' ./nagiosxi/basedir/var/xiversion)

# OS-related variables have a detailed long variable, and a more useful short
# one: distro/dist, version/ver, architecture/arch. If in doubt, use the short
. ./get-os-info
xivar distro  "$distro"
xivar version "$version"
xivar ver     "${version%%.*}" # short major version, e.g. "6" instead of "6.2"
xivar architecture "$architecture"

# Set dist variable like before (el5/el6 on both CentOS & Red Hat)
case "$distro" in
    CentOS | RedHatEnterpriseServer | OracleServer | CloudLinux )
        xivar dist "el$ver"
        ;;
    Fedora )
        xivar dist "fedora$ver"
        ;;
    Debian )
        xivar dist "debian$ver"
        ;;
    "SUSE LINUX" )
        xivar dist "suse$ver"
        ;;
    *)
        xivar dist $(echo "$distro$ver" | tr A-Z a-z)
esac

# i386 is a more useful value than i686 for el5, because repo paths and
# package names use i386
if [ "$dist $architecture" = "el5 i686" ]; then
    xivar arch i386
else
    xivar arch "$architecture"
fi

case "$dist" in
    el8 | el9 )
        xivar ntpd chronyd
        if [ "$arch" = "x86_64" ]; then
            xivar php_extension_dir /usr/lib64/php/modules
        else
            xivar php_extension_dir /usr/lib/php/modules
        fi
        ;;
    el5 | el6 | el7 )
        if [ "$arch" = "x86_64" ]; then
            xivar php_extension_dir /usr/lib64/php/modules
        else
            xivar php_extension_dir /usr/lib/php/modules
        fi
        ;;
    suse11 | suse12 )
        if [ "$arch" = "x86_64" ]; then
            xivar php_extension_dir /usr/lib64/php5/extensions
            xivar apacheuser wwwrun
            xivar apachegroup www
            xivar httpdconfdir /etc/apache2/conf.d
            xivar httpdconf /etc/apache2/httpd.conf
            xivar httpdroot /srv/www/htdocs
            xivar phpini /etc/php5/cli/php.ini
            xivar phpconfd /etc/php5/conf.d
            xivar htpasswdbin /usr/bin/htpasswd2
            xivar httpd apache2
            xivar ntpd ntp
            xivar crond cron
            xivar mysqld mysql
        fi
        ;;
    ubuntu14 | ubuntu16 | ubuntu18 | ubuntu20 | ubuntu22 | debian8 | debian9 | debian10 | debian11 )
            xivar apacheuser www-data
            xivar apachegroup www-data
            xivar httpdconf /etc/apache2/apache2.conf
            xivar httpdconfdir /etc/apache2/conf-enabled
            xivar httpdroot /var/www/html
            xivar phpini /etc/php5/apache2/php.ini
            xivar phpconfd /etc/php5/apache2/conf.d
            xivar phpconfdcli /etc/php5/cli/conf.d
            xivar mibsdir /usr/share/mibs
            xivar httpd apache2
            xivar ntpd ntp
            xivar crond cron
            xivar mysqld mysql
        if [ "$dist" == "ubuntu16" ]; then
            xivar phpini /etc/php/7.0/apache2/php.ini
            xivar phpconfd /etc/php/7.0/apache2/conf.d
            xivar phpconfdcli /etc/php/7.0/cli/conf.d
        elif [ "$dist" == "ubuntu18" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/7.2/apache2/php.ini
            xivar phpconfd /etc/php/7.2/apache2/conf.d
            xivar phpconfdcli /etc/php/7.2/cli/conf.d
        elif [ "$dist" == "ubuntu20" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/7.4/apache2/php.ini
            xivar phpconfd /etc/php/7.4/apache2/conf.d
            xivar phpconfdcli /etc/php/7.4/cli/conf.d
        elif [ "$dist" == "ubuntu22" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/8.1/apache2/php.ini
            xivar phpconfd /etc/php/8.1/apache2/conf.d
            xivar phpconfdcli /etc/php/8.1/cli/conf.d
        elif [ "$dist" == "debian9" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/7.0/apache2/php.ini
            xivar phpconfd /etc/php/7.0/apache2/conf.d
            xivar phpconfdcli /etc/php/7.0/cli/conf.d
        elif [ "$dist" == "debian10" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/7.3/apache2/php.ini
            xivar phpconfd /etc/php/7.3/apache2/conf.d
            xivar phpconfdcli /etc/php/7.3/cli/conf.d
        elif [ "$dist" = "debian11" ]; then
            xivar mibsdir /usr/share/snmp/mibs
            xivar phpini /etc/php/7.4/apache2/php.ini
            xivar phpconfd /etc/php/7.4/apache2/conf.d
            xivar phpconfdcli /etc/php/7.4/cli/conf.d
            xivar mysqld mariadb
        fi
        ;;
    *)
        :
esac

# do a basic test to determine mysql type (except for debian 9)
# similar to the one in 1-prereqs, but not as advanced
if which systemctl >/dev/null 2>&1 && `systemctl list-unit-files | grep -q mariadb`; then
    if [ "$dist" != "debian9" ]; then
        xivar mysqld mariadb
    fi
fi

# load xi config if present
if [ -f /usr/local/nagiosxi/html/config.inc.php ]; then
    /usr/bin/php nagiosxi/basedir/scripts/import_xiconfig.php >> xi-sys.cfg
fi

# try and detect an appropriate amount of cores for make -j
procs=2

# most linux and osx
if which getconf &>/dev/null && getconf _NPROCESSORS_ONLN &>/dev/null; then
    procs=$(getconf _NPROCESSORS_ONLN)
else
    # anything with a procfs
    if [ -f /proc/cpuinfo ]; then
        procs=$(cat /proc/cpuinfo | grep processor | wc -l)
        if [ "$procs" == "0" ]; then
            procs=2
        fi
    fi
fi

xivar make_j_flag $procs