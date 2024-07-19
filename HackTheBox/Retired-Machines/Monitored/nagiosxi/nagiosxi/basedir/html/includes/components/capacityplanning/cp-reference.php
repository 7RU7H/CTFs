<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  

require_once('cp-common.inc.php');
// cp-common handles request initialization and authentication.


define("PDATADIR", pnp_get_perfdata_dir());


route_request();

function route_request()
{
    $host = grab_request_var('host', null);
    $service = grab_request_var('service', null);

    if ($host === null) {
        // If the host is set to null, then we need to get a list of
        // possible hosts. Please note this will only return hosts that
        // have registered perfdata.
        $output = get_host_list();
    } else if ($service === null) {
        // If the host is not null, but the service is, we need to
        // return the available service list for the host.
        $output = get_service_list($host);
    } else if ($service !== null && $host !== null) {
        // Now we just need the track info for the service. Since this
        // is directly applicable to what's in the RRD, we are going
        // to look at what's in the RRD tracks.
        $output = get_track_list($host, $service);
    }

    header('Content-type: application/json');
    print json_encode($output);
}

/**
 * Returns a list of avaiable hosts.
 */
function get_host_list()
{
    $args = array('brevity' => 1);
    $oxml = get_xml_host_objects($args);

    $hosts = array();
    if ($oxml && $oxml->host) {
        foreach ($oxml->host as $host) {
            $hosts[] = strval($host->host_name);
        }
    }

    $final = array();
    foreach ($hosts as $host) {
        $s_host = pnp_sanitize_filename($host);
        $dir = PDATADIR . "/$s_host";
        if (is_dir($dir)) {
            $final[] = $host;
        }
    }

    return $final;
}

/**
 * Returns a list of available services for a host.
 */
function get_service_list($host)
{
    $args = array('brevity' => 1, 'host_name' => $host);
    $oxml = get_xml_service_objects($args);

    $services = array();
    if ($oxml) {
        $services[] = '_HOST_';
        if ($oxml->service) {
            foreach ($oxml->service as $service) {
                $services[] = strval($service->service_description);
            }
        }
    }

    $s_host = pnp_sanitize_filename($host);
    $final = array();
    foreach ($services as $service) {
        $s_service = pnp_sanitize_filename($service);
        $file = PDATADIR . "/$s_host/$s_service.rrd";
        if (is_file($file)) {
            $final[] = $service;
        }
    }

    return $final;
}

/**
 * Returns a list of tracks available to this service.
 */
function get_track_list($host, $service)
{
    $host = pnp_sanitize_filename($host);
    $service = pnp_sanitize_filename($service);
    $xml = simplexml_load_file(PDATADIR . "/$host/$service.xml");

    $tracks = array();
    if ($xml && $xml->DATASOURCE) {
        foreach ($xml->DATASOURCE as $ds) {
            $tracks[] = strval($ds->NAME);
        }
    }

    return $tracks;
}
