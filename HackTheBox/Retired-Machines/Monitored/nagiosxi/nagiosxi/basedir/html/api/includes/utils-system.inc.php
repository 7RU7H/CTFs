<?php
//
// New API utils and classes for Nagios XI 5
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

function api_system_apply_config()
{
    $cmd_id = submit_command(COMMAND_NAGIOSCORE_APPLYCONFIG);
    if (empty($cmd_id) || $cmd_id == -1) {
        return array('error' => _('Apply config command failed to be sent to the command subsystem.'));
    }
    return array('success' => _('Apply config command has been sent to the backend.'), 'command_id' => $cmd_id);
}


function api_system_import_config()
{
    $cmd_id = submit_command(COMMAND_NAGIOSCORE_IMPORTONLY);
    if (empty($cmd_id) || $cmd_id == -1) {
        return array('error' => _('Import config command failed to be sent to the command subsystem.'));
    }
    return array('success' => _('Import configs command has been sent to the backend.'), 'command_id' => $cmd_id);
}


function api_system_get_commands($args, $command_id = 0)
{
    $data = array();
    $opts = array();

    if (!empty($command_id)) {
        $opts['command_id'] = $command_id;
    } else if (!empty($args['command_id'])) {
        $opts['command_id'] = $args['command_id'];
    }

    // Do some filtering
    if (array_key_exists('status_code', $args)) {
        $opts['status_code'] = $args['status_code'];
    }

    $arr = get_command_status_xml($opts, true, false);
    if (!empty($arr)) {
        foreach ($arr as $i => $cmd) {
            unset($cmd['group_id']);
            unset($cmd['frequency_type']);
            unset($cmd['frequency_units']);
            unset($cmd['frequency_interval']);
            unset($cmd['beneficiary_id']);
            unset($cmd['command_data']);
            $data[] = $cmd;
        }
    }

    return $data;
}


function api_system_get_users($user_id = 0)
{
    $data = array();
    $opts = array();

    if (!empty($user_id)) {
        $opts = array("user_id" => $user_id);
    }

    // See if we're expecting all information
    $advanced = (bool) grab_request_var("advanced", false);

    // Get users
    $users = get_users($opts);

    // Add in the data
    $data['records'] = count($users);
    foreach ($users as $u) {

        $user = array(
            'user_id'  => $u['user_id'],
            'username' => $u['username'],
            'name'     => $u['name'],
            'email'    => $u['email'],
            'enabled'  => $u['enabled'],
        );

        if ($advanced) {

            $user_id = $u['user_id'];

            $admin                      = intval(is_admin($user_id));
            $authorized_for_all_objects = intval(get_user_meta($user_id, 'authorized_for_all_objects', 0));
            $language                   = intval(get_user_meta($user_id, 'language', 0));
            $date_format                = intval(get_user_meta($user_id, 'date_format', 0));
            $number_format              = intval(get_user_meta($user_id, 'number_format', 0));

            // Convert to string for consistency in API
            $user['admin']                      = strval($admin);
            $user['authorized_for_all_objects'] = strval($authorized_for_all_objects);
            $user['language']                   = strval($language);
            $user['date_format']                = strval($date_format);
            $user['number_format']              = strval($number_format);
        }

        $data['users'][] = $user;
    }

    return $data;
}


