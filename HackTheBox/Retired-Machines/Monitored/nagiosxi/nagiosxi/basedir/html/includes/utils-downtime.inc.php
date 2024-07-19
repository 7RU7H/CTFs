<?php
//
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//


/**
 * Get an array of scheduled downtimes from NDO
 *
 * @param   array   $opts   User options
 * @return  array           List of scheduled downtimes
 */
function get_scheduled_downtime($opts=array())
{
    global $sqlquery;
    global $db_tables;

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["instances"] . ".instance_id",
        "downtime_type" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".downtime_type",
        "entry_time" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".entry_time",
        "author_name" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".author_name",
        "comment_data" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".comment_data",
        "internal_downtime_id" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".internal_downtime_id",
        "internal_id" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".internal_downtime_id",
        "triggered_by_id" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".triggered_by_id",
        "is_fixed" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".is_fixed",
        "duration" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".duration",
        "scheduled_start_time" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".scheduled_start_time",
        "scheduled_end_time" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".scheduled_end_time",
        "was_started" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".was_started",
        "actual_start_time" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".actual_start_time",
        "actual_start_time_usec" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".actual_start_time_usec",
        "object_id" => $db_tables[DB_NDOUTILS]["scheduleddowntime"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2"
    );
    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $default_order = "scheduled_start_time DESC, scheduleddowntime_id DESC";
    $args = array(
        "sql" => $sqlquery['GetScheduledDowntime'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "default_order" => $default_order,
        "useropts" => $opts
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        if (defined("BACKEND")) {
            handle_backend_db_error(DB_NDOUTILS);
        } else {
            return false;
        }
    } else {
        $downtimes = $rs->GetArray();
    }

    return $downtimes;
}


/**
 * Gets an XML output version of downtimes
 *
 * @param   array    $request_args  Opts for the get_scheduled_downtime function
 * @return  string                  XML output
 */
function get_scheduled_downtime_xml_output($request_args=array())
{
    $downtimes = get_scheduled_downtime($request_args);

    // Start generating XML output
    $output = "<scheduleddowntimelist>\n";
    $output .= "  <recordcount>" . count($downtimes) . "</recordcount>\n";

    // Loop through each downtime to convert to XML
    if (!isset($request_args["totals"])) {
        foreach ($downtimes as $dt) {
            $output .= "  <scheduleddowntime id='" . get_xml_db_field_val($dt, 'scheduleddowntime_id') . "'>\n";
            $output .= get_xml_db_field(2, $dt, 'instance_id', '', true);
            $output .= get_xml_db_field(2, $dt, 'downtime_type', '', true);
            $output .= get_xml_db_field(2, $dt, 'object_id', '', true);
            $output .= get_xml_db_field(2, $dt, 'objecttype_id', '', true);
            $output .= get_xml_db_field(2, $dt, 'host_name', '', true);
            $output .= get_xml_db_field(2, $dt, 'service_description', '', true);
            $output .= get_xml_db_field(2, $dt, 'entry_time', '', true);
            $output .= get_xml_db_field(2, $dt, 'author_name', '', true);
            $output .= get_xml_db_field(2, $dt, 'comment_data', '', true);
            $output .= get_xml_db_field(2, $dt, 'internal_downtime_id', 'internal_id', true);
            $output .= get_xml_db_field(2, $dt, 'triggered_by_id', 'triggered_by', true);
            $output .= get_xml_db_field(2, $dt, 'is_fixed', 'fixed', true);
            $output .= get_xml_db_field(2, $dt, 'duration', '', true);
            $output .= get_xml_db_field(2, $dt, 'scheduled_start_time', '', true);
            $output .= get_xml_db_field(2, $dt, 'scheduled_end_time', '', true);
            $output .= get_xml_db_field(2, $dt, 'was_started', '', true);
            $output .= get_xml_db_field(2, $dt, 'actual_start_time', '', true);
            $output .= get_xml_db_field(2, $dt, 'actual_start_time_usec', '', true);
            $output .= "  </scheduleddowntime>\n";
        }
    }

    $output .= "</scheduleddowntimelist>\n";

    return $output;
}

