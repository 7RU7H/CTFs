<?php
//
// XI Status Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/status-ncpa-host-detail.inc.php');
include_once(dirname(__FILE__) . '/status-object-detail.inc.php');


////////////////////////////////////////////////////////////////////////
// HELPER FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Show object icon (host or service)
 *
 * @param   string  $host       Host name
 * @param   string  $service    Service name
 * @param   bool    $usehost    True to force using host
 */
function show_object_icon($host, $service = "", $usehost = false)
{
    echo get_object_icon($host, $service, $usehost);
}


/**
 * Get object icon and return HTML (host or service)
 *
 * @param   string  $host       Host name
 * @param   string  $service    Service name
 * @param   bool    $usehost    True to force using host
 * @return  string              HTML string
 */
function get_object_icon($host, $service = "", $usehost = false)
{
    $html = "";
    $tryhost = false;
    $iconimage = "";

    // We are showing a host icon
    if (empty($service)) {
        $tryhost = true;
    } else {
        $sid = get_service_id($host, $service);
        $xml = get_xml_service_objects(array("service_id" => $sid));
        if ($xml != null && $xml->recordcount > 0) {
            $iconimage = strval($xml->service->icon_image);
            $iconimagealt = strval($xml->service->icon_image_alt);
            if (empty($iconimage) && $usehost == true) {
                $tryhost = true;
            }
        } else {
            if ($usehost == true) {
                $tryhost = true;
            }
        }
    }

    if ($tryhost == true) {
        $hid = get_host_id($host);
        $xml = get_xml_host_objects(array("host_id" => $hid));
        if ($xml != null && $xml->recordcount > 0) {
            $iconimage = strval($xml->host->icon_image);
            $iconimagealt = strval($xml->host->icon_image_alt);
        }
    }

    if (!empty($iconimage)) {
        $html = get_object_icon_html($iconimage, $iconimagealt);
    }

    return $html;
}


/**
 * Get an object icon image (host and service)
 *
 * @param   string  $host       Host name
 * @param   string  $service    Service name
 * @param   bool    $usehost    True to force using host
 * @return  string              HTML string
 */
function get_object_icon_image($host, $service = "", $usehost = false)
{
    $iconimage = "";
    $tryhost = false;

    // We are showing a host icon
    if (empty($service)) {
        $tryhost = true;
    } else {
        $sid = get_service_id($host, $service);
        $xml = get_xml_service_objects(array("service_id" => $sid));
        if ($xml != null && $xml->recordcount > 0) {
            $iconimage = strval($xml->service->icon_image);
            if (empty($iconimage) && $usehost == true) {
                $tryhost = true;
            }
        } else {
            if ($usehost == true) {
                $tryhost = true;
            }
        }
    }

    if ($tryhost == true) {
        $hid = get_host_id($host);
        $xml = get_xml_host_objects(array("host_id" => $hid));
        if ($xml != null && $xml->recordcount > 0) {
            $iconimage = strval($xml->host->icon_image);
        }
    }

    return $iconimage;
}


/**
 * Get the url HTML for notes
 *
 * @param   string  $url    URL string
 * @return  string          HTML string
 */
function get_notes_url_html($url)
{
    $html = '';
    if (!empty($url)) {
        $html = '<a href="'.str_replace('&amp;', '&', encode_form_val($url)).'" target="_new" class="tt-bind" title="'._('Notes URL').'"><img src="'.theme_image('page_white_go.png').'"></a>';
    }
    return $html;
}


/**
 * Get the url HTML for actions
 *
 * @param   string  $url    URL string
 * @return  string          HTML string
 */
function get_action_url_html($url)
{
    $html = '';
    if (!empty($url)) {
        $html = '<a href="'.str_replace('&amp;', '&', encode_form_val($url)).'" target="_new" class="tt-bind" title="'._('Action URL').'"><img src="'.theme_image('resultset_next.png').'"></a>';
    }
    return $html;
}


/**
 * Get an object's icon image HTML
 *
 * @param   string  $img        Image name
 * @param   string  $imgalt     Image alternate text
 * @return  string              HTML string
 */
function get_object_icon_html($img, $imgalt)
{
    $html = "";
    if ($img != "") {
        $html = "<img class='objecticon' src='" . get_object_icon_url($img) . "' title='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "' alt='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "'>";
    }
    return $html;
}


/**
 * Get a service status note image icon
 *
 * @param   string  $img        Image name
 * @param   string  $imgalt     Image alternate text
 * @return  string              HTML string
 */
function get_service_status_note_image($img, $imgalt)
{
    $html = "<img src='" . theme_image($img) . "' title='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "' alt='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "'>";
    return $html;
}


/**
 * Get a host status note image icon
 *
 * @param   string  $img        Image name
 * @param   string  $imgalt     Image alternate text
 * @return  string              HTML string
 */
