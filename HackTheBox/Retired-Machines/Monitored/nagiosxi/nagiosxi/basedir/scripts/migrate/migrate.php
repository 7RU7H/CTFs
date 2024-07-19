#!/usr/bin/php -q
<?php
//
// Migrate Server Script
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//
// Transfers Nagios Core system configuration and plugins to Nagios XI system.
//

define("SUBSYSTEM", 1);

// Include XI codebase
require_once(dirname(__FILE__) . '/../../html/includes/constants.inc.php');
require_once(dirname(__FILE__) . '/../../html/config.inc.php');

// Boostrap the CCM
require_once(dirname(__FILE__) . '/../../html/includes/components/ccm/bootstrap.php');

// Connect to databases
db_connect_all();

// Get all options/config settings
$overwrite = 0;
$clear = 0;
$address = '';
$username = '';
$password = '';
$nagios_cfg = '';

$opts = getopt("a:u:p:C:eoch", array('help', 'clear', 'overwrite'));

// Required options
if (isset($opts['a'])) {
    $address = $opts['a'];
}
if (isset($opts['u'])) {
    $username = $opts['u'];
}
if (isset($opts['p'])) {
    $password = $opts['p'];
}

// Optional

if (isset($opts['C'])) {
    $nagios_cfg = $opts['C'];
}

if (isset($opts['e'])) {
    $password = decrypt_data($password);
}
if (isset($opts['o']) || isset($opts['overwrite'])) {
    $overwrite = 1;
}
if (isset($opts['c']) || isset($opts['clear'])) {
    $clear = 1;
}

// Help section

if (isset($opts['help']) || isset($opts['h'])) {
    echo "Nagios Migration Tool\n";
    echo "Copyright 2020 Nagios Enterprises, LLC\n";
    echo "\nRequired Options:\n";
    echo "-a        Address to Nagios Core system.\n";
    echo "-u        SSH user, normally root but can be user with sudo.\n";
    echo "-p        Password for the user.\n";
    echo "\nOptional Options:\n";
    echo "-e                Use to denote that the password is a Nagios encrypted password.\n";
    echo "-C                Location of nagios.cfg file on the remote system.\n";
    echo "-c | --clear      Clear ALL current configuration before doing migration.\n";
    echo "-o | --overwrite  Overwrite config objects and plugins even if they exist already.\n";
}

// Run ansible script

run_migration_ansible($address, $username, $password, $nagios_cfg, $overwrite);

