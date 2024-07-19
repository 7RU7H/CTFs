<?php
//
// Audit Log Functions
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// REPORTING FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get XML audit log output
 *
 * @param   array   $args   Array of arguments
 * @return  object          A SimpleXMLObject of XML output
 */
function get_xml_auditlog($args)
{
    $x = simplexml_load_string(get_auditlog_xml_output($args));
    return $x;
}


////////////////////////////////////////////////////////////////////////
// AUDIT LOG FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Send to audit log function with sane defaults
 *
 * @param   string  $message
 * @param   int     $type
 * @param   string  $source
 * @param   string  $user
 * @param   string  $ipaddress
 * @return  bool
 */
function send_to_audit_log($message, $type = AUDITLOGTYPE_NONE, $source = "", $user = "", $ipaddress = "", $details = "")
{
    $logtime = time();

    if (empty($user)) {
        $user = get_user_attr(0, "username");
    }

    if (empty($source)) {
        $source = AUDITLOGSOURCE_USER_INTERFACE;
    }

    if (empty($ipaddress)) {
        $ipaddress = "localhost";
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $ipaddress = $_SERVER["REMOTE_ADDR"];
        }
    }

    $auditlogfile = get_option("auditlogfile", 0);

    $args = array(
        "time" => $logtime,
        "source" => $source,
        "user" => $user,
        "type" => $type,
        "ipaddress" => $ipaddress,
        "message" => $message,
        "details" => $details,
        "auditlogfile" => $auditlogfile
    );

    return record_log_in_db($args);
}

function auditlog_setup_nls($hostname, $port) {

    $log_file_path = get_root_dir() . '/var/components/auditlog.log';
    $log_file_tag = 'xi_auditlog';

    $args = array(
        'tag' => 'xi_auditlog',
        'hostname' => $hostname,
        'port' => $port,
        'file' => $log_file_path
    );
    $id = submit_command(COMMAND_SEND_TO_LOGSERVER, serialize($args));

    return $id;
}

function auditlog_teardown_nls() {
    $args = array('tag' => 'xi_auditlog');
    $id = submit_command(COMMAND_STOP_SENDING_TO_LOGSERVER, serialize($args));
    return $id;
}

/**
 * Record the actual log entry in the database
 *
 * @param   array   $arr    Array of log data (message, time, source, etc)
 * @return  bool            True if log was added successfully
 */
function record_log_in_db($arr = null)
{
    global $db_tables;
    global $cfg;

    if (!is_array($arr)) {
        return false;
    }

    $logtime = grab_array_var($arr, "time", time());
    $source = grab_array_var($arr, "source", "User Interface");
    $user = grab_array_var($arr, "user", get_user_attr(0, "username"));
    $type = grab_array_var($arr, "type", AUDITLOGTYPE_NONE);
    $message = grab_array_var($arr, "message", "");
    $details = grab_array_var($arr, "details", "");
    $auditlogfile = grab_array_var($arr, "auditlogfile", 0);

    if (isset($_SERVER["REMOTE_ADDR"])) {
        $ip = $_SERVER["REMOTE_ADDR"];
    } else {
        $ip = "localhost";
    }
    $ipaddress = grab_array_var($arr, "ip_address", $ip);

    $t = date("Y-m-d H:i:s", $logtime);

    if ($cfg['db_info']['nagiosxi']['dbtype'] == "mysql") {
        $u = 'user';
    } else {
        $u = '"user"';
    }

    $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["auditlog"] . " (log_time,source,$u,type,message,ip_address,details) VALUES ('" . escape_sql_param($t, DB_NAGIOSXI) . "','" . escape_sql_param($source, DB_NAGIOSXI) . "','" . escape_sql_param($user, DB_NAGIOSXI) . "'," . escape_sql_param($type, DB_NAGIOSXI) . ",'" . escape_sql_param($message, DB_NAGIOSXI) . "','" . escape_sql_param($ipaddress, DB_NAGIOSXI) . "','" . escape_sql_param($details, DB_NAGIOSXI) . "')";

    // Write to the audit log file as well!
    if (empty($user)) {
        $user = 'system';
    }

    $log = $t . " - " . $source . " [" . $type . "] " . $user . ":" . $ipaddress . " - " . $message . PHP_EOL;

    file_put_contents('/usr/local/nagiosxi/var/components/auditlog.log', $log, FILE_APPEND);

    if (!exec_sql_query(DB_NAGIOSXI, $sql)) {
        return false;
    }

    return true;
}

/**
 * Update the audit log database with new username
 *
 * @param   string  $old    Old username
 * @param   string  $new    New username
 * @return  bool            True if database log entry update was successful
 */
function update_audit_log_entires($old, $new)
{
    global $db_tables;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["auditlog"] . " SET user = '" . escape_sql_param($new, DB_NAGIOSXI) . "' WHERE user = '" . escape_sql_param($old, DB_NAGIOSXI) . "'";
    if (!exec_sql_query(DB_NAGIOSXI, $sql)) {
        return false;
    }

    return true;
}
