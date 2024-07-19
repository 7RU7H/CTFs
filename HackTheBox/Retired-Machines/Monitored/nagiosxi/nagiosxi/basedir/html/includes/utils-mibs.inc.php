<?php
//
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//


///////////////////////////////////////////////////////////////////////////////////////////
// MIB FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


/**
 * @return string
 */
function get_mib_dir()
{
    $mib_dir = "/usr/share/snmp/mibs";
    if (!is_dir($mib_dir)) {
        $mib_dir = "/usr/share/mibs";
    }
    return $mib_dir;
}

function is_nxti_used() {
    $ret = enterprise_features_enabled();
    $ret = get_option('is_nxti_used', $ret);
    return intval($ret);
}


function get_db_mibs()
{
    $mibs = get_db_mibs_raw();
    $N_A = _("N/A");

    $is_nxti_used = is_nxti_used();

    foreach ($mibs as &$mib) {

        $mib['wrong_processing'] = 0;

        foreach ($mib as $index => $entry) {

            if ($entry === NULL) {
                $mib[$index] = $N_A;
            }
        }

        switch ($mib['mib_type']) {
            case 'upload':
                $mib['mib_type'] = MIB_UPLOAD_DO_NOTHING;
                break;
            case 'process_manual':
                $mib['mib_type'] = MIB_UPLOAD_PROCESS_ONLY;
                break;
            case 'process_nxti':
                $mib['mib_type'] = MIB_UPLOAD_NXTI;
                break;
        }
    }

    return $mibs;
}

function mib_id_from_name($mib_name) {
    $mib_id = NULL;
    if (!empty($mib_name)) {
        $sql = sprintf("SELECT mib_id from " . $db_tables[DB_NAGIOSXI]['mibs'] . "WHERE mib_name = '%s'", escape_sql_param($mib_name, DB_NAGIOSXI));
        $res = exec_sql_query(DB_NAGIOSXI, $sql);
        $id = $res->GetRows();
        if (!empty($id)) {
            $mib_id = intval($id[0]['mib_id']);
        }
    }
    return $mib_id;
}

function get_db_mibs_raw() {

    global $db_tables;
    $sql = "SELECT mib_id, 
                   mib_name, 
                   mib_uploaded, 
                   mib_last_processed, 
                   mib_type, 
                   (SELECT COUNT(*) FROM " . $db_tables[DB_NAGIOSXI]['cmp_trapdata'] . " WHERE trapdata_parent_mib_name = mib_name) as mib_count_assoc_traps 
            FROM ". $db_tables[DB_NAGIOSXI]['mibs'];

    $rows = exec_sql_query(DB_NAGIOSXI, $sql);

    if ($rows) {
        $rows = $rows->GetRows();
    }

    usort($rows, function($first, $second) {
        return strcmp(strtolower($first['mib_name']), strtolower($second['mib_name']));
    });

    return $rows;
}

function mib_name_from_path($filepath) {

    // Remove directory structure
    $filepath = basename($filepath);

    // Remove file extension
    $last_dot_index = strrpos($filepath, '.');
    if ($last_dot_index !== false) { 
        $filepath = substr($filepath, 0, $last_dot_index);
    }

    return $filepath;

}

function mibs_update_db($mib_name, $mib_type = 'upload', $upload_date = false) {

    global $db_tables;
    $mib_name = escape_sql_param($mib_name, DB_NAGIOSXI);
    $mib_type = escape_sql_param($mib_type, DB_NAGIOSXI);
    $mib_type_clause = "mib_type = '$mib_type'";

    $mib_name_clause = "mib_name = '$mib_name'";

    $upload_date_clause = '';
    if ($upload_date) {
        $upload_date_clause = ', mib_uploaded = ' . escape_sql_param($upload_date, DB_NAGIOSXI);
    }

    $process_date_clause = ', mib_last_processed = NOW()';

    $sql = "UPDATE " .$db_tables[DB_NAGIOSXI]['mibs']. " SET $mib_type_clause $upload_date_clause $process_date_clause WHERE $mib_name_clause";

    global $DB;
    $result = exec_sql_query(DB_NAGIOSXI, $sql);

    if (intval($DB[DB_NAGIOSXI]->affected_rows() === 0)) {
        /* INSERT INTO...SET only works for mysql, not for postgres. */
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]['mibs'] . " (mib_type, mib_name, mib_last_processed, mib_uploaded) VALUES ('$mib_type', '$mib_name', NOW(), NOW())";

        $result = exec_sql_query(DB_NAGIOSXI, $sql);
    }

}