function api_system_add_user($args)
{
    $missing = array();

    if (in_demo_mode()) {
        return array("error" => _("Can not use this action in demo mode."));
    }

    // Get values
    $username = strtolower(grab_array_var($args, "username", ""));
    $password = grab_array_var($args, "password", "");
    $email = grab_array_var($args, "email", "");
    $name = grab_array_var($args, "name", "");
    $level = grab_array_var($args, "auth_level", "user");
    $forcechangepass = grab_array_var($args, "force_pw_change", 1);
    $email_info = grab_array_var($args, "email_info", 1);
    $add_contact = grab_array_var($args, "monitoring_contact", 1);
    $enable_notifications = grab_array_var($args, "enable_notifications", 1);
    $language = grab_array_var($args, "language", "");
    $date_format = grab_array_var($args, "defaultDateFormat", DF_ISO8601);
    $number_format = grab_array_var($args, "defaultNumberFormat", NF_2);
    $authorized_for_all_objects = grab_array_var($args, "can_see_all_hs", 0);
    $authorized_for_all_object_commands = grab_array_var($args, "can_control_all_hs", 0);
    $authorized_to_configure_objects = grab_array_var($args, "can_reconfigure_hs", 0);
    $authorized_for_monitoring_system = grab_array_var($args, "can_control_engine", 0);
    $advanced_user = grab_array_var($args, "can_use_advanced", 0);
    $readonly_user = grab_array_var($args, "read_only", 0);
    $api_enabled = grab_array_var($args, "api_enabled", 0);
    $theme = grab_array_var($args, "theme", "");

    // Auth type variables
    $auth_type = grab_array_var($args, "auth_type", "local");
    $allow_local = grab_array_var($args, "allow_local", 0);
    $auth_server_id = grab_array_var($args, "auth_server_id", "");
    $ad_username = grab_array_var($args, "ad_username", "");
    $ldap_dn = grab_array_var($args, "ldap_dn", "");

    // Verify that everything we need is here
    $required = array('username', 'email', 'name');
    foreach ($required as $r) {
        if (!array_key_exists($r, $args)) {
            $missing[] = $r;
        }
    }

    // Verify auth level
    $auth = '';
    if ($level == "user") {
        $level = 1;
    } else if ($level == "admin") {
        $level = 255;
    } else {
        $auth = array('auth_level' => _('Must be either user or admin.'));
    }

    // Verify auth types
    if ($auth_type == 'local') {
        if (empty($password)) {
            $missing[] = "password";
        }
    } else {
        if (empty($auth_server_id)) {
            $missing[] = "auth_server_id";
        } else {
            $forcechangepass = 0;
            $email_info = 0;

            // Verify that the auth server provided exists
            if (auth_server_get($auth_server_id) === false) {
                $auth = array('auth_server_id' => _('Not a valid auth_server_id given. Auth server IDs can be found using GET system/authserver.'));
            }

            // Verify password exists if local auth allowed
            if ($allow_local && empty($password)) {
                $auth = array('password' => _('You must enter a password if you are allowing local authentication if the AD/LDAP server cannot be reached.'));
            } else if (empty($password)) {
                $password = random_string(16);
            }

        }
    }

    // We are missing required fields
    if (count($missing) > 0 || !empty($auth)) {
        $errormsg = array('error' => _('Could not create user. Missing required fields.'));
        if (!empty($auth)) { $errormsg['messages'] = $auth; }
        if (count($missing) > 0) { $errormsg['missing'] = $missing; }
        return $errormsg;
    }

    // Verify that user doesn't exist
    if (get_user_id($username) !== null) {
        return array("error" => _('A user with the username provided already exists.'));
    }

    // Verify that password is proper length
    if (strlen($password) > 72) {
        return array("error" => _('Passwords must be less than 72 characters long.'));
    }

    // If everything looks okay then proceed to create the user
    $user_id = add_user_account($username, $password, $name, $email, $level, $forcechangepass, $add_contact, $api_enabled, $errmsg);

    set_user_meta($user_id, 'name', $name);
    set_user_meta($user_id, 'language', $language);
    set_user_meta($user_id, 'theme', $theme);
    set_user_meta($user_id, "date_format", $date_format);
    set_user_meta($user_id, "number_format", $number_format);
    set_user_meta($user_id, "authorized_for_all_objects", $authorized_for_all_objects);
    set_user_meta($user_id, "authorized_for_all_object_commands", $authorized_for_all_object_commands);
    set_user_meta($user_id, "authorized_to_configure_objects", $authorized_to_configure_objects);
    set_user_meta($user_id, "authorized_for_monitoring_system", $authorized_for_monitoring_system);
    set_user_meta($user_id, "advanced_user", $advanced_user);
    set_user_meta($user_id, "readonly_user", $readonly_user);

    // Set authentication settings
    set_user_meta($user_id, "auth_type", $auth_type);
    set_user_meta($user_id, "allow_local", $allow_local);
    if ($auth_type == 'ad') {
        set_user_meta($user_id, "auth_server_id", $auth_server_id);
        set_user_meta($user_id, "ldap_ad_username", $ad_username);
    } else if ($auth_type == 'ldap') {
        set_user_meta($user_id, "auth_server_id", $auth_server_id);
        set_user_meta($user_id, "ldap_ad_dn", $ldap_dn);
    }

    if ($add_contact) {
        set_user_meta($user_id, "enable_notifications", $enable_notifications);
    }

    // Update nagios cgi config file
    update_nagioscore_cgi_config();

    if ($email_info) {
        $url = get_option("url");

        // Use this for debug output in PHPmailer log
        $debugmsg = "";

        // Set where email is coming from for PHPmailer log
        $send_mail_referer = "api/includes/utils-api.inc.php > API - Add User";

        $msg = _("An account has been created for you to access Nagios XI.  You can login using the following information:\n\nUsername: %s\nPassword: %s\nURL: %s\n\n");
        $message = sprintf($msg, $username, $password, $url);
        $opts = array(
            "to" => $email,
            "subject" => _("Nagios XI Account Created"),
            "message" => $message,
        );
        send_email($opts, $debugmsg, $send_mail_referer);
    }

    // Log it
    if ($level == L_GLOBALADMIN) {
        send_to_audit_log("User account '" . $username . "' was created with GLOBAL ADMIN privileges", AUDITLOGTYPE_SECURITY);
    }

    $ret = array('success' => _('User account') . ' ' . $username . ' ' . _('was added successfully!'));
    $ret['user_id'] = $user_id;
    return $ret;
}


