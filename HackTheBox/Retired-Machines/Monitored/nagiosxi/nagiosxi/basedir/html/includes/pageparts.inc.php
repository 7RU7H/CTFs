<?php
//
// Page Generation Library
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/utils.inc.php');
include_once(dirname(__FILE__) . '/auth.inc.php');
include_once(dirname(__FILE__) . '/components.inc.php');

// Generates the header tags and includes
function do_page_start($opts = array(), $child = false, $vue = false)
{
    define('IS_CHILD_PAGE', $child);

    // What title should be used for the page?
    $title = "";
    if (isset($opts["page_title"])) {
        $title = $opts["page_title"];
    }
    $pagetitle = get_product_name();
    if ($title != "") {
        $pagetitle = "$title &middot; " . $pagetitle;
    }

    // Recycle tour - from user account page link, this will
    // reset tours in usermeta to be accessed by helpsystem
    $rerun_tour = grab_request_var("rerun_tour", 0);
    $reset_tour_script = "";
    if ($rerun_tour) {
        $settings = array("new_user" => 0, 1000 => 0);
        set_user_meta(0, "tours", serialize($settings), false);
    }

    // Add a quickstart option
    $qs = grab_request_var("qs", '');
    if (!empty($qs)) {
        $qs = intval($qs);
        set_option('quickstart_id', $qs);
    }

    // Body ID
    $bid = "";
    $body_id = "";
    if (isset($opts["body_id"])) {
        $bid = $opts["body_id"];
    }
    if ($bid != "") {
        $body_id = ' id="'.$bid.'"';
    }

    // Body class
    $bc = "";
    $body_class = ' class="';
    if ($child == false) {
        $body_class .= "parent";
    } else {
        $body_class .= "child";
    }
    if (isset($opts["body_class"])) {
        $bc = $opts["body_class"];
    }
    if ($bc != "") {
        $body_class .= ' '.$bc;
    }

    // Check if login page
    $page = get_current_page();
    if ($page == PAGEFILE_LOGIN) {
        $body_class .= " scroll";
    }

    $body_class .= '"';

    // Body style
    $bs = "";
    $body_style = "";
    if (isset($opts["body_style"]))
        $bs = $opts["body_style"];
    if ($bs != "")
        $body_style = ' style="'.$bs.'"';

    // Page id
    $pid = "";
    $page_id = "";
    if (isset($opts["page_id"]))
        $pid = $opts["page_id"];
    if ($pid != "")
        $page_id = ' id="'.$pid.'"';

    // Page class
    $page_class = "parentpage";
    if ($child == true)
        $page_class = "childpage";
    $pc = "";
    if (isset($opts["page_class"]))
        $pc = $opts["page_class"];
    if ($pc != "")
        $page_class .= " $pc";

    $jquery_plugins = array();
    if (isset($opts['jquery_plugins'])) {
        $jquery_plugins = $opts['jquery_plugins'];
    }

    $thispage = get_current_page();

    if ($child == false) { ?>
        <!DOCTYPE html>
        <!-- <!DOCTYPE html> -->
    <?php } else { ?>
        <!DOCTYPE html>
        <!-- Produced by Nagios XI. Copyright (c) 2008-<?php echo date("Y", time()); ?> Nagios Enterprises, LLC (www.nagios.com). All Rights Reserved. -->
        <!-- Powered by the Nagios Synthesis Framework -->
    <?php } ?>
    <html>

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
        <?php if ($child == false) { ?>
        <!-- Produced by Nagios XI. Copyright (c) 2008-<?php echo date("Y", time()); ?> Nagios Enterprises, LLC (www.nagios.com). All Rights Reserved. -->
        <!-- Powered by the Nagios Synthesis Framework -->
        <?php } ?>
        <title><?php echo $pagetitle; ?></title>
        <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

        <?php if (isset($opts['mobile_compat'])) { ?>
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <?php } ?>

        <?php
        do_page_head_links($child, $jquery_plugins, $vue);
        
        $cbargs = array("child" => $child);
        do_callbacks(CALLBACK_PAGE_HEAD, $cbargs);
        ?>

        <!-- Mobile redirect -->
        <script>
        <?php if (is_authenticated()) { ?>
        var mobile_redirects_disabled = <?php echo intval(get_user_meta(0, 'mobile_redirects_disabled', 0)); ?>;
        <?php } else { ?>
        var mobile_redirects_disabled = 0;
        <?php } ?>
        check_for_mobile();
        </script>

    </head>

    <body <?php echo $body_id; ?><?php echo $body_class; ?> <?php echo $body_style; ?>>

    <?php
    do_callbacks(CALLBACK_BODY_START, $cbargs);

    // Display enterprise messages
    $theme = get_theme();
    if (isset($opts['enterprise'])) {
        if ($theme == "xi5" || $theme == "xi5dark") {
            echo enterprise_message(true);
        } else {
            echo enterprise_message();
        }
    }
    ?>

    <?php
    if ($child) {
        echo get_flash_message();
    }
    ?>

    <div <?php echo $page_id; ?> class="<?php echo $page_class; ?>">

    <div id="whiteout"></div>
    <div id="blackout"></div>

    <div <?php if ($child == false) echo 'id="header" class="parenthead" '; else echo 'id ="childheader" class="childhead" '; ?>>
        <?php
        do_page_header($child);
        ?>
        <div id="throbber" class="sk-spinner sk-spinner-center sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>

    <?php if (is_authenticated()) { do_callbacks(CALLBACK_FRAME_START, $cbargs); } ?>

    <?php
    if ($child == false) {
        ?>
        <div id="mainframe">

        <?php if (is_authenticated()) { do_callbacks(CALLBACK_CONTENT_START, $cbargs); } ?>

        <?php
        if (is_authenticated()) {
            $page = get_current_page();
            if ($page != PAGEFILE_LOGIN && $page != PAGEFILE_INSTALL && $page != PAGEFILE_UPGRADE) {
                ?>
                <div id="fullscreen" class="fs-open"></div>
            <?php
            }
        }
    }

    // Display screen dashboard in parent if someone is logged in
    if ($child == false && is_authenticated() == true) {
        $db = get_dashboard_by_id(0, SCREEN_DASHBOARD_ID);
        if ($db != null) {
            echo "<!-- SCREEN DASHBOARD START -->";
            display_dashboard_dashlets($db);
            echo "<!-- SCREEN DASHBOARD END -->";
        }
    }

    // Display renewal reminders
    if (is_authenticated() == true && $child == false) {
        if (!is_trial_license()) {
            do_maintenance_renewal_check();
        }
    }

    // Display login alerts (maybe)
    if (is_authenticated() == true && $child == false && ($thispage != "upgrade.php" && $thispage != "login.php")) {
        do_login_alert_popup();
    }

}


