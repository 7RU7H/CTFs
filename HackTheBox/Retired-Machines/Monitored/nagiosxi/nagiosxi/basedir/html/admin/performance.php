<?php
//
// Performance Settings
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
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
    global $request;

    if (isset($request['update'])) {
        do_update_options();
    } else {
        show_options();
    }
}


function get_dashlet_friendly_name($id)
{
    $dashlet_friendly_names = array(
        "available_updates" => _("Available Updates"),
        "systat_eventqueuechart" => _("Monitoring Engine Event Queue Chart"),
        "sysstat_monitoringstats" => _("Monitoring Engine Check Statistics"),
        "systat_monitoringperf" => _("Monitoring Engine Performance"),
        "sysstat_monitoringproc" => _("Monitoring Engine Process"),
        "perfdata_chart" => _("Performance Graphs"),
        "network_outages" => _("Network Outages"),
        "host_status_summary" => _("Host Status Summary"),
        "service_status_summary" => _("Service Status Summary"),
        "hostgroup_status_overview" => _("Hostgroup Status Overview"),
        "hostgroup_status_grid" => _("Hostgroup Status Grid"),
        "servicegroup_status_overview" => _("Servicegroup Status Overview"),
        "servicegroup_status_grid" => _("Servicegroup Status Grid"),
        "hostgroup_status_summary" => _("Hostgroup Status Summary"),
        "servicegroup_status_summary" => _("Servicegroup Status Summary"),
        "sysstat_componentstates" => _("Component Status"),
        "sysstat_serverstats" => _("Server Statistics"),
        "network_outages_summary" => _("Network Outages Summary"),
        "network_health" => _("Network Health"),
        "host_status_tac_summary" => _("Host Status TAC Summary"),
        "service_status_tac_summary" => _("Service Status TAC Summary"),
        "feature_status_tac_summary" => _("Feature Status Tac Summary"),
        "admin_tasks" => _("Administrative Tasks"),
        "getting_started" => _("Getting Started"),
        "pagetop_alert_content" => _("Page Top Alert Content"),
        "tray_alert" => _("Tray Alert Content"),
        "legacy_statusmap" => _("Legacy Network Status Map"),
        "host_status_table" => _("Host Status Table"),
        "host_status_state_summary" => _("Host Status State Summary"),
        "host_state_info" => _("Host State Info"),
        "host_state_quick_actions" => _("Host State Quick Actions"),
        "host_comments" => _("Host Comments"),
        "host_service_statuses" => _("Host Service Statuses"),
        "host_detailed_state_info" => _("Host Detailed State Info"),
        "host_status_attrs" => _("Host Advanced Status Attributes"),
        "service_status_table" => _("Service Status Table"),
        "service_status_state_summary" => _("Service Status State Summary"),
        "service_state_info" => _("Service State Info"),
        "service_state_quick_actions" => _("Service State Quick Actions"),
        "service_comments" => _("Service Comments"),
        "service_detailed_state_info" => _("Service Detailed State Info"),
        "service_status_attrs" => _("Service Advanced Status Attributes"),
    );

    $name = grab_array_var($dashlet_friendly_names, $id, $id);
    return $name;
}


