<?php
//
// General authenticating functions
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


include_once('utils.inc.php');


/**
 * Generates a header for redrecting to the login page
 * 
 * @param   bool    $logout     True to logout user before redirect
 */
function redirect_to_login($logout = false, $msg = '')
{
    if ($logout) {
        deinit_session();
        init_session();
    }

    // Add message for redirect
    if (!empty($msg)) { 
        $_SESSION["flash_msg"] = $msg;
    }

    $redirecturl = $_SERVER["PHP_SELF"];
    $redirecturl .= "%3f"; // Question mark
    $redirect_q_string = urlencode($_SERVER["QUERY_STRING"]);
    $redirecturl .= $redirect_q_string;

    $theurl = get_base_url() . PAGEFILE_LOGIN . "?redirect=$redirecturl";
    $theurl .= "&noauth=1"; // Needed for auto-login
    header("Location: " . $theurl);
}


/**
 * Check for user authentication
 * - Redirects user to the login screen if not authenticated
 * - Used in most pages that require authentication
 *
 * @param   bool    $redirect   True to redirect, false to exit
 * @param   bool    $backend    True to check backend auth also
 * @param   bool    $apikey     True to allow API key authentication
 */
function check_authentication($redirect = true, $backend = false, $apikey = false)
{
    global $request;

    // Check if we are authenticated, if not - redirect us (or not)
    if (!is_authenticated($backend, $apikey)) {

        // Redirect the user if we need to
        if ($redirect) {
            redirect_to_login();
        }

        // If we haven't authenticated or redirected, let's let them know
        // that they are not able to access this page
        echo _("Your session has timed out.");
        exit();
    }

    // Update session
    user_update_session();

    // Check restrictions on session and redirect or state session issues
    if (user_is_restricted_area()) {
        if ($redirect) {
            redirect_to_login(true, _('You cannot access that page with a restricted session.<br>Please log into your account.'));
        }
        echo _("Your current session is not authorized to view this page.");
        exit();
    }

    // Do successful check authentication callbacks
    $args = array();
    do_callbacks(CALLBACK_AUTHENTICATION_PASSED, $args);
}


/**
 * Check if a user is authenticated
 * - Checks backend authentication if BACKEND defined
 * - Checks session values for authentication
 *
 * This is only used for specific locations such as:
 * login.php, index.php, pageparts.inc.php, auth.inc.php, header.inc.php,
 * and footer.inc.php and check_authentication()
 *
 * @param   bool    $backend    True to force backend check also
 * @param   bool    $apikey     True to allow API key authentication
 * @return  bool                True if authenticated
 */
function is_authenticated($backend = false, $apikey = false)
{
    // Check if we are the BACKEND and do backend authentication
    if (defined("BACKEND") || $backend) {
        return is_backend_authenticated($apikey);
    }

    // Check if the session is already authenticated
    if (is_session_authenticated()) {
        return true;
    }

    // Check if auth token authentication is happening
    if (is_auth_token_authenticated()) {
        return true;
    }

    // Check for insecure login authenticaiton
    if (is_insecure_login_authenticated()) {
        return true;
    }

    if ($apikey && is_apikey_authenticated()) {
        return true;
    }

    // HTTP basic authentication support
    if (is_http_basic_authenticated($remote_user)) {
        $uid = get_user_id($remote_user);

        // User has authenticated, and they are configured in Nagios XI
        if ($uid > 0) {
            $_SESSION["user_id"] = $uid;
            $_SESSION["username"] = $remote_user;
            $_SESSION["session_id"] = user_generate_session();
            return true;
        } else {
            return false;
        }
    }

    return false;
}


/**
 * Check for API key authentication and log the user in if they have a valid key
 *
 * @return  bool            True if API key is authed
 */
