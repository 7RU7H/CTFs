<?php
define('RECURRINGDOWNTIME_CFG', '/usr/local/nagios/etc/recurringdowntime.cfg');


// Register callback for updating configs if we change the name
register_callback(CALLBACK_SUBSYS_GENERIC, 'recurringdowntime_apply_pending_changes');
register_callback(CALLBACK_SUBSYS_GENERIC, 'recurringdowntime_cancel_pending_changes');
register_callback(CALLBACK_CCM_MODIFY_HOSTSERVICE, 'recurringdowntime_ccm_hostservice_sync');
register_callback(CALLBACK_CCM_MODIFY_GROUP, 'recurringdowntime_ccm_group_sync');


/**
 * Sync the changes that were pending for the recurring downtime
 * configuration after apply config is complete
 * - Runs after apply/reconfigure subsys command has been completed
 */
function recurringdowntime_apply_pending_changes($cbtype, &$cbargs)
{
    // Verify this is an apply config and it finished successfully
    if ($cbargs['command'] != COMMAND_NAGIOSCORE_APPLYCONFIG || $cbargs['return_code'] != 0) {
        return;
    }

    // Verify there are pending changes
    $pending = recurringdowntime_get_pending_changes();
    if (empty($pending)) {
        return;
    }

    // Apply the actual changes to the cfg
    $cfg = recurringdowntime_get_cfg();
    foreach ($pending as $p) {
        if (array_key_exists('host_name', $p)) {
            $cfg[$p['cfg_id']]['host_name'] = $p['host_name'];
        } else if (array_key_exists('service_description', $p)) {
            $cfg[$p['cfg_id']]['service_description'] = $p['service_description'];
        } else if (array_key_exists('hostgroup_name', $p)) {
            $cfg[$p['cfg_id']]['hostgroup_name'] = $p['hostgroup_name'];
        } else if (array_key_exists('servicegroup_name', $p)) {
            $cfg[$p['cfg_id']]['servicegroup_name'] = $p['servicegroup_name'];
        }
    }

    recurringdowntime_write_cfg($cfg);
    recurringdowntime_update_pending_changes(array());
}


function recurringdowntime_cancel_pending_changes($cbtype, &$cbargs)
{
    // Verify this is a CCM restore and it finished successfully
    if ($cbargs['command'] != COMMAND_RESTORE_NAGIOSQL_SNAPSHOT || $cbargs['return_code'] != 0) {
        return;
    }

    recurringdowntime_update_pending_changes(array());
}


/**
 * Sync the modified host/service objects
 * - Runs after CCM host or service is saved
 * - Runs after Bulk Renaming Tool is saved
 */
function recurringdowntime_ccm_hostservice_sync($cbtype, &$cbargs)
{
    $cfg = recurringdowntime_get_cfg();
    $pending = recurringdowntime_get_pending_changes();

    // Check if the object is part of the pending changes
    foreach ($pending as $i => $p) {
        if ($p['object_id'] == $cbargs['id']) {
            $tmp = $p;
            if ($cbargs['type'] == 'host') {
                $tmp['host_name'] = $cbargs['host_name'];
            } else if ($cbargs['type'] == 'service') {
                $tmp['service_description'] = $cbargs['service_description'];
            }
            $pending[$i] = $tmp;
            recurringdowntime_update_pending_changes($pending);
            return;
        }
    }

    // Check if host name changed
    if ($cbargs['type'] == 'host') {

        // Check if host name changed
        if (!empty($cbargs['old_host_name']) && $cbargs['host_name'] != $cbargs['old_host_name']) {
            foreach ($cfg as $id => $c) {

                if ($c['schedule_type'] == 'host' || $c['schedule_type'] == 'service') {

                    // Replace config host_name that used the old host name
                    if ($c['host_name'] == $cbargs['old_host_name']) {
                        $pending[] = array(
                            'cfg_id' => $id,
                            'object_id' => $cbargs['id'],
                            'host_name' => $cbargs['host_name']
                        );
                    }

                }

            }
        }


    // Check for service description change
    } else if ($cbargs['type'] == 'service') {

        // This one is complicated ... we will only do the hosts defined directly to the service
        if (!empty($cbargs['old_service_description']) && $cbargs['service_description'] != $cbargs['old_service_description']) {
            foreach ($cfg as $id => $c) {
                if (array_key_exists('service_description', $c) && $c['service_description'] == $cbargs['old_service_description']) {

                    // Get all hosts attached to service
                    $sql = "SELECT host_name FROM nagiosql.tbl_lnkServiceToHost AS lnk
                            LEFT JOIN nagiosql.tbl_host AS h ON h.id = lnk.idSlave
                            WHERE idMaster = ".intval($cbargs['id']).";";
                    if (!($rs = exec_sql_query(DB_NAGIOSQL, $sql))) {
                        continue;
                    }

                    // Check all hosts against current cfg
                    $arr = $rs->GetArray();
                    foreach ($arr as $a) {
                        if ($c['host_name'] == $a['host_name']) {
                            $pending[] = array(
                                'cfg_id' => $id,
                                'object_id' => $cbargs['id'],
                                'service_description' => $cbargs['service_description']
                            );
                        }
                    }

                }
            }
        }

    }

    // Save pending changes
    recurringdowntime_update_pending_changes($pending);
}


