<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

$thedir = dirname(__FILE__);

require_once($thedir . '/constants.inc.php');
require_once($thedir . '/errors.inc.php');

require_once($thedir . '/utils-auditlog.inc.php');
require_once($thedir . '/utils-backend.inc.php');
require_once($thedir . '/utils-banner.inc.php');

require_once($thedir . '/utils-ccm.inc.php');
require_once($thedir . '/utils-commands.inc.php');

require_once($thedir . '/utils-components.inc.php');
require_once($thedir . '/utils-configwizards.inc.php');
require_once($thedir . '/utils-dashboards.inc.php');
require_once($thedir . '/utils-dashlets.inc.php');

require_once($thedir . '/utils-email.inc.php');
require_once($thedir . '/utils-events.inc.php');
require_once($thedir . '/utils-links.inc.php');
require_once($thedir . '/utils-graphs.inc.php');
require_once($thedir . '/utils-menu.inc.php');
require_once($thedir . '/utils-mibs.inc.php');
require_once($thedir . '/utils-metrics.inc.php');
require_once($thedir . '/utils-nagioscore.inc.php');
require_once($thedir . '/utils-notifications.inc.php');
require_once($thedir . '/utils-notificationmethods.inc.php');
require_once($thedir . '/utils-objects.inc.php');
require_once($thedir . '/utils-perms.inc.php');

require_once($thedir . '/utils-reports.inc.php');
require_once($thedir . '/utils-reports-export.inc.php');
require_once($thedir . '/utils-rrdexport.inc.php');

require_once($thedir . '/utils-sounds.inc.php');
require_once($thedir . '/utils-status.inc.php');
require_once($thedir . '/utils-systat.inc.php');
require_once($thedir . '/utils-tables.inc.php');
require_once($thedir . '/utils-tools.inc.php');
require_once($thedir . '/utils-themes.inc.php');
require_once($thedir . '/utils-updatecheck.inc.php');
require_once($thedir . '/utils-users.inc.php');
require_once($thedir . '/utils-views.inc.php');
require_once($thedir . '/utils-wizards.inc.php');
require_once($thedir . '/utils-xmlauditlog.inc.php');
require_once($thedir . '/utils-xmlobjects.inc.php');
require_once($thedir . '/utils-xmlreports.inc.php');
require_once($thedir . '/utils-xmlstatus.inc.php');
require_once($thedir . '/utils-xmlsysstat.inc.php');
require_once($thedir . '/utils-downtime.inc.php');
require_once($thedir . '/utils-banner_message.inc.php');

require_once($thedir . '/utils-time.inc.php');
require_once($thedir . '/utils-macros.inc.php');

require_once($thedir . '/utilsl.inc.php');
require_once($thedir . '/utilsx.inc.php');

$request_vars_decoded = false;


////////////////////////////////////////////////////////////////////////
// SESSION FUNCTIONS
////////////////////////////////////////////////////////////////////////


