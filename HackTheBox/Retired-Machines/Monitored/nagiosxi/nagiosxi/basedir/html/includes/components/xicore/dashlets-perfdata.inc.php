<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// CORE PERFDATA DASHLETS
////////////////////////////////////////////////////////////////////////


/**
 * Display a performance graph dashlet HTML
 *
 * @param   string  $mode
 * @param   string  $id
 * @param   array   $args
 * @return  string
 */
function xicore_dashlet_perfdata_chart($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";
    
    if (empty($args)) {
        $args = array();
    }

    switch ($mode)
    {   
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';

            if (get_option("perfdata_theme", 1) == 1) {
                $output .= '
                <div style="padding: 5px;">
                    <label>
                        <input type="checkbox" name="no_legend" value="1">
                        '._("Do not display graph legend in dashboard").'
                    </label>
                </div>';
            }

            break;

        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $host_id = grab_array_var($args,"host_id",-1);
            $service_id = grab_array_var($args,"service_id",-1);
        
            // Check authentication to view this object
            if ($service_id > 0) {
                $auth = is_authorized_for_object_id(0, $service_id);
            } else {
                $auth = is_authorized_for_object_id(0, $host_id);
            }

            // If unauthorized return error
            if ($auth == false) {
                return _("You are not authorized to access this feature.  Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
                break;
            }

            // If we can use this dashlet, display based on dashlet version
            if (get_option("perfdata_theme", 1) == 0 || grab_array_var($args, "old", 0) == 1) {
                $output = xicore_dashlet_perfdata_get_rrdtool($id, $args);
            } else {
                $output = xicore_dashlet_perfdata_get_highcharts($id, $args, $mode);
            }
            
            break;
            
        case DASHLET_MODE_PREVIEW:
            $imgbase = get_base_url() . "includes/components/graphexplorer/images/";
            $output = "<p><img src='" . $imgbase . "timeline.png'></p>";
            break;
    }

    return $output;
}


/**
 * @param   string  $hostname
 * @param   string  $servicename
 * @param   string  $source
 * @param   string  $view
 * @param   string  $start
 * @param   string  $end
 * @param   string  $startdate
 * @param   string  $enddate
 * @param   int     $mode
 * @param   int     $host_id
 * @param   int     $service_id
 * @return  string
 */
function xicore_dashlet_perfdata_chart_get_chart_url($hostname = "", $servicename = "_HOST_", $source = "", $view = "", $start = "", $end = "", $startdate = "", $enddate = "", $mode = PERFGRAPH_MODE_HOSTSOVERVIEW, $host_id = -1, $service_id = -1)
{
    $url = get_base_url()."perfgraphs/?";
    
    $url.="&host=".urlencode($hostname);
    $url.="&service=".urlencode($servicename);
    $url.="&source=$source";
    if (empty($start)) {
        $url.="&view=$view";
    }
    $url.="&start=$start";
    $url.="&end=$end";
    $url.="&startdate=$startdate";
    $url.="&enddate=$enddate";
    $url.="&mode=$mode";

    if ($host_id > 0) {
        $url .= "&host_id=$host_id";
    }
    if ($service_id > 0) {
        $url .= "&service_id=$service_id";
    }

    return $url;
}


//
//  Split up the RRD version and the Highcharts version into their own functions
//


/**
 * @param $id
 * @param $args
 * @return string
 */
function xicore_dashlet_perfdata_get_rrdtool($id, $args)
{
    $id = "perfgraph_".random_string(6);

    $output = '<div class="perfgraph" id="'.$id.'">';

    $hostname = grab_array_var($args, "hostname","");
    $host_id = grab_array_var($args, "host_id", -1);
    $servicename = grab_array_var($args, "servicename", "");
    $service_id = grab_array_var($args, "service_id", -1);
    $source = grab_array_var($args, "source", "");
    $sourcename = grab_array_var($args, "sourcename", "");
    $sourcetemplate = grab_array_var($args, "sourcetemplate", "");
    $view = grab_array_var($args, "view", "");
    $start = grab_array_var($args, "start", "");
    $end = grab_array_var($args, "end", "");
    $startdate = grab_array_var($args, "startdate", "");
    $enddate = grab_array_var($args, "enddate", "");
    $mode = grab_array_var($args, "mode", "");

    $title = "";
    $imagetitle = "";
    $imgurla = "";
    $imgurlb = "";

    switch ($mode) {

        case PERFGRAPH_MODE_HOSTSOVERVIEW:
            $title = $hostname." "._("Host Performance Graph");
            $imagetitle = _("View All")." ".encode_form_val($hostname)." "._("Performance Graphs");
            $url = xicore_dashlet_perfdata_chart_get_chart_url($hostname, $servicename, $source, $view, $start, $end, $startdate, $enddate, PERFGRAPH_MODE_HOSTOVERVIEW, $host_id, $service_id);
            $imgurla = "<a href='".$url."'>";
            $imgurlb = "</a>";
        break;

        case PERFGRAPH_MODE_HOSTOVERVIEW:
            if ($servicename == "_HOST_") {
                $title = _("Host Performance");
            } else {
                $title = $servicename;
            }
            $imagetitle = _("View Detailed")." ".encode_form_val($servicename)." "._("Performance Graphs");
            $url = xicore_dashlet_perfdata_chart_get_chart_url($hostname, $servicename, $source, $view, $start, $end, $startdate, $enddate, PERFGRAPH_MODE_SERVICEDETAIL, $host_id, $service_id);
            $imgurla = "<a href='".$url."'>";
            $imgurlb = "</a>";
            break;

        case PERFGRAPH_MODE_GOTOSERVICEDETAIL:
            $title = _("Datasource").": ".perfdata_get_friendly_source_name($sourcename, $sourcetemplate);
            $url = xicore_dashlet_perfdata_chart_get_chart_url($hostname, $servicename, $source, $view, $start, $end, $startdate, $enddate, PERFGRAPH_MODE_SERVICEDETAIL, $host_id, $service_id);
            $imgurla = "<a href='".$url."'>";
            $imgurlb = "</a>";
            break;

        case PERFGRAPH_MODE_SERVICEDETAIL:
            if (!empty($sourcename)) {
                $title = _("Datasource").": ".perfdata_get_friendly_source_name($sourcename, $sourcetemplate);
            }
            break;

        default:
            break;

    }

    $id = "perfdata_chart_".random_string(6);

    $output .= "<div class='perfgraphtitle'>".$title."</div>\n";
    $output .= "<div id='throbber_".$id."'>".get_throbber_html()."</div>";

    $divwidth = "";
    if ($mode == DASHLET_MODE_INBOARD) {
        $divwidth = "width: 100%;";
    }
    $output .= "<div style='".$divwidth."'>&nbsp;";
    $output .= $imgurla;
    $output .= "<img src='";

    $imgwidth = "";
    if ($mode == DASHLET_MODE_INBOARD) {
        $imgwidth = "width='100%'";
    }

    $output .= "' title='".$imagetitle."' id='".$id."' ".$imgwidth.">";
    $output .= $imgurlb;

    $output .= "</div>";
    $output .= '</div>';

    // Build args for javascript
    $n = 0;
    $jargs = "{";
    foreach ($args as $var => $val) {
        if ($n > 0) {
            $jargs .= ", ";
        }
        $jargs .= "\"".encode_form_val($var)."\" : \"".encode_form_val($val)."\"";
        $n++;
    }
    $jargs .= "}";
            
    $output.='
    <script type="text/javascript">
    $(document).ready(function(){
            
        get_'.$id.'_content();
        
        $("#'.$id.'").everyTime('.get_dashlet_refresh_rate(60,"perfdata_chart").', "timer-'.$id.'", function(i) {
            get_'.$id.'_content();
        });
                
    });
        
    function delete_'.$id.'_throbber(){
        $("#throbber_'.$id.'").each(function(){
            $(this).remove();  // remove the throbber if it exists
        });
    }
            
    function get_'.$id.'_content(){
        $("#'.$id.'").each(function(){
            var optsarr = {
                "func": "get_perfdata_chart_html",
                "args": '.$jargs.'
                }
            var opts=JSON.stringify(optsarr);
            //get_ajax_data_imagesrc("getxicoreajax",opts,true,this);
            get_ajax_data_imagesrc_with_callback("getxicoreajax",opts,true,this,"delete_'.$id.'_throbber");
        });
    }       
    </script>';

    return $output;
}


/**
 * @param $id
 * @param $args
 * @param $mode
 * @return string
 */
function xicore_dashlet_perfdata_get_highcharts($id, $args, $mode)
{
    // Process args
    $hostname = grab_array_var($args, "hostname", "");
    $servicename= grab_array_var($args, "servicename", "");
    $startdate = grab_array_var($args, "startdate", "");
    $enddate = grab_array_var($args, "enddate", "");
    $start = grab_array_var($args, "start", "");
    $view = grab_array_var($args, "view", -1);
    $render_mode = grab_array_var($args, "render_mode", "");
    $no_legend = grab_array_var($args, "no_legend", 0);
    $link_mode = intval(grab_array_var($args, "link_mode", 0));

    if (!empty($hostname)) {
        $link_mode = 1;
    }

    // Convert startdates to time
    if (empty($start)) {
        $start = nstrtotime($startdate);
    }
    $end = nstrtotime($enddate);

    // Variables required
    $div_id = "perfgraph_hc_".random_string(6);
    $comp_url = get_base_url() . 'includes/components/graphexplorer/';

    // Next page link
    $link = base64_encode(get_base_url() . 'perfgraphs/index.php?search=&host=' . urlencode($hostname) . '&startdate=' . $startdate . '&enddate=' . $enddate . '&mode=' . $link_mode);

    if ($view < 0) { 
        $link .= '&view=' . $view;
    }

    // Generate a URL for hc
    $url = $comp_url . "visApi.php?type=perfdata&host=" . urlencode($hostname) . "&service=" . urlencode($servicename) . "&div=" . $div_id . "&filter=&subtitle=0&view=" . $view . "&link=" . $link . "&render_mode=" . $render_mode."&no_legend=" . $no_legend;

    if (!empty($start)) {
        $url .= "&start=" . $start;
    }
    if (!empty($end)) {
        $url .= "&end=" . $end;
    }

    // Actual output of the dashlet
    $output = '';
    $output .= '<div class="perfdata_dashlet" id="'.$div_id.'"></div>
    <script type="text/javascript">
    $(document).ready(function() {';

    $output .= '
        resize_'.$div_id.'();
        get_'.$div_id.'_content();

        var hc = $("#'.$div_id.'").find(".highcharts-container");
        
        // Re-build the content when we resize
        $("#'.$div_id.'").closest(".ui-resizable").on("resizestop", function(e, ui) {
            var height = ui.size.height;
            var width = ui.size.width;
            get_'.$div_id.'_content(height, width);
        });

        var resize_timer_'.$div_id.';
        $(window).resize(function(e) {
            clearTimeout(resize_timer_'.$div_id.');
            resize_timer_'.$div_id.' = setTimeout(function() {
                resize_'.$div_id.'();
                get_'.$div_id.'_content();
            }, 300);
        });

        // Auto-update every x amount of time
        $("#'.$div_id.'").everyTime('.get_dashlet_refresh_rate(60,"perfdata_chart").', "timer-'.$div_id.'", function(i) {
            get_'.$div_id.'_content();
        });
    });

    function resize_'.$div_id.'() {';

        if ($mode == DASHLET_MODE_OUTBOARD) {
        $output .= '// Grab the width/height and set it before we actually create the perfdata dashlet
                    var total_width = $("#'.$div_id.'").parents(".pd-container").width();
                    total_px = total_width - $("#'.$div_id.'").parents(".pd-container").find(".perfgraphlinks").outerWidth();
                    total_height_px = 340;
                    if (total_px < 500) { total_px = 500; total_height_px = 225; }
                    $("#'.$div_id.'").parents(".dashlettable").css({ width: total_px, height: total_height_px });
                    $("#'.$div_id.'").css({ width: total_px - 16, height: total_height_px - 32 });';
        }

    $output .= '}

    function get_'.$div_id.'_content(height, width) {
        if (height == undefined) { var height = $("#'.$div_id.'").parent().height(); }';

    // Check if we are outboard or not
    if ($mode == DASHLET_MODE_INBOARD) {
        $output .= 'height = height - 17;';
    }

    $output.='if (width == undefined) { var width = $("#'.$div_id.'").parent().width(); }

        $("#'.$div_id.'").load("'.$url.'&height="+height+"&width="+width);
                
        // Stop clicking in graph from moving dashlet
        $("#'.$div_id.'").closest(".ui-draggable").draggable({ cancel: "#'.$div_id.'" });
    }
    </script>';
            
    return $output;
}

