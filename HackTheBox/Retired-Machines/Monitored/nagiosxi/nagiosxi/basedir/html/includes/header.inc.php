<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/common.inc.php');

$theme = get_theme();
?>

<!--- HEADER START -->

<?php
// Default logo stuff
$logo = "nagiosxi-logo-small.png";
$logo_alt = get_product_name();
$logo_url = get_base_url();
$logo_target = "_top";

// Use custom logo if it exists
$logosettings_raw = get_option("custom_logo_options");
if ($logosettings_raw == "")
    $logosettings = array();
else
    $logosettings = unserialize($logosettings_raw);

$custom_logo_enabled = grab_array_var($logosettings, "enabled");
if ($custom_logo_enabled == 1) {
    $logo = grab_array_var($logosettings, "logo", $logo);
    $logo_alt = grab_array_var($logosettings, "logo_alt", $logo_alt);
    $logo_url = grab_array_var($logosettings, "logo_url", $logo_url);
    $logo_target = grab_array_var($logosettings, "logo_target", $logo_target);
}

if ($theme == "xi5" || $theme == "xi5dark") {

    if (!$custom_logo_enabled) {
?>

    <div id="toplogo">
        <a href="<?php echo $logo_url; ?>" target="<?php echo $logo_target; ?>">
            <img src="<?php echo get_base_url(); ?>images/nagios_logo_white_transbg.png" border="0" class="xi-logo" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
            XI
        </a>
    </div>
    
    <?php  } else { ?>

    <div id="toplogo">
        <a href="<?php echo $logo_url; ?>" target="<?php echo $logo_target; ?>">
            <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
        </a>
    </div>

    <?php } ?>

    <div id="topmenu">
        <?php if (is_authenticated()) { ?>
            <div class="mainmenu">
                <div><a href="<?php echo get_base_url(); ?>"><?php echo _("Home"); ?></a></div>
                <div><a href="<?php echo get_base_url(); ?>views/"><?php echo _("Views"); ?></a></div>
                <div><a href="<?php echo get_base_url(); ?>dashboards/"><?php echo _("Dashboards"); ?></a></div>
                <div><a href="<?php echo get_base_url(); ?>reports/"><?php echo _("Reports"); ?></a></div>
                <?php if ((is_authorized_to_configure_objects() && !is_readonly_user()) || user_can_access_ccm()) { ?>
                    <div id="config-menulink">
                        <span>
                            <a href="<?php echo get_base_url(); ?>config/"><?php echo _("Configure"); ?></a>
                            <ul class="config-dropdown">
                                <?php if (is_authorized_to_configure_objects()) { ?>
                                <li><a href="<?php echo get_base_url(); ?>config/?xiwindow=monitoringwizard.php"><i class="fa fa-magic fa-fw l"></i> <?php echo _('Configuration Wizards'); ?></a></li>
                                <?php } ?>
                                <li><a href="<?php echo get_base_url(); ?>config/?xiwindow=deployment/index.php"><i class="fa fa-play fa-fw l"></i> <?php echo _('Deploy Agent'); ?></a></li>
                                <?php if (user_can_access_ccm()) { ?>
                                <li><a href="<?php echo get_base_url(); ?>includes/components/ccm/xi-index.php"><i class="fa fa-cog fa-fw l"></i> <?php echo _('Core Config Manager'); ?></a></li>
                                <?php } ?>
                            </ul>
                        </span>
                    </div>
                <?php } ?>
                <div><a href="<?php echo get_base_url(); ?>tools/"><?php echo _("Tools"); ?></a></div>
                <div><a href="<?php echo get_base_url(); ?>help/"><?php echo _("Help"); ?></a></div>
                <?php if (is_admin()) { ?>
                    <div><a href="<?php echo get_base_url(); ?>admin/"><?php echo _("Admin"); ?></a></div>
                <?php } ?>
            </div>
            <div class="hiddenmenu">
                <div id="mdropdown">
                    <span>
                        <a class="nav-head"><i class="fa fa-chevron-down l"></i> <?php echo _("Navigation"); ?></a>
                        <ul class="dropdown-items">
                            <li><a href="<?php echo get_base_url(); ?>"><?php echo _("Home"); ?></a></li>
                            <li><a href="<?php echo get_base_url(); ?>views/"><?php echo _("Views"); ?></a></li>
                            <li><a href="<?php echo get_base_url(); ?>dashboards/"><?php echo _("Dashboards"); ?></a></li>
                            <li><a href="<?php echo get_base_url(); ?>reports/"><?php echo _("Reports"); ?></a></li>
                            <?php  if (is_authorized_to_configure_objects() && !is_readonly_user()) { ?>
                                <li><a href="<?php echo get_base_url(); ?>config/"><?php echo _("Configure"); ?></a></li>
                            <?php } ?>
                            <li><a href="<?php echo get_base_url(); ?>tools/"><?php echo _("Tools"); ?></a></li>
                            <li><a href="<?php echo get_base_url(); ?>help/"><?php echo _("Help"); ?></a></li>
                            <?php if (is_admin()) { ?>
                                <li><a href="<?php echo get_base_url(); ?>admin/"><?php echo _("Admin"); ?></a></li>
                            <?php } ?>
                        </ul>
                    </span>
                </div>
            </div>
        <?php } else if (get_current_page() == PAGEFILE_INSTALL) { ?>
            <div style="color: #EEE; font-size: 1.1rem; cursor: default;">
                <b><?php echo _("Install"); ?></b>
            </div>
        <?php } else { ?>
        <div class="mainmenu">
            <div><a href="<?php echo get_base_url() . PAGEFILE_LOGIN; ?>"><?php echo _("Login"); ?></a></div>
        </div>
        <?php } ?>
    </div>

    <?php if (is_authenticated()) { ?>
    <div class="header-right ext">
        <span class="ext-menu">
            <i class="fa fa-bars"></i>
            <ul>
                <li id="schedulepagereport" class="tt-bind" data-placement="left" title="<?php echo _('Schedule page'); ?>"><a href="#"><i class="fa fa-clock-o"></i></a></li>
                <li id="popout" class="tt-bind" data-placement="left" title="<?php echo _('Popout'); ?>"><a href="#"><i class="fa fa-share-square-o"></i></a></li>
                <li id="addtomyviews" class="tt-bind" data-placement="left" title="<?php echo _('Add to my views'); ?>"><a href="#"><i class="fa fa-plus-circle"></i></a></li>
                <li id="permalink" class="tt-bind" data-placement="left" title="<?php echo _('Get permalink'); ?>"><a href="#"><i class="fa fa-chain"></i></a></li>
                <li id="feedback" class="tt-bind" data-placement="left" title="<?php echo _('Send us feedback'); ?>"><a href="#"><i class="fa fa-comment-o"></i></a></li>
            </ul>
        </span>
    </div>
    <div class="header-right profile">
        <a href="<?php echo get_base_url(); ?>account/" style="margin-right: 1.5rem;"><i class="fa fa-user"></i> <span><?php echo encode_form_val($_SESSION["username"]); ?></span></a>
        <?php if (!is_http_basic_authenticated()) { ?>
            <a href="<?php echo get_base_url() . PAGEFILE_LOGIN; ?>?logout&amp;nsp=<?php echo get_nagios_session_protector_id(); ?>"><i class="fa fa-power-off"></i> <span><?php echo _("Logout"); ?></span></a>
        <?php } ?>
    </div>
    <div class="header-right system-alerts">
        <?php display_pagetop_alerts(); ?>
    </div>
    <div class="header-right search">
        <div class="search-field hide">
            <form method="post" target="maincontentframe" action="<?php echo get_base_url(); ?>includes/components/xicore/status.php?show=services">
                <input type="hidden" name="navbarsearch" value="1"/>
                <input type="text" class="search-query form-control" name="search" id="navbarSearchBox" value="" placeholder="<?php echo _('Search...'); ?>"/>
            </form>
        </div>
        <a href="#" id="open-search" title="<?php echo _('Search'); ?>"><i class="fa fa-search"></i></a>
    </div>
    <?php } ?>

<?php } else { ?>
    <div id="toplogo">
        <a href="<?php echo $logo_url; ?>" target="<?php echo $logo_target; ?>">
            <img src="<?php echo get_base_url(); ?>images/<?php echo $logo; ?>" border="0" alt="<?php echo $logo_alt; ?>" title="<?php echo $logo_alt; ?>">
        </a>
    </div>
    <div id="pagetopalertcontainer">
        <?php if (is_authenticated() == true) {
            display_pagetop_alerts();
        } ?>
    </div>
    <div id="authinfo">
        <?php if (is_authenticated() == true) { ?>
            <div id="authinfoname">
                <?php echo _("Logged in as"); ?>: <a href="<?php echo get_base_url(); ?>account/"><?php echo encode_form_val($_SESSION["username"]); ?></a>
            </div>
            <?php if (is_http_basic_authenticated() == false) { ?>
                <div id="authlogout">
                    <a href="<?php echo get_base_url() . PAGEFILE_LOGIN; ?>?logout&amp;nsp=<?php echo get_nagios_session_protector_id(); ?>"><?php echo _("Logout"); ?></a>
                </div>
            <?php
            }
        }
        ?>
    </div>
<?php
}

