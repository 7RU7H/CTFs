<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/includes/common.inc.php');


// Start session
init_session(false, false);

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs(false);
check_authentication(false);


route_request();


function route_request()
{
    check_nagios_session_protector();

    $cmd = grab_request_var("cmd", "");

    switch ($cmd) {

        // MISC
        case "keepalive":
            ah_keepalive();
            break;
        case "getsessionvars":
            ah_get_session_vars();
            break;
        case "setsessionvars":
            ah_set_session_vars();
            break;
        case "getformattednumber":
            ah_get_formatted_number();
            break;
        case "gettimestampstring":
            ah_get_timestamp_string();
            break;
        case "getdatetimestring":
            ah_get_datetime_string();
            break;

        // USER META
        case "setusermeta":
            ah_set_user_meta();
            break;
        case "getusermeta":
            ah_get_user_meta();
            break;

        // XI CORE AJAX
        case "getxicoreajax":
            session_write_close(); //dev experiment
            ah_get_xicore_ajaxdata();
            break;

        // CORE GET APPLY CONFIG CHANGES & ERRORS
        case "getapplyconfigchanges":
            ah_get_apply_config_changes();
            break;
        case "getapplyconfigerrors":
            ah_get_apply_config_errors();
            break;

        // COMMANDS
        case "getcommandstatus":
            ah_get_command_status();
            break;

        // COMMANDS
        case "submitcommand":
            ah_submit_command();
            break;

        // USERS
        case "masquerade":
            ah_masquerade();
            break;

        // VIEWS
        case "addview":
            ah_add_view();
            break;
        case "updateview":
            ah_update_view();
            break;
        case "getviewbynum":
            ah_get_view_by_number();
            break;
        case "getviewbyid":
            ah_get_view_by_id();
            break;
        case "deleteviewbyid":
            ah_delete_view_by_id();
            break;
        case "getviewsmenuhtml":
            ah_get_views_menu_html();
            break;
        case "getviewnumfromid":
            ah_get_view_num_from_id();
            break;
        case "updateviewsortorder":
            ah_update_view_sortorder();
            break;

        // DASHBOARDS
        case "adddashboard":
            ah_add_dashboard();
            break;
        case "updatedashboard":
            ah_update_dashboard();
            break;
        case "getdashboardbyid":
            ah_get_dashboard_by_id();
            break;
        case "getrawdashboardbyid":
            ah_get_rawdashboard_by_id();
            break;
        case "importdashboard":
            ah_dashboard_import();
            break;
        case "getdashboardbynum":
            ah_get_dashboard_by_number();
            break;
        case "deletedashboardbyid":
            ah_delete_dashboard_by_id();
            break;
        case "clonedashboardbyid":
            ah_clone_dashboard_by_id();
            break;
        case "getdashboardsmenuhtml":
            ah_get_dashboards_menu_html();
            break;
        case "getdashboardnumfromid":
            ah_get_dashboard_num_from_id();
            break;

        // REPORTS
        case "getmyreportsmenu":
            ah_get_myreports_menu();
            break;

        // TOOLS
        case "getmytoolsmenu":
            ah_get_my_tools_menu();
            break;
        case "getcommontoolsmenu":
            ah_get_common_tools_menu();
            break;
            
        // DASHLETS
        case "addtodashboard":
            ah_add_to_dashboard();
            break;
        case "getdashboardselectmenuhtml":
            ah_get_dashboard_select_menu_html();
            break;
        case "getadddashletdata":
            session_write_close();
            ah_get_add_dashlet_data();
            break;
        case "setdashletproperty":
            ah_set_dashlet_property();
            break;
        case "deletedashlet":
            ah_delete_dashlet();
            break;

        // CONFIG WIZARDS
        case "doconfigwizardinstallpostback":
            ah_do_config_wizard_install_postback();
            break;

        // DEBUG TOOLS
        case "dbgtaillog":
            ah_dbg_tail_log();
            break;
        case "dbgcleartail":
            ah_dbg_clear_tail();
            break;
        case "dbgtruncatelog":
            ah_dbg_truncate_log();
            break;
        case "dbgtruncatebacktracelog":
            ah_dbg_truncate_backtrace_log();
            break;
        case "dbgtoggle":
            ah_dbg_toggle();
            break;
        case "dbgbacktracetoggle":
            ah_dbg_backtrace_toggle();
            break;

        default:
            break;
    }

    exit;
}


