<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: applyconfig.inc.php
//  Desc: Handles writing out config files to disk, verifying configurations, and submitting restart
//        commands to Nagios Core.
//


/**
 * Routes page commands, display page html with feedback 
 *
 * @param string $mode handles the page commands, passed from page_router(), $_REQUEST['type'] 
 * @return string $html returns html output to index.php 
 */ 
function apply_config($mode='')
{
    // Check session protector (for deletion since we use calls from scripts elsewhere like
    // when we apply config and call nagiosql_exportall.php)
    if ($mode == 'delete') {
        check_nagios_session_protector();
    }

    switch ($mode)
    {
        case 'writeConfig':
        case 'verify':
        case 'restart':
        case 'delete':
            list($returnCode, $returnMessage) = write_config_tool($mode);
            $html = write_config_html($returnCode, $returnMessage);
            break;

        case 'applyfull':
            $msg = _("Applying Configuration").'...<br />';
            $errors = 0;

            // Validate configuration
            list($verifyMsg, $verifyCode) = write_config_tool('verify');
            if ($verifyCode > 0) {
                $html = write_config_html($verifyCode, $verifyMessage);
                break; 
            }
            $msg .= _("Configuration verification successful!")." <br />";

            // Write changes to disk
            list($writeCode, $writeMessage) = write_config_tool('writeConfig');
            if ($writeCode > 0) {
                $html = write_config_html($writeCode, $writeMessage); 
                break;              
            }
            $msg .= _("Configurations successfully written to file!")." <br />"; 
            
            // Restart Nagios Core
            list($restartCode, $restartMessage) = write_config_tool('restart');
            if ($restartCode > 0) {
                $errors = 1; 
                $html = write_config_html($errors, $restartMessage);
                break;
            }
            $msg .= _("Nagios process restarted successfully!")."<br />";

            $html = write_config_html($errors, $msg);
            break; 
        
        // Display the default page options
        default:
            $html = write_config_html();
            break;
    }

    return $html; 
}


/**
 * Write the page html based on success or failure of the command submitted
 *
 * @param   int     $code       Error code returned from the command submitted
 * @param   string  $message    Return message from any submitted command
 * @return  string  $html       HTML string to be displayed in the browser 
 */
function write_config_html($code=0, $message='')
{ 
    if ($message == '') {
        $retClass = 'invisible';
    } else {
        $retClass = (($code > 0) ? 'error' : 'success'); 
    }

    $nsp = get_nagios_session_protector();

    // Build html string 
    $html = "
    <div id='contentWrapper'>
        <h1>"._("Config File Management")."</h1>
        <p>"._("Use this tool to manually manage (write, delete, update) Nagios object configurations physical configuration files.")."<br><b>"._('Note')."</b>: "._("To manually apply configuration, first use <em>Delete Files</em> and then run <em>Write Configs</em> followed by a verification (optional) and restart to fully apply the configuration.")." </p>
        <div id='writeConfigDiv'>
            <form action='index.php?cmd=apply' method='post' id='writeConfigForm'>
                ".$nsp."
                <input type='hidden' name='type' id='type' value=''>
                <input type='hidden' name='key' id='key' value=''>
                    <button onclick=\"doConfig('writeConfig')\" type='button' name='writeConfig' title='"._('Write all objects in the CCM to their physical config files')."' class='btn btn-sm btn-primary tt-bind' id='writeConfig'><i class='fa fa-sticky-note l'></i> "._("Write Configs")."</button>
                    <button onclick=\"doConfig('delete')\" title='"._('Delete all host and service files to forcefully write them')."' class='btn btn-sm btn-danger tt-bind' type='button' name='delete' id='delete'><i class='fa fa-trash l'></i> "._("Delete Files")."</button>
                    <button onclick=\"doConfig('verify')\" title='"._('Verify physical configuration files using Nagios Core')."' class='btn btn-sm btn-default tt-bind' type='button' name='verify' id='verify'><i class='fa fa-check l'></i> "._("Verify Files")."</button>
                    <button onclick=\"doConfig('restart')\" title='"._('Restart the Nagios Core backend to update in-use configuration')."' class='btn btn-sm btn-default tt-bind' type='button' name='restart' id='restart'><i class='fa fa-refresh l'></i> "._("Restart Nagios Core")."</button>
            </form> 
        </div>
        <div id='applyConfigOutput' class='{$retClass} floatLeft'>{$message}</div>
    </div>";

    return $html; 
}


/**
 * Submits commands based on the $mode (write | verify | restart)
 *
 * @param   string  $mode   (write | verify | restart)
 * @return  array           array(int errorCode, string returnMessage) 
 */
