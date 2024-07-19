<?php
//
// Nagios XI Mobile Interface
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

// Base XI code base
require_once(dirname(__FILE__) . '/../includes/common.inc.php');
// Mobile XI code base
require_once(dirname(__FILE__) . '/controllers/main-utils.php');
load_controllers();

// Initialization stuff
pre_init();
init_session();
check_mobile_authentication();
grab_request_vars();
decode_request_vars();

// Check prereqs and authentication
check_prereqs();
mobile_check_authentication();

mobile_page_start(_('Nagios XI Mobile'));

mobile_page_end();