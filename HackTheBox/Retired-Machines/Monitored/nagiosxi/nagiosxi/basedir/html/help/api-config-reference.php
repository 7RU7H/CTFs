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
    $apikey = get_apikey();

    do_page_start(array("page_title" => _('API - Config Reference')), true);
?>

    <!-- Keep the help section part of the page -->
    <script src="../includes/js/clipboard.min.js"></script>
    <script src="../includes/js/api-help.js"></script>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">

                <h1><?php echo _('API - Config Reference'); ?></h1>
                <p style="padding: 0;"><?php echo _('With the new API in Nagios XI 5 we linked it up directly with the Core Config Manager. This allows creation and deletion of items directly from the API.'); ?> <em><?php echo _('This API section is'); ?> <b><?php echo _('admin only'); ?></b>.</em></p>

                <div class="alert alert-warning" style="margin-top: 2em;">
                    <i class="fa fa-exclamation l"></i> <b><?php echo _('Warning'); ?>:</b> <?php echo _('By default the Nagios Core service <em><b>is not restarted</b></em> when an object is created or deleted via the API.'); ?> <?php echo _('To apply the configuration, use the'); ?> <a href="api-system-reference.php#system-applyconfig">GET system/applyconfig</a> <?php echo _('request after making the changes you want to apply'); ?>. <?php echo _('You may change the default behavior by passing the argument <code>applyconfig=1</code> in your API request which will <em><b>write out your config and restart</b></em> the Nagios Core service.'); ?>
                </div>

                <div class="alert alert-warning" style="margin-top: 0;">
                    <i class="fa fa-exclamation l"></i> <b><?php echo _('Warning'); ?>:</b> <?php echo _('Some objects returned by <b>GET</b> queries may not be applied yet. Use the objects reference API when querying for a list of objects that are being monitored.'); ?>
                </div>

                <div class="alert alert-info" style="margin-top: 0;">
                    <i class="fa fa-exclamation l"></i> <b><?php echo _('Note'); ?>:</b> <?php echo _('If you need to skip the per-item config verification add <code>force=1</code> to the config API request. This is especially helpful when applying an object that uses a template and may be inheriting one of the required parameters such as <code>check_command</code> which the CCM verification will not know about.'); ?> <em><?php echo _('Warning: This can cause the apply config to fail if not used properly.'); ?></em>
                </div>

                <div class="alert alert-info" style="margin-top: 0;">
                        <i class="fa fa-exclamation l"></i> <b><?php echo _('Note'); ?>:</b> <?php echo _('Remember that you will need to url encode any data that is being passed such as / and blank spaces.'); ?>
                </div>

                <h3 class="help-section-head"><?php echo _('Hosts'); ?></h3>

                <a name="get-host"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/host</h4>
                    <p><?php echo _('This command gets all the configured hosts in the CCM even if they have not been applied. Note: This lists <i>all</i> hosts in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-host"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/host?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-host">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/host?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "host_name": "localhost",
        "use": [
            "linux-server"
        ],
        "alias": "localhost",
        "address": "127.0.0.1",
        "check_command": "check-host-alive!!!!!!!!",
        "first_notification_delay": "0",
        "notifications_enabled": "1",
        "register": "1"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-host"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/host</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command creates a new host object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>address</td>
                                            <td>ip address</td>
                                        </tr>
                                        <tr>
                                            <td>max_check_attempts</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                        </tr>
                                        <tr>
                                            <td>notification_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>notification_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal host directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-host"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-host">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/host?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "host_name=testapihost&amp;address=127.0.0.1&amp;check_command=check_ping\!3000,80%\!5000,100%&amp;max_check_attempts=2&amp;check_period=24x7&amp;contacts=nagiosadmin&amp;notification_interval=5&amp;notification_period=24x7&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testapihost to the system. Config applied, Nagios Core was restarted."
}
</pre>
                                <h6><?php echo _('Response (Failure)'); ?>:</h6>
                                <pre>{
    "error": "Missing required variables",
    "missing": [
        "host_name",
        "address",
        "max_check_attempts",
        "check_period",
        "notification_interval",
        "notification_period",
        "contacts OR contact_groups"
    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-host"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/host/&lt;host_name&gt;</h4>
                    <p><?php echo _('This command updates a host object.'); ?> <?php echo sprintf(_('If you have special characters in the host_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/host</code>', '<code>old_host_name=&lt;host_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>address</td>
                                            <td>ip address</td>
                                        </tr>
                                        <tr>
                                            <td>max_check_attempts</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>contacts</td>
                                            <td>contacts</td>
                                        </tr>
                                        <tr>
                                            <td>contact_groups</td>
                                            <td>contact_groups</td>
                                        </tr>
                                        <tr>
                                            <td>notification_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>notification_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal host directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-host"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-host">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/host/testapihost?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;address=127.0.0.1&amp;notification_interval=30&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated testapihost in the system. Config applied, Nagios Core was restarted."
}
</pre>
                                <h6><?php echo _('Response (Failure)'); ?>:</h6>
                                <pre>{
    "error": "Could not update the host specified. Does the host exist?"
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-host"></a>
                <div class="help-section obj-reference">
                    <h4>
                        <span class="label label-danger">DELETE</span> config/host <i>or</i> config/host/&lt;host_name&gt;
                    </h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command removes a host object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>host_name[] *</td>
                                            <td>host name</td>
                                        </tr>
                                    </tbody>
                                </table>
                                * <?php echo _('You can use the [] parameter to pass multiple values, see'); ?> <a href="api-common-solutions.php#delete-multiples"><?php echo _('common solutions'); ?></a>.
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-host"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-host">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/host/testapihost?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed hosts from the system. Config applied, Nagios Core was restarted.",
    "hosts": [
        "testapihost"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Services'); ?></h3>

                <a name="get-service"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/service</h4>
                    <p><?php echo _('This command gets all the configured services in the CCM even if they have not been applied. Note: This lists <i>all</i> services in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>config_name</td>
                                            <td>config name</td>
                                        </tr>
                                        <tr>
                                            <td>service_description</td>
                                            <td>service name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-service"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/service?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-service">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/service?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "config_name": "localhost",
        "host_name": [
            "localhost"
        ],
        "service_description": "Current Load",
        "use": [
            "local-service"
        ],
        "check_command": "check_local_load!5.0,4.0,3.0!10.0,6.0,4.0!!!!!!",
        "notes": "Testing 123",
        "register": "1"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-service"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/service</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command creates a new service object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>service_description</td>
                                            <td>service name</td>
                                        </tr>
                                        <tr>
                                            <td>check_command</td>
                                            <td>command_name</td>
                                        </tr>
                                        <tr>
                                            <td>max_check_attempts</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>retry_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>notification_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>notification_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>config_name</td>
                                            <td>config name (<?php echo _('in CCM'); ?>)</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal service directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-service"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-service">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/service?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "host_name=testapihost&amp;service_description=PING&amp;check_command=check_ping\!3000,80%\!5000,100%&amp;check_interval=5&amp;retry_interval=5&amp;max_check_attempts=2&amp;check_period=24x7&amp;contacts=nagiosadmin&amp;notification_interval=5&amp;notification_period=24x7&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testapihost :: PING to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-service"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/service/&lt;config_name&gt;/&lt;service_description&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <p><?php echo _('This command creates or updates a service object. To update, a host_name or config_name must be specified to find the specific service.'); ?> <?php echo sprintf(_('If you have special characters in the config_name or service_description and cannot pass it through the path, you can use %s with the %s and %s URL parameters instead.'), '<code>config/service</code>', '<code>old_config_name=&lt;config_name&gt;</code>', '<code>old_service_description=&lt;service_description&gt;</code>'); ?></p></p>
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>config_name</td>
                                            <td>config name (<?php echo _('in CCM'); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>service_description</td>
                                            <td>service name</td>
                                        </tr>
                                        <tr>
                                            <td>check_command</td>
                                            <td>command_name</td>
                                        </tr>
                                        <tr>
                                            <td>max_check_attempts</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>retry_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>check_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>notification_interval</td>
                                            <td>#</td>
                                        </tr>
                                        <tr>
                                            <td>notification_period</td>
                                            <td>timeperiod_name</td>
                                        </tr>
                                        <tr>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                            <td>contacts<br><em>or</em><br>contact_groups</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal service directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <p><?php echo _('Updates a service name by using <code>config_name</code> and <code>service_description</code>.'); ?></p>
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-update-service"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-update-service">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/service/localhost/PING?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;display_name=PINGTEST&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated testapihost :: PING in the system. Config applied, Nagios Core was restarted."
}
</pre>
                                <h6><?php echo _('Response (Failure)'); ?>:</h6>
                                <pre>{
    "error": "Could not update the service specified. Does the service exist?"
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-service"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/service <i>or</i> config/service/&lt;config_name&gt;/&lt;service_description&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command removes a service object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>host_name</td>
                                            <td>host name</td>
                                        </tr>
                                        <tr>
                                            <td>service_description</td>
                                            <td>service name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-service"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-service">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/service?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host_name=testapihost&amp;service_description=PING&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed services from the system. Config applied, Nagios Core was restarted.",
    "services": [
        "testapihost - PING"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Groups'); ?></h3>

                <a name="get-hostgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/hostgroup</h4>
                    <p><?php echo _('This command gets all the configured host groups in the CCM even if they have not been applied. Note: This lists <i>all</i> host groups in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hostgroup_name</td>
                                            <td>host group name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-hostgroup"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-hostgroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "hostgroup_name": "linux-servers",
        "alias": "Linux Servers",
        "members": [
            "localhost"
        ]
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="get-servicegroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/servicegroup</h4>
                    <p><?php echo _('This command gets all the configured service groups in the CCM even if they have not been applied. Note: This lists <i>all</i> service groups in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>servicegroup_name</td>
                                            <td>service group name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-servicegroup"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-servicegroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "servicegroup_name": "testgroup",
        "alias": "test123"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-hostgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/hostgroup</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command creates a new hostgroup object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hostgroup_name</td>
                                            <td>hostgroup name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal hostgroup directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-hg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-hg">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "hostgroup_name=testapihostgroup&amp;alias=HostGroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testapihostgroup to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-servicegroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/servicegroup</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command creates a new servicegroup object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>servicegroup_name</td>
                                            <td>servicegroup name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal servicegroup directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-sg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-sg">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "servicegroup_name=testapiservicegroup&amp;alias=ServiceGroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testapiservicegroup to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-hostgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/hostgroup/&lt;hostgroup_name&gt;</h4>
                    <p><?php echo _('This command updates a current hostgroup.'); ?> <?php echo sprintf(_('If you have special characters in the hostgroup_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/hostgroup</code>', '<code>old_hostgroup_name=&lt;hostgroup_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hostgroup_name</td>
                                            <td>hostgroup name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal hostgroup directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-hg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-hg">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/hostgroup/testapihostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;alias=NewAlias&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated testapihostgroup in the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-servicegroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/servicegroup/&lt;servicegroup_name&gt;</h4>
                    <p><?php echo _('This command updates a current servicegroup.'); ?> <?php echo sprintf(_('If you have special characters in the servicegroup_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/servicegroup</code>', '<code>old_servicegroup_name=&lt;servicegroup_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>servicegroup_name</td>
                                            <td>servicegroup name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal servicegroup directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-sg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-sg">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/servicegroup/testapiservicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;alias=NewAlias&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated testapiservicegroup in the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-hostgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/hostgroup <i>or</i> config/hostgroup/&lt;hostgroup_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command removes a hostgroup object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>hostgroup_name</td>
                                            <td>hostgroup name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-del-hg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-del-hg">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;hostgroup_name=testapihostgroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed host groups from the system. Config applied, Nagios Core was restarted.",
    "hostgroups": [
        "testapihostgroup"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-servicegroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/servicegroup <i>or</i> config/servicegroup/&lt;servicegroup_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This command removes a servicegroup object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>servicegroup_name</td>
                                            <td>servicegroup name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-del-sg"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-del-sg">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;servicegroup_name=testapiservicegroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed service groups from the system. Config applied, Nagios Core was restarted.",
    "servicegroups": [
        "testapiservicegroup"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Commands'); ?></h3>

                <a name="get-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/command</h4>
                    <p><?php echo _('This command gets all the configured commands in the CCM even if they have not been applied. Note: This lists <i>all</i> commands in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>command_name</td>
                                            <td>command name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-command"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/command?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-command">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/command?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "command_name": "check-host-alive",
        "command_line": "$USER1$\/check_icmp -H $HOSTADDRESS$ -w 3000.0,80% -c 5000.0,100% -p 5"
    },
    {
        "command_name": "check-host-alive-http",
        "command_line": "$USER1$\/check_http -H $HOSTADDRESS$"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/command</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint creates a new command object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>command_name</td>
                                            <td>command name</td>
                                        </tr>
                                        <tr>
                                            <td>command_line</td>
                                            <td>command line</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-command"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-command">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/command?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "command_name=check_touch_tmp&amp;command_line=touch /tmp/testing&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added check_touch_tmp to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/command/&lt;command_name&gt;</h4>
                    <p><?php echo _('This endpoint edits a command object.'); ?> <?php echo sprintf(_('If you have special characters in the command_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/command</code>', '<code>old_command_name=&lt;command_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>command_name</td>
                                            <td>command name</td>
                                        </tr>
                                        <tr>
                                            <td>command_line</td>
                                            <td>command line</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-command"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-command">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/command/check_touch_tmp?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;command_name=check_touch_tmp_new&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated check_touch_tmp_new in the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-command"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/command <i>or</i> config/command/&lt;command_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint deletes a command object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>command_name</td>
                                            <td>command name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-command"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-command">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/command?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;command_name=check_touch_tmp&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed commands from the system. Config applied, Nagios Core was restarted.",
    "commands": [
        "check_touch_tmp"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Contacts'); ?></h3>

                <a name="get-contact"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/contact</h4>
                    <p><?php echo _('This command gets all the configured contacts in the CCM even if they have not been applied. Note: This lists <i>all</i> contacts in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contact_name</td>
                                            <td>contact name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-contact"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-contact">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "contact_name": "nagiosadmin",
        "alias": "Nagios Administrator",
        "host_notifications_enabled": "1",
        "service_notifications_enabled": "1",
        "host_notification_period": "nagiosadmin_notification_times",
        "service_notification_period": "nagiosadmin_notification_times",
        "host_notification_options": "d,u,r,f,s",
        "service_notification_options": "w,u,c,r,f,s",
        "host_notification_commands": [
            "xi_host_notification_handler"
        ],
        "service_notification_commands": [
            "xi_service_notification_handler"
        ],
        "email": "root@localhost",
        "use": [
            "xi_contact_generic"
        ]
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="get-contactgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/contactgroup</h4>
                    <p><?php echo _('This command gets all the configured contact groups in the CCM even if they have not been applied. Note: This lists <i>all</i> contact groups in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contactgroup_name</td>
                                            <td>contact group name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-contactgroup"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-contactgroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> 
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "contactgroup_name": "admins",
        "alias": "Nagios Administrators",
        "members": [
            "nagiosadmin"
        ]
    },
    {
        "contactgroup_name": "xi_contactgroup_all",
        "alias": "All Contacts"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-contact"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/contact</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint creates a new contact object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contact_name</td>
                                            <td>contact name</td>
                                        </tr>
                                        <tr>
                                            <td>host_notifications_enabled</td>
                                            <td>[0/1]</td>
                                        </tr>
                                        <tr>
                                            <td>service_notifications_enabled</td>
                                            <td>[0/1]</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_period</td>
                                            <td>timeperiod name</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_period</td>
                                            <td>timeperiod name</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_options</td>
                                            <td>[d,u,r,f,s,n]</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_options</td>
                                            <td>[w,u,c,r,f,s,n]</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_commands</td>
                                            <td>command name</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_commands</td>
                                            <td>command name</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-contact"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-contact">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "contact_name=testcontact&amp;host_notifications_enabled=1&amp;service_notifications_enabled=1&amp;host_notification_period=24x7&amp;service_notification_period=24x7&amp;host_notification_options=d,r&amp;service_notification_options=w,c,r&amp;host_notification_commands=notify-host-by-email&amp;service_notification_commands=notify-service-by-email&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testcontact to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-contactgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/contactgroup</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint creates a new contact group object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contactgroup_name</td>
                                            <td>contact group name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>members</td>
                                            <td>contact names</td>
                                        </tr>
                                        <tr>
                                            <td>contactgroup_members</td>
                                            <td>contact group names</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-contactgroup"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-contactgroup">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "contactgroup_name=testcontactgroup&amp;alias=newcontactgroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testcontact to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-contact"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/contact/&lt;contact_name&gt;</h4>
                    <p><?php echo _('This endpoint edits a contact object.'); ?> <?php echo sprintf(_('If you have special characters in the contact_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/contact</code>', '<code>old_contact_name=&lt;contact_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contact_name</td>
                                            <td>contact name</td>
                                        </tr>
                                        <tr>
                                            <td>host_notifications_enabled</td>
                                            <td>[0/1]</td>
                                        </tr>
                                        <tr>
                                            <td>service_notifications_enabled</td>
                                            <td>[0/1]</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_period</td>
                                            <td>timeperiod name</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_period</td>
                                            <td>timeperiod name</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_options</td>
                                            <td>[d,u,r,f,s,n]</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_options</td>
                                            <td>[w,u,c,r,f,s,n]</td>
                                        </tr>
                                        <tr>
                                            <td>host_notification_commands</td>
                                            <td>command name</td>
                                        </tr>
                                        <tr>
                                            <td>service_notification_commands</td>
                                            <td>command name</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal contact directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-contact"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-contact">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/contact/testcontact?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;contact_name=jsmith&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated jsmith in the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-contactgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/contactgroup/&lt;contactgroup_name&gt;</h4>
                    <p><?php echo _('This endpoint edits a contact group object.'); ?> <?php echo sprintf(_('If you have special characters in the contactgroup_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/contactgroup</code>', '<code>old_contactgroup_name=&lt;contactgroup_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contactgroup_name</td>
                                            <td>contact group name</td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                        <tr>
                                            <td>members</td>
                                            <td>contact names</td>
                                        </tr>
                                        <tr>
                                            <td>contactgroup_members</td>
                                            <td>contact group names</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal contact group directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-contactgroup"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-contactgroup">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/contactgroup/testcontactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;contactgroup_name=sysadmins&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated sysadmins in the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-contact"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/contact <i>or</i> config/contact/&lt;contact_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint deletes a contact object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contact_name</td>
                                            <td>contact name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-contact"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-contact">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;contact_name=testcontact&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed contacts from the system. Config applied, Nagios Core was restarted.",
    "contacts": [
        "testcontact"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-contactgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/contactgroup <i>or</i> config/contactgroup/&lt;contactgroup_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint deletes a contact group object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>contactgroup_name</td>
                                            <td>contact group name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-contactgroup"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-contactgroup">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;contactgroup_name=testcontactgroup&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed contact groups from the system. Config applied, Nagios Core was restarted.",
    "contactgroups": [
        "testcontactgroup"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Time Periods'); ?></h3>

                <a name="get-timeperiod"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> config/timeperiod</h4>
                    <p><?php echo _('This command gets all the configured time periods in the CCM even if they have not been applied. Note: This lists <i>all</i> time periods in the CCM unless filtered by the optional paramters below.'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>timeperiod_name</td>
                                            <td>time period name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-timeperiod"><?php echo _('Copy'); ?></a> <a href="<?php echo get_base_url(); ?>api/v1/config/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a></h6>
                                <pre><code id="cmd-get-timeperiod">curl -XGET "<?php echo get_base_url(); ?>api/v1/config/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre>
                                <h6><?php echo _('Response'); ?>:</h6>
                                <pre>[
    {
        "timeperiod_name": "24x7",
        "alias": "24 Hours A Day, 7 Days A Week",
        "sunday": "00:00-24:00",
        "monday": "00:00-24:00",
        "tuesday": "00:00-24:00",
        "wednesday": "00:00-24:00",
        "thursday": "00:00-24:00",
        "friday": "00:00-24:00",
        "saturday": "00:00-24:00"
    }
]
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="add-timeperiod"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/timeperiod</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint creates a new time period object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>timeperiod_name</td>
                                            <td>time period name </td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>[weekday]</td>
                                            <td>time ranges</td>
                                        </tr>
                                        <tr>
                                            <td>[exception]</td>
                                            <td>time ranges</td>
                                        </tr>
                                        <tr>
                                            <td>exclude</td>
                                            <td>list of time periods</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-add-timeperiod"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-add-timeperiod">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "timeperiod_name=testtimeperiod&amp;alias=newtimeperiod&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Added testtimeperiod to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="edit-timeperiod"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-success">PUT</span> config/timeperiod/&lt;timeperiod_name&gt;</h4>
                    <p><?php echo _('This endpoint edits a time period object.'); ?> <?php echo sprintf(_('If you have special characters in the timeperiod_name and cannot pass it through the path, you can use %s with the %s URL parameter instead.'), '<code>config/timeperiod</code>', '<code>old_timeperiod_name=&lt;timeperiod_name&gt;</code>'); ?></p>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>timeperiod_name</td>
                                            <td>time period name </td>
                                        </tr>
                                        <tr>
                                            <td>alias</td>
                                            <td>alias</td>
                                        </tr>
                                        <tr>
                                            <td>[weekday]</td>
                                            <td>time ranges</td>
                                        </tr>
                                        <tr>
                                            <td>[exception]</td>
                                            <td>time ranges</td>
                                        </tr>
                                        <tr>
                                            <td>exclude</td>
                                            <td>list of time periods</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal command directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-edit-timeperiod"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-edit-timeperiod">curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/timeperiod/testtimeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1&alias=newalias&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Updated testtimeperiod to the system. Config applied, Nagios Core was restarted."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <a name="delete-timeperiod"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-danger">DELETE</span> config/timeperiod <i>or</i> config/timeperiod/&lt;timeperiod_name&gt;</h4>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('This endpoint deletes a time period object.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Required Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>timeperiod_name</td>
                                            <td>time period name</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-delete-timeperiod"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-delete-timeperiod">curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;timeperiod_name=testtimeperiod&amp;applyconfig=1"</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Removed time periods from the system. Config applied, Nagios Core was restarted.",
    "timeperiods": [
        "testtimeperiod"
    ],
    "failed": [

    ]
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="help-section-head"><?php echo _('Raw Import'); ?></h3>

                <a name="import"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-info">POST</span> config/import</h4>
                    <div class="alert alert-warning" style="margin-top: 20px;">
                        <i class="fa fa-14 fa-exclamation-triangle"></i> <?php echo _('<b>Warning!</b> These configurations are not checked for validity. You should be sure your configuration will apply before sending your raw config data to this endpoint.'); ?>
                    </div>
                    <div class="container-fluid" style="padding: 0 5px;">
                        <div class="row">
                            <div class="col-md-5 col-lg-4 col-xl-3">
                                <p><?php echo _('Import raw configurations into the CCM. You can see a full list of configuratin objects that you can use in the') . ' <a href="https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/4/en/objectdefinitions.html" target="_new">'. _('Object Definitions') . '</a> ' . _('section of the Core docs.'); ?></p>
                                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _('Optional Parameters'); ?></th>
                                            <th><?php echo _('Value Type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>overwrite</td>
                                            <td><b>0</b> (default) or 1</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p><em>** <?php echo _('All normal config directives are able to be used like normal core config definition files.'); ?></em></p>
                            </div>
                            <div class="col-md-7 col-lg-8 col-xl-9">
                                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-import"><?php echo _('Copy'); ?></a></h6>
                                <pre><code id="cmd-import">curl -XPOST "<?php echo get_base_url(); ?>api/v1/config/import?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d '
define host {
    host_name           newhost.example
    address             127.0.0.1
    use                 xiwizard_generic_host
    max_check_attempts  5
    check_interval      5
    check_period        xi_timeperiod_24x7
    notification_period xi_timeperiod_24x7
    register            1
}'</code></pre>
                                <h6><?php echo _('Response (Success)'); ?>:</h6>
                                <pre>{
    "success": "Imported configuration data."
}
</pre>
                                <h6><?php echo _('Response (Failure)'); ?>:</h6>
                                <pre>{
    "error": "Import failed. Configuration passed may not be formatted properly."
}
</pre>
                            </div>
                        </div>
                    </div>
                </div>

             </div>
             <div class="col-sm-4 col-md-3 col-lg-3 nav-box">
                <div class="well help-right-nav">
                    <h5><?php echo _('API - Config Reference'); ?></h5>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Hosts'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-host">config/host</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-host">config/host</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-host">config/host/&lt;host_name&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-host">config/host</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Services'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-service">config/service</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-service">config/service</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-service">config/service/&lt;config_name&gt;/&lt;service_description&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-service">config/service</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Groups'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-hostgroup">config/hostgroup</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#get-servicegroup">config/servicegroup</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-hostgroup">config/hostgroup</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-servicegroup">config/servicegroup</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-hostgroup">config/hostgroup/&lt;hostgroup_name&gt;</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-servicegroup">config/servicegroup/&lt;servicegroup_name&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-hostgroup">config/hostgroup</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-servicegroup">config/servicegroup</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Commands'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-command">config/command</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-command">config/command</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-command">config/command/&lt;command_name&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-command">config/command</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Contacts'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-contact">config/contact</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#get-contactgroup">config/contactgroup</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-contact">config/contact</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-contactgroup">config/contactgroup</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-contact">config/contact/&lt;contact_name&gt;</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-contactgroup">config/contactgroup/&lt;contactgroup_name&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-contact">config/contact</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-contactgroup">config/contactgroup</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Time Periods'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#get-timeperiod">config/timeperiod</a></li>
                        <li><span class="label label-info">POST</span> <a href="#add-timeperiod">config/timeperiod</a></li>
                        <li><span class="label label-success">PUT</span> <a href="#edit-timeperiod">config/timeperiod/&lt;timeperiod_name&gt;</a></li>
                        <li><span class="label label-danger">DELETE</span> <a href="#delete-timeperiod">config/timeperiod</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Raw Import'); ?></p>
                    <ul>
                        <li><span class="label label-info">POST</span> <a href="#import">config/import</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}