function mibs_add_entry($filename) {

    global $db_tables;
    global $DB;
    $mib_name = mib_name_from_path($filename);
    $mib_name = escape_sql_param($mib_name, DB_NAGIOSXI);

    $sql = "SELECT * FROM  " .$db_tables[DB_NAGIOSXI]['mibs']. " WHERE mib_name = '$mib_name'";
    $result = exec_sql_query(DB_NAGIOSXI, $sql);

    $rows = $result->GetRows();

    if (count($rows) == 0) {

        $sql = "INSERT INTO " .$db_tables[DB_NAGIOSXI]['mibs']. " (mib_name, mib_uploaded, mib_type) VALUES ('$mib_name', NOW(), 'upload');";

        $result = exec_sql_query(DB_NAGIOSXI, $sql);
        return $result;
    }
}

/**
 * I didn't see any references to this function
 * outside of the Manage MIBs page, but this keeps the header and behavior
 * just in case.
 */
function get_mibs() {
    $fs_mibs = get_fs_mibs();
    $db_mibs = get_db_mibs();
    return mibs_array_merge($fs_mibs, $db_mibs);
}

function mibs_missing_file_string() {
    return _("Missing File");
}

function mibs_missing_db_string() {
    return _("Unknown (Missing Database Entry)");
}


function mibs_remove_file($file) {

    // That's empty string, not empty contents!
    if (empty($file)) {
        return true;
    }

    // clean the filename
    $file = str_replace("..", "", $file);
    $file = str_replace("/", "", $file);
    $file = str_replace("\\", "", $file);

    $dir = get_mib_dir();
    $thefile = $dir . "/" . $file;

    return unlink($thefile);
}

function mibs_remove_db_entry($mib_name) {

    if (empty($mib_name)) {
        return true;
    }

    global $db_tables;

    $mib_name = escape_sql_param($mib_name, DB_NAGIOSXI);
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["mibs"] . " WHERE mib_name = '". $mib_name . "'";
    exec_sql_query(DB_NAGIOSXI, $sql);
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]['cmp_trapdata'] . " WHERE trapdata_parent_mib_name = '$mib_name'";
    exec_sql_query(DB_NAGIOSXI, $sql);

    return true;
}

function mibs_revert_db_entry($mib_name) {

    if (empty($mib_name)) {
        return true;
    }

    global $db_tables;

    $mib_name = escape_sql_param($mib_name, DB_NAGIOSXI);
    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["mibs"] . " SET mib_type = 'upload', mib_last_processed = NULL WHERE mib_name = '". $mib_name . "'";
    exec_sql_query(DB_NAGIOSXI, $sql);
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]['cmp_trapdata'] . " WHERE trapdata_parent_mib_name = '$mib_name'";
    exec_sql_query(DB_NAGIOSXI, $sql);

    return NULL;

}


