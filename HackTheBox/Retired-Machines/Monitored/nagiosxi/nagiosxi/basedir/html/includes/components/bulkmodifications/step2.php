<?php
//
// Bulk Modifications Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);

// Only admins and people with CCM access can access this component
if (get_user_meta(0, 'ccm_access') == 0 && !is_admin()) {
    echo _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}

if (empty($_POST)) {
    bmt_display_step2();
} else {
    bmt_process_ccm_bulk_submission();
}

function bmt_process_ccm_bulk_submission()
{
    $cmd = grab_request_var('cmd', '');

    // Stop saving in demo mode
    if (in_demo_mode()) {
        flash_message(_("This feature is disabled in demo mode."));
        header("Location: index.php");
        exit();
    }

    // Stop them if they don't have enterprise
    if (!enterprise_features_enabled()) {
        flash_message(_("This feature requires enterprise features to be enabled."));
        header("Location: index.php");
        exit();
    }

    // Hosts and Services selected
    $hosts = grab_request_var('hosts', array());
    $services = grab_request_var('services', array());
    $contacts = grab_request_var('contacts', array());
    $hostschecked = grab_request_var('hostschecked', array());
    $serviceschecked = grab_request_var('serviceschecked', array());
    $contactschecked = grab_request_var('contactschecked', array());
    $hostopt = grab_request_var('host_tpl_options', 2);
    $serviceopt = grab_request_var('service_tpl_options', 2);
    $contactopt = grab_request_var('contact_tpl_options', 2);

    // Change a single config option
    $config_option = grab_request_var('option_list', '');
    $field_value = grab_request_var('field_value', '');
    $oosn_value = grab_request_var('oosn_value', '');
    $host_opts_value = grab_request_var('host_opts_value', array());
    $service_opts_value = grab_request_var('service_opts_value', array());
    $timeperiod = grab_request_var('timeperiod', '');

    // Add hostgroups to host (cannot use $hostgroups since it is used below)
    $add_hostgroups = grab_request_var('add_hostgroups', array());
    $add_servicegroups = grab_request_var('add_servicegroups', array());

    // Do some host/service selection based on host/service groups
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroups = grab_request_var('servicegroups', array());
    $contactgroups = grab_request_var('contactgroups', array());

    // If hostgroups were selected...
    // NOTE: These MEMBERS are not TRUE to the DB. They are from Nagios Core.
    //       This means that you may have to apply configuration to see member objects appear in host/service groups.
    if (!empty($hostgroups)) {
        $tmp = array();
        foreach ($hostgroups as $hg) {
            $args = array('hostgroup_name' => $hg);
            $xml = get_xml_hostgroup_member_objects($args);
            if ($xml->recordcount > 0) {
                foreach ($xml->hostgroup->members->host as $obj) {
                    $tmp[] = "'".strval($obj->host_name)."'";
                }
            }
        }

        // Grab the ids from the nagiosql tbl_host table

        // NOTE: If the $tmp array is empty it provides an invalid $sql string.
        //       The handler below checks if $tmp is empty and guarantees a valid $sql string. -AC
        if (!empty($tmp)) {
            $sql = "SELECT `id` FROM tbl_host WHERE `host_name` IN (".implode(',', $tmp).");";
        } else {
            $sql = "SELECT `id` FROM tbl_host WHERE `host_name` IN ('');";
        }

        $objs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        foreach ($objs as $h) {
            $hosts[] = intval($h['id']);
        }

        $hosts = array_unique($hosts);
    }

    // If servicegroups were selected...
    // NOTE: These MEMBERS are not TRUE to the DB. They are from Nagios Core.
    //       This means that you may have to apply configuration to see member objects appear in host/service groups.
    if (!empty($servicegroups)) {
        $tmp = array();
        $s = array();
        foreach ($servicegroups as $sg) {
            $args = array('servicegroup_name' => $sg);
            $xml = get_xml_servicegroup_member_objects($args);
            if ($xml->recordcount > 0) {
                foreach ($xml->servicegroup->members->service as $obj) {
                    $s[] = array('host_name' => strval($obj->host_name), 'service_description' => strval($obj->service_description));
                    $tmp[] = "'".strval($obj->service_description)."'";
                }
            }
        }
        $tmp = array_unique($tmp);

        // Grab the ids from the nagiosql tbl_service table
        $sql = "SELECT tbl_service.id, tbl_host.host_name, tbl_service.service_description
                FROM tbl_service LEFT JOIN tbl_lnkServiceToHost ON tbl_service.id = tbl_lnkServiceToHost.idMaster
                                 LEFT JOIN tbl_host ON tbl_lnkServiceToHost.idSlave = tbl_host.id
                                 WHERE tbl_service.`service_description` IN (".implode(',', $tmp).");";
        $objs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        foreach ($objs as $o) {
            foreach ($s as $sv) {
                if ($o['host_name'] == $sv['host_name'] && $o['service_description'] == $sv['service_description']) {
                    $services[] = intval($o['id']);
                }
            }
        }

        $services = array_unique($services);
    }

    // This is currently only used for free variables.
    // Other contact-related buttons handle contact processing in the switch below.
    if (!empty($contactgroups)) {
        $tmp = array();
        $c = array();
        foreach ($contactgroups as $contactgroup) {
            $args = array('contactgroup_name' => $contactgroup);
            $xml = get_xml_contactgroup_member_objects($args);
            if ($xml->recordcount > 0) {
                foreach ($xml->contactgroup->members->contact as $obj) {
                    $c[] = array('contact_name' => strval($obj->contact_name));
                    $tmp[] = "'".strval($obj->service_description)."'";
                }
            }
        }

        // NOTE: If the $tmp array is empty it provides an invalid $sql string.
        //       The handler below checks if $tmp is empty and guarantees a valid $sql string. -AC
        $tmp = array_unique($tmp);
        if (!empty($tmp)) {
            $sql = "SELECT `id` FROM tbl_contact WHERE `contact_name` IN (".implode(',', $tmp).");";
        } else {
            $sql = "SELECT `id` FROM tbl_contact WHERE `contact_name` IN ('');";
        }
        $objs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        foreach ($objs as $contact) {
            $contacts[] = intval($contact['id']);
        }

        $contacts = array_unique($contacts);
    }

    $errors = 0;
    $msg = '';

    switch ($cmd) {

        case 'singleoption':
            $log = "Change Single Config Option";
            $opts_values = array("host" => $host_opts_value, "service" => $service_opts_value);
            $errors = bmt_change_single_config_option($config_option, $field_value, $oosn_value, $opts_values, $timeperiod, $hosts, $services, $msg);
            break;

        case 'command':
            $log = "Change Command and Arguments";
            $args = grab_request_var('args', array());
            $args_c = grab_request_var('args_c', array());
            $command = grab_request_var('command', '');
            $errors = bmt_change_command_and_arguments($command, $args, $args_c, $hosts, $services, $msg);
            break;

        case 'addcontacts':
            $log = "Add Contact(s)";
            $contacts = grab_request_var('contacts', array());
            $errors = bmt_add_contact_relationships($contacts, $hosts, $services, $hostopt, $serviceopt, $msg);
            break;

        case 'removecontacts':
            $log = "Remove Contact";
            $contact = grab_request_var('contact', '');
            $errors = bmt_remove_contact_relationships($contact, $hostschecked, $serviceschecked, $msg);
            break;

        case 'addcgs':
            $log = "Add Contact Group(s)";
            $contactgroups = grab_request_var('contactgroups', array());
            $errors = bmt_add_contactgroup_relationships($contactgroups, $hosts, $services, $hostopt, $serviceopt, $msg);
            break;

        case 'removecgs':
            $log = "Remove Contact Group";
            $contactgroup = grab_request_var('contactgroup', '');
            $errors = bmt_remove_contactgroup_relationships($contactgroup, $hostschecked, $serviceschecked, $msg);
            break;

        case 'addhostgroups':
            $log = "Add Host Group(s)";
            $hostgroups = grab_request_var('hostgroups', array());
            $errors = bmt_add_hostgroups_to_hosts($add_hostgroups, $hosts, $msg);
            break;

        case 'removehostgroups':
            $log = "Remove Host Group";
            $hostgroup = grab_request_var('hostgroup', '');
            $errors = bmt_remove_hostgroup_from_hosts($hostgroup, $hostschecked, $msg);
            break;

        case 'addservicegroups':
            $log = "Add Service Group(s)";
            $servicegroups = grab_request_var('servicegroups', array());
            $errors = bmt_add_servicegroups_to_services($add_servicegroups, $services, $msg);
            break;

        case 'removeservicegroups':
            $log = "Remove Service Group";
            $servicegroup = grab_request_var('servicegroup', '');
            $errors = bmt_remove_servicegroup_from_services($servicegroup, $serviceschecked, $msg);
            break;

        case 'addparenthosts':
            $log = "Add Parent Host(s)";
            $parenthosts = grab_request_var('parenthosts', array());
            $errors = bmt_add_parent_hosts_to_hosts($parenthosts, $hosts, $msg);
            break;

        case 'removeparenthosts':
            $log = "Remove Parent Host";
            $parenthost = grab_request_var('parenthost', '');
            $errors = bmt_remove_parent_host_from_hosts($parenthost, $hostschecked, $msg);
            break;

        case 'addfreevariables':
            $log = "Add Free Variables";
            $free_variable_names = grab_request_var('variables', array());
            $free_variable_values = grab_request_var('variabledefs', array());
            $errors = bmt_add_free_variables($hosts, $services, $contacts, $free_variable_names, $free_variable_values);
            break;

        case 'removefreevariables':
            $log = "Add Free Variables";
            $variable_ids = grab_request_var('variable', array());
            $errors = bmt_remove_free_variables($hostschecked, $serviceschecked, $contactschecked, $variable_ids, $msg);
            break;

        case 'addservices':
            $log = "Add Service";
            $addservices = grab_request_var('addservices', array());
            $errors = bmt_add_services_to_hosts($addservices, $hosts, $msg);
            break;

        case 'confignames':
            $log = "Change Config Names for Services";
            $config_name = grab_request_var('config_name', array());
            $errors = bmt_change_config_names($config_name, $services, $msg);
            break;

        case 'templates':
            $hosttemplates = grab_request_var('hosttemplates', array());
            $servicetemplates = grab_request_var('servicetemplates', array());
            if (!empty($hosttemplates)) {
                $log = "Setting Host Templates";
                $errors = bmt_set_host_templates($hosttemplates, $hosts, $msg);
            } else if (!empty($servicetemplates)) {
                $log = "Setting Service Templates";
                $errors = bmt_set_service_templates($servicetemplates, $services, $msg);
            }
            break;

        default:
            $errors = 1;
            $msg .= _("Invalid bulk command specified!");
            break;

    }

    // If there is no message, default out
    if ($msg == '' && empty($errors)) {
        $_SESSION['success_msg'] = _("Updates saved successfully!");
        $_SESSION['success'] = 1;

        // Update apply config required in CCM
        set_option('ccm_apply_config_needed', 1);

        if(!empty($hosts) || !empty($hostschecked)) {
            nagiosccm_set_table_modified('host');
        }

        if(!empty($services) || !empty($serviceschecked) || !empty($addservices)) {
            nagiosccm_set_table_modified('service');
        }


        header("Location: index.php");
    }

    // Display errors
    bmt_display_step2($msg);
}

