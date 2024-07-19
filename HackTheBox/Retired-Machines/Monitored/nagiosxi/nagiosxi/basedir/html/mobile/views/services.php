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

mobile_page_start(_('Services'));

route_request();

function route_request()
{
	$view = grab_request_var('view', '');

	$problem_tooltip = '<a class="problem-tooltip help-icon" data-placement="bottom" data-content="A problem service is any service that is in a non-OK state, whether it has been acknowledged or not." data-original-title="" title="" style="margin-left: 20px;"><i class="fa fa-question-circle"></i></a>';
	$unhandled_tooltip = '<a class="unhandled-tooltip help-icon" data-placement="bottom" data-content="An unhandled service is any service that is in a non-OK state, and has NOT been acknowledged or scheduled for downtime." data-original-title="" title="" style="margin-left: 20px;"><i class="fa fa-question-circle"></i></a>';

	switch ($view) {
		case 'ok':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Services'), _('OK')));
			show_service_status(0);
			break;
		case 'warning':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Services'), _('Warning')));
			show_service_status(1);
			break;
		case 'critical':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Services'), _('Critical')));
			show_service_status(2);
			break;
		case 'unknown':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s', _('Services'), _('Unknown')));
			show_service_status(3);
			break;
		case 'problems':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s' . " $problem_tooltip", _('Services'), _('Problems')));
			show_service_status('problems');
			break;
		case 'unhandled':
			mobile_load_navbar(sprintf('%1$s &middot; %2$s' . " $unhandled_tooltip", _('Services'), _('Unhandled')));
			show_service_status('unhandled');
			break;
		case 'host':
			mobile_load_navbar(_("Services"));
			show_service_status('host');
			break;
		default:
			break;
	}
}

function show_service_status($status = null)
{

	// Get down host status
	$backendargs = array();

    $backendargs["limitrecords"] = false; // don't limit records

    if ($status !== null) {
		$backendargs["current_state"] = "in:$status"; // down or unreachable
	}

    if ($status === 'problems' || $status === 'unhandled') {
    	$backendargs["current_state"] = "in:1,2,3";
    }

    if ($status === 'host') {
    	$backendargs['current_state'] = 'in:0,1,2,3';
    	$host_id = grab_request_var('host', '');
        $backendargs["host_id"] = "in:$host_id";
    }

	$service_xml = get_xml_service_status($backendargs);
	if (!empty($service_xml)) {
		$service_xml->asXML();
	}
	?>

		<div class="mobile-body">
			<div class="mobile-grid">

				<?php 
					if (empty($service_xml) || $service_xml->recordcount == 0) {
						show_zero_record_card();
					} else {

						foreach ($service_xml->servicestatus as $service) {

						$status_duration = time() - strtotime($service->last_state_change);
						$status_duration_text = seconds_to_time($status_duration);
						$service_name = strval($service->name);
						$host_name = strval($service->host_name);
						$notifications_enabled = intval($service->notifications_enabled);

						if ($notifications_enabled) {
							$notification_ajax = get_notification_ajax('disable', $host_name, $service_name, 'toggle_notification_icon');
						} else {
							$notification_ajax = get_notification_ajax('enable', $host_name, $service_name, 'toggle_notification_icon');
						}

						if ($status === 'unhandled') {
							if ( $service->problem_acknowledged == 1) {
								continue;
							}
						}

					?>
					<div class="card small check-overview-card">

						<h6 class="check-overview-title"><?php echo $service->name; ?></h6>
						<a class="check-link" href="hosts.php?host=<?php echo $service->host_id; ?>"><?php echo $service->host_name; ?></a>

						<div class="stats">
							<h6><?php echo _('Status'); ?></h6>
							<?php echo get_service_status_badge($service->current_state); ?>
							<span><?php echo sprintf(_('Duration: %s'), $status_duration_text); ?></span>

							<h6><?php echo _('Check Output'); ?></h6>
							<p><?php echo $service->status_text; ?></p>

							<h6><?php echo _('Last Check'); ?></h6>
							<p><?php echo $service->status_update_time; ?></p>
						</div>

						<div class="card-actions">
							<?php if ($service->current_state != 0): ?>
								<?php if (intval($service->problem_acknowledged) == '1'): ?>
									<div>
										<a>
											<i class="fas fa-check" style="font-size: 2.3rem;"></i><br>
											<span><?php echo _('Acknowledged'); ?></span>
										</a>
									</div>
								<?php else: ?>
									<div>
										<a href="acknowledge.php?type=service&host=<?php echo $service->host_id; ?>&service=<?php echo $service->service_id; ?>">
											<i class="fas fa-wrench" style="font-size: 2.3rem;"></i><br>
											<span><?php echo _('Acknowledge'); ?></span>
										</a>
									</div>
								<?php endif;?>
							<?php endif; ?>
							<div>
								<a href="downtime.php?type=service&host=<?php echo $service->host_id; ?>&service=<?php echo $service->service_id; ?>">
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