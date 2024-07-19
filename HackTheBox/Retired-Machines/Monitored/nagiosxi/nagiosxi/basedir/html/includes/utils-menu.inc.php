<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


$menus = array();


////////////////////////////////////////////////////////////////////////
// MENU FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param $items
 */
function draw_menu_items($items)
{
    $html = get_menu_items_html($items);
    echo $html;
}


/**
 * @param string $menu_name
 */
function print_menu($menu_name = "")
{
    global $menus;

    // bad menu name
    if (!menu_exists($menu_name))
        return;

    // sort menu items
    sort_menu($menu_name);

    $html = get_menu_items_html($menus[$menu_name][MENUITEMS]);
    echo $html;
}


/**
 * @param $id
 * @param $expanded
 */
function set_user_menu_preferences($id, $expanded)
{
    $settings_raw = get_user_meta(0, "menu_collapse_options");
    if ($settings_raw != "")
        $settings = unserialize($settings_raw);

    $settings[$id] = $expanded;
    set_user_meta(0, "menu_collapse_options", serialize($settings), false);
}


/**
 * @param string $menu_name
 *
 * @return bool
 */
function menu_exists($menu_name = "")
{
    global $menus;

    // bad menu name
    if ($menu_name == "")
        return false;
    if (!array_key_exists($menu_name, $menus))
        return false;

    return true;
}


/**
 * @param $menu_name
 */
function add_menu($menu_name)
{
    global $menus;

    // already exists
    if (menu_exists($menu_name))
        return;

    // create new menu
    $menus[$menu_name] = array(
        MENUITEMS => array(),
    );
}


/**
 * @param $menu_name
 * @param $menu_item
 */
function add_menu_item($menu_name, $menu_item)
{
    global $menus;

    // menu doesn't exist
    if (!menu_exists($menu_name))
        return;

    $menus[$menu_name][MENUITEMS][] = $menu_item;
}


/**
 * @param        $menu_name
 * @param        $match
 * @param string $field
 *
 * @return null
 */
function find_menu_item($menu_name, $match, $field = "id")
{
    global $menus;

    // menu doesn't exist
    if (!menu_exists($menu_name))
        return null;

    foreach ($menus[$menu_name][MENUITEMS] as $index => $arr) {
        if (!array_key_exists($field, $arr))
            continue;
        if ($arr[$field] == $match)
            return $arr;
    }

    return null;
}


/**
 * @param        $menu_name
 * @param        $match
 * @param string $field
 *
 * @return bool
 */
function delete_menu_item($menu_name, $match, $field = "id")
{
    global $menus;

    // menu doesn't exist
    if (!menu_exists($menu_name))
        return false;

    foreach ($menus[$menu_name][MENUITEMS] as $index => $arr) {
        if (!array_key_exists($field, $arr))
            continue;
        if ($arr[$field] == $match) {
            unset($menus[$menu_name][MENUITEMS][$index]);
            return true;
        }
    }

    return false;
}


/**
 * @param $menu_name
 */
function sort_menu($menu_name)
{
    global $menus;

    // menu doesn't exist
    if (!menu_exists($menu_name))
        return;

    $items = $menus[$menu_name][MENUITEMS];

    //print_r($items);

    // obtain a list of sort orders
    $sortorders = array();
    foreach ($items as $index => $item) {
        // get the items sort order (default to zero if non-existent)
        $sortorder = grab_array_var($item, "order", 0);
        $sortorders[$index] = $sortorder;
    }

    // sort the items by their sortorder
    array_multisort($sortorders, SORT_ASC, $items);

    //print_r($items);

    $menus[$menu_name][MENUITEMS] = $items;
}


/**
 * @param $items
 *
 * @return string
 */
function get_menu_items_html($items)
{
    $html = "";
    foreach ($items as $item) {

        // some items should only be displayed if a function evaluates to true
        if (array_key_exists("function", $item)) {
            $f = $item["function"];
            if ($f() != true)
                continue;
        }

        $menu_id = "";
        if (array_key_exists("id", $item))
            $menu_id = $item["id"];
        $title = "";
        if (array_key_exists("title", $item))
            $title = $item["title"];
        $opts = "";
        if (array_key_exists("opts", $item))
            $opts = $item["opts"];

        // use user-defined collapse settings
        $useropts = get_user_meta(0, "menu_collapse_options");

        if ($useropts != "") {
            $useropts = unserialize($useropts);

            if (array_key_exists($menu_id, $useropts))
                $opts["expanded"] = $useropts[$menu_id];
        }

        $html .= get_menu_item_html($item["type"], $title, $opts, $menu_id);
    }
    return $html;
}


/**
 * @param        $type
 * @param        $title
 * @param        $opts
 * @param string $menu_id
 *
 * @return string
 */
