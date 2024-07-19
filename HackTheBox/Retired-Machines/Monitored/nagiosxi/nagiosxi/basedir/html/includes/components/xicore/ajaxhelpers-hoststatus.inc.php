<?php
//
// XI Core Ajax Helper Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


////////////////////////////////////////////////////////////////////////
// STATUS AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get host status table HTML
 *
 * @param   array   $args   Object arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_hoststatus_table($args = array())
{
    $url = get_base_url() . "includes/components/xicore/status.php";

    $sortby = "host_name:a";
    $sortorder = "asc";
    $page = 1;
    $records = 25;
    $search = "";
    $allow_html = get_option('allow_status_html', false);

    $show = grab_array_var($args, "show", "hosts");
    $host = grab_array_var($args, "host", "");
    $hostgroup = grab_array_var($args, "hostgroup", "");
    $servicegroup = grab_array_var($args, "servicegroup", "");
    $hostattr = grab_array_var($args, "hostattr", 0);
    $serviceattr = grab_array_var($args, "serviceattr", 0);
    $hoststatustypes = grab_array_var($args, "hoststatustypes", 0);
    $servicestatustypes = grab_array_var($args, "servicestatustypes", 0);

    $sortby = grab_array_var($args, "sortby", $sortby);
    $sortorder = grab_array_var($args, "sortorder", $sortorder);
    $records = grab_array_var($args, "records", $records);
    $page = grab_array_var($args, "page", $page);
    $search = trim(grab_array_var($args, "search", $search));
    if ($search == _("Search...")) {
        $search = "";
    }

    if ($host == "all") {
        $host = "";
    }

    // Limit hosts by hostgroup or host
    $host_ids = array();
    if (!empty($hostgroup)) {
        $host_ids = get_hostgroup_member_ids($hostgroup);
    } else if (!empty($servicegroup)) {
        $host_ids = get_servicegroup_host_member_ids($servicegroup);
    } else if (!empty($host)) {
        $host_ids[] = get_host_id($host);
    }

    $output = "";

    // PREP TO GET DATA FROM BACKEND...

    $backendargs = array();
    $backendargs["cmd"] = "gethoststatus";

    // Order criteria
    if (!empty($sortby)) {
        $backendargs["orderby"] = $sortby;
        if (isset($sortorder) && $sortorder == "desc") {
            $backendargs["orderby"] .= ":d";
        } else {
            $backendargs["orderby"] .= ":a";
        }
    } else {
        if ($sortorder == "desc") {
            $backendargs["orderby"] = "host_name:d";
        } else {
            $backendargs["orderby"] = "host_name:a";
        }
    }

    // Host ID limiters
    if (!empty($host_ids)) {
        $backendargs["host_id"] = "in:" . implode(',', $host_ids);
    }

    // Search criteria
    if (!empty($search)) {
        $backendargs["name"] = "lk:" . $search . ";alias=lk:" . $search . ";address=lk:" . $search . ";display_name=lk:" . $search;
    }

    // Host status limiters
    if ($hoststatustypes != 0 && $hoststatustypes != HOSTSTATE_ANY) {
        $hoststatus_ids = array();
        if (($hoststatustypes & HOSTSTATE_UP)) {
            $hoststatus_ids[] = 0;
            $backendargs["has_been_checked"] = 1;
        } else if (($hoststatustypes & HOSTSTATE_PENDING)) {
            $hoststatus_ids[] = 0;
            $backendargs["has_been_checked"] = 0;
        }
        if (($hoststatustypes & HOSTSTATE_DOWN)) {
            $hoststatus_ids[] = 1;
        }
        if (($hoststatustypes & HOSTSTATE_UNREACHABLE)) {
            $hoststatus_ids[] = 2;
        }
        $backendargs["current_state"] = "in:" . implode(',', $hoststatus_ids);
    }

    // Host attribute limiters
    if ($hostattr != 0) {
        if (($hostattr & HOSTSTATUSATTR_ACKNOWLEDGED))
            $backendargs["problem_acknowledged"] = 1;
        if (($hostattr & HOSTSTATUSATTR_NOTACKNOWLEDGED))
            $backendargs["problem_acknowledged"] = 0;
        if (($hostattr & HOSTSTATUSATTR_INDOWNTIME))
            $backendargs["scheduled_downtime_depth"] = "gte:1";
        if (($hostattr & HOSTSTATUSATTR_NOTINDOWNTIME))
            $backendargs["scheduled_downtime_depth"] = 0;
        if (($hostattr & HOSTSTATUSATTR_ISFLAPPING))
            $backendargs["is_flapping"] = 1;
        if (($hostattr & HOSTSTATUSATTR_ISNOTFLAPPING))
            $backendargs["is_flapping"] = 0;
        if (($hostattr & HOSTSTATUSATTR_CHECKSDISABLED))
            $backendargs["active_checks_enabled"] = 0;
        if (($hostattr & HOSTSTATUSATTR_CHECKSENABLED))
            $backendargs["active_checks_enabled"] = 1;
        if (($hostattr & HOSTSTATUSATTR_NOTIFICATIONSDISABLED))
            $backendargs["notifications_enabled"] = 0;
        if (($hostattr & HOSTSTATUSATTR_NOTIFICATIONSENABLED))
            $backendargs["notifications_enabled"] = 1;
        if (($hostattr & HOSTSTATUSATTR_HARDSTATE))
            $backendargs["state_type"] = 1;
        if (($hostattr & HOSTSTATUSATTR_SOFTSTATE))
            $backendargs["state_type"] = 0;

        // These may not all be implemented by the backend yet...
        if (($hostattr & HOSTSTATUSATTR_EVENTHANDLERDISABLED))
            $backendargs["event_handler_enabled"] = 0;
        if (($hostattr & HOSTSTATUSATTR_EVENTHANDLERENABLED))
            $backendargs["event_handler_enabled"] = 1;
        if (($hostattr & HOSTSTATUSATTR_FLAPDETECTIONDISABLED))
            $backendargs["flap_detection_enabled"] = 0;
        if (($hostattr & HOSTSTATUSATTR_FLAPDETECTIONENABLED))
            $backendargs["flap_detection_enabled"] = 1;
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECKSDISABLED))
            $backendargs["passive_checks_enabled"] = 0;
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECKSENABLED))
            $backendargs["passive_checks_enabled"] = 1;
        if (($hostattr & HOSTSTATUSATTR_PASSIVECHECK))
            $backendargs["check_type"] = 0;
        if (($hostattr & HOSTSTATUSATTR_ACTIVECHECK))
            $backendargs["check_type"] = 1;
    }

    // FIRST GET TOTAL RECORD COUNT FROM BACKEND...

    $backendargs["cmd"] = "gethoststatus";
    $backendargs["limitrecords"] = false;
    $backendargs["totals"] = 1;

    // Get result from backend
    $xml = get_xml_host_status($backendargs);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // GET RECORDS FROM BACKEND...

    unset($backendargs["limitrecords"]);
    unset($backendargs["totals"]);

    // Check page (set to max page if it was higher)
    $max_page = ceil($total_records / $records);
    if ($page > $max_page) {
        $page = $max_page;
    }

    // Record-limiters
    $backendargs["records"] = $records . ":" . (($page - 1) * $records);

    // Get result from backend
    $xml = get_xml_host_status($backendargs);

    // Get comments - we need this later...
    $backendargs = array();
    $backendargs["cmd"] = "getcomments";
    $backendargs["brevity"] = 1;
    $backendargs["orderby"] = 'comment_id:a';

    // Get only the current object's IDs for comment query
    $obj_ids = array();
    foreach ($xml->hoststatus as $x) {
        $host_id = intval($x->host_id);
        if (!@isset($objs_ids[$host_id])) {
            $obj_ids[] = $host_id;
        }
    }

    // Do the actual object_id limit if we have any to limit by
    if (!empty($obj_ids)) {
        $backendargs["object_id"] = "in:" . implode(',', $obj_ids);
    }

    $commentsxml = get_xml_comments($backendargs);

    $comments = array();
    if ($commentsxml != null) {
        foreach ($commentsxml->comment as $c) {
            $objid = intval($c->object_id);
            $comments[$objid] = $c->comment_data;
        }
    }

    // Get table paging info - reset page number if necessary
    $pager_args = array(
        "sortby" => $sortby,
        "sortorder" => $sortorder,
        "search" => $search,
        "show" => $show,
        "hoststatustypes" => $hoststatustypes,
        "hostattr" => $hostattr,
        "host" => $host,
        "hostgroup" => $hostgroup,
        "servicegroup" => $servicegroup
    );
    $pager_results = get_table_pager_info($url, $total_records, $page, $records, $pager_args);

    $output .= "<form action='" . $url . "'>";
    $output .= "<input type='hidden' name='show' value=\"" . encode_form_val($show) . "\">\n";
    $output .= "<input type='hidden' name='sortby' value=\"" . encode_form_val($sortby) . "\">\n";
    $output .= "<input type='hidden' name='sortorder' value=\"" . encode_form_val($sortorder) . "\">\n";
    $output .= "<input type='hidden' name='host' value=\"" . encode_form_val($host) . "\">\n";
    $output .= "<input type='hidden' name='hostgroup' value=\"" . encode_form_val($hostgroup) . "\">\n";
    $output .= "<input type='hidden' name='servicegroup' value=\"" . encode_form_val($servicegroup) . "\">\n";
    $output .= "<input type='hidden' name='hoststatustypes' value=\"" . encode_form_val($hoststatustypes) . "\">\n";
    $output .= "<input type='hidden' name='servicestatustypes' value=\"" . encode_form_val($servicestatustypes) . "\">\n";
    $output .= "<input type='hidden' name='hostattr' value=\"" . encode_form_val($hostattr) . "\">\n";
    $output .= "<input type='hidden' name='serviceattr' value=\"" . encode_form_val($serviceattr) . "\">\n";

    $output .= '<div id="statusTableContainer" class="tableContainer">';

    $output .= '<div class="tableHeader">';

    $filterargs = array(
        "host" => $host,
        "hostgroup" => $hostgroup,
        "servicegroup" => $servicegroup,
        "search" => $search,
        "show" => $show
    );
    $output .= get_status_view_filters_html($show, $filterargs, $hostattr, $serviceattr, $hoststatustypes, $servicestatustypes, $url);

    $output .= '</div>';

    $id = "hoststatustable_" . random_string(6);

    $clear_args = array(
        "sortby" => $sortby,
        "search" => "",
        "show" => $show,
        "hoststatustypes" => $hoststatustypes,
        "servicestatustypes" => $servicestatustypes
    );
    $output .= '<div class="xi-table-box"><div class="xi-table-recordcount">'.table_record_count_text($pager_results, $search, true, $clear_args, $url).'</div>';

    $records_options = array("5", "10", "15", "25", "50", "100", "250", "500", "1000");
    $output .= get_table_record_pager($pager_results, $records_options);

    $output .= '
        <div class="servicestatustablesearch">
            <input type="text" size="15" name="search" id="status-search-box" value="'.encode_form_val($search).'" class="form-control condensed" placeholder="'._('Search').'...">
            <button type="submit" class="btn btn-xs btn-default" name="searchButton" id="searchButton"><i class="fa fa-search"></i></button>
        </div></div>';

    // Get custom column headers
    $cbdata = array(
        "objecttype" => OBJECTTYPE_HOST,
        "table_headers" => array(),
        "allow_html" => $allow_html
    );
    do_callbacks(CALLBACK_CUSTOM_TABLE_HEADERS, $cbdata);
    $customheaders = grab_array_var($cbdata, "table_headers", array());

    $headercols = count($customheaders) + 6;

    $output .= "<table class='tablesorter hoststatustable table table-condensed table-striped table-bordered' id='" . $id . "'>\n";
    $output .= "<thead>";
    $output .= "<tr>";

    // Extra args for sorted table header
    $extra_args = array();
    foreach ($args as $var => $val) {
        if ($var == "sortby" || $var == "sortorder") {
            continue;
        }
        $extra_args[$var] = $val;
    }
    $extra_args["show"] = "hosts";

    // Sorted table header
    $output .= sorted_table_header($sortby, $sortorder, "name", _("Host"), $extra_args, "", $url);
    $output .= sorted_table_header($sortby, $sortorder, "current_state", _("Status"), $extra_args, "", $url);
    $output .= sorted_table_header($sortby, $sortorder, "last_state_change", _("Duration"), $extra_args, "", $url);
    $output .= sorted_table_header($sortby, $sortorder, "current_check_attempt", _("Attempt"), $extra_args, "", $url);
    $output .= sorted_table_header($sortby, $sortorder, "last_check", _("Last Check"), $extra_args, "", $url);
    $output .= sorted_table_header($sortby, $sortorder, "status_text", _("Status Information"), $extra_args, "", $url);

    // Add custom headers if they exist
    foreach ($customheaders as $header) {
        if (!empty($header)) {
            $output .= strip_html_from_table_data($allow_html, $header);
        }
    }

    // Close table headers
    $output .= "</tr>\n";
    $output .= "</thead>\n";
    $output .= "<tbody>\n";

    $last_host_name = "";
    $current_host = 0;
    $display_host_display_name = get_user_meta(0, 'display_host_display_name');

    foreach ($xml->hoststatus as $x) {
        $current_host++;

        if (($current_host % 2) == 0) {
            $rowclass = "even";
        } else {
            $rowclass = "odd";
        }

        // Get the host name
        $host_name = strval($x->name);
        $address = strval($x->address);
        if ($last_host_name != $host_name) {
            $display_host_name = $host_name;
        } else {
            $display_host_name = "";
        }
        $last_host_name = $host_name;

        // Host status
        $host_current_state = intval($x->current_state);
        switch ($host_current_state) {
            case 0:
                $status_string = _("Up");
                $host_status_class = "hostup";
                break;
            case 1:
                $status_string = _("Down");
                $host_status_class = "hostdown";
                break;
            case 2:
                $status_string = _("Unreachable");
                $host_status_class = "hostunreachable";
                break;
            default:
                $status_string = "";
                $host_status_class = "";
                break;
        }

        $has_been_checked = intval($x->has_been_checked);
        if ($has_been_checked == 0) {
            $status_string = _("Pending");
            $host_status_class = "hostpending";
        }

        // Host name cell
        $host_name_cell = "";

        if (!empty($display_host_name)) {

            $host_icons = "";

            // Host comments
            if (array_key_exists(intval($x->host_id), $comments)) {
                $host_icons .= get_host_status_note_image("hascomments.png", $comments[intval($x->host_id)]);
            }

            // Flapping
            if (intval($x->is_flapping) == 1) {
                $host_icons .= get_host_status_note_image("flapping.png", _("This host is flapping"));
            }

            // Acknowledged
            if (intval($x->problem_acknowledged) == 1) {
                $host_icons .= get_host_status_note_image("ack.png", _("This host problem has been acknowledged"));
            }

            $passive_checks_enabled = intval($x->passive_checks_enabled);
            $active_checks_enabled = intval($x->active_checks_enabled);

            // Passive only
            if ($active_checks_enabled == 0 && $passive_checks_enabled == 1) {
                $host_icons .= get_host_status_note_image("passiveonly.png", _("Passive Only Check"));
            }

            // Notifications
            if (intval($x->notifications_enabled) == 0) {
                $host_icons .= get_host_status_note_image("nonotifications.png", _("Notifications are disabled for this host"));
            }

            // Downtime
            if (intval($x->scheduled_downtime_depth) > 0) {
                $host_icons .= get_host_status_note_image("downtime.png", _("This host is in scheduled downtime"));
            }

            // Host icon
            $host_icons .= get_object_icon_html($x->icon_image, $x->icon_image_alt);

            // Notes URL icon/link HTML
            if (!empty($x->notes_url)) {
                $host_icons .= get_notes_url_html(xicore_replace_macros($x->notes_url, $x));
            }

            // Action URL icon/link HTML
            if (!empty($x->action_url)) {
                $host_icons .= get_action_url_html(xicore_replace_macros($x->action_url, $x));
            }

            $detail_url = get_host_status_detail_link($host_name);

            // Show display name if it exists
            $show_host_name = $host_name;

            if ($display_host_display_name && !empty($x->display_name)) {
                $show_host_name = $x->display_name;
            }

            // Add alias to the output
            if (!empty($x->alias) && $host_name != $x->alias) {
                $show_host_name .= " (". strval($x->alias) . ")";
            }

            $host_name_cell .= "<div class='hostname'><a href='" . $detail_url . "' title='" . $address . "'>" . $show_host_name . "</a></div>";

            // Host icons
            $extra_icons = '';
            $cbdata = array(
                "objecttype" => OBJECTTYPE_HOST,
                "host_name" => $host_name,
                "object_id" => intval($x->host_id),
                "object_data" => $x,
                'allow_html' => $allow_html,
                "icons" => array()
            );
            do_callbacks(CALLBACK_CUSTOM_TABLE_ICONS, $cbdata);
            $custom_icons = grab_array_var($cbdata, "icons", array());

            // Add custom icons if they exist
            foreach ($custom_icons as $icon) {
                if (!empty($icon)) {
                    $extra_icons .= strip_non_img_from_table_icons($allow_html, $icon);
                }
            }

            // Add icons and custom icons
            $host_name_cell .= '<div class="extraicons">' . $extra_icons . '</div>';
            $host_name_cell .= '<div class="hosticons"><a href="' . get_host_status_detail_link($host_name) . '">' . $host_icons . '</a>';

            // Service details link
            $url = get_base_url() . "includes/components/xicore/status.php?show=services&host=".urlencode($host_name);
            $alttext = _("View service status details for this host");
            
            // FIRST GET TOTAL RECORD COUNT FROM BACKEND...

            $backendargs["limitrecords"] = false;
            $backendargs["totals"] = 1;
            $backendargs["host_name"] = $host_name;
            $xml = get_xml_service_status($backendargs);

            $service_count = 0;
            if ($xml) {
                $service_count = intval($xml->recordcount);
            }
        
            if ($service_count > 0) {
                $host_name_cell .= "<a href='" . $url . "'><img src='" . theme_image("statusdetailmulti.png") . "' alt='" . $alttext . "' title='" . $alttext . "'></a>";
            }
            
            $host_name_cell .= "</div>";

        } else {
            $host_status_class = "empty";
        }

        // Status cell
        $status_cell = "";
        $status_cell .= $status_string;

        // Last check
        $last_check_time = strval($x->last_check);
        $last_check = get_datetime_string_from_datetime($last_check_time, "", DT_SHORT_DATE_TIME, DF_AUTO, "N/A");

        // Current attempt
        $current_attempt = intval($x->current_check_attempt);
        $max_attempts = intval($x->max_check_attempts);

        // Last state change / duration
        $last_state_change_time = strtotime($x->last_state_change);
        if ($last_state_change_time == 0) {
            $statedurationstr = "N/A";
        } else {
            $statedurationstr = get_duration_string(time() - $last_state_change_time);
        }

        // Status information
        $status_info = strval($x->status_text);
        if ($allow_html) {
            $status_info = html_entity_decode($status_info);
        }

        // Check to see if it is still in pending state
        if ($has_been_checked == 0) {
            $should_be_scheduled = intval($x->should_be_scheduled);
            $next_check_time = strval($x->next_check);
            $next_check = get_datetime_string_from_datetime($next_check_time, "", DT_SHORT_DATE_TIME, DF_AUTO, "N/A");
            if ($should_be_scheduled == 1) {
                $status_info = _("Host check is pending...");
                if (strtotime($next_check_time) != 0) {
                    $status_info .= _(" Check is scheduled for ") . $next_check;
                }
            } else {
                $status_info = _("No check results for host yet...");
            }
        }

        // Table row output
        $output .= "<tr class='" . $rowclass . "'>
                    <td style='white-space: nowrap'>" . $host_name_cell . "</td>
                    <td class='" . $host_status_class . "'>" . $status_cell . "</td>
                    <td nowrap>" . $statedurationstr . "</td>
                    <td>" . $current_attempt . "/" . $max_attempts . "</td>
                    <td nowrap>" . $last_check . "</td>
                    <td>" . $status_info . "</td>";

        // Add custom columns
        unset($cbdata["icons"]);
        $cbdata["table_data"] = array();
        $cbdata["allow_html"] = $allow_html;

        do_callbacks(CALLBACK_CUSTOM_TABLE_DATA, $cbdata);
        $custom_data = grab_array_var($cbdata, "table_data", array());

        // Add custom data if it exists
        foreach ($custom_data as $data) {
            if (!empty($data)) {
                $output .= strip_html_from_table_data($allow_html, $data);
            }
        }

        $output .= "</tr>";
    }

    if ($current_host == 0) {
        $output .= "<tr><td colspan='".$headercols."'>" . _("No matching host found") . "</td></tr>";
    }

    $output .= "</tbody>";
    $output .= "</table>";

    $records_options = array("5", "10", "15", "25", "50", "100", "250", "500", "1000");
    $output .= '<div class="xi-table-box">'.get_table_record_pager($pager_results, $records_options).'</div>';
    $output .= '<div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>';
    $output .= "</form>\n";
    $output .= "</div><script>init_sorted_tables();</script><!-- tableContainer -->";

    return $output;
}


