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


// Callback function for cleaning post and get variables 
function clean_value(&$value)
{
    $value = htmlspecialchars(trim($value));
}


/**
 * Adds a new BPI group config string to the bpi.conf config file.
 *
 * @param string $string a pre-processed config string to write to file
 *
 * @return mixed array (int $errorcode, string $message)
 */
function add_group($string, $backup = true)
{
    $msg = '';
    $errors = 0;

    // Backup original config file first
    if (!CLI && $backup) {
        if (!$config_id = bpi_config_create_backup(BPI_BACKUP_AUTO)) {
            $msg .= _("Writing to backup file failed. Check permissions for ") . BPI_CONFIGBACKUP;
            $errors++;
        }
    }

    // Check config writeabiliy
    if (is_writeable(BPI_CONFIGFILE)) {
        if (!$f = fopen(BPI_CONFIGFILE, 'ab')) {
            $msg .= _("Cannot open config file!");
            $errors++;
        }
        if (fwrite($f, $string) === FALSE) {
            $msg .= _("Cannot write to config file!");
            $errors++;
        }
    } else {
        $msg .= _('Config file is not writeable!');
        $errors++;
    }

    if ($errors == 0) {
        if (!CLI && $backup) {
            bpi_config_update_changes($config_id);
        }
        $msg .= _("BPI configuration applied successfully!");
    }

    @fclose($f);
    return array($errors, $msg);
}