// Display the step 2 page along with whatever was selected by the user
// when they were on step 1, such as modifying a single config option.
function bmt_display_step2($errormsg='')
{
    $title = "Nagios XI - "._("Bulk Modifications Tool");
    do_page_start(array("page_title" => $title, "enterprise" => true), true);

?>

<script type="text/javascript" src="../ccm/javascript/form_js.js?<?php echo get_build_id(); ?>"></script>
<script type="text/javascript" src="includes/bulkmods.js?<?php echo get_build_id(); ?>"></script>
<link type="text/css" rel="stylesheet" property="stylesheet" href="includes/bulkmods.css?<?php echo get_build_id(); ?>" />

<h1><?php echo _("Bulk Modifications Tool"); ?></h1>
<div id="screen-overlay"></div>
<div id="main">

    <h2 style="padding-bottom: 15px; border-bottom: 1px solid #EEE;"><?php echo _('Step 2: Make Modifications'); ?></h2>

    <div id="contentWrapper">

        <?php if (!empty($errormsg)) { ?>
        <div class="alert alert-error inline">
            <i class="fa fa-exclamation-triangle"></i> &nbsp; <?php echo $errormsg; ?>
        </div>
        <?php } ?>

        <form action="step2.php" method="post" name="step2">

            <?php
            // Grab the proper html based on what was selected
            $cmd = grab_request_var('cmd', '');
            switch ($cmd) {

                case 'singleoption':
                    bmt_display_change_single_config_option();
                    break;

                case 'addcontacts':
                    bmt_add_contacts();
                    break;

                case 'removecontacts':
                    bmt_remove_contacts();
                    break;

                case 'addcgs':
                    bmt_add_contact_groups();
                    break;

                case 'removecgs':
                    bmt_remove_contact_groups();
                    break;

                case 'addhostgroups':
                    bmt_dsiplay_add_hostgroups_to_hosts();
                    break;

                case 'removehostgroups':
                    bmt_display_remove_hostrgroup_from_hosts();
                    break;

                case 'addservicegroups':
                    bmt_display_add_servicegroups_to_services();
                    break;

                case 'removeservicegroups':
                    bmt_display_remove_servicegroup_from_services();
                    break;

                case 'addparenthosts':
                    bmt_display_add_parents_to_hosts();
                    break;

                case 'removeparenthosts':
                    bmt_display_remove_parent_from_hosts();
                    break;

                case 'addfreevariables':
                    bmt_display_add_free_variables();
                    break;

                case 'removefreevariables':
                    bmt_display_remove_free_variables();
                    break;

                case 'addservices':
                    bmt_display_add_services();
                    break;

                case 'command':
                    bmt_display_command();
                    break;

                case 'confignames':
                    bmt_display_config_names();
                    break;

                case 'changetemplates':
                    bmt_display_change_templates();
                    break;

                default:
                    echo _("Could not load page due to missing cmd option.");
                    die();
            }

            // Create the host/service hidden overlays
            $sel_options = false;
            if ($cmd == "addcontacts" || $cmd == "addcgs") {
                $sel_options = true;
            }
            bmt_display_hidden_overlays($sel_options);

            $ioptions = array('addcontacts', 'addcgs', 'addhostgroups', 'addservicegroups', 'addparenthosts');
            if (in_array($cmd, $ioptions)) {
            ?>

            <div style="margin: -20px 0 20px;">
                <label style="margin-top: 10px;">
                    <?php echo _('Host Inheritance Options'); ?>
                </label>
                <br />
                <div class="btn-group" data-toggle="buttons" style="line-height: 30px;">
                    <label class="btn btn-xs btn-default">
                        <input type="radio" name="host_tpl_options" value="0">+
                    </label>
                    <label class="btn btn-xs btn-default">
                        <input type="radio" name="host_tpl_options" value="1"><?php echo _('Null'); ?>
                    </label>
                    <label class="btn btn-xs btn-default active">
                        <input type="radio" name="host_tpl_options" value="2"><?php echo _('Standard'); ?>
                    </label>
                </div>
                <br />
                <label style="margin-top: 10px;">
                    <?php echo _('Service Inheritance Options'); ?>
                </label>
                <br />
                <div class="btn-group" data-toggle="buttons" style="line-height: 30px;">
                    <label class="btn btn-xs btn-default">
                        <input type="radio" name="service_tpl_options" value="0">+
                    </label>
                    <label class="btn btn-xs btn-default">
                        <input type="radio" name="service_tpl_options" value="1"><?php echo _('Null'); ?>
                    </label>
                    <label class="btn btn-xs btn-default active">
                        <input type="radio" name="service_tpl_options" value="2"><?php echo _('Standard'); ?>
                    </label>
                </div>
            </div>

            <?php } ?>

            <div class="clear"></div>

            <div class="save-box">
                <div class="save-button">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check l"></i> <?php echo _('Save Changes'); ?></button>
                </div>
                <div>
                    <a href="index.php" class="btn btn-sm btn-danger" style="color:#fff"><i class="fa fa-times l"></i> <?php echo _('Cancel'); ?></a>
                </div>
            </div>

        </form>

    </div>
</div>

<?php
    do_page_end(true);
}

