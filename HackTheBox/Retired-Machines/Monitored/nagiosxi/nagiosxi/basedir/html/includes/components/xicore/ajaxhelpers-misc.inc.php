<?php
//
// XI Core Ajax Helper Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


////////////////////////////////////////////////////////////////////////
// MISC AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get the alert tray popup and icon HTML
 *
 * @return  string      HTML output
 */
function xicore_ajax_get_tray_alert_html()
{
    $critical_problem = false;
    $noncritical_problem = false;

    $html = "<ul>";

    $last_update_check_succeeded = get_option("last_update_check_succeeded");
    $update_available = get_option("update_available");

    $base_url = get_base_url();

    $hide_updates = false;
    if (custom_branding()) {
        global $bcfg;
        if ($bcfg['hide_updates']) {
            $hide_updates = true;
        }
    }

    if (!$hide_updates) {
        if ($update_available) {
            $html .= "<li><img src='" . theme_image("info_small.png") . "'> <a href='https://go.nagios.com/upgradexi' target='_blank'>" . sprintf(_("New %s release available!"), get_product_name()) . "</a></strong></li>";
            $noncritical_problem = true;
        } else if ($last_update_check_succeeded == 0 && is_admin() == true) {
            $html .= "<li><img src='" . theme_image("critical_small.png") . "'> <a href='" . $base_url . "admin/?xiwindow=updates.php'>" . _("Last update check failed") . "</a></li>";
            $noncritical_problem = true;
        }
    }

    // Get sysstat data
    $xml = get_xml_sysstat_data();
    if ($xml) {
        $problem = false;
        foreach ($xml->daemons->daemon as $d) {
            $status = intval($d->status);
            switch ($d["id"]) {
                case "nagioscore":
                    if ($status != SUBSYS_COMPONENT_STATUS_OK)
                        $problem = true;
                    break;
                case "pnp":
                    if ($status != SUBSYS_COMPONENT_STATUS_OK)
                        $problem = true;
                    break;
                default:
                    break;
            }
        }
        if ($problem == true) {
            $critical_problem = true;
            $html .= "<img src='" . theme_image("critical_small.png") . "'> <a href='" . $base_url . "admin/?xiwindow=sysstat.php'><b> " . _("System status degraded!") . "</b></a>";
        }
    }

    // Get process status
    $xml = get_xml_program_status();
    if ($xml) {

        // Active host checks
        $v = intval($xml->programstatus->active_host_checks_enabled);
        if ($v == 0) {
            $text = _("Active host checks are disabled");
            $img = theme_image("info_small.png");
            $html .= "<li><img src='" . $img . "' alt='" . $text . "' title='" . $text . "'> <a href='" . $base_url . "admin/?xiwindow=sysstat.php%3Fpageopt=monitoringengine'> " . $text . "</a></li>";
        }

        // Active service checks
        $v = intval($xml->programstatus->active_service_checks_enabled);
        if ($v == 0) {
            $text = _("Active service checks are disabled");
            $img = theme_image("info_small.png");
            $html .= "<li><img src='" . $img . "' alt='" . $text . "' title='" . $text . "'> <a href='" . $base_url . "admin/?xiwindow=sysstat.php%3Fpageopt=monitoringengine'> " . $text . "</a></li>";
        }

        // Notifications
        $v = intval($xml->programstatus->notifications_enabled);
        if ($v == 0) {
            $text = _("Notifications are disabled");
            $img = theme_image("info_small.png");
            $html .= "<li><img src='" . $img . "' alt='" . $text . "' title='" . $text . "'> <a href='" . $base_url . "admin/?xiwindow=sysstat.php%3Fpageopt=monitoringengine'> " . $text . "</a></li>";
        }
    }

    $html .= '</ul>';

    // Check for unhandled problems...
    $problemhtml = "";

    // Unhandled host problems
    $backendargs = array();
    $backendargs["cmd"] = "gethoststatus";
    $backendargs["limitrecords"] = false; // don't limit records
    $backendargs["totals"] = 1; // only get recordcount
    $backendargs["current_state"] = "in:1,2"; // down or unreachable
    $backendargs["problem_acknowledged"] = 0; // not acknowledged
    $backendargs["scheduled_downtime_depth"] = 0; // not in downtime
    $xml = get_xml_host_status($backendargs);
    if ($xml) {
        $total = intval($xml->recordcount);
        if ($total > 0) {
            $noncritical_problem = true;
            $problemhtml .= "<li><img src='" . theme_image("warning_small.png") . "'> <a href='" . $base_url . "/?xiwindow=" . urlencode("includes/components/xicore/status.php?show=hosts&hoststatustypes=12&hostattr=10") . "'>
                <b>" . $total . "</b> " . _("Unhandled host problems") . "</a></li>";
        }
    }

    // Unhandled service problems
    $backendargs = array();
    $backendargs["cmd"] = "getservicestatus";
    $backendargs["combinedhost"] = 1; // combined host status
    $backendargs["limitrecords"] = false; // don't limit records
    $backendargs["totals"] = 1; // only get recordcount
    $backendargs["host_current_state"] = "0"; // host up
    $backendargs["host_problem_acknowledged"] = 0; // host not acknowledged
    $backendargs["host_scheduled_downtime_depth"] = 0; // host not in downtime
    $backendargs["current_state"] = "in:1,2,3"; // non-ok state
    $backendargs["problem_acknowledged"] = 0; // not acknowledged
    $backendargs["scheduled_downtime_depth"] = 0; // not in downtime
    $xml = get_xml_service_status($backendargs);
    if ($xml) {
        $total = intval($xml->recordcount);
        if ($total > 0) {
            $noncritical_problem = true;
            $problemhtml .= "<li><img src='" . theme_image("warning_small.png") . "'> <a href='" . $base_url . "/?xiwindow=" . urlencode("includes/components/xicore/status.php?show=services&hoststatustypes=2&servicestatustypes=28&serviceattr=10") . "'>
                <b>" . $total . "</b> " . _("Unhandled service problems") . "</a></li>";
        }
    }

    if ($critical_problem == true || $noncritical_problem == true) {
        $html .= "<ul>";
        $html .= $problemhtml;
        $html .= "</ul>";
    } else {
        $html .= "<p>" . _("No problems detected.") . "</p>";
    }

    $html .= '<br>';
    $html .= '<div class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>';

    $html .= '<div id="tray_alerter_status" style="visibility: hidden;">';
    if ($critical_problem == true) {
        $html .= "<img src='" . theme_image("critical_small.png") . "' alt='Critical Problems Detected' title='"._('Critical Problems Detected')."'>";
    } else if ($noncritical_problem == true) {
        $html .= '<img src="' . theme_image("error.png") . '" class="spin-y" alt="Problems Detected" data-toggle="tooltip" data-placement="top" title="'._('Problems Detected').'">';
    } else {
        $html .= "<img src='" . theme_image("info_small.png") . "' alt='No Problems Detected' title='"._('Critical Problems Detected')."'>";
    }
    $html .= '</div>';

    return $html;
}


