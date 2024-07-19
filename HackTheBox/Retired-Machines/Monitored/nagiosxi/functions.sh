#!/bin/bash

usage() {
	fmt -s -w $(tput cols) <<-EOF

		Nagios XI installer script
		Copyright 2009-2019, Nagios Enterprises LLC.
		License:
		    Nagios Software License <http://assets.nagios.com/licenses/nagios_software_license.txt>
		Support:
		    XI Support Mailing List <customersupport@nagios.com> (customers only)
		    Community Forums <http://support.nagios.com/forum/>

		Usage: fullinstall [options...]

		Options:
		    -h | --help
		        Display this help text
		    -n | --non-interactive
		        Assume defaults for all questions (for scripted installs)
		    -p | --mysql-password
		        Specify custom MySQL root password.
		    -v | --version
		        Show the version of XI to be installed (and existing version, for upgrades)

		IMPORTANT: This script should only be used on a 'clean' (new) install of your operating system. Do NOT use this on a system that has been tasked with other purposes or has an existing install of Nagios Core. To create such a clean install you should have selected ONLY the 'Base' package in the OS installer.

		Supported Operating Systems:
		    CentOS and RHEL 7, 8, 9
		    Oracle 7, 8
		    Ubuntu LTS 18, 20, 22
		    Debian 10, 11

	EOF
}

# Wrapper function for installation scripts
run_sub() {
	echo "Running '$1'..."

	# Run the command and copy output to installer log
	# Fail file is a portable bourne shell alternative to $PIPESTATUS
	FAILFILE=".fail-$$"
	rm -f "$FAILFILE"
	(eval "$@" 2>&1 || echo $? > "$FAILFILE") | tee -a "$log"
	echo "RESULT=$(cat "$FAILFILE" 2>/dev/null || echo 0)"
	if [ -f "$FAILFILE" ]; then
		cat >&2 <<-EOF

			===================
			INSTALLATION ERROR!
			===================
			Installation step failed - exiting.
			Check for error messages in the install log (install.log).

			If you require assistance in resolving the issue, please include install.log
			in your communications with Nagios XI technical support.

			The script that failed was: '$1'
		EOF
		exit 1
	fi
	rm -f "$FAILFILE"
}

# Check that /sbin & /usr/sbin are in $PATH
path_is_ok() {
	echo "$PATH" \
	| awk 'BEGIN{RS=":"} {p[$0]++} END{if (p["/sbin"] && p["/usr/sbin"]) exit(0); exit(1)}'
}
