<?php
//
// State History Report
// Copyright (c) 2010-2020 Nagios Enterprises, LLC. All rights reserved.
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
        case "csv":
            get_statehistory_csv();
            break;
        case "pdf":
            export_report('statehistory', EXPORT_PDF, EXPORT_LANDSCAPE);
            break;
        case "jpg":
            export_report('statehistory', EXPORT_JPG);
            break;
        case "getservices":
            $host = grab_request_var("host", "");
            $args = array('brevity' => 1, 'host_name' => $host, 'orderby' => 'service_description:a');
            $oxml = get_xml_service_objects($args);
            echo '<option value="">['._("All Services").']</option>';
            echo '<option value="*">[' .  _("Host Only") . ']</option>';
            if ($oxml) {
                foreach ($oxml->service as $serviceobj) {
                    $name = strval($serviceobj->service_description);
                    echo "<option value='" . $name . "' " . is_selected($service, $name) . ">$name</option>\n";
                }
            }
            break;
        case 'getpage':
            get_statehistory_page();
            break;
        case "getreport":
            get_statehistory_report();
            break;
        default:
            display_statehistory();
            break;
    }
}


///////////////////////////////////////////////////////////////////
// BACKEND DATA FUNCTIONS
///////////////////////////////////////////////////////////////////


/**
 * Grabs the state history data from the XML backend
 *
 * @param   array   $args   Arguments to pass to the backend
 * @return  object          SimpleXMLObject with data
 */
function get_statehistory_data($args)
{
    $xml = get_xml_statehistory($args);
    return $xml;
}


///////////////////////////////////////////////////////////////////
// REPORT GENERATION FUCNTIONS
///////////////////////////////////////////////////////////////////

// this function displays event log data in HTML
function display_statehistory()
{
    global $request;

    // makes sure user has appropriate license level
    licensed_feature_check();

    // get values passed in GET/POST request
    $page = grab_request_var("page", 0);
    $records = grab_request_var("records", get_user_meta(0, 'report_defualt_recordlimit', 10));
    $reportperiod = grab_request_var("reportperiod", "last24hours");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $search = grab_request_var("search", "");
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $statetype = grab_request_var("statetype", "hard");
    $hostservice = grab_request_var("hostservice", "both");
    $state = grab_request_var("state", "");
    $export = grab_request_var("export", 0);

    // Do not do any processing unless we have default report running enabled
    $disable_report_auto_run = get_option("disable_report_auto_run", 0);

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

    $host_ids = array();
    $service_ids = array();

    //  limit hosts by hostgroup or host
    //  limit by hostgroup
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
        $service_ids = get_hostgroup_service_member_ids($hostgroup);
    }
    //  limit service by servicegroup
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

    // NOTES:
    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:
    /**/
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1,
    );

    if ($service != "" && $service != "*") {
        $args["service_description"] = $service;
    }

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
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }
    // object id limiters
    if ($object_ids_str != "")
        $args["object_id"] = "in:" . $object_ids_str;
    else {
        if ($host != "")
            $args["host_name"] = $host;
    }
    if ($search)
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    $xml = get_statehistory_data($args);
    //print_r($xml);
    $total_records = 0;

    if ($xml)
        $total_records = intval($xml->recordcount);

    // determine paging information
    $args = array(
        "reportperiod" => $reportperiod,
        "startdate" => $startdate,
        "enddate" => $enddate,
        "starttime" => $starttime,
        "endtime" => $endtime,
        "search" => $search,
        "host" => $host,
        "service" => $service,
        "hostgroup" => $hostgroup,
        "servicegroup" => $servicegroup,
        "statetype" => $statetype,
        "hostservice" => $hostservice,
        "state" => $state
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    /**/
    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    /**/
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record,
    );

    if ($service != "" && $service != "*") {
        $args["service_description"] = $service;
    }
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
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }

    switch ($state) {
        case 'ok':
            $args["state"] = STATE_OK;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'warning':
            $args["state"] = STATE_WARNING;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'critical':
            $args["state"] = STATE_CRITICAL;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'unknown':
            $args["state"] = STATE_UNKNOWN;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'up':
            $args["state"] = STATE_UP;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'down':
            $args["state"] = STATE_DOWN;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'unreachable':
            $args["state"] = STATE_UNREACHABLE;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
    }

    // object id limiters
    if ($object_ids_str != "")
        $args["object_id"] = "in:" . $object_ids_str;
    else {
        if ($host != "")
            $args["host_name"] = $host;
    }
    if ($search)
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    $xml = get_statehistory_data($args);

    // Determine title
    $title = _("State History");
    if ($service != "" && $service != "*")
        $title = _("Service State History");
    else if ($host != "")
        $title = _("Host State History");
    else if ($hostgroup != "")
        $title = _("Hostgroup State History");
    else if ($servicegroup != "")
        $title = _("Servicegroup State History");

    // Auto start and end dates for datetime
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

    do_page_start(array("page_title" => $title), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    // If we should run it right away
    if (!<?php echo $disable_report_auto_run; ?>) {
        run_statehistory_ajax();
    }

    showhidedates();

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

    // Add sumoselect to filters selection
    //$('.state').SumoSelect({
    //    placeholder: "<?php echo _('Select states'); ?>",
    //    captionFormatAllSelected: "<?php echo _('All states'); ?>"
    //});
    //$('.sumo-hide').removeClass('sumo-hide');

    // Set state disabled based on object selection
    $('#hostserviceDropdown').change(function() {
        $('.state').val('');
        var type = $(this).val();
        if (type == 'hosts') {
            $('.state .service').prop('disabled', true);
            $('.state .host').prop('disabled', false);
        } else if (type == 'services') {
            $('.state .host').prop('disabled', true);
            $('.state .service').prop('disabled', false);
        } else {
            $('.state option').prop('disabled', false);
        }
    });

    // Actually return the report
    $('#run').click(function() {
        run_statehistory_ajax();
    });

    $('#reportperiodDropdown').change(function () {
        showhidedates();
    });

    // Get the export button link and send user to it
    $('.btn-export').on('mousedown', function(e) {
        var type = $(this).data('type');
        var formvalues = $("form").serialize();
        formvalues += '&mode=getreport';
        var url = "<?php echo get_base_url(); ?>reports/statehistory.php?" + formvalues + "&mode=" + type;
        if (e.which == 2) {
            window.open(url);
        } else if (e.which == 1) {
            window.location = url;
        }
    });

});

