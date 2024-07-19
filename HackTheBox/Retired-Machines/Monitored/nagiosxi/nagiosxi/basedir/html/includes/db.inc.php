<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../config.inc.php');

// DB related includes
require_once(dirname(__FILE__) . '/../db/adodb/adodb.inc.php');
require_once(dirname(__FILE__) . '/dbl.inc.php');
require_once(dirname(__FILE__) . '/dbauth.inc.php');

$DB = array();

// Initialize table names
init_db_table_names();


////////////////////////////////////////////////////////////////////////
// TABLE NAME FUNCTIONS
////////////////////////////////////////////////////////////////////////


function init_db_table_names()
{
    global $db_tables;

    $db_tables = array();

    // XI table names
    generate_table_name(DB_NAGIOSXI, "auditlog");
    generate_table_name(DB_NAGIOSXI, "commands");
    generate_table_name(DB_NAGIOSXI, "events");
    generate_table_name(DB_NAGIOSXI, "meta");
    generate_table_name(DB_NAGIOSXI, "mibs");
    generate_table_name(DB_NAGIOSXI, "options");
    generate_table_name(DB_NAGIOSXI, "sysstat");
    generate_table_name(DB_NAGIOSXI, "usermeta");
    generate_table_name(DB_NAGIOSXI, "users");
    generate_table_name(DB_NAGIOSXI, "sessions");
    generate_table_name(DB_NAGIOSXI, "auth_tokens");
    generate_table_name(DB_NAGIOSXI, "deploy_jobs");
    generate_table_name(DB_NAGIOSXI, "deploy_agents");
    generate_table_name(DB_NAGIOSXI, "banner_messages");
    generate_table_name(DB_NAGIOSXI, "link_users_messages");

    // XI component tables
    generate_table_name(DB_NAGIOSXI, "cmp_trapdata");
    generate_table_name(DB_NAGIOSXI, "cmp_trapdata_log");
    generate_table_name(DB_NAGIOSXI, "cmp_ccm_backups");
    generate_table_name(DB_NAGIOSXI, "cmp_nagiosbpi_backups");
    generate_table_name(DB_NAGIOSXI, "cmp_favorites");
    generate_table_name(DB_NAGIOSXI, "cmp_scheduledreports_log");

    // NagiosQL table names
    generate_table_name(DB_NAGIOSQL, "contact");
    generate_table_name(DB_NAGIOSQL, "contactgroup");
    generate_table_name(DB_NAGIOSQL, "host");
    generate_table_name(DB_NAGIOSQL, "hostgroup");
    generate_table_name(DB_NAGIOSQL, "lnkHostgroupToHost");
    generate_table_name(DB_NAGIOSQL, "lnkHostToHost");
    generate_table_name(DB_NAGIOSQL, "lnkHostToHostgroup");
    generate_table_name(DB_NAGIOSQL, "lnkHostdependencyToHost_DH");
    generate_table_name(DB_NAGIOSQL, "lnkHostdependencyToHost_H");
    generate_table_name(DB_NAGIOSQL, "lnkServiceToHost");
    generate_table_name(DB_NAGIOSQL, "lnkServicedependencyToService_DS");
    generate_table_name(DB_NAGIOSQL, "lnkServicedependencyToService_S");
    generate_table_name(DB_NAGIOSQL, "lnkServiceToHost");
    generate_table_name(DB_NAGIOSQL, "lnkServiceToHostgroup");
    generate_table_name(DB_NAGIOSQL, "lnkServiceToServicegroup");
    generate_table_name(DB_NAGIOSQL, "logbook");
    generate_table_name(DB_NAGIOSQL, "service");
    generate_table_name(DB_NAGIOSQL, "servicegroup");
    generate_table_name(DB_NAGIOSQL, "timeperiod");
    generate_table_name(DB_NAGIOSQL, "timedefinition");
    generate_table_name(DB_NAGIOSQL, "hosttemplate");
    generate_table_name(DB_NAGIOSQL, "servicetemplate");
    generate_table_name(DB_NAGIOSQL, "user");
    generate_table_name(DB_NAGIOSQL, "command");

    // NDOUtils table names
    generate_table_name(DB_NDOUTILS, "acknowledgements");
    generate_table_name(DB_NDOUTILS, "commands");
    generate_table_name(DB_NDOUTILS, "commenthistory");
    generate_table_name(DB_NDOUTILS, "comments");
    generate_table_name(DB_NDOUTILS, "configfiles");
    generate_table_name(DB_NDOUTILS, "configfilevariables");
    generate_table_name(DB_NDOUTILS, "conninfo");
    generate_table_name(DB_NDOUTILS, "contact_addresses");
    generate_table_name(DB_NDOUTILS, "contact_notificationcommands");
    generate_table_name(DB_NDOUTILS, "contactgroup_members");
    generate_table_name(DB_NDOUTILS, "contactgroups");
    generate_table_name(DB_NDOUTILS, "contactnotificationmethods");
    generate_table_name(DB_NDOUTILS, "contactnotifications");
    generate_table_name(DB_NDOUTILS, "contacts");
    generate_table_name(DB_NDOUTILS, "contactstatus");
    generate_table_name(DB_NDOUTILS, "customvariables");
    generate_table_name(DB_NDOUTILS, "customvariablestatus");
    generate_table_name(DB_NDOUTILS, "dbversion");
    generate_table_name(DB_NDOUTILS, "downtimehistory");
    generate_table_name(DB_NDOUTILS, "eventhandlers");
    generate_table_name(DB_NDOUTILS, "externalcommands");
    generate_table_name(DB_NDOUTILS, "flappinghistory");
    generate_table_name(DB_NDOUTILS, "host_contactgroups");
    generate_table_name(DB_NDOUTILS, "host_contacts");
    generate_table_name(DB_NDOUTILS, "host_parenthosts");
    generate_table_name(DB_NDOUTILS, "hostchecks");
    generate_table_name(DB_NDOUTILS, "hostdependencies");
    generate_table_name(DB_NDOUTILS, "hostescalation_contactgroups");
    generate_table_name(DB_NDOUTILS, "hostescalation_contacts");
    generate_table_name(DB_NDOUTILS, "hostescalations");
    generate_table_name(DB_NDOUTILS, "hostgroup_members");
    generate_table_name(DB_NDOUTILS, "hostgroups");
    generate_table_name(DB_NDOUTILS, "hosts");
    generate_table_name(DB_NDOUTILS, "hoststatus");
    generate_table_name(DB_NDOUTILS, "instances");
    generate_table_name(DB_NDOUTILS, "logentries");
    generate_table_name(DB_NDOUTILS, "notifications");
    generate_table_name(DB_NDOUTILS, "objects");
    generate_table_name(DB_NDOUTILS, "processevents");
    generate_table_name(DB_NDOUTILS, "programstatus");
    generate_table_name(DB_NDOUTILS, "runtimevariables");
    generate_table_name(DB_NDOUTILS, "scheduleddowntime");
    generate_table_name(DB_NDOUTILS, "service_contactgroups");
    generate_table_name(DB_NDOUTILS, "service_contacts");
    generate_table_name(DB_NDOUTILS, "servicechecks");
    generate_table_name(DB_NDOUTILS, "servicedependencies");
    generate_table_name(DB_NDOUTILS, "serviceescalation_contactgroups");
    generate_table_name(DB_NDOUTILS, "serviceescalation_contacts");
    generate_table_name(DB_NDOUTILS, "serviceescalations");
    generate_table_name(DB_NDOUTILS, "servicegroup_members");
    generate_table_name(DB_NDOUTILS, "servicegroups");
    generate_table_name(DB_NDOUTILS, "services");
    generate_table_name(DB_NDOUTILS, "servicestatus");
    generate_table_name(DB_NDOUTILS, "statehistory");
    generate_table_name(DB_NDOUTILS, "systemcommands");
    generate_table_name(DB_NDOUTILS, "timedeventqueue");
    generate_table_name(DB_NDOUTILS, "timedevents");
    generate_table_name(DB_NDOUTILS, "timeperiod_timeranges");
    generate_table_name(DB_NDOUTILS, "timeperiods");

    $args = array();
    do_callbacks(CALLBACK_TABLE_NAMES_GENERATED, $args);

    //print_r($db_tables);
}