function get_menu_item_html($type, $title, $opts, $menu_id = "")
{

    $html = "";

    switch ($type) {

        // html
        case "html":
            $html .= $opts["html"];
            break;

        // menu links
        case "menusection":
            $secondclass = "";
            $icon = "fa-chevron-down";
            $icon_text = _("Collapse menu section");

            if ($opts["expanded"] == false) {
                $icon = "fa-chevron-up";
                $secondclass = "menusection-collapsed";
                $icon_text = _("Show menu section");
            }

            $numhtml = "";
            if (array_key_exists("num", $opts)) {
                $numhtml = " <span class='num'>(".$opts['num'].")</span>";
            }

            if (isset($menu_id))
                $add_menu_id = "data-id='" . $menu_id . "' id='mid-" . $menu_id . "'";
            $linkopts = "";
            if (array_key_exists("linkopts", $opts))
                $linkopts = $opts["linkopts"];
            $target = "";
            if (array_key_exists("target", $opts)) {
                $t = $opts["target"];
                if (have_value($target))
                    $target = "target='" . $t . "'";
            } else
                $target = "target='maincontentframe'";

            $urla = "";
            $urlb = "";
            $url = "";
            if (array_key_exists("url", $opts))
                $url = $opts["url"];
            if (array_key_exists("href", $opts))
                $url = $opts["href"];
            if ($url != "") {
                $urla = "<a href='" . $opts["url"] . "' " . $target . " " . $linkopts . ">";
                $urlb = "</a>";
            }
            $html .= "<div class='menusection " . $secondclass . "'>";
            $html .= "<div class='menusectiontitle' " . $add_menu_id . "><i class='fa ".$icon."' title='".$icon_text."'></i> " . $urla . $title . $urlb . $numhtml . "</div>";
            $ulopts = "";
            if (array_key_exists("ulopts", $opts))
                $ulopts = $opts["ulopts"];
            $html .= "<ul class='menusection' " . $ulopts . ">";
            break;
        case "menusectionend":
            $html .= "</ul>";
            $html .= "</div>";
            break;
        case "linkspacer":
            $html .= "<li class='menulinkspacer'></li>";
            break;
        case "link":
            $xopts = "";
            foreach ($opts as $var => $val) {
                if ($var == "href") {
                    $xopts .= " " . $var . "=\"" . str_replace('&amp;', '&', encode_form_valq($val)) . "\"";
                    continue;
                }
                $xopts .= " " . $var . "=\"" . encode_form_val($val) . "\"";
            }
            // no target was specified - default it
            if (array_key_exists("target", $opts) == false) {
                $xopts .= " target=\"maincontentframe\"";
            }

            // Link image
            $img = "";
            if (array_key_exists("img", $opts)) {
                $img .= '<img src="' . encode_form_val($opts["img"]) . '">';
            }

            // Link icon
            $icon = "";
            if (array_key_exists("icon", $opts)) {
                $icon = '<span class="menu-icon"><i class="fa fa-fw ' . encode_form_val($opts['icon']) . '"></i> </span>';
            }
            
            // Add a unique link ID for a tags
            $linkid = "";
            if (!empty($menu_id)) {
                $linkid = " id='lid-" . $menu_id . "'";
            }

            $html .= "<li class='menulink'><a " . $xopts . "$linkid>" . $img . $icon . $title . "</a></li>";
            break;
        default:
            break;
    }

    return $html;
}

////////////////////////////////////////////////////////////////////////
// MENU INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function init_menus()
{
    global $menus;

    // add the menus
    add_menu(MENU_HOME);
    add_menu(MENU_VIEWS);
    add_menu(MENU_DASHBOARDS);
    add_menu(MENU_REPORTS);
    add_menu(MENU_CONFIGURE);
    add_menu(MENU_HELP);
    add_menu(MENU_ADMIN);
    add_menu(MENU_TOOLS);
    add_menu(MENU_ACCOUNT);

    // do callbacks
    // components can add top-level menus here
    do_callbacks(CALLBACK_MENUS_DEFINED, $menus);

    // initialize menus with menu items
    init_config_menu();
    init_help_menu();
    init_reports_menu();
    init_tools_menu();
    init_account_menu();
    init_admin_menu();
    init_home_menu();
    init_views_menu();
    init_dashboards_menu();

    // do callbacks
    // components can add menu items here
    do_callbacks(CALLBACK_MENUS_INITIALIZED, $menus);

    // do callbacks
    // here we might do final sorting of menus after components modified items in the callback above
    do_callbacks(CALLBACK_MENUS_INITIALIZED_FINAL, $menus);
}


