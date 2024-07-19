<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2021 Nagios Enterprises, LLC
//
//  Authors:
//      Jacob Omann - Nagios Enterprises <jomann@nagios.com>
//      Scott Wilkerson - Nagios Enterprises <swilkerson@nagios.com>
//
//  Past Authors:
//      Mike Guthrie - Nagios Enterprises
//      Luke Groshen - Nagios Enterprises
//      Bryan Heden - Nagios Enterprises
//
//  Based on NagiosQL 3.0.3
//  Original Authors:
//      Martin Willisegger
//

// Include the component helper file
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// Run the initialization function
$ccm_component_name = "ccm";
$ccm_update_running = 0;
ccm_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ccm_component_init()
{
    global $ccm_component_name;
    global $cfg;
    $versionok = ccm_component_checkversion();
    
    // Component description / version ok check
    $desc = _("Integration with Nagios Core Config Manager used to manage object configuration files for Nagios XI.");
    if (!$versionok) {
        $desc = "<b>"._('Error: This component requires Nagios XI 2011R3.4 or later.')."</b>";
    }
    
    // All components require a few arguments to be initialized correctly.  
    $args = array(
        COMPONENT_NAME => $ccm_component_name,
        COMPONENT_VERSION => '3.2.0',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => _("Nagios Core Config Manager (CCM)"),
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );
    
    // Register this component with XI 
    register_component($ccm_component_name, $args);
    
    // Register the addmenu function
    define('MENU_CCM', 'ccm');
    
    // Register all callbacks for Nagios XI
    if ($versionok) {
        register_callback(CALLBACK_MENUS_DEFINED, 'add_ccm_menu');
        register_callback(CALLBACK_MENUS_INITIALIZED, 'ccm_component_addmenu');
        register_callback(CALLBACK_PAGE_HEAD, 'ccm_component_head_include');
        register_callback(CALLBACK_SUBSYS_GENERIC, 'ccm_sync_permissions');
        register_callback(CALLBACK_USER_DELETED, 'ccm_delete_permissions');
        register_callback(CALLBACK_CCM_MODIFY_HOSTSERVICE, 'rename_ccm_hostservice');
        register_callback(CALLBACK_SUBSYS_GENERIC, 'sync_rename_ccm_hostservice');
    }   
}   

/**
 * Callback function for the do_page_head funtion that creates the required includes for
 * the CCM styles and javascript while inside of Nagios XI.
 */
function ccm_component_head_include($cbtype='', $args=null)
{
    global $components;
    $component_base = get_base_url().'includes/components/ccm/';

    if (strpos($_SERVER['PHP_SELF'], 'includes/components/ccm/') !== false || strpos($_SERVER['PHP_SELF'], 'includes/components/bulkmodifications/') !== false) {
        echo "<link rel='stylesheet' type='text/css' href='".$component_base."css/style.css?".$components['ccm']['args']['version']."' />";
        echo '<script type="text/javascript" src="'.$component_base.'javascript/main_js.js?'.$components['ccm']['args']['version'].'"></script>
              <script type="text/javascript">
              var NAGIOSXI=true
              </script>';
    }
}

// Requires Nagios XI version greater than 5.6.0
function ccm_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }
    if (get_product_release() < 5600) {
        return false;
    }
    return true;
}


///////////////////////////////////////////////////
//  Menu Functions 
//////////////////////////////////////////////////


// Add the CCM link to the Configure page's menu items
function ccm_component_addmenu($arg=null)
{
	global $autodiscovery_component_name;

	$mi = find_menu_item(MENU_CONFIGURE, "menu-configure-section-advanced", "id");
	if ($mi == null) {
		return;
	}
		
	$order = grab_array_var($mi, "order", "");
	if ($order == "") {
		return;
	}
	$neworder = $order + 1;

    add_menu_item(MENU_CONFIGURE, array(
        "type" => "link",
        "title" => _("Core Config Manager"),
        "id" => "menu-configure-ccm",
        "order" => $neworder,
        "opts" => array(
            "href" => get_base_url().'includes/components/ccm/xi-index.php',
            "icon" => "fa-cog",
            "target" => "_top"
            ),
        "function" => "user_can_access_ccm"
    ));
}