////////////////////////////////////////////////////////////////////////
// CONFIG WIZARDS
////////////////////////////////////////////////////////////////////////


function ah_do_config_wizard_install_postback()
{
    $opts = grab_request_var("opts", "");

    $optsarr_s = json_decode($opts);
    if ($optsarr_s) {
        $optsarr = (array)$optsarr_s;
        $wizard = $optsarr["wizard"];
        $wizard_result = $optsarr["result"];
        $wizard_request = (array)$optsarr["request"];
    } else {
        return;
    }

    // Make config wizard callback
    $inargs = $wizard_request;
    $outargs = array();
    $result = 0;

    require_once(dirname(__FILE__) . '/includes/configwizards.inc.php');

    if ($wizard_result == 0) {
        make_configwizard_callback($wizard, CONFIGWIZARD_MODE_COMMITOK, $inargs, $outargs, $result);
    } else {
        make_configwizard_callback($wizard, CONFIGWIZARD_MODE_COMMITCONFIGERROR, $inargs, $outargs, $result);
    }
}


////////////////////////////////////////////////////////////////////////
// USER META FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_set_user_meta()
{
    $keyname = "";
    $keyvalue = "";
    $autoload = false;

    // get the command data
    $opts = grab_request_var("opts", "");
    $options = json_decode($opts, true);

    // Turn it into an array if it isn't already (this is so we can use arrays)
    if (isset($options['keyname'])) {
        $options = array($options);
    }

    foreach ($options as $optsarr) {

        $keyname = grab_array_var($optsarr, "keyname", "");
        $keyvalue = grab_array_var($optsarr, "keyvalue", "");
        $autoload = grab_array_var($optsarr, "autoload", "");

        $allowed = array('view_rotation_speed', 'view_rotation_enabled', 'show_login_alert_screen', 'wizard_import_only', 'ccm_stop_import_info_popup',
                         'map_stop_info_popup', 'map_default_coords', 'day_45', 'day_30', 'day_15', 'day_5', 'day_1', 'exp_10', 'exp_30', 'exp_45',
                         'exp_60', 'exp_no_show', 'downtime_page_refresh', 'mobile_redirects_disabled');

        // Only allow some user metas to be set for security reasons
        $allow = false;
        if (in_array($keyname, $allowed)) {
            $allow = true;
        } else if (strpos($keyname, '_tpl_default') !== false) {
            $allow = true;
        } else if (strpos($keyname, 'ps_') !== false) {
            // PAGE SETTINGS (INTEGERS ONLY)
            $allow = true;
            $keyvalue = intval($keyvalue);
        } else if ($keyname == "menu_collapse_options") {
            set_user_menu_preferences($optsarr["menuid"], $optsarr["keyvalue"]);
            return;
        } else if ($keyname == "tours") {
            set_user_tour_preferences($optsarr["tourid"], $optsarr["keyvalue"]);
            return;
        }

        if ($allow == false) {
            echo "NOT ALLOWED";
            return;
        }

        set_user_meta(0, $keyname, $keyvalue, $autoload);
    }

    echo "DONE";
}


function ah_get_user_meta()
{
    $keyname = grab_request_var("opts", "");
    $keyvalue = get_user_meta(0, $keyname);
    if ($keyvalue == null) {
        $keyvalue = "";
    }
    echo $keyvalue;
}


////////////////////////////////////////////////////////////////////////
// COMMAND FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_submit_command()
{
    $command_id = COMMAND_NONE;
    $command_data = "";
    $event_time = time();
    $command_args = array();
    $result = -1;

    // Get the command data
    $opts = grab_request_var("opts", "");

    $optsarr_s = json_decode($opts);
    if ($optsarr_s) {
        $optsarr = (array)$optsarr_s;
        $command_id = intval($optsarr["cmd"]);
        if (array_key_exists("cmddata", $optsarr)) {
            $cmddata_arr = (array)($optsarr["cmddata"]);
            $command_data = json_encode($cmddata_arr);
        }
        if (array_key_exists("cmdtime", $optsarr)) {
            $event_time = $optsarr["cmdtime"];
        }
        if (array_key_exists("cmdargs", $optsarr)) {
            $command_args = $optsarr["cmdargs"];
        }
    }

    // Submit the command
    if ($command_id != COMMAND_NONE) {
        $result = submit_command($command_id, $command_data, $event_time, 0, $command_args);
    }

    echo $result;
}