/**
 * Sync the modified host/service group objects
 * - Runs after CCM hostgroup or servicegroup is saved
 */
function recurringdowntime_ccm_group_sync($cbtype, &$cbargs)
{
    $cfg = recurringdowntime_get_cfg();
    $pending = recurringdowntime_get_pending_changes();

    // Check if the object is part of the pending changes
    foreach ($pending as $i => $p) {
        if ($p['object_id'] == $cbargs['id']) {
            $tmp = $p;
            if ($cbargs['type'] == 'hostgroup') {
                $tmp['hostgroup_name'] = $cbargs['hostgroup_name'];
            } else if ($cbargs['type'] == 'servicegroup') {
                $tmp['servicegroup_name'] = $cbargs['servicegroup_name'];
            }
            $pending[$i] = $tmp;
            recurringdowntime_update_pending_changes($pending);
            return;
        }
    }

    if ($cbargs['type'] == 'hostgroup') {

        if ($cbargs['old_hostgroup_name'] != $cbargs['hostgroup_name']) {
            foreach ($cfg as $id => $c) {
                if (array_key_exists('hostgroup_name', $c) && $c['hostgroup_name'] == $cbargs['old_hostgroup_name']) {
                    $pending[] = array(
                        'cfg_id' => $id,
                        'object_id' => $cbargs['id'],
                        'hostgroup_name' => $cbargs['hostgroup_name']
                    );
                }
            }
        }

    } else if ($cbargs['type'] == 'servicegroup') {

        if ($cbargs['old_servicegroup_name'] != $cbargs['servicegroup_name']) {
            foreach ($cfg as $id => $c) {
                if (array_key_exists('servicegroup_name', $c) && $c['servicegroup_name'] == $cbargs['old_servicegroup_name']) {
                    $pending[] = array(
                        'cfg_id' => $id,
                        'object_id' => $cbargs['id'],
                        'servicegroup_name' => $cbargs['servicegroup_name']
                    );
                }
            }
        }

    }

    // Save pending changes
    recurringdowntime_update_pending_changes($pending);
}


/**
 * Get all the currently pending changes
 */
function recurringdowntime_get_pending_changes()
{
    $obj = get_option('recurringdowntime_pending_changes', array());
    if (!is_array($obj)) {
        $obj = json_decode(base64_decode($obj), true);
    }
    return $obj;
}


/**
 * Save the pending changes for recurringdowntime
 *
 * @param   array   $arr    Array of pending changes
 */
function recurringdowntime_update_pending_changes($arr)
{
    if (empty($arr)) {
        $arr = array();
    }

    // Save the array into an option
    $encoded = base64_encode(json_encode($arr));
    set_option('recurringdowntime_pending_changes', $encoded);
}


/**
 * Convert the string form of the recurring downtime config to a PHP array
 * (Possibly convert this into a regex? Pull out whatever is between {x} instead)
 *
 * @param $str_cfg
 * @return array
 */
function recurringdowntime_cfg_to_array($str_cfg)
{
    $schedules = preg_split('/define schedule \{([\s]+)/', $str_cfg);
    $ret = array();
    if (count($schedules) > 0) {
        foreach ($schedules as $k => $schedule) {
            $lines = explode("\n", $schedule);
            $schedule_arr = array();
            foreach ($lines as $k2 => $line) {
                if ($line == "" || $line == "}") {
                    continue;
                }
                $line_arr = array();
                foreach (preg_split('/\s+/', trim($line), 2) as $k3 => $val) {
                    if ($val != "") {
                        $line_arr[] = trim($val);
                    }
                }
                if ($line_arr[0] == "sid") {
                    $sid = isset($line_arr[1]) ? $line_arr[1] : "";
                } else {
                    $val = isset($line_arr[1]) ? $line_arr[1] : "";
                    $schedule_arr[$line_arr[0]] = $val;
                }
            }
            if (count($schedule_arr) > 0) {
                $ret[$sid] = $schedule_arr;
            }
        }
    }
    return $ret;
}


/**
 * Convert a PHP array containing recurring downtime config to a string appropriate for
 * downtime's schedule.cfg
 *
 * @param $arr
 * @return string
 */
function recurringdowntime_array_to_cfg($arr)
{
    if (count($arr) == 0) {
        return "";
    }
    $cfg_str = "";
    foreach ($arr as $sid => $schedule) {
        if (count($schedule) == 0) {
            continue;
        }
        $cfg_str .= "define schedule {\n";
        $cfg_str .= "\tsid\t\t\t$sid\n";
        foreach ($schedule as $var => $val) {

            // get a sane tab count for proper viewing/troubleshooting
            $tabs = "\t\t";
            if ($var == 'servicegroup_name' || $var == 'service_description')
                $tabs = "\t";
            if ($var == 'user' || $var == 'comment' || $var == 'time' || $var == 'svcalso')
                $tabs .= "\t";

            $cfg_str .= "\t$var{$tabs}$val\n";
        }
        $cfg_str .= "}\n\n";
    }
    return $cfg_str;
}


