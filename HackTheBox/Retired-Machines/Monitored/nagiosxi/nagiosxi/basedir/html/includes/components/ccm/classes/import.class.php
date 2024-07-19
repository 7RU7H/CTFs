<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//  Copyright (c) 2008, 2009 Martin Willisegger
//
//  File: import.class.php
//  Desc: Handles the importing of Nagios Core configuration files
//
//  $StrDBMessage:  Releases of the database server
//  $StrMessage:    Communications class function 
//


class Import
{
    // Declare class variables
    var $intDomainId = 0;   // Is filled in the class
    var $strDBMessage = ""; // Classes will be filled internally
    var $strMessage = "";   // Classes will be filled internally
    var $strList1 = "";     // Value list 1
    var $strList2 = "";     // Value list 2

    /**
     * Constructor for the import class. You can pass a configuration
     * array or the config array will be pulled from the session.
     * (Can someone explain why $CFG is passed as a session value... and named SETS?)
     *
     * @param   int     $domain     For multiple domain configs (optional - not used)
     */
    function __construct($domain = 1)
    {
        // Set domain (not currently used for anything)
        $this->intDomainId = $domain;
        if (isset($_SESSION['domain'])) {
            $this->intDomainId = $_SESSION['domain'];
        }
    }

    /**
     * Function: Data Import
     * ---------
     * Imports a configuration file and writes their data to the corresponding Data Table
     *
     * @param $strFileName - Name of file to import or string to import from
     * @param $intOverwrite - 0 = not to overwrite data, 1 = Data overwritten
     * @param $useStrInstead - Tells function that $strFileName is a string of config values not a file name
     * @param $strict - Whether or not to remove anything that looks like a comment even in check_command when importing
     *
     * @return int - 0 for success or 1 for failure
     */
    function fileImport($strFileName, $intOverwrite, $useStrInstead=false, $strict=false)
    {
        global $ccm;

        $intBlock       = 0;
        $intCheck       = 0;
        $intRemoveTmp   = 0;
        $arrData        = array();

        if (!$useStrInstead) {
            $strFileName    = trim($strFileName);
            $booReturn      = $ccm->config->getConfigData("method", $intMethod);
            
            // Are the files readable?
            if ($intMethod == 1) {
                if (!is_readable($strFileName)) {
                    $this->strDBMessage .= _('Cannot open the data file (check the permissions)!')." ".$strFileName."<br>";
                    return(1);
                }
            } 
        }

        // Get array of file or string from lines
        $lines = array();
        if ($useStrInstead) {
            $lines = explode("\n", $strFileName);
            $strFileName = '';
        } else {
            $resFile = fopen($strFileName, "r");
            while (!feof($resFile)) {
                $l = fgets($resFile);
                $lines[] = $l;
            }
        }

        // Configuration line by line
        $nextLineContinues = false;
        $strLastLine = "";
        foreach ($lines as $strConfLine) {

            $strConfLine = trim($strConfLine);

            // Comment lines
            if (substr($strConfLine,0,1) == "#") {
                if ($intBlock == 1 && ($strBlockKey == 'serviceescalation' || $strBlockKey == 'hostescalation' || $strBlockKey == 'servicedependency' || $strBlockKey == 'hostdependency')) {
                    if (strpos($strConfLine, 'config_name') !== false) {
                        $arrLine = preg_split("/[\s]+/", $strConfLine);
                        unset($arrLine[0]);
                        unset($arrLine[1]);
                        $arrData['_config_name'] = implode(' ', $arrLine);
                    }
                }
                continue;
            }

            // Blank lines
            if ($strConfLine == "") continue;
            if (($intBlock == 1) && ($strConfLine == "{")) continue;

            // Check if we are still continuing from the last line... if we are we need to smash
            // the last line together with the current line
            if ($nextLineContinues) {
                $nextLineContinues = false;
                $strConfLine = $strLastLine.$strConfLine;
            }

            // Check if we need to add another line to the current line
            if (substr($strConfLine, -1) == '\\') {
                $nextLineContinues = true;
                $strLastLine = str_replace('\\', '', $strConfLine);
                continue;
            }

            // Line process (reduce space and comments cut)
            // For check_command in strict mode, we will check for a comment by assuming a comment would
            // have a space before the actual comment ... this could break check commands but it also ensures
            // that if we are importing random stuff from Nagios Core thee won't be weirdness
            $arrLine    = preg_split("/[\s]+/", $strConfLine, 2);
            $strNewLine = implode(" ", $arrLine);
            if ($arrLine[0] != "check_command" || $strict) {
                $arrTemp    = explode(";", $strNewLine);
                $strNewLine = trim($arrTemp[0]);
            }

            // Block start search
            if ($arrLine[0] == "define") {
                $intBlock = 1;
                $strBlockKey = trim(str_replace("{","",$arrLine[1]));
                $arrData = array();
                continue;
            }

            // Block data stored in an array
            if (($intBlock == 1) && ($arrLine[0] != "}")) {
                $strExclude = "timeperiod_name,template_name,alias,name,use,register,exclude";
                if (($strBlockKey == "timeperiod") && (!in_array($arrLine[0], explode(",", $strExclude)))) {
                    $arrNewLine = explode(" ", $strNewLine);
                    $arrData[$arrLine[0]] = array("key" => str_replace(" ".$arrNewLine[count($arrNewLine)-1], "", $strNewLine), "value" => $arrNewLine[count($arrNewLine)-1]);
                } else {
                    $key   = $arrLine[0];
                    $value = str_replace($arrLine[0]." ","",$strNewLine);
                    if ($value == $key) { $value = ""; }

                    // Special case retry_check_interval, normal_check_interval
                    if ($key == "retry_check_interval") $key = "retry_interval";
                    if ($key == "normal_check_interval") $key = "check_interval";
                    $arrData[$arrLine[0]] = array("key" => $key, "value" => $value);
                }
            }

            // Process at end of block data
            if ((substr_count($strConfLine,"}") == 1) && (is_array($arrData)))  {

                $intBlock = 0;

                // Validate the block!
                if ($errors = $this->invalid_name_check($strBlockKey, $arrData)) {
                    $this->strDBMessage .= $errors;
                    return(1);
                }

                $intReturn = $this->importTable($strBlockKey, $arrData, $intOverwrite, $strFileName);
                $intCheck = $intReturn;
            }
        }

        if ($intRemoveTmp == 1 && !$useStrInstead) {
            unlink($strFileName);
        }

        return $intCheck;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Helper function: import table
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Imports a configuration file into the appropriate data table.
    //
    // Parameters: $strBlockKey configuration key (define)
    // $ArrImportData Scanned data of a block
    // $IntOverwrite overwrite data in Table 1 = yes, 0 = No
    // $StrFileName name of the configuration file
    //
    //
    // Return value: 0 for success or 1 on failure / 2 entry already exists
    // Success - / strDBMessage error message via variable class 
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strBlockKey
     * @param $arrImportData
     * @param $intOverwrite
     * @param $strFileName
     *
     * @return int
     */
    function importTable($strBlockKey, $arrImportData, $intOverwrite, $strFileName)
    {
        global $ccm;

        // Declare variables
        $intExists = 0;
        $intInsertRelations = 0;
        $intInsertVariables = 0;
        $intInsertTimeperiods = 0;
        $intIsTemplate = 0;
        $strVCValues = "";
        $strRLValues = "";
        $strVWValues = "";
        $strVIValues = "";
        $intWriteConfig = 0;
        $strWhere = "";
        $this->strList1 = "";
        $this->strList2 = "";

        // Template or configuration
        if (array_key_exists("name", $arrImportData) && (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0))) {
            $intIsTemplate = 1;
        }

        // Table Name Set
        if ($intIsTemplate == 0) {
            switch($strBlockKey) {

                case "command":
                    $strTable = "tbl_command";
                    $strKeyField = "command_name";
                    break;

                case "contactgroup":
                    $strTable = "tbl_contactgroup";
                    $strKeyField = "contactgroup_name";
                    break;

                case "contact":
                    $strTable = "tbl_contact";
                    $strKeyField = "contact_name";
                    break;

                case "timeperiod":
                    $strTable = "tbl_timeperiod";
                    $strKeyField = "timeperiod_name";
                    break;

                case "host":
                    $strTable = "tbl_host";
                    $strKeyField = "host_name";
                    break;

                case "service":
                    $strTable = "tbl_service";
                    $strKeyField = "";
                    break;

                case "hostgroup":
                    $strTable = "tbl_hostgroup";
                    $strKeyField = "hostgroup_name";
                    break;
                
                case "servicegroup":
                    $strTable = "tbl_servicegroup";
                    $strKeyField = "servicegroup_name";
                    break;
                
                case "hostescalation":
                    $strTable = "tbl_hostescalation";
                    $strKeyField = "";
                    break;
                
                case "serviceescalation":
                    $strTable = "tbl_serviceescalation";
                    $strKeyField = "";
                    break;
                
                case "hostdependency":
                    $strTable = "tbl_hostdependency";
                    $strKeyField = "";
                    break;
                
                case "servicedependency":
                    $strTable = "tbl_servicedependency";
                    $strKeyField = "";
                    break;
                
                case "hostextinfo":
                    $strTable = "tbl_hostextinfo";
                    $strKeyField = "host_name";
                    break;
                
                case "serviceextinfo":
                    $strTable = "tbl_serviceextinfo";
                    $strKeyField = "";
                    break;
                
                default:
                    $this->strDBMessage = _('Table for import definition')." '".$strBlockKey."' "._('is not available!');
                    return(1);
                    break;
            }
        } else {
            $strKeyField = "name";
            switch($strBlockKey) {
                
                case "contact":
                    $strTable = "tbl_contacttemplate";
                    break;

                case "host":
                    $strTable = "tbl_hosttemplate";
                    break;

                case "service":
                    $strTable = "tbl_servicetemplate";
                    break;
                
                default:
                    $this->strDBMessage = _('Table for import definition')." '".$strBlockKey."' "._('is not available!');
                    return(1);
            }
        }

        // In configurations without generating such a key field
        // Service-specific
        if ($strTable == "tbl_service") {

            if (strpos($strFileName, ".") !== false) {
                $arrTemp1 = explode(".", strrev(basename($strFileName)), 2);
            }

            if (isset($arrImportData['host_name'])) {
                $arrTemp2 = explode(",", $arrImportData['host_name']['value']);
            }

            if (isset($arrImportData['hostgroup_name'])) {
                $arrTemp3 = explode(",", $arrImportData['hostgroup_name']['value']);
            }
          
            $strTemp = "";
            if (!empty($arrTemp1)) {
                $strTemp = strrev($arrTemp1[1]);
            } else if (!empty($arrTemp2)) {
                $strTemp = $arrTemp2[0];
            } else if (!empty($arrTemp3)) {
                $strTemp = $arrTemp3[0];
            } else {
                $strTemp = "Import ".microtime(true);
            }
            $strTemp = preg_replace("/^\++/", "", $strTemp);

            $strKeyField = "config_name";
            $arrImportData['config_name']['key'] = "config_name";
            if (empty($arrImportData['config_name']['value'])) {
                $arrImportData['config_name']['value'] = $strTemp;
            }
            $strWhere = " AND `service_description` = '".$arrImportData['service_description']['value']."' ";
        }

        // Host/Service dependency and Host/Service escalation specific
        if (($strTable == "tbl_hostdependency") || ($strTable == "tbl_servicedependency") || ($strTable == "tbl_hostescalation") || ($strTable == "tbl_serviceescalation")) {
            if (isset($arrImportData['_config_name'])) {
                $config_name = $arrImportData['_config_name'];
                unset($arrImportData['_config_name']);
            }

            if (empty($config_name)) {
                $config_name = "Import ".microtime(true);
            }

            $config_name = preg_replace("/^\++/", "", $config_name);
            $strKeyField = "config_name";
            $arrImportData['config_name']['key'] = "config_name";
            $arrImportData['config_name']['value'] = $config_name;
        }

        // Service extra info specifics
        if ($strTable == "tbl_serviceextinfo") {
            $arrTemp1 = explode(".", strrev(basename($strFileName)), 2);

            if (isset($arrImportData['host_name'])) {
                $arrTemp2 = explode(",", $arrImportData['host_name']['value']);
            }

            if (isset($arrImportData['service_description'])) {
                $arrTemp3 = explode(",", $arrImportData['service_description']['value']);
            }
          
            if (isset($arrTemp2[0])) { $strTemp = $arrTemp2[0]; }
            if (isset($arrTemp3[0])) { $strTemp .= " - ".$arrTemp3[0]; }
            if ($strTemp == "") {
                $strTemp = strrev($arrTemp1[1]);
            }
            $strTemp = preg_replace("/^\++/", "", $strTemp);
            $strKeyField = "config_name";
            $arrImportData['config_name']['key'] = "config_name";
            $arrImportData['config_name']['value'] = $strTemp;
        }

        // Relations that read this table
        $intRelation = $ccm->data->tableRelations($strTable, $arrRelations);

        // Does the entry already exist?
        if ($intIsTemplate == 0) {
            if (($strKeyField != "") && isset($arrImportData[$strKeyField])) {

                // Free variables don't need a config ID defined
                $str_config_id = "`config_id`=".$this->intDomainId." AND";
                if ($strTable == "tbl_variabledefinition") {
                    $str_config_id = "";
                }

                $e_value = $ccm->db->escape_string($arrImportData[$strKeyField]['value']);
                $intExists = $ccm->db->getFieldData("SELECT `id` FROM `".$strTable."` WHERE ".$str_config_id." `".$strKeyField."` = '".$e_value."' $strWhere");
                if ($intExists == false) { $intExists = 0; }
            }
        } else {
            if (($strKeyField != "") && isset($arrImportData['name'])) {
                $e_value = $ccm->db->escape_string($arrImportData[$strKeyField]['value']);
                $intExists = $ccm->db->getFieldData("SELECT `id` FROM `".$strTable."` WHERE `config_id`=".$this->intDomainId." AND `template_name`='".$e_value."' $strWhere");
                if ($intExists == false) { $intExists = 0; }
            }
        }

        // For Services to host second test
        if (($strTable == "tbl_service") && ($intExists != 0)) {

            $e_value = $ccm->db->escape_string($arrImportData[$strKeyField]['value']);
            $intExists = 0;
            $strSQLService = "SELECT `id`,`host_name`,`hostgroup_name` FROM `tbl_service`
                              WHERE `config_id`=".$this->intDomainId." AND `".$strKeyField."`='".$e_value."' $strWhere";
            $booReturn = $ccm->db->getDataArray($strSQLService, $arrDataService, $intDCService);

            if ($booReturn && ($intDCService != 0)) {
                $arrHc = array();
                $arrHgc = array();
                foreach ($arrDataService AS $servElem) {

                    // Get hosts
                    $strSQLHC = "SELECT host_name FROM tbl_host LEFT JOIN tbl_lnkServiceToHost ON id = idSlave WHERE idMaster = ".$servElem['id']." ORDER BY host_name";
                    $booReturn = $ccm->db->getDataArray($strSQLHC, $arrDataHC, $intDCHC);
                    if ($servElem['host_name'] == '2') {
                        $strHostline = "*";
                    } else {
                        $strHostline = "";
                    }
                    if ($booReturn && ($intDCHC != 0)) {
                        $tmp = array();
                        foreach ($arrDataHC AS $elemHC) {
                            $tmp[] = $elemHC['host_name'];
                        }
                        $strHostline = implode("::", $tmp);
                    }
                    $arrHc[$servElem['id']] = $strHostline;

                    // Get hostgroups
                    $strSQLHGC = "SELECT hostgroup_name FROM tbl_hostgroup LEFT JOIN tbl_lnkServiceToHostgroup ON id = idSlave WHERE idMaster = ".$servElem['id']." ORDER BY hostgroup_name";
                    $booReturn = $ccm->db->getDataArray($strSQLHGC, $arrDataHGC, $intDCHGC);
                    if ($servElem['hostgroup_name'] == '2') {
                        $strHostgroupline = "*";
                    } else {
                        $strHostgroupline = "";
                    }               
                    if ($booReturn && ($intDCHGC != 0)) {
                        $tmp = array();
                        foreach ($arrDataHGC AS $elemHGC) {
                            $tmp[] = $elemHGC['hostgroup_name'];
                        }
                        $strHostgroupline = implode("::", $tmp);
                    }
                    $arrHgc[$servElem['id']] = $strHostgroupline;

                }
            }

            // Comparisons

            // Get all hosts and remove the ! from ones that are excluded
            if (isset($arrImportData['host_name']['value'])) {
                $arrTemp1 = explode(",", $arrImportData['host_name']['value']);
                foreach ($arrTemp1 as $i => $h) {
                    if (strpos($h, '!') === 0) {
                        $arrTemp1[$i] = substr($h, 1);
                    }
                }
                asort($arrTemp1);
                $strHostline = implode("::", $arrTemp1);
            }

            // Get all host groups and remove the ! from ones that are excluded
            if (isset($arrImportData['hostgroup_name']['value'])) {
                $arrTemp2 = explode(",", $arrImportData['hostgroup_name']['value']);
                foreach ($arrTemp2 as $i => $hg) {
                    if (strpos($hg, '!') === 0) {
                        $arrTemp2[$i] = substr($hg, 1);
                    }
                }
                asort($arrTemp2);
                $strHostgroupline = implode("::", $arrTemp2);
            }

            // HOSTS ONLY
            if (isset($arrImportData['host_name']['value']) && !isset($arrImportData['hostgroup_name']['value'])) {
                foreach ($arrHc AS $key => $chkElem1) {
                    if (($chkElem1 == $strHostline) && (!isset($arrHgc[$key]) || ($arrHgc[$key] == ""))) {
                        $intExists = $key;
                    }
                }
            }

            // HOSTGROUPS ONLY
            if (!isset($arrImportData['host_name']['value']) && isset($arrImportData['hostgroup_name']['value'])) {
                foreach($arrHgc AS $key => $chkElem2) {
                    if (($chkElem2 == $strHostgroupline) && (!isset($arrHc[$key]) || ($arrHc[$key] == ""))) {
                       $intExists = $key;
                    }
                }
            }

            // HOSTS AND HOSTGROUPS
            if (isset($arrImportData['host_name']['value']) && isset($arrImportData['hostgroup_name']['value'])) {
                foreach ($arrHc AS $key => $chkElem1) {
                    if ($chkElem1 == $strHostline) {
                        if ($strHostgroupline != "") {
                            // Host groups agree, too?
                            foreach($arrHgc AS $key => $chkElem2) {
                                if ($chkElem2 == $strHostgroupline) {
                                   $intExists = $key;
                                }
                            }
                        } else {
                           $intExists = $chkElem['id'];
                        }
                    }
                }
            }

        }

        // For Services with config_name, that weren't found via the above
        if ($strTable == "tbl_service" && $intExists == 0) {
            if (isset($arrImportData['config_name']['value']) && isset($arrImportData['service_description']['value'])) {

                $cn = $ccm->db->escape_string($arrImportData['config_name']['value']);
                $sd = $ccm->db->escape_string($arrImportData['service_description']['value']);

                $strSQLService = "SELECT `id`,`config_name`,`service_description` FROM `tbl_service`
                                  WHERE `config_name` = '".$cn."' AND `service_description` = '".$sd."'";
                $booReturn = $ccm->db->getSingleDataset($strSQLService, $arrService);

                if ($booReturn && !empty($arrService)) {
                    $intExists = $arrService['id'];
                }
            }
        }

        // Does the entry, but must not be overwritten?
        if (($intExists != 0) && ($intOverwrite == 0)) {
            $this->strMessage .= _('Entry')." ".$strKeyField." => ".$arrImportData[$strKeyField]['value']." "._('exists and was not overwritten')."<br>";
            return 1;
        }

        // * Values do not write
        if ($arrImportData[$strKeyField] == "*") {
            $this->strMessage .= _('Entry')." ".$strKeyField."::".$arrImportData[$strKeyField]['value']._('inside')." "._('were not written')."<br>";
            return 1;
        }

        // Entry is active?
        if (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0) && ($intIsTemplate != 1)) {
            $intActive = 0;
        } else {
            $intActive = 1;
        }

