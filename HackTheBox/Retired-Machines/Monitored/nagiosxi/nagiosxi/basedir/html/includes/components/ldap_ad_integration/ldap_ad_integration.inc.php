<?php
//
// LDAP / Active Directory Integration
// Copyright (c) 2015-2022 Nagios Enterprises, LLC. All rights reserved.
//
// Use AD or LDAP as an authentication source for XI
// -----------------------------------------
// This component makes it possible to use an Active Directory store as a source of user account
// information for Nagios XI. Rather than checking the username and password given against the
// built-in PostgreSQL database, they are checked against the AD domain controller specified to log
// the user in.
//
// Use AD or LDAP to import users into Nagios XI for integration
// -----------------------------------------
// This component allows you to browse the LDAP/AD directories to find users to import into
// the Nagios XI installation and use as authenticated users inside Nagios XI.
//

require_once(dirname(__FILE__).'/../componenthelper.inc.php');
if (!class_exists('adLDAP')) {
    require_once(dirname(__FILE__).'/adLDAP/src/adLDAP.php');
}
require_once(dirname(__FILE__).'/basicLDAP.php');

$ldap_ad_component_name = "ldap_ad_integration";

// Run the initialization function
ldap_ad_component_init();

//============================================
// COMPONENT INIT FUNCTIONS
//============================================

function ldap_ad_component_init()
{
    global $ldap_ad_component_name;
    $versionok = ldap_ad_component_checkversion();

    $desc = "";
    if (!$versionok) {
        $desc = "<br><b>"._("Error: This component requires Nagios XI 5.2.1 or later.")."</b>";
    }

    $args = array(
        COMPONENT_NAME => $ldap_ad_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Allows LDAP / Active Directory to be used as a central user authentication source for Nagios XI").". ".$desc,
        COMPONENT_TITLE => _("LDAP / Active Directory Integration"),
        COMPONENT_CONFIGFUNCTION => array("location" => "manage.php"),
        COMPONENT_TYPE => COMPONENT_TYPE_CORE,
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true
    );

    register_component($ldap_ad_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_PROCESS_AUTH_INFO, 'ldap_ad_component_check_authentication');
        register_callback(CALLBACK_MENUS_INITIALIZED, 'ldap_ad_component_addmenu');
    }
}

//============================================
// VERSION CHECK FUNCTIONS
//============================================

function ldap_ad_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }

    // Requires greater than XI 5
    if (get_product_release() < 512) {
        return false;
    }

    return true;
}

//============================================
// CONFIG FUNCTIONS
//============================================

function ldap_ad_has_security($security_level, $level)
{
    if ($security_level == $level) {
        return " selected";
    }
}

function ldap_ad_display_type($type) {
    switch ($type) {
        case 'ad':
            return "Active Directory";
        case 'ldap':
            return "LDAP";
        default:
            return $type;
    }
}

// Get user count with associations to this server
function ldap_ad_get_associations($server_id)
{
    $users = get_users();
    $count = 0;

    // Cycle through user list
    foreach ($users as $user) {
        $meta_auth_server_id = get_user_meta($user['user_id'], "auth_server_id");
        if ($meta_auth_server_id == $server_id) {
            $count++;
        }
    }

    return $count;
}

function ldap_ad_component_addmenu($arg=null)
{
    global $ldap_ad_component_name;

    // Retrieve the URL for this component
    $urlbase = get_component_url_base($ldap_ad_component_name);

    // Get the menu order from other menu items
    $mi = find_menu_item(MENU_ADMIN, "menu-admin-section-users", "id");
    if ($mi == null) { return; }
    $order = grab_array_var($mi, "order", "");
    if ($order == "") { return; }
    $neworder = $order+1.1;

    // Add this to the main home menu 
    add_menu_item(MENU_ADMIN,array(
        "type" => "link",
        "title" => _("LDAP/AD Integration"),
        "id" => "menu-admin-ldap-ad-integration",
        "order" => $neworder,
        "opts" => array(
            "icon" => "fa-id-card-o",
            "href" => $urlbase."/manage.php"  
        )
    ));
}

