<?php
// PNP Sub-Component Functions
//
// Copyright (c) 2008-2015 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id$

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');
include_once(dirname(__FILE__) . '/pnpproxy.inc.php');

define("PNP_VIEW_DEFAULT", 1);
define("PNP_VIEW_4HOURS", 0);
define("PNP_VIEW_1DAY", 1);
define("PNP_VIEW_1WEEK", 2);
define("PNP_VIEW_1MONTH", 3);
define("PNP_VIEW_1YEAR", 4);

define("PNP_VIEW_CUSTOM", 99);

define("PNP_DEFAULT_SERVICE", "_HOST_");


// run the initialization function
pnp_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function pnp_component_init()
{

    $name = "pnp";

    $args = array(

        // need a name
        COMPONENT_NAME => $name,

        // informative information
        //COMPONENT_VERSION => "1.1",
        //COMPONENT_DATE => "11-27-2009",
        COMPONENT_TITLE => "PNP Integration",
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => "Provides integration with PNP.",
        COMPONENT_COPYRIGHT => "Copyright (c) 2009 Nagios Enterprises",
        //COMPONENT_HOMEPAGE => "https://www.nagios.com",

        // do not delete
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE,

        // configuration function (optional)
        //COMPONENT_CONFIGFUNCTION => "pnp_component_config_func",
    );

    register_component($name, $args);
}


////////////////////////////////////////////////////////////////////////
// BASIC FUNCTIONS
////////////////////////////////////////////////////////////////////////

function pnp_get_share_dir()
{
    global $cfg;
    return $cfg["component_info"]["pnp"]["share_dir"];
}

function pnp_get_perfdata_dir()
{
    global $cfg;
    return $cfg["component_info"]["pnp"]["perfdata_dir"];
}

/**
 * @param string $hostname
 *
 * @return string
 */
function pnp_get_host_perfdata_dir($hostname = "")
{
    return pnp_get_perfdata_dir() . "/" . pnp_convert_object_name($hostname);
}

/**
 * @param string $hostname
 * @param string $servicename
 *
 * @return string
 */
function pnp_get_perfdata_file($hostname = "", $servicename = "_HOST_")
{
    $hostdir = pnp_get_host_perfdata_dir($hostname) . "/";
    if ($servicename == "")
        $fname = PNP_DEFAULT_SERVICE . ".rrd";
    else
        $fname = pnp_convert_object_name($servicename) . ".rrd";
    $p = $hostdir . $fname;
    return $p;
}

/**
 * @param $raw
 *
 * @return mixed
 */
function pnp_convert_object_name($raw)
{
    //print $raw;
    // Old regex to replace ... '`[^a-z0-9\.\-#]`i'
    $name = preg_replace('`[:~\`!@$%\^&*()\\\/ "=|]`i', '_', urldecode($raw));
    return $name;
}

/**
 * @param string $hostname
 * @param string $servicename
 *
 * @return bool
 */
function pnp_chart_exists($hostname = "", $servicename = "_HOST_")
{

    if ($hostname == "")
        return false;

    $path = pnp_get_perfdata_file($hostname, $servicename);

    if (file_exists($path) === FALSE)
        return false;

    return true;
}


////////////////////////////////////////////////////////////////////////
// CHART QUERY FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * @param string $hostname
 *
 * @return array
 */
function pnp_get_all_data_for_host($hostname = "")
{

    $a = array();

    // directory for this hosts' performance data
    $dir = pnp_get_host_perfdata_dir($hostname);
    $a["dir"] = $dir;
    $a["pnphost"] = $hostname;

    // services associated with this host
    $a["services"] = array();

    // find all services
    $files = scandir($dir);
    foreach ($files as $file) {

        //echo "FILE: $file<BR>";

        $ext = substr($file, -4);

        // we found an RRD file - that means we have a valid service with a chart
        if ($ext == ".rrd") {

            // get the pnp service name (used in URLs)
            $pnpservice = substr($file, 0, strlen($file) - 4);

            //echo "FOUND $pnpservice<BR>";

            $s = array(
                "pnpservice" => $pnpservice,
                "rrdfile" => $dir . "/" . $file,
                "views" => pnp_read_all_service_views_from_file($dir . "/" . $pnpservice . ".xml")
            );
            $a["services"][$pnpservice] = $s;

        }
    }
    asort($a);

    return $a;
}

