<?php
//
// Dashboard Deployment Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$deploydashboard_component_name = "deploydashboard";
deploydashboard_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function deploydashboard_component_init()
{
    global $deploydashboard_component_name;
    $versionok = deploydashboard_component_checkversion();

    $desc = "";
    if (!$versionok) {
        $desc = "<br><b>" . _("Error: This component requires Nagios XI 2009R1.4B or later.") . "</b>";
    }

    $args = array(
        COMPONENT_NAME => $deploydashboard_component_name,
        COMPONENT_VERSION => '1.3.2',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Allows admins to deploy dashboards to other users.") . " " . $desc,
        COMPONENT_TITLE => _("Dashboard Deployment Tool"),
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($deploydashboard_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'deploydashboard_component_addmenu');
    }
}

///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function deploydashboard_component_checkversion()
{
    // Requires XI version 2009R1.4B or higher
    if (!function_exists('get_product_release'))
        return false;
    if (get_product_release() < 126)
        return false;
    return true;
}

function deploydashboard_component_addmenu($arg = null)
{
    global $deploydashboard_component_name;
    $url = get_component_url_base($deploydashboard_component_name);
    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "linkspacer",
        "order" => 120,
        "function" => "is_admin"
    ));
    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "link",
        "title" => _("Deploy Dashboards"),
        "id" => "menu-dashboards-deploydashboards",
        "order" => 121,
        "opts" => array(
            "href" => $url,
            "icon" => "fa-clipboard"
        ),
        "function" => "is_admin"
    ));
}