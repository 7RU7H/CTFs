<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// USER AUTHENTICATION
////////////////////////////////////////////////////////////////////////////////


/**
 * Function checks the login credentials for the user
 *
 * @param   string  $username   Username
 * @param   string  $password   Password to verify
 * @param   string  $info_msg   Info messages
 * @return  bool                True if login was successful
 */
function check_login_credentials($username, $password, &$info_msg)
{
    global $db_tables;
    global $callbacks;

    // clear messages
    $info_msg = array();
    $debug_msg = array();

    // Try other authentication methods...
    $auth_ok = false;
    $skip_local = false;

    $have_two_factor_callbacks = false;
    $two_factor_auth_ok = false;
    $two_factor_skip_count = 0;

    $cbargs = array(
        "credentials" => array(
            "username" => $username,
            "password" => $password,
        ),
        "login_ok" => 0,
        "info_messages" => array(),
        "debug_messages" => array(),
    );

    if (array_key_exists(CALLBACK_PROCESS_AUTH_INFO, $callbacks)) {
        foreach ($callbacks[CALLBACK_PROCESS_AUTH_INFO] as $cb) {

            // Try this authentication method
            $cb(CALLBACK_PROCESS_AUTH_INFO, $cbargs);

            // Login was okay, so stop processing...
            if ($cbargs["login_ok"] == 1) {
                $auth_ok = true;
                send_to_audit_log(_("Authenticated successfully against external credentials"), AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_USER_INTERFACE, $username);
                break;
            }

            if (!empty($cbargs['skip_local'])) {
                $skip_local = true;
            }
        }
    }

    $info_msg = array_merge($info_msg, $cbargs["info_messages"]);

    $uid = get_user_id($username);

    // Hardcode these for skip_local
    if ($uid == 1 || $username == "nagiosadmin") {
        $skip_local = false;
    }

    // Bail out here if configured to do so
    if ($auth_ok == false && $skip_local == true) {
        return false;
    }

    // Check if user password matches (only if it hasn't been authenticated yet)
    if ($auth_ok == false && user_password_matches($username, $password)) {
        send_to_audit_log(_("Authenticated successfully against Nagios XI credentials"), AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_USER_INTERFACE, $username);
        $auth_ok = true;
    }

    // Only do 2FA if auth_ok at this point
    if ($auth_ok == true) {

        $cbargs = array(
            "credentials" => array(
                "username" => $username,
                "password" => $password,
            ),
            "2fa_ok" => 0,
            "2fa_disabled" => 0,
            "info_messages" => array(),
            "debug_messages" => array(),
        );

        if (array_key_exists(CALLBACK_TWO_FACTOR_AUTH, $callbacks)) {
            foreach ($callbacks[CALLBACK_TWO_FACTOR_AUTH] as $i => $cb) {

                $have_two_factor_callbacks = true;

                $cb(CALLBACK_TWO_FACTOR_AUTH, $cbargs);

                // If 2fa is disabled, remove the callback
                if ($cbargs["2fa_disabled"] == 1) {
                    unset($callbacks[CALLBACK_TWO_FACTOR_AUTH][$i]);
                    continue;
                }

                // If successful 2fa then break out of 2fa checks
                if ($cbargs["2fa_ok"] == 1) {
                    $two_factor_auth_ok = true;
                    send_to_audit_log(_("Authenticated successfully against external 2FA source"), AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_USER_INTERFACE, $username);
                    break;
                }

                // Give 2FA components ability to skip per user
                if (!empty($cbargs["2fa_skip"])) {
                    $two_factor_skip_count++;
                    continue;
                }
            }

            $two_factor_callback_count = count($callbacks[CALLBACK_TWO_FACTOR_AUTH]);

            // If there are no callbacks (due to disabled ones) then return true
            if (empty($two_factor_callback_count)) {
                return true;
            }

            // If every 2FA component skipped this user, let it pass
            if ($two_factor_skip_count >= $two_factor_callback_count) {
                $two_factor_auth_ok = true;
                send_to_audit_log(_("User configured to skip every external 2FA source, no 2FA done"), AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_USER_INTERFACE, $username);
            }
        }

        $info_msg = array_merge($info_msg, $cbargs["info_messages"]);

        // If we did no 2FA cbs or if it passed (or was skipped)
        if ($have_two_factor_callbacks == false || $two_factor_auth_ok == true) {
            return true;
        }

        $info_msg[0] = _('Two factor authentication failed');
    }

    // Otherwise set it to some generic info message
    else {
        $info_msg[] = _('Invalid username or password');
    }

    return false;
}


////////////////////////////////////////////////////////////////////////////////
// USER DATA
////////////////////////////////////////////////////////////////////////////////


/**
 * Get a list (array) of the users in the database
 *
 * @param   array   $opts   Array of options for limiting the query
 * @return  array           Array of users or false if failure
 */
function get_users($opts = array())
{
    global $sqlquery;
    global $db_tables;
    
    $fieldmap = array(
        "user_id" => $db_tables[DB_NAGIOSXI]["users"] . ".user_id",
        "username" => $db_tables[DB_NAGIOSXI]["users"] . ".username",
        "name" => $db_tables[DB_NAGIOSXI]["users"] . ".name",
        "email" => $db_tables[DB_NAGIOSXI]["users"] . ".email",
        "enabled" => $db_tables[DB_NAGIOSXI]["users"] . ".enabled",
    );
    $args = array(
        "sql" => $sqlquery['GetUsers'],
        "fieldmap" => $fieldmap,
        "useropts" => $opts
    );
    $sql = generate_sql_query(DB_NAGIOSXI, $args);

    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if (defined("BACKEND")) {
            handle_backend_db_error(DB_NAGIOSXI);
        } else {
            return false;
        }
    } else {
        $users = $rs->getArray();
    }

    return $users;
}


/**
 * Get a list of users in XML format
 * (WARNING! DEPRECATED - LEFT IN FOR BACKWARDS COMPATIBILITY)
 *
 * @param   array   $opts   Array of options for limiting the query
 * @return  string          An XML string of current users
 */
function get_xml_users($opts = array())
{
    global $request;

    $users = get_users($opts);

    $output = "<userlist>\n";
    $output .= "  <recordcount>" . count($users) . "</recordcount>\n";
    if (!isset($request["totals"])) {
        foreach ($users as $user) {
            $output .= "  <user id='" . get_xml_db_field_val($user, 'user_id') . "'>\n";
            $output .= get_xml_db_field(2, $user, 'user_id', 'id');
            $output .= get_xml_db_field(2, $user, 'username');
            $output .= get_xml_db_field(2, $user, 'name');
            $output .= get_xml_db_field(2, $user, 'email');
            $output .= get_xml_db_field(2, $user, 'enabled');
            $output .= "  </user>\n";
        }
    }
    $output .= "</userlist>\n";

    return $output;
}


////////////////////////////////////////////////////////////////////////
// USER ACCOUNT FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Create a new user account in the database. This is an XI account, and is not
 * really linked to Core besides having a matching username for a CCM account and
 * a Core contact matching username, which XI matches against to send notifications.
 *
 * @param   string  $username           Account username
 * @param   string  $password           Account password
 * @param   string  $name               Account user's full/display name
 * @param   string  $email              Account email address
 * @param   int     $level              User level (1: regular user, 255: admin)
 * @param   bool    $forcepasschange    Force password change on first login
 * @param   bool    $addcontact         True to add as a monitoring contact in Core
 * @param   bool    $api_enabled        True to enable API access
 * @param   array   $errmsg             Error message array
 * @param   bool    $checkpwreqs        Check password against password requirements
 * @return  int|null                    User ID or null if no user was created
 */
