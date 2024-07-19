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
    $pageopt = grab_request_var("pageopt", "info");
    switch ($pageopt) {
        default:
            show_page();
            break;
    }
}


function show_page()
{
    do_page_start(array("page_title" => _('Tools')), true);
?>

    <h1><?php echo _('Tools'); ?></h1>
    <p><?php echo _("Tools are utilities that you can quickly access from Nagios using your web browser.") ?></p>

    <h2><?php echo _("My Tools") ?></h2>
    <p><a href="mytools.php"><?php echo _("Manage your own personal tools") ?></a>.</p>

    <h2><?php echo _("Common Tools"); ?></h2>
    <p><a href="commontools.php"><?php echo _("Access tools pre-defined by the administrator") ?></a>.</p>

    <?php
    do_page_end(true);
    exit();
}