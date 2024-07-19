<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: escalation_dependency_template.php
//  Desc: Template of HTML for the layout of contact definition management.
//
?>
    <div id='tab1'>
        <div class="leftBox">
            <div class="ccm-row">
                <label for="tfConfigName"><?php echo _("Config Name"); ?> <span class="req" title="<?php echo _('Required'); ?>">*</span></label>
                <input name="tfConfigName" type="text" id="tfConfigName" class='required form-control fc-fl' value="<?php val(encode_form_val($FIELDS['config_name'])); ?>">
            </div>
            <?php
            // For escalations only
            if (strpos($this->exactType, 'escalation')) {
                $options = explode(',', $FIELDS['escalation_options']);
            ?>
            <div class="ccm-row">
                <label><?php echo _("First / Last Notification"); ?> <span class="req">*</span></label>
                <input type='text' class='form-control required' style="width: 49%; margin-right: 2%; display: inline-block;" name='tfFirstNotif' id='tfFirstNotif' placeholder="<?php echo _('First'); ?>" value="<?php val(encode_form_val($FIELDS['first_notification'])); ?>"><input type='text' class='form-control required' style="width: 49%; display: inline-block;" name='tfLastNotif' id='tfLastNotif' placeholder="<?php echo _('Last'); ?>" value="<?php val(encode_form_val($FIELDS['last_notification'])); ?>">
            </div>
            <div class="ccm-row">
                <label for='tfNotifInterval'><?php echo _("Notification Interval"); ?> <span class="req">*</span></label>
                <div class="input-group">
                    <input type='text' class='form-control required' name='tfNotifInterval' id='tfNotifInterval' size='2' value="<?php val(encode_form_val($FIELDS['notification_interval'])); ?>">
                    <span class="input-group-addon"><?php echo _("min"); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Escalation Options"); ?> <span class="req">*</span></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    // Host escalations
                    if ($this->exactType == 'hostescalation') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOd' id='chbEOd' <?php check('escalation_options', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('r', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOr' id='chbEOr' <?php check('escalation_options', 'r'); ?>> <?php echo _('Up'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOu' id='chbEOu' <?php check('escalation_options', 'u'); ?>> <?php echo _('Unreachable'); ?>
                    </label>
                    <?php
                    }

                    // Service escalations
                    if ($this->exactType =='serviceescalation') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOw' id='chbEOw' <?php check('escalation_options', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOc' id='chbEOc' <?php check('escalation_options', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('r', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOr' id='chbEOr' <?php check('escalation_options', 'r'); ?>> <?php echo _('Ok'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $options)) { echo 'active'; } ?>">
                        <input type='checkbox' name='chbEOu' id='chbEOu' <?php check('escalation_options', 'u'); ?>> <?php echo _('Unknown'); ?>
                    </label>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            }

            // For dependencies only
            if (strpos($this->exactType, 'dependency')) {
                // If it's a new service/host dependency let's set it to be defaulted to checked
                if ($FIELDS['inherits_parent'] == "") { $FIELDS['inherits_parent'] = 1; }
                $exfc = explode(',', $FIELDS['execution_failure_criteria']);
                $nfc = explode(',', $FIELDS['notification_failure_criteria']);
            ?>
            <div class="ccm-row oneline">
                <div class="checkbox">
                    <label>
                        <input class="checkbox" type="checkbox" value="1" name="chbInherit" id="chbInherit" <?php check('inherits_parent', 1); ?>> <?php echo _("Inherit from parents"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for=''><?php echo _("Execution failure criteria"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    // Host dependencies
                    if ($this->exactType == 'hostdependency') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $exfc)) { echo 'active'; } ?>">
                        <input id="chbEOo" class="checkbox" type="checkbox" value="o" name="chbEOo" <?php check('execution_failure_criteria', 'o'); ?>> <?php echo _('Up'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $exfc)) { echo 'active'; } ?>">
                        <input id='chbEOd' class='checkbox' type='checkbox' value='d' name='chbEOd' <?php check('execution_failure_criteria', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php
                    // Service dependencies
                    } else if ($this->exactType == 'servicedependency') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $exfc)) { echo 'active'; } ?>">
                        <input id="chbEOo" class="checkbox" type="checkbox" value="o" name="chbEOo" <?php check('execution_failure_criteria', 'o'); ?>> <?php echo _('Ok'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $exfc)) { echo 'active'; } ?>">
                        <input id='chbEOw' class='checkbox' type='checkbox' value='w' name='chbEOw' <?php check('execution_failure_criteria', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $exfc)) { echo 'active'; } ?>">
                        <input id='chbEOc' class='checkbox' type='checkbox' value='c' name='chbEOc' <?php check('execution_failure_criteria', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <?php
                    }
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $exfc)) { echo 'active'; } ?>">
                        <input id="chbEOu" class="checkbox" type="checkbox" value="u" name="chbEOu" <?php check('execution_failure_criteria', 'u'); ?>> <?php echo _('Unknown'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('p', $exfc)) { echo 'active'; } ?>">
                        <input id="chbEOp" class="checkbox" type="checkbox" value="p" name="chbEOp" <?php check('execution_failure_criteria', 'p'); ?>> <?php echo _('Pending'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('n', $exfc)) { echo 'active'; } ?>">
                        <input id="chbEOn" class="checkbox" type="checkbox" value="n" name="chbEOn" <?php check('execution_failure_criteria', 'n'); ?>> <?php echo _('None'); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for=''><?php echo _("Notification failure criteria"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    // Host dependencies
                    if ($this->exactType == 'hostdependency') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $nfc)) { echo 'active'; } ?>">
                        <input id="chbNOo" class="checkbox" type="checkbox" value="o" name="chbNOo" <?php check('notification_failure_criteria', 'o'); ?>> <?php echo _('Up'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $nfc)) { echo 'active'; } ?>">
                        <input id='chbNOd' class='checkbox' type='checkbox' value='d' name='chbNOd' <?php check('notification_failure_criteria', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php
                    // Service dependencies
                    } else if ($this->exactType == 'servicedependency') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $nfc)) { echo 'active'; } ?>">
                        <input id="chbNOo" class="checkbox" type="checkbox" value="o" name="chbNOo" <?php check('notification_failure_criteria', 'o'); ?>> <?php echo _('Ok'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $nfc)) { echo 'active'; } ?>">
                        <input id='chbNOw' class='checkbox' type='checkbox' value='w' name='chbNOw' <?php check('notification_failure_criteria', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>   
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $nfc)) { echo 'active'; } ?>">
                        <input id='chbNOc' class='checkbox' type='checkbox' value='c' name='chbNOc' <?php check('notification_failure_criteria', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <?php
                    }    
                    ?>      
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $nfc)) { echo 'active'; } ?>">
                        <input id="chbNOu" class="checkbox" type="checkbox" value="u" name="chbNOu" <?php check('notification_failure_criteria', 'u'); ?>> <?php echo _('Unknown'); ?>
                    </label>     
                    <label class="btn btn-xs btn-default <?php if (in_array('p', $nfc)) { echo 'active'; } ?>">
                        <input id="chbNOp" class="checkbox" type="checkbox" value="p" name="chbNOp" <?php check('notification_failure_criteria', 'p'); ?>> <?php echo _('Pending'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('n', $nfc)) { echo 'active'; } ?>">
                        <input id="chbNOn" class="checkbox" type="checkbox" value="n" name="chbNOn" <?php check('notification_failure_criteria', 'n'); ?>> <?php echo _('None'); ?>
                    </label>
                </div>
            </div>
            <?php
            }
            ?>
            <div class="ccm-row spacer">
                <label for='selPeriod'><?php echo ccm_get_full_title($this->exactType); ?> <?php echo _("Period"); ?></label>
                <select name='selPeriod' class="form-control fc-fl" id='selPeriod'>
                    <option value="0">&nbsp;</option>
                    <?php
                    foreach ($FIELDS['selTimeperiods'] as $opt) {
                        $selected = '';
                        if(isset($FIELDS['escalation_period']) && $FIELDS['escalation_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                        if(isset($FIELDS['dependency_period']) && $FIELDS['dependency_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                        echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['timeperiod_name']).'</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
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
            <?php
            $hs = count($FIELDS['pre_hosts_AB']);
            $hgs = count($FIELDS['pre_hostgroups_AB']);
            ?>

            <div class="ccm-row">
                <button id="manage_hosts_button" type="button" class="btn btn-sm btn-info btn-hostBox" onclick="overlay('hostBox')"><?php echo _("Manage Hosts"); ?> <span id='manage_hosts_count' class="badge"><?php echo $hs; ?></span></button>
            </div>
            <div class="ccm-row">
                <button id='manage_hostgroups_button' type="button" class='btn btn-sm btn-info btn-hostgroupBox' onclick="overlay('hostgroupBox')"><?php echo _("Manage Host Groups"); ?> <span id='manage_hostgroups_count' class="badge"><?php echo $hgs; ?></span></button>
            </div>
            <?php
            if (strpos($this->exactType, 'escalation')) {
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
            if ($this->exactType == 'serviceescalation' || $this->exactType == 'servicedependency') {
                $ss = count($FIELDS['pre_services']);
            ?>
            <div class="ccm-row">
                <button id='manage_services_button' type="button" class='btn btn-sm btn-info btn-serviceBox' onclick="overlay('serviceBox')"><?php echo _("Manage Services"); ?> <span id="manage_services_count" class="badge"><?php echo $ss; ?></span></button>
            </div>
            <?php
            }
            if ($this->exactType == 'serviceescalation') {
                $sg = count($FIELDS['pre_servicegroups_AB']);
            ?>
            <div class="ccm-row">
                <button id='manage_servicegroups_button' type="button" class='btn btn-sm btn-info btn-servicegroupBox' onclick="overlay('servicegroupBox')"><?php echo _("Manage Service Groups"); ?> <span id="manage_servicegroups_count" class="badge"><?php echo $sg; ?></span></button>
            </div>
            <?php
            } else if ($this->exactType == 'servicedependency') {
                $sg = count($FIELDS['pre_servicegroups']);
            ?>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-servicegroupBox' onclick="overlay('servicegroupBox')"><?php echo _("Manage Service Groups"); ?> <span class="badge"><?php echo $sg; ?></span></button>
            </div>
            <?php
            }
            ?>
            <div class="ccm-row spacer"></div>
            <?php
            if ($this->exactType == 'hostdependency' || $this->exactType == 'servicedependency') {
                $hds = count($FIELDS['pre_hostdependencys']);
                $hgds = count($FIELDS['pre_hostgroupdependencys']);
            ?>
            <div class="ccm-row">
                <label><?php echo _('Dependent Relationships'); ?></label>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-hostdependencyBox' onclick="overlay('hostdependencyBox')"><?php echo _("Manage Dependent Hosts"); ?> <span class="badge"><?php echo $hds; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-hostgroupdependencyBox' onclick="overlay('hostgroupdependencyBox')"><?php echo _("Manage Dependent Host Groups"); ?> <span class="badge"><?php echo $hgds; ?></span></button>
            </div>
            <?php
            }
            if ($this->exactType == 'servicedependency') {
                $sds = count($FIELDS['pre_servicedependencys']);
                $sdsg = count($FIELDS['pre_servicegroupdependencys']);
            ?>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-servicedependencyBox' onclick="overlay('servicedependencyBox')"><?php echo _("Manage Dependent Services"); ?> <span class="badge"><?php echo $sds; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-servicegroupdependencyBox' onclick="overlay('servicegroupdependencyBox')"><?php echo _("Manage Dependent Service Groups"); ?> <span class="badge"><?php echo $sdsg; ?></span></button>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>