function add_user_account($username, $password, $name, $email, $level, $forcepasschange, $addcontact, $api_enabled, &$errmsg, $checkpwreqs=true)
{
    global $db_tables;

    $error = false;
    $errors = 0;

    $user_id = -1;

    // make sure we have required variables
    if (!have_value($username)) {
        $error = true;
        $errmsg[$errors++] = _("Username is blank.");
    }
    if (!have_value($email)) {
        $error = true;
        $errmsg[$errors++] = _("Email address is blank.");
    } else if (!valid_email($email)) {
        $error = true;
        $errmsg[$errors++] = _("Email address is invalid.");
    }
    if (!have_value($name)) {
        $error = true;
        $errmsg[$errors++] = _("Name is blank.");
    }
    if (!have_value($password)) {
        $error = true;
        $errmsg[$errors++] = _("Password is blank.");
    }
    if (!have_value($level)) {
        $error = true;
        $errmsg[$errors++] = _("Security level is blank.");
    }

    // does user account already exist?
    if (is_valid_user($username) == true) {
        $error = true;
        $errmsg[$errors++] = _("An account with that username already exists.");
    }

    // does the password meet the complexity requirements?
    if ($checkpwreqs && !password_meets_complexity_requirements($password)) {
        $error = true;
        $errmsg[$errors++] = _("Specified password does not meet the complexity requirements.") . get_password_requirements_message();
    }
    
    // generate random backend ticket string
    $backend_ticket = random_string(64);

    // add account
    if ($error == false) {
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["users"] . " (username,email,name,password,backend_ticket) VALUES ('" . escape_sql_param($username, DB_NAGIOSXI) . "','" . escape_sql_param($email, DB_NAGIOSXI) . "','" . escape_sql_param($name, DB_NAGIOSXI) . "','" . hash_password($password) . "','" . $backend_ticket . "')";
        if (!exec_sql_query(DB_NAGIOSXI, $sql)) {
            $error = true;
            $errmsg[$errors++] = _("Failed to add account") . ": " . get_sql_error(DB_NAGIOSXI);
        } else
            $user_id = get_sql_insert_id(DB_NAGIOSXI, "xi_users_user_id_seq");
    }
    if ($error == false && $user_id < 1) {
        $errmsg[$errors++] = "Unable to get insert id for new user account";
        $error = true;
    }
    if ($error == false) {
        // assign privs
        if (!set_user_meta($user_id, 'userlevel', $level)) {
            $error = true;
            $errmsg[$errors++] = _("Unable to assign account privileges.");
        }
        // force password change at next login
        if ($forcepasschange == true)
            set_user_meta($user_id, 'forcepasswordchange', '1');

        // notification defaults
        set_user_meta($user_id, 'enable_notifications', '1', false);
        set_user_meta($user_id, 'notify_by_email', '1', false);

        set_user_meta($user_id, 'notify_host_down', '1', false);
        set_user_meta($user_id, 'notify_host_unreachable', '1', false);
        set_user_meta($user_id, 'notify_host_recovery', '1', false);
        set_user_meta($user_id, 'notify_host_flapping', '1', false);
        set_user_meta($user_id, 'notify_host_downtime', '1', false);
        set_user_meta($user_id, 'notify_service_warning', '1', false);
        set_user_meta($user_id, 'notify_service_unknown', '1', false);
        set_user_meta($user_id, 'notify_service_critical', '1', false);
        set_user_meta($user_id, 'notify_service_recovery', '1', false);
        set_user_meta($user_id, 'notify_service_flapping', '1', false);
        set_user_meta($user_id, 'notify_service_downtime', '1', false);

        // set password change time
        change_user_attr($user_id, 'last_password_change', time());

        // set api enabled
        change_user_attr($user_id, 'api_enabled', $api_enabled);
        change_user_attr($user_id, 'api_key', random_string(64));

        $notification_times = array();
        for ($day = 0; $day < 7; $day++) {
            $notification_times[$day] = array(
                "start" => "00:00",
                "end" => "24:00",
            );
        }
        $notification_times_raw = serialize($notification_times);
        set_user_meta($user_id, 'notification_times', $notification_times_raw, false);
    }

    // add/update corresponding contact to/in Nagios Core
    if ($error == false && $addcontact == true) {
        $contactargs = array(
            "contact_name" => $username,
            "alias" => $name,
            "email" => $email,
        );
        add_nagioscore_contact($contactargs);
    }

    // do user addition callbacks
    if ($error == false) {
        $cbargs = array(
            'username' => $username,
            'user_id' => $user_id,
            'password' => $password,
            );
        do_callbacks(CALLBACK_USER_CREATED, $cbargs);
    }

    if ($error == false) {
        send_to_audit_log("New user account '" . $username . "' created", AUDITLOGTYPE_SECURITY);
        return $user_id;
    } else {
        return null;
    }
}


/**
 * Check if a user's password matches the one given
 * 
 * @param   string  $username   The username of the user
 * @param   string  $password   The password to be tested
 * @return  bool                True if the user password matches
 */
function user_password_matches($username, $password)
{
    global $db_tables;
    $hash = '';

    if ($username === null && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else if (empty($username)) {
        return false;
    }

    // Get the current user's password out of the DB and check hash
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE lower(username)='" . escape_sql_param(strtolower($username), DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($arr = $rs->FetchRow()) {
            $hash = $arr['password'];
        }
    }

    // Verify hash was found (DB pull worked)
    if (empty($hash)) {
        return false;
    }

    // Check the password
    // (Old passwords will require some MD5 checking and magical adjustment in the DB)
    if (strpos($hash, '$2a$') !== false) {
        $hash_check = hash_password($password, $hash);
        if (hash_equals($hash, $hash_check)) {
            return true;
        }
    } else {
        // User password is md5 hashed, we need to update it in the DB
        if (md5($password) == $hash) {
            $user_id = get_user_id($username);
            change_user_attr($user_id, 'password', hash_password($password));
            return true;
        }
    }

    return false;
}


/**
 * Creates a password hash for a user (max password length, 72 chars)
 *
 * @param   string  $password   Password to be hashed
 * @param   string  $hash       An already hashed password
 * @return  string              Hashed password
 */
function hash_password($password, $hash = null)
{
    // Generate random salt if we need one
    if ($hash === null) {
        $salt = substr(hash('sha256', uniqid()), 0, 22);
        if (function_exists('random_bytes')) {
            $tmp = random_bytes(11);
            $salt = bin2hex($tmp);
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $tmp = openssl_random_pseudo_bytes(11);
            $salt = bin2hex($tmp);
        }
        return crypt($password, '$2a$10$'.$salt.'$');
    }

    // Generate with given hash (used to get the old salt)
    return crypt($password, $hash);
}


/**
 * Get a user attribute from the database based on column name and user ID
 *
 * @param   int         $user_id    The user ID
 * @param   string      $attr       Column name
 * @return  bool|mixed              The value in the database or false if nothing found
 */
function get_user_attr($user_id, $attr)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Make sure we have required variables
    if (!have_value($user_id) || !have_value($attr)) {
        return false;
    }

    // Get attribute from database
    $sql = "SELECT " . escape_sql_param($attr, DB_NAGIOSXI) . " FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, false))) {
        if ($rs->MoveFirst()) {
            return $rs->fields[$attr];
        }
    }

    return false;
}


/**
 * Update a user attribute in the database based on column name and user ID
 *
 * @param   int     $user_id    The user ID
 * @param   string  $attr       Column name
 * @param   mixed   $value      The value to set
 * @return  bool                True if value was set
 */
function change_user_attr($user_id, $attr, $value)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Make sure we have required variables
    if (!have_value($user_id))
        return false;
    if (!have_value($attr))
        return false;

    // Update attribute
    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["users"] . " SET " . escape_sql_param($attr, DB_NAGIOSXI) . "='" . escape_sql_param($value, DB_NAGIOSXI) . "' WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "'";

    if (!exec_sql_query(DB_NAGIOSXI, $sql)) {
        return false;
    }

    return true;
}


/**
 * Callback that happens when a user changes their password
 *
 * @param   int     $user_id    The user ID of the user that changed their password
 * @param   string  $password   The new password
 */
function do_user_password_change_callback($user_id, $password)
{
    // Use logged in user's id if 0
    if ($user_id == 0) {
        $user_id = $_SESSION["user_id"];
    }

    // Values that will be passed to the callback
    $args = array(
        'user_id' => $user_id,
        'username' => get_user_attr($user_id, "username"),
        'password' => $password
    );
    do_callbacks(CALLBACK_USER_PASSWORD_CHANGED, $args);
}


