<?php
//
// Audit Log (Enterprise Feature)
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check prereqs
grab_request_vars();
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

route_request();

function route_request()
{
    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "getreport":
            get_auditlog_report();
            break;
        case "getpage":
            get_auditlog_page();
            break;
        case "view-configure":
            get_auditlog_configuration();
            break;
        case "do-configure":
            do_auditlog_configuration();
            break;
        case "csv":
            get_auditlog_csv();
            break;
        case "pdf":
            export_report('auditlog', EXPORT_PDF, EXPORT_LANDSCAPE);
            break;
        default:
            show_auditlog();
            break;
    }
}


function get_auditlog_page()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Check enterprise license
    $efe = enterprise_features_enabled();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 1);
    $records = grab_request_var("records", 10);
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $details = grab_request_var("details", 'hide');
    $export = grab_request_var('export', 0);
    $type = grab_request_var('type', '');
    $source = grab_request_var("source", "");

    // Expired enterprise license can only stay on 1st page
    if ($efe == false) {
        $page = 1;
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
        "records" => ""
    );
    if ($search) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";details=lk:" . $search;
    }

    if (!empty($type)) {
        $args["type"] = intval($type);
    }

    if ($source != "") {
        $args["source"] = $source;
    }

    $xml = get_auditlog_xml($args);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // Determine paging information
    $args = array(
        "reportperiod" => $reportperiod,
        "startdate" => $startdate,
        "enddate" => $enddate,
        "starttime" => $starttime,
        "endtime" => $endtime,
        "search" => $search,
        "source" => $source,
        "type" => $type,
        "details" => $details
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record,
    );

    if ($search) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";details=lk:" . $search;
    }
    if ($source != "") {
        $args["source"] = $source;
    }
    if ($type != "") {
        $args["type"] = $type;
    }
    $xml = get_auditlog_xml($args);
    ?>

    <table class="auditlogtable table table-condensed table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th style="width: 140px;"><?php echo _("Date / Time"); ?></th>
                <th><?php echo _("Source"); ?></th>
                <th><?php echo _("Type"); ?></th>
                <th><?php echo _("User"); ?></th>
                <th><?php echo _("IP Address"); ?></th>
                <th><?php echo _("Message"); ?></th>
                <?php if ($details == "show") { ?><th><?php echo _("Details"); ?></th><?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($xml) {

            $cols = 7;
            if ($details == "show") { $cols = 8; }
            $x = 0;

            if ($total_records == 0) {
                echo "<tr><td colspan='".$cols."'>" . _("No matching results found. Try expanding your search criteria.") . "</td></tr>\n";
            } else foreach ($xml->auditlogentry as $a) {

                $x++;
                if ($efe == false && $x > 15)
                    break;

                $user = strval($a->user);
                $ip = strval($a->ip_address);
                if ($user == "NULL")
                    $user = "";
                if ($ip == "NULL")
                    $ip = "";

                $source = $a->source;
                if ($source == 'Nagios XI') {
                    $source = get_product_name();
                } else if ($source == 'Nagios CCM') {
                    $source = _('Core Config Manager');
                }

                echo "<tr >";
                echo "<td nowrap><span class='notificationtime'>" . $a->log_time . "</span></td>";
                echo "<td>" . $source . "</td>";
                echo "<td>" . $a->typestr . "</td>";
                echo "<td>" . $user . "</td>";
                echo "<td>" . $ip . "</td>";
                echo "<td>" . $a->message . "</td>";
                if ($details == "show") { echo "<td>" . $a->details . "</td>"; }
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>

    <?php
}

function do_auditlog_configuration()
{
    global $request;
    $nls_server = grab_request_var("server", array());

    $ret = array('status' => 'success', 'msg' => _('Successfully changed audit log settings!'));
    $hostname = grab_array_var($nls_server, 'hostname', '');
    $port = intval(grab_array_var($nls_server, 'port', 0));

    // End validation
    if (grab_array_var($nls_server, 'enable', 0)) {

        // Begin validation
        if (!$hostname) {
            $ret = array('error' => true, 'invalid' => 'ls_hostname', 'msg' => _('Please enter the hostname of your Log Server.'));
        }

        if (!$port) {
            $ret = array('error' => true, 'invalid' => 'ls_port', 'msg' => _('Please enter the port of your syslog input.'));
        }

        if (array_key_exists('error', $ret)) {
            print json_encode($ret);
            return;
        }

        // Try to connect to the port
        $fp = fsockopen($hostname, $port, $errno, $errmsg);
        if ($fp) {
            fclose($fp);
        }
        else {
            $ret = array('error' => true, 'msg' => sprintf(_('Could not connect to %1$s on port %2$s: %3$s'), $hostname, $port, $errmsg));
            print json_encode($ret);
            return;
        }

        $command_id = auditlog_setup_nls($hostname, $port);
    }
    else {
        auditlog_teardown_nls();
    }

    print json_encode($ret);

    if (array_key_exists('error', $ret) && $ret['error'] === true) {
        return;
    }

    // Save new data to xi_options
    set_option('auditlog_nls_server', base64_encode(serialize($nls_server)));
}

function get_auditlog_configuration()
{
    global $request;
    $server = unserialize(base64_decode(get_option('auditlog_nls_server', base64_encode(serialize(array())))));

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Check enterprise license
    $efe = enterprise_features_enabled();

    do_page_start(array("page_title" => _("Audit Log Configuration"), "enterprise" => true), true);

?>

<h1><?php echo _("Send to Nagios Log Server");?></h1>
<p><?php echo  _("Configure settings to send the outbound audit log messages to a Nagios Log Server system."); ?><br><?php echo _("Note: Only the current day and newly created logs will be sent."); ?></p>

<div class="alert alert-danger hide" id="validation-failed"></div>

<form id="auditlog-update-settings" action="?" method="post">

    <div class="checkbox" style="margin: 15px 0;">
        <label>
            <input type="checkbox" name="server[enable]" id="enable-export" value="1" <?php echo is_checked(grab_array_var($server, 'enable', 0), 1); ?>>
            <?php echo _("Send audit logs to Nagios Log Server"); ?>
        </label>
    </div>
    
    <h5 class="ul"><?php echo _("Log Server Configuration");?></h5>
    <table class="table-condensed table-no-border table-auto-width">
        <tbody>
            <tr>
                <td>
                    <label for="ls_hostname"><?php echo _("Hostname:"); ?></label>
                </td>
                <td>
                    <input type="text" class="form-control textfield" id="ls_hostname" name="server[hostname]" value="<?php echo encode_form_val(grab_array_var($server, 'hostname', '')); ?>">&nbsp;
                    <i class="fa fa-14 fa-question-circle tt-bind" title="<?php echo _('The hostname of the Nagios Log Server system.'); ?>"></i>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="ls_port"><?php echo _("Input Port:"); ?></label>
                </td>
                <td>
                    <input type="text" class="form-control textfield" id="ls_port" name="server[port]" size="6" value="<?php echo intval(grab_array_var($server, 'port', 2056)); ?>">&nbsp;
                    <i class="fa fa-14 fa-question-circle tt-bind" title="<?php echo _('Input a port number for the syslog input that is shown in the Log Server configuration. Default is 2056.'); ?>"></i>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <input type="hidden" name="mode" value="do-configure">
        <button type="submit" class="btn btn-sm btn-primary" id="add-nls-server"><?php echo _("Submit"); ?></button>
        <a href="auditlog.php" class="btn btn-sm btn-default"><?php echo _("Back");?></a>
    </div>

</form>

<script>

$(document).ready(function () {
    $('#auditlog-update-settings').submit(function (event) {
        $('.form-error').removeClass('form-error');
        $('#validation-failed').addClass('hide');

        event.preventDefault();
        $.post('?' + $(this).serialize(), {}, function (result) {

            if (result.hasOwnProperty('error') && result.error === true) {
                console.log(result);
                $('#validation-failed').html(result.msg).removeClass('hide');
                if (result.hasOwnProperty('invalid')) {
                    $('#' + result.invalid).addClass('form-error');
                }
            }
            else {
                window.location.href = '?';
            }
        }, 'json');
    });
});
</script>
<?php
}

function get_auditlog_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Check enterprise license
    $efe = enterprise_features_enabled();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 1);
    $records = grab_request_var("records", get_user_meta(0, 'report_defualt_recordlimit', 10));
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $source = grab_request_var("source", "");
    $details = grab_request_var("details", 'hide');
    $hideoptions = grab_request_var('hideoptions', 0);
    $type = grab_request_var('type', '');
    $export = grab_request_var('export', 0);
    $host = grab_request_var('host', '');
    $service = grab_request_var('service', '');
    $hostgroup = grab_request_var('hostgroup', '');
    $servicegroup = grab_request_var('servicegroup', '');
    $user = grab_request_var('user', '');

    // Expired enterprise license can only stay on 1st page
    if ($efe == false) {
        $page = 1;
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
        "records" => ""
    );
    if ($search) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";details=lk:" . $search;
    }

    if (!empty($type)) {
        $args["type"] = intval($type);
    }

    if ($source != "") {
        $args["source"] = $source;
    }

    $xml = get_auditlog_xml($args);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // Determine paging information
    $args = array(
        "reportperiod" => $reportperiod,
        "startdate" => $startdate,
        "enddate" => $enddate,
        "starttime" => $starttime,
        "endtime" => $endtime,
        "search" => $search,
        "source" => $source,
        "type" => $type,
        "details" => $details
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record,
    );

    if (!empty($search)) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";details=lk:" . $search;
    }
    if (!empty($source)) {
        $args["source"] = $source;
    }
    if (!empty($type)) {
        $args["type"] = $type;
    }
    if (!empty($user)) {
        $args["user"] = $user;
    }
    $xml = get_auditlog_xml($args);

    if ($export) {

        do_page_start(array("page_title" => _("Notifications"), "enterprise" => true), true);

        // Default logo stuff
        $logo = "nagiosxi-logo-small.png";
        $logo_alt = get_product_name();

        // Use custom logo if it exists
        $logosettings_raw = get_option("custom_logo_options");
        if ($logosettings_raw == "") {
            $logosettings = array();
        } else {
            $logosettings = unserialize($logosettings_raw);
        }

        $custom_logo_enabled = grab_array_var($logosettings, "enabled");
        if ($custom_logo_enabled == 1) {
            $logo = grab_array_var($logosettings, "logo", $logo);
            $logo_alt = grab_array_var($logosettings, "logo_alt", $logo_alt);
        }
        ?>

        <script type='text/javascript' src='<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>'></script>

        <div style="padding-bottom: 10px;">
            <div style="float: left; margin-right: 30px;">
                <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
            </div>
            <div style="float: left; height: 44px;">
                <h1 style="margin: 0; padding: 0 0 5px 0;"><?php echo _("Audit Log"); ?></h1>
                <div><?php echo _("Report covers from"); ?>:
                    <strong><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong> <?php echo _("to"); ?>
                    <strong><?php echo get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <?php } else { ?>

        <h1><?php echo _('Audit Log'); ?></h1>
        <div style="margin-top: 10px;">
            <?php echo _("From"); ?>:
            <b><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></b>
            <?php echo _("to"); ?>
            <b><?php echo get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></b>
        </div>

        <?php } ?>

    <div class="recordcounttext">
        <?php
        $clear_args = array(
            "reportperiod" => $reportperiod,
            "startdate" => $startdate,
            "enddate" => $enddate,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "source" => $source,
            "type" => $type,
            "details" => $details,
            "user" => $user
        );
        echo table_record_count_text($pager_results, $search, true, $clear_args, '', true);
        ?>
    </div>

    <?php if (!$export) { ?>
    <div class="ajax-pagination">
        <button class="btn btn-xs btn-default first-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-backward"></i></button>
        <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>" disabled><i class="fa fa-chevron-left l"></i></button>
        <span style="margin: 0 10px;"><?php echo _('Page'); ?> <span class="pagenum">1 <?php echo _('of'); ?> <?php echo $pager_results['total_pages']; ?></span></span>
        <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
        <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

        <select class="form-control condensed num-records">
            <option value="5"<?php if ($pager_results['records_per_page'] == 5) { echo ' selected'; } ?>>5 <?php echo _('Per Page'); ?></option>
            <option value="10"<?php if ($pager_results['records_per_page'] == 10) { echo ' selected'; } ?>>10 <?php echo _('Per Page'); ?></option>
            <option value="25"<?php if ($pager_results['records_per_page'] == 25) { echo ' selected'; } ?>>25 <?php echo _('Per Page'); ?></option>
            <option value="50"<?php if ($pager_results['records_per_page'] == 50) { echo ' selected'; } ?>>50 <?php echo _('Per Page'); ?></option>
            <option value="100"<?php if ($pager_results['records_per_page'] == 100) { echo ' selected'; } ?>>100 <?php echo _('Per Page'); ?></option>
        </select>

        <input type="text" class="form-control condensed jump-to">
        <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
    </div>
    <?php } ?>