// Start the session (called on every page)
function init_session($lock = false, $autorefresh = true, $forceupdate = false)
{
    // Connect to DBs
    $dbok = db_connect_all();
    if ($dbok == false) {
        exit();
    }

    // We are running as a subsystem cron job
    if (defined("SUBSYSTEM")) {
        $_SESSION["user_id"] = 0;
        return;
    }

    // Set secure cookie if we are using SSL
    $secure = false;
    if (!empty($_SERVER['HTTPS'])) {
        $secure = true;
    }

    // Cookie timeout in seconds
    $cookie_timeout_mins = get_option('cookie_timeout_mins', 30);

    // If cookie timeout is set to 0, we should make the cookie set for 30 days and
    // force auto-refresh to on
    if ($cookie_timeout_mins == 0) {
        $cookie_timeout_mins = 60*24*30;
    }
    $cookie_timeout = $cookie_timeout_mins * 60;

    $cookie_path = "/";
    $garbage_timeout = $cookie_timeout + 600; // Cookie timeout + 10 minutes
    
    // Initialize the session
    if (!session_id()) {

        session_name("nagiosxi");

        // Set cookie params
        ini_set("session.use_cookies", "1");
        ini_set("session.use_only_cookies", "1");
        ini_set("session.cookie_lifetime", "0");

        // Disable upload progress (not needed, security issues)
        ini_set("session.upload_progress.enabled", "0");

        // Only allow our own sessions to generate, this only works in PHP 5.5.2+
        ini_set("session.use_strict_mode", "1");

        // Set cookie
        session_set_cookie_params($cookie_timeout, $cookie_path, '', $secure, true);
        ini_set("session.gc_maxlifetime", $garbage_timeout);
    
        // Start session
        session_start();

    }

    // Adust cookie timeout to reset after page refresh
    if (session_id() && $autorefresh) {
        setcookie(session_name(), session_id(), time() + $cookie_timeout, $cookie_path, '', $secure, true);
    }

    // Force update of session id
    if ($forceupdate) {
        session_regenerate_id(true);
    }

    // Do session start callbacks
    $args = array();
    do_callbacks(CALLBACK_SESSION_STARTED, $args);

    // Lock session writes as long as backend auth isn't being attempted
    if ($lock && !isset($_REQUEST['ticket']) && !isset($_REQUEST['token'])) {
        session_write_close();
    }

    // Set headers for frame security
    $frame_options_norestrict = get_option('frame_options_norestrict', 0);
    $req_frame_access = grab_request_var('req_frame_access', '');

    if (!$frame_options_norestrict) {
        $headers_set = false;

        // Check session values to see if we've already set a frame source
        if (array_key_exists('req_frame_access', $_SESSION)) {
            header("X-Frame-Options: ALLOW-FROM " . $_SESSION['req_frame_access']);
            header("Content-Security-Policy: frame-ancestors 'self' " . $_SESSION['req_frame_access']);
            $headers_set = true;
        }

        // Verify that the access domain is in the list of allowed URIs
        if (!empty($req_frame_access)) {
            $allowed_frame_uris = explode(',', get_option('frame_options_allowed_hosts', ''));
            $allowed_fusion_frame_uris = explode(',', get_option('frame_options_allowed_fusion_hosts', ''));
            $allowed_frame_uris = array_merge($allowed_frame_uris, $allowed_fusion_frame_uris);

            // Loop through and check if the given value is in the list
            if (count($allowed_frame_uris) > 0) {
                foreach ($allowed_frame_uris as $afu) {
                    $clean_afu = trim($afu);
                    if (!empty($afu) && $req_frame_access == $afu) {
                        header("X-Frame-Options: ALLOW-FROM " . $afu);
                        header("Content-Security-Policy: frame-ancestors 'self' " . $afu);
                        $headers_set = true;
                        $_SESSION['req_frame_access'] = $afu;
                        break;
                    }
                }
            }
        }

        // Set default frame configuration
        if (!$headers_set) {
            header("X-Frame-Options: SAMEORIGIN");
            header("Content-Security-Policy: frame-ancestors 'self'");
        }
    }
}


// Destroy the session and remove cookie (must actually be SENT to remove the cookie)
function deinit_session()
{
    // Remove session from DB
    user_remove_session(session_id());

    $_SESSION = array();

    // Delete the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/', '', false, true);
    }

    session_destroy();
}


////////////////////////////////////////////////////////////////////////
// REQUEST FUNCTIONS
////////////////////////////////////////////////////////////////////////


$escape_request_vars = true;
$request_vars_decoded = false;


function map_htmlentities($arrval, $strip_tags=false)
{
    if (is_array($arrval)) {
        if ($strip_tags) {
            $arrval = array_map("map_strip_tags", $arrval);
        }
        return array_map('map_htmlentities', $arrval);
    } else {
        if ($strip_tags) {
            $arrval = strip_tags($arrval);
        }
        return htmlentities($arrval, ENT_QUOTES);
    }
}


function map_strip_tags($arrval)
{
    if (is_array($arrval)) {
        return array_map("map_strip_tags", $arrval);
    } else {
        return strip_tags($arrval);
    }
}


function map_htmlentitydecode($arrval)
{
    if (is_array($arrval)) {
        return array_map('map_htmlentitydecode', $arrval);
    } else {
        return html_entity_decode($arrval, ENT_QUOTES);
    }
}


// Grabs POST and GET variables
function grab_request_vars($preprocess = true, $type = "")
{
    global $escape_request_vars;
    global $request;

    // Do we need to strip slashes?
    $strip = false;
    if ((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != "off"))) {
        $strip = true;
    }

    $request = array();

    if ($type == "" || $type == "get") {
        foreach ($_GET as $var => $val) {
            if ($escape_request_vars == true) {
                $request[$var] = map_htmlentities($val, true);
            } else {
                $request[$var] = $val;
            }
        }
    }

    if ($type == "" || $type == "post") {
        foreach ($_POST as $var => $val) {
            if ($escape_request_vars == true) {
                $request[$var] = map_htmlentities($val);
            } else {
                $request[$var] = $val;
            }
        }
    }

    // Strip slashes - we escape them later in SQL queries
    if ($strip == true) {
        foreach ($request as $var => $val) {
            if (is_array($val)) {
                $request[$var] = array_map('stripcslashes', $val);
            } else {
                $request[$var] = stripslashes($val);
            }
        }
    }
}


// Grabs a specific request variable
function grab_request_var($varname, $default = "")
{
    global $request;
    global $escape_request_vars;
    global $request_vars_decoded;

    $v = $default;
    if (isset($request[$varname])) {
        if ($escape_request_vars == true && $request_vars_decoded == false) {
            $v = map_htmlentitydecode($request[$varname]);
        } else {
            $v = $request[$varname];
        }
    }
    return $v;
}


