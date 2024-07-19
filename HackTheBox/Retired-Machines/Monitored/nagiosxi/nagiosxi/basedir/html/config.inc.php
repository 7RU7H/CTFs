<?php
//
// Main Nagios XI Configuration
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

// Base url
// Do not include http(s) or host name - this is the base after "://<hostname>"
$cfg['base_url'] = "/nagiosxi";

// Base root directory where XI is installed
$cfg['root_dir'] = "/usr/local/nagiosxi";

// Directory where scripts are installed
$cfg['script_dir'] = "/usr/local/nagiosxi/scripts";

$cfg['xidpe_dir'] = '/usr/local/nagios/var/spool/xidpe/';
$cfg['perfdata_spool'] = '/usr/local/nagios/var/spool/perfdata/';

// Nom checkpoints
$cfg['nom_checkpoints_dir'] = "/usr/local/nagiosxi/nom/checkpoints/nagioscore/";

// Force http/https
$cfg['use_https'] = false; // determines whether cron jobs and other scripts will force the use of HTTPS instead of HTTP

// Allow for different http port for subsystem calls 
$cfg['port_number'] = false;

// Default server, db, connection settings
$cfg['dbtype'] = ''; // this setting is no longer used - use settings below
$cfg['dbserver'] = 'localhost'; // this setting is no longer used - use settings below

// Database connection type
// 1 = persistent, 0 = normal
$cfg['db_conn_persistent'] = 1;

// DB-specific connection information
$cfg['db_info'] = array(
    "nagiosxi" => array(
        "dbtype" => 'mysql',
        "dbserver" => '',
        "user" => 'nagiosxi',
        "pwd" => 'n@gweb',
        "db" => 'nagiosxi',
        "charset" => "utf8",
        "dbmaint" => array( // variables affecting maintenance of db
            "max_auditlog_age" => 180, // max time (in DAYS) to keep audit log entries
            "max_commands_age" => 480, // max time (minutes) to keep commands
            "max_events_age" => 480, // max time (minutes) to keep events
            "optimize_interval" => 60, // time (in minutes) between db optimization runs
            "repair_interval" => 0, // time (in minutes) between db repair runs
        ),
    ),
    "ndoutils" => array(
        "dbtype" => 'mysql',
        "dbserver" => 'localhost',
        "user" => 'ndoutils',
        "pwd" => 'n@gweb',
        "db" => 'nagios',
        "charset" => "utf8",
        "dbmaint" => array( // variables affecting maintenance of ndoutils db
            "max_externalcommands_age" => 7, // max time (in DAYS) to keep external commands
            "max_logentries_age" => 90, // max time (in DAYS) to keep log entries
            "max_statehistory_age" => 730, // max time (in DAYS) to keep state history information
            "max_notifications_age" => 90, // max time (in DAYS) to keep notifications
            "max_timedevents_age" => 5, // max time (minutes) to keep timed events
            "max_systemcommands_age" => 5, // max time (minutes) to keep system commands
            "max_servicechecks_age" => 5, // max time (minutes) to keep service checks
            "max_hostchecks_age" => 5, // max time (minutes) to keep host checks
            "max_eventhandlers_age" => 5, // max time (minutes) to keep event handlers
            "optimize_interval" => 60, // time (in minutes) between db optimization runs
            "repair_interval" => 0, // time (in minutes) between db repair runs
        ),
    ),
    "nagiosql" => array(
        "dbtype" => 'mysql',
        "dbserver" => 'localhost',
        "user" => 'nagiosql',
        "pwd" => 'n@gweb',
        "db" => 'nagiosql',
        "charset" => "utf8",
        "dbmaint" => array( // variables affecting maintenance of db
            "max_logbook_age" => 480, // max time (minutes) to keep log book records
            "optimize_interval" => 60, // time (in minutes) between db optimization runs
            "repair_interval" => 0, // time (in minutes) between db repair runs
        ),
    ),
);

// Override the db setup with ENV variables globally (for testing mostly)
$GLOBAL_MYSQL_HOST = getenv('GLOBAL_MYSQL_HOST');
$GLOBAL_MYSQL_USER = getenv('GLOBAL_MYSQL_USER');
$GLOBAL_MYSQL_PASS = getenv('GLOBAL_MYSQL_PASS');
if (!empty($GLOBAL_MYSQL_HOST)) {
    $cfg['db_info']['nagiosxi']['dbserver'] = $GLOBAL_MYSQL_HOST;
    $cfg['db_info']['ndoutils']['dbserver'] = $GLOBAL_MYSQL_HOST;
    $cfg['db_info']['nagiosql']['dbserver'] = $GLOBAL_MYSQL_HOST;
}
if (!empty($GLOBAL_MYSQL_USER)) {
    $cfg['db_info']['nagiosxi']['user'] = $GLOBAL_MYSQL_USER;
    $cfg['db_info']['ndoutils']['user'] = $GLOBAL_MYSQL_USER;
    $cfg['db_info']['nagiosql']['user'] = $GLOBAL_MYSQL_USER;
}
if (!empty($GLOBAL_MYSQL_PASS)) {
    $cfg['db_info']['nagiosxi']['pwd'] = $GLOBAL_MYSQL_PASS;
    $cfg['db_info']['ndoutils']['pwd'] = $GLOBAL_MYSQL_PASS;
    $cfg['db_info']['nagiosql']['pwd'] = $GLOBAL_MYSQL_PASS;
}