/**
 * @param string $package
 * @param string $tablename
 */
function generate_table_name($package = "unknown", $tablename = "unknown")
{
    global $cfg;
    global $db_tables;

    $db_tables[$package][$tablename] = $cfg['db_prefix'][$package] . $tablename;
}


////////////////////////////////////////////////////////////////////////
// CONFIG FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * @param        $dbname
 * @param        $iname
 * @param string $default
 *
 * @return null|string
 */
function get_database_interval($dbname, $iname, $default = "")
{
    global $cfg;

    $val = null;

    // first try to get saved option from db
    $oname = $dbname . "_db_" . $iname;
    $opt = get_option($oname);
    if ($opt != "")
        $val = $opt;

    // get default from config file
    else if (array_key_exists($dbname, $cfg['db_info'])) {
        if (array_key_exists("dbmaint", $cfg['db_info'][$dbname])) {
            $val = grab_array_var($cfg['db_info'][$dbname]["dbmaint"], $iname);
        }
    }

    if ($val == null)
        $val = $default;

    return $val;
}


/**
 * @param $dbname
 * @param $iname
 * @param $val
 */
function set_database_interval($dbname, $iname, $val)
{

    $oname = $dbname . "_db_" . $iname;

    set_option($oname, $val);
}


////////////////////////////////////////////////////////////////////////
// CONNECTION FUNCTIONS
////////////////////////////////////////////////////////////////////////

