<?php
//
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
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
            show_help_page();
            break;
    }
}

function show_help_page()
{
    $product_url = get_product_portal_backend_url();

    do_page_start(array("page_title" => _('Help for Nagios XI')), true);
?>

    <h1><?php echo _('Help for Nagios XI'); ?></h1>

    <div style="width: 500px; float: left;">
        <h5 class="ul"><?php echo _('Get Help Online'); ?></h5>
        <p><?php echo _("Get help for Nagios XI online."); ?></p>
        <ul>
            <li>
                <a href='https://support.nagios.com/wiki/index.php/Nagios_XI:FAQs'><b><?php echo _("Frequently Asked Questions"); ?></b></a>
            </li>
            <li><a href='https://library.nagios.com/'><b><?php echo _("Visit the Nagios Library"); ?></b></a></li>
            <li><a href='https://support.nagios.com/forum'><b><?php echo _("Visit the Support Forum"); ?></b></a></li>
            <li><a href='https://support.nagios.com/wiki'><b><?php echo _("Visit the Support Wiki"); ?></b></a></li>
        </ul>
        <h5 class="ul"><?php echo _('More Options'); ?></h5>
        <ul>
            <li>
                <a href="<?php echo $product_url; ?>" target="_blank" rel="noreferrer"><strong><?php echo _("Learn about XI"); ?></strong></a>
                <br><?php echo _("Learn more about XI and its capabilities."); ?>
            </li>
            <li>
                <a href="<?php echo $product_url; ?>#stayinformed" target="_blank" rel="noreferrer"><strong><?php echo _("Signup for XI news"); ?></strong></a>
                <br><?php echo _("Stay informed on the latest updates and happenings for XI."); ?>
            </li>
        </ul>
    </div>

    <div style="width: 500px; float: left; margin-left: 20px;">
        <?php display_dashlet("xicore_getting_started", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

<?php
    do_page_end(true);
}