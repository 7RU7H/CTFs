<?php
//
// Nagios XI API Documentation
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/html-helpers.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

if (!is_admin()) {
    die(_('Not authorized to view this page.'));
}

route_request();

function route_request()
{
    $page = grab_request_var("page", "");

    switch ($page) {
        default:
            show_main_api_page();
            break;
    }
}

function show_main_api_page()
{
    $apikey = get_apikey();

    do_page_start(array("page_title" => _('Translations')), true);
?>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">
    
                <h1><?php echo _('Translations'); ?></h1>
                <p><?php echo _("The translations for the interface are done through gettext, an open source software that allows localization of system environments. The translations are stored in .po files. These files can be edited with a .po editor. We recommend using"); ?> <a href="https://poedit.net" target="_new">poedit <i class="fa fa-external-link"></i></a> <?php echo _("since it's an open source editor that works on most operating systems."); ?></p>
                <p>
                    <ul>
                        <li><?php echo _("The files are stored in"); ?> <code><?php echo get_base_dir(); ?>/lang/locale/&lt;language&gt;/LC_MESSAGES</code></li>
                        <li><code>&lt;language&gt;</code> <?php echo _("is specified by the ISO 639 two-letter local name and ISO 3166 two-leter country code, such as"); ?> <code>en_US</code></li>
                        <li><?php echo _("Each language has a .po and .mo file. The .po should be edited and the .mo is a compiled after all edits have been made to the .po file."); ?></li>
                    </ul>
                </p>
                <p><?php echo _("Send updated or new translation files to"); ?> <a href="mailto:translations@nagios.com">translations@nagios.com</a> <?php echo _("for inclusion into upcoming releases of Nagios XI"); ?>.</p>

                <div class="help-section">
                    <h4><?php echo _("Updating Current Translations"); ?></h4>
                    <p><?php echo _("If a translation that came bundled in Nagios XI needs to be edited for correctness, you should first make sure you are on the latest version of Nagios XI to get the latest translation files. You can then edit the files using poedit. Navigate to the location of the .po file and open it in poedit. Make the desired changes. You will need to compile the .mo file with your updated .po file. You can compile the .mo file using poedit or install gettext and run <code>msgfmt &lt;language&gt;.po -o &lt;language&gt;.mo</code> from the command line."); ?></p>
                    <p><?php echo _("After you've made your updates and replaced the .po and .mo files you should restart apache before verifying in the interface that the changes have been made."); ?></p>
                </div>

                <div class="help-section">
                    <h4><?php echo _("Creating New Translations"); ?></h4>
                    <p><?php echo _("To create a new translation you will need to do the following. We recommend that you make sure your Nagios XI installation is the latest available version, so that your translation has all the new language variables. For this example, we will use French"); ?> (<code>fr_FR</code>) <?php echo _("as our new language"); ?>:</p>
                    <p>
                        <ul>
                            <li>
                                <?php echo _("Copy the English .po file from") ?> <code><?php echo get_base_dir(); ?>/lang/en_US/LC_MESSAGES/en_US.po</code>:
                                <pre style="margin-top: 10px;">cd <?php echo get_base_dir(); ?>/lang
mkdir -p fr_FR/LC_MESSAGES/
cp en_US/LC_MESSAGES/en_US.po fr_FR/LC_MESSAGES/fr_FR.po</pre>
                            </li>
                            <li><?php echo _("Open the .po file in poedit and make the translations to the new language"); ?></li>
                            <li><?php echo _("Convert your .po file to a .mo file and restart apache see changes in the interface (the language will show up in the selection drop down right away)"); ?></li>
                            <li><?php echo _("Once you're done, send us an email to the address above with the translated file to get it included into future versions of Nagios XI"); ?></li>
                        </ul>
                    </p>
                </div>

            </div>
        </div>
    </div>

<?php
}