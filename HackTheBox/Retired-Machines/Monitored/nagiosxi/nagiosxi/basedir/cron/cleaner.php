#!/bin/env php -q
<?php
//
// File Cleaner Cron
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/common.inc.php');
require_once(dirname(__FILE__).'/../html/config/deployment/includes/utils-deployment.inc.php');

init_cleaner();
do_cleaner_jobs();

// Connects to databases
function init_cleaner()
{
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }
    return;
}

function do_cleaner_jobs()
{
    global $cfg;

    // Get checkpoint amounts to save
    $reg_num = intval(get_option('num_snapshots', 10));
    $error_num = intval(get_option('num_error_snapshots', 10));
    $ccm_num = intval(get_option('num_ccm_snapshots', 10));

    // CLEANUP OLD NOM CHECKPOINTS
    // Keep only the most recent checkpoints
    $cmdline = $cfg['script_dir']."/nom_trim_checkpoints.sh --num $reg_num --enum $error_num --cnum $ccm_num";
    system($cmdline, $return_code);

    echo "----------------------------------\n";
    echo "Running callbacks:\n";

    // Misc cleanup functions
    $args = array(); 
    do_callbacks(CALLBACK_SUBSYS_CLEANER, $args); 

    echo "----------------------------------\n";

    update_sysstat();

    check_xi_file_integrity();

    // Do deployment checks
    do_deploy_statuschecks();
}

function do_deploy_statuschecks()
{
    global $cfg;
    global $db_tables;
    $deploy_avail_check = get_option('deploy_avail_check', 24);

    $dbtype = '';
    if (array_key_exists("dbtype", $cfg['db_info'][DB_NAGIOSXI])) {
        $dbtype = $cfg['db_info'][DB_NAGIOSXI]['dbtype'];
    }

    $interval = "INTERVAL ".intval($deploy_avail_check)." HOUR";
    if ($dbtype == 'pgsql') {
        $interval = "INTERVAL '".intval($deploy_avail_check)." hours'";
    }

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["deploy_agents"] . " WHERE last_status_check + ".$interval." <= NOW()";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        $data = $rs->GetArray();
        if (!empty($data)) {
            foreach ($data as $agent) {
                deploy_do_avail_check($agent);
            }
        }
    }
}

function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array("last_check" => time());
    $sdata = serialize($arr);
    update_systat_value("cleaner", $sdata);
}