// This just does array_merge_recursive, except the actual indices are numeric,
// and we treat the mib name as though it's the 'index' in the original algorithm.
// We assumed that both arrays are sorted alphabetically with respect to the MIB name.
function mibs_array_merge($fs_mibs, $db_mibs) {

    $ret = array();
    $fs_index = 0;
    $db_index = 0;

    $i = 0;
    for ($i = 0; $i < count($fs_mibs) + count($db_mibs); $i++) {

        $res = 0;
        $can_cmp = true;

        // Check boundary conditions. strcmp isn't safe to use if we've run off the end of the array
        if ($fs_index >= count($fs_mibs)) {
            $res += 1;
            $can_cmp = false;
        }
        if ($db_index >= count($db_mibs)) {
            $res -= 1;
            $can_cmp = false;
        }

        if ($can_cmp !== false) {
            $res = strcmp($fs_mibs[$fs_index]['mibname'], $db_mibs[$db_index]['mib_name']);
        }

        if ($res === 0) {
            // both indices referring to the same item. Just merge and continue.
            if (!$can_cmp) {
                // Index is out of bounds for each array. We're finished!
                break;
            }
            $ret[] = array_merge($fs_mibs[$fs_index], $db_mibs[$db_index]);

            $fs_index++;
            $db_index++;
        }
        else if ($res < 0) {
            // fs_mibs comes earlier in dictionary. Missing db_mibs for this entry
            $false_db_mib = array(
                'mib_id' => _('N/A'),
                'mib_name' => $fs_mibs[$fs_index]['mibname'],
                'mib_uploaded' => mibs_missing_db_string(),
                'mib_last_processed' => _('N/A'),
                'mib_type' => MIB_TYPE_UNKNOWN,
                'mib_count_assoc_traps' => _('N/A')
            );

            $ret[] = array_merge($fs_mibs[$fs_index], $false_db_mib);

            $fs_index++;
        }
        else {
            // db_mibs comes earlier in dictionary. Missing fs_mibs for this entry
            $false_fs_mib = array(
                'mibname' => $db_mibs[$db_index]['mib_name'],
                'file' => mibs_missing_file_string(),
                'timestamp' => 0,
                'date' => _("N/A"),
                'perms' => _('N/A'),
                'permstring' => _('N/A'),
                'owner' => _('N/A'),
                'group' => _('N/A')
            );

            $ret[] = array_merge($db_mibs[$db_index], $false_fs_mib);

            $db_index++;

        }
    }

    return $ret;
}

function mibs_get_associated_traps($mibs) {

    global $db_tables;

    if (!is_array($mibs)) {
        $mibs = array($mibs);
    }

    $traps = array();
    $x = 0;

    foreach ($mibs as $mib_name) {

        if (is_array($mib_name)) {
            $mib_name = $mib_name['mib_name'];
        }

        $sql = 'SELECT trapdata_event_name as event_name, trapdata_event_oid as oid, trapdata_category as category, trapdata_severity as severity from ' . $db_tables[DB_NAGIOSXI]["cmp_trapdata"] . " WHERE trapdata_parent_mib_name = ".escape_sql_param($mib_name, DB_NAGIOSXI, true);
 
        $result = exec_sql_query(DB_NAGIOSXI, $sql);

        if (is_object($result) && $result->numrows() > 0) {
            $traps[$mib_name] = $result->GetRows();
        }

    }

    return $traps;
}

/**
 * @return array
 */
function get_fs_mibs()
{

    $mibs = array();

    $dir = get_mib_dir();

    $p = $dir;
    $direntries = file_list($p, "");
    foreach ($direntries as $de) {

        $file = $de;
        $filepath = $dir . "/" . $file;
        $ts = filemtime($filepath);

        $perms = fileperms($filepath);
        $perm_string = file_perms_to_string($perms);

        $ownerarr = fileowner($filepath);
        if (function_exists('posix_getpwuid')) {
            $ownerarr = posix_getpwuid($ownerarr);
            $owner = $ownerarr["name"];
        }
        else {
            $owner = $ownerarr;
        }
        $grouparr = filegroup($filepath);
        if (function_exists('posix_getgrgid')) {
            $grouparr = posix_getgrgid($grouparr);
            $group = $grouparr["name"];
        }
        else {
            $group = $grouparr;
        }

        $info = pathinfo($file);
        $mib_name = basename($file, '.' . grab_array_var($info, "extension", ""));

        if ($mib_name == "" || $file == ".index")
            continue;

        $mibs[] = array(
            "mibname" => $mib_name,
            "file" => $file,
            "timestamp" => $ts,
            "date" => get_datetime_string($ts),
            "perms" => $perms,
            "permstring" => $perm_string,
            "owner" => $owner,
            "group" => $group,
        );
    }

    usort($mibs, function($first, $second) {
        return strcmp(strtolower($first['mibname']), strtolower($second['mibname']));
    });

    return $mibs;
}
