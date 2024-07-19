<?php
//
// Graph Explorer
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//


/**
 * @param $args
 *
 * @return string
 */
function fetch_multistack($args)
{
    global $cfg;

    // Allow for height to be passed for resizing
    $height = grab_array_var($args, 'height', 500);
    $warning = grab_array_var($args, "warning");
    $critical = grab_array_var($args, "critical");
    $wc_enable = grab_array_var($args, "wc_enable", 0);
    $wc_display = grab_array_var($args, "wc_display", 0);
    $highchart_scale = grab_array_var($args, 'highcharts_scale', '');
    $show_thresh = grab_array_var($args, "show_thresh");

    // Remove some of the underscores
    $args['title'] = str_replace("_", " ", $args['title']);
    $args['title'] = encode_form_val(urldecode($args['title']));

    // Create warning/crit lines (not visible by default)
    $start = (int) $args['start'];
    $step = $args['increment'] * 1000;
    $stop = $step * $args['count'] * 1000;
    $rrd_start = (int) substr($start, 0, -3); // convert back since it was converted before it was sent
    $rrd_stop = $rrd_start + ($args['count'] * $args['increment']);
    $rrd_step = $args['increment'];

    $warning_plot = json_encode(array());
    $critical_plot = json_encode(array());

    if ($show_thresh) {
        // Determine range band or non-range line color
        if (isset($warning["range"])) {
            $warn_color = 'rgba(255,255,0,0.3)';
            $crit_color = 'rgba(255,0,0,0.3)';
        } else {
            $warn_color = '#ffff80';
            $crit_color = '#ff3333';
        }

        if ($stop && $start && $step) {
            if ($warning !== "") {
                $warning_plot = make_highcharts_warn_crit('Warning', $warn_color, $warning, $start, $step, $stop, $args['count'], $wc_enable);
            }

            if ($critical !== "") {
                $critical_plot = make_highcharts_warn_crit('Critical', $crit_color, $critical, $start, $step, $stop, $args['count'], $wc_enable);
            }
        }
    }

    $buttons = "";
    $zoom_button = "";
    $crit_button = theme_image("critical_small.png");
    $crit_button = str_replace('"', '', $crit_button);
    $warn_button = theme_image("warning_small.png");
    $warn_button = str_replace('"', '', $warn_button);

    $container_id = $args['container'];

    // Set line and other colors
    $line_color = '#EEE';
    $dot_color = '#DFDFDF';
    $button_fill = '#F0F0F0';
    $button_fill_hover = '#F0F0F0';
    $button_stroke = '#AAA';
    $button_color = '#000';
    if (get_theme() == 'xi5dark') {
        $line_color = '#444';
        $dot_color = '#333';
        $button_fill = '#333';
        $button_fill_hover = '#666';
        $button_stroke = '#000';
        $button_color = '#F0F0F0';
    }

    // show buttons if displaying a single metric
    if ($show_thresh && $wc_enable) {
        $buttons = ",
                        buttons: {
                            Critical: {
                                _id: 'hc-critical-button',
                                _titleKey: 'critical',
                                symbol: 'url($crit_button)',
                                symbolX: 20,
                                symbolY: 19,
                                symbolSize: 16,
                                onclick: function () { toggle_critical_plot_{$container_id}(); }
                            },
                            Warning: {
                                _id: 'hc-warning-button',
                                _titleKey: 'warning',
                                symbol: 'url($warn_button)',
                                symbolX: 20,
                                symbolY: 19,
                                symbolSize: 16,
                                onclick: function() { toggle_warning_plot_{$container_id}();  }
                            }
                        }";
    }

    // Special export settings for local exporting
    $filename = str_replace(array("  ", " ", ":", "__", "_-_", "-/", "/"),
                            array(" ", "_", "-", "_", "-", "", ""),
                            strtolower($args['title']));
    $filename = trim($filename, "_");
    if (empty($filename)) { $filename = "multistacked_graph"; }
    $exporting = "";

    if (get_option('highcharts_local_export_server', 1)) {
        $exporting_url = "";
        $ini = parse_ini_file($cfg['root_dir'] . '/var/xi-sys.cfg');
        if ($ini['distro'] == "el9" || $ini['distro'] == "ubuntu22") {
            $exporting_url = "url: '".get_base_url()."/includes/components/highcharts/exporting-server/index.php',";
        }
        $exporting = "exporting: {
                        {$exporting_url}
                        sourceHeight: $('#{$args['container']}').height(),
                        sourceWidth: $('#{$args['container']}').width(),
                        filename: '{$filename}',
                        chartOptions: { chart: { spacing: [25, 25, 25, 25], marginRight: 30 },
                                        title: { text: '" . _("Multistacked Performance Graph") . "' }
                        }{$buttons}
                    },";
    }

    //begin heredoc string syntax 
    $graph = <<<GRAPH

        var COUNT = {$args['count']}; //total rrd entries fetched
        var START = {$args['start']};   //Date.UTC(2011, 1, 21) ->added below for correct datatype
        var TITLE = '{$args['title']}'; 
        var CONTAINER = '{$args['container']}'; 
        var SCALE = '$highchart_scale';

        //reset default colors 
        Highcharts.setOptions({
            colors: ['#4089F9', '#23B55E', '#FFA121', '#F24800', '#454545', '#7472DF', '#FF9655', '#FFF263', '#6AF9C4'] 
        }); 

        //data points added below for correct datatype interpretation               
        //use browser's timezone offset for date        
        Highcharts.setOptions({
            global: { useUTC: false },
        });

            var chart;
            chart = new Highcharts.Chart({
                    {$exporting}
                    chart: {
                        renderTo: CONTAINER,
                        zoomType: 'x',
                        spacingRight: 10,
                        height: {$height},
                        animation: false,
                        plotBorderWidth: 1,
                        plotBorderColor: '{$line_color}',
                        resetZoomButton: {
                            theme: {
                                height: 15,
                                r: 0,
                                stroke: '{$button_stroke}',
                                fill: '{$button_fill}',
                                states: {
                                    hover: {
                                        'stroke-width': 1,
                                        stroke: '{$button_stroke}',
                                        fill: '{$button_fill_hover}'
                                    },
                                    select: {
                                        'stroke-width': 1,
                                        stroke: '{$button_stroke}',
                                        fill: '{$button_fill_hover}'
                                    }
                                },
                                style: {
                                    cursor: 'pointer',
                                    color: '{$button_color}'
                                }
                            }
                        }
                    },
                    lang: {
                        warning: "Toggle Warning",
                        critical: "Toggle Critical"
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        //insert host/service name here 
                        text: TITLE,
                        style: {
                            fontWeight: 'bold',
                            "font-family": "'verdana', 'serif'",
                            "font-size": 15
                        }
                    },
                    navigation: {
                        menuStyle: {
                            'box-shadow': '1px 1px 0 #AAA',
                            'border-right': 0,
                            'border-bottom': 0
                        },
                        buttonOptions: {
                            verticalAlign: 'bottom',
                            y: -10,
                            theme: {
                                r: 0,
                                states: {
                                    hover: {
                                        fill: '{$button_fill}'
                                    },
                                    select: {
                                        fill: '{$button_fill}'
                                    }
                                }
                            }
                        }
                    },
                    subtitle: {
                        text: ""
                    },
                    xAxis: {
                        type: 'datetime',
                        maxZoom: {$args['increment']}*1000,  // max zoom is 5 minutes 
                        title: {
                            text: null
                        },
                        events: {
                            // setExtremes: function(event) {
                            //     if (typeof event.min == 'undefined' && typeof event.max == 'undefined') {
                            //         $('#{$args['container']} .highcharts-button:eq(1)').find('rect').attr('class', '');
                            //         $('#{$args['container']} .highcharts-button:eq(2)').find('rect').attr('class', '');
                            //     }
                            // },
                            afterSetExtremes: function(event) {
                                // update the values for custom export functionality
                                this.chart.options.exporting.start = Math.floor(event.min / 1000);
                                this.chart.options.exporting.end = Math.floor(event.max / 1000);
                            }
                        },
                        gridLineWidth: 1,
                        gridLineColor: '{$dot_color}',
                        gridLineDashStyle: 'dot',
                        lineColor: '{$line_color}',
                        tickColor: '{$line_color}'
                    },
                    yAxis: [
GRAPH;

    // Add the different axis
    $axis = array();
    foreach ($args['UOM'] as $uom) {
        $axis[] = '{
                            title: {
                                text: ""
                            },
                            labels: {
                                formatter: function() {
                                    if (this.value % 1 != 0) {
                                        return this.value.toFixed(2) + "' . $uom . '";
                                    }
                                    return this.value + "' . $uom . '";
                                }
                            },
                            startOnTick: true,
                            endOnTick: true,
                            showFirstLabel: false,
                            type: SCALE,
                            gridLineWidth: 1,
                            gridLineColor: "'.$line_color.'"
                        }';
    }
    $graph .= implode(',', $axis);

    $stacking = "";
    if ($args['linetype'] == 'stacked') {
        $stacking = "stacking: 'normal',";
    }

    $graph .= <<<GRAPH

                    ],
                    tooltip: {
                        shared: true,
                        shadow: false,
                        borderRadius: 0,
                        formatter: function() {
                            html = Highcharts.dateFormat("%A %b, %e - %l:%M %p", parseInt(this.x));
                            for (var i = 0; i < this.points.length; i++) {
                                var series_name = this.points[i].series.name;
                                var symbol_string = this.points[i].series.symbol;

                                switch (symbol_string) {
                                    case 'circle':
                                        symbol = '●';
                                        break;
                                    case 'diamond':
                                        symbol = '♦';
                                        break;
                                    case 'square':
                                        symbol = '■';
                                        break;
                                    case 'triangle':
                                        symbol = '▲';
                                        break;
                                    case 'triangle-down':
                                        symbol = '▼';
                                        break;
                                }

                                html += '<br><span style="color:' + this.points[i].series.color + '">' + symbol + ' </span> <b>' + series_name + '</b>: ' + Math.round(this.points[i].y * 1000) / 1000;
                            }

                            return html;
                        }
                    },
                    legend: {
                        enabled: true,
                        itemStyle: {
                            fontWeight: 'normal'
                        }
                    },
                    plotOptions: {
                        series: {
                           fillOpacity: 0.5
                        },
                        area: {
                            lineWidth: 1,{$stacking}
                            marker: {
                                enabled: false,
                                states: {
                                    hover: {
                                        enabled: true,
                                        radius: 4
                                    }
                                }
                            },
                            shadow: false,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            }
                        },
                        line: {
                            lineWidth: 1,
                            marker: {
                                enabled: false,
                                states: {
                                    hover: {
                                        enabled: true,
                                        radius: 4
                                    }
                                }
                            },
                            shadow: false,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            }
                        },
                        spline: {
                            lineWidth: 1,
                            marker: {
                                enabled: false,
                                states: {
                                    hover: {
                                        enabled: true,
                                        radius: 4
                                    }
                                }
                            },
                            shadow: false,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            }
                        }
                    },
