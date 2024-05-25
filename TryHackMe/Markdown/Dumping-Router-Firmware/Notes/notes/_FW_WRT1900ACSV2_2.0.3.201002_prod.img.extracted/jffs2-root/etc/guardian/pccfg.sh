#!/bin/sh

#### Functions
syscfg_set()
{
	NAME=$1
	VALUE=$2

#	echo $@
	syscfg set $NAME "$VALUE"
}

value()
{
	NAME=$1
	POLICY=$2
	VALUE=$3

	syscfg_set pcp_$POLICY::$NAME $VALUE
}

blocklist()
{
	POLICY=$1
	shift
	COUNT=$#

	i=1
	for url in $@; do
		syscfg_set pcp_$POLICY::blocked_url_$i $url
		i=`expr $i + 1`
	done	
	syscfg_set pcp_$POLICY::blocked_url_count $COUNT
}

whitelist()
{
	POLICY=$1
	shift
	COUNT=$#

	i=1
	for url in $@; do
		syscfg_set pcp_$POLICY::allowed_url_$i $url
		i=`expr $i + 1`
	done	
	syscfg_set pcp_$POLICY::allowed_url_count $COUNT
}

devicelist()
{
	POLICY=$1
	shift
	COUNT=$#

	i=1
	for mac_uuid in $@; do
		MAC=`echo $mac_uuid | cut -d , -f 1`
		UUID=`echo $mac_uuid | cut -s -d , -f 2`
		syscfg_set pcp_$POLICY::blocked_device_$i $MAC
		syscfg_set pcp_$POLICY::blocked_device_uuid_$i $UUID
		i=`expr $i + 1`
	done	
	syscfg_set pcp_$POLICY::blocked_device_count $COUNT
}

schedule()
{
	WDAY=$1
	POLICY=$2
	shift
	shift

	TB=111111111111111111111111111111111111111111111111
	for t in $@; do
		eval START=`echo $t | cut -d - -f 1`
		eval END=`echo $t | cut -d - -f 2`

		ZERO=
		for i in `seq $START $END`; do
			ZERO=${ZERO}0
		done;

		eval RIGHT=`expr $END + 1`
		eval TEMP=${TB:0:$START}${ZERO}${TB:$RIGHT}
		eval TB=$TEMP
	done
	syscfg_set pcp_$POLICY::$WDAY $TB
}

category()
{
	POLICY=$1
	shift
	COUNT=$#

	i=1
	for c in $@; do
		START=`echo $c | cut -d - -f 1`
		END=`echo $c | cut -d - -f 2`

		for cat in `seq $START $END`; do
			syscfg_set pcp_$POLICY::blocked_category_ex_$i $cat
			i=`expr $i + 1`
		done;
	done
	syscfg_set pcp_$POLICY::blocked_category_count `expr $i - 1`
}

port()
{
	POLICY=$1
	shift
	COUNT=$#

	i=1
	for name_proto_start_end in $@; do
		NAME=`echo $name_proto_start_end | cut -d , -f 1`
		PROTO=`echo $name_proto_start_end | cut -s -d , -f 2`
		START=`echo $name_proto_start_end | cut -s -d , -f 3`
		END=`echo $name_proto_start_end | cut -s -d , -f 4`
		syscfg_set pcp_$POLICY::blocked_port_name_$i $NAME
		syscfg_set pcp_$POLICY::blocked_port_proto_$i $PROTO
		syscfg_set pcp_$POLICY::blocked_port_start_$i $START
		syscfg_set pcp_$POLICY::blocked_port_end_$i $END
		i=`expr $i + 1`
	done	
	syscfg_set pcp_$POLICY::blocked_port_count $COUNT
}

get_category()
{
	POLICY=$1
	
	CAT="00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000"

	COUNT=`syscfg get pcp_$POLICY::blocked_category_count`
	if [ "$COUNT" == "" ]; then
		COUNT=0;
	fi
	for i in `seq 1 $COUNT`; do
		eval bc=`syscfg get pcp_$POLICY::blocked_category_ex_$i`
		eval RIGHT=`expr $bc + 1`
		eval TEMP=${CAT:0:$bc}1${CAT:$RIGHT}
		eval CAT=$TEMP
	done
	echo "  category: $CAT"
}

