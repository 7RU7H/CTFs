<?php
//
// Backend API Error Functions
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

////////////////////////////////////////////////////////////////////////
// ERROR HANDLING FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * Returns an formatted error
 *
 * @param $msg
 */
function handle_backend_error($msg)
{
    output_backend_header();

    // Grab the type and return based on that
    $outputtype = strtolower(grab_request_var("outputtype", "xml"));
    if ($outputtype == "json") {
        $data = array("error" => 1,
            "message" => $msg);
        print backend_output($data);
    } else {
        echo "<error>\n<errormessage>$msg</errormessage>\n</error>\n";
    }
    exit;
}

/**
 * Handles database errors
 *
 * @param string $dbh
 */
function handle_backend_db_error($dbh = "")
{
    global $DB;

    $errmsg = "";
    if (have_value($dbh)) {
        $errmsg = $DB[$dbh]->ErrorMsg();
    }

    handle_backend_error("DB Error: " . $errmsg);
}
