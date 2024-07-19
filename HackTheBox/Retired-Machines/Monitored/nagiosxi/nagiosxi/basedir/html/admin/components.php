<?php
//
// Manage Components
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();
decode_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

// Route request
route_request();

function route_request()
{
    global $request;
    $cmd = grab_request_var('cmd', '');

    if ($cmd == 'install') {
        do_c_install();
    } else if ($cmd == 'installstatus') {
        get_c_install_status();
    } else if (isset($request["download"]))
        do_download();
    else if (isset($request["checkupdates"])) {
        do_checkupdates();
    } else if (isset($request["upload"])) {
        do_upload();
    } else if (isset($request["delete"])) {
        do_delete();
    } else if (isset($request["config"])) {
        if (isset($request["cancelButton"])) {
            show_components();
        } else if (isset($request["update"])) {
            do_configure();
        } else {
            show_configure();
        }
    } else if (isset($request["installedok"])) {
        show_components(false, _("Component installed."));
    } else {
        show_components();
    }

    exit;
}

/**
 * @param bool   $error
 * @param string $msg
 */
function show_components($error = false, $msg = "")
{
    global $components;
    global $components_api_versions;

    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'components_api_versions.xml';
    if (file_exists($xmlcache)) {
        $components_api_versions = simplexml_load_file($xmlcache);
    }

    $update_components = array();
    foreach ($components as $k => $c) {
        if (!empty($c['args'][COMPONENT_TYPE])) { if ($c['args'][COMPONENT_TYPE] == COMPONENT_TYPE_CORE) continue; }
        if (isset($components_api_versions->$k)) {
            if (version_compare($c['args'][COMPONENT_VERSION], $components_api_versions->$k->version, '<')) {
                $update_components[$k] = $c;
            }
        }
    }

    do_page_start(array("page_title" => _('Manage Components')), true);
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
        $('#installing').show().center();

        $.post('<?php echo get_base_url(); ?>admin/components.php', { 'cmd': 'install', 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
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
        $.post('<?php echo get_base_url(); ?>admin/components.php', { 'cmd': 'install', 'name': name, 'url': url, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
            cmd_id = data.cmd_id;
            job = 1;
            int_id = setInterval(watch_job, 1000);
        }, 'json');

    });

});


