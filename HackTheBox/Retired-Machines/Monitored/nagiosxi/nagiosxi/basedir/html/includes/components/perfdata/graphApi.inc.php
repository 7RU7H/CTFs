<?php
//
// Perfdata Output Functions
//
// Copyright (c) 2008-2018 Nagios Enterprises, LLC.  All rights reserved.
//

require_once(dirname(__FILE__)."/../componenthelper.inc.php");


// NOTE: This file is not included automatically
// If you wish to use this functionality, you need to
// manually include it!!


// get the appropriate command for image generation with `rrdtool graph`
function fetch_graph_command($hostname, $servicedesc, $source, $view, $start, $end, $filename = "-")
{
    global $cfg;

    $hostname    = pnp_convert_object_name($hostname);
    $servicedesc = pnp_convert_object_name($servicedesc);

    if (empty($source)) {
        $source = 1;
    }

    // vars for rrdtool graph query
    $rrdgraph = perfdata_rrdtool_path() . " graph";

    $perf_dir = "/usr/local/nagios/share/perfdata";
    if (!empty($cfg["component_info"]["pnp"]["perfdata_dir"])) {
        $perf_dir = $cfg["component_info"]["pnp"]["perfdata_dir"];
    }

    // make sure we get the appropriate timeperiod
    $timeperiod = calculate_graph_timeperiod($view, $start, $end);

    // clear cached file stats to make sure we have fresh data 
    clearstatcache(); 

    // build rrd/xml file location
    if ($servicedesc != "") {
        $file = "{$perf_dir}/{$hostname}/{$servicedesc}";
    }
    else {
        $file = "{$perf_dir}/{$hostname}/_HOST_";
    }

    $rrdfile = $file . ".rrd";
    $xmlfile = $file . ".xml";

    // stop script if rrd doesn't exist 
    if (!file_exists($rrdfile)) {
        die(_("No performance data available"));
    }

    // fetch template
    $template_string = fetch_graph_template($hostname, $servicedesc, $rrdfile, $xmlfile, $source);

    // set graph options
    $opts = " --width=500 --height=100 ";

    $cmd_string = "{$rrdgraph} {$filename} {$opts} {$timeperiod} {$template_string}";

    return $cmd_string;
}