/**
 * @param bool $child
 */
function do_page_head_links($child = false, $jquery_plugins = array(), $vue = false)
{
    global $cfg;

    $old_browser_compat = 0;
    if (array_key_exists('old_browser_compat', $cfg)) {
        $old_browser_compat = $cfg['old_browser_compat'];
    }
    $old_browser_compat = grab_request_var("old_browser_compat", $old_browser_compat);

    $mode = grab_request_var("mode", "");

    $theme = get_theme();
    $base_url = get_base_url();
?>

    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/favicon-32x32.png" sizes="32x32">
    <link rel="shortcut icon" href="<?php echo $base_url; ?>images/favicon.ico" type="image/ico">
    <link rel="apple-touch-icon-precomposed" href="<?php echo $base_url; ?>images/apple-touch-icon-precomposed.png">
    <link rel="apple-touch-icon" href="<?php echo $base_url; ?>images/apple-touch-icon.png">

    <!-- Adding Font-Awesome for all themes -->
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>includes/css/font-awesome.min.css?<?php echo get_build_id(); ?>" />

    <!-- Global variables & Javascript translation text -->
    <script type="text/javascript">
    var base_url = "<?php echo $base_url; ?>";
    var backend_url = "<?php echo urlencode(get_backend_url(false)); ?>";
    var ajax_helper_url = "<?php echo get_ajax_helper_url(); ?>";
    var ajax_proxy_url = "<?php echo get_ajax_proxy_url(); ?>";
    var suggest_url = "<?php echo get_suggest_url(); ?>";
    var request_uri = "<?php echo urlencode($_SERVER['REQUEST_URI']); ?>";
    var demo_mode = <?php echo (in_demo_mode()) ? 1 : 0; ?>;
    var nsp_str = "<?php echo get_nagios_session_protector_id(); ?>";
    var theme = "<?php echo encode_form_valq($theme); ?>";

    // Language string for translations
    var lang = {
        'Add to Dashboard': "<?php echo encode_form_val(_('Add to Dashboard')); ?>",
        'Add Dashboard': "<?php echo encode_form_val(_('Add Dashboard')); ?>",
        'Edit Dashboard': "<?php echo encode_form_val(_('Edit Dashboard')); ?>",
        'Dashlet Title': "<?php echo encode_form_val(_('Dashlet Title')); ?>",
        'Dashboard Added': "<?php echo encode_form_val(_('Dashboard Added')); ?>",
        'Add It': "<?php echo encode_form_val(_('Add It')); ?>",
        'Add this powerful little dashlet to one of your dashboards for visual goodness.': "<?php echo encode_form_val(_('Add this powerful little dashlet to one of your dashboards for visual goodness.')); ?>",
        'Select a Dashboard to Add To': "<?php echo encode_form_val(_('Select a Dashboard to Add To')); ?>",
        'Add this graph to a dashboard.': "<?php echo encode_form_val(_('Add this graph to a dashboard.')); ?>",
        'Dashlet is now loaded on your dashboard.': "<?php echo encode_form_val(_('Dashlet is now loaded on your dashboard.')); ?>",
        'Dashlet Added': "<?php echo encode_form_val(_('Dashlet Added')); ?>",
        'Please Wait': "<?php echo encode_form_val(_('Please Wait')); ?>",
        'Submitting command': "<?php echo encode_form_val(_('Submitting command')); ?>",
        'Show Details': "<?php echo encode_form_val(_('Show Details')); ?>",
        'Hide Details': "<?php echo encode_form_val(_('Hide Details')); ?>",
        'Show password': "<?php echo encode_form_val(_('Show password')); ?>",
        'Hide password': "<?php echo encode_form_val(_('Hide password')); ?>",
        'Permalink': "<?php echo encode_form_val(_('Permalink')); ?>",
        'Copy the URL below to retain a direct link to your current view.': "<?php echo encode_form_val(_('Copy the URL below to retain a direct link to your current view.')); ?>",
        'URL': "<?php echo encode_form_val(_('URL')); ?>",
        'Thank You!': "<?php echo encode_form_val(_('Thank You!')); ?>",
        'Thanks for helping to make this product better! We will review your comments as soon as we get a chance. Until then, kudos to you for being awesome and helping drive innovation!': "<?php echo encode_form_val(_('Thanks for helping to make this product better! We will review your comments as soon as we get a chance. Until then, kudos to you for being awesome and helping drive innovation!')); ?>",
        'Error': "<?php echo encode_form_val(_('Error')); ?>",
        'An error occurred. Please try again later.': "<?php echo encode_form_val(_('An error occurred. Please try again later.')); ?>",
        'Sending Feedback': "<?php echo encode_form_val(_('Sending Feedback')); ?>",
        'Use this to add a new dashboard to your Dashboards page.': "<?php echo encode_form_val(_('Use this to add a new dashboard to your Dashboards page.')); ?>",
        'Dashboard Title': "<?php echo encode_form_val(_('Dashboard Title')); ?>",
        'Background Color': "<?php echo encode_form_val(_('Background Color')); ?>",
        'Submit': "<?php echo encode_form_val(_('Submit')); ?>",
        'Processing': "<?php echo encode_form_val(_('Processing')); ?>",
        'Success! Your new dashboard has been added.': "<?php echo encode_form_val(_('Success! Your new dashboard has been added.')); ?>",
        'An error occurred processing your request.': "<?php echo encode_form_val(_('An error occurred processing your request.')); ?>",
        'Dashboard Changes Saved': "<?php echo encode_form_val(_('Dashboard Changes Saved')); ?>",
        'Success! Your dashboard was updated successfully.': "<?php echo encode_form_val(_('Success! Your dashboard was updated successfully.')); ?>",
        'You cannot delete your home page dashboard.': "<?php echo encode_form_val(_('You cannot delete your home page dashboard.')); ?>",
        'Confirm Dashboard Deletion': "<?php echo encode_form_val(_('Confirm Dashboard Deletion')); ?>",
        'Are you sure you want to delete this dashboard and all dashlets it contains?': "<?php echo encode_form_val(_('Are you sure you want to delete this dashboard and all dashlets it contains?')); ?>",
        'Delete': "<?php echo encode_form_val(_('Delete')); ?>",
        'Cancel': "<?php echo encode_form_val(_('Cancel')); ?>",
        'The requested dashboard has been deleted.': "<?php echo encode_form_val(_('The requested dashboard has been deleted.')); ?>",
        'Dashboard Deleted': "<?php echo encode_form_val(_('Dashboard Deleted')); ?>",
        'Clone Dashboard': "<?php echo encode_form_val(_('Clone Dashboard')); ?>",
        'Use this to make an exact clone of the current dashboard and all its wonderful dashlets.': "<?php echo encode_form_val(_('Use this to make an exact clone of the current dashboard and all its wonderful dashlets.')); ?>",
        'Clone': "<?php echo encode_form_val(_('Clone')); ?>",
        'New Title': "<?php echo encode_form_val(_('New Title')); ?>",
        'Dashboard Cloned': "<?php echo encode_form_val(_('Dashboard Cloned')); ?>",
        'Dashboard successfully cloned.': "<?php echo encode_form_val(_('Dashboard successfully cloned.')); ?>",
        'Deleting dashlets from the home page dashboard is disabled while in demo mode.': "<?php echo encode_form_val(_('Deleting dashlets from the home page dashboard is disabled while in demo mode.')); ?>",
        'Dashlet Deleted': "<?php echo encode_form_val(_('Dashlet Deleted')); ?>",
        'Dashlet removed from dashboard.': "<?php echo encode_form_val(_('Dashlet removed from dashboard.')); ?>",
        'The dashlet has been added and will now show up on your dashboard.': "<?php echo encode_form_val(_('The dashlet has been added and will now show up on your dashboard.')); ?>",
        'Masquerade Notice': "<?php echo encode_form_val(_('Masquerade Notice')); ?>",
        'You are about to masquerade as another user. If you choose to continue you will be logged out of your current account and logged in as the selected user. In the process of doing so, you may lose your admin privileges.': "<?php echo encode_form_val(_('You are about to masquerade as another user. If you choose to continue you will be logged out of your current account and logged in as the selected user. In the process of doing so, you may lose your admin privileges.')); ?>",
        'Continue': "<?php echo encode_form_val(_('Continue')); ?>",
        'Add View': "<?php echo encode_form_val(_('Add View')); ?>",
        'Use this to add what you see on the screen to your views page.': "<?php echo encode_form_val(_('Use this to add what you see on the screen to your views page.')); ?>",
        'View Title': "<?php echo encode_form_val(_('View Title')); ?>",
        'View Added': "<?php echo encode_form_val(_('View Added')); ?>",
        'Success! Your view was added to your views page.': "<?php echo encode_form_val(_('Success! Your view was added to your views page.')); ?>",
        'View Deleted': "<?php echo encode_form_val(_('View Deleted')); ?>",
        'View has been removed.': "<?php echo encode_form_val(_('View has been removed.')); ?>",
        'Edit View': "<?php echo encode_form_val(_('Edit View')); ?>",
        'View Changes Saved': "<?php echo encode_form_val(_('View Changes Saved')); ?>",
        'Success! Your view was updated successfully.': "<?php echo encode_form_val(_('Success! Your view was updated successfully.')); ?>",
        'Start Rotation': "<?php echo encode_form_val(_('Start Rotation')); ?>",
        'Stop Rotation': "<?php echo encode_form_val(_('Stop Rotation')); ?>",
        'Pause rotation': "<?php echo encode_form_val(_('Pause rotation')); ?>",
        'Resume rotation': "<?php echo encode_form_val(_('Resume rotation')); ?>",
        'You are about to delete the view': "<?php echo encode_form_val(_('You are about to delete the view')); ?>",
        'Cannot schedule outside pages.': "<?php echo encode_form_val(_('Cannot schedule outside pages.')); ?>",
        'Any page not under nagiosxi cannot be scheduled.': "<?php echo encode_form_val(_('Any page not under nagiosxi cannot be scheduled.')); ?>",
        'Loading': "<?php echo encode_form_val(_('Loading')); ?>",
        'Update': "<?php echo encode_form_val(_('Update')); ?>",
        'Close': "<?php echo encode_form_val(_('Close')); ?>",
        'Time Range': "<?php echo encode_form_val(_('Time Range')); ?>",
        'Last 4 Hours': "<?php echo encode_form_val(_('Last 4 Hours')); ?>",
        'Last 24 Hours': "<?php echo encode_form_val(_('Last 24 Hours')); ?>",
        'Last Week': "<?php echo encode_form_val(_('Last Week')); ?>",
        'Last Month': "<?php echo encode_form_val(_('Last Month')); ?>",
        'Last Year': "<?php echo encode_form_val(_('Last Year')); ?>",
        'Last 7 Days': "<?php echo encode_form_val(_('Last 7 Days')); ?>",
        'Last 30 Days': "<?php echo encode_form_val(_('Last 30 Days')); ?>",
        'Last 365 Days': "<?php echo encode_form_val(_('Last 365 Days')); ?>",
        'My Graph': "<?php echo encode_form_val(_('My Graph')); ?>",
        'You must fill out the entire form.': "<?php echo encode_form_val(_('You must fill out the entire form.')); ?>",
        'Copy to Clipboard': "<?php echo encode_form_val(_('Copy to Clipboard')); ?>",
        'Copied': "<?php echo encode_form_val(_('Copied')); ?>",
        'Press Ctrl+C to copy': "<?php echo encode_form_val(_('Press Ctrl+C to copy')); ?>",
        'Dismiss' : "<?php echo encode_form_val(_('Dismiss')); ?>"
    };

    // Translation helper function
    function _(str) {
        var trans = lang[str];
        if (trans) { return trans; }
        return str;
    }
    </script>

    <!-- main jquery libraries -->
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-3.6.0.min.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-migrate-3.0.0.min.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-migrate-1.4.1.min.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.checkboxes.js?<?php echo get_build_id(); ?>'></script>
    <?php if ($old_browser_compat) {
        if (get_option('old_browser_compat_jquery1', 1)) { ?>
        <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-1.12.4.min.js?<?php echo get_build_id(); ?>'></script>
        <?php } else { ?>
        <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-3.x.compat.js?<?php echo get_build_id(); ?>'></script>
    <?php } } ?>
    <link type="text/css" href="<?php echo $base_url; ?>includes/js/jquery/css/smoothness/jquery-ui.custom.min.css?<?php echo get_build_id(); ?>" rel="stylesheet"/>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.colorBlend.js'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.timers-1.1.3.js'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-ui-1.12.1.custom.min.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery-ui-timepicker-addon.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.searchabledropdown.custom.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.checkboxes.js?<?php echo get_build_id(); ?>'></script>

    <?php
    if ($vue) {
        if (is_dev_mode()) { ?>
        <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/vue.js?<?php echo get_build_id(); ?>'></script>
        <?php } else { ?>
        <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/vue.min.js?<?php echo get_build_id(); ?>'></script>
    <?php } } ?>

    <link rel="stylesheet" href="<?php echo $base_url; ?>includes/css/bootstrap.3.min.css?<?php echo get_build_id(); ?>" type="text/css" />
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/bootstrap.3.min.js?<?php echo get_build_id(); ?>'></script>

    <!-- spin kit -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>includes/css/spinkit.css?<?php echo get_build_id(); ?>" type="text/css" />

    <!-- jquery autocomplete -->
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/jquery.autocomplete.css'/>

    <!-- colorpicker -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>includes/js/jquery/colorpicker/css/colorpicker.css" type="text/css" />
    <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/jquery/colorpicker/js/colorpicker.js"></script>

    <!-- clipboard plugin -->
    <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/clipboard.min.js"></script>

    <?php if (in_array('sumoselect', $jquery_plugins)) { ?>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/jquery/jquery.sumoselect.min.js?<?php echo get_build_id(); ?>'></script>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/sumoselect.css?<?php echo get_build_id(); ?>'>
    <?php } ?>

    <!-- XI JS Scripts -->
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/core.js?<?php echo get_build_id(); ?>'></script>
    <?php if ($theme == "xi5" || $theme == "xi5dark") { ?>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/themes/modern.js?<?php echo get_build_id(); ?>'></script>
    <?php } else if ($theme == "xi2014") { ?>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/themes/2014.js?<?php echo get_build_id(); ?>'></script>
    <?php } else { ?>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/themes/classic.js?<?php echo get_build_id(); ?>'></script>
    <?php } ?>
    
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/commands.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/views.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/dashboards.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/dashlets.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/tables.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/users.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/perfdata.js?<?php echo get_build_id(); ?>'></script>
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/wizards.js?<?php echo get_build_id(); ?>'></script>

    <!-- XI CSS -->
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/base.css?<?php echo get_build_id(); ?>' />
    <?php if ($theme == "xi5" || $theme == "xi5dark") { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/themes/modern.css?<?php echo get_build_id(); ?>' />
    <?php if (is_dark_theme()) { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/themes/modern-dark.css?<?php echo get_build_id(); ?>' />
    <?php } ?>
    <?php } else if ($theme == "xi2014") { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/themes/2014.css?<?php echo get_build_id(); ?>' />
    <?php } else { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/themes/classic.css?<?php echo get_build_id(); ?>' />
    <?php } ?>

    <!-- Highcharts Graphing Library -->
    <?php
    if (file_exists(get_base_dir() . "/includes/js/highcharts/highcharts.js")) {
        // New Highcharts location
        echo '<script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/highcharts.js?' . get_build_id() . '"></script>
            <script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/highcharts-more.js?' . get_build_id() . '"></script>
            <script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/modules/exporting.js?' . get_build_id() . '"></script>
            <script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/modules/no-data-to-display.js?' . get_build_id() . '"></script>';
        if (get_option("default_highcharts_theme") == 'gray') {
            if (strpos($_SERVER['SCRIPT_FILENAME'], "html/reports") === false || $theme == "xi5dark") {
                echo '<script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/themes/gray.js?' . get_build_id() . '"></script>';
            }
        } else if ($theme == "xi5dark" && $mode != "getreport") {
            echo '<script type="text/javascript" src="' . get_base_url() . '/includes/js/highcharts/themes/gray.js?' . get_build_id() . '"></script>';
        }
    }
    ?>

    <!-- D3 Graphing Library -->
    <script type='text/javascript' src='<?php echo $base_url; ?>includes/js/d3/d3.v3.min.js?<?php echo get_build_id(); ?>'></script>

    <?php if (!$child) { ?>

        <!-- jScrollPane -->
        <link type="text/css" href="<?php echo $base_url; ?>includes/js/jquery/css/jquery.jscrollpane.css" rel="stylesheet" media="all" />
        <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/jquery/jquery.jscrollpane.min.js"></script>

    <?php
    }

    // Include css/js stuff for dashlets
    echo get_dashlets_pagepart_includes();
}


