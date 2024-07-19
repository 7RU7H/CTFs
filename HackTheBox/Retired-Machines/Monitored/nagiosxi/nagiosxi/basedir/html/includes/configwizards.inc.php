<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

if (!isset($configwizards))
    $configwizards = array();

// Include all dashlets - only if we're in the UI
if (!defined("BACKEND") && !defined("SUBSYSTEM")) {
    $p = dirname(__FILE__) . "/configwizards/";
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

// Alphabetically sort configwizards by display title.

$wizardnames = array();
foreach ($configwizards as $wizard) {
    $wizardnames[] = $wizard['display_title'];
}

array_multisort($wizardnames, SORT_ASC, $configwizards);
