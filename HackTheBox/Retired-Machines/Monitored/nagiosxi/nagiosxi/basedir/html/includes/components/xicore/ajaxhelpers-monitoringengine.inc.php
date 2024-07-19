<?php
//
// XI Core Ajax Helper Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


////////////////////////////////////////////////////////////////////////
// MONITORING ENGINE AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get the monitoring process status HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_monitoring_proc_html($args = null)
{
    if (!is_authorized_for_monitoring_system()) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get process status
    $xml = get_xml_program_status();

    $output = '';
    $output .= '<div class="infotable_title">' . _('Monitoring Engine Process') . '</div>';
    if ($xml == null) {
        $output .= _("No data");
    } else {
        $output .= '
        <table class="infotable table table-condensed table-striped table-bordered">
        <thead>
        <tr><th><div style="width: 50px;">' . _('Metric') . '</div></th>
        <th><div style="width: 50px;">' . _('Value') . '</div></th>
        <th><div style="width: 50px;">' . _('Action') . '</div></th></tr>
        </thead>
        <tbody>
        ';

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Process Info') . '</span></td></tr>';

        $programrunning = intval($xml->programstatus->is_currently_running);

        $programstateactions = "<ul class='horizontalactions'>\n";

        if ($programrunning == 1) {
            $programstateimg = "<img src='" . theme_image("enabled_small.png") . "' title='Running'>";
            $programstateactions .= "<li onClick='submit_command(" . COMMAND_NAGIOSCORE_STOP . ")'><a href='#' class='stop_nagioscore'><img src='" . theme_image("control_stop.png") . "' title='Stop'></a></li>\n";
            $programstateactions .= "<li onClick='submit_command(" . COMMAND_NAGIOSCORE_RESTART . ")'><a href='#' class='restart_nagioscore'><img src='" . theme_image("control_restart.png") . "' title='Restart'></a></li>\n";
        } else {
            $programstateimg = "<img src='" . theme_image("disabled_small.png") . "' title='Not Running'>";
            $programstateactions .= "<li onClick='submit_command(" . COMMAND_NAGIOSCORE_START . ")'><a href='#' class='start_nagioscore'><img src='" . theme_image("control_start.png") . "' title='Start'></a></li>\n";
        }
        $programstateactions .= "</ul>";

        $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Process State') . '</span></td><td class="text-center">' . $programstateimg . '</td><td class="text-center">' . $programstateactions . '</td></tr>';

        if ($programrunning == 0) {
            $endtime = get_datetime_string_from_datetime($xml->programstatus->program_end_time);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Process End Time') . '</span></td><td colspan="2">' . $endtime . '</td></tr>';
        } else {

            $starttime = get_datetime_string_from_datetime($xml->programstatus->program_start_time);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Process Start Time') . '</span></td><td colspan="2">' . $starttime . '</td></tr>';

            $runtime = get_duration_string(strval($xml->programstatus->program_run_time));
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Total Running Time') . '</span></td><td colspan="2">' . $runtime . '</td></tr>';

            $pid = intval($xml->programstatus->process_id);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Process ID') . '</span></td><td>' . $pid . '</td><td></td></tr>';

            $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Process Settings') . '</span></td></tr>';

            // Initialze some stuff we'll use a few times...
            $okcmd = array("command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND);
            $errcmd = array("command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND);

            // ACTIVE SERVICE CHECKS
            $v = intval($xml->programstatus->active_service_checks_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_EXECUTING_SVC_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_EXECUTING_SVC_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Active Service Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // PASSIVE SERVICE CHECKS
            $v = intval($xml->programstatus->passive_service_checks_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_ACCEPTING_PASSIVE_SVC_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_ACCEPTING_PASSIVE_SVC_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Passive Service Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // ACTIVE HOST CHECKS
            $v = intval($xml->programstatus->active_host_checks_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_EXECUTING_HOST_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_EXECUTING_HOST_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Active Host Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // PASSIVE HOST CHECKS
            $v = intval($xml->programstatus->passive_host_checks_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_ACCEPTING_PASSIVE_HOST_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_ACCEPTING_PASSIVE_HOST_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Passive Host Checks') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // NOTIFICATIONS
            $v = intval($xml->programstatus->notifications_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_DISABLE_NOTIFICATIONS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_ENABLE_NOTIFICATIONS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Notifications') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // EVENT HANDLERS
            $v = intval($xml->programstatus->event_handlers_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_DISABLE_EVENT_HANDLERS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_ENABLE_EVENT_HANDLERS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Event Handlers') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // FLAP DETECTION
            $v = intval($xml->programstatus->flap_detection_enabled);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_DISABLE_FLAP_DETECTION);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_ENABLE_FLAP_DETECTION);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Flap Detection') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // PERFORMANCE DATA
            $v = intval($xml->programstatus->process_performance_data);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_DISABLE_PERFORMANCE_DATA);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_ENABLE_PERFORMANCE_DATA);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Performance Data') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // OBSESS OVER SERVICES
            $v = intval($xml->programstatus->obsess_over_services);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_OBSESSING_OVER_SVC_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_OBSESSING_OVER_SVC_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Service Obsession') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';

            // OBSESS OVER HOSTS
            $v = intval($xml->programstatus->obsess_over_hosts);
            $okcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_STOP_OBSESSING_OVER_HOST_CHECKS);
            $errcmd["command_args"] = array("cmd" => NAGIOSCORE_CMD_START_OBSESSING_OVER_HOST_CHECKS);
            $output .= '<tr><td><span class="sysstat_stat_subtitle">' . _('Host Obsession') . '</span></td><td class="text-center">' . xicore_ajax_get_setting_status_html($v, false) . '</td><td class="text-center">' . xicore_ajax_get_setting_action_html($v, $okcmd, $errcmd) . '</td></tr>';
        }

        $output .= '
        </tbody>
        </table>';
    }

    $output .= '
    <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
    ';

    return $output;
}


/**
 * Get settings status HMTL
 *
 * @param   int     $x              Status int
 * @param   bool    $important      True if important or not
 * @return  string                  HTML output
 */
function xicore_ajax_get_setting_status_html($x, $important = true)
{
    $importantimage = "";
    if ($important == false)
        $importantimage = "_unimportant";

    if ($x == 1) {
        $img = theme_image("enabled_small.png");
        $imgtitle = "Enabled";
    } else if ($x == 0) {
        $img = theme_image("disabled" . $importantimage . "_small.png");
        $imgtitle = "Disabled";
    } else {
        $img = theme_image("unknown_small.gif");
        $imgtitle = "Unknown";
    }

    $output = "<img src='" . $img . "' title='" . $imgtitle . "'>";

    return $output;
}


/**
 * Get the action button/click event HTML based on status
 *
 * @param   int     $x          Status int
 * @param   string  $okcmd      Command for ok status 
 * @param   string  $errcmd     Command for error status
 * @return  string              HTML output
 */
function xicore_ajax_get_setting_action_html($x, $okcmd = null, $errcmd = null)
{
    if ($x == 1) {
        $img = theme_image("disable_small.png");
        $imgtitle = "Disable";
    } else if ($x == 0) {
        $img = theme_image("enable_small.gif");
        $imgtitle = "Enable";
    } else {
        return "";
    }

    $clickcmd = "";
    $cmdarr = null;

    if ($x == 1 && $okcmd != null)
        $cmdarr = $okcmd;
    if ($x == 0 && $errcmd != null)
        $cmdarr = $errcmd;

    if ($cmdarr != null) {
        switch ($cmdarr["command"]) {

            case COMMAND_NAGIOSCORE_SUBMITCOMMAND:
                $args = array();
                if ($cmdarr["command_args"] != null) {
                    foreach ($cmdarr["command_args"] as $var => $val) {
                        $args[$var] = $val;
                    }
                }
                $cmddata = json_encode($args);
                $clickcmd = "onClick='submit_command(" . COMMAND_NAGIOSCORE_SUBMITCOMMAND . "," . $cmddata . ")'";
                break;

            default:
                break;
        }
    }

    $output = "<a href='#' " . $clickcmd . "><img src='" . $img . "' title='" . $imgtitle . "'></a>";

    return $output;
}


/**
 * Get the monitoring engine status HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_monitoring_perf_html($args = null)
{
    if (!is_authorized_for_monitoring_system()) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get sysstat data
    $xml = get_xml_sysstat_data();

    $output = '';
    $output .= '<div class="infotable_title">' . _('Monitoring Engine Performance') . '</div>';
    if ($xml == null) {
        $output .= _("No data");
    } else {
        $output .= '
        <table class="infotable table table-condensed table-striped table-bordered">
        <thead>
        <tr><th><div style="width: 50px;">' . _('Metric') . '</div></th><th><div style="width: 50px;">' . _('Value') . '</div></th><th><div style="width: 105px;"></div></th></tr>
        </thead>
        <tbody>
        ';

        $max = 1;
        $v = intval($xml->nagioscore->activehostcheckperf->min_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostcheckperf->max_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostcheckperf->avg_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostcheckperf->min_execution_time);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostcheckperf->max_execution_time);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostcheckperf->avg_execution_time);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->min_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->max_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->avg_latency);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->min_execution_time);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->max_execution_time);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicecheckperf->avg_execution_time);
        if ($v > $max)
            $max = $v;

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Host Check Latency') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->min_latency, "Min", get_formatted_number($xml->nagioscore->activehostcheckperf->min_latency, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->max_latency, "Max", get_formatted_number($xml->nagioscore->activehostcheckperf->max_latency, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->avg_latency, "Avg", get_formatted_number($xml->nagioscore->activehostcheckperf->avg_latency, 2) . " sec", (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Host Check Execution Time') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->min_execution_time, "Min", get_formatted_number($xml->nagioscore->activehostcheckperf->min_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->max_execution_time, "Max", get_formatted_number($xml->nagioscore->activehostcheckperf->max_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostcheckperf->avg_execution_time, "Avg", get_formatted_number($xml->nagioscore->activehostcheckperf->avg_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Service Check Latency') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->min_latency, "Min", get_formatted_number($xml->nagioscore->activeservicecheckperf->min_latency, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->max_latency, "Max", get_formatted_number($xml->nagioscore->activeservicecheckperf->max_latency, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->avg_latency, "Avg", get_formatted_number($xml->nagioscore->activeservicecheckperf->avg_latency, 2) . " sec", (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Service Check Execution Time') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->min_execution_time, "Min", get_formatted_number($xml->nagioscore->activeservicecheckperf->min_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->max_execution_time, "Max", get_formatted_number($xml->nagioscore->activeservicecheckperf->max_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicecheckperf->avg_execution_time, "Avg", get_formatted_number($xml->nagioscore->activeservicecheckperf->avg_execution_time, 2) . " sec", (100 / $max), 100, 101, 101);


        $output .= '
        </tbody>
        </table>';
    }

    $output .= '
    <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
    ';

    return $output;
}


/**
 * Get the monitoring engine check status HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_monitoring_stats_html($args = null)
{
    if (!is_authorized_for_monitoring_system()) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get sysstat data
    $xml = get_xml_sysstat_data();

    $output = '';
    $output .= '<div class="infotable_title">' . _('Monitoring Engine Check Statistics') . '</div>';
    if ($xml == null) {
        $output .= _("No data");
    } else {
        $output .= '
        <table class="infotable table table-condensed table-striped table-bordered">
        <thead>
        <tr><th><div style="width: 50px;">' . _('Metric') . '</div></th><th><div style="width: 50px;">' . _('Value') . '</div></th><th><div style="width: 105px;"></div></th></tr>
        </thead>
        <tbody>
        ';

        $max = 1;
        $v = intval($xml->nagioscore->activehostchecks->val1);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostchecks->val5);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activehostchecks->val15);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passivehostchecks->val1);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passivehostchecks->val5);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passivehostchecks->val15);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicechecks->val1);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicechecks->val5);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->activeservicechecks->val15);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passiveservicechecks->val1);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passiveservicechecks->val5);
        if ($v > $max)
            $max = $v;
        $v = intval($xml->nagioscore->passiveservicechecks->val15);
        if ($v > $max)
            $max = $v;

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Active Host Checks') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostchecks->val1, "1-min", get_formatted_number($xml->nagioscore->activehostchecks->val1, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostchecks->val5, "5-min", get_formatted_number($xml->nagioscore->activehostchecks->val5, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activehostchecks->val15, "15-min", get_formatted_number($xml->nagioscore->activehostchecks->val15, 0), (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Passive Host Checks') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passivehostchecks->val1, "1-min", get_formatted_number($xml->nagioscore->passivehostchecks->val1, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passivehostchecks->val5, "5-min", get_formatted_number($xml->nagioscore->passivehostchecks->val5, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passivehostchecks->val15, "15-min", get_formatted_number($xml->nagioscore->passivehostchecks->val15, 0), (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Active Service Checks') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicechecks->val1, "1-min", get_formatted_number($xml->nagioscore->activeservicechecks->val1, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicechecks->val5, "5-min", get_formatted_number($xml->nagioscore->activeservicechecks->val5, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->activeservicechecks->val15, "15-min", get_formatted_number($xml->nagioscore->activeservicechecks->val15, 0), (100 / $max), 100, 101, 101);

        $output .= '<tr><td colspan="3"><span class="sysstat_stat_title">' . _('Passive Service Checks') . '</span></td></tr>';
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passiveservicechecks->val1, "1-min", get_formatted_number($xml->nagioscore->passiveservicechecks->val1, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passiveservicechecks->val5, "5-min", get_formatted_number($xml->nagioscore->passiveservicechecks->val5, 0), (100 / $max), 100, 101, 101);
        $output .= xicore_ajax_get_stat_bar_html($xml->nagioscore->passiveservicechecks->val15, "15-min", get_formatted_number($xml->nagioscore->passiveservicechecks->val15, 0), (100 / $max), 100, 101, 101);


        $output .= '
        </tbody>
        </table>';
    }

    $output .= '
    <div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
    ';

    return $output;
}


/**
 * Get the monitoring system event queue chart HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_eventqueue_chart_html($args = null)
{
    if (!is_authorized_for_monitoring_system()) {
        return _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    }

    // Get container
    $container = grab_array_var($args, "container");

    // Get the data
    $args = array(
        "brevity" => 1,
        "window" => 300,
        "bucket_size" => 10
    );
    $xml = get_timedeventqueuesummary_xml_output($args);
    $xml = simplexml_load_string($xml);

    if ($xml == null) {
        $output = "Error: No output from backend!";
    } else {

        $values = "";
        $n = 0;
        foreach ($xml->bucket as $b) {
            if ($n < 30) {
                if ($n > 0) {
                    $t = intval($b->eventtotals);
                    if ($n > 1) {
                        $values .= ",";
                    }
                    $values .= $t;
                }
                $n++;
            } else {
                break;
            }
        }

        $output = "
        <script type='text/javascript'>
        $(function () {
            $('#".$container."').highcharts({
                chart: {
                    type: 'column',
                    animation: false,
                    marginRight: 35
                },
                title: {
                    text: '"._('Last Updated').": ".get_datetime_string(time())."',
                    style: {
                        color: '#999',
                        fontSize: '10px'
                    }
                },
                credits: { enabled: false },
                tooltip: { enabled: false },
                legend: { enabled: false },
                exporting: { enabled: false },
                xAxis: {
                    categories: ['"._('Now')."','','','','','','','','','','','','','','','','','','','','+5 Min'],
                    tickInterval: 20,
                    tickmarkPlacement: 'on'
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    minRange: 8,
                    title: {
                        text: null
                    },
                    gridLineDashStyle: 'solid',
                    gridLineColor: '#EEE'
                },
                plotOptions: {
                    column: {
                        pointPadding: -0.33,
                        borderWidth: 0
                    }
                },
                series: [{
                    data: [".$values."],
                    animation: false
                }]
            });
        });
        </script>
        ";
    }

    return $output;
}