// Build the CCM menu and add it to the page
function add_ccm_menu()
{
    add_menu(MENU_CCM); 
    $ccm_home = get_base_url()."includes/components/ccm/";
    $corecfg_path = get_base_url()."includes/components/nagioscorecfg/";

    add_menu_item(MENU_CCM, array(
        "type" => "html",
        "title" => _("Nagios Core Config Manager"),
        "id" => "menu-ccm-logo",
        "order" => 100,
        "opts" => array(
            "html" => "<a href='index.php' id='menu-ccm-logo' target='maincontentframe'>"._("Core Config Manager")."</a>"
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Quick Tools"),
        "id" => "menu-ccm-section-quicktools",
        "order" => 200,
        "opts" => array(
            "id" => "quicktools",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Apply Configuration"),
        "id" => "menu-ccm-applyconfiguration",
        "order" => 210,
        "opts" => array(
            "id" => "ccm-apply-menu-link",
            "icon" => "fa-asterisk",
            "href" => $corecfg_path."applyconfig.php?cmd=confirm"
        )
    ));

    if (is_authorized_for_all_objects() || is_admin()) {
        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Configuration Snapshots"),
            "id" => "menu-ccm-configsnapshots",
            "order" => 211,
            "opts" => array(
                "icon" => "fa-hdd-o",
                "href" => get_base_url()."admin/coreconfigsnapshots.php",
                "target" => "maincontentframe"
            )
        ));
    }

    if (is_authorized_to_configure_objects() || is_admin()) {
        add_menu_item(MENU_CCM, array(
            "type" => "linkspacer",
            "order" => 212
        ));
    }

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Monitoring Plugins"),
        "id" => "menu-ccm-monitoringplugins",
        "order" => 221,
        "opts" => array(
            "icon" => "fa-share",
            "href" => get_base_url()."admin/?xiwindow=monitoringplugins.php",
            "target" => "_parent"
            ),
        "function" => "is_admin"
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Configuration Wizards"),
        "id" => "menu-ccm-configwizards",
        "order" => 222,
        "opts" => array(
            "icon" => "fa-share",
            "href" => get_base_url()."config/?xiwindow=monitoringwizard.php",
            "target" => "_parent",
        ),
        "function" => "is_authorized_to_configure_objects"
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "id" => "menu-ccm-sectionend-quicktools",
        "order" => 223,
        "title" => "",
        "opts" => ""
    ));
    
    //
    // Monitoring
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Monitoring"),
        "id" => "menu-ccm-section-monitoring",
        "order" => 300,
        "opts" => array(
            "id" => "monitoring",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Hosts"),
        "id" => "menu-ccm-hosts",
        "order" => 301,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $ccm_home.'?cmd=view&type=host'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Services"),
        "id" => "menu-ccm-services",
        "order" => 302,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $ccm_home.'?cmd=view&type=service'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Host Groups"),
        "id" => "menu-ccm-hostgroups",
        "order" => 303,
        "opts" => array(
            "icon" => "fa-folder-open-o",
            "href" => $ccm_home.'?cmd=view&type=hostgroup'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Service Groups"),
        "id" => "menu-ccm-servicegroups",
        "order" => 304,
        "opts" => array(
            "icon" => "fa-folder-open-o",
            "href" => $ccm_home.'?cmd=view&type=servicegroup'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-monitoring",
        "order" => 305,
        "opts" => ""
    ));

    // 
    // Alerting
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Alerting"),
        "id" => "menu-ccm-section-alerting",
        "order" => 400,
        "opts" => array(
            "id" => "alerting",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_CCM,array(
        "type" => "link",
        "title" => _("Contacts"),
        "id" => "menu-ccm-contacts",
        "order" => 401,
        "opts" => array(
            "icon" => "fa-user",
            "href" => $ccm_home.'?cmd=view&type=contact'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Contact Groups"),
        "id" => "menu-ccm-contactgroups",
        "order" => 402,
        "opts" => array(
            "icon" => "fa-users",
            "href" => $ccm_home.'?cmd=view&type=contactgroup'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Time Periods"),
        "id" => "menu-ccm-timeperiods",
        "order" => 403,
        "opts" => array(
            "icon" => "fa-clock-o",
            "href" => $ccm_home.'?cmd=view&type=timeperiod'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Host Escalations"),
        "id" => "menu-ccm-hostescalations",
        "order" => 404,
        "opts" => array(
            "icon" => "fa-flag",
            "href" => $ccm_home."?cmd=view&type=hostescalation"
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Service Escalations"),
        "id" => "menu-ccm-serviceescalations",
        "order" => 405,
        "opts" => array(
            "icon" => "fa-flag",
            "href" => $ccm_home."?cmd=view&type=serviceescalation"
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-alerting",
        "order" => 406,
        "opts" => ""
    ));

    //
    // Templates
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Templates"),
        "id" => "menu-ccm-section-templates",
        "order" => 500,
        "opts" => array(
            "id" => "templates",
            "expanded" => false
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Host Templates"),
        "id" => "menu-ccm-hosttemplates",
        "order" => 501,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $ccm_home.'?cmd=view&type=hosttemplate'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Service Templates"),
        "id" => "menu-ccm-servicetemplates",
        "order" => 502,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $ccm_home.'?cmd=view&type=servicetemplate'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Contact Templates"),
        "id" => "menu-ccm-contacttemplates",
        "order" => 503,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $ccm_home.'?cmd=view&type=contacttemplate'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-templates",
        "order" => 504,
        "opts" => ""
    ));

    //
    // Commands
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Commands"),
        "id" => "menu-ccm-section-commands",
        "order" => 600,
        "opts" => array(
            "id" => "commands",
            "expanded" => false
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => "Commands",
        "id" => "menu-ccm-commands",
        "order" => 601,
        "opts" => array(
            "icon" => 'fa-terminal',
            "href" => $ccm_home.'?cmd=view&type=command'
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-commands",
        "order" => 602,
        "opts" => ""
    ));

    //
    // Advanced
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Advanced"),
        "id" => "menu-ccm-section-advanced",
        "order" => 700,
        "opts" => array(
            "id" => "advanced",
            "expanded" => false
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Host Dependencies"),
        "id" => "menu-ccm-hostdependencies",
        "order" => 701,
        "opts" => array(
            "icon" => "fa-list-ul",
            "href" => $ccm_home."?cmd=view&type=hostdependency"
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Service Dependencies"),
        "id" => "menu-ccm-servicedependencies",
        "order" => 702,
        "opts" => array(
            "icon" => "fa-list-ul",
            "href" => $ccm_home."?cmd=view&type=servicedependency"
        )
    ));

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-advanced",
        "order" => 705,
        "opts" => ""
    ));

    //
    // Tools
    //

    add_menu_item(MENU_CCM, array(
        "type" => "menusection",
        "title" => _("Tools"),
        "id" => "menu-ccm-section-tools",
        "order" => 800,
        "opts" => array(
            "id" => "tools",
            "expanded" => false
        )
    ));

    if (!is_v2_license_type('cloud')) {

        if (ccm_has_access_for('staticconfig')) {
            add_menu_item(MENU_CCM, array(
                "type" => "link",
                "title" => _("Static Config Editor"),
                "id" => "menu-ccm-staticconfigurations",
                "order" => 801,
                "opts" => array(
                    "icon" => 'fa-pencil-square-o',
                    "href" => $ccm_home."?cmd=admin&type=static"
                )
            ));
        }

        if (ccm_has_access_for('import')) {
            add_menu_item(MENU_CCM, array(
                "type" => "link",
                "title" => _("Import Config Files"),
                "id" => "menu-ccm-importconfigfiles",
                "order" => 803,
                "opts" => array(
                    "icon" => 'fa-upload',
                    "href" => $ccm_home."?cmd=admin&type=import"
                )
            ));
        }

    }

    if (ccm_has_access_for('configmanagement')) {
        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Config File Management"),
            "id" => "menu-ccm-configfilemanage",
            "order" => 804,
            "opts" => array(
                'icon' => 'fa-file',
                "href" => $ccm_home.'?cmd=apply'
            )
        ));
    }

    add_menu_item(MENU_CCM, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-ccm-sectionend-tools",
        "order" => 810,
        "opts" => ""
    ));

    //
    // Config Manager Admin
    //

    // Only allow full admins to view the CCM Admin section
    if (is_admin() || get_user_meta(0, 'ccm_access', 0) == 1) {

        add_menu_item(MENU_CCM, array(
            "type" => "menusection",
            "title" => _("CCM Admin"),
            "id" => "menu-ccm-section-admin",
            "order" => 900,
            "opts" => array(
                "id" => "nagiosqladmin",
                "expanded" => false
            )
        ));

        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Manage Users"),
            "id" => "menu-ccm-manageconfigaccess",
            "order" => 901,
            "opts" => array(
                "icon" => 'fa-wrench',
                "href" => $ccm_home."?cmd=admin&type=user"
            )
        ));

        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Settings"),
            "id" => "menu-ccm-configmanagersettings",
            "order" => 903,
            "opts" => array(
                "icon" => 'fa-cog',
                "href" => $ccm_home."?cmd=admin&type=settings"
            )
        ));

        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Core Configs"),
            "id" => "menu-ccm-coremainconfig",
            "order" => 904,
            "opts" => array(
                "icon" => 'fa-book',
                "href" => $ccm_home."?cmd=admin&type=corecfg"
            )
        ));

        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("Audit Log"),
            "id" => "menu-ccm-configmanagerlog",
            "order" => 905,
            "opts" => array(
                "icon" => 'fa-bars',
                "href" => $ccm_home."?cmd=admin&type=log",
            )
        ));

        add_menu_item(MENU_CCM, array(
            "type" => "menusectionend",
            "title" => "",
            "id" => "menu-ccm-sectionend-admin",
            "order" => 920,
            "opts" => ""
        ));
    }
}


