<?php
//
// Nagios XI Backend API (XML/JSON Read Interface)
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//
// THIS IS DEPRECATED!
// THIS IS BEING REMOVED IN XI 6 FOR NEW API!
// DO NOT ADD THINGS HERE! USE ../api INSTEAD!
//

// Make sure error messages don't break API response
ini_set('display_errors', 'off');

define("BACKEND", true);

require_once(dirname(__FILE__) . '/../config.inc.php');
require_once(dirname(__FILE__) . '/includes/common.inc.php');

// Initialization
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars(false);
check_backend_prereqs();

if (!is_backend_authenticated()) {
    handle_backend_error("Authentication Failure");
}

route_request();

function route_request()
{
    global $page_start_time;
    global $page_end_time;
    global $request;

    // For debugging execution time
    $debug = grab_request_var("debug", "");
    if (have_value($debug)) {
        $page_start_time = get_timer();
    }

    // Get API command
    $cmd = strtolower(grab_request_var("cmd", ""));
    $outputtype = strtolower(grab_request_var("outputtype", "xml"));

    // Handle the command
    switch ($cmd) {

        // Return Backend product information
        case "hello":
        case "getproductinfo":
            fetch_backend_info($outputtype);
            break;

        // Get ticket
        case "getticket":
            fetch_backend_ticket();
            break;

        // magic pixel (auto-login for Fusion)
        case "getmagicpixel":
            fetch_magic_pixel();
            break;

        // users
        case "getusers":
            fetch_users();
            break;

        // system statistics
        case "getsysstat":
            fetch_sysstat_info();
            break;

        // Command subsystem
        case "submitcommand":
            backend_submit_command();
            break;
        case "getcommands":
            $out = get_command_status_xml($request);
            print backend_output($out);
            break;

        // pnp
        case "pnpproxy":
            fetch_proxied_pnp_data();
            break;

        // ndo misc
        case "getndodbversion":
            fetch_ndodbversion();
            break;
        case "getinstances":
            fetch_instances();
            break;

        // objects
        case "getobjects":
            fetch_objects();
            break;
        case "gethosts":
            fetch_hosts();
            break;
        case "getparenthosts":
            fetch_parenthosts();
            break;
        case "getservices":
            fetch_services();
            break;
        case "getcontacts":
            fetch_contacts();
            break;
        case "gethostgroups":
            fetch_hostgroups();
            break;
        case "gethostgroupmembers":
            fetch_hostgroupmembers();
            break;
        case "getservicegroups":
            fetch_servicegroups();
            break;
        case "getservicegroupmembers":
            fetch_servicegroupmembers();
            break;
        case "getservicegrouphostmembers":
            fetch_servicegrouphostmembers();
            break;
        case "getcontactgroups":
            fetch_contactgroups();
            break;
        case "getcontactgroupmembers":
            fetch_contactgroupmembers();
            break;

        case "gettopalertproducers":
            fetch_top_alert_producers();
            break;

        case "getauditlog":
            fetch_auditlog();
            break;

        /*
        // perms
        case "getinstanceperms":
            fetch_instanceperms();
            break;
        case "getobjectperms":
            fetch_objectperms();
            break;
        */

        // current status

        case "getprogramstatus":
            fetch_programstatus();
            break;
        case "getprogramperformance":
            fetch_programperformance();
            break;
        case "getcontactstatus":
            fetch_contactstatus();
            break;
        case "getcustomcontactvariablestatus":
            fetch_customcontactvariablestatus();
            break;
        case "gethoststatus":
            fetch_hoststatus();
            break;
        case "getcustomhostvariablestatus":
            fetch_customhostvariablestatus();
            break;
        case "getservicestatus":
            fetch_servicestatus();
            break;
        case "getcustomservicevariablestatus":
            fetch_customservicevariablestatus();
            break;
        case "gettimedeventqueue":
            fetch_timedeventqueue();
            break;
        case "gettimedeventqueuesummary":
            fetch_timedeventqueuesummary();
            break;
        case "getscheduleddowntime":
            fetch_scheduleddowntime();
            break;
        case "getcomments":
            fetch_comments();
            break;

        // Historical info

        case "getlogentries":
            fetch_logentries();
            break;
        case "getstatehistory":
            fetch_statehistory();
            break;
        case "getnotifications":
            fetch_notifications();
            break;
        case "getnotificationswithcontacts":
            fetch_notifications_with_contacts();
            break;
        case "gethistoricalhoststatus":
            fetch_historical_host_status();
            break;
        case "gethistoricalservicestatus":
            fetch_historical_service_status();
            break;
        case "getalerthistogram":
            fetch_alert_histogram();
            break;

        // Default Request
        default:
            handle_backend_error(_("Invalid or no command specified. Try") . " <a href=\"?cmd=getProgramStatus\">'getProgramstatus'</a>");
            exit;
    }

    // For debugging execution time
    if (have_value($debug)) {
        // timer info
        $page_end_time = get_timer();
        $page_time = get_timer_diff($page_start_time, $page_end_time);
        echo "\n\nFinished in " . $page_time . " seconds";
    }

    // Remove auth if enabled by backend auth
    if (array_key_exists('backend_only', $_SESSION) || defined('SUBSYSTEM_CALL')) {
        deinit_session();
    }
}


/**
 * Return some information about XI and the backend 
 *
 * @param $outputtype
 */
function fetch_backend_info($outputtype)
{
    if ($outputtype == "json") {
        $data = array("product" => get_product_name(),
            "version" => get_product_version(),
            "version_major" => get_product_version("major"),
            "version_minor" => get_product_version("minor"),
            "build" => get_product_build(),
            "api_url" => get_backend_url());
        print backend_output($data);
    } else {
        output_backend_header();
        echo "<backendinfo>\n";
        echo "  <productinfo>\n";
        xml_field(2, "productname", get_product_name());
        xml_field(2, "productversion", get_product_version());
        xml_field(2, "productbuild", get_product_build());
        echo "  </productinfo>\n";
        echo "  <apis>\n";
        xml_field(2, "backend", get_backend_url());
        echo "  </apis>\n";
        echo "</backendinfo>\n";
    }
}


// Do pnp proxy
function fetch_proxied_pnp_data()
{
    global $request;

    $pnpfile = grab_request_var("pnpfile", "");

    unset($request['cmd']);
    unset($request['pnpfile']);

    pnp_do_proxy($pnpfile);
}


/**
 * Output data as JSON
 *
 * @param $data
 * @return mixed|string
 */
function backend_convert_to_json($data)
{
    // Convert XML to an object that can be converted to json
    if (!is_array($data) && is_string($data)) {
        $data = simplexml_load_string($data);
    }
    return json_encode($data);
}


/**
 * Output data based on the type...
 *
 * @param $output
 * @return mixed|string
 */
function backend_output($output)
{
    $outputtype = grab_request_var("outputtype", "xml");

    if ($outputtype == "json") {
        $output = backend_convert_to_json($output);
    }

    output_backend_header();
    return $output;
}
