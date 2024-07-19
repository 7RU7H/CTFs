<?php 
require_once(dirname(__FILE__)) . '/../includes/common.inc.php';

/**
 * Sends the Banner Message to the database
 * 
 * Inserts the Banner Message into the database along with when it was created
 * and who created it.
 *
 * @param string $message                   The Banner Message
 * @param int    $created_by                Id of the user that created the message
 * @param int    $acknowledgeable           Boolean for if the message is acknowledgeable or not.
 * @param int    $specify_user              Boolean for if we should display this to only specified users.
 * @param int    $message_active            Boolean for if the message should be displayed.
 * @param string $banner_color              Class to apply to banner for styling.
 * @param int    $schedule_banner_message   Boolean for if the user wants to only display the message during a 
 *                                          certain time period.
 * @param date   $start_date                Date to start displaying the message if scheduled is set.
 * @param date   $end_date                  Date to stop displaying the message if scheduled is set.
 * @param int    $feature_active            Boolean on whether to display any banner messages from this feature.
 * 
 * @return int  $result                     The amount of rows affected by the sql query we run.
 */
function send_banner_message($message, $created_by, $acknowledgeable, $specify_user, $message_active, $banner_color, $schedule_banner_message, $start_date, $end_date, $feature_active){
    global $db_tables;
    global $DB;

    $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " (message, time_created, created_by, acknowledgeable, specify_users,message_active, banner_color, schedule_message, start_date, end_date,feature_active)
            VALUES (
                ". escape_sql_param($message, DB_NAGIOSXI, true ) .",
                NOW(),
                ". escape_sql_param($created_by, DB_NAGIOSXI, true ).",
                ". escape_sql_param($acknowledgeable, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($specify_user, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($message_active, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($banner_color, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($schedule_banner_message, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($start_date, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($end_date, DB_NAGIOSXI, true ) .",
                ". escape_sql_param($feature_active, DB_NAGIOSXI, true ) .")";

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    return $result;
}

/**
 * Retrieves the banner_message table from the database
 *
 * Retrieves all of the created messages of the day from the database.
 * 
 * @return array $json_sql_data the data from the banner_message table.
 */
function retrieve_banner_message(){
    global $db_tables;

    $sql = "SELECT * FROM `" . $db_tables[DB_NAGIOSXI]["banner_messages"] . "` JOIN `" . $db_tables[DB_NAGIOSXI]["link_users_messages"] . "` ON `" . $db_tables[DB_NAGIOSXI]["banner_messages"] . "`.`msg_id` = `" . $db_tables[DB_NAGIOSXI]["link_users_messages"] . "`.`msg_id` ORDER BY `" . $db_tables[DB_NAGIOSXI]["banner_messages"] . "`.`msg_id` DESC";

    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        $sql_data = $rs->GetArray();
    }

    return $sql_data;
}

/**
 * Checks to see if a banner_message exists
 *
 * Checks to see if a banner_message exists in the database and returns a boolean value.
 * Will use this to determine if the banner_message banner should be displayed.
 * 
 * @return Bool $exists     Boolean value of whether a banner_message exists or not.
 */
function check_banner_message_exists() {
    global $db_tables;

    $exists = false;

    $sql = "SELECT COUNT(*)  AS count FROM " . $db_tables[DB_NAGIOSXI]["banner_messages"];
    $rs = exec_sql_query(DB_NAGIOSXI, $sql);
    if ($rs) {
        $data = $rs->GetArray();
        if (count($data) && array_key_exists('count', $data[0])) {
            $msg_count = intval($data[0]['count']);
            $exists = ($msg_count >= 1); // This would also be fine as another nested if statement.
        }
    }
    return $exists;
}


/**
 * Updates banner_message table
 *
 * Updates the banner_message table for the specific Banner Message that it was acknowledged
 * by the specific user.
 * 
 * @param string $acknowledged_by The user who acknowledged the Banner Message
 * 
 */
function update_acknowledged_banner_message($acknowledged_by, $msg_id) {
    global $db_tables;
    global $DB;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["link_users_messages"] . " SET acknowledged = 1 WHERE msg_id = ". $msg_id . " and user_id = " . $acknowledged_by;

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    return $result;
}