function api_system_delete_user($user_id = 0)
{
    if (in_demo_mode()) {
        return array("error" => _("Can not use this action in demo mode."));
    }

    if (!is_valid_user_id($user_id)) {
        return array("error" => _('User with') . ' ID ' . $user_id . ' ' . _('does not exist.'));
    }

    if ($user_id == $_SESSION["user_id"]) {
        return array("error" => _('Cannot delete the user you are currently using.'));
    }

    // Remove user from http
    update_nagioscore_cgi_config();
    $args = array(
        "username" => get_user_attr($user_id, 'username'),
    );
    submit_command(COMMAND_NAGIOSXI_DEL_HTACCESS, serialize($args));
    delete_user_id($user_id);

    return array('success' => _('User removed.'));
}


function api_system_get_status()
{
    return get_program_status_xml_output(array(), true);
}


function api_system_get_info()
{
    $ret = array(
        'product' => get_product_name(true),
        'version' => get_product_version(),
        'version_major' => get_product_version('major'),
        'version_minor' => get_product_version('minor'),
        'build_id' => get_build_id(),
        'release' => get_product_release()
    );
    return $ret;
}


// =========================================
// Auth Servers
// =========================================


function api_system_get_auth_servers()
{
    // Check if we are getting a single auth server by ID
    $server_id = grab_request_var('server_id', '');
    $data = array('records' => 0, 'authservers' => array());

    // Remove the auth server from the DB
    if (empty($server_id)) {
        $servers = auth_server_list();
        $data = array('records' => count($servers), 'authservers' => $servers);
    } else {
        $server = auth_server_get($server_id);
        if ($server) {
            $data = array('records' => 1, 'authservers' => array($server));
        }
    }

    return $data;
}