// Change templates
function bmt_display_change_templates()
{
?>
<div id="change_templates_selector">
    <h4><?php echo _('Change Templates for Hosts/Services'); ?></h4>
    <p><?php echo _('Select if modifying Hosts or Services.'); ?></p>
    <div style="padding: 0px 0 20px 0;">
        <button type="button" class="btn btn-sm btn-default hs-template-select" data-type="host"><?php echo _('Hosts'); ?></button>
        <button type="button" class="btn btn-sm btn-default hs-template-select" data-type="service"><?php echo _('Services'); ?></button>
    </div>
</div>
<div id="bulk_change_templates" class="bulk_wizard hide">
    <h4><?php echo _('Change Templates for Hosts/Services'); ?></h4>
    <p><?php echo _('Change the templates used for hosts/services. <strong>Creates or updates the "use x,x,x" line in the host/service config definition.</strong> Templates are applied in order.'); ?></p>
    <p><em>** <?php echo _('THIS WILL OVERWRITE CURRENTLY USED TEMPLATES ON SELECTED OBJECTS'); ?> **</em></p>
    <div id="templates-hosts" class="hide">
        <div style="padding: 5px 0 10px 0;">
            <button type="button" class="btn btn-sm btn-primary" onclick="overlay('hosttemplateBox')"><?php echo _('Select Templates'); ?></button>
        </div>
        <div class="button-box">
            <div style="margin-bottom: 10px;">
                <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
            </div>
        </div>
    </div>
    <div id="templates-services" class="hide">
        <div style="padding: 5px 0 10px 0;">
            <button type="button" class="btn btn-sm btn-primary btn-servicetemplateBox" onclick="overlay('servicetemplateBox')"><?php echo _('Select Templates'); ?> <span class="badge">0</span></button>
        </div>
        <div class="button-box">
            <div style="margin-bottom: 10px;">
                <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="cmd" value="templates">
<?php

    // Add template overlays for this only...
    $FIELDS['selServicetemplateOpts'] = array();
    $FIELDS['selHosttemplateOpts'] = array();
    $FIELDS['pre_hosttemplates'] = array();
    $FIELDS['pre_servicetemplates'] = array();

    $hosttemplates = exec_sql_query(DB_NAGIOSQL, "SELECT `id`,`template_name` FROM tbl_hosttemplate ORDER BY `template_name`;", true);
    foreach ($hosttemplates as $ht) {
        $FIELDS['selHosttemplateOpts'][] = $ht;
    }

    $servicetemplates = exec_sql_query(DB_NAGIOSQL, "SELECT `id`,`template_name` FROM tbl_servicetemplate ORDER BY `template_name`;", true);
    foreach ($servicetemplates as $st) {
        $FIELDS['selServicetemplateOpts'][] = $st;
    }

    unset($hosttemplates);
    unset($servicetemplates);

    $sel_options = false;
    echo bmt_build_hidden_overlay($FIELDS, 'hosttemplate', 'template_name', false, $sel_options);
    echo bmt_build_hidden_overlay($FIELDS, 'servicetemplate', 'template_name', false, $sel_options);
}

