<?php
//
// Performance Data Graphs
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');


// Initialization stuff and check authentication
pre_init();
init_session();
grab_request_vars();
check_prereqs();
check_authentication();


display_page();


function display_page()
{
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");

    // Set the correct mode
    $mode = PERFGRAPH_MODE_HOSTSOVERVIEW;
    if (!empty($host)) {
        if (!empty($service)) {
            $mode = PERFGRAPH_MODE_SERVICEDETAIL;
        } else {
            $mode = PERFGRAPH_MODE_HOSTOVERVIEW;
        }
    }

    // Grab column
    $column = grab_request_var("column", '');
    if (!empty($column)) {
        set_user_meta(0, "default_perfdata_column", intval($column));
    }

    switch ($mode) {
        case PERFGRAPH_MODE_HOSTSOVERVIEW:
            draw_hosts_overview_graphs($host);
            break;
        case PERFGRAPH_MODE_HOSTOVERVIEW:
            draw_host_overview_graphs($host);
            break;
        case PERFGRAPH_MODE_SERVICEDETAIL:
            draw_service_detail_graphs($host, $service);
            break;
        default:
            break;
    }
}


function do_perfgraphs_page_titles($mode)
{
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $view = grab_request_var("view", get_perfgraph_default_setting("view", PNP_VIEW_DEFAULT));
    $start = grab_request_var("start", "");
    $end = grab_request_var("end", "");

    // custom start date
    $startdate = grab_request_var("startdate", "");
    if ($startdate != "") {
        $start = nstrtotime($startdate);
    }
    // custom end date
    $enddate = grab_request_var("enddate", "");
    if ($enddate != "") {
        $end = nstrtotime($enddate);
    }

    // custom dates
    if ($startdate != "" && $enddate != "")
        $view = PNP_VIEW_CUSTOM;

    //bug fix for potential blank view of graphs on initial load -MG
    if ($start == "" && $end == "" && $view == PNP_VIEW_CUSTOM)
        $view = PNP_VIEW_DEFAULT;

    $title = "";
    $subtitle = "";

    switch ($mode) {
        case PERFGRAPH_MODE_HOSTSOVERVIEW:
            $title = _("Host Performance Graphs");
            break;
        case PERFGRAPH_MODE_HOSTOVERVIEW:
            $title = $host . " " . _("Performance Graphs");
            break;
        case PERFGRAPH_MODE_SERVICEDETAIL:
            if ($service == "_HOST_")
                $servicename = _("Host");
            else
                $servicename = $service;
            $title = $host . " " . $servicename . " " . _("Performance Graphs");
            break;
        default:
            break;
    }

    switch ($view) {
        case PNP_VIEW_4HOURS:
            $title .= " - " . _("4 Hour View");
            if ($end != "")
                $start = $end - (60 * 60 * 4);
            break;
        case PNP_VIEW_1DAY:
            $title .= " - " . _("24 Hour View");
            if ($end != "")
                $start = $end - (60 * 60 * 24);
            break;
        case PNP_VIEW_1WEEK:
            $title .= " - " . _("1 Week View");
            if ($end != "")
                $start = $end - (60 * 60 * 24 * 7);
            break;
        case PNP_VIEW_1MONTH:
            $title .= " - " . _("1 Month View");
            if ($end != "")
                $start = $end - (60 * 60 * 24 * 30);
            break;
        case PNP_VIEW_1YEAR:
            $title .= " - " . _("1 Year View");
            if ($end != "")
                $start = $end - (60 * 60 * 24 * 265);
            break;
        case PNP_VIEW_CUSTOM:
            $title .= " - " . _("Custom Period");
    }

    if ($end != "") {
        $url = build_url_from_current(array("start" => "", "end" => "", "startdate" => "", "enddate" => "", "view" => PNP_VIEW_1DAY));
        $daterange = "<b>" . get_datetime_string($start) . "</b> - <b>" . get_datetime_string($end) . "</b>";
        $subtitle = $daterange . '&nbsp;<a href="' . $url . '" class="tt-bind" title="' . _("Clear date filter") .'"><img src="' . theme_image("b_clearsearch.png") . '"></a>';
    }

    echo "<div class='perfgraphstitle'>" . $title . "</div>";

    $subhide = '';
    if (empty($subtitle)) {
        $subhide = ' hide';
    }
    echo "<div class='perfgraphssubtitle$subhide'>" . $subtitle . "</div>";
}