var report_sym = 0;
function run_statehistory_ajax() {
    report_sym = 1;
    setTimeout('show_loading_report()', 500);

    var formvalues = $("form").serialize();
    formvalues += '&mode=getreport';
    var url = 'statehistory.php?'+formvalues;

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

<form method="get" data-type="statehistory">
    <div class="well report-options form-inline">

        <div class="reportexportlinks">
            <?php echo get_add_myreport_html(_("State History"), $_SERVER['PHP_SELF'], array()); ?>
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
                    <option value="*">[<?php echo _("Host Only"); ?>]</option>
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
                <label class="input-group-addon"><?php echo _('Type'); ?></label>
                <select id="hostserviceDropdown" name="hostservice" class="form-control">
                    <option value="hosts" <?php echo is_selected("hosts", $hostservice); ?>><?php echo _("Hosts"); ?></option>
                    <option value="services" <?php echo is_selected("services", $hostservice); ?>><?php echo _("Services"); ?></option>
                    <option value="both" <?php echo is_selected("both", $hostservice); ?>><?php echo _("Both"); ?></option>
                </select>
            </div>

            <div class="input-group" style="margin-right: 10px;">
                <label class="input-group-addon"><?php echo _('State Type'); ?></label>
                <select id="statetypeDropdown" name="statetype" class="form-control">
                    <option value="soft" <?php echo is_selected("soft", $statetype); ?>><?php echo _("Soft"); ?></option>
                    <option value="hard" <?php echo is_selected("hard", $statetype); ?>><?php echo _("Hard"); ?></option>
                    <option value="both" <?php echo is_selected("both", $statetype); ?>><?php echo _("Both"); ?></option>
                </select>
            </div>

            <div class="input-group" style="width: 180px; margin-right: 10px;">
                <label class="input-group-addon"><?php echo _('State'); ?></label>
                <select class="state form-control" name="state">
                    <option value="">Any</option>
                    <optgroup label="<?php echo _('Services'); ?>">
                        <option value="ok" class="service" <?php echo is_selected("ok", $state); ?>>OK</option>
                        <option value="warning" class="service" <?php echo is_selected("warning", $state); ?>>WARNING</option>
                        <option value="critical" class="service" <?php echo is_selected("critical", $state); ?>>CRITICAL</option>
                        <option value="unknown" class="service" <?php echo is_selected("unknown", $state); ?>>UNKNOWN</option>
                    </optgroup>
                    <optgroup label="<?php echo _('Hosts'); ?>">
                        <option value="up" class="host" <?php echo is_selected("up", $state); ?>>UP</option>
                        <option value="down" class="host" <?php echo is_selected("down", $state); ?>>DOWN</option>
                        <option value="unreachable" class="host" <?php echo is_selected("unreachable", $state); ?>>UNREACHABLE</option>
                    </optgroup>
                </select>
            </div>

            <button type="button" id="run" class='btn btn-sm btn-primary' name='reporttimesubmitbutton'><?php echo _("Run"); ?></button>

        </div>
    </div>
</form>

<div id="report"></div>
<?php
}

