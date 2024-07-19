<?php

require_once(dirname(__FILE__)."/../config.inc.php");
require_once(dirname(__FILE__)."/utilsl.inc.php");
require_once(dirname(__FILE__)."/utils-themes.inc.php");

if ($cfg['db_info']['nagiosxi']['dbtype'] == 'pgsql') {
    register_callback(CALLBACK_FRAME_START, 'print_upgrade_to_mysql_banner');
}

register_callback(CALLBACK_FRAME_START, 'print_banner_message_banner');

/** 
 * Prints a banner warning the user to upgrade from PostgreSQL to MySQL.
 * Does not check _whether_ the user is using any specific database.
 *  
 * @param           $cbtype     The callback type slug
 * @param   array   $args       An array of arguments

 */
function print_upgrade_to_mysql_banner($cbtype, $args) {

    // Don't display if we are in a parent page
    // These conditions were stolen from license_check_content_header()
    $theme = get_theme();
    if ($theme == 'xi2014' || $theme == 'classic') {
        if (!$args['child']) {
            return;
        }
    } else if ($args['child']) {
        return;
    }

    ?>
    <div class='contentheadernotice'><?php echo sprintf(_('Notice: Your Nagios XI server is configured to use PostgreSQL, which is deprecated. Please reach out to the Support Team for migration assistance.')); ?></div>
    <?php
}

function print_banner_message_banner($cbtype, $args = null) {
    global $cfg;
    // Don't display if we are in a parent page
    // Also stolen from license_check_content_header()
    $theme = get_theme();
    if ($theme == 'xi2014' || $theme == 'classic') {
        if (!$args['child']) {
            return;
        }
    } else if ($args['child']) {
        return;
    }

    $dbtype = '';
    if (array_key_exists("dbtype", $cfg['db_info'][DB_NAGIOSXI])) {
        $dbtype = $cfg['db_info'][DB_NAGIOSXI]['dbtype'];
    }

    if ($dbtype == 'mysql') {
        $banner_exists = check_banner_message_exists();
        if ($banner_exists) {

            $json_banner_message = retrieve_banner_message();
            $output = "<div>";
            $json_banner_message_length = count($json_banner_message);
            $banner_message_feature = $json_banner_message[0]['feature_active'];

            if ($banner_message_feature == 1) {

                for ($i = 0; $i < $json_banner_message_length; $i++) {

                    $banner_message_message = $json_banner_message[$i]['message'];
                    $msg_id = $json_banner_message[$i]['msg_id'];
                    $user_id = $json_banner_message[$i]['user_id'];
                    $msg_is_active = $json_banner_message[$i]['message_active'];
                    $acknowledgeable = $json_banner_message[$i]['acknowledgeable'];
                    $specify_users = $json_banner_message[$i]['specify_users'];
                    $current_user_id = $_SESSION["user_id"];
                    $banner_color = $json_banner_message[$i]['banner_color'];
                    $scheduled_message = $json_banner_message[$i]['schedule_message'];
                    $start_date = $json_banner_message[$i]['start_date'];
                    $end_date = $json_banner_message[$i]['end_date'];
                    $acknowledged = $json_banner_message[$i]['acknowledged'];
                    $specified_user = $json_banner_message[$i]['specified'];

                    if ($msg_is_active == 0) { continue; }
                    if ($current_user_id != $user_id) { continue; }
                    if ($scheduled_message == 1 && !isWithinDateRange($start_date, $end_date)) { continue; }
                    if ($specify_users == 1 && $specified_user == 0 ) { continue; }
                    if ($acknowledged == 1) { continue; }

                    $acknowledgeable_clause = "";
                    if ($acknowledgeable == 1) {
                        $acknowledgeable_clause = "<div class='fr'><a href='#' data-id='". $msg_id ."' class='". $banner_color ." acknowledge_banner_message_btn' title='"._('Close')."'>". _('Acknowledge') ."</a></div>";
                    }

                    $output .= "<div class='contentheadernotice banner_message_notice ". $banner_color ."'>".$acknowledgeable_clause. encode_form_val($banner_message_message) . "</div>";
                }
                $output .= "</div>";
                echo $output;
            }
        }
    }
}