<script>
var report_url = '<?php echo get_base_url(); ?>admin/auditlog.php';
var report_url_args = {
    reportperiod: '<?php echo encode_form_valq($reportperiod); ?>',
    startdate: '<?php echo encode_form_valq($startdate); ?>',
    enddate: '<?php echo encode_form_valq($enddate); ?>',
    starttime: '<?php echo $starttime; ?>',
    endtime: '<?php echo $endtime; ?>',
    search: '<?php echo encode_form_valq($search); ?>',
    details: '<?php echo encode_form_valq($details); ?>',
    host: '<?php echo encode_form_valq($host); ?>',
    service: '<?php echo encode_form_valq($service); ?>',
    hostgroup: '<?php echo encode_form_valq($hostgroup); ?>',
    servicegroup: '<?php echo encode_form_valq($servicegroup); ?>',
    type: '<?php echo encode_form_valq($type); ?>',
    source: '<?php echo encode_form_valq($source); ?>',
    user: '<?php echo encode_form_valq($user); ?>'
}
var record_limit = <?php echo $pager_results['records_per_page']; ?>;
var max_records = <?php echo $pager_results['total_records']; ?>;
var max_pages = <?php echo $pager_results['total_pages']; ?>;

$(document).ready(function() {
    load_page();
});
</script>

    <div class="reportentries">
        <div id="loadscreen" class="hide"></div>
        <div id="loadscreen-spinner" class="sk-spinner sk-spinner-rotating-plane sk-spinner-center hide"></div>
        <div class="report-data" style="min-height: 140px;"></div>
    </div>

    <?php if (!$export) { ?>
    <div class="ajax-pagination">
        <button class="btn btn-xs btn-default first-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-backward"></i></button>
        <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>" disabled><i class="fa fa-chevron-left l"></i></button>
        <span style="margin: 0 10px;"><?php echo _('Page'); ?> <span class="pagenum">1 <?php echo _('of'); ?> <?php echo $pager_results['total_pages']; ?></span></span>
        <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
        <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

        <select class="form-control condensed num-records">
            <option value="5"<?php if ($pager_results['records_per_page'] == 5) { echo ' selected'; } ?>>5 <?php echo _('Per Page'); ?></option>
            <option value="10"<?php if ($pager_results['records_per_page'] == 10) { echo ' selected'; } ?>>10 <?php echo _('Per Page'); ?></option>
            <option value="25"<?php if ($pager_results['records_per_page'] == 25) { echo ' selected'; } ?>>25 <?php echo _('Per Page'); ?></option>
            <option value="50"<?php if ($pager_results['records_per_page'] == 50) { echo ' selected'; } ?>>50 <?php echo _('Per Page'); ?></option>
            <option value="100"<?php if ($pager_results['records_per_page'] == 100) { echo ' selected'; } ?>>100 <?php echo _('Per Page'); ?></option>
        </select>

        <input type="text" class="form-control condensed jump-to">
        <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
    </div>
    <?php
    }
}