function watch_job()
{
    if (job) {
        $.post('<?php echo get_base_url(); ?>admin/components.php', { 'cmd': 'installstatus', 'id': cmd_id, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data) {
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

    <h1><?php echo _('Manage Components'); ?></h1>
    <?php display_message($error, false, $msg); ?>

    <?php if (!custom_branding()) { ?>
    <div>
        <?php echo _("Manage the components that are installed on this system.") . " " . _("Need a custom component created to extend Nagios XI's capabilities?  <a href='https://www.nagios.com/contact/' target='_blank'>Contact us</a> for pricing information."); ?>
        <p>
            <?php echo _("You can find additional components for Nagios XI at"); ?>
            <a href='https://exchange.nagios.org/directory/Addons/Components' target='_blank'><?php echo _('Nagios Exchange'); ?></a>.<br/>
        </p>
    </div>
    <?php } else { ?>
    <p><?php echo _("Manage the components that are installed on this system."); ?></p>
    <?php } ?>

    <div class="well" style="margin-top: 10px;">
        <form enctype="multipart/form-data" action="components.php" method="post" style="margin: 0;">
            <?php echo get_nagios_session_protector(); ?>
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_upload_max_filesize(); ?>">

            <div class="fl upload-title"><?php echo _('Upload a Component'); ?></div>
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
                <button type="button" class="btn btn-sm btn-success" id="install" style="margin-right: 5px;" <?php if (count($update_components) == 0) { echo 'disabled'; } ?>><?php echo _("Install Updates"); ?></button>
                <?php if (!custom_branding()) { ?>
                <a href="https://exchange.nagios.org/directory/Addons/Components" class="btn btn-sm btn-default"><?php echo _('More Components'); ?> <i class="fa fa-external-link r"></i></a>
                <?php } ?>
            </div>

            <div class="clear"></div>
        </form>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?php echo _("Component"); ?></th>
                <th class="center" style="width: 54px"><?php echo _("Type"); ?></th>
                <th class="center" style="width: 60px;"><?php echo _("Settings"); ?></th>
                <th class="center" style="width: 70px;"><?php echo _("Actions"); ?></th>
                <th class="center" style="width: 60px;"><?php echo _("Version"); ?></th>
                <th class="center" style="width: 120px;"><?php echo _('Status'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $x = 0;

        $sorted_components = array();
        foreach ($components as $c) {
            $sorted_components[$c[COMPONENT_ARGS][COMPONENT_TITLE]] = $c;
        }
        ksort($sorted_components, SORT_STRING);

        foreach ($sorted_components as $carray) {
            if (!empty($carray[COMPONENT_ARGS][COMPONENT_TYPE])) {
                if ($carray[COMPONENT_ARGS][COMPONENT_TYPE] == COMPONENT_TYPE_CORE) {
                    continue;
                }
            }
            // Component may have just been deleted
            if (!file_exists(dirname(__FILE__) . "/../includes/components/" . $carray[COMPONENT_DIRECTORY])) {
                continue;
            }
            show_component($carray[COMPONENT_DIRECTORY], $carray[COMPONENT_ARGS][COMPONENT_NAME], $carray[COMPONENT_ARGS], $x);
            $x++;
        }
        ?>
        </tbody>
    </table>

    <div id="updates" class="xi-modal hide" style="max-width: 400px;">
        <p><strong><?php echo _('Please verify the changes below.'); ?></strong> <?php echo _('Installing all updates will update the following components'); ?>:</p>
        <ul>
            <?php foreach ($update_components as $c) { ?>
            <li><?php echo $c['args'][COMPONENT_TITLE]; ?></li>
            <?php } ?>
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

    <h2><?php echo _("Core Components"); ?></h2>
    <p><?php echo _('These are components that are required for XI to function normally. These components should not be removed or edited.'); ?></p>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?php echo _("Component"); ?></th>
                <th class="center" style="width: 54px"><?php echo _("Type"); ?></th>
                <th class="center" style="width: 60px;"><?php echo _("Settings"); ?></th>
                <th class="center" style="width: 60px;"><?php echo _("Version"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $x = 0;
        foreach ($sorted_components as $carray) {
            if (empty($carray[COMPONENT_ARGS][COMPONENT_TYPE])) {
                continue;
            }
            // Component may have just been deleted
            if (!file_exists(dirname(__FILE__) . "/../includes/components/" . $carray[COMPONENT_DIRECTORY])) {
                continue;
            }
            show_component($carray[COMPONENT_DIRECTORY], $carray[COMPONENT_ARGS][COMPONENT_NAME], $carray[COMPONENT_ARGS], $x);
            $x++;
        }
        ?>
        </tbody>
    </table>

<?php
    do_page_end(true);
    exit();
}

/**
 * @param $component_dir
 * @param $component_name
 * @param $carray
 * @param $x
 */
function show_component($component_dir, $component_name, $carray, $x)
{
    global $components_api_versions;

    // grab variables
    $type = grab_array_var($carray, COMPONENT_TYPE, "");
    $title = grab_array_var($carray, COMPONENT_TITLE, "");
    $desc = grab_array_var($carray, COMPONENT_DESCRIPTION, "");
    $version = grab_array_var($carray, COMPONENT_VERSION, "");
    $date = grab_array_var($carray, COMPONENT_DATE, "");
    $author = grab_array_var($carray, COMPONENT_AUTHOR, "");
    $homepage = grab_array_var($carray, COMPONENT_HOMEPAGE, "");

    $configfunc = grab_array_var($carray, COMPONENT_CONFIGFUNCTION, "");
    $protected = grab_array_var($carray, COMPONENT_PROTECTED, false);
    $encrypted = grab_array_var($carray, COMPONENT_ENCRYPTED, false);

    echo "<tr>";

    $displaytitle = $component_name;
    if (!empty($title))
        $displaytitle = $title;

    // Gettext displaytitle and desc because they aren't getting gettexted beforehand
    // somehow the init_language is not getting called before the components are loading in

    $enc_display = '';
    if ($encrypted) {
        $enc_display = ' <i class="fa fa-enc fa-lock tt-bind" title="'._('Encrypted component').'"></i>';
    }

    echo "<td>";
    echo '<div style="font-size: 12px;"><b>' . _($displaytitle) . "</b>" . $enc_display . "</div>";

    if (!empty($desc))
        echo '<div style="margin: 3px 0 5px 0;">' . _($desc) . '</div>';

    if (!empty($version))
        echo '<i style="color: #555;" class="fa fa-tag tt-bind" title="'._("Version").'"></i> '.$version.' &nbsp; ';

    if (!empty($date))
        echo '<i style="color: #555;" class="fa fa-calendar tt-bind" title="'._("Release date").'"></i> '.$date.' &nbsp; ';

    if (!empty($author) && !custom_branding())
        echo '<i style="color: #555;" class="fa fa-user tt-bind" title="'._("Author").'"></i> '.$author.' &nbsp; ';

    if (!empty($homepage))
        echo '<i style="color: #555;" class="fa fa-user tt-bind" title="'._("Website").'"'.' <a href="'.$homepage.'" target="_blank" rel="noreferrer">'.$homepage.'<a/>';

    echo "</td>";

    echo "<td class='center'>";
    switch ($type) {
        case "core":
            echo _("Core");
            break;
        default:
            echo _("User");
            break;
    }
    echo "</td>";

    echo "<td class='center'>";
    if (!empty($configfunc)) {
        if (is_array($configfunc)) {
            echo "<a href='" . get_component_url_base($component_name) . "/". $configfunc['location'] ."'><img src='" . theme_image("editsettings.png") . "' alt=" . _('Edit Settings') . "' title='" . _('Edit Settings') . "' class='tt-bind'></a>";
        } else {
            echo "<a href='?config=" . $component_dir . "'><img src='" . theme_image("editsettings.png") . "' alt=" . _('Edit Settings') . "' title='" . _('Edit Settings') . "' class='tt-bind'></a>";
        }
    } else
        echo "-";
    echo "</td>";

    if ($type != 'core') {
        echo "<td class='center'>";
        if (!$protected) {
            echo "<a href='?download=" . $component_dir . "'><img src='" . theme_image("package_go.png") . "' alt='" . _('Download') . "' title='" . _('Download') . "' class='tt-bind'></a> ";
            echo "<a href='?delete=" . $component_dir . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("cross.png") . "' alt='" . _('Delete') . "' title='" . _('Delete') . "' class='tt-bind'></a>";
        } else
            echo "-";
        echo "</td>";
    }

    echo "<td class='center'>";
    if ($version != "")
        echo "$version";
    else
        echo "-";
    echo "</td>";

    if ($type != 'core') {
        if ($version != "" && isset($components_api_versions->$component_dir->version)) {

            if (version_compare($version, $components_api_versions->$component_dir->version, '<')) {
                echo "<td style='background-color:#B2FF5F'>";
                echo $components_api_versions->$component_dir->version . " "._('Available')."<br/>";
                if ($components_api_versions->$component_dir->download != "") {
                    echo "<a class='install' data-url='" . $components_api_versions->$component_dir->download . "' data-name='" . $component_dir . "'>"._('Install')."</a> &middot; <a href='" . $components_api_versions->$component_dir->download . "'>"._('Download')."</a>";
                }
            } else {
                echo "<td class='center'>";
                echo _("Up to date");
            }
        } else
            echo "<td class='center'>";
        echo "</td>";
    }

    echo "</tr>\n";
}


function do_download()
{

    // demo mode
    if (in_demo_mode() == true)
        show_components(true, _("Changes are disabled while in demo mode."));

    $component_dir = grab_request_var("download");
    if (have_value($component_dir) == false)
        show_components();

    // clean the name, should only ever need alpha-numeric, hyphen, underscore, and period
    $component_dir = preg_replace("/[^a-zA-Z0-9\_\-\.]/", '', $component_dir);

    $id = submit_command(COMMAND_PACKAGE_COMPONENT, $component_dir);
    if ($id <= 0)
        show_dashlets(true, _("Error submitting command."));
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

                    // component was packaged, send it to user
                    $dir = get_tmp_dir();
                    $thefile = $dir . "/component-" . $component_dir . ".zip";

                    //chdir($dir);

                    $mime_type = "";
                    header('Content-type: ' . $mime_type);
                    header("Content-length: " . filesize($thefile));
                    header('Content-Disposition: attachment; filename="' . basename($thefile) . '"');
                    readfile($thefile);
                } else
                    show_components(true, _("Component packaging timed out."));
                exit();
            }
            usleep(500000);
        }
    }

    exit();
}


function do_upload()
{

    //print_r($request);
    //exit();

    // demo mode
    if (in_demo_mode() == true)
        show_components(true, _("Changes are disabled while in demo mode."));

    // check session
    check_nagios_session_protector();

    $target_path = get_tmp_dir() . "/";
    $component_file = preg_replace('/[^A-Za-z0-9\.\-]/', '', basename($_FILES['uploadedfile']['name']));
    $target_path .= "component-" . $component_file;

    // log it
    send_to_audit_log(_("User installed component '") . $component_file . "'", AUDITLOGTYPE_CHANGE);

    //echo "TEMP NAME: ".$_FILES['uploadedfile']['tmp_name']."<BR>\n";
    //echo "TARGET: ".$target_path."<BR>\n";

    if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {

        // fix perms
        chmod($target_path, 0770);
        chgrp($target_path, "nagios");

        $id = submit_command(COMMAND_INSTALL_COMPONENT, $component_file);
        if ($id <= 0)
            show_components(true, _("Error submitting command."));
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
                    if ($result_code == 0) {
                        // redirect to show install message (so the list will include the new component)
                        header("Location: ?installedok");
                        exit();
                    } else {
                        //print_r($xml);
                        //echo "<BR>RESULT TEXT=$result_text<BR>";
                        $emsg = "";
                        if ($result_text != "")
                            $emsg .= " " . $result_text . "";
                        show_components(true, _("Component installation failed.") . $emsg);
                        //show_components(true,"ERROR=".$emsg);
                    }
                    exit();
                }
                usleep(500000);
            }
        }
        show_components(false, _("Component scheduled for installation."));
    } else {
        // error
        show_components(true, _("Component upload failed."));
    }

    exit();
}