/**
 * Get quick action HTML for host object
 *
 * @param   array   $args   Object arugments
 * @return  string          HTML output
 */
function xicore_ajax_get_host_status_quick_actions_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    }

    $auth_command = is_authorized_for_host_command(0, $hostname);

    // Get host status
    $args = array(
        "cmd" => "gethoststatus",
        "host_id" => $host_id
    );
    $xml = get_xml_host_status($args);

    $output = '';
    if ($xml) {

        if ($auth_command) {

            // Initialze some stuff we'll use a few times...
            $cmd = array("command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND);
            $cmd["command_args"] = array(
                "host_name" => $hostname
            );

            // Acknowledge problem
            if (intval($xml->hoststatus->current_state) != 0 && intval($xml->hoststatus->problem_acknowledged) == 0) {
                $urlbase = get_base_url() . "includes/components/nagioscore/ui/cmd.php?cmd_typ=";
                $urlmod = "&host=" . urlencode($hostname) . "&com_data=Problem has been acknowledged";
                $output .= '<li><a class="cmdlink" data-modal="add-ack" data-cmd-type="'.NAGIOSCORE_CMD_ACKNOWLEDGE_HOST_PROBLEM.'"><img src="'.theme_image('ack_add.png').'">'._('Acknowledge this problem').'</a></li>';
            }

            // Notifications
            if (intval($xml->hoststatus->notifications_enabled) == 1) {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_NOTIFICATIONS;
                $output .= '<li>' . get_host_detail_command_link($cmd, "nonotifications.png", _("Disable notifications")) . '</li>';
            } else {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_NOTIFICATIONS;
                $output .= '<li>' . get_host_detail_command_link($cmd, "enablenotifications.png", _("Enable notifications")) . '</li>';
            }

            // Force immediate check
            $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_CHECK;
            $cmd["command_args"]["start_time"] = time();
            $output .= '<li>' . get_host_detail_command_link($cmd, "arrow_refresh.png", _("Force an immediate check")) . '</li>';
        }

        // Additional actions...
        $cbdata = array(
            "hostname" => $hostname,
            "host_id" => $host_id,
            "hoststatus_xml" => $xml,
            "actions" => array(),
        );
        do_callbacks(CALLBACK_HOST_DETAIL_ACTION_LINK, $cbdata);
        $customactions = grab_array_var($cbdata, "actions", array());
        foreach ($customactions as $ca) {
            $output .= $ca;
        }

    }

    if (empty($output)) {
        $output = "<li>" . _('No actions are available') . "</li>";
    }

    return $output;
}