function api_system_add_auth_server($args)
{
    $missing = array();
    $valid_methods = array('ldap', 'ad');

    $enabled = grab_array_var($args, 'enabled', 1);
    $conn_method = grab_array_var($args, 'conn_method', '');
    $base_dn = grab_array_var($args, 'base_dn', '');
    $domain_controllers = grab_array_var($args, 'ad_domain_controllers', '');
    $security_level = grab_array_var($args, 'security_level', 'none');
    $ldap_host = grab_array_var($args, 'ldap_host', '');
    $ldap_port = grab_array_var($args, 'ldap_port', '');
    $account_suffix = grab_array_var($args, 'ad_account_suffix', '');

    // Add default LDAP port if we need to
    if ($conn_method == 'ldap') {
        if (empty($ldap_port)) {
            $ldap_port = '389';
        }
    }

    // Verification checks
    if (!in_array($conn_method, $valid_methods)) {
        return array('error' => _('The conn_method you specified is not allowed. Choose ad or ldap only.'));
    }

    // Verify user has all items based on conn_method
    if ($conn_method == 'ad') {
        if (empty($domain_controllers)) {
            $missing[] = "ad_domain_controllers";
        }
        if (empty($account_suffix)) {
            $missing[] = "ad_account_suffix";
        }

        if (!empty($missing)) {
            return array('error' => _('If you are using AD you must pass the following options.'),
                         'missing' => $missing);
        }
    } else {
        if (empty($ldap_host)) {
            $missing[] = "ldap_host";
        }

        if (!empty($missing)) {
            return array('error' => _('If you are using LDAP you must pass the following option.'),
                         'missing' => $missing);
        }
    }

    // Create the auth server
    $server = array(
        "id" => uniqid(),
        "enabled" => $enabled,
        "conn_method" => $conn_method,
        "ad_account_suffix" => $account_suffix,
        "ad_domain_controllers" => $domain_controllers,
        "base_dn" => $base_dn,
        "security_level" => $security_level,
        "ldap_port" => $ldap_port,
        "ldap_host" => $ldap_host
    );
    $server_id = auth_server_add($server);

    return array('success' => 'Auth server created successfully.',
                 'server_id' => $server_id);
}


function api_system_remove_auth_server($id='')
{
    if (empty($id)) {
        return array('error' => _('No auth server ID given.'));
    }

    // Remove the auth server
    $removed = auth_server_remove($id);

    // If the server wasn't removed, we should not save it and error out
    if (!$removed) {
        return array('error' => _("Could not remove auth server with ID given."));
    }

    return array('success' => _("Removed auth server successfully."));
}


// =========================================
// Fusion Connections
// =========================================


function api_system_add_fuse_host($args)
{
    global $ccmDB;
    $host = $ccmDB->escape_string($args['host']);

    if (empty($host)) {
        return array('error' => _('No host given.'));
    }

    // Add the fused host to the fused host list
    $fuse_hosts = explode(',', get_option('frame_options_allowed_fusion_hosts', ''));
    $add = true;
    foreach ($fuse_hosts as $k => $h) {
        if ($h == $host) {
            $add = false;
        }
    }
    if ($add) {
        $fuse_hosts[] = $host;
    }
    set_option('frame_options_allowed_fusion_hosts', implode(',', $fuse_hosts));

    return array('success' => _("Fusion host added successfully."));
}


function api_system_remove_fuse_host($host='')
{
    if (empty($host)) {
        return array('error' => _('No host given.'));
    }

    // Remove fuse host from the list
    $fuse_hosts = explode(',', get_option('frame_options_allowed_fusion_hosts', ''));
    if ($fuse_hosts > 0) {
        foreach ($fuse_hosts as $k => $h) {
            if ($h == $host) {
                unset($fuse_hosts[$k]);
            }
        }
        set_option('frame_options_allowed_fusion_hosts', implode(',', $fuse_hosts));
    } else {
        return array('error' => _("No fuse hosts exist."));
    }

    return array('success' => _("Fusion host removed successfully."));
}


// =========================================
// Scheduled Downtime
// =========================================


