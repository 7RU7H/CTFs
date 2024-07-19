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
check_authentication(false);


route_request();


function route_request()
{
    global $request;

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $reroute = false;

    // Send back if the host/service doesn't exist
    if (host_exists($host) == false || ($service != "" && service_exists($host, $service) == false)) {
        $reroute = true;
    }

    // Check permissions for this host/service
    if ($service != "") {
        if (is_authorized_to_configure_service(0, $host, $service) == false) {
            $reroute = true;
        }
    } else {
        if (is_authorized_to_configure_host(0, $host) == false) {
            $reroute = true;
        }
    }

    // Send back to main if it failed any of the tests above
    if ($reroute == true) {
        header("Location: main.php");
        exit();
    }

    // Give out a message if we are in demo mode
    if (in_demo_mode() && isset($request["apply"])) {

        // If clicking cancel, exit back to the host/service page
        if (isset($request['cancelButton'])) {
            $return = grab_request_var("return", "");
            $url = get_return_url($return, $host, $service);
            header("Location: " . $url);
            exit();
        }

        flash_message(_('You cannot re-configure objects in demo mode.'));
        unset($request["apply"]);
    }

    // Determine if we are using a host or service
    if ($service != "") {
        if (isset($request["apply"])) {
            do_config_service();
        } else {
            show_service_config();
        }
    } else {
        if (isset($request["apply"])) {
            do_config_host();
        } else {
            show_host_config();
        }
    }
}

/**
 * @param bool   $error
 * @param string $msg
 */