//$db_connect_all_done=false;

/**
 * @return bool
 */
function db_connect_all()
{
    $start_time = time();
    $time = 0;
    $timeout = 20;
    $all_connected = false;
    $error = '';

    $xi_conn = false;
    $ndo_conn = false;
    $ql_conn = false;

    // Try to connect to the databases
    do {
        try {
            if (!$xi_conn) {
                $xi_conn = db_connect(DB_NAGIOSXI);
            }
            if (!$ndo_conn) {
                $ndo_conn = db_connect(DB_NDOUTILS);
            }
            if (!$ql_conn) {
                $ql_conn = db_connect(DB_NAGIOSQL);
            }
            $all_connected = true;
        }
        catch (Exception $e) {
            $error = $e->getMessage();
            sleep(2);
        }
        $time = time() - $start_time;
    } while ($timeout > $time && !$all_connected);

    // If, after timeout, we aren't connected - display error and return
    if (!$all_connected) {
        if (!defined("SUBSYSTEM")) {
            echo '<h3>'. _('Database Error').'</h3>';
            if (!empty($error)) {
                echo $error;
            }
            echo '<p>' . _('Run the following from the CLI as root to attempt to repair the DB') . ':<br><pre>' . get_root_dir() . '/scripts/repair_databases.sh</pre></p>';
            exit();
        } else {
            print "Database Error: Could not connect to database\n";
            print $error."\n";
            exit(1);
        }
    }

    return true;
}

/**
 * @param      $db
 * @param null $opts
 *
 * @return bool
 * @throws Exception
 */
