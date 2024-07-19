<?php
//
// Scheduled Reporting Component
// Copyright (c) 2011-2018 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');
require_once(dirname(__FILE__) . '/../../common.inc.php');

$scheduledreporting_component_name = "scheduledreporting";
scheduledreporting_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function scheduledreporting_component_init()
{
    global $scheduledreporting_component_name;

    $versionok = scheduledreporting_component_checkversion();

    $desc = "";
    if (!$versionok) {
        $desc = "<br><b>Error: This component requires Nagios XI 5.2.1 or later.</b>";
    }

    $args = array(
        COMPONENT_NAME => $scheduledreporting_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Adds scheduled reporting capability to Nagios XI.") . $desc,
        COMPONENT_TITLE => _("Scheduled Reporting"),
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($scheduledreporting_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_REPORTS_ACTION_LINK, 'scheduledreporting_component_report_action');
        register_callback(CALLBACK_MENUS_INITIALIZED, 'scheduledreporting_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// VERSION CHECK FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


// New version requires XI 5 R1.0
function scheduledreporting_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }
    if (get_product_release() < 512) {
        return false;
    }
    return true;
}


///////////////////////////////////////////////////////////////////////////////////////////
// HELPER FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function scheduledreporting_component_get_report_options($rawurl)
{
    global $request;

    // Give source type (report or page)
    $opts = array(
        "source" => "report",
        "attachments" => array(),
    );

    // Strip out the names
    $urlparts = parse_url($rawurl);

    $path = $urlparts["path"];

    $theurl = $path;
    $theurl = str_replace("/" . get_base_uri(false) . "reports/", "", $theurl);
    $theurl = str_replace( get_base_uri(false) . "reports/", "", $theurl);
    $theurl = str_replace("reports/", "", $theurl);
    $theurl = str_replace("/" . get_base_uri(false) , "", $theurl);

    switch ($theurl) {
        // Variable attachment reports
        case "availability.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "Availability_Report.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "Availability_Report.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            $opts["attachments"]["csv"] = array(
                "type" => _("CSV (Combined)"),
                "file" => "Availability_Report.csv",
                "urlopts" => "mode=csv&csvtype=combined",
                "icon" => "fa-file-text-o"
            );
            $opts["attachments"]["csvhost"] = array(
                "type" => _("CSV (Host data only)"),
                "file" => "Host_Availability.csv",
                "urlopts" => "mode=csv&csvtype=host",
                "icon" => "fa-file-text-o"
            );
            $opts["attachments"]["csvservice"] = array(
                "type" => _("CSV (Service data only)"),
                "file" => "Service_Availability.csv",
                "urlopts" => "mode=csv&csvtype=service",
                "icon" => "fa-file-text-o"
            );
            break;

        // PDF-only reports
        case "execsummary.php":
            if (get_product_release() >= 304) {
                $opts["attachments"]["pdf"] = array(
                    "type" => _("PDF"),
                    "file" => "execsummary.pdf",
                    "urlopts" => "mode=pdf",
                    "icon" => "fa-file-pdf-o"
                );
                $opts["attachments"]["jpg"] = array(
                    "type" => _("JPG"),
                    "file" => "execsummary.jpg",
                    "urlopts" => "mode=jpg",
                    "icon" => "fa-file-image-o"
                );
            }
            break;
        case "alertheatmap.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "Heatmap.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "Heatmap.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        case "sla.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "SLA.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "SLA.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        case "includes/components/capacityplanning/capacityplanning.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "CapacityPlanning.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "CapacityPlanning.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        case "includes/components/nagiosna/nagiosna-reports.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "NetworkReport.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "NetworkReport.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        case "includes/components/nagiosna/nagiosna-queries.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "NetworkQueryReport.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "NetworkQueryReport.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        case "includes/components/nsti/nsti-queries.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "NSTI.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => "NSTI.jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            break;
        // PDF, CSV, and JPG reports
        case "statehistory.php":
        case "histogram.php":
        case "topalertproducers.php":
        case "notifications.php":
        case "eventlog.php":
        case "includes/components/bandwidthreport/index.php":
            $fname = scheduledreporting_component_get_report_fname($theurl);
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => $fname . ".pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["jpg"] = array(
                "type" => _("JPG"),
                "file" => $fname . ".jpg",
                "urlopts" => "mode=jpg",
                "icon" => "fa-file-image-o"
            );
            $opts["attachments"]["csv"] = array(
                "type" => _("CSV"),
                "file" => $fname . ".csv",
                "urlopts" => "mode=csv",
                "icon" => "fa-file-text-o"
            );
            break;
        // PDF and CSV only reports
        case "admin/auditlog.php":
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "auditlog.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o"
            );
            $opts["attachments"]["csv"] = array(
                "type" => _("CSV"),
                "file" => "auditlog.csv",
                "urlopts" => "mode=csv",
                "icon" => "fa-file-text-o"
            );
            break;
        default;
            $opts["attachments"]["pdf"] = array(
                "type" => _("PDF"),
                "file" => "page.pdf",
                "urlopts" => "mode=pdf",
                "icon" => "fa-file-pdf-o",
                "checked" => true
            );
            break;
    }

    return $opts;
}