/**
 * Write the configuration to disk
 *
 * @param $cfg
 * @return bool
 */
function recurringdowntime_write_cfg($cfg)
{
    if (is_array($cfg)) {
        $cfg_str = recurringdowntime_array_to_cfg($cfg);
    } else {
        $cfg_str = $cfg;
    }
    recurringdowntime_check_cfg();
    file_put_contents(RECURRINGDOWNTIME_CFG, $cfg_str);
    return true;
}


/*
 * Make sure cfg file exists, and if not, create it.
 */
function recurringdowntime_check_cfg()
{
    if (!file_exists(RECURRINGDOWNTIME_CFG)) {
        $fh = @fopen(RECURRINGDOWNTIME_CFG, "w+");
        fclose($fh);
    }
}


/**
 * Get config from cfg file
 *
 * @return array
 */
function recurringdowntime_get_cfg()
{
    recurringdowntime_check_cfg();
    $cfg = file_get_contents(RECURRINGDOWNTIME_CFG);
    return recurringdowntime_cfg_to_array($cfg);
}


/**
 * Get downtime schedule for specified host
 *
 * @param bool $host
 * @return array
 */
function recurringdowntime_get_host_cfg($host = false)
{
    $cfg = recurringdowntime_get_cfg();
    $ret = array();
    foreach ($cfg as $sid => $schedule) {
        if (array_key_exists('schedule_type', $schedule)) {
            if ($schedule["schedule_type"] == "hostgroup") {
                continue;
            }
            if ($schedule["schedule_type"] == "servicegroup") {
                continue;
            }
            if ($schedule["schedule_type"] == "service") {
                continue;
            }
        }
        if ($host && !(strtolower($schedule["host_name"]) == strtolower($host))) {
            continue;
        }
        if (array_key_exists('host_name', $schedule)) {
            if (is_authorized_for_host(0, $schedule["host_name"])) {
                $ret[$sid] = $schedule;
            }
        }
    }
    return $ret;
}


/**
 * Get downtime schedule for specified host
 *
 * @param bool $host
 * @return array
 */
function recurringdowntime_get_service_cfg($host = false)
{
    $cfg = recurringdowntime_get_cfg();
    $ret = array();
    foreach ($cfg as $sid => $schedule) {
        if (array_key_exists('schedule_type', $schedule)) {
            if ($schedule["schedule_type"] == "hostgroup") {
                continue;
            }
            if ($schedule["schedule_type"] == "servicegroup") {
                continue;
            }
            if ($schedule["schedule_type"] == "host") {
                continue;
            }
        }

        if ($host && !(strtolower($schedule["host_name"]) == strtolower($host))) {
            continue;
        }

        if (array_key_exists('host_name', $schedule)) {
            if (array_key_exists('service_description', $schedule)) {
                if ($schedule["service_description"] != '*') {
                    $search_str = $schedule["service_description"];

                    if (strstr($schedule["service_description"], "*")) {
                        $search_str = "lk:" . str_replace("*", "%", $schedule["service_description"]);
                    }
                    if (!is_authorized_for_service(0, $schedule["host_name"], $search_str)) {
                        continue;
                    }
                }
            }

            if (is_authorized_for_host(0, $schedule["host_name"])) {
                $ret[$sid] = $schedule;
            }
        }
    }
    return $ret;
}


/**
 * Get downtime schedule for specified hostgroup
 *
 * @param bool $hostgroup
 * @return array
 */
function recurringdowntime_get_hostgroup_cfg($hostgroup = false)
{
    $cfg = recurringdowntime_get_cfg();
    $ret = array();
    foreach ($cfg as $sid => $schedule) {
        if (array_key_exists('schedule_type', $schedule)) {
            if ($schedule["schedule_type"] != "hostgroup") {
                continue;
            }
        }
        if ($hostgroup && !(strtolower($schedule["hostgroup_name"]) == strtolower($hostgroup))) {
            continue;
        }
        if (array_key_exists('hostgroup_name', $schedule)) {
            if (is_authorized_for_hostgroup(0, $schedule["hostgroup_name"])) {
                $ret[$sid] = $schedule;
            }
        }
    }
    return $ret;
}


/**
 * Get downtime schedule for specified servicegroup
 *
 * @param bool $servicegroup
 * @return array
 */
function recurringdowntime_get_servicegroup_cfg($servicegroup = false)
{
    $cfg = recurringdowntime_get_cfg();
    $ret = array();
    foreach ($cfg as $sid => $schedule) {
        if (array_key_exists('schedule_type', $schedule)) {
            if ($schedule["schedule_type"] != "servicegroup") {
                continue;
            }
        }
        if ($servicegroup && !(strtolower($schedule["servicegroup_name"]) == strtolower($servicegroup))) {
            continue;
        }
        if (array_key_exists('servicegroup_name', $schedule)) {
            if (is_authorized_for_servicegroup(0, $schedule["servicegroup_name"])) {
                $ret[$sid] = $schedule;
            }
        }
    }
    return $ret;
}
