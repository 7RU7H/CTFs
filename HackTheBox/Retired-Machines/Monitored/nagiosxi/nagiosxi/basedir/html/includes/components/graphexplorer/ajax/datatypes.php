<?php
//
// API Access for Hosts/Services
// Copyright (c) 2016-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../../common.inc.php');
include_once(dirname(__FILE__) . '/../dashlet.inc.php');
require_once(dirname(__FILE__) . '/../visFunctions.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and pre-reqs
grab_request_vars();
check_prereqs();

// Check authentication
check_authentication(false);

$host = grab_request_var('host');
$service = grab_request_var('service');
$all = grab_request_var('all');
show_service_datatypes($host, $service, $all);
