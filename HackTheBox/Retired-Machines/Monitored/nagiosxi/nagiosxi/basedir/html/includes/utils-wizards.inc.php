<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// WIZARD FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get the configuration wizard used for a specified hostname
 *
 * @param   string  $hostname   Host name
 * @return  string              Name of config wizard
 */
function get_host_configwizard($hostname)
{
    global $db_tables;
    $wizardname = "";

    // Find the config wizard name for hostusing saved meta-data
    $keyname = get_configwizard_meta_key_name2($hostname);
    if ($keyname != "") {

        $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["meta"] . " WHERE metatype_id='" . escape_sql_param(METATYPE_CONFIGWIZARD, DB_NAGIOSXI) . "' AND keyname LIKE '%" . escape_sql_param($keyname, DB_NAGIOSXI) . "'";

        if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {

            // Only find the first match
            if ($rs->MoveFirst()) {

                $dbkeyname = $rs->fields["keyname"];

                // Get the wizard name from the key
                $pos = strpos($dbkeyname, $keyname);
                if ($pos !== false) {
                    $wizardname = substr($dbkeyname, 0, $pos);
                }
            }
        }
    }

    return $wizardname;
}


/**
 * @param $hostname
 * @param $servicename
 *
 * @return string
 */
function get_service_configwizard($hostname, $servicename)
{
    global $db_tables;

    $wizardname = "";

    // find the config wizard name for service using saved meta-data
    $keyname = get_configwizard_meta_key_name2($hostname, $servicename);
    if ($keyname != "") {

        $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["meta"] . " WHERE metatype_id='" . escape_sql_param(METATYPE_CONFIGWIZARD, DB_NAGIOSXI) . "' AND keyname LIKE '%" . escape_sql_param($keyname, DB_NAGIOSXI) . "'";

        if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {

            // only find the first match
            if ($rs->MoveFirst()) {

                $dbkeyname = $rs->fields["keyname"];

                // get the wizard name from the key
                $pos = strpos($dbkeyname, $keyname);
                if ($pos !== false) {
                    $wizardname = substr($dbkeyname, 0, $pos);
                }
            }
        }
    }

    // if no wizard was found, try the host
    if ($wizardname == "") {
        $wizardname = get_host_configwizard($hostname);
    }

    return $wizardname;
}


// generates a meta key name that can be used for saving/retrieving config wizard data for configured items
/**
 * @param        $wizardname
 * @param        $hostname
 * @param string $servicename
 *
 * @return string
 */
function get_configwizard_meta_key_name($wizardname, $hostname, $servicename = "")
{
    $keyname = "";
    $keyname .= $wizardname;
    $keyname .= get_configwizard_meta_key_name2($hostname, $servicename);
    return $keyname;
}


// generates the host/service portion of the meta key - used for saving and retreiving the config wizard data later
/**
 * @param        $hostname
 * @param string $servicename
 *
 * @return string
 */
function get_configwizard_meta_key_name2($hostname, $servicename = "")
{
    $keyname = "";
    $keyname .= "__" . $hostname;
    $keyname .= "__" . $servicename;
    return $keyname;
}


// save config wizard data for later re-entrace
/**
 * @param $wizardname
 * @param $hostname
 * @param $servicename
 * @param $meta_arr
 */
function save_configwizard_object_meta($wizardname, $hostname, $servicename, $meta_arr)
{
    $meta_ser = serialize($meta_arr);
    set_meta(METATYPE_CONFIGWIZARD, 0, get_configwizard_meta_key_name($wizardname, $hostname, $servicename), $meta_ser);
}


// retrieves config wizard data
/**
 * @param $wizardname
 * @param $hostname
 * @param $servicename
 *
 * @return array|mixed
 */
function get_configwizard_object_meta($wizardname, $hostname, $servicename)
{
    $meta_arr = array();
    $meta_ser = get_meta(METATYPE_CONFIGWIZARD, 0, get_configwizard_meta_key_name($wizardname, $hostname, $servicename));
    if ($meta_ser != null) {
        $meta_arr = unserialize($meta_ser);
    }
    return $meta_arr;
}


// determines if config wizard data exists
/**
 * @param $wizardname
 * @param $hostname
 * @param $servicename
 *
 * @return bool
 */
function configwizard_object_meta_exists($wizardname, $hostname, $servicename)
{
    $meta_ser = get_meta(METATYPE_CONFIGWIZARD, 0, get_configwizard_meta_key_name($wizardname, $hostname, $servicename));
    if ($meta_ser != null) {
        return true;
    }
    return false;
}


/**
 * @param $hostname
 */
function delete_host_configwizard_meta($hostname)
{
    $wizardname = get_host_configwizard($hostname);
    $keyname = $wizardname . get_configwizard_meta_key_name2($hostname);
    delete_meta(METATYPE_CONFIGWIZARD, 0, $keyname);
}


/**
 * @param $hostname
 * @param $servicename
 */
function delete_service_configwizard_meta($hostname, $servicename)
{
    $wizardname = get_service_configwizard($hostname, $servicename);
    $keyname = $wizardname . get_configwizard_meta_key_name2($hostname, $servicename);
    delete_meta(METATYPE_CONFIGWIZARD, 0, $keyname);
}

