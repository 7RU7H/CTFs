<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// STATUS DASHLETS
////////////////////////////////////////////////////////////////////////


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_network_outages($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $id = "network_outages_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="network_outages_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . _('Network Outages') . '</div>
			' . get_throbber_html() . '
			
			</div><!--network_outages_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "network_outages") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_network_outages_html",
							"args": ' . $jargs . '
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/network_outages_preview.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
        default:
            break;
    }
    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function xicore_dashlet_host_status_summary($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            // Get min/max heights and widths... remember to -2 for borders
            $min_height = 160-2;
            $min_width = 260-2;
            $max_height = 160-2;
            $max_width = 0;

            $output = "";
            $hide_title = "";

            $id = "host_status_summary_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="host_status_summary_dashlet size-info" data-min-height="'.$min_height.'" data-max-height="'.$max_height.'" data-min-width="'.$min_width.'" data-max-width="'.$max_width.'" id="' . $id . '">
			
			' . get_throbber_html() . '
			
			</div><!--ahost_status_summary_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "host_status_summary") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_host_status_summary_html",
							"args": ' . $jargs . '
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/host_status_summary.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_service_status_summary($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            // Get min/max heights and widths... remember to -2 for borders
            $min_height = 160-2;
            $min_width = 300-2;
            $max_height = 160-2;
            $max_width = 0;

            $output = "";

            $id = "host_status_summary_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="service_status_summary_dashlet size-info" data-min-height="'.$min_height.'" data-max-height="'.$max_height.'" data-min-width="'.$min_width.'" data-max-width="'.$max_width.'" id="' . $id . '">
			
			<div class="infotable_title">' . _('Service Status Summary') . '</div>
			' . get_throbber_html() . '
			
			</div><!--service_status_summary_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "service_status_summary") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_service_status_summary_html",
							"args": ' . $jargs . '
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/service_status_summary.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_hostgroup_status_overview($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $hostgroup = grab_array_var($args, "hostgroup");
            $hostgroup_alias = grab_array_var($args, "hostgroup_alias");

            $output = "";

            $id = "hostgroup_status_overview_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="hostgroup_status_overview_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . encode_form_val($hostgroup_alias) . ' (' . encode_form_val($hostgroup) . ')</div>

			' . get_throbber_html() . '
			
			</div><!--hostgroup_status_overview_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "hostgroup_status_overview") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_hostgroup_status_overview_html",
							"args": ' . $jargs . '
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
            $output = '
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_hostgroup_status_grid($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $hostgroup = grab_array_var($args, "hostgroup");
            $hostgroup_alias = grab_array_var($args, "hostgroup_alias");

            $output = "";

            $id = "hostgroup_status_grid_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="hostgroup_status_grid_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . encode_form_val($hostgroup_alias) . ' (' . encode_form_val($hostgroup) . ')</div>
	
			' . get_throbber_html() . '
			
			</div><!--hostgroup_status_grid_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "hostgroup_status_grid") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_hostgroup_status_grid_html",
							"args": ' . $jargs . '
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
            $output = '
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_servicegroup_status_overview($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $servicegroup = grab_array_var($args, "servicegroup");
            $servicegroup_alias = grab_array_var($args, "servicegroup_alias");

            $output = "";

            $id = "servicegroup_status_overview_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="servicegroup_status_overview_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . encode_form_val($servicegroup_alias) . ' (' . encode_form_val($servicegroup) . ')</div>
	
			' . get_throbber_html() . '
			
			</div><!--servicegroup_status_overview_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "servicegroup_status_overview") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_servicegroup_status_overview_html",
							"args": ' . $jargs . '
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
            $output = '
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_servicegroup_status_grid($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $servicegroup = grab_array_var($args, "servicegroup");
            $servicegroup_alias = grab_array_var($args, "servicegroup_alias");

            $output = "";

            $id = "servicegroup_status_grid_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="servicegroup_status_grid_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . encode_form_val($servicegroup_alias) . ' (' . encode_form_val($servicegroup) . ')</div>

			' . get_throbber_html() . '
			
			</div><!--servicegroup_status_grid_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "servicegroup_status_grid") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_servicegroup_status_grid_html",
							"args": ' . $jargs . '
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
            $output = '
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function xicore_dashlet_hostgroup_status_summary($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $output = "";

            $id = "hostgroup_status_summary_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="hostgroup_status_summary_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . _('Status Summary For All Host Groups') . '</div>

			' . get_throbber_html() . '
			
			</div><!--hostgroup_status_summary_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "hostgroup_status_summary") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_hostgroup_status_summary_html",
							"args": ' . $jargs . '
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/hostgroup_status_summary.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 * @return string
 */
function xicore_dashlet_servicegroup_status_summary($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if (empty($args)) {
        $args = array();
    }

    $args['mode'] = $mode;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $output = "";

            $id = "servicegroup_status_summary_" . random_string(6);

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"" . encode_form_val($var) . "\" : \"" . encode_form_val($val) . "\"";
                $n++;
            }
            $jargs .= "}";

            $output .= '
			<div class="servicegroup_status_summary_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . _('Status Summary For All Service Groups') . '</div>

			' . get_throbber_html() . '
			
			</div><!--servicegroup_status_summary_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "servicegroup_status_summary") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_servicegroup_status_summary_html",
							"args": ' . $jargs . '
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/servicegroup_status_summary.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}
