<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  

require_once(dirname(__FILE__) . '/cp-common.inc.php');
// cp-common handles request initialization and authentication.

echo '<script>';
require_once(dirname(__FILE__) . '/../../js/dashlets.js');
echo '</script>';

// Grab all the needed variables
$host = grab_request_var('host', '');
$service = grab_request_var('service', '');
$track = grab_request_var('track', '');
$method = grab_request_var('method', 'Holt-Winters');
$period = grab_request_var('period', '1 week');
$width = grab_request_var('width', 650);
$height = grab_request_var('height', 300);

// Make the actual dashlet
$hostlist = base64_encode(serialize(array($host => array('service' => $service, 'track' => $track))));
$hostoptions = base64_encode(serialize(array($host => array('method' => $method, 'period' => $period))));
$dargs = array(
    DASHLET_ARGS => array(
        'hostlist' => $hostlist,
        'hostoptions' => $hostoptions,
        'host' => $host,
        'width' => $width,
        'height' => $height
    ),
);

display_dashlet('capacityplanning', '', $dargs, DASHLET_MODE_OUTBOARD);
