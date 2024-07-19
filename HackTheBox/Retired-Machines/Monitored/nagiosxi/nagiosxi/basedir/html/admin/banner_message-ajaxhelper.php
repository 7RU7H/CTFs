<?php

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../includes/utils-banner_message.inc.php');

global $request;
// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs, and authorization
grab_request_vars();
check_prereqs();
check_authentication(false);

route_request();

function route_request()
{
    $action = grab_request_var('action', "");

    switch ($action) {
        case 'submit_banner_message':
            ajax_submit_banner_message();
            break;
        case 'retrieve_banner_message':
            ajax_retrieve_banner_message();
            break;
        case 'retrieve_users':
            ajax_retrieve_users();
            break;
        case 'acknowledge_banner_message':
            ajax_update_acknowledged_banner_message();
            break;
        case 'update_banner_message_settings':
            ajax_update_banner_message_settings();
            break;
        case 'retrieve_specific_banner_message':
            ajax_specific_banner_message();
            break;
        case 'delete_banner_message':
            ajax_delete_banner_message();
            break;
        case 'update_display_banner_message':
            ajax_update_display_banner_message();
            break;
        case 'deletemany':
            ajax_delete_many_banner_message();
            break;
        case 'enablemany':
            ajax_enable_many_banner_message();
            break;
        case 'disablemany':
            ajax_disable_many_banner_message();
            break;
        case 'update_feature_display':
            ajax_update_feature_display();
            break;
        default:
            break;
    }
}

/**
 * Creates a response array based on the results of multiple sql queries.
 *
 * Recieves values from sql queries and will create a response array based on the results. If a query has failed
 * then it will log the information on which table has had an issue. Checks to see what type of operation is beig
 * done to return the correct message and run the correct logic since the operations are done slightly differnt.
 * The response array will be used later in a flash message.
 * 
 * @param int $result_message             An int value based on a sql query modifying the banner_messages table.
 * @param int $error_amount               An int balue based on a sql query modifying the linked users messages table
 * @param str $function_operation         The type of operation that the user wanted to perform.
 * 
 * @return $response_arr                  Response array to be used in a flash message to give the correct response message.
 */
function generate_multi_table_response_array($result_message, $error_amount, $function_operation) {
    $successful_operation = '';

    if ($function_operation == 'create'){
        $successful_operation = _('created');
    } else if ($function_operation == 'update'){
        $successful_operation = _('updated');
    } else if ($function_operation == 'delete'){
        $successful_operation = _('deleted');
    }

    $success_msg = sprintf(_("Message has been successfully %s."), $successful_operation);
    $msg_fail_only = sprintf(_("Failed to %s message. There was an issue in the banner message table."), $function_operation);
    $linked_fail_only = sprintf(_("Failed to %s message. There was an issue in the linked user table."), $function_operation);
    $both_failed = sprintf(_("Failed to %s message. There was an issue in both the banner message table and linked user table."), $function_operation);
    $error_msg = sprintf(_("Failed to %s message."), $function_operation);


    if ($result_message && $error_amount == 0) {
        $response_arr = array('message' => $success_msg, 'msg_type' => 'success');
    } else if (!$result_message && $error_amount == 0) {
        file_put_contents('/usr/local/nagiosxi/var/components/banner_messages.log', $msg_fail_only, FILE_APPEND);
        $response_arr = array('message' => $error_msg, 'msg_type' => 'error');
    } else if ($result_message && $error_amount > 0) {
        file_put_contents('/usr/local/nagiosxi/var/components/banner_messages.log', $linked_fail_only, FILE_APPEND);
        $response_arr = array('message' => $error_msg, 'msg_type' => 'error');
    } else {
        file_put_contents('/usr/local/nagiosxi/var/components/banner_messages.log', $both_failed, FILE_APPEND);
        $response_arr = array('message' => $error_msg, 'msg_type' => 'error');
    }

    return $response_arr;
}

/**
 * Creates a response array based on the results of multiple sql queries.
 *
 * Recieves two arrays and checks to see if the count of the arrays are equal. If they are
 * then everything was successful, if not a sql query failed at some point. Creates a response array
 * to be used in a flash message.
 * 
 * @param arr $queries_ran_count             An array of how many queries were successful.
 * @param arr $banner_message_id_array       An array of how many queries that have ran.
 * @param str $function_operation            The type of operation that the user wanted to perform.
 * 
 * @return $respose                          Response array to be used in a flash message to give the correct response message.
 */
