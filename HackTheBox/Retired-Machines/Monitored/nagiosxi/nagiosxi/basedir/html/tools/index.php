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
    add_mytools_to_tools_menu();
    add_commontools_to_tools_menu();

    $pageopt = grab_request_var('pageopt', '');
    $url = "main.php";

    switch ($pageopt) {
        default:
            draw_page($url);
            break;
    }
}


/**
 * @param $frameurl
 */
function draw_page($frameurl)
{

    do_page_start(array("page_title" => _("Tools")), false);
    ?>
    
    <div id="leftnav">
        <?php print_menu(MENU_TOOLS); ?>
    </div>

    <div id="maincontent">
        <iframe src="<?php echo get_window_frame_url($frameurl); ?>" width="100%" frameborder="0" id="maincontentframe" name="maincontentframe" allowfullscreen>
            [<?php echo _('Your user agent does not support frames or is currently configured not to display frames'); ?>.]
        </iframe>
    </div>

    <?php
    do_page_end(false);
}


/**
 * @param int $userid
 */
function add_mytools_to_tools_menu($userid = 0)
{
    $mytools = get_mytools($userid);
    $x = 0;

    foreach ($mytools as $id => $r) {
        $x++;
        add_menu_item(MENU_TOOLS, array(
            "type" => "link",
            "title" => encode_form_val($r["name"]),
            "order" => (100 + $x),
            "opts" => array(
                "href" => "mytools.php?go=1&id=" . $id,
                "id" => "mytools-" . $id,
            )
        ));
    }
}


/**
 * @param int $userid
 */
function add_commontools_to_tools_menu($userid = 0)
{
    $ctools = get_commontools($userid);
    $x = 0;

    foreach ($ctools as $id => $r) {
        $x++;
        add_menu_item(MENU_TOOLS, array(
            "type" => "link",
            "title" => encode_form_val($r["name"]),
            "order" => (200 + $x),
            "opts" => array(
                "href" => "commontools.php?go=1&id=" . $id,
                "id" => "commontools-" . $id,
            )
        ));
    }
}