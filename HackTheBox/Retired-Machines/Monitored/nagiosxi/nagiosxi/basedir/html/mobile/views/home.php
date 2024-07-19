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

mobile_page_start(_('Home'));
mobile_load_navbar(_('Home'));

// Get overview data
$host_overview_data = get_host_overview_data();
$service_overview_data = get_service_overview_data();

$redirects_disabled = get_user_meta(0, 'mobile_redirects_disabled');

?>

<!-- Header -->
<!-- <div class="mobile-header">

</div> -->

<!-- Body -->
<div class="mobile-body">
	<!-- Host Status Section -->
	<h5 class="card-section-header"><?php echo _('Host Overview') ?></h5>

	<div class="mobile-grid">
		<a href="hosts.php?view=up" class="no-style">
			<div class="card xsmall overview-card green-border">
				<div class="card-number-container">
					<h6><?php echo $host_overview_data['up']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Up'); ?></p>	
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>
		
		<a href="hosts.php?view=down" class="no-style">
			<div class="card xsmall overview-card red-border">
				<div class="card-number-container">
					<h6><?php echo $host_overview_data['down']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Down'); ?></p>	
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="hosts.php?view=unreachable" class="no-style">
			<div class="card xsmall overview-card black-border">
				<div class="card-number-container">
					<h6><?php echo $host_overview_data['unreachable']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Unreachable'); ?></p>	
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="hosts.php?view=problems" class="no-style">			
			<div class="card xsmall overview-card orange-border">
				<div class="card-number-container">
					<h6><?php echo $host_overview_data['problems']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Problems'); ?></p>	
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="hosts.php?view=unhandled" class="no-style">
			<div class="card xsmall overview-card orange-border">
				<div class="card-number-container">
					<h6><?php echo $host_overview_data['unhandled_problems']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Unhandled'); ?></p>	
				<i class="fas fa-2x fa-angle-right"></i>
			</div>	
		</a>

	</div>

	<!-- Service Status Section -->
	<h5 class="card-section-header"><?php echo _('Service Overview') ?></h5>

	<div class="mobile-grid">
				
		<a href="services.php?view=ok" class="no-style">
			<div class="card xsmall overview-card green-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['ok']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('OK'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="services.php?view=warning" class="no-style">
			<div class="card xsmall overview-card yellow-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['warning']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Warning'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="services.php?view=critical" class="no-style">
			<div class="card xsmall overview-card red-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['critical']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Critical'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="services.php?view=unknown" class="no-style">
			<div class="card xsmall overview-card black-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['unknown']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Unknown'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="services.php?view=problems" class="no-style">
			<div class="card xsmall overview-card red-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['problems']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Problems'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

		<a href="services.php?view=unhandled" class="no-style">
			<div class="card xsmall overview-card red-border">
				<div class="card-number-container">
					<h6><?php echo $service_overview_data['unhandled_problems']; ?></h6>
				</div>
				<p class="grey-text center"><?php echo _('Unhandled'); ?></p>
				<i class="fas fa-2x fa-angle-right"></i>
			</div>
		</a>

	</div>

	<?php /* lots of inline styles here because this is the only element like this. 
		   * Before copying, consider using the checkbox styles in acknowledge.php. 
		   */ ?>
	<div class="mobile-grid" style="padding: 15px; grid-template-columns: 30px auto">
		<input class="mobile-checkbox" type="checkbox" name="disable-redirect" id="disable-redirect" <?php echo is_checked($redirects_disabled, '1'); ?> style="grid-column-start: 1; grid-column-end: span 1" >
		<label for="disable-redirect" class="mobile-label-interior" style="font-size:1.5rem; grid-column-start: 2; grid-column-end: span 1; display: flex; align-self: center;"><?php echo _('Disable redirection to mobile interface'); ?></span>
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#disable-redirect').click(function() {

			var redirect = $('#disable-redirect').prop('checked') ? '1' : '0';
			var opts_value = { "keyname": "mobile_redirects_disabled", "keyvalue": redirect };

			var args = {
			    cmd: 'setusermeta',
			    opts: JSON.stringify(opts_value),
			    nsp: nsp_str
			}

			$.ajax({
				type: "POST",
				async: false,
				url: ajax_helper_url,
				data: args,
				success: function(data) { return; }
			});
		});
	}); 
</script>
<?php

//mobile_load_footer();

mobile_page_end();