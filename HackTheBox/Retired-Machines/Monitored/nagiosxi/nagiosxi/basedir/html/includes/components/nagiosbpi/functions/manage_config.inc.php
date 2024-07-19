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
// 
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


/**
 * Manages the configuration editor from the browser.
 *
 * @param string $content REFERENCE variable to main content string
 *
 * @return mixed array(int $errors, string $msg)
 */
function manual_edit_config(&$content)
{
    $errors = 0;
    $msg = '';

    if (isset($_POST['newconfig'])) {
        $newconfig = grab_request_var('newconfig');
        list($errors, $msg) = write_config_file($newconfig);
    } else {
        $content .= '
<div style="margin-top: 10px;">
    <h4><b>' . _('Config Management') . '</b> &middot; ' . _('Edit Config') . '</h4>
    <div style="margin: 0 5px;">
        ' . config_editor() . '
    </div>
</div>';
    }

    return array($errors, $msg);
}


/**
 * This function pulls up the contents of the config file and inserts them into a text editor.
 *
 * @return string $output html output string
 */
function config_editor()
{
    $contents = file_get_contents(BPI_CONFIGFILE);
    $output = '
    <div id="configEditor">
        <form id="configedit" action="index.php?tab=config&cmd=editconfig" method="post" style="margin: 0;">
            <div style="margin: 12px 0;"">
                <textarea name="newconfig" id="newconfig" class="form-control" style="width: 100%; min-height: 500px; height: calc(100vh - 202px); font-family: \'courier new\'; font-size: 14px; line-height: 16px;">'.encode_form_val($contents).'</textarea>
            </div>
            <button type="submit" name="saveconfig" class="btn btn-sm btn-primary">'._('Save Config').'</button>
            <a href="index.php?tab=config" class="btn btn-sm btn-default" style="margin-left: 10px;">'._('Cancel').'</a>
            <input type="hidden" name="configeditor" value="true">
        </form>
    </div>';
    return $output;
}


/**
 * Expecting replacement contents for configuration file, writes a new config file.
 *
 * @param   string  $new    The new configuration string for the bpi.conf file
 * @return  mixed           array(int $errorcode, string $message)
 */
function write_config_file($new)
{
    $errors = 0;
    $msg = '';
    $backup = true;

    // Backup the config file first
    $new_hash = sha1($new);
    $current_hash = sha1_file(BPI_CONFIGFILE);
    if ($new_hash == $current_hash) {
        $backup = false;
    }

    if (!$backup) {
        $msg_end = "<i>" . _("No changes detected in config file, no backup was created.") . "</i>";
    } else if ($config_id = bpi_config_create_backup(BPI_BACKUP_AUTO)) {
        $msg_end = _("Backup created successfully.");
    } else {
        $errors++;
        $msg = sprintf(_("Backup failed. Aborting change. Verify that backup file and directory (%s) is writeable."), "");
    }

    if (!$errors) {
        if (file_put_contents(BPI_CONFIGFILE, $new)) {
            bpi_config_update_changes($config_id);
            $msg .= " " . _("Config file successfully written.");
        } else {
            $errors++;
            $msg .= BPI_CONFIGFILE . " " . _("failed to save successfully");
        }
    }

    $msg = $msg . " " . $msg_end;
    return array($errors, $msg);
}


/**
 * Creates a backup file of the BPI config
 *
 * @param   string  $btype   Determines what type of backup is created
 * @return  bool    
 */
function bpi_config_create_backup($btype = BPI_BACKUP_MANUAL, $cfg_str = '')
{
    $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
    $backup_file = uniqid().'.conf';

    // Try to make directory if it doesn't exist
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir);
    }

    // Create backup
    if (empty($cfg_str)) {
        $x = copy(BPI_CONFIGFILE, $backup_dir.$backup_file);
    } else {
        $x = file_put_contents($backup_dir.$backup_file, $cfg_str);
    }
    if ($x === false) {
        return false;
    }
    $backup_hash = sha1_file($backup_dir.$backup_file);

    // Check if we should remove a backup before we add the new one
    $backups = bpi_config_get_backups();
    $backup_count = get_option('bpi_config_backup_count', 15);
    if ($backup_count != 0) {
        while (count($backups) >= $backup_count) {
            $config = array_pop($backups);
            bpi_config_delete_backup($config['config_id']);
        }
    }

    // Set the name
    $name = "Manual Backup";
    if ($btype == BPI_BACKUP_AUTO) {
        $name = "Automatic Backup";
    } else if ($btype == BPI_BACKUP_SYNC) {
        $name = "Automatic Sync Backup";
    }

    $user_id = 0;
    if (isset($_SESSION['user_id'])) {
        $user_id = intval($_SESSION["user_id"]);
    }

    // Update database with new backup
    $sql = "INSERT INTO xi_cmp_nagiosbpi_backups (config_creator, config_name, config_file, config_hash, config_date)
            VALUES (".$user_id.", '" . $name . "', '".$backup_file."', '" . $backup_hash . "', NOW());";
    $x = exec_sql_query(DB_NAGIOSXI, $sql);

    // Get config id
    $config_id = get_sql_insert_id(DB_NAGIOSXI);

    return $config_id;
}


