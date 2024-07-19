<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and do prereq and auth checks
grab_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    global $request;

    if (isset($request['update']))
        do_updateprefs();
    else
        show_updateprefs();
    exit;
}


/**
 * Show the user's profile information when the user clicks on their name in the upper right.
 *
 * @param bool   $error
 * @param string $msg
 */
function show_updateprefs($error = false, $msg = "", $reset = false)
{
    // Check if we should display a user api update message
    if (!$error && $msg === "") {
        $backend_ticket = get_user_attr(0, 'backend_ticket');
        $api_key = get_user_attr(0, 'api_key');
        if ($backend_ticket == $api_key) {
            $msg = _("Your API Key hasn't been updated in a while! You should generate a new key.");
        }
    }

    // Get defaults
    $email = grab_request_var("email", get_user_attr(0, 'email'));
    $name = grab_request_var("name", get_user_attr(0, 'name'));
    $language = grab_request_var("defaultLanguage", get_user_meta(0, 'language'));
    $date_format = grab_request_var("defaultDateFormat", intval(get_user_meta(0, 'date_format')));
    $number_format = grab_request_var("defaultNumberFormat", intval(get_user_meta(0, 'number_format')));
    $week_format = grab_request_var("defaultWeekFormat", intval(get_user_meta(0, 'week_format')));
    $ignore_notice_update = grab_request_var("ignore_notice_update", get_user_meta(0, 'ignore_notice_update'));
    if ($ignore_notice_update != "on")
        $ignore_notice_update = 0;
    else
        $ignore_notice_update = 1;

    $api_key = grab_request_var("api_key", get_user_attr(0, "api_key"));
    $api_enabled = grab_request_var("api_enabled", get_user_attr(0, "api_enabled"));

    $insecure_login_enabled = grab_request_var("insecure_login_enabled", get_user_meta(0, "insecure_login_enabled", 0));
    $insecure_login_ticket = grab_request_var("insecure_login_ticket", get_user_meta(0, "insecure_login_ticket", random_string(64)));

    $show_login_alert_screen = grab_request_var("show_login_alert_screen", get_user_meta(0, "show_login_alert_screen", 1));
    $display_host_display_name = grab_request_var("display_host_display_name", get_user_meta(0, "display_host_display_name"));
    $display_service_display_name = grab_request_var("display_service_display_name", get_user_meta(0, "display_service_display_name"));
    
    $theme = grab_request_var("theme", get_user_meta(0, "theme"));
    $highcharts_default_type = grab_request_var("highcharts_default_type", get_highcharts_default_type(0));

    // Get global variables
    $languages = get_languages();
    $number_formats = get_number_formats();
    $date_formats = get_date_formats();
    $week_formats = get_week_formats();

    // Keep flash message if we reset the page (reload for style changes)
    if ($reset) {
        $_SESSION['msg']['message'] = _('Your account settings have been updated. The page may flash from the update.');
        keep_flash_message();
    }

    do_page_start(array("page_title" => _('Account Information')), true);
    ?>

    <script type="text/javascript">
    <?php if ($reset) { ?>
    window.parent.location.href="<?php echo get_base_url(); ?>account/";
    <?php } ?>
    </script>

    <h1><?php echo _('Account Information'); ?></h1>

    <?php
    display_message($error, false, $msg);
    ?>

    <form id="updateUserPreferencesForm" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>?page=<?php echo PAGE_ACCTINFO; ?>">
        <input type="hidden" name="update" value="1"/>
        <?php echo get_nagios_session_protector(); ?>

        <h5 class="ul"><?php echo _('General Account Settings'); ?></h5>

        <table class="updateUserPreferencesTable table table-condensed table-no-border" style="width: auto;">
            <tr>
                <td>
                    <label for="currentPassword"><?php echo _('Current Password'); ?>:</label>
                </td>
                <td>
                    <input type="password" style="width: 160px;" name="current_password" id="currentPassword" class="textfield form-control" <?php echo sensitive_field_autocomplete(); ?>>
                    <button type="button" style="vertical-align: top;" class="btn btn-sm btn-default tt-bind btn-show-password" title="<?php echo _("Show password"); ?>"><i class="fa fa-eye"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="passwordBox1"><?php echo _('New Password'); ?>:</label>
                </td>
                <td>
                    <input type="password" style="width: 160px;" name="password1" id="passwordBox1" class="textfield form-control" <?php echo sensitive_field_autocomplete(); ?>>
                    <button type="button" style="vertical-align: top;" class="btn btn-sm btn-default tt-bind btn-show-password" title="<?php echo _("Show password"); ?>"><i class="fa fa-eye"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="nameBox"><?php echo _('Name'); ?>:</label>
                </td>
                <td>
                    <input type="text" size="30" name="name" id="nameBox" value="<?php echo encode_form_val($name); ?>" class="textfield form-control">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="emailAddressBox"><?php echo _('Email Address'); ?>:</label>
                </td>
                <td>
                    <input type="text" size="30" name="email" id="emailAddressBox" value="<?php echo encode_form_val($email); ?>" class="textfield form-control">
                </td>
            </tr>
            <?php if (is_admin() || ($api_enabled)) { ?>
            <tr>
                <td>
                    <label for="apikey"><?php echo _('API Key'); ?>:</label>
                </td>
                <td>
                    <input type="text" size="70" onClick="this.select();" value="<?php echo encode_form_val($api_key); ?>" class="textfield form-control" readonly name="apikey" id="apikey">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="button" class="btn btn-xs btn-info" id="resetapikey" name="resetapikey" onclick="generate_new_api_key(<?php echo get_user_attr(0, 'user_id'); ?>);"><?php echo _("Generate new API key"); ?></button>
                </td>
            </tr>
            <?php } if (is_admin()) { ?>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="checkbox" id="api_enabled" name="api_enabled" <?php echo is_checked(checkbox_binary($api_enabled), 1); ?>> <?php echo _("Enable API Access"); ?></label>
                </td>
            </tr>
            <?php } ?>
        </table>

        <?php if (get_option('insecure_login', 0) && ($insecure_login_enabled || is_admin())) { ?>
        <h5 class="ul"><?php echo _('Insecure Login Settings'); ?></h5>

        <table class="table table-condensed table-no-border" style="width: auto;">
            <?php if (is_admin()) { ?>
            <tr>
                <td><label for="insecure_login_enabled"><?php echo _("Enable Insecure Login"); ?>:</label></td>
                <td class="checkbox">
                    <label>
                        <input type="checkbox" id="insecure_login_enabled" name="insecure_login_enabled" <?php echo is_checked($insecure_login_enabled, 1); ?> value="1">
                        <?php echo _("Allow using the insecure login ticket to authenticate this user. Use by passing the username and ticket in the URL parameters."); ?>
                    </label>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td><label for="insecure_login_ticket"><?php echo _("Insecure Login Ticket"); ?>:</label></td>
                <td>
                    <input type="text" size="70" onClick="this.select();" value="<?php echo $insecure_login_ticket; ?>" class="textfield form-control" readonly name="insecure_login_ticket" id="insecure_login_ticket">
                </td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo _('Example of usage:'); ?> <code><?php echo get_base_url(); ?>index.php?username=&lt;username&gt;&amp;ticket=&lt;insecure login ticket&gt;</code></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="button" class="btn btn-xs btn-info" id="resetticket" name="resetticket" onclick="generate_new_ticket(<?php echo get_user_attr(0, 'user_id'); ?>);"><?php echo _("Generate new insecure ticket"); ?></button>
                </td>
            </tr>
        </table>
        <?php } ?>

        <h5 class="ul"><?php echo _('Account Preferences'); ?></h5>

        <table class="updateUserPreferencesTable table table-condensed table-no-border" style="width: auto;">
            <tr>
                <td>
                    <label for="languageListForm"><?php echo _("Language"); ?>:</label>
                </td>
                <td>
                    <select name="language" id="languageListForm" class="languageListForm dropdown form-control">
                        <?php foreach ($languages as $lang => $title) { ?>
                            <option value="<?php echo $lang; ?>" <?php echo is_selected($language, $lang); ?>><?php echo get_language_nicename($title); ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?php echo _("User Interface Theme"); ?>:</label></td>
                <td>
                    <select id="theme" name="theme" class="dropdown form-control">
                        <option value=""<?php if ($theme == '') { echo " selected"; } ?>><?php echo _("Default"); ?></option>
                        <option value="xi5"<?php if ($theme == 'xi5') { echo " selected"; } ?>><?php echo _("Modern"); ?></option>
                        <option value="xi5dark"<?php if ($theme == 'xi5dark') { echo " selected"; } ?>><?php echo _("Modern Dark"); ?></option>
                        <option value="xi2014"<?php if ($theme == 'xi2014') { echo " selected"; } ?>><?php echo _("2014"); ?></option>
                        <option value="classic"<?php if ($theme == 'classic') { echo " selected"; } ?>><?php echo _("Classic"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><?php echo _("Default Type for Highcharts Graphs"); ?>:</label></td>
                <td>
                    <select id="highcharts_default_type" name="highcharts_default_type" class="form-control">
                        <option value="stacked"<?php if ($highcharts_default_type == "stacked") { echo " selected"; } ?>><?php echo _("Area (Stacked)"); ?></option>
                        <option value="area"<?php if ($highcharts_default_type == "area") { echo " selected"; } ?>><?php echo _("Area"); ?></option>
                        <option value="line"<?php if ($highcharts_default_type == "line") { echo " selected"; } ?>><?php echo _("Line"); ?></option>
                        <option value="spline"<?php if ($highcharts_default_type == "spline") { echo " selected"; } ?>><?php echo _("Spline"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="defaultDateFormat"><?php echo _("Date Format"); ?>:</label>
                </td>
                <td>
                    <select name="defaultDateFormat" class="dateformatList dropdown form-control">
                        <?php foreach ($date_formats as $id => $txt) { ?>
                            <option value="<?php echo $id; ?>" <?php echo is_selected($id, $date_format); ?>><?php echo $txt; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="defaultNumberFormat"><?php echo _("Number Format"); ?>:</label>
                </td>
                <td>
                    <select name="defaultNumberFormat" class="numberformatList dropdown form-control">
                        <?php foreach ($number_formats as $id => $txt) { ?>
                            <option value="<?php echo $id; ?>" <?php echo is_selected($id, $number_format); ?>><?php echo $txt; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="defaultWeekFormat"><?php echo _("Week Format"); ?>:</label>
                </td>
                <td>
                    <select name="defaultWeekFormat" class="weekFormatList dropdown form-control">
                        <?php foreach ($week_formats as $id => $txt) { ?>
                            <option value="<?php echo $id; ?>" <?php echo is_selected($id, $week_format); ?>><?php echo $txt; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="checkbox" id="show_login_alert_screenCheckBox" name="show_login_alert_screen" <?php echo is_checked(checkbox_binary($show_login_alert_screen), 1); ?>> <?php echo _("Show login alert screen"); ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="checkbox" id="display_host_display_name" name="display_host_display_name" <?php echo is_checked(checkbox_binary($display_host_display_name), 1); ?>> <?php echo _("Display host display names (replaces host name on host/service status and host status detail pages)"); ?></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="checkbox" id="display_service_display_name" name="display_service_display_name" <?php echo is_checked(checkbox_binary($display_service_display_name), 1); ?>> <?php echo _("Display service display names (replaces service name in service status and service status detail pages)"); ?></label>
                </td>
            </tr>
        </table>

        <div id="formButtons">
            <input type="submit" class="btn btn-sm btn-primary" name="updateButton" value="<?php echo _('Update Settings'); ?>" id="updateButton">
            <a href="" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></a>
        </div>

    </form>

    <?php
    do_page_end(true);
    exit();
}


/**
 * Does the actual updating of user profile data.
 */
function do_updateprefs()
{
    global $request;
    global $db_tables;

    check_nagios_session_protector();

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: main.php");
    }

    // Values used to "reset" the page if we change them
    $reset = false;
    $old_theme = get_user_meta(0, 'theme');
    $old_language = get_user_meta(0, 'language');

    $errmsg = array();
    $errors = 0;

    // Grab variables
    $current_password = grab_request_var("current_password", "");
    $password1 = trim(grab_request_var("password1", ""));
    $email = grab_request_var("email", "");
    $name = grab_request_var("name", "");
    $api_enabled = checkbox_binary(grab_request_var("api_enabled", 0));
    $default_language = grab_request_var("language", "");
    $date_format = grab_request_var("defaultDateFormat", DF_ISO8601);
    $number_format = grab_request_var("defaultNumberFormat", NF_2);
    $week_format = grab_request_var("defaultWeekFormat", WF_US);
    $show_login_alert_screen = checkbox_binary(grab_request_var("show_login_alert_screen", 0));
    $display_host_display_name = checkbox_binary(grab_request_var("display_host_display_name", 0));
    $display_service_display_name = checkbox_binary(grab_request_var("display_service_display_name", 0));
    $theme = grab_request_var("theme", "xi5");
    $highcharts_default_type = grab_request_var("highcharts_default_type", 'line');

    $insecure_login_enabled = grab_request_var("insecure_login_enabled", 0);
    $insecure_login_ticket = grab_request_var("insecure_login_ticket", "");

    // Get old email address
    $oldemail = get_user_attr(0, 'email');

    // Check for errors
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    // Make sure the theme is a real one
    if (!in_array($theme, array('', 'xi5', 'xi5dark', 'xi2014', 'classic'))) {
        $errmsg[$errors++] = _("Theme selected does not exist.");
    }

    // We cannot allow an empty login ticket
    if (get_option('insecure_login') && empty($insecure_login_ticket) && is_admin()) {
        $errmsg[$errors++] = _("You cannot have an empty insecure login ticket.");
    }

    // Should we change password or email?
    $changepass = false;
    if (have_value($password1) == true || $email != $oldemail) {
        // user has entered a password
        if (have_value($current_password) == true) {
            if (user_password_matches($_SESSION['username'], $current_password)) {
                if (have_value($password1)) {
                    $changepass = true;
                }
            } else {
                $errmsg[$errors++] = _("Current password does not match authentication records.");
            }
        } else {
            $errmsg[$errors++] = _("Please enter your current password to change password or email.");
        }
    }

    // Password matches and now we need to check if it's complex enough and
    // if the password has been used before
    if ($changepass == true) {
        if (strlen($password1) > 72) {
            $changepass = false;
            $errmsg[$errors++] = _("Password provided must be less than 72 characters long.");
        } else if (!is_valid_user_password(0, $password1)) {
            $changepass = false;
            $errmsg[$errors++] = _("Password provided was previously used. You must use a new password.");
        } else if (!password_meets_complexity_requirements($password1)) {
            $changepass = false;
            $errmsg[$errors++] = _("Specified password does not meet the complexity requirements.") . get_password_requirements_message();
        }
    }

    if (have_value($name) == false)
        $errmsg[$errors++] = _("Name is blank.");
    if (have_value($email) == false)
        $errmsg[$errors++] = _("Email address is blank.");
    else if (!valid_email($email))
        $errmsg[$errors++] = _("Email address is invalid.");

    // Handle errors
    if ($errors > 0) {
        show_updateprefs(true, $errmsg);
    }

    if ($changepass == true) {

        // Save the old password hash for later
        $old_pw_hash = get_user_attr(0, 'password');
        user_add_old_password(0, $old_pw_hash);

        // Change password
        change_user_attr(0, 'password', hash_password($password1));
        change_user_attr(0, 'last_password_change', time());
        submit_command(COMMAND_NAGIOSXI_SET_HTACCESS, serialize(array('username' => $_SESSION['username'], 'password' => $password1)));

        // Run the password change callback
        do_user_password_change_callback($_SESSION['user_id'], $password1);

    }

    // Change email
    if ($oldemail != $email) {
        change_user_attr(0, 'email', $email);
        update_nagioscore_contact($_SESSION['username'], array('email' => $email), false);
    }

    change_user_attr(0, 'name', $name);

    if (is_admin()) {
        change_user_attr(0, 'api_enabled', $api_enabled);
        set_user_meta(0, "insecure_login_enabled", $insecure_login_enabled);
    }

    // Set last update time/user (self)
    change_user_attr(0, 'last_edited', time());
    change_user_attr(0, 'last_edited_by', $_SESSION['user_id']);

    set_user_meta(0, 'language', $default_language);
    set_user_meta(0, "date_format", $date_format);
    set_user_meta(0, "number_format", $number_format);
    set_user_meta(0, "week_format", $week_format);
    set_user_meta(0, "show_login_alert_screen", $show_login_alert_screen);
    set_user_meta(0, "display_host_display_name", $display_host_display_name);
    set_user_meta(0, "display_service_display_name", $display_service_display_name);
    set_user_meta(0, "theme", $theme);
    set_user_meta(0, "highcharts_default_type", $highcharts_default_type);

    // Set session vars
    $_SESSION["language"] = $default_language;
    $_SESSION["date_format"] = $date_format;
    $_SESSION["number_format"] = $number_format;
    $_SESSION["week_format"] = $week_format;

    // Log it in audit log
    send_to_audit_log("User updated their account settings", AUDITLOGTYPE_CHANGE);
    if ($changepass == true) {
        send_to_audit_log("User changed their password", AUDITLOGTYPE_SECURITY);
    }

    // Set reset if the old language was different than new language
    // also reset the screen if the theme changed
    $global_theme = get_option('theme');
    if (($theme != '' && $theme != $global_theme) || $theme != $old_theme || $default_language != $old_language) {
        $reset = true;
    }

    if ($reset) {
        flash_message(_('Your account settings have been updated. The page may flash from the update.'));
    } else {
        flash_message(_('Your account settings have been updated.'));
    }

    show_updateprefs(false, '', $reset);
}


function draw_menu()
{
    $m = get_menu_items();
    draw_menu_items($m);
}