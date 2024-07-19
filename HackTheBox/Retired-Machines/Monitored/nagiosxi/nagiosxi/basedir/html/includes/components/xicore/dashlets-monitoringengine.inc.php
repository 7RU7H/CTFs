<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// CORE MONITORING ENGINE DASHLETS
////////////////////////////////////////////////////////////////////////


/**
 * System status page event queue chart HTML and JS
 *
 * @param 	string 	$mode
 * @param 	string 	$id
 * @param 	array 	$args
 * @return 	string
 */
function xicore_dashlet_eventqueue_chart($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $id = "sysstat_eventqueuechart_" . random_string(6);

            $stylewidth = "";
            if ($mode == DASHLET_MODE_INBOARD) {
                $stylewidth = "width: 100%; height: 100%;";
            } else {
            	$stylewidth = "width: 300px; height: 170px;";
            }

            $output = '
	<div class="sysstat_eventqueuechart">

	<div class="infotable_title"><a href="' . get_component_url_base('xicore') . '/queue.php">' . _('Monitoring Engine Event Queue') . '</a></div>
	<table class="infotable table table-condensed table-striped table-bordered" style="' . $stylewidth . '">
	<thead>
	<tr><th><div>' . _('Scheduled Events Over Time') . '</div></th></tr>
	</thead>
	<tbody>
	<tr><td>
	<div style="padding: 5px; ' . $stylewidth . '">

	<div id="throbber_' . $id . '">' . get_throbber_html() . '</div>
	
	<div style="height: 160px;" id="' . $id . '"></div>

	</div>
	</td></tr>
	</tbody>
	</table>
	
	</div>

	<script type="text/javascript">
	$(document).ready(function() {
		get_' . $id . '_content();
		$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(5, "systat_eventqueuechart") . ', "timer-' . $id . '", function(i) {
			get_' . $id . '_content();
		});
	});
	
	function delete_' . $id . '_throbber() {
		$("#throbber_' . $id . '").each(function(){
			$(this).remove();  // remove the throbber if it exists
		});
	}
		
	function get_' . $id . '_content() {
		$("#' . $id . '").each(function() {
			var optsarr = {
				"func": "get_eventqueue_chart_html",
				"args": { "container": "' . $id . '" }
				}
			var opts=JSON.stringify(optsarr);
			get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
			delete_' . $id . '_throbber();
		});
	}
	</script>
			';
            break;
        case DASHLET_MODE_PREVIEW:
            $imgurl = get_component_url_base('xicore') . "/images/dashlets/eventqueue_chart_preview.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * System status page monitoring status dashlet HTML
 *
 * @param 	string 	$mode
 * @param 	string 	$id
 * @param 	array  	$args
 * @return 	string
 */
function xicore_dashlet_monitoring_stats($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
	$output = "";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:
            $id = "sysstat_monitoringstats_" . random_string(6);

            $output = '
	<div class="sysstat_monitoringstats" id="' . $id . '">

	<div class="infotable_title">' . _('Monitoring Engine Check Statistics') . '</div>
	' . get_throbber_html() . '
			
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "sysstat_monitoringstats") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_monitoring_stats_html",
							"args": ""
							}
						var opts=JSON.stringify(optsarr);
						get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
						});
					}
		
	});
	</script>
			';
            break;
        case DASHLET_MODE_PREVIEW:
            $imgurl = get_component_url_base() . "xicore/images/dashlets/monitoring_stats_preview.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * @param 	string 	$mode
 * @param 	string 	$id
 * @param  	array 	$args
 * @return 	string
 */
function xicore_dashlet_monitoring_perf($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:
            $id = "sysstat_monitoringperf_" . random_string(6);

            $output = '
	<div class="sysstat_monitoringperf" id="' . $id . '">
	
	<div class="infotable_title">' . _('Monitoring Engine Performance') . '</div>
	' . get_throbber_html() . '
			
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "systat_monitoringperf") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_monitoring_perf_html",
							"args": ""
							}
						var opts=JSON.stringify(optsarr);
						get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
						});
					}

	});
	</script>
			';
            break;
        case DASHLET_MODE_PREVIEW:
            $imgurl = get_component_url_base() . "xicore/images/dashlets/monitoring_perf_preview.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * @param 	string 	$mode
 * @param 	string 	$id
 * @param 	array  	$args
 * @return 	string
 */
function xicore_dashlet_monitoring_process($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $id = "sysstat_monitoringproc_" . random_string(6);

            $output = '
	<div class="sysstat_monitoringproc" id="' . $id . '">

	<div class="infotable_title">' . _('Monitoring Engine Process') . '</div>
	' . get_throbber_html() . '
			
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
	
		get_' . $id . '_content();
		init_timer_' . $id . '();
			
		});
	
	function init_timer_' . $id . '(){
		$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "sysstat_monitoringproc") . ', "timer-' . $id . '", function(i) {
			get_' . $id . '_content();
			});
		}

	function get_' . $id . '_content(){
		$("#' . $id . '").each(function(){
			var optsarr = {
				"func": "get_monitoring_proc_html",
				"args": ""
				}
			var opts=JSON.stringify(optsarr);
			//get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
		get_ajax_data_innerHTML_with_callback("getxicoreajax",opts,true,this,"init_component_dropdowns_' . $id . '");
			});
		}

	function init_component_dropdowns_' . $id . '(){
		$(".sysstate_monitoringproc_image img.actionimage").each(function(){

		// handle action clicks!
			$(this).click(function(){
				
				
				// hide or show this dropdown
				var p=this.parentNode;
				$(p).children("ul").each(function(){
				
					var theone=this;
				
					// hide all other hidden dropdowns (another one might be showing)
					$(".hiddendropdown").each(function(){
						if(this!=theone)
							$(this).css("visibility","hidden");
						});

					var cv=$(this).css("visibility");
					if(cv=="hidden"){
					
						// show the menu
						$(this).css("visibility","visible");
						
						// stop the timer
						$("#' . $id . '").stopTime("timer-' . $id . '");
						
						// menu should disappear on hover-out
						$(this).hover(
							function(){
								},
							function(){
							
								// hide the menu
								$(this).css("visibility","hidden");
								
								// restart timer
								init_timer_' . $id . '();
								}
							);
						}
					else{
					
						// hide the menu
						$(this).css("visibility","hidden");
						
						// restart timer
						init_timer_' . $id . '();
						}
					});
				});
			});
		}

	</script>
			';
            break;
        case DASHLET_MODE_PREVIEW:
            $imgurl = get_component_url_base() . "xicore/images/dashlets/monitoring_proc_preview.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}
