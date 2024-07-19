<?php
//
// Copyright (c) 2011-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');


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
    global $request;

    if (isset($request['update']))
        do_update_tool();
    if (isset($request['delete']))
        do_delete_tool();
    else if (isset($request['edit']))
        show_edit_tool();
    else if (isset($request['go']))
        visit_tool();
    else
        show_tools();
}


function show_tools($error = false, $msg = "")
{
    global $request;

    do_page_start(array("page_title" => _('Common Tools')), true);
    
    if ((isset($request['update']) || isset($request['delete'])) && !$error) {
    ?>
    <script>
    $(document).ready(function() {
        var data = get_ajax_data("getcommontoolsmenu", "");
        data = JSON.parse(data);
        $('#leftnav ul.menusection', window.parent.document)[1].innerHTML = data.html;
    });
    </script>
    <?php } ?>

    <h1><?php echo _('Common Tools'); ?></h1>

    <?php display_message($error, false, $msg); ?>
    <?php if (is_admin()) { ?>

    <p><?php echo _("Common tools that you have defined are available to all users on the system.") ?></p>
    <p><a href="?edit=1" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add a Tool") ?></a></p>

    <?php } else { ?>

    <p><?php echo _("Access common tools that have been defined by the administrator.") ?></p>

    <?php } ?>

    <table class="table table-condensed table-striped table-auto-width">
        <thead>
            <tr>
                <th><?php echo _("Tool Name") ?></th>
                <th><?php echo _("URL") ?></th>
                <th><?php echo _("Actions") ?></th>
            </tr>
        </thead>
        <tbody>

        <?php
        $mr = get_commontools();
        foreach ($mr as $id => $r) {
            echo "<tr>";
            echo "<td>" . encode_form_val($r["name"]) . "</td>";
            echo '<td><a href="' . urlencode(encode_form_val($r["url"])) . '" target="_blank">' . encode_form_val($r["url"]) . '</a></td>';
            echo "<td>";
            if (is_admin()) {
                echo "<a href='?edit=1&id=" . $id . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("pencil.png") . "' alt='" . _('Edit') . "' title='" . _('Edit') . "' class='tt-bind'></a>&nbsp;";
                echo "<a href='?delete=1&id=" . $id . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("cross.png") . "' alt='" . _('Delete') . "' title='" . _('Delete') . "' class='tt-bind'></a>&nbsp;";
            }
            echo "<a href='?go=1&id=" . $id . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("b_next.png") . "' alt='" . _('View') . "' title='" . _('View') . "' class='tt-bind'></a>";
            echo "</td>";
            echo "</tr>";
        }
        if (count($mr) == 0) {
            if (is_admin())
                echo "<tr><td colspan='3'>" . _("You haven't defined any tools yet.") . " (<a href='?edit=1'>" . _("Add one now") . "</a>)</td></tr>";
            else
                echo "<tr><td colspan='3'>" . _("No common tools have been defined yet.") . "</td></tr>";
        }
        ?>

        </tbody>
    </table>

    <?php
    do_page_end(true);
    exit();
}


function visit_tool()
{
    $id = grab_request_var("id", 0);
    $url = get_commontool_url($id);

    if ($url == "") {
        show_tools(true, _("Invalid tool. Please select a tool from the list below."));
    }

    header("Location: " . $url);
}


function show_edit_tool($error = false, $msg = "")
{
    $name = _("New Tool");
    $url = "";

    // Grab variables
    $id = grab_request_var("id", -1);

    $add = false;
    if ($id == -1) {
        $add = true;
    }

    if ($add == true) {
        $pagetitle = _('Add Common Tool');
        $pageheader = _('Add Common Tool');
    } else {
        $pagetitle = _('Edit Common Tool');
        $pageheader = _('Edit Common Tool');

        // Load old values
        $ctool = get_commontool_id($id);
        $name = grab_array_var($ctool, "name", $name);
        $url = grab_array_var($ctool, "url", $url);
    }

    // Get posted variables
    $name = grab_request_var("name", $name);
    $url = grab_request_var("url", $url);

    do_page_start(array("page_title" => $pagetitle), true);
?>

    <h1><?php echo $pageheader; ?></h1>

    <?php display_message($error, false, $msg); ?>

    <form id="manageOptionsForm" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">

        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="id" value="<?php echo encode_form_val($id); ?>">

        <?php if ($add == true) { ?>
        <p><?php echo _("Use this form to define a new tool that can be quickly accessed from Nagios.") ?></p>
        <?php } ?>

        <table class="table table-condensed table-no-border table-auto-width">
            <tr>
                <td class="vt">
                    <label for="nameBox"><?php echo _("Tool Name") ?>:</label>
                </td>
                <td>
                    <input type="text" size="40" name="name" id="nameBox" value="<?php echo encode_form_val($name); ?>" class="form-control">
                    <div class="subtext"><?php echo _("The name you want to use for this tool.") ?></div>
                </td>
            <tr>
            <tr>
                <td class="vt">
                    <label for="urlBox"><?php echo _("URL") ?> :</label>
                </td>
                <td>
                    <input type="text" size="40" name="url" id="urlBox" value="<?php echo encode_form_val($url); ?>" class="form-control">
                    <div class="subtext"><?php echo _("The URL used to access this tool.") ?></div>
                </td>
            <tr>
        </table>

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-primary" name="updateButton" id="updateButton"><?php echo _('Save'); ?></button>
            <button type="submit" class="btn btn-sm btn-default" name="cancelButton" id="cancelButton"><?php echo _('Cancel'); ?></button>
        </div>

    </form>
    <?php


    // closes the HTML page
    do_page_end(true);
    exit();
}


function do_delete_tool()
{

    if (!is_admin()) {
        show_tools();
        exit();
    }

    // check session
    check_nagios_session_protector();

    // grab variables
    $id = grab_request_var("id", -1);

    $errmsg = array();
    $errors = 0;

    // check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    if ($id == -1)
        $errmsg[$errors++] = _("Invalid tool.");

    // handle errors
    if ($errors > 0)
        show_tools(true, $errmsg);

    // delete the tool
    delete_commontool($id);

    show_tools(false, _("Tool deleted."));
}


function do_update_tool()
{
    global $request;

    if (!is_admin()) {
        show_tools();
        exit();
    }

    // Grab variables
    $id = grab_request_var("id", -1);
    $name = grab_request_var("name", _("New Tool"));
    $url = grab_request_var("url", "");

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: commontools.php");
        exit();
    }

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    if (have_value($url) == false)
        $errmsg[$errors++] = _("Invalid tool URL.");
    if (have_value($name) == false)
        $errmsg[$errors++] = _("No tool name specified.");

    // Handle errors 
    if ($errors > 0) {
        show_edit_tool(true, $errmsg);
    }

    update_commontool($id, $name, $url);

    show_tools(false, _("Tool saved."));
}