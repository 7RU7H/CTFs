<?php
//
// Gauge Dashlet
//
// Copyright (c) 2013-2018 Nagios Enterprises, LLC.
//
// LICENSE:
//
// Except where explicitly superseded by other restrictions or licenses, permission
// is hereby granted to the end user of this software to use, modify and create
// derivative works or this software under the terms of the Nagios Software License, 
// which can be found online at:
//
// https://www.nagios.com/legal/licenses/
//

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');

gauges_dashlet_init();

function gauges_dashlet_init()
{
    $name = "gauges";

    $args = array(
        DASHLET_NAME => $name,
        DASHLET_VERSION => "1.2.2",
        DASHLET_AUTHOR => "Nagios Enterprises, LLC",
        DASHLET_DESCRIPTION => _("Displays gauges."),
        DASHLET_COPYRIGHT => "Copyright (c) 2013-2018 Nagios Enterprises",
        DASHLET_HOMEPAGE => "https://www.nagios.com",
        DASHLET_FUNCTION => "gauges_dashlet_func", // the function name to call
        DASHLET_TITLE => _("Gauge Dashlet"), // title used in the dashlet
        DASHLET_OUTBOARD_CLASS => "gauges_outboardclass", // used when the dashlet is embedded in a non-dashboard page
        DASHLET_INBOARD_CLASS => "gauges_inboardclass", // used when the dashlet is on a dashboard
        DASHLET_PREVIEW_CLASS => "gauges_previewclass", // used in the "Available Dashlets screen of Nagios XI
        DASHLET_JS_FILE => "js/gauge.js", // optional Javascript file to cinlude
        DASHLET_WIDTH => "120", // default size of the dashlet when first placed on the dashboard
        DASHLET_HEIGHT => "180",
        DASHLET_OPACITY => "1", // transparency/opacity of the dashlet (0=invisible,1.0=visible)
        DASHLET_BACKGROUND => "", // background color of the dashlet (optional)
    );

    register_dashlet($name, $args);
    register_callback(CALLBACK_PAGE_HEAD, 'gauges_component_head_include');
}

/**
 * @param string $cbtype
 * @param null   $args
 *
 * @return bool
 */
function gauges_component_head_include($cbtype = '', $args = null)
{
    return true;
}

// Dashlet function
// This gets called at various points by Nagios XI.  The $mode argument will be different, depending on what XI is asking of the dashlet
/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function gauges_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{
    $output = "";
    $imgbase = get_dashlet_url_base("gauges") . "/images/"; // the relative URL base for the "images" subfolder for the dashlet

    switch ($mode) {

        // the dashlet is being configured
        // add optioal form arguments (text boxes, dropdown lists, etc.) to capture data here
        case DASHLET_MODE_GETCONFIGHTML:
            if ($args == null) {
                $output = '<script type="text/javascript">load_gauge_hosts();</script>';
                $output .= '<div class="popup-form-box"><label>' . _('Host') . '</label>
                            <div><select id="gauges_form_name" class="form-control" name="host" onchange="getgaugejson()">
                                    <option selected></option>';
                $output .= '</select> <i class="fa fa-spinner fa-spin fa-14 hide host-loader" title="'._('Loading').'"></i></div></div>';
                $output .= '<div class="popup-form-box"><label>' . _('Services') . '</label>
                                <div id="gauges_services">
                                    <select id="gauges_form_services" class="form-control" name="service" onchange="getgaugeservices()" disabled>
                                        <option selected></option>
                                    </select> <i class="fa fa-spinner fa-spin fa-14 hide service-loader" title="'._('Loading').'"></i>
                                    <div id="empty-services" class="hide">'._("No services found").'</div>
                                </div>
                            </div>';
                $output .= '<div class="popup-form-box"><label>' . _('Datasource') . '</label>
                                <div id="gauges_datasource">
                                    <select id="gauges_form_ds" class="form-control" name="ds" disabled>
                                        <option selected></option>
                                    </select> <i class="fa fa-spinner fa-spin fa-14 hide ds-loader" title="'._('Loading').'"></i>
                                    <div id="empty-ds" class="hide">'._("No data sources found").'</div>
                                </div>
                            </div>';
                $output .= '';
            }
            break;

        // for this example, we display the sample output whether we're on a dashboard or on a normal (non-dashboard) page
        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:

            $output = "";
            if (empty($args['ds'])) {
                $output .= "ERROR: Missing Arguments";
                break;
            }
            $id = "gauges_" . random_string(6); // a random ID to assign to the <div> tag that wraps the output, so the sample dashlet can appear multiple times on the sample dashboard

            // ajax updater args
            $ajaxargs = $args;
            // build args for javascript
            $n = 0;
            $jargs = "{";
            foreach ($ajaxargs as $var => $val) {
                if ($n > 0)
                    $jargs .= ", ";
                $jargs .= "\"$var\" : \"$val\"";
                $n++;
            }
            $jargs .= "}";

            // here we output some HTML that contains a <div> that gets updated via ajax every 5 seconds...

            $output .= '
			<div class="gauges_dashlet" id="' . $id . '">
			<div class="infotable_title">'._('Gauge').'</div>
			' . get_throbber_html() . '
			</div>

			<script type="text/javascript">
			$(document).ready(function() {
			
				get_' . $id . '_content();
				
				function get_' . $id . '_content(height, width) {

                    var args = ' . $jargs . ';

                    if (height == undefined) { var height = $("#' . $id . '").parent().height(); }
                    if (width == undefined) { var width = $("#' . $id . '").parent().width(); }

                    args.height = height;
                    args.width = width;

					$("#' . $id . '").each(function() {
						var optsarr = {
							"func": "get_gauges_dashlet_html",
							"args": args
						}
						var opts = JSON.stringify(optsarr);
						get_ajax_data_innerHTML("getxicoreajax", opts, true, this);
				    });
				}

                // Re-build the content when we resize
                $("#' . $id . '").closest(".ui-resizable").on("resizestop", function(e, ui) {
                    var height = ui.size.height;
                    var width = ui.size.width;
                    get_' . $id . '_content(height, width);
                });

			});
			</script>
			';

            break;

        // dashlet is in "preview" mode
        // it is being shown either under the Admin menu, or under the "Available Dashlets" screen
        case DASHLET_MODE_PREVIEW:
            $output = "<img src='" . $imgbase . "preview.png'>";
            break;
    }

    return $output;
}

