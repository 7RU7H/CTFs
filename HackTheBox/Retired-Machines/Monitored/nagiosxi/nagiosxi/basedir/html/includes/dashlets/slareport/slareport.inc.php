<?php
//
// SLA Report Dashlet
// Copyright (c) 2015-2017 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');

$sla_report_name = "sla";

// Register the dashlet
$args = array();
$args[DASHLET_NAME] = "sla";
$args[DASHLET_TITLE] = "SLA Report";
$args[DASHLET_FUNCTION] = "sla_dashlet_func";
$args[DASHLET_DESCRIPTION] = "Displays SLA Report.";
$args[DASHLET_WIDTH] = "350";
$args[DASHLET_HEIGHT] = "450";
$args[DASHLET_INBOARD_CLASS] = "sla_report_inboard";
$args[DASHLET_OUTBOARD_CLASS] = "sla_report_outboard";
$args[DASHLET_CLASS] = "sla_report";
$args[DASHLET_AUTHOR] = "Nagios Enterprises, LLC";
$args[DASHLET_COPYRIGHT] = "Dashlet Copyright &copy; 2015-2017 Nagios Enterprises. All rights reserved.";
$args[DASHLET_HOMEPAGE] = "https://www.nagios.com";
$args[DASHLET_SHOWASAVAILABLE] = false;
register_dashlet($args[DASHLET_NAME], $args);

function sla_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";
    $imgbase = get_base_url() . "";

    switch ($mode) {

        case DASHLET_MODE_GETCONFIGHTML:
            break;

        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            //$metric=grab_array_var($args,"metric","");
            //$url = grab_array_var($args, 'url', '');
            $url = "reports/sla.php";
            $dashtype = grab_array_var($args, 'dashtype', '');

            // set dash options
            $url .= "?mode=getreport&dashify=1&dashtype=" . $dashtype;

            $divId = uniqid();

            // Check to make sure it's not using static hardcoded IPs
            if (strpos($url, "://") === FALSE) {
                $url = get_base_url() . $url;
            }            

            // SLA args            
            $host = grab_array_var($args, 'host', '');
            $service = grab_array_var($args, 'service', '');
            $hostgroup = grab_array_var($args, 'hostgroup', '');
            $servicegroup = grab_array_var($args, 'servicegroup', '');
            $reportperiod = grab_array_var($args, 'reportperiod', 'last24hours');
            $startdate = grab_array_var($args, 'startdate', '');
            $enddate = grab_array_var($args, 'enddate', '');
            $starttime = grab_array_var($args, 'starttime', 0);
            $endtime = grab_array_var($args, 'endtime', 0);
            $slalevel = grab_array_var($args, 'slalevel', 95);
            $dont_count_downtime = grab_array_var($args, 'dont_count_downtime', 0);
            $showonlygraphs = grab_array_var($args, 'showonlygraphs', '');
            $timeperiod = grab_array_var($args, 'timeperiod', '');

            // add args to URL that will be displayed in dashlet
            foreach ($args as $key => $val) {
                $url .= "&" . $key . "=" .  urlencode($val);
            }

            $output .= '

            <div class="sla_dashlet" id="' . $divId . '">
            
            <div class="infotable_title">SLA Report</div>
            ' . get_throbber_html() . '
            
            </div><!--ahost_status_summary_dashlet-->

            <!-- end  dashlet -->
            <script type="text/javascript">
            $(document).ready(function(){           
                get_' . $divId . '_content();
                
                // Re-build the content when we resize
                $("#' . $divId . '").closest(".ui-resizable").on("resizestop", function(e, ui) {
                    var height = ui.size.height - 17;
                    var width = ui.size.width;
                    get_' . $divId . '_content(height, width);
                });

                // Auto-update every x amount of time
                setInterval(get_' . $divId . '_content, 300*1000);
            });
            
            function get_' . $divId . '_content(height, width)
            {
                if (height == undefined) { var height = $("#' . $divId . '").parent().height() - 17; }
                if (width == undefined) { var width = $("#' . $divId . '").parent().width(); }
                    $("#' . $divId . '").load("' . $url . '");
            }
            </script>';

            break;

        case DASHLET_MODE_PREVIEW:
            break;
    }

    return $output;
}