function generate_multi_action_response($queries_ran_count, $banner_message_id_array, $function_operation) {

    $success_msg = sprintf(_("Messages have been successfully %s."), $function_operation);
    $error_msg = sprintf(_("Messages have failed to be %s."), $function_operation);

    if (count($queries_ran_count) == count($banner_message_id_array) ) {
        $response = array('message' => $success_msg, 'msg_type' => 'success');
    } else {
        $response = array('message' => $error_msg, 'msg_type' => 'error');
    }
    return $response;
}

/**
 * Creates a response array based on the results of a singular sql query.
 *
 * Takes the results of the sql query and the type of operation being preformed and creates a response
 * array to be used in a flash message.
 * 
 * @param int $result               A int value on the response from the sql query.
 * @param str $function_operation   The type of operation that the user wanted to perform.
 * 
 * @return $respose                 Response array to be used in a flash message to give the correct response message.
 */
function generate_single_response($result, $function_operation){
    $successful_operation = '';

    if ($function_operation == 'update'){
        $successful_operation = _('updated');
    } else if ($function_operation == 'acknowledge'){
        $successful_operation = _('acknowledged');
    }

    $success_msg = sprintf(_("Message has been successfully %s."), $function_operation);
    $error_msg = sprintf(_("Failed to %s message."), $function_operation);
    if ($result) {
        $response = array('message' => $success_msg, 'msg_type' => 'success');
    } else {
        $response = array('message' => $error_msg, 'msg_type' => 'error');
    }
    return $response;
}

/**
 * Creates an array of users by id and returns it.
 *
 * Nothing special here. Just pushing the id values of each user to an empty array to create an array
 * of the users by id.
 * 
 * @return $users_list      An array of users by their id's.
 */
function retrieve_users_list() {
    $users_arr = retrieve_users();
    $users_list = array();
    // creating array of user id's to later loop thourgh for the linked table.
    for($i = 0; $i < count($users_arr); $i++) {
        array_push($users_list, $users_arr[$i]['user_id']);
    }
    return $users_list;
}

/**
 * Takes an parameter and checks to see if its null empty
 * 
 * @param bool          Whatever you want to check if its null or empty
 * 
 * @return bool         returns bool based on if the parameter is null or is empty
 */
function isNullOrEmpty($value) {
    return (!isset($value) || strlen($value) == 0);
}

/**
 * Ajax helper function for submitting a banner message.
 *
 * Recieves input values from the banner message form and passes them into functions to create rows in
 * banner message table and the linked user messages table. Recieves feedback from those functions and
 * checks to see if they were successful. Creates a message correlated to the results and returns it to
 * banner_message.php to be displayed in a flash message.
 * 
 */
function ajax_submit_banner_message()
{

    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    global $request;

    $msg = grab_request_var("banner_message","");
    $creator = grab_request_var("banner_message_creator","");
    $specify_user = grab_request_var("individual_banner_message", "");
    $specific_users = grab_request_var("users", "");
    $acknowledgeable_banner_message = grab_request_var("acknowledgeable_banner_message", "");
    $banner_color = grab_request_var("banner_message_banner_color", "");
    $enable_banner_message = grab_request_var("set_banner_message", "");
    $schedule_banner_message = grab_request_var("schedule_banner_message", "");
    $start_date = grab_request_var("start", "");
    $end_date = grab_request_var("end", "");
    $feature_active = grab_request_var("feature_active", "");

    if (isNullOrEmpty($msg)) {
        $response_arr = array('message' => _('Message is empty. Please enter a message.'), 'msg_type' => 'error');
    } else if ($schedule_banner_message && isNullOrEmpty($start_date) && isNullOrEmpty($end_date)) {
        $response_arr = array('message' => _('Can not schedule a message without a specified time frame.'), 'msg_type' => 'error');
    } else {
        $result_message = send_banner_message($msg, $creator, $acknowledgeable_banner_message, $specify_user, $enable_banner_message, $banner_color, $schedule_banner_message, $start_date, $end_date, $feature_active);

        $last_insert_id = get_sql_insert_id(DB_NAGIOSXI);
        $users_list = retrieve_users_list();

        $error = 0;
        for($i = 0; $i < count($users_list) && $error == 0; $i++) {
            $user_id = $users_list[$i];
            $specified = 0;
            if (is_array($specific_users) && in_array($user_id, $specific_users)) {
                $specified = 1;
            }
            $result_users = insert_users_messages_table($last_insert_id, $user_id, $specified);

            if (!$result_users) {
                $error++;
            }
        }

        $translate_create = _('create');
        $response_arr = generate_multi_table_response_array($result_message, $error, $translate_create);
    }
    echo json_encode($response_arr);
}

