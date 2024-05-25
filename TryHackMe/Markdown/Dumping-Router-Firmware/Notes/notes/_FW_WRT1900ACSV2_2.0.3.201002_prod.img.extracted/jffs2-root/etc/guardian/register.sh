#!/bin/sh

if [ $# == 0 ]; then
        echo "usage: $0 [rule file]";
        echo "   ex) $0 /tmp/guardian.ipt";
        exit 1;
fi

IPT_RUN=/etc/init.d/ipt_run.sh

# $1(message)
errlog()
{
	logger -p local7.error -t UTOPIA pc.info $1
}

$IPT_RUN iptables-restore -n < $1 2> /dev/null
if [ $? != 0 ]; then
	errlog "restoring $1 failed"
	if [ $# -gt 1 ]; then
		iptables-save > $2 2> /dev/null;
	fi;
	exit 1
fi;

exit 0
