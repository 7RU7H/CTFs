#!/bin/bash
 
#pnp component sanity check

function zipit() {
	:
}

#~ Include general library (should go in all sanity scripts.)
if [ ! -f /usr/local/nagiosxi/html/includes/components/sanitychecks/sanitylib.sh ];then
    echo "Sanity Checks Component not installed"
    exit 1
else 
    . /usr/local/nagiosxi/html/includes/components/sanitychecks/sanitylib.sh
fi

do_these_files_exist $COMPONENTS/pnp/pnp.inc.php \
	$COMPONENTS/pnp/pnpproxy.inc.php \
	$COMPONENTS/pnp/js/pnp.js

is_component $COMPONENTS/pnp/pnp.inc.php

print_results
