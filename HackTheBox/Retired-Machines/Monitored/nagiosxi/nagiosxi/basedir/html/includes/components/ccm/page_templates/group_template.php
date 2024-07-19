<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: group_template.php
//  Desc: Creates the HTML output of any Group object. (Host Group, Service Group, Contact Group)
//
?>

    <div id="tab1">
        <div class="leftBox">
            <div class="ccm-row">
                <label for="tfName"><?php echo ccm_get_full_title($this->exactType); ?> <?php echo _("Name"); ?> <span class="req">*</span></label>
                <input name="tfName" type="text" id="tfName" value="<?php val(encode_form_val($FIELDS[$this->exactType.'_name'])); ?>" class="required form-control fc-fl">
            </div>
            <div class="ccm-row">
                <label for="tfFriendly"><?php echo _("Alias"); ?> <span class="req">*</span></label>
                <input name="tfFriendly" class='required form-control fc-fl' type="text" id="tfFriendly" value="<?php val(encode_form_val($FIELDS['alias'])); ?>">
            </div>
            <?php
            // Host Group or Service Group only
            if ($this->exactType == 'hostgroup' || $this->exactType == 'servicegroup') {
            ?>
            <div class="ccm-row">
                <label for="tfNotes"><?php echo _("Notes"); ?></label>
                <input name="tfNotes" type="text" id="tfNotes" value="<?php val(encode_form_val($FIELDS['notes'])); ?>" class="form-control fc-fl">
            </div>
            <div class="ccm-row">
                <label for="tfNotesURL"><?php echo _("Notes URL"); ?></label>
                <input name="tfNotesURL" type="text" id="tfNotesURL" value="<?php val(encode_form_val($FIELDS['notes_url'])); ?>" class="form-control fc-fl">
            </div>
            <div class="ccm-row spacer">
                <label for="tfActionURL"><?php echo _("Action URL"); ?></label>
                <input name="tfActionURL" type="text" id="tfActionURL" value="<?php val(encode_form_val($FIELDS['action_url'])); ?>" class="form-control fc-fl">
            </div>
            <?php
            }
            
            // Check if the active button should be checked
            $active_checked = '';
            if ((isset($FIELDS['active']) && $FIELDS['active'] == '1') || !isset($FIELDS['active'])) {
                $active_checked = 'checked="checked"';
            }
            ?>
            <div class="ccm-row oneline">
                <div class="checkbox">
                    <label>
                        <input name="chbActive" type="checkbox" id="chbActive" value="1" <?php echo $active_checked; ?>>
                        <?php echo _("Active"); ?> <i class="fa fa-info-circle fa-14 tooltip-info" title="<?php echo _("Only active objects will be written to the config files and appear in Nagios. Inactive objects will only be shown in the CCM."); ?>"></i>
                    </label>
                </div>
            </div>
        </div>
        <div class="rightBox">
            <div class="ccm-row">
                <label><?php echo _('Assign Memberships'); ?></label>
            </div>
            <?php
            // Host groups only
            if ($this->exactType == 'hostgroup') {
                $hs = count($FIELDS['pre_hosts_AB']);
                $hgs = count($FIELDS['pre_hostgroups_AB']);
            ?>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-hostBox'onclick="overlay('hostBox')"><?php echo _("Manage Hosts"); ?> <span class="badge"><?php echo $hs; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-hostgroupBox' onclick="overlay('hostgroupBox')"><?php echo _("Manage Host Groups"); ?> <span class="badge"><?php echo $hgs; ?></span></button>
            </div>
            <?php 
            }

            // Service Groups only
            if ($this->exactType == 'servicegroup') {
                $ss = count($FIELDS['pre_hostservices_AB']);
                $sgs = count($FIELDS['pre_servicegroups_AB']);
            ?>          
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-hostserviceBox' onclick="overlay('hostserviceBox')"><?php echo _("Manage Services"); ?> <span class="badge"><?php echo $ss; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-servicegroupBox' onclick="overlay('servicegroupBox')"><?php echo _("Manage Service Groups"); ?> <span class="badge"><?php echo $sgs; ?></span></button>
            </div>
            <?php 
            }

            // Contact Groups only
            if ($this->exactType == 'contactgroup') {
                $cs = count($FIELDS['pre_contacts_AB']);
                $cgs = count($FIELDS['pre_contactgroups_AB']);
            ?>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-contactBox' onclick="overlay('contactBox')"><?php echo _("Manage Contacts"); ?> <span class="badge"><?php echo $cs; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-contactgroupBox' onclick="overlay('contactgroupBox')"><?php echo _("Manage Contact Groups"); ?> <span class="badge"><?php echo $cgs; ?></span></button>
            </div>
            <?php 
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>