function api_system_add_scheduled_downtime()
{
    global $cfg;

    $errors = 0;
    $error_items = array();
    $scheduled = array();

    $author = grab_request_var('author', get_user_attr($_SESSION['user_id'], 'username'));
    $comment = grab_request_var('comment', '');
    $triggered_by = intval(grab_request_var('triggered_by', 0));
    $flexible = intval(grab_request_var('flexible', 0));
    $duration = intval(grab_request_var('duration', 0));
    $start = grab_request_var('start', 0);
    $end = grab_request_var('end', 0);

    // Objects
    $hosts = grab_request_var('hosts', array());
    $services = grab_request_var('services', array());
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroups = grab_request_var('servicegroups', array());

    // Host only
    $child_hosts = intval(grab_request_var('child_hosts', 0));
    $all_services = intval(grab_request_var('all_services', 0));

    // Hostgroups and Servicegroups only
    $only = grab_request_var('only', '');
    if ($only != 'services' && $only != 'hosts') {
        $only = '';
    }

    // Check for all empty
    if (empty($hosts) && empty($services) && empty($hostgroups) && empty($servicegroups)) {
        return array('error' => _("You must enter at least one hosts[], services[], hostgroups[], or servicegroups[]."));
    }

    // Comments are required
    if (empty($comment)) {
        return array('error' => _("You must enter a comment."));
    }

    // Check if start/end times are in a non-timestamp format
    if (!empty($cfg["api_datetime_format"])) {

        $format = $cfg["api_datetime_format"];

        // If we can translate from the specified format
        // back into what the submitted time was
        // Then we know it isn't timestamp format
        if (   (date($format, strtotime($start)) === $start)
            && (date($format, strtotime($end)) === $end)) {

            $start = strtotime($start);
            $end = strtotime($end);
        }
    }

    $start = intval($start);
    $end = intval($end);

    // Check for valid timestamp
    if ($start > $end) {
        return array('error' => _("Your start time cannot be after your end time."));
    }

    // Generate command arguments
    $args = array("comment_data" => $comment,
                  "comment_author" => $author,
                  "trigger_id" => $triggered_by,
                  "start_time" => $start,
                  "end_time" => $end,
                  "fixed" => 1);

    // If flexible, add a duration value
    if ($flexible == 1) {
        $args['fixed'] = 0;
        $args['duration'] = $duration;
    }

    // Do any hosts we were sent
    if (!empty($hosts)) {
        foreach ($hosts as $host) {
            $host_args = $args;
            $host_args['host_name'] = $host;

            $cmd = NAGIOSCORE_CMD_SCHEDULE_HOST_DOWNTIME;
            if ($child_hosts == 2) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_TRIGGERED_HOST_DOWNTIME;
            } else if ($child_hosts == 1) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_HOST_DOWNTIME;
            }

            // Run command
            $core_cmd = core_get_raw_command($cmd, $host_args);
            $x = core_submit_command($core_cmd, $output);
            if (!$x) {
                $errors++;
                $error_items['hosts'][] = $host;
            }

            $scheduled['hosts'][] = $host;

            // If all services on host
            if ($all_services == 1) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_HOST_SVC_DOWNTIME;
                $core_cmd = core_get_raw_command($cmd, $host_args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['services'][$host][] = _('ALL');
                }
                $scheduled['services'][$host][] = _('ALL');
            }
        }
    }

    // Do any services we were sent
    if (!empty($services)) {
        foreach ($services as $host => $svcs) {
            if (!is_array($svcs)) {
                $svcs = array($svcs);
            }
            foreach ($svcs as $service) {
                $service_args = $args;
                $service_args['host_name'] = $host;
                $service_args['service_name'] = $service;
                $core_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_SVC_DOWNTIME, $service_args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['services'][$host][] = $service;
                }
                $scheduled['services'][$host][] = $service;
            }
        }
    }

    // Do host groups
    if (!empty($hostgroups)) {
        foreach ($hostgroups as $hostgroup) {
            $hostgroup_args = $args;
            $hostgroup_args['host_group'] = $hostgroup;
            $core_host_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_HOSTGROUP_HOST_DOWNTIME, $hostgroup_args);
            $core_svc_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_HOSTGROUP_SVC_DOWNTIME, $hostgroup_args);

            // Add hosts if we are not only adding services
            if ($only != 'services') {
                $x = core_submit_command($core_host_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['hostgroups'][$hostgroup][] = _('HOSTS');
                }
                $scheduled['hostgroups'][$hostgroup][] = _('HOSTS');
            }

            // Add services if we are not only adding hosts
            if ($only != 'hosts') {
                $x = core_submit_command($core_svc_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['hostgroups'][$hostgroup][] = _('SERVICES');
                }
                $scheduled['hostgroups'][$hostgroup][] = _('SERVICES');
            }
        }
    }

    // Do service groups
    if (!empty($servicegroups)) {
        foreach ($servicegroups as $servicegroup) {
            $servicegroup_args = $args;
            $servicegroup_args['service_group'] = $servicegroup;
            $core_host_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_SERVICEGROUP_HOST_DOWNTIME, $servicegroup_args);
            $core_svc_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_SERVICEGROUP_SVC_DOWNTIME, $servicegroup_args);

            // Add hosts if we are not only adding services
            if ($only != 'services') {
                $x = core_submit_command($core_host_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['servicegroups'][$servicegroup][] = _('HOSTS');
                }
                $scheduled['servicegroups'][$servicegroup][] = _('HOSTS');
            }

            // Add services if we are not only adding hosts
            if ($only != 'hosts') {
                $x = core_submit_command($core_svc_cmd, $output);
                if (!$x) {
                    $errors++;
                    $error_items['servicegroups'][$servicegroup][] = _('SERVICES');
                }
                $scheduled['servicegroups'][$servicegroup][] = _('SERVICES');
            }
        }
    }

    // Return data on what was created
    if ($errors) {
        return array(
            'error' => _("Errors sending schedule downtime command(s) to Core."),
            'scheduled' => $scheduled,
            'not_scheduled' => $error_items
        );
    }

    return array(
        'success' => _("Schedule downtime command(s) sent successfully."),
        'scheduled' => $scheduled
    );
}


