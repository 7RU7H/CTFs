#!/bin/sh
source /etc/init.d/util_functions.sh
FILEPATH="/tmp/.usbinfo"
if [ ! -d "$FILEPATH" ] ; then 
	exit
fi
if [ $# -eq 1 ]; then
	if [ "$1" == "list" ]; then
		ls -1 $FILEPATH | cut -d'.' -f1
	fi
	if [ "$1" == "attrib" ]; then
		nfo_file=`ls $FILEPATH | head -1`
		cat $FILEPATH/$nfo_file | cut -d':' -f1
	fi
elif [ $# -eq 2 ]; then
	DEVICE=$1
	PARAM=$2
	FILENAME="${FILEPATH}/${DEVICE}.nfo"
	if [ -e $FILENAME ]; then
		getValueForKey $FILENAME $PARAM
	fi
fi
