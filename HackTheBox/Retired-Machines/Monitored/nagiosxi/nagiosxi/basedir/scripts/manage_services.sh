#!/bin/bash
#
# Manage Services (start/stop/restart)
# Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
#
# =====================
# Built to allow start/stop/restart of services using the proper method based on
# the actual version of operating system.
#
# Examples:
# ./manage_services.sh start httpd
# ./manage_services.sh restart mysqld
# ./manage_services.sh checkconfig nagios
#

BASEDIR=$(dirname $(readlink -f $0))

# Import xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg

# Things you can do
first=("start" "stop" "restart" "status" "reload" "checkconfig" "enable" "disable")
second=("postgresql" "httpd" "mysqld" "nagios" "ndo2db" "npcd" "snmptt" "ntpd" "crond" "shellinaboxd" "snmptrapd" "php-fpm")

# Helper functions
# -----------------------

contains () {
    local array="$1[@]"
    local seeking=$2
    local in=1
    for element in "${!array}"; do
        if [[ "$element" == "$seeking" ]]; then
            in=0
            break
        fi
    done
    return $in
}

# Verify to avoid abuse
# -----------------------

# Check to verify the proper usage format
# ($1 = action, $2 = service name)

if ! contains first "$1"; then
    echo "First parameter must be one of: ${first[*]}"
    exit 1
fi

if ! contains second "$2"; then
    echo "Second parameter must be one of: ${second[*]}"
    exit 1
fi

action=$1

# if service name is defined in xi-sys.cfg use that name
# else use name passed
if [ "$2" != "php-fpm" ] && [ ! -z "${!2}" ];then
    service=${!2}
else
    service=$2
fi

# if the action is status, add -n 0 to args to stop journal output
# on CentOS/RHEL 7 systems
args=""
if [ "$action" == "status" ]; then
    args="-n 0"
fi

# Special case for ndo2db since we don't use it anymore
if [ "$service" == "ndo2db" ]; then
    echo "OK - Nagios XI 5.7 uses NDO3 build in and no longer uses the ndo2db service"
    exit 0
fi

# Run the command
# -----------------------

# CentOS / Red Hat

if [ "$distro" == "CentOS" ] || [ "$distro" == "RedHatEnterpriseServer" ] || [ "$distro" == "EnterpriseEnterpriseServer" ] || [ "$distro" == "OracleServer" ]; then
    # Check for enable/disable verb
    if [ "$action" == "enable" ] || [ "$action" == "disable" ]; then
        if [ `command -v systemctl` ]; then
            `which systemctl` --no-pager "$action" "$service"
        elif [ `command -v chkconfig` ]; then
            chkconfig_path=`which chkconfig`
            if [ "$action" == "enable" ]; then
                "$chkconfig_path" --add "$service"
                return_code=$?
            elif [ "$action" == "disable" ]; then
                "$chkconfig_path" --del "$service"
                return_code=$?
            fi
        fi

        exit $return_code
    fi

    if [ `command -v systemctl` ]; then
        `which systemctl` --no-pager "$action" "$service" $args
        return_code=$?
        if [ "$service" == "mysqld" ] && [ $return_code -ne 0 ]; then
            service="mariadb"
            `which systemctl` "$action" "$service" $args
            return_code=$?
        fi
    elif [ ! `command -v service` ]; then
        "/etc/init.d/$service" "$action"
        return_code=$?
    else
        `which service` "$service" "$action"
        return_code=$?
    fi
fi

# OpenSUSE / SUSE Enterprise

if [ "$distro" == "SUSE LINUX" ]; then
    if [ "$dist" == "suse11" ]; then
        `which service` "$service" "$action"
        return_code=$?
    fi
fi


# Ubuntu / Debian

if [ "$distro" == "Debian" ] || [ "$distro" == "Ubuntu" ]; then
    # Adjust the shellinabox service, no trailing 'd' in Debian/Ubuntu
    if [ "$service" == "shellinaboxd" ]; then
        service="shellinabox"
    fi

    if [ `command -v systemctl` ]; then
        `which systemctl` --no-pager "$action" "$service" $args
        return_code=$?
    else
        `which service` "$service" "$action"
        return_code=$?
    fi
fi

# Others?

exit $return_code
