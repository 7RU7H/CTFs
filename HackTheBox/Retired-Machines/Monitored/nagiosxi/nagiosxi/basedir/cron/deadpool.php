#!/bin/env php -q
<?php
//
// Deadpool Reaper
// Copyright (c) 2012-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

require_once(dirname(__FILE__).'/../html/includes/components/nagiosql/nagiosql.inc.php');


$max_time = 55;


$doit = init_deadpool_reaper();
if ($doit) {
    init_language();
    do_deadpool_reaper();
} else {
    echo "deadpool init cancelled\n";
    update_sysstat(); 
}


function init_deadpool_reaper()
{
    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false){
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }
    echo "CONNECTED TO DATABASES\n";

    // Added option to disable deadpool reaper
    $default = 0;
    $doit = get_option('enable_deadpool_reaper', $default);

    if ($doit == 0){
        echo "deadpool reaper is disabled\n";
        return false;
    }

    return true;
}


function create_deadpool_host_hostgroup($name, $alias)
{
    // Create the import file
    $fname = "";
    $fh = create_nagioscore_import_file($fname);

    fprintf($fh, "define hostgroup{\n");
    fprintf($fh, "hostgroup_name\t%s\n", $name);
    fprintf($fh, "alias\t%s\n", $alias);
    fprintf($fh, "}\n");

    // Commit the import file
    fclose($fh);
    $newfname = commit_nagioscore_import_file($fname);
    echo "IMPORT FILE=$newfname\n";
}


function create_deadpool_service_servicegroup($name, $alias)
{
    // Create the import file
    $fname = "";
    $fh = create_nagioscore_import_file($fname);

    fprintf($fh, "define servicegroup{\n");
    fprintf($fh, "servicegroup_name\t%s\n", $name);
    fprintf($fh, "alias\t%s\n", $alias);
    fprintf($fh, "}\n");

    // Commit the import file
    fclose($fh);
    $newfname = commit_nagioscore_import_file($fname);
    echo "IMPORT FILE=$newfname\n";
}