function decode_request_vars()
{
    global $request;
    global $request_vars_decoded;

    $newarr = array();
    foreach ($request as $var => $val) {
        $newarr[$var] = grab_request_var($var);
    }

    $request_vars_decoded = true;

    $request = $newarr;
}


function get_pageopt($default = "")
{
    global $request;

    $popt = grab_request_var("pageopt", "");
    if ($popt == "") {
        if (count($request) > 0) {
            foreach ($request as $var => $val) {
                $popt = $var;
                break;
            }
        } else {
            $popt = $default;
        }
    }
    return $popt;
}


function have_value($var)
{
    if ($var == null)
        return false;
    if (!isset($var))
        return false;
    if (empty($var))
        return false;
    if (is_array($var))
        return true;
    if (!strcmp($var, ""))
        return false;
    return true;
}


////////////////////////////////////////////////////////////////////////
// Session flash message functions
////////////////////////////////////////////////////////////////////////


/**
 *  Creates a new 'flash_message' to be displayed on the next page by default. It can also be set
 *  to be persistent accross multiple pages.
 *
 *  @param string $text The main text of the message to display
 *  @param string $type The type of message: info, error, success
 *  @param array $options The options for the message including details, persistent, dismissable, etc
 */
function flash_message($text, $type='info', $options=array())
{
    $valid_types = array('info', 'success', 'error', 'warning');
    if (!in_array($type, $valid_types)) {
        $type = 'info';
    }

    // Create message array (with default values)
    $msg = array('message' => $text,
                 'type' => $type,
                 'persistent' => 0,
                 'dismissable' => 1,
                 'keep' => 1);

    if (!empty($options)) {

        // Add details
        if (array_key_exists('details', $options)) {
            $msg['details'] = $options['details'];
        }

        // Persistence
        if (array_key_exists('persistent', $options)) {
            $msg['persistent'] = (bool) $options['persistent'];
        }

        // Dismissable
        if (array_key_exists('dismissable', $options)) {
            $msg['dismissable'] = (bool) $options['dismissable'];
        }

    }

    // Set session values
    $_SESSION['msg'] = $msg;
}


/**
 *  Keeps flash message for another page load if called before page_start() function
 *
 *  @return boolean True if successful add to keep value, False if no message exists
 */
function keep_flash_message() {
    if (array_key_exists('msg', $_SESSION)) {
        $_SESSION['msg']['keep']++;
        return true;
    }
    return false;
}


/**
 *  Generates the HTML for a flash message
 *
 *  @return string Full flash message HTML with details hidden if details exist
 */
function get_flash_message()
{
    $html = '';
    $msg = array();
    if (empty($_SESSION['msg'])) {
        return $html;
    } else {
        $msg = $_SESSION['msg'];
    }

    // Update keep value
    $msg['keep'] = intval($msg['keep']);
    if ($msg['keep'] > 0) {
        $msg['keep']--;
    }

    // Generate details html
    $details_html = '';
    if (!empty($msg['details'])) {
        $details_html = '<span class="msg-show-details">' . _('Show Details') . '</span>  <i class="fa msg-show-details-icon fa-chevron-up"></i>
            <div class="msg-details">' . $msg['details'] . '</div>';
    }

    // Can be dismissable
    $dismiss = '';
    if ($msg['dismissable']) {
        $dismiss = '<span class="msg-close tt-bind" title="' . _('Dismiss') . '" data-placement="left"><i class="fa fa-times"></i></span>';
    }

    // Generate full message
    $html = '<div class="flash-msg ' . encode_form_val($msg['type']) . '">
                <span class="msg-text">
                    ' . $msg['message'] . '
                    ' . $details_html . '
                </span>
                ' . $dismiss . '
                <div class="clear"></div>
            </div>';

    // Remove message
    if (!$msg['persistent']) {
        if ($msg['keep'] <= 0) {
            unset($_SESSION['msg']);
        } else {
            $_SESSION['msg'] = $msg;
        }
    }

    return $html;
}


////////////////////////////////////////////////////////////////////////
// LANGUAGE FUNCTIONS
////////////////////////////////////////////////////////////////////////


function set_language($language)
{
    ini_set('default_charset', 'UTF-8');

    // Fix for wrong en_US language name (en and en_EN)
    if ($language == 'en' || $language == 'en_EN') {
        $language = 'en_US';
    }

    // Only set gettext (now _()) locale if we have a language file
    if (!file_exists(dirname(__FILE__) . '/lang/locale/' . $language)) {
        return;
    }

    // Set session language
    $_SESSION["language"] = $language;

    // Set the locale/environment language
    setlocale(LC_MESSAGES, $language, $language . 'utf-8', $language . 'utf8', "en_US.utf8");
    putenv("LANG=" . $language);

    // Non-English numeric formats will turn decimals to commas and mess up all kinds of stuff
    // so we aren't going to do that
    setlocale(LC_NUMERIC, 'C');

    // Bind text domains
    bindtextdomain($language, dirname(__FILE__) . '/lang/locale/');
    bind_textdomain_codeset($language, 'UTF-8');
    textdomain($language);
}