function db_connect($db, $opts = null)
{
    global $cfg;
    global $DB;

    // global defaults
    $dbtype = $cfg['dbtype'];
    $dbserver = $cfg['dbserver'];

    if (array_key_exists("dbtype", $cfg['db_info'][$db]))
        $dbtype = $cfg['db_info'][$db]['dbtype'];
    if (array_key_exists("dbserver", $cfg['db_info'][$db])) {
        $dbserver = $cfg['db_info'][$db]['dbserver'];
    }

    if ($opts == null) {
        $opts = array(
            "user" => $cfg['db_info'][$db]['user'],
            "pwd" => $cfg['db_info'][$db]['pwd'],
            "db" => $cfg['db_info'][$db]['db'],
        );
    }

    $username = $opts["user"];
    $password = $opts["pwd"];
    $dbname = $opts["db"];

    // Updated settings for mysqli
    if (empty($dbserver)) { $dbserver = 'localhost'; }
    if ($dbtype == 'mysql') { $dbtype = 'mysqli'; }

    $DB[$db] = NewADOConnection($dbtype);
    $DB[$db]->SetFetchMode(ADODB_FETCH_ASSOC);
    $DB[$db]->autoRollback = true;

    // optional memcached support
    if (grab_array_var($cfg, 'memcached_enable') == true) {
        $DB[$db]->memCache = true;
        $DB[$db]->memCacheHost = $cfg['memcached_hosts'];
        $DB[$db]->memCachePost = $cfg['memcached_port'];
        $DB[$db]->memCacheCompress = $cfg['memcached_compress'];

        // set caching ttl
        $ttl = grab_array_var($cfg, 'memcached_ttl', 10);
        $DB[$db]->cacheSecs = $ttl;
    }

    // Set up persistent connection or not
    $db_conn_persistent = grab_array_var($cfg, 'db_conn_persistent', 1);
    if ($db_conn_persistent) {
        $dbh = $DB[$db]->PConnect($dbserver, $username, $password, $dbname);
    } else {
        $dbh = $DB[$db]->Connect($dbserver, $username, $password, $dbname);
    }

    if (!$dbh) {
        if (defined('SUBSYSTEM')) {
            throw new Exception($DB[$db]->ErrorMsg());
        }
        throw new Exception(_("A database connection error has been detected, please follow the repair prompt below. If the issue persists, please contact Nagios support."));
    }

    // Set charset to UTF-8 to fix characters
    $db_conn_utf8 = grab_array_var($cfg, 'db_conn_utf8', 1);
    if ($dbtype == 'mysqli') {
        // Update for XI on el8 systems (XI 5.6.8+) but leave old way in if config does not have
        // the new way of setting the charset via the config charset
        if (isset($cfg['db_info'][$db]['charset'])) {
            $charset = grab_array_var($cfg['db_info'][$db], 'charset', 'utf8');
            $DB[$db]->setCharset($charset);
        } else if ($db_conn_utf8) {
            $DB[$db]->setCharset('utf8');
        }
    }

    return true;
}


//**********************************************************************************
//**
//** DBMS-SPECIFIC FUNCTIONS
//**
//**********************************************************************************


/**
 * Escape SQL parameters based on database type or handler (and quote optionally)
 * Important to use the database connection to aquire it's character set so the paramter
 * can be escaped correctly
 *
 * @param   string  $in         parameter to be escaped
 * @param   string  $dbh        Database Handler
 * @param   bool    $quote      Whether to add quotes
 * @return  string              
 */
function escape_sql_param($in, $dbh, $quote = false)
{
    global $cfg;
    global $DB;

    $escaped = "";

    if ($in === null) {
        $escaped = "NULL";
        $quote = false;
    } else if (is_bool($in)) {
        $quote = false;
    } else {
        switch ($dbh) {
            case DB_NAGIOSXI:
            case DB_NDOUTILS:
            case DB_NAGIOSQL:
                if (@isset($DB[$dbh])) {
                    $escaped = $DB[$dbh]->escape($in);
                } else {
                    // DB is not connected... so trigger error
                    // we don't know of an open conenction
                    trigger_error("DB is not connected", E_USER_NOTICE);
                }
                break;
            default:
                trigger_error("DB not specified in escape_sql_param", E_USER_NOTICE);
                break;
        }
    }

    // Quote the string if we told it to
    if ($quote == true) {
        $out = "'" . $escaped . "'";
    } else {
        $out = $escaped;
    }

    return $out;
}


/**
 * Creates an SQL-syntax version of a unix timestamp that can be pu
 * in place of a DATETIME or TIMESTAMP
 * Note: If timestamp is set to 0 it will use NOW() 
 *
 * @param   int     $t      Unix timestamp
 * @param   string  $dbh    Database handler type
 * @return  string          SQL syntax timestamp/datetime value
 */
function sql_time_from_timestamp($t = 0, $dbh)
{
    global $cfg;
    $dbtype = '';

    if (array_key_exists("dbtype", $cfg['db_info'][$dbh])) {
        $dbtype = $cfg['db_info'][$dbh]['dbtype'];
    }

    if ($t == 0) {
        $timestring = "NOW()";
    } else {
        $t = intval($t);
        switch ($dbtype) {
            case 'pgsql':
                $timestring = "$t::abstime::timestamp without time zone";
                break;
            default:
                $timestring = "FROM_UNIXTIME($t)";
                break;
        }
    }

    return $timestring;
}