function do_deadpool_reaper()
{
    global $max_time;
    global $db_tables;

    $testing = false;
    $reconfigure_nagios = false;

    // Get options
    $deadpool_remove_rrds = get_option('deadpool_remove_rrds', 0);

    $host_stage_1_age = get_option('deadpool_reaper_stage_1_host_age', 60*60*24*7);
    $host_stage_2_age = get_option('deadpool_reaper_stage_2_host_age', 60*60*24*30);

    $host_hostgroup_name = get_option('deadpool_reaper_host_hostgroup_name', "host-deadpool");
    $host_hostgroup_alias = get_option('deadpool_reaper_host_hostgroup_alias', "Host Deadpool");
    $host_filter = get_option("deadpool_host_filter", "");

    $service_stage_1_age = get_option('deadpool_reaper_stage_1_service_age', 60*60*24*7);
    $service_stage_2_age = get_option('deadpool_reaper_stage_2_service_age', 60*60*24*30);
    $service_servicegroup_name = get_option('deadpool_reaper_service_servicegroup_name', "service-deadpool");
    $service_servicegroup_alias = get_option('deadpool_reaper_service_servicegroup_alias', "Service Deadpool");
    $service_filter = get_option("deadpool_service_filter", "");

    $stage_2_host_action = get_option('stage_2_host_action', 'delete');
    $stage_2_service_action = get_option('stage_2_service_action', 'delete');

    // Explode filters
    $host_filters = explode("\n", $host_filter);
    $service_filters = explode("\n", $service_filter);

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // DETERMINE HOSTS TO PROCESS
    //
    //////////////////////////////////////////////////////////////////////////////////////

    echo "DETERMINING HOSTS TO PROCESS...\n";

    // Determine cutoff times
    $now = time();
    $stage_1_cutoff = $now - $host_stage_1_age;
    $stage_2_cutoff = $now - $host_stage_2_age;

    $stage1_candidates = 0;
    $stage2_candidates = 0;

    echo "NOW: $now\n";
    echo "STAGE 1 AGE: $host_stage_1_age\n";
    echo "STAGE 2 AGE: $host_stage_2_age\n";
    echo "STAGE 1 CUTOFF: $stage_1_cutoff\n";
    echo "STAGE 2 CUTOFF: $stage_2_cutoff\n";

    // Get current deadpool hostgroup members
    $host_deadpool_member_count = 0;
    $host_deadpool_members = array();

    $args = array();
    $args["hostgroup_name"] = $host_hostgroup_name;
    $xml = get_xml_hostgroup_member_objects($args);
    if ($xml && $xml->recordcount > 0) {
        $host_deadpool_member_count = intval($xml->recordcount);

        foreach ($xml->hostgroup->members->host as $hgm) {
            $host_deadpool_members[] = strval($hgm->host_name);
        }
    }

    echo "CURRENT DEADPOOL MEMBERS:\n";
    print_r($host_deadpool_members);

    // Initialize candidate arrays
    $host_candidates = array();

    // Find all hosts in a DOWN state
    unset($args);
    $args = array();
    $args["current_state"] = 1;
    $xml = get_xml_host_status($args);

    if ($xml) {
        foreach ($xml->hoststatus as $hs) {
            $hostname = strval($hs->name);
            $hostid = intval($hs->host_id);

            $laststatechange = strtotime($hs->last_state_change);
            $ago = $now - $laststatechange;

            $candidate = 0;
            $stage = 0;
            if ($laststatechange <= $stage_2_cutoff) {
                $stage = 2;
                $stage2_candidates++;
                $candidate = 1;
            } else if ($laststatechange <= $stage_1_cutoff) {
                $stage = 1;
                $stage1_candidates++;
                $candidate = 1;
            }

            echo "HOST: $hostname = $laststatechange ($ago seconds ago) [$candidate]\n";

            // Exclude passive-only hosts
            $ace = intval($hs->active_checks_enabled);
            $pce = intval($hs->passive_checks_enabled);
            if ($ace == 0 && $pce == 1) {
                echo "   Passive only -> skipping host\n";
                continue;
            }

            // Skip hosts that have active checks disabled
            if ($ace == 0) {
                echo "   Active checks disabled -> skipping host\n";
                continue;
            }

            // Check host filters
            $skip = false;
            foreach ($host_filters as $hf) {
                $hf = trim($hf);

                // Exact match
                if (!strcmp($hostname, $hf)) {
                    echo "   Matched host filter '$hf' -> skipping host\n";
                    $skip = true;
                    continue;
                }

                // Regex match
                if (@preg_match($hf, $hostname)) {
                    echo "   Matched host filter '$hf' -> skipping host\n";
                    $skip = true;
                    continue;
                }
            }

            if ($skip == true) {
                continue;
            }

            if ($candidate == 1) {
                $host_candidates[] = array(
                    "name" => $hostname,
                    "id" => $hostid,
                    "laststatechange" => $laststatechange,
                    "ago" => $ago,
                    "stage" => $stage
                );
            }
        }
    } else {
        echo "Erorr: Could not parse host status XML.\n";
    }

    echo "HOST CANDIDATES\n";
    print_r($host_candidates);

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // DETERMINE SERVICES TO PROCESS
    //
    //////////////////////////////////////////////////////////////////////////////////////

    echo "DETERMINING SERVICES TO PROCESS...\n";

    // Determine cutoff times
    $now = time();
    $stage_1_cutoff = $now - $service_stage_1_age;
    $stage_2_cutoff = $now - $service_stage_2_age;

    $stage1_candidates = 0;
    $stage2_candidates = 0;

    echo "NOW: $now\n";
    echo "STAGE 1 AGE: $service_stage_1_age\n";
    echo "STAGE 2 AGE: $service_stage_2_age\n";
    echo "STAGE 1 CUTOFF: $stage_1_cutoff\n";
    echo "STAGE 2 CUTOFF: $stage_2_cutoff\n";

    // Get current deadpool servicegroup members
    $service_deadpool_member_count = 0;
    $service_deadpool_members = array();

    $args = array();
    $args["servicegroup_name"] = $service_servicegroup_name;
    $xml = get_xml_servicegroup_member_objects($args);
    if ($xml && $xml->recordcount > 0) {
        $service_deadpool_member_count = intval($xml->recordcount);
        foreach ($xml->servicegroup->members->service as $sgm) {
            $service_deadpool_members[] = array(
                "host" => strval($sgm->host_name),
                "service" => strval($sgm->service_description),
            );
        }
    }

    echo "CURRENT SERVICE DEADPOOL MEMBERS:\n";
    print_r($service_deadpool_members);

    // Initialize candidate arrays
    $service_candidates = array();

    // Find all services in CRITICAL/UNKNOWN state
    unset($args);
    $args = array();
    $args["current_state"] = "in:2,3";
    $xml = get_xml_service_status($args);
    
    if ($xml) {
        foreach ($xml->servicestatus as $ss) {
            $hostname = strval($ss->host_name);
            $servicename = strval($ss->name);
            $serviceid = intval($ss->service_id);

            $laststatechange = strtotime($ss->last_state_change);
            $ago = $now - $laststatechange;

            $candidate = 0;
            $stage = 0;
            if ($laststatechange <= $stage_2_cutoff) {
                $stage = 2;
                $stage2_candidates++;
                $candidate = 1;
            } else if ($laststatechange <= $stage_1_cutoff) {
                $stage = 1;
                $stage1_candidates++;
                $candidate = 1;
            }

            echo "HOST/SVC: $hostname/$servicename = $laststatechange ($ago seconds ago) [$candidate]\n";

            // Exclude passive-only services
            $ace = intval($ss->active_checks_enabled);
            $pce = intval($ss->passive_checks_enabled);
            if ($ace == 0 && $pce == 1) {
                echo "   Passive only -> skipping service\n";
                continue;
            }

            // Skip services that have active checks disabled
            if ($ace == 0) {
                echo "   Active checks disabled -> skipping service\n";
                continue;
            }

            // Skip services associated with hosts that are being processed
            if (in_array($hostname, $host_candidates)) {
                echo "   Host is being processed -> skipping service\n";
                continue;
            }

            // Check host filters
            $skip = false;
            foreach ($host_filters as $hf) {
                $hf = trim($hf);

                // Exact match
                if (!strcmp($hostname,$hf)) {
                    echo "   Matched host filter '$hf'-> skipping service\n";
                    $skip = true;
                    continue;
                }

                // Regex match
                if (@preg_match($hf,$hostname)) {
                    echo "   Matched host filter '$hf' -> skipping service\n";
                    $skip = true;
                    continue;
                }
            }

            if ($skip == true) {
                continue;
            }

            // Check service filters
            foreach ($service_filters as $sf) {
                $sf = trim($sf);

                // Exact match
                if (!strcmp($servicename, $sf)) {
                    echo "   Matched service filter '$sf' -> skipping service\n";
                    $skip = true;
                    continue;
                }

                // Regex match
                if (@preg_match($sf, $servicename)) {
                    echo "   Matched service filter '$sf' -> skipping service\n";
                    $skip = true;
                    continue;
                }
            }

            if ($skip == true) {
                continue;
            }

            if ($candidate == 1) {
                $service_candidates[] = array(
                    "hostname" => $hostname,
                    "servicename" => $servicename,
                    "id" => $serviceid,
                    "laststatechange" => $laststatechange,
                    "ago" => $ago,
                    "stage" => $stage
                );
            }
        }
    } else {
        echo "Could not parse service status XML.\n";
    }

    echo "SERVICE CANDIDATES\n";
    print_r($service_candidates);

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // CREATE GROUPS
    //
    //////////////////////////////////////////////////////////////////////////////////////

    // MAKE SURE WE HAVE A DEADPOOL HOSTGROUP
    $total_host_candidates = count($host_candidates);
    if ($total_host_candidates > 0) {

        // Get hostgroup id in NagiosQL
        $nagiosql_hostgroup_id = nagiosql_get_hostgroup_id($host_hostgroup_name);

        // Create deadpool hostgroup if necessary
        if ($nagiosql_hostgroup_id <= 0) {
            $reconfigure_nagios = true;
            $nagiosql_hostgroup_id = create_deadpool_host_hostgroup($host_hostgroup_name, $host_hostgroup_alias);
            echo "Created deadpool hostgroup.\n";
        }
    }

    // MAKE SURE WE HAVE A DEADPOOL SERVICEGROUP
    $total_service_candidates = count($service_candidates);
    if ($total_service_candidates > 0) {

        // Get servicegroup id in NagiosQL
        $nagiosql_servicegroup_id = nagiosql_get_servicegroup_id($service_servicegroup_name);

        // Create deadpool servicegroup if necessary
        if ($nagiosql_servicegroup_id <= 0) {
            $reconfigure_nagios = true;
            $nagiosql_servicegroup_id = create_deadpool_service_servicegroup($service_servicegroup_name, $service_servicegroup_alias);
            echo "Created deadpool servicegroup.\n";
        }
    }

    // Reconfigure Nagios
    if ($reconfigure_nagios == true) {
        echo "Reconfiguring Nagios with new group(s) - exiting until next run...\n";
        reconfigure_nagioscore();
        exit();
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // PROCESS HOSTS
    //
    //////////////////////////////////////////////////////////////////////////////////////

    echo "PROCESSING HOSTS...\n";
    $processed_hosts = array();

    // PROCESS CANDIDATES
    $mods = 0;
    foreach ($host_candidates as $h) {

        $stage = grab_array_var($h, "stage");
        $name = grab_array_var($h, "name");

        echo "Processing host '$name' in stage $stage\n";

        // Get host id in NagiosQL
        $nagiosql_host_id = nagiosql_get_host_id($name);
        if ($nagiosql_host_id == 0) {
            echo "Error: Could not get ID for host '$name' - skipping\n";
            continue;
        }

        echo "NagiosQL Host ID = $nagiosql_host_id\n";

        // STAGE 1
        if ($stage == 1) {

            // Exclude hosts already in the deadpool
            if (in_array($name, $host_deadpool_members) == true) {
                echo "   Already in deadpool -> skipping host\n";
                continue;
            }

            if ($testing == false) {

                // Add host to deadpool hostgroup
                $sql = "INSERT INTO " . $db_tables[DB_NAGIOSQL]["lnkHostToHostgroup"] . " SET idSlave='" . escape_sql_param($nagiosql_hostgroup_id, DB_NAGIOSQL) . "', idMaster='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Mark the host as having hostgroups 
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET hostgroups='1' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                $reconfigure_nagios = true;

                // Disable notifications
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET notifications_enabled='0' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Update NagiosQL timestamp for host
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET last_modified='" . strftime("%F %T", $now) . "' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);
            }

            $processed_hosts[] = array(
                "name" => $name,
                "stage" => 1,
            );

        } else if ($stage == 2) { // STAGE 2

            $reconfigure_nagios = true;

            if ($testing == false) {

                // Remove host from deadpool hostgroup
                $sql = "DELETE FROM " . $db_tables[DB_NAGIOSQL]["lnkHostToHostgroup"] . " WHERE idSlave='" . escape_sql_param($nagiosql_hostgroup_id, DB_NAGIOSQL) . "' AND idMaster='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Mark the host as not having hostgroups 
                $sql = "SELECt * FROM " . $db_tables[DB_NAGIOSQL]["lnkHostToHostgroup"] . " WHERE idMaster='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";

                if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
                    $members = $rs->RecordCount();

                    if ($members == 0) {
                        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET hostgroups='0' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                        echo "SQL: $sql\n";
                        exec_sql_query(DB_NAGIOSQL, $sql);
                    }
                }

                // Update NagiosQL timestamp for host
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET last_modified='" . strftime("%F %T", $now) . "' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);
            }

            if ($stage_2_host_action == "delete") {

                // Delete host's services
                echo "Deleting host's services...\n";
                $cmd = "cd " . get_root_dir() . "/scripts && ./ccm_delete_object.php --type service --config=" . escapeshellarg($name) . "";
                echo "COMMAND: $cmd\n";
                if ($testing == false) {
                    exec($cmd, $output, $bool);
                    echo "RET: $bool " . print_r($output, true);
                }

                // Delete host...
                echo "Deleting host...\n";
                $cmd = "cd " . get_root_dir() . "/scripts && ./ccm_delete_object.php --type host --id=" . $nagiosql_host_id . "";
                echo "COMMAND: $cmd\n";

                if ($testing == false) {
                    exec($cmd, $output, $bool);
                    echo "RET: $bool " . print_r($output, true);
                }

                if ($deadpool_remove_rrds != 0) {
                    echo "Deleting hosts performance data files...\n";
                    $host_dir = pnp_get_host_perfdata_dir($name);
                    $cmd = "rm -rf $host_dir";
                    echo "COMMAND: $cmd\n";
                    if ($testing == false) {
                        exec($cmd, $output, $bool);
                        echo "RET: $bool " . print_r($output, true);
                    }
                }

            } else if ($stage_2_host_action == "deactivate") {

                // Disable host's services in the db
                $service_ids = nagiosql_host_get_service_ids($nagiosql_host_id);
                if (count($service_ids) > 0) {
                    $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET last_modified='" . strftime("%F %T", $now) . "', active='0' WHERE id=IN(" . implode(',', $service_ids) . ")";
                    echo "SQL: $sql\n";
                    exec_sql_query(DB_NAGIOSQL, $sql);
                }

                // Disable host in db
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET active='0' WHERE id='" . escape_sql_param($nagiosql_host_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

            }

            $processed_hosts[] = array(
                "name" => $name,
                "stage" => 2,
            );
        }
    }

    echo "PROCESSED HOSTS:\n";
    print_r($processed_hosts);

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // PROCESS SERVICES
    //
    //////////////////////////////////////////////////////////////////////////////////////

    echo "PROCESSING SERVICES...\n";
    $processed_services = array();

    // PROCESS CANDIDATES
    foreach ($service_candidates as $s) {

        $stage = grab_array_var($s, "stage");
        $hostname = grab_array_var($s, "hostname");
        $servicename = grab_array_var($s, "servicename");

        echo "Processing service '$hostname' / '$servicename' in stage $stage\n";

        // Get service id in NagiosQL
        $nagiosql_service_id = nagiosql_get_service_id($hostname, $servicename);
        if ($nagiosql_service_id == 0) {
            echo "Error: Could not get ID for service '$hostname' / '$servicename' - skipping\n";
            continue;
        }
        echo "NagiosQL Service ID = $nagiosql_service_id\n";

        // STAGE 1
        if ($stage == 1) {

            // Exclude services already in the deadpool
            if (in_array(array("host" => $hostname,"service" => $servicename), $service_deadpool_members) == true) {
                echo "   Already in deadpool -> skipping service\n";
                continue;
            }

            if ($testing == false) {

                // Add service to deadpool servicegroup
                $sql = "INSERT INTO " . $db_tables[DB_NAGIOSQL]["lnkServiceToServicegroup"] . " SET idSlave='" . escape_sql_param($nagiosql_servicegroup_id, DB_NAGIOSQL) . "', idMaster='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Mark the service as having servicegroups 
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET servicegroups='1', servicegroups_tploptions='2' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                $reconfigure_nagios = true;

                // Disable notifications
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET notifications_enabled='0' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Update NagiosQL timestamp for service
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET last_modified='" . strftime("%F %T", $now) . "' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);
            }

            $processed_services[] = array(
                "hostname" => $hostname,
                "servicename" => $servicename,
                "stage" => 1
            );

        // STAGE 2
        } else if ($stage == 2) {

            // Service must already be in the deadpool or stage 2 is cancelled
            if (in_array(array("host" => $hostname,"service" => $servicename), $service_deadpool_members) == false) {
                echo "   Not in deadpool -> skipping service\n";
                continue;
            }

            $reconfigure_nagios = true;

            if ($testing == false) {

                // Remove service from deadpool servicegroup
                $sql = "DELETE FROM " . $db_tables[DB_NAGIOSQL]["lnkServiceToServicegroup"] . " WHERE idSlave='" . escape_sql_param($nagiosql_servicegroup_id, DB_NAGIOSQL) . "' AND idMaster='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

                // Mark the service as not having servicegroups 
                $sql = "SELECt * FROM " . $db_tables[DB_NAGIOSQL]["lnkServiceToServicegroup"] . " WHERE idMaster='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
                    $members = $rs->RecordCount();
                    if ($members == 0) {
                        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET servicegroups='0' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                        echo "SQL: $sql\n";
                        exec_sql_query(DB_NAGIOSQL, $sql);
                    }
                }
            }

            // Update NagiosQL timestamp for service
            $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET last_modified='" . strftime("%F %T", $now) . "' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
            echo "SQL: $sql\n";
            exec_sql_query(DB_NAGIOSQL, $sql);

            if ($stage_2_host_action == "delete") {

                // Delete services
                echo "Deleting service...\n";
                $cmd = "cd " . get_root_dir() . "/scripts && ./ccm_delete_object.php --type service --id=" . $nagiosql_service_id . "";
                echo "COMMAND: $cmd\n";
                if ($testing == false) {
                    exec($cmd);
                }

                if ($deadpool_remove_rrds != 0) {
                    $host_dir = pnp_get_host_perfdata_dir($hostname);
                    $service_rrd = $host_dir . "/" . pnp_convert_object_name($servicename) . ".rrd";
                    $service_xml = $host_dir . "/" . pnp_convert_object_name($servicename) . ".xml";
                    $location = pnp_get_perfdata_file($hostname, $servicename);
                    if (file_exists($location)) {
                        echo "Deleting service performance data file...\n";
                        $cmd = "rm -f $service_rrd $service_xml";
                        echo "COMMAND: $cmd\n";
                        if ($testing == false) {
                            exec($cmd, $output, $bool);
                            echo "RET: $bool " . print_r($output, true);
                        }
                    }
                }

            } else if ($stage_2_host_action == "deactivate") {

                // Set the service as deactivated
                $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET active='0' WHERE id='" . escape_sql_param($nagiosql_service_id, DB_NAGIOSQL) . "'";
                echo "SQL: $sql\n";
                exec_sql_query(DB_NAGIOSQL, $sql);

            }

            $processed_services[] = array(
                "hostname" => $hostname,
                "servicename" => $servicename,
                "stage" => 2
            );
        }
    }

    // Reconfigure nagios if new objects were created
    if ($reconfigure_nagios == true) {
        echo "Reconfiguring Nagios Core...\n";
        reconfigure_nagioscore();
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //
    // FINISH UP
    //
    //////////////////////////////////////////////////////////////////////////////////////

    echo "PROCESSED HOSTS:\n";
    print_r($processed_hosts);

    echo "PROCESSED SERVICES:\n";
    print_r($processed_services);

    // Send an email to selected recipients
    send_deadpool_email($processed_hosts, $processed_services);

    echo "Done.\n";

    update_sysstat();
}