function init_language()
{
    // URL override
    $locale = grab_request_var('locale', '');
    if (!empty($locale)) {
        set_language($locale);
    }

    $session_language = '';
    // Read session language if available
    if (!empty($_SESSION['language'])) {
        $session_language = $_SESSION["language"];
    } else {
        // Try user-specific and global default language from DB
        $udblang = get_user_meta(0, "default_language");
        if (!empty($udlang)) {
            $session_language = $udblang;
        } else {
            $dblang = get_option("default_language");
            $session_language = $dblang;
        }
    }

    set_language($session_language);

    return true;
}


function get_languages()
{
    global $cfg;

    $dirs = scandir(dirname(__FILE__) . '/lang/locale');

    // Add directories to language options
    foreach ($dirs as $dir) {
        if (is_dir(dirname(__FILE__) . '/lang/locale/' . $dir) && strpos($dir, '.') === false && !isset($cfg[$dir])) {
            $newlang = htmlentities(utf8_encode($dir), ENT_QUOTES, 'UTF-8');
            $cfg['languages'][$newlang] = $newlang;
            if ($newlang == 'en_EN') {
                $cfg['languages'][$newlang] = 'en_US';
            }
        }
    }

    return $cfg['languages'];
}


function get_language_nicename($lang)
{
    switch ($lang) {
        case "en_US":
            return _("English") . " (English)";
        case "de_DE":
            return _("German") . " (Deutsche)";
        case "es_ES":
            return _("Spanish") . " (Español)";
        case "fr_FR":
            return _("French") . " (Français)";
        case "it_IT":
            return _("Italian") . " (Italiano)";
        case "ja_JP":
            return _("Japanese") . " (日本語)";
        case "ko_KR":
            return _("Korean") . " (한국어)";
        case "pt_PT":
            return _("Portuguese") . " (Português)";
        case "ru_RU":
            return _("Russian") . " (Русский)";
        case "zh_CN":
            return _("Simplified Chinese") . " (简体中文)";
        case "zh_TW":
            return _("Traditional Chinese") . " (繁體中文)";
        case "pl_PL":
            return _("Polish") . " (Polski)";
        case "cs_CZ":
            return _("Czech") . " (Čeština)";
        case "bg_BG":
            return _("Bulgarian") . " (Български)";
        default:
            return $lang;
    }
}


////////////////////////////////////////////////////////////////////////
// FORM FUNCTIONS
////////////////////////////////////////////////////////////////////////


function encode_form_val($rawval)
{
    return htmlentities($rawval, ENT_COMPAT, 'UTF-8');
}

function encode_form_valq($rawval)
{
    return htmlentities($rawval, ENT_QUOTES, 'UTF-8');
}


function yes_no($var)
{
    if (isset($var) && ($var == 1 || $var == true)) {
        return _("Yes");
    }
    return _("No");
}


function is_selected($var1, $var2)
{
    if (is_string($var1) || is_string($var2)) {
        if (!strcmp($var1, $var2)) {
            return "SELECTED";
        }
    } else {
        if ($var1 == $var2) {
            return "SELECTED";
        }
    }
    return "";
}


function is_checked($var1, $var2 = "on")
{
    if ($var1 == $var2)
        return "CHECKED";
    else if (is_string($var1) && $var1 == "on")
        return "CHECKED";
    else if (!strcmp($var1, $var2))
        return "CHECKED";
    else
        return "";
}


function checkbox_binary($var1)
{
    if (isset($var1)) {
        if (is_numeric($var1)) {
            if ($var1 == 1) {
                return 1;
            }
        } else if (is_string($var1) && $var1 == "on") {
            return 1;
        }
    }
    return 0;
}


////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////


// Gets value from array and give default
function grab_array_var($arr, $varname, $default = "")
{
    $v = $default;
    if (is_array($arr)) {
        if (array_key_exists($varname, $arr)) {
            $v = $arr[$varname];
        }
    }
    return $v;
}

// Generates a random alpha-numeric string (password or backend ticket)
function random_string($len = 6, $additional_chars = '')
{
    $chars = "023456789abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ" . $additional_chars;
    $rnd = "";
    $charlen = strlen($chars);

    srand((double)microtime() * 1000000);

    for ($x = 0; $x < $len; $x++) {
        $num = rand() % $charlen;
        $ch = substr($chars, $num, 1);
        $rnd .= $ch;
    }

    return $rnd;
}


// See if NDOUtils tables exist
function ndoutils_exists()
{
    if (!exec_named_sql_query('CheckNDOUtilsInstall', false)) {
        return false;
    }
    return true;
}