function get_statehistory_page()
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
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $statetype = grab_request_var("statetype", "hard");
    $hostservice = grab_request_var("hostservice", "both");
    $state = grab_request_var("state", "");
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

    // Special "all" stuff
    if ($hostgroup == "all") {
        $hostgroup = "";
    }
    if ($servicegroup == "all") {
        $servicegroup = "";
    }
    if ($host == "all") {
        $host = "";
    }

    // Can do hostgroup OR servicegroup OR host
    if ($hostgroup != "") {
        $servicegroup = "";
        $host = "";
    } else if ($servicegroup != "") {
        $host = "";
    }

    $host_ids = array();
    $service_ids = array();

    // Limit hosts by hostgroup or host
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
        $service_ids = get_hostgroup_service_member_ids($hostgroup);
    }

    // Limit service by servicegroup
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

    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1
    );

    if ($service != "" && $service != "*") {
        $args["service_description"] = $service;
    }

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
    
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }

    switch ($state) {
        case 'ok':
            $args["state"] = STATE_OK;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'warning':
            $args["state"] = STATE_WARNING;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'critical':
            $args["state"] = STATE_CRITICAL;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'unknown':
            $args["state"] = STATE_UNKNOWN;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'up':
            $args["state"] = STATE_UP;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'down':
            $args["state"] = STATE_DOWN;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'unreachable':
            $args["state"] = STATE_UNREACHABLE;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
    }

    // Object id limiters
    if ($object_ids_str != "") {
        $args["object_id"] = "in:" . $object_ids_str;
    } else {
        if ($host != "") {
            $args["host_name"] = $host;
        }
    }
    
    if ($search) {
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    }
    $xml = get_statehistory_data($args);
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
        "host" => $host,
        "service" => $service,
        "hostgroup" => $hostgroup,
        "servicegroup" => $servicegroup,
        "statetype" => $statetype,
        "hostservice" => $hostservice,
        "state" => $state
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    // SPECIFIC RECORDS (FOR PAGING): if you want to get specific records, use this type of format:
    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "records" => $records . ":" . $first_record
    );

    if ($service != "" && $service != "*") {
        $args["service_description"] = $service;
    }
    
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
    
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }

    switch ($state) {
        case 'ok':
            $args["state"] = STATE_OK;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'warning':
            $args["state"] = STATE_WARNING;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'critical':
            $args["state"] = STATE_CRITICAL;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'unknown':
            $args["state"] = STATE_UNKNOWN;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'up':
            $args["state"] = STATE_UP;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'down':
            $args["state"] = STATE_DOWN;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'unreachable':
            $args["state"] = STATE_UNREACHABLE;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
    }
    
    // Object id limiters
    if ($object_ids_str != "") {
        $args["object_id"] = "in:" . $object_ids_str;
    } else {
        if ($host != "") {
            $args["host_name"] = $host;
        }
    }

    if ($search) {
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    }
    $xml = get_statehistory_data($args);
