<?php
//
// Unconfigured Objects
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../includes/configwizards.inc.php');


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
    $delete = grab_request_var("delete");
    $configure = grab_request_var("configure");
    $purge = grab_request_var("purge");
    $save = grab_request_var("save");

    if ($delete == 1) {
        do_delete();
    } else if ($configure == 1) {
        do_configure();
    } else if ($purge == 1) {
        do_purge();
    } else if ($save == 1) {
        do_save();
    } else {
        show_objects();
    }
}


function do_configure()
{
    
    $hosts = grab_request_var("host", array());
    $services = grab_request_var("service", array());

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    $wizard_url = "https://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards/Unconfigured-Passive-Object-Wizard/details";

    // Check for errors
    if (in_demo_mode() == true) {
        $errmsg = _("Changes are disabled while in demo mode.");
        $errors++;
    }
    if (count($hosts) == 0 && count($services) == 0) {
        $errmsg = _("No objects selected.");
        $errors++;
    }
    if (!file_exists(get_base_dir() . "/includes/configwizards/passiveobject/passiveobject.inc.php")) {
        $errmsg = _("You must install the unconfigured passive object wizard to configure the selected hosts and services. You can get the wizard"). "<a href='" . $wizard_url . "' target='_blank'>" . _("here") . "</a>.";
        $errors++;
    }

    if ($errors > 0) {
        flash_message($errmsg, FLASH_MSG_ERROR);
        header("Location: missingobjects.php");
        exit();
    }

    $url = "../config/monitoringwizard.php?wizard=passiveobject&update=1&nextstep=3&nsp=" . get_nagios_session_protector_id();
    foreach ($hosts as $host_name => $id) {
        $url .= "&host[" . urlencode($host_name) . "]=1";
    }
    foreach ($services as $host_name => $service_name) {
        $url .= "&service[" . urlencode($host_name) . "]=" . urlencode($service_name);
    }

    header("Location: " . $url);
}


function do_delete()
{
    $hosts = grab_request_var("host", array());
    $services = grab_request_var("service", array());

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Check for errors
    if (in_demo_mode() == true) {
        $errmsg = _("Changes are disabled while in demo mode.");
        $errors++;
    }
    if (count($hosts) == 0 && count($services) == 0) {
        $errmsg = _("No objects selected.");
        $errors++;
    }

    if ($errors > 0) {
        flash_message($errmsg, FLASH_MSG_ERROR);
        header("Location: missingobjects.php");
        exit();
    }

    // Load object file
    clearstatcache();
    $datas = file_get_contents(get_root_dir() . "/var/corelog.newobjects");
    $newobjects = unserialize($datas);

    // Delete hosts
    foreach ($hosts as $hn => $id) {
        send_to_audit_log("User deleted host '" . $hn . "' from unconfigured objects", AUDITLOGTYPE_DELETE);
        unset($newobjects[$hn]);
    }

    // Delete services
    foreach ($services as $hn => $sn) {
        send_to_audit_log("User deleted service '" . $sn . "' (on host '" . $hn . "') from unconfigured objects", AUDITLOGTYPE_DELETE);
        unset($newobjects[$hn]['services'][$sn]);

        // Did we delete all the services?
        if (count($newobjects[$hn]['services']) == 0) {
            unset($newobjects[$hn]['services']);
        }
    }

    file_put_contents(get_root_dir() . "/var/corelog.newobjects", serialize($newobjects));

    flash_message(_("Successfully deleted object."));
    header("Location: missingobjects.php");
}


/**
 * Save the changes to the auto config setup section
 */
