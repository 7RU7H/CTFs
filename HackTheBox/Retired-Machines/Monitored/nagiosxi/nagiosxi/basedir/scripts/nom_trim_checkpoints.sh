#!/bin/bash
#
# Trims Nagios Core NOM Checkpoints
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

# Defaults
reg_num=11
error_num=11
ccm_num=11

basedir="/usr/local/nagiosxi/nom/checkpoints/nagioscore"
ccm_basedir="/usr/local/nagiosxi/nom/checkpoints/nagiosxi"

# TRIM GOOD CHECKPOINTS
function trim_checkpoints {

    if [[ -n $reg_usedir ]] ; then
        checkpointdir=$reg_usedir
    else
        checkpointdir="${basedir}"
    fi

    echo "DIR: $checkpointdir"

    # Get rid of all but the most recent 10 checkpoints
    numtokeep=$reg_num
    lasttokeep=`ls -1tr $checkpointdir/*.gz | tail -$numtokeep | head -1`

    checkpoints=`find $checkpointdir -maxdepth 1 -name "[0-9]*.tar.gz" | wc -l`
    echo "NUMFOUND: $checkpoints"

    if [ $checkpoints -lt $numtokeep ]; then
        echo "KEEPING ALL GOOD CHECKPOINTS"
        return;
    fi

    echo "ALL:"
    ls -1t $checkpointdir

    echo "LAST GOOD CHECKPOINT TO KEEP: $lasttokeep"
    echo "DELETING OLD TEXT FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.txt" -not -newer "$lasttokeep" -exec rm -f {} \;
    echo "DELETING OLD DIFF FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.diff" -not -newer "$lasttokeep" -exec rm -f {} \;
    find $checkpointdir -maxdepth 1 -name "[0-9]*.diffnum" -not -newer "$lasttokeep" -exec rm -f {} \;
    echo "DELETING OLD CONFIG DIRS...";
    find $checkpointdir -maxdepth 1 -type d -name "[0-9]*" -not -newer "$lasttokeep" -exec rm -rf {} \;
    echo "DELETING OLD TARBALL FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.tar.gz" -not -newer "$lasttokeep" -exec rm -f {} \;
}

# TRIM CCM SNAPSHOTS
function trim_ccm_snapshots {

    if [[ -n $ccm_usedir ]] ; then
        checkpointdir=$ccm_usedir
    else
        checkpointdir="${ccm_basedir}"
    fi

    echo "DIR: $checkpointdir"

    # Get rid of all but the most recent 10 checkpoints
    numtokeep=$ccm_num
    lasttokeep=`ls -1tr $checkpointdir/*.gz | tail -$numtokeep | head -1`

    checkpoints=`find $checkpointdir -maxdepth 1 -name "[0-9]*_nagiosql.sql.gz" | wc -l`
    echo "NUMFOUND: $checkpoints"

    if [ $checkpoints -lt $numtokeep ]; then
        echo "KEEPING ALL SNAPSHOTS"
        return;
    fi

    echo "ALL:"
    ls -1t $checkpointdir

    echo "LAST GOOD SNAPSHOTS TO KEEP: $lasttokeep"
    echo "DELETING OLD SNAPSHOT FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*_nagiosql.sql.gz" -not -newer "$lasttokeep" -exec rm -f {} \;
}

# TRIM ERROR CHECKPOINTS
function trim_errorpoints {

    if [[ -n $error_usedir ]] ; then
        checkpointdir=$error_usedir
    else
        checkpointdir="${basedir}/errors"
    fi

    echo "DIR: $checkpointdir"

    # Get rid of all but the most recent 10 checkpoints
    numtokeep=$error_num
    lasttokeep=`ls -1tr $checkpointdir/*.gz | tail -$numtokeep | head -1`

    checkpoints=`find $checkpointdir -maxdepth 1 -name "[0-9]*.tar.gz" | wc -l`
    echo "NUMFOUND: $checkpoints"

    if [ $checkpoints -lt $numtokeep ]; then
        echo "KEEPING ALL ERROR CHECKPOINTS"
        return;
    fi

    echo "ALL:"
    ls -1t $checkpointdir

    echo "LAST ERROR CHECKPOINT TO KEEP: $lasttokeep"
    echo "DELETING OLD TEXT FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.txt" -not -newer "$lasttokeep" -exec rm -f {} \;
    echo "DELETING OLD DIFF FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.diff" -not -newer "$lasttokeep" -exec rm -f {} \;
    find $checkpointdir -maxdepth 1 -name "[0-9]*.diffnum" -not -newer "$lasttokeep" -exec rm -f {} \;
    echo "DELETING OLD CONFIG DIRS...";
    find $checkpointdir -maxdepth 1 -type d -name "[0-9]*" -not -newer "$lasttokeep" -exec rm -rf {} \;
    echo "DELETING OLD TARBALL FILES...";
    find $checkpointdir -maxdepth 1 -name "[0-9]*.tar.gz" -not -newer "$lasttokeep" -exec rm -f {} \;
}

# HELP USAGE
function usage {
    echo ""
    echo "Trims Nagios Core and Nagios CCM/XI configuration snapshots."
    echo ""
    echo "Default directories:"
    echo "  -d /usr/local/nagiosxi/nom/checkpoints/nagioscore"
    echo "  -e /usr/local/nagiosxi/nom/checkpoints/nagioscore/errors"
    echo "  -c /usr/local/nagiosxi/nom/checkpoints/nagiosxi"
    echo ""
    echo " -d | --dir       (Optional) The Nagios Core checkpoint directory"
    echo " -n | --num       (Optional) Number of checkpoints (Default: 10)"
    echo ""
    echo " -e | --edir      (Optional) The Nagios Core error checkpoint directory"
    echo " -u | --enum      (Optional) Number of error checkpoints (Default: 10)"
    echo ""
    echo " -c | --cdir      (Optional) The Nagios CCM/XI checkpoint directory"
    echo " -m | --cnum      (Optional) Number of CCM/XI checkpoints (Default: 10)"
    echo ""
    echo " -h | --help      Help usage information"
    echo ""
}

# GET ARGUMENTS
while [ -n "$1" ]; do
    case "$1" in
        -h | --help)
            usage
            exit 0
            ;;
        -d | --dir)
            reg_usedir=$2
            ;;
        -e | --edir)
            error_usedir=$2
            ;;
        -c | --cdir)
            ccm_usedir=$2
            ;;
        -n | --num)
            reg_num=$(($2+1))
            ;;
        -u | --enum)
            error_num=$(($2+1))
            ;;
        -m | --cnum)
            ccm_num=$(($2+1))
            ;;
    esac
    shift
done

trim_checkpoints
trim_errorpoints
trim_ccm_snapshots
