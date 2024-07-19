<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: admin_views.inc.php
//  Desc: Defines displays for all non-object configuration pages in the CCM.
//


/**
 * Displays the import config files page
 * Menu link: Import Config Files
 */ 
function ccm_import_page()
{
    if (!ccm_has_access_for('import')) {
        die(_("You do not have access to this page."));
    }

    // Process input variables 
    $search = ccm_grab_request_var('txtSearch', '');
    $submitted = ccm_grab_request_var('importsubmitted', 0);
    $filenames = ccm_grab_request_var('selImportFile', array());
    $overwrite = ccm_grab_request_var('chbOverwrite', 0);
    $subForm = ccm_grab_request_var('subForm', 0);
    $returnClass = '';

    $ccm_stop_info_popup = get_user_meta(0, 'ccm_stop_import_info_popup', 0);

    // Feedback based on if anything was imported, import_configs() defined below
    if (!empty($filenames) && $subForm != false) {
        list($msg, $details) = (($submitted) ? import_configs($filenames, $overwrite, $returnClass) : '');
        flash_message($msg, $returnClass, array('details' => $details));
    }
?>

<script type="text/javascript">
$(document).ready(function() {

    <?php if (!$ccm_stop_info_popup && empty($_SESSION['shown_popup'])) { ?>
    show_info_popup(true);
    <?php
    }
    // Popup was shown, hide it on refresh
    $_SESSION['shown_popup'] = 1;
    ?>

    $('.info-popup.import .btn-close').click(function() {
        clear_whiteout();
        $('.info-popup.import').hide();

        var dont_show = $('.dont-show').is(':checked');
        if (dont_show) {
            var optsarr = {
                "keyname": "ccm_stop_import_info_popup",
                "keyvalue": 1
            };
            var opts = JSON.stringify(optsarr);
            var result = get_ajax_data("setusermeta", opts);
        }
    });

    $(window).resize(function() {
        $('.info-popup.import').center().css('top', '140px');
    });
    
    $('.import-info').click(function() {
        show_info_popup(false);
    });

    $('#importonly').change(function() {
        if ($(this).is(":checked")) {
            $('#selImportFile option').each(function(i) {
                if ($(this).text().indexOf('/usr/local/nagios/etc/import') === -1) {
                    $(this).addClass('hide');
                }
            });
        } else {
            $('#selImportFile option').each(function(i) {
                $(this).removeClass('hide');
            });
        }
    });

});

function show_info_popup(show_stop) {
    whiteout();
    $('.info-popup.import').show().center().css('top', '140px');
    if (show_stop) {
        $('.dont-display').show();
    } else {
        $('.dont-display').hide();
    }
}
</script>

    <div id='contentWrapper' style="width: 100%;">
        <form action='index.php?cmd=admin&type=import' method='post' id='formInput' name='frmInput'>

            <h1><?php echo _("Import Config Files"); ?></h1>
            <p><?php echo _('Manually import config files from the'); ?> <b>/usr/local/nagios/etc</b> <?php echo _('directory into the CCM database'); ?>. <?php echo _('More information on'); ?> <a class="import-info"><?php echo _('how to import configs'); ?></a>.</p>

            <div style="width: 50%; min-width: 400px;">
                <div class="fl">
                    <input type="text" class="form-control" value="<?php echo encode_form_val($search); ?>" name="txtSearch" id='txtSearch' placeholder="<?php echo _('Search'); ?>...">
                    <button type="button" class="btn btn-sm btn-default" style="vertical-align: top;" onclick="document.forms[0].submit()"><i class="fa fa-search"></i></button>
                    <button type="button" class="btn btn-sm btn-default" style="vertical-align: top;" onclick="$('#txtSearch').val('');document.forms[0].submit();"><i class="fa fa-times"></i></button>
                </div>
                <div class="fr">
                    <div class="checkbox" style="margin: 4px 0;">
                        <label>
                            <input type="checkbox" id="importonly">
                            <?php echo _('Show only import directory'); ?>
                        </label>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            
            <select id="selImportFile" multiple="multiple" style="width: 50%; min-width: 400px; height: 230px; margin: 10px 0;" class="form-control" name="selImportFile[]">
                <?php populate_config_files($search); ?>
            </select>

            <div class="ccm-row">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" checked="checked" value="1" id="chbOverwrite" name="chbOverwrite" style="vertical-align: middle;">
                        <?php echo _("Overwrite Database"); ?>
                    </label>
                </div>
            </div>

            <div class="info-popup import hide">
                <h2 style="margin-top: 0; padding-top: 0;"><?php echo _('How to Import Configs'); ?></h2>
                <P><?php echo _("Please review the following documents before you use this tool"); ?>:</P>
                <p>
                    <i class="fa fa-external-link"></i> <a target="_blank" href="https://library.nagios.com/library/products/nagiosxi/documentation/259-using-the-nagios-xi-config-import-prep-tool"><?php echo _("Using The Nagios XI Config Import Prep Tool"); ?></a>
                    <br>
                    <i class="fa fa-external-link"></i> <a target="_blank" href="https://library.nagios.com/library/products/nagiosxi/documentation/258-importing-config-files-into-xi"><?php echo _("Importing Config Files Into XI"); ?></a>
                </p>

                <p><?php echo _("To prevent errors or misconfigurations, you should import your configurations in an useful order. We recommend importing in the following order"); ?>:</p>
                <p>
                    <em><?php echo _('Commands'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Time Periods'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Contact Templates'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Contacts') ?> <i class="fa fa-arrow-right"></i> <?php echo _('Contact Groups'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Host Templates'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Hosts'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Host Groups'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Service Templates'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Services'); ?> <i class="fa fa-arrow-right"></i> <?php echo _('Service Groups'); ?></em>
                </p>
                <p style="margin: 10px 0;"><strong><?php echo _("Check your imported configurations in the CCM before running Apply Configuration!"); ?></strong></p>
                <div>
                    <button type="button" class="btn-close btn btn-sm btn-default"><?php echo _('Close'); ?></button>
                    <span class="checkbox dont-display" style="display: inline-block; margin-left: 15px;">
                        <label>
                            <input type="checkbox" class="dont-show"> <?php echo _("Don't show this message again"); ?>
                        </label>
                    </span>
                </div>
            </div>
    
            <button type="submit" class='btn btn-sm btn-primary' id="subForm" name="subForm" value="1"><i class="fa fa-upload l"></i> <?php echo _('Import'); ?></button>
            <input type="hidden" value="1" id="importsubmitted" name="importsubmitted">
        
        </form>
    </div>
<?php 
}


