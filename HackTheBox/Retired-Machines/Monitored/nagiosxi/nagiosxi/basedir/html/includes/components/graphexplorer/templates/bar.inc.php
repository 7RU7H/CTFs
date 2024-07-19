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
function fetch_bargraph($args)
{
    global $cfg;

    $height = grab_array_var($args, 'height', 500);
    $filename = str_replace(" ", "_", strtolower($args['title']));

    $args['title'] = encode_form_val($args['title']);
    $args['names'] = encode_form_val($args['names']);
    $args['container'] = encode_form_valq($args['container']);

    // Special export settings for local exporting
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
            chartOptions: { chart: { spacing: [30, 50, 30, 30] } }
         },";
    }

    //begin heredoc string
    $graph = <<<GRAPH
    
    var chart1; // globally available

    //reset default colors
    Highcharts.setOptions({
                colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });

    chart1 = new Highcharts.Chart({
         {$exporting}
         chart: {
            renderTo: '{$args['container']}',      
            defaultSeriesType: 'bar',
            height: {$height},
            animation: false
         },
         credits: {
            enabled: false
         },
         title: {
            text: '{$args['title']}'      
         },
         legend: {
                enabled: false
         },
         xAxis: {
                categories: {$args['categories']},  //use if there are multiple perf outputs like "rta" and "pl"
              //categories: ['Apples', 'Bananas', 'Oranges']
         },
         yAxis: {
            title: {
               text: '{$args['yTitle']}'         
            }
         },
         series: 
         [{
                name: '{$args['names']}',
                data: {$args['data']},
                animation: false
          }]      
    });
GRAPH;

    return $graph;
}       
        
