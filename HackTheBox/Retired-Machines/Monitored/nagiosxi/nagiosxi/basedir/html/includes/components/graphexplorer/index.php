<?php
//
// Nagios XI Graph Explorer
// Copyright (c) 2011-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');
include_once(dirname(__FILE__) . '/dashlet.inc.php');
require_once(dirname(__FILE__) . '/visFunctions.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab request variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Do the actual page start
do_page_start(array("page_title" => _("Nagios Graph Explorer")), true);

// Get dropdowns
$opts = get_report_timeperiod_options();
unset($opts['last24hours']);
unset($opts['custom']);
$rand = rand();
?>
<script type="text/javascript">
/////////////////js page variables ////////////////////////
var filtering = false;
var host;
var service;
var type;
var filter;
var start;
var end = '';
var minus = true;
var rand = '<?php echo $rand; ?>';
var opt = '';

////////////////////////////////////////////////////////////
// Creating a Dashlet...
////////////////////////////////////////////////////////////

$(document).ready(function () {
    ge_default_page_load();
});

// Do default page load
function ge_default_page_load() {
    var t = 'bar';
    if (window.location.hash != '') {
        t = window.location.hash.substring(1);
    }

    setType(t);

    if (t == 'bar') {
        fetch_bar();
    } else if (t == 'hostpie') {
        fetch_pie("hosthealth");
    } else if (t == 'servicepie') {
        fetch_pie("servicehealth");
    }

    toggle_filter(t);

    $('#tabs li').removeClass('ui-tabs-active').removeClass('ui-state-active');
    $('#'+t).parent().addClass('ui-tabs-active').addClass('ui-state-active');
}

// Do display dashlet created
function ge_display_dashlet_created() {
    content = "<div id='child_popup_header' style='margin-bottom: 5px;'><b>"+_('Dashlet Added')+"</b></div><div id='child_popup_data'><p>"+_('The dashlet has been added and will now show up on your dashboard.')+"</p></div>";
    set_child_popup_content(content);
    display_child_popup();
    fade_child_popup('green');
}

function ge_add_graph_to_dashlet() {
    var board = $('#boardName').val();
    var dashletname = $('#dashletName').val();
    var hiddenurl = $('#hiddenUrl').val();

    // Send request to dashify
    $.post("dashifygraph.php", {url: hiddenurl, dashletName: dashletname, boardName: board, nsp: nsp_str }, function (data) {

        // If it was a success show created message
        if (data.success == 1) {
            ge_display_dashlet_created();
        }

    }, 'json');
}
</script>
<!-- graph explorer JS functions -->
<script type="text/javascript" src="graphexplorer.js"></script>
<style type="text/css">

    ul, li {
        text-indent: 0px;
        margin: 0px;
        padding: 0px;
    }

    ul.childUl {
        padding-left: 5px;
    }

    .parentLi {
        color: #4D89F9;
        list-style: none;
        padding-left: 0px;
        width: 100%;
    }

    .parentLi:hover {
        cursor: pointer;
    }

    .parentLi:before {
        content: url('images/expand1.png');
        display: inline;
    }

    .childLi {
        text-indent: 1px;
        background-color: #DEDEDE;
        margin: 1px;
        padding: 1px 1px 1px 8px;
        list-style: none;
    }

    .childLi:hover {
        cursor: pointer;
    }

    .childLi a {
        width: 100%;
    }

    #leftNav {
        overflow: auto;
    }

    select {
        width: auto;
    }

    p.message {
        text-align: center;
    }

    #startDate, #endDate {
        background-color: #DEDEDE;
    }

    .childcontentthrobber {
        margin: 30px auto;
        height: 300px;
        text-align: center;
    }

    #service_stack {
        margin: 20px 0;
    }

    #service_stack div {
        padding: 5px;
        line-height: 16px;
        background-color: #EEE;
        margin-bottom: 4px;
    }

    #service_stack div img {
        cursor: pointer;
        vertical-align: top;
        padding-right: 6px;
    }

</style>

