<?php
//
// Event Log Reports
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
    // Only admins can see the event log
    $user_id = grab_request_var("user_id");
    if (is_admin() == false && !get_user_meta($user_id, "authorized_for_monitoring_system")) {
        echo _("You are not authorized to view the event log");
        exit();
    }

    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "csv":
            get_eventlog_csv();
            break;
        case "pdf":
            export_report('eventlog', EXPORT_PDF, EXPORT_LANDSCAPE);
            break;
        case "jpg":
            export_report('eventlog', EXPORT_JPG);
            break;
        case "getpage":
            get_eventlog_page();
            break;
        case "getreport":
            get_eventlog_report();
            break;
        default:
            display_eventlog();
            break;
    }
}


///////////////////////////////////////////////////////////////////
// BACKEND DATA FUNCTIONS
///////////////////////////////////////////////////////////////////

// this function gets event log data in XML format from the backend
/**
 * @param $args
 *
 * @return SimpleXMLElement
 */
function get_eventlog_data($args)
{

    $xml = get_xml_logentries($args);

    return $xml;
}

///////////////////////////////////////////////////////////////////
// HELPER FUNCTIONS
///////////////////////////////////////////////////////////////////

// given an event log type, return corresponding image and text to use...
/**
 * @param $entrytype
 * @param $entrytext
 * @param $img
 * @param $text
 */
function get_eventlog_type_info($entrytype, $entrytext, &$img, &$text)
{
    $img = "info.png";
    $text = "";
    
    // What type of log entry is this?  we change the image used for each line based on what type it is...
    switch ($entrytype) {
        case NAGIOSCORE_LOGENTRY_RUNTIME_ERROR:
            $img = "critical.png";
            $text = _("Runtime Error");
            break;
        case NAGIOSCORE_LOGENTRY_RUNTIME_WARNING:
            $img = "warning.png";
            $text = _("Runtime Warning");
            break;
        case NAGIOSCORE_LOGENTRY_VERIFICATION_ERROR:
            $img = "critical.png";
            $text = _("Verification Error");
            break;
        case NAGIOSCORE_LOGENTRY_VERIFICATION_WARNING:
            $img = "warning.png";
            $text = _("Verification Warning");
            break;
        case NAGIOSCORE_LOGENTRY_CONFIG_ERROR:
            $img = "critical.png";
            $text = _("Configuration Error");
            break;
        case NAGIOSCORE_LOGENTRY_CONFIG_WARNING:
            $img = "warning.png";
            $text = _("Configuration Warning");
            break;
        case NAGIOSCORE_LOGENTRY_PROCESS_INFO:
            $text = _("Process Information");
            break;
        case NAGIOSCORE_LOGENTRY_EVENT_HANDLER:
            $img = "action.gif";
            $text = _("Event Handler");
            break;
        case NAGIOSCORE_LOGENTRY_NOTIFICATION:
            $img = "notify.gif";
            $text = _("Notification");
            break;
        case NAGIOSCORE_LOGENTRY_EXTERNAL_COMMAND:
            $img = "command.png";
            $text = _("External Command");
            break;
        case NAGIOSCORE_LOGENTRY_HOST_UP:
            $img = "recovery.png";
            $text = _("Host Recovery");
            break;
        case NAGIOSCORE_LOGENTRY_HOST_DOWN:
            $img = "critical.png";
            $text = _("Host Down");
            break;
        case NAGIOSCORE_LOGENTRY_HOST_UNREACHABLE:
            $img = "critical.png";
            $text = _("Host Unreachable");
            break;
        case NAGIOSCORE_LOGENTRY_SERVICE_OK:
            $img = "recovery.png";
            $text = _("Service Recovery");
            break;
        case NAGIOSCORE_LOGENTRY_SERVICE_UNKNOWN:
            $img = "unknown.png";
            $text = _("Service Unknown");
            break;
        case NAGIOSCORE_LOGENTRY_SERVICE_WARNING:
            $img = "warning.png";
            $text = _("Service Warning");
            break;
        case NAGIOSCORE_LOGENTRY_SERVICE_CRITICAL:
            $img = "critical.png";
            $text = _("Service Critical");
            break;
        case NAGIOSCORE_LOGENTRY_PASSIVE_CHECK:
            $img = "passiveonly.gif";
            $text = _("Passive Check");
            break;
        case NAGIOSCORE_LOGENTRY_INFO_MESSAGE:

            $img = "info.png";
            $text = _("Information");

            if (strstr($entrytext, " starting...")) {
                $img = "start";
                $text = _("Program Start");
            } else if (strstr($entrytext, " shutting down...")) {
                $img = "stop.gif";
                $text = _("Program Stop");
            } else if (strstr($entrytext, "Bailing out")) {
                $img = "stop.gif";
                $text = _("Program Halt");
            } else if (strstr($entrytext, " restarting...")) {
                $img = "restart.gif";
                $text = _("Program Restart");
            } else if (strstr($entrytext, "SERVICE EVENT HANDLER:")) {
                $img = "serviceevent.gif";
                $text = _("Service Event Handler");
            } else if (strstr($entrytext, "HOST EVENT HANDLER:")) {
                $img = "hostevent.gif";
                $text = _("Host Event Handler");
            } else if (strstr($entrytext, " FLAPPING ALERT:")) {
                $img = "flapping.gif";
                if (strstr($entrytext, ";STARTED;"))
                    $text = _("Flapping Start");
                else if (strstr($entrytext, ";DISABLED;"))
                    $text = _("Flapping Disabled");
                else
                    $text = _("Flapping Stop");
            } else if (strstr($entrytext, " DOWNTIME ALERT:")) {
                $img = "downtime.gif";
                if (strstr($entrytext, ";STARTED;"))
                    $text = _("Scheduled Downtime Start");
                else if (strstr($entrytext, ";CANCELLED;"))
                    $text = _("Scheduled Downtime Cancelled");
                else
                    $text = _("Scheduled Downtime Stop");
            }

            break;
        case NAGIOSCORE_LOGENTRY_HOST_NOTIFICATION:
            $img = "notify.gif";
            $text = _("Host Notification");
            break;
        case NAGIOSCORE_LOGENTRY_SERVICE_NOTIFICATION:
            $img = "notify.gif";
            $text = _("Service Notification");
            break;

        default:
            $img = "info.png";
            break;
    }
}