function get_host_status_note_image($img, $imgalt)
{
    $html = "<img src='" . theme_image($img) . "' title='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "' alt='" . encode_form_val($imgalt, ENT_COMPAT, 'UTF-8') . "'>";
    return $html;
}


/**
 * Get an object icon image base on image name
 *
 * @param   string  $img        Image name
 * @return  string              HTML string
 */
function get_object_icon_url($img)
{
    return wizard_logo($img);
}


/**
 * Get a command icon image
 *
 * @param   string  $img        Image name
 * @param   string  $imgalt     Image alternate text
 * @return  string              HTML string
 */
function get_object_command_icon($img, $alt)
{
    return "<img src='" . get_object_command_icon_url($img) . "' title='" . encode_form_val($alt, ENT_COMPAT, 'UTF-8') . "' alt='" . encode_form_val($alt, ENT_COMPAT, 'UTF-8') . "'>";
}


/**
 * Get command icon image URL
 *
 * @param   string  $img        Image name
 * @return  string              HTML string
 */
function get_object_command_icon_url($img)
{
    $url = get_base_url() . "includes/components/nagioscore/ui/images/" . $img;
    return $url;
}


/**
 * Returns the command link HTML
 *
 * @param   string  $url        URL to command
 * @param   string  $img        Image name
 * @param   string  $title      Title of command
 * @return  string              HTML string
 */
function get_object_command_link($url, $img, $title)
{
    return '<div class="commandimage"><a href="' . $url . '">' . get_object_command_icon($img, $title) . '</a></div><div class="commandtext"><a href="' . $url . '">' . $title . '</a></div>';
}


/**
 * Echo out an object's command link HTML
 *
 * @param   string  $url    URL to command
 * @param   string  $img    Image name
 * @param   string  $title  Title of command link
 */
function show_object_command_link($url, $img, $title)
{
    echo get_object_command_link($url, $img, $title);
}


/**
 * Get AJAX JS and HTML code for running a Nagios Core command
 *
 * @param   array   $cmdarr     Command data
 * @return  string              JS/HTML
 */
function get_nagioscore_command_ajax_code($cmdarr)
{
    $args = array();
    if (!empty($cmdarr["multi_cmd"]))
        foreach ($cmdarr["multi_cmd"] as $k => $command_args)
            foreach ($command_args["command_args"] as $var => $val)
                $args['multi_cmd'][$k][$var] = $val;
            
    if (!empty($cmdarr["command_args"])) {
        foreach ($cmdarr["command_args"] as $var => $val) {
            $args[$var] = $val;
        }
    }

    $cmddata = json_encode($args);
    $clickcmd = "onClick='submit_command(" . COMMAND_NAGIOSCORE_SUBMITCOMMAND . "," . $cmddata . ")'";

    return $clickcmd;
}


/**
 * Get a service command link HTML
 *
 * @param   array   $cmdarr     Command data
 * @param   string  $img        Image name
 * @param   string  $text       Title text
 * @return  string              HTML
 */
function get_service_detail_command_link($cmdarr, $img, $text)
{
    $clickcmd = get_nagioscore_command_ajax_code($cmdarr);

    return '<div class="commandimage"><a ' . $clickcmd . '><img src="' . theme_image($img) . '" alt="' . $text . '" title="' . $text . '"></a></div><div class="commandtext"><a ' . $clickcmd . '>' . $text . '</a></div>';
}


/**
 * Get a host command link HTML
 *
 * @param   array   $cmdarr     Command data
 * @param   string  $img        Image name
 * @param   string  $text       Title text
 * @return  string              HTML
 */
function get_host_detail_command_link($cmdarr, $img, $text)
{
    $clickcmd = get_nagioscore_command_ajax_code($cmdarr);

    return '<div class="commandimage"><a ' . $clickcmd . '><img src="' . theme_image($img) . '" alt="' . $text . '" title="' . $text . '"></a></div><div class="commandtext"><a ' . $clickcmd . '>' . $text . '</a></div>';
}


/**
 * Get the inplace action link for a specific command in the
 * service status details
 *
 * @param   string  $clickcmd   JS/HTML
 * @param   string  $img        Image name
 * @param   string  $text       Title text
 * @return  string              HTML
 */
function get_service_detail_inplace_action_link($clickcmd, $img, $text)
{
    return '<div class="commandimage"><a onClick="' . $clickcmd . '"><img src="' . theme_image($img) . '" alt="' . $text . '" title="' . $text . '"></a></div><div class="commandtext"><a onClick="' . $clickcmd . '">' . $text . '</a></div>';
}


/**
 * Get a list of links for a service
 *
 * @param   string  $hostname       Host name
 * @param   string  $servicename    Service name
 * @param   object  $obj            Hostservice object
 */
