<?php
//
// Tactical Overview
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


// Initialization stuff
pre_init();
init_session(true);

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    // Performance optimization
    $opt = get_option("use_unified_tac_overview");
    if ($opt == 1) {
        header("Location: " . get_base_url() . "includes/components/nagioscore/ui/tac.php");
    }

    $view = grab_request_var("show", "");

    switch ($view) {
        default:
            show_tac();
            break;
    }
}


function show_tac()
{
    $host_warning_threshold = grab_request_var("host_warning_threshold",  get_user_meta(0, 'tac_host_warning_threshold', 80));
    $host_critical_threshold = grab_request_var("host_critical_threshold",  get_user_meta(0, 'tac_host_critical_threshold', 60));
    $service_warning_threshold = grab_request_var("service_warning_threshold",  get_user_meta(0, 'tac_service_warning_threshold', 80));
    $service_critical_threshold = grab_request_var("service_critical_threshold",  get_user_meta(0, 'tac_service_critical_threshold', 60));
    $ignore_soft_states = checkbox_binary(grab_request_var("ignore_soft_states",  get_user_meta(0, 'tac_ignore_soft_states', 0)));
    
    set_user_meta(0, 'tac_host_warning_threshold', $host_warning_threshold);
    set_user_meta(0, 'tac_host_critical_threshold', $host_critical_threshold);
    set_user_meta(0, 'tac_service_warning_threshold', $service_warning_threshold);
    set_user_meta(0, 'tac_service_critical_threshold', $service_critical_threshold);
    set_user_meta(0, 'tac_ignore_soft_states', $ignore_soft_states);

    do_page_start(array("page_title" => _('Tactical Overview')), true);
?>

<script type="text/javascript">
$(document).ready(function () {

    var p = $('#taccontrol').position();
    var left = Math.floor(p.left+16) - ($('#tacform').outerWidth()/2);
    var top = Math.floor(p.top+32);
    $('#tacform').css('left', left+'px');
    $('#tacform').css('top', top+'px');

    $("#taccontrol").click(function () {
        var d = $("#tacform").css("display");
        if (d == "none") {
            $("#tacform").fadeIn("fast");
        } else {
            $("#tacform").fadeOut("fast");
        }
    });
});
</script>

<h1><?php echo _('Tactical Overview'); ?> <img src="<?php echo theme_image("cog.png"); ?>" alt="<?php echo _('Configure'); ?>" class="tt-bind" data-placement="right" title="<?php echo _('Configure display options'); ?>" id="taccontrol"></h1>

<div id="tacform">
    <form method="GET" action="" style="margin: 0;">
        <b><?php echo _("Options"); ?>:</b>
        <div class="checkbox" style="margin: 2px 0 10px 0;">
            <label>
                <input type="checkbox" name="ignore_soft_states" <?php echo is_checked($ignore_soft_states, 1); ?>> <?php echo _("Ignore soft problems"); ?>
            </label>
        </div>
        <b><?php echo _('Health Thresholds'); ?>:</b>
        <div style="margin-top: 4px;">
            <label><?php echo _('Host Warning'); ?>:</label>
            <input type="text" size="2" name="host_warning_threshold" class="form-control condensed" value="<?php echo encode_form_val($host_warning_threshold); ?>"> %
        </div>
        <div style="margin-top: 4px;">
            <label><?php echo _("Host Critical"); ?>:</label>
            <input type="text" size="2" name="host_critical_threshold" class="form-control condensed" value="<?php echo encode_form_val($host_critical_threshold); ?>"> %
        </div>
        <div style="margin-top: 4px;">
            <label><?php echo _("Service Warning"); ?>:</label>
            <input type="text" size="2" name="service_warning_threshold" class="form-control condensed" value="<?php echo encode_form_val($service_warning_threshold); ?>"> %
        </div>
        <div style="margin-top: 4px;">
            <label><?php echo _("Service Critical"); ?>:</label>
            <input type="text" size="2" name="service_critical_threshold" class="form-control condensed" value="<?php echo encode_form_val($service_critical_threshold); ?>"> %
        </div>
        <div style="margin-top: 10px;">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-refresh l"></i> <?php echo _('Update'); ?></button>
        </div>
    </form>
</div>

<div class="tacoverview">

    <div class="fl">

        <div class="fl">
            <?php
            $dargs = array(
                DASHLET_ARGS => array()
            );
            display_dashlet("xicore_network_outages_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
            ?>
        </div>
        <div class="fl">
            <?php
            $dargs = array(
                DASHLET_ARGS => array(
                    "host_warning_threshold" => $host_warning_threshold,
                    "host_critical_threshold" => $host_critical_threshold,
                    "service_warning_threshold" => $service_warning_threshold,
                    "service_critical_threshold" => $service_critical_threshold,
                    "ignore_soft_states" => $ignore_soft_states
                )
            );
            display_dashlet("xicore_network_health", "", $dargs, DASHLET_MODE_OUTBOARD);
            ?>
        </div>
        <div class="clear"></div>

        <div clsas="fl">
            <?php
            $dargs = array(
                DASHLET_ARGS => array(
                    "ignore_soft_states" => $ignore_soft_states
                )
            );
            display_dashlet("xicore_host_status_tac_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
            ?>
        </div>
        <div class="clear"></div>

        <div class="fl">
            <?php
            $dargs = array(
                DASHLET_ARGS => array(
                    "ignore_soft_states" => $ignore_soft_states
                )
            );
            display_dashlet("xicore_service_status_tac_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
            ?>
        </div>
        <div class="clear"></div>

        <div class="fl">
            <?php
            $dargs = array(
                DASHLET_ARGS => array()
            );
            display_dashlet("xicore_feature_status_tac_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
            ?>
        </div>
        <div class="clear"></div>

    </div>

    <div class="clear"></div>
</div>

<?php
    do_page_end(true);
}
