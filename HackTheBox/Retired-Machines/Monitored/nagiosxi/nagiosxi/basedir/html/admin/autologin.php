<?php
//
// Automatic Login Settings
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

// No cloud license access
if (is_v2_license_type('cloud')) {
    header("Location: main.php");
    exit();
}


route_request();


function route_request()
{
    global $request;

    if (isset($request['update'])) {
        do_update_options();
    } else {
        show_options();
    }
}


function show_options($error = false, $msg = "")
{

    do_page_start(array("page_title" => _('Automatic Login')), true);

    $opt_s = get_option("autologin_options");
    if ($opt_s == "") {
        $opts = array(
            "autologin_enabled" => 0,
            "autologin_username" => "",
            "autologin_password" => "",
        );
    } else {
        $opts = unserialize($opt_s);
    }

    $autologin_enabled = checkbox_binary(grab_request_var("autologin_enabled", grab_array_var($opts, "autologin_enabled")));
    $autologin_username = grab_request_var("autologin_username", grab_array_var($opts, "autologin_username"));
    $autologin_password = grab_request_var("autologin_password", "");
?>

    <h1><?php echo _("Automatic Login"); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p><?php echo _("These options allow you to configure a user account that should be used to automatically login visitors.  Visitors can logout of the default account and into their own if they wish."); ?></p>

    <form id="manageOptionsForm" method="post">

        <input type="hidden" name="options" value="1">
        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="update" value="1">

        <div class="checkbox">
            <label>
                <input type="checkbox" class="checkbox" name="autologin_enabled" <?php echo is_checked($autologin_enabled, 1); ?>>
                <?php echo _("Enable Auto-Login"); ?>
            </label>
        </div>

        <h5 class="ul"><?php echo _("Auto-Login Settings"); ?></h5>

        <table class="table table-condensed table-no-border table-auto-width">
            <tr>
                <td>
                    <label><?php echo _("Account"); ?>:</label>
                </td>
                <td>
                    <select name="autologin_username" class="form-control">
                        <option value=""></option>
                        <?php
                        $users = get_users();
                        foreach ($users as $u) {
                            echo "<option value='" . encode_form_val($u['username']) . "' " . is_selected($autologin_username, $u['username']) . ">" . encode_form_val($u['username']) . " (" . encode_form_val($u['name']) . ")</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>

        <div id="formButtons">
            <button type="submit" class="submitbutton btn btn-sm btn-primary" name="updateButton"><?php echo _('Update Settings'); ?></button>
            <button type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton" value="cancel"><?php echo _('Cancel'); ?></button>
        </div>

    </form>

    <?php

    do_page_end(true);
    exit();
}


function do_update_options()
{
    global $request;

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: main.php");
		return;
    }

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Get values
    $autologin_enabled = checkbox_binary(grab_request_var("autologin_enabled"));
    $autologin_username = grab_request_var("autologin_username");
    $autologin_password = grab_request_var("autologin_password");


    // Make sure we have requirements
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }
    if ($autologin_enabled == 1) {
        if (have_value($autologin_username) == false) {
            $errmsg[$errors++] = _("No account specified");
        } else if (is_valid_user($autologin_username) == false) {
            $errmsg[$errors++] = _("Invalid user account");
        }
    }

    // Handle errors
    if ($errors > 0) {
        show_options(true, $errmsg);
    }

    // Original options (for auditing)
    $opts_s = get_option("autologin_options");
    $opts = unserialize($opts_s);
    if (is_array($opts)) {
        $old_enabled = $opts["autologin_enabled"];
    } else {
        $old_enabled = 0;
    }

    // Save options
    $opts = array(
        "autologin_enabled" => $autologin_enabled,
        "autologin_username" => $autologin_username,
        "autologin_password" => $autologin_password,
    );
    $opts_s = serialize($opts);
    set_option("autologin_options", $opts_s);

    // Log it i nthe audit log
    if ($autologin_enabled == 0) {
        send_to_audit_log("User disabled auto-login functionality", AUDITLOGTYPE_SECURITY);
    } else if ($old_enabled != $autologin_enabled) {
        send_to_audit_log(_("User enabled auto-login functionality. Auto-login user='") . $autologin_username . "'", AUDITLOGTYPE_SECURITY);
    } else {
        send_to_audit_log(_("User updated auto-login functionality. Auto-login user='") . $autologin_username . "'", AUDITLOGTYPE_SECURITY);
    }

    show_options(false, _("Options updated."));
}