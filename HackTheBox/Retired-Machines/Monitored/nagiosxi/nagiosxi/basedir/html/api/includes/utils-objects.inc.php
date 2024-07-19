<?php
//
// New API utils and classes for Nagios XI 5
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//


function api_objects_unconfigured()
{
    $unconfigured = array();

    // Give error that unconfigured objects is disabled
    $listen = get_option('enable_unconfigured_objects', true);
    if (!$listen) {
        return array("error" => _("Unconfigured objects listener is disabled. Check your performance settings."));
    }

    // Get the list of unconfigured objects
    $newobjects = array();
    $data = null;
    clearstatcache();
    $f = get_root_dir() . "/var/corelog.newobjects";
    if (file_exists($f)) {
        $data = file_get_contents($f);
    }
    if (!empty($data)) {
        $newobjects = @unserialize($data);
    }

    // Format the unconfigured objects better
    if (count($newobjects) > 0) {
        foreach ($newobjects as $host => $obj) {

            // Add hosts
            if (!host_exists($host)) {
                $unconfigured[] = array('host_name' => $host,
                                        'last_seen' => $obj['last_seen']);
            }

            // Add services
            if (array_key_exists('services', $obj)) {
                if (count($obj['services']) > 0) {
                    foreach ($obj['services'] as $service_description => $last_seen) {
                        if (!service_exists($host, $service_description)) {
                            $unconfigured[] = array('host_name' => $host,
                                                    'service_description' => $service_description,
                                                    'last_seen' => $last_seen);
                        }
                    }
                }
            }

        }
    }

    return array('recordcount' => count($unconfigured),
                 'unconfigured' => $unconfigured);
}


function api_objects_get_rrd_json_output($args) {

    if (in_demo_mode()) {
        return array("error" => _("Can not use this action in demo mode."));
    }

    if (empty($args['host_name'])) {
        return array('error' => _('You must provide host_name.'));
    }

    include_once('../../includes/utils-rrdexport.inc.php');

    // get values and defaults
    $host = $args['host_name'];
    $service = (empty($args['service_description']) ? "_HOST_" : $args['service_description']);
    $start = "";
    $end = "";
    $step = "";
    $columns_to_display = array();
    $maxrows = "";

    // check for proper timestamps - if *you* want the data, *you* convert to timestamp
    if (!empty($args['start'])) {
        $start = date("U", $args['start']);
        if (!$start)
            return array("error" => _("Start time must be in Unix timestamp format."));
    }
    if (!empty($args['end'])) {
        $end = date("U", $args['end']);
        if (!$end)
            return array("error" => _("End time must be in Unix timestamp format."));
    }

    // check for proper step value, default to 300 if nothing or invalid value
    if (!empty($args['step']) && is_numeric($args['step']))
        $step = $args['step'];

    // check for proper step value, default to 300 if nothing or invalid value
    if (!empty($args['maxrows']) && is_numeric($args['maxrows']))
        $maxrows = $args['maxrows'];

    // check for columns specified - if found, attempt to build an array of acceptable ones
    if (!empty($args['columns'])) {
        if (is_array($args['columns'])) {
            foreach($args['columns'] as $column) {
                $columns_to_display[] = $column;
            }
        } else {
            $columns_to_display[] = $args['columns'];
        }
    }
    
    $output = get_rrd_data($host, $service, 'json', $start, $end, $step, $columns_to_display, $maxrows);

    if (!$output) {
        return array("error" => _("No valid data returned."));
    } else {
        return $output;
    }
}


function api_objects_get_bpi_data($type='json')
{
    define('CLI', true);

    global $bpi_config;
    global $bpi_errors;
    global $bpi_objects;
    global $bpi_errors;
    global $bpi_obj_count;
    global $bpi_state_count;
    global $bpi_unique;
    global $host_details;

    $bpi_config = true;
    $bpi_errors = '';
    $bpi_objects = array();
    $bpi_errors = '';
    $bpi_obj_count = 0;
    $bpi_state_count = 0;
    $bpi_unique = 0;
    $host_details = array();

    require_once('../../includes/components/nagiosbpi/inc.inc.php');

    $data = bpi_xml_fetch();

    if ($type == 'json' || $type == 'csv') {
        $json_arr = array();
        $data = simplexml_load_string($data);
        foreach ($data->group as $group) {
            $json_arr[strval($group['id'])] = array(
                'name' => strval($group->name),
                'title' => strval($group->title),
                'type' => strval($group->type),
                'status_text' => strval($group->status_text),
                'current_state' => intval($group->current_state),
                'problems' => intval($group->problems),
                'health' => floatval($group->health),
                'warning' => floatval($group->warning),
                'critical' => floatval($group->critical)
            );
        }
        $data = $json_arr;
        if ($type == 'csv') {
            $data = API::csv_format($data);
        }
    }

    return $data;
}