// Change config names
function bmt_display_config_names()
{
    $config_name = grab_request_var('config_name', '');
?>
<div id="bulk_config_names" class="bulk_wizard">
    <h4><?php echo _('Change Config Names for Services'); ?></h4>
    <p><?php echo _('Change the config name of the service. Normally the config name is the host name the service is on.'); ?></p>
    <div style="padding: 15px 0 10px 0;">
        <label><?php echo _('Config Name'); ?>:</label> <input type="text" name="config_name" class="form-control" style="width: 220px;" value="<?php echo $config_name; ?>">
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
        <input type="hidden" name="cmd" value="confignames">
    </div>
</div>
<?php
}

// Change the command and arguments
function bmt_display_command() {
?>

<script type="text/javascript">
$(document).ready(function() {

    var toggle = true;

    $('.arg-chk-box').click(function() {
        var sel = $(this).parents('.input-group').find('input[type="text"]');
        if ($(this).is(":checked")) {
            sel.prop('readonly', false);
        } else {
            sel.prop('readonly', true);
        }
    });

    $('.toggle').click(function() {
        if (toggle) {
            $('.arg-chk-box').prop('checked', true);
            $('.arg-txt-box').prop('readonly', false);
            toggle = false;
        } else {
            $('.arg-chk-box').prop('checked', false);
            $('.arg-txt-box').prop('readonly', true);
            toggle = true;
        }
    });

});
</script>

<div id="bulk_change_command" class="bulk_wizard">
    <h4><?php echo _('Change Command and Arguments'); ?></h4>
    <p><?php echo _('Change the command and arguments. To change only arguments do not select a new command and leave the check command blank. <br>Only argument fields which are checked will be overwritten/updated.'); ?></p>
    <p><em>** <?php echo _('CHANGING THE COMMAND WILL OVERWRITE THE CURRENT COMMAND'); ?> **</em></p>
    <div class="form-inline">
        <?php $commands = get_commands(); ?>
        <script type="text/javascript">
        var command_list = new Array();
        <?php
        foreach ($commands as $c) {
            echo "command_list['".$c['id']."'] = '".addslashes(htmlentities($c['line'], ENT_NOQUOTES))."';";
        }
        ?>
        </script>
        <div class="input-group">
            <div class="input-group-addon"><?php echo _('Check Command'); ?></div>
            <select name="command" id="commands" class="form-control">
                <option value="" selected></option>
                <option value="blank"><?php echo _('no command (blank)'); ?></option>
                <?php foreach ($commands as $c) { ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div id="command-box" class="hide">
        <pre id="fullcommand"></pre>
    </div>
    <div class="arg-box">
        <div><a class="toggle"><?php echo _('Toggle All'); ?></a></div>
        <?php for ($i = 1; $i < 9; $i++) { ?>
        <div class="input-group">
            <div class="input-group-addon">
                <input type="checkbox" class="arg-chk-box" name="args_c[<?php echo $i; ?>]" checked>
            </div>
            <div class="input-group-addon no-rb">$ARG<?php echo $i; ?>$</div>
            <input type="text" name="args[<?php echo $i; ?>]" class="arg-txt-box form-control">
        </div>
        <?php } ?>
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
        <input type="hidden" name="cmd" value="command">
    </div>
</div>
<?php
}

// Change a single config option
function bmt_display_change_single_config_option() {
?>
<div id="bulk_change_option" class="bulk_wizard">
    <h4><?php echo _('Change a single configuration option'); ?></h4>
    <p><?php echo _('Select a configuration option to change. These changes will overwrite any configuration value already present.'); ?></p>
    <div id="config_options" class="option_box">
        <select name="option_list" id="option_list" class="form-control">
            <option value="max_check_attempts" data-type="field" data-uom=""><?php echo _('Max check attempts'); ?></option>
            <option value="check_interval" data-type="field" data-uom="min"><?php echo _('Check interval'); ?></option>
            <option value="check_period" data-type="dd"><?php echo _('Check period'); ?></option>
            <option value="retry_interval" data-type="field" data-uom="min"><?php echo _('Retry interval'); ?></option>
            <option value="initial_state" data-type="dou" data-r="1"><?php echo _('Initial state'); ?></option>
            <option value="freshness_threshold" data-type="field" data-uom="sec"><?php echo _('Freshness threshold'); ?></option>
            <option value="low_flap_threshold" data-type="field" data-uom="%"><?php echo _('Low flap threshold'); ?></option>
            <option value="high_flap_threshold" data-type="field" data-uom="%"><?php echo _('High flap threshold'); ?></option>
            <option value="notification_interval" data-type="field" data-uom="min"><?php echo _('Notification interval'); ?></option>
            <option value="notification_period" data-type="dd"><?php echo _('Notification period'); ?></option>
            <option value="notifications_enabled" data-type="oosn"><?php echo _('Notifications enabled'); ?></option>
            <option value="notification_options" data-type="nopts"><?php echo _('Notification options'); ?></option>
            <option value="first_notification_delay" data-type="field" data-uom="min"><?php echo _('First notification delay'); ?></option>
            <option value="active_checks_enabled" data-type="oosn"><?php echo _('Active checks enabled'); ?></option>
            <option value="passive_checks_enabled" data-type="oosn"><?php echo _('Passive checks enabled'); ?></option>
            <option value="check_freshness" data-type="oosn"><?php echo _('Check freshness'); ?></option>
            <option value="event_handler_enabled" data-type="oosn"><?php echo _('Event handler enabled'); ?></option>
            <option value="flap_detection_enabled" data-type="oosn"><?php echo _('Flap detection enabled'); ?></option>
            <option value="flap_detection_options" data-type="dou"><?php echo _('Flap detection options'); ?></option>
            <option value="retain_status_information" data-type="oosn"><?php echo _('Retain status information'); ?></option>
            <option value="retain_nonstatus_information" data-type="oosn"><?php echo _('Retain non-status information'); ?></option>
            <option value="process_perf_data" data-type="oosn"><?php echo _('Process perf data'); ?></option>
            <option value="stalking_options" data-type="douNn"><?php echo _('Stalking options'); ?></option>
            <option value="is_volatile" data-type="oosn"><?php echo _('Is volatile'); ?></option>
        </select>
        <div id="inner_config_option" class="pad">
            <label><?php echo _("Value"); ?>: <input type="text" size="2" value="" name="field_value" class="form-control"></label>
        </div>
        <div id="timeperiod_config_option" class="hide pad">
            <script>
              $(function() {
                var availableTags = [<?php echo get_timeperiod_list(); ?>];
                $("#timeperiod").autocomplete({
                  source: availableTags
                });
              });
              </script>
            <label><?php echo _('Time period'); ?>:</label> <input type="text" name="timeperiod" class="form-control" id="timeperiod">
        </div>
        <div>
            <div style="margin-bottom: 10px;">
                <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
                <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
                <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
            </div>
            <input type="hidden" name="cmd" value="singleoption">
        </div>
    </div>
</div>
<?php
}

// Add contacts form
function bmt_add_contacts() {
?>
<div id="contact_edit" class="bulk_wizard">
    <h4><?php echo _("Add Contacts to Hosts/Services"); ?></h4>
    <div>
        <label for="contacts"><?php echo _("Contacts"); ?></label>
    </div>
    <div style="margin: 5px 0;">
        <select name="contacts[]" id="contacts" class="multiselect form-control" multiple>
            <?php echo get_contact_list(); ?>
        </select>
    </div>
    <div style="color: #AAA;"><i class="fa fa-exclamation-circle"></i> <?php echo _("Contact is not active and can not be applied"); ?></div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addcontacts">
</div>
<?php
}

// Remove contacts form
function bmt_remove_contacts() {
?>
<div id="contact_edit" class="bulk_wizard">
    <h4><?php echo _("Remove Contact from Hosts/Services"); ?></h4>
    <div style="margin: 20px 0;">
        <div><label for="contact"><?php echo _("Contact"); ?></label></div>
        <select name="contact" id="contact" class="form-control">
            <?php echo get_contact_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getContactRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removecontacts">
</div>
<?php
}

// Add contact groups to hosts/services
function bmt_add_contact_groups() {
?>
<div id="contactgroup_edit" class="bulk_wizard">
    <h4><?php echo _("Add Contact Groups to Hosts/Services"); ?></h4>
    <div>
        <label for="contactgroups"><?php echo _("Contact Groups"); ?></label>
    </div>
    <div style="margin: 5px 0;">
        <select name="contactgroups[]" id="contactgroups" class="multiselect form-control" multiple>
            <?php echo get_contactgroup_list(); ?>
        </select>
    </div>
    <div style="color: #AAA;"><i class="fa fa-exclamation-circle"></i> <?php echo _("Contact group is not active and can not be applied"); ?></div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addcgs">
</div>
<?php
}

// Remove contact groups from hosts/services
function bmt_remove_contact_groups() {
?>
<div id="contactgroup_edit" class="bulk_wizard">
    <h4><?php echo _("Remove Contact Group from Hosts/Services"); ?></h4>
    <div style="margin: 20px 0;">
        <div><label for="contactgroup"><?php echo _("Contact Group"); ?></label></div>
        <select name="contactgroup" id="contactgroup" class="form-control">
            <?php echo get_contactgroup_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getContactGroupRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removecgs">
</div>

<?php
}

// Add hosts to host groups
function bmt_dsiplay_add_hostgroups_to_hosts() {
?>
<div id="bulk_add_hostgroup" class="bulk_wizard">
    <h4><?php echo _("Add a Host Group(s) to Hosts"); ?></h4>
    <div>
        <select name="add_hostgroups[]" id="hostgroups" class="multiselect form-control" multiple>
            <?php echo get_hostgroup_list(); ?>
        </select>
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addhostgroups">
</div>
<?php
}

// Remove hosts from host groups
function bmt_display_remove_hostrgroup_from_hosts() {
?>
<div id="bulk_add_hostgroup" class="bulk_wizard">
    <h4><?php echo _("Remove a Host Group from Hosts"); ?></h4>
    <div style="margin: 20px 0;">
        <select name="hostgroup" id="hostgroup" class="form-control">
            <?php echo get_hostgroup_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getHostgroupRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removehostgroups">
</div>
<?php
}

// Add services to servicegroups
function bmt_display_add_servicegroups_to_services() {
?>
<div id="bulk_add_servicegroup" class="bulk_wizard">
    <h4><?php echo _("Add a Service Group(s) to Services"); ?></h4>
    <div>
        <select name="add_servicegroups[]" id="servicegroups" class="multiselect form-control" multiple>
            <?php echo get_servicegroup_list(); ?>
        </select>
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addservicegroups">
</div>
<?php
}

// Remove services from servicegroups
function bmt_display_remove_servicegroup_from_services() {
?>
<div id="bulk_add_servicegroup" class="bulk_wizard">
    <h4><?php echo _("Remove a Service Group from Services"); ?></h4>
    <div style="margin: 20px 0;">
        <select name="servicegroup" id="servicegroup" class="form-control">
            <?php echo get_servicegroup_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getServicegroupRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removeservicegroups">
</div>
<?php
}

// Adds parent hosts to hosts
function bmt_display_add_parents_to_hosts() {
?>
<div id="bulk_add_parent_host" class="bulk_wizard">
    <h4><?php echo _("Add Parent Host(s) to Hosts"); ?></h4>
    <div>
        <select name="parenthosts[]" id="parenthosts" class="multiselect form-control" multiple>
            <?php echo get_host_list(); ?>
        </select>
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Child Hosts'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Child Hosts using Hostgroups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addparenthosts">
</div>
<?php
}

// Remove parent host from hosts
function bmt_display_remove_parent_from_hosts() {
?>
<div id="bulk_add_hostgroup" class="bulk_wizard">
    <h4><?php echo _("Remove a Parent Host from Hosts"); ?></h4>
    <div>
        <select name="parenthost" id="parenthost" class="form-control">
            <?php echo get_host_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getParentHostRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removeparenthosts">
</div>
<?php
}

function bmt_display_add_free_variables() {
?>
<script>
    $(document).ready(function() {
        $('#free-variables-insert').on('click', function() {
            var name = $('#txtVariablename').val();
            if (name[0] !== '_') {
                $('.alert.alert-error.hide:not(.dismissed)').removeClass('hide');
            }
            insertDefinition(false, false);
        });

        $('.alert.alert-error').click(function() {
            $(this).addClass('hide');
            $(this).addClass('dismissed');
        });
    });
</script>
<div id="bulk_add_free_variables" class="bulk_wizard">
    <h4><?php echo _("Add Free Variables"); ?></h4>
    <div>
        <div style="width: 30%; display: inline-block; vertical-align: top;">
            <input type="text" style="vertical-align: middle;" id="txtVariablename" class="form-control" name="name" placeholder="<?php echo _("Name");?>">
            <input type="text" style="vertical-align: middle;" id="txtVariablevalue" class="form-control" name="value" placeholder="<?php echo _("Value");?>">
            <button type="button" id="free-variables-insert" class="btn btn-sm btn-primary"><?php echo _("Insert");?>&nbsp;<i class="fa fa-chevron-right"></i></button>
        </div><!--
     --><div class="" style="width: 70%; display: inline-block;">
            <table class='table table-condensed table-hover table-assigned' id='tblVariables'>
                <thead>
                    <tr>
                        <th style='width: 45%;'><?php echo _("Name");?></th>
                        <th style='width: 45%;'><?php echo _("Value");?></th>
                        <th style='width: 10%;'></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- insert selected items here -->
                </tbody>
            </table>
        </div>
    </div>
    <br/>
    <div>
        <div>
            <p>
                <?php echo _("Note: If an object already has a free variable with a name in this list, it will be deleted and restored with the new value."); ?>
                <?php echo _("Free variable names are case-insensitive."); ?>
            </p>
        </div>
        <div class="alert alert-error hide">
            <?php echo _("All free variable names should start with '_' (Click to dismiss)"); ;?>
        </div>
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Host Groups'); ?> <span class="badge">0</span></button>
        </div>
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-serviceBox" onclick="overlay('serviceBox')"><?php echo _('Select Services'); ?> <span class="badge">0</span></button>
            <button type="button" class="btn btn-sm btn-default btn-servicegroupBox" onclick="overlay('servicegroupBox')"><?php echo _('Select Services using Service Groups'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-contactBox" onclick="overlay('contactBox')"><?php echo _('Select Contacts'); ?> <span class="badge">0</span></button>

            <button type="button" class="btn btn-sm btn-default btn-contactgroupBox" onclick="overlay('contactgroupBox')"><?php echo _('Select Contacts using Contact Groups'); ?> <span class="badge">0</span></button>
        </div>
        <input type="hidden" name="cmd" value="addfreevariables">
    </div>
</div>

<?php
}

function bmt_display_remove_free_variables() {
?>
<div id="bulk_remove_free_variables" class="bulk_wizard">
    <h4><?php echo _("Remove Free Variables"); ?></h4>
    <div style="margin: 20px 0;">
        <div><label for="variable"><?php echo _("Variable Name"); ?></label></div>
        <select name="variable" id="variable" class="form-control">
            <?php echo get_free_variables_name_list(); ?>
        </select>
        <button type="button" class="btn btn-sm btn-default" onclick="getVariableRelationships();"><?php echo _("Find Relationships"); ?></button>
    </div>
    <div>
        <div id="relationships"></div>
    </div>
    <input type="hidden" name="cmd" value="removefreevariables">
</div>
<?php
}

// Add services to hosts
function bmt_display_add_services() {
?>
<div id="bulk_add_service" class="bulk_wizard">
    <h4><?php echo _("Add Service(s) to Hosts"); ?></h4>
    <p style="padding: 5px 0;"><b><?php echo _('Note: Templates, Service Groups, Host Groups, Contacts, Contact Groups, and Free Variables will be copied'); ?>.</b></p>
    <p><?php echo _("Template to use for new service"); ?>:</p>
    <div>
        <select name="addservices[]" id="addservices" class="multiselect form-control" style="width: 500px; height: 200px;" multiple>
            <?php echo get_bulk_servicelist(); ?>
        </select>
    </div>
    <div class="button-box">
        <div style="margin-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-default btn-hostBox" onclick="overlay('hostBox')"><?php echo _('Select Hosts'); ?> <span class="badge">0</span></button>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-default btn-hostgroupBox" onclick="overlay('hostgroupBox')"><?php echo _('Select Hosts using Hostgroups'); ?> <span class="badge">0</span></button>
        </div>
    </div>
    <input type="hidden" name="cmd" value="addservices">
</div>
<?php
}

// Fetches an html select list of available services
function get_bulk_servicelist()
{
    $sql = "SELECT `id`, `config_name`, `service_description` FROM `tbl_service`";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR host_name = 2)";
    }

    $sql .= " ORDER BY `config_name`,`service_description` ASC";

    $res = exec_sql_query(DB_NAGIOSQL, $sql, true);

    $html = '';
    foreach ($res as $service) {
        $html .= '<option value="' . $service['id'] . '">' . $service['config_name'] . " - " . $service['service_description'] . '</option>';
    }

    return $html;
}

