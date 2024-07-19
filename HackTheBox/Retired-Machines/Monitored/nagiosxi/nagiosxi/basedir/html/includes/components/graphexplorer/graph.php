<?php
//
// Graph Explorer - Standalone Graph
// Copyright (c) 2008-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and pre-reqs
grab_request_vars();
check_prereqs();

// Check authentication
check_authentication(false);


route_request();


function route_request()
{
    global $request;

    $mode = grab_request_var("mode");
    switch ($mode) {
        default:
            graphexplorer_get_graph();
            break;
    }
}


function graphexplorer_get_graph()
{
    global $request;

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "_HOST_");
    $width = grab_request_var("width", "600");
    $height = grab_request_var("height", "260");
    $view = grab_request_var("view", "");
    $start = grab_request_var("start", "");
    $end = grab_request_var("end", "");

    $title = $host;
    $title .= ($service == "_HOST_") ? " Graph" : " : " . $service . " Graph";

    do_page_start(array("page_title" => $title), true);

    echo "<div id='scriptcontainer'></div>
    <div id='graphcontainer'></div>
    <script type='text/javascript'>  
    $(document).ready(function(){
        var host = '".urlencode($host)."';
        var service = '".urlencode($service)."';
        var args = 'height=".$height."&width=".$width."&type=timeline&host=' + host + '&service=' + service + '&div=graphcontainer&view=".$view."&start=".$start."&end=".$end."';
        var url = base_url + 'includes/components/graphexplorer/visApi.php?' + args;
        $('#scriptcontainer').load(base_url + 'includes/components/graphexplorer/visApi.php?' + args, function () {
        });
    });
    </script>";

    do_page_end(true);
}
