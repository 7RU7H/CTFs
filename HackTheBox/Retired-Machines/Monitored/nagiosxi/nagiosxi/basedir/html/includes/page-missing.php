<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');


route_request();

function route_request()
{
    show_page();
}

/**
 * @param bool   $error
 * @param string $msg
 */
function show_page($error = false, $msg = "")
{

    // page start
    do_page_start(_("Missing Page"));
    ?>
    <h1><?php echo _("What the..."); ?></h1>

    <p>
        <?php echo _("The page you requested seems to be missing.  It is theoretically possible - though highly unlikely - that we are to blame for this.  It is far more likely that something is wrong with the Universe.  Run for it!"); ?>
    </p>

    <p>
        <?php echo _("The page that went missing was: "); ?>: <?php echo grab_request_var("page"); ?>
    </p>
    <?php
    do_page_end();
}