/**
 * Display the main nagios.cfg in an editable web form.
 */
function ccm_corecfg() 
{
    global $ccm;

    if (get_user_meta(0, 'ccm_access') == 2 && !is_admin()) {
        die(_("You do not have access to this page."));
    }

    $ccm->config->getConfigData("nagiosbasedir", $nagiosEtc);
    
    // Handle request variables
    $submitted = ccm_grab_request_var('submitted', false); 
    $nagioscfg = ccm_grab_array_var($_REQUEST, 'nagioscfg', '');
    $cgicfg = ccm_grab_array_var($_REQUEST, 'cgicfg', '');
    $filecheck = false;
    $feedback = '';
    $contents = '';
    $returnClass = '';
    $nagioscfg_fn = $nagiosEtc.'nagios.cfg';
    $cgicfg_fn = $nagiosEtc.'cgi.cfg';

    // Save the config file
    if ($submitted) {
        $n = file_put_contents($nagioscfg_fn, $nagioscfg);
        $c = file_put_contents($cgicfg_fn, $cgicfg);

        if ($n === false || $c === false) {
            flash_message(_('Could not save configuration file.'), FLASH_MSG_ERROR);
        } else {
            flash_message(_('Files saved successfully.'), FLASH_MSG_SUCCESS);
        }
        $ccm->data->writeLog(_('Saved changes to nagios.cfg and cgi.cfg configuration files'), AUDITLOGTYPE_MODIFY);
    }
    ?>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#tabs').tabs().show();
    });
    </script>

    <div id='contentWrapper'>

        <h1><?php echo _('Core Configs'); ?></h1>
        <p><?php echo _('Manage the static Nagios Core .cfg files located in'); ?> /usr/local/nagios/etc.</p>

        <form action='index.php?cmd=admin&type=corecfg' method='post' id='formInput' name='frmInput'>

            <div id="tabs" class="hide">
                <ul>
                    <li><a href="#nagios"><?php echo _('General'); ?> [nagios.cfg]</a></li>
                    <li><a href="#cgi"><?php echo _('CGI'); ?> [cgi.cfg]</a></li>
                </ul>
                <div id="nagios">
                    <?php
                    if (file_exists($nagioscfg_fn) && is_writeable($nagioscfg_fn)) {
                        $ncfgcontent = file_get_contents($nagioscfg_fn);
                    } else {
                        $ncfgcontent = _("ERROR: Unable to read/write nagios.cfg file. Check permissions!");
                    }
                    ?>
                    <textarea name="nagioscfg" style="min-width: 600px; width: 50%; height: 600px;" class="monospace-textarea form-control"><?php echo $ncfgcontent; ?></textarea>
                </div>
                <div id="cgi">
                    <?php
                    if (file_exists($cgicfg_fn) && is_writeable($cgicfg_fn)) {
                        $ccfgcontent = file_get_contents($cgicfg_fn);
                    } else {
                        $ccfgcontent = _("ERROR: Unable to read/write cfi.cfg file. Check permissions!");
                    }
                    ?>
                    <textarea name="cgicfg" style="min-width: 600px; width: 50%; height: 600px;" class="monospace-textarea form-control"><?php echo $ccfgcontent; ?></textarea>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Save Changes'); ?></button>
                <input type="hidden" value="1" id="submitted" name="submitted">
            </div>

        </form>

    </div>

    <?php
}


