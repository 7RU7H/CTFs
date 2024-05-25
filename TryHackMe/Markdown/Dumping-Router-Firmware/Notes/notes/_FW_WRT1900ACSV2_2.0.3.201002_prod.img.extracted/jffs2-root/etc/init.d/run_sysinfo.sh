#!/bin/sh
REQUEST_ID=$$_$(date -u +%s)
TIMEOUT=2m
OUTFILE=/tmp/sysinfo.txt.${REQUEST_ID}
WIFI_OUTFILE=/tmp/sysinfo_wifi.txt.${REQUEST_ID}
SECTIONS=$1
WIFI_SUBSECTIONS=$2
{
   sleep $TIMEOUT
   kill -9 $$
   rm -f $OUTFILE
   rm -f $WIFI_OUTFILE
} &
echo $REQUEST_ID
if [ "$SECTIONS" != "WifiInfo" ]; then
   /www/sysinfo_json.cgi $SECTIONS > $OUTFILE 2>/dev/null
fi
if [ -n "$WIFI_SUBSECTIONS" ]; then
   /www/sysinfo_wifi_json.cgi $(echo $WIFI_SUBSECTIONS | sed 's/,/ /g') > $WIFI_OUTFILE 2>/dev/null
fi