/**
 * Checks if a user account exists
 *
 * @param   string  $username   Username to check for
 * @return  bool                True if account exists
 */
function is_valid_user($username)
{
    $id = get_user_id($username);
    if (!have_value($id)) {
        return false;
    }
    return true;
}


/**
 * Checks if an account exists by user ID
 *
 * @param   int     $userid     The ID of a user
 * @return  bool                True if account exists
 */
function is_valid_user_id($userid)
{
    global $db_tables;

    // Force lowercase comparison
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE user_id='" . escape_sql_param($userid, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0) {
            return true;
        }
    }

    return false;
}


/**
 * Checks if a user password is valid or not by checking if we
 * need to check for and old password
 *
 * @param   int     $user_id    User ID (0 for session user)
 * @param   string  $password   Password
 * @return  bool                True if valid user password
 */
function is_valid_user_password($user_id, $password)
{
    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Check if we should allow old passwords
    $pw_check_old_passwords = get_option('pw_check_old_passwords', 0);
    if ($pw_check_old_passwords) {

        // Check against current password
        if (user_password_matches(null, $password)) {
            return false;
        }

        // Get old password hashes
        $old_pw_hashes = get_user_meta($user_id, 'old_pw_hashes', array());
        if (!empty($old_pw_hashes)) {
            $old_pw_hashes = unserialize(base64_decode($old_pw_hashes));
        }

        // Check passwords against the old passwords
        if (count($old_pw_hashes) > 0) {
            foreach ($old_pw_hashes as $hash) {
                $hash_check = hash_password($password, $hash);
                if (hash_equals($hash, $hash_check)) {
                    return false;
                }
            }
        }

    }

    return true;
}


/**
 * Add a password (hashed) to a user's old passwords list
 *
 * @param   int     $user_id    User ID (0 for session user)
 * @param   string  $hash       Password hash
 */
function user_add_old_password($user_id, $hash)
{
    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Get old password hashes
    $old_pw_hashes = get_user_meta($user_id, 'old_pw_hashes', array());
    if (!empty($old_pw_hashes)) {
        $old_pw_hashes = unserialize(base64_decode($old_pw_hashes));
    }

    // Store the passed password hash in the array
    $old_pw_hashes[] = $hash;
    set_user_meta($user_id, 'old_pw_hashes', base64_encode(serialize($old_pw_hashes)));
}


/**
 * Gets the user ID of an account by username
 *
 * @param   string      $username   The username to get the ID of
 * @return  int|null                Returns user ID or null
 */
function get_user_id($username)
{
    global $db_tables;

    // Force lowercase comparison
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE lower(username)='" . escape_sql_param(strtolower($username), DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0) {
            return $rs->fields["user_id"];
        }
    }
    return null;
}


/**
 * Tries to get a username from an email address, if there are multiple
 * then it will get only the first selection
 *
 * @param   string  $email  Email address
 * @return  mixed           Array of user information or false on failure
 */
function get_user_by_email($email)
{
    global $db_tables;

    // Check for email in user database
    $sql = "SELECT user_id, username, name, email, enabled FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE lower(email)='" . escape_sql_param(strtolower($email), DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0) {
           $tmp = $rs->GetArray();
           return $tmp[0];
        }
    }

    return false;
}


/**
 * Deletes a user account by user ID
 *
 * @param   int     $userid         User ID of account to delete
 * @param   bool    $deletecontact  True to delete the monitoring contact
 * @return  bool                    True if user was deleted
 */
function delete_user_id($userid, $deletecontact = true)
{
    global $db_tables;
    $username = get_user_attr($userid, "username");

    // Log deletion
    send_to_audit_log("User deleted account '" . $username . "'", AUDITLOGTYPE_SECURITY);

    // Delete corresponding contact from Nagios Core
    if ($deletecontact == true) {
        delete_nagioscore_contact($username);
    }

    // Delete the user's scheduled reports, if any
    $reports = scheduledreporting_component_get_reports($userid);
    if (!empty($reports)) {
        foreach ($reports as $rid => $report) {
            scheduledreporting_component_delete_report($rid, $userid);
        }
    }

    // Delete user account
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE user_id='" . escape_sql_param($userid, DB_NAGIOSXI) . "'";
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }

    // Delete all user meta
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($userid, DB_NAGIOSXI) . "'";
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }

    // Delete all user meta
    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["cmp_favorites"] . " WHERE user_id='" . escape_sql_param($userid, DB_NAGIOSXI) . "'";
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }

    return true;
}


////////////////////////////////////////////////////////////////////////
// USER AUTHORIZATION
////////////////////////////////////////////////////////////////////////


/**
 * Checks if a user is an admin by user ID
 *
 * @param   int     $user_id    The user ID to check
 * @return  bool                True if user is an admin or false if not
 */
function is_admin($user_id = 0)
{
    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Get user's level
    if (empty($_SESSION['userlevel']) || $_SESSION["user_id"] != $user_id) {
        $level = get_user_meta($user_id, 'userlevel');
    } else {
        $level = $_SESSION['userlevel'];
    }

    // Return true if admin or false if not
    if (intval($level) == L_GLOBALADMIN) {
        return true;
    }

    return false;
}


/**
 * Returns the available authentication levels that exist
 *
 * @return  array   Authentication levels
 */
function get_authlevels()
{
    $levels = array(
        L_USER => _("User"),
        L_GLOBALADMIN => _("Admin")
    );
    return $levels;
}


/**
 * Checks if the auth level exists in the available auth levels
 *
 * @param   int     $level  The auth level type check
 * @return  bool            True if auth level exists
 */
function is_valid_authlevel($level)
{
    $levels = get_authlevels();
    return array_key_exists($level, $levels);
}


////////////////////////////////////////////////////////////////////////
// MISC USER FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Check if a user has a specific permission
 *
 * @param   string  $perm   Permission
 * @return  bool            True if the user has the permission
 */
function user_has_permission($perm, $user_id = 0)
{
    if ($user_id == 0 && @isset($_SESSION['user_id'])) {
        $user_id = $_SESSION["user_id"];
    }

    // Admins have all permissions
    if (is_admin($user_id)) {
        return true;
    }

    // Check to see if the permission exists
    if (get_user_meta($user_id, $perm, 0)) {
        return true;
    }

    return false;
}


/**
 * Checks if a user has the "Advanced User" permissions
 *
 * @param   int     $userid     The user ID
 * @return  bool                True if user has advanced user permissions
 */
function is_advanced_user($userid = 0)
{
    if ($userid == 0 && @isset($_SESSION['user_id'])) {
        $userid = $_SESSION["user_id"];
    }

    // Admins are experts
    if (is_admin($userid)) {
        return true;
    }

    // Certain users are experts
    $advanceduser = get_user_meta($userid, "advanced_user");
    if ($advanceduser == 1) {
        return true;
    }

    return false;
}


/** 
 * Checks to see if a user has access to the auto deployment component
 *
 * @param   int     $userid     The user ID
 * @return  bool                True if user has Auto Deployment access
 */
function user_can_access_autodeploy($userid = 0)
{
    if ($userid == 0 && @isset($_SESSION['user_id'])) {
        $userid = $_SESSION["user_id"];
    }

    if (is_admin($userid)) {
        return true;
    }

    if (get_user_meta($userid, 'autodeploy_access', 0) > 0) {
        return true;
    }

    return false;
}


/**
 * Checks if a user can access the CCM
 *
 * @param   int     $userid     The user ID
 * @return  bool                True if user has CCM access
 */
function user_can_access_ccm($userid = 0)
{
    if ($userid == 0 && @isset($_SESSION['user_id'])) {
        $userid = $_SESSION["user_id"];
    }

    if (is_admin($userid)) {
        return true;
    }

    if (get_user_meta($userid, 'ccm_access', 0) > 0) {
        return true;
    }

    return false;
}


/**
 * Checks if a user is a read only user
 *
 * @param   int     $userid     The user ID
 * @return  bool                True if the user has read only permissions
 */