function ah_get_command_status()
{
    $result = array(
        "command_id" => -1,
        "status_code" => -1,
        "result_code" => -1,
        "submission_time" => "",
        "event_time" => "",
        "processing_time" => "",
        "result" => "",
    );

    // Get the command ID
    $opts = intval(grab_request_var("opts", ""));

    // Get command status from backend
    $args = array(
        "command_id" => $opts
    );
    $arr = get_command_status_xml($args, true, false);
    if (!empty($arr)) {
        $arr = $arr[0];
        foreach ($result as $i => $val) {
            $result[$i] = $arr[$i];
        }
    }

    $output = json_encode($result);
    echo $output;
}


////////////////////////////////////////////////////////////////////////
// USER FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_masquerade()
{
    $opts = grab_request_var("opts", "");
    $urlopts = strstr($opts, "user_id=");
    $args = explode("?&", $urlopts);
    $userid = -1;

    // Find the user id
    foreach ($args as $varvalpair) {
        $p = explode("=", $varvalpair);
        if ($p[0] == "user_id") {
            $userid = intval($p[1]);
        }
    }
    echo "\nUSERID: $userid\n";
    masquerade_as_user_id($userid);
}


////////////////////////////////////////////////////////////////////////
// CORE AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_get_xicore_ajaxdata()
{
    // What function does the user want to run
    $opts = grab_request_var("opts", "");
    $optsarr = json_decode($opts);

    // Sanity check against input garbage
    if (!is_object($optsarr)) {
        trigger_error('JSON array cannot be decoded. ' . print_r($optsarr, true));
        die('ERROR: Bad JSON format!');
    }

    $fname = "xicore_ajax_" . strval($optsarr->func);
    $args = array();
    if ($optsarr->args) {
        foreach ($optsarr->args as $var => $val) {
            $args[strval($var)] = strval($val);
        }
    }

    $output = $fname($args);
    echo $output;
}


////////////////////////////////////////////////////////////////////////
// DASHLET FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_delete_dashlet()
{
    $opts = grab_request_var("opts", "");
    $optsarr = json_decode($opts);

    $board_id = $optsarr->board;
    $dashlet_id = $optsarr->dashlet;

    remove_dashlet_from_dashboard(0, $board_id, $dashlet_id);
}


function ah_set_dashlet_property()
{
    $opts = grab_request_var("opts", "");
    $optsarr = json_decode($opts);

    $board_id = $optsarr->board;
    $dashlet_id = $optsarr->dashlet;

    $props = array();
    foreach ($optsarr->props as $propvar => $propval) {
        $props[$propvar] = $propval;
    }

    set_dashboard_dashlet_property(0, $board_id, $dashlet_id, $props);
}


function ah_get_add_dashlet_data()
{
    $opts = grab_request_var("opts", 0);
    $a = unserialize(base64_decode($opts));

    // Initialize the return array
    $ret = array();
    $ret[DASHLET_NAME] = "";
    $ret[DASHLET_TITLE] = "";

    if (array_key_exists(DASHLET_NAME, $a)) {
        $ret[DASHLET_NAME] = $a[DASHLET_NAME];
    }
    if (array_key_exists(DASHLET_TITLE, $a)) {
        $ret[DASHLET_TITLE] = $a[DASHLET_TITLE];
    }

    $args = array();
    if (array_key_exists(DASHLET_ARGS, $a)) {
        $args = $a[DASHLET_ARGS];
    }

    // Get config options from dashlet function...
    if ($ret[DASHLET_NAME] != "") {
        $ret[DASHLET_CONFIGHTML] = get_dashlet_output($ret[DASHLET_NAME], "", DASHLET_MODE_GETCONFIGHTML, $args);
    }

    $output = json_encode($ret);
    echo $output;
}


