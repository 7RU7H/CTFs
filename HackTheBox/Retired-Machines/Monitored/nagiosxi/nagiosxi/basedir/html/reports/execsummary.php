<?php
//
// Executive Summary Report
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');


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
    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "pdf":
            export_report('execsummary', EXPORT_PDF, EXPORT_LANDSCAPE);
            break;
        case "jpg":
            export_report('execsummary', EXPORT_JPG);
            break;
        case "getservices":
            $host = grab_request_var("host", "");
            $args = array('brevity' => 1, 'host_name' => $host, 'orderby' => 'service_description:a');
            $oxml = get_xml_service_objects($args);
            echo '<option value="">['._("All Services").']</option>';
            if ($oxml) {
                foreach ($oxml->service as $serviceobj) {
                    $name = strval($serviceobj->service_description);
                    echo "<option value='" . $name . "' " . is_selected($service, $name) . ">$name</option>\n";
                }
            }
            break;
        case "getreport":
            get_execsummary_report();
            break;
        default:
            display_report();
            break;
    }
}


///////////////////////////////////////////////////////////////////
// REPORT GENERATION FUCNTIONS
///////////////////////////////////////////////////////////////////

// this function displays report in HTML
function display_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $export = grab_request_var('export', 0);
    $manual_run = grab_request_var("manual_run", 0);
    $advanced = intval(grab_request_var("advanced", 0));
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));
    $timeperiod = grab_request_var('timeperiod', '');

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

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");

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

    do_page_start(array("page_title" => _("Executive Summary")), true);
?>