function is_readonly_user($userid = 0)
{
    if ($userid == 0 && @isset($_SESSION['user_id'])) {
        $userid = $_SESSION['user_id'];
    }

    // Admins are always read/write
    if (is_admin($userid) == true) {
        return false;
    }

    // Certain users are experts
    $readonlyuser = get_user_meta($userid, "readonly_user");
    if ($readonlyuser == 1) {
        return true;
    } else {
        return false;
    }
}


////////////////////////////////////////////////////////////////////////
// USER META DATA FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Get user meta data from the database by user ID and meta lookup key
 *
 * @param   int     $user_id    The user ID
 * @param   string  $key        The meta lookup key
 * @param   mixed   $default    The default value if not value is found
 * @return  mixed               Value of the key or $default
 */
function get_user_meta($user_id, $key, $default=null)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0) {
        if (!isset($_SESSION["user_id"])) {
            return null;
        } else {
            $user_id = $_SESSION["user_id"];
        }
    }

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->MoveFirst()) {
            return $rs->fields["keyvalue"];
        }
    }

    return $default;
}


/**
 * Get an array of all the available user meta data by user ID
 *
 * @param   int         $user_id    The user ID
 * @return  bool|array              All the user meta data available or false
 */
function get_all_user_meta($user_id)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0) {
        if (!isset($_SESSION["user_id"])) {
            return null;
        } else {
            $user_id = $_SESSION["user_id"];
        }
    }

    $sql = "SELECT keyname, keyvalue FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return $rs->GetArray();
    }

    return false;
}


/**
 * Get the current user's meta data and store it in the user's session
 *
 * @param   bool    $overwrite  Overwrite the current session values
 * @return  bool                True if values were set
 */
function get_user_meta_session_vars($overwrite = false)
{
    global $db_tables;

    if (!@isset($_SESSION["user_id"])) {
        return false;
    }

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($_SESSION["user_id"], DB_NAGIOSXI) . "' AND autoload='1'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, false))) {
        while (!$rs->EOF) {
            switch ($rs->fields["keyname"]) {
                case "user_id"; // This would allow setting a different user ID
                    break;
                default:
                    if (!($overwrite == false && @isset($_SESSION[$rs->fields["keyname"]]))) {
                        $_SESSION[$rs->fields["keyname"]] = $rs->fields["keyvalue"];
                    }
                    break;
            }
            $rs->MoveNext();
        }
    }

    return false;
}


/**
 * Sets the user meta for the user ID given to the value given for a key. If the
 * key does not exist, one is created and the value is set. This will also load
 * the current session with the key => value pair unless $sessionload is false. 
 *
 * @param   int     $user_id        The user ID
 * @param   string  $key            The meta lookup key
 * @param   mixed   $value          The value to set
 * @param   bool    $sessionload    True to set/overwrite the session's value
 * @return  bool                    True if set in the database
 */
function set_user_meta($user_id, $key, $value, $sessionload = true)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0 && @isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }

    // Set the session value too
    $autoload = 0;
    if ($sessionload == true) {
        $autoload = 1;
    }

    // See if data exists already
    $key_exists = false;
    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        if ($rs->RecordCount() > 0)
            $key_exists = true;
    }

    // Insert new key or update an existing key
    if ($key_exists == false) {
        $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["usermeta"] . " (user_id,keyname,keyvalue,autoload) VALUES ('" . escape_sql_param($user_id, DB_NAGIOSXI) . "','" . escape_sql_param($key, DB_NAGIOSXI) . "','" . escape_sql_param($value, DB_NAGIOSXI) . "','" . $autoload . "')";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    } else {
        $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["usermeta"] . " SET keyvalue='" . escape_sql_param($value, DB_NAGIOSXI) . "', autoload='" . $autoload . "' WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
        return exec_sql_query(DB_NAGIOSXI, $sql);
    }
}


function get_array_user_meta($user_id, $key, $default=null)
{
    $tmp = get_user_meta($user_id, $key, $default);
    if (!empty($tmp)) {
        $tmp = json_decode(base64_decode($tmp), true);
    }
    return $tmp;
}


function set_array_user_meta($user_id, $key, $array, $autoload = false)
{
    return set_user_meta($user_id, $key, base64_encode(json_encode($array)), $autoload);
}


function add_array_user_meta($user_id, $key, $item, $autoload = false)
{
    $tmp = get_user_meta($user_id, $key, array());
    if (!empty($tmp)) {
        $tmp = json_decode(base64_decode($tmp), true);
    }

    // Add to array
    $tmp[] = $item;

    return set_user_meta($user_id, $key, $tmp, $autoload);
}


/**
 * Delete a piece of user meta from the database with user ID and lookup key
 *
 * @param   int     $user_id    The user ID
 * @param   string  $key        The meta lookup key
 * @return  bool                True if meta was removed 
 */
function delete_user_meta($user_id, $key)
{
    global $db_tables;

    // Use logged in user's ID
    if ($user_id == 0) {
        $user_id = $_SESSION["user_id"];
    }

    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["usermeta"] . " WHERE user_id='" . escape_sql_param($user_id, DB_NAGIOSXI) . "' AND keyname='" . escape_sql_param($key, DB_NAGIOSXI) . "'";
    return exec_sql_query(DB_NAGIOSXI, $sql);
}


////////////////////////////////////////////////////////////////////////
// USER MASQUERADING
////////////////////////////////////////////////////////////////////////


/**
 * Masquerade as a user by setting session data for that user
 *
 * @param   int     $user_id    The user ID to masquerade as
 */
function masquerade_as_user_id($user_id = -1)
{
    // Only admins can masquerade
    if (!is_admin()) { return; }

    $original_user = $_SESSION["username"];

    if (!is_valid_user_id($user_id)) {
        return;
    }

    $username = get_user_attr($user_id, "username");

    // Destroy current user session
    deinit_session();
    init_session(false, true, true);

    // Set up new user session
    $_SESSION["user_id"] = $user_id;
    $_SESSION["username"] = $username;

    // Load user session variables (e.g. preferences)
    get_user_meta_session_vars(true);

    send_to_audit_log("Masqueraded as user '" . $username . "'", AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_USER_INTERFACE, $original_user);
}


////////////////////////////////////////////////////////////////////////
// DEFAULT VIEWS/DASHBOARDS
////////////////////////////////////////////////////////////////////////


/**
 * Adds default views to a user if the user has none
 *
 * @param   int     $userid     The user ID
 */
function add_default_views($userid = 0)
{
    $views = get_user_meta($userid, "views");
    if (empty($views)) {
        add_view($userid, "/nagiosxi/includes/components/xicore/tac.php", _("Tactical Overview"));
        add_view($userid, "/nagiosxi/includes/components/xicore/status.php?show=services&hoststatustypes=2&servicestatustypes=28&serviceattr=10", _("Open Problems"));
        add_view($userid, "/nagiosxi/includes/components/xicore/status.php?show=hosts", _("Host Detail"));
        add_view($userid, "/nagiosxi/includes/components/xicore/status.php?show=services", _("Service Detail"));
        add_view($userid, "/nagiosxi/includes/components/xicore/status.php?show=hostgroups&hostgroup=all&style=overview", _("Hostgroup Overview"));
    }
}


/**
 * Add default dashboards to a user if the user has no dashboards
 *
 * @param   int     $userid     The user ID
 */
function add_default_dashboards($userid = 0)
{
    $dashboards = get_user_meta($userid, "dashboards");
    if (empty($dashboards)) {
        add_dashboard($userid, "Home Page", array(), HOMEPAGE_DASHBOARD_ID);
        add_dashboard($userid, "Empty Dashboard", array(), null);
    }
    init_home_dashboard_dashlets($userid);
}


/**
 * Add default dashlets to a blank home dashboard
 *
 * @param   int     $userid     The user ID
 */