function api_system_remove_scheduled_downtime($id='')
{
    global $db_tables;
    $errors = 0;

    if (empty($id)) {
        return array('error' => _("You must specify a scheduled downtime ID."));
    }

    // If passed a list, do all of them
    $downtime_ids = array();
    if (strpos($id, ',') === false) {
        $downtime_ids[] = intval($id);
    } else {
        $ids = explode(',', $id);
        foreach ($ids as $i) {
            $downtime_ids[] = intval(trim($i));
        }
    }

    // Try to remove the scheduled downtime
    foreach ($downtime_ids as $downtime_id) {
        $sql = "SELECT downtime_type FROM " . $db_tables[DB_NDOUTILS]['scheduleddowntime'] . " WHERE internal_downtime_id = '" . $downtime_id . "'";
        $rs = exec_sql_query(DB_NDOUTILS, $sql);
        $dt = $rs->GetArray();

        // Could not find the downtime in the NDO db
        if (empty($dt)) {
            continue;
        }

        // Delete downtime based on type
        if ($dt[0]['downtime_type'] == 2) {
            $cmd = NAGIOSCORE_CMD_DEL_HOST_DOWNTIME;
        } else {
            $cmd = NAGIOSCORE_CMD_DEL_SVC_DOWNTIME;
        }

        $core_cmd = core_get_raw_command($cmd, array('downtime_id' => $downtime_id));
        $x = core_submit_command($core_cmd, $output);
        if (!$x) { $errors++; }
    }

    // Check for errors
    if ($errors) {
        return array('error' => _("Errors sending remove downtime command(s) to Core."));
    }

    return array('success' => _("Remove downtime command(s) sent successfully."));
}

// =========================================
// Mass Immediate Check
// =========================================

/**
 * Perform an immediate check on whatever service,host,hostgroup,servicegroup indicated in api request
 * 
 * @return array            Success and Errors
 */