function scheduledreporting_component_get_report_fname($url)
{
    $fname = $url;
    switch ($url) {
        case "statehistory.php":
            $fname = "StateHistory";
            break;
        case "histogram.php":
            $fname = "AlertHistogram";
            break;
        case "topalertproducers.php":
            $fname = "TopAlertProducers";
            break;
        case "notifications.php":
            $fname = "Notifications";
            break;
        case "eventlog.php":
            $fname = "EventLog";
            break;
        case "includes/components/bandwidthreport/index.php":
            $fname = "Bandwidth";
            break;
        default:
            break;
    }
    return $fname;
}


/**
 * Gets a array of all the scheduled reports that are in the system
 *
 * @return  array       An array of scheduled reports
 */
function scheduledreporting_component_get_all_reports()
{
    $scheduled_reports = array();

    // Get all the users and grab their reports
    $users = get_users();
    foreach ($users as $user) {
        $tmp = scheduledreporting_component_get_reports($user['user_id']);
        foreach ($tmp as $i => $t) {
            $tmp[$i]['user_id'] = $user['user_id'];
        }
        $scheduled_reports = array_merge($scheduled_reports, $tmp);
    }

    return $scheduled_reports;
}


/**
 * Get all the reports for a specific user or the current user that
 * is logged in (if user ID is set to 0)
 *
 * @param   int     $userid     Id of a user (or 0 for current logged in user)
 * @return  array               An array of scheduled reports
 */
function scheduledreporting_component_get_reports($userid = 0)
{
    $scheduled_reports = array();
    $temp = get_user_meta($userid, 'scheduled_reports');
    if ($temp != null)
        $scheduled_reports = mb_unserialize($temp);

    // Add user_id to reports
    if (empty($userid)) {
        $userid = $_SESSION['user_id'];
    }
    foreach ($scheduled_reports as $id => $report) {
        $scheduled_reports[$id]['user_id'] = $userid;
    }

    return $scheduled_reports;
}


function scheduledreporting_component_get_report_id($id = -1, $userid = 0)
{
    $scheduled_reports = scheduledreporting_component_get_reports($userid);
    if (!array_key_exists($id, $scheduled_reports))
        return null;

    return $scheduled_reports[$id];
}


function scheduledreporting_component_get_scheduled_report_url($id, $userid = 0)
{
    $report = scheduledreporting_component_get_report_id($id, $userid);
    if ($report == null)
        return null;

    $url = "";

    $bu = get_base_url();
    $rawurl = $report["url"];

    // full url - don't mess with it
    $r = strpos($rawurl, "http");
    if ($r == 0 && $r !== FALSE) {
        $url = $rawurl;
    } else {
        $rawurl = str_replace("/" . get_base_uri(false), "", $rawurl);
        $rawurl = str_replace(get_base_uri(false), "", $rawurl);
        $url = $bu . $rawurl;
    }
    return $url;
}


