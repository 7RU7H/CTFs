<?php
//
// XI Status Functions
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__).'/../componenthelper.inc.php');


// Initialization stuff
pre_init();
init_session(true);

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    $cmd = grab_request_var('cmd', '');

    switch ($cmd) {

        case 'gethost':
            $host_name = grab_request_var('host', '');
            $host = get_data_host_status(array('host_name' => $host_name, 'brevity' => 1));
            $host = $host[0];
            $services = get_data_service_status(array('host_name' => $host_name, 'brevity' => 1));
            $host['services'] = $services;
            print json_encode($host);
            break;

        case 'getservices':
            $host_name = grab_request_var('host', '');
            $services = utils_status_get_services($host_name);
            $data = array('requested_at' => get_datetime_string(time()), 'services' => $services);
            print json_encode($data);
            break;

    }
}
