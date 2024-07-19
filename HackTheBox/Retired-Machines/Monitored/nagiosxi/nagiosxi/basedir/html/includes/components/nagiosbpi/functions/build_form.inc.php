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
 * Builds the add/edit group form and preloads values depending on $cmd call
 * if preloaded, expecting array from get_config_array() that has relevant details for the form.
 *
 * @param mixed $array () [optional] data array for preloaded form
 *
 * @return string $form html form, either empty or preloaded
 */
function build_form($array = array())
{
    global $bpi_objects;
    global $host_details;

    // Set default variables based on whether or not the form is preloaded
    $primary = ((isset($array['primary']) && $array['primary'] == 1) || !isset($array['primary'])) ? " checked='checked' " : '';
    $priority = grab_array_var($array, 'priority', MEDIUM);
    $warn = grab_array_var($array, 'warning_threshold', 90);
    $crit = grab_array_var($array, 'critical_threshold', 80);
    $members = grab_array_var($array, 'members', array());
    $critical = grab_array_var($array, 'critical', array());
    $id = grab_request_var('arg', '');
    $disabled = ($id == '') ? '' : "disabled='disabled'";
    $hidden = ($id == '') ? 'addSubmitted' : 'editSubmitted';
    $title = grab_array_var($array, 'title', '');
    $desc = grab_array_var($array, 'desc', '');
    $info = grab_array_var($array, 'info', '');
    $type = grab_array_var($array, 'type', 'default');
    $gid = grab_array_var($array, 'gid', $id);
    $auth_users = isset($array['auth_users']) ? explode(',', $array['auth_users']) : array();

    $hiddenInput = ($id == '') ? '' : "<input type='hidden' value=\"".encode_form_val($id)."\" name='hiddenID' />";

    $action = ($id != '') ? "?cmd=edit&arg=".encode_form_val(urlencode($id)) : '?cmd=add';
    $form = '';
    $form .= "

<div id='container'>    
    <form id='outputform' method='post' action='index.php{$action}'>
        
        <div class='floatLeft'> 

        <div class='well'>
            <div class='bpiform-group'>
                <label for='groupIdInput'>
                    " . _("Group ID") . "
                    <i class='fa fa-asterisk fa-req tt-bind' title='"._('Required')."'></i>
                </label>
                <i class='fa fa-question-circle fa-14 pop' title='"._('Group ID')."' data-content='"._('The Group ID is a unique identifier used internally by Nagios BPI and the check plugin. Only alpha-numeric characters are allowed. Spaces are not allowed.')."'></i>
                <div>
                    <input id='groupIdInput' size='40' type='text' {$disabled} name='groupID' value=\"".encode_form_val($gid)."\" class='form-control'>
                    {$hiddenInput}
                    <input type='hidden' name='groupType' id='groupType' value='{$type}'>
                </div>
            </div>

            <div class='bpiform-group'>
                <label for='groupTitleInput'>
                    " . _("Display Name") . "
                    <i class='fa fa-asterisk fa-req tt-bind' title='"._('Required')."'></i>
                </label>
                <i class='fa fa-question-circle fa-14 pop' title='"._('Display Name')."' data-content='"._('The group name that will be displayed to the end-user in the BPI Interface.')."'></i>
                <div>
                    <input id='groupTitleInput' size='40' type='text' name='groupTitle' value=\"".encode_form_val($title)."\" class='form-control'>
                </div>
            </div>

            <div class='bpiform-group'>
                <label for='groupDescInput'>" . _("Group Description") . "</label>
                <div>
                    <input id='groupDescInput' size='40' type='text' name='groupDesc' value=\"".encode_form_val($desc)."\" class='form-control'>
                </div>
            </div>

            <div class='bpiform-group'>
                <label for='groupInfoUrl'>" . _("Info URL") . "</label>
                <div>
                    <input id='groupInfoUrl' size='40' type='text' name='groupInfoUrl' value=\"".encode_form_val(urldecode($info))."\" class='form-control'>
                </div>
            </div>

            <div class='bpiform-group checkbox'>
                <label for='groupPrimaryInput'>
                    <input id='groupPrimaryInput' type='checkbox' name='groupPrimary' value='1' {$primary}>
                    " . _("Primary Group") . "
                </label>
                <i class='fa fa-question-circle fa-14 pop' title='" . _("Primary Group") . "' data-content='" . _('Primary Groups are visible on the top level of the tree. Non-primary groups must be added as a child member to a visible group in order to be displayed in the tree.') . "'></i>
            </div>

            <div class='bpiform-group' style='padding-bottom: 15px;'>
                <label>" . _("Health Thresholds") . "</label>
                <div style='margin: 5px 0;'>
                    <img src='".theme_image('error.png')."' class='tt-bind' title='"._('Warning Threshold')."'>&nbsp;
                    <input type='text' id='groupWarn' name='groupWarn' value=\"".encode_form_val($warn)."\" size='1' class='form-control condensed'>&nbsp;
                    0-100%
                </div>
                <div>
                    <img src='".theme_image('critical_small.png')."' class='tt-bind' title='"._('Critical Threshold')."'>&nbsp;
                    <input type='text' id='groupCrit' name='groupCrit' value=\"".encode_form_val($crit)."\" size='1' class='form-control condensed'>&nbsp;
                    0-100% - " . _("Must be lower than warning threshold") . "
                </div>
            </div>

            <div class='bpiform-group'>
                <label for='groupDisplayInput'>" . _("Priority") . "</label>
                <i class='fa fa-question-circle fa-14 pop' title='" . _("Priority") . "' data-content='" . _('The priority tab the group will be displayed in. Defaults to "None" for hostgroups, servicegroups, and dependencies.') . "'></i>
                <div>
                    <select id='groupDisplayInput' name='groupDisplay' class='form-control'> ";

    switch ($priority)
    {
        case LOW:
            $form .= "<option value='1'>" . _("High") . "</option>
                <option value='2'>" . _("Medium") . "</option>
                <option selected='selected' value='3'>" . _("Low") . "</option>";
            if ($type == 'hostgroup' || $type == 'servicegroup')
                $form .= "<option value='0'>" . _("None") . "</option>";
            break;

        case HIGH:
        default:
            $form .= "<option selected='selected' value='1'>" . _("High") . "</option>
                <option value='2'>" . _("Medium") . "</option>
                <option value='3'>" . _("Low") . "</option>";
            if ($type == 'hostgroup' || $type == 'servicegroup')
                $form .= "<option value='0'>" . _("None") . "</option>";
            break;

        case 0:
            $form .= "<option value='1'>" . _("High") . "</option>
            <option value='2'>" . _("Medium") . "</option>
            <option value='3'>" . _("Low") . "</option>
            <option value='0' selected='selected'>" . _("None") . "</option>";
            break;

        case MEDIUM:
            $form .= "<option value='1'>" . _("High") . "</option>
                <option selected='selected' value='2'>" . _("Medium") . "</option>
                <option value='3'>" . _("Low") . "</option>";
            if ($type == 'hostgroup' || $type == 'servicegroup')
                $form .= "<option value='0'>" . _("None") . "</option>";
            break;

    }

    $form .= "  </select>
            </div>
        </div>

        <div class='bpiform-group' style='padding: 0;'>
            <label for='auth_users'>" . _("Authorized Users") . "</label>
            <i class='fa fa-question-circle fa-14 pop' title='" . _("Authorized Users") . "' data-content='" . _('A list of non-administrative users who can view this group. Non-administrative users will only see hosts and services within the groups that they are authorized for, and the group state will be calculated based on the "visible" group members. Admin-level users can automatically see and modify all groups.') . "'></i>
            <div>
                <select multiple='multiple' style='width: 100%; height: 100px;' name='auth_users[]' id='auth_users' class='form-control'>
                    " . create_user_options($auth_users) . "
                </select>
            </div>
        </div>
        </div>

        <button type='button' class='btn btn-sm btn-primary' onclick='submitForm();'><i class='fa fa-pencil l'></i> " . _('Write Configuration') . "</button>

    </div>

    <div class='floatRight'>

        <div class='bpiform-group'>
            <label for='multiple'>
                " . _("Available Hosts") . " (<strong>H:</strong>), " . _("Services") . " (<strong>S:</strong>), " . _("and BPI Groups") . " (<strong>G:</strong>)
            </label>
            <select id='multiple' multiple='multiple' class='form-control' style='height: 160px; width: 100%;'>
                " . create_option_list() . "
            </select>
        </div>

        <button type='button' id='addMembers' class='btn btn-sm btn-info' onclick='dostuff()'>" . _("Add Member(s)") . " <i class='fa fa-chevron-down r'></i></button>
        <button type='button' onclick='clearMembers()' class='btn btn-sm btn-default fr'><i class='fa fa-times l'></i> " . _("Clear All") . "</button>
        <div class='clear'></div>

        <div class='bpiform-group' style='margin-top: 10px;'>
            <div id='memberWrapper'>
                <label for='selectoutput'>
                    " . _("Group Members") . "
                    <i class='fa fa-asterisk fa-req tt-bind' title='"._('Required')."'></i>
                </label>
                <i class='fa fa-question-circle fa-14 pop' title='" . _("Group Members") . "' data-content='" . _('Group Members can be hosts, services, or other groups. "Essential" members can decide the entire groups state. If an essential members state is in a problem state the parent group is listed as "Critical." If all essential members are in a non-problem state, the groups state is then determined by the threshold settings.') . "'></i>       
                <table id='selectoutput' class='table table-condensed table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th style='text-align: left;'>" . _("Member Name") . "</th>
                            <th style='width: 50px;'>" . _("EM") . " <i class='fa fa-question-circle fa-14 pop' title='" . _('Essential Member') . "' data-content='" . _('Essential members cause the group to become critical if an EM group member is not in an OK state.') ."'></i></th>
                            <th style='width: 60px;'>" . _("Actions") . "</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <input type='hidden' name='{$hidden}' value='true'>
        </div>

    </div>

    <div class='clear'></div>
    </form>";

    if ($id != '') {
        build_preload_js($form, $id);
    } else {
        build_preload_js($form, null, $members, $critical);
    }

    return $form;
}


