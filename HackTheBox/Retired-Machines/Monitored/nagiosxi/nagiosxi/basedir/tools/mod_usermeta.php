#!/usr/bin/php -q
<?php
//
// SET/GET Nagios XI User Meta Information
// Copyright (c) 2011-2019 Nagios Enterprises, LLC. All rights reserved.
//  

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


doit();

    
function doit()
{
    global $argv;
    $value = "";
    $have_value = false;
    $args = parse_argv($argv);

    $username = grab_array_var($args, "username");
    $key = grab_array_var($args, "key");
    $autoload = intval(grab_array_var($args, "autoload", 0));
    if (array_key_exists("value", $args)) {
        $have_value = true;
        $value = grab_array_var($args, "value");
    }

    if (empty($username) || empty($key)) {
        echo "Nagios XI User Meta Mod Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --username=<name> --key=<key> [--value=<newval>] [--autoload=<0/1>]\n";
        echo "\n";
        echo "Gets or sets user meta information in the Nagios XI Postgres database.\n";
        exit(1);
    }

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    // Get user id
    $uid = get_user_id($username);
    if ($uid == null) {
        echo "Error: Invalid user '$username'\n";
        exit(1);
    }

    if (!$have_value) {
        $r = get_user_meta($uid, $key);
        if ($r == null) {
            echo "Error: Invalid key '$key'\n";
            exit(1);
        }
        echo "$r\n";
    } else {
        $r = set_user_meta($uid, $key, $value, $autoload);
        if (!$r) {
            echo "Error: Could not update key '$key' to '$value'\n";
            exit(1);
        }
        echo "$key = $value\n";
    }

    exit(0);
}