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
			show_downtime();
			break;
	}
}

function show_downtime()
{
	mobile_page_start(_('Schedule Downtime'));

	$type = grab_request_var('type', '');
	$host_id = grab_request_var('host', '');
	$service_id = grab_request_var('service', '');

	// Downtime URL
	$base_path = get_base_url();
	$downtime_url = "$base_path" . "includes/components/xicore/downtime.php?cmd=submit";

	// Grab object details
	$backendargs["limitrecords"] = false; // don't limit records
	$backendargs['current_state'] = 'in:0,1,2,3';
	$backendargs["host_id"] = "in:$host_id";

	$default_start_date = strftime("%FT%T", strtotime('now'));
	$default_end_date   = strftime("%FT%T", strtotime('now + 2 hours'));

	$host_xml = get_xml_host_status($backendargs);
	$host_xml->asXML();
	$host_name = $host_xml->hoststatus->name;

	// If service, grab service info
	if ($type == 'service') {
		$backendargs["service_id"] = "in:$service_id";
		$service_xml = get_xml_service_status($backendargs);
		$service_xml->asXML();
		$service_name = $service_xml->servicestatus->name;
	}

	mobile_load_navbar(_('Schedule Downtime'));

	?>

		<div class="mobile-body">
			<div class="mobile-grid">

				<div class="card medium">
					<h1 class="card-title"><?php echo _('Schedule Downtime'); ?></h1>
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
							<label for="comment"><?php echo _('Comment'); ?></label>
							<input type="text" name="comment" id="comment" placeholder="<?php echo _('Comment'); ?>">
						</div>

						<div class="form-group">
							<label for="start-datetime"><?php echo _('Start'); ?>:</label>
							<input type="datetime-local" name="start-datetime" value="<?php echo $default_start_date; ?>" id="start-datetime">
						</div>

						<div class="form-group">
							<label for="end-datetime"><?php echo _('End'); ?>:</label>
							<input type="datetime-local" name="end-datetime" value="<?php echo $default_end_date; ?>" id="end-datetime">
						</div>


						<input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
						<input type="hidden" name="host_id" id="host_id" value="<?php echo $host_name; ?>">
						<input type="hidden" name="downtime_url" id="downtime_url" value="<?php echo $downtime_url; ?>">

						<?php if ($type == 'service'): ?>
							<input type="hidden" name="services" value="<?php echo $service_id; ?>">
						<?php endif; ?>

						<button type="button" class="btn btn-default" id="submitDowntime"><?php echo _('Schedule Downtime'); ?></button>
					
					</form>
				</div>

				<script type="text/javascript">
					$(document).ready(function() {
						$('#submitDowntime').click(function() {

							hosts = new Array();
							services = new Object();

							var type = $('#type').val();
							var comment = $('#comment').val();
							var start_datetime = $('#start-datetime').val().replace('T', ' ');
							var end_datetime = $('#end-datetime').val().replace('T', ' ');
							var downtime_url = $('#downtime_url').val();
							var host = $('#host_name').val();
							var service = $('#service_name').val();
							var service_array = new Array();
							service_array.push(service);
							hosts.push(host);
							services[host] = service_array;

							// Check to verify downtime comment and other things
							if (comment == '' || type == '' || start_datetime == '' || end_datetime == '') {
								alert("You must fill out the entire form.")
								return;
							}

							$.ajax({
								type: "POST",
								async: false,
								url: downtime_url,
								data: {mode: 'submit', nsp: nsp_str, type: type, hosts: hosts, services: services, start_time: start_datetime, end_time: end_datetime, com_data: comment},
								success: function (data) {
									ajax_set_toast('<?php echo _('Successfully Scheduled Downtime'); ?>', 'success');
									window.location.replace('home.php');
								}
							});
						});
					}); 
				</script>

	<?php

	mobile_page_end();

}