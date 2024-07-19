<?php
//
// System Profile Component
// Copyright (c) 2010-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$profile_component_name = "profile";
profile_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function profile_component_init()
{
    global $profile_component_name;

    $versionok = profile_component_checkversion();

    $desc = _("This component creates a system profile menu in the Admin panel which can be used for troubleshooting purposes.");

    if (!$versionok) {
        $desc = "<b>" . _("Error: This component requires Nagios XI 20011R1.1 or later.") . "</b>";
    }

    $args = array(
        COMPONENT_NAME => $profile_component_name,
        COMPONENT_VERSION => '1.4.1',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => _("System Profile"),
        COMPONENT_TYPE => COMPONENT_TYPE_CORE,
        COMPONENT_REQUIRES_VERSION => 500
    );

    register_component($profile_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'profile_component_addmenu');
    }
}

///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function profile_component_checkversion()
{
    if (!function_exists('get_product_release'))
        return false;
    if (get_product_release() < 201)
        return false;
    return true;
}

function profile_component_addmenu($arg = null)
{
    global $profile_component_name;
    $urlbase = get_component_url_base($profile_component_name);

    $mi = find_menu_item(MENU_ADMIN, "menu-admin-managesystemconfig", "id");
    if ($mi == null) {
        return;
    }

    $order = grab_array_var($mi, "order", "");
    if ($order == "") {
        return;
    }

    $neworder = $order + 0.1;

    // Add this to the main home menu
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("System Profile"),
        "id" => "menu-admin-profile",
        "order" => $neworder,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => $urlbase . "/profile.php"
        )
    ));
}
