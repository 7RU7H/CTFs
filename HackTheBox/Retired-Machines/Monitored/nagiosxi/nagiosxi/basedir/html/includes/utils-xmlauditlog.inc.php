<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// AUDIT LOG
////////////////////////////////////////////////////////////////////////////////


/**
 * @param $request_args
 *
 * @return string
 */
function get_auditlog_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;
    global $request;

    // only let admins see this
    if (is_admin() == false) {
        exit;
    }

    // WE HAVE TO MUNGE THINGS HERE BECAUSE THE TIMESTAMPS CONTAINS COLONS AND WILL GET MESSED UP BY THE NORMAL SQL LIMITING LOGIC...
    $sqlmods = "";
    // by default, only show last 24 hours worth of logs
    $starttime = grab_array_var($request_args, "starttime", time() - (24 * 60 * 60));
    unset($request_args["starttime"]);
    $sqlmods .= " AND " . $db_tables[DB_NAGIOSXI]["auditlog"] . ".log_time >= '" . get_datetime_string($starttime, DT_SQL_DATE_TIME) . "'";
    // optional end time
    $endtime = grab_array_var($request_args, "endtime", "");
    if ($endtime != "")
        $sqlmods .= " AND " . $db_tables[DB_NAGIOSXI]["auditlog"] . ".log_time <= '" . get_datetime_string($endtime, DT_SQL_DATE_TIME) . "'";

    $output = "";

    // generate query
    $fieldmap = array(
        "auditlog_id" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".auditlog_id",
        "log_time" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".log_time",
        "source" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".source",
        "user" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".user",
        "type" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".type",
        "message" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".message",
        "ip_address" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".ip_address",
        "details" => $db_tables[DB_NAGIOSXI]["auditlog"] . ".details",
    );
    $args = array(
        "sql" => $sqlquery['GetAuditLog'] . $sqlmods,
        "fieldmap" => $fieldmap,
        "useropts" => $request_args, // FOR NON-BACKEND-CALLS
    );
    $sql = generate_sql_query(DB_NAGIOSXI, $args);

    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql)))
        handle_backend_db_error(DB_NAGIOSXI);
    else {
        $output .= "<auditlog>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <auditlogentry id='" . get_xml_db_field_val($rs, 'auditlog_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'auditlog_id', 'id');
                $output .= get_xml_db_field(2, $rs, 'log_time');
                $output .= get_xml_db_field(2, $rs, 'source');
                $output .= get_xml_db_field(2, $rs, 'user');
                $output .= get_xml_db_field(2, $rs, 'type');

                $typestr = intval($rs->fields["type"]);
                switch ($typestr) {
                    case AUDITLOGTYPE_ADD:
                        $typestr = "ADD";
                        break;
                    case AUDITLOGTYPE_DELETE:
                        $typestr = "DELETE";
                        break;
                    case AUDITLOGTYPE_MODIFY:
                        $typestr = "MODIFY";
                        break;
                    case AUDITLOGTYPE_CHANGE:
                        $typestr = "CHANGE";
                        break;
                    case AUDITLOGTYPE_SECURITY:
                        $typestr = "SECURITY";
                        break;
                    case AUDITLOGTYPE_INFO:
                        $typestr = "INFO";
                        break;
                    case AUDITLOGTYPE_OTHER:
                        $typestr = "OTHER";
                        break;
                    default:
                        break;
                }
                $output .= "    <typestr>" . $typestr . "</typestr>\n";

                $output .= get_xml_db_field(2, $rs, 'message');
                $output .= get_xml_db_field(2, $rs, 'details');
                $output .= get_xml_db_field(2, $rs, 'ip_address');
                $output .= "  </auditlogentry>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</auditlog>\n";
    }

    return $output;
}