// See if installation is needed
function install_needed()
{
    $db_version = get_db_version();
    if ($db_version == null) {
        return true;
    }

    $installed_version = get_install_version();
    if ($installed_version == null) {
        return true;
    }

    return false;
}


// See if upgrade is needed
function upgrade_needed()
{
    global $cfg;

    if (is_dev_mode()) {
        return false;
    }

    $db_version = get_db_version();

    if (strcmp($db_version, $cfg['db_version'])) {
        return true;
    }

    $installed_version = get_install_version();
    if ($installed_version != get_product_version()) {
        return true;
    }

    return false;
}


// Get currently install db version
function get_db_version()
{
    $db_version = get_option('db_version');
    return $db_version;
}


function set_db_version($version = "")
{
    global $cfg;
    if ($version == "") {
        $dbv = $cfg['db_version'];
    } else {
        $dbv = $version;
    }
    set_option('db_version', $dbv);
}


// Get currently installed version
function get_install_version()
{
    $db_version = get_option('install_version');
    return $db_version;
}


function set_install_version($version = "")
{
    if ($version == "") {
        $iv = get_product_version();
    } else {
        $iv = $version;
    }
    set_option('install_version', $iv);
}


////////////////////////////////////////////////////////////////////////
// URL FUNCTIONS
////////////////////////////////////////////////////////////////////////


// Returns base URL used to access product
function get_base_url($usefullpath = true)
{
    return get_base_uri($usefullpath);
}


// Returns URL used to access XI from public networks
function get_external_url()
{
    $url = get_option("external_url");
    if ($url == "") {
        $url = get_option("url");
    }
    return $url;
}

// Returns the URL used to access XI from internal networks or itself
function get_internal_url()
{
    $url = get_option("url");
    return $url;
}


// Returns base URI used to access product
function get_base_uri($usefullpath = true, $nobase = false)
{
    global $cfg;

    $base_url = $cfg['base_url'] . "/";

    if ($usefullpath == true) {
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $proto = "https";
        } else {
            $proto = "http";
        }
        if (isset($_SERVER["SERVER_PORT"]) && ($proto == "http" && $_SERVER["SERVER_PORT"] != "80") || ($proto == "https" && $_SERVER["SERVER_PORT"] != "443")) {
            $port = ":" . $_SERVER["SERVER_PORT"];
        } else {
            $port = "";
        }

        $hostname = "localhost";
        if (isset($_SERVER['SERVER_NAME'])) {
            $hostname = $_SERVER['SERVER_NAME'];
        }

        // Check if SERVER_NAME is an ipv6 address
        if (filter_var($hostname, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV6))) {
            $hostname = '['.$hostname.']';
        }

        $url = $proto . "://" . $hostname . $port;

        if ($nobase) {
            return $url;
        }
        
        $url .= $base_url;
    } else {
        $url = $base_url;
    }

    return $url;
}

// Return the base URI used to access the product
// FROM the server itself (e.g. a curl response embedded in a page)
function get_localhost_url() {

    global $cfg;

    $https = grab_array_var($cfg, 'use_https', false);
    $proto = ($https) ? "https" : "http";

    $port = grab_array_var($cfg, 'port_number' , false); 
    $port = ($port) ? ":$port" : '';

    $url_base = str_replace("//","/", "localhost$port/" . get_base_url(false));
    $url = "$proto://" . $url_base;

    return $url;
}


// Returns URL to ajax helper
function get_ajax_helper_url()
{
    $url = get_base_url(true);
    $url .= PAGEFILE_AJAXHELPER;
    return $url;
}


// Returns URL to ajax proxy
function get_ajax_proxy_url()
{
    $url = get_base_url(true);
    $url .= PAGEFILE_AJAXPROXY;
    return $url;
}


// Returns URL to suggest
function get_suggest_url()
{
    $url = get_base_url(true);
    $url .= PAGEFILE_SUGGEST;
    return $url;
}


// Returns URL to update check page
function get_update_check_url()
{
    $url = "https://www.nagios.com/checkforupdates/?product=" . get_product_name(true) . "&version=" . get_product_version() . "&build=" . get_product_build();
    return $url;
}


// Returns URL used to access current page
function get_current_url($baseonly = false, $fulluri = false)
{
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $proto = "https";
    } else {
        $proto = "http";
    }

    if (($proto == "http" && $_SERVER["SERVER_PORT"] != "80") || ($proto == "https" && $_SERVER["SERVER_PORT"] != "443")) {
        $port = ":" . $_SERVER["SERVER_PORT"];
    } else {
        $port = "";
    }

    if ($fulluri == true) {
        $uri = $_SERVER["REQUEST_URI"];
        $url = $proto . "://" . $_SERVER['SERVER_NAME'] . $port . $uri;
    } else {
        $page = encode_form_valq($_SERVER['PHP_SELF']);
        if ($baseonly == true && ($last_slash = strrpos($page, "/"))) {
            $page = substr($page, 0, $last_slash + 1);
        }
        $url = $proto . "://" . $_SERVER['SERVER_NAME'] . $port . $page;
    }

    return $url;
}


