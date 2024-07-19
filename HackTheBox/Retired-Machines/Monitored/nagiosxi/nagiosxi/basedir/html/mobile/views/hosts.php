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

mobile_page_start(_('Hosts'));

route_request();

function route_request()
{
	$view = grab_request_var('view', '');

	$problems_tooltip = '<a class="problem-tooltip help-icon" data-placement="bottom" data-content="' . _('A problem host is any host that is in a non-OK state, whether it has been acknowledged or not.') . '" data-original-title="" title="" style="margin-left: 20px;"><i class="fa fa-question-circle"></i></a>';
	$unhandled_tooltip = '<a class="unhandled-tooltip help-icon" data-placement="bottom" data-content="' . _('An unhandled host is any host that is in a non-OK state, and has NOT been acknowledged or scheduled for downtime.') . '" data-original-title="" title="" style="margin-left: 20px;"><i class="fa fa-question-circle"></i></a>';

	switch ($view) {
		case 'up':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Hosts'), _('Up')));
			show_host_status(0);
			break;
		case 'down':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Hosts'), _('Down')));
			show_host_status(1);
			break;
		case 'unreachable':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Hosts'), _('Unreachable')));
			show_host_status(2);
			break;
		case 'problems':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s' . " $problems_tooltip", _('Hosts'), _('Problems')));
			show_host_status('problems');
			break;
		case 'unhandled':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s' . " $unhandled_tooltip", _('Hosts'), _('Unhandled')));
			show_host_status('unhandled');
			break;
		default:
			mobile_load_navbar(_('Host Status'));
			show_host_status();
			break;
	}
}


function show_host_status($status = null)
{
	$backendargs = array();

	$host_id = grab_request_var('host', '');

    $backendargs["limitrecords"] = false; // don't limit records

    if ($status !== null) {
    	$backendargs["current_state"] = "in:$status"; // down or unreachable
    }

    if (!empty($host_id) ) {
		$backendargs["host_id"] = $host_id;
    } else {
    	if ($status === 'problems' || $status === 'unhandled') {
	    	$backendargs["current_state"] = "in:1,2";
	    }
    }

	$hosts_xml = get_xml_host_status($backendargs);
	if (!empty($hosts_xml)) {
		$hosts_xml->asXML();
	}
	?>

		<div class="mobile-body">
			<div class="mobile-grid">

				<?php 

					if (empty($hosts_xml) || $hosts_xml->recordcount == 0) {
						show_zero_record_card();
					} else {

						foreach ($hosts_xml->hoststatus as $host) {
							
							$service_info = get_service_overview_data($host->host_id);
							$problem_count = $service_info['problems'];

							if ( ($status === 'unhandled') && (($host->problem_acknowledged == 1) || ($host->scheduled_downtime_depth != 0)) ) {
								continue;
							}	

							$status_duration = time() - strtotime($host->last_state_change);
							$status_duration_text = seconds_to_time($status_duration);
							$host_name = strval($host->name);
							$notifications_enabled = intval($host->notifications_enabled);

							if ($notifications_enabled) {
								$notification_ajax = get_notification_ajax('disable', $host_name, '', 'toggle_notification_icon');
							} else {
								$notification_ajax = get_notification_ajax('enable', $host_name, '', 'toggle_notification_icon');
							}
					?>
					<div class="card small check-overview-card">

						<?php if ($problem_count > 0): ?>
							<div class="problem-badge">
								<span><?php echo sprintf(_("%d Problem(s)"), $problem_count); ?></span>
							</div>
						<?php endif; ?>
						
						<h6 class="check-overview-title"><?php echo $host->name; ?></h6>
						
						<div class="stats">
							<h6><?php echo _('Status'); ?></h6>
							<?php echo get_host_status_badge($host->current_state); ?>
							<span><?php echo sprintf(_('Duration: %s'), $status_duration_text); ?></span>

							<h6><?php echo _('Check Output'); ?></h6>
							<p><?php echo $host->status_text; ?></p>

							<h6><?php echo _('Last Check'); ?></h6>
							<p><?php echo $host->status_update_time; ?></p>
						</div>

						<div class="card-actions">
							<?php
							if ($host->current_state != 0) {
								if (intval($host->problem_acknowledged) == '1') {
							?>
								<div>
									<a>
										<i class="fas fa-check" style="font-size: 2.3rem;"></i><br>
										<span><?php echo _('Acknowledged'); ?></span>
									</a>
								</div>
							<?php }
								else {
							?>
								<div>
									<a href="acknowledge.php?type=host&host=<?php echo $host->host_id; ?>">
										<i class="fas fa-wrench" style="font-size: 2.3rem;"></i><br>
										<span><?php echo _('Acknowledge'); ?></span>
									</a>
								</div>
							<?php 
								}
							}
							?>
							<div>
								<a href="downtime.php?type=host&host=<?php echo $host->host_id; ?>">
									<i class="far fa-clock" style="font-size: 2.3rem;"></i><br>
									<span><?php echo _('Schedule Downtime'); ?></span>
								</a>
							</div>
							<div>
								<?php if ($notifications_enabled): ?>
									<a class="notifications-enabled notification-toggle" <?php echo $notification_ajax; ?>>
										<i class="far fa-bell-slash" style="font-size: 2.3rem;"></i><br>
										<span><?php echo _('Disable Notifications'); ?></span>
									</a>
								<?php else: ?>
									<a class="notification-toggle" <?php echo $notification_ajax; ?>>
										<i class="far fa-bell" style="font-size: 2.3rem;"></i><br>
										<span><?php echo _('Enable Notifications'); ?></span>
									</a>
								<?php endif; ?>
							</div>
							<div>
								<a href="services.php?view=host&host=<?php echo $host->host_id; ?>">
									<i class="fas fa-sitemap" style="font-size: 2.3rem;"></i><br>
									<span><?php echo _('View Services'); ?></span>
								</a>
							</div>
						</div>

					</div>
				<?php
					}
				}
				?>

			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {
				$(".problem-tooltip").popover({ html: true });
				$(".unhandled-tooltip").popover({ html: true });
			});
		</script>

	<?php

mobile_page_end();

}