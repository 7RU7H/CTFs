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
 * Wrapper function to process get request to add a new group.
 * Builds config string, and writes to file through other functions.
 *
 * @param string $content REFERENCE variable to the main content string
 *
 * @return mixed array(int $errorcode, string $message)
 */
function handle_add_command(&$content)
{
    $vars = array();

    if (isset($_REQUEST['addSubmitted'])) {
        $config = process_post($_REQUEST, true, $vars);
        if ($config) {
            list($errors, $msg) = add_group($config);
            if (empty($errors)) {
                flash_message(_("Group was added successfully. BPI configuration applied and backup created."));
                header("Location: index.php?tab=".bpi_get_priority_tab($vars['priority']));
                die();
            } else {
                return array($errors, $msg);
            }
        }
    }

    $content .= build_form($vars);

    return array(0, "");
}


/**
 * Wrapper function to process delete request for group.
 *
 * @param string $content REFERENCE variable to the main content string
 *
 * @return mixed array(int $errorcode, string $message)
 */
function handle_delete_command(&$content)
{
    $arg = grab_request_var('arg', false);
    if ($arg) {
        list($errors, $msg) = delete_group($arg);
        if (empty($errors)) {
            flash_message(_("Group was removed successfully. BPI configuration applied and backup created."));
        } else {
            flash_message($msg, FLASH_MSG_ERROR);
        }
        $tab = grab_request_var('tab', 'all');
        header("Location: index.php?tab=".urlencode($tab));
        die();
    } else {
        return array(1, _("No BPI Group was specified for deletion."));
    }
}


/**
 * Wrapper function to process edit request for group.
 *
 * @param string $content REFERENCE variable to the main content string
 *
 * @return mixed array(int $errorcode, string $message)
 */
function handle_edit_command(&$content)
{
    $arg = grab_request_var('arg', false);
    $errors = 0;
    $msg = '';
    $content = "";

    // Edit existing groups
    if ($arg) {
        $config = get_config_array($arg);

        if (isset($_POST['editSubmitted'])) {
            $config = process_post($_POST, false, $vars);
            if ($config) {
                list($errors, $msg) = edit_group($arg, $config);
                if (empty($errors)) {
                    flash_message(_("Group was edited successfully. BPI configuration applied and backup created."));
                    header("Location: index.php?tab=".bpi_get_priority_tab($vars['priority']));
                    die();
                } else {
                    return array($errors, $msg);
                }
            }
        }

        // If form hasn't been submitted, preload the form with config data
        $content .= build_form($config);
    } else {
        $msg .= _("Error: No BPI Group specifies to edit.");
        $errors++;
    }

    return array($errors, $msg);
}


/**
 * Wrapper function to process manual_edit_config command.
 *
 * @param   string $content REFERENCE variable to the main content string
 * @return  string          Page html contents
 */
function handle_manual_edit_command(&$content)
{
    return manual_edit_config($content);
}


/**
 * Get the name of the tab basedo on the priority number
 *
 * @param   int     $priority   Integer that signifies the priority type
 * @return  string              Return tab priority value
 */
function bpi_get_priority_tab($priority)
{
    switch ($priority)
    {
        case 1:
            $p = 'high';
            break;

        case 2:
            $p = 'medium';
            break;

        case 3:
            $p = 'low';
            break;

        default:
            $p = 'all';
            break;
    }
    return $p;
}