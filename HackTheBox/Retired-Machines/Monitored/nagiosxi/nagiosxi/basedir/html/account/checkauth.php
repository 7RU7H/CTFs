<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../includes/dbauth.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    show_page();
    exit;
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_page($error = false, $msg = "")
{
    $testperms = "";
    $otype = "";
    $oname = "";
    $oname2 = "";

    $testperms = grab_request_var("testperms", $testperms, "");
    $otype = grab_request_var("otype", $otype, "");
    $oname = grab_request_var("oname", $oname, "");
    $oname2 = grab_request_var("oname2", $oname2, "");

    do_page_start(array("page_title" => _("Auth Check")), true);
    ?>

    <h1>Auth Check</h1>

    <?php
    display_message($error, false, $msg);
    ?>

    <strong>Test Permissions:</strong>
    <form method="get" action="">
        <input type="hidden" name="testperms" value="1">
        <table border="1">
            <tr>
                <td>Object Type:</td>
                <td>
                    <select name="otype">
                        <option
                            value="<?php echo OBJECTTYPE_HOST; ?>" <?php echo is_selected($otype, OBJECTTYPE_HOST); ?>>
                            Host
                        </option>
                        <option
                            value="<?php echo OBJECTTYPE_SERVICE; ?>" <?php echo is_selected($otype, OBJECTTYPE_SERVICE); ?>>
                            Service
                        </option>
                        <option
                            value="<?php echo OBJECTTYPE_HOSTGROUP; ?>" <?php echo is_selected($otype, OBJECTTYPE_HOSTGROUP); ?>>
                            Hostgroup
                        </option>
                        <option
                            value="<?php echo OBJECTTYPE_SERVICEGROUP; ?>" <?php echo is_selected($otype, OBJECTTYPE_SERVICEGROUP); ?>>
                            Servicegroup
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Object Name:</td>
                <td>
                    <input type="text" name="oname" value="<?php echo encode_form_val($oname); ?>" size="15">
                    <input type="text" name="oname2" value="<?php echo encode_form_val($oname2); ?>" size="15">
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td><input type="submit" value="Test"></td>
            </tr>
        </table>
    </form>


    <?php
    if ($testperms == 1) {
        echo "<strong>Permission Test Results:</strong>\n";
        echo "<table border='1'>\n";

        if ($otype != OBJECTTYPE_SERVICE)
            $oname2 = null;
        $oid = get_object_id($otype, $oname, $oname2);
        echo "<tr><td>Object Exists</td><td>" . var_export(object_exists($otype, $oname, $oname2), true) . "</td></tr>\n";
        echo "<tr><td>ObjectID</td><td>" . $oid . "</td></tr>\n";
        if ($otype == OBJECTTYPE_HOST) {
            echo "<tr><td>Host Name</td><td>" . $oname . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Read?</td><td>" . var_export(is_authorized_for_object_id(0, $oid, P_READ), true) . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Commands?</td><td>" . var_export(is_authorized_for_host_command(0, $oname), true) . "</td></tr>\n";
        }
        if ($otype == OBJECTTYPE_HOSTGROUP) {
            echo "<tr><td>Hostgroup Name</td><td>" . $oname . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Read?</td><td>" . var_export(is_authorized_for_object_id(0, $oid, P_READ), true) . "</td></tr>\n";
            //echo "<tr><td>Is Authorized For Commands?</td><td>".var_export(is_authorized_for_hostgroup_command(0,$oname),true)."</td></tr>\n";
        }
        if ($otype == OBJECTTYPE_SERVICEGROUP) {
            echo "<tr><td>Servicegroup Name</td><td>" . $oname . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Read?</td><td>" . var_export(is_authorized_for_object_id(0, $oid, P_READ), true) . "</td></tr>\n";
            //echo "<tr><td>Is Authorized For Commands?</td><td>".var_export(is_authorized_for_servicegroup_command(0,$oname),true)."</td></tr>\n";
        }
        if ($otype == OBJECTTYPE_SERVICE) {
            echo "<tr><td>Host Name</td><td>" . $oname . "</td></tr>\n";
            echo "<tr><td>Service Name</td><td>" . $oname2 . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Read?</td><td>" . var_export(is_authorized_for_object_id(0, $oid, P_READ), true) . "</td></tr>\n";
            echo "<tr><td>Is Authorized For Commands?</td><td>" . var_export(is_authorized_for_service_command(0, $oname, $oname2), true) . "</td></tr>\n";
        }
        echo "</table>\n";
        echo "<br><br>";
    }
    ?>

    <strong>Auth Debug Info:</strong>
    <table border="1">
        <thead>
        <tr>
            <th>Item</th>
            <th>Value</th>
        </tr>
        </thead>
        <tbody>
        <?php

        $xi_userid = $_SESSION["user_id"];
        $xi_username = get_user_attr($xi_userid, "username");
        $ccm_id = nagiosql_get_contact_id($xi_username);
        $ndoutils_id = get_object_id(OBJECTTYPE_CONTACT, $xi_username);

        //echo "<tr><td></td><td></td></tr>";
        echo "<tr><td>XI User ID</td><td>" . $xi_userid . "</td></tr>";
        echo "<tr><td>XI Username</td><td>" . $xi_username . "</td></tr>";
        echo "<tr><td></td><td></td></tr>";
        echo "<tr><td>NDOUtils Contact ID</td><td>" . $ndoutils_id . "</td></tr>";
        echo "<tr><td>CCM Contact ID</td><td>" . $ccm_id . "</td></tr>";
        echo "<tr><td>Authorized to configure objects</td><td>" . is_authorized_to_configure_objects() . "</td></tr>";
        echo "<tr><td>Authorized for monitoring system</td><td>" . is_authorized_for_monitoring_system() . "</td></tr>";
        echo "<tr><td>Authorized for all objects</td><td>" . is_authorized_for_all_objects() . "</td></tr>";
        echo "<tr><td>Authorized for all object commands</td><td>" . is_authorized_for_all_object_commands() . "</td></tr>";
        echo "<tr><td>Is admin</td><td>" . is_admin() . "</td></tr>";


        echo "<tr><td>Is advanced user</td><td>" . get_user_meta($_SESSION["user_id"], "advanced_user") . "</td></tr>";
        echo "<tr><td>Is read-only user</td><td>" . get_user_meta($_SESSION["user_id"], "readonly_user") . "</td></tr>";
        echo "<tr><td></td><td></td></tr>";
        echo "<tr><td valign='top'>Authorized instance IDs (for non-admins) (NDOUtils)</td><td>" . str_replace("\n", "<BR>", var_export(get_authorized_instance_ids(), true)) . "</td></tr>";
        echo "<tr><td valign='top'>Authorized object IDs (NDOUtils)</td><td>" . str_replace("\n", "<BR>", var_export(get_authorized_object_ids(), true)) . "</td></tr>";

        ?>
        </tbody>
    </table>


    <?php

    do_page_end(true);
    exit();
}


?>