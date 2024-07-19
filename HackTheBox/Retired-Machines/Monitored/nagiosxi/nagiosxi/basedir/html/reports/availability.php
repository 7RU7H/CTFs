<?php
//
// Availability Report
// Copyright (c) 2010-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');


// Graphs will not generate if error messaging turned on
ini_set('display_errors', 'off');


// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


route_request();


function route_request()
{
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");

    // Check for proper permissions
    $auth = true;
    if ($service != "" && $service != "*")
        $auth = is_authorized_for_service(0, $host, $service);
    else if ($host != "" || ($host != "" && $service == "*") )
        $auth = is_authorized_for_host(0, $host);
    else if ($hostgroup != "")
        $auth = is_authorized_for_hostgroup(0, $hostgroup);
    else if ($servicegroup != "")
        $auth = is_authorized_for_servicegroup(0, $servicegroup);
    if ($auth == false) {
        echo _("ERROR: You are not authorized to view this report.");
        exit;
    }

    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "csv":
            get_availability_csv();
            break;
        case "pdf":
            export_report('availability', EXPORT_PDF);
            break;
        case "jpg":
            export_report('availability', EXPORT_JPG);
            break;
        case "getchart":
            get_availability_highcharts();
            break;
        case "getservices":
            $host = grab_request_var("host", "");
            $args = array('brevity' => 1, 'host_name' => $host, 'orderby' => 'service_description:a');
            $oxml = get_xml_service_objects($args);
            echo '<option value="">['._("All Services").']</option>';
            echo '<option value="*">['._("Host Only").']</option>';
            if ($oxml) {
                foreach ($oxml->service as $serviceobj) {
                    $name = strval($serviceobj->service_description);
                    echo "<option value='" . $name . "' " . is_selected($service, $name) . ">$name</option>\n";
                }
            }
            break;
        case "getreport":
            run_availability_report();
            break;         
        default:
            display_availability();
            break;
    }
}


///////////////////////////////////////////////////////////////////
// CHART FUNCTIONS
///////////////////////////////////////////////////////////////////


function get_availability_highcharts()
{
    $width = grab_request_var("width", 400);
    $height = grab_request_var("height", 300);
    $title = grab_request_var("title", "");
    $subtitle = grab_request_var("subtitle", "");
    $divId = grab_request_var("divId", "");
    $dashtype = grab_request_var("dashtype", "");
    $rawdata = grab_request_var("data", "");
    $export = grab_request_var("export", 0);

    // If no Raw Data we need to grab data
    if (empty($rawdata)) {
        $data = array();
        $rawdata = get_avail_for_graphs();
        foreach ($rawdata as $name => $value) {
            $data[] = $value;
        }
    } else {
        $rawdata = explode(",", $rawdata);
        $data = $rawdata;
    }

    // Create data for chart
    if ($dashtype == 'hostdata') {
        $colors = '["#b2ff5f", "#FF795F", "#FFC45F"]';
        $formatted_data = array(
            array("name" => "Up", "y" => round($data[0], 3)),
            array("name" => "Down", "y" => round($data[1], 3)),
            array("name" => "Unreachable", "y" => round($data[2], 3))
        );
    } else {
        $colors = '["#b2ff5f", "#FEFF5F", "#FFC45F", "#FF795F"]';
        $formatted_data = array(
            array("name" => "Ok", "y" => round($data[0], 3)),
            array("name" => "Warning", "y" => round($data[1], 3)),
            array("name" => "Unknown", "y" => round($data[2], 3)),
            array("name" => "Critical", "y" => round($data[3], 3))
        );
    }

    $color = "#000000";
    if (is_dark_theme()) {
        $color = '#EEEEEE';
    }

    $output = '<script type="text/javascript">
Highcharts.setOptions({
    colors: '.$colors.'
});

var availability_chart_' . $divId . ';
var options = {
    chart: {
        renderTo: "' . $divId . '",
        width: ' . $width . ',
        height: ' . $height . ',
        type: "pie"
    },
    credits: {
        enabled: false
    },
    title: {
        text: "' . $title . '"
    },
    subtitle: {
        text: "' . $subtitle . '"
    },
    plotOptions: {
        pie: {
            size: 160,
            enableMouseTracking: false,
            animation: false,
            dataLabels: {
                useHTML: true,
                enabled: true,
                color: "'.$color.'",
                connectorColor: "'.$color.'",
                format: "{point.name} {point.percentage:.2f}%",
                distance: 10
            }
        }
    },
    series: [{
        data: '.json_encode($formatted_data).'
    }]
}
availability_chart_' . $divId . ' = new Highcharts.Chart(options);
</script>';
    print $output;
    die();
}

///////////////////////////////////////////////////////////////////
// BACKEND DATA FUNCTIONS
///////////////////////////////////////////////////////////////////

function get_availability_data($args, &$hostdata, &$servicedata)
{
    $data = get_parsed_nagioscore_csv_availability_xml_output($args, true);
    $hostdata = $data[0];
    $servicedata = $data[1];
    return $data[2];
}

///////////////////////////////////////////////////////////////////
// REPORT GENERATION FUCNTIONS
///////////////////////////////////////////////////////////////////

// This function displays event log data in HTML
function display_availability()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");

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

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $export = grab_request_var("export", 0);
    $showonlygraphs = grab_request_var("showonlygraphs", 0);

    // Should we show detail by default?
    $showdetail = 1;
    if ($host == "" && $service == "" && $hostgroup == "" && $servicegroup == "") {
        $showdetail = 0;
    }

    $showdetail = grab_request_var("showdetail", $showdetail);

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // Advanced options
    $timeperiod = grab_request_var("timeperiod", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $advanced = grab_request_var("advanced", 0);
    $display_service_graphs = grab_request_var("servicegraphs", 0);
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));
    $no_services = checkbox_binary(grab_request_var("no_services", 0));

    $disable_report_auto_run = get_option("disable_report_auto_run", 0);

    // Determine title
    if ($service != "")
        $title = _("Service Availability");
    else if ($host != "")
        $title = _("Host Availability");
    else if ($hostgroup != "")
        $title = _("Hostgroup Availability");
    else if ($servicegroup != "")
        $title = _("Servicegroup Availability");
    else
        $title = _("Availability Summary");

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

    // Start the HTML page
    do_page_start(array("page_title" => $title), true);
?>

