<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
decode_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    global $request;

    if (isset($request['update'])) {
        do_updateprefs();
    } else {
        show_updateprefs();
    }
    exit;
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_updateprefs($error = false, $msg = "")
{
    global $request;
    global $notificationmethods;

    // Check contact details
    $contact_name = get_user_attr(0, "username");
    $arr = get_user_nagioscore_contact_info($contact_name);
    $is_nagioscore_contact = $arr["is_nagioscore_contact"]; // Is the user a Nagios Core contact?
    $has_nagiosxi_commands = $arr["has_nagiosxi_commands"]; // Does the contact have XI notification commands?

    // Defaults
    $host_email_subject = get_user_host_email_notification_subject(0);
    $host_email_message = get_user_host_email_notification_message(0);
    $service_email_subject = get_user_service_email_notification_subject(0);
    $service_email_message = get_user_service_email_notification_message(0);

    $host_mobiletext_subject = get_user_host_mobiletext_notification_subject(0);
    $host_mobiletext_message = get_user_host_mobiletext_notification_message(0);
    $service_mobiletext_subject = get_user_service_mobiletext_notification_subject(0);
    $service_mobiletext_message = get_user_service_mobiletext_notification_message(0);

    // Default messages
    $default_host_email_subject = get_user_host_email_notification_subject(0, true);
    $default_host_email_message = get_user_host_email_notification_message(0, true);
    $default_service_email_subject = get_user_service_email_notification_subject(0, true);
    $default_service_email_message = get_user_service_email_notification_message(0, true);

    $default_host_mobiletext_subject = get_user_host_mobiletext_notification_subject(0, true);
    $default_host_mobiletext_message = get_user_host_mobiletext_notification_message(0, true);
    $default_service_mobiletext_subject = get_user_service_mobiletext_notification_subject(0, true);
    $default_service_mobiletext_message = get_user_service_mobiletext_notification_message(0, true);

    // Are settings locked for this non-adminsitrative user?
    $lock_notifications = (is_null(get_user_meta(0, 'lock_notifications')) || is_admin()) ? false : get_user_meta(0, 'lock_notifications');

    do_page_start(array("page_title" => _('Notification Messages')), true);
?>

    <h1><?php echo _('Notification Messages'); ?></h1>

    <?php
    if ($lock_notifications) {
        $nmsg = array();
        $nmsg[] = '<div><i class="fa fa-exclamation-triangle"></i> <strong>'._('Alert').'!</strong> '._('Notification settings have been locked for your account').'.</div>';
        display_message(true, false, $nmsg);
    }
    ?>

    <script type="text/javascript">
    var default_host_email_subject = <?php echo json_encode($default_host_email_subject);?>;
    var default_host_email_message = <?php echo json_encode($default_host_email_message);?>;
    var default_service_email_subject = <?php echo json_encode($default_service_email_subject);?>;
    var default_service_email_message = <?php echo json_encode($default_service_email_message);?>;

    var default_host_mobiletext_subject = <?php echo json_encode($default_host_mobiletext_subject);?>;
    var default_host_mobiletext_message = <?php echo json_encode($default_host_mobiletext_message);?>;
    var default_service_mobiletext_subject = <?php echo json_encode($default_service_mobiletext_subject);?>;
    var default_service_mobiletext_message = <?php echo json_encode($default_service_mobiletext_message);?>;

    $(document).ready(function () {
        $("#resetemail").click(function () {
            $("#host_email_subject").val(default_host_email_subject);
            $("#host_email_message").val(default_host_email_message);
            $("#service_email_subject").val(default_service_email_subject);
            $("#service_email_message").val(default_service_email_message);
        });
        $("#resetmobiletext").click(function () {
            $("#host_mobiletext_subject").val(default_host_mobiletext_subject);
            $("#host_mobiletext_message").val(default_host_mobiletext_message);
            $("#service_mobiletext_subject").val(default_service_mobiletext_subject);
            $("#service_mobiletext_message").val(default_service_mobiletext_message);
        });
        <?php if ($lock_notifications) { ?>
        $('#updateNotificationPreferencesForm input').attr('disabled', 'disabled');
        $('#updateNotificationPreferencesForm textarea').attr('disabled', 'disabled');
        <?php } ?>
    });
    </script>

    <?php
    // Warn user about notifications being disabled
    if (get_user_meta(0, 'enable_notifications') == 0) {
        $nmsg = array();
        $nmsg[] = '<div><i class="fa fa-exclamation-triangle"></i> <strong>'._('Alert').'!</strong> '._('You currently have notifications disabled for your account').'. <a href="notifyprefs.php">'._('Change your settings').'</a> <i class="fa fa-arrow-circle-right"></i> '._('if you would like to receive alerts').'.</div>';
        display_message(true, false, $nmsg);
    }

    if ($is_nagioscore_contact == false || $has_nagiosxi_commands == false) {
        $error = $arr['error'];
        $msg = $arr['is_nagioscore_contact_message'] . $arr['has_nagiosxi_commands_message'];
    }

    display_message($error, false, $msg);

    if ($is_nagioscore_contact == false) {
        echo _("Management of notification preferences is not available because your account is not configured to be a monitoring contact. Contact your system administrator for details.");
    }

    if ($has_nagiosxi_commands == false) {
        echo _("Management of notification preferences is not available for your account. Contact your system administrator for details.");
    } else {
    ?>

        <p><?php echo _("Use this feature to customize the content of the notification messages you receive."); ?></p>

        <form id="updateNotificationPreferencesForm" method="post" action="">
            
            <input type="hidden" name="update" value="1">
            <?php echo get_nagios_session_protector(); ?>

            <?php
            // Get additional tabs
            $cbdata = array(
                "tabs" => array()
            );
            do_callbacks(CALLBACK_USER_NOTIFICATION_MESSAGES_TABS_INIT, $cbdata);
            $customtabs = grab_array_var($cbdata, "tabs", array());
            ?>

            <script type="text/javascript">
            $(document).ready(function() {
                $("#tabs").tabs().show();
            });
            </script>

            <div id="tabs" class="hide">
                <ul>
                    <li><a href="#tab-email"><i class="fa fa-envelope"></i> <?php echo _('Email'); ?></a></li>
                    <li><a href="#tab-sms"><i class="fa fa-phone"></i> <?php echo _('Mobile Text (SMS)'); ?></a></li>
                    <?php
                    // Custom tabs
                    foreach ($customtabs as $ct) {
                        $id = grab_array_var($ct, "id");
                        $title = grab_array_var($ct, "title");
                        echo "<li><a href='#tab-custom-" . $id . "'>" . $title . "</a></li>";
                    }
                    ?>
                </ul>


            <div id="tab-email">

                <?php
                // Warn user about notifications being disabled
                if (get_user_meta(0, 'notify_by_email') == 0) {
                    $nmsg = array();
                    $nmsg[] = '<div><strong>'._('Note').':</strong> '._('You currently have email notifications disabled').'. <a href="notifymethods.php#tab-email">'._('Change notification methods').'</a> <i class="fa fa-arrow-circle-right"></i></div>';
                    display_message(true, false, $nmsg);
                }
                ?>

                <p><?php echo _('Specify the format of the host and service alert messages you receive via email'); ?>.</p>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td>
                            <label><?php echo _('Host Alert Subject'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="65" name="host_email_subject" id="host_email_subject" value="<?php echo encode_form_val($host_email_subject); ?>" class="textfield form-control">
                        </td>
                    </tr>
                    <tr>
                        <td class="vt">
                            <label><?php echo _('Host Alert Message'); ?>:</label>
                        </td>
                        <td>
                            <textarea name="host_email_message" class="form-control" style="width: 100%; height: 240px;" id="host_email_message"><?php echo encode_form_val($host_email_message); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label><?php echo _('Service Alert Subject'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="65" name="service_email_subject" id="service_email_subject" value="<?php echo encode_form_val($service_email_subject); ?>" class="textfield form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label><?php echo _('Service Alert Message'); ?>:</label>
                        </td>
                        <td>
                            <textarea name="service_email_message" class="form-control" style="width: 100%; height: 240px;" id="service_email_message"><?php echo encode_form_val($service_email_message); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-xs btn-info" id="resetemail" name="resetemail"><i class="fa fa-file-o l"></i> <?php echo _("Reset to default messages"); ?></button>
                        </td>
                    </tr>
                </table>

            </div>

            <div id="tab-sms">

                <?php
                // Warn user about notifications being disabled
                if (get_user_meta(0, 'notify_by_mobiletext') == 0) {
                    $nmsg = array();
                    $nmsg[] = '<div><strong>'._('Note').':</strong> '._('You currently have mobile text notifications disabled').'. <a href="notifymethods.php#tab-mobiletext">'._('Change notification methods').'</a> <i class="fa fa-arrow-circle-right"></i></div>';
                    display_message(true, false, $nmsg);
                }
                ?>

                <p><?php echo _('Specify the format of the host and service alert messages you receive via mobile text message'); ?>.</p>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td>
                            <label><?php echo _('Host Alert Subject'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="65" name="host_mobiletext_subject" id="host_mobiletext_subject" value="<?php echo encode_form_val($host_mobiletext_subject); ?>" class="textfield form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label><?php echo _('Host Alert Message'); ?>:</label>
                        </td>
                        <td>
                            <textarea name="host_mobiletext_message" class="form-control" style="width: 100%; height: 240px;" id="host_mobiletext_message"><?php echo encode_form_val($host_mobiletext_message); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php echo _('Service Alert Subject'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="65" name="service_mobiletext_subject" id="service_mobiletext_subject" value="<?php echo encode_form_val($service_mobiletext_subject); ?>" class="textfield form-control">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php echo _('Service Alert Message'); ?>:</label>
                        </td>
                        <td>
                            <textarea name="service_mobiletext_message" class="form-control" style="width: 100%; height: 240px;" id="service_mobiletext_message"><?php echo encode_form_val($service_mobiletext_message); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-xs btn-info" id="resetmobiletext" name="resetmobiletext"> <i class="fa fa-file-o l"></i> <?php echo _("Reset to default messages"); ?></button>
                        </td>
                    </tr>

                </table>

            </div>
            <!--mobile text tab-->


            <?php

            foreach ($notificationmethods as $name => $arr) {

                /*
                $tabid="";

                if(array_key_exists("tabs",$customtabs)){
                    foreach($customtabs["tabs"] as $ct){
                        if($ct["id"]==$name){
                            $tabid=$ct["id"];
                            break;
                            }
                        }
                    }
                */

                $inargs = $request;
                $outargs = array();
                $result = 0;

                echo "<div id='tab-custom-" . $name . "'>";

                $output = make_notificationmethod_callback($name, NOTIFICATIONMETHOD_MODE_GETMESSAGEFORMAT, $inargs, $outargs, $result);

                if ($output != '') {
                    echo $output;
                }

                echo "</div>";
            }
            ?>

        </div>

            <div>
                <button type="submit" class="submitbutton btn btn-sm btn-primary" id="updateButton" name="updateButton"><?php echo _('Update Settings'); ?></button>
                <button type="submit" class="submitbutton btn btn-sm btn-default" id="cancelButton" name="cancelButton"><?php echo _('Cancel'); ?></button>
            </div>

        </form>

    <?php
    }
    ?>


    <?php

    do_page_end(true);
    exit();
}


function do_updateprefs()
{
    global $request;
    global $notificationmethods;

    // user pressed the cancel button
    if (isset($request["cancelButton"])){
        header("Location: notifyprefs.php");
		return;
	}

    // check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;


    // grab form variable values
    $messages = array();

    $messages["email"]["host"]["subject"] = html_entity_decode(grab_request_var("host_email_subject", ""));
    $messages["email"]["host"]["message"] = html_entity_decode(grab_request_var("host_email_message", ""));
    $messages["email"]["service"]["subject"] = html_entity_decode(grab_request_var("service_email_subject", ""));
    $messages["email"]["service"]["message"] = html_entity_decode(grab_request_var("service_email_message", ""));

    $messages["mobiletext"]["host"]["subject"] = html_entity_decode(grab_request_var("host_mobiletext_subject", ""));
    $messages["mobiletext"]["host"]["message"] = html_entity_decode(grab_request_var("host_mobiletext_message", ""));
    $messages["mobiletext"]["service"]["subject"] = html_entity_decode(grab_request_var("service_mobiletext_subject", ""));
    $messages["mobiletext"]["service"]["message"] = html_entity_decode(grab_request_var("service_mobiletext_message", ""));

    // check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");

    // make callbacks to other notification methods
    foreach ($notificationmethods as $name => $arr) {

        $inargs = $request; // pass request vars to methods
        $outargs = array();
        $result = 0;

        make_notificationmethod_callback($name, NOTIFICATIONMETHOD_MODE_SETMESSAGEFORMAT, $inargs, $outargs, $result);

        // handle errors
        if ($result != 0) {
            if (array_key_exists(NOTIFICATIONMETHOD_ERROR_MESSAGES, $outargs)) {
                foreach ($outargs[NOTIFICATIONMETHOD_ERROR_MESSAGES] as $e)
                    $errmsg[$errors++] = $e;
            }
        }
    }

    // handle errors
    if ($errors > 0)
        show_updateprefs(true, $errmsg);

    // set new prefs
    set_user_meta(0, "notification_messages", serialize($messages));

    // log it
    send_to_audit_log(_("User updated their notification message settings"), AUDITLOGTYPE_CHANGE);

    // success!
    show_updateprefs(false, _("Notification preferences updated."));
}


?>