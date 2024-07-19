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
 *  @param mixed $array string['groupID']
 *                      string['groupDisplay'] groups UI display tab
 *                      array['members']  master array of members 
 *                      array['critical'] array of designated essential members 
 *                      string['groupDesc'] text description of a group
 *                      boolean['groupPrimary'] 0 | 1 is the group a primary display group?
 *                      string['groupInfoUrl'] hyperlink for group
 *                      int['groupWarn'] warning threshold for group
 *                      int['groupCrit'] critical threshold for group
 *                      string['groupType'] type (default | hostgroup | servicegroup | depedency | false ) 
 *                      array['auth_users'] list of auth users 
 *                      array['critical'] array of members that are essential members 
 */
function process_post($array, $add=false, &$vars)
{
    if ((isset($array['groupID']) || isset($array['hiddenID'])) && isset($array['groupTitle'], $array['groupDisplay'])) {

        $groupID = isset($array['groupID']) ? encode_form_val(trim($array['groupID'])) : encode_form_val(trim($array['hiddenID']));
        $title = encode_form_val(trim($array['groupTitle']));
        $display = encode_form_val(trim($array['groupDisplay']));
        $members = grab_array_var($array, 'members', array());

        // Optional config parameters
        $desc = (isset($array['groupDesc']) ? encode_form_val(trim($array['groupDesc'])) : '');

        // Check for primary
        $primary = 0;
        if (isset($array['groupPrimary']) && $array['groupPrimary'] == 1) {
            $primary = 1;
        }

        $critical = (isset($array['critical']) ? $array['critical'] : array());
        $info = (isset($array['groupInfoUrl']) ? urlencode(trim($array['groupInfoUrl'])) : '');
        $warning = (isset($array['groupWarn']) ? encode_form_val(trim($array['groupWarn'])) : '0');
        $crit = (isset($array['groupCrit']) ? encode_form_val(trim($array['groupCrit'])) : '0');
        $type = (isset($array['groupType']) ? encode_form_val($array['groupType']) : 'default');
        $auth_users = grab_array_var($array, 'auth_users', array());

        $auth_user_string = empty($auth_users) ? '' : implode(',', $auth_users);

        $memberString = '';
        if (!empty($members)) {
            if (!empty($critical)) {
                foreach ($members as $member) {
                    if (empty($member)) { continue; }

                    if (in_array($member, $critical)) {
                        $memberString .= $member . ';|, ';
                    } else {
                        $memberString .= $member . ';&, ';
                    }
                }
            } else {
                foreach ($members as $member) {
                    if (empty($member)) { continue; }
                    $memberString .= $member . ';&, ';
                }
            }
        }

        // Set variables because ..
        $vars = array('gid' => $groupID,
                      'title' => $title,
                      'desc' => $desc,
                      'info' => $info,
                      'type' => $type,
                      'primary' => $primary,
                      'warning_threshold' => $warning,
                      'critical_threshold' => $crit,
                      'type' => $type,
                      'priority' => $display,
                      'auth_users' => $auth_user_string,
                      'members' => $members,
                      'critical' => $critical);

        // Check if we already have a group ID
        if ($add) {
            $config = file_get_contents(BPI_CONFIGFILE);
            if (strpos($config, "define {$groupID}") !== false) {
                print '<br><div class="alert alert-error">'._('Group ID must be unique.').'</div>';
                return false;
            }
        }

        // Validate group ID
        if (isset($array['groupID']) && preg_match('/\s/', trim($array['groupID']))) {
            print '<br><div class="alert alert-error">'._('Group ID cannot contain spaces.').'</div>';
            return false;
        }

        $config = <<<TEST
define {$groupID} {
        title={$title}
        desc={$desc}
        primary={$primary}
        info={$info}
        members={$memberString}
        warning_threshold={$warning}
        critical_threshold={$crit} 
        priority={$display}
        type={$type}
        auth_users={$auth_user_string}
}
TEST;
        
        if ($add) {
            $config .= "\n\n";
        }

        return $config;

    } else {
        print '<p class="error">' . _('Missing data from required fields. Please go back and complete all fields.') . '</p>';
        return false;
    }
}
