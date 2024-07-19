<?php
//
// Manage scheduled reports that were created by users
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

if (!is_admin()) {
    die(_("You do not have access to this section."));
}

route_request();

function route_request()
{
    global $request;
    $cmd = grab_request_var('cmd', '');

    switch ($cmd) {

        default:
            show_page();
            break;

    }
}


function show_page()
{
    $page = intval(grab_request_var("page", 1));
    $length = intval(grab_request_var("length", 10));
    if ($length < 5) { $length = 5; }

    $user_id = intval(grab_request_var("user_id", 0));
    $offset = 0;

    // Do paging stuff
    $total = get_scheduled_report_logs_count($user_id);
    $pages = ceil($total / $length);

    // Set page to max
    if ($page >= $pages+1) {
        $page = $pages;
    }
    if ($page < 1 && $pages >= 1) {
        $page = 1;
    } else if ($page < 0) {
        $page = 0;
    }

    $offset = ($page - 1) * $length;
    $max = $offset + $length;
    if ($offset < 0) {
        $offset = 0;
    }
    if ($max > $total) { $max = $total; }

    // Get the report logs
    $sr_logs = get_scheduled_report_logs($user_id, $offset, $length);

    // Grab users
    $users = get_users();

    // Enterprise Edition message
    $theme = get_theme();
    if ($theme == 'xi2014' || $theme == 'classic') {
        echo enterprise_message();
    }

    do_page_start(array("page_title" => _('Scheduled Reports History'), "enterprise" => true), true);

    // Do actual enterprise only check
    make_enterprise_only_feature();
?>

    <script type="text/javascript">
    $(document).ready(function() {

        // Update number of records
        $('.num-records').change(function() {
            var num = $(this).val();
            $('.num-records').val(num);
            $('input[name="length"]').val(num);
            $('form.filter').submit();
        });

    });
    </script>

    <form class="filter" method="get">
        <input type="hidden" name="length" value="<?php echo $length; ?>">
        <div class="well report-options form-inline">
            <div class="fl">
                <h1><?php echo _('Scheduled Reports History'); ?></h1>
            </div>
            <div class="fl" style="margin: 0 10px 0 20px;">
                <div class="input-group">
                    <label class="input-group-addon"><?php echo _("User"); ?></label>
                    <select name="user_id" class="form-control">
                        <option value=""></option>
                        <?php foreach ($users as $user) { ?>
                        <option value="<?php echo $user['user_id']; ?>"<?php if ($user['user_id'] == $user_id) { echo ' selected'; } ?>><?php echo $user['username']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-filter"></i> <?php echo _("Filter"); ?></button>
            <div class="clear"></div>
        </div>
    </form>

    <div style="margin-bottom: 20px;">
        <div class="fl" style="line-height: 26px;">
            <?php if ($total == 0) {
                echo _("Showing") . " " . $max . " " . _("of") . " " . $total . " " . _("scheduled report logs");
            } else {
                echo _("Showing") . " " . ($offset+1) . "-" . $max . " " . _("of") . " " . $total . " " . _("scheduled report logs");
            }?>
        </div>
        <div class="fr">
            <select class="form-control condensed num-records">
                <option value="5" <?php if ($length == 5) { echo 'selected'; } ?>><?php echo _("5 Per Page"); ?></option>
                <option value="10" <?php if ($length == 10) { echo 'selected'; } ?>><?php echo _("10 Per Page"); ?></option>
                <option value="25" <?php if ($length == 25) { echo 'selected'; } ?>><?php echo _("25 Per Page"); ?></option>
                <option value="50" <?php if ($length == 50) { echo 'selected'; } ?>><?php echo _("50 Per Page"); ?></option>
                <option value="100" <?php if ($length == 100) { echo 'selected'; } ?>><?php echo _("100 Per Page"); ?></option>
            </select>
        </div>
        <div class="fr">
            <a href="?page=1&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-xs btn-default" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></a>
            <a href="?page=<?php echo $page-1; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" <?php if ($page <= 1 ) { echo 'disabled'; } ?> class="btn btn-xs btn-default" title="<?php echo _('Previous Page'); ?>"><i class="fa fa-chevron-left"></i></a>
            <span style="margin: 0 10px;"><?php echo _("Page") . " " . $page . " " . _("of") . " " . $pages; ?></span>
            <a href="?page=<?php echo $page+1; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" <?php if ($page >= $pages) { echo 'disabled'; } ?> class="btn btn-xs btn-default" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right"></i></a>
            <a href="?page=<?php echo $pages; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-xs btn-default" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></a>
        </div>
        <div class="clear"></div>
    </div>

    <form class="multi-form" method="post">
        <input type="hidden" class="cmd-input" name="cmd" value="">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <?php echo get_nagios_session_protector(); ?>

        <table class="table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo _("Report Run Time"); ?></th>
                    <th><?php echo _("Report Name"); ?></th>
                    <th><?php echo _("Status"); ?></th>
                    <th><?php echo _("User"); ?></th>
                    <th><?php echo _("Recipients"); ?></th>
                    <th><?php echo _("Type") ?></th>
                    <th><?php echo _("Run Type") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($sr_logs) > 0) {
                    foreach ($sr_logs as $id => $log) {
                ?>
                <tr>
                    <td><?php echo get_datetime_string_from_datetime($log['report_run']); ?></td>
                    <td><?php echo encode_form_val($log['report_name']); ?></td>
                    <td><?php if ($log['report_status'] == 0) { echo _('Success'); } else { echo _('Error'); } ?></td>
                    <td><?php echo encode_form_val(get_user_attr($log['report_user_id'], 'username')); ?></td>
                    <td><?php echo encode_form_val($log['report_recipients']); ?></td>
                    <td><?php if ($log['report_type'] == 0) { echo _('Report'); } else { echo _('Page'); } ?></td>
                    <td><?php if ($log['report_run_type'] == 1) { echo _('Auto (Scheduled)'); } else { echo _('Manual'); } ?></td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7"><?php echo _("No scheduled reports have been ran that match the filters applied."); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <div class="fr">
                <select class="form-control condensed num-records">
                    <option value="5" <?php if ($length == 5) { echo 'selected'; } ?>><?php echo _("5 Per Page"); ?></option>
                    <option value="10" <?php if ($length == 10) { echo 'selected'; } ?>><?php echo _("10 Per Page"); ?></option>
                    <option value="25" <?php if ($length == 25) { echo 'selected'; } ?>><?php echo _("25 Per Page"); ?></option>
                    <option value="50" <?php if ($length == 50) { echo 'selected'; } ?>><?php echo _("50 Per Page"); ?></option>
                    <option value="100" <?php if ($length == 100) { echo 'selected'; } ?>><?php echo _("100 Per Page"); ?></option>
                </select>
            </div>
            <div class="fr">
                <a href="?page=1&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-xs btn-default" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></a>
                <a href="?page=<?php echo $page-1; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" <?php if ($page <= 1) { echo 'disabled'; } ?> class="btn btn-xs btn-default" title="<?php echo _('Previous Page'); ?>"><i class="fa fa-chevron-left"></i></a>
                <span style="margin: 0 10px;"><?php echo _("Page") . " " . $page . " " . _("of") . " " . $pages; ?></span>
                <a href="?page=<?php echo $page+1; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" <?php if ($page >= $pages) { echo 'disabled'; } ?> class="btn btn-xs btn-default" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right"></i></a>
                <a href="?page=<?php echo $pages; ?>&length=<?php echo $length; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-xs btn-default" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></a>
            </div>
            <div class="clear"></div>
        </div>

    </form>

<?php
    do_page_end(true);
}

