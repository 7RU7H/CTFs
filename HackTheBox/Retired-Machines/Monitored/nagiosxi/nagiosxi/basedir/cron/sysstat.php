#!/bin/env php -q
<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

init_session();

$max_time = 50;
$sleep_time = 15; // In seconds
$logging = true;
$start_time = time(); 

init_sysstat();
do_sysstat_jobs();

function init_sysstat()
{
    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    return;
}

function do_sysstat_jobs()
{
    global $max_time;
    global $sleep_time;
    global $logging;
    global $start_time; 
    
    // Enable logging?  
    $logging = is_null(get_option('enable_subsystem_logging')) ? true : get_option("enable_subsystem_logging");

    while (1) {

        $n = 0;

        // Bail if if we're been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }

        process_sysstat();
        $n++;

        // Record our run in sysstat table
        $arr = array(
            "last_check" => $now
        );
        $sdata = serialize($arr);
        update_systat_value("sysstat", $sdata);

        // Sleep a bit
        if ($logging) {
            echo ".";
        }
        sleep($sleep_time);
    }
        
    echo "Done\n";
}

function process_sysstat()
{
    global $db_tables;
    
    get_daemon_status();
    get_nagioscore_stats();
    get_machine_stats();
}

/* This function is deprecated - it used to interface with nagios_conninfo, which was removed in NDO 3.
 * This function stub remains to prevent a crash/error in the event that a customer-written component used this function.
 */
function get_db_backend_status()
{
}

function get_daemon_status()
{
    global $logging;
    global $cfg;

    $script_dir = $cfg["script_dir"];
    if (substr($script_dir, -1) == '/') {
        $script_dir = substr($script_dir, 0, strlen($script_dir) - 1);
    }

    $daemons = array(
        "nagioscore" => array(
            "daemon" => "nagios",
            "output" => "",
            "return_code" => 0,
            "status" => SUBSYS_COMPONENT_STATUS_UNKNOWN
        ),
        "pnp" => array(
            "daemon" => "npcd",
            "output" => "",
            "return_code" => 0,
            "status" => SUBSYS_COMPONENT_STATUS_UNKNOWN
        )
    );

    foreach ($daemons as $dname => $darr) {

        $cmdline = sprintf("sudo %s/manage_services.sh status %s", $script_dir, $darr["daemon"]);

        if ($logging) {
            echo "CMDLINE=$cmdline\n";
        }

        $return_code = 0;
        $output = system($cmdline, $return_code);

        if ($logging) {
            echo "OUTPUT=$output\n";
            echo "RETURNCODE=$return_code\n";
        }    

        $daemons[$dname]["output"] = $output;
        $daemons[$dname]["return_code"] = $return_code;
        if ($return_code == 0 && strpos($output, 'not running') === false) {
            $daemons[$dname]["status"] = SUBSYS_COMPONENT_STATUS_OK;
        } else {
            $daemons[$dname]["status"] = SUBSYS_COMPONENT_STATUS_ERROR;
        }
    }

    if ($logging) {  
        echo "DAEMONS:\n";
        print_r($daemons);
    }

    $sdata = serialize($daemons);
    update_systat_value("daemons", $sdata);

    return $daemons;
}

function get_machine_stats()
{
    global $logging;
    global $cfg;
    $return_code = 0;

    // Get system information
    $xisys = $cfg['root_dir'] . '/var/xi-sys.cfg';
    $ini = parse_ini_file($xisys);

    // GET LOAD INFO
    $cmdline = sprintf("/usr/bin/uptime | sed s/,//g | awk -F'average: ' '{  print $2 }'");
    $output = array();
    exec($cmdline, $output, $return_code);

    $rawload = $output[0];
    $loads = explode(" ", $rawload);
    $load = array(
        "load1" => $loads[0],
        "load5" => $loads[1],
        "load15" => $loads[2]
    );

    if ($logging) {
        echo "LOAD:\n";
        print_r($load);
    }

    $sdata = serialize($load);
    update_systat_value("load", $sdata);

    // GET MEMORY INFO
    $cmdline = sprintf("/usr/bin/free -m | head --lines=2 | tail --lines=1 | awk '{ print $2,$3,$4,$5,$6,$7}'");
    $output = array();
    exec($cmdline, $output, $return_code);

    $rawmem = $output[0];
    $meminfo = explode(" ", $rawmem);
    $mem = array(
        "total" => $meminfo[0],
        "used" => $meminfo[1],
        "free" => $meminfo[2],
        "shared" => $meminfo[3],
        "buffers" => $meminfo[4],
        "cached" => $meminfo[5]
    );

    if ($logging) {
        echo "MEMORY:\n";
        print_r($mem);
    }
    $sdata = serialize($mem);
    update_systat_value("memory", $sdata);

    // GET SWAP INFO
    $cmdline = sprintf("/usr/bin/free -m | tail --lines=1 | awk '{ print $2,$3,$4}'");
    $output = array();
    exec($cmdline, $output, $return_code);

    $rawswap = $output[0];
    $swapinfo = explode(" ", $rawswap);
    $swap = array(
        "total" => $swapinfo[0],
        "used" => $swapinfo[1],
        "free" => $swapinfo[2]
    );

    if ($logging) {
        echo "SWAP:\n";
        print_r($swap);
    }
    $sdata = serialize($swap);
    update_systat_value("swap", $sdata);

    // GET IOSTAT INFO

    // Newer versions of iostat have an extra blank line at the bottom of the output.
    // New version of iostat seen installed by default in:
    // - RHEL 8.2
    // - Ubuntu 18
    // - Ubuntu 20
    $cmdline = '/usr/bin/iostat -c 5 2 | grep -v \'^ *$\' | tail -1 | awk \'{ print $1,$2,$3,$4,$5,$6 }\'';
    $output = array();
    exec($cmdline, $output);

    $rawiostat = $output[0];
    $iostatinfo = explode(' ', $rawiostat);
    $iostat = array(
        'user' => $iostatinfo[0],
        'nice' => $iostatinfo[1],
        'system' => $iostatinfo[2],
        'iowait' => $iostatinfo[3],
        'steal' => $iostatinfo[4],
        'idle' => $iostatinfo[5],
    );

    if ($logging) {
        echo "IOSTAT:\n";
        print_r($iostat);
    }
    $sdata = serialize($iostat);
    update_systat_value("iostat", $sdata);
}