function init_home_dashboard_dashlets($userid = 0)
{
    $homedash = get_dashboard_by_id($userid, HOMEPAGE_DASHBOARD_ID);
    if ($homedash == null) {
        return;
    }

    $dashcount = count($homedash["dashlets"]);
    if ($dashcount == 0) {
        $getting_started_left = 30;

        if (is_admin()) {

            // Administrative Tasks
            add_dashlet_to_dashboard($userid, HOMEPAGE_DASHBOARD_ID, "xicore_admin_tasks", _("Administrative Tasks"),
                array("height" => 361, "width" => 330, "top" => 30, "left" => 30, "pinned" => 0, "zindex" => "1"), array());

            // Server Stats
            add_dashlet_to_dashboard($userid, HOMEPAGE_DASHBOARD_ID, "xicore_server_stats", _("Server Statistics"),
                array("height" => 495, "width" => 330, "top" => 421, "left" => 30, "pinned" => 0, "zindex" => "1"), array());

            // Push 'Getting Started' to the right of 'Administrative Tasks'
            $getting_started_left = 400;
        }

        // Getting started
        add_dashlet_to_dashboard($userid, HOMEPAGE_DASHBOARD_ID, "xicore_getting_started", _("Getting Started"), 
            array("height" => 379, "width" => 330, "top" => 30, "left" => $getting_started_left, "pinned" => 0, "zindex" => "1"), array());

    }
}


/**
 * Determines the user's highcharts_default_type preference
 *
 * @param   int     $userid     The user ID
 * @return  string              Will return one of the following: line, spline, area, stacked
 */
function get_highcharts_default_type($userid = 0) 
{
    $allowed_values = array("stacked", "area", "line", "spline");
    $user_highcharts_default_type = get_user_meta($userid, "highcharts_default_type");

    // First check if the user has a type set, which overrides the sys default
    if (in_array($user_highcharts_default_type, $allowed_values)) {
        return $user_highcharts_default_type;
    }

    // Now check if we have a system default set to return
    $highcharts_default_type = get_option('highcharts_default_type', 'line');
    if (in_array($highcharts_default_type, $allowed_values)) {
        return $highcharts_default_type;
    }

    return 'line';
}


////////////////////////////////////////////////////////////////////////
// PASSWORD REQUIREMENTS
////////////////////////////////////////////////////////////////////////


/**
 * 
 *
 * @param   bool    $default    If true, returns the default pw_requirements_array (for testing purposes)
 * @return  array               Password requirement info
 */
function get_pw_requirements_array($default = false)
{
    $defaults = array(
        'max_age'               => 90,
        'min_length'            => 8,
        'enforce_complexity'    => 0,
        'complex_upper'         => 2,
        'complex_lower'         => 2,
        'complex_numeric'       => 2,
        'complex_special'       => 2,
    );

    if ($default) {
        return $defaults;
    }

    $pw_requirements = get_option('pw_requirements');
    $pw = is_null($pw_requirements) ? $defaults : unserialize($pw_requirements);
    if ($pw === false) {
        $pw = $defaults;
    }

    return $pw;
}


/**
 * Checks if the password is within an allowed age range, false if not
 *
 * @param   mixed   $user   If numeric, then used as user's id, if non-numeric, used as username
 * @return  bool            True if the password for specified user is within the allowed range
 */
function allowed_password_age($user)
{
    $userid = is_numeric($user) ? $user : get_user_id($user);

    $pw_enforce_requirements = get_option('pw_enforce_requirements', 0);
    if (!$pw_enforce_requirements) {
        return true;
    }

    $pw = get_pw_requirements_array();
    if (intval($pw['max_age']) == 0) {
        return true;
    }

    $password_set_timestamp = get_user_attr($userid, 'last_password_change', 0);
    if ($password_set_timestamp == 0) {
        return true;
    }

    if ((time() - $password_set_timestamp) >= (intval($pw['max_age']) * 86400)) {
        return false;
    }

    return true;
}


/**
 * Checks if specified string meets password requirements [based on enforcement policy], false if otherwise
 *
 * @param   string  $password   The string to test against the pw_complexity_requirements
 * @param   bool    $default    If true, returns the default pw_requirements_array (for testing purposes)
 * @return  bool                True if the password supplied meets the complexity requirements
 */
function password_meets_complexity_requirements($password, $default = false)
{
    $pw_enforce_requirements = get_option('pw_enforce_requirements', 0);
    if (!$pw_enforce_requirements) {
        return true;
    }

    $default = $default == true ? true : false;
    $pw = get_pw_requirements_array($default);

    // Check length
    if (strlen($password) < $pw['min_length']) {
        return false;
    }

    // See if we need to enforce any other complexity
    if (!$pw['enforce_complexity']) {
        return true;
    }

    $password_array = str_split($password);

    // Check uppercase letter count in current locale
    if ($pw['complex_upper'] > 0) {
        $upper_count = 0;
        foreach($password_array as $char) {
            if (ctype_upper($char)) {
                $upper_count++;
            }
        }
        if ($upper_count < $pw['complex_upper']) {
            return false;
        }
    }

    // Check lowercase letter count in current locale
    if ($pw['complex_lower'] > 0) {
        $lower_count = 0;
        foreach($password_array as $char) {
            if (ctype_lower($char)) {
                $lower_count++;
            }
        }
        if ($lower_count < $pw['complex_lower']) {
            return false;
        }
    }

    // Check numeric count in current locale
    if ($pw['complex_numeric'] > 0) {
        $numeric_count = 0;
        foreach($password_array as $char) {
            if (ctype_digit($char)) {
                $numeric_count++;
            }
        }
        if ($numeric_count < $pw['complex_numeric']) {
            return false;
        }
    }

    // Check special character count in current locale
    if ($pw['complex_special'] > 0) {
        $special_count = 0;
        foreach($password_array as $char) {
            if (!ctype_alnum($char)) {
                $special_count++;
            }
        }
        if ($special_count < $pw['complex_special']) {
            return false;
        }
    }

    return true;
}


/**
 * Get a detailed, translated password requirement message
 *
 * @return  string      A translated requirement message
 */
function get_password_requirements_message()
{
    $message = "";

    $pw_enforce_requirements = get_option('pw_enforce_requirements', 0);
    if (!$pw_enforce_requirements) {
        return $message;
    }

    $pw = get_pw_requirements_array();

    $message = "<br /><br />" . _("The password complexity requirements are as follows:") . "<br /><ul style='list-style-type: disc; margin: 0; padding: 0 0 0 30px;'>\n";
    $message_length = strlen($message);

    if ($pw['min_length'] > 0) {
        $message .= "<li>" . sprintf(_("Minimum password length is: %d characters."), $pw['min_length']) . "</li>\n";
    }

    // See if we need to enforce any other complexity
    if ($pw['enforce_complexity']) {
        if ($pw['complex_upper'] > 0) { $message .= "<li>" . sprintf(_("Minimum of %d uppercase characters."), $pw['complex_upper']) . "</li>\n"; }
        if ($pw['complex_lower'] > 0) { $message .= "<li>" . sprintf(_("Minimum of %d lowercase characters."), $pw['complex_lower']) . "</li>\n"; }
        if ($pw['complex_numeric'] > 0) { $message .= "<li>" . sprintf(_("Minimum of %d numeric characters."), $pw['complex_numeric']) . "</li>\n"; }
        if ($pw['complex_special'] > 0) { $message .= "<li>" . sprintf(_("Minimum of %d special characters."), $pw['complex_special']) . "</li>\n"; }
    }

    if (strlen($message) == $message_length) {
        return "";
    } else {
        $message .= "</ul>\n";
    }

    return $message;
}


////////////////////////////////////////////////////////////////////////
// LOCKED ACCOUNT FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Returns array of ids if there are any accounts that need unlocked or
 * false if there aren't any that need unlocked
 *
 * @return  bool|array  Array containing the ids of locked accounts if total locked accounts > 0, false otherwise
 */
function locked_account_list()
{
    global $db_tables;

    $now = time();
    $account_login_attempts_before_lockout = get_option('account_login_attempts_before_lockout', 3);
    $account_lockout_period = get_option('account_lockout_period', 300);
    $user_ids = array();

    $sql = "SELECT user_id FROM " . $db_tables[DB_NAGIOSXI]["users"] . " WHERE (login_attempts >= {$account_login_attempts_before_lockout}) ";
    if ($account_lockout_period > 0) {
        $sql .= " AND (({$now} - last_attempt <= {$account_lockout_period}) AND (last_attempt > 0))";
    }

    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        foreach ($rs as $user) {
            $user_ids[] = $user['user_id'];
        }
    }

    if (count($user_ids) > 0) {
        return $user_ids;
    }

    return false;
}


