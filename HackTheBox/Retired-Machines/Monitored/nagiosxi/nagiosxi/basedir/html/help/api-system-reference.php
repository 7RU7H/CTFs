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
check_authentication(false);

if (!is_admin()) {
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

    do_page_start(array("page_title" => _('API - System Reference')), true);
?>

    <!-- Keep the help section part of the page -->
    <script src="../includes/js/clipboard.min.js"></script>
    <script src="../includes/js/api-help.js"></script>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">

                <h1><?php echo _('API - System Reference'); ?></h1>
                <p><?php echo _('The system section of the API allows management of the system, services, and backend.'); ?> <em><?php echo _('This API section is'); ?> <b><?php echo _('admin only'); ?></b>.</em></p>

                <h3 class="help-section-head"><?php echo _('Basic System'); ?></h3>

                <a name="system-status"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/status</h4>
                    <p><?php echo _('Gives the output of the current system status.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-status"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-status">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/status?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/status?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "instance_id": "1",
    "instance_name": "localhost",
    "status_update_time": "2015-09-21 01:48:14",
    "program_start_time": "2015-09-20 12:21:20",
    "program_run_time": "48419",
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
                </div>

                <a name="system-status-detail"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/statusdetail</h4>
                    <p><?php echo _('Gives the output of the current system status in more detail.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-status-detail"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-status-detail">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/statusdetail?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/statusdetail?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "dbmaint": {
        "last_check": "1529884501"
    },
    "cleaner": {
        "last_check": "1529884501"
    },
    "nom": {
        "last_check": "1529884501"
    },
    "reportengine": {
        "last_check": "1529884501"
    },
    "daemons": { ... },
    "nagioscore": { ... },
    ...
}
</pre>
                </div>

                <a name="system-info"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/info</h4>
                    <p><?php echo _('Gives the output of the current system information.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-info"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-info">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/info?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/info?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "product": "nagiosxi",
    "version": "5.5.0",
    "version_major": "5",
    "version_minor": "5.0",
    "build_id": "09172017",
    "release": 5500
}</pre>
                </div>

                <a name="system-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/command</h4>
                    <p><?php echo _('Show status of subsystem commands. Can be filtered to a subset, shown in the second example.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-commands"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-commands">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/command?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/command?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>[
    {
        "command_id": 3157,
        "submitter_id": 1,
        "command": 17,
        "submission_time": "2018-03-07 14:03:06",
        "event_time": "2018-03-07 14:03:03",
        "processing_time": "2018-03-07 14:03:06",
        "status_code": 2,
        "result_code": 0,
        "result": ""
    },
    {
        "command_id": 3158,
        "submitter_id": 1,
        "command": 17,
        "submission_time": "2018-03-07 14:03:57",
        "event_time": "2018-03-07 14:03:55",
        "processing_time": "2018-03-07 14:03:57",
        "status_code": 0,
        "result_code": 0,
        "result": ""
    }
]</pre>
                    <p style="margin-top: 20px;"><?php echo _('You may want to see just a single command, to check something like the status. This can be done by passing <code>system/status/&lt;command_id&gt;</code> in the URL. You can also pass the <code>status_code</code> in the URL to get only values with certain status codes. Below is an example of how to grab a single command by <code>command_id</code>.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>:</h6>
                        <pre class="curl-request"><code id="cmd-system-commands">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/command/3157?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>[
    {
        "command_id": 3157,
        "submitter_id": 1,
        "command": 17,
        "submission_time": "2018-03-07 14:03:06",
        "event_time": "2018-03-07 14:03:03",
        "processing_time": "2018-03-07 14:03:06",
        "status_code": 2,
        "result_code": 0,
        "result": ""
    }
]</pre>
                </div>

                <h3 class="help-section-head"><?php echo _('Core System'); ?></h3>

                <a name="system-applyconfig"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span><span class="label label-info">POST</span> system/applyconfig</h4>
                    <p><?php echo _('Run the apply config command which imports configs and restarts Nagios Core. This should normally be ran after adding objects via the API if the <code>applyconfig=1</code> parameter is not sent. Note: You can also run this as either a GET or POST.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-applyconfig"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-applyconfig">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/applyconfig?apikey=<?php echo $apikey; ?>"</code></pre>
                        <div class="clear"></div>
                    </div>
                </div>

                <a name="system-importconfig"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span><span class="label label-info">POST</span> system/importconfig</h4>
                    <p><?php echo _('Runs the import command which imports all configuration files from') . ' <code>/usr/local/nagios/etc/import</code> ' ._('into the CCM. This command does not restart Nagios Core. Note: You can also run this as either a GET or POST.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-import"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-import">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/importconfig?apikey=<?php echo $apikey; ?>"</code></pre>
                        <div class="clear"></div>
                    </div>
                </div>

                <a name="system-core-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> system/corecommand</h4>
                    <p><?php echo _('Send an core command (external command and arguments) to the nagios.cmd through the API. You can send any of the commands that are listed in the').' <a target="_blank" href="https://old.nagios.org/developerinfo/externalcommands/">'._('Nagios Developer External Commands').'</a> '._('page. You will need to pass the commands completely, including semi-colons. You do not, however, need to pass the command timestamp.'); ?></p>
                    <p><?php echo _('Example of command formatting in API using the <code>ADD_HOST_COMMENT</code> command'); ?>:</p>
                    <p><code>cmd=ADD_HOST_COMMENT;localhost;1;nagiosadmin;This is a test comment</code></p>
                    <p><?php echo _('You can also use the <code>%user%</code> macro in the command line to place the username of the user who\'s API key is running the command.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-core-command"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-core-command">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/corecommand?apikey=<?php echo $apikey; ?>" -d "cmd=ADD_HOST_COMMENT;localhost;1;%user%;This is a test comment"</code></pre>
                        <div class="clear"></div>
                    </div>
                    <div>
                        <h6><?php echo _('Example Repsonse (Success)'); ?></h6>
                        <pre>{
    "cmd": "ADD_HOST_COMMENT;localhost;1;nagiosadmin;This is a test comment",
    "success": "Core command submitted"
}
</pre>
                        <div class="clear"></div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Scheduled Downtime'); ?></h3>

                <a name="system-schedule-downtime"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> system/scheduleddowntime</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-xl-5">
                                <p><?php echo _('Schedules a new scheduled downtime in Nagios XI.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>start</td>
                                            <td>integer</td>
                                            <td>timestamp</td>
                                        </tr>
                                        <tr>
                                            <td>end</td>
                                            <td>integer</td>
                                            <td>timestamp</td>
                                        </tr>
                                        <tr>
                                            <td>comment</td>
                                            <td>string</td>
                                            <td><?php echo _('comment'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>author</td>
                                            <td>string</td>
                                            <td><b><?php echo _("api key's user"); ?></b> <?php echo _('by default if empty or a username'); ?></td>
                                        </tr>
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hosts[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host names (i.e. hosts[]=1&hosts[]=2, etc)"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>child_hosts</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or 1 for regular / 2 for children triggered by parent"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>all_services</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or 1 for all services on specified hosts"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>services[host]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host/service combos (i.e. services[localhost]=PING)"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>hostgroups[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host group names"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>servicegroups[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of service group names"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>flexible</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>duration</td>
                                            <td>integer</td>
                                            <td><?php echo _('flexible duration length in minutes'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>triggered_by</td>
                                            <td>integer</td>
                                            <td><?php echo _('ID of downtime to be triggered by'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>only</td>
                                            <td>string</td>
                                            <td>hosts, services<br>(<?php echo _('make downtime for only hosts or services'); ?> <i><?php echo _('only applies to servicegroups[] and hostgroups[]'); ?></i>)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-7">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-add-sd"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-add-sd">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/scheduleddowntime?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "comment=Test downtime creation&amp;start=<?php echo time(); ?>&amp;end=<?php echo (time() + (60 * 120)); ?>&amp;hosts[]=localhost"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Schedule downtime command(s) sent successfully.",
    "scheduled": {
        "hosts": [
            "localhost"
        ]
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="system-remove-downtime"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> system/scheduleddowntime/&lt;internal_id&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('Deletes a scheduled downtime from the Nagios XI system. The downtime ID can be found in'); ?> <code>objects/downtime</code>.</p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>internal_id</td>
                                            <td>integer</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><?php echo _('You can get the internal ID from the'); ?> <a href="api-object-reference.php#objects-downtime"><?php echo _('objects downtime endpoint'); ?></a>.</p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-delete-downtime"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-delete-downtime">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/system/scheduleddowntime/<b>10</b>?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Remove downtime command(s) sent successfully."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Here is the start delete this -->
                <h3 class="help-section-head"><?php echo _('Mass Immediate Check'); ?></h3>

                <a name="system-mass-check"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> system/massimmediatecheck</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-xl-5">
                                <p><?php echo _('Triggers an immediate check of an object or objects in Nagios XI. You must enter at least one hosts[], services[], hostgroups[], or servicegroups[].'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hosts[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host names (i.e. hosts[]=localhost&hosts[]=192.168.1.1, etc)"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>all_services</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or 1 for all services on specified hosts"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>services[host][]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host/service combos (i.e. services[localhost][]=PING)"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>hostgroups[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of host group names"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>servicegroups[]</td>
                                            <td>array</td>
                                            <td><?php echo _("list of service group names"); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-7">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-mass-check-sd"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-mass-check-sd">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/massimmediatecheck?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "hosts[]=localhost"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Mass immediate check command(s) sent successfully.",
    "time": "<?= date("Y-m-d H:i:s", time()) ?>",
    "checks": {
       "hosts": [
            "localhost"
        ] 
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Users'); ?></h3>

                <a name="user"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/user</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-4 col-lg-4 col-xl-3">
                                <p><?php echo _('Lists all users in the Nagios XI system.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>advanced</td>
                                            <td>integer</td>
                                            <td><?php echo _("0 or 1"); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-8 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-user"><?php echo _('Copy'); ?></a></h6>
                                <pre class="curl-request"><code id="cmd-system-user">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/user?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/user?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                                <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "records": 2,
    "users": [
        {
            "user_id": "2",
            "username": "jmcdouglas",
            "name": "Jordan McDouglas",
            "email": "jmcdouglas@localhost",
            "enabled": "1"
        },
        {
            "user_id": "1",
            "username": "nagiosadmin",
            "name": "Nagios Administrator",
            "email": "root@localhost",
            "enabled": "1"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-user"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> system/user</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-xl-5">
                                <p><?php echo _('Creates a new user in the Nagios XI system. Values in <b>bold</b> are defaults.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>username</td>
                                            <td>string</td>
                                            <td>user name</td>
                                        </tr>
                                        <tr>
                                            <td>password</td>
                                            <td>string</td>
                                            <td>password</td>
                                        </tr>
                                        <tr>
                                            <td>name</td>
                                            <td>string</td>
                                            <td>name</td>
                                        </tr>
                                        <tr>
                                            <td>email</td>
                                            <td>string</td>
                                            <td>email address</td>
                                        </tr>
                                        <tr>
                                            <td>force_pw_change</td>
                                            <td>integer</td>
                                            <td><b>1</b> or 0</td>
                                        </tr>
                                        <tr>
                                            <td>email_info</td>
                                            <td>integer</td>
                                            <td><b>1</b> or 0</td>
                                        </tr>
                                        <tr>
                                            <td>monitoring_contact</td>
                                            <td>integer</td>
                                            <td><b>1</b> or 0</td>
                                        </tr>
                                        <tr>
                                            <td>enable_notifications</td>
                                            <td>integer</td>
                                            <td><b>1</b> or 0</td>
                                        </tr>
                                        <tr>
                                            <td>language</td>
                                            <td>string</td>
                                            <td><b>xi <?php echo _('default'); ?></b> or &lt;locale&gt; (<?php echo _('locale exmaple'); ?>: en_US, fr_FR, etc)</td>
                                        </tr>
                                        <tr>
                                            <td>date_format</td>
                                            <td>integer</td>
                                            <td>
                                                <b>1 - YYYY-MM-DD</b><br>
                                                2 - MM/DD/YYYY<br>
                                                3 - DD/MM/YYYY
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>number_format</td>
                                            <td>integer</td>
                                            <td>
                                                <b>1 - 1000.00</b><br>
                                                2 - 1,000.00<br>
                                                3 - 1.000,00<br>
                                                4 - 1 000.00<br>
                                                5 - 1'000.00
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>auth_level</td>
                                            <td>string</td>
                                            <td><b><?php echo _('user'); ?></b> <?php echo _('or admin'); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><?php echo _('If user type selected: (Ignored if user is admin)'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>can_see_all_hs</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>can_control_all_hs</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>can_reconfigure_hs</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>can_control_engine</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>can_use_advanced</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>read_only</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1 (<?php echo _("all others won't work"); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><?php echo _('Auth settings for using AD or LDAP'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>auth_type</td>
                                            <td>string</td>
                                            <td><b>local</b>, ad, ldap</td>
                                        </tr>
                                        <tr>
                                            <td>auth_server_id</td>
                                            <td>string</td>
                                            <td><?php echo _('Auth server ID'); ?> (<?php echo _('from'); ?> <a href="#auth-server">GET system/authserver</a>)</td>
                                        </tr>
                                        <tr>
                                            <td>allow_local</td>
                                            <td>integer</td>
                                            <td><b>0</b> <?php echo _("or"); ?> 1</td>
                                        </tr>
                                        <tr>
                                            <td>ad_username</td>
                                            <td>string</td>
                                            <td><?php echo _('AD username without suffix'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>ldap_dn</td>
                                            <td>string</td>
                                            <td><?php echo _('LDAP full distinguished name'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-7">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-add-user"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-add-user">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/user?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d 'username=jmcdouglas&amp;password=test&amp;name=Jordan%20McDouglas&amp;email=jmcdouglas@localhost'</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "<?php echo _('User account jmcdouglas was added successfully!'); ?>",
    "user_id": "16"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-user"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> system/user/&lt;user_id&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('Deletes a user from the Nagios XI system.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>user_id</td>
                                            <td>integer</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-delete-user"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-delete-user">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/system/user/<b>99</b>?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "User removed."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Auth Servers'); ?></h3>

                <a name="auth-server"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> system/authserver</h4>
                    <p><?php echo _('Get a list of auth servers. You can filter this list by <b>server_id</b> by passing it through as a URL parameter.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-authserver"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-system-authserver">curl -XGET "<?php echo get_base_url(); ?>api/v1/system/authserver?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/system/authserver?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "records": 1,
    "authservers": [
        {
            "id": "59bd9f6f67775",
            "enabled": 1,
            "conn_method": "ad",
            "ad_account_suffix": "@not.nagios.local",
            "ad_domain_controllers": "dc1.not.nagios.local",
            "base_dn": "dc=not,dc=nagios,dc=local",
            "security_level": "none",
            "ldap_port": "",
            "ldap_host": ""
        }
    ]
}</pre>
                </div>

                <a name="add-auth-server"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> system/authserver</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-xl-5">
                                <p><?php echo _('Creates a new auth server in the Nagios XI system. Values in <b>bold</b> are defaults.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                            <th><?php echo _('Values'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>conn_method</td>
                                            <td>string</td>
                                            <td>ad <?php echo _("or"); ?> ldap</td>
                                        </tr>
                                        <tr>
                                            <td>enabled</td>
                                            <td>integer</td>
                                            <td><b>1</b> <?php echo _("or"); ?> 0</td>
                                        </tr>
                                        <tr>
                                            <td>base_dn</td>
                                            <td>string</td>
                                            <td><?php echo _('full distinguished name'); ?></td>
                                        </tr>
                                        <tr>
                                            <td>security_level</td>
                                            <td>string</td>
                                            <td><b>none</b>, ssl, tls</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">conn_method=ldap</td>
                                        </tr>
                                        <tr>
                                            <td>ldap_host</td>
                                            <td>string</td>
                                            <td><?php echo _("ip address or hostname"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>ldap_port</td>
                                            <td>integer</td>
                                            <td><b>389</b> <?php echo _('or port number'); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">conn_method=ad</td>
                                        </tr>
                                        <tr>
                                            <td>ad_account_suffix</td>
                                            <td>string</td>
                                            <td><?php echo _('account suffix'); ?> (ex: @domain)</td>
                                        </tr>
                                        <tr>
                                            <td>ad_domain_controllers</td>
                                            <td>string</td>
                                            <td><?php echo _('comma separated list of domain controllers'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-7">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-add-authserver"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-add-authserver">curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/authserver?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "conn_method=ldap&amp;ldap_host=192.168.0.123&amp;base_dn=dc%3Dnagios,dc%3Dlocal&amp;security_level=ssl"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Auth server created successfully.",
    "server_id": "59bdbea68f924"
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-auth-server"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> system/authserver/&lt;server_id&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('Deletes an auth server from the Nagios XI system.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>server_id</td>
                                            <td>integer</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-system-delete-authserver"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-system-delete-authserver">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/system/authserver/<b>59bd9f6f67775</b>?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed auth server successfully."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

             </div>
             <div class="col-sm-4 col-md-3 col-lg-3 nav-box">
                <div class="well help-right-nav">
                    <h5><?php echo _('API - System Reference'); ?></h5>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Basic System'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#system-status">system/status</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#system-status-detail">system/statusdetail</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#system-info">system/info</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#system-command">system/command</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Core System'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span><span class="label label-info">POST</span> <a href="#system-applyconfig">system/applyconfig</a></li>
                        <li><span class="label label-primary">GET</span><span class="label label-info">POST</span> <a href="#system-importconfig">system/importconfig</a></li>
                        <li><span class="label label-info">POST</span> <a href="#system-core-command">system/corecommand</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Scheduled Downtime'); ?></p>
                    <ul>
                        <li><span class="label label-info">POST</span> <a href="#system-schedule-downtime">system/scheduleddowntime</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#system-remove-downtime">system/scheduleddowntime/&lt;internal_id&gt;</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Mass Immediate Check'); ?></p>
                    <ul>
                        <li><span class="label label-info">POST</span> <a href="#system-mass-check">system/massimmediatecheck</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Users'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#user">system/user</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-user">system/user</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-user">system/user/&lt;user_id&gt;</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Auth Servers'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#auth-server">system/authserver</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-auth-server">system/authserver</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-auth-server">system/authserver/&lt;server_id&gt;</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}
