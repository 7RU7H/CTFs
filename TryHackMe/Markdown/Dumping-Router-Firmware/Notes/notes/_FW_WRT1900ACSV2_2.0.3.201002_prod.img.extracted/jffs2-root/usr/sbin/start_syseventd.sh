#! /bin/sh

/sbin/syseventd
sleep .5
se_pid=`cat /var/run/syseventd.pid`
cp /proc/$se_pid/maps /var/log/syseventd.maps

