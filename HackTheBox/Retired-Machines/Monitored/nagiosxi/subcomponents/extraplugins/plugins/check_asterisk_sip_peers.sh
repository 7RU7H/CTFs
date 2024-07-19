#!/bin/bash
#
# Simple Asterisk Peer Check.
# Copyright (C) 2008 Marcus Rejås / Rejås Datakonsult
#
# Modified with perfdata by Peter Andersson
# http://www.it-slav.net/blogs/?p=123
# peter@it-slav.net
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#
# Very simple plugin that checks if a peer is ok. The peers needs "qualify=yes"
# in its configuration.
#
# A peer that is not registered or non-existent will result in error. If the
# peer is OK a short statusline (from Asterisk) is written. There is timing
# information suitable for graphing as well.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see .
#
# Example use of this script:
#
# sip:~# ./sip_check_peer mysecretary-100
# mysecretary-100/461762501 62.80.200.53 5060 OK (10 ms)
# sip:~#
#
#

if [ $# == 0 -o "$1" == "-h" -o  $# -gt 1 ]; then
	echo "Usage: $0"
	exit 3
fi

LINE=`/usr/sbin/asterisk -r -x "sip show peers" | grep $1 | grep "OK ("`

#
# This is a uggly. Just to check that the expression above does not match more
# then one line.
#
HITS=`/usr/sbin/asterisk -r -x "sip show peers" | grep $1 | grep "OK (" | wc -l`

if [ $HITS -gt 1 ]; then
	echo "ERROR: Multiple match, tweak your arguments or fix $0 :-) "
	exit 3
fi

if [ "$LINE" ]; then
	echo -n "OK: "
	echo -n $LINE
	#Create perdata
	echo -n "|time="
	echo $LINE | awk '{gsub(/\(/,"")};{gsub(/\)/,"")};{print $(NF-1)$NF}'
	exit 0
elif [ -z "$LINE" ]; then
	echo "CRITICAL: Something is wrong with $1";
	exit 2
else
	echo $LINE
	exit 2
fi

