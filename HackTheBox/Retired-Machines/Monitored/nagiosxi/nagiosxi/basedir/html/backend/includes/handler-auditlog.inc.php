<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

///////////////////////////////////////////////////////////////////////////////////////////
//
// AUDIT LOG FUNCTIONS
//
///////////////////////////////////////////////////////////////////////////////////////////

function fetch_auditlog()
{
    global $request;
    $output = get_auditlog_xml_output($request);
    print backend_output($output);
}