// Returns current page (used for online help and feedback submissions)
function get_current_page($baseonly = false)
{
    $page = encode_form_valq($_SERVER['PHP_SELF']);

    if ($last_slash = strrpos($page, "/")) {
        $page_name = substr($page, $last_slash + 1);
    } else {
        $page_name = $page;
    }

    return $page_name;
}


function build_url_from_current($args)
{
    global $request;
    $url = get_current_url();

    // Possible override original request variables
    $r = $request;
    foreach ($args as $var => $val) {
        $r[$var] = $val;
    }

    // Generate query string
    $url .= "?";
    foreach ($r as $var => $val) {
        $url .= "&" . urlencode($var) . "=" . urlencode($val);
    }

    return $url;
}


////////////////////////////////////////////////////////////////////////
// TIMING FUNCTIONS
////////////////////////////////////////////////////////////////////////


function get_timer()
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime;
    return $starttime;
}


function get_timer_diff($starttime, $endtime)
{
    $totaltime = ($endtime - $starttime);
    return number_format($totaltime, 5);
}


////////////////////////////////////////////////////////////////////////
// OPTION FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Returns an option from the database
 *
 * @param      $name    The name of the option
 * @param null $default The value of the option if it doesn't exist in the database
 *
 * @return string|null The option value
 */
function get_option($name, $default = null)
{
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["options"] . " WHERE name='" . escape_sql_param($name, DB_NAGIOSXI) . "'";

    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, false))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["value"];
        }
    }
    return $default;
}


/**
 * Sets an option in the database.
 *
 * @param $name  The name of the option
 * @param $value The value the option should hold (normally string, int, or base64 encoded serialized array)
 *
 * @return mixed
 */
function set_option($name, $value)
{
    global $db_tables;

    // See if data exists already
    $key_exists = false;
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["options"] . " WHERE name='" . escape_sql_param($name, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0) {
            $key_exists = true;
        }
    }

    // Insert new key
    if ($key_exists == false) {
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["options"] . " (name,value) VALUES ('" . escape_sql_param($name, DB_NAGIOSXI) . "','" . escape_sql_param($value, DB_NAGIOSXI) . "')";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    } else {
        $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["options"] . " SET value='" . escape_sql_param($value, DB_NAGIOSXI) . "' WHERE name='" . escape_sql_param($name, DB_NAGIOSXI) . "'";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    }
}


/**
 * @param $name
 *
 * @return mixed
 */
function delete_option($name)
{
    global $db_tables;

    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["options"] . " WHERE name='" . escape_sql_param($name, DB_NAGIOSXI) . "'";
    return exec_sql_query(DB_NAGIOSXI, $sql);
}


/**
 * Gets an array from the database and decodes it into PHP array
 */
function get_array_option($name, $default = array())
{
    $tmp = get_option($name, $default);
    if (!empty($tmp)) {
        $tmp = unserialize(base64_decode($tmp));
    }
    return $tmp;
}


/**
 * Saves an array option in the database as a serialized, base64 encoded block of text
 */
function set_array_option($name, $array)
{
    set_option($name, base64_encode(serialize($array)));
    return true;
}


/**
 * Gets an array from the database and decodes it into PHP array
 */
function get_array_option_json($name, $default = array())
{
    $tmp = get_option($name, $default);
    if (!empty($tmp)) {
        $tmp = json_decode(base64_decode($tmp), true);
    }
    return $tmp;
}


/**
 * Saves an array option in the database as a jscon encoded, base64 encoded block of text
 */
function set_array_option_json($name, $array)
{
    set_option($name, base64_encode(json_encode($array)));
    return true;
}


////////////////////////////////////////////////////////////////////////
// MISC  FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @return bool
 */
function in_demo_mode()
{
    global $cfg;

    if (isset($cfg['demo_mode']) && $cfg['demo_mode'] == true)
        return true;

    return false;
}


// returns attribute value of a simplexml object
/**
 * @param $obj
 * @param $att
 *
 * @return string
 */
function get_xml_attribute($obj, $att)
{
    foreach ($obj->attributes() as $a => $b) {
        if ($a == $att)
            return $b;
    }
    return "";
}


/**
 * @param $address
 *
 * @return bool
 */
function valid_ip($address)
{
    if (!have_value($address))
        return false;
    return true;
}

/**
 * @param $email
 *
 * @return bool
 */
function valid_email($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Test for localhost (we will allow that)
        $tmp = explode('@', $email);
        if (count($tmp) == 2 && $tmp[1] == 'localhost' && preg_match("/^[a-zA-Z0-9_\-.]*$/", $tmp[0])) {
            return true;
        }
        return false;
    }
    return true;
}


