<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: contact_template.php
//  Desc: Template of HTML for the layout of contact definition management.
//

$name = ($this->exactType=='contact') ? 'contact_name' : 'template_name'; 
$cgs = count($FIELDS['pre_contactgroups_AB']);
$cts = count($FIELDS['pre_contacttemplates']);

?>
    <div id="tab1">
        <div class="leftBox">
            <div class="ccm-row">
                <label for="tfName"><?php echo ccm_get_full_title($this->exactType); ?> <?php echo _("Name"); ?> <span class="req">*</span></label>
                <input name="tfName" type="text" id="tfName" value="<?php val(encode_form_val($FIELDS[$name])); ?>" class="required form-control fc-fl">
            </div>
            <div class="ccm-row">
                <label for="tfFriendly"><?php echo _("Description"); ?></label>
                <input name="tfFriendly" class='form-control fc-fl' type="text" id="tfFriendly" value="<?php val(encode_form_val($FIELDS['alias'])); ?>">
            </div>
            <div class="ccm-row">
                <label for="tfEmail"><?php echo _("Email Address"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['email'])); ?>" class='form-control fc-fl' id="tfEmail" name="tfEmail">
            </div>
            <div class="ccm-row spacer">
                <label for="tfPager"><?php echo _("Pager Number"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['pager'])); ?>" class='form-control fc-fl' id="tfPager" name="tfPager">
            </div>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-contactgroupBox' onclick="overlay('contactgroupBox')"><i class="fa fa-users"></i> <?php echo _("Manage Contact Groups"); ?> <span class="badge"><?php echo $cgs; ?></span></button>
            </div>
            <div class="ccm-row spacer">
                <button type="button" class='btn btn-sm btn-info btn-contacttemplateBox' onclick="overlay('contacttemplateBox')"><?php echo _("Manage Contact Templates"); ?> <span class="badge"><?php echo $cts; ?></span></button>
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
                        <input name="Active" type="checkbox" class="checkbox" id="Active" value="1" <?php echo $active_checked; ?>> <?php echo _('Active'); ?>
                        <i class="fa fa-info-circle tooltip-info" title="<?php echo _("Only active objects will be written to the config files and appear in Nagios. Inactive objects will only be shown in the CCM."); ?>"></i>
                    </label>
                </div>
            </div>
        </div>
        <div class="rightBox">      
            <div class="ccm-row">
                <label for="tfAddress1"><?php echo _("Addon Address 1"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address1'])); ?>" id="tfAddress1" name="tfAddress1" class='form-control fc-fl'>
            </div>
            <div class="ccm-row">
                <label for="tfAddress2"><?php echo _("Addon Address 2"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address2'])); ?>" id="tfAddress2" name="tfAddress2" class='form-control fc-fl'>
            </div>
            <div class="ccm-row">
                <label for="tfAddress3"><?php echo _("Addon Address 3"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address3'])); ?>" id="tfAddress3" name="tfAddress3" class='form-control fc-fl'>
            </div>
            <div class="ccm-row">
                <label for="tfAddress4"><?php echo _("Addon Address 4"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address4'])); ?>" id="tfAddress4" name="tfAddress4" class='form-control fc-fl'>
            </div>
            <div class="ccm-row">
                <label for="tfAddress5"><?php echo _("Addon Address 5"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address5'])); ?>" id="tfAddress5" name="tfAddress5" class='form-control fc-fl'>
            </div>
            <div class="ccm-row">
                <label for="tfAddress6"><?php echo _("Addon Address 6"); ?></label>
                <input type="text" value="<?php val(encode_form_val($FIELDS['address6'])); ?>" id="tfAddress6" name="tfAddress6" class='form-control fc-fl'>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <!-- End of Tab 1 -->

    <div id="tab2" class="contact-settings">
        <div class="leftBox">   
            <div class="ccm-row">
                <label><?php echo _("Host Notifications Enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['host_notifications_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radHostNotifEnabled" type="radio" class="checkbox" id="radHostNotifEnabled1" value="1" <?php check('host_notifications_enabled', '1'); ?>>
                        <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['host_notifications_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radHostNotifEnabled" type="radio" class="checkbox" id="radHostNotifEnabled0" value="0" <?php check('host_notifications_enabled', '0'); ?>>
                        <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['host_notifications_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radHostNotifEnabled" type="radio" class="checkbox" id="radHostNotifEnabled2" value="2" <?php check('host_notifications_enabled', '2'); ?>>
                        <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['host_notifications_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radHostNotifEnabled" type="radio" class="checkbox" id="radHostNotifEnabled3" value="3" <?php check('host_notifications_enabled', '3'); ?>>
                        <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for="selHostPeriod"><?php echo _("Host Notifications Timeperiod"); ?></label>
                <select id="selHostPeriod" class="form-control fc-fl" name="selHostPeriod">
                <?php
                // Insert Time Period options
                $selected = '';
                if (isset($FIELDS['host_notification_period']) && $FIELDS['host_notification_period'] == '0') { $selected = ' selected="selected"'; }
                echo '<option value="0"'.$selected.'>&nbsp;</option>'; 
                foreach ($FIELDS['selTimeperiods'] as $opt) {
                    $selected = '';
                    if (isset($FIELDS['host_notification_period']) && $FIELDS['host_notification_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                    echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['timeperiod_name']).'</option>';
                }                   
                ?>
                </select>
            </div>
            <div class="ccm-row spacer">
                <label for=""><?php echo _("Host Notification options"); ?></label>
                <?php $hno = explode(',', $FIELDS['host_notification_options']); ?>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOd" type="checkbox" class="checkbox" id="chbHOd3" value="d" <?php check('host_notification_options', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOu" type="checkbox" class=" checkbox" id="chbHOu3" value="u" <?php check('host_notification_options', 'u'); ?>> <?php echo _('Unreachable'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('r', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOr" type="checkbox" class=" checkbox" id="chbHOr3" value="r" <?php check('host_notification_options', 'r'); ?>> <?php echo _('Up'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('f', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOf" type="checkbox" class=" checkbox" id="chbHOf3" value="f" <?php check('host_notification_options', 'f'); ?>> <?php echo _('Flapping'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('s', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOs" type="checkbox" class=" checkbox" id="chbHOs3" value="s" <?php check('host_notification_options', 's'); ?>> <?php echo _('Scheduled Downtime'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('n', $hno)) { echo 'active'; } ?>">
                        <input name="chbHOn" type="checkbox" class=" checkbox" id="chbHOn3" value="n" <?php check('host_notification_options', 'n'); ?>> <?php echo _('None'); ?>
                    </label>
                </div>
            </div> 
            <div class="ccm-row spacer">
                <?php 
                $hcmds = count($FIELDS['pre_hostcommands']);
                ?>
                <button type="button" class='btn btn-sm btn-info btn-hostcommandBox' onclick="overlay('hostcommandBox')"><?php echo _("Manage Host Notification Commands"); ?> <span class="badge"><?php echo $hcmds; ?></span></button>
            </div>
            <div class="ccm-row">
                <label for=""><?php echo _("Retain status information"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_status_information'] == '1') { echo 'active'; } ?>">
                        <input name="radStatusInfos" type="radio" class="checkbox" id="radStatusInfos1" value="1" <?php check('retain_status_information', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_status_information'] == '0') { echo 'active'; } ?>">
                        <input name="radStatusInfos" type="radio" class="checkbox" id="radStatusInfos0" value="0" <?php check('retain_status_information', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_status_information'] == '2') { echo 'active'; } ?>">
                        <input name="radStatusInfos" type="radio" class="checkbox" id="radStatusInfos2" value="2" <?php check('retain_status_information', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_status_information'] == '3') { echo 'active'; } ?>">
                        <input name="radStatusInfos" type="radio" class="checkbox" id="radStatusInfos3" value="3" <?php check('retain_status_information', '3'); ?>> <?php echo _("Null"); ?>
                    </label> 
                </div>
            </div>
            <div class="ccm-row">
                <label for=""><?php echo _("Retain non-status information"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_nonstatus_information'] == '1') { echo 'active'; } ?>">
                        <input name="radNoStatusInfos" type="radio" class="checkbox" id="radNoStatusInfos1" value="1" <?php check('retain_nonstatus_information', '1'); ?>> <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_nonstatus_information'] == '0') { echo 'active'; } ?>">
                        <input name="radNoStatusInfos" type="radio" class="checkbox" id="radNoStatusInfos0" value="0" <?php check('retain_nonstatus_information', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_nonstatus_information'] == '2') { echo 'active'; } ?>">
                        <input name="radNoStatusInfos" type="radio" class="checkbox" id="radNoStatusInfos2" value="2" <?php check('retain_nonstatus_information', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['retain_nonstatus_information'] == '3') { echo 'active'; } ?>">
                        <input name="radNoStatusInfos" type="radio" class="checkbox" id="radNoStatusInfos3" value="3" <?php check('retain_nonstatus_information', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="rightBox">
            <div class="ccm-row">
                <label for=""><?php echo _("Service Notifications Enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['service_notifications_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radServiceNotifEnabled" type="radio" class="checkbox" id="radServiceNotifEnabled1" value="1" <?php check('service_notifications_enabled', '1'); ?>>
                        <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['service_notifications_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radServiceNotifEnabled" type="radio" class="checkbox" id="radServiceNotifEnabled0" value="0" <?php check('service_notifications_enabled', '0'); ?>>
                        <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['service_notifications_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radServiceNotifEnabled" type="radio" class="checkbox" id="radServiceNotifEnabled2" value="2" <?php check('service_notifications_enabled', '2'); ?>>
                        <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['service_notifications_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radServiceNotifEnabled" type="radio" class="checkbox" id="radServiceNotifEnabled3" value="3" <?php check('service_notifications_enabled', '3'); ?>>
                        <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for="selServicePeriod"><?php echo _("Service Notifications Timeperiod"); ?></label>
                <select id="selServicePeriod" class="form-control fc-fl" name="selServicePeriod">
                <?php
                // Insert Time Period options
                $selected = '';
                if (isset($FIELDS['service_notification_period']) && $FIELDS['service_notification_period'] == '0') { $selected = ' selected="selected"'; }
                echo '<option value="0"'.$selected.'>&nbsp;</option>';
                foreach ($FIELDS['selTimeperiods'] as $opt) {
                    $selected = '';
                    if(isset($FIELDS['service_notification_period']) && $FIELDS['service_notification_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                    echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['timeperiod_name']).'</option>';
                }
                ?>
                </select>
            </div>
            <div class="ccm-row spacer">
                <label for=""><?php echo _("Service Notification options"); ?></label>
                <?php $sno = explode(',', $FIELDS['service_notification_options']); ?>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOw" type="checkbox" class=" checkbox" id="chbSOw3" value="w" <?php check('service_notification_options', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOu" type="checkbox" class=" checkbox" id="chbSOu3" value="u" <?php check('service_notification_options', 'u'); ?>> <?php echo _('Unknown'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOc" type="checkbox" class=" checkbox" id="chbSOc3" value="c" <?php check('service_notification_options', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('f', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOf" type="checkbox" class=" checkbox" id="chbSOf3" value="f" <?php check('service_notification_options', 'f'); ?>> <?php echo _('Flapping'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('s', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOs" type="checkbox" class=" checkbox" id="chbSOs3" value="s" <?php check('service_notification_options', 's'); ?>> <?php echo _('Scheduled Downtime'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('r', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOr" type="checkbox" class=" checkbox" id="chbSOr3" value="r" <?php check('service_notification_options', 'r'); ?>> <?php echo _('Ok'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('n', $sno)) { echo 'active'; } ?>">
                        <input name="chbSOn" type="checkbox" class=" checkbox" id="chbSOn3" value="n" <?php check('service_notification_options', 'n'); ?>> <?php echo _('None'); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row spacer">
                <?php 
                $scmds = count($FIELDS['pre_servicecommands']);
                ?>
                <button type="button" class="btn btn-sm btn-info btn-servicecommandBox" onclick="overlay('servicecommandBox')"><?php echo _("Manage Service Notification Commands"); ?> <span class="badge"><?php echo $scmds; ?></span></a>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Can Submit Commands"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['can_submit_commands'] == '1') { echo 'active'; } ?>">
                        <input type="radio" value="1" id="radCanSubCmds1" class="checkbox" name="radCanSubCmds" <?php check('can_submit_commands', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['can_submit_commands'] == '0') { echo 'active'; } ?>">
                        <input type="radio" value="0" id="radCanSubCmds0" class="checkbox" name="radCanSubCmds" <?php check('can_submit_commands', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['can_submit_commands'] == '2') { echo 'active'; } ?>">
                        <input type="radio" value="2" id="radCanSubCmds2" class="checkbox" name="radCanSubCmds" <?php check('can_submit_commands', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['can_submit_commands'] == '3') { echo 'active'; } ?>">
                        <input type="radio" value="3" id="radCanSubCmds3" class="checkbox" name="radCanSubCmds" <?php check('can_submit_commands', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <!-- End of Tab 2 -->