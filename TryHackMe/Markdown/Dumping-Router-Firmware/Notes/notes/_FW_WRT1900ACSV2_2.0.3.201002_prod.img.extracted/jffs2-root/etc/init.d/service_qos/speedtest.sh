#!/bin/sh
source /etc/init.d/ulog_functions.sh
case "$1" in
  speedtest-download-start)
     if [ -f /usr/sbin/speedtest_down ] ; then
        /usr/sbin/speedtest_down
     else
        ulog qos status "Cannot start speedtest since speedtest_down does not exist"
     fi
     ;;
  speedtest-upload-start)
     if [ -f /usr/sbin/speedtest_up ] ; then
        /usr/sbin/speedtest_up
     else
        ulog qos status "Cannot start speedtest since speedtest_up does not exist"
     fi
     ;;
  *)
    exit 3
    ;;
esac