/**
 * @param $component
 * @param $cname
 *
 * @return null
 */
function get_component_credential($component, $cname)
{
    global $cfg;

    $optname = $component . "_" . $cname;

    $optval = get_option($optname);
    if ($optval == null || have_value($optval) == false) {
        // default to config file value if we didn't find it in the database
        $optval = $cfg['component_info'][$component][$cname];
        set_option($optname, $optval);
    }

    return $optval;
}

/**
 * @param $component
 * @param $cname
 * @param $val
 *
 * @return bool
 */
function set_component_credential($component, $cname, $val)
{
    $optname = $component . "_" . $cname;
    set_option($optname, $val);
    return true;
}

/**
 * @return string
 */
function get_throbber_html()
{
    $html = '<div class="sk-spinner sk-spinner-circle">
        <div class="sk-circle1 sk-circle"></div>
        <div class="sk-circle2 sk-circle"></div>
        <div class="sk-circle3 sk-circle"></div>
        <div class="sk-circle4 sk-circle"></div>
        <div class="sk-circle5 sk-circle"></div>
        <div class="sk-circle6 sk-circle"></div>
        <div class="sk-circle7 sk-circle"></div>
        <div class="sk-circle8 sk-circle"></div>
        <div class="sk-circle9 sk-circle"></div>
        <div class="sk-circle10 sk-circle"></div>
        <div class="sk-circle11 sk-circle"></div>
        <div class="sk-circle12 sk-circle"></div>
    </div>';
    return $html;
}


////////////////////////////////////////////////////////////////////////
// DIRECTORY FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get the current directory of the script running
 *
 * @return  string      Full path of current directory
 */
function get_current_dir()
{
    global $argv;
    $cur_dir = realpath($argv[0]);
    return $cur_dir;
}


/**
 * Get the full path to the Nagios XI root directory (nagiosxi)
 *
 * @return  string      Full path to nagiosxi
 */
function get_root_dir()
{
    global $cfg;

    $root_dir = "/usr/local/nagiosxi";

    if (array_key_exists("root_dir", $cfg)) {
        $root_dir = $cfg["root_dir"];
    }

    return $root_dir;
}


/**
 * Get the full path to the Nagios XI HTML directory (nagiosxi/html)
 *
 * @return  string      Full path to nagiosxi/html
 */
function get_base_dir()
{
    $base_dir = get_root_dir() . "/html";

    if (defined("BACKEND") && BACKEND == true) {
        $base_dir = substr($base_dir, 0, -8);
    }

    return $base_dir;
}


/**
 * @return string
 */
function get_tmp_dir()
{
    $tmp_dir = get_root_dir() . "/tmp";
    return $tmp_dir;
}


/**
 * @return string
 */
function get_backend_dir()
{
    $backend_dir = get_base_dir() . "/backend";
    return $backend_dir;
}


/**
 * @return null|string
 */
function get_subsystem_ticket()
{
    $ticket = get_option("subsystem_ticket");
    if ($ticket == null || have_value($ticket) == false) {
        $ticket = random_string(8);
        set_option("subsystem_ticket", $ticket);
    }
    return $ticket;
}


////////////////////////////////////////////////////////////////////////
// XML DB FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param        $level
 * @param        $rs
 * @param        $fieldname
 * @param string $nodename
 *
 * @return string
 */
function get_xml_db_field($level, $rs, $fieldname, $nodename = "")
{
    if ($nodename == "")
        $nodename = $fieldname;
    return get_xml_field($level, $nodename, get_xml_db_field_val($rs, $fieldname));
}


/**
 * @param $rs
 * @param $fieldname
 *
 * @return string|XML
 */
function get_xml_db_field_val($rs, $fieldname)
{
    if (is_object($rs)) {
        if (@isset($rs->fields[$fieldname])) {
            return xmlentities($rs->fields[$fieldname]);
        }
    } else if (is_array($rs)) {
        if (@isset($rs[$fieldname])) {
            return xmlentities($rs[$fieldname]);
        }
    }
    return "";
}


/**
 * @param $level
 * @param $nodename
 * @param $nodevalue
 *
 * @return string
 */
function get_xml_field($level, $nodename, $nodevalue)
{
    $output = "";
    for ($x = 0; $x < $level; $x++)
        $output .= "  ";
    $output .= "<" . $nodename . ">" . xmlentities($nodevalue) . "</" . $nodename . ">\n";
    return $output;
}


////////////////////////////////////////////////////////////////////////
// META DATA FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param $type_id
 * @param $obj_id
 * @param $key
 *
 * @return null
 */
function get_meta($type_id, $obj_id, $key)
{
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["meta"] . " WHERE metatype_id='" . escape_sql_param($type_id, DB_NAGIOSXI) . "' AND metaobj_id='" . escape_sql_param($obj_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["keyvalue"];
        }
    }
    return null;
}