/**
 * Get host notes, links, and action links
 *
* @param   array   $args       Array of arguments
 * @return  string              HTML output
 */
function xicore_ajax_get_host_status_misc_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    }

    $auth_command = is_authorized_for_host_command(0, $hostname);

    // Get host status
    $args = array(
        "cmd" => "gethoststatus",
        "host_id" => $host_id
    );
    $xml = get_xml_host_status($args);

    $output = '';
    if ($xml) {
        $notes = strval($xml->hoststatus->notes);

        // Notes is escaped beacuse it's using XML version of API
        // and must be escaped if it is cahnged to use JSON version...
        if (!empty($notes)) {
            $output = '<div class="status-misc-top">' . $notes . '</div>';
        }

        // Notes URL icon/link HTML
        if (!empty($xml->hoststatus->notes_url)) {
            $url = str_replace('&amp;', '&', encode_form_val(xicore_replace_macros($xml->hoststatus->notes_url, $xml->hoststatus)));
            $output .= '<div class="status-misc"><a href="' . $url . '" target="_new"><img src="'.theme_image('page_white_go.png').'"></a> <a href="' . $url . '" target="_new">' . _('Notes URL') . '</a></div>';
        }

        // Action URL icon/link HTML
        if (!empty($xml->hoststatus->action_url)) {
            $url = str_replace('&amp;', '&', encode_form_val(xicore_replace_macros($xml->hoststatus->action_url, $xml->hoststatus)));
            $output .= '<div class="status-misc"><a href="' . $url . '" target="_new"><img src="'.theme_image('resultset_next.png').'"></a> <a href="' . $url . '" target="_new">' . _('Actions URL') . '</a></div>';
        }
    }

    if (empty($output)) {
        $output = '<div class="status-misc-top">' . _('No notes or misc info') . '</div>';
    }

    return $output;
}