/**
 * @param string $hostname
 * @param string $servicename
 * @param bool   $usetemplate
 *
 * @return array
 */
function pnp_read_service_views($hostname = "", $servicename = "_HOST_", $usetemplate = true)
{

    // directory for this hosts' performance data
    $dir = pnp_get_host_perfdata_dir($hostname);

    $pnpservice = pnp_convert_object_name($servicename);

    $views = pnp_read_service_sources_from_file($dir . "/" . $pnpservice . ".xml", $usetemplate);

    return $views;
}

/**
 * @param      $file
 * @param bool $usetemplate
 *
 * @return array
 */
function pnp_read_service_sources_from_file($file, $usetemplate = true)
{

    $sources = array();
    $filesources = array();
    $templatesources = array();

    //echo "SERVICEXML: $file<BR>";

    // load xml from file and save it for later reference
    if (file_exists($file) === FALSE)
        return $sources;
    $xml = simplexml_load_file($file);

    // run an xpath query to only return the nodes we're interested in
    if ($xml)
        $xp = $xml->xpath("/NAGIOS/DATASOURCE");
    else
        $xp = null;
    if ($xp) {

        // check to see if a template is defined to override datasource we would normally find here
        $templatesources = $usetemplate ? pnp_read_service_sources_from_template(strval($xp[0]->TEMPLATE)) : array();
        // we got some, so bail out
        if (count($templatesources) > 0) {
            //echo "WE GOT ONE!";
        } // else use datasources we find in the xml file
        else {
            foreach ($xp as $ds) {

                // check to see if a template is defined to override datasource we find here
                $templatesources = $usetemplate ? pnp_read_service_sources_from_template($ds->TEMPLATE) : array();
                // we got some, so bail out
                if (count($templatesources) > 0)
                    break;

                // else use datasources we find in the xml file
                $filesources[] = array(
                    "id" => intval($ds->DS),
                    "name" => "$ds->NAME",
                    "template" => "$ds->TEMPLATE",
                    "display_name" => perfdata_get_friendly_source_name(strval($ds->NAME), strval($ds->TEMPLATE)),
                );
            }
        }
    }

    if (count($templatesources) > 0)
        $sources = $templatesources;
    else
        $sources = $filesources;

    return $sources;
}


/**
 * @param string $template
 *
 * @return array
 */
function pnp_read_service_sources_from_template($template = "")
{

    $sources = array();

    if ($template == "")
        return $sources;

    $dir = pnp_get_share_dir();
    $f1 = $dir . "/templates/" . $template . ".php";
    $f2 = $dir . "/templates.dist/" . $template . ".php";

    //echo "F1: $f1<BR>\n";
    //echo "F2: $f2<BR>\n";

    // try dist and user template directories
    if (file_exists($f1))
        $sources = pnp_read_service_sources_from_template_file($f1, $template);
    else if (file_exists($f2))
        $sources = pnp_read_service_sources_from_template_file($f2, $template);

    return $sources;
}

/**
 * @param string $fname
 * @param string $template
 *
 * @return array
 */
function pnp_read_service_sources_from_template_file($fname = "", $template = "")
{

    $sources = array();
    $def = array();

    //echo "INCLUDING $fname<BR>\n";
    //include_once($fname);
    $fh = fopen($fname, "r");
    if ($fh) {
        while (!feof($fh)) {
            $buf = fgets($fh, 4096);

            $i = strpos($buf, "def[");
            if ($i === FALSE)
                continue;
            $j = $i + 4;
            $dn = intval($buf[$j]);
            //echo "FOUND: $dn [I=$i][J=$j][BUF=$buf]<BR>\n";
            $def[$dn] = $dn;
        }
        fclose($fh);
    }

    //print_r($def);

    $n = 1;
    foreach ($def as $d) {

        //echo "DEF".$n."= ".$def[$n]."<BR>\n";

        $name = "ds" . $n;
        $sources[] = array(
            "id" => $n,
            "name" => $name,
            "template" => $template,
            "display_name" => perfdata_get_friendly_source_name($name, $template),
        );

        $n++;
    }

    //echo "TEMPLATE_SOURCES:<BR>\n";
    //print_r($sources);

    return $sources;
}