/**
 * Ajax helper function for retrieving a banner message
 *
 * Retrieves the banner message from the database and echos them as a json encoded string.
 * 
 */
function ajax_retrieve_banner_message()
{
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $banner_message = retrieve_banner_message();
    echo json_encode($banner_message);
}

/**
 * Ajax helper function for retrieving users
 *
 * Retrieves the user data from the database and echos them as a json encoded string.
 * 
 */
function ajax_retrieve_users()
{
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $users = retrieve_users();
    echo json_encode($users);
}

/**
 * Ajax helper function for retrieving specific banner_message
 *
 * Recieves msg id and uses it to retrieve the specific banner_message from the database.
 * Echos the specific banner_message as a json encoded string.
* 
 */
function ajax_specific_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    global $request;
    $msg_id = grab_request_var("msg_id", "");

    $specific_banner_message = retrieve_specific_banner_message($msg_id);
    echo json_encode($specific_banner_message);
}

/**
 * Ajax helper function for updating acknowledged banner_message
 *
 * Recieves the username of the user who acknowledged the banner_message and sends it to
 * the database to update who has acknowledged the message. Returns back a response for if
 * the process was succesful or has failed.
 * 
 */
function ajax_update_acknowledged_banner_message()
{
    $msg_id = grab_request_var("id", "");
    $acknowledged_by = $_SESSION["user_id"];
    $result = update_acknowledged_banner_message($acknowledged_by, $msg_id);

    $response = generate_single_response($result, 'acknowledge');

    echo json_encode($response);
}

/**
 * Ajax helper function for updating banner_message settings
 *
 * Retrieves the values from the banner_message settings form and sends them to the
 * database to update the correlated messages in both banner messages and linked user
 * messages tables. The SQL queries return a value that we check to see whether the
 * process was succesful or failed which run through some logic to see if the queries
 * were succesful. We then create a message correlated to the results and returns it to
 * banner_message.php to be displayed in a flash message.
 * 
 */
function ajax_update_banner_message_settings() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    global $request;
    $msg_id = grab_request_var("id", "");
    $msg = grab_request_var("banner_message", "");
    $creator = grab_request_var("banner_message_creator", "");
    $specify_user = grab_request_var("individual_banner_message", "");
    $specific_users = grab_request_var("users", "");
    $acknowledgeable_banner_message = grab_request_var("acknowledgeable_banner_message", "");
    $banner_color = grab_request_var("banner_message_banner_color", "");
    $enable_banner_message = grab_request_var("set_banner_message", "");
    $schedule_banner_message = grab_request_var("schedule_banner_message", "");
    $start_date = grab_request_var("start", "");
    $end_date = grab_request_var("end", "");

    if (isNullOrEmpty($msg)) {
        $response_arr = array('message' => _('Message is empty. Please enter a message.'), 'msg_type' => 'error');
    } else if ($schedule_banner_message && isNullOrEmpty($start_date) && isNullOrEmpty($end_date)) {
        $response_arr = array('message' => _('Can not schedule a message without a specified time frame.'), 'msg_type' => 'error');
    } else {
        $result_message = update_banner_message_settings($msg_id, $msg, $creator, $specify_user, $acknowledgeable_banner_message, $enable_banner_message, $banner_color, $schedule_banner_message, $start_date, $end_date);
        $users_list = retrieve_users_list();

        $error = 0;
        for($i = 0; $i < count($users_list) && $error == 0; $i++) {
            $user_id = $users_list[$i];
            $specified = 0;
            if (is_array($specific_users) && in_array($user_id, $specific_users)) {
                $specified = 1;
            }
            $result_users = update_users_messages_table($msg_id, $user_id, $specified);

            if (!$result_users) {
                $error++;
            }
        }

        $translate_update = _('update');
        $response_arr = generate_multi_table_response_array($result_message, $error, $translate_update);
    }

    echo json_encode($response_arr);
}

