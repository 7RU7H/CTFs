<?php
//
// XI Core Ajax Helper Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


////////////////////////////////////////////////////////////////////////
// TAC AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   array   $args
 * @return  string
 */
function xicore_ajax_get_network_outages_summary_html($args = null)
{
    $mode = grab_array_var($args, "mode");
    $admin = is_admin();

    $output = '';

    $url = "outages-xml.cgi";
    $cgioutput = coreui_get_raw_cgi_output($url, array());
    $xml = simplexml_load_string($cgioutput);

    if (!$xml) {
        $output .= '
        <table class="table table-condensed table-striped table-bordered table-no-margin">
        <thead>
        <tr><th>' . _('Network Outages') . '</th></tr>
        </thead>
        <tbody>
        ';
        $text = "";
        $text .= _("Monitoring engine may be stopped.");
        if ($admin == true)
            $text .= "<br><a href='" . get_base_url() . "admin/sysstat.php'>" . _('Check engine') . "</a>";
        $output .= "<tr><td class='tacoutageImportantProblem'><b>" . _('Error: Unable to parse XML output!') . "</b><br>" . $text . "</td></tr>";
        $output .= '
        </tbody>
        </table>
        ';
    } else {
        $output .= '
        <table class="table table-condensed table-striped table-bordered table-no-margin">
        <thead>
        <tr><th>' . _('Network Outages') . '</th></tr>
        </thead>
        <tbody>
        ';

        $total = 0;
        foreach ($xml->hostoutage as $ho) {
            $total++;
        }

        $url = get_base_url() . "includes/components/xicore/status.php?show=outages";

        $output .= '<tr class="tacSubHeader"><td><a href="' . $url . '">' . $total . ' ' . _('Outages') . '</a></td></tr>';

        if ($total == 0)
            $output .= '<tr><td>' . _('No Blocking Outages') . '</td></tr>';
        else {
            $output .= '<tr><td><div class="tacoutageImportantProblem"><a href="' . $url . '">' . $total . ' ' . _('Blocking Outages') . '</a></div></td></tr>';
        }

        $output .= '
        </tbody>
        </table>
        ';
    }

    if ($mode == DASHLET_MODE_INBOARD) {
        $output .= '
        <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
        ';
    }

    return $output;
}


/**
 * @param   array   $args
 * @return  string
 */
function xicore_ajax_get_network_health_html($args = null)
{
    $ignore_soft_states = grab_array_var($args, "ignore_soft_states");
    $host_warning_threshold = grab_array_var($args, "host_warning_threshold");
    $host_critical_threshold = grab_array_var($args, "host_critical_threshold");
    $service_warning_threshold = grab_array_var($args, "service_warning_threshold");
    $service_critical_threshold = grab_array_var($args, "service_critical_threshold");

    $output = '';

    // Get host status
    $total_hosts = 0;

    // Get total number of hosts
    $backendargs = array();
    $backendargs["cmd"] = "gethoststatus";
    $backendargs["totals"] = 1;
    $xml = get_xml_host_status($backendargs);

    if ($xml) {
        $total_hosts = intval($xml->recordcount);
    }

    // Get the total number of problems
    $backendargs["current_state"] = "in:1,2";
    if ($ignore_soft_states == 1) {
        $backendargs["state_type"] = 1;
    }
    $xml = get_xml_host_status($backendargs);

    if ($xml) {
        $total_notok = intval($xml->recordcount);
    }

    $hosts_ok = $total_hosts - $total_notok;

    if ($total_hosts == 0) {
        $health_percent = 0;
    } else {
        $health_percent = ($hosts_ok / $total_hosts) * 100;
    }

    $val = intval($health_percent);

    // Begin html build
    $output .= '
    <table class="table table-condensed table-striped table-bordered" style="margin: 0 0 5px 0;">
        <thead>
            <tr><th colspan="2">' . _('Network Health') . '</th></tr>
        </thead>
    <tbody>';

    $url = get_base_url() . "includes/components/xicore/status.php?show=hosts";
    $content = "<a href='" . $url . "'>" . $val . "%</a>";

    if ($health_percent < $host_critical_threshold) {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_RED . ";'>" . $content . "</div>";
    } else if ($health_percent < $host_warning_threshold) {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_YELLOW . ";'>" . $content . "</div>";
    } else {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_GREEN . ";'>" . $content . "</div>";
    }

    $output .= '<tr><td><b>' . _('Host Health') . '</b></td><td width="100px"><span class="statbar">' . $spanval . '</span></td></tr>';

    // Get total number of hosts
    $backendargs = array();
    $backendargs["cmd"] = "getservicestatus";
    $backendargs["totals"] = 1;
    $xml = get_xml_service_status($backendargs);

    if ($xml) {
        $total_services = intval($xml->recordcount);
    }

    // Get the total number of problems
    $backendargs["current_state"] = "in:1,2,3";
    if ($ignore_soft_states == 1) {
        $backendargs["state_type"] = 1;
    }
    $xml = get_xml_service_status($backendargs);

    if ($xml) {
        $total_notok = intval($xml->recordcount);
    }

    $services_ok = $total_services - $total_notok;

    // Calculate percentage
    if ($total_services == 0) {
        $health_percent = 0;
    } else {
        $health_percent = ($services_ok / $total_services) * 100;
    }

    $val = intval($health_percent);

    $url = get_base_url() . "includes/components/xicore/status.php?show=services";
    $content = "<a href='" . $url . "'>" . $val . "%</a>";

    if ($health_percent < $service_critical_threshold) {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_RED . ";'>" . $content . "</div>";
    } else if ($health_percent < $service_warning_threshold) {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_YELLOW . ";'>" . $content . "</div>";
    } else {
        $spanval = "<div style='height: 15px; width: " . $val . "px; background-color:  " . COMMONCOLOR_GREEN . ";'>" . $content . "</div>";
    }

    $output .= '<tr><td><b>' . _('Service Health') . '</b></td><td width="100px"><span class="statbar">' . $spanval . '</span></td></tr>';

    $output .= '
    </tbody>
    </table>';

    $output .= '<div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>';

    return $output;
}


