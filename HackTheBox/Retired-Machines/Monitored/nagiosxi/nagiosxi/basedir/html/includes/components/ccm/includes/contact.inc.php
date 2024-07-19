<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: contact.inc.php
//  Desc: Handles the submit and processing of contacts.
//


/**
 * Handles form submissions for contact and contacttemplate object configurations 
 *
 * @return  array                   array(int $returnCode,string $returnMessage) return output for browser
 */
function process_contact_submission()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;

    // Grab form variables  
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);
    $chkDomainId = $_SESSION['domain'];

    $chkTfName = ccm_grab_request_var('tfName', '');
    $chkTfFriendly = ccm_grab_request_var('tfFriendly', '');
    $chkTfEmail = ccm_grab_request_var('tfEmail', '');
    $chkTfPager = ccm_grab_request_var('tfPager', '');
    $chkTfAddress1 = ccm_grab_request_var('tfAddress1', '');
    $chkTfAddress2 = ccm_grab_request_var('tfAddress2', '');
    $chkTfAddress3 = ccm_grab_request_var('tfAddress3', '');
    $chkTfAddress4 = ccm_grab_request_var('tfAddress4', '');
    $chkTfAddress5 = ccm_grab_request_var('tfAddress5', '');
    $chkTfAddress6 = ccm_grab_request_var('tfAddress6', '');

    $chkSelContactGroup = ccm_grab_request_var('contactgroups', array(""));
    $chkRadContactGroup = intval(ccm_grab_request_var('radContactgroup', 2));
    $chkHostNotifEnable = intval(ccm_grab_request_var('radHostNotifEnabled', 2));
    $chkServiceNotifEnable = intval(ccm_grab_request_var('radServiceNotifEnabled', 2));

    $chkSelHostPeriod = intval(ccm_grab_request_var('selHostPeriod', 0));
    $chkSelServicePeriod = intval(ccm_grab_request_var('selServicePeriod', 0));

    $chkSelHostCommand = ccm_grab_request_var('hostcommands', array(""));
    $chkRadHostCommand = intval(ccm_grab_request_var('radHostcommand', 2));
    $chkSelServiceCommand = ccm_grab_request_var('servicecommands', array(""));
    $chkRadServiceCommand = intval(ccm_grab_request_var('radServicecommand', 2));

    $chkRetStatInf = intval(ccm_grab_request_var('radStatusInfos', 2));
    $chkRetNonStatInf = intval(ccm_grab_request_var('radNoStatusInfos', 2));
    $chkCanSubCmds = intval(ccm_grab_request_var('radCanSubCmds', 2));

    // Template name
    $chkTfGeneric = ccm_grab_request_var('tfGenericName',"");

    // Checkbox options 
    $chbHOd = ccm_grab_request_var('chbHOd', '');
    $chbHOu = ccm_grab_request_var('chbHOu', '');
    $chbHOr = ccm_grab_request_var('chbHOr', '');
    $chbHOf = ccm_grab_request_var('chbHOf', '');
    $chbHOs = ccm_grab_request_var('chbHOs', '');
    $chbHOn = ccm_grab_request_var('chbHOn', '');
    $chbSOw = ccm_grab_request_var('chbSOw', '');
    $chbSOu = ccm_grab_request_var('chbSOu', '');
    $chbSOc = ccm_grab_request_var('chbSOc', '');
    $chbSOr = ccm_grab_request_var('chbSOr', '');
    $chbSOf = ccm_grab_request_var('chbSOf', '');
    $chbSOs = ccm_grab_request_var('chbSOs', '');
    $chbSOn = ccm_grab_request_var('chbSOn', '');

    $chkActive = intval(ccm_grab_request_var('Active', 0));

    // Check for dependent relationships
    if ($chkActive == 0) {
        include_once(INCDIR.'activate.inc.php');
        $returnContent = can_be_deactivated($chkDataId, $exactType, $chkActive);
        if ($returnContent[0] != 0) {
             return $returnContent;
        }
    }

    // Unused in current CCM and NagiosQL
    $chkRadTemplates = ccm_grab_request_var('radTemplate', 2);

    // Check for dependent relationships
    if ($chkActive == 0) {
        include_once(INCDIR.'activate.inc.php');
        $returnContent = can_be_deactivated($chkDataId, $exactType, $chkActive);
        if ($returnContent[0] != 0) {
            return $returnContent;
        }
    }

    // Build host/service notification options strings, add commas where needed 
    $strHO = '';
    foreach (array($chbHOd, $chbHOu, $chbHOr, $chbHOf, $chbHOs, $chbHOn) as $item) {
        if ($item != '') {
            $strHO .= $item.',';
        }
    }
    $strSO = ''; 
    foreach (array($chbSOw, $chbSOu, $chbSOc, $chbSOr, $chbSOf, $chbSOs, $chbSOn) as $item) {
        if ($item != '') {
            $strSO .= $item.',';
        } 
    }

    // Check for templates
    // =================================

    $templates = ccm_grab_request_var('contacttemplates', array());

    // Are templates being used?
    $intTemplates = (count($templates) > 0) ? 1 : 0;

    // Check for Free Variables
    // ================================

    $variables = ccm_grab_request_var('variables', array());
    $definitions = ccm_grab_request_var('variabledefs', array());

    // Freeform variables being used?
    $intVariables = (count($variables) ) > 0 ? 1 : 0;

    // Check submitted arrays
    $intContactGroups = 1;
    $intHostCommand = 1;
    $intServiceCommand = 1;

    if (($chkSelContactGroup[0] == "") || ($chkSelContactGroup[0] == "0")) {
        $intContactGroups = 0;
    }
    if (($chkSelHostCommand[0] == "") || ($chkSelHostCommand[0] == "0")) {
        $intHostCommand = 0;
    }
    if ($chkSelHostCommand[0] == "*") {
        $intHostCommand = 2;
    }
    if (($chkSelServiceCommand[0] == "") || ($chkSelServiceCommand[0] == "0")) {
        $intServiceCommand = 0;
    }
    if ($chkSelServiceCommand[0] == "*") {
        $intServiceCommand = 2;
    }

    $intContactGroups = (is_array($chkSelContactGroup) && in_array("*", $chkSelContactGroup) ) ? 2 : $intContactGroups;

    // Prepare SQL query 
    $strSQLx = "`tbl_".$exactType."` SET `alias`='$chkTfFriendly', `contactgroups`=$intContactGroups,
            `contactgroups_tploptions`=$chkRadContactGroup, `host_notifications_enabled`='$chkHostNotifEnable',
            `service_notifications_enabled`='$chkServiceNotifEnable', `host_notification_period`='$chkSelHostPeriod',
            `service_notification_period`='$chkSelServicePeriod', `host_notification_options`='$strHO',
            `host_notification_commands_tploptions`=$chkRadHostCommand, `service_notification_options`='$strSO',
            `host_notification_commands`=$intHostCommand, `service_notification_commands`=$intServiceCommand,
            `service_notification_commands_tploptions`=$chkRadServiceCommand, `can_submit_commands`='$chkCanSubCmds ',
            `retain_status_information`='$chkRetStatInf', `retain_nonstatus_information`='$chkRetNonStatInf', `email`='$chkTfEmail',
            `pager`='$chkTfPager', `address1`='$chkTfAddress1', `address2`='$chkTfAddress2', `address3`='$chkTfAddress3',
            `address4`='$chkTfAddress4', `address5`='$chkTfAddress5', `address6`='$chkTfAddress6', 
            `use_variables`='$intVariables', `use_template`=$intTemplates, `use_template_tploptions`='$chkRadTemplates',
            `active`='$chkActive', `config_id`='$chkDomainId', `last_modified`=NOW(),";

    if ($exactType == 'contact') {
        $strSQLx .= "`contact_name`='$chkTfName',`name`='$chkTfGeneric' ";
    } else {
        $strSQLx .= "`template_name`= '$chkTfName' ";
    }

    if ($chkModus == "insert") {
        $strSQL = "INSERT INTO ".$strSQLx;
        $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);
    } else { 
        $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";

        // Save original chkTfName so that we can check if it has changed
        if ($exactType == 'contact') {
            $strRelSQL = "SELECT `id`,`".$exactType."_name` FROM `tbl_".$exactType."` WHERE `id` = ".intval($chkDataId);
        } else {
            $strRelSQL = "SELECT `id`,`template_name` FROM `tbl_".$exactType."` WHERE `id` = ".intval($chkDataId);
        }
        $ccm->db->getDataArray($strRelSQL, $arrData, $intDataCount);
        $chkTfOldName = $arrData[0][''.$exactType.'_name'];

        // Run relation check to generate arrDBIds (array of related table,ids)
        $ccm->data->infoRelation("tbl_".$exactType, $chkDataId, "id");
        
        $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);

        // If the above insert succeeded and the config value used for the config
        // file name has changed,  iterate through the relation table/ids
        // updating the last_modified time on related hosts and services so
        // the CCM can update those files
        if ($intInsert == 0 && $chkTfOldName != $chkTfName) {
            foreach($ccm->data->arrDBIds as $data) {
                if ($data[0] == "tbl_host" || $data[0] == "tbl_service"){
                    $strUpdSQL  = "UPDATE `".$data[0]."` SET `last_modified`=NOW() WHERE `id` = '".intval($data[1])."' ";
                    $intUpdate = $ccm->data->dataInsert($strUpdSQL,$intInsertId);
                if ($intUpdate != 0) 
                    $ccm->data->writeLog(_('Problem detected updating object name on relative: '.$data[0].'('.$data[1].')')." ".$chkTfName);    
                }
            }
        }
    }

    if ($chkModus == "insert") {
        $chkDataId = $intInsertId;
    }

    // Bail on error    
    if ($intInsert == 1) {
        return array(1, $ccm->data->strDBMessage);        
    } else {

        if ($chkModus == "insert") {
            $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_ADD);
        }
        if ($chkModus == "modify") {
            $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_MODIFY);
        }

        //
        // Relationen eintragen/updaten
        // ============================

        if ($chkModus == "insert") {
            
            if ($intContactGroups == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToContactgroup", $chkDataId, $chkSelContactGroup);
            }

            if ($intHostCommand == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToCommandHost", $chkDataId, $chkSelHostCommand);
            }

            if ($intServiceCommand == 1) {
                $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToCommandService", $chkDataId, $chkSelServiceCommand);
            }

        } else if ($chkModus == "modify") {

            if ($intContactGroups == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToContactgroup",$chkDataId,$chkSelContactGroup);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToContactgroup",$chkDataId);
            }

            if ($intHostCommand == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToCommandHost", $chkDataId, $chkSelHostCommand);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToCommandHost", $chkDataId);
            }

            if ($intServiceCommand == 1) {
                $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToCommandService", $chkDataId, $chkSelServiceCommand);
            } else {
                $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToCommandService", $chkDataId);
            }

        }

        // Update template information
        // ========================================

        if ($chkModus == "modify") {
            $strSQL = "DELETE FROM `tbl_lnk".$ucType."ToContacttemplate` WHERE `idMaster`=$chkDataId";
            $booReturn = $ccm->data->dataInsert($strSQL, $intInsertId);
            if ($booReturn > 0) {
                $errors++;
            }
        }

        // If there are templates
        if ($intTemplates = 1) {
            $intSortId = 1;
            $tblTemplate = 'Contacttemplate';

            foreach ($templates as $elem) {

                $idTable = 1;
                if (strpos($elem, '::2')) {
                    $idTable = 2;
                    $elem = str_replace('::2', '', $elem);
                }

                $strSQL = "INSERT INTO `tbl_lnk".$ucType."To".$tblTemplate."` (`idMaster`,`idSlave`,`idTable`,`idSort`) VALUES ($chkDataId, $elem, $idTable , $intSortId)";
                $booReturn  = $ccm->data->dataInsert($strSQL, $intInsertId);  
                if ($booReturn > 0) {
                    $errors++;
                }

                $intSortId++;
            }
        }

        //
        // Update Variable definition relationships
        // ========================================

        // Clear out old variable definition
        if ($chkModus == "modify") {
            $strSQL = "SELECT * FROM `tbl_lnk".$ucType."ToVariabledefinition` WHERE `idMaster`=$chkDataId";
            $booReturn = $ccm->db->getDataArray($strSQL, $arrData, $intDataCount);

            if ($intDataCount != 0) {
                foreach ($arrData AS $elem) {
                    $strSQL = "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
                    $booReturn = $ccm->data->dataInsert($strSQL, $intInsertId);   
                    if ($booReturn > 0) {
                        $errors++;
                    }
                }
            }

            $strSQL = "DELETE FROM `tbl_lnk".$ucType."ToVariabledefinition` WHERE `idMaster`=$chkDataId";
            $booReturn = $ccm->data->dataInsert($strSQL, $intInsertId);  
            if ($booReturn > 0) {
                $errors++;
            }
        }

        // If there are variables to insert... 
        if ($intVariables == 1) {
            $vars = $variables;
            $defs = $definitions;
            $count = 0;

            for ($i = 0; $i < count($vars); $i++) {
                $strSQL = "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`) VALUES ('{$vars[$i]}','{$defs[$i]}',now())";
                $booReturn = $ccm->data->dataInsert($strSQL, $intInsertId);
                if ($booReturn > 0) {
                    $errors++;
                }

                $strSQL = "INSERT INTO `tbl_lnk".$ucType."ToVariabledefinition` (`idMaster`,`idSlave`)
                       VALUES ($chkDataId,$intInsertId)";
                $booReturn  = $ccm->data->dataInsert($strSQL, $intInsertId);
                if ($booReturn > 0) {
                    $errors++;
                }
            }
        }

    }

    // Status messages and share
    if ($errors > 0) {
        $strMessage .= _("There were ").$errors._(" errors while processing this request")."<br />".$ccm->data->strDBMessage;
    } else {
        $strMessage .= $ucType." <strong>".$chkTfName."</strong>"._(" sucessfully updated. ");
    }

    //
    // Last database update and file date
    // ======================================
    $ccm->config->lastModified("tbl_".$exactType, $strLastModified, $strFileDate, $strOld);

    // Check if there are errors then set the Apply Configuration as needed
    if ($errors == 0) {
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified($exactType);
    }

    return array($errors, $strMessage.'<br />');    
}