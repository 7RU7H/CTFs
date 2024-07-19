<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
// 

require_once(dirname(__FILE__) . '/common.inc.php');


// LOG ENTRIES *************************************************************************
function fetch_logentries()
{
    global $request;
    $output = get_logentries_xml_output($request);
    print backend_output($output);
}


// STATE HISTORY *************************************************************************
function fetch_statehistory()
{
    global $request;
    $output = get_statehistory_xml_output($request);
    print backend_output($output);
}


// HISTORICAL STATUS *************************************************************************
function fetch_historical_host_status()
{
    global $request;
    $output = get_historical_host_status_xml_output($request);
    print backend_output($output);
}

function fetch_historical_service_status()
{
    global $request;
    $output = get_historical_service_status_xml_output($request);
    print backend_output($output);
}


// NOTIFICATIONS *************************************************************************
function fetch_notifications()
{
    global $request;
    $output = get_notifications_xml_output($request);
    print backend_output($output);
}

function fetch_notifications_with_contacts()
{
    global $request;
    $output = get_notificationswithcontacts_xml_output($request);
    print backend_output($output);
}


// TOP ALERT PRODUCERS ***************************************************
function fetch_top_alert_producers()
{

    // Determine start/end times based on period
    $reportperiod = grab_request_var("reportperiod", "today");
    $records = grab_request_var("records", "10");
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":0"
    );

    $output = get_topalertproducers_xml_output($args);
    print backend_output($output);
}


// Alert Histogram ************************
function fetch_alert_histogram()
{

    // Determine start/end times based on period
    $reportperiod = grab_request_var("reportperiod", "today");
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime
    );

    $output = get_histogram_xml_output($args);
    print backend_output($output);
}   