function do_perfgraphs_page_start($view)
{
    licensed_feature_check(true, true);

    $search = grab_request_var("search", "");

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $startdate = grab_request_var("startdate", "");
    $enddate = grab_request_var("enddate", "");
    $mode = grab_request_var("mode", PERFGRAPH_MODE_HOSTSOVERVIEW);
    $host_id = grab_request_var('host_id', '');
    $service_id = grab_request_var('service_id', '');
    $source = grab_request_var('source', 1);
    $column = get_user_meta(0, 'default_perfdata_column', 2);

    // Column set to 1 for rrd graphs
    if (get_option("perfdata_theme", 1) != 1) {
        $column = 1;
    }

    // Set modes based on what we were given
    if (!empty($host)) {
        if (!empty($service)) {
            $mode = PERFGRAPH_MODE_SERVICEDETAIL;
        } else {
            $mode = PERFGRAPH_MODE_HOSTOVERVIEW;
        }
    }

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

    do_page_start(array("page_title" => _("Performance Graphs"), "page_id" => "perfgraphspage"), true);
?>

    <script type="text/javascript">
    $(document).ready(function () {

        $('#searchBox').click(function () {
            if ($('#searchBox').val() == '<?php echo _('Search...'); ?>') {
                $('#searchBox').val('');
            }
        });

        $('#searchButton').click(function () {
            if ($('#searchBox').val() != '') {
                $('#hiddenmode').val('0');
                $('#hiddenhost').val('');
            }
        });

        $('#view').change(function() {
            if ($(this).val() == 99) {
                $('.custom-view').show();
            } else {
                $('.custom-view input').val('');
                $('.custom-view').hide();
            }
        });

        $('#startdateBox').click(function () {
            if ($('#startdateBox').val() == '') {
                $('#startdateBox').val('<?php echo $auto_start_date;?>');
            }
        });

        $('#enddateBox').click(function () {
            if ($('#enddateBox').val() == '') {
                $('#enddateBox').val('<?php echo $auto_end_date;?>');
            }
        });

        $('#hostListPerf').searchable({maxMultiMatch: 9999});
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

        $('form').submit(function(e) {
            if ($('#view').val() == 99) {
                if ($('#startdateBox').val() == '' || $('#enddateBox').val() == '') {
                   alert('<?php echo _("Both Start Date and End Date are required for custom date searches"); ?>');
                   e.preventDefault();
                }
            }
        });

        $('.pf-display-settings label').click(function() {
            if ($(this).find('input').val() == 1) {
                $('input[name="column"]').val(1);
            } else {
                $('input[name="column"]').val(2);
            }
            $('.update-pf').submit();
        });

    });
    </script>

    <form method="get" class="update-pf" action="">
        <div class="well report-options form-inline">

            <div class="input-group fl" style="width: 220px; margin-right: 20px;">
                <labal class="input-group-addon"><?php echo _('Time Period'); ?></labal>
                <select class="form-control" id="view" name="view">
                    <option value="0" <?php echo is_selected($view, 0); ?>><?php echo _('Last 4 Hours'); ?></option>
                    <option value="1" <?php echo is_selected($view, 1); ?>><?php echo _('Last 24 Hours'); ?></option>
                    <option value="2" <?php echo is_selected($view, 2); ?>><?php echo _('Last 7 Days'); ?></option>
                    <option value="3" <?php echo is_selected($view, 3); ?>><?php echo _('Last 30 Days'); ?></option>
                    <option value="4" <?php echo is_selected($view, 4); ?>><?php echo _('Last 365 Days'); ?></option>
                    <option value="99" <?php echo is_selected($view, 99); ?>><?php echo _('Custom'); ?></option>
                </select>
            </div>

            <div class="custom-view <?php if ($view != 99) { echo 'hide'; } ?>">
                <div class="input-group fl" style="width: 440px; margin-right: 20px;">
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

            <div class="input-group fl" style="width: 220px; margin-right: 20px;">
                <label class="input-group-addon"><?php echo _('Host'); ?></label>
                <select name="host" id="hostListPerf" data-full-width="1" class="form-control">
                    <option value=""></option>
                    <?php
                    $args = array('brevity' => 1, 'orderby' => 'host_name:a');
                    $oxml = get_xml_host_objects($args);
                    if ($oxml) {
                        foreach ($oxml->host as $hostobject) {
                            $name = strval($hostobject->host_name);
                            echo "<option value='" . $name . "' " . is_selected($host, $name) . ">$name</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="perfgraphsuggest fr" style="vertical-align: top;">
                <input type="text" size="15" name="search" id="searchBox" value="<?php echo encode_form_val($search); ?>" placeholder="<?php echo _("Search..."); ?>" class="textfield form-control">
            </div>
            <div class="fl">
                <button type="submit" name="update" id="update" class="btn btn-sm btn-primary"><?php echo _('Update'); ?></button>
            </div>
            <div class="clear"></div>
        </div>
        <input type='hidden' name='host_id' value="<?php echo encode_form_val($host_id); ?>">
        <input type='hidden' name='service_id' value="<?php echo encode_form_val($service_id); ?>">
        <input type='hidden' name='service' value="<?php echo encode_form_val($service); ?>">
        <input type='hidden' name='source' value="<?php echo intval($source); ?>">
        <input type='hidden' name='column' value="">
    </form>

    <div class="perfgraphspage">
        <div>
            <h1 style="padding-top: 0; padding-right: 20px;" class="fl"><?php echo _("Performance Graphs"); ?></h1>
            <div class="fl">
                <?php if (get_option("perfdata_theme", 1) == 1) { ?>
                <div class="btn-group pf-display-settings" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($column == 2) { echo 'active'; } ?> tt-bind" style="padding: 4px;" title="<?php echo _('Two column view'); ?>">
                        <input type="radio" name="columns" value="2" autcomplete="off" <?php echo is_checked($column, 2); ?>><i class="fa fa-fw fa-th-large" style="font-size: 14px;"></i>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($column == 1) { echo 'active'; } ?> tt-bind" style="padding: 4px;" title="<?php echo _('Single column view'); ?>">
                        <input type="radio" name="columns" value="1" autocomplete="off" <?php echo is_checked($column, 1); ?>><i class="fa fa-fw fa-bars" style="font-size: 14px;"></i>
                    </label>
                </div>
                <?php } ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="perfgraphscontainer<?php if ($column == 2) { echo ' two-column'; } ?>">

<?php
}

function do_perfgraphs_page_end()
{
    echo "</div><!--perfgraphscontainer-->\n";
    echo "</div><!--perfgraphspage-->\n";
    do_page_end(true);
}


/**
 * @param        $setting
 * @param string $default
 *
 * @return array|null|string
 */
function get_perfgraph_default_setting($setting, $default = "")
{
    $value = $default;

    // Get saved value (user's preference)
    $savedval = get_user_meta(0, "perfgraph_default_" . $setting);
    if ($savedval != null) {
        $value = $savedval;
    }

    // Save new setting (use value passed in client request)
    $requestval = grab_request_var($setting, "");
    if ($requestval != "") {
        $value = $requestval;
        set_user_meta(0, "perfgraph_default_" . $setting, $value, false);
    }

    return $value;
}

function draw_service_detail_graphs()
{
    $host = grab_request_var("host", "");
    $host_id = grab_request_var("host_id", -1);
    $service = grab_request_var("service", "");
    $service_id = grab_request_var("service_id", -1);
    $view = grab_request_var("view", get_perfgraph_default_setting("view", PNP_VIEW_DEFAULT));
    $start = grab_request_var("start", "");
    $end = grab_request_var("end", "");

    // Custom start date
    $startdate = grab_request_var("startdate", "");
    if ($startdate != "") {
        $start = nstrtotime($startdate);
    }

    // Custom end date
    $enddate = grab_request_var("enddate", "");
    if ($enddate != "") {
        $end = nstrtotime($enddate);
    }

    // Custom dates
    if ($startdate != "" && $enddate != "")
        $view = PNP_VIEW_CUSTOM;

    // Bug fix for potential blank view of graphs on initial load -MG
    if ($start == "" && $end == "" && $view == PNP_VIEW_CUSTOM)
        $view = PNP_VIEW_DEFAULT;

    do_perfgraphs_page_start($view);
    do_perfgraphs_page_titles(PERFGRAPH_MODE_SERVICEDETAIL);

    $sources = perfdata_get_service_sources($host, $service);
    foreach ($sources as $s) {

        echo "<div class='serviceperfgraphcontainer pd-container'>";

        $dargs = array(
            DASHLET_ADDTODASHBOARDTITLE => _("Add to Dashboard"),
            DASHLET_ARGS => array(
                "hostname" => $host,
                "host_id" => $host_id,
                "servicename" => $service,
                "service_id" => $service_id,
                "source" => $s["id"],
                "sourcename" => $s["name"],
                "sourcetemplate" => $s["template"],
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "width" => "600",
                "height" => "300",
                "mode" => PERFGRAPH_MODE_SERVICEDETAIL,
            ),
            DASHLET_TITLE => $host . " " . $service . " " . _("Performance Graph"),
        );

        display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);

        if ($service == "_HOST_")
            draw_host_perfgraph_links($host);
        else
            draw_service_perfgraph_links($host, $service);

        echo '<div class="clear"></div></div>';
    }

    if (count($sources) == 0)
        echo _("There are no datasources to display for this service.");

    do_perfgraphs_page_end();
}

function draw_host_overview_graphs($host)
{
    $mode = PERFGRAPH_MODE_HOSTOVERVIEW;
    $search = grab_request_var("search", "");
    $source = grab_request_var("source", 1);
    $view = grab_request_var("view", get_perfgraph_default_setting("view", PNP_VIEW_DEFAULT));
    $start = grab_request_var("start", "");
    $end = grab_request_var("end", "");
    $records = grab_request_var("records", get_perfgraph_default_setting("records", 5));
    $page = grab_request_var("page", 1);

    // Don't display primary host performance graph if it's not in search term
    $disp_host = true;
    if (!empty($search)) {
        $disp_host = false;
        if (strpos('_host_', strtolower($search)) !== false) {
            $disp_host = true;
        }
    }

    // Custom start date
    $startdate = grab_request_var("startdate", "");
    if ($startdate != "") {
        $start = nstrtotime($startdate);
    }

    // Custom end date
    $enddate = grab_request_var("enddate", "");
    if ($enddate != "") {
        $end = nstrtotime($enddate);
    }

    // Custom dates
    if ($startdate != "" && $enddate != "")
        $view = PNP_VIEW_CUSTOM;

    // Bug fix for potential blank view of graphs on initial load -MG
    if ($start == "" && $end == "" && $view == PNP_VIEW_CUSTOM)
        $view = PNP_VIEW_DEFAULT;

    // First get total records
    $args = array(
        "cmd" => "getservicestatus",
        "host_name" => $host,
        "brevity" => 2,
        "service_description" => "lk:" . $search
    );
    $xml = get_xml_service_status($args);

    // Check to make sure the service has perfdata
    $total_records = 0;
    if ($disp_host) {
        $total_records = 1;
    }
    if ($xml) {
        foreach ($xml->servicestatus as $s) {
            if (pnp_chart_exists(strval($s->host_name), strval($s->name))) {
                $total_records++;
            }
        }
    }

    // Get paging info - reset page number if necessary
    $pager_args = array(
        "search" => $search,
        "host" => $host,
        "source" => $source,
        "view" => $view,
        "mode" => $mode,
        "start" => $start,
        "end" => $end,
        "startdate" => $startdate,
        "enddate" => $enddate
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $pager_args);

    // Adjust start/end records to compensate for first record being host performance graph
    $first_record = (($pager_results["current_page"] - 1) * $records);
    $records_this_page = $records;
    if ($page == 1 && $disp_host) {
        $records_this_page--;
    } else {
        $first_record--;
    }

    // Run record-limiting query
    $args = array(
        "cmd" => "getservicestatus",
        "host_name" => $host,
        "brevity" => 2,
        "service_description" => "lk:" . $search
    );

    $xml = get_xml_service_status($args);
    $i = 0;
    $j = 0;
    $servicelist = array();
    foreach ($xml->servicestatus as $s) {
        if (pnp_chart_exists(strval($s->host_name), strval($s->name))) {
            if ($i < $first_record) {
                $i++;
                continue;
            }
            if ($j < $records_this_page) {
                $servicelist[] = $s;
                $j++;
            } else {
                break;
            }
        }
    }

    do_perfgraphs_page_start($view);
    do_perfgraphs_page_titles($mode);

    ?>

    <div class="recordcounttext">
        <?php
        $clear_args = array(
            "search" => "",
            "host" => $host,
            "source" => $source,
            "view" => $view,
            "start" => $start,
            "end" => $end,
            "startdate" => $startdate,
            "enddate" => $enddate,
        );
        echo table_record_count_text($pager_results, $search, true, $clear_args);
        ?>
    </div>
    <div class='recordpagerlinks'>
        <form method="get" action="">
            <?php
            $opts = array(
                "search" => $search,
                "host" => $host,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
            );
            ?>
            <?php table_record_pager($pager_results, null, $opts); ?>
        </form>
    </div>
    <div class="perfgraphsheader"></div>

    <?php

    if ($page == 1 && $disp_host) {
        // Primary host performance graph
        echo "<div class='serviceperfgraphcontainer pd-container'>";
        $dargs = array(
            DASHLET_ADDTODASHBOARDTITLE => _("Add to Dashboard"),
            DASHLET_ARGS => array(
                "host_id" => get_host_id($host),
                "hostname" => $host,
                "servicename" => "_HOST_",
                "source" => $source,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "width" => "600",
                "height" => "300",
                "mode" => PERFGRAPH_MODE_HOSTOVERVIEW,
            ),
            DASHLET_TITLE => $host . " " . _("Host Performance Graph"),
        );
        display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);
        draw_host_perfgraph_links($host);
        echo '<div class="clear"></div></div>';
    }

    // loop over all services

    foreach ($servicelist as $s) {

        $hostname = strval($s->host_name);
        $servicename = strval($s->name);

        $sources = perfdata_get_service_sources($hostname, $servicename);
        foreach ($sources as $s_ds) {

            echo "<div class='serviceperfgraphcontainer pd-container'>";

            $dargs = array(
                DASHLET_ADDTODASHBOARDTITLE => _("Add to Dashboard"),
                DASHLET_ARGS => array(
                    "hostname" => $hostname,
                    "host_id" => get_host_id($hostname),
                    "servicename" => $servicename,
                    "service_id" => intval($s->service_id),
                    "source" => $s_ds["id"],
                    "sourcename" => $s_ds["name"],
                    "sourcetemplate" => $s_ds["template"],
                    "view" => $view,
                    "start" => $start,
                    "end" => $end,
                    "startdate" => $startdate,
                    "enddate" => $enddate,
                    "width" => "600",
                    "height" => "300",
                    "mode" => PERFGRAPH_MODE_GOTOSERVICEDETAIL,
                ),
                DASHLET_TITLE => $host . " " . $servicename . " " . _("Performance Graph"),
            );

            display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);

            if ($servicename == "_HOST_")
                draw_host_perfgraph_links($host);
            else
                draw_service_perfgraph_links($host, $servicename);

            echo '<div class="clear"></div></div>';

            // Break if we are using the HC theme so we don't display multiples of the same graphs on service detail page -SW
            if (get_option("perfdata_theme", 1) != 0)
                break;
        }
    }
    ?>

    <div class='recordpagerlinks'>
        <form method="get" action="">
            <?php
            $opts = array(
                "search" => $search,
                "host" => $host,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
            );
            ?>
            <?php table_record_pager($pager_results, null, $opts); ?>
        </form>
    </div>

    <?php
    do_perfgraphs_page_end();
}


