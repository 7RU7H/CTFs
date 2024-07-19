<?php
//
// Global Notification Management Component
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$deploynotification_component_name = "deploynotification";

// Run the initialization function
deploynotification_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function deploynotification_component_init()
{
    global $deploynotification_component_name;

    // Check for latest version
    $versionok = deploynotification_component_checkversion();

    // Component description
    $desc = _("This component allows administrators to create, save,
	and deploy notification message to a list of Nagios XI users or contact groups.");

    if (!$versionok) {
        $desc = "<b>" . _("Error: This component requires Nagios XI 5.2.1 Enterprise edition or later.") . "</b>";
    }

    // All components require a few arguments to be initialized correctly.
    $args = array(
        COMPONENT_NAME => $deploynotification_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => "Notification Management",
        COMPONENT_VERSION => "1.3.4",
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    // Register this component with XI
    register_component($deploynotification_component_name, $args);

    // Register the addmenu function
    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'deploynotification_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


// Requires XI 5 or greater
function deploynotification_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }
    if (get_product_release() < 512) {
        return false;
    }
    return true;
}


function deploynotification_component_addmenu($arg = null)
{
    global $deploynotification_component_name;

    // Retrieve the URL for this component
    $urlbase = get_component_url_base($deploynotification_component_name);
    
    // Figure out where I'm going on the menu
    $mi = find_menu_item(MENU_ADMIN, "menu-admin-manageusers", "id");
    if ($mi == null) {
        return;
    }
    $order = grab_array_var($mi, "order", "");
    $neworder = $order + 0.1;

    // Add this to the main home menu
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Notification Management"),
        "id" => "menu-admin-deploynotification",
        "order" => $neworder,
        "opts" => array(
            "href" => $urlbase . "/deploynotification.php",
            "icon" => "fa-bell"
        )
    ));
}