////////////////////////////////////////////////////////////////////////
// URL FUNCTIONS
////////////////////////////////////////////////////////////////////////

function pnp_get_direct_url()
{
    global $cfg;
    return $cfg["component_info"]["pnp"]["direct_url"];
}

/**
 * @param string $hostname
 * @param string $servicename
 * @param int    $source
 * @param int    $view
 * @param string $start
 * @param string $end
 *
 * @return string
 */
function pnp_get_direct_pnp_page_url($hostname = "", $servicename = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "")
{
    $url = pnp_get_direct_url() . "/index.php?host=" . pnp_convert_object_name($hostname);
    if ($servicename != "")
        $url .= "&srv=" . pnp_convert_object_name($servicename);
    else
        $url .= "&srv=_HOST_";
    if ($start != "")
        $url .= "&start=" . $start;
    if ($end != "")
        $url .= "&end=" . $end;
    $url .= "&source=" . $source;
    $url .= "&view=" . $view;
    return $url;
}

/**
 * @param string $hostname
 * @param string $servicename
 * @param int    $source
 * @param int    $view
 * @param string $start
 * @param string $end
 *
 * @return string
 */
function pnp_get_direct_pnp_image_url($hostname = "", $servicename = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "")
{
    $url = pnp_get_direct_url() . "/index.php?host=" . pnp_convert_object_name($hostname) . "&display=image";

    // make sure user is authorzed
    // NOTE: can't do this, as host/service names may have been munged by pnp_convert_object_name()
    /*
    if($servicename!="" && $servicename!="_HOST_"){
        $auth=is_authorized_for_service(0,$hostname,$servicename);
        }
    else
        $auth=is_authorized_for_host(0,$hostname);
    if($auth==false)
        return false;
    */

    if ($servicename != "")
        $url .= "&srv=" . pnp_convert_object_name($servicename);
    else
        $url .= "&srv=_HOST_";
    if ($start != "")
        $url .= "&start=" . $start;
    if ($end != "")
        $url .= "&end=" . $end;
    $url .= "&source=" . $source;
    $url .= "&view=" . $view;
    return $url;
}


////////////////////////////////////////////////////////////////////////
// OUTPUT FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * @param string $hostname
 * @param string $servicename
 * @param int    $source
 * @param int    $view
 * @param string $start
 * @param string $end
 *
 * @return string
 */
function pnp_get_direct_pnp_image_html($hostname = "", $servicename = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "")
{
    $html = "<img src='" . pnp_get_direct_pnp_image_url($hostname, $servicename, $source, $view, $start, $end) . "' class='pnp_chart'>";
    return $html;
}

/**
 * @param string $hostname
 * @param string $servicename
 * @param int    $source
 * @param int    $view
 * @param string $start
 * @param string $end
 * @param bool   $dashit
 */
function pnp_get_direct_page_html($hostname = "", $servicename = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "", $dashit = true)
{

    if ($dashit == true) {
        dashletify_begin(array("class" => "pnp_chart"));
    }
    $url = pnp_get_direct_pnp_page_url($hostname, $servicename, $source, $view, $start, $end);
    echo "<a href='" . $url . "'>";
    echo pnp_get_direct_pnp_image_html($hostname, $servicename);
    echo "</a>";
    if ($dashit == true) {
        dashletify_end();
    }
    return;
}

/**
 * @param string $hostname
 * @param string $servicename
 * @param int    $source
 * @param int    $view
 * @param string $start
 * @param string $end
 * @param bool   $dashit
 */
function pnp_display_chart($hostname = "", $servicename = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "", $dashit = true)
{

    pnp_get_direct_page_html($hostname, $servicename, $source, $view, $start, $end, $dashit);
}
	