function show_service_config($error = false, $msg = "")
{

    // Grab variables
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $return = grab_request_var("return", "");

    // Can this service be configured?
    check_service_config_prereqs($host, $service);

    // Default values
    $display_name = "";
    $check_interval = "";
    $retry_interval = "";
    $max_check_attempts = "";
    $check_command = "";
    $first_notification_delay = "";
    $notification_interval = "";
    $notification_targets = array(
        "myself" => "",
        "contacts" => "",
        "contactgroups" => "",
    );
    $contacts = "";
    $contact_names = array();
    $contact_groups = "";
    $contact_group_names = array();
    $contact_id = array();
    $contactgroup_id = array();
    $service_groups = "";
    $service_group_names = array();
    $servicegroup_id = array();

    // read existing configuration
    $sa = nagiosql_read_service_config_from_file($host, $service);

    //print_r($sa);
    if ($sa == null)
        redirect_service_config();

    // process values
    $val = grab_array_var($sa, "display_name");
    if ($val != "")
        $display_name = $val;
    $val = grab_array_var($sa, "check_interval");
    if ($val != "")
        $check_interval = $val;
    $val = grab_array_var($sa, "retry_interval");
    if ($val != "")
        $retry_interval = $val;
    $val = grab_array_var($sa, "max_check_attempts");
    if ($val != "")
        $max_check_attempts = $val;
    $val = grab_array_var($sa, "check_command");
    if ($val != "")
        $check_command = $val;

    $notifications_enabled = 1;
    $val = grab_array_var($sa, "notifications_enabled");
    if ($val != "")
        $notifications_enabled = $val;

    $val = grab_array_var($sa, "first_notification_delay");
    if ($val != "")
        $first_notification_delay = $val;

    $val = grab_array_var($sa, "notification_interval");
    if ($val != "")
        $notification_interval = $val;

    $val = grab_array_var($sa, "notification_options");
    if ($val == "n" || $notifications_enabled == 0)
        $notification_options = "none";
    else if ($first_notification_delay != "" && $first_notification_delay != "0")
        $notification_options = "delayed";
    else
        $notification_options = "immediate";

    $val = grab_array_var($sa, "contacts");
    if ($val != "")
        $contacts = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;
    $val = grab_array_var($sa, "contact_groups");
    if ($val != "")
        $contact_groups = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;

    $val = grab_array_var($sa, "servicegroups");
    if ($val != "")
        $service_groups = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;

    //echo "SERVICEGROUPS: $service_groups<BR>";

    // process contacts
    $c = explode(",", $contacts);
    // get user's name
    $username = get_user_attr(0, 'username');
    foreach ($c as $cid => $cname) {
        // "myself"
        if ($cname == $username) {
            $notification_targets["myself"] = "on";
            continue;
        }
        if ($cname == "null" || $cname == "")
            continue;
        // other contacts
        $contact_names[] = $cname;
    }

    if (count($contact_names) > 0)
        $notification_targets["contacts"] = "on";

    // process contactgroups
    $c = explode(",", $contact_groups);
    foreach ($c as $cid => $cname) {
        if ($cname == "null" || $cname == "")
            continue;
        $contact_group_names[] = $cname;
    }
    if (count($contact_group_names) > 0)
        $notification_targets["contactgroups"] = "on";

    // set some defaults for update purposes
    if (empty($first_notification_delay)) {
        $first_notification_delay = 15;
    }

    // process servicegroups
    $c = explode(",", $service_groups);
    foreach ($c as $cid => $cname) {
        if ($cname == "null" || $cname == "")
            continue;
        $service_group_names[] = $cname;
    }

    do_page_start(array("page_title" => _('Configure Service')), true);
    ?>

    <h1><?php echo _('Configure Service'); ?></h1>

    <div class="servicestatusdetailheader" style="margin-bottom: 10px;">
        <div class="serviceimage">
            <?php show_object_icon($host, $service, true); ?>
        </div>
        <div class="servicetitle">
            <div class="servicename">
                <a href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname">
                <a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <?php
    display_message($error, false, $msg);
    ?>

    <p>
    <?php
    if (user_can_access_ccm()) {
        $url = get_base_url() . "includes/components/ccm/xi-index.php";
        echo _("Note: You may update basic settings for the service below or use the") . " 
		<a href='" . $url . "' target='_top'>"._("Nagios Core Config Manager")."</a> " . _("to modify advanced settings for this service.");
    }
    echo _("Service attribute values which are inherited from advanced templates are not shown below.");
    ?>
    </p>

    <form method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
    <?php echo get_nagios_session_protector(); ?>
    <input type="hidden" name="apply" value="1"/>
    <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>"/>
    <input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>"/>
    <input type="hidden" name="return" value="<?php echo encode_form_val($return); ?>"/>
    <input type="hidden" name="originalservice" value="<?php echo base64_encode(serialize($sa)); ?>"/>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#tabs").tabs().show();
        });
    </script>

    <div id="tabs" class="hide">
        <ul>
            <li><a href="#attributes-tab"><?php echo _("Attributes"); ?></a></li>
            <li><a href="#monitoring-tab"><?php echo _("Monitoring"); ?></a></li>
            <li><a href="#notifications-tab"><?php echo _("Notifications"); ?></a></li>
            <li><a href="#groups-tab"><?php echo _("Groups"); ?></a></li>
        </ul>
        
    <div id="attributes-tab">
        <p><?php echo _("Change basic service settings."); ?></p>
        <table class="table table-no-border table-auto-width table-no-margin">
            <tr>
                <td class="vt">
                    <label><?php echo _("Host Name"); ?>:</label>
                </td>
                <td>
                    <input type="text" size="40" name="hostname2" id="hostname2" value="<?php echo encode_form_val($host); ?>" class="form-control" disabled>
                    <div class="subtext"><?php echo _("The unique name of the host."); ?></div>
                </td>
            </tr>
            <tr>
                <td class="vt">
                    <label><?php echo _("Service Description"); ?>:</label>
                </td>
                <td>
                    <input type="text" size="40" name="service2" id="service2" value="<?php echo encode_form_val($service); ?>" class="form-control" disabled>
                    <div class="subtext"><?php echo _("The unique description of the service"); ?>.</div>
                </td>
            </tr>
            <tr>
                <td class="vt">
                    <label><?php echo _("Display Name"); ?>:</label>
                </td>
                <td>
                    <input type="text" size="40" name="display_name" id="display_name" value="<?php echo encode_form_val($display_name); ?>" class="form-control">
                </td>
            </tr>
        </table>
    </div>

    <div id="monitoring-tab">
        <p><?php echo _("Specify the parameters that determine how the service should be monitored"); ?>.</p>
        <table class="table table-condensed table-no-border table-auto-width table-no-margin">
            <tbody>
                <tr>
                    <td><b><?php echo _("Under normal circumstances"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <?php echo _("Monitor the service every"); ?>
                        <input type="text" size="2" name="check_interval" id="check_interval" value="<?php echo encode_form_val($check_interval); ?>" class="form-control condensed">
                        <?php echo _("minutes"); ?>.
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("When a potential problem is first detected"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                            <?php echo _("Re-check the service every"); ?>
                            <input type="text" size="2" name="retry_interval" id="retry_interval" value="<?php echo $retry_interval; ?>" class="form-control condensed">
                            <?php echo _("minutes up to"); ?>
                            <input type="text" size="2" name="max_check_attempts" id="max_check_attempts" value="<?php echo encode_form_val($max_check_attempts); ?>" class="form-control condensed">
                            <?php echo _("times before generating an alert"); ?>.
                    </td>
                </tr>
                <?php if (is_advanced_user()) { ?>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b><?php echo _("Monitor the service with this command"); ?>:</b> <?php echo _("(Advanced users only)"); ?></td>
                    </tr>
                    <tr>
                        <td class="well text-pad">
                            <input type="text" size="90" name="check_command" id="check_command" value="<?php echo encode_form_val($check_command); ?>" class="form-control">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="notifications-tab">
        <p><?php echo _("Specify the parameters that determine how notifications should be sent for the service"); ?>.</p>

        <script type="text/javascript">
            function check_contacts() {
                if ($('input[name^="contact_id"]:checked').length != 0) {
                    $('#notification_targets_contacts').attr('checked', true);
                } else {
                    $('#notification_targets_contacts').attr('checked', false);
                }
            }
            function check_contactgroups() {
                if ($('input[name^="contactgroup_id"]:checked').length != 0) {
                    $('#notification_targets_contactgroups').attr('checked', true);
                } else {
                    $('#notification_targets_contactgroups').attr('checked', false);
                }
            }
        </script>

        <table class="table table-condensed table-no-border table-auto-width table-no-margin">
            <tbody>
                <tr>
                    <td><b><?php echo _("When a problem is detected"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well" style="padding: 12px 15px 15px 15px;">
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="none" <?php echo is_checked($notification_options, "none"); ?>>
                                <?php echo _("Don't send any notifications"); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="immediate" <?php echo is_checked($notification_options, "immediate"); ?>>
                                <?php echo _("Send a notification immediately"); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="delayed" <?php echo is_checked($notification_options, "delayed"); ?>>
                                <?php echo _("Wait"); ?>
                                <input type="text" size="2" name="first_notification_delay" id="first_notification_delay" value="<?php echo $first_notification_delay; ?>" class="form-control condensed">
                                <?php echo _("minutes before sending a notification"); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("If problems persist"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <?php echo _("Send a notification every"); ?>
                        <input type="text" size="2" name="notification_interval" id="notification_interval" value="<?php echo encode_form_val($notification_interval); ?>" class="form-control condensed">
                        <?php echo _("minutes until the problem is resolved"); ?>.
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("Send alert notifications to"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[myself]" id="notification_targets_myself" <?php echo is_checked($notification_targets["myself"], "on"); ?>>
                                <?php echo _("Myself"); ?>
                            </label>
                            (<a href="<?php echo get_base_url() . "account/?xiwindow=notifyprefs.php"; ?>" target="_blank" rel="noreferrer"><?php echo _("Adjust settings"); ?></a>)
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[contacts]" id="notification_targets_contacts" <?php echo is_checked($notification_targets["contacts"], "on"); ?>>
                                <?php echo _("Other individual contacts"); ?>
                            </label>
                        </div>
                        <div class="sel-users-new fixed">
                            <?php
                            $xml = get_xml_contact_objects(array("is_active" => 1, "orderby" => "alias:a"));
                            $username = get_user_attr(0, 'username');
                            foreach ($xml->contact as $c) {
                                $cid = strval($c->attributes()->id);
                                $cname = strval($c->contact_name);
                                $calias = strval($c->alias);

                                if (!strcmp($cname, $username)) {
                                    continue;
                                }
                                if (array_key_exists($cid, $contact_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($cname, $contact_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='contact_id[" . $cid . "]' " . $ischecked . " onclick='check_contacts()'>" . $calias . " (" . $cname . ")</label></div>";
                            }
                            ?>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[contactgroups]" id="notification_targets_contactgroups" <?php echo is_checked($notification_targets["contactgroups"], "on"); ?>>
                                <?php echo _("Specific contact groups"); ?>
                            </label>
                        </div>
                        <div class="sel-users-new fixed">
                            <?php
                            $xml = get_xml_contactgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                            foreach ($xml->contactgroup as $c) {
                                $cid = strval($c->attributes()->id);
                                $cname = strval($c->contactgroup_name);
                                $calias = strval($c->alias);

                                if (array_key_exists($cid, $contactgroup_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($cname, $contact_group_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='contactgroup_id[" . $cid . "]' " . $ischecked . " onclick='check_contactgroups()'>" . $calias . " (" . $cname . ")</label></div>";
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="groups-tab">
        <p><?php echo _("Define which servicegroup(s) the monitored service(s) should belong to (if any)."); ?></p>

        <table class="table table-condensed table-no-border table-auto-width table-no-margin">
            <tbody>
                <tr>
                    <td class="well text-pad freeform">
                        <div class="sel-users-new">
                            <?php
                            $xml = get_xml_servicegroup_objects(array("is_active" => 1, "orderby" => "servicegroup_name:a"));
                            foreach ($xml->servicegroup as $c) {
                                $cid = strval($c->attributes()->id);
                                $cname = strval($c->servicegroup_name);
                                $calias = strval($c->alias);

                                if (array_key_exists($cid, $servicegroup_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($cname, $service_group_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='servicegroup_id[" . $cid . "]' " . $ischecked . " >" . $calias . " (" . $cname . ")</label></div>";
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    </div>

    <div id="formButtons" style="margin: 10px 0 0 0;">
        <button type="submit" class="btn btn-sm btn-primary" name="updateButton"><?php echo _('Update'); ?></button>
        <button type="submit" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></button>
    </div>
</form>

<?php
}


/**
 * @param bool   $error
 * @param string $msg
 */
function redirect_service_config($error = false, $msg = "")
{
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");

    // Get edit in CCM link
    $ccm_id = nagiosccm_get_service_id($host, $service);
    $url = get_base_url() . "includes/components/ccm/xi-index.php?cmd=modify&name_filter=null&type=service&id=" . $ccm_id;

    // if we didn't get a match, then the url isn't going to take the user to the right page
    // it also means that we have an *actual* advanced config (service defined to hostgroup)
    // so we'll give them a better link (as if they searched for the service) and a more descriptive paragraph
    if ($ccm_id == -1)
        $url = get_base_url() . "includes/components/ccm/xi-index.php?cmd=view&type=service&name_filter=null&search=" . urlencode($service);

    do_page_start(array("page_title" => _('Configure Service')), true);
?>

    <h1><?php echo _('Configure Service'); ?></h1>

    <div class="servicestatusdetailheader">
        <div class="serviceimage">
            <?php show_object_icon($host, $service, true); ?>
        </div>
        <div class="servicetitle">
            <div class="servicename"><a href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>
    <?php display_message($error, false, $msg); // Display rrror messages ?>
    <p style="margin: 20px 0 10px 0;">
        <?php
        echo _("This service appears to make use of an advanced configuration using the Core Config Manager.");

        if (user_can_access_ccm() && $ccm_id == -1) {
            echo '<br>' . 
                _("Unfortunately, due to this service not being bound to a specific host (perhaps to a hostgroup instead?) we are unable to link you directly to it.") .
                '<br>' . 
                _("You can use the link below to search the CCM for potential matches.");
        }

        // Access denied for people who cannot access the CCM
        if (!user_can_access_ccm()) {
            echo '<br>' .  _("Contact your Nagios administrator to modify the settings for this service.");
        }
        ?>
    </p>

    <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
        <input type="hidden" name="show" value="servicedetail">
        <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">
        <input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>">

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-default" name="backButton"><i class="fa fa-chevron-left l"></i> <?php echo _('Back'); ?></button>
            <?php
            if (user_can_access_ccm()) { 
                $edit_text = _('Edit in CCM');
                if ($ccm_id == -1) {
                    $edit_text = _('Search for service in CCM');
                }
            ?>
            <a href="<?php echo $url; ?>" target="_top" class="btn btn-sm btn-info" style="margin-left: 5px;">
                <i class="fa fa-share l"></i> <?php echo $edit_text; ?>
            </a>
            <?php } ?>
        </div>
    </form>

    <?php
    exit();
}


/**
 * @param $host
 * @param $service
 */
function check_service_config_prereqs($host, $service)
{
    if (!is_service_configurable($host, $service)) {
        redirect_service_config();
    }
}


/**
 * @param bool   $error
 * @param string $msg
 */
function do_config_service($error = false, $msg = "")
{
    global $request;

    // Check session
    check_nagios_session_protector();

    // Grab variables
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $display_name = grab_request_var("display_name", "");
    $return = grab_request_var("return", "");

    // User cancelled, so redirect them
    if (isset($request["cancelButton"])) {
        $url = get_return_url($return, $host, $service);
        header("Location: " . $url);
        exit();
    }

    $original_service_s = grab_request_var("originalservice", "");
    if ($original_service_s == "") {
        $original_service = array();
    } else {
        $original_service = unserialize(base64_decode($original_service_s));
    }

    // Grab config variables
    $display_name = grab_request_var("display_name");
    $check_interval = grab_request_var("check_interval");
    $retry_interval = grab_request_var("retry_interval");
    $max_check_attempts = grab_request_var("max_check_attempts");
    $check_command = grab_request_var("check_command");
    $first_notification_delay = grab_request_var("first_notification_delay");
    $notification_interval = grab_request_var("notification_interval");
    $notification_options = grab_request_var("notification_options");
    $notification_targets = grab_request_var("notification_targets", array());
    $contact_id = grab_request_var("contact_id", array());
    $contactgroup_id = grab_request_var("contactgroup_id", array());
    $servicegroup_id = grab_request_var("servicegroup_id", array());

    // resolve contact names
    $contact_names = "";
    $total_contacts = 0;
    // this user
    if (array_key_exists("myself", $notification_targets)) {
        $contact_names .= get_user_attr(0, "username");
        $total_contacts++;
    }
    // additional individual contacts
    if (array_key_exists("contacts", $notification_targets)) {
        $ids = "";
        foreach ($contact_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "contact_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_contact_objects($args);
            foreach ($xml->contact as $c) {
                if ($total_contacts > 0)
                    $contact_names .= ",";
                $contact_names .= $c->contact_name;
                $total_contacts++;
            }
        }
    }
    //echo "<BR>CONTACTS: $contact_names<BR>";

    // resolve contactgroup names
    $contactgroup_names = "";
    $total_contactgroups = 0;
    // additional individual contactgroups
    if (array_key_exists("contactgroups", $notification_targets)) {
        $ids = "";
        foreach ($contactgroup_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "contactgroup_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_contactgroup_objects($args);
            foreach ($xml->contactgroup as $cg) {
                if ($total_contactgroups > 0)
                    $contactgroup_names .= ",";
                $contactgroup_names .= $cg->contactgroup_name;
                $total_contactgroups++;
            }
        }
    }
    //echo "<BR>CONTACTGROUPS: $contactgroup_names<BR>";

    // resolve servicegroup names
    $servicegroup_names = "";
    $total_servicegroups = 0;
    $ids = "";
    if (is_array($servicegroup_id)) {
        foreach ($servicegroup_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "servicegroup_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_servicegroup_objects($args);
            foreach ($xml->servicegroup as $sg) {
                if ($total_servicegroups > 0)
                    $servicegroup_names .= ",";
                $servicegroup_names .= $sg->servicegroup_name;
                $total_servicegroups++;
            }
        }
    }

    // new object config array
    $new_service = $original_service;

    // apply config settings to new object
    if ($display_name != "")
        $new_service["display_name"] = $display_name;
    if ($check_interval != "")
        $new_service["check_interval"] = $check_interval;
    if ($retry_interval != "")
        $new_service["retry_interval"] = $retry_interval;
    if ($max_check_attempts != "")
        $new_service["max_check_attempts"] = $max_check_attempts;
    if ($check_command != "")
        $new_service["check_command"] = $check_command;
    if ($notification_interval != "")
        $new_service["notification_interval"] = $notification_interval;

    // Contacts (only if modified)
    if ($contact_names != "") {
        $new_service["contacts"] = $contact_names;
        if (substr(grab_array_var($original_service, "contacts"), 0, 1) == "+") {
            $new_service["contacts"] = "+" . $new_service["contacts"];
        }
    } else {
        // Setting contacts to none using a query instead of the broken importing
        $orig_contacts = grab_array_var($original_service, "contacts", "");
        if ($orig_contacts != "null" && $orig_contacts != "+" && $orig_contacts != "") {
            $sid = nagiosql_get_service_id($host, $service);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_service` SET `contacts` = 0 WHERE `id` = '.$sid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkServiceToContact` WHERE `idMaster` = '.$sid);
            unset($new_service['contacts']);
        }
    }

    // Contactgroups (only if modified)
    if ($contactgroup_names != "") {
        $new_service["contact_groups"] = $contactgroup_names;
        if (substr(grab_array_var($original_service, "contact_groups"), 0, 1) == "+") {
            $new_service["contact_groups"] = "+" . $new_service["contact_groups"];
        }
    } else {
        // Setting contact groups to none using a query instead of the broken importing
        $orig_contact_groups = grab_array_var($original_service, "contact_groups", "");
        if ($orig_contact_groups != "null" && $orig_contact_groups != "+" && $orig_contact_groups != "") {
            $sid = nagiosql_get_service_id($host, $service);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_service` SET `contact_groups` = 0 WHERE `id` = '.$sid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkServiceToContactgroup` WHERE `idMaster` = '.$sid);
            unset($new_service['contact_groups']);
        }
    }

    // service groups (only if modified)
    if ($servicegroup_names != "") {
        $new_service["servicegroups"] = $servicegroup_names;
        if (substr(grab_array_var($original_service, "servicegroups"), 0, 1) == "+")
            $new_service["servicegroups"] = "+" . $new_service["servicegroups"];
    } else {
        $osg = grab_array_var($original_service, "servicegroups");
        if ($osg == "null") {
            $new_service["servicegroups"] = "null";
        } else {
            $sid = nagiosql_get_service_id($host, $service);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_service` SET `servicegroups` = 0 WHERE `id` = '.$sid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkServiceToServicegroup` WHERE `idMaster`=' . $sid);
            unset($new_service["servicegroups"]);
        }
    }

    // notification options
    // defaults (needed to override old settings when we re-import into NagiosQL
    $new_service["notifications_enabled"] = "1";
    if (isset($original_service["notification_options"]) && substr(grab_array_var($original_service, "notification_options"), 0, 1) == "n")
        $new_service["notification_options"] = "w,u,c,r,f,s";

    if ($notification_options == "immediate") {
        if (isset($new_service["first_notification_delay"])) {
            $new_service["first_notification_delay"] = "0";
        }
    } else if ($notification_options == "delayed") {
        $new_service["first_notification_delay"] = $first_notification_delay;
    } else if ($notification_options == "none") {
        $new_service["notification_options"] = "n";
        $new_service["notifications_enabled"] = "0";
    }

    // COMMIT THE SERVICE

    // log it
    send_to_audit_log("User reconfigured service '" . $service . "' on host '" . $host . "'", AUDITLOGTYPE_MODIFY, "", "", "", print_r($new_service, true));

    // create the import file
    $fname = $host; // use the hostname as part of the import file
    $fh = create_nagioscore_import_file($fname);

    // write the object definition to file
    fprintf($fh, "define service {\n");
    //print_r($new_service);
    foreach ($new_service as $var => $val) {
        //echo "PROCESSING $var=$val<BR>\n";
        fprintf($fh, $var . "\t%s\n", $val);
    }
    fprintf($fh, "}\n");

    // commit the import file
    fclose($fh);
    commit_nagioscore_import_file($fname);

    show_service_commit_complete();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_service_commit_complete($error = false, $msg = "")
{

    // Grab variables
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");

    do_page_start(array("page_title" => _("Configure Service")), true);
    ?>

    <h1><?php echo _("Configure Service"); ?></h1>

    <div class="servicestatusdetailheader">
        <div class="serviceimage">
            <?php show_object_icon($host, $service, true); ?> <!--image-->
        </div>
        <div class="servicetitle">
            <div class="servicename"><a href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>
    <?php display_message($error, false, $msg); // Display error messages ?>
    <ul class="commandresult" style="margin: 25px 0 10px 0;">
        <?php
        $id = submit_command(COMMAND_NAGIOSCORE_APPLYCONFIG);
        if ($id > 0) {
            echo "<li class='commandresultwaiting' id='commandwaiting'>" . _("Configuration submitted for processing") . "...</li>";
        } else {
            echo "<li class='commandresulterror'>" . _("An error occurred during command submission. If this problem persists, contact your Nagios administrator.") . "</li>\n";
        }
        ?>
    </ul>

    <div id="commandsuccesscontent" style="visibility: hidden;">

        <div class="message">
            <ul class="infoMessage">
                <li><?php echo _('Configuration applied successfully and backend was restarted.'); ?></li>
            </ul>
        </div>

        <h5 class="ul"><?php echo _("Service Re-Configuration Successful"); ?></h5>
        <p><?php echo _("The service has successfully been re-configured with the new settings."); ?></p>
        <?php
        $servicestatus_link = get_service_status_link($host, $service);
        ?>
        <ul>
            <li><a href="<?php echo $servicestatus_link; ?>"
                   target="_blank" rel="noreferrer"><?php echo _("View status details for"); ?> <?php echo encode_form_val($host) . " / " . encode_form_val($service); ?></a>
            </li>
            <?php
            if (is_admin() == true) {
                ?>
                <li><a href="<?php echo get_base_url(); ?>admin/?xiwindow=coreconfigsnapshots.php"
                       target="_top"><?php echo _("View the latest configuration snapshots"); ?></a></li>
            <?php
            }
            ?>
        </ul>

        <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
            <input type="hidden" name="show" value="servicedetail">
            <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">
            <input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>">

            <div id="formButtons" style="margin-top: 20px;">
                <button type="submit" class="btn btn-sm btn-primary" name="backButton"><?php echo _('Continue'); ?> <i class="fa fa-chevron-right r"></i></button>
            </div>
        </form>

    </div>

    <div id="commanderrorcontent" style="visibility: hidden;">

        <div class="message">
            <ul class="errorMessage">
                <li><?php echo _('Configuration error. Could not apply configuration.'); ?></li>
            </ul>
        </div>

        <h5 class="ul"><?php echo _("Service Re-Configuration Failed"); ?></h5>

        <p><?php echo _("A failure occurred while attempting to re-configure the service with the new settings."); ?></p>

        <?php
        if (is_admin() == true) {
            ?>
            <p><a href="<?php echo get_base_url(); ?>admin/?xiwindow=coreconfigsnapshots.php" target="_top"><?php echo _("View the latest configuration snapshots"); ?></a></p>
        <?php
        }
        ?>

        <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
            <input type="hidden" name="show" value="servicedetail">
            <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">
            <input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>">

            <div id="formButtons" style="margin-top: 10px;">
                <button type="submit" class="btn btn-sm btn-default" name="backButton"><i class="fa fa-chevron-left l"></i> <?php echo _('Back'); ?></button>
            </div>
        </form>

    </div>

    <script type="text/javascript">

        setTimeout(get_apply_config_result, 1000, <?php echo $id; ?>);

        function get_apply_config_result(command_id) {

            $('.commandresultwaiting').html('<?php echo _("Waiting for configuration verification"); ?>');

            $(this).everyTime(1 * 1000, "commandstatustimer", function (i) {

                $(".commandresultwaiting").append(".");

                var csdata = get_ajax_data("getcommandstatus", command_id);
                eval('var csobj=' + csdata);
                if (csobj.status_code == 2) {
                    if (csobj.result_code == 0) {
                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresultok");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).css("visibility", "visible");
                        });
                        $('#commandwaiting').html("<?php echo _('Configuration applied successfully.'); ?>");
                    }
                    else {
                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresulterror");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).css("display", "none")
                        });
                        $('#commanderrorcontent').each(function () {
                            $(this).css("visibility", "visible")
                        });
                        $('#commandwaiting').html("<?php echo _('Configuration verification failed.'); ?>");
                    }
                    $(this).stopTime("commandstatustimer");
                }
            });

        }
    </script>



    <?php

    do_page_end(true);
    exit();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_host_config($error = false, $msg = "")
{

    // grab variables
    $host = grab_request_var("host", "");
    $return = grab_request_var("return", "");

    // can this host be configured??
    check_host_config_prereqs($host);

    // default values
    $address = "";
    $alias = "";
    $display_name = "";
    $check_interval = "";
    $retry_interval = "";
    $max_check_attempts = "";
    $check_command = "";
    $first_notification_delay = "";
    $notification_interval = "";
    $notification_targets = array(
        "myself" => "",
        "contacts" => "",
        "contactgroups" => "",
    );
    $contacts = "";
    $contact_names = array();
    $contact_groups = "";
    $contact_group_names = array();
    $contact_id = array();
    $contactgroup_id = array();
    $host_groups = "";
    $hostgroup_id = array();
    $host_group_names = array();
    $parent_hosts = "";
    $parenthost_id = array();
    $parent_host_names = array();

    // read existing configuration
    $ha = nagiosql_read_host_config_from_file($host);

    //print_r($ha);

    // process values
    $val = grab_array_var($ha, "address");
    if ($val != "")
        $address = $val;
    $val = grab_array_var($ha, "alias");
    if ($val != "")
        $alias = $val;
    $val = grab_array_var($ha, "display_name");
    if ($val != "")
        $display_name = $val;
    $val = grab_array_var($ha, "check_interval");
    if ($val != "")
        $check_interval = $val;
    $val = grab_array_var($ha, "retry_interval");
    if ($val != "")
        $retry_interval = $val;
    $val = grab_array_var($ha, "max_check_attempts");
    if ($val != "")
        $max_check_attempts = $val;
    $val = grab_array_var($ha, "check_command");
    if ($val != "")
        $check_command = $val;

    $notifications_enabled = 1;
    $val = grab_array_var($ha, "notifications_enabled");
    if ($val != "")
        $notifications_enabled = $val;

    $val = grab_array_var($ha, "first_notification_delay");
    if ($val != "")
        $first_notification_delay = $val;

    $val = grab_array_var($ha, "notification_interval");
    if ($val != "")
        $notification_interval = $val;

    $val = grab_array_var($ha, "notification_options");
    if ($val == "n" || $notifications_enabled == 0)
        $notification_options = "none";
    else if ($first_notification_delay != "" && $first_notification_delay != "0")
        $notification_options = "delayed";
    else
        $notification_options = "immediate";

    $val = grab_array_var($ha, "contacts");
    if ($val != "")
        $contacts = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;
    $val = grab_array_var($ha, "contact_groups");
    if ($val != "")
        $contact_groups = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;

    $val = grab_array_var($ha, "hostgroups");
    if ($val != "")
        $host_groups = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;

    $val = grab_array_var($ha, "parents");
    if ($val != "")
        $parent_hosts = (substr($val, 0, 1) == "+") ? substr($val, 1) : $val;

    //echo "HOSTGROUPS: $host_groups<BR>";
    //echo "PARENTS: $parent_hosts<BR>";

    // process contacts
    $c = explode(",", $contacts);
    // get user's name
    $username = get_user_attr(0, 'username');
    foreach ($c as $cid => $cname) {
        // "myself"
        if ($cname == $username) {
            $notification_targets["myself"] = "on";
            continue;
        }
        if ($cname == "null" || $cname == "")
            continue;
        // other contacts
        $contact_names[] = $cname;
    }
    if (count($contact_names) > 0)
        $notification_targets["contacts"] = "on";

    // process contactgroups
    $c = explode(",", $contact_groups);
    foreach ($c as $cid => $cname) {
        if ($cname == "null" || $cname == "")
            continue;
        $contact_group_names[] = $cname;
    }
    if (count($contact_group_names) > 0)
        $notification_targets["contactgroups"] = "on";

    // set some defaults for update purposes
    if (empty($first_notification_delay)) {
        $first_notification_delay = 15;
    }

    // process hostgroups
    $c = explode(",", $host_groups);
    foreach ($c as $cid => $cname) {
        if ($cname == "null" || $cname == "")
            continue;
        $host_group_names[] = $cname;
    }

    // process hostgroups
    $c = explode(",", $parent_hosts);
    foreach ($c as $cid => $cname) {
        if ($cname == "null" || $cname == "")
            continue;
        $parent_host_names[] = $cname;
    }

    do_page_start(array("page_title" => _('Configure Host')), true);
?>

    <h1><?php echo _('Configure Host'); ?></h1>

    <div class="hoststatusdetailheader" style="margin-bottom: 10px;">
        <div class="hostimage">
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname">
                <a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <?php display_message($error, false, $msg); ?>

    <p>
    <?php
    if (user_can_access_ccm()) {
        $url = get_base_url() . "includes/components/ccm/xi-index.php";
        echo _("Note: You may update basic settings for the host below or use the") . " <a href='" . $url . "' target='_top'>" . _('Nagios Core Config Manager') . "</a> " . _("to modify advanced settings for this host.");
    }
    echo _("Host attribute values which are inherited from advanced templates are not shown below.");
    ?>
    </p>

    <form method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
    <?php echo get_nagios_session_protector(); ?>
    <input type="hidden" name="apply" value="1"/>
    <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>"/>
    <input type="hidden" name="return" value="<?php echo encode_form_val($return); ?>"/>
    <input type="hidden" name="originalhost" value="<?php echo base64_encode(serialize($ha)); ?>"/>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#tabs").tabs().show();
        });
    </script>

    <div id="tabs" class="hide">
        <ul>
            <li><a href="#attributes-tab"><?php echo _("Attributes"); ?></a></li>
            <li><a href="#monitoring-tab"><?php echo _("Monitoring"); ?></a></li>
            <li><a href="#notifications-tab"><?php echo _("Notifications"); ?></a></li>
            <li><a href="#groups-tab"><?php echo _("Host Groups"); ?></a></li>
            <?php if (is_advanced_user()) { ?>
                <li><a href="#parents-tab"><?php echo _("Host Parents"); ?></a></li>
            <?php } ?>
        </ul>
        <div id="attributes-tab">
            <p><?php echo _("Change basic host settings."); ?></p>
            <table class="table table-no-border table-auto-width table-no-margin">
                <tr>
                    <td class="vt">
                        <label><?php echo _("Host Name"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="hostname2" id="hostname2" value="<?php echo encode_form_val($host); ?>" class="form-control" disabled>
                        <div class="subtext"><?php echo _("The unique name of the host."); ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Address"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="address" id="address" value="<?php echo encode_form_val($address); ?>" class="form-control">
                        <div class="subtext"><?php echo _("The IP address or FQDNS name of the host"); ?>.</div>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Alias"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="alias" id="alias" value="<?php echo encode_form_val($alias); ?>" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Display Name"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="40" name="display_name" id="display_name" value="<?php echo encode_form_val($display_name); ?>" class="form-control">
                    </td>
                </tr>
            </table>
        </div>

        <div id="monitoring-tab">
            <p><?php echo _("Specify the parameters that determine how the host should be monitored"); ?>.</p>
            <table class="table table-condensed table-no-border table-auto-width table-no-margin">
                <tbody>
                <tr>
                    <td><b><?php echo _("Under normal circumstances"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <?php echo _("Monitor the host every"); ?>
                        <input type="text" size="2" name="check_interval" id="check_interval" value="<?php echo encode_form_val($check_interval); ?>" class="form-control condensed"> <?php echo _("minutes"); ?>.
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("When a potential problem is first detected"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <?php echo _("Re-check the host every"); ?>
                        <input type="text" size="2" name="retry_interval" id="retry_interval" value="<?php echo $retry_interval; ?>" class="form-control condensed">
                        <?php echo _("minutes up to"); ?>
                        <input type="text" size="2" name="max_check_attempts" id="max_check_attempts" value="<?php echo encode_form_val($max_check_attempts); ?>" class="form-control condensed">
                        <?php echo _("times before generating an alert"); ?>.
                    </td>
                </tr>
                <?php if (is_advanced_user()) { ?>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b><?php echo _("Monitor the host with this command"); ?>:</b> <?php echo _("(Advanced users only)"); ?></td>
                    </tr>
                    <tr>
                        <td class="well text-pad">
                            <input type="text" size="90" name="check_command" id="check_command" value="<?php echo encode_form_val($check_command); ?>" class="form-control">
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="notifications-tab">
            <p><?php echo _("Specify the parameters that determine how notifications should be sent for the host."); ?></p>

            <script type="text/javascript">
                function check_contacts() {
                    if ($('input[name^="contact_id"]:checked').length != 0) {
                        $('#notification_targets_contacts').attr('checked', true);
                    } else {
                        $('#notification_targets_contacts').attr('checked', false);
                    }
                }
                function check_contactgroups() {
                    if ($('input[name^="contactgroup_id"]:checked').length != 0) {
                        $('#notification_targets_contactgroups').attr('checked', true);
                    } else {
                        $('#notification_targets_contactgroups').attr('checked', false);
                    }
                }
            </script>

            <table class="table table-condensed table-no-border table-auto-width table-no-margin">
                <tbody>
                <tr>
                    <td><b><?php echo _("When a problem is detected"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well" style="padding: 12px 15px 15px 15px;">
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="none" <?php echo is_checked($notification_options, "none"); ?>>
                                <?php echo _("Don't send any notifications"); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="immediate"  <?php echo is_checked($notification_options, "immediate"); ?>>
                                <?php echo _("Send a notification immediately"); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="notification_options" value="delayed" <?php echo is_checked($notification_options, "delayed"); ?>>
                                <?php echo _("Wait"); ?>
                                <input type="text" size="2" name="first_notification_delay" id="first_notification_delay" value="<?php echo $first_notification_delay; ?>" class="form-control condensed">
                                <?php echo _("minutes before sending a notification"); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("If problems persist"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <?php echo _("Send a notification every"); ?>
                        <input type="text" size="2" name="notification_interval" id="notification_interval" value="<?php echo encode_form_val($notification_interval); ?>" class="form-control condensed">
                        <?php echo _("minutes until the problem is resolved"); ?>.
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo _("Send alert notifications to"); ?>:</b></td>
                </tr>
                <tr>
                    <td class="well text-pad">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[myself]" id="notification_targets_myself" <?php echo is_checked($notification_targets["myself"], "on"); ?>>
                                <?php echo _("Myself"); ?>
                            </label> (<a href="<?php echo get_base_url() . "account/?xiwindow=notifyprefs.php"; ?>" target="_blank" rel="noreferrer"><?php echo _("Adjust settings"); ?></a>)
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[contacts]" id="notification_targets_contacts" <?php echo is_checked($notification_targets["contacts"], "on"); ?>>
                                <?php echo _("Other individual contacts"); ?>
                            </label>
                        </div>
                        <div class="sel-users-new fixed">
                            <?php
                            $xml = get_xml_contact_objects(array("is_active" => 1, "orderby" => "alias:a"));
                            $username = get_user_attr(0, 'username');
                            foreach ($xml->contact as $c) {
                                $cid = strval($c->attributes()->id);
                                $cname = strval($c->contact_name);
                                $calias = strval($c->alias);

                                if (!strcmp($cname, $username)) {
                                    continue;
                                }
                                if (array_key_exists($cid, $contact_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($cname, $contact_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='contact_id[" . $cid . "]' " . $ischecked . " onclick='check_contacts()'>" . $calias . " (" . $cname . ")</label></div>";
                            }
                            ?>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notification_targets[contactgroups]"
                                       id="notification_targets_contactgroups" <?php echo is_checked($notification_targets["contactgroups"], "on"); ?>>
                                <?php echo _("Specific contact groups"); ?>
                            </label>
                        </div>
                        <div class="sel-users-new fixed">
                            <?php
                            $xml = get_xml_contactgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                            foreach ($xml->contactgroup as $c) {
                                $cid = strval($c->attributes()->id);
                                $cname = strval($c->contactgroup_name);
                                $calias = strval($c->alias);

                                if (array_key_exists($cid, $contactgroup_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($cname, $contact_group_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='contactgroup_id[" . $cid . "]' " . $ischecked . " onclick='check_contactgroups()'>" . $calias . " (" . $cname . ")</label></div>";
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- End monitoring tab -->

        <div id="groups-tab">
            <p><?php echo _("Define which hostgroup(s) the host should belong to (if any)."); ?></p>

            <table class="table table-condensed table-no-border table-auto-width table-no-margin">
                <tbody>
                <tr>
                    <td class="well text-pad freeform">
                        <div class="sel-users-new">
                            <?php
                            $xml = get_xml_hostgroup_objects(array("is_active" => 1, "orderby" => "hostgroup_name:a"));
                            foreach ($xml->hostgroup as $hg) {
                                $hgid = strval($hg->attributes()->id);
                                $hgname = strval($hg->hostgroup_name);
                                $hgalias = strval($hg->alias);

                                if (array_key_exists($hgid, $hostgroup_id)) {
                                    $ischecked = "CHECKED";
                                } else if (in_array($hgname, $host_group_names)) {
                                    $ischecked = "CHECKED";
                                } else {
                                    $ischecked = "";
                                }

                                echo "<div class='checkbox'><label><input type='checkbox' name='hostgroup_id[" . $hgid . "]' " . $ischecked . ">" . $hgalias . " (" . $hgname . ")</label></div>";
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- End groups tab -->

        <?php if (is_advanced_user()) { ?>
        <div id="parents-tab">
            <p><?php echo _("Define which host(s) are considered the parents of the the monitored host (if any). Note: Typically only one (1) host is specified as a parent."); ?></p>

            <table class="table table-condensed table-no-border table-auto-width table-no-margin">
                <tbody>
                <tr>
                    <td class="well text-pad freeform">
                        <div class="sel-users-new">
                        <?php
                        $xml = get_xml_host_objects(array("is_active" => 1, "orderby" => "host_name:a"));
                        foreach ($xml->host as $h) {
                            $hid = strval($h->attributes()->id);
                            $hname = strval($h->host_name);
                            $halias = strval($h->alias);

                            if (array_key_exists($hid, $parenthost_id)) {
                                $ischecked = "CHECKED";
                            } else if (in_array($hname, $parent_host_names)) {
                                $ischecked = "CHECKED";
                            } else {
                                $ischecked = "";
                            }

                            // Skip the host that we are editing to stop apply config errors
                            if ($hname == $host) {
                                continue;
                            }

                            echo "<div class='checkbox'><label><input type='checkbox' name='parenthost_id[" . $hid . "]' " . $ischecked . ">" . $halias . " (" . $hname . ")</label></div>";
                        }
                        ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>

    </div>

    <div id="formButtons" style="margin: 10px 0 0 0;">
        <button type="submit" class="btn btn-sm btn-primary" name="updateButton"><?php echo _('Update'); ?></button>
        <button type="submit" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></button>
    </div>
</form>

<?php
}

/**
 * @param bool   $error
 * @param string $msg
 */
function redirect_host_config($error = false, $msg = "")
{

    // Grab variables
    $host = grab_request_var("host", "");

    // Get edit in CCM link
    $ccm_id = nagiosccm_get_host_id($host);
    $url = get_base_url() . "includes/components/ccm/xi-index.php?cmd=modify&type=host&id=" . $ccm_id;

    do_page_start(array("page_title" => _('Configure Host')), true);
    ?>

    <h1><?php echo _('Configure Host'); ?></h1>

    <div class="hoststatusdetailheader">
        <div class="hostimage">
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <?php
    display_message($error, false, $msg);
    ?>

    <p>
        <?php
        echo _("This host appears to make use of an advanced configuration using the Core Config Manager.");
        if (!user_can_access_ccm()) {
            echo '<br>'._("Contact your Nagios administrator to modify the settings for this host.");
        }
        ?>
    </p>

    <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
        <input type="hidden" name="show" value="hostdetail">
        <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-default" name="backButton"><i class="fa fa-chevron-left l"></i> <?php echo _('Back'); ?></button>
            <?php if (user_can_access_ccm()) { ?>
            <a href="<?php echo $url; ?>" target="_top" class="btn btn-sm btn-info" style="margin-left: 5px;"><i class="fa fa-share l"></i> <?php echo _('Edit in CCM'); ?></a>
            <?php } ?>
        </div>
    </form>

    <?php
    exit();
}


/**
 * @param $host
 */
function check_host_config_prereqs($host)
{

    if (is_host_configurable($host) == false)
        redirect_host_config();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function do_config_host($error = false, $msg = "")
{
    global $request;

    // check session
    check_nagios_session_protector();

    // grab variables
    $host = grab_request_var("host", "");
    $return = grab_request_var("return", "");

    // user cancelled, so redirect them
    if (isset($request["cancelButton"])) {
        $url = get_return_url($return, $host);
        header("Location: " . $url);
        exit();
    }

    $original_host_s = grab_request_var("originalhost", "");
    if ($original_host_s == "") {
        $original_host = array();
    } else {
        $original_host = unserialize(base64_decode($original_host_s));
    }

    // grab config variables
    $address = grab_request_var("address");
    $alias = grab_request_var("alias");
    $display_name = grab_request_var("display_name");
    $check_interval = grab_request_var("check_interval");
    $retry_interval = grab_request_var("retry_interval");
    $max_check_attempts = grab_request_var("max_check_attempts");
    $check_command = grab_request_var("check_command");
    $first_notification_delay = grab_request_var("first_notification_delay");
    $notification_interval = grab_request_var("notification_interval");
    $notification_options = grab_request_var("notification_options");
    $notification_targets = grab_request_var("notification_targets", array());
    $contact_id = grab_request_var("contact_id", array());
    $contactgroup_id = grab_request_var("contactgroup_id", array());
    $hostgroup_id = grab_request_var("hostgroup_id", array());
    $parenthost_id = grab_request_var("parenthost_id", array());

    // resolve contact names
    $contact_names = "";
    $total_contacts = 0;
    // this user
    if (array_key_exists("myself", $notification_targets)) {
        $contact_names .= get_user_attr(0, "username");
        $total_contacts++;
    }
    // additional individual contacts
    if (array_key_exists("contacts", $notification_targets)) {
        $ids = "";
        foreach ($contact_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "contact_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_contact_objects($args);
            foreach ($xml->contact as $c) {
                if ($total_contacts > 0)
                    $contact_names .= ",";
                $contact_names .= $c->contact_name;
                $total_contacts++;
            }
        }
    }
    //echo "<BR>CONTACTS: $contact_names<BR>";

    // resolve contactgroup names
    $contactgroup_names = "";
    $total_contactgroups = 0;
    // additional individual contactgroups
    if (array_key_exists("contactgroups", $notification_targets)) {
        $ids = "";
        foreach ($contactgroup_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "contactgroup_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_contactgroup_objects($args);
            foreach ($xml->contactgroup as $cg) {
                if ($total_contactgroups > 0)
                    $contactgroup_names .= ",";
                $contactgroup_names .= $cg->contactgroup_name;
                $total_contactgroups++;
            }
        }
    }
    //echo "<BR>CONTACTGROUPS: $contactgroup_names<BR>";

    // resolve hostgroup names
    $hostgroup_names = "";
    $total_hostgroups = 0;
    $ids = "";
    if (is_array($hostgroup_id)) {
        foreach ($hostgroup_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "hostgroup_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_hostgroup_objects($args);
            foreach ($xml->hostgroup as $hg) {
                if ($total_hostgroups > 0)
                    $hostgroup_names .= ",";
                $hostgroup_names .= $hg->hostgroup_name;
                $total_hostgroups++;
            }
        }
    }
    //echo "<BR>HOSTGROUPS: $hostgroup_names<BR>";

    // resolve parent host names
    $parenthost_names = "";
    $total_parenthosts = 0;
    $ids = "";
    if (is_array($parenthost_id)) {
        foreach ($parenthost_id as $id => $val) {
            $ids .= "," . $id;
        }
        if ($ids != "") {
            $args = array(
                "is_active" => 1,
                "host_id" => "in:" . $ids,
            );
            //echo "IDS: $ids<BR>";
            //print_r($args);	
            $xml = get_xml_host_objects($args);
            foreach ($xml->host as $h) {
                if ($total_parenthosts > 0)
                    $parenthost_names .= ",";
                $parenthost_names .= $h->host_name;
                $total_parenthosts++;
            }
        }
    }
    //echo "<BR>PARENTHOSTS: $parenthost_names<BR>";

    //exit();

    // new object config array
    $new_host = $original_host;

    // apply config settings to new object
    if ($address != "")
        $new_host["address"] = $address;
    if ($alias != "")
        $new_host["alias"] = $alias;
    if ($display_name != "")
        $new_host["display_name"] = $display_name;
    if ($check_interval != "")
        $new_host["check_interval"] = $check_interval;
    if ($retry_interval != "")
        $new_host["retry_interval"] = $retry_interval;
    if ($max_check_attempts != "")
        $new_host["max_check_attempts"] = $max_check_attempts;
    if ($check_command != "")
        $new_host["check_command"] = $check_command;
    if ($notification_interval != "")
        $new_host["notification_interval"] = $notification_interval;

    // Contacts (only if modified)
    if ($contact_names != "") {
        $new_host["contacts"] = $contact_names;
        if (substr(grab_array_var($original_host, "contacts"), 0, 1) == "+") {
            $new_host["contacts"] = "+" . $new_host["contacts"];
        }
    } else {
        // Setting contacts to none if none using a query instead of the broken importing
        $orig_contacts = grab_array_var($original_host, "contacts", "");
        if ($orig_contacts != "null" && $orig_contacts != "+" && $orig_contacts != "") {
            $hid = nagiosql_get_host_id($host);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_host` SET `contacts` = 0 WHERE `id` = '.$hid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkHostToContact` WHERE `idMaster` = '.$hid);
            unset($new_host['contacts']);
        }
    }

    // Contactgroups (only if modified)
    if ($contactgroup_names != "") {
        $new_host["contact_groups"] = $contactgroup_names;
        if (substr(grab_array_var($original_host, "contact_groups"), 0, 1) == "+") {
            $new_host["contact_groups"] = "+" . $new_host["contact_groups"];
        }
    } else {
        // Setting contacts to none if none using a query instead of the broken importing
        $orig_contact_groups = grab_array_var($original_host, "contact_groups", "");
        if ($orig_contact_groups != "null" && $orig_contact_groups != "+" && $orig_contact_groups != "") {
            $hid = nagiosql_get_host_id($host);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_host` SET `contact_groups` = 0 WHERE `id` = '.$hid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkHostToContactgroup` WHERE `idMaster` = '.$hid);
            unset($new_host['contact_groups']);
        }
    }

    // hostgroups (only if modified)
    if ($hostgroup_names != "") {
        $new_host["hostgroups"] = $hostgroup_names;
        if (substr(grab_array_var($original_host, "hostgroups"), 0, 1) == "+")
            $new_host["hostgroups"] = "+" . $new_host["hostgroups"];
    } else {
        $ohg = grab_array_var($original_host, "hostgroups");
        if ($ohg == "null") {
            $new_host["hostgroups"] = "null";
        } else {
            $hid = nagiosql_get_host_id($host);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_host` SET `hostgroups` = 0 WHERE `id` = '.$hid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkHostToHostgroup` WHERE `idMaster`=' . $hid);
            unset($new_host["hostgroups"]);
        }
    }

    // parents (only if modified)
    if ($parenthost_names != "") {
        $new_host["parents"] = $parenthost_names;
        if (substr(grab_array_var($original_host, "parents"), 0, 1) == "+")
            $new_host["parents"] = "+" . $new_host["parents"];
    } else {
        $op = grab_array_var($original_host, "parents");
        if ($op == "null") {
            $new_host["parents"] = "null";
        } else {
            $hid = nagiosql_get_host_id($host);
            exec_sql_query(DB_NAGIOSQL, 'UPDATE `tbl_host` SET `parents` = 0 WHERE `id` = '.$hid);
            exec_sql_query(DB_NAGIOSQL, 'DELETE FROM `tbl_lnkHostToHost` WHERE `idMaster`=' . $hid);
            unset($new_host["parents"]);
        }
    }

    // notification options
    // defaults (needed to override old settings when we re-import into NagiosQL
    $new_host["notifications_enabled"] = "1";
    if (isset($original_host["notification_options"]) && substr(grab_array_var($original_host, "notification_options"), 0, 1) == "n")
        $new_host["notification_options"] = "d,u,r,f,s";

    if ($notification_options == "immediate") {
        if (isset($new_host["first_notification_delay"])) {
            $new_host["first_notification_delay"] = "0";
        }
    } else if ($notification_options == "delayed") {
        $new_host["first_notification_delay"] = $first_notification_delay;
    } else if ($notification_options == "none") {
        $new_host["notification_options"] = "n";
        $new_host["notifications_enabled"] = "0";
    }

    //echo "<BR>NEW HOST:<BR>";
    //print_r($new_host);

    // COMMIT THE HOST

    // log it
    send_to_audit_log(_("User reconfigured host '") . $host . "'", AUDITLOGTYPE_MODIFY, "", "", "", print_r($new_host, true));

    // create the import file
    $fname = $host; // use the hostname as part of the import file
    $fh = create_nagioscore_import_file($fname);

    // write the object definition to file
    fprintf($fh, "define host {\n");
    foreach ($new_host as $var => $val) {
        fprintf($fh, $var . "\t" . $val . "\n");
    }
    fprintf($fh, "}\n");

    // commit the import file
    fclose($fh);
    commit_nagioscore_import_file($fname);

    show_host_commit_complete();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_host_commit_complete($error = false, $msg = "")
{

    // grab variables
    $host = grab_request_var("host", "");

    do_page_start(array("page_title" => _("Configure Host")), true);
    ?>

    <h1><?php echo _("Configure Host"); ?></h1>

    <div class="hoststatusdetailheader">
        <div class="hostimage">
            <!--image-->
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <?php
    display_message($error, false, $msg);
    ?>

    <ul class="commandresult" style="margin: 25px 0 10px 0;">
        <?php
        $id = submit_command(COMMAND_NAGIOSCORE_APPLYCONFIG);
        if ($id > 0) {
            echo "<li class='commandresultwaiting' id='commandwaiting'>" . _("Configuration submitted for processing"). "...</li>";
        } else {
            echo "<li class='commandresulterror'>" . _("An error occurred during command submission.  If this problem persists, contact your Nagios administrator.") . "</li>\n";
        }
        ?>
    </ul>

    <div id="commandsuccesscontent" style="visibility: hidden;">

        <div class="message">
            <ul class="infoMessage">
                <li><?php echo _('Configuration applied successfully and backend was restarted.'); ?></li>
            </ul>
        </div>

        <h5 class="ul"><?php echo _("Host Re-Configuration Successful"); ?></h5>

        <p><?php echo _("The host has successfully been re-configured with the new settings."); ?></p>


        <?php
        $hoststatus_link = get_host_status_link($host);
        ?>
        <ul>
            <li><a href="<?php echo $hoststatus_link; ?>"
                   target="_blank" rel="noreferrer"><?php echo _("View status details for"); ?> <?php echo encode_form_val($host); ?></a>
            </li>
            <?php
            if (is_admin() == true) {
                ?>
                <li><a href="<?php echo get_base_url(); ?>admin/?xiwindow=coreconfigsnapshots.php" target="_top">
                        <?php echo _("View the latest configuration snapshots"); ?></a></li>
            <?php
            }
            ?>
        </ul>

        <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
            <input type="hidden" name="show" value="hostdetail">
            <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">

            <div id="formButtons" style="margin-top: 20px;">
                <button type="submit" class="btn btn-sm btn-primary" name="backButton"><?php echo _('Continue'); ?> <i class="fa fa-chevron-right r"></i></button>
            </div>
        </form>

    </div>

    <div id="commanderrorcontent" style="visibility: hidden;">

        <div class="message">
            <ul class="errorMessage">
                <li><?php echo _('Configuration error. Could not apply configuration.'); ?></li>
            </ul>
        </div>

        <h5 class="ul"><?php echo _("Host Re-Configuration Failed"); ?></h5>

        <p><?php echo _("A failure occurred while attempting to re-configure the host with the new settings."); ?></p>

        <?php
        if (is_admin() == true) {
            ?>
            <p><a href="<?php echo get_base_url(); ?>admin/?xiwindow=coreconfigsnapshots.php" target="_top">
                    <?php echo _("View the latest configuration snapshots"); ?></a></p>
        <?php
        }
        ?>
        <form method="get" action="<?php echo get_base_url() . "includes/components/xicore/status.php"; ?>">
            <input type="hidden" name="show" value="hostdetail">
            <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>">

            <div id="formButtons" style="margin-top: 10px;">
                <button type="submit" class="btn btn-sm btn-default" name="backButton"><i class="fa fa-chevron-left l"></i> <?php echo _('Back'); ?></button
            </div>
        </form>

    </div>

    <script type="text/javascript">

        setTimeout(get_apply_config_result, 1000, <?php echo $id; ?>);

        function get_apply_config_result(command_id) {

            $('#commandwaiting').html('<?php echo _("Waiting for configuration verification"); ?>');

            $(this).everyTime(1 * 1000, "commandstatustimer", function (i) {

                $(".commandresultwaiting").append(".");

                var csdata = get_ajax_data("getcommandstatus", command_id);
                eval('var csobj=' + csdata);
                if (csobj.status_code == 2) {
                    if (csobj.result_code == 0) {
                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresultok");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).css("visibility", "visible");
                        });
                        $('#commandwaiting').html("<?php echo _('Configuration applied successfully.'); ?>");
                    }
                    else {
                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresulterror");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).css("display", "none")
                        });
                        $('#commanderrorcontent').each(function () {
                            $(this).css("visibility", "visible")
                        });
                        $('#commandwaiting').html("<?php echo _('Configuration verification failed.'); ?>");
                    }
                    $(this).stopTime("commandstatustimer");
                }
            });

        }
    </script>

    <?php

    do_page_end(true);
    exit();
}


/**
 * @param        $return
 * @param        $host
 * @param string $service
 *
 * @return string
 */
function get_return_url($return, $host, $service = "")
{

    switch ($return) {
        case "servicedetail";
            $url = get_service_status_detail_link($host, $service);
            break;
        case "hostdetail";
            $url = get_host_status_detail_link($host);
            break;
        default:
            $url = "main.php";
            break;
    }

    return $url;
}
