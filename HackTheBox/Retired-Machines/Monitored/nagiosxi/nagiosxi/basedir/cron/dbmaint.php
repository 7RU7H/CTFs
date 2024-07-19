#!/bin/env php -q
<?php
//
// DB Maintenance cron trims database files, optimizes, and repairs
// both the Postgres database and the MySQL databsses.
//
// This script also syncs checks that have been passed to this Nagios XI box
// via NRDP from another Nagios XI in the past. It reaps a spool file for the
// check log entries and places those entries in the proper rotated log files
// so that reports work properly.
//
// Also updates Nagios Cloud information on cloud licensed XI instances.
//
// Run frequency: 5 minutes
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

$dbmaint_lockfile = get_root_dir()."/var/dbmaint.lock";

init_dbmaint();
do_dbmaint_jobs();

function init_dbmaint()
{
    global $dbmaint_lockfile;

    // Check lock file
    if (@file_exists($dbmaint_lockfile)) {
        $ft = filemtime($dbmaint_lockfile);
        $now = time();
        if (($now - $ft) > 1800) {
            echo "LOCKFILE '".$dbmaint_lockfile."' IS OLD - REMOVING\n";
            unlink($dbmaint_lockfile);
        } else {
            echo "LOCKFILE '".$dbmaint_lockfile."' EXISTS - EXITING!\n";
            exit();
        }
    }
    
    // Create lock file
    echo "CREATING: $dbmaint_lockfile\n";
    file_put_contents($dbmaint_lockfile, "");

    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    return;
}

