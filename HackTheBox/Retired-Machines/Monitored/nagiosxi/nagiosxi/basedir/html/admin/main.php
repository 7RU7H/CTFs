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

    if (is_admin() == false) {
        echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
        exit();
    }

    $pageopt = grab_request_var("pageopt", "");
    switch ($pageopt) {
        case "":
            show_admin_splash();
            break;
    }
}


function show_admin_splash()
{
    $product = get_product_name();
    do_page_start(array("page_title" => _("Admin")), true);

    ?>

    <h1><?php echo _("Administration"); ?></h1>

    <p>
        <?php echo sprintf(_("Manage your %s installation with the administrative options available to you in this section. Make sure you complete any setup tasks that are shown below before using your %s installation."), $product, $product); ?>
    </p>

    <div style="float: left; margin-right: 25px;">

        <div>
            <?php
            display_dashlet("xicore_admin_tasks", "", null, DASHLET_MODE_OUTBOARD);
            ?>
        </div>


    </div><!--left float -->


    <div style="float: left;">

        <?php
        display_dashlet("xicore_component_status", "", null, DASHLET_MODE_OUTBOARD);
        ?>

    </div><!--right float-->


    <?php

    do_page_end(true);
}
