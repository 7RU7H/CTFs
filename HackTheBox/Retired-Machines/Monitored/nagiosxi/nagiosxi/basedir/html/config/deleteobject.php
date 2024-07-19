<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab request vars and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


route_request();


function route_request()
{
    global $request;

    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");

    $reroute = false;

    // Make sure host/service exists
    if (host_exists($host) == false || ($service != "" && service_exists($host, $service) == false)) {
        $reroute = true;
    }

    // Check perms
    if ($service != "") {
        if (is_authorized_to_configure_service(0, $host, $service) == false) {
            $reroute = true;
        }
    } else {
        if (is_authorized_to_configure_host(0, $host) == false) {
            $reroute = true;
        }
    }

    if ($reroute == true) {
        header("Location: main.php");
        exit();
    }

    if ($service != "") {
        if (isset($request["delete"])) {
            do_delete_service();
        } else {
            confirm_service_delete();
        }
    } else {
        if (isset($request["delete"])) {
            do_delete_host();
        } else {
            confirm_host_delete();
        }
    }
}

/**
 * @param bool   $error
 * @param string $msg
 */
function confirm_service_delete($error = false, $msg = "")
{
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $return = grab_request_var("return", "");

    check_service_deletion_prereqs($host, $service);

    do_page_start(array("page_title" => _('Confirm Service Deletion')), true);
    ?>

    <h1><?php echo _('Confirm Service Deletion'); ?></h1>

    <div class="servicestatusdetailheader">
        <div class="serviceimage">
            <?php show_object_icon($host, $service, true); ?>
        </div>
        <div class="servicetitle">
            <div class="servicename">
                <a href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname">
                <a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <?php display_message($error, false, $msg); ?>

    <p style="margin: 20px 0;"><?php echo _('Are you sure you want to delete this service and remove it from the monitoring configuration?'); ?></p>

    <form method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="delete" value="1"/>
        <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>"/>
        <input type="hidden" name="service" value="<?php echo encode_form_val($service); ?>"/>
        <input type="hidden" name="return" value="<?php echo encode_form_val($return); ?>"/>

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-danger" name="deleteButton"><?php echo _('Delete'); ?></button>
            <button type="submit" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></button>
        </div>
    </form>

<?php
}


/**
 * @param bool   $error
 * @param string $msg
 */
function do_delete_service($error = false, $msg = "")
{
    global $request;


    // check session
    check_nagios_session_protector();

    // grab variables
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $return = grab_request_var("return", "");

    // user cancelled, so redirect them
    if (isset($request["cancelButton"])) {
        $url = get_return_url($return, $host, $service);
        //echo "CANCELLED - REDIRECTING TO ".$url." ($return)";
        header("Location: " . $url);
        exit();
    }

    check_service_deletion_prereqs($host, $service);

    // log it
    send_to_audit_log("User deleted service '" . $service . "' on host '" . $host . "'", AUDITLOGTYPE_DELETE);

    // submit delete command
    delete_nagioscore_service($host, $service);

    do_page_start(array("page_title" => _("Service Deletion Scheduled")), true);
    ?>

    <h1><?php echo _("Service Deletion Scheduled"); ?></h1>

    <div class="servicestatusdetailheader">
        <div class="serviceimage">
            <!--image-->
            <?php show_object_icon($host, $service, true); ?>
        </div>
        <div class="servicetitle">
            <div class="servicename"><a
                    href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <br clear="all">


    <?php
    display_message($error, false, $msg);
    ?>

    <p>
        <?php echo _("The requested service has been scheduled for deletion and should be removed shortly."); ?>
    </p>

    <?php
    if (is_advanced_user() == true) {
        ?>
        <p>
            <?php echo _("If the service fails to be removed"); ?>...
        </p>
        <ul>
            <?php
            if (is_admin() == true) {

                $url = get_base_url() . "admin/coreconfigsnapshots.php";
                echo "<li>" . _("Check the most recent") . " <a href='" . $url . "' target='_top'>" . _("configuration snapshots") . "</a> " . _("for errors") . "</li>";

                $url = get_base_url() . "includes/components/ccm/xi-index.php";
                echo "<li>Use the <a href='" . $url . "' target='_top'>Nagios Core Configuration Manager</a> " . _("to delete the service") . "</li>";
            } else {
                echo "<li>" . _("Ask your Nagios administrator to remove the service") . "</li>";
            }
            ?>
        </ul>
    <?php
    }
    ?>

    <?php
    if ($return != "") {
        ?>
        <form method="post" action="<?php echo get_return_url($return, $host, $service, true); ?>">

            <div id="formButtons" style="margin-top: 20px;">
                <button type="submit" class="btn btn-sm btn-primary" name="backButton"><?php echo _('Continue'); ?></button>
            </div>
        </form>

    <?php
    }
    ?>

    <?php
    exit();

}


/**
 * @param $host
 * @param $service
 */