function do_dbmaint_jobs()
{
    global $dbmaint_lockfile;
    global $db_tables;
    global $cfg;

    $now = time();

    // Nagios v2 license systems only (update database with latest data from v2 API)
    if (is_v2_license()) {
        get_v2_license_stats();
    }

    /////////////////////////////////////////////////////////////
    // TRIM NDOUTILS TABLES
    /////////////////////////////////////////////////////////////
    $dbminfo = $cfg['db_info']['ndoutils']['dbmaint'];
    
    // Comment history (DAYS)
    $age = get_database_interval("ndoutils", "max_commenthistory_age", 730);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "commenthistory", "entry_time", $cutoff);
    
    // Process events (DAYS)
    $age = get_database_interval("ndoutils", "max_processevents_age", 365);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "processevents", "event_time", $cutoff);
    
    // External commands (DAYS)
    $age = get_database_interval("ndoutils", "max_externalcommands_age", 7);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "externalcommands", "entry_time", $cutoff);
    
    // Log entries (DAYS)
    $age = get_database_interval("ndoutils", "max_logentries_age", 90);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "logentries", "logentry_time", $cutoff);
    
    // Notifications (DAYS)
    $age = get_database_interval("ndoutils", "max_notifications_age", 90);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "notifications", "start_time", $cutoff);
    clean_db_table(DB_NDOUTILS, "contactnotifications", "start_time", $cutoff);
    clean_db_table(DB_NDOUTILS, "contactnotificationmethods", "start_time", $cutoff);
    
    // State history (DAYS)
    $age = get_database_interval("ndoutils", "max_statehistory_age", 730);
    $cutoff = $now - (intval($age) * 60 * 60 * 24);
    clean_db_table(DB_NDOUTILS, "statehistory", "state_time", $cutoff);
    
    // Timed events
    $age = get_database_interval("ndoutils", "max_timedevents_age", 5);
    $cutoff = $now - (intval($age) * 60);
    clean_db_table(DB_NDOUTILS, "timedevents", "event_time", $cutoff);
    
    // System commands
    $age = get_database_interval("ndoutils", "max_systemcommands_age", 5);
    $cutoff = $now - (intval($age) * 60);
    clean_db_table(DB_NDOUTILS, "systemcommands", "start_time", $cutoff);
    
    // Service checks
    $age = get_database_interval("ndoutils", "max_servicechecks_age", 5);
    $cutoff = $now - (intval($age) * 60);
    clean_db_table(DB_NDOUTILS, "servicechecks", "start_time", $cutoff);
    
    // Host checks
    $age = get_database_interval("ndoutils", "max_hostchecks_age", 5);
    $cutoff = $now - (intval($age) * 60);
    clean_db_table(DB_NDOUTILS, "hostchecks", "start_time", $cutoff);

    // Event handlers
    $age = get_database_interval("ndoutils", "max_eventhandlers_age", 5);
    $cutoff = $now - (intval($age) * 60);
    clean_db_table(DB_NDOUTILS, "eventhandlers", "start_time", $cutoff);
        
    /////////////////////////////////////////////////////////////
    // OPTIMIZE NDOUTILS TABLES
    /////////////////////////////////////////////////////////////
    
    $optimize_interval = get_database_interval("ndoutils", "optimize_interval", 60);
    
    $optimize = false;
    $lastopt = get_meta(METATYPE_NONE, 0, "last_ndoutils_optimization");
    
    if ($lastopt == null) {
        $optimize = true;
        echo "NEVER OPTIMIZED\n";
    } else {
        $opt_time = ($lastopt + ($optimize_interval * 60));
        if ($now > $opt_time) {
            $optimize = true;
            echo "TIME TO OPTIMIZE\n";
        }
        echo "LASTOPT:  $lastopt\n";
        echo "INTERVAL: $optimize_interval\n";
        echo "NOW:      $now\n";
        echo "OPTTIME:  $opt_time\n";
    }
    
    if ($optimize_interval == 0) {
        echo "OPTIMIZE INTERVAL=0\n";
        $optimize = false;
    }
    
    if ($optimize == true) {
        foreach ($db_tables[DB_NDOUTILS] as $table) {
            echo "OPTIMIZING NDOUTILS TABLE: $table\n";
            optimize_table(DB_NDOUTILS, $table);
        }
        set_meta(METATYPE_NONE, 0, "last_ndoutils_optimization", $now);
    }

    /////////////////////////////////////////////////////////////
    // TRIM NAGIOSXI TABLES
    /////////////////////////////////////////////////////////////
    $dbminfo = $cfg['db_info']['nagiosxi']['dbmaint'];

    // Commands
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_commands_age", 480)) * 60);
    $extra = "AND status_code = ".COMMAND_STATUS_COMPLETED;
    clean_db_table(DB_NAGIOSXI, "commands", "processing_time", $cutoff, $extra);

    // Events
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_events_age", 480)) * 60);
    $extra = "AND status_code = ".EVENTSTATUS_COMPLETED;
    clean_db_table(DB_NAGIOSXI, "events", "processing_time", $cutoff, $extra);

    // Expired auth tokens (24 hour default)
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_auth_session_age", 24)) * 60 * 60);
    clean_db_table(DB_NAGIOSXI, "auth_tokens", "auth_valid_until", $cutoff);

    // NXTI trap removal (90 day defeault)
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_nxti_age", 90)) * 24 * 60 * 60);
    clean_db_table(DB_NAGIOSXI, "cmp_trapdata_log", "trapdata_log_datetime", $cutoff);

    // Scheduled Reports log removal (365 day defeault)
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_scheduledreports_log_age", 365)) * 24 * 60 * 60);
    clean_db_table(DB_NAGIOSXI, "cmp_scheduledreports_log", "report_run", $cutoff);

    // Event Metadata...

    // First find meta records with no matching event record...
    $sql = "SELECT ".$db_tables[DB_NAGIOSXI]["meta"].".meta_id FROM ".$db_tables[DB_NAGIOSXI]["meta"]." LEFT JOIN ".$db_tables[DB_NAGIOSXI]["events"]." ON ".$db_tables[DB_NAGIOSXI]["meta"].".metaobj_id=".$db_tables[DB_NAGIOSXI]["events"].".event_id WHERE metatype_id='1' AND event_id IS NULL";
    echo "SQL1: $sql\n";

    $rs = exec_sql_query(DB_NAGIOSXI, $sql, true, false);
    $rc = 0;
    foreach ($rs as $r) {

        // Now delete the meta records
        $sql2 = "DELETE FROM ".$db_tables[DB_NAGIOSXI]["meta"]." WHERE meta_id = " . $r['meta_id'];
        if (exec_sql_query(DB_NAGIOSXI, $sql2, true, false)) {
            $rc++;
        }
    }
    echo "SQL2: Deleted {$rc} (DELETE FROM ".$db_tables[DB_NAGIOSXI]["meta"]." WHERE meta_id IN ({$sql}))\n";

    // Audit log entries
    $cutoff = $now - (intval(get_database_interval("nagiosxi", "max_auditlog_age", 180)) * 24 * 60 * 60);
    clean_db_table(DB_NAGIOSXI, "auditlog", "log_time", $cutoff);

    // Special section for Nagios XI sessions (delete when they are cleaned up)
    $session_path = session_save_path();
    $sql = "SELECT session_id, session_phpid FROM ".$db_tables[DB_NAGIOSXI]["sessions"];
    $rs = exec_sql_query(DB_NAGIOSXI, $sql, true, false);
    $sessions = $rs->GetArray();
    if (!empty($sessions)) {
        $rc = 0;
        foreach ($sessions as $session) {
            // Check if the php session still exists
            if (!file_exists($session_path.'/sess_'.$session['session_phpid'])) {
                if (exec_sql_query(DB_NAGIOSXI, "DELETE FROM ".$db_tables[DB_NAGIOSXI]["sessions"]." WHERE session_id = ".$session['session_id'], true, false)) {
                    $rc++;
                }
            }
        }
        echo "SESSION CLEANUP: Cleaned up {$rc} sessions.\n";
    }

    /////////////////////////////////////////////////////////////
    // OPTIMIZE NAGIOSXI TABLES
    /////////////////////////////////////////////////////////////

    $optimize_interval = get_database_interval("nagiosxi", "optimize_interval", 60);

    $optimize = false;
    $lastopt = get_meta(METATYPE_NONE, 0, "last_db_optimization");
    if ($lastopt == null) {
        $optimize = true;
    } else {
        if ($now > ($lastopt + ($optimize_interval * 60))) {
            $optimize = true;
        }
    }

    if ($optimize_interval == 0) {
        $optimize = false;
    }

    if ($optimize == true) {
        foreach ($db_tables[DB_NAGIOSXI] as $table) {
            echo "OPTIMIZING NAGIOSXI TABLE: $table\n";
            optimize_table(DB_NAGIOSXI, $table);
        }
        set_meta(METATYPE_NONE, 0, "last_db_optimization", $now);
    }

    /////////////////////////////////////////////////////////////
    // TRIM NAGIOSQL TABLES
    /////////////////////////////////////////////////////////////
    $dbminfo = $cfg['db_info']['nagiosql']['dbmaint'];

    // Log book records
    $cutoff = $now - (intval(get_database_interval("nagiosql", "max_logbook_age", 480)) * 60);
    clean_db_table(DB_NAGIOSQL, "logbook", "time", $cutoff);

    /////////////////////////////////////////////////////////////
    // OPTIMIZE NAGIOSQL TABLES
    /////////////////////////////////////////////////////////////

    $optimize_interval = get_database_interval("nagiosql", "optimize_interval", 60);

    $optimize = false;
    $lastopt = get_meta(METATYPE_NONE, 0, "last_nagiosql_optimization");
    if ($lastopt == null) {
        $optimize = true;
    } else {
        if($now > ($lastopt + ($optimize_interval * 60))) {
            $optimize = true;
        }
    }

    if ($optimize_interval == 0) {
        $optimize = false;
    }

    if ($optimize == true) {
        foreach ($db_tables[DB_NAGIOSQL] as $table) {
            echo "OPTIMIZING NAGIOSQL TABLE: $table\n";
            optimize_table(DB_NAGIOSQL, $table);
        }
        set_meta(METATYPE_NONE, 0, "last_nagiosql_optimization", $now);
    }

    // Misc cleanup functions
    $args = array(); 
    do_callbacks(CALLBACK_SUBSYS_DBMAINT, $args);

    // Sync database with log files via spool dir files
    sync_databases_with_spooled_checks();

    update_sysstat();

    // Delete lock file
    if (unlink($dbmaint_lockfile)) { echo "Repair Complete: Removing Lock File\n"; }
    else { echo "Repair Complete: FAILED TO REMOVE LOCK FILE\n"; }
}

