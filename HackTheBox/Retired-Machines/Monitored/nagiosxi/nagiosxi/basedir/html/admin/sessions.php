<?php
//
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs, and authorization
grab_request_vars();
check_prereqs();
check_authentication(false);


if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    $cmd = grab_request_var("cmd", "");
    switch ($cmd) {

        case 'logout':
            do_logout_session();
            break;

        default:
            show_page();
            break;

    }
}


function do_logout_session()
{
    $session_path = session_save_path();
    $session_id = grab_request_var('session_id', 0);
    if (empty($session_id)) {
        flash_message(_("Not a valid user session."), FLASH_MSG_ERROR);
        header("Location: sessions.php");
        return; 
    }

    // Get the actual session id
    $rs = exec_sql_query(DB_NAGIOSXI, "SELECT session_id, session_phpid FROM xi_sessions WHERE session_id = ".intval($session_id));
    $sessions = $rs->GetArray();
    if (empty($sessions)) {
        flash_message(_("Not a valid user or session."), FLASH_MSG_ERROR);
        header("Location: sessions.php");
        return; 
    } else {
        $session = $sessions[0];
    }

    // Remove session from the disk
    unlink($session_path.'/sess_'.$session['session_phpid']);

    // Remove the session from the database
    exec_sql_query(DB_NAGIOSXI, "DELETE FROM xi_sessions WHERE session_id = ".intval($session['session_id']));

    flash_message(_("Session logged out."));
    header("Location: sessions.php");
}


function show_page()
{

    // Get user session data
    $rs = exec_sql_query(DB_NAGIOSXI, "SELECT * FROM xi_sessions LEFT JOIN xi_users ON xi_sessions.session_user_id = xi_users.user_id");
    $sessions = $rs->GetArray();
    foreach ($sessions as $i => $d) {
        $sessions[$i]['session_data'] = unserialize(base64_decode($d['session_data']));
    }

    do_page_start(array("page_title" => _("User Sessions")), true);
    ?>

    <h1><?php echo _("User Sessions"); ?></h1>

    <p><?php echo _("The current sessions that are open for this Nagios XI. Past sessions will also show until they expire."); ?></p>

    <table class="table table-striped table-bordered table-hover table-condensed" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 150px;"><?php echo _('Created At'); ?></th>
                <th style="width: 150px;"><?php echo _('Last Active'); ?></th>
                <th><?php echo _('Username'); ?></th>
                <th><?php echo _('IP Address'); ?></th>
                <th><?php echo _('Active Location'); ?></th>
                <th style="width: 60px;"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $current_sid = session_id();
            foreach ($sessions as $session) {
                if ($session['session_address'] == $_SERVER['SERVER_ADDR']) {
                    continue;
                }

                // Get location
                $location = $session['session_address'];
                if (function_exists('geoip_country_name_by_name')) {
                    $arr = geoip_region_by_name($session['session_address']);
                    if (!empty($arr)) {
                        $location .= " (" . $arr['region'] . ", " . $arr['country_code'] . ")";
                    }
                }
            ?>
            <tr>
                <td><?php echo get_datetime_string(nstrtotime($session['session_created'])); ?></td>
                <td><?php echo get_datetime_string(nstrtotime($session['session_last_active'])); ?></td></td>
                <td>
                <?php
                echo encode_form_val($session['username']);
                if ($session['session_phpid'] == session_id()) {
                    echo " <em>("._('current session').")</em>";
                }
                ?>
                </td>
                <td><?php echo encode_form_val($location); ?></td>
                <td><?php echo encode_form_val(str_replace('/nagiosxi/', '', $session['session_page'])); ?></td>
                <td>
                    <?php
                    if ($session['session_phpid'] != $current_sid) {
                        echo '<a href="?cmd=logout&session_id=' . intval($session['session_id']) . '" class="tt-bind" data-placement="left" title="' . _('Remove') . '"><img src="'.theme_image('cross.png').'"></a>';
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php
    do_page_end(true);
}
