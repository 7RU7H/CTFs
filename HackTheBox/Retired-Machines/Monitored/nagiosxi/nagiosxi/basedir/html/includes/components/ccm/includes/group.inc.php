<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: group.inc.php
//  Desc: Handles submissions (modify/insert) of group objects (Host, Service, Contact) in the CCM.
//


/**
 * Handles form submissions for hostgroup, contactgroup, and servicegroup object configurations 
 *
 * @return  array                   array(int $returnCode,string $returnMessage) return output for browser
 */
function process_ccm_group()
{
    global $ccm;
    $strMessage = "";
    $errors = 0;

    // Process form variables 
    $chkModus = ccm_grab_request_var('mode');
    $chkDataId = intval(ccm_grab_request_var('hidId'));
    $chkOldName = ccm_grab_request_var('hidName', '');
    $exactType = ccm_grab_request_var('exactType');
    $genericType = ccm_grab_request_var('genericType');
    $ucType = ucfirst($exactType);
    $chkTfName = ccm_grab_request_var('tfName', '');
    $chkTfFriendly = ccm_grab_request_var('tfFriendly', '');
    
    // Changed from chckSelMembers 
    $chkSelHostMembers = ccm_grab_request_var('hosts', array(''));
    $chkSelHostMembersExc = ccm_grab_request_var('hosts_exc', array(''));
    $chkSelHostgroupMembers = ccm_grab_request_var('hostgroups', array(""));
    $chkSelHostgroupMembersExc = ccm_grab_request_var('hostgroups_exc', array(""));
    $chkSelServicegroupMembers = ccm_grab_request_var('servicegroups', array(""));
    $chkSelHostServiceMembers = ccm_grab_request_var('hostservices', array());
    
    // Contactgroup specific vars    
    $chkSelContactMembers = ccm_grab_request_var('contacts', array(''));
    $chkSelContactgroupMembers = ccm_grab_request_var('contactgroups', array(''));
    
    $chkTfNotes = ccm_grab_request_var('tfNotes', '');
    $chkTfNotesURL = ccm_grab_request_var('tfNotesURL', '');
    $chkTfActionURL = ccm_grab_request_var('tfActionURL', '');
    $chkActive = ccm_grab_request_var('chbActive', 0);  

    $chkDomainId = $_SESSION['domain'];

    // Handle Lists
    // =================

    // Determine host memberships 
    $intSelHostMembers = 1;
    if ($chkSelHostMembers[0] == "" || $chkSelHostMembers[0] == "0") {
        $intSelHostMembers = 0;
    }
    if (in_array("*", $chkSelHostMembers)) {
        $intSelHostMembers = 2;
    }

    // Determine service memberships
    $intSelHostServiceMembers = 1;
    if (count($chkSelHostServiceMembers) == 0) {
        $intSelHostServiceMembers = 0;
    }
    if (is_array($chkSelHostServiceMembers) && in_array("*", $chkSelHostServiceMembers)) {
        $intSelHostServiceMembers = 2;
    }

    // Determine hostgroup memberships
    $intSelHostgroupMembers = 1;
    if ($chkSelHostgroupMembers[0] == "" || $chkSelHostgroupMembers[0] == "0") {
        $intSelHostgroupMembers = 0;
    }
    if (in_array("*", $chkSelHostgroupMembers)) {
        $intSelHostgroupMembers = 2;
    }

    // Determine servicegroup memberships
    $intSelServicegroupMembers = 1;
    if ($chkSelServicegroupMembers[0] == "" || $chkSelServicegroupMembers[0] == "0") {
        $intSelServicegroupMembers = 0;
    }
    if (in_array("*", $chkSelServicegroupMembers)) {
        $intSelServicegroupMembers = 2;
    }

    // Determine contact memberships
    $intSelContactMembers = 1;
    if ($chkSelContactMembers[0] == "" || $chkSelContactMembers[0] == "0") {
        $intSelContactMembers = 0;
    }
    if (in_array("*", $chkSelContactMembers)) {
        $intSelContactMembers = 2;
    }

    // Determine contactgroup memberships
    $intSelContactgroupMembers = 1;
    if ($chkSelContactgroupMembers[0] == "" || $chkSelContactgroupMembers[0] == "0") {
        $intSelContactgroupMembers = 0;
    }
    if (in_array("*", $chkSelContactgroupMembers)) {
        $intSelContactgroupMembers = 2;
    }

    // Build SQL Query based on mode and object type
    // =================

    if (($chkModus == "insert") || ($chkModus == "modify")) 
    {
        $strSQLx = "`tbl_{$exactType}` SET `{$exactType}_name`='$chkTfName', `alias`='$chkTfFriendly', `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW(), ";

        if ($exactType != 'contactgroup') { 
            $strSQLx .="`notes`='$chkTfNotes', `notes_url`='$chkTfNotesURL', `action_url`='$chkTfActionURL', ";
        }

        if ($exactType == 'hostgroup') {
            $strSQLx .= "`members`=$intSelHostMembers,`{$exactType}_members`=$intSelHostgroupMembers";
        }

        if ($exactType == 'servicegroup') {
            $strSQLx .= "`members`=$intSelHostServiceMembers,`{$exactType}_members`=$intSelServicegroupMembers";
        }

        if ($exactType == 'contactgroup') {
            $strSQLx .= "`members`=$intSelContactMembers,`{$exactType}_members`=$intSelContactgroupMembers";
        }

        if ($chkModus == "insert") {
            $strSQL = "INSERT INTO ".$strSQLx;
            $intInsert = $ccm->data->dataInsert($strSQL,$intInsertId);
        } else {
            $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";

            // Save original chkTfName so that we can check if it has changed
            $strRelSQL  = "SELECT `id`,`".$exactType."_name` FROM `tbl_".$exactType."` WHERE `id` = '$chkDataId' ";
            $ccm->db->getDataArray($strRelSQL, $arrData, $intDataCount);
            $chkTfOldName = $arrData[0][''.$exactType.'_name'];

            // Run relation check to generate arrDBIds (array of related table,ids)
            $ccm->data->infoRelation("tbl_".$exactType, $chkDataId, "id");
            $intInsert = $ccm->data->dataInsert($strSQL, $intInsertId);

            // If the above insert succeeded and the config value used for the config
            // file name has changed, iterate through the relation table/ids
            // updating the last_modified time on related hosts and services so
            // the CCM can update those files
            if ($intInsert == 0 && $chkTfOldName != $chkTfName) {
                foreach($ccm->data->arrDBIds as $data) {
                    if ($data[0] == "tbl_host" || $data[0] == "tbl_service") {
                        $strUpdSQL = "UPDATE `".$data[0]."` SET `last_modified`=NOW() WHERE `id` = '".$data[1]."' ";
                        $intUpdate = $ccm->data->dataInsert($strUpdSQL, $intInsertId);
                        if ($intUpdate != 0) {
                            $ccm->data->writeLog(_('Problem detected updating object name on relative: '.$data[0].'('.$data[1].')')." ".$chkTfName);
                        }
                    }
                }
            }
        }

        // Bail if initial insert fails
        if ($intInsert > 0) {
            $errors++;
            $strMessage .= $ccm->data->strDBMessage;
            return array($errors, $strMessage);
        }

        if ($chkModus == "insert") {
            $chkDataId = $intInsertId;
        }

        if ($intInsert == 1) {
            $intReturn = 1;
        } else {

            if ($chkModus == "insert") {
                $ccm->data->writeLog(_('Created '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_ADD);
            }
            if ($chkModus == "modify") {
                $ccm->data->writeLog(_('Modified '.strtolower(ccm_get_full_title($exactType))).": ".$chkTfName, AUDITLOGTYPE_MODIFY);
            }

            //
            // Update Relations
            // ============================

            if ($chkModus == "insert") {

                if ($intSelHostMembers == 1) {
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToHost", $chkDataId, $chkSelHostMembers);
                }

                if ($intSelHostgroupMembers == 1) {
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToHostgroup", $chkDataId, $chkSelHostgroupMembers);
                }

                if ($intSelServicegroupMembers == 1) { 
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToServicegroup", $chkDataId, $chkSelServicegroupMembers);
                }

                if ($intSelHostServiceMembers == 1) {
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToService", $chkDataId, $chkSelHostServiceMembers, 1);
                }

                if ($intSelContactMembers == 1) { 
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToContact", $chkDataId, $chkSelContactMembers);
                }

                if ($intSelContactgroupMembers == 1) {
                    $ccm->data->dataInsertRelation("tbl_lnk".$ucType."ToContactgroup", $chkDataId, $chkSelContactgroupMembers);
                }

            } else if ($chkModus == "modify") {

                switch ($exactType)
                {

                    case 'hostgroup':

                        // Host links
                        if ($intSelHostMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToHost", $chkDataId, $chkSelHostMembers, 0, $chkSelHostMembersExc);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToHost", $chkDataId);
                        }

                        // Hostgroup links
                        if ($intSelHostgroupMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToHostgroup", $chkDataId, $chkSelHostgroupMembers, 0, $chkSelHostgroupMembersExc);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToHostgroup", $chkDataId);
                        }

                        break;

                    case 'servicegroup':

                        // Servicegroup links
                        if ($intSelServicegroupMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToServicegroup", $chkDataId, $chkSelServicegroupMembers);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToServicegroup", $chkDataId);
                        }

                        // Service links
                        if ($intSelHostServiceMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToService", $chkDataId, $chkSelHostServiceMembers, 1);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToService", $chkDataId);
                        }

                        break;

                    case 'contactgroup':

                        // Contact links
                        if ($intSelContactMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToContact", $chkDataId, $chkSelContactMembers);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToContact", $chkDataId);
                        }

                        // Contactgroup links 
                        if ($intSelContactgroupMembers == 1) {
                            $ccm->data->dataUpdateRelation("tbl_lnk".$ucType."ToContactgroup", $chkDataId, $chkSelContactgroupMembers);
                        } else {
                            $ccm->data->dataDeleteRelation("tbl_lnk".$ucType."ToContactgroup", $chkDataId);
                        }

                    break;

                }

            }

            $intReturn = 0;
        }
    }

    // Log return status and send back to page router 
    if (isset($intReturn) && ($intReturn == 1)) {
        $strMessage .= $ccm->data->strDBMessage;
    }
    if (isset($intReturn) && ($intReturn == 0)) {
        $strMessage .= $ucType." <strong>".$chkTfName."</strong>"._(" sucessfully updated") . ".";
    }

    //
    // Last database update and file date
    // ======================================

    $ccm->config->lastModified("tbl_".$exactType, $strLastModified, $strFileDate, $strOld);

    // Check if there are errors then set the Apply Configuration as needed
    if ($errors == 0) {
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified($exactType);

        // Run a callback to do anything else we need to do when saving
        // the hostgroup or servicegroup
        $args = array(
            'type' => $exactType,
            'id' => $chkDataId
        );

        if ($exactType == 'hostgroup') {
            $args['hostgroup_name'] = $chkTfName;
            $args['old_hostgroup_name'] = $chkOldName;
        } else if ($exactType == 'servicegroup') {
            $args['servicegroup_name'] = $chkTfName;
            $args['old_servicegroup_name'] = $chkOldName;
        }

        if ($chkModus == 'insert') {
            do_callbacks(CALLBACK_CCM_INSERT_GROUP, $args);
        } else if ($chkModus == 'modify') {
            do_callbacks(CALLBACK_CCM_MODIFY_GROUP, $args);
        }
    }

    return array($errors, $strMessage.'<br />');
}