?>

        <table class="table table-condensed table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 150px;"><?php echo _("Date / Time"); ?></th>
                    <th style="width: 12%;"><?php echo _("Host"); ?></th>
                    <th style="width: 18%;"><?php echo _("Service"); ?></th>
                    <th style="width: 80px;"><?php echo _("State"); ?></th>
                    <th style="width: 80px;"><?php echo _("State Type"); ?></th>
                    <th style="width: 80px;"><?php echo _("Attempt"); ?></th>
                    <th><?php echo _("Information"); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($xml) {
                if ($total_records == 0) {
                    echo "<tr><td colspan='7'>" . _("No matching results found. Try expanding your search criteria") . ".</td></tr>";
                } else {
                    foreach ($xml->stateentry as $se) {
                        $type_text = "";
                        $trclass = "";
                        $tdclass = "";

                        $object_type = intval($se->objecttype_id);
                        $host_name = strval($se->host_name);
                        $service_description = strval($se->service_description);
                        $output = strval($se->output);

                        $state = intval($se->state);
                        $state_type = intval($se->state_type);
                        $current_attempt = intval($se->current_check_attempt);
                        $max_attempts = intval($se->max_check_attempts);

                        if ($object_type == OBJECTTYPE_HOST) {
                            if ($hostservice == 'services')
                                    continue;
                            $state_text = host_state_to_string($state);
                            switch ($state) {
                                case 0:
                                    $trclass = "hostrecovery";
                                    $tdclass = "hostup";
                                    break;
                                case 1:
                                    $trclass = "hostproblem";
                                    $tdclass = "hostdown";
                                    break;
                                case 2:
                                    $trclass = "hostproblem";
                                    $tdclass = "hostunreachable";
                                    break;
                                default:
                                    break;
                            }
                        } else {
                            if ($hostservice == 'hosts')
                                    continue;
                            $state_text = service_state_to_string($state);
                            switch ($state) {
                                case 0:
                                    $trclass = "servicerecovery";
                                    $tdclass = "serviceok";
                                    break;
                                case 1:
                                    $trclass = "serviceproblem";
                                    $tdclass = "servicewarning";
                                    break;
                                case 2:
                                    $trclass = "serviceproblem";
                                    $tdclass = "servicecritical";
                                    break;
                                case 3:
                                    $trclass = "serviceproblem";
                                    $tdclass = "serviceunknown";
                                    break;
                                default:
                                    break;
                            }
                        }
                        $state_type_text = state_type_to_string($state_type);

                        if ($export) {
                            $burl = get_external_url();
                        } else {
                            $burl = get_base_url();
                        }

                        $base_url = $burl . "includes/components/xicore/status.php";
                        $host_url = $base_url . "?show=hostdetail&host=" . urlencode($host_name);
                        $service_url = $base_url . "?show=servicedetail&host=" . urlencode($host_name) . "&service=" . urlencode($service_description);

                        echo "<tr class='" . $trclass . "'>";
                        echo "<td nowrap><span class='statehistorytype'>";
                        if (!$export) {
                            echo "<img src='' alt='" . $type_text . "' title='" . $type_text . "'>";
                        }
                        echo "</span><span class='statehistorytime'>" . $se->state_time . "</span></td>";
                        echo "<td><a href='" . $host_url . "'>" . $host_name . get_host_alias($host_name) . "</a></td>";
                        echo "<td><a href='" . $service_url . "'>" . $service_description . get_service_alias($host_name, $service_description) . "</a></td>";
                        echo "<td class='" . $tdclass . "'>" . _($state_text) . "</td>";
                        echo "<td>" . _($state_type_text) . "</td>";
                        echo "<td>" . $current_attempt . " of " . $max_attempts . "</td>";
                        echo "<td>" . $output . "</td>";
                        echo "</tr>";

                    }
                }
            }
            ?>
            </tbody>
        </table>

<?php
}