function do_delete()
{

    // demo mode
    if (in_demo_mode() == true)
        show_components(true, _("Changes are disabled while in demo mode."));

    // check session
    check_nagios_session_protector();

    $dir = grab_request_var("delete", "");

    // clean the filename
    $dir = str_replace("..", "", $dir);
    $dir = str_replace("/", "", $dir);
    $dir = str_replace("\\", "", $dir);

    if ($dir == "")
        show_components();

    // log it
    send_to_audit_log("User deleted component '" . $dir . "'", AUDITLOGTYPE_DELETE);

    $id = submit_command(COMMAND_DELETE_COMPONENT, $dir);
    if ($id <= 0)
        show_components(true, _("Error submitting command."));
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
                show_components(false, _("Component deleted."));
                exit();
            }
            usleep(500000);
        }
    }
    show_components(false, _("Component scheduled for deletion."));
    exit();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_configure($error = false, $msg = "")
{
    global $request;
    global $components;

    $dir = grab_request_var("config", "");

    // clean the filename
    $dir = str_replace("..", "", $dir);
    $dir = str_replace("/", "", $dir);
    $dir = str_replace("\\", "", $dir);

    $component_name = $dir;

    if ($component_name == "")
        show_components();

    $component = $components[$component_name];

    $title = grab_array_var($component[COMPONENT_ARGS], COMPONENT_TITLE, "");

    // Special thing for hiding cancel button for certain components
    $hidedetails = grab_request_var("hidedetails", 0);

    do_page_start(array("page_title" => _("Component Configuration") . " - " . $title), true);

    if (!$hidedetails) {
    ?>
    <h1><?php echo $title; ?></h1>
    <?php
    }

    display_message($error, false, $msg);
    ?>

    <form method="post" action="">
    <input type="hidden" name="config" value="<?php echo encode_form_val($component_name); ?>">
    <input type="hidden" name="update" value="1">
    <?php echo get_nagios_session_protector(); ?>

    <?php
    // get component output
    $configfunc = grab_array_var($component[COMPONENT_ARGS], COMPONENT_CONFIGFUNCTION, "");
    if ($configfunc != "") {
        $inargs = $request;
        $outargs = array();
        $result = 0;
        $output = $configfunc(COMPONENT_CONFIGMODE_GETSETTINGSHTML, $inargs, $outargs, $result);
        echo $output;
    } else
        echo "Component function does not exist.";

    ?>

    <div id="formButtons">
        <input type="submit" class="submitbutton btn btn-sm btn-primary" name="submitButton" value="<?php echo _('Apply Settings'); ?>">
        <?php if (!$hidedetails) { ?>
        <input type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton" value="<?php echo _('Cancel'); ?>">
        <?php } ?>
    </div>

    <form>

<?php
}