function run_migration_ansible($address, $username, $password, $nagios_cfg, $overwrite = 0)
{
    global $ccm;

    $dir = get_root_dir() . '/scripts/migrate';
    $job_name = uniqid();
    $vault_password = uniqid();
    $job_dir = $dir.'/jobs/'.$job_name;
    $yml_file = file_get_contents($dir."/templates/migrate_core.yml");

    // Set job name
    set_option('migration_job_name', $job_name);

    // Check if we are root or not
    $become = 'no';
    if ($username != 'root') {
        $become = 'yes';
    }

    // Replace items
    $migrate_core_role = $dir.'/roles/migrate_core';
    $find = array('$BECOME$', '$REMOTE_USER$', '$REMOTE_USER_PASSWORD$', '$MIGRATE_CORE_ROLE$');
    $replace = array($become, $username, str_replace("'", "\'", $password), $migrate_core_role);
    $yml_file = str_replace($find, $replace, $yml_file);

    // Generate temp migrate folder and make sure that it's empty for the migrate
    if (!is_dir(get_root_dir()."/tmp/migrate")) {
        mkdir(get_root_dir()."/tmp/migrate");
    }
    exec("rm -rf ".get_root_dir()."/tmp/migrate/*", $output, $code);

    // Generate the actual folder and files
    if (!is_dir($job_dir)) {
        mkdir($job_dir);
    }
    copy($dir.'/templates/ansible.cfg', $job_dir.'/ansible.cfg');
    $job_file = $job_dir.'/'.$job_name.'.yml';
    file_put_contents($job_file, $yml_file);

    // Add hosts and password file
    file_put_contents($job_dir.'/hosts', $address);
    file_put_contents($job_dir.'/.vp', $vault_password);

    // Make encrypted ansible playbook to protect passwords
    $cmd = "echo -n '".$vault_password."' | ansible-vault encrypt --vault-password-file=".$job_dir.'/.vp'." ".$job_file." --output ".$job_file;
    $x = exec($cmd, $output, $code);

    // Add extra args if we have to
    $args = array('bundler_args' => '');
    if (!empty($nagios_cfg)) {
        $args['bundler_args'] .= '-f '.escapeshellarg($nagios_cfg);
    }
    $extra = ' --extra-vars='.escapeshellarg(json_encode($args));

    // Run ansible playbook
    $cmd = "cd $job_dir; ansible-playbook ".$job_name.".yml -i hosts $extra --vault-password-file=.vp > output.json 2> errors.txt";
    $x = exec($cmd, $output, $code);
    if ($code === 0) {
        set_option('migration_status_transfer', 1);
    } else {
        set_option('migration_error', 1);
        echo "Error: Could not transfer the configuration.";
        unlink($job_dir.'/.vp');
        exit(1);
    }

    print $x."\n";
    print $output."\n";
    print $code."\n";

    // Delete temp password file
    unlink($job_dir.'/.vp');

    // Read output and add in the hosts that finished properly
    $output = file_get_contents($dir.'/jobs/'.$job_name.'/output.json');
    $start = strpos($output, '{');
    $raw = substr($output, $start);
    $data = json_decode($raw, true);

    // Run the unbundler script once we have the new bundle on the XI system
    $cmd = "cd ".get_root_dir()."/tmp/migrate && tar xf nagiosbundle-*.tar.gz";
    $x = exec($cmd, $output, $code);
    // This is done after tarball is extracted to make sure that this is our python script being ran...
    $cmd = "cp -f ".get_root_dir()."/scripts/migrate/nagios_unbundler.py ".get_root_dir()."/tmp/migrate/nagios_unbundler.py";
    $x = exec($cmd, $output, $code);
    $cmd = "cd ".get_root_dir()."/tmp/migrate && python nagios_unbundler.py";
    $x = exec($cmd, $output, $code);
    if ($code === 0) {
        set_option('migration_status_prep', 1);
    } else {
        set_option('migration_error', 1);
        echo "Error: Configs could not be processed.";
        exit(1);
    }

    // Import order!
    // Commands > Time Periods > Contact Templates > Contacts > Contact Groups > Host Templates > 
    // Hosts > Host Groups > Service Templates > Services > Service Groups

    // Import the bundled configurations
    $cfg_dir = get_root_dir()."/tmp/migrate/nagiosbundle-config/";
    print "\n--- migrate.php -------------------\n";
    print "> Setting import directory: $cfg_dir\n";
    print "> Importing config files into the CCM\n";

    // Import everything except services and service groups (IN ORDER)
    $mfl = file_list($cfg_dir, "/.*\.cfg/");
    if (!empty($mfl)) {
        m_import_file($cfg_dir, $mfl, 'commands.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'timeperiods.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'contacttemplates.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'contacts.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'contactgroups.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'hosttemplates.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'hosts.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'hostgroups.cfg', $overwrite);
        m_import_file($cfg_dir, $mfl, 'servicetemplates.cfg', $overwrite);
    } else {
        set_option('migration_error', 1);
        echo "Error: No config files to parse.";
        exit(1);
    }

    // Import service objects
    $svcs_cfg_dir = $cfg_dir."/services/";
    $fl = file_list($svcs_cfg_dir, "/.*\.cfg/");
    if (!empty($fl)) {
        foreach ($fl as $f) {
            m_import_file($svcs_cfg_dir, $fl, $f, $overwrite);
        }
    }

    // Import the service groups last
    m_import_file($cfg_dir, $mfl, 'servicegroups.cfg', $overwrite);

    set_option('migration_status_import', 1);

    // Make sure the output was fine and import each of the files
    if ($code === 0) {
        $cmd = get_root_dir()."/scripts/restart_nagios_with_export.sh";
        $x = exec($cmd, $output, $code);
        if ($code === 0) {
            set_option('migration_status_apply', 1);
        } else {
            set_option('migration_error', 1);
            echo 'Error: Could not apply configuration. The old configuration has been applied, but your migrated config has been imported.';
            exit(3);
        }
    } else {
        // Error with the bundle
        set_option('migration_error', 1);
        echo "Error: Could not import the Nagios Core configuration.";
        exit(2);
    }

    foreach ($output as $line) {
        echo $line."\n";
    }
    set_option('migration_complete', 1);
    set_option('migration_running', 0);
}

function m_import_file($cfg_dir, $fl, $file, $overwrite)
{
    global $ccm;

    if (in_array($file, $fl)) {
        $full = $cfg_dir . $file;
        print "  - Importing: $full .. \n";
        $error = $ccm->import->fileImport($full, $overwrite, false, true);
        return $error;
    }

    return true;
}
