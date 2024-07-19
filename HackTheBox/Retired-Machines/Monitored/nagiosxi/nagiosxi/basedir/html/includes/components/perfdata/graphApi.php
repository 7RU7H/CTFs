<?php
//
// Perfdata Output
//
// Copyright (c) 2008-2018 Nagios Enterprises, LLC.  All rights reserved.
//

//hide error message so it doesn't break the header declaration
ini_set("display_errors", "off");

require_once(dirname(__FILE__) . "/../componenthelper.inc.php");
require_once(dirname(__FILE__) . "/graphApi.inc.php");

// initialization stuff
pre_init();

// start session
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication(false);


// print the graph
print_graph();


function print_graph()
{
    $hostname    = urldecode(grab_request_var("host",""));
    $servicename = urldecode(grab_request_var("service",""));
    $source      = grab_request_var("source", 1);
    $view        = grab_request_var("view", "");
    $start       = grab_request_var("start", "");
    $end         = grab_request_var("end", "");

    // grab the cmd string for rrdtool
    $cmdString = fetch_graph_command($hostname, $servicename, $source, $view, $start, $end);

    // generate graph image
    header("Content-type: image/png");

    // execute command and direct output to browser
    passthru($cmdString, $ret);

    // log command being run on error
    if ($ret != 0) {
        $errString = "GRAPH ERROR: " . date("c") . "\n{$cmdString}\n\n";
        file_put_contents("/usr/local/nagios/var/graphapi.log", $errString, FILE_APPEND);
    }
}
