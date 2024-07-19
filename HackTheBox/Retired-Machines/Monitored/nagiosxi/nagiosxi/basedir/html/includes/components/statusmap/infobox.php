<?php
require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs 
grab_request_vars();
check_prereqs();
check_authentication(false);
?>
<table class="infoBox" border="1" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td class="infoBox">
				<div>
					<div><?php echo _('Last Updated'); ?>: {{lastUpdate | date:'EEE MMM dd HH:mm:ss yyyy'}}</div>
					<div ng-show="updateInterval > 0">
						<?php echo _('Updated every'); ?> {{updateInterval}} <?php echo _('seconds'); ?>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
