<?php
//
// Nagios XI API Documentation
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/html-helpers.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

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

    do_page_start(array("page_title" => _('API - Objects Reference')), true);
?>

    <!-- Keep the help section part of the page -->
    <script src="../includes/js/clipboard.min.js"></script>
    <script src="../includes/js/api-help.js"></script>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">

                <h1><?php echo _('API - Objects Reference'); ?></h1>
                <p><?php echo _('This is a read-only backend for getting the host, services, and other objects.'); ?></p>

                <div class="help-section obj-reference">
                    <a name="building-queries"></a>
                    <h4><?php echo _('Building Limited Queries'); ?></h4>
                    <p><?php echo _('Sometimes you will need to only see a specific subset of data. Since these queries are generally akin to MySQL queries on databases, there are some modifiers that you can add in to get the data that you want. This section will show some examples of these modifiers and give a small reference table of modifiers that are available for these API objects.'); ?></p>
                    <p><?php echo _('Values in <em>italics</em> are considered optional and are not necessary to use the parameter.'); ?> <b class="ref-tt"><?php echo _('Bold and underlined with dots'); ?></b> <?php echo _('means there is a help tooltip or popup describing the functionality of the value. Anything inside parenthesis ( ) is a default value. Anything inside brackets [ ] is an optional additional argument.'); ?></p>
                    <table class="table table-condensed table-bordered table-no-margin">
                        <thead>
                            <tr>
                                <td><?php echo _('Parameter'); ?></td>
                                <td><?php echo _('Values'); ?></td>
                                <td><?php echo _('Examples'); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>pretty</td>
                                <td><b>1</b></td>
                                <td><?php echo _('If the value is 1, the API displays readable JSON. This is helpful when developing and should not be used in a production API call.'); ?></td>
                            </tr>
                            <tr>
                                <td>starttime</td>
                                <td>&lt;timestamp&gt; (Default: -24 hours)</td>
                                <td><code>objects/statehistory?starttime=<?php echo strtotime('-1 week'); ?></code> <a href="<?php echo get_base_url(); ?>api/v1/objects/statehistory?starttime=<?php echo strtotime('-1 week'); ?>&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a> - <?php echo _('Displays the last week of data until now.'); ?></td>
                            </tr>
                            <tr>
                                <td>endttime</td>
                                <td>&lt;timestamp&gt; (Default: now)</td>
                                <td><code>objects/statehistory?starttime=<?php echo strtotime('-2 weeks'); ?>&amp;endtime=<?php echo strtotime('-1 week'); ?></code> <a href="<?php echo get_base_url(); ?>api/v1/objects/statehistory?starttime=<?php echo strtotime('-2 weeks'); ?>&amp;endtime=<?php echo strtotime('-1 week'); ?>&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a> - <?php echo _('Displays 1 week of data starting 2 weeks ago. Should be used with starttime.'); ?></td>
                            </tr>
                            <tr>
                                <td>records</td>
                                <td>&lt;amount&gt;<em>:&lt;starting at&gt;</em></td>
                                <td>
                                    <div><code>objects/hoststatus?records=1</code> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?records=1&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a> - <?php echo _('Displays only the first record.'); ?></div>
                                    <div><code>objects/hoststatus?records=10:20</code> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?records=10:20&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a> - <?php echo _('Displays the the next 10 records after the 20th record.'); ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td>orderby</td>
                                <td>&lt;column&gt;<em>:&lt;<b class="ref-tt tt-bind" title="<?php echo _('Ascending'); ?> [ 0 to 10 ] [ A to Z ]">a</b> or <b class="ref-tt tt-bind" title="<?php echo _('Descending'); ?> [ 10 to 0 ] [ Z to A ]">d</b>&gt;</em></td>
                                <td>
                                    <div><code>objects/hoststatus?orderby=name:a</code> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?orderby=name:a&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a> - <?php echo _('Displays the items ordered by the name field and ascending values.'); ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td>&lt;column&gt;</td>
                                <td><em class="ref-pop"><b class="ref-tt pop" data-content="<div style='font-style: normal; font-size: 12px;'><table class='table table-condensed table-bordered'><thead><tr><td><?php echo _('Type'); ?></td><td><?php echo _('SQL Equivalent'); ?></td><td><?php echo _('Description'); ?></td></tr></thead><tbody><tr><td></td><td>= value</td><td><?php echo _('Default equals match'); ?></td></tr><tr><td><b>ne:</b></td><td>!= value</td><td><?php echo _('Not equals match'); ?></td></tr><tr><td><b>lt:</b></td><td>&lt; value</td><td><?php echo _('Less than match'); ?></td></tr><tr><td><b>lte:</b></td><td>&lt;= value</td><td><?php echo _('Less than or equal match'); ?></td></tr><tr><td><b>gt:</b></td><td>&gt; value</td><td><?php echo _('Greater than match'); ?></td></tr><tr><td><b>gte:</b></td><td>&gt;= value</td><td><?php echo _('Greater than or equal match'); ?></td></tr><tr><td><b>lks:</b></td><td>LIKE value%</td><td><?php echo _('Beginning of string match'); ?></td></tr><tr><td><b>nlks:</b></td><td>NOT LIKE value%</td><td><?php echo _('Beginning of string non-match'); ?></td></tr><tr><td><b>lke:</b></td><td>LIKE %value</td><td><?php echo _('End of string match'); ?></td></tr><tr><td><b>nlke:</b></td><td>NOT LIKE value%</td><td><?php echo _('End of string non-match'); ?></td></tr><tr><td><b>lk:</b> or <b>lkm:</b></td><td><?php echo _('LIKE %value%'); ?></td><td><?php echo _('General mid string match'); ?></td></tr><tr><td><b>nlk:</b> or <b>nlkm:</b></td><td>NOT LIKE %value%</td><td><?php echo _('General mid string non-match'); ?></td></tr><tr><td><b>in:</b></td><td>IN (values)</td><td><?php echo _('In comma-separated list'); ?></td></tr><tr><td><b>nin:</b></td><td>NOT IN (values)</td><td><?php echo _('Not in comma-separated list'); ?></td></tr></tbody></table></div>">&lt;type&gt;:</b></em>&lt;value&gt;</td>
                                <td>
                                    <div><code>objects/hoststatus?name=lk:local</code> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?name=lk:local&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a>  - <?php echo _("Displays any matching name with 'local' anywhere in the string."); ?></div>
                                    <div><code>objects/hoststatus?name=in:localhost,nagios,testhost</code> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?name=in:localhost,nagios,testhost&amp;pretty=1&amp;apikey=<?php echo $apikey; ?>" target="_blank" rel="noreferrer" class="tt-bind" style="vertical-align: middle;" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share"></i></a>  - <?php echo _("Displays any matching name with the comma-separated list."); ?></div>
                                    <div><b><?php echo _('Note'); ?>:</b> <?php echo _('You can use multiple different column names in a row such as:'); ?> <code>host_name=localhost&amp;current_state=1</code></div>
                                </td>
                            </tr>
                            <tr>
                                <td>outputtype</td>
                                <td><i>json</i> or <i>xml</i> or <i>csv</i> (Default: json)</td>
                                <td>
                                    <div><?php echo _('Use this variable to get XML or CSV output instead of JSON (default).'); ?></div>
                                    <div><?php echo _('The pretty parameter only applies to JSON output.'); ?></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="help-section-head"><?php echo _('Basic Objects'); ?></h3>

                <a name="objects-hoststatus"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/hoststatus</h4>
                    <p><?php echo _('This command returns the current host status.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-hoststatus"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-hoststatus">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/hoststatus?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/hoststatus?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "hoststatus": [
        {
            "host_object_id": "145",
            "host_name": "localhost",
            "host_alias": "localhost",
            "display_name": "localhost",
            "address": "127.0.0.1",
            "icon_image": "",
            "icon_image_alt": "",
            "notes": "",
            "notes_url": "",
            "action_url": "",
            "hoststatus_id": "3",
            "instance_id": "1",
            "status_update_time": "2020-04-06 14:18:15",
            "output": "OK - 127.0.0.1: rta 0.011ms, lost 0%",
            "long_output": "",
            "perfdata": "rta=0.011ms;3000.000;5000.000;0; pl=0%;80;100;; rtmax=0.034ms;;;; rtmin=0.005ms;;;;",
            "current_state": "0",
            "has_been_checked": "1",
            "should_be_scheduled": "1",
            "current_check_attempt": "1",
            "max_check_attempts": "10",
            "last_check": "2020-04-06 14:18:15",
            "next_check": "2020-04-06 14:23:15",
            "check_type": "0",
            "check_options": "0",
            "last_state_change": "2020-02-01 16:10:11",
            "last_hard_state_change": "2020-02-01 16:10:11",
            "last_hard_state": "0",
            "last_time_up": "2020-04-06 14:18:15",
            "last_time_down": "1969-12-31 18:00:00",
            "last_time_unreachable": "1969-12-31 18:00:00",
            "state_type": "1",
            "last_notification": "1969-12-31 18:00:00",
            "next_notification": "1969-12-31 18:00:00",
            "no_more_notifications": "0",
            "notifications_enabled": "1",
            "problem_has_been_acknowledged": "0",
            "acknowledgement_type": "0",
            "current_notification_number": "0",
            "passive_checks_enabled": "1",
            "active_checks_enabled": "1",
            "event_handler_enabled": "1",
            "flap_detection_enabled": "1",
            "is_flapping": "0",
            "percent_state_change": "0",
            "latency": "0.00153100001625717",
            "execution_time": "0.001404",
            "scheduled_downtime_depth": "0",
            "failure_prediction_enabled": "0",
            "process_performance_data": "1",
            "obsess_over_host": "1",
            "modified_host_attributes": "0",
            "event_handler": "",
            "check_command": "check-host-alive!!!!!!!!",
            "normal_check_interval": "5",
            "retry_check_interval": "1",
            "check_timeperiod_object_id": "129"
        }
    ]
}</pre>
                </div>

                <a name="objects-servicestatus"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/servicestatus</h4>
                    <p><?php echo _('This command returns the current service status.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-servicestatus"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-servicestatus">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/servicestatus?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/servicestatus?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "servicestatus": [
        {
            "host_name": "localhost",
            "service_description": "Current Load",
            "display_name": "Current Load",
            "host_object_id": "145",
            "host_address": "127.0.0.1",
            "host_alias": "localhost",
            "icon_image": "",
            "icon_image_alt": "",
            "notes": "Testing 123",
            "notes_url": "",
            "action_url": "",
            "servicestatus_id": "21",
            "instance_id": "1",
            "service_object_id": "157",
            "status_update_time": "2020-04-06 14:16:30",
            "output": "OK - load average: 0.26, 0.32, 0.30",
            "long_output": "",
            "perfdata": "load1=0.260;5.000;10.000;0; load5=0.320;4.000;6.000;0; load15=0.300;3.000;4.000;0;",
            "current_state": "0",
            "has_been_checked": "1",
            "should_be_scheduled": "1",
            "current_check_attempt": "1",
            "max_check_attempts": "4",
            "last_check": "2020-04-06 14:16:30",
            "next_check": "2020-04-06 14:21:30",
            "check_type": "0",
            "check_options": "0",
            "last_state_change": "2020-02-01 16:10:38",
            "last_hard_state_change": "2020-02-01 16:10:38",
            "last_hard_state": "0",
            "last_time_ok": "2020-04-06 14:16:30",
            "last_time_warning": "1969-12-31 18:00:00",
            "last_time_unknown": "1969-12-31 18:00:00",
            "last_time_critical": "1969-12-31 18:00:00",
            "state_type": "1",
            "last_notification": "1969-12-31 18:00:00",
            "next_notification": "1969-12-31 18:00:00",
            "no_more_notifications": "0",
            "notifications_enabled": "1",
            "problem_has_been_acknowledged": "0",
            "acknowledgement_type": "0",
            "current_notification_number": "0",
            "passive_checks_enabled": "1",
            "active_checks_enabled": "1",
            "event_handler_enabled": "1",
            "flap_detection_enabled": "1",
            "is_flapping": "0",
            "percent_state_change": "0",
            "latency": "0.00219999998807907",
            "execution_time": "0.00254",
            "scheduled_downtime_depth": "0",
            "failure_prediction_enabled": "0",
            "process_performance_data": "1",
            "obsess_over_service": "1",
            "modified_service_attributes": "0",
            "event_handler": "",
            "check_command": "check_local_load!5.0,4.0,3.0!10.0,6.0,4.0\\!test!!!!!!",
            "normal_check_interval": "5",
            "retry_check_interval": "1",
            "check_timeperiod_object_id": "129"
        }
    ]
}</pre>
                </div>

                <a name="objects-logentries"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/logentries</h4>
                    <p><?php echo _('This command returns a list of log entries.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-logentries"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-logentries">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/logentries?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/logentries?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 3,
    "logentry": [
        {
            "logentry_id": "792645",
            "entry_time": "2020-04-06 14:20:19",
            "logentry_type": "1048576",
            "logentry_data": "SERVICE NOTIFICATION: nagiosadmin;localhost;Memory Usage;WARNING;xi_service_notification_handler;WARNING: Used memory was 54.80 %",
            "instance_id": "1"
        },
        {
            "logentry_id": "792644",
            "entry_time": "2020-04-06 14:10:07",
            "logentry_type": "1048576",
            "logentry_data": "SERVICE NOTIFICATION: nagiosadmin;localhost;Connections;CRITICAL;xi_service_notification_handler;CRITICAL: Connections Active was 675523.00",
            "instance_id": "1"
        },
        {
            "logentry_id": "792643",
            "entry_time": "2020-04-06 14:03:42",
            "logentry_type": "1048576",
            "logentry_data": "SERVICE NOTIFICATION: nagiosadmin;localhost;User Count;CRITICAL;xi_service_notification_handler;CRITICAL: Count was 5 users",
            "instance_id": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-statehistory"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/statehistory</h4>
                    <p><?php echo _('This command returns a list of state changes.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-statehistory"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-statehistory">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/statehistory?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/statehistory?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "stateentry": [
        {
            "host_name": "localhost,
            "service_description": "CPU Usage",
            "objecttype_id": "2",
            "object_id": "171",
            "state_time": "2020-04-06 09:47:03",
            "state_change": "1",
            "state": "0",
            "state_type": "0",
            "current_check_attempt": "3",
            "max_check_attempts": "5",
            "last_state": "1",
            "last_hard_state": "0",
            "instance_id": "1",
            "output": "OK: Percent was 2.16 %"
        }
    ]
}</pre>
                </div>

                <a name="objects-comment"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/comment</h4>
                    <p><?php echo _('This command returns a list of comments.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-comment"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-comment">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/comment?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/comment?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "comment": [
        {
            "instance_id": "1",
            "object_id": "161",
            "objecttype_id": "2",
            "host_name": "localhost",
            "service_description": "PING",
            "comment_id": "4",
            "entry_time": "2020-04-06 13:23:49",
            "entry_time_usec": "458412",
            "comment_type": "2",
            "entry_type": "2",
            "comment_time": "2020-04-06 13:23:49",
            "internal_comment_id": "228",
            "author_name": "nagiosadmin",
            "comment_data": "This service has been scheduled for fixed downtime from 04-08-2020 09:20:00 to 04-08-2020 10:20:00.",
            "is_persistent": "0",
            "comment_source": "0",
            "expires": "0",
            "expiration_time": "1969-12-31 18:00:00"
        }
    ]
}</pre>
                </div>

                <a name="objects-downtime"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/downtime</h4>
                    <p><?php echo _('This command returns a list of scheduled downtimes.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-downtime"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-downtime">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/downtime?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/downtime?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "scheduleddowntime": [
        {
            "instance_id": "1",
            "instance_name": "localhost",
            "object_id": "161",
            "objecttype_id": "2",
            "host_name": "localhost",
            "service_description": "PING",
            "scheduleddowntime_id": "2",
            "downtime_type": "1",
            "entry_time": "2020-04-02 10:01:01",
            "author_name": "nagiosadmin",
            "comment_data": "AUTO: test",
            "internal_downtime_id": "52",
            "triggered_by_id": "0",
            "is_fixed": "1",
            "duration": "3600",
            "scheduled_start_time": "2020-04-08 09:20:00",
            "scheduled_end_time": "2020-04-08 10:20:00",
            "was_started": "0",
            "actual_start_time": "1970-01-01 00:00:01",
            "actual_start_time_usec": "0"
        }
    ]
}</pre>
                </div>

                <a name="objects-contact"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/contact</h4>
                    <p><?php echo _('This command returns a list of contacts.'); ?></p>
                    <p><?php echo _('Adding the'); ?> <code>customvars=1</code> <?php echo _('value to the URL will output custom variables for the contacts.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-contact"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-contact">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/contact?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "contact": [
        {
            "object_id": "140",
            "contact_name": "xi_default_contact",
            "instance_id": "1",
            "config_type": "1",
            "contact_object_id": "140",
            "alias": "Default Contact",
            "email_address": "root@localhost",
            "pager_address": "",
            "minimum_importance": "0",
            "host_timeperiod_object_id": "137",
            "service_timeperiod_object_id": "137",
            "host_notifications_enabled": "1",
            "service_notifications_enabled": "1",
            "can_submit_commands": "1",
            "notify_service_recovery": "0",
            "notify_service_warning": "0",
            "notify_service_unknown": "0",
            "notify_service_critical": "0",
            "notify_service_flapping": "0",
            "notify_service_downtime": "0",
            "notify_host_recovery": "0",
            "notify_host_down": "0",
            "notify_host_unreachable": "0",
            "notify_host_flapping": "0",
            "notify_host_downtime": "0",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-host"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/host</h4>
                    <p><?php echo _('This command returns a list of hosts.'); ?></p>
                    <p><?php echo _('Adding the'); ?> <code>customvars=1</code> <?php echo _('value to the URL will output custom variables for the hosts.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-host"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-host">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/host?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/host?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "host": [
        {
            "host_name": "localhost",
            "object_id": "145",
            "host_object_id": "145",
            "alias": "localhost",
            "display_name": "localhost",
            "address": "127.0.0.1",
            "check_interval": "5",
            "retry_interval": "5",
            "max_check_attempts": "10",
            "first_notification_delay": "0",
            "notification_interval": "60",
            "passive_checks_enabled": "1",
            "active_checks_enabled": "1",
            "notifications_enabled": "1",
            "notes": "",
            "notes_url": "",
            "action_url": "",
            "icon_image": "",
            "icon_image_alt": "",
            "statusmap_image": "",
            "config_type": "1",
            "instance_id": "1",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-service"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/service</h4>
                    <p><?php echo _('This command returns a list of services.'); ?></p>
                    <p><?php echo _('Adding the'); ?> <code>customvars=1</code> <?php echo _('value to the URL will output custom variables for the services.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-service"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-service">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/service?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/service?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "service": [
        {
            "host_name": "localhost",
            "service_description": "Memory Usage",
            "object_id": "160",
            "host_object_id": "160",
            "display_name": "Memory Usage",
            "check_interval": "5",
            "retry_interval": "5",
            "max_check_attempts": "4",
            "first_notification_delay": "0",
            "notification_interval": "60",
            "passive_checks_enabled": "1",
            "active_checks_enabled": "1",
            "notifications_enabled": "1",
            "notes": "",
            "notes_url": "",
            "action_url": "",
            "icon_image": "",
            "icon_image_alt": "",
            "config_type": "1",
            "instance_id": "1",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-hostgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/hostgroup</h4>
                    <p><?php echo _('This command returns a list of host groups.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-hostgroup"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-hostgroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/hostgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 2,
    "hostgroup": [
        {
            "instance_id": "1",
            "config_type": "1",
            "hostgroup_object_id": "146",
            "alias": "Linux Servers",
            "object_id": "146",
            "hostgroup_name": "linux-servers",
            "is_active": "1"
        },
        {
            "instance_id": "1",
            "config_type": "1",
            "hostgroup_object_id": "147",
            "alias": "test",
            "object_id": "147",
            "hostgroup_name": "windows-servers",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-servicegroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/servicegroup</h4>
                    <p><?php echo _('This command returns a list of service groups.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-servicegroup"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-servicegroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/servicegroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 2,
    "servicegroup": [
        {
            "instance_id": "1",
            "config_type": "1",
            "servicegroup_object_id": "183",
            "alias": "",
            "object_id": "183",
            "servicegroup_name": "Group 1",
            "is_active": "1"
        },
        {
            "instance_id": "1",
            "config_type": "1",
            "servicegroup_object_id": "170",
            "alias": "",
            "object_id": "170",
            "servicegroup_name": "Group 2 servers",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-contactgroup"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/contactgroup</h4>
                    <p><?php echo _('This command returns a list of contact groups.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-contactgroup"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-contactgroup">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/contactgroup?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 2,
    "contactgroup": [
        {
            "object_id": "141",
            "contactgroup_name": "admins",
            "alias": "Nagios Administrators",
            "contactgroup_object_id": "141",
            "config_type": "1",
            "instance_id": "1",
            "is_active": "1"
        },
        {
            "object_id": "142",
            "contactgroup_name": "xi_contactgroup_all",
            "alias": "All Contacts",
            "contactgroup_object_id": "142",
            "config_type": "1",
            "instance_id": "1",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <a name="objects-timeperiod"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/timeperiod</h4>
                    <p><?php echo _('This command returns a list of all defined time periods.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-timeperiod"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-timeperiod">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/timeperiod?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 1,
    "timeperiod": [
        {
            "timeperiod_id": "1",
            "object_id": "129",
            "timeperiod_name": "24x7",
            "alias": "24 Hours A Day, 7 Days A Week",
            "instance_id": "1",
            "config_type": "1",
            "timeperiod_object_id": "129",
            "objecttype_id": "9",
            "is_active": "1"
        }
    ]
}</pre>
                </div>

                <h3 class="help-section-head"><?php echo _('Unconfigured Objects'); ?></h3>

                <a name="objects-unconfigured"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/unconfigured</h4>
                    <p><?php echo _('This command returns a list of host group members.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-unconfigured"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-unconfigured">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/unconfigured?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/unconfigured?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    recordcount": 2,
    "unconfigured": [
        {
            "host_name": "Test Host",
            "last_seen": 1537666221
        },
        {
            "host_name": "Test Host",
            "service_description": "Test Service",
            "last_seen": 1537666221
        }
    ]
}</pre>
                </div>

                <h3 class="help-section-head"><?php echo _('Group Members'); ?></h3>

                <a name="objects-hostgroupmembers"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/hostgroupmembers</h4>
                    <p><?php echo _('This command returns a list of host group members.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-hostgroupmembers"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-hostgroupmembers">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/hostgroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/hostgroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 2,
    "hostgroup": [
        {
            "hostgroup_object_id": "147",
            "hostgroup_name": "windows-servers",
            "instance_id": "1",
            "members": {
                "host": [
                    {
                        "host_object_id": "145",
                        "host_name": "localhost"
                    },
                    {
                        "host_object_id": "143",
                        "host_name": "192.168.1.10"
                    }
                ]
            }
        }
    ]
}</pre>
                </div>

                <a name="objects-servicegroupmembers"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/servicegroupmembers</h4>
                    <p><?php echo _('This command returns a list of service group members.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-servicegroupmembers"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-servicegroupmembers">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/servicegroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/servicegroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 4,
    "servicegroup": [
        {
            "servicegroup_object_id": "170",
            "servicegroup_name": "test group",
            "instance_id": "1",
            "members": {
                "service": [
                    {
                        "service_object_id": "158",
                        "host_name": "localhost",
                        "service_description": "Current Users"
                    },
                    {
                        "service_object_id": "157",
                        "host_name": "localhost",
                        "service_description": "Current Load"
                    }
                ]
            }
        },
        {
            "servicegroup_object_id": "183",
            "servicegroup_name": "test group 2",
            "instance_id": "1",
            "members": {
                "service": [
                    {
                        "service_object_id": "169",
                        "host_name": "localhost",
                        "service_description": "someservice"
                    },
                    {
                        "service_object_id": "171",
                        "host_name": "localhost",
                        "service_description": "CPU Usage"
                    }
                ]
            }
        }
    ]
}</pre>
                </div>

                <a name="objects-contactgroupmembers"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/contactgroupmembers</h4>
                    <p><?php echo _('This command returns a list of contact group members.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-contactgroupmembers"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-contactgroupmembers">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/contactgroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/contactgroupmembers?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "recordcount": 3,
    "contactgroup": [
        {
            "contactgroup_object_id": "142",
            "contactgroup_name": "xi_contactgroup_all",
            "instance_id": "1",
            "members": {
                "contact": [
                    {
                        "contact_object_id": "139",
                        "contact_name": "testuser"
                    },
                    {
                        "contact_object_id": "138",
                        "contact_name": "nagiosadmin"
                    }
                ]
            }
        },
        {
            "contactgroup_object_id": "141",
            "contactgroup_name": "admins",
            "instance_id": "1",
            "members": {
                "contact": [
                    {
                        "contact_object_id": "138",
                        "contact_name": "nagiosadmin"
                    }
                ]
            }
        }
    ]
}</pre>
                </div>

                <h3 class="help-section-head"><?php echo _('Data Exporting'); ?></h3>

                <a name="objects-rrdexport"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/rrdexport</h4>
                    <p><?php echo _('This command returns an exported RRD performance data file.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div style="margin-bottom: 20px;">
                        <h6>Arguments:</h6>
                        <?php
                            $rrd_argument_table = new help_table("Parameter", "Required", "Description", "Default");
                            $rrd_argument_table
                                ->tr(_("host_name"),            _("Yes"),   _("The name of the host to grab performance data for."),                        "")
                                ->tr(_("service_description"),  _("No"),    _("The name of the service to grab performance data for."),                     _("_HOST_"))
                                ->tr(_("start"),                _("No"),    _("Datetime to specify the start of the exported data, in Unix timestamp."),    _("NOW - 24 Hours"))
                                ->tr(_("end"),                  _("No"),    _("Datetime to specify the end of the exported data, in Unix timestamp."),      _("NOW"))
                                ->tr(_("step"),                 _("No"),    _("Interval between data points, in seconds."),                                 _("300"))
                                ->tr(_("maxrows"),              _("No"),    _("Maximum amount of rows to return."),                                         _("400"))
                                ->tr(_("columns[]"),            _("No"),    _("Array of columns to display (e.g.: &amp;columns[]=pl&amp;columns[]=rta)."),  _("All available"))
                                ->write();
                        ?>
                    </div>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-rrdexport"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-rrdexport">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/rrdexport?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host_name=localhost"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/rrdexport?apikey=<?php echo $apikey; ?>&pretty=1&host_name=localhost" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "meta": {
        "start": "1453838100",
        "step": "300",
        "end": "1453838400",
        "rows": "2",
        "columns": "4",
        "legend": {
            "entry": [
                "rta",
                "pl",
                "rtmax",
                "rtmin"
            ]
        }
    },
    "data": {
        "row": [
            {
                "t": "1453838100",
                "v": [
                    "6.0373333333e-03",
                    "0.0000000000e+00",
                    "1.7536000000e-02",
                    "3.0000000000e-03"
                ]
            },
            {
                "t": "1453838400",
                "v": [
                    "6.0000000000e-03",
                    "0.0000000000e+00",
                    "1.7037333333e-02",
                    "3.0000000000e-03"
                ]
            }
        ]
    }
}
</pre>
                </div>

                <a name="objects-cpexport"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/cpexport</h4>
                    <p><?php echo _('This command returns the historical and predicted performance data for a host, service, and RRD track. This is essentially a stripped down version of the Capacity Planning report and allows running the Capacity Planning script on a single object for inclusion elsewhere.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div style="margin-bottom: 20px;">
                        <h6>Arguments:</h6>
                        <?php
                            $cp_argument_table = new help_table("Parameter", "Required", "Description", "Default");
                            $cp_argument_table
                                ->tr("host_name",            _("Yes"),   _("The name of the host to use for capacity planning."),                        "")
                                ->tr("service_description",  _("Yes"),   _("The name of the service to use for capacity planning."),                     "")
                                ->tr("track",                _("Yes"),   _("The name of the track in the RRD file for use in capacity planning. You can get this from the Capacity Planning report."), "")
                                ->tr("period",               _("No"),    _("Number of weeks or months to run prediction for."),                          "1 week")
                                ->tr("method",               _("No"),    _("Type of prediction method. Choose from:")." Holt-Winters, Linear Fit, Quadratic Fit, Cubic Fit",      "Holt-Winters")
                                ->write();
                        ?>
                    </div>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-cpexport"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-cpexport">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/cpexport?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host_name=localhost&amp;service_description=PING&amp;track=rta"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/cpexport?apikey=<?php echo $apikey; ?>&pretty=1&host_name=localhost&service_description=PING&track=rta" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
<pre>{
    "emax": 0.054638913633013,
    "eslope": -6.6762593089667e-9,
    "evalue_max": 0.048465071737742,
    "t_start": 1461607200,
    "warn_level": 100,
    "dmean": 0.049758636574074,
    "t_stop": 1463423400,
    "dmax": 0.066815333333333,
    "integrity": 0.99851411589896,
    "unit": "ms",
    "residue": 0.022304421929443,
    "emean": 0.043936264308191,
    "edate": 1463421600,
    "evalue": 0.042703899629423,
    "nd": 672,
    "ne": 336,
    "evalue_min": 0.036942727521105,
    "adjusted_sigma": 0.0057697452810991,
    "t_step": 1800,
    "crit_level": 500,
    "f_of_x_on_date": 0.041683033313948,
    "sigma": 0.0057611721083188,
    "data": {
        "historical": {
            "1461607200": 0.054187444444444,
            "1461609000": 0.046684444444444,
            "1461610800": 0.050581666666667,
            ...
        },
        "predicted": {
            "1462818600": 0.044343964403375,
            "1462820400": 0.042981548657107,
            "1462822200": 0.041565485688109,
            ...
        }
    },
    "name": "localhost_PING_rta"
}</pre>
                </div>

                <a name="objects-hostavailability"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/hostavailability</h4>
                    <p><?php echo _('This endpoint returns host availability information for a speficied, host, hostgroup, or servicegroup. While there are no required paramteres, you must have at least one host, service, hostgroup, or servicegroup in your query.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div style="margin-bottom: 20px;">
                        <h6>Arguments:</h6>
                        <?php
                            $av_argument_table = new help_table("Parameter", "Required", "Description", "Default");
                            $av_argument_table
                                ->tr("host",            _("No"),   _("The name of the host to use. Optional but one object must be selected."),          "")
                                ->tr("hostgroup",       _("No"),   _("The name of the hostgroup to use. Optional but one object must be selected."),     "")
                                ->tr("servicegroup",    _("No"),   _("The name of the servicegroup to use. Optional but one object must be selected."),  "")
                                ->tr('starttime', _("No"), _("Start time timestamp."), "-24 hours")
                                ->tr('endtime', _("No"), _("End time timestamp."), "now")
                                ->tr('assumeinitialstates', _("No"), _("Set to 'yes' or 'no' to assume initial states or not."), "yes")
                                ->tr('assumestateretention', _("No"), _("Set to 'yes' or 'no' to assume state retention or not."), "yes")
                                ->tr('assumestatesduringdowntime', _("No"), _("Set to 'yes' or 'no' to assume states during downtime."), "yes")
                                ->tr('includesoftstates', _("No"), _("Set to 'yes' or 'no' to include soft states or not."), "no")
                                ->tr('assumedhoststate', _("No"), _("Set the assumed host state to 0 = Unspecified, -1 = Current State, 3 = Host Up, 4 = Host Down, 5 = Host Unreachable"), "3")
                                ->tr('assumedservicestate', _("No"), _("Set the assumed service state to 0 = Unspecified, -1 = Current State, 6 = Service Ok, 7 = Service Unknown, 8 = Service Warning, 9 = Service Critical"), "6")
                                ->write();
                        ?>
                    </div>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-hostavailability"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-hostavailability">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/hostavailability?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host=localhost"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/hostavailability?apikey=<?php echo $apikey; ?>&pretty=1&host=localhost" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
<pre>
{
    "hostavailability": [
        {
            "host_name": "localhost",
            "time_up_scheduled": " 0",
            "percent_time_up_scheduled": 0,
            "percent_known_time_up_scheduled": 0,
            "time_up_unscheduled": " 86400",
            "percent_time_up_unscheduled": 100,
            "percent_known_time_up_unscheduled": 100,
            "total_time_up": " 86400",
            "percent_total_time_up": 100,
            "percent_known_time_up": 100,
            "time_down_scheduled": " 0",
            "percent_time_down_scheduled": 0,
            "percent_known_time_down_scheduled": 0,
            "time_down_unscheduled": " 0",
            "percent_time_down_unscheduled": 0,
            "percent_known_time_down_unscheduled": 0,
            "total_time_down": " 0",
            "percent_total_time_down": 0,
            "percent_known_time_down": 0,
            "time_unreachable_scheduled": " 0",
            "percent_time_unreachable_scheduled": 0,
            "percent_known_time_unreachable_scheduled": 0,
            "time_unreachable_unscheduled": " 0",
            "percent_time_unreachable_unscheduled": 0,
            "percent_known_time_unreachable_unscheduled": 0,
            "total_time_unreachable": " 0",
            "percent_total_time_unreachable": 0,
            "percent_known_time_unreachable": 0,
            "time_undetermined_not_running": " 0",
            "percent_time_undetermined_not_running": 0,
            "time_undetermined_no_data": " 0",
            "percent_time_undetermined_no_data": 0,
            "total_time_undetermined": " 0",
            "percent_total_time_undetermined": 0
        }
    ]
}
</pre>
                </div>

                <a name="objects-serviceavailability"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/serviceavailability</h4>
                    <p><?php echo _('This endpoint returns service availability information for a speficied, host, service, hostgroup, or servicegroup. You can define a host and service at the same time. While there are no required paramteres, you must have at least one host, service, hostgroup, or servicegroup in your query.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div style="margin-bottom: 20px;">
                        <h6>Arguments:</h6>
                        <?php
                            $av_argument_table = new help_table("Parameter", "Required", "Description", "Default");
                            $av_argument_table
                                ->tr("host",            _("No"),   _("The name of the host to use. Optional but one object must be selected."),          "")
                                ->tr("service",         _("No"),   _("The name of the service to use. Optional but one object must be selected."),       "")
                                ->tr("hostgroup",       _("No"),   _("The name of the hostgroup to use. Optional but one object must be selected."),     "")
                                ->tr("servicegroup",    _("No"),   _("The name of the servicegroup to use. Optional but one object must be selected."),  "")
                                ->tr('starttime', _("No"), _("Start time timestamp."), "-24 hours")
                                ->tr('endtime', _("No"), _("End time timestamp."), "now")
                                ->tr('assumeinitialstates', _("No"), _("Set to 'yes' or 'no' to assume initial states or not."), "yes")
                                ->tr('assumestateretention', _("No"), _("Set to 'yes' or 'no' to assume state retention or not."), "yes")
                                ->tr('assumestatesduringdowntime', _("No"), _("Set to 'yes' or 'no' to assume states during downtime."), "yes")
                                ->tr('includesoftstates', _("No"), _("Set to 'yes' or 'no' to include soft states or not."), "no")
                                ->tr('assumedhoststate', _("No"), _("Set the assumed host state to 0 = Unspecified, -1 = Current State, 3 = Host Up, 4 = Host Down, 5 = Host Unreachable"), "3")
                                ->tr('assumedservicestate', _("No"), _("Set the assumed service state to 0 = Unspecified, -1 = Current State, 6 = Service Ok, 7 = Service Unknown, 8 = Service Warning, 9 = Service Critical"), "6")
                                ->write();
                        ?>
                    </div>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-serviceavailability"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-serviceavailability">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/serviceavailability?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host=localhost&amp;service=PING"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/serviceavailability?apikey=<?php echo $apikey; ?>&pretty=1&host=localhost&service=PING" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
<pre>
{
    "serviceavailability": [
        {
            "host_name": "localhost",
            "service_description": "PING",
            "time_ok_scheduled": " 0",
            "percent_time_ok_scheduled": 0,
            "percent_known_time_ok_scheduled": 0,
            "time_ok_unscheduled": " 0",
            "percent_time_ok_unscheduled": 0,
            "percent_known_time_ok_unscheduled": 0,
            "total_time_ok": " 0",
            "percent_total_time_ok": 0,
            "percent_known_time_ok": 0,
            "time_warning_scheduled": " 0",
            "percent_time_warning_scheduled": 0,
            "percent_known_time_warning_scheduled": 0,
            "time_warning_unscheduled": " 0",
            "percent_time_warning_unscheduled": 0,
            "percent_known_time_warning_unscheduled": 0,
            "total_time_warning": " 0",
            "percent_total_time_warning": 0,
            "percent_known_time_warning": 0,
            "time_unknown_scheduled": " 0",
            "percent_time_unknown_scheduled": 0,
            "percent_known_time_unknown_scheduled": 0,
            "time_unknown_unscheduled": " 86400",
            "percent_time_unknown_unscheduled": 100,
            "percent_known_time_unknown_unscheduled": 100,
            "total_time_unknown": " 86400",
            "percent_total_time_unknown": 100,
            "percent_known_time_unknown": 100,
            "time_critical_scheduled": " 0",
            "percent_time_critical_scheduled": 0,
            "percent_known_time_critical_scheduled": 0,
            "time_critical_unscheduled": " 0",
            "percent_time_critical_unscheduled": 0,
            "percent_known_time_critical_unscheduled": 0,
            "total_time_critical": " 0",
            "percent_total_time_critical": 0,
            "percent_known_time_critical": 0,
            "time_undetermined_not_running": " 0",
            "percent_time_undetermined_not_running": 0,
            "time_undetermined_no_data": " 0",
            "percent_time_undetermined_no_data": 0,
            "total_time_undetermined": " 0",
            "percent_total_time_undetermined": 0
        }
    ]
}
</pre>
                </div>

                <a name="objects-sla"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/sla</h4>
                    <p><?php echo _('This endpoint returns SLA information for a speficied, host, service, hostgroup, or servicegroup. You can define a host and service at the same time. While there are no required parameters, you must have at least one host, service, hostgroup, or servicegroup in your query.'); ?><b><?php echo _(' This endpoint returns JSON only.'); ?></b></p>
                    <div style="margin-bottom: 20px;">
                        <h6>Arguments:</h6>
                        <?php
                            $sla_argument_table = new help_table("Parameter", "Required", "Description", "Default");
                            $sla_argument_table
                                ->tr("host",            _("No"),   _("The name of the host to use. Optional but one object must be selected."),          "")
                                ->tr("service",         _("No"),   _("The name of the service to use. Optional but one object must be selected."),       "")
                                ->tr("hostgroup",       _("No"),   _("The name of the hostgroup to use. Optional but one object must be selected."),     "")
                                ->tr("servicegroup",    _("No"),   _("The name of the servicegroup to use. Optional but one object must be selected."),  "")
                                ->tr('showopts',        _("No"),   _("Set to 1 to show the options that were passed to the SLA report."),                "0")
                                ->tr('starttime', _("No"), _("Start time timestamp."), "-24 hours")
                                ->tr('endtime', _("No"), _("End time timestamp."), "now")
                                ->tr('dont_count_downtime', _("No"), _("Set to 1 to not count downtime in SLA report."), "0")
                                ->tr('dont_count_warning', _("No"), _("Set to 1 to not count warning in SLA report."), "0")
                                ->tr('dont_count_unknown', _("No"), _("Set to 1 to not count unknown in SLA report."), "0")
                                ->tr('assumeinitialstates', _("No"), _("Set to 'yes' or 'no' to assume initial states or not."), "yes")
                                ->tr('assumestateretention', _("No"), _("Set to 'yes' or 'no' to assume state retention or not."), "yes")
                                ->tr('assumestatesduringdowntime', _("No"), _("Set to 'yes' or 'no' to assume states during downtime."), "yes")
                                ->tr('includesoftstates', _("No"), _("Set to 'yes' or 'no' to include soft states or not."), "no")
                                ->tr('assumedhoststate', _("No"), _("Set the assumed host state to 0 = Unspecified, -1 = Current State, 3 = Host Up, 4 = Host Down, 5 = Host Unreachable"), "3")
                                ->tr('assumedservicestate', _("No"), _("Set the assumed service state to 0 = Unspecified, -1 = Current State, 6 = Service Ok, 7 = Service Unknown, 8 = Service Warning, 9 = Service Critical"), "6")
                                ->write();
                        ?>
                    </div>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-sla"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-sla">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/sla?apikey=<?php echo $apikey; ?>&amp;pretty=1&amp;host=localhost"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/sla?apikey=<?php echo $apikey; ?>&pretty=1&host=localhost" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <p><?php echo _("The format of the data changes slightly depending on the type of object used. A host SLA report looks different than a service SLA report."); ?></p>
<pre>
{
    "sla": {
        "hosts": {
            "localhost": "100.000"
        },
        "services": {
            "\/ Disk Usage": "0.000",
            "CPU Stats": "0.000",
            "Cron Scheduling Daemon": "0.000",
            "HTTP": "100.000",
            "Load": "79.786",
            "Memory Usage": "0.000",
            "Open Files": "0.000",
            "Root Partition": "0.000",
            "SSH Server": "0.000",
            "Service Status - crond": "100.000",
            "Service Status - httpd": "100.000",
            "Service Status - npcd": "100.000",
            "Service Status - ntpd": "100.000",
            "Swap Usage": "0.000",
            "Total Processes": "0.000",
            "Users": "0.000",
            "Yum Updates": "0.000",
            "average": "37.766"
        }
    }
}
</pre>
                </div>

                <a name="objects-bpi"></a>
                <div class="help-section obj-reference">
                    <h4><span class="label label-primary">GET</span> objects/bpi</h4>
                    <p><?php echo _('This endpoint returns BPI group summary information in a condensed format.'); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-get-bpi"><?php echo _('Copy'); ?></a></h6>
                        <pre class="curl-request"><code id="cmd-get-bpi">curl -XGET "<?php echo get_base_url(); ?>api/v1/objects/bpi?apikey=<?php echo $apikey; ?>&amp;pretty=1"</code></pre> <a href="<?php echo get_base_url(); ?>api/v1/objects/bpi?apikey=<?php echo $apikey; ?>&pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
<pre>
{
    "windows-servers": {
        "name": "windows-servers",
        "title": "HG: windows-servers",
        "type": "hostgroup",
        "status_text": "Group health is 50% with 2 problem(s).",
        "current_state": 0,
        "problems": 2,
        "health": 50,
        "warning": 0,
        "critical": 0
    }
}
</pre>
                </div>

             </div>
             <div class="col-sm-4 col-md-3 col-lg-3 nav-box">
                <div class="well help-right-nav">
                    <h5><?php echo _('API - Objects Reference'); ?></h5>
                    <a href="#building-queries"><?php echo _('Building Limited Queries'); ?></a>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Basic Objects'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#objects-hoststatus">objects/hoststatus</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-servicestatus">objects/servicestatus</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-logentries">objects/logentries</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-statehistory">objects/statehistory</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-comment">objects/comment</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-downtime">objects/downtime</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-contact">objects/contact</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-host">objects/host</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-service">objects/service</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-hostgroup">objects/hostgroup</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-servicegroup">objects/servicegroup</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-contactgroup">objects/contactgroup</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-timeperiod">objects/timeperiod</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Unconfigured Objects'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#objects-unconfigured">objects/unconfigured</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Group Members'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#objects-hostgroupmembers">objects/hostgroupmembers</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-servicegroupmembers">objects/servicegroupmembers</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-contactgroupmembers">objects/contactgroupmembers</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _('Data Exporting'); ?></p>
                    <ul>
                        <li><span class="label label-primary">GET</span> <a href="#objects-rrdexport">objects/rrdexport</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-cpexport">objects/cpexport</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-hostavailability">objects/hostavailability</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-serviceavailability">objects/serviceavailability</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-sla">objects/sla</a></li>
                        <li><span class="label label-primary">GET</span> <a href="#objects-bpi">objects/bpi</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

<?php
    do_page_end(true);
}