/**
 * @param $child
 */
function do_page_header($child)
{
    $cbargs = array("child" => $child);
    do_callbacks(CALLBACK_HEADER_START, $cbargs);

    if ($child == true) {
        include_once(dirname(__FILE__) . '/header-child.inc.php');
    } else {
        include_once(dirname(__FILE__) . '/header.inc.php');
    }

    do_callbacks(CALLBACK_HEADER_END, $cbargs);
}


/**
 * Adds footer and analytics to the end of the page
 * @param bool $child
 */
function do_page_end($child = false)
{
    $cbargs = array("child" => $child);

    do_callbacks(CALLBACK_CONTENT_END, $cbargs);

    if ($child == false) {
?>
    </div><!--mainframe-->

    <?php
    }

    do_page_footer($child);
    ?>

    </div><!--page-->

    <noframes>
        <!-- This page requires a web browser which supports frames. -->
        <h2><?php echo get_product_name(); ?></h2>
        <p align="center">
            <a href="https://www.nagios.com/">www.nagios.com</a><br>
            Copyright (c) 2009-<?php echo date("Y", time()); ?> Nagios Enterprises, LLC<br>
        </p>
        <p>
            <i>Note: These pages require a browser which supports frames</i>
        </p>
    </noframes>

    <?php do_callbacks(CALLBACK_BODY_END, $cbargs); ?>

    </body>
</html>

<?php
}


