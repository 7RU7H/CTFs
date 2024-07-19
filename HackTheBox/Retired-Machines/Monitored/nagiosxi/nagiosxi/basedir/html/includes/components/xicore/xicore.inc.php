<?php
//
// XI Core Component Functions
// Copyright (c) 2008-2019 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/ajaxhelpers.inc.php');
include_once(dirname(__FILE__) . '/dashlets.inc.php');
include_once(dirname(__FILE__) . '/status-utils.inc.php');
include_once(dirname(__FILE__) . '/recurringdowntime.inc.php');


xicore_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function xicore_component_init()
{
    $name = "xicore";
    $args = array(
        COMPONENT_NAME => $name,
        COMPONENT_TITLE => _("Nagios XI Core Functions"),
        COMPONENT_AUTHOR => _("Nagios Enterprises, LLC"),
        COMPONENT_DESCRIPTION => _("Provides core functions and interface functionality for Nagios XI."),
        COMPONENT_COPYRIGHT => _("Copyright (c) 2009-2019 Nagios Enterprises"),
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );
    register_component($name, $args);
}


////////////////////////////////////////////////////////////////////////
// EVENT HANDLER AND NOTIFICATION FUNCTIONS
////////////////////////////////////////////////////////////////////////


register_callback(CALLBACK_EVENT_PROCESSED, 'xicore_eventhandler');
register_callback(CALLBACK_SUBSYS_CLEANER, 'process_incoming_mail');


/**
 * Set up event type handlers for Nagios Core events
 *
 * @param   int     $cbtype     Callback type
 * @param   array   $args       Arguments
 */
function xicore_eventhandler($cbtype, $args)
{
    switch ($args["event_type"]) {
        case EVENTTYPE_NOTIFICATION:
            xicore_handle_notification_event($args);
            break;
        default:
            break;
    }
}


/**
 * Handle Nagios Core notifications through XI
 *
 * @param   array   $args   Notification event arguments
 */