// Fetches an html select list of available hostgroups
function get_hostgroup_list()
{
    $res = exec_sql_query(DB_NAGIOSQL, "SELECT `id`, `hostgroup_name`, `alias` FROM `tbl_hostgroup` ORDER BY `hostgroup_name` ASC");

    $html = '';
    foreach ($res as $hostgroup) {
        $name = $hostgroup['hostgroup_name'];
        if (!empty($hostgroup['alias'])) {
            $name = $hostgroup['alias'] . " (" . $hostgroup['hostgroup_name'] . ")";
        }
        $html .= '<option value="' . $hostgroup['id'] . '">' . $name . '</option>';
    }

    return $html;
}

// Fetches an html select list of available hosts
function get_host_list()
{
    $sql = "SELECT `id`, `host_name` FROM `tbl_host`";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR host_name = 2)";
    }

    $sql .= " ORDER BY `host_name` ASC";

    $res = exec_sql_query(DB_NAGIOSQL, $sql);

    $html = '';
    foreach ($res as $host) {
        $html .= '<option value="' . $host['id'] . '">' . $host['host_name'] . '</option>';
    }

    return $html;
}

// Fetches an html select list of available servicegroups
function get_servicegroup_list()
{
    $res = exec_sql_query(DB_NAGIOSQL, "SELECT `id`, `servicegroup_name`, `alias` FROM `tbl_servicegroup` ORDER BY `servicegroup_name` ASC");

    $html = '';
    foreach ($res as $servicegroup) {
        $name = $servicegroup['servicegroup_name'];
        if (!empty($servicegroup['alias'])) {
            $name = $servicegroup['alias'] . " (" . $servicegroup['servicegroup_name'] . ")";
        }
        $html .= '<option value="' . $servicegroup['id'] . '">' . $name . '</option>';
    }

    return $html;
}