function draw_service_detail_links($hostname, $servicename, $obj = null)
{
    echo "<div class='statusdetaillinks'>";
    echo "<div class='statusdetaillink'><a href='" . get_host_status_link($hostname) . "'><img src='" . theme_image("statusdetailmulti.png") . "' class='tt-bind' alt='" . _("View Current Status of Host Services") . "' title='" . _("View Current Status For Host Services") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_service_notifications_link($hostname, $servicename) . "'><img src='" . theme_image("notifications.png") . "' class='tt-bind' alt='" . _("View Service Notifications") . "' title='" . _("View Service Notifications") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_service_history_link($hostname, $servicename) . "'><img src='" . theme_image("history.png") . "' class='tt-bind' alt='" . _("View Service History") . "' title='" . _("View Service History") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_service_availability_link($hostname, $servicename) . "'><img src='" . theme_image("availability.png") . "' class='tt-bind' alt='" . _("View Service Availability") . "' title='" . _("View Service Availability") . "'></a></div>";
    
    // Notes URL icon/link HTML
    if (!empty($obj->notes_url)) {
        echo '<div class="statusdetaillink">'.get_notes_url_html(xicore_replace_macros($obj->notes_url, $obj, true)).'</div>';
    }

    // Action URL icon/link HTML
    if (!empty($obj->action_url)) {
        echo '<div class="statusdetaillink">'.get_action_url_html(xicore_replace_macros($obj->action_url, $obj, true)).'</div>';
    }

    echo "</div>";
}


/**
 * Create a list of links based on host name
 *
 * @param   string  $hostname   Host name
 * @param   object  $obj        Host object
 */
function draw_host_detail_links($hostname, $obj = null)
{
    $backendargs["limitrecords"] = false;
    $backendargs["totals"] = 1;
    $backendargs["host_name"] = $hostname;
    $xml = get_xml_service_status($backendargs);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    echo "<div class='statusdetaillinks'>";
    if ($total_records > 0) {
        echo "<div class='statusdetaillink'><a href='" . get_host_status_link($hostname) . "'><img src='" . theme_image("statusdetailmulti.png") . "' class='tt-bind' alt='" . _("View Current Status of Host Services") . "' title='" . _("View Current Status For Host Services") . "'></a></div>";
    }
    echo "<div class='statusdetaillink'><a href='" . get_host_notifications_link($hostname) . "'><img src='" . theme_image("notifications.png") . "' class='tt-bind' alt='" . _("View Host Notifications") . "' title='" . _("View Host Notifications") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_host_history_link($hostname) . "'><img src='" . theme_image("history.png") . "' class='tt-bind' alt='" . _("View Host History") . "' title='" . _("View Host History") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_host_availability_link($hostname) . "'><img src='" . theme_image("availability.png") . "' class='tt-bind' alt='" . _("View Host Availability") . "' title='" . _("View Host Availability") . "'></a></div>";
    
    // Notes URL icon/link HTML
    if (!empty($obj->notes_url)) {
        echo '<div class="statusdetaillink">'.get_notes_url_html(xicore_replace_macros($obj->notes_url, $obj)).'</div>';
    }

    // Action URL icon/link HTML
    if (!empty($obj->action_url)) {
        echo '<div class="statusdetaillink">'.get_action_url_html(xicore_replace_macros($obj->action_url, $obj)).'</div>';
    }

    echo "</div>";
}


/**
 * Host group name links
 *
 * @param   string  $hostgroupname  Host group name
 */
function draw_hostgroup_viewstyle_links($hostgroupname)
{
    $xistatus_url = get_base_url() . "includes/components/xicore/status.php";

    echo "<div class='statusdetaillinks'>";
    echo "<div class='statusdetaillink'><a href='" . $xistatus_url . "?show=services&hostgroup=" . urlencode($hostgroupname) . "'><img src='" . theme_image("statusdetailmulti.png") . "' alt='" . _("View Hostgroup Service Details") . "' title='" . _("View Hostgroup Service Details") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_hostgroup_status_link($hostgroupname, "summary") . "'><img src='" . theme_image("vssummary.png") . "' alt='" . _("View Hostgroup Summary") . "' title='" . _("View Hostgroup Summary") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_hostgroup_status_link($hostgroupname, "overview") . "'><img src='" . theme_image("vsoverview.png") . "' alt='" . _("View Hostgroup Overview") . "' title='" . _("View Hostgroup Overview") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_hostgroup_status_link($hostgroupname, "grid") . "'><img src='" . theme_image("vsgrid.png") . "' alt='" . _("View Hostgroup Grid") . "' title='" . _("View Hostgroup Grid") . "'></a></div>";
    echo "</div>";
}


/**
 * Get links for service groups
 *
 * @param   string  $servicegroupname   Service group name
 */