function get_statehistory_report()
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
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $statetype = grab_request_var("statetype", "hard");
    $hostservice = grab_request_var("hostservice", "both");
    $state = grab_request_var("state", "");
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

    // Special "all" stuff
    if ($hostgroup == "all") {
        $hostgroup = "";
    }
    if ($servicegroup == "all") {
        $servicegroup = "";
    }
    if ($host == "all") {
        $host = "";
    }

    // Can do hostgroup OR servicegroup OR host
    if ($hostgroup != "") {
        $servicegroup = "";
        $host = "";
    } else if ($servicegroup != "") {
        $host = "";
    }

    $host_ids = array();
    $service_ids = array();

    // Limit hosts by hostgroup or host
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
        $service_ids = get_hostgroup_service_member_ids($hostgroup);
    }

    // Limit service by servicegroup
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

    // TOTAL RECORD COUNT (FOR PAGING): if you wanted to get the total count of records in a given timeframe (instead of the records themselves), use this:

    $args = array(
        "starttime" => $starttime,
        "endtime" => $endtime,
        "totals" => 1
    );

    if ($service != "" && $service != "*") {
        $args["service_description"] = $service;
    }

    switch ($statetype) {
        case "soft":
            $args["state_type"] = STATETYPE_SOFT;
            break;
        case "hard":
            $args["state_type"] = STATETYPE_HARD;
            break;
        default:
            break;
    }
    
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }

    switch ($state) {
        case 'ok':
            $args["state"] = STATE_OK;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'warning':
            $args["state"] = STATE_WARNING;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'critical':
            $args["state"] = STATE_CRITICAL;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'unknown':
            $args["state"] = STATE_UNKNOWN;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'up':
            $args["state"] = STATE_UP;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'down':
            $args["state"] = STATE_DOWN;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'unreachable':
            $args["state"] = STATE_UNREACHABLE;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
    }

    // Object id limiters
    if ($object_ids_str != "") {
        $args["object_id"] = "in:" . $object_ids_str;
    } else {
        if ($host != "") {
            $args["host_name"] = $host;
        }
    }
    
    if ($search) {
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    }
    $xml = get_statehistory_data($args);
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
        "host" => $host,
        "service" => $service,
        "hostgroup" => $hostgroup,
        "servicegroup" => $servicegroup,
        "statetype" => $statetype,
        "hostservice" => $hostservice,
        "state" => $state
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    $title = _("State History");
    $sub_title = "";
    if ($service != "" && $service != "*") {
        $title = _("Service State History");
        $sub_title = "
        <div class='servicestatusdetailheader' style='margin-bottom: 10px;'>
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
        $title = _("Host State History");
        $sub_title = "
            <div class='hoststatusdetailheader' style='margin-bottom: 10px;'>
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
        $title = _("Hostgroup State History");
        $sub_title = "
            <div class='hoststatusdetailheader' style='margin-bottom: 10px;'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($hostgroup) . get_hostgroup_alias($hostgroup) . "</div>
                </div>
                <div class='clear'></div>
            </div>";

    } else if ($servicegroup != "") {
        $title = _("Servicegroup State History");
        $sub_title = "
            <div class='hoststatusdetailheader' style='margin-bottom: 10px;'>
                <div class='hosttitle'>
                    <div class='hostname'>" . encode_form_val($servicegroup) . get_servicegroup_alias($servicegroup) . "</div>
                </div>
                <div class='clear'></div>
            </div>";
    }
    $report_covers_from = "
                <div>" . _("Report covers from") . ":
                    <strong>" . get_datetime_string($starttime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</strong> " . _("to") . "
                    <strong>" . get_datetime_string($endtime, DT_SHORT_DATE_TIME, DF_AUTO, "null") . "</strong>
                </div>";

    // Start the HTML page
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

        <script type='text/javascript' src='<?php echo get_base_url(); ?>includes/js/reports.js?<?php echo get_build_id(); ?>'></script>

        <div style="padding-bottom: 20px;">
            <div style="float: left; margin-right: 30px;">
                <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
            </div>
            <div style="float: left; height: 44px;">
                <div style="font-weight: bold; font-size: 22px;"><?php echo $title; ?></div>
                <?php echo $report_covers_from; ?>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php echo $sub_title; ?>
        
    <?php
    } else { ?>

    <h1 style="margin-bottom: 10px;"><?php echo $title; ?></h1>

    <?php echo $sub_title; ?>
    <?php echo $report_covers_from; ?>

    <?php } ?>

    <div class="recordcounttext">
        <?php
        $clear_args = array(
            "reportperiod" => $reportperiod,
            "startdate" => $startdate,
            "enddate" => $enddate,
            "starttime" => $starttime,
            "endtime" => $endtime,
            "host" => $host,
            "service" => $service,
            "hostgroup" => $hostgroup,
            "servicegroup" => $servicegroup
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

<?php
$url_args = array(
    "reportperiod" => $reportperiod,
    "startdate" => $startdate,
    "enddate" => $enddate,
    "starttime" => $starttime,
    "endtime" => $endtime,
    "search" => $search,
    "host" => $host,
    "service" => $service,
    "hostgroup" => $hostgroup,
    "servicegroup" => $servicegroup,
    "statetype" => $statetype,
    "hostservice" => $hostservice,
    "state" => $state,
    "export" => intval($export)
);
?>

<script>
var report_url = '<?php echo get_base_url(); ?>reports/statehistory.php';
var report_url_args = <?php echo json_encode($url_args); ?>;
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

    do_page_end(true);
}