/**
 * Creates html for add/edit user form. Is not available for people who have Nagios XI
 * integration installed because they will never see the users page.
 */ 
function manage_user_html()
{
    global $ccm;

    $mode = ccm_grab_request_var('cmd', 'insert');
    $id = ccm_grab_request_var('id', false);

    // Defaults
    $username = '';
    $alias = ''; 
    $password = ccm_random_string(8);
    $confirm = ''; 
    $active = 'checked="checked"';

    // Not used in CCM but exists for old NagiosQL
    $access = '11111111';
    $ws_auth = false;

    // Editing an existing user 
    if ($mode == 'modify' && $id != '') {
        $query = "SELECT * FROM tbl_user WHERE id=".intval($id).";";
        $array = $ccm->db->query($query);

        $username = ccm_grab_array_var($array[0], 'username', '');
        $alias = ccm_grab_array_var($array[0], 'alias', ''); 
        $active = (($array[0]['active'] == 1) ? 'checked="checked"' : '');
    }
?>  
    <div id='tab1'>
        <div class="ccm-row">
            <label for="username"><?php echo _("Username"); ?> <span class="req" title="<?php echo _('Required'); ?>">*</span></label>
            <input type="text" class="required form-control" name="username" id="username" value="<?php echo encode_form_val($username); ?>" style="width: 240px;">
        </div>
        <div class="ccm-row">
            <label for="alias"><?php echo _("Alias"); ?></label>
            <input type='text' class="form-control" name='alias' id='alias' value="<?php echo encode_form_val($alias); ?>" style="width: 240px;">
        </div>
        <div class="ccm-row">
            <label for="password"><?php echo _("Password"); ?> <span class="req" title="<?php echo _('Required'); ?>">*</span></label>
            <input type="password" name="password" class="required form-control" id="password" value="<?php echo encode_form_val($password); ?>" <?php echo sensitive_field_autocomplete(); ?>>
        </div>
        <div class="ccm-row spacer">
            <label for="config"><?php echo _("Confirm Password"); ?> <span class="req" title="<?php echo _('Required'); ?>">*</span></label>
            <input type="password" name="confirm" class="required form-control" id="confirm" value="<?php echo encode_form_val($confirm); ?>" <?php echo sensitive_field_autocomplete(); ?>>
        </div>
        <div class="ccm-row">
            <div class="checkbox">
                <label>
                    <input name="active" type="checkbox" class="checkbox" id="chbActive" value="1" <?php echo encode_form_val($active); ?>> <?php echo _("Active"); ?>
                    <i class="fa fa-info-circle tooltip-info" title="<?php echo _("Only active objects will be written to the config files and appear in Nagios. Inactive objects will only be shown in the CCM."); ?>"></i>
                </label>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo intval($id); ?>">
    </div>
<?php
}

/**
 * Handle add/insert submission for a CCM user
 * @return mixed array(int $errors, string $message) 
 */
