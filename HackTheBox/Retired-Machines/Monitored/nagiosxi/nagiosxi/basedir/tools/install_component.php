#!/usr/bin/php -q
<?php
//
// Manual Component Installation Script
// Copyright (c) 2011-2019 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


doit();

    
function doit()
{
    global $argv;
    $args = parse_argv($argv);
    
    $file = grab_array_var($args, "file");
    $refresh = grab_array_var($args, "refresh", 0);
        
    if ($file == "") {
        echo "Nagios XI Component Installation Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --file=<zipfile>  [--refresh=<0/1>]\n";
        echo "\n";
        echo "Installs a new component from a zip file.\n";
        exit(1);
    }

    $zipfile = realpath($file);

    if (!file_exists($zipfile)) {
        echo "ERROR: File '$file' does not exist\n";
        exit(1);
    }

    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    // Create a new temp directory for holding the unzipped component
    $tmpname = random_string(5);
    echo "TMPNAME: $tmpname\n";
    $tmpdir = get_tmp_dir() . "/" . $tmpname;
    system("rm -rf " . $tmpdir);
    mkdir($tmpdir);
    
    // Unzip component to temp directory
    $cmdline = "cd " . $tmpdir . " && unzip -o " . $zipfile;
    system($cmdline);
    
    // Determine component directory/file name
    $cdir = system("ls -1 " . $tmpdir . "/");
    $component_name = $cdir;
    
    echo "COMPONENT NAME: $component_name\n";
    
    // Make sure this is a component
    $cmdline = "grep register_component " . $tmpdir . "/" . $cdir . "/" . $component_name . ".inc.php | wc -l";
    echo "CMD=$cmdline\n";
    $out = system($cmdline, $rc);
    echo "OUT=$out\n";
    if ($out == "0") {
    
        // Delete temp directory
        $cmdline = "rm -rf " . $tmpdir;
        echo "CMD: $cmdline\n";
        system($cmdline);
        
        $output = "Zip file is not a component.";
        echo $output . "\n";
        exit(1);
    }
        
    echo "Component looks ok...\n";
    
    // Where should the component go?
    $component_dir = get_base_dir() . "/includes/components/" . $component_name;
    
    // Check times
    if ($refresh == 1) {
        $newfile = $tmpdir . "/" . $cdir . "/" . $component_name . ".inc.php";
        $ziptime = filemtime($newfile);
        $oldfile = $component_dir . "/" . $component_name . ".inc.php";
        if (!file_exists($oldfile)) {
            $oldtime = 0;
        } else {
            $oldtime = filemtime($oldfile);
        }
        echo "ZIPTIME: $ziptime\n";
        echo "OLDTIME: $oldtime\n";
        if ($ziptime <= $oldtime) {

            echo "Component does not need to be updated.\n";
            
            // Delete temp directory
            $cmdline = "rm -rf ".$tmpdir;
            echo "CMD: $cmdline\n";
            system($cmdline);
            exit(0);

        } else {
            echo "Component needs to be updated...\n";
        }
    }

    // Make new component directory (might exist already)
    @mkdir($component_dir);

    // Move component to production directory and delete temp directory
    $cmdline = "cp -rf " . $tmpdir . "/" . $cdir . " " . get_base_dir() . "/includes/components/";
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Delete temp directory
    $cmdline = "rm -rf " . $tmpdir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Run internal component installation functions
    $args = array(
        "component_name" => $component_name,
        "component_dir" => $component_dir
    );
    install_component($args);

    // Fix permissions
    $cmdline = ". " . get_root_dir() . "/var/xi-sys.cfg && chown -R \$nagiosuser:\$nagiosgroup " . $component_dir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Force executable permissions
    echo 'CMD: /bin/chmod -R +x ' . $component_dir . "\n"; 
    system('/bin/chmod -R +x ' . $component_dir); 

    echo "\n\nDone!\n";

    exit(0);
}
