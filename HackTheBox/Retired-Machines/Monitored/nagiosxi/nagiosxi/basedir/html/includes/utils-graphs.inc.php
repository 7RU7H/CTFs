<?php
//
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//


///////////////////////////////////////////////////////////////////////////////////////////
// GRAPH FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


/**
 * @param array $args -> *host, service, start, end (only hostname is required)
 * @returns: array $data['sets']      -> array of rrd data in a csv STRING formatted for highcharts
 *                 $data['start']     -> starting time of the data
 *                 $data['increment'] -> timespan between data points
 *                 $data['count']     -> total number of data points for graph
 */
function fetch_rrd($args)
{
    $host = isset($args['host']) ? $args['host'] : die("No host specified"); // Need a hostname at minimum
    $service = $args['service']; // Set to _HOST_ as default

    // RRD fetches 1 day worth of data by default
    $start = (isset($args['start']) && $args['start'] != '') ? $args['start'] : '0'; // Defaults to 24hrs
    $end = (isset($args['end']) && $args['end'] != '') ? $args['end'] : time(); // Defaults to now 
    $resolution = (isset($args['resolution']) && $args['resolution'] != '') ? $args['resolution'] : '';
    
    $fetch = perfdata_rrdtool_path().' fetch ';

    // Check to make sure an RRD file actually exists
    $location = pnp_get_perfdata_file($host, $service);
    if (!file_exists($location)) {
        return false;
    }

    // Fetch data and return into $rrddata array
    $cmd = $fetch.$location.' AVERAGE';
    if (!empty($resolution)) {
        $cmd .= ' -r '.escapeshellarg($resolution);
    }
    if (!empty($start)) {
        $cmd .= ' -s '.escapeshellarg($start);
    }
    if (!empty($end)) {
        $cmd .= ' -e '.escapeshellarg($end);
    }

    putenv("LC_ALL=en_US");
    putenv("LANG=en_US");

    exec($cmd, $rrddata);

    $times = array(); // Array of all of the timestamps
    $data = array(); // Make room for multiple columns 
    $data['sets'] = array();

    foreach ($rrddata as $k => $line)
    {
        $line = trim($line);

        // Check line syntax, ignore bad data 
        if (strlen($line) < 10 || empty($line)) continue;

        // Get values and process the time
        $values = explode(' ', $line);

        $time = substr(trim($values[0]), 0, 10);
        if (strlen($time < 9)) continue; // Skip if there's no timestamp, data is bad
        $times[] = $time; // Assign valid time to array

        for ($i = 1; $i < count($values); $i++)  
        {
            // Create comma delineated list for JSON object
            if (!isset($data['sets'][$i-1])) $data['sets'][$i-1] = array(); // Create new string index if none exists 

            // Convert 'nan' to 'null'
            if (strpos($values[$i], 'nan') !== false) {
                $data['sets'][$i-1][] = 'null';
                continue;
            }

            // Handle RRD exponent multiplied values
            $parts = explode('e', $values[$i]);
            $flt = $parts[0]; // The float
            $power = $parts[1]; // The multiplier ie -02 or +02
            $mult = pow(10, $power); // Get the actual multiplier
            $str = $flt * $mult; // Get the real number

            // Append to datastring
            $data['sets'][$i-1][] = $str;
        }

        unset($rrddata[$k]);
    }

    unset($rrddata);

    $data['start'] = $times[0]; // Save the start time from the data grab
    $data['increment'] = (intval($times[1]) - intval($times[0])); // Calculate rrd resolution
    $data['count'] = count($times);

    // Returns an array data strings for JSON object
    return $data;
}


/**
 * Checks to make sure the start time is correct format
 *
 * @param $start
 * @param $view
 *
 * @return int
 */
function graph_format_start_time($start, $view = -1, &$e)
{
    // Date selected
    if ($view == 99) {
        return $start;
    } else if (is_numeric($start) || is_int($start)) {
        return $start; // Timestamp for custom times
    }

    // Check for report options
    $s = null;
    get_times_from_report_timeperiod($start, $s, $e);
    if (!empty($s)) {
        return $s;
    }

    // Check for view first
    if ($view >= 0) {
        if ($view == 0) {
            return (time() - 4*60*60);
        } else if ($view == 1) {
            return (time() - 24*60*60);
        } else if ($view == 2) {
            return strtotime("-7 days");
        } else if ($view == 3) {
            return strtotime("-1 month");
        } else if ($view == 4) {
            return strtotime("-1 year");
        }
    }

    // Then check for start time...
    if ($start == '-4h') {
        return (time() - 4*60*60);
    } else if ($start == '-24h') {
        return (time() - 24*60*60);
    } else if ($start == '-48h') {
        return (time() - 2*24*60*60);
    } else if ($start == '-1w') {
        return strtotime("-7 days");
    } else if ($start == '-1m') {
        return strtotime("-1 month");
    } else if ($start == '-1y') {
        return strtotime("-1 year");
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// GRAPH TEMPLATE FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


/**
 * @return string
 */
function get_graph_template_dir()
{
    return "/usr/local/nagios/share/pnp";
}


/**
 * @return array
 */
function get_graph_templates()
{

    $templates = array();

    $basedir = get_graph_template_dir();

    $dirs = array(
        $basedir . "/templates",
        $basedir . "/templates.dist",
    );

    foreach ($dirs as $dir) {

        $p = $dir;
        $direntries = file_list($p, "");
        foreach ($direntries as $de) {

            $file = $de;
            $filepath = $dir . "/" . $file;
            $ts = filemtime($filepath);

            $perms = fileperms($filepath);
            $perm_string = file_perms_to_string($perms);

            $ownerarr = fileowner($filepath);
            if (function_exists('posix_getpwuid')) {
                $ownerarr = posix_getpwuid($ownerarr);
                $owner = $ownerarr["name"];
            } else
                $owner = $ownerarr;
            $grouparr = filegroup($filepath);
            if (function_exists('posix_getgrgid')) {
                $grouparr = posix_getgrgid($grouparr);
                $group = $grouparr["name"];
            } else
                $group = $grouparr;

            $dir_name = basename($dir);


            $templates[] = array(
                "dir" => $dir_name,
                "file" => $file,
                "timestamp" => $ts,
                "date" => get_datetime_string($ts),
                "perms" => $perms,
                "permstring" => $perm_string,
                "owner" => $owner,
                "group" => $group,
            );
        }
    }

    return $templates;
}
