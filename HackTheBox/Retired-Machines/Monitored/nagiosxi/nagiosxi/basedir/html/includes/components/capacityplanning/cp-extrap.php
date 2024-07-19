<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  

require_once(dirname(__FILE__) . '/cp-common.inc.php');

// Start session
init_session(false, false);

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs(false);
check_authentication(false);

route_request();

function route_request()
{
    $action = grab_request_var('cmd', null);

    $host = grab_request_var('host', null);
    $service = grab_request_var('service', null);
    $track = grab_request_var('track', null);

    // Set locale for the get_timeframe and get_extrapolation functions
    $language = $_SESSION['language'];
    setlocale(LC_CTYPE, $language.'.UTF-8');

    switch ($action) {
        case 'timeframe':
            $result = get_timeframe($host, $service, $track);
            break;
        case 'extrapolate':
            $options = grab_request_var('options', array());
            $result = get_extrapolation($host, $service, $track, $options);
            break;
        default:
            $result = '{"error": "Unknown command."}';
            break;
    }

    header('Content-type: application/json');
    print $result;
}
