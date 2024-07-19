<?php
//
// Alert Histogram Report
// Copyright (c) 2010-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');


// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);


route_request();


function route_request()
{
    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "image":
            get_histogram_image();
            break;
        case "csv":
            get_histogram_csv();
            break;
         case "pdf":
            export_report('histogram', EXPORT_PDF, EXPORT_LANDSCAPE);
            break;
        case "jpg":
            export_report('histogram', EXPORT_JPG);
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
                    echo "<option value='" . $name . "' " . is_selected($serviceobj, $name) . ">$name</option>\n";
                }
            }
            break;
        case "getreport":
            get_histogram_report();
            break;
        default:
            display_histogram();
            break;
    }
}

///////////////////////////////////////////////////////////////////
// BACKEND DATA FUNCTIONS
///////////////////////////////////////////////////////////////////

/**
 * Gets state history data in XML format from the backend.
 *
 * @param $args
 *
 * @return SimpleXMLElement
 */
function get_histogram_data($args)
{
    $xml = get_xml_histogram($args);
    return $xml;
}

///////////////////////////////////////////////////////////////////
// REPORT GENERATION FUCNTIONS
///////////////////////////////////////////////////////////////////

// Displays the data by generating the HTML page
function display_histogram()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $groupby = grab_request_var("groupby", "hour_of_day");
    $statetype = grab_request_var("statetype", "both");
    $export = grab_request_var("export", 0);
    $manual_run = grab_request_var("manual_run", 0);

    $disable_report_auto_run = get_option("disable_report_auto_run", 0);
   
    // Fix search
    if ($search == _("Search...")) {
        $search = "";
    }

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

    // Special "all" stuff
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    // Can do hostgroup OR servicegroup OR host
    if ($hostgroup != "") {
        $servicegroup = "";
        $host = "";
    } else if ($servicegroup != "") {
        $host = "";
    }

    // Limit hosts by hostgroup or host
    $host_ids = array();

    // Limit by hostgroup
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
    }

    // Limit service by servicegroup
    $service_ids = array();
    if ($servicegroup != "") {
        $service_ids = get_servicegroup_member_ids($servicegroup);
    }

    $object_ids_str = "";
    $y = 0;
    foreach ($host_ids as $hid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $hid;
        $y++;
    }
    foreach ($service_ids as $sid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $sid;
        $y++;
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "groupby" => $groupby,
    );

    // Object id limiters
    if ($object_ids_str != "") {
        $args["object_id"] = "in:" . $object_ids_str;
    } else {
        if ($host != "") {
            $args["host_name"] = $host;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
        }
        if ($service != "") {
            $args["service_description"] = $service;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
        }
    }

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

    do_page_start(array("page_title" => _("Alert Histogram")), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    showhidedates();

    if (!<?php echo $disable_report_auto_run; ?>) {
        run_histogram_ajax();
    }

    $('#hostList').searchable({maxMultiMatch: 9999});
    $('#serviceList').searchable({maxMultiMatch: 9999});
    $('#hostgroupList').searchable({maxMultiMatch: 9999});
    $('#servicegroupList').searchable({maxMultiMatch: 9999});
    if ($('#serviceList').is(':visible')) {
        $('.serviceList-sbox').show();
    } else {
        $('.serviceList-sbox').hide();
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
    });

    $('#servicegroupList').change(function () {
        $('#hostList').val('');
        $('#hostgroupList').val('');
        $('#serviceList').val('').hide();
        $('.serviceList-sbox').hide();
    });

    $('#hostgroupList').change(function () {
        $('#servicegroupList').val('');
        $('#hostList').val('');
        $('#serviceList').val('').hide();
        $('.serviceList-sbox').hide();
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

    // Actually return the report
    $('#run').click(function() {
        run_histogram_ajax();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>reports/histogram.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

});

function run_histogram_ajax() {
    $('#report').html('');
    show_throbber();

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'histogram.php?'+formvalues;

    $.get(url, {}, function(data) {
        hide_throbber();
        $('#report').html(data);
    });
}


function update_service_list() {
    var host = $('#hostList').val();
    $.get('histogram.php?mode=getservices&host='+host, function(data) {
        $('#serviceList').html(data);
    });
}

function showhidedates() {
    if ($('#reportperiodDropdown').val() == 'custom') {
        $('#customdates').show();
    } else {
        $('#customdates').hide();
    }
}
</script>

<form method="get" data-type="histogram">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <?php echo get_add_myreport_html(_("Alert Histogram"), $_SERVER['PHP_SELF'], array()); ?>
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

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _("Group By"); ?></label>
                <select id="groupbyDropdown" class="form-control" name="groupby">
                    <option value="hour_of_day" <?php echo is_selected("hour_of_day", $groupby); ?>><?php echo _("Hour of Day"); ?></option>
                    <option value="day_of_week" <?php echo is_selected("day_of_week", $groupby); ?>><?php echo _("Day of Week"); ?></option>
                    <option value="day_of_month" <?php echo is_selected("day_of_month", $groupby); ?>><?php echo _("Day of Month"); ?></option>
                    <option value="month" <?php echo is_selected("month", $groupby); ?>><?php echo _("Month"); ?></option>
                </select>
            </div>

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _("States"); ?></label>
                <select id="statetypeDropdown" class="form-control" name="statetype">
                    <option value="soft" <?php echo is_selected("soft", $statetype); ?>><?php echo _("Soft"); ?></option>
                    <option value="hard" <?php echo is_selected("hard", $statetype); ?>><?php echo _("Hard"); ?></option>
                    <option value="both" <?php echo is_selected("both", $statetype); ?>><?php echo _("Both"); ?></option>
                </select>
            </div>

            <button type="button" id="run" class="btn btn-sm btn-primary" name="reporttimesubmitbutton"><?php echo _("Run"); ?></button>

        </div>

    </div>
</form>

<div id="report"></div>

<?php
}

function get_histogram_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $groupby = grab_request_var("groupby", "hour_of_day");
    $statetype = grab_request_var("statetype", "both");
    $export = grab_request_var("export", 0);
    $manual_run = grab_request_var("manual_run", 0);
   
    // Fix search
    if ($search == _("Search...")) {
        $search = "";
    }

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

    // Special "all" stuff
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    // Can do hostgroup OR servicegroup OR host
    if ($hostgroup != "") {
        $servicegroup = "";
        $host = "";
    } else if ($servicegroup != "") {
        $host = "";
    }

    // Limit hosts by hostgroup or host
    $host_ids = array();

    // Limit by hostgroup
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
    }

    // Limit service by servicegroup
    $service_ids = array();
    if ($servicegroup != "") {
        $service_ids = get_servicegroup_member_ids($servicegroup);
    }

    $object_ids_str = "";
    $y = 0;
    foreach ($host_ids as $hid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $hid;
        $y++;
    }
    foreach ($service_ids as $sid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $sid;
        $y++;
    }

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "groupby" => $groupby,
    );

    // Object id limiters
    if ($object_ids_str != "") {
        $args["object_id"] = "in:" . $object_ids_str;
    } else {
        if ($host != "") {
            $args["host_name"] = $host;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
        }
        if ($service != "") {
            $args["service_description"] = $service;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
        }
    }

    $title = _("Top Alert Histogram");
    $sub_title = "";
    if ($service != "") {
        $title = _("Service Alert Histogram");
        $sub_title = "
        <div class='servicestatusdetailheader'>
            <div class='serviceimage'>
                " . get_object_icon($host, $service, true) . "
            </div>
            <div class='servicetitle'>
                <div class='servicename'>
                    <a href='" . get_service_status_detail_link($host, $service) . "'>" . encode_form_val($service) . "</a>" . get_service_alias($host, $service) . "
                </div>
                <div class='hostname'>
                    <a href='" . get_host_status_detail_link($host) . "'>" . encode_form_val($host) . "</a>" . get_host_alias($host) . "
                </div>
            </div>
            <div class='clear'></div>
        </div>";

    } else if ($host != "") {
        $title = _("Host Alert Histogram");
        $sub_title = "
            <div class='hoststatusdetailheader'>
                <div class='hostimage'>
                    " . get_object_icon($host, "", true) . "
                </div>
                <div class='hosttitle'>
                    <div class='hostname'>
                        <a href='" . get_host_status_detail_link($host) . "'>" . encode_form_val($host) . "</a>" . get_host_alias($host) . "
                    </div>
                </div>
                <div class='clear'></div>
            </div>";
    } else if ($hostgroup != "") {
        $title = _("Hostgroup Alert Histogram");
        $sub_title = "
            <div class='hoststatusdetailheader'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($hostgroup) . get_hostgroup_alias($hostgroup) . "</div>
                </div>
            </div>";

    } else if ($servicegroup != "") {
        $title = _("Servicegroup Alert Histogram");
        $sub_title = "
            <div class='hoststatusdetailheader'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($servicegroup) . get_servicegroup_alias($servicegroup) . "</div>
                </div>
            </div>";
    }
    $report_covers_from = "
                <div>" . _("Report covers from") . ":
                    <strong>" . get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</strong> " . _("to") . "
                    <strong>" . get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</strong>
                </div>";


    // Show only the options required if not rendering
    if ($export) {

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
                <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
            </div>
            <div style="float: left; height: 44px;">
                <div style="font-weight: bold; font-size: 22px; padding-bottom: 4px;"><?php echo $title; ?></div>
                <?php echo $report_covers_from; ?>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php echo $sub_title;
    } else {
        ?>
        <h1 style="margin-bottom: 10px;"><?php echo $title; ?></h1>
        <?php echo $sub_title; ?>
        <?php echo $report_covers_from; ?>
    <?php
    }

    $url = "histogram.php?mode=image";
    foreach ($request as $var => $val) {
        if ($var == "mode" || $var == "showheader") { continue; }
        $url .= "&" . urlencode($var) . "=" . urlencode($val);
    }
    ?>
    <br/>
    <script type="text/javascript">
        $(document).ready(function () {

            <?php if($export): ?>
            var tracking = false;
            <?php else: ?>
            var tracking = true;
            <?php endif; ?>

            $.getJSON('<?php echo $url; ?>',
                function (data) {
                    var chart;
                    var options = {
                        'plotOptions': {'series': {'enableMouseTracking': tracking, 'animation': false}},
                        'chart': {
                            'renderTo': 'alert_histogram_image',
                            'width': 600,
                            'height': 225
                        },
                        'credits': {'enabled': false},
                        'title': {'text': data.graph_title},
                        'xAxis': {
                            'categories': data.categories,
                            'title': {'text': data.x_label},
                            'labels': {
                                'rotation': -45,
                                'align': 'right'
                            }
                        },
                        'yAxis': {
                            'title': {'text': data.y_label},
                            'min': 0
                        },
                        'series': [
                            {
                                'data': data.data,
                                'name': data.name
                            }
                        ]
                    }
                    chart = new Highcharts.Chart(options);
                });
        });
    </script>

    <div id='alert_histogram_image'></div>

    <?php
    do_page_end(true);
}


