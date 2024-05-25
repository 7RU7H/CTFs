#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="conntrack"
case "$1" in
    delete_conntrack)
        IP=`sysevent get delete_conntrack` 
        conntrack -D -s "$IP"
        ulog service_conntrack status "delete $IP"
        ;;
    *)
        echo "Usage: service-${SERVICE_NAME} delete" > /dev/console
        exit 3
        ;;
esac
