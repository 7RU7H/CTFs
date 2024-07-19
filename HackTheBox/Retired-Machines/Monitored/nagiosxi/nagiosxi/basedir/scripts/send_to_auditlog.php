#!/usr/bin/php -q
<?php
//
// Send external commands or application log entries to the XI audit log
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/common.inc.php');

// Connect to database
$dbok = db_connect_all();
if ($dbok == false) {
    echo "ERROR CONNECTING TO DATABASES!\n";
    exit(7);
}

external_send_to_auditlog();

/*

// AUDIT LOG TYPES
define("AUDITLOGTYPE_NONE", 0);
define("AUDITLOGTYPE_ADD", 1); // Adding objects /users
define("AUDITLOGTYPE_DELETE", 2); // Deleting objects / users
define("AUDITLOGTYPE_MODIFY", 4); // Modifying objects / users
define("AUDITLOGTYPE_MODIFICATION", 4); // Modifying objects / users
define("AUDITLOGTYPE_CHANGE", 8); // Changes (reconfiguring system settings)
define("AUDITLOGTYPE_SYSTEMCHANGE", 8); // Changes (reconfiguring system settings)
define("AUDITLOGTYPE_SECURITY", 16); // Security-related events
define("AUDITLOGTYPE_INFO", 32); // Informational messages
define("AUDITLOGTYPE_OTHER", 64); // Other

// AUDIT LOG SOURCES
define("AUDITLOGSOURCE_USER_INTERFACE", "User Interface");
define("AUDITLOGSOURCE_CCM", "Core Config Manager");
define("AUDITLOGSOURCE_NAGIOSCORE", "Core");
define("AUDITLOGSOURCE_SUBSYSTEM", "Subsystem");
define("AUDITLOGSOURCE_API", "API");
define("AUDITLOGSOURCE_OTHER", "Other");

*/

function external_send_to_auditlog()
{
    global $argv; 
    $args = parse_argv($argv); 

    $message = grab_array_var($args, 'message', 'External Message');
    $type = grab_array_var($args, 'type', AUDITLOGTYPE_NONE);
    $source = grab_array_var($args, 'source', 'Other'); 
    $user = grab_array_var($args, 'user', 'Unknown');

    @send_to_audit_log($message, $type, $source, $user, 'localhost');   
    echo "Message successfully sent to audit log!";
    exit(0);
}