// this function gets the XML records of state history data for multiple
// output formats (CSV, PDF, HTML)
/**
 * @return SimpleXMLElement
 */
function get_histogram_xml()
{

    // makes sure user has appropriate license level
    licensed_feature_check();

    // get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $groupby = grab_request_var("groupby", "hour_of_day");
    $statetype = grab_request_var("statetype", "both");

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

    // special "all" stuff
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    // can do hostgroup OR servicegroup OR host
    if ($hostgroup != "") {
        $servicegroup = "";
        $host = "";
    } else if ($servicegroup != "") {
        $host = "";
    }

    //  limit hosts by hostgroup or host
    $host_ids = array();
    //  limit by hostgroup
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup, true);
    }
    //  limit by host
    //else if($host!=""){
    /// $host_ids[]=get_host_id($host);
    //  }
    //  limit service by servicegroup
    $service_ids = array();
    if ($servicegroup != "") {
        $service_ids = get_servicegroup_member_ids($servicegroup);
    }

    $object_ids_str = "";
    $y = 0;
    foreach ($host_ids as $hid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $hid;
        $y++;
    }
    foreach ($service_ids as $sid) {
        if ($y > 0)
            $object_ids_str .= ",";
        $object_ids_str .= $sid;
        $y++;
    }


    // determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    // get XML data from backend - the most basic example
    // this will return all records (no paging), so it can be used for CSV export
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "groupby" => $groupby,
    );
    switch ($statetype) {
        case "soft":
            $args["state_type"] = 0;
            break;
        case "hard":
            $args["state_type"] = 1;
            break;
        default:
            break;
    }
    // object id limiters
    if ($object_ids_str != "")
        $args["object_id"] = "in:" . $object_ids_str;
    else {
        if (!empty($host)) {
            $args["host_name"] = strval($host);
            if ($service == '*') {
                $args["objecttype_id"] = OBJECTTYPE_HOST;
            }
        }
        if (!empty($service) && $service != '*') {
            $args["service_description"] = $service;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
        }
    }
    if ($search) {
        $args["output"] = "lk:" . $search;
    }

    //file_put_contents("/tmp/xidebug.log", print_r($args, true), FILE_APPEND);
    $xml = get_histogram_data($args);
    return $xml;
}

