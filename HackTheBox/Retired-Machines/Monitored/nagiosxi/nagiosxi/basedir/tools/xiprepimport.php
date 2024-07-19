#!/usr/bin/php -q
<?php
//
// Prep configs for import
// Copyright (c) 2009-2019 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');


$args = parse_argv($argv);
$c = count($args);
if ($c < 1) {
    echo "Nagios XI Configuration Import Prep Utilitiy\n";
    echo "Copyright (c) 2009-2019 Nagios Enterprises, LLC\n";
    echo "License: GPL\n";
    echo "Usage: xiprepimport.php [configfile]\n";
    echo "\n";
    exit();
}
$source = $args[0];


process_config_file($source);


function process_config_file($source)
{
    $fh = fopen($source, "r");
    if (!$fh) {
        echo "Error opening config file '".$source."'!\n";
        exit(2);
    }

    // Initialize arrays
    $thisobject = array();
    $inservice = false;
    $objectname = "";

    // Open "others file"
    $fname = basename($source);

    // Make sure others file and orig files aren't the same
    if ($fname == $source) {
        echo "Error: Source file cannot reside in the current working directory\n";
        exit(2);
    }

    $fho = fopen($fname, "a+");
    if (!$fho) {
        echo "Cannot open file '".$fname."' for writing!\n";
        exit(2);
    }

    while (!feof($fh)) {

        $buf = fgets($fh);
        $buf = trim($buf);

        if (strstr($buf, '#') == $buf) {
            continue;
        }
        if (empty($buf)) {
            continue;
        }

        // Start of service definition
        if ((strstr($buf, "define service{") == $buf) || (strstr($buf, "define service ") == $buf)) {
            $thisobject = array();
            $objectname = "";
            $thisobject[] = $buf;
            $inservice = true;
        } else if ($inservice) {

            // Mid or end of service definition
            if (strstr($buf, "}") == $buf) {

                $thisobject[] = $buf;
                $inobject = false;

                $fname = $objectname;
                if (empty($fname)) {
                    $fname = "_empty_host";
                }
                $fname .= ".cfg";
                write_entries_to_file($fname, $thisobject);

            } else {

                $thisobject[] = $buf;
                $parts = preg_split("/[\s]+/", $buf);

                $var = trim(array_shift($parts));
                $val = trim(implode(' ', $parts));

                // Remove spaces 
                $val = str_replace(', ', ',', $val);

                if (strstr($var, "host_name") == $var) {
                    if (empty($objectname)) {
                        $objectname = get_object_name($val);
                    } else {
                        $objectname = "_multiple_hosts";
                    }
                } else if (strstr($var, "hostgroup_name") == $var) {
                    $objectname = "_multiple_hosts";
                }
            }
        } else {
            $buf2 = str_replace(', ', ',', $buf);
            fprintf($fho, "%s\n", $buf2);
        }
    }

    fclose($fh);
    fclose($fho);
}


function get_object_name($rawname)
{
    $multiname = "_multiple_hosts";

    if (strstr($rawname, "*")) {
        $name = $multiname;
    } else if (strstr($rawname, ",")) {
        $name = trim($multiname);
    } else if (strstr($rawname, "!")) {
        $name = $multiname;
    } else {
        $length = strpos($rawname, ';');
        if ($length === false) {
            $name = trim($rawname);
        } else {
            $name = trim(substr($rawname, 0, $length));
        }
    }

    return $name;
}


function write_entries_to_file($fname, $entries)
{
    $fh = fopen($fname, "a+");
    if (!$fh) {
        fclose($fh);
        echo "Error opening ".$fname." for writing!\n";
        exit(2);
    }

    foreach ($entries as $entry) {
        fprintf($fh, "%s\n", $entry);
    }

    fclose($fh);
}