/**
 * Checks if the specified username's account is locked or not
 *
 * @param   mixed   $user   If numeric, then used as user's id, if non-numeric, used as username
 * @return  bool            True if the user's account is currently in a locked state
 */
function locked_account($user)
{
    $userid = is_numeric($user) ? $user : get_user_id($user);

    // Check if we even care about locked accounts
    $account_lockout = get_option('account_lockout', 0);
    if ($account_lockout != 1) {
        return false;
    }

    $account_login_attempts_before_lockout = get_option('account_login_attempts_before_lockout', 3);
    $account_lockout_period = get_option('account_lockout_period', 300);

    $login_attempts = get_user_attr($userid, "login_attempts", 0);
    $last_attempt = get_user_attr($userid, "last_attempt", 0);

    if (($login_attempts >= $account_login_attempts_before_lockout) && ($login_attempts != 0)) {

        // If we have indefinite lockout period then at this point we are in fact locked out
        if ($account_lockout_period <= 0) {
            return true;
        } else {

            // If we don't have a last_attempt at this point, something is funky
            if ($last_attempt == 0) {
                return true;
            }

            // We are locked out!
            if (time() - $last_attempt <= $account_lockout_period) {
                return true;
            }

            // We aren't locked out, but we need to clean some stuff up
            if (time() - $last_attempt > $account_lockout_period) {
                change_user_attr($userid, "login_attempts", 0);
                return false;
            }

        }
    }

    return false;
}


/**
 * Increments the number of failed login attempts for username's account
 *
 * @param   mixed   $user   If numeric, then used as user's id, if non-numeric, used as username
 */
function failed_login_attempt($user)
{
    $userid = is_numeric($user) ? $user : get_user_id($user);

    $login_attempts = get_user_attr($userid, "login_attempts", 0);
    $login_attempts++;
    change_user_attr($userid, "login_attempts", $login_attempts);

    // Check if the account is already considered "locked" - and if so bail out before we update the last_attempt
    if (locked_account($userid)) {
        return;
    }

    change_user_attr($userid, "last_attempt", time());
}


////////////////////////////////////////////////////////////////////////////////
// PHONE VERIFICATION
////////////////////////////////////////////////////////////////////////////////


/**
 * Get the current vtag which shows what the current verification status
 * is of the phone number that the user entered.
 *
 * @param   int     $user_id    User ID, if 0 use session user_id (optional)
 * @return  string              HTML output for current phone verified stage
 */
function get_user_phone_vtag($user_id=0)
{
    $fa = 'fa-circle-thin';
    $text = _("No phone number");

    $phone = get_user_meta($user_id, 'mobile_number', '');
    $vkey = get_user_meta($user_id, 'phone_key', '');
    $expires = get_user_meta($user_id, 'phone_key_expires', '');

    // Check validation stage
    if (is_user_phone_verified($user_id)) {
        $fa = 'fa-check-circle';
        $text = _("Verified number");
    } else if (!empty($vkey)) {
        if (!empty($expires) && $expires < time()) {
            $fa = 'fa-times-circle';
            $text = _("Verification failed - key no longer valid");
        } else {
            $fa = 'fa-circle';
            $text = _("Verification in progress - key sent to number");
        }
    } else if (!empty($phone)) {
        $fa = 'fa-circle-o';
        $text = _("Number not verified");
    }

    $output = '<i title="' . $text . '" class="fa tt-bind fa-fw fa-14 ' . $fa . '"></i>';
    return $output;
}


/**
 * Alias for get_user_meta() 'phone_verified' value that will
 * return a actual boolean of true or false
 *
 * @param   int     $user_id    User ID, if 0 use session user_id (optional)
 * @return  bool                True if phone is verified
 */
function is_user_phone_verified($user_id=0)
{
    return (bool) get_user_meta($user_id, 'phone_verified', 0);
}


/**
 * Get a human readable time when the vkey is going to expire
 *
 * @param   int     $user_id    User ID, if 0 use session user_id (optional)
 * @return  string              The value of how long until the key expires
 */
function get_user_phone_vkey_expire($user_id=0)
{
    $expires = get_user_meta($user_id, 'phone_key_expires', '');
    $time = $expires - time();
    return get_duration_string($time, null, null, true);
}


/**
 * Verifies the given phone key against the one sent. If the verification
 * is successful, then the function returns true and sets "phone_verified"
 * user meta value.
 *
 * @param   int     $user_id    User ID, if 0 use session user_id (optional)
 * @param   string  $key        The key value to test
 * @return  bool                True if key is correct, false if any other error
 */
function verify_phone_vkey($user_id=0, $key)
{
    if (empty($key)) { return false; }

    // Check if a key exists and if it's not expired
    $valid_key = get_user_meta($user_id, 'phone_key', '');
    $expires = get_user_meta($user_id, 'phone_key_expires', '');
    if (empty($valid_key)) { return false; }
    if (!empty($expires) && $expires < time()) { return false; }

    // Check key validation
    if ($key == $valid_key) {
        set_user_meta($user_id, 'phone_verified', 1);
        set_user_meta($user_id, 'phone_key', '');
        set_user_meta($user_id, 'phone_key_expires', '');

        // Send validation message
        $phone = get_user_meta($user_id, 'mobile_number', '');
        $provider = get_user_meta($user_id, 'mobile_provider');
        $mobile_email = get_mobile_text_email($phone, $provider);
        $debugmsg = "";
        $message = _("Thank you. Your mobile device has been verified with the Nagios XI system.");
        $opts = array(
            "to" => $mobile_email,
            "subject" => _("Nagios XI"),
            "message" => $message
        );
        send_email($opts, $debugmsg, "verify_phone_vkey()", true);

        return true;
    }

    return false;
}


/**
 * Sends a vkey to the user to put into XI to verify
 * the phone number entered
 *
 * @param   int     $user_id    User ID, if 0 use session user_id (optional)
 * @return  bool                True if sent successfully
 */
function send_user_phone_vkey($user_id=0)
{
    $phone = get_user_meta($user_id, 'mobile_number', '');
    if (empty($phone)) { return false; }
    if (is_user_phone_verified($user_id)) { return false; }

    // Generate a new key
    $key = sprintf("%05d", mt_rand(1, 99999));
    set_user_meta($user_id, 'phone_key', $key);
    
    // Actually send key
    $provider = get_user_meta($user_id, 'mobile_provider');
    $mobile_email = get_mobile_text_email($phone, $provider);
    $debugmsg = "";
    $message = _("Your mobile phone verification key is").":\n".$key;
    $opts = array(
        "to" => $mobile_email,
        "subject" => _("Nagios XI"),
        "message" => $message
    );
    send_email($opts, $debugmsg, "send_user_phone_vkey()", true);

    // Key expires in 1 hour minutes
    $expire = time() + (60 * 60);
    set_user_meta($user_id, 'phone_key_expires', $expire);

    return true;
}


////////////////////////////////////////////////////////////////////////////////
// TWO FACTOR AUTHENTICATION
////////////////////////////////////////////////////////////////////////////////


/**
 * Creates and sents a two factor auth token to a user
 *
 * @param   string  $username   The username of the user logging in
 */
function send_two_factor_token($username)
{
    // Get user_id from username
    $user_id = get_user_id($username);
    if (empty($user_id)) { return false; }

    // Grab the details we need
    $email = get_user_attr($user_id, 'email');
    $name = get_user_attr($user_id, 'name');

    // Generate 5 digit random token
    $token = rand(10000, 99999);

    // Set expiration time (convert minutes to seconds)
    $two_factor_timeout = get_option('two_factor_timeout', 15); 
    $expires = time() + ($two_factor_timeout * 60);

    $data = array('token' => $token,
                  'expires' => $expires);

    // Save token in the DB
    $token_data = base64_encode(serialize($data));
    set_user_meta($user_id, 'two_factor_token', $token_data);

    // Create the message body
    $message = _("Hello")." $name,\n\n"._("To continue logging in, please enter the token below").":\n\n$token\n\n"._("Note").": "._("If you close your browser window or too much time has elapsed, you may need to start the process over again.")."\n\n"._("Contact your Nagios XI admin if you are unsure what this message is for.");

    // Send the token
    $data = array(
        'to' => $email,
        'subject' => _('Email Confirmation'),
        'message' => $message
    );
    send_email($data);
}