// Checks with API for component updates via XML
function do_checkupdates()
{
    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'components_api_versions.xml';
    $url = "https://api.nagios.com/product_versions/nagiosxi/511/components_api_versions.xml";

    $proxy = false;
    if (have_value(get_option('use_proxy'))) {
        $proxy = true;
    }

    $options = array(
        'return_info' => true,
        'method' => 'get',
        'timeout' => 10
    );

    // Fetch the url
    $result = load_url($url, $options, $proxy);
    $getfile = trim($result["body"]);

    $error = false;

    // Make sure we succeeded and the file is an appropriate length
    if ($getfile && strlen($getfile) > 3000) {
        file_put_contents($xmlcache, $getfile);
        $msg = _("Component Versions Updated");
    } else {
        $error = true;
        $msg = _("Could not download component version list from Nagios Server, check Internet Connnectivity");
    }
    show_components($error, $msg);
}


// Configure a single component if it has a config section
function do_configure($error = false, $msg = "")
{
    global $request;
    global $components;

    // demo mode
    if (in_demo_mode() == true) {
        show_configure(true, _("Changes are disabled while in demo mode."));
        exit();
    }

    // check session
    check_nagios_session_protector();

    $dir = grab_request_var("config", "");

    // clean the filename
    $dir = str_replace("..", "", $dir);
    $dir = str_replace("/", "", $dir);
    $dir = str_replace("\\", "", $dir);

    $component_name = $dir;

    if ($component_name == "")
        show_components();

    $component = $components[$component_name];

    // log it
    send_to_audit_log(_("Applied component settings for") . ": " . $component_name, AUDITLOGTYPE_CHANGE);

    // save component settings
    $configfunc = grab_array_var($component[COMPONENT_ARGS], COMPONENT_CONFIGFUNCTION, "");
    if ($configfunc != "") {

        // pass request vars to component
        $inargs = $request;

        // initialize return values
        $outargs = array("test" => "test2");
        $result = 0;

        // tell component to save settings
        $configfunc(COMPONENT_CONFIGMODE_SAVESETTINGS, $inargs, $outargs, $result);

        // handle errors thrown by component
        if ($result != 0)
            show_configure(true, $outargs[COMPONENT_ERROR_MESSAGES]);

        // handle success
        else {
            $msg = _("Component settings updated.");
            if (array_key_exists(COMPONENT_INFO_MESSAGES, $outargs))
                $msg = $outargs[COMPONENT_INFO_MESSAGES];
            show_configure(false, $msg);
        }
    } else
        echo _("Component function does not exist.");

    exit();
}


