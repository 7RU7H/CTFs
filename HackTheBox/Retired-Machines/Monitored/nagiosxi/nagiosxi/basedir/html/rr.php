<?php
//
// Rapid Response
// Copyright (c) 2017-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/includes/common.inc.php');


// Start session and check prereqs
init_session();
grab_request_vars(false);
check_prereqs();


// Check if we should force them to actually log in and view details
$secure_rr_url = get_option('secure_rr_url', grab_array_var($cfg, 'secure_response_url', 0));
if ($secure_rr_url) {
    echo _("Rapid response URLs have been secured, you must login and view object details. This link is no longer valid.");
    exit();
}


route_request();


/**
 * Re-initialize the session and check for auth token authentication
 *
 * @param   int     $object_id      Object ID
 * @param   string  $auth_token     User's auth token
 */
function rr_reinit_auth_token_check($object_id, $auth_token)
{
    deinit_session();
    init_session();

    if (!is_auth_token_authenticated()) {
        $error = rr_get_token_error($auth_token, $object_id);
        show_restricted($error);
        die();
    }
}


/**
 * Get the error to be displayed based on token and object ID
 *
 * @param   int     $object_id      Object ID
 * @param   string  $auth_token     User's auth token
 * @return  string                  Error message
 */
function rr_get_token_error($auth_token, $object_id)
{
    $error = '';
    $token_info = user_get_auth_token_info($auth_token);
    if ($token_info['auth_used']) {
        $error = _('Error: This rapid response URL has alread been used.');
    } else if (time() > $token_info['valid_until_ts']) {
        $error = _('Error: This rapid response URL has expired and can no longer be used.');
    }

    $url = 'login.php?redirect=' . urlencode('rr.php?oid=' . $object_id);
    $error .=  '<br>' . _('You must') . ' <a href="' . $url . '">' . _('login') . '</a> ' .  _('to view this object.');

    return $error;
}


/**
 * Verify rapid response authentication (slightly different than normal auth)
 *
 * @param   int     $object_id      Object ID
 * @param   string  $auth_token     User's auth token
 */
function rr_verify_auth($object_id, $auth_token)
{
    // Do special authentication for rapid reponse url and check if we are
    // session authed - if we have access to the object
    if (is_session_authenticated()) {

        // Check if session matches auth_token user, destroy the session if
        // the user_id does not match the current session's ID
        if (!empty($auth_token)) {

            $token_info = user_get_auth_token_info($auth_token);

            // Check if the user ID of the session is different... if they
            // are then we need to re-initialzie the session with a new one
            if ($_SESSION['user_id'] != $token_info['auth_user_id']) {
                rr_reinit_auth_token_check($object_id, $auth_token);
            }

        }

        // We should tell someone they are logged in so they know
        // that they need to logout if they want to leave the page securely
        if (!isset($_SESSION['restrictions'])) {
            $msg = _('You are currently logged in as') . ' <b>' . get_user_attr(0, 'username') . '</b> ' . _('you can view this object in the status detail page by click the object name.');
            $msg_type = FLASH_MSG_INFO;

            // If there is already a message, we need to append to that
            if (isset($_SESSION['msg']) && $msg != $_SESSION['msg']) {
                $msg = $_SESSION['msg']['message'] . ' ' . $msg;
                $msg_type = $_SESSION['msg']['type'];
            }

            flash_message($msg, $msg_type);
        }

    } else if (!is_auth_token_authenticated()) {

        if (empty($auth_token)) {
            header("Location: login.php");
        }

        $error = rr_get_token_error($auth_token, $object_id);
        show_restricted($error);
        die();
    }

    user_update_session();

    // Check restrictions on session allow for access to this page
    if (user_is_restricted_area()) {
        redirect_to_login(true, _('You cannot access that page with a restricted session.<br>Please log into your account.'));
    }

    send_to_audit_log(_("User authenticated via Rapid Response"), AUDITLOGTYPE_SECURITY);

    // Do one more check to verify that the user has the ability to edit
    // the object that is specified
    if (!array_key_exists('user_id', $_SESSION) || !is_authorized_for_object_id($_SESSION['user_id'], $object_id)) {
        show_restricted(_("Access denied. You do not have access to this object."), true);
        die();
    }
}