// Fetches an html select list of available services
function get_service_list()
{
    $sql = "SELECT `id`, `service_name` FROM `tbl_service`";

    // Limited access to CCM objects
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $sql .= " WHERE (id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
        $sql .= " OR service_name = 2)";
    }

    $sql .= " ORDER BY `service_name` ASC";

    $res = exec_sql_query(DB_NAGIOSQL, $sql);

    $html = '';
    foreach ($res as $service) {
        $html .= '<option value="' . $service['id'] . '">' . $service['service_name'] . '</option>';
    }

    return $html;
}

// Fetches an html select list of available nagios contacts
function get_contact_list()
{
    $rs = exec_sql_query(DB_NAGIOSQL, 'SELECT `id`,`contact_name`,`active` FROM tbl_contact ORDER BY `contact_name` ASC', true);

    $html = '';
    foreach ($rs as $c) {
        $disabled = '';
        if (!$c['active']) { $disabled = ' class="disabled" disabled'; }
        $html .= "<option value='" . $c['id'] . "'".$disabled.">" . $c['contact_name'] . "</option>";
    }

    return $html;
}

// Fetches an html select list of available nagios contactgroups
function get_contactgroup_list()
{
    $rs = exec_sql_query(DB_NAGIOSQL, 'SELECT `id`,`contactgroup_name`,`active` FROM tbl_contactgroup ORDER BY `contactgroup_name` ASC', true);

    $html = '';
    foreach ($rs as $c) {
        $disabled = '';
        if (!$c['active']) { $disabled = ' class="disabled" disabled'; }
        $html .= "<option value='" . $c['id'] . "'".$disabled.">" . $c['contactgroup_name'] . "</option>";
    }

    return $html;
}