function check_service_deletion_prereqs($host, $service)
{

    $sid = nagiosql_get_service_id($host, $service);

    // check for errors...
    $errors = 0;
    $errmsg = array();
    if ($sid <= 0)
        $errmsg[$errors++] = "Could not find a unique id for this service";
    if (can_service_be_deleted($host, $service) == false)
        $errmsg[$errors++] = "Service cannot be deleted using this method";

    // handle errors
    if ($errors > 0) {
        show_service_delete_error(true, $errmsg);
        exit();
    }
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_service_delete_error($error = false, $msg = "")
{

    // grab variables
    $host = grab_request_var("host", "");
    $service = grab_request_var("service", "");
    $return = grab_request_var("return", "");

    do_page_start(array("page_title" => _("Service Deletion Error")), true);
    ?>

    <h1><?php echo _("Service Deletion Error"); ?></h1>

    <div class="servicestatusdetailheader">
        <div class="serviceimage">
            <!--image-->
            <?php show_object_icon($host, $service, true); ?>
        </div>
        <div class="servicetitle">
            <div class="servicename"><a
                    href="<?php echo get_service_status_detail_link($host, $service); ?>"><?php echo $service; ?></a>
            </div>
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <?php display_message($error, false, $msg); ?>

    <div style="margin: 10px 0 20px 0;">

    <p>
        <?php echo _("One or more errors were detected that prevent the service from being deleted."); ?>
    </p>

    <p>
        <?php echo _("Possible causes include"); ?>...
    </p>
    <ul>
        <li><?php echo _("The service is associated with other hosts, services, or objects that need to be deleted first"); ?></li>
        <li><?php echo _("The service is generated by an advanced monitoring configuration entry"); ?></li>
        <li><?php echo _("The service is maintained in a static or external configuration file"); ?></li>
    </ul>

    <p>
        <?php echo _("To resolve this issue"); ?>...
    </p>
    <ul>
        <?php
        if (is_admin() == true) {
            $url = get_base_url() . "includes/components/ccm/xi-index.php";
            echo "<li>" . _("Use the") . " <a href='" . $url . "' target='_top'>Nagios Core Configuration Manager</a> " . _("to delete the service") . "  <b>- or -</b></li>";
            echo "<li>" . _("Manually delete the service definition from the appropriate external configuration file") . "</li>";
        } else {
            echo "<li>" . _("Ask your Nagios administrator to remove this service") . "</li>";
        }
        ?>
    </ul>

    </div>

    <?php
    if ($return != "") {
        ?>
        <form method="post" action="<?php echo get_return_url($return, $host, $service); ?>">
            <div id="formButtons">
                <input type="submit" class="btn btn-sm btn-default" name="backButton" value="<?php echo _("Back"); ?>"/>
            </div>
        </form>
    <?php
    }
    ?>

    <?php
    exit();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function confirm_host_delete($error = false, $msg = "")
{
    $host = grab_request_var("host", "");
    $return = grab_request_var("return", "");

    check_host_deletion_prereqs($host);

    do_page_start(array("page_title" => _('Confirm Host Deletion')), true);
    ?>

    <h1><?php echo _('Confirm Host Deletion'); ?></h1>

    <div class="hoststatusdetailheader">
        <div class="hostimage">
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <?php
    display_message($error, false, $msg);
    ?>

    <p style="margin: 20px 0;"><?php echo _('Are you sure you want to delete this host and remove it from the monitoring configuration?'); ?></p>

    <form method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="delete" value="1"/>
        <input type="hidden" name="host" value="<?php echo encode_form_val($host); ?>"/>
        <input type="hidden" name="return" value="<?php echo encode_form_val($return); ?>"/>

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-danger" name="deleteButton"><?php echo _('Delete'); ?></button>
            <button type="submit" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></button>
        </div>
    </form>

<?php
}


/**
 * @param bool   $error
 * @param string $msg
 */
function do_delete_host($error = false, $msg = "")
{
    global $request;

    // grab variables
    $host = grab_request_var("host", "");
    $return = grab_request_var("return", "");

    // user cancelled, so redirect them
    if (isset($request["cancelButton"])) {
        $url = get_return_url($return, $host);
        //echo "CANCELLED - REDIRECTING TO ".$url." ($return)";
        header("Location: " . $url);
        exit();
    }

    check_host_deletion_prereqs($host);

    // log it
    send_to_audit_log(_("User deleted host") . " '" . $host . "'", AUDITLOGTYPE_DELETE);

    // submit delete command
    delete_nagioscore_host($host);

    do_page_start(array("page_title" => _("Host Deletion Scheduled")), true);
    ?>

    <h1><?php echo _("Host Deletion Scheduled"); ?></h1>

    <div class="hoststatusdetailheader">
        <div class="hostimage">
            <!--image-->
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
    </div>

    <br clear="all">

    <?php
    display_message($error, false, $msg);
    ?>

    <p>
        <?php echo _("The requested host has been scheduled for deletion and should be removed shortly."); ?>
    </p>

    <?php
    if (is_advanced_user() == true) {
        ?>
        <p>
            <?php echo _("If the host fails to be removed"); ?>...
        </p>
        <ul>
            <?php
            if (is_admin() == true) {

                $url = get_base_url() . "admin/coreconfigsnapshots.php";
                echo "<li>" . _("Check the most recent") . " <a href='" . $url . "' target='_top'>" . _("configuration snapshots") . "</a> " . _("for errors") . "</li>";

                $url = get_base_url() . "includes/components/ccm/xi-index.php";
                echo "<li>" . _("Use the") . " <a href='" . $url . "' target='_top'>Nagios Core Configuration Manager</a> " . _("to delete the host") . "</li>";
            } else {
                echo "<li>" . _("Ask your Nagios administrator to remove the host") . "</li>";
            }
            ?>
        </ul>
    <?php
    }
    ?>

    <?php
    if ($return != "") {
        ?>
        <form method="post" action="<?php echo get_return_url($return, $host, "", true); ?>">

            <div id="formButtons" style="margin-top: 20px;">
                <button type="submit" class="btn btn-sm btn-primary" name="backButton"><?php echo _('Continue'); ?></button>
            </div>
        </form>

    <?php
    }
    ?>

    <?php
    exit();
}


/**
 * @param $host
 */
function check_host_deletion_prereqs($host)
{

    $hid = nagiosql_get_host_id($host);

    // check for errors...
    $errors = 0;
    $errmsg = array();
    if ($hid <= 0)
        $errmsg[$errors++] = _("Could not find a unique id for this host");
    if (can_host_be_deleted($host) == false)
        $errmsg[$errors++] = _("Host cannot be deleted using this method");

    // handle errors
    if ($errors > 0) {
        show_host_delete_error(true, $errmsg);
        exit();
    }
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_host_delete_error($error = false, $msg = "")
{

    // grab variables
    $host = grab_request_var("host", "");
    $return = grab_request_var("return", "");

    do_page_start(array("page_title" => _("Host Deletion Error")), true);
    ?>

    <h1><?php echo _("Host Deletion Error"); ?></h1>

    <div class="hoststatusdetailheader">
        <div class="hostimage">
            <!--image-->
            <?php show_object_icon($host, "", true); ?>
        </div>
        <div class="hosttitle">
            <div class="hostname"><a href="<?php echo get_host_status_detail_link($host); ?>"><?php echo $host; ?></a>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <?php display_message($error, false, $msg); ?>

    <div style="margin: 10px 0 20px 0;">

    <p>
        <?php echo _("One or more errors were detected that prevent the host from being deleted."); ?>
    </p>

    <p>
        <?php echo _("Possible causes include"); ?>...
    </p>
    <ul>
        <li><?php echo _("The host is associated with other hosts, services, or objects that need to be deleted first"); ?></li>
        <li><?php echo _("The host is maintained in a static or external configuration file"); ?></li>
    </ul>

    <p>
        <?php echo _("To resolve this issue"); ?>...
    </p>
    <ul>
        <?php
        if (is_admin() == true) {
            $url = get_base_url() . "includes/components/ccm/xi-index.php";
            echo "<li>" . _("Use the") . " <a href='" . $url . "' target='_top'>Nagios Core Configuration Manager</a> " . _("to delete the host") . "  <b>- or -</b></li>";
            echo "<li>" . _("Manually delete the host definition from the appropriate external configuration file") . "</li>";
        } else {
            echo "<li>" . _("Delete all services associated with this host first") . " <b>- or - </b></li>";
            echo "<li>" . _("Ask your Nagios administrator to remove this host") . "</li>";
        }
        ?>
    </ul>

    </div>

    <?php
    if ($return != "") {
        ?>
        <form method="post" action="<?php echo get_return_url($return, $host); ?>">
            <div id="formButtons">
                <input type="submit" class="btn btn-sm btn-default" name="backButton" value="<?php echo _("Back"); ?>"/>
            </div>
        </form>

    <?php
    }
    ?>

    <?php
    exit();
}


/**
 * @param        $return
 * @param        $host
 * @param string $service
 * @param bool   $deleteurl
 *
 * @return string
 */
function get_return_url($return, $host, $service = "", $deleteurl = false)
{

    switch ($return) {
        case "servicedetail";
            $url = get_service_status_detail_link($host, $service);
            if ($deleteurl == true)
                $url = get_base_url() . "includes/components/xicore/status.php?show=services";
            break;
        case "hostdetail";
            $url = get_host_status_detail_link($host);
            if ($deleteurl == true)
                $url = get_base_url() . "includes/components/xicore/status.php?show=hosts";
            break;
        default:
            $url = "main.php";
            break;
    }

    return $url;
}
