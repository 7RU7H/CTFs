<?php
//
// Nagios CCM Integration Component
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

if (!is_authorized_to_configure_objects() && !user_can_access_ccm()) {
    echo _("Not authorized");
    exit();
}


route_request();


function route_request()
{
    $cmd = grab_request_var("cmd", "");
    check_perms();

    switch ($cmd) {
        case "confirm":
            nagioscorecfg_confirm_apply();
            break;
        default:
            nagioscorecfg_apply_config();
            break;
    }
}


function check_perms()
{
    nagiosql_check_setuid_files($scripts_ok, $goodscripts, $badscripts);
    nagiosql_check_file_perms($config_ok, $goodfiles, $badfiles);

    if ($scripts_ok == true && $config_ok == true) {
        return;
    }

    do_page_start(array("page_title" => _("Permissions Problem")), true);
?>

    <h1><?php echo _("Permissions Problem"); ?></h1>

    <p>
        <img src="<?php echo theme_image("error_small.png"); ?>"> <b><?php echo _("Error"); ?>:</b> <?php echo _("The permissions on one or more configuration files or scripts appear to be incorrect. This will prevent your configuration from being applied properly."); ?>
    </p>

    <p>
        <a href="<?php echo get_base_url() . "/admin/?xiwindow=configpermscheck.php"; ?>" target="_top"><b><?php echo _("Click here to resolve this problem"); ?></b></a>.
    </p>

<?php
    do_page_end(true);
}


function nagioscorecfg_apply_config()
{
    $return_url = grab_request_var("return", "");

    send_to_audit_log("Ran apply configuration", AUDITLOGTYPE_CHANGE);

    do_page_start(array("page_title" => _('Apply Configuration')), true);
?>

    <h1><?php echo _('Apply Configuration'); ?></h1>

    <ul class="commandresult" style="margin: 15px 0;">
        <?php
        $error = false;

        // Check for any Apply Configuration commands ... We need to make sure only 1 is in the queue
        $args = array("command" => COMMAND_NAGIOSCORE_APPLYCONFIG, "status_code" => 0);
        $xml = get_command_status_xml($args);
        if ($xml) {
            foreach ($xml->command as $command) {
                $id = $command->attributes();
            }
        }

        if (!isset($id)) {
            $id = submit_command(COMMAND_NAGIOSCORE_APPLYCONFIG);
        }

        if ($id > 0) {
            echo "<li class='commandresultwaiting' id='commandwaiting'>" . _("Command submitted for processing...") . "</li>";
        } else {
            echo "<li class='commandresulterror'>" . _("An error occurred during command submission. If this problem persists, contact your Nagios administrator.") . "</li>";
            $error = true;
        }
        ?>
    </ul>

    <div id="commandsuccesscontent" style="display: none;">

        <div class="message">
            <ul class="infoMessage" style="margin: 0 0 15px 0;">
                <li><?php echo _('Nagios Core was').' <strong>'._('restarted').'</strong> '._('with an updated configuration.'); ?></li>
            </ul>
        </div>

        <?php
        echo "<p>";
        if (is_admin() == true) {
            echo "<p><a class='btn btn-xs btn-default' href='" . get_base_url() . "admin/coreconfigsnapshots.php'><i class='fa fa-external-link l'></i> " . _("View configuration snapshots") . "</a></p>";
        }
        if ($return_url != "") {
            echo "<p>\n";
            echo "<form method='get' action='" . htmlentities($return_url) . "'><input type='submit' name='continue' value='" . _("Continue") . "'></form>\n";
        }
        ?>
    </div>

    <div id="commanderrorcontent" style="display: none;">

        <div class="message">
            <ul class="errorMessage" style="margin: 0 0 20px 0;">
                <li><?php echo _("An error occurred while attempting to apply your configuration to Nagios Core.  Monitoring engine configuration files have been rolled back to their last known good checkpoint."); ?></li>
            </ul>
        </div>

        <?php
        echo "  <div>
                    <form method='post' action='' style='display: inline-block;'>
                        <input type='hidden' name='cmd' value=''>
                        <button type='submit' name='continue' class='btn btn-sm btn-primary'><i class='fa fa-refresh l'></i> " . _("Try Again") . "</button>
                    </form>";
        if (is_admin()) { echo "<a style='margin-left: 10px;' class='btn btn-sm btn-default' href='" . get_base_url() . "admin/coreconfigsnapshots.php'><i class='fa fa-external-link l'></i> " . _("View Snapshot") . "</a>"; }                
        echo "  </div>";
        ?>
    </div>

    <?php
    if (is_admin() == true) {
        echo '  <div class="view-opts">
                    <div><a class="show-details-button btn btn-xs btn-default" style="display: none; margin-bottom: 5px;">' . _("Show Written Configs") . '</a></div>
                    <div><a class="show-errors-button btn btn-xs btn-default" style="display: none;">' . _("Show Errors") . '</a></div>
                </div>';
    }
    ?>

    <div class="apply-config-details">
        <div class="alert alert-info" id="changes"></div>
        <div id="errors"></div>
    </div>

    <script type="text/javascript">

        $(document).ready(function () {

            $('.view-opts').on('click', '.show-details-button', function () {
                $('#changes').show();
                $('.show-details-button').hide();
            });

            $('.view-opts').on('click', '.show-errors-button', function () {
                $('#errors').show();
                $('.show-errors-button').hide();
            });

            setTimeout(applyconfig_get_apply_config_result, 1000, <?php echo $id; ?>);
        });

        function applyconfig_get_apply_config_result(command_id) {

            $('#commandwaiting').html('<?php echo _("Waiting for configuration verification..."); ?>');

            var intvid = setInterval(function () {

                $(".commandresultwaiting").append(".");
                var csdata = get_ajax_data("getcommandstatus", command_id);
                eval('var csobj=' + csdata);

                if (csobj.status_code > 1) {
                    if (csobj.result_code == 0) {
                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresultok");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).show();
                        });
                        $('#commandwaiting').html("<?php echo _("Configuration applied successfully"); ?>.");

                        // Display the actual changes for users to see
                        $('.show-details-button').show();
                        $('#changes').html(get_ajax_data("getapplyconfigchanges"));

                        // Remove the asterik from the Apply Configuration button ...
                        window.parent.$("#ccm-apply-menu-link").html('<span class="menu-icon"><i class="fa fa-fw fa-asterisk"></i></span><?php echo _("Apply Configuration"); ?>');
                    } else {

                        $('.commandresultwaiting').each(function () {
                            $(this).removeClass("commandresultwaiting");
                            $(this).addClass("commandresulterror");
                        });
                        $('#commandsuccesscontent').each(function () {
                            $(this).hide();
                        });
                        $('#commanderrorcontent').each(function () {
                            $(this).show();
                        });

                        //display message based on error code
                        /* exit codes:
                         #       1       config verification failed
                         #       2       nagiosql login failed
                         #       3       nagiosql import failed
                         #       4       reset_config_perms failed
                         #       5       nagiosql_exportall.php failed (write configs failed)
                         #       6       /etc/init.d/nagios restart failed
                         #       7       db_connect failed
                         */

                        var returnCode = csobj.result_code;
                        switch (returnCode) {
                            case '7': //db connect failed
                                $('#commandwaiting').html("<?php echo _("Failed to connect to the database"); ?>.");
                                break;
                            case '6': //nagios restart failed
                                $('#commandwaiting').html("<?php echo _("Nagios restart command failed"); ?>.");
                                break;
                            case '5': //write configs failed
                                $('#commandwaiting').html("<?php echo _("Configurations failed to write to file"); ?>.");
                                break;
                            case '4': //reset config perms failed
                                $('#commandwaiting').html("<?php echo _("Reset config permissions failed"); ?>.");
                                break;
                            case '3':  //nagiosql import (wizard import) failed
                                $('#commandwaiting').html("<?php echo _("Configuration import failed"); ?>.");
                                break;
                            case '2':  //nagiosql login failed
                                $('#commandwaiting').html("<?php echo _("Backend login to the Core Config Manager failed"); ?>.");
                                break;
                            case '1': //config verification failed
                                $('#commandwaiting').html("<?php echo _("Configuration verification failed"); ?>.");
                                break;
                            default:
                                $('#commandwaiting').html("<?php echo _("There was an error while attempting to apply configuration. Error code"); ?>: " + returnCode + ".");
                                break;
                        }

                        // Display the actual changes and errors for users to see
                        $('.show-errors-button').show();
                        $('.show-details-button').show();
                        $('#changes').html(get_ajax_data("getapplyconfigchanges"));
                        $('#errors').html(get_ajax_data("getapplyconfigerrors"));
                    }

                    clearInterval(intvid);
                }
            }, 1000);
        }
    </script>

    <?php
    do_page_end(true);
}


