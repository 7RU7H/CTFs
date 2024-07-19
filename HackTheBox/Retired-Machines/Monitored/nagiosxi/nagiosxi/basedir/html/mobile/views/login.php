<?php
//
// Nagios XI Mobile Interface
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

// Base XI code base
require_once('../../includes/common.inc.php');
// Mobile XI code base
require_once('../controllers/main-utils.php');
load_controllers();

// Initialization stuff
pre_init();
init_session();
grab_request_vars();
decode_request_vars();

// Check prereqs and authentication
check_prereqs();

route_request();

function route_request()
{
    $mode = grab_request_var('mode', '');

    switch ($mode) {
        case 'login':
            do_login();
            break;
        case 'logout':
            do_logout();
            break;
        default:
            show_login();
    }
}

function show_login()
{
    mobile_page_start(_('Login'));

    // Grab username in case of failed login attempt
    $username = grab_request_var('username', '');
    ?>

    <div id="login-bg">
        <div id="login-container">
            <div>
                <div id="login-logo-container"><img src="<?php echo load_image('white-n.svg'); ?>" id="login-logo" alt="Nagios Logo"></div>
            </div>
            <h1><?php echo _('Nagios XI Mobile'); ?></h1>
            <div>
                <form id="login-form" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
                    <input type="text" name="username" class="login-input" id="login-username" placeholder="Username">
                    <input type="password" name="password" class="login-input" id="login-password" placeholder="Password">
                    <button type="submit" id="login-button" class="button shadow-2"><?php echo _('Log In'); ?></button>
                    <input type="hidden" name="mode" value="login">
                    <?php echo get_nagios_session_protector(); ?>
                </form>
            </div>
        </div>
    </div>

    <?php
    mobile_page_end();
}

