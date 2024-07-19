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
 * Function not used in BPI enterprise version
 */
function grab_details()
{

    $f = fopen(STATUSFILE, "r") or exit("Unable to open status.dat file!");
    $hostdetails = array();
    $servicedetails = array();

    // Counters for iteration through file
    $hostcounter = 0;
    $servicecounter = 0;
    $case = 0;
    $service_id = 0;
    $host_id = 0;

    $hoststring = 'hoststatus {';
    $servicestring = 'servicestatus {';
    $matches = array('host_name', 'service_description', 'current_state',
        'last_check', 'has_been_checked', 'plugin_output',
        'problem_has_been_acknowledged', 'scheduled_downtime_depth');

    // Read through file and assign host and service status into separate arrays
    while (!feof($f))
    {
        $line = fgets($f);

        if (strpos($line, $hoststring) !== false) {
            $case = 1;
            $hostcounter++;
            $hostdetails[$hostcounter] = array();
            $host_id++;
        }

        if (strpos($line, $servicestring) !== false) {
            $case = 2;
            $servicecounter++;
            $servicedetails[$servicecounter] = array();
            $service_id++;
        }

        if (strpos($line, '}') !== false) {
            $case = 0;
        }

        // Grab variables according to the enabled boolean switch
        switch ($case)
        {
            case 0:
                break;

            case 1:
                if (strpos($line, $hoststring) !== 0) {

                    $strings = explode('=', $line);
                    $key = trim($strings[0]);
                    if (!in_array($key, $matches)) break;
                    $value = trim($strings[1]);

                    // Added conditional to count for '=' signs in the performance data
                    if (isset($strings[2])) {
                        $i = 2;
                        while (isset($strings[$i])) {
                            $value .= '=' . $strings[$i];
                            $i++;
                        }
                    }
                    $hostdetails[$hostcounter][$key] = $value;
                    $hostdetails[$hostcounter]['host_id'] = $host_id;
                }
                break;

            case 2:
                if (strpos($line, $servicestring) !== 0) {

                    $strings = explode('=', $line);
                    $key = trim($strings[0]);
                    if (!in_array($key, $matches)) break;
                    $value = trim($strings[1]);

                    // Added conditional to count for '=' signs in the performance data
                    if (isset($strings[2])) {
                        $i = 2;
                        while (isset($strings[$i])) {
                            $value .= '=' . $strings[$i];
                            $i++;
                        }
                    }
                    $servicedetails[$servicecounter][$key] = $value;
                    $servicedetails[$servicecounter]['service_id'] = $service_id;
                }
                break;
        }
    }

    fclose($f);
    return array($hostdetails, $servicedetails);
}