/**
 * Updates latest banner_message
 *
 * Updates the latest banner_message in the database with the new settings. Checks to see if the banner color
 * is a valid hex color. If it is it will add a hash to the front and add it to the sql query. If the
 * hex value is invalid, it will create the sql query without the banner color.
 * 
 * @param int    $id                                The id of the message to be updated
 * @param string $message                           The message for the updated input
 * @param int    $creator                           The user id of the person who updated the message.
 * @param int    $specify_user                      Whether or not to specify users to see the banner_message
 * @param int    $acknowledgeable_banner_message    Whether or not the banner_message is acknowledgeable
 * @param int    $enable_banner_message             Whether or not to enable the banner_message
 * @param string $banner_color                      The color of the banner_message banner
 * @param int    $schedule_message                  Boolean for if they want to schedule the message to display at a specific time period.
 * @param date   $start_date                        Date to start showing the message if schedule message is selected
 * @param date   $end_date                          Date to stop showing the message if shcedule message is selected
 * 
 * @return int   $result                            A int value of the rows affected by the sql query we ran.
 */
function update_banner_message_settings($id, $message, $creator, $specify_user, $acknowledgeable_banner_message, $enable_banner_message, $banner_color, $schedule_message, $start_date, $end_date) {
    global $db_tables;
    global $DB;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " SET
    message = '" . escape_sql_param($message, DB_NAGIOSXI) . "',
    time_created = NOW(),
    created_by = '" . escape_sql_param($creator, DB_NAGIOSXI) . "',
    specify_users = '" . escape_sql_param($specify_user, DB_NAGIOSXI) . "',
    message_active = '" . escape_sql_param($enable_banner_message, DB_NAGIOSXI) . "',
    schedule_message = '" . escape_sql_param($schedule_message, DB_NAGIOSXI) . "',
    start_date = '" . escape_sql_param($start_date, DB_NAGIOSXI) . "',
    end_date = '" . escape_sql_param($end_date, DB_NAGIOSXI) . "',
    banner_color = '" . escape_sql_param($banner_color, DB_NAGIOSXI) . "',
    acknowledgeable = '" . escape_sql_param($acknowledgeable_banner_message, DB_NAGIOSXI) . "'
    WHERE msg_id = " . $id;

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    return $result;
}

/**
 * Retrieves user data
 *
 * Retrieves the user data from the database and returns it in a json format.
 * 
 * @return array $json_sql_data The user data in a json format.
 */
function retrieve_users(){
    global $request;
    global $db_tables;

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["users"] . " ORDER BY username ASC";

    $rs = exec_sql_query(DB_NAGIOSXI, $sql);
    $sql_data = $rs->GetArray();

    return $sql_data;
}

/**
 * Retrieves count of banner_message data
 *
 * Retrieves the count of the banner_message data from the database under the specific search
 * data and returns it.
 * 
 * @param string  $search    The search data to search the banner_message data for
 * 
 * @return int    $count     The count of the banner_message data
 */
function get_count_banner_message_data($search)
{
    global $db_tables;

    $search = escape_sql_param($search, DB_NAGIOSXI);

    $sql = "SELECT COUNT(*) as count FROM " . $db_tables[DB_NAGIOSXI]["banner_messages"];
    $sql .= " WHERE msg_id LIKE '%" . $search . "%' or message LIKE '%" . $search . "%' or time_created LIKE '%" . $search . "%'"; 
    $object = exec_sql_query(DB_NAGIOSXI, $sql);

    $count = 0;
    if (!empty($object) && isset($object->fields["count"])) {
        $count = intval($object->fields["count"]);
    }

    return $count;
}

/**
 * Retrieves banner_message data
 *
 * Retrieves the banner_message data that falls under specific search data and returns it. Only returns
 * data that fits the page, perpage, and search parameters.
 * 
 * @param int    $page      The page number
 * @param int    $perpage   The number of banner_message data to display per page
 * @param string $order_by  The column to order the banner_message data by
 * @param string $order_dir The direction to order the banner_message data by
 * @param string $search    The search data to search the banner_message data for
 *  
 * @return array $rows      The data from the sql query
 */