// checks if template exists
// returns defined template string on success, default template string on failure 
function fetch_graph_template($hostname, $servicedesc, $rrdfile, $xmlfile, $source)
{
    // TODO
    // $source is the XML index for templates and datasources 

    // necessary template vars 
    // force indexes to start at 1 for PNP template compatibility
    $RRDFILE  = array("");
    $TEMPLATE = array("");
    $DS       = array("");
    $NAME     = array("");
    $LABEL    = array("");
    $UNIT     = array("");
    $ACT      = array("");
    $MIN      = array("");
    $MAX      = array("");
    $WARN     = array("");
    $CRIT     = array("");
    $WARN_MIN = array("");
    $WARN_MAX = array("");
    $CRIT_MIN = array("");
    $CRIT_MAX = array("");

    // load xml file for data
    $xmlDat = simplexml_load_file($xmlfile);

    // load xml into arrays
    $RRDFILE             = array_merge($RRDFILE,    $xmlDat->xpath("/NAGIOS/DATASOURCE/RRDFILE"));
    $TEMPLATE            = array_merge($TEMPLATE,   $xmlDat->xpath("/NAGIOS/DATASOURCE/TEMPLATE"));
    $DS                  = array_merge($DS,         $xmlDat->xpath("/NAGIOS/DATASOURCE/DS"));
    $NAME                = array_merge($NAME,       $xmlDat->xpath("/NAGIOS/DATASOURCE/NAME"));
    $LABEL               = array_merge($LABEL,      $xmlDat->xpath("/NAGIOS/DATASOURCE/LABEL"));
    $UNIT                = array_merge($UNIT,       $xmlDat->xpath("/NAGIOS/DATASOURCE/UNIT"));
    $ACT                 = array_merge($ACT,        $xmlDat->xpath("/NAGIOS/DATASOURCE/ACT"));
    $MIN                 = array_merge($MIN,        $xmlDat->xpath("/NAGIOS/DATASOURCE/MIN"));
    $MAX                 = array_merge($MAX,        $xmlDat->xpath("/NAGIOS/DATASOURCE/MAX"));
    $WARN                = array_merge($WARN,       $xmlDat->xpath("/NAGIOS/DATASOURCE/WARN"));
    $CRIT                = array_merge($CRIT,       $xmlDat->xpath("/NAGIOS/DATASOURCE/CRIT"));

    $NAGIOS_TIMET        = $xmlDat->xpath("/NAGIOS/NAGIOS_TIMET");
    $NAGIOS_LASTHOSTDOWN = $xmlDat->xpath("/NAGIOS/NAGIOS_LASTHOSTDOWN");
    $NAGIOS_TIMET        = $NAGIOS_TIMET[0];

    $NAGIOS_LASTHOSTDOWN = isset($NAGIOS_LASTHOSTDOWN[0]) ? $NAGIOS_LASTHOSTDOWN[0] : "";

    
    // refactor DS array  --> moved for all templates on 11/15/2011, all DS arrays need to be refactored.
    $ds = array();

    // worth commenting here, since it was encountered
    // that you can not replace with an !empty()
    // or get rid of the quotes
    // it will literally break everything!
    foreach ($DS as $D) {
        if ("$D" != "") {
            $ds["$D"] = "$D";
        }
    }

    $DS = $ds;
    $lower = " ";

    // default template
    $template_base = "/usr/local/nagios/share/pnp/templates";
    $template = "{$template_base}.dist/default.php";
    
    // check if template exists

    // /usr/local/nagios/share/pnp/templates
    if (file_exists("{$template_base}/{$TEMPLATE[$source]}.php")) {
        $template = "{$template_base}/{$TEMPLATE[$source]}.php";
    }

    // /usr/local/nagios/share/pnp/templates.dist
    else if(file_exists("{$template_base}.dist/{$TEMPLATE[$source]}.php")) {
        $template = "{$template_base}.dist/{$TEMPLATE[$source]}.php";
    }

    // /usr/local/nagios/share/pnp/templates.special
    else if(file_exists("{$template_base}.special/{$TEMPLATE[$source]}.php")) {
        $template = "{$template_base}.special/{$TEMPLATE[$source]}.php";
    }


    // call appropriate template include
    include($template);


    // build template command string
    // what to do about datasource label????
    $opt_string = grab_array_var($opt, $source, "");
    $def_string = grab_array_var($def, $source, "");

    // assemble full string 
    $template_string = "{$opt_string} {$def_string}";

    return $template_string;
}


// calculates graph duration and returns the string commmand value for rrdtool 
function calculate_graph_timeperiod($view, $start, $end)
{
    $pnp_views  = perfdata_pnp_views();
    $pnp_stamps = perfdata_pnp_timestamps();
    $timeperiod = "";
    
    // calculate all possible combos and return start/end string

    // default PNP views, no start/end defined   
    if ($view !== "" && $start === "" && $end === "") {
        
        if (!empty($pnp_views[$view])) {
            $timeperiod = " --start={$pnp_views[$view]} ";
        }
    }

    // specific date
    else if (!empty($start) && !empty($end)) {

        $start = escapeshellarg($start);
        $end   = escapeshellarg($end);
        
        $timeperiod = " --start={$start} --end={$end} ";
    
    }

    // specific start
    else if (!empty($start) && empty($end)) {
        
        $start = escapeshellarg($start);
        $timeperiod = " --start={$start} ";

    }

    // end/view combo
    else if ($view !== "" && $end !== "" && $start === "") {

        if (!empty($pnp_stamps[$view])) {

            $end = intval($end);

            //calculate starting timestamp
            $start = $end - $pnp_stamps[$view];
            $timeperiod = " --start={$start} --end={$end} ";
        }
    }   

    return $timeperiod;
}
