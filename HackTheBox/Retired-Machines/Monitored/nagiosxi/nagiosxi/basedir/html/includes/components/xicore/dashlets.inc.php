<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/../../utils-dashlets.inc.php');

include_once(dirname(__FILE__) . '/dashlets-comments.inc.php');
include_once(dirname(__FILE__) . '/dashlets-monitoringengine.inc.php');
include_once(dirname(__FILE__) . '/dashlets-perfdata.inc.php');
include_once(dirname(__FILE__) . '/dashlets-status.inc.php');
include_once(dirname(__FILE__) . '/dashlets-sysstat.inc.php');
include_once(dirname(__FILE__) . '/dashlets-tac.inc.php');
include_once(dirname(__FILE__) . '/dashlets-tasks.inc.php');
include_once(dirname(__FILE__) . '/dashlets-misc.inc.php');

init_xicore_dashlets();


////////////////////////////////////////////////////////////////////////
// CORE DASHLET INITIALIZATION
////////////////////////////////////////////////////////////////////////


/**
 * Initializes all core dashlets
 */
function init_xicore_dashlets()
{
    // Stuff that's common to all core dashlets
    $args = array(
        DASHLET_AUTHOR => "Nagios Enterprises, LLC",
        DASHLET_COPYRIGHT => "Dashlet Copyright &copy; 2009-".date('Y')." Nagios Enterprises. All rights reserved.",
        DASHLET_HOMEPAGE => "https://www.nagios.com",
        DASHLET_SHOWASAVAILABLE => true,
    );

    // XI news
    $args[DASHLET_NAME] = "xicore_xi_news_feed";
    $args[DASHLET_TITLE] = sprintf(_("%s News Feed"), get_product_name());
    $args[DASHLET_FUNCTION] = "xicore_dashlet_xi_news_feed";
    $args[DASHLET_DESCRIPTION] = sprintf(_("Shows the latest tutorials, howtos, videos, and news on %s."), get_product_name());
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_INBOARD_CLASS] = "xicore_xi_news_feed_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_xi_news_feed_outboard";
    $args[DASHLET_CLASS] = "xicore_xi_news_feed";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Getting started tasks
    $args[DASHLET_NAME] = "xicore_getting_started";
    $args[DASHLET_TITLE] = _("Getting Started Guide");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_getting_started";
    $args[DASHLET_DESCRIPTION] = sprintf(_("Displays helpful information on getting started with %s."), get_product_name());
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_INBOARD_CLASS] = "xicore_getting_started_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_getting_started_outboard";
    $args[DASHLET_CLASS] = "xicore_getting_started";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Admin tasks
    $args[DASHLET_NAME] = "xicore_admin_tasks";
    $args[DASHLET_TITLE] = _("Administrative Tasks");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_admin_tasks";
    $args[DASHLET_DESCRIPTION] = sprintf(_("Displays tasks that an administrator should take to setup and maintain the %s installation."), get_product_name());
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_INBOARD_CLASS] = "xicore_admin_tasks_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_admin_tasks_outboard";
    $args[DASHLET_CLASS] = "xicore_admin_tasks";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Timed event queue summary - admin page
    $args[DASHLET_NAME] = "xicore_eventqueue_chart";
    $args[DASHLET_TITLE] = _("Monitoring Engine Event Queue");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_eventqueue_chart";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime status of the monitoring engine event queue.");
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_INBOARD_CLASS] = "xicore_eventqueue_chart_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_eventqueue_chart_outboard";
    $args[DASHLET_CLASS] = "xicore_eventqueue_chart";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Component status - admin page
    $args[DASHLET_NAME] = "xicore_component_status";
    $args[DASHLET_TITLE] = _("Core Component Status");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_component_status";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime status of core system components.");
    $args[DASHLET_WIDTH] = "300";
    $args[DASHLET_INBOARD_CLASS] = "xicore_component_status_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_component_status_outboard";
    $args[DASHLET_CLASS] = "xicore_component_status";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Server stats - admin page
    $args[DASHLET_NAME] = "xicore_server_stats";
    $args[DASHLET_TITLE] = _("Server Stats");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_server_stats";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime statistics of the server.");
    $args[DASHLET_WIDTH] = "300";
    $args[DASHLET_INBOARD_CLASS] = "xicore_server_stats_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_server_stats_outboard";
    $args[DASHLET_CLASS] = "xicore_server_stats";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Monitoring engine stats - admin page
    $args[DASHLET_NAME] = "xicore_monitoring_stats";
    $args[DASHLET_TITLE] = _("Monitoring Engine Stats");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_monitoring_stats";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime check statistics of the monitoring engine.");
    $args[DASHLET_WIDTH] = "300";
    $args[DASHLET_INBOARD_CLASS] = "xicore_monitoring_stats_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_monitoring_stats_outboard";
    $args[DASHLET_CLASS] = "xicore_monitoring_stats";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Monitoring engine performance - admin page
    $args[DASHLET_NAME] = "xicore_monitoring_perf";
    $args[DASHLET_TITLE] = _("Monitoring Engine Performance");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_monitoring_perf";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime performance of the monitoring engine.");
    $args[DASHLET_WIDTH] = "300";
    $args[DASHLET_INBOARD_CLASS] = "xicore_monitoring_stats_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_monitoring_stats_outboard";
    $args[DASHLET_CLASS] = "xicore_monitoring_stats";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Monitoring engine process - admin page
    $args[DASHLET_NAME] = "xicore_monitoring_process";
    $args[DASHLET_TITLE] = _("Monitoring Engine Process");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_monitoring_process";
    $args[DASHLET_DESCRIPTION] = _("Displays realtime information of the XI monitoring engine process.");
    $args[DASHLET_WIDTH] = "300";
    $args[DASHLET_INBOARD_CLASS] = "xicore_monitoring_process_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_monitoring_process_outboard";
    $args[DASHLET_CLASS] = "xicore_monitoring_process";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Performance graph chart
    $args[DASHLET_NAME] = "xicore_perfdata_chart";
    $args[DASHLET_TITLE] = _("Performance Graph");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_perfdata_chart";
    $args[DASHLET_DESCRIPTION] = _("Displays a performance data graph for a specific host or service.");
    $args[DASHLET_WIDTH] = "500";
    $args[DASHLET_HEIGHT] = "225";
    $args[DASHLET_INBOARD_CLASS] = "xicore_perfdata_chart_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_perfdata_chart_outboard";
    $args[DASHLET_CLASS] = "xicore_perfdata_chart";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Host status sumary
    $args[DASHLET_NAME] = "xicore_host_status_summary";
    $args[DASHLET_TITLE] = _("Host Status Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_host_status_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays a table with a quick summary of host status.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_host_status_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_host_status_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_host_status_summary";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Service status summary
    $args[DASHLET_NAME] = "xicore_service_status_summary";
    $args[DASHLET_TITLE] = _("Service Status Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_service_status_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays a table with a quick summary of service status.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_service_status_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_service_status_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_service_status_summary";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Comments
    $args[DASHLET_NAME] = "xicore_comments";
    $args[DASHLET_TITLE] = _("Acknowledgements and Comments");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_comments";
    $args[DASHLET_DESCRIPTION] = _("Displays current acknowledgements and comments.");
    $args[DASHLET_WIDTH] = "500";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_comments_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_comments_outboard";
    $args[DASHLET_CLASS] = "xicore_comments";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Hostgroup status overview
    $args[DASHLET_NAME] = "xicore_hostgroup_status_overview";
    $args[DASHLET_TITLE] = _("Hostgroup Status Overview");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_hostgroup_status_overview";
    $args[DASHLET_DESCRIPTION] = _("Displays an overview of host and service status for a particular hostgroup.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_hostgroup_status_overview_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_hostgroup_status_overview_outboard";
    $args[DASHLET_CLASS] = "xicore_hostgroup_status_overview";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Hostgroup status grid
    $args[DASHLET_NAME] = "xicore_hostgroup_status_grid";
    $args[DASHLET_TITLE] = _("Hostgroup Status Grid");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_hostgroup_status_grid";
    $args[DASHLET_DESCRIPTION] = _("Displays a grid of host and service status for a particular hostgroup.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_hostgroup_status_overview_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_hostgroup_status_overview_outboard";
    $args[DASHLET_CLASS] = "xicore_hostgroup_status_overview";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Servicegroup status overview
    $args[DASHLET_NAME] = "xicore_servicegroup_status_overview";
    $args[DASHLET_TITLE] = _("Servicegroup Status Overview");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_servicegroup_status_overview";
    $args[DASHLET_DESCRIPTION] = _("Displays an overview of host and service status for a particular servicegroup.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_servicegroup_status_overview_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_servicegroup_status_overview_outboard";
    $args[DASHLET_CLASS] = "xicore_servicegroup_status_overview";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Servicegroup status grid
    $args[DASHLET_NAME] = "xicore_servicegroup_status_grid";
    $args[DASHLET_TITLE] = _("Servicegroup Status Grid");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_servicegroup_status_grid";
    $args[DASHLET_DESCRIPTION] = _("Displays a grid of host and service status for a particular servicegroup.");
    $args[DASHLET_WIDTH] = "250";
    $args[DASHLET_HEIGHT] = "125";
    $args[DASHLET_INBOARD_CLASS] = "xicore_servicegroup_status_grid_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_servicegroup_status_grid_outboard";
    $args[DASHLET_CLASS] = "xicore_servicegroup_status_grid";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Hostgroup status summary
    $args[DASHLET_NAME] = "xicore_hostgroup_status_summary";
    $args[DASHLET_TITLE] = _("Hostgroup Status Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_hostgroup_status_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays a summary of host and service status for all hostgroups.");
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_hostgroup_status_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_hostgroup_status_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_hostgroup_status_summary";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Servicegroup status summary
    $args[DASHLET_NAME] = "xicore_servicegroup_status_summary";
    $args[DASHLET_TITLE] = _("Servicegroup Status Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_servicegroup_status_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays a summary of host and service status for all servicegroups.");
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_servicegroup_status_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_servicegroup_status_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_servicegroup_status_summary";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Available updates
    $args[DASHLET_NAME] = "xicore_available_updates";
    $args[DASHLET_TITLE] = _("Available Updates");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_available_updates";
    $args[DASHLET_DESCRIPTION] = sprintf(_("Displays the status of available updates for your %s installation."), get_product_name());
    $args[DASHLET_WIDTH] = "350";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_available_updates_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_available_updates_outboard";
    $args[DASHLET_CLASS] = "xicore_available_updates";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Network outages
    $args[DASHLET_NAME] = "xicore_network_outages";
    $args[DASHLET_TITLE] = _("Network Outages");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_network_outages";
    $args[DASHLET_DESCRIPTION] = _("Displays blocking network outages.");
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_network_outages_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_network_outages_outboard";
    $args[DASHLET_CLASS] = "xicore_network_outages";
    $args[DASHLET_SHOWASAVAILABLE] = true;
    register_dashlet($args[DASHLET_NAME], $args);

    // Network outages
    $args[DASHLET_NAME] = "xicore_network_outages_summary";
    $args[DASHLET_TITLE] = _("Network Outages Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_network_outages_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays summary of network outages.");
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_network_outages_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_network_outages_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_network_outages_summary";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Network health
    $args[DASHLET_NAME] = "xicore_network_health";
    $args[DASHLET_TITLE] = _("Network Health");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_network_health";
    $args[DASHLET_DESCRIPTION] = _("Displays summary of network health.");
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_network_health_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_network_health_outboard";
    $args[DASHLET_CLASS] = "xicore_network_health";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Host status tac summary
    $args[DASHLET_NAME] = "xicore_host_status_tac_summary";
    $args[DASHLET_TITLE] = _("Host Status TAC Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_host_status_tac_summary";
    $args[DASHLET_DESCRIPTION] = "Displays summary of host status.";
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_host_status_tac_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_host_status_tac_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_host_status_tac_summary";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Service status tac summary
    $args[DASHLET_NAME] = "xicore_service_status_tac_summary";
    $args[DASHLET_TITLE] = _("Service Status TAC Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_service_status_tac_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays summary of service status.");
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_service_status_tac_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_service_status_tac_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_service_status_tac_summary";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);

    // Feature status tac summary
    $args[DASHLET_NAME] = "xicore_feature_status_tac_summary";
    $args[DASHLET_TITLE] = _("Feature Status TAC Summary");
    $args[DASHLET_FUNCTION] = "xicore_dashlet_feature_status_tac_summary";
    $args[DASHLET_DESCRIPTION] = _("Displays summary of feature status.");
    $args[DASHLET_WIDTH] = "450";
    $args[DASHLET_HEIGHT] = "250";
    $args[DASHLET_INBOARD_CLASS] = "xicore_feature_status_tac_summary_inboard";
    $args[DASHLET_OUTBOARD_CLASS] = "xicore_feature_status_tac_summary_outboard";
    $args[DASHLET_CLASS] = "xicore_feature_status_tac_summary";
    $args[DASHLET_SHOWASAVAILABLE] = false;
    register_dashlet($args[DASHLET_NAME], $args);
}