function process_user_submission()
{
    global $ccm;

    // Return variables 
    $errors = 0;
    $message ='';

    // Process input variables 
    $mode = ccm_grab_request_var('mode', 'insert');
    $id = ccm_grab_request_var('id', false);
    $cmd = ccm_grab_request_var('cmd', false);

    // Form defaults 
    $username = ccm_grab_request_var('username', '');
    $alias = ccm_grab_request_var('alias', ''); 
    $password = ccm_grab_request_var('password', '');
    $confirm = ccm_grab_request_var('confirm', '');
    $active = ccm_grab_request_var('active', false);
    $lang = ccm_grab_request_var('lang', 'en_US');

    // Not used in CCM but exists for old NagiosQL
    $access = '11111111';
    $ws_auth = false;

    // Statements below modified from original admin/user.php    
    $strSQLx = "`tbl_user` SET `username`='".$ccm->db->escape_string($username)."', `alias`='".$ccm->db->escape_string($alias)."', `access_rights`='".$ccm->db->escape_string($access)."', `password`=MD5('".$ccm->db->escape_string($password)."'), `wsauth`='".$ccm->db->escape_string($ws_auth)."', `active`='".$ccm->db->escape_string($active)."', `locale`='".$ccm->db->escape_string($lang)."', `last_modified`=NOW()";
    if ($mode == "insert" && $cmd != 'delete') {
        $strSQL = "INSERT INTO ".$strSQLx;
    } else if ($cmd == 'delete') {
        if (empty($username)) {
            $ccm->db->getSingleDataset("SELECT username FROM tbl_user WHERE id = ".intval($id), $user);
            $username = $user['username'];
        }
        $strSQL = "DELETE FROM tbl_user WHERE `id` = ".intval($id);
    } else {
        $strSQL = "UPDATE ".$strSQLx." WHERE `id` = ".intval($id);
    }

    if ($mode == "modify" && $username == $_SESSION['username']) {
        ccm_set_language($lang);
    }

    // Error handling 
    $errors = $ccm->data->dataInsert($strSQL, $intInsertId);
    $message = (($errors > 0)? $ccm->data->strDBMessage : _('User updated successfully!'));
    
    // Logging
    if ($mode == "insert" && $errors == 0) {
        if ($cmd == 'delete') {
            $ccm->data->writeLog(_('Deleted user').": ".$username, AUDITLOGTYPE_DELETE);
        } else {
            $ccm->data->writeLog(_('Created new user').": ".$username, AUDITLOGTYPE_ADD);
        }
    } else if ($mode == "modify") {
        $ccm->data->writeLog(_('Modified user').": ".$username, AUDITLOGTYPE_MODIFY);
    }

    $msg_type = FLASH_MSG_INFO;
    if ($error) {
        $msg_type = FLASH_MSG_ERROR;
    }
    flash_message($message, $msg_type);
}


/**
 * Updates global CCM settings based on request vars
 */
function update_ccm_settings() 
{
    global $ccm;

    $errors = 0;
    $msg = '';

    $txtRootPath = ccm_grab_request_var('txtRootPath', '');
    $txtBasePath = ccm_grab_request_var('txtBasePath', '');
    $selProtocol = ccm_grab_request_var('selProtocol', '');
    $txtDBserver = ccm_grab_request_var('txtDBserver');
    $txtDBport = ccm_grab_request_var('txtDBport');
    $txtDBname = ccm_grab_request_var('txtDBname');
    $txtDBuser = ccm_grab_request_var('txtDBuser');
    $txtDBpassword = ccm_grab_request_var('txtDBpassword');
    $txtLogoff = ccm_grab_request_var('txtLogoff');

    // Set default limit
    $default_lines = intval(ccm_grab_request_var('default_lines', 15));
    set_option('ccm_default_lines', $default_lines);
    $_SESSION['limit'] = $default_lines;

    // Set lock and timeout settings
    $page_lock_timeout = ccm_grab_request_var('page_lock_timeout', 300);
    $enable_locking = ccm_grab_request_var('enable_locking', 0);
    set_option('ccm_page_lock_timeout', $page_lock_timeout);
    set_option('ccm_enable_locking', $enable_locking);

    if ($errors == 0) {
        $msg = _('Settings updated successfully.');
        $ccm->data->writeLog(_('Saved admin settings'), AUDITLOGTYPE_CHANGE);
    } else {
        $ccm->data->writeLog(_('Failed to save admin settings'), AUDITLOGTYPE_CHANGE);
    }

    flash_message($msg);
    return;
}