/**
 * @param $child
 */
function do_page_footer($child)
{
    $cbargs = array("child" => $child);

    do_callbacks(CALLBACK_FOOTER_START, $cbargs);

    if ($child === true) {
        include_once(dirname(__FILE__) . '/footer-child.inc.php');
    } else {
        include_once(dirname(__FILE__) . '/footer.inc.php');
    }

    do_callbacks(CALLBACK_FOOTER_END, $cbargs);
}


/**
 * Displays page feedback in a formated box
 *
 * @param bool   $error
 * @param bool   $info
 * @param string $msg
 * @param bool   $echo
 *
 * @return string
 */
function display_message($error = true, $info = true, $msg = "", $echo = true)
{
    if ($echo) {
        echo get_message_text($error, $info, $msg);
    } else {
        return get_message_text($error, $info, $msg);
    }
}


/**
 * @param $default
 *
 * @return string
 */
function get_window_frame_url($default)
{
    // Default window url may have been overridden with a permalink...
    $xiwindow = grab_request_var("xiwindow", "");
    if ($xiwindow != "") {
        $rawurl = urldecode($xiwindow);
    } else {
        $rawurl = $default;
    }

    // Parse url and remove permalink option from base
    $a = parse_url($rawurl);
    $a ['scheme'] = isset($a['scheme']) ? $a['scheme'] : 'http';

    // Remove bad characters from the url path just to be safe
    $a['path'] = str_replace(array(':', '(', ')'), '', $a['path']);
    if ($a['scheme'] == 'javascript') {
        $a['scheme'] = 'http';
    }

    // Build base url
    if (isset($a["host"])) {
        if (isset($a["port"]) && $a["port"] != "80") {
            $windowurl = $a["scheme"] . "://" . $a["host"] . ":" . $a["port"] . $a["path"] . "?";
        } else {
            $windowurl = $a["scheme"] . "://" . $a["host"] . $a["path"] . "?";
        }
    } else {
        $windowurl = $a["path"] . "?";
    }

    $q = "";
    if (isset($a["query"])) {
        $q = $a["query"];
    }

    $pairs = explode("&", $q);
    foreach ($pairs as $pair) {
        $v = explode("=", $pair);
        if (is_array($v)) {
            $windowurl .= "&" . urlencode($v[0]) . "=" . urlencode(isset($v[1]) ? $v[1] : "");
        }
    }

    return encode_form_valq($windowurl);
}

