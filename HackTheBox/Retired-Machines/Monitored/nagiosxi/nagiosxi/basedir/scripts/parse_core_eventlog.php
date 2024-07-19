#!/usr/bin/php -q
<?php
// 
// Parses Nagios Core Event Log for Unconfigured Objects
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);
define("SUBSYSTEM_CALL", 1);

// Include XI codebase
require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

// Boostrap the CCM
require_once(dirname(__FILE__) . '/../html/includes/components/ccm/bootstrap.php');

// Include NagiosQL functions
require_once(dirname(__FILE__) . '/../html/includes/components/nagiosql/nagiosql.inc.php');


// Run unconfigured object parsing
check_for_unconfigured_objects();


/**
 * Checks for unconfigured objects in the Nagios Core log and stores
 * them in <xi directory>/var/corelog.newobjects
 */
function check_for_unconfigured_objects()
{
    global $cfg;
    global $ccm;

    // Allow for custom directory locations
    $xidir = get_root_dir();

    $log_file = grab_array_var($cfg['component_info']['nagioscore'], 'log_file', '/usr/local/nagios/var/nagios.log'); 
    $size_file = $xidir . "/var/corelog.data";
    $diff_file = $xidir . "/var/corelog.diff";
    $obj_file = $xidir . "/var/corelog.newobjects";

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "Error: Could not connect to databases!\n";
        exit();
    }

    // Get auto configuration options
    $auto_import_unconfigured = get_option('auto_import_unconfigured', 0);
    $auto_apply_unconfigured = get_option('auto_apply_unconfigured', 1);

    // Get current size of log file
    $current_size = filesize($log_file);
    echo "CURRENT SIZE: $current_size\n";

    // Get last size of log file
    $data = @file_get_contents($size_file);
    if ($data === false) {
        $last_size = 0;
    } else {
        $last_size = intval($data);
    }
    echo "LAST SIZE: $last_size\n";

    // How much of the file should we read?
    $read_all = false;
    $read_bytes = 0;
    if ($last_size == 0 || ($last_size > $current_size)) {
        $read_all = true;
    } else {
        $read_bytes = $current_size - $last_size;
    }

    echo "READ ALL=" . (($read_all == true) ? "Yes" : "No") . "\n";
    echo "READ BYTES=$read_bytes\n";

    // Read from the file into the diff file
    if ($read_all == true) {
        $cmd = "cat $log_file > $diff_file";
    } else {
        $cmd = "tail $log_file --bytes=$read_bytes > $diff_file";
    }
    echo "CMD=$cmd\n";
    exec($cmd);

    // Save current size of log file
    $data = $current_size . "\n";
    file_put_contents($size_file, $data);

    /////////////////////////
    // PROCESS DIFF
    /////////////////////////

    // Find non-existent hosts and services
    $missing_objects = array();

    $cmds = array(
        "grep 'Warning: Check result queue contained results for ' " . $diff_file,
        "grep 'Passive check result was received for ' " . $diff_file,
        "grep 'Error: Got check result for ' " . $diff_file, // Core 4
        "grep 'Error: Got host checkresult for ' " . $diff_file, // Core 4
    );

    // Run each grep command in the diff version of the log file
    foreach ($cmds as $cmd) {

        exec($cmd, $output_lines);
        echo "CMD=$cmd\n";
        echo "MATCHES=\n";
        print_r($output_lines);

        $time_now = time();

        foreach ($output_lines as $ol) {

            $parts = explode("'", $ol);
            print_r($parts);

            $host_name = "";
            $service_name = "";

            $n = count($parts);
            if ($n == 3) {
                $host_name = $parts[1];
                if (!array_key_exists($host_name, $missing_objects)) {
                    $missing_objects[$host_name] = array(
                        "last_seen" => $time_now,
                        "services" => array()
                    );
                }
            } else if ($n == 5) {
                $host_name = $parts[3];
                $service_name = $parts[1];
                if (!array_key_exists($host_name, $missing_objects)) {
                    $missing_objects[$host_name] = array(
                        "last_seen" => $time_now,
                        "services" => array()
                    );
                }
                if (!array_key_exists($service_name, $missing_objects[$host_name]["services"])) {
                    $missing_objects[$host_name]["services"][$service_name] = $time_now;
                }
            }
            echo "HOST=$host_name, SVC=$service_name\n";
        }
    }

    echo "MISSING OBJECTS:\n";
    print_r($missing_objects);

    // Return if we didn't find any missing objects
    if (empty($missing_objects) && !$auto_import_unconfigured) {
        return;
    }

    // Read missing objects
    $old_sobj = @file_get_contents($obj_file);
    echo "OLDSMO: $old_sobj\n";
    if ($old_sobj == "") {
        $old_objects = array();
    } else {
        $old_objects = unserialize($old_sobj);
    }

    // Add old objects
    foreach ($old_objects as $host_name => $harr) {

        // Missing host
        if (!array_key_exists($host_name, $missing_objects)) {
            $missing_objects[$host_name] = array(
                "last_seen" => $harr["last_seen"],
                "services" => $harr["services"],
                "hidden_services" => $harr["hidden_services"], 
                "hide_all" => false
            );
        } else {
            // Loop through services and add missing
            foreach ($harr["services"] as $service_name => $ts) {
                // If the service now exists, remove it from the list
                if (service_exists($host_name, $service_name)) {
                    continue;
                }
                // Missing service gets added
                if (!array_key_exists($service_name, $missing_objects[$host_name]["services"])) {
                    $missing_objects[$host_name]["services"][$service_name] = $ts;
                }
            }
        }

        // Delete hosts if they now exist in the monitoring config
        if (count($missing_objects[$host_name]["services"]) == 0 && host_exists($host_name)) {
            unset($missing_objects[$host_name]);
        }
    }

    echo "NEW OBJECTS:\n";
    print_r($missing_objects);

    // ---------------------------------------------------
    // Add new objects to XI if we have it set up
    // ---------------------------------------------------

    if ($auto_import_unconfigured && can_add_more_objects()) {

        if (empty($missing_objects)) {
            return;
        }

        // Get host and service templates to use
        $auto_import_service_tpl = get_option('auto_import_service_tpl', 'xiwizard_passive_service');
        $auto_import_host_tpl = get_option('auto_import_host_tpl', 'xiwizard_passive_host');
        $auto_import_notifications = get_option('auto_import_notifications', 1);
        $auto_import_contacts = get_array_option('auto_import_contacts');
        $auto_import_contactgroups = get_array_option('auto_import_contactgroups');
        $auto_import_hostgroups = get_array_option('auto_import_hostgroups');
        $auto_import_servicegroups = get_array_option('auto_import_servicegroups');
        $auto_import_volatile = get_option('auto_import_volatile', 0);
        $auto_import_stalking = get_option('auto_import_stalking', 0);

        // Create contacts list
        $contact_names = "";
        if (!empty($auto_import_contacts)) {
            $args = array(
                "is_active" => 1,
                "contact_id" => "in:" . implode(',', array_keys($auto_import_contacts)),
            );
            $tmp = array();
            $xml = get_xml_contact_objects($args);
            foreach ($xml->contact as $c) {
                $tmp[] = $c->contact_name;
            }
            $contact_names = implode(',', $tmp);
        }

        // Create contact group list
        $contactgroup_names = "";
        if (!empty($auto_import_contactgroups)) {
            $args = array(
                "is_active" => 1,
                "contactgroup_id" => "in:" . implode(',', array_keys($auto_import_contactgroups)),
            );
            $tmp = array();
            $xml = get_xml_contactgroup_objects($args);
            foreach ($xml->contactgroup as $cg) {
                $tmp[] = $cg->contactgroup_name;
            }
            $contactgroup_names = implode(',', $tmp);
        }

        // Create host group list
        $hostgroup_names = "";
        if (!empty($auto_import_hostgroups)) {
            $args = array(
                "is_active" => 1,
                "hostgroup_id" => "in:" . implode(',', array_keys($auto_import_hostgroups)),
            );
            $tmp = array();
            $xml = get_xml_hostgroup_objects($args);
            foreach ($xml->hostgroup as $hg) {
                $tmp[] = $hg->hostgroup_name;
            }
            $hostgroup_names = implode(',', $tmp);
        }

        // Create service group list
        $servicegroup_names = "";
        if (!empty($auto_import_servicegroups)) {
            $args = array(
                "is_active" => 1,
                "servicegroup_id" => "in:" . implode(',', array_keys($auto_import_servicegroups)),
            );
            $tmp = array();
            $xml = get_xml_servicegroup_objects($args);
            foreach ($xml->servicegroup as $sg) {
                $tmp[] = $sg->servicegroup_name;
            }
            $servicegroup_names = implode(',', $tmp);
        }

        // Loop through the missing objects
        $now = time();
        $objs = array();
        foreach ($missing_objects as $hostname => $obj) {

            // Check if we still have "room" to configure more objects
            if (!can_add_more_objects()) {
                break;
            }

            // Check if it's an integer and get the id of the template
            if (is_numeric($auto_import_host_tpl)) {
                $tpl = nagiosql_get_host_templates($auto_import_host_tpl);
                if (!empty($tpl)) {
                    $auto_import_host_tpl = $tpl[0]['template_name'];
                } else {
                    $auto_import_host_tpl = "xiwizard_passive_host";
                }
            }
            if (is_numeric($auto_import_service_tpl)) {
                $tpl = nagiosql_get_service_templates($auto_import_service_tpl);
                if (!empty($tpl)) {
                    $auto_import_service_tpl = $tpl[0]['template_name'];
                } else {
                    $auto_import_service_tpl = "xiwizard_passive_service";
                }
            }

            if (!host_exists($hostname)) {
                $tmp = array(
                    "type" => OBJECTTYPE_HOST,
                    "use" => $auto_import_host_tpl,
                    "host_name" => $hostname,
                    "address" => $hostname,
                    "icon_image" => "passiveobject.png",
                    "statusmap_image" => "passiveobject.png",
                    'stalking_options' => ($auto_import_stalking) ? "o,u,d" : "n",
                    '_xi_passive_gen' => $now,
                    '_xi_passive_firstcheck' => $obj["last_seen"]
                );

                // Add host groups
                if (!empty($hostgroup_names)) {
                    $tmp['hostgroups'] = $hostgroup_names;
                }

                $objs[] = $tmp;
            }

            if (!empty($obj["services"])) {
                foreach ($obj["services"] as $svcname => $ts) {
                    $tmp = array(
                        'type' => OBJECTTYPE_SERVICE,
                        'host_name' => $hostname,
                        'service_description' => $svcname,
                        'use' => $auto_import_service_tpl,
                        'check_interval' => 1,
                        'retry_interval' => 1,
                        'max_check_attempts' => 1,
                        'is_volatile' => ($auto_import_volatile) ? 1 : 0,
                        'stalking_options' => ($auto_import_stalking) ? "o,w,u,c" : "n",
                        '_xi_passive_gen' => $now,
                        '_xi_passive_firstcheck' => $ts
                    );

                    if (!empty($servicegroup_names)) {
                        $tmp['servicegroups'] = $servicegroup_names;
                    }

                    $objs[] = $tmp;
                }
            }

            // Unset the host after creating the object
            unset($missing_objects[$hostname]);

        }

        // Add on contacts / contact groups
        foreach ($objs as &$obj) {
            if (!empty($contact_names)) {
                $obj['contacts'] = $contact_names;
            }
            if (!empty($contactgroup_names)) {
                $obj['contact_groups'] = $contactgroup_names;
            }
        }

        $firsthost = "";
        $config = get_cfg_objects_str($objs, $firsthost);
        $ccm->import->fileImport($config, 1, true);

        // If reset apply configuration, do so now
        if ($auto_apply_unconfigured) {
            submit_command(COMMAND_NAGIOSCORE_APPLYCONFIG);
        }
    }

    $sobj = serialize($missing_objects);
    echo "SMO: $sobj\n";

    // Save missing objects and set permissions
    file_put_contents($obj_file, $sobj);
    chmod($obj_file, 0664);
    chgrp($obj_file, "nagios");
}