/**
 * Displays global CCM settings page form.
 * Menu link: Config Manager Settings
 */ 
function ccm_settings()
{
    $errors = 0;
    $msg = '';
    $submitted = ccm_grab_request_var('submitted', false);
    $returnClass = 'hidden';

    if ($submitted) {
        update_ccm_settings();
    }

    // If Nagios XI specific settings
    $enable_locking = grab_request_var('enable_locking', get_option('ccm_enable_locking', 1));
    $page_lock_timeout = grab_request_var('page_lock_timeout', get_option('ccm_page_lock_timeout', 300));
    $default_lines = grab_request_var('default_lines', get_option('ccm_default_lines', 15));
?>

<script type="text/javascript">
$(document).ready(function() {
    $('#tabs').tabs().show();
    $('.req').tooltip();
});
</script>

    <div id="mainWrapper">
        <h1><?php echo _("Settings"); ?></h1>
        <div id='returnContent' class='<?php echo $returnClass; ?>'>
            <?php echo $msg; ?>
        </div>
        <form action='index.php' method='post' id='formInput' name='frmInput'>
            <div id="tabs" class="main-form hide">
                <ul>
                    <li><a href="#general"><i class="fa fa-cog"></i> <?php echo _("General"); ?></a></li>
                </ul>
                <div id="general">
                    <div class="ccm-row">
                        <label for='default_lines'><?php echo _("Default Result Limit"); ?></label>
                        <div>
                            <select class="form-control" name="default_lines" id="default_lines">
                                <option value="15"<?php if ($default_lines == 15) { echo ' selected'; } ?>>15</option>
                                <option value="30"<?php if ($default_lines == 30) { echo ' selected'; } ?>>30</option>
                                <option value="50"<?php if ($default_lines == 50) { echo ' selected'; } ?>>50</option>
                                <option value="100"<?php if ($default_lines == 100) { echo ' selected'; } ?>>100</option>
                                <option value="250"<?php if ($default_lines == 250) { echo ' selected'; } ?>>250</option>
                                <option value="500"<?php if ($default_lines == 500) { echo ' selected'; } ?>>500</option>
                                <option value="1000"<?php if ($default_lines == 1000) { echo ' selected'; } ?>>1000</option>
                            </select>
                        </div>
                    </div>

                    <div><label><?php echo _('User-Based Editing Page Locking'); ?></label></div>
                    <div class="checkbox" style="margin-bottom: 10px;">
                        <label>
                            <input type="checkbox" value="1" name="enable_locking" <?php echo is_checked($enable_locking, 1); ?>> <?php echo _('Enable page locking for multiple users editing same page'); ?>
                        </label>
                    </div>
                    <div class="ccm-row">
                        <label for="lock_settings"><?php echo _('Page Lock Timeout'); ?></label>
                        <p style="font-size: 10px; margin: 0; padding: 0 0 5px 0;"><?php echo _('If the session ends unexpectedly (like a browswer close non-CCM page change) the session tracking data will be removed after this amount of time, no longer showing whatever was being edited as being edited anymore. Default: 300'); ?></p>
                        <div class="input-group" style="width: 140px;">
                            <input type="text" class="form-control" name="page_lock_timeout" value="<?php echo intval($page_lock_timeout); ?>">
                            <span class="input-group-addon"><?php echo _('seconds'); ?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div style="margin-top: 15px;">
                <input type="submit" class='btn btn-sm btn-primary' value="<?php echo _("Save Settings"); ?>" id="subForm" name="subForm">
                <input type="hidden" value="true" id="submitted" name="submitted">
                <input type='hidden' name='cmd' value='admin'>
                <input type='hidden' name='type' value='settings'>
            </div>
        </form>
    </div>
<?php 
}


/**
 * Displays the page that allows editing of static files from the static files directory.
 * Menu link: Static Configurations
 */
