#!/usr/bin/php -q
<?php
//
// Utility to create CSV from RRD data
// Copyright (c) 2010-2019 Nagios Enterprises, LLC. All rights reserved.
//  

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


doit();


function doit()
{
    global $argv;
    $args = parse_argv($argv);

    $host = clean_name(grab_array_var($args, "host"));
    $service = clean_name(grab_array_var($args, "service", ""));
    $start = grab_array_var($args, "start", "");
    $end = grab_array_var($args, "end", ""); 

    if (empty($host)) {
        print_usage();
    }

    $perfdata = '/usr/local/nagios/share/perfdata/';

    $rrdfile = '_HOST_.rrd';
    if (!empty($service)) {
        $rrdfile = $service.'.rrd';
    }

    $path = $perfdata.$host.'/'.$rrdfile; 
    $start = ($start == '') ? '' : " -s $start ";
    $end = ($end == '') ? '' : " -e $end ";

    $cmd = "/usr/bin/rrdtool fetch $path AVERAGE $start $end";

    $bool = exec($cmd, $results);
    if ($bool == 1) {
        print_usage();
    }

    $data['sets'] = array(); 

    foreach ($results as $line)
    {
        // Check line syntax, ignore bad data
        $line = trim($line); 
        if (strlen($line) < 10 || empty($line)) {
            continue;
        }

        $values = explode(' ', $line);
        $time = substr(trim($values[0]), 0, 10);

        // Skip if no time data
        if (strlen($time < 9)) {
            continue;
        }

        for ($i = 1; $i < count($values); $i++)
        {
            // Create comma delineated list for JSON object
            if (!isset($data['sets'][$i-1])) {
                $data['sets'][$i-1] = '';
            }

            // Chop down string a raw float 
            $str = substr(trim($values[$i]), 0, 11);

            // Replace nan with 0
            if (strstr($str, 'nan')) {
                $data['sets'][$i-1] .= 'null, ';
            } else {      
                $data['sets'][$i-1] .= $str.', ';
            }
        }
    }

    // Print each set of data with a label in between
    foreach($data['sets'] as $set)
    {
        echo "####################################";
        echo "DATASET"; 
        echo "####################################\n";

        echo $set;
        echo "\n\n";
    }
}


function print_usage()
{
    global $argv;
    echo "\nNagios XI RRD To CSV Tool\n";
    echo "Copyright (c) 2011-2018 Nagios Enterprises, LLC\n";
    echo "\n";
    echo "Usage: ".$argv[0]." --host=<host> [--service='<service>'] [--start=<start>] [--end=<end>]\n";
    echo "\n";
    echo "Options:\n";
    echo "  <host>     = Host name [required]\n";
    echo "  <service>  = Service description: Use single quotes if spaces in name\n"; 
    echo "  <start>    = Start time. Defaults to 'today.' Accepts -12h, -3d, or YYYYMMDD format. \n";
    echo "  <end>      = End time. Defaults to 'today.' Accepts -12h, -3d, or YYYYMMDD format. \n";
    echo "\n";

    echo "This utility creates host and service definitions for use in testing.\n";
    exit(1);
}


function clean_name($string) {
    $string = preg_replace('/[ :\/\\\\]/', "_", $string);
    return $string;
}