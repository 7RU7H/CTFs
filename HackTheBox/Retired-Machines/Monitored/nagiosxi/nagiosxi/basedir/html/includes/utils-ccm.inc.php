<?php
//
// Copyright (c) 2009-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// DELETION FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param      $hostname
 * @param      $servicename
 * @param bool $cascade
 *
 * @return bool
 */
function nagiosccm_can_service_be_deleted($hostname, $servicename, $cascade = false)
{
    // Make sure the host is in NagiosQL
    if (($serviceid = nagiosccm_get_service_id($hostname, $servicename)) <= 0) {
        return false;
    }

    // Make sure service is unique, and not an advanced setup (using wildcards, multiple hosts, etc)
    if (nagiosccm_is_service_unique($serviceid) == false) {
        return false;
    }

    if ($cascade == false) {
        // Make sure service is not in a dependency
        if (nagiosccm_service_is_in_dependency($serviceid) == true) {
            return false;
        }
    }

    return true;
}


/**
 * @param      $hostname
 * @param bool $cascade
 *
 * @return bool
 */
function nagiosccm_can_host_be_deleted($hostname, $cascade = false)
{
    // Make sure the host is in NagiosQL
    if (($hostid = nagiosql_get_host_id($hostname)) <= 0) {
        return false;
    }

    // See if associated services can be deleted too
    if ($cascade == true) {
        // ??
    } else {
        // Make sure host doesn't have any services
        if (nagiosql_host_has_services($hostname) == true) {
            return false;
        }
        // Make sure host is not in a dependency
        if (nagiosql_host_is_in_dependency($hostname) == true) {
            return false;
        }
        // Make sure host is not related to other hosts (e.g. parent host)
        if (nagiosql_host_is_related_to_other_hosts($hostname) == true) {
            return false;
        }
    }

    return true;
}


///////////////////////////////////////////////////////////////////////////////////////////
// HOST FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


/**
 * @param $hostname
 *
 * @return int
 */
function nagiosccm_get_host_id($hostname)
{
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSQL]["host"] . " WHERE host_name = BINARY '" . escape_sql_param($hostname, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if (!$rs->EOF) {
            return intval($rs->fields["id"]);
        }
    }

    return -1;
}


/**
 * @param $hostid
 *
 * @return bool
 */
function nagiosccm_host_is_in_dependency($hostid)
{
    global $db_tables;

    // See if host is a master host in a dependency
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkHostdependencyToHost_H"] . " WHERE idSlave='" . escape_sql_param($hostid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    // See if host is a dependent host in a dependency
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkHostdependencyToHost_DH"] . " WHERE idSlave='" . escape_sql_param($hostid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    return false;
}


/**
 * @param $hostid
 *
 * @return bool
 */
function nagiosccm_host_has_services($hostid)
{
    global $db_tables;

    // See if host has services associated with it
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . " WHERE idSlave='" . escape_sql_param($hostid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    return false;
}


/**
 * @param $hostid
 *
 * @return bool
 */
function nagiosccm_host_is_related_to_other_hosts($hostid)
{
    global $db_tables;

    // See if host is related to other hosts
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkHostToHost"] . " WHERE idMaster='" . escape_sql_param($hostid, DB_NAGIOSQL) . "' OR  idSlave='" . escape_sql_param($hostid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    return false;
}


///////////////////////////////////////////////////////////////////////////////////////////
// SERVICE FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


/**
 * @param $hostname
 * @param $servicename
 *
 * @return int
 */
function nagiosccm_get_service_id($hostname, $servicename)
{
    global $db_tables;

    $sql = "SELECT 
" . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . ".idMaster as service_id,
" . $db_tables[DB_NAGIOSQL]["host"] . ".id as host_id,
" . $db_tables[DB_NAGIOSQL]["host"] . ".host_name as host_name,
" . $db_tables[DB_NAGIOSQL]["service"] . ".service_description
FROM " . $db_tables[DB_NAGIOSQL]["service"] . "
LEFT JOIN " . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . " ON " . $db_tables[DB_NAGIOSQL]["service"] . ".id=" . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . ".idMaster
LEFT JOIN " . $db_tables[DB_NAGIOSQL]["host"] . " ON " . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . ".idSlave=" . $db_tables[DB_NAGIOSQL]["host"] . ".id
 WHERE " . $db_tables[DB_NAGIOSQL]["host"] . ".host_name = BINARY '" . escape_sql_param($hostname, DB_NAGIOSQL) . "' AND " . $db_tables[DB_NAGIOSQL]["service"] . ".service_description = BINARY '" . escape_sql_param($servicename, DB_NAGIOSQL) . "'";

    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if (!$rs->EOF) {
            return intval($rs->fields["service_id"]);
        }
    }

    return -1;
}