function init_config_menu()
{
    $basedir = get_base_url();

    // QUICKTOOLS
    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENUSECTION,
        "title" => _("Configure"),
        "id" => "menu-configure-section-quicktools",
        "order" => 100,
        "opts" => array(
            "expanded" => true
        )
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENULINK,
        "title" => _("Configuration Options"),
        "id" => "menu-configure-confighome",
        "order" => 101,
        "opts" => array(
            "icon" => "fa-th-large",
            "href" => "main.php"
        )
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENUSECTIONEND,
        "id" => "menu-configure-sectionend-quicktools",
        "order" => 199,
        "title" => "",
        "opts" => ""
    ));

    if (!is_readonly_user() && is_authorized_to_configure_objects()) {

        // WIZARDS
        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTION,
            "title" => _("Configuration Tools"),
            "id" => "menu-configure-section-wizards",
            "order" => 200,
            "opts" => array(
                "expanded" => true
            )
        ));

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENULINK,
            "title" => _("Configuration Wizards"),
            "id" => "menu-configure-monitoringwizard",
            "order" => 201,
            "opts" => array(
                "href" => "monitoringwizard.php",
                "icon" => "fa-magic"
            )
        ));

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENULINK,
            "title" => _("Manage Templates"),
            "id" => "menu-configure-manage-templates",
            "order" => 202,
            "opts" => array(
                "href" => "managetpls.php",
                "icon" => "fa-file"
            )
        ));

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTIONEND,
            "title" => "",
            "id" => "menu-configure-sectionend-wizards",
            "order" => 299,
            "opts" => ""
        ));

    }

    if (user_can_access_autodeploy()) {

        // Auto Deployment
        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTION,
            "title" => _("Auto Deployment"),
            "id" => "menu-configure-section-autodeploy",
            "order" => 300,
            "opts" => array(
                "expanded" => true
            )
        ));

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENULINK,
            "title" => _("Deploy Agent"),
            "id" => "menu-configure-deploy",
            "order" => 301,
            "opts" => array(
                "href" => "deployment/index.php",
                "icon" => "fa-play"
            )
        ));

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENULINK,
            "title" => _("Manage Deployed Agents"),
            "id" => "menu-configure-agents",
            "order" => 302,
            "opts" => array(
                "href" => "deployment/manage.php",
                "icon" => "fa-folder"
            )
        ));

        if (is_admin()) {
            add_menu_item(MENU_CONFIGURE, array(
                "type" => MENULINK,
                "title" => _("Deployment Settings"),
                "id" => "menu-configure-settings",
                "order" => 303,
                "opts" => array(
                    "href" => "deployment/settings.php",
                    "icon" => "fa-cog"
                )
            ));
        }

        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTIONEND,
            "title" => "",
            "id" => "menu-configure-sectionend-autodeploy",
            "order" => 399,
            "opts" => ""
        ));

    }

    // Check if user can access this section
    if (user_can_access_ccm()) {

        // Advanced Configuration
        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTION,
            "title" => _("Advanced Configuration"),
            "id" => "menu-configure-section-advanced",
            "order" => 400,
            "opts" => array(
                "expanded" => true
            )
        ));
        
        add_menu_item(MENU_CONFIGURE, array(
            "type" => MENUSECTIONEND,
            "title" => "",
            "id" => "menu-configure-sectionend-advanced",
            "order" => 499,
            "opts" => ""
        ));

    }

    // MORE OPTIONS
    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENUSECTION,
        "title" => _("More Options"),
        "id" => "menu-configure-section-moreoptions",
        "order" => 900,
        "opts" => array(
            "id" => "myaccountquickview",
            "expanded" => true,
        )
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENULINK,
        "title" => _("My Account Settings"),
        "id" => "menu-configure-myaccountsettings",
        "order" => 901,
        "opts" => array(
            "icon" => "fa-share",
            "href" => $basedir . "account/",
            "target" => "_top"
        )
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENULINK,
        "title" => _("System Configuration"),
        "id" => "menu-configure-systemconfig",
        "order" => 902,
        "opts" => array(
            "icon" => "fa-share",
            "href" => $basedir . "admin/?xiwindow=globalconfig.php",
            "target" => "_top"
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENULINK,
        "title" => _("User Management"),
        "id" => "menu-configure-usermanagement",
        "order" => 903,
        "opts" => array(
            "icon" => "fa-share",
            "href" => $basedir . "admin/?xiwindow=users.php",
            "target" => "_top"
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENULINK,
        "title" => _("Unconfigured Objects"),
        "id" => "menu-configure-unconfiguredobjects",
        "order" => 904,
        "opts" => array(
            "icon" => "fa-share",
            "href" => $basedir . "admin/?xiwindow=missingobjects.php",
            "target" => "_top"
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => "link",
        "title" => _("Deadpool Settings"),
        "id" => "menu-admin-deadpool",
        "order" => 905,
        "opts" => array(
            "icon" => "fa-share",
            "href" => $basedir . "admin/?xiwindow=deadpool.php",
            "target" => "_top"
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_CONFIGURE, array(
        "type" => MENUSECTIONEND,
        "title" => "",
        "id" => "menu-configure-sectionend-moreoptions",
        "order" => 999,
        "opts" => ""
    ));


}

function init_help_menu()
{
    // Nagios XI Help
    // --------------------------------

    if (get_user_attr(0, 'api_enabled', 0) || is_admin()) {

        add_menu_item(MENU_HELP, array(
            "type" => "menusection",
            "title" => _('API Docs'),
            "id" => "menu-help-nagiosxi-start",
            "order" => 100,
            "opts" => array(
                "id" => "int-help",
                "expanded" => true
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Introduction"),
            "id" => "menu-help-api",
            "order" => 101,
            "opts" => array(
                "href" => "api.php"
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Objects Reference"),
            "id" => "menu-help-api-objects",
            "order" => 102,
            "opts" => array(
                "href" => "api-object-reference.php"
            )
        ));

    }

    if (is_admin()) {

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Config Reference"),
            "id" => "menu-help-api-config",
            "order" => 103,
            "opts" => array(
                "href" => "api-config-reference.php"
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("System Reference"),
            "id" => "menu-help-api-system",
            "order" => 104,
            "opts" => array(
                "href" => "api-system-reference.php"
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Common Solutions"),
            "id" => "menu-help-api-common-solutions",
            "order" => 105,
            "opts" => array(
                "href" => "api-common-solutions.php"
            )
        ));

    }

    /*
    // Wait for 5.x.0
    add_menu_item(MENU_HELP, array(
        "type" => MENULINK,
        "title" => _("Shortcuts &amp; Hotkeys"),
        "id" => "menu-help-shortcuts",
        "order" => 102,
        "opts" => array(
            "href" => "shortcuts.php",
        )
    ));
    */

    if (get_user_attr(0, 'api_enabled', 0) || is_admin()) {

        add_menu_item(MENU_HELP, array(
            "type" => "menusectionend",
            "title" => "",
            "id" => "menu-help-nagiosxi-end",
            "order" => 110,
            "opts" => ""
        ));

    }

    // Developer Docs
    // -------------------------------

    if (is_admin()) {

        add_menu_item(MENU_HELP, array(
            "type" => "menusection",
            "title" => _('Developer Docs'),
            "id" => "menu-help-dev-start",
            "order" => 111,
            "opts" => array(
                "id" => "int-dev",
                "expanded" => true
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Auth Tokens"),
            "id" => "menu-help-auth-tokens",
            "order" => 112,
            "opts" => array(
                "href" => "auth-token-reference.php",
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Custom API Endpoints"),
            "id" => "menu-help-customapi",
            "order" => 113,
            "opts" => array(
                "href" => "custom-api-endpoints.php",
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Callback Reference"),
            "id" => "menu-help-callbacks",
            "order" => 114,
            "opts" => array(
                "href" => "callback-reference.php",
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => MENULINK,
            "title" => _("Translations"),
            "id" => "menu-help-translations",
            "order" => 115,
            "opts" => array(
                "href" => "translations.php",
            )
        ));

        add_menu_item(MENU_HELP, array(
            "type" => "menusectionend",
            "title" => "",
            "id" => "menu-help-dev-end",
            "order" => 120,
            "opts" => ""
        ));

    }

    // External Resources
    // --------------------------------

    add_menu_item(MENU_HELP, array(
        "type" => "menusection",
        "title" => _("External Help Resources"),
        "id" => "menu-help-section-resources",
        "order" => 200,
        "opts" => array(
            "id" => "help",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("Knowledgebase"),
        "id" => "menu-help-kb",
        "order" => 201,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://support.nagios.com/kb/category.php?id=2",
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("FAQs"),
        "id" => "menu-help-faqs",
        "order" => 202,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://support.nagios.com/kb/category.php?id=44",
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("Support Forum"),
        "id" => "menu-help-supportforum",
        "order" => 203,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://support.nagios.com/forum",
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("Nagios Library"),
        "id" => "menu-help-library",
        "order" => 204,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://library.nagios.com/",
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-help-sectionend-resources",
        "order" => 205,
        "opts" => ""
    ));

    // Documentation Guides
    // --------------------------------

    add_menu_item(MENU_HELP, array(
        "type" => "menusection",
        "title" => _("Documentation Guides"),
        "id" => "menu-help-guides",
        "order" => 300,
        "opts" => array(
            "id" => "guides",
            "expanded" => true,
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("Administrator Guide"),
        "id" => "menu-help-adminguide",
        "order" => 301,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://assets.nagios.com/downloads/nagiosxi/guides/administrator",
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "link",
        "title" => _("User Guide"),
        "id" => "menu-help-userguide",
        "order" => 302,
        "opts" => array(
            "icon" => "fa-external-link",
            "href" => "https://assets.nagios.com/downloads/nagiosxi/guides/user",
        )
    ));

    add_menu_item(MENU_HELP, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-help-sectionend-resources",
        "order" => 304,
        "opts" => ""
    ));

}


function init_reports_menu()
{
    $xireports_path = get_base_url() . "reports/";

    if (function_exists("nagioscore_get_ui_url")) {
        $nagioscoreui_path = nagioscore_get_ui_url();
    } else {
        $nagioscoreui_path = "";
    }

    $mr = get_myreports(0);
    $num = count($mr);

    // My reports
    add_menu_item(MENU_REPORTS, array(
        "type" => "menusection",
        "title" => _("My Reports"),
        "id" => "menu-reports-myreports",
        "order" => 100,
        "opts" => array(
            "id" => "myreports",
            "expanded" => true,
            "num" => $num,
            "url" => "myreports.php",
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "menusectionend",
        "id" => "menu-reports-sectionend-myreports",
        "order" => 199,
        "title" => "",
        "opts" => ""
    ));


    // new xi reports
    add_menu_item(MENU_REPORTS, array(
        "type" => "menusection",
        "title" => _("Available Reports"),
        "id" => "menu-reports-nagiosxi",
        "order" => 300,
        "opts" => array(
            "id" => "nagiosxireports",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Availability"),
        "id" => "menu-reports-nagiosxi-availablity",
        "order" => 309,
        "opts" => array(
            "href" => $xireports_path . "availability.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Executive Summary"),
        "id" => "menu-reports-nagiosxi-execsummary",
        "order" => 310,
        "opts" => array(
            "href" => $xireports_path . "execsummary.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("SLA Report"),
        "id" => "menu-reports-nagiosxi-sla",
        "order" => 311,
        "opts" => array(
            "href" => $xireports_path . "sla.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("State History"),
        "id" => "menu-reports-nagiosxi-statehistory",
        "order" => 312,
        "opts" => array(
            "href" => $xireports_path . "statehistory.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Top Alert Producers"),
        "id" => "menu-reports-nagiosxi-topalertproducers",
        "order" => 313,
        "opts" => array(
            "href" => $xireports_path . "topalertproducers.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Alert Histogram"),
        "id" => "menu-reports-nagiosxi-histogram",
        "order" => 314,
        "opts" => array(
            "href" => $xireports_path . "histogram.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Notifications"),
        "id" => "menu-reports-nagiosxi-notifications",
        "order" => 315,
        "opts" => array(
            "href" => $xireports_path . "notifications.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Event Log"),
        "id" => "menu-reports-nagiosxi-eventlog",
        "order" => 316,
        "opts" => array(
            "href" => $xireports_path . "eventlog.php"
        ),
        "function" => "is_authorized_for_monitoring_system",
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "menusectionend",
        "id" => "menu-reports-sectionend-nagiosxi",
        "order" => 399,
        "title" => "",
        "opts" => ""
    ));


    // visualization
    add_menu_item(MENU_REPORTS, array(
        "type" => "menusection",
        "title" => _("Data Visualizations"),
        "id" => "menu-reports-visualization",
        "order" => 400,
        "opts" => array(
            "id" => "visualizations",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Alert Heatmap"),
        "id" => "menu-reports-visualization-alertheatmap",
        "order" => 401,
        "opts" => array(
            "href" => $xireports_path . "alertheatmap.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "menusectionend",
        "id" => "menu-reports-sectionend-visualization",
        "order" => 499,
        "title" => "",
        "opts" => ""
    ));


    // Show admin-only management section

    if (is_admin()) {

        add_menu_item(MENU_REPORTS, array(
            "type" => "menusection",
            "title" => _("Report Management"),
            "id" => "menu-reports-manage",
            "order" => 500,
            "opts" => array(
                "expanded" => true
            )
        ));

        add_menu_item(MENU_REPORTS, array(
            "type" => "menusectionend",
            "id" => "menu-reports-end-manage",
            "order" => 519
        ));

    }


    // Legacy (Core) reports
    
    add_menu_item(MENU_REPORTS, array(
        "type" => "menusection",
        "title" => _("Legacy Reports"),
        "id" => "menu-reports-nagioscore",
        "order" => 600,
        "opts" => array(
            "id" => "nagioscorereports",
            "expanded" => false
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Availability"),
        "id" => "menu-reports-nagioscore-availability",
        "order" => 601,
        "opts" => array(
            "href" => $nagioscoreui_path . "avail.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Trends"),
        "id" => "menu-reports-nagioscore-trends",
        "order" => 602,
        "opts" => array(
            "href" => $nagioscoreui_path . "trends.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Alert History"),
        "id" => "menu-reports-nagioscore-alerthistory",
        "order" => 603,
        "opts" => array(
            "href" => $nagioscoreui_path . "history.php?host=all"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Alert Summary"),
        "id" => "menu-reports-nagioscore-alertsummary",
        "order" => 604,
        "opts" => array(
            "href" => $nagioscoreui_path . "summary.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Alert Histogram"),
        "id" => "menu-reports-nagioscore-alerthistograms",
        "order" => 605,
        "opts" => array(
            "href" => $nagioscoreui_path . "histogram.php"
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Notifications"),
        "id" => "menu-reports-nagioscore-notifications",
        "order" => 606,
        "opts" => array(
            "href" => $nagioscoreui_path . "notifications.php?contact=all"
        )
    ));
    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Event Log"),
        "id" => "menu-reports-nagioscore-eventlog",
        "order" => 607,
        "opts" => array(
            "href" => $nagioscoreui_path . "showlog.php"
        ),
        "function" => "is_authorized_for_monitoring_system",

    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "link",
        "title" => _("Network Status Map"),
        "id" => "menu-reports-nagioscore-networkstatusmap",
        "order" => 608,
        "opts" => array(
            "href" => get_statusmap_link()
        )
    ));

    add_menu_item(MENU_REPORTS, array(
        "type" => "menusectionend",
        "id" => "menu-reports-sectionend-nagioscore",
        "order" => 610,
        "title" => "",
        "opts" => ""
    ));

}


function init_tools_menu()
{


    add_menu_item(MENU_TOOLS, array(
        "type" => "menusection",
        "title" => _("My Tools"),
        "id" => "menu-tools-mytools",
        "order" => 100,
        "opts" => array(
            "id" => "mytools",
            "expanded" => true,
            "url" => "mytools.php",
        )
    ));


    add_menu_item(MENU_TOOLS, array(
        "type" => "menusectionend",
        "id" => "menu-tools-sectionend-mytools",
        "order" => 199,
        "title" => "",
        "opts" => ""
    ));

    add_menu_item(MENU_TOOLS, array(
        "type" => "menusection",
        "title" => _("Common Tools"),
        "id" => "menu-tools-commontools",
        "order" => 200,
        "opts" => array(
            "id" => "mytools",
            "expanded" => true,
            "url" => "commontools.php",
        )
    ));


    add_menu_item(MENU_TOOLS, array(
        "type" => "menusectionend",
        "id" => "menu-tools-sectionend-commontools",
        "order" => 299,
        "title" => "",
        "opts" => ""
    ));

}


function init_account_menu()
{

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "menusection",
        "title" => _("My Account"),
        "id" => "menu-account-section-myaccount",
        "order" => 100,
        "opts" => array(
            "id" => "myaccountquickview",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("Account Information"),
        "id" => "menu-account-accountinfo",
        "order" => 101,
        "opts" => array(
            "href" => "main.php",
            "icon" => "fa-user"
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("User Sessions"),
        "id" => "menu-account-user-sessions",
        "order" => 102,
        "opts" => array(
            "href" => "sessions.php",
            "icon" => "fa-asterisk"
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-account-sectionend-myaccount",
        "order" => 110,
        "opts" => ""
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "menusection",
        "title" => _("Notification Options"),
        "id" => "menu-account-section-notificationoptions",
        "order" => 200,
        "opts" => array(
            "id" => "notificationoptions",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("Notification Preferences"),
        "id" => "menu-account-notificationpreferences",
        "order" => 201,
        "opts" => array(
            "href" => "notifyprefs.php",
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "linkspacer",
        "order" => 202,
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("Notification Methods"),
        "id" => "menu-account-notificationmethods",
        "order" => 210,
        "opts" => array(
            "href" => "notifymethods.php",
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("Notification Messages"),
        "id" => "menu-account-notificationmessages",
        "order" => 211,
        "opts" => array(
            "href" => "notifymsgs.php",
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "linkspacer",
        "order" => 212,
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "link",
        "title" => _("Send Test Notifications"),
        "id" => "menu-account-testnotifications",
        "order" => 220,
        "opts" => array(
            "href" => "testnotification.php",
        )
    ));

    add_menu_item(MENU_ACCOUNT, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-account-sectionend-notificationoptions",
        "order" => 221,
        "opts" => ""
    ));

}


function init_admin_menu()
{
    global $cfg;
    $base_url = get_base_url();

    // System
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("System Information"),
        "id" => "menu-admin-section-systemstatus",
        "order" => 200,
        "opts" => array(
            "id" => "systemstatus",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("System Status"),
        "id" => "menu-admin-systemstatus",
        "order" => 201,
        "opts" => array(
            "icon" => "fa-pie-chart",
            "href" => "sysstat.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Monitoring Engine Status"),
        "id" => "menu-admin-monitoringenginestatus",
        "order" => 202,
        "opts" => array(
            "icon" => "fa-dashboard",
            "href" => "sysstat.php?pageopt=monitoringengine"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Audit Log"),
        "id" => "menu-admin-auditlog",
        "order" => 203,
        "opts" => array(
            "icon" => "fa-shield",
            "href" => "auditlog.php"
        )
    ));

    // Check for custom branding and if they can do updates
    $hide_updates = false;
    if (custom_branding()) {
        if ($bcfg['hide_updates']) {
            $hide_updates = true;
        }
    }

    if (!is_v2_license() && !$hide_updates) {

        add_menu_item(MENU_ADMIN, array(
            "type" => "link",
            "title" => _("Check For Updates"),
            "id" => "menu-admin-checkforupdates",
            "order" => 204,
            "opts" => array(
                "icon" => "fa-arrow-circle-up",
                "href" => "updates.php"
            )
        ));
    }

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "id" => "menu-admin-sectionend-systemstatus",
        "order" => 299,
        "title" => "",
        "opts" => ""
    ));


    // Users
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("Users"),
        "id" => "menu-admin-section-users",
        "order" => 300,
        "opts" => array(
            "id" => "users",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Users"),
        "id" => "menu-admin-manageusers",
        "order" => 301,
        "opts" => array(
            "icon" => "fa-user",
            "href" => "users.php",
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("User Sessions"),
        "id" => "menu-admin-users-sessions",
        "order" => 302,
        "opts" => array(
            "icon" => "fa-asterisk",
            "href" => "sessions.php",
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "id" => "menu-admin-sectionend-users",
        "order" => 399,
        "title" => "",
        "opts" => ""
    ));


    // System Configuration
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("System Config"),
        "id" => "menu-admin-section-systemconfig",
        "order" => 400,
        "opts" => array(
            "id" => "systemconfig",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("System Settings"),
        "id" => "menu-admin-managesystemconfig",
        "order" => 401,
        "opts" => array(
            "icon" => "fa-cog",
            "href" => "globalconfig.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("License Information"),
        "id" => "menu-admin-licenseinformation",
        "order" => 401.1,
        "opts" => array(
            "icon" => "fa-bookmark",
            "href" => "license.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Email Settings"),
        "id" => "menu-admin-manageemailsettings",
        "order" => 403,
        "opts" => array(
            "icon" => "fa-envelope",
            "href" => "mailsettings.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Mobile Carriers"),
        "id" => "menu-admin-managemobilecarriers",
        "order" => 404,
        "opts" => array(
            "icon" => "fa-phone",
            "href" => "mobilecarriers.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Performance Settings"),
        "id" => "menu-admin-performance",
        "order" => 405,
        "opts" => array(
            "icon" => "fa-sliders",
            "href" => "performance.php"
        )
    ));
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Announcement Banners"),
        "id" => "menu-admin-banner-message",
        "order" => 405.1,
        "opts" => array(
            "icon" => "fa-comment-o",
            "href" => "banner_message.php"
        )
    ));

    if (!is_v2_license()) {
        add_menu_item(MENU_ADMIN, array(
            "type" => "link",
            "title" => _("Automatic Login"),
            "id" => "menu-admin-autologin",
            "order" => 406,
            "opts" => array(
                "icon" => "fa-send-o",
                "href" => "autologin.php"
            )
        ));

        $xisys = $cfg['root_dir'] . '/var/xi-sys.cfg';
        $ini = parse_ini_file($xisys);
        if ($ini['dist'] != "el9") {
            add_menu_item(MENU_ADMIN, array(
                "type" => "link",
                "title" => _("SSH Terminal"),
                "id" => "menu-admin-sshterm",
                "order" => 408,
                "opts" => array(
                    "icon" => "fa-terminal",
                    "href" => "sshterm.php"
                )
            ));
        }
    }

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-admin-sectionend-systemconfig",
        "order" => 499,
        "opts" => ""
    ));

    // Monitoring Configuration
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("Monitoring Config"),
        "id" => "menu-admin-section-monitoringconfig",
        "order" => 500,
        "opts" => array(
            "id" => "monitoringconfig",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Config Snapshots"),
        "id" => "menu-admin-configsnapshots",
        "order" => 501,
        "opts" => array(
            "icon" => "fa-hdd-o",
            "href" => "coreconfigsnapshots.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Migrate Server"),
        "id" => "menu-admin-migrate",
        "order" => 501.5,
        "opts" => array(
            "icon" => "fa-exchange",
            "href" => "migrate.php"
        )
    ));

    if (!is_v2_license()) {
        add_menu_item(MENU_ADMIN, array(
            "type" => "link",
            "title" => _("Check File Permissions"),
            "id" => "menu-admin-configperms",
            "order" => 502,
            "opts" => array(
                "icon" => "fa-tag",
                "href" => "configpermscheck.php"
            )
        ));
    }

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Unconfigured Objects"),
        "id" => "menu-admin-missingobjects",
        "order" => 503,
        "opts" => array(
            "icon" => "fa-circle-o",
            "href" => "missingobjects.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Deadpool Settings"),
        "id" => "menu-admin-deadpool",
        "order" => 504,
        "opts" => array(
            "icon" => "fa-heartbeat",
            "href" => "deadpool.php"
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "linkspacer",
        "order" => 590
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-admin-sectionend-monitoringconfig",
        "order" => 599,
        "opts" => ""
    ));

    // Data Transfer
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("Check Transfers"),
        "id" => "menu-admin-section-datatransfer",
        "order" => 600,
        "opts" => array(
            "id" => "datatransfer",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Outbound Transfers"),
        "id" => "menu-admin-datatransfer-outbound",
        "order" => 650,
        "opts" => array(
            "icon" => "fa-sign-out",
            "href" => "dtoutbound.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Inbound Transfers"),
        "id" => "menu-admin-datatransfer-inbound",
        "order" => 651,
        "opts" => array(
            "icon" => "fa-sign-in fa-flip-horizontal",
            "href" => "dtinbound.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-admin-sectionend-datatransfer",
        "order" => 699,
        "opts" => ""
    ));

    // System Extensions
    add_menu_item(MENU_ADMIN, array(
        "type" => "menusection",
        "title" => _("System Extensions"),
        "id" => "menu-admin-section-systemextensions",
        "order" => 700,
        "opts" => array(
            "id" => "systemextensions",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Components"),
        "id" => "menu-admin-managecomponents",
        "order" => 701,
        "opts" => array(
            "icon" => "fa-cube",
            "href" => "components.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Config Wizards"),
        "id" => "menu-admin-managewizards",
        "order" => 702,
        "opts" => array(
            "icon" => "fa-magic",
            "href" => "configwizards.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Dashlets"),
        "id" => "menu-admin-managedashlets",
        "order" => 703,
        "opts" => array(
            "icon" => "fa-folder-o",
            "href" => "dashlets.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Plugins"),
        "id" => "menu-admin-manageplugins",
        "order" => 704,
        "opts" => array(
            "icon" => "fa-plug",
            "href" => "monitoringplugins.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage Graph Templates"),
        "id" => "menu-admin-graphtemplates",
        "order" => 705,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => "graphtemplates.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => _("Manage MIBs"),
        "id" => "menu-admin-managemibs",
        "order" => 706,
        "opts" => array(
            "icon" => "fa-sticky-note-o",
            "href" => "mibs.php"
        )
    ));

    add_menu_item(MENU_ADMIN, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-admin-sectionend-systemextensions",
        "order" => 710,
        "opts" => ""
    ));

}

function init_home_menu()
{

    $base_path = get_base_url();
    $includes_path = $base_path . "includes/";
    $components_path = $includes_path . "components/";

    $xistatus_path = $components_path . "xicore/status.php";
    $xireports_path = $base_path . "reports/";
    $xicomponent_path = $components_path . "xicore/";

    if (function_exists("nagioscore_get_ui_url"))
        $nagioscoreui_path = nagioscore_get_ui_url();
    else
        $nagioscoreui_path = "";


    // Quick View
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Quick View"),
        "id" => "menu-home-section-quickview",
        "order" => 100,
        "opts" => array(
            "id" => "quickview",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Home Dashboard"),
        "id" => "menu-home-homedashboard",
        "order" => 101,
        "opts" => array(
            "href" => get_base_url() . "includes/page-home-main.php"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 102,
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Tactical Overview"),
        "id" => "menu-home-tacticaloverview",
        "order" => 111,
        "opts" => array(
            "href" => $xicomponent_path . "tac.php"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 120,
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Open Service Problems"),
        "id" => "menu-home-openserviceproblems",
        "order" => 130,
        "opts" => array(
            "href" => $xistatus_path . "?show=services&hostattr=" . (HOSTSTATUSATTR_NOTACKNOWLEDGED | HOSTSTATUSATTR_NOTINDOWNTIME) . "&hoststatustypes=" . HOSTSTATE_UP . "&servicestatustypes=" . (SERVICESTATE_WARNING | SERVICESTATE_UNKNOWN | SERVICESTATE_CRITICAL) . "&serviceattr=" . (SERVICESTATUSATTR_NOTACKNOWLEDGED | SERVICESTATUSATTR_NOTINDOWNTIME)
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Open Host Problems"),
        "id" => "menu-home-openhostproblems",
        "order" => 131,
        "opts" => array(
            "href" => $xistatus_path . "?show=hosts&hoststatustypes=" . (HOSTSTATE_DOWN | HOSTSTATE_UNREACHABLE) . "&hostattr=" . (HOSTSTATUSATTR_NOTACKNOWLEDGED | HOSTSTATUSATTR_NOTINDOWNTIME)
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 140,
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("All Service Problems"),
        "id" => "menu-home-allserviceproblems",
        "order" => 150,
        "opts" => array(
            "href" => $xistatus_path . "?show=services&servicestatustypes=" . (SERVICESTATE_WARNING | SERVICESTATE_UNKNOWN | SERVICESTATE_CRITICAL)
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("All Host Problems"),
        "id" => "menu-home-allhostproblems",
        "order" => 151,
        "opts" => array(
            "href" => $xistatus_path . "?show=hosts&hoststatustypes=" . (HOSTSTATE_DOWN | HOSTSTATE_UNREACHABLE)
        )
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 160,
        "function" => "is_authorized_for_all_objects",
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Network Outages"),
        "id" => "menu-home-networkoutages",
        "order" => 161,
        "opts" => array(
            "href" => get_network_outages_link(),
            "icon" => "fa-flag"
        ),
        "function" => "is_authorized_for_all_objects",
    ));

    // Remove quick find in 2014 - it's moved to the navbar!
    if (get_theme() == "classic") {

        add_menu_item(MENU_HOME, array(
            "type" => "linkspacer",
            "order" => 170,
        ));
        add_menu_item(MENU_HOME, array(
            "type" => "linkspacer",
            "order" => 171,
        ));

        add_menu_item(MENU_HOME, array(
            "type" => "html",
            "title" => _("Quick Find"),
            "id" => "menu-home-quickfind",
            "order" => 180,
            "opts" => array(
                "html" => '
                <li>
                <div>
                <form method="post" target="maincontentframe" action="' . $xistatus_path . "?show=services" . '">
                <input type="hidden" name="navbarsearch" value="1" />
                <label for="navbarSearchBox">' . _("Quick Find") . ':</label><br class="nobr" />
                <input type="text" size="12" name="search" id="navbarSearchBox" value="" placeholder="'._('Search').'..." />
                <input type="submit" class="submitbutton" name="searchButton" value="' . _("Go") . '" id="searchButton" style="margin: 0px;">
                </form>
                </div>
                </li>'
            )
        ));
    }

    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-home-sectionend-quickview",
        "order" => 181,
        "opts" => ""
    ));

    // Detail
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Details"),
        "id" => "menu-home-section-details",
        "order" => 200,
        "opts" => array(
            "id" => "statusdetails",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Service Status"),
        "id" => "menu-home-servicestatus",
        "order" => 201,
        "opts" => array(
            "href" => $xistatus_path . "?show=services"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Host Status"),
        "id" => "menu-home-hoststatus",
        "order" => 202,
        "opts" => array(
            "href" => $xistatus_path . "?show=hosts"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 203,
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Hostgroup Summary"),
        "id" => "menu-home-hostgroupsummary",
        "order" => 210,
        "opts" => array(
            "href" => get_hostgroup_status_link("all", "summary")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Hostgroup Overview"),
        "id" => "menu-home-hostgroupoverview",
        "order" => 211,
        "opts" => array(
            "href" => get_hostgroup_status_link("all", "overview")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Hostgroup Grid"),
        "id" => "menu-home-hostgroupgrid",
        "order" => 212,
        "opts" => array(
            "href" => get_hostgroup_status_link("all", "grid")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "order" => 213,
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Servicegroup Summary"),
        "id" => "menu-home-servicegroupsummary",
        "order" => 220,
        "opts" => array(
            "href" => get_servicegroup_status_link("all", "summary")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Servicegroup Overview"),
        "id" => "menu-home-servicegroupoverview",
        "order" => 221,
        "opts" => array(
            "href" => get_servicegroup_status_link("all", "overview")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Servicegroup Grid"),
        "id" => "menu-home-servicegroupgrid",
        "order" => 222,
        "opts" => array(
            "href" => get_servicegroup_status_link("all", "grid")
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "id" => "menu-home-sectionend-details",
        "order" => 230,
        "title" => "",
        "opts" => ""
    ));

    // Graphs
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Graphs"),
        "id" => "menu-home-section-graphs",
        "order" => 300,
        "opts" => array(
            "id" => "perfgraphs",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Performance Graphs"),
        "id" => "menu-home-performance-graphs",
        "order" => 301,
        "opts" => array(
            "href" => $base_path . "perfgraphs/",
            "icon" => 'fa-line-chart'
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-home-sectionend-graphs",
        "order" => 319,
        "opts" => ""
    ));


    // Maps
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Maps"),
        "id" => "menu-home-section-maps",
        "order" => 400,
        "opts" => array(
            "id" => "maps",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-home-sectionend-maps",
        "order" => 420,
        "opts" => ""
    ));


    // Incident Management
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Incident Management"),
        "id" => "menu-home-section-incident-management",
        "order" => 500,
        "opts" => array(
            "id" => "incidentmanagement",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Acknowledgements"),
        "id" => "menu-home-acknowledgements",
        "order" => 501,
        "opts" => array(
            "href" => $xistatus_path . "?show=comments"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Scheduled Downtime"),
        "id" => "menu-home-scheduleddowntime",
        "order" => 501,
        "opts" => array(
            "href" => $base_path . "includes/components/xicore/downtime.php"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Recurring Downtime"),
        "id" => "menu-home-recurringdowntime",
        "order" => 503,
        "opts" => array(
            "href" => $xicomponent_path . "recurringdowntime.php",
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Notifications"),
        "id" => "menu-home-notifications",
        "order" => 504,
        "opts" => array(
            "href" => $base_path . "reports/notifications.php"
        )
    ));
    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-home-sectionend-incidentmanagement",
        "order" => 510,
        "opts" => ""
    ));


    // Process Info
    add_menu_item(MENU_HOME, array(
        "type" => "menusection",
        "title" => _("Monitoring Process"),
        "id" => "menu-home-section-monitoringprocess",
        "order" => 600,
        "opts" => array(
            "id" => "system",
            "expanded" => true
        ),
        "function" => "is_authorized_for_monitoring_system"
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Process Info"),
        "id" => "menu-home-processinfo",
        "order" => 601,
        "opts" => array(
            "href" => $xistatus_path . "?show=process"
        ),
        "function" => "is_authorized_for_monitoring_system"
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Performance"),
        "id" => "menu-home-performance",
        "order" => 602,
        "opts" => array(
            "href" => $xistatus_path . "?show=performance"
        ),
        "function" => "is_authorized_for_monitoring_system"
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Scheduling Queue"),
        "id" => "menu-home-scheduling-queue",
        "order" => 603,
        "opts" => array(
            "href" => $components_path . "xicore/queue.php"
        ),
        "function" => "is_authorized_for_monitoring_system"
    ));

    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Event Log"),
        "id" => "menu-home-eventlog",
        "order" => 604,
        "opts" => array(
            "href" => $xireports_path . "eventlog.php"
        ),
        "function" => "is_authorized_for_monitoring_system",

    ));
    add_menu_item(MENU_HOME, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-home-sectionend-monitoringprocess",
        "order" => 610,
        "opts" => "",
        "function" => "is_authorized_for_monitoring_system",
    ));
}

function init_views_menu()
{
    // Quick View
    add_menu_item(MENU_VIEWS, array(
        "type" => "menusection",
        "title" => _("View Tools"),
        "id" => "menu-views-section-viewtools",
        "order" => 100,
        "opts" => array(
            "id" => "myviewsquickview",
            "expanded" => true
        )
    ));
    add_menu_item(MENU_VIEWS, array(
        "type" => "link",
        "title" => _("Start Rotation"),
        "additional_title" => _("Stop Rotation"),
        "id" => "menu-views-rotateviews",
        "order" => 101,
        "opts" => array(
            "href" => "#",
            "class" => "rotatemyviewslink",
            "target" => "",
            "icon" => "fa-play"
        )
    ));
    add_menu_item(MENU_VIEWS, array(
        "type" => "linkspacer",
        "order" => 102,
    ));
    add_menu_item(MENU_VIEWS, array(
        "type" => "link",
        "title" => _("New View"),
        "id" => "menu-views-addnewview",
        "order" => 103,
        "opts" => array(
            "icon" => "fa-plus",
            "href" => "#",
            "class" => "addnewviewlink",
            "target" => ""
        )
    ));
    add_menu_item(MENU_VIEWS, array(
        "type" => "link",
        "title" => _("Manage My Views"),
        "id" => "menu-views-manage",
        "order" => 104,
        "opts" => array(
            "icon" => "fa-tv",
            "class" => "manage-views",
            "href" => "manage.php"
        )
    ));
    add_menu_item(MENU_VIEWS, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-views-sectionend-viewtools",
        "order" => 105,
        "opts" => ""
    ));

    // My Views
    add_menu_item(MENU_VIEWS, array(
        "type" => "menusection",
        "title" => _("My Views"),
        "id" => "menu-views-section-myviews",
        "order" => 200,
        "opts" => array(
            "id" => "myviews",
            "expanded" => true,
            "ulopts" => "id='myviewsmenu'",
        )
    ));

    add_menu_item(MENU_VIEWS, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-views-sectionend",
        "order" => 299,
        "opts" => ""
    ));

}

function init_dashboards_menu()
{
    // Quick View
    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusection",
        "title" => _("Dashboard Tools"),
        "id" => "menu-dashboards-section-tools",
        "order" => 100,
        "opts" => array(
            "id" => "mydashboardsquickview",
            "expanded" => true
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "linkspacer",
        "order" => 101,
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "link",
        "title" => _("New Dashboard"),
        "id" => "menu-dashboards-adddashboard",
        "order" => 110,
        "opts" => array(
            "icon" => "fa-plus",
            "href" => "#",
            "class" => "addnewdashboardlink",
            "target" => ""
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "link",
        "title" => _("Manage My Dashboards"),
        "id" => "menu-dashboards-manage",
        "order" => 111,
        "opts" => array(
            "icon" => "fa-window-maximize",
            "href" => "manage.php"
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-dashboards-sectionend-tools",
        "order" => 199,
        "opts" => ""
    ));

    // My Dashboards
    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusection",
        "title" => _("My Dashboards"),
        "id" => "menu-dashboards-section-mydashboards",
        "order" => 200,
        "opts" => array(
            "id" => "mydashboards",
            "expanded" => true,
            "ulopts" => "id='mydashboardsmenu'"
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-dashboards-sectionend-mydashboards",
        "order" => 299,
        "opts" => ""
    ));


    // Dashlets
    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusection",
        "title" => _("Add Dashlets"),
        "id" => "menu-dashboards-section-dashlets",
        "order" => 300,
        "opts" => array(
            "id" => "dashlets",
            "expanded" => true,
            "ulopts" => "id='dashletsmenu'"
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "link",
        "title" => _("Available Dashlets"),
        "id" => "menu-dashboards-availabledashlets",
        "order" => 301,
        "opts" => array(
            "icon" => "fa-window-restore",
            "href" => "dashlets.php",
        )
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "linkspacer",
        "order" => 302,
        "function" => "is_admin",
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "link",
        "title" => _("Manage Dashlets"),
        "id" => "menu-dashboards-managedashlets",
        "order" => 303,
        "opts" => array(
            "icon" => "fa-share",
            "href" => get_base_url() . "admin/?xiwindow=dashlets.php",
            "target" => "_top",
        ),
        "function" => "is_admin",
    ));

    add_menu_item(MENU_DASHBOARDS, array(
        "type" => "menusectionend",
        "title" => "",
        "id" => "menu-dashboards-sectionend-dashlets",
        "order" => 304,
        "opts" => ""
    ));

}

