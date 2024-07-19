<?php
//
// Report Utilities
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// MENU
////////////////////////////////////////////////////////////////////////////////


function get_myreports_menu_html($userid = 0, $menufilter = 0)
{
    // Get all user reports
    $myreports = get_myreports($userid);

    $mi = array();
    foreach ($myreports as $id => $r) {
        if (empty($r['dontdisplay'])) {
            $mi[] = array(
                "type" => "link",
                "title" => encode_form_val($r["title"]),
                "order" => (100 + $x),
                "opts" => array(
                    "href" => "myreports.php?go=1&id=" . $id,
                    "id" => "myreports-" . $id,
                )
            );
        }
    }

    $html = get_menu_items_html($mi);
    return $html;
}


function get_myreports_count($userid = 0)
{
    $myreports = get_myreports($userid);
    return count($myreports);
}


////////////////////////////////////////////////////////////////////////////////
// XML DATA
////////////////////////////////////////////////////////////////////////////////


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_logentries($args)
{
    $x = simplexml_load_string(get_logentries_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_statehistory($args)
{
    $x = simplexml_load_string(get_statehistory_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_historicalhoststatus($args)
{
    $x = simplexml_load_string(get_historical_host_status_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_historicalservicestatus($args)
{
    $x = simplexml_load_string(get_historical_service_status_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_topalertproducers($args)
{
    $x = simplexml_load_string(get_topalertproducers_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_histogram($args)
{
    $x = simplexml_load_string(get_histogram_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_notificationswithcontacts($args)
{
    $x = simplexml_load_string(get_notificationswithcontacts_xml_output($args));
    return $x;
}


/**
 * @param $args
 * @return SimpleXMLElement
 */
function get_xml_notifications($args)
{
    $x = simplexml_load_string(get_notificationswithcontacts_xml_output($args));
    return $x;
}


/**
 * @param   string  $type   Type of object (host or service)
 * @param   array   $args   Array of arguments to pass
 * @return  object          A SimpleXMLElement object
 */
function get_xml_availability($type = "host", $args)
{
    $x = simplexml_load_string(get_parsed_nagioscore_csv_availability_xml_output($args, false, $type));
    return $x;
}


////////////////////////////////////////////////////////////////////////////////
// AVAILABILITY FUNCTIONS
////////////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $type
 * @param   array   $args
 * @return  string
 */
function get_parsed_nagioscore_csv_availability_xml_output($args, $raw = false, $type = 'host')
{
    $havedata = false;
    $hostdata = array();
    $servicedata = array();

    $host = grab_array_var($args, "host", "");
    $hostgroup = grab_array_var($args, "hostgroup", "");
    $servicegroup = grab_array_var($args, "servicegroup", "");
    $service = grab_array_var($args, "service", "");

    // Hostgroup members
    $hostgroup_members = array();
    if ($hostgroup != "") {
        $hargs = array(
            "hostgroup_name" => $hostgroup,
        );
        $xml = get_xml_hostgroup_member_objects($hargs);
        if ($xml) {
            foreach ($xml->hostgroup->members->host as $hgm) {
                $hostgroup_members[] = strval($hgm->host_name);
            }
        }
    }

    // Servicegroup members
    $servicegroup_members = array();
    if ($servicegroup != "") {
        $sargs = array(
            "servicegroup_name" => $servicegroup,
        );
        $xml = get_xml_servicegroup_member_objects($sargs);
        if ($xml) {
            foreach ($xml->servicegroup->members->service as $sgm) {
                $sgmh = strval($sgm->host_name);
                $sgms = strval($sgm->service_description);
                $servicegroup_members[] = array($sgmh, $sgms);
            }
        }
    }

    // Get the data
    $d = get_raw_nagioscore_csv_availability($args);

    // Explode by lines
    $lines = explode("\n", $d);
    $x = 0;
    foreach ($lines as $line) {
        $x++;

        // Make sure we have expected data in first line, otherwise bail
        if ($x == 1) {
            $pos = strpos($line, "HOST_NAME,");
            // Doesn't look like proper CSV output - Nagios Core may not be running
            if ($pos === FALSE) {
                $havedata = false;
                break;
            } else {
                $havedata = true;
            }
        } else {

            // Skip the line that would be the service header line
            if (strpos($line, "HOST_NAME,") !== false) {
                continue;
            }

            $cols = explode(",", $line);

            // Trim whitespace from data
            foreach ($cols as $i => $c) {
                trim($cols[$i]);
            }

            $parts = count($cols);

            // Make sure we have good data
            if ($parts == 34) {

                $hn = str_replace("\"", "", $cols[0]);
                $hn = trim($hn);

                // Filter by host name
                if ($host != "" && ($host != $hn)) {
                    continue;
                }

                // Filter by hostgroup
                if ($hostgroup != "" && (!in_array($hn, $hostgroup_members))) {
                    continue;
                }

                // Filter by servicegroup
                if ($servicegroup != "" && !is_host_member_of_servicegroup($hn, $servicegroup)) {
                    continue;
                }

                // Make sure user is authorized
                if (!is_admin() && !is_authorized_for_all_objects(0) && is_authorized_for_host(0, $hn) == false) {
                    continue;
                }

                $hostdata[] = array(

                    "host_name" => $hn,

                    "time_up_scheduled" => $cols[1],
                    "percent_time_up_scheduled" => floatval($cols[2]),
                    "percent_known_time_up_scheduled" => floatval($cols[3]),
                    "time_up_unscheduled" => $cols[4],
                    "percent_time_up_unscheduled" => floatval($cols[5]),
                    "percent_known_time_up_unscheduled" => floatval($cols[6]),
                    "total_time_up" => $cols[7],
                    "percent_total_time_up" => floatval($cols[8]),
                    "percent_known_time_up" => floatval($cols[9]),

                    "time_down_scheduled" => $cols[10],
                    "percent_time_down_scheduled" => floatval($cols[11]),
                    "percent_known_time_down_scheduled" => floatval($cols[12]),
                    "time_down_unscheduled" => $cols[13],
                    "percent_time_down_unscheduled" => floatval($cols[14]),
                    "percent_known_time_down_unscheduled" => floatval($cols[15]),
                    "total_time_down" => $cols[16],
                    "percent_total_time_down" => floatval($cols[17]),
                    "percent_known_time_down" => floatval($cols[18]),

                    "time_unreachable_scheduled" => $cols[19],
                    "percent_time_unreachable_scheduled" => floatval($cols[20]),
                    "percent_known_time_unreachable_scheduled" => floatval($cols[21]),
                    "time_unreachable_unscheduled" => $cols[22],
                    "percent_time_unreachable_unscheduled" => floatval($cols[23]),
                    "percent_known_time_unreachable_unscheduled" => floatval($cols[24]),
                    "total_time_unreachable" => $cols[25],
                    "percent_total_time_unreachable" => floatval($cols[26]),
                    "percent_known_time_unreachable" => floatval($cols[27]),

                    "time_undetermined_not_running" => $cols[28],
                    "percent_time_undetermined_not_running" => floatval($cols[29]),
                    "time_undetermined_no_data" => $cols[30],
                    "percent_time_undetermined_no_data" => floatval($cols[31]),
                    "total_time_undetermined" => $cols[32],
                    "percent_total_time_undetermined" => floatval($cols[33]),
                );

//          HOST_NAME, TIME_UP_SCHEDULED, PERCENT_TIME_UP_SCHEDULED, PERCENT_KNOWN_TIME_UP_SCHEDULED, TIME_UP_UNSCHEDULED, PERCENT_TIME_UP_UNSCHEDULED, PERCENT_KNOWN_TIME_UP_UNSCHEDULED, TOTAL_TIME_UP, PERCENT_TOTAL_TIME_UP, PERCENT_KNOWN_TIME_UP, TIME_DOWN_SCHEDULED, PERCENT_TIME_DOWN_SCHEDULED, PERCENT_KNOWN_TIME_DOWN_SCHEDULED, TIME_DOWN_UNSCHEDULED, PERCENT_TIME_DOWN_UNSCHEDULED, PERCENT_KNOWN_TIME_DOWN_UNSCHEDULED, TOTAL_TIME_DOWN, PERCENT_TOTAL_TIME_DOWN, PERCENT_KNOWN_TIME_DOWN, TIME_UNREACHABLE_SCHEDULED, PERCENT_TIME_UNREACHABLE_SCHEDULED, PERCENT_KNOWN_TIME_UNREACHABLE_SCHEDULED, TIME_UNREACHABLE_UNSCHEDULED, PERCENT_TIME_UNREACHABLE_UNSCHEDULED, PERCENT_KNOWN_TIME_UNREACHABLE_UNSCHEDULED, TOTAL_TIME_UNREACHABLE, PERCENT_TOTAL_TIME_UNREACHABLE, PERCENT_KNOWN_TIME_UNREACHABLE, TIME_UNDETERMINED_NOT_RUNNING, PERCENT_TIME_UNDETERMINED_NOT_RUNNING, TIME_UNDETERMINED_NO_DATA, PERCENT_TIME_UNDETERMINED_NO_DATA, TOTAL_TIME_UNDETERMINED, PERCENT_TOTAL_TIME_UNDETERMINED

            // Services
            } else if ($parts == 44) {

                $hn = str_replace("\"", "", $cols[0]);
                $sn = str_replace("\"", "", $cols[1]);
                $hn = trim($hn);
                $sn = trim($sn);

                // Filter by host name
                if ($host != "" && ($host != $hn)) {
                    continue;
                }

                // Filter by hostgroup
                if ($hostgroup != "" && (!in_array($hn, $hostgroup_members))) {
                    continue;
                }

                // Fiiter by service
                if ($service != "" && ($service != $sn)) {
                    continue;
                }

                // Filter by servicegroup
                $sga = array($hn, $sn);
                if ($servicegroup != "" && (!in_array($sga, $servicegroup_members))) {
                    continue;
                }

                // Make sure user is authorized
                if (!is_admin() && !is_authorized_for_all_objects(0) && is_authorized_for_service(0, $hn, $sn) == false) {
                    continue;
                }

                $servicedata[] = array(

                    "host_name" => $hn,
                    "service_description" => $sn,

                    "time_ok_scheduled" => $cols[2],
                    "percent_time_ok_scheduled" => floatval($cols[3]),
                    "percent_known_time_ok_scheduled" => floatval($cols[4]),
                    "time_ok_unscheduled" => $cols[5],
                    "percent_time_ok_unscheduled" => floatval($cols[6]),
                    "percent_known_time_ok_unscheduled" => floatval($cols[7]),
                    "total_time_ok" => $cols[8],
                    "percent_total_time_ok" => floatval($cols[9]),
                    "percent_known_time_ok" => floatval($cols[10]),

                    "time_warning_scheduled" => $cols[11],
                    "percent_time_warning_scheduled" => floatval($cols[12]),
                    "percent_known_time_warning_scheduled" => floatval($cols[13]),
                    "time_warning_unscheduled" => $cols[14],
                    "percent_time_warning_unscheduled" => floatval($cols[15]),
                    "percent_known_time_warning_unscheduled" => floatval($cols[16]),
                    "total_time_warning" => $cols[17],
                    "percent_total_time_warning" => floatval($cols[18]),
                    "percent_known_time_warning" => floatval($cols[19]),

                    "time_unknown_scheduled" => $cols[20],
                    "percent_time_unknown_scheduled" => floatval($cols[21]),
                    "percent_known_time_unknown_scheduled" => floatval($cols[22]),
                    "time_unknown_unscheduled" => $cols[23],
                    "percent_time_unknown_unscheduled" => floatval($cols[24]),
                    "percent_known_time_unknown_unscheduled" => floatval($cols[25]),
                    "total_time_unknown" => $cols[26],
                    "percent_total_time_unknown" => floatval($cols[27]),
                    "percent_known_time_unknown" => floatval($cols[28]),

                    "time_critical_scheduled" => $cols[29],
                    "percent_time_critical_scheduled" => floatval($cols[30]),
                    "percent_known_time_critical_scheduled" => floatval($cols[31]),
                    "time_critical_unscheduled" => $cols[32],
                    "percent_time_critical_unscheduled" => floatval($cols[33]),
                    "percent_known_time_critical_unscheduled" => floatval($cols[34]),
                    "total_time_critical" => $cols[35],
                    "percent_total_time_critical" => floatval($cols[36]),
                    "percent_known_time_critical" => floatval($cols[37]),

                    "time_undetermined_not_running" => $cols[38],
                    "percent_time_undetermined_not_running" => floatval($cols[39]),
                    "time_undetermined_no_data" => $cols[40],
                    "percent_time_undetermined_no_data" => floatval($cols[41]),
                    "total_time_undetermined" => $cols[42],
                    "percent_total_time_undetermined" => floatval($cols[43]),
                );
            }

        }
    }

    if ($raw) {
        return array($hostdata, $servicedata, $havedata);
    }

    $output = "";
    $output .= "<availability>\n";
    $output .= "<havedata>";
    if ($havedata == true) {
        $output .= "1";
    } else {
        $output .= "0";
    }
    $output .= "</havedata>\n";
    $output .= "<" . $type . "availability>\n";
    if ($type == "host") {
        foreach ($hostdata as $hd) {
            $output .= "   <host>\n";

            $output .= "   <host_name>" . xmlentities($hd["host_name"]) . "</host_name>\n";

            $output .= "   <time_up_scheduled>" . xmlentities($hd["time_up_scheduled"]) . "</time_up_scheduled>\n";
            $output .= "   <percent_time_up_scheduled>" . xmlentities($hd["percent_time_up_scheduled"]) . "</percent_time_up_scheduled>\n";
            $output .= "   <percent_known_time_up_scheduled>" . xmlentities($hd["percent_known_time_up_scheduled"]) . "</percent_known_time_up_scheduled>\n";
            $output .= "   <time_up_unscheduled>" . xmlentities($hd["time_up_unscheduled"]) . "</time_up_unscheduled>\n";
            $output .= "   <percent_time_up_unscheduled>" . xmlentities($hd["percent_time_up_unscheduled"]) . "</percent_time_up_unscheduled>\n";
            $output .= "   <percent_known_time_up_unscheduled>" . xmlentities($hd["percent_known_time_up_unscheduled"]) . "</percent_known_time_up_unscheduled>\n";
            $output .= "   <total_time_up>" . xmlentities($hd["total_time_up"]) . "</total_time_up>\n";
            $output .= "   <percent_total_time_up>" . xmlentities($hd["percent_total_time_up"]) . "</percent_total_time_up>\n";
            $output .= "   <percent_known_time_up>" . xmlentities($hd["percent_known_time_up"]) . "</percent_known_time_up>\n";

            $output .= "   <time_down_scheduled>" . xmlentities($hd["time_down_scheduled"]) . "</time_down_scheduled>\n";
            $output .= "   <percent_time_down_scheduled>" . xmlentities($hd["percent_time_down_scheduled"]) . "</percent_time_down_scheduled>\n";
            $output .= "   <percent_known_time_down_scheduled>" . xmlentities($hd["percent_known_time_down_scheduled"]) . "</percent_known_time_down_scheduled>\n";
            $output .= "   <time_down_unscheduled>" . xmlentities($hd["time_down_unscheduled"]) . "</time_down_unscheduled>\n";
            $output .= "   <percent_time_down_unscheduled>" . xmlentities($hd["percent_time_down_unscheduled"]) . "</percent_time_down_unscheduled>\n";
            $output .= "   <percent_known_time_down_unscheduled>" . xmlentities($hd["percent_known_time_down_unscheduled"]) . "</percent_known_time_down_unscheduled>\n";
            $output .= "   <total_time_down>" . xmlentities($hd["total_time_down"]) . "</total_time_down>\n";
            $output .= "   <percent_total_time_down>" . xmlentities($hd["percent_total_time_down"]) . "</percent_total_time_down>\n";
            $output .= "   <percent_known_time_down>" . xmlentities($hd["percent_known_time_down"]) . "</percent_known_time_down>\n";

            $output .= "   <time_unreachable_scheduled>" . xmlentities($hd["time_unreachable_scheduled"]) . "</time_unreachable_scheduled>\n";
            $output .= "   <percent_time_unreachable_scheduled>" . xmlentities($hd["percent_time_unreachable_scheduled"]) . "</percent_time_unreachable_scheduled>\n";
            $output .= "   <percent_known_time_unreachable_scheduled>" . xmlentities($hd["percent_known_time_unreachable_scheduled"]) . "</percent_known_time_unreachable_scheduled>\n";
            $output .= "   <time_unreachable_unscheduled>" . xmlentities($hd["time_unreachable_unscheduled"]) . "</time_unreachable_unscheduled>\n";
            $output .= "   <percent_time_unreachable_unscheduled>" . xmlentities($hd["percent_time_unreachable_unscheduled"]) . "</percent_time_unreachable_unscheduled>\n";
            $output .= "   <percent_known_time_unreachable_unscheduled>" . xmlentities($hd["percent_known_time_unreachable_unscheduled"]) . "</percent_known_time_unreachable_unscheduled>\n";
            $output .= "   <total_time_unreachable>" . xmlentities($hd["total_time_unreachable"]) . "</total_time_unreachable>\n";
            $output .= "   <percent_total_time_unreachable>" . xmlentities($hd["percent_total_time_unreachable"]) . "</percent_total_time_unreachable>\n";
            $output .= "   <percent_known_time_unreachable>" . xmlentities($hd["percent_known_time_unreachable"]) . "</percent_known_time_unreachable>\n";

            $output .= "   </host>\n";
        }
    } else {
        foreach ($servicedata as $sd) {
            $output .= "   <service>\n";

            $output .= "   <host_name>" . xmlentities($sd["host_name"]) . "</host_name>\n";
            $output .= "   <service_description>" . xmlentities($sd["service_description"]) . "</service_description>\n";

            $output .= "   <time_ok_scheduled>" . xmlentities($sd["time_ok_scheduled"]) . "</time_ok_scheduled>\n";
            $output .= "   <percent_time_ok_scheduled>" . xmlentities($sd["percent_time_ok_scheduled"]) . "</percent_time_ok_scheduled>\n";
            $output .= "   <percent_known_time_ok_scheduled>" . xmlentities($sd["percent_known_time_ok_scheduled"]) . "</percent_known_time_ok_scheduled>\n";
            $output .= "   <time_ok_unscheduled>" . xmlentities($sd["time_ok_unscheduled"]) . "</time_ok_unscheduled>\n";
            $output .= "   <percent_time_ok_unscheduled>" . xmlentities($sd["percent_time_ok_unscheduled"]) . "</percent_time_ok_unscheduled>\n";
            $output .= "   <percent_known_time_ok_unscheduled>" . xmlentities($sd["percent_known_time_ok_unscheduled"]) . "</percent_known_time_ok_unscheduled>\n";
            $output .= "   <total_time_ok>" . xmlentities($sd["total_time_ok"]) . "</total_time_ok>\n";
            $output .= "   <percent_total_time_ok>" . xmlentities($sd["percent_total_time_ok"]) . "</percent_total_time_ok>\n";
            $output .= "   <percent_known_time_ok>" . xmlentities($sd["percent_known_time_ok"]) . "</percent_known_time_ok>\n";

            $output .= "   <time_warning_scheduled>" . xmlentities($sd["time_warning_scheduled"]) . "</time_warning_scheduled>\n";
            $output .= "   <percent_time_warning_scheduled>" . xmlentities($sd["percent_time_warning_scheduled"]) . "</percent_time_warning_scheduled>\n";
            $output .= "   <percent_known_time_warning_scheduled>" . xmlentities($sd["percent_known_time_warning_scheduled"]) . "</percent_known_time_warning_scheduled>\n";
            $output .= "   <time_warning_unscheduled>" . xmlentities($sd["time_warning_unscheduled"]) . "</time_warning_unscheduled>\n";
            $output .= "   <percent_time_warning_unscheduled>" . xmlentities($sd["percent_time_warning_unscheduled"]) . "</percent_time_warning_unscheduled>\n";
            $output .= "   <percent_known_time_warning_unscheduled>" . xmlentities($sd["percent_known_time_warning_unscheduled"]) . "</percent_known_time_warning_unscheduled>\n";
            $output .= "   <total_time_warning>" . xmlentities($sd["total_time_warning"]) . "</total_time_warning>\n";
            $output .= "   <percent_total_time_warning>" . xmlentities($sd["percent_total_time_warning"]) . "</percent_total_time_warning>\n";
            $output .= "   <percent_known_time_warning>" . xmlentities($sd["percent_known_time_warning"]) . "</percent_known_time_warning>\n";

            $output .= "   <time_critical_scheduled>" . xmlentities($sd["time_critical_scheduled"]) . "</time_critical_scheduled>\n";
            $output .= "   <percent_time_critical_scheduled>" . xmlentities($sd["percent_time_critical_scheduled"]) . "</percent_time_critical_scheduled>\n";
            $output .= "   <percent_known_time_critical_scheduled>" . xmlentities($sd["percent_known_time_critical_scheduled"]) . "</percent_known_time_critical_scheduled>\n";
            $output .= "   <time_critical_unscheduled>" . xmlentities($sd["time_critical_unscheduled"]) . "</time_critical_unscheduled>\n";
            $output .= "   <percent_time_critical_unscheduled>" . xmlentities($sd["percent_time_critical_unscheduled"]) . "</percent_time_critical_unscheduled>\n";
            $output .= "   <percent_known_time_critical_unscheduled>" . xmlentities($sd["percent_known_time_critical_unscheduled"]) . "</percent_known_time_critical_unscheduled>\n";
            $output .= "   <total_time_critical>" . xmlentities($sd["total_time_critical"]) . "</total_time_critical>\n";
            $output .= "   <percent_total_time_critical>" . xmlentities($sd["percent_total_time_critical"]) . "</percent_total_time_critical>\n";
            $output .= "   <percent_known_time_critical>" . xmlentities($sd["percent_known_time_critical"]) . "</percent_known_time_critical>\n";

            $output .= "   <time_unknown_scheduled>" . xmlentities($sd["time_unknown_scheduled"]) . "</time_unknown_scheduled>\n";
            $output .= "   <percent_time_unknown_scheduled>" . xmlentities($sd["percent_time_unknown_scheduled"]) . "</percent_time_unknown_scheduled>\n";
            $output .= "   <percent_known_time_unknown_scheduled>" . xmlentities($sd["percent_known_time_unknown_scheduled"]) . "</percent_known_time_unknown_scheduled>\n";
            $output .= "   <time_unknown_unscheduled>" . xmlentities($sd["time_unknown_unscheduled"]) . "</time_unknown_unscheduled>\n";
            $output .= "   <percent_time_unknown_unscheduled>" . xmlentities($sd["percent_time_unknown_unscheduled"]) . "</percent_time_unknown_unscheduled>\n";
            $output .= "   <percent_known_time_unknown_unscheduled>" . xmlentities($sd["percent_known_time_unknown_unscheduled"]) . "</percent_known_time_unknown_unscheduled>\n";
            $output .= "   <total_time_unknown>" . xmlentities($sd["total_time_unknown"]) . "</total_time_unknown>\n";
            $output .= "   <percent_total_time_unknown>" . xmlentities($sd["percent_total_time_unknown"]) . "</percent_total_time_unknown>\n";
            $output .= "   <percent_known_time_unknown>" . xmlentities($sd["percent_known_time_unknown"]) . "</percent_known_time_unknown>\n";

            $output .= "   </service>\n";
        }
    }
    $output .= "</" . $type . "availability>\n";
    $output .= "</availability>\n";

    return $output;
}


/**
 * Get the raw CSV availability data for a host with arguments
 *
 * @param   string  $type   Type of object (host or service)
 * @param   array   $args   Array of arguments to pass
 * @return  string
 */
function get_raw_nagioscore_csv_availability($args)
{
    global $cfg;
    global $request;

    // Get username
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];
    } else if (isset($request["uid"])) {
        $username = get_user_attr($request["uid"], "username");
    } else {
        $username = "UNKNOWN_USER";
    }

    // Get 'required' args
    $assume_initial_states = grab_array_var($args, "assume_initial_states", grab_array_var($args, "assumeinitialstates", "yes"));
    $assume_state_retention = grab_array_var($args, "assume_state_retention", grab_array_var($args, "assumestateretention", "yes"));
    $assume_states_during_not_running = grab_array_var($args, "assume_states_during_not_running", grab_array_var($args, "assumestatesduringnotrunning", "yes"));
    $include_soft_states = grab_array_var($args, "include_soft_states", grab_array_var($args, "includesoftstates", "no"));
    $initial_assumed_host_state = grab_array_var($args, "initial_assumed_host_state", grab_array_var($args, "initialassumedhoststate", 3));
    $initial_assumed_service_state = grab_array_var($args, "initial_assumed_service_state", grab_array_var($args, "initialassumedservicestate", 6));
    $backtrack = grab_array_var($args, "backtrack", 4);
    $timeperiod = grab_array_var($args, "timeperiod", "");

    // Get timeframe
    $starttime = grab_array_var($args, "starttime", time() - (60 * 60 * 24));
    $endtime = grab_array_var($args, "endtime", time());

    // Get main filtering args
    $host = grab_array_var($args, "host", "");
    $hostgroup = grab_array_var($args, "hostgroup", "");
    $servicegroup = grab_array_var($args, "servicegroup", "");
    $service = grab_array_var($args, "service", "");

    $query_string = "show_log_entries=&t1=" . $starttime . "&t2=" . $endtime;

    // In order to add filtering options, make sure we are filtering by something
    if (!empty($host) || !empty($service) || !empty($hostgroup) || !empty($servicegroup)) {
        
        if (!empty($host)) {
            $query_string .= "&host=" . $host;
        }
        if (!empty($service)) {
            $query_string .= "&service=" . $service;
        }
        if (!empty($hostgroup)) {
            $query_string .= "&hostgroup=" . $hostgroup;
        }
        if (!empty($servicegroup)) {
            $query_string .= "&servicegroup=" . $servicegroup;
        }

    }

    // If we only want hosts or services...
    $only_opt = "";
    if (grab_array_var($args, "hostonly", false)) {
        $only_opt = "hostonly";
    } else if (grab_array_var($args, "serviceonly", false)) {
        $only_opt = "serviceonly";
    }

    // Append options
    $query_string .= "&assumeinitialstates=" . $assume_initial_states . "&assumestateretention=" . $assume_state_retention . "&assumestatesduringnotrunning=" . $assume_states_during_not_running . "&includesoftstates=" . $include_soft_states . "&initialassumedhoststate=" . $initial_assumed_host_state . "&initialassumedservicestate=" . $initial_assumed_service_state . "&backtrack=" . $backtrack . "&csvoutput=&rpttimeperiod=" . $timeperiod;

    // This variable **MUST** go at the end to ensure it works
    $query_string .= "&combinedcsv=" . $only_opt;

    putenv("REQUEST_METHOD=GET");
    putenv("REMOTE_USER=" . $username);
    putenv("QUERY_STRING=" . $query_string);

    $binpath = $cfg['component_info']['nagioscore']['cgi_dir'] . "/avail.cgi";

    //$time_pre = microtime(true);

    $rawoutput = '';
    $fp = popen($binpath, 'r');
    // TODO Throw exception if this fails?
    if ($fp) {
        while (!feof($fp)) {
            $rawoutput .= fread($fp, 1024);
        }
        pclose($fp);
    }

    //$time_post = microtime(true);
    //$exec_time = $time_post - $time_pre;
    //print $exec_time.'<br>';
    //print $query_string.'<br>';

    // Separate HTTP headers from content
    $a = strpos($rawoutput, "Content-type:");
    $pos = strpos($rawoutput, "\r\n\r\n", $a);
    if ($pos === false) {
        $pos = strpos($rawoutput, "\n\n", $a);
    }

    $output = substr($rawoutput, $pos + 4);

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// TIME CALCULATION FUNCTIONS
////////////////////////////////////////////////////////////////////////////////


/**
 * @return  array
 */
function get_report_timeperiod_options()
{
    $arr = array(
        "today" => _("Today"),
        "last24hours" => _("Last 24 Hours"),
        "yesterday" => _("Yesterday"),
        "thisweek" => _("This Week"),
        "thismonth" => _("This Month"),
        "thisquarter" => _("This Quarter"),
        "thisyear" => _("This Year"),
        "lastweek" => _("Last Week"),
        "lastmonth" => _("Last Month"),
        "lastquarter" => _("Last Quarter"),
        "lastyear" => _("Last Year"),
        "custom" => _("Custom"),
    );

    return $arr;
}


/**
 * @param           $tp
 * @param           $start
 * @param           $end
 * @param   string  $startdate
 * @param   string  $enddate
 */
function get_times_from_report_timeperiod($tp, &$start, &$end, $startdate = "", $enddate = "")
{
    $now = time();
    $start = 0;

    if (empty($end)) {
        $end = 0;
    }

    switch ($tp) {

        case "today":
            $start = strtotime("midnight");
            $end = $now;
            break;

        case "last24hours":
            $start = strtotime("24 hours ago");
            $end = $now;
            break;

        case "yesterday":
            $start = strtotime("yesterday");
            $end = strtotime("midnight");
            break;

        case "thisweek":
            if (is_null($format = get_user_meta(0, 'week_format'))) {
                $format = get_option('default_week_format');
            }

            $day_short = 'Sun';
            $day = 'sunday';
            if ($format == WF_EURO) {
                $day_short = 'Mon';
                $day = 'monday';
            } 

            if (date('D', $now) == $day_short) {
                $start = strtotime("today");
            } else {
                $start = strtotime("last $day");
            }

            $end = $now;
            break;

        case "thismonth":
            $start = mktime(0, 0, 0, date("n"), 1, date("Y"));
            $end = $now;
            break;

        case "thisquarter":
            $current_month = date("n");
            $current_year = date("Y");
            for ($x = $current_month, $y = 0; $y <= 4; $x--, $y++) {
                // Cover december rollbacks
                if ($x == 0) {
                    $x = 12;
                    $current_year--;
                }
                if ((($x - 1) % 3) == 0) {
                    break;
                }
            }
            $start = mktime(0, 0, 0, $x, 1, $current_year);
            $end = $now;
            break;

        case "thisyear":
            $start = strtotime("January 1");
            $end = $now;
            break;

        case "lastweek":
            if (is_null($format = get_user_meta(0, 'week_format'))) {
                $format = get_option('default_week_format');
            }

            $day_short = 'Sun';
            $day = 'sunday';
            if ($format == WF_EURO) {
                $day_short = 'Mon';
                $day = 'monday';
            } 

            if (date('D', $now) == $day_short) {
                $start = strtotime("today -7 days");
                $end = strtotime("today");
            } else {
                $start = strtotime("last $day -7 days");
                $end = strtotime("last $day");
            }
            
            break;

        case "lastmonth":
            $start = mktime(0, 0, 0, date("n") - 1, 1, date("Y"));
            $end = mktime(0, 0, 0, date("n"), 1, date("Y"));
            break;

        case "lastquarter":
            $current_month = date("n");
            $current_year = date("Y");
            $quarters = 0;
            $q_end_month = 1;
            $q_end_year = $current_year;
            for ($x = $current_month, $y = 0; $y <= 7; $x--, $y++) {
                // Cover december rollbacks
                if ($x == 0) {
                    $x = 12;
                    $current_year--;
                }
                if ((($x - 1) % 3) == 0) {
                    $quarters++;
                    if ($quarters == 1) {
                        $q_end_month = $x;
                        $q_end_year = $current_year;
                    }
                    if ($quarters > 1) {
                        break;
                    }
                }
            }
            $start = mktime(0, 0, 0, $x, 1, $current_year);
            $end = mktime(0, 0, 0, $q_end_month, 1, $q_end_year);
            break;

        case "lastyear":
            $start = mktime(0, 0, 0, 1, 1, date("Y") - 1);
            $end = mktime(0, 0, 0, 1, 1, date("Y"));
            break;

        case "custom":

            // Check for empty custom dates
            if (empty($enddate)) {
                $enddate = strftime("%c", time());
            }
            if (empty($startdate)) {
                $startdate = strftime("%c", time() - (60 * 60 * 24));
            }

            // Custom dates passed to us
            if (isset($_SESSION['date_format']))
                $format = $_SESSION['date_format'];
            else {
                if (is_null($format = get_user_meta(0, 'date_format'))) {
                    $format = get_option('default_date_format');
                }
            }

            $format = intval($format);

            switch ($format) {
                case DF_US:
                    break;
                case DF_EURO:
                    $datestart_array = explode(" ", trim($startdate));
                    $datestart_time = trim(grab_array_var($datestart_array, "1", ""));
                    $datestart_array = explode("/", trim($datestart_array[0]));
                    $startdate = implode("-", array_reverse($datestart_array)) . " " . $datestart_time;

                    $dateend_array = explode(" ", trim($enddate));
                    $dateend_time = trim(grab_array_var($dateend_array, "1", ""));
                    $dateend_array = explode("/", trim($dateend_array[0]));
                    $enddate = implode("/", array_reverse($dateend_array)) . " " . $dateend_time;
                    break;
                default:
                    break;
            }

            $start = strtotime($startdate);
            $end = strtotime($enddate);

            // Handle unix timestamps
            if ($start === false) {
                $start = intval($startdate);
            }
            if ($end === false) {
                $end = intval($enddate);
            }
            break;

        default:
            break;

    }

    // Fix wierd values
    if ($end > $now) {
        $end = $now;
    }

    if ($start > $end) {
        if ($end == 0) {
            $end = $start;
        } else {
            $start = $end;
        }
    }
}


////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////////////


/**
 * Converts state from integer to string
 * 
 * @param   int     $state_type     State type as integer
 * @return  string                  State type as human readable
 */
function state_type_to_string($state_type)
{
    $output = "?";
    switch ($state_type) {
        case 0:
            $output = _("SOFT");
            break;
        case 1:
            $output = _("HARD");
            break;
        default:
            break;
    }
    return $output;
}


/**
 * Converts service state type to string
 *
 * @param   int     $state
 * @param   bool    $assumeok
 * @return  string
 */
function service_state_to_string($state, $assumeok = true)
{
    $output = "? (" . $state . ")";
    switch ($state) {
        case 0:
            $output = _("OK");
            break;
        case 1:
            $output = _("WARNING");
            break;
        case 2:
            $output = _("CRITICAL");
            break;
        case 3:
            $output = _("UNKNOWN");
            break;
        case -1:
            if ($assumeok == true) {
                $output = _("OK");
            }
            break;
        default:
            break;
    }
    return $output;
}


/**
 * Converts host state type to string
 *
 * @param   string  $state
 * @param   bool    $assumeup
 * @return  string
 */
function host_state_to_string($state, $assumeup = true)
{
    $output = "? (" . $state . ")";
    switch ($state) {
        case 0:
            $output = _("UP");
            break;
        case 1:
            $output = _("DOWN");
            break;
        case 2:
            $output = _("UNREACHABLE");
            break;
        case -1:
            if ($assumeup == true)
                $output = _("UP");
            break;
        default:
            break;
    }
    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// MY REPORTS FUNCTIONS
////////////////////////////////////////////////////////////////////////////////


/**
 * @param   int     $userid
 * @return  array
 */
function get_myreports($userid = 0)
{
    $myreports_s = get_user_meta($userid, 'myreports');

    if ($myreports_s == null) {
        $myreports = array();
    } else {
        $myreports = unserialize($myreports_s);
    }

    return $myreports;
}


/**
 * @param   int     $userid
 * @param   int     $id
 * @return  null
 */
function get_myreport_id($userid = 0, $id)
{
    $myreports = get_myreports($userid);

    if (array_key_exists($id, $myreports)) {
        return $myreports[$id];
    }

    return null;
}


/**
 * @param   int     $userid
 * @param   int     $id
 * @return  string
 */
function get_myreport_url($userid = 0, $id)
{
    $url = "";

    $myreport = get_myreport_id($userid, $id);
    if ($myreport != null) {
        $url = $myreport["url"];
    }

    return $url;
}


/**
 * Add a report to the users reports
 *
 * @param   int     $userid
 * @param   string  $title
 * @param   string  $url
 * @param   array   $meta
 * @return  array
 */
function add_myreport($userid = 0, $title = '', $url = '', $meta = null, $dontdisplay = 0, $id = 0)
{
    $myreports = get_myreports($userid);

    if (empty($id)) {
        $id = random_string(6);
    }

    $newreport = array(
        "title" => $title,
        "url" => $url,
        "meta" => $meta,
        "dontdisplay" => $dontdisplay
    );

    $myreports[$id] = $newreport;

    set_user_meta($userid, 'myreports', serialize($myreports), false);

    return $myreports;
}


/**
 * Delete a report from list of reports
 *
 * @param   int     $userid
 * @param   int     $id
 */
function delete_myreport($userid = 0, $id)
{
    $myreports = get_myreports(0);
    unset($myreports[$id]);
    set_user_meta(0, 'myreports', serialize($myreports), false);
}


/**
 * Creates buttons in upper right-hand corner that let you select if you'd
 * like to add the report to your my reports list
 *
 * @param   string  $title      Report title
 * @param   string  $url        URL for the report to add
 * @param   array   $meta       Array of meta data to get added
 * @param   string  $return     
 * @return  string              HTML for my report addition button
 */
function get_add_myreport_html($title, $url, $meta = array(), $return = '')
{
    $myreportsurl = get_base_url() . "reports/myreports.php?add=1&title=" . urlencode($title) . "&meta_s=" . urlencode(serialize($meta));

    $html = "";

    $theme = get_theme();
    if ($theme == "xi2014" || $theme == "classic") {
        $html .= '<a data-url="' . $myreportsurl . '" alt="'._('Add to My Reports').'" class="btn-report-action" title="'._('Add to My Reports').'"><img src="' . theme_image("star.png") . '"></a>';
    } else {
        $html .= '<a data-url="' . $myreportsurl . '" alt="'._('Add to My Reports').'" title="'._('Add to My Reports').'" class="btn btn-sm btn-default tt-bind btn-report-action" data-placement="bottom"><i class="fa fa-star"></i></a>';
    }

    // Determine whether there is "native" PDF and scheduled reporting or not...
    $rawurl = $url;
    $urlparts = parse_url($rawurl);
    $path = $urlparts["path"];
    $theurl = $path;
    $theurl = str_replace("/nagiosxi/reports/", "", $theurl);
    $theurl = str_replace("reports/", "", $theurl);

    switch ($theurl) {
        case "availability.php":
        case "alertheatmap.php":
        case "statehistory.php":
        case "histogram.php":
        case "topalertproducers.php":
        case "notifications.php":
        case "sla.php":
        case "/nagiosxi/includes/components/bandwidthreport/index.php":
        case "/nagiosxi/includes/components/capacityplanning/capacityplanning.php":
        case "/nagiosxi/includes/components/nagiosna/nagiosna-reports.php":
        case "/nagiosxi/includes/components/nagiosna/nagiosna-queries.php":
        case "/nagiosxi/includes/components/nsti/nsti-queries.php":
        case "eventlog.php":
        case "execsummary.php":
            break;
        default;
            break;
    }

    // CALLBACKS /////////////////////////////////////////////////////////
    // Do callbacks for other components - e.g. scheduled reporting component
    $cbdata = array(
        "title" => $title,
        "url" => $url,
        "meta" => $meta,
        "actions" => array(),
    );
    do_callbacks(CALLBACK_REPORTS_ACTION_LINK, $cbdata);
    $customactions = grab_array_var($cbdata, "actions", array());
    foreach ($customactions as $ca) {
        $html .= $ca;
    }

    return $html;
}


function get_hostgroup_alias($hostgroup, $sprintf_format = " (%s)", $dont_check_hostgroup_alias_setting = false, $show_if_alias_same_as_hostgroup = false, $htmlentities = true)
{
    $hostgroup_object = get_xml_hostgroup_objects(array("hostgroup_name" => $hostgroup));
    $hostgroup_alias = "";

    if ((string)$hostgroup_object->hostgroup->alias != "") {
        if (((string)$hostgroup_object->hostgroup->alias != $hostgroup) || $show_if_alias_same_as_hostgroup) {
            $hostgroup_alias = sprintf($sprintf_format, (string)$hostgroup_object->hostgroup->alias);
        }
    }
    if ($htmlentities && !empty($hostgroup_alias)) {
        $hostgroup_alias = encode_form_val($hostgroup_alias);
    }

    return $hostgroup_alias;
}


function get_servicegroup_alias($servicegroup, $sprintf_format = " (%s)", $dont_check_servicegroup_alias_setting = false, $show_if_alias_same_as_servicegroup = false, $htmlentities = true)
{
    $servicegroup_object = get_xml_servicegroup_objects(array("servicegroup_name" => $servicegroup));
    $servicegroup_alias = "";

    if ((string)$servicegroup_object->servicegroup->alias != "") {
        if (((string)$servicegroup_object->servicegroup->alias != $servicegroup) || $show_if_alias_same_as_servicegroup) {
            $servicegroup_alias = sprintf($sprintf_format, (string)$servicegroup_object->servicegroup->alias);
        }
    }
    if ($htmlentities && !empty($servicegroup_alias)) {
        $servicegroup_alias = encode_form_val($servicegroup_alias);
    }

    return $servicegroup_alias;
}


function get_host_alias($host, $sprintf_format = " (%s)", $dont_check_host_alias_setting = false, $show_if_alias_same_as_host = false, $htmlentities = true)
{
    $host_alias = "";

    if (get_user_meta(0, "display_host_display_name") || $dont_check_host_alias_setting) {
        $host_object = get_xml_host_objects(array("host_name" => $host));
        if ((string)$host_object->host->alias != "") {
            if (((string)$host_object->host->alias != $host) || $show_if_alias_same_as_host) {
                $host_alias = sprintf($sprintf_format, (string)$host_object->host->alias);
            }
        }
    }
    if ($htmlentities && !empty($host_alias)) {
        $host_alias = encode_form_val($host_alias);
    }

    return $host_alias;
}


function get_service_alias($host, $service, $sprintf_format = " (%s)", $dont_check_service_alias_setting = false, $show_if_alias_same_as_service = false, $htmlentities = true) 
{
    $service_alias = "";

    if (get_user_meta(0, "display_service_display_name") || $dont_check_service_alias_setting) {
        $service_object = get_xml_service_objects(array("host_name" => $host, "service_description" => $service));
        if ((string)$service_object->service->display_name != "") {
            if (((string)$service_object->service->display_name != $service) || $show_if_alias_same_as_service) {
                $service_alias = sprintf($sprintf_format, (string)$service_object->service->display_name);
            }
        }
    }
    if ($htmlentities && !empty($service_alias)) {
        $service_alias = encode_form_val($service_alias);
    }

    return $service_alias;
}


// ==================================
// SLA Functions
// ==================================


/**
 * Get an array of SLA data (mostly used in API and check_xi_sla.php)
 *
 */
function get_sla_data()
{
    global $request;
    $data = array();

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");

    $dont_count_downtime = grab_request_var("dont_count_downtime", 0);
    $dont_count_warning = grab_request_var("dont_count_warning", 0);
    $dont_count_unknown = grab_request_var("dont_count_unknown", 0);
    $show_options = grab_request_var("showopts", 0);

    $starttime = grab_request_var("starttime", time() - 60*60*24);
    $endtime = grab_request_var("endtime", time());

    // ADVANCED OPTIONS
    $timeperiod = grab_request_var("timeperiod", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);

    // Validate passed data
    if (empty($host) && empty($service) && empty($hostgroup) && empty($servicegroup)) {
        return array('error' => _('You must enter a host, service, hostgroup, or servicegroup.'));
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC SERVICE
    ///////////////////////////////////////////////////////////////////////////
    if ($service != "" && $service != "average") {

        // Get service availability
        $args = array(
            "host" => $host,
            "service" => $service,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate,
            'dont_count_downtime' => $dont_count_downtime,
            'dont_count_warning' => $dont_count_warning,
            'dont_count_unknown' => $dont_count_unknown
        );
        $servicedata = get_xml_availability('service', $args);

        // Check if we have data
        $have_data = false;
        if ($servicedata && intval($servicedata->havedata) == 1)
            $have_data = true;

        if ($have_data == false) {
            print_r("Availability data is not available when monitoring engine is not running");
        } else {
            // We have data
            if ($servicedata) {

                $lasthost = "";
                foreach ($servicedata->serviceavailability->service as $s) {

                    $hn = strval($s->host_name);
                    $sd = strval($s->service_description);

                    if (!$dont_count_downtime) {
                        $ok = floatval($s->percent_known_time_ok);
                    } else {
                        $ok = (($ok = floatval($s->percent_known_time_ok) + floatval($s->percent_known_time_warning_scheduled) + floatval($s->percent_known_time_critical_scheduled) + floatval($s->percent_known_time_unkown_scheduled)) > 100 ? 100 : $ok);
                    }

                    $data['sla'][$hn][$sd] = number_format($ok, 3);
                }
            }
        }

        if ($show_options) {
            $data['options'] = $args;
        }
        return $data;
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOST
    ///////////////////////////////////////////////////////////////////////////
    else if ($host != "") {

        // Get host availability
        $args = array(
            "host" => $host,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate,
            'dont_count_downtime' => $dont_count_downtime,
            'dont_count_warning' => $dont_count_warning,
            'dont_count_unknown' => $dont_count_unknown
        );
        $hostdata = get_xml_availability("host", $args);

        // Get service availability
        $args = array(
            "host" => $host,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate,
            'dont_count_downtime' => $dont_count_downtime,
            'dont_count_warning' => $dont_count_warning,
            'dont_count_unknown' => $dont_count_unknown
        );
        $servicedata = get_xml_availability("service", $args);

        // Check if we have data
        $have_data = false;
        if ($hostdata && $servicedata && intval($hostdata->havedata) == 1 && intval($servicedata->havedata) == 1)
            $have_data = true;

        if ($have_data == false) {
            print_r("Availability data is not available when monitoring engine is not running");
        } else {

            // We have data let's do it!

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata->serviceavailability->service as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = floatval($s->percent_known_time_ok);
                        $service_warning = floatval($s->percent_known_time_warning);
                        $service_unknown = floatval($s->percent_known_time_unknown);
                        $service_critical = floatval($s->percent_known_time_critical);
                    } else {
                        $service_ok = (($ok = floatval($s->percent_known_time_ok) + floatval($s->percent_known_time_warning_scheduled) + floatval($s->percent_known_time_critical_scheduled) + floatval($s->percent_known_time_unkown_scheduled)) > 100 ? 100 : $ok);
                        $service_warning = floatval($s->percent_known_time_warning_unscheduled);
                        $service_unknown = floatval($s->percent_known_time_unkown_unscheduled);
                        $service_critical = floatval($s->percent_known_time_critical_unscheduled);
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            if ($hostdata) {
                foreach ($hostdata->hostavailability->host as $h) {

                    $hn = strval($h->host_name);

                    if (!$dont_count_downtime) {
                        $up = floatval($h->percent_known_time_up);
                    } else {
                        $up = floatval($h->percent_known_time_up) + floatval($h->percent_known_time_down_scheduled) + floatval($h->percent_known_time_unreachable_scheduled);
                    }

                    $data['sla']['hosts'][$hn] = number_format($up, 3);
                }
            }

            if ($servicedata) {
                foreach ($servicedata->serviceavailability->service as $s) {

                    $hn = strval($s->host_name);
                    $sd = strval($s->service_description);

                    if (!$dont_count_downtime) {
                        $ok = floatval($s->percent_known_time_ok);
                    } else {
                        $ok = (($ok = floatval($s->percent_known_time_ok) + floatval($s->percent_known_time_warning_scheduled) + floatval($s->percent_known_time_critical_scheduled) + floatval($s->percent_known_time_unkown_scheduled)) > 100 ? 100 : $ok);
                    }

                    // send average only
                    $data['sla']['services'][$sd] = number_format($ok, 3);
                }
            }

            $data['sla']['services']['average'] = number_format($avg_service_ok, 3);
        }

        if ($show_options) {
            $data['options'] = $args;
        }
        return $data;
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOSTGROUP OR SERVICEGROUP
    ///////////////////////////////////////////////////////////////////////////
    else if ($hostgroup != "" || $servicegroup != "") {

        // get host availability
        $args = array(
            "host" => "",
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate,
            'dont_count_downtime' => $dont_count_downtime,
            'dont_count_warning' => $dont_count_warning,
            'dont_count_unknown' => $dont_count_unknown
        );
        if ($hostgroup != "")
            $args["hostgroup"] = $hostgroup;
        else
            $args["servicegroup"] = $servicegroup;
        $hostdata = get_xml_availability("host", $args);

        // get service availability
        $args = array(
            "host" => "",
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate,
            'dont_count_downtime' => $dont_count_downtime,
            'dont_count_warning' => $dont_count_warning,
            'dont_count_unknown' => $dont_count_unknown
        );
        if ($hostgroup != "")
            $args["hostgroup"] = $hostgroup;
        else
            $args["servicegroup"] = $servicegroup;
        $servicedata = get_xml_availability("service", $args);

        // check if we have data
        $have_data = false;
        if ($hostdata && $servicedata && intval($hostdata->havedata) == 1 && intval($servicedata->havedata) == 1)
            $have_data = true;
        if ($have_data == false) {
            print_r("Availability data is not available when monitoring engine is not running");
        } // we have data..
        else {
            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata->hostavailability->host as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = floatval($h->percent_known_time_up);
                        $host_down = floatval($h->percent_known_time_down);
                        $host_unreachable = floatval($h->percent_known_time_unreachable);
                    } else {
                        $host_up = (($ok = floatval($h->percent_known_time_up) + floatval($h->percent_known_time_down_scheduled) + floatval($h->percent_known_time_unreachable_scheduled)) > 100 ? 100 : $ok);
                        $host_down = floatval($h->percent_known_time_down_unscheduled);
                        $host_unreachable = floatval($h->percent_known_time_unreachable_unscheduled);
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata->serviceavailability->service as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = floatval($s->percent_known_time_ok);
                        $service_warning = floatval($s->percent_known_time_warning);
                        $service_unknown = floatval($s->percent_known_time_unknown);
                        $service_critical = floatval($s->percent_known_time_critical);
                    } else {
                        $service_ok = (($ok = floatval($s->percent_known_time_ok) + floatval($s->percent_known_time_warning_scheduled) + floatval($s->percent_known_time_critical_scheduled) + floatval($s->percent_known_time_unkown_scheduled)) > 100 ? 100 : $ok);
                        $service_warning = floatval($s->percent_known_time_warning_unscheduled);
                        $service_unknown = floatval($s->percent_known_time_unkown_unscheduled);
                        $service_critical = floatval($s->percent_known_time_critical_unscheduled);
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            // host table
            if ($hostdata) {

                foreach ($hostdata->hostavailability->host as $h) {

                    $hn = strval($h->host_name);

                    if (!$dont_count_downtime) {
                        $up = floatval($h->percent_known_time_up);
                    } else {
                        $up = floatval($h->percent_known_time_up) + floatval($h->percent_known_time_down_scheduled) + floatval($h->percent_known_time_unreachable_scheduled);
                    }
                }

                // send average only
                $data['sla']['hosts']['average'] = number_format($avg_host_up, 3);
            }

            // service table
            if ($servicedata) {
                foreach ($servicedata->serviceavailability->service as $s) {

                    $hn = strval($s->host_name);
                    $sd = strval($s->service_description);

                    if (!$dont_count_downtime) {
                        $ok = floatval($s->percent_known_time_ok);
                    } else {
                        $ok = (($ok = floatval($s->percent_known_time_ok) + floatval($s->percent_known_time_warning_scheduled) + floatval($s->percent_known_time_critical_scheduled) + floatval($s->percent_known_time_unkown_scheduled)) > 100 ? 100 : $ok);
                    }
                }

                // send average only
                $data['sla']['services']['average'] = number_format($avg_service_ok, 3);
            }
        }

        if ($show_options) {
            $data['options'] = $args;
        }
        return $data;
    }
}

/**
 * @param $apct
 * @param $npct
 * @param $cnt
 */
function update_avail_avg(&$apct, $npct, &$cnt)
{
    $newpct = (($apct * $cnt) + $npct) / ($cnt + 1);
    $cnt++;
    $apct = $newpct;
}