<script type="text/javascript">
$(document).ready(function () {

    showhidedates();
    verify_graphs_avail();

    // If we should run it right away
    if (!<?php echo $disable_report_auto_run; ?>) {
        run_availability_ajax();
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

    $('#startdateBox').click(function () {
        $('#reportperiodDropdown').val('custom');
        if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
            $('#startdateBox').val('<?php echo $auto_start_date;?>');
            $('#enddateBox').val('<?php echo $auto_end_date;?>');
        }
    });

    $('#enddateBox').click(function () {
        $('#reportperiodDropdown').val('custom');
        if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
            $('#startdateBox').val('<?php echo $auto_start_date;?>');
            $('#enddateBox').val('<?php echo $auto_end_date;?>');
        }
    });

    $('#reportperiodDropdown').change(function () {
        showhidedates();
    });

    $('#hostList').searchable({maxMultiMatch: 9999});
    $('#serviceList').searchable({maxMultiMatch: 9999});
    $('#hostgroupList').searchable({maxMultiMatch: 9999});
    $('#servicegroupList').searchable({maxMultiMatch: 9999});
    if ($('#serviceList').is(':visible')) {
        $('.serviceList-sbox').show();
    } else {
        $('.serviceList-sbox').hide();
    }

    $('#hostList').change(function () {
        $('#hostgroupList').val('');
        $('#servicegroupList').val('');

        if ($(this).val() != '') {
            update_service_list();
            $('#serviceList').show();
            $('.serviceList-sbox').show();
        } else {
            $('#serviceList').val('').hide();
            $('.serviceList-sbox').hide();
        }
        verify_graphs_avail();
    });

    $('#servicegroupList').change(function () {
        $('#hostList').val('');
        $('#hostgroupList').val('');
        $('#serviceList').val('').hide();
        $('.serviceList-sbox').hide();
        verify_graphs_avail();
    });

    $('#hostgroupList').change(function () {
        $('#servicegroupList').val('');
        $('#hostList').val('');
        $('#serviceList').val('').hide();
        $('.serviceList-sbox').hide();
        verify_graphs_avail();
    });

    // Add the ability to show the advanced options section
    $('#advanced-options-btn').click(function () {
        if ($('#advanced-options').is(":visible")) {
            $('#advanced-options').hide();
            $('#advanced').val(0);
            $('#advanced-options-btn').html('<?php echo _("Advanced"); ?> <i class="fa fa-chevron-up"></i>');
        } else {
            $('#advanced-options').show();
            $('#advanced').val(1);
            $('#advanced-options-btn').html('<?php echo _("Advanced"); ?> <i class="fa fa-chevron-down"></i>');
        }
    });

    // Actually return the report
    $('#run').click(function() {
        run_availability_ajax();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>reports/availability.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

});

function verify_graphs_avail() {
    var host = $('#hostList').val();
    var hostgroup = $('#hostgroupList').val();
    var servicegroup = $('#servicegroupList').val();

    if (host == '' && hostgroup == '' && servicegroup == '') {
        $('#display-graphs').prop('disabled', true);
    } else {
        $('#display-graphs').prop('disabled', false);
    }
}

var report_sym = 0;
function run_availability_ajax() {
    report_sym = 1;
    setTimeout('show_loading_report()', 500);

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'availability.php?'+formvalues;

    $.get(url, {}, function(data) {
        report_sym = 0;
        hide_throbber();
        $('#report').html(data);
    });
}
</script>

<script type='text/javascript' src='<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>'></script>

<form method="get" data-type="availability">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <?php echo get_add_myreport_html($title, $_SERVER['PHP_SELF'], array()); ?>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <?php echo _('Download'); ?> <i class="fa fa-caret-down r"></i>
                </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel" style="left: initial; right: 0;">
                    <li class="service-csv"><a class="btn-export" data-type="csv&csvtype=combined" title="<?php echo _("Download both host and service data as CSV"); ?>"><i class="fa fa-file-text-o l"></i> <?php echo _("Combined CSV"); ?></a></li>
                    <li class="service-csv"><a class="btn-export" data-type="csv&csvtype=host" title="<?php echo _("Download only host data as CSV"); ?>"><i class="fa fa-file-text-o l"></i> <?php echo _("Host CSV"); ?></a></li>
                    <li class="host-csv"><a class="btn-export" data-type="csv&csvtype=service" title="<?php echo _("Download only service data as CSV"); ?>"><i class="fa fa-file-text-o l"></i> <?php echo _("Service CSV"); ?></a></li>
                    <li><a class="btn-export" data-type="pdf" title="<?php echo _("Download as PDF"); ?>"><i class="fa fa-file-pdf-o l"></i> <?php echo _("PDF"); ?></a></li>
                    <li><a class="btn-export" data-type="jpg" title="<?php echo _("Download as JPG"); ?>"><i class="fa fa-file-image-o l"></i> <?php echo _("JPG"); ?></a></li>
                </ul>
            </div>
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

            <div class="input-group">
                <label class="input-group-addon"><?php echo _("Limit To"); ?></label>
                <select name="host" id="hostList" style="width: 150px;" class="form-control">
                    <option value=""><?php echo _("Host"); ?>:</option>
                    <?php
                    $args = array('brevity' => 1, 'orderby' => 'host_name:a');
                    $oxml = get_xml_host_objects($args);
                    if ($oxml) {
                        foreach ($oxml->host as $hostobject) {
                            $name = strval($hostobject->host_name);
                            echo "<option value='" . $name . "' " . is_selected($host, $name) . ">$name</option>\n";
                        }
                    }
                    ?>
                </select>
                <select name="service" id="serviceList" style="width: 200px; <?php if (empty($service) && empty($host)) { echo 'display: none;'; } ?>" class="form-control">
                    <option value="">[<?php echo _("All Services"); ?>]</option>
                    <?php
                    $args = array('brevity' => 1, 'host_name' => $host, 'orderby' => 'service_description:a');
                    $oxml = get_xml_service_objects($args);
                    if ($oxml) {
                        foreach ($oxml->service as $serviceobj) {
                            $name = strval($serviceobj->service_description);
                            echo "<option value='" . $name . "' " . is_selected($service, $name) . ">$name</option>\n";
                        }
                    }
                    ?>
                </select>
                <select name="hostgroup" id="hostgroupList" style="width: 150px;" class="form-control">
                    <option value=""><?php echo _("Hostgroup"); ?>:</option>
                    <?php
                    $args = array('orderby' => 'hostgroup_name:a');
                    $oxml = get_xml_hostgroup_objects($args);
                    if ($oxml) {
                        foreach ($oxml->hostgroup as $hg) {
                            $name = strval($hg->hostgroup_name);
                            echo "<option value='" . $name . "' " . is_selected($hostgroup, $name) . ">$name</option>\n";
                        }
                    }
                    ?>
                </select>
                <select name="servicegroup" id="servicegroupList" style="width: 150px;" class="form-control">
                    <option value=""><?php echo _("Servicegroup"); ?>:</option>
                    <?php
                    $args = array('orderby' => 'servicegroup_name:a');
                    $oxml = get_xml_servicegroup_objects($args);
                    if ($oxml) {
                        foreach ($oxml->servicegroup as $sg) {
                            $name = strval($sg->servicegroup_name);
                            echo "<option value='" . $name . "' " . is_selected($servicegroup, $name) . ">$name</option>\n";
                        }
                    }
                    ?>
                </select>
            </div>

            <a id="advanced-options-btn" class="tt-bind" data-placement="bottom" title="<?php echo _('Toggle advanced options'); ?>"><?php echo _('Advanced'); ?>  <?php if (!$advanced) { echo '<i class="fa fa-chevron-up"></i>'; } else { echo '<i class="fa fa-chevron-down"></i>'; } ?></a>
            <input type="hidden" value="<?php echo intval($advanced); ?>" id="advanced" name="advanced">

            <button type="button" id="run" class='btn btn-sm btn-primary' name='reporttimesubmitbutton'><?php echo _("Run"); ?></button>
        </div>

        <div id="advanced-options" style="<?php if (!$advanced) { echo 'display: none;'; } ?>">

            <div class="floatbox-sm">
                <?php echo _("Assume Initial States"); ?>:
                <select name="assumeinitialstates" class="form-control condensed">
                    <option value="yes" <?php if ($assumeinitialstates == "yes") { echo "selected"; } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumeinitialstates == "no") { echo "selected"; } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Assume State Retention"); ?>:
                <select name="assumestateretention" class="form-control condensed">
                    <option value="yes" <?php if ($assumestateretention == "yes") { echo "selected"; } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumestateretention == "no") { echo "selected"; } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Assume States During Program Downtime"); ?>:
                <select name="assumestatesduringdowntime" class="form-control condensed">
                    <option value="yes" <?php if ($assumestatesduringdowntime == "yes") { echo "selected"; } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumestatesduringdowntime == "no") { echo "selected"; } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Include Soft States"); ?>:
                <select name="includesoftstates" class="form-control condensed">
                    <option value="no" <?php if ($includesoftstates == "no") { echo "selected"; } ?>><?php echo _("No"); ?></option>
                    <option value="yes" <?php if ($includesoftstates == "yes") { echo "selected"; } ?>><?php echo _("Yes"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("First Assumed Host State"); ?>:
                <select name="assumedhoststate" class="form-control condensed">
                    <option value="0" <?php if ($assumedhoststate == 0) { echo "selected"; } ?>><?php echo _("Unspecified"); ?></option>
                    <option value="-1" <?php if ($assumedhoststate == -1) { echo "selected"; } ?>><?php echo _("Current State"); ?></option>
                    <option value="3" <?php if ($assumedhoststate == 3) { echo "selected"; } ?>><?php echo _("Host Up"); ?></option>
                    <option value="4" <?php if ($assumedhoststate == 4) { echo "selected"; } ?>><?php echo _("Host Down"); ?></option>
                    <option value="5" <?php if ($assumedhoststate == 5) { echo "selected"; } ?>><?php echo _("Host Unreachable"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("First Assumed Service State"); ?>:
                <select name="assumedservicestate" class="form-control condensed">
                    <option value="0" <?php if ($assumedservicestate == 0) { echo "selected"; } ?>><?php echo _("Unspecified"); ?></option>
                    <option value="-1" <?php if ($assumedservicestate == -1) { echo "selected"; } ?>><?php echo _("Current State"); ?></option>
                    <option value="6" <?php if ($assumedservicestate == 6) { echo "selected"; } ?>><?php echo _("Service Ok"); ?></option>
                    <option value="8" <?php if ($assumedservicestate == 8) { echo "selected"; } ?>><?php echo _("Service Warning"); ?></option>
                    <option value="7" <?php if ($assumedservicestate == 7) { echo "selected"; } ?>><?php echo _("Service Unknown"); ?></option>
                    <option value="9" <?php if ($assumedservicestate == 9) { echo "selected"; } ?>><?php echo _("Service Critical"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Report Time Period"); ?>:
                <select name="timeperiod" class="form-control condensed">
                    <option value="" <?php if ($timeperiod == "") {
                        echo "selected";
                    } ?>><?php echo _("None"); ?></option>
                    <?php
                    // Get a list of timeperiods
                    $request = array("objecttype_id" => 9);
                    $objects = new SimpleXMLElement(get_objects_xml_output($request, false));
                    foreach ($objects as $object) {
                        $tp = strval($object->name1);
                        if (!empty($tp)) {
                            echo "<option " . is_selected($timeperiod, $tp) . ">" . $tp . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('Must select a host/service, hostgroup, or servicegroup selected'); ?>">
                        <input type="checkbox" value="1" id="display-graphs" name="servicegraphs" <?php echo is_checked($display_service_graphs, 1); ?>> <?php echo _("Display service performance graphs"); ?>
                    </label>
                </div>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                <label title="<?php echo _('This will count any state during scheduled downtime as OK for the Availability report'); ?>">
                    <input type="checkbox" name="dont_count_downtime" <?php echo is_checked($dont_count_downtime, 1); ?>> <?php echo _("Hide scheduled downtime"); ?>
                </label>
                </div>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('This will count any WARNING state as OK for the Availability report'); ?>">
                        <input type="checkbox" name="dont_count_warning" <?php echo is_checked($dont_count_warning, 1); ?>> <?php echo _("Hide WARNING states"); ?>
                    </label>
                </div>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('This will count any UNKNOWN state as OK for the Availability report'); ?>">
                        <input type="checkbox" name="dont_count_unknown" <?php echo is_checked($dont_count_unknown, 1); ?>> <?php echo _("Hide UNKNOWN/UNREACHABLE states"); ?>
                    </label>
                </div>
            </div>

            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('Show only the hosts with no service data shown'); ?>">
                        <input type="checkbox" name="no_services" <?php echo is_checked($no_services, 1); ?>> <?php echo _("Do not show service data"); ?>
                    </label>
                </div>
            </div>

            <div style="clear: both;"></div>

        </div>

    </div>
</form>

<div id="report"></div>

<?php
}

function run_availability_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");

    // Fix custom dates
    if ($reportperiod == "custom") {
        if ($enddate == "") {
            $enddate = strftime("%c", time());
        }
        if ($startdate == "") {
            $startdate = strftime("%c", time() - (60 * 60 * 24));
            $enddate = strftime("%c", time());
        }
    } else {
        $startdate = "";
        $enddate = "";
    }

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $export = grab_request_var("export", 0);
    $showonlygraphs = grab_request_var("showonlygraphs", 0);

    // Should we show detail by default?
    $showdetail = 1;
    if ($host == "" && $service == "" && $hostgroup == "" && $servicegroup == "") {
        $showdetail = 0;
    }

    $showdetail = grab_request_var("showdetail", $showdetail);

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // Advanced options
    $timeperiod = grab_request_var("timeperiod", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $advanced = grab_request_var("advanced", 0);
    $display_service_graphs = grab_request_var("servicegraphs", 0);
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));
    $no_services = checkbox_binary(grab_request_var("no_services", 0));

    // Determine title
    if ($service != "" && $service != "*") {
        $title = _("Service Availability");
    } else if ($host != "") {
        $title = _("Host Availability");
    } else if ($hostgroup != "") {
        $title = _("Hostgroup Availability");
    } else if ($servicegroup != "") {
        $title = _("Servicegroup Availability");
    } else {
        $title = _("Availability Summary");
    }

    // LOGO FOR GENERATED PDFS
    if ($export && !$showonlygraphs) {

        do_page_start(array("page_title" => $title), true);

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

        <div style="padding-bottom: 10px;">
            <div style="float: left; margin-right: 30px;">
                <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0"
                     alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
            </div>
            <div style="float: left; height: 44px;">
                <div style="font-weight: bold; font-size: 22px; padding-bottom: 4px;"><?php echo $title; ?></div>
                <div><?php echo _("Report covers from"); ?>:
                    <strong><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong> <?php echo _("to"); ?>
                    <strong><?php echo get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></strong>
                </div>
            </div>
            <div class="clear"></div>
        </div>

    <?php
    } else if (!$showonlygraphs) {

        $state_history_link = '';
        if ($service != "" && $service != "*") {
            $url = "statehistory.php?host=" . urlencode($host) . "&service=" . urlencode($service) . "&reportperiod=" . urlencode($reportperiod) . "&startdate=" . urlencode($startdate) . "&enddate=" . urlencode($enddate);
            $state_history_link = "<a href='" . $url . "' style='margin-left: 4px;'><img src='" . theme_image("history2.png") . "' alt='" . _("View State History") . "' title='" . _("View State History") . "'></a>";
        } else if ($host != "") {
            $url = "statehistory.php?host=" . urlencode($host) . "&reportperiod=" . urlencode($reportperiod) . "&startdate=" . urlencode($startdate) . "&enddate=" . urlencode($enddate);
            $state_history_link = "<a href='" . $url . "' style='margin-left: 4px;'><img src='" . theme_image("history2.png") . "' alt='" . _("View State History") . "' title='" . _("View State History") . "'></a>";
        }
    ?>

    <h1><?php echo $title; ?> <?php echo $state_history_link; ?></h1>

    <?php
    }

    if (!$showonlygraphs) {
        if ($service != "" && $service != "*") {
            ?>
            <div class="servicestatusdetailheader">
                <div class="serviceimage">
                    <!--image-->
                    <?php show_object_icon($host, $service, true); ?>
                </div>
                <div class="servicetitle">
                    <div class="servicename">
                        <a href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo encode_form_val($service); ?></a>
                        <?php echo get_service_alias($host, $service); ?>
                    </div>
                    <div class="hostname">
                        <a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo encode_form_val($host); ?></a>
                        <?php echo get_host_alias($host); ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        <?php
        } else if ($host != "") {
            ?>
            <div class="hoststatusdetailheader">
                <div class="hostimage">
                    <?php show_object_icon($host, "", true); ?>
                </div>
                <div class="hosttitle">
                    <div class="hostname">
                        <a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo encode_form_val($host); ?></a>
                        <?php echo get_host_alias($host); ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        <?php
        } else if ($hostgroup != "") {
            ?>
            <div class="hoststatusdetailheader">
                <div class="hosttitle">
                    <div class="hostname"><?php echo encode_form_val($hostgroup) . get_hostgroup_alias($hostgroup); ?></div>
                </div>
                <div class="clear"></div>
            </div>
        <?php
        } else if ($servicegroup != "") {
            ?>
            <div class="hoststatusdetailheader">
                <div class="hosttitle">
                    <div class="hostname"><?php echo encode_form_val($servicegroup) . get_servicegroup_alias($servicegroup); ?></div>
                </div>
                <div class="clear"></div>
            </div>
        <?php
        }
    }

    if (!$export && !$showonlygraphs) {
    ?>

        <div class="report-covers" style="padding: 10px 0 0 0;">
            <?php echo _("Report covers from"); ?>:
            <b><?php echo get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></b> <?php echo _("to"); ?>
            <b><?php echo get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?></b>
        </div>

    <?php } ?>

    <div id='availabilityreport' class="availabilityreport <?php if ($export) { echo "report-export"; } ?>">
    <?php

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC SERVICE
    ///////////////////////////////////////////////////////////////////////////
    if ($service != "" && $service != "*") {

        // Get service availability
        $args = array(
            "host" => $host,
            "service" => $service,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo "<p>" . _("Availability data is not available when monitoring engine is not running") . "</p>";
        } else {

            $service_ok = 0;
            $service_warning = 0;
            $service_unknown = 0;
            $service_critical = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }
                    
                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }
                    
                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }
                }
            }

            // Service chart
            $service_availability = _("Service Availability");
            $service_availability_sub = $host . ": " . $service;

            $dargs = array(
                DASHLET_ARGS => array(
                    'dashtype' => 'servicedata',
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'reportperiod' => $reportperiod,
                    'title' => $service_availability,
                    'subtitle' => $service_availability_sub,
                    'host' => $host,
                    'service' => $service,
                    'data' => "{$service_ok},{$service_warning},{$service_unknown},{$service_critical}",
                    'export' => $export
                ),
            );
            display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);

            // Service table
            if ($servicedata && !$showonlygraphs) {
                echo '<div class="availability-services">
                        <div style="float: left; padding-right: 20px;">';
                echo "<h5>" . _("Service Data") . "</h5>";
                echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-service'>";
                echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("Service") . "</th><th>" . _("Ok") . "</th><th>" . _("Warning") . "</th><th>" . _("Unknown") . "</th><th>" . _("Critical") . "</th></tr></thead>";
                echo "<tbody>";
                $lasthost = "";
                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }
                    
                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }
                    
                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    // Newline
                    if ($lasthost != $hn && $lasthost != "") {
                        echo "<tr><td colspan='6'></td></tr>";
                    }

                    echo "<tr>";
                    if ($lasthost != $hn)
                        echo "<td>" . $hn . "</td>";
                    else
                        echo "<td></td>";
                    echo "<td>" . $sd . "</td>";
                    echo "<td>" . $ok . "%</td>";
                    echo "<td>" . $wa . "%</td>";
                    echo "<td>" . $un . "%</td>";
                    echo "<td>" . $cr . "%</td>";
                    echo "</tr>";

                    $lasthost = $hn;
                }

                echo "</tbody>";
                echo "</table>
                </div>";

                // Loop through each service and display a perfdata graph
                if ($display_service_graphs && !$showonlygraphs) {
                    echo '<div style="float: left;">';
                    foreach ($servicedata as $s) {

                        $host = $s['host_name'];
                        $service = $s['service_description'];

                        // If rendering as pdf
                        if ($export)
                            $mode = "pdf";
                        else
                            $mode = "";

                        if (pnp_chart_exists($host, $service)) {
                            echo "<div class='serviceperfgraphcontainer pd-container'>";
                            $dargs = array(
                                DASHLET_ADDTODASHBOARDTITLE => _("Add This Performance Graph To A Dashboard"),
                                DASHLET_ARGS => array(
                                    "host_id" => get_host_id($host),
                                    "hostname" => $host,
                                    "servicename" => $service,
                                    "startdate" => date("Y-m-d H:i", $starttime),
                                    "enddate" => date("Y-m-d H:i", $endtime),
                                    "width" => "",
                                    "height" => "",
                                    "mode" => PERFGRAPH_MODE_SERVICEDETAIL,
                                    "render_mode" => $mode),
                                DASHLET_TITLE => $host . " " . $service . " " . _("Performance Graph"));

                            display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);
                            echo "</div>";
                        }
                    }
                    echo '</div>';
                }
                // End: Display Service Graphs

                echo '</div>';
            }

        }

    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOST
    ///////////////////////////////////////////////////////////////////////////
    else if ($host != "") {

        // Get availability
        $args = array(
            "host" => $host,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        if ($service == "*") {
            $args['hostonly'] = true;
        }
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo "<p>" . _("Availability data is not available when monitoring engine is not running") . ".</p>";
        } else {

            $host_up = 0;
            $host_down = 0;
            $host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }
                    
                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }
                    
                }
            }

            // Host chart
            $host_availability = _("Host Availability");
            $host_availability_sub = $host;

            $dargs = array(
                DASHLET_ARGS => array(
                    'dashtype' => 'hostdata',
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'reportperiod' => $reportperiod,
                    'title' => $host_availability,
                    'subtitle' => $host_availability_sub,
                    'host' => $host,
                    'data' => "{$host_up},{$host_down},{$host_unreachable}",
                    'export' => $export
                )
            );
            display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }
                    
                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }
                    
                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            if (!$no_services) {

                $average_service_availability = _("Average Service Availability");
                $average_service_availability_sub = $host . _(": All Services");

                $dargs = array(
                    DASHLET_ARGS => array(
                        'dashtype' => 'servicedata',
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                        'reportperiod' => $reportperiod,
                        'title' => $average_service_availability,
                        'subtitle' => $average_service_availability_sub,
                        'host' => $host,
                        'data' => "{$avg_service_ok},{$avg_service_warning},{$avg_service_unknown},{$avg_service_critical}",
                        'export' => $export
                    ),
                );

                // only show service chart if there are services (some percent exists)
                if (($avg_service_ok + $avg_service_warning + $avg_service_unknown + $avg_service_critical) > 0) {
                    display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);
                }

            }

            // Host table
            if ($hostdata && !$showonlygraphs) {
                echo "<h5>" . _("Host Data") . "</h5>";
                echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-host'>";
                echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("UP") . "</th><th>" . _("Down") . "</th><th>" . _("Unreachable") . "</th></tr></thead>";
                echo "<tbody>";
                foreach ($hostdata as $h) {
                    $hn = $h['host_name'];

                    if (!$dont_count_downtime) {
                        $up = $h['percent_known_time_up'];
                        $dn = $h['percent_known_time_down'];
                        $un = $h['percent_known_time_unreachable'];
                    } else {
                        $up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled'];
                        $dn = $h['percent_known_time_down_unscheduled'];
                        $un = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $up = (($up += $h['percent_known_time_unreachable_unscheduled']) > 100 ? 100 : $up);
                        $un = 0.00;
                    }

                    echo "<tr>";
                    echo "<td>" . $hn . "</td>";
                    echo "<td>" . $up . "%</td>";
                    echo "<td>" . $dn . "%</td>";
                    echo "<td>" . $un . "%</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }

            if ($servicedata && !$showonlygraphs && !$no_services) {
                echo '<div class="availability-services">';

                if ($display_service_graphs) {
                    echo '<div style="float: left; padding-right: 20px;">';
                } else {
                    echo '<div>';
                }

                echo "<h5>" . _("Service Data") . "</h5>";
                echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-service'>";
                echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("Service") . "</th><th>" . _("Ok") . "</th><th>" . _("Warning") . "</th><th>" . _("Unknown") . "</th><th>" . _("Critical") . "</th></tr></thead>";

                echo "<tbody>";
                $lasthost = "";
                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }
                    
                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }
                    
                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    // newline
                    if ($lasthost != $hn && $lasthost != "") {
                        echo "<tr><td colspan='6'></td></tr>";
                    }

                    echo "<tr>";
                    if ($lasthost != $hn)
                        echo "<td>" . $hn . "</td>";
                    else
                        echo "<td></td>";
                    echo "<td>" . $sd . "</td>";
                    echo "<td>" . $ok . "%</td>";
                    echo "<td>" . $wa . "%</td>";
                    echo "<td>" . $un . "%</td>";
                    echo "<td>" . $cr . "%</td>";
                    echo "</tr>";

                    $lasthost = $hn;
                }


                echo "</tbody><tfoot>";
                echo "<tr><td></td><td><b>" . _("Average") . "</b></td><td>" . number_format($avg_service_ok, 3) . "%</td><td>" . number_format($avg_service_warning, 3) . "%</td><td>" . number_format($avg_service_unknown, 3) . "%</td><td>" . number_format($avg_service_critical, 3) . "%</td></tr>";
                echo "</foot>";
                echo "</table>";
                echo '</div>';

                // Loop through each service and display a perfdata graph
                if ($display_service_graphs && !$showonlygraphs) {
                    echo '<div style="float: left;">';
                    foreach ($servicedata as $s) {

                        $host = $s['host_name'];
                        $service = $s['service_description'];

                        // If rendering as pdf
                        if ($export)
                            $mode = "pdf";
                        else
                            $mode = "";

                        if (pnp_chart_exists($host, $service)) {
                            echo "<div class='serviceperfgraphcontainer pd-container'>";
                            $dargs = array(
                                DASHLET_ADDTODASHBOARDTITLE => _("Add This Performance Graph To A Dashboard"),
                                DASHLET_ARGS => array(
                                    "host_id" => get_host_id($host),
                                    "hostname" => $host,
                                    "servicename" => $service,
                                    "startdate" => date("Y-m-d H:i", $starttime),
                                    "enddate" => date("Y-m-d H:i", $endtime),
                                    "width" => "",
                                    "height" => "",
                                    "mode" => PERFGRAPH_MODE_SERVICEDETAIL,
                                    "render_mode" => $mode),
                                DASHLET_TITLE => $host . " " . $service . " " . _("Performance Graph"));

                            display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);
                            echo "</div>";
                        }
                    }
                    echo '</div>';
                }
                // End: Display Service Graphs
            }

            echo '</div>';
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOSTGROUP OR SERVICEGROUP
    ///////////////////////////////////////////////////////////////////////////
    else if ($hostgroup != "" || $servicegroup != "") {

        // get availability
        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        if ($hostgroup != "") {
            $args["hostgroup"] = $hostgroup;
        } else {
            $args["servicegroup"] = $servicegroup;
        }
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo "<p>" . _("Availability data is not available when monitoring engine is not running") . ".</p>";
        } else {
            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }
                    
                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            // Host chart
            $title = _('Average Host Availability');

            if ($hostgroup != "")
                $targetgroup = $hostgroup;
            else
                $targetgroup = $servicegroup;

            $subtitle = $targetgroup . _(': All Hosts');
            $up = _('Up');
            $down = _('Down');
            $unreachable = _('Unreachable');

            $dargs = array(
                DASHLET_ARGS => array(
                    'dashtype' => 'hostdata',
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'reportperiod' => $reportperiod,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'hostgroup' => $hostgroup,
                    'servicegroup' => $servicegroup,
                    'data' => "{$avg_host_up},{$avg_host_down},{$avg_host_unreachable}",
                    'export' => $export
                )
            );
            display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }
                    
                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }
                    
                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            if (!$no_services) {

                // Service chart
                $title = _('Average Service Availability');

                if ($hostgroup != "")
                    $targetgroup = $hostgroup;
                else
                    $targetgroup = $servicegroup;

                $subtitle = $targetgroup . _(': All Services');
                $ok = _('Ok');
                $warning = _('Warning');
                $unknown = _('Unknown');
                $critical = _('Critical');
                
                $dargs = array(
                    DASHLET_ARGS => array(
                        'dashtype' => 'servicedata',
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                        'reportperiod' => $reportperiod,
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'hostgroup' => $hostgroup,
                        'servicegroup' => $servicegroup,
                        'data' => "{$avg_service_ok},{$avg_service_warning},{$avg_service_unknown},{$avg_service_critical}",
                        'export' => $export
                    ),
                );
                display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);
            
            }

            // Host table
            if ($hostdata && !$showonlygraphs) {
                echo "<h5>" . _('Host Data') . "</h5>";
                echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-host'>";
                echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("UP") . "</th><th>" . _("Down") . "</th><th>" . _("Unreachable") . "</th></tr></thead>";
                echo "<tbody>";
                if ($showdetail == 1) {
                    $lasthost = "";
                    foreach ($hostdata as $h) {

                        $hn = $h['host_name'];

                        if (!$dont_count_downtime) {
                            $up = $h['percent_known_time_up'];
                            $dn = $h['percent_known_time_down'];
                            $un = $h['percent_known_time_unreachable'];
                        } else {
                            $up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled'];
                            $dn = $h['percent_known_time_down_unscheduled'];
                            $un = $h['percent_known_time_unreachable_unscheduled'];
                        }
                        
                        if ($dont_count_unknown) {
                            $up = (($up += $h['percent_known_time_unreachable_unscheduled']) > 100 ? 100 : $up);
                            $un = 0.00;
                        }

                        echo "<tr>";
                        echo "<td>" . $hn . "</td>";
                        echo "<td>" . number_format($up, 3) . "%</td>";
                        echo "<td>" . number_format($dn, 3) . "%</td>";
                        echo "<td>" . number_format($un, 3) . "%</td>";
                        echo "</tr>";

                        $lasthost = $hn;
                    }
                }

                echo "</tbody><tfoot>";
                echo "<tr><td><b>" . _("Average") . "</b></td><td>" . number_format($avg_host_up, 3) . "%</td><td>" . number_format($avg_host_down, 3) . "%</td><td>" . number_format($avg_host_unreachable, 3) . "%</td></tr>";
                echo "</tfoot>";
                echo "</table>";
            }

            // Service table
            if ($servicedata && !$showonlygraphs && !$no_services) {
                echo '<div class="availability-services">
                        <div style="float: left; padding-right: 20px;">';

                echo "<h5>" . _('Service Data') . "</h5>";
                echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-service'>";

                echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("Service") . "</th><th>" . _("Ok") . "</th><th>" . _("Warning") . "</th><th>" . _("Unknown") . "</th><th>" . _("Critical") . "</th></tr></thead>";
                echo "<tbody>";
                if ($showdetail == 1) {
                    $lasthost = "";
                    foreach ($servicedata as $s) {

                        $hn = $s['host_name'];
                        $sd = $s['service_description'];

                        if (!$dont_count_downtime) {
                            $ok = $s['percent_known_time_ok'];
                            $wa = $s['percent_known_time_warning'];
                            $un = $s['percent_known_time_unknown'];
                            $cr = $s['percent_known_time_critical'];
                        } else {
                            $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                            $wa = $s['percent_known_time_warning_unscheduled'];
                            $un = $s['percent_known_time_unknown_unscheduled'];
                            $cr = $s['percent_known_time_critical_unscheduled'];
                        }
                        
                        if ($dont_count_warning) {
                            $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                            $wa = 0.00;
                        }
                        
                        if ($dont_count_unknown) {
                            $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                            $un = 0.00;
                        }

                        echo "<tr>";
                        if ($lasthost != $hn)
                            echo "<td>" . $hn . "</td>";
                        else
                            echo "<td></td>";
                        echo "<td>" . $sd . "</td>";
                        echo "<td>" . number_format($ok, 3) . "%</td>";
                        echo "<td>" . number_format($wa, 3) . "%</td>";
                        echo "<td>" . number_format($un, 3) . "%</td>";
                        echo "<td>" . $cr . "%</td>";
                        echo "</tr>";

                        $lasthost = $hn;
                    }
                }

                echo "</tbody><tfoot>";
                echo "<tr><td></td><td><b>" . _("Average") . "</b></td><td>" . number_format($avg_service_ok, 3) . "%</td><td>" . number_format($avg_service_warning, 3) . "%</td><td>" . number_format($avg_service_unknown, 3) . "%</td><td>" . number_format($avg_service_critical, 3) . "%</td></tr>";
                echo "</tfoot>";
                echo "</table>";
                echo '</div>';

                // Loop through each service and display a perfdata graph
                if ($display_service_graphs && !$showonlygraphs) {
                    echo '<div style="float: left;">';
                    foreach ($servicedata as $s) {

                        $host = $s['host_name'];
                        $service = $s['service_description'];

                        // If rendering as pdf
                        if ($export)
                            $mode = "pdf";
                        else
                            $mode = "";

                        if (pnp_chart_exists($host, $service)) {
                            echo "<div class='serviceperfgraphcontainer pd-container'>";
                            $dargs = array(
                                DASHLET_ADDTODASHBOARDTITLE => _("Add This Performance Graph To A Dashboard"),
                                DASHLET_ARGS => array(
                                    "host_id" => get_host_id($host),
                                    "hostname" => $host,
                                    "servicename" => $service,
                                    "startdate" => date("Y-m-d H:i", $starttime),
                                    "enddate" => date("Y-m-d H:i", $endtime),
                                    "width" => "",
                                    "height" => "",
                                    "mode" => PERFGRAPH_MODE_SERVICEDETAIL,
                                    "render_mode" => $mode),
                                DASHLET_TITLE => $host . " " . $service . " " . _("Performance Graph"));

                            display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);
                            echo "</div>";
                        }
                    }
                    echo '</div>';
                }
                // End: Display Service Graphs

                echo '</div>';
            }

        }

    }


    ///////////////////////////////////////////////////////////////////////////
    // OVERVIEW (ALL HOSTS OR SERVICES)
    ///////////////////////////////////////////////////////////////////////////
    else {

        // get availability
        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo "<p>" . _("Availability data is not available when monitoring engine is not running") . ".</p>";
        } else {

            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            // Host chart
            $title = _('Average Host Availability');
            $subtitle = _('All Hosts');
            $up = _('Up');
            $down = _('Down');
            $unreachable = _('Unreachable');

            $dargs = array(
                DASHLET_ARGS => array(
                    'dashtype' => 'hostdata',
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'reportperiod' => $reportperiod,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'data' => "{$avg_host_up},{$avg_host_down},{$avg_host_unreachable}",
                    'export' => $export
                )
            );
            display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_ok = 0;
            $count_service_warning = 0;
            $count_service_critical = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            if (!$no_services) {

                // Service chart
                $title = _('Average Service Availability');
                $subtitle = _('All Services');
                $ok = _('Ok');
                $warning = _('Warning');
                $unknown = _('Unknown');
                $critical = _('Critical');

                $dargs = array(
                    DASHLET_ARGS => array(
                        'dashtype' => 'servicedata',
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                        'reportperiod' => $reportperiod,
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'data' => "{$avg_service_ok},{$avg_service_warning},{$avg_service_unknown},{$avg_service_critical}",
                        'export' => $export
                    ),
                );
                display_dashlet("availability", "", $dargs, DASHLET_MODE_OUTBOARD);

            }

            if (!$showonlygraphs) {

                // Host table
                if ($hostdata && !$showonlygraphs) {
                    echo "<h5>" . _('Host Data') . "</h5>";
                    echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-host'>";
                    echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("UP") . "</th><th>" . _("Down") . "</th><th>" . _("Unreachable") . "</th></tr></thead>";
                    echo "<tbody>";
                    if ($showdetail == 1) {
                        $lasthost = "";
                        foreach ($hostdata as $h) {

                            $hn = $h['host_name'];

                            if (!$dont_count_downtime) {
                                $up = $h['percent_known_time_up'];
                                $dn = $h['percent_known_time_down'];
                                $un = $h['percent_known_time_unreachable'];
                            } else {
                                $up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled'];
                                $dn = $h['percent_known_time_down_unscheduled'];
                                $un = $h['percent_known_time_unreachable_unscheduled'];
                            }

                            if ($dont_count_unknown) {
                                $up = (($up += $h['percent_known_time_unreachable_unscheduled']) > 100 ? 100 : $up);
                                $un = 0.00;
                            }

                            echo "<tr>";
                            if ($lasthost != $hn)
                                echo "<td>" . $hn . "</td>";
                            else
                                echo "<td></td>";
                            echo "<td>" . $sd . "</td>";
                            echo "<td>" . number_format($ok, 3) . "%</td>";
                            echo "<td>" . number_format($wa, 3) . "%</td>";
                            echo "<td>" . number_format($un, 3) . "%</td>";
                            echo "<td>" . $cr . "%</td>";
                            echo "</tr>";

                            $lasthost = $hn;
                        }
                    }

                    echo "</tbody><tfoot>";
                    echo "<tr><td><b>" . _("Average") . "</b></td><td>" . number_format($avg_host_up, 3) . "%</td><td>" . number_format($avg_host_down, 3) . "%</td><td>" . number_format($avg_host_unreachable, 3) . "%</td></tr>";
                    echo "</tfoot>";
                    echo "</table>";
                }

                // Service table
                if ($servicedata && !$showonlygraphs && !$no_services) {
                    echo "<h5>" . _("Service Data") . "</h5>";
                    echo "<table class='table table-condensed table-auto-width table-striped table-bordered table-service'>";

                    echo "<thead><tr><th>" . _("Host") . "</th><th>" . _("Service") . "</th><th>" . _("Ok") . "</th><th>" . _("Warning") . "</th><th>" . _("Unknown") . "</th><th>" . _("Critical") . "</th></tr></thead>";
                    echo "<tbody>";
                    if ($showdetail == 1) {
                        $lasthost = "";
                        foreach ($servicedata as $s) {

                            $hn = $s['host_name'];
                            $sd = $s['service_description'];

                            if (!$dont_count_downtime) {
                                $ok = $s['percent_known_time_ok'];
                                $wa = $s['percent_known_time_warning'];
                                $un = $s['percent_known_time_unknown'];
                                $cr = $s['percent_known_time_critical'];
                            } else {
                                $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                                $wa = $s['percent_known_time_warning_unscheduled'];
                                $un = $s['percent_known_time_unknown_unscheduled'];
                                $cr = $s['percent_known_time_critical_unscheduled'];
                            }

                            if ($dont_count_warning) {
                                $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                                $wa = 0.00;
                            }

                            if ($dont_count_unknown) {
                                $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                                $un = 0.00;
                            }

                            echo "<tr>";
                            if ($lasthost != $hn)
                                echo "<td>" . $hn . "</td>";
                            else
                                echo "<td></td>";
                            echo "<td>" . $sd . "</td>";
                            echo "<td>" . number_format($ok, 3) . "%</td>";
                            echo "<td>" . number_format($wa, 3) . "%</td>";
                            echo "<td>" . number_format($un, 3) . "%</td>";
                            echo "<td>" . $cr . "</td>";
                            echo "</tr>";

                            $lasthost = $hn;
                        }
                    }

                    echo "</tbody><tfoot>";
                    echo "<tr><td></td><td><b>" . _("Average") . "</b></td><td>" . number_format($avg_service_ok, 3) . "%</td><td>" . number_format($avg_service_warning, 3) . "%</td><td>" . number_format($avg_service_unknown, 3) . "%</td><td>" . number_format($avg_service_critical, 3) . "%</td></tr>";
                    echo "</tfoot>";
                    echo "</table>";
                }
            }

        } // end show only graphs
    }
    ?>
    </div>
    <?php
    // closes the HTML page
    if (!$showonlygraphs) {
        do_page_end(true);
    }
}