///////////////////////////////////////////////////////////////////
// REPORT GENERATION FUCNTIONS
///////////////////////////////////////////////////////////////////

// This function displays event log data in HTML
function display_eventlog()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 0);
    $records = grab_request_var("records", get_user_meta(0, 'report_defualt_recordlimit', 10));
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $export = grab_request_var("export", 0);

    // Do not do any processing unless we have default report running enabled
    $disable_report_auto_run = get_option("disable_report_auto_run", 0);

    // Fix custom dates
    if ($reportperiod == "custom") {
        if ($enddate == "") {
            $enddate = strftime("%c", time());
        }
        if ($startdate == "") {
            $startdate = strftime("%c", time() - (60 * 60 * 24));
            $enddate = strftime("%c", time());
        }
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
    );
    if ($search)
        $args["logentry_data"] = "lk:" . $search;
    $xml = get_eventlog_data($args);
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
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record,
    );
    if ($search) {
        $args["logentry_data"] = "lk:" . $search;
    }
    $xml = get_eventlog_data($args);

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

    do_page_start(array("page_title" => _("Event Log")), true);
?>

<script type="text/javascript">
$(document).ready(function () {

    showhidedates();

    // If we should run it right away
    if (!<?php echo $disable_report_auto_run; ?>) {
        run_eventlog_ajax();
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
        run_eventlog_ajax();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>reports/eventlog.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

});

var report_sym = 0;
function run_eventlog_ajax() {
    report_sym = 1;
    setTimeout('show_loading_report()', 500);

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'eventlog.php?'+formvalues;

    current_page = 1;

    $.get(url, {}, function(data) {
        report_sym = 0;
        hide_throbber();
        $('#report').html(data);
        $('#report .tt-bind').tooltip();
    });
}
</script>

<script type='text/javascript' src='<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>'></script>

<form method="get" data-type="eventlog">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <?php echo get_add_myreport_html(_('Event Log'), $_SERVER['PHP_SELF'], array()); ?>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <?php echo _('Download'); ?> <i class="fa fa-caret-down r"></i>
                </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <li><a class="btn-export" data-type="csv" title="<?php echo _("Download as CSV"); ?>"><i class="fa fa-file-text-o l"></i> <?php echo _("CSV"); ?></a></li>
                    <li><a class="btn-export" data-type="pdf" title="<?php echo _("Download as PDF"); ?>"><i class="fa fa-file-pdf-o l"></i> <?php echo _("PDF"); ?></a></li>
                    <li><a class="btn-export" data-type="jpg" title="<?php echo _("Download as JPG"); ?>"><i class="fa fa-file-image-o l"></i> <?php echo _("JPG"); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="reportsearchbox">
            <input type="text" size="15" name="search" id="searchBox" value="<?php echo encode_form_val($search); ?>" placeholder="<?php echo _("Search..."); ?>" class="textfield form-control">
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

            <button type="button" id="run" class='btn btn-sm btn-primary' name='reporttimesubmitbutton'><?php echo _("Run"); ?></button>

        </div>

    </div>
</form>

<div id="report"></div>

<?php
    do_page_end(true);
}


function get_eventlog_page()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 0);
    $records = grab_request_var("records", 10);
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $export = grab_request_var("export", 0);

    // Fix custom dates
    if ($reportperiod == "custom") {
        if ($enddate == "") {
            $enddate = strftime("%c", time());
        }
        if ($startdate == "") {
            $startdate = strftime("%c", time() - (60 * 60 * 24));
            $enddate = strftime("%c", time());
        }
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
    );
    if ($search)
        $args["logentry_data"] = "lk:" . $search;
    $xml = get_eventlog_data($args);
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
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record,
    );
    if ($search) {
        $args["logentry_data"] = "lk:" . $search;
    }
    $xml = get_eventlog_data($args);