/**
 * @param $serviceid
 *
 * @return bool
 */
function nagiosccm_is_service_unique($serviceid)
{
    global $db_tables;

    if ($serviceid <= 0) {
        return false;
    }

    // Check flags in service definition to see if there are wildcards used for host or hostgroup
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkServiceToHostgroup"] . " WHERE idMaster='" . escape_sql_param($serviceid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if (!$rs->EOF) {

            $host_flag = intval($rs->fields["host_name"]);
            $hostgroup_flag = intval($rs->fields["hostgroup_name"]);

            // Service is associated with one or more ( or wildcard) hostgroups, so its not unique
            if ($hostgroup_flag != 0) {
                return false;
            }

            // Service is associated with no( or wildcard) hosts, so its probably not unique
            if ($host_flag != 1) {
                return false;
            }
        }
    }

    // See if service is associated with multiple hosts (or no hosts)
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkServiceToHost"] . " WHERE idMaster='" . escape_sql_param($serviceid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 1) {
            return false;
        }
    }

    // next see if service is associated with one or more hostgroups
    // NOTE - already taken care of by checking hostgroup_flag above...
    /*
    $sql="SELECT  * FROM ".$db_tables[DB_NAGIOSQL]["lnkServiceToHostgroup"]." WHERE idMaster='".escape_sql_param($serviceid,DB_NAGIOSQL)."'";
    if(($rs=exec_sql_query(DB_NAGIOSQL,$sql))){
        if($rs->RecordCount()>0)
            return false;
        }
    */

    return true;
}


/**
 * @param $serviceid
 *
 * @return bool
 */
function nagiosccm_service_is_in_dependency($serviceid)
{
    global $db_tables;

    // See if service is a master service in a dependency
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkServicedependencyToService_S"] . " WHERE idSlave='" . escape_sql_param($serviceid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    // See if service is a dependent service in a dependency
    $sql = "SELECT  * FROM " . $db_tables[DB_NAGIOSQL]["lnkServicedependencyToService_DS"] . " WHERE idSlave='" . escape_sql_param($serviceid, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if ($rs->RecordCount() != 0) {
            return true;
        }
    }

    return false;
}


// --------------------------------------------------
// Other CCM Functions
// --------------------------------------------------


/**
 * Get the object ID from the CCM database
 *
 * @param   string  $type   The type of object (command, contact, etc)
 * @param   string  $name   The type_name in defininition
 * @return  int             Object ID
 */
function nagiosccm_get_object_id($type, $name)
{
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSQL][$type] . " WHERE " . $type . "_name='" . escape_sql_param($name, DB_NAGIOSQL) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
        if (!$rs->EOF) {
            return intval($rs->fields["id"]);
        }
    }

    return -1;
}


