<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    $pageopt = grab_request_var("pageopt", "");

    if ($pageopt == "monitoringengine") {
        show_enginestat();
    } else {
        show_sysstat();
    }
    exit;
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_sysstat($error = false, $msg = "")
{

    do_page_start(array("page_title" => _('System Status')), true);
?>

    <h1><?php echo _('System Status'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_component_status", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_server_stats", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <?php
    do_page_end(true);
    exit();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_enginestat($error = false, $msg = "")
{

    do_page_start(array("page_title" => _('Monitoring Engine Status')), true);
?>

    <h1><?php echo _('Monitoring Engine Status'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_process", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_eventqueue_chart", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <br clear="all">

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_stats", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <div style="float: left; margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_perf", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <?php
    do_page_end(true);
    exit();
}