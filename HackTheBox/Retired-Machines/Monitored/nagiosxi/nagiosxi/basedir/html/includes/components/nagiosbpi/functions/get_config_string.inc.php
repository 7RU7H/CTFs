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
 * Parses bpi.conf file for config string.
 *
 * @param string $arg the group ID to fetch
 *
 * @return string $config the configuration string output from the bpi.conf file | false on failure
 */
function get_config_string($arg)
{
    $f = fopen(BPI_CONFIGFILE, "r") or exit("Unable to open file: " . BPI_CONFIGFILE . "!");
    $case = 0;
    $keystring = 'define ' . $arg;
    $config = '';

    // Read through file and assign host and service status into separate arrays
    while (!feof($f))
    {
        $line = fgets($f);

        if (preg_match('/' . preg_quote($keystring, '/') . ' /', $line)) {
            $case = 1;
        }

        if (preg_match('/}/', $line) && $case == 1) {
            $config .= $line;
            $case = 0;
        }

        // Grab variables according to the enabled boolean switch
        switch ($case)
        {
            case 0:
                break;

            case 1:
                $config .= $line;
                break;

        }
    }

    fclose($f);

    // Check to make sure it actually found something
    $len = strlen($config);
    if ($len > 30) {
        return trim($config);
    }

    return false;
}