/**
 * @param   array   $args
 * @return  string
 */
function xicore_ajax_get_host_status_tac_summary_html($args = null)
{
    $mode = grab_array_var($args, "mode");
    $base_url = get_base_url() . "includes/components/xicore/status.php?show=hosts";

    $info = get_host_status_tac_summary_data(false, array(), true);

    $output = '
    <table class="table table-condensed table-striped table-bordered table-no-margin">
    <thead>
    <tr><th colspan="4">' . _("Hosts") . '</th></tr>
    </thead>
    <tbody>';

    $output .= '<tr class="tacSubHeader">';
    $output .= '<td width="135"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '">' . $info["down"]["total"] . ' ' . _('Down') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '">' . $info["unreachable"]["total"] . ' ' . _('Unreachable') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UP . '">' . $info["up"]["total"] . ' ' . _('Up') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_PENDING . '">' . $info["pending"]["total"] . ' ' . _('Pending') . '</a></td>';
    $output .= '</tr>';

    $output .= '<tr>';

    // Down
    $output .= '<td>';
    if ($info["down"]["unhandled"]) {
        $output .= '<div class="tachostImportantProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=' . (HOSTSTATUSATTR_NOTINDOWNTIME | HOSTSTATUSATTR_NOTACKNOWLEDGED) . '">' . $info["down"]["unhandled"] . ' ' . _('Unhandled Problems') . '</a></div>';
    }
    if ($info["down"]["acknowledged"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=4">' . $info["down"]["acknowledged"] . '' . _(' Acknowledged') . '</a></div>';
    }
    if ($info["down"]["scheduled"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=1">' . $info["down"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["down"]["active"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=' . (HOSTSTATUSATTR_CHECKSENABLED) . '">' . $info["down"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["down"]["disabled"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=' . (HOSTSTATUSATTR_CHECKSDISABLED | HOSTSTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["down"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    if ($info["down"]["soft"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_DOWN . '&hostattr=524288">' . $info["down"]["soft"] . ' ' . _('Soft Problems') . '</a></div>';
    }
    $output .= '</td>';

    // Unreachable
    $output .= '<td>';
    if ($info["unreachable"]["unhandled"]) {
        $output .= '<div class="tachostImportantProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=' . (HOSTSTATUSATTR_NOTINDOWNTIME | HOSTSTATUSATTR_NOTACKNOWLEDGED) . '">' . $info["unreachable"]["unhandled"] . ' ' . _('Unhandled Problems') . '</a></div>';
    }
    if ($info["unreachable"]["acknowledged"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=4">' . $info["unreachable"]["acknowledged"] . ' ' . _('Acknowledged') . '</a></div>';
    }
    if ($info["unreachable"]["scheduled"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=1">' . $info["unreachable"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["unreachable"]["active"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=' . (HOSTSTATUSATTR_CHECKSENABLED) . '">' . $info["unreachable"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["unreachable"]["disabled"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=' . (HOSTSTATUSATTR_CHECKSDISABLED | HOSTSTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["unreachable"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    if ($info["unreachable"]["soft"]) {
        $output .= '<div class="tachostProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UNREACHABLE . '&hostattr=524288">' . $info["unreachable"]["soft"] . ' ' . _('Soft Problems') . '</a></div>';
    }
    $output .= '</td>';

    // Up
    $output .= '<td>';
    if ($info["up"]["scheduled"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UP . '&hostattr=1">' . $info["up"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["up"]["active"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UP . '&hostattr=' . (HOSTSTATUSATTR_CHECKSENABLED) . '">' . $info["up"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["up"]["disabled"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_UP . '&hostattr=' . (HOSTSTATUSATTR_CHECKSDISABLED | HOSTSTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["up"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    $output .= '</td>';

    // Pending
    $output .= '<td>';
    if ($info["pending"]["scheduled"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_PENDING . '&hostattr=1">' . $info["pending"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["pending"]["active"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_PENDING . '&hostattr=' . (HOSTSTATUSATTR_CHECKSENABLED) . '">' . $info["pending"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["pending"]["disabled"]) {
        $output .= '<div class="tachostNoProblem"><a href="' . $base_url . '&hoststatustypes=' . HOSTSTATE_PENDING . '&hostattr=' . (HOSTSTATUSATTR_CHECKSDISABLED | HOSTSTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["pending"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    $output .= '</td>';

    $output .= '</tr>';

    $output .= '
    </tbody>
    </table>';

    if ($mode == DASHLET_MODE_INBOARD) {
        $output .= '
        <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
        ';
    }

    return $output;
}


/**
 * @param   array   $args
 * @return  string
 */
function xicore_ajax_get_service_status_tac_summary_html($args = null)
{
    $mode = grab_array_var($args, "mode");
    $base_url = get_base_url() . "includes/components/xicore/status.php?show=services";

    $info = get_service_status_tac_summary_data(false, array(), true);

    $output = '
    <table class="table table-condensed table-striped table-bordered table-no-margin">
    <thead>
    <tr><th colspan="5">' . _('Services') . '</th></tr>
    </thead>
    <tbody>';

    $output .= '<tr class="tacSubHeader">';
    $output .= '<td width="135"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '">' . $info["critical"]["total"] . ' ' . _('Critical') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '">' . $info["warning"]["total"] . ' ' . _('Warning') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '">' . $info["unknown"]["total"] . ' ' . _('Unknown') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_OK . '">' . $info["ok"]["total"] . ' ' . _('Ok') . '</a></td>';
    $output .= '<td width="135"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_PENDING . '">' . $info["pending"]["total"] . ' ' . _('Pending') . '</a></td>';
    $output .= '</tr>';

    $output .= '<tr>';

    // Critical
    $output .= '<td>';
    if ($info["critical"]["unhandled"]) {
        $output .= '<div class="tacserviceImportantProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=' . (SERVICESTATUSATTR_NOTINDOWNTIME | SERVICESTATUSATTR_NOTACKNOWLEDGED) . '&hoststatustypes=3">' . $info["critical"]["unhandled"] . ' ' . _('Unhandled Problems') . '</a></div>';
    }
    if ($info["critical"]["hostproblem"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&hoststatustypes=12">' . $info["critical"]["hostproblem"] . ' ' . _('On Problem Hosts') . '</a></div>';
    }
    if ($info["critical"]["acknowledged"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=4">' . $info["critical"]["acknowledged"] . ' ' . _('Acknowledged') . '</a></div>';
    }
    if ($info["critical"]["scheduled"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=1">' . $info["critical"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["critical"]["active"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSENABLED) . '">' . $info["critical"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["critical"]["disabled"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSDISABLED | SERVICESTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["critical"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    if ($info["critical"]["soft"]) {
        $output .= '<div class="tacserviceProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_CRITICAL . '&serviceattr=524288">' . $info["critical"]["soft"] . ' ' . _('Soft Problems') . '</a></div>';
    }
    $output .= '</td>';

    // Warning
    $output .= '<td>';
    if ($info["warning"]["unhandled"]) {
        $output .= '<div class="tacserviceImportantWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=' . (SERVICESTATUSATTR_NOTINDOWNTIME | SERVICESTATUSATTR_NOTACKNOWLEDGED) . '&hoststatustypes=3">' . $info["warning"]["unhandled"] . ' ' . _('Unhandled Problems') . '</a></div>';
    }
    if ($info["warning"]["hostproblem"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&hoststatustypes=12">' . $info["warning"]["hostproblem"] . ' ' . _('On Problem Hosts') . '</a></div>';
    }
    if ($info["warning"]["acknowledged"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=4">' . $info["warning"]["acknowledged"] . ' ' . _('Acknowledged') . '</a></div>';
    }
    if ($info["warning"]["scheduled"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=1">' . $info["warning"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["warning"]["active"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSENABLED) . '">' . $info["warning"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["warning"]["disabled"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSDISABLED | SERVICESTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["warning"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    if ($info["warning"]["soft"]) {
        $output .= '<div class="tacserviceProblemWarning"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_WARNING . '&serviceattr=524288">' . $info["warning"]["soft"] . ' ' . _('Soft Problems') . '</a></div>';
    }
    $output .= '</td>';

    // Unknown
    $output .= '<td>';
    if ($info["unknown"]["unhandled"]) {
        $output .= '<div class="tacserviceImportantUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=' . (SERVICESTATUSATTR_NOTINDOWNTIME | SERVICESTATUSATTR_NOTACKNOWLEDGED) . '&hoststatustypes=3">' . $info["unknown"]["unhandled"] . ' ' . _('Unhandled Problems') . '</a></div>';
    }
    if ($info["unknown"]["hostproblem"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&hoststatustypes=12">' . $info["unknown"]["hostproblem"] . ' ' . _('On Problem Hosts') . '</a></div>';
    }
    if ($info["unknown"]["acknowledged"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=4">' . $info["unknown"]["acknowledged"] . ' ' . _('Acknowledged') . '</a></div>';
    }
    if ($info["unknown"]["scheduled"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=1">' . $info["unknown"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["unknown"]["active"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSENABLED) . '">' . $info["unknown"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["unknown"]["disabled"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSDISABLED | SERVICESTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["unknown"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    if ($info["unknown"]["soft"]) {
        $output .= '<div class="tacserviceProblemUnknown"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_UNKNOWN . '&serviceattr=524288">' . $info["unknown"]["soft"] . ' ' . _('Soft Problems') . '</a></div>';
    }
    $output .= '</td>';

    // Ok
    $output .= '<td>';
    if ($info["ok"]["scheduled"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_OK . '&serviceattr=1">' . $info["ok"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["ok"]["active"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_OK . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSENABLED) . '">' . $info["ok"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["ok"]["disabled"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_OK . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSDISABLED | SERVICESTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["ok"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    $output .= '</td>';

    // Pending
    $output .= '<td>';
    if ($info["pending"]["scheduled"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_PENDING . '&serviceattr=1">' . $info["pending"]["scheduled"] . ' ' . _('Scheduled') . '</a></div>';
    }
    if ($info["pending"]["active"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_PENDING . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSENABLED) . '">' . $info["pending"]["active"] . ' ' . _('Active') . '</a></div>';
    }
    if ($info["pending"]["disabled"]) {
        $output .= '<div class="tacserviceNoProblem"><a href="' . $base_url . '&servicestatustypes=' . SERVICESTATE_PENDING . '&serviceattr=' . (SERVICESTATUSATTR_CHECKSDISABLED | SERVICESTATUSATTR_PASSIVECHECKSENABLED) . '">' . $info["pending"]["disabled"] . ' ' . _('Passive') . '</a></div>';
    }
    $output .= '</td>';

    $output .= '</tr>';

    $output .= '
    </tbody>
    </table>
    ';

    if ($mode == DASHLET_MODE_INBOARD) {
        $output .= '
        <div class="ajax_date">' . _('Last Updated:') . ' ' . get_datetime_string(time()) . '</div>
        ';
    }

    return $output;
}


/**
 * @param   array   $args
 * @return  string
 */
function xicore_ajax_get_feature_status_tac_summary_html($args = null)
{
    $mode = grab_array_var($args, "mode");

    $flap_detection_enabled = 0;
    $active_checks_enabled = 0;
    $passive_checks_enabled = 0;
    $notifications_enabled = 0;
    $event_handlers_enabled = 0;

    // Get program status
    $xml = get_xml_program_status();
    if ($xml) {
        $flap_detection_enabled = intval($xml->programstatus->flap_detection_enabled);
        $active_checks_enabled = intval($xml->programstatus->active_service_checks_enabled);
        $passive_checks_enabled = intval($xml->programstatus->passive_service_checks_enabled);
        $notifications_enabled = intval($xml->programstatus->notifications_enabled);
        $event_handlers_enabled = intval($xml->programstatus->event_handlers_enabled);
    }

    // Get service status totals
    $backendargs = array(
        "totals" => 1,
        "is_active" => 1
    );

    // Flap detection?
    $backendargs["flap_detection_enabled"] = 0;
    $xml = get_xml_service_status($backendargs);
    $services_flap_detection_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["flap_detection_enabled"]);

    // Services flapping
    $backendargs["is_flapping"] = 1;
    $xml = get_xml_service_status($backendargs);
    $services_flapping = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["is_flapping"]);

    // Services notifications disabled
    $backendargs["notifications_enabled"] = 0;
    $xml = get_xml_service_status($backendargs);
    $services_notifications_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["notifications_enabled"]);

    // Services event handler disabled
    $backendargs["event_handler_enabled"] = 0;
    $xml = get_xml_service_status($backendargs);
    $services_event_handlers_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["event_handler_enabled"]);

    // Services active checks disabled
    $backendargs["active_checks_enabled"] = 0;
    $xml = get_xml_service_status($backendargs);
    $services_active_checks_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["active_checks_enabled"]);

    // Services passive checks disabled
    $backendargs["passive_checks_enabled"] = 0;
    $xml = get_xml_service_status($backendargs);
    $services_passive_checks_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["passive_checks_enabled"]);

    // Get host status totals
    $backendargs = array(
        "totals" => 1,
        "is_active" => 1
    );

    // Flap detection?
    $backendargs["flap_detection_enabled"] = 0;
    $xml = get_xml_host_status($backendargs);
    $hosts_flap_detection_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["flap_detection_enabled"]);

    // Hosts flapping
    $backendargs["is_flapping"] = 1;
    $xml = get_xml_host_status($backendargs);
    $hosts_flapping = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["is_flapping"]);

    // Hosts notifications disabled
    $backendargs["notifications_enabled"] = 0;
    $xml = get_xml_host_status($backendargs);
    $hosts_notifications_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["notifications_enabled"]);

    // Hosts event handler disabled
    $backendargs["event_handler_enabled"] = 0;
    $xml = get_xml_host_status($backendargs);
    $hosts_event_handlers_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["event_handler_enabled"]);

    // Hosts active checks disabled
    $backendargs["active_checks_enabled"] = 0;
    $xml = get_xml_host_status($backendargs);
    $hosts_active_checks_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["active_checks_enabled"]);

    // Hosts passive checks disabled
    $backendargs["passive_checks_enabled"] = 0;
    $xml = get_xml_host_status($backendargs);
    $hosts_passive_checks_disabled = ($xml) ? intval($xml->recordcount) : 0;
    unset($backendargs["passive_checks_enabled"]);

    // Begin building html output
    $output = '';

    $output .= '
    <table class="table table-condensed table-striped table-bordered table-no-margin">
    <thead>
    <tr><th colspan="10">' . _('Features') . '</th></tr>
    </thead>
    <tbody>
    ';

    $output .= '<tr class="tacSubHeader"><td colspan="2">' . _('Flap Detection') . '</td><td colspan="2">' . _('Notifications') . '</td><td colspan="2">' . _('Event Handlers') . '</td><td colspan="2">' . _('Active Checks') . '</td><td colspan="2">' . _('Passive Checks') . '</td></tr>';

    $output .= '<tr>';

    $process_status_url = get_base_url() . "includes/components/xicore/status.php?show=process";

    $status_url = get_base_url() . "includes/components/xicore/status.php";

    // Flap detection
    $output .= '<td><a href="' . $process_status_url . '"><img src="' . theme_image(($flap_detection_enabled == 0) ? "tacdisabled.png" : "tacenabled.png") . '"></a></td>';
    $output .= '<td width="135">';
    if ($flap_detection_enabled == 0) {
        $output .= '<div class="tacfeatureNoProblem">N/A</div>';
    } else {
        if ($services_flap_detection_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=services&serviceattr=256">' . $services_flap_detection_disabled . ' ' . _('Services Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Services Enabled') . '</div>';
        if ($services_flapping > 0)
            $output .= '<div class="tacfeatureProblem"><a href="' . $status_url . '?show=services&serviceattr=1024">' . $services_flapping . ' ' . _('Services Flapping') . '</a></div>';
        if ($hosts_flap_detection_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=hosts&hostattr=256">' . $hosts_flap_detection_disabled . ' ' . _('Hosts Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Hosts Enabled') . '</div>';
        if ($hosts_flapping > 0)
            $output .= '<div class="tacfeatureProblem"><a href="' . $status_url . '?show=hosts&hostattr=1024">' . $hosts_flapping . ' ' . _('Hosts Flapping') . '</a></div>';
    }
    $output .= '</td>';

    // Notifications
    $output .= '<td><a href="' . $process_status_url . '"><img src="' . theme_image(($notifications_enabled == 0) ? "tacdisabled.png" : "tacenabled.png") . '"></a></td>';
    $output .= '<td width="135">';
    if ($notifications_enabled == 0) {
        $output .= '<div class="tacfeatureNoProblem">N/A</div>';
    } else {
        if ($services_notifications_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=services&serviceattr=4096">' . $services_notifications_disabled . ' ' . _('Services Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Services Enabled') . '</div>';
        if ($hosts_notifications_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=hosts&hostattr=4096">' . $hosts_notifications_disabled . ' ' . _('Hosts Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Hosts Enabled') . '</div>';
    }
    $output .= '</td>';

    // Event handlers
    $output .= '<td><a href="' . $process_status_url . '"><img src="' . theme_image(($event_handlers_enabled == 0) ? "tacdisabled.png" : "tacenabled.png") . '"></a></td>';
    $output .= '<td width="135">';
    if ($event_handlers_enabled == 0) {
        $output .= '<div class="tacfeatureNoProblem">N/A</div>';
    } else {
        if ($services_event_handlers_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=services&serviceattr=64">' . $services_event_handlers_disabled . ' ' . _('Services Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Services Enabled') . '</div>';
        if ($hosts_event_handlers_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=hosts&hostattr=64">' . $hosts_event_handlers_disabled . ' ' . _('Hosts Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Hosts Enabled') . '</div>';
    }
    $output .= '</td>';

    // Active checks
    $output .= '<td><a href="' . $process_status_url . '"><img src="' . theme_image(($active_checks_enabled == 0) ? "tacdisabled.png" : "tacenabled.png") . '"></a></td>';
    $output .= '<td width="135">';
    if ($active_checks_enabled == 0) {
        $output .= '<div class="tacfeatureNoProblem">N/A</div>';
    } else {
        if ($services_active_checks_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=services&serviceattr=16">' . $services_active_checks_disabled . ' ' . _('Services Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Services Enabled') . '</div>';
        if ($hosts_active_checks_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=hosts&hostattr=16">' . $hosts_active_checks_disabled . ' ' . _('Hosts Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Hosts Enabled') . '</div>';
    }
    $output .= '</td>';

    // Passive checks
    $output .= '<td><a href="' . $process_status_url . '"><img src="' . theme_image(($passive_checks_enabled == 0) ? "tacdisabled.png" : "tacenabled.png") . '"></a></td>';
    $output .= '<td width="135">';
    if ($passive_checks_enabled == 0) {
        $output .= '<div class="tacfeatureNoProblem">N/A</div>';
    } else {
        if ($services_passive_checks_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=services&serviceattr=16384">' . $services_passive_checks_disabled . ' ' . _('Services Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Services Enabled') . '</div>';
        if ($hosts_passive_checks_disabled > 0)
            $output .= '<div class="tacfeatureNoProblem"><a href="' . $status_url . '?show=hosts&hostattr=16384">' . $hosts_passive_checks_disabled . ' ' . _('Hosts Disabled') . '</a></div>';
        else
            $output .= '<div class="tacfeatureNoProblem">' . _('All Hosts Enabled') . '</div>';
    }
    $output .= '</td>';

    $output .= '</tr>';

    $output .= '
    </tbody>
    </table>
    ';

    if ($mode == DASHLET_MODE_INBOARD) {
        $output .= '
        <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
        ';
    }

    return $output;
}
