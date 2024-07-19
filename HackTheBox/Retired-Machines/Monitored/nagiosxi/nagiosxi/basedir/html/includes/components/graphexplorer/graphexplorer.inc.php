<?php
//
// Graph Explorer Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/dashlet.inc.php');
$graphexplorer_component_name = "graphexplorer";


graphexplorer_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function graphexplorer_component_init()
{
    global $graphexplorer_component_name;

    $versionok = graphexplorer_component_checkversion();

    $desc = _("Nagios Graph Explorer is an interactive graphing tool for your Nagios data. Requires Nagios XI 2011R1.3 or later.
            For most reliable graphs and dashlets, use Mozilla Firefox or Google Chrome.");
    if (!$versionok)
        $desc = "<b>" . _("Error: This component requires Nagios XI 2011R1.3 or later.") . "</b>";

    $args = array(
        COMPONENT_NAME => $graphexplorer_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => "Nagios Graph Explorer",
        COMPONENT_VERSION => '2.3.0',
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($graphexplorer_component_name, $args);

    // Add a menu link
    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'graphexplorer_component_addmenu');
    }

    // Add graph icons to status tables and add required javascript to page head 
    if (get_product_release() > 302) {
        register_callback(CALLBACK_CUSTOM_TABLE_ICONS, 'graphexplorer_component_table_icon');
        register_callback(CALLBACK_PAGE_HEAD, 'graphexplorer_component_js_include');
    }

    // Register a dashlet
    $args = array();
    $args[DASHLET_NAME] = "graphexplorer";
    $args[DASHLET_TITLE] = "Graph Explorer";
    $args[DASHLET_FUNCTION] = "graphexplorer_dashlet_func";
    $args[DASHLET_DESCRIPTION] = _("Displays a graph explorer dashlet.");
    $args[DASHLET_WIDTH] = "600";
    $args[DASHLET_HEIGHT] = "350";
    $args[DASHLET_INBOARD_CLASS] = "graphexplorer_map_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "graphexplorer_map_outboard";
    $args[DASHLET_CLASS] = "graphexplorer_map";
    $args[DASHLET_AUTHOR] = "Nagios Enterprises, LLC";
    $args[DASHLET_COPYRIGHT] = "Dashlet Copyright &copy; 2011-2019 Nagios Enterprises. All rights reserved.";
    $args[DASHLET_HOMEPAGE] = "https://www.nagios.com";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

/**
 * Adds a clickable graph icon into the status tables
 *
 * @param string $cbtype : the callback name being referenced: CALLBACK_CUSTOM_TABLE_ICONS
 * @param mixed  $args   : the callback data array, contains all host/service data, also used to pass data back up to it
 */
function graphexplorer_component_table_icon($cbtype = '', &$args = array())
{
    $host = grab_array_var($args, 'host_name', false);
    $service = grab_array_var($args, 'service_description', '');

    // Bail if we're missing what we need or there's no graph available
    if (!$host || !pnp_chart_exists($host, $service)) {
        return;
    }

    // Bail if we don't have permissions
    if (!is_authorized_for_host(0, $host) && !is_authorized_for_service(0, $host, $service)) {
        return;
    }

    //clean input 
    $host = urlencode($host);
    $service = urlencode($service);
    $service = empty($service) ? '_HOST_' : $service;

    //html to be added to the status table icon
    $icondata = '<img class="graphexplorericon tt-bind" style="cursor: pointer;" src="' . theme_image('chart_line.png') . '" alt="" title="' . _('View performance graph') . '" onclick="graphexplorer_display_graph(\''.$host.'\', \''.$service.'\')">';

    //send the html data back to the callback data array
    $args['icons'][] = $icondata;
}

/**
 * Adds required Highcharts if it hasn't been included
 *
 * @param string $cbtype
 * @param null   $args
 */
function graphexplorer_component_js_include($cbtype = '', $args = null)
{
    // Add graphexplorer dependancy
    echo '<script type="text/javascript" src="' . get_base_url() . 'includes/components/graphexplorer/includes/graphexplorerinclude.js"></script>';

    // If highcharts hasn't been added then let's add it
    if (!file_exists(get_base_dir() . "/includes/js/highcharts/highcharts.js")) {
        echo '<script type="text/javascript" src="' . get_base_url() . '/includes/components/highcharts/js/highcharts.js"></script>
          <script type="text/javascript" src="' . get_base_url() . '/includes/components/highcharts/js/modules/exporting.js"></script>';

        if (get_option("default_highcharts_theme") == 'gray') {
            echo '<script type="text/javascript" src="' . get_base_url() . '/includes/components/highcharts/js/themes/gray.js"></script>';
        }
    }
}

/**
 *   Make sure we will actually work and we're not REALLY old
 */
function graphexplorer_component_checkversion()
{

    if (!function_exists('get_product_release')) {
        return false;
    }

    //requires greater than 2009R1.4B
    if (get_product_release() < 200) {
        return false;
    }

    return true;
}

/**
 *   Add a menu item to the graphs section
 *
 * @param null $arg
 */
function graphexplorer_component_addmenu($arg = null)
{
    $mi = find_menu_item(MENU_HOME, "menu-home-performance-graphs", "id");
    if ($mi == null) {
        return;
    }

    $order = grab_array_var($mi, "order", "");
    if ($order == "") {
        return;
    }

    $neworder = $order + 0.1;
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => _("Graph Explorer"),
        "id" => "menu-home-graphexplorer",
        "order" => $neworder,
        "opts" => array(
            "href" => get_base_url() . 'includes/components/graphexplorer/',
            "icon" => "fa-map-o"
        )
    ));
}