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
<table>
	<tbody>
		<tr>
			<td class="popup-label"><?php echo _('Name'); ?>:</td>
			<td class="popup-value">{{popupContents.hostname}}</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Alias'); ?>:</td>
			<td class="popup-value">{{popupContents.alias}}</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Address'); ?>:</td>
			<td class="popup-value">{{popupContents.address}}</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('State'); ?>:</td>
			<td class="popup-value" ng-class="popupContents.state">
				{{popupContents.state}}
			</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('State Duration'); ?>:</td>
			<td class="popup-value">
				{{popupContents.duration | duration}}
			</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Last Status Check'); ?>:</td>
			<td class="popup-value">
				{{popupContents.lastcheck | date:'EEE MMM dd HH:mm:ss yyyy'}}
			</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Last State Change'); ?>:</td>
			<td class="popup-value">
				{{popupContents.lastchange | date:'EEE MMM dd HH:mm:ss yyyy'}}
			</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Parent Host(s)'); ?>:</td>
			<td class="popup-value">{{popupContents.parents}}</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Immediate Child Host(s)'); ?>:</td>
			<td class="popup-value">{{popupContents.children}}</td>
		</tr>
		<tr>
			<td class="popup-label"><?php echo _('Services'); ?>:</td>
			<td class="popup-value">
				<table>
					<tbody>
						<tr ng-repeat="(state, count) in popupContents.services">
							<td>
								<span ng-class="state">{{state}}: {{count}}</span>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="popup-value">
				<div style="margin-top: 10px;">
					<div><?php echo _('Click node to collapse/expand its children'); ?></div>
					<div><?php echo _('Shift-click node to make it the root'); ?></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
