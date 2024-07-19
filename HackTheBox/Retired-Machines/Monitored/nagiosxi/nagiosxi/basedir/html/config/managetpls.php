<?php
//
// Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Check for configuration authorization
if (!is_authorized_to_configure_objects() || is_readonly_user()) {
    header("Location: " . get_base_url());
}


route_request();


function route_request()
{
    $cmd = grab_request_var('cmd', '');

    switch ($cmd)
    {
        case "delete":
            do_delete();
            break;
        case "multidelete":
            do_multi_delete();
            break;
        case "edit":
            draw_edit_page();
            break;
        case "update":
            do_update();
            break;
        default:
            draw_page();
            break;
    }
}


function draw_page()
{
    // Grab all templates
    $tpls = get_templates();
    $tpls = array_merge($tpls['global'], $tpls['user']);

    $msg = '';
    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
    }

    do_page_start(array("page_title" => _('Manage Templates')), true);
?>

    <script type="text/javascript">
    $(document).ready(function() {
        $('.btn-delete').click(function() {
            return confirm("Are you sure you want to delete this template?");
        });

        $('#select-tpls').change(function() {
            if (confirm('Are you sure you want to delete all checked templates?')) {
                $('.tpl-manage').submit();
            }
        });
    });
    </script>
    
    <form action="managetpls.php?cmd=multidelete" method="post" class="tpl-manage">

    <h1><?php echo _('Manage Templates'); ?></h1>
    <p><?php echo _('Templates allow users and administrators to set defaults for the advanced settings of hosts/services created by configuration wizards.'); ?></p>

    <?php echo display_message(false, false, $msg); ?>

    <table class="table table-condensed table-bordered table-hover table-striped table-auto-width" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 28px;"></th>
                <th><?php echo _('Title'); ?></th>
                <th style="width: 32px;"></th>
                <th style="width: 60px;" class="center"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (count($tpls) > 0) {
                foreach ($tpls as $tpl) {
                ?>
                <tr>
                    <td class="center">
                        <?php if (($tpl['global'] == 1 && is_admin()) || $tpl['global'] == 0) { ?>
                        <input type="checkbox" name="tpl_ids[]" value="<?php echo $tpl['id']; ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($tpl['global'] == 1) { echo '<i class="fa fa-globe fa-14 fa-m tt-bind" title="'._('Global').'"></i>'; } ?>
                        <?php echo $tpl['title']; ?>
                    </td>
                    <td class="center"><i class="fa fa-file-text-o fa-14 fa-m pop" title="<?php echo _('Description'); ?>" data-content="<?php echo $tpl['desc']; ?>"></i></td>
                    <td class="center">
                        <?php if (($tpl['global'] == 1 && is_admin()) || $tpl['global'] == 0) { ?>
                        <a href="managetpls.php?cmd=edit&id=<?php echo $tpl['id']; ?>" class="tt-bind" data-placement="right" title="<?php echo _('Edit'); ?>"><img src="<?php echo theme_image('pencil.png'); ?>"></a>
                        <a href="managetpls.php?cmd=delete&id=<?php echo $tpl['id']; ?>" class="btn-delete tt-bind" data-placement="right" title="<?php echo _('Delete'); ?>"><img src="<?php echo theme_image('cross.png'); ?>"></a>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                }
            } else {
            ?>
            <tr>
                <td colspan="4"><?php echo _('No templates have been created yet.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php echo _('With selected'); ?>:
    <select class="form-control" id="select-tpls">
        <option></option>
        <option><?php echo _('Delete'); ?></option>
    </select>

    </form>

<?php
    do_page_end(true);
}


function draw_edit_page()
{
    $id = grab_request_var('id', '');
    if (empty($id)) {
        header('Location: managetpls.php');
    }

    $tpls = get_templates();
    if (is_admin()) {
        $tpls = array_merge($tpls['global'], $tpls['user']);
    } else {
        $tpls = $tpls['user'];
    }

    // Find the tpl we neeed
    $tpl = array();
    foreach ($tpls as $t) {
        if ($id == $t['id']) {
            $tpl = $t;
            break;
        }
    }

    if (empty($tpl)) {
        header('Location: managetpls.php');
    }

    $check_interval = grab_array_var($tpl['tpl'], 'check_interval', 5);
    $retry_interval = grab_array_var($tpl['tpl'], 'retry_interval', 1);
    $max_check_attempts = grab_array_var($tpl['tpl'], 'max_check_attempts', 5);

    $notification_interval = grab_array_var($tpl['tpl'], 'notification_interval', 15);
    $first_notification_delay = grab_array_var($tpl['tpl'], 'first_notification_delay', 60);
    $notification_options = grab_array_var($tpl['tpl'], 'notification_options', array());
    $notification_targets = grab_array_var($tpl['tpl'], 'notification_targets', array());
    if (!is_array($notification_targets)) { $notification_targets = xiunserialize(base64_decode($notification_targets)); }
    $contact_id = grab_array_var($tpl['tpl'], 'contact_id', array());
    if (!is_array($contact_id)) { $contact_id = xiunserialize(base64_decode($contact_id)); }
    $contacts = grab_array_var($tpl['tpl'], 'contacts', array());
    if (!is_array($contacts)) { $contacts = xiunserialize(base64_decode($contacts)); }
    $contactgroup_id = grab_array_var($tpl['tpl'], 'contactgroup_id', array());
    if (!is_array($contactgroup_id)) { $contactgroup_id = xiunserialize(base64_decode($contactgroup_id)); }
    $contactgroups = grab_array_var($tpl['tpl'], 'contactgroups', array());
    if (!is_array($contactgroups)) { $contactgroups = xiunserialize(base64_decode($contactgroups)); }

    $parenthost_id = grab_array_var($tpl['tpl'], 'parenthost_id', array());
    if (!is_array($parenthost_id)) { $parenthost_id = xiunserialize(base64_decode($parenthost_id)); }
    $parenthosts = grab_array_var($tpl['tpl'], 'parenthosts', array());
    if (!is_array($parenthosts)) { $parenthosts = xiunserialize(base64_decode($parenthosts)); }
    $hostgroup_id = grab_array_var($tpl['tpl'], 'hostgroup_id', array());
    if (!is_array($hostgroup_id)) { $hostgroup_id = xiunserialize(base64_decode($hostgroup_id)); }
    $hostgroups = grab_array_var($tpl['tpl'], 'hostgroups', array());
    if (!is_array($hostgroups)) { $hostgroups = xiunserialize(base64_decode($hostgroups)); }
    $servicegroup_id = grab_array_var($tpl['tpl'], 'servicegroup_id', array());
    if (!is_array($servicegroup_id)) { $servicegroup_id = xiunserialize(base64_decode($servicegroup_id)); }
    $servicegroups = grab_array_var($tpl['tpl'], 'servicegroups', array());
    if (!is_array($servicegroups)) { $servicegroups = xiunserialize(base64_decode($servicegroups)); }

    // Use the selected tpl to populate the edit page
    //print "<pre>";
    //print_r($tpl);
    //print "</pre>";

    do_page_start(array("page_title" => _('Edit Template')), true);
?>

<script>
$(document).ready(function() {
    $('#tabs').tabs().show();
});
</script>

<form action="managetpls.php?cmd=update" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <h1><?php echo _('Edit Template'); ?></h1>

    <div id="tabs" class="hide">
        <ul>
            <li><a href="#general"><?php echo _('General'); ?></a></li>
            <li><a href="#monitoring"><?php echo _('Monitoring'); ?></a></li>
            <li><a href="#notification"><?php echo _('Notification'); ?></a></li>
            <li><a href="#connections"><?php echo _('Groups & Parents'); ?></a></li>
        </ul>

        <div id="general">
            <table class="table table-condensed table-no-border table-auto-width">
                <tr>
                    <td><label><?php echo _('Title'); ?>:</label></td>
                    <td><input type="text" class="form-control" name="title" value="<?php echo $tpl['title']; ?>" style="width: 240px;"></td>
                </tr>
                <tr>
                    <td class="vt"><label><?php echo _('Description'); ?>:</label></td>
                    <td>
                        <textarea class="form-control" name="desc" style="width: 400px; height: 100px;"><?php echo $tpl['desc']; ?></textarea>
                    </td>
                </tr>
                <?php if (is_admin()) { ?>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="global" id="tpl-global" <?php if ($tpl['global']) { echo "checked"; } ?>> <?php echo _('Make global template'); ?>
                        </label>
                        <i class="fa fa-question-circle fa-14 pop" title="<?php echo _('Global Templates'); ?>" data-content="<?php echo _('Global templates are templates that all users can use. Normally templates are available for the user who created them. Only admins can create global templates.'); ?>" style="vertical-align: middle;"></i>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <div id="monitoring">
            <p><?php echo _("Define basic parameters that determine how the host and service(s) should be monitored."); ?></p>
            <table class="table table-condensed table-no-border table-auto-width table-padded">
                <tbody>
                    <tr>
                        <td>
                            <b><?php echo _("Under normal circumstances"); ?>:</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="well text-pad">
                            <?php echo _("Monitor the host and service(s) every"); ?>
                            <input type="text" size="2" name="check_interval" id="check_interval" value="<?php echo encode_form_val($check_interval); ?>" class="textfield form-control condensed">
                            <?php echo _("minutes."); ?>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>    
                            <b><?php echo _("When a potential problem is first detected"); ?>:</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="well text-pad">
                            <?php echo _("Re-check the host and service(s) every"); ?>
                            <input type="text" size="2" name="retry_interval" id="retry_interval" value="<?php echo encode_form_val($retry_interval); ?>" class="textfield form-control condensed" >
                            <?php echo _("minutes up to"); ?>
                            <input type="text" size="2" name="max_check_attempts" id="max_check_attempts" value="<?php echo encode_form_val($max_check_attempts); ?>" class="textfield form-control condensed">
                            <?php echo _("times before generating an alert"); ?>.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="notification">
            <p><?php echo _("Define basic parameters that determine how notifications should be sent for the host and service(s)."); ?></p>
            <table class="table table-condensed table-no-border table-auto-width table-padded">
                <tbody>
                    <tr>
                        <td>
                            <b><?php echo _("When a problem is detected"); ?>:</b>
                        </td>
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
                                    <input type="radio" name="notification_options"  value="delayed" <?php echo is_checked($notification_options, "delayed"); ?>>
                                    <?php echo _("Wait"); ?>
                                    <input type="text" size="2" name="first_notification_delay" id="first_notification_delay" value="<?php echo encode_form_val($first_notification_delay); ?>" class="textfield form-control condensed">
                                    <?php echo _("minutes before sending a notification"); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>
                            <b><?php echo _("If problems persist"); ?>:</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="well text-pad">
                            <?php echo _("Send a notification every"); ?>
                            <input type="text" size="2" name="notification_interval" id="notification_interval" value="<?php echo encode_form_val($notification_interval); ?>" class="textfield form-control condensed">
                            <?php echo _("minutes until the problem is resolved."); ?>
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td>
                            <b><?php echo _("Send alert notifications to"); ?>:</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="well" style="padding: 10px 15px;">
                            <script type="text/javascript">
                                function check_contacts() { $('#notification_targets_contacts').attr('checked', true); }
                                function check_contactgroups() { $('#notification_targets_contactgroups').attr('checked', true); }
                            </script>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notification_targets[myself]" id="notification_targets_myself" <?php echo is_checked(@$notification_targets["myself"], "on"); ?>>
                                    <?php echo _("Myself"); ?>
                                    (<a href="<?php echo get_base_url() . "account/?xiwindow=notifyprefs.php"; ?>" target="_blank" rel="noreferrer"><?php echo _("Adjust my settings"); ?></a>)
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notification_targets[contacts]" id="notification_targets_contacts" <?php echo is_checked(@$notification_targets["contacts"], "on"); ?>>
                                    <?php echo _("Other individual contacts"); ?>
                                </label>
                            </div>
                            <div class="sel-users-new fixed">
                                <?php
                                $xml = get_xml_contact_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                $username = get_user_attr(0, 'username');
                                foreach ($xml->contact as $c) {

                                    // Skip the same contact as the current user - this is taken care of by the "myself" option above
                                    if (!strcmp(strval($c->contact_name), $username))
                                        continue;

                                    if (array_key_exists(strval($c->attributes()->id), $contact_id))
                                        $ischecked = "CHECKED";
                                    else if (array_key_exists(strval($c->contact_name), $contacts))
                                        $ischecked = "CHECKED";
                                    else
                                        $ischecked = "";

                                    echo "<div class='checkbox'><label><input type='checkbox' name='contact_id[" . $c->attributes()->id . "]' " . $ischecked . " onclick='check_contacts()'>" . $c->alias . " (" . $c->contact_name . ")</label></div>";
                                }
                                ?>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notification_targets[contactgroups]" id="notification_targets_contactgroups" <?php echo is_checked(@$notification_targets["contactgroups"], "on"); ?>>
                                    <?php echo _("Specific contact groups"); ?>
                                </label>
                            </div>
                            <div class="sel-users-new fixed">
                                <?php
                                $xml = get_xml_contactgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                foreach ($xml->contactgroup as $c) {

                                    if (array_key_exists(strval($c->attributes()->id), $contactgroup_id))
                                        $ischecked = "CHECKED";
                                    else if (array_key_exists(strval($c->contactgroup_name), $contactgroups))
                                        $ischecked = "CHECKED";
                                    else
                                        $ischecked = "";

                                    echo "<div class='checkbox'><label><input type='checkbox' name='contactgroup_id[" . $c->attributes()->id . "]' " . $ischecked . " onclick='check_contactgroups()'>" . $c->alias . " (" . $c->contactgroup_name . ")</label></div>";
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="connections">
            <h5 class="ul"><?php echo _("Host Groups"); ?></h5>
            <div class="well freeform">
                <p><?php echo _("Define which hostgroup(s) the monitored host should belong to (if any)."); ?></p>
                <div class="sel-users-new">
                    <?php
                    $xml = get_xml_hostgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                    foreach ($xml->hostgroup as $c) {

                        if (array_key_exists(strval($c->attributes()->id), $hostgroup_id))
                            $ischecked = "CHECKED";
                        else if (array_key_exists(strval($c->hostgroup_name), $hostgroups))
                            $ischecked = "CHECKED";
                        else
                            $ischecked = "";

                        echo "<div class='checkbox'><label><input type='checkbox' name='hostgroup_id[" . $c->attributes()->id . "]' " . $ischecked . " >" . $c->alias . " (" . $c->hostgroup_name . ")</label></div>";
                    }
                    ?>
                </div>
            </div>
            <h5 class="ul"><?php echo _("Service Groups"); ?></h5>
            <div class="well freeform">
                <p><?php echo _("Define which servicegroup(s) the monitored service(s) should belong to (if any)."); ?></p>
                <div class="sel-users-new">
                <?php
                $xml = get_xml_servicegroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                foreach ($xml->servicegroup as $c) {

                    if (array_key_exists(strval($c->attributes()->id), $servicegroup_id))
                        $ischecked = "CHECKED";
                    else if (array_key_exists(strval($c->servicegroup_name), $servicegroups))
                        $ischecked = "CHECKED";
                    else
                        $ischecked = "";

                    echo "<div class='checkbox'><label><input type='checkbox' name='servicegroup_id[" . $c->attributes()->id . "]' " . $ischecked . " >" . $c->alias . " (" . $c->servicegroup_name . ")</label></div>";
                }
                ?>
                </div>
            </div>
            <h5 class="ul"><?php echo _("Parent Host"); ?></h5>
            <div class="well freeform">
                <p><?php echo _("Define which host(s) are considered the parents of the the monitored host (if any). Note: Typically only one (1) host is specified as a parent."); ?></p>
                <div class="sel-users-new">
                    <?php
                    $xml = get_xml_host_objects(array("is_active" => 1, "orderby" => "host_name:a"));
                    foreach ($xml->host as $c) {

                        if (array_key_exists(strval($c->attributes()->id), $parenthost_id))
                            $ischecked = "CHECKED";
                        else if (array_key_exists(strval($c->host_name), $parenthosts))
                            $ischecked = "CHECKED";
                        else
                            $ischecked = "";

                        echo "<div class='checkbox'><label><input type='checkbox' name='parenthost_id[" . $c->attributes()->id . "]' " . $ischecked . " >" . $c->host_name . " (" . $c->address . ")</label></div>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>

    <div>
        <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Save Changes'); ?></button>
        <a href="managetpls.php" class="btn btn-sm btn-default"><?php echo _('Cancel'); ?></a>
    </div>

</form>

<?php
    do_page_end(true);
}


function do_update()
{
    $id = grab_request_var('id', '');
    if (empty($id)) {
        header('Location: managetpls.php');
    }

    $tpls = get_templates();
    if (is_admin()) {
        $tpls = array_merge($tpls['global'], $tpls['user']);
    } else {
        $tpls = $tpls['user'];
    }

    // Find the tpl we neeed
    $arr = array();
    foreach ($tpls as $t) {
        if ($id == $t['id']) {
            $arr = $t;
            break;
        }
    }

    if (empty($arr)) {
        header('Location: managetpls.php');
    }

    // Set old global
    $old_global = $arr['global'];

    $title = grab_request_var('title', '');
    $desc = grab_request_var('desc', '');
    $global = grab_request_var('global', 0);

    $check_interval = grab_request_var('check_interval', '');
    $retry_interval = grab_request_var('retry_interval', '');
    $max_check_attempts = grab_request_var('max_check_attempts', '');

    if (!empty($title)) { $arr['title'] = $title; }
    if (!empty($desc)) { $arr['desc'] = $desc; }
    $arr['global'] = $global;

    if (!empty($check_interval)) { $arr['tpl']['check_interval'] = $check_interval; }
    if (!empty($retry_interval)) { $arr['tpl']['retry_interval'] = $retry_interval; }
    if (!empty($max_check_attempts)) { $arr['tpl']['max_check_attempts'] = $max_check_attempts; }

    $notification_interval = grab_request_var('notification_interval', '');
    $first_notification_delay = grab_request_var('first_notification_delay', '');
    $notification_options = grab_request_var('notification_options', '');
    $notification_targets = grab_request_var('notification_targets', array());
    $contact_id = grab_request_var('contact_id', array());
    $contacts = grab_request_var('contacts', array());
    $contactgroup_id = grab_request_var('contactgroup_id', array());
    $contactgroups = grab_request_var('contactgroups', array());

    if ($notification_interval != '') { $arr['tpl']['notification_interval'] = $notification_interval; }
    if ($first_notification_delay != '') { $arr['tpl']['first_notification_delay'] = $first_notification_delay; }
    if ($notification_options != '') { $arr['tpl']['notification_options'] = $notification_options; }
    $arr['tpl']['notification_targets'] = base64_encode(json_encode($notification_targets));
    $arr['tpl']['contact_id'] = base64_encode(json_encode($contact_id));
    $arr['tpl']['contacts'] = base64_encode(json_encode($contacts));
    $arr['tpl']['contactgroup_id'] = base64_encode(json_encode($contactgroup_id));
    $arr['tpl']['contactgroups'] = base64_encode(json_encode($contactgroups));

    $parenthost_id = grab_request_var('parenthost_id', array());
    $parenthosts = grab_request_var('parenthosts', array());
    $hostgroup_id = grab_request_var('hostgroup_id', array());
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroup_id = grab_request_var('servicegroup_id', array());
    $servicegroups = grab_request_var('servicegroups', array());

    $arr['tpl']['parenthost_id'] = base64_encode(json_encode($parenthost_id));
    $arr['tpl']['parenthosts'] = base64_encode(json_encode($parenthosts));
    $arr['tpl']['hostgroup_id'] = base64_encode(json_encode($hostgroup_id));
    $arr['tpl']['hostgroups'] = base64_encode(json_encode($hostgroups));
    $arr['tpl']['servicegroup_id'] = base64_encode(json_encode($servicegroup_id));
    $arr['tpl']['servicegroups'] = base64_encode(json_encode($servicegroups));

    if ($old_global == $arr['global']) {
        // Save the tpl
        if ($arr['global']) {
            update_template($arr, true);
        } else {
            update_template($arr);
        }
    } else {

        // Remove template
        delete_template($id);

        // Add a new template in the proper location
        add_template($arr);
    }

    $_SESSION['msg'] = _('Template has been updated successfully.');
    header('Location: managetpls.php');
}


function do_delete()
{
    $id = grab_request_var('id', '');
    delete_template($id);

    $_SESSION['msg'] = _('Template removed successfully.');
    header('Location: managetpls.php');
}


function do_multi_delete()
{
    $tpl_ids = grab_request_var('tpl_ids', array());
    foreach ($tpl_ids as $i) {
        delete_template($i);
    }

    $_SESSION['msg'] = _('Template(s) removed successfully.');
    header('Location: managetpls.php');
}