/**
 * @param $state
 *
 * @return string
 */
function get_avail_color($state)
{
    switch ($state) {
        case "up":
        case "ok":
            $c = "56DA56";
            break;
        case "down":
            $c = "E9513D";
            break;
        case "unreachable":
            $c = "CB2525";
            break;
        case "warning":
            $c = "F6EB3A";
            break;
        case "critical":
            $c = "F35F3D";
            break;
        case "unknown":
            $c = "F3AC3D";
            break;
        default:
            $c = "000000";
            break;
    }
    return "%23" . $c;
}


function get_avail_for_graphs()
{
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $dashtype = grab_request_var("dashtype", "");

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");

    // Generate start/end times
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // Advanced options
    $timeperiod = grab_request_var("timeperiod", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $advanced = grab_request_var("advanced", 0);
    $display_service_graphs = grab_request_var("servicegraphs", 0);
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC SERVICE
    ///////////////////////////////////////////////////////////////////////////

    if ($service != "") {

        // Get service availability
        $args = array(
            "host" => $host,
            "service" => $service,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running.");
        } else {
            if (!empty($servicedata)) {
                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }
                }
            }

            // Output the data for the graphs
            $data = array(
                'avg_service_ok' => $ok,
                'avg_service_warning' => $wa,
                'avg_service_unknown' => $un,
                'avg_service_critical' => $cr
            );
            return $data;
        }

    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOST
    ///////////////////////////////////////////////////////////////////////////

    else if ($host != "") {

        $args = array(
            "host" => $host,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );

        // Check dashtype
        if ($dashtype == "hostdata") {
            $args['hostonly'] = true;
        } else if ($dashtype == "servicedata") {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running.");
        } else {

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if (!empty($servicedata)) {
                foreach ($servicedata as $s) {

                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            $host_up = 0;
            $host_down = 0;
            $host_unreachable = 0;

            // Host table
            if (!empty($hostdata)) {
                foreach ($hostdata as $h) {
                    $host_name = $h['host_name'];

                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }
                }
            }
        }

        // Output the data for the graphs
        if ($dashtype == 'hostdata') {
            $data = array(
                'avg_host_up' => $host_up,
                'avg_host_down' => $host_down,
                'avg_host_unreachable' => $host_unreachable
            );
        } else {
            $data = array(
                'avg_service_ok' => $avg_service_ok,
                'avg_service_warning' => $avg_service_warning,
                'avg_service_unknown' => $avg_service_unknown,
                'avg_service_critical' => $avg_service_critical
            );
        }
        return $data;

    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOSTGROUP OR SERVICEGROUP
    ///////////////////////////////////////////////////////////////////////////

    else if ($hostgroup != "" || $servicegroup != "") {

        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        if ($hostgroup != "") {
            $args["hostgroup"] = $hostgroup;
        } else {
            $args["servicegroup"] = $servicegroup;
        }

        // Check dashtype
        if ($dashtype == "hostdata") {
            $args['hostonly'] = true;
        } else if ($dashtype == "servicedata") {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running.");
        } else {

            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if (!empty($hostdata)) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if (!empty($servicedata)) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }
        }

        // Output the data for the graphs
        if ($dashtype == 'hostdata') {
            $data = array(
                'avg_host_up' => $avg_host_up,
                'avg_host_down' => $avg_host_down,
                'avg_host_unreachable' => $avg_host_unreachable
            );
        } else {
            $data = array(
                'avg_service_ok' => $avg_service_ok,
                'avg_service_warning' => $avg_service_warning,
                'avg_service_unknown' => $avg_service_unknown,
                'avg_service_critical' => $avg_service_critical
            );
        }
        return $data;

    }

    ///////////////////////////////////////////////////////////////////////////
    // OVERVIEW (ALL HOSTS AND SERVICES)
    ///////////////////////////////////////////////////////////////////////////
    else {

        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );

        // Check dashtype
        if ($dashtype == "hostdata") {
            $args['hostonly'] = true;
        } else if ($dashtype == "servicedata") {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if ($have_data == false) {
            echo _("Availability data is not available when monitoring engine is not running.");
        } else {

            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if (!empty($hostdata)) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if (!empty($servicedata)) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }
        }

        // Output the data for the graphs
        if ($dashtype == 'hostdata') {
            $data = array(
                'avg_host_up' => $avg_host_up,
                'avg_host_down' => $avg_host_down,
                'avg_host_unreachable' => $avg_host_unreachable
            );
        } else {
            $data = array(
                'avg_service_ok' => $avg_service_ok,
                'avg_service_warning' => $avg_service_warning,
                'avg_service_unknown' => $avg_service_unknown,
                'avg_service_critical' => $avg_service_critical
            );
        }
        return $data;

    }

}