function draw_hosts_overview_graphs($host)
{
    $mode = PERFGRAPH_MODE_HOSTSOVERVIEW;

    // get request vars
    $search = grab_request_var("search", "");
    $source = grab_request_var("source", 1);
    $view = grab_request_var("view", get_perfgraph_default_setting("view", PNP_VIEW_DEFAULT));
    $start = grab_request_var("start", "");
    $end = grab_request_var("end", "");
    $sortby = grab_request_var("sortby", "host_name:a");
    $records = grab_request_var("records", get_perfgraph_default_setting("records", 5));
    $page = grab_request_var("page", 1);

    // custom start date
    $startdate = grab_request_var("startdate", "");
    if ($startdate != "") {
        $start = nstrtotime($startdate);
    }
    // custom end date
    $enddate = grab_request_var("enddate", "");
    if ($enddate != "") {
        $end = nstrtotime($enddate);
    }

    // custom dates
    if ($startdate != "" && $enddate != "")
        $view = PNP_VIEW_CUSTOM;

    //bug fix for potential blank view of graphs on initial load -MG
    if ($start == "" && $end == "" && $view == PNP_VIEW_CUSTOM)
        $view = PNP_VIEW_DEFAULT;

    // run record-limiting query
    $args = array(
        "cmd" => "gethoststatus",
        "brevity" => 2
    );
    if (have_value($search) == true)
        $args["host_name"] = "lk:" . $search . ";service_description=lk:" . $search;
    if (have_value($host) == true) // specific host
        $args["host_name"] = $host;
    if (have_value($sortby) == true)
        $args["orderby"] = $sortby;
    $xml = get_xml_host_status($args);

    // adjust total based on hosts with working perfgraphs
    $total_records = 0;
    foreach ($xml->hoststatus as $h) {
        if (pnp_chart_exists(strval($h->name)) == false)
            continue;
        $total_records++;
    }

    // Get paging info - reset page number if necessary
    $pager_args = array(
        "sortby" => $sortby,
        "search" => $search,
        "view" => $view,
        "start" => $start,
        "end" => $end,
        "startdate" => $startdate,
        "enddate" => $enddate
    );
    $pager_results = get_table_pager_info("", $total_records, $page, $records, $pager_args);
    $first_record = (($pager_results["current_page"] - 1) * $records);

    // ONE SEARCH MATCH - REDIRECT
    // only one match found in search - redirect to host overview screen
    if ($xml != null && intval($xml->recordcount) == 1 && have_value($search) == true) {
        $hostname = strval($xml->hoststatus[0]->name);
        $newurl = build_url_from_current(array(
            "service" => "",
            "search" => "",
            "host" => $hostname
        ));
        header("Location: $newurl");
        return;
    }

    do_perfgraphs_page_start($view);
    do_perfgraphs_page_titles($mode);

    ?>
    <div class="recordcounttext">
        <?php
        $clear_args = array(
            "search" => "",
            "source" => $source,
            "view" => $view,
            "start" => $start,
            "end" => $end,
            "startdate" => $startdate,
            "enddate" => $enddate
        );
        echo table_record_count_text($pager_results, $search, true, $clear_args);
        ?>
    </div>
    <div class='recordpagerlinks'>
        <form method="get" action="">
            <?php
            $opts = array(
                "search" => $search,
                "host" => $host,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
            );
            ?>
            <?php table_record_pager($pager_results, null, $opts); ?>
        </form>
    </div>
    <div class="perfgraphsheader"></div>

    <?php
    // loop over all hosts
    $current_record = 0;
    $processed_records = 0;
    //echo "FIRST=$first_record, MAX=$records<BR>";
    //print_r($xml);
    foreach ($xml->hoststatus as $h) {

        //echo "<br clear='all'>PROCESSING ".$h->name."<BR>";

        // skip hosts with no perfdata
        if (pnp_chart_exists(strval($h->name)) == false) {
            //echo "CHART DOES NOT EXIST FOR ".$h->name."!<BR>";
            continue;
        } else {
            if ($processed_records >= $records) {
                //echo "REACHED $processed_records RECORDS<BR>";
                break;
            }
            //echo "CHART EXISTS FOR ".$h->name."<BR>";
        }

        $current_record++;
        if ($current_record <= $first_record)
            continue;
        $processed_records++;

        echo '<div class="hostperfgraphcontainer pd-container">';

        $dargs = array(
            DASHLET_ADDTODASHBOARDTITLE => _("Add to Dashboard"),
            DASHLET_ARGS => array(
                "host_id" => intval($h->host_id),
                "hostname" => strval($h->name),
                "servicename" => "",
                "source" => $source,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "width" => "600",
                "height" => "300",
                "mode" => PERFGRAPH_MODE_HOSTSOVERVIEW,
            ),
            DASHLET_TITLE => strval($h->name) . " " . _("Performance Graph"),
        );

        display_dashlet("xicore_perfdata_chart", "", $dargs, DASHLET_MODE_OUTBOARD);

        draw_host_perfgraph_links($h->name);

        echo '<div class="clear"></div></div>';
    }
    ?>

    <div class='recordpagerlinks'>
        <form method="get" action="">
            <?php
            $opts = array(
                "search" => $search,
                "host" => $host,
                "view" => $view,
                "start" => $start,
                "end" => $end,
                "startdate" => $startdate,
                "enddate" => $enddate,
            );
            ?>
            <?php table_record_pager($pager_results, null, $opts); ?>
        </form>
    </div>

    <?php
    do_perfgraphs_page_end();
}