GRAPH;
//end heredoc syntax

    $graph .= "
                    series: [";

    // Counter to only create one set of warning/critical legend keys
    $wc_count = 0;

    //loop for multiple data sets in perfdata
    $series = array();
    $uom_on_graph = array();
    for ($i = 0; $i < count($args['datastrings']); $i++) {
        // Remove some more underscores
        $series_name = encode_form_val(str_replace("_", " ", $args['names'][$i]));

        $found = false;
        foreach ($uom_on_graph as $uom => $v) {
            if ($uom == $args['UOM'][$i]) {
                $uom_to_use = $v;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $uom_on_graph[$args['UOM'][$i]] = $i;
            $uom_to_use = $i;
        }

        $yaxis = "";
        if (count($args['datastrings']) > 1) {
            $yaxis = "yAxis: " . $uom_to_use . ",";
        }

        $linetype = $args['linetype'];
        if ($linetype == 'stacked') {
            $linetype = 'area';
        }

        $series[] = "{
                        type: \"{$linetype}\",
                        name: \"{$series_name}\",  //service description or host check,
                        pointInterval: {$args['increment']}*1000,            //time scale, 5mn 
                        pointStart: {$args['start']},    //start time  
                        data: [
                            " . implode(', ', $args['datastrings'][$i]) . "
                        ],
                        " . $yaxis . "
                        tooltip: {
                            valueSuffix: ' " . $args['UOM'][$i] . "'
                        },
                        animation: false
                    }";
    } //end loop

    $graph .= implode(',', $series);
    $graph .= "],  //series data closed";

    $graph .= "

    });

    var wc_display = " . $wc_display . ";
    var viewWarning = false;
    var viewCritical = false;
    var chartw_" . $container_id . ";
    var chartc_" . $container_id . ";

    function toggle_warning_plot_" . $container_id . "() {
        var CONTAINER_ID = '" . $container_id . "';

        // Set objects
        var warning_plot = {};
        warning_plot[CONTAINER_ID] = " . $warning_plot . ";

        chartw_" . $container_id . " = $({$args['container']}).highcharts();
        var extremes_" . $container_id . ";
        var maxY_" . $container_id . ";
        var minY_" . $container_id . ";

        // Get current chart min/max
        extremes_" . $container_id . " = chartw_" . $container_id . ".yAxis[0].getExtremes();
        maxY_" . $container_id . " = extremes_" . $container_id . ".max;
        minY_" . $container_id . " = extremes_" . $container_id . ".min;

        // If range set min and max
        if ('bottom' in warning_plot[CONTAINER_ID] || 'top' in warning_plot[CONTAINER_ID]) {
            if (warning_plot[CONTAINER_ID]['bottom']['from'] == \"\") {
                warning_plot[CONTAINER_ID]['bottom']['from'] = minY_" . $container_id . ";
            }

            if (warning_plot[CONTAINER_ID]['top']['to'] == \"\") {
                warning_plot[CONTAINER_ID]['top']['to'] = maxY_" . $container_id . ";
            }
        }

        // Determine if range and toggle warning plot
        if (warning_plot[CONTAINER_ID]['range']) {
            if (!viewWarning) {
                $('#{$args['container']} .highcharts-button:eq(2)').find('rect').attr('class', 'active');

                // if theres a second set we are doing inside ranges, else normal range
                if ('bottom' in warning_plot[CONTAINER_ID] && 'top' in warning_plot[CONTAINER_ID]) {
                    addPlot_bottom = {
                        color: warning_plot[CONTAINER_ID]['bottom']['color'],
                        from: warning_plot[CONTAINER_ID]['bottom']['from'],
                        to: warning_plot[CONTAINER_ID]['bottom']['to'],
                        label: {
                            text: warning_plot[CONTAINER_ID]['bottom']['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'left',
                            x: 6,
                            y: 14,
                            zIndex: 20
                        },
                        id: 'thresh-warn-in-band-bottom-" . $container_id . "',
                        zIndex: warning_plot[CONTAINER_ID]['bottom']['zIndex']
                    };

                    addPlot_top = {
                        color: warning_plot[CONTAINER_ID]['top']['color'],
                        from: warning_plot[CONTAINER_ID]['top']['from'],
                        to: warning_plot[CONTAINER_ID]['top']['to'],
                        label: {
                            text: warning_plot[CONTAINER_ID]['top']['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'left',
                            x: 6,
                            y: 14,
                            zIndex: 20
                        },
                        id: 'thresh-warn-in-band-top-" . $container_id . "',
                        zIndex: warning_plot[CONTAINER_ID]['top']['zIndex']
                    };

                    addBands('#{$args['container']}', 0, addPlot_top, extremes_" . $container_id . ");
                    addBands('#{$args['container']}', 0, addPlot_bottom, extremes_" . $container_id . ");
                } else {
                    addPlot = {
                        color: warning_plot[CONTAINER_ID]['color'],
                        from: warning_plot[CONTAINER_ID]['from'],
                        to: warning_plot[CONTAINER_ID]['to'],
                        label: {
                            text: warning_plot[CONTAINER_ID]['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'left',
                            x: 5,
                            y: 10,
                            zIndex: 20
                        },
                        id: 'thresh-warn-out-band-" . $container_id . "',
                        zIndex: warning_plot[CONTAINER_ID]['zIndex']
                    };

                    addBands('#{$args['container']}', 0, addPlot, extremes_" . $container_id . ");
                }
            } else {
                $('#{$args['container']} .highcharts-button:eq(2)').find('rect').attr('class', '');

                if (typeof addPlot_top !== 'undefined' || typeof addPlot_bottom !== 'undefined') {
                    removeBands('#{$args['container']}', 0, addPlot_top, 'thresh-warn-in-band-top-" . $container_id . "', extremes_" . $container_id . ");
                    removeBands('#{$args['container']}', 0, addPlot_bottom, 'thresh-warn-in-band-bottom-" . $container_id . "', extremes_" . $container_id . ");
                }

                if (typeof addPlot !== 'undefined') {
                    removeBands('#{$args['container']}', 0, addPlot, 'thresh-warn-out-band-" . $container_id . "', extremes_" . $container_id . ");
                }
            }

            viewWarning = !viewWarning;
        } else {
            if (!viewWarning) {
                $('#{$args['container']} .highcharts-button:eq(2)').find('rect').attr('class', 'active');

                addPlot = {
                    color: 'rgba(221, 221, 0, 1)',
                    dashStyle: warning_plot[CONTAINER_ID]['dashStyle'],
                    value: warning_plot[CONTAINER_ID]['value'],
                    width: warning_plot[CONTAINER_ID]['width'],
                    label: {
                        text: warning_plot[CONTAINER_ID]['label'],
                        style: { fontWeight: 'bold' },
                        align: 'left',
                        verticalAlign: 'top',
                        zIndex: 20
                    },
                    id: 'warn-plot-line-" . $container_id . "',
                    zIndex: warning_plot[CONTAINER_ID]['zIndex']
                };

                addBands('#{$args['container']}', 1, addPlot, extremes_" . $container_id . ");
            } else {
                $('#{$args['container']} .highcharts-button:eq(2)').find('rect').attr('class', '');

                removeBands('#{$args['container']}', 1, addPlot, 'warn-plot-line-" . $container_id . "', extremes_" . $container_id . ");
            }

            viewWarning = !viewWarning;
        }
    }

    function toggle_critical_plot_" . $container_id . "() {
        var CONTAINER_ID = '" . $container_id . "';

        // Set objects
        var critical_plot = {};
        critical_plot[CONTAINER_ID] = " . $critical_plot . ";

        chartc_" . $container_id . " = $({$args['container']}).highcharts();
        var extremes_" . $container_id . ";
        var maxY_" . $container_id . ";
        var minY_" . $container_id . ";

        // Get current chart min/max
        extremes_" . $container_id . " = chartc_" . $container_id . ".yAxis[0].getExtremes();
        maxY_" . $container_id . " = extremes_" . $container_id . ".max;
        minY_" . $container_id . " = extremes_" . $container_id . ".min;

        // If range set min and max
        if ('bottom' in critical_plot[CONTAINER_ID] || 'top' in critical_plot[CONTAINER_ID]) {
            if (critical_plot[CONTAINER_ID]['bottom']['from'] == \"\") {
                critical_plot[CONTAINER_ID]['bottom']['from'] = minY_" . $container_id . ";
            }

            if (critical_plot[CONTAINER_ID]['top']['to'] == \"\") {
                critical_plot[CONTAINER_ID]['top']['to'] = maxY_" . $container_id . ";
            }
        }

        // Determine if range and toggle critical plot
        if (critical_plot[CONTAINER_ID]['range']) {
            if (!viewCritical) {
                $('#{$args['container']} .highcharts-button:eq(1)').find('rect').attr('class', 'active');

                // if theres a second set we are doing inside ranges, else normal range
                if ('bottom' in critical_plot[CONTAINER_ID] && 'top' in critical_plot[CONTAINER_ID]) {
                    addPlot_top =  {
                        color: critical_plot[CONTAINER_ID]['top']['color'],
                        from: critical_plot[CONTAINER_ID]['top']['from'],
                        to: critical_plot[CONTAINER_ID]['top']['to'],
                        label: {
                            text: critical_plot[CONTAINER_ID]['top']['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'right',
                            x: -6,
                            y: 14,
                            zIndex: 20
                        },
                        id: 'thresh-crit-in-band-top-" . $container_id . "',
                        zIndex: critical_plot[CONTAINER_ID]['top']['zIndex']
                    };

                    addPlot_bottom = {
                        color: critical_plot[CONTAINER_ID]['bottom']['color'],
                        from: critical_plot[CONTAINER_ID]['bottom']['from'],
                        to: critical_plot[CONTAINER_ID]['bottom']['to'],
                        label: {
                            text: critical_plot[CONTAINER_ID]['bottom']['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'right',
                            x: -6,
                            y: 14,
                            zIndex: 20
                        },
                        id: 'thresh-crit-in-band-bottom-" . $container_id . "',
                        zIndex: critical_plot[CONTAINER_ID]['bottom']['zIndex']
                    };

                    addBands('#{$args['container']}', 0, addPlot_top, extremes_" . $container_id . ");
                    addBands('#{$args['container']}', 0, addPlot_bottom, extremes_" . $container_id . ");
                } else {
                    addPlot = {
                        color: critical_plot[CONTAINER_ID]['color'],
                        from: critical_plot[CONTAINER_ID]['from'],
                        to: critical_plot[CONTAINER_ID]['to'],
                        label: {
                            text: critical_plot[CONTAINER_ID]['label'],
                            style: { fontWeight: 'bold' },
                            verticalAlign: 'top',
                            align: 'right',
                            x: -6,
                            y: 14,
                            zIndex: 20
                        },
                        id: 'thresh-crit-out-band-" . $container_id . "',
                        zIndex: critical_plot[CONTAINER_ID]['zIndex']
                    };

                    addBands('#{$args['container']}', 0, addPlot, extremes_" . $container_id . ");
                }
            } else {
                $('#{$args['container']} .highcharts-button:eq(1)').find('rect').attr('class', '');

                if (typeof addPlot_top !== 'undefined' || typeof addPlot_bottom !== 'undefined') {
                    removeBands('#{$args['container']}', 0, addPlot_bottom, 'thresh-crit-in-band-bottom-" . $container_id . "', extremes_" . $container_id . ");
                    removeBands('#{$args['container']}', 0, addPlot_top, 'thresh-crit-in-band-top-" . $container_id . "', extremes_" . $container_id . ");
                }

                if (typeof addPlot !== 'undefined') {
                    removeBands('#{$args['container']}', 0, addPlot, 'thresh-crit-out-band-" . $container_id . "', extremes_" . $container_id . ");
                }
            }

            viewCritical = !viewCritical;
        } else {
            if (!viewCritical) {
                $('#{$args['container']} .highcharts-button:eq(1)').find('rect').attr('class', 'active');

                addPlot = {
                    color: '#ff3333',
                    dashStyle: critical_plot[CONTAINER_ID]['dashStyle'],
                    value: critical_plot[CONTAINER_ID]['value'],
                    width: critical_plot[CONTAINER_ID]['width'],
                    label: {
                        text: critical_plot[CONTAINER_ID]['label'],
                        style: { fontWeight: 'bold' },
                        align: 'right',
                        x: -6,
                        verticalAlign: 'top',
                        zIndex: 20
                    },
                    id: 'crit-plot-line-" . $container_id . "',
                    zIndex: critical_plot[CONTAINER_ID]['zIndex']
                };

                addBands('#{$args['container']}', 1, addPlot, extremes_" . $container_id . ");
            } else {
                $('#{$args['container']} .highcharts-button:eq(1)').find('rect').attr('class', '');

                removeBands('#{$args['container']}', 1, addPlot, 'crit-plot-line-" . $container_id . "', extremes_" . $container_id . ");
            }

            viewCritical = !viewCritical;
        }
    } 

    // Add/remove object functions
    var chartid;
    var uchart;
    var plot_active = false;

    function addBands(chartid, type, addPlot, extremes) {
        chartid = chartid;
        uchart = $(chartid).highcharts();

        // Add to graph
        if (type === 1) {
            uchart.yAxis[0].addPlotLine(addPlot);
        } else {
            uchart.yAxis[0].addPlotBand(addPlot);
        }

        // Check if we need to set a new height
        if (extremes.max < addPlot.value) {
            var newmax = parseFloat(addPlot.value);

            uchart.yAxis[0].setExtremes(extremes.min, newmax);
        } else if (extremes.min > addPlot.value) {
            var newmin = parseFloat(addPlot.value);

            uchart.yAxis[0].setExtremes(newmin, extremes.max);
        }
    }

    // Remove function
    function removeBands(chartid, type, addPlot, plot_id, extremes) {
        chartid = chartid;
        uchart = $(chartid).highcharts();

        if (type === 1) {
            uchart.yAxis[0].removePlotLine(plot_id);
        } else {
            uchart.yAxis[0].removePlotBand(plot_id);
        }

        // Check if another line/band is in this graph
        for (var i = 0; i < uchart.yAxis[0].plotLinesAndBands.length; i++) {
            var plot_active_ids = uchart.yAxis[0].plotLinesAndBands[i].id;

            if (plot_active_ids !== '') {
                plot_active = true;
            }
        }

        // Reset extremes to current view if no other lines present
        if (extremes.dataMax < extremes.max && !plot_active) {
            uchart.zoomOut();
        }
    } // End graph

    // Toggle the button if wc_display is enabled
    if (wc_display) {
        toggle_critical_plot_{$container_id}();
        toggle_warning_plot_{$container_id}();
    }";

    return $graph;
}


// create lines for warning and critical
function make_highcharts_warn_crit($name, $color, $level, $start, $step, $stop, $count, $wc_enable) {
    // make legened key last in the list
    if ($name == "Warning") {
        $legendIndex = 1;
    } else {
        $legendIndex = 2;
    }

    if (is_array($level)) {
        // Using ranges- lets create the plotBand
        $count = intval($count);
        $inside = ($level['range'] == 'inside') ? 1 : 0;

        // Check if inside range
        if (!$inside) { // Outside
            if ($level['range'] == 'outside' && $level['max'] == 0) { // Check for outside single number range eg, '10:'
                $output = array(
                    'color' => "{$color}",
                    'from' => "{$level['min']}",
                    'to' => "{$level['max']}",
                    'label' => "{$name} (Range) '{$level['min']}:'",
                    'zIndex' => "{$legendIndex}",
                    'range' => true
                );
            } else {
                $output = array(
                    'bottom' => array(
                                'color' => "{$color}",
                                'from' => "",
                                'to' => "{$level['min']}",
                                'label' => "{$name} (Range) <= {$level['min']}",
                                'zIndex' => "{$legendIndex}"
                            ),
                    'top' => array(
                                'color' => "{$color}",
                                'from' => "{$level['max']}",
                                'to' => "",
                                'label' => "{$name} (Range) >= {$level['max']}",
                                'zIndex' => "{$legendIndex}"
                            ),
                    'range' => true
                );
            }
        } else { // Inside
            $output = array(
                'color' => "{$color}",
                'from' => "{$level['min']}",
                'to' => "{$level['max']}",
                'label' => "{$name} (Range) {$level['min']} - {$level['max']}",
                'zIndex' => "{$legendIndex}",
                'range' => true
            );
        }
    } else {
        // Single plotLine
        $output = array(
            'color' => "{$color}",
            'dashStyle' => "solid",
            'value' => "{$level}",
            'width' => 1,
            'label' => "{$name}: {$level}",
            'zIndex' => "{$legendIndex}",
            'range' => false
        );
    }

    return json_encode($output);
}
