<?php
//
// Availability Dashlet
// Copyright (c) 2010-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');


$availability_report_name = "availability";


// Register a dashlet
$args = array();
$args[DASHLET_NAME] = "availability";
$args[DASHLET_TITLE] = _("Availability Graph");
$args[DASHLET_FUNCTION] = "availability_dashlet_func";
$args[DASHLET_DESCRIPTION] = _("Displays availability report graph.");
$args[DASHLET_WIDTH] = "400";
$args[DASHLET_HEIGHT] = "300";
$args[DASHLET_INBOARD_CLASS] = "availability_report_inboard";
$args[DASHLET_OUTBOARD_CLASS] = "availability_report_outboard";
$args[DASHLET_CLASS] = "availability_report";
$args[DASHLET_AUTHOR] = "Nagios Enterprises, LLC";
$args[DASHLET_COPYRIGHT] = "Dashlet Copyright &copy; 2015-2018 Nagios Enterprises. All rights reserved.";
$args[DASHLET_HOMEPAGE] = "https://www.nagios.com";
$args[DASHLET_SHOWASAVAILABLE] = false;
register_dashlet($args[DASHLET_NAME], $args);


function availability_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";
    $imgbase = get_base_url() . "";

    switch ($mode) {

        case DASHLET_MODE_GETCONFIGHTML:
            break;

        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $divId = uniqid();
            $url = "reports/availability.php";

            $url .= "?mode=getchart&divId=".$divId;

            // Check to make sure it's not using static hardcoded IPs
            if (strpos($url, "://") === FALSE) {
                $url = get_base_url() . $url;
            }

            // Availability args
            $dashtype = grab_array_var($args, 'dashtype', '');
            $title = grab_array_var($args, 'title', '');
            $subtitle = grab_array_var($args, 'subtitle', '');
            $legend = grab_array_var($args, 'legend', '');
            $colors = grab_array_var($args, 'colors', '');

            foreach ($args as $key => $val) {
                if ($mode == DASHLET_MODE_INBOARD && $key == 'data') { continue; }
                $url .= "&" . $key . "=" .  urlencode($val);
            }

            $size = "";
            if ($mode == DASHLET_MODE_OUTBOARD) {
                $size = ' style="width: 400px; height: 300px;"';
            }

            $output .= '
            <div class="availability_dashlet" id="' . $divId . '"'.$size.'>
            ' . get_throbber_html() . '
            </div>

            <script type="text/javascript">
            $(document).ready(function() {

                get_' . $divId . '_content();

                // Re-build the content when we resize
                $("#' . $divId . '").closest(".ui-resizable").on("resizestop", function(e, ui) {
                    var height = ui.size.height - 27;
                    var width = ui.size.width - 17;
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
            }
            </script>';
            break;

        case DASHLET_MODE_PREVIEW:
            break;
    }

    return $output;
}