function get_free_variables_name_list()
{
    $rs = exec_sql_query(DB_NAGIOSQL, 'SELECT GROUP_CONCAT(`id`) as `id`, `name` FROM tbl_variabledefinition GROUP BY `name`', true);

    $html = '';
    foreach($rs as $variable) {
        $html .= "<option value='" . $variable['id'] . "'>" . $variable['name'] . "</option>";
    }

    return $html;
}

// Fetch the html select list of the check periods
function get_timeperiod_list()
{
    $rs = exec_sql_query(DB_NAGIOSQL, 'SELECT `timeperiod_name` FROM tbl_timeperiod ORDER BY `timeperiod_name` ASC', true);

    $x = array();
    foreach ($rs as $r) {
        $x[] = '"'.$r['timeperiod_name'].'"';
    }

    $html = implode(',', $x);
    return $html;
}

// Function to grab all commands from the database
function get_commands()
{
    $rs = exec_sql_query(DB_NAGIOSQL, 'SELECT `id`,`command_name`,`command_line` FROM tbl_command ORDER BY `command_name` ASC', true);
    $commands = array();

    foreach ($rs as $command) {
        $commands[] = array('id' => $command['id'], 'name' => $command['command_name'], 'line' => $command['command_line']);
    }

    return $commands;
}
