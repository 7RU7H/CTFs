<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//  Copyright (c) 2008, 2009 Martin Willisegger
//
//  File: data.class.php
//  Desc: Handles finding relationships and grabbing object data
//
//  $strDBMessage  Releases of the database server
//


class Data
{

    // Declare class variables
    var $intDomainId = 0;   // Is filled in the class
    var $strDBMessage = ""; // Classes will be filled internally
    var $lastCopyID = 0;    // Last ID of copied object
    var $debug = false;     // Turn on debug logging

    // Stores the table name and id of the related/dependent object
    // Used for updating the last_modified time for services and hosts
    // when a related config name is changed - forcing the ccm to write
    // the related host and service files to disk.
    var $arrDBIds = array();

    // Stores information about the dependant relationships in array format
    var $arrRR = array();
    var $arrInactive = array();
    var $hasDepRels = false;

    /**
     * Constructor for the data class. You can pass a configuration
     * array or the config array will be pulled from the session.
     * (Can someone explain why $CFG is passed as a session value... and named SETS?)
     *
     * @param   array   $config     The configuration array (optional)
     * @param   int     $domain     For multiple domain configs (optional - not used)
     */
    function __construct($config=null, $domain=1)
    {
        // Set domain (not currently used for anything)
        $this->intDomainId = $domain;
        if (isset($_SESSION['domain'])) {
            $this->intDomainId = $_SESSION['domain'];
        }
    }


    /**
     * Write data to the database
     *
     * Sends the given string to the SQL database server and evaluates the return
     * of the server.
     *
     * @param   string  $strSQL         SQL command
     * @param   int     $intDataID      ID of the last inserted record
     * @return  int                     0 on success, 1 on failure
     */
    function dataInsert($strSQL, &$intDataID)
    {
        global $ccm;

        // Send data to the database server
        $boolReturn = $ccm->db->insertData($strSQL);
        $intDataID = $ccm->db->intLastId;

        // Could the record be inserted successfully?
        if ($boolReturn == true) {
            // Success
            $this->strDBMessage = _('Data successfully inserted into the database!');
            return(0);
        } else {
            // Failure
            $this->strDBMessage = _('Error while inserting data into the database:')."<br>".$ccm->db->strDBError;
            return(1);
        }
    }


    /**
     * -------- NOT USED anywhere -------- TODO: delete -PhW 8/4/21
     * Deletes a record or multiple records from a data table. Alternatively,
     * A single record ID to be specified, or the values ​​of ['chbId_n'] with $ _POST
     * Parameters passed are evaluated, where "n" is the record ID must match.
     *
     * This function only deletes the data from a single table!
     *
     * @param   string  $strTableName   Table name
     * @param   string  $strKeyField    Field name that contains the record ID
     * @param   int     $intDataId      Individual record ID to be deleted
     * @param   int     $intTableId     Tables in special relationships (templates)
     * @param   bool    $audit
     * @return  int                     0 on success, 1 on failure
     */
    function dataDeleteEasy($strTableName, $strKeyField, $intDataId = 0, $intTableId = 0, $audit=true)
    {
        global $ccm;

        $this->strDBMessage = "";
    
        // Special rule for tables with "nodelete" cells
        if (($strTableName == "tbl_domain") || ($strTableName == "tbl_user")) {
            $strNoDelete = "AND `nodelete` <> '1'";
        } else {
            $strNoDelete = "";
        }
    
        // Special rule for template link table
        if ($intTableId != 0) {
            $strTableId = "AND `idTable` = ".intval($intTableId);
        } else {
            $strTableId = "";
        }

        // Delete a single record
        if ($intDataId != 0) {

            // For hosts also delete the configuration file
            if ($strTableName == "tbl_host") {
                $strSQL = "SELECT `host_name` FROM `tbl_host` WHERE `id` = ".intval($intDataId);
                $strHost = $ccm->db->getFieldData($strSQL);

                $intReturn = $ccm->config->moveFile("host", $strHost.".cfg");
                if ($intReturn == 0) {
                    $this->strMessage .=  _('The assigned, no longer used configuration files were deleted successfully!');
                    $this->writeLog(_('Host file deleted:')." ".$strHost.".cfg");
                } else {
                    $this->strMessage .=  _('Errors while deleting the old configuration file - please check!:')."<br>".$ccm->config->strDBMessage;
                }
            }

            // If service, also delete the service configuration file
            if ($strTableName == "tbl_service") {
                $strSQL = "SELECT `config_name` FROM `tbl_service` WHERE `id` = ".intval($intDataId);
                $strService = $ccm->db->getFieldData($strSQL);
                $strSQL = "SELECT * FROM `tbl_service` WHERE `config_name` = '$strService'";
                $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
                if ($intDataCount == 1) {
                    $intReturn = $ccm->config->moveFile("service", $strService.".cfg");
                    if ($intReturn == 0) {
                        $this->strMessage .=  _('The assigned, no longer used configuration files were deleted successfully!');
                        $this->writeLog(_('Host file deleted:')." ".$strService.".cfg");
                    } else {
                        $this->strMessage .=  _('Errors while deleting the old configuration file - please check!:')."<br>".$ccm->config->strDBMessage;
                    }
                }
            }

            $strSQL = "DELETE FROM `".$strTableName."` WHERE `".$strKeyField."` = ".intval($intDataId)." $strNoDelete $strTableId";
            $booReturn = $ccm->data->insertData($strSQL);
      
            // Error handling
            if ($booReturn == false) {
                $this->strDBMessage .= _('Delete failed because a database error:')."<br>".$ccm->db->error;
                return(1);
            } else if ($ccm->data->intAffectedRows == 0) {
                //$this->strDBMessage .= _('No data deleted. Probably the dataset does not exist or it is protected from delete.');
                return(0);
            } else {
                $this->strDBMessage .= _('Dataset successfully deleted. Affected rows:')." ".$ccm->data->intAffectedRows;
                $this->writeLog(_('Delete dataset id:')." $intDataId "._('- from table:')." $strTableName "._('- with affected rows:')." ".$ccm->data->intAffectedRows);
                return(0);
            }

        } else {
            
            // Delete multiple records
            $strSQL = "SELECT `id` FROM `".$strTableName."` WHERE 1=1 $strNoDelete";
            $booReturn = $ccm->data->getDataArray($strSQL, $arrData, $intDataCount);
            if ($intDataCount != 0) {
                $intDeleteCount = 0;
                foreach ($arrData AS $elem) {
                    $strChbName = "chbId_".$elem['id'];
                    
                    // The current record has been marked for deletion?
                    if (isset($_POST[$strChbName]) && ($_POST[$strChbName] == "on")) {
                        
                        // For hosts also delete the configuration file
                        if ($strTableName == "tbl_host") {
                            $strSQL = "SELECT `host_name` FROM `tbl_host` WHERE `id` = ".intval($elem['id']);
                            $strHost = $ccm->data->getFieldData($strSQL);
                            $intReturn = $ccm->config->moveFile("host", $strHost.".cfg");
                            if ($intReturn == 0) {
                                if ($intDeleteCount == 0) {
                                    $this->strMessage .=  _('The assigned, no longer used configuration files were deleted successfully!');
                                }
                                $this->writeLog(_('Host file deleted:')." ".$strHost.".cfg");
                            } else {
                                $this->strMessage .=  _('Errors while deleting the old configuration file - please check!:')."<br>".$ccm->config->strDBMessage;
                            }
                        }

                        // Delete services and the configuration file
                        if ($strTableName == "tbl_service") {
                          $strSQL = "SELECT `config_name` FROM `tbl_service` WHERE `id` = ".intval($elem['id']);
                          $strService = $ccm->db->getFieldData($strSQL);
                          $strSQL = "SELECT * FROM `tbl_service` WHERE `config_name` = '$strService'";
                          $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
                          if ($intDataCount == 1) {
                                $intReturn = $ccm->config->moveFile("service", $strService.".cfg");
                                if ($intReturn == 0) {
                                    if ($intDeleteCount == 0) {
                                        $this->strMessage .=  _('The assigned, no longer used configuration files were deleted successfully!');
                                    }
                                    $this->writeLog(_('Service file deleted:')." ".$strService.".cfg");
                                } else {
                                    $this->strMessage .=  _('Errors while deleting the old configuration file - please check!:')."<br>".$ccm->config->strDBMessage;
                                }
                            }
                        }

                        $strSQL = "DELETE FROM `".$strTableName."` WHERE `".$strKeyField."` = ".intval($elem['id'])." $strTableId";
                        $booReturn = $ccm->db->insertData($strSQL);
            
                        // Error handling
                        if ($booReturn == false) {
                            $this->strDBMessage .= _('Delete failed because a database error:')."<br>".$ccm->db->error;
                            return(1);
                        } else {
                            $intDeleteCount = $intDeleteCount + $ccm->data->intAffectedRows;
                        }
                    }
                }
        
                // Mitteilungen ausgeben
                if ($intDeleteCount == 0) {
                    //$this->strDBMessage .= _('No data deleted. Probably the dataset does not exist or it is protected from delete.');
                    return(0);
                } else {
                    $this->strDBMessage .= _('Dataset successfully deleted. Affected rows:')." ".$intDeleteCount;
                    $this->writeLog(_('Delete data from table:')." $strTableName "._('- with affected rows:')." ".$ccm->data->intAffectedRows);
                    return(0);
                }

            } else {
                $this->strDBMessage .= _('No data deleted. Probably the dataset does not exist or it is protected from delete.');
                return(1);
            }
        }
    }


