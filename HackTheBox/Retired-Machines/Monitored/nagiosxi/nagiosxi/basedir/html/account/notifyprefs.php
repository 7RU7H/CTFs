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
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    global $request;

    if (isset($request['updateButton'])) {
        do_updateprefs();
    } else if (isset($request["cancelButton"])) {
        header("Location: notifyprefs.php");
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

    // Check contact details
    $contact_name = get_user_attr(0, "username");
    $arr = get_user_nagioscore_contact_info($contact_name);
    $is_nagioscore_contact = $arr["is_nagioscore_contact"]; // Is the user a Nagios Core contact?
    $has_nagiosxi_timeperiod = $arr["has_nagiosxi_timeperiod"]; // Does the contact have XI notification timeperiod?

    // Fixes for assumed/missing recovery options prior to 2009R1.2
    $notify_recovery = get_user_meta(0, 'notify_host_recovery');
    if ($notify_recovery == "") {
        set_user_meta(0, 'notify_host_recovery', 1);
    }
    $notify_recovery = get_user_meta(0, 'notify_service_recovery');
    if ($notify_recovery == "") {
        set_user_meta(0, 'notify_service_recovery', 1);
    }

    // Defaults
    $enable_notifications = get_user_meta(0, 'enable_notifications');
    $notify_host_recovery = get_user_meta(0, 'notify_host_recovery');
    $notify_host_down = get_user_meta(0, 'notify_host_down');
    $notify_host_unreachable = get_user_meta(0, 'notify_host_unreachable');
    $notify_host_flapping = get_user_meta(0, 'notify_host_flapping');
    $notify_host_downtime = get_user_meta(0, 'notify_host_downtime');
    $notify_host_acknowledgment = get_user_meta(0, 'notify_host_acknowledgment', 1);
    $notify_service_recovery = get_user_meta(0, 'notify_service_recovery');
    $notify_service_warning = get_user_meta(0, 'notify_service_warning');
    $notify_service_unknown = get_user_meta(0, 'notify_service_unknown');
    $notify_service_critical = get_user_meta(0, 'notify_service_critical');
    $notify_service_flapping = get_user_meta(0, 'notify_service_flapping');
    $notify_service_downtime = get_user_meta(0, 'notify_service_downtime');
    $notify_service_acknowledgment = get_user_meta(0, 'notify_service_acknowledgment', 1);
    
    // Email priority
    $notify_host_recovery_priority = get_user_meta(0, 'notify_host_recovery_priority');
    $notify_host_down_priority = get_user_meta(0, 'notify_host_down_priority');
    $notify_host_unreachable_priority = get_user_meta(0, 'notify_host_unreachable_priority');
    $notify_host_flapping_priority = get_user_meta(0, 'notify_host_flapping_priority');
    $notify_host_downtime_priority = get_user_meta(0, 'notify_host_downtime_priority');
    $notify_host_acknowledgment_priority = get_user_meta(0, 'notify_host_acknowledgment_priority');
    $notify_service_recovery_priority = get_user_meta(0, 'notify_service_recovery_priority');
    $notify_service_warning_priority = get_user_meta(0, 'notify_service_warning_priority');
    $notify_service_unknown_priority = get_user_meta(0, 'notify_service_unknown_priority');
    $notify_service_critical_priority = get_user_meta(0, 'notify_service_critical_priority');
    $notify_service_flapping_priority = get_user_meta(0, 'notify_service_flapping_priority');
    $notify_service_downtime_priority = get_user_meta(0, 'notify_service_downtime_priority');
    $notify_service_acknowledgment_priority = get_user_meta(0, 'notify_service_acknowledgment_priority');

    // SMS defaults
    $notify_sms_host_recovery = get_user_meta(0, 'notify_sms_host_recovery', $notify_host_recovery);
    $notify_sms_host_down = get_user_meta(0, 'notify_sms_host_down', $notify_host_down);
    $notify_sms_host_unreachable = get_user_meta(0, 'notify_sms_host_unreachable', $notify_host_unreachable);
    $notify_sms_host_flapping = get_user_meta(0, 'notify_sms_host_flapping', $notify_host_flapping);
    $notify_sms_host_downtime = get_user_meta(0, 'notify_sms_host_downtime', $notify_host_downtime);
    $notify_sms_host_acknowledgment = get_user_meta(0, 'notify_sms_host_acknowledgment', $notify_host_acknowledgment);
    $notify_sms_service_recovery = get_user_meta(0, 'notify_sms_service_recovery', $notify_service_recovery);
    $notify_sms_service_warning = get_user_meta(0, 'notify_sms_service_warning', $notify_service_warning);
    $notify_sms_service_unknown = get_user_meta(0, 'notify_sms_service_unknown', $notify_service_unknown);
    $notify_sms_service_critical = get_user_meta(0, 'notify_sms_service_critical', $notify_service_critical);
    $notify_sms_service_flapping = get_user_meta(0, 'notify_sms_service_flapping', $notify_service_flapping);
    $notify_sms_service_downtime = get_user_meta(0, 'notify_sms_service_downtime', $notify_service_downtime);
    $notify_sms_service_acknowledgment = get_user_meta(0, 'notify_sms_service_acknowledgment', $notify_service_acknowledgment);
    
    // Are settings locked for this non-adminsitrative user?
    $lock_notifications = (is_null(get_user_meta(0, 'lock_notifications')) || is_admin()) ? false : get_user_meta(0, 'lock_notifications');

    $notification_times = array();
    $notification_times_raw = get_user_meta(0, 'notification_times');
    if ($notification_times_raw != null) {
        $notification_times = unserialize($notification_times_raw);
    }

    for ($day = 0; $day < 7; $day++) {
        if (!array_key_exists($day, $notification_times)) {
            $notification_times[$day] = array(
                "start" => "00:00",
                "end" => "24:00",
            );
        }
    }

    // Grab form variable values
    $enable_notifications = checkbox_binary(grab_request_var("enable_notifications", $enable_notifications));

    // Grav email host/service settings
    $notify_host_recovery = checkbox_binary(grab_request_var("notify_host_recovery", $notify_host_recovery));
    $notify_host_down = checkbox_binary(grab_request_var("notify_host_down", $notify_host_down));
    $notify_host_unreachable = checkbox_binary(grab_request_var("notify_host_unreachable", $notify_host_unreachable));
    $notify_host_flapping = checkbox_binary(grab_request_var("notify_host_flapping", $notify_host_flapping));
    $notify_host_downtime = checkbox_binary(grab_request_var("notify_host_downtime", $notify_host_downtime));
    $notify_host_acknowledgment = checkbox_binary(grab_request_var("notify_host_acknowledgment", $notify_host_acknowledgment));
    $notify_service_recovery = checkbox_binary(grab_request_var("notify_service_recovery", $notify_service_recovery));
    $notify_service_warning = checkbox_binary(grab_request_var("notify_service_warning", $notify_service_warning));
    $notify_service_unknown = checkbox_binary(grab_request_var("notify_service_unknown", $notify_service_unknown));
    $notify_service_critical = checkbox_binary(grab_request_var("notify_service_critical", $notify_service_critical));
    $notify_service_flapping = checkbox_binary(grab_request_var("notify_service_flapping", $notify_service_flapping));
    $notify_service_downtime = checkbox_binary(grab_request_var("notify_service_downtime", $notify_service_downtime));
    $notify_service_acknowledgment = checkbox_binary(grab_request_var("notify_service_acknowledgment", $notify_service_acknowledgment));
    
    $notify_host_recovery_priority = checkbox_binary(grab_request_var("notify_host_recovery_priority", $notify_host_recovery_priority));
    $notify_host_down_priority = checkbox_binary(grab_request_var("notify_host_down_priority", $notify_host_down_priority));
    $notify_host_unreachable_priority = checkbox_binary(grab_request_var("notify_host_unreachable_priority", $notify_host_unreachable_priority));
    $notify_host_flapping_priority = checkbox_binary(grab_request_var("notify_host_flapping_priority", $notify_host_flapping_priority));
    $notify_host_downtime_priority = checkbox_binary(grab_request_var("notify_host_downtime_priority", $notify_host_downtime_priority));
    $notify_host_acknowledgment_priority = checkbox_binary(grab_request_var("notify_host_acknowledgment_priority", $notify_host_acknowledgment_priority));
    $notify_service_recovery_priority = checkbox_binary(grab_request_var("notify_service_recovery_priority", $notify_service_recovery_priority));
    $notify_service_warning_priority = checkbox_binary(grab_request_var("notify_service_warning_priority", $notify_service_warning_priority));
    $notify_service_unknown_priority = checkbox_binary(grab_request_var("notify_service_unknown_priority", $notify_service_unknown_priority));
    $notify_service_critical_priority = checkbox_binary(grab_request_var("notify_service_critical_priority", $notify_service_critical_priority));
    $notify_service_flapping_priority = checkbox_binary(grab_request_var("notify_service_flapping_priority", $notify_service_flapping_priority));
    $notify_service_downtime_priority = checkbox_binary(grab_request_var("notify_service_downtime_priority", $notify_service_downtime_priority));
    $notify_service_acknowledgment_priority = checkbox_binary(grab_request_var("notify_service_acknowledgment_priority", $notify_service_acknowledgment_priority));

    // Grab sms host/service settings
    $notify_sms_host_recovery = checkbox_binary(grab_request_var("notify_sms_host_recovery", $notify_sms_host_recovery));
    $notify_sms_host_down = checkbox_binary(grab_request_var("notify_sms_host_down", $notify_sms_host_down));
    $notify_sms_host_unreachable = checkbox_binary(grab_request_var("notify_sms_host_unreachable", $notify_sms_host_unreachable));
    $notify_sms_host_flapping = checkbox_binary(grab_request_var("notify_sms_host_flapping", $notify_sms_host_flapping));
    $notify_sms_host_downtime = checkbox_binary(grab_request_var("notify_sms_host_downtime", $notify_sms_host_downtime));
    $notify_sms_service_recovery = checkbox_binary(grab_request_var("notify_sms_service_recovery", $notify_sms_service_recovery));
    $notify_sms_service_warning = checkbox_binary(grab_request_var("notify_sms_service_warning", $notify_sms_service_warning));
    $notify_sms_service_unknown = checkbox_binary(grab_request_var("notify_sms_service_unknown", $notify_sms_service_unknown));
    $notify_sms_service_critical = checkbox_binary(grab_request_var("notify_sms_service_critical", $notify_sms_service_critical));
    $notify_sms_service_flapping = checkbox_binary(grab_request_var("notify_sms_service_flapping", $notify_sms_service_flapping));
    $notify_sms_service_downtime = checkbox_binary(grab_request_var("notify_sms_service_downtime", $notify_sms_service_downtime));
    
    do_page_start(array("page_title" => _('Notification Preferences')), true);
?>

    <h1><?php echo _('Notification Preferences'); ?></h1>

    <?php
    if ($lock_notifications) {
        $nmsg = array();
        $nmsg[] = '<div><i class="fa fa-exclamation-triangle"></i> <strong>'._('Alert').'!</strong> '._('Notification settings have been locked for your account').'.</div>';
        display_message(true, false, $nmsg);
    }
    ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#tabs').tabs().show();

            // Automatically set email checkbox if priority is selected
            $('.priority').click(function() {
                if ($(this).is(":checked")) {
                    var id = $(this).attr('id').replace('Priority', '');
                    $('#'+id).attr('checked', true);
                }
            });

            // Automatically deselect priority checkbox if the regular email box is unchecked
            $('.regular').click(function() {
                if (!$(this).is(":checked")) {
                    var id = $(this).attr('id');
                    $('#'+id+'Priority').attr('checked', false);
                }
            });

        });
        <?php if ($lock_notifications) { ?>
        $(document).ready(function() {
            $('#updateNotificationPreferencesForm input').attr('disabled', 'disabled');
            $('#updateNotificationPreferencesForm input').attr('disabled', 'disabled');
        });
        <?php } ?>
    </script>

    <?php
    display_message($error, false, $msg);

    if ($is_nagioscore_contact == false) {
        echo _("Management of notification preferences is not available because your account is not configured to be a monitoring contact. Contact your system administrator for details.");
        exit;
    }
    ?>

    <form id="updateNotificationPreferencesForm" method="post" action="">

        <input type="hidden" name="update" value="1">
        <?php echo get_nagios_session_protector(); ?>

        <h5 class="ul"><?php echo _('Notification Status'); ?></h5>
        <p><?php echo _('Choose whether or not you want to receive alert messages'); ?>.<br><strong><?php echo _('Note'); ?>:</strong> <?php echo _('You must specify which notification methods to use in the'); ?> <a href="notifymethods.php"><?php echo _('notification methods'); ?></a> <?php echo _('page'); ?>.</p>

        <table class="table table-condensed table-no-border table-auto-width" style="margin-bottom: 10px;">
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="checkbox" name="enable_notifications" <?php echo is_checked($enable_notifications, 1); ?>> <?php echo _('Enable notifications'); ?></label>
                </td>
            </tr>
        </table>

        <div id="tabs" class="hide">
            <ul>
                <li><a href="#tab-email"><i class="fa fa-envelope"></i> <?php echo _('Email'); ?></a></li>
                <li><a href="#tab-sms"><i class="fa fa-phone"></i> <?php echo _('Mobile Text (SMS)'); ?></a></li>
                <li><a href="#tab-timeperiods"><i class="fa fa-clock-o"></i> <?php echo _('Time Periods'); ?></a></li>
            </ul>

            <div id="tab-email">

                <p><?php echo _('Select the types of alerts you\'d like to receive'); ?>.</p>

                <table class="table table-condensed table-no-border table-auto-width">
                    <thead>
                        <tr>
                            <th></th>
                            <th><i class="fa fa-envelope-o tt-bind" title="<?php echo _('Send email'); ?>"></i></th>
                            <th><i class="fa fa-exclamation-triangle tt-bind" title="<?php echo _('Send email high priority'); ?>"></i></th>
                            <th></th>
                            <th></th>
                            <th><i class="fa fa-envelope-o tt-bind" title="<?php echo _('Send email'); ?>"></i></th>
                            <th><i class="fa fa-exclamation-triangle tt-bind" title="<?php echo _('Send email high priority'); ?>"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label for="hostAcknowledgeNotificationsCheckBox"><?php echo _('Host Acknowledgment'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostAcknowledgeNotificationsCheckBox" name="notify_host_acknowledgment" <?php echo is_checked($notify_host_acknowledgment, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostAcknowledgeNotificationsCheckBoxPriority" name="notify_host_acknowledgment_priority" <?php echo is_checked($notify_host_acknowledgment_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="serviceAcknowledgeNotificationsCheckBox"><?php echo _('Service Acknowledgment'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceAcknowledgeNotificationsCheckBox" name="notify_service_acknowledgment" <?php echo is_checked($notify_service_acknowledgment, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceAcknowledgeNotificationsCheckBoxPriority" name="notify_service_acknowledgment_priority" <?php echo is_checked($notify_service_acknowledgment_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="hostRecoveryNotificationsCheckBox"><?php echo _('Host Recovery'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostRecoveryNotificationsCheckBox" name="notify_host_recovery" <?php echo is_checked($notify_host_recovery, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostRecoveryNotificationsCheckBoxPriority" name="notify_host_recovery_priority" <?php echo is_checked($notify_host_recovery_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="serviceRecoveryNotificationsCheckBox"><?php echo _('Service Recovery'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceRecoveryNotificationsCheckBox" name="notify_service_recovery" <?php echo is_checked($notify_service_recovery, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceRecoveryNotificationsCheckBoxPriority" name="notify_service_recovery_priority" <?php echo is_checked($notify_service_recovery_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="hostDownNotificationsCheckBox"><?php echo _('Host Down'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostDownNotificationsCheckBox" name="notify_host_down" <?php echo is_checked($notify_host_down, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostDownNotificationsCheckBoxPriority" name="notify_host_down_priority" <?php echo is_checked($notify_host_down_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="serviceWarningNotificationsCheckBox"><?php echo _('Service Warning'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceWarningNotificationsCheckBox" name="notify_service_warning" <?php echo is_checked($notify_service_warning, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceWarningNotificationsCheckBoxPriority" name="notify_service_warning_priority" <?php echo is_checked($notify_service_warning_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="hostUnreachableNotificationsCheckBox"><?php echo _('Host Unreachable'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostUnreachableNotificationsCheckBox" name="notify_host_unreachable" <?php echo is_checked($notify_host_unreachable, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostUnreachableNotificationsCheckBoxPriority" name="notify_host_unreachable_priority" <?php echo is_checked($notify_host_unreachable_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td></td>
                            <td>
                                <label for="serviceUnknownNotificationsCheckBox"><?php echo _('Service Unknown'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceUnknownNotificationsCheckBox" name="notify_service_unknown" <?php echo is_checked($notify_service_unknown, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceUnknownNotificationsCheckBoxPriority" name="notify_service_unknown_priority" <?php echo is_checked($notify_service_unknown_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="hostFlappingNotificationsCheckBox"><?php echo _('Host Flapping'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostFlappingNotificationsCheckBox" name="notify_host_flapping" <?php echo is_checked($notify_host_flapping, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostFlappingNotificationsCheckBoxPriority" name="notify_host_flapping_priority" <?php echo is_checked($notify_host_flapping_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td></td>
                            <td>
                                <label for="serviceCriticalNotificationsCheckBox"><?php echo _('Service Critical'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceCriticalNotificationsCheckBox" name="notify_service_critical" <?php echo is_checked($notify_service_critical, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceCriticalNotificationsCheckBoxPriority" name="notify_service_critical_priority" <?php echo is_checked($notify_service_critical_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="hostDowntimeNotificationsCheckBox"><?php echo _('Host Downtime'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="hostDowntimeNotificationsCheckBox" name="notify_host_downtime" <?php echo is_checked($notify_host_downtime, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="hostDowntimeNotificationsCheckBoxPriority" name="notify_host_downtime_priority" <?php echo is_checked($notify_host_downtime_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                            <td></td>
                            <td>
                                <label for="serviceFlappingNotificationsCheckBox"><?php echo _('Service Flapping'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceFlappingNotificationsCheckBox" name="notify_service_flapping" <?php echo is_checked($notify_service_flapping, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceFlappingNotificationsCheckBoxPriority" name="notify_service_flapping_priority" <?php echo is_checked($notify_service_flapping_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <label for="serviceDowntimeNotificationsCheckBox"><?php echo _('Service Downtime'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox regular" id="serviceDowntimeNotificationsCheckBox" name="notify_service_downtime" <?php echo is_checked($notify_service_downtime, 1); ?> title="<?php echo _('Send email'); ?>">
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox priority" id="serviceDowntimeNotificationsCheckBoxPriority" name="notify_service_downtime_priority" <?php echo is_checked($notify_service_downtime_priority, 1); ?> title="<?php echo _('Send email high priority'); ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <div id="tab-sms">

                <p><?php echo _('Select the types of mobile phone text (SMS) alerts you\'d like to receive'); ?>.</p>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tbody>
                        <tr>
                            <td>
                                <label for="SMShostAcknowledgmentNotificationsCheckBox"><?php echo _('Host Acknowledgment'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostAcknowledgmentNotificationsCheckBox" name="notify_sms_host_acknowledgment" <?php echo is_checked($notify_sms_host_acknowledgment, 1); ?>>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="SMSserviceAcknowledgmentNotificationsCheckBox"><?php echo _('Service Acknowledgment'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceAcknowledgmentNotificationsCheckBox" name="notify_sms_service_acknowledgment" <?php echo is_checked($notify_sms_service_acknowledgment, 1); ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="SMShostRecoveryNotificationsCheckBox"><?php echo _('Host Recovery'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostRecoveryNotificationsCheckBox" name="notify_sms_host_recovery" <?php echo is_checked($notify_sms_host_recovery, 1); ?>>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="SMSserviceRecoveryNotificationsCheckBox"><?php echo _('Service Recovery'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceRecoveryNotificationsCheckBox" name="notify_sms_service_recovery" <?php echo is_checked($notify_sms_service_recovery, 1); ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="SMShostDownNotificationsCheckBox"><?php echo _('Host Down'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostDownNotificationsCheckBox" name="notify_sms_host_down" <?php echo is_checked($notify_sms_host_down, 1); ?>>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                <label for="SMSserviceWarningNotificationsCheckBox"><?php echo _('Service Warning'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceWarningNotificationsCheckBox" name="notify_sms_service_warning" <?php echo is_checked($notify_sms_service_warning, 1); ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="SMShostUnreachableNotificationsCheckBox"><?php echo _('Host Unreachable'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostUnreachableNotificationsCheckBox" name="notify_sms_host_unreachable" <?php echo is_checked($notify_sms_host_unreachable, 1); ?>>
                            </td>
                            <td></td>
                            <td>
                                <label for="SMSserviceUnknownNotificationsCheckBox"><?php echo _('Service Unknown'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceUnknownNotificationsCheckBox" name="notify_sms_service_unknown" <?php echo is_checked($notify_sms_service_unknown, 1); ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="SMShostFlappingNotificationsCheckBox"><?php echo _('Host Flapping'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostFlappingNotificationsCheckBox" name="notify_sms_host_flapping" <?php echo is_checked($notify_sms_host_flapping, 1); ?>>
                            </td>
                            <td></td>
                            <td>
                                <label for="SMSserviceCriticalNotificationsCheckBox"><?php echo _('Service Critical'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceCriticalNotificationsCheckBox" name="notify_sms_service_critical" <?php echo is_checked($notify_sms_service_critical, 1); ?>>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <label for="SMShostDowntimeNotificationsCheckBox"><?php echo _('Host Downtime'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMShostDowntimeNotificationsCheckBox" name="notify_sms_host_downtime" <?php echo is_checked($notify_sms_host_downtime, 1); ?>>
                            </td>
                            <td></td>
                            <td>
                                <label for="SMSserviceFlappingNotificationsCheckBox"><?php echo _('Service Flapping'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceFlappingNotificationsCheckBox" name="notify_sms_service_flapping" <?php echo is_checked($notify_sms_service_flapping, 1); ?>>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <label for="SMSserviceDowntimeNotificationsCheckBox"><?php echo _('Service Downtime'); ?>:</label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="SMSserviceDowntimeNotificationsCheckBox" name="notify_sms_service_downtime" <?php echo is_checked($notify_sms_service_downtime, 1); ?>>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <div id="tab-timeperiods">

                <?php if ($has_nagiosxi_timeperiod == true) { ?>

                <p><?php echo _("Specify the times of day you'd like to receive alerts."); ?></p>
                
                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td>
                        </td>
                        <td>
                            <label><?php echo _("From"); ?>:</label>
                        </td>
                        <td>
                            <label><?php echo _("To"); ?>:</label>
                        </td>
                    </tr>
                    <?php
                    for ($day = 0; $day < 7; $day++) {

                        $start = $notification_times[$day]["start"];
                        $end = $notification_times[$day]["end"];
                        ?>

                        <tr>
                            <td>
                                <label><?php echo jddayofweek($day + 6, 1); ?>:</label>
                            </td>
                            <td>
                                <input type="text" size="5" name="start<?php echo encode_form_val($day); ?>" value="<?php echo $start; ?>" class="textfield form-control">
                            </td>
                            <td>
                                <input type="text" size="5" name="end<?php echo encode_form_val($day); ?>" value="<?php echo $end; ?>" class="textfield form-control">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>

                <?php } ?>

            </div>

        </div>

        <div id="formButtons">
            <input type="submit" class="submitbutton btn btn-sm btn-primary" name="updateButton" value="<?php echo _('Update Settings'); ?>" id="updateButton">
            <input type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton" value="<?php echo _('Cancel'); ?>" id="cancelButton">
        </div>

    </form>

    <?php
    do_page_end(true);
    exit();
}


function do_updateprefs()
{
    global $request;

    // Check session
    check_nagios_session_protector();

    // Check contact details
    $contact_name = get_user_attr(0, "username");
    $arr = get_user_nagioscore_contact_info($contact_name);
    $is_nagioscore_contact = $arr["is_nagioscore_contact"]; // Is the user a Nagios Core contact?
    $has_nagiosxi_timeperiod = $arr["has_nagiosxi_timeperiod"]; // Does the contact have XI notification timeperiod?

    // Not a nagios core contact
    if ($is_nagioscore_contact == false) {
        show_updateprefs();
        exit();
    }

    $errmsg = array();
    $errors = 0;

    // Defaults
    $enable_notifications = "";
    $notify_host_recovery = "";
    $notify_host_down = "";
    $notify_host_unreachable = "";
    $notify_host_flapping = "";
    $notify_host_downtime = "";
    $notify_host_acknowledgment = "";
    $notify_service_recovery = "";
    $notify_service_warning = "";
    $notify_service_unknown = "";
    $notify_service_critical = "";
    $notify_service_flapping = "";
    $notify_service_downtime = "";
    $notify_service_acknowledgment = "";
    
    // SMS defaults    
    $notify_sms_host_recovery = "";
    $notify_sms_host_down = "";
    $notify_sms_host_unreachable = "";
    $notify_sms_host_flapping = "";
    $notify_sms_host_downtime = "";
    $notify_sms_service_recovery = "";
    $notify_sms_service_warning = "";
    $notify_sms_service_unknown = "";
    $notify_sms_service_critical = "";
    $notify_sms_service_flapping = "";
    $notify_sms_service_downtime = "";
    $notify_sms_service_acknowledgment = '';
    $notify_sms_host_acknowledgment = '';

    // Grab form variable values
    $enable_notifications = checkbox_binary(grab_request_var("enable_notifications", $enable_notifications));

    $notify_host_recovery = checkbox_binary(grab_request_var("notify_host_recovery", $notify_host_recovery));
    $notify_host_down = checkbox_binary(grab_request_var("notify_host_down", $notify_host_down));
    $notify_host_unreachable = checkbox_binary(grab_request_var("notify_host_unreachable", $notify_host_unreachable));
    $notify_host_flapping = checkbox_binary(grab_request_var("notify_host_flapping", $notify_host_flapping));
    $notify_host_downtime = checkbox_binary(grab_request_var("notify_host_downtime", $notify_host_downtime));
    $notify_host_acknowledgment = checkbox_binary(grab_request_var("notify_host_acknowledgment", $notify_host_acknowledgment));
    $notify_service_recovery = checkbox_binary(grab_request_var("notify_service_recovery", $notify_service_recovery));
    $notify_service_warning = checkbox_binary(grab_request_var("notify_service_warning", $notify_service_warning));
    $notify_service_unknown = checkbox_binary(grab_request_var("notify_service_unknown", $notify_service_unknown));
    $notify_service_critical = checkbox_binary(grab_request_var("notify_service_critical", $notify_service_critical));
    $notify_service_flapping = checkbox_binary(grab_request_var("notify_service_flapping", $notify_service_flapping));
    $notify_service_downtime = checkbox_binary(grab_request_var("notify_service_downtime", $notify_service_downtime));
    $notify_service_acknowledgment = checkbox_binary(grab_request_var("notify_service_acknowledgment", $notify_service_acknowledgment));
    
    $notify_host_recovery_priority = checkbox_binary(grab_request_var("notify_host_recovery_priority", ''));
    $notify_host_down_priority = checkbox_binary(grab_request_var("notify_host_down_priority", ''));
    $notify_host_unreachable_priority = checkbox_binary(grab_request_var("notify_host_unreachable_priority", ''));
    $notify_host_flapping_priority = checkbox_binary(grab_request_var("notify_host_flapping_priority", ''));
    $notify_host_downtime_priority = checkbox_binary(grab_request_var("notify_host_downtime_priority", ''));
    $notify_host_acknowledgment_priority = checkbox_binary(grab_request_var("notify_host_acknowledgment_priority", ''));
    $notify_service_recovery_priority = checkbox_binary(grab_request_var("notify_service_recovery_priority", ''));
    $notify_service_warning_priority = checkbox_binary(grab_request_var("notify_service_warning_priority", ''));
    $notify_service_unknown_priority = checkbox_binary(grab_request_var("notify_service_unknown_priority", ''));
    $notify_service_critical_priority = checkbox_binary(grab_request_var("notify_service_critical_priority", ''));
    $notify_service_flapping_priority = checkbox_binary(grab_request_var("notify_service_flapping_priority", ''));
    $notify_service_downtime_priority = checkbox_binary(grab_request_var("notify_service_downtime_priority", ''));
    $notify_service_acknowledgment_priority = checkbox_binary(grab_request_var("notify_service_acknowledgment_priority", ''));

    // Grab form variables for sms settings
    $notify_sms_host_recovery = checkbox_binary(grab_request_var("notify_sms_host_recovery", $notify_sms_host_recovery));
    $notify_sms_host_down = checkbox_binary(grab_request_var("notify_sms_host_down", $notify_sms_host_down));
    $notify_sms_host_unreachable = checkbox_binary(grab_request_var("notify_sms_host_unreachable", $notify_sms_host_unreachable));
    $notify_sms_host_flapping = checkbox_binary(grab_request_var("notify_sms_host_flapping", $notify_sms_host_flapping));
    $notify_sms_host_downtime = checkbox_binary(grab_request_var("notify_sms_host_downtime", $notify_sms_host_downtime));
    $notify_sms_host_acknowledgment = checkbox_binary(grab_request_var("notify_sms_host_acknowledgment", $notify_sms_host_acknowledgment));
    $notify_sms_service_recovery = checkbox_binary(grab_request_var("notify_sms_service_recovery", $notify_sms_service_recovery));
    $notify_sms_service_warning = checkbox_binary(grab_request_var("notify_sms_service_warning", $notify_sms_service_warning));
    $notify_sms_service_unknown = checkbox_binary(grab_request_var("notify_sms_service_unknown", $notify_sms_service_unknown));
    $notify_sms_service_critical = checkbox_binary(grab_request_var("notify_sms_service_critical", $notify_sms_service_critical));
    $notify_sms_service_flapping = checkbox_binary(grab_request_var("notify_sms_service_flapping", $notify_sms_service_flapping));
    $notify_sms_service_downtime = checkbox_binary(grab_request_var("notify_sms_service_downtime", $notify_sms_service_downtime));
    $notify_sms_service_acknowledgment = checkbox_binary(grab_request_var("notify_sms_service_acknowledgment", $notify_sms_service_acknowledgment));
    
    $notification_times = array();
    for ($day = 0; $day < 7; $day++) {
        $notification_times[$day] = array();
        $notification_times[$day]["start"] = grab_request_var("start" . $day, "00:00");
        $notification_times[$day]["end"] = grab_request_var("end" . $day, "24:00");
    }

    // Check for errors
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    for ($day = 0; $day < 7; $day++) {
        if (is_valid_timeperiod_timerange($notification_times[$day]["start"], $notification_times[$day]["end"]) == false) {
            $errmsg[$errors++] = _("One or more time ranges is invalid.");
            break;
        }
    }

    // Handle errors
    if ($errors > 0) {
        show_updateprefs(true, $errmsg);
    }

    // Set new prefs
    set_user_meta(0, 'enable_notifications', $enable_notifications, false);

    set_user_meta(0, 'notify_host_recovery', $notify_host_recovery, false);
    set_user_meta(0, 'notify_host_down', $notify_host_down, false);
    set_user_meta(0, 'notify_host_unreachable', $notify_host_unreachable, false);
    set_user_meta(0, 'notify_host_flapping', $notify_host_flapping, false);
    set_user_meta(0, 'notify_host_downtime', $notify_host_downtime, false);
    set_user_meta(0, 'notify_host_acknowledgment', $notify_host_acknowledgment, false);
    set_user_meta(0, 'notify_service_recovery', $notify_service_recovery, false);
    set_user_meta(0, 'notify_service_warning', $notify_service_warning, false);
    set_user_meta(0, 'notify_service_unknown', $notify_service_unknown, false);
    set_user_meta(0, 'notify_service_critical', $notify_service_critical, false);
    set_user_meta(0, 'notify_service_flapping', $notify_service_flapping, false);
    set_user_meta(0, 'notify_service_downtime', $notify_service_downtime, false);
    set_user_meta(0, 'notify_service_acknowledgment', $notify_service_acknowledgment, false);
    
    set_user_meta(0, 'notify_host_recovery_priority', $notify_host_recovery_priority, false);
    set_user_meta(0, 'notify_host_down_priority', $notify_host_down_priority, false);
    set_user_meta(0, 'notify_host_unreachable_priority', $notify_host_unreachable_priority, false);
    set_user_meta(0, 'notify_host_flapping_priority', $notify_host_flapping_priority, false);
    set_user_meta(0, 'notify_host_downtime_priority', $notify_host_downtime_priority, false);
    set_user_meta(0, 'notify_host_acknowledgment_priority', $notify_host_acknowledgment_priority, false);
    set_user_meta(0, 'notify_service_recovery_priority', $notify_service_recovery_priority, false);
    set_user_meta(0, 'notify_service_warning_priority', $notify_service_warning_priority, false);
    set_user_meta(0, 'notify_service_unknown_priority', $notify_service_unknown_priority, false);
    set_user_meta(0, 'notify_service_critical_priority', $notify_service_critical_priority, false);
    set_user_meta(0, 'notify_service_flapping_priority', $notify_service_flapping_priority, false);
    set_user_meta(0, 'notify_service_downtime_priority', $notify_service_downtime_priority, false);
    set_user_meta(0, 'notify_service_acknowledgment_priority', $notify_service_acknowledgment_priority, false);

    // Set new SMS prefs
    set_user_meta(0, 'notify_sms_host_recovery', $notify_sms_host_recovery, false);
    set_user_meta(0, 'notify_sms_host_down', $notify_sms_host_down, false);
    set_user_meta(0, 'notify_sms_host_unreachable', $notify_sms_host_unreachable, false);
    set_user_meta(0, 'notify_sms_host_flapping', $notify_sms_host_flapping, false);
    set_user_meta(0, 'notify_sms_host_downtime', $notify_sms_host_downtime, false);
    set_user_meta(0, 'notify_sms_host_acknowledgment', $notify_sms_host_acknowledgment, false);
    set_user_meta(0, 'notify_sms_service_recovery', $notify_sms_service_recovery, false);
    set_user_meta(0, 'notify_sms_service_warning', $notify_sms_service_warning, false);
    set_user_meta(0, 'notify_sms_service_unknown', $notify_sms_service_unknown, false);
    set_user_meta(0, 'notify_sms_service_critical', $notify_sms_service_critical, false);
    set_user_meta(0, 'notify_sms_service_flapping', $notify_sms_service_flapping, false);
    set_user_meta(0, 'notify_sms_service_downtime', $notify_sms_service_downtime, false);
    set_user_meta(0, 'notify_sms_service_acknowledgment', $notify_sms_service_acknowledgment, false);

    $notification_times_raw = serialize($notification_times);
    set_user_meta(0, 'notification_times', $notification_times_raw, false);

    // Generate the notification option strings
    $host_notification_options = "";
    $x = 0;
    if (($notify_host_down == 1) || ($notify_sms_host_down == 1)) {
        $host_notification_options .= "d";
        $x++;
    }
    if (($notify_host_unreachable == 1) || ($notify_sms_host_unreachable == 1)) {
        if ($x > 0)
            $host_notification_options .= ",";
        $host_notification_options .= "u";
        $x++;
    }
    if (($notify_host_recovery == 1) || ($notify_sms_host_recovery == 1)) {
        if ($x > 0)
            $host_notification_options .= ",";
        $host_notification_options .= "r";
        $x++;
    }
    if (($notify_host_flapping == 1) || ($notify_sms_host_flapping == 1)) {
        if ($x > 0)
            $host_notification_options .= ",";
        $host_notification_options .= "f";
        $x++;
    }
    if (($notify_host_downtime == 1) || ($notify_sms_host_downtime == 1)) {
        if ($x > 0)
            $host_notification_options .= ",";
        $host_notification_options .= "s";
        $x++;
    }
    if ($x == 0) {
        $host_notification_options = "n";
    }

    // Generate the notification option strings
    $service_notification_options = "";
    $x = 0;
    if (($notify_service_warning == 1) || ($notify_sms_service_warning == 1)) {
        $service_notification_options .= "w";
        $x++;
    }
    if (($notify_service_unknown == 1) || ($notify_sms_service_unknown == 1)) {
        if ($x > 0)
            $service_notification_options .= ",";
        $service_notification_options .= "u";
        $x++;
    }
    if (($notify_service_critical == 1) || ($notify_sms_service_critical == 1)) {
        if ($x > 0)
            $service_notification_options .= ",";
        $service_notification_options .= "c";
        $x++;
    }
    if (($notify_service_recovery == 1) || ($notify_sms_service_recovery == 1)) {
        if ($x > 0)
            $service_notification_options .= ",";
        $service_notification_options .= "r";
        $x++;
    }
    if (($notify_service_flapping == 1) || ($notify_sms_service_flapping == 1)) {
        if ($x > 0)
            $service_notification_options .= ",";
        $service_notification_options .= "f";
        $x++;
    }
    if (($notify_service_downtime == 1) || ($notify_sms_service_downtime == 1)) {
        if ($x > 0)
            $service_notification_options .= ",";
        $service_notification_options .= "s";
        $x++;
    }
    if ($x == 0) {
        $service_notification_options = "n";
    }

    // Update nagios core configuration
    $args = array(
        "host_notifications_enabled" => $enable_notifications,
        "service_notifications_enabled" => $enable_notifications,
        "host_notification_options" => $host_notification_options,
        "service_notification_options" => $service_notification_options,
    );
    $contact_name = get_user_attr(0, "username");
    update_nagioscore_contact($contact_name, $args, false);

    if ($has_nagiosxi_timeperiod == true) {
        $timeperiod_name = get_nagioscore_contact_timeperiod_name($contact_name);
        for ($day = 0; $day < 7; $day++) {
            $args = array(
                "range" => $notification_times[$day]["start"] . "-" . $notification_times[$day]["end"],
            );
            update_nagioscore_timeperiod_times($timeperiod_name, nagiosql_get_weekday_name($day), $args, false);
        }
    }

    // Apply contact and timeperiod updates
    reconfigure_nagioscore();

    // Log it in the audit log
    send_to_audit_log(_("User updated their notification preferences"), AUDITLOGTYPE_CHANGE);

    show_updateprefs(false, _("Notification preferences updated."));
}