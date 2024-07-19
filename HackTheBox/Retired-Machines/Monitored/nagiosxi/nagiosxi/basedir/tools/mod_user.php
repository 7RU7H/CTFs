#!/usr/bin/php -q
<?php
//
// SET/GET Nagios XI User
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
    $attribute = grab_array_var($args, "attribute");
    if (array_key_exists("value", $args)) {
        $have_value = true;
        $value = grab_array_var($args, "value");
    }

    if (empty($username) || empty($attribute)) {
        echo "Nagios XI User Mod Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --username=<name> --attribute=<attr> [--value=<newval>]\n";
        echo "\n";
        echo "Gets or sets user attribute in the Nagios XI Postgres database.\n";
        exit(1);
    }

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    if ($attribute == "username") {
        echo "Invalid attribute '$attribute'\n";
        exit(1);
    }

    // Get user id
    $uid = get_user_id($username);
    if ($uid == null) {
        echo "Error: Invalid user '$username'\n";
        exit(1);
    }

    if (!$have_value) {
        $r = get_user_attr($uid, $attribute);
        if ($r == null) {
            echo "Error: Invalid attribute '$attribute'\n";
            exit(1);
        }
        echo "$r\n";
    } else {
        $r = change_user_attr($uid, $attribute, $value);
        if (!$r) {
            echo "Error: Could not update attribute '$attribute' to '$value'\n";
            exit(1);
        }
        echo "$attribute = $value\n";
    }

    exit(0);
}