/**
 * Get login alert information HTML popup
 *
 * @return  string      HTML output
 */
function xicore_ajax_get_login_alert_popup_html()
{
    $html = "";

    $last_update_check_succeeded = get_option("last_update_check_succeeded");
    $update_available = get_option("update_available");

    $base_url = get_base_url();

    $hide_updates = false;
    if (custom_branding()) {
        global $bcfg;
        if ($bcfg['hide_updates']) {
            $hide_updates = true;
        }
    }

    // Check for updates
    if ($update_available && !$hide_updates) {
        $html .= "
    <strong><img src='" . theme_image("info_small.png") . "'> " . sprintf(_("New %s Release Available!"), get_product_name()) . "</strong>
    <br>" . sprintf(_("A new version of %s is available. The new version may have important security or bug fixes that should be applied to this server."), get_product_name()) . "<br><ul>";

        if (is_admin() == true)
            $html .= "<li><a href='" . $base_url . "admin/?xiwindow=updates.php'><b>" . _("See details") . "</b></a></li>";
        $html .= "<li><a href='https://go.nagios.com/upgradexi' target='_blank'><b>" . _("Download the latest version") . "</b></a></li>";
        $html .= "</ul>";
        $html .= "<hr>";
    } else if ($last_update_check_succeeded == 0) {
        $product = get_product_name();
        $html .= "
    <strong><img src='" . theme_image("critical_small.png") . "'> " . _("Update Check Failed.") . " </strong>
    <br>" . sprintf(_("The last update check failed. Make sure your %s server can access the Internet and check for program updates.
    Staying updated with the latest release of %s is important to preventing security breaches."), $product, $product) . "<br><ul>";
        if (is_admin() == true)
            $html .= "<li><a href='" . $base_url . "admin/?xiwindow=updates.php'><b>" . _("Try a manual update check") . "</b></a></li>";
        $html .= "</ul>";
        $html .= "<hr>";
    }

    // Check for miss-matched IP address/hostname
    if (is_admin()) {
        $server_ip_addr = $_SERVER['SERVER_ADDR'];

        // Get host/ip address from the internal url
        $host_or_ip = get_internal_url();
        preg_match('/:\/\/(.*)\//U', $host_or_ip, $clean);
        $host_or_ip = $clean[1];

        // Get IP from hostname if possible
        $could_not_resolve = false;
        if (!filter_var($host_or_ip, FILTER_VALIDATE_IP)) {
            $ip = gethostbyname($host_or_ip);
            if ($ip == $host_or_ip) {
                $could_not_resolve = true;
            } else {
                $host_or_ip = $ip;
            }
        }

        if ($could_not_resolve) {
            $html .= "
        <strong><img src='" . theme_image("info_small.png") . "'> " . _('Could not resolve Internal Program URL') . "</strong>
        <br>" . _('Nagios XI could not resolve the hostname in the Program URL field.') . "<ul>";
            $html .= "<li><a href='" . $base_url . "admin/?xiwindow=globalconfig.php'><b>" . _("View Setting") . "</b></a></li>";
            $html .= "</ul>";
            $html .= "<hr>";
        } else if ($host_or_ip != $server_ip_addr) {
            $html .= "
        <strong><img src='" . theme_image("info_small.png") . "'> " . _('IP Address Mismatch in Internal Program URL') . "</strong>
        <br>" . _('Nagios XI seems to be running on a differnt IP address than the one in the Program URL field.') . "<ul>";
            $html .= "<li><a href='" . $base_url . "admin/?xiwindow=globalconfig.php'><b>" . _("View Setting") . "</b></a></li>";
            $html .= "</ul>";
            $html .= "<hr>";
        }
    }

    // Get sysstat data
    $xml = get_xml_sysstat_data();
    if ($xml) {
        $problem = false;
        foreach ($xml->daemons->daemon as $d) {
            $status = intval($d->status);
            switch ($d["id"]) {
                case "nagioscore":
                    if ($status != SUBSYS_COMPONENT_STATUS_OK)
                        $problem = true;
                    break;
                case "pnp":
                    if ($status != SUBSYS_COMPONENT_STATUS_OK)
                        $problem = true;
                    break;
                default:
                    break;
            }
        }
        if ($problem == true) {
            $html .= "<strong><img src='" . theme_image("critical_small.png") . "'> " . _("System Status Degraded!") . "</strong>
            <br>" . sprintf(_("One or more critical components of %s has been stopped, is disabled, or has malfunctioned.
            This can cause problems with monitoring, notifications, reporting, and more. You should investigate this problem immediately"), get_product_name()) . ".<br>
            <ul>
            <li><a href='" . $base_url . "admin/?xiwindow=sysstat.php'><b>" . _("Check system status") . "</b></a></li>
            <li><a href='" . $base_url . "admin/?xiwindow=sysstat.php'><b>" . _("Check monitoring engine status") . "</b></a></li>
            </ul>
    <hr>";
        }
    }

    // Check for unhandled problems...
    $problem = false;
    $problemhtml = "";

    // Unhandled host problems
    $backendargs = array();
    $backendargs["cmd"] = "gethoststatus";
    $backendargs["limitrecords"] = false; // don't limit records
    $backendargs["totals"] = 1; // only get recordcount
    $backendargs["current_state"] = "in:1,2"; // down or unreachable
    $backendargs["problem_acknowledged"] = 0; // not acknowledged
    $backendargs["scheduled_downtime_depth"] = 0; // not in downtime
    $xml = get_xml_host_status($backendargs);
    if ($xml) {
        $total = intval($xml->recordcount);
        if ($total > 0) {
            $problem = true;
            $problemhtml .= "<li><a href='" . $base_url . "/?xiwindow=" . urlencode("includes/components/xicore/status.php?show=hosts&hoststatustypes=12&hostattr=10") . "'>
            <b>" . $total . " " . _("Unhandled Host Problems") . "</b></a></li>";
        }
    }

    // Unhandled service problems
    $backendargs = array();
    $backendargs["cmd"] = "getservicestatus";
    $backendargs["combinedhost"] = 1; // combined host status
    $backendargs["limitrecords"] = false; // don't limit records
    $backendargs["totals"] = 1; // only get recordcount
    $backendargs["host_current_state"] = "0"; // host up
    $backendargs["host_problem_acknowledged"] = 0; // host not acknowledged
    $backendargs["host_scheduled_downtime_depth"] = 0; // host not in downtime
    $backendargs["current_state"] = "in:1,2,3"; // non-ok state
    $backendargs["problem_acknowledged"] = 0; // not acknowledged
    $backendargs["scheduled_downtime_depth"] = 0; // not in downtime
    $xml = get_xml_service_status($backendargs);
    if ($xml) {
        $total = intval($xml->recordcount);
        if ($total > 0) {
            $problem = true;
            $problemhtml .= "<li><a href='" . $base_url . "/?xiwindow=" . urlencode("includes/components/xicore/status.php?show=services&hoststatustypes=2&servicestatustypes=28&serviceattr=10") . "'>
            <b>" . $total . " " . _("Unhandled Service Problems") . "</b></a></li>";
        }
    }

    if ($problem == true) {
        $html .= "<strong><img src='" . theme_image("critical_small.png") . "'> " . _("Unhandled Problems!") . "</strong>
        <br>" . _("There are one or more unhandled problems that require attention") . ".<br><ul>";
        $html .= $problemhtml;
        $html .= "</ul>";
        $html .= "<hr>";
    }

    return $html;
}


