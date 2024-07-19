<?php
//
// Externally Authored Functions
// All function code copyright by respective authors.
// Nagios Enterprises makes no claims of ownership over these functions.
//


/**
 * Timing safe string comparison
 * (ability to support hash_equals in PHP < 5.6)
 * (original: http://php.net/manual/en/function.hash-equals.php#115635)
 *
 * @param   string  $known_string   The known string
 * @param   string  $user_string    The string the user defines
 * @return  bool                    True if strings match
 */
if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string) {
        if (strlen($known_string) != strlen($user_string)) {
            return false;
        } else {
            $res = $known_string ^ $user_string;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }
}


/**
 * Like htmlentities but for XML
 * (XML Entity Mandatory Escape Characters code copied from http://www.php.net/htmlentities)
 *
 * @param   string  $string     The string you want to escape
 * @return  string              XML-escaped string
 */
function xmlentities($string)
{
    $length = strlen($string);
    $clean_output = '';

    // If it's less than 4k then don't bother looping
    if ($length < 4000) {
        $clean_output = do_xml_replace($string);
    } else {

        // Loop through 4k chunks (substr doesnt care if length > string)
        $start = 0;
        $len = 4000;
        while ($start < $length) {
            $tmp = substr($string, $start, $len);
            $clean_output .= do_xml_replace($tmp);
            $start += $len;
        }

    }

    return $clean_output;
}


/**
 * Does the actual XML escaping... do not pass this more than 4k characters at a time
 * for some reason Apache can crash if you do...
 */
function do_xml_replace($string)
{
    $data = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $string);
    preg_match_all('/([\x09\x0a\x0d\x20-\x7e]' . // ASCII characters
        '|[\xc2-\xdf][\x80-\xbf]' . // 2-byte (except overly longs)
        '|\xe0[\xa0-\xbf][\x80-\xbf]' . // 3 byte (except overly longs)
        '|[\xe1-\xec\xee\xef][\x80-\xbf]{2}' . // 3 byte (except overly longs)
        '|\xed[\x80-\x9f][\x80-\xbf])+/', // 3 byte (except UTF-16 surrogates)
        $data, $clean_pieces);
    $clean_output = join('?', $clean_pieces[0]);
    return $clean_output;
}


/**
 * Loads a URL and returns the string or object given
 * (See http://www.bin-co.com/php/scripts/load/)
 * Version : 1.00.A
 * License: BSD
 *
 * @param   string  $url        The URL to load
 * @param   array   $options    Options to pass to curl/loader
 * @param   bool    $use_proxy  Use proxy or not
 * @return  array|string        The contents that were loaded
 */
