<?php
ob_start();

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

// BPI Specific stuff 
define('CLI', false);
require_once('inc.inc.php');

$cmd = grab_request_var('cmd',false);

// Ajax call from the router... not the best spot but can't re-do control atm
if ($cmd == 'rename') {
    $name = grab_request_var('name', '');
    $cid = intval(grab_request_var('cid', 0));
    if (empty($name) || empty($cid)) {
        print json_encode(array('error' => _('Cannot have a blank name or no ID selected.')));
        exit();
    }
    $x = bpi_config_rename_backup($cid, $name);
    print json_encode(array('error' => 0));
    exit();
}

do_page_start(array("page_title" => _("Business Process Intelligence")), true);
?>

<link rel="stylesheet" href="bpi_style.css?<?php echo get_product_release(); ?>" type="text/css" media="screen">
<script type="text/javascript" src="bpi.js"></script>
<script type="text/javascript">
<?php
$tab = grab_request_var('tab',false);
$tab = preg_replace('/[^A-Za-z0-9\-]/', '', $tab);
if ($cmd == 'add') echo "var tab='tabcreate';";
else if ($tab) echo "var tab='tab{$tab}';";
else if (empty($cmd) && empty($tab)) echo "var tab='tabhigh';";
else echo "var tab=false";
echo "MULTIPLIER = ".$bpi_options['MULTIPLIER'].';';
?>
</script>

<h1><?php echo _("Business Process Intelligence"); ?></h1>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header">
        <li class="ui-state-default" style="margin-right: 20px;">
            <a class="ui-tabs-anchor" id="taball" href="index.php?tab=all" title="<?php echo _("All Priorities"); ?>"><?php echo _("All Priorities"); ?></a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id="tabhigh" href="index.php?tab=high" title="<?php echo _("High Priority"); ?>"><?php echo _("High Priority"); ?></a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id="tabmedium" href="index.php?tab=medium" title="<?php echo _("Medium Priority"); ?>"><?php echo _("Medium Priority"); ?></a>
        </li>
        <li class="ui-state-default" style="margin-right: 20px;">
            <a class="ui-tabs-anchor" id="tablow" href="index.php?tab=low" title="<?php echo _("Low Priority"); ?>"><?php echo _("Low Priority"); ?></a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id="tabhostgroups" href="index.php?tab=hostgroups" title="<?php echo _("Hostgroups"); ?>"><?php echo _("Hostgroups"); ?></a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id="tabservicegroups" href="index.php?tab=servicegroups" title="<?php echo _("Servicegroups"); ?>"><?php echo _("Servicegroups"); ?></a>
        </li>
        <?php if (can_control_bpi_groups($_SESSION['username'])) { ?>
        <li class="bpiTab" style="padding: 5px 10px 7px 10px; margin-left: 8px;">
            <a class="tab" id="tabcreate" href="index.php?cmd=add&tab=add" title="<?php echo _("Create New BPI Group"); ?>"><?php echo _("Create New BPI Group"); ?></a>
        </li>
        <?php } ?>
        <li class="ui-state-default" style="float: right;">
            <a class="ui-tabs-anchor" id="tabdocumentation" href="https://assets.nagios.com/downloads/nagiosxi/docs/Using_Nagios_BPI_v2.pdf" target="_blank" title="<?php echo _('Documentation'); ?>"><i class="fa fa-file l"></i> <?php echo _("Documentation"); ?> <i class="fa fa-external-link r"></i></a>
        </li>
        <?php if (is_admin()) { ?>
        <li class="ui-state-default" style="float: right;">
            <a class="ui-tabs-anchor" id="tabsettings" href="index.php?tab=settings" title="<?php echo _('Settings'); ?>"><i class="fa fa-cog l"></i> <?php echo _("Settings"); ?></a>
        </li>
        <li class="ui-state-default" style="float: right;">
            <a class="ui-tabs-anchor" id="tabconfig" href="index.php?tab=config" title="<?php echo _('Config Management'); ?>"><i class="fa fa-folder l"></i> <?php echo _("Config Management"); ?></a>
        </li>
        <?php } ?>
    </ul>
</div>

<div id="lastUpdate" class="hide"></div>

<?php
error_check();

print bpi_page_router();

do_page_end(true);

ob_end_flush();