/**
 * Creates list of hosts, services, and BPI groups.
 *
 * @return string $options an html option list for select form element
 */
function create_option_list()
{
    global $host_details;
    global $bpi_objects;
    $options = '';

    bpi_init('default', false);

    $bpi_objects_array = json_decode(json_encode($bpi_objects), true);

    // Add groups to select list as options
    foreach ($bpi_objects_array as $key => $row) {
        $name[$key]  = $row['name'];
        $title[$key] = $row['title'];
    }

    array_multisort(array_map('strtolower', $name), SORT_ASC, array_map('strtolower', $title), SORT_ASC, $bpi_objects_array);

    foreach ($bpi_objects_array as $object) {
        $options .= "<option value='$" . $object['name'] . "'>G: " . $object['title'] . " (Group) </option>\n";
    }

    foreach ($host_details as $host) {
        $options .= "<option value='{$host['host_name']};NULL'>H: {$host['host_name']}</option>\n";
        foreach ($host as $item) {
            if (!is_array($item)) continue;
            $titlevar = $item['host_name'] . ' :: ' . $item['service_description'];
            $valuevar = $item['host_name'] . ';' . $item['service_description'];
            $options .= "<option value='$valuevar'>S: $titlevar</option>\n";
        }
    }

    return $options;
}


