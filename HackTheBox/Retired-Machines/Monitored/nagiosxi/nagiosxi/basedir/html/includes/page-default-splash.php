<?php
//
// Default "Home Dashboard" on user login.
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check prereqs/auth
grab_request_vars();
check_prereqs();
check_authentication(false);

page_route();

function page_route()
{
    $getfeed = grab_request_var("getfeed", "");

    if ($getfeed != "") {
        do_fetch_content($getfeed);
    } else {
        do_page();
    }
}

/**
 * Fetch the news feeds for the main page from the RSS handler.
 *
 * @param $feed
 */
function do_fetch_content($feed)
{
    // XI rss handler include
    require_once(dirname(__FILE__) . '/utils-rss.inc.php');

    switch ($feed) {

        case "techsupport":

            $html = "";
            $html .= "<div style='float: left; width: 270px;'>";
            $html .= "<h3>"._('Support Options')."</h3>";
            $html .= "<ul>";
            $html .= "
            <li><a href='https://support.nagios.com/forum' target='_blank'>"._('Online Support Forum')."</a></li>
            <li><a href='https://support.nagios.com/forum/viewforum.php?f=16' target='_blank'>"._('Customer Support Forum')."</a></li>
            <li><a target='_blank' href='https://support.nagios.com/tickets/''>"._('Customer Ticket Support Center')."</a></li>
            <li>"._('Customer Phone Support').": +1 651-204-9102 Ext. 4</li>
            </ul>";

            $html .= "<h3>" . _("Documentation and Tutorials") . "</h3>";
            $html .= "<ul>";
            if (is_admin() == true) {
                $html .= "<li><a href='https://assets.nagios.com/downloads/nagiosxi/guides/administrator/' target='_blank'>" . _("Administrator Guide") . "</a></li>";
            }
            $html .= "
            <li><a href='https://assets.nagios.com/downloads/nagiosxi/guides/user/' target='_blank'>" . _("User Guide") . "</a></li>
            <li><a href='https://library.nagios.com/library/products/nagiosxi/tutorials' target='_blank'>" . _("Video Tutorials") . "</a></li>
            <li><a href='https://library.nagios.com/library/products/nagiosxi/documentation' target='_blank'>" . _("Documentation and HOWTOs") . "</a></li>
            <li><a href='https://support.nagios.com/wiki/index.php/Nagios_XI:FAQs' target='_blank'>" . _("FAQs") . "</a></li>
            </ul>";
            $html .= "</div>";

            $html .= "<div style='float: left; margin-left: 10px; padding-bottom: 10px; width: 220px;'>";
            $html .= "<h3>" . _("We're Here To Help!") . "</h3>";
            $html .= "<p>" . _("Our knowledgeable techs are happy to help you with any questions or problems you may have getting Nagios up and running.") . "</p>";

            if (is_trial_license()) {
                $html .= "<h3>" . _("Free Quickstart Services") . "</h3>";
                $html .= "<p>" . _("Our techs can help you get up and running quickly with Nagios XI so you can get the most out of your evaluation period.") . "</p>";
                $html .= "<p>" . _("Click the link below to request free quickstart services.") . "</p>";
                $html .= "<a href='https://go.nagios.com/xi-quickstart-request' target='_blank'><b>" . _("Request Quickstart Services") . "</b></a>";
            } else {
                $html .= "<h3>" . _("Nagios Demos and Webinars") . "</h3>";
                $html .= "<p>" . _("Our techs can demonstrate the latest features of Nagios XI and show you how to make the most of your IT monitoring environment.") . "</p>";
                $html .= "<p>" . _("Click the link below to request a demo.") . "</p>";
                $html .= "<a href='https://go.nagios.com/xi-demo-request' target='_blank'><b>" . _("Request A Demo") . "</b></a>";
            }

            $html .= "</div>";

            $html .= "<div style='float: left; margin-left: 10px; width: 210px;'>";
            $html .= "<img src='" . theme_image("techsupport-splash.png") . "' style='padding: 10px;'>";
            $html .= "<br>";

            $html .= "<h3>" . _("Connect With Us") . "</h3>";

            $html .= '  <a target="_blank" rel="noreferrer" href="http://www.facebook.com/pages/Nagios/194145247332208">
    <img title="Facebook" src="' . get_base_url() . 'images/social/facebook-32x32.png">
    </a>
    <a target="_blank" rel="noreferrer" href="http://twitter.com/#%21/nagiosinc">
    <img title="Twitter" src="' . get_base_url() . 'images/social/twitter-32x32.png">
    </a>
    <a target="_blank" rel="noreferrer" href="http://www.youtube.com/nagiosvideo">
    <img title="YouTube" src="' . get_base_url() . 'images/social/youtube-32x32.png">
    </a>
    <a target="_blank" rel="noreferrer" href="http://www.linkedin.com/groups?gid=131532">
    <img title="LinkedIn" src="' . get_base_url() . 'images/social/linkedin-32x32.png">
    </a>
    <a target="_blank" rel="noreferrer" href="http://www.flickr.com/photos/nagiosinc">
    <img title="Flickr" src="' . get_base_url() . 'images/social/flickr-32x32.png">
    </a>';

            $html .= "</div>";

            print $html;
            break;

        case "xipromo":

            $url = "https://api.nagios.com/feeds/xipromo/";
            $rss = xi_fetch_rss($url);
            if ($rss) {
                $x = 0;
                $html = "
                <ul>\n";

                foreach ($rss as $item) {
                    $x++;
                    if ($x > 5)
                        break;
                    $summary = strval($item->description);
                    $html .= "<li>" . $summary . "</li>";
                }
                $html .= '
                </ul>';

                print $html;
            } else {
                $html = _("Stay on top of what our development team is up to by visiting") .
                    ":<br /><a href='https://labs.nagios.com/' target='_blank'>https://labs.nagios.com/</a>.";
                print $html;
            }
            break;

        case "labs":

            $url = "https://labs.nagios.com/feed";
            $rss = xi_fetch_rss($url);
            if ($rss) {
                $x = 0;
                $html = "
                <ul>\n";

                foreach ($rss as $item) {
                    $x++;
                    if ($x > 5)
                        break;
                    $href = strval($item->link);
                    $title = strval($item->title);
                    $html .= "<li><a href='$href' target='_blank'>" . htmlentities($title, ENT_COMPAT, 'UTF-8') . "</a></li>";
                }
                $html .= '
                <li><a href="https://labs.nagios.com/" target="_blank" rel="noreferrer">' . _("More blog posts") . '...</a></li>
                </ul>';

                print $html;
            } else {
                $html = _("Stay on top of what our development team is up to by visiting") . ":<br />
                <a href='https://labs.nagios.com/' target='_blank'>https://labs.nagios.com/</a>.
                ";
                print $html;
            }
            break;

        case "library":

            $url = "https://library.nagios.com/library/products/nagiosxi/documentation?format=feed&type=rss";
            $rss = xi_fetch_rss($url);
            if ($rss) {
                $x = 0;
                $html = "
                <ul>\n";

                foreach ($rss as $item) {
                    $x++;
                    if ($x > 5)
                        break;
                    $href = strval($item->link);
                    $title = strval($item->title);
                    $html .= "<li><a href='$href' target='_blank'>" . htmlentities($title, ENT_COMPAT, 'UTF-8') . "</a></li>";
                }
                $html .= '
                <li><a href="https://library.nagios.com/" target="_blank" rel="noreferrer">' . _("More tutorials") . '...</a></li>
                </ul>';

                print $html;
            } else {
                $html = _("Stay on top of new documentation by visiting") . ":<br />
                <a href='https://library.nagios.com/' target='_blank'>https://library.nagios.com/</a>.";
                print $html;
            }
            break;
        default:
            //echo "This is $feed";
            break;
    }
}


