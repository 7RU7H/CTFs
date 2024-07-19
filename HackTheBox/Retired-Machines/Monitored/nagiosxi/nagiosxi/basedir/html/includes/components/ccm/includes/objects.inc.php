<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: objects.inc.php
//  Desc: Handles submissions (modify/insert) of most objects in the CCM.
//


/** 
 * Handles form submissions for timeperiod object configurations
 *
 * @return  array                   array(int $returnCode,string $returnMessage) return output for browser
 */
function process_timeperiod_submission()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;
    
    // Process form variables 
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);

    $chkInsName = ccm_grab_request_var('tfName', '');
    $chkInsAlias = ccm_grab_request_var('tfFriendly', '');
    $chkInsTplName = ccm_grab_request_var('tfTplName', '');
    $chkSelExclude = ccm_grab_request_var('excludes', array(''));
    $chkActive = ccm_grab_request_var('chbActive', 0);
    $timeDefs = ccm_grab_request_var('timedefinitions', array());
    $timeRanges = ccm_grab_request_var('timeranges', array());
    $chkDomainId = $_SESSION['domain'];

    // Handle input arrays 
    // =================
    if (($chkSelExclude[0] == "") || ($chkSelExclude[0] == "0")) { 
        $intSelExclude = 0;  
    } else {
        $intSelExclude = 1;
    }

    // Build SQL query 
    $strSQLx = "`tbl_timeperiod` SET `timeperiod_name`='$chkInsName', `alias`='$chkInsAlias', `exclude`=$intSelExclude,
            `name`='$chkInsTplName', `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
    
    // Insert or modify 
    if ($chkModus == "insert") { 
        $strSQL = "INSERT INTO ".$strSQLx;
        $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);
    } else {
        $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
        
        // Save original chkTfName so that we can check if it has changed
        $strRelSQL  = "SELECT `id`,`".$exactType."_name` FROM `tbl_".$exactType."` WHERE `id` = '$chkDataId' ";
        $ccm->db->getDataArray($strRelSQL,$arrData,$intDataCount);
        $chkTfOldName = $arrData[0][''.$exactType.'_name'];

        // Run relation check to generate arrDBIds (array of related table,ids)
        $ccm->data->infoRelation("tbl_".$exactType, $chkDataId,"id");

        // Exec SQL query 
        $intInsert = $ccm->data->dataInsert($strSQL,$intInsertId);

        // If the above insert succeeded and the config value used for the config
        // file name has changed,  iterate through the relation table/ids
        // updating the last_modified time on related hosts and services so
        // the CCM can update those files
        if ($intInsert == 0 && $chkTfOldName != $chkInsName) {
            foreach($ccm->data->arrDBIds as $data) {
                if ($data[0] == "tbl_host" || $data[0] == "tbl_service") {
                    $strUpdSQL  = "UPDATE `".$data[0]."` SET `last_modified`=NOW() WHERE `id` = '".$data[1]."' ";
                    $intUpdate = $ccm->data->dataInsert($strUpdSQL, $intInsertId);
                    if ($intUpdate != 0) {
                        $ccm->data->writeLog(_('Problem detected updating object name on relative: '.$data[0].'('.$data[1].')')." ".$chkInsName);
                    }
                }
            }
        }
    }
    
    // Bail if initial query fails 
    if ($intInsert > 0) {
        $errors++; 
        $strMessage .= $ccm->data->strDBMessage; 
        return array($errors, $strMessage); 
    }

    if ($chkModus == "insert") {
        $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_ADD);
    }
    if ($chkModus == "modify") {
        $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_MODIFY);
    }

    // Update relationships
    if ($chkModus == "insert") {
        if ($intSelExclude == 1) {
            $ccm->data->dataInsertRelation("tbl_lnkTimeperiodToTimeperiod", $chkDataId, $chkSelExclude);
        }
        $intTipId = $intInsertId;
    } else if ($chkModus == "modify") {
        if ($intSelExclude == 1) {
            $ccm->data->dataUpdateRelation("tbl_lnkTimeperiodToTimeperiod", $chkDataId, $chkSelExclude);
        } else {
            $ccm->data->dataDeleteRelation("tbl_lnkTimeperiodToTimeperiod", $chkDataId);
        }
    }
        
    // Clear out old time definitions
    if ($chkModus == "modify") {
        $strSQL = "DELETE FROM `tbl_timedefinition` WHERE `tipId`=$chkDataId";
        $booReturn = $ccm->data->dataInsert($strSQL, $intInsertId);
        $intTipId = $chkDataId;
    }
        
    // Process timedefinitions and timeranges (tipId = timeperiod id)
    for ($i = 0; $i < count($timeDefs); $i++) {
        $def = strtolower($timeDefs[$i]); 
        $range = str_replace(" ","",$timeRanges[$i]);    //strip whitespace             
        $strSQL = "INSERT INTO `tbl_timedefinition` (`tipId`,`definition`,`range`,`last_modified`)
                   VALUES ($intTipId,'$def','$range',now())";
    
        $booReturn  = $ccm->data->dataInsert($strSQL,$intInsertId);    
        if($booReturn > 0) {
            $errors++;
        }
    }

    // If there were no errors then we should set the global apply config needed
    if ($errors == 0) {
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified('timeperiod');
    }

    // Return data 
    $strMessage .= $ccm->data->strDBMessage;
    return array($errors, $strMessage);
}


/**
 * Handles form submissions for command object configurations
 *
 * @return  array                   array(int $returnCode,string $returnMessage) return output for browser
 */
function process_command_submission()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;
    
    // Process form variables
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);

    // Command form values
    $chkInsName = ccm_grab_request_var('tfName', '');
    $chkInsCommand = ccm_grab_request_var('tfCommand', '');
    $chkInsType = ccm_grab_request_var('selCommandType', '');
    $chkActive = ccm_grab_request_var('chbActive', 0);

    // Temp session item 
    $chkDomainId = $_SESSION['domain'];

    // Data processing
    $strSQLx = "tbl_command SET command_name='$chkInsName', command_line='$chkInsCommand', command_type=$chkInsType, active='$chkActive', config_id=$chkDomainId, last_modified=NOW()";
    if ($chkModus == "insert") {
        $strSQL = "INSERT INTO ".$strSQLx;
        $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);
    } else {
        $strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";
        
        // Save original chkTfName so that we can check if it has changed
        $strRelSQL  = "SELECT `id`,`".$exactType."_name` FROM `tbl_".$exactType."` WHERE `id` = '$chkDataId' ";
        $ccm->db->getDataArray($strRelSQL, $arrData, $intDataCount);
        $chkTfOldName = $arrData[0][''.$exactType.'_name'];

        // Run relation check to generate arrDBIds (array of related table,ids)
        $ccm->data->infoRelation("tbl_".$exactType, $chkDataId, "id");
        
        //Exec SQL query
        $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);

        // If the above insert succeeded and the config value used for the config
        // file name has changed,  iterate through the relation table/ids
        // updating the last_modified time on related hosts and services so
        // the CCM can update those files
        if ($intInsert == 0 && $chkTfOldName != $chkInsName) {
            foreach($ccm->data->arrDBIds as $data) {
                if ($data[0] == "tbl_host" || $data[0] == "tbl_service") {
                    $strUpdSQL = "UPDATE `".$data[0]."` SET `last_modified`=NOW() WHERE `id` = '".$data[1]."' ";
                    $intUpdate = $ccm->data->dataInsert($strUpdSQL, $intInsertId);
                    if ($intUpdate != 0) {
                        $ccm->data->writeLog(_('Problem detected updating object name on relative: '.$data[0].'('.$data[1].')')." ".$chkInsName);
                    }
                }
            }
        }
    }

    if ($intInsert == 1) {
        $intReturn = 1;
        $errors++;
    } else {
        if ($chkModus == "insert") {
            $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkInsName, AUDITLOGTYPE_ADD);
        }
        if ($chkModus == "modify") {
            $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkInsName, AUDITLOGTYPE_MODIFY);
        }
        $intReturn = 0;
    }

    // Return status 
    if (isset($intReturn) && ($intReturn == 1)) $strMessage = $ccm->data->strDBMessage;
    if (isset($intReturn) && ($intReturn == 0)) $strMessage = $ccm->data->strDBMessage;

    // Last database update
    $ccm->config->intDomainId = $_SESSION['domain'];
    $ccm->config->lastModified("tbl_command", $strLastModified, $strFileDate, $strOld);

    // If there are no errors set the global apply config needed
    if ($errors == 0) {
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified('command');
    }

    // Return data
    return array($errors, $strMessage);
}


/**
 * Handles submitting escalation submissions
 *
 * @return  array
 */
function process_escalation_submission()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;

    // Expected $_REQUEST variables for all forms
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);

    // Select lists
    $chkSelHost = ccm_grab_request_var('hosts', array());
    $chkSelHostExc = ccm_grab_request_var('hosts_exc', array());
    $chkSelHostGroup = ccm_grab_request_var('hostgroups', array());
    $chkSelHostGroupExc = ccm_grab_request_var('hostgroups_exc', array());
    $chkSelService = ccm_grab_request_var('services', array());
    $chkSelServiceExc = ccm_grab_request_var('services_exc', array());
    $chkSelServiceGroup = ccm_grab_request_var('servicegroups', array());
    $chkSelContact = ccm_grab_request_var('contacts', array());
    $chkSelContactGroup = ccm_grab_request_var('contactgroups', array());

    // Misc 
    $chkTfFirstNotif = ccm_grab_request_var('tfFirstNotif', "NULL");
    $chkTfLastNotif = ccm_grab_request_var('tfLastNotif', "NULL");
    $chkTfNotifInterval = ccm_grab_request_var('tfNotifInterval', "NULL");
    $chkSelEscPeriod = ccm_grab_request_var('selPeriod', 0);
    $chkActive = ccm_grab_request_var('chbActive', 0);

    // Escalation options 
    $chkEOd = (ccm_grab_request_var('chbEOd', false)) ? 'd' : '';
    $chkEOw = (ccm_grab_request_var('chbEOw', false)) ? 'w' : '';
    $chkEOu = (ccm_grab_request_var('chbEOu', false)) ? 'u' : '';
    $chkEOc = (ccm_grab_request_var('chbEOc', false)) ? 'c' : '';
    $chkEOr = (ccm_grab_request_var('chbEOr', false)) ? 'r' : '';

    $chkTfConfigName = ccm_grab_request_var('tfConfigName', '');
    $chkDomainId = $_SESSION['domain']; 

    // Process variables as needed
    $strEO = '';

    foreach (array($chkEOw, $chkEOu, $chkEOc, $chkEOr, $chkEOd) as $a) {
        if ($a !='') {
            $strEO .= $a.',';
        }
    }

    // Set markers if there are selections
    $intSelHost = empty($chkSelHost) ? 0 : 1;
    $intSelHostGroup = empty($chkSelHostGroup) ? 0 : 1;
    $intSelService = empty($chkSelService) ? 0 : 1;
    $intSelServiceGroup = empty($chkSelServiceGroup) ? 0 : 1;
    $intSelContact = empty($chkSelContact) ? 0 : 1;
    $intSelContactGroup = empty($chkSelContactGroup) ? 0 : 1;

    // Wildcards?
    $intSelHost = (is_array($chkSelHost) && in_array("*", $chkSelHost) ) ? 2 : $intSelHost;
    $intSelHostGroup = (is_array($chkSelHostGroup) && in_array("*", $chkSelHostGroup) ) ? 2 : $intSelHostGroup;
    $intSelService = (is_array($chkSelService) && in_array("*", $chkSelService) ) ? 2 : $intSelService;
    $intSelServiceGroup = (is_array($chkSelServiceGroup) && in_array("*", $chkSelServiceGroup) ) ? 2 : $intSelServiceGroup;
    $intSelContact = (is_array($chkSelContact) && in_array("*", $chkSelContact) ) ? 2 : $intSelContact;
    $intSelContactGroup = (is_array($chkSelContactGroup) && in_array("*", $chkSelContactGroup) ) ? 2 : $intSelContactGroup;

    // Only select the negation objects if we are using *
    if ($intSelHost == 2) {
        $chkSelHost = $chkSelHostExc;
    }
    if ($intSelHostGroup == 2) {
        $chkSelHostGroup = $chkSelHostGroupExc;
    }
    if ($intSelService == 2) {
        $chkSelService = $chkSelServiceExc;
    }

    // Build SQL Query 
    $strSQLx = "`tbl_{$exactType}` SET `config_name`='$chkTfConfigName', `host_name`=$intSelHost,
         `hostgroup_name`=$intSelHostGroup, `contacts`=$intSelContact,
        `contact_groups`=$intSelContactGroup, `first_notification`=$chkTfFirstNotif, `last_notification`=$chkTfLastNotif,
        `notification_interval`=$chkTfNotifInterval, `escalation_period`='$chkSelEscPeriod', `escalation_options`='$strEO',
        `config_id`=$chkDomainId, `active`='$chkActive', `last_modified`=NOW()";

    if ($exactType == 'serviceescalation') {
        $strSQLx .= ",`service_description`=$intSelService, `servicegroup_name`=$intSelServiceGroup";
    }

    if ($chkModus == "insert") {
        $strSQL = "INSERT INTO ".$strSQLx;  
    } else {
        $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
    }

    // Send query to SQL
    $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);
    if ($chkModus == "insert") {
        $chkDataId = $intInsertId;
    }

    // There was an error updating the DB, BAIL! 
    if ($intInsert == 1) {
        $errors++;
        $strMessage = $ccm->data->strDBMessage;
    } else {

        if ($chkModus == "insert") {
            $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfConfigName, AUDITLOGTYPE_ADD);
        }
        if ($chkModus == "modify") {
            $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfConfigName, AUDITLOGTYPE_MODIFY);
        }

        // Update Relations 
        // ============================
        if ($chkModus == "insert") {

            if (!empty($chkSelHost)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHost", $chkDataId, $chkSelHost, 0, $chkSelHostExc);
            }

            if (!empty($chkSelHostGroup)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHostgroup", $chkDataId, $chkSelHostGroup, 0, $chkSelHostGroupExc);
            }

            if (!empty($chkSelService)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToService", $chkDataId, $chkSelService, 0, $chkSelServiceExc);
            }

            if (!empty($chkSelServiceGroup)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToServicegroup", $chkDataId, $chkSelServiceGroup);
            }

            if (!empty($chkSelContact)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToContact", $chkDataId, $chkSelContact);
            }

            if (!empty($chkSelContactGroup)) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToContactgroup", $chkDataId, $chkSelContactGroup);
            }

        }

        if ($chkModus == "modify") {

            // Update hosts 
            if (!empty($chkSelHost)) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHost", $chkDataId, $chkSelHost, 0, $chkSelHostExc);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHost", $chkDataId);
            }

            // Update hostgroups 
            if (!empty($chkSelHostGroup)) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHostgroup", $chkDataId, $chkSelHostGroup, 0, $chkSelHostGroupExc);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHostgroup", $chkDataId);
            }

            // Services 
            if (!empty($chkSelService)) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToService", $chkDataId, $chkSelService, 0, $chkSelServiceExc);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToService", $chkDataId);
            }

            // Services 
            if (!empty($chkSelServiceGroup) && $intSelServiceGroup != 2) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToServicegroup", $chkDataId, $chkSelServiceGroup);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToServicegroup", $chkDataId);
            }

            // Contacts 
            if (!empty($chkSelContact) && $intSelContact != 2) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToContact", $chkDataId, $chkSelContact);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToContact", $chkDataId);
            }

            // Contact groups 
            if (!empty($chkSelContactGroup) && $intSelContactGroup != 2) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToContactgroup", $chkDataId, $chkSelContactGroup);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToContactgroup", $chkDataId);
            }

        }
    }

    // Create return string
    if ($errors == 0) {
        $strMessage = $ccm->data->strDBMessage;
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified($exactType);
    }

    return array($errors, $strMessage);
}


/**
 * Processes the dependency submission to the database
 *
 * @return  array
 */
function process_dependency_submission()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;

    // Expected $_REQUEST variables for all forms
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);

    //select lists
    $chkSelHost = ccm_grab_request_var('hosts', array());
    $chkSelHostgroup = ccm_grab_request_var('hostgroups', array());
    $chkSelService = ccm_grab_request_var('services', array());
    $chkSelServicegroup = ccm_grab_request_var('servicegroups', array());
    $chkSelHostDepend = ccm_grab_request_var('hostdependencys', array());
    $chkSelHostgroupDepend = ccm_grab_request_var('hostgroupdependencys', array());
    $chkSelServiceDepend = ccm_grab_request_var('servicedependencys', array());
    $chkSelServicegroupDepend = ccm_grab_request_var('servicegroupdependencys', array());

    // Misc
    $chkInherit = ccm_grab_request_var('chbInherit', 0);
    $chkSelDependPeriod = ccm_grab_request_var('selPeriod', 0);
    $chkActive = ccm_grab_request_var('chbActive', 0);

    // Execution failure options 
    $chkEOo = (ccm_grab_request_var('chbEOo', false)) ? 'o' : '';
    $chkEOd = (ccm_grab_request_var('chbEOd', false)) ? 'd' : '';
    $chkEOu = (ccm_grab_request_var('chbEOu', false)) ? 'u' : '';
    $chkEOp = (ccm_grab_request_var('chbEOp', false)) ? 'p' : '';
    $chkEOn = (ccm_grab_request_var('chbEOn', false)) ? 'n' : '';
    $chkEOw = (ccm_grab_request_var('chbEOw', false)) ? 'w' : '';
    $chkEOc = (ccm_grab_request_var('chbEOc', false)) ? 'c' : '';

    // Notification failure options 
    $chkNOo = (ccm_grab_request_var('chbNOo', false)) ? 'o' : '';
    $chkNOd = (ccm_grab_request_var('chbNOd', false)) ? 'd' : '';
    $chkNOu = (ccm_grab_request_var('chbNOu', false)) ? 'u' : '';
    $chkNOp = (ccm_grab_request_var('chbNOp', false)) ? 'p' : '';
    $chkNOn = (ccm_grab_request_var('chbNOn', false)) ? 'n' : '';
    $chkNOw = (ccm_grab_request_var('chbNOw', false)) ? 'w' : '';
    $chkNOc = (ccm_grab_request_var('chbNOc', false)) ? 'c' : '';

    $chkTfConfigName = ccm_grab_request_var('tfConfigName', '');
    $chkDomainId = $_SESSION['domain'];

    // Process variables as needed
    $strEO = '';
    foreach (array($chkEOw, $chkEOu, $chkEOc, $chkEOd, $chkEOo, $chkEOp, $chkEOn) as $a) {
        if ($a != '') {
            $strEO .= $a.',';
        }
    }

    // Build notification failure criteria option string
    $strNO = '';
    foreach (array($chkNOw, $chkNOu, $chkNOc, $chkNOd, $chkNOo, $chkNOp, $chkNOn) as $a) {
        if ($a != '') {
            $strNO .= $a.',';
        }
    }

    // Set booleans 
    $intSelHost = empty($chkSelHost) ? 0 : 1;
    $intSelHostgroup = empty($chkSelHostgroup) ? 0 : 1;
    $intSelService = empty($chkSelService) ? 0 : 1;
    $intSelServicegroup = empty($chkSelServicegroup) ? 0 : 1;
    $intSelHostDepend = empty($chkSelHostDepend) ? 0 : 1;
    $intSelHostgroupDepend = empty($chkSelHostgroupDepend) ? 0 : 1;
    $intSelServiceDepend = empty($chkSelServiceDepend) ? 0 : 1;
    $intSelServicegroupDepend = empty($chkSelServicegroupDepend) ? 0 : 1;

    // Wildcards?
    $intSelHost = (is_array($chkSelHost) && in_array("*", $chkSelHost) ) ? 2 : $intSelHost;
    $intSelService = (is_array($chkSelService) && in_array("*", $chkSelService) ) ? 2 : $intSelService;
    $intSelServiceDepend = (is_array($chkSelServiceDepend) && in_array("*", $chkSelServiceDepend) ) ? 2 : $intSelServiceDepend;
    $intSelServicegroupDepend = (is_array($chkSelServicegroupDepend) && in_array("*", $chkSelServicegroupDepend) ) ? 2 : $intSelServicegroupDepend;

    // Build SQL query   
    $strSQLx = "`tbl_{$exactType}` SET `dependent_host_name`=$intSelHostDepend, `dependent_hostgroup_name`=$intSelHostgroupDepend,
             `host_name`=$intSelHost, `hostgroup_name`=$intSelHostgroup,
             `config_name`='$chkTfConfigName', `inherits_parent`='$chkInherit',
            `execution_failure_criteria`='$strEO', `notification_failure_criteria`='$strNO', `dependency_period`=$chkSelDependPeriod,
            `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";

    if ($exactType == 'servicedependency') {
        $strSQLx .=",`dependent_service_description`=$intSelServiceDepend, `dependent_servicegroup_name`=$intSelServicegroupDepend,
                     `servicegroup_name`=$intSelServicegroup, `service_description`=$intSelService ";
    }

    if ($chkModus == "insert") {
        $strSQL = "INSERT INTO ".$strSQLx;
    } else { 
        $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
    }

    $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);

    if ($chkModus == "insert") { 
        $chkDataId = $intInsertId;
    }

    if ($intInsert == 1) {
        $errors++;
    } else {

        if ($chkModus == "insert") {
            $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfConfigName, AUDITLOGTYPE_ADD);
        }
        if ($chkModus == "modify") {
            $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfConfigName, AUDITLOGTYPE_MODIFY);
        }

        // UPDATE RELATIONS 
        // ============================

        if ($chkModus == "insert") {

            if ($intSelHostDepend == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHost_DH", $chkDataId, $chkSelHostDepend);
            }

            if ($intSelHostgroupDepend == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHostgroup_DH", $chkDataId, $chkSelHostgroupDepend);
            }

            if ($intSelServiceDepend == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToService_DS", $chkDataId, $chkSelServiceDepend);
            }

            if ($intSelServicegroupDepend == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToServicegroup_DS", $chkDataId, $chkSelServicegroupDepend);
            }

            if ($intSelHost == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHost_H", $chkDataId, $chkSelHost);
            }

            if ($intSelHostgroup == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToHostgroup_H", $chkDataId, $chkSelHostgroup);
            }

            if ($intSelService == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToService_S", $chkDataId, $chkSelService);
            }

            if ($intSelServicegroup == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk{$ucType}ToServicegroup_S", $chkDataId, $chkSelServicegroup);
            }

        }

        if ($chkModus == "modify") {

            // Host deps 
            if ($intSelHostDepend == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHost_DH", $chkDataId, $chkSelHostDepend);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHost_DH", $chkDataId);
            }

            // Hostgroup deps 
            if ($intSelHostgroupDepend == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHostgroup_DH", $chkDataId, $chkSelHostgroupDepend);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHostgroup_DH", $chkDataId);
            }

            // Hosts 
            if ($intSelHost == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHost_H", $chkDataId, $chkSelHost);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHost_H", $chkDataId);
            }

            // Hostgroup
            if ($intSelHostgroup == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToHostgroup_H", $chkDataId, $chkSelHostgroup);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToHostgroup_H", $chkDataId);
            }
            
            // Service deps only 
            if ($exactType == 'servicedependency') {
                
                // Service
                if ($intSelService == 1) {
                    $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToService_S", $chkDataId, $chkSelService);
                } else {
                    $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToService_S", $chkDataId);
                }

                // Service Groups
                if ($intSelServicegroup == 1) {
                    $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToServicegroup_S", $chkDataId, $chkSelServicegroup);
                } else {
                    $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToServicegroup_S", $chkDataId);
                }
                
                // Service dependencies 
                if ($intSelServiceDepend == 1)  {
                    $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToService_DS", $chkDataId, $chkSelServiceDepend);
                } else {
                    $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToService_DS", $chkDataId);
                }

                // Service group dependencies 
                if ($intSelServicegroupDepend == 1)  {
                    $ccm->data->dataUpdateRelation("tbl_lnk{$ucType}ToServicegroup_DS", $chkDataId, $chkSelServicegroupDepend);
                } else {
                    $ccm->data->dataDeleteRelation("tbl_lnk{$ucType}ToServicegroup_DS", $chkDataId);
                }

            }

        }

    }

    // If there are no errors, then we can set the apply config needed global variable
    if ($errors == 0) {
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified($exactType);
    }

    // Return status
    $strMessage = $ccm->data->strDBMessage;
    return array($errors, $strMessage);
}