<?php
//
// Internet Traffic Report
// Copyright (c) 2008-2019 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');

internettrafficreport_dashlet_init();

function internettrafficreport_dashlet_init()
{
    $name = "internettrafficreport";

    $args = array(
        DASHLET_NAME => $name,
        DASHLET_AUTHOR => "Nagios Enterprises, LLC",
        DASHLET_DESCRIPTION => _("Internet Traffic Report."),
        DASHLET_COPYRIGHT => "Dashlet Copyright &copy; 2009-2019 Nagios Enterprises, LLC.<br>Data Copyright &copy; ".date('Y', time())." Keynote Systems, Inc.",
        DASHLET_LICENSE => "MIT",
        DASHLET_FUNCTION => "internettrafficreport_dashlet_func",
        DASHLET_TITLE => _("Internet Traffic Report"),
        DASHLET_OUTBOARD_CLASS => "internettrafficreport_outboardclass",
        DASHLET_INBOARD_CLASS => "internettrafficreport_inboardclass",
        DASHLET_PREVIEW_CLASS => "internettrafficreport_previewclass",
        DASHLET_CSS_FILE => "internettrafficreport.css",
        DASHLET_WIDTH => "200",
        DASHLET_HEIGHT => "200",
        DASHLET_OPACITY => "1.0",
        DASHLET_BACKGROUND => ""
    );

    register_dashlet($name, $args);
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function internettrafficreport_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $baseurl = "http://www.internettrafficreport.com";

    $regions = array(
        "global" => array(
            "name" => "Global",
            "details_url" => "/",
            "trend_map" => "/gifs/tr_map_global.gif",
            "traffic_graph" => "/graphs/tr_global_s1.gif",
            "response_time_graph" => "/graphs/tr_global_r1.gif",
            "packet_loss_graph" => "/graphs/tr_global_p1.gif",
        ),
        "asia" => array(
            "name" => "Asia",
            "details_url" => "/asia.htm",
            "trend_map" => "/gifs/tr_map_asia.gif",
            "traffic_graph" => "/graphs/tr_asia_s1.gif",
            "response_time_graph" => "/graphs/tr_asia_r1.gif",
            "packet_loss_graph" => "/graphs/tr_asia_p1.gif",
        ),
        "australia" => array(
            "name" => "Australia",
            "details_url" => "/australia.htm",
            "trend_map" => "/gifs/tr_map_australia.gif",
            "traffic_graph" => "/graphs/tr_australia_s1.gif",
            "response_time_graph" => "/graphs/tr_australia_r1.gif",
            "packet_loss_graph" => "/graphs/tr_australia_p1.gif",
        ),
        "europe" => array(
            "name" => "Europe",
            "details_url" => "/europe.htm",
            "trend_map" => "/gifs/tr_map_europe.gif",
            "traffic_graph" => "/graphs/tr_europe_s1.gif",
            "response_time_graph" => "/graphs/tr_europe_r1.gif",
            "packet_loss_graph" => "/graphs/tr_europe_p1.gif",
        ),
        "northamerica" => array(
            "name" => "North America",
            "details_url" => "/namerica.htm",
            "trend_map" => "/gifs/tr_map_namerica.gif",
            "traffic_graph" => "/graphs/tr_namerica_s1.gif",
            "response_time_graph" => "/graphs/tr_namerica_r1.gif",
            "packet_loss_graph" => "/graphs/tr_namerica_p1.gif",
        ),
        "southamerica" => array(
            "name" => "South America",
            "details_url" => "/samerica.htm",
            "trend_map" => "/gifs/tr_map_samerica.gif",
            "traffic_graph" => "/graphs/tr_samerica_s1.gif",
            "response_time_graph" => "/graphs/tr_samerica_r1.gif",
            "packet_loss_graph" => "/graphs/tr_samerica_p1.gif",
        ),
    );

    $output = "";

    $imgbase = get_dashlet_url_base("internettrafficreport") . "/images/";

    switch ($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            $output = '
			<LABEL FOR="region">Region</LABEL>
			<BR CLASS="nobr" />
			<SELECT NAME="region" class="form-control" ID="region">
			<OPTION VALUE="global">Global</OPTION>
			<OPTION VALUE="asia">Asia</OPTION>
			<OPTION VALUE="australia">Australia</OPTION>
			<OPTION VALUE="europe">Europe</OPTION>
			<OPTION VALUE="northamerica">North America</OPTION>
			<OPTION VALUE="southamerica">South America</OPTION>
			</SELECT>
			<BR CLASS="nobr" />
			<LABEL FOR="map">Map</LABEL>
			<BR CLASS="nobr" />
			<SELECT NAME="map" class="form-control" ID="map">
			<OPTION VALUE="trend_map">Trends Map</OPTION>
			<OPTION VALUE="traffic_graph">Traffic Graph</OPTION>
			<OPTION VALUE="response_time_graph">Response Time Graph</OPTION>
			<OPTION VALUE="packet_loss_graph">Packet Loss Graph</OPTION>
			</SELECT>
			<BR CLASS="nobr" />
            <br>
			';
            break;
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            //print_r($args);
            //return "ARGS:";

            $regionid = $args["region"];
            $theregion = $regions[$regionid];
            $siteurl = $baseurl . "" . $theregion["details_url"];
            $imgurl = $baseurl . "" . $theregion[$args["map"]];

            $width = "90%";
            $height = "90%";

            $output = "<div id='internettrafficreport-container-" . $id . "' style='width: " . $width . "; height: " . $height . ";' ><div style='display: block;'><a href='" . $siteurl . "' target='_blank' title='Go To The Internet Traffic Report'><img src='" . $imgurl . "' width='100%'></a></div></div>";
            break;
        case DASHLET_MODE_PREVIEW:
            $output = "<img src='" . $imgbase . "preview.png'>";
            break;
    }

    return $output;
}
