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

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;

    if (isset($request["download"]))
        do_download();
    else if (isset($request["upload"]))
        do_upload();
    else if (isset($request["delete"]))
        do_delete();
    else
        show_plugins();

    exit;
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_plugins($error = false, $msg = "")
{
    $plugins = get_nagioscore_plugins();

    do_page_start(array("page_title" => _('Manage Plugins')), true);

    $users = array();
    $u = explode("\n", file_get_contents('/etc/passwd'));
    foreach ($u as $l) {
        if (!empty($l)) {
            $x = explode(":", $l);
            $users[$x[2]] = $x[0];
        }
    }

    $groups = array();
    $g = explode("\n", file_get_contents('/etc/group'));
    foreach ($g as $l) {
        if (!empty($l)) {
            $x = explode(":", $l);
            $groups[$x[2]] = $x[0];
        }
    }
?>

    <h1><?php echo _('Manage Plugins'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p>
        <?php echo _('Manage the monitoring plugins and scripts that are installed on this system. Use caution when deleting plugins or scripts, as they may cause your monitoring system to generate errors.'); if (!custom_branding()) { ?><br><?php echo _(' Find thousands of community-developed plugins to extend Nagios XI\'s capabilities at the'); ?> <a href="https://exchange.nagios.org/directory/Plugins" target="_blank" rel="noreferrer"><?php echo _('Nagios Exchange'); ?><i class="fa fa-external-link fa-ml"></i></a>.<?php } ?>
    </p>

    <div class="well">
        <form enctype="multipart/form-data" action="" method="post" style="margin: 0;">
            <input type="hidden" name="upload" value="1">
            <?php echo get_nagios_session_protector(); ?>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_upload_max_filesize(); ?>">

            <div class="fl" style="height: 29px; line-height: 29px; margin-right: 10px; font-weight: bold; color: #666;">
                <label><?php echo _('Upload a Plugin'); ?></label>
            </div>
            <div class="fl">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-btn">
                        <span class="btn btn-sm btn-default btn-file">
                            <?php echo _('Browse'); ?>&hellip; <input type="file" name="uploadedfile">
                        </span>
                    </span>
                    <input type="text" class="form-control" style="width: 200px;" readonly>
                </div>
            </div>
            <div class="checkbox fl" style="margin: 5px 10px 0 10px;">
                <label>
                    <input type="checkbox" value="1" name="convert_to_unix"><?php echo _('Convert line endings'); ?>
                </label>
                <i title="<?php echo _('Convert line endings'); ?>" data-width="250" data-content="<?php echo _("Convert plugin's line endings to UNIX line endings. This process will not break already UNIX-formatted files."); ?>" class="fa fa-question-circle pop"></i>
            </div>
            <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Upload Plugin'); ?></button>
            <div class="fr">
                <?php if (!custom_branding()) { ?>
                <a class="btn btn-sm btn-default" href="https://exchange.nagios.org/directory/Plugins" target="_blank" rel="noreferrer"><?php echo _("More Plugins"); ?> <i class="fa fa-external-link r"></i></a>
                <?php } ?>
            </div>
            <div class="clear"></div>
        </form>
    </div>

    <table class="table table-condensed table-bordered table-striped table-auto-width">
        <thead>
            <tr>
                <th><?php echo _('File'); ?></th>
                <th><?php echo _('Owner'); ?></th>
                <th><?php echo _('Group'); ?></th>
                <th><?php echo _('Permissions'); ?></th>
                <th><?php echo _('Date'); ?></th>
                <th class="center"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>

        <?php
        foreach ($plugins as $plugin) {

            if (array_key_exists($plugin['group'], $groups)) {
                $group = $groups[$plugin["group"]];
                if ($group != 'nagios' && $group != 'nagcmd') {
                    $group = '<em>' . $group . '</em>';
                }
            } else {
                $group = $plugin['group'];
            }

            if (array_key_exists($plugin['owner'], $users)) {
                $user = $users[$plugin["owner"]];
                if ($user != 'nagios' && $user != 'nagcmd') {
                    $user = '<em>' . $user . '</em>';
                }
            } else {
                $user = $plugin['owner'];
            }

            echo "<tr>";
            echo "<td>" . $plugin["file"] . "</td>";
            echo "<td>" . $user . "</td>";
            echo "<td>" . $group . "</td>";
            echo "<td>" . $plugin["permstring"] . "</td>";
            echo "<td>" . $plugin["date"] . "</td>";
            echo "<td class='center'>";
            echo "<a href='?download=" . $plugin["file"] . "'><img src='" . theme_image("download.png") . "' class='tt-bind' alt='" . _('Download') . "' title='" . _('Download') . "'></a> ";
            echo "<a href='?delete=" . $plugin["file"] . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("cross.png") . "' class='tt-bind' alt='" . _('Delete') . "' title='" . _('Delete') . "'></a>";
            echo "</td>";
            echo "</tr>\n";
        }
        ?>

        </tbody>
    </table>

    <?php
    do_page_end(true);
    exit();
}


function do_download()
{
    global $cfg;

    $file = grab_request_var("download", "");


    // clean the filename
    $file = str_replace("..", "", $file);
    $file = str_replace("/", "", $file);
    $file = str_replace("\\", "", $file);

    $dir = $cfg['component_info']['nagioscore']['plugin_dir'];
    $thefile = $dir . "/" . $file;

    $mime_type = "";
    header('Content-type: ' . $mime_type);
    header("Content-length: " . filesize($thefile));
    header('Content-Disposition: attachment; filename="' . basename($thefile) . '"');
    readfile($thefile);
    exit();
}


function do_upload()
{
    global $cfg;

    if (in_demo_mode()) {
        show_plugins(true, _("Changes are disabled while in demo mode."));
    }

    check_nagios_session_protector();

    $target_path = $cfg['component_info']['nagioscore']['plugin_dir'];
    $target_path .= "/";
    $target_path .= basename($_FILES['uploadedfile']['name']);

    $plugin_file = $_FILES['uploadedfile']['name'];

    send_to_audit_log(_("Installed a plugin") . ': ' . $plugin_file, AUDITLOGTYPE_CHANGE);

    if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {

        // If convert line endings has been selected, let's convert them
        $convert_file = grab_request_var('convert_to_unix', 0);
        if ($convert_file) {
            $cmd = 'mv '.escapeshellarg($target_path).' '.escapeshellarg($target_path.'.old').'; tr -d \'\15\32\' < '.escapeshellarg($target_path.'.old').' > '.escapeshellarg($target_path).'; rm -rf '.escapeshellarg($target_path.'.old').';';
            $out = shell_exec($cmd);
        }

        // make the plugin executable
        chmod($target_path, 0755);

        // success!
        show_plugins(false, _("New plugin was installed successfully."));
    } else {
        // error
        show_plugins(true, _("Plugin could not be installed - directory permissions may be incorrect."));
    }

    exit();
}


function do_delete()
{
    global $cfg;

    if (in_demo_mode()) {
        show_plugins(true, _("Changes are disabled while in demo mode."));
    }

    check_nagios_session_protector();

    $file = grab_request_var("delete", "");

    // clean the filename
    $file = str_replace("..", "", $file);
    $file = str_replace("/", "", $file);
    $file = str_replace("\\", "", $file);

    send_to_audit_log(_("Deleted a plugin") . ': ' . $file, AUDITLOGTYPE_DELETE);

    $dir = $cfg['component_info']['nagioscore']['plugin_dir'];
    $thefile = $dir . "/" . $file;

    if (unlink($thefile) === TRUE) {
        // success!
        show_plugins(false, _("Plugin deleted."));
    } else {
        // error
        show_plugins(true, _("Plugin delete failed - directory permissions may be incorrect."));
    }
}