function clean_db_table($db, $table, $field, $ts, $extra='')
{
    global $db_tables;

    $sql = "DELETE FROM ".$db_tables[$db][$table]." WHERE ".$field." < ".sql_time_from_timestamp($ts, $db)."";

    if (!empty($extra)) {
        $sql .= " $extra";
    }

    echo "CLEANING $db TABLE '$table'...\n";
    echo "SQL: $sql\n";

    $rs = exec_sql_query($db, $sql, true, false);
}

function optimize_table($db, $table)
{
    global $cfg;
    global $db_tables;

    $dbtype = $cfg['db_info'][$db]["dbtype"];

    // Postgres or MySQL
    if ($dbtype == 'pgsql') {
        $sql = "VACUUM ANALYZE ".$table.";";
    } else {
        $sql = "OPTIMIZE TABLE ".$table."";
    }

    echo "SQL: $sql\n";

    $rs = exec_sql_query($db, $sql, true, false);
}

function repair_table($db, $table)
{
    global $db_tables;
    global $cfg;

    $dbtype = $cfg['db_info'][$db]["dbtype"];

    // Only works with mysql
    if ($dbtype == 'mysql') {
        $sql = "REPAIR TABLE ".$table."";
        echo "SQL: $sql\n";
        $rs = exec_sql_query($db,$sql,true,false);
    }
}