/**
 * Route the rapid response requests
 */
function route_request()
{
    $object_id = intval(grab_request_var('oid', 0));
    $auth_token = grab_request_var('token');

    if (empty($object_id)) {
        header("Location: login.php");
        die();
    }

    // Check auth for object
    rr_verify_auth($object_id, $auth_token);

    // Run a command
    $cmd = grab_request_var('cmd', '');
    if (!empty($cmd)) {
        process_command();
        header("Location: rr.php?oid=".$object_id);
        die();
    }

    show_options($object_id);
}


// Process the request for commands suche as acknowledgement, notification,
// comments, downtimes, etc.
function process_command()
{
    check_nagios_session_protector();

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");

    $action = grab_request_var("action", "");
    $username = get_user_attr(0, "username");

    $comment = grab_request_var("comment", "");
    $duration = intval(grab_request_var("duration", "")) * 60;
    $sticky = intval(get_option('adefault_sticky_acknowledgment', 1));
    $notify = intval(get_option('adefault_send_notification', 1));
    $persistent = intval(get_option('adefault_persistent_comment', 0));

    // Get full object text for audit log
    $full_object = _("host") . " '" . $host . "'";
    if (!empty($service)) {
        $full_object = _("service") . " '" . $service . "' " . _('on') . " " . $full_object;
    }

    switch ($action) {

        case "comment":

            // Verify needed data exists
            if (empty($comment)) {
                flash_message(_('You must enter a comment to submit.'), FLASH_MSG_ERROR);
                return;
            }

            // Generate the raw command string
            $cmd_args = array('host_name' => $host,
                              'comment_data' => $comment,
                              'comment_author' => $username,
                              'persistent_comment' => $persistent);

            if (!empty($service)) {
                $cmd = NAGIOSCORE_CMD_ADD_SVC_COMMENT;
                $cmd_args['service_name'] = $service;
            } else {
                $cmd = NAGIOSCORE_CMD_ADD_HOST_COMMENT;
            }
            
            $action = _("User added a comment on");
            $msg = _("Comment has been submitted.");
            break;

        case "disablenotifications":

            $cmd_args = array('host_name' => $host);

            if (!empty($service)) {
                $cmd = NAGIOSCORE_CMD_DISABLE_SVC_NOTIFICATIONS;
                $cmd_args['service_name'] = $service;
            } else {
                $cmd = NAGIOSCORE_CMD_DISABLE_HOST_NOTIFICATIONS;
            }

            $action = _("User disabled notifications for");
            $msg = _("Notifications have been disabled.");
            break;

        case "acknowledge":

            $cmd_args = array('host_name' => $host,
                              'sticky' => $sticky,
                              'notify' => $notify,
                              'comment_data' => $comment,
                              'comment_author' => $username,
                              'persistent_comment' => $persistent);

            if (!empty($service)) {
                $cmd = NAGIOSCORE_CMD_ACKNOWLEDGE_SVC_PROBLEM;
                $cmd_args['service_name'] = $service;
            } else {
                $cmd = NAGIOSCORE_CMD_ACKNOWLEDGE_HOST_PROBLEM;
            }

            $action = _("User acknowledged problem state for");
            $msg = _("Acknowledged problem state.");
            break;

        case "scheduledowntime":
            $starttime = time();
            $endtime = $starttime + $duration;

            $cmd_args = array('host_name' => $host,
                              'fixed' => 1,
                              'trigger_id' => 0,
                              'start_time' => $starttime,
                              'end_time' => $endtime,
                              'duration' => $duration,
                              'comment_data' => $comment,
                              'comment_author' => $username,
                              'persistent_comment' => $persistent);

            if (!empty($service)) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_SVC_DOWNTIME;
                $cmd_args['service_name'] = $service;
            } else {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_HOST_DOWNTIME;
            }

            $action = _("User scheduled downtime for");
            $msg = _("Downtime has been scheduled.");
            break;

        default:
            return;
            break;
    }

    $msg_type = FLASH_MSG_INFO;

    $cmd_str = core_get_raw_command($cmd, $cmd_args);
    $ret = core_submit_command($cmd_str, $output);

    if (!$ret) {
        send_to_audit_log(_("Failed action") . ": " . $action . " " . $full_object . _(" via Rapid Response"), AUDITLOGTYPE_CHANGE);
    } else {
        send_to_audit_log($action . " " . $full_object . _(" via Rapid Response"), AUDITLOGTYPE_CHANGE);
    }

    flash_message($msg, $msg_type);
}