        // SQL Define - Part 1
        if ($intExists != 0) {
            // DB Update entry
            $strSQL1 = "UPDATE `".$strTable."` SET ";
            $strSQL2 = "  `config_id`=".$this->intDomainId.", `active`='$intActive', `last_modified`=NOW() WHERE `id`=$intExists";
        } else {
            // DB Insert contact
            $strSQL1 = "INSERT INTO `".$strTable."` SET ";
            $strSQL2 = "  `config_id`=".$this->intDomainId.", `active`='$intActive', `last_modified`=NOW()";
        }

        // Statement of the values
        // -----------------------
        // $ StrVCValues = pure text values in the table is stored as varchar null = 'null' as the Text value empty =''
        // $ = StrRLValues Relations - values, with links to other tables
        // $ StrVWValues = Integer values - are stored in the table to the zero-INT = -1, empty values as NULL
        // $ StrVIValues decision values = 0 = no, 1 = yes, 2 = Bypass, 3 = null

        // 
        // Read Command configurations
        // ================================
        if ($strKeyField == "command_name") {
            $strVCValues = "command_name,command_line";

            // Command type to find
            if ((substr_count($arrImportData['command_line']['value'],"ARG1") != 0) ||
                (substr_count($arrImportData['command_line']['value'],"USER1") != 0)) {
                $strSQL1 .= "`command_type` = 1,";
            } else {
                $strSQL1 .= "`command_type` = 2,";
            }
            $intWriteConfig = 1;
        }
        //
        // Contact configurations read
        // ================================
        else if ($strKeyField == "contact_name") {
            $strVCValues  = "contact_name,alias,host_notification_options,service_notification_options,email,";
            $strVCValues .= "pager,address1,address2,address3,address4,address5,address6,name";

            $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
            $strVIValues .= "retain_nonstatus_information";

            $strRLValues  = "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
            $strRLValues .= "service_notification_commands,use";
            $intWriteConfig = 1;
        }
        //
        // Contact Group configurations read
        // =====================================
        else if ($strKeyField == "contactgroup_name") {
            $strVCValues  = "contactgroup_name,alias";

            $strRLValues  = "members,contactgroup_members";
            $intWriteConfig = 1;
        }
        //
        // Timeperiod Configurations read
        // ===================================
         else if ($strKeyField == "timeperiod_name") {
            $strVCValues  = "timeperiod_name,alias,name";

            $strRLValues  = "exclude";
            $intWriteConfig = 1;
        }
        //
        // Contacttemplate Configurations read
        // ========================================
        else if (($strKeyField == "name") && ($strTable == "tbl_contacttemplate")) {
            $strVCValues  = "contact_name,alias,host_notification_options,service_notification_options,email,";
            $strVCValues .= "pager,address1,address2,address3,address4,address5,address6,name";

            $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
            $strVIValues .= "retain_nonstatus_information";

            $strRLValues  = "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
            $strRLValues .= "service_notification_commands,use";
            $intWriteConfig = 1;
        }
        //
        // Host Configurations read
        // =============================
        else if ($strTable == "tbl_host") {
            $strVCValues  = "host_name,alias,display_name,address,initial_state,flap_detection_options,notification_options,";
            $strVCValues .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
            $strVCValues .= "2d_coords,3d_coords,name";

            $strVWValues  = "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
            $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay,";

            $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
            $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
            $strVIValues .= "notifications_enabled";

            $strRLValues  = "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
            $strRLValues .= "notification_period";
            $intWriteConfig = 1;
        }
        //
        // Hosttemplate Configurations read
        // =====================================
         else if (($strKeyField == "name") && ($strTable == "tbl_hosttemplate")) {
            $strVCValues  = "template_name,alias,initial_state,flap_detection_options,notification_options,";
            $strVCValues .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
            $strVCValues .= "2d_coords,3d_coords,name";

            $strVWValues  = "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
            $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay,";

            $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
            $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
            $strVIValues .= "notifications_enabled";

            $strRLValues  = "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
            $strRLValues .= "notification_period";
            $intWriteConfig = 1;
        }
        //
        // Hostgroup Configurations read
        // ==================================
        else if ($strKeyField == "hostgroup_name") {
            $strVCValues = "hostgroup_name,alias,notes,notes_url,action_url";

            $strRLValues = "members,hostgroup_members";
            $intWriteConfig = 1;
        }
        //
        // Service Configurations read
        // ================================
        else if ($strTable == "tbl_service") {
            $strVCValues  = "service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
            $strVCValues .= "action_url,icon_image,icon_image_alt,name,config_name,notification_options";

            $strVWValues  = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
            $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay";

            $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
            $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
            $strVIValues .= "retain_nonstatus_information,notifications_enabled";

            $strRLValues  = "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
            $intWriteConfig = 1;
        }
        //
        // Servicetemplate Configurations read
        // ========================================
        else if (($strKeyField == "name") && ($strTable == "tbl_servicetemplate")) {
            $strVCValues  = "template_name,service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
            $strVCValues .= "action_url,icon_image,icon_image_alt,name,notification_options";

            $strVWValues  = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
            $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay";

            $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
            $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
            $strVIValues .= "retain_nonstatus_information,notifications_enabled";

            $strRLValues  = "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
            $intWriteConfig = 1;
        }
        //
        // Servicegroup Configurations read
        // ==================================
        else if ($strKeyField == "servicegroup_name") {
            $strVCValues  = "servicegroup_name,alias,notes,notes_url,action_url";

            $strRLValues  = "members,servicegroup_members";
            $intWriteConfig = 1;
        }
        //
        // Hostdependency Configurations read
        // =======================================
        else if ($strTable == "tbl_hostdependency") {
            $strVCValues  = "config_name,execution_failure_criteria,notification_failure_criteria";

            $strVIValues  = "inherits_parent";

            $strRLValues  = "dependent_host_name,dependent_hostgroup_name,host_name,hostgroup_name,dependency_period";
            $intWriteConfig = 1;
        }
        //
        // Hostescalation Configurations read
        // =======================================
        else if ($strTable == "tbl_hostescalation") {
            $strVCValues  = "config_name,escalation_options";

            $strVWValues  = "first_notification,last_notification,notification_interval";

            $strRLValues  = "host_name,hostgroup_name,contacts,contact_groups,escalation_period";
            $intWriteConfig = 1;
        }
        //
        // Hostextinfo Configurations read
        // ====================================
        else if ($strTable == "tbl_hostextinfo") {
            $strVCValues  = "notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,2d_coords,3d_coords";

            $strRLValues  = "host_name";
            $intWriteConfig = 1;
        }
        //
        // Hostdependency Configurations read
        // =======================================
        else if ($strTable == "tbl_servicedependency") {
            $strVCValues  = "config_name,execution_failure_criteria,notification_failure_criteria";

            $strVIValues  = "inherits_parent";

            $strRLValues  = "dependent_host_name,dependent_hostgroup_name,dependent_service_description,host_name,";
            $strRLValues .= "hostgroup_name,dependency_period,service_description";
            $intWriteConfig = 1;
        }
        //
        // Serviceescalation Configurations read
        // ==========================================
        else if ($strTable == "tbl_serviceescalation") {
            $strVCValues  = "config_name,escalation_options";

            $strVWValues  = "first_notification,last_notification,notification_interval";

            $strRLValues  = "host_name,hostgroup_name,contacts,contact_groups,service_description,escalation_period";
            $intWriteConfig = 1;
        }
        //
        // Serviceextinfo Configurations read
        // =======================================
        else if ($strTable == "tbl_serviceextinfo") {
            $strVCValues  = "notes,notes_url,action_url,icon_image,icon_image_alt";

            $strRLValues  = "host_name,service_description";
            $intWriteConfig = 1;
        }
    