function show_two_factor_verify($error = false, $msg = '')
{
    // Remember browser cookie
    $tf_cookie = intval(grab_request_var('tf_cookie', 0));

    // Grab two factor auth settings
    $two_factor_cookie = get_option('two_factor_cookie', 0);
    $two_factor_cookie_timeout = intval(get_option('two_factor_cookie_timeout', 90));

    mobile_page_start(_('Verification'));
    ?>

    <div id="login-bg">
        <div id="login-container">
            <div>
                <div id="login-logo-container"><img src="<?php echo load_image('white-n.svg'); ?>" id="login-logo" alt="Nagios Logo"></div>
            </div>
            <div>
                <h1><?php echo _("Two Factor Verification"); ?></h1>
                <p style="color: #FFF; text-align: center; padding: 20px 0;"><?php echo _("An email has been sent with a 5 digit verifcation token. Enter it below."); ?></p>
            </div>
            <form id="login-form" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
                <?php display_message($error, false, $msg); ?>
                <input type="hidden" name="mode" value="login">
                <?php echo get_nagios_session_protector(); ?>
                <input type="text" name="tf_token" class="login-input" id="login-tf_token" placeholder="Token">
                <?php if ($two_factor_cookie) { ?>
                <div class="checkbox" style="text-align: center; padding-top: 20px;">
                    <label style="color: #DDD !important;">
                        <input type="checkbox" name="tf_cookie" value="1" <?php echo is_checked($tf_cookie, 1); ?>>
                        <?php echo _("Remember this browser"); ?>
                    </label>
                    <i class="fa fa-question-circle tt-bind" style="font-size: 13px; margin-left: 5px; color: #FFF; font-size: 2rem" title="<?php echo sprintf(_('If you select this option, you will not be required to verify your email for this username in this browser for the next %d days'), $two_factor_cookie_timeout); ?>"></i>
                </div>
                <?php } ?>
                <div style="margin-top: 20px;">
                    <button type="submit" id="login-button" class="button shadow-2"><?php echo _('Verify'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    mobile_page_end();
    exit();
}

function do_login()
{
    global $db_tables;

    //check_nagios_session_protector();

    // Grab requested info
    $username = grab_request_var("username", null);
    $password = grab_request_var("password");
    $debug = grab_request_var("debug");
    $locale = grab_request_var('locale');

    // Check if we are doing two factor auth
    $two_factor_auth = get_option('two_factor_auth', 0);
    $two_factor_cookie = get_option('two_factor_cookie', 0);

    // Defaulting to invalid username and password unless we know otherwise
    $login_error = true;
    $login_errmsg = _("Invalid username or password.");

    // User has already authenticated, but is doing two factor
    if (isset($_SESSION['tf_username']) && $two_factor_auth && $username === null) {
        $login_error = false;
        $username = $_SESSION['tf_username'];
        unset($_SESSION['tf_username']);
    } else {

        // Unset the username if it exists and we are trying to log in
        unset($_SESSION['tf_username']);

        // Do auto-login before checking for user...
        $autologin = false;
        $loginbutton = grab_request_var("loginButton");
        if ($loginbutton == _("Auto-Login") && is_autologin_enabled() == true) {
            $opts_s = get_option("autologin_options");
            $opts = unserialize($opts_s);
            $username = grab_array_var($opts, "autologin_username");
            $autologin = true;
            $login_error = false;
        }

        // If there is a username let's check authentication
        if (have_value($username)) {

            // Check if the account is locked out
            if (locked_account($username)) {

                $login_errmsg = _("This account has been locked. Please contact the administrator.");
                $login_error = true;
                
            } else {

                // Do the credentials check out? Remove the error if they do!
                if (check_login_credentials($username, $password, $info_msg)) {
                    $login_error = false;

                    // Also check to see if the password requirements are enforced and the length of time that the password has been around
                    if (!allowed_password_age($username)) {
                        set_user_meta(get_user_id($username), 'forcepasswordchange', "1");
                    }
                }
            }
        }

    }

    // Do some actual login processing
    if ($login_error == false) {

        // Check if we are doing two factor auth or not
        if ($two_factor_auth && !$autologin) {

            $user_id = get_user_id($username);

            // Check cookie to see if we need to do two factor auth
            $do_tf_auth = true;
            if (user_tfv_verify($username)) {
                $do_tf_auth = false;
            }

            if ($do_tf_auth) {

                // Check if we are sending in a token, or send one if the user doesn't have one
                $tf_token = grab_request_var('tf_token', null);
                if ($tf_token === null) {
                    send_two_factor_token($username);
                    $_SESSION['tf_username'] = strtolower($username);
                    show_two_factor_verify();
                } else {

                    // Try to validate the token sent and error if it fails
                    if (!verify_two_factor_auth($username, $tf_token)) {
                        $token_data = get_user_meta($user_id, 'two_factor_token');
                        if (empty($token_data)) {
                            // Send user to the login page if token has expired
                            $msg = _("Two factor validation token has expired.");
                            show_login(true, $msg);
                        } else {
                            $msg = _("Could not validate token. Only the last token sent is valid.");
                            $_SESSION['tf_username'] = strtolower($username);
                            show_two_factor_verify(true, $msg);
                        }
                    }

                    // Save if the cookie was set
                    $tf_cookie = grab_request_var('tf_cookie', 0);
                    if ($tf_cookie && $two_factor_cookie) {
                        user_tfv_generate_token($username);
                    }

                }
            }

        }

        // Destroy the current session and re-initialize
        init_session(false, true, true);

        // look the user information up in Nagios XI database
        // fix: force username to lowercase to prevent problems with Nagios Core permissions
        $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE lower(username)='" . escape_sql_param(strtolower($username), DB_NAGIOSXI) . "'";
        if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {

            // account was found in the Nagios XI database
            if ($arr = $rs->FetchRow()) {

                // account is disabled
                if ($arr["enabled"] != 1) {
                    $login_error = true;
                    $login_errmsg = _("The specified user account has been disabled or does not exist.");
                } // else login checks out okay
                else {

                    $login_error = false;

                    // reset any failed login attempts
                    change_user_attr($arr["user_id"], "login_attempts", 0);
                    change_user_attr($arr["user_id"], "last_attempt", 0);

                    // set session variables
                    $_SESSION["user_id"] = $arr["user_id"];
                    $_SESSION["username"] = strtolower($arr["username"]); // <-- Username must be lowercase or Nagios Core permissions will get screwed up

                    $sid = user_generate_session();
                    $_SESSION["session_id"] = $sid;

                    // see if the user needs to agree to the license
                    if (user_has_agreed_to_license() == false) {
                        $_SESSION["agreelicense"] = 1;
                    }

                    // load user session variables (e.g. preferences)
                    get_user_meta_session_vars(true);

                    // save last login time
                    set_user_meta(0, "lastlogintime", time());

                    // increment times logged in
                    $times = get_user_meta(0, "timesloggedin", 0);
                    set_user_meta(0, "timesloggedin", $times + 1);

                    // add a home dashboard if they don't have one
                    $homedash = get_dashboard_by_id(0, HOMEPAGE_DASHBOARD_ID);
                    if ($homedash == null) {
                        // add the dashboard
                        add_dashboard(0, HOMEPAGE_DASHBOARD_TITLE, null, HOMEPAGE_DASHBOARD_ID, true);

                        // add some dashlets...
                    }

                    // add a screen dashboard if they don't have one
                    //delete_dashboard_id(0,SCREEN_DASHBOARD_ID);
                    $screendash = get_dashboard_by_id(0, SCREEN_DASHBOARD_ID);
                    if ($screendash == null) {
                        // add the dashboard
                        add_dashboard(0, SCREEN_DASHBOARD_TITLE, null, SCREEN_DASHBOARD_ID, true);
                    }

                    // sync username/password with Nagios Core (basic authentication)
                    $args = array(
                        "username" => strtolower($username),
                        "password" => $password
                    );
                    submit_command(COMMAND_NAGIOSXI_SET_HTACCESS, serialize($args), 0, 0, null, true);

                    // they haven't seen the login alerts screen yet
                    $_SESSION["has_seen_login_alerts"] = false;
                }
            } // row could not be found - user doesn't exist in Nagios XI
            else {
                $login_error = true;
                $login_errmsg = sprintf(_("The specified user account has not been setup in this application - contact your %s administrator."), get_product_name());
            }
        } // error encountered running the SQL query
        else {
            $login_error = true;
            $login_errmsg = sprintf(_("An error was encountered when looking up the user in the %s database."), get_product_name());
        }
    }

    // an error occurred - redirect to login page
    if ($login_error == true) {
        $login_msg = array();
        $login_msg[] = $login_errmsg;
        $error_msgs = $login_msg;
        // log that this is a retry
        failed_login_attempt($username);
        $auditlog_message = sprintf(_('Login failure - Username: %1$s - %2$s'), $username, implode("\n", $error_msgs));
        send_to_audit_log($auditlog_message, AUDITLOGTYPE_SECURITY);
        show_login(true, $error_msgs);
    } // login was okay, but user wants debug information
    else if ($debug == 1) {
        echo "AUTH INFO:<BR>";
        print_r($info_msg);
        exit();
    } //  login was okay
    else {
        if (!empty($locale)) {
            set_language($locale);
        }

        // Set last login time
        change_user_attr(0, 'last_login', time());

        // Log the logins
        send_to_audit_log(_("Logged in"), AUDITLOGTYPE_SECURITY);

        // check for updates
        do_update_check();

        // check if upgrade is needed
        if (upgrade_needed() == true && is_admin() == true) {
            header("Location: " . get_base_url() . PAGEFILE_UPGRADE);
            exit();
        }

        // Redirect user
        $redirecturl = grab_request_var("redirect", "");
        if (have_value($redirecturl)) {
            // Override redirect url if it's to an outside location
            if (strpos($redirecturl, '://') !== false || strpos($redirecturl, '.php') === false) {
                // Verify if we are sending locally
                $exurl = get_option('external_url');
                if (strpos($redirecturl, $exurl) === false) {
                    $redirecturl = 'index.php';
                }
            }
            $url = $redirecturl;
        } else {
            $url = "home.php";
        }

        header("Location: " . $url);
    }

}

function do_logout()
{
    // Log it (if there was a user that was logged in)
    $user = get_user_attr(0, "username");
    if (!empty($user)) {
        send_to_audit_log(_("Logged out"), AUDITLOGTYPE_SECURITY);
    }

    // Destroy the current session
    deinit_session();

    header('Location: login.php');
}