// This is the function that XI calls when the dashlet javascript makes an AJAX call.
// Note how the function name is prepended with 'xicore_ajax_', compared to the function name we used in the javascript code when producing the wrapper <div> tag above
/**
 * @param null $args
 *
 * @return string
 */
function xicore_ajax_get_gauges_dashlet_html($args = null)
{
    //$args = array("host" => "www.twitter.com", "service" => "Ping", "ds" => "rta");
    $host = grab_array_var($args, 'host', '');
    $service = urldecode(grab_array_var($args, 'service', ''));
    $ds = grab_array_var($args, 'ds', '');

    $id = "gauges_inner_" . random_string(6);
    $displayed_title = ($service == "_HOST_") ? $host : "$host - $service";
    $output = "<div class='infotable_title' id='$id'></div>";

    $output .= '<script type="text/javascript">
			$(document).ready(function(){
			
				myShinyGauge_' . $id . '_url="' . get_dashlet_url_base("gauges") . '/getdata.php?host=' . $host . '&service=' . $service . '&ds=' . $ds . '"
                
                $.ajax({"url": myShinyGauge_' . $id . '_url, dataType: "json",
                    "success": function(result) {
                        myShinyGauge_' . $id . ' = create_gauge("' . $id . '", 1, result)
                        myShinyGauge_' . $id . '.redraw(result["current"], 500)
                    }
                });

               setInterval(function() {  $.ajax({"url": myShinyGauge_' . $id . '_url, dataType: "json",
                    "success": function(result) { 
                        myShinyGauge_' . $id . '.redraw(result["current"], 500)
                    }
                }); }, ' . get_dashlet_refresh_rate(60, "perfdata_chart") . ');
			});
			</script>';

    return $output;
}

/**
 * @param $host
 * @param $service
 * @param $ds
 *
 * @return bool
 */
function gauges_dashlet_gauge_exists($host, $service, $ds)
{

    $result = get_datasources($host, $service, $ds);

    if (count($result) > 0)
        return true;
    return false;
}


/**
 * @return mixed|string
 */
function get_gauge_json()
{
    $host = grab_request_var('host', '');
    $service = grab_request_var('service', '');
    $ds = grab_request_var('ds');

    if (empty($host) || empty($service) || empty($ds)) {
        return json_encode(get_datasources($host, $service, $ds));
    }

    $result = get_datasources($host, $service, $ds);
    foreach ($result as $services)
        foreach ($services as $service)
            foreach ($service as $ds)
                return json_encode($ds);
}

function gauges_get_host_services()
{
    $host = grab_request_var('host', '');

    $backendargs = array(
        'orderby' => "host_name:a,service_description:a",
        'brevity' => 4);

    if (!empty($host)) {
        $services = array();
        $backendargs['host_name'] = $host;
        $objs = get_xml_service_status($backendargs);
        foreach ($objs->servicestatus as $o) {
            $services[] = strval($o->name);
        }
        return json_encode($services);
    } else {
        $hosts = array();
        $objs = get_xml_host_status($backendargs);
        foreach ($objs->hoststatus as $o) {
            $hosts[] = strval($o->name);
        }
        return json_encode($hosts);
    }
}

/**
 * @param null $host
 * @param null $service
 * @param null $ds
 *
 * @return array
 */
function get_datasources($host = null, $service = null, $ds = null)
{
    $result = array();
    $backendargs = array();

    $backendargs["orderby"] = "host_name:a,service_description:a";
    if ($host)
        $backendargs["host_name"] = $host;
    if ($service)
        $backendargs["service_description"] = $service; // service

    $services = get_xml_service_status($backendargs);
    $hosts = get_xml_host_status($backendargs);

    foreach ($services->servicestatus as $status) {
        $status = (array)$status;
        $result[$status['host_name']][$status['name']] = get_gauge_datasource($status, $ds);
        if (empty($result[$status['host_name']]))
            unset($result[$status['host_name']]);
    }
    if (empty($service) || $service == '_HOST_')
        foreach ($hosts->hoststatus as $status) {
            $status = (array)$status;
            $result[$status['name']]['_HOST_'] = get_gauge_datasource($status, $ds);
            if (empty($result[$status['name']]))
                unset($result[$status['name']]);
        }

    return $result;
}