/**
 * Sends a deadpool email message to the recipients set in the deadpool management page.
 *
 * @param   array   $hosts      Hosts that were moved
 * @param   array   $services   Services that were moved
 */
function send_deadpool_email($hosts, $services)
{
    // Only send an email if something happened
    if (count($hosts) == 0 && count($services) == 0) {
        return;
    }

    $baseurl = get_external_url();

    // Do not send email if no recipients are specified
    $recipients = get_option("deadpool_notice_recipients", "");
    if (empty($recipients)) {
        return;
    }

    // Get host/service actions (delete, deactivate)
    $stage_2_host_action = get_option('stage_2_host_action', 'delete');
    $stage_2_service_action = get_option('stage_2_service_action', 'delete');

    $subject = _("Nagios Deadpool Report");
    $fullbody = "";
    $stage1_host_body = "";

    foreach ($hosts as $h) {
        $name = grab_array_var($h, "name");
        $stage = grab_array_var($h, "stage");

        if ($stage == 1) {
            $stage1_host_body .= $name . "\n";
        }
    }

    if ($stage_2_host_action == 'delete') {
        $host_action = _('deleted');
    } else {
        $host_action = _('deactivated');
    }

    if ($stage_2_service_action == 'delete') {
        $service_action = _('deleted');
    } else {
        $service_action = _('deactivated');
    }

    if ($stage1_host_body != "") {
        $fullbody .= "\n";
        $fullbody .= _("Stage 1 Hosts")."\n";
        $fullbody .= "===\n";
        $fullbody .= sprintf(_("The following hosts were moved to the host deadpool because they have remained in a problem state longer than the stage 1 deadpool threshold.  If the hosts do not recover before the stage 2 threshold, they will automatically be %s from the monitoring configuration."), $host_action) . "\n\n";
        $fullbody .= $stage1_host_body;
    }

    $stage1_service_body = "";
    foreach ($services as $s) {
        $hostname = grab_array_var($s, "hostname");
        $servicename = grab_array_var($s, "servicename");
        $stage = grab_array_var($s, "stage");
        if ($stage == 1)
            $stage1_service_body .= $hostname . " / " . $servicename . "\n";
    }

    if ($stage1_service_body != "") {
        $fullbody .= "\n";
        $fullbody .= _("Stage 1 Services")."\n";
        $fullbody .= "===\n";
        $fullbody .= sprintf(_("The following services were moved to the service deadpool because they have remained in a problem state longer than the stage 1 deadpool threshold.  If the services do not recover before the stage 2 threshold, they will automatically be %s from the monitoring configuration."), $service_action) . "\n\n";
        $fullbody .= $stage1_service_body;
    }

    $stage2_host_body = "";
    reset($hosts);
    foreach ($hosts as $h) {
        $name = grab_array_var($h, "name");
        $stage = grab_array_var($h, "stage");
        if ($stage == 2)
            $stage2_host_body .= $name . "\n";
    }

    if ($stage2_host_body != "") {
        $fullbody .= "\n";
        $fullbody .= sprintf(_("%s Hosts"), ucfirst($host_action))."\n";
        $fullbody .= "===\n";
        $fullbody .= sprintf(_("The following hosts were %s from the monitoring configuration because they remained in a problem state longer than the stage 2 deadpool threshold."), $host_action) . "\n\n";
        $fullbody .= $stage2_host_body;
    }

    $stage2_service_body="";
    reset($services);
    foreach ($services as $s) {
        $hostname = grab_array_var($s, "hostname");
        $servicename = grab_array_var($s, "servicename");
        $stage = grab_array_var($s, "stage");
        if ($stage == 2)
            $stage2_service_body .= $hostname . " / " . $servicename . "\n";
    }

    if ($stage2_service_body!="") {
        $fullbody .= "\n";
        $fullbody .= sprintf(_("%s Services"), ucfirst($service_action))."\n";
        $fullbody .= "===\n";
        $fullbody .= sprintf(_("The following services were %s from the monitoring configuration because they remained in a problem state longer than the stage 2 deadpool threshold."), $service_action) . "\n\n";
        $fullbody .= $stage2_service_body;
    }

    $fullbody .= "\n";
    $fullbody .= "\n";
    $fullbody .= _("Access Nagios XI at").":\n";
    $fullbody .= $baseurl;
    $fullbody .= "\n\n";

    // Set where email is coming from for PHPmailer log
    $send_mail_referer = "cron/deadpool.php";

    $opts = array(
        "to"            => $recipients,
        "subject"       => $subject,
        "message"       => $fullbody,
    );

    echo "EMAIL:\n";
    print_r($opts);
    echo "\n";

    send_email($opts, $debugmsg, $send_mail_referer);
}


function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array(
        "last_check" => time(),
    );

    $sdata = serialize($arr);
    update_systat_value("deadpool_reaper", $sdata);
}
