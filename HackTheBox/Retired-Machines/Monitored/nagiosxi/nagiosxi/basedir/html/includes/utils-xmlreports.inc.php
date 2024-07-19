<?php
//
// Copyright (c) 2010-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// LOG ENTRIES
////////////////////////////////////////////////////////////////////////////////


/**
 * Get all the log entires specified and return as XML
 *
 * @param   array   $request_args   Arguments for the SQL query
 * @return  string                  XML output
 */
function get_logentries_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $logentries = get_data_logentries($request_args);

    $output .= "<logentries>\n";

    if ($totals) {
        $count = empty($logentries[0]['total']) ? 0 : $logentries[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {
        $output .= "  <recordcount>" . count($logentries) . "</recordcount>\n";

        foreach ($logentries as $rs) {

            $output .= "  <logentry>\n";
            $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
            $output .= get_xml_db_field(2, $rs, 'entry_time');
            $output .= get_xml_db_field(2, $rs, 'logentry_type');
            $output .= get_xml_db_field(2, $rs, 'logentry_data');
            $output .= "  </logentry>\n";

        }
    }

    $output .= "</logentries>\n";

    return $output;
}


function get_data_logentries($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";

    // By default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND logentry_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";

    // Optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "") {
        $sqlmods .= " AND logentry_time < '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";
    }

    $fieldmap = array(
        "logentry_id" => $db_tables[DB_NDOUTILS]["logentries"] . ".logentry_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["logentries"] . ".instance_id",
        "logentry_time" => $db_tables[DB_NDOUTILS]["logentries"] . ".logentry_time",
        "entry_time" => $db_tables[DB_NDOUTILS]["logentries"] . ".entry_time",
        "entry_time_usec" => $db_tables[DB_NDOUTILS]["logentries"] . ".entry_time_usec",
        "logentry_type" => $db_tables[DB_NDOUTILS]["logentries"] . ".logentry_type",
        "logentry_data" => $db_tables[DB_NDOUTILS]["logentries"] . ".logentry_data",
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetLogEntries'] . $sqlmods;
    if ($totals) {
        $q = $sqlquery['GetLogEntriesCount'] . $sqlmods;
    }

    $default_order = "logentry_time DESC, logentry_id DESC";
    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// STATE HISTORY
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_statehistory_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $statehistory = get_data_statehistory($request_args);

    $output .= "<statehistory>\n";

    if ($totals) {
        $count = empty($statehistory[0]['total']) ? 0 : $statehistory[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {
        $output .= "  <recordcount>" . count($statehistory) . "</recordcount>\n";

        foreach ($statehistory as $rs) {

            $output .= "  <stateentry>\n";
            $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
            $output .= get_xml_db_field(2, $rs, 'state_time');
            $output .= get_xml_db_field(2, $rs, 'object_id');
            $output .= get_xml_db_field(2, $rs, 'objecttype_id');
            $output .= get_xml_db_field(2, $rs, 'host_name');
            $output .= get_xml_db_field(2, $rs, 'service_description');
            $output .= get_xml_db_field(2, $rs, 'state_change');
            $output .= get_xml_db_field(2, $rs, 'state');
            $output .= get_xml_db_field(2, $rs, 'state_type');
            $output .= get_xml_db_field(2, $rs, 'current_check_attempt');
            $output .= get_xml_db_field(2, $rs, 'max_check_attempts');
            $output .= get_xml_db_field(2, $rs, 'last_state');
            $output .= get_xml_db_field(2, $rs, 'last_hard_state');
            $output .= get_xml_db_field(2, $rs, 'output');
            $output .= "  </stateentry>\n";

        }
    }

    $output .= "</statehistory>\n";

    return $output;
}


function get_data_statehistory($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";

    // By default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND state_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";

    // Optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "") {
        $sqlmods .= " AND state_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";
    }

    $fieldmap = array(
        "statehistory_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".statehistory_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".instance_id",
        "state_time" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_time",
        "object_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2",
        "state_change" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_change",
        "state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state",
        "state_type" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_type",
        "current_check_attempt" => $db_tables[DB_NDOUTILS]["statehistory"] . ".current_check_attempt",
        "max_check_attempts" => $db_tables[DB_NDOUTILS]["statehistory"] . ".max_check_attempts",
        "last_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_state",
        "last_hard_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_hard_state",
        "output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".output",
        "long_output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".long_output",
    );

    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetStateHistory'] . $sqlmods;
    if ($totals) {
        $q = $sqlquery['GetStateHistoryCount'] . $sqlmods;
    }

    $default_order = "state_time DESC, statehistory_id DESC";
    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HISTORICAL HOST STATUS 
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_historical_host_status_xml_output($request_args)
{

    global $sqlquery;

    $output = "";

    // we have to munge the state date/time
    $state_time = time();
    if (isset($request_args["state_time"])) {
        $state_time = intval($request_args["state_time"]);
        unset($request_args["state_time"]);
    }
    $rawsql = str_replace("STATUSTIME", $state_time, $sqlquery['GetHistoricalStatus']);

    // only include hosts
    $request_args["objecttype_id"] = "1";

    // generate query
    $fieldmap = array(
        "name" => "o.name1",
        "host_name" => "o.name1",
        "instance_id" => "s.instance_id",
        "host_id" => "o.object_id",
        "object_id" => "o.object_id",
        "objecttype_id" => "o.objecttype_id",
        "is_active" => "o.is_active",
        "state_time" => "s.state_time",
        "state" => "s.state",
        "state_change" => "s.state_change",
        "state_type" => "s.state_type",
        "last_state" => "s.last_state",
        "last_hard_state" => "s.last_hard_state",
        "output" => "s.output",
    );
    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(//"instance_id"
    );
    $args = array(
        "sql" => $rawsql,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<historicalhoststatuslist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <historicalhoststatus id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'object_id', 'host_id');
                $output .= get_xml_db_field(2, $rs, 'host_name', 'name');
                $output .= get_xml_db_field(2, $rs, 'state_time');
                $output .= get_xml_db_field(2, $rs, 'output', 'status_text');
                $output .= get_xml_db_field(2, $rs, 'state');
                $output .= get_xml_db_field(2, $rs, 'state_type');
                $output .= get_xml_db_field(2, $rs, 'state_change');
                $output .= get_xml_db_field(2, $rs, 'last_state');
                $output .= get_xml_db_field(2, $rs, 'last_hard_state');

                $output .= "  </historicalhoststatus>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</historicalhoststatuslist>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HISTORICAL SERVICE STATUS 
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_historical_service_status_xml_output($request_args)
{
    global $sqlquery;

    $output = "";

    // we have to munge the state date/time
    $state_time = time();
    if (isset($request_args["state_time"])) {
        $state_time = intval($request_args["state_time"]);
        unset($request_args["state_time"]);
    }
    $rawsql = str_replace("STATUSTIME", $state_time, $sqlquery['GetHistoricalStatus']);

    // only include services
    $request_args["objecttype_id"] = "2";

    // generate query
    $fieldmap = array(
        "host_name" => "o.name1",
        "service_description" => "o.name2",
        "name" => "o.name2",
        "instance_id" => "s.instance_id",
        "service_id" => "o.object_id",
        "object_id" => "o.object_id",
        "objecttype_id" => "o.objecttype_id",
        "is_active" => "o.is_active",
        "state_time" => "s.state_time",
        "state" => "s.state",
        "state_change" => "s.state_change",
        "state_type" => "s.state_type",
        "last_state" => "s.last_state",
        "last_hard_state" => "s.last_hard_state",
        "output" => "s.output",
    );
    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(//"instance_id"
    );
    $args = array(
        "sql" => $rawsql,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);


    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<historicalservicestatuslist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <historicalservicestatus id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'object_id', 'host_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'service_description', 'name');
                $output .= get_xml_db_field(2, $rs, 'state_time');
                $output .= get_xml_db_field(2, $rs, 'output', 'status_text');
                $output .= get_xml_db_field(2, $rs, 'state');
                $output .= get_xml_db_field(2, $rs, 'state_type');
                $output .= get_xml_db_field(2, $rs, 'state_change');
                $output .= get_xml_db_field(2, $rs, 'last_state');
                $output .= get_xml_db_field(2, $rs, 'last_hard_state');

                $output .= "  </historicalservicestatus>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</historicalservicestatuslist>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// TOP ALERT PRODUCERS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_topalertproducers_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";
    // by default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND state_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";
    // optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "")
        $sqlmods .= " AND state_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";


    $output = "";

    // generate query
    $fieldmap = array(
        "statehistory_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".statehistory_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".instance_id",
        "state_time" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_time",
        "object_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2",
        "state_change" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_change",
        "state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state",
        "state_type" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_type",
        "current_check_attempt" => $db_tables[DB_NDOUTILS]["statehistory"] . ".current_check_attempt",
        "max_check_attempts" => $db_tables[DB_NDOUTILS]["statehistory"] . ".max_check_attempts",
        "last_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_state",
        "last_hard_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_hard_state",
        "output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".output",
        "long_output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".long_output",
    );

    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $default_order = "total_alerts DESC, state_time DESC ";
    $args = array(
        "sql" => $sqlquery['GetTopAlertProducers'] . $sqlmods,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "groupby" => "GROUP BY " . $db_tables[DB_NDOUTILS]["statehistory"] . ".object_id",
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    //echo "$sql<BR>";
    //debug($sql);

    // Disable only_full_group_by for this query... (older mysql requires this)
    exec_sql_query(DB_NDOUTILS, "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));", false);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<topalertproducers>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <producer>\n";
                $output .= get_xml_db_field(2, $rs, 'total_alerts');
                $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'state_time');
                $output .= get_xml_db_field(2, $rs, 'last_state_time');
                $output .= get_xml_db_field(2, $rs, 'object_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'host_alias');
                $output .= get_xml_db_field(2, $rs, 'service_description');
                $output .= get_xml_db_field(2, $rs, 'service_alias');
                $output .= get_xml_db_field(2, $rs, 'state_change');
                $output .= get_xml_db_field(2, $rs, 'state');
                $output .= get_xml_db_field(2, $rs, 'state_type');
                $output .= get_xml_db_field(2, $rs, 'current_check_attempt');
                $output .= get_xml_db_field(2, $rs, 'max_check_attempts');
                $output .= get_xml_db_field(2, $rs, 'last_state');
                $output .= get_xml_db_field(2, $rs, 'last_hard_state');
                $output .= get_xml_db_field(2, $rs, 'output');
                //$output.=get_xml_db_field(2,$rs,'long_output');
                $output .= "  </producer>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</topalertproducers>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HISTOGRAM
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_histogram_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";
    // by default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND state_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";
    // optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "")
        $sqlmods .= " AND state_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";


    $output = "";

    // generate query
    $fieldmap = array(
        "statehistory_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".statehistory_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".instance_id",
        "state_time" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_time",
        "object_id" => $db_tables[DB_NDOUTILS]["statehistory"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2",
        "state_change" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_change",
        "state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state",
        "state_type" => $db_tables[DB_NDOUTILS]["statehistory"] . ".state_type",
        "current_check_attempt" => $db_tables[DB_NDOUTILS]["statehistory"] . ".current_check_attempt",
        "max_check_attempts" => $db_tables[DB_NDOUTILS]["statehistory"] . ".max_check_attempts",
        "last_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_state",
        "last_hard_state" => $db_tables[DB_NDOUTILS]["statehistory"] . ".last_hard_state",
        "output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".output",
        "long_output" => $db_tables[DB_NDOUTILS]["statehistory"] . ".long_output",
    );

    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $groupby = grab_array_var($request_args, "groupby", "hour_of_day");


    $groupby_field = "";
    switch ($groupby) {
        case "month";
            $groupby_field = "DATE_FORMAT(" . $db_tables[DB_NDOUTILS]['statehistory'] . ".state_time,'%c')";
            break;
        case "day_of_month":
            $groupby_field = "DATE_FORMAT(" . $db_tables[DB_NDOUTILS]['statehistory'] . ".state_time,'%d')";
            break;
        case "day_of_week":
            $groupby_field = "DATE_FORMAT(" . $db_tables[DB_NDOUTILS]['statehistory'] . ".state_time,'%w')";
            break;
        case "hour_of_day":
            $groupby_field = "DATE_FORMAT(" . $db_tables[DB_NDOUTILS]['statehistory'] . ".state_time,'%H')";
            break;
        default:
            break;
    }

    $sqlpre = "SELECT COUNT(" . $groupby_field . ") AS total,";

    $default_order = $groupby . " ASC, state_time DESC ";
    $args = array(
        "sql" => $sqlpre . $sqlquery['GetHistogram'] . $sqlmods,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "groupby" => "GROUP BY " . $groupby,
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    //echo "$sql<BR>";

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<histogramdata>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <histogramelement>\n";
                $output .= get_xml_db_field(2, $rs, 'total');
                $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'object_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'service_description');
                $output .= get_xml_db_field(2, $rs, 'month');
                $output .= get_xml_db_field(2, $rs, 'day_of_month');
                $output .= get_xml_db_field(2, $rs, 'day_of_week');
                $output .= get_xml_db_field(2, $rs, 'hour_of_day');
                //$output.=get_xml_db_field(2,$rs,'long_output');
                $output .= "  </histogramelement>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</histogramdata>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// NOTIFICATIONS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_notificationswithcontacts_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";
    // by default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND " . $db_tables[DB_NDOUTILS]["notifications"] . ".start_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";
    // optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "")
        $sqlmods .= " AND " . $db_tables[DB_NDOUTILS]["notifications"] . ".start_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";

    //exception to allow expanded search, added 5/21/2012 -MG
    $search = grab_array_var($request_args, 'search', '');
    $search = escape_sql_param($search, DB_NDOUTILS, false);
    if ($search != '') {
        $sqlmods .= " AND ( obj1.name1 LIKE '%{$search}%' OR obj1.name2 LIKE '%{$search}%'
						OR obj2.name1 LIKE '%{$search}%' OR " . $db_tables[DB_NDOUTILS]["notifications"] . ".output LIKE '%{$search}%' )";
    }
    // only get records where actual notifications occurred
    //$sqlmods.=" AND ".$db_tables[DB_NDOUTILS]["notifications"].".contacts_notified > '0'";

    $output = "";

    // generate query
    $fieldmap = array(
        "notification_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".instance_id",
        "start_time" => $db_tables[DB_NDOUTILS]["notifications"] . ".start_time",
        "object_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2",
        "notification_type" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_type",
        "notification_reason" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_reason",
        "state" => $db_tables[DB_NDOUTILS]["notifications"] . ".state",
        "output" => $db_tables[DB_NDOUTILS]["notifications"] . ".output",
        "long_output" => $db_tables[DB_NDOUTILS]["notifications"] . ".long_output",
    );

    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $default_order = " `start_time` DESC, `notification_id` DESC";
    $args = array(
        "sql" => $sqlquery['GetNotificationsWithContacts'] . $sqlmods,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
        //add group by clause to prevent duplicate entries
        // removed 10/22/2012. This tanks mysql with larger result sets
        //"groupby" => " GROUP BY `notification_id`,`contact_object_id`,`contactnotification_id`,`contactnotificationmethod_id` ",
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    //echo "<BR>SQL: $sql<BR>";

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, true))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<notifications>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <notification>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'notification_type');
                $output .= get_xml_db_field(2, $rs, 'notification_reason');
                $output .= get_xml_db_field(2, $rs, 'escalated');
                $output .= get_xml_db_field(2, $rs, 'start_time');
                $output .= get_xml_db_field(2, $rs, 'object_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'service_description');
                $output .= get_xml_db_field(2, $rs, 'state');
                $output .= get_xml_db_field(2, $rs, 'output');
                $output .= get_xml_db_field(2, $rs, 'contact_name');
                $output .= get_xml_db_field(2, $rs, 'contact_object_id');
                $output .= get_xml_db_field(2, $rs, 'notification_command');
                $output .= get_xml_db_field(2, $rs, 'command_object_id');
                $output .= get_xml_db_field(2, $rs, 'command_args');

                $output .= get_xml_db_field(2, $rs, 'contactnotificationmethod_id');
                $output .= get_xml_db_field(2, $rs, 'contactnotification_id');
                $output .= get_xml_db_field(2, $rs, 'contact_object_id');
                $output .= get_xml_db_field(2, $rs, 'notification_id');

                //$output.=get_xml_db_field(2,$rs,'long_output');
                $output .= "  </notification>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</notifications>\n";
    }

    return $output;
}


/**
 * @param $request_args
 *
 * @return string
 */
function get_notifications_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";
    // by default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND start_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";
    // optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "")
        $sqlmods .= " AND start_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";


    $output = "";

    // generate query
    $fieldmap = array(
        "notification_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_id",
        "instance_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".instance_id",
        "start_time" => $db_tables[DB_NDOUTILS]["notifications"] . ".start_time",
        "object_id" => $db_tables[DB_NDOUTILS]["notifications"] . ".object_id",
        "objecttype_id" => "obj1.objecttype_id",
        "host_name" => "obj1.name1",
        "service_description" => "obj1.name2",
        "notification_type" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_type",
        "notification_reason" => $db_tables[DB_NDOUTILS]["notifications"] . ".notification_reason",
        "state" => $db_tables[DB_NDOUTILS]["notifications"] . ".state",
        "output" => $db_tables[DB_NDOUTILS]["notifications"] . ".output",
        "long_output" => $db_tables[DB_NDOUTILS]["notifications"] . ".long_output",
    );

    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $default_order = "start_time DESC, notification_id DESC";
    $args = array(
        "sql" => $sqlquery['GetNotifications'] . $sqlmods,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST,
        "default_order" => $default_order,
        "useropts" => $request_args, // ADDED 12/22/09 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    //echo "<BR>SQL: $sql<BR>";

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();
        $output .= "<notifications>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <notification>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id', 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'notification_type');
                $output .= get_xml_db_field(2, $rs, 'notification_reason');
                $output .= get_xml_db_field(2, $rs, 'start_time');
                $output .= get_xml_db_field(2, $rs, 'object_id');
                $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'service_description');
                $output .= get_xml_db_field(2, $rs, 'state');
                $output .= get_xml_db_field(2, $rs, 'output');
                //$output.=get_xml_db_field(2,$rs,'long_output');
                $output .= "  </notification>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</notifications>\n";
    }

    return $output;
}