/**
 * Ajax helper function for deleting a message from banner_message
 *
 * Retrieves the value for which message we want to delete. We run two SQL queries to delete
 * the correlated messages. We recieve back values which we check to see whether the deletions
 * where succesful. We then create a message correlated to the results which we return to
 * banner_message.php to be displayed in a flash message.
 * 
 */
function ajax_delete_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    global $request;

    $msg_id = grab_request_var("id","");
    $error = 0;

    $result = delete_banner_message($msg_id);
    $link_result = delete_banner_message_from_link($msg_id);
    if (!$link_result) {
        $error++;
    }

    $translate_delete = _('delete');
    $response_arr = generate_multi_table_response_array($result, $error, $translate_delete);

    echo json_encode($response_arr);
}

/**
 * Ajax helper function for updating display settings
 *
 * Retrieves the values from the ajax performed on click of the enabled/disabled btn on 
 * banner_message table and uses that data to make a sql query to change the setting. Returns
 * value for flash message to show whether it has failed or was successful.
 * 
 */
function ajax_update_display_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    global $request;

    $msg_id = grab_request_var("id","");
    $display_update = grab_request_var("display","");
    $result = update_banner_message($msg_id, $display_update);

    $translate_update = _('update');
    $response = generate_single_response($result, $translate_update);

    echo json_encode($response);
}

/**
 * Ajax helper function for deleting multiple message
 *
 * Function recieves an array of id's from which to loop through and call the delete banner_message
 * function on each of them. Checks to see if the deletions were successful and sends whether they
 * were or weren't back to banner message for a flash message.
 * 
 */
function ajax_delete_many_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $banner_message_id_array = grab_request_var("id","");

    $queries_ran_count = array();
    foreach($banner_message_id_array as $msg_id) {
        $result = delete_banner_message($msg_id);
        $result_link = delete_banner_message_from_link($msg_id);
        if ($result >= 0 && $result_link) {
            array_push($queries_ran_count, "Both queries were successful.");
        }
    }

    $translate_deleted = _('deleted');
    $response = generate_multi_action_response($queries_ran_count, $banner_message_id_array, $translate_deleted);

    echo json_encode($response);
}

/**
 * Ajax helper function for enabling multiple display settings
 *
 * Function recieves an array of id's from which to loop through and call
 * the update banner_message function on each of them with enabled for the dispay setting.
 * Checks to see if the actions were successful and sends data back for the flash msg to 
 * display.
 * 
 */
function ajax_enable_many_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $banner_message_id_array = grab_request_var("id","");

    $queries_ran_count = array();
    foreach($banner_message_id_array as $msg_id) {
        $result = update_banner_message($msg_id, 1);
        if ($result) {
            array_push($queries_ran_count, "Query successfully ran.");
        }
    }

    $translate_enabled = _('enabled');
    $response = generate_multi_action_response($queries_ran_count, $banner_message_id_array, $translate_enabled);

    echo json_encode($response);
}

/**
 * Ajax helper function for disabling multiple display settings
 *
 * Function recieves an array of id's from which to loop through and call
 * the update banner_message function on each of them with disabled for the dispay setting.
 * Checks to see if the actions were successful and sends data back for the flash msg to 
 * display.
 * 
 */
function ajax_disable_many_banner_message() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $banner_message_id_array = grab_request_var("id","");

    $queries_ran_count = array();
    foreach($banner_message_id_array as $msg_id) {
        $result = update_banner_message($msg_id, 0);
        if ($result) {
            array_push($queries_ran_count, "Query successfully ran.");
        }
    }

    $translate_disabled = _('disabled');
    $response = generate_multi_action_response($queries_ran_count, $banner_message_id_array, $translate_disabled);

    echo json_encode($response);
}

/**
 * Ajax helper function for updating the display setting of the message feature
 *
 * Grabs the value of feature display toggle sent in the ajax request. Then sends that 
 * value to the enable_mod_feature function which updates the value in the database. 
 * Returns a value for if it was successful or not to display in a flash msg.
 * 
 */
function ajax_update_feature_display() {
    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }
    $feature_active = grab_request_var("setting_value", "");

    $result = enable_banner_message_feature($feature_active);

    $success_msg = _("Banner messages feature has been successfully updated.");
    $error_msg = _("Failed to change the status of the banner messages feature.");

    if ($result) {
        $response = array('message' => $success_msg, 'msg_type' => 'success');
    } else {
        $response = array('message' => $error_msg, 'msg_type' => 'error');
    }

    echo json_encode($response);
}
