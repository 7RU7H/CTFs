<?php
include_once(dirname(__FILE__).'/includes/utils.inc.php');

$this_version = "4.4.13 in Nagios XI";
$this_year = date('Y');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>

<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title>Nagios Core</title>
<link rel="stylesheet" type="text/css" href="stylesheets/common.css?<?php echo $this_version; ?>" />
<link rel="stylesheet" type="text/css" href="stylesheets/nag_funcs.css?<?php echo $this_version; ?>" />
<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="js/nag_funcs.js"></script>

<script type='text/javascript'>
	var cookie;

	<?php if ($cfg["enable_page_tour"]) { ?>
		var vbox;
		var vBoxId = "main";
		var vboxText = "<a href=https://www.nagios.com/tours target=_blank> " +
						"Click here to watch the entire Nagios Core 4 Tour!</a>";
	<?php } ?>

	$(document).ready(function() {
		var user = "<?php echo htmlspecialchars($_SERVER['REMOTE_USER']); ?>";

		<?php if ($cfg["enable_page_tour"]) { ?>
			vBoxId += ";" + user;
			vbox = new vidbox({pos:'lr',vidurl:'https://www.youtube.com/embed/2hVBAet-XpY',
								text:vboxText,vidid:vBoxId});
		<?php } ?>

		getCoreStatus();
	});

	function processBannerItem(item) {
		return item.find('description').text();
	}

	function processNewsItem(item) {
		var link = item.find('link').text();
		var title = item.find('title').text();
		return link && title
			? '<li><a href="' + link + '" target="_blank">' + title + '</a></li>'
			: '';
	}

	function processPromoItem(item) {
		var description = item.find('description').text();
		return description
			? '<li>' + description + '</li>'
			: '';
	}

	// Get the daemon status JSON.
	function getCoreStatus() {
		setCoreStatusHTML('passiveonly', 'Checking process status...');

		$.get('<?php echo $cfg["cgi_base_url"];?>/statusjson.cgi?query=programstatus', function(d) {
			d = d && d.data && d.data.programstatus || false;
			if (d && d.nagios_pid) {
				var pid = d.nagios_pid;
				var daemon = d.daemon_mode ? 'Daemon' : 'Process';
				setCoreStatusHTML('enabled', daemon + ' running with PID ' + pid);
			} else {
				setCoreStatusHTML('disabled', 'Not running');
			}
		}).fail(function() {
			setCoreStatusHTML('disabled', 'Unable to get process status');
		});
	}

	function setCoreStatusHTML(image, text) {
		$('#core-status').html('<img src="images/' + image + '.gif" /> ' + text);
	}
</script>

</head>


<body id="splashpage">


<div id="mainbrandsplash" style="margin: 60px 0;">
	<div id="mainlogo"><a href="https://www.nagios.org/" target="_blank"><img src="images/logofullsize.png" border="0" alt="Nagios" title="Nagios"></a></div>
	<div><span id="core-status"></span></div>
</div>


<div id="currentversioninfo">
	<div class="product">Nagios<sup><span style="font-size: small;">&reg;</span></sup> Core<sup><span style="font-size: small;">&trade;</span></sup></div>
	<div class="version" style="margin-top: 5px;">Version <?php echo $this_version; ?></div>
</div>


<div style="padding: 60px 0 100px 0;">
<div id="splashboxes">

	<div id='splashrow1'>

		<div style="margin-bottom: 40px; font-weight: bold;">
			<a href="../nagiosxi" target="_parent">Back to Nagios XI</a>
		</div>

		<div id="splashbox2" class="splashbox" style="padding: 20px; float: none; margin: auto; width: 50%;">
			<h2>Quick Links</h2>
			<ul>
				<li><a href="https://library.nagios.com" target="_blank">Nagios Library</a> (tutorials and docs)</li>
				<li><a href="https://labs.nagios.com" target="_blank">Nagios Labs</a> (development blog)</li>
				<li><a href="https://exchange.nagios.org" target="_blank">Nagios Exchange</a> (plugins and addons)</li>
				<li><a href="https://support.nagios.com" target="_blank">Nagios Support</a> (tech support)</li>
				<li><a href="https://www.nagios.com" target="_blank">Nagios.com</a> (company)</li>
				<li><a href="https://www.nagios.org" target="_blank">Nagios.org</a> (project)</li>
			</ul>
		</div>

	</div><!-- end splashrow1 -->

	<div style="clear: both;"></div>

</div><!-- end splashboxes-->
</div>


<div id="mainfooter">
	<div id="maincopy">
		Copyright &copy; 2010-<?php echo $this_year; ?> Nagios Core Development Team and Community Contributors. Copyright &copy; 1999-2009 Ethan Galstad. See the THANKS file for more information on contributors.
	</div>
	<div CLASS="disclaimer">
		Nagios Core is licensed under the GNU General Public License and is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A PARTICULAR PURPOSE.  Nagios, Nagios Core and the Nagios logo are trademarks, servicemarks, registered trademarks or registered servicemarks owned by Nagios Enterprises, LLC.  Use of the Nagios marks is governed by the <A href="https://www.nagios.com/legal/trademarks/">trademark use restrictions</a>.
	</div>
	<div class="logos">
		<a href="https://www.nagios.org/" target="_blank"><img src="images/weblogo1.png" width="102" height="47" border="0" style="padding: 0 40px 0 40px;" title="Nagios.org" /></a>
		<a href="http://sourceforge.net/projects/nagios" target="_blank"><img src="images/sflogo.png" width="88" height="31" border="0" alt="SourceForge.net Logo" /></a>
	</div>
</div>


</body>
</html>
