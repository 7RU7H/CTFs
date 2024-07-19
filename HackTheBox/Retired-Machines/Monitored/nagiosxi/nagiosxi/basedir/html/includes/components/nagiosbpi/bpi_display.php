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


ini_set('display_errors', 'off');
define('CLI', false);


// Contains all includes, session, and global data
require_once('inc.inc.php');


bpi_display();


/**
 * API page routing to be used by the AJAX calls from the main index page, mainly a wrapper function for route_rab().
 */
function bpi_display()
{
    $group_id = grab_request_var('id', false);
    $tab = grab_array_var($_SESSION, 'tab', false);
    $request_tab = grab_request_var('tab', false);
    if ($request_tab) {
        $tab = $request_tab;
    }
    $tab = preg_replace('/[^A-Za-z0-9\-]/', '', $tab);
    $backend = grab_request_var('cmd', false);

    // XML display
    if ($backend == 'getbpixml' && is_admin()) {
        header('Content-type:text/xml');
        echo bpi_xml_fetch();
        exit();
    }

    // Crunch data to determine group states
    if ($tab) {
        $msg = bpi_init($tab);
        route_tab($tab, $msg);
    }
}


/**
 * Page routing function to handle page content for tab navigation.
 *
 * @param string $tab the selected page tab
 * @param string $msg [optional] return message to be displayed at the top of the page
 */
function route_tab($tab, $msg = '')
{
    print $msg;

    switch ($tab)
    {
        case 'low':
            print bpi_view_object_html(LOW);
            break;

        case 'medium':
            print bpi_view_object_html(MEDIUM);
            break;

        case 'hostgroups':
            print bpi_view_hostgroups();
            break;

        case 'servicegroups':
            print bpi_view_servicegroups();
            break;

        case 'all':
            print bpi_view_object_html();
            break;

        case 'high':
        default:
            print bpi_view_object_html(HIGH);
            break;
    }
}
