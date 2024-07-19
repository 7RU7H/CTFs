<?php
//
// Buisness Process Intelligence (BPI) Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$bpi_component_name = "nagiosbpi";
bpi_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function bpi_component_init()
{
    global $bpi_component_name;
    $versionok = bpi_component_checkversion();

    $desc = '';
    if (!$versionok) {
        $desc = "<b>" . _("Error: This component requires Nagios XI 5.2.0 or later.") . "</b>";
    }

    $args = array(
        COMPONENT_NAME => $bpi_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Advanced grouping and dependency tool for viewing business processes. Can be used for specialized checks. ") . $desc,
        COMPONENT_TITLE => "Nagios BPI",
        COMPONENT_VERSION => "3.0.4",
        COMPONENT_DATE => '03/15/2022',
        COMPONENT_CONFIGFUNCTION => "bpi_component_config_func",
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($bpi_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'bpi_component_addmenu');
        register_callback(CALLBACK_SUBSYS_GENERIC, 'bpi_component_sync_everything');
        register_callback(CALLBACK_CCM_APPLY_MODIFY_HOSTSERVICE, 'bpi_component_modify_hostservice');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function bpi_component_checkversion()
{
    if (!function_exists('get_product_release'))
        return false;
    if (get_product_release() < 511)
        return false;
    return true;
}


function bpi_component_addmenu($arg = null)
{
    global $bpi_component_name;
    $urlbase = get_component_url_base($bpi_component_name);

    $mi = find_menu_item(MENU_HOME, "menu-home-servicegroupgrid", "id");
    if ($mi == null)
        return;

    $order = grab_array_var($mi, "order", "");
    if ($order == "")
        return;

    $neworder = $order + 0.1;
    add_menu_item(MENU_HOME, array(
        "type" => "linkspacer",
        "title" => "",
        "id" => "menu-home-bpi_spacer",
        "order" => $neworder,
        "opts" => array()
    ));

    $neworder = $neworder + 0.1;
    add_menu_item(MENU_HOME, array(
        "type" => "link",
        "title" => "BPI",
        "id" => "menu-home-bpi",
        "order" => $neworder,
        "opts" => array(
            "href" => $urlbase . "/index.php",
            "icon" => "fa-briefcase"
        )
    ));
}


function bpi_component_config_func($mode = "", $inargs, &$outargs, &$result)
{
    // Initialize return code and output
    $result = 0;
    $output = "";

    switch ($mode) {
        case COMPONENT_CONFIGMODE_GETSETTINGSHTML:

            // Initial values
            $configfile = get_option('bpi_configfile', get_root_dir() . '/etc/components/bpi.conf');
            $config_backup_count = get_option('bpi_config_backup_count', 15);
            $logfile = get_option('bpi_logfile', get_root_dir() . '/var/components/bpi.log');
            $ignore_handled = (get_option('bpi_ignore_handled') == 'on') ? 'checked="checked"' : '';
            $xmlfile = get_option('bpi_xmlfile', get_root_dir() . '/var/components/bpi.xml');
            $xmlthreshold = get_option('bpi_xmlthreshold', 90);
            $multiplier = get_option('bpi_multiplier', 30);
            $showallgroups = (get_option('bpi_showallgroups') == 'on') ? 'checked="checked" ' : '';
            $output_ok = get_option("bpi_output_ok", 'Group health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            $output_warn = get_option("bpi_output_warn", 'Group health below warning threshold of $WARNTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            $output_crit = get_option("bpi_output_crit", 'Group health below critical threshold of $CRITTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            
            $logic_essential = get_option("bpi_logic_essential", array(1, 2, 3));
            if (!is_array($logic_essential)) {
                $logic_essential = json_decode(base64_decode($logic_essential));
            }

            $bpi_sync_on_apply_config = get_option("bpi_sync_on_apply_config", 1);
            $bpi_sync_rm_on_apply_config = get_option("bpi_sync_rm_on_apply_config", 1);

            $output = '

<script type="text/javascript">
function reset_bpi_status_defaults() {
    $("#bpi_output_ok").val("Group health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).");
    $("#bpi_output_warn").val("Group health below warning threshold of $WARNTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).");
    $("#bpi_output_crit").val("Group health below critical threshold of $CRITTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).");
}
</script>

<h5 class="ul">' . _('Nagios BPI Settings') . '</h5>

<table class="table table-condensed table-no-border table-auto-width">
    <tr>
        <td class="vt">
            <label>' . _('BPI Configuration File') . ':</label>
        </td>
        <td>
            <input type="text" size="45" name="bpi_configfile" id="bpi_configfile" value="' . encode_form_val($configfile) . '" class="form-control">
            <div class="subtext">' . _('The directory location of your bpi.conf file.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('BPI Config Backups') . ':</label></td>
        <td>
            <div class="input-group">
                <input type="text" size="4" name="bpi_config_backup_count" id="bpi_config_backup_count" value="' . encode_form_val($config_backup_count) . '" class="form-control">
                <labal class="input-group-addon">' . _('backups') . '</label>
            </div>
            <div class="subtext">' . _('This is the amount of non-archived backups the system will keep. This only applies to the left-hand column in the Config Management tab.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('BPI Log File') . ':</label>
        </td>
        <td>
            <input type="text" size="45" name="bpi_logfile" id="bpi_logfile" value="' . encode_form_val($logfile) . '" class="form-control">
            <div class="subtext">' . _('The directory location of your bpi.log file.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('BPI XML Cache') . ':</label>
        </td>
        <td>
            <input type="text" size="45" name="bpi_xmlfile" id="bpi_xmlfile" value="' . encode_form_val($xmlfile) . '" class="form-control">
            <div class="subtext">' . _('The directory location of your bpi.xml file. This file is used to cache check results for BPI service checks and to decrease CPU usage from BPI checks.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('XML Cache Threshold') . ':</label></td>
        <td>
            <div class="input-group">
                <input type="text" size="4" name="bpi_xmlthreshold" id="bpi_xmlthreshold" value="' . encode_form_val($xmlthreshold) . '" class="form-control">
                <labal class="input-group-addon">' . _('seconds') . '</label>
            </div>
            <div class="subtext">' . _('This is the age limit for cached BPI check result data.  If a BPI service check detects this file as being
            too old, it will recalculate the status of all BPI groups and cache to the XML file.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('AJAX Multiplier') . ':</label>
        </td>
        <td>
            <input type="text" size="4" name="bpi_multiplier" id="bpi_multiplier" value="' . encode_form_val($multiplier) . '" class="form-control">
            <div class="subtext">' . _('The AJAX multiplier is the amount of time before the data on the BPI page reloads. For large installations use a larger number to reduce CPU usage.') . '</div>
        </td>
    </tr>
    <tr>
        <td>
            <label for="problemhandler">' . _('Logic Handling For Problem States') . ':</label>
        </td>
        <td>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ' . $ignore_handled . ' name="bpi_ignore_handled" id="bpi_ignore_handled">
                    ' . _('Ignore host and service problems that are acknowledged or in scheduled downtime.') . ' ' . _("Handled problems will not be factored into the group's problem percentage.") . '
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <label for="">' . _('Logic for Essential Members') . ':</label>
        </td>
        <td>
            <div style="margin-bottom: 2px;">' . _('Set group state to CRITICAL if any essential member is in any of these states') . ':</div>
            <div class="checkbox">
                <label><input type="checkbox" name="logic_essential[]" value="2" ' . (in_array(2, $logic_essential) ? 'checked' : '') . '> ' . _('Critical or Down') . '</label>
            </div>
            <div class="checkbox">
                <label><input type="checkbox" name="logic_essential[]" value="1" ' . (in_array(1, $logic_essential) ? 'checked' : '') . '> ' . _('Warning') . '</label>
            </div>
            <div class="checkbox">
                <label><input type="checkbox" name="logic_essential[]" value="3" ' . (in_array(3, $logic_essential) ? 'checked' : '') . '> ' . _('Unknown') . '</label>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <label for="showallgroups">' . _('Show All Groups To All Users') . ':</label>
        </td>
        <td>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ' . $showallgroups . ' name="bpi_showallgroups" id="bpi_showallgroups">
                    ' . _('This will bypass the normal permissions schemes for BPI groups and show all groups to all users. Host and service permissions for Nagios objects will still be honored, so contacts will still only see hosts or services that they are authorized for.') . '
                </label>
            </div>
        </td>
    </tr>
        <tr>
        <td>
            <label for="bpi_sync_on_apply_config">' . _('Sync On Apply Config') . ':</label>
        </td>
        <td>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ' . is_checked($bpi_sync_on_apply_config, 1) . ' value="1" name="bpi_sync_on_apply_config" id="bpi_sync_on_apply_config">
                    ' . _('Sync all hostgroups and servicegroups on apply config.') . '
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ' . is_checked($bpi_sync_rm_on_apply_config, 1) . ' value="1" name="bpi_sync_rm_on_apply_config" id="bpi_sync_rm_on_apply_config">
                    ' . _('Remove missing hosts and services from all groups.') . '
                </label>
            </div>
        </td>
    </tr>
</table>
<h5 class="ul">' . _('Nagios BPI Status Text') . '</h5>
<p>' . _('You can use the following substitutions in your custom Status Texts') . ':
    <ul>
        <li><strong>$HEALTH$</strong> - ' . _("The overall health of the BPI group. (Percentage)") . '</li>
        <li><strong>$PROBLEMPERCENTAGE$</strong> - ' . _('The percentage of the group health that is a problem. (e.g. 100% - $HEALTH$). (Percentage)') . '</li>
        <li><strong>$MEMBERCOUNT$</strong> - ' . _("The total amount of members in the BPI group. (Integer)") . '</li>
        <li><strong>$PROBLEMCOUNT$</strong> - ' . _("The total amount of problem members in the BPI group. (Integer)") . '</li>
        <li><strong>$CRITTHRESHHOLD$</strong> - ' . _("The current critical threshhold. (Percentage)") . '</li>
        <li><strong>$WARNTHRESHHOLD$</strong> - ' . _("The current warning threshhold. (Percantage)") . '</li>
    </ul>
</p>
<table class="table table-condensed table-no-border table-auto-width">
    <tr>
        <td class="vt">
            <label>' . _('OK Status Text') . ':</label>
        </td>
        <td>
            <input type="text" size="120" name="bpi_output_ok" id="bpi_output_ok" value="' . $output_ok . '" class="form-control">
            <div class="subtext">' . _('The status text to display for OK states.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('WARNING Status Text') . ':</label>
        </td>
        <td>
            <input type="text" size="120" name="bpi_output_warn" id="bpi_output_warn" value="' . $output_warn . '" class="form-control">
            <div class="subtext">' . _('The status text to display for WARNING states.') . '</div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label>' . _('CRITICAL Status Text') . ':</label>
        </td>
        <td>
            <input type="text" size="120" name="bpi_output_crit" id="bpi_output_crit" value="' . $output_crit . '" class="form-control">
            <div class="subtext">' . _('The status text to display for CRITICAL states.') . '</div>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <button type="button" class="btn btn-xs btn-info" id="resetbpistatusdefaults" name="resetbpistatusdefaults" onclick="reset_bpi_status_defaults();">'. _("Reset BPI Status Texts to Defaults") . '</button>
        </td>
    </tr>
</table>';

            break;

        // Save config settings for component
        case COMPONENT_CONFIGMODE_SAVESETTINGS:

            $configfile = grab_array_var($inargs, "bpi_configfile", get_root_dir() . "/etc/components/bpi.conf");
            $config_backup_count = intval(grab_array_var($inargs, "bpi_config_backup_count", 15));
            $logfile = grab_array_var($inargs, "bpi_logfile", "/usr/local/nagios/var/bpi.log");
            $xmlfile = grab_array_var($inargs, "bpi_xmlfile", "/usr/local/nagios/var/bpi.xml");
            $xmlthreshold = grab_array_var($inargs, "bpi_xmlthreshold", 90);
            $multiplier = grab_array_var($inargs, "bpi_multiplier", 30);
            $ignore_handled = grab_array_var($inargs, "bpi_ignore_handled", false);
            $showallgroups = grab_array_var($inargs, "bpi_showallgroups", false);
            $output_ok = grab_array_var($inargs, "bpi_output_ok", 'Group health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            $output_warn = grab_array_var($inargs, "bpi_output_warn", 'Group health below warning threshold of $WARNTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            $output_crit = grab_array_var($inargs, "bpi_output_crit", 'Group health below critical threshold of $CRITTHRESHHOLD$%! Health is $HEALTH$% with $PROBLEMCOUNT$ problem(s).');
            $bpi_sync_on_apply_config = grab_array_var($inargs, "bpi_sync_on_apply_config", 0);
            $bpi_sync_rm_on_apply_config = grab_array_var($inargs, "bpi_sync_rm_on_apply_config", 0);
            $logic_essential = grab_array_var($inargs, "logic_essential", array());

            // Validate variables
            $errors = 0;
            $errmsg = array();

            // Verify that we can write in the directory set for the backupdir and conf file
            if (!is_writable($configfile)) {
                $errors++;
                $errmsg[] = _("BPI config file must be writeable (normally this means set permissions 664 and nagios:nagios)");
            }

            // Verify backup count is an int and > 0
            if ($config_backup_count < 0) {
                $config_backup_count = 15;
            }

            // Handle errors
            if ($errors > 0) {
                $outargs[COMPONENT_ERROR_MESSAGES] = $errmsg;
                $result = 1;
                return '';
            }

            set_option("bpi_configfile", $configfile);
            set_option("bpi_config_backup_count", $config_backup_count);
            set_option("bpi_logfile", $logfile);
            set_option("bpi_ignore_handled", $ignore_handled);
            set_option("bpi_xmlfile", $xmlfile);
            set_option("bpi_xmlthreshold", $xmlthreshold);
            set_option("bpi_multiplier", $multiplier);
            set_option("bpi_showallgroups", $showallgroups);
            set_option("bpi_output_ok", $output_ok);
            set_option("bpi_output_warn", $output_warn);
            set_option("bpi_output_crit", $output_crit);
            set_option("bpi_sync_on_apply_config", $bpi_sync_on_apply_config);
            set_option("bpi_sync_rm_on_apply_config", $bpi_sync_rm_on_apply_config);
            set_option("bpi_logic_essential", base64_encode(json_encode($logic_essential)));
            break;

        default:
            break;

    }

    return $output;
}


/**
 * Sync the BPI configuration after Nagios Core update
 *
 * @param   int     $callback   Callback ID
 * @param   array   $args       Arguments passed from callback
 */
function bpi_component_sync_everything($callback, $args)
{
    $command = grab_array_var($args, 'command', '');
    if ($command != COMMAND_NAGIOSCORE_APPLYCONFIG && $command != COMMAND_NAGIOSCORE_RECONFIGURE) {
        return;
    }

    // Get sync settings for apply config
    $bpi_sync_on_apply_config = get_option("bpi_sync_on_apply_config", 1);
    $bpi_sync_rm_on_apply_config = get_option("bpi_sync_rm_on_apply_config", 1);

    // Create command to sync the hostgroups/servicegroups and delete
    // the missing hosts and services that it finds
    if ($bpi_sync_on_apply_config) {

        $command_data = "";
        if ($bpi_sync_rm_on_apply_config) {
            $command_data = "remove";
        }

        submit_command(COMMAND_BPI_SYNC, $command_data, 0, 0, null, true);
    } else if ($bpi_sync_rm_on_apply_config) {
        submit_command(COMMAND_BPI_REMOVE_MISSING, "", 0, 0, null, true);
    }
}


/**
 * Sync the new host/service names to the old ones in BPI config when apply config is run.
 *
 * @param   int     $callback   Callback ID
 * @param   array   $objs       Objects passed from callback
 */
function bpi_component_modify_hostservice($callback, $objs)
{
    $bpi_config = get_option('bpi_configfile', '/usr/local/nagiosxi/etc/components/bpi.conf');
    if (!empty($objs) && is_writable($bpi_config)) {
        $contents = file_get_contents($bpi_config);
        if (empty($contents)) {
            return;
        }

        $chars = array(';&', ';|');

        // Loop over the objects that need to be renamed
        foreach ($objs as $id => $obj) {
            $hoststring = '';
            if ($obj['type'] == 'host') {

                $old_name = stripslashes(trim(str_replace($chars, '', $obj['old_name'])));
                $new_name = stripslashes(trim(str_replace($chars, '', $obj['host_name'])));
                $find = $old_name.';';
                $replace = $new_name.';';
                $contents = str_replace($find, $replace, $contents);
                
            } else {

                $obj['hosts'] = get_service_to_host_relationships($id, $hoststring);
                $old_name = stripslashes(trim(str_replace($chars, '', $obj['old_name'])));
                $new_name = stripslashes(trim(str_replace($chars, '', $obj['service_description'])));
                
                foreach ($obj['hosts'] as $host) {
                    $host = stripslashes(trim(str_replace($chars, '', $host)));
                    $find = $host.';'.$old_name.';&';
                    $replace = $host.';'.$new_name.';&';
                    $contents = str_replace($find, $replace, $contents);
                }

            }
        }

        file_put_contents($bpi_config, $contents);
    }
}


/**
 * Remove BPI member from the config
 *
 * @param   string  $group      Group name (string/slug)
 * @param   stirng  $host       The name of the host
 * @param   string  $service    The name of the service (or NULL)
 * @return  array               Has two values of array('err', 'msg')
 */
function bpi_component_remove_member($group, $host, $service='NULL')
{
    $arr = get_config_array($group);

    // Refactor array as if it was posted from web form
    $members = explode(',', $arr['members']);

    // Reset the members array
    $arr['members'] = array();
    $critical = array();
    $chars = array(';&', ';|');

    // Refactor members array to spoof the form
    foreach ($members as $m) {
        $new = trim(str_replace($chars, '', $m));
        if (trim($m) == '') { continue; }
        if (strpos($m, '|') !== false) {
            $critical[] = $new;
        }

        if (trim($new) == "{$host};{$service}") {
            continue;
        }

        $arr['members'][] = $new;
    }

    // Same steps for all group modification functions, just refactoring the array a bit
    $users = grab_array_var($arr, 'auth_users', '');

    // Refactor auth users from string to array
    $arr['auth_users'] = explode(',', $users);

    // Reference the other values for the form array. Not going to break the UI form at this time with alterations
    $arr['groupTitle'] = & $arr['title'];
    $arr['groupDesc'] = & $arr['desc'];
    $arr['groupPrimary'] = & $arr['primary'];
    $arr['groupInfoUrl'] = & $arr['info'];
    $arr['groupWarn'] = & $arr['warning_threshold'];
    $arr['groupCrit'] = & $arr['critical_threshold'];
    $arr['groupType'] = & $arr['type'];
    $arr['critical'] = $critical;
    $arr['groupDisplay'] =& $arr['priority'];

    // Generate config string to save to file
    $config_string = process_post($arr, false);
    if ($config_string) {
        list($err, $msg) = edit_group($group, $config_string);
        return array($err, strip_tags($msg));
    }
}