function retrieve_banner_message_data($page, $perpage, $order_by, $order_dir, $search)
{
    global $db_tables;

    $page = escape_sql_param($page, DB_NAGIOSXI);
    $perpage = escape_sql_param($perpage, DB_NAGIOSXI);
    $search = escape_sql_param($search, DB_NAGIOSXI);

    // Whitelist for order_dir
    if (!in_array($order_dir, array('ASC', 'DESC'))) {
        $order_dir = 'ASC';
    }

    // Whitelist for order_by
    $wl_order_by = array('msg_id', 'message', 'time_created', 'created_by',
                        'acknowledged_by', 'specify_users', 'specific_users',
                        'acknowledgeable', 'display_message', 'banner_color');
    if (!in_array($order_by, $wl_order_by)) {
        $order_by = 'msg_id';
    }

    $sql = "SELECT * FROM " . $db_tables[DB_NAGIOSXI]["banner_messages"];
    $sql .= " WHERE msg_id LIKE '%" . $search . "%' or message LIKE '%" . $search . "%' or time_created LIKE '%" . $search . "%'"; 
    $sql .= " ORDER BY " . escape_sql_param($order_by, DB_NAGIOSXI) . " " . escape_sql_param($order_dir, DB_NAGIOSXI);
    $sql .= " LIMIT " . $perpage . " OFFSET " . ($page-1) * $perpage;
    $rows = exec_sql_query(DB_NAGIOSXI, $sql);

    if ($rows) {
        $rows = $rows->GetRows();

        foreach($rows as $i => $row) {
            $rows[$i] = unencode_row($row);
        }
    }

    return $rows;
}

/**
 * Retrieves specific banner_message
 *
 * Retrieves the specific banner_message data by msg id from the database and returns it.
 * 
 * @param int       $msg_id The msg id of the banner_message data to retrieve
 * 
 * @return array    $data   The specific banner_message data
 */
function retrieve_specific_banner_message($msg_id){
    global $db_tables;

    $sql = "SELECT banner_messages.*, users.username, link_users_messages.*
        FROM " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " AS banner_messages
        JOIN " . $db_tables[DB_NAGIOSXI]["link_users_messages"] . " AS link_users_messages ON banner_messages.msg_id = link_users_messages.msg_id
        JOIN " . $db_tables[DB_NAGIOSXI]["users"] . " AS users ON link_users_messages.user_id = users.user_id
        WHERE banner_messages.msg_id = " . $msg_id;

    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql))) {
        $data = $rs->GetArray();
    }
    return $data;
}

/**
 * Deletes specific banner_message
 *
 * Recieves a msg id and uses it to delete the correct message in mysql
 * 
 * @param int $msg_id The msg id of the banner_message data to delete
 * 
 */
function delete_banner_message($msg_id) {
    global $db_tables;
    global $DB;

    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " WHERE msg_id = " . $msg_id;

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    return $result;
}

/**
 * Updates display for banner_message
 *
 * Updates the display setting for a banner_message. Will be used in a bulk option.
 * 
 * @param int    $msg_id            The msg id of the banner_message data to delete
 * @param string $display_option    Variable holding the value to set the banner_message display to.
 * 
 * @return int   $result            Integer value of the rows affected by the sql query we run.
 */
function update_banner_message($msg_id, $display_option) {
    global $db_tables;
    global $DB;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " SET message_active = '" . escape_sql_param($display_option, DB_NAGIOSXI) . "' WHERE msg_id = " . $msg_id;

    $result = exec_sql_query(DB_NAGIOSXI, $sql);

    // affected rows is returning 0 here on successful updates. Will return result for now since
    // it gives the correct response to what is happening.

    return $result;
}

/**
 * Updates setting to display or not display the message feature.
 *
 * Recieves the value and sends a sql query to the xi_banner_message table in the nagiosxi database togive
 * the corresponding value for what the user input.
 * 
 * @param bool  $feature_display         The boolean value corresponding with to enable thefeature.
 *
 * @return bool $result                  Integer value of how many rows were affected by the sql query.
 */
function enable_banner_message_feature($feature_active) {
    global $db_tables;
    global $DB;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["banner_messages"] . " SET feature_active = '" . escape_sql_param($feature_active, DB_NAGIOSXI) ."'";

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    return $result;
}

/**
 * Checks to see if a date is within a range.
 *
 * Recieves data on a particular message and checks to see if the it should display the message
 * based on the given dates. We are returning false unless the date is within the start and end
 * date. The user can input a single start or end date and it will display accordingly and if no
 * input is given, the default value will be '0000-00-00'.
 *
 * @param bool  $start_date         A date value for when the user would like to start
 *                                  displaying this message.
 * @param bool  $end_date           A date value for when the user would like to stop
 *                                  displaying this message.
 *
 * @return bool $within_range       Boolean value for if the date is within the start and end date specified.
 */