    /**
     * Deletes an object and all of it's relations
     *
     * @param   string  $strTableName   Object's table name without tbl_
     * @param   int     $intDataId      Unique record ID to be deleted
     * @param   int     $intForce       Force delete even if dependencies exist (not used)
     * @param   bool    $audit          True to send to audit log (default)
     * @return  int                     Return value: 0 for success or 1 for failure
     */
    function dataDeleteFull($strTableName, $intDataId=0, $intForce=0, $audit=true)
    {
        global $ccm;
        $protected = false;
        $type = str_replace('tbl_', '', $strTableName);

        // Find all DB relationships     
        $this->fullTableRelations($strTableName, $arrRelations);
        $columns = $this->getKeyField($type) . ',' . $this->getKeyField($type, true);

        // Check for item existence 
        $strSQL = "SELECT ".$columns." FROM `".$strTableName."` WHERE `id` = ".intval($intDataId).";";
        $exists = $ccm->db->getSingleDataset($strSQL, $object);

        // We have to get a full relationship check prior to running the purgeRelations funciton
        // so that we know what items to update later on to re-write the config files, this data is stored
        // in the arrDBIds variable of this object
        $this->infoRelation($strTableName, $intDataId, "id");

        // Log the deletion of the item
        if ($type == 'service') {
            $msg = _("Deleted") . " " . ccm_get_full_title($type, false, false) . ": " . $object['service_description'] . ' (' . _('from config') . ' ' . $object['config_name'] . ')';
        } else {
            $msg = _("Deleted") . " " . ccm_get_full_title($type, false, false) . ": " . $object[$this->getKeyField($type)];
        }
        $this->writeLog($msg, AUDITLOGTYPE_DELETE);

        // Must have a valid id and exist in the DB
        if ($intDataId != 0 && $exists)
        {
            $intDeleteCount = 0;
            $intFileRemoved = 0;
            $strFileMessage = "";

            // Handle file removal for hosts and services
            if ($strTableName == 'tbl_host' || $strTableName == 'tbl_service') {
                list($fileReturn, $strFileMessage) = $this->handleFiles($intDataId, $strTableName);
            }

            // Clear any existing relations
            $this->purgeRelations($intDataId, $strTableName, $arrRelations);
            $strSQL = "DELETE FROM `".$strTableName."` WHERE `id` = ".intval($intDataId)." LIMIT 1;";
            $booReturn = $ccm->db->insertData($strSQL);
            if ($booReturn == 1) { $intDeleteCount++; }

            // Return output
            if ($intDeleteCount == 0) {
                $this->strDBMessage .= '<b>'._('Object was not deleted.').'</b>';
                return 1;
            } else {
                $this->strDBMessage .= _('Object deleted successfully.');
                return 0;
            }
        }

        $this->strDBMessage .= "<b>"._('Invalid object.')."</b> "._('The object with the ID given may not exist.')." <b>(ID: ".intval($intDataId).")</b>";
        return 1;
    }


