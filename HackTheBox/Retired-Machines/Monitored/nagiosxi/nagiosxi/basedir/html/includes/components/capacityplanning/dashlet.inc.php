<?php
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  


include_once(dirname(__FILE__) . '/../../dashlets/dashlethelper.inc.php');


function capacityplanning_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";
    $imgbase = get_base_url() . "includes/components/capacityplanning/images/";

    switch ($mode) {

        case DASHLET_MODE_GETCONFIGHTML:
            break;

        case DASHLET_MODE_OUTBOARD:

            $output = "";

            $id = "capacityplanning_" . uniqid();

            $ajaxargs = $args;

            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"$var\" : \"$val\"";
                $n++;
            }

            $jargs .= "}";

            $output .= '
            <div class="capacityplanning_dashlet" style="min-height: 340px;" id="' . $id . '">
                <div class="infotable_title">' . _("Capacity Planning") . '</div>
                ' . get_throbber_html() . '
            </div>

            <script type="text/javascript">
            $(document).ready(function() {
                var cp_timeout_' . $id . ';
                var width = $("#' . $id . '").width();
                if (width != 0) {
                    width = width - 36; // Fix display of charts in report pages
                }
                var height = $("#' . $id . '").height();
                get_' . $id . '_content(height, width);

                $(window).resize(function() {
                    var width = $("#' . $id . ' .cp-tab-container").width();
                    var height = $("#' . $id . ' .cp-tab-container").height();
                    clearTimeout(cp_timeout_' . $id . ');
                    cp_timeout_' . $id . ' = setTimeout(get_' . $id . '_content, 500, height, width);
                });

                function get_' . $id . '_content(height, width) {
                    var jargs = ' . $jargs . ';
                    if (jargs["height"] == undefined) {
                        jargs["height"] = height;
                    }
                    if (jargs["width"] == undefined) {
                        jargs["width"] = width;
                    }
                    var optsarr = {
                        "func": "get_capacityplanning_dashlet_html",
                        "args": jargs
                    }
                        
                    var opts=JSON.stringify(optsarr);
                    get_ajax_data_innerHTML_with_callback("getxicoreajax", opts, true, $("#' . $id . '"), "bind_tt");
                    
                    // Stop clicking in graph from moving dashlet
                    $("#' . $id . '").closest(".ui-draggable").draggable({ cancel: "#' . $id . '" });
                }
            });
            </script>
            ';

            break;

        case DASHLET_MODE_INBOARD:

            $output = "";

            $id = "capacityplanning_" . uniqid();


            $ajaxargs = $args;

            $ajaxargs['hide_data'] = true;

            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"$var\" : \"$val\"";
                $n++;
            }

            $jargs .= "}";

            // Enterprise only feature, check to make sure enterprise is enabled
            echo enterprise_message();
            if (enterprise_features_enabled()) {

                $output .= '
                <div class="capacityplanning_dashlet" id="' . $id . '">

                <div class="infotable_title">' . _("Capacity Planning") . '</div>
                ' . get_throbber_html() . '

                </div><!--ahost_status_summary_dashlet-->

                <link href="' . get_base_url() . '/includes/components/capacityplanning/includes/capacityplanning.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="' . get_base_url() . '/includes/components/capacityplanning/includes/capacityreport.js.php"></script>

                <script type="text/javascript">
                $(document).ready(function(){
                    
                    get_' . $id . '_content();

                    // Refresh every 6 hours
                    $("#' . $id . '").everyTime(6*3600*1000, "timer-' . $id . '", function(i) {
                        get_' . $id . '_content();
                    });
                    
                    // Re-build the content when we resize
                    $("#' . $id . '").closest(".ui-resizable").on("resizestop", function(e, ui) {
                        var height = ui.size.height;
                        var width = ui.size.width;
                        get_' . $id . '_content(height, width);
                    });
                });

                function get_' . $id . '_content(height, width){
                    var optsarr = {
                        "func": "get_capacityplanning_dashlet_html",
                        "args": ' . $jargs . '
                    }
                            
                    if (height == undefined) { var height = $("#' . $id . '").parent().height(); }
                    if (width == undefined) { var width = $("#' . $id . '").parent().width(); }
                            
                    optsarr["args"]["height"] = height - 22;
                    optsarr["args"]["width"] = width - 36;
                            
                    var opts=JSON.stringify(optsarr);

                    get_ajax_data_innerHTML("getxicoreajax", opts, true, $("#' . $id . '"));
                            
                    // Stop clicking in graph from moving dashlet
                    $("#' . $id . '").closest(".ui-draggable").draggable({cancel: "#' . $id . '"});
                }
                </script>
                ';
            }

            break;

        case DASHLET_MODE_PREVIEW:
            $output = "<p><img src='" . $imgbase . "preview.png'></p>";
            break;
    }

    return $output;
}