function do_save()
{
    $auto_import_unconfigured = intval(grab_request_var('auto_import_unconfigured'));
    $auto_apply_unconfigured = intval(grab_request_var('auto_apply_unconfigured'));
    $auto_import_service_tpl = grab_request_var('auto_import_service_tpl', 'xiwizard_passive_service');
    $auto_import_host_tpl = grab_request_var('auto_import_host_tpl', 'xiwizard_passive_service');
    $auto_import_volatile = grab_request_var('auto_import_volatile', 0);
    $auto_import_stalking = grab_request_var('auto_import_stalking', 0);
    $auto_import_notifications = intval(grab_request_var('auto_import_notifications', 1));
    $auto_import_contacts = grab_request_var('auto_import_contacts', array());
    $auto_import_contactgroups = grab_request_var('auto_import_contactgroups', array());
    $auto_import_hostgroups = grab_request_var('auto_import_hostgroups', array());
    $auto_import_servicegroups = grab_request_var('auto_import_servicegroups', array());

    // Set the option
    set_option('auto_import_unconfigured', $auto_import_unconfigured);
    set_option('auto_apply_unconfigured', $auto_apply_unconfigured);
    set_option('auto_import_host_tpl', $auto_import_host_tpl);
    set_option('auto_import_service_tpl', $auto_import_service_tpl);
    set_option('auto_import_notifications', $auto_import_notifications);
    set_option('auto_import_volatile', $auto_import_volatile);
    set_option('auto_import_stalking', $auto_import_stalking);
    set_array_option('auto_import_contacts', $auto_import_contacts);
    set_array_option('auto_import_contactgroups', $auto_import_contactgroups);
    set_array_option('auto_import_hostgroups', $auto_import_hostgroups);
    set_array_option('auto_import_servicegroups', $auto_import_servicegroups);

    flash_message(_("Successfully updated auto configuration settings."));
    header("Location: missingobjects.php#auto-config");
}


/**
 * Purge objects file so that there are no more unconfigured objects
 */
