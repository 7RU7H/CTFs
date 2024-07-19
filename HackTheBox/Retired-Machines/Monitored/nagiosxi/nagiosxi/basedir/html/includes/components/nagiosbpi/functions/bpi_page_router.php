<?php
// Nagios BPI (Business Process Intelligence) 
// Copyright (c) 2010-2020 Nagios Enterprises, LLC.
//
// LICENSE:
//
// This work is made available to you under the terms of Version 2 of
// the GNU General Public License. A copy of that license should have
// been provided with this software, but in any event can be obtained
// from http://www.fsf.org.
// This work is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
// 02110-1301 or visit their web page on the internet at
// http://www.fsf.org.
//
//
// CONTRIBUTION POLICY:
//
// (The following paragraph is not intended to limit the rights granted
// to you to modify and distribute this software under the terms of
// licenses that may apply to the software.)
//
// Contributions to this software are subject to your understanding and acceptance of
// the terms and conditions of the Nagios Contributor Agreement, which can be found 
// online at:
//
// http://www.nagios.com/legal/contributoragreement/
//
//
// DISCLAIMER:
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
// INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
// PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
// HOLDERS BE LIABLE FOR ANY CLAIM FOR DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
// OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
// GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, STRICT LIABILITY, TORT (INCLUDING 
// NEGLIGENCE OR OTHERWISE) OR OTHER ACTION, ARISING FROM, OUT OF OR IN CONNECTION 
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


function bpi_page_router_get_config_id()
{
    $config_id = grab_request_var('config_id', 0);

    // Exit if we don't have a config ID
    if (empty($config_id)) {
        header("Location: index.php?tab=config");
        exit();
    }

    return $config_id;
}


/**
 * Main page routing NOTE: there is a second page routing function on bpi_display.php that routes the ajax based content.
 *
 * @param string $cmd a command option so this function can be specified from the command-line API
 *
 * @global        $bpi_options
 * @return string $content pre-built html output to pass to the browser
 */
