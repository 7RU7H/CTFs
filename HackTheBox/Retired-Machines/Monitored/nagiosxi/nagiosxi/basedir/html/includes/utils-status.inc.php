<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// STATUS 
////////////////////////////////////////////////////////////////////////////////


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_program_status($args = array())
{
    return get_backend_cache("get_program_status_xml_output", $args);
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_service_status($args = array())
{
    insert_ndoutils_pending_states(); // Previous existing hack
    return get_backend_cache("get_service_status_xml_output", $args);
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_custom_service_variable_status($args = array())
{
    return get_backend_cache("get_custom_service_variable_status_xml_output", $args);
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_host_status($args = array())
{
    insert_ndoutils_pending_states(); // Previous existing hack
    return get_backend_cache("get_host_status_xml_output", $args);
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_custom_host_variable_status($args = array())
{
    return get_backend_cache("get_custom_host_variable_status_xml_output", $args);
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_comments($args = array())
{
    return get_backend_cache("get_comments_xml_output", $args);
}


////////////////////////////////////////////////////////////////////////////////
// FIX / HACK
////////////////////////////////////////////////////////////////////////////////


/**
 * Newly added and pending hosts/services don't show up for a while unless we do this
 */
function insert_ndoutils_pending_states()
{
    global $db_tables;

    $sql = "SELECT (TIMESTAMPDIFF(SECOND," . $db_tables[DB_NDOUTILS]["programstatus"] . ".program_start_time,NOW())) AS program_run_time, " . $db_tables[DB_NDOUTILS]['programstatus'] . ".* FROM " . $db_tables[DB_NDOUTILS]['programstatus'] . "  WHERE " . $db_tables[DB_NDOUTILS]['programstatus'] . ".instance_id='1'";

    $now = time();

    if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        $runtime = intval($rs->fields["program_run_time"]);
        $starttime = $rs->fields["program_start_time"];
        $is_running = $rs->fields["is_currently_running"];
        $stu = strtotime($starttime);
    } else {
        return false;
    }

    $lnpf = get_option("last_ndo_pending_fix", 0);
    if ($lnpf < $stu && $is_running) {
        $sql = "DELETE FROM " . $db_tables[DB_NDOUTILS]['servicestatus'] . " WHERE service_object_id = 0;";
        exec_sql_query(DB_NDOUTILS, $sql);
        $sql = "DELETE FROM " . $db_tables[DB_NDOUTILS]['hoststatus'] . " WHERE host_object_id = 0;";
        exec_sql_query(DB_NDOUTILS, $sql);
        set_option("last_ndo_pending_fix", $now);
    }

    $lnsf = get_option("last_ndoutils_status_fix", 0);

    $do_update = false;
    if ($lnsf < $stu && $runtime > 5) {
        $do_update = true;
    }

    // Update ndoutils
    if ($do_update) {

        set_option("last_ndoutils_status_fix", $now);

        // Insert missing service status records
        $sql = "SELECT " . $db_tables[DB_NDOUTILS]['services'] . ".service_object_id AS sid, " . $db_tables[DB_NDOUTILS]['services'] . ".*, " . $db_tables[DB_NDOUTILS]['servicestatus'] . ".* FROM " . $db_tables[DB_NDOUTILS]['services'] . "
LEFT JOIN " . $db_tables[DB_NDOUTILS]['servicestatus'] . " ON " . $db_tables[DB_NDOUTILS]['services'] . ".service_object_id=" . $db_tables[DB_NDOUTILS]['servicestatus'] . ".service_object_id
WHERE servicestatus_id IS NULL";

        if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            while (!$rs->EOF) {
                $sid = intval($rs->fields["sid"]);
                $args = array(
                    "notifications_enabled" => 1,
                    "active_checks_enabled" => 1,
                );
                add_ndoutils_servicestatus($sid, STATE_OK, STATETYPE_HARD, "Service check is pending...", 1, $args);
                $rs->MoveNext();
            }
        }

        // Insert missing host status records
        $sql = "SELECT " . $db_tables[DB_NDOUTILS]['hosts'] . ".host_object_id AS hid, " . $db_tables[DB_NDOUTILS]['hosts'] . ".*, " . $db_tables[DB_NDOUTILS]['hoststatus'] . ".* FROM " . $db_tables[DB_NDOUTILS]['hosts'] . "
LEFT JOIN " . $db_tables[DB_NDOUTILS]['hoststatus'] . " ON " . $db_tables[DB_NDOUTILS]['hosts'] . ".host_object_id=" . $db_tables[DB_NDOUTILS]['hoststatus'] . ".host_object_id
WHERE hoststatus_id IS NULL";

        if (($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            while (!$rs->EOF) {
                $hid = intval($rs->fields["hid"]);
                $args = array(
                    "notifications_enabled" => 1,
                    "active_checks_enabled" => 1,
                );
                add_ndoutils_hoststatus($hid, STATE_UP, STATETYPE_HARD, "Host check is pending...", 1, $args);
                $rs->MoveNext();
            }
        }

    }
}

function utils_status_get_services($host)
{
    $services = get_data_service_status(array('host_name' => $host));
    $now = time();
    $allow_html = get_option('allow_status_html', false);
    foreach ($services as $i => $service) {
        $services[$i]['last_check_formatted'] = get_datetime_string_from_datetime($service['last_check']);
        $last_state_change_time = strtotime($service['last_state_change']);
        if ($last_state_change_time == 0) {
            $statedurationstr = "N/A";
        } else {
            $statedurationstr = get_duration_string($now - $last_state_change_time);
        }
        $services[$i]['duration'] = $statedurationstr;

        $service_name = $service['service_description'];

        // Icons
        $service_icons = "";
        $service_name_cell = "";

        // Flapping
        if (intval($service['is_flapping']) == 1) {
            $service_icons .= get_service_status_note_image("flapping.png", _("This service is flapping"));
        }

        // Acknowledged
        if (array_key_exists('problem_acknowledged', $service) && intval($service['problem_acknowledged']) == 1) {
            $service_icons .= get_service_status_note_image("ack.png", _("This service problem has been acknowledged"));
        }

        $passive_checks_enabled = intval($service['passive_checks_enabled']);
        $active_checks_enabled = intval($service['active_checks_enabled']);

        // Passive only
        if ($active_checks_enabled == 0 && $passive_checks_enabled == 1) {
            $service_icons .= get_service_status_note_image("passiveonly.png", _("Passive Only Check"));
        }

        // Notifications
        if (intval($service['notifications_enabled']) == 0) {
            $service_icons .= get_service_status_note_image("nonotifications.png", _("Notifications are disabled for this service"));
        }

        // Downtime
        if (intval($service['scheduled_downtime_depth']) > 0) {
            $service_icons .= get_service_status_note_image("downtime.png", _("This service is in scheduled downtime"));
        }

        // Service icons
        $service_icons .= get_object_icon_html($service['icon_image'], $service['icon_image_alt']);

        // Notes URL icon/link HTML
        if (!empty($service['notes_url'])) {
            $service_icons .= get_notes_url_html(xicore_replace_macros($service['notes_url'], $service, true));
        }

        // Action URL icon/link HTML
        if (!empty($service['action_url'])) {
            $service_icons .= get_action_url_html(xicore_replace_macros($service['action_url'], $service, true));
        }

        // Set display name if it exists
        $show_service_name = $service_name;
        if (!empty($service['display_name'])) {
            $show_service_name = $service['display_name'];
        }

        $service_name_cell .= "<div class='servicename' style='float: left;'><a href='" . get_service_status_detail_link($host, $service_name) . "'>" . $show_service_name . "</a></div>";
        if (!array_key_exists('service_id', $service)) {
            $service['service_id'] = '';
        }
        // Get custom service icons
        $extra_icons = '';
        $cbdata = array(
            "objecttype" => OBJECTTYPE_SERVICE,
            "host_name" => $host,
            "service_description" => $service_name,
            "object_id" => $service['service_id'],
            "object_data" => $service,
            "allow_html" => $allow_html,
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

        // Add icons to cell
        $service_name_cell .= "<div class='extraicons' style='float: right;'>" . $extra_icons . "</div>";
        $service_name_cell .= "<div class='serviceicons' style='float: right;'><a href='" . get_service_status_detail_link($host, $service_name) . "'>" . $service_icons . "</a>";

        $services[$i]['name'] = $service_name_cell;
    }
    return $services;
}
