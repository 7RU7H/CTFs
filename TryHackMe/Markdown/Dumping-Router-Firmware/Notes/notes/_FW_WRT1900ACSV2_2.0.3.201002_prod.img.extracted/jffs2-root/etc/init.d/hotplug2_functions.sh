#!/bin/sh
SetTimer()
{
        sysevent set factory_reset_timeout_sec $1
}
GetTimer()
{
        RET_VAL=$(sysevent get factory_reset_timeout_sec)
        if [ -z ${RET_VAL} ]; then
		RET_VAL=0
	fi
        echo ${RET_VAL}
}
DecTimer()
{
        TIME_OUT=$(GetTimer)
        if [ $TIME_OUT -lt 1 ]; then
                TIME_OUT=1;
        fi
        TIME_OUT=$(expr $TIME_OUT - 1)
        SetTimer $TIME_OUT
}
FactoryResetAfter()
{
	SetTimer $1
        while [ $(GetTimer) -gt 0 ]
        do
                if [ "released" = "`sysevent get reset_hw_button`" ] ; then
                	echo "" > /dev/console
                	echo "Ignore the reset within 10 seconds" > /dev/console
                	exit
                fi
                echo $(GetTimer)
                sleep 1
                DecTimer
        done
        SetTimer 0
	if [ "pressed" = "`sysevent get reset_hw_button`" ] ; then
		echo "" > /dev/console
		echo "RESET TO FACTORY SETTING EVENT DETECTED" > /dev/console
		echo "PLEASE WAIT WHILE REBOOTING THE DEVICE..." > /dev/console
		/sbin/utcmd factory_reset
	fi
}