function nagioscorecfg_confirm_apply()
{
    // If there are sessions older than 5 min let's delete them
    $timeout = get_option('ccm_page_lock_timeout', 300);
    $del_time = time() - $timeout;
    $sql = "DELETE FROM `tbl_session` WHERE `last_updated` < ".$del_time.";";
    exec_sql_query(DB_NAGIOSQL, $sql);

    // Check for users currently inside the CCM
    $user_id = intval($_SESSION['user_id']);
    $sql = "SELECT *, l.id AS id FROM `tbl_session_locks` AS l LEFT JOIN `tbl_session` AS s ON l.sid = s.id WHERE s.user_id != ".$user_id.";";
    $res = exec_sql_query(DB_NAGIOSQL, $sql);
    $locks = $res->getArray();

    do_page_start(array("page_title" => _('Apply Configuration')), true);
?>

    <h1><?php echo _('Apply Configuration'); ?></h1>

    <p>
        <?php echo _('Use this feature to apply any outstanding configuration changes to Nagios Core. Changes will be applied and the monitoring engine will be restarted with the updated configuration.'); ?>
    </p>

    <?php if (count($locks) > 0) { ?>
    <div class="alert alert-danger" style="margin: 10px 0;">
        <div style="margin-bottom: 10px;"><i class="fa fa-exclamation-triangle fa-14 fa-l"></i> <b><?php echo _("Users are currently editing objects in the CCM"); ?></b></div>
        <ul>
        <?php
        foreach ($locks as $lock) {
            echo '<li>'.get_user_attr($lock['user_id'], 'username').' '._('is editing a').' '.$lock['type'].' (started at '.get_datetime_string($lock['started'], DT_SHORT_DATE_TIME, DF_AUTO, "null").')</li>';
        }
        ?>
        </ul>
    </div>
    <?php } ?>

    <p>
        <form method='post' action=''>
            <input type='hidden' name='cmd' value=''>
            <button type='submit' class="btn btn-sm btn-primary" name='continue'><?php echo _('Apply Configuration'); ?></button>
        </form>
    </p>

    <?php
    do_page_end(true);
}