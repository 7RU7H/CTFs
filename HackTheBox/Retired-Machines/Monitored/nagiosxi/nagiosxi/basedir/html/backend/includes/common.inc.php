<?php
//
// Common Backend Includes
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

// Backend-specific routines
require_once(dirname(__FILE__) . '/errors.inc.php');
require_once(dirname(__FILE__) . '/utils.inc.php');
require_once(dirname(__FILE__) . '/handlers.inc.php');

// Use frontend logic for most stuff
require_once(dirname(__FILE__) . '/../../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../../includes/utils.inc.php');


// SYSTEM STATISTICS *************************************************************************
function fetch_sysstat_info()
{
    global $request;
    $output = get_sysstat_data_xml_output($request);
    print backend_output($output);
}


// USERS (FRONTEND)  *************************************************************************
function fetch_users()
{
    global $request;
    $output = get_xml_users($request);
    print backend_output($output);
}
