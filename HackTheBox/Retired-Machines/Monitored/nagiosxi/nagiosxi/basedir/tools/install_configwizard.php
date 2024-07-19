#!/usr/bin/php -q
<?php
//
// Manual Config Wizard Installation Script
// Copyright (c) 2011-2019 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


doit();


function doit()
{
    global $argv;
    $allow_restart = false;
    $args = parse_argv($argv);

    $file = grab_array_var($args, "file");
    $restart = grab_array_var($args, "restart", "true");
    $refresh = grab_array_var($args, "refresh", 0);

    if ($restart == "true") {
        $allow_restart = true;
    }

    if (empty($file)) {
        echo "Nagios XI Wizard Installation Tool\n";
        echo "Copyright (c) 2011-2019 Nagios Enterprises, LLC\n";
        echo "\n";
        echo "Usage: ".$argv[0]." --file=<zipfile> [--restart=<true/false>] [--refresh=<0/1>]\n";
        echo "\n";
        echo "Installs a new configuration wizard from a zip file.\n";
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


    // Create a new temp directory for holding the unzipped wizard
    $tmpname = random_string(5);
    echo "TMPNAME: $tmpname\n";
    $tmpdir = get_tmp_dir()."/".$tmpname;
    system("rm -rf ".$tmpdir);
    mkdir($tmpdir);

    // Unzip wizard to temp directory
    $cmdline = "cd ".$tmpdir." && unzip -o ".$zipfile;
    system($cmdline);

    // Determine wizard directory/file name
    $cdir = system("ls -1 ".$tmpdir."/");
    $wizard_name = $cdir;

    echo "WIZARD NAME: $wizard_name\n";

    // Make sure this is a config wizard
    $cmdline = "grep register_configwizard ".$tmpdir."/".$cdir."/".$wizard_name.".inc.php | wc -l";
    echo "CMD=$cmdline\n";
    $out = system($cmdline, $rc);
    echo "OUT=$out\n";

    if ($out == 0) {

        // Delete temp directory
        $cmdline = "rm -rf ".$tmpdir;
        echo "CMD: $cmdline\n";
        system($cmdline);

        $output = "Zip file is not a config wizard.";
        echo $output."\n";
        exit(1);
    }

    echo "Wizard looks ok...\n";

    // Where should the wizard go?
    $wizard_dir = get_base_dir()."/includes/configwizards/".$wizard_name;

    // Check times
    if ($refresh) {
        $newfile = $tmpdir."/".$cdir."/".$wizard_name.".inc.php";
        $ziptime = filemtime($newfile);
        $oldfile = $wizard_dir."/".$wizard_name.".inc.php";
        if (!file_exists($oldfile)) {
            $oldtime = 0;
        } else {
            $oldtime = filemtime($oldfile);
        }
        echo "ZIPTIME: $ziptime\n";
        echo "OLDTIME: $oldtime\n";
        if ($ziptime <= $oldtime) {

            echo "Wizard does not need to be updated.\n";

            // Delete temp directory
            $cmdline = "rm -rf ".$tmpdir;
            echo "CMD: $cmdline\n";
            system($cmdline);

            exit(0);
        } else{
            echo "Wizard needs to be updated...\n";
        }
    }

    // Make new wizard directory (might exist already)
    @mkdir($wizard_dir);
    
    // Move wizard to production directory and delete temp directory
    $cmdline = "cp -rf ".$tmpdir."/".$cdir." ".get_base_dir()."/includes/configwizards/";
    echo "CMD: $cmdline\n";
    system($cmdline);
    
    // Delete temp directory
    $cmdline = "rm -rf ".$tmpdir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Run internal wizard installation functions
    $args = array(
        "wizard_name" => $wizard_name,
        "wizard_dir" => $wizard_dir,
        "allow_restart" => $allow_restart
    );
    install_configwizard($args);

    // Fix permissions
    $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chown -R \$nagiosuser ".$wizard_dir;
    echo "CMD: $cmdline\n";
    system($cmdline);

    // Force executable permissions
    system('/bin/chmod -R +x '.$wizard_dir);

    echo "\n\nDone!\n";
    exit(0);
}