function write_config_tool($mode='')
{
    global $ccm;
    global $cfg;

    $chkDomainId = isset($_SESSION) ? $_SESSION['domain'] : 1;
    $strMessage = '';
    
    $ccm->config->getConfigData("hostconfig", $strHostDir);
    $ccm->config->getConfigData("serviceconfig", $strServiceDir);

    // Route command by mode 
    switch ($mode)
    {
        // Verify nagios config 
        case 'verify': 
            $errorString = verify_configs($strMessage);
            if ($errorString) {
                return array(1, "<span class='urgent'>$errorString</span>$strMessage");
            } else {
                $ccm->data->writeLog(_('Ran verify configuration'), AUDITLOGTYPE_INFO);
                return array(0, $strMessage);
            }

        // Restart nagios process 
        case 'restart':
            $strMessage = '';
            $code = 0;
            $errors = verify_configs($strMessage);

            // Bail if config is bad 
            if ($errors !== false) { 
                return array(1, _('RESTART FAILED. CONFIG ERROR:')."<br />".$errors);
            }

            $now = time();
            $commandfile = '/usr/local/nagios/var/rw/nagios.cmd';
            if (isset($cfg['component_info']['nagioscore']['cmd_file'])) {
                $commandfile = $cfg['component_info']['nagioscore']['cmd_file'];
            }

            $cmd = '/usr/bin/printf "[%lu] RESTART_PROGRAM\n" '.$now.' > '.$commandfile;
            
            $msg = system($cmd, $code);
            if ($code == 0) {
                $strMessage = _("Restart command successfully sent to Nagios");
            }
            $ccm->data->writeLog(_('Restarted Nagios Core'), AUDITLOGTYPE_INFO);
            return array($code, $strMessage);

        // Delete all configuration files
        case 'delete':
            $errorString = delete_configs($strMessage);
            if ($errorString) {
                return array(1, "<span class='urgent'>$errorString</span>$strMessage");
            }
            $ccm->data->writeLog(_('Deleted all physical configuration files'), AUDITLOGTYPE_INFO);
            return array(0, $strMessage);
    
        // Write DB to config files 
        case 'writeConfig':
            // Write host configurations
            $strInfo = _("Write host configurations")." ...<br />";
            $strSQL = "SELECT `id`,`host_name` FROM `tbl_host` WHERE `config_id` = ".intval($chkDomainId)." AND `active`='1'";
            $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
            $intError = 0;

            // Initialize the host haystack array - used to check for ghost configs
            $host_haystack = array(); 
            if ($intDataCount != 0) {
                foreach ($arrData AS $data) {
                    $ccm->config->lastModifiedDir($data['host_name'], $data['id'], 'host', $strTime, $strTimeFile, $intOlder);
                    $tmp_config_name = $ccm->config->sanatize_filename($data['host_name']);
                    array_push($host_haystack, $tmp_config_name);
                    if ($intOlder == 1) {
                        $ccm->config->createConfigSingle("tbl_host", $data['host_name']);
                        $strInfo .= _("Configuration file: ")."<strong>".$tmp_config_name.".cfg</strong>"._(" successfully written!")."<br>";
                        if ($ccm->config->strDBMessage != _("Configuration file successfully written!")) {
                            $intError++;
                        }
                    }
                }
            }

            // Verify if there are any ghost host files and remove them
            $host_files = preg_grep('/^([^.])/', scandir($strHostDir));
            foreach ($host_files AS $file_name) {
                $config_name = preg_replace('/.cfg$/', '', $file_name);
                if (!in_array($config_name, $host_haystack)) {
                    $strInfo .= "<span class=\"verify-ok\">"._("WARNING! Ghost host config detected! Removing the file <strong>$config_name.cfg</strong>")."</span><br />";
                    $ccm->config->removeFile($strHostDir."$config_name.cfg");
                }   
            }
          
            // Error output  
            if ($intError == 0) {
                $strInfo .= "<span class=\"verify-ok\">"._("Host configuration files successfully written!")."</span><br /><br />";
            } else {
                $strInfo .= "<span class='urgent'>"._("Cannot open/overwrite the host configuration files (check the permissions)!")."</span><br>";
            }

            // Write service configuration
            $strInfo .= _("Write service configurations")." ...<br>";
            $strSQL = "SELECT DISTINCT `config_name` FROM `tbl_service` WHERE `config_id` = $chkDomainId AND `active`='1'"; 
            $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
            $intError = 0;
          
            // Initialize the service haystack array - used to check for ghost configs
            $service_haystack = array(); 
            if ($intDataCount != 0) {
                foreach ($arrData AS $data) {
                    $ccm->config->lastModifiedDir($data['config_name'], null, 'service', $strTime, $strTimeFile, $intOlder, true);
                    $tmp_config_name = $ccm->config->sanatize_filename($data['config_name']);
                    array_push($service_haystack, $tmp_config_name);
                    if ($intOlder == 1) {
                        $ccm->config->createConfigSingle("tbl_service", $data['config_name']);
                        $strInfo .= _("Configuration file: ")."<strong>".$data['config_name'].".cfg</strong>"._(" successfully written!")."<br>";
                        if ($ccm->config->strDBMessage != _("Configuration file successfully written!")) {
                            $intError++;
                        }
                    }
                }
            }

            // Verify if we have any ghost service files and remove them
            $service_files = preg_grep('/^([^.])/', scandir($strServiceDir));
            foreach ($service_files AS $file_name) {
                $config_name = preg_replace('/.cfg$/', '', $file_name);
                if (!in_array($config_name, $service_haystack)) {
                    $strInfo .= "<span class=\"verify-ok\">"._("WARNING! Ghost service config detected! Removing the file <strong>$config_name.cfg</strong>")."</span><br />";
                    $ccm->config->removeFile($strServiceDir."$config_name.cfg");
                }   
            }

            // Check for service errors
            if ($intError == 0) {
                $strInfo .= "<span class=\"verify-ok\">"._("Service configuration files successfully written!")."</span><br><br>";
            } else {
                $strInfo .= "<span class='urgent'>"._("Cannot open/overwrite service configuration files (check the permissions)!")."</span><br>";
            }

            // Write configs for single config files
            $ccm_config_tables = array("tbl_hostgroup", "tbl_servicegroup", "tbl_hosttemplate", "tbl_servicetemplate", "tbl_timeperiod", "tbl_command", "tbl_contact", "tbl_contactgroup", "tbl_contacttemplate", "tbl_servicedependency", "tbl_hostdependency", "tbl_serviceescalation", "tbl_hostescalation", "tbl_serviceextinfo", "tbl_hostextinfo");
            foreach ($ccm_config_tables as $ccm_table) {
                $ccm->config->createConfig($ccm_table);
                $strInfo .= $ccm->config->strDBMessage."<br>";
            }

            // If we have successfully wrote all configs and we are using Nagios XI
            // then let's set the ccm_apply_config_needed option back to 0
            if ($intError == 0) {
                set_option("ccm_apply_config_needed", 0);
                nagiosccm_clear_tables_modified();
                if (!defined('SUBSYSTEM')) {
                    $ccm->data->writeLog(_('Wrote physical configuration files'), AUDITLOGTYPE_INFO);
                }
            }

            return array($intError, $strInfo); 
            break;

        default:
            // Do nothing break; 
            break; 
    }
}