function get_availability_csv()
{

    // get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");

    $csvtype = grab_request_var("csvtype", "service");

    // determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // Advanced options
    $timeperiod = grab_request_var("timeperiod", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $advanced = grab_request_var("advanced", 0);
    $display_service_graphs = grab_request_var("servicegraphs", 0);
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . time() ."-availability.csv\"");

    if ($csvtype !== "combined") {
        write_csv_header($csvtype);
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC SERVICE
    ///////////////////////////////////////////////////////////////////////////

    if ($service != "") {

        if ($csvtype == "combined") {
            write_csv_header("service");
        }

        // get service availability
        $args = array(
            "host" => $host,
            "service" => $service,
            "serviceonly" => true,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running") . ".\n";
        } else {

            // service table
            if ($servicedata) {
                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    write_service_csv_data($hn, $sd, $ok, $wa, $un, $cr);
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOST
    ///////////////////////////////////////////////////////////////////////////
    else if ($host != "") {

        $args = array(
            "host" => $host,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );

        if ($csvtype == 'host') {
            $args['hostonly'] = true;
        } else if ($csvtype == 'service') {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running") . ".\n";
        } else {

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {

                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            $host_up = 0;
            $host_down = 0;
            $host_unreachable = 0;

            // Host table
            if ($hostdata) {
                // Create header for combined CSV
                if ($csvtype == "combined")
                    write_csv_header("host");

                foreach ($hostdata as $h) {

                    $host_name = $h['host_name'];

                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    write_host_csv_data($host_name, $host_up, $host_down, $host_unreachable);
                }
            }

            // Service table
            if ($servicedata) {
                // Create header for combined CSV
                if ($csvtype == "combined")
                    write_csv_header("service");

                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    write_service_csv_data($hn, $sd, $ok, $wa, $un, $cr);
                }

                // Averages
                write_service_csv_data("", "AVERAGE", $avg_service_ok, $avg_service_warning, $avg_service_unknown, $avg_service_critical);
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // SPECIFIC HOSTGROUP OR SERVICEGROUP
    ///////////////////////////////////////////////////////////////////////////
    else if ($hostgroup != "" || $servicegroup != "") {

        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );
            
        if ($hostgroup != "") {
            $args["hostgroup"] = $hostgroup;
        } else {
            $args["servicegroup"] = $servicegroup;
        }

        if ($csvtype == 'host') {
            $args['hostonly'] = true;
        } else if ($csvtype == 'service') {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running") . ".\n";
        } else {

            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }


            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            // Host table
            if ($hostdata) {
                foreach ($hostdata as $h) {

                    $hn = $h['host_name'];

                    if (!$dont_count_downtime) {
                        $up = $h['percent_known_time_up'];
                        $dn = $h['percent_known_time_down'];
                        $un = $h['percent_known_time_unreachable'];
                    } else {
                        $up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled'];
                        $dn = $h['percent_known_time_down_unscheduled'];
                        $un = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $up = (($up += $h['percent_known_time_unreachable_unscheduled']) > 100 ? 100 : $up);
                        $un = 0.00;
                    }

                    write_host_csv_data($hn, $up, $dn, $un);
                }

                // Averages
                write_host_csv_data("AVERAGE", $avg_host_up, $avg_host_down, $avg_host_unreachable);
            }

            // Service table
            if ($servicedata) {
                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    write_service_csv_data($hn, $sd, $ok, $wa, $un, $cr);
                }

                // Averages
                write_service_csv_data("", "AVERAGE", $avg_service_ok, $avg_service_warning, $avg_service_unknown, $avg_service_critical);
            }
        }

    }

    ///////////////////////////////////////////////////////////////////////////
    // OVERVIEW (ALL HOSTS AND SERVICES)
    ///////////////////////////////////////////////////////////////////////////
    else {

        $args = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "timeperiod" => $timeperiod,
            "assume_initial_states" => $assumeinitialstates,
            "assume_state_retention" => $assumestateretention,
            "assume_states_during_not_running" => $assumestatesduringdowntime,
            "include_soft_states" => $includesoftstates,
            "initial_assumed_host_state" => $assumedhoststate,
            "initial_assumed_service_state" => $assumedservicestate
        );

        if ($csvtype == 'host') {
            $args['hostonly'] = true;
        } else if ($csvtype == 'service') {
            $args['serviceonly'] = true;
        }

        $have_data = get_availability_data($args, $hostdata, $servicedata);

        if (!$have_data) {
            echo _("Availability data is not available when monitoring engine is not running") . ".\n";
        } else {

            $avg_host_up = 0;
            $avg_host_down = 0;
            $avg_host_unreachable = 0;
            $count_host_up = 0;
            $count_host_down = 0;
            $count_host_unreachable = 0;

            if ($hostdata) {
                foreach ($hostdata as $h) {
                    if (!$dont_count_downtime) {
                        $host_up = $h['percent_known_time_up'];
                        $host_down = $h['percent_known_time_down'];
                        $host_unreachable = $h['percent_known_time_unreachable'];
                    } else {
                        $host_up = (($host_up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled']) > 100 ? 100 : $host_up);
                        $host_down = $h['percent_known_time_down_unscheduled'];
                        $host_unreachable = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $host_up = (($host_up += $h['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $host_up);
                        $host_unreachable = 0.00;
                    }

                    update_avail_avg($avg_host_up, $host_up, $count_host_up);
                    update_avail_avg($avg_host_down, $host_down, $count_host_down);
                    update_avail_avg($avg_host_unreachable, $host_unreachable, $count_host_unreachable);
                }
            }

            $avg_service_ok = 0;
            $avg_service_warning = 0;
            $avg_service_unknown = 0;
            $avg_service_critical = 0;
            $count_service_critical = 0;
            $count_service_warning = 0;
            $count_service_unknown = 0;

            if ($servicedata) {
                foreach ($servicedata as $s) {
                    if (!$dont_count_downtime) {
                        $service_ok = $s['percent_known_time_ok'];
                        $service_warning = $s['percent_known_time_warning'];
                        $service_unknown = $s['percent_known_time_unknown'];
                        $service_critical = $s['percent_known_time_critical'];
                    } else {
                        $service_ok = (($service_ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = $s['percent_known_time_warning_unscheduled'];
                        $service_unknown = $s['percent_known_time_unknown_unscheduled'];
                        $service_critical = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $service_ok = (($service_ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_warning = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $service_ok = (($service_ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $service_ok);
                        $service_unknown = 0.00;
                    }

                    update_avail_avg($avg_service_ok, $service_ok, $count_service_ok);
                    update_avail_avg($avg_service_warning, $service_warning, $count_service_warning);
                    update_avail_avg($avg_service_unknown, $service_unknown, $count_service_unknown);
                    update_avail_avg($avg_service_critical, $service_critical, $count_service_critical);
                }
            }

            // Host table
            if ($hostdata) {

                // Write the host header if combined report
                if ($csvtype == "combined") {
                    write_csv_header("host");
                }

                foreach ($hostdata as $h) {

                    $hn = $h['host_name'];

                    if (!$dont_count_downtime) {
                        $up = $h['percent_known_time_up'];
                        $dn = $h['percent_known_time_down'];
                        $un = $h['percent_known_time_unreachable'];
                    } else {
                        $up = $h['percent_known_time_up'] + $h['percent_known_time_down_scheduled'] + $h['percent_known_time_unreachable_scheduled'];
                        $dn = $h['percent_known_time_down_unscheduled'];
                        $un = $h['percent_known_time_unreachable_unscheduled'];
                    }

                    if ($dont_count_unknown) {
                        $up = (($up += $h['percent_known_time_unreachable_unscheduled']) > 100 ? 100 : $up);
                        $un = 0.00;
                    }

                    write_host_csv_data($hn, $up, $dn, $un);
                }

                // Host averages
                write_host_csv_data("AVERAGE", $avg_host_up, $avg_host_down, $avg_host_unreachable);
                   
                // If we are running a combined report, add some spacing for readability 
                if ($csvtype == "combined") {
                    echo "\n\n";
                }
            }

            // Service table
            if ($servicedata) {

                // Write the service header if combined report
                if ($csvtype == "combined") {
                    write_csv_header("service");
                }

                foreach ($servicedata as $s) {

                    $hn = $s['host_name'];
                    $sd = $s['service_description'];

                    if (!$dont_count_downtime) {
                        $ok = $s['percent_known_time_ok'];
                        $wa = $s['percent_known_time_warning'];
                        $un = $s['percent_known_time_unknown'];
                        $cr = $s['percent_known_time_critical'];
                    } else {
                        $ok = (($ok = $s['percent_known_time_ok'] + $s['percent_known_time_warning_scheduled'] + $s['percent_known_time_critical_scheduled'] + $s['percent_known_time_unknown_scheduled']) > 100 ? 100 : $ok);
                        $wa = $s['percent_known_time_warning_unscheduled'];
                        $un = $s['percent_known_time_unknown_unscheduled'];
                        $cr = $s['percent_known_time_critical_unscheduled'];
                    }

                    if ($dont_count_warning) {
                        $ok = (($ok += $s['percent_known_time_warning_unscheduled']) > 100 ? 100 : $ok);
                        $wa = 0.00;
                    }

                    if ($dont_count_unknown) {
                        $ok = (($ok += $s['percent_known_time_unknown_unscheduled']) > 100 ? 100 : $ok);
                        $un = 0.00;
                    }

                    // Service data
                    write_service_csv_data($hn, $sd, $ok, $wa, $un, $cr);
                }

                // Service averages
                write_service_csv_data("", "AVERAGE", $avg_service_ok, $avg_service_warning, $avg_service_unknown, $avg_service_critical);
            }
        }
    }
}


/**
 * @param $csvtype
 */
function write_csv_header($csvtype)
{
    if ($csvtype == "service")
        echo "host,service,ok %,warning %,unknown %,critical %\n";
    else if ($csvtype == "host" || $csvtype = "combined")
        echo "host,up %,down %,unreachable %\n";
}

/**
 * @param $hn
 * @param $up
 * @param $dn
 * @param $un
 */
function write_host_csv_data($hn, $up, $dn, $un)
{
    echo "\"$hn\",$up,$dn,$un\n";
}

/**
 * @param $hn
 * @param $sn
 * @param $ok
 * @param $wa
 * @param $un
 * @param $cr
 */
function write_service_csv_data($hn, $sn, $ok, $wa, $un, $cr)
{
    echo "\"$hn\",\"$sn\",$ok,$wa,$un,$cr\n";
}