function scheduledreporting_component_delete_report($id, $userid = 0)
{
    $scheduled_reports = scheduledreporting_component_get_reports($userid);
    unset($scheduled_reports[$id]);
    scheduledreporting_component_save_reports($scheduled_reports, $userid);

    // update cron
    scheduledreporting_component_delete_cron($id, $userid);
}


/**
 * Adds a report to the user's scheduled reports
 *
 * @param   array   $report     An array of reporting options for a scheduled report
 * @param   int     $userid     The user ID of the scheduled report (0 for current user)
 * @return  string              ID of the report that was added
 */
function scheduledreporting_component_add_report($report, $userid = 0)
{
    $reports = scheduledreporting_component_get_reports($userid);
    
    $id = random_string(12);
    $reports[$id] = $report;

    scheduledreporting_component_save_reports($reports, $userid);

    return $id;
}


function scheduledreporting_component_save_reports($reports, $userid = 0)
{
    set_user_meta($userid, 'scheduled_reports', serialize($reports), false);
}


function scheduledreporting_component_update_cron($id, $userid = 0)
{
    $croncmd = scheduledreporting_component_get_cron_cmdline($id, $userid);
    $crontimes = scheduledreporting_component_get_cron_times($id, $userid);

    $cronline = sprintf("%s\t%s > /dev/null 2>&1\n", $crontimes, $croncmd);
    scheduled_reporting_component_log("UPDATE CRON: {$cronline}\n");
    $tmpfile = get_tmp_dir() . "/scheduledreport." . $id;
    file_put_contents($tmpfile, $cronline);

    $cmd = "crontab -l | grep -v " . escapeshellarg($croncmd) . " | cat - " . escapeshellarg($tmpfile) . " | crontab - ; rm -f " . escapeshellarg($tmpfile);
    //echo "<BR>CMD: $cmd<BR>";	
    exec($cmd, $output, $bool);
    scheduled_reporting_component_log("CMD: $cmd\nRET: $bool\nOUTPUT: " . implode("\n", $output));

    if ($bool > 0) {
        echo "ERROR: " . implode("<br />\n", $output);
    }
}


function scheduledreporting_component_delete_cron($id, $userid = 0)
{
    $croncmd = scheduledreporting_component_get_cron_cmdline($id, $userid);
    $cmd = "crontab -l | grep -v " . escapeshellarg($croncmd) . " | crontab -";
    //echo "<BR>CMD: $cmd<BR>";
    exec($cmd, $output, $bool);
    scheduled_reporting_component_log("CMD: $cmd\nRET: $bool\nOUTPUT: " . implode("\n", $output));

    if ($bool > 0) {
        echo "ERROR: " . implode("<br />\n", $output);
    }

}


function scheduledreporting_component_get_cron_cmdline($id, $userid = 0)
{
    $cmdline = scheduledreporting_component_get_cmdline($id, $userid);
    $cmd = $cmdline;
    return $cmd;
}


function scheduledreporting_component_get_cmdline($id, $userid = 0)
{
    $component_path = get_base_dir() . "/includes/components/scheduledreporting";
    $username = get_user_attr($userid, "username");
    $cmd = "/usr/bin/php " . $component_path . "/sendreport.php --report=" . escapeshellarg($id) . " --username=" . escapeshellarg($username);
    return $cmd;
}


function scheduledreporting_component_get_cron_times($id, $userid = 0)
{
    $times = "";

    $sr = scheduledreporting_component_get_report_id($id, $userid);
    if ($sr == null)
        return $times;

    $frequency = grab_array_var($sr, "frequency", "");

    $sched = grab_array_var($sr, "schedule", array());
    $hour = grab_array_var($sched, "hour", 0);
    $minute = grab_array_var($sched, "minute", 0);
    $ampm = grab_array_var($sched, "ampm", "AM");
    $dayofweek = grab_array_var($sched, "dayofweek", 0);
    $dayofmonth = grab_array_var($sched, "dayofmonth", 1);

    $h = intval($hour);
    $m = intval($minute);
    if (($ampm == "PM") && ($h < 12))
        $h += 12;
    if (($ampm == "AM") && ($h == 12))
        $h = 0;
    if ($frequency == "Monthly")
        $dom = $dayofmonth;
    else
        $dom = "*";
    if ($frequency == "Weekly")
        $dow = $dayofweek;
    else
        $dow = "*";

    $times = sprintf("%d %d %s * %s", $m, $h, $dom, $dow);

    return $times;
}


