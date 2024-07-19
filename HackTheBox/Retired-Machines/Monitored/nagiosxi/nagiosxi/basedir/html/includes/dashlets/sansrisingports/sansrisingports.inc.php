<?php
// HELLO WORLD EXAMPLE DASHLET
//
// Copyright (c) 2008-2015 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id$

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');

sansrisingports_route_request();

function sansrisingports_route_request() {

    // check if we're only interested in the highcharts graph before we do anything else
    grab_request_vars();
    $get_hc = grab_request_var('get_hc', 0);
    if ($get_hc != 0) {

        $height = intval(grab_request_var('height', 300));
        $width = intval(grab_request_var('width', 400));
        $id = grab_request_var('id');

        echo sansrisingports_get_hc($id, $height, $width);    
        exit();
    
    } else {

        // if we made it this far, run the initialization function
        sansrisingports_dashlet_init();
    }
}

// initialize and register dashlet with xi
function sansrisingports_dashlet_init() {

    // respect the name!
    $name = "sansrisingports";

    $args = array(

        // need a name
        DASHLET_NAME =>             $name,

        // informative information
        DASHLET_VERSION =>          "2.0",
        DASHLET_DATE =>             "07-12-2016",
        DASHLET_AUTHOR =>           "Nagios Enterprises, LLC",
        DASHLET_DESCRIPTION =>      _("A graph of the top 10 rising ports from the SAN Internet Storm Center. Useful for spotting trends related to virus and worm outbreaks."),
        DASHLET_COPYRIGHT =>        _("Dashlet Copyright &copy; 2016 Nagios Enterprises. Data Copyright &copy; SANS Internet Storm Center."),
        DASHLET_LICENSE =>          _("Creative Commons Attribution-Noncommercial 3.0 United States License."),
        DASHLET_HOMEPAGE =>         _("https://www.nagios.com"),

        // the good stuff - only one output method is used.  order of preference is 1) function, 2) url
        DASHLET_FUNCTION =>         "sansrisingports_dashlet_func",
        //DASHLET_URL =>            get_dashlet_url_base($name)."/$name.php",

        DASHLET_TITLE =>            "SANS Internet Storm Center Top 10 Rising Ports",

        DASHLET_OUTBOARD_CLASS =>   "sansrisingports_outboardclass",
        DASHLET_INBOARD_CLASS =>    "sansrisingports_inboardclass",
        DASHLET_PREVIEW_CLASS =>    "sansrisingports_previewclass",

        DASHLET_WIDTH =>            "300px",
        DASHLET_HEIGHT =>           "212px",
        DASHLET_OPACITY =>          "0.8",
        DASHLET_BACKGROUND =>       "",
    );

    register_dashlet($name, $args);
}

// the function for printing the dashlet container and jquery ajax to pull the hc data
function sansrisingports_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null) {

    $output = "";
    $imgbase = get_dashlet_url_base("sansrisingports") . "/images/";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $div_id = random_string(24);
            $dashlet_url = get_dashlet_url_base("sansrisingports") . '/sansrisingports.inc.php?get_hc=1&id=' . $div_id;

            $output .= <<<OUTPUT
                <div id="{$div_id}"></div>
                <script>
                $(function () {

                    get_{$div_id}_content();

                    $("#{$div_id}").closest(".ui-resizable").on("resizestop", function(e, ui) {
                        var height = ui.size.height;
                        var width = ui.size.width;
                        get_{$div_id}_content(height, width);
                    });
                });

                function get_{$div_id}_content(height, width) {

                    if (height == undefined) { 
                        var height = $("#{$div_id}").parent().height(); 
                    }

                    if (width == undefined) { 
                        var width = $("#{$div_id}").parent().width(); 
                    }

                    height = height - 17;

                    $("#{$div_id}").load("{$dashlet_url}" + "&height=" + height + "&width=" + width);

                    // Stop clicking in graph from moving dashlet
                    $("#{$div_id}").closest(".ui-draggable").draggable({ cancel: "#{$div_id}" });
                }
                </script>
OUTPUT;
            break;

        case DASHLET_MODE_PREVIEW:
            $output = "<p><img src='" . $imgbase . "preview.png'></p>";
            break;
    }

    return $output;
}

// simply return the highcharts data necessary to create the graph
function sansrisingports_get_hc($id, $height, $width) {

    $date = date("Y-m-d");
    $url = "https://isc.sans.edu/portascii.html?start={$date}&end={$date}";

    $get_fresh_data = true;
    $port_data_array = array();

    $div_id = json_encode($id);

    $sansrisingports = get_array_option('sansrisingports', array());
    if (isset($sansrisingports['url'])) {

        if ($sansrisingports['url'] == $url &&
            is_array($sansrisingports['data']) &&
            count($sansrisingports['data']) == 10) {

            $get_fresh_data = false;
            $port_data_array = $sansrisingports['data'];
        }
    }

    // only get fresh data if we need it
    if ($get_fresh_data) {

        // get the data
        $data = load_url($url);

        // break the data into an array
        $data_lines = explode("\n", $data);
        $start_counting = false;
        $port_count = 0;
        foreach ($data_lines as $line) {

            // skip lines that start with a comment
            if (strpos($line, '#') === 0)
                continue;

            // skip empty lines
            if (trim($line) === "")
                continue;

            // see if we are at the header row
            if  (
                strpos($line, 'port') !== false &&
                strpos($line, 'records') !== false &&
                strpos($line, 'targets') !== false &&
                strpos($line, 'sources') !== false) {

                $start_counting = true;
                continue;
            }

            // replace all whitespace with a single whitespace character
            $line = preg_replace('/\s/', ' ', $line);

            // break line into an array of its own!
            $line_array = explode(' ', $line);

            $port_data_array[] = array(
                'port' =>       $line_array[0],
                'records' =>    $line_array[1],
                'targets' =>    $line_array[2],
                'sources' =>    $line_array[3],
                'tcpratio' =>   $line_array[4],
                );

            $port_count++;
            if ($port_count >= 10)
                    break;
        }

        $sansrisingports = array();
        $sansrisingports['url'] = $url;
        $sansrisingports['data'] = $port_data_array;
        set_array_option('sansrisingports', $sansrisingports);
    }

    $categories = '';
    $records = '';
    foreach ($port_data_array as $port) {
        $categories .= "'" . $port['port'] . "',";
        $records .= $port['records'] . ',';
    }

    // trim trailing comma
    $categories = substr($categories, 0, -1);
    $records = substr($records, 0, -1);

    return <<<OUTPUT
        <script type="text/javascript">

        Highcharts.setOptions({
            colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'] 
        });

        var CONTAINER = {$div_id};
        var chart;
        $(function () {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: CONTAINER,
                    type: 'bar',
                    height: {$height},
                    width: {$width}
                },
                title: {
                    text: 'SANS Internet Storm Center Top 10 Rising Ports for {$date}'
                },
                subtitle: {
                    text: 'Source: <a href="{$url}">Internet Storm Center</a>'
                },
                xAxis: {
                    categories: [{$categories}],
                    title: {
                        text: 'Ports'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Records',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -40,
                    y: 100,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                    shadow: true
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'Records',
                    data: [{$records}]
                }]
            });
        });
        </script>
OUTPUT;
}