/**
 * Get top page alert content HTML
 *
 * @param   array   $args   Object arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_pagetop_alert_content_html($args = null)
{
    $admin = is_admin();
    $urlbase = get_base_url();

    $error = false;
    $warning = false;

    $output = "";

    // Get sysstat data
    $xml = get_xml_sysstat_data();

    if ($xml == null) {
        if ($admin) {
            $output .= "<a href='" . $urlbase . "admin/sysstat.php'>";
        }

        $text = _("Could not read program data!");
        $img = theme_image("critical_small.png");
        $output .= "<img src='" . $img . "'> " . $text;

        if ($admin) {
            $output .= "</a>";
        }
    } else {
        foreach ($xml->daemons->daemon as $d) {

            if ($admin) {
                $output .= "<a href='" . $urlbase . "admin/?xiwindow=" . urlencode("sysstat.php") . "'>";
            } else {
                $output .= "<span class='pop-row'>";
            }

            $text = "";

            $status = intval($d->status);

            switch ($status) {
                case SUBSYS_COMPONENT_STATUS_OK:
                    $img = theme_image("ok_small.png");
                    break;
                case SUBSYS_COMPONENT_STATUS_ERROR:
                    $img = theme_image("critical_small.png");
                    $error = true;
                    break;
                case SUBSYS_COMPONENT_STATUS_UNKNOWN:
                    $img = theme_image("unknown_small.png");
                    $warning = true;
                    break;
                default:
                    break;
            }

            switch ($d["id"]) {
                case "nagioscore":
                    $text = _("Monitoring engine");
                    if ($status == SUBSYS_COMPONENT_STATUS_OK)
                        $stext = _("Running");
                    else
                        $stext = _("Not running");
                    break;
                case "pnp":
                    $text = _("Performance grapher");
                    if ($status == SUBSYS_COMPONENT_STATUS_OK)
                        $stext = _("Running");
                    else
                        $stext = _("Not running");
                    break;
                default:
                    break;
            }

            $output .= "<img src='" . $img . "' alt='" . $stext . "' title='" . $stext . "'> " . $text;
            if ($admin) {
                $output .= "</a>";
            } else {
                $output .= "</span>";
            }

        }
    }

    // Get process status
    $xml = get_xml_program_status();
    if ($xml) {

        // Notifications
        $v = intval($xml->programstatus->notifications_enabled);
        if ($v == 0) {
            $text = _("Disabled");
            $img = theme_image("info_small.png");
        } else {
            $text = _("Enabled");
            $img = theme_image("ok_small.png");
        }

        if ($admin) {
            $output .= "<a href='" . $urlbase . "admin/?xiwindow=" . urlencode("sysstat.php?pageopt=monitoringengine") . "'>";
        } else {
            $output .= "<span class='pop-row'>";
        }
        $output .= "<img class='tt' src='" . $img . "' alt='" . $text . "' title='" . $text . "'> " . _('Notifications');
        if ($admin) {
            $output .= "</a>";
        } else {
            $output .= "</span>";
        }

        // Active host checks
        $v = intval($xml->programstatus->active_host_checks_enabled);
        if ($v == 0) {
            $text = _("Disabled");
            $img = theme_image("info_small.png");
        } else {
            $text = _("Enabled");
            $img = theme_image("ok_small.png");
        }

        if ($admin) {
            $output .= "<a href='" . $urlbase . "admin/?xiwindow=" . urlencode("sysstat.php?pageopt=monitoringengine") . "'>";
        } else {
            $output .= "<span class='pop-row'>";
        }
        $output .= "<img src='" . $img . "' alt='" . $text . "' title='" . $text . "'> " . _("Active host checks");
        if ($admin) {
            $output .= "</a>";
        } else {
            $output .= "</span>";
        }

        // Active service checks
        $v = intval($xml->programstatus->active_service_checks_enabled);
        if ($v == 0) {
            $text = _("Disabled");
            $img = theme_image("info_small.png");
        } else {
            $text = _("Enabled");
            $img = theme_image("ok_small.png");
        }

        if ($admin) {
            $output .= "<a href='" . $urlbase . "admin/?xiwindow=" . urlencode("sysstat.php?pageopt=monitoringengine") . "'>";
        } else {
            $output .= "<span class='pop-row'>";
        }
        $output .= "<img src='" . $img . "' alt='" . $text . "' title='" . $text . "'> " . _('Active service checks');
        if ($admin) {
            $output .= "</a>";
        } else {
            $output .= "</span>";
        }
        
    }

    $class = "ok";
    $t = _("System Ok") . ":&nbsp;";
    if ($error) {
        $class = "error";
        $t = _("System Problem") . ":&nbsp;";
    } else if ($warning) {
        $class = "warning";
        $t = _("System Problem") . ":&nbsp;";
    }
    $pre = "<div class='pagetopalert" . $class . "'>";

    $post = "";
    $post .= "</div>";


    $img = '<img src="'.theme_image("ok_small.png").'">';
    $text = _('System is OK');
    if ($error) {
        $text = _('System problems detected');
        $img = '<img src="'.theme_image("critical_small.png").'">';
    } else if ($warning) {
        $text = _('System warnings detected');
        $img = '<img src="'.theme_image("warning_small.png").'">';
    }

    $html = '<a id="topalert-popover" title="'.$text.'" data-placement="bottom" data-content="'.$output.'">'.$img.'</a>';

    $theme = get_theme();
    if ($theme == 'xi2014' || $theme == 'classic') {
        $html = _('System Status') . ': ' . $html;
    } 

    return $html;
}


/**
 * Get the XI news feed data HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_xi_news_feed_html($args = null)
{
    $url = "https://api.nagios.com/feeds/xipromo/";
    $update_news = false;
    $news = array();
    $output = "";

    $newsraw = get_meta(METATYPE_NONE, 0, "xinews");
    if ($newsraw == null || have_value($newsraw) == false) {
        $update_news = true;
    } else {
        $news = unserialize($newsraw);
        $now = time();
        if (($now - intval($news["time"])) > 60 * 60 * 24) {
            $update_news = true;
        }
    }

    if ($update_news) {
        $rss = simplexml_load_file($url);

        $newsitems = array();
        foreach ($rss->channel->item as $i) {
            $newsitems[] = array(
                "link" => strval($i->guid),
                "title" => strval($i->title),
                "description" => strval($i->description),
            );
        }

        // Cache news
        $news["time"] = time();
        $news["rss"] = json_encode($newsitems);
        set_meta(METATYPE_NONE, 0, "xinews", serialize($news));
        $newsitems_s = json_decode($news["rss"]);
        $newsitems = (array) $newsitems_s;
    } else {
        $news = unserialize($newsraw);
        $newsitems_s = json_decode($news["rss"]);
        $newsitems = (array) $newsitems_s;
    }

    $x = 0;
    foreach ($newsitems as $is) {
        $x++;
        if ($x > 3) {
            break; // Only grab the top 3
        }
        $i = (array)$is;
        $link = strval($i["link"]);
        $title = strval($i["title"]);
        $description = strval($i["description"]);
        $output .= "<tr><td><a href='" . $link . "' target='_blank'>" . $title . "</a><div>" . $description . "</div></td></tr>";
    }

    return $output;
}


/**
 * Get the available updates check HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_available_updates_html($args = null)
{
    // Check if we are going to force an update
    $force = false;
    if ($args != null) {
        if ($args['force'] == "yes") {
            $force = true;
        }
    }

    // Check for updates
    do_update_check($force);

    $update_info = array(
        "last_update_check_time" => get_option("last_update_check_time"),
        "last_update_check_succeeded" => get_option("last_update_check_succeeded"),
        "update_available" => get_option("update_available"),
        "update_version" => get_option("update_version"),
        "update_release_date" => get_option("update_release_date"),
        "update_release_notes" => get_option("update_release_notes"),
    );

    if ($update_info["last_update_check_succeeded"] == 0) {
        $update_str = "<p><div style='float: left; margin-right: 10px;'><img src='" . theme_image("unknown_small.png") . "'></div>
        <b>" . _("Update Check Problem: Last update check failed.") . "</b></p>";
    } else if ($update_info["update_available"] == 1) {
        $update_str = "<p><div style='float: left; margin-right: 10px;'><img src='" . theme_image("critical_small.png") . "'></div>
        <b>" . sprintf(_("A new %s update is available"), get_product_name()) . ".</b></p>";

        if ($update_info["update_release_notes"] != "")
            $update_str .= "<p>" . encode_form_val($update_info["update_release_notes"]);

        if (!custom_branding()) {
            $update_str .= "<p>" . _("Visit") . " <a href='https://www.nagios.com/products/nagiosxi/' target='_blank'>www.nagios.com</a> " . _("to obtain the latest update") . ".</p>";
        }
    } else {
        $update_str = "<p><div style='float: left; margin-right: 10px;'><img src='" . theme_image("ok_small.png") . "'></div>
        <b>" . sprintf(_("Your %s installation is up to date."), get_product_name()) . "</b></p>";
    }

    $output = '';

    $output .= '
    <div class="infotable-wrapper">
    <table class="infotable">
    <tbody>
    ';

    $output .= '<tr><td colspan="2">' . $update_str . '</td></tr>';

    $output .= '<tr><td>' . _('Latest Available Version') . ':</td><td>' . encode_form_val($update_info["update_version"]) . '</td></tr>';
    $output .= '<tr><td>' . _('Installed Version') . ':</td><td>' . get_product_version() . '</td></tr>';
    $output .= '<tr><td>' . _('Last Update Check') . ':</td><td>' . get_datetime_string($update_info["last_update_check_time"]) . '</td></tr>';

    $output .= '
    </tbody>
    </table>
    </div>
    ';

    $output .= '
    <div style="clear:both;" class="ajax_date">' . _('Last Updated') . ': ' . get_datetime_string(time()) . '</div>
    ';

    return $output;
}


/**
* Function to set specified user api key and return that value
*
* @param    array   $rgs    Contains user_id as element
* @return   string          New random api key
*/
function xicore_ajax_set_random_api_key($args)
{
    if (empty($args['user_id'])) {
        return "";
    }

    // Verify user identity
    if (!is_admin() && $_SESSION['user_id'] != $args['user_id']) {
        return "";
    }

    $user_id = $args['user_id'];

    if (!is_valid_user_id($user_id)) {
        return "";
    }

    change_user_attr($user_id, 'api_key', random_string(64));

    return get_user_attr($user_id, 'api_key');
}


/**
 * Sets the random ticket for the user
 *
 * @param    array   $rgs    Contains user_id as element
 * @return   string          New random ticket
 */
function xicore_ajax_set_random_ticket($args)
{
    if (empty($args['user_id'])) {
        return "";
    }

    $user_id = $args['user_id'];

    if (!is_valid_user_id($user_id)) {
        return "";
    }

    $ticket = random_string(64);
    set_user_meta($user_id, 'insecure_login_ticket', $ticket);

    return $ticket;
}
