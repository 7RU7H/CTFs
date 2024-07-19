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


route_request();


function route_request()
{
    show_legacy_reports_page();
}


function show_legacy_reports_page()
{

    licensed_feature_check();

    do_page_start(array("page_title" => _("Reports")), true);

    ?>
    <h1><?php echo _("Reports"); ?></h1>

    <p><?php echo _("Reports allow you to see how well your network and system have performed over a period of time.  Available reports are listed below."); ?></p>

    <br>

    <div class="reportslist">

        <h2><a href="availability.php"><?php echo _("Availability"); ?></a></h2>

        <p><?php echo _("Shows you what percentage of time monitored hosts and services were in different operational states.  Useful for SLA reports."); ?>
            <a href="availability.php"><?php echo _("Run availability report"); ?></a>.</p>


        <h2><a href="statehistory.php"><?php echo _("State History"); ?></a></h2>

        <p><?php echo _("Provides a historical report of alerts/events that occurred over time."); ?>  <a
                href="statehistory.php"><?php echo _("Run state history report"); ?></a>.</p>

        <h2><a href="topalertproducers.php"><?php echo _("Top Alert Producers"); ?></a></h2>

        <p><?php echo _("Provides a report of hosts and services that experienced the most number of problems or events over a given time frame."); ?>
            <a href="topalertproducers.php"><?php echo _("Run top alert producers report"); ?></a>.</p>

        <h2><a href="histogram.php"><?php echo _("Alert Histogram"); ?></a></h2>

        <p><?php echo _("Provides a report of host and service alerts grouped by time units.  Useful for spotting when problems occur most often."); ?>
            <a href="histogram.php"><?php echo _("Run alert histogram report"); ?></a>.</p>

        <h2><a href="alertheatmap.php"><?php echo _("Alert Heatmap"); ?></a></h2>

        <p><?php echo _("Provides a visual heatmap image of host and service alerts over time.  Useful for spotting when problems occur most often."); ?>
            <a href="alertheatmap.php"><?php echo _("Run alert heatmap report"); ?></a>.</p>

        <h2><a href="notifications.php"><?php echo _("Notifications"); ?></a></h2>

        <p><?php echo _("Provides a historical report of notifications that have been sent out over time."); ?>
            <a href="notifications.php"><?php echo _("Run notifications report"); ?></a>.</p>

        <h2><a href="eventlog.php"><?php echo _("Event Log"); ?></a></h2>

        <p><?php echo _("Provides a detailed log of events captured by the Nagios Core monitoring engine.  Useful for debugging problems."); ?>
            <a href="eventlog.php"><?php echo _("Run event log report"); ?></a>.</p>


    </div>

    <?php
    do_page_end(true);
}

?>

