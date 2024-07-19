<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check prereqs
grab_request_vars();
check_prereqs();

route_request();

function route_request()
{
    draw_page();
}

function draw_page()
{
    $pageopt = get_pageopt("info");
    do_page_start();
?>

    <div id="leftnav">
        <?php draw_menu(); ?>
    </div>
    <div id="maincontent">
        <iframe src="<?php echo get_window_frame_url("main.php?" . $pageopt); ?>" width="100%" frameborder="0" id="maincontentframe" name="maincontentframe" allowfullscreen>
            [<?php echo _('Your user agent does not support frames or is currently configured not to display frames'); ?>.]
        </iframe>
    </div>

<?php
    do_page_end();
}

function draw_menu()
{
    $m = get_menu_items();
    draw_menu_items($m);
}

function get_menu_items()
{
    $page_path = "main.php";

    $mi = array();

    // About
    $mi[] = array(
        "type" => "menusection",
        "title" => _("About"),
        "opts" => array(
            "id" => "quickview",
            "expanded" => true
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("About") . ' ' . get_product_name(),
        "opts" => array(
            "href" => $page_path . "?=about",
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Legal Information"),
        "opts" => array(
            "href" => $page_path . "?legal",
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("License"),
        "opts" => array(
            "href" => $page_path . "?license",
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    return $mi;
}