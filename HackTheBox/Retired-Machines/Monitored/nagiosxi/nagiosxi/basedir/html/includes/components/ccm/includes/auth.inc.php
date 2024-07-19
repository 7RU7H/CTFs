<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: auth.inc.php
//  Desc: Authentication methods for the CCM. If the CCM is not being accessed from the
//        Nagios XI web instance and the Nagios XI system config setting for seperate CCM
//        login and user management has not been enabled then the user will automatically
//        be logged into the CCM as the Nagios XI user.
//


/**
 * Handles and verifies user authorization for all pages and syncs login with the
 * old NagiosQL and Nagios XI if auto login is turned on.
 *
 * @global  array   $_SESSION       Sets all login related session variables
 * @global  object  $myDBClass      nagiosql object
 * @global  object  $myDataClass    nagiosql object
 * @return  bool    $AUTH           Global variable if auth is good or not
 */
function check_auth()
{
    global $ccm;

    // Grab any submitted login variables 
    $username = $ccm->db->escape_string(ccm_grab_request_var('username', '')); 
    $password = $ccm->db->escape_string(ccm_grab_request_var('password', ''));
    $loginID = ccm_grab_request_var('loginid', ''); 
    $login_submitted = ccm_grab_request_var('loginSubmitted', false);

    // First check any existing CCM login
    if (isset($_SESSION['ccm_username']) && isset($_SESSION['ccm_login']) && $_SESSION['ccm_login'] == true) {
        $_SESSION['loginMessage'] = _('Logged in as: ').$_SESSION['ccm_username']." <a href='index.php?cmd=logout'>"._('Logout')."</a>";
        $_SESSION['loginStatus'] = true;
        return true; 
    }

    // If we are using Nagios XI and are already logged into Nagios XI we can log in now
    if (!$login_submitted) {

        // If we are in Nagios XI and viewing the CCM we need to log the user in using
        // the Nagios XI authentication if they are able to actually view the CCM and
        // if the Nagios XI server has the config option set to allow this.
        $ccm_access = get_user_meta(0, 'ccm_access', 0);
        if (($ccm_access > 1 || is_admin()) && !in_demo_mode()) {

            // Let's check out the user's credentials and see if they can use the CCM
            // and if they are we will go ahead and log them into the CCM using their
            // Nagios XI username as the CCM username.
            $username = get_user_attr($_SESSION['user_id'], "username");
            ccm_process_login($username);

            return true;
        }
    }
    
    // If we are using the standalone CCM or the separate login in Nagios XI we will do the
    // login processing if the login form was submitted.
    if ($login_submitted) {
        $str_sql = "SELECT * FROM `tbl_user` WHERE `username`='$username' AND `password`='".md5($password)."' AND `active`='1'";
        $booReturn = $ccm->db->getDataArray($str_sql, $arr_user_data, $int_data_count);
        if ($booReturn == false)  {
            if (!isset($strMessage)) {
                $strMessage = ""; 
            }
            $_SESSION['loginMessage']= _('Error while selecting data from database:')." ".$ccm->db->strDBError;
            $_SESSION['loginStatus'] = false;
        } else if ($int_data_count == 1) {
        
            // Process the login if we know the user is authenticated
            ccm_process_login($username);

            // Add the new last login time for the user in the database
            $strSQLUpdate = "UPDATE `tbl_user` SET `last_login`=NOW() WHERE `username`='$username'";
            $booReturn = $ccm->db->insertData($strSQLUpdate);
            $log = true;

            // Special login case for Nagios XI when applying configuration there are multiple logins into
            // the CCM and instead of displaying a bunch of login successfuls we are going to save that the
            // Nagios XI user logged in to apply configuration
            if ($username == "nagiosxi") {
                $log = false;
            }

            // Add the actual login to the database log
            if ($log) {
                $ccm->data->writeLog(_('Logged in'));
            }

            return true; 
        } else {

            $_SESSION['loginMessage'] = _('Contact your Nagios XI administrator if you have forgotten your login credentials.');
            $_SESSION['loginStatus'] = false;

            $ccm->data->writeLog(_('Login failed')." - Username: ".$username);

            return false; 
        }
    }
    
    // If we went through all the checks and logins and we haven't returned yet then we can
    // go ahead and let the user know they aren't logged in.
    $_SESSION['loginMessage'] = _("Login Required!");
    $_SESSION['loginStatus'] = true;
    return false;
}


/**
 * Creates the actual login session and writes to the log when the user gets logged in
 * via any of the check_auth login methods.
 *
 * @param   string  $username   The username of the logged in user
 */
function ccm_process_login($username)
{
    // Set login session values for the CCM
    $_SESSION['ccm_username'] = $username;
    $_SESSION['ccm_login'] = true;
    $_SESSION['timestamp'] = time();
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    if (isset($_SESSION['language'])) { $_SESSION['ccm_language'] = $_SESSION['language']; }
}