function xicore_handle_notification_event($args)
{
    $debug = get_option("enable_subsystem_logging", true);

    // Don't do the notification events if we are inactive
    if (!is_license_activated()) {
        if ($debug) {
            echo "ERROR: License is not activated.\n";
        }
        return;
    }

    // Use this for debug output in PHPmailer log
    $debugmsg = "";

    $meta = $args["event_meta"];
    $contact = $meta["contact"];
    $nt = $meta["notification-type"];

    // Find the XI user
    $user_id = get_user_id($contact);
    if ($user_id <= 0) {
        if ($debug) {
            echo "ERROR: Could not find user_id for contact '" . $contact . "'\n";
        }
        return;
    }

    if ($debug) {
        echo "Got XI user id for contact '" . $contact . "': $user_id\n";
    }

    // Set user id session variable - used later in date/time, preference, etc. functions
    if (!defined("NAGIOSXI_USER_ID")) {
        define("NAGIOSXI_USER_ID", $user_id);
    }

    // Bail if user has notifications disabled completely
    $notifications_enabled = get_user_meta($user_id, 'enable_notifications');
    if ($notifications_enabled != 1) {
        if ($debug) {
            echo "" . _('ERROR: User has (global) notifications disabled!') . "\n";
        }
        return;
    }

    // EMAIL NOTIFICATIONS
    $notify = get_user_meta($user_id, "notify_by_email");
    $email_plaintext = (bool) get_user_meta($user_id, "email_plaintext", 0);

    // Default Priority
    $priority = ($meta['type'] == "PROBLEM") ? 1 : 0;

    // Get email notification options for user
    $notify_host_recovery = get_user_meta($user_id, 'notify_host_recovery');
    $notify_host_down = get_user_meta($user_id, 'notify_host_down');
    $notify_host_unreachable = get_user_meta($user_id, 'notify_host_unreachable');
    $notify_host_flapping = get_user_meta($user_id, 'notify_host_flapping');
    $notify_host_downtime = get_user_meta($user_id, 'notify_host_downtime');
    $notify_host_acknowledgment = get_user_meta($user_id, 'notify_host_acknowledgment', 1);
    $notify_service_recovery = get_user_meta($user_id, 'notify_service_recovery');
    $notify_service_warning = get_user_meta($user_id, 'notify_service_warning');
    $notify_service_unknown = get_user_meta($user_id, 'notify_service_unknown');
    $notify_service_critical = get_user_meta($user_id, 'notify_service_critical');
    $notify_service_flapping = get_user_meta($user_id, 'notify_service_flapping');
    $notify_service_downtime = get_user_meta($user_id, 'notify_service_downtime');
    $notify_service_acknowledgment = get_user_meta($user_id, 'notify_service_acknowledgment', 1);

    // Priority settings
    $notify_host_recovery_priority = get_user_meta($user_id, 'notify_host_recovery_priority');
    $notify_host_down_priority = get_user_meta($user_id, 'notify_host_down_priority');
    $notify_host_unreachable_priority = get_user_meta($user_id, 'notify_host_unreachable_priority');
    $notify_host_flapping_priority = get_user_meta($user_id, 'notify_host_flapping_priority');
    $notify_host_downtime_priority = get_user_meta($user_id, 'notify_host_downtime_priority');
    $notify_host_acknowledgment_priority = get_user_meta($user_id, 'notify_host_acknowledgment_priority');
    $notify_service_recovery_priority = get_user_meta($user_id, 'notify_service_recovery_priority');
    $notify_service_warning_priority = get_user_meta($user_id, 'notify_service_warning_priority');
    $notify_service_unknown_priority = get_user_meta($user_id, 'notify_service_unknown_priority');
    $notify_service_critical_priority = get_user_meta($user_id, 'notify_service_critical_priority');
    $notify_service_flapping_priority = get_user_meta($user_id, 'notify_service_flapping_priority');
    $notify_service_downtime_priority = get_user_meta($user_id, 'notify_service_downtime_priority');
    $notify_service_acknowledgment_priority = get_user_meta($user_id, 'notify_service_acknowledgment_priority');

    // Service
    if ($nt == "service") {
        switch ($meta['type']) {

            case "PROBLEM":
                if (($notify_service_warning != 1) && ($meta['servicestateid'] == 1)) {
                    $notify = 0;
                } else if (($notify_service_critical != 1) && ($meta['servicestateid'] == 2)) {
                    $notify = 0;
                } else if (($notify_service_unknown != 1) && ($meta['servicestateid'] == 3)) {
                    $notify =0;
                }
                
                if (($notify_service_warning_priority != 1) && ($meta['servicestateid'] == 1)) {
                    $priority = 0;
                } else if (($notify_service_critical_priority != 1) && ($meta['servicestateid'] == 2)) {
                    $priority = 0;
                } else if (($notify_service_unknown_priority != 1) && ($meta['servicestateid'] == 3)) {
                    $priority = 0;
                }
                break;

            case "RECOVERY":
                if ($notify_service_recovery != 1) {
                    $notify = 0;
                }
                $priority = $notify_service_recovery_priority;
                break;

            case "ACKNOWLEDGEMENT":
                if ($notify_service_acknowledgment != 1) {
                    $notify = 0;
                }
                $priority = $notify_service_acknowledgment_priority;
                break;

            case "FLAPPINGSTART":
            case "FLAPPINGSTOP":
                if ($notify_service_flapping != 1) {
                    $notify = 0;
                }
                $priority = $notify_service_flapping_priority;
                break;

            case "DOWNTIMESTART":
            case "DOWNTIMECANCELLED":
            case "DOWNTIMEEND":
                if ($notify_service_downtime != 1) {
                    $notify = 0;
                }
                $priority = $notify_service_downtime_priority;
                break;

        }

    // Host
    } else {
        switch ($meta['type']) {

            case "PROBLEM":
                if (($notify_host_down != 1) && ($meta['hoststateid'] == 1)) {
                    $notify = 0;
                } else if (($notify_host_unreachable != 1) && ($meta['hoststateid'] == 2)) {
                    $notify = 0;
                }
                
                if (($notify_host_down_priority != 1) && ($meta['hoststateid'] == 1)) {
                    $priority = 0;
                } else if (($notify_host_unreachable_priority != 1) && ($meta['hoststateid'] == 2)) {
                    $priority = 0;
                }
                break;

            case "RECOVERY":
                if ($notify_host_recovery != 1) {
                    $notify = 0;
                }
                $priority = $notify_host_recovery_priority;
                break;

            case "ACKNOWLEDGEMENT":
                if ($notify_host_acknowledgment != 1) {
                    $notify = 0;
                }
                $priority = $notify_host_acknowledgment_priority;
                break;

            case "FLAPPINGSTART":
            case "FLAPPINGSTOP":
                if ($notify_host_flapping != 1) {
                    $notify = 0;
                }
                $priority = $notify_host_flapping_priority;
                break;

            case "DOWNTIMESTART":
            case "DOWNTIMECANCELLED":
            case "DOWNTIMEEND":
                if ($notify_host_downtime != 1) {
                    $notify = 0;
                }
                $priority = $notify_host_downtime_priority;
                break;

        }    
    }

    if ($notify == 1) {

        if ($debug) {
            echo _("An email notification will be sent") . "...\n\n";
        }

        // Get the user's email address
        $email = get_user_attr($user_id, "email");

        // Get the email subject and message
        if ($nt == "service") {
            $subject = get_user_service_email_notification_subject($user_id);
            $message = get_user_service_email_notification_message($user_id);
        } else {
            $subject = get_user_host_email_notification_subject($user_id);
            $message = get_user_host_email_notification_message($user_id);
        }

        $opts = array(
            "subject" => $subject,
            "message" => $message,
            "meta"    => $meta,
        );

        do_callbacks(CALLBACK_NOTIFICATION_TEMPLATE_EMAIL, $opts);

        // process the alert text and replace variables
        $subject = process_notification_text($opts["subject"], $opts["meta"]);
        $message = process_notification_text($opts["message"], $opts["meta"]);

        // Set where email is coming from for PHPmailer log
        $send_mail_referer = "includes/components/xicore/xicore.inc.php > Event Handler Notification Email";

        $opts = array(
            "meta"          => $meta,
            "referer"       => $send_mail_referer,
            "to"            => $email,
            "subject"       => $subject,
            "high_priority" => $priority,
            "message"       => $message,
            "plaintext"     => $email_plaintext,
        );

        // If we are allowing reponses, add info the email before we send
        if (get_option("mail_inbound_process", 0)) {

            // Add the text to look for on reply
            $hash = mail_notification_hash($meta['host'], $meta['service']);
            if (!$email_plaintext) {
                $hash = '<div>'.$hash.'</div>';
            }
            $response = "---- "._("Respond above this line")." ----\n\n";
            $opts["message"] = $response . $opts["message"] . $hash;

        }

        do_callbacks(CALLBACK_NOTIFICATION_EMAIL, $opts);


        if ($debug) {
            echo "Email Notification Data:\n\n";
            print_r($opts);
            echo "\n\n";
        }

        send_email($opts, $debugmsg, $opts["referer"], $opts['plaintext']);

        do_callbacks(CALLBACK_NOTIFICATION_EMAIL_SENT, $opts);

    } else {
        if ($debug) {
            echo "" . _('User has email notifications disabled.') . "\n\n";
        }
    }

    // MOBILE TEXT NOTIFICATIONS
    $notify = get_user_meta($user_id, "notify_by_mobiletext");
  
    // Get SMS notificaiton options for user 
    $notify_sms_host_recovery = get_user_meta($user_id, 'notify_sms_host_recovery', $notify_host_recovery);
    $notify_sms_host_down = get_user_meta($user_id, 'notify_sms_host_down', $notify_host_down);
    $notify_sms_host_unreachable = get_user_meta($user_id, 'notify_sms_host_unreachable', $notify_host_unreachable);
    $notify_sms_host_flapping = get_user_meta($user_id, 'notify_sms_host_flapping', $notify_host_flapping);
    $notify_sms_host_downtime = get_user_meta($user_id, 'notify_sms_host_downtime', $notify_host_downtime);
    $notify_sms_host_acknowledgment = get_user_meta($user_id, 'notify_sms_host_acknowledgment', $notify_host_acknowledgment);
    $notify_sms_service_recovery = get_user_meta($user_id, 'notify_sms_service_recovery', $notify_service_recovery);
    $notify_sms_service_warning = get_user_meta($user_id, 'notify_sms_service_warning', $notify_service_warning);
    $notify_sms_service_unknown = get_user_meta($user_id, 'notify_sms_service_unknown', $notify_service_unknown);
    $notify_sms_service_critical = get_user_meta($user_id, 'notify_sms_service_critical', $notify_service_critical);
    $notify_sms_service_flapping = get_user_meta($user_id, 'notify_sms_service_flapping', $notify_service_flapping);
    $notify_sms_service_downtime = get_user_meta($user_id, 'notify_sms_service_downtime', $notify_service_downtime);
    $notify_sms_service_acknowledgment = get_user_meta($user_id, 'notify_sms_service_acknowledgment', $notify_service_acknowledgment);
    
    // Service
    if ($nt == "service") {
        switch ($meta['type']) {

            case "PROBLEM":
                if (($notify_sms_service_warning != 1) && ($meta['servicestateid'] == 1)) {
                    $notify = 0;
                } else if (($notify_sms_service_critical != 1) && ($meta['servicestateid'] == 2)) {
                    $notify = 0;
                } else if (($notify_sms_service_unknown != 1) && ($meta['servicestateid'] == 3)) {
                    $notify = 0;
                }
                break;

            case "RECOVERY":
                if ($notify_sms_service_recovery != 1) {
                    $notify = 0;
                }
                break;

            case "ACKNOWLEDGEMENT":
                if ($notify_sms_service_acknowledgment != 1) {
                    $notify = 0;
                }
                break;

            case "FLAPPINGSTART":
            case "FLAPPINGSTOP":
                if ($notify_sms_service_flapping != 1) {
                    $notify = 0;
                }
                break;

            case "DOWNTIMESTART":
            case "DOWNTIMECANCELLED":
            case "DOWNTIMEEND":
                if ($notify_sms_service_downtime != 1) {
                    $notify = 0;
                }
                break;

        }

    // Host
    } else {
        switch ($meta['type']) {

            case "PROBLEM":
                if (($notify_sms_host_down != 1) && ($meta['hoststateid'] == 1)) {
                    $notify = 0;
                } else if (($notify_sms_host_unreachable != 1) && ($meta['hoststateid'] == 2)) {
                    $notify = 0;
                }
                break;

            case "RECOVERY":
                if ($notify_sms_host_recovery != 1) {
                    $notify = 0;
                }
                break;

            case "ACKNOWLEDGEMENT":
                if ($notify_sms_host_acknowledgment != 1) {
                    $notify = 0;
                }
                break;

            case "FLAPPINGSTART":
            case "FLAPPINGSTOP":
                if ($notify_sms_host_flapping != 1) {
                    $notify = 0;
                }
                break;

            case "DOWNTIMESTART":
            case "DOWNTIMECANCELLED":
            case "DOWNTIMEEND":
                if ($notify_sms_host_downtime != 1) {
                    $notify = 0;
                }
                break;
        }    
    }

    if ($notify == 1) {

        if (!is_user_phone_verified($user_id)) {
            if ($debug) {
                echo _('ERROR: User has enabled SMS notifications but phone has not been verified.') . "\n\n";
            }
            return;
        }

        if ($debug) {
            echo _('A mobile text notification will be sent...') . "\n\n";
        }

        // Get the user's mobile info
        $mobile_number = get_user_meta($user_id, "mobile_number");
        $mobile_provider = get_user_meta($user_id, "mobile_provider");

        // Generate the email address to use
        $email = get_mobile_text_email($mobile_number, $mobile_provider);

        if ($debug == true)
            echo "Mobile number: " . $mobile_number . ", Mobile provider: " . $mobile_provider . " => Mobile Email: " . $email . "\n\n";

        // Get the email subject and message
        if ($nt == "service") {
            $subject = get_user_service_mobiletext_notification_subject($user_id);
            $message = get_user_service_mobiletext_notification_message($user_id);
        } else {
            $subject = get_user_host_mobiletext_notification_subject($user_id);
            $message = get_user_host_mobiletext_notification_message($user_id);
        }

        $opts = array(
            "subject" => $subject,
            "message" => $message,
            "meta"    => $meta
        );

        do_callbacks(CALLBACK_NOTIFICATION_TEMPLATE_SMS, $opts);

        // process the alert text and replace variables
        $subject = process_notification_text($opts["subject"], $opts["meta"]);
        $message = process_notification_text($opts["message"], $opts["meta"]);

        // Set where email is coming from for PHPmailer log
        $send_mail_referer = "includes/components/xicore/xicore.inc.php > Event Handler Notification Mobile Text";

        // Run bacllback on message
        $opts = array(
            "meta"    => $meta,
            "referer" => $send_mail_referer,
            "to"      => $email,
            "subject" => $subject,
            "message" => $message
        );

        do_callbacks(CALLBACK_NOTIFICATION_SMS, $opts);

        if ($debug) {
            echo "Mobile Text Notification Data:\n\n";
            print_r($opts);
            echo "\n\n";
        }

        send_email($opts, $debugmsg, $opts["referer"], true);

        do_callbacks(CALLBACK_NOTIFICATION_SMS_SENT, $opts);

    } else {
        if ($debug) {
            echo "User has mobile text notifications disabled.\n\n";
        }
    }
}


