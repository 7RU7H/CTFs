<?php
//
// Perfdata Component
//
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . "/../componenthelper.inc.php");

perfdata_component_init();


function perfdata_component_init()
{
    $args = array(
        COMPONENT_NAME           => "perfdata",
        COMPONENT_TITLE          => _("Performance Graphing"),
        COMPONENT_AUTHOR         => _("Nagios Enterprises, LLC"),
        COMPONENT_DESCRIPTION    => _("Provides integrated performance graphing functionality."),
        COMPONENT_DATE           => "10/09/2018",
        COMPONENT_PROTECTED      => true,
        COMPONENT_TYPE           => COMPONENT_TYPE_CORE,
        COMPONENT_CONFIGFUNCTION => "perfdata_config_func",
    );

    register_component($args[COMPONENT_NAME], $args);

    register_callback(CALLBACK_NOTIFICATION_EMAIL,      "perfdata_update_emails");
    register_callback(CALLBACK_NOTIFICATION_EMAIL_SENT, "perfdata_cleanup_tmp_images");
}


// given the parameters, return the appropriate url for
// the graph image (if no data exists, return noperfdata.png)
function perfdata_get_graph_image_url($host = "", $service = "_HOST_", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "", $host_id = -1, $service_id = -1)
{
    // no perfdata
    if (!pnp_chart_exists($host, $service)) {
        return get_component_url_base("perfdata") . "/noperfdata.png?";
    }

    // otherwise good to go
    return perfdata_get_api_url($host, $service, $source, $view, $start, $end);
}


// return the url for host/service/etc. for the graph itself
function perfdata_get_api_url($host = "", $service = "_HOST_", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "")
{
    $service = str_replace(" ", "_", $service);
    $service = urlencode($service);

    $url = get_component_url_base("perfdata") . "/graphApi.php";
    $url .= "?host=$host";
    $url .= "&service=$service";
    $url .= "&source=$source";
    $url .= "&view=$view";
    $url .= "&start=$start";
    $url .= "&end=$end";

    return $url;
}


// not sure why it says service_sources when it is
// clearly returning views
function perfdata_get_service_sources($host = "", $service = "_HOST_", $usetemplate = true)
{
    return pnp_read_service_views($host, $service, $usetemplate);
}


// return a human readable source name
function perfdata_get_friendly_source_name($source_name, $template = "")
{
    $name = $source_name;

    $source_map = array(
        "time"   => _("Time"),
        "size"   => _("Size"),
        "pl"     => _("Packet Loss"),
        "rta"    => _("Round Trip Average"),
        "load1"  => _("1 Minute Load"),
        "load5"  => _("5 Minute Load"),
        "load15" => _("15 Minute Load"),
        "users"  => _("Users")
    );
    
    if ($template == "check_http") {
        $source_map["time"] = _("Response Time");
        $source_map["size"] = _("Page Size");
        $source_map["ds1"]  = _("Response Time");
        $source_map["ds2"]  = _("Page Size");
    }

    else if ($template == "check_dns") {
        $source_map["time"] = _("Response Time");
    }

    else if ($template == "check_local_load") {
        $source_map["ds1"]  = _("CPU Load");
    }

    if (!empty($source_map[$source_name])) {
        $name = $source_map[$source_name];
    }

    return $name;
}


// return an html image tag built with the appropriate
// image url
function perfdata_get_direct_graph_image_html($host = "", $service = "", $source = 1, $view = PNP_VIEW_DEFAULT, $start = "", $end = "", $title = "", $width = "", $height = "", $host_id = -1, $service_id = -1)
{
    $url = perfdata_get_graph_image_url($host, $service, $source, $view, $start, $end, $host_id, $service_id);

    $title = encode_form_val($title);

    $html = "<img src=\"$url\" alt=\"$title\" title=\"$title\" class=\"perfdatachart\"";

    if (!empty($width)) {
        $width = encode_form_val($width);
        $html .= " width=\"$width\"";
    }

    if (!empty($height)) {
        $height = encode_form_val($height);
        $html .= " height=\"$height\"";
    }

    $html .= ">";

    return $html;
}


// return either the default rrdtool path
// or if it has been updated in the config file
function perfdata_rrdtool_path()
{
    global $cfg;

    $rrdtool_path = "/usr/bin/rrdtool";

    if (!empty($cfg["component_info"]["perfdata"]["rrdtool_path"])) {
        $rrdtool_path = $cfg["component_info"]["perfdata"]["rrdtool_path"];
    }

    return $rrdtool_path;
}


