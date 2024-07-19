#!/bin/env php -q
<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

$max_time = 55;
$sleep_time = 1;

init_nom();
do_nom_jobs();


function init_nom()
{
    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    return;
}


function do_nom_jobs()
{
    global $max_time;
    global $cfg;

    $start_time = time();
    $script_dir = $cfg['script_dir'];

    while (true) {

        // Nail if if we're been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }

        /////////////////////////////////////////////////////////////
        // CREATE NAGIOS CORE CHECKPOINT
        /////////////////////////////////////////////////////////////

        $cpinterval = $cfg['component_info']['nagioscore']['nom_checkpoint_interval'];
        $docp = false;
        
        $lastcp = get_meta(METATYPE_NONE, 0, "last_nom_nagioscore_checkpoint");
        if ($lastcp == null) {
            $docp = true;
        } else {
            if ($now > ($lastcp + (intval($cpinterval) * 60))) {
                $docp = true;
            }
        }
        
        if (intval($cpinterval) == 0) {
            $docp = false;
        }

        if ($docp) {
            // Config was good, so create a checkpoint
            $cmdline = $script_dir."/nom_create_nagioscore_checkpoint_cond.sh";
            $output = system($cmdline, $return_code);
            if ($return_code == 0) {
                set_meta(METATYPE_NONE, 0, "last_nom_nagioscore_checkpoint", $now);
            }
            set_meta(METATYPE_NONE, 0, "last_nom_nagioscore_checkpoint_result", $output);
        }

        break;
    }
        
    update_sysstat();
}


function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array(
        "last_check" => time()
    );
    $sdata = serialize($arr);
    update_systat_value("nom", $sdata);
}
