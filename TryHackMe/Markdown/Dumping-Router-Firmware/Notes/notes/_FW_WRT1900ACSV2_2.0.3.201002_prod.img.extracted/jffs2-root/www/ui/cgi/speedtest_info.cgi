#!/bin/sh

########################################################
# speedtest information 
######################################################

echo "================================================"
echo "----- speedtest info -------"
if [ -f /var/log/messages.0 ] ; then
   echo "`grep speedtest /var/log/messages.0`"
fi
echo "`grep speedtest /var/log/messages`"
