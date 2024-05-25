#!/bin/sh
STATUS_FILE="/tmp/wpsstatus"
CLIENT_MAC=""
INTERFACE=""
TIMEOUT="true"
MAXWAIT=120
CNT=0;
while [ $CNT -lt $MAXWAIT ]
do
	STATE=`sysevent get wps_process`
	if [ "completed" = "$STATE" ]; then
		sysevent set wl_wps_status "success"
		sysevent set wps-success
		TIMEOUT="false"
		break;
	fi
	sleep 2;
	CNT=`expr $CNT + 2`
	sysevent set wl_wps_progress $CNT
done
if [ "true" = "$TIMEOUT" ]; then
	sysevent set wl_wps_status "failed"
	sysevent set wps-failed
fi