function do_login_alert_popup()
{

    // Display login alerts if they haven't seen it already
    if (isset($_SESSION["has_seen_login_alerts"]) && $_SESSION["has_seen_login_alerts"] == true) {
        return;
    }
    $_SESSION["has_seen_login_alerts"] = true;

    // User has alert screen disabled
    $show = get_user_meta(0, "show_login_alert_screen", 1);
    if (empty($show)) {
        return;
    }
?>

    <div id="login_alert_popup" style="visibility: hidden;">

        <div id="close_login_alert_popup" style="float: right;">
            <a id="close_login_alert_popup_link" href="#">
                <i class="fa fa-times" title="<?php echo _('Close'); ?>"></i>
            </a>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                $(window).resize(function() {
                    $('#login_alert_popup').center();
                });
                $("#login_alert_popup").each(function () {
                    $(this).draggable();
                });
                $("#close_login_alert_popup_link").click(function () {
                    $("#login_alert_popup").css("display", "none");
                    clear_whiteout();
                });
            });
        </script>


        <h1><img src='<?php echo theme_image("message_bubble.png"); ?>'> <?php echo _("Notices"); ?></h1>

        <p><?php echo _("Some important information you should be aware of is listed below.") ?></p>

        <div id="login_alert_popup_content">
        </div>

        <div id="no_login_alert_popup" style="float: right; clear: right;">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="no_login_alert_popup_cb" name="no_login_alert_popup_cb" checked="checked"/>
                    <?php echo _("Show these alerts when I login"); ?>
                </label>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {

                get_login_alert_popup_content();

                $("#no_login_alert_popup_cb").click(function () {
                    checked = 0;
                    if ($(this).is(":checked")) {
                        checked = 1;
                    }
                    var optsarr = {
                        "keyname": "show_login_alert_screen",
                        "keyvalue": checked,
                        "autoload": false
                    };
                    var opts = JSON.stringify(optsarr);
                    var result = get_ajax_data("setusermeta", opts);

                });
            });

            // Show the login alert popup only if we have some alerts!
            function display_login_alert_popup_content(edata) {
                data = unescape(edata);
                if (data == "") {
                    $("#login_alert_popup").css("visibility", "hidden");
                } else {
                    whiteout();
                    $("#login_alert_popup").css("visibility", "visible");
                    center_login_alert_popup();
                }
            }

            function get_login_alert_popup_content() {
                $("#login_alert_popup_content").each(function () {
                    var optsarr = {
                        "func": "get_login_alert_popup_html",
                        "args": ""
                    }
                    var opts = JSON.stringify(optsarr);
                    get_ajax_data_innerHTML_with_callback("getxicoreajax", opts, true, this, "display_login_alert_popup_content");
                });
            }
        </script>

    </div>
<?php
}