//============================================
// AUTHENTICATION FUNCTIONS
//============================================

function ldap_ad_component_check_authentication($cbtype, &$cbargs)
{
    // Get the credentials the user is passing to us
    $username = grab_array_var($cbargs["credentials"], "username");
    $password = grab_array_var($cbargs["credentials"], "password");
    $user_id = get_user_id($username);

    if (empty($user_id)) {
        return;
    }

    // Get the server and auth type of the user
    $auth_type = get_user_meta($user_id, 'auth_type');
    $server_id = get_user_meta($user_id, 'auth_server_id');
    $ldap_ad_username = get_user_meta($user_id, 'ldap_ad_username');
    $ldap_ad_dn = get_user_meta($user_id, 'ldap_ad_dn');
    $allow_local = get_user_meta($user_id, 'allow_local', 0);

    if (empty($server_id) || empty($auth_type)) {
        return;
    }

    if ($auth_type != "local") {

        // Create a new connection to the auth server
        $obj = create_auth_conn_obj($server_id);

        // Check authentication
        if ($auth_type == "ad") {

            // Authenticate using adLDAP library
            if ($obj->authenticate($ldap_ad_username, $password)) {
                $cbargs['login_ok'] = 1;
            } else {
                $cbargs["debug_messages"][] = _("Active Directory authentication failed!");
            }

        } else if ($auth_type == "ldap") {

            // Authenticate using basicLDAP library
            if ($obj->authenticate($ldap_ad_dn, $password)) {
                $cbargs['login_ok'] = 1;
            } else {
                $cbargs["debug_messages"][] = _("LDAP authentication failed!");
            }

        }

        if ($allow_local) {
            $cbargs['skip_local'] = 0;
        } else {
            $cbargs['skip_local'] = 1;
        }
    }
}

function create_auth_conn_obj($server_id='')
{
    // Get our settings
    $servers_raw = get_option("ldap_ad_integration_component_servers");
    if ($servers_raw == "") { $servers = array(); } else {
        $servers = unserialize(base64_decode($servers_raw));
    }

    // Find the server we are using or error...
    $server = array();
    foreach ($servers as $s) { 
        if ($s['id'] == $server_id) {
            $server = $s;
            break;
        }
    }

    if (empty($server)) {
        // Give out error about not connecting...
    }

    $use_tls = false;
    $use_ssl = false;

    // Initial values
    $enabled = grab_array_var($server, "enabled");
    $conn_method = grab_array_var($server, "conn_method");
    $base_dn = grab_array_var($server, "base_dn");
    $security_level = grab_array_var($server, "security_level", "");

    // AD only
    $ad_account_suffix = grab_array_var($server, "ad_account_suffix");
    $ad_domain_controllers = grab_array_var($server, "ad_domain_controllers");

    // LDAP only
    $ldap_host = grab_array_var($server, "ldap_host");
    $ldap_port = grab_array_var($server, "ldap_port");

    // Bail out if missing libraries...
    if (!function_exists('ldap_bind')) {
        echo "<p>"._('The required libraries for the Active Directory integration component are ')." <strong>"._('missing')."</strong>.</p>";
        return;
    }

    // Special cases for ssl or tls
    if ($security_level == "ssl") { $use_ssl = true; }
    if ($security_level == "tls") { $use_tls = true; }

    if ($conn_method == "ad") {
        $dc_array = explode('|', preg_replace('/[\,\ \;]+/', '|', $ad_domain_controllers));
        $options = array(
            'account_suffix' => $ad_account_suffix,
            'base_dn' => $base_dn,
            'domain_controllers' => $dc_array,
            'use_ssl' => $use_ssl,
            'use_tls' => $use_tls
        );

        // Include the class and create a connection
        try {
            $ldap_obj = new adLDAP($options);
            return $ldap_obj;
        } catch (adLDAPException $e) {
            return false;
        }
    } else if ($conn_method == "ldap") {

        // Try connecting to the LDAP server...
        try {
            $ldap_obj = new basicLDAP($ldap_host, $ldap_port, $base_dn, $security_level);
            return $ldap_obj;
        } catch (Exception $e) {
            return false;
        }
    }

    return false;
}

