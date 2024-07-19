<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


/**
 * @param   string  $url
 * @return  bool
 */
function xi_fetch_rss($url)
{
    $tmp = get_tmp_dir() . '/';
    $xmlcache = $tmp . str_replace('/', '_', $url) . '.xml';

    // Check for cache, or update cache if it's older than 1 day
    if (file_exists($xmlcache) && filemtime($xmlcache) > (time() - (60 * 60 * 24))) {
        // Use cache
        $xml = @simplexml_load_file($xmlcache);
        if ($xml) {
            return $xml->channel->item;
        }
    } else {
        // Fetch live rss feed and cache it
        $xml = fetch_live_rss_and_cache($url, $xmlcache);
        if ($xml) {
            return $xml->channel->item;
        }
    }

    return false;
}


/**
 * @param   string  $url
 * @param   string  $xmlcache
 * @return  object              A SimpleXMLElement object
 */
function fetch_live_rss_and_cache($url, $xmlcache)
{
    $proxy = false;
    if (have_value(get_option('use_proxy'))) {
        $proxy = true;
    }

    $options = array(
        'return_info' => true,
        'method' => 'get',
        'timeout' => 10
    );

    // Fetch the url
    $result = load_url($url, $options, $proxy);
    $body = trim($result["body"]);

    // Cache contents
    file_put_contents($xmlcache, $body);
    $xml = simplexml_load_string($body);

    return $xml;
}
