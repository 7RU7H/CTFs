#!/bin/env php -q
<?php
//
// Recurring Downtime
// Check for scheduled recurring downtime and schedule as required
//
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

define('SUBSYSTEM', 1);

require_once(dirname(__FILE__) . '/../html/config.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/utils.inc.php');
require_once(get_component_dir_base('xicore/recurringdowntime.inc.php'));


///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// Default Values

// Only schedule any downtime occuring until this time
// in seconds
$max_lookahead_time = 60 * 60 * 24 * 6;

// The minimum outage duration we'll accept
// in minutes
$minimum_outage_duration = 5;

// Enable verboseging (set here or use --verbose)
$verbose = true;


///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// Execution

init_session();
parse_options();

schedule_downtimes();


///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// Functions

// Return current version
function get_version()
{
    return '1.0.0';
}


// Parse arguments/options passed to script
function parse_options()
{
    $long_options = array('help', 'version', 'verbose');
    $options = getopt('hvV', $long_options);
    $o = $options;

    if (isset($o['h']) || isset($o['help'])) {
        print_help_and_exit();
    }

    if (isset($o['v']) || isset($o['version'])) {
        print_version_and_exit();
    }

    if (isset($o['V']) || isset($o['verbose'])) {
        enable_verbose_messaging();
    }
}


// Print a fatal error and exit
function _error_exit($msg)
{
    $msg = trim($msg);
    $msg = "FATAL: {$msg}\n";

    echo $msg;

    // Now print to the actual [apache] error log
    // in case it isn't picked up otherwise
    $msg = trim($msg);
    $msg = "RECURRING DOWNTIME {$msg}";
    error_log($msg);

    exit(1);
}


// Print verbose message if verboseging enabled
function _verbose($msg)
{
    global $verbose;

    if ($verbose) {
        echo "{$msg}\n";
    }
}


// Print seperator line (for consistency!)
function print_seperator()
{
    echo "-------------------------\n";
}


// Print header (script name)
function print_header()
{
    echo "\nNagios Recurring Downtime\n";
    print_seperator();
}


// Print version information
function print_version()
{
    $version = get_version();

    print_header();
    echo "(c) 2018  Nagios Enterprises\n";
    echo "Version:  {$version}\n";
}


// Print usage information
function print_usage()
{
    global $argv;

    echo "Usage: {$argv[0]} -h|--help -v|--version -d|--verbose -f|--nagios-config-file=[FILE]\n";
    echo "\n";
    echo "\n";
    echo "    -h, --help                   Print help and exit\n";
    echo "    -v, --version                Print version info and exit\n";
    echo "    -d, --verbose                  Enable script verboseging\n";
    print_seperator();
}


// Print help information and exit
function print_help_and_exit()
{
    global $argv;
    global $default_nagios_config_file;

    print_version();
    print_seperator();
    print_usage();
    echo "This program will parse the recurring downtime configuration file\n";
    echo "and schedule the necessary downtime (within the specified amount of time)\n";
    print_seperator();
    echo "\n";
    exit();
}


// Print version information and exit
function print_version_and_exit()
{
    print_version();
    exit();
}


// Enable verbose output
function enable_verbose_messaging()
{
    global $verbose;
    $verbose = true;
}