?>

<table class="table table-condensed table-hover table-bordered table-striped<?php if ($export) { echo ' table-export'; } ?>">
    <thead>
    <tr>
        <th style="width: 44px; text-align: center;"><?php echo _("Type"); ?></th>
        <th style="width: 145px;"><?php echo _("Date / Time"); ?></th>
        <th><?php echo _("Information"); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($xml) {
        foreach ($xml->logentry as $le) {

            // What type of log entry is this?
            $entrytype = intval($le->logentry_type);
            $entrytext = strval($le->logentry_data);

            get_eventlog_type_info($entrytype, $entrytext, $type_img, $type_text);

            echo '<tr>';
            echo '<td style="width: 44px; text-align: center;"><span class="logentrytype"><img src="' . nagioscore_image($type_img) . '" alt="' . $type_text . '" title="' . $type_text . '"></span></td>';
            echo '<td nowrap>' . $le->entry_time . '</td>';
            echo '<td>' . $entrytext . '</td>';
            echo '</tr>';
        }
    }
    ?>
    </tbody>
</table>

<?php
}


// This function displays event log data in HTML
function get_eventlog_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $page = grab_request_var("page", 0);
    $records = grab_request_var("records", get_user_meta(0, 'report_defualt_recordlimit', 10));
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $export = grab_request_var("export", 0);

    // Fix custom dates
    if ($reportperiod == "custom") {
        if ($enddate == "") {
            $enddate = strftime("%c", time());
        }
        if ($startdate == "") {
            $startdate = strftime("%c", time() - (60 * 60 * 24));
            $enddate = strftime("%c", time());
        }
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
    );
    if ($search)
        $args["logentry_data"] = "lk:" . $search;
    $xml = get_eventlog_data($args);
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
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    if ($export) {

        do_page_start(array("page_title" => _("Event Log")), true);

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
                <div style="font-weight: bold; font-size: 22px;"><?php echo _("Event Log"); ?></div>
                <div><?php echo _("Report covers from"); ?>:
                    <strong><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong>
                    <?php echo _("to"); ?> <strong><?php echo get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong>
                </div>
            </div>
            <div class="clear"></div>
        </div>

    <?php } else { ?>
    
    <h1><?php echo _("Event Log"); ?></h1>

    <div class="report-covers">
        <?php echo _("Report covers from"); ?>:
        <b><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></b> <?php echo _("to"); ?>
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
            "endtime" => $endtime
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
var report_url = '<?php echo get_base_url(); ?>reports/eventlog.php';
var report_url_args = {
    reportperiod: '<?php echo $reportperiod; ?>',
    startdate: '<?php echo $startdate; ?>',
    enddate: '<?php echo $enddate; ?>',
    starttime: '<?php echo $starttime; ?>',
    endtime: '<?php echo $endtime; ?>',
    search: '<?php echo $search; ?>'
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
        <div id="loadscreen-spinner" class="sk-spinner sk-spinner-center sk-spinner-rotating-plane hide"></div>
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


// This function gets the XML records of event log data for multiple
// output formats (CSV, PDF, HTML)
function get_eventlog_xml()
{

    // makes sure user has appropriate license level
    licensed_feature_check();

    // get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");

    // fix search
    if ($search == _("Search..."))
        $search = "";


    // fix custom dates
    if ($reportperiod == "custom") {
        if ($enddate == "") {
            $enddate = strftime("%c", time());
        }
        if ($startdate == "") {
            $startdate = strftime("%c", time() - (60 * 60 * 24));
            $enddate = strftime("%c", time());
        }
    }

    // determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // get XML data from backend - the most basic example
    // this will return all records (no paging), so it can be used for CSV export
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
    );
    if ($search)
        $args["logentry_data"] = "lk:" . $search;
    $xml = get_eventlog_data($args);
    return $xml;
}

// this function generates a CSV file of event log data
function get_eventlog_csv()
{
    $xml = get_eventlog_xml();

    // Output header for csv
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . time() ."-eventlog.csv\"");

    // Column definitions
    echo "type,time,information\n";

    if ($xml) {
        foreach ($xml->logentry as $le) {

            // What type of log entry is this?  we change the image used for each line based on what type it is...
            $entrytype = intval($le->logentry_type);
            $entrytext = strval($le->logentry_data);

            get_eventlog_type_info($entrytype, $entrytext, $type_img, $type_text);

            echo $type_text . "," . $le->entry_time . ",\"" . str_replace( array("\r", "\n", "&apos;"), array(" ", " ", "'"), html_entity_decode($entrytext)) . "\"\n";
        }
    }
}
