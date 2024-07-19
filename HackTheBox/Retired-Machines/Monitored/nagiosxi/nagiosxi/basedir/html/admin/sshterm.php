<?php
//
// SSH Terminal (with shellinabox)
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

// Check for disabled state
$ssh_terminal_disable = get_option('ssh_terminal_disable', 0);
if ($ssh_terminal_disable == 1) {
    do_page_start(array("page_title" => _("SSH Terminal"), "enterprise" => true), true);
    echo '<h1>'._("SSH Terminal").'</h1>';
    echo '<p>'._('The SSH Terminal has been <b>disabled</b> by your Nagios XI administrator.').'</p>';
    echo '<p>'._('Contact your administrator for more information or to enable this feature.').'</p>';
    do_page_end(true);
    exit();
}

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

// If cloud license, do not allow access to this page
if (is_v2_license_type('cloud')) {
    echo _("Cloud licensed XI systems cannot use this page.");
    exit();
}


route_request();


function route_request()
{
    show_sshterm();
}


function show_sshterm($error = false, $msg = "")
{
    // Enterprise Feature Neatness
    // ** THIS FILE NEEDS TO BE ENCRYPTED **

    // Enterprise Edition message on other themes
    $theme = get_theme();
    if ($theme == 'xi2014' || $theme == 'classic') {
        echo enterprise_message();
    }

    // Use current URL to craft HTTPS url - this is needed to accomodate users who are
    // connecting through a NAT redirect/port forwarding setup
    $current = get_current_url(false, true);

    $default_url = $current;
    $pos = strpos($default_url, "/nagiosxi/admin/sshterm.php");
    $newurl = substr($default_url, 0, $pos) . "/nagiosxi/terminal";

    // Force SSL by default
    $url = str_replace("http:", "https:", $newurl);

    // User can override the URL
    $url = grab_request_var("url", $url);

    // Fix url to ensure it's proper
    $tmp = parse_url($url);
    $url = "https://".$tmp['host'].$tmp['path'];
    if (!empty($tmp['query'])) {
        $url .= "?".$tmp['query'];
    }

    // Check enterprise license
    $efe = enterprise_features_enabled();

    do_page_start(array("page_title" => _("SSH Terminal"), "enterprise" => true), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    // Page-specific settings functions
    var inframe = (window.location != window.parent.location) ? true : false;

    if ($('#open-dtform').length > 0) {
        var p = $('#open-dtform').position();
        var left = Math.floor(p.left+8) - ($('#settings-dropdown').outerWidth()/2);
        var top = Math.floor(p.top+15);

        if (inframe && $('.enterprisefeaturenotice').length > 0) {
            top = top + 30;
        }

        $('#settings-dropdown').css('left', left+'px');
        $('#settings-dropdown').css('top', top+'px');

        $("#open-dtform").click(function () {
            var d = $("#settings-dropdown").css("display");
            if (d == "none") {
                $("#settings-dropdown").fadeIn("fast");
            } else {
                $("#settings-dropdown").fadeOut("fast");
            }
        });
    }

    $('.btn-close-settings').click(function() {
        $("#settings-dropdown").fadeOut("fast");
    });

    $('.btn-update-settings').click(function() {
        var newurl = $('#urlBox').val();
        if (newurl) {
            window.location.href = "sshterm.php?cmd=update&url=" + encodeURIComponent(newurl);
        }
        $("#settings-dropdown").fadeOut("fast");
    });

});
</script>

    <div style="height: 29px; margin: 10px 0;">
        <h1 style="margin: 0 20px 0 0; padding: 0;" class="fl"><?php echo _("SSH Terminal"); ?></h1>
        <a class="tt-bind settings-dropdown" style="vertical-align: middle;" id="open-dtform" data-placement="right" title="<?php echo _('Edit page settings'); ?>"><img src="<?php echo theme_image('cog.png'); ?>"></a>
        <div id="settings-dropdown">
            <div class="content">
                <div class="input-group" style="width: 300px;">
                    <label class="input-group-addon"><?php echo _('URL'); ?></label>
                    <input type="text" size="45" name="url" id="urlBox" value="<?php echo encode_form_val($url); ?>" class="form-control">
                </div>
                <div style="margin-top: 10px">
                    <button type="button" class="btn btn-update-settings btn-xs btn-primary fl"><i class="fa fa-check fa-l"></i> <?php echo _('Update'); ?></button>
                    <button type="button" class="btn btn-close-settings btn-xs btn-default fr"><i class="fa fa-times"></i> <?php echo _('Close'); ?></button>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <?php display_message($error, false, $msg); ?>

    <p style="margin-bottom: 10px;">
        <?php echo _("The SSH terminal allows you to connect to your XI system using SSH. If the terminal does not display you must open it in a new window first and accept the SSL certificate before using it."); ?> <a href="javascript: void(0)" onclick="window.open('<?php echo encode_form_valq($url); ?>', 'windowname1', 'width=700,height=450,scrollbars=1'); return false;"><?php echo _('Open terminal in a new window'); ?> <i class="fa fa-external-link"></i></a>
    </p>

    <?php if ($efe) { ?>
        <iframe src="<?php echo encode_form_val($url); ?>" style="width: 50%; min-width: 600px; height: 500px;"></iframe>
        <?php } else { ?>
        <div style="color: #FFF; font-size: 14px; font-family: consolas, courier-new; background-color: #000; padding: 2px 6px; overflow-y: scroll; width: 50%; min-width: 600px; height: 500px;">Enterprise features must be enabled</div>
    <?php
    }

    $theme = get_theme();
    if (($theme == 'xi2014' || $theme == 'classic') && !$efe) {
        echo enterprise_limited_feature_message("This feature is only available in Enterprise Edition.");
    }

    do_page_end(true);
    exit();
}
