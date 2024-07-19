<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: activate.inc.php
//  Desc: Defines displays for all non-object configuration pages in the CCM.
//


/**
 * Routes the activate request to the functions below
 *
 * @param   string  $cmd
 * @param   string  $type
 * @param   int     $id
 * @return  mixed
 */
function route_activate($cmd, $type, $id)
{
    $active = (($cmd == 'activate' || $cmd =='activate_multi') ? 1 : 0);
    if ($cmd == 'deactivate' || $cmd =='activate') {
        return single_toggle_active($id, $type, $active);
    } else {
        return multi_toggle_activate($type, $active);
    }
}


/**
 * Checks if item can be deactivated
 *
 * @param int $id db item ID
 * @param string $type db table type 
 * @param int $active the value to set the `active` field to: 0 | 1 
 * @return mixed array($errors, $message) 
 */
function can_be_deactivated($id, $type, $active)
{
    global $ccm;
    $errors = 0;    
    $message = ''; 

    //check to make sure this item can be disabled:
    if ($active == 0) {
        $bool = $ccm->data->infoRelation('tbl_'.$type, $id, "id", 1);

        if (intval($bool) == 1) {
            $message .= _("Item") . " " . _("cannot be disabled because it has dependent relationships") . "<br />";
            return array(1, $message); 
        }
    }

    return array(0, $message);
}


/**
 * Deactivates/activates a single entry from specified table
 *
 * @param   int     $id         DB item ID
 * @param   string  $type       DB table type
 * @param   int     $active     The value to set the `active` field to: 0 | 1
 * @param   bool    $name       The item name
 * @return  mixed               array($errors, $message)
 */
function single_toggle_active($id, $type, $active, $name=false)
{
    global $ccm;
    $errors = 0;
    $message = '';

    if ($name == false) {
        $name = ccm_grab_request_var('objectName', '');
    }

    if (!$id || !$type || $name == '') {
        trigger_error('Missing required arguments for "single_toggle_actiive()"', E_USER_ERROR);  
    }

    // Check to make sure this item can be disabled
    if ($active == 0) {

        // Object cannot be disabled, dependent relationships
        $bool = $ccm->data->infoRelation('tbl_'.$type, $id, "id", 1);
        if (intval($bool) == 1) {
            $message .= _("Object")." {$name} "._("cannot be disabled because it has dependent relationships");
            return array(1, $message);
        }

        if ($type == 'host' || $type == 'service') {
            ccm_move_user_permissions($type, $id, false);
        }

    } else {

        // Check if there are any relations with hosts for services
        if ($type == 'service') {
            
            // Check host relations
            $query = "SELECT `n2`.`active` FROM `tbl_lnkServiceToHost` AS `n1`
                      LEFT JOIN `tbl_host` AS `n2` ON `n2`.`id` = n1.`idSlave`
                      WHERE `idMaster` = ".intval($id);
            $return = $ccm->db->query($query);
            foreach ($return as $r) {
                if ($r['active'] == 0) {
                    $message .= _("Object")." {$name} "._("cannot be activated because it has parent host relationships that are inactive");     
                    return array(1, $message); 
                }
            }

            // Check servicegroup relations
            $query = "SELECT `n2`.`active` FROM `tbl_lnkServiceToServicegroup` AS `n1`
                      LEFT JOIN `tbl_servicegroup` AS `n2` ON `n2`.`id` = n1.`idSlave`
                      WHERE `idMaster` = ".intval($id);
            $return = $ccm->db->query($query);
            foreach ($return as $r) {
                if ($r['active'] == 0) {
                    $message .= _("Object")." {$name} "._("cannot be activated because it has parent service group relationships that are inactive");    
                    return array(1, $message); 
                }
            }

            // Check hostgroup relations
            $query = "SELECT `n2`.`active` FROM `tbl_lnkServiceToHostgroup` AS `n1`
                      LEFT JOIN `tbl_hostgroup` AS `n2` ON `n2`.`id` = n1.`idSlave`
                      WHERE `idMaster` = ".intval($id);
            $return = $ccm->db->query($query);
            foreach ($return as $r) {
                if ($r['active'] == 0) {
                    $message .= _("Object")." {$name} "._("cannot be activated because it has parent host group relationships that are inactive");   
                    return array(1, $message);
                }
            }
        }

        if ($type == 'host' || $type == 'service') {
            ccm_move_user_permissions($type, $id, true);
        }

    }

    // Run query and capture any errors
    $query = "UPDATE tbl_{$type} SET `active`='".intval($active)."' WHERE `id`=".intval($id).";";
    $success = $ccm->db->query($query, false);
    if (!$success) {
        $message .= _("Update query failed.")." <br />".$ccm->db->error;
        $errors++;
    }
    
    // If the host has been disabled - Delete File
    if ($active == 0 && $type == 'host') {
        $cfg = $name.".cfg";
        $intReturn = $ccm->config->moveFile($type, $cfg);
        if ($intReturn != 0) {
            $message .=  _('Errors while deleting the old configuration file: ').$cfg._(' - please check permissions!')."<br />".$ccm->config->strDBMessage;
            $errors++;
        }
    }

    // Log the active/inactive switch
    $columns = $ccm->data->getKeyField($type) . ',' . $ccm->data->getKeyField($type, true);
    $query = "SELECT ".$columns." FROM `tbl_".$type."` WHERE `id`= ".intval($id).";";
    $object = $ccm->db->query($query);
    $object = $object[0];

    if ($active) {
        $msg = _("Activated");
    } else {
        $msg = _("Deactivated");
    }

    if ($type == 'service') {
        $msg .= " " . ccm_get_full_title($type, false, false) . ": " . $object['service_description'] . ' (' . _('from config') . ' ' . $object['config_name'] . ')';
    } else {
        $msg .= " " . ccm_get_full_title($type, false, false) . ": " . $object[$ccm->data->getKeyField($type)];
    }

    $ccm->data->writeLog($msg, AUDITLOGTYPE_MODIFY);

    if ($errors == 0) {
        // Save that something updated for the apply config needed variable
        set_option("ccm_apply_config_needed", 1);
        nagiosccm_set_table_modified($type);
        return array($errors, _("Object updated successfully.")."<br />".$message);
    } else {
        return array($errors, _("There was a problem updating the selected item type").": {$type}<br /> ID: {$id}<br />".$message); 
    }
}


/**
 * Enables / disables a selected array of objects
 *
 * @param string $type nagios object type
 * @param int $active boolean to set in the DB
 *
 * @return mixed $array( int $errors, string $message) 
 */
function multi_toggle_activate($type, $active)
{
    global $ccm;

    $failMessage= '';
    $itemsUpdated = 0;
    $itemsFailed = 0;
    
    $checks = ccm_grab_request_var('checked', array());
    foreach ($checks as $i => $c) {
        $checks[$i] = intval($c);
    }
    $idString = implode(',', $checks);
    list($table, $name, $desc) = get_table_and_fields($type);
        
    //fetch list of selected objects 
    $query = "SELECT `id`,`{$name}` FROM tbl_{$type} WHERE `id` IN({$idString})";   
    $results = $ccm->db->query($query);
    
    // Handle each item individually
    foreach ($results as $row) {
        $r = single_toggle_active($row['id'], $type, $active, $row[$name]);
        if ($r[0] === 0) {
            $itemsUpdated++;
        } else {
            $itemsFailed++;
            $failMessage .= $r[1]; // Append DB return messages
        }       
    }

    if ($itemsFailed > 0) {
        return array(1, $failMessage); 
    } else {     
        return array(0, $itemsUpdated." "._('objects updated successfully.')." <br />"); 
    }
}