////////////////////////////////////////////////////////////////////////
// SQL QUERY FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Runs a named SQL query that is pre-defined in the SQL $sqlquery array
 *
 * @param   string      $dbh            Database handler type
 * @param   string      $name           Name of the SQL query
 * @param   bool        $handle_error   True if errors should be handled
 * @param   bool        $allow_caching  True if caching should be enabled
 * @return  mixed|null
 */
function exec_named_sql_query($dbh, $name, $handle_error = true, $allow_caching = true)
{
    global $sqlquery;

    if (!have_value($name)) {
        return null;
    }

    return exec_sql_query($dbh, $sqlquery[$name], $handle_error, $allow_caching);
}


/**
 * Execute a SQL query on the passed database
 *
 * @param   string  $dbh            Database handler type
 * @param   string  $sql            SQL query
 * @param   bool    $handle_error   True if errors should be handled
 * @param   bool    $allow_caching  True if caching should be enabled
 * @return  mixed                   Results or false on error
 */
function exec_sql_query($dbh, $sql, $handle_error = true, $allow_caching = true)
{
    global $DB;
    global $cfg;

    $debug = false;

    if ($debug == true)
        $start_time = get_timer();

    if (!have_value($sql)) {
        trigger_error('Tried to execute an empty SQL query', E_USER_NOTICE);
        return null;
    }

    if (!$dbh) {
        trigger_error('Tried to execute a SQL query without a database handle', E_USER_NOTICE);
        return null;
    }

    if (!isset($DB[$dbh])) {
        ob_start();
        debug_print_backtrace();
        $debug_data = ob_get_clean();

        // Skip error if it is for a specific components
        if (strpos($debug_data, 'nagiosim') !== false || strpos($debug_data, 'duo_enabled') !== false
            || strpos($debug_data, 'reactor')  !== false) {
            return null;
        }

        trigger_error("Call made to exec_sql_query() when DB handler does not exist\n" . $debug_data, E_USER_NOTICE);
        return null;
    }

    // Optional memcached support (for queries that allow for caching)
    // and we only cache SELECT statements
    if (grab_array_var($cfg, 'memcached_enable') == true && $allow_caching == true) {
        if (substr($sql, 0, 6) == "SELECT") {
            $rs = $DB[$dbh]->CacheExecute($sql);
        } else {
            $rs = $DB[$dbh]->Execute($sql);
        }
    } else {
        $rs = $DB[$dbh]->Execute($sql);
    }

    if (!$rs && $handle_error == true) {
        handle_sql_error($dbh, $sql);
    } else {

        if ($debug == true) {
            $end_time = get_timer();
            $query_time = get_timer_diff($start_time, $end_time);

            $fh = fopen("/tmp/queries.csv", "a+");
            $line = $query_time . "," . str_replace("\n", " ", $sql) . "\n";
            fputs($fh, $line);
            fclose($fh);
        }

        return $rs;
    }
}


/**
 * Get the last SQL queries error message if one exists
 *
 * @param   string  $dbh    Database handler type
 * @return  string          Database error message
 */
function get_sql_error($dbh)
{
    global $DB;
    $d = $DB[$dbh];
    return $d->ErrorMsg();
}


/**
 * Get the last inserted ID of the SQL command
 *
 * @param   string  $dbh        Database handler type
 * @param   string  $seqname    Sequence name (postgresql only)
 * @return  int                 ID or -1 if none
 */
function get_sql_insert_id($dbh, $seqname = '')
{
    global $cfg;
    global $DB;

    $dbtype = '';

    if (array_key_exists("dbtype", $cfg['db_info'][$dbh])) {
        $dbtype = $cfg['db_info'][$dbh]['dbtype'];
    }

    // for postgresql we must get current value of sequence
    if ($dbtype == 'pgsql') {
        $id = -1;
        if ($seqname != '') {
            $sql = "SELECT currval('" . $seqname . "') AS newid;";
            if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, false))) {
                if ($rs->MoveFirst()) {
                    $id = intval($rs->fields['newid']);
                }
            }
        }
    } else {
        $id = $DB[$dbh]->Insert_ID();
    }

    if ($id == '') {
        $id = -1;
    }

    return $id;
}