// This function is the main driver
// we check the recurring config
// against the objects and existing downtimes
// and against the specific vars
// to make sure we're scheduling appropriately
function schedule_downtimes()
{
    global $minimum_outage_duration;

    _verbose("schedule_downtimes() called");

    // grab the cfg
    $recurring_cfg = recurringdowntime_get_cfg();
    // print_r($recurring_cfg);

    $downtimes = get_downtimes();
    // print_r($downtimes);

    foreach ($recurring_cfg as $id => $recurring_downtime) {

        // for brevity
        $r = $recurring_downtime;

        $message = "";
        $objects = array();

        // check duration first
        if ($r['duration'] < $minimum_outage_duration) {
            _verbose("Duration ({$r['duration']} m) is shorter than minimum ({$minimum_outage_duration} m)");
            continue;
        }

        // check to see if the object exists before we do anything else
        if (isset($r['hostgroup_name'])) {
            // hostgroup

            if (!hostgroup_exists($r['hostgroup_name'])) {

                _verbose("No hostgroup found: {$r['hostgroup_name']}");
                continue;
            }

            $objects = get_hostgroup_objects($r['hostgroup_name'], !empty($r['svcalso']));
            $message = "hostgroup: {$r['hostgroup_name']}, services: " . (!empty($r['svcalso']) ? 'yes' : 'no');
        }

        else if (isset($r['servicegroup_name'])) {
            // servicegroup

            if (!servicegroup_exists($r['servicegroup_name'])) {

                _verbose("No servicegroup found: {$r['servicegroup_name']}");
                continue;
            }

            $objects = get_servicegroup_objects($r['servicegroup_name']);
            $message = "servicegroup: {$r['servicegroup_name']}";
        }

        else if (isset($r['service_description'])) {
            // service

            if ((!isset($r['host_name']) || 
                !service_exists($r['host_name'], $r['service_description'])) &&
                strpos($r['service_description'], '*') === false) {

                _verbose("No service found: {$r['host_name']} - {$r['service_description']}");
                continue;
            }

            $objects = get_service_objects($r['host_name'], $r['service_description']);
            if (!empty($objects)) {
                $message = "host: {$r['host_name']}, service: {$r['service_description']}";
            } else {
                _verbose("No services found for: {$r['host_name']} - {$r['service_description']}");
                continue;
            }
        }

        else if (isset($r['host_name'])) {
            // host

            if (!host_exists($r['host_name'])) {

                _verbose("No host found: {$r['host_name']}");
                continue;
            }

            $objects = get_host_objects($r['host_name'], !empty($r['svcalso']));
            $message = "host: {$r['host_name']}, services: " . (!empty($r['svcalso']) ? 'yes' : 'no');
        }

        _verbose("\n\nChecking recurring config id: {$id}");
        _verbose("{$message}");
        _verbose("**************************************************************\n");

        // get the next scheduled time based on specific parameters
        $start_time = get_next_scheduled_time($r['time'], $r['days_of_week'], $r['days_of_month'], $r['months_of_year']);
        if ($start_time == false) {
            _verbose("no suitable time candidate found, skipping");
            continue;
        }

        // loop through all of the objects we built and see if they're already scheduled or not
        foreach ($objects as $i => $object) {

            if (!isset($object['host'])) {
                continue;
            }

            $host_name = $object['host'];
            $service_description = '';

            if (!empty($object['service'])) {
                $service_description = $object['service'];
            }

            $schedule = true;

            // Check for a downtime for the host/service 
            foreach ($downtimes as $d_id => $downtime) {

                if (empty($downtime['host_name'])) {
                    continue;
                }

                if (!isset($downtime['service_description'])) {
                    $downtime['service_description'] = '';
                }

                // Check for a host/service name combo (empty service is fine)
                if ($downtime['host_name'] == $host_name && $downtime['service_description'] == $service_description) {

                    // Verify if our start time matches the downtime we found
                    if ($downtime['start_time'] != $start_time) {
                        continue;
                    }

                    // Check duration for the downtime we did not replace vs recurring one
                    if ($downtime['duration'] >= $r['duration']) {
                        _verbose("Downtime exists with start_time: {$start_time}, and duration {$downtime['duration']} seconds ..");
                        _verbose("NOT SCHEDULING");
                    }

                    $schedule = false;
                    break;
                }

            }

            if ($schedule) {

                $comment = "AUTO: Automatically scheduled for service";
                if (empty($service_description)) {
                    $comment = "AUTO: Automatically scheduled for host";
                }

                if (!empty($r['comment'])) {
                    $comment = "AUTO: {$r['comment']}";
                }

                $user = 'nagiosadmin';
                if (!empty($r['user'])) {
                    $user = $r['user'];
                }

                $minutes = $r['duration'] * 60;

                $args = array(
                    'comment_data'   => $comment,
                    'comment_author' => $user,
                    'trigger_id'     => 0,
                    'start_time'     => $start_time,
                    'end_time'       => $start_time + $minutes,
                    'fixed'          => 1,
                    'duration'       => $minutes,
                    'host_name'      => $host_name
                );

                $cmd = NAGIOSCORE_CMD_SCHEDULE_HOST_DOWNTIME;

                if (!empty($service_description)) {
                    $args['service_name'] = $service_description;
                    $cmd = NAGIOSCORE_CMD_SCHEDULE_SVC_DOWNTIME;
                } else if (!empty($r['childoptions'])) {
                    if ($r['childoptions'] == 1) {
                        $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_TRIGGERED_HOST_DOWNTIME;
                    } else if ($r['childoptions'] == 2) {
                        $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_HOST_DOWNTIME;
                    }
                }

                $core_cmd = core_get_raw_command($cmd, $args);
                $success = core_submit_command($core_cmd, $output);
                if ($success) {
                    $svc_msg = '';
                    if (!empty($service_description)) {
                        $svc_msg = ", service: {$service_description}";
                    }

                    _verbose("Successfully submitted downtime for host: {$host_name}{$svc_msg}");
                } else {

                    _verbose("Something went wrong submitting command: {$output}");
                }

            }
        }


    }
}


