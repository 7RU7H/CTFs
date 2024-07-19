#!/usr/bin/php -q
<?php
//
// Manual Dashlet Installation Script
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

    if (empty($file)) {
        echo "Nagios XI Dashlet Installation Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --file=<zipfile>  [--refresh=<0/1>]\n";
        echo "\n";
        echo "Installs a new dashlet from a zip file.\n";
        exit(1);
    }

    $zipfile = realpath($file);

    if (!file_exists($zipfile)) {
        echo "ERROR: File '$file' does not exist\n";
        exit(1);
    }

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    // Create a new temp directory for holding the unzipped dashlet
    $tmpname = random_string(5);
    echo "TMPNAME: $tmpname\n";
    $tmpdir = get_tmp_dir()."/".$tmpname;
    system("rm -rf ".$tmpdir);
    mkdir($tmpdir);

    // Unzip dashlet to temp directory
    $cmdline = "cd ".$tmpdir." && unzip -o ".$zipfile;
    system($cmdline);

    // Determine dashlet directory/file name
    $cdir = system("ls -1 ".$tmpdir."/");
    $dashlet_name = $cdir;

    echo "DASHLET NAME: $dashlet_name\n";

    // Make sure this is a dashlet
    $cmdline = "grep register_dashlet ".$tmpdir."/".$cdir."/".$dashlet_name.".inc.php | wc -l";
    echo "CMD=$cmdline\n";
    $out = system($cmdline, $rc);
    echo "OUT=$out\n";

    if ($out == 0) {

        // Delete temp directory
        $cmdline = "rm -rf ".$tmpdir;
        echo "CMD: $cmdline\n";
        system($cmdline);

        $output = "Zip file is not a dashlet.";
        echo $output."\n";
        exit(1);
    }

    echo "Dashlet looks ok...\n";

    // Where should the dashlet go?
    $dashlet_dir = get_base_dir()."/includes/dashlets/".$dashlet_name;

    // Check times
    if ($refresh) {
        $newfile = $tmpdir."/".$cdir."/".$dashlet_name.".inc.php";
        $ziptime = filemtime($newfile);
        $oldfile = $dashlet_dir."/".$dashlet_name.".inc.php";
        if (!file_exists($oldfile)) {
            $oldtime = 0;
        } else {
            $oldtime = filemtime($oldfile);
        }

        echo "ZIPTIME: $ziptime\n";
        echo "OLDTIME: $oldtime\n";
        if ($ziptime <= $oldtime) {

            echo "Dashlet does not need to be updated.\n";

            // Delete temp directory
            $cmdline = "rm -rf ".$tmpdir;
            echo "CMD: $cmdline\n";
            system($cmdline);

            exit(0);
        } else {
            echo "Dashlet needs to be updated...\n";
        }
    }

    // Make new dashlet directory (might exist already)
    @mkdir($dashlet_dir);

    // Move dashlet to production directory and delete temp directory
    $cmdline = "cp -rf ".$tmpdir."/".$cdir." ".get_base_dir()."/includes/dashlets/";
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Delete temp directory
    $cmdline = "rm -rf ".$tmpdir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Fix permissions
    $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chown -R \$nagiosuser ".$dashlet_dir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Force executable permissions
    system('/bin/chmod -R +x '.$dashlet_dir);

    echo "\n\nDone!\n";
    exit(0);
}