function show_options($error = false, $msg = "")
{

    $dashlet_refresh_rates = array(
        "available_updates" => 24 * 60 * 60,
        "systat_eventqueuechart" => 5,
        "sysstat_monitoringstats" => 30,
        "systat_monitoringperf" => 30,
        "sysstat_monitoringproc" => 30,
        "perfdata_chart" => 60,
        "network_outages" => 30,
        "host_status_summary" => 60,
        "service_status_summary" => 60,
        "hostgroup_status_overview" => 60,
        "hostgroup_status_grid" => 60,
        "servicegroup_status_overview" => 60,
        "servicegroup_status_grid" => 60,
        "hostgroup_status_summary" => 60,
        "servicegroup_status_summary" => 60,
        "sysstat_componentstates" => 7,
        "sysstat_serverstats" => 5,
        "network_outages_summary" => 30,
        "network_health" => 30,
        "host_status_tac_summary" => 30,
        "service_status_tac_summary" => 30,
        "feature_status_tac_summary" => 30,
        "admin_tasks" => 60,
        "getting_started" => 60,
        "pagetop_alert_content" => 30,
        "legacy_statusmap" => 30,
        "host_status_table" => 30,
        "host_status_state_summary" => 7,
        "host_state_info" => 7,
        "host_state_quick_actions" => 10,
        "host_comments" => 10,
        "host_service_statuses" => 10,
        "host_detailed_state_info" => 10,
        "host_status_attrs" => 10,
        "service_status_table" => 30,
        "service_status_state_summary" => 7,
        "service_state_info" => 7,
        "service_state_quick_actions" => 10,
        "service_comments" => 10,
        "service_detailed_state_info" => 10,
        "service_status_attrs" => 10
    );

    // Get saved defaults
    foreach ($dashlet_refresh_rates as $rid => $rate) {
        $dashlet_refresh_rates[$rid] = get_dashlet_refresh_rate($rate, $rid, true);
    }

    // Get options
    $dashlet_refresh_multiplier = grab_request_var("dashlet_refresh_multiplier", get_dashlet_refresh_multiplier($multiplier = 1000));
    $default_dashlet_rate = grab_request_var("default_dashlet_rate", 60);
    $dashlet_refresh_rates = grab_request_var("dashlet_refresh_rates", $dashlet_refresh_rates);

    // Unified views
    $use_unified_tac_overview = checkbox_binary(grab_request_var("use_unified_tac_overview", get_option("use_unified_tac_overview")));
    $use_unified_hostgroup_screens = checkbox_binary(grab_request_var("use_unified_hostgroup_screens", get_option("use_unified_hostgroup_screens")));
    $use_unified_servicegroup_screens = checkbox_binary(grab_request_var("use_unified_servicegroup_screens", get_option("use_unified_servicegroup_screens")));

    // Nagios XI / PostgresQL
    $nagiosxi_db_max_commands_age = grab_request_var("nagiosxi_db_max_commands_age", get_database_interval("nagiosxi", "max_commands_age", 480));
    $nagiosxi_db_max_events_age = grab_request_var("nagiosxi_db_max_events_age", get_database_interval("nagiosxi", "max_events_age", 480));
    $nagiosxi_db_optimize_interval = grab_request_var("nagiosxi_db_optimize_interval", get_database_interval("nagiosxi", "optimize_interval", 60));
    $nagiosxi_db_nxti_age = grab_request_var("nagiosxi_db_nxti_age", get_database_interval("nagiosxi", "max_nxti_age", 90));
    $nagiosxi_db_max_scheduledreports_log_age = grab_request_var("nagiosxi_db_max_scheduledreports_log_age", get_database_interval("nagiosxi", "max_scheduledreports_log_age", 365));
    $nagiosxi_db_auth_token_age = grab_request_var("nagiosxi_db_auth_token_age", get_database_interval("nagiosxi", "max_auth_token_age", 24));
    $nagiosxi_db_auth_session_age = grab_request_var("nagiosxi_db_auth_session_age", get_database_interval("nagiosxi", "max_auth_session_age", 24));
    $nagiosxi_db_max_auditlog_age = grab_request_var("nagiosxi_db_max_auditlog_age", get_database_interval("nagiosxi", "max_auditlog_age", 180));

    // Ndoutils
    $ndoutils_db_max_externalcommands_age = grab_request_var("ndoutils_db_max_externalcommands_age", get_database_interval("ndoutils", "max_externalcommands_age", 7));
    $ndoutils_db_max_logentries_age = grab_request_var("ndoutils_db_max_logentries_age", get_database_interval("ndoutils", "max_logentries_age", 90));
    $ndoutils_db_max_notifications_age = grab_request_var("ndoutils_db_max_notifications_age", get_database_interval("ndoutils", "max_notifications_age", 90));
    $ndoutils_db_max_statehistory_age = grab_request_var("ndoutils_db_max_statehistory_age", get_database_interval("ndoutils", "max_statehistory_age", 730));
    $ndoutils_db_max_timedevents_age = grab_request_var("ndoutils_db_max_timedevents_age", get_database_interval("ndoutils", "max_timedevents_age", 5));
    $ndoutils_db_max_systemcommands_age = grab_request_var("ndoutils_db_max_systemcommands_age", get_database_interval("ndoutils", "max_systemcommands_age", 5));
    $ndoutils_db_max_servicechecks_age = grab_request_var("ndoutils_db_max_servicechecks_age", get_database_interval("ndoutils", "max_servicechecks_age", 5));
    $ndoutils_db_max_hostchecks_age = grab_request_var("ndoutils_db_max_hostchecks_age", get_database_interval("ndoutils", "max_hostchecks_age", 5));
    $ndoutils_db_max_eventhandlers_age = grab_request_var("ndoutils_db_max_eventhandlers_age", get_database_interval("ndoutils", "max_eventhandlers_age", 5));
    $ndoutils_db_max_commenthistory_age = grab_request_var("ndoutils_db_max_commenthistory_age", get_database_interval("ndoutils", "max_commenthistory_age", 730));
    $ndoutils_db_optimize_interval = grab_request_var("ndoutils_db_optimize_interval", get_database_interval("ndoutils", "optimize_interval", 60));

    // Subsystem
    $enable_outbound_data_transfer = checkbox_binary(grab_request_var("enable_outbound_data_transfer", get_option("enable_outbound_data_transfer")));
    $enable_subsystem_logging = checkbox_binary(grab_request_var("enable_subsystem_logging", get_option('enable_subsystem_logging', 1)));
    $enable_unconfigured_objects = checkbox_binary(grab_request_var("enable_unconfigured_objects", get_option('enable_unconfigured_objects', 1)));
    $bpi_sync_on_apply_config = checkbox_binary(grab_request_var("bpi_sync_on_apply_config", get_option("bpi_sync_on_apply_config", 1)));
    $bpi_api_tool_ndo_timeout = grab_request_var("bpi_api_tool_ndo_timeout", get_option("bpi_api_tool_ndo_timeout", 120));

    // No longer used
    $nagiosxi_db_repair_interval = grab_request_var("nagiosxi_db_repair_interval", get_database_interval("nagiosxi", "repair_interval", 0));
    $ndoutils_db_repair_interval = grab_request_var("ndoutils_db_repair_interval", get_database_interval("ndoutils", "repair_interval", 0));
    $nagiosql_db_max_logbook_age = grab_request_var("nagiosql_db_max_logbook_age", get_database_interval("nagiosql", "max_logbook_age", 480));
    $nagiosql_db_optimize_interval = grab_request_var("nagiosql_db_optimize_interval", get_database_interval("nagiosql", "optimize_interval", 60));
    $nagiosql_db_repair_interval = grab_request_var("nagiosql_db_repair_interval", get_database_interval("nagiosql", "repair_interval", 0));

    // Auto-run page settings
    $disable_report_auto_run = grab_request_var("disable_report_auto_run", get_option("disable_report_auto_run"));
    $disable_metrics_auto_run = grab_request_var("disable_metrics_auto_run", get_option("disable_metrics_auto_run"));

    // Backend Cache settings
    $enable_backend_cache = grab_request_var('enable_backend_cache', get_option('enable_backend_cache', 0));
    $location_backend_cache = grab_request_var('location_backend_cache', get_option('location_backend_cache', '/usr/local/nagiosxi/tmp/backendcache'));
    $ttl_backend_cache = grab_request_var('ttl_backend_cache', get_option('ttl_backend_cache', 300));
    $enable_perfmon_backend_cache = grab_request_var('enable_perfmon_backend_cache', get_option('enable_perfmon_backend_cache', 0));

    // Snapshot settings
    $num_snapshots = grab_request_var('num_snapshots', get_option('num_snapshots', 10));
    $num_error_snapshots = grab_request_var('num_error_snapshots', get_option('num_error_snapshots', 10));
    $num_ccm_snapshots = grab_request_var('num_ccm_snapshots', get_option('num_ccm_snapshots', 10));

    // Delay for PDF/JPG generation
    $report_export_delay = grab_request_var('report_export_delay', get_option('report_export_delay', 300));
    $report_export_cp_delay = grab_request_var('report_export_cp_delay', get_option('report_export_cp_delay', 600));
    $old_browser_compat_jquery1 = grab_request_var('old_browser_compat_jquery1', get_option('old_browser_compat_jquery1', 1));

    // Status page waiting time
    $status_object_retries = intval(grab_request_var('status_object_retries', get_option('status_object_retries', 6)));

    do_page_start(array("page_title" => _('Performance Settings')), true);
?>

    <h1><?php echo _('Performance Settings'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <form id="manageOptionsForm" method="post">

        <input type="hidden" name="options" value="1">
        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="update" value="1">
        <input type="hidden" value="general" id="tab_hash" name="tab_hash">

        <script type="text/javascript">
        $(function () {
            $("#tabs").tabs().show();
        });
        </script>

        <div id="tabs" class="hide">
            <ul>
                <li><a href="#tab-pages"><?php echo _("Pages"); ?></a></li>
                <li><a href="#tab-dashlets"><?php echo _("Dashlets"); ?></a></li>
                <li><a href="#tab-database"><?php echo _("Databases"); ?></a></li>
                <li><a href="#tab-subsystem"><?php echo _("Subsystem"); ?></a></li>
                <li><a href="#tab-auto"><?php echo _("Auto-Running"); ?></a></li>
                <li><a href="#tab-caching"><?php echo _("Backend Cache"); ?></a></li>
                <li><a href="#tab-snapshots"><?php echo _("Snapshots"); ?></a></li>
                <li><a href="#tab-pdf"><?php echo _("PDF/JPG Exporting"); ?></a></li>
            </ul>

            <div id="tab-pages">
                
                <p><?php echo sprintf(_("These options allow you to select which pages are used in the %s web interface."), get_product_name()); ?></p>
                
                <p><?php echo _("Non-unified pages provide dynamic updates via Ajax and let users add certain sections of the page to their dashboards, but incur a higher performance hit than unified pages.  Unified pages offer higher performance than non-unified pages, but do not offer users the ability to add portions of the page to their dashboards."); ?></p>

                <h5 class="ul"><?php echo _("Page Settings"); ?></h5>

                <table class="table table-no-border table-condensed table-auto-width">
                    <tr>
                        <td></td>
                        <td class="checkbox">
                            <label>
                                <input type="checkbox" class="checkbox" id="use_unified_tac_overviewCheckBox" name="use_unified_tac_overview" <?php echo is_checked($use_unified_tac_overview, 1); ?>> <?php echo _("Use Unified Tactical Overview"); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="checkbox">
                            <label>
                                <input type="checkbox" class="checkbox" id="use_unified_hostgroup_screensCheckBox" name="use_unified_hostgroup_screens" <?php echo is_checked($use_unified_hostgroup_screens, 1); ?>> <?php echo _("Use Unified Hostgroup Screens"); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="checkbox">
                            <label>
                                <input type="checkbox" class="checkbox" id="use_unified_servicegroup_screensCheckBox" name="use_unified_servicegroup_screens" <?php echo is_checked($use_unified_servicegroup_screens, 1); ?>> <?php echo _("Use Unified Servicegroup Screens"); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <h5 class="ul"><?php echo _("Status Page Settings"); ?></h5>

                <table class="table table-no-border table-condensed table-auto-width">
                    <tr>
                        <td class="vt">
                            <label for="status_object_retries"><?php echo _('Object status retries'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="3" name="status_object_retries" id="status_object_retries" value="<?php echo encode_form_val($status_object_retries); ?>" class="textfield form-control" style="margin-right: 5px;">
                            <?php echo _("Number of times to retry before showing object does not exist message in host/service status pages. One retry per second."); ?>
                        </td>
                    <tr>
                </table>

            </div>

            <div id="tab-dashlets">

                <h5 class="ul"><?php echo _("Global Dashlet Settings"); ?></h5>

                <table class="table table-no-border table-condensed table-auto-width">
                    <tr>
                        <td class="vt">
                            <label for="dashlet_refresh_multiplierBox"><?php echo _('Dashlet Refresh Multiplier'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="5" name="dashlet_refresh_multiplier" id="dashlet_refresh_multiplierBox" value="<?php echo encode_form_val($dashlet_refresh_multiplier); ?>" class="textfield form-control" style="margin-right: 5px;">
                            <?php echo _("Number of milliseconds to multiply individual dashlet refresh rates by. Defaults to 1000 (1 second)."); ?>
                        </td>
                    <tr>
                </table>

                <h5 class="ul"><?php echo _("Dashlet Refresh Rates"); ?></h5>

                <p><?php echo _("Number of time units (usually seconds) between dashlet refreshes.  Lower numbers increase system load, while higher numbers decrease load.  Refresh rates specified below are multiplied by the refresh multiplier specified above."); ?></p>

                <table class="table table-no-border table-condensed table-auto-width">
                    <?php
                    ksort($dashlet_refresh_rates);
                    foreach ($dashlet_refresh_rates as $rid => $rate) {
                    ?>
                    <tr>
                        <td class="vt">
                            <label><?php echo get_dashlet_friendly_name($rid); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="5" name="dashlet_refresh_rates[<?php echo encode_form_val($rid); ?>]" value="<?php echo encode_form_val($dashlet_refresh_rates[$rid]); ?>" class="textfield form-control">
                        </td>
                    <tr>
                    <?php } ?>
                </table>

            </div>

            <div id="tab-database">

            <p><?php echo sprintf(_("These options allow you to specify data retention, and optimization intervals for the databases %s uses."), get_product_name()); ?></p>

            <h5 class="ul"><?php echo sprintf(_("%s Database"), get_product_name()); ?></h5>

            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Commands Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_max_commands_age" value="<?php echo encode_form_val($nagiosxi_db_max_commands_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep commands"); ?>.
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Events Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_max_events_age" value="<?php echo encode_form_val($nagiosxi_db_max_events_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep events"); ?>.
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max SNMP Trap Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_nxti_age" value="<?php echo encode_form_val($nagiosxi_db_nxti_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to store SNMP trap data in the database."); ?>
                    </td>
                <tr>
                    <tr>
                    <td class="vt">
                        <label><?php echo _("Max Scheduled Reports History Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_max_scheduledreports_log_age" value="<?php echo encode_form_val($nagiosxi_db_max_scheduledreports_log_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to store scheduled report log data."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Expired Auth Token Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_auth_token_age" value="<?php echo encode_form_val($nagiosxi_db_auth_token_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in HOURS to store expired auth tokens in the database."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Expired Session Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_auth_session_age" value="<?php echo encode_form_val($nagiosxi_db_auth_session_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in HOURS to store expired (no longer active) session data in the database."); ?>
                    </td>
                <tr>
                    <tr>
                    <td class="vt">
                        <label><?php echo _("Max Audit Log Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_max_auditlog_age" value="<?php echo encode_form_val($nagiosxi_db_max_auditlog_age); ?>" class="textfield form-control">
                        <?php echo _("Max to keep audit log entires in days"); ?>.
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Optimize Interval"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosxi_db_optimize_interval" value="<?php echo encode_form_val($nagiosxi_db_optimize_interval); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes between optimization runs."); ?>
                    </td>
                <tr>
            </table>

            <h5 class="ul"><?php echo _("NDO Database"); ?></h5>

            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max External Commands Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_externalcommands_age" value="<?php echo encode_form_val($ndoutils_db_max_externalcommands_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to keep external commands."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Log Entries Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_logentries_age" value="<?php echo encode_form_val($ndoutils_db_max_logentries_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to keep log entries."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Notifications Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_notifications_age" value="<?php echo encode_form_val($ndoutils_db_max_notifications_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to keep notifications."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max State History Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_statehistory_age" value="<?php echo encode_form_val($ndoutils_db_max_statehistory_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to keep state history information."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Timed Events Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_timedevents_age" value="<?php echo encode_form_val($ndoutils_db_max_timedevents_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep timed events"); ?>.
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max System Commands Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_systemcommands_age" value="<?php echo encode_form_val($ndoutils_db_max_systemcommands_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep  system commands."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Service Checks Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_servicechecks_age" value="<?php echo encode_form_val($ndoutils_db_max_servicechecks_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep service checks."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Host Checks Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_hostchecks_age" value="<?php echo encode_form_val($ndoutils_db_max_hostchecks_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep host checks."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Event Handlers Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_eventhandlers_age" value="<?php echo encode_form_val($ndoutils_db_max_eventhandlers_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep event handlers."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Comment History Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_max_commenthistory_age" value="<?php echo encode_form_val($ndoutils_db_max_commenthistory_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in DAYS to keep comment history."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Optimize Interval"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ndoutils_db_optimize_interval" value="<?php echo encode_form_val($ndoutils_db_optimize_interval); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes between optimization runs."); ?>
                    </td>
                <tr>
            </table>

            <h5 class="ul"><?php echo _("CCM Database"); ?></h5>

            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td class="vt">
                        <label><?php echo _("Max Logbook Age"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosql_db_max_logbook_age" value="<?php echo encode_form_val($nagiosql_db_max_logbook_age); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes to keep logbook records."); ?>
                    </td>
                <tr>
                <tr>
                    <td class="vt">
                        <label><?php echo _("Optimize Interval"); ?>:</label>
                    </td>
                    <td>
                        <input type="text" size="5" name="nagiosql_db_optimize_interval" value="<?php echo encode_form_val($nagiosql_db_optimize_interval); ?>" class="textfield form-control">
                        <?php echo _("Max time in minutes between optimization runs."); ?>
                    </td>
                <tr>
            </table>

        </div>

        <!-- Subsystem page -->
        <div id="tab-subsystem">

            <p><?php echo sprintf(_("These options allow you to enable/disable certain ongoing subsystem processes of %s."), get_product_name()); ?></p>

            <p>
                <?php echo _("Disabling Outbound Data Transfers and listening for Unconfigured Objects will result in a slight decrease in CPU usage, and disabling subsystem logging will reduce disk activity for subsystem processes."); ?>
                <br/><strong><?php echo _("NOTE"); ?>: </strong>
                <?php echo _("Disabling Outbound Transfers will stop any currently defined outbound transfers.  Outbound settings can be viewed"); ?>
                <a href='dtoutbound.php' title="Outbound Transfers"><?php echo _("here"); ?></a>.
            </p>

            <h5 class="ul"><?php echo _("Subsystem Options"); ?></h5>

            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" class="checkbox" id="enable_outbound_data_transfer" name="enable_outbound_data_transfer" <?php echo is_checked($enable_outbound_data_transfer, 1); ?>> <?php echo _("Enable Outbound Data Transfers"); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" class="checkbox" id="enable_unconfigured_objects" name="enable_unconfigured_objects" <?php echo is_checked($enable_unconfigured_objects, 1); ?>> <?php echo _("Enable Listener For Unconfigured Objects"); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" class="checkbox" id="enable_subsystem_logging" name="enable_subsystem_logging" <?php echo is_checked($enable_subsystem_logging, 1); ?>> <?php echo _("Enable Subsystem Logging"); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" class="checkbox" id="bpi_sync_on_apply_config" name="bpi_sync_on_apply_config" <?php echo is_checked($bpi_sync_on_apply_config, 1); ?>> <?php echo _("Enable BPI sync on Apply Config"); ?> (<?php echo _('this option can also be changed in the'); ?> <a href="components.php?config=nagiosbpi"><?php echo _('BPI settings page') ?></a>)
                        </label>
                    </td>
                </tr>
            </table>

            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td><label for="bpi_api_tool_ndo_timeout"><?php echo _("BPI Sync NDO Startup Timeout"); ?>:</label></td>
                    <td class="checkbox">
                        <label>
                            <input type="text" size="5" name="bpi_api_tool_ndo_timeout" id="bpi_api_tool_ndo_timeout" value="<?php echo encode_form_val($bpi_api_tool_ndo_timeout); ?>" class="textfield form-control">
                            &nbsp; <?php echo _('Amount of seconds to wait for NDO to startup when syncing BPI after apply config.'); ?>
                        </label>
                    </td>
                </tr>
            </table>

        </div>

        <!-- Auto-Running Settings Page -->
        <div id="tab-auto">
        
            <p><?php echo _("Edit performance settings for auto-running pages such as reports, metrics, etc"); ?>.</p>

            <h5 class="ul"><?php echo _("Auto-running Page Performance Options"); ?></h5>
        
            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input id="report-auto-run" value="1" name="disable_report_auto_run" type="checkbox" <?php echo is_checked($disable_report_auto_run, 1); ?>> <?php echo _("Disable reports from automatically running on page load"); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input id="metrics-auto-run" value="1" name="disable_metrics_auto_run" type="checkbox" <?php echo is_checked($disable_metrics_auto_run, 1); ?>> <?php echo _("Disable metrics from loading on page load"); ?>
                        </label>
                    </td>
                </tr>
            </table>

        </div>

        <!-- Backend Caching Settings Page -->
        <div id="tab-caching">

            <p><?php echo _("Edit settings relating to the Backend Cache. This is used to cache some of the calls to the database."); ?></p>
            <div class="alert alert-info" style="font-weight: bold;">
                <?php echo sprintf(_("Enabling this feature will result in non-realtime data. If you need data displayed in realtime, then %sDO NOT%s enable this feature."), "<u>", "</u>"); ?>
            </div>
            <ul>
                <li>
                    <?php echo _("This can considerably improve performance on systems that perform a lot of host and/or service checks."); ?>
                </li><li>
                    <?php echo sprintf(_("Enabling this feature on systems with a lower amount of checks (%s 1,000) will be detrimental to performance and is %snot recommended%s."), "&lt;", "<u><strong>", "</strong></u>"); ?>
                </li><li>
                    <?php echo sprintf(_("Enabling this feature on systems that add or remove hosts and/or services frequently is %snot recommended%s."), "<u><strong>", "</strong></u>"); ?>
                </li>
            </ul>

            <h5 class="ul"><?php echo _("Backend Cache Settings"); ?></h5>

            <table class="table table-no-border table-condensed table-auto-width">

                <tr>
                    <td class="vt">
                        <label for="enable_backend_cache">
                            <?php echo _("Enable Backend Cache"); ?>
                        </label>
                    </td>
                    <td>
                        <input id="enable_backend_cache" value="1" name="enable_backend_cache" type="checkbox" <?php echo is_checked($enable_backend_cache, 1); ?>>
                    </td>
                </tr>

                <tr>
                    <td class="vt">
                        <label for="location_backend_cache">
                            <?php echo _("Backend Cache Location"); ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" size="40" name="location_backend_cache" value="<?php echo encode_form_val($location_backend_cache); ?>" class="textfield form-control">
                        <br />
                        <?php echo _("Change the location of the backend cache, just make sure that the new location has write permissions for the apache user."); ?>
                    </td>
                </tr>

                <tr>
                    <td class="vt">
                        <label for="ttl_backend_cache">
                            <?php echo _("Backend Cache Expiration Time"); ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" size="5" name="ttl_backend_cache" value="<?php echo encode_form_val($ttl_backend_cache); ?>" class="textfield form-control">
                        <br />
                        <?php echo _("How long will a particular cache exist? Measured in seconds."); ?>
                    </td>
                </tr>

                <?php if (is_dev_mode()) { ?>

                    <tr>
                        <td class="vt">
                            <label for="enable_perfmon_backend_cache">
                                <?php echo _("Backend Cache Performance Monitoring"); ?>
                            </label>
                        </td>
                        <td>
                            <input id="enable_perfmon_backend_cache" value="1" name="enable_perfmon_backend_cache" type="checkbox" <?php echo is_checked($enable_perfmon_backend_cache, 1); ?>>
                            <br />
                            <?php echo _("This option only shows up in Developer Mode. Enabling will output some basic microtime() timestamps to the xidebug.log file."); ?>
                        </td>
                    </tr>

                <?php } ?>

            </table>

            <h5 class="ul"><?php echo _("Maximum Backend Cache Performance"); ?></h5>

            <p><?php echo _("In order to take full advantage of the Backend Cache, you might want to ensure that your Backend Cache Location is writing to RAM instead of disk."); ?></p>
            <p>
                <?php echo sprintf(_("To create a 512 MB RAM Disk to hold the cache data, console in to your %s server as root, and execute the following command:"), get_product_name()); ?>
                <pre>mount -t tmpfs -o size=512m tmpfs <?php echo encode_form_val($location_backend_cache); ?></pre>
            </p>
        </div>

        <div id="tab-snapshots">
            <p><?php echo _("Select the amount of different kinds of snapshots to keep. You must keep at least the last one good CCM and Core snapshot."); ?></p>
            <table class="table table-no-border table-condensed table-auto-width">
                <tr>
                    <td class="vt"><?php echo _("Number of Core Snapshots"); ?></td>
                    <td><input name="num_snapshots" class="form-control" size="5" value="<?php echo intval($num_snapshots); ?>"></td>
                </tr>
                <tr>
                    <td class="vt"><?php echo _("Number of Error Snapshots"); ?></td>
                    <td><input name="num_error_snapshots" class="form-control" size="5" value="<?php echo intval($num_error_snapshots); ?>"></td>
                </tr>
                <tr>
                    <td class="vt"><?php echo _("Number of CCM Snapshots"); ?></td>
                    <td><input name="num_ccm_snapshots" class="form-control" size="5" value="<?php echo intval($num_ccm_snapshots); ?>"> &nbsp; (<?php echo _("This must be equal or greater than number of Core snapshots"); ?>)</td>
                </tr>
            </table>
        </div>

        <div id="tab-pdf">
            <p><?php echo _("These are advanced options for PDF/JPG backend. If your PDFs are not always properly displaying the graphs, increase the delay options below."); ?></p>
            <table class="table table-no-border table-condensed table-auto-width" style="margin-top: 10px;">
                <tr>
                    <td class="vt"><?php echo _("Default Delay"); ?></td>
                    <td><input name="report_export_delay" class="form-control" size="5" value="<?php echo intval($report_export_delay); ?>"> &nbsp; <?php echo _('Number of milliseconds'); ?></td>
                </tr>
                <tr>
                    <td class="vt"><?php echo _("Capacity Planning Delay"); ?></td>
                    <td><input name="report_export_cp_delay" class="form-control" size="5" value="<?php echo intval($report_export_cp_delay); ?>"> &nbsp; <?php echo _('Number of milliseconds'); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" name="old_browser_compat_jquery1" <?php echo is_checked($old_browser_compat_jquery1, 1); ?> value="1">
                            <?php echo _('Use patched jQuery 1.x to render PDF/JPG reports'); ?>
                        </label>
                        <div class="subtext">
                            <?php echo _("The jQuery provided has been patched for CVE-2019-11358 but if you would prefer to use the latest jQuery uncheck this box."); ?>
                            <br>
                            <?php echo _("Unchecking this may cause graphs to timeout and not display in PDF/JPG and may require changing the delay options above for them to load."); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div id="formButtons">
        <button type="submit" class="submitbutton btn btn-sm btn-primary" name="updateButton" id="updateButton"><?php echo _('Update Settings'); ?></button>
        <button type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton" id="cancelButton"><?php echo _('Cancel'); ?></button>
    </div>

    </form>

<?php
    do_page_end(true);
    exit();
}


function do_update_options()
{
    global $request;

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: main.php");
		return;
    }

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Get values
    $dashlet_refresh_multiplier = grab_request_var("dashlet_refresh_multiplier", get_dashlet_refresh_multiplier($multiplier = 1000));
    $dashlet_refresh_rates = grab_request_var("dashlet_refresh_rates", array());

    // Unified views
    $use_unified_tac_overview = checkbox_binary(grab_request_var("use_unified_tac_overview", 0));
    $use_unified_hostgroup_screens = checkbox_binary(grab_request_var("use_unified_hostgroup_screens", 0));
    $use_unified_servicegroup_screens = checkbox_binary(grab_request_var("use_unified_servicegroup_screens", 0));

    // Nagiosxi PostgresQL
    $nagiosxi_db_max_commands_age = grab_request_var("nagiosxi_db_max_commands_age", get_database_interval("nagiosxi", "max_commands_age", 480));
    $nagiosxi_db_max_events_age = grab_request_var("nagiosxi_db_max_events_age", get_database_interval("nagiosxi", "max_events_age", 480));
    $nagiosxi_db_optimize_interval = grab_request_var("nagiosxi_db_optimize_interval", get_database_interval("nagiosxi", "optimize_interval", 60));
    $nagiosxi_db_repair_interval = grab_request_var("nagiosxi_db_repair_interval", get_database_interval("nagiosxi", "repair_interval", 0));
    $nagiosxi_db_nxti_age = grab_request_var("nagiosxi_db_nxti_age", get_database_interval("nagiosxi", "max_nxti_age", 90));
    $nagiosxi_db_max_scheduledreports_log_age = grab_request_var("nagiosxi_db_max_scheduledreports_log_age", get_database_interval("nagiosxi", "max_scheduledreports_log_age", 365));
    $nagiosxi_db_auth_token_age = grab_request_var("nagiosxi_db_auth_token_age", get_database_interval("nagiosxi", "max_auth_token_age"), 24);
    $nagiosxi_db_auth_session_age = grab_request_var("nagiosxi_db_auth_session_age", get_database_interval("nagiosxi", "max_auth_session_age"), 24);
    $nagiosxi_db_max_auditlog_age = grab_request_var("nagiosxi_db_max_auditlog_age", get_database_interval("nagiosxi", "max_auditlog_age", 180));

    // Ndoutils
    $ndoutils_db_max_externalcommands_age = grab_request_var("ndoutils_db_max_externalcommands_age", get_database_interval("ndoutils", "max_externalcommands_age", 7));
    $ndoutils_db_max_logentries_age = grab_request_var("ndoutils_db_max_logentries_age", get_database_interval("ndoutils", "max_logentries_age", 90));
    $ndoutils_db_max_notifications_age = grab_request_var("ndoutils_db_max_notifications_age", get_database_interval("ndoutils", "max_notifications_age", 90));
    $ndoutils_db_max_statehistory_age = grab_request_var("ndoutils_db_max_statehistory_age", get_database_interval("ndoutils", "max_statehistory_age", 730));
    $ndoutils_db_max_timedevents_age = grab_request_var("ndoutils_db_max_timedevents_age", get_database_interval("ndoutils", "max_timedevents_age", 5));
    $ndoutils_db_max_systemcommands_age = grab_request_var("ndoutils_db_max_systemcommands_age", get_database_interval("ndoutils", "max_systemcommands_age", 5));
    $ndoutils_db_max_servicechecks_age = grab_request_var("ndoutils_db_max_servicechecks_age", get_database_interval("ndoutils", "max_servicechecks_age", 5));
    $ndoutils_db_max_hostchecks_age = grab_request_var("ndoutils_db_max_hostchecks_age", get_database_interval("ndoutils", "max_hostchecks_age", 5));
    $ndoutils_db_max_eventhandlers_age = grab_request_var("ndoutils_db_max_eventhandlers_age", get_database_interval("ndoutils", "max_eventhandlers_age", 5));
    $ndoutils_db_max_commenthistory_age = grab_request_var("ndoutils_db_max_commenthistory_age", get_database_interval("ndoutils", "max_commenthistory_age", 730));
    $ndoutils_db_optimize_interval = grab_request_var("ndoutils_db_optimize_interval", get_database_interval("ndoutils", "optimize_interval", 60));
    $ndoutils_db_repair_interval = grab_request_var("ndoutils_db_repair_interval", get_database_interval("ndoutils", "repair_interval", 0));

    // Nagiosql
    $nagiosql_db_max_logbook_age = grab_request_var("nagiosql_db_max_logbook_age", get_database_interval("nagiosql", "max_logbook_age", 480));
    $nagiosql_db_optimize_interval = grab_request_var("nagiosql_db_optimize_interval", get_database_interval("nagiosql", "optimize_interval", 60));
    $nagiosql_db_repair_interval = grab_request_var("nagiosql_db_repair_interval", get_database_interval("nagiosql", "repair_interval", 0));

    // Subsystem
    $enable_subsystem_logging = checkbox_binary(grab_request_var("enable_subsystem_logging", 0));
    $enable_outbound_data_transfer = checkbox_binary(grab_request_var("enable_outbound_data_transfer", 0));
    $enable_unconfigured_objects = checkbox_binary(grab_request_var("enable_unconfigured_objects", 0));
    $bpi_sync_on_apply_config = checkbox_binary(grab_request_var("bpi_sync_on_apply_config", 0));
    $bpi_api_tool_ndo_timeout = grab_request_var("bpi_api_tool_ndo_timeout", 120);

    // Auto-running page settings
    $disable_report_auto_run = grab_request_var("disable_report_auto_run", 0);
    $disable_metrics_auto_run = grab_request_var("disable_metrics_auto_run", 0);

    // Backend Cache settings
    $enable_backend_cache = checkbox_binary(grab_request_var('enable_backend_cache', 0));
    $location_backend_cache = grab_request_var('location_backend_cache', '/usr/local/nagiosxi/tmp/backendcache');
    $ttl_backend_cache = grab_request_var('ttl_backend_cache', 300);
    $ttl_backend_cache = is_numeric($ttl_backend_cache) ? $ttl_backend_cache : 300;
    $enable_perfmon_backend_cache = checkbox_binary(grab_request_var('enable_perfmon_backend_cache', 0));

    $status_object_retries = intval(grab_request_var('status_object_retries', 6));

    // Snapshot settings
    $num_snapshots = intval(grab_request_var('num_snapshots', 10));
    $num_error_snapshots = intval(grab_request_var('num_error_snapshots', 10));
    $num_ccm_snapshots = intval(grab_request_var('num_ccm_snapshots', 10));

    // PDF/JPG generation
    $report_export_delay = intval(grab_request_var('report_export_delay', 300));
    $report_export_cp_delay = intval(grab_request_var('report_export_cp_delay', 600));
    $old_browser_compat_jquery1 = intval(grab_request_var('old_browser_compat_jquery1', 0));

    // Make sure we have requirements
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    // If we've enabled the backend cache, we need to attempt to create, or at least check the specified location
    if ($enable_backend_cache) {
        if (!is_dir($location_backend_cache)) {
            if (!file_exists($location_backend_cache)) {
                if (!mkdir($location_backend_cache, 0775, true)) {
                    $errors++;
                    $errmsg = "Unable to create $location_backend_cache.";
                }
            } else {
                $error++;
                $errmsg = "$location_backend_cache already exists.";
            }
        } else {
            if (!is_writable($location_backend_cache)) {
                $errors++;
                $errmsg = "$location_backend_cache is not writable.";
            }
        }
    }

    // Check to make sure snapshot values are sane
    if ($num_error_snapshots < 0) { $num_error_snapshots = 0; }
    if ($num_snapshots < 1) { $num_snapshots = 1; }
    if ($num_ccm_snapshots < 1) { $num_ccm_snapshots = 1; }
    if ($num_ccm_snapshots < $num_snapshots) { $num_ccm_snapshots = $num_snapshots; }

    // Handle errors
    if ($errors > 0) {
        show_options(true, $errmsg);
    }

    // Update options
    set_dashlet_refresh_multiplier($dashlet_refresh_multiplier);
    foreach ($dashlet_refresh_rates as $rid => $rate) {
        set_dashlet_refresh_rate($rate, $rid);
    }

    set_option("use_unified_tac_overview", $use_unified_tac_overview);
    set_option("use_unified_hostgroup_screens", $use_unified_hostgroup_screens);
    set_option("use_unified_servicegroup_screens", $use_unified_servicegroup_screens);

    set_database_interval("nagiosxi", "max_commands_age", $nagiosxi_db_max_commands_age);
    set_database_interval("nagiosxi", "max_events_age", $nagiosxi_db_max_events_age);
    set_database_interval("nagiosxi", "optimize_interval", $nagiosxi_db_optimize_interval);
    set_database_interval("nagiosxi", "repair_interval", $nagiosxi_db_repair_interval);
    set_database_interval("nagiosxi", "max_nxti_age", $nagiosxi_db_nxti_age);
    set_database_interval("nagiosxi", "max_scheduledreports_log_age", $nagiosxi_db_max_scheduledreports_log_age);
    set_database_interval("nagiosxi", "max_auth_token_age", $nagiosxi_db_auth_token_age);
    set_database_interval("nagiosxi", "max_auth_session_age", $nagiosxi_db_auth_session_age);
    set_database_interval("nagiosxi", "max_auditlog_age", $nagiosxi_db_max_auditlog_age);

    set_database_interval("ndoutils", "max_externalcommands_age", $ndoutils_db_max_externalcommands_age);
    set_database_interval("ndoutils", "max_logentries_age", $ndoutils_db_max_logentries_age);
    set_database_interval("ndoutils", "max_notifications_age", $ndoutils_db_max_notifications_age);
    set_database_interval("ndoutils", "max_statehistory_age", $ndoutils_db_max_statehistory_age);
    set_database_interval("ndoutils", "max_timedevents_age", $ndoutils_db_max_timedevents_age);
    set_database_interval("ndoutils", "max_systemcommands_age", $ndoutils_db_max_systemcommands_age);
    set_database_interval("ndoutils", "max_servicechecks_age", $ndoutils_db_max_servicechecks_age);
    set_database_interval("ndoutils", "max_hostchecks_age", $ndoutils_db_max_hostchecks_age);
    set_database_interval("ndoutils", "max_eventhandlers_age", $ndoutils_db_max_eventhandlers_age);
    set_database_interval("ndoutils", "max_commenthistory_age", $ndoutils_db_max_commenthistory_age);
    set_database_interval("ndoutils", "optimize_interval", $ndoutils_db_optimize_interval);
    set_database_interval("ndoutils", "repair_interval", $ndoutils_db_repair_interval);

    set_database_interval("nagiosql", "max_logbook_age", $nagiosql_db_max_logbook_age);
    set_database_interval("nagiosql", "optimize_interval", $nagiosql_db_optimize_interval);
    set_database_interval("nagiosql", "repair_interval", $nagiosql_db_repair_interval);

    // Subsystem
    set_option('enable_subsystem_logging', $enable_subsystem_logging);
    set_option('enable_outbound_data_transfer', $enable_outbound_data_transfer);
    set_option('enable_unconfigured_objects', $enable_unconfigured_objects);
    set_option('bpi_sync_on_apply_config', $bpi_sync_on_apply_config);
    set_option('bpi_api_tool_ndo_timeout', $bpi_api_tool_ndo_timeout);

    // Auto-running Report settings
    set_option('disable_report_auto_run', $disable_report_auto_run);
    set_option('disable_metrics_auto_run', $disable_metrics_auto_run);

    // Backend Cache settings
    set_option('enable_backend_cache', $enable_backend_cache);
    set_option('location_backend_cache', $location_backend_cache);
    set_option('ttl_backend_cache', $ttl_backend_cache);
    set_option('enable_perfmon_backend_cache', $enable_perfmon_backend_cache);

    set_option('status_object_retries', $status_object_retries);

    // Snapshot settings
    set_option('num_snapshots', $num_snapshots);
    set_option('num_error_snapshots', $num_error_snapshots);
    set_option('num_ccm_snapshots', $num_ccm_snapshots);

    // PDF/JPG generation
    set_option('report_export_delay', $report_export_delay);
    set_option('report_export_cp_delay', $report_export_cp_delay);
    set_option('old_browser_compat_jquery1', $old_browser_compat_jquery1);

    send_to_audit_log(_("Updated global performance settings"), AUDITLOGTYPE_CHANGE);

    flash_message(_("Performance settings updated."));

    show_options(false);
}