function is_apikey_authenticated()
{
    $apikey = grab_request_var("apikey");

    if (empty($apikey)) {
        return false;
    }

    $apikey = escape_sql_param($apikey, DB_NAGIOSXI);

    // Get the user based on apikey
    $rs = exec_sql_query(DB_NAGIOSXI, "SELECT user_id, username, enabled, api_enabled FROM xi_users WHERE api_key = '$apikey';");

    foreach ($rs as $r) {
        if ($r['api_enabled'] == 1 && $r['enabled'] == 1) {
            $_SESSION['user_id'] = $r['user_id'];
            $_SESSION['username'] = $r['username'];
            return true;
        }
    }

    return false;
}


/**
 * Checks if a user has insecure login enabled, and checks if they have
 * passed the proper ticket in order to log in.
 *
 * @return  bool            True if user is authed
 */
function is_insecure_login_authenticated()
{
    $username = grab_request_var("username");
    $ticket = grab_request_var("ticket");

    if (empty($username) || empty($ticket)) {
        return false;
    }

    if (get_option("insecure_login", 0)) {

        $uid = get_user_id($username);
        if (empty($uid)) {
            return false;
        }

        // Check if enabled and the ticket matches, if it does, log them in and return true
        if (get_user_meta($uid, "insecure_login_enabled", 0)) {
            $il_ticket = get_user_meta($uid, "insecure_login_ticket", "");

            // $il_ticket is always 64 characters, so we don't need to worry about leaking the length.
            // use hash_equals() to prevent timing attacks.
            if (hash_equals($ticket, $il_ticket)) {
                $_SESSION["user_id"] = $uid;
                $_SESSION["username"] = $username;
                $_SESSION["session_id"] = user_generate_session();
                get_user_meta_session_vars(true);
                return true;
            }
        }
    }

    return false;
}


/**
 * Checks if an auth token is valid and if we should log someone
 * in or not using it.
 *
 * @return  bool            True if user valid
 */
function is_auth_token_authenticated()
{
    $auth_token = grab_request_var("token", "");
    if (empty($auth_token)) {
        return false;
    }

    // Check token for validity
    if (user_is_valid_auth_token($auth_token)) {
        $user_id = user_get_auth_token_user_id($auth_token);
        $username = get_user_attr($user_id, "username");

        // Log user in
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $username;
        set_user_meta($user_id, "lastlogintime", time());

        // Set token as used and update session ID to current user's session
        $session_id = user_generate_session();
        $_SESSION["session_id"] = $session_id;
        user_set_auth_token_session_id($session_id, $auth_token);

        // Set restrictions if there are any
        user_restrict_session_token($auth_token);
        $_SESSION["auth_token"] = $auth_token;

        // Get the user meta
        get_user_meta_session_vars(true);

        return true;
    }

    return false;
}


/**
 * Checks if a user is authenticated in the current session
 *
 * @return  bool    True if session is authenticated
 */
function is_session_authenticated()
{
    if (isset($_SESSION["user_id"])) {
        if (is_valid_user_id($_SESSION["user_id"]) === false) {
            return false;
        }

        // Add a session in the DB if one doesn't already exist
        if (empty($_SESSION["session_id"])) {
            $_SESSION["session_id"] = user_generate_session();
        }

        return true;
    }
    return false;
}


/**
 * Check if HTTP basic authentication is being used 
 * 
 * @return  bool    True if user is using HTTP basic auth
 */
function is_http_basic_authenticated(&$remote_user = '')
{
    $remote_user = "";
    if (isset($_SERVER["REMOTE_USER"])) {
        $remote_user = $_SERVER["REMOTE_USER"];
    }

    if (!empty($remote_user)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Check if auto-login has been enabled
 *
 * @return  bool    True if auto-login is enabled
 */
function is_autologin_enabled()
{
    $opt_s = get_option("autologin_options");
    if (empty($opt_s)) {
        return false;
    } else {
        $opts = unserialize($opt_s);
    }

    $enabled = grab_array_var($opts, "autologin_enabled");
    $username = grab_array_var($opts, "autologin_username");

    if ($enabled == 1 && !empty($username) && is_valid_user($username)) {
        return true;
    }

    return false;
}

