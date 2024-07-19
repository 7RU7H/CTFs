<?php
/* This script should only be called from snmptt.conf */

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/common.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/components/nxti/includes/utils-traps.inc.php');
// Initialization stuff

db_connect(DB_NAGIOSXI);

//pre_init();
//init_session();

// Grab GET or POST variables and check pre-reqs

//grab_request_vars();
//check_prereqs();
//check_authentication(false);

/* this is the start page command. this takes care of
   all the setup of the page. everything after this
   function is page content */

$shortopts = "";
$longopts = array(
  "event_name:",
  "event_oid:",
  "numeric_oid:",
  "symbolic_oid:",
  "community:",
  "trap_hostname:",
  "trap_ip:",
  "agent_hostname:",
  "agent_ip:",
  "category:",
  "severity:",
  "uptime:",
  "datetime:",
  "bindings:",
  "unixtime:"
);

$options = getopt($shortopts, $longopts);

foreach ($longopts as $option) {
  $option = trim($option, ":");
  $$option = "";
  if(array_key_exists($option, $options))
    $$option = $options[$option];
}

$result = add_trapdata_log($event_name, $event_oid, $numeric_oid, $symbolic_oid, $community, $trap_hostname, $trap_ip, $agent_hostname, $agent_ip, $category, $severity, $uptime, $datetime, $unixtime, $bindings);

exit();

?>
