<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

if (!isset($components)) {
    $components = array();
}

if (!defined("SKIPCOMPONENTS")) {

    // Include all components
    $parentdir = dirname(__FILE__) . "/components/";
    $subdirs = scandir($parentdir);

    foreach ($subdirs as $subdir) {
        if ($subdir == "." || $subdir == "..")
            continue;

        $curdir = $parentdir . $subdir;
        $curname = $curdir;
        if (is_dir($curdir)) {

            $component_file = $curdir . "/{$subdir}.inc.php";
            if (file_exists($component_file)) {

                $components_temp = $components;
                reset($components);

                include_once($component_file);

                foreach ($components as $name => $carray) {
                    $components[$name][COMPONENT_DIRECTORY] = basename($curdir);
                    $curname = $name;
                }

                $components_temp[$curname] = $components[$curname];
                $components = $components_temp;
            }
        }
    }
}
