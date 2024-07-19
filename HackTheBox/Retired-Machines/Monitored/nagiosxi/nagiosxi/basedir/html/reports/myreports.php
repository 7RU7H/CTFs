<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
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
    $cmd = grab_request_var('cmd', '');

    if (!empty($cmd)) {
        if ($cmd == 'edit') {
            do_edit_report();
        }
    }

    if (isset($request['delete']))
        do_delete_report();
    else if (isset($request['add']))
        show_edit_report();
    else if (isset($request['edit']))
        show_edit_report();
    else if (isset($request['go']))
        visit_report();
    else
        show_reports();
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_reports($error = false, $msg = "")
{
    global $request;

    do_page_start(array("page_title" => _('My Reports')), true);

    if ((isset($request['cmd']) || isset($request['delete'])) && !$error) {
    ?>
    <script>
    $(document).ready(function() {
        var parent_path = window.parent.location.pathname;
        if (parent_path.indexOf("reports") != -1) {
            var data = get_ajax_data("getmyreportsmenu", "");
            data = JSON.parse(data);
            $('#leftnav ul.menusection', window.parent.document)[0].innerHTML = data.html;
            $('#leftnav div.menusectiontitle span.num', window.parent.document)[0].innerHTML = '('+data.count+')';
        }
    });
    </script>
    <?php } ?>

    <h1><?php echo _('My Reports'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p>
        <?php echo _("Your saved reports are shown below."); ?> <?php echo _("You can add a new report to this list by clicking on the"); ?> <i class="fa fa-star"></i> <?php echo _("button when viewing the report"); ?>.
    </p>

    <table class="table table-condensed table-striped table-auto-width">
        <thead>
            <tr>
                <th style="min-width: 240px;"><?php echo _("Report Name"); ?></th>
                <th class="center"><?php echo _("Actions"); ?></th>
            </tr>
        </thead>
        <tbody>

            <?php
            $mr = get_myreports(0);
            foreach ($mr as $id => $r) {
                echo "<tr>";
                echo "<td>" . encode_form_val($r["title"]) . "</td>";
                echo '<td class="center">';
                echo '<a href="?edit=1&id=' . urlencode($id) . '&nsp=' . get_nagios_session_protector_id() . '"><img src="' . theme_image('pencil.png') . '" alt="' . _('Edit') . '" title="' . _('Edit') . '" class="tt-bind"></a>&nbsp;';
                echo "<a href='?delete=1&id=" . urlencode($id) . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("cross.png") . "' alt='" . _('Delete') . "' title='" . _('Delete') . "' class='tt-bind'></a>&nbsp;";
                echo "<a href='?go=1&id=" . urlencode($id) . "&nsp=" . get_nagios_session_protector_id() . "'><img src='" . theme_image("b_next.png") . "' alt='" . _('View') . "' title='" . _('View') . "' class='tt-bind'></a>";
                echo "</td>";
                echo "</tr>";
            }
            if (count($mr) == 0)
                echo "<tr><td colspan='2'>" . _('You have no saved reports') . ".</td></tr>";
            ?>

        </tbody>
    </table>

    <?php
    do_page_end(true);
    exit();
}


function visit_report()
{
    $id = grab_request_var("id", 0);
    $url = get_myreport_url(0, $id);

    if ($url == "") {
        show_reports(true, _("Invalid report. Please select a saved report from the list below."));
    }

    header("Location: " . $url);
}


function show_edit_report($error = false, $msg = "")
{

    $id = grab_request_var('id', 0);
    if (!empty($id)) {
        $r = get_myreport_id(0, $id);
        $pagetitle = _('Edit My Report');
        $title = $r['title'];
        $dontdisplay = $r['dontdisplay'];
        $url = $r['url'];
        $meta = $r['meta'];
    } else {
        $pagetitle = _('Add to My Reports');
        $dontdisplay = 0;
        $url = '';
        $meta = '';
    }

    $title = grab_request_var("title", $title);
    $url = grab_request_var("url", $url);
    $meta_s = grab_request_var("meta_s", $meta);
    $dontdisplay = grab_request_var('dontdisplay', $dontdisplay);

    do_page_start(array("page_title" => $pagetitle), true);
?>

    <h1><?php echo $pagetitle; ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p><?php echo _("Use this function to save reports that you frequently access to your 'My Reports' menu."); ?></p>

    <form id="manageOptionsForm" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">

        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="cmd" value="edit">
        <input type="hidden" name="url" value="<?php echo encode_form_val($url); ?>">
        <input type="hidden" name="meta_s" value="<?php echo encode_form_val($meta_s); ?>">
        <input type="hidden" name="id" value="<?php echo encode_form_val($id); ?>">

        <table class="table table-condensed table-no-border table-auto-width">
            <tr>
                <td class="vt">
                    <label for="titleBox"><?php echo _("Report Title"); ?>:</label>
                </td>
                <td>
                    <input type="text" size="40" name="title" id="titleBox" value="<?php echo encode_form_val($title); ?>" class="form-control">
                    <div class="subtext"><?php echo _("The name you want to use for this report"); ?>.</div>
                </td>
            <tr>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" name="dontdisplay" value="1" <?php if ($dontdisplay == 1) { echo 'checked'; } ?>> <?php echo _('Do not show this report in the my reports menu section.'); ?></label>
                </td>
            </tr>
        </table>

        <div id="formButtons">
            <input type="submit" class="btn btn-sm btn-primary" name="updateButton" value="<?php echo _('Save Report'); ?>" id="updateButton">
            <input type="submit" class="btn btn-sm btn-default" name="cancelButton" value="<?php echo _('Cancel'); ?>" id="cancelButton">
        </div>

    </form>

    <?php
    do_page_end(true);
    exit();
}

function do_delete_report()
{
    
    check_nagios_session_protector();

    // Grab variables
    $id = grab_request_var("id", -1);

    $errmsg = array();
    $errors = 0;

    // Check for errors
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    if ($id == -1)
        $errmsg[$errors++] = _("Invalid report.");

    if ($errors > 0) {
        show_reports(true, $errmsg);
    }

    delete_myreport(0, $id);

    show_reports(false, _("Report deleted."));
}


function do_edit_report()
{
    global $request;

    $id = grab_request_var('id', 0);
    $title = grab_request_var('title', _('My Report'));
    $url = grab_request_var('url', '');
    $meta_s = grab_request_var('meta_s', '');
    $dontdisplay = grab_request_var('dontdisplay', 0);

    if ($meta_s == "") {
        $meta = array();
    } else {
        $meta = unserialize($meta_s);
    }

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: ".$url);
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
        $errmsg[$errors++] = _("Invalid report URL.");
    if (have_value($title) == false)
        $errmsg[$errors++] = _("No report title specified.");

    // Handle errors
    if ($errors > 0) {
        show_add_report(true, $errmsg);
    }
    
    add_myreport(0, $title, $url, $meta, $dontdisplay, $id);
    show_reports(false, _("Report saved."));
}