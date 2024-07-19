<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');


// PROGRAM STATUS *************************************************************************
function fetch_programstatus()
{
    global $request;
    $output = get_program_status_xml_output($request);
    print backend_output($output);
}


// PROGRAM PERFORMANCE *************************************************************************
function fetch_programperformance()
{
    global $db_tables;

    // Only authorized users can access this
    if (is_authorized_for_monitoring_system() == false) {
        return;
    }

    // SQL queries
    $service_totals_sql_query = "SELECT 
COUNT(nagios_servicestatus.servicestatus_id) AS total
FROM nagios_servicestatus
WHERE TRUE
AND check_type='%d'
AND (TIMESTAMPDIFF(SECOND,nagios_servicestatus.last_check,NOW()) < %d)";
    $host_totals_sql_query = "SELECT 
COUNT(nagios_hoststatus.hoststatus_id) AS total
FROM nagios_hoststatus
WHERE TRUE
AND check_type='%d'
AND (TIMESTAMPDIFF(SECOND,nagios_hoststatus.last_check,NOW()) < %d)";

    // initial values
    $perfinfo = array(
        "active_services" => array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            10 => 0,
            15 => 0,
            30 => 0,
            60 => 0
        ),
        "passive_services" => array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            10 => 0,
            15 => 0,
            30 => 0,
            60 => 0
        ),
        "active_hosts" => array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            10 => 0,
            15 => 0,
            30 => 0,
            60 => 0
        ),
        "passive_hosts" => array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            10 => 0,
            15 => 0,
            30 => 0,
            60 => 0
        ),
    );

    // generate query
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        //"sql" => $sql_query,   // <- this is a bit different that most other functions
        //"fieldmap" => $fieldmap,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => ""
    );

    // service mappings
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["servicestatus"] . ".instance_id",
    );
    $args["fieldmap"] = $fieldmap;

    // get active service stats
    foreach ($perfinfo["active_services"] as $timeframe => $val) {

        $args["sql"] = sprintf($service_totals_sql_query, 0, escape_sql_param($timeframe * 60, DB_NDOUTILS));
        $sql = generate_sql_query(DB_NDOUTILS, $args);

        if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            handle_backend_db_error(DB_NDOUTILS);
        } else {
            $perfinfo["active_services"][$timeframe] = intval($rs->fields["total"]);
        }
    }

    // get passive service stats
    foreach ($perfinfo["passive_services"] as $timeframe => $val) {

        $args["sql"] = sprintf($service_totals_sql_query, 1, escape_sql_param($timeframe * 60, DB_NDOUTILS));
        $sql = generate_sql_query(DB_NDOUTILS, $args);

        if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            handle_backend_db_error(DB_NDOUTILS);
        } else {
            $perfinfo["passive_services"][$timeframe] = intval($rs->fields["total"]);
        }
    }

    // host mappings
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["hoststatus"] . ".instance_id",
    );
    $args["fieldmap"] = $fieldmap;

    // get active host stats
    foreach ($perfinfo["active_hosts"] as $timeframe => $val) {

        $args["sql"] = sprintf($host_totals_sql_query, 0, escape_sql_param($timeframe * 60, DB_NDOUTILS));
        $sql = generate_sql_query(DB_NDOUTILS, $args);

        if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            handle_backend_db_error(DB_NDOUTILS);
        } else {
            $perfinfo["active_hosts"][$timeframe] = intval($rs->fields["total"]);
        }
    }

    // get passive host stats
    foreach ($perfinfo["passive_hosts"] as $timeframe => $val) {

        $args["sql"] = sprintf($host_totals_sql_query, 1, escape_sql_param($timeframe * 60, DB_NDOUTILS));
        $sql = generate_sql_query(DB_NDOUTILS, $args);

        if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
            handle_backend_db_error(DB_NDOUTILS);
        } else {
            $perfinfo["passive_hosts"][$timeframe] = intval($rs->fields["total"]);
        }
    }

    // Generate the XML
    $output = "<programperformanceinfo>\n";
    $output .= "  <recordcount>1</recordcount>\n";

    $output .= "  <active_services>\n";
    foreach ($perfinfo["active_services"] as $timeframe => $val) {
        $output .= "    <t" . $timeframe . "min>" . $val . "</t" . $timeframe . "min>\n";
    }
    $output .= "  </active_services>\n";

    $output .= "  <passive_services>\n";
    foreach ($perfinfo["passive_services"] as $timeframe => $val) {
        $output .= "    <t" . $timeframe . "min>" . $val . "</t" . $timeframe . "min>\n";
    }
    $output .= "  </passive_services>\n";

    $output .= "  <active_hosts>\n";
    foreach ($perfinfo["active_hosts"] as $timeframe => $val) {
        $output .= "    <t" . $timeframe . "min>" . $val . "</t" . $timeframe . "min>\n";
    }
    $output .= "  </active_hosts>\n";

    $output .= "  <passive_hosts>\n";
    foreach ($perfinfo["passive_hosts"] as $timeframe => $val) {
        $output .= "    <t" . $timeframe . "min>" . $val . "</t" . $timeframe . "min>\n";
    }
    $output .= "  </passive_hosts>\n";

    $output .= "</programperformanceinfo>\n";

    // Send to converter and print out
    print backend_output($output);
}


