<?php
//
// Manage Mobile Carriers
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;

    if (isset($request['update'])) {
        do_update_settings();
    }

    if (isset($request['resetdefaults'])) {
        do_reset_defaults();
    } else {
        show_settings();
    }
}


function show_settings($error = false, $msg = "")
{

    // Get defaults
    $mpinfo = get_mobile_provider_info();

    // Get variables submitted to us
    $mp = grab_request_var("mp");
    if (is_array($mp)) {
        $mpinfo = $mp;
    }

    // Pre-process all carriers
    foreach ($mpinfo as $mpid => $mparr) {
        $id = grab_array_var($mparr, "id");
        $description = grab_array_var($mparr, "description");
        $format = grab_array_var($mparr, "format");
        $delete = grab_array_var($mparr, "delete");

        // Remove empty lines
        if ($id == "" && $description == "" && $format == "") {
            unset($mpinfo[$mpid]);
        }

        // Remove lines user wants deleted
        if ($delete != "") {
            unset($mpinfo[$mpid]);
            continue;
        }
    }

    do_page_start(array("page_title" => _('Mobile Carriers')), true);
?>

    <h1><?php echo _('Mobile Carriers'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p><?php echo _('Manage the mobile carrier settings that can be used for email-to-text mobile notifications. Note: The <code>%number%</code> macro in the address format will be replaced with the user\'s phone number.'); ?></p>

    <form id="manageMobileCarriersForm" method="post" action="mobilecarriers.php">

        <input type="hidden" name="update" value="1">
        <?php echo get_nagios_session_protector(); ?>

        <table class="table table-condensed table-no-border table-auto-width" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo _("Unique Id"); ?></th>
                    <th><?php echo _("Description"); ?></th>
                    <th><?php echo _("Email-To-Text Address Format"); ?></th>
                    <th><?php echo _("Delete"); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $x = 0;
            foreach ($mpinfo as $mparr) {
                echo "<tr>";
                echo "<td>" . ($x + 1) . "</td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][id]' value='" . encode_form_valq($mparr["id"]) . "'></td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][description]' value='" . encode_form_valq($mparr["description"]) . "'></td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][format]' value='" . encode_form_valq($mparr["format"]) . "' size='50'></td>";
                echo "<td><input type='checkbox' name='mp[" . $x . "][delete]'></td>";
                echo "</tr>";
                $x++;
            }
            for ($y = 0; $y < 2; $y++) {
                echo "<tr>";
                echo "<td>" . ($x + 1) . "</td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][id]' value=''></td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][description]' value=''></td>";
                echo "<td><input type='text' class='form-control' name='mp[" . $x . "][format]' value='' size='50'></td>";
                echo "</tr>";
                $x++;
            }
            ?>
            </tbody>
        </table>

        <div id="formButtons">
            <button type="submit" class="submitbutton btn btn-sm btn-primary" name="updateButton"><?php echo _('Update Settings'); ?></button>
            <button type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></button>
        </div>

    </form>

    <p><a href="?resetdefaults"><?php echo _("Restore defaults"); ?></a></p>

    <?php
    do_page_end(true);
    exit();
}


function do_update_settings()
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

    // Get variables submitted to us
    $mp = grab_request_var("mp");

    // Make sure we have requirements
    if (in_demo_mode() == true){
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    if (!is_array($mp)) {
        $errmsg[$errors++] = _("Could not process request");
    }

    foreach ($mp as $mpid => $mparr) {
        $id = grab_array_var($mparr, "id");
        $description = grab_array_var($mparr, "description");
        $format = grab_array_var($mparr, "format");
        $delete = grab_array_var($mparr, "delete");

        // Remove empty lines
        if ($id == "" && $description == "" && $format == "") {
            unset($mp[$mpid]);
            continue;
        }

        // Remove lines user wants deleted
        if ($delete != "") {
            unset($mp[$mpid]);
            continue;
        }

        if ($id == "") {
            $errmsg[$errors++] = _("Unique ID missing on line #") . $mpid;
        }
        if ($description == "") {
            $errmsg[$errors++] = _("Description missing on line #") . $mpid;
        }
        if ($format == "") {
            $errmsg[$errors++] = _("Format missing on line #") . $mpid;
        }
    }

    // Update settings
    if (in_demo_mode() == false) {
        set_option("mobile_provider_info", serialize($mp));
    }

    // Handle errors
    if ($errors > 0) {
        show_settings(true, $errmsg);
    }

    // Send a log to the audit log
    send_to_audit_log(_("User updated global mobile carrier settings"), AUDITLOGTYPE_CHANGE);

    show_settings(false, _('Mobile carriers updated.'));
}


function do_reset_defaults()
{
    if (in_demo_mode() == false) {
        $mpinfo = get_default_mobile_provider_info();
        set_option("mobile_provider_info", serialize($mpinfo));
    }
    show_settings(false, _("Defaults restored"));
}