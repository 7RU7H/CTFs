<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

// Check authentication
check_authentication();

route_request();

function route_request()
{
    draw_page();
}

function draw_page()
{
    do_page_start(array("page_id" => "home-page"));
?>

    <div id="leftnav">
        <?php print_menu(MENU_HOME); ?>
    </div>
    <div id="maincontent">
        <iframe src="<?php echo get_window_frame_url(get_base_url() . "includes/page-home-main.php"); ?>" width="100%" frameborder="0" id="maincontentframe" name="maincontentframe" allowfullscreen>
            [<?php echo _('Your user agent does not support frames or is currently configured not to display frames'); ?>.]
        </iframe>
    </div>

<?php
    do_page_end();
}