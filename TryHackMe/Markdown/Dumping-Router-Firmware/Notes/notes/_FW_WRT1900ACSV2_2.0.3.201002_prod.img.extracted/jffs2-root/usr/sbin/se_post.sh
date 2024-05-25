#! /bin/sh

sysevent debug feecfeec &
echo "*** couldn't get ping response from syseventd" > /dev/console
echo "*** ps output ***" > /dev/console
ps -w  > /dev/console
if [ -f /var/log/syseventd.maps ];then
	echo "** dumping syseventd.maps **" > /dev/console
	cat /var/log/syseventd.maps > /dev/console
fi
pid=$(pgrep syseventd | sort | sed 1q)
echo "*** FDs opened by syseventd* (pid=$pid) ***" > /dev/console
ls -l /proc/$pid/fd | wc -l > /dev/console
echo "** dumping syseventd log **" > /dev/console
cat /var/log/syseventd.out > /dev/console
if [ -f /var/log/syseventd.out.0 ];then
    	echo "** dumping syseventd.out.0 **" > /dev/console
    	tail -n 20 /var/log/syseventd.out.0 > /dev/console
fi


