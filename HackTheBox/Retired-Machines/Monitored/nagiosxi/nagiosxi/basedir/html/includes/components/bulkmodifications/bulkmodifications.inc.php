<?php
//
// Bulk Modifications Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//
// A wizard that allows wizard-like modifications to configurations.
// Version 2.0.0 of the bulk modifications tool allows a more step by step
// process that is easier to understand.
//

// Include the helper file
require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$bulkmodifications_component_name = "bulkmodifications";
bulkmodifications_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function bulkmodifications_component_init()
{
    global $bulkmodifications_component_name;
    $versionok = bulkmodifications_component_checkversion();
    $desc = _("This component allows administrators to submit bulk configurations changes for selected hosts and services. ");

    if (!$versionok) {
        $desc = "<b>" . _("Error: This component requires Nagios XI 2012R1.0 or later.") . "</b>";
    }

    // All components require a few arguments to be initialized correctly.
    $args = array(
        COMPONENT_NAME => $bulkmodifications_component_name,
        COMPONENT_VERSION => '2.3.1',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => _("Bulk Modifications Tool"),
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    // Register this component with XI
    register_component($bulkmodifications_component_name, $args);

    // Register the addmenu function
    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'bulkmodifications_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function bulkmodifications_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }

    // Requires greater than 2011R3.1
    if (get_product_release() < 215) {
        return false;
    }

    return true;
}


function bulkmodifications_component_addmenu($arg = null)
{
    global $bulkmodifications_component_name;
    global $menus;

    // Retrieve the URL for this component
    $urlbase = get_component_url_base($bulkmodifications_component_name);

    // Add to the new CCM
    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Bulk Modifications Tool"),
        "id" => "menu-ccm-bulkmodifications",
        "order" => 802.7,
        "opts" => array(
            "href" => $urlbase . "/index.php",
            "icon" => "fa-th-large"
        )
    ));
}


// =================================================
//  Bulk Mod Processing Functions
// =================================================


