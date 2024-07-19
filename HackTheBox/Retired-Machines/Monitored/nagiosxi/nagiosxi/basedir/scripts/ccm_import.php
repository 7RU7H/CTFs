#!/usr/bin/php -q
<?php
//
// Import Files into CCM Database
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

// Include XI codebase
require_once(dirname(__FILE__) . '/../html/includes/constants.inc.php');
require_once(dirname(__FILE__) . '/../html/config.inc.php');

// Boostrap the CCM
require_once(dirname(__FILE__) . '/../html/includes/components/ccm/bootstrap.php');

// Import all files from the import directory
$dir = "/usr/local/nagios/etc/import/";
$errors = false;

// Connect to the Nagios XI database
// (required for writeLog() function)
db_connect(DB_NAGIOSXI);

print "\n--- ccm_import.php -------------------\n";
print "> Setting import directory: $dir\n";
print "> Importing config files into the CCM\n";

// Get list of files to import and import each one of them separately
$fl = file_list($dir, "/.*\.cfg/");
if (!empty($fl)) {
    foreach ($fl as $f) {
        $file = $dir . $f;

        print "  - Importing: $file .. ";

        // Import the file
        $error = $ccm->import->fileImport($file, 1);
        $ccm->data->writeLog(_('File imported - File [overwrite flag]:')." ".$file." [1]");

        // If error, output error
        if ($error) {
            $errors = true;
            print "ERROR\n";
            print "   " . $ccm->import->strDBMessage . $ccm->import->strMessage . "\n";
        } else {
            print "SUCCESS\n";
            unlink($file);
        }
    }
} else {
    print "  No files to import\n";
}

print "--------------------------------------\n";

// Exit properly on errors
if ($errors) {
    exit(2);
}

exit(0);
