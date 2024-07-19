<?php
//
// Core Config Snapshots
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


// Only admins can access this page
if (get_user_meta(0, 'ccm_access') == 0 && !is_authorized_for_all_objects() && !is_admin()) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;

    if (isset($request["download"])) {
        do_download();
    } else if (isset($request["view"])) {
        do_view();
    } else if (isset($request["viewdiff"])) {
        $ts = grab_request_var('viewdiff', '');
        show_ccm_file_changes($ts);
    } else if (isset($request["currentdiff"])) {
        $ts = grab_request_var('currentdiff', '');
        $archive = grab_request_var('archive', 0);
        show_ccm_file_changes($ts, true, $archive);
    } else if (isset($request["delete"])) {
        do_delete();
    } else if (isset($request["doarchive"])) {
        do_archive();
    } else if (isset($request["restore"])) {
        do_restore();
    } else if (isset($request["rename"])) {
        do_rename();
    }

    show_log();
}


function show_log($error = false, $msg = "")
{
    $archived_snapshots = array();
    $snapshots = get_nagioscore_config_snapshots();
    foreach ($snapshots as $i => $s) {
        if ($s['archive']) {
            $archived_snapshots[] = $s;
            unset($snapshots[$i]);
        }
    }

    do_page_start(array("page_title" => _('Configuration Snapshots')), true);
?>

<script type="text/javascript">
function verify() {
    var answer = confirm("<?php echo _('Are you sure you want to restore the CCM database?'); ?>");
    if (answer) {
        $("#childcontentthrobber").css("visibility", "visible");
        return true;
    }
    return false;
}

function verify_delete_archive() {
    var conf = confirm("<?php echo _('Are you sure you want to permanently delete this archived Configuration Snapshot?');?>");
    if (conf) {
        $("#childcontentthrobber").css("visibility", "visible");
        return true;
    }
    return false;
}

$(document).ready(function() {
    // View the config output
    $('.view').click(function () {
        var a = $(this);
        var ts = a.data('timestamp');
        var ar = a.data('archive');
        var res = a.data('result');

        whiteout();
        show_throbber();
        
        $.get('coreconfigsnapshots.php', { view: ts, archive: ar, result: res }, function (data) {
            var text_header = "<?php echo _('View Command Output'); ?>";
            var content = "<div id='popup_header' style='margin-bottom: 10px;'><b>" + text_header + "</b></div><div id='popup_data'></div>";
            content += "<div><textarea style='width: 600px; height: 240px;' class='code'>" + data + "</textarea></div>";

            hide_throbber();
            set_child_popup_content(content);
            display_child_popup();
        });
    });
});
</script>

<h1><?php echo _('Configuration Snapshots'); ?></h1>

<?php display_message($error, false, $msg); ?>

<p><?php echo _('The latest configuration snapshots of the monitoring engine are shown below. Download the most recent snapshots as backups or get vital information for troubleshooting configuration errors.'); ?></p>

<div class="container-fluid" style="padding-left: 5px; padding-right: 5px;">
    <div class="row">
        <div class="col-sm-12 col-lg-6">

            <div style="margin-bottom: 10px; font-size: 14px;">
                <b><?php echo _('Snapshots'); ?></b> <i tabindex="2" class="fa fa-question-circle pop" data-content="<?php echo _('Every time you apply configuration a new snapshot is created of the previous configuration.'); ?>"></i>
            </div>
            <table class="table table-condensed table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo _('Date Created'); ?></th>
                        <th><?php echo _('Config Status'); ?></th>
                        <th><?php echo _('Snapshot Name'); ?></th>
                        <th><?php echo _('Diff Changes'); ?></th>
                        <th class="center"><?php echo _('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($snapshots)) {
                        foreach ($snapshots as $s) {
                            $qstring = "result=ok&archive=0";
                            $result = 'ok';
                            if ($s['error']) {
                                $qstring = "result=error&archive=0";
                                $result = 'error';
                            }
                    ?>
                    <tr <?php if ($s['error']) { echo 'class="alert"'; } ?>>
                        <td><?php echo $s['date']; ?></td>
                        <td>
                            <div class="fl"><?php if ($s['error']) { echo _('ERROR'); } else { echo _('OK'); } ?></div>
                            <div class="fr"><a class="view" data-timestamp="<?php echo $s["timestamp"]; ?>" data-result="<?php echo $result; ?>" data-archive="0"><img src="<?php echo theme_image('page_white.png'); ?>" class="tt-bind" title="<?php echo _('View command output'); ?>"></a></div>
                            <div class="clear"></div>
                        </td>
                        <td><?php echo sprintf(_('Snapshot %s'), $s['timestamp']); ?></td>
                        <td>
                            <div class="fl"><?php echo $s['diffnum']; ?></div>
                            <div class="fr"><?php if ($s['diffnum'] != 'N/A') { ?> <a href="?viewdiff=<?php echo $s["timestamp"]; ?>"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View diff change between this and the prior snapshot'); ?>"></a><?php } ?></div>
                            <div class="clear"></div>
                        </td>
                        <td class="center">
                            <?php if (!$s['error']) { ?>
                            <a href="?currentdiff=<?php echo $s["timestamp"]; ?>"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View changes from current config'); ?>"></a>&nbsp;
                            <a href="?restore=<?php echo $s["timestamp"]."&".$qstring; ?>"><img src="<?php echo theme_image('arrow_undo.png'); ?>" onclick="return verify();" class="tt-bind" title="<?php echo _('Restore'); ?>"></a>&nbsp;
                            <?php } ?>
                            <a href="?download=<?php echo $s["timestamp"]."&".$qstring; ?>"><img src="<?php echo theme_image('download.png'); ?>" class="tt-bind" title="<?php echo _('Download'); ?>"></a>
                            <?php if ($s['error']) { ?>
                            &nbsp;<a href="?delete=<?php echo $s["timestamp"]."&".$qstring; ?>"><img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Delete'); ?>"></a>
                            <?php } else { ?>
                            &nbsp;<a href="?doarchive=<?php echo $s["timestamp"]."&".$qstring; ?>"><img src="<?php echo theme_image('folder_go.png'); ?>" class="tt-bind" title="<?php echo _('Archive'); ?>"></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="6"><?php echo _('No snapshots have been created yet. Apply configuration to create one.'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
        <div class="col-sm-12 col-lg-6">

            <div style="margin-bottom: 10px; font-size: 14px;">
                <b><?php echo _('Archived Snapshots'); ?></b> <i tabindex="2" class="fa fa-question-circle pop" data-content="<?php echo _('Archive a snapshot to save it indefinitely.'); ?>"></i>
            </div>
            <table class="table table-condensed table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo _('Date Created'); ?></th>
                        <th><?php echo _('Config Status'); ?></th>
                        <th><?php echo _('Snapshot Name'); ?></th>
                        <th class="center"><?php echo _('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody id="archived-table">
                    <?php
                    if (!empty($archived_snapshots)) {
                        foreach ($archived_snapshots as $a) {
                            $name = sprintf(_('Snapshot %s'), $a['timestamp']);
                            if (substr_count($a['file'], '.') > 2) {
                                $name = substr($a['file'], 0, strpos($a['file'], '.'));
                            }
                            // Remove the ending of filename
                            $filename = str_replace(".tar.gz", "", $a['file']);

                            $qstring = "result=ok&archive=1";
                            $result = 'ok';
                    ?>
                    <tr>
                        <td><?php echo $a['date']; ?></td>
                        <td>
                            <div class="fl"><?php if ($a['error']) { echo _('ERROR'); } else { echo _('OK'); } ?></div>
                            <div class="fr">
                                <a class="view" data-timestamp="<?php echo $filename; ?>" data-result="<?php echo $result; ?>" data-archive="1"><img src="<?php echo theme_image('page_white.png'); ?>" class="tt-bind" title="<?php echo _('View command output'); ?>"></a>
                            </div>
                            <div class="clear"></div>
                        </td>
                        <td><?php echo $name; ?></td>
                        <td class="center">
                            <a href="?rename=<?php echo $a["timestamp"]."&file=".$filename."&".$qstring; ?>" class="rename"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Rename'); ?>"></a>&nbsp; 
                            <a href="?currentdiff=<?php echo $filename; ?>&archive=1"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View changes from current config'); ?>"></a>&nbsp; 
                            <a href="?restore=<?php echo $filename."&".$qstring; ?>" onclick="return verify();" ><img src="<?php echo theme_image('arrow_undo.png'); ?>" class="tt-bind" title="<?php echo _('Restore'); ?>"></a>&nbsp;
                            <a href="?download=<?php echo $filename."&".$qstring; ?>"><img src="<?php echo theme_image('download.png'); ?>" class="tt-bind" title="<?php echo _('Download'); ?>"></a>&nbsp;
                            <a href="?delete=<?php echo $filename."&".$qstring; ?>" onclick="return verify_delete_archive();"><img src="<?php echo theme_image('delete.png'); ?>" class="tt-bind" title="<?php echo _('Delete'); ?>"></a>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="6"><?php echo _('No snapshots have been archived yet.'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<form method="post" action="" id="rename_form">
    <input type="hidden" name="">
</form>

    <?php

    do_page_end(true);
    exit();
}


function show_ccm_file_changes($ts, $current = false, $archive = false)
{
    global $cfg;
    $display = 'side-by-side';
    $name = '';

    if ($current) {
        $prior = $ts;

        // Get the actual diff
        if (!$archive) {
            $checkpoint_dir = $cfg['nom_checkpoints_dir'].$ts;
        } else {
            $checkpoint_dir = $cfg['nom_checkpoints_dir']."archives/".$ts;
            if (strpos($ts, '.') !== false) {
                $name = substr($ts, 0, strrpos($ts, '.')).' ';
                $prior = substr(strrchr($ts, '.'), 1);
            }
        }

        $cmd = "git diff --no-index --ignore-space-at-eol -G'^[^#]' -w -b " . escapeshellarg($cfg['component_info']['nagioscore']['root_dir']."/etc") . " " . escapeshellarg($checkpoint_dir);
        $diff = shell_exec($cmd);
        $diff = substr($diff, strpos($diff, "\n"));

    } else {

        // Get the actual file with the diff change in it
        $file = $cfg['nom_checkpoints_dir'].$ts.'.diff';
        if (file_exists($file)) {
            $diff = file_get_contents($file);
        } else {
            header('Location: coreconfigsnapshots.php');
            exit();
        }

        // set the values for the prior (left) vs newer (right) ones
        $times = array();
        $first_line = substr($diff, 0, strpos($diff, "\n"));
        preg_match_all('/\/([0-9]*)\//', $first_line, $times);
        $prior = $times[1][0];
        $newer = $times[1][1];

    }

    do_page_start(array("page_title" => _('Configuration Snapshots - Config Changes')), true);
?>

<link rel="stylesheet" type="text/css" href="../includes/css/diff2html.min.css">
<script type="text/javascript" src="../includes/js/diff2html.min.js"></script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", () => {
    var diffHtml = Diff2Html.getPrettyHtml(
        <?php echo json_encode($diff); ?>,
        { inputFormat: 'diff', matching: 'lines', outputFormat: '<?php echo $display; ?>', renderNothingWhenEmpty: false }
    );
    document.getElementById("changes").innerHTML = diffHtml;
});
</script>

<h1><?php echo _('Configuration Snapshots - Config Changes'); ?></h1>

<?php if ($current) { ?>
<p style="margin: 10px 0;"><?php echo sprintf(_('Changes in configuration between the %s (on the left) and the snapshot %s at %s (on the right) are shown below.'), '<b>'._('current running configuration').'</b>', '<b>'.$name.'</b>', '<b>'.get_datetime_string($prior).'</b>'); ?></p>
<?php } else { ?>
<p style="margin: 10px 0;"><?php echo sprintf(_('Changes in configuration between the old snapshot at %s (on the left) and this new snapshot at %s (on the right) are shown below.'), '<b>'.get_datetime_string($prior).'</b>', '<b>'.get_datetime_string($newer).'</b>'); ?></p>
<?php } ?>

<?php if (empty($diff)) { ?>
<p>
    <b><?php echo _('There are no changes between the two configurations.'); ?></b><br><br>
    <?php echo _('There are a few possible reasons for this:'); ?>
    <ul>
        <li><?php echo _('You are viewing the latest snapshot, which is a copy of the current config'); ?></li>
        <li><?php echo _('There is no config directory for this snapshot'); ?></li>
    </ul>
</p>
<?php } ?>

<div id="changes"></div>

<?php
    do_page_end(true);
    exit();
}


function do_download()
{
    global $cfg;

    $result = grab_request_var("result", "ok");
    $ts = grab_request_var("download", "");
    $archive = grab_request_var("archive", 0);

    // Base checkpoints dir
    $dir = $cfg['nom_checkpoints_dir'];
    if ($archive == "1") {
        $dir .= "archives/";
    }
    if ($result == "error") {
        $dir .= "errors/";
    }

    // Clean the timestamp
    $ts = str_replace("..", "", $ts);
    $ts = str_replace("/", "", $ts);
    $ts = str_replace("\\", "", $ts);

    $thefile = $dir . $ts . ".tar.gz";

    header('Content-type: application/x-gzip');
    header("Content-length: " . filesize($thefile));
    header('Content-Disposition: attachment; filename="' . basename($thefile) . '"');
    readfile($thefile);
    exit();
}


function do_view()
{
    global $cfg;

    $result = grab_request_var("result", "ok");
    $ts = grab_request_var("view", "");
    $archive = grab_request_var("archive", 0);

    // base checkpoints dir
    $dir = $cfg['nom_checkpoints_dir'];
    if ($archive == "1")
        $dir .= "archives/";
    if ($result == "error")
        $dir .= "errors/";

    // clean the timestamp
    $ts = str_replace("..", "", $ts);
    $ts = str_replace("/", "", $ts);
    $ts = str_replace("\\", "", $ts);

    $thefile = $dir . $ts . ".txt";

    echo file_get_contents($thefile);
    exit();
}


function do_delete()
{
    if (in_demo_mode() == true) {
        flash_message(_("Changes are disabled while in demo mode."), FLASH_MSG_ERROR);
    }

    $ts = grab_request_var("delete", "");
    $archived = grab_request_var("archive", 0);

    // Add a message to the audit log
    $msg = _("Deleted configuration snapshot");
    if ($archived) {
        $msg = _("Deleted archived configuration snapshot");
    }
    send_to_audit_log($msg . " '" . $ts . "'", AUDITLOGTYPE_DELETE);

    // Clean the filename
    $ts = str_replace("..", "", $ts);
    $ts = str_replace("/", "", $ts);
    $ts = str_replace("\\", "", $ts);

    if ($ts == "") {
        return;
    }

    // Allow archived snapshot deletion
    if ($archived) {
        $id = submit_command(COMMAND_DELETE_ARCHIVE_SNAPSHOT, $ts);
    } else {
        $id = submit_command(COMMAND_DELETE_CONFIGSNAPSHOT, $ts);
    }

    if ($id <= 0) {
        show_log(true, _("Error submitting command."));
        return;
    } else {
        for ($x = 0; $x < 14; $x++) {
            $status_code = -1;
            $args = array(
                "command_id" => $id
            );
            $xml = get_command_status_xml($args);
            if ($xml) {
                if ($xml->command[0]) {
                    $status_code = intval($xml->command[0]->status_code);
                }
            }
            if ($status_code == 2) {
                flash_message(_("Config snapshot deleted."));
                header("Location: coreconfigsnapshots.php");
                exit();
            }
            usleep(500000);
        }
    }
    flash_message(_("Config snapshot deleted."));
    header("Location: coreconfigsnapshots.php");
    exit();
}


function do_archive()
{
    if (in_demo_mode() == true) {
        flash_message(_("Changes are disabled while in demo mode."), FLASH_MSG_ERROR);
    }

    $ts = grab_request_var("doarchive", "");

    send_to_audit_log(_("Archived configuration snapshot") . " '" . $ts . "'", AUDITLOGTYPE_DELETE);

    // Clean the filename
    $ts = str_replace("..", "", $ts);
    $ts = str_replace("/", "", $ts);
    $ts = str_replace("\\", "", $ts);

    if ($ts == "") {
        return;
    }

    $id = submit_command(COMMAND_ARCHIVE_SNAPSHOT, $ts);
    if ($id <= 0) {
        show_log(true, _("Error submitting command."));
    } else {
        for ($x = 0; $x < 14; $x++) {
            $status_code = -1;
            $args = array(
                "command_id" => $id
            );
            $xml = get_command_status_xml($args);
            if ($xml) {
                if ($xml->command[0]) {
                    $status_code = intval($xml->command[0]->status_code);
                }
            }
            if ($status_code == 2) {
                flash_message(_('Snapshot archived'));
                header("Location: coreconfigsnapshots.php");
                exit();
            }
            usleep(500000);
        }
    }
    flash_message(_('Snapshot scheduled for archiving'));
    header("Location: coreconfigsnapshots.php");
    exit();
}


function do_restore()
{
    global $cfg;

    if (in_demo_mode() == true) {
        flash_message(_("Changes are disabled while in demo mode."), FLASH_MSG_ERROR);
        return;
    }

    $ts = grab_request_var("restore", "");
    $archive = grab_request_var("archive", 0);
    $baseurl = get_base_url();

    send_to_audit_log(_("Restored system to configuration snapshot") . " '" . $ts . "'", AUDITLOGTYPE_CHANGE);

    // Clean the filename
    $ts = str_replace("..", "", $ts);
    $ts = str_replace("/", "", $ts);
    $ts = str_replace("\\", "", $ts);

    if ($ts == "") {
        return;
    }

    if ($archive == 1) {
        $dir = $cfg['nom_checkpoints_dir'] . '/../nagiosxi/archives';
    } else {
        $dir = $cfg['nom_checkpoints_dir'] . '/../nagiosxi';
    }

    if (!file_exists($dir . "/" . $ts . "_nagiosql.sql.gz")) {
        flash_message(_("This snapshot doesn't exist"), FLASH_MSG_ERROR);
        return;
    }

    if ($archive == 1) {
        $id = submit_command(COMMAND_RESTORE_NAGIOSQL_SNAPSHOT, $ts . " restore archives", 0, 0, null, true);
    } else {
        $id = submit_command(COMMAND_RESTORE_NAGIOSQL_SNAPSHOT, $ts . " restore");
    }

    if ($id <= 0) {
        flash_message(_("Error submitting command."), FLASH_MSG_ERROR);
        return;
    } else {
        for ($x = 0; $x < 14; $x++) {
            $status_code = -1;
            $args = array(
                "command_id" => $id
            );
            $xml = get_command_status_xml($args);
            if ($xml) {
                if ($xml->command[0]) {
                    $status_code = intval($xml->command[0]->status_code);
                }
            }
            if ($status_code == 2) {
                flash_message("CCM Snapshot Restored.</br><strong><a href='" . get_component_url_base('nagioscorecfg') . "/applyconfig.php?cmd=confirm'>" . _("Apply Configuration") . "</a></strong> &nbsp;<a href='" . $baseurl . "includes/components/ccm/xi-index.php' target='_top'>"._("View Config")."</a>");
                header("Location: coreconfigsnapshots.php");
                exit();
            }
            usleep(500000);
        }
    }
    flash_message(_("Configure snapshot restore has been scheduled."));
    header("Location: coreconfigsnapshots.php");
    exit();
}


/**
 * Rename an archived snapshot
 */
function do_rename()
{

    $ts = grab_request_var("rename", "");
    $file = grab_request_var("file", "");
    $new_name = grab_request_var("new_name", "");
    $cancel = grab_request_var("cancel", 0);

    // Get actual name
    $name = sprintf(_('Snapshot %s'), $ts);
    if (strpos($file, '.') !== false) {
        $name = substr($file, 0, strpos($file, '.'));
    }

    if ($ts == '' || $file == '' || $cancel) {
        return;
    }

    if (!$new_name) {

        // Display the rename form

        do_page_start(array("page_title" => _('Monitoring Configuration Snapshots')), true);

        echo "<h1>" . _('Monitoring Configuration Snapshots') . "</h1>";
        echo '<p>' . _('Rename an archived configuration snapshot. Archived snapshots must have no spaces in their names.') . '</p>';

        ?>
        <table class="table table-striped table-condensed table-auto-width">
            <thead>
                <tr>
                    <th><?php echo _('Snapshot Name'); ?></th>
                    <th><?php echo _('Date Created'); ?></th>
                    <th><?php echo _('Filename'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $ts); ?></td>
                    <td><?php echo $file . ".tar.gz"; ?></td>
                </tr>
            </tbody>
        </table>
        
        <h4><?php echo _('Rename Snapshot To'); ?></h4>
        <form method="post">
            <p style="margin-bottom: 30px;"><input type="text" class="form-control" name="new_name"></p>
            <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Rename'); ?></button>
            <button type="submit" class="btn btn-sm btn-default" name="cancel" value="1"><?php echo _('Cancel'); ?></button>
        </form>

        <?php
        do_page_end(true);
        exit();

    } else {

        //
        // RENAME THE ARCHIVED SNAPSHOT
        //

        // Actually set the name!
        $command_data = array();
        $command_data[0] = str_replace(".tar.gz", "", $file);
        $command_data[1] = $new_name . "." . $ts;
        $command_data = serialize($command_data);

        // Send command to the subsystem
        $id = submit_command(COMMAND_RENAME_ARCHIVE_SNAPSHOT, $command_data);

        if ($id <= 0) {
            flash_message(_("Error submitting command."), FLASH_MSG_ERROR);
            return;
        } else {
            for ($x = 0; $x < 14; $x++) {
                $status_code = -1;
                $args = array(
                    "command_id" => $id
                );
                $xml = get_command_status_xml($args);
                if ($xml) {
                    if ($xml->command[0]) {
                        $status_code = intval($xml->command[0]->status_code);
                    }
                }
                if ($status_code == 2) {
                    flash_message(_("Snapshot has been renamed."));
                    header("Location: coreconfigsnapshots.php");
                    exit();
                }
                usleep(500000);
            }
        }
        flash_message(_("Snapshot scheduled to be renamed."));
        header("Location: coreconfigsnapshots.php");
        exit();
    }
}