function nagiosccm_replace_nonmacro_dollars($target, $replacement)
{
    /* List of nagios core macros - not sure if this is anywhere else in the php source*/
    $macros_prepended_dollar = array(
        '\$HOSTNAME',
        '\$HOSTDISPLAYNAME',
        '\$HOSTALIAS',
        '\$HOSTADDRESS',
        '\$HOSTSTATE',
        '\$HOSTSTATEID',
        '\$LASTHOSTSTATE',
        '\$LASTHOSTSTATEID',
        '\$HOSTSTATETYPE',
        '\$HOSTATTEMPT',
        '\$MAXHOSTATTEMPTS',
        '\$HOSTEVENTID',
        '\$LASTHOSTEVENTID',
        '\$HOSTPROBLEMID',
        '\$LASTHOSTPROBLEMID',
        '\$HOSTLATENCY',
        '\$HOSTEXECUTIONTIME',
        '\$HOSTDURATION',
        '\$HOSTDURATIONSEC',
        '\$HOSTDOWNTIME',
        '\$HOSTPERCENTCHANGE',
        '\$HOSTGROUPNAME',
        '\$HOSTGROUPNAMES',
        '\$LASTHOSTCHECK',
        '\$LASTHOSTSTATECHANGE',
        '\$LASTHOSTUP',
        '\$LASTHOSTDOWN',
        '\$LASTHOSTUNREACHABLE',
        '\$HOSTOUTPUT',
        '\$LONGHOSTOUTPUT',
        '\$HOSTPERFDATA',
        '\$HOSTCHECKCOMMAND',
        '\$HOSTACKAUTHOR',
        '\$HOSTACKAUTHORNAME',
        '\$HOSTACKAUTHORALIAS',
        '\$HOSTACKCOMMENT',
        '\$HOSTACTIONURL',
        '\$HOSTNOTESURL',
        '\$HOSTNOTES',
        '\$TOTALHOSTSERVICES',
        '\$TOTALHOSTSERVICESOK',
        '\$TOTALHOSTSERVICESWARNING',
        '\$TOTALHOSTSERVICESUNKNOWN',
        '\$TOTALHOSTSERVICESCRITICAL',
        '\$HOSTGROUPALIAS',
        '\$HOSTGROUPMEMBERS',
        '\$HOSTGROUPNOTES',
        '\$HOSTGROUPNOTESURL',
        '\$HOSTGROUPACTIONURL',
        '\$SERVICEDESC',
        '\$SERVICEDISPLAYNAME',
        '\$SERVICESTATE',
        '\$SERVICESTATEID',
        '\$LASTSERVICESTATE',
        '\$LASTSERVICESTATEID',
        '\$SERVICESTATETYPE',
        '\$SERVICEATTEMPT',
        '\$MAXSERVICEATTEMPTS',
        '\$SERVICEISVOLATILE',
        '\$SERVICEEVENTID',
        '\$LASTSERVICEEVENTID',
        '\$SERVICEPROBLEMID',
        '\$LASTSERVICEPROBLEMID',
        '\$SERVICELATENCY',
        '\$SERVICEEXECUTIONTIME',
        '\$SERVICEDURATION',
        '\$SERVICEDURATIONSEC',
        '\$SERVICEDOWNTIME',
        '\$SERVICEPERCENTCHANGE',
        '\$SERVICEGROUPNAME',
        '\$SERVICEGROUPNAMES',
        '\$LASTSERVICECHECK',
        '\$LASTSERVICESTATECHANGE',
        '\$LASTSERVICEOK',
        '\$LASTSERVICEWARNING',
        '\$LASTSERVICEUNKNOWN',
        '\$LASTSERVICECRITICAL',
        '\$SERVICEOUTPUT',
        '\$LONGSERVICEOUTPUT',
        '\$SERVICEPERFDATA',
        '\$SERVICECHECKCOMMAND',
        '\$SERVICEACKAUTHOR',
        '\$SERVICEACKAUTHORNAME',
        '\$SERVICEACKAUTHORALIAS',
        '\$SERVICEACKCOMMENT',
        '\$SERVICEACTIONURL',
        '\$SERVICENOTESURL',
        '\$SERVICENOTES',
        '\$SERVICEGROUPALIAS',
        '\$SERVICEGROUPMEMBERS',
        '\$SERVICEGROUPNOTES',
        '\$SERVICEGROUPNOTESURL',
        '\$SERVICEGROUPACTIONURL',
        '\$CONTACTNAME',
        '\$CONTACTALIAS',
        '\$CONTACTEMAIL',
        '\$CONTACTPAGER',
        '\$CONTACTADDRESSn',
        '\$CONTACTGROUPALIAS',
        '\$CONTACTGROUPMEMBERS',
        '\$TOTALHOSTSUP',
        '\$TOTALHOSTSDOWN',
        '\$TOTALHOSTSUNREACHABLE',
        '\$TOTALHOSTSDOWNUNHANDLED',
        '\$TOTALHOSTSUNREACHABLEUNHANDLED',
        '\$TOTALHOSTPROBLEMS',
        '\$TOTALHOSTPROBLEMSUNHANDLED',
        '\$TOTALSERVICESOK',
        '\$TOTALSERVICESWARNING',
        '\$TOTALSERVICESCRITICAL',
        '\$TOTALSERVICESUNKNOWN',
        '\$TOTALSERVICESWARNINGUNHANDLED',
        '\$TOTALSERVICESCRITICALUNHANDLED',
        '\$TOTALSERVICESUNKNOWNUNHANDLED',
        '\$TOTALSERVICEPROBLEMS',
        '\$TOTALSERVICEPROBLEMSUNHANDLED',
        '\$NOTIFICATIONTYPE',
        '\$NOTIFICATIONRECIPIENTS',
        '\$NOTIFICATIONISESCALATED',
        '\$NOTIFICATIONAUTHOR',
        '\$NOTIFICATIONAUTHORNAME',
        '\$NOTIFICATIONAUTHORALIAS',
        '\$NOTIFICATIONCOMMENT',
        '\$HOSTNOTIFICATIONNUMBER',
        '\$HOSTNOTIFICATIONID',
        '\$SERVICENOTIFICATIONNUMBER',
        '\$SERVICENOTIFICATIONID',
        '\$LONGDATETIME',
        '\$SHORTDATETIME',
        '\$DATE',
        '\$TIME',
        '\$TIMET',
        '\$ISVALIDTIME',
        '\$NEXTVALIDTIME',
        '\$MAINCONFIGFILE',
        '\$STATUSDATAFILE',
        '\$COMMENTDATAFILE',
        '\$DOWNTIMEDATAFILE',
        '\$RETENTIONDATAFILE',
        '\$OBJECTCACHEFILE',
        '\$TEMPFILE',
        '\$TEMPPATH',
        '\$LOGFILE',
        '\$RESOURCEFILE',
        '\$COMMANDFILE',
        '\$HOSTPERFDATAFILE',
        '\$SERVICEPERFDATAFILE',
        '\$PROCESSSTARTTIME',
        '\$EVENTSTARTTIME',
        '\$ADMINEMAIL',
        '\$ADMINPAGER',
        '\$CHECKSOURCE',
    );
    for ($i = 1; $i < 33; $i++) {
        $macros_prepended_dollar[] = '\$ARG'. strval($i);
    }
    for ($i = 1; $i < 257; $i++) {
        $macros_prepended_dollar[] = '\$USER'. strval($i);
    }

    $macros_appended_dollar = array_map(function($a) { return substr($a, 2) . '\$'; }, $macros_prepended_dollar);

    $is_normal_dollar = '/(?<!' . implode('|', $macros_prepended_dollar) . ')\$(?!' . implode('|', $macros_appended_dollar) . ')/';
    return preg_replace($is_normal_dollar, preg_quote($replacement), $target);
}

