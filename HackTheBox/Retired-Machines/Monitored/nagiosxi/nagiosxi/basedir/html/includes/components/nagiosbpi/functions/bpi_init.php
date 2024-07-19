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


/**
 * Fetches config info from bpi.conf file and fetches status details, initializes BPI groups by type
 * and calculates group states based on group type.
 *
 * @param string  $type             [optional] high, medium, low, hostgroup, servicegroup
 * @param boolean $determine_states : should we calculate group states, or just determine group properties?
 *
 * @return string $msg an output message depending on where this function is being called from
 */
function bpi_init($type = 'default', $determine_states = true)
{
    global $bpi_objects;
    global $bpi_config;
    global $bpi_obj_count;
    global $bpi_state_count;
    global $host_details;
    global $bpi_errors;
    global $bpi_missing;

    $orphans = false;

    // Fetch configuration information
    $bpi_raw_groups = parse_bpi_conf();

    $host_details = grab_status_details();

    // Initialize all BpGroup instances
    foreach ($bpi_raw_groups as $key => $value) {
        if ((!isset($value['groupType'])) || (isset($value['groupType']) && $value['groupType'] == $type))
        {
            $Grp = new BpGroup($value, $key);
            if (CLI == false && !is_authorized_for_bpi_group($Grp, $_SESSION['username'])) { continue; }
            $bpi_objects[$key] = $Grp;
            $bpi_obj_count++;
        }
    }

    if (!empty($bpi_objects)) {

        // Only display objects with display>0 for initial loop, then unpack groups after that
        foreach ($bpi_objects as $object) {
            $object->process_children();
        }

        // Safety check for orpaned groups
        foreach ($bpi_objects as $object) {
            if ($object->primary == false && (count($object->parents) == 0) && CLI == false) {
                $bpi_errors .= _("WARNING! Group") . ": <strong>{$object->title}</strong> " . _("is an orphan group and will not be visible in the interface!") . "
                            " . _("Make this group a primary group or add it as a child to another group for it to be visible.") . "
                            <a href='index.php?cmd=edit&arg={$object->name}'>" . _("Edit Group") . "</a><br />";
                $orphans = true;
            }
        }
    }

    if ($bpi_config == true && $determine_states == true) {
        foreach ($bpi_objects as $object) {
            if ($object->primary) {
                $object->drill_down();
            }
        }
    }

    if ($orphans == true || $bpi_config == false) {
        if (CLI == false && is_admin($_SESSION['user_id'])) {
            $msg = "<div class='message'><ul class='errorMessage'>$bpi_errors</ul></div>
                <div id='repair'><a href='index.php?tab=config&cmd=editconfig'><i class='fa fa-pencil'></i> " . _("Edit BPI Configuration File") . "</a></div>";
            return $msg;
        } else {
            if (CLI) { $user = _('System'); } else { $user = $_SESSION['username']; }
            bpi_log_errors("CONFIG ERRORS: " . $user . time() . $bpi_errors);
        }
    }

    return '';
}