/**
 * Get host status detailed info HTML
 *
 * @param   array   $args   Object arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_host_status_detailed_info_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);
    $display = grab_array_var($args, "display", "simple");

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get host status
    $args = array(
        "cmd" => "gethoststatus",
        "host_id" => $host_id
    );
    $xml = get_xml_host_status($args);

    $output = '';
    if ($xml) {

        $current_state = intval($xml->hoststatus->current_state);
        $has_been_checked = intval($xml->hoststatus->has_been_checked);

        switch ($current_state) {
            case 0:
                $statestr = _("Up");
                $statecolor = 'fa-ok';
                break;
            case 1:
                $statestr = _("Down");
                $statecolor = 'fa-critical';
                break;
            case 2:
                $statestr = _("Unreachable");
                $statecolor = 'fa-unknown';
                break;
            default:
                break;
        }

        if ($has_been_checked == 0) {
            $statestr = _("Pending");
            $statecolor = 'fa-pending';
        }

        if ($display == "advanced") {
            $title = _("Advanced Status Details");
        } else {
            $title = _("Status Details");
        }

        $output .= '
        <div style="float: left; margin-right: 25px;">
        <div class="infotable_title">' . $title . '</div>
        <table class="infotable table table-condensed table-striped table-bordered" style="width: 400px;">
        <thead>
        </thead>
        <tbody>
        ';

        $output .= '<tr><td style="width: 140px;">' . _('Host State') . ':</td><td><i class="fa fa-circle ' . $statecolor . '"></i> ' . $statestr . '</td></tr>';

        $last_state_change_time = strtotime($xml->hoststatus->last_state_change);
        if ($last_state_change_time == 0) {
            $statedurationstr = "N/A";
        } else {
            $statedurationstr = get_duration_string(time() - $last_state_change_time);
        }
        $output .= '<tr><td>' . _('Duration') . ':</td><td>' . $statedurationstr . '</td></tr>';

        $state_type = intval($xml->hoststatus->state_type);
        if ($display == "advanced") {
            if ($state_type == STATETYPE_HARD) {
                $statetypestr = _("Hard");
            } else {
                $statetypestr = _("Soft");
            }
            $output .= '<tr><td>' . _('State Type') . ':</td><td>' . $statetypestr . '</td></tr>';
            $output .= '<tr><td>' . _('Current Check') . ':</td><td>' . $xml->hoststatus->current_check_attempt . ' of ' . $xml->hoststatus->max_check_attempts . '</td></tr>';
        } else {
            if ($state_type == STATETYPE_SOFT) {
                $output .= '<tr><td>' . _('Host Stability') . ':</td><td>Changing</td></tr>';
                $output .= '<tr><td>' . _('Current Check') . ':</td><td>' . $xml->hoststatus->current_check_attempt . ' of ' . $xml->hoststatus->max_check_attempts . '</td></tr>';
            } else {
                $output .= '<tr><td>' . _('Host Stability') . ':</td><td>' . _('Unchanging (stable)') . '</td></tr>';
            }
        }

        $lastcheck = get_datetime_string_from_datetime($xml->hoststatus->last_check, "", DT_SHORT_DATE_TIME, DF_AUTO, "Never");
        $output .= '<tr><td>' . _('Last Check') . ':</td><td>' . $lastcheck . '</td></tr>';

        $nextcheck = get_datetime_string_from_datetime($xml->hoststatus->next_check, "", DT_SHORT_DATE_TIME, DF_AUTO, "Not scheduled");
        $output .= '<tr><td>' . _('Next Check') . ':</td><td>' . $nextcheck . '</td></tr>';

        if ($display == "advanced") {

            $laststatechange = get_datetime_string_from_datetime($xml->hoststatus->last_state_change, "", DT_SHORT_DATE_TIME, DF_AUTO, "Never");
            $output .= '<tr><td nowrap>' . _('Last State Change') . ':</td><td>' . $laststatechange . '</td></tr>';

            $lastnotification = get_datetime_string_from_datetime($xml->hoststatus->last_notification, "", DT_SHORT_DATE_TIME, DF_AUTO, "Never");
            $output .= '<tr><td>' . _('Last Notification') . ':</td><td>' . $lastnotification . '</td></tr>';

            if ($xml->hoststatus->check_type == ACTIVE_CHECK) {
                $checktype = _("Active");
            } else {
                $checktype = _("Passive");
            }

            $output .= '<tr><td valign="top" nowrap>' . _('Check Type') . ':</td><td>' . $checktype . '</td></tr>';
            $output .= '<tr><td valign="top" nowrap>' . _('Check Latency') . ':</td><td>' . $xml->hoststatus->latency . ' ' . _('seconds') . '</td></tr>';
            $output .= '<tr><td valign="top" nowrap>' . _('Execution Time') . ':</td><td>' . $xml->hoststatus->execution_time . ' ' . _('seconds') . '</td></tr>';
            $output .= '<tr><td valign="top" nowrap>' . _('State Change') . ':</td><td>' . $xml->hoststatus->percent_state_change . '%</td></tr>';
            $output .= '<tr><td valign="top" nowrap>' . _('Performance Data') . ':</td><td>' . $xml->hoststatus->performance_data . '</td></tr>';
        }

        $notesoutput = "";

        // Acknowledge problems
        if (intval($xml->hoststatus->problem_acknowledged) == 1) {
            $attr_text = _("Host problem has been acknowledged");
            $attr_icon = theme_image("ack.png");
            $attr_icon_alt = $attr_text;
            $notesoutput .= '<li><div class="hoststatusdetailattrimg"><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '"></div><div class="hoststatusdetailattrtext">' . $attr_text . '</div></li>';
        }

        // Scheduled downtime
        if (intval($xml->hoststatus->scheduled_downtime_depth) > 0) {
            $attr_text = _("Host is in scheduled downtime");
            $attr_icon = theme_image("downtime.png");
            $attr_icon_alt = $attr_text;
            $notesoutput .= '<li><div class="hoststatusdetailattrimg"><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '"></div><div class="hoststatusdetailattrtext">' . $attr_text . '</div></li>';
        }

        // Is flapping
        if (intval($xml->hoststatus->is_flapping) == 1) {
            $attr_text = _("Host is flapping between states");
            $attr_icon = theme_image("flapping.png");
            $attr_icon_alt = $attr_text;
            $notesoutput .= '<li><div class="hoststatusdetailattrimg"><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '"></div><div class=hoststatusdetailattrtext">' . $attr_text . '</div></li>';
        }

        // Notificaitons
        if (intval($xml->hoststatus->notifications_enabled) == 0) {
            $attr_text = _("Host notifications are disabled");
            $attr_icon = theme_image("nonotifications.png");
            $attr_icon_alt = $attr_text;
            $notesoutput .= '<li><div class="hoststatusdetailattrimg"><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '"></div><div class="hoststatusdetailattrtext">' . $attr_text . '</div></li>';
        }

        if (!empty($notesoutput)) {
            $output .= '<tr><td valign="top">' . _('Host Notes') . ':</td><td><ul class="hoststatusdetailnotes">';
            $output .= $notesoutput;
            $output .= '</ul></td></tr>';
        }

        $output .= '
        </tbody>
        </table>
        </div>
        ';
    }

    return $output;
}


/**
 * Get the HTML output for the host status summary
 *
 * @param   array   $args       Array of settings
 * @return  string              HTML output
 */