function ah_add_to_dashboard()
{
    global $request;

    $name = grab_request_var("name", 0);
    $title = grab_request_var("title", 0);
    $board = grab_request_var("board", 0);
    $paramsraw = grab_request_var("params", 0);
    $params = unserialize(base64_decode($paramsraw));
    $width = grab_request_var("width", 0);
    $height = grab_request_var("height", 0);

    // Get dashlet-specifc args
    $args = array();

    // Save what was passed to us by default
    if (array_key_exists(DASHLET_ARGS, $params)) {
        $args = $params[DASHLET_ARGS];
    }

    // Add/override base on what was submitted
    foreach ($request as $var => $val) {

        // Add args (skip some we use for other purposes)
        switch ($var) {
            case "cmd":
            case "submitButton":
            case "name":
            case "title":
            case "board":
            case "params":
                break;
            default:
                $args[$var] = $val;
                break;
        }

    }

    // Check if we have height/width specified in args
    if (empty($height)) {
        $height = grab_array_var($args, 'height', grab_array_var($params, 'height', 0));
    }
    if (empty($width)) {
        $width = grab_array_var($args, 'width', grab_array_var($params, 'width', 0));
    }

    // Options are null for the time being - they should only contain opacity, height, width, info...
    $opts = array(
        'width' => $width, 
        'height' => $height
    );

    add_dashlet_to_dashboard(0, $board, $name, $title, $opts, $args);
}


function ah_get_dashboard_select_menu_html()
{
    // Add a default dashboard if none exists
    $dashboards = get_dashboards(0);
    $html = "";

    // Grab new dashboard
    if (count($dashboards) == 0) {
        $opts = array();
        add_dashboard(0, "Default Dashboard", $opts);
        $dashboards = get_dashboards(0);
    }

    foreach ($dashboards as $d) {
        if ($d["id"] == SCREEN_DASHBOARD_ID) {
            continue;
        }
        $html .= '<option value="' . encode_form_val($d["id"]) . '">' . encode_form_val($d["title"]) . '</option>';
    }

    // Add "SCREEN" at the end of the list
    $html .= '<option value="' . SCREEN_DASHBOARD_ID . '">SCREEN</option>';

    echo $html;
}


////////////////////////////////////////////////////////////////////////
// DASHBOARD FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_update_dashboard()
{
    $id = grab_request_var("id", 0);
    $title = grab_request_var("title", 0);
    $background = grab_request_var("background", 0);
    $transparent = grab_request_var("transparent", 0);

    $opts = array();
    $opts["background"] = $background;
    $opts["transparent"] = $transparent;

    update_dashboard(0, $id, $title, $opts);
}


function ah_get_dashboard_num_from_id()
{
    $id = grab_request_var("opts", 0);
    $dashboards = get_dashboards(0);
    $n = 0;

    foreach ($dashboards as $d) {
        if ($d["id"] == $id) {
            break;
        }
        $n++;
    }

    echo $n;
}


function ah_get_dashboards_menu_html()
{
    echo get_dashboards_menu_html(0);
}


/**
 * Get dashboard details based on ID
 */
function ah_get_dashboard_by_id()
{
    $id = grab_request_var("opts", 0);
    $dashboards = get_dashboards(0);

    // Initialize the array
    $thedashboard = array(
        "id" => "nodashboardid",
        "url" => get_base_url() . "/dashboards/dashboard.php",
        "title" => "",
        "background" => "",
        "transparent" => 0,
        "number" => -1
    );
    $n = 0;

    foreach ($dashboards as $dashboard) {
        if ($dashboard["id"] == $id) {
            $thedashboard = array(
                "id" => $dashboards[$n]["id"],
                "url" => get_base_url() . "/dashboards/dashboard.php?id=" . $dashboards[$n]["id"],
                "title" => encode_form_val($dashboards[$n]["title"]),
                "background" => encode_form_val($dashboards[$n]["opts"]["background"]),
                "transparent" => intval($dashboards[$n]["opts"]["transparent"]),
                "number" => $n
            );
        }
        $n++;
    }

    $s = json_encode($thedashboard);
    echo $s;
}