/**
 * @param $status
 * @param $ds_label
 *
 * @return array
 */
function get_gauge_datasource($status, $ds_label)
{
    $ds = array();

    if (empty($status['performance_data'])) {
        return '';
    }

    $perfdata_datasources = str_getcsv($status['performance_data'], " ", "&apos;");
    foreach ($perfdata_datasources as $perfdata_datasource) {

        $perfdata_s = explode('=', $perfdata_datasource);
        $perfdata_name = trim(str_replace("apos;", "", $perfdata_s[0]));

        // Strip bad char from key name and label (REMOVED for pnp convert function -JO)
        //$perfdata_name = str_replace('\\', '', $perfdata_name);
        //$perfdata_name = str_replace(' ', '_', $perfdata_name);
        $perfdata_name = pnp_convert_object_name($perfdata_name);
        if ($ds_label && $perfdata_name != $ds_label && $perfdata_name != pnp_convert_object_name($ds_label))
            continue;
        if (!isset($perfdata_s[1]))
            continue;
        
        //test=13; "test helo"=3; 

        $perfdata = explode(';', $perfdata_s[1]);
        $current = preg_replace("/[^0-9.]/", "", grab_array_var($perfdata, 0, 0));

        $ds[$perfdata_name]['label'] = $perfdata_name;
        $ds[$perfdata_name]['current'] = round(floatval($current), 3);
        $ds[$perfdata_name]['uom'] = str_replace($current, '', $perfdata[0]);
        $ds[$perfdata_name]['warn'] = grab_array_var($perfdata, 1, 0);
        $ds[$perfdata_name]['crit'] = grab_array_var($perfdata, 2, 0);
        $ds[$perfdata_name]['min'] = floatval(grab_array_var($perfdata, 3, "0"));
        $ds[$perfdata_name]['max'] = floatval(grab_array_var($perfdata, 4, "0"));

        // Do some guessing if max is not set
        if ($ds[$perfdata_name]['max'] == 0) {
            if ($ds[$perfdata_name]['crit'] != 0 && $ds[$perfdata_name]['crit'] > 0) {
                $ds[$perfdata_name]['max'] = $ds[$perfdata_name]['crit'] * 1.1;
            } else if ($ds[$perfdata_name]['uom'] == '%') {
                $ds[$perfdata_name]['max'] = 100;
            }
        }

        // Do some rounding
        $ds[$perfdata_name]['max'] = round($ds[$perfdata_name]['max'], 1);

        // Remove the item if we were not able to determine the max
        if ($ds[$perfdata_name]['max'] == 0) {
            $ds[$perfdata_name]['max'] = 100;
        }

        // add yellowZones & redZones
        if (!empty($ds[$perfdata_name]['warn'])) {
            if (strpos($ds[$perfdata_name]['warn'], ":") !== false) {
                // We are doing a range warning threshold
                list($end, $start) = explode(":", $ds[$perfdata_name]['warn']);
                $ds[$perfdata_name]['yellowZones'] = array(
                    array(
                        "from" => floatval($start),
                        "to" => floatval($end)
                    )
                );
            } else {
                // Standard warning threshold
                $ds[$perfdata_name]['yellowZones'] = array(
                    array(
                        "from" => floatval($ds[$perfdata_name]['warn']),
                        "to" => ($ds[$perfdata_name]['crit'] != 0) ? floatval($ds[$perfdata_name]['crit']) : $ds[$perfdata_name]['max'],
                    )
                );
            }
        }

        if (!empty($ds[$perfdata_name]['crit'])) {
            if (strpos($ds[$perfdata_name]['crit'], ":") !== false) {
                // We are doing a range warning threshold
                list($end, $start) = explode(":", $ds[$perfdata_name]['crit']);
                $ds[$perfdata_name]['redZones'] = array(
                    array(
                        "from" => floatval($start),
                        "to" => floatval($end)
                    )
                );
            } else {
                // Standard critical threshold
                $ds[$perfdata_name]['redZones'] = array(
                    array(
                        "from" => floatval($ds[$perfdata_name]['crit']),
                        "to" => $ds[$perfdata_name]['max'],
                    )
                );
            }
        }
    }

    return $ds;
}

if (!function_exists('str_getcsv')) {

    /**
     * @param        $input
     * @param string $delimiter
     * @param string $enclosure
     * @param null   $escape
     * @param null   $eol
     *
     * @return array
     */
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = null, $eol = null)
    {
        $temp = fopen("php://memory", "rw");
        fwrite($temp, $input);
        fseek($temp, 0);
        $r = array();
        while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
            $r[] = $data;
        }
        fclose($temp);
        return $r;
    }
}	

