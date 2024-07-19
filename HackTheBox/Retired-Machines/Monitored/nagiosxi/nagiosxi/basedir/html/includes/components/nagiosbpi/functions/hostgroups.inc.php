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
 * Fetches current list of hostgroups and either adds or updates BPI group accordingly.
 *
 * @return mixed array(int $errorcode, string $message)
 */
function build_bpi_hostgroups($delete_missing=true)
{
    global $bpi_objects;
    $errors = 0;
    $msg = '';
    $addString = '';
    $xml = get_xml_hostgroup_member_objects();

    $old_hash = sha1_file(BPI_CONFIGFILE);
    $old_config = file_get_contents(BPI_CONFIGFILE);

    $bpi_hostgroups = array();
    if (!empty($bpi_objects)) {
        foreach ($bpi_objects as $id => $obj) {
            if ($obj->type == 'hostgroup') {
                $bpi_hostgroups[$id] = $obj;
            }
        }
    }

    foreach ($xml->hostgroup as $hostgroup) {
        $key = "$hostgroup->hostgroup_name";
        $id = "hg_".str_replace(' ', '', $key);
        $array = array('groupTitle' => 'HG: ' . $key,
            'groupID' => $id,
            'members' => array(),
            'groupDisplay' => 0,
            'groupPrimary' => 1,
            'groupType' => 'hostgroup',
        );

        foreach ($hostgroup->members->host as $member) {
            $name = strval($member->host_name);
            if (empty($name)) continue;
            $array['members'][] = "{$name};NULL";
        }

        // Add
        if (!isset($bpi_objects[$id])) {

            $vars = array();
            if (!empty($array['members'])) {
                $addString .= process_post($array, true, $vars);
            }

        // Edit
        } else if (!empty($array['members'])) {

            // Remove object from list of host groups that exist
            unset($bpi_hostgroups[$id]);

            // Add existing group properties back into array for data persistance
            $array['critical'] = array();
            $obj = $bpi_objects[$id];

            // Handle existing members
            $members = $obj->get_memberlist();
            foreach ($members as $member) {
                if ($member['option'] == '|') $array['critical'][] = $member['host_name'] . ';NULL';
            }

            // Handle existing thresholds
            $array['groupWarn'] = $obj->warning_threshold;
            $array['groupCrit'] = $obj->critical_threshold;

            // Handle group description
            $array['groupDesc'] = $obj->desc;

            // Handle existing display priority
            $array['groupPrimary'] = $obj->primary;

            // Handle primary display group
            $array['groupDisplay'] = $obj->priority;

            // Handle info URL
            $array['groupInfoUrl'] = $obj->info;

            $vars = array();
            $editString = process_post($array, false, $vars);

            // Commit the changes
            list($error, $errmsg) = edit_group($id, $editString, false);
            if ($error > 0) {
                $msg .= $errmsg;
            }

            $errors += $error;
        }
    }

    // Verify we have all the correct groups, remove the ones that we didn't add/edit
    if (count($bpi_hostgroups) > 0 && $delete_missing) {
        foreach ($bpi_hostgroups as $id => $obj) {

            list($error, $errmsg) = delete_group($id, false);
            if ($error > 0) $msg .= $errmsg;
            $errors += $error;

        }
    }

    list($error, $errmsg) = add_group($addString, false);
    $errors += $error;
    $msg .= $errmsg;

    $current_hash = sha1_file(BPI_CONFIGFILE);

    // Make a backup specifically for sync
    if ($old_hash !== $current_hash) {
       $config_id = bpi_config_create_backup(BPI_BACKUP_SYNC, $old_config);
       bpi_config_update_changes($config_id);
    }

    return array($errors, $msg);
}


/**
 * Wrapper function for bpi hostgroup viewing in the browser.
 *
 * @return string $content html output generated by bpi_view_object_html('hostgroup')
 */
function bpi_view_hostgroups()
{
    $content = '';
    $content .= enterprise_message();

    // Check auth
    if (can_control_bpi_groups($_SESSION['username']) && enterprise_features_enabled()) {
        $content .= '<div class="syncmessage"><a href="index.php?cmd=synchostgroups" class="btn btn-sm btn-primary" title="' . _('Sync Hostgroups') . '"><i class="fa fa-refresh l"></i> ' . _('Sync Hostgroups') . '</a></div>';
    }

    // Explain why features are hidden
    if (!enterprise_features_enabled()) {
        return $content;
    }

    $content .= bpi_view_object_html('hostgroup');
    return $content;
}