        foreach ($arrImportData AS $elem) {

            $intCheck = 0;
            // Write text values
            if (in_array($elem['key'], explode(",", $strVCValues))) {
                if (strtolower(trim($elem['value'])) == "null") {
                    $strSQL1 .= "`".$elem['key']."` = 'null',";
                } else {
                    $elem['value'] = addslashes($elem['value']);
                    if ($intIsTemplate == 1) {
                        if ($elem['key'] == "name") {
                            $strSQL1 .= "template_name = '".$elem['value']."',";
                        } else {
                            $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
                        }
                    } else {
                        $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
                    }
                }
                $intCheck = 1;
            }

            // Status values Leave
            if (in_array($elem['key'], explode(",", $strVIValues))) {
                if (strtolower(trim($elem['value'])) == "null") {
                    $strSQL1 .= "`".$elem['key']."` = 3,";
                } else {
                    $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
                }
                $intCheck = 1;
            }

            // Integer values Leave
            if (in_array($elem['key'], explode(",", $strVWValues))) {
                if (strtolower(trim($elem['value'])) == "null") {
                    $strSQL1 .= "`".$elem['key']."` = -1,";
                } else {
                    $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
                }
                $intCheck = 1;
            }

            // Leave relations
            if (($intCheck == 0) && (in_array($elem['key'], explode(",", $strRLValues)))) {
                if ($elem['key'] == "use") { $elem['key'] = "use_template"; }
                $arrTemp = array();
                $arrTemp['key'] = $elem['key'];
                $arrTemp['value'] = $elem['value'];
                $arrImportRelations[] = $arrTemp;
                $intInsertRelations = 1;
                $intCheck = 1;
            }

            // Leave free variables
            if ($intCheck == 0) {
                if ($elem['key'][0] == "_") {
                    $arrTemp = array();
                    $arrTemp['key'] = $elem['key'];
                    $arrTemp['value'] = $elem['value'];
                    $arrFreeVariables[] = $arrTemp;
                    $intInsertVariables = 1;
                    $intCheck = 1;
                }
            }

            // Leave timeperiods
            if ($intCheck == 0) {
                $strSkip = "register";
                if (!in_array($elem['key'], explode(",", $strSkip))) {
                    $arrTemp = array();
                    $arrTemp['key'] = $elem['key'];
                    $arrTemp['value'] = $elem['value'];
                    $arrTimeperiods[] = $arrTemp;
                    $intInsertTimeperiods = 1;
                }
            }
        }
    