// db-specific table prefixes
$cfg['db_prefix'] = array(
    "ndoutils" => "nagios_", // prefix for NDOUtils tables
    "nagiosxi" => "xi_", // prefix for XI tables
    "nagiosql" => "tbl_", // prefix for NagiosQL tables
);

// component info
$cfg['component_info'] = array(
    "nagioscore" => array(
        "root_dir" => "/usr/local/nagios",
        "cgi_dir" => "/usr/local/nagios/sbin",
        "import_dir" => "/usr/local/nagios/etc/import",
        "static_dir" => "/usr/local/nagios/etc/static",
        "plugin_dir" => "/usr/local/nagios/libexec",
        "cgi_config_file" => "/usr/local/nagios/etc/cgi.cfg",
        "cmd_file" => "/usr/local/nagios/var/rw/nagios.cmd",
        "log_file" => "/usr/local/nagios/var/nagios.log",
        "nom_checkpoint_interval" => 1440, // time (in minutes) between nom checkpoints
    ),
    "pnp" => array(
        "perfdata_dir" => "/usr/local/nagios/share/perfdata",
        "share_dir" => "/usr/local/nagios/share/pnp",
        "direct_url" => "/nagios/pnp",
        "username" => 'nagiosxi', // don't change this!
        "password" => 'nagiosadmin', // this gets reset when security credentials are reset after installation
    ),
    "perfdata" => array(
        "rrdtool_path" => "/usr/bin/rrdtool",
    ),
    "nagvis" => array(
        "share_dir" => "/usr/local/nagios/share/nagvis",
        "direct_url" => "/nagios/nagvis",
        "username" => 'nagiosadmin', // don't change this!
        "password" => 'nagiosadmin', // this gets reset when security credentials are reset after installation
    ),
);

$cfg['demo_mode'] = false; // is this in demo mode

$cfg['dashlet_refresh_multiplier'] = 1000; // milliseconds (1 second = 1000)

// REFRESH RATES FOR VARIOUS DASHLETS (IN SECONDS UNLESS THE MULTIPLIER IS CHANGED)
$cfg['dashlet_refresh_rates'] = array(
    "available_updates" => 24 * 60 * 60, // 24 hours
    "systat_eventqueuechart" => 5,
    "sysstat_monitoringstats" => 30,
    "systat_monitoringperf" => 30,
    "sysstat_monitoringproc" => 30,
    "perfdata_chart" => 60, // performance graphs
    "network_outages" => 30,
    "host_status_summary" => 60,
    "service_status_summary" => 60,
    "hostgroup_status_overview" => 60,
    "hostgroup_status_grid" => 60,
    "servicegroup_status_overview" => 60,
    "servicegroup_status_grid" => 60,
    "hostgroup_status_summary" => 60,
    "servicegroup_status_summary" => 60,
    "sysstat_componentstates" => 7,
    "sysstat_serverstats" => 5,
    "network_outages_summary" => 30,
    "network_health" => 30,
    "host_status_tac_summary" => 30,
    "service_status_tac_summary" => 30,
    "feature_status_tac_summary" => 30,
    "admin_tasks" => 60,
    "getting_started" => 60,
    "pagetop_alert_content" => 30, // not a dashlet yet, sits in page header
    "tray_alert" => 30, // sites in page footer
);


// MEMCACHED SETUP	
$cfg['memcached_enable'] = false; // should we use memcached or not?
$cfg['memcached_hosts'] = array('127.0.0.1'); // one or more memcached servers
$cfg['memcached_port'] = 11211; // default memcached port
$cfg['memcached_compress'] = false; // use true to store items compressed
$cfg['memcached_ttl'] = 10; // max number of seconds data (from SELECT statements) should be cached


// HTTP BASIC AUTHENTICATION INFO -- USED BY SUBSYSTEM
$cfg['use_basic_authentication'] = false; // is HTTP Basic authentication being used? if so, set the two variables below...
$cfg['subsystem_basic_auth_username'] = 'nagiosxi'; // subsystem credentials
$cfg['subsystem_basic_auth_password'] = 'somepassword';

$cfg['default_language'] = 'en_US'; // default language
$cfg['default_theme'] = ''; // default theme

// available languages
$cfg['languages'] = array(
    "en_US" => "English",
);

// Globally disable in page help system
// $cfg['disable_helpsystem'] = 1;

/*********   DO NOT MODIFY ANYTHING BELOW THIS LINE   **********/

$cfg['default_instance_id'] = 1; // default ndoutils instance to read from
$cfg['default_result_records'] = 100000; // max number of records to return by default

$cfg['online_help_url'] = "https://support.nagios.com/"; // comment this out to disable online help links
$cfg['feedback_url'] = "https://api.nagios.com/feedback/";
$cfg['privacy_policy_url'] = "https://www.nagios.com/legal/privacypolicy/";

$cfg['db_version'] = 113;

$cfg['htaccess_file'] = "/usr/local/nagiosxi/etc/htpasswd.users";
$cfg['htpasswd_path'] = "/usr/bin/htpasswd";

///////// keep these in order /////////

if (!defined('CFG_ONLY')) {

    // include generic db defs
    require_once(dirname(__FILE__) . '/includes/db.inc.php');

    // include generic  definitions
    require_once(dirname(__FILE__) . '/db/common.inc.php');

}
