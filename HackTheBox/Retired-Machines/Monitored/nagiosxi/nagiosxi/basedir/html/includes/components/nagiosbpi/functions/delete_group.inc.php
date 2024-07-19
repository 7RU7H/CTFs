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
 * Deletes a BPI group from the config file.
 *
 * @param string $arg expecting a group ID as $arg
 *
 * @return mixed array($errorcode,$message)
 */
function delete_group($arg, $backup = true)
{
    // Grab entire config definition as a string
    $msg = '';
    $errors = 0;
    $config = get_config_string($arg);

    if ($config) {

        if (!CLI && $backup) {
            if (!$config_id = bpi_config_create_backup(BPI_BACKUP_AUTO)) {
                $msg .= _("Backup failed. Aborting change. Verify that backup directory is writeable.");
                $errors++;
                return array($errors, $msg);
            }
        }

        $contents = file_get_contents(BPI_CONFIGFILE);

        $new = str_replace($config, "", $contents, $count0);
        $msg .= _("Groups Deleted") . ": $count0 <br />";

        if (isset($new) && $count0 > 0) {
            //delete all instances where group is a child/member of another group
            //removes $group;|,
            $matchstring1 = '$' . $arg . ';|,'; //used for str_replace
            $pregString1 = '/\$' . preg_quote($arg, '/') . ';\|,/'; //used for preg_match
            //removes $group;&,
            $matchstring2 = '$' . $arg . ';&,'; //used for str_replace
            $pregString2 = '/\$' . preg_quote($arg, '/') . ';\&,/'; //used for preg_match

            $changecount = 0;
            if (preg_match($pregString1, $new)) {
                $new = str_replace($matchstring1, "", $new, $count1); //replace all matched instances of string
                //print "<p>Matched |, should be replaced.</p>";
                if (isset($count1)) {
                    $changecount += $count1;
                }
            }
            if (preg_match($pregString2, $new)) {
                $new = str_replace($matchstring2, "", $new, $count2);
                //print "<p>Matched & , should be replaced.</p>";
                if (isset($count2)) {
                    $changecount += $count2;
                }
            }

            file_put_contents(BPI_CONFIGFILE, $new);

            if ($changecount > 0) {
                $msg .= _("Group has been removed as a member from $changecount other groups.");
            }

            $msg .= _("File successfully written!");

            if (!CLI && $backup) {
                bpi_config_update_changes($config_id);
            }

        } else {
            $msg .= _("Unable to match string in config file.");
            $errors++;
        }

    } else {
        $msg .= _("Unable to find group in config file, no changes made.");
        $errors++;
    }

    return array($errors, $msg);
}
