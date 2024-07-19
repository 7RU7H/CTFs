<?php

// Copyright (c) 2019-2022 Nagios Enterprises, Inc. All Rights Reserved.

/***
 * This script works around a bug in debian perl, which prevents snmptt from submitting check results to nagios.cmd.
 * Instead, we'll read these results from a file in /usr/local/nagiosxi/var
 * and submit them from here after some sanity checking.
 * On enterprise linux systems, this script does nothing.
 */

$snmptt_store = '/etc/snmp/nagios-check-storage';

if (!file_exists($snmptt_store)) {
        echo "No file at $snmptt_store\n";
        exit;
}

$results = file($snmptt_store);
file_put_contents($snmptt_store, '');

for ($i = 0; $i < count($results); $i++) {
        $result = trim($results[$i]);
        echo "Processing: $result\n";
        if (empty($result)) {
                echo "Line empty\n";
                continue;
        }
        /** Regex explanation
         *
         * ^\[                                                                       - Require that the first character is a [
         *    [0-9]+\]                                                               - Match a number, followed by a closing bracket
         *            PROCESS_(HOST|SERVICE)_CHECK_RESULT                            - Only allow check results as commands.
         *                                               ;[^;]+;[^;]+;[^;]+(;[^;]*)? - Match 3 or 4 groups of data after, delimited by ;
         */
        $validation_regex = '/^\[[0-9]+\] PROCESS_(HOST|SERVICE)_CHECK_RESULT;[^;]+;[^;]+;[^;]+(;[^;]*)?/';
        $match = preg_match($validation_regex, $result);
        $cmd_file = '/usr/local/nagios/var/rw/nagios.cmd';
        if ($match !== false && file_exists($cmd_file)) {
                // Write the result to nagios.cmd
                $cmd = "printf \"$result\n\" > $cmd_file";
                echo "Succesfully matched!\nCommand is $cmd\n";
                exec($cmd, $out, $rc);
                $out = implode("\n", $out);
                echo "printf returned code $rc and message $out\n";
        }
}