/* Nagios Core Special Character Handling:
 * - By default, ! must be turned into \! in bash
 * - For a $ that starts/ends a macro, nothing should happen
 * - Otherwise, Core needs a $$ to recognize a single $, and bash needs a \$ to recognize a non-variable
 * - If you intentionally use bash variables (e.g. $RANDOM), invoke this function multiple times with different arguments
 *   (e.g. set $nonmacro_dollar_replacement to '$$')
 */
function nagiosccm_replace_command_line($argument, $nonmacro_dollar_replacement='\$$', $exclam_replacement='\!')
{
    $argument = str_replace('!', $exclam_replacement, $argument);
    $argument = nagiosccm_replace_nonmacro_dollars($argument, $nonmacro_dollar_replacement);
    return $argument;
}

/**
 * Replace user macros with Nagios macros in resource.cfg
 *
 * @param   string  $str
 * @return  mixed
 */
function nagiosccm_replace_user_macros($str = "")
{
    if (empty($str)) {
        return "";
    }

    $cfg_file_data = nagiosccm_get_resource_cfg(false, true, false);

    // Replace macros in the string given
    $newstr = str_replace($cfg_file_data["user_macros"], $cfg_file_data["user_macro_values"], $str);
    return $newstr;
}


/**
 * Read resource.cfg file and return raw text or array of contents
 *
 * @param   bool    $raw
 * @return  mixed
 */