// CONTACT STATUS *************************************************************************
function fetch_contactstatus()
{
    global $sqlquery;
    global $request;
    global $db_tables;

    // generate query
    $fieldmap = array(
        "name" => "obj1.name1",
        "alias" => $db_tables[DB_NDOUTILS]["contacts"] . ".alias",
        "instance_id" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".instance_id",
        "contactstatus_id" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".contactstatus_id",
        "contact_id" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".contact_object_id",
        "status_update_time" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".status_update_time",
        "host_notifications_enabled" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".host_notifications_enabled",
        "service_notifications_enabled" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".service_notifications_enabled",
        "last_host_notification" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".last_host_notification",
        "last_service_notification" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".last_service_notification",
        "modified_attributes" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".modified_attributes",
        "modified_host_attributes" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".modified_host_attributes",
        "modified_service_attributes" => $db_tables[DB_NDOUTILS]["contactstatus"] . ".modified_service_attributes",
    );
    $objectauthfields = array(
        "contact_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        "sql" => $sqlquery['GetContactStatus'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!isset($request['brevity'])) {
        $brevity = 0;
    } else {
        $brevity = $request['brevity'];
    }

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error(DB_NDOUTILS);
    } else {

        // Generate the XML
        $outputtype = grab_request_var("outputtype", "");
        $output = "<contactstatuslist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {
                if ($outputtype == 'json') {
                    $output .= "  <contactstatus>\n";
                    $output .= xml_db_field(2, $rs, 'contactstatus_id', 'id', true);
                } else {
                    $output .= "  <contactstatus id='" . db_field($rs, 'contactstatus_id') . "'>\n";
                }
                $output .= xml_db_field(2, $rs, 'instance_id', '', true);
                $output .= xml_db_field(2, $rs, 'contact_object_id', 'contact_id', true);
                $output .= xml_db_field(2, $rs, 'contact_name', 'name', true);
                $output .= xml_db_field(2, $rs, 'contact_alias', 'alias', true);
                $output .= xml_db_field(2, $rs, 'status_update_time', '', true);
                $output .= xml_db_field(2, $rs, 'host_notifications_enabled', '', true);
                $output .= xml_db_field(2, $rs, 'service_notifications_enabled', '', true);
                $output .= xml_db_field(2, $rs, 'last_host_notification', '', true);
                $output .= xml_db_field(2, $rs, 'last_service_notification', '', true);
                if ($brevity < 1) {
                    $output .= xml_db_field(2, $rs, 'modified_attributes', '', true);
                    $output .= xml_db_field(2, $rs, 'modified_host_attributes', '', true);
                    $output .= xml_db_field(2, $rs, 'modified_service_attributes', '', true);
                }
                $output .= "  </contactstatus>\n";
                $rs->MoveNext();
            }
        }

        $output .= "</contactstatuslist>\n";

        print backend_output($output);
    }
}


// CUSTOM CONTACT VARIABLE STATUS *************************************************************************
function fetch_customcontactvariablestatus()
{
    global $sqlquery;
    global $request;
    global $db_tables;

    // generate query
    $fieldmap = array(
        "contact_name" => "obj1.name1",
        "contact_alias" => $db_tables[DB_NDOUTILS]["contacts"] . ".alias",
        "instance_id" => $db_tables[DB_NDOUTILS]["customvariablestatus"] . ".instance_id",
        "contact_id" => "obj1.object_id",
        "var_name" => $db_tables[DB_NDOUTILS]["customvariablestatus"] . ".varname",
        "var_value" => $db_tables[DB_NDOUTILS]["customvariablestatus"] . ".varvalue"
    );
    $objectauthfields = array(
        "contact_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        "sql" => $sqlquery['GetCustomContactVariableStatus'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error(DB_NDOUTILS);
    } else {

        // Generate the XML
        $output = "<customcontactvarstatuslist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            $last_id = -1;
            while (!$rs->EOF) {

                $this_id = $rs->fields['object_id'];
                if ($last_id != $this_id) {
                    if ($last_id != -1) {
                        $output .= "    </customvars>\n";
                        $output .= "  </customcontactvarstatus>\n";
                    }
                    $last_id = $this_id;
                    $output .= "  <customcontactvarstatus>\n";
                    $output .= xml_db_field(2, $rs, 'instance_id', '', true);
                    $output .= xml_db_field(2, $rs, 'object_id', 'contact_id', true);
                    $output .= xml_db_field(2, $rs, 'contact_name', '', true);
                    $output .= xml_db_field(2, $rs, 'contact_alias', '', true);
                    $output .= "    <customvars>\n";
                }

                $output .= "      <customvar>\n";
                $output .= xml_db_field(4, $rs, 'varname', 'name', true);
                $output .= xml_db_field(4, $rs, 'varvalue', 'value', true);
                $output .= xml_db_field(4, $rs, 'has_been_modified', 'modified', true);
                $output .= xml_db_field(4, $rs, 'status_update_time', 'last_update', true);
                $output .= "      </customvar>\n";

                $rs->MoveNext();
            }

            if ($last_id != -1) {
                $output .= "    </customvars>\n";
                $output .= "  </customcontactvarstatus>\n";
            }
        }

        $output .= "</customcontactvarstatuslist>\n";

        print backend_output($output);
    }
}