    /**
     * Removes object's relationships from the database
     *
     * @param   int     $intDataId
     * @param   string  $strTableName
     * @param   array   $arrRelations
     * @param   int     $intDataCount
     * @param   array   $arrData
     */
    function purgeRelations($intDataId, $strTableName, $arrRelations, &$intDataCount=1, &$arrData=array())
    {
        global $ccm;
        $this->strDBMessage = "";

        // Delete relations
        foreach ($arrRelations as $rel) {
            $strSQL = "";

            // Dissolve flags
            $arrFlags = explode(",", $rel['flags']);

            if ($arrFlags[3] == 1) { //1:n

                // Special relationship for hosts -> services, escalations etc. that will delete the parent objects
                // if the objects do not have any linkage to other objects
                $specialRelationTables = array('tbl_lnkServiceToHost', 'tbl_lnkHostescalationToHost');

                if ($strTableName == 'tbl_host' && in_array($rel['tableName'], $specialRelationTables)) {

                    // Loop through the relationships
                    $strSQL = "SELECT id, config_name, host_name, hostgroup_name FROM `".$rel['tableName']."` LEFT JOIN ".$rel['target']." ON id = idMaster WHERE `".$rel['fieldName']."`=".intval($intDataId);
                    $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
                    if ($intDataCount != 0) {
                        foreach ($arrData as $vardata) {
                            if ($vardata['host_name'] == 1) {

                                // Check if we have multiple hosts (remove only the one if we do)
                                $strSQL = "SELECT * FROM `".$rel['tableName']."` WHERE idMaster = ".$vardata['id'];
                                $booReturn = $ccm->db->getDataArray($strSQL, $arrDataRels, $intDataCountRels);
                                if ($intDataCountRels == 1 && $vardata['hostgroup_name'] == 0) {
                                    $this->dataDeleteFull($rel['target'], $vardata['id']);
                                } else {

                                    // Remove just the one relationship
                                    $strSQL = "DELETE FROM `".$rel['tableName']."` WHERE idMaster = " . intval($vardata['id']) . " AND idSlave = " . intval($intDataId);
                                    $booReturn = $ccm->db->insertData($strSQL);

                                    // Update the last_modified for the service
                                    $strSQL = "UPDATE `".$rel['target']."` SET `last_modified`=NOW() WHERE id = ".intval($vardata['id']);
                                    $booReturn = $ccm->db->insertData($strSQL);

                                }

                            }
                        }
                    }

                } else {
                    $strSQL = "DELETE FROM `".$rel['tableName']."` WHERE `".$rel['fieldName']."`=".intval($intDataId);
                }
            }

            if ($arrFlags[3] == 0)  {
                if ($arrFlags[2] == 0) {
                    $strSQL = "DELETE FROM `".$rel['tableName']."` WHERE `".$rel['fieldName']."`=".intval($intDataId);
                }
                if ($arrFlags[2] == 2) {
                    $strSQL = "UPDATE `".$rel['tableName']."` SET `".$rel['fieldName']."`=0 WHERE `".$rel['fieldName']."`=".intval($intDataId);
                }
            }

            if ($arrFlags[3] == 2) {
                $strSQL = "SELECT * FROM `".$rel['tableName']."` WHERE `".$rel['fieldName']."`=".intval($intDataId);
                $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);
                if ($intDataCount != 0) {
                    foreach ($arrData as $vardata) {
                        $strSQL = "DELETE FROM ".$rel['target']." WHERE `id`=".$vardata['idSlave'];
                        $booReturn = $ccm->db->insertData($strSQL);
                    }
                }
                $strSQL = "DELETE FROM `".$rel['tableName']."` WHERE `".$rel['fieldName']."`=".intval($intDataId);
            }

            if ($arrFlags[3] == 3) {
                $strSQL = "DELETE FROM `tbl_timedefinition` WHERE `tipId`=".intval($intDataId);
                $booReturn = $ccm->db->insertData($strSQL);
            }

            if ($strSQL != "") {
                $booReturn = $ccm->db->insertData($strSQL);
            }
        }
    }


    /**
     * @param   int     $intDataId
     * @param   string  $strTableName
     * @return  array
     */
    function handleFiles($intDataId, $strTableName)
    {
        global $ccm;
        $strFileMessage = '';
        $intFileRemoved = 1;

        // Delete the host configuration file
        if ($strTableName == "tbl_host") {
            $strSQL = "SELECT `host_name` FROM `tbl_host` WHERE `id`=".intval($intDataId);
            $strHost = $ccm->db->getFieldData($strSQL);
            $intReturn = $ccm->config->moveFile("host", $strHost.".cfg");
            if ($intReturn == 0) {
                $intFileRemoved = 1;
                if ($this->debug) {
                    $strFileMessage .=  "<br />"._('Host file').': <strong>'.$strHost.'.cfg</strong> '._('was deleted').'<br />';
                }
            } else {
                $intFileRemoved = 2;
                $strFileMessage .=  "<br><span class='dependency'>"._('Errors while deleting the old configuration file - please check!:')."</span><br>".$ccm->config->strDBMessage;
            }
        }

        // Delete the service configuration file so it's rewritten
        if ($strTableName == "tbl_service") {
            $strSQL = "SELECT `config_name` FROM `tbl_service` WHERE `id`=".intval($intDataId);
            $strService = $ccm->db->getFieldData($strSQL);
            $intReturn = $ccm->config->moveFile("service", $strService.".cfg");
            if ($intReturn == 0) {
                $intFileRemoved = 1;
                if ($this->debug) {
                    $strFileMessage .=  "<br />"._('Service file').': <strong>'.$strService.'.cfg</strong> '._('was deleted successfully!').'<br />';
                }
            } else {
                $intFileRemoved = 2;
                $strFileMessage .=  "<br>"._('Errors while deleting the old configuration file - please check!').":<br>".$ccm->config->strDBMessage;
            }
        }

        return array($intFileRemoved, $strFileMessage);
    }


    /**
     * Get key fields, fetches the appropriate name or description field for the object $type
     *
     * @param   string  $type   Object type (host, service, contact, etc)
     * @param   bool    $desc   Boolean to return either the name or the description field
     * @return  string          Field name (with description if $desc true) or false
     */
    function getKeyField($type, $desc=false)
    {
        switch ($type)
        {

            case 'host':
            case 'hostgroup':
            case 'servicegroup':
            case 'contact':
            case 'contactgroup':
            case 'timeperiod':
                $typeName = $type.'_name';
                $typeDesc = 'alias';
                break;

            case 'hostdependency':
            case 'hostescalation':
            case 'servicedependency':
            case 'serviceescalation':
            case 'service':
                $typeName = 'config_name';
                $typeDesc = 'service_description';
                if ($type == 'hostdependency' || $type == 'hostescalation' || $type == 'serviceescalation') {
                    $typeDesc = 'config_name';
                }
                break;

            case 'command':
                $typeName = 'command_name';
                $typeDesc = 'command_line';
                break;

            case 'servicetemplate': 
            case 'hosttemplate': 
            case 'contacttemplate':
                $typeName = 'template_name';
                $typeDesc = 'alias';
                if ($type == 'servicetemplate') { $typeDesc = 'display_name'; }
                break;
            
            default:
                return false;
                break;
        }

        // Return either name or description field 
        if ($desc) {
            return $typeDesc;
        } else {
            return $typeName;
        }
    }


    /**
     * Copy one or more records in a data table. Optionally, a
     * Parameters passed are evaluated, where "n" is the record ID must match.
     *
     * @param   string  $strTableName   Table name
     * @param   string  $strKeyField    The key field of table
     * @param   int     $intDataId      Individual record ID to be copied
     * @return  int                     0 on success, 1 on failure
     */
    function dataCopyEasy($strTableName, $strKeyField, $intDataId = 0)
    {
        global $ccm;
        $intError = 0;
        $intNumber= 0;
        $this->strDBMessage = "";

        // All record IDs of the target table query
        $booReturn = $ccm->db->getDataArray("SELECT `id` FROM `".$strTableName."` ORDER BY `id`", $arrData, $intDataCount);
        if ($booReturn == false) {
            $this->strDBMessage = _('Error while selecting data from database:')."<br>".$ccm->db->strDBError."<br>";
            return 1;
        } else if ($intDataCount != 0) {
            
            // Records returned
            for ($i = 0; $i < $intDataCount; $i++) {
                
                // Form transfer parameters compose
                $strChbName = "chbId_".$arrData[$i]['id'];
        
                // If provided with a $ _POST parameter mountain just with this name or explicitly, this ID
                if ($intDataId == 0 || $intDataId == $arrData[$i]['id']) {
          
                    // Data entry of the corresponding fetch
                    $ccm->db->getSingleDataset("SELECT * FROM `".$strTableName."` WHERE `id`=".$arrData[$i]['id'], $arrData[$i]);
                    
                    // Suffix create
                    for ($y = 1; $y <= $intDataCount; $y++) {
                        $strNewName = $arrData[$i][$strKeyField]."_copy_$y";
                        $booReturn = $ccm->db->getFieldData("SELECT `id` FROM `".$strTableName."` WHERE `".$strKeyField."`='".$ccm->db->escape_string($strNewName)."'");
                        if ($booReturn == false) { break; } // If the new name is unique to cancel
                    }

                    // According assemble the table name with the database insert command
                    $strSQLInsert = "INSERT INTO `".$strTableName."` SET `".$strKeyField."`='".$ccm->db->escape_string($strNewName)."',";
                    foreach ($arrData[$i] AS $type => $value) {
                        if ($type != $strKeyField && $type != "active" && $type != "last_modified" && $type != "id") {
                            // NULL Depreciations field data set
                            if (($type == "normal_check_interval") && ($value == "")) $value="NULL";
                            if (($type == "retry_check_interval") && ($value == "")) $value="NULL";
                            if (($type == "max_check_attempts") && ($value == "")) $value="NULL";
                            if (($type == "low_flap_threshold") && ($value == "")) $value="NULL";
                            if (($type == "high_flap_threshold") && ($value == "")) $value="NULL";
                            if (($type == "freshness_threshold") && ($value == "")) $value="NULL";
                            if (($type == "notification_interval") && ($value == "")) $value="NULL";
                            if (($type == "first_notification_delay")&& ($value == "")) $value="NULL";
                            if (($type == "check_interval") && ($value == "")) $value="NULL";
                            if (($type == "retry_interval") && ($value == "")) $value="NULL";
                            if (($type == "access_rights") && ($value == "")) $value="NULL";
                            
                            // NULL Values ​​set by table name
                            if (($strTableName == "tbl_hostextinfo") && ($type == "host_name")) $value="NULL";
                            if (($strTableName == "tbl_serviceextinfo") && ($type == "host_name")) $value="NULL";
              
                            // Password for user copied not Apply
                            if (($strTableName == "tbl_user") && ($type == "password")) $value="xxxxxxx";
              
                            // Erase protection / Webserverauthentification not accept
                            if ($type == "nodelete") $value = "0";
                            if ($type == "wsauth") $value = "0";

                            // Set special value for timeperiods (we will remove timeperiod_name at some point)
                            if (($strTableName == "tbl_timeperiod") && ($type == "name") && !empty($value)) {
                                $value = $value."_copy_$y";
                            }
              
                            // If the data value is not "NULL", include the data value in quotes
                            if ($value != "NULL") {
                                $strSQLInsert .= "`".$type."`='".addslashes($value)."',";
                            } else {
                                $strSQLInsert .= "`".$type."`=".$value.",";
                            }
                        }
                    }
          
                    $strSQLInsert .= "`active`='0', `last_modified`=NOW()";
          
                    // Copy into the database
                    $intCheck = 0;
                    $booReturn = $ccm->db->insertData($strSQLInsert);
                    $intMasterId = $ccm->db->intLastId;
                    $this->lastCopyID = $intMasterId;
                    if ($booReturn == false) { $intCheck++; }

                    // Copy any existing relationships
                    if (($this->tableRelations($strTableName, $arrRelations) != 0) && ($intCheck == 0)) {
                        foreach ($arrRelations AS $elem) {
                            if (($elem['type'] != "3") && ($elem['type'] != "5") && ($elem['type'] != "1")) {
                
                                // Field is not set to "None" or "*"?
                                if ($arrData[$i][$elem['fieldName']] == 1) {
                                    $extra = "";
                                    if (array_key_exists('excludes', $elem) && $elem['excludes'] == 1) {
                                        $extra = ",`exclude`";
                                    }
                                    $strSQL = "SELECT `idSlave`".$extra." FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrData[$i]['id'];
                                    $booReturn = $ccm->db->getDataArray($strSQL, $arrRelData, $intRelDataCount);
                                    if ($intRelDataCount != 0) {
                                        for ($y=0; $y < $intRelDataCount; $y++) {
                                            if ($elem['type'] == 4) { // Special case for custom variables 
                                                // Clone the variable itself
                                                $strSQL = "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`) 
                                                           SELECT `name`,`value`,`last_modified` FROM tbl_variabledefinition WHERE id=".$arrRelData[$y]['idSlave'];
                                                $booReturn = $ccm->db->insertData($strSQL); 
                                                $id = $ccm->db->intLastId;
                                                if (!empty($id)) {
                                                    $strSQLRel = "INSERT INTO `".$elem['linktable']."` SET `idMaster`=$intMasterId, `idSlave`=".$id;
                                                    $booReturn = $ccm->db->insertData($strSQLRel);    
                                                } else {
                                                    $booReturn = false; 
                                                }
                                            } else {
                                                $extra_insert = "";
                                                if (array_key_exists('excludes', $elem) && $elem['excludes'] == 1) {
                                                    $extra_insert = ",`exclude`=".$arrRelData[$y]['exclude'];
                                                }
                                                $strSQLRel = "INSERT INTO `".$elem['linktable']."` SET `idMaster`=$intMasterId, `idSlave`=".$arrRelData[$y]['idSlave'].$extra_insert;
                                                $booReturn = $ccm->db->insertData($strSQLRel);
                                            }

                                            if ($booReturn == false) { $intCheck++; }                 
                                        }
                                    }
                                }
                            } else if (($elem['type'] != "5") && ($elem['type'] != "1") &&($elem['type'] != 4)) {
                                // Field is not set to "None" or "*"?
                                // XI MOD - Fixed variable copying
                                if ($arrData[$i][$elem['fieldName']] == 1) {
                                    $strSQL = "SELECT `idSlave`,`idSort`,`idTable` FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrData[$i]['id'];
                                    $booReturn = $ccm->db->getDataArray($strSQL, $arrRelData, $intRelDataCount);
                                    if ($intRelDataCount != 0) {
                                        for ($y=0; $y < $intRelDataCount; $y++) {
                                            $strSQLRel = "INSERT INTO `".$elem['linktable']."` SET `idMaster`=$intMasterId, `idSlave`=".$arrRelData[$y]['idSlave'].",`idTable`=".$arrRelData[$y]['idTable'].", `idSort`=".$arrRelData[$y]['idSort'];
                                            $booReturn   = $ccm->db->insertData($strSQLRel);
                                            if ($booReturn == false) { $intCheck++; }
                                        }
                                    }
                                }
                            } else if ($elem['type'] != "1") {
                                // Field is not set to "None" or "*"?
                                if ($arrData[$i][$elem['fieldName']] == 1) {
                                    $strSQL = "SELECT `idSlaveH`,`idSlaveHG`,`idSlaveS` FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrData[$i]['id'];
                                    $booReturn = $ccm->db->getDataArray($strSQL, $arrRelData, $intRelDataCount);
                                    if ($intRelDataCount != 0) {
                                        for ($y=0; $y < $intRelDataCount; $y++) {
                                            $strSQLRel = "INSERT INTO `".$elem['linktable']."` SET `idMaster`=$intMasterId, `idSlaveH`=".$arrRelData[$y]['idSlaveH'].",`idSlaveHG`=".$arrRelData[$y]['idSlaveHG'].",`idSlaveS`=".$arrRelData[$y]['idSlaveS'];
                                            $booReturn = $ccm->db->insertData($strSQLRel);
                                            if ($booReturn == false) { $intCheck++; }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Under Copy table values ​​at tbl_timeperiod
                    if ($strTableName == "tbl_timeperiod") {
                        $strSQL = "SELECT * FROM `tbl_timedefinition` WHERE `tipId`=".$arrData[$i]['id'];
                        $booReturn = $ccm->db->getDataArray($strSQL, $arrRelDataTP, $intRelDataCountTP);
                        if ($intRelDataCountTP != 0) {
                            foreach ($arrRelDataTP AS $elem) {
                                $strSQLRel = "INSERT INTO `tbl_timedefinition` (`tipId`,`definition`,`range`,`last_modified`) VALUES ($intMasterId,'".$elem['definition']."','".$elem['range']."',now())";
                                $booReturn = $ccm->db->insertData($strSQLRel);
                                if ($booReturn == false) { $intCheck++; }
                            }
                        }
                    }

                    // Write Log (1 is error, 0 is okay)
                    $type = str_replace('tbl_', '', $strTableName);
                    $columns = $this->getKeyField($type) . ',' . $this->getKeyField($type, true);
                    $strSQL = "SELECT ".$columns." FROM `tbl_".$type."` WHERE `id`='".$intMasterId."';";
                    $exists = $ccm->db->getSingleDataset($strSQL, $object);

                    if ($intCheck != 0) {
                        $intError++;
                        $msg = _("Copy object failed to create");
                    } else {
                        $msg = _("Copy object created");
                    }
                    $intNumber++;

                    if ($type == 'service') {
                        $msg .= " " . ccm_get_full_title($type, false, false) . ": " . $object['service_description'] . ' (' . _('from config') . ' ' . $object['config_name'] . ')';
                    } else {
                        $msg .= " " . ccm_get_full_title($type, false, false) . ": " . $object[$this->getKeyField($type)];
                    }

                    $this->writeLog($msg, AUDITLOGTYPE_ADD);
                }
            }
        }

        // Return data (1 is error, 0 is okay)
        if ($intNumber > 0) {
            if ($intError == 0) {
                $this->strDBMessage = _("Data successfully inserted to the database! Object <strong>$strNewName</strong> created.");
                return 0;
            } else {
                $this->strDBMessage = _('Error while inserting the data to the database:')."<br>".$ccm->db->strDBError;
                return 1;
            }
        }
    }


    /**
     * Saves the given string in the log file
     *
     * @param   string  $str_message    Audit log message
     * @param   bool    $audit_type     Log type
     * @return  int                     0 if success, 1 if false
     */
    function writeLog($str_message, $audit_type=AUDITLOGTYPE_MODIFY)
    {
        global $ccm;

        // Get address if not a CLI subsystem call
        $address = '127.0.0.1';
        if (!defined('SUBSYSTEM')) {
            $address = $_SERVER["REMOTE_ADDR"];
        }

        // Log message string in db
        $strUserName = (isset($_SESSION['username']) && ($_SESSION['username'] != "")) ? $_SESSION['username'] : "unknown";
        $strDomain = $ccm->db->getFieldData("SELECT `domain` FROM `tbl_domain` WHERE `id`=".intval($this->intDomainId));
        $booReturn = $ccm->db->insertData("INSERT INTO `tbl_logbook` SET `user`='".$ccm->db->escape_string($strUserName)."',`time`=NOW(), `ipadress`='".$ccm->db->escape_string($address)."', `domain`='".$ccm->db->escape_string($strDomain)."', `entry`='".$ccm->db->escape_string(utf8_encode($str_message))."'");

        // Send to XI audit log
        audit_log($audit_type, $str_message);

        return intval($booReturn);
    }


    /**
     * Returns a list of all the data fields in a table that have a 1:1 or 1:n
     *
     * @param   string  $strTable       Table name
     * @param   array   $arrRelations   Array of relationships
     * @return  int                     0 if no relationships, 1 if relationships
     */
    function tableRelations($strTable, &$arrRelations)
    {
        $arrRelations = array();
        switch ($strTable) {
            
            case "tbl_command":
                return(0);

            case "tbl_timeperiod":
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "exclude",
                                        'target' => "timeperiod_name",
                                        'linktable' => "tbl_lnkTimeperiodToTimeperiod",
                                        'type' => 2);
                return(1);

            case "tbl_contact":
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "host_notification_commands",
                                        'target' => "command_name",
                                        'linktable' => "tbl_lnkContactToCommandHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "service_notification_commands",
                                        'target' => "command_name",
                                        'linktable' => "tbl_lnkContactToCommandService",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contactgroups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkContactToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "host_notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "service_notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_contacttemplate",
                                        'tableName2' => "tbl_contact",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkContactToContacttemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target' => "name",
                                        'linktable' => "tbl_lnkContactToVariabledefinition",
                                        'type' => 4);
                return(1);

            case "tbl_contacttemplate":
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "host_notification_commands",
                                        'target' => "command_name",
                                        'linktable' => "tbl_lnkContacttemplateToCommandHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "service_notification_commands",
                                        'target' => "command_name",
                                        'linktable' => "tbl_lnkContacttemplateToCommandService",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contactgroups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkContacttemplateToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "host_notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "service_notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_contacttemplate",
                                        'tableName2' => "tbl_contact",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkContacttemplateToContacttemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target' => "name",
                                        'linktable' => "tbl_lnkContacttemplateToVariabledefinition",
                                        'type' => 4);
                return(1);

            case "tbl_contactgroup":
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "members",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkContactgroupToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contactgroup_members",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkContactgroupToContactgroup",
                                        'type' => 2);
                return(1);

            case "tbl_hosttemplate":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "parents",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkHosttemplateToHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroups",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkHosttemplateToHostgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkHosttemplateToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkHosttemplateToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "check_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "check_command",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "event_handler",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_hosttemplate",
                                        'tableName2' => "tbl_host",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkHosttemplateToHosttemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target' => "name",
                                        'linktable' => "tbl_lnkHosttemplateToVariabledefinition",
                                        'type' => 4);
                return(1);

            case "tbl_host":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "parents",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkHostToHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroups",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkHostToHostgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkHostToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkHostToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "check_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "check_command",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "event_handler",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_hosttemplate",
                                        'tableName2' => "tbl_host",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkHostToHosttemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target' => "name",
                                        'linktable' => "tbl_lnkHostToVariabledefinition",
                                        'type' => 4);
                return(1);

            case "tbl_hostgroup":
                $arrRelations[] = array('tableName' => "tbl_host",
                                  'fieldName' => "members",
                                  'target'  => "host_name",
                                  'linktable' => "tbl_lnkHostgroupToHost",
                                  'type'    => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                  'fieldName' => "hostgroup_members",
                                  'target'  => "hostgroup_name",
                                  'linktable' => "tbl_lnkHostgroupToHostgroup",
                                  'type'    => 2);
                return(1);
            
            case "tbl_servicetemplate":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkServicetemplateToHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkServicetemplateToHostgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_servicegroup",
                                        'fieldName' => "servicegroups",
                                        'target' => "servicegroup_name",
                                        'linktable' => "tbl_lnkServicetemplateToServicegroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkServicetemplateToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkServicetemplateToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "check_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "check_command",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "event_handler",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_servicetemplate",
                                        'tableName2' => "tbl_service",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkServicetemplateToServicetemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target' => "name",
                                        'linktable' => "tbl_lnkServicetemplateToVariabledefinition",
                                        'type' => 4);
                return(1);

            case "tbl_service":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkServiceToHost",
                                        'type' => 2,
                                        'excludes' => 1);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkServiceToHostgroup",
                                        'type' => 2,
                                        'excludes' => 1);
                $arrRelations[] = array('tableName' => "tbl_servicegroup",
                                        'fieldName' => "servicegroups",
                                        'target' => "servicegroup_name",
                                        'linktable' => "tbl_lnkServiceToServicegroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkServiceToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkServiceToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "check_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "check_command",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "notification_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_command",
                                        'fieldName' => "event_handler",
                                        'target' => "command_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName1' => "tbl_servicetemplate",
                                        'tableName2' => "tbl_service",
                                        'fieldName' => "use_template",
                                        'target1' => "template_name",
                                        'target2' => "name",
                                        'linktable' => "tbl_lnkServiceToServicetemplate",
                                        'type' => 3);
                $arrRelations[] = array('tableName' => "tbl_variabledefinition",
                                        'fieldName' => "use_variables",
                                        'target'  => "name",
                                        'linktable' => "tbl_lnkServiceToVariabledefinition",
                                        'type'    => 4);
                return(1);

            case "tbl_servicegroup": 
                $arrRelations[] = array('tableName1' => "tbl_host",
                                        'tableName2' => "tbl_service",
                                        'fieldName' => "members",
                                        'target1' => "host_name",
                                        'target2' => "service_description",
                                        'linktable' => "tbl_lnkServicegroupToService",
                                        'type' => 5);
                $arrRelations[] = array('tableName' => "tbl_servicegroup",
                                        'fieldName' => "servicegroup_members",
                                        'target' => "servicegroup_name",
                                        'linktable' => "tbl_lnkServicegroupToServicegroup",
                                        'type' => 2);
                return(1);

            case "tbl_hostdependency":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "dependent_host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkHostdependencyToHost_DH",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkHostdependencyToHost_H",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "dependent_hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkHostdependencyToHostgroup_DH",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkHostdependencyToHostgroup_H",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "dependency_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);

            case "tbl_hostescalation":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkHostescalationToHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkHostescalationToHostgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkHostescalationToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkHostescalationToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "escalation_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);
        
            case "tbl_hostextinfo":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);

            case "tbl_servicedependency":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "dependent_host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkServicedependencyToHost_DH",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkServicedependencyToHost_H",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "dependent_hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkServicedependencyToHostgroup_DH",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkServicedependencyToHostgroup_H",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldName' => "dependent_service_description",
                                        'target' => "service_description",
                                        'linktable' => "tbl_lnkServicedependencyToService_DS",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_servicegroup",
                                        'fieldName' => "dependent_servicegroup_name",
                                        'target' => "servicegroup_name",
                                        'linktable' => "tbl_lnkServicedependencyToServicegroup_DS",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldName' => "service_description",
                                        'target' => "service_description",
                                        'linktable' => "tbl_lnkServicedependencyToService_S",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_servicegroup",
                                        'fieldName' => "servicegroup_name",
                                        'target' => "servicegroup_name",
                                        'linktable' => "tbl_lnkServicedependencyToServicegroup_S",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "dependency_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);

            case "tbl_serviceescalation":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "tbl_lnkServiceescalationToHost",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_hostgroup",
                                        'fieldName' => "hostgroup_name",
                                        'target' => "hostgroup_name",
                                        'linktable' => "tbl_lnkServiceescalationToHostgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldName' => "service_description",
                                        'target' => "service_description",
                                        'linktable' => "tbl_lnkServiceescalationToService",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => 'tbl_servicegroup',
                                        'fieldName' => 'servicegroup_name',
                                        'target' => 'servicegroup_name',
                                        'linktable' => "tbl_lnkServiceescalationToServicegroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contact",
                                        'fieldName' => "contacts",
                                        'target' => "contact_name",
                                        'linktable' => "tbl_lnkServiceescalationToContact",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_contactgroup",
                                        'fieldName' => "contact_groups",
                                        'target' => "contactgroup_name",
                                        'linktable' => "tbl_lnkServiceescalationToContactgroup",
                                        'type' => 2);
                $arrRelations[] = array('tableName' => "tbl_timeperiod",
                                        'fieldName' => "escalation_period",
                                        'target' => "timeperiod_name",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);

            case "tbl_serviceextinfo":
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "host_name",
                                        'target' => "host_name",
                                        'linktable' => "",
                                        'type' => 1);
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldNamw' => "service_description",
                                        'target' => "service_description",
                                        'linktable' => "",
                                        'type' => 1);
                return(1);
            
            default:
                return(0);
        }
    }


    /**
     * Write relations in the database
     *
     * Does the necessary relationships for a 1: n (Optional 1: n: n) relationship in the
     * relations table
     *
     * @param   string  $strTable       Table name
     * @param   int     $intMasterId    ID of the main table object
     * @param   array   $arrSlaveId     Array of object IDs of the sub-table
     * @param   int     $intMulti       0 = normal 1:n, 1 = 1:n:n relationship
     * @param   array   $arrExcIds      Exclude IDs that will be given a ! in the config
     * @return  int                     0 on success, 1 on failure
     */
    function dataInsertRelation($strTable, $intMasterId, $arrSlaveId, $intMulti=0, $arrExcIds=array())
    {
        global $ccm;

        // Make for each array position an entry in the relation table
        foreach ($arrSlaveId AS $elem) {
            // Hide empty values
            if ($elem == '0' || $elem=='*') continue;

            // SQL Statement
            if ($intMulti != 0) {
                $arrValues = "";
                $arrValues = explode("::", $elem);
                $strSQL = "INSERT INTO `$strTable` SET `idMaster`=".intval($intMasterId).", `idSlaveH`=".intval($arrValues[0]).", `idSlaveHG`=".intval($arrValues[1]).", `idSlaveS`=".intval($arrValues[2]);
            } else {
                $exclude = '';
                if (!empty($arrExcIds)) {
                    if (in_array($elem, $arrExcIds)) {
                        $exclude = ', `exclude`=1';
                    }
                }
                $strSQL = "INSERT INTO `$strTable` SET `idMaster`=".intval($intMasterId).", `idSlave`=".intval($elem).$exclude;
            }

            // Send data to the database server
            $intReturn = $this->dataInsert($strSQL, $intDataID);
            if ($intReturn != 0) {  
                return 1;
            }   
        }

        return 0;
    }


    /**
     * Update relations in the database
     *
     * Changes the relations for a 1:n (optonal 1:n:n) relationship within 
     * the relational table
     *
     * @param   string  $strTable       Table name
     * @param   int     $intMasterId    Object ID of the main table
     * @param   array   $arrSlaveId     Array of all the object IDs of the table
     * @param   int     $intMulti       0 = normal 1:n, 1 = 1:n:n relationship
     * @param   array   $arrExcIds      Excluded ids that will be given a ! in the config file
     * @return  int                     0 on success, 1 on failure
     */
    function dataUpdateRelation($strTable, $intMasterId, $arrSlaveId, $intMulti=0, $arrExcIds=array())
    {
        // Delete old relations
        $intReturn1 = $this->dataDeleteRelation($strTable, $intMasterId);
        if ($intReturn1 != 0) { return(1); }

        // Submit new relations
        $intReturn2 = $this->dataInsertRelation($strTable, $intMasterId, $arrSlaveId, $intMulti, $arrExcIds);
        if ($intReturn2 != 0) { return(1); }
        return(0);
    }


    /**
     * Removes a relation from the relation table
     *
     * @param   string  $strTable       Table name
     * @param   int     $intMasterId    Object ID of main table
     * @return  int                     0 on success, 1 on failure
     */
    function dataDeleteRelation($strTable, $intMasterId)
    {
        global $ccm;

        // SQL Statement
        $strSQL = "DELETE FROM `$strTable` WHERE `idMaster`=".intval($intMasterId);
        
        // Send data to the database server
        $intReturn = $this->dataInsert($strSQL, $intDataID);
        if ($intReturn != 0) { return(1); }
        return(0);
    }


    /**
     * Finds all relations from the database
     *
     * @param   string  $strTable           Table name
     * @param   int     $intMasterId        Object ID of the main table
     * @param   string  $strMasterfield     Name field of the main entry
     * @param   int     $intReporting       Text output (0 = yes 1 = no)
     * @return  int                         0 with no relations, 1 with relations
     */
    function infoRelation($strTable, $intMasterId, $strMasterfield, $intReporting=0, $service_only=false, $show_only_active=true)
    {
        global $ccm;
        $intReturn = $this->fullTableRelations($strTable, $arrRelations);
        $intDeletion = 0;

        if ($intReturn == 1) {
            $strNewMasterfield = str_replace(',', '`,`', $strMasterfield);
            $strSQL = "SELECT `".$strNewMasterfield."` FROM `$strTable` WHERE `id` = ".intval($intMasterId);
            $ccm->db->getSingleDataset($strSQL, $arrSource);
            
            ///////////////////MOD -MG ////////////
            if (count($arrSource) ==0) { return(0); } // Bail if there are no relations, deletion possible 
            ////////////////////////////////////////
      
            if (substr_count($strMasterfield, ",") != 0) {
                $arrTarget = explode(",", $strMasterfield);
                $strName = $arrSource[$arrTarget[0]]."-".$arrSource[$arrTarget[1]];
            } else {
                $strName = $arrSource[$strMasterfield];
            }

            $this->strDBMessage = "<span class='relationInfo'>Object ID: <strong>".encode_form_val($strName)."</strong> of table <strong>".$strTable.":</strong><br /></span>\n";

            foreach ($arrRelations AS $elem) {

                $arrFlags = explode(",", $elem['flags']);
                if ($elem['fieldName'] == "check_command") {
                    $strSQL = "SELECT * FROM `".$elem['tableName']."` WHERE SUBSTRING_INDEX(`".$elem['fieldName']."`,'!',1)= ".intval($intMasterId);
                } else {
                    $strSQL = "SELECT * FROM `".$elem['tableName']."` WHERE `".$elem['fieldName']."`= ".intval($intMasterId);
                }
                $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);

                // Display links in use only
                if ($intDataCount != 0) {

                    // Link type
                    if ($arrFlags[3] == 1) {

                        if ($elem['target'] == 'tbl_service' && $strTable == 'tbl_serviceescalation') {
                            $service_only = true;
                        }

                        foreach ($arrData AS $data) {

                            if ($elem['fieldName'] == "idMaster") {
                                $strRef = "idSlave";
                                if ($elem['target'] == "tbl_service") {
                                    if ($elem['tableName'] == "tbl_lnkServicegroupToService") {
                                        $strRef = "idSlaveS";
                                    }
                                } else if ($elem['target'] == "tbl_host") {
                                    if ($elem['tableName'] == "tbl_lnkServicegroupToService") {
                                        $strRef = "idSlaveH";
                                    }
                                } else if ($elem['target'] == "tbl_hostgroup") {
                                    if ($elem['tableName'] == "tbl_lnkServicegroupToService") {
                                        $strRef = "idSlaveHG";
                                    }
                                }
                            } else {
                                $strRef = "idMaster";
                            }
              
                            // Fetch data
                            $strSQL = "SELECT * FROM `".$elem['tableName']."`
                                       LEFT JOIN `".$elem['target']."` ON `".$strRef."` = `id`
                                       WHERE `".$elem['fieldName']."` = ".$data[$elem['fieldName']]."
                                       AND `".$strRef."` = ".intval($data[$strRef]);

                            // Make sure we can see all objects, even if inactive
                            if ($show_only_active) {
                                $strSQL .= " AND ".$elem['target'].".active = '1'";
                            }

                            $ccm->db->getSingleDataset($strSQL, $arrDSTarget);
                            $full_name = substr($elem['target'], 4, strlen($elem['target']));
                            
                            if (substr_count($elem['targetKey'], ",") != 0) {
                                $arrTarget = explode(",", $elem['targetKey']);
                                if ($service_only) {
                                    $c = '';
                                    $s = $arrDSTarget[$arrTarget[1]];
                                    $strTarget = $s;
                                } else {
                                    $c = $arrDSTarget[$arrTarget[0]];
                                    $s = $arrDSTarget[$arrTarget[1]];
                                    $strTarget = $c."-".$s;
                                }
                            } else {
                                $strTarget = isset($arrDSTarget[$elem['targetKey']]) ? $arrDSTarget[$elem['targetKey']] : '';
                            }

                            // Add to list of inactive objects
                            if (!$show_only_active && !$arrDSTarget['active']) {
                                $this->arrInactive[] = $arrDSTarget['id'];
                            }

                            // Consider the case of "must do" box, if multiple entries
                            if (($arrFlags[0] == 1) && ($strTarget != "-")) {
                                $strSQL = "SELECT * FROM `".$elem['tableName']."`
                                           WHERE `".$strRef."` = ".intval($arrDSTarget[$strRef]);
                                $booReturn = $ccm->db->getDataArray($strSQL, $arrDSCount, $intDCCount);
                                if ($intDCCount > 0) {
                                    $this->strDBMessage .= _("Relation to <strong>").encode_form_val(ucfirst($full_name))._("s</strong>, entry: <strong>").$strTarget." - </strong><span class='dependent'>"._("Dependent relationship")."</span><br />\n";
                                    $a = array('dependent' => 1);
                                    $this->hasDepRels = true;
                                    if (!empty($s)) { $a['cfg'] = $c; $a['service'] = $s; }
                                    else { $a['name'] = $strTarget; }
                                    $this->arrRR[$full_name][$arrDSTarget['id']] = $a;
                                    $this->arrDBIds[] = array($elem['target'], $arrDSTarget['idMaster']);
                                    $intDeletion = 1;
                                }
                            } else if ($strTarget != "-") {
                                // Removed extra output 
                                if ($intReporting != 0) {
                                    $this->strDBMessage .= _("Relation to <strong>").encode_form_val(ucfirst($full_name))._("s</strong>, entry: <strong>").$strTarget."</strong><br>\n";
                                }
                                $this->arrRR[$full_name][$arrDSTarget['id']] = $strTarget;
                                $this->arrDBIds[] = array($elem['target'], $arrDSTarget['idMaster']);
                            }
                        }
                    } else if ($arrFlags[3] == 0) {
                        $friendlyName = ucfirst(substr($elem['target'], 4, strlen($elem['target'])));

                        // Get peers entry
                        $strSQL = "SELECT * FROM `".$elem['tableName']."` WHERE `".$elem['fieldName']."`=".intval($intMasterId);
                        $booReturn = $ccm->db->getDataArray($strSQL, $arrDataCheck, $intDCCheck);
                        foreach ($arrDataCheck AS $data) {
                            $s = '';
                            $c = '';
                            if (substr_count($elem['targetKey'], ",") != 0) {
                                $arrTarget = explode(",", $elem['targetKey']);
                                $strTarget = $data[$arrTarget[0]]."-".$data[$arrTarget[1]];
                                $s = $data[$arrTarget[1]];
                                $c = $data[$arrTarget[0]];
                            } else {
                                $strTarget = $data[$elem['targetKey']];
                            }
                            $full_name = substr($elem['tableName'], 4, strlen($elem['tableName']));

                            if ($arrFlags[0] == 1) {
                                $this->strDBMessage .= _("Relation to <strong>").$elem['tableName']._("</strong>, entry: <strong>").$strTarget." - </strong><span class='dependent'>"._("Dependent relationship")."</span><br>\n";

                                $a = array('dependent' => 1);
                                $this->hasDepRels = true;
                                if (!empty($s)) { $a['cfg'] = $c; $a['service'] = $s; }
                                else { $a['name'] = $strTarget; }
                                $this->arrRR[$full_name][$data['id']] = $a;
                                $this->arrDBIds[] = array($elem['tableName'], $data['id']);
                                $intDeletion = 1;
                            } else {
                                // Remove extra log output 
                                if ($intReporting != 0) {
                                    $this->strDBMessage .= _("Relation to <strong>").$elem['tableName']._("</strong>, entry: <strong>").$strTarget."</strong><br />\n";
                                }
                                $a = array();
                                if (!empty($s)) { $a['cfg'] = $c; $a['service'] = $s; }
                                else { $a['name'] = $strTarget; }
                                $this->arrRR[$full_name][$data['id']] = $a;
                                $this->arrDBIds[] = array($elem['tableName'], $data['id']);
                            }
                        }
                    }
                }
            }
        }
        return $intDeletion;
    }

    /**
     * FOR DEBUGGING PURPOSES ONLY
     * Recursively parses the relations arrays provided by $this->fullTableRelations() to generate a tree for a given table. 
     *
     * Returns tree as a string suitable for inserting directly into a log or text file.
     *
     * @param   string  $tblName            Table name
     * @param   string  $relNameFilter      Name of specific relation table to follow.If blank all relations will be parsed.
     * 
     * All other params are to support recursion and shouldn't be supplied by user.
     * 
     * @return  string   formatted relation tree. For high level tables, e.g. tbl_host, this is a very large string.
     */

    function showRelations($tblName, $relNameFilter='', $parents=array(), &$visited='', $indents='', $depth=0) {
        // error_log(debug_backtrace()[1]['function'] . " - \$tblName: ".print_r($tblName,true). ");

        if (!$this->fullTableRelations($tblName, $relations)) {
            return $indents . "No relations for table $tblName\n";
        }

        $indent = '----';
        $depth++;
        $visited = $visited ?: array($tblName);
        $result = "\n" . $indents . "$depth:Relations for " . implode(':', $parents) . " - $tblName\n";
        $parents[] = $tblName;
        $indents .= $indent;

        foreach ($relations as $relation) {
            extract($relation);
            
            if ($tableName == $relNameFilter || $relNameFilter == '') {
                $flgs = explode(',',$flags);
                $result .= $indents . "$tableName.$fieldName - target: $target, targetKey: $targetKey, flags: $flags\n";

                if ($depth < 5) {
                        $goTbl =  (strpos(' '.$tableName, '_lnk') > 1) ? $target : $tableName;
                        $parChld = "$tableName-$goTbl";
                        $grPar = $visited[count($visited)-1];
                        $grPar = explode('-', $grPar)[0];

                        // These conditions determine if it should recurse. 
                        // They are minimal, so the output is too much. If the depth isn't limited it will crash the server
                        // They should probably take some of the flags into account or $fieldName s into account.
                        if (!in_array($parChld, $visited) && !in_array($goTbl, $parents)) {
                            $visited[] = $parChld;
                            $result .= $this->showRelations($goTbl,'', $parents, $visited, $indents, $depth) . "\n";
                            // error_log(debug_backtrace()[1]['function'] . " - \$ RETURNED from $goTbl - depth: ".print_r($depth,true));

                        } else {
                            // error_log(debug_backtrace()[1]['function'] . "$indents SKIP \$goTbl: $goTbl, \$parChld: $parChld,  \$parents: " . implode(',', $parents));
                        }
                }
            }
        }

        // Optionally writes list of all parent-child combinatins visited
        if ( 0 && $depth <= 1) {
            error_log(debug_backtrace()[1]['function'] . " - \$visited: ".print_r($visited,true));
        }

        return $result;
    }


    /**
     * Return full relations of a data table
     *
     * Returns a list with all data fields in a table that have a relation to another table.
     * Here passive relations are returned that do not yet have to be written in a
     * configuration consist separation, eg. Relations to be written by other configurations,
     * but the specified table involves.
     *
     * This function is used on a configuration entry to completely delete or to determine
     * whether the current configuration is used elsewhere.
     *
     * Return Value: $arrRelations Array the affected data fields
     *                -> tableName - Contains the table name of the linked ID
     *                -> fieldName - Table field that contains the linked ID
     *                -> flags Pos1 -> 0 = normal field, 1 = required field     [Field type]
     *                         Pos2 -> 0 = delete, 1 = leave, 2 = set to 0      [Delete if normal]
     *                         Pos3 -> 0 = delete, 2 = set to 0                 [Forced?]
     *                         Pos4 -> 0 = 1:1, 1=1:n, 2=1:nVar, 3=1:nTime      Link type]
     *
     * @param   string  $strTable       Table name
     * @param   array   $arrRelations   
     * @return  int                     0 No field with relation, 1 At least one field with relation
     */
    function fullTableRelations($strTable, &$arrRelations)
    {
        $arrRelations = array();
        switch ($strTable) {
            
            case "tbl_command": 
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToCommandHost",
                                        'fieldName' => "idSlave",
                                        'target'    => "tbl_contacttemplate",
                                        'targetKey' => "template_name",
                                        'flags'     => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToCommandService",
                                        'fieldName' => "idSlave",
                                        'target'    => "tbl_contacttemplate",
                                        'targetKey' => "template_name",
                                        'flags'     => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToCommandHost",
                                        'fieldName' => "idSlave",
                                        'target'    => "tbl_contact",
                                        'targetKey' => "contact_name",
                                        'flags'     => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToCommandService",
                                        'fieldName' => "idSlave",
                                        'target'    => "tbl_contact",
                                        'targetKey' => "contact_name",
                                        'flags'     => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "check_command",
                                        'target'    => "",
                                        'targetKey' => "host_name",
                                        'flags'     => "1,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_host",
                                        'fieldName' => "event_handler",
                                        'target'    => "",
                                        'targetKey' => "host_name",
                                        'flags'     => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldName' => "check_command",
                                        'target'    => "",
                                        'targetKey' => "config_name,service_description",
                                        'flags'     => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_service",
                                        'fieldName' => "event_handler",
                                        'target'    => "",
                                        'targetKey' => "config_name,service_description",
                                        'flags'     => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_hosttemplate",
                                        'fieldName' => "check_command",
                                        'target'    => "",
                                        'targetKey' => "template_name",
                                        'flags'     => "1,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_servicetemplate",
                                        'fieldName' => "check_command",
                                        'target'    => "",
                                        'targetKey' => "template_name",
                                        'flags'     => "1,2,2,0");
                return(1);
      
            case "tbl_contact":
                $arrRelations[] = array('tableName' => "tbl_lnkContactgroupToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "1,2,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToCommandHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_command",
                                  'targetKey' => "command_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToCommandService",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_command",
                                  'targetKey' => "command_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToContacttemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contacttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_serviceescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToContact",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "1,1,0,1");
                return(1);

            case "tbl_contactgroup":
                $arrRelations[] = array('tableName' => "tbl_lnkContactgroupToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactgroupToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactgroupToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contacttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_serviceescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToContactgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "1,1,0,1");
                return(1);

            case "tbl_contacttemplate":
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToCommandHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_command",
                                  'targetKey' => "command_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToCommandService",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_command",
                                  'targetKey' => "command_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToContacttemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contacttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToContacttemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contacttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkContacttemplateToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_lnkContactToContacttemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_host":
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHost_DH",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostdependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHost_H",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostdependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostgroupToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHosttemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHost_DH",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHost_H",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_serviceescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToHost",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToService",
                                  'fieldName' => "idSlaveH",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_hostextinfo",
                                  'fieldName' => "host_name",
                                  'target'  => "",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,0");
                $arrRelations[] = array('tableName' => "tbl_serviceextinfo",
                                  'fieldName' => "host_name",
                                  'target'  => "",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,0");
                return(1);

            case "tbl_hostdependency":
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHostgroup_DH",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHostgroup_H",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHost_DH",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHost_H",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_hostescalation":
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_hostextinfo":
                return(0);

            case "tbl_hostgroup":
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHostgroup_DH",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostdependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostdependencyToHostgroup_H",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostdependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostescalationToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostgroupToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostgroupToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostgroupToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHostgroup_DH",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHostgroup_H",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_serviceescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToHostgroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToService",
                                  'fieldName' => "idSlaveHG",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_hosttemplate":
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHosttemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToHosttemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_hosttemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkHosttemplateToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_lnkHostToHosttemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_service":
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToService_DS",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToService_S",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicedependency",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToService",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_serviceescalation",
                                  'targetKey' => "config_name",
                                  'flags'   => "1,1,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToService",
                                  'fieldName' => "idSlaveS",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToServicegroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToServicetemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_serviceextinfo",
                                  'fieldName' => "service_description",
                                  'target'  => "",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,0");
                return(1);

            case "tbl_servicedependency":
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHostgroup_DH",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHostgroup_H",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHost_DH",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToHost_H",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToService_DS",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicedependencyToService_S",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_serviceescalation":
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToService",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => 'tbl_lnkServiceescalationToServicegroup',
                                  'fieldName' => 'idMaster',
                                  'target' => 'tbl_servicegroup',
                                  'targetKey' => 'servicegroup_name',
                                  'flags' => '0,0,0,1');
                return(1);

            case "tbl_serviceextinfo":
                return(0);

            case "tbl_servicegroup":
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToService",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToServicegroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicegroupToServicegroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToServicegroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToServicegroup",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceescalationToServicegroup",
                                  'fieldName' => 'idSlave',
                                  'target' => 'tbl_serviceescalation',
                                  'targetKey' => 'config_name',
                                  'flags' => '0,0,0,1');
                return(1);

            case "tbl_servicetemplate":
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToContact",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contact",
                                  'targetKey' => "contact_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToContactgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_contactgroup",
                                  'targetKey' => "contactgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToHost",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_host",
                                  'targetKey' => "host_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToHostgroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_hostgroup",
                                  'targetKey' => "hostgroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToServicegroup",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_servicegroup",
                                  'targetKey' => "servicegroup_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToServicetemplate",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToServicetemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_servicetemplate",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkServicetemplateToVariabledefinition",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_variabledefinition",
                                  'targetKey' => "name",
                                  'flags'   => "0,0,0,2");
                $arrRelations[] = array('tableName' => "tbl_lnkServiceToServicetemplate",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_service",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,0,0,1");
                return(1);

            case "tbl_timeperiod":
                $arrRelations[] = array('tableName' => "tbl_lnkTimeperiodToTimeperiod",
                                  'fieldName' => "idMaster",
                                  'target'  => "tbl_timeperiod",
                                  'targetKey' => "timeperiod_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_lnkTimeperiodToTimeperiod",
                                  'fieldName' => "idSlave",
                                  'target'  => "tbl_timeperiod",
                                  'targetKey' => "timeperiod_name",
                                  'flags'   => "0,0,0,1");
                $arrRelations[] = array('tableName' => "tbl_contact",
                                  'fieldName' => "host_notification_period",
                                  'target'  => "",
                                  'targetKey' => "contact_name",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_contact",
                                  'fieldName' => "service_notification_period",
                                  'target'  => "",
                                  'targetKey' => "contact_name",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_contacttemplate",
                                  'fieldName' => "host_notification_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_contacttemplate",
                                  'fieldName' => "service_notification_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_host",
                                  'fieldName' => "check_period",
                                  'target'  => "",
                                  'targetKey' => "host_name",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_host",
                                  'fieldName' => "notification_period",
                                  'target'  => "",
                                  'targetKey' => "host_name",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_hosttemplate",
                                  'fieldName' => "check_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_hosttemplate",
                                  'fieldName' => "notification_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_hostdependency",
                                  'fieldName' => "dependency_period",
                                  'target'  => "",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_hostescalation",
                                  'fieldName' => "escalation_period",
                                  'target'  => "",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_service",
                                  'fieldName' => "check_period",
                                  'target'  => "",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_service",
                                  'fieldName' => "notification_period",
                                  'target'  => "",
                                  'targetKey' => "config_name,service_description",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_servicetemplate",
                                  'fieldName' => "check_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_servicetemplate",
                                  'fieldName' => "notification_period",
                                  'target'  => "",
                                  'targetKey' => "template_name",
                                  'flags'   => "1,1,2,0");
                $arrRelations[] = array('tableName' => "tbl_servicedependency",
                                  'fieldName' => "dependency_period",
                                  'target'  => "",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_serviceescalation",
                                  'fieldName' => "escalation_period",
                                  'target'  => "",
                                  'targetKey' => "config_name",
                                  'flags'   => "0,2,2,0");
                $arrRelations[] = array('tableName' => "tbl_timedefinition",
                                  'fieldName' => "tipId",
                                  'target'  => "",
                                  'targetKey' => "id",
                                  'flags'   => "0,0,0,3");
                return 1;

            default:
                return 0;
        }
    }
}