function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array("last_check" => time());
    $sdata = serialize($arr);
    update_systat_value("dbmaint", $sdata);
}

/**
 * Syncs the NRDP "back in time" passive checks that are sent with a timestamp
 * to by added to the Nagios Core log files when file rotation happens.
 */
function sync_databases_with_spooled_checks()
{
    global $cfg;
    print "\n\n";

    $spool_dir = get_root_dir().'/tmp/passive_spool';
    $nagios_log_dir = '/usr/local/nagios/var/archives';
    $nagios_cfg = read_nagios_config_file();
    $sort_files = array();

    // Check if we have anything in the spool dir
    if (file_exists($spool_dir)) {

        // Read in all files/timestamps from spool directory
        $spool_files = array();
        if ($h = opendir($spool_dir)) {
            while (false !== ($f = readdir($h))) {
                if ($f != "." && $f != "..") {
                    $spool_files[] = $f; 
                }
            }
            closedir($h);
        }

        // Add spooled files into current log rotate if necessary
        foreach ($spool_files as $spool_file) {
            list($ts, $j) = explode('.', $spool_file);

            echo 'Running spooled file - '.$ts."\n\n";

            // If the timestamp falls after the log rotation period... then add all contents to the 
            // archived log file and re-order (possibly)
            $contents = file_get_contents($spool_dir.'/'.$spool_file);
            $lines = explode("\n", $contents);
            foreach ($lines as $k => $l) {
                if (empty($l)) { unset($lines[$k]); continue; }

                $timestamp = substr($l, 1, 10);
                $logline = substr($l, 13);

                // Find the file it goes into (add 1 day on since it will be named the NEXT day
                // i.e. if it was sent on 01-14-2015 it will be in the 01-15-2015 log file)
                if ($nagios_cfg['log_rotation_method'] == 'd') {
                    $date = date('m-d-Y-00', $timestamp+(24*60*60));
                } else if ($nagios_cfg['log_rotation_method'] == 'h') {
                    $date = date('m-d-Y-H', $timestamp+(60*60));
                }
                $logfile = $nagios_log_dir.'/nagios-'.$date.'.log';

                // Place data into the file and remove it from the lines
                if (file_exists($logfile)) {
                    file_put_contents($logfile, $l."\n", FILE_APPEND);
                    $sort_files[] = $logfile;
                    unset($lines[$k]);
                }
            }

            // If the lines aren't empty now, we need to re-write over the file OR delete it
            if (empty($lines)) {
                unlink($spool_dir.'/'.$spool_file);
            } else {
                $newlines = "";
                foreach ($lines as $line) {
                    $newlines += $line."";
                }
                file_put_contents($spool_dir.'/'.$spool_file, $newlines);
            }

        }

    }

    // Sort all necessary log files that we put data into
    if (!empty($sort_files)) {
        // Do sorting here... but implement later (not necessary)
    }

    print "\n\n";
}

function read_nagios_config_file()
{
    $nagios_cfg = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
    $ncfg = explode("\n", $nagios_cfg);

    $nagios_cfg = array();
    foreach ($ncfg as $line) {
        if (strpos($line, "=") !== false) {
            $var = explode("=", $line);
            $nagios_cfg[$var[0]] = $var[1];
        }
    }

    return $nagios_cfg;
}