function ccm_static_editor()
{
    global $ccm;
    global $cfg;

    if (!ccm_has_access_for('staticconfig')) {
        die(_("You do not have access to this page."));
    }

    $returnClass = 'hidden';
    $feedback = '';
    $submitted = ccm_grab_request_var('submitted', false);

    // Get config option for static directory
    $staticDir = '/usr/local/nagios/etc/static';
    if (isset($cfg['component_info']['nagioscore']['static_dir'])) {
        $staticDir = $cfg['component_info']['nagioscore']['static_dir'];
    }

    // Get static files, dump to $output variable
    ccm_dir_to_array($staticDir, '', '', $output, $errMessage);
    
    // Save static file
    if ($submitted && in_demo_mode()) {
        flash_message(_("This feature is disabled in demo mode."));
    } else if ($submitted) {
        $newcfg = ccm_grab_request_var('newcfg', '');

        // Get file
        $file = urldecode(ccm_grab_request_var('staticFile', ''));
        $p = explode("/", str_replace('..', '', $file));
        $file = end($p);
        $file = $staticDir . '/' . $file;
        
        if (is_writeable($file)) {
            $newcfg = str_replace('\r\n', "\n", $newcfg);
            if (file_put_contents($file, $newcfg) === false) {
                flash_message(_("Unable to write to file").": <b>$file</b>. "._("Check file permissions."), FLASH_MSG_ERROR);
                $ccm->data->writeLog(_('Failed to write static configuration to file').' '.$file, AUDITLOGTYPE_MODIFY);
            } else {
                flash_message(_("File")." <strong>$file</strong> "._("saved successfully!"), FLASH_MSG_SUCCESS);
                $ccm->data->writeLog(_('Wrote static configuration to file').' '.$file, AUDITLOGTYPE_MODIFY);
            }
        }
    }
?>
    <div id='mainWrapper'>
        <h1><?php echo _("Static Config Editor"); ?></h1>
        <div id='returnContent' class='<?php echo $returnClass; ?>'>
            <?php echo $feedback; ?>
            <div id='closeReturn'>
                <a href='javascript:void(0)' id='closeReturnLink' title='Close'><?php echo _("Close"); ?></a>
            </div>
        </div>
        <p style="margin: 0 0 10px 0;">
            <?php echo _("This tool is used for editing static configuration files."); ?> <b><em><?php echo _("These definitions are NOT stored in the CCM database"); ?>.</em></b>
            <br><?php echo _("Static files directory"); ?>: <b><?php echo $staticDir; ?></b>
        </p>
        <div id='centerdiv'>
            <form action='index.php' method='post' id='formInput' name='frmInput'>
                <div class="content-row">
                    <div>
                        <select id='staticFiles' class="form-control" name='staticFile'>
                        <?php
                        foreach ($output as $file) {
                            $p = explode("/", str_replace('\\', '', $file));
                            $file = end($p);
                            echo "<option value='".urlencode($file)."'>".basename($file)."</option>";
                        }
                        ?>  
                        </select>
                        <button type='button' id='loadValue' class='btn btn-sm btn-info' style="vertical-align: top;" onclick='getStaticFile()'><?php echo _('Load File'); ?> <i class="fa fa-chevron-circle-right"></i></button>
                    </div>
                </div>
                <div>
                    <textarea name="newcfg" style="width: 600px; height: 600px;" id='newcfg' class="form-control monospace-textarea"></textarea>
                </div>
                <div style="margin-top: 15px;">
                    <button type="submit" class='btn btn-sm btn-primary' id="subForm" name="subForm"><i class="fa fa-download"></i> <?php echo _("Save File"); ?></button>
                    <input type="hidden" value="true" id="submitted" name="submitted">
                    <input type='hidden' name='cmd' value='admin'>
                    <input type='hidden' name='type' value='static'>
                </div>
            </form>
        </div>
    </div>
<?php
}


/**
 * Imports nagios configs based on request variable array of files modified from original import.php script
 *
 * @param mixed $files Array of files to import
 * @param int $chkOverwrite Checkbox option to overwrite existing DB info
 * @param string $returnClass REFERENCE variable for div's CSS class ... "success" or "error"
 * @return string $message Feedback message to tell if imports were all successful
 */