function xicore_ajax_get_host_status_state_summary_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);
    $hide_details = grab_array_var($args, "hide_details", get_option("hide_object_details", 0));

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get host status
    $args = array(
        "cmd" => "gethoststatus",
        "host_id" => $host_id
    );
    $xml = get_xml_host_status($args);

    $output = '';
    if ($xml == null) {
        $output .= "No data";
    } else {

        $img = theme_image("nagios_unknown_large.png");
        $imgalt = _("Unknown");

        $current_state = intval($xml->hoststatus->current_state);
        $has_been_checked = intval($xml->hoststatus->has_been_checked);
        $status_text = $xml->hoststatus->status_text;
        $status_text_long = strval($xml->hoststatus->status_text_long);
        $status_text_long = str_replace("\\n", "<br />", $status_text_long);
        $status_text_long = str_replace("\n", "<br />", $status_text_long);

        if (get_option('allow_status_html') == true) {
            $status_text = html_entity_decode($status_text);
            $status_text_long = html_entity_decode($status_text_long);
        }

        switch ($current_state) {
            case 0:
                $img = theme_image("nagios_ok_large.png");
                $statestr = _("Up");
                $imgalt = $statestr;
                break;
            case 1:
                $img = theme_image("nagios_critical_large.png");
                $statestr = _("Down");
                $imgalt = $statestr;
                break;
            case 2:
                $img = theme_image("nagios_critical_large.png");
                $statestr = _("Unreachable");
                $imgalt = $statestr;
                break;
            default:
                break;
        }
        if ($has_been_checked == 0) {
            $img = theme_image("nagios_pending_large.png");
            $statestr = _("Pending");
            $imgalt = $statestr;
            $status_text = _("Host check is pending...");
        }

        $output .= '<div class="well"><div class="hoststatusdetailinfo summary-status">';
        $imgwidth = "24";
        $state_icon = "<img src='" . $img . "' alt='" . $imgalt . "' title='" . $imgalt . "'  width='" . $imgwidth . "'>";
        $output .= '<div class="hoststatusdetailinfoimg">' . $state_icon . '</div><div class="hoststatusdetailinfotext">' . $status_text . '</div>';

        if (!empty($status_text_long)) {
            $hide = '';
            if ($hide_details) {
                if (isset($_SESSION['show_details'])) {
                    $output .= '<div style="position: absolute; top: -25px; right: 5px;"><a class="show-details">'._("Hide details").'</a> <i class="fa fa-chevron-down"></i></div>';
                } else {
                    $hide = ' hide';
                    $output .= '<div style="position: absolute; top: -25px; right: 5px;"><a class="show-details">'._("Show details").'</a> <i class="fa fa-chevron-up"></i></div>';
                }
            }
            $output .= '<div class="hoststatusdetailinfotextlong longtext' . $hide . '">' . $status_text_long . '</div>';
        }

        $output .= '</div></div>';
    }

    return $output;
}


