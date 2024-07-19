<?php
//
// User Macros Component
// Copyright (c) 2016-2019 Nagios Enterprises, LLC. All rights reserved.
//
// Recommended Reading
// https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/4/en/macrolist.html#user
// https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/4/en/macros.html
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$usermacros_component_name = "usermacros";
usermacros_component_init();


////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function usermacros_component_init()
{
    global $usermacros_component_name;
    $versionok = usermacros_component_checkversion();

    $args = array(
        COMPONENT_NAME => $usermacros_component_name,
        COMPONENT_VERSION => '1.1.0',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Manage user and system macros."),
        COMPONENT_TITLE => _("User Macros"),
        COMPONENT_CONFIGFUNCTION => "usermacros_component_config_func",
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($usermacros_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'usermacros_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function usermacros_component_checkversion()
{
    if (!function_exists('get_product_release'))
        return false;
    if (get_product_release() < 5500)
        return false;
    return true;
}


function usermacros_component_addmenu($arg = null)
{
    global $usermacros_component_name;
    global $menus;

    // Retrieve the URL for this component
    $urlbase = get_component_url_base($usermacros_component_name);

    // Add to the new CCM
    if (ccm_has_access_for('usermacros')) {
        add_menu_item(MENU_CCM, array(
            "type" => "link",
            "title" => _("User Macros"),
            "id" => "menu-ccm-usermacros",
            "order" => 802,
            "opts" => array(
                "href" => $urlbase . "/index.php",
                "icon" => "fa-usd"
            )
        ));
    }
}


////////////////////////////////////////////////////////////////////////
// CONFIG FUNCTIONS
////////////////////////////////////////////////////////////////////////


function usermacros_component_config_func($mode = "", $inargs, &$outargs, &$result)
{
    $result = 0;
    $output = "";

    $component_name = "usermacros";

    switch ($mode) {
        case COMPONENT_CONFIGMODE_GETSETTINGSHTML:

            $tab = grab_request_var("tab", 1);
            $usermacro = grab_request_var("usermacro", 1);

            $component_url = get_component_url_base($component_name);

            // Grab the config files to add to database
            $resource_file = '/usr/local/nagios/etc/resource.cfg';
            $user_macro_data = file_get_contents($resource_file);

            $usermacro_disable = grab_array_var($inargs, "usermacro_disable", get_option("usermacro_disable", 0));
            $usermacro_redacted = grab_array_var($inargs, "usermacro_redacted", get_option("usermacro_redacted", 1));
            $usermacro_user_redacted = grab_array_var($inargs, "usermacro_user_redacted", get_option("usermacro_user_redacted", 0));

            $usermacro_ccm_link = get_base_url() . "includes/components/usermacros/index.php";

            $output = '
                <script type="text/javascript">
                    $(document).ready(function() {
                        var input = $("input#usermacro_redacted");
                        check_redacted(input);

                        $("input#usermacro_redacted").change(function() {
                            check_redacted($(this));
                        });

                        function check_redacted(input) {
                            if (input.is(":checked")) {
                                $("#usermacro_user_redacted").parent().css("color", "#999");
                                $("#usermacro_user_redacted").prop("disabled", true);

                                if ($("input#usermacro_user_redacted").is(":checked")) {
                                    $("#usermacro_user_redacted").attr("checked", false);
                                } else {
                                    $("#usermacro_user_redacted").prop("disabled", true);
                                }
                            } else {
                                $("#usermacro_user_redacted").parent().css("color", "rgb(102, 102, 102)");
                                $("#usermacro_user_redacted").prop("disabled",false);
                            }
                        }
                    });
                </script>

                <p style="color: #888;">' . _("Detect, create, display and define system and user macros in Nagios XI.") . '</p><br>

                <p style="max-width: 1100px;">' . _("This component will detect input of '\$USERn\$' user defined macros where 'n' is a number between 1 and 256. This number is defined in Nagios Core as MAX_USER_MACROS.  It will also give a user a list of commonly used System Macros in the appropriate inputs.  The system marcos are displayed using autocomplete and the full list of available macros are defined in the System Macros tab.") . '</p>

                <div class="subtext">' . _("Note: To manually enable detection fucntionality on a page inside Nagios XI or a custom component/tool you can add the class \"usermacro-detection\" to an HTML input.") . '</div><br>
                <i class="fa fa-usd fa-3"></i><a style="margin-left: 5px;" href="' . $usermacro_ccm_link . '">User Macro Control (CCM)<a><div class="subtext" style="margin-left: 30px;"></a>' . _("To view the macro files go the the User Macro section in the CCM by clicking this link.") . '</div><br>

                <h5 class="ul">' . _("Component Settings") . '</h5>
                <p>' . _("These are the general settings for this component") . '.</p>

                <div style="padding-left: 16px;">
                    <table class="table table-condensed table-no-border table-auto-width">
                        <tr>
                            <td class="checkbox">
                                <label>
                                    <input id="usermacro_disable" name="usermacro_disable" type="checkbox" value="1" ' . is_checked($usermacro_disable, 1) . '>
                                    ' . _("Disable Component") . '
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="checkbox">
                                <label>
                                    <input id="usermacro_redacted" name="usermacro_redacted" type="checkbox" value="1" ' . is_checked($usermacro_redacted, 1) . '>
                                    ' . _("Redact Displayed Values (Enabled by Default)") . '<br><div class="subtext" style="max-width: 930px;">' . _('This option will redact the USER macro values from being displayed to protect sensitive data for ALL users.  With this option enabled you will still be able to add undefined macros when they are detected.  This will override the Non-Admin user setting.') . '</div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="checkbox">
                                <label>
                                    <input id="usermacro_user_redacted" name="usermacro_user_redacted" type="checkbox" value="1" ' . is_checked($usermacro_user_redacted, 1) . '>
                                    ' . _("Redact Values for Non-Admin Users") . '<br><div class="subtext">' . _('This option will redact the USER macro values from being displayed to all but Administrator level users.') . '</div>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>';

            break;

        case COMPONENT_CONFIGMODE_SAVESETTINGS:

            // get variables
            $usermacro_disable = grab_array_var($inargs, "usermacro_disable");
            $usermacro_redacted = grab_array_var($inargs, "usermacro_redacted");
            $usermacro_user_redacted = grab_array_var($inargs, "usermacro_user_redacted");

            $errors = 0;
            $errmsg = array();

            if (is_admin() == false) {
                $errmsg[$errors++] = "You must be an Administrator level user to modify this page.";
            }

            // Handle errors
            if ($errors > 0) {
                $outargs[COMPONENT_ERROR_MESSAGES] = $errmsg;
                $result = 1;
                return '';
            }

            set_option("usermacro_disable", $usermacro_disable);
            set_option("usermacro_redacted", $usermacro_redacted);
            set_option("usermacro_user_redacted", $usermacro_user_redacted);

            break;

        default:
            break;

    }

    return $output;
}