<h1><?php echo _("Graph Explorer"); ?></h1>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header">
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='bar' href='javascript:setType("bar"); javascript:fetch_bar(); javascript:toggle_filter();' title="Shows the top alert producers for the last 24 hours">
                <?php echo _("Top Alert Producers"); ?>
            </a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='hostpie' href='javascript:setType("hostpie"); javascript:fetch_pie("hosthealth"); javascript:toggle_filter();' title="Shows host health percentage as a pie graph.">
                <?php echo _("Host Health"); ?>
            </a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='servicepie' href='javascript:setType("servicepie"); javascript:fetch_pie("servicehealth"); javascript:toggle_filter();' title="Shows service health percentage as a pie graph.">
                <?php echo _("Service Health"); ?>
            </a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='timeline' href='javascript:setType("timeline"); javascript:toggle_filter("timeline");' title="Shows performance data on a scalable timeline.">
                <?php echo _("Scalable Performance Graph"); ?>
            </a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='stack' href='javascript:setType("stack"); javascript:toggle_filter("stack");' title="Shows performance data with overlapping timeperiods for comparison.">
                <?php echo _("Time Stacked Performance Graph"); ?>
            </a>
        </li>
        <li class="ui-state-default">
            <a class="ui-tabs-anchor" id='multistack' href='javascript:setType("multistack"); javascript:toggle_filter("multistack");' title="Multistacked performance data with overlapping services and/or hosts for comparison.">
                <?php echo _("Multistacked Performance Graph"); ?>
            </a>
        </li>
    </ul>
</div>

    <?php
    $auto_start_date = get_datetime_string(strtotime('yesterday'), DT_SHORT_DATE);
    $auto_end_date = get_datetime_string(strtotime('Today'), DT_SHORT_DATE);
    ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#startdateBox').click(function () {
                $('#timeperiod_select').val('custom');
                if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
                    $('#startdateBox').val('<?php echo $auto_start_date;?>');
                    $('#enddateBox').val('<?php echo $auto_end_date;?>');
                }
            });
            $('#enddateBox').click(function () {
                $('#timeperiod_select').val('custom');
                if ($('#startdateBox').val() == '' && $('#enddateBox').val() == '') {
                    $('#startdateBox').val('<?php echo $auto_start_date;?>');
                    $('#enddateBox').val('<?php echo $auto_end_date;?>');
                }
            });
        });
    </script>

