#!/usr/bin/env python

"""
Written by Francois Meehan (Cedval Info)
First release 2004/09/15
Modified by Nagios Enterprises, LLC.

This script receives input from sec.pl concerning translated snmptraps

*** Important note: sec must send DATA within quotes


Ex: ./snmptraphandling.py <HOST> <SERVICE> <SEVERITY> <TIME> <PERFDATA> <DATA>
"""

import sys, os, stat, signal
signal.alarm(15)


def printusage():
	print("usage: snmptraphandling.py <HOST> <SERVICE> <SEVERITY> <TIME> <PERFDATA> <DATA>")
	sys.exit()


def check_arg():
	try:
		host = sys.argv[1]
		if host.startswith("UDP/IPv6"):
			try:
				import socket
				host = socket.gethostbyaddr(sys.argv[1].partition('[')[-1].rpartition(']')[0])[0]
			except:
				host = sys.argv[1].partition('[')[-1].rpartition(']')[0]
	except:
		printusage()
	try:
		service = sys.argv[2]
	except:
		printusage()
	try:
		severity = sys.argv[3]
	except:
		printusage()
	try:
		mytime = sys.argv[4]
	except:
		printusage()
	try:
		if sys.argv[5] == '':
			mondata_res = sys.argv[6]
		else:
			mondata_res = sys.argv[6] + " / " + sys.argv[5]
	except:
		printusage()
	return (host, service, severity, mytime, mondata_res)


def get_return_code(severity):
	severity = severity.upper()
	if severity == "INFORMATIONAL":
		return_code = "0"
	elif severity == "NORMAL":
		return_code = "0"
	elif severity == "OK":
		return_code = "0"
	elif severity == "SEVERE":
		return_code = "2"
	elif severity == "MAJOR":
		return_code = "2"
	elif severity == "CRITICAL":
		return_code = "2"
	elif severity == "WARNING":
		return_code = "1"
	elif severity == "MINOR":
		return_code = "1"
	elif severity == "UNKNOWN":
		return_code = "3"
	else:
		printusage()
	return return_code


def post_results(host, service, mytime, mondata_res, return_code):
	if os.path.exists('/usr/local/nagios/var/rw/nagios.cmd') and stat.S_ISFIFO(os.stat('/usr/local/nagios/var/rw/nagios.cmd').st_mode):
		try:
			output = open('/usr/local/nagios/var/rw/nagios.cmd', 'w')
		except IOError:
			output = open('/etc/snmp/nagios-check-storage', 'a+')
		if service == 'PROCESS_HOST_CHECK_RESULT':
			results = "[" + mytime + "] " + "PROCESS_HOST_CHECK_RESULT;" \
				+ host + ";" + return_code + ";" + mondata_res + "\n"
			
		else:
			results = "[" + mytime + "] " + "PROCESS_SERVICE_CHECK_RESULT;" \
				+ host + ";" + service + ";" \
				+ return_code + ";" + mondata_res + "\n"
		output.write(results)


# Main routine...
if __name__ == '__main__':
	(host, service, severity, mytime, mondata_res) = check_arg()  # validating
																  # parameters
	return_code = get_return_code(severity)
	post_results(host, service, mytime, mondata_res, return_code)
