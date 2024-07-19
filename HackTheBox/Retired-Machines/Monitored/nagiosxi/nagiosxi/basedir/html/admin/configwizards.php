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

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

route_request();

function route_request()
{
    global $request;
    $cmd = grab_request_var('cmd', '');

    if ($cmd == 'install') {
        do_cw_install();
    } else if ($cmd == 'installstatus') {
        get_cw_install_status();
    } else if (isset($request["download"])) {
        do_download();
    } else if (isset($request["upload"])) {
        do_upload();
    } else if (isset($request["delete"])) {
        do_delete();
    } else if (isset($request["checkupdates"])) {
        do_checkupdates();
    } else {
        show_wizards();
    }

    exit;
}


function show_wizards($error = false, $msg = "")
{
    global $configwizards;
    global $configwizards_api_versions;
    $wizards = array();

    // Report all errors (so we know if there is an issue with a config wizard)
    error_reporting(E_ALL);

    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'configwizards_api_versions.xml';
    if (file_exists($xmlcache)) {
        $configwizards_api_versions = simplexml_load_file($xmlcache);
    }

    $wizardhtml = '';
    $x = 0;
    $p = dirname(__FILE__) . "/../includes/configwizards/";
    $subdirs = scandir($p);
    $updates_available = false;
    foreach ($subdirs as $sd) {

        if (strpos($sd, '.') === 0) {
            continue;
        }

        $d = $p . $sd;

        if (is_dir($d)) {

            $cf = $d . "/$sd.inc.php";
            if (file_exists($cf)) {

                include_once($cf);

                $wizard_dir = basename($d);

                foreach ($configwizards as $wizard) {

                    if (isset($configwizards_api_versions->$wizard_dir)) {
                        if (version_compare($wizard[CONFIGWIZARD_VERSION], $configwizards_api_versions->$wizard_dir->version, '<')) {
                            $wizard['update_available'] = 1;
                            $updates_available = true;
                        }
                    } else {
                        $wizard['update_available'] = 0;
                    }
                    $wizard['directory'] = $wizard_dir;

                    $wizards[] = $wizard;

                }

                $configwizards = array();
                reset($configwizards);

                $x++;
            }
        }
    }

    // Alphabetize the wizard data before creating HTML.
    $wizardnames = array();
    foreach ($wizards as $wizard) {
        $wizardnames[] = $wizard['display_title'];
    }
    array_multisort($wizardnames, SORT_ASC, $wizards);

    foreach ($wizards as $index => $wizard) {
        $wizardhtml .= show_wizard($wizard['directory'], $wizard, $index);
    }

    do_page_start(array("page_title" => _('Manage Configuration Wizards')), true);

?>

<script type="text/javascript">
var cmd_id = 0;
var job = 0;
var int_id = '';

$(document).ready(function() {

    $('#install').click(function() {
        whiteout();
        $('#updates').show().center();
    });

    $('.btn-install').click(function() {

        $('#updates').hide();
        $('#installing').show().center();;

        $.post('<?php echo get_base_url(); ?>admin/configwizards.php', { 'cmd': 'install', 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
            cmd_id = data.cmd_id;
            job = 1;
            int_id = setInterval(watch_job, 1000);
        }, 'json');
    });

    $('.btn-cancel').click(function() {
        $("#updates").hide();
        clear_whiteout();
    });

    $('#complete-close').click(function() {
        $('#complete').hide();
        show_throbber();
        location.reload();
    });
    
    $('#failed-close').click(function() {
        $('#failed').hide();
        show_throbber();
        location.reload();
    });

    $('.install').click(function() {
        var url = $(this).data('url');
        var name = $(this).data('name');
        whiteout();

        $('#installing').show().center();

        // Send job and watch it
        $.post('<?php echo get_base_url(); ?>admin/configwizards.php', { 'cmd': 'install', 'name': name, 'url': url, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
            cmd_id = data.cmd_id;
            job = 1;
            int_id = setInterval(watch_job, 1000);
        }, 'json');

    });

});


function watch_job()
{
    if (job) {
        $.post('<?php echo get_base_url(); ?>admin/configwizards.php', { 'cmd': 'installstatus', 'id': cmd_id, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
            if (data.status_code != 1) {
                // Finished install, let's close the loading window
                $('#installing').hide();
                if (data.result_code == 0) {
                    $('#complete').show().center();
                } else {
                    $('#failed').show().center();
                }
                job = 0;
                cmd_id = 0;
                clearInterval(int_id);
            }
        }, 'json');
    }
}
</script>

    <h1><?php echo _('Manage Configuration Wizards'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <?php if (!custom_branding()) { ?>
    <p>
        <?php echo _("Manage the configuration wizards that are installed on this system and available to users under the <a href='../config/' target='_parent'>configuration</a> menu.") . " " . _("Need a custom configuration wizard created for your organization?  <a href='https://www.nagios.com/contact/' target='_blank'>Contact us</a> for pricing information."); ?>
        <br><?php echo _("You can find additional configuration wizards for Nagios XI at"); ?>
        <a href='https://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards' target='_blank'><?php echo _('Nagios Exchange'); ?></a>.
    </p>
    <?php } else { ?>
    <?php echo _("Manage the configuration wizards that are installed on this system and available to users under the <a href='../config/' target='_parent'>configuration</a> menu."); ?>
    <?php } ?>

    <div class="well" style="margin-top: 10px;">
        <form enctype="multipart/form-data" action="configwizards.php" method="post" style="margin: 0;">
            <?php echo get_nagios_session_protector(); ?>
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_upload_max_filesize(); ?>">

            <div class="fl upload-title"><?php echo _('Upload a Wizard'); ?></div>
            <div class="fl" style="margin-right: 10px;">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-btn">
                        <span class="btn btn-sm btn-default btn-file">
                            <?php echo _('Browse'); ?>&hellip; <input type="file" name="uploadedfile">
                        </span>
                    </span>
                    <input type="text" class="form-control" style="width: 200px;" readonly>
                </div>
            </div>
            <div class="fl">
                <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Upload &amp; Install'); ?></button>
            </div>

            <div class="fr">
                <a href="?checkupdates=true" class="btn btn-sm btn-primary" style="margin-right: 5px;"><i class="fa fa-check l"></i> <?php echo _("Check for Updates"); ?></a>
                <button type="button" class="btn btn-sm btn-success" id="install" style="margin-right: 5px;" <?php if (!$updates_available) { echo 'disabled'; } ?>><?php echo _("Install Updates"); ?></button>
                <?php if (!custom_branding()) { ?>
                <a href="https://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards" class="btn btn-sm btn-default"><?php echo _('More Wizards'); ?> <i class="fa fa-external-link r"></i></a>
                <?php } ?>
            </div>

            <div class="clear"></div>
        </form>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th style="width: 40px;"></th>
                <th><?php echo _('Wizard Information'); ?></th>
                <th style="width: 120px;"><?php echo _('Type'); ?></th>
                <th style="width: 80px; text-align: center;"><?php echo _('Actions'); ?></th>
                <th style="width: 80px; text-align: center;"><?php echo _('Version'); ?></th>
                <th style="width: 120px;"><?php echo _('Status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $wizardhtml; ?>
        </tbody>
    </table>

    <div id="updates" style="max-width: 400px;" class=" xi-modal hide">
        <p><strong><?php echo _('Please verify the changes below.'); ?></strong> <?php echo _('Installing all updates will update the following wizards'); ?>:</p>
        <ul>
            <?php
            foreach ($wizards as $w) {
                if (array_key_exists('update_available', $w)) {
                    if (!$w['update_available']) { continue; }
            ?>
            <li><?php echo $w[CONFIGWIZARD_DISPLAYTITLE]; ?></li>
            <?php
                }
            }
            ?>
        </ul>
        <div style="padding-top: 20px;">
            <button type="button" class="btn btn-sm btn-success btn-install" style="margin-right: 5px;"><?php echo _('Install'); ?></button>
            <button type="button" class="btn btn-sm btn-default btn-cancel"><?php echo _('Cancel'); ?></button>
        </div>
    </div>

    <div id="installing" class="xi-modal hide">
        <div class="sk-spinner sk-spinner-fading-circle fl" style="width: 30px; height: 30px;">
            <div class="sk-circle1 sk-circle"></div>
            <div class="sk-circle2 sk-circle"></div>
            <div class="sk-circle3 sk-circle"></div>
            <div class="sk-circle4 sk-circle"></div>
            <div class="sk-circle5 sk-circle"></div>
            <div class="sk-circle6 sk-circle"></div>
            <div class="sk-circle7 sk-circle"></div>
            <div class="sk-circle8 sk-circle"></div>
            <div class="sk-circle9 sk-circle"></div>
            <div class="sk-circle10 sk-circle"></div>
            <div class="sk-circle11 sk-circle"></div>
            <div class="sk-circle12 sk-circle"></div>
        </div>
        <p style="margin: 0; padding: 0 0 0 20px;" class="fl">
            <strong><?php echo _('Installing updates ...'); ?></strong><br>
            <?php echo _('This should take less than a few minutes.'); ?>
        </p>
    </div>

    <div id="complete" class="xi-modal hide">
        <p><strong><?php echo _('Installation Complete!'); ?></strong></p>
        <div><button id="complete-close" class="btn btn-sm btn-default"><?php echo _('Close'); ?></button></div>
    </div>
    
    <div id="failed" class="xi-modal hide">
        <p><strong><?php echo _('Installation failed, check internet connectivity or proxy settings.'); ?></strong></p>
        <div><button id="failed-close" class="btn btn-sm btn-default"><?php echo _('Close'); ?></button></div>
    </div>

    <?php
    do_page_end(true);
    exit();
}


function show_wizard($wizard_dir, $cw, $x)
{
    global $configwizards_api_versions;

    $wizard_typeid = $cw[CONFIGWIZARD_TYPE];
    if (array_key_exists(CONFIGWIZARD_VERSION, $cw))
        $version = $cw[CONFIGWIZARD_VERSION];
    else
        $version = "";
    switch ($wizard_typeid) {
        case CONFIGWIZARD_TYPE_MONITORING:
            $wizard_type = _("Configuration");
            break;
        default:
            $wizard_type = "?";
            break;
    }

    $html = "<tr>";

   
    $img = $cw[CONFIGWIZARD_PREVIEWIMAGE];
    if ($img != "") {
        $html .= "<td style='text-align: center;'><img src='" . wizard_logo($img) . "'></td>";
    }
    $html .= "<td>";
    $html .= '<div style="font-size: 12px;"><b>' . $cw[CONFIGWIZARD_DISPLAYTITLE] . '</b></div>';
    $html .= '<div style="padding: 3px 0 5px 0;">' . $cw[CONFIGWIZARD_DESCRIPTION] . '</div>';
    $about = "";
    if (array_key_exists(CONFIGWIZARD_VERSION, $cw))
        $about .= '<i style="color: #555;" class="fa fa-tag tt-bind" title="'._("Version").'"></i> ' . $cw[CONFIGWIZARD_VERSION] . ' &nbsp; ';
    if (array_key_exists(CONFIGWIZARD_DATE, $cw)) {
        $about .= '<i style="color: #555;" class="fa fa-calendar tt-bind" title="'._("Release date").'"></i> ' . $cw[CONFIGWIZARD_DATE] . ' &nbsp; ';
    }
    if (array_key_exists(CONFIGWIZARD_AUTHOR, $cw) && !custom_branding()) {
        $about .= '<i style="color: #555;" class="fa fa-user tt-bind" title="'._("Author").'"></i> ' . $cw[CONFIGWIZARD_AUTHOR] . ' &nbsp; ';
    }
    if (array_key_exists(CONFIGWIZARD_COPYRIGHT, $cw) && !custom_branding()) {
        $about .= $cw[CONFIGWIZARD_COPYRIGHT];
    }
    if (!empty($about)) {
        $html .= $about;
    }
    $html .= "</div>";
    $html .= "</td>";

    $html .= "<td>" . $wizard_type . "</td>";

    $html .= "<td style='text-align: center;'>";
    $html .= "<a href='?download=" . $wizard_dir . "'><img src='" . theme_image("package_go.png") . "' alt='" . _('Download Archived') . "' title='" . _('Download Archived') . "' class='tt-bind'></a> ";
    $html .= "<a href='?delete=" . $wizard_dir . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("cross.png") . "' alt='" . _('Delete') . "' title='" . _('Delete') . "' class='tt-bind'></a>";
    $html .= "</td>";

    $html .= "<td style='text-align: center;'>";
    if ($version != "")
        $html .= "$version";
    $html .= "</td>";

    if ($version != "" && isset($configwizards_api_versions->$wizard_dir->version)) {

        if (version_compare($version, $configwizards_api_versions->$wizard_dir->version, '<')) {
            $html .= "<td style='background-color:#B2FF5F'>";
            $html .= $configwizards_api_versions->$wizard_dir->version . " "._('Available')."<br/>";
            if ($configwizards_api_versions->$wizard_dir->download != "") {
                $html .= "<a class='install' data-url='" . $configwizards_api_versions->$wizard_dir->download . "' data-name='" . $wizard_dir . "'>"._('Install')."</a> &middot; <a href='" . $configwizards_api_versions->$wizard_dir->download . "'>"._('Download')."</a>";
            }
        } else {
            $html .= "<td>";
            $html .= _("Up to date");
        }
    } else
        $html .= "<td>";
    $html .= "</td>";
    $html .= "</tr>";

    return $html;
}


function do_download()
{

    // demo mode
    if (in_demo_mode() == true)
        show_wizards(true, _("Changes are disabled while in demo mode."));

    $wizard_dir = grab_request_var("download");
    if (have_value($wizard_dir) == false)
        show_wizards();

    // clean the name
    $wizard_dir = str_replace("..", "", $wizard_dir);
    $wizard_dir = str_replace("/", "", $wizard_dir);
    $wizard_dir = str_replace("\\", "", $wizard_dir);

    $id = submit_command(COMMAND_PACKAGE_CONFIGWIZARD, $wizard_dir);
    if ($id <= 0)
        show_wizards(true, _("Error submitting command."));
    else {
        for ($x = 0; $x < 40; $x++) {
            $status_code = -1;
            $result_code = -1;
            $args = array(
                "command_id" => $id
            );
            $xml = get_command_status_xml($args);
            if ($xml) {
                if ($xml->command[0]) {
                    $status_code = intval($xml->command[0]->status_code);
                    $result_code = intval($xml->command[0]->result_code);
                }
            }
            if ($status_code == 2) {
                if ($result_code == 0) {

                    // Wizard was packaged, send it to user
                    $dir = get_tmp_dir();
                    $thefile = $dir . "/configwizard-" . $wizard_dir . ".zip";

                    $mime_type = "";
                    header('Content-type: ' . $mime_type);
                    header("Content-length: " . filesize($thefile));
                    header('Content-Disposition: attachment; filename="' . basename($thefile) . '"');
                    readfile($thefile);
                } else
                    show_wizards(true, _("Wizard packaging timed out."));
                exit();
            }
            usleep(500000);
        }
    }

    exit();
}


function do_upload()
{

    if (in_demo_mode() == true) {
        show_wizards(true, _("Changes are disabled while in demo mode."));
    }

    check_nagios_session_protector();

    $target_path = get_tmp_dir() . "/";
    $wizard_file = preg_replace('/[^A-Za-z0-9\.\-_]/', '', basename($_FILES['uploadedfile']['name']));
    $target_path .= "configwizard-" . $wizard_file;

    // Log to the audit log
    send_to_audit_log("User installed configuration wizard '" . $wizard_file . "'", AUDITLOGTYPE_CHANGE);

    if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {

        chmod($target_path, 0770);
        chgrp($target_path, "nagios");

        $id = submit_command(COMMAND_INSTALL_CONFIGWIZARD, $wizard_file);
        if ($id <= 0)
            show_wizards(true, _("Error submitting command."));
        else {
            for ($x = 0; $x < 20; $x++) {
                $status_code = -1;
                $result_code = -1;
                $args = array(
                    "command_id" => $id
                );
                $xml = get_command_status_xml($args);
                if ($xml) {
                    if ($xml->command[0]) {
                        $status_code = intval($xml->command[0]->status_code);
                        $result_code = intval($xml->command[0]->result_code);
                        $result_text = strval($xml->command[0]->result);
                    }
                }
                if ($status_code == 2) {
                    if ($result_code == 0)
                        show_wizards(false, _("Wizard installed."));
                    else {
                        $emsg = "";
                        if ($result_text != "")
                            $emsg .= " " . $result_text . "";
                        show_wizards(true, _("Wizard installation failed.") . $emsg);
                    }
                    exit();
                }
                usleep(500000);
            }
        }
        show_wizards(false, _("Wizard scheduled for installation."));
    } else {
        show_wizards(true, _("Wizard upload failed."));
    }

    exit();
}


function do_checkupdates()
{
    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'configwizards_api_versions.xml';
    $url = "https://api.nagios.com/product_versions/nagiosxi/511/configwizards_api_versions.xml";

    //use proxy component?
    $proxy = false;
    if (have_value(get_option('use_proxy')))
        $proxy = true;

    $options = array(
        'return_info' => true,
        'method' => 'get',
        'timeout' => 10
    );

    // Fetch the url
    $result = load_url($url, $options, $proxy);
    $getfile = trim($result["body"]);

    $error = false;
    if ($getfile && strlen($getfile) > 5000) {
        file_put_contents($xmlcache, $getfile);
        $msg = _("Config Wizard Versions Updated");
    } else {
        $error = true;
        $msg = _("Could not download wizard version list from Nagios Server, check Internet Connnectivity");
    }
    show_wizards($error, $msg);
}


function do_delete()
{

    if (in_demo_mode() == true) {
        show_wizards(true, _("Changes are disabled while in demo mode."));
    }

    check_nagios_session_protector();
    $dir = grab_request_var("delete", "");

    // Log to the audit log
    send_to_audit_log("User deleted configuration wizard '" . $dir . "'", AUDITLOGTYPE_DELETE);

    // clean the filename
    $dir = str_replace("..", "", $dir);
    $dir = str_replace("/", "", $dir);
    $dir = str_replace("\\", "", $dir);

    if ($dir == "")
        show_wizards();

    $id = submit_command(COMMAND_DELETE_CONFIGWIZARD, $dir);
    if ($id <= 0)
        show_wizards(true, _("Error submitting command."));
    else {
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
                show_wizards(false, _("Wizard deleted."));
                exit();
            }
            usleep(500000);
        }
    }
    show_wizards(false, _("Wizard scheduled for deletion."));
    exit();
}


function do_cw_install()
{
    check_nagios_session_protector();
    $arr = array();

    $name = grab_request_var('name', '');
    $url = grab_request_var('url', '');

    $args = array();
    if (!empty($name)) {
        $args = array('name' => $name, 'url' => $url);
    }

    $arr['cmd_id'] = submit_command(COMMAND_UPGRADE_CONFIGWIZARD, serialize($args));

    print json_encode($arr);
}


function get_cw_install_status()
{
    check_nagios_session_protector();
    $arr = array();

    $cmd_id = grab_request_var('id', 0);

    if (!empty($cmd_id)) {

        $args = array(
            "command_id" => $cmd_id
        );

        $xml = get_command_status_xml($args);
        if ($xml) {
            if ($xml->command[0]) {
                $status_code = intval($xml->command[0]->status_code);
                $result_code = intval($xml->command[0]->result_code);
            }
        }

        $arr['status_code'] = $status_code;
        $arr['result_code'] = $result_code;
        print json_encode($arr);
    }
}