/**
 * Get dashboard details based on ID
 */
function ah_get_rawdashboard_by_id()
{
    $dashboardid = grab_request_var("opts", 0);
    $userid = $_SESSION['user_id'];

    $rawdashboard = get_dashboard_by_id($userid, $dashboardid);

    // Before we export the dashboard, we need to remove any Nagios Session Protector (NSP) values from each dashlet.
    // These will be re-added on import, we just don't want them to be easily viewable. -AC
    $dashlets_no_nsp = array();
    foreach($rawdashboard['dashlets'] as $dashlet) {
        if (array_key_exists('nsp', $dashlet['args'])){
            $dashlet['args']['nsp'] = '';
            array_push($dashlets_no_nsp, $dashlet);
        }
        else {
            array_push($dashlets_no_nsp, $dashlet);
        }
    }
    $rawdashboard['dashlets'] = $dashlets_no_nsp;

    $export_ready_dashboard = json_encode(serialize($rawdashboard));
    echo $export_ready_dashboard;
}


function ah_get_dashboard_by_number()
{
    $num = grab_request_var("opts", 0);
    $dashboards = get_dashboards(0);
    $count = count($dashboards);

    if ($count == 0) {
        $thedashboard = array(
            "id" => "nodashboardid",
            "url" => get_base_url() . "/dashboards/dashboard.php",
            "title" => "",
            "background" => "",
            "transparent" => 0,
            "number" => -1
        );
    } else {
        $thedashboard = array(
            "id" => $dashboards[$num]["id"],
            "url" => get_base_url() . "/dashboards/dashboard.php?id=" . $dashboards[$num]["id"],
            "title" => encode_form_val($dashboards[$num]["title"]),
            "background" => encode_form_val($dashboards[$num]["opts"]["background"]),
            "transparent" => intval($dashboards[$num]["opts"]["transparent"]),
            "number" => $num
        );
    }

    $s = json_encode($thedashboard);
    echo $s;
}


/**
 * Delete a dashboard based on ID
 */
function ah_delete_dashboard_by_id()
{
    $id = grab_request_var("opts", -1);
    delete_dashboard_id(0, $id);

    // Add a default dashboard if that was the last one
    $dashboards = get_dashboards(0);
    if (count($dashboards) == 0) {
        $opts = array();
        add_dashboard(0, "Default Dashboard", $opts);
    }
}


/**
 * Clone a dashboard based on ID
 */
function ah_clone_dashboard_by_id()
{
    $id = grab_request_var("opts", -1);
    $title = grab_request_var("title", "("._("Cloned").")");

    clone_dashboard_id(0, $id, $title);
}


/**
 * Import a dashboard
 */
function ah_dashboard_import()
{
    $importString = grab_request_var("importstring");

    importDashboard($importString);
}


/**
 * Add a new dashboard
 */
function ah_add_dashboard()
{
    $title = grab_request_var("title", "");
    if (empty($title)) {
        echo _("NO TITLE") . "\n";
        return;
    }

    $opts = grab_request_var("opts", array());
    if (!is_array($opts)) {
        $opts = array();
    }

    $background = encode_form_val(grab_request_var("background", ""));
    $opts["background"] = $background;

    $transparent = intval(grab_request_var("transparent", 0));
    $opts["transparent"] = $transparent;

    add_dashboard(0, $title, $opts);
}


/**
 * Get JSON and HTML for "My Reports" menu
 */
function ah_get_myreports_menu()
{
    $arr = array();
    $arr['html'] = get_myreports_menu_html(0);
    $arr['count'] = get_myreports_count();
    echo json_encode($arr);
}


function ah_get_my_tools_menu()
{
    $arr = array();
    $mytools = get_mytools($userid);
    $x = 1;

    $mi = array();
    foreach ($mytools as $id => $t) {
        $mi[] = array(
            "type" => "link",
            "title" => encode_form_val($t["name"]),
            "order" => (100 + $x),
            "opts" => array(
                "href" => "mytools.php?go=1&id=" . $id,
                "id" => "mytools-" . $id,
            )
        );
        $X++;
    }

    $arr['html'] = get_menu_items_html($mi);
    $arr['count'] = count($mytools);
    echo json_encode($arr);
}