/**
 * @param $type_id
 * @param $obj_id
 * @param $key
 * @param $value
 *
 * @return mixed
 */
function set_meta($type_id, $obj_id, $key, $value)
{
    global $db_tables;

    // see if data exists already
    $key_exists = false;
    if (get_meta($type_id, $obj_id, $key) != null)
        $key_exists = true;

    // insert new key
    if ($key_exists == false) {
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["meta"] . " (metatype_id,metaobj_id,keyname,keyvalue) VALUES ('" . escape_sql_param($type_id, DB_NAGIOSXI) . "','" . escape_sql_param($obj_id, DB_NAGIOSXI) . "','" . escape_sql_param($key, DB_NAGIOSXI) . "','" . escape_sql_param($value, DB_NAGIOSXI) . "')";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    } // update existing key
    else {
        $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["meta"] . " SET keyvalue='" . escape_sql_param($value, DB_NAGIOSXI) . "' WHERE metatype_id='" . escape_sql_param($type_id, DB_NAGIOSXI) . "' AND metaobj_id='" . escape_sql_param($obj_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    }

}


/**
 * @param $type_id
 * @param $obj_id
 * @param $key
 *
 * @return mixed
 */
function delete_meta($type_id, $obj_id, $key)
{
    global $db_tables;

    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["meta"] . " WHERE metatype_id='" . escape_sql_param($type_id, DB_NAGIOSXI) . "' AND metaobj_id='" . escape_sql_param($obj_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
    return exec_sql_query(DB_NAGIOSXI, $sql);
}


////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Used to generate alert/info message boxes used in form pages  
 *
 * @param   bool    $error
 * @param   bool    $info
 * @param   string  $msg
 * @return  string
 */
function get_message_text($error = true, $info = true, $msg = "")
{
    $output = "";

    if (have_value($msg)) {
        if ($error == true)
            $divclass = "errorMessage";
        else if ($info == true)
            $divclass = "infoMessage";
        else
            $divclass = "actionMessage";

        $output .= '
		<div class="message">
		<ul class="' . $divclass . '">
		';

        if (is_array($msg)) {
            foreach ($msg as $m)
                $output .= "<li>" . $m . "</li>";
        } else
            $output .= "<li>" . $msg . "</li>";

        $output .= '
		</ul>
		</div>
		';
    }

    return $output;
}


/**
 * Used for debugging and viewing arrays in the web browser
 *
 * @param   array   $array
 */
function array_dump($array)
{
    print "<pre>" . print_r($array, true) . "</pre>";
}


/**
 * Benchmarking function to stop the timer
 *
 * @return  float       Timer start time
 */
function timer_start()
{
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = $time;
    return $start;
}


/**
 * Benchmarking function to stop the timer
 * 
 * @param   float   $start  Start time
 * @return  float           Timer total time
 */
function timer_stop($start)
{
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    return $total_time;
}


/**
 * Display a readable value
 */
function human_readable_bytes($bytes)
{
    $base = log($bytes) / log(1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), 2) . " " . $suffixes[floor($base)];
}


/**
 * Check if autocomplete should be allowed or not based on global settings for sensitive fields
 *
 * @return  string
 */
function sensitive_field_autocomplete()
{
    if (get_option('sensitive_field_autocomplete')) {
        return "";
    } else {
        return " autocomplete='off'";
    }
}


/**
 * Check if a valid URL using PHP's filter_var, but also check if it's IPv6
 *
 * @param   string  $url    URL
 * @return  bool            True if valid URL
 */
function valid_url($url)
{
    if (!have_value($url)) {
        return false;
    }

    // If not valid, check IPv6
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {

        // Check for host being ipv6
        $host = parse_url($url, PHP_URL_HOST);
        $ipv6 = trim($host, '[]');

        // Replace with generic host to validate url if IPv6 is valid
        if (filter_var($ipv6, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV6))) {
            $url = str_replace($host, 'validhost.com', $url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return true;
            }
        }

        return false;
    }

    return true;
}


/**
 * Get the PHP upload_max_filesize in Bytes for inputs
 *
 * @return  int     Size in bytes
 */
function get_php_upload_max_filesize()
{
    $size = 20000000; // 20M because we don't know upload max size
    $upload_max_filesize = ini_get('upload_max_filesize');

    if (empty($upload_max_filesize)) {
        return $size;
    }

    $num = substr($upload_max_filesize, 0, strlen($upload_max_filesize) - 1);
    $unit = substr($upload_max_filesize, -1);

    if ($unit == 'M') {
        $size = $num * 1000000;
    } else if ($unit == 'G') {
        $size = $num * 1000000000;
    }

    return $size;
}


/**
 * Get the PHP upload_max_filesize in Bytes for inputs
 *
 * @param   string  $path  Path to sanatize
 * @return  string         Sanatized path
 */
function clean_path($path) {
    return str_replace(array('..', '//'), array('', '/'), $path);
}
