<?php
//
// Script does an internal upgrade check for XI from the command line
// and forces it - can pass boot or first check
//
// Copyright (c) 2017-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../html/includes/common.inc.php');

// Database connections
$dbok = db_connect_all();
if ($dbok == false)
    exit();

// Initial values
$firstcheck = false;
$bootcheck = false;

// Check for boot call arg
if (isset($argv[1])) {
    if ($argv[1] == 'boot') {
        $bootcheck = true;
    }
}

// Do forced upgrade check
$ret = do_update_check(true, $firstcheck, $bootcheck);

if ($ret == true) {
    echo "Update check success.\n";
} else {
    echo "Update check failed.\n";
}
