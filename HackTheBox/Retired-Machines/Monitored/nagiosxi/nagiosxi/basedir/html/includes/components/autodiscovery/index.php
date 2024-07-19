<?php
//
// Auto-Discovery Component
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/../../../config/deployment/includes/utils-deployment.inc.php');

// Initialization stuff
pre_init();
init_session(true);

// Grab GET or POST variables 
grab_request_vars();
check_prereqs();
check_authentication(false);

if (!is_authorized_to_configure_objects() || is_readonly_user()) {
    header("Location: " . get_base_url());
    exit();
}

route_request();

function route_request()
{
    global $request;

    // Check installation
    $installok = autodiscovery_component_checkinstall($installed, $prereqs, $missing_components);
    if (!$installok) {
        display_install_error($installed, $prereqs, $missing_components);
    }

    $mode = grab_request_var("mode");
    switch ($mode) {
        case "newjob":
        case "editjob":
            $cancelButton = grab_request_var("cancelButton");
            if ($cancelButton) {
                display_jobs();
                break;
            }
            $update = grab_request_var("update");
            if ($update == 1)
                do_update_job();
            else
                display_add_job();
            break;
        case "deletejob":
            do_delete_job();
            break;
        case "viewjob":
            do_view_job();
            break;
        case "runjob":
            do_run_job();
            break;
        case "processjob":
            do_process_job();
            break;
        case "csv":
            do_csv();
            break;
        case "jobcomplete":
            is_job_complete();
        default:
            display_jobs();
            break;
    }
}


function do_csv()
{
    $jobid = grab_request_var("job");
    $show_old = grab_request_var("showold", 0);

    $services = autodiscovery_component_parse_job_data($jobid);

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"autodiscovery.csv\"");

    echo "address,hostname,type,os,status\n";

    foreach ($services as $address => $arr) {
        if ($show_old == 0 && $arr["status"] == "old") {
            continue;
        }
        echo $arr["address"] . "," . $arr["fqdns"] . "," . $arr["type"] . "," . $arr["os"] . "," . ucwords($arr["status"]) . "\n";
    }

    exit();
}


function is_job_complete()
{
    $jobid = grab_request_var('jobid');
    $output_file = get_component_dir_base("autodiscovery") . "/jobs/" . $jobid . ".xml";
    $error = false; // Place for errors when situation arises
    $total_hosts = 0;
    $new_hosts = 0;
    $xml = @simplexml_load_file($output_file);
    if ($xml) {
        foreach ($xml->device as $d) {
            $status = strval($d->status);
            if ($status == "new") {
                $new_hosts++;
            }
            $total_hosts++;
        }
    }
    $jobdone = file_exists($output_file);

    if ($jobdone && !$xml) {
        $error = 'XML was not valid.';
    }

    $json = array('jobdone' => $jobdone,
        'error' => $error,
        'total_hosts' => $total_hosts,
        'new_hosts' => $new_hosts,
        'jobid' => $jobid
    );

    $json_str = json_encode($json);
    header("Content-type: application/json");
    echo $json_str;
    exit();
}


function display_jobs($error = false, $msg = "")
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Generage messages
    if ($msg == "") {
        if (isset($request["jobadded"]))
            $msg = _("Auto-discovery job added.");
        if (isset($request["jobupdated"]))
            $msg = _("Auto-discovery job updated.");
        if (isset($request["jobdeleted"]))
            $msg = _("Job deleted.");
        if (isset($request["jobrun"]))
            $msg = _("Auto-discovery job started.");
    }

    do_page_start(array("page_title" => _("Auto-Discovery Jobs")), true);
?>