get()
{
	POLICY=$1

	echo "[Policy #$POLICY]"
	echo "  number: `syscfg get pcp_$POLICY::number`"
	echo "  status: `syscfg get pcp_$POLICY::status`"
	echo "  name: `syscfg get pcp_$POLICY::name`"
	echo "  safe_surfing: `syscfg get pcp_$POLICY::safe_surfing`"
	echo "  device:"
	COUNT=`syscfg get pcp_$POLICY::blocked_device_count`
	if [ "$COUNT" == "" ]; then
		COUNT=0;
	fi
	for i in `seq 1 $COUNT`; do
		echo "    `syscfg get pcp_$POLICY::blocked_device_$i` `syscfg get pcp_$POLICY::blocked_device_uuid_$i`"
	done
	get_category $POLICY
	echo "  schedule:"
	echo "    mon: `syscfg get pcp_$POLICY::monday_time_blocks`"
	echo "    tue: `syscfg get pcp_$POLICY::tuesday_time_blocks`"
	echo "    wed: `syscfg get pcp_$POLICY::wednesday_time_blocks`"
	echo "    thu: `syscfg get pcp_$POLICY::thursday_time_blocks`"
	echo "    fri: `syscfg get pcp_$POLICY::friday_time_blocks`"
	echo "    sat: `syscfg get pcp_$POLICY::saturday_time_blocks`"
	echo "    sun: `syscfg get pcp_$POLICY::sunday_time_blocks`"
	echo "  blocked urls:"
	COUNT=`syscfg get pcp_$POLICY::blocked_url_count`
	if [ "$COUNT" == "" ]; then
		COUNT=0;
	fi
	for i in `seq 1 $COUNT`; do
		echo "    `syscfg get pcp_$POLICY::blocked_url_$i`"
	done
	echo "  allowed urls:"
	COUNT=`syscfg get pcp_$POLICY::allowed_url_count`
	if [ "$COUNT" == "" ]; then
		COUNT=0;
	fi
	for i in `seq 1 $COUNT`; do
		echo "    `syscfg get pcp_$POLICY::allowed_url_$i`"
	done
	echo "  blocked ports:"
	COUNT=`syscfg get pcp_$POLICY::blocked_port_count`
	if [ "$COUNT" == "" ]; then
		COUNT=0;
	fi
	for i in `seq 1 $COUNT`; do
		echo "    `syscfg get pcp_$POLICY::blocked_port_name_$i`/`syscfg get pcp_$POLICY::blocked_port_proto_$i`,`syscfg get pcp_$POLICY::blocked_port_start_$i`-`syscfg get pcp_$POLICY::blocked_port_end_$i`"
	done
}

list()
{
	COUNT=`syscfg get parental_control_policy_count`
	
	for policy in `seq 1 $COUNT`; do
		get $policy
		echo
	done
}

help()
{
	APP=$1
	echo "usage: $1 command args..."
	echo
	echo "commands:"
	echo " list - list all policies"
	echo "   ex) $1 list"
	echo
	echo " get - display the policy"
	echo "   arg1 - policy"
	echo "   ex) $1 get 1                  # show policy 1."
	echo
	echo " preset - set predefined policies. Existing polcies will be overwritten"
	echo "   ex) $1 preset"
	echo
	echo " set_number - set policy number."
	echo "   arg1 - policy"
	echo "   arg2 - number. 1~256"
	echo "   ex) $1 set_number 1 10         # set policy 1's number to 10 "
	echo
	echo " set_name - set name"
	echo "   arg1 - policy"
	echo "   arg2 - name in string"
	echo "   ex) $1 set_name 1 test         # set policy 1's name to test "
	echo
	echo " set_status - set policy status. Unused"
	echo "   arg1 - policy"
	echo "   arg2 - status. enable/disable"
	echo "   ex) $1 set_status 1 enable     # enable policy 1"
	echo
	echo " set_sws - set policy safe web surfing"
	echo "   arg1 - policy"
	echo "   arg2 - 0(disable), 1(enable)"
	echo "   ex) $1 set_sws 1 1             # enable safe web surfing of policy 1"
	echo
	echo " set_cat - set policy bad category"
	echo "   arg1 - policy"
	echo "   arg[2:n] - category list. 1~88(category), [1:88]-[1:88](category's start and end)"
	echo "   ex) $1 set_cat 1 1 2-5 10 11 40-50"
	echo "       $1 set_cat 1               # clear bad categories"
	echo
	echo " set_mon - set monday's time schedule"
	echo " set_tue - set tuesday's time schedule"
	echo " set_wed - set wednesday's time schedule"
	echo " set_thr - set thursday's time schedule"
	echo " set_fri - set friday's time schedule"
	echo " set_sat - set saturday's time schedule"
	echo " set_sun - set sunday's time schedule"
	echo "   arg1 - policy"
	echo "   arg[2:n] - blocking time by 30 minutes. 0~47(blocking time), [0:47]-[0:47](blocking time's start and end) "
	echo "   ex) $1 set_mon 1 0-11 42-47    # block 0~6am and 9pm~12am"
	echo "       $1 set_mon 1 28            # block 2pm~2:30pm"
	echo "       $1 set_mon 1               # clear blocking times"
	echo
	echo " set_dev - set policy devices"
	echo "   arg1 - policy"
	echo "   arg[2:n] - pairs of MAC and UUID separated by ','. UUID is optional"
	echo "   ex) $1 set_dev 1 01:02:03:04:05:06,abcdefgh-ijkl-mnop-qrst-uvwxyz123456 0a:0b:0c:0d:0e:0f"
	echo "       $1 set_dev 1               # clear devices"
	echo
	echo " set_blk - set policy blocklist"
	echo "   arg1 - policy"
	echo "   arg[2:n] - url list"
	echo "   ex) $1 set_blk 1 .blocked1.com blocked2.com"
	echo "       $1 set_blk 1               # clear blocked urls"
	echo
	echo " set_wht - set policy whitelist"
	echo "   arg1 - policy"
	echo "   arg[2:n] - url list"
	echo "   ex) $1 set_wht 1 .allowed1.com allowed2.com"
	echo "       $1 set_wht 1               # clear allowed urls"
	echo
	echo " set_port - set blocked ports"
	echo "   arg1 - policy"
	echo "   arg[2:n] - port list(name,protocol,start,end), protocol=both,tcp,udp,none(for icmp)"
	echo "   ex) $1 set_port 1 www,tcp,80,80 ping,none,0,0 dns,udp,53,53"
	echo "       $1 set_port 1               # clear blocked ports"
	echo
	echo " help - show help message"
	echo "   ex) $1 help"
}

