#!/usr/bin/php -q
<?php
//
// SET/GET Nagios XI Option
// Copyright (c) 2010-2019 Nagios Enterprises, LLC. All rights reserved.
//  

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


doit();

    
function doit()
{
    global $argv;
    $value = "";
    $have_value=false;
    $args = parse_argv($argv);

    $option = grab_array_var($args, "option");
    if (array_key_exists("value", $args)) {
        $have_value = true;
        $value = grab_array_var($args, "value");
    }

    if (empty($option)) {
        echo "Nagios XI Option Mod Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --option=<opt> [--value=<newval>]\n";
        echo "\n";
        echo "Gets or sets an option in the Nagios XI (nagiosxi) database.\n";
        exit(1);
    }

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok){
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    if (!$have_value) {
        $r = get_option($option);
        echo "$r\n";
    } else{
        set_option($option, $value);
        echo "$option = $value\n";
    }

    exit(0);
}
