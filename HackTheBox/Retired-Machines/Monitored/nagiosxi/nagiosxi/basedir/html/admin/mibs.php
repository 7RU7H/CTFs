<?php
//
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../includes/components/nxti/includes/utils-traps.inc.php');


// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


// Only admins can access this page
if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

route_request();


function route_request()
{
    global $request;

    $mode = '';
    if (isset($request['mode'])) {
        $mode = $request['mode'];    
    }

    switch ($mode) {
        case 'download':
            do_download();
            break;
        case 'upload':
            $mode = MIB_UPLOAD_DO_NOTHING;

            $process = false;
            if (isset($request["processMibCheck"])) {
                $process = $request["processMibCheck"];
            }

            if ($process) {
                $mode = get_processing_mode();
            }

            do_upload($mode);
            break;
        case 'delete':
            do_delete();
            break;
        case 'process-all':
            do_process_all(get_processing_mode());
            break;
        case 'process-trap':
            do_process_single(get_processing_mode());
            break;
        case 'undo-all-processing':
            undo_process_all();
            break;
        case 'undo-processing':
            undo_process_single();
            break;
        case 'change-processing-mode':
            $new_value = $request['new_value'];
            do_change_processing_mode($new_value);
            break;
        case 'partial-traps':
            show_partial_traps_table();
            break;
        default:
            show_mibs();
    }

    exit;
}


# this gets $().html()'ed into a tbody
function show_partial_traps_table() {
    $mib_name = grab_request_var('mib_name', '');

    $trap_definitions = mibs_get_associated_traps($mib_name);

    foreach ($trap_definitions as $name => $mib) {
        foreach ($mib as $trap_definition) {
            echo '<tr>';
            echo '<td>' . $name . '</td>';
            echo '<td>' . $trap_definition['event_name'] . '</td>';
            echo '<td>' . $trap_definition['oid'] . '</td>';
            echo '<td>' . $trap_definition['category'] . '</td>';
            echo '<td>' . $trap_definition['severity'] . '</td>';
            echo '</tr>';
        }
    }
}

function get_processing_mode() {
    if (is_nxti_used()) {
        return MIB_UPLOAD_NXTI;
    }
    return MIB_UPLOAD_PROCESS_ONLY;
}

function undo_process_single() {
    check_nagios_session_protector();
    // Mode needs to be based on processing type of MIB, not on 'current' processing type

    $file = grab_request_var('file', '');
    $name = grab_request_var('name', '');
    $current_type = intval(grab_request_var('type', MIB_UPLOAD_DO_NOTHING));

    if ($current_type !== MIB_UPLOAD_PROCESS_ONLY && $current_type !== MIB_UPLOAD_NXTI) {
        show_mibs(false, _("No processing to be undone"));
    }

    undo_processing($file, $name, $current_type);

    $error_message = '';
    $result = commit_trapdata_db($error_message);

    if ($result == 0) {
        restart_snmptt($error_message);
    }

    if ($error) {
        show_mibs(true, $error_message);
        return;
    }

    show_mibs(false, sprintf(_("Successfully reverted %s to 'uploaded' state, restarted SNMPTT"), $name));
}

function undo_process_all() {
    check_nagios_session_protector();

    $mibs = get_mibs();
    foreach ($mibs as $mib) {

        if ($mib['file'] === mibs_missing_file_string() 
            || $mib['mib_uploaded'] === mibs_missing_db_string()) {
            // If it's missing either the file or DB component, 
            // then the file hasn't been processed in the first place.
            continue;
        }

        undo_processing($mib['file'], $mib['mib_name'], $mib['mib_type']);
    }

    $error_message = '';
    $result = commit_trapdata_db($error_message);

    if ($result == 0) {
        restart_snmptt($error_message);
    }

    if ($error) {
        show_mibs(true, $error_message);
        return;
    }

    show_mibs(false, _('Reverted all MIBs to \'uploaded\' state, restarted SNMPTT'));
}

function undo_processing($file, $name, $current_type) {

    if ($current_type !== MIB_UPLOAD_PROCESS_ONLY && $current_type !== MIB_UPLOAD_NXTI) {
        return;
    }

    $current_conf_path = get_processing_destination($current_type) . '/' . $file;

    remove_snmpttconvertmib_files(array($file));

    if ($current_type === MIB_UPLOAD_PROCESS_ONLY) {
        $get_event_names_cmd = get_root_dir() . "/scripts/nxti_import.php ".escapeshellarg($current_conf_path)." --no-insert";
        exec($get_event_names_cmd, $all_events, $rc);
        $all_events = array_unique($all_events);

        remove_from_snmptt_conf($all_events);
    }

    mibs_revert_db_entry($name);
}

function do_change_processing_mode($new_value) {
    $status = array(
        'status' => 'success'
    );

    $success = true;
    if ($new_value === 'true') {
        set_option('is_nxti_used', 1);
    }
    else if ($new_value === 'false') {
        set_option('is_nxti_used', 0);
    }
    else {
        $status['status'] = 'failure';
        $status['errmsg'] = _("Failed to set option is_nxti_used: invalid input.");
    }

    $status['new_value'] = get_option('is_nxti_used');
    print json_encode($status);
}