function create_obj()
{
    global $ad_error;

    $username = $_SESSION['import_ldap_ad_username'];
    $password = $_SESSION['import_ldap_ad_password'];
    $server_id = $_SESSION['import_ldap_ad_server_id'];
    $ldap_obj = create_auth_conn_obj($server_id);

    // Otherwise check authentication
    try {
        $x = $ldap_obj->authenticate($username, $password);
        if (!$x) {
            ldap_get_option($ldap_obj->getLdapConnection(), LDAP_OPT_ERROR_STRING, $out);
            if (empty($out)) {
                $ad_error = _("Could not connect to the LDAP server selected.");
            } else {
                $ad_error = $out;
            }
            return false;
        }
        return $ldap_obj;
    } catch (Exception $ex) {
        $ad_error = $ex->getMessage();
        return false;
    }
}


//============================================
// Auth Server Management Functions
//============================================


// Auth server data structure
//
// array(
//   "id" => (uniqid()),
//   enabled" => (1 or 0),
//   "conn_method" => (ad or ldap),
//   "ad_account_suffix" => (@nagios.com, req ad only),
//   "ad_domain_controllers" => (dc1.nagios.com, req ad only),
//   "base_dn" => (dc=nagios,dc=com),
//   "security_level" => (none, ssl, tls),
//   "ldap_port" => (386, req ldap only),
//   "ldap_host" => (192.168.1.12, req ldap only)
// );


/**
 * Adds an auth server to the list
 *
 * @param   array   $data   Auth server (formatted as auth_server)
 */
function auth_server_add($data)
{
    $servers = auth_server_list();
    $servers[] = $data;
    set_option("ldap_ad_integration_component_servers", base64_encode(serialize($servers)));
    return $data['id'];
}


/**
 * Removes an auth server from the list
 *
 * @param   string  $id     Auth server ID
 * @return  bool            True if removed
 */
function auth_server_remove($id)
{
    $servers = auth_server_list();
    $removed = false;

    $new_servers = array();
    foreach ($servers as $server) {
        if ($server['id'] != $id) {
            $new_servers[] = $server;
        } else {
            $removed = true;
        }
    }

    if (!$removed) {
        return false;
    }

    set_option('ldap_ad_integration_component_servers', base64_encode(serialize($new_servers)));
    return true;
}


/**
 * Update auth server with data based on the auth server ID given
 *
 * @param   string  $id     Auth server ID
 * @param   array   $data   Data to update as key => value
 * @return  bool            True if updated
 */
function auth_server_update($id, $data)
{
    $servers = auth_server_list();

    foreach ($servers as &$server) {
        if ($server['id'] == $id) {
            foreach ($data as $key => $value) {
                $server[$key] = $value;
            }
        }
    }

    set_option("ldap_ad_integration_component_servers", base64_encode(serialize($servers)));
    return true;
}


/**
 * Get a list of auth servers servers
 *
 * @return  array           List of auth servers
 */
function auth_server_list()
{
    $servers_raw = get_option("ldap_ad_integration_component_servers");
    if ($servers_raw == "") { $servers = array(); } else {
        $servers = unserialize(base64_decode($servers_raw));
    }

    return $servers;
}


/**
 * Get a single auth server based on ID
 *
 * @param   string          $id     Auth server ID
 * @return  array|bool              Auth server or false if server doesn't exist
 */
function auth_server_get($id)
{
    $servers = auth_server_list();

    foreach ($servers as $server) {
        if ($server['id'] == $id) {
            return $server;
        }
    }

    return false;
}