/**
 * Get host detailed comments HTML
 *
 * @param null $args
 * @return string
 */
function xicore_ajax_get_host_comments_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);
    $output = "";

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    $auth_command = is_authorized_for_host_command(0, $hostname);

    // Get host downtimes
    $args = array("object_id" => $host_id);
    $downtimes = get_scheduled_downtime($args);

    // Get host comments
    $args = array("object_id" => $host_id);
    $comments = get_data_comment($args);

    $output .= '<div class="infotable_title">' . _('Acknowledgements and Comments') . '</div>';

    if (count($comments) == 0) {
        $output .= '<div style="margin-bottom: 20px;">'._('No comments or acknowledgements.').'</div>';
    } else {

        $output .= '
        <table class="infotable table table-condensed table-striped table-bordered table-auto-width" style="margin-bottom: 20px;">
        <tbody>';

        foreach ($comments as $c) {
            switch ($c['entry_type']) {
                case COMMENTTYPE_ACKNOWLEDGEMENT:
                    $typeimg = theme_image("ack.png");
                    break;
                default:
                    $typeimg = theme_image("comment.png");
                    break;
            }
            $type = "<img src='" . $typeimg . "'>";
            $timestr = get_datetime_string_from_datetime($c['comment_time']);
            $author = $c['author_name'];

            $comment = (get_option('allow_comment_html') == true) ? html_entity_decode($c['comment_data']) : encode_form_val($c['comment_data']);

            $output .= '<tr><td valign="top">' . $type . '</td><td>By <b>' . encode_form_val($author) . '</b> at ' . $timestr . '<br>' . $comment . '</td>';
            if ($auth_command) {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DEL_HOST_COMMENT;
                $cmd["command_args"]["comment_id"] = $c['internal_comment_id'];
                $action = "<a href='#' " . get_nagioscore_command_ajax_code($cmd) . "><img src='" . theme_image("cross.png") . "' alt='" . _("Delete comment") . "' title='" . _("Delete comment") . "'></a>";
                $output .= '<td>' . $action . '</td>';
            }
            $output .= '</tr>';
        }

        $output .= '
        </tbody>
        </table>';

    }

    // Show all downtimes and their comments/information specifically because it can't be linked to the actual comment
    if (!empty($downtimes)) {

        $output .= '<div class="infotable_title">' . _('Scheduled Downtimes') . '</div>
        <table class="infotable table table-condensed table-striped table-bordered table-auto-width">
        <tbody>';

        foreach ($downtimes as $i => $d) {
            $author = $d['author_name'];
            $start = get_datetime_string_from_datetime($d['scheduled_start_time']);
            $end = get_datetime_string_from_datetime($d['scheduled_end_time']);
            $dtype = _('Fixed');
            if (!$d['is_fixed']) {
                $dtype = _('Flexible');
            }
            $active = '';
            if ($d['was_started']) {
                $active = '<div style="margin-bottom: 2px;"><span class="label label-primary" style="font-size: 9px;">'._('Downtime Started').'</span></div>';
            }
            $scheduled = sprintf(_('%s downtime scheduled for %s to %s'), $dtype, $start, $end);
            $comment = (get_option('allow_comment_html') == true) ? html_entity_decode($d['comment_data']) : encode_form_val($d['comment_data']);
            $output .= '<tr><td valign="top"><img src="' . theme_image("downtime.png") . '"></td><td>' . $active . 'By <b>' . encode_form_val($author) . '</b><br>' . $scheduled . '<hr class="dt">' . $comment . '</td>';
            if ($auth_command) {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DEL_HOST_DOWNTIME;
                $cmd["command_args"]["downtime_id"] = $d['internal_downtime_id'];
                $action = "<a href='#' " . get_nagioscore_command_ajax_code($cmd) . "><img src='" . theme_image("cross.png") . "' alt='" . _("Cancel downtime") . "' title='" . _("Cancel downtime") . "'></a>";
                $output .= '<td>' . $action . '</td>';
            }
            $output .= '</tr>';
        }

        $output .= '
        </tbody>
        </table>';
    }

    return $output;
}