/**
 * Show the restricted page message and any information about the
 * access to the page and how to fix it
 */
function show_restricted($error = '', $object_error = false)
{
    $page_title = _("Rapid Response");

    if (empty($error)) {
        $error = _('Error: Unauthorized access. You cannot view this object.');
    }

    do_page_start(array("page_title" => $page_title), true);
    ?>

    <h1><?php echo $page_title; ?></h1>

    <p class="rr-error">
    <?php
    echo $error;
    ?>
    </p>

    <?php if (!$object_error) { ?>
    <div class="rr-description"><?php echo _('Why am I getting this error?'); ?></div>
    <div><?php echo _('Verify the rapid response URL is the exact same as in the notifiction. Possible reasons for error include'); ?>:</div>
    <ul class="rr-description-ul">
        <li><?php echo _('The single use URL has already been used'); ?></li>
        <li><?php echo _('The URL has expired'); ?></li>
        <li><?php echo _('Invalid or no auth token given in URL'); ?></li>
    </ul>
    <?php } ?>

    <?php
    do_page_end(true);
}


/**
 * Show the options that you can do with the RR object
 *
 * @param   int     $object_id  Object's ID (host/service)
 */
function show_options($object_id)
{
    $page_title = _("Rapid Response");

    // Get object information
    $args = array('object_id' => $object_id,
                  'objecttype_id' => 'in:' . implode(',', array(OBJECTTYPE_HOST, OBJECTTYPE_SERVICE)) );
    $oxml = get_xml_objects($args);
    if ($oxml != null) {
        $host = strval($oxml->object->name1);
        $service = strval($oxml->object->name2);
    }

    // If the object is not a host or service we should exit out here
    if (empty($host) && empty($service)) {
        show_restricted(_('Could not load object. The object is not a host or service.'), $object_id, true);
        die();
    }

    do_page_start(array("page_title" => $page_title, "body_class" => "rr", "body_style" => "overflow-y: scroll;", "mobile_compat" => true), true);
?>

    <h1><?php echo $page_title; ?></h1>

    <?php
    if (!empty($service)) {

        // Get service status info
        $args = array(
            "cmd" => "getservicestatus",
            "service_id" => $object_id,
        );
        $xml = get_xml_service_status($args);
        $servicestatus = array();
        if (!empty($xml)) {
            $servicestatus = $xml->servicestatus;
        }

        // Create links
        $service_title = encode_form_val($service);
        $host_title = encode_form_val($host);
        if (empty($_SESSION['restrictions'])) {
            $service_title = '<a href="index.php?xiwindow='.urlencode(get_service_status_detail_link($host, $service, "auto", false)).'">'.$service_title.'</a>';
            $host_title = '<a href="index.php?xiwindow='.urlencode(get_host_status_detail_link($host, "auto", false)).'">'.$host_title.'</a>';
        }
    ?>

        <div class="servicestatusdetailheader" style="margin-bottom: 20px;">
            <div class="serviceimage">
                <?php show_object_icon($host, $service, true); ?>
            </div>
            <div class="servicetitle">
                <div class="servicename">
                    <?php echo $service_title; ?>
                </div>
                <div class="hostname">
                    <?php echo $host_title; ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="row" style="margin-right: -10px; margin-left: -10px;">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
            <?php
            $args = array(
                "hostname" => $host,
                "servicename" => urlencode($service),
                "service_id" => $object_id,
                "hide_details" => 1
            );

            $id = "service_state_summary_" . random_string(6);
            $output = '
            <div class="service_state_summary state_summary" id="' . $id . '">
                ' . xicore_ajax_get_service_status_state_summary_html($args) . '
            </div>
            <script type="text/javascript">
            $(document).ready(function() {
                    
                $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(7, 'service_status_state_summary') . ', "timer-' . $id . '", function(i) {
                    var optsarr = {
                        "func": "get_service_status_state_summary_html",
                        "args": ' . json_encode($args) . '
                    }
                    var opts = JSON.stringify(optsarr);
                    get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
                });
                
            });
            </script>';
            echo $output;
            ?>
            </div>
        </div>

        <?php
        if (!empty($servicestatus)) {

            $notesoutput = "";

            $has_been_checked = intval($servicestatus->has_been_checked);
            $current_state = intval($servicestatus->current_state);
            $status_text = strval($servicestatus->status_text);

            $problem_acknowledged = intval($servicestatus->problem_acknowledged);
            $scheduled_downtime_depth = intval($servicestatus->scheduled_downtime_depth);
            $is_flapping = intval($servicestatus->is_flapping);
            $notifications_enabled = intval($servicestatus->notifications_enabled);

            $img = theme_image("unknown_small.png");
            $imgalt = _("Unknown");

            switch ($current_state) {
                case 0:
                    $img = theme_image("ok_small.png");
                    $statestr = _("Ok");
                    $imgalt = $statestr;
                    break;
                case 1:
                    $img = theme_image("warning_small.png");
                    $statestr = _("Warning");
                    $imgalt = $statestr;
                    break;
                case 2:
                    $img = theme_image("critical_small.png");
                    $statestr = _("Critical");
                    $imgalt = $statestr;
                    break;
                default:
                    break;
            }

            if ($problem_acknowledged == 1) {
                $attr_text = "Acknowledged";
                $attr_icon = theme_image("ack.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($scheduled_downtime_depth > 0) {
                $attr_text = "In Scheduled Downtime";
                $attr_icon = theme_image("downtime.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($is_flapping == 1) {
                $attr_text = "Flapping";
                $attr_icon = theme_image("flapping.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($notifications_enabled == 0) {
                $attr_text = "Notifications Disabled";
                $attr_icon = theme_image("nonotifications.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }

            if (!empty($notesoutput)) {
                echo '<div class="infotable_title">'._('Status Details').'</div>';
                echo '<ul class="servicestatusdetailnotes rr-detail-notes">';
                echo $notesoutput;
                echo '</ul>';
            }
        }
        ?>

        <div style="padding-bottom: 20px;">
            <div style="float: left;">
            <?php
            $args = array(
                "hostname" => encode_form_val($host),
                "servicename" => urlencode($service),
                "service_id" => $object_id,
                "display" => "simple",
            );

            $id = "service_comments_" . random_string(6);
            $output = '
                <div class="service_comments" id="' . $id . '">
                ' . xicore_ajax_get_service_comments_html($args) . '
                </div><!--service_comments-->
                <script type="text/javascript">
                $(document).ready(function(){
                        
                    $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(10, 'service_comments') . ', "timer-' . $id . '", function(i) {
                        var optsarr = {
                            "func": "get_service_comments_html",
                            "args": ' . json_encode($args) . '
                        }
                        var opts = JSON.stringify(optsarr);
                        get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
                    });
                    
                });
                </script>';
            echo $output;
            ?>
            </div>
            <div class="clear"></div>
        </div>

    <?php
    } else if ($host != "") {

        // Get host status info
        $args = array(
            "cmd" => "gethoststatus",
            "host_id" => $args["object_id"],
        );
        $xml = get_xml_host_status($args);
        $hoststatus = array();
        if (!empty($xml)) {
            $hoststatus = $xml->hoststatus;
        }

        // Create links
        $host_title = encode_form_val($host);
        if (empty($_SESSION['restrictions'])) {
            $host_title = '<a href="index.php?xiwindow='.urlencode(get_host_status_detail_link($host, "auto", false)).'">'.$host_title.'</a>';
        }

    ?>

        <div class="hoststatusdetailheader" style="margin-bottom: 20px;">
            <div class="hostimage">
                <?php show_object_icon($host, "", true); ?>
            </div>
            <div class="hosttitle">
                <div class="hostname">
                    <?php echo $host_title; ?>
                </div>
                <?php if (isset($hoststatus->alias)) { echo _('Alias') . ': ' . $hoststatus->alias; } ?>
            </div>
            <div class="clear"></div>
        </div>

        <div class="row" style="margin-right: -10px; margin-left: -10px;">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
            <?php
                $args = array(
                    "hostname" => $host,
                    "host_id" => $object_id,
                    "display" => "simple",
                    "hide_details" => 1
                );

                $id = "host_state_summary_" . random_string(6);
                $output = '
                <div class="host_state_summary state_summary" id="' . $id . '">
                    ' . xicore_ajax_get_host_status_state_summary_html($args) . '
                </div>
                <script type="text/javascript">
                $(document).ready(function(){
                        
                    $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(7, 'host_status_state_summary') . ', "timer-' . $id . '", function(i) {
                        var optsarr = {
                            "func": "get_host_status_state_summary_html",
                            "args": ' . json_encode($args) . '
                        }
                        var opts = JSON.stringify(optsarr);
                        get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
                    });
                    
                });
                </script>';
                echo $output;
                ?>
            </div>
        </div>

        <?php
        if (!empty($hoststatus)) {

            $notesoutput = "";

            $has_been_checked = intval($xml->hoststatus->has_been_checked);
            $current_state = intval($xml->hoststatus->current_state);
            $status_text = strval($xml->hoststatus->status_text);

            $problem_acknowledged = intval($xml->hoststatus->problem_acknowledged);
            $scheduled_downtime_depth = intval($xml->hoststatus->scheduled_downtime_depth);
            $is_flapping = intval($xml->hoststatus->is_flapping);
            $notifications_enabled = intval($xml->hoststatus->notifications_enabled);

            $img = theme_image("unknown_small.png");
            $imgalt = _("Unknown");

            switch ($current_state) {
                case 0:
                    $img = theme_image("ok_small.png");
                    $statestr = _("Up");
                    $imgalt = $statestr;
                    break;
                case 1:
                    $img = theme_image("critical_small.png");
                    $statestr = _("Down");
                    $imgalt = $statestr;
                    break;
                case 2:
                    $img = theme_image("critical_small.png");
                    $statestr = _("Unreachable");
                    $imgalt = $statestr;
                    break;
                default:
                    break;
            }

            if ($problem_acknowledged == 1) {
                $attr_text = "Acknowledged";
                $attr_icon = theme_image("ack.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($scheduled_downtime_depth > 0) {
                $attr_text = "In scheduled downtime";
                $attr_icon = theme_image("downtime.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($is_flapping == 1) {
                $attr_text = "Flapping";
                $attr_icon = theme_image("flapping.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }
            if ($notifications_enabled == 0) {
                $attr_text = "Notifications disabled";
                $attr_icon = theme_image("nonotifications.png");
                $attr_icon_alt = $attr_text;
                $notesoutput .= '<li><img src="' . $attr_icon . '" alt="' . $attr_icon_alt . '" title="' . $attr_icon_alt . '">' . $attr_text . '</li>';
            }

            if (!empty($notesoutput)) {
                echo '<div class="infotable_title">'._('Status Details').'</div>';
                echo '<ul class="hoststatusdetailnotes rr-detail-notes">';
                echo $notesoutput;
                echo '</ul>';
            }
        }

        ?>

        <div style="padding-bottom: 20px;">
            <div style="float: left;">
            <?php
            $args = array(
                "hostname" => $host,
                "host_id" => $object_id,
                "display" => "simple"
            );

            $id = "host_comments_" . random_string(6);
            $output = '
                <div class="host_comments" id="' . $id . '">
                ' . xicore_ajax_get_host_comments_html($args) . '
                </div>
                <script type="text/javascript">
                $(document).ready(function(){
                        
                    $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(10, 'host_comments') . ', "timer-' . $id . '", function(i) {
                        var optsarr = {
                            "func": "get_host_comments_html",
                            "args": ' . json_encode($args) . '
                        }
                        var opts = JSON.stringify(optsarr);
                        get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
                    });

                });
                </script>';
                echo $output;
                ?>
            </div>
            <div class="clear"></div>
        </div>

    <?php } ?>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#durationfield').hide();

        $('#actionList').change(function() {
            selected = $('#actionList').val();
            if (selected == "acknowledge" || selected == "scheduledowntime" || selected == "comment") {
                $('#commentfield').show();
            } else {
                $('#commentfield').hide();
            }
            if (selected == "scheduledowntime") {
                $('#durationfield').show();
            } else {
                $('#durationfield').hide();
            }
        });
    });
    </script>

    <div class="clear"></div>

    <div class="infotable_title"><?php echo _('Perform Action'); ?></div>

    <form method="post" action="rr.php?oid=<?php echo intval($object_id); ?>">
		<?php echo get_nagios_session_protector();?>
		<input type="hidden" name="cmd" value="submit">
		<input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">
		<input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>">
		<table class="table table-condensed table-no-border">
            <tr>
                <td style="width: 70px;">
                    <?php echo _("Action"); ?>:
                </td>
                <td>
                    <select class="form-control" name="action" id="actionList">
                        <?php
                        //added authorization check for read-only users 4/11/2012 -MG
                        if (($service == '' && is_authorized_for_host_command($_SESSION['user_id'], $host))
                            || ($service != '' && is_authorized_for_service_command($_SESSION['user_id'], $host, $service))) {
                            ?>
                            <?php if ($problem_acknowledged == 0) { ?><option value="acknowledge"><?php echo _("Acknowledge"); ?></option><?php } ?>
                            <?php if ($scheduled_downtime_depth == 0) { ?><option value="scheduledowntime"><?php echo _("Schedule Downtime"); ?></option><?php } ?>
                            <option value="comment"><?php echo _("Comment"); ?></option>
                            <?php if ($notifications_enabled == 1) { ?><option value="disablenotifications"><?php echo _("Disable Notifications"); ?></option><?php } ?>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="commentfield">
                <td>
                    <?php echo _("Comment"); ?>:
                </td>
                <td>
                    <input class="form-control" type="text" name="comment" size="30" value="<?php echo _("Problem is acknowledged"); ?>"><br>
                </td>
            </tr>
            <tr id="durationfield">
                <td>
                    <?php echo _("Duration"); ?>:
                </td>
                <td>
                    <input class="form-control" type="text" name="duration" size="2" value="60"> <?php echo _("Minutes"); ?><br/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" class="btn btn-sm btn-default" name="btnSubmit"><?php echo _('Go'); ?> <i class="fa fa-r fa-chevron-right"></i></button></td>
            </tr>
        </table>
	</form>
	
<?php
	do_page_end(true);
}