// If using the new style
if ($theme == "xi2014") {

    // Find out what tab is active
    $active = "home";

    $filename = $_SERVER['SCRIPT_FILENAME'];
    if (strpos($filename, "html/admin")) {
        $active = "admin";
    } else if (strpos($filename, "html/views")) {
        $active = "views";
    } else if (strpos($filename, "html/dashboards")) {
        $active = "dashboards";
    } else if (strpos($filename, "html/reports")) {
        $active = "reports";
    } else if (strpos($filename, "html/config") || strpos($filename, "includes/components/ccm")) {
        $active = "configure";
    } else if (strpos($filename, "html/tools")) {
        $active = "tools";
    } else if (strpos($filename, "html/help")) {
        $active = "help";
    } else if (strpos($filename, "login.php")) {
        $active = "login";
    }

    ?>

    <!-- New Nagios XI Navbar -->
    <div class="navbar navbar-inverse">
        <div class="container-fluid">
            <ul class="nav navbar-nav pull-left">
                <?php if (is_authenticated()) { ?>
                    <li<?php if ($active == "home") {
                        echo ' class="active"';
                    } ?>><a href="<?php echo get_base_url(); ?>"><?php echo _("Home"); ?></a></li>
                    <li<?php if ($active == "views") {
                        echo ' class="active"';
                    } ?>><a href="<?php echo get_base_url(); ?>views/"><?php echo _("Views"); ?></a></li>
                    <li<?php if ($active == "dashboards") {
                        echo ' class="active"';
                    } ?>><a href="<?php echo get_base_url(); ?>dashboards/"><?php echo _("Dashboards"); ?></a>
                    </li>
                    <li<?php if ($active == "reports") {
                        echo ' class="active"';
                    } ?>><a href="<?php echo get_base_url(); ?>reports/"><?php echo _("Reports"); ?></a></li>
                    <?php if ((is_authorized_to_configure_objects() && !is_readonly_user()) || user_can_access_ccm()) { ?>
                        <li<?php if ($active == "configure") {
                            echo ' class="active"';
                        } ?>><a href="<?php echo get_base_url(); ?>config/"><?php echo _("Configure"); ?></a>
                        </li>
                    <?php
                    } ?>
                    <li<?php if ($active == "tools") { echo ' class="active"'; } ?>><a href="<?php echo get_base_url(); ?>tools/"><?php echo _("Tools"); ?></a></li>
                    <li<?php if ($active == "help") { echo ' class="active"'; } ?>><a href="<?php echo get_base_url(); ?>help/"><?php echo _("Help"); ?></a></li>
                    <?php if (is_admin()) { ?>
                        <li<?php if ($active == "admin") { echo ' class="active"'; } ?>><a href="<?php echo get_base_url(); ?>admin/"><?php echo _("Admin"); ?></a></li>
                    <?php } ?>
                <?php } else { ?>
                    <li<?php if ($active == "login") { echo ' class="active"'; } ?>>
                        <a href="<?php echo get_base_url() . PAGEFILE_LOGIN; ?>"><?php echo _("Login"); ?></a>
                    </li>
                <?php } ?>
            </ul>
            <?php if (is_authenticated()) { ?>
                <ul class="nav navbar-nav pull-right">
                    <li class="navbar-icons">
                        <div id="schedulepagereport">
                            <a href="#" class="tt-bind" title="<?php echo _('Schedule page'); ?>"><i class="fa fa-clock-o"></i></a>
                        </div>
                        <div id="permalink">
                            <a href="#" class="tt-bind" title="<?php echo _('Get permalink'); ?>"><i class="fa fa-chain"></i></a>
                        </div>
                        <div id="feedback">
                            <a href="#" class="tt-bind" title="<?php echo _('Send us feedback'); ?>"><i class="fa fa-comment"></i></a>
                        </div>
                        <div id="addtomyviews">
                            <a href="#" class="tt-bind" title="<?php echo _('Add to My Views'); ?>"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <div id="popout">
                            <a href="#" class="tt-bind" title="<?php echo _('Popout'); ?>"><i class="fa fa-share-square-o"></i></a>
                        </div>
                    </li>
                </ul>
                <form method="post" class="navbar-search pull-right" target="maincontentframe" action="<?php echo get_base_url(); ?>includes/components/xicore/status.php?show=services">
                    <input type="hidden" name="navbarsearch" value="1"/>
                    <input type="text" class="search-query" name="search" id="navbarSearchBox" value="" placeholder="<?php echo _('Search...'); ?>"/>
                </form>
            <?php } ?>
    </div>
    </div>

<?php
// Classic XI Style
} else if ($theme == 'classic') {
?>

    <div id="topmenucontainer">
        <ul class="menu">
            <?php if (is_authenticated()) { ?>
                <li><a href="<?php echo get_base_url(); ?>"><?php echo _("Home"); ?></a></li>
                <li><a href="<?php echo get_base_url(); ?>views/"><?php echo _("Views"); ?></a></li>
                <li><a href="<?php echo get_base_url(); ?>dashboards/"><?php echo _("Dashboards"); ?></a></li>
                <li><a href="<?php echo get_base_url(); ?>reports/"><?php echo _("Reports"); ?></a></li>
                <?php if ((is_authorized_to_configure_objects() && !is_readonly_user()) || user_can_access_ccm()) { ?>
                    <li><a href="<?php echo get_base_url(); ?>config/"><?php echo _("Configure"); ?></a></li>
                <?php } ?>
                <li><a href="<?php echo get_base_url(); ?>tools/"><?php echo _("Tools"); ?></a></li>
                <li><a href="<?php echo get_base_url(); ?>help/"><?php echo _("Help"); ?></a></li>
                <?php if (is_admin()) { ?>
                    <li><a href="<?php echo get_base_url(); ?>admin/"><?php echo _("Admin"); ?></a></li>
                <?php
                }
            } else {
                ?>
                <li><a href="<?php echo get_base_url() . PAGEFILE_LOGIN; ?>"><?php echo _("Login"); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php if (is_authenticated()) { ?>
    <div id="primarybuttons">
        <div id="schedulepagereport">
            <a href="#" title="<?php echo _('Schedule page'); ?>"><i class="fa fa-clock-o"></i></a>
        </div>
        <div id="permalink">
            <a href="#" title="<?php echo _('Get permalink'); ?>"><i class="fa fa-chain"></i></a>
        </div>
        <div id="feedback">
            <a href="#" title="<?php echo _('Send us feedback'); ?>"><i class="fa fa-comment"></i></a>
        </div>
    </div>
    <?php
    }
}
?>

<?php if (is_authenticated()) { display_feedback_layer(); } ?>
<div id="popup_layer">
    <div id="popup_content">
        <div id="popup_close">
            <a id="close_popup_link" style="display: inline-block;" title="<?php echo _("Close"); ?>"><i class="fa fa-times" style="font-size: 16px;"></i></a>
        </div>
        <div id="popup_container">
        </div>
    </div>
</div>

<?php if (is_authenticated()) { ?>
<!-- Display logout warning message -->
<div id="session-timeout" class="info-popup" style="text-align: center;">
    <h2><?php echo _("Your session has timed out."); ?></h2>
    <div style="line-height: 20px;"><?php echo _("You have been automatically logged out due to inactivity."); ?></div>
    <div><a href="<?php echo get_base_url(); ?>login.php" class="btn btn-xs btn-default"><?php echo _("Log In"); ?></a></div>
</div>
<script type="text/javascript">
var ACHECK = '';
$(document).ready(function() {

    // Do a check for authentication every 60 seconds
    ACHECK = setInterval(function() {
        $.ajax({
            type: 'GET',
            async: true,
            url: ajax_helper_url,
            data: { cmd: 'keepalive', nsp: nsp_str },
            success: function(data) {
                if (data == "<?php echo _("Your session has timed out."); ?>") {
                    whiteout(true);
                    $('#session-timeout').center(false);
                    $('#session-timeout').show();
                }
            }
        });
    }, 60000);

});
</script>
<?php } ?>

<?php
function display_feedback_layer()
{
    global $cfg;

    $name = get_user_attr(0, 'name');
    $email = get_user_attr(0, 'email');
    ?>
    <div id="feedback_layer">
        <div id="feedback_content">

            <div id="feedback_close">
                <a id="close_feedback_link" style="display: inline-block;" title="<?php echo _("Close"); ?>"><i class="fa fa-times" style="font-size: 16px;"></i></a>
            </div>

            <div id="feedback_container">

                <div id="feedback_header">
                    <b><?php echo _("Send Us Feedback"); ?></b>
                    <p><?php echo _("We love input!  Tell us what you think about this product and you'll directly drive future innovation!"); ?></p>
                </div>
                <!-- feedback_header -->

                <div id="feedback_data">

                    <form id="feedback_form" method="get" action="<?php echo get_ajax_proxy_url(); ?>">

                        <input type="hidden" name="proxyurl" value="<?php echo $cfg['feedback_url']; ?>">
                        <input type="hidden" name="proxymethod" value="post">

                        <label for="feedbackCommentBox"><?php echo _("Comments"); ?>:</label>
                        <textarea class="textarea form-control" name="comment" style="width: 100%; height: 100px;"></textarea>

                        <label for="feedbackNameBox"><?php echo _("Your Name (Optional)"); ?>:</label>
                        <input type="text" size="30" name="name" id="feedbackNameBox" value="<?php echo encode_form_val($name); ?>" class="textfield form-control">

                        <label for="feedbackEmailAddressBox"><?php echo _("Your Email Address (Optional)"); ?>:</label>
                        <input type="text" size="30" name="email" id="feedbackEmailAddressBox" value="<?php echo encode_form_val($email); ?>" class="textfield form-control">

                        <div>
                            <div class="fl" id="feedbackFormButtons">
                                <input type="submit" class="submitbutton btn btn-sm btn-primary" name="submitButton" value="<?php echo _("Submit"); ?>" id="submitFeedbackButton">
                            </div>
                            <div class="fr feedback-pp">
                                <a href="<?php echo $cfg["privacy_policy_url"]; ?>" target="_blank" rel="noreferrer"><?php echo _("Privacy Policy"); ?></a>
                            </div>
                            <div class="clear"></div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}


function display_pagetop_alerts()
{
    $id = "pagetopalertcontent";

    $output = ' <div id="' . $id . '"></div>

                <script type="text/javascript">

                function create_popover() {
                    $("#topalert-popover").tooltip({ placement: "left" });
                    $("#topalert-popover").popover({ html: true });
                }

                $(document).ready(function() {

                    get_' . $id . '_content();
                        
                    $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, "pagetop_alert_content") . ', "timer-' . $id . '", function(i) {
                        get_' . $id . '_content();
                    });
                    
                    function get_' . $id . '_content() {
                        $("#' . $id . '").each(function() {
                            var optsarr = {
                                "func": "get_pagetop_alert_content_html",
                                "args": ""
                            }
                            var opts = JSON.stringify(optsarr);
                            get_ajax_data_innerHTML_with_callback("getxicoreajax", opts, true, this, "create_popover");
                        });
                    }
                });
                </script>';

    echo $output;

}