function draw_servicegroup_viewstyle_links($servicegroupname)
{
    $xistatus_url = get_base_url() . "includes/components/xicore/status.php";

    echo "<div class='statusdetaillinks'>";
    echo "<div class='statusdetaillink'><a href='" . $xistatus_url . "?show=services&servicegroup=" . urlencode($servicegroupname) . "'><img src='" . theme_image("statusdetailmulti.png") . "' alt='" . _("View Servicegroup Service Details") . "' title='" . _("View Servicegroup Service Details") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_servicegroup_status_link($servicegroupname, "summary") . "'><img src='" . theme_image("vssummary.png") . "' alt='" . _("View Servicegroup Summary") . "' title='" . _("View Servicegroup Summary") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_servicegroup_status_link($servicegroupname, "overview") . "'><img src='" . theme_image("vsoverview.png") . "' alt='" . _("View Servicegroup Overview") . "' title='" . _("View Servicegroup Overview") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_servicegroup_status_link($servicegroupname, "grid") . "'><img src='" . theme_image("vsgrid.png") . "' alt='" . _("View Servicegroup Grid") . "' title='" . _("View Servicegroup Grid") . "'></a></div>";
    echo "</div>";
}


function draw_servicestatus_table()
{
    // What meta key do we use to save user prefs?
    $meta_pref_option = 'servicestatus_table_options';

    $sortby = "";
    $sortorder = "asc";
    $page = 1;
    $records = 15;
    $search = "";

    // Default to use saved options
    $s = get_user_meta(0, $meta_pref_option);
    $saved_options = unserialize($s);
    if (is_array($saved_options)) {
        if (isset($saved_options["sortby"]))
            $sortby = $saved_options["sortby"];
        if (isset($saved_options["sortorder"]))
            $sortorder = $saved_options["sortorder"];
        if (isset($saved_options["records"]))
            $records = $saved_options["records"];
    }

    // Grab request variables
    $show = grab_request_var("show", "services");
    $host = grab_request_var("host", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $hostattr = grab_request_var("hostattr", 0);
    $serviceattr = grab_request_var("serviceattr", 0);
    $hoststatustypes = grab_request_var("hoststatustypes", 0);
    $servicestatustypes = grab_request_var("servicestatustypes", 0);

    // Fix for "all" options
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    $sortby = grab_request_var("sortby", $sortby);
    $sortorder = grab_request_var("sortorder", $sortorder);
    $records = grab_request_var("records", $records);
    $page = grab_request_var("page", $page);
    $search = trim(grab_request_var("search", $search));
    if ($search == _("Search...")) {
        $search = "";
    }

    // Save options for later
    $saved_options = array(
        "sortby" => $sortby,
        "sortorder" => $sortorder,
        "records" => $records
    );
    $s = serialize($saved_options);
    set_user_meta(0, $meta_pref_option, $s, false);

    $output = '';

    // AJAX updater args
    $ajaxargs = array();
    $ajaxargs["host"] = $host;
    $ajaxargs["hostgroup"] = $hostgroup;
    $ajaxargs["servicegroup"] = $servicegroup;
    $ajaxargs["sortby"] = $sortby;
    $ajaxargs["sortorder"] = $sortorder;
    $ajaxargs["records"] = $records;
    $ajaxargs["page"] = $page;
    $ajaxargs["search"] = $search;
    $ajaxargs["hostattr"] = $hostattr;
    $ajaxargs["serviceattr"] = $serviceattr;
    $ajaxargs["hoststatustypes"] = $hoststatustypes;
    $ajaxargs["servicestatustypes"] = $servicestatustypes;

    $id = "servicestatustable_" . random_string(6);

    $output .= "<div class='servicestatustable' id='" . $id . "'>\n";
    $output .= get_throbber_html();
    $output .= "</div>";

    // Build args for javascript
    $n = 0;
    $jargs = "{";
    foreach ($ajaxargs as $var => $val) {
        if ($n > 0) {
            $jargs .= ", ";
        }
        $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
        $n++;
    }
    $jargs .= "}";

    // AJAX updater
    $output .= '
    <script type="text/javascript">

    var statussearch;
    function updated_' . $id . '() {
        if (statussearch !== undefined) {
            $("#status-search-box").val(statussearch);
        }
        $("#status-search-box").each(function() {
            $(this).myautocomplete({ source: suggest_url+"?type=host", minLength: 1 });
        });
    }

    var refresh_lock = false;
    $(document).ready(function() {

        $("#' . $id . '").on("focus", "#status-search-box", function() {
            refresh_lock = true;
        });

        $("#' . $id . '").on("blur", "#status-search-box", function() {
            refresh_lock = false;
        });

        get_' . $id . '_content();

        $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, 'service_status_table') . ', "timer-' . $id . '", function(i) {
            get_' . $id . '_content();
        });

        function get_' . $id . '_content() {
            statussearch = $("#status-search-box").val();
            if (!refresh_lock) {
                $("#' . $id . '").each(function() {
                    var optsarr = {
                        "func": "get_servicestatus_table",
                        "args": ' . $jargs . '
                    }
                    var opts = JSON.stringify(optsarr);
                    get_ajax_data_innerHTML_with_callback("getxicoreajax", opts, true, this, "updated_' . $id . '");
                });
            }
        }

    });
    </script>
    ';

    echo $output;
}