function do_page()
{
    $page_title = "Nagios XI";
    do_page_start(array("page_title" => $page_title), true);
?>

    <script type='text/javascript'>
        // RSS fetch by ajax to reduce page load time
        $(document).ready(function () {
            $('#techsupport-contents').load('?getfeed=techsupport');
            $('#xipromo-contents').load('?getfeed=xipromo');
            $('#libraryfeed-contents').load('?getfeed=library');
            $('#labsfeed-contents').load('?getfeed=labs');
        });
    </script>

    <style type="text/css">
        #leftcol { float: left; width: 50%; padding-right: 20px; }
        #rightcol { float: left; width: 50%; }

        #xipromo { margin-top: 20px; }
        #xipromo li, #libraryfeed li, #labsfeed li { clear: left; list-style: none; }
        #xipromo li { padding-bottom: 10px; }
        #xipromo ul, #libraryfeed ul, #labsfeed ul { padding: 0 5px; margin: 5px 0; }

        #techsupport ul { padding: 0 5px; margin: 5px 10px; }
        #techsupport h3 { margin: 8px 4px 4px; font-size: 1.15rem; font-weight: bold; }

        #libraryfeed, #labsfeed { margin-bottom: 20px; }
        
        .support-list { margin: 0; padding: 0 20px; list-style-type: none; }
        .support-list li { margin-bottom: 2px; }
        .support-list li:last-child { margin-bottom: 0; }
    </style>

    <h1 class="home-title"><?php echo _('Home Dashboard'); ?> <a href="/nagiosxi/includes/components/homepagemod/useropts.php" class="tt-bind" data-placement="right" style="font-size: 16px;" title="<?php echo _('Change my default home page'); ?>"><i class="fa fa-cog" style="vertical-align: text-top;"></i></a></h1>

    <div class="container-fluid" style="margin: 10px -10px;">
        <div class="row">
            
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4 span6">
                <div class="well">
                    <div class="homepage-box-bg">
                        <?php display_dashlet("xicore_getting_started", "", null, DASHLET_MODE_OUTBOARD); ?>
                    </div>
                </div>
                <?php if (is_admin()) { ?>
                <div class="well">
                    <div class="homepage-box-bg">
                        <?php display_dashlet("xicore_admin_tasks", "", null, DASHLET_MODE_OUTBOARD); ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 span6">
                <div class="well">
                    <div class="homepage-box-bg">
                        <?php display_dashlet("xicore_host_status_summary", "", null, DASHLET_MODE_OUTBOARD); ?>
                    </div>
                </div>
                <div class="well">
                    <div class="homepage-box-bg">
                        <?php display_dashlet("xicore_service_status_summary", "", null, DASHLET_MODE_OUTBOARD); ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-5">
                <div class="well" style="overflow: hidden;">
                    <div style="display: table;">
                        <div style="display: table-cell; vertical-align: top; padding-right: 20px;">
                            <h3 style="font-size: 16px; margin: 0 0 10px 0;"><?php echo _("We're Here To Help!"); ?></h3>
                            <p><?php echo _('Our knowledgeable techs are happy to help you with any questions or problems you may have getting Nagios up and running.'); ?></p>
                            <ul class="support-list">
                                <li><i style="font-size: 12px;" class="fa fa-fw fa-users"></i> <a href="https://support.nagios.com/forum/"><?php echo _('Support Forum'); ?></a> / <a href="https://support.nagios.com/forum/viewforum.php?f=16"><?php echo _('Customer Support Forum'); ?></a></li>
                                <li><i style="font-size: 12px;" class="fa fa-fw fa-question-circle"></i> <a href="<?php echo get_base_url(); ?>help" target="_top"><?php echo _('Help Resources'); ?></a></li>
                                <li><i style="font-size: 12px;" class="fa fa-fw fa-sticky-note"></i> <a target="_blank" href="https://support.nagios.com/tickets/"><?php echo _('Customer Ticket Support Center'); ?></a></li>
                                <li><i style="font-size: 12px;" class="fa fa-fw fa-phone"></i>  <?php echo _('Customer Phone Support'); ?>: +1 651-204-9102 Ext. 4</li>
                            </ul>
                        </div>
                        <div style="display: table-cell; vertical-align: top;">
                            <div class="ts-splash"></div>
                        </div>
                    </div>
                </div>
                <?php if (is_admin() || is_authorized_to_configure_objects()) { ?>
                <div class="well">
                    <h3 style="font-size: 16px; margin: 0 0 5px 0;"><?php echo _("Start Monitoring"); ?></h3>
                    <div class="container-fluid">
                        <div class="row" style="margin: 0 -25px;">
                            <div class="col-lg-12 col-xl-4">
                                <a href="<?php echo get_base_url().'?xiwindow=config/monitoringwizard.php'; ?>" target="_top" class="sm-link">
                                    <img src="<?php echo theme_image('config-wizard.png'); ?>">
                                    <span><?php echo _('Run a Config Wizard'); ?></span>
                                </a>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <a href="<?php echo get_base_url().'?xiwindow=includes/components/autodiscovery/'; ?>" target="_top" class="sm-link">
                                    <img src="<?php echo wizard_logo('autodiscovery.png'); ?>">
                                    <span><?php echo _('Run Auto-Discovery'); ?></span>
                                </a>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <?php if (is_advanced_user()) { ?>
                                <a href="<?php echo get_base_url().'includes/components/ccm/xi-index.php'; ?>" target="_top" class="sm-link" style="padding-top: 14px;">
                                    <img src="<?php echo get_base_url(); ?>includes/components/ccm/images/ccm.png" style="width: 70px;">
                                    <span><?php echo _('Advanced Config'); ?></span>
                                </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                } 
                if (is_trial_license()) {
                    $d = _('days');
                    if (get_trial_days_left() == 1) { $d = _('day'); }
                    $pricing_url = "https://www.nagios.com/products/nagios-xi/#pricing";
                    if (is_v2_license()) {
                        $pricing_url = "https://" . CLOUD_HOSTNAME . "/pricing";
                    }
                ?>
                <div>
                    <div class="container-fluid">
                        <div class="row" style="margin: 0 -25px;">
                            <div class="col-lg-12 col-xl-6">
                                <div class="well support-links">
                                    <h3 style="font-size: 16px; margin: 0 0 10px 0;"><?php echo _('Demos and Webinars'); ?></h3>
                                    <p style="padding: 0;"><?php echo _('Our techs can demonstrate the latest features of Nagios XI and show you how to make the most of your IT monitoring environment.'); ?></p>
                                    <a href="https://www.nagios.com/events/webinars/" target="_blank" rel="noreferrer" class="btn btn-xs btn-info"><?php echo _('View Webinar'); ?> <i class="fa fa-external-link r"></i></a>
									<a href="https://www.nagios.com/services/quickstart/nagios-xi/" target="_blank" rel="noreferrer" class="btn btn-xs btn-info"><?php echo _('Quickstart Session'); ?> <i class="fa fa-external-link r"></i></a>
                                    <a href="https://www.nagios.com/request-demo/" target="_blank" rel="noreferrer" class="btn btn-xs btn-default"><?php echo _('Request a Demo'); ?> <i class="fa fa-external-link r"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="well support-links">
                                    <h3 style="font-size: 16px; margin: 0 0 10px 0;"><?php echo _('Features and Pricing'); ?></h3>
                                    <p class="days-left" style="background-color: <?php if (get_trial_days_left() == 0) { echo '#FF9999'; } else { echo '#CFEBF7'; } ?>; border: 1px solid <?php if (get_trial_days_left() == 0) { echo '#c69'; } else { echo '#2580B2'; } ?>;"><?php echo get_trial_days_left() . ' ' . $d . ' ' . _('left in trial'); ?></p>
                                    <p><?php echo _('View pricing or a summary of all features included in Nagios XI and Nagios XI Enterprise edition.'); ?></p>
                                    <a href="<?php echo $pricing_url; ?>" target="_blank" rel="noreferrer" class="btn btn-xs btn-info"><?php echo _('View Pricing'); ?> <i class="fa fa-external-link r"></i></a>
                                    <a href="https://www.nagios.com/products/nagios-xi/edition-comparison/" target="_blank" rel="noreferrer" class="btn btn-xs btn-default"><?php echo _('Feature Comparison'); ?> <i class="fa fa-external-link r"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>
    </div>

<?php
    do_page_end(true);
}