/**
 * @param $xml
 * @param $xarr
 * @param $yarr
 * @param $xtitle
 */
function process_histogram_xml($xml, &$xarr, &$yarr, &$xtitle)
{
    $groupby = grab_request_var("groupby", "hour_of_day");

    // Setup titles and X-axis labels
    $xtitle = "";
    switch ($groupby) {
        case "month":
            $xtitle = _("Month");
            $buckets = 12;
            break;
        case "day_of_month":
            $xtitle = _("Day of the Month");
            $buckets = 31;
            break;
        case "day_of_week":
            $xtitle = _("Day of the Week");
            $buckets = 7;
            break;
        case "hour_of_day":
            $xtitle = _("Hour of the Day");
            $buckets = 24;
            break;
        default:
            break;
    }

    // initialize arrays
    for ($x = 0; $x < $buckets; $x++) {

        $yarr[] = 0;

        switch ($groupby) {
            case "month":
                // We need to iterate all the way through for yarr, so if its
                // day_of_week, just initialize it, then skip the rest of
                // the iteration.
                if (empty($xarr) === TRUE) {
                    $xarr = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                }
                break;
            case "day_of_month":
                $xarr[] = ($x);
                break;
            case "day_of_week":
                // We need to iterate all the way through for yarr, so if its
                // day_of_week, just initialize it, then skip the rest of
                // the iteration.
                if (empty($xarr) === TRUE) {
                    $xarr = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
                }
                break;
            case "hour_of_day":
                // We need to iterate all the way through for yarr, so if its
                // day_of_week, just initialize it, then skip the rest of
                // the iteration.
                if (empty($xarr) === TRUE) {
                    $xarr = array("12am", "1am", "2am", "3am", "4am", "5am", "6am", "7am", "8am", "9am", "10am", "11am", "Noon", "1pm", "2pm", "3pm", "4pm", "5pm", "6pm", "7pm", "8pm", "9pm", "10pm", "11pm");
                }
                break;
            default:
                break;
        }
    }
    // extra one for hour of day (jpgraph bug)
    //if($groupby=="hour_of_day")
    //  $xarr[24]=24;

    // insert real data
    if ($xml) {
        foreach ($xml->histogramelement as $he) {

            $total = intval($he->total);

            $month = intval($he->month);
            $day_of_month = intval($he->day_of_month);
            $day_of_week = intval($he->day_of_week);
            $hour_of_day = intval($he->hour_of_day);

            $index = 0;
            switch ($groupby) {
                case "month":
                    $index = $month - 1;
                    break;
                case "day_of_month":
                    $index = $day_of_month;
                    break;
                case "day_of_week":
                    $index = $day_of_week;
                    break;
                case "hour_of_day":
                    $index = $hour_of_day;
                    break;
                default:
                    break;
            }

            $yarr[$index] = $total;
        }
    }

    // last bucket is same as first (only for hour of day)
    //if($groupby=="hour_of_day")
//      $yarr[24]=$yarr[0];
    //$yarr[$buckets]=$yarr[0];
}

