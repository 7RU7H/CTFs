<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/includes/common.inc.php');


// Start session, authenticate, and grab request vars
init_session(false, false);
grab_request_vars();
check_authentication();


do_proxy();


/**
 * We need a proxy to use ajax stuff for external domains
 */
function do_proxy()
{
    global $request;

    // SSRF security fix
    $request_url = parse_url($request['proxyurl']);
    $scheme = strtolower($request_url['scheme']);
    $host = strtolower($request_url['host']);

    // Whitelist hosts that we are able to access via this call
    $whitelist = array('api.nagios.com');

    // Get url to fetch
    if ((strpos($scheme, 'http') === false && strpos($scheme, 'https') === false) || !in_array($host, $whitelist)) {
        exit();
    }
    $url = $request["proxyurl"];

    // Get method to send send
    $method = "post";
    if (isset($request["proxymethod"])) {
        $method = $request["proxymethod"];
    }

    // We don't want the url or method passed to the remote host
    unset($request["proxyurl"]);
    unset($request["proxymethod"]);

    // Add product information
    $request['product'] = get_product_name(true);
    $request['version'] = get_product_version();
    $request['build'] = get_build_id();

    // Build URL from array of arguments
    $theurl = $url . "?" . http_build_query($request);

    $options = array(
        'return_info' => true,
        'method' => $method
    );

    // Fetch the URL
    $result = load_url($theurl, $options);

    $headers = $result["headers"];

    $contenttype = "";
    if (array_key_exists("Content-Type", $headers)) {
        $contenttype = $headers["Content-Type"];
    }

    if (!have_value($contenttype)) {
        $contenttype = "text/html";
    }

    header("Content-Type: $contenttype");
    echo $result["body"];
}