<script type="text/javascript">
$(document).ready(function () {

    showhidedates();

    if (!<?php echo $disable_report_auto_run; ?>) {
        run_execsummary_ajax();
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
        run_execsummary_ajax();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>reports/execsummary.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

});

var report_sym = 0;
function run_execsummary_ajax() {
    report_sym = 1;
    setTimeout('show_loading_report()', 500);

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'execsummary.php?'+formvalues;

    $.get(url, {}, function(data) {
        report_sym = 0;
        hide_throbber();
        $('#report').html(data);
    });
}
</script>

<script type="text/javascript" src="<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>"></script>

<form method="get" data-type="execsummary">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <?php echo get_add_myreport_html(_("Executive Summary"), $_SERVER['PHP_SELF'], array()); ?>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <?php echo _('Download'); ?> <i class="fa fa-caret-down r"></i>
                </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
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

            <button type="button" class="btn btn-sm btn-primary" id="run" name="reporttimesubmitbutton"><?php echo _("Run"); ?></button>

        </div>

        <div id="advanced-options" style="<?php if (!$advanced) { echo 'display: none;'; } ?>">

            <div class="floatbox-sm">
                <?php echo _("Assume Initial States"); ?>:
                <select name="assumeinitialstates" class="form-control condensed">
                    <option value="yes" <?php if ($assumeinitialstates == "yes") {
                        echo "selected";
                    } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumeinitialstates == "no") {
                        echo "selected";
                    } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Assume State Retention"); ?>:
                <select name="assumestateretention" class="form-control condensed">
                    <option value="yes" <?php if ($assumestateretention == "yes") {
                        echo "selected";
                    } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumestateretention == "no") {
                        echo "selected";
                    } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Assume States During Program Downtime"); ?>:
                <select name="assumestatesduringdowntime" class="form-control condensed">
                    <option value="yes" <?php if ($assumestatesduringdowntime == "yes") {
                        echo "selected";
                    } ?>><?php echo _("Yes"); ?></option>
                    <option value="no" <?php if ($assumestatesduringdowntime == "no") {
                        echo "selected";
                    } ?>><?php echo _("No"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Include Soft States"); ?>:
                <select name="includesoftstates" class="form-control condensed">
                    <option value="no" <?php if ($includesoftstates == "no") {
                        echo "selected";
                    } ?>><?php echo _("No"); ?></option>
                    <option value="yes" <?php if ($includesoftstates == "yes") {
                        echo "selected";
                    } ?>><?php echo _("Yes"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("First Assumed Host State"); ?>:
                <select name="assumedhoststate" class="form-control condensed">
                    <option value="0" <?php if ($assumedhoststate == 0) {
                        echo "selected";
                    } ?>><?php echo _("Unspecified"); ?></option>
                    <option value="-1" <?php if ($assumedhoststate == -1) {
                        echo "selected";
                    } ?>><?php echo _("Current State"); ?></option>
                    <option value="3" <?php if ($assumedhoststate == 3) {
                        echo "selected";
                    } ?>><?php echo _("Host Up"); ?></option>
                    <option value="4" <?php if ($assumedhoststate == 4) {
                        echo "selected";
                    } ?>><?php echo _("Host Down"); ?></option>
                    <option value="5" <?php if ($assumedhoststate == 5) {
                        echo "selected";
                    } ?>><?php echo _("Host Unreachable"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("First Assumed Service State"); ?>:
                <select name="assumedservicestate" class="form-control condensed">
                    <option value="0" <?php if ($assumedservicestate == 0) {
                        echo "selected";
                    } ?>><?php echo _("Unspecified"); ?></option>
                    <option value="-1" <?php if ($assumedservicestate == -1) {
                        echo "selected";
                    } ?>><?php echo _("Current State"); ?></option>
                    <option value="6" <?php if ($assumedservicestate == 6) {
                        echo "selected";
                    } ?>><?php echo _("Service Ok"); ?></option>
                    <option value="7" <?php if ($assumedservicestate == 7) {
                        echo "selected";
                    } ?>><?php echo _("Service Warning"); ?></option>
                    <option value="8" <?php if ($assumedservicestate == 8) {
                        echo "selected";
                    } ?>><?php echo _("Service Unknown"); ?></option>
                    <option value="9" <?php if ($assumedservicestate == 9) {
                        echo "selected";
                    } ?>><?php echo _("Service Critical"); ?></option>
                </select>
            </div>
            <div class="floatbox-sm">
                <?php echo _("Report Time Period"); ?>:
                <select name="timeperiod" class="form-control condensed">
                    <option value="" <?php if (empty($timeperiod)) {
                        echo "selected";
                    } ?>><?php echo _("None"); ?></option>
                    <?php
                    // Get a list of timeperiods
                    $obj_request = array("objecttype_id" => 9);
                    $objects = new SimpleXMLElement(get_objects_xml_output($obj_request, false));
                    foreach ($objects as $object) {
                        $tp = (string)$object->name1;
                        if (!empty($tp)) {
                            echo "<option " . is_selected($timeperiod, $tp) . ">" . $tp . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('This will count any state during scheduled downtime as OK for the Executive Summary report'); ?>">
                        <input type="checkbox" name="dont_count_downtime" <?php echo is_checked($dont_count_downtime, 1); ?>> <?php echo _("Hide scheduled downtime"); ?>
                    </label>
                </div>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('This will count any WARNING state as OK for the Executive Summary report'); ?>">
                        <input type="checkbox" name="dont_count_warning" <?php echo is_checked($dont_count_warning, 1); ?>> <?php echo _("Hide WARNING states"); ?>
                    </label>
                </div>
            </div>
            
            <div class="floatbox-sm">
                <div class="checkbox">
                    <label title="<?php echo _('This will count any UNKNOWN state as OK for the Executive Summary report'); ?>">
                        <input type="checkbox" name="dont_count_unknown" <?php echo is_checked($dont_count_unknown, 1); ?>> <?php echo _("Hide UNKNOWN/UNREACHABLE states"); ?>
                    </label>
                </div>
            </div>

            <div class="clear"></div>

        </div>

    </div>
</form>

<div id="report"></div>

<?php 
}

function get_execsummary_report()
{
    global $request;

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Get values passed in GET/POST request
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $assumeinitialstates = grab_request_var("assumeinitialstates", "yes");
    $assumestateretention = grab_request_var("assumestateretention", "yes");
    $assumestatesduringdowntime = grab_request_var("assumestatesduringdowntime", "yes");
    $includesoftstates = grab_request_var("includesoftstates", "no");
    $assumedhoststate = grab_request_var("assumedhoststate", 3);
    $assumedservicestate = grab_request_var("assumedservicestate", 6);
    $export = grab_request_var('export', 0);
    $manual_run = grab_request_var("manual_run", 0);
    $advanced = intval(grab_request_var("advanced", 0));
    $dont_count_downtime = checkbox_binary(grab_request_var("dont_count_downtime", 0));
    $dont_count_warning = checkbox_binary(grab_request_var("dont_count_warning", 0));
    $dont_count_unknown = checkbox_binary(grab_request_var("dont_count_unknown", 0));

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

    // Determine start/end times based on period
    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

    $title = _("Executive Summary");
    $sub_title = "";
    if ($service != "") {
        $title = _("Service Executive Summary");
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
        $title = _("Host Executive Summary");
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
        $title = _("Hostgroup Executive Summary");
        $sub_title = "
            <div class='hoststatusdetailheader'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($hostgroup) . get_hostgroup_alias($hostgroup) . "</div>
                </div>
                <div class='clear'></div>
            </div>";

    } else if ($servicegroup != "") {
        $title = _("Servicegroup Executive Summary");
        $sub_title = "
            <div class='hoststatusdetailheader'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($servicegroup) . get_servicegroup_alias($servicegroup) . "</div>
                </div>
                <div class='clear'></div>
            </div>";
    }

    $sub_title .= "
        <div class='report-covers' style='padding: 0;'>
            " . _("Report covers from") . ": <b>" . get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</b>
            " . _("to") . " <b>" . get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</b>
        </div>";

    if ($export) {
        do_page_start(array("page_title" => $title), true);
    }

    // Options can be suppressed for rendered reports
    if ($export) {

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
                <?php echo $sub_title; ?>
            </div>
            <div class="clear"></div>
        </div>

    <?php
    } else {
        ?>
        <h1 style="margin-bottom: 10px;"><?php echo $title; ?></h1>
        <?php 
        echo $sub_title;
    }
    ?>

    <script type='text/javascript'>
        // AJAX load the different report from around the site
        $(document).ready(function () {
            <?php
            $arg = '';
            foreach ($request as $var => $val) {
                if ($var == "mode") { continue; }
                $arg .= "&".urlencode($var)."=".urlencode($val);
            }
            ?>
            $('#inner_availability').load('availability.php?mode=getreport&showonlygraphs=1<?php echo $arg;?>');
            $('#inner_top_alert_producers').load('topalertproducers.php?mode=getpage&records=10<?php echo $arg; ?>');

            // Load dashlet content into div
            $('#inner_latest_alerts').each(function () {
                var optsarr = {
                    "func": "get_latestalerts_dashlet_html",
                    "args": {
                        "type": "",
                        "host": "<?php echo $host;?>",
                        "service": "<?php echo $service;?>",
                        "hostgroup": "<?php echo $hostgroup;?>",
                        "servicegroup": "<?php echo $servicegroup;?>",
                        "maxitems": "10",
                        "export": "<?php echo intval($export); ?>"
                    }
                }
                var opts = JSON.stringify(optsarr);
                get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
            });

        });
    </script>

    <!-- remove inline styles -->
    <div id='allreports' style='margin:10px auto;'>

        <div class='availability' style='float:left; margin-right: 20px;'>
            <h4><?php echo _("Availability"); ?></h4>

            <div id='inner_availability'>
                <div class="childcontentthrobber" id='availabilitythrobber'>
                    <div class="sk-spinner sk-spinner-pulse"></div>
                </div>
            </div>
        </div>
        <div class='top_alert_producers' style='float:left; width: 450px;'>
            <h4><?php echo _("Top Alert Producers"); ?></h4>

            <div id='inner_top_alert_producers'>
                <div class="childcontentthrobber" id='topalertsthrobber'>
                    <div class="sk-spinner sk-spinner-pulse"></div>
                </div>
            </div>
        </div>

        <br clear="all"/>
        <!-- remove inline styles -->
        <div class='alert_histogram' style="margin-bottom: 20px;">
            <h4><?php echo _("Alert Histogram"); ?></h4>

            <div id='inner_alert_histogram'>
                <?php
                $url = 'histogram.php?mode=image';
                foreach ($request as $var => $val) {
                    if ($var == "mode") { continue; }
                    $url .= "&" . urlencode($var) . "=" . urlencode($val);
                }
                ?>
                <br/>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#hostList').change(function () {
                            $('#hostgroupList').val('');
                            $('#servicegroupList').val('');
                        });
                        $('#servicegroupList').change(function () {
                            $('#hostList').val('');
                            $('#hostgroupList').val('');
                        });
                        $('#hostgroupList').change(function () {
                            $('#servicegroupList').val('');
                            $('#hostList').val('');
                        });

                        <?php if($export): ?>
                        var tracking = false;
                        <?php else: ?>
                        var tracking = true;
                        <?php endif; ?>

                        var api_url = '<?php echo $url; ?>';
                        $.getJSON(api_url,
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
                                            'name': data.name,
                                            'color': '#7CB5EC'
                                        }
                                    ]
                                }
                                chart = new Highcharts.Chart(options);
                            });
                    });
                </script>
                <div id='alert_histogram_image'></div>
            </div>
        </div>

        <!-- style="float: left; margin: 10px; padding: 10px; border: 1px solid gray;" -->


        <div class='most_recent_alerts fl'>
            <div id='inner_latest_alerts'>
                <div class="childcontentthrobber" id='latestalertsthrobber'>
                    <div class="sk-spinner sk-spinner-pulse"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>

    </div> <!-- end allreports div -->
    <?php

    // closes the HTML page
    do_page_end(true);
}