<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: delete_object.inc.php
//  Desc: Handles the deletion of objects.
//


/**
 * Deletes a single object configuration from the nagiosql database, also removes relations
 *
 * @param   string  $type   Object type
 * @param   int     $id     The object id/primary key for the nagios object
 * @param   bool    $audit  Whether or not to log to audit log
 * @return  array           Return data for browser output
 */
function delete_object($type, $id, $audit=true) 
{
    global $ccm;

    // Bail if missing id 
    if (!$id) {
        $ccm->data->writeLog(_('Could not delete object type ' . $type), true, AUDITLOGTYPE_DELETE);
        return array(1, _("Cannot delete data, no object ID specified!"));
    }

    if ($type == 'log') { $type = 'logbook'; }
    $strMessage = '';

    $intReturn = $ccm->data->dataDeleteFull("tbl_".$type, $id, 0, $audit);
    $strMessage .= $ccm->data->strDBMessage;

    // If the above row delete was successful, remove config files for things that may need to be rewritten
    if ($intReturn == 0) {
        foreach ($ccm->data->arrDBIds as $data) {
            if ($data[0] == "tbl_host" || $data[0] == "tbl_service") {
                $strUpdSQL = "UPDATE `".$data[0]."` SET `last_modified`=NOW() WHERE `id` = '".$data[1]."' ";
                $intUpdate = $ccm->data->dataInsert($strUpdSQL, $intInsertId);
                if ($intUpdate != 0) {
                    $ccm->data->writeLog(_('Problem detected updating object name on relative: '.$data[0].'('.$data[1].')')." ".$chkInsName, true, AUDITLOGTYPE_DELETE);
                }
            }
        }

        // Update the ccm permissions
        if ($type == 'host' || $type == 'service') {
            ccm_remove_all_user_permissions($type, $id);
        }
    }

    // Return success or failure message 
    return array($intReturn, $strMessage);
}


/**
 * Deletes multiple object configurations from the nagiosql database, also removes relations
 *
 * @param   string  $table  The appropriate object database table
 * @return  array           Return data for browser output 
*/ 
function delete_multi($table)
{
    $checks = ccm_grab_request_var('checked', array());
    $failMessage = '';
    $itemsDeleted = 0;
    $itemsFailed = 0;
    
    foreach ($checks as $c) {
        $r = delete_object($table, $c, false);
        if ($r[0] == 0) {
            $itemsDeleted++;
        } else {
            $itemsFailed++;
            $failMessage .= $r[1]; // Append DB return messages
        }
    }

    $intReturn = 0;
    $returnMessage = '';
    if ($itemsFailed == 0 && $itemsDeleted == 0) { $returnMessage .= _("No items were deleted from the database.")."<br />"; }
    if ($itemsDeleted > 0) { $returnMessage .= $itemsDeleted." "._("items deleted")."<br />"; }
    if ($itemsFailed > 0) {
        $returnMessage .= "<strong>".$itemsFailed." "._("items failed to delete.")."</strong><br />
                                                    "._("Items may have dependent relationships that prevent deletion").".<br /> 
                                                    "._("Use the 'info'  button to see all relationships.")."
                                                    <img src='/nagiosql/images/info.gif' alt='' /><br />
                                                    $failMessage";
        $intReturn = 1;
    }

    return array($intReturn, $returnMessage);
}
