<?php
//
// Gauge Dashlet
//
// Copyright (c) 2013-2015 Nagios Enterprises, LLC.
//
// LICENSE:
//
// Except where explicitly superseded by other restrictions or licenses, permission
// is hereby granted to the end user of this software to use, modify and create
// derivative works or this software under the terms of the Nagios Software License, 
// which can be found online at:
//
// https://www.nagios.com/legal/licenses/
//

require_once(dirname(__FILE__) . '/../dashlethelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

$cmd = grab_request_var('cmd', '');

header('Content-Type: application/json');

if ($cmd == "noperfdata") {
	echo gauges_get_host_services();
} else {
	echo get_gauge_json();
}