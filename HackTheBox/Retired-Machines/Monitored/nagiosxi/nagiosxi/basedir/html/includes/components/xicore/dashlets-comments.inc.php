<?php
//
// XI Core Dashlet Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// COMMENT DASHLETS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $mode
 * @param   string  $id
 * @param   array   $args
 * @return  string
 */
function xicore_dashlet_comments($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";

    if ($args == null) {
        $args = array();
    }

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $output = "";
            $id = "comments_" . random_string(6);
            $ajaxargs = $args;

            // Build ajax json string
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
			<div class="comments_dashlet" id="' . $id . '">
			
			<div class="infotable_title">' . _('Acknowledgements and Comments') . '</div>
			' . get_throbber_html() . '
			
			</div><!--comment_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
			
				get_' . $id . '_content();
					
				$("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "comments_dashlet") . ', "timer-' . $id . '", function(i) {
					get_' . $id . '_content();
				});
				
				function get_' . $id . '_content(){
					$("#' . $id . '").each(function(){
						var optsarr = {
							"func": "get_comments_html",
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
            $imgurl = get_component_url_base() . "xicore/images/dashlets/comments.png";
            $output = '
			<img src="' . $imgurl . '">
			';
            break;
    }

    return $output;
}