/**
 * Checks to see if the token passed matches the token for the user
 *
 * @param   string      $username   The user's username
 * @param   string      $token      The two factor auth token
 * @return  bool|null               True if token matches the user's token or null if expired
 */
function verify_two_factor_auth($username, $token)
{
    // Get user_id from username
    $user_id = get_user_id($username);
    if (empty($user_id) || empty($token)) { return false; }

    // Get token
    $token_data = get_user_meta($user_id, 'two_factor_token');
    if (empty($token_data)) { return false; }
    $token_data = unserialize(base64_decode($token_data));

    // Check if token is expired
    if (time() > $token_data['expires']) {
        delete_user_meta($user_id, 'two_factor_token');
        return false;
    }

    // Check for token validity
    if ($token == $token_data['token']) {
        return  true;
    }

    return false;
}


/**
 * Checks a TFV cookie token against the tokens that are stored in
 * the user's meta data. Also removes any old tokens before doing
 * the TFV check.
 *
 * @param   string      $username   The user's username
 * @return  bool    True if token is valid and not expired
 */
function user_tfv_verify($username)
{
    // Get user_id from username
    $user_id = get_user_id($username);
    if (empty($user_id)) { return false; }

    $tfv = 'nagiosxi_'.$user_id.'_tfv';
    if (!@isset($_COOKIE[$tfv])) { return false; }
    $token = $_COOKIE[$tfv];

    print $token;

    // Check the list of tokens
    $tfv_tokens = user_tfv_get_tokens($user_id);
    print_r($tfv_tokens);
    if (!empty($tfv_tokens)) {
        if (@isset($tfv_tokens[$token])) {

            // Check expiration
            $expires = $tfv_tokens[$token];
            if (time() < $expires) {
                return true;
            } else {
                user_tfv_delete_token($user_id, $token);
            }

        }
    }

    return false;
}


/**
 * Generates a new TFV token and sets the cookie value for the token
 * for the user that is specified by $username.
 *
 * @param   string      $username   The user's username
 */
function user_tfv_generate_token($username)
{
    // Get user_id from username
    $user_id = get_user_id($username);
    if (empty($user_id)) { return false; }

    // Generate a TFV token and save it for the user
    $tfv_token = random_string(16);

    // Set the expires time
    $two_factor_cookie_timeout = get_option('two_factor_cookie_timeout', 90);
    $expires = time() + ($two_factor_cookie_timeout * (60 * 60 * 24));

    // Add token to user meta
    user_tfv_add_token($user_id, $tfv_token, $expires);

    // Check if the cookie should be secure or not
    $secure = false;
    if (!empty($_SERVER['HTTPS'])) {
        $secure = true;
    }

    setcookie('nagiosxi_'.$user_id.'_tfv', $tfv_token, $expires, '/', '', $secure, true);
}


/**
 * Add a TFV token to the user's token list
 *
 * @param   int     $user_id    User ID
 * @param   string  $token      TFV token
 * @param   string  $expires    Timestamp of expiration
 */
function user_tfv_add_token($user_id, $token, $expires)
{
    $tfv_tokens = user_tfv_get_tokens($user_id);

    // Add TFV token to the list
    $tfv_tokens[$token] = $expires;

    $encoded = base64_encode(serialize($tfv_tokens));
    set_user_meta($user_id, 'tfv_tokens', $encoded);
}


/**
 * Delete a TFV token to the user's token list
 *
 * @param   int     $user_id    User ID
 * @param   string  $token      TFV token
 */
function user_tfv_delete_token($user_id, $token)
{
    $tfv_tokens = user_tfv_get_tokens($user_id);

    // Remove token and set the values back into the users's token array
    if (@isset($tfv_tokens[$token])) {
        unset($tfv_tokens[$token]);
        $encoded = base64_encode(serialize($tfv_tokens));
        set_user_meta($user_id, 'tfv_tokens', $encoded);
    }
}


/**
 * Get an array of TFV tokens from a specified user
 *
 * @param   int     $user_id    User ID
 * @return  array               Array of TFV tokens
 */
function user_tfv_get_tokens($user_id=0)
{
    $tfv_raw = get_user_meta($user_id, 'tfv_tokens');
    if (!empty($tfv_raw)) {
        $tfv_tokens = unserialize(base64_decode($tfv_raw));
    } else {
        $tfv_tokens = array();
    }
    return $tfv_tokens;
}


////////////////////////////////////////////////////////////////////////////////
// AUTHENTICATION TOKENS
//////////////////////////////////////////////////////////////////////////////// 


/**
 * Creates a single-use authentication token for login/rapid response
 *
 * @param   int     $user_id        User ID to create token for
 * @param   int     $valid_until    Unix timestamp for when token becomes invalid
 * @param   array   $restrictions   Pages that should be allowed to be viewed, empty by default (so no restriction)
 * @return  string                  Single-use auth token
 */
function user_generate_auth_token($user_id=0, $valid_until=0, $restrictions='')
{
    global $db_tables;

    if (empty($user_id)) {
        $user_id = $_SESSION['user_id'];
    }

    // Generate token and set time limit for auth (5 mins by default)
    $auth_token = sha1(uniqid());
    if (function_exists("random_bytes")) {
        $auth_token = random_bytes(20);
        $auth_token = bin2hex($auth_token);
    } else if (function_exists("openssl_random_pseudo_bytes")) {
        $auth_token = openssl_random_pseudo_bytes(20);
        $auth_token = bin2hex($auth_token);
    }
    if (empty($valid_until)) {
        $valid_until = time() + (5 * 60);
    }

    // Set restrictions if there are any
    if (!empty($restrictions) || is_array($restrictions)) {
        $restrictions = base64_encode(serialize($restrictions));
    }

    $sql = "INSERT INTO ".$db_tables[DB_NAGIOSXI]['auth_tokens']."
            (auth_user_id, auth_session_id, auth_token, auth_valid_until, auth_expires_at, auth_restrictions)
            VALUES ('".intval($user_id)."', 0,
                    ".escape_sql_param($auth_token, DB_NAGIOSXI, true).",
                    ".sql_time_from_timestamp($valid_until, DB_NAGIOSXI).",
                    null,
                    ".escape_sql_param($restrictions, DB_NAGIOSXI, true).")";
    exec_sql_query(DB_NAGIOSXI, $sql);

    return $auth_token;
}


/**
 * Get auth token information (valid time, restristions, etc)
 *
 * @param   string  $auth_token     Auth token
 * @return  array                   Array of information about auth token
 */
function user_get_auth_token_info($auth_token)
{
    global $db_tables;

    $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]['auth_tokens']." WHERE auth_token = ".escape_sql_param($auth_token, DB_NAGIOSXI, true);
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }

    $arr = $rs->GetArray();
    if (!empty($arr)) {
        $arr = $arr[0];

        // Convert valid until to timestamp
        $arr['valid_until_ts'] = strtotime($arr['auth_valid_until']);
        return $arr;
    }

    return false;
}


/**
 * Verifies that an auth token is valid and has not yet expired.
 *
 * @param   string  $auth_token     Auth token
 * @return  bool                    True if valid (non-expired) auth token
 */
function user_is_valid_auth_token($auth_token)
{
    global $db_tables;

    // Get token from the DB to validate
    $token_info = user_get_auth_token_info($auth_token);

    // Check to make sure we are within the valid time range
    if (!empty($token_info) && !$token_info['auth_used'] && time() < $token_info['valid_until_ts']) {
        return true;
    }

    return false;
}


/**
 * Get the user ID from the auth token specified.
 *
 * @param   string  $auth_token     Auth token
 * @return  int                     User ID or false on failure
 */