// Return hostgroup objects as a list of hosts, or list of host/services
function get_hostgroup_objects($hostgroup_name, $svcalso = false)
{
    $xml = get_xml_hostgroup_member_objects(array('hostgroup_name' => $hostgroup_name));
    if (empty($xml->recordcount)) {
        return array();
    }

    $hostgroup_members = $xml->hostgroup->members->host;
    if ($xml->recordcount == 1) {
        $hostgroup_members = array($hostgroup_members);
    }

    $objects = array();

    foreach ($hostgroup_members as $id => $member) {

        $host = (string) $member->host_name;

        $services = array();
        if ($svcalso) {
            $services = get_service_objects($host);
        }

        $objects[] = array('host' => $host);

        $objects = array_merge($objects, $services);
    }

    return $objects;
}


// return servicegroup objects as a list of hosts,services
function get_servicegroup_objects($servicegroup_name)
{
    $xml = get_xml_servicegroup_member_objects(array('servicegroup_name' => $servicegroup_name));
    if (empty($xml->recordcount)) {
        return array();
    }

    $servicegroup_members = $xml->servicegroup->members->service;
    if ($xml->recordcount == 1) {
        $servicegroup_members = array($servicegroup_members);
    }

    $objects = array();

    foreach ($servicegroup_members as $id => $member) {

        $objects[] = array(
            'host'    => (string) $member->host_name,
            'service' => (string) $member->service_description,
            );
    }

    return $objects;
}


// Return a list of host,service objects
function get_service_objects($host_name, $service_description = "")
{
    $args = array("host_name" => $host_name);
    if (!empty($service_description)) {
        $limit = $service_description;

        // Verify if we are using a * wildcard or not
        if (strpos($service_description, '*') !== false) {
            $first = substr($service_description, 0, 1);
            $last = substr($service_description, -1);
            if ($first == '*' && $last == '*') {
                $limit = "lk:" . trim($service_description, '*');
            } else if ($first == '*') {
                $limit = "lke:" . ltrim($service_description, '*');
            } else if ($last == '*') {
                $limit = "lks:" . rtrim($service_description, '*');
            }
        }

        $args['service_description'] = $limit;
    }

    $xml = get_xml_service_objects($args);
    if (empty($xml->recordcount)) {
        return array();
    }

    $services = $xml->service;
    if ($xml->recordcount == 1) {
        $services = array($services);
    }

    $objects = array();

    foreach ($services as $id => $service) {

        $host = (string) $service->host_name;
        $service = (string) $service->service_description;

        $objects[] = array(
            'host'    => $host,
            'service' => $service,
            );
    }

    return $objects;
}


// Return either a list with a single host entry, or that plus it's host,svc objects
function get_host_objects($host_name, $svcalso = false)
{
    $objects = array();
    $objects[] = array('host' => $host_name);

    if ($svcalso) {
        $objects = array_merge($objects, get_service_objects($host_name));
    }

    return $objects;
}


