<?php
// RESPONSE COMPONENT
//
// Copyright (c) 2008-2015 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id$

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');


// run the initialization function
response_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function response_component_init()
{

    $name = "response";

    $args = array(

        // need a name
        COMPONENT_NAME => $name,

        // informative information
        //COMPONENT_VERSION => "1.1",
        //COMPONENT_DATE => "11-27-2009",
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => "Provides integration with Response.",
        COMPONENT_TITLE => "Response Integration",
        //COMPONENT_COPYRIGHT => "Copyright (c) 2009 Nagios Enterprises",
        //COMPONENT_HOMEPAGE => "https://www.nagios.com",

        // configuration function (optional)
        COMPONENT_CONFIGFUNCTION => "response_component_config_func",
    );

    register_component($name, $args);
}


///////////////////////////////////////////////////////////////////////////////////////////
//CONFIG FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

/**
 * @param string $mode
 * @param        $inargs
 * @param        $outargs
 * @param        $result
 *
 * @return string
 */
function response_component_config_func($mode = "", $inargs, &$outargs, &$result)
{

    // initialize return code and output
    $result = 0;
    $output = "";
    $outargs = array("hello" => "test");

    switch ($mode) {
        case COMPONENT_CONFIGMODE_GETSETTINGSHTML:

            $settings_raw = get_option("response_options");
            if ($settings_raw == "")
                $settings = array();
            else
                $settings = unserialize($settings_raw);

            // initial values
            $url = grab_array_var($settings, "url", "http://");
            $username = grab_array_var($settings, "username", "");
            $password = grab_array_var($settings, "password", "");
            $enabled = grab_array_var($settings, "enabled", "");
            $autocreateissues = grab_array_var($settings, "autocreateissues", "");

            //echo "ACI1: $autocreateissues<BR>";

            // values passed to us
            $url = grab_array_var($inargs, "url", $url);
            $username = grab_array_var($inargs, "username", $username);
            $password = grab_array_var($inargs, "password", $password);
            $enabled = checkbox_binary(grab_array_var($inargs, "enabled", $enabled));
            $autocreateissues = checkbox_binary(grab_array_var($inargs, "autocreateissues", $autocreateissues));

            //echo "ACI2: $autocreateissues<BR>";

            //$autocreateissues=checkbox_binary($autocreateissues);

            //echo "ACI3: $autocreateissues<BR>";

            //print_r($inargs);


            $output = '
			
	<div class="sectionTitle">' . _('Response Server Settings') . '</div>
	
	<table>

	<tr>
	<td valign="top">
	<label>' . _('URL') . ':</label><br class="nobr" />
	</td>
	<td>
<input type="text" size="50" name="url" id="url" value="' . $url . '" class="textfield" /><br class="nobr" />
	' . _('The URL of your Response server') . '.
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label>' . _('Username') . ':</label><br class="nobr" />
	</td>
	<td>
<input type="text" size="10" name="username" id="username" value="' . $username . '" class="textfield" /><br class="nobr" />
	' . _('Username used for Response authentication') . '.
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label>' . _('Password') . ':</label><br class="nobr" />
	</td>
	<td>
<input type="password" size="10" name="password" id="password" value="' . $password . '" class="textfield" /><br class="nobr" />
	' . _('Password used for Response authentication') . '.
	</td>
	</tr>

	</table>

	<div class="sectionTitle">' . _('Integration Settings') . '</div>
	
	<table>

	<tr>
	<td valign="top">
	<label for="enabled">' . _('Enable Integration') . ':</label><br class="nobr" />
	</td>
	<td>
	<input type="checkbox" class="checkbox" id="enabled" name="enabled" ' . is_checked($enabled, 1) . '>
<br class="nobr" />
	' . _('Enables integration between Nagios XI and Response') . '.
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label for="autocreateissues">' . _('Auto-Create Issues') . ':</label><br class="nobr" />
	</td>
	<td>
	<input type="checkbox" class="checkbox" id="autocreateissues" name="autocreateissues" ' . is_checked($autocreateissues, 1) . '>
<br class="nobr" />
	' . _('Automatically create new issues in Response when problems are detected in Nagios') . '.
	</td>
	</tr>

	</table>

			';
            break;

        case COMPONENT_CONFIGMODE_SAVESETTINGS:

            // get variables
            $url = grab_array_var($inargs, "url", "");
            $username = grab_array_var($inargs, "username", "");
            $password = grab_array_var($inargs, "password", "");
            $enabled = checkbox_binary(grab_array_var($inargs, "enabled", ""));
            $autocreateissues = checkbox_binary(grab_array_var($inargs, "autocreateissues", ""));

            // validate variables
            $errors = 0;
            $errmsg = array();
            if ($enabled == 1) {
                if (have_value($url) == false) {
                    $errmsg[$errors++] = "No URL specified.";
                }
                if (have_value($username) == false) {
                    $errmsg[$errors++] = "No username specified.";
                }
                if (have_value($password) == false) {
                    $errmsg[$errors++] = "No password specified.";
                }
            }

            // handle errors
            if ($errors > 0) {
                $outargs[COMPONENT_ERROR_MESSAGES] = $errmsg;
                $result = 1;
                return '';
            }

            // save settings
            $settings = array(
                "url" => $url,
                "username" => $username,
                "password" => $password,
                "enabled" => $enabled,
                "autocreateissues" => $autocreateissues,
            );
            set_option("response_options", serialize($settings));


            break;

        default:
            break;

    }

    return $output;
}


////////////////////////////////////////////////////////////////////////
// EVENT HANDLER AND NOTIFICATION FUNCTIONS
////////////////////////////////////////////////////////////////////////


register_callback(CALLBACK_EVENT_PROCESSED, 'response_eventhandler');

/**
 * @param $cbtype
 * @param $args
 */
function response_eventhandler($cbtype, $args)
{

    switch ($args["event_type"]) {
        case EVENTTYPE_NOTIFICATION:
            response_handle_notification_event($args);
            break;
        default:
            break;
    }
}

/**
 * @param $args
 */
function response_handle_notification_event($args)
{

    $meta = $args["event_meta"];
    $contact = $meta["contact"];

    // find the XI user
    $user_id = get_user_id($contact);
    if ($user_id <= 0)
        return;
}


////////////////////////////////////////////////////////////////////////
// ISSUE FUNCTIONS
////////////////////////////////////////////////////////////////////////

/**
 * @return bool
 */
function response_open_issue_exists()
{
    return false;
}

/**
 * @return bool
 */
function response_create_issue()
{
    return false;
}

/**
 * @return bool
 */
function response_add_to_issue()
{
    return false;
}