/**
 * When a user is deleted, remove all the permissions from the databases
 *
 * @param   array   $args   Arguments passed via callback
 */
function ccm_delete_permissions($cbtype='', $args=null)
{
    if (!empty($args['user_id'])) {
        exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_permission WHERE user_id = ".intval($args['user_id']).";");
        exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_permission_inactive WHERE user_id = ".intval($args['user_id']).";");
    }
}


/**
 * Send the CCM update permissions command when an apply config is ran
 *
 * @param   string  $cbtype     Callback type
 * @param   array   $args       Arguments passed via callback
 */
function ccm_sync_permissions($cbtype='', $args=null)
{
    // Check if it was apply config
    if ($args['command'] != COMMAND_NAGIOSCORE_APPLYCONFIG || $args['return_code'] != 0) {
        return;
    }

    // If a command isn't already scheduled, add one to update perms
    if (!is_command_scheduled(COMMAND_CCM_UPDATE_PERMS)) {
        submit_command(COMMAND_CCM_UPDATE_PERMS);
    }
}


/**
 * Update the CCM user permissions (may take a while because it waits for ndo to start)
 */
function ccm_update_permissions()
{
    // Verify that NDO is running before continuing...
    $ndo_is_running = false;
    $timeout = 0;
    do {

        $rs = exec_sql_query(DB_NDOUTILS, "SELECT is_currently_running FROM nagios_programstatus;");
        $x = $rs->GetArray();

        // Wait for database to be populated
        if (!empty($x) && $x[0]['is_currently_running']) {
            $ndo_is_running = true;
            break;
        }

        sleep(1);
        $timeout++;

    } while ($timeout < 30);

    // Get all users with user meta for CCM limited access
    if ($ndo_is_running) {
        $users = get_users();
        foreach ($users as $u) {

            // Skip admin users and anyone without limited CCM access
            if (is_admin($u['user_id']) || get_user_meta($u['user_id'], 'ccm_access', 0) != 2) {
                continue;
            }

            ccm_update_user_permissions($u['user_id']);

        }
    }
}