        $strTemp1 = "";
        $strTemp2 = "";

        // Database update
        if ($intWriteConfig == 1) {
            $booResult = $ccm->db->insertData($strSQL1.$strSQL2);
        } else {
            $booResult = false;
        }

        if ($strKeyField == "") { $strKey = $strConfig; } else { $strKey = $strKeyField; }
        if ($booResult != true) {
            $this->strDBMessage = $ccm->db->strDBError;
            if ($strKeyField != "") {
                $this->strMessage .= _('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." "._('inside')." ".$strTable." "._('could not be inserted:')." ".$ccm->db->error."<br>";
            } else {
                $this->strMessage .= _('Entry')." ".$strTemp1."::".$strTemp2._('inside')." ".$strTable." ".$strTable." "._('could not be inserted:')." ".$ccm->db->error."<br>";
            }
            return(1);
        } else {
            if ($strKeyField != "") $this->strMessage .= "<span class=\"greenmessage\">"._('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." "._('inside')." ".$strTable." "._('successfully inserted')."</span><br>";
            if ($strKeyField == "") $this->strMessage .= "<span class=\"greenmessage\">"._('Entry')." ".$strTemp1."::".$strTemp2." "._('inside')." ".$strTable." "._('successfully inserted')."</span><br>";
            
            // Record set ID
            if ($intExists != 0) {
                $intDatasetId = $intExists;
            } else {
                $intDatasetId = $ccm->db->intLastId;
            }

            // Relations still need to be registered?
            if ($intInsertRelations == 1) {
                /* arrRelations stores the order in which relations must be processed, when such an order exists. 
                 * Avoid changing this loop ordering
                 */
                foreach ($arrRelations as $reldata) {
                    foreach ($arrImportRelations as $elem) {
                        if ($reldata['fieldName'] == $elem['key']) {
                            if ($elem['key'] == "check_command") {
                                $this->writeRelation_5($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            } else if ($reldata['type'] == 1) {
                                $this->writeRelation_1($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            } else if ($reldata['type'] == 2) {
                                $this->writeRelation_2($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            } else if ($reldata['type'] == 3) {
                                $this->writeRelation_3($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            } else if ($reldata['type'] == 4) {
                                $this->writeRelation_4($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            } else if ($reldata['type'] == 5) {
                                $this->writeRelation_6($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                            }
                        }
                    }
                }
            }

            // Insert timeperiod relations
            if ($intInsertTimeperiods == 1) {
                if ($strTable == "tbl_timeperiod") {
                    // Delete old notes
                    $strSQL = "DELETE FROM `tbl_timedefinition` WHERE `tipId` = $intDatasetId";
                    $booResult = $ccm->db->insertData($strSQL);
                    foreach ($arrTimeperiods as $timeperiod) {
                        $strSQL = "INSERT INTO `tbl_timedefinition` SET `tipId` = $intDatasetId, `definition` = '".addslashes($timeperiod['key'])."', `range` = '".addslashes($timeperiod['value'])."'";
                        $booResult = $ccm->db->insertData($strSQL);
                    }
                }
            }

            // Insert free variable definitions
            if ($intInsertVariables == 1) {
                foreach ($arrRelations as $rel) {
                    if ($rel['fieldName'] == "use_variables") {
                        $reldata = $rel;
                    }
                }
                foreach ($arrFreeVariables as $elem) {
                    $this->writeRelation_4($elem['key'], $elem['value'], $intDatasetId, $strTable, $reldata);
                }
            }
            
            return(0);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link type 1, a (1:1)
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten
    //
    // Return value: 0 for success or 1 for failure 
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_1($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;
        $intSlaveId = 0;

        if (strtolower(trim($strValue)) == "null") {
            // Field data update in main table
            $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
            $booResult = $ccm->db->insertData($strSQL);
        } else {
            // Data value split
            $arrValues = explode(",", $strValue);
            
            // Execute data values
            foreach ($arrValues AS $elem) {
                $strWhere = "";
                $strLink = "";
                if (($strDataTable == "tbl_serviceextinfo") && (substr_count($strKey,"service") != 0)) {
                    $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
                    $strWhere = "AND `idSlave` IN (".$this->strList1.")";
                }
                
                // Determine whether the entry already exists
                $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` $strLink WHERE `".$arrRelData['target']."` = '".$elem."' $strWhere AND `config_id`=".$this->intDomainId;
                $strId = $ccm->db->getFieldData($strSQL);
                if ($strId != "") {
                    $intSlaveId = $strId+0;
                }

                // Temporary Make entry into the target table
                if ($intSlaveId == 0) {
                    $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                    $booResult = $ccm->db->insertData($strSQL);
                    $intSlaveId = $ccm->db->intLastId;
                }
            
                // Field data update in main table
                $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = ".$intSlaveId." WHERE `id` = ".$intDataId;
                $booResult = $ccm->db->insertData($strSQL);
                
                if ($strDataTable == "tbl_serviceextinfo") {
                    $this->strList1 = $intSlaveId;
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link of type 2, a (1: n)
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten??
    //
    //  Return value: 0 for success or 1 for failure
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_2($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;

        // Determine whether a field exists: tploptions
        $strSQL = "SELECT * FROM `".$strDataTable."` WHERE `id` = ".$intDataId;
        $booResult = $ccm->db->getSingleDataset($strSQL, $arrDataset);
        if (isset($arrDataset[$arrRelData['fieldName']."_tploptions"])) {
            $intTplOption = 1;
        } else {
            $intTplOption = 0;
        }

        // Link table delete
        $strSQL = "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
        $booResult = $ccm->db->insertData($strSQL);
    
        // Define variables
        $intSlaveId = 0;
        if (strtolower(trim($strValue)) == "null") {
            // Field data update in main table
            if ($intTplOption == 1) {
                $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,`".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
            } else {
                $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0 WHERE `id` = ".$intDataId;
            }
            $booResult = $ccm->db->insertData($strSQL);
        } else {
            if (substr(trim($strValue), 0, 1) == "+") {
                $intOption = 0;
                $strValue = preg_replace("/^\++/", "", $strValue);
            } else {
                $intOption = 2;
            }
            
            // Data value split
            $arrValues = explode(",", $strValue);
            
            // Execute data values
            foreach ($arrValues AS $elem) {

                // Skip empty named hosts
                if (empty($elem)) {
                    continue;
                }

                $excluded = false;
                $strWhere = "";
                $strLink  = "";

                if ((($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) && (substr_count($strKey, "service") != 0)) {
                    if (substr_count($strKey, "depend") != 0) {
                        $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
                        $strWhere = "AND `idSlave` IN (".substr($this->strList1, 0, -1).")";
                        // When there isn't an explicit dependent host, it's a same-host dependency.
                        if (empty($this->strList1)) {
                            $strWhere = "AND `idSlave` IN (".substr($this->strList2, 0, -1).")";
                        }
                    } else {
                        $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
                        $strWhere = "AND `idSlave` IN (".substr($this->strList2, 0, -1).")";
                    }
                }

                // Remove ! from the element and set excluded
                if ($arrRelData['target'] == 'host_name' || ($arrRelData['target'] == 'service_description' && $strDataTable == "tbl_serviceescalation") || $arrRelData['target'] == 'hostgroup_name') {
                    if (strpos($elem, '!') === 0) {
                        $elem = substr($elem, 1);
                        $excluded = true;
                    }
                }
        
                // Determine whether the entry already exists
                $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` $strLink WHERE `".$arrRelData['target']."` = '".$elem."' $strWhere AND `config_id`=".$this->intDomainId;
                $strId = $ccm->db->getFieldData($strSQL);
                if ($strId != "") {
                    $intSlaveId = $strId+0;
                } else {
                    $intSlaveId = 0;
                }

                if (($intSlaveId == 0) && ($elem != "*")) {
                    // Temporary Make entry into the target table
                    $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                    $booResult = $ccm->db->insertData($strSQL);
                    $intSlaveId = $ccm->db->intLastId;
                }

                // Submit link
                if ($elem != "*") {
                    $strSQL = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId;
                    if ($excluded) {
                        $strSQL .= ', `exclude` = 1';
                    }
                    $booResult = $ccm->db->insertData($strSQL);
            
                    // Values in Value cache list
                    if (($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) {
                        $strTemp = "";
                        if (($strKey == "dependent_host_name") || ($strKey == "host_name")) {
                            $strTemp .= $intSlaveId.",";
                        } else if (($strKey == "dependent_hostgroup_name") || ($strKey == "hostgroup_name")) {
                            $strSQL = "SELECT DISTINCT `id` FROM `tbl_host`
                                       LEFT JOIN `tbl_lnkHostToHostgroup` ON `id` = `tbl_lnkHostToHostgroup`.`idMaster`
                                       LEFT JOIN `tbl_lnkHostgroupToHost` ON `id` = `tbl_lnkHostgroupToHost`.`idSlave`
                                       WHERE (`tbl_lnkHostgroupToHost`.`idMaster` = $intSlaveId
                                       OR `tbl_lnkHostToHostgroup`.`idSlave` = $intSlaveId)
                                       AND `active`='1'
                                       AND `config_id`=".$this->intDomainId;
                            $booReturn = $ccm->db->getDataArray($strSQL, $arrDataHostgroups, $intDCHostgroups);
                            $arrDataHg2 = "";
                            foreach ($arrDataHostgroups AS $elem) {
                                $strTemp .= $elem['id'].",";
                            }
                        }
                        if (substr_count($strKey, "dependent") != 0) {
                            $this->strList1 .= $strTemp;
                        } else {
                            $this->strList2 .= $strTemp;
                        }
                    }
                } else if ($strDataTable == "tbl_serviceescalation" && $arrRelData['target'] == 'host_name') {
                    if (count($arrValues) == 1) {

                        // If we have a host_name of * and we are a service escalation we need to add all hosts to the list for services
                        $strSQL = "SELECT `id` from `tbl_host` WHERE `active` = '1' AND `config_id`=".$this->intDomainId;
                        $booReturn = $ccm->db->getDataArray($strSQL, $arrDataHosts, $intDataHosts);
                        if (!empty($arrDataHosts)) {
                            $strTemp = "";
                            foreach ($arrDataHosts AS $o) {
                                $strTemp .= $o['id'].",";
                            }
                            $this->strList2 .= $strTemp;
                        }

                    }
                }

                // Field data update in main table
                if ($strValue == "*") {
                    $intRelValue = 2;
                } else {
                    $intRelValue = 1;
                }
        
                if ($intTplOption == 1) {
                    $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue,`".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
                } else {
                    $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue WHERE `id` = ".$intDataId;
                }
                $booResult = $ccm->db->insertData($strSQL);
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link of type 3, a template
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten
    //
    // Return value: 0 for success or 1 for failure
    // 
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_3($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;

        $intSlaveId = 0;
        $intTable = 0;
        $intSortNr = 1;
    
        // Link table delete
        $strSQL = "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
        $booResult = $ccm->db->insertData($strSQL);

        if (strtolower(trim($strValue)) == "null") {
            // Field data update in main table
            $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,`".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
            $booResult = $ccm->db->insertData($strSQL);
        } else {
            if (substr(trim($strValue), 0, 1) == "+") {
                $intOption = 0;
                $strValue = preg_replace("/^\++/", "", $strValue);
            } else {
                $intOption = 2;
            }
      
            // Data value split
            $arrValues = explode(",", $strValue);
      
            // Execute data values
            foreach ($arrValues AS $elem) {
                
                // Determine if the template already exists (Table 1)
                $strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                $strId = $ccm->db->getFieldData($strSQL);
                if ($strId != "") {
                    $intSlaveId = intval($strId);
                    $intTable = 1;
                }

                if ($intSlaveId == 0) {
                    // Determine if the template already exists (Table 2)
                    $strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."` WHERE `".$arrRelData['target2']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                    $strId = $ccm->db->getFieldData($strSQL);
                    if ($strId != "") {
                        $intSlaveId = intval($strId);
                        $intTable = 2;
                    }
                }
                if ($intSlaveId == 0) {
                    // Temporary Make entry in the template table
                    $strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                    $booResult = $ccm->db->insertData($strSQL);
                    $intSlaveId = $ccm->db->intLastId;
                    $intTable = 1;
                }
        
                // Submit link
                $strSQL = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId.",`idSort` = ".$intSortNr.", `idTable` = ".$intTable;
                $booResult = $ccm->db->insertData($strSQL);
                $intSortNr++;
        
                // Field data update in main table
                $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1,`".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
                $booResult = $ccm->db->insertData($strSQL);
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link type 4, a (free variables)
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten
    //
    // Return value: 0 for success or 1 for failure
    //  
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_4($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;

        // Check if the variable already exists
        $strSQL = "SELECT `id` FROM `".$arrRelData['linktable']."`
                   LEFT JOIN `tbl_variabledefinition` ON `idSlave` = `id`
                   WHERE `idMaster` = ".intval($intDataId)." AND `name` = '".$ccm->db->escape_string($strKey)."'";
        $booResult = $ccm->db->getSingleDataset($strSQL, $data);

        // Update instead of add
        if (!empty($data)) {

            $intLnkId = $data['id'];
            $strSQL = "UPDATE `tbl_variabledefinition` SET `value` = '".$ccm->db->escape_string($strValue)."', `last_modified` = NOW() WHERE `id` = ".$intLnkId.";";
            $booResult  = $ccm->db->insertData($strSQL);

        } else {

            // Enter values in the Variable Table
            $strSQL   = "INSERT INTO `tbl_variabledefinition` SET `name` = '".$ccm->db->escape_string($strKey)."', `value` = '".$ccm->db->escape_string($strValue)."', `last_modified`=now()";
            $booResult  = $ccm->db->insertData($strSQL);
            $intSlaveId = $ccm->db->intLastId;
            
            // Enter values in the link table
            $strSQL   = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId;
            $booResult  = $ccm->db->insertData($strSQL);
            
            // Field data update in main table
            $strSQL   = "UPDATE `".$strDataTable."` SET `use_variables` = 1 WHERE `id` = ".$intDataId;
            $booResult  = $ccm->db->insertData($strSQL);

        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link from a type 5 (1:1) check_command
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten
    //
    // Return value: 0 for success or 1 for failure
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_5($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;

        $arrCommand = explode("!", $strValue);
        $strValue = $arrCommand[0];
    
        // Define variables
        $intSlaveId = 0;
        if (strtolower(trim($strValue)) == "null") {
            // Field data update in main table
            $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
            $booResult = $ccm->db->insertData($strSQL);
        } else {
            // Data value split
            $arrValues = explode(",", $strValue);
      
            // Execute data values
            foreach ($arrValues AS $elem) {
                // Determine whether the entry already exists
                $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` WHERE `".$arrRelData['target']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                $strId = $ccm->db->getFieldData($strSQL);
                if ($strId != "") {
                    $intSlaveId = $strId+0;
                }
                if ($intSlaveId == 0) {
                    // Temporary Make entry into the target table
                    $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                    $booResult = $ccm->db->insertData($strSQL);
                    $intSlaveId = $ccm->db->intLastId;
                }
                
                // Field data update in main table
                $arrCommand[0] = $intSlaveId;
                $strValue = implode("!", $arrCommand);
                $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = '".$ccm->db->escape_string($strValue)."' WHERE `id` = ".$intDataId;
                $booResult = $ccm->db->insertData($strSQL);
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: add data relation
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Adds a data link of type 5, a (1: n: n) (service groups)
    //
    // Parameters: $ strKey data field
    // $ StrValue data value
    // $ IntDataId data ID
    // $ StrDataTable data table (master)
    // $ ArrRelData Verknüfungsdaten
    //
    // Return value: 0 for success or 1 for failure
    // 
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strKey
     * @param $strValue
     * @param $intDataId
     * @param $strDataTable
     * @param $arrRelData
     */
    function writeRelation_6($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        global $ccm;

        $intSlaveId = 0;
        $intSlaveIdS = 0;
        $intSlaveIdH = 0;

        // Data value split
        $arrValues = explode(",", $strValue);
    
        // Link table delete
        $strSQL = "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
        $booResult = $ccm->db->insertData($strSQL);
    
        // Check that the number of elements is correct
        if (count($arrValues) % 2 != 0) {
            $this->strMessage .= _("Error: wrong number of arguments - cannot import service group members")."<br>";
        } else {
      
            // Execute data values
            $intCounter = 1;
            foreach ($arrValues AS $elem) {
                if ($intCounter % 2 == 0) {
                    
                    // Determine whether the host entry already exists
                    $strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$strValue."' AND `config_id`=".$this->intDomainId;
                    $strId = $ccm->db->getFieldData($strSQL);
                    if ($strId != "") {
                        $intSlaveIdH = $strId+0;
                    }
                    if ($intSlaveIdH == 0) {
                        // Temporary Make entry into the target table
                        $strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$strValue."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                        $booResult = $ccm->db->insertData($strSQL);
                        $intSlaveIdH = $ccm->db->intLastId;
                    }
            
                    // Determine whether the service record exists
                    $strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."`
                               LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `idMaster`
                               WHERE `".$arrRelData['target2']."` = '".$elem."' AND `idSlave` = ".$intSlaveIdH." AND `config_id`=".$this->intDomainId;
                    $strId = $ccm->db->getFieldData($strSQL);
                    if ($strId != "") {
                        $intSlaveIdS = $strId+0;
                    }
                    if ($intSlaveIdS == 0) {
                        // Temporary Make entry into the target table
                        $strSQL = "INSERT INTO `".$arrRelData['tableName2']."` SET `".$arrRelData['target2']."` = '".$strValue."',`config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                        $booResult = $ccm->db->insertData($strSQL);
                        $intSlaveIdS = $ccm->db->intLastId;
                    }
          
                    // Submit link
                    $strSQL = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlaveH` = ".$intSlaveIdH.", `idSlaveS` = ".$intSlaveIdS;
                    $booResult = $ccm->db->insertData($strSQL);
                    
                    // Field data update in main table
                    $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1 WHERE `id` = ".$intDataId;
                    $booResult = $ccm->db->insertData($strSQL);
                } else {
                    $strValue = $elem;
                }
                $intCounter++;
            }
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////
    // Function: integrate template
    ////////////////////////////////////////////////// /////////////////////////////////////////
    //
    // Version: 2:00:00 (Internal)
    // Date: 12.03.2007
    //
    // Data for a specific template integrated in the import data arrays
    //
    // Parameters: strFileName import $ filename
    // $ StrTemplate name of the template
    //
    // Return value: 0 for success or 1 for failure
    // Return: data array with added template variables
    // 
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param $strFileName
     * @param $strTemplate
     * @param $arrData
     *
     * @return int
     */
    function insertTemplate($strFileName, $strTemplate, &$arrData)
    {
        // Declaring variables
        $intBlock = 0;
        $intCheck = 0;
        $intIsTemplate = 0;
    
        // Configuration file to open and read line by line
        $resTplFile = fopen($strFileName,"r");
        while(!feof($resTplFile)) {
            $strConfLine = fgets($resTplFile, 1024);
            $strConfLine = trim($strConfLine);
      
            // Comment lines and blank lines pass
            if (substr($strConfLine, 0, 1) == "#") continue;
            if ($strConfLine == "") continue;
            if (($intBlock == 1) && ($strConfLine == "{")) continue;
      
            // Line process (reduce space and comments cut)
            $arrLine = preg_split("/[\s]+/", $strConfLine);
            $arrTemp = explode(";", implode(" ", $arrLine));
            $strNewLine = trim($arrTemp[0]);
      
            // Block start search
            if ($arrLine[0] == "define") {
                $intBlock = 1;
                $strBlockKey = str_replace("{", "", $arrLine[1]);
                if (($strBlockKey == "command") && (substr_count($strFileName, "misccommand") != 0)) { $strBlockKey = "misccommand"; }
                if (($strBlockKey == "command") && (substr_count($strFileName, "checkcommand") != 0)) { $strBlockKey = "checkcommand"; }
                $arrDataTpl = "";
                continue;
            }

            // Block data stored in an array
            if (($intBlock == 1) && ($arrLine[0] != "}")) {
                if (($arrLine[0] == "name") && (str_replace($arrLine[0]." ", "", $strNewLine) == $strTemplate)) { $intIsTemplate = 1; }
                if (($arrLine[0] != "name") && ($arrLine[0] != "register")) {
                    $arrDataTpl[$arrLine[0]] = str_replace($arrLine[0]." ", "", $strNewLine);
                }
            }

            // Process at end of block data
            if (substr_count($strConfLine, "}") == 1)  {
                $intBlock = 0;
                
                // Template in Data array insert
                if ($intIsTemplate) {
                    foreach($arrDataTpl AS $key => $value) {
                        $arrData[$key] = array("key" => $key, "value" => $value);
                    }
                    return(0);
                }
            }
        }
        return(1);
    }

    // Data validation to check for names that are valid for Core/XI
    /**
     * @param $str_block_key
     * @param $arr_data
     *
     * @return bool|string
     */
    function invalid_name_check($str_block_key, $arr_data)
    {
        $illegal_chars = ccm_get_nagioscore_config_option('illegal_object_name_chars');
        if (empty($illegal_chars)) {
            $illegal_chars = "` ~ ! $ % ^ & * \" ' | < > ? ( ) =";
        } else {
            $illegal_chars = rtrim(chunk_split($illegal_chars, 1, " "));
        }

        # Split and remove , since it splits on it
        $arr_illegal_chars = explode(" ", $illegal_chars);
        while (($comma_key = array_search(',', $arr_illegal_chars)) !== false) {
            unset($arr_illegal_chars[$comma_key]);
        }

        if ($str_block_key == "host") {
            if (array_key_exists('host_name', $arr_data)) {
                if ($this->strposa($arr_data['host_name']['value'], $arr_illegal_chars)) {
                    return '<b>' . $arr_data['host_name']['value'] . '</b> - ' . _("You must enter a proper host name. You can not use the characters: \\~!$%^&*\"'|<>?()=");
                }
            }
        } else if ($str_block_key == "service") {
            if (array_key_exists('service_description', $arr_data)) {
                $type = 'service_description';
                $object = $arr_data[$type]['value'];
            } else if (array_key_exists('host_name', $arr_data)) {
                $type = 'host_name';
                $object = $arr_data[$type]['value'];
            } else {
                return false;
            }

            if ($this->strposa($object, $arr_illegal_chars)) {
                return '<b>' . $object . '</b> - ' . _("You must enter a proper $type. You can not use the characters: \\~!$%^&*\"'|<>?()=");
            }
        }

        return false;
    }

    // Get the string position of an array of characters
    /**
     * @param     $haystack
     * @param     $needle
     * @param int $offset
     *
     * @return bool
     */
    function strposa($haystack, $needle, $offset=0) {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $query) {
            if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
        }
        return false;
    }

}