#!/bin/bash
# ========================================================================================
# CPU Utilization Statistics plugin for Nagios 
#
# Written by         	: Steve Bosek (sbosek@mac.com)
# Release               : 1.3
# Creation date			: 8 September 2007
# Revision date         : 18 October 2007
# Package               : DTB Nagios Plugin
# Description           : Nagios plugin (script) to check cpu utilization statistics.
#						  This script has been designed and written on Unix plateform (Linux, Aix), 
#						  requiring iostat as external program. The locations of these can easily 
#						  be changed by editing the variables $IOSTAT at the top of the script. 
#						  The script is used to query 4 of the key cpu statistics (user,system,iowait,idle)
#						  at the same time. Note though that there is only one set of warning 
#						  and critical values for iowait percent.
#
# Usage                 : ./check_cpu_stats.sh [-w <warn>] [-c <crit]
# ----------------------------------------------------------------------------------------
#
# TODO:  Support for HP-UX and Solaris
#		 Separate warning and critical levels must be used for user/system/iowait/idle 
#		 with new flags
#
# 1.3.1 - Updated 12/16/2015 to handle properly floating point values - Scott Wilkerson swilkerson@nagios.com
# ========================================================================================

# Paths to commands used in this script.  These may have to be modified to match your system setup.

IOSTAT=/usr/bin/iostat

# Nagios return codes
STATE_OK=0
STATE_WARNING=1
STATE_CRITICAL=2
STATE_UNKNOWN=3

# Plugin default level
WARNING_THRESHOLD=${WARNING_THRESHOLD:="30"}
CRITICAL_THRESHOLD=${CRITICAL_THRESHOLD:="100"}

if [ ! -x $IOSTAT ]; then
	echo "UNKNOWN: iostat not found or is not executable by the nagios user."
	exit $STATE_UNKNOWN
fi

# Plugin variable description
PROGNAME=$(basename $0)
RELEASE="Revision 1.3.1"
AUTHOR="(c) 2007 Steve Bosek (sbosek@mac.com)"

# Functions plugin usage
print_release() {
    echo "$RELEASE $AUTHOR"
}

print_usage() {
	echo ""
	echo "$PROGNAME $RELEASE - CPU Utilization check script for Nagios"
	echo ""
	echo "Usage: check_cpu_utilization.sh -w <warning value in % for iowait>"
	echo ""
	echo "		-w  Warning level for cpu iowait"
	echo "		-h  Show this page"
	echo ""
    echo "Usage: $PROGNAME"
    echo "Usage: $PROGNAME --help"
    echo "Usage: $PROGNAME -w <warning>"
    echo ""
}

print_help() {
		print_usage
        echo ""
        print_release $PROGNAME $RELEASE
        echo ""
        echo "This plugin will check cpu utilization (user,system,iowait,idle in %)"
		echo "-w is for reporting warning levels in percent of iowait"
        echo ""
		exit 0
}

# Make sure the correct number of command line arguments have been supplied
#if [ $# -lt 1 ]; then
#    print_usage
#    exit $STATE_UNKNOWN
#fi

# Parse parameters
while [ $# -gt 0 ]; do
    case "$1" in
        -h | --help)
            print_help
            exit $STATE_OK
            ;;
        -v | --version)
                print_release
                exit $STATE_OK
                ;;
        -w | --warning)
                shift
                WARNING_THRESHOLD=$1
                ;;
        -c | --critical)
               shift
                CRITICAL_THRESHOLD=$1
                ;;
        *)  echo "Unknown argument: $1"
            print_usage
            exit $STATE_UNKNOWN
            ;;
        esac
shift
done

# CPU Utilization Statistics Unix Plateform ( Linux and AIX are supported )
case `uname` in
	Linux ) CPU_REPORT=`iostat -c 5 2 |  tr -s ' ' ';' | sed '/^$/d' | tail -1`
			CPU_USER=`echo $CPU_REPORT | cut -d ";" -f 2`
			CPU_SYSTEM=`echo $CPU_REPORT | cut -d ";" -f 4`
			CPU_IOWAIT=`echo $CPU_REPORT | cut -d ";" -f 5`
			CPU_IDLE=`echo $CPU_REPORT | cut -d ";" -f 7`
            ;;
 	AIX )   CPU_REPORT=`iostat -t 5 2 | sed -e 's/,/./g'|tr -s ' ' ';' | tail -1`
			CPU_USER=`echo $CPU_REPORT | cut -d ";" -f 4`
			CPU_SYSTEM=`echo $CPU_REPORT | cut -d ";" -f 5`
			CPU_IOWAIT=`echo $CPU_REPORT | cut -d ";" -f 7`
			CPU_IDLE=`echo $CPU_REPORT | cut -d ";" -f 6`
            ;;
	*) 		echo "UNKNOWN: `uname` not yet supported by this plugin. Coming soon !"
			exit $STATE_UNKNOWN 
			;;
	esac

#echo "$CPU_REPORT"

#	echo "CPU STATISTICS: user=${CPU_USER}% system=${CPU_SYSTEM}% iowait=${CPU_IOWAIT}% idle=${CPU_IDLE}% | ${CPU_USER};${CPU_SYSTEM};${CPU_IOWAIT};${CPU_IDLE};$WARNING_THRESHOLD;$CRITICAL_THRESHOLD"
#	exit $STATE_OK

	if [ $(bc <<< "${CPU_IOWAIT} >= $WARNING_THRESHOLD") -eq 1 ] && [ $(bc <<< "${CPU_IOWAIT} <= $CRITICAL_THRESHOLD") -eq 1 ]; then
	    label="CPU IOWAIT WARNING:"
	    result=$STATE_WARNING
	elif [ $(bc <<< "${CPU_IOWAIT} >= $CRITICAL_THRESHOLD") -eq 1 ]; then
	    label="CPU IOWAIT CRITICAL:"
	    result=$STATE_CRITICAL
	else
	    label="CPU STATISTICS OK:"
	    result=$STATE_OK
	fi

	# round down values less than 1.0 for PNP graphing
	CPU_USER2=${CPU_USER}
	if [ $(bc <<< "${CPU_USER} < 1.0") -eq 1 ]; then
	    CPU_USER2=0.0
	fi
	CPU_SYSTEM2=${CPU_SYSTEM}
	if [ $(bc <<< "${CPU_SYSTEM} < 1.0") -eq 1 ]; then
	    CPU_SYSTEM2=0.0
	fi
	CPU_IOWAIT2=${CPU_IOWAIT}
	if [ $(bc <<< "${CPU_IOWAIT} < 1.0") -eq 1 ]; then
	    CPU_IOWAIT2=0.0
	fi
	CPU_IDLE2=${CPU_IDLE}
	if [ $(bc <<< "${CPU_IDLE} < 1.0") -eq 1 ]; then
	    CPU_IDLE2=0.0
	fi

	echo "$label user=${CPU_USER}% system=${CPU_SYSTEM}% iowait=${CPU_IOWAIT}% idle=${CPU_IDLE}% | user=${CPU_USER2}% system=${CPU_SYSTEM2}% iowait=${CPU_IOWAIT2}%;$WARNING_THRESHOLD;$CRITICAL_THRESHOLD idle=${CPU_IDLE2}%"
	exit $result