// update notification emails
// with perfdata graphs if it's enabled
function perfdata_update_emails($cbtype, &$args)
{
    if ($cbtype != CALLBACK_NOTIFICATION_EMAIL) {
        return;
    }

    $meta = grab_array_var($args, "meta", array());

    // we check again in case it was disabled since the
    // callback was registered
    $perfdata_email_inclusion = get_option("perfdata_email_inclusion", 0);
    if ($perfdata_email_inclusion == 0) {
        return;
    }

    $host    = grab_array_var($meta, "host", "");
    $service = grab_array_var($meta, "service", "_HOST_");

    if (empty($host)) {
        return;
    }

    // no sense in continuing if there is no
    // graph to include
    if (!pnp_chart_exists($host, $service)) {
        return;
    }

    // now we have to build the image to include it with
    // the email
    $image_id = random_string(6);
    $image_path = get_tmp_dir() . "/img_{$image_id}.png";


    require_once(dirname(__FILE__) . "/graphApi.inc.php");

    // need to see what the preferred view is
    $view = get_option("perfdata_email_view", 0);

    // 1, 0, "", "" = source=1, view=0=-4h, start="", end=""
    $cmd = fetch_graph_command($host, $service, 1, $view, "", "", $image_path);
    exec($cmd, $output, $ret);

    // if the command didn't work, let everything
    // else continue to work
    if ($ret != 0) {
        echo "COULT NOT GENERATE IMAGE: $image_path\n";
        return;
    }

    // initialize the images
    if (!isset($args["images"]) || !is_array($args["images"])) {
        $args["images"] = array();
    }

    // it might seem trivial, but you actually need
    // all of the "optional" values (for phpmailer AddEmbeddedImage)
    $image = array(
        "path"        => $image_path,
        "cid"         => $image_id,
        "name"        => "graph.png",
        "encoding"    => "base64",
        "type"        => "image/png",
        "disposition" => "inline",

        // this is only here for our cleanup callback
        "perfdata_id" => $image_id,
    );

    // add our image to the array
    // with will ultimately end up
    // in send_email()
    $args["images"][] = $image;

    // now we need to update the message
    // so we have an image tag in the body
    $args["message"] .= "<br /><br /><img src=\"cid:{$image_id}\" />";
}


// once an email is sent we can cleanup whatever images
// have been sent since it's an attachment and not
// hosted here
function perfdata_cleanup_tmp_images($cbtype, &$args)
{
    if ($cbtype != CALLBACK_NOTIFICATION_EMAIL_SENT) {
        return;
    }

    // we only care about images
    if (empty($args["images"]) || !is_array($args["images"])) {
        return;
    }

    foreach ($args["images"] as $img) {

        // we need to be careful here
        // as we should only be deleting
        // images that *WE* created
        if (empty($img["perfdata_id"])) {
            continue;
        }

        // now delete the file
        $ret = unlink($img["path"]);

        if ($ret != 0) {
            echo "COULD NOT UNLINK IMAGE: $path\n";
        }
    }
}


// default pnp views
function perfdata_pnp_views()
{
    return array("-4h", "-24h", "-7d", "-30d", "-365d");
}


// correlating timestamps for views
function perfdata_pnp_timestamps()
{
    //return array((60*60*4), (60*60*24), (60*60*24*7), (60*60*24*30), (60*60*24*365));
    return array(14400, 86400, 604800, 2592000, 31536000);
}


// allow for people to decide whether or not
// they want perfdata to show up in their notifications
function perfdata_config_func($mode = "", $inargs, &$outargs, &$result)
{
    $result = 0;
    $output = "";

    if ($mode == COMPONENT_CONFIGMODE_GETSETTINGSHTML) {

        $enabled = get_option("perfdata_email_inclusion", 0);
        $view    = get_option("perfdata_email_view", 0);

        $enabled = intval($enabled);

        $output = '

            <h5 class="ul">' . _("Perfdata Email Settings") . '</h5>

            <table class="table table-condensed table-no-border table-auto-width">
                <tr>
                    <td></td>
                    <td class="checkbox">
                        <label>
                            <input type="checkbox" class="checkbox" id="enabled" name="enabled" ' . is_checked($enabled, 1) . ' value="1">
                            ' . _("Enable Perfdata Graphs in Notifcations") . '
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="vt">
                        <label>' . _("Perfdata Graph View") . ':</label>
                    </td>
                    <td>
                        <select name="view" id="view" class="form-control">
                            <option value="0" ' . is_selected($view, 0) . '>' . _("Last 4 hours") . '</option>
                            <option value="1" ' . is_selected($view, 1) . '>' . _("Last 24 hours") . '</option>
                            <option value="2" ' . is_selected($view, 2) . '>' . _("Last 7 days") . '</option>
                            <option value="3" ' . is_selected($view, 3) . '>' . _("Last 30 days") . '</option>
                            <option value="4" ' . is_selected($view, 4) . '>' . _("Last year") . '</option>
                        </select>
                        <div class="subtext">' . _("The view (timeperiod) of the graphs to include in notifications.") . '</div>
                    </td>
                </tr>
            </table>';
    }

    else if ($mode == COMPONENT_CONFIGMODE_SAVESETTINGS) {

        $enabled = grab_array_var($inargs, "enabled", 0);
        $view    = grab_array_var($inargs, "view", 0);

        $enabled = intval($enabled);
        $view    = intval($view);

        if ($view < 0 || $view > 4) {
            $view = 0;
        }

        set_option("perfdata_email_inclusion", $enabled);
        set_option("perfdata_email_view", $view);
    }

    return $output;
}
