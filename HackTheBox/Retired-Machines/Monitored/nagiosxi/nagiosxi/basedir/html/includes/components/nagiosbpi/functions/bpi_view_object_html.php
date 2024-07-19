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
 * Main display function for all bpi group trees, filters group's processed by the $arg param.
 *
 * @param string $arg group type to be filtered (high, medium, low, hostgroup, servicegroup
 *
 * @return string $content processed html content based on $arg
 */
function bpi_view_object_html($arg = null)
{
    global $bpi_objects;
    global $bpi_unique;
    global $bpi_missing;
    $tds = @unserialize(grab_request_var('tds'));
    $divs = @unserialize(grab_request_var('divs'));
    $sorts = @unserialize(grab_request_var('sorts'));
    $resultCount = 0;
    $content = '';

    // Create javascript for reload
    $content .= "<script type='text/javascript'>
        $(document).ready(function() { ";

    // Toggled groups
    if (is_array($divs)) {
        for ($i = 0; $i < count($divs); $i++) {
            $content .= "reShowHide('{$divs[$i]}','{$tds[$i]}');\n";
        }
    }

    // Sorted groups
    if (is_array($sorts)) {
        foreach ($sorts as $s) {
            $content .= "sortchildren('{$s}',true);";
        }
    }

    $content .= '$("body").on("click", "#show-details", function() {
                    if ($(".details").is(":visible")) {
                        $(".details").hide();
                        $(this).html(\''._('Show Details').'<i style="margin-left: 4px;" class="fa fa-chevron-up"></i>\');
                    } else {
                        $(".details").show();
                        $(this).html(\''._('Hide Details').'<i style="margin-left: 4px;" class="fa fa-chevron-down"></i>\');
                    }
                 });';

    $content .= " }); \n\n </script>";

    // Sort by state with problems showing first
    uasort($bpi_objects, function($a, $b) {
        return strcmp($b->state, $a->state);
    });

    foreach ($bpi_objects as $object) {

        // Skip if arg is the priority type, if null, then don't skip ever
        if ($arg !== null && ($object->priority != $arg && $object->type != $arg)) {
            continue;
        } else if ($arg === null && $object->type != 'default') {
            continue;
        }

        $tab = grab_request_var('tab', bpi_get_priority_tab($object->priority));

        if ($object->get_primary() > 0) {

            if (!is_authorized_for_bpi_group($object, $_SESSION['username'])) continue;

            $state = return_state($object->state);
            $state_css_class = return_state_css_class($object->state);
            $gpc_icon = '';
            if ($object->has_group_children == true) {
                $gpc_icon = '<td style="width: 28px; text-align: center;"><i class="fa fa-sitemap fa-14 bpi-tt-bind" title="'._('Contains child groups').'"></i></td>';
            }

            $td_id = 'td' . $bpi_unique;
            $id = md5($object->name)."_".$bpi_unique;
            $info_th = $object->get_info_html();
            $desc_td = (trim($object->desc) == '') ? '' : "<td>{$object->desc}</td>";

            // Display for only primary groups. See the $object->display_tree() for subgroup displays
            $content .= "
             <table class='primary table table-striped table-bordered'>
                <tr>
                    <td class='{$state_css_class} fixedwidth'>{$state}</th>
                    <td class='group'>
                        <a id='{$td_id}' href='javascript:void(0)' title='Group ID: ".encode_form_valq($object->name)."' onclick='showHide(\"".$id."\",\"$td_id\")' class='grouphide'>
                            <i class='fa fa-fw fa-chevron-right' style='padding-right: 3px;'></i>" . $object->get_title() . "
                        </a>
                    </td>
                    <td class='sort'>
                        <a class='sortlink bpi-tt-bind' title='"._('Sort by priority')."' href=\"javascript:sortchildren('{$id}',false);\">
                            <i class='fa fa-sort fa-14'></i>
                        </a>
                    </td>
                    {$gpc_icon}
                    {$info_th}
                    <td>{$object->status_text}</td>
                    {$desc_td}\n";

            // For auth_users with full permissions
            if (can_control_bpi_groups($_SESSION['username'])) {
                $content .= '<td class="actions"><a class="bpi-tt-bind" href="index.php?cmd=edit&arg='.encode_form_val(urlencode($object->name)).'" title="'._('Edit').'"><img src="'.theme_image('pencil.png').'"></a> <a class="bpi-tt-bind" href="javascript:deleteGroup(\'index.php?cmd=delete&arg='.encode_form_val(urlencode($object->name)).'&tab='.$tab.'\')" title="'._('Delete').'"><img src="'.theme_image('cross.png').'"></a></td>';
            }

            $content .= "
                </tr>
            </table>";

            $content .= "<div class='toplevel' id='{$id}' style='display:none;'>";

            $object->display_tree($content);
            $content .= "</div>\n\n";
            $bpi_unique++;
            $resultCount++;
        }
    }
    $content .= "";

    if ($resultCount == 0) $content .= "<div class='message'>" . _("No BPI Group results for this filter.") . "</div>";

    // Get missing objects to list
    $missing_hosts = 0;
    $missing_services = 0;
    $missing_objects = 0;

    if (!empty($bpi_missing['services'])) {
        foreach ($bpi_missing['services'] as $group => $hosts) {
            foreach ($hosts as $host => $services) {
                $missing_services += count($services);
            }
        }
    }

    if (!empty($bpi_missing['hosts'])) {
        $missing_hosts = count($bpi_missing['hosts']);
    }

    $missing_html = '';
    $missing_objects = $missing_hosts + $missing_services;
    if (!empty($missing_objects)) {
        $missing_html = '<div class="alert alert-danger">' . _("The current BPI config has") . " <b>$missing_objects</b> " . _('missing objects') . '. ' . _("Missing objects will show up as having an UNKNOWN or UNREACHABLE status") . '. <a id="show-details">' . _("Show details") . '<i style="margin-left: 4px;" class="fa fa-chevron-up"></i></a><div class="hide details">';
        if (!empty($bpi_missing['hosts'])) {
            $html = '<table class="table table-striped table-bordered table-condensed"><thead><tr><th>'._('BPI Group').'</th><th>'._('Host').'</th></tr></thead><tbody>';
            foreach ($bpi_missing['hosts'] as $group => $host) {
                $html .= '<tr><td>'.$group.'</td><td>'.$host.'</td></tr>';
            }
            $html .= '</tbody></table>';
            $missing_html .= $html;
        }
        if (!empty($bpi_missing['services'])) {
            $html = '<table class="table table-striped table-bordered table-condensed"><thead><tr><th>'._('BPI Group').'</th><th>'._('Host').'</th><th>'._('Service').'</th></tr></thead><tbody>';
            foreach ($bpi_missing['services'] as $group => $hosts) {
                foreach ($hosts as $host => $services) {
                    foreach ($services as $service) {
                        $html .= '<tr><td>'.$group.'</td><td>'.$host.'</td><td>'.$service.'</td></tr>';
                    }
                }
            }
            $html .= '</tbody></table>';
            $missing_html .= $html;
        }
        $missing_html .= '</div></div>';
    }

    print $missing_html;

    return $content;
}
