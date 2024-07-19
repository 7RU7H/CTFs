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
    add_myreports_to_reports_menu();
    $pageopt = grab_request_var("pageopt", "");
    $url = "availability.php";

    switch ($pageopt)
    {
        // Set the default amount of report records
        case 'setrecords':
            $records = grab_request_var('records', 10);
            if ($records > 0 && $records <= 100) {
                set_user_meta(0, 'report_defualt_recordlimit', $records);
            }
            break;
        
        default:
            draw_page($url);
            break;
    }
}


function draw_page($frameurl)
{
    do_page_start(array("page_title" => _('Reports')), false);
?>
    <div id="leftnav">
        <?php print_menu(MENU_REPORTS); ?>
    </div>

    <div id="maincontent">
        <iframe src="<?php echo get_window_frame_url($frameurl); ?>" width="100%" frameborder="0" id="maincontentframe" name="maincontentframe" allowfullscreen>
            [<?php echo _('Your user agent does not support frames or is currently configured not to display frames'); ?>.]
        </iframe>
    </div>

    <?php
    do_page_end(false);
}


function add_myreports_to_reports_menu($userid = 0)
{
    $myreports = get_myreports($userid);
    $x = 0;

    foreach ($myreports as $id => $r) {
        if (empty($r['dontdisplay'])) {
            $x++;
            add_menu_item(MENU_REPORTS, array(
                "type" => "link",
                "title" => encode_form_val($r["title"]),
                "order" => (100 + $x),
                "opts" => array(
                    "href" => "myreports.php?go=1&id=" . $id,
                    "id" => "myreports-" . $id,
                )
            ));
        }
    }
}