function import_configs($files, $chkOverwrite, &$returnClass)
{
    global $ccm;

    if (!ccm_has_access_for('import')) {
        die(_("You do not have access to this page."));
    }

    $imported_files = 0;
    $details = '';
    $errors = 0;

    // Process selected files for import 
    if (!empty($files)) {       
        foreach ($files AS $elem) {
            $intReturn = $ccm->import->fileImport($elem, $chkOverwrite);
            $extra = "";
            if ($chkOverwrite) {
                $extra .= " ("._("overwrite flag was set").")";
            }
            $ccm->data->writeLog(_('Imported configuration file')." ".$elem.$extra);
            if ($intReturn == 1) {
                $details .= $ccm->import->strDBMessage . $ccm->import->strMessage . "<br>";
                $errors++;
            } else {
                $imported_files++;
            }
        }
    }

    if ($errors == 0) {
        $returnClass = 'success';
        $message = $imported_files. " "._("file(s) imported successfully.");
    } else {
        $returnClass = 'error';
        $message = $errors." "._("items failed to import.");
    }
    return array($message, $details);
}


/**
 * Writes the changes made in the textarea page to the files.
 *
 * @param string $chkSearch
 */
function populate_config_files($chkSearch) 
{
    global $ccm;

    $ccm->config->getConfigData("basedir", $strBaseDir);
    $ccm->config->getConfigData("hostconfig", $strHostDir);
    $ccm->config->getConfigData("serviceconfig", $strServiceDir);
    $ccm->config->getConfigData("backupdir", $strBackupDir);
    $ccm->config->getConfigData("hostbackup", $strHostBackupDir);
    $ccm->config->getConfigData("servicebackup", $strServiceBackupDir);
    $ccm->config->getConfigData("importdir", $strImportDir);
    $ccm->config->getConfigData("nagiosbasedir", $strNagiosBaseDir);

    // Building local file list
    $output = array();
    $temp = ccm_dir_to_array($strBaseDir, "\.cfg", "cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg|ndo.cfg|resource.cfg", $output, $errMessage);
    if ($strNagiosBaseDir != $strBaseDir) {
        $temp = ccm_dir_to_array($strNagiosBaseDir, "\.cfg", "cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg|ndo.cfg|resource.cfg", $output, $errMessage);
    }
    
    $temp = ccm_dir_to_array($strHostDir, "\.cfg", "", $output, $errMessage);
    $temp = ccm_dir_to_array($strServiceDir, "\.cfg", "", $output, $errMessage);
    $temp = ccm_dir_to_array($strHostBackupDir, "\.cfg_", "", $output, $errMessage);
    $temp = ccm_dir_to_array($strServiceBackupDir, "\.cfg_", "", $output, $errMessage);
    
    if (($strImportDir != "") && ($strImportDir != $strBaseDir) && ($strImportDir != $strNagiosBaseDir)) {
        $temp = ccm_dir_to_array($strImportDir, "\.cfg", "", $output, $errMessage);
    }
    
    $output = array_unique($output);
    if (is_array($output) && (count($output) != 0)) {
        foreach ($output AS $elem) {
            if (($chkSearch == "") || (stripos($elem, $chkSearch) !== false))  {
                echo "<option value='$elem'>$elem</option>";       
            }
        }
    }
}


/**
 * Function to add files of a given directory to an array
 *
 * @param string $sPath
 * @param string $include string match to include
 * @param string $exclude expression match to exclude
 * @param string $output  REFERENCE variable to output
 * @param        $errMessage
 *
 * @internal param string $errMEssage REFERENCE variable to error output message
 */
function ccm_dir_to_array($dir, $include, $exclude, &$output, &$errMessage) 
{
    while (substr($dir, -1) == "/" || substr($dir, -1) == "\\") {
        $dir = substr($dir, 0, -1);
    }

    $handle = @opendir($dir);
    if ($handle === false) {
        $errMessage = _('Could not open directory').": ".$dir;
    } else {
        while ($arrDir[] = readdir($handle)) { }
        closedir($handle);
        sort($arrDir);
        foreach($arrDir as $file) {
            if (!preg_match("/^\.{1,2}/", $file) and strlen($file)) {
                if (is_dir($dir."/".$file) && strpos($file, 'static') === false && strpos($file, 'pnp') === false) {
                    ccm_dir_to_array($dir."/".$file, $include, $exclude, $output, $errMessage);
                } else {
                    if (preg_match("/".$include."/", $file) && (($exclude == "") || !preg_match("/".$exclude."/", $file))) {
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            $dir = str_replace("/", "\\", $dir);
                            $output [] = $dir."\\".$file;
                        } else {
                            $output [] = $dir."/".$file;
                        }
                    }
                }
            }
        }
    }
}