function bpi_config_display_changes($changes)
{
    $additions = 0;
    $deletions = 0;
    $changes = explode(',', $changes);
    if (count($changes) == 2) {
        $additions = intval($changes[0]);
        $deletions = intval($changes[1]);
    }
    $output = '';

    if (!empty($deletions)) {
        $output .= '<span style="font-weight: bold; color: red;">-' . $deletions . '</span>';
    }

    if (!empty($additions)) {
        $output .= ' <span style="font-weight: bold; color: green;">+' . $additions . '</span>';
    }

    if (empty($output)) {
        $output = '-';
    }

    return $output;
}


function bpi_config_get_git_version()
{
    $git_version = get_option('git_version', '');

    // If empty git version, get the version and save it
    if (empty($git_version)) {
        $git_version = end(explode(' ', exec("git version")));
        set_option('git_version', $git_version);
    }

    return $git_version;
}


function bpi_config_update_changes($config_id)
{
    $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
    $cfg = bpi_config_get_config($config_id);

    if (empty($cfg)) {
        return;
    }

    // Removed options not in certain git versions...
    $git_version = bpi_config_get_git_version();
    $extra = "";
    if (version_compare($git_version, '2.16.0', '>=')) {
        $extra .= "--ignore-cr-at-eol ";
    }
    if (version_compare($git_version, '1.8.4', '>=')) {
        $extra .= "--ignore-blank-lines ";
    }

    // Get the number of changes
    $cmd = "git diff --no-index $extra --ignore-space-at-eol -w -b --numstat " . escapeshellarg($backup_dir.$cfg['config_file']) . " " . escapeshellarg(BPI_CONFIGFILE);
    $changes = '';
    $diff = exec($cmd);
    if (!empty($diff)) {
        $diff = explode("\t", $diff);
        $changes = $diff[0].",".$diff[1];
    }

    // Get the actual diff
    $cmd = "git diff --no-index $extra --ignore-space-at-eol -w -b " . escapeshellarg($backup_dir.$cfg['config_file']) . " " . escapeshellarg(BPI_CONFIGFILE);
    $diff = shell_exec($cmd);
    $diff = substr($diff, strpos($diff, "\n"));

    $sql = "UPDATE xi_cmp_nagiosbpi_backups SET config_changes = '" . escape_sql_param($changes, DB_NAGIOSXI) . "', config_diff = '" . escape_sql_param($diff, DB_NAGIOSXI) . "' WHERE config_id = " . intval($config_id);
    exec_sql_query(DB_NAGIOSXI, $sql);

    return true;
}


function bpi_config_get_backups($archived = 0)
{
    $backups = array();
    $sql = "SELECT * FROM xi_cmp_nagiosbpi_backups WHERE archived = " . intval($archived) . " ORDER BY config_id DESC;";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        $backups = $rs->GetArray();
    }
    return $backups;
}


function bpi_config_rename_backup($config_id, $name)
{
    $sql = "UPDATE xi_cmp_nagiosbpi_backups SET config_name = '" . escape_sql_param($name, DB_NAGIOSXI) . "' WHERE config_id = " . intval($config_id);
    exec_sql_query(DB_NAGIOSXI, $sql);
    return true;
}


function bpi_config_delete_backup($config_id)
{
    $cfg = bpi_config_get_config($config_id);
    if (empty($cfg)) {
        return false;
    }

    $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
    unlink($backup_dir . $cfg['config_file']);

    $sql = "DELETE FROM xi_cmp_nagiosbpi_backups WHERE config_id = " . intval($config_id) . ";";
    exec_sql_query(DB_NAGIOSXI, $sql);
    return true;
}


function bpi_config_restore_backup($config_id)
{
    $cfg = bpi_config_get_config($config_id);
    if (empty($cfg)) {
        return false;
    }

    // Restore the configuration
    $backup_dir = get_option('bpi_backupdir', get_root_dir() . '/etc/components/bpi/');
    $x = copy($backup_dir.$cfg['config_file'], BPI_CONFIGFILE);
    if (!$x) {
        return false;
    }

    return true;
}


function bpi_config_archive_backup($config_id)
{
    $sql = "UPDATE xi_cmp_nagiosbpi_backups SET archived = 1 WHERE config_id = " . intval($config_id) . ";";
    exec_sql_query(DB_NAGIOSXI, $sql);
    return true;
}


function bpi_config_get_config($config_id)
{
    $sql = "SELECT * FROM xi_cmp_nagiosbpi_backups WHERE config_id = " . intval($config_id) . ";";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        $cfgs = $rs->GetArray();
    }
    if (!empty($cfgs)) {
        $cfgs = $cfgs[0];
    }
    return $cfgs;
}
