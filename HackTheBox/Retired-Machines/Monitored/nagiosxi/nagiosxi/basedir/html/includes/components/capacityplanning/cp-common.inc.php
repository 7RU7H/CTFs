<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Defined variables
define("PDATADIR", pnp_get_perfdata_dir());
define('CAPACITYBIN', dirname(__FILE__) . '/backend/capacityplanning.py');
define('TIMEFRAMEBIN', dirname(__FILE__) . '/backend/timeframe.py');

function pnp_sanitize_filename($filename)
{
    return preg_replace('@[: \\/]@', '_', $filename);
}

function get_timeframe($host, $service, $track)
{
    $host = escapeshellarg(pnp_sanitize_filename($host));
    $service = escapeshellarg(pnp_sanitize_filename($service));
    $track = escapeshellarg(pnp_sanitize_filename($track));

    $cmd = TIMEFRAMEBIN . " $host $service $track " . PDATADIR . ' 2>&1';

    $output = array();
    exec($cmd, $output, $returncode);

    return $output ? $output[0] : '';
}

function get_extrapolation($host, $service, $track, $options)
{
    $host = escapeshellarg(pnp_sanitize_filename($host));
    $service = escapeshellarg(pnp_sanitize_filename($service));
    $track = escapeshellarg(pnp_sanitize_filename($track));

    $cmd = CAPACITYBIN . ' -D ' . PDATADIR . " -H $host -S $service -T $track";

    // Process any options and add them to our command string.
    if ($options && is_array($options)) {

        // Process dates options (potentially a combination of one or more
        // timestamps or date strings as a comma separated list or array).
        process_options_dates_to_times($options, 'dates');
        process_options_dates_to_times($options, 'times');

        // The arguments with values we accept: URL param => CLI param.
        $args['method'] = ' -M ';
        $args['end'] = ' -E ';
        $args['period'] = ' -P ';
        $args['steps'] = ' -s ';
        $args['dates'] = ' -d ';
        $args['times'] = ' -t ';
        $args['json-indent'] = ' -j ';
        $args['json-prettify'] = ' -p ';
        $args['gt'] = ' --gt ';
        $args['ge'] = ' --ge ';
        $args['lt'] = ' --lt ';
        $args['le'] = ' --le ';
        $args['eq'] = ' --eq ';

        // The boolean flags we accept.
        $flags['no-highcharts'] = ' --no-highcharts';
        $flags['json-sort'] = ' -k';
        $flags['warn'] = ' -w';
        $flags['crit'] = ' -c';

        foreach ($options as $key => $value) {
            if (isset($args[$key])) {
                $cmd .= $args[$key] . escapeshellarg($value);
            } else if (isset($flags[$key])) {
                $cmd .= $flags[$key];
            }
        }
    }

    $output = array();
    exec($cmd, $output, $returncode);

    return $output ? $output[0] : '';
}

/**
 * Converts date/time options to UNIX timestamps. The input
 * options are potentially a combination of one or more timestamps or date
 * strings (in PHP strtotime() format) as a comma separated list or array.
 *
 * @param $options The input options key => value hashmap (array).
 * @param $key     The option name to process if it exists in $options.
 *
 * @post The original $options[$key] is replaced with a list of UNIX
 * timestamps for the original dates/times as a comma separated string.
 */
function process_options_dates_to_times(&$options, $key)
{
    // Process the option value for our key if it exists.
    if (isset($options[$key]) && $options[$key]) {

        // Convert scalars to an array so we can treat them uniformly, and
        // also handle comma separated lists as input.
        if (!is_array($options[$key])) {
            $options[$key] = explode(',', $options[$key]);
        }

        // Now get a timestamp for each date. Explicitly convert numbers
        // to integers. We'll let strtotime() failures get converted to 0
        // and be handled by the backend as out of range dates.
        foreach ($options[$key] as &$date) {
            $date = intval(is_numeric($date) ? $date : strtotime($date));
        }

        // Now join all the times together as a comma separated list.
        $options[$key] = implode(',', $options[$key]);
    }
}
