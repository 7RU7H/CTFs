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
function fetch_columngraph($args)
{
    global $cfg;

    $height = grab_array_var($args, 'height', 500);
    $filename = str_replace(" ", "_", strtolower($args['title']));

    $args['title'] = encode_form_val(urldecode($args['title']));
    $args['names'] = encode_form_val($args['names']);

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
         },";
    }

    //begin heredoc string
    $graph = <<<GRAPH
    
        var chart1; // globally available
$(document).ready(function() {

    //reset default colors
    Highcharts.setOptions({
        colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });


      chart1 = new Highcharts.Chart({
         {$exporting}
         chart: {
            renderTo: '{$args['container']}',      
            defaultSeriesType: 'column', 
            margin: [ 50, 50, 100, 80],
            height: {$height},
            animation: false
         },
         credits: {
            enabled: false
         },
         title: {
            text: '{$args['title']}'      
         },
         xAxis: {
                categories: {$args['categories']},  //use if there are multiple perf outputs like "rta" and "pl"
              //categories: ['Apples', 'Bananas', 'Oranges']
              
            labels: 
            {
                rotation: -45,
                align: 'right',
                style: { font: 'normal 10px Verdana, sans-serif' }
            }  
              
         },
         yAxis: {
            title: {
               text: '{$args['yTitle']}'         
            }
         },
         legend: {
                    enabled: false
         },
         tooltip: {
            formatter: function() {
                return '<b>'+ this.x +'</b><br />'+this.y;
            }
        },
         series: 
        [{
                name: '{$args['names']}',           
                data: {$args['data']},      //json encoded array of integers     
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: Highcharts.theme.dataLabelsColor || '#FFFFFF',
                    align: 'right',
                    x: -3,
                    y: 10,
                    formatter: function() {
                        return this.y;
                    },
                    style: { font: 'normal 10px Verdana, sans-serif' }
                },
                animation: false
        }],  //end series 

    
                         
      });  //close series 
   });
GRAPH;


    return $graph;
}       
        