function bpi_page_router($cmd = false)
{
    global $bpi_options;

    // Processes $_GET and $_POST data
    if (!$cmd) $cmd = grab_request_var('cmd', false);
    $tab = grab_request_var('tab', 'high');
    $tab = preg_replace('/[^A-Za-z0-9\-]/', '', $tab);
    $msg = grab_request_var('msg', '');
    $valid_tabs = array('low', 'medium', 'high', 'hostgroups', 'servicegroups', 'default', 'all', 'add');

    // Page content string
    $content = '';
    $content .= unserialize($msg);

    if ($cmd) {
        $errors = 0;

        // Auth check
        if (CLI == false && !can_control_bpi_groups()) {
            return "<div class='error'>" . _('You are not authorized to access this feature.') . "</div>";
        }

        switch ($cmd) {

            case 'delete':
                list($errors, $msg) = handle_delete_command($content);
                break;

            case 'edit':
                $init_msg = bpi_init('all', false);
                list($errors, $msg) = handle_edit_command($content);
                break;

            case 'add':
                $init_msg = bpi_init('all', false);
                list($errors, $msg) = handle_add_command($content);
                break;

            case 'editconfig':
                $init_msg = bpi_init('all', false);
                $content .= $init_msg;
                list($errors, $msg) = handle_manual_edit_command($content);
                break;

            case 'synchostgroups':
                if (!enterprise_features_enabled()) {
                    flash_message(_('Hostgroup syncing is only available for Nagios XI Enterprise Edition'), FLASH_MSG_ERROR);
                    $errors++;
                } else {
                    $init_msg = bpi_init('all', false);
                    list($errors, $msg) = build_bpi_hostgroups();
                }
                if (empty($errors)) {
                    flash_message(_("Hostgroup sync was successful. BPI configuration applied."));
                } else {
                    flash_message(_("Hostgroup sync was unsuccessful."), FLASH_MSG_ERROR, array('details' => $msg));
                }
                header("Location: index.php?tab=hostgroups");
                die();
                break;

            case 'syncservicegroups':
                if (!enterprise_features_enabled()) {
                    flash_message(_('Servicegroup syncing is only available for Nagios XI Enterprise Edition'), FLASH_MSG_ERROR);
                    $errors++;
                } else {
                    $init_msg = bpi_init('all', false);
                    list($errors, $msg) = build_bpi_servicegroups();
                }
                if (empty($errors)) {
                    flash_message(_("Servicegroup sync was successful. BPI configuration applied."));
                } else {
                    flash_message(_("Servicegroup sync was unsuccessful."), FLASH_MSG_ERROR, array('details' => $msg));
                }
                header("Location: index.php?tab=servicegroups");
                die();
                break;

            case 'checkgroupstatus':
                if (CLI == false) die('Illegal action!');
                break;

            case 'createbackup':
                $x = bpi_config_create_backup();
                if ($x) {
                    $_SESSION['bpi_flash_msg'] = _('Manual backup was created succsesfully.');
                } else {
                    $backupdir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
                    $_SESSION['bpi_flash_msg'] = sprintf(_('Could not create a manual backup. Check permissions on the backup directory: %s'), $backupdir);
                }
                header("Location: index.php?tab=config");
                exit();
                break;

            case 'archiveconfig':
                $config_id = bpi_page_router_get_config_id();
                $cfg = bpi_config_get_config($config_id);
                bpi_config_archive_backup($config_id);

                $_SESSION['bpi_flash_msg'] = sprintf(_('Config <b>%s</b> has been archived.'), $cfg['config_name']);
                header("Location: index.php?tab=config");
                exit();
                break;

            case 'restoreconfig':
                $config_id = bpi_page_router_get_config_id();

                // Restore the backup
                if (bpi_config_restore_backup($config_id)) {
                    $_SESSION['bpi_flash_msg'] = sprintf(_('Successfully restored BPI configuration: <b>%s</b>'), $cfg['config_name']);
                }
                header("Location: index.php?tab=config");
                exit();
                break;

            case 'deleteconfig':
                $config_id = bpi_page_router_get_config_id();

                // Remove the actual config
                $cfg = bpi_config_get_config($config_id);
                bpi_config_delete_backup($config_id);

                $_SESSION['bpi_flash_msg'] = sprintf(_('Config <b>%s</b> deleted successfully.'), $cfg['config_name']);
                header("Location: index.php?tab=config");
                exit();
                break;

            case 'downloadconfig':
                $config_id = bpi_page_router_get_config_id();
                $cfg = bpi_config_get_config($config_id);
                $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
                $file = $backup_dir.$cfg['config_file'];
                ob_clean();
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($cfg['config_name'].'.txt'));
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit();
                break;

            case 'diff':
                $cid_a = grab_request_var('cid_a', 0);
                $cid_b = grab_request_var('cid_b', 0);
                return bpi_display_diff($cid_a, $cid_b);
                break;

            case 'diffsaved':
                $cid = grab_request_var('cid', 0);
                return bpi_display_diff($cid, 0, true);
                break;

            default:
                return _("Unknown command");

        }

        // Generic error handler
        if ($errors > 0) {
            $content .= "<div class='error'><strong>" . _("Error") . "</strong>: $msg</div>";
        }

        // Display a generic success message for a config change?
        if (isset($_POST['saveconfig'])) {

            $_SESSION['bpi_flash_msg'] = $msg;
            header("Location: index.php?tab=config");
            exit();

        } else if ($errors == 0 &&
            (isset($_REQUEST['addSubmitted']) ||
                isset($_REQUEST['editSubmitted']) ||
                isset($_REQUEST['configeditor']) ||
                $cmd == 'delete' || $cmd == 'synchostgroups' || $cmd == 'syncservicegroups'
            )) {
            if (!empty($msg)) {
                $content .= "<div class='success'>$msg</div>";
            }
        }

        return $content;
    }

    if ($tab == "config") {
        $flash_msg = "";

        // Get flash message
        if (isset($_SESSION['bpi_flash_msg'])) {
            $flash_msg = $_SESSION['bpi_flash_msg'];
            unset($_SESSION['bpi_flash_msg']);
        }

    ?>

    <script type="text/javascript">
    $(document).ready(function() {

        // Do rename modal
        $('#archived-table').on('click', '.rename', function() {
            $('#rename-cid').val($(this).data('cid'));
            $('#rename-name').val('').focus();
            $('#rename-modal').modal('show');
        });

        $('#rename-submit').click(function() {
            submit_rename();
        });

        $('#rename-name').keypress(function(e) {
            if (e.keyCode == 13) {
                submit_rename();
            }
        });

        $('#rename-modal').on('shown.bs.modal', function () {
            $('#rename-name').focus()
        })

        function submit_rename()
        {
            cid = $('#rename-cid').val();
            name = $('#rename-name').val();
            $.post('index.php', { cmd: 'rename', cid: cid, name: name }, function(data) {
                if (data.error !== 0) {
                    alert(data.error);
                } else {
                    $('#rename-modal').modal('hide');
                    $('#config-name-' + cid).html(name);
                }
            }, 'json');
        }

    }); 
    </script>

    <div class="modal fade" id="rename-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo _('Rename Config Backup'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin: 0;">
                        <input type="hidden" value="" id="rename-cid">
                        <label><?php echo _('New Name'); ?>:&nbsp;</label>
                        <input type="text" class="form-control" id="rename-name" style="width: 300px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><?php echo _('Cancel'); ?></button>
                    <button type="button" class="btn btn-sm btn-primary" id="rename-submit"><?php echo _('Save'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 10px;">
        <h4><b><?php echo _('Config Management'); ?></b></h4>
        <div style="margin: 10px 5px;">

            <?php if (!empty($flash_msg)) { ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size: 18px;"><span aria-hidden="true">&times;</span></button>
                <?php echo $flash_msg; ?>
            </div>
            <?php } ?>

            <div style="margin: 20px 0;">
                <a href="index.php?tab=config&cmd=createbackup" class="btn btn-xs btn-default"><i class="fa fa-plus l"></i> <?php echo _("Create Backup"); ?></a>&nbsp;&nbsp;&nbsp;
                <a href="index.php?tab=config&cmd=editconfig" class="btn btn-xs btn-primary"><i class="fa fa-pencil l"></i> <?php echo _('Edit Config'); ?></a>
                <span style="margin-left: 10px;"><b><?php echo _('Warning'); ?></b>: <?php echo _('Advanced users only'); ?> <i tabindex="1" class="fa fa-question-circle pop" data-content="<?php echo _('You can manually edit the configuration. We recommend using the UI unless there are errors in your configuration that require manual changes.'); ?>"></i></span>
            </div>

            <div class="container-fluid" style="padding-left: 5px; padding-right: 5px;">
                <div class="row">
                    <div class="col-sm-12 col-lg-6">

                        <div style="margin-bottom: 10px; font-size: 14px;">
                            <b><?php echo _('Configuration Backups'); ?></b> <i tabindex="2" class="fa fa-question-circle pop" data-content="<?php echo _('Every time a configuration change occurs, a new automatic backup will be created. Each backup will show the amount of line changes made from the original config to the new one. If no changes are made, no backup will be created. To create a backup with no changes, use the Create Backup button.'); ?>"></i>
                        </div>
                        <table class="table table-condensed table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo _('Backup Name'); ?></th>
                                    <th><?php echo _('Diff Changes'); ?></th>
                                    <th><?php echo _('Date Created'); ?></th>
                                    <th><?php echo _('Created By'); ?></th>
                                    <th class="center"><?php echo _('Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $backups = bpi_config_get_backups();
                                if (!empty($backups)) {
                                    foreach ($backups as $b) {
                                ?>
                                <tr>
                                    <td><?php echo $b['config_id']; ?></td>
                                    <td><?php echo $b['config_name']; ?></td>
                                    <td>
                                        <div class="fl"><?php $changes = bpi_config_display_changes($b['config_changes']); echo $changes; ?></div>
                                        <div class="fr"><?php if ($changes != '-') { ?> <a href="index.php?tab=config&cmd=diffsaved&cid=<?php echo $b['config_id']; ?>"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View diff change'); ?>"></a><?php } ?></div>
                                        <div class="clear"></div>
                                    </td>
                                    <td><?php echo get_datetime_string_from_datetime($b['config_date']); ?></td>
                                    <td><?php echo get_user_attr($b['config_creator'], 'username'); ?></td>
                                    <td class="center">
                                        <a href="index.php?tab=config&cmd=diff&cid_b=<?php echo $b['config_id']; ?>"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View changes from current config'); ?>"></a>&nbsp; 
                                        <a href="index.php?tab=config&cmd=restoreconfig&config_id=<?php echo $b['config_id']; ?>"><img src="<?php echo theme_image('arrow_undo.png'); ?>" class="tt-bind" title="<?php echo _('Restore'); ?>"></a>&nbsp; 
                                        <a href="index.php?tab=config&cmd=downloadconfig&config_id=<?php echo $b['config_id']; ?>"><img src="<?php echo theme_image('download.png'); ?>" class="tt-bind" title="<?php echo _('Download'); ?>"></a>&nbsp;
                                        <a href="index.php?tab=config&cmd=archiveconfig&config_id=<?php echo $b['config_id']; ?>"><img src="<?php echo theme_image('folder_go.png'); ?>" class="tt-bind" title="<?php echo _('Archive'); ?>"></a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr>
                                    <td colspan="6"><?php echo _('No backups yet. Make a change to the BPI config to create one.'); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                    <div class="col-sm-12 col-lg-6">

                        <div style="margin-bottom: 10px; font-size: 14px;">
                            <b><?php echo _('Archived Configuration Backups'); ?></b> <i tabindex="2" class="fa fa-question-circle pop" data-content="<?php echo _('Configurations that have been backed up can be archived to be saved indefinitely. You can restore archived configurations if necessary.'); ?>"></i>
                        </div>
                        <table class="table table-condensed table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo _('ID'); ?></th>
                                    <th><?php echo _('Backup Name'); ?></th>
                                    <th><?php echo _('Date Created'); ?></th>
                                    <th><?php echo _('Created By'); ?></th>
                                    <th class="center"><?php echo _('Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="archived-table">
                                <?php
                                $archived = bpi_config_get_backups(1);
                                if (!empty($archived)) {
                                    foreach ($archived as $a) {
                                ?>
                                <tr>
                                    <td><?php echo $a['config_id']; ?></td>
                                    <td id="config-name-<?php echo $a['config_id']; ?>"><?php echo $a['config_name']; ?></td>
                                    <td><?php echo get_datetime_string_from_datetime($a['config_date']); ?></td>
                                    <td><?php echo get_user_attr($a['config_creator'], 'username'); ?></td>
                                    <td class="center">
                                        <a class="rename" data-cid="<?php echo $a['config_id']; ?>"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Rename'); ?>"></a>&nbsp; 
                                        <a href="index.php?tab=config&cmd=diff&cid_b=<?php echo $a['config_id']; ?>"><img src="<?php echo theme_image('page_go.png'); ?>" class="tt-bind" title="<?php echo _('View changes from current config'); ?>"></a>&nbsp; 
                                        <a href="index.php?tab=config&cmd=restoreconfig&config_id=<?php echo $a['config_id']; ?>"><img src="<?php echo theme_image('arrow_undo.png'); ?>" class="tt-bind" title="<?php echo _('Restore'); ?>"></a>&nbsp;
                                        <a href="index.php?tab=config&cmd=downloadconfig&config_id=<?php echo $a['config_id']; ?>"><img src="<?php echo theme_image('download.png'); ?>" class="tt-bind" title="<?php echo _('Download'); ?>"></a>&nbsp;
                                        <a href="index.php?tab=config&cmd=deleteconfig&config_id=<?php echo $a['config_id']; ?>"><img src="<?php echo theme_image('delete.png'); ?>" class="tt-bind" title="<?php echo _('Delete'); ?>"></a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr>
                                    <td colspan="6"><?php echo _('No backups have been archived yet.'); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
    } else if ($tab == "settings") {
    ?>

    <iframe style="border: none; width: 100%; margin: 0 -15px; height: 1200px;" src="../../../admin/components.php?config=nagiosbpi&hidedetails=1"></iframe>

    <?php
    } else {

        if (in_array($tab, $valid_tabs)) {
            $_SESSION['tab'] = $tab;
            $content .= '<div id="notes" class="note"><i class="fa fa-fw fa-dot-circle-o bpi-tt-bind" title="'._('Essential member').'"></i> - ' . _("Essential group members");
            if ($bpi_options['IGNORE_PROBLEMS'] == true)
                $content .= '<div><i class="fa fa-fw fa-check"></i> - ' . _("Handled problems (acknowledged or in downtime)") . '</div>';

            $content .= "</div>
            <script type='text/javascript'>
                $(document).ready(function() {
                    bpi_load(); 
                });
            </script>
            <div id='bpiContent'><i class='fa fa-spinner fa-spin' style='font-size: 16px;'></i></div>";
        } else {
            echo _("Invalid tab");
        }

    }

    return $content;
}


function bpi_has_diff_vals($diff)
{
    $lines = explode("\n", $diff);
    if (count($lines) <= 5) {
        foreach ($lines as $l) {
            if (!empty($l) && strpos($l, 'old mode') === false && strpos($l, 'new mode') === false && strpos($l, 'index') === false) {
                return true;
            }
        }
    } else {
        return true;
    }
    return false;
}


/**
 * Display a diff between two config, the main BPI config, or a saved bpi config change
 *
 * @param int $cid_a
 * @param int $cid_b
 * @param boolean $saved
 * @return
 */
function bpi_display_diff($cid_a, $cid_b=0, $saved=false)
{
    $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');

    // Get display
    $saved_view = get_user_meta(0, 'bpi_default_diff_display', 1);
    $view = grab_request_var('view', $saved_view);
    $display = 'side-by-side';
    if ($view == 2) {
        $display = 'line-by-line';
    }

    // Set user view type if it isn't already set
    if ($view !=  $saved_view) {
        set_user_meta(0, 'bpi_default_diff_display', $view);
    }

    if (!$saved) {
        $backups = bpi_config_get_backups();
        $backups_archived = bpi_config_get_backups(1);

        // Get the two configurations
        $a = BPI_CONFIGFILE;
        $b = BPI_CONFIGFILE;
        if (!empty($cid_a)) {
            $c = bpi_config_get_config($cid_a);
            $a = $backup_dir.$c['config_file'];
        }
        if (!empty($cid_b)) {
            $c = bpi_config_get_config($cid_b);
            $b = $backup_dir.$c['config_file'];
        }

        // Removed options not in certain git versions...
        $git_version = bpi_config_get_git_version();
        $extra = "";
        if (version_compare($git_version, '2.16.0', '>=')) {
            $extra .= "--ignore-cr-at-eol";
        }
        if (version_compare($git_version, '1.8.4', '>=')) {
            $extra .= "--ignore-blank-lines";
        }

        // Get actual diff
        $cmd = "git diff --no-index $extras --ignore-space-at-eol -w -b " . escapeshellarg($a) . " " . escapeshellarg($b);
        $diff = shell_exec($cmd);
        $diff = substr($diff, strpos($diff, "\n"));
    } else {
        $cfg = bpi_config_get_config($cid_a);
        $diff = $cfg['config_diff'];
    }
?>

<link rel="stylesheet" type="text/css" href="../../css/diff2html.min.css">
<script type="text/javascript" src="../../js/diff2html.min.js"></script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", () => {
    var diffHtml = Diff2Html.getPrettyHtml(
        <?php echo json_encode($diff); ?>,
        { inputFormat: 'diff', matching: 'lines', outputFormat: '<?php echo $display; ?>', renderNothingWhenEmpty: false }
    );
    document.getElementById("changes").innerHTML = diffHtml;
});
</script>

<div style="margin-top: 10px;">

    <h4><b><?php echo _('Config Management - File Changes'); ?></b></h4>
    <?php if ($saved) { ?>
    <p><?php echo _('Showing the differences between the old configuration and the new changes in') . " <b>" . $cfg['config_name'] . "</b>"; ?>.</p>
    <?php } else {?>

    <div class="container-fluid" style="padding: 10px 5px;">
        <div class="row">
            <div class="col-sm-10">

                <form class="form-inline" action="index.php" method="get" style="margin-bottom: 15px;">

                    <input type="hidden" name="cmd" value="diff">
                    <input type="hidden" name="tab" value="config">

                    <div class="input-group">
                        <label class="input-group-addon"><?php echo _('Changes from'); ?></label>
                        <select name="cid_a" class="form-control">
                            <option value="0" <?php echo is_selected($cid_a, 0); ?>><?php echo _('Current Config'); ?></option>
                            <?php if (!empty($backups)) { ?>
                                <optgroup label="<?php echo _('Backups'); ?>">
                                <?php foreach ($backups as $b) { ?>
                                    <option value="<?php echo $b['config_id']; ?>" <?php echo is_selected($cid_a, $b['config_id']); ?>><?php echo $b['config_name'] . " [ID: " . $b['config_id'] . "]"; ?></option>
                                <?php } ?>
                                </optgroup>
                            <?php } ?>
                            <?php if (!empty($backups_archived)) { ?>
                                <optgroup label="<?php echo _('Archived Backups'); ?>">
                                <?php foreach ($backups_archived as $b) { ?>
                                    <option value="<?php echo $b['config_id']; ?>" <?php echo is_selected($cid_a, $b['config_id']); ?>><?php echo $b['config_name']; ?></option>
                                <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                        <label class="input-group-addon" style="border-left: 0; border-right: 0;"><?php echo _('to'); ?></label>
                        <select name="cid_b" class="form-control">
                            <option value="0" <?php echo is_selected($cid_b, 0); ?>><?php echo _('Current Config'); ?></option>
                            <?php if (!empty($backups)) { ?>
                                <optgroup label="<?php echo _('Backups'); ?>">
                                <?php foreach ($backups as $b) { ?>
                                    <option value="<?php echo $b['config_id']; ?>" <?php echo is_selected($cid_b, $b['config_id']); ?>><?php echo $b['config_name'] . " [ID: " . $b['config_id'] . "]"; ?></option>
                                <?php } ?>
                                </optgroup>
                            <?php } ?>
                            <?php if (!empty($backups_archived)) { ?>
                                <optgroup label="<?php echo _('Archived Backups'); ?>">
                                <?php foreach ($backups_archived as $b) { ?>
                                    <option value="<?php echo $b['config_id']; ?>" <?php echo is_selected($cid_b, $b['config_id']); ?>><?php echo $b['config_name']; ?></option>
                                <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-sm btn-default"><?php echo _('View Changes'); ?></button>

                </form>

            </div>
            <div class="col-sm-2 form-inline" style="text-align: right;">

                <div class="input-group">
                    <a href="index.php?tab=config&cmd=diff&cid_a=<?php echo intval($cid_a); ?>&cid_b=<?php echo intval($cid_b); ?>&view=2" class="btn btn-sm btn-default <?php if ($view == 2) { echo 'active'; } ?>"><?php echo _('Inline'); ?></a>
                    <a href="index.php?tab=config&cmd=diff&cid_a=<?php echo intval($cid_a); ?>&cid_b=<?php echo intval($cid_b); ?>&view=1" class="btn btn-sm btn-default <?php if ($view == 1) { echo 'active'; } ?>"><?php echo _('Side-by-Side'); ?></a>
                </div>

            </div>
        </div>
    </div>

    <?php } ?>

    <?php if (bpi_has_diff_vals($diff)) { ?>

    <div id="changes"></div>

    <?php } else { ?>

    <p style="padding: 20px; background: #F9F9F9; border: 1px solid #DDD;">
        <?php echo _("There are no differences between the two files selected."); ?>
    </p>

    <?php } ?>

</div>

<?php
}