/**
 * Process incoming mail for responses and do actions based on responses
 */
function process_incoming_mail()
{
    $debug = get_option("enable_subsystem_logging", true);
    $debugmsg = "";

    // Check if we are supposed to be processing
    $process = get_option("mail_inbound_process", 0);
    if (!$process) {
        return;
    }

    // Check if process time has passed
    $mail_inbound_process_time = get_option("mail_inbound_process_time", 2);
    if ($mail_inbound_process_time > 1) {
        $mail_inbound_last_process_time = get_option("mail_inbound_last_process_time", 0);
        if (time() < ($mail_inbound_last_process_time + ($mail_inbound_process_time * 60))) {
            return;
        }
    }

    // Check for emails
    $i = 0;
    $emails = mail_get_unseen_messages();
    if (count($emails) > 0) {
        foreach ($emails as $email) {
            $error = false;
            $i++;

            // 1. Verify the sender email exists in the system (check users db)
            $from = $email['header']->from[0];
            $from_address = $from->mailbox.'@'.$from->host;
            $user = get_user_by_email($from_address);

            if (empty($user) || !$user['enabled']) {
                echo "Error - Email recieved from " . $from . " not found in the database or is a disabled user\n";
                continue;
            }

            // Convert quoted-printable string
            $email['body'] = quoted_printable_decode($email['body']);

            // Replace all Windows line endings with Linux ones
            $email['body'] = preg_replace("/[\\r\\n]+/", "\n", $email['body']);
        
            // 2. Verify that the email object hash exists and the host/service exists
            $start = strpos($email['body'], '##');
            $end = strpos($email['body'], '##', $start + 2);
            if ($start !== false && $end !== false) {

                $hash = trim(str_replace(array('> ', '>', "\n", "\t", "\r"), '', substr($email['body'], $start + 2, $end - $start - 2)));
                $obj = json_decode(decrypt_data($hash));

                // Verify that at least a hostname is present
                if (empty($obj->host)) {
                    echo "Error - Could not find a host object in the hash provided\n";
                    $error = true;
                }

            } else {
                echo "Error - Could not find a valid hash in the email sent\n";
                $error = true;
            }

            // Send email back to explain error
            if ($error) {
                $opts = array(
                    "referer" => "includes/components/xicore/xicore.inc.php > Inbound Email Processing",
                    "to"      => $from_address,
                    "subject" => _("Nagios XI - Response Error"),
                    "message" => _("Could not submit command due to missing hash data. Did you reply with the full email?")."\n\n- Nagios XI System"
                );
                send_email($opts, $debugmsg, $opts["referer"], true);
                continue;
            }

            // 3. Check for an acceptable response text
            $msg_end = strpos($email['body'], '----');
            $msg = substr($email['body'], 0, $msg_end);
            $msg_lines = explode("\n", $msg);
            $msg_lines_count = count($msg_lines);

            if ($debug) {
                echo "msg_end: " . $msg_end . "\n";
                echo "msg_lines_count: " . $msg_lines_count . "\n";
                echo "msg_lines: " . print_r($msg_lines) . "\n";
                echo "msg:" . $msg . "\n";
            }

            if ($msg_lines_count < 1) {
                continue;
            }

            // Strip tags from lines
            for ($x = 0; $x < $msg_lines_count; $x++) {
                $line = trim($msg_lines[$x]);
                $msg_lines[$x] = strip_tags($line);
            }

            // Check the lines, try to find out if there is a line with a command
            $cmds = array();
            for ($x = 0; $x < $msg_lines_count; $x++) {

                // Verify if line is empty and continue
                $line = strtolower(str_replace(' ', '', trim($msg_lines[$x])));
                if (empty($line)) {
                    continue;
                }
                
                // Set command if valid command
                $cmds = line_has_valid_commands($line);
                if (!empty($cmds)) {
                    break;
                }

            }

            if ($debug) {
                print_r($cmds);
            }

            if (empty($cmds)) {
                $opts = array(
                    "referer" => "includes/components/xicore/xicore.inc.php > Inbound Email Processing",
                    "to"      => $from_address,
                    "subject" => _("Nagios XI - Command Error"),
                    "message" => _("Missing response command. Did you send a valid command?")."\n\n"._("Valid commands:")." https://assets.nagios.com/downloads/nagiosxi/docs/Inbound-Email-Commands-for-Nagios-XI.pdf\n\n- Nagios XI System"
                );
                send_email($opts, $debugmsg, $opts["referer"], true);
                continue;
            }

            // Check for the actual data for each command and submit it
            $start_line = $x+1;
            $re = '/.* *- *\d+:\d+/'; // Super basic time format matching regex

            foreach ($cmds as $cmd) {
                $comment = false;
                $time = false;
                $minutes = false;
                $cmdtype = null;
                $x = $start_line;

                $args = array('host_name' => $obj->host, "comment_author" => $user['name']);
                if (!empty($obj->service)) {
                    $args['service_name'] = $obj->service;
                }

                switch ($cmd)
                {

                    case 'ack':
                    case 'acknowledge':
                    case 'acknowledgment':
                    case 'acknowledgement':
                        $args['persistent_comment'] = 0;
                        $args['comment_data'] = _('Problem was acknowledged by email response');
                        $comment = true;
                        $cmdtype = NAGIOSCORE_CMD_ACKNOWLEDGE_HOST_PROBLEM;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_ACKNOWLEDGE_SVC_PROBLEM;
                        }
                        break;

                    case 'down':
                    case 'downtime':
                    case 'scheduledowntime':
                        $comment = true;
                        $time = true;
                        $cmdtype = NAGIOSCORE_CMD_SCHEDULE_HOST_DOWNTIME;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_SCHEDULE_SVC_DOWNTIME;
                        }
                        break;

                    case 'com':
                    case 'comment':
                        $comment = true;
                        $cmdtype = NAGIOSCORE_CMD_ADD_HOST_COMMENT;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_ADD_SVC_COMMENT;
                        }
                        break;

                    case 'nonotify':
                    case 'nonotifications':
                    case 'disablenotify':
                    case 'disablenotifications':
                        $cmdtype = NAGIOSCORE_CMD_DISABLE_HOST_NOTIFICATIONS;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_DISABLE_SVC_NOTIFICATIONS;
                        }
                        break;

                    case 'check':
                    case 'immediate':
                    case 'immediatecheck':
                        $cmdtype = NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_CHECK;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_SCHEDULE_FORCED_SVC_CHECK;
                        }
                        break;

                    case 'delay':
                    case 'delaynotification':
                    case 'delaynotifications':
                    case 'delaynotify':
                        $minutes = true;
                        $cmdtype = NAGIOSCORE_CMD_DELAY_HOST_NOTIFICATION;
                        if (!empty($obj->service)) {
                            $cmdtype = NAGIOSCORE_CMD_DELAY_SVC_NOTIFICATION;
                        }
                        break;

                }

                // Get next minutes amount 
                if ($minutes) {
                    if (array_key_exists($x, $msg_lines)) {
                        $minutes_line = trim($msg_lines[$x]);
                        if (is_numeric($minutes_line)) {
                            $minutes = intval($minutes_line);
                            $args['notification_time'] = strtotime("+$minutes minutes");
                        } else {
                            $args['notification_time'] = strtotime($minutes_line);
                        }
                    }
                }

                // If the downtime command was passed
                if ($time) {
                    if (array_key_exists($x, $msg_lines)) {
                        $time_line = trim($msg_lines[$x]);
                        if (preg_match($re, $time_line) !== false) {
                            $times = explode('-', $time_line);
                            $start_time = trim($times[0]);
                            if ($start_time == 'now') {
                                $args['start_time'] = time();
                            } else {
                                $args['start_time'] = strtotime($times[0]);
                            }
                            $args['end_time'] = strtotime($times[1]);
                            $args['duration'] = abs($args['end_time'] - $args['start_time']);
                            $args['fixed'] = 1;
                            $args['comment_data'] = _('Downtime scheduled by email response');
                        }
                    }
                }

                // Get next (comment) line if it exists
                if ($comment) {

                    // Verify not a time and if it is, skip the line (for multi expressions)
                    if (array_key_exists($x, $msg_lines) && preg_match($re, trim($msg_lines[$x])) >= 1) {
                        $x++;
                    }

                    if (array_key_exists($x, $msg_lines)) {
                        $comment_line = trim($msg_lines[$x]);
                        if (!empty($comment_line)) {
                            $args['comment_data'] = $comment_line;

                            // Check for the rest of the lines and break when we find an empty one
                            $x++;
                            for ($n = $x; $n <= count($msg_lines); $n++) {
                                $comment_line = trim($msg_lines[$n]);
                                if (empty($comment_line)) {
                                    break;
                                }
                                $args['comment_data'] .= " ".$comment_line;
                            }

                            if ($debug) {
                                echo "Comment line: " . $args['comment_data'] . "\n";
                            }
                        }
                    }
                }

                // 4. Perform any actions that were requested that were found
                $output = '';
                $cmd = core_get_raw_command($cmdtype, $args);
                $result = core_submit_command($cmd, $output);

                if ($debug) {
                    echo $cmd . "\n";
                    print_r($args);
                    echo $result . "\n";
                    echo $output . "\n";
                }
            }

        }
    }

    echo "Processed " . $i . " incoming emails\n";
}


/**
 * Guess what the command we are trying to apply is
 *
 * @param   string  $line   Line from email
 * @return  array           Array of commands (if any)
 */
function line_has_valid_commands($line)
{
    $cmds = array();

    // Create an array of commands
    if (strpos($line, ',') !== false) {
        $lines = explode(',', $line);
    } else {
        $lines = array($line);
    }

    $valid_commands = array('ack', 'acknowledge', 'acknowledgment', 'acknowledgement',
                            'down', 'downtime', 'scheduledowntime',
                            'com', 'comment',
                            'disablenotifications', 'disablenotify', 'nonotify', 'nonotifications',
                            'immediate', 'immediatecheck', 'check',
                            'delay', 'delaynotification', 'delaynotifications', 'delaynotify');

    foreach ($lines as $line) {
        if (in_array(trim($line), $valid_commands)) {
            $cmds[] = $line;
        }
    }

    return $cmds;
}