/**
 * Create user permissions in the CCM user permissions table
 *
 * This allows us to easily do permissions-based authentication in the CCM
 * based on what we have in NDO (so even external config files or static ones count)
 *
 * @param   int     $user_id    The user ID
 * @return  bool                True if permissions updated
 */
function ccm_update_user_permissions($user_id=null)
{
    global $ccm_update_running;

    // We have a global variable set to 0 by default, and if set to 1 that means we are
    // already running this function. This is caused by running get_objects_xml_output() due
    // to the cached ndoutils permissions being updated. When they are updated during an apply config
    // this function is called after cache update and will cause SQL errors in the cmdsubsys log.
    if ($ccm_update_running) {
        return;
    }
    $ccm_update_running = 1;

    // Check if we are logged in doing an update
    if (empty($user_id) && array_key_exists('user_id', $_SESSION)) {
        $user_id = $_SESSION['user_id'];
    }
    $user_id = intval($user_id);

    // Get all the permissions for hosts
    $in_objects = array();
    $objects = get_objects_xml_output(array('objecttype_id' => OBJECTTYPE_HOST), true, false, $user_id);
    foreach ($objects as $o) {
        $in_objects[] = $o['name1'];
    }

    // Get host IDs from the CCM
    $rs = exec_sql_query(DB_NAGIOSQL, "SELECT id FROM tbl_host WHERE host_name IN ('".implode("','", $in_objects)."');");
    $objects = $rs->GetArray();

    // Remove old hosts
    exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_permission WHERE user_id = ".$user_id." AND type = ".OBJECTTYPE_HOST.";");

    // Add new hosts
    foreach ($objects as $o) {
        exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_permission (user_id, object_id, type) VALUES (".$user_id.", ".$o['id'].", ".OBJECTTYPE_HOST.");", false);
    }

    // Get all services and find the ones we want to see
    $rs = exec_sql_query(DB_NAGIOSQL, "SELECT tbl_service.id, tbl_host.host_name, tbl_service.service_description FROM tbl_service
                                       LEFT JOIN tbl_lnkServiceToHost ON tbl_service.id = idMaster
                                       LEFT JOIN tbl_host ON tbl_host.id = idSlave;");
    $services = $rs->GetArray();

    // Get all service objects
    $objects = get_objects_xml_output(array('objecttype_id' => OBJECTTYPE_SERVICE), true, false, $user_id);

    // Remove old services
    exec_sql_query(DB_NAGIOSQL, "DELETE FROM tbl_permission WHERE user_id = ".$user_id." AND type = ".OBJECTTYPE_SERVICE.";");

    foreach ($objects as $o) {
        foreach ($services as $s) {
            if ($s['host_name'] == $o['name1'] && $s['service_description'] == $o['name2']) {
                exec_sql_query(DB_NAGIOSQL, "INSERT INTO tbl_permission (user_id, object_id, type) VALUES (".$user_id.", ".$s['id'].", ".OBJECTTYPE_SERVICE.");", false);
                break;
            }
        }
    }

    $ccm_update_running = 0;
    return true;
}


/**
 * Copy user permissions from one object to another object
 *
 * Note: This function copies ALL current permissions for the object
 * including any users who also have permission not just the user logged in
 *
 * @param   string  $type       Type of object (host or service)
 * @param   int     $oid        Object ID to copy from
 * @param   int     $new_oid    Object ID to copy permissions to
 * @param   bool    $inactive   True to force copying permissions into the inactive table
 * @return  bool                True if successful
 */
function ccm_copy_user_permissions($type, $oid, $new_oid, $inactive=false)
{
    global $ccm;
    $oid = intval($oid);
    $new_oid = intval($new_oid);

    if (!in_array($type, array('host', 'service'))) {
        return false;
    }

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    // Check if original object is active or not
    $obj = $ccm->db->query("SELECT * FROM tbl_$type WHERE id = $oid;");
    $active = $obj[0]['active'];

    // Set the table to copy from
    $table = "tbl_permission";
    if (!$active) {
        $table = "tbl_permission_inactive";
    }

    // Set the table we are copying to
    $to_table = "tbl_permission";
    if ($inactive || !$active) {
        $to_table = "tbl_permission_inactive";
    }

    // Update the object's regular permissions
    $perms = $ccm->db->query("SELECT * FROM $table WHERE object_id = $oid AND type = $type_id;");
    if (!empty($perms)) {
        foreach ($perms as $p) {
            $ccm->db->query("INSERT INTO $to_table (user_id, object_id, type) VALUES (".$p['user_id'].", $new_oid, $type_id);");
        }
    }

    return true;
}


/**
 * Move user permissions for active/inactive button clicks 
 *
 * @param   string  $type       Type of object (host or service)
 * @param   int     $oid        Object ID to move permission of
 * @param   string  $inactive   The table to move from
 * @return  bool                True if successfully moved
 */
function ccm_move_user_permissions($type, $oid, $inactive=false)
{
    global $ccm;
    $oid = intval($oid);

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    $from = "tbl_permission";
    $to = "tbl_permission_inactive";

    if ($inactive) {
        $from = "tbl_permission_inactive";
        $to = "tbl_permission";
    }

    // Get all permissions and move them
    $perms = $ccm->db->query("SELECT * FROM $from WHERE object_id = $oid AND type = $type_id;");
    if (!empty($perms)) {
        foreach ($perms as $p) {
            $ccm->db->query("INSERT INTO $to (user_id, object_id, type) VALUES (".$p['user_id'].", $oid, $type_id);");
        }
    }

    // Remove all the permissions from the original table
    $ccm->db->query("DELETE FROM $from WHERE object_id = $oid AND type = $type_id");

    return true;
}


/**
 * Add permissions on a single object for a user
 * 
 * This is used during the CCM activation/deactivation of objects, so that
 * users can still edit and see them when NDO doesn not have the data anymore
 *
 * @param   string  $type       Type of object (host or service)
 * @param   int     $object_id  Object ID
 * @param   bool    $inactive   Inactive object or not
 * @param   int     $user_id    The User ID
 * @return  bool                True if successfully added
 */
function ccm_add_user_permissions($type, $object_id, $inactive=false, $user_id=null)
{
    global $ccm;
    $object_id = intval($object_id);

    if (empty($user_id) && array_key_exists('user_id', $_SESSION)) {
        $user_id = $_SESSION['user_id'];
    }
    $user_id = intval($user_id);

    $table = 'tbl_permission';
    if ($inactive) {
        $table = 'tbl_permission_inactive';
    }

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    // Add the actual permission into the DB
    $result = $ccm->db->query("INSERT INTO $table (user_id, object_id, type) VALUES ($user_id, $object_id, $type_id);");

    return $result;
}


/**
 * Remove permissions on a single object for a user
 *
 * @param   string  $type       Type of object (host or service)
 * @param   int     $object_id  Object ID
 * @param   bool    $inactive   Inactive object or not
 * @param   int     $user_id    The User ID
 * @return  bool                True if successfully removed
 */
function ccm_remove_user_permissions($type, $object_id, $inactive=false, $user_id=null)
{
    global $ccm;
    $object_id = intval($object_id);

    if (empty($user_id) && array_key_exists('user_id', $_SESSION)) {
        $user_id = $_SESSION['user_id'];
    }
    $user_id = intval($user_id);

    $table = 'tbl_permission';
    if ($inactive) {
        $table = 'tbl_permission_inactive';
    }

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    // Remove the actual permission into the DB
    $result = $ccm->db->query("DELETE FROM $table WHERE user_id = $user_id AND object_id = $object_id AND type_id = $type_id;");

    return $result;
}


/**
 * Remove all user permissions for a single object (active and inactive)
 *
 * @param   string  $type       Type of object (host or service)
 * @param   int     $object_id  Object ID
 * @return  bool                True if successfully removed
 */
function ccm_remove_all_user_permissions($type, $object_id)
{
    global $ccm;
    $object_id = intval($object_id);

    // Remove all permissions
    $ccm->db->query("DELETE FROM tbl_permission WHERE object_id = $object_id;");
    
    // Remove all inactive permissions
    $ccm->db->query("DELETE FROM tbl_permission_inactive WHERE object_id = $object_id;");

    return true;
}


/**
 * Get array of CCM object IDs for certain types
 *
 * @param   string  $type       Type of object (host or servie)
 * @param   int     $user_id    User ID
 * @return  array               Array of object IDs
 */
function ccm_get_user_object_ids($type, $user_id=null)
{
    global $ccm;
    $in_objects = array();

    if (empty($user_id) && array_key_exists('user_id', $_SESSION)) {
        $user_id = $_SESSION['user_id'];
    }
    $user_id = intval($user_id);

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    $objects = $ccm->db->query("SELECT object_id FROM tbl_permission WHERE type = $type_id AND user_id = $user_id;");
    if (count($objects) > 0) {
        foreach ($objects as $o) {
            $in_objects[] = $o['object_id'];
        }
    }
    return $in_objects;
}


/**
 * Check if the user has permission for a specific CCM object based on ID
 *
 * @param   int     $id         The database ID (id in db)
 * @param   string  $type       Type of object (host or service)
 * @param   array   $data       Table data for the host/service object
 * @param   int     $user_id    User ID to check against (uses session value if not specified)
 * @return  bool                True if user can access the object
 */
function ccm_has_access_for_object($id, $type, $data=array(), $user_id=null)
{
    global $ccm;
    $id = intval($id);

    if (empty($user_id) && array_key_exists('user_id', $_SESSION)) {
        $user_id = $_SESSION['user_id'];
    }
    $user_id = intval($user_id);

    $type_id = OBJECTTYPE_HOST;
    if ($type == 'service') {
        $type_id = OBJECTTYPE_SERVICE;
    }

    // Get data for host or service object if it does not already exist (passed via $data)
    if (empty($data)) {
        $table = 'tbl_host';
        if ($type == 'service') {
            $table = 'tbl_service';
        }
        $data = $ccm->db->query("SELECT * FROM $table WHERE id = $id LIMIT 1");
        if (empty($data) || count($data) != 1) {
            return false;
        }
        $data = $data[0];
    }

    // Check for direct permissions (active vs inactive)
    $table = 'tbl_permission';
    if ($data['active'] == 0) {
        $table = 'tbl_permission_inactive';
    }
    $object = $ccm->db->query("SELECT object_id FROM $table WHERE type = $type_id AND user_id = $user_id AND object_id = $id;");
    if (!empty($object)) {
        return true;
    }

    // If service host_name == 2 means that the host_name applied is a * or all hosts thus the user has access to it
    if ($type == 'service') {
        if ($data['host_name'] == 2) {
            return true;
        }
    }

    return false;
}


/**
 * Check if a user has CCM access to a specific area
 * NOTE: Only works with CCM ACCESS == 2 (if the CCM is actually restricted for a user)
 *
 * @param   string  $permission     Permission value
 * @return  bool                    True if user can access
 */
function ccm_has_access_for($permission)
{
    if (is_admin()) {
        return true;
    }

    // Check for ccm access type
    if (get_user_meta(0, 'ccm_access') != 2) {
        return true;
    }

    // Check if permissions are implied
    if (in_array($permission, array('host', 'service'))) {
        return true;
    }

    // Check if the user has the specific permissions
    $ccm_access_list = get_user_meta(0, 'ccm_access_list', array());
    if (!empty($ccm_access_list)) {
        $ccm_access_list = unserialize(base64_decode($ccm_access_list));
    }
    if (in_array($permission, $ccm_access_list)) {
        return true;
    }

    return false;
}


/**
 * Tracks a user in the CCM to show where they currently are and to make sure that
 * someone isn't editing the same object
 */
function session_tracking()
{
    global $ccm;

    $session_id = hash('sha256', session_id());
    $type = $ccm->db->escape_string(grab_request_var('type', ''));
    $id = intval(grab_request_var('id', 0));
    $ip = $_SERVER['REMOTE_ADDR'];
    $backend = intval(grab_request_var('backend', 0));

    if (empty($type) || empty($id) || $backend) {
        return;
    }

    $user_id = intval($_SESSION["user_id"]);
    $sql = "SELECT * FROM `tbl_session` WHERE `user_id` = ".$user_id." AND `session_id` = '".$session_id."' AND `obj_id` = ".$id." AND `type` = '".$type."';";
    $sessions = $ccm->db->query($sql);
    
    if (empty($sessions)) {

        // Record current session
        $t = time();
        $sql = "INSERT INTO `tbl_session` (`user_id`, `type`, `obj_id`, `started`, `last_updated`, `session_id`, `ip`, `active`) VALUES (".$user_id.", '".$type."', ".$id.", ".$t.", ".$t.", '".$session_id."', '".$ip."', 1);";
        $ccm->db->query($sql, false);
        $ccm_session_id = $ccm->db->get_last_id();

    } else {

        // Update current session
        $ccm_session_id = $sessions[0]['id'];
        foreach ($sessions as $s) {
            $sql = "UPDATE `tbl_session` SET `last_updated` = '".time()."' WHERE `id` = ".$s['id'].";";
            $ccm->db->query($sql, false);
        }

    }

    // If there are sessions older than 5 min let's delete them
    $timeout = get_option('ccm_page_lock_timeout', 300);
    $del_time = time() - $timeout;
    $sql = "DELETE FROM `tbl_session` WHERE `last_updated` < ".$del_time.";";
    $ccm->db->query($sql, false);

    // Look for any locks that exist or not
    $sql = "SELECT l.id as id FROM `tbl_session_locks` AS l LEFT JOIN `tbl_session` AS s ON s.id = l.sid WHERE s.session_id IS null;";
    $res = $ccm->db->query($sql);
    if (!empty($res)) {
        foreach ($res as $r) {
            $ccm->db->query("DELETE FROM `tbl_session_locks` WHERE id = ".$r['id'], false);
        }
    }

    // Check if there are any valid locks
    // and if there aren't we can create our own
    $sql = "SELECT * FROM `tbl_session_locks` AS l LEFT JOIN `tbl_session` AS s ON s.id = l.sid WHERE l.obj_id = ".$id." AND l.type = '".$type."';";
    $res = $ccm->db->query($sql);
    if (empty($res)) {
        $sql = "INSERT INTO `tbl_session_locks` (`sid`, `obj_id`, `type`) VALUES (".$ccm_session_id.", ".$id.", '".$type."');";
        $ccm->db->query($sql, false);
    }

    return $ccm_session_id;
}


/**
 * Create a session-based lock on an object while it is actively being edited
 */
function session_get_lock()
{
    global $ccm;

    $type = $ccm->db->escape_string(grab_request_var('type', ''));
    $id = intval(grab_request_var('id', 0));

    // Grab user_id if session exists (backend never has a full session)
    $user_id = 0;
    if (array_key_exists('user_id', $_SESSION)) {
        $user_id = intval($_SESSION["user_id"]);
    }

    if (empty($type) || empty($id)) {
        return false;
    }

    // Check if the current page is locked or not
    $sql = "SELECT *, l.id AS id FROM `tbl_session_locks` AS l LEFT JOIN `tbl_session` AS s ON l.sid = s.id WHERE l.obj_id = ".$id." AND l.type = '".$type."' AND s.user_id != ".$user_id." LIMIT 1;";
    $res = $ccm->db->query($sql);
    if (!empty($res)) {
        $lock = $res[0];

        $username = get_user_attr($lock['user_id'], 'username');
        $lock['username'] = $username;
        return $lock;
    }

    return false;
}


// ---------------------------------------
// Rename functions
// ---------------------------------------


/**
 * Adds renamed host/service objects to sync data
 *
 * @param   string  $cbtype     Callback type
 * @param   array   $args       Array of data from do_callback call
 */
function rename_ccm_hostservice($cbtype='', $args=null)
{
    // Don't continue if rename component is missing
    if (!function_exists('rename_service_perfdata') && !function_exists('rename_host_perfdata')) {
        return;
    }

    // Get the rename cache
    $rename_cache = get_array_option_json('rename_cache');

    if (!empty($args['service_description'])) {
        // Services
        if ($args['service_description'] != $args['old_service_description']) {
            if (array_key_exists($args['id'], $rename_cache)) {
                $rename_cache[$args['id']]['service_description'] = $args['service_description'];
            } else {
                $rename_cache[$args['id']] = array(
                    'service_description' => $args['service_description'],
                    'old_name' => $args['old_service_description'],
                    'type' => 'service'
                );
            }
        }
    } else {
        // Hosts
        if ($args['host_name'] != $args['old_host_name']) {
            if (array_key_exists($args['id'], $rename_cache)) {
                $rename_cache[$args['id']]['host_name'] = $args['host_name'];
            } else {
                $rename_cache[$args['id']] = array(
                    'host_name' => $args['host_name'],
                    'old_name' => $args['old_host_name'],
                    'type' => 'host'
                );
            }
        }
    }

    // Save the cache
    set_array_option_json('rename_cache', $rename_cache);
}


/**
 * Syncs renamed host/service objects with their RRD/XML data files
 *
 * @param   string  $cbtype     Callback type
 * @param   array   $args       Array of data from do_callback call
 */
function sync_rename_ccm_hostservice($cbtype='', $args=null)
{
    // Check if it was apply config
    if ($args['command'] != COMMAND_NAGIOSCORE_APPLYCONFIG || $args['return_code'] != 0) {
        return;
    }

    // Get the rename cache
    $rename_cache = get_array_option_json('rename_cache');

    if (!empty($rename_cache)) {

        // Loop over the objects that need to be renamed
        foreach ($rename_cache as $id => $obj) {
            $msg = '';
            $hoststring = '';
            if ($obj['type'] == 'host') {
                if (function_exists('rename_host_perfdata')) {
                    rename_host_perfdata($obj, $msg);
                }
            } else {
                if (function_exists('rename_service_perfdata')) {
                    $obj['hosts'] = get_service_to_host_relationships($id, $hoststring);
                    rename_service_perfdata($obj, $msg);
                }
            }
        }

        // Do hostservice rename callbacks
        do_callbacks(CALLBACK_CCM_APPLY_MODIFY_HOSTSERVICE, $rename_cache);

        // Set rename cache
        set_array_option_json('rename_cache', array());

    }
}