<div class="divtable">
    <div class="divtable-column" id="leftContainer" style="width: 20%; display: none;">

        <!-- Host/Service Selection -->
        <div id="host_service_selection">
            <h5><?php echo _("Graph Objects"); ?></h5>
            <div style="margin-bottom: 6px;">
                <label for='selectHost'><?php echo _("Select Host"); ?></label><br/>
                <select name='selectHost' id='selectHost' style="width: 255px;" class="form-control">
                    <!-- AJAX load all hosts -->
                    <option value=''> &nbsp; </value>
                </select>
            </div>
            <div style="margin-bottom: 6px;">
                <label for='selectService'><?php echo _("Select Service"); ?></label><br/>
                <select name='selectService' id='selectService' style="width: 255px;" class="form-control">
                    <!-- AJAX load services on select -->
                    <option value=''><?php echo _("Select a host..."); ?></option>
                </select>
            </div>
            <div id="selectDataTypeBox" style="margin-bottom: 20px;">
                <label for="selectDataType"><?php echo _("Select Data Type"); ?></label><br/>
                <select id="selectDataType" style="width: 255px;" class="form-control">
                    <!-- AJAX load data types for object or hide -->
                </select>
            </div>
            <div id="service_stack_div">
                <div>
                    <button id="addToGraph" class="btn btn-sm btn-default" type="button"><?php echo _("Add To Graph"); ?></button>
                </div>
                <div id="service_stack">
                    <!-- AJAX populated stack of services -->
                </div>
            </div>
            <h5><?php echo _("Filtering Options"); ?></h5>
            <div id="dateFilterTimeline">
                <label><?php echo _("Time Period"); ?></label><br/>
                <select id="reportperiodDropdown" name="timeperiod" style="width: 140px; margin-bottom: 5px;" class="form-control">
                    <option value="-4h"><?php echo _("Last 4 Hours"); ?></option>
                    <option value="-24h"><?php echo _("Last 24 Hours"); ?></option>
                    <option value="-48h"><?php echo _("Last 48 Hours"); ?></option>
                    <option value="-1w"><?php echo _("Last 7 Days"); ?></option>
                    <option value="-1m"><?php echo _("Last 30 Days"); ?></option>
                    <option value="-1y"><?php echo _("Last 365 Days"); ?></option>
                    <?php
                    foreach ($opts as $shortname => $longname) {
                        echo "<option value='" . $shortname . "'>" . $longname . "</option>";
                    }
                    ?>
                    <option value="custom"><?php echo _("Custom"); ?></option>
                </select>
            </div>
            <div id="customdates" class="cal hide">
                <div style="margin-bottom: 5px;">
                    <span style="width: 32px; display: inline-block;"><?php echo _("From"); ?></span>
                    <input class="textfield form-control" type="text" id='startdateBox' name="startdate" value="" size="16">
                    <div id="startdatepickercontainer"></div>
                    <div class="reportstartdatepicker"><i class="fa fa-calendar fa-cal-btn"></i></div>
                </div>
                <div>
                    <span style="width: 32px; display: inline-block;"><?php echo _("To"); ?></span>
                    <input class="textfield form-control" type="text" id='enddateBox' name="enddate" value="" size="16">
                    <div id="enddatepickercontainer"></div>
                    <div class="reportenddatepicker"><i class="fa fa-calendar fa-cal-btn"></i></div>
                </div>
            </div>
        </div>

        <!-- Filter Options -->
        <div id="filterDiv">
            <div id='dateFilterStack'>
                <label for='timeStackOpt'><?php echo _("Select Time Frame"); ?></label><br/>
                <select name='timeStackOpt' id='timeStackOpt' style="width: 150px;" class="form-control">
                    <option value='days'> <?php echo _("Last 3 Days"); ?> </option>
                    <option value='weeks'> <?php echo _("Last 3 Weeks"); ?> </option>
                    <option value='months'> <?php echo _("Last 3 Months"); ?> </option>
                </select>
            </div>
        </div>

        <div id="graphOptions">
            <h5><?php echo _("Graph Options"); ?></h5>
            <div>
                <label for="linetype"><?php echo _("Line Type"); ?></label><br/>
                <select name="linetype" id="linetype" style="width: auto; max-width: 320px;" class="form-control">
                    <option value="stacked"<?php if (get_highcharts_default_type() == "stacked") echo " selected"; ?>><?php echo _("Area (Stacked)"); ?></option>
                    <option value="area"<?php if (get_highcharts_default_type() == "area") echo " selected"; ?>><?php echo _("Area"); ?></option>
                    <option value="line"<?php if (get_highcharts_default_type() == "line") echo " selected"; ?>><?php echo _("Line"); ?></option>
                    <option value="spline"<?php if (get_highcharts_default_type() == "spline") echo " selected"; ?>><?php echo _("Spline"); ?></option>
                </select>
            </div>
        </div>

        <div style="margin-top: 15px;">
            <button id='filterButton' class="btn btn-sm btn-primary" type="button"><?php echo _("Update Graph"); ?></button>
        </div>

    </div>
    <!-- end leftContainer -->

    <div class="divtable-column" style="padding: 0 3px;">

        <div class="well" style="margin: 20px 0;">

            <div id="graphText" style="display: none; padding: 40px 0; text-align: center;">
                <?php echo _("Select services to stack on the left. Less services and similar services makes a better, more readable graph. Filter by the desired time."); ?>
                <br/>
                <br/>
                <strong><?php echo _("Only hosts/services with performance data will show up on the lists."); ?></strong>

                <div id="test" style="padding-top: 20px;"></div>
            </div>

            <div id="graphDisplay">
                <div class="dashifybutton2">
                    <a class="dashifybutton2 tt-bind" id="dashify2" href="#" title="<?php echo _('Add to Dashboard'); ?>"><i class="fa fa-14 fa-sign-in fa-rotate-270"></i></a>
                </div>

                <!-- attempting to pass args for dashlet creation -->
                <input type="hidden" id="hiddenUrl" name="url" value="">
                <input type="hidden" id="dashletName" name="dashletName" value="">
                <input type="hidden" id="boardName" name="boardName" value="">

                <div class='viewer' id="visContainer<?php echo $rand; ?>">
                    <!-- javascript magic here -->
                    <div class="childcontentthrobber" id='childcontentthrobber'>
                        <div class="sk-spinner sk-spinner-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>