///////////////////////////////////////////////////////////////////////////////////////////
// MENU ITEMS
///////////////////////////////////////////////////////////////////////////////////////////


function scheduledreporting_component_addmenu()
{
    $desturl = get_component_url_base("scheduledreporting");

    $mi = find_menu_item(MENU_REPORTS, "menu-reports-sectionend-myreports", "id");
    if ($mi == null)
        return;

    $order = grab_array_var($mi, "order", "");
    if ($order == "")
        return;

    $reports = get_user_meta('0', 'scheduled_reports');
    ($reports) ? $reports = mb_unserialize($reports) : $reports = array();

    $num = count($reports);

    $neworder = $order + .01;

    // Add scheduled reports
    add_menu_item(MENU_REPORTS, array(
        "type" => "menusection",
        "title" => _("My Scheduled Reports"),
        "id" => "menu-reports-scheduledreportings",
        "order" => $neworder,
        "opts" => array(
            "id" => "scheduledreportings",
            "expanded" => true,
            "num" => $num,
            "url" => $desturl . '/schedulereport.php',
        )
    ));

    $neworder += 0.01;

    foreach ($reports as $key => $r) {
        if (empty($r['dontdisplay'])) {
            add_menu_item(MENU_REPORTS, array(
                "type" => MENULINK,
                "title" => encode_form_val($r['name']),
                "id" => "menu-reports-scheduledreporting-".uniqid(),
                "order" => $neworder,
                "opts" => array(
                    "href" => $desturl . "/schedulereport.php?visit=1&id=" . urlencode($key) . "&nsp=" . get_nagios_session_protector_id(),
                )));
            $neworder += .01;
        }
    }

    add_menu_item(MENU_REPORTS, array(
        "type" => "menusectionend",
        "id" => "menu-reports-sectionend-scheduledreportings",
        "order" => $neworder,
        "title" => "",
        "opts" => ""
    ));

    // Add management section if admin

    if (is_admin()) {
        add_menu_item(MENU_REPORTS, array(
            "type" => "link",
            "title" => _("Scheduled Reports"),
            "id" => "menu-reports-user-scheduled",
            "order" => 501,
            "opts" => array(
                "href" => $desturl . "/manage.php"
            )
        ));
        add_menu_item(MENU_REPORTS, array(
            "type" => "link",
            "title" => _("Scheduled Reports History"),
            "id" => "menu-reports-user-scheduled-history",
            "order" => 501.5,
            "opts" => array(
                "href" => $desturl . "/history.php"
            )
        ));
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// ACTION FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function scheduledreporting_component_report_action($cbtype, &$cbargs)
{
    global $request;

    $current_url = get_current_url();
    $base_url = get_base_url();
    // get report url (strip out protocol, ip, root)
    // NOTE: You can only use the relative url to http://server/nagiosxi because the IP address/protocol may be different when running a scheduled report from cron, compared to access the XI interface from the user's browser!
    $theurl = str_replace($base_url, "", $current_url);

    // should we even allow scheduling of this report?  only do so for reports we know about
    $show_link = true;
    $is_page = false;
    switch ($theurl) {
        case "reports/availability.php":
            $report_name = "Availability Report";
            break;
        case "reports/statehistory.php":
            $report_name = "State History Report";
            break;
        case "reports/topalertproducers.php":
            $report_name = "Top Alert Producers Report";
            break;
        case "reports/histogram.php":
            $report_name = "Histogram Report";
            break;
        case "reports/notifications.php":
            $report_name = "Notifications Report";
            break;
        case "reports/eventlog.php":
            $report_name = "Eventlog Report";
            break;
        case "reports/alertheatmap.php":
            $report_name = "Alert Heatmap Report";
            break;
        case "reports/execsummary.php":
            $report_name = "Executive Summary Report";
            break;
        case "reports/sla.php":
            $report_name = "SLA Report";
            break;
        case "includes/components/bandwidthreport/index.php":
            $report_name = "Bandwidth Report";
            break;
        case "includes/components/capacityplanning/capacityplanning.php":
            $report_name = "Capacity Planning Report";
            break;
        case "includes/components/nagiosna/nagiosna-reports.php":
            $report_name = "Network Report";
            break;
        case "includes/components/nagiosna/nagiosna-queries.php":
            $report_name = "Network Query Report";
            break;
        case "admin/auditlog.php":
            $report_name = "Audit Log";
            break;

        default;
            $show_link = false;
            $is_page = true;
            break;
    }

    //currently this does nothing...
    if ($show_link == false)
        return;

    $theurl .= "?";
    if ($is_page == true)
        $theurl .= "type=page";
    // add GET/POST args to url
    foreach ($request as $var => $val) {
        $theurl .= "&" . urlencode($var) . "=" . urlencode($val);
    }

    // Where should we direct people?
    $desturl = get_component_url_base("scheduledreporting", true) . "/schedulereport.php?name=$report_name";

    if ($is_page) {
        $desturl .= "&url=".$theurl;
        // Do something for page displays with report options?
    } else {
        $theme = get_theme();
        if ($theme == "xi2014" || $theme == "classic") {
            $title = _("Schedule this Report");
            $cbargs["actions"][] = "<a data-url='" . $desturl . "' class='btn-report-action' alt='" . $title . "' title='" . $title . "'><img src='" . theme_image("time.png") . "' border='0'></a>";
            $title = _("Email this Report");
            $cbargs["actions"][] = "<a data-url='" . $desturl . "&sendonce=1' class='btn-report-action' alt='" . $title . "' title='" . $title . "'><img src='" . theme_image("sendemail.png") . "' border='0'></a>";
        } else {
            $title = _("Schedule this Report");
            $cbargs["actions"][] = " <a data-url='" . $desturl . "' alt='" . $title . "' title='" . $title . "' class='btn btn-sm btn-default tt-bind btn-report-action' data-placement='bottom'><i class='fa fa-clock-o'></i></a>";
            $title = _("Email this Report");
            $cbargs["actions"][] = " <a data-url='" . $desturl . "&sendonce=1' alt='" . $title . "' title='" . $title . "' class='btn btn-sm btn-default tt-bind btn-report-action' data-placement='bottom'><i class='fa fa-envelope'></i></a>";
        }
    }

    return;
}


function scheduled_reporting_component_log($msg = '')
{
    global $cfg;
    $logfile = get_root_dir() . '/var/scheduledreporting.log';

    // Prepend time
    $msg = '[' . date('r') . '] ' . $msg;
    if (file_exists($logfile)) {
        file_put_contents($logfile, $msg, FILE_APPEND);
    }
}


function scheduled_reporting_log_report($reportname, $username, $error, $type, $runtype, $recipients)
{
    global $db_tables;

    // Set values before we enter into the db, error should be either 0 or 1 only
    $user_id = get_user_id($username);
    if ($error >= 1) {
        $error = 1;
    }

    // Enter the scheduled reporting log into the db
    $sql = "INSERT INTO ".$db_tables[DB_NAGIOSXI]['cmp_scheduledreports_log']." (report_name, report_run, report_user_id, report_status, report_type, report_run_type, report_recipients) VALUES (".escape_sql_param($reportname, DB_NAGIOSXI, true).", NOW(), ".intval($user_id).", ".intval($error).", ".intval($type).", ".intval($runtype).", ".escape_sql_param($recipients, DB_NAGIOSXI, true).");";
    $rs = exec_sql_query(DB_NAGIOSXI, $sql);
}


function get_scheduled_report_logs($user_id = 0, $offset = 0, $length = 0)
{
    global $db_tables;

    $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]['cmp_scheduledreports_log'];

    // Limit for users if user_id is not 0
    if (!empty($user_id)) {
        $sql .= " WHERE report_user_id = " . intval($user_id);
    }

    $sql .= ' ORDER BY report_run DESC';

    if (!empty($length)) {
        $sql .= " LIMIT " . intval($length) . " OFFSET " . intval($offset);
    }

    $rs = exec_sql_query(DB_NAGIOSXI, $sql);
    if ($rs) {
        return $rs->GetArray();
    }

    return false;
}


function get_scheduled_report_logs_count($user_id = 0)
{
    global $db_tables;
    $sql = "SELECT COUNT(*) AS total FROM ".$db_tables[DB_NAGIOSXI]['cmp_scheduledreports_log'];

    // Limit for users if user_id is not 0
    if (!empty($user_id)) {
        $sql .= " WHERE report_user_id = " . intval($user_id);
    }

    $rs = exec_sql_query(DB_NAGIOSXI, $sql);
    if ($rs) {
        $result = $rs->GetArray();
        return $result[0]['total'];
    }
    return 0;
}

/**
 * Validates report URLs to prevent PDF hacks
 *     - not empty
 *     - only allowed domains (uses get_allowed_domains())
 *     - path traversal (../..)
 *     - only allowed schemes
 * Doesn't validate URL format
 * 
 * @param string $url
 * @param $allowed_domains  can provide array of additional allowed domains
 * 
 * @return bool|string      false or 'relative', 'absolute' for valid
 */

function validate_url($url, $allowed_domains=array()) {
    global $cfg;
    $validate_url_disabled = grab_array_var($cfg, "validate_url_disabled", false);

    $system_domains = get_allowed_domains();
    $allowed_domains = array_merge($system_domains, $allowed_domains);

    // Check if URL is empty
    if (!$url) {
        return false;
    }

    $url_tmp = "";

    // Eliminate multiple encodings before validating
    do {
        $url = $url_tmp ?: $url;
        $url_tmp = urldecode($url);
    }
    while ($url_tmp != $url );

    // Check for path traversal attempts, /../
    if (preg_match("~(/[.]{2}|[.]{2}/)~", $url)) {
        return false;
    }

    $isRelative = true;
    if (preg_match("~^(\w+)://~", $url, $matches)) {

        // Invalid scheme check (e.g. ftp://, file:// etc.)
        if ($matches[1] != 'http' && $matches[1] != 'https') {
            return false;
        }

        // Check for valid domain
        if (!$validate_url_disabled) {
            $has_allowed = 0;
            foreach ($allowed_domains as $domain) {
                $has_allowed += strpos(' ' . $url, $domain) == 1 ? 1 : 0;
                if ($has_allowed) {
                    break;
                }
            }
            if (!$has_allowed) {
                return false;
            }
        }

        $isRelative = false;
    }

    return $isRelative ? "relative" : "absolute";
}

/**
 * Get allowed domains
 * Retrieves admin-specified (e.g. internal external) domains
 * Provides single change point if we want to expand allowable domains
 * 
 * @return array
 */
function get_allowed_domains() {
    global $cfg;

    $system_domains = array(get_internal_url());
    
    if (get_external_url()) {
        $system_domains[] = get_external_url();
    }

    // Allow default nagvis access based on scheme/host/port given
    foreach ($system_domains as $url) {
        $tmp = parse_url($url);
        $tmp_url = $tmp['scheme'].'://'.$tmp['host'];
        if ($tmp['port']) {
            $tmp_url .= ':'.$tmp['port'];
        }
        $tmp_url .= "/nagvis";
        $system_domains[] = $tmp_url;
    }

    // Add localhost (always okay)
    $system_domains[] = 'http://localhost/nagiosxi';
    $system_domains[] = 'http://localhost/nagvis';
    $system_domains[] = 'https://localhost/nagiosxi';
    $system_domains[] = 'https://localhost/nagvis';

    // Get any allowed domains from global config.inc.php
    $cfg_allowed_domains = grab_array_var($cfg, "validate_url_allowed_domains", array());
    if (is_array($cfg_allowed_domains)) {
        $system_domains = array_merge($system_domains, $cfg_allowed_domains);
    }

    return $system_domains;
}
