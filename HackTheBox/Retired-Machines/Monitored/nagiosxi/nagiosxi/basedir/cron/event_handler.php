#!/bin/env php -q
<?php
//
// Event handler
// This script runs once a minute, and checks the event_handler queue
// for events to be added to the DB
//
// Run frequency: 1 minute
//
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//

define('SUBSYSTEM', 1);
require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


$script_start_time = time();
$max_exec_time = 60; // Maximum time for the event_handler to run for (DO NOT CHANGE)
$max_wait_time = 15; // Wait time between last run
$sleep_time = 1;
$eventhandler_lockfile = get_root_dir() . '/var/event_handler.lock';
$chunk_size = 500; // Max amount of events to be ran at any one time


init_eventhandler();
do_eventhandler_jobs();


// Create a lockfile and connect to db
function init_eventhandler()
{
    global $eventhandler_lockfile;
    global $max_exec_time;
    global $max_wait_time;
    global $sleep_time;
    global $script_start_time;

    // Check lock file and loop and wait for lock file to be removed
    while (true) {

        if (!@file_exists($eventhandler_lockfile)) {
            break;
        }

        // Check to see if we have gone over our alloted max_exec_time and exit if necessary
        if ((time() - $script_start_time) >= ($max_exec_time - $sleep_time)) {
            echo "LOCKFILE '$eventhandler_lockfile' STILL EXISTS! CHECKING TIME...\n";

            if (filemtime($eventhandler_lockfile) < ($script_start_time - $max_wait_time)) {
                echo "TAKING OVER LOCK FILE...\n";
                break;
            }

            echo "EXITING!\n";
            exit(1);
        }

        sleep($sleep_time);

    }
    
    // Attempt to create lock file
    if (@touch($eventhandler_lockfile) === false) {
        echo "LOCKFILE '$eventhandler_lockfile' NOT CREATED - EXITING!\n";
        exit(1);
    } else {
        echo "LOCKFILE '$eventhandler_lockfile' CREATED\n";
    }
    
    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES - EXITING!\n";
        exit(1);
    }
}


// cycle through any events to be added from in the queue
function do_eventhandler_jobs() {

    global $script_start_time;
    global $max_exec_time;
    global $eventhandler_queuefile;
    global $eventhandler_queuelockfile;
    global $sleep_time;
    global $chunk_size;

    while (true) {

        // check to see if the next run is going to cause us to move over our alloted max_exec_time and exit if necessary
        if ((time() - $script_start_time) >= ($max_exec_time - $sleep_time)) {
            eventhandler_safe_exit();
        }

        $clear_ids = array();

        // obtain event handler queue data and process
        $sql = "SELECT * FROM xi_eventqueue LIMIT $chunk_size";
        $rs = exec_sql_query(DB_NAGIOSXI, $sql);
        if ($rs->RecordCount() > 0) {
            $rows = $rs->getArray();
            foreach ($rows as $row) {

                $clear_ids[] = $row['eventqueue_id'];

                $time = $row['event_time'];
                $source = $row['event_source'];
                $type = $row['event_type'];
                $meta = base64_decode($row['event_meta']);

                print_r($row);

                add_event($source, $type, $time, $meta);
            }
        }

        // now see if we need to clear our queue data out
        if (count($clear_ids) > 0) {
            $ids = implode(',', $clear_ids);
            $sql = "DELETE FROM xi_eventqueue WHERE eventqueue_id IN ($ids)";
            exec_sql_query(DB_NAGIOSXI, $sql);
        }

        sleep($sleep_time);
    }
}


// delete the lockfile
function eventhandler_safe_exit() {

    global $eventhandler_lockfile;

    if (@file_exists($eventhandler_lockfile)) {
        if (@unlink($eventhandler_lockfile) === false) {
            echo "UNABLE TO DELETE LOCKFILE '$eventhandler_lockfile' - EXITING!\n";
            exit(1);
        } else {
            echo "DELETED LOCKFILE '$eventhandler_lockfile'\n";
        }
    }

    echo "EVENT HANDLER EXITING\n";
    exit();
}