<style>
.show-running { display: none; }
</style>

    <h1><?php echo _("Auto-Discovery Jobs"); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <script>
    $(document).ready(function () {
        function get_autodiscovery_jobs() {
            $('.job_throbber').each(function () {
                t_id = $(this).attr('id');
                tag_content = $('#job_info_' + t_id).text();
                if ($.trim(tag_content) == 'N/A') {
                    $(this).parents('tr').find('.hide-running').hide();
                    $(this).parents('tr').find('.show-running').show();
                    var data = {};
                    data.mode = 'jobcomplete';
                    data.jobid = t_id;
                    $.getJSON('index.php',
                        data,
                        function (data) {
                            jobid = data.jobid
                            last_content = $('#job_last_' + jobid).text(); 
                            freq_content = $('#job_freq_' + jobid).text(); 
                            if (data.error) {
                                $('#' + jobid).html(data.error);
                                $('#job_info_' + jobid).html('Error.');
                            } else if (data.jobdone) {
                                $('#' + jobid).html('Finished');
                                job_info = "<b><a class='tt-bind-a' title='<?php echo _('Run the wizard'); ?>' href='?mode=processjob&job=" + encodeURI(jobid) + "'>" + data.new_hosts + " <?php echo _("New") ?></a></b> / " + data.total_hosts + "<?php echo _(" Total") ?>";
                                $('#job_info_' + jobid).html(job_info);
                                $('.tt-bind-a').tooltip();
                            } else if ($.trim(last_content) == ''){
                                $('#' + jobid).html('Waiting');
                            }
                            if (data.error || data.jobdone) {
                                $('#' + jobid).parents('tr').find('.hide-running').show();
                                $('#' + jobid).parents('tr').find('.show-running').hide();
                            }
                        }
                    );
                }
            });
        }

        get_autodiscovery_jobs();
        setInterval(get_autodiscovery_jobs, 10000);
    });
    </script>

    <div style="margin: 5px 0 20px 0;">
        <a href="?mode=newjob" class="btn btn-sm btn-primary">
            <i class="fa fa-plus l"></i>
            <?php echo _('New Auto-Discovery Job'); ?>
        </a>
        <a href="?" class="btn btn-sm btn-default">
            <img src="<?php echo theme_image("reload.png"); ?>" alt="<?php echo _('Refresh job list'); ?>" title="<?php echo _('Refresh job list'); ?>">
            <?php echo _('Refresh job list'); ?>
        </a>
    </div>

    <table class="table table-striped table-auto-width">
        <thead>
            <tr>
                <th><?php echo _("Scan Target"); ?></th>
                <th><?php echo _("Exclusions"); ?></th>
                <th><?php echo _("Schedule"); ?></th>
                <th><?php echo _("Last Run"); ?></th>
                <th style="min-width: 140px;"><?php echo _("Devices Found"); ?></th>
                <th><?php echo _("Created By"); ?></th>
                <th style="min-width: 80px;"><?php echo _("Status"); ?></th>
                <th><?php echo _("Actions"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $jobs = autodiscovery_component_getjobs();
        if (count($jobs) == 0) {
            echo "<tr><td colspan='8'>" . _("There are no auto-discovery jobs.") . "  <a href='?mode=newjob'>" . _("Add one now") . "</a>.</td></tr>";
        } else {

            foreach ($jobs as $jobid => $jobarr) {

                $frequency = grab_array_var($jobarr, "frequency", "Once");
                $sched = grab_array_var($jobarr, "schedule", array());

                $hour = grab_array_var($sched, "hour", "");
                $minute = grab_array_var($sched, "minute", "");
                $ampm = grab_array_var($sched, "ampm", "");
                $dayofweek = grab_array_var($sched, "dayofweek", "");
                $dayofmonth = grab_array_var($sched, "dayofmonth", "");

                $days = array(
                    0 => 'Sunday',
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                );

                if ($frequency == "Once")
                    $fstr = "";
                else
                    $fstr = $hour . ":" . $minute . " " . $ampm;
                if ($frequency == "Weekly")
                    $fstr .= " " . $days[$dayofweek];
                else if ($frequency == "Monthly")
                    $fstr .= ", Day " . $dayofmonth;

                $addr = trim($jobarr["address"]);
                if (strpos($addr, ' ') !== false) {
                    $addr = substr($addr, strpos($addr, ' ') + 1);
                }

                echo "<tr>";
                echo "<td>" . encode_form_val($addr) . "</td>";
                $exclude_address = grab_array_var($jobarr, "exclude_address");
                if ($exclude_address == "")
                    $exclude_address = "-";
                echo "<td>" . encode_form_val($exclude_address) . "</td>";

                echo "<td id='job_freq_$jobid'>" . encode_form_val($frequency) . "<BR>" . $fstr . "</td>";

                $output_file = get_component_dir_base("autodiscovery") . "/jobs/" . $jobid . ".xml";
                $total_hosts = 0;
                $new_hosts = 0;
                $xml = @simplexml_load_file($output_file);
                if ($xml) {
                    foreach ($xml->device as $d) {
                        $status = strval($d->status);
                        if ($status == "new")
                            $new_hosts++;
                        $total_hosts++;
                    }
                }

                $last_run = "N/A";
                $date_file = get_component_dir_base("autodiscovery") . "/jobs/" . $jobid . ".out";
                if (file_exists($date_file)) {
                    $t = filemtime($date_file);
                    $last_run = get_datetime_string($t);
                }
                echo "<td id='job_last_$jobid'>" . $last_run . "</td>";

                if (file_exists($output_file))
                    $running = false;
                else
                    $running = true;

                echo "<td><div id='job_info_$jobid'>";
                echo "N/A";
                echo "</div></td>";


                echo "<td>" . encode_form_val($jobarr["initiator"]) . "</td>";

                echo "<td><div id='$jobid' class='job_throbber'>";
                echo "<img src='" . theme_image("throbber.gif") . "'> ";
                echo "</div>";
                echo "</td>";

                echo "<td>";
                echo "<a href='?mode=deletejob&job=" . urlencode($jobid) . "'><img src='" . theme_image("cross.png") . "' alt='"._('Cancel')."' class='tt-bind show-running' title='"._('Cancel')."'></a>";
                echo "<a href='?mode=editjob&job=" . urlencode($jobid) . "'><img src='" . theme_image("editsettings.png") . "' alt='"._('Edit job')."' class='tt-bind hide-running' title='"._('Edit job')."'></a>&nbsp; ";
                echo "<a href='?mode=runjob&job=" . urlencode($jobid) . "'><img src='" . theme_image("resultset_next.png") . "' alt='"._('Re-run job')."' class='tt-bind hide-running' title='"._('Re-run job')."'></a>&nbsp;";
                echo "<a href='?mode=viewjob&job=" . urlencode($jobid) . "'><img src='" . theme_image("detail.png") . "' alt='"._('View job results')."' class='tt-bind hide-running' title='"._('View job results')."'></a>&nbsp; ";
                echo "<a href='?mode=deletejob&job=" . urlencode($jobid) . "'><img src='" . theme_image("cross.png") . "' alt='"._('Delete job')."' class='tt-bind hide-running' title='"._('Delete job')."'></a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
    <?php

    // closes the HTML page
    do_page_end(true);
}


function do_update_job()
{

    // check session
    check_nagios_session_protector();

    // get variables
    $jobid = grab_request_var("job", -1);
    if ($jobid == -1)
        $add = true;
    else
        $add = false;

    $address = grab_request_var("address");
    $exclude_address = grab_request_var("exclude_address");
    $os_detection = grab_request_var("os_detection", "on");
    $topology_detection = grab_request_var("topology_detection", "on");
    $system_dns = grab_request_var("system_dns", "off");
    $scandelay = grab_request_var("scandelay", "");
    $custom_ports = grab_request_var("custom_ports", "");

    $frequency = grab_request_var("frequency", "Once");
    $hour = grab_request_var("hour", "09");
    $minute = grab_request_var("minute", "00");
    $ampm = grab_request_var("ampm", "AM");
    $dayofweek = grab_request_var("dayofweek", "");
    $dayofmonth = grab_request_var("dayofmonth", "");


    $errmsg = array();
    $errors = 0;

    // check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    if ($address == "")
        $errmsg[$errors++] = _("Invalid address.");

    if (preg_match('/[^0-9 .\/,-]/', $address))
        $errmsg[$errors++] = _("Invalid characters in scan target.");
    if (preg_match('/[^0-9 .\/,-]/', $exclude_address))
        $errmsg[$errors++] = _("Invalid characters in exclude IPs.");

    if ($frequency != "Once" && enterprise_features_enabled() == false) {
        $errmsg[$errors++] = _("Scheduled scans are only available in the Enterprise Edition.");
    }

    // handle errors
    if ($errors > 0)
        display_add_job(true, $errmsg);

    // single ip address specified, so add netmask
    if (strstr($address, "/") === FALSE)
        $address = trim($address) . "/32";
    if (strpos($address, "/32") || strpos($address, "/31")) {
        $addressparts = explode("/", $address);
        $address = $addressparts[0] . " " . $addressparts[0];
        $mask = $addressparts[1];
    }

    // okay, so add/update job
    if ($jobid == -1)
        $jobid = random_string(6);
    $job = array(
        "address" => $address,
        "exclude_address" => $exclude_address,
        "os_detection" => $os_detection,
        "topology_detection" => $topology_detection,
        "system_dns" => $system_dns,
        "initiator" => $_SESSION["username"],
        "start_date" => time(),
        "scandelay" => $scandelay,
        "custom_ports" => $custom_ports,

        "frequency" => $frequency,
        "schedule" => array(
            "hour" => $hour,
            "minute" => $minute,
            "ampm" => $ampm,
            "dayofweek" => $dayofweek,
            "dayofmonth" => $dayofmonth,
        ),
    );
    autodiscovery_component_addjob($jobid, $job);

    // always delete the old cron job (it might not exit)
    autodiscovery_component_delete_cron($jobid);

    // add a new cron job if this should (now) be scheduled
    if ($frequency != "Once")
        autodiscovery_component_update_cron($jobid);

    if ($add == true && $frequency == "Once") {
        do_run_job($jobid, false);
    }

    // redirect user
    if ($add == true)
        header("Location: ?jobadded");
    else
        header("Location: ?jobupdated");
}

function do_run_job($jobid = -1, $redirect = true)
{

    // get variables
    if ($jobid == -1)
        $jobid = grab_request_var("job", -1);


    $errmsg = array();
    $errors = 0;

    // check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    if ($jobid == -1)
        $errmsg[$errors++] = _("Invalid job.");
    else {
        $cmdline = autodiscovery_component_get_cmdline($jobid);
        if ($cmdline == "")
            $errmsg[$errors++] = _("Invalid command.");
    }

    // handle errors
    if ($errors > 0)
        display_jobs(true, $errmsg);

    // prep files
    //autodiscovery_component_prep_job_files($jobid);

    //echo "FILES PREPPED<BR>";
    //exit();

    // run the command
    //echo "CMD: $cmdline<BR>";
    exec($cmdline, $op);
    //exit();

    // redirect user
    if ($redirect == true)
        header("Location: ?jobrun");
}


function do_delete_job()
{

    $jobid = grab_request_var("job");

    // delete files
    $base_dir = get_component_dir_base("autodiscovery");
    $output_file = $base_dir . "/jobs/" . $jobid . ".xml";
    $watch_file = $base_dir . "/jobs/" . $jobid . ".watch";
    $tmp_file = $base_dir . "/jobs/" . $jobid . ".out";

    if (file_exists($watch_file)) {
        unlink($watch_file);
    }

    if (file_exists($output_file)) {
        unlink($output_file);
    }

    if (file_exists($tmp_file)) {
        unlink($tmp_file);
    }

    //echo "WATCH: $watch_file<BR>";
    //echo "OUTPUT: $output_file<BR>";
    //echo "TMP: $tmp_file<BR>";

    // remove job
    autodiscovery_component_delete_jobid($jobid);

    //print_r($jobs);

    //exit();

    // redirect user
    header("Location: ?jobdeleted");
}


function do_view_job()
{
    global $request;

    do_page_start(array("page_title" => _("Auto-Discovery Jobs")), true);
    ?>

    <script type="text/javascript">
    $(document).ready(function() {
        $('.check-toggle').click(function() {
            if ($(this).is(":checked")) {
                $('input[type="checkbox"]').prop('checked', true);
            } else {
                $('input[type="checkbox"]').prop('checked', false);
            }
        })
    });
    </script>

    <h1><?php echo _("Scan Results"); ?></h1>

    <p><i class="fa fa-chevron-left l"></i> <a href="?"><?php echo _("Back To Auto-Discovery Jobs"); ?></a></p>

    <?php

    $jobid = grab_request_var("job");

    $show_services = grab_request_var("showservices", 0);
    $show_old = grab_request_var("showold", 0);

    $new_hosts = 0;
    $total_hosts = 0;

    $services = autodiscovery_component_parse_job_data($jobid, $new_hosts, $total_hosts);

    $jobarr = autodiscovery_component_get_jobid($jobid);

    $date_file = get_component_dir_base("autodiscovery") . "/jobs/" . $jobid . ".out";
    $t = filemtime($date_file);

    // Build url for later use
    $page_url = "?1";
    foreach ($request as $var => $val) {
        $page_url .= "&" . urlencode($var) . "=" . urlencode($val);
    }

    $addr = trim($jobarr["address"]);
    if (strpos($addr, ' ') !== false) {
        $addr = substr($addr, strpos($addr, ' ') + 1);
    }
    ?>

    <div style="padding: 20px 0 0 0;">
    <div style="float: left">
        <table class="table table-condensed table-bordered table-auto-width">
            <thead>
            <tr>
                <th colspan="2"><?php echo _("Scan Summary"); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo _("Scan Date"); ?>:</td>
                <td><?php echo get_datetime_string($t); ?></td>
            </tr>
            <tr>
                <td><?php echo _("Scan Address"); ?>:</td>
                <td><?php echo encode_form_val($addr); ?></td>
            </tr>
            <?php
            $exclude_address = grab_array_var($jobarr, "exclude_address");
            if (empty($exclude_address)) {
                $exclude_address = "-";
            }
            ?>
            <tr>
                <td><?php echo _("Excludes"); ?>:</td>
                <td><?php echo encode_form_val($exclude_address); ?></td>
            </tr>
            <tr>
                <td><?php echo _("Initiated By"); ?>:</td>
                <td><?php echo encode_form_val($jobarr["initiator"]); ?></td>
            </tr>
            <tr>
                <td><?php echo _("Total Hosts Found"); ?>:</td>
                <td><?php echo intval($total_hosts); ?>&nbsp;&nbsp;
                    <?php
                    if ($show_old == 0)
                        echo "<a href='" . $page_url . "&showold=1'>"._('Show all')."</a>";
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php echo _("New Hosts Found"); ?>:</td>
                <td><b><?php echo intval($new_hosts); ?></b>&nbsp;&nbsp;
                    <?php
                    if ($show_old == 1)
                        echo "<a href='" . $page_url . "&showold=0'>"._('Show only new')."</a>";
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="float: left; margin-left: 25px;">
        <table class="table table-condensed table-bordered table-auto-width">
            <thead>
                <tr>
                    <th colspan="2"><?php echo _("Processing Options"); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo _("Export Data As"); ?>:</td>
                    <td>
                        <a href="<?php echo $page_url; ?>&mode=csv" target="_blank" alt="<?php echo _('Export As CSV'); ?>" title="<?php echo _('Export As CSV'); ?>"><i class="fa fa-file-text-o" style="font-size: 14px;"></i> CSV</a>
                    </td>
                </tr>
                <tr>
                    <td><?php echo _("Configure Basic Monitoring"); ?>:</td>
                    <td>
                        <?php
                        if ($show_old == 1) {
                            echo "<a href='?mode=processjob&job=" . urlencode($jobid) . "&show=new'><img src='" . theme_image("b_next.png") . "'> "._('New hosts only')."</a><br>";
                            echo "<a href='?mode=processjob&job=" . urlencode($jobid) . "&show=all'><img src='" . theme_image("b_next.png") . "'> "._('Both old and new hosts')."</a>";
                        } else {
                            echo "<a href='?mode=processjob&job=" . urlencode($jobid) . "&show=new'><img src='" . theme_image("b_next.png") . "'> "._('New hosts')."</a><br>";
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
        <div class="clear"></div>
    </div>

    <h5 class="ul"><?php echo _("Discovered Items"); ?></h5>

    <?php
    if ($show_services == 1)
        $str = _(" and services");
    else
        $str = "";
    ?>

    <p><?php echo _("The hosts"); ?><?php echo $str; ?> <?php echo _("below were discovered during the auto-discovery scan. Hosts identified as Linux servers with SSH available and no agent already deployed have been pre-selected for Agent Deployment."); ?></p>

    <?php
    if ($show_services == 0) {
        echo "<p><a href='" . $page_url . "&showservices=1'>"._("Show discovered services")."</a></p>";
    } else {
        echo "<p><a href='" . $page_url . "&showservices=0'>"._("Hide services")."</a></p>";
    }
    ?>

<form action="../../../config/deployment/index.php" method="post">

    <table class="table table-condensed table-striped table-hover table-bordered table-auto-width">
        <thead>
            <tr>
                <th><input type="checkbox" class="check-toggle"></th>
                <th><?php echo _("Address"); ?></th>
                <th><?php echo _("Host Name"); ?></th>
                <th><?php echo _("Type"); ?></th>
                <th><?php echo _("Device/Operating System [Accuracy]"); ?> <i class="fa fa-question-circle tt-bind" title="<?php echo _('The accuracy value is given by nmap\'s known hashes database. Even 100% accurate matches may not be the correct device or operating system.'); ?>"></i></th>
                <th><?php echo _("MAC Vendor"); ?></th>
                <th><?php echo _("Agent Deployed"); ?> <i class="fa fa-question-circle tt-bind" title="<?php echo _('Will show yes if an agent has been deployed or has been added to the Manage Deployed Agents page in this Nagios XI system.'); ?>"></i></th>
                <th><?php echo _("Status"); ?></th>
                <?php if ($show_services == 1) { ?>
                <th><?php echo _("Service Name"); ?></th>
                <th><?php echo _("Port"); ?></th>
                <th><?php echo _("Protocol"); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>

        <?php
        // Get currently deployed agent ips
        $agents = deploy_get_agents();
        $agent_ips = array();
        foreach ($agents as $a) {
            $agent_ips[] = $a['address'];
        }

        $output = "";
        foreach ($services as $address => $arr) {

            $status = ucfirst($arr["status"]);

            if ($show_old == 0 && $status != "New")
                continue;

            $accuracy = '';
            if (!empty($arr['osaccuracy'])) { $accuracy = ' [' . $arr['osaccuracy'] . '%]'; }

            // Create services HTML
            $has_ssh = false;
            $services = '';
            foreach ($arr["ports"] as $pid => $parr) {
                if ($parr["service"] == "ssh") { $has_ssh = true; }
                $services .= '<tr>';
                $services .= '<td colspan="8"></td>';
                $services .= '<td>' . $parr["service"] . '</td>';
                $services .= '<td>' . $parr["port"] . '</td>';
                $services .= '<td>' . $parr["protocol"] . '</td>';
                $services .= '</tr>';
            }
            if (count($arr["ports"]) == 0) {
                $services .= '<tr><td colspan="8"></td><td colspan="3">' . _('No services found.') . '</td></tr>';
            }

            $checked = '';
            if ($arr['type'] == 'Linux Server' && $has_ssh && !in_array($arr["address"], $agent_ips)) { $checked = 'checked'; }

            $output .= '<tr>';
            $output .= '<td><input type="checkbox" name="addresses[]" value="'.$arr["address"].'" '.$checked.'></td>';
            $output .= '<td>' . $arr["address"] . '</td>';
            $output .= '<td>' . $arr["fqdns"] . '</td>';
            $output .= '<td>' . $arr["type"] . '</td>';
            $output .= '<td>' . $arr['os'] . $accuracy . '</td>';
            $output .= '<td>' . $arr['macvendor'] . '</td>';
            $output .= '<td>' . ((in_array($arr["address"], $agent_ips)) ? _('Yes') : _('No')) . '</td>';
            $output .= '<td colspan="5">' . $status . '</td>';
            $output .= '</tr>';

            if ($show_services == 1) {
                $output .= $services;
            }
        }

        echo $output;
        ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Deploy Agents to Selected Hosts'); ?></button>
</form>

    <?php

    // closes the HTML page
    do_page_end(true);
}

function do_process_job()
{

    $jobid = grab_request_var("job");
    $show = grab_request_var("show", "all");

    $url = get_base_url() . "/config/monitoringwizard.php?update=1&nextstep=2&wizard=autodiscovery&job=" . urlencode($jobid) . "&nsp=" . get_nagios_session_protector_id() . "&show=" . urlencode($show);
    header("Location: $url");
}

function display_add_job($error = false, $msg = "")
{

    // defaults
    $address = "192.168.1.0/24";
    $exclude_address = "";
    $os_detection = "on";
    $topology_detection = "off";
    $system_dns = "off";
    $scandelay = "";
    $custom_ports = "";

    $frequency = "Once";
    $hour = "09";
    $minute = "00";
    $ampm = "AM";
    $dayofweek = "1";
    $dayofmonth = "1";

    $jobid = grab_request_var("job", -1);

    if ($jobid == -1) {
        $title = "New Auto-Discovery Job";
        $add = true;
    } else {
        $title = "Edit Auto-Discovery Job";
        $add = false;

        // Get existing job
        $jobarr = autodiscovery_component_get_jobid($jobid);

        // Vars default to saved values
        $address = grab_array_var($jobarr, "address", "192.168.1.0/24");
        $exclude_address = grab_array_var($jobarr, "exclude_address", "");
        $os_detection = grab_array_var($jobarr, "os_detection", "on");
        $topology_detection = grab_array_var($jobarr, "topology_detection", "on");
        $system_dns = grab_array_var($jobarr, "system_dns", "off");
        $scandelay = grab_array_var($jobarr, "scandelay", "");
        $custom_ports = grab_array_var($jobarr, "custom_ports", "");

        $frequency = grab_array_var($jobarr, "frequency", $frequency);

        $sched = grab_array_var($jobarr, "schedule", array());
        $hour = grab_array_var($sched, "hour", $hour);
        $minute = grab_array_var($sched, "minute", $minute);
        $ampm = grab_array_var($sched, "ampm", $ampm);
        $dayofweek = grab_array_var($sched, "dayofweek", $dayofweek);
        $dayofmonth = grab_array_var($sched, "dayofmonth", $dayofmonth);

        // Update address if single
        if (strpos($address, ' ') !== false) {
            $address = substr($address, strpos($address, ' ') + 1);
        }
    }

    $address = grab_request_var("address", $address);
    $exclude_address = grab_request_var("exclude_address", $exclude_address);
    $os_detection = grab_request_var("os_detection", $os_detection);
    $topology_detection = grab_request_var("topology_detection", $topology_detection);
    $system_dns = grab_request_var("system_dns", $system_dns);
    $scandelay = grab_request_var("scandelay", $scandelay);
    $custom_ports = grab_request_var("custom_ports", $custom_ports);

    $frequency = grab_request_var("frequency", $frequency);
    $hour = grab_request_var("hour", $hour);
    $minute = grab_request_var("minute", $minute);
    $ampm = grab_request_var("ampm", $ampm);
    $dayofweek = grab_request_var("dayofweek", $dayofweek);
    $dayofmonth = grab_request_var("dayofmonth", $dayofmonth);

    // start the HTML page
    do_page_start(array("page_title" => $title), true);
    ?>
    <h1><?php echo $title; ?></h1>

    <?php
    display_message($error, false, $msg);
    ?>

    <?php
    // Enterprise Edition message
    if ($frequency != "Once")
        echo enterprise_message();
    ?>

    <p>
        <?php echo _("Use this form to configure an auto-discovery job."); ?>
    </p>

    <form id="updateForm" method="post" action="">
    <input type="hidden" name="update" value="1">
    <input type="hidden" name="job" value="<?php echo encode_form_val($jobid); ?>">
    <?php echo get_nagios_session_protector(); ?>

    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td class="vt">
                <label for="addressBox"><?php echo _("Scan Target"); ?>:</label>
            </td>
            <td>
                <input type="text" size="15" name="address" id="addressBox" value="<?php echo encode_form_val($address); ?>" class="textfield form-control">
                <div class="subtext"><?php echo _("Enter an network address and netmask to define the IP ranges to scan."); ?></div>
            </td>
        </tr>
        <tr>
            <td class="vt">
                <label for="addressBox"><?php echo _("Exclude IPs"); ?>:</label>
            </td>
            <td>
                <input type="text" size="80" name="exclude_address" id="excludeaddressBox" value="<?php echo encode_form_val($exclude_address); ?>" class="textfield form-control">
                <div class="subtext"><?php echo _("An optional comma-separated list of IP addresses and/or network addresses to exclude from the scan."); ?><br><b><?php echo _("Note"); ?>:</b> <?php echo _("The excluded addresses may be pinged, but they will not be scanned for open/available services via nmap."); ?></div>
            </td>
        </tr>
        <tr>
            <td class="vt">
                <label><?php echo _('Schedule'); ?>:</label>
            </td>
            <td>
                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt"><label><?php echo _('Frequency'); ?>:</label></td>
                        <td>
                            <select name="frequency" class="form-control" id="select_frequency" onchange="showTimeOpts()">
                                <option value="Once" <?php echo is_selected($frequency, "Once"); ?>><?php echo _('One Time'); ?></option>
                                <option value="Daily" <?php echo is_selected($frequency, "Daily"); ?>><?php echo _('Daily'); ?></option>
                                <option value="Weekly" <?php echo is_selected($frequency, "Weekly"); ?>><?php echo _('Weekly'); ?></option>
                                <option value="Monthly" <?php echo is_selected($frequency, "Monthly"); ?>><?php echo _('Monthly'); ?></option>
                            </select>
                        </td>
                    </tr>
                        <tr id="time-div">
                            <td><label><?php echo _('Time'); ?>:</label></td>
                            <td>
                                <select name="hour" class="form-control condensed">
                                    <?php
                                    for ($x = 1; $x <= 12; $x++) {
                                        $nstr = sprintf("%02d", $x);
                                        echo "<option value='" . $nstr . "' " . is_selected($hour, $nstr) . ">" . $nstr . "</option>";
                                    }
                                    ?>
                                </select>:<select name="minute" class="form-control condensed">
                                    <?php
                                    for ($x = 0; $x < 60; $x++) {
                                        $nstr = sprintf("%02d", $x);
                                        echo "<option value='" . $nstr . "' " . is_selected($minute, $nstr) . ">" . $nstr . "</option>";
                                    }
                                    ?>
                                </select>
                                <select name="ampm" class="form-control condensed">
                                    <option value="AM" <?php echo is_selected($ampm, "AM"); ?>>AM</option>
                                    <option value="PM" <?php echo is_selected($ampm, "PM"); ?>>PM</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="dayofweek-div">
                            <td><label><?php echo _('Weekday'); ?>:</label></td>
                            <td>
                                <select name="dayofweek" class="form-control condensed">
                                    <?php
                                    $days = array(
                                        0 => 'Sunday',
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                    );
                                    foreach ($days as $did => $day) {
                                        echo "<option value='" . $did . "' " . is_selected($dayofweek, $did) . ">" . $day . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="dayofmonth-div">
                            <td><label><?php echo _('Day of Month'); ?>:</label></td>
                            <td>
                                <select name="dayofmonth" class="form-control condensed">
                                    <?php
                                    for ($x = 1; $x <= 31; $x++) {
                                        echo "<option value='" . $x . "' " . is_selected($dayofmonth, $x) . ">" . $x . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="subtext"><?php echo _("Specify the schedule you would like this job to be run."); ?></div>
                </td>
            </tr>

            <script type='text/javascript'>
            $(document).ready(function () {
                showTimeOpts();
                $('#advopts1').hide();
                $('#advopts2').hide();
                $('#advopts3').hide();
                $('#advopts4').hide();
                $('#advancedoptsbutton').click(function () {
                    $('#advopts1').show();
                    $('#advopts2').show();
                    $('#advopts3').show();
                    $('#advopts4').show();
                    $('#advancedoptsbutton').hide();
                });
            });
            function showTimeOpts() {
                var opt = $('#select_frequency').val();
                $('#time-div').hide();
                $('#dayofweek-div').hide();
                $('#dayofmonth-div').hide();
                switch (opt) {
                    case 'Daily':
                        $('#time-div').show('fast');
                        break;
                    case 'Weekly':
                        $('#time-div').show('fast');
                        $('#dayofweek-div').show('fast');
                        break;
                    case 'Monthly':
                        $('#time-div').show('fast');
                        $('#dayofmonth-div').show('fast');
                        break;
                    default:
                        break;
                }
            }
            </script>

            <tr id="advancedoptsbutton">
                <td colspan="2"><a href="#"><?php echo _("Show Advanced Options +"); ?></a></td>
            </tr>

            <tr id="advopts1">
                <td class="vt">
                    <label><?php echo _("OS Detection:"); ?></label>
                </td>
                <td>
                    <select name="os_detection" class="form-control">
                        <option value="off" <?php echo is_selected($os_detection, "off"); ?>><?php echo _("Off"); ?></option>
                        <option value="on" <?php echo is_selected($os_detection, "on"); ?>><?php echo _("On"); ?></option>
                    </select>
                    <div class="subtext"><?php echo _("Attempt to detect the operating system of each host"); ?>.<br><b><?php echo _("Note:"); ?></b> <?php echo _("OS detection may cause the scan to take longer to complete and may not be 100% accurate."); ?></div>
                </td>
            </tr>
            <tr id="advopts2">
                <td class="vt">
                    <label><?php echo _("Scan Delay:"); ?></label>
                </td>
                <td>
                    <input type="text" name="scandelay" style="width: 60px;" class="form-control" value="<?php echo encode_form_val($scandelay); ?>"> ms
                    <div class="subtext"><?php echo _("Adjust delay between probes to a given host."); ?> <br>
                    <?php echo _("If set, this option causes Nmap to wait at least the given amount of time between each probe it sends to a given host."); ?> <br>
                    <?php echo _("This is particularly useful in the case of rate limiting. milliseconds."); ?></div>
                </td>
            </tr>
            <tr id="advopts3">
                <td class="vt">
                    <label><?php echo _("System DNS:"); ?></label>
                </td>
                <td>
                    <select name="system_dns" class="form-control">
                        <option value="off" <?php echo is_selected($system_dns, "off"); ?>><?php echo _("Off"); ?></option>
                        <option value="on" <?php echo is_selected($system_dns, "on"); ?>><?php echo _("On"); ?></option>
                    </select>
                    <div class="subtext"><?php echo _("Use system DNS."); ?></div>
                </td>
            </tr>
            <tr id="advopts4">
                <td class="vt">
                    <label><?php echo _("Custom Ports:"); ?></label>
                </td>
                <td>
                    <input name="custom_ports" size="80" id="custom_portsBox" value="<?php echo encode_form_val($custom_ports); ?>" class="textfield form-control">
                    <div class="subtext"><?php echo _("Specify Custom ports."); ?>  Ex: 22; 1-65535; U:53,111,137,T:21-25,80,139,8080,S:9</div>
                </td>
            </tr>
            <input type="hidden" name="topology_detection" value="on">
        </table>

    <div id="formButtons">
        <button type="submit" class="btn btn-sm btn-primary" name="updateButton" id="updateButton"><?php echo _('Submit'); ?></button>
        <a href="index.php" class="btn btn-sm btn-default" style="margin-left: 5px;"><?php echo _('Cancel'); ?></a>
    </div>

    </form>

    <?php
    do_page_end(true);
    exit();
}


function display_install_error($installed, $prereqs, $missing_components)
{
    do_page_start(array("page_title" => _("Installation Problem")), true);
    ?>

    <h1>Installation Problem</h1>

    <p>An installation errror was detected. The following steps must be completed before using this component:
    </p>
    <ul>
        <?php if ($prereqs == false) { ?>
        <li><b><?php echo _('Make sure pre-requisite programs are installed'); ?>.</b> <?php echo _('The following programs must be installed on your Nagios XI server'); ?>:
            <ul>
                <?php echo $missing_components; ?>
            </ul>
        </li>
        <?php } ?>
    </ul>

    <?php
    do_page_end(true);
    exit();
}