// HOST STATUS *************************************************************************
function fetch_hoststatus()
{
    global $request;
    $output = get_host_status_xml_output($request);
    print backend_output($output);
}


// CUSTOM HOST VARIABLE STATUS *************************************************************************
function fetch_customhostvariablestatus()
{
    global $request;
    $output = get_custom_host_variable_status_xml_output($request);
    print backend_output($output);
}


// SERVICE STATUS *************************************************************************
function fetch_servicestatus()
{
    global $request;
    $output = get_service_status_xml_output($request);
    print backend_output($output);
}


// CUSTOM SERVICE VARIABLE STATUS *************************************************************************
function fetch_customservicevariablestatus()
{
    global $request;
    $output = get_custom_service_variable_status_xml_output($request);
    print backend_output($output);
}


// TIMED EVENT QUEUE *************************************************************************
function fetch_timedeventqueue()
{
    global $sqlquery;
    global $db_tables;
    global $request;

    // Only admins can access this
    if (is_admin() == false) {
        return;
    }

    // Default # of records to return if none specified
    if (!isset($request["records"])) {
        $request["records"] = 100;
    }

    // Generate query
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["instances"] . ".instance_id",
        "timedeventqueue_id" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".timedeventqueue_id",
        "event_type" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".event_type",
        "queued_time" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".queued_time",
        "queued_time_usec" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".queued_time_usec",
        "scheduled_time" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".scheduled_time",
        "recurring_event" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".recurring_event",
        "object_id" => $db_tables[DB_NDOUTILS]["timedeventqueue"] . ".object_id",
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
    $default_order = "scheduled_time ASC, timedeventqueue_id ASC";
    $args = array(
        "sql" => $sqlquery['GetTimedEventQueue'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "default_order" => $default_order
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error(DB_NDOUTILS);
    } else {

        // Generate the XML
        $outputtype = grab_request_var("outputtype", "");
        $output = "<timedeventqueue>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {
                if ($outputtype == "json") {
                    $output .= "  <timedevent>\n";
                    $output .= xml_db_field(2, $rs, 'timedeventqueue_id', 'id', true);
                } else {
                    $output .= "  <timedevent id='" . db_field($rs, 'timedeventqueue_id') . "'>\n";
                }
                $output .= xml_db_field(2, $rs, 'instance_id', '', true);
                $output .= xml_db_field(2, $rs, 'event_type', '', true);
                $output .= xml_db_field(2, $rs, 'queued_time', '', true);
                $output .= xml_db_field(2, $rs, 'queued_time_usec', '', true);
                $output .= xml_db_field(2, $rs, 'scheduled_time', '', true);
                $output .= xml_db_field(2, $rs, 'recurring_event', '', true);
                $output .= xml_db_field(2, $rs, 'object_id', '', true);
                $output .= xml_db_field(2, $rs, 'objecttype_id', '', true);
                $output .= xml_db_field(2, $rs, 'host_name', '', true);
                $output .= xml_db_field(2, $rs, 'service_description', '', true);
                $output .= "  </timedevent>\n";

                $rs->MoveNext();
            }
        }

        $output .= "</timedeventqueue>\n";

        print backend_output($output);
    }
}


// TIMED EVENT QUEUE SUMMARY *************************************************************************
function fetch_timedeventqueuesummary()
{
    global $request;
    $output = get_timedeventqueuesummary_xml_output($request);
    print backend_output($output);
}


// SCHEDULED DOWNTIME *************************************************************************
function fetch_scheduleddowntime()
{
    global $request;
    $output = get_scheduled_downtime_xml_output($request);
    print backend_output($output);
}


// COMMENTS *************************************************************************
function fetch_comments()
{
    global $request;
    $output = get_comments_xml_output($request);
    print backend_output($output);
}
