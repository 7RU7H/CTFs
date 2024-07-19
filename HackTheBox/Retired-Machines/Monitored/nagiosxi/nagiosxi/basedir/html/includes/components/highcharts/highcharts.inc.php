<?php
//
// Highcharts Component
// Copyright (c) 2010-2020 Nagios Enterprises, LLC.  All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Respect the name
$highcharts_component_name = "highcharts";

// Run the initialization function
highcharts_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function highcharts_component_init()
{
    global $highcharts_component_name;

    $args = array(
        COMPONENT_NAME => $highcharts_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => "Provides dynamic graphing integration capabilities.",
        COMPONENT_TITLE => "Highcharts Exporting Server",
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($highcharts_component_name, $args);
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
function highcharts_component_config_func($mode = "", $inargs, &$outargs, &$result)
{
    global $highcharts_component_name;

    // Initialize return code and output
    $result = 0;
    $output = "";

    //delete_option("highcharts_component_options");


    switch ($mode) {
        case COMPONENT_CONFIGMODE_GETSETTINGSHTML:
            $output = '<p>'._("You can set up a local exporting server instead of using export.highcharts.com to do your Highcharts exporting").'.</p>';

            // Verify that the pre-reqs have been installed for the exporting server
            $disabled = '';
            if (!highcharts_exporting_prereqs_installed()) {
                $disabled = 'disabled';
                $output .= '<div class="message">
                    <ul class="errorMessage" style="padding: 1em 1em 1em 1.5em;">
                        <li>'._("It looks like you don't have the prereqs for exporting Highcharts graphs locally. You must install the prerequisites for the local Highcharts Exporting Server. We have a script that will install everything you need. Follow the directions below.").'</li>
                        <li style="margin-top: 10px;">'._("To install, run the following as").' <strong>root</strong>:</i>
                        <li><strong>'.get_component_dir_base($highcharts_component_name).'/install-prereqs.sh</strong></li>
                    </ul>
                </div>';
            }

            $checked = '';
            $export_locally = get_option('highcharts_local_export_server', 0);
            if ($export_locally) {
                $checked = "checked";
            }

            $output .= '<div style="margin-bottom: 20px;">
                <label>
                    <input type="checkbox" name="export_locally" value="1" '.$checked.'> '._("Use local Highcharts exporting server when exporting Highcharts graphs").'
                </label>
            </div>';
            break;

        case COMPONENT_CONFIGMODE_SAVESETTINGS:

            $export_locally = grab_array_var($inargs, "export_locally", 0);
            set_option('highcharts_local_export_server', $export_locally);

            break;

        default:
            break;

    }

    return $output;
}

/**
 * Checks if the Highcharts Exporting Server pre-reqs have been installed
 * Requires: batik-rasterizer.jar & 'lib' directory
 */
function highcharts_exporting_prereqs_installed()
{
    global $highcharts_component_name;

    if (file_exists(get_component_dir_base($highcharts_component_name)."/exporting-server/batik-rasterizer.jar")) {
        return true;
    }

    return false;
}