function show_auditlog($error = false, $msg = "")
{
    global $request;
    $theme = get_theme();

    // Do not do any processing unless we have default report running enabled
    $disable_report_auto_run = get_option("disable_report_auto_run", 0);

    if (enterprise_features_enabled() == true) {
        $fullaccess = true;
    } else {
        $fullaccess = false;
    }

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Check enterprise license
    $efe = enterprise_features_enabled();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 1);
    $records = grab_request_var("records", get_user_meta(0, 'report_defualt_recordlimit', 10));
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $source = grab_request_var("source", "");
    $details = grab_request_var("details", 'hide');
    $hideoptions = grab_request_var('hideoptions', 0);
    $type = grab_request_var('type', '');
    $user = grab_request_var('user', '');

    // Expired enterprise license can only stay on 1st page
    if ($efe == false) {
        $page = 1;
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
        "records" => ""
    );
    if ($search) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";details=lk:" . $search;
    }

    if (!empty($type)) {
        $args["type"] = intval($type);
    }

    if ($source != "") {
        $args["source"] = $source;
    }

    $xml = get_auditlog_xml($args);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // Determine paging information
    $args = array(
        "reportperiod" => $reportperiod,
        "startdate" => $startdate,
        "enddate" => $enddate,
        "starttime" => $starttime,
        "endtime" => $endtime,
        "search" => $search,
        "source" => $source,
        "type" => $type,
        "details" => $details
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    $auto_start_date = get_datetime_string(strtotime('yesterday'), DT_SHORT_DATE);
    $auto_end_date = get_datetime_string(strtotime('today'), DT_SHORT_DATE);

    // Get timezone datepicker format
    if (isset($_SESSION['date_format']))
        $format = $_SESSION['date_format'];
    else {
        if (is_null($format = get_user_meta(0, 'date_format')))
            $format = get_option('default_date_format');
    }
    $f = get_date_formats();

    $js_date = 'mm/dd/yy';
    if ($format == DF_ISO8601) {
        $js_date = 'yy-mm-dd';
    } else if ($format == DF_US) {
        $js_date = 'mm/dd/yy';
    } else if ($format == DF_EURO) {
        $js_date = 'dd/mm/yy';
    }

    do_page_start(array("page_title" => _("Audit Log"), "enterprise" => true), true);
    make_enterprise_only_feature();
?>

<script type='text/javascript' src='<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>'></script>

<script type="text/javascript">
$(document).ready(function () {

    showhidedates();

    // If we should run it right away
    if (!<?php echo $disable_report_auto_run; ?>) {
        run_auditlog_ajax();
    }
    
    $('.datetimepicker').datetimepicker({
        dateFormat: '<?php echo $js_date; ?>',
        timeFormat: 'HH:mm:ss',
        showHour: true,
        showMinute: true,
        showSecond: true
    });

    $('.btn-datetimepicker').click(function() {
        var id = $(this).data('picker');
        $('#' + id).datetimepicker('show');
    });

    $('#startdateBox').click(function() {
        $('#reportperiodDropdown').val('custom');
        if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
            $('#startdateBox').val('<?php echo $auto_start_date;?>');
            $('#enddateBox').val('<?php echo $auto_end_date;?>');
        }
    });

    $('#enddateBox').click(function() {
        $('#reportperiodDropdown').val('custom');
        if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
            $('#startdateBox').val('<?php echo $auto_start_date;?>');
            $('#enddateBox').val('<?php echo $auto_end_date;?>');
        }
    });

    $('#reportperiodDropdown').change(function () {
        showhidedates();
    });

    // Actually return the report
    $('#run').click(function() {
        run_auditlog_ajax();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>admin/auditlog.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

    $('.auditlog-remove-nls').on('click', function () {
        var url = '?mode=ajax-remove-nls';
        var query = {};
        query.hostname = $(this).data('hostname');
        remove_me = $(this);

        $.post(url, query, function() {
            remove_me.parent().parent().remove();
            do_show_no_receiving_servers();
        }, 'json');
    });

    do_show_no_receiving_servers();

    $('#add-nls-server').on('click', function() {

        reset_nls_validation();

        var query = {};
        query.hostname = $('#ls_hostname').val();
        query.port = parseInt($('#ls_port').val());

        // TODO: get SSL settings

        // TODO: validate properly

        if (isNaN(query.port) || query.port < 1 || query.port > 65535) {
            nls_validation_error('<?php echo _("Please enter a valid port number");?>', $('#ls_port'));
            return;
        }

        var proto_regexp = /https?:\/\//;
        if (query.hostname === '' || query.hostname.match(proto_regexp)) {
            nls_validation_error('<?php echo _("Please enter a valid hostname");?>', $('#ls_hostname'));
            return;
        }

        var url = '?mode=ajax-add-nls';
        $.post(url, query, function(data) {
            if (data.hasOwnProperty('error') && data.error) {
                nls_validation_error(data.msg);
                return;
            }
            console.log('Success!');
            console.log(data);
            window.location.reload();
        }, 'json');
    });
});

function do_show_no_receiving_servers() {
    var no_receiving_servers = $('.no-receiving-servers');
    if (no_receiving_servers.siblings().length === 0) {
        no_receiving_servers.show();
    }
}

function reset_nls_validation() {
    $('.form-error').removeClass('form-error');
    $('#nls-export-modal-notice').addClass('hide');
}

function nls_validation_error(message, jquery_element) {

    if (jquery_element) {
        jquery_element.addClass('form-error');
    }

    $('#nls-export-modal-notice').html(message).removeClass('hide');
}

var report_sym = 0;
function run_auditlog_ajax() {
    report_sym = 1;
    setTimeout('show_loading_report()', 500);

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'auditlog.php?'+formvalues;

    current_page = 1;

    $.get(url, {}, function(data) {
        report_sym = 0;
        hide_throbber();
        $('#report').html(data);
        $('#report .tt-bind').tooltip();
    });
}

</script>

<form method="get" id="report-options" data-type="notifications">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <?php echo _('Download'); ?> <i class="fa fa-caret-down r"></i>
                </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <li><a class="btn-export" data-type="csv" title="<?php echo _("Download as CSV"); ?>"><i class="fa fa-file-text-o l"></i> <?php echo _("CSV"); ?></a></li>
                    <li><a class="btn-export" data-type="pdf" title="<?php echo _("Download as PDF"); ?>"><i class="fa fa-file-pdf-o l"></i> <?php echo _("PDF"); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="reportsearchbox">
            <input type="text" size="15" name="search" id="searchBox" value="<?php echo encode_form_val($search); ?>" placeholder="<?php echo _("Search..."); ?>" class="textfield form-control">
        </div>

        <div class="fr" style="margin-right: 10px;">
            <a id="auditlog-configure" class="btn btn-sm btn-default" href="?mode=view-configure"><i class="fa fa-send"></i> <?php echo _("Send to Nagios Log Server"); ?></a>
        </div>

        <div class="reportoptionpicker">

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _("Period"); ?></label>
                <select id="reportperiodDropdown" name="reportperiod" class="form-control">
                    <?php
                    $tp = get_report_timeperiod_options();
                    foreach ($tp as $shortname => $longname) {
                        echo "<option value='" . $shortname . "' " . is_selected($shortname, $reportperiod) . ">" . $longname . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div id="customdates" class="cal">
                <div class="input-group" style="width: 450px; margin-right: 10px;">
                    <label class="input-group-addon"><?php echo _('From') ?></label>
                    <input class="form-control datetimepicker" type="text" id='startdateBox' name="startdate" value="<?php echo encode_form_val(get_datetime_from_timestring($startdate)); ?>">
                    <div data-picker="startdateBox" class="input-group-btn btn btn-sm btn-default btn-datetimepicker">
                        <i class="fa fa-calendar fa-14"></i>
                    </div>
                    <label class="input-group-addon" style="border-left: 0; border-right: 0;"><?php echo _('to') ?></label>
                    <input class="form-control datetimepicker" type="text" id='enddateBox' name="enddate" value="<?php echo encode_form_val(get_datetime_from_timestring($enddate)); ?>">
                    <div data-picker="enddateBox" class="input-group-btn btn btn-sm btn-default btn-datetimepicker">
                        <i class="fa fa-calendar fa-14"></i>
                    </div>
                </div>
            </div>

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _('Source'); ?></label>
                <select name="source" class="form-control vam">
                    <option value="" <?php echo is_selected($source, ''); ?>><?php echo _('Any'); ?></option>
                    <option value="User Interface" <?php echo is_selected($source, 'User Interface'); ?>><?php echo _('User Interface'); ?></option>
                    <option value="Core Config Manager" <?php echo is_selected($source, 'Core Config Manager'); ?>><?php echo _('Core Config Manager'); ?></option>
                    <option value="Subsystem" <?php echo is_selected($source, 'Subsystem'); ?>><?php echo _('Subsystem'); ?></option>
                    <option value="API" <?php echo is_selected($source, 'API'); ?>>API</option>
                    <option value="Core" <?php echo is_selected($source, 'Core'); ?>><?php echo _('Core'); ?></option>
                    <option value="Other" <?php echo is_selected($source, 'Other'); ?>><?php echo _('Other'); ?></option>
                </select>
            </div>

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _('Type'); ?></label>
                <select name="type" class="form-control vam">
                    <option value="" <?php echo is_selected($type, ''); ?>><?php echo _('Any'); ?></option>
                    <option value="1" <?php echo is_selected($type, 1); ?>>ADD</option>
                    <option value="2" <?php echo is_selected($type, 2); ?>>DELETE</option>
                    <option value="4" <?php echo is_selected($type, 4); ?>>MODIFY</option>
                    <option value="8" <?php echo is_selected($type, 8); ?>>CHANGE</option>
                    <option value="16" <?php echo is_selected($type, 16); ?>>SECURITY</option>
                    <option value="32" <?php echo is_selected($type, 32); ?>>INFO</option>
                    <option value="64" <?php echo is_selected($type, 64); ?>>OTHER</option>
                </select>
            </div>

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _('User'); ?></label>
                <select name="user" class="form-control vam">
                    <option value="" <?php echo is_selected($user, ''); ?>><?php echo _('Any'); ?></option>
                    <optgroup label="<?php echo _('Regular'); ?>">
                        <?php
                        $users = get_users();
                        foreach ($users as $u) {
                        ?>
                        <option value="<?php echo encode_form_val($u['username']); ?>" <?php echo is_selected($user, $u['username']); ?>><?php echo encode_form_val($u['username']); ?></option>
                        <?php } ?>
                    </optgroup>
                    <optgroup label="<?php echo _('Reserved'); ?>">
                        <option value="system" <?php echo is_selected($user, 'system'); ?>>system</option>
                    </optgroup>
                </select>
            </div>

            <label style="font-weight: normal; cursor: pointer; margin: 6px 10px 0 0;">
                <input type="checkbox" name="details" value="show" <?php echo is_checked($details, 'show'); ?> style="vertical-align: text-top; margin: 0;"> <?php echo _("Show Details"); ?>
            </label>

            <button type="button" id="run" class="btn btn-sm btn-primary" name="reporttimesubmitbutton"><?php echo _("Run"); ?></button>

        </div>

    </div>
</form>

<div id="report"></div>

<?php

}

/**
 * This function gets the XML records of audit log data.
 * @return SimpleXMLElement
 */
function get_auditlog_xml($args=array())
{

    // makes sure user has appropriate license level
    licensed_feature_check();

    // get values passed in GET/POST request
    $reportperiod = grab_array_var($args, "reportperiod", grab_request_var("reportperiod", "last24hours"));
    $startdate = grab_array_var($args, "startdate", grab_request_var("startdate", ""));
    $enddate = grab_array_var($args, "enddate", grab_request_var("enddate", ""));
    $search = grab_array_var($args, "search", grab_request_var("search", ""));
    $source = grab_array_var($args, "source", grab_request_var("source", ""));
    $type = grab_array_var($args, "type", grab_request_var("type", ""));
    $user = grab_array_var($args, "user", grab_request_var("user", ""));
    $ip_address = grab_array_var($args, "ip_address", grab_request_var("ip_address", ""));
    $records = grab_array_var($args, "records", grab_request_var("records", ""));
    $details = grab_request_var("details", 'hide');

    // fix search
    if ($search == _("Search..."))
        $search = "";


    // determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // get XML data from backend - the most basic example
    // this will return all records (no paging), so it can be used for CSV export
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
    );
    if ($source != "")
        $args["source"] = $source;
    if ($user != "")
        $args["user"] = $user;
    if ($type != "")
        $args["type"] = $type;
    if ($ip_address != "")
        $args["ip_address"] = $ip_address;
    if ($search) {
        $args["message"] = "lk:" . $search . ";source=lk:" . $search . ";user=lk:" . $search . ";ip_address=lk:" . $search . ";details=lk:" . $search;
    }

    if ($records != "") {
        $args["records"] = $records;
    }

    $xml = get_xml_auditlog($args);
    return $xml;
}

// Generates a CSV file of audit log data
function get_auditlog_csv()
{

    $xml = get_auditlog_xml(array("records" => ""));

    // output header for csv
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . time() ."-auditlog.csv\"");

    // column definitions
    echo "id,time,source,user,type,ip_address,message,details\n";

    // bail out of trial expired
    if (enterprise_features_enabled() == false)
        return;

    //print_r($xml);
    //exit();

    if ($xml) {
        foreach ($xml->auditlogentry as $a) {
            $source = $a->source;
            if ($source == 'Nagios XI') {
                $source = get_product_name();
            } else if ($source == 'Nagios CCM') {
                $source = _('Core Config Manager');
            }
            echo "\"" . $a->id . "\",\"" . $a->log_time . "\",\"" . $source . "\",\"" . $a->user . "\",\"" . $a->type . "\",\"" . $a->ip_address . "\",\"" . $a->message . "\"\n";
        }
    }
}
