#!/bin/env php -q
<?php
//
// Process Feed From Core Logs
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

$max_time = 55;
$sleep_time = 20;
$debug = 0;


// Check if enabled and do the job
$listen = init_feedprocessor();
if ($listen) {
    do_feedprocessor_jobs();
} else {
    echo "Unconfigured Object Feed - DISABLED\n";
    update_sysstat(); 
}


/**
 * Checks to see if we have enabled unconfigured opbjects or not
 *
 * @return  bool    True if unconfigured objects is enabled (default)
 */
function init_feedprocessor()
{
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "Error: Could not connect to databases!\n";
        exit();
    }
    $listen = get_option('enable_unconfigured_objects', true);
    return $listen;
}


/**
 * The actual processor loop that will call the process_feeds function
 * to do the work (i.e. checking for unconfigured objects) every second 
 */
function do_feedprocessor_jobs()
{
    global $max_time;
    global $sleep_time;

    $start_time = time();
    $t = 0;

    while (1) {

        $n = 0;

        // Bail if if we're been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }

        $n += process_feeds();
        $t += $n;

        // Sleep for 1 second and continue
        if ($n == 0) {
            update_sysstat();
            echo ".";
            sleep($sleep_time);
        }
    }

    update_sysstat();

    echo "\n";
    echo "PROCESSED $t COMMANDS\n";
}


/**
 * Records the last run time of feedprocessor to the sysstat
 * table in the database
 */
function update_sysstat()
{
    $arr = array("last_check" => time());
    $sdata = serialize($arr);
    update_systat_value("feedprocessor", $sdata);
}


/**
 * Runs the actual parse_core_eventlog.php script that reads and processes
 * the unconfigured objects and stores them for later use
 *
 * @return  int     Returns zero
 */
function process_feeds()
{
    global $db_tables;
    global $debug;

    // Parse Nagios Core log file for missing objects that passive checks were received form
    $output = array();
    $cmd = "php -q ".get_root_dir()."/scripts/parse_core_eventlog.php";
    exec($cmd, $output);

    if ($debug) {
        foreach ($output as $line) {
            print $line."\n";
        }
    }

    return 0;
}