/**
 * @param $hostname
 */
function draw_host_perfgraph_links($hostname)
{
    echo "<div class='perfgraphlinks'>";
    echo "<div class='perfgraphlink'>
            <a href='" . get_host_status_link($hostname) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View current host status") . "'>
                <img src='" . theme_image("statusdetail.png") . "' alt='" . _("Host status") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_host_notifications_link($hostname) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View host notifications") . "'>
                <img src='" . theme_image("notifications.png") . "' alt='" . _("Host notifications") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_host_history_link($hostname) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View host history") . "'>
                <img src='" . theme_image("history.png") . "' alt='" . _("Host history") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_host_availability_link($hostname) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View host availability") . "'>
                <img src='" . theme_image("availability.png") . "' alt='" . _("Host availability") . "'>
            </a>
        </div>";
    echo "</div>";
}

/**
 * @param $hostname
 * @param $servicename
 */
function draw_service_perfgraph_links($hostname, $servicename)
{
    echo "<div class='perfgraphlinks'>";
    echo "<div class='perfgraphlink'>
            <a href='" . get_service_status_link($hostname, $servicename) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View current service status") . "'>
                <img src='" . theme_image("statusdetail.png") . "' alt='" . _("Service status") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_service_notifications_link($hostname, $servicename) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View service notifications") . "'>
                <img src='" . theme_image("notifications.png") . "' alt='" . _("Service notifications") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_service_history_link($hostname, $servicename) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View service history") . "'>
                <img src='" . theme_image("history.png") . "' alt='" . _("Service history") . "'>
            </a>
        </div>
        <div class='perfgraphlink'>
            <a href='" . get_service_availability_link($hostname, $servicename) . "' class='btn btn-sm btn-default tt-bind' data-placement='left' title='" . _("View service availability") . "'>
                <img src='" . theme_image("availability.png") . "' alt='" . _("Service availability") . "'>
            </a>
        </div>";
    echo "</div>";
}
