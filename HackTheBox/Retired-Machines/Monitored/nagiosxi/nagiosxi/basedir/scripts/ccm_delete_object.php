#!/usr/bin/php -q
<?php
//
// Delete Object(s) from the CCM
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

// Include XI codebase
require_once(dirname(__FILE__) . '/../html/includes/constants.inc.php');
require_once(dirname(__FILE__) . '/../html/config.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/common.inc.php');

// Boostrap the CCM
require_once(dirname(__FILE__) . '/../html/includes/components/ccm/bootstrap.php');

// Connect to the database
// (required for calling the nagiosql_x functions)
db_connect(DB_NAGIOSQL);
db_connect(DB_NAGIOSXI);

// Pass options into getopt for parsing of $argv
$short = "t:i:n:c:h";
$long = array("type:", "id:", "name:", "config:", "help");
$options = getopt($short, $long);

// Grab the values required out of the array
$help = ! grab_array_var($options, "help", grab_array_var($options, "h", true));
$type = grab_array_var($options, "type", grab_array_var($options, "t", ''));
$id = grab_array_var($options, "id", grab_array_var($options, "i", 0));
$name = grab_array_var($options, "name", grab_array_var($options, "n", ''));
$config = grab_array_var($options, "config", grab_array_var($options, "c", ''));

if (!empty($id) && !empty($config)) {
    print "You must specify only one. Either an ID or a config name.\n";
    exit(1);
}

if (!empty($id) && !empty($name)) {
    print "You must specify only one. Either an ID or a host name.\n";
    exit(1);
}

// Help section
if ($help) {
    print "\nDeletes an object (or list of objects) from the CCM database.\n";
    print "\nUsage: ./ccm_delete_host.php [option] ..\n";
    print "\nExample: Remove all services from service with config name localhost\n";
    print "         ./ccm_delete_host.php -t service -c localhost\n";
    print "\nUsage Options:\n";
    print "    -t|--type <type>             Object type (host, service, contact, timeperiod)\n";
    print "    -i|--id <object id>          (OPTIONAL) Object ID\n";
    print "\Host Type Options:\n";
    print "    -n|--name <host name>        (OPTIONAL) Host name (host type only)\n";
    print "\nService Type Options:\n";
    print "    -c|--config <config name>    (OPTIONAL) Config name to remove all services from (service type only)\n";
    print "\nHelp and Logging:\n";
    print "    -h|--help                    Print out help output\n";
    print "\n";
    exit(0);
}

// Check for type and do action
if (!empty($type)) {

    switch ($type) {

        case 'host':
            do_host_deletion($id, $name);
            break;

        case 'service':
            do_service_deletion($id, $config);
            break;

        case 'contact':
            do_contact_deletion($id);
            break;

        case 'timeperiod':
            do_timeperiod_deletion($id);
            break;

        default:
            break;

    }

}

// Exit with error code 1 - no type selcted
exit_with_code(1, "A type must be selected using -t|--type. Use -h|--help to see more usage information.");


// ============================
// HELPER FUNCTIONS
// ============================


/**
 * Delete the host based on id or name given
 *
 * @param   int     $id     Object ID of a host
 * @param   string  $name   Host name
 */
function do_host_deletion($id=0, $name='')
{
    if (!empty($name)) {
        // Validate that host name exists and that there are no dependencies
        $id = nagiosql_get_host_id($name);
        if ($id > 0) {
            if (nagiosql_host_is_in_dependency($name) || nagiosql_host_has_services($name) || nagiosql_host_is_related_to_other_hosts($name)) {
                exit_with_code(3, "Unable to delete host '$name' because it has dependent relationships.");
            }
        } else {
            exit_with_code(2, "Could not find host '$name' in the CCM database.");
        }
    }

    // Delete the host by id
    if (!empty($id)) {
        list($code, $message) = delete_object('host', $id);
        if ($code) {
            exit_with_code($code, $message);
        } else {
            exit_with_code(0, "Successfully removed host from CCM database.");
        }
    }

    exit_with_code(1, "When using type 'host' you must specify one:\n-i|--id (object id)\n-n|--name (host name)");
}


/**
 * Delete the service based on id, name, or config given
 *
 * @param   int     $id         Object ID of a service
 * @param   string  $config     Config name
 */
function do_service_deletion($id=0, $config='')
{
    if (!empty($config)) {
        $errors = 0;
        $output = '';

        // Get a list of services that can be removed
        $ids = get_deletable_services_by_config_name($config);

        // Try to remove all the services in the list
        if (!empty($ids)) {
            foreach ($ids as $id) {
                list($code, $message) = delete_object('service', $id);
                if ($code) {
                    $errors++;
                    $output .= $message;
                }
            }
        } else {
            exit_with_code(1, "No services were found for config '$config' in the CCM database.");
        }

        // If there was any errors removing and of the services
        if ($errors > 0) {
            exit_with_code(2, $message);
        } else {
            $num = count($ids);
            exit_with_code(0, "Successfully removed $num services.");
        }

    } else {

        // Delete an individual service based on ID
        if (!empty($id)) {
            list($code, $message) = delete_object('service', $id);
            if ($code) {
                exit_with_code($code, $message);
            } else {
                exit_with_code(0, "Successfully removed service from CCM database.");
            }
        }

    }

    exit_with_code(1, "When using type 'service' you must specifiy one:\n-i|--id (object id)\n-c|--config (confg name)");
}


/**
 * Gets an array of service object IDs for a config
 *
 * @param   string  $config     Config name
 * @return  array               Array of object IDs
 */
function get_deletable_services_by_config_name($config)
{
    global $db_tables;

    // Do SQL check to find services linked to the config name
    $sql = "SELECT `id`,`config_name`,`service_description` FROM ".$db_tables[DB_NAGIOSQL]["service"]." 
            WHERE `config_name`='".escape_sql_param($config, DB_NAGIOSQL)."'";
    $rs = exec_sql_query(DB_NAGIOSQL, $sql);
    if (!$rs) {
        exit_with_code(1, "Failed to retrieve service ID's of config name '$config' from CCM database.");
    }

    // Check to make sure the services are actually services that can be removed
    $ids = array();
    foreach($rs as $r) {
        if (!nagiosql_is_service_unique($config, $r['service_description']) || nagiosql_service_is_in_dependency($config, $r['service_description'])) {
            continue;
        }
        $ids[] = $r['id'];
    }

    return $ids;
}


/**
 * Do deletion of contact based on ID passed
 *
 * @param   int     $id     Object ID
 */
function do_contact_deletion($id=0)
{
    if (!empty($id)) {
        list($code, $message) = delete_object('contact', $id);
        if ($code) {
            exit_with_code($code, $message);
        } else {
            exit_with_code(0, "Successfully removed contact from CCM database.");
        }
    }

    exit_with_code(1, "When using type 'contact' you must specify one:\n-i|--id (object id)");
}


/**
 * Do deletion of time period based on ID passed
 *
 * @param   int     $id     Object ID
 */
function do_timeperiod_deletion($id=0)
{
    if (!empty($id)) {
        list($code, $message) = delete_object('timeperiod', $id);
        if ($code) {
            exit_with_code($code, $message);
        } else {
            exit_with_code(0, "Successfully removed timeperiod from CCM database.");
        }
    }

    exit_with_code(1, "When using type 'timeperiod' you must specify one:\n-i|--id (object id)");
}


/**
 * Exit from the script with an error code and message provided
 *
 * @param   int     $code       Exit code
 * @param   string  $message    Text message printed to screen
 */
function exit_with_code($code=99, $message='')
{
    print $message."\n";
    exit($code);
}