function ah_get_common_tools_menu()
{
    $arr = array();
    $mytools = get_commontools($userid);
    $x = 1;

    $mi = array();
    foreach ($mytools as $id => $t) {
        $mi[] = array(
            "type" => "link",
            "title" => encode_form_val($t["name"]),
            "order" => (100 + $x),
            "opts" => array(
                "href" => "commontools.php?go=1&id=" . $id,
                "id" => "commontools-" . $id,
            )
        );
        $X++;
    }

    $arr['html'] = get_menu_items_html($mi);
    $arr['count'] = count($mytools);
    echo json_encode($arr);
}


////////////////////////////////////////////////////////////////////////
// VIEW FUNCTIONS
////////////////////////////////////////////////////////////////////////


function ah_update_view()
{
    $id = grab_request_var("id", 0);
    $url = grab_request_var("url", 0);
    $title = grab_request_var("title", 0);
    update_view(0, $id, $url, $title);
}


function ah_get_view_num_from_id()
{
    $id = grab_request_var("opts", 0);
    $views = get_views(0);

    foreach ($views as $k => $v) {
        if ($v["id"] == $id) {
            break;
        }
    }

    echo $k;
}


function ah_get_views_menu_html()
{
    echo get_views_menu_html(0);
}


function ah_get_view_by_number()
{
    $num = grab_request_var("opts", 0);
    $views = get_views(0);
    $count = count($views);

    if ($count == 0) {
        $theview = array(
            "id" => "noviewid",
            "url" => get_base_url() . "/views/main.php",
            "title" => ""
        );
    } else {
        $thenum = $num % $count;
        $theview = $views[$thenum];
    }

    $s = json_encode($theview);
    echo $s;
}


function ah_get_view_by_id()
{
    $id = grab_request_var("opts", 0);
    $views = get_views(0);

    foreach ($views as $v) {
        if ($v['id'] == $id) {
            $theview = $v;
            break;
        }
    }

    $s = json_encode($theview);
    echo $s;
}


function ah_delete_view_by_id()
{
    $id = grab_request_var("opts", 0);
    delete_view_id(0, $id);
}


function ah_add_view()
{
    $url = grab_request_var("url", "");
    $title = grab_request_var("title", "("._("Untitled").")");
    if (!have_value($url)) {
        return;
    }
    add_view(0, $url, $title);
}


function ah_update_view_sortorder()
{
    $sortorder = grab_request_var('opts', '');
    if (!empty($sortorder)) {
        $order = json_decode($sortorder);
        update_view_sortorder(0, $order);
    }
}


////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////


// Returns a formatted number
function ah_get_formatted_number()
{
    $num = grab_request_var("num", 0);
    $dec = grab_request_var("dec", -1);
    $fmt = grab_request_var("fmt", "");

    echo get_formatted_number($num, $dec, $fmt);
}


// Returns a formatted date/time string
function ah_get_datetime_string()
{
    $str = grab_request_var("t", "");
    $tz = grab_request_var("tz", "UTC");
    $type = grab_request_var("type", DT_SHORT_DATE_TIME);
    $fmt = grab_request_var("fmt", DF_AUTO);
    $zs = grab_request_var("zs", "");

    echo get_datetime_string_from_datetime($str, $tz, $type, $fmt, $zs);
}


// Returns a formatted date/time string
function ah_get_timestamp_string()
{
    $ts = grab_request_var("ts", time());
    $type = grab_request_var("type", DT_SHORT_DATE_TIME);
    $fmt = grab_request_var("fmt", DF_AUTO);
    $zs = grab_request_var("zs", "");

    echo get_datetime_string($ts, $type, $fmt, $zs);
}


// Calling this function will force the session to stay alive, while normally
// an ajaxhelper function call will not refresh the session
function ah_keepalive()
{
    // Update cookie if we need to
    $cookie_auto_refresh = get_option('cookie_auto_refresh', 1);
    if ($cookie_auto_refresh) {
        init_session(false);
    }
}


/**
 * Set a session variable - ignore non-specified variables for security reasons
 */