function api_system_mass_immediate_check()
{

    $errors = 0;
    $error_items = array();
    $sent = array();
    $now = time();

    // Objects
    $hosts = grab_request_var('hosts', array());
    $services = grab_request_var('services', array());
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroups = grab_request_var('servicegroups', array());

    // Host only
    $all_services = intval(grab_request_var('all_services', 0));

    // Hostgroups and Servicegroups only
    $only = grab_request_var('only', '');
    if ($only != 'services' && $only != 'hosts') {
        $only = '';
    }

    // Check for all empty
    if (empty($hosts) && empty($services) && empty($hostgroups) && empty($servicegroups)) {
        return array('error' => _("You must enter at least one hosts[], services[], hostgroups[], or servicegroups[]."));
    }

    // Do any hosts we were sent
    if (!empty($hosts)) {
        foreach ($hosts as $host) {
            $x = api_host_immediate_check($host, $all_services);
            $errors += $x['errors'];
            $error_items['hosts'][] = $x['error_items'];
            $sent['hosts'][] = $x['sent'];
        }
    }

    // Do any services we were sent
    if (!empty($services)) {
        foreach ($services as $host => $svcs) {
            if (!is_array($svcs)) {
                $svcs = array($svcs);
            }
            foreach ($svcs as $service) {
                $x = api_service_immediate_check($host, $service);
                $errors += $x['errors'];
                $error_items['services'][$host][] = $x['error_items'];
                $sent['services'][$host][] = $x['sent'];
            }
        }
    }

    // Do host groups
    // Essentially going to get a list of hosts in a hostgroup and run the host command on it.
    if (!empty($hostgroups)) {
        foreach ($hostgroups as $hostgroup) {
            $x = api_hostgroup_immediate_check($hostgroup, $all_services);
            $errors += $x['errors'];
            $error_items['hostgroups'][] = $x['error_items'];
            $sent['hostgroups'][] = $x['sent'];
        }
    }

    // Do service groups
    // Same as host groups
    if (!empty($servicegroups)) {
        foreach ($servicegroups as $servicegroup) {
            $x = api_servicegroup_immediate_check($servicegroup);
            $errors += $x['errors'];
            $error_items['servicegroups'][] = $x['error_items'];
            $sent['servicegroups'][] = $x['sent'];
        }
    }

    // Return data on what was created
    if ($errors) {
        return array(
            'error' => _("Errors sending mass immediate check command(s) to Core."),
            'time' => date("Y-m-d H:i:s", $now),
            'error_items' => $error_items
        );
    }

    return array(
        'success' => _("Mass immediate check command(s) sent successfully."),
        'time' => date("Y-m-d H:i:s", $now),
        'checks' => $sent
    );
}

/**
 * Submits a schedule forced check core command to a particular host
 * 
 * @param string $host                  Host to send command to
 * @param string $all_services          Determine kind of command
 * @return array                        Error items, error count, and sent items
 */
function api_host_immediate_check($host, $all_services) {
    $host_args = array("start_time" => time());
    $host_args['host_name'] = $host;
    $retVal = array(
        'errors' => 0,
        'error_items' => null,
        'sent' => null
    );

    $cmd = NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_CHECK;
    if ($all_services == 1) {
        $cmd = NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_SVC_CHECKS;
    }
    // Run command
    $core_cmd = core_get_raw_command($cmd, $host_args);
    $x = core_submit_command($core_cmd, $output);
    if (!$x) {
        $retVal['errors']++;
        $retVal['error_items'] = $host;
    }
    $retVal['sent'] = $host;
    return $retVal;
}

/**
 * Submits a schedule forced check core command for a particular service
 * 
 * @param string $host              Host of the service 
 * @param string $service           Service to send command to
 * @return array                    Error items, error count, and sent items
 */
function api_service_immediate_check($host, $service) {
    $service_args = array("start_time" => time());
    $service_args['host_name'] = $host;
    $service_args['service_name'] = $service;
    $retVal = array(
        'errors' => 0,
        'error_items' => null,
        'sent' => null
    );

    $core_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_FORCED_SVC_CHECK, $service_args);
    $x = core_submit_command($core_cmd, $output);
    if (!$x) {
        $retVal['errors']++;
        $retVal['error_items'] = $service;
    }
    $retVal['sent'] = $service;
    return $retVal;
}

/**
 * Uses SQL to gather a list of hosts based on a hostgroup and submits a schedule forced check command for each host
 * 
 * @param string $hostgroup             Hostgroup to submit commands to
 * @param mixed $all_services           Decides which command to submit
 * @return array                        Returns an array of error count, error items, and sent commands
 */
