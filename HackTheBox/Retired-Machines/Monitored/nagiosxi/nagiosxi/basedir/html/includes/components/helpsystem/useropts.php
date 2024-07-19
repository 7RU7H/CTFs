<?php
//
// Help System Component
// Copyright (c) 2011-2017 - Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

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
    global $request;

    if (isset($request['update'])) {
        do_update_options();
    } else {
        show_options();
    }
}

/**
 * @param bool   $error
 * @param string $msg
 */
function show_options($error = false, $msg = "")
{

    // Get settings specified by admin
    $settings_raw = get_option("helpsystem_component_options");
    if (empty($settings_raw)) {
        $settings_raw = get_option("helpsystem_component_options");
    }

    if (empty($settings_raw)) {
        $settings = array(
            "enabled" => 1,
            "allow_user_override" => 1
        );
    } else {
        $settings = unserialize($settings_raw);
    }

    // Default settings for allow override
    $allow_override = grab_array_var($settings, "allow_user_override", 1);
    $settings_default = array(
        "enabled" => 1,
    );

    // Saved settings
    $settings_raw = get_user_meta(0, "helpsystem_component_options");
    if ($settings_raw != "") {
        $settings_default = unserialize($settings_raw);
    }

    // Settings passed to us
    $settings = grab_request_var("settings", $settings_default);

    $title = _("Help System");

    // Let the user know if they can't override the help system if the admin
    // has disabled the component
    if ($allow_override != 1) {
        $error = true;
        $msg .= _("Help system is currently disabled.");
    }

    // Start the HTML page
    do_page_start(array("page_title" => $title), true);
?>

    <h1><?php echo $title; ?></h1>

    <?php display_message($error, false, $msg); ?>
    <p><?php echo _("You can use the settings on this page to enable/disable the Help System."); ?></p>

    <form id="manageOptionsForm" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">

        <input type="hidden" name="options" value="1">
        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="update" value="1">

       <h5 class="ul"><?php echo _('Help System Settings'); ?></h5>
    
       <table class="table table-condensed table-no-border table-auto-width"> 
           <tr>
               <td></td>
               <td class="checkbox">
                   <label><input type="checkbox" class="checkbox" id="enabled" name="settings[enabled]" <?php echo is_checked($settings["enabled"], 1); ?>> <?php echo _('Enable the help system.'); ?></label>
               </td>
           </tr>
       </table>

        <div id="formButtons">
            <input type="submit" class="submitbutton btn btn-sm btn-primary" name="updateButton" value="<?php echo _('Update Settings'); ?>" id="updateButton">
            <input type="submit" class="submitbutton btn btn-sm btn-default" name="cancelButton" value="<?php echo _('Cancel'); ?>" id="cancelButton">
        </div>

    </form>
    <?php
    do_page_end(true);
}

// Update user's selected options
function do_update_options()
{
    global $request;

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: " . get_base_url() . "/account/main.php");
    }

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Get setting values
    $settings = grab_request_var("settings", array());

    // Fix checkboxes
    $settings["enabled"] = checkbox_binary(grab_array_var($settings, "enabled", ""));

    // Make sure we have requirements
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    // Handle errors
    if ($errors > 0) {
        show_options(true, $errmsg);
    }

    // Update options
    set_user_meta(0, "helpsystem_component_options", serialize($settings), false);
    set_user_meta(0, "helpsystem_component_options_configured", 1, false);

    show_options(false, _("Settings updated."));
}