function get_nagioscore_stats()
{
    global $logging;

    $corestats = array(
        "activehostchecks" => array(
            "1min" => get_checks_total(60, 'host', true),
            "5min" => get_checks_total(300, 'host', true),
            "15min" => get_checks_total(900, 'host', true)
        ),
        "passivehostchecks" => array(
            "1min" => get_checks_total(60, 'host', false),
            "5min" => get_checks_total(300, 'host', false),
            "15min" => get_checks_total(900, 'host', false)
        ),
        "activeservicechecks" => array(
            "1min" => get_checks_total(60, 'service', true),
            "5min" => get_checks_total(300, 'service', true),
            "15min" => get_checks_total(900, 'service', true)
        ),
        "passiveservicechecks" => array(
            "1min" => get_checks_total(60, 'service', false),
            "5min" => get_checks_total(300, 'service', false),
            "15min" => get_checks_total(900, 'service', false)
        ),    
        "activehostcheckperf" => get_check_perf_stats("hoststatus", 0),
        "activeservicecheckperf" => get_check_perf_stats("servicestatus", 0)
    );

    if ($logging) {
        echo "CORE STATS:\n";
        print_r($corestats);
    }

    $sdata = serialize($corestats);
    update_systat_value("nagioscore", $sdata);
}

function get_timedeventqueue_total($interval, $types)
{
    global $db_tables;
    $total = 0;

    // Get host check totals
    $sql = "SELECT COUNT(*) AS total FROM " . $db_tables[DB_NDOUTILS]["timedevents"] . " WHERE ";
    if (is_array($types)) {
        $sql .= " event_type IN (";
        $n = 0;
        foreach ($types as $type) {
            if ($n > 0)
                $sql .= ",";
            $sql .= "'" . $type . "'";
            $n++;
        }
        $sql .= ") AND ";
    }

    $sql.=" (TIMESTAMPDIFF(SECOND," . $db_tables[DB_NDOUTILS]["timedevents"] . ".scheduled_time,NOW()) < " . $interval . ")";
    if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["total"];
        }
    }
    return $total;
}

function get_checks_total($interval, $object, $active)
{
    global $db_tables;
    $table = $object == 'service' ? 'servicestatus' : 'hoststatus'; 

    $sql = "SELECT COUNT(*) AS total FROM " . $db_tables[DB_NDOUTILS][$table] . " WHERE TRUE";
        
    if ($active) {
        $sql .= "  AND`active_checks_enabled`=1 "; 
    } else {
        $sql .= " AND `active_checks_enabled`=0 AND `passive_checks_enabled`=1 ";  
    }

    $sql .= " AND (TIMESTAMPDIFF(SECOND," . $db_tables[DB_NDOUTILS][$table] . ".last_check,NOW()) < " . $interval . ")";
    if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["total"];
        }
    } else {
        return 0;
    }
}

function get_check_perf_stats($table, $type=0)
{
    global $db_tables;
    $arr = array();

    $sql = "
    SELECT 
    MIN(latency) AS min_latency, 
    MAX(latency) AS max_latency, 
    AVG(latency) AS avg_latency,
    MIN(execution_time) AS min_execution_time,
    MAX(execution_time) AS max_execution_time,
    AVG(execution_time) AS avg_execution_time
    FROM ".$db_tables[DB_NDOUTILS][$table]." WHERE check_type='".$type."'
    ";
    
    if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        if ($rs->MoveFirst()) {
            $arr["min_latency"] = $rs->fields["min_latency"];
            $arr["max_latency"] = $rs->fields["max_latency"];
            $arr["avg_latency"] = $rs->fields["avg_latency"];
            $arr["min_execution_time"] = $rs->fields["min_execution_time"];
            $arr["max_execution_time"] = $rs->fields["max_execution_time"];
            $arr["avg_execution_time"] = $rs->fields["avg_execution_time"];
        }
    }

    return $arr;
}

function get_status_file_location()
{
    $sql = "SELECT `varvalue` FROM nagios_configfilevariables WHERE `varname`='status_file' AND `instance_id`=1;"; 
    $rs = exec_sql_query(DB_NDOUTILS, $sql, true);
    if ($rs) {
        foreach ($rs as $r) {
            return $r['varvalue'];
        }
    } else {
        return '/usr/local/nagios/var/status/dat'; 
    }
}