function get_map_from_file($filename) {
    $map = array();
    $entries = explode("\n", file_get_contents($filename));
    foreach ($entries as $entry) {
        if (!empty($entry)) {
            $parts = explode(':', $entry);
            $map[$parts[2]] = $parts[0];
        }
    }
    return $map;
}
/**
 * @param bool   $error
 * @param string $msg
 */
function show_mibs($error = false, $msg = "")
{
    do_page_start(array("page_title" => _('Manage MIBs')), true);

    $users = get_map_from_file('/etc/passwd');

    $groups = get_map_from_file('/etc/group');

    $is_nxti_used = is_nxti_used();

    $mibs = get_mibs();

    $nxti_link = '<a href="'. get_component_url_base('nxti') . '/index.php" icon="fa-th-large" target="maincontentframe"><span class="menu-icon"><i class="fa fa-fw fa-th-large"></i></span>' . _("SNMP Trap Interface") . '</a>';
?>

    <h1><?php echo _('Manage MIBs'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <script type='text/javascript'>
        var enterprise_features_enabled = <?php echo (enterprise_features_enabled() ? true : false); ?>;

        $(document).ready(function() {
            $('#nxti-is-used').click(function () {
                if (!(this.checked || enterprise_features_enabled)) {
                    alert('<?php echo _("Warning: the SNMP Trap Interface is an enterprise-only feature.");?>');
                }
            });
        });
    </script>

    <script type='text/javascript' src='/nagiosxi/includes/js/mibs.js'></script>

    <style type="text/css">
        span.wrong-processing {
            color: red;
        }
    </style>

    <div id="text-samples">
        <div class="hide" id="title-wrong-processing">
            <?php echo _("Undo processing before reprocessing this MIB."); ?>
        </div>
        <div class="hide" id="title-is-upload">
            <?php echo _("File not yet processed."); ?>
        </div>
        <div class="hide" id="title-missing-file-process">
            <?php echo _("Cannot Process Traps - File Missing"); ?>
        </div>
        <div class="hide" id="title-missing-file-undo">
            <?php echo _("Cannot Undo Trap Processing - File Missing"); ?>
        </div>
        <div class="hide" id="title-missing-file-save">
            <?php echo _('Cannot Download - File Missing'); ?>
        </div>
        <div class="hide" id="title-process-default">
            <?php echo _('Process Traps'); ?>
        </div>
        <div class="hide" id="title-undo-default">
            <?php echo _("Undo Trap Processing"); ?>
        </div>
        <div class="hide" id="title-save-default">
            <?php echo _("Download"); ?>
        </div>

    </div>
    <p>
        <?php echo _('Manage the MIBs installed on this server in') . ' <b>' . get_mib_dir() .  '</b>. '.  _('There are hundreds of MIBs available at'); ?>
        <a href="http://www.mibdepot.com/" target="_blank" rel="noreferrer">mibdepot<i class="fa fa-external-link fa-ml"></i></a> <?php echo _('and'); ?> <a href="http://www.oidview.com/mibs/detail.html" target="_blank">oidview<i class="fa fa-external-link fa-ml"></i></a>.
    </p>

    <table class="table table-condensed table-no-border table-auto-width">
        <tbody>
            <tr>
                <td style="padding-right: 0px;">
                    <input type="checkbox" class="" style="margin: 2 0 2 0;" name="nxti-is-used" id="nxti-is-used" <?php echo is_checked($is_nxti_used, '1'); ?>> 
                </td>
                <td>
                    <label for="nxti-is-used" style="vertical-align: bottom">
                        <?php echo sprintf(_('Check this box if this server uses the %1$s.'), $nxti_link);?>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>

    <form enctype="multipart/form-data" action="" method="post" style="margin: 0;">
        <div class="well" style="width: 740px;">
            <?php echo get_nagios_session_protector(); ?>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_upload_max_filesize(); ?>">

            <div class="fl" style="height: 29px; line-height: 29px; margin-right: 10px; font-weight: bold; color: #666;">
                <label><?php echo _('Upload a MIB'); ?>:</label>
            </div>
            <div class="fl">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-btn">
                        <span class="btn btn-sm btn-default btn-file">
                            <?php echo _('Browse'); ?>&hellip; <input type="file" name="uploadedfile[]" multiple>
                        </span>
                    </span>
                    <input type="text" class="form-control" style="width: 200px;" readonly>
                </div>
            </div>
            <div class="fl">
                <div class="checkbox" style="margin: 3px 10px;">
                    <label>
                        <input type="checkbox" name="processMibCheck" value="YES">
                        <?php echo _("Process traps"); ?>
                    </label>
                </div>
            </div>
            <button type="submit" name="mode" value="upload" class="btn btn-sm btn-primary"><?php echo _('Upload MIB'); ?></button>
            <div class="clear"></div>
        </div>
        <div class="well" style="text-align: center; display: flex; justify-content: space-around; width: 740px;">
            <button type="button" id="view-file-permissions" class="btn btn-sm btn-default"><?php echo _("View File Permissions"); ?></button>
            <button type="submit" id="process-all-traps" name="mode" value="process-all" class="btn btn-sm btn-default"><div class="" id="process-all-throbber" style="display: none"><img class="" src="/nagiosxi/images/throbber.gif">&nbsp;</div><?php echo _("Process All Traps"); ?></button>
            <button type="submit" id="undo-all-processing" name="mode" value="undo-all-processing" class="btn btn-sm btn-default"><?php echo _('Undo All Trap Processing'); ?></button>
            <button type="button" id="view-associated-traps" class="btn btn-sm btn-default"><?php echo _("View All Associated Traps"); ?></button>

        </div>
    </form>

    <div id="mainTableContainer" class="">
        <table id="mainTable" class="table table-condensed table-striped table-bordered table-auto-width">
            <thead>
                <tr>
                    <th><?php echo _("MIB"); ?></th>
                    <th><?php echo _("First Uploaded"); ?></th>
                    <th><?php echo _("Status"); ?></th>
                    <th><?php echo _("Date Processed"); ?></th>
                    <th class="nxti-only"><?php echo _("# Assoc Traps"); ?></th>
                    <th><?php echo _("Actions"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                // case insensitive sorting
                usort($mibs, function($x, $y) {
                   return strcasecmp($x['mibname'] , $y['mibname']);
                });
                foreach ($mibs as $mib) {

                    switch ($mib['mib_type']) {
                        case MIB_UPLOAD_DO_NOTHING:
                            $mib_type = _("Uploaded");
                            break;
                        case MIB_UPLOAD_PROCESS_ONLY:
                            $mib_type = _("Processed");
                            if ($is_nxti_used) {
                                $mib_type = '<span class="processing wrong-processing tt-bind-dynamic" title="'._("This MIB was processed in a different environment. Please process this MIB again.").'" data-maybe-title="'._("This MIB was processed in a different environment. Please process this MIB again.").'">'.$mib_type.'</span>';
                            }
                            else {
                                $mib_type = '<span class="processing tt-bind-dynamic" data-maybe-title="'._("This MIB was processed in a different environment. Please process this MIB again.").'">'.$mib_type.'</span>';
                            }
                            break;
                        case MIB_UPLOAD_NXTI:
                            $mib_type = _("Processed");
                            // This is the reverse of the PROCESS_ONLY case.
                            if ($is_nxti_used) {
                                $mib_type = '<span class="processing tt-bind-dynamic" data-maybe-title="'._("This MIB was processed in a different environment. Please process this MIB again.").'">'.$mib_type.'</span>';
                            }
                            else {
                                $mib_type = '<span class="processing wrong-processing tt-bind-dynamic" title="'._("This MIB was processed in a different environment. Please process this MIB again.").'" data-maybe-title="'._("This MIB was processed in a different environment. Please process this MIB again.").'">'.$mib_type.'</span>';
                            }
                            break;
                        default:
                        case MIB_TYPE_UNKNOWN:
                            $mib_type = _("Unknown");
                            break;
                    }

                    $save_classes = array('mibs-save');
                    $undo_classes = array('mibs-undo');
                    $process_classes = array('mibs-process');

                    $mib_count_assoc_traps = $mib['mib_count_assoc_traps'];
                    if (is_numeric($mib['mib_count_assoc_traps']) && intval($mib_count_assoc_traps) !== 0) {
                        $mib_count_assoc_traps = '<a class="show-partial-traps" data-mib-name="'.$mib['mib_name'].'">' . $mib_count_assoc_traps . '</a>';    
                    }

                    if ($mib['mib_type'] === MIB_UPLOAD_DO_NOTHING) {
                        $undo_classes[] = 'is-upload';
                    }
                    else if (($mib['mib_type'] === MIB_UPLOAD_NXTI && !$is_nxti_used)
                        || ($mib['mib_type'] === MIB_UPLOAD_PROCESS_ONLY && $is_nxti_used)) {
                        $process_classes[] = 'wrong-processing';
                    }
                    else {
                        $process_classes[] = 'correct-processing';
                    }

                    if ($mib['file'] === mibs_missing_file_string()) {
                        $save_classes[] = 'missing-file';
                        $undo_classes[] = 'missing-file';
                        $process_classes[] = 'missing-file';
                    }


                    $save_button = '<a style="padding: 0 1px;" data-href="?mode=download&file='.$mib['file'].'"><img class="tt-bind-dynamic '.implode(' ', $save_classes).'" src="' . theme_image("download.png") . '" title="'._("Download").'"></a>';
                    $undo_button = '<a style="padding: 0px 1px; margin-right: -5px" data-href="?mode=undo-processing&name='.$mib['mib_name'].'&file='.$mib['file'].'&type='.$mib['mib_type'].'&nsp='.get_nagios_session_protector_id().'"><img class="tt-bind-dynamic '.implode(' ', $undo_classes).'" src="' . theme_image("b_prev.png") . '"></a>';
                    $process_button = '<a style="padding: 0px 1px; margin-right: -2px" data-href="?mode=process-trap&name='.$mib['mib_name'].'&file='.$mib['file'].'&nsp='.get_nagios_session_protector_id().'"><img class="tt-bind-dynamic '.implode(' ', $process_classes).'" src="' . theme_image("b_next.png") . '"></a>';

                    $delete_href = "?mode=delete&nsp=" . get_nagios_session_protector_id();
                    if ($mib['file'] !== mibs_missing_file_string()) {
                        $delete_href .= "&file=" . $mib['file'];
                    }
                    if ($mib['mib_uploaded'] !== mibs_missing_db_string()) {
                        $delete_href .= "&name=" . $mib['mib_name'];
                    }
                    $delete_button = '<a style="padding: 0 1px;" href="'.$delete_href.'"><img class="tt-bind" src="' . theme_image("cross.png") . '" title="'._("Delete").'"></a>';

                    $actions = $save_button . $undo_button . $process_button . $delete_button;

                ?>
                    <tr>
                        <td><?php echo $mib['mib_name'];?></td>
                        <td><?php echo $mib['mib_uploaded'];?></td>
                        <td><?php echo $mib_type;?></td>
                        <td><?php echo $mib['mib_last_processed'];?></td>
                        <td class="nxti-only"><?php echo $mib_count_assoc_traps;?></td>
                        <td><?php echo $actions;?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="viewAllAssociatedTrapsModal" class="modal hide">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2><?php echo _("Associated Trap Definitions - All");?></h2>
                </div>
                <div class="modal-body" style="height: 500px; overflow-y: auto;">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo _('MIB'); ?></th>
                                <th><?php echo _('Event Name'); ?></th>
                                <th><?php echo _('OID'); ?></th>
                                <th><?php echo _('Category'); ?></th>
                                <th><?php echo _('Severity'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $mib_names = array();
                        foreach ($mibs as $mib) {
                            $mib_names[] = $mib['mib_name'];
                        }

                        //echo '<pre>' . print_r($mib_names, true) . '</pre>';
                        $traps = mibs_get_associated_traps($mib_names);

                        $trap_sentinel = false;
                        foreach ($traps as $mib_name => $tf) {
                            foreach ($tf as $t) {
                                $trap_sentinel = true;
                                echo '<tr>';
                                echo '<td>' . $mib_name . '</td>';
                                echo '<td>' . $t['event_name'] . '</td>';
                                echo '<td>' . $t['oid'] . '</td>';
                                echo '<td>' . $t['category'] . '</td>';
                                echo '<td>' . $t['severity'] . '</td>';
                                echo "</tr>\n";
                            }
                        }

                        if ($trap_sentinel === false) {
                            echo '<tr>';
                            echo '<td colspan="5">' . _("No associated trap definitions! Make sure you are using the SNMP Trap Interface and process some MIB files!") . '</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><?php echo _("Close");?></button>
                </div>
            </div>
        </div>
    </div>


    <div id="viewPartialAssociatedTrapsModal" class="modal hide">
        <div class="modal-dialog" style=" width: 700px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2><?php echo sprintf(_("Associated Trap Definitions - %s"), '<span class="mib-name"></span>');?></h2>
                </div>
                <div class="modal-body" style="height: 500px; overflow-y: auto;">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo _("MIB"); ?></th>
                                <th><?php echo _('Event Name'); ?></th>
                                <th><?php echo _('OID'); ?></th>
                                <th><?php echo _('Category'); ?></th>
                                <th><?php echo _('Severity'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="mib-trap-contents">
                        <?php
                        $traps = mibs_get_associated_traps($mibs);

                        $x = 0;
                        foreach ($mibs as $mib) {

                            if (array_key_exists($mib['mib_name'], $traps)) {
                                foreach ($traps[$mib['mib_name']] as $trap_definition) {

                                    echo '<tr>';
                                    echo '<td>' . $mib['mib_name'] . '</td>';
                                    echo '<td>' . $trap_definition['event_name'] . '</td>';
                                    echo '<td>' . $trap_definition['oid'] . '</td>';
                                    echo '<td>' . $trap_definition['category'] . '</td>';
                                    echo '<td>' . $trap_definition['severity'] . '</td>';
                                    echo "</tr>\n";
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><?php echo _("Close");?></button>
                </div>
            </div>
        </div>
    </div>

    <div id="filePermissionsModal" class="modal hide" style="width: 100%;">
        <div class="modal-dialog" style=" width: 900px">
            <div class="modal-content" style="border-radius: 0px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3><?php echo _("MIB File Permissions");?></h3>
                </div>
                <div class="modal-body" style="height: 500px; overflow-y: auto; padding-right: 30px; padding-left: 30px">
                    <table class="table table-condensed table-striped table-bordered table-auto-width">
                        <thead>
                            <tr>
                                <th><?php echo _('MIB'); ?></th>
                                <th><?php echo _('File'); ?></th>
                                <th><?php echo _('Owner'); ?></th>
                                <th><?php echo _('Group'); ?></th>
                                <th><?php echo _('Permissions'); ?></th>
                                <th><?php echo _('Date'); ?></th>
                                <!--<th class="center"><?php echo _('Actions'); ?></th>-->
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                        $x = 0;
                        foreach ($mibs as $mib) {

                            if (array_key_exists($mib['group'], $groups)) {
                                $group = $groups[$mib["group"]];
                                if ($group != 'nagios' && $group != 'nagcmd') {
                                    $group = '<em>' . $group . '</em>';
                                }
                            } else {
                                $group = $mib['group'];
                            }

                            if (array_key_exists($mib['owner'], $users)) {
                                $user = $users[$mib["owner"]];
                                if ($user != 'nagios' && $user != 'nagcmd') {
                                    $user = '<em>' . $user . '</em>';
                                }
                            } else {
                                $user = $mib['owner'];
                            }

                            echo "<tr>";
                            echo "<td>" . $mib["mibname"] . "</td>";
                            echo "<td>" . $mib["file"] . "</td>";
                            echo "<td>" . $user . "</td>";
                            echo "<td>" . $group . "</td>";
                            echo "<td>" . $mib["permstring"] . "</td>";
                            echo "<td>" . $mib["date"] . "</td>";
                            echo "</tr>\n";
                        }
                        ?>

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><?php echo _("Close");?></button>
                </div>
            </div>
        </div>
    </div>
    <?php

    do_page_end(true);
    exit();
}

/* Given a list of event names to comment out in snmptt.conf,
 * parse through the file line by line and comment out the entire
 * event def'n.
 */
function remove_from_snmptt_conf($event_names) {
    $the_file = '/etc/snmp/snmptt.conf';
    $lines = file($the_file);
    $commenting = false;

    foreach ($lines as $index => $line) {
        if (preg_match('/^\s*?EVENT.*$/', $line)) {
            // new event defn, figure out whether to keep commenting.
            $terms = explode(' ', $line);
            $this_event = $terms[1];
            $commenting = false;

            if (in_array($this_event, $event_names)) {
                $lines[$index] = "# This trap definition is being managed by NXTI\n#" . $line;
                $commenting = true;
            }
        }
        else {
            if ($commenting) {
                $lines[$index] = "#" . $line;
            }
        }
    }

    $lines = implode('', $lines);
    $success = file_put_contents($the_file, $lines);
    return $success;
}

function handle_nxti_import($files , &$message) {
    if (!is_array($files)) {
        $files = array($files);
    }

    $base_command = get_root_dir() . '/scripts/nxti_import.php '; // Have nxti_import print event names, \n-delimited
    $okay = true;
    $message .= sprintf(_("Note: If a conf file is missing from the list, it may not contain any trap definitions.%s"), '<br/>');

    $all_events = array(); // To be removed from snmptt.conf
    foreach ($files as $ind => $file) {
        $command = $base_command . escapeshellarg($file);
        $rc = 0;
        $event_names = array();
        $unused = exec($command, $event_names, $rc);

        $all_events = array_unique(array_merge($all_events, $event_names));

        if ($rc !== 0) {
            $okay = false;
            $message .= sprintf(_("Failed to import conf file %s %s"), $files[$ind], '<br/>');
            unset($files[$ind]);
        }
        else {
            $files[$ind] = basename($file);
            $message .= sprintf(_("Imported conf file %s %s"), $files[$ind], '<br/>');
        }
    }

    $db_message = '';
    $error = commit_trapdata_db($db_message);
    $message .= $db_message . '<br/>';

    // Removes definitions added by addmib
    remove_from_snmptt_conf($all_events);

    // Removes definitions added by snmpttconvertmib
    remove_snmpttconvertmib_files($files);

    return $okay;
}


// From here to end of fn removes definitions added by snmpttconvertmib.
function remove_snmpttconvertmib_files($files) {
    $ini = get_snmptt_ini_path();
    $lines = file($ini);

    // Poor man's sed
    // Look for the part where we read in conf files, remove any which are now managed by NXTI.
    $started = false;
    foreach ($lines as $index => $line) {
        // Trigger once
        if (!$started && substr(trim($line), 0, 17) !== "snmptt_conf_files") {
            continue;
        }

        $started = true;
        // If we match any of the base filenames
        if (preg_match('/'.implode('|', $files).'/i', $line)) {
            // This is managed by NXTI, so we can remove the 'manual' conf file.
            unset($lines[$index]);
        }
        if (trim($lines[$index+1]) === "END") {
            break;
        }
    }

    $lines = implode('', $lines);
    $result = file_put_contents($ini, $lines);
    return $result;
}

// Same as ticking the checkbox on an uploaded file, but does it for every file that's already been uploaded.
function do_process_all($mode) {
    check_nagios_session_protector();

    $source_directory = get_mib_dir();
    $dest_directory = get_processing_destination($mode);

    $success_count = 0;
    $total_count = 0;
    $mibs = get_mibs();

    if ($nxti_call) {
        $files = array();
    }

    foreach ($mibs as $mib) {
        $source_full = $source_directory . '/' . $mib['file'];

        $dest_file = $dest_directory . '/' . $mib['file'];
        if ($mib['mib_type'] !== $mode && $mib['mib_type'] !== MIB_TYPE_UNKNOWN && $mib['mib_type'] !== MIB_UPLOAD_DO_NOTHING) {
            continue;
        }

        $result = handle_mib_processing_and_import($mode, $source_full, $dest_file);

        if (isset($result['error']) && $result['error'] == 1) {
            $errors[] = $result['msg'];
        }
        else {
            $files[] = $dest_file;
            $success_count += 1;
        }

        $total_count++;
    }

    if ($total_count === $success_count) {
        $result_message = sprintf(_('%s MIB files processed'), $total_count) . "<br>";
        $error = false;
    }
    else {
        $result_message = sprintf(_('%1$d of %2$d MIB files successfully processed'), $success_count, $total_count) . '<br>';
        $result_message .= implode("<br/>", $errors);
        $error = true;
    }

    if (restart_snmptt($restart_message) !== 0) {
        $result_message .= "<br>" . $restart_message;
    }

    show_mibs($error, $result_message);
}

function do_download()
{

    $file = grab_request_var("file", "");


    // clean the filename
    $file = str_replace("..", "", $file);
    $file = str_replace("/", "", $file);
    $file = str_replace("\\", "", $file);

    $dir = get_mib_dir();
    $thefile = $dir . "/" . $file;

    $mime_type = "";
    header('Content-type: ' . $mime_type);
    header("Content-length: " . filesize($thefile));
    header('Content-Disposition: attachment; filename="' . basename($thefile) . '"');
    readfile($thefile);
    exit();
}

/**
 * @param $src
 * @param $dest
 * @param $message
 * @param $exec - ***IMPORTANT*** - this should ALWAYS be generated by the program, preferably as a literal.
 *                                - otherwise, this opens XI to bash injection
 *
 * @return int
 */
function convert_mib($src, $dest, &$message, $exec='')
{
    $rc = 0;
    $lrc = 0;
    $convertmib_location = '/usr/bin/snmpttconvertmib';
    $integration_path = "<a href='https://assets.nagios.com/downloads/nagiosxi/docs/Integrating_SNMP_Traps_With_Nagios_XI.pdf' target='_blank'>here</a>";
    $locate_output = array();
    $convert_results = array();
    $matches = array();
    $success_string = array();

    // locate snmpttconvertmib
    $locate_convertmib = 'which snmpttconvertmib 2>&1';
    exec($locate_convertmib, $locate_output, $lrc);

    // construct and execute command if snmpttconvertmib is available
    if ($lrc != 0) {
        // snmpttconvertmib.py is missing or not found error code
        $rc = 4;
    } else {
        if (!is_executable($convertmib_location) && !empty($locate_output)) {
            $convertmib_location = $locate_output[0];
        }
        if ($exec !== '') {
            $exec = ' --exec=\'' . $exec . '\'';
        }
        $convert_cmd = $convertmib_location . ' --in=' . escapeshellarg($src) . ' --out=' . escapeshellarg($dest) . $exec . ' 2>&1';

        exec($convert_cmd, $convert_results, $rc);
    }

    if ($rc != 0) {
        if ($rc == 4) {
            // show location output error and link to integration script
            $message = sprintf(_("snmpttconvertmib is not in the correct location or is not installed. If you have not run the %s SNMP Integration script it is located %s."), get_product_name(), $integration_path);
        } 
        else if ($rc == 13) {
            $message = sprintf(_("Check permissions on files/directories: %s, %s. The apache user should be able to write to both of them. "), dirname($src), dirname($dest));
        }
        else {
            $message = _("Failed to convert uploaded file to snmptt mib.");
        }
    }
    else {
        foreach ($convert_results as $line) {
            if (preg_match('/Failed translations:\s*(\d+)/', $line, $matches) === 1) {
                if ($matches[1] > 0) {
                    $rc = 1;
                    $message = _("Uploaded file had one or more failed translations when converting to snmptt mib.");
                    break;
                }
            } else if (strpos($line, "translations:") !== false) {
                $success_string[] = $line;
            } else if (strpos($line, "MIB file did not contain any TRAP-TYPE or NOTIFICATION-TYPE") !== false) {
                $rc = 2;
                $success_string[] = "MIB was added, but did not contain any TRAP-TYPE definitions.";
                break;
            }
        }

        if ($rc === 0 || $rc === 2) {
            $message = implode("<br />", $success_string);
        }
    }
    return $rc;
}

/**
 * @return string
 */
function get_snmptt_ini_path()
{
    return '/etc/snmp/snmptt.ini';
}

/**
 * @param $mib
 * @param $message
 *
 * @return int
 */
function add_mib_to_conf($mib, &$message)
{
    //add processed mib to snmptt.ini
    $sed_status = 0;
    $sed_results = array();
    $snmptt_ini_path = get_snmptt_ini_path();

    $grep_cmd = "grep $mib $snmptt_ini_path";
    exec($grep_cmd, $grep_arry, $rc);
    if ($rc == 0) {
        return 0; //already in file
    }

    // Note: sed creates a temporary file, requiring that it be able to write to the directory where the target file is located.
    // As a workaround, we're using sed to write to a file in our tmp directory, then copying the contents to our ini file.

    $temp_file = get_tmp_dir() . '/' . uniqid();
    $add_to_temp_cmd = "cp '$snmptt_ini_path' '$temp_file' && /bin/sed -i '/^snmptt_conf_files/a" . $mib . "' '$temp_file'";
    exec($add_to_temp_cmd, $sed_results, $sed_status);

    if ($sed_status != 0) {
        $message = _("Failed to add converted mib path to snmptt.ini") . ". (rc: " . $sed_status . ")";
    }

    $transfer_command = "cat '$temp_file' > '$snmptt_ini_path'";
    exec($transfer_command, $cat_results, $cat_status);

    if ($cat_status != 0) {
        $message = _("Failed to add converted mib path to snmptt.ini") . ". (rc: " . $cat_status . ")";
    }

    return $sed_status;
}

function which_mib_converter($mode) {
    $addmib = '/usr/local/bin/addmib';

    if (is_executable($addmib) && $mode != MIB_UPLOAD_NXTI) {
        return 'addmib';
    }
    else {
        return 'convertmib';
    }
}

/**
 * @param $mib
 * @param $message
 *
 * @return mixed
 */
function run_addmib($mib, &$message)
{
    //run addmib command if command exists
    $addmib = '/usr/local/bin/addmib';
    $success_output = array();

    exec($addmib . " " . $mib, $addmib_result, $addmib_returncode);

    if ($addmib_returncode != 0 
        && !empty($addmib_result) 
        && strpos($addmib_result[0], "This file looks like it has been added already!") === false) {

        // send error message from exec
        $message = implode("<br>", $addmib_result);
        return 1;
    } else {
        foreach ($addmib_result as $line) {
            if (strpos($line, "translations:") !== false) {
                $success_output[] = $line;
            } else if (strpos($line, "MIB file did not contain any TRAP-TYPE or NOTIFICATION-TYPE") !== false) {
                $success_output[] = _("MIB was added, but did not contain any SNMP Trap definitions.");
                break;
            }
        }

        $message = implode("<br>", $success_output);
        return 0;
    }
}

// taken from php.net comments - http://php.net/manual/en/features.file-upload.multiple.php
// Makes it easier to loop through existing code instead of rewriting to use php's weird data structure
function rearrange_multiple_upload($file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

/**
 * @param $mode - Whether to simply upload, process as part of an external config, or import into NXTI.
 */
function do_upload($mode) {
    // demo mode
    if (in_demo_mode() == true) {
        show_mibs(true, _("Changes are disabled while in demo mode."));
    }

    if ($mode === MIB_UPLOAD_NXTI && !enterprise_features_enabled()) {
        show_mibs(true, _("Upload failed: the SNMP Trap Interface is an enterprise-only feature."));
    }

    if ($mode != MIB_UPLOAD_DO_NOTHING) {

        // Check that snmptt.ini is a file we can write to.
        if (!file_exists(get_snmptt_ini_path())) {

            show_mibs(true, _('MIB could not be installed - snmptt is not installed.'));
            exit();
        } elseif (!is_writable(get_snmptt_ini_path())) {

            show_mibs(true, 'snmptt.ini ' . _('is not writable by Nagios.') . '<br>' . _('Run the following from the command line') . ':<br><br>chown root.nagios /etc/snmp/snmptt.ini /etc/snmp<br>
    chmod g+w /etc/snmp/snmptt.ini /etc/snmp');
            exit();
        }
    }

    check_nagios_session_protector();

    $_FILES['uploadedfile'] = rearrange_multiple_upload($_FILES['uploadedfile']);

    $error = false;
    foreach ($_FILES['uploadedfile'] as $file) {    

        $target_path = install_mib($file);

        mibs_add_entry($target_path);

        if ($mode == MIB_UPLOAD_DO_NOTHING) {
            continue;
        }
        else {
            $convert_path = get_processing_destination($mode);

            $target_path_parts = pathinfo($target_path);
            $convert_path .= '/' . $target_path_parts['filename'] . '.txt';
            $convert_message = "";

            $result = handle_mib_processing_and_import($mode, $target_path, $convert_path, $convert_message);
        }
        if (isset($result['error'])) {
            $result_message .= "<br>" . $result['msg'];
            $error = true;
        }
    }

    if (!$error) {
        $result_message = _('MIB file(s) successfully installed') . ". <br>";
    }

    if (restart_snmptt($restart_message) !== 0) {
        $result_message .= "<br>" . $restart_message;
    }

    show_mibs($error, $result_message);

    exit();
}

function do_process_single($mode) {
    check_nagios_session_protector();

    $source_directory = get_mib_dir();
    $dest_directory = get_processing_destination($mode);

    $source_file = grab_request_var('file', '');
    if (empty($source_file)) {
        show_mibs(true, _('Please specify a MIB to process.'));
    }

    $dest_file_full = $dest_directory . '/' . $source_file;
    $source_file_full = $source_directory . '/' . $source_file;


    $result = handle_mib_processing_and_import($mode, $source_file_full, $dest_file_full);

    if (restart_snmptt($restart_message) !== 0) {
        $result['msg'] .= "<br>" . $restart_message;
    }

    if (isset($result['error'])) {
        show_mibs(true, $result['msg']);
    }
    show_mibs(false, $result['msg']);
}

function install_mib($file_object) {

    $target_path = get_mib_dir();
    $target_path .= "/";
    $target_path .= basename(str_replace(array(" ", "`", "$", "(", ")"), "_", $file_object['name']));

    $error = $file_object['error'];
    if ($error == UPLOAD_ERR_OK) {
        $test_write = move_uploaded_file($file_object['tmp_name'], $target_path);
    } else if ($error == UPLOAD_ERR_INI_SIZE) {
        $test_write = false;
        $additional_error = _('Your php.ini upload_max_size is likely lower than the size of the uploaded MIB. Update it and restart apache.');
    } else if ($error == UPLOAD_ERR_NO_FILE) {
        $test_write = false;
        $additional_error = _('No file was uploaded.');
    } else {
        $test_write = false;
        $additional_error = _('Directory permissions may not be correct. Check that /usr/share/snmp/mibs is writable by the nagios group.');
    }

    if (!$test_write) {
        show_mibs(true, _("MIB could not be installed.") . ' ' . $additional_error);
        exit;
    }

    chmod($target_path, 0664);
    chgrp($target_path, 'nagios');

    return $target_path;
}

// Arguably this can be rolled into convert_mib, but it's checking the actual results rather than 
// direct messages from the executable.
function run_mib_conversion($target_path, $convert_path, &$convert_message, $exec='') {

    $rc = convert_mib($target_path, $convert_path, $convert_message, $exec);



    if ($rc !== 0 && $rc !== 1) {
        if ($rc === 2) {
            // Nothing found in MIB
            return 2;
        }
        return 1;
    }
    else if (!file_exists($convert_path)) {
        $convert_message = _("Unable to create file ") . $convert_path;
        return 1;
    }

    if (add_mib_to_conf($convert_path, $convert_message) != 0) {
        unlink($convert_path);
        return 1;
    }
    else {
        $convert_message = _('MIB file successfully processed') . ": <br>" . $convert_message;

        return 0;
    }
}

// Returns an array with key 'msg', pointing to a string.
// May also have key 'error'. error = 1 implies a 'fatal' error, 
// error = 2 is an 'error' that may or may not actually be a problem
function handle_mib_processing_and_import($mode, $target_path, $convert_path) {

    if ($mode == MIB_UPLOAD_DO_NOTHING) {
        return;
    }

    $convert_message = '';

    if (which_mib_converter($mode) == "addmib") {
        $rc = run_addmib($target_path, $convert_message);

        if ($rc !== 0) {
            return array('msg' => $convert_message . "\nrc $rc", 'error' => 1);
        } 
    }
    else {
        if ($mode === MIB_UPLOAD_NXTI) {
            $exec = '/usr/local/bin/snmptraphandling.py "$aR" "SNMP Traps" "$s" "$@" "$-*"';
        }
        $rc = run_mib_conversion($target_path, $convert_path, $convert_message, $exec);

        if ($rc === 1) {
            return array('msg' => $convert_message . "\nrc $rc", 'error' => 1);
        }
    }

    if ($mode === MIB_UPLOAD_NXTI) {

        $result_okay = handle_nxti_import($convert_path, $message);
        $ret = array('msg' => $message);
        if ( !$result_okay ) {
            $ret['error'] = 2;
        }
        else {
            unlink($convert_path);
        }
    }


    $mib_name = mib_name_from_path($convert_path);

    update_mibs_database_entry($mib_name, $mode);

    if ($rc === 2) {
        return array('msg' => $convert_message, 'error' => $rc);
    }

    return $ret;
}

function update_mibs_database_entry($mib_name, $mode) {

    switch ($mode) {
        case MIB_UPLOAD_DO_NOTHING:
            $mode = 'upload';
            break;
        case MIB_UPLOAD_PROCESS_ONLY:
            $mode = 'process_manual';
            break;
        case MIB_UPLOAD_NXTI:
            $mode = 'process_nxti';
            break;
    }
    mibs_update_db($mib_name, $mode, false);
}

function do_delete()
{
    // demo mode
    if (in_demo_mode() == true) {
        show_mibs(true, _("Changes are disabled while in demo mode."));
    }

    // check session
    check_nagios_session_protector();

    $mib_name = grab_request_var("name", "");
    $db_result = mibs_remove_db_entry($mib_name);

    $file = grab_request_var("file", "");
    $file_result = mibs_remove_file($file);

    if ($file_result && $db_result) {
        // success!
        show_mibs(false, _("MIB deleted."));
    }
    else if ($file_result) {
        // error
        show_mibs(true, _("MIB file successfully deleted, but database entry could not be removed."));
    }
    else if ($db_result) {
        show_mibs(true, _("MIB database entry deleted, but file could not be removed."));
    }
    else {
        show_mibs(true, _("MIB could not be deleted - check file/directory permissions."));
    }
}

// Get the desination directory, create it if it doesn't exist
function get_processing_destination($mode) {

    $source_directory = get_mib_dir();
    if ($mode === MIB_UPLOAD_PROCESS_ONLY) {
        $dest_directory = $source_directory . "/processed_mibs";
    }
    else if ($mode === MIB_UPLOAD_NXTI) {
        $dest_directory = get_tmp_dir() . "/mibs";
    }

    if (!is_dir($dest_directory)) {
        mkdir($dest_directory, 0777, true);
    }
    return $dest_directory;
}
