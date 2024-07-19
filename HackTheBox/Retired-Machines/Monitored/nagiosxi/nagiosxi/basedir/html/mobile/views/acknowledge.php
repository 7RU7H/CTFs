<?php
//
// Nagios XI Mobile Interface
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

// Base XI code base
require_once('../../includes/common.inc.php');
// Mobile XI code base
require_once('../controllers/main-utils.php');
load_controllers();

// Initialization stuff
pre_init();
init_session();
check_mobile_authentication();
grab_request_vars();
decode_request_vars();

// Check prereqs and authentication
check_prereqs();

route_request();

function route_request()
{
	$mode = grab_request_var('mode', '');

	switch ($mode) {
		default:
			show_acknowledgement();
			break;
	}
}

function show_acknowledgement()
{
	mobile_page_start(_('Acknowledge Problem'));

	$type = grab_request_var('type', '');
	$host_id = grab_request_var('host', '');
	$service_id = grab_request_var('service', '');

	// Downtime URL
	$base_path = get_base_url();
	$acknowledge_url = $base_path . "includes/components/nagioscore/ui/cmd.php?";

	// Grab object details
	$backendargs["limitrecords"] = false; // don't limit records
	$backendargs['current_state'] = 'in:0,1,2,3';
	$backendargs["host_id"] = "in:$host_id";

	$default_start_date = strftime("%FT%T", strtotime('now'));
	$default_end_date   = strftime("%FT%T", strtotime('now + 2 hours'));

	$host_xml = get_xml_host_status($backendargs);
	$host_xml->asXML();
	$host_name = $host_xml->hoststatus->name;

	$author = get_user_attr(0, 'name');

	// If service, grab service info
	if ($type == 'service') {
		$backendargs["service_id"] = "in:$service_id";
		$service_xml = get_xml_service_status($backendargs);
		$service_xml->asXML();
		$service_name = $service_xml->servicestatus->name;
	}

	mobile_load_navbar(_('Acknowledge Problem'));

	?>

		<div class="mobile-body">
			<div class="mobile-grid">

				<div class="card medium">
					<h1 class="card-title"><?php echo _('Acknowledge Problem'); ?></h1>
					<form class="mobile-form" action="" method="post">

						<div class="form-group">
							<label for="host_name"><?php echo _('Host Name'); ?></label>
							<input type="text" id="host_name" placeholder="<?php echo _('Host Name'); ?>" value="<?php echo $host_name; ?>" disabled>
						</div>

						<?php if ($type == 'service'): ?>
							<div class="form-group">
								<label for="service_name"><?php echo _('Service Name'); ?></label>
								<input type="text" name="service_name" id="service_name" placeholder="<?php echo _('Service Name'); ?>" value="<?php echo $service_name; ?>" disabled>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<label for="author"><?php echo _('Author'); ?></label>
							<input type="text" id="author" placeholder="<?php echo _('Host Name'); ?>" value="<?php echo $author; ?>" disabled>
						</div>

						<div class="form-group">
							<label for="comment"><?php echo _('Comment'); ?></label>
							<input type="text" name="comment" id="comment" placeholder="<?php echo _('Comment'); ?>">
						</div>

						<div class="form-group">
							<label class="mobile-checkbox-label" for="sticky">
								<input class="mobile-checkbox" type="checkbox" name="sticky" id="sticky" checked>
								<span class="mobile-label-interior"><?php echo _('Sticky Acknowledgement'); ?><p>
							</label>
						</div>

						<div class="form-group">
							<label class="mobile-checkbox-label" for="notify">
								<input class="mobile-checkbox" type="checkbox" name="notify" id="notify" checked>
								<span class="mobile-label-interior"><?php echo _('Send Notification'); ?><p>
							</label>
						</div>

						<div class="form-group">
							<label class="mobile-checkbox-label" for="persistent">
								<input class="mobile-checkbox" type="checkbox" name="persistent" id="persistent">
								<span class="mobile-label-interior"><?php echo _('Persistent Comment'); ?><p>
							</label>
						</div>

						<input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
						<input type="hidden" name="host_id" id="host_id" value="<?php echo $host_name; ?>">
						<input type="hidden" name="acknowledge_url" id="acknowledge_url" value="<?php echo $acknowledge_url; ?>">

						<?php if ($type == 'service'): ?>
							<input type="hidden" name="services" value="<?php echo $service_id; ?>">
						<?php endif; ?>

						<button type="button" class="btn btn-default" id="submitDowntime"><?php echo _('Acknowledge Problem'); ?></button>
					
					</form>
				</div>

				<script type="text/javascript">
					$(document).ready(function() {
						$('#submitDowntime').click(function() {

							var acknowledge_url = $('#acknowledge_url').val();
							var comment = $('#comment').val();
							var author = $('#author').val();

							// Verify form before submit
							if (comment == '' || author == '') {
								alert("You must fill out the entire form.");
								return;
							}

							var args = {
							    cmd_typ: <?php echo $type == "service" ? NAGIOSCORE_CMD_ACKNOWLEDGE_SVC_PROBLEM : NAGIOSCORE_CMD_ACKNOWLEDGE_HOST_PROBLEM; ?>,
							    cmd_mod: 2,
							    nsp: nsp_str,
							    host: $('#host_name').val(),
							    service: $('#service_name').val(),
							    com_author: author,
							    com_data: comment
							}

							if ($('#sticky').prop('checked')) {
								args.sticky_ack = 'on';
							}

							if ($('#notify').prop('checked')) {
								args.send_notification = 'on';
							}

							if ($('#persistent').prop('checked')) {
								args.persistent = 'on';
							}

							$.ajax({
								type: "POST",
								async: false,
								url: acknowledge_url,
								data: args,
								success: function (data) {
									ajax_set_toast('<?php echo _('Successfully Acknowledged Problem'); ?>', 'success');
									window.location.replace('home.php');
								}
							});
						});
					}); 
				</script>

	<?php

	mobile_page_end();

}