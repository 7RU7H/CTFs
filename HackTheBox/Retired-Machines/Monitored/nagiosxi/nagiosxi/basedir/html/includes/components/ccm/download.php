<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2021 Nagios Enterprises, LLC
//
//  File: download.php
//  Desc: Creates a configuration file based on what is in the CCM database and allows the user to
//        download just the file for verification/testing.
//


// Include the Nagios XI helper functions through the component helper file and initialize
// anything we will need to authenticate ourselves to the CCM
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


// Verify access
if (!user_can_access_ccm() || (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects())) {
    die(_('You do not have access to this page.'));
}


// Set the location of the CCM root directory
define('BASEDIR', dirname(__FILE__).'/');
require_once('includes/constants.inc.php');
require_once('includes/session.inc.php');


// Grab the token variable and do an authentication check
$cmd = '';
$token = ccm_grab_request_var('token', '');
if ($AUTH !== true) {
    $cmd = 'login';
}


// Verify that the command was submitted from the form and if it's not then the user will be
// routed to the login page if it's an illegal operation otherwise route the request for download
verify_token($cmd, $token);
route_request($cmd);


/**
 * Directs page navigation and input requests for config downloads, verifies auth
 *
 * @param   string  $cmd    Rrequires a valid command to do anything, if auth it bad this will be '' or 'login'
 */
function route_request($cmd='')
{
    if ($cmd == 'login') {
        header('Location: index.php?cmd=login');
    }

    download_config();
}


/**
* function download_config()
* Generates the config file on the fly based ont the object type
* @global object $myConfigClass Config object (config_class)
* @global object $myDataClass Data object (data_class)
*/
function download_config()
{
    global $ccm;

    // Get request vars
    $chkTable = 'tbl_'.ccm_grab_request_var('type', "");
    $cfgName = stripslashes(ccm_grab_request_var('config', ""));

    // Generate config file name if it's not a host/service
    switch ($chkTable) {
        case "tbl_timeperiod": $strFile = "timeperiods.cfg"; break;
        case "tbl_command": $strFile = "commands.cfg"; break;
        case "tbl_contact": $strFile = "contacts.cfg"; break;
        case "tbl_contacttemplate": $strFile = "contacttemplates.cfg"; break;
        case "tbl_contactgroup": $strFile = "contactgroups.cfg"; break;
        case "tbl_hosttemplate": $strFile = "hosttemplates.cfg"; break;
        case "tbl_servicetemplate": $strFile = "servicetemplates.cfg"; break;
        case "tbl_hostgroup": $strFile = "hostgroups.cfg"; break;
        case "tbl_servicegroup": $strFile = "servicegroups.cfg"; break;
        case "tbl_servicedependency": $strFile = "servicedependencies.cfg"; break;
        case "tbl_hostdependency": $strFile = "hostdependencies.cfg"; break;
        case "tbl_serviceescalation": $strFile = "serviceescalations.cfg"; break;
        case "tbl_hostescalation": $strFile = "hostescalations.cfg"; break;
        case "tbl_hostextinfo": $strFile = "hostextinfo.cfg"; break;
        case "tbl_serviceextinfo": $strFile = "serviceextinfo.cfg"; break;
        default: $strFile = "objects.cfg";
    }

    if ($strFile == ".cfg") {
        print "Error: Invalid Config Option.";
        exit;
    }

    // Set content headers and set disposition to inline so the viewer can see it when they click
    // the link instead of being directed to download right away
    header("Content-Disposition: inline; filename=".$strFile);
    header("Content-Type: text/plain");

    // Create and display the config output
    if (empty($cfgName)) {
        $ccm->config->createConfig($chkTable, 1);
    } else {
        $ccm->config->createConfigSingle($chkTable, $cfgName, 1);
    }
    $ccm->data->writeLog(_('Viewed config')." ".$strFile, AUDITLOGTYPE_INFO);
}