function load_url($url, $options = array('method' => 'get', 'return_info' => false), $use_proxy = false)
{
    // Added 04-28-08 EG added a default timeout of 15 seconds
    if (!isset($options['timeout'])) {
        $options['timeout'] = 15;
    }

    $url_parts = parse_url($url);

    // HTTP code is currently only supported by cURL
    $info = array(
        'http_code' => 200
    );
    $response = '';

    // Set the user agent (added Nagios XI with version number)
    $ver = get_product_version();
    $send_header = array(
        'Accept' => 'text/*',
        'User-Agent' => 'Nagios XI/'.$ver.' using BinGet/1.00.A'
    );

    ///////////////////////////// Curl /////////////////////////////////////
    // If curl is available, use curl to get the data.
    if (function_exists("curl_init")
        and (!(isset($options['use']) and $options['use'] == 'fsocketopen'))
    ) { //Don't user curl if it is specifically stated to user fsocketopen in the options
        if (isset($options['method']) and $options['method'] == 'post') {
            $port = (isset($url_parts['port'])) ? ':' . $url_parts['port'] : '';
            $page = $url_parts['scheme'] . '://' . $url_parts['host'] . $port . $url_parts['path'];
        } else {
            $page = $url;
        }

        $ch = curl_init($url_parts['host']);

        // added 04-28-08 EG set a timeout
        if (isset($options['timeout']))
            curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);

        curl_setopt($ch, CURLOPT_URL, $page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
        curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
        curl_setopt($ch, CURLOPT_NOBODY, false); //The content - if true, will not download the contents
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query']) and $url_parts['query']) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $url_parts['query']);
        }
        //Set the headers our spiders sends
        curl_setopt($ch, CURLOPT_USERAGENT, $send_header['User-Agent']); //The Name of the UserAgent we will be using ;)
        $custom_headers = array("Accept: " . $send_header['Accept']);
        if (isset($options['modified_since']))
            array_push($custom_headers, "If-Modified-Since: " . gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since'])));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);

        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt"); //If ever needed...
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_SSLVERSION, get_option('curl_ssl_version', CURL_SSLVERSION_TLSv1_2));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        $curl_force_verifypeer = get_option('curl_force_verifypeer', 1);
        if (intval($curl_force_verifypeer) != 1 && $options['disable_verifypeer']) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }

        //Updated to 2 on 4/10/2013, support for 1 is removed in curl 7.28.1 and PHP 5.4 - MG - Updated 6/4/2013 to take into account older systems -SW
        $curl_version_info = curl_version();
        if ($curl_version_info['version_number'] >= 465921 || version_compare(phpversion(), '5.4', '>'))
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        else
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        //proxy options - added 10/12/2011 -MG
        if ($use_proxy) {
            //Added ability to turn off HTTPPROXYTUNNEL from proxy component -SW
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, get_option('proxy_tunnel', 1));
            curl_setopt($ch, CURLOPT_PROXY, get_option('proxy_address'));
            curl_setopt($ch, CURLOPT_PROXYPORT, get_option('proxy_port'));
            curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            //use auth credentials if specified
            if (have_value(get_option('proxy_auth')))
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, get_option('proxy_auth'));

        }

        if (isset($url_parts['user']) and isset($url_parts['pass'])) {
            $custom_headers = array("Authorization: Basic " . base64_encode($url_parts['user'] . ':' . $url_parts['pass']));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
        }

        $response = curl_exec($ch);

        // Log error if there is a problem
        if ($response === false) {
            $error = curl_error($ch);
            error_log($error);
        }

        $info = curl_getinfo($ch); //Some information on the fetch
        curl_close($ch);

        //////////////////////////////////////////// FSockOpen //////////////////////////////
    } else { //If there is no curl, use fsocketopen
        if (isset($url_parts['query'])) {
            if (isset($options['method']) and $options['method'] == 'post')
                $page = $url_parts['path'];
            else
                $page = $url_parts['path'] . '?' . $url_parts['query'];
        } else {
            $page = $url_parts['path'];
        }

        $fp = fsockopen($url_parts['host'], 80, $errno, $errstr, 30);
        if ($fp) {

            // added 04-28-08 EG set a timeout
            if (isset($options['timeout']))
                stream_set_timeout($fp, $options['timeout']);

            $out = '';
            if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
                $out .= "POST $page HTTP/1.1\r\n";
            } else {
                $out .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1
            }
            $out .= "Host: $url_parts[host]\r\n";
            $out .= "Accept: $send_header[Accept]\r\n";
            $out .= "User-Agent: {$send_header['User-Agent']}\r\n";
            if (isset($options['modified_since']))
                $out .= "If-Modified-Since: " . gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since'])) . "\r\n";

            $out .= "Connection: Close\r\n";

            //HTTP Basic Authorization support
            if (isset($url_parts['user']) and isset($url_parts['pass'])) {
                $out .= "Authorization: Basic " . base64_encode($url_parts['user'] . ':' . $url_parts['pass']) . "\r\n";
            }

            //If the request is post - pass the data in a special way.
            if (isset($options['method']) and $options['method'] == 'post' and $url_parts['query']) {
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= 'Content-Length: ' . strlen($url_parts['query']) . "\r\n";
                $out .= "\r\n" . $url_parts['query'];
            }
            $out .= "\r\n";

            fwrite($fp, $out);
            while (!feof($fp)) {
                $response .= fgets($fp, 128);
            }
            fclose($fp);
        }
    }

    //Get the headers in an associative array
    $headers = array();

    //added logging for connection failures
    if ($info['http_code'] > 399 || $info['http_code'] < 200) {
        $f = @fopen(get_root_dir() . '/var/load_url.log', 'w');
        @fwrite($f, "CURL ERROR\n TIME: " . date('c', time()) . "\n" . print_r($info, true) . "\nURL:\n $url \n OPTIONS: \n" . print_r($options, true) . "\nEND LOGENTRY\n\n");
        @fclose($f);
    }


    if ($info['http_code'] == 404) {
        $body = "";
        $headers['Status'] = 404;
    } else {
        //Seperate header and content
        //echo "RESPONSE: ".$response."<BR><BR>\n";
        //exit();
        $separator_position = strpos($response, "\r\n\r\n");
        $header_text = substr($response, 0, $separator_position);
        $body = substr($response, $separator_position + 4);

        // added 04-28-2008 EG if we get a 301 (moved), another set of headers is received,
        if (substr($body, 0, 5) == "HTTP/") {
            $separator_position = strpos($body, "\r\n\r\n");
            $header_text = substr($body, 0, $separator_position);
            $body = substr($body, $separator_position + 4);
        }

        //echo "SEP: ".$separator_position."<BR><BR>\n";
        //echo "HEADER: ".$header_text."<BR><BR>\n";
        //echo "BODY: ".$body."<BR><BR>\n";

        foreach (explode("\n", $header_text) as $line) {
            $parts = explode(": ", $line);
            if (count($parts) == 2) $headers[$parts[0]] = chop($parts[1]);
        }
    }

    if ($options['return_info'])
        return array('headers' => $headers, 'body' => $body, 'info' => $info);
    return $body;
}