/**
 * @param $val
 *
 * @return mixed
 */
function histogram_xaxis_callback($val)
{

    $groupby = grab_request_var("groupby", "hour_of_day");
//~ print $val;
    switch ($groupby) {
        case "month":
            $month = $val % 12;
            $months = array(_("Jan"), _("Feb"), _("Mar"), _("Apr"), _("May"), _("Jun"), _("Jul"), _("Aug"), _("Sep"), _("Oct"), _("Nov"), _("Dec"));
            $out = $months[$month];
            break;
        case "day_of_month":
            $out = $val + 1;
            break;
        case "day_of_week":
            $day = $val % 7;
            $days = array(_("Sun"), _("Mon"), _("Tue"), _("Wed"), _("Thu"), _("Fri"), _("Sat"));
            $out = $days[$day];
            break;
        case "hour_of_day":
            $hour = $val % 24;
            $hours = array(_("12am"), _("1am"), _("2am"), _("3am"), _("4am"), _("5am"), _("6am"), _("7am"), _("8am"), _("9am"), _("10am"), _("11am"), _("Noon"), _("1pm"), _("2pm"), _("3pm"), _("4pm"), _("5pm"), _("6pm"), _("7pm"), _("8pm"), _("9pm"), _("10pm"), _("11pm"));
            $out = $hours[$hour];
            //$out=$val;
            break;
        default:
            $out = $val;
            break;
    }

    return $out;
}


// this function generates json for a histogram highchart
function get_histogram_image()
{

    $xml = get_histogram_xml();

    // build array
    $xdata = array();
    $ydata = array();
    process_histogram_xml($xml, $xdata, $ydata, $xtitle);

    // Turn the xy points into datasets for Highcharts
    $categories = $xdata;
    $data = $ydata;
    $name = _('Alerts');
    $gtitle = sprintf(_('Alerts by %s'), $xtitle);
    $ytitle = _('Alerts');

    $json = array('categories' => $categories,
        'data' => $data,
        'name' => $name,
        'graph_title' => $gtitle,
        'y_label' => $ytitle,
        'x_label' => $xtitle);

    header("Content-type: application/json");

    print json_encode($json);
    return;

}


// this function generates a CSV file of histogram data
function get_histogram_csv()
{
    $groupby = grab_request_var("groupby", "hour_of_day");


    $xml = get_histogram_xml();
    // build array
    $xdata = array();
    $ydata = array();
    process_histogram_xml($xml, $xdata, $ydata, $xtitle);

    // output header for csv
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . time() ."-histogram.csv\"");

    // column definitions
    echo $groupby . ",total_alerts\n";

    //print_r($xml);
    //exit();

    $x = 0;
    foreach ($xdata as $xd) {
        echo $xd . "," . $ydata[$x] . "\n";
        $x++;
    
    }
}