/**
 * Runs the nagios verification command and returns the text output from it
 *
 * @param   string  $strMessage     REFERENCE variable to the return message 
 * @return  string  $errorString
 */
function verify_configs(&$strMessage)
{
    global $ccm;

    $chkDomainId = isset($_SESSION) ? $_SESSION['domain'] : 1;

    $ccm->config->getConfigData("binaryfile", $strBinary);
    $ccm->config->getConfigData("basedir", $strBaseDir);
    $ccm->config->getConfigData("nagiosbasedir", $strNagiosBaseDir);
    $errorString = false;

    if (file_exists($strBinary) && is_executable($strBinary)) {
        $resFile = popen($strBinary." -v ".str_replace("//","/",$strNagiosBaseDir."/nagios.cfg"),"r");
        if($resFile) {
            $output ='';
            while(!feof($resFile)) {
                $line = fgets($resFile);
                // Capture error output 
                if (strpos($line, 'Error:') !== false) $errorString.= $line.'<br />';
                $strMessage .= $line."<br />";
            }
        }
        else $errorString = _("Can't find Nagios binary!");
        pclose($resFile);
    } else {
        $errorString = _('Cannot find the Nagios binary or no rights for execution!');
    }

    // If there's an error verifying configurations then we need to apply config again
    if ($errorString !== false) {
        set_option("ccm_apply_config_needed", 1);
    }
    
    return $errorString;
}


/**
 * Deletes ALL host/service configurations from their local directories
 *
 * @param   string  $strMessage     REFERENCE variable to the return message
 * @return  int     $intError       0 or 1 if there is an error
 */
function delete_configs(&$strMessage)
{
    global $ccm;
    
    // Set directories
    $ccm->config->getConfigData("hostconfig", $strHostDir);
    $ccm->config->getConfigData("serviceconfig", $strServiceDir);

    // Run deletion commands
    $cmdline = "rm -f $strHostDir/* $strServiceDir/*";
    $output = system($cmdline, $return_code);

    // Return error code and exit text
    if ($return_code == 0) { 
        $strMessage = _("Successfully deleted all Host / Service Config Files");
        set_option("ccm_apply_config_needed", 1);
    }

    $intError = 0;
    return $intError;
}