function do_purge()
{
    $objects_file = get_root_dir() . "/var/corelog.newobjects";

    // Remove the objects
    if (file_exists($objects_file)) {
        file_put_contents($objects_file, serialize(array()));
        flash_message(_("Successfully removed unconfigured objects list."));
    } else {
        flash_message(_("No objects file found. You may not have any unconfigured objects to clear."), FLASH_MSG_ERROR);
    }

    header("Location: missingobjects.php");
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_objects($error = false, $msg = "")
{
    $configured = grab_request_var("configured");

    $wizard_url = "https://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards/Unconfigured-Passive-Object-Wizard/details";

    // Is the listener enabled?
    $listen = get_option('enable_unconfigured_objects', true);
    $perflink = "<a href='performance.php' title='Performance Settings'>" . _("Performance Settings") . "</a>";

    // Set up automatic configuring of passive checks
    $auto_import_unconfigured = get_option('auto_import_unconfigured', 0);
    $auto_apply_unconfigured = get_option('auto_apply_unconfigured', 1);
    $auto_import_volatile = get_option('auto_import_volatile', 0);
    $auto_import_stalking = get_option('auto_import_stalking', 0);
    $auto_import_service_tpl = get_option('auto_import_service_tpl', 'xiwizard_passive_service');
    $auto_import_host_tpl = get_option('auto_import_host_tpl', 'xiwizard_passive_host');
    $auto_import_notifications = get_option('auto_import_notifications', 1);
    $auto_import_contacts = get_array_option('auto_import_contacts');
    $auto_import_contactgroups = get_array_option('auto_import_contactgroups');
    $auto_import_hostgroups = get_array_option('auto_import_hostgroups');
    $auto_import_servicegroups = get_array_option('auto_import_servicegroups');

    // Special check for templates to find the ids of default templates
    if (!is_numeric($auto_import_host_tpl)) {
        $tpl = nagiosql_get_host_templates(null, $auto_import_host_tpl);
        if (!empty($tpl)) {
            $auto_import_host_tpl = $tpl[0]['id'];
        } else {
            $tpl = nagiosql_get_host_templates(null, "xiwizard_passive_host");
            $auto_import_host_tpl = $tpl[0]['id'];
        }
    }
    if (!is_numeric($auto_import_service_tpl)) {
        $tpl = nagiosql_get_service_templates(null, $auto_import_service_tpl);
        if (!empty($tpl)) {
            $auto_import_service_tpl = $tpl[0]['id'];
        } else {
            $tpl = nagiosql_get_host_templates(null, "xiwizard_passive_service");
            $auto_import_host_tpl = $tpl[0]['id'];
        }
    }

    if (!$listen) {
        $error = true;
        $msg = _("Unconfigured objects listener is currently disabled. This feature can be enabled from the ") . $perflink . _(" page by selecting the 'Subsystem' tab.");
    }
    if ($error == false && $configured == 1) {
        $msg = _("Objects configured.");
    }

    do_page_start(array("page_title" => _('Unconfigured Objects')), true);
?>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#tabs').tabs().show();
        $('.tabs').tabs();

        // When auto import gets checked or unchecked, disable some options
        $('input[name="auto_import_unconfigured"]').click(function() {
            if ($(this).is(":checked")) {
                $('input[name="auto_apply_unconfigured"]').prop('disabled', false);
            } else {
                $('input[name="auto_apply_unconfigured"]').prop('disabled', true);
            }
        });

    });
    </script>

    <h1><?php echo _('Unconfigured Objects'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p>
        <?php echo _("This page shows host and services that check results have been received for, but which have not yet been configured in Nagios."); ?><br><?php echo _("Passive checks may be received by NSCA or NRDP (as defined in your"); ?>
        <a href="dtinbound.php"><?php echo _("inbound transfer settings"); ?></a>) <?php echo _("or through the direct check submission API."); ?>
    </p>
    <p>
        <?php echo _("You may delete unneeded host and services or add them to your monitoring configuration through this page. Note that a large amount of persistent unused passive checks can result in a performance decrease."); ?>
    </p>

    <div id="tabs" class="hide">

        <ul>
            <li><a href="#unconfigured"><i class="fa fa-cube"></i> <?php echo _('Unconfigured Objects'); ?></a></li>
            <li><a href="#auto-config"><i class="fa fa-cog"></i> <?php echo _('Auto Configure Settings'); ?></a></li>
        </ul>

        <div id="unconfigured">
            <p>
                <a href='missingobjects.php?purge=1' title='Clear Unconfigured Objects'><?php echo _("Clear Unconfigured Objects List"); ?></a>
            </p>

            <?php
            if (!file_exists(get_base_dir() . "/includes/configwizards/passiveobject/passiveobject.inc.php"))
                echo "<p><strong>Note:</strong> " . _("You must install the unconfigured passive object wizard to configure the selected hosts and services. You can get the wizard from") .
                    " <a href='" . $wizard_url . "' target='_blank'>Nagios Exchange</a>.</p>";
            ?>

            <form method="post" action="">
                <input type="hidden" value="unconfigured" id="tab_hash" name="tab_hash">
                <?php echo get_nagios_session_protector(); ?>

                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#checkall").click(function () {
                            var checked_status = this.checked;
                            $("input[type='checkbox']").each(function () {
                                this.checked = checked_status;
                            });
                        });
                    });
                </script>

                <table class="table table-condensed table-striped table-auto-width">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkall"></th>
                        <th><?php echo _("Host"); ?></th>
                        <th><?php echo _("Service"); ?></th>
                        <th><?php echo _("Last Seen"); ?></th>
                        <th><?php echo _("Actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $datas = null;
                        clearstatcache();
                        $f = get_root_dir() . "/var/corelog.newobjects";
                        if (file_exists($f)) {
                            $datas = file_get_contents($f);
                        }
                        
                        if ($datas == "" || $datas == null) {
                            $newobjects = array();
                        } else {
                            $newobjects = @unserialize($datas);
                        }

                        $current_host = 0;
                        $displayed = 0;

                        foreach ($newobjects as $hn => $arr) {
                            //skip hidden (deleted) hosts
                            if (grab_array_var($arr, 'hide_all', false) == true)
                                continue;

                            $svcs = $arr["services"];
                            $hidden = grab_array_var($arr, 'hidden_services', array());
                            if (!is_array($hidden))
                                $hidden = array();

                            //refactor services array to handle hidden (deleted) items from the list
                            $show_svcs = array();
                            foreach ($svcs as $sn => $val) {
                                if (in_array($sn, $hidden))
                                    continue;
                                $show_svcs[$sn] = $val;
                            }

                            //overwrite old array
                            $svcs = $show_svcs;
                            $total_services = count($svcs);

                            // skip if host/service already exists
                            if ($total_services == 0 && host_exists($hn) == true)
                                continue;
                            else if ($total_services > 0) {
                                $missing = 0;
                                foreach ($svcs as $sn => $sarr) {
                                    if (service_exists($hn, $sn) == true || in_array($sn, $hidden)) //hide deleted services
                                        continue;
                                    $missing++;
                                }
                                if ($missing == 0)
                                    continue;
                            }

                            $displayed++;

                            if ($current_host > 0)
                                echo "<tr><td colspan='5'></td></tr>";

                            // xxx recalculate the $total_services using the same logic as in the loop below  - submitted by forum user nagiosadmin42 - 10/22/2012
                            $total_services = 0;
                            foreach ($svcs as $sn => $sarr) {
                                if (service_exists($hn, $sn) == true || in_array($sn, $hidden))
                                    continue;
                                $total_services++;
                            }

                            echo "<tr>";
                            echo "<td rowspan='" . ($total_services + 1) . "'><input type='checkbox' name='host[" . $hn . "]'></td>";
                            echo "<td rowspan='" . ($total_services + 1) . "'>" . $hn . "</td>";
                            echo "<td>-</td>";
                            echo "<td>" . get_datetime_string($arr["last_seen"]) . "</td>";
                            echo "<td>";
                            echo "<a href='?delete=1&amp;host[" . $hn . "]=1&nsp=" . get_nagios_session_protector_id() . "'><img class='tableItemButton' src='" . theme_image("cross.png") . "' border='0' alt='" . _("Delete") . "' title='" . _("Delete") . "'></a>";
                            echo "<a href='?configure=1&amp;host[" . $hn . "]=1&nsp=" . get_nagios_session_protector_id() . "'><img class='tableItemButton' src='" . theme_image("b_next.png") . "' border='0' alt='Configure' title='Configure'></a>";
                            echo "</td>";
                            echo "</tr>";

                            $svcs = $arr["services"];
                            if ($total_services > 0) {
                                foreach ($svcs as $sn => $sarr) {

                                    if (service_exists($hn, $sn) == true || in_array($sn, $hidden))
                                        continue;

                                    echo "<tr>";
                                    echo "<td>" . $sn . "</td>";
                                    echo "<td>" . get_datetime_string($arr["last_seen"]) . "</td>";
                                    echo "<td>";
                                    echo "<a href='?delete=1&amp;service[" . $hn . "]=" . $sn . "&nsp=" . get_nagios_session_protector_id() . "'><img class='tableItemButton' src='" . theme_image("cross.png") . "' border='0' alt='" . _("Delete") . "' title='" . _("Delete") . "'></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }

                            $current_host++;
                        }
                        if ($displayed == 0) {
                            echo "<tr><td colspan='5'>" . _("No unconfigured passive objects found") . ".</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="tableListMultiOptions">
                    <?php echo _("With Selected:"); ?>
                    <button class="tableMultiItemButton" title="<?php echo _('Remove'); ?>" value="1" name="delete" type="submit">
                        <img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Remove'); ?>">
                    </button>
                    <button class="tableMultiItemButton" title="<?php echo _('Configure'); ?>" value="1" name="configure" type="submit">
                        <img src="<?php echo theme_image('cog_go.png'); ?>" class="tt-bind" title="<?php echo _('Configure'); ?>">
                    </button>
                </div>

            </form>

        </div>

        <div id="auto-config">

            <form method="post" action="" class="form-horizontal">
                <input type="hidden" value="unconfigured" id="tab_hash" name="tab_hash">
                <input type="hidden" value="1" name="save">
                <?php echo get_nagios_session_protector(); ?>

                <table class="table table-no-border table-auto-width table-no-margin">
                    <tr>
                        <td>
                            <label for="auto_import_unconfigured">
                                <?php echo _("Enable Auto Import"); ?>:
                            </label>
                        </td>
                        <td class="checkbox">
                            <label>
                                <input type="checkbox" name="auto_import_unconfigured" id="auto_import_unconfigured" value="1" <?php echo is_checked($auto_import_unconfigured, 1); ?>>
                                <?php echo _("Automatically import host/services into the CCM with templates applied below."); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="auto_apply_unconfigured">
                                <?php echo _("Apply Configuration"); ?>:
                            </label>
                        </td>
                        <td class="checkbox">
                            <label>
                                <input type="checkbox" name="auto_apply_unconfigured" id="auto_apply_unconfigured" value="1" <?php echo is_checked($auto_apply_unconfigured, 1); if (!$auto_import_unconfigured) { echo " disabled"; } ?>>
                                <?php echo _("Automatically apply configuration after import of configs."); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <div class="tabs" style="border: none;">

                    <ul>
                        <li><a href="#notifications"><i class="fa fa-bell"></i> <?php echo _('Notifications'); ?></a></li>
                        <li><a href="#groups"><i class="fa fa-folder-open"></i> <?php echo _('Groups'); ?></a></li>
                        <li><a href="#advanced"><i class="fa fa-cog"></i> <?php echo _('Advanced'); ?></a></li>
                    </ul>

                    <div id="notifications">

                        <table class="table table-no-border table-auto-width table-no-margin">
                            <tr>
                                <td>
                                    <label><?php echo _("Notification Options"); ?>:</label>
                                </td>
                                <td class="checkbox">
                                    <label>
                                        <input type="checkbox" name="auto_import_notifications" value="1" <?php echo is_checked($auto_import_notifications, 1); ?>>
                                        <?php echo _("Send notifications for hosts and services"); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="vt">
                                    <label><?php echo _("Contacts"); ?>:</label>
                                </td>
                                <td>
                                    <div class="well freeform">
                                        <div class="sel-users-new">
                                            <?php
                                            $xml = get_xml_contact_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                            $username = get_user_attr(0, 'username');
                                            foreach ($xml->contact as $c) {

                                                $ischecked = "";
                                                if (array_key_exists(intval($c->attributes()->id), $auto_import_contacts)) {
                                                    $ischecked = "checked";
                                                }

                                                echo "<div class='checkbox'><label><input type='checkbox' name='auto_import_contacts[" . $c->attributes()->id . "]' " . $ischecked . ">" . $c->alias . " (" . $c->contact_name . ")</label></div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="vt">
                                    <label><?php echo _("Contact Groups"); ?>:</label>
                                </td>
                                <td>
                                    <div class="well freeform">
                                        <div class="sel-users-new">
                                            <?php
                                            $xml = get_xml_contactgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                            foreach ($xml->contactgroup as $c) {

                                                $ischecked = "";
                                                if (array_key_exists(intval($c->attributes()->id), $auto_import_contactgroups)) {
                                                    $ischecked = "checked";
                                                }

                                                echo "<div class='checkbox'><label><input type='checkbox' name='auto_import_contactgroups[" . $c->attributes()->id . "]' " . $ischecked . ">" . $c->alias . " (" . $c->contactgroup_name . ")</label></div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>

                    <div id="groups">

                        <table class="table table-no-border table-auto-width table-no-margin">
                            <tr>
                                <td class="vt">
                                    <label><?php echo _("Host Groups"); ?>:</label>
                                </td>
                                <td>
                                    <div class="well freeform">
                                        <p><?php echo _("Define which hostgroup(s) the host should belong to (if any)."); ?></p>
                                        <div class="sel-users-new">
                                            <?php
                                            $xml = get_xml_hostgroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                            foreach ($xml->hostgroup as $c) {

                                                $ischecked = "";
                                                if (array_key_exists(strval($c->attributes()->id), $auto_import_hostgroups)) {
                                                    $ischecked = "checked";
                                                }

                                                echo "<div class='checkbox'><label><input type='checkbox' name='auto_import_hostgroups[" . $c->attributes()->id . "]' " . $ischecked . " >" . $c->alias . " (" . $c->hostgroup_name . ")</label></div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="vt">
                                    <label><?php echo _("Service Groups"); ?>:</label>
                                </td>
                                <td>
                                    <div class="well freeform">
                                        <p><?php echo _("Define which servicegroup(s) the service(s) should belong to (if any)."); ?></p>
                                        <div class="sel-users-new">
                                        <?php
                                        $xml = get_xml_servicegroup_objects(array("is_active" => 1, "orderby" => "alias:a"));
                                        foreach ($xml->servicegroup as $c) {

                                            $ischecked = "";
                                            if (array_key_exists(strval($c->attributes()->id), $auto_import_servicegroups)) {
                                                $ischecked = "checked";
                                            }

                                            echo "<div class='checkbox'><label><input type='checkbox' name='auto_import_servicegroups[" . $c->attributes()->id . "]' " . $ischecked . " >" . $c->alias . " (" . $c->servicegroup_name . ")</label></div>";
                                        }
                                        ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>

                    <div id="advanced">

                        <table class="table table-no-border table-auto-width table-no-margin">
                            <tr>
                                <td class="vt">
                                    <label><?php echo _('Stalking'); ?>:</label>
                                </td>
                                <td>
                                    <select class="form-control" name="auto_import_stalking">
                                        <option value="0"><?php echo _('Disabled'); ?></option>
                                        <option value="1" <?php echo is_selected($auto_import_stalking, 1); ?>><?php echo _('Enabled'); ?></option>
                                    </select>
                                    &nbsp;
                                    <i class="fa fa-info-circle fa-14 tt-bind" title="<?php echo _('Stalked services will have their output data (textual alert information) logged by Nagios each time newly received output differs from the most recent previously received output. This can be useful to track important or security-related information.'); ?>"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="vt">
                                    <label><?php echo _("Advanced Config"); ?>:</label>
                                </td>
                                <td>
                                    <table class="table table-padding-bottom-right-only table-no-border table-auto-width">
                                        <tr>
                                            <td><h6 style="margin: 8px 0 0 0;"><?php echo _("Host Config"); ?></h6></td>
                                            <td><h6 style="margin: 8px 0 0 0;"><?php echo _("Service Config"); ?></h6></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><?php echo _("Host Template"); ?></div>
                                                    <select class="form-control" name="auto_import_host_tpl">
                                                        <?php
                                                        $host_tpls = nagiosql_get_host_templates();
                                                        foreach ($host_tpls as $tpl) {
                                                            $selected = '';
                                                            if ($auto_import_host_tpl == $tpl['id']) { $selected = ' selected'; }
                                                            echo '<option value="'.intval($tpl['id']).'"'.$selected.'>'.encode_form_val($tpl['template_name']).'</opyion>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><?php echo _("Service Template"); ?></div>
                                                    <select class="form-control" name="auto_import_service_tpl">
                                                        <?php
                                                        $service_tpls = nagiosql_get_service_templates();
                                                        foreach ($service_tpls as $tpl) {
                                                            $selected = '';
                                                            if ($auto_import_service_tpl == $tpl['id']) { $selected = ' selected'; }
                                                            echo '<option value="'.intval($tpl['id']).'"'.$selected.'>'.encode_form_val($tpl['template_name']).'</opyion>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><?php echo _('Volatility'); ?> <i class="fa fa-info-circle fa-14 tt-bind" title="<?php echo _('Volatile services generate alerts each time a non-OK event is received, which can be useful when monitoring security events.'); ?>"></i></div>
                                                    <select class="form-control" name="auto_import_volatile">
                                                        <option value="0"><?php echo _('Non-volatile'); ?></option>
                                                        <option value="1" <?php echo is_selected($auto_import_volatile, 1); ?>><?php echo _('Volatile'); ?></option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                    </div>

                </div>

                <button type="submit" class="btn btn-sm btn-primary"><?php echo _("Update Settings"); ?></button>

            </form>

        </div>

    </div>

    <?php

    do_page_end(true);
    exit();
}