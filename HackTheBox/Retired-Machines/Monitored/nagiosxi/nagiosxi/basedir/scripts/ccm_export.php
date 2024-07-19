#!/usr/bin/php -q
<?php
//
// Export CCM Configuration to Files
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

// Include XI codebase
require_once(dirname(__FILE__) . '/../html/includes/constants.inc.php');
require_once(dirname(__FILE__) . '/../html/config.inc.php');

// Boostrap the CCM
require_once(dirname(__FILE__) . '/../html/includes/components/ccm/bootstrap.php');

// Connect to the Nagios XI database
// (required for set_options() inside the write_config_tool() function)
db_connect(DB_NAGIOSXI);

print "\n--- ccm_export.php -------------------\n";
print "> Writing CCM configuration to Nagios files\n";

// Write CCM configuration to files
list($code, $message) = write_config_tool('writeConfig');
set_option('apply_config_output', $message);

// Check for an error and print it out if there is one
if ($code > 0) {
    print "  " . $message . "\n";
    exit(4);
}

print "  Finished writing out configuraton\n";
print "--------------------------------------\n";

exit(0);