// Given a series of parameters, find the next time
// given that it's within the max_lookahead_time specified
function get_next_scheduled_time($time, $days_of_week, $days_of_month, $months_of_year)
{
    _verbose("get_next_scheduled_time('$time', '$days_of_week', '$days_of_month', '$months_of_year') called");

    global $max_lookahead_time;

    $time = trim($time);
    $days_of_week = trim($days_of_week, " ,");
    $days_of_month = trim($days_of_month, " ,");
    $months_of_year = trim($months_of_year, " ,");

    if (!empty($days_of_week)) {
        $days_of_week = explode(",", $days_of_week);
    }
    if (!empty($days_of_month)) {
        $days_of_month = explode(",", $days_of_month);
    }
    if (!empty($months_of_year)) {
        $months_of_year = explode(",", $months_of_year);
    }

    $current_timestamp = time();
    $candidate_timestamp = strtotime($time);
    _verbose("current_timestamp:   {$current_timestamp}, " . date("Y-m-d H:i", $current_timestamp));
    _verbose("candidate_timestamp: {$candidate_timestamp}, " . date("Y-m-d H:i", $candidate_timestamp));

    if ($candidate_timestamp < $current_timestamp) {
        $candidate_timestamp = strtotime("tomorrow $time");
        _verbose("candidate_timestamp in past, adjusting..");
        _verbose("candidate_timestamp: {$candidate_timestamp}, " . date("Y-m-d H:i", $candidate_timestamp));
    }

    while ($candidate_timestamp <= ($current_timestamp + $max_lookahead_time)) {

        $match_days_of_week = true;
        $match_days_of_month = true;
        $match_months_of_year = true;

        if (is_array($days_of_week) && (count($days_of_week) > 0)) {
            $candidate_day_of_week = strtolower(date('D', $candidate_timestamp));
            _verbose("got candidate_day_of_week: {$candidate_day_of_week}, checking: " . implode(",", $days_of_week));
            $match_days_of_week = in_array($candidate_day_of_week, $days_of_week);
            if ($match_days_of_week) {
                _verbose("check successful");
            }
        }

        if (is_array($days_of_month) && (count($days_of_month) > 0)) {
            $candidate_day_of_month = date('j', $candidate_timestamp);
            _verbose("got candidate_day_of_month: {$candidate_day_of_month}, checking: " . implode(",", $days_of_month));
            $match_days_of_month = in_array($candidate_day_of_month, $days_of_month);
            if ($match_days_of_month) {
                _verbose("check successful");
            }
        }

        if (is_array($months_of_year) && (count($months_of_year) > 0)) {
            $candidate_month_of_year = strtolower(date('M', $candidate_timestamp));
            _verbose("got candidate_month_of_year: {$candidate_month_of_year}, checking: " . implode(",", $months_of_year));
            $match_months_of_year = in_array($candidate_month_of_year, $months_of_year);
            if ($match_months_of_year) {
                _verbose("check successful");
            }
        }

        if ($match_days_of_week && $match_days_of_month && $match_months_of_year) {
            _verbose("all parameters match, re-adjusting candidate for proper time");
            $candidate_timestamp = strtotime(date("Y-m-d", $candidate_timestamp) . " {$time}");
            _verbose("candidate_timestamp: {$candidate_timestamp}000, " . date("Y-m-d H:i", $candidate_timestamp));
            return $candidate_timestamp;
        }

        $candidate_timestamp = strtotime("+1 day", $candidate_timestamp);
        _verbose("candidate_timestamp: {$candidate_timestamp}," . date("Y-m-d H:i", $candidate_timestamp));
    }

    _verbose("all candidate_timestamps outside of max_lookahead_time ({$max_lookahead_time}), nothing to see here...");
    return false;
}


function get_downtimes()
{
    $downtimes = array();

    $dts = get_scheduled_downtime();
    if (!empty($dts)) {
        foreach ($dts as $d) {

            $start_time = convert_datetime_to_timestamp($d['scheduled_start_time']);
            $end_time = convert_datetime_to_timestamp($d['scheduled_end_time']);

            $duration = ($end_time - $start_time) / 1000;

            $downtime = array(
                'downtime_id'   => $d['internal_downtime_id'],
                'type'          => $d['downtime_type'],
                'host_name'     => $d['host_name'],
                'entry_time'    => convert_datetime_to_timestamp($d['entry_time']),
                'start_time'    => intval($start_time / 1000),
                'end_time'      => intval($end_time/ 1000),
                'author'        => $d['author_name'],
                'comment'       => $d['comment_data'],
                'fixed'         => $d['is_fixed'],
                'duration'      => intval($duration),
                'triggered_by'  => $d['triggered_by_id'],
                'is_in_effect'  => $d['was_started'],
                );

            if (!empty($d['service_description'])) {
                $downtime['service_description'] = $d['service_description'];
            }

            $downtimes[] = $downtime;
        }
    }

    return $downtimes;
}