// Change template for hosts
function bmt_set_host_templates($hosttemplates, $hosts, &$msg)
{
    $errors = 0;

    foreach ($hosts as $host) {
        // Remove all relationships
        $sql = "DELETE FROM tbl_lnkHostToHosttemplate WHERE idMaster = '".intval($host)."'";
        exec_sql_query(DB_NAGIOSQL, $sql, true);

        $log = sprintf("Removed all host templates from host %s", nagiosql_get_host_name($host));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");

        // Add new relationships
        foreach ($hosttemplates as $i => $ht) {
            $sort = $i+1;
            $sql = "INSERT INTO tbl_lnkHostToHosttemplate (`idMaster`, `idSlave`, `idSort`, `idTable`) VALUES (".intval($host).", ".intval($ht).", ".intval($sort).", 1)";
            exec_sql_query(DB_NAGIOSQL, $sql, true);

            $log = sprintf("Added host template %s to host %s", nagiosql_get_object_name('template', $ht, 'hosttemplate'), nagiosql_get_host_name($host));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    // Update last modified timestamps
    if (count($hosts) > 0) {
        $hoststring = escape_sql_param(implode(',', $hosts), DB_NAGIOSQL);
        $sql = "UPDATE tbl_host SET `last_modified` = NOW(), `use_template` = 1 WHERE `id` IN ({$hoststring});";
        exec_sql_query(DB_NAGIOSQL, $sql, true);
    }

    return $errors;
}


// Change templates for services
function bmt_set_service_templates($servicetemplates, $services, &$msg)
{
    $errors = 0;

    foreach ($services as $service) {

        // Remove all relationships
        $sql = "DELETE FROM tbl_lnkServiceToServicetemplate WHERE idMaster = '".intval($service)."'";
        exec_sql_query(DB_NAGIOSQL, $sql, true);

        $log = sprintf("Removed all service templates from service %s", nagiosql_get_service_name($service));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");

        // Add new relationships
        foreach ($servicetemplates as $i => $st) {
            $sort = $i+1;
            $sql = "INSERT INTO tbl_lnkServiceToServicetemplate (`idMaster`, `idSlave`, `idSort`, `idTable`) VALUES (".intval($service).", ".intval($st).", ".intval($sort).", 1)";
            exec_sql_query(DB_NAGIOSQL, $sql, true);

            $log = sprintf("Added service template %s to service %s", nagiosql_get_object_name('template', $st, 'servicetemplate'), nagiosql_get_service_name($service));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }

    }

    // Update last modified timestamps
    if (count($services) > 0) {
        $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
        $sql = "UPDATE tbl_service SET `last_modified` = NOW(), `use_template` = 1 WHERE `id` IN ({$servicestring});";
        exec_sql_query(DB_NAGIOSQL, $sql, true);
    }

    return $errors;
}


// Change config names
function bmt_change_config_names($config_name, $services, &$msg)
{
    $errors = 0;
    if (empty($config_name) || empty($services)) {
        $msg = _("You must enter a config name and at least 1 service.");
        return 1;
    }

    // Get unique host_name array based on services
    $uniq_hosts = array();
    $sql = "SELECT tbl_host.id FROM tbl_service LEFT JOIN tbl_lnkServiceToHost ON tbl_service.id = tbl_lnkServiceToHost.idMaster
                                      LEFT JOIN tbl_host ON tbl_lnkServiceToHost.idSlave = tbl_host.id
                                      WHERE tbl_service.id IN (".escape_sql_param(implode(',', $services), DB_NAGIOSQL).");";
    $res = exec_sql_query(DB_NAGIOSQL, $sql, true);
    foreach ($res as $r) {
        if (!in_array($r['id'], $uniq_hosts)) {
            $uniq_hosts[] = $r['id'];
        }
    }

    // Update log
    foreach ($services as $service) {
        $log = sprintf("Renamed service %s config name to %s", nagiosql_get_service_name($service), $config_name);
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    // Update the last modified for specific hosts we touched
    $hoststring = escape_sql_param(implode(',', $uniq_hosts), DB_NAGIOSQL);
    $sql = "UPDATE tbl_host SET `last_modified` = NOW() WHERE `id` IN ({$hoststring});";
    exec_sql_query(DB_NAGIOSQL, $sql, true);

    // Get a unique list of config_names base on services
    $uniq_cfgs = array();
    $sql = "SELECT config_name FROM tbl_service WHERE `id` IN (".escape_sql_param(implode(',', $services), DB_NAGIOSQL).");";
    $res = exec_sql_query(DB_NAGIOSQL, $sql, true);
    foreach ($res as $r) {
        if (!in_array($r['config_name'], $uniq_cfgs)) {
            $uniq_cfgs[] = "'".$r['config_name']."'";
        }
    }

    // Update the last modified for specific config_names
    $cfgstring = escape_sql_param(implode(',', $uniq_cfgs), DB_NAGIOSQL);
    $sql = "UPDATE tbl_service SET `last_modified` = NOW() WHERE `config_name` IN ({$cfgstring});";
    exec_sql_query(DB_NAGIOSQL, $sql, true);

    // Change config name values in the database
    $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
    $config_name = escape_sql_param($config_name, DB_NAGIOSQL);
    $sql = "UPDATE tbl_service SET `config_name` = '{$config_name}', `last_modified` = NOW() WHERE `id` IN ({$servicestring});";
    exec_sql_query(DB_NAGIOSQL, $sql, true);

    return $errors;
}


// Updates the command and arguments (if necessary)
function bmt_change_command_and_arguments($command, $args, $args_c, $hosts, $services, &$msg)
{
    global $DB;

    $errors = 0;

    // Make sure we only have the args that we checked the box for
    $cmdargs = array();
    foreach ($args as $i => $arg) {
        if (!empty($args_c[$i]) && $args_c[$i] == "on") {
            $cmdargs[$i] = $arg;
        }
    }

    // Loop over the current hosts and services and remake the commands...
    if (!empty($hosts)) {
        $hoststring = escape_sql_param(implode(',', $hosts), DB_NAGIOSQL);
        $hosts = exec_sql_query(DB_NAGIOSQL, "SELECT `id`,`check_command` FROM tbl_host WHERE `id` IN ({$hoststring})", true);
        foreach ($hosts as $host) {

            if ($command == 'blank') {
                $check_command = '';
            } else {

                // Update old command with new options...
                if (!empty($host['check_command'])) {
                    $check_command = $host['check_command'];
                    $cmdarr = explode('!', $check_command);
                }

                // Update arguments that we need to update
                $newargs = array();
                for ($i = 1; $i <= 8; $i++) {
                    if (isset($cmdargs[$i])) {
                        $newargs[$i] = $cmdargs[$i];
                    } else if (isset($cmdarr[$i])) {
                        $newargs[$i] = $cmdarr[$i];
                    }
                }

                // Set command to the old command if we aren't changing it
                if (!empty($host['check_command']) && $command == '') {
                    $command = $cmdarr[0];
                    $log = sprintf("Updated command arguments for host %s", nagiosql_get_host_name($host['id']));
                } else {
                    $log = sprintf("Updated command for host %s to %s", nagiosql_get_host_name($host['id']), nagiosql_get_object_name('command', $command));
                }

                $check_command = escape_sql_param($command.'!'.implode('!', $newargs), DB_NAGIOSQL);

            }

            $query = "UPDATE tbl_host SET `check_command`='".$check_command."', `last_modified` = NOW() WHERE `id` = '".intval($host['id'])."'";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    if (!empty($services)) {
        $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
        $services = exec_sql_query(DB_NAGIOSQL, "SELECT `id`,`check_command` FROM tbl_service WHERE `id` IN ({$servicestring})", true);
        foreach ($services as $service) {

            if ($command == 'blank') {
                $check_command = '';
            } else {

                // Update old command with new options...
                if (!empty($service['check_command'])) {
                    $check_command = $service['check_command'];
                    $cmdarr = explode('!', $check_command);
                }

                // Update arguments that we need to update
                $newargs = array();
                for ($i = 1; $i <= 8; $i++) {
                    if (isset($cmdargs[$i])) {
                        $newargs[$i] = $cmdargs[$i];
                    } else if (isset($cmdarr[$i])) {
                        $newargs[$i] = $cmdarr[$i];
                    }
                }

                // Set command to the old command if we aren't changing it
                if (!empty($service['check_command']) && $command == '') {
                    $command = $cmdarr[0];
                    $log = sprintf("Updated command arguments for service %s", nagiosql_get_service_name($service['id']));
                } else {
                    $log = sprintf("Updated command for service %s to %s", nagiosql_get_service_name($service['id']), nagiosql_get_object_name('command', $command));
                }

                $check_command = escape_sql_param($command.'!'.implode('!', $newargs), DB_NAGIOSQL);

            }

            $query = "UPDATE tbl_service SET `check_command`='".$check_command."', `last_modified` = NOW() WHERE `id` = '".intval($service['id'])."'";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    return $errors;
}


/**
 * Updates a single config setting for a list of hosts and services.
 *
 * @param string $config   config table option to modify
 * @param string $value    the field value
 * @param mixed  $hosts    array of host ID's
 * @param mixed  $services array of service ID's
 * @return int $errors count of any sql errors
 */
function bmt_change_single_config_option($config, $field_value, $oosn_value, $opts_values, $timeperiod, $hosts, $services, &$msg)
{
    $type = 'field';
    $value = $field_value;
    $errors = 0;

    if ($value == '') {
        $value = 'NULL';
    } else {
        $value = escape_sql_param($field_value, DB_NAGIOSQL, true);
    }

    $oosn_configs = array('active_checks_enabled', 'passive_checks_enabled', 'check_freshness',
        'obsess_over_host', 'event_handler_enabled', 'flap_detection_enabled',
        'retain_status_information', 'retain_nonstatus_information',
        'process_perf_data', 'notifications_enabled', 'is_volatile');
    $opts_configs = array('stalking_options', 'flap_detection_options', 'initial_state', 'notification_options');
    $dd_configs = array('check_period', 'notification_period');

    if (in_array($config, $oosn_configs)) {
        $value = escape_sql_param($oosn_value, DB_NAGIOSQL, true);
    } else if (in_array($config, $opts_configs)) {
        $type = 'opt';
    } else if (in_array($config, $dd_configs)) {

        // if they want to blank it out to let a template take over
        if ($timeperiod != "") {

            // Grab the timeperiod id since that's what we need to put into the SQL
            $res = exec_sql_query(DB_NAGIOSQL, "SELECT id from tbl_timeperiod WHERE timeperiod_name = '".escape_sql_param($timeperiod, DB_NAGIOSQL)."' LIMIT 1", true);
            foreach ($res as $r) {
                $timeperiod_id = $r['id'];
            }
            $value = $timeperiod_id;
        }
    }

    // Update hosts
    if (!empty($hosts)) {
        if ($type == 'opt') { $value = escape_sql_param(implode(',', $opts_values['host']), DB_NAGIOSQL, true); }
        $hoststring = escape_sql_param(implode(',', $hosts), DB_NAGIOSQL);
        $query = "UPDATE tbl_host SET `$config`=$value, `last_modified` = NOW() WHERE `id` IN ({$hoststring})";
        exec_sql_query(DB_NAGIOSQL, $query, true);
        foreach ($hosts as $host) {
            $log = sprintf("Updated config option %s to %s on host %s", $config, $value, nagiosql_get_host_name($host));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    // Update services
    if (!empty($services)) {
        if ($type == 'opt') { $value = escape_sql_param(implode(',', $opts_values['service']), DB_NAGIOSQL, true); }
        $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
        $query = "UPDATE tbl_service SET `$config`=$value, `last_modified` = NOW() WHERE `id` IN ({$servicestring})";
        exec_sql_query(DB_NAGIOSQL, $query, true);
        foreach ($services as $service) {
            $log = sprintf("Updated config option %s to %s on service %s", $config, $value, nagiosql_get_service_name($service));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }



    return $errors;
}


/**
 * Adds relationships for a contact to selected hosts and services.
 *
 * @param int   $contact  contact ID
 * @param mixed $hosts    array of host ID's
 * @param mixed $services array of service ID's
 * @return int $errors count of any sql errors
 */
function bmt_add_contact_relationships($contacts, $hosts, $services, $hostopt, $serviceopt, &$msg)
{
    $errors = 0;

    foreach ($contacts as $contact) {

        // Update hosts
        if (!empty($hosts)) {
            $hoststring = escape_sql_param(implode(',', $hosts), DB_NAGIOSQL);
            $query = "UPDATE tbl_host SET `contacts`='1', `last_modified` = NOW(), `contacts_tploptions` = '".intval($hostopt)."' WHERE `id` IN ({$hoststring});";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            // Add host relations
            $contact = escape_sql_param($contact, DB_NAGIOSQL);
            foreach ($hosts as $host) {
                $host = escape_sql_param($host, DB_NAGIOSQL);
                $query = "INSERT INTO tbl_lnkHostToContact SET `idMaster`='{$host}',`idSlave`='{$contact}' ON DUPLICATE KEY UPDATE `idMaster`='{$host}',`idSlave`='{$contact}';";
                exec_sql_query(DB_NAGIOSQL, $query, true);

                $log = sprintf("Added contact %s to host %s", nagiosql_get_object_name('contact', $contact), nagiosql_get_host_name($host));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }

        // Update services
        if (!empty($services)) {
            $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
            $query = "UPDATE tbl_service SET `contacts`='1', `last_modified` = NOW(), `contacts_tploptions` = '".intval($serviceopt)."' WHERE `id` IN ({$servicestring})";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            // Add service relations
            $contact = escape_sql_param($contact, DB_NAGIOSQL);
            foreach ($services as $service) {
                $service = escape_sql_param($service, DB_NAGIOSQL);
                $query = "INSERT INTO tbl_lnkServiceToContact SET `idMaster`='{$service}',`idSlave`='{$contact}' ON DUPLICATE KEY UPDATE `idMaster`='{$service}',`idSlave`='{$contact}';";
                exec_sql_query(DB_NAGIOSQL, $query, true);

                $log = sprintf("Added contact %s to service %s", nagiosql_get_object_name('contact', $contact), nagiosql_get_service_name($service));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    return $errors;
}


/**
 * Removes relationships for a contact to selected hosts and services.
 *
 * @param int   $contact  contact ID
 * @param mixed $hosts    array of host ID's
 * @param mixed $services array of service ID's
 * @return int $errors count of any sql errors
 */
function bmt_remove_contact_relationships($contact, $hosts, $services, &$msg)
{
    $errors = 0;
    $contact = intval($contact);

    // Add host relations
    if (!empty($hosts)) {
        foreach ($hosts as $host) {
            $host = intval($host);
            $query = "DELETE FROM tbl_lnkHostToContact WHERE `idMaster`='$host' AND`idSlave`='{$contact}';";
            exec_sql_query(DB_NAGIOSQL, $query, true);
            $updateQuery = "UPDATE tbl_host SET `last_modified` = NOW() WHERE `id` = '$host';";
            exec_sql_query(DB_NAGIOSQL, $updateQuery, true);

            $log = sprintf("Removed contact %s from host %s", nagiosql_get_object_name('contact', $contact), nagiosql_get_host_name($host));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    // Add service relations
    if (!empty($services)) {
        foreach ($services as $service) {
            $service = intval($service);
            $query = "DELETE FROM tbl_lnkServiceToContact WHERE `idMaster`='$service' AND`idSlave`='{$contact}';";
            exec_sql_query(DB_NAGIOSQL, $query, true);
            $updateQuery = "UPDATE tbl_service SET `last_modified` = NOW() WHERE `id` = '$service';";
            exec_sql_query(DB_NAGIOSQL, $updateQuery, true);

            $log = sprintf("Removed contact %s from service %s", nagiosql_get_object_name('contact', $contact), nagiosql_get_service_name($service));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    return $errors;
}


/**
 * Adds relationships for a contactgroup to selected hosts and services.
 *
 * @param int   $contactgroup contactgroup ID
 * @param mixed $hosts        array of host ID's
 * @param mixed $services     array of service ID's
 * @return int $errors count of any sql errors
 */
function bmt_add_contactgroup_relationships($contactgroups, $hosts, $services, $hostopt, $serviceopt, &$msg)
{
    $errors = 0;

    foreach ($contactgroups as $contactgroup) {
        $contactgroup = intval($contactgroup);

        // Add to hosts
        if (!empty($hosts)) {
            $hoststring = escape_sql_param(implode(',', $hosts), DB_NAGIOSQL);
            $query = "UPDATE tbl_host SET `contact_groups`='1', `last_modified` = NOW(), `contact_groups_tploptions` = '".intval($hostopt)."' WHERE `id` IN ({$hoststring})";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            // Add host relations
            foreach ($hosts as $host) {
                $host = intval($host);
                $query = "INSERT INTO tbl_lnkHostToContactgroup SET `idMaster`='{$host}',`idSlave`='{$contactgroup}'
                ON DUPLICATE KEY UPDATE `idMaster`='{$host}',`idSlave`='{$contactgroup}'";
                exec_sql_query(DB_NAGIOSQL, $query, true);

                $log = sprintf("Added contact group %s to host %s", nagiosql_get_object_name('contactgroup', $contactgroup), nagiosql_get_host_name($host));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }

        // Add to services
        if (!empty($services)) {
            $servicestring = escape_sql_param(implode(',', $services), DB_NAGIOSQL);
            $query = "UPDATE tbl_service SET `contact_groups`='1', `last_modified` = NOW(), `contact_groups_tploptions` = '".intval($serviceopt)."' WHERE `id` IN ({$servicestring})";
            exec_sql_query(DB_NAGIOSQL, $query, true);

            // Add service relations
            foreach ($services as $service) {
                $service = intval($service);
                $query = "INSERT INTO tbl_lnkServiceToContactgroup SET `idMaster`='$service',`idSlave`='{$contactgroup}'
                ON DUPLICATE KEY UPDATE `idMaster`='$service',`idSlave`='{$contactgroup}';";
                exec_sql_query(DB_NAGIOSQL, $query, true);

                $log = sprintf("Added contact group %s to service %s", nagiosql_get_object_name('contactgroup', $contactgroup), nagiosql_get_service_name($service));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    return $errors;
}


/**
 * Removes relationships for a contact to selected hosts and services.
 *
 * @param int   $contactgroup contactgroup ID
 * @param mixed $hosts        array of host ID's
 * @param mixed $services     array of service ID's
 * @return int $errors count of any sql errors
 */
function bmt_remove_contactgroup_relationships($contactgroup, $hosts, $services, &$msg)
{
    $errors = 0;
    $contactgroup = intval($contactgroup);

    // Add host relations
    if (!empty($hosts)) {
        foreach ($hosts as $host) {
            $host = intval($host);
            $query = "DELETE FROM tbl_lnkHostToContactgroup WHERE `idMaster`='$host' AND`idSlave`='{$contactgroup}';";
            exec_sql_query(DB_NAGIOSQL, $query, true);
            $updateQuery = "UPDATE tbl_host SET `last_modified` = NOW() WHERE `id` = '$host';";
            exec_sql_query(DB_NAGIOSQL, $updateQuery, true);

            $log = sprintf("Removed contact group %s from host %s", nagiosql_get_object_name('contactgroup', $contactgroup), nagiosql_get_host_name($host));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    // Add service relations
    if (!empty($services)) {
        foreach ($services as $service) {
            $service = intval($service);
            $query = "DELETE FROM tbl_lnkServiceToContactgroup WHERE `idMaster`='$service' AND`idSlave`='{$contactgroup}';";
            exec_sql_query(DB_NAGIOSQL, $query, true);
            $updateQuery = "UPDATE tbl_service SET `last_modified` = NOW() WHERE `id` = '$service';";
            exec_sql_query(DB_NAGIOSQL, $updateQuery, true);

            $log = sprintf("Removed contact group %s from service %s", nagiosql_get_object_name('contactgroup', $contactgroup), nagiosql_get_service_name($service));
            send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
        }
    }

    return $errors;
}


/**
 * Adds a hostgroup to all the selected hosts.
 *
 * @param int   $hostgroup_tbl_id Host Group ID
 * @param mixed $hosts            Array of host ID's
 * @param str   $msg              String that will display the success message
 * @return int $errors Count of any sql errors
 */
function bmt_add_hostgroups_to_hosts($hostgroups, $hosts, &$msg)
{
    $errors = 0;

    foreach ($hostgroups as $hg_id) {
        if (!empty($hosts)) {
            foreach ($hosts as $host_id) {
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tbl_host` SET `hostgroups` = 1, `last_modified` = NOW()  WHERE `id` = " . intval($host_id), false);
                exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkHostToHostgroup` (`idMaster`, `idSlave`) VALUES (" . intval($host_id) . ", " . intval($hg_id) . ")", false);

                $log = sprintf("Added host group %s to host %s", nagiosql_get_object_name('hostgroup', $hg_id), nagiosql_get_host_name($host_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    return $errors;
}


// Remove host group from hosts
function bmt_remove_hostgroup_from_hosts($hostgroup, $hosts, &$msg)
{
    $errors = 0;

    foreach ($hosts as $host_id) {
        // Remove the hostgroup relationship
        exec_sql_query(DB_NAGIOSQL, "DELETE FROM `tbl_lnkHostToHostgroup` WHERE `idMaster` = '".intval($host_id)."' AND `idSlave` = '".intval($hostgroup)."';", false);
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_host SET `last_modified` = NOW() WHERE `id` = '".intval($host_id)."';", true);

        $log = sprintf("Remove host group %s from host %s", nagiosql_get_object_name('hostgroup', $hostgroup), nagiosql_get_host_name($host_id));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    return $errors;
}

/**
 * Adds a servicegroup to all the selected services.
 *
 * @param int   $servicegroup_tbl_id Service Group ID
 * @param mixed $hosts            Array of service ID's
 * @param str   $msg              String that will display the success message
 * @return int $errors Count of any sql errors
 */
function bmt_add_servicegroups_to_services($servicegroups, $services, &$msg)
{
    $errors = 0;

    foreach ($servicegroups as $sg_id) {
        if (!empty($services)) {
            foreach ($services as $service_id) {
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tbl_service` SET `servicegroups` = 1, `last_modified` = NOW()  WHERE `id` = " . intval($service_id), false);
                exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToServicegroup` (`idMaster`, `idSlave`) VALUES (" . intval($service_id) . ", " . intval($sg_id) . ")", false);
            }
        }
    }

    return $errors;
}


// Remove servicegroup from hosts
function bmt_remove_servicegroup_from_services($servicegroup, $services, &$msg)
{
    $errors = 0;

    foreach ($services as $service_id) {
        // Remove the servicegroup relationship
        exec_sql_query(DB_NAGIOSQL, "DELETE FROM `tbl_lnkServiceToServicegroup` WHERE `idMaster` = '".intval($service_id)."' AND `idSlave` = '".intval($servicegroup)."';", false);
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_service SET `last_modified` = NOW() WHERE `id` = '".intval($service_id)."';", true);
    }

    return $errors;
}


/**
 * Adds a parent host to all the selected hosts.
 *
 * @param int   $parenthost_tbl_id Parent Host ID
 * @param mixed $hosts             Array of host ID's
 * @param str   $msg               String that will display the success message
 * @return int $errors Count of any sql errors
 */
function bmt_add_parent_hosts_to_hosts($parenthosts, $hosts, &$msg)
{
    $errors = 0;

    foreach ($parenthosts as $parent_id) {

        if (!empty($hosts)) {
            $new_vals = array();
            foreach ($hosts as $host_id) {
                $new_vals[] = " (" . intval($host_id) . ", " . intval($parent_id) . ")";
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tbl_host` SET `parents` = 1, `last_modified` = NOW() WHERE `id` = " . intval($host_id));

                $log = sprintf("Added parent host %s to host %s", nagiosql_get_host_name($parent_id), nagiosql_get_host_name($host_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }

            # Do actual insert
            if (count($new_vals > 1)) {
                $values = implode(", ", $new_vals);
            } else {
                $values = $new_vals[0];
            }

            exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkHostToHost` (`idMaster`, `idSlave`) VALUES " . escape_sql_param($values, DB_NAGIOSQL));
        }

    }

    return $errors;
}


// Remove parent host from hosts
function bmt_remove_parent_host_from_hosts($parenthost, $hosts, &$msg)
{
    $errors = 0;
    $parenthost = intval($parenthost);

    foreach ($hosts as $host_id) {
        // Remove the parent host relationship
        exec_sql_query(DB_NAGIOSQL, "DELETE FROM `tbl_lnkHostToHost` WHERE `idMaster` = '".intval($host_id)."' AND `idSlave` = '$parenthost';", false);
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_host SET `last_modified` = NOW() WHERE `id` = '".intval($host_id)."';", true);

        $log = sprintf("Removed parent host %s from host %s", nagiosql_get_host_name($parenthost), nagiosql_get_host_name($host_id));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    return $errors;
}

function bmt_add_free_variables($hosts, $services, $contacts, $free_variable_names, $free_variable_values)
{
    $errors = 0;

    if (!empty($hosts)) {
        foreach ($hosts as $host_id) {

            $host_id = intval($host_id);
            // Get free variables sharing our new names, delete those entries
            $free_variable_ids = exec_sql_query(DB_NAGIOSQL, "SELECT `tbl_variabledefinition`.`id` FROM tbl_variabledefinition INNER JOIN tbl_lnkHostToVariabledefinition ON `id` = `idSlave` WHERE `idMaster` = '$host_id' AND `name` in ('" . escape_sql_param(implode("','", $free_variable_names), DB_NAGIOSQL) . "');");
            $free_variable_ids = $free_variable_ids->GetRows();

            if (!empty($free_variable_ids)) {
                foreach ($free_variable_ids as &$id) {
                    $id = $id['id'];
                }
                $free_variable_ids = implode("','", $free_variable_ids);

                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_variabledefinition WHERE id in ('" . $free_variable_ids . "')");
                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_lnkHostToVariabledefinition WHERE idSlave in ('" . $free_variable_ids . "')");
            }

            // Now add all free variables as though they're new.
            // We could try a multi-insert here but the methods for retrieving last insert ID return different values across databases.
            foreach ($free_variable_names as $ind => $name) {
                $name = escape_sql_param($name, DB_NAGIOSQL);
                $value = escape_sql_param($free_variable_values[$ind], DB_NAGIOSQL);

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_variabledefinition (`name`, `value`, `last_modified`) VALUES ('" . $name . "','" . $value . "',NOW())", true);

                $idSlave = get_sql_insert_id(DB_NAGIOSQL);

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_lnkHostToVariabledefinition (`idMaster`, `idSlave`) VALUES ('" . $host_id . "','" . $idSlave . "')", true);

                exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_host SET use_variables = '1', last_modified = NOW() WHERE id = '" . $host_id . "'");

                $log = sprintf("Added free variable %s to host %s", $name, nagiosql_get_host_name($host_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    if (!empty($services)) {
        foreach ($services as $service_id) {

            $service_id = intval($service_id);
            // Get free variables sharing our new names, delete those entries
            $free_variable_ids = exec_sql_query(DB_NAGIOSQL, "SELECT `tbl_variabledefinition`.`id` FROM tbl_variabledefinition INNER JOIN tbl_lnkServiceToVariabledefinition ON `id` = `idSlave` WHERE `idMaster` = '$service_id' AND `name` in ('" . escape_sql_param(implode("','", $free_variable_names), DB_NAGIOSQL) . "');");
            $free_variable_ids = $free_variable_ids->GetRows();

            if (!empty($free_variable_ids)) {
                foreach ($free_variable_ids as &$id) {
                    $id = $id['id'];
                }
                $free_variable_ids = implode("','", $free_variable_ids);

                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_variabledefinition WHERE id in ('" . $free_variable_ids . "')");
                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_lnkServiceToVariabledefinition WHERE idSlave in ('" . $free_variable_ids . "')");
            }

            // Now add all free variables as though they're new.
            // We could try a multi-insert here but the methods for retrieving last insert ID return different values across databases.
            foreach ($free_variable_names as $ind => $name) {

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_variabledefinition (`name`, `value`, `last_modified`) VALUES ('" . escape_sql_param($name, DB_NAGIOSQL) . "','" . escape_sql_param($free_variable_values[$ind], DB_NAGIOSQL) . "',NOW())", true);

                $idSlave = get_sql_insert_id(DB_NAGIOSQL);

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_lnkServiceToVariabledefinition (`idMaster`, `idSlave`) VALUES ('" . $service_id . "','" . $idSlave . "')");

                exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_service SET use_variables = '1', last_modified = NOW() WHERE id = '" . $service_id . "'");

                $log = sprintf("Added free variable %s to service %s", $name, nagiosql_get_service_name($service_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    if (!empty($contacts)) {
        foreach ($contacts as $contact_id) {

            $contact_id = intval($contact_id);
            // Get free variables sharing our new names, delete those entries
            $free_variable_ids = exec_sql_query(DB_NAGIOSQL, "SELECT `tbl_variabledefinition`.`id` FROM tbl_variabledefinition INNER JOIN tbl_lnkContactToVariabledefinition ON `id` = `idSlave` WHERE `idMaster` = '$host_id' AND `name` in ('" . escape_sql_param(implode("','", $free_variable_names), DB_NAGIOSQL) . "');");
            $free_variable_ids = $free_variable_ids->GetRows();

            if (!empty($free_variable_ids)) {
                foreach ($free_variable_ids as &$id) {
                    $id = $id['id'];
                }
                $free_variable_ids = implode("','", $free_variable_ids);

                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_variabledefinition WHERE id in ('" . $free_variable_ids . "')");
                exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_lnkContactToVariabledefinition WHERE idSlave in ('" . $free_variable_ids . "')");
            }

            // Now add all free variables as though they're new.
            // We could try a multi-insert here but the methods for retrieving last insert ID return different values across databases.
            foreach ($free_variable_names as $ind => $name) {

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_variabledefinition (`name`, `value`, `last_modified`) VALUES ('" . escape_sql_param($name, DB_NAGIOSQL) . "','" . escape_sql_param($free_variable_values[$ind], DB_NAGIOSQL) . "',NOW())", true);

                $idSlave = get_sql_insert_id(DB_NAGIOSQL);

                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_lnkContactToVariabledefinition (`idMaster`, `idSlave`) VALUES ('" . $contact_id . "','" . $idSlave . "')");

                exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_contact SET use_variables = '1', last_modified = NOW() WHERE id = '" . $contact_id . "'");

                $log = sprintf("Added free variable %s to contact %s", $name, nagiosql_get_object_name('contact', $contact_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    return $errors;
}

function bmt_remove_free_variables($hosts, $services, $contacts, $variable_id_list, &$msg)
{
    $errors = 0;

    if (preg_match('/[^0-9,\s]/', $variable_id_list)) {
        $msg = _("The variable ID list must be a comma-delimited list of integers, optionally with spaces.");
        $errors += 1;
        return $errors;
    }

    // Get the name of the free variable
    $free_var_id = intval($variable_id_list);
    if (strpos($variable_id_list, ',') !== false) {
        $ids = explode(',', $variable_id_list);
        $free_var_id = intval($id[0]);
    }

    $var_name = '';
    $vars = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM tbl_variabledefinition WHERE id = $free_var_id");
    $vars = $vars->GetArray();
    if (!empty($vars)) {
        $var_name = $vars[0]['name'];
    }

    // Each variable entry will only have one reference to it, so we can delete the entry alongside the link.
    foreach ($hosts as $host_id) {
        $host_id = intval($host_id);
        exec_sql_query(DB_NAGIOSQL, "DELETE link, vbl FROM tbl_lnkHostToVariabledefinition link JOIN tbl_variabledefinition vbl ON link.idSlave = vbl.id WHERE link.idMaster = '$host_id' AND link.idSlave IN ($variable_id_list)");

        // Host has been modified (must be set so it gets written out)
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_host SET last_modified = NOW() WHERE id = '$host_id'");

        $log = sprintf("Removed free variable %s from host %s", $var_name, nagiosql_get_host_name($host_id));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    foreach ($services as $service_id) {
        $service_id = intval($service_id);
        exec_sql_query(DB_NAGIOSQL, "DELETE link, vbl FROM tbl_lnkServiceToVariabledefinition link JOIN tbl_variabledefinition vbl ON link.idSlave = vbl.id WHERE link.idMaster = '$service_id' AND link.idSlave IN ($variable_id_list)");

        // Service has been modified (must be set so it gets written out)
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_service SET last_modified = NOW() WHERE id = '$service_id'");

        $log = sprintf("Removed free variable %s from service %s", $var_name, nagiosql_get_service_name($service_id));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    foreach ($contacts as $contact_id) {
        $contact_id = intval($contact_id);
        exec_sql_query(DB_NAGIOSQL, "DELETE link, vbl FROM tbl_lnkContactToVariabledefinition link JOIN tbl_variabledefinition vbl ON link.idSlave = vbl.id WHERE link.idMaster = '$contact_id' AND link.idSlave IN ($variable_id_list)");

        // Contact has been modified
        exec_sql_query(DB_NAGIOSQL, "UPDATE tbl_contact SET last_modified = NOW() WHERE id = '$contact_id'");

        $log = sprintf("Removed free variable %s from contact %s", $var_name, nagiosql_get_object_name('contact', $contact_id));
        send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
    }

    return $errors;
}

/**
 * Adds a service to all the selected hosts (by cloning not by association only!)
 *
 * DOES NOT CLONE: CONTACTS, GROUPS, TEMPLATES, PARENTS
 *
 * @param int   $service_tbl_id Service ID
 * @param mixed $hosts          Array of host ID's
 * @param str   $msg            String that will display the success message
 * @return int $errors Count of any sql errors
 */
function bmt_add_services_to_hosts($services, $hosts, &$msg)
{
    global $DB;
    global $ccm;
    $errors = 0;

    foreach ($services as $service_id) {
        if (!empty($hosts)) {
            foreach ($hosts as $host_id) {

                $service_id = intval($service_id);
                $host_id = intval($host_id);

                // Get the host name so we can create a new config_name
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT `host_name` FROM `tbl_host` WHERE `id` = " . $host_id);
                foreach ($res as $r) {
                    $host_name = escape_sql_param($r['host_name'], DB_NAGIOSQL);
                }

                if (empty($host_name)) {
                    $host_name = _('Unknown');
                }

                // Create a temp row and add it back in with a new ID... then we can create a relationship
                exec_sql_query(DB_NAGIOSQL, "CREATE TEMPORARY TABLE `tmp_service` SELECT * FROM `tbl_service` WHERE `id` = " . $service_id);
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tmp_service` SET `id` = NULL, `config_name` = '" . $host_name . "'");
                exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_service` SELECT * FROM `tmp_service`");
                $new_service_id = intval(get_sql_insert_id(DB_NAGIOSQL));
                exec_sql_query(DB_NAGIOSQL, "DROP TEMPORARY TABLE IF EXISTS `tmp_service`");

                // Select the new table and get the ID to add to host
                exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToHost` (`idMaster`, `idSlave`) VALUES (" . $new_service_id . ", " . $host_id . ")");

                // Update last_modified so these objects get written out by apply config
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tbl_host` SET `last_modified` = NOW() WHERE `id` = " . $host_id);
                exec_sql_query(DB_NAGIOSQL, "UPDATE `tbl_service` SET `last_modified` = NOW() WHERE `id` = " . $new_service_id);

                // Clone service templates
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToServicetemplate` WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToServicetemplate` (`idMaster`, `idSlave`, `idSort`, `idTable`)
                                                 VALUES (" . $new_service_id . ", " . $r['idSlave'] . ", " . $r['idSort'] . ", " . $r['idTable'] . ")");
                }

                // Clone host groups
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToHostgroup` WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToHostgroup` (`idMaster`, `idSlave`)
                                                 VALUES (" . $new_service_id . ", " . $r['idSlave'] . ")");
                }

                // Clone service groups
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToServicegroup` WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToServicegroup` (`idMaster`, `idSlave`)
                                                 VALUES (" . $new_service_id . ", " . $r['idSlave'] . ")");
                }

                // Clone contacts
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToContact` WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToContact` (`idMaster`, `idSlave`)
                                                 VALUES (" . $new_service_id . ", " . $r['idSlave'] . ")");
                }

                // Clone contact groups
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToContactgroup` WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToContactgroup` (`idMaster`, `idSlave`)
                                                 VALUES (" . $new_service_id . ", " . $r['idSlave'] . ")");
                }

                // Clone free variables
                $res = exec_sql_query(DB_NAGIOSQL, "SELECT * FROM `tbl_lnkServiceToVariabledefinition`
                                                    LEFT JOIN `tbl_variabledefinition` ON `tbl_lnkServiceToVariabledefinition`.`idSlave` = `tbl_variabledefinition`.`id`
                                                    WHERE `idMaster` = " . $service_id);
                foreach ($res as $r) {
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_variabledefinition` (`name`, `value`, `last_modified`)
                                                 VALUES ('" . $r['name'] . "', '" . $r['value'] . "', NOW())");
                    $new_id = intval(get_sql_insert_id(DB_NAGIOSQL));
                    exec_sql_query(DB_NAGIOSQL, "INSERT INTO `tbl_lnkServiceToVariabledefinition` (`idMaster`, `idSlave`)
                                                 VALUES (" . $new_service_id . ", " . $new_id . ")");
                }

                $log = sprintf("Added service %s to host %s", nagiosql_get_service_name($service_id, false), nagiosql_get_host_name($host_id));
                send_to_audit_log($log, AUDITLOGTYPE_MODIFY, "Bulk Modifications Tool");
            }
        }
    }

    return $errors;
}


// =================================================
//  Additional Helper Functions
// =================================================


// Get all the data for the overlays and display the HTML
function bmt_display_hidden_overlays($sel_options=false)
{
    // Create necessary $FIELDS arrays (selHosts, selServices, and empty pre_arrays)
    $FIELDS['selServiceOpts'] = array();
    $FIELDS['selHostOpts'] = array();
    $FIELDS['selContactOpts'] = array();
    $FIELDS['selServicegroupOpts'] = array();
    $FIELDS['selHostgroupOpts'] = array();
    $FIELDS['selContactgroupOpts'] = array();
    $FIELDS['pre_hosts'] = array();
    $FIELDS['pre_services'] = array();
    $FIELDS['pre_contacts'] = array();
    $FIELDS['pre_hostgroups'] = array();
    $FIELDS['pre_servicegroups'] = array();
    $FIELDS['pre_contactgroups'] = array();

    $sql = "SELECT `id`,`host_name` FROM tbl_host";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR host_name = 2)";
    }

    $sql .= " ORDER BY `host_name` ASC";

    $hosts = exec_sql_query(DB_NAGIOSQL, $sql, true);
    foreach ($hosts as $h) {
        $FIELDS['selHostOpts'][] = $h;
    }

    $sql = "SELECT `id`,`config_name`,`service_description` FROM tbl_service";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR host_name = 2)";
    }

    $sql .= " ORDER BY `config_name`,`service_description`";

    $services = exec_sql_query(DB_NAGIOSQL, $sql, true);
    foreach ($services as $s) {
        $FIELDS['selServiceOpts'][] = array('id' => $s['id'], 'service_description' => $s['config_name'] . ' - ' . $s['service_description']);
    }

    $sql = "SELECT `id`,`contact_name` FROM tbl_contact";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_CONTACT." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_CONTACT." AND user_id = ".$_SESSION['user_id']."))";
    }

    $sql .= " ORDER BY `contact_name` ASC";

    $contacts = exec_sql_query(DB_NAGIOSQL, $sql, true);
    foreach ($contacts as $c) {
        $FIELDS['selContactOpts'][] = $c;
    }

    $hostgroups = exec_sql_query(DB_NAGIOSQL, "SELECT `hostgroup_name` as `id`,`hostgroup_name`,`alias` FROM tbl_hostgroup ORDER BY `hostgroup_name`;", true);
    foreach ($hostgroups as $h) {
        $FIELDS['selHostgroupOpts'][] = $h;
    }

    $servicegroups = exec_sql_query(DB_NAGIOSQL, "SELECT `servicegroup_name` as `id`,`servicegroup_name`,`alias` FROM tbl_servicegroup ORDER BY `servicegroup_name`;", true);
    foreach ($servicegroups as $sg) {
        $FIELDS['selServicegroupOpts'][] = $sg;
    }

    $contactgroups = exec_sql_query(DB_NAGIOSQL, "SELECT `contactgroup_name` as `id`, `contactgroup_name`, `alias` FROM tbl_contactgroup ORDER BY `contactgroup_name`;", true);

    foreach ($contactgroups as $cg) {
        $FIELDS['selContactgroupOpts'][] = $cg;
    }

    unset($services);
    unset($hosts);
    unset($hostgroups);
    unset($servicegroups);
    unset($contacts);
    unset($contactgroups);

    echo bmt_build_hidden_overlay($FIELDS, 'host', 'host_name', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'service', 'service_description', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'contact', 'contact_name', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'hostgroup', 'hostgroup_name', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'servicegroup', 'servicegroup_name', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'contactgroup', 'contactgroup_name', false, $sel_options);

}


// Gets the full english-looking title (can be translated)
// p = plural or not (true or false)
function bmt_get_full_title($type, $p = false) {
    if ($p) {
        $titles = array(
            'host' => _('Hosts'),
            'service' => _('Services'),
            'hosttemplate' => _('Host Templates'),
            'servicetemplate' => _('Service Templates'),
            'contact' => _('Contacts'),
            'contactgroup' => _('Contact Groups'),
            'contacttemplate' => _('Contact Templates'),
            'hostgroup' => _('Host Groups'),
            'servicegroup' => _('Service Groups'),
            'command' => _('Commands'),
            'timeperiod' => _('Time Periods'),
            'serviceescalation' => _('Service Escalations'),
            'hostescalation' => _('Host Escalations'),
            'servicedependency' => _('Service Dependencies'),
            'hostdependency' => _('Host Dependencies'),
            'user' => _('Users'),
            'template' => _('Templates'),
            'parent' => _('Parents')
        );
    } else {
        $titles = array(
            'host' => _('Host'),
            'service' => _('Service'),
            'hosttemplate' => _('Host Template'),
            'servicetemplate' => _('Service Template'),
            'contact' => _('Contact'),
            'contactgroup' => _('Contact Group'),
            'contacttemplate' => _('Contact Template'),
            'hostgroup' => _('Host Group'),
            'servicegroup' => _('Service Group'),
            'command' => _('Command'),
            'timeperiod' => _('Time Period'),
            'serviceescalation' => _('Service Escalation'),
            'hostescalation' => _('Host Escalation'),
            'servicedependency' => _('Service Dependency'),
            'hostdependency' => _('Host Dependency'),
            'user' => _('User'),
            'template' => _('Template'),
            'parent' => _('Parent')
        );
    }

    if (isset($titles[$type])) {
        return $titles[$type];
    }
}


/**
 * Builds a hidden overlay div and populates values based on parameters given.
 *
 * @param string $type        nagios object type (host, service, command, etc)
 * @param string $optionValue the DB fieldname for that objects name (host_name, service_description, template_name)
 * @param bool   $BA          boolean switch, are there two-way relationships possible for this object (host->hostgroup, hostgroup->host)
 * @param bool   $tplOpts     boolean switch for showing template options
 * @param string $fieldArray  optional specification for which select list to use
 * @return string returns populated html select lists for the $type object
 */
function bmt_build_hidden_overlay($FIELDS, $type, $optionValue, $BA = false, $tplOpts = false, $fieldArray = '')
{
    $unique = 0;
    $Title = ucfirst($type);
    $Titles = ucfirst($type).'s';
    if ($fieldArray == '') {
        $fieldArray = 'sel'.$Title.'Opts';
    }
    $full_title = bmt_get_full_title($type, true);

    $html = "<!-- ------------------------------------ {$Titles} ($type) --------------------- -->

    <div class='overlay' id='{$type}Box'>

    <div class='overlay-title'>
        <h2>{$full_title}</h2>
        <div class='overlay-close ccm-tt-bind' data-placement='left' title='"._('Close')."' onclick='killOverlay(\"{$type}Box\")'><i class='fa fa-times'></i></div>
        <div class='clear'></div>
    </div>

    <div class='left'>
        <div class='listDiv'>
            <div class='filter'>
                <span class='clear-filter ccm-tt-bind' title='"._('Clear')."'><i class='fa fa-times fa-14'></i></span>
                <input type='text' id='filter{$Titles}' class='form-control fc-fl' style='border-bottom: 0;' placeholder='"._('Filter')."...'>
            </div>
            <select name='sel{$Titles}[]' class='form-control fc-m lists' multiple='multiple' id='sel{$Titles}' ondblclick='transferMembers(\"sel{$Titles}\", \"tbl{$Titles}\", \"{$type}s\")'>
                <!-- option value is tbl ID -->
    ";

    // Special case for hostService array
    if ($type == 'hostservice') {
        foreach ($FIELDS['selHostServiceOpts'] as $key => $opt) {
            $disabled = '';
            $html .= "<option ";
            if (grab_array_var($opt, 'active', 1) == 0) { $disabled = " disabled='disabled' class='disabled'"; }
            if (in_array($key, $FIELDS['pre_hostservices_AB'])) $html .= "selected='selected' ";
            if (in_array($key, $FIELDS['pre_hostservices_BA'])) $disabled .= "disabled='disabled' class='hiddenDependency' ";
            $html .= " id='".$type.$unique++."' title='".$opt['name']."' value='".$key."'".$disabled.">".$opt['name']."</option>";
        }
    } else if ($type == 'parent') {
        foreach ($FIELDS['selParentOpts'] as $key => $opt) {
            // The pre_hosts_BA are child elements of a parent host
            $pre_array = isset($FIELDS['pre_'.$type.'s_AB']) ? $FIELDS['pre_'.$type.'s_AB'] : $FIELDS['pre_'.$type.'s'] ;
            $child = '';
            $html .= '<option ';
            if (in_array($opt['id'], $pre_array)) $html .= "selected='selected' orderid='".array_search($opt['id'], $pre_array)."' ";
            if (in_array($opt['id'], $FIELDS['pre_hosts_BA'])) { $html .= 'disabled="disabled" class="child" '; $child = ' ['._('Child').']'; }
            else if ($opt['active'] == 0) { $html .= ' disabled="disabled" class="disabled"'; }
            $html .= ' id="'.$type.$unique++.'" title="'.$opt[$optionValue].'" value="'.$opt['id'].'">'.$opt[$optionValue].$child.'</option>';
        }
    }
    // If there are two-way database relationships for this object
    else if ($BA == true) {
        foreach ($FIELDS[$fieldArray] as $opt) {
            $html .= '<option ';
            if (in_array($opt['id'], $FIELDS['pre_'.$type.'s_AB'])) $html .= "selected='selected' ";
            if (in_array($opt['id'], $FIELDS['pre_'.$type.'s_BA'])) {
                $html .= "disabled='disabled' class='hiddenDependency' title='"._('Object has a relationship established elsewhere')."' ";
            } else if (grab_array_var($opt,'active',1) == 0 && $opt[$optionValue] != "*") {
                // If the object is not active we should turn it to disabled
                $html .= "disabled='disabled' class='disabled' ";
            }

            $html .= " id='".$type.$unique++."' title='{$opt[$optionValue]}' value='".$opt['id']."'>".$opt[$optionValue].'</option>';
        }
    // Only one-way DB relationships (i.e. service dependency)
    } else {
        $pre_array = isset($FIELDS['pre_'.$type.'s_AB']) ? $FIELDS['pre_'.$type.'s_AB'] : $FIELDS['pre_'.$type.'s'] ;

        foreach ($FIELDS[$fieldArray] as $opt) {

            $html.= '<option ';
            $disabled = "";
            if (grab_array_var($opt,'active',1) == 0) { $disabled = " disabled='disabled' class='disabled'"; }
            if (in_array($opt['id'], $pre_array)) {
                $html .= "selected='selected' orderid='".array_search($opt['id'], $pre_array)."'";
            }

            $name = $opt[$optionValue];
            $html .= " id='".$type.$unique++."' title='{$name}' value='".$opt['id']."'".$disabled.">".$name.'</option>';
        }
    }
    $html .= "  </select>
                <div class='overlay-left-bottom'>
                    <button type='button' class='btn btn-sm btn-primary fl' onclick='transferMembers(\"sel{$Titles}\", \"tbl{$Titles}\", \"{$type}s\")'>"._("Add Selected")." <i class='fa fa-chevron-right'></i></button>
                    <div class='fr ccm-label' style='margin-right: 20px;'>
                        <div><i class='fa fa-fw fa-link'></i> <span class='ccm-tt-bind qtt' title='"._('An example of a relationship that can only be linked in one direction is a child host with a host defined as the parent cannot be set as a child from the host it is already a child of.')."'>"._("Relationship defined elsewhere")."</span></div>
                        <div><i class='fa fa-fw fa-exclamation-circle'></i> "._('Inactive object')."</div>
                    </div>
                    <div class='clear'></div>
                </div>
            <div class='closeOverlay'>
                <button type='button' class='btn btn-sm btn-default' onclick='killOverlay(\"{$type}Box\")'>"._("Close")."</button>
            </div>
        </div>
    </div>
    <!-- end leftBox -->

    <div class='right'>
        <div class='right-container'>
            <table class='table table-no-margin table-small-border-bottom table-x-condensed'>
                <thead>
                    <tr>
                        <th colspan='2'>
                            <span class='thMember'>"._("Assigned")."</span>
                            <a class='fr' title='Remove All' href='javascript:void(0)' onclick=\"removeAll('tbl{$Titles}')\">"._("Remove All")."</a>
                            <div class='clear'></div>
                        </th>
                    </tr>
                </thead>
            </table>
            <div class='assigned-container'>
                <table class='table table-x-condensed table-hover table-assigned' id='tbl{$Titles}'>
                    <tbody>
                        <!-- insert selected items here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- $type radio buttons -->

    </div> <!-- end {$type}box --> ";

    return $html;
}