function user_get_auth_token_user_id($auth_token)
{
    global $db_tables;

    $sql = "SELECT auth_token_id, auth_user_id FROM ".$db_tables[DB_NAGIOSXI]['auth_tokens']." WHERE auth_token = ".escape_sql_param($auth_token, DB_NAGIOSXI, true);
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }

    $arr = $rs->GetArray();
    if (!empty($arr)) {
        return $arr[0]['auth_user_id'];
    }

    return false;
}


/**
 * Sets an auth token's session ID in the database. Also automatically sets
 * the auth_used value to 1.
 *
 * @param   string  $session_id     The session ID
 * @param   string  $auth_token     Auth token
 * @param   bool    $used           True to set auth_used = 1 in DB
 * @return  bool                    True if successful
 */
function user_set_auth_token_session_id($session_id, $auth_token, $used=true)
{
    global $db_tables;
    $set = array();

    // Set session ID
    $set[] = "auth_session_id = '".intval($session_id)."'";

    // If we are adding in auth_used = 1 do that too
    if ($used) {
        $set[] = "auth_used = '1'";
    }

    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]['auth_tokens']." SET ".implode(', ', $set)." WHERE auth_token = ".escape_sql_param($auth_token, DB_NAGIOSXI, true);
    exec_sql_query(DB_NAGIOSXI, $sql);

    return true;
}


////////////////////////////////////////////////////////////////////////////////
// SESSION DATABASE
////////////////////////////////////////////////////////////////////////////////


/**
 * Generates a user session in the database given the current session
 * or session passed in.
 *
 * @param   int     $user_id        User ID
 * @param   string  $session_id     Session ID (php generated)
 * @return  int                     Session ID
 */
function user_generate_session($user_id=0, $session_id=null)
{
    global $db_tables;
    $address = $_SERVER['REMOTE_ADDR'];

    if (empty($user_id)) {
        $user_id = $_SESSION['user_id'];
    }

    if ($session_id === null) {
        $session_id = session_id();
    }

    $sql = "INSERT INTO ".$db_tables[DB_NAGIOSXI]['sessions']."
            (session_phpid, session_created, session_user_id, session_address)
            VALUES (".escape_sql_param($session_id, DB_NAGIOSXI, true).",
                    ".sql_time_from_timestamp(0, DB_NAGIOSXI).",
                    '".intval($user_id)."',
                    ".escape_sql_param($address, DB_NAGIOSXI, true).")";
    exec_sql_query(DB_NAGIOSXI, $sql);

    // Get the session ID
    $id = get_sql_insert_id(DB_NAGIOSXI, 'xi_sessions_id_seq');

    return $id;
}


/**
 * Updates the current user session in the database. This is what actually
 * tracks where the user is inside of XI.
 */
function user_update_session($session_id=null, $force=true)
{
    global $DB;
    global $db_tables;
    $address = $_SERVER['REMOTE_ADDR'];
    $page = str_replace('//', '/', $_SERVER['SCRIPT_NAME']);

    // Pages that don't need to be recorded (because they are called often)
    $skip_pages = array('/nagiosxi/index.php', '/nagiosxi/ajaxhelper.php');
    if (in_array($page, $skip_pages)) {
        return;
    }

    if ($session_id === null) {
        $session_id = session_id();
    }

    // Get the current page data that we will store in the DB for later
    $https = 0;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) { // HTTPS isn't always set on non-HTTPS servers.
        $https = 1;
    }
    $data = array('request_method' => $_SERVER['REQUEST_METHOD'],
                  'request_time' => $_SERVER['REQUEST_TIME'],
                  'request_query_string' => $_SERVER['QUERY_STRING'],
                  'request_https' => $https);
    $data = base64_encode(serialize($data));

    // Update session database
    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]['sessions']."
            SET session_address = '".escape_sql_param($address, DB_NAGIOSXI)."',
                session_page = '".escape_sql_param($page, DB_NAGIOSXI)."',
                session_data = '".escape_sql_param($data, DB_NAGIOSXI)."',
                session_last_active = ".sql_time_from_timestamp(0, DB_NAGIOSXI)."
            WHERE session_phpid = '".escape_sql_param($session_id, DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);

    // If no session was updated, let's add the session and re-run the update
    if ($DB[DB_NAGIOSXI]->affected_rows() == 0 && $force) {
        user_generate_session();
        user_update_session($session_id, false);
    }
}


/**
 * Remove a session from the xi_sessions database based on php session_id
 *
 * @param   string  $session_id     The session ID to remove
 */
function user_remove_session($session_id)
{
    global $db_tables;

    $sql = "DELETE FROM ".$db_tables[DB_NAGIOSXI]['sessions']." WHERE session_phpid = '".escape_sql_param($session_id, DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);
}


////////////////////////////////////////////////////////////////////////////////
// PAGE RESTRICTED AUTHENTICATION
////////////////////////////////////////////////////////////////////////////////
//
// Page restricted authentication is used in rapid response and elsewhere to
// allow users to authenticate into a session that only allows access to certain
// pages that we allow them to access. If the user tries to access a restricted
// page it will force them to log in normally will allow them to log in even
// while in the special, restricted session state.
//
// Pages are denoted based on the script name using $_SERVER['SCRIPT_NAME'] and
// you can specify even more specific restrictions inside a script/page by using
// a check that parses args given in $_SERVER['QUERY_STRING'].
//
////////////////////////////////////////////////////////////////////////////////


/**
 * Sets a session to restrict a user based on the data in an auth token.
 *
 * @param   string  $auth_token     The auth token who's restrict data we should be using
 */
function user_restrict_session_token($auth_token)
{
    global $db_tables;

    // Get token from the DB
    $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]['auth_tokens']." WHERE auth_token = ".escape_sql_param($auth_token, DB_NAGIOSXI, true);
    if (!($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        return false;
    }
    $arr = $rs->GetArray();
    $arr = $arr[0];

    // Check for restrictions / set them if they exist
    if (!empty($arr["auth_restrictions"])) {
        $_SESSION["restrictions"] = unserialize(base64_decode($arr["auth_restrictions"]));
    }
}


/**
 * Restrict a user session by forcing it to allow certain pages and possibly
 * certain queryargs based on query string values.
 *
 * @param   array|string    $pages      
 * @param   array           $queryargs       
 */
function user_restrict_session($pages, $queryargs=array())
{
    $_SESSION["restrictions"] = array();

    if (is_array($pages)) {
        $_SESSION["restrictions"]["pages"] = $pages;
    } else {
        $_SESSION["restrictions"]["pages"] = array($pages);
    }
    
    // Add array to queryargs
    $_SESSION["restrictions"]["queryargs"] = $queryargs;
}


/**
 * Checks if the current session is trying to authenticate on a restricted
 * area in the XI interface. Typically, all areas are restricted except the
 * list of pages given in the array that is stored in the xi_auth_tokens DB.
 *
 * "pages" - whitelist of pages that are allowed
 * "queryargs" - whitelist of values for certain arguments that can be passed
 *
 * @return      bool        True if in a restricted area, otherwise false
 */
function user_is_restricted_area()
{
    global $db_tables;
    $restricted = true;

    // Grab the session restrictions
    if (empty($_SESSION["restrictions"])) {
        return false;
    }
    $restrictions = $_SESSION["restrictions"];

    $pages = $restrictions["pages"];
    $current_page = str_replace('/nagiosxi/', '', $_SERVER["SCRIPT_NAME"]);

    // Check if current page is in the restricted pages
    if (in_array($current_page, $pages)) {
        $restricted = false;
    }

    // Add query args restrictions
    // - QUERY ARGS MUST MATCH QUERY STRING ARGUMENTS FOR SPECIFIED ARG
    if (!empty($restrictions["queryargs"])) {
        $queryargs = $restrictions["queryargs"];
        $query_string = $_SERVER["QUERY_STRING"];

        // Parse query string
        parse_str($query_string, $args);
        foreach ($queryargs as $qa => $qav) {
            if (isset($args[$qa]) && $args[$qa] != $qav) {
                $restricted = true;
            }
        }
    }

    return $restricted;
}
