<?php
//
// Dashboard Deployment Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;
    $update = grab_request_var("update");
    if ($update == 1) {
        do_deploy_dashboards();
    } else {
        show_deploy();
    }
}


function do_deploy_dashboards()
{
    global $request;

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: index.php");
    }

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Get values
    $dashboards = grab_request_var("dashboards", array());
    $users = grab_request_var("users", array());

    // Make sure we have requirements
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }
    $total = 0;
    foreach ($dashboards as $db) {
        $selected = checkbox_binary(grab_array_var($db, "selected", 0));
        if ($selected == 1) {
            $total++;
            if ($db["desttitle"] == "") {
                $errmsg[$errors++] = _("Destination name cannot be blank.");
            }
        }
    }

    if ($total == 0) {
        $errmsg[$errors++] = _("You must select one or more dashboards to deploy.");
    }
    if (count($users) == 0) {
        $errmsg[$errors++] = _("You must select at least one user.");
    }

    // Handle errors
    if ($errors > 0) {
        show_deploy(true, $errmsg);
        exit();
    }

    // Deploy the dashboards
    foreach ($dashboards as $db) {

        $selected = checkbox_binary(grab_array_var($db, "selected", 0));
        if ($selected == 0) {
            continue;
        }

        $dbid = grab_array_var($db, "id");
        $keepsynced = checkbox_binary(grab_array_var($db, "keepsynced", 0));

        $dbopts = array(
            "sourceuser" => $_SESSION["user_id"],
            "sourceid" => $dbid,
            "title" => $db["desttitle"],
            "keepsynced" => $keepsynced
        );

        foreach ($users as $uid => $u) {
            $userid = intval($uid);
            if ($userid == 0) {
                continue;
            }
            deploy_dashboard_to_user($userid, $dbopts);
        }
    }

    show_deploy(false, _("Dashboards deployed"));
}


function deploy_dashboard_to_user($destuserid, $dbopts)
{
    // Get options
    $userid = grab_array_var($dbopts, "sourceuser", 0);
    $sourceid = grab_array_var($dbopts, "sourceid", 0);
    $title = grab_array_var($dbopts, "title", "");
    $keepsynced = grab_array_var($dbopts, "keepsynced", 0);

    // Find source dashboard
    $sourcedashboards = get_dashboards($userid);
    $i = 0;
    $source = null;
    foreach ($sourcedashboards as $d) {
        if ($d["id"] == $sourceid) {
            $source = $sourcedashboards[$i];
            break;
        }
        $i++;
    }

    // Didn't find the original, so bail
    if ($source == null) {
        return false;
    }

    $dest = $source;

    // Dest gets a new title
    $dest["title"] = $title;

    // Set options
    $dest["opts"]["sourceuserid"] = $userid;
    $dest["opts"]["sourceid"] = $sourceid;
    $dest["opts"]["keepsynced"] = $keepsynced;

    // Get dest dashboards
    $destdashboards = get_dashboards($destuserid);

    $newdashboards = array();
    $did = null;

    foreach ($destdashboards as $d) {

        $thedid = grab_array_var($d, "id");

        // Special case for home dashboard
        if ($sourceid == "home" && $thedid == "home") {
            $did = $sourcedid;
            continue;
        }

        // Overwrite/skip dashboards of the same source id (previously deployed dashboards)
        $sid = grab_array_var($d["opts"], "sourceid");
        if ($sid == $sourceid) {
            $did = grab_array_var($d, "id");
            continue;
        }

        // Otherwise save the dashboard
        $newdashboards[] = $d;
    }

    // Use the same id if possible
    if ($did == null) {
        if ($sourceid == "home") {
            $did = "home";
        } else {
            $did = random_string(6);
        }
    }

    $dest["id"] = $did;
    $newdashboards[] = $dest;

    $dashboardsraw = serialize($newdashboards);
    set_user_meta($destuserid, "dashboards", $dashboardsraw, false);
    return true;
}


