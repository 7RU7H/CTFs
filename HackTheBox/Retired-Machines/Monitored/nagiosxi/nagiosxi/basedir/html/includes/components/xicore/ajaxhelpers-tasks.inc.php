<?php
//
// XI Core Ajax Helper Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


////////////////////////////////////////////////////////////////////////
// TASK AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get admin task list HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_admin_tasks_html($args = null)
{
    $output = '';

    if (!is_admin()) {
        return _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    }

    $output .= '<div class="infotable_title">' . _('Administrative Tasks') . '</div>';

    $output .= '
    <table class="infotable table table-condensed table-striped table-bordered">
    <thead>
    <tr><th>' . _('Task') . '</th></tr>
    </thead>
    <tbody>
    ';

    $base_url = get_base_url();
    $admin_base = $base_url . "admin/";
    $config_base = $base_url . "config/";

    // Check for problems
    $problemoutput = "";

    nagiosql_check_setuid_files($scripts_ok, $goodscripts, $badscripts);
    nagiosql_check_file_perms($config_ok, $goodfiles, $badfiles);
    if ($scripts_ok == false || $config_ok == false) {
        $problemoutput .= "<li><a href='" . $admin_base . "?xiwindow=configpermscheck.php' target='_top'><b>" . _('Fix permissions problems') . "</b></a><br>" . _('One or more configuration files or scripts has incorrect settings, which will cause configuration changes to fail.') . "</li>";
    }

    if (!empty($problemoutput)) {
        $output .= "<tr><td><span class='infotable_subtitle'><img src='" . theme_image("error_small.png") . "'> " . _('Problems Needing Attention:') . "</span></td></tr>";
        $output .= "<tr><td>";
        $output .= "<ul>";
        $output .= $problemoutput;
        $output .= "</ul>";
        $output .= "</td></tr>";
    }

    // Check for setup tasks that need to be done
    $setupoutput = "";

    $opt = get_option("system_settings_configured");
    if ($opt != 1) {
        $setupoutput .= "<li><a href='" . $admin_base . "?xiwindow=globalconfig.php' target='_top'><b>" . _('Configure system settings') . "</b></a><br>" . _('Configure basic settings for your system.') . "</li>";
    }

    $opt = get_option("mail_settings_configured");
    if ($opt != 1) {
        $setupoutput .= "<li><a href='" . $admin_base . "?xiwindow=mailsettings.php' target='_top'><b>" . _('Configure mail settings') . "</b></a><br>" . _('Configure email settings for your  system.') . "</li>";
    }

    if (!empty($setupoutput)) {
        $output .= "<tr><td><span class='infotable_subtitle'>" . _('Initial Setup Tasks') . ":</span></td></tr>";
        $output .= "<tr><td>";
        $output .= "<ul>";
        $output .= $setupoutput;
        $output .= "</ul>";
        $output .= "</td></tr>";
    }

    // Check for important tasks that need to be done
    $alertoutput = "";

    // Check for custom branding and if they can do updates
    $hide_updates = false;
    if (custom_branding()) {
        global $bcfg;
        if ($bcfg['hide_updates']) {
            $hide_updates = true;
        }
    }

    if (!is_v2_license() && !$hide_updates) {
        $update_info = array("last_update_check_succeeded" => get_option("last_update_check_succeeded"),
                             "update_available" => get_option("update_available"));
        $updateurl = get_base_url() . "admin/?xiwindow=updates.php";
        if ($update_info["last_update_check_succeeded"] == 0) {
            $alertoutput .= "<li><div style='float: left; margin-right: 5px;'><img src='" . theme_image("unknown_small.png") . "'></div>" . _('The last') . " <a href='" . $updateurl . "' target='_top'>" . _('update check failed') . "</a>.</li>";
        } else if ($update_info["update_available"] == 1) {
            $alertoutput .= "<li><div style='float: left; margin-right: 5px;'><img src='" . theme_image("critical_small.png") . "'></div>" . _('A new Nagios XI') . " <a href='" . $updateurl . "' target='_top'>" . _('update is available') . "</a>.</li>";
        }
    }

    if ($alertoutput != "") {
        $output .= "<tr><td><span class='infotable_subtitle'>" . _('Important Tasks') . ":</span></td></tr>";
        $output .= "<tr><td>";
        $output .= "<ul style='list-style-type: none;'>";
        $output .= $alertoutput;
        $output .= "</ul>";
        $output .= "</td></tr>";
    }

    $output .= "<tr><td><span class='infotable_subtitle'>" . _('Ongoing Tasks') . ":</span></td></tr>";
    $output .= "<tr><td>";
    $output .= "<ul>";
    $output .= "<li><a href='" . $config_base . "' target='_top'>" . _('Configure your monitoring setup') . "</a><br>" . _('Add or modify items to be monitored') . ".</li>";
    $output .= "<li><a href='" . $admin_base . "?xiwindow=users.php' target='_top'>" . _('Add new user accounts') . "</a><br>" . sprintf(_('Setup new users with access to %s.'), get_product_name()) . "</li>";
    $output .= "</ul>";
    $output .= "</td></tr>";

    $output .= '
    </tbody>
    </table>
    ';

    return $output;
}


/**
 * Get the getting started tasks HTML
 *
 * @param   array   $args   Arguments
 * @return  string          HTML output
 */
function xicore_ajax_get_getting_started_html($args = null)
{
    $output = '<div class="infotable_title">' . _('Getting Started Guide') . '</div>';

    $output .= '
    <table class="infotable table table-condensed table-striped table-bordered">
    <tbody>';

    $base_url = get_base_url();
    $account_base = $base_url . "account/";
    $config_base = $base_url . "config/";

    $product_url = get_product_portal_backend_url();

    $output .= "<tr><td><span class='infotable_subtitle'>" . _('Common Tasks') . ":</span></td></tr>";
    $output .= "<tr><td>";
    $output .= "<ul>";
    $output .= "<li><a href='" . $account_base . "' target='_top'>" . _('Change your account settings') . "</a><br>" . _('Change your account password and general preferences') . ".</li>";
    $output .= "<li><a href='" . $account_base . "?xiwindow=notifyprefs.php' target='_top'>" . _('Change your notifications settings') . "</a><br>" . _('Change how and when you receive alert notifications') . ".</li>";
    $output .= "<li><a href='" . $config_base . "' target='_top'>" . _('Configure your monitoring setup') . "</a><br>" . _('Add or modify items to be monitored with easy-to-use wizards') . ".</li>";
    $output .= "</ul>";
    $output .= "</td></tr>";

    if (!custom_branding()) {
        $output .= "<tr><td><span class='infotable_subtitle'>" . _('Getting Started') . ":</span></td></tr>";
        $output .= "<tr><td>";
        $output .= "<ul>";
        $output .= "<li><a href='" . $product_url . "' target='_blank'><b>" . _('Learn about XI') . "</b></a><br>" . _('Learn more about XI and its capabilities') . ".</li>";
        $output .= "<li><a href='" . $product_url . "#stayinformed' target='_blank'><b>" . _('Signup for XI news') . "</b></a><br>" . _('Stay informed on the latest updates and happenings for XI') . ".</li>";
        $output .= "</ul>";
        $output .= "</td></tr>";
    }

    $output .= '</tbody>
    </table>';

    return $output;
}