preset1()
{
	POLICY=$1

	echo -n "generating policy $POLICY..."

	value "number" $POLICY 1
	value "name" $POLICY "Access Policy 1"
	value "status" $POLICY "Enabled"
	value "safe_surfing" $POLICY 0
	category $POLICY
	schedule "monday_time_blocks" $POLICY
	schedule "tuesday_time_blocks" $POLICY
	schedule "wednesday_time_blocks" $POLICY
	schedule "thursday_time_blocks" $POLICY
	schedule "friday_time_blocks" $POLICY
	schedule "saturday_time_blocks" $POLICY
	schedule "sunday_time_blocks" $POLICY
	devicelist $POLICY
	blocklist $POLICY
	whitelist $POLICY

	echo "done"
}

preset2()
{
	POLICY=$1
	
	echo -n "generating policy $POLICY..."

	value "number" $POLICY $POLICY
	value "name" $POLICY custom
	value "status" $POLICY "Enabled"
	value "safe_surfing" $POLICY 0
	category $POLICY 1 3-6 8-11 14-16 22 25-26 30 33 39 42-44 47 50-59 61-63 68-86 88	# Child Profile
	schedule "monday_time_blocks" $POLICY 0-18 42-47	# 12am ~ 9am, 9pm ~ 12pm
	schedule "tuesday_time_blocks" $POLICY 0-18 42-47	# 12am ~ 9am, 9pm ~ 12pm
	schedule "wednesday_time_blocks" $POLICY 0-18 42-47	# 12am ~ 9am, 9pm ~ 12pm
	schedule "thursday_time_blocks" $POLICY 0-18 42-47	# 12am ~ 9am, 9pm ~ 12pm
	schedule "friday_time_blocks" $POLICY 0-18 42-47	# 12am ~ 9am, 9pm ~ 12pm
	schedule "saturday_time_blocks" $POLICY
	schedule "sunday_time_blocks" $POLICY
	devicelist $POLICY 00:00:00:00:00:00^00000000-0000-0000-0000-000000000000
	blocklist $POLICY
	whitelist $POLICY

	echo "done"
}

preset()
{
	POLICY=1
	preset1 $POLICY
	syscfg set parental_control_policy_$POLICY pcp_$POLICY

	POLICY=2
	preset2 $POLICY
	syscfg set parental_control_policy_$POLICY pcp_$POLICY
	
	syscfg_set parental_control_policy_count $POLICY
}
####

#### main
APP=$0

if [ $# == 0 ]; then
  help $APP
  exit 1
fi

CMD=$1
shift

case $CMD in
  list)
    list
    ;;
  get)
    get $@
    ;;
  preset)
    preset
    syscfg commit
    ;;
  set_number)
    value "number" $@
    syscfg commit
    ;;
  set_name)
    value "name" $@
    syscfg commit
    ;;
  set_status)
    value "status" $@
    syscfg commit
    ;;
  set_sws)
    value "safe_surfing" $@
    syscfg commit
    ;;
  set_cat)
    category $@
    syscfg commit
    ;;
  set_mon)
    schedule "monday_time_blocks" $@
    syscfg commit
    ;;
  set_tue)
    schedule "tuesday_time_blocks" $@
    syscfg commit
    ;;
  set_wed)
    schedule "wednesday_time_blocks" $@
    syscfg commit
    ;;
  set_thu)
    schedule "thursday_time_blocks" $@
    syscfg commit
    ;;
  set_fri)
    schedule "friday_time_blocks" $@
    syscfg commit
    ;;
  set_sat)
    schedule "saturday_time_blocks" $@
    syscfg commit
    ;;
  set_sun)
    schedule "sunday_time_blocks" $@
    syscfg commit
    ;;
  set_dev)
    devicelist $@
    syscfg commit
    ;;
  set_blk)
    blocklist $@;
    syscfg commit
    ;;
  set_wht)
    whitelist $@;
    syscfg commit
    ;;
  set_port)
    port $@;
    syscfg commit
    ;;
  help)
    help $APP
    ;;
  *)
    help $APP
    ;;
esac
####