function show_deploy($error = false, $msg = "")
{
    global $request;

    $dashboards = array();
    $users = array();

    $update = grab_request_var("update", 0);

    // Initialize dashboard info
    if ($update == 0) {
        $dbs = get_dashboards(0);
        foreach ($dbs as $db) {
            $dashboards[] = array(
                "id" => $db["id"],
                "selected" => 0,
                "title" => $db["title"],
                "desttitle" => $db["title"],
                "keepsynced" => 0,
                "readonly" => 0,
            );
        }
    } else {
        $dashboards = grab_request_var("dashboards", array());
        $users = grab_request_var("users", array());
    }

    do_page_start(array("page_title" => "Dashboard Deployment Tool"), true);
?>

<script>
function selectMass(mode)
{
    if (mode == 'deployall') {
        $('.deploybox').each(function(){this.checked=true;});
    } else if (mode == 'deploynone') {
        $('.deploybox').each(function(){this.checked=false;});
    } else if (mode == 'syncall') {
        $('.syncbox').each(function(){this.checked=true;});
    } else if (mode == 'syncnone') {
        $('.syncbox').each(function(){this.checked=false;});
    }
}
</script>

<h1><?php echo _("Dashboard Deployment Tool"); ?></h1>
<p>
    <?php echo _("This tool allows admins to deploy one or more of their own dashboards to other users. This can be useful when you design a dashboard that other users may want access to."); ?><br>
    <?php echo _("If you want each user's copy of your dashboard to stay synchronized with your dashboard, select the 'Keep Synced' option."); ?>
</p>
<p>
    <strong><?php echo _("Note"); ?>:</strong>
    <?php echo _("Re-deploying a dashboard that has already been deployed will overwrite the user's old copy of the deployed dashboard."); ?>
</p>

<?php display_message($error, false, $msg); ?>

<form action="" method="post">
    <input type="hidden" name="update" value="1">
    <?php echo get_nagios_session_protector(); ?>

    <h5 class="ul"><?php echo _("Dashboards To Deploy"); ?></h5>
    <p><?php echo _("Specify which of your dashboards should be deployed."); ?></p>

    <table class="table table-condensed table-striped table-bordered table-auto-width" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th><?php echo _("Deploy"); ?></th>
                <th>ID</th>
                <th><?php echo _("Local Name"); ?></th>
                <th><?php echo _("Destination Name"); ?></th>
                <th><?php echo _("Keep Synced"); ?></th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td><a onClick='selectMass("deployall")'><?php echo _("All"); ?></a> / <a onClick='selectMass("deploynone")'><?php echo _("None"); ?></a></td>
            <td></td><td></td><td></td>
            <td><a onClick='selectMass("syncall")'><?php echo _("All"); ?></a> / <a onClick='selectMass("syncnone")'><?php echo _("None"); ?></a></td>
        </tr>
        <?php
        foreach ($dashboards as $db) {
            if ($db['id'] == 'screen') { continue; }

            $selected = checkbox_binary(grab_array_var($db, "selected", "off"));
            $keepsynced = checkbox_binary(grab_array_var($db, "keepsynced", "off"));
            $readonly = checkbox_binary(grab_array_var($db, "readonly", "off"));

            echo "<tr>";
            echo "<input type='hidden' name='dashboards[" . $db["id"] . "][id]' value='" . encode_form_val($db["id"]) . "'>";
            echo "<input type='hidden' name='dashboards[" . $db["id"] . "][title]' value='" . encode_form_val($db["title"]) . "'>";
            echo "<td><input type='checkbox' class='deploybox' name='dashboards[" . $db["id"] . "][selected]' " . is_checked($selected, 1) . "></td>";
            echo "<td>" . encode_form_val($db["id"]) . "</td>";
            echo "<td>" . encode_form_val($db["title"]) . "</td>";
            echo "<td><input type='text' class='form-control condensed' name='dashboards[" . $db["id"] . "][desttitle]' value='" . encode_form_val($db["desttitle"]) . "' style='width: 200px;'></td>";
            echo "<td><input type='checkbox' class='syncbox' name='dashboards[" . $db["id"] . "][keepsynced]' " . is_checked($keepsynced, 1) . "></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

    <h5 class="ul"><?php echo _("Deploy To Users"); ?></h5>
    <p><?php echo _("Specify which users the dashboards should be deployed to."); ?> <a onClick="$('.sel-users-new input[type=\'checkbox\']').prop('checked', true)"><?php echo _("Check All"); ?></a> / <a onClick="$('.sel-users-new input[type=\'checkbox\']').prop('checked', false)"><?php echo _("Uncheck All"); ?></a></p>

    <div class="sel-users-new" style="width: 400px; margin-bottom: 20px;">
        <?php
        $ulist = get_users();
        $curr_username = get_user_attr(0, 'username');
        $total_users = 0;

        // Get all users except the current user and sort by full name
        foreach ($ulist as $i => $u) {
            if ($u['username'] == $curr_username) {
                unset($ulist[$i]);
            }
        }

        // Naturally sort the users list
        usort($ulist, function($a, $b) { return strnatcmp($a['name'], $b['name']); });

        // Generate the div box with all the users inside
        foreach ($ulist as $i => $u) {

            if (array_key_exists($u['user_id'], $users)) {
                $ischecked = "checked";
            } else {
                $ischecked = "";
            }

            echo "<div class='checkbox'><label><input type='checkbox' name='users[" . intval($u['user_id']) . "]' " . $ischecked . ">" . encode_form_val($u['name']) . " (" . encode_form_val($u['username']) . ")</label></div>";

            $total_users++;
        }

        if ($total_users == 0) {
            echo _("There are no other users defined on the system.");
        }
        ?>
    </div>

    <div id="formButtons">
        <button type="submit" class="btn btn-sm btn-primary" name="updateButton" id="updateButton"><?php echo _("Deploy Dashboards"); ?></button>
        <button type="submit" class="btn btn-sm btn-default" name="cancelButton" id="cancelButton"><?php echo _('Cancel'); ?></button>
    </div>
</form>

<?php
    do_page_end(true);
}
