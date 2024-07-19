#!/usr/bin/env php
<?php 

/* Usage: ./nxti_import.php <PATH>
 * Run this against a trap definition file (such that snmptt.conf) to parse 
 * data and insert it into the NXTI internal database. 
 */

// These 4 lines used to be require_once(common.inc.php)
// This saves us .07 seconds per script run (or 5 seconds off the "Process All Traps" button)
define("CFG_ONLY", 1);
require_once(dirname(__FILE__) . '/../html/config.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/constants.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/db.inc.php');

require_once(dirname(__FILE__) . '/../html/includes/utils-mibs.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/components/nxti/includes/utils-traps.inc.php');

db_connect(DB_NAGIOSXI);

$current_mib = mib_name_from_path($argv[1]);

$no_insert = false;
if (isset($argv[2]) && $argv[2] === "--no-insert") {
    $no_insert = true;
}

main();
exit();

function main() {
    global $argv;

    $filename = $argv[1];
    $contents = file($filename); // dont need to write

    if ($contents === false) {
        return;
    }

    $current_defn = array();

    $skip_until = -1;

    foreach ($contents as $ind => $line) {
        if ($ind <= $skip_until) {
            continue; // between SDESC and EDESC
        }

        if (!$line) {
            continue;
        }

        $line = trim($line);

        switch ($line[0]) {
            case 'E':
                if ($line[1] === 'V') { // EVENT
                    db_insert($current_defn);
                    $current_defn = array(
                        'trapdata_desc' => '',
                        'trapdata_exec' => array(),
                        'trapdata_raw_data' => '',
                        'integration_enabled' => "0"
                    );

                    parse_event($line, $current_defn);
                }
                else if ($line[1] === 'X') { // EXEC
                    parse_exec($line, $current_defn);
                }
                break;

            case 'F': // FORMAT
                parse_custom_format($line, $current_defn);
                break;

            case 'P': // PREEXEC
            case 'N': // NODES
            case 'M': // MATCH or MIB
                if ($line[1] === 'I')  { // MIB 
                    parse_mib($line);
                    break;
                }
            case 'R': // REGEX
                parse_raw_data($line, $current_defn);
                break;

            case 'S': // SDESC
                $skip_until = parse_description($contents, $ind, $current_defn);
                break;

            default:
                // Probably a pound comment line.
                break;
        }
    
    }
    db_insert($current_defn);

    $unused = '';
}

// MIB: DISMAN-EVENT-MIB (file:/usr/share/snmp/mibs/DISMAN-EVENT-MIB.txt) converted on Thu Jan 10 13:50:12 2019 using snmpttconvertmib v1.3
function parse_mib($line) {
    $line = explode(' ', $line);
    $len = count($line);

    if ($line[0] !== "MIB:" || $len < 3) {
        return '';
    }

    global $current_mib;
    $current_mib = $line[2];
    $current_mib = trim(strrchr($current_mib, '/'), '/()');
    $current_mib = substr($current_mib, 0, strrpos($current_mib, '.'));
}

// EVENT name oid "c a t e g o r y" severity
function parse_event($line, &$defn) {
    $line = explode(' ', $line);
    $len = count($line);
    if ($len < 5)
        return;

    $defn['trapdata_event_name'] = $line[1];
    $defn['trapdata_event_oid'] = $line[2];
    $defn['trapdata_severity'] = $line[$len - 1];

    $tmp = array_slice($line, 3, count($line) - 4);
    $tmp = implode(" ", $tmp);
    $defn['trapdata_category'] = trim($tmp, '"');
}

function parse_exec($line, &$defn) {
    $line = explode(" ", $line);

    if ($line[1] === "/usr/local/bin/snmptraphandling.py") {
        $line = array_slice($line, 2);
        parse_passive_service(implode(" ", $line), $defn);
        return;
    }

    $line = array_slice($line, 1);
    $defn['trapdata_exec'][] = implode(" ", $line);
}

function parse_passive_service($line, &$defn) {
    $line = preg_split('/[\'"] [\'"]/', $line);
    if (count($line) < 6) {
        return;
    }
    $defn['integration_enabled'] = "1";

    $defn['integration_data'] = array(
        'host' => trim($line[0], '\'"'),
        'service' => $line[1],
        'severity' => $line[2],
        'time' => trim($line[3], '\'"'),
        'perfdata' => trim($line[4], '\'"'),
        'output' => trim($line[5], '\'"')
    );
}

function parse_custom_format($line, &$defn) {
    $line = explode(" ", $line);
    $line = array_slice($line, 1);
    $defn['trapdata_custom_format'] = implode(" ", $line);

}

function parse_description($contents, $index, &$defn) {
    $defn['trapdata_desc'] = '';
    if (substr(trim($contents[$index]), 0, 5) === "SDESC") {
        foreach ($contents as $test_index => $line) {

            if ($test_index <= $index) {
                continue;
            }

            if (substr(trim($line), 0, 5) === "EDESC") {
                return $test_index;
            }
            else {
                $defn['trapdata_desc'] .= $line;
            }
        }
    }
}

function parse_raw_data($line, &$defn) {
    if (isset($defn['trapdata_raw_data'])) {
        $defn['trapdata_raw_data'] .= $line;
    }
    else {
        $defn['trapdata_raw_data'] = $line;
    }
}

function db_insert($trap_definition) {
    global $current_mib;
    global $no_insert;

    if ($trap_definition === array() || !isset($trap_definition['trapdata_event_name'])) {
        return FALSE;
    }
    //print_r($trap_definition);
    echo $trap_definition['trapdata_event_name'] . "\n";
    if ($no_insert) {
        return;
    }
    $existing = get_by_event_name_trapdata($trap_definition['trapdata_event_name']);
    if (is_array($existing) && isset($existing[0])) {
        // Update the trap definition rather than adding it
        $existing = $existing[0];

        // Assume that if other columns differ, the user prefers the one in our database.
        $affected_columns = array('trapdata_event_name',
                                  'trapdata_event_oid',
                                  'trapdata_category',
                                  'trapdata_severity',
                                  'trapdata_raw_data');
        foreach($affected_columns as $column) {
            if (!isset($trap_definition[$column])) {
                $trap_definition[$column] = $existing[$column];
            }
        }
        $result = update_trapdata_full($trap_definition['trapdata_event_name'],
                                      $trap_definition['trapdata_event_oid'],
                                      $trap_definition['trapdata_category'],
                                      $trap_definition['trapdata_severity'],
                                      $trap_definition['trapdata_raw_data'],
                                      $current_mib);
    }
    else {
        // New trap
        $result = add_trapdata($trap_definition['trapdata_event_name'], 
                               $trap_definition['trapdata_event_oid'], 
                               $trap_definition['trapdata_category'], 
                               $trap_definition['trapdata_severity'],
                               $trap_definition['trapdata_custom_format'],
                               $trap_definition['trapdata_raw_data'], 
                               $trap_definition['trapdata_exec'],
                               $trap_definition['trapdata_desc'],
                               $trap_definition['integration_enabled'],
                               $trap_definition['integration_data'],
                               $current_mib);
    }

}

