<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

if (!isset($dashlets)) {
    $dashlets = array();
}

// Include all dashlets - only if we're in the UI
if (!defined("BACKEND") && !defined("SUBSYSTEM") && !defined("SKIPDASHLETS")) {
    $p = dirname(__FILE__) . "/dashlets/";
    $subdirs = scandir($p);
    foreach ($subdirs as $sd) {
        if ($sd == "." || $sd == "..")
            continue;
        $d = $p . $sd;
        if (is_dir($d)) {
            $cf = $d . "/$sd.inc.php";
            if (file_exists($cf))
                include_once($cf);
        }
    }
}
