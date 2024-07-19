<?php
//
// Graph Explorer Dashlet
// Copyright (c) 2010-2019 Nagios Enterprises, LLC.  All rights reserved.
//

include_once(dirname(__FILE__) . '/../../dashlets/dashlethelper.inc.php');


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function graphexplorer_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $imgbase = get_base_url() . "includes/components/graphexplorer/images/";

    //$metric=grab_array_var($args,"metric","");
    $url = grab_array_var($args, 'url', '');
    $divId = grab_array_var($args, 'divId', '');

    // Check to make sure it's not using static hardcoded IPs
    if (strpos($url, "://") === FALSE) {
        $url = get_base_url() . $url;
    }

    switch ($mode) {

        case DASHLET_MODE_GETCONFIGHTML:
            break;

        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $output = '';
            $output .= '<div class="graphexplorer_dashlet" id="' . $divId . '"></div>
            
			<script type="text/javascript">
			$(document).ready(function() {
				get_' . $divId . '_content();
				
				var hc = $("#' . $divId . '").find(".highcharts-container");
				
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

				$("#' . $divId . '").load("' . $url . '&height="+height+"&width="+width);
				
				// Stop clicking in graph from moving dashlet
				$("#' . $divId . '").closest(".ui-draggable").draggable({ cancel: "#' . $divId . '" });
			}
			</script>
			';

            break;

        case DASHLET_MODE_PREVIEW:
            $output = "<p><img src='" . $imgbase . "timeline.png'></p>";
            break;
    }

    return $output;
}