/**
 * Preloads form by adding javascript to load preselected group members.
 *
 * @param string $form REFERENCE variable to main $form string
 * @param string $id   group ID / global array index
 * @param array $members an array of members if we set the $id to null
 * @param array $critical an array of ids to define if a member is critical
 */
function build_preload_js(&$form, $id, $members=array(), $critical=array())
{
    global $bpi_objects;

    if (!is_null($id)) {
        $obj = $bpi_objects[$id];

        // Add members upon page load through the javascript function
        $host_name = array();
        $service_description = array();
        $members = $obj->get_memberlist();
        foreach ($members as $key => $row) {
            $host_name[$key]  = $row['host_name'];
            $service_description[$key]= (array_key_exists('service_description', $row)) ? $row['service_description'] : '';
        }

        array_multisort(array_map('strtolower', $host_name), SORT_ASC, SORT_STRING, array_map('strtolower', $service_description), SORT_ASC, SORT_STRING, $members );

        // Fixed bug where group members weren't repopulating in the form correctly with a group
        $members = array_merge($obj->get_group_children(), $members);

    }

    $form .= "\n<script type='text/javascript'>\n";
    
    // Loop through members and print javascript for group or service
    foreach ($members as $member) {

        // If the member was based on an ID (if we are editing)
        if (is_array($member)) {
            if ($member['type'] == 'group') {
                $value = '$' . $member['index'];
            } else if ($member['type'] == 'service') {
                $value = $member['host_name'] . ';' . $member['service_description'];
            } else if ($member['type'] == 'host') {
                $value = $member['host_name'] . ';NULL';
            }
            $opt = $member['option'];
            $value = str_replace('\\', '\\\\', $value); // Fix issue with JS sending value without the \
            $form .= "\npreload('$value', '$opt');\n";

        // If the member is based on members input from request (if we are adding new one)
        } else {
            $c = '';
            if (in_array($member, $critical)) {
                $c = '|';
            }
            $form .= "\npreload('$member', '$c');\n";
        }
    }

    $form .= "\n</script>\n";
}


/**
 * Fetches list of non-admin users and returs an html option list.
 *
 * @param mixed $selected array of selected users for preloaded form
 *                        return string $options html option list of non-admin users
 */
function create_user_options($selected = array())
{
    $reg_users = get_regular_users();
    $options = '';
    foreach ($reg_users as $username => $fullname) {
        $options .= "<option value='{$username}'";
        if (in_array($username, $selected)) $options .= " selected='selected'";
        $options .= ">{$fullname}</option>\n";
    }
    return $options;
}