/**
 * Get the host status details attributes HTML
 *
 * @param   array   $args   Object arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_host_status_attributes_html($args = null)
{
    $hostname = grab_array_var($args, "hostname", "");
    $host_id = grab_array_var($args, "host_id", -1);
    $display = grab_array_var($args, "display", "simple");

    if (!is_authorized_for_host(0, $hostname)) {
        return _("You are not authorized to access this feature.  Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    $auth_command = is_authorized_for_host_command(0, $hostname);

    // Get service status
    $args = array(
        "cmd" => "gethoststatus",
        "host_id" => $host_id
    );
    $xml = get_xml_host_status($args);

    if ($display == "advanced") {
        $title = _("Advanced Host Attributes");
    } else {
        $title = _("Host Attributes");
    }

    $output = '';
    $output .= '<div class="infotable_title">' . $title . '</div>';
    if ($xml) {

        $output .= '
        <table class="infotable table table-condensed table-striped table-bordered">
        <thead>
        <tr><th><div style="width: 50px;">' . _('Attribute') . '</div></th><th><div style="width: 50px;">' . _('State') . '</div></th>';

        if ($auth_command) {
            $output .= '<th><div style="width: 50px;">' . _('Action') . '</div></th>';
        }

        $output .= '</tr>
        </thead>
        <tbody>
        ';

        // Initialze some stuff we'll use a few times...
        $okcmd = array("command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND);
        $errcmd = array("command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND);
        $okcmd["command_args"] = array(
            "host_name" => $hostname
        );
        $errcmd["command_args"] = array(
            "host_name" => $hostname
        );

        if ($display == "simple" || $display == "all") {

            // Active checks
            $v = intval($xml->hoststatus->active_checks_enabled);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_CHECK;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_CHECK;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Active Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

        }

        if ($display == "advanced" || $display == "all") {

            // Passive checks
            $v = intval($xml->hoststatus->passive_checks_enabled);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_PASSIVE_HOST_CHECKS;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_PASSIVE_HOST_CHECKS;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Passive Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

        }

        if ($display == "simple" || $display == "all") {

            // Notifications
            $v = intval($xml->hoststatus->notifications_enabled);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_NOTIFICATIONS;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_NOTIFICATIONS;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Notifications') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

            // Flap detection
            $v = intval($xml->hoststatus->flap_detection_enabled);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_FLAP_DETECTION;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_FLAP_DETECTION;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Flap Detection') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

        }

        if ($display == "advanced" || $display == "all") {

            // Event handler
            $v = intval($xml->hoststatus->event_handler_enabled);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_EVENT_HANDLER;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_EVENT_HANDLER;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Event Handler') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

            // Performance data
            $v = intval($xml->hoststatus->process_performance_data);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Performance Data') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td></td></tr>';

            // Obsess over
            $v = intval($xml->hoststatus->obsess_over_host);
            $okcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_STOP_OBSESSING_OVER_HOST;
            $errcmd["command_args"]["cmd"] = NAGIOSCORE_CMD_START_OBSESSING_OVER_HOST;
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Obsession') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td>';
            if ($auth_command) {
                $output .= '<td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td>';
            }
            $output .= '</tr>';

        }

        $output .= '
        </tbody>
        </table>';
    }

    return $output;
}
