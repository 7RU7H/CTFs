<?php
//
// Load the Nagios XI configuration file
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//

define('CFG_ONLY', true);
require_once("/usr/local/nagiosxi/html/config.inc.php");

// Display all variables out
$prev = array('cfg');
export_all_variables($cfg, $prev);

function export_all_variables($arr, $prev=array())
{
    // Loop through all the config variables and display them out
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            // If it is an array we need to do some recursion
            $prev[] = $k;
            export_all_variables($v, $prev);
        } else {    
            // If it's not an array lets print the info
            if (is_string($v)) {
                $v = "'".$v."'";
            } else if (is_bool($v)) {
                if ($v) {
                    $v = 1;
                } else {
                    $v = 0;
                }
            }
            print display_previous($prev) . $k . "=" . $v . "\n";
        }
        $prev = array_diff($prev, array($k));
    }
}

function display_previous($prev=array())
{
    if (!empty($prev) && is_array($prev)) {
        $str = "";
        foreach ($prev as $p) {
            $str .= $p . "__";
        }
        return $str;
    }
}
