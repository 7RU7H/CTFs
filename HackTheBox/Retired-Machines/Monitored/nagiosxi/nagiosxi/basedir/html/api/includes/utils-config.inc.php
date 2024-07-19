<?php
//
// New API utils and classes for Nagios XI 5
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

// Bootstrap CCM functionality
include_once(dirname(__FILE__) . '/../../includes/components/ccm/bootstrap.php');


// =========================================
// Raw Config Import
// =========================================


function api_config_raw_cfg_import()
{
    global $ccm;
    $overwrite = intval(grab_request_var('overwrite', 0));

    $import = file_get_contents("php://input");
    $x = $ccm->import->fileImport($import, $overwrite, true);

    // Catch import error
    if ($x == 1) {
        return array('error' => _('Import failed. Configuration passed may not be formatted properly.'));
    }

    return array('success' => _('Imported configuration data.'));
}


// =========================
// Configuration
// =========================


// Hosts


function api_config_create_cfg_host($args)
{
    global $firsthost;
    global $ccm;

    // Verify required arguments were sent
    $required = array('host_name', 'address', 'max_check_attempts', 'check_period', 'notification_interval', 'notification_period');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (!array_key_exists('contacts', $args) && !array_key_exists('contact_groups', $args)) {
        $missing[] = 'contacts OR contact_groups';
    }
    $required[] = 'contacts';
    $required[] = 'contact_groups';

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_HOST);
    foreach ($required as $r) {
        if (array_key_exists($r, $args)) {
            $obj[$r] = $args[$r];
        }
    }

    // Add any optional components that we need to
    $others = array('alias', 'display_name', 'parents', 'hourly_value', 'hostgroups', 'check_command', 'use', 'initial_state', 'check_interval', 'retry_interval', 'active_checks_enabled', 'passive_checks_enabled', 'obsess_over_host', 'check_freshness', 'freshness_threshold', 'event_handler', 'event_handler_enabled', 'low_flap_threshold', 'high_flap_threshold', 'flap_detection_enabled', 'flap_detection_options', 'process_perf_data', 'retain_status_information', 'retain_nonstatus_information', 'first_notification_delay', 'notification_options', 'notifications_enabled', 'stalking_options', 'notes', 'notes_url', 'action_url', 'icon_image', 'icon_image_alt', 'vrml_image', 'statusmap_image', '2d_coords', '3d_coords', 'name', 'register');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            if ($o == 'check_command') { $args[$o] = stripslashes($args[$o]); }
            $obj[$o] = $args[$o];
        }
    }

    // Add any free variables that we find (they must start with an underscore)
    foreach ($args as $key => $value) {
        if (substr($key, 0, 1) === '_') {
            $obj[$key] = $value;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['host_name'] . ' ' . _('to the system.'));
}


/**
 * Updates a host using the API
 *
 * @param   array   $request    The variables of the request
 * @param   array   $args       The arguments passed in the path
 * @return  array               Success for failure message
 */
