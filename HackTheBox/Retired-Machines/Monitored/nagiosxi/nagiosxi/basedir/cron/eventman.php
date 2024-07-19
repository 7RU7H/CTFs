#!/bin/env php -q
<?php
//
// Event Manager
//
// Cron script that processes events that are created during Nagios operations including
// those created/used by the global event and notification handlers.
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/common.inc.php');

$max_time = 59;
$logging = true;


init_eventman();
do_eventman_jobs();


function init_eventman()
{
    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    return;
}


function do_eventman_jobs()
{
    global $max_time;
    global $logging;

    // Enable logging?
    $logging = is_null(get_option('enable_subsystem_logging')) ? true : get_option("enable_subsystem_logging");

    $start_time = time();
    $t = 0;

    while (true) {
        $n = 0;

        // Bail if if we're been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }

        $n += process_events();
        $t += $n;

        // Sleep for 3 second if we didn't do anything...
        if ($n == 0){
            update_sysstat();
            if ($logging) { 
                echo ".";
            }
            usleep(3000000);
        }
    }

    update_sysstat();

    // Log this regardless once per minute, not system intensive
    echo "\n";
    echo "PROCESSED $t EVENTS\n";
}


// Record the last run in the sysstat table
function update_sysstat() {
    $arr = array(
        "last_check" => time()
    );
    $sdata = serialize($arr);
    update_systat_value("eventman", $sdata);
}


function process_events()
{
    global $db_tables;
    global $cfg;

    // Get the next queued command
    if ($cfg['db_info']['nagiosxi']['dbtype'] == 'pgsql') {
        $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]["events"]." WHERE (status_code='0' AND event_time<=NOW()) OR (status_code='".escape_sql_param(EVENTSTATUS_PROCESSING,DB_NAGIOSXI)."' AND processing_time + INTERVAL '1 minute' <= NOW()) ORDER BY event_id ASC";
    } else {
        $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]["events"]." WHERE (status_code='0' AND event_time<=NOW()) OR (status_code='".escape_sql_param(EVENTSTATUS_PROCESSING,DB_NAGIOSXI)."' AND processing_time + INTERVAL 1 MINUTE <= NOW()) ORDER BY event_id ASC";
    }

    $args = array(
        "sql" => $sql,
        "useropts" => array("records" => 1)
    );
    $sql = limit_sql_query_records($args, DB_NAGIOSXI);
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, true, false))) {
        if (!$rs->EOF) {
            process_event_record($rs);
            return 1;
        }
    }

    return 0;
}


function process_event_record($rs)
{
    global $db_tables;

    $event_id = $rs->fields["event_id"];
    $event_source = $rs->fields["event_source"];
    $event_type = $rs->fields["event_type"];
    $event_time = $rs->fields["event_time"];

    // Immediately update the event as being processed
    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]["events"]." SET status_code='".escape_sql_param(EVENTSTATUS_PROCESSING,DB_NAGIOSXI)."', processing_time=NOW() WHERE event_id='".escape_sql_param($event_id,DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);

    // Process the event
    $result_code = process_event($event_id, $event_source, $event_type, $event_time);

    // Mark the event as being completed
    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]["events"]." SET status_code='".escape_sql_param(EVENTSTATUS_COMPLETED,DB_NAGIOSXI)."', processing_time=NOW() WHERE event_id='".escape_sql_param($event_id,DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);
}


function process_event($event_id, $event_source, $event_type, $event_time)
{
    global $cfg;
    global $db_tables;
    global $logging;

    if ($logging) {
        echo "PROCESS EVENT: ID=$event_id, SOURCE=$event_source, TYPE=$event_type, TIME=$event_time\n";
    }

    // Get the meta data associated with this event
    $event_meta = array();
    $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]["meta"]." WHERE metatype_id='".escape_sql_param(METATYPE_EVENT,DB_NAGIOSXI)."' AND metaobj_id='".escape_sql_param($event_id,DB_NAGIOSXI)."'";
    $rs = exec_sql_query(DB_NAGIOSXI,$sql,true,false);
    if ($rs) {
        $rs->MoveFirst();
        $event_meta = unserialize(base64_decode($rs->fields["keyvalue"]));
    }

    // Do CALLBACK_EVENT_PROCESSED callbacks
    $args = array(
        "event_id" => $event_id,
        "event_source" => $event_source,
        "event_type" => $event_type,
        "event_time" => $event_time,
        "event_meta" => $event_meta,
        "logging_enabled" => $logging
    );

    do_callbacks(CALLBACK_EVENT_PROCESSED, $args);
}