function nagiosccm_get_resource_cfg($raw = 0, $time = 1, $um_component = true) {
    $usermacro_disable = get_option("usermacro_disable", 0);
    $usermacro_redacted = get_option("usermacro_redacted", 1);
    $usermacro_user_redacted = get_option("usermacro_user_redacted", 0);

    if ($usermacro_disable == 1 && $um_component) {
        return _("The User Macro Component has been disabled. Contact your Administrator.");
    }

    // trigger new timestamp by default
    if ($time)
        $time = time();

    // Check if raw file was requested
    if ($raw) {
        $lines = file('/usr/local/nagios/etc/resource.cfg', FILE_IGNORE_NEW_LINES);
    } else {
        $lines = file('/usr/local/nagios/etc/resource.cfg', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Grab the resource.cfg and read it
    $user_macros = array();
    $user_macro_values = array();
    $lines_redacted = array();

    foreach ($lines as $k => $line) {
        if ($raw) {
            if (($usermacro_redacted || ($usermacro_user_redacted == 1 && is_admin() == false)) && $um_component) {
                $pos = strpos($line, "=");

                if ($pos !== false) {
                    $line = substr_replace($line, "*****", $pos + 1);
                }

                $lines_redacted[] = $line;
            } else {
                // return raw file contents - Not redacted
                return $lines;
            }
        } else {
            if ($line[0] != "#") {
                list($macro, $value) = explode("=", $line);
                $user_macros[] = trim($macro);

                if ($um_component) {
                    if ($usermacro_redacted == 1) {
                        $user_macro_values[] = "*****";
                    } else if ($usermacro_user_redacted == 1 && is_admin() == false) {
                        $user_macro_values[] = "*****";
                    } else {
                        $user_macro_values[] = trim($value);
                    }
                } else {
                    $user_macro_values[] = trim($value);
                }
            }
        }
    }

    // Return array
    if (!$raw) {
        return array("user_macros" => $user_macros, "user_macro_values" => $user_macro_values, "last_read" => $time);
    } else if ($raw && ($usermacro_redacted || $usermacro_user_redacted)) {
        return $lines_redacted;
    }
}


/**
 * Verify current USER macros to existing ones and if no matches found write the new macro to resource.cfg
 *
 * @param           $macro
 * @param           $new_value
 * @param           $write_time
 * @return  array                   Array containing return code and reponse string
 */
function nagiosccm_add_new_macro($macro, $new_value, $write_time)
{
    $current = nagiosccm_get_resource_cfg(false, false);
    $return = array();
    $datetime = get_datetime_string(time(), DT_SHORT_DATE_TIME, DF_AUTO, "null");
    $user = $_SESSION["username"];

    // Double check macro key doesn't exist
    if (in_array($macro, $current['user_macros'])) {
        $return["response"] = _("This User Macro Key already exists.  Please check the value and try again or choose another User Macro Key.");
        $return["return_code"] = 1;

        return $return;
    }

    // If macro value already exists exit
    if (in_array($new_value, $current['user_macro_values'])) {
        $return["response"] = _("This User Macro Value already exists in the resource.cfg file and can be used by using selected key.");
        $return["return_code"] = 1;

        return $return;
    }

    // Construct Macro config line with added by info
    $new_line = "\n# Created by " . $user . " - [" . $datetime . "] \n" . $macro . "=" . $new_value . "\n";

    // Write to file
    $write = file_put_contents('/usr/local/nagios/etc/resource.cfg', $new_line, FILE_APPEND);

    // Check for success
    if ($write) {
        $return["response"] = _("Successfuly added new user macro.");
        $return["return_code"] = 0;
    } else {
        $return["response"] = _("Writing to /usr/local/nagios/etc/resource.cfg failed.  Verify the file is set as user apache and group nagios with read, write and execute permissions.");
        $return["return_code"] = 1;
    }

    return json_encode($return);
}


/**
 * @param $objects
 * @param $firsthost
 */
function get_cfg_objects_str($objects, &$firsthost)
{
    $have_first_host = false;
    $ncfg = "";

    // FIRST PROCESS NON-SERVICES
    foreach ($objects as $oid => $obj) {

        // Get the object type
        $oname = "";
        switch ($obj["type"]) {
            case OBJECTTYPE_HOST:
                $oname = "host";
                break;
            case OBJECTTYPE_HOSTGROUP:
                $oname = "hostgroup";
                break;
            case OBJECTTYPE_SERVICEGROUP:
                $oname = "servicegroup";
                break;
            case OBJECTTYPE_COMMAND:
                $oname = "command";
                break;
            case OBJECTTYPE_CONTACT:
                $oname = "contact";
                break;
            case OBJECTTYPE_CONTACTGROUP:
                $oname = "contactgroup";
                break;
            case OBJECTTYPE_TIMEPERIOD:
                $oname = "timeperiod";
                break;
            case OBJECTTYPE_HOSTESCALATION:
                $oname = "hostescalation";
                break;
            case OBJECTTYPE_SERVICEESCALATION:
                $oname = "serviceescalation";
                break;
            case OBJECTTYPE_HOSTDEPENDENCY:
                $oname = "hostdependency";
                break;
            case OBJECTTYPE_SERVICEDEPENDENCY:
                $oname = "servicedependency";
                break;
            default:
                break;
        }

        // Unhandled object types
        if ($oname == "") {
            continue;
        }

        // Write the object definition to file
        $ncfg .= "define " . $oname . "{\n";

        foreach ($obj as $var => $val) {
            if ($var == "type")
                continue;
            $ncfg .= $var . "\t" . $val . "\n";

            if ($oname == "host" && $have_first_host == false && $var == "host_name") {
                $have_first_host = true;
                $firsthost = $val;

                // Preload ndoutils with host
                add_ndoutils_object(OBJECTTYPE_HOST, $firsthost);
            }
        }
        $ncfg .= "}\n";
    }

    // PROCESS SERVICES
    $openfile = false;
    $importfiles = array();
    $multihost = false;
    foreach ($objects as $oid => $obj) {

        if (!$have_first_host) {
            if (!array_key_exists("host_name", $obj)) {
                $multihost = true;
            } else {
                $have_first_host = true;
                $firsthost = $obj["host_name"];
            }
        } else {
            if ($firsthost != $obj["host_name"]) {
                $multihost = true;
            }
        }

        // Get the object type
        $oname = "";
        switch ($obj["type"]) {
            case OBJECTTYPE_SERVICE:
                $oname = "service";

                // Preload ndoutils with service
                if (!$multihost) {
                    add_ndoutils_object(OBJECTTYPE_SERVICE, $obj["host_name"], $obj["service_description"]);
                }

                break;
            default:
                break;
        }

        // unhandled object types
        if ($oname == "")
            continue;

        // write the object definition to file
        $ncfg .= "define " . $oname . "{\n";

        foreach ($obj as $var => $val) {
            if ($var == "type")
                continue;
            $ncfg .= $var . "\t" . $val . "\n";
        }
        $ncfg .= "}\n";
    }

    return $ncfg;
}

function nagiosccm_set_table_modified($type) {
    $ccm_modified_array = get_array_option('ccm_modified_tables_array', array());
    $ccm_modified_array[$type] = 1;
    set_array_option('ccm_modified_tables_array', $ccm_modified_array);
}

function nagiosccm_get_table_modified($type) {
    $ccm_modified_array = get_array_option('ccm_modified_tables_array', array());
    return isset($ccm_modified_array[$type]) && $ccm_modified_array[$type] === 1;
}

function nagiosccm_clear_tables_modified() {
    set_array_option('ccm_modified_tables_array', array());
}