function ah_set_session_vars()
{
    $optsarr = array();
    $opts = grab_request_var("opts", "");

    $optsarr_s = json_decode($opts);
    if ($optsarr_s) {
        $optsarr = (array)$optsarr_s;
    }

    $sessionvars = $optsarr;

    foreach ($sessionvars as $var => $val) {
        switch ($var) {
            // Only set non-security affecting variables
            case "ignore_notice_update":
            case "ignore_notice_language":
            case "ignore_trial_notice":
            case "ignore_free_notice":
            case "show_details":
            case "was_redirected_to_mobile":
                $_SESSION[$var] = $val;
                echo "SET VARIABLE '" . encode_form_val($var) . "' to '" . encode_form_val($val) . "'\n";
                break;
            default:
                // Do nothing by default for security reasons
                break;
        }
    }

    echo "DONE\n";
}

/**
 * Get a session variable
 */
function ah_get_session_vars()
{
    $optsarr = array();
    $needles = grab_request_var("needles", "");

    // Check for array
    $needles_decoded = json_decode("$needles");
    if ( $needles_decoded != false) {
        $return_values = array();

        foreach ( $needles_decoded as $needle ) {
            if ( array_key_exists($needle, $_SESSION)) {
                $return_values["$needle"] = $_SESSION["$needle"];
            } else {
                // Set the return value to 0, mostly to preserve original json structure
                $return_values["$needle"] = 0;
            }
        }

        $return_json = json_encode($return_values);
        echo $return_json;
        return true;
    }  else {
        // One needle
        if ( array_key_exists($needle, $_SESSION) ) {
            echo $_SESSION["$needle"];
            return true;
        } else {
            echo 'false';
            return false;
        }
        return true;
    }

    echo _("Request completed");
    return true;
}


// Get the apply config changes
function ah_get_apply_config_changes()
{
    echo get_option('apply_config_output');
}


// Get the apply config errors and output them in divs
function ah_get_apply_config_errors()
{
    global $cfg;

    $error_checkpoints = $cfg['nom_checkpoints_dir'] . "errors/";
    $last_mod = 0;
    $last_mod_file = '';
    foreach (scandir($error_checkpoints) as $checkpoint) {
        if (is_file($error_checkpoints . $checkpoint) && filectime($error_checkpoints . $checkpoint) > $last_mod && substr($checkpoint, -4) == ".txt") {
            $last_mod = filectime($error_checkpoints . $checkpoint);
            $last_mod_file = $checkpoint;
        }
    }

    $temp_errors = array();
    $errors = explode("\n", file_get_contents($error_checkpoints . $last_mod_file));
    foreach ($errors as $error) {
        if (strpos($error, "Error:") !== false) {
            $temp_errors[] = "<div>" . $error . "</div>";
        }
    }
    $errors = implode("", $temp_errors);
    echo '<div class="error">' . encode_form_valq($errors) . "</div>";
}


function ah_dbg_tail_log()
{
    $xdbgfile = get_debug_log_file();
    $handle = fopen($xdbgfile, 'r');

    if (isset($_SESSION['dbg_offset']) || !empty($_SESSION['dbg_offset'])) {
        fseek($handle, 0, $_SESSION['dbg_offset']);
        $data = stream_get_contents($handle, 50000000, $_SESSION['dbg_offset']);
        $_SESSION['dbg_offset'] = ftell($handle);
        echo $data;
    } else {
        fseek($handle, 0, SEEK_END);
        $_SESSION['dbg_offset'] = ftell($handle);
    }
}


function ah_dbg_clear_tail()
{
    $_SESSION['dbg_offset'] = 0;
}


function ah_dbg_truncate_log()
{
    $xdbgfile = get_debug_log_file();
    $cmd = "echo \"\" > $xdbgfile";
    exec($cmd);
}


function ah_dbg_truncate_backtrace_log()
{
    $xdbgbacktracefile = get_debug_backtrace_log_file();

    $cmd = "echo \"\" > $xdbgbacktracefile";
    exec($cmd);
}


function ah_dbg_toggle()
{
    debug_logging(true);
    echo (debug_logging() ? "On" : "Off");
}


function ah_dbg_backtrace_toggle()
{
    debug_backtracing(true);
    echo (debug_backtracing() ? "On" : "Off");
}