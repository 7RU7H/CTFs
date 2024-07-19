<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// BACKEND OBJECT FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @return SimpleXMLElement
 */
function get_xml_sysstat_data()
{
    $x = @simplexml_load_string(get_sysstat_data_xml_output());
    return $x;
}


////////////////////////////////////////////////////////////////////////
// SYSTAT FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param $metric
 * @return null
 */
function get_systat_value($metric)
{
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["sysstat"] . " WHERE metric='" . escape_sql_param($metric, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, false))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["value"];
        }
    }
    return null;
}


/**
 * @param $metric
 * @param $value
 * @return mixed
 */
function update_systat_value($metric, $value)
{
    global $db_tables;

    // see if data exists already
    $key_exists = false;
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["sysstat"] . " WHERE metric='" . escape_sql_param($metric, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0)
            $key_exists = true;
    }

    // insert new key
    if ($key_exists == false) {
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["sysstat"] . " (metric,value,update_time) VALUES ('" . escape_sql_param($metric, DB_NAGIOSXI) . "','" . escape_sql_param($value, DB_NAGIOSXI) . "',NOW())";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    } // update existing key
    else {
        $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["sysstat"] . " SET value='" . escape_sql_param($value, DB_NAGIOSXI) . "', update_time=NOW() WHERE metric='" . escape_sql_param($metric, DB_NAGIOSXI) . "'";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    }
}
