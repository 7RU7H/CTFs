<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// TASK DASHLETS
////////////////////////////////////////////////////////////////////////


/**
 * @param string $mode
 * @param string $id
 * @param array  $args
 * @return string
 */
function xicore_dashlet_admin_tasks($mode = DASHLET_MODE_PREVIEW, $id = "", $args = array())
{
    $output = "";

    // Get min/max heights and widths... remember to -2 for borders
    $min_height = 200-2;
    $min_width = 290-2;
    $max_height = 200-2;
    $max_width = 290-2;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $id = "admin_task_" . random_string(6);

            $output = '
            <div class="admin_task_dashlet size-info" data-min-height="'.$min_height.'" data-max-height="'.$max_height.'" data-min-width="'.$min_width.'" data-max-width="'.$max_width.'" id="' . $id . '">

            <div class="infotable_title">' . _('Administrative Tasks') . '</div>
            ' . get_throbber_html() . '
            
            </div><!--admin_task_dashlet-->

            <script type="text/javascript">
            $(document).ready(function(){

                get_' . $id . '_content();
                    
                $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "admin_tasks") . ', "timer-' . $id . '", function(i) {
                    get_' . $id . '_content();
                });
                
                function get_' . $id . '_content(){
                    $("#' . $id . '").each(function(){
                        var optsarr = {
                            "func": "get_admin_tasks_html",
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/admin_tasks_preview.png";
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
 * @param array  $args
 * @return string
 */
function xicore_dashlet_getting_started($mode = DASHLET_MODE_PREVIEW, $id = "", $args = array())
{
    $output = "";

    // Get min/max heights and widths... remember to -2 for borders
    $min_height = 340-2;
    $min_width = 400-2;
    $max_height = 340-2;
    $max_width = 400-2;

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $id = "getting_started_" . random_string(6);

            $output = '
            <div class="getting_started_dashlet size-info" data-min-height="'.$min_height.'" data-max-height="'.$max_height.'" data-min-width="'.$min_width.'" data-max-width="'.$max_width.'" id="' . $id . '">
                <div class="infotable_title">' . _('Getting Started Guide') . '</div>
                ' . get_throbber_html() . '
            </div><!--getting_started_dashlet-->

            <script type="text/javascript">
            $(document).ready(function(){

                get_' . $id . '_content();
                    
                $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(60, "getting_started") . ', "timer-' . $id . '", function(i) {
                    get_' . $id . '_content();
                });
                
                function get_' . $id . '_content(){
                    $("#' . $id . '").each(function(){
                        var optsarr = {
                            "func": "get_getting_started_html",
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/getting_started_preview.png";
            $output = '
            <img src="' . $imgurl . '">
            ';
            break;
    }

    return $output;
}
