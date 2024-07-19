<?php
//
// API for Graph Generation
// Copyright (c) 2016-2019 Nagios Enterprises, LLC. All rights reserved.
//


/**
 * Main graph router function of the graph explorer
 *
 * @return null (prints output to screen) 
 */
function get_jquery()
{
    // If there are nav requests, process and retrieve a graph
    if (process_globs()) {
        $graph = fetch_graph();
        print $graph;
    } else {
        include(dirname(__FILE__).'/templates/default.inc.php');
    }
}


/**
 * Checks and validates request args for valid graph retrieval
 *
 * @global array $vtkArgs - modifies global array and sets default values 
 * @return bool
 */
function process_globs()
{
    // Grab hostname, service description, etc 
    global $vtkArgs; 
    if (isset($_REQUEST))
    {
        // Need a graph type
        if (!isset($_REQUEST['type'])) {
            return false;
        }

        $vtkArgs['type'] = grab_request_var('type');

        if ($vtkArgs['type'] == 'multistack') {
            $vtkArgs['hslist'] = grab_request_var('hslist', array());
            $vtkArgs['linetype'] = grab_request_var('linetype', 'line');
        } else {
            // Fix and host/services with no underscores
            $vtkArgs['host'] = grab_request_var('host',NULL); 
            $vtkArgs['service'] = grab_request_var('service', NULL);

            // Check if they are being sent as hostname or servicename
            if (empty($vtkArgs['host'])) {
                $vtkArgs['host'] = grab_request_var('hostname', NULL);
            }

            if (empty($vtkArgs['service'])) {
                $vtkArgs['service'] = grab_request_var('servicename', NULL);
            }
        }

        // Establish necessary vars
        $vtkArgs['start'] = grab_request_var('start', '-24h');
        $vtkArgs['end'] = grab_request_var('end','');
        $vtkArgs['container'] = grab_request_var('div', 'visContainer');
        $vtkArgs['filter'] = grab_request_var('filter','');
        $vtkArgs['height'] = grab_request_var('height', 500);
        $vtkArgs['width'] = grab_request_var('width');
        $vtkArgs['subtitle'] = grab_request_var('subtitle', 1);
        $vtkArgs['view'] = grab_request_var('view', -1);
        $vtkArgs['link'] = grab_request_var('link', '');
        $vtkArgs['render_mode'] = grab_request_var('render_mode', '');
        $vtkArgs['no_legend'] = grab_request_var('no_legend', 0);
        debug($vtkArgs);
        return true;
    }

    return false;
}


/**
 * Request $vars are valid so far, fetch appropriate graph
 *
 * @return string JSON object or jquery string
 */