/**
 * Parse argv options (this is DEPRECATED and being removed in XI 6, use
 * PHP built-in getopt instead)
 * 
 * Note: This function is duplicated in scripts/handle_nagioscore.inc.php
 * and should be updated there if updated here.
 *
 * @param   array  $argv    Array of command line arguments
 * @return  array           Associative array of arguments
 */
function parse_argv($argv)
{
    array_shift($argv);
    $out = array();
    foreach ($argv as $arg) {

        if (substr($arg, 0, 2) == '--') {
            $eq = strpos($arg, '=');
            if ($eq === false) {
                $key = substr($arg, 2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            } else {
                $key = substr($arg, 2, $eq - 2);
                $out[$key] = substr($arg, $eq + 1);
            }
        } else if (substr($arg, 0, 1) == '-') {
            if (substr($arg, 2, 1) == '=') {
                $key = substr($arg, 1, 1);
                $out[$key] = substr($arg, 3);
            } else {
                $chars = str_split(substr($arg, 1));
                foreach ($chars as $char) {
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
        } else {
            $out[] = $arg;
        }
    }

    return $out;
}


/**
 * Gets a list of files in a directory
 * (http://www.php.net/manual/en/function.scandir.php#90628)
 *
 * @param   string  $d  Directory to list
 * @param   string  $x  Regex to match against
 * @return  array       Array of file in a directory
 */
function file_list($d, $x)
{
    $l = array();
    foreach (array_diff(scandir($d), array('.', '..')) as $f) {
        //echo "EXAMINING: $f\n";
        if (is_file($d . '/' . $f) && (($x) ? preg_match($x, $f) : 1))
            $l[] = $f;
    }
    return $l;
}


/**
 * Creates a string showing different file parameters
 * (http://www.php.net/manual/en/function.fileperms.php)
 *
 * @param   mixed   $perms  File permissions
 * @return  string          String of permissions
 */
function file_perms_to_string($perms)
{
    $info = "";

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x') :
        (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x') :
        (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x') :
        (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}


/**
 * Recursively delete a directory using PHP functions
 * (http://www.php.net/manual/en/function.unlink.php#87045)
 *
 * @param   string  $dir    Directory name
 * @param   bool    $drt    Delete specified top-level directory as well (default is true)
 */
function unlinkRecursive($dir, $drt=true)
{
    if (!$dh = @opendir($dir)) {
        return;
    }
    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..') {
            continue;
        }

        if (!@unlink($dir . '/' . $obj)) {
            unlinkRecursive($dir . '/' . $obj, true);
        }
    }

    closedir($dh);

    if ($drt) {
        @rmdir($dir);
    }

    return;
}


/**
 * Multi-dimensional array sorting
 * (improved version of: http://www.firsttube.com/read/sorting-a-multi-dimensional-array-with-php/)
 *
 * @param   array   $a          The array to sort
 * @param   string  $subkey     The subkey to sort by
 * @param   bool    $reverse    True to sort in reverse
 * @return  array               Sorted array
 */
function array_sort_by_subval($a, $subkey, $reverse = false)
{
    $b = array();
    foreach ($a as $k => $v) {
        $b[$k] = strtolower($v[$subkey]);
    }
    if ($reverse == false) {
        asort($b);
    } else {
        arsort($b);
    }
    $c = array();
    foreach ($b as $key => $val) {
        $c[] = $a[$key];
    }
    return $c;
}


/**
 * Multi-Byte unserialize funciton
 *
 * @param   string  $string     Replace string multibyte characters for unserialize
 * @return  string              Unserialized string (object/array)
 */
function mb_unserialize($string)
{
    $string = preg_replace_callback(
        '!s:(\d+):"(.*?)";!s',
        function ($matches) {
            if ( isset( $matches[2] ) )
                return 's:'.strlen($matches[2]).':"'.$matches[2].'";';
        },
        $string
    );
    return unserialize($string);
}


/**
 * Get an array of ip addresses within a cidr range
 *
 * @param   string  $cidr   IP address with cidr format
 * @return  mixed           False if IP not valid cidr, array of ips otherwise
 */
function cidr_to_range($cidr) {
    $ip_range = array();
    $split = explode('/', $cidr);
    if (!empty($split[0]) && is_scalar($split[1]) && filter_var($split[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $start = ip2long($split[0]) & ((-1 << (32 - (int)$split[1])));
        $end = ip2long($split[0]) + pow(2, (32 - (int)$split[1])) - 1;
        for ($i = $start; $i <= $end; $i++) {
            $ip_range[] = long2ip($i);
        }
        return $ip_range;
    }
    return false;
}