function api_hostgroup_immediate_check($hostgroup, $all_services) {
    global $db_tables;
    $retVal = array(
        'errors' => 0,
        'error_items' => null,
        'sent' => null
    );

    $sql =  "select obj2.name1 from " . $db_tables[DB_NDOUTILS]['objects'] . " as obj2 ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['hostgroup_members'] . " on obj2.object_id=" . $db_tables[DB_NDOUTILS]['hostgroup_members'] . ".host_object_id ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['hostgroups'] . " on " . $db_tables[DB_NDOUTILS]['hostgroups'] . ".hostgroup_id=" . $db_tables[DB_NDOUTILS]['hostgroup_members'] . ".hostgroup_id ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['objects'] . " as obj1 on obj1.object_id=" . $db_tables[DB_NDOUTILS]['hostgroups'] . ".hostgroup_object_id where obj1.name1=". escape_sql_param($hostgroup, DB_NDOUTILS, true) . ";"; 
    $hostraw = exec_sql_query(DB_NDOUTILS, $sql);   
    if (!$hostraw) {
        $retVal['errors']++;
        $retVal['error_items'][] = $hostgroup;
    }
    $hostarray = $hostraw->GetArray();
    foreach ($hostarray as $hostwrap) {
        $host = $hostwrap['name1'];
        $x = api_host_immediate_check($host, $all_services);
        $retVal['errors'] += $x['errors'];
        $retVal['error_items'][$hostgroup][] = $x['error_items'];
        $retVal['sent'][$hostgroup][] = $x['sent'];
    }
    return $retVal;
}

/**
 * Uses a SQL query to gather a list of services in a servicegroup and submits a schedule forced check command
 * 
 * @param string $servicegroup              Service group to submit commands to
 * @return array                            Returns an array of error count, error items, and sent commands
 */
function api_servicegroup_immediate_check($servicegroup) {
    global $db_tables;
    $retVal = array(
        'errors' => 0,
        'error_items' => null,
        'sent' => null
    );

    $sql =  "select obj2.name1,obj2.name2 from " . $db_tables[DB_NDOUTILS]['objects'] . " as obj2 ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['servicegroup_members'] . " on obj2.object_id=" . $db_tables[DB_NDOUTILS]['servicegroup_members'] . ".service_object_id ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['servicegroups'] . " on " . $db_tables[DB_NDOUTILS]['servicegroups'] . ".servicegroup_id=" . $db_tables[DB_NDOUTILS]['servicegroup_members'] . ".servicegroup_id ";
    $sql .= "inner join " . $db_tables[DB_NDOUTILS]['objects'] . " as obj1 on obj1.object_id=" . $db_tables[DB_NDOUTILS]['servicegroups'] . ".servicegroup_object_id where obj1.name1=". escape_sql_param($servicegroup, DB_NDOUTILS, true) . ";"; 
    $serviceraw = exec_sql_query(DB_NDOUTILS, $sql);   
    if (!$serviceraw) {
        $retVal['errors']++;
        $retVal['error_items'][] = $servicegroup;
    }
    $servicearray = $serviceraw->GetArray();
    foreach ($servicearray as $servicewrap) {
        $host = $servicewrap['name1'];
        $service = $servicewrap['name2'];
        $x = api_service_immediate_check($host, $service);
        $retVal['errors'] += $x['errors'];
        $retVal['error_items'][$servicegroup][$host][] = $x['error_items'];
        $retVal['sent'][$servicegroup][$host][] = $x['sent'];
    }
    return $retVal;
}


// =========================================
// Run Core Command
// =========================================


function api_system_core_command()
{
    global $nagioscore_cmds;
    $cmd = grab_request_var('cmd', '');

    // Validate command is available
    if (empty($cmd)) {
        return array('error' => _("You must specify an external command."));
    }

    if (strpos($cmd, ';') !== false) {
        $cmd_parts = explode(';', $cmd);
        $cmd_name = $cmd_parts[0];
    } else {
        $cmd_name = $cmd;
    }

    // If no command exists, exit out
    $valid_cmds = array_values($nagioscore_cmds);
    if (!in_array($cmd_name, $valid_cmds)) {
        return array('error' => _("You must specify a valid external command."));
    }

    // Replace macros
    $username = get_user_attr($_SESSION['user_id'], 'username');
    $cmd = str_replace("%user%", $username, $cmd);

    // If everything is good, send the full command to the nagios.cmd file
    // TODO: We can improve this and not have to write out the command manually
    $output = "";
    $success = core_submit_command($cmd, $output);

    if (!$success) {
        return array('error' => $output);
    }

    return array('cmd' => $cmd, 'success' => $output);
}