function fetch_graph()
{
    global $vtkArgs;

    // Determine graph type 
    $graph = '';
    $datasource = '';

    switch ($vtkArgs['type'])
    {
        ////////////////////////////////////TIMELINE/////////////////////////////////////
        case 'perfdata':
        case 'timeline':
            // Timeline requirements  
            if(!isset($vtkArgs['host'])) die("Host name is required for timeline graph");
            if(!isset($vtkArgs['service'])) $vtkArgs['service'] =  '_HOST_';
            $vtkArgs['title'] = $vtkArgs['host'].' : '.$vtkArgs['service'];
            require(dirname(__FILE__).'/templates/'.$vtkArgs['type'].'.inc.php');

            // Make a get call to the fetch_rrd to grab the data and do JSON encode 
            $xmlDoc = '/usr/local/nagios/share/perfdata/'.pnp_convert_object_name($vtkArgs['host']).'/'.pnp_convert_object_name($vtkArgs['service']).'.xml';

            // Get the xmlDoc and units of measurement/names
            if (file_exists($xmlDoc)) {
                $xmlDat = simplexml_load_file($xmlDoc);
                $vtkArgs['units'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/UNIT'); // Units of measurement from perfdata 
                $vtkArgs['names'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/NAME'); // Perfdata names (rta and pl)

                // Replace %% with %
                foreach ($vtkArgs['units'] as $i => $unit) {
                    if ($unit == '%%') { $vtkArgs['units'][$i] = '%'; }
                }

                // Get datasource to add warning critical lines to all graphs
                $datasource = $xmlDat->xpath('/NAGIOS/DATASOURCE');

                $vtkArgs['datatypes'] = $vtkArgs['names'];
            }

            $vtkArgs['show_thresh'] = true;

            // Don't display for graphs with multiple data sources
            if (count($datasource) <= 1) {
                // Check if warning/crit are empty
                foreach ($datasource as $k => $v) {
                    // Get base and ranges from XML if they exist
                    $warn = (float) $v->WARN[0];
                    $warn_range = (string) $v->WARN_RANGE_TYPE[0];
                    $warn_min = (int) $v->WARN_MIN;
                    $warn_max = (int) $v->WARN_MAX;

                    $crit = (float) $v->CRIT[0];
                    $crit_range = (string) $v->CRIT_RANGE_TYPE[0];
                    $crit_min = (int) $v->CRIT_MIN;
                    $crit_max = (int) $v->CRIT_MAX;

                    $warn_xml = array(
                        'warn' => $warn,
                        'range' => $warn_range,
                        'min' => $warn_min,
                        'max' => $warn_max
                    );

                    $crit_xml = array(
                        'crit' => $crit,
                        'range' => $crit_range,
                        'min' => $crit_min,
                        'max' => $crit_max
                    );

                    // create args for warning and critical to send to chart templates
                    if ($warn == 0 && $warn_xml['range'] !== "") {
                        $vtkArgs['warning'] = $warn_xml;
                    } else {
                        if ($warn_xml != 0 || $warn_xml != "") {
                            $vtkArgs['warning'] = $warn;
                        }
                    }

                    if ($crit == 0 && $crit_xml['range'] !== "") {
                        $vtkArgs['critical'] = $crit_xml;
                    } else {
                        if ($crit_xml != 0 || $crit_xml != "") {
                            $vtkArgs['critical'] = $crit;
                        }
                    }
                }
            } else {
                $vtkArgs['show_thresh'] = false;
            }

            // Warning/ Critical display value
            $vtkArgs['wc_enable'] = get_option('wc_enable', 1);
            $vtkArgs['wc_display'] = get_option('wc_display', 0);

            // Get graph scale
            $vtkArgs['highchart_scale'] = get_option('highchart_scale', 'linear');

            // Show or hide RRD stats
            $vtkArgs['show_rrd_stats'] = grab_request_var('show_rrd_stats', get_option('show_rrd_stats', 0));
            
            // Make start date
            $end = grab_array_var($vtkArgs, 'end', null);
            $vtkArgs['start'] = graph_format_start_time($vtkArgs['start'], $vtkArgs['view'], $end);
            $vtkArgs['end'] = $end;

            // Retrieve RRD data if it's available
            $vtkArgs['nodata'] = false;
            if ($rrd = fetch_rrd($vtkArgs)) {
                // Add ability to filter performance data sets
                if (isset($vtkArgs['filter']) && $vtkArgs['filter'] != '') {
                    $filter = $vtkArgs['filter'];
                    $vtkArgs['datastrings'][] = $rrd['sets'][$filter] ; 
                } else {
                    $vtkArgs['datastrings'] = $rrd['sets']; 
                }
                $vtkArgs['count'] = $rrd['count']; // Data points retrieved 
                $vtkArgs['increment'] = $rrd['increment'];
            } else {
                $vtkArgs['nodata'] = true;
                $vtkArgs['count'] = 0;
                $vtkArgs['increment'] = 0;
            }

            // If we know we are using logarithmic scale, change null to 0 until we can upgrade to highcharts 5.x.x
            if ($vtkArgs['highchart_scale'] != 'linear') {
                foreach ($vtkArgs['datastrings'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if ($v == 'null' || $v <= 0 || empty($v)) {
                            $vtkArgs['datastrings'][$key][$k] = 'null';
                        }
                    }
                }
            }

            $vtkArgs['start'] .= '000'; // Make javacscript start time

            // Create label for unit of measurement
            if (isset($vtkArgs['filter']) && $vtkArgs['filter'] != '') {
                $vtkArgs['UOM'] = $vtkArgs['units'][$filter];
                $vtkArgs['names'][0] = $vtkArgs['names'][$filter];
            } else { // No filter defined
                $vtkArgs['UOM'] = '';
                // Concatenate UOM string for multiple data sets
                if (isset($vtkArgs['units'])) {
                    for ($i = 0; $i < count($vtkArgs['units']); $i++) {
                        $unit = $vtkArgs['units'][$i];
                        if ($unit == "%%") { $unit = "%"; }
                        $vtkArgs['UOM'] .= $unit.' ';
                    }
                }
            }

            // Misc vars for timeline
            if (!isset($vtkArgs['container'])) {
                $vtkArgs['container'] = 'visContainer';
            }

            // Lets create a URL to the host/service data pages
            $vtkArgs['service'] = str_replace("_", "+", $vtkArgs['service']);
            if (empty($vtkArgs['link'])) {
                if ($vtkArgs['service'] == "_HOST_" || $vtkArgs['service'] == '+HOST+' || $vtkArgs['service'] == "HOST") {
                    $hs_url = get_base_url() . "/includes/components/xicore/status.php?show=hostdetail&host=" . $vtkArgs['host'];
                } else {
                    $link_srv = str_replace("+", " ", $vtkArgs['service']);
                    $hs_url = get_base_url() . "/includes/components/xicore/status.php?show=servicedetail&host=" . $vtkArgs['host'] . "&service=" . urlencode($link_srv);
                }
            } else {
                $hs_url = base64_decode($vtkArgs['link']);
            }

            $vtkArgs['hs_url'] = encode_form_valq($hs_url);

            if ($vtkArgs['type'] == "perfdata") {
                $graph = fetch_timeline_perfdata($vtkArgs);
            } else {
                $graph = fetch_timeline($vtkArgs);
            }
            break;

        ////////////////////////////////////BAR/////////////////////////////////////
        case 'bar':
            require(dirname(__FILE__).'/templates/bar.inc.php');

            $subType = grab_request_var('opt'); 
            switch($subType)
            {
                ///////////////////////////////TOP ALERT PRODUCERS//////////////
                case 'topalerts':
                    require_once(dirname(__FILE__).'/../../utils-reports.inc.php');

                    // Determine start/end times based on period
                    $reportperiod = grab_request_var("reportperiod", "last24hours");
                    $startdate = grab_request_var("startdate", "");
                    $enddate = grab_request_var("enddate", "");
                    get_times_from_report_timeperiod($reportperiod, $starttime, $endtime, $startdate, $enddate);

                    $args = array(
                        "starttime" => $starttime,
                        "endtime" => $endtime,
                        "records" => "10:0"
                    );
                    $xml = get_xml_topalertproducers($args);
                    $categories = array();
                    $alerts = array();

                    $c = 0;
                    foreach ($xml->producer as $node)
                    {
                        $categories[] = "$node->host_name<br>$node->service_description";
                        $alerts[] = intval("{$node->total_alerts}");
                        $c++;
                        if ($c > 8) break;
                    }
                    echo "//Categories count is: ".count($categories);
                    echo "//Alerts count is: ".count($alerts);

                    $args['title'] = 'Top Alert Producers Last 24 Hours';
                    $args['yTitle'] = '';
                    $args['names'] = 'Alerts';
                    $args['categories'] = json_encode($categories); // Hosts names/Service descriptions
                    $args['data'] = json_encode($alerts); // Alert counts 
                    $args['container'] = $vtkArgs['container']; // Render to div  
                    $args['height'] = $vtkArgs['height'];
                    $graph = fetch_bargraph($args);
                    break;

                default:
                    echo "alert('The bar is not ready yet');";
                    break;
            }
            break;

        ////////////////////////////////////PIE/////////////////////////////////////
        case 'pie':
            $pieType = grab_request_var('opt');
            $args = array();
            $args['pieType'] = $pieType; 
            $args['height'] = $vtkArgs['height'];
            $args['url'] = get_base_url().'/includes/components/xicore/status.php?&show=';

            switch ($pieType)
            {
                case 'hosthealth':
                    $backendargs = array('brevity' => 3);
                    $hostXML = get_xml_host_status($backendargs);
                    $total = intval($hostXML->recordcount);
                    $hosts = $hostXML->hoststatus;
                    $up = 0;
                    $down = 0;
                    $unreachable = 0;
                    foreach ($hosts as $host)
                    {
                        switch ($host->current_state)
                        {
                            case 0: $up++; break;
                            case 1: $down++; break;
                            case 2: $unreachable++; break;
                            default: break;
                        }
                    }
                    print "// Total records: $total  UP: $up, DOWN $down, UR $unreachable \n";
                    $up = number_format( ($up*100)/ $total, 2);
                    $down = number_format( ($down*100)/ $total, 2);
                    $unreachable = number_format( ($unreachable*100)/ $total, 2);
                    $args['datastring'] = "['UP', $up], ['DOWN', $down], ['UNREACHABLE', $unreachable]";
                    $args['title'] = 'Host Health';
                    $args['subtitle'] = 'Host Health Percentage';
                    $args['url'] .= 'hosts&hoststatustypes=';
                    break;

                case 'servicehealth':
                    $backendargs = array('brevity' => 3); 
                    $serviceXML = get_xml_service_status($backendargs);
                    $total = intval($serviceXML->recordcount);
                    $services = $serviceXML->servicestatus; 
                    $ok = 0;
                    $warning=0;
                    $critical = 0;
                    $unknown = 0;
                    foreach ($services as $service)
                    {
                        switch ($service->current_state)
                        {
                            case 0: $ok++; break;
                            case 1: $warning++; break;
                            case 2: $critical++; break;
                            case 3: $unknown++; break;
                            default: break; 
                        }
                    }
                    print "// Total records: $total  OK: $ok, WARNING $warning, CRIT $critical  UK $unknown \n";
                    $ok = number_format( ($ok*100)/ $total, 2);
                    $warning = number_format(($warning*100) / $total, 2);
                    $critical = number_format(($critical*100) / $total, 2);
                    $unknown = number_format(($unknown*100) / $total, 2);
                    $args['datastring'] = "['OK', $ok], ['WARNING', $warning], ['CRITICAL', $critical], ['UNKNOWN', $unknown]"; 
                    $args['title'] = 'Service Health';
                    $args['subtitle'] = 'Service Health Percentage';
                    $args['url'] .='services&hoststatustypes=0&servicestatustypes=';
                    break;

                default:
                    break;
            }

            $args['container'] = $vtkArgs['container']; 
            require_once(dirname(__FILE__).'/templates/pie.inc.php');
            $graph = fetch_piegraph($args); 
            break;
        
        ////////////////////////////////STACK/////////////////////////////
        case 'stack':

            // Timeline requirements  
            if (!isset($vtkArgs['host'])) die("Host name is required for timeline graph");
            if (!isset($vtkArgs['service'])) $vtkArgs['service'] =  '_HOST_';
            require(dirname(__FILE__).'/templates/timeline.inc.php');

            // Determine days, weeks, months, etc
            $host = $vtkArgs['host'];
            $service = $vtkArgs['service'];
            $opt = grab_request_var('opt', '');

            // Calculate starts and stops for last 3 timeperiods
            switch ($opt)
            {
                case 'weeks':
                    $starts = array('-7d', '-14d', '-21d');
                    $stops = array('', '-7d', '-14d');
                    $res = 1800;
                    break;

                case 'months':
                    $starts = array('-30d', '-60d', '-90d');
                    $stops = array('', '-30d', '-60d');
                    $res = 900;
                    break;

                // Defaults to last 3 days
                case 'days':
                default:
                    $starts = array('-24h', '-48h', '-72h');
                    $stops = array('', '-24h', '-48h');
                    $res = 300;
                    $opt = 'days';
                    break;
            }

            $args = array();
            $args['host'] = $host;
            $args['service'] = $service;

            // RRD fetch the 3 sets of data 
            $rrd1 = fetch_rrd(array('host' => $host, 'service' => $service, 'start' => $starts[0], 'end' => $stops[0], 'resolution' => $res));
            $rrd2 = fetch_rrd(array('host' => $host, 'service' => $service, 'start' => $starts[1], 'end' => $stops[1], 'resolution' => $res));
            $rrd3 = fetch_rrd(array('host' => $host, 'service' => $service, 'start' => $starts[2], 'end' => $stops[2], 'resolution' => $res));

            // Get UOM for graph from xml file 
            $xmlDoc = '/usr/local/nagios/share/perfdata/'.pnp_convert_object_name($host).'/'.pnp_convert_object_name($service).'.xml';
            $xmlDat = simplexml_load_file($xmlDoc);

            // Retrieve UOM as xml objects -> print in double quotes to display text value correctly 
            $vtkArgs['units'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/UNIT'); // UOM from perfdata 
            $args['datatypes'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/NAME'); // Perfdata names (rta and pl)

            // Get datasource to add warning critical lines to all graphs
            $datasource = $xmlDat->xpath('/NAGIOS/DATASOURCE');

            // Check if warning/crit are empty
            foreach ($datasource as $k => $v) {
                $warn_xml = (int) $v->WARN[0];
                if ($warn_xml != 0 || $warn_xml != "") {
                    $args['warning'] = $warn_xml;
                }

                $crit_xml = (int) $v->CRIT[0];
                if ($crit_xml != 0 || $crit_xml != "") {
                    $args['critical'] = $crit_xml;
                }
            }

            // Warning/ Critical display value
            $args['wc_enable'] = get_option('wc_enable', 1);
            $args['wc_display'] = get_option('wc_display', 0);

            // Process and format data set for graph
            // automatically filter this graph for cleaner data
            if (isset($vtkArgs['filter']) && $vtkArgs['filter'] != '') {
                $filter = $vtkArgs['filter'];
            } else {
                $filter = 0;
            }

            $args['datastrings'] = array(); 
            $args['datastrings'][] = $rrd1['sets'][$filter];
            $args['datastrings'][] = $rrd2['sets'][$filter];
            $args['datastrings'][] = $rrd3['sets'][$filter];

            $args['start'] = $rrd1['start'].'000'; // Starting timestamp for actual data grabbed, ms
            $args['count'] = $rrd1['count']; // Data points retrieved 
            $args['increment'] = $rrd1['increment'];

            // Create label for unit of measurement
            $args['UOM'] = $vtkArgs['units'][$filter];
            $names = array("{$starts[0]} -> Now","{$starts[1]} -> {$stops[1]}","{$starts[2]} -> {$stops[2]}");
            $args['names'] = $names;

            // Misc vars for timeline
            if (!isset($vtkArgs['container']) || $vtkArgs['container'] == '') {
                $args['container'] = 'visContainer'; 
            } else {
                $args['container'] = $vtkArgs['container'];
            }
            $args['title'] = $host.' : '.$service.' - Last 3 '.$opt;
            $args['height'] = $vtkArgs['height'];

            // Pass array to graph for display 
            $graph = fetch_timeline($args); 
            break;

        ///////////////////////////MULTISTACK/////////////////////////////
        case 'multistack':

            // Require host/service combos for display
            if (empty($vtkArgs['hslist'])) die("Host and service list is required for a multi stacked timeline graph");
            require(dirname(__FILE__).'/templates/multistack.inc.php');

            $args['datastrings'] = array();
            $args['names'] = array();
            $args['title'] = '';

            $hs_count = 0;
            $hslist = $vtkArgs['hslist'];
            $hs_count = count($hslist);

            $hn = array();
            $sv = array();
            $dt = array();
            foreach ($hslist as $k => $x) {
                $hn[$k] = $x['host'];
                $sv[$k] = $x['service'];
                $dt[$k] = $x['datatype'];
            }
            array_multisort($hn, SORT_ASC, $sv, SORT_ASC, $dt, SORT_ASC, $hslist);

            // Split up the hostservice array and run each 
            for ($i = 0; $i < count($hslist); $i++) {
                $host = $hslist[$i]['host'];
                $service = $hslist[$i]['service'];
                $datatype = $hslist[$i]['datatype'];
                $end = grab_array_var($vtkArgs, 'end', '');
                $start =  graph_format_start_time($vtkArgs['start'], $vtkArgs['view'], $end);
                $vtkArgs['end'] = $end;

                // Fetch RRD for each service
                $rrd1 = fetch_rrd(array('host' => $host,
                                        'service' => $service,
                                        'start' => $start,
                                        'end' => $end,
                                        'resolution' => 300));

                // Get UOM for graph from xml file 
                $xmlDoc = '/usr/local/nagios/share/perfdata/'.pnp_convert_object_name($host).'/'.pnp_convert_object_name($service).'.xml';
                $xmlDat = simplexml_load_file($xmlDoc);

                // Retrieve UOM as xml objects -> print in double quotes to display text value correctly
                $vtkArgs['units'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/UNIT'); // UOM from perfdata 
                $args['datatypes'] = $xmlDat->xpath('/NAGIOS/DATASOURCE/NAME'); // Perfdata names (rta and pl)

                // Get datasource to add warning critical lines to all graphs
                $datasource = $xmlDat->xpath('/NAGIOS/DATASOURCE');

                $args['show_thresh'] = true;

                // Check if warning/crit are empty
                foreach ($datasource as $k => $v) {
                    // Check if we are viewing one and display warn/crit buttons
                    if ($hs_count == 1) {
                        // Get base and ranges from XML if they exist
                        $warn = (float) $v->WARN[0];
                        $warn_range = (string) $v->WARN_RANGE_TYPE[0];
                        $warn_min = (int) $v->WARN_MIN;
                        $warn_max = (int) $v->WARN_MAX;

                        $crit = (float) $v->CRIT[0];
                        $crit_range = (string) $v->CRIT_RANGE_TYPE[0];
                        $crit_min = (int) $v->CRIT_MIN;
                        $crit_max = (int) $v->CRIT_MAX;

                        $warn_xml = array(
                            'warn' => $warn,
                            'range' => $warn_range,
                            'min' => $warn_min,
                            'max' => $warn_max
                        );

                        $crit_xml = array(
                            'crit' => $crit,
                            'range' => $crit_range,
                            'min' => $crit_min,
                            'max' => $crit_max
                        );

                        // Create args for warning and critical to send to chart templates
                        if ($warn == 0 && $warn_xml['range'] !== "") {
                            $args['warning'] = $warn_xml;
                        } else {
                            if ($warn_xml != 0 || $warn_xml != "") {
                                $args['warning'] = $warn;
                            }
                        }

                        if ($crit == 0 && $crit_xml['range'] !== "") {
                            $args['critical'] = $crit_xml;
                        } else {
                            if ($crit_xml != 0 || $crit_xml != "") {
                                $args['critical'] = $crit;
                            }
                        }
                    } else {
                        // Create args for warning and critical to send to chart templates
                        $warn_xml = (int) $v->WARN[0];
                        if ($warn_xml != 0 || $warn_xml != "") {
                            $args['warning'] = $warn_xml;
                        }

                        $crit_xml = (int) $v->CRIT[0];
                        if ($crit_xml != 0 || $crit_xml != "") {
                            $args['critical'] = $crit_xml;
                        }

                        $args['show_thresh'] = false;
                    }
                }

                $args['datastrings'][] = $rrd1['sets'][$datatype];

                $args['start'] = $start . '000'; // Starting timestamp for actual data grabbed, ms
                $args['count'] = $rrd1['count']; // Data points retrieved 
                $args['increment'] = $rrd1['increment'];

                // Create label for unit of measurement
                $unit = (string) $vtkArgs['units'][$datatype];
                if ($unit == "%%") { $unit = "%"; }
                $args['UOM'][] = $unit;
                $args['names'][] = $service . ' (' . $host . ') [' . $args['datatypes'][$datatype] . ']';

                // Misc vars for timeline
                if (!isset($vtkArgs['container']) || $vtkArgs['container'] == '') {
                    $args['container'] = 'visContainer';
                } else {
                    $args['container'] = $vtkArgs['container'];
                }
                $args['height'] = $vtkArgs['height'];

            }

            // Warning/ Critical display value
            $args['wc_enable'] = get_option('wc_enable', 1);
            $args['wc_display'] = get_option('wc_display', 0);
            $args['linetype'] = $vtkArgs['linetype'];

            $graph = fetch_multistack($args);
            break;

        ///////////////////////////DEFAULT/////////////////////////////
        default:
            include(dirname(__FILE__).'/templates/default.inc.php');        
            break;

    }

    return $graph;
}


/**
 * Prints a list of host selection options
 *
 * @return null prints output directly to browser
 */
function show_perfdata_hosts()
{
    global $cfg;

    // Get hosts with perfdata only
    $perfdata = $cfg['component_info']['pnp']['perfdata_dir'];
    $content = "";

    // Get combined list from backend 
    $args = array('cmd' => 'getobjects',
              'brevity' => 3, 
              'orderby' =>'name1:a',
              'objecttype_id' => 'in:1'); // Get anything that's a host
    $objsXML = get_xml_objects($args);

    // Get unique hosts
    foreach ($objsXML as $obj) {
        $hosts["$obj->name1"] = "$obj->name1";
    }

    foreach ($hosts as $host) {
        $hostdir = pnp_convert_object_name($host); 

        // Create list of hosts with perfdata
        if (!empty($host)) {
            $host_rrd_dir = $perfdata.'/'.$hostdir; 
            if (is_dir($host_rrd_dir)) {
                $content .= "<option value='" . $host . "'>" . $host . "</option>";
            }
        }
    }

    echo $content;
}


/**
 * Prints a list of services based on host
 *
 * @param $host
 * @return null prints output directly to browser
 */
function show_perfdata_services($host)
{
    global $cfg;

    // Get hosts with perfdata only
    $perfdata = $cfg['component_info']['pnp']['perfdata_dir'];
    $dirs = scandir($perfdata);

    // Get perfdata directory array 
    foreach ($dirs as $dir) {
        if ($dir[0]!='.' || $dir[0] !='..') $hosts[] = $dir;   
    }

    // Get combined list from backend 
    $args = array('cmd' => 'getobjects',
              'brevity' => 3, 
              'orderby' =>'name2:a',
              'objecttype_id' => 'in:2', // Get anything that's a service
              'name1' => $host);
    $objsXML = get_xml_objects($args);

    echo "<option value='_HOST_'>_HOST_</option>";

    foreach ($objsXML as $obj) {
        $type = intval($obj->objecttype_id);
        $service = "$obj->name2";
        $hostdir = pnp_convert_object_name($host); 

        // Capture actual hostname 
        $full_hostdir = $perfdata.'/'.$hostdir.'/';
        $servicerrd = pnp_convert_object_name($service);
        if (file_exists($full_hostdir.$servicerrd.'.rrd')) {
            echo "<option value='" . $service . "'>" . $service . "</option>";
        }
    }
}


/**
 * @param $host
 * @param $service
 * @param $all
 */
function show_service_datatypes($host, $service, $all)
{
    $host = str_replace(" ", "_", $host);
    $service = str_replace(" ", "_", $service);

    $host = ($host == "") ? "localhost" : $host;
    $service = ($service == "") ? "_HOST_" : $service;

    $xmlDoc = '/usr/local/nagios/share/perfdata/'.pnp_convert_object_name($host).'/'.pnp_convert_object_name($service).'.xml';
    if (file_exists($xmlDoc)) {
        $xmlDat = simplexml_load_file($xmlDoc); 
        $datatypes = $xmlDat->xpath('/NAGIOS/DATASOURCE/NAME'); //perfdata names (rta and pl)
        if ($all && count($datatypes) > 1) { echo '<option value="">All</option>'; }
        foreach ($datatypes as $k => $dt) {
            echo '<option value="' . $k . '">' . $dt . '</option>';
        }
    }
}


/**
 * Function to output all tracks for a host-service as options
 *
 * @param $host
 * @param $service
 * @param $all
 */
function show_service_tracks($host, $service, $all)
{
    $host = str_replace(" ", "_", $host);
    $service = str_replace(" ", "_", $service);
        $service = ($service == "") ? "_HOST_" : $service;
    $xmlDoc = '/usr/local/nagios/share/perfdata/'.pnp_convert_object_name($host).'/'.pnp_convert_object_name($service).'.xml';
    $xmlDat = simplexml_load_file($xmlDoc);
    $datatypes = $xmlDat->xpath('/NAGIOS/DATASOURCE/NAME'); //perfdata names (rta and pl)
    if ($all && count($datatypes) > 1) { echo '<option value="">All</option>'; }
    foreach ($datatypes as $k => $dt) {
        echo '<option value="' . $dt . '">' . $dt . '</option>';
    }
}


/**
 * @param $text
 */
function debug_logger($text)
{
    $f = fopen(get_tmp_dir().'/debugger.log','ab'); 
    fwrite($f,$text); 
    fclose($f);     
}


/**
 * Function to overwrite exporting functionality in highcharts - this is necessary
 * so as not to force added functionality to pie/bar/etc. charts - only the ones we want
 *
 * @return string containing the html/js to put in place of the 'exporting:' option in highcharts constructors
 */
function overwrite_exporting_buttons() {
    return "contextButton: {
                symbol: 'menu',
                _titleKey: 'contextButtonTitle',
                menuItems: [{
                    textKey: 'printChart',
                    onclick: function() { this.print(); }
                },{
                    separator: true
                },{
                    textKey: 'downloadPNG',
                    onclick: function() { this.exportChart({ type: 'image/png' }); }
                },{
                    textKey: 'downloadJPEG',
                    onclick: function() { this.exportChart({ type: 'image/jpeg' }); }
                },{
                    textKey: 'downloadPDF',
                    onclick: function() { this.exportChart({ type: 'application/pdf' }); }
                },{
                    textKey: 'downloadSVG',
                    onclick: function() { this.exportChart({ type: 'image/svg+xml' }); }
                },{
                    separator: true
                },{
                    text: 'Download CSV',
                    onclick: function() { exporting_url(this.options.exporting, 'csv'); }
                },{
                    text: 'Download XML',
                    onclick: function() { exporting_url(this.options.exporting, 'xml'); }
                },{
                    text: 'Download JSON',
                    onclick: function() { exporting_url(this.options.exporting, 'json'); }
                }]
        },";
}