function api_config_edit_cfg_host($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the host_name and test the values given in the path first, but also allow using "old_" values as well
    if (!empty($args) && count($args) == 1) {
        $host = $args[0];
    } else {
        $host = grab_array_var($request, 'old_host_name', '');
    }

    if (empty($host)) {
        return array('error' => _('You must enter a host_name to update a host.'));
    }

    // Check if a valid host exists
    $id = nagiosql_get_host_id($host);
    if (!$id) {
        return array('error' => _('Could not update the host specified. Does the host exist?'));
    }

    // We have to do something special if we are changing the host_name
    // before we do the rest of the import
    $host_name = grab_array_var($request, 'host_name', '');
    if (!empty($host_name)) {
        $host = $host_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " SET host_name = '" . escape_sql_param($host_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    // Create object
    $obj = array("type" => OBJECTTYPE_HOST,
                 "host_name" => $host);

    // Update all other options
    $options = array('address', 'max_check_attempts', 'check_period', 'notification_interval', 'notification_period', 'contacts', 'contact_groups', 'alias', 'display_name', 'parents', 'hourly_value', 'hostgroups', 'check_command', 'use', 'initial_state', 'check_interval', 'retry_interval', 'active_checks_enabled', 'passive_checks_enabled', 'obsess_over_host', 'check_freshness', 'freshness_threshold', 'event_handler', 'event_handler_enabled', 'low_flap_threshold', 'high_flap_threshold', 'flap_detection_enabled', 'flap_detection_options', 'process_perf_data', 'retain_status_information', 'retain_nonstatus_information', 'first_notification_delay', 'notification_options', 'notifications_enabled', 'stalking_options', 'notes', 'notes_url', 'action_url', 'icon_image', 'icon_image_alt', 'vrml_image', 'statusmap_image', '2d_coords', '3d_coords', 'name', 'register');

    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            if ($o == 'check_command') { $request[$o] = stripslashes($request[$o]); }
            $obj[$o] = $request[$o];
        }
    }

    // Add any free variables that we find (they must start with an underscore)
    foreach ($request as $key => $value) {
        if (substr($key, 0, 1) === '_') {
            $obj[$key] = $value;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $host . ' ' . _('in the system.'));
}


/**
 * Remove a host or a set of hosts from the CCM configuration
 *
 * @param   array   $request    The variables of the request
 * @return  array               Success for failure message
 */
function api_config_remove_cfg_host($request, $args)
{
    $missing = array();

    // First argument must be the host_name
    if (!empty($args) && count($args) == 1) {
        $hosts = $args[0];
    } else {
        $hosts = grab_array_var($request, 'host_name', array());
    }

    if (empty($hosts)) {
        $missing[] = 'host_name';
    }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    // Check if we are doing a single one or not
    if (!is_array($hosts)) {
        $hosts = array($hosts);
    }

    // Loop through and remove hosts if we can
    $host_errors = array();
    foreach ($hosts as $host) {
        $id = nagiosql_get_host_id($host);
        if ($id <= 0) {
            $host_errors[] = $host;
            continue;
        }
        list($code, $message) = delete_object('host', $id);
        if ($code) {
            $host_errors[] = $host;
        }
    }

    // Check if we actually removed anything
    $removed_hosts = array_diff($hosts, $host_errors);
    if (empty($removed_hosts)) {
        return array('error' => _('Could not remove hosts. Failed hosts may not exist.'),
                     'hosts' => array(),
                     'failed' => $host_errors);
    }

    return array('success' => _('Removed hosts from the system.'),
                 'hosts' => $removed_hosts,
                 'failed' => $host_errors);
}


// Services


function api_config_create_cfg_service($args)
{
    global $firsthost;
    global $ccm;

    // Verify required arguments were sent
    $required = array('host_name', 'service_description', 'max_check_attempts', 'check_interval', 'retry_interval', 'check_period', 'notification_interval', 'notification_period');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (!array_key_exists('contacts', $args) && !array_key_exists('contact_groups', $args)) {
        $missing[] = 'contacts OR contact_groups';
    }
    $required[] = 'contacts';
    $required[] = 'contact_groups';

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_SERVICE);
    foreach ($required as $r) {
        if (array_key_exists($r, $args)) {
            $obj[$r] = $args[$r];
        }
    }

    // Add any optional components that we need to
    $others = array('hostgroup_name', 'display_name', 'parents', 'hourly_value', 'servicegroups', 'is_volatile', 'use', 'initial_state', 'active_checks_enabled', 'passive_checks_enabled', 'obsess_over_service', 'check_freshness', 'check_command', 'freshness_threshold', 'event_handler', 'event_handler_enabled', 'low_flap_threshold', 'high_flap_threshold', 'flap_detection_enabled', 'flap_detection_options', 'process_perf_data', 'retain_status_information', 'retain_nonstatus_information', 'first_notification_delay', 'notification_options', 'notifications_enabled', 'stalking_options', 'notes', 'notes_url', 'action_url', 'icon_image', 'icon_image_alt', 'name', 'register', 'config_name');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            if ($o == 'check_command') { $args[$o] = stripslashes($args[$o]); }
            $obj[$o] = $args[$o];
        }
    }

    // Add any free variables that we find (they must start with an underscore)
    foreach ($args as $key => $value) {
        if (substr($key, 0, 1) === '_') {
            $obj[$key] = $value;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['host_name'] . ' :: ' . $args['service_description'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_service($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;
    $missing = array();

    // First argument must be the config_name and second argument must be service_description
    // test the values given in the path first, but also allow using "old_" values as well
    if (!empty($args) && count($args) == 2) {
        $config = $args[0];
        $service = $args[1];
    } else {
        $config = grab_array_var($request, 'old_config_name', '');
        $service = grab_array_var($request, 'old_service_description', '');
    }

    // Check if a valid service exists
    $id = nagiosql_get_service_id_from_config($config, $service);
    if (!$id) {
        return array('error' => _('Could not update the service specified. Does the service exist?'));
    }

    // We have to do special things if the service name is different
    // because we have to change that, then change everything else
    
    // Check for new config name
    $config_name = grab_array_var($request, 'config_name', '');
    if (!empty($config_name)) {
        $config = $config_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET config_name = '" . escape_sql_param($config_name, DB_NAGIOSQL) . "', last_modified = NOW()
                WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    // Check for new service description
    $service_description = grab_array_var($request, 'service_description', '');
    if (!empty($service_description)) {
        $service = $service_description;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " SET service_description = '" . escape_sql_param($service_description, DB_NAGIOSQL) . "', last_modified = NOW()
                WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    // Create object
    $obj = array("type" => OBJECTTYPE_SERVICE,
                 "config_name" => $config,
                 "service_description" => $service);

    // Optional components that we need to add
    $options = array('max_check_attempts', 'check_interval', 'retry_interval', 'check_period', 'notification_interval', 'notification_period', 'contacts', 'contact_groups', 'host_name', 'hostgroup_name', 'display_name', 'parents', 'hourly_value', 'servicegroups', 'is_volatile', 'use', 'initial_state', 'active_checks_enabled', 'passive_checks_enabled', 'obsess_over_service', 'check_freshness', 'check_command', 'freshness_threshold', 'event_handler', 'event_handler_enabled', 'low_flap_threshold', 'high_flap_threshold', 'flap_detection_enabled', 'flap_detection_options', 'process_perf_data', 'retain_status_information', 'retain_nonstatus_information', 'first_notification_delay', 'notification_options', 'notifications_enabled', 'stalking_options', 'notes', 'notes_url', 'action_url', 'icon_image', 'icon_image_alt', 'name', 'register');

    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            if ($o == 'check_command') { $request[$o] = stripslashes($request[$o]); }
            $obj[$o] = $request[$o];
        }
    }

    // Add any free variables that we find (they must start with an underscore)
    foreach ($request as $key => $value) {
        if (substr($key, 0, 1) === '_') {
            $obj[$key] = $value;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $config . ' :: ' . $service . ' ' . _('in the system.'));
}


/**
 * Remove a service or a set of services from the CCM configuration
 *
 * @param   array   $request    The variables of the request
 * @return  array               Success for failure message
 */
function api_config_remove_cfg_service($request, $args)
{
    $missing = array();

    // First argument must be the host_name and second argument must be service_description
    if (!empty($args) && count($args) == 2) {
        $hosts = $args[0];
        $services = $args[1];
    } else {
        $hosts = grab_array_var($request, 'host_name', array());
        $services = grab_array_var($request, 'service_description', array());
    }

    if (empty($hosts)) { $missing[] = 'host_name'; }
    if (empty($services)) { $missing[] = 'service_description'; }
    
    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($hosts)) {
        $hosts = array($hosts);
    }
    if (!is_array($services)) {
        $services = array($services);
    }

    // Loop through each host, and then remove the services given
    $removed_services = array();
    $service_errors = array();
    foreach ($hosts as $host) {
        foreach ($services as $service) {
            $id = nagiosql_get_service_id($host, $service);
            if ($id <= 0) {
                $service_errors[] = $host . ' - ' . $service;
                continue;
            }
            list($code, $message) = delete_object('service', $id);
            if ($code) {
                $service_errors[] = $host . ' - ' . $service;
                continue;
            }

            // Add removed service
            $removed_services[] = $host . ' - ' . $service;
        }
    }

    // Check if we actually removed anything
    if (empty($removed_services)) {
        return array('error' => _('Could not remove services. Failed services may not exist.'),
                     'services' => array(),
                     'failed' => $service_errors);
    }

    return array('success' => _('Removed services from the system.'),
                 'services' => $removed_services,
                 'failed' => $service_errors);
}


// Hostgroups


function api_config_create_cfg_hostgroup($args)
{
    global $firsthost;
    global $ccm;

    $required = array('hostgroup_name', 'alias');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_HOSTGROUP);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
    }

    $others = array('members', 'hostgroup_members', 'notes', 'notes_url', 'action_url');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            $obj[$o] = $args[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['hostgroup_name'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_hostgroup($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the hostgroup_name
    if (!empty($args) && count($args) == 1) {
        $hostgroup = $args[0];
    } else {
        $hostgroup = grab_array_var($request, 'old_hostgroup_name', '');
    }

    if (empty($hostgroup)) {
        return array('error' => _('You must enter a hostgroup_name to update a host group.'));
    }

    // Check if a valid host group exists
    $id = nagiosccm_get_object_id('hostgroup', $hostgroup);
    if (!$id) {
        return array('error' => _('Could not update the host group specified. Does the host group exist?'));
    }

    // We have to do something special if we are changing the hostgroup_name
    // before we do the rest of the import
    $hostgroup_name = grab_array_var($request, 'hostgroup_name', '');
    if (!empty($hostgroup_name)) {
        $hostgroup = $hostgroup_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["hostgroup"] . " SET hostgroup_name = '" . escape_sql_param($hostgroup_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);

        // Update any hosts this is applied to
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["host"] . " AS h LEFT JOIN " . $db_tables[DB_NAGIOSQL]["lnkHostToHostgroup"] . " AS hgl ON h.id = hgl.idMaster SET h.last_modified = NOW() WHERE hgl.idSlave = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " AS s LEFT JOIN " . $db_tables[DB_NAGIOSQL]["lnkServiceToHostgroup"] . " AS sgl ON s.id = sgl.idMaster SET s.last_modified = NOW() WHERE sgl.idSlave = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_HOSTGROUP,
                 "hostgroup_name" => $hostgroup);

    $options = array('alias', 'members', 'hostgroup_members', 'notes', 'notes_url', 'action_url');
    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            $obj[$o] = $request[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $hostgroup . ' ' . _('in the system.'));
}


function api_config_remove_cfg_hostgroup($request, $args)
{
    $missing = array();
    
    // First argument must be the hostgroup_name
    if (!empty($args) && count($args) == 1) {
        $hostgroups = $args[0];
    } else {
        $hostgroups = grab_array_var($request, 'hostgroup_name', array());
    }

    if (empty($hostgroups)) { $missing[] = 'hostgroup_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($hostgroups)) {
        $hostgroups = array($hostgroups);
    }

    // Loop through and remove the host groups
    $hostgroup_errors = array();
    foreach ($hostgroups as $hostgroup) {
        $id = nagiosccm_get_object_id('hostgroup', $hostgroup);
        if ($id <= 0) {
            $hostgroup_errors[] = $hostgroup;
            continue;
        }
        list($code, $message) = delete_object('hostgroup', $id);
        if ($code) {
            $hostgroup_errors[] = $hostgroup;
        }
    }

    // Check if we actually removed anything
    $removed_hostgroups = array_diff($hostgroups, $hostgroup_errors);
    if (empty($removed_hostgroups)) {
        return array('error' => _('Could not remove host groups. Failed host groups may not exist.'),
                     'hostgroups' => array(),
                     'failed' => $hostgroup_errors);
    }

    return array('success' => _('Removed host groups from the system.'),
                 'hostgroups' => $removed_hostgroups,
                 'failed' => $hostgroup_errors);
}


// Servicegroups


function api_config_create_cfg_servicegroup($args)
{
    global $firsthost;
    global $ccm;

    $required = array('servicegroup_name', 'alias');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_SERVICEGROUP);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
    }

    $others = array('members', 'servicegroup_members', 'notes', 'notes_url', 'action_url');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            $obj[$o] = $args[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['servicegroup_name'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_servicegroup($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the servicegroup_name
    if (!empty($args) && count($args) == 1) {
        $servicegroup = $args[0];
    } else {
        $servicegroup = grab_array_var($request, 'old_servicegroup_name', '');
    }

    if (empty($servicegroup)) {
        return array('error' => _('You must enter a servicegroup_name to update a service group.'));
    }

    // Check if a valid service group exists
    $id = nagiosccm_get_object_id('servicegroup', $servicegroup);
    if (!$id) {
        return array('error' => _('Could not update the service group specified. Does the service group exist?'));
    }

    // We have to do something special if we are changing the servicegroup_name
    // before we do the rest of the import
    $servicegroup_name = grab_array_var($request, 'servicegroup_name', '');
    if (!empty($servicegroup_name)) {
        $servicegroup = $servicegroup_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["servicegroup"] . " SET servicegroup_name = '" . escape_sql_param($servicegroup_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);

        // Update any service this is applied to
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["service"] . " AS s LEFT JOIN " . $db_tables[DB_NAGIOSQL]["lnkServiceToServicegroup"] . " AS sgl ON s.id = sgl.idMaster SET s.last_modified = NOW() WHERE sgl.idSlave = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_SERVICEGROUP,
                 "servicegroup_name" => $servicegroup);

    $options = array('alias', 'members', 'servicegroup_members', 'notes', 'notes_url', 'action_url');
    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            $obj[$o] = $request[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $servicegroup . ' ' . _('in the system.'));
}


function api_config_remove_cfg_servicegroup($request, $args)
{
    $missing = array();

    // First argument must be the servicegroup_name
    if (!empty($args) && count($args) == 1) {
        $servicegroups = $args[0];
    } else {
        $servicegroups = grab_array_var($request, 'servicegroup_name', array());
    }

    if (empty($servicegroups)) { $missing[] = 'servicegroup_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($servicegroups)) {
        $servicegroups = array($servicegroups);
    }

    // Loop through and remove the service groups
    $servicegroup_errors = array();
    foreach ($servicegroups as $servicegroup) {
        $id = nagiosccm_get_object_id('servicegroup', $servicegroup);
        if ($id <= 0) {
            $servicegroup_errors[] = $servicegroup;
            continue;
        }
        list($code, $message) = delete_object('servicegroup', $id);
        if ($code) {
            $servicegroup_errors[] = $servicegroup;
        }
    }

    // Check if we actually removed anything
    $removed_servicegroups = array_diff($servicegroups, $servicegroup_errors);
    if (empty($removed_servicegroups)) {
        return array('error' => _('Could not remove service groups. Failed service groups may not exist.'),
                     'servicegroups' => array(),
                     'failed' => $servicegroup_errors);
    }

    return array('success' => _('Removed service groups from the system.'),
                 'servicegroups' => $removed_servicegroups,
                 'failed' => $servicegroup_errors);
}


// Commands


function api_config_create_cfg_command($args)
{
    global $firsthost;
    global $ccm;

    $required = array('command_name', 'command_line');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_COMMAND);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['command_name'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_command($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the command_name
    if (!empty($args) && count($args) == 1) {
        $command = $args[0];
    } else {
        $command = grab_array_var($request, 'old_command_name', '');
    }

    if (empty($command)) {
        return array('error' => _('You must enter a command_name to update a command.'));
    }

    // Check if a valid command exists
    $id = nagiosccm_get_object_id('command', $command);
    if (!$id) {
        return array('error' => _('Could not update the command specified. Does the command exist?'));
    }

    // We have to do something special if we are changing the command_name
    // before we do the rest of the import
    $command_name = grab_array_var($request, 'command_name', '');
    if (!empty($command_name)) {
        $command = $command_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["command"] . " SET command_name = '" . escape_sql_param($command_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_COMMAND,
                 "command_name" => $command);

    $options = array('command_line');
    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            $obj[$o] = $request[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $command . ' ' . _('in the system.'));
}


/**
 * Remove a comamnd or a set of commands from the CCM configuration
 *
 * @param   array   $request    The variables of the request
 * @return  array               Success for failure message
 */
function api_config_remove_cfg_command($request, $args)
{
    $missing = array();

    // First argument must be the command_name
    if (!empty($args) && count($args) == 1) {
        $commands = $args[0];
    } else {
        $commands = grab_array_var($request, 'command_name', array());
    }

    if (empty($commands)) { $missing[] = 'command_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($commands)) {
        $commands = array($commands);
    }

    // Loop through and remove the commands
    $command_errors = array();
    foreach ($commands as $command) {
        $id = nagiosccm_get_object_id('command', $command);
        if ($id <= 0) {
            $command_errors[] = $command;
            continue;
        }
        list($code, $message) = delete_object('command', $id);
        if ($code) {
            $command_errors[] = $command;
        }
    }

    // Check if we actually removed anything
    $removed_commands = array_diff($commands, $command_errors);
    if (empty($removed_commands)) {
        return array('error' => _('Could not remove commands. Failed commands may not exist.'),
                     'commands' => array(),
                     'failed' => $command_errors);
    }

    return array('success' => _('Removed commands from the system.'),
                 'commands' => $removed_commands,
                 'failed' => $command_errors);
}


// Contacts


function api_config_create_cfg_contact($args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    $required = array('contact_name', 'host_notifications_enabled', 'service_notifications_enabled', 'host_notification_period', 'service_notification_period', 'host_notification_options', 'service_notification_options', 'host_notification_commands', 'service_notification_commands');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_CONTACT);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
    }

    // Add any optional components that we need to
    $others = array('alias', 'contactgroups', 'minimum_importance', 'email', 'pager', 'can_submit_commands', 'retain_status_information', 'retain_nonstatus_information', 'use');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            $obj[$o] = $args[$o];
        }
    }

    // Add check for addressx variables which start with address
    foreach ($args as $key => $value) {
        if (substr($key, 0, 7) === 'address') {
            $obj[$key] = $value;
        }
    }

    // Add any free variables that we find (they must start with an underscore)
    foreach ($args as $key => $value) {
        if (substr($key, 0, 1) === '_') {
            $obj[$key] = $value;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['contact_name'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_contact($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the contact_name
    if (!empty($args) && count($args) == 1) {
        $contact = $args[0];
    } else {
        $contact = grab_array_var($request, 'old_contact_name', '');
    }

    if (empty($contact)) {
        return array('error' => _('You must enter a contact_name to update a contact.'));
    }

    // Check if a valid contact exists
    $id = nagiosccm_get_object_id('contact', $contact);
    if (!$id) {
        return array('error' => _('Could not update the contact specified. Does the contact exist?'));
    }

    // We have to do something special if we are changing the contact_name
    // before we do the rest of the import
    $contact_name = grab_array_var($request, 'contact_name', '');
    if (!empty($contact_name)) {
        $contact = $contact_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["contact"] . " SET contact_name = '" . escape_sql_param($contact_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_CONTACT,
                 "contact_name" => $contact);

    $options = array('host_notifications_enabled', 'service_notifications_enabled', 'host_notification_period', 'service_notification_period', 'host_notification_options', 'service_notification_options', 'host_notification_commands', 'service_notification_commands', 'alias', 'contactgroups', 'minimum_importance', 'email', 'pager', 'can_submit_commands', 'retain_status_information', 'retain_nonstatus_information', 'use');
    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            $obj[$o] = $request[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $contact . ' ' . _('in the system.'));
}


function api_config_remove_cfg_contact($request, $args)
{
    $missing = array();

    // First argument must be the contact_name
    if (!empty($args) && count($args) == 1) {
        $contacts = $args[0];
    } else {
        $contacts = grab_array_var($request, 'contact_name', array());
    }

    if (empty($contacts)) { $missing[] = 'contact_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($contacts)) {
        $contacts = array($contacts);
    }

    // Loop through and remove the contacts
    $contact_errors = array();
    foreach ($contacts as $contact) {
        $id = nagiosccm_get_object_id('contact', $contact);
        if ($id <= 0) {
            $contact_errors[] = $contact;
            continue;
        }
        list($code, $message) = delete_object('contact', $id);
        if ($code) {
            $contact_errors[] = $contact;
        }
    }

    // Check if we actually removed anything
    $removed_contacts = array_diff($contacts, $contact_errors);
    if (empty($removed_contacts)) {
        return array('error' => _('Could not remove contacts. Failed contacts may not exist.'),
                     'contacts' => array(),
                     'failed' => $contact_errors);
    }

    return array('success' => _('Removed contacts from the system.'),
                 'contacts' => $removed_contacts,
                 'failed' => $contact_errors);
}


// Contact Groups


function api_config_create_cfg_contactgroup($args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    $required = array('contactgroup_name', 'alias');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $obj = array("type" => OBJECTTYPE_CONTACTGROUP);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
    }

    // Add any optional components that we need to
    $others = array('members', 'contactgroup_members');

    foreach ($others as $o) {
        if (array_key_exists($o, $args)) {
            $obj[$o] = $args[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $args['contactgroup_name'] . ' ' . _('to the system.'));
}


function api_config_edit_cfg_contactgroup($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the contactgroup_name
    if (!empty($args) && count($args) == 1) {
        $contactgroup = $args[0];
    } else {
        $contactgroup = grab_array_var($request, 'old_contactgroup_name', '');
    }

    if (empty($contactgroup)) {
        return array('error' => _('You must enter a contactgroup_name to update a contact group.'));
    }

    // Check if a valid contactgroup exists
    $id = nagiosccm_get_object_id('contactgroup', $contactgroup);
    if (!$id) {
        return array('error' => _('Could not update the contact specified. Does the contact exist?'));
    }

    // We have to do something special if we are changing the contactgroup_name
    // before we do the rest of the import
    $contactgroup_name = grab_array_var($request, 'contactgroup_name', '');
    if (!empty($contactgroup_name)) {
        $contactgroup = $contactgroup_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["contactgroup"] . " SET contactgroup_name = '" . escape_sql_param($contactgroup_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_CONTACTGROUP,
                 "contactgroup_name" => $contactgroup);

    $options = array('alias', 'members', 'contactgroup_members');
    foreach ($options as $o) {
        if (array_key_exists($o, $request)) {
            $obj[$o] = $request[$o];
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $contactgroup . ' ' . _('in the system.'));
}


function api_config_remove_cfg_contactgroup($request, $args)
{
    $missing = array();

    // First argument must be the contactgroup_name
    if (!empty($args) && count($args) == 1) {
        $contactgroups = $args[0];
    } else {
        $contactgroups = grab_array_var($request, 'contactgroup_name', array());
    }

    if (empty($contactgroups)) { $missing[] = 'contactgroup_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($contactgroups)) {
        $contactgroups = array($contactgroups);
    }

    // Loop through and remove the contact groups
    $contactgroup_errors = array();
    foreach ($contactgroups as $contactgroup) {
        $id = nagiosccm_get_object_id('contactgroup', $contactgroup);
        if ($id <= 0) {
            $contactgroup_errors[] = $contactgroup;
            continue;
        }
        list($code, $message) = delete_object('contactgroup', $id);
        if ($code) {
            $contactgroup_errors[] = $contactgroup;
        }
    }

    // Check if we actually removed anything
    $removed_contactgroups = array_diff($contactgroups, $contactgroup_errors);
    if (empty($removed_contactgroups)) {
        return array('error' => _('Could not remove contact groups. Failed contact groups may not exist.'),
                     'contactgroups' => array(),
                     'failed' => $contactgroup_errors);
    }

    return array('success' => _('Removed contact groups from the system.'),
                 'contactgroups' => $removed_contactgroups,
                 'failed' => $contactgroup_errors);
}


// Time Periods


function api_config_create_cfg_timeperiod($args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    $required = array('timeperiod_name', 'alias');
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    if (count($missing) > 0 && empty($args['force'])) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    $timeperiod_name = $args['timeperiod_name'];
    $obj = array("type" => OBJECTTYPE_TIMEPERIOD);
    foreach ($required as $r) {
        $obj[$r] = $args[$r];
        unset($args[$r]); // Unset so we can loop through the rest later
    }

    // Add all other options that are passed
    foreach ($args as $k => $v) {
        if (!in_array($k, array('apikey', 'applyconfig', 'pretty', 'force', 'request'))) {
            $obj[$k] = $v;
        }
    }

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Added') . ' ' . $timeperiod_name . ' ' . _('to the system.'));
}


function api_config_edit_cfg_timeperiod($request, $args)
{
    global $db_tables;
    global $firsthost;
    global $ccm;

    // First argument must be the timeperiod_name
    if (!empty($args) && count($args) == 1) {
        $timeperiod = $args[0];
    } else {
        $timeperiod = grab_array_var($request, 'old_timeperiod_name', '');
    }

    if (empty($timeperiod)) {
        return array('error' => _('You must enter a timeperiod_name to update a contact group.'));
    }

    // Check if a valid timeperiod exists
    $id = nagiosccm_get_object_id('timeperiod', $timeperiod);
    if (!$id) {
        return array('error' => _('Could not update the contact specified. Does the contact exist?'));
    }

    // We have to do something special if we are changing the timeperiod_name
    // before we do the rest of the import
    $timeperiod_name = grab_array_var($request, 'timeperiod_name', '');
    if (!empty($timeperiod_name)) {
        $timeperiod = $timeperiod_name;
        $sql = "UPDATE " . $db_tables[DB_NAGIOSQL]["timeperiod"] . " SET timeperiod_name = '" . escape_sql_param($timeperiod_name, DB_NAGIOSQL) . "', last_modified = NOW() WHERE id = '" . $id . "'";
        exec_sql_query(DB_NAGIOSQL, $sql);
    }

    $obj = array("type" => OBJECTTYPE_TIMEPERIOD,
                 "timeperiod_name" => $timeperiod);

    // Add all other options that are passed
    foreach ($request as $k => $v) {
        if (!in_array($k, array('apikey', 'applyconfig', 'pretty', 'force', 'request'))) {
            $obj[$k] = $v;
        }
    }

    print_r($obj);

    // Commit the data to a file
    $objs = array($obj);
    $str = get_cfg_objects_str($objs, $firsthost);
    $ccm->import->fileImport($str, 1, true);

    return array('success' => _('Updated') . ' ' . $timeperiod . ' ' . _('in the system.'));
}


function api_config_remove_cfg_timeperiod($request, $args)
{
    $missing = array();

    // First argument must be the timeperiod_name
    if (!empty($args) && count($args) == 1) {
        $timeperiods = $args[0];
    } else {
        $timeperiods = grab_array_var($request, 'timeperiod_name', array());
    }

    if (empty($timeperiods)) { $missing[] = 'timeperiod_name'; }

    if (count($missing) > 0) {
        return array('error' => _('Missing required variables'), 'missing' => $missing);
    }

    if (!is_array($timeperiods)) {
        $timeperiods = array($timeperiods);
    }

    // Loop through and remove the contact groups
    $timeperiod_errors = array();
    foreach ($timeperiods as $timeperiod) {
        $id = nagiosccm_get_object_id('timeperiod', $timeperiod);
        if ($id <= 0) {
            $timeperiod_errors[] = $timeperiod;
            continue;
        }
        list($code, $message) = delete_object('timeperiod', $id);
        if ($code) {
            $timeperiod_errors[] = $timeperiod;
        }
    }

    // Check if we actually removed anything
    $removed_timeperiods = array_diff($timeperiods, $timeperiod_errors);
    if (empty($removed_timeperiods)) {
        return array('error' => _('Could not remove time periods. Failed time periods may not exist.'),
                     'timeperiods' => array(),
                     'failed' => $timeperiod_errors);
    }

    return array('success' => _('Removed time periods from the system.'),
                 'timeperiods' => $removed_timeperiods,
                 'failed' => $timeperiod_errors);
}