function isWithinDateRange($start_date, $end_date) {
    $current_date = date('Y-m-d');
    $within_range = false;

    if ($start_date != '0001-01-01' && $end_date != '0001-01-01') {
        // Check if current date is between start and end date
        if ($current_date >= $start_date && $current_date <= $end_date) {
            $within_range = true;
        }
    } elseif ($start_date != '0001-01-01' && $end_date == '0001-01-01') {
        // Check if current date is after start date
        if ($current_date >= $start_date) {
            $within_range = true;
        }
    } elseif ($start_date == '0001-01-01' && $end_date != '0001-01-01') {
        if ($current_date <= $end_date) {
            $within_range = true;
        }
    }
    return $within_range;
}

/**
 * Creates correlated rows in link_users_messages table to banner_messages.
 *
 * This sql query is ran when a user makes a new message. It is ran through a for loop equal to
 * the amount of users there are. It creates a new row for each user and displays if the user has
 * acknowledged the msg and if they have been specified for this particualy message by the msg id.
 *
 * @param bool $msg_id              Msg id to connect user details with banner message.
 * @param bool $user_id             User id to create a unique row of data for each user on each
 *                                  new message.
 * @param bool $specified           A boolean value for if the message is to be displayed to
 *                                  specific users.
 *
 * @return int $result              Integer value for the amount of rows affected by the sql query we run.
 */
function insert_users_messages_table($msg_id, $user_id, $specified) {
    global $db_tables;
    global $DB;

    $sql = "INSERT INTO " . $db_tables[DB_NAGIOSXI]["link_users_messages"] . " (msg_id, user_id, specified)
    VALUES (
        ". escape_sql_param($msg_id, DB_NAGIOSXI, true ) .",
        ". escape_sql_param($user_id, DB_NAGIOSXI, true ).",
        ". escape_sql_param($specified, DB_NAGIOSXI, true ) .")";

    exec_sql_query(DB_NAGIOSXI, $sql);
    $result = $DB[DB_NAGIOSXI]->affected_rows();

    // return $result !== false && $result !== null;
    return $result;
}

/**
 * Updates correlated rows in link_users_messages table to banner_messages.
 *
 * Similar function to the one above. Ran when an admin is editing a message. Will change the
 * information in the database for the given message and will loop through this sql query to 
 * update the db to give the new information that was specified.
 *
 * @param int $msg_id               Msg id to connect user details with banner message.
 * @param int $user_id              User id to create a unique row of data for each user on each
 *                                  new message.
 * @param bool $specified           A boolean value for if the message is to be displayed to
 *                                  specific users.
 *
 * @return int $result              The return value from running the sql query.
 */
function update_users_messages_table($msg_id, $user_id, $specified) {
    global $db_tables;
    global $DB;

    $sql = "UPDATE " . $db_tables[DB_NAGIOSXI]["link_users_messages"] . " SET specified = '" . escape_sql_param($specified, DB_NAGIOSXI) . "' WHERE msg_id = '" . $msg_id ."' and user_id = '" . $user_id. "'";

    $result = exec_sql_query(DB_NAGIOSXI, $sql);

    // For some reason running affected_rows here causes it to no longer update the table.
    // Will just return the result of the sql query since it returns the correct information as it gives the correct response.
    return $result;
}

/**
 * Deletes rows correlated to a specific message
 *
 * Recieves a msg id and deletes all rows in the link_users_messages table that have
 * this msg id.
 * 
 * @param int   $msg_id     The msg id of the banner_message data to delete
 * 
 * @return int  $result     The return value from running the sql query.
 */
function delete_banner_message_from_link($msg_id) {
    global $db_tables;
    global $DB;

    $sql = "DELETE FROM " . $db_tables[DB_NAGIOSXI]["link_users_messages"] . " WHERE msg_id = " . $msg_id;

    $result = exec_sql_query(DB_NAGIOSXI, $sql);

    // When using affected rows it seems to always return 0 even when deleting rows. This isnt an
    // error however it will cause the return message to give a failure. Checking for 0 and above
    // isn't the right solution as then it will always say it deleted the message unless theres an
    // error. Will return the SQL query result for now as it is giving the correct response.

    return $result;
}
