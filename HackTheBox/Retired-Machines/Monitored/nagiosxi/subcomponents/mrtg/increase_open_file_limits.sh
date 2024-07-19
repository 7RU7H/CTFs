#!/bin/bash -e

# attempt to increase open file limits to correspond to the recent changes [5.3.0] in mrtg/switch wizard

limitfile="/etc/security/limits.conf"
numcheck="^[0-9]+$"

# check if the limits file exists
if [ -f $limitfile ] && [ -w $limitfile ]; then

	# check if global hard/soft limits are defined
	if grep -q "^\*\s*hard" $limitfile && grep -q "^\*\s*soft" $limitfile; then

		# if they are, we need to get their current values and increase them!
		global_hard=$(grep "^\*\s*hard\s*nofile" $limitfile | awk '{ print $4 }')
		global_soft=$(grep "^\*\s*soft\s*nofile" $limitfile | awk '{ print $4 }')

		# make sure we got sane [numeric] values
		if ! [[ $global_hard =~ $numcheck && $global_soft =~ $numcheck ]]; then
			echo "Attempting to read global hard/soft nofile limits failed. Check $limitfile and try again!" >&2
			exit 0
		fi

		# increase the limits!
		if [ $global_hard -lt 2048 ]; then
			new_global_hard=10000
			if ! sed -i "s/^\*\s*hard.*$global_hard/\* hard nofile $new_global_hard/" $limitfile; then
				echo "Inline sed for changing global hard nofile limit failed. Check $limitfile and try again!" >&2
				exit 0
			fi
		fi
		if [ $global_soft -lt 2048 ]; then
			new_global_soft=10000
			if ! sed -i "s/^\*\s*soft.*$global_soft/\* soft nofile $new_global_soft/" $limitfile; then
				echo "Inline sed for changing global soft nofile limit failed. Check $limitfile and try again!" >&2
				exit 0
			fi
		fi

	else

		if ! echo -e "* hard nofile 10000\n* soft nofile 10000\n" >> $limitfile; then
			echo "Unable to write to $limitfile. Check permissions and try again!" >&2
			exit 0
		fi
	fi


	# check if root hard/soft limits are defined
	if grep -q "^root\s*hard" $limitfile && grep -q "^root\s*soft" $limitfile; then

		# if they are, we need to get their current values and increase them!
		root_hard=$(grep "^root\s*hard\s*nofile" $limitfile | awk '{ print $4 }')
		root_soft=$(grep "^root\s*soft\s*nofile" $limitfile | awk '{ print $4 }')

		# make sure we got sane [numeric] values
		if ! [[ $root_hard =~ $numcheck && $root_soft =~ $numcheck ]]; then
			echo "Attempting to read root hard/soft nofile limits failed. Check $limitfile and try again!" >&2
			exit 0
		fi

		# increase the limits!
		if [ $root_hard -lt 2048 ]; then
			new_root_hard=10000
			if ! sed -i "s/^root\s*hard.*$root_hard/root hard nofile $new_root_hard/" $limitfile; then
				echo "Inline sed for changing root hard nofile limit failed. Check $limitfile and try again!" >&2
				exit 0
			fi
		fi
		if [ $root_soft -lt 2048 ]; then
			new_root_soft=10000
			if ! sed -i "s/^root\s*soft.*$root_soft/root soft nofile $new_root_soft/" $limitfile; then
				echo "Inline sed for changing root soft nofile limit failed. Check $limitfile and try again!" >&2
				exit 0
			fi
		fi

	else

		if ! echo -e "root hard nofile 10000\nroot soft nofile 10000\n" >> $limitfile; then
			echo "Unable to write to $limitfile. Check permissions and try again!" >&2
			exit 0
		fi
	fi
fi