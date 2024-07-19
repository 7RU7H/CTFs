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
    $pageopt = get_pageopt("info");

    switch ($pageopt) {
        case "legal":
            show_legal();
            break;
        case "license":
            show_license();
            break;
        default:
            show_about();
            break;
    }
}

function show_about()
{
    $copyright = 'Nagios<sup>&reg;</sup> XI&trade; Copyright &copy; 2008-'.date("Y").' <a href="https://www.nagios.com/" target="_blank" rel="noreferrer">Nagios Enterprises, LLC</a>. All rights reserved.';
    $hide_credits = 0;

    if (custom_branding()) {
        global $bcfg;
        $copyright = $bcfg['copyright'];
        $hide_credits = $bcfg['hide_credits'];
        $about = $bcfg['about'];
        $contact_support = $bcfg['contact_support'];
        $contact_sales = $bcfg['contact_sales'];
        $contact_web = $bcfg['contact_web'];
    }

    do_page_start(array("page_title" => _('About')), true);
?>

    <h1><?php echo _('About') . ' ' . get_product_name(); ?></h1>

    <p style="margin-top: 10px;"><?php echo $copyright; ?></p>
    
    <div class="sectionTitle"><?php echo _("About") . ' ' . get_product_name(); ?></div>
    <?php if (empty($about)) { ?>
    <p><?php echo _("Nagios XI is an enterprise-class monitoring and alerting solution that provides organizations with extended insight of their IT infrastructure before problems affect critical business processes. For more information on Nagios XI, visit the"); ?> <a href="https://www.nagios.com/products/nagiosxi/" target="_blank" rel="noreferrer"><?php echo _('Nagios XI product page'); ?></a>.</p>
    <?php } else { ?>
    <p><?php echo $about; ?></p>
    <?php } ?>

    <div class="sectionTitle"><?php echo _("License"); ?></div>
    <p><?php echo _("Use of") . " " . get_product_name() . " " . _("is subject to acceptance of the"); ?> <a href="?license"><?php echo _("Software License Terms and Conditions"); ?></a>.</p>

    <div class="sectionTitle"><?php echo _("Contact Us"); ?></div>
    <p><?php echo _("Have a question or technical problem? Contact us today"); ?>:</p>
    <table class="table table-condensed table-no-border" style="width: auto;">
        <tr>
            <td><?php echo _("Support"); ?>:</td>
            <td>
                <?php if (empty($contact_support)) { ?>
                <a href="https://support.nagios.com/forum/" target="_blank" rel="noreferrer"><?php echo _("Online Support Forum"); ?></a>
                <?php } else { echo $contact_support; } ?>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><?php echo _("Sales"); ?>:</td>
            <td>
                <?php if (empty($contact_sales)) { ?>
                <?php echo _("Phone"); ?>: (651) 204-9102
                <br><?php echo _("Fax"); ?>: (651) 204-9103
                <br><?php echo _("Email"); ?>: sales@nagios.com
                <?php } else { echo $contact_sales; } ?>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo _("Web"); ?>:</td>
            <td>
                <?php if (empty($contact_web)) { ?>
                <a href="https://www.nagios.com/" target="_blank" rel="noreferrer">www.nagios.com</a>
                <?php } else { echo $contact_web; } ?>
            </td>
        </tr>
    </table>

    <?php if (!$hide_credits) { ?>
    <div class="sectionTitle"><?php echo _("Credits"); ?></div>
    <p><?php echo _("We'd like to thank the many individuals, companies, partners, and customers who have shared their ideas and stories with us and participated in developing some really great software solutions that have made Nagios XI a possibility.  Neither Nagios Enterprises nor Nagios XI are necessarily endorsed by any of these parties - we just wanted to list them here as a public way of thanking them for the contributions they've made in various ways."); ?></p>
    <p><?php echo _("Some particular Open Source projects and development communities we'd like to thank include"); ?>:<br><?php echo _("The PHP development community, the MySQL and Postgres development communities, the ADODB project team, The Jquery project team and expanded jQuery community, the Silk icon set author at famfamfam.com, the PHPMailer team, the RRDTool project, the Nagios Core project, the Nagios Plugins projects, the PNP project, the NagVis project, the NagiosQL project, the Vartour Style project, the author of the F*Nagios image pack, and the entire Nagios Community and greater OSS community members who make great OSS solutions a possibility through their tireless contributions.  We just wanted to let you know that we think you rock."); ?></p>
    <p><?php echo _("We'd like to give an extra special thanks to the individual founders and leaders of each OSS project mentioned above.  We know that it takes a lot to build something that stands head and shoulders above the competition.  Kudos for you to bringing awesomeness into the world."); ?></p>
    <p>- The Nagios Enterprises Team</p>
    <?php } ?>

<?php
    do_page_end(true);
}

function show_legal()
{
    $copyright = 'Nagios<sup>&reg;</sup> XI&trade; Copyright &copy; 2008-'.date("Y").' <a href="https://www.nagios.com/" target="_blank" rel="noreferrer">Nagios Enterprises, LLC</a>. All rights reserved.';
    $hide_trademarks = 0;

    // Update output of info on page with custom branding (if it exists)
    if (custom_branding()) {
        global $bcfg;
        $copyright = $bcfg['copyright'];
        $hide_trademarks = $bcfg['hide_trademarks'];
    }

    do_page_start(array("page_title" => _('Legal Information')), true);
?>

    <h1><?php echo _('Legal Information'); ?></h1>

    <p style="margin-top: 10px;"><?php echo $copyright; ?></p>

    <div class="sectionTitle"><?php echo _("License"); ?></div>
    <p><?php echo _("Use of") . " " . get_product_name() . " " . _("is subject to acceptance of the"); ?> <a href="?license">Software License Terms and Conditions</a>.</p>

    <div class="sectionTitle"><?php echo _("Disclaimer of Warranty"); ?></div>
    <p><?php echo get_product_name() . " " . _("and all information, documentation, and software components contained in and distributed with it are provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A PARTICULAR PURPOSE."); ?></p>

    <?php if (!$hide_trademarks) { ?>
    <div class="sectionTitle"><?php echo _("Trademarks"); ?></div>
    <p><?php echo _("Nagios, Nagios XI, Nagios Core, and Nagios graphics are trademarks, servicemarks, registered servicemarks or registered trademarks of Nagios Enterprises. All other trademarks, servicemarks, registered trademarks, and registered servicemarks mentioned herein may be the property of their respective owner(s).  Use of our trademarks is subject to Nagios Enterprises'"); ?>
        <a href="https://www.nagios.com/legal/" target="_blank" rel="noreferrer"><?php echo _("Trademark Use Restrictions"); ?></a>.
    </p>
    <?php } ?>

<?php
    do_page_end(true);
}

function show_license()
{
    $copyright = 'Nagios<sup>&reg;</sup> XI&trade; Copyright &copy; 2008-'.date("Y").' <a href="https://www.nagios.com/" target="_blank" rel="noreferrer">Nagios Enterprises, LLC</a>. All rights reserved.';

    if (custom_branding()) {
        global $bcfg;
        $copyright = $bcfg['copyright'];
    }

    do_page_start(array("page_title" => _('License Information')), true);
?>

    <h1><?php echo _('License Information'); ?></h1>
    
    <p style="margin-top: 10px;"><?php echo $copyright; ?></p>

    <p style="margin-top: 30px;"><?php echo get_formatted_license_text(); ?></p>

<?php
    do_page_end(true);
}