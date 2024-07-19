<?php
//
// Nagios XI API Documentation
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

if (!get_user_attr(0, 'api_enabled', 0) && !is_admin()) {
    die(_('Not authorized to view this page.'));
}

route_request();

function route_request()
{
    $page = grab_request_var("page", "");

    switch ($page) {
        default:
            show_main_api_page();
            break;
    }
}

function show_main_api_page()
{
    $backend_url = get_product_portal_backend_url();
    $apikey = get_apikey();

    do_page_start(array("page_title" => _('API - Introduction')), true);
?>

    <!-- Keep the help section part of the page -->
    <script src="../includes/js/api-help.js"></script>
    <script>
    $(document).ready(function() {
        // perform some basic highlighting on the code samples
        $('pre.code').html(function(index, html) {
            return html.replace(/\/\/.*/g, '<span style="color: #00b200;">$&</span>');
        });
    });

    </script>

    <div class="container-fluid" style="padding: 0 5px;">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">

                <h1><?php echo _('API - Introduction'); ?></h1>
                <p><?php echo _('New in Nagios XI 5 is a API that includes far more features and control over the Nagios XI system. This documentation explains how to use the new API to read, write, delete, and update data in the Nagios XI system through commands that are authenticated via Nagios XI API keys. The new API uses JSON instead of XML. However, with the release of Nagios XI 5 there are multiple output formats for the original backend API. It is recommended to use the new API over the old backend API. More information about the original backend API can be found in the backend API URL component.'); ?></p>

                <div class="help-section">
                    <a name="start"></a>
                    <h4><?php echo _('Getting Started'); ?></h4>
                    <p><?php echo _('To gain access to the new API use the URL formatted as such'); ?>:</p>
                    <p>
                        <code><?php echo get_base_url(); ?>api/v1/</code> <a href="<?php echo get_base_url(); ?>api/v1/" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                    </p>
                    <h5><?php echo _('API Sections'); ?></h5>
                    <p><?php echo _('The API is split into three separate sections.'); ?></p>
                    <p>
                        <ul>
                            <li style="margin-bottom: 10px;"><strong><?php echo _('Objects'); ?></strong> - <?php echo _('The standard read-only backend API that has always been available in Nagios XI. The objects section returns XML or JSON data about objects in Nagios XI including things like object statuses, contacts, contact groups, logs, history, downtime, etc.'); ?></li>
                            <li style="margin-bottom: 10px;"><strong><?php echo _('Config'); ?></strong> - (<?php echo _('Admin Only'); ?>) <?php echo _('Allows adding and removing objects such as hosts and services from Nagios XI.'); ?></li>
                            <li><strong><?php echo _('System'); ?></strong> - (<?php echo _('Admin Only'); ?>) <?php echo _('The system section allows managing certain subsystems in Nagios XI and sending commands to Nagios XI/Core.'); ?></li>
                        </ul>
                    </p>
                </div>

                <div class="help-section">
                    <a name="auth"></a>
                    <h4><?php echo _('Authentication'); ?></h4>
                    <p><?php echo _('Each user has their own API key to access the API. Normal users are allowed to have read access if the <em>Allow API Access</em> setting is selected in the user management Edit/Create pages. Every admin has full access to the API. Access to the documentation is restricted to users who have API access. Normal XI users, which have read only API access, will only be able to see the documentation about the objects section.'); ?></p>
                    <p><?php echo _('Whether you connect via a browser, cURL, or any other way you will need to pass the API key as a <em>GET</em> variable named <code>apikey</code> just like the example in <em>Getting Started</em>.'); ?></p>
                    <p><?php echo _('Your API Key:'); ?> <code><?php echo $apikey; ?></code></p>
                    <p><?php echo _('Access Level:'); ?> <?php if (is_admin()) { echo "Full Access"; } ?></p>
                </div>

                <div class="help-section">
                    <a name="usage"></a>
                    <h4><?php echo _('Basic Usage'); ?></h4>
                    <?php if (is_admin()) { ?>
                    <p><?php echo _('The example below is using the <code>GET system/status</code> command which shows general information about this Nagios XI server. Adding <code>pretty=1</code> to the URL will print the JSON in human readable format like the example below and can be used on any API request that returns output.'); ?></p>
					<div>
                        <h6><?php echo _('Example cURL Request'); ?>:</h6>
                        <pre class="curl-request">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/status?apikey=<?php echo $apikey; ?>&amp;pretty=1"</pre> <a href="<?php echo get_base_url(); ?>api/v1/system/status?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>
{
    "instance_id": "1",
    "instance_name": "localhost",
    "status_update_time": "2015-09-21 01:28:06",
    "program_start_time": "2015-09-20 12:21:20",
    "program_run_time": "47212",
    "program_end_time": "0000-00-00 00:00:00",
    "is_currently_running": "1",
    "process_id": "105075",
    "daemon_mode": "1",
    "last_command_check": "1969-12-31 18:00:00",
    "last_log_rotation": "2015-09-21 00:00:00",
    "notifications_enabled": "1",
    "active_service_checks_enabled": "1",
    "passive_service_checks_enabled": "1",
    "active_host_checks_enabled": "1",
    "passive_host_checks_enabled": "1",
    "event_handlers_enabled": "1",
    "flap_detection_enabled": "1",
    "process_performance_data": "1",
    "obsess_over_hosts": "0",
    "obsess_over_services": "0",
    "modified_host_attributes": "0",
    "modified_service_attributes": "0",
    "global_host_event_handler": "xi_host_event_handler",
    "global_service_event_handler": "xi_service_event_handler"
}</pre>
                <?php } else { ?>
                    <p><?php echo _('The example below is using the <code>GET objects/host</code> command which shows hosts that this users can view in the Nagios XI server. Adding <code>pretty=1</code> to the URL will print the JSON in human readable format like the example below and can be used on any API request that returns output.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>:</h6>
                        <pre class="curl-request">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/host?apikey=<?php echo $apikey; ?>&amp;pretty=1"</pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/host?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "hostlist": {
        "recordcount": "12",
        "host": [
            {
                "@attributes": {
                    "id": "202"
                },
                "instance_id": "1",
                "host_name": "tset",
                "is_active": "1",
                "config_type": "1",
                "alias": "tset",
                "display_name": "tset",
                "address": "127.0.53.53",
                "check_interval": "40",
                "retry_interval": "5",
                "max_check_attempts": "4",
                "first_notification_delay": "0",
                "notification_interval": "1",
                "passive_checks_enabled": "1",
                "active_checks_enabled": "1",
                "notifications_enabled": "0",
                "notes": "",
                "notes_url": "",
                "action_url": "",
                "icon_image": "server.png",
                "icon_image_alt": "",
                "statusmap_image": "server.png"
            }
        ]
    }
}</pre>
                <?php } ?>
                </div>

            </div>

            <div class="col-sm-4 col-md-3 col-lg-3 nav-box">
                <div class="well help-right-nav">
                    <h5><?php echo _('API - Introduction'); ?></h5>
                    <ol>
                        <li><a href="#start"><?php echo _('Getting Started'); ?></a></li>
                        <li><a href="#auth"><?php echo _('Authentication'); ?></a></li>
                        <li><a href="#usage"><?php echo _('Basic Usage'); ?></a></li>
                        <!--
                        <li><a href="#config"><?php echo _('Examples'); ?></a></li>
                        <ul>
                            <li><a href="#addhost"><?php echo _('Adding a Host'); ?></a></li>
                            <li><a href="#addservice"><?php echo _('Adding a Service'); ?></a></li>
                            <li><a href="#removeobject"><?php echo _('Remove Host/Service'); ?></a></li>
                            <li><a href="#aplpyconfig"><?php echo _('Apply Configuration'); ?></a></li>
                        </ul>
                        -->
                    </ol>
                    <ul style="padding: 0; margin: 15px 0 0 0; list-style-type: none;">
                        <li><a href="api-object-reference.php"><?php echo _('Objects Reference'); ?></a> <i class="fa fa-external-link"></i></li>
                        <li><a href="api-config-reference.php"><?php echo _('Config Reference'); ?></a> <i class="fa fa-external-link"></i></li>
                        <li><a href="api-system-reference.php"><?php echo _('System Reference'); ?></a> <i class="fa fa-external-link"></i></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}