function component_install_available($wizards, $configwizards_api_versions)
{
    $updates = 0;

    foreach ($wizards as $wiz) {
        $name = $wiz['name'];
        if (version_compare($wiz['version'], $configwizards_api_versions->{$name}->version, '<')) {
            $updates++;
        }
    }

    if ($updates > 0) {
        return true;
    } else {
        return false;
    }
}


function do_c_install()
{
    global $components;
    global $components_api_versions;

    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'components_api_versions.xml';
    if (file_exists($xmlcache)) {
        $components_api_versions = simplexml_load_file($xmlcache);
    }

    $update_components = array();
    foreach ($components as $k => $c) {
        if (!empty($c['args'][COMPONENT_TYPE])) { if ($c['args'][COMPONENT_TYPE] == COMPONENT_TYPE_CORE) continue; }
        if (version_compare($c['args'][COMPONENT_VERSION], $components_api_versions->$k->version, '<')) {
            $update_components[$k] = $c;
        }
    }

    check_nagios_session_protector();
    $arr = array();

    $name = grab_request_var('name', '');
    $url = grab_request_var('url', '');

    if (!empty($name)) {
        $args = array(array('name' => $name, 'url' => $url));
    } else {
        // Get all that need to be updated and pass them to the cmdsubsys
        $args = array();
        foreach ($update_components as $k => $c) {
            $args[] = array('name' => $k, 'url' => strval($components_api_versions->$k->download));
        }
    }

    $args = serialize($args);
    $arr['cmd_id'] = submit_command(COMMAND_UPGRADE_COMPONENT, $args);

    print json_encode($arr);
}


function get_c_install_status()
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