function draw_hoststatus_table()
{
    // What meta key do we use to save user prefs?
    $meta_pref_option = 'hoststatus_table_options';

    $sortby = "";
    $sortorder = "asc";
    $page = 1;
    $records = 15;
    $search = "";

    // default to use saved options
    $s = get_user_meta(0, $meta_pref_option);
    if ($s) {
        $saved_options = unserialize($s);
        if (is_array($saved_options)) {
            if (isset($saved_options["sortby"]))
                $sortby = $saved_options["sortby"];
            if (isset($saved_options["sortorder"]))
                $sortorder = $saved_options["sortorder"];
            if (isset($saved_options["records"]))
                $records = $saved_options["records"];
        }
    }

    // Grab request variables
    $show = grab_request_var("show", "services");
    $host = grab_request_var("host", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $hostattr = grab_request_var("hostattr", 0);
    $serviceattr = grab_request_var("serviceattr", 0);
    $hoststatustypes = grab_request_var("hoststatustypes", 0);
    $servicestatustypes = grab_request_var("servicestatustypes", 0);

    // Fix for "all" options
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    $sortby = grab_request_var("sortby", $sortby);
    $sortorder = grab_request_var("sortorder", $sortorder);
    $records = grab_request_var("records", $records);
    $page = grab_request_var("page", $page);
    $search = trim(grab_request_var("search", $search));
    if ($search == _("Search...")) {
        $search = "";
    }

    // Save options for later
    $saved_options = array(
        "sortby" => $sortby,
        "sortorder" => $sortorder,
        "records" => $records
    );
    $s = serialize($saved_options);
    set_user_meta(0, $meta_pref_option, $s, false);

    $output = '';

    // AJAX updater args
    $ajaxargs = array();
    $ajaxargs["host"] = $host;
    $ajaxargs["hostgroup"] = $hostgroup;
    $ajaxargs["servicegroup"] = $servicegroup;
    $ajaxargs["sortby"] = $sortby;
    $ajaxargs["sortorder"] = $sortorder;
    $ajaxargs["records"] = $records;
    $ajaxargs["page"] = $page;
    $ajaxargs["search"] = $search;
    $ajaxargs["hostattr"] = $hostattr;
    $ajaxargs["serviceattr"] = $serviceattr;
    $ajaxargs["hoststatustypes"] = $hoststatustypes;
    $ajaxargs["servicestatustypes"] = $servicestatustypes;

    $id = "hoststatustable_" . random_string(6);

    $output .= "<div class='hoststatustable' id='" . $id . "'>\n";
    $output .= get_throbber_html();
    $output .= "</div>";

    // Build args for javascript
    $n = 0;
    $jargs = "{";
    foreach ($ajaxargs as $var => $val) {
        if ($n > 0) {
            $jargs .= ", ";
        }
        $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
        $n++;
    }
    $jargs .= "}";

    // AJAX updater
    $output .= '
    <script type="text/javascript">

    var statussearch;
    function updated_' . $id . '() {
        if (statussearch !== undefined) {
            $("#status-search-box").val(statussearch);
        }
        $("#status-search-box").each(function() {
            $(this).myautocomplete({ source: suggest_url+"?type=host", minLength: 1 });
        });
    }

    var refresh_lock = false;
    $(document).ready(function() {

        $("#' . $id . '").on("focus", "#status-search-box", function() {
            refresh_lock = true;
        });

        $("#' . $id . '").on("blur", "#status-search-box", function() {
            refresh_lock = false;
        });

        get_' . $id . '_content();

        $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, 'host_status_table') . ', "timer-' . $id . '", function(i) {
            get_' . $id . '_content();
        });

        function get_' . $id . '_content() {
            statussearch = $("#status-search-box").val();
            if (!refresh_lock) {
                $("#' . $id . '").each(function() {
                    var optsarr = {
                        "func": "get_hoststatus_table",
                        "args": ' . $jargs . '
                    }
                    var opts = JSON.stringify(optsarr);
                    get_ajax_data_innerHTML_with_callback("getxicoreajax", opts, true, this, "updated_' . $id . '");
                });
            }
        }

    });
    </script>
    ';

    echo $output;
}


/**
 * Get status view filter HTML
 *
 * @param   bool    $show
 * @param   array   $urlargs
 * @param   bool    $hostattr
 * @param   bool    $serviceattr
 * @param   int     $hoststatustypes
 * @param   int     $servicestatustypes
 * @param   string  $url
 * @return  string
 */