function get_statehistory_xml()
{
    global $request;

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
    $statetype = grab_request_var("statetype", "hard");
    $hostservice = grab_request_var("hostservice", "both");
    $state = grab_request_var("state", "");

    // fix search
    if ($search == _("Search..."))
        $search = "";

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

    $host_ids = array();
    $service_ids = array();

    //  limit hosts by hostgroup or host
    //  limit by hostgroup
    if ($hostgroup != "") {
        $host_ids = get_hostgroup_member_ids($hostgroup);
        $service_ids = get_hostgroup_service_member_ids($hostgroup);
        //echo "SIDS:<BR>";
        //print_r($service_ids);
        //echo "<BR>";
    }
    //  limit service by servicegroup
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
    switch ($hostservice) {
        case "hosts":
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case "services":
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        default:
            break;
    }

    switch ($state) {
        case 'ok':
            $args["state"] = STATE_OK;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'warning':
            $args["state"] = STATE_WARNING;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'critical':
            $args["state"] = STATE_CRITICAL;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'unknown':
            $args["state"] = STATE_UNKNOWN;
            $args["objecttype_id"] = OBJECTTYPE_SERVICE;
            break;
        case 'up':
            $args["state"] = STATE_UP;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'down':
            $args["state"] = STATE_DOWN;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
        case 'unreachable':
            $args["state"] = STATE_UNREACHABLE;
            $args["objecttype_id"] = OBJECTTYPE_HOST;
            break;
    }

    // object id limiters
    if ($object_ids_str != "")
        $args["object_id"] = "in:" . $object_ids_str;
    else {
        if ($host != "")
            $args["host_name"] = $host;
    }
    if (!empty($search)) {
        $args["output"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    }

    if ($service != "") {
        $args["service_description"] = $service;
    }

    $xml = get_statehistory_data($args);
    return $xml;
}

// This function generates a CSV file of event log data
function get_statehistory_csv()
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
    $statetype = grab_request_var("statetype", "hard");
    $hostservice = grab_request_var("hostservice", "both");
    $state = grab_request_var("state", "");
    $xml = get_statehistory_xml();

    // Output header for csv
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . time() ."-statehistory.csv\"");

    // Column definitions
    echo "time,host,service,statechange,state,statetype,currentattempt,maxattempts,laststate,lasthardstate,information\n";

    if ($xml) {
        foreach ($xml->stateentry as $se) {

            // What type of log entry is this?  we change the image used for each line based on what type it is...
            $object_type = intval($se->objecttype_id);
            $host_name = strval($se->host_name);
            $service_description = strval($se->service_description);
            $state_change = intval($se->state_change);
            if ($object_type == OBJECTTYPE_HOST) {
                if ($hostservice == 'services')
                        continue;
                $state = host_state_to_string(intval($se->state));
                $last_state = host_state_to_string(intval($se->last_state));
                $last_hard_state = host_state_to_string(intval($se->last_hard_state));
            } else {
                if ($hostservice == 'hosts')
                        continue;
                $state = service_state_to_string(intval($se->state));
                $last_state = service_state_to_string(intval($se->last_state));
                $last_hard_state = service_state_to_string(intval($se->last_hard_state));
            }
            $state_type = state_type_to_string(intval($se->state_type));
            $current_check_attempt = intval($se->current_check_attempt);
            $max_check_attempts = intval($se->max_check_attempts);
            $output = strval($se->output);

            echo $se->state_time . ",\"" . $host_name . "\",\"" . $service_description . "\"," . $state_change . ",\"" . $state . "\",\"" . $state_type . "\"," . $current_check_attempt . "," . $max_check_attempts . "," . $last_state . "," . $last_hard_state . ",\"" . str_replace( array("\r", "\n", "&apos;"), array(" ", " ", "'"), html_entity_decode($output)) . "\"\n";
        }
    }
}

///////////////////////////////////////////////////////////////////
// HELPER FUNCTIONS
///////////////////////////////////////////////////////////////////

// Return corresponding image and text to use
function get_statehistory_type_info($objecttype, $state, $statetype, &$img, &$text)
{

    // initial/default values
    $img = "info.png";
    $text = "";
    //return;

    if ($objecttype == OBJECTTYPE_HOST) {
        switch ($state) {
            case 0:
                $img = "recovery.png";
                break;
            case 1:
                $img = "critical.png";
                break;
            case 2:
                $img = "critical.png";
                break;
        }
    } else {
        switch ($state) {
            case 0:
                $img = "recovery.png";
                break;
            case 1:
                $img = "warning.png";
                break;
            case 2:
                $img = "critical.png";
                break;
            case 3:
                $img = "unknown.png";
                break;
        }
    }
}