function get_status_view_filters_html($show, $urlargs, $hostattr, $serviceattr, $hoststatustypes, $servicestatustypes, $url = "")
{
    if (empty($url)) {
        $theurl = get_current_page();
    } else {
        $theurl = $url;
    }

    $output = '';

    // No filter is being used...
    if ($hostattr == 0 && ($hoststatustypes == 0 || $hoststatustypes == HOSTSTATE_ANY) && $serviceattr == 0 && ($servicestatustypes == 0 || $servicestatustypes == SERVICESTATE_ANY)) {
        return '';
    }

    if ($show == "openproblems" || $show == "serviceproblems") {
        $show = "services";
    } else if ($show == "hostproblems") {
        $show = "hosts";
    }

    $theurl .= "?show=" . $show;
    foreach ($urlargs as $var => $val) {
        if ($var == "show" || $var == "hostattr" || $var == "serviceattr" || $var == "hoststatustypes" || $var == "servicestatustypes") {
            continue;
        }
        $theurl .= "&" . $var . "=" . $val;
    }

    $output .= '<img src="' . theme_image("filter.png") . '"> ' . _('Filters') . ':';
    $filters = "";

    if ($hostattr != 0 || ($hoststatustypes != 0 && $hoststatustypes != HOSTSTATE_ANY)) {
        $filters .= " <b>" . _('Host') . "</b>=";
        $filterstrs = array();

        if (($hoststatustypes & HOSTSTATE_UP))
            $filterstrs[] = _("Up");
        if (($hoststatustypes & HOSTSTATE_DOWN))
            $filterstrs[] = _("Down");
        if (($hoststatustypes & HOSTSTATE_UNREACHABLE))
            $filterstrs[] = _("Unreachable");
        if (($hostattr & HOSTSTATUSATTR_ACKNOWLEDGED))
            $filterstrs[] = _("Acknowledged");
        if (($hostattr & HOSTSTATUSATTR_NOTACKNOWLEDGED))
            $filterstrs[] = _("Not Acknowledged");
        if (($hostattr & HOSTSTATUSATTR_INDOWNTIME))
            $filterstrs[] = _("In Downtime");
        if (($hostattr & HOSTSTATUSATTR_NOTINDOWNTIME))
            $filterstrs[] = _("Not In Downtime");
        if (($hostattr & HOSTSTATUSATTR_ISFLAPPING))
            $filterstrs[] = _("Flapping");
        if (($hostattr & HOSTSTATUSATTR_ISNOTFLAPPING))
            $filterstrs[] = _("Not Flapping");
        if (($hostattr & HOSTSTATUSATTR_CHECKSENABLED))
            $filterstrs[] = _("Checks Enabled");
        if (($hostattr & HOSTSTATUSATTR_CHECKSDISABLED))
            $filterstrs[] = _("Checks Disabled");
        if (($hostattr & HOSTSTATUSATTR_NOTIFICATIONSENABLED))
            $filterstrs[] = _("Notifications Enabled");
        if (($hostattr & HOSTSTATUSATTR_NOTIFICATIONSDISABLED))
            $filterstrs[] = _("Notifications Disabled");
        if (($hostattr & HOSTSTATUSATTR_HARDSTATE))
            $filterstrs[] = _("Hard State");
        if (($hostattr & HOSTSTATUSATTR_SOFTSTATE))
            $filterstrs[] = _("Soft State");

        if (($hostattr & HOSTSTATUSATTR_EVENTHANDLERDISABLED))
            $filterstrs[] = _("Event Handler Disabled");
        if (($hostattr & HOSTSTATUSATTR_EVENTHANDLERENABLED))
            $filterstrs[] = _("Event Handler Enabled");
        if (($hostattr & HOSTSTATUSATTR_FLAPDETECTIONDISABLED))
            $filterstrs[] = _("Flap Detection Disabled");
        if (($hostattr & HOSTSTATUSATTR_FLAPDETECTIONENABLED))
            $filterstrs[] = _("Flap Detection Enabled");
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECKSDISABLED))
            $filterstrs[] = _("Passive Checks Disabled");
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECKSENABLED))
            $filterstrs[] = _("Passive Checks Enabled");
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECK))
            $filterstrs[] = _("Passive Check");
        if (($hostattr & HOSTSTATUSATTR_ACTIVECHECK))
            $filterstrs[] = _("Active Check");
        if (($hostattr & HOSTSTATUSATTR_HARDSTATE))
            $filterstrs[] = _("Hard State");
        if (($hostattr & HOSTSTATUSATTR_SOFTSTATE))
            $filterstrs[] = _("Soft State");

        $x = 0;
        foreach ($filterstrs as $f) {
            if ($x > 0)
                $filters .= ",";
            $filters .= $f;
            $x++;
        }
    }

    if ($serviceattr != 0 || ($servicestatustypes != 0 && $servicestatustypes != SERVICESTATE_ANY)) {
        $filters .= " <b>" . _('Service') . "</b>=";
        $filterstrs = array();

        if (($servicestatustypes & SERVICESTATE_OK))
            $filterstrs[] = _("Ok");
        if (($servicestatustypes & SERVICESTATE_WARNING))
            $filterstrs[] = _("Warning");
        if (($servicestatustypes & SERVICESTATE_UNKNOWN))
            $filterstrs[] = _("Unknown");
        if (($servicestatustypes & SERVICESTATE_CRITICAL))
            $filterstrs[] = _("Critical");
        if (($serviceattr & SERVICESTATUSATTR_ACKNOWLEDGED))
            $filterstrs[] = _("Acknowledged");
        if (($serviceattr & SERVICESTATUSATTR_NOTACKNOWLEDGED))
            $filterstrs[] = _("Not Acknowledged");
        if (($serviceattr & SERVICESTATUSATTR_INDOWNTIME))
            $filterstrs[] = _("In Downtime");
        if (($serviceattr & SERVICESTATUSATTR_NOTINDOWNTIME))
            $filterstrs[] = _("Not In Downtime");
        if (($serviceattr & SERVICESTATUSATTR_ISFLAPPING))
            $filterstrs[] = _("Flapping");
        if (($serviceattr & SERVICESTATUSATTR_ISNOTFLAPPING))
            $filterstrs[] = _("Not Flapping");
        if (($serviceattr & SERVICESTATUSATTR_CHECKSENABLED))
            $filterstrs[] = _("Checks Enabled");
        if (($serviceattr & SERVICESTATUSATTR_CHECKSDISABLED))
            $filterstrs[] = _("Checks Disabled");
        if (($serviceattr & SERVICESTATUSATTR_NOTIFICATIONSENABLED))
            $filterstrs[] = _("Notifications Enabled");
        if (($serviceattr & SERVICESTATUSATTR_NOTIFICATIONSDISABLED))
            $filterstrs[] = _("Notifications Disabled");
        if (($serviceattr & SERVICESTATUSATTR_HARDSTATE))
            $filterstrs[] = _("Hard State");
        if (($serviceattr & SERVICESTATUSATTR_SOFTSTATE))
            $filterstrs[] = _("Soft State");

        if (($serviceattr & SERVICESTATUSATTR_EVENTHANDLERDISABLED))
            $filterstrs[] = _("Event Handler Disabled");
        if (($serviceattr & SERVICESTATUSATTR_EVENTHANDLERENABLED))
            $filterstrs[] = _("Event Handler Enabled");
        if (($serviceattr & SERVICESTATUSATTR_FLAPDETECTIONDISABLED))
            $filterstrs[] = _("Flap Detection Disabled");
        if (($serviceattr & SERVICESTATUSATTR_FLAPDETECTIONENABLED))
            $filterstrs[] = _("Flap Detection Enabled");
        if (($serviceattr & SERVICESTATUSATTR_PASSIVECHECKSDISABLED))
            $filterstrs[] = _("Passive Checks Disabled");
        if (($serviceattr & SERVICESTATUSATTR_PASSIVECHECKSENABLED))
            $filterstrs[] = _("Passive Checks Enabled");
        if (($serviceattr & SERVICESTATUSATTR_PASSIVECHECK))
            $filterstrs[] = _("Passive Check");
        if (($serviceattr & SERVICESTATUSATTR_ACTIVECHECK))
            $filterstrs[] = _("Active Check");
        if (($serviceattr & SERVICESTATUSATTR_HARDSTATE))
            $filterstrs[] = _("Hard State");
        if (($serviceattr & SERVICESTATUSATTR_SOFTSTATE))
            $filterstrs[] = _("Soft State");

        $x = 0;
        foreach ($filterstrs as $f) {
            if ($x > 0)
                $filters .= ",";
            $filters .= $f;
            $x++;
        }
    }

    $output .= $filters;
    $output .= " <a href='" . $theurl . "'><img src='" . theme_image("clearfilter.png") . "' class='tt-bind' data-placement='right' alt='" . _("Clear Filter") . "' title='" . _("Clear Filter") . "'></a>";

    return $output;
}


/**
 * Used for stripping html out of custom data sent with table_data/table_header callbacks
 *
 * @param   bool    $allow_html     True to allow HTML
 * @param   string  $data           HTML data
 * @return  string                  HTML
 */
function strip_html_from_table_data($allow_html, $data)
{
    $output = $data;

    if (!$allow_html) {
        $output = "";

        // If we don't allow html, we need to still allow td and th tags, otherwise whats the point?
        $data_array_split_by_td_tags = preg_split("/(<[tT][dD].*?>|<\/[tT][dD]>|<[tT][hH].*?>|<\/[tT][hH]>)/", $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (count($data_array_split_by_td_tags) > 0) {
            foreach ($data_array_split_by_td_tags as $data_element) {

                if (preg_match("/^<[tT][dD]|<[tT][hH]|<\/[tT][dD]|<\/[tT][hH]/", $data_element) === 1) {
                    $output .= $data_element;
                    continue;
                }

                $output .= encode_form_val($data_element);

            }
        } else {
            $output .= $data;
        }
    }

    return $output;
}


/**
 * Used for stripping non-img tags out of custom data sent with table_icons callbacks
 *
 * @param   bool    $allow_html     True to allow HTML
 * @param   string  $data           HTML data
 * @return  string                  HTML
 */
function strip_non_img_from_table_icons($allow_html, $icons)
{
    $output = $icons;

    if (!$allow_html) {
        $output = "";

        // Only include img tags - don't use php's strip_tags since < 5.3.4 doesn't handle self closing html tags properly
        if (preg_match_all("/<[iI][mM][gG]\s.*?>/", $icons, $img_matches) > 0) {
            debug($img_matches);
            foreach($img_matches[0] as $img) {
                $output .= $img;
            }
        }
    }

    return $output;
}


/**
 * Draw the status map view links
 */
function draw_statusmap_viewstyle_links()
{
    echo "<div class='statusdetaillinks'>";
    echo "<div class='statusdetaillink'><a href='" . get_statusmap_link(6) . "'><img src='" . theme_image("statusmapballoon.png") . "' alt='" . _("View Balloon Map") . "' title='" . _("View Balloon Map") . "'></a></div>";
    echo "<div class='statusdetaillink'><a href='" . get_statusmap_link(3) . "'><img src='" . theme_image("statusmaptree.png") . "' alt='" . _("View Tree Map") . "' title='" . _("View Tree Map") . "'></a></div>";
    echo "</div>";
}


/**
 * Replace *SOME* macros with ones that are available from
 * the initial data that we grabbed. This is used in the host and
 * service status tables and status details pages.
 *
 * Host macros useable:
 *      $HOSTNAME$, $HOSTALIAS$, $HOSTADDRESS$, $HOSTDISPLAYNAME$,
 *      $HOSTSTATE$, $HOSTSTATEID$, $SERVICEOUTPUT$
 * 
 * Service macros useable (all the hosts and below if $hs is true):
 *      $SERVICEDESC$, $SERVICEDISPLAYNAME$, $SERVICESTATE$, 
 *      $SERVICESTATEID$, $SERVICEOUTPUT$
 *
 * @param   string  $text   The text to replace macros in
 * @param   object  $obj    The object data to use for replacement
 * @param   bool    $hs     Host and service status (appends host_ to most host options)
 * @return  string          String with macros replaced
 */
function xicore_replace_macros($text, $obj = null, $hs = false)
{
    // Skip empty object data and text with no macros in it
    if (strpos($text, '$') === false || empty($obj)) {
        return $text;
    }

    // Generate a list of macros and replacements to split later
    $macros = array();

    // Set prefix if we are using a hostservices object
    $prefix = '';
    if ($hs) {
        $prefix = 'host_';
    }

    // Add host macros to the list
    $macros['$HOSTNAME$'] = strval($obj->{"$prefix"."name"});
    $macros['$HOSTALIAS$'] = strval($obj->{"$prefix"."alias"});
    $macros['$HOSTADDRESS$'] = strval($obj->{"$prefix"."address"});
    $macros['$HOSTDISPLAYNAME$'] = strval($obj->{"$prefix"."display_name"});
    $macros['$HOSTSTATE$'] = strtoupper(xicore_get_state_from_stateid(strval($obj->{"$prefix"."current_state"}), 'host'));
    $macros['$HOSTSTATEID$'] = strval($obj->{"$prefix"."current_state"});
    $macros['$SERVICEOUTPUT$'] = strval($obj->{"$prefix"."status_text"});

    // Add service macros to the list
    if ($hs) {

        $macros['$SERVICEDESC$'] = strval($obj->name);
        $macros['$SERVICEDISPLAYNAME$'] = strval($obj->display_name);
        $macros['$SERVICESTATE$'] = strtoupper(xicore_get_state_from_stateid(strval($obj->current_state), 'service'));
        $macros['$SERVICESTATEID$'] = strval($obj->current_state);
        $macros['$SERVICEOUTPUT$'] = strval($obj->status_text);

    }

    $text = str_replace(array_keys($macros), array_values($macros), $text);
    return $text;
}


/**
 * Get the state of an object
 *
 * @param   int     $state_id   State ID
 * @param   string  $type       Type of object (host, service)
 * @return  string              The object state
 */
function xicore_get_state_from_stateid($state_id, $type = 'service')
{
    $state = '';

    if ($type == 'service') {
        switch ($state_id) {

            case 0:
                $state = _('Ok');
                break;

            case 1:
                $state = _('Warning');
                break;

            case 2:
                $state = _('Critical');
                break;

            case 3:
                $state = _('Unknown');
                break;

        }
    } else if ($type == 'host') {
        switch ($state_id) {

            case 0:
                $state = _('Up');
                break;

            case 1:
                $state = _('Down');
                break;

            case 2:
                $state = _('Unreachable');
                break;

        }
    }

    return $state;
}
