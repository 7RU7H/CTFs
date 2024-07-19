<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: alert_settings.php
//  Desc: Creates the HTML for the "Alert Settings" tab in object management pages. Used in the
//        form class to output the area where everything is defined.
//
?>
    <div id="tab3" class="alert-settings">
        <div class='leftBox'>
            <div class="ccm-row">
                <?php
                $contacts = count($FIELDS['pre_contacts_AB']);
                $cgs = count($FIELDS['pre_contactgroups_AB']);
                ?>
                <div style="margin-bottom: 10px;">
                    <button type="button" class="btn btn-sm btn-info btn-contactBox" onclick="overlay('contactBox')"><i class="fa fa-user"></i> <?php echo _("Manage Contacts"); ?> <span class="badge"><?php echo $contacts; ?></span></button>
                    <button type="button" class="btn btn-sm btn-info btn-contactgroupBox" onclick="overlay('contactgroupBox')"><i class="fa fa-users"></i> <?php echo _("Manage Contact Groups"); ?> <span class="badge"><?php echo $cgs; ?></span></button>
                </div>
            </div>
            <div class="ccm-row">
                <label for="selNotifPeriod"><?php echo _("Notification period"); ?> <span class="req">*</span></label>
                <select name="selNotifPeriod" class="form-control fc-fl" id="selNotifPeriod">
                <?php
                // Time Period options for notifications
                $selected = '';
                if (isset($FIELDS['notification_period']) && $FIELDS['notification_period'] == '0') { $selected = ' selected="selected"'; }
                echo '<option value="0"'.$selected.'>&nbsp;</option>';
                foreach ($FIELDS['selTimeperiods'] as $opt) {
                    $selected = '';
                    if (isset($FIELDS['notification_period']) && $FIELDS['notification_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                    echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['timeperiod_name']).'</option>';
                }
                ?>
                </select>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Notification options"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    if (empty($FIELDS['notification_options'])) {
                        $FIELDS['notification_options'] = '';
                    }
                    $nops = explode(',', $FIELDS['notification_options']);

                    // Host and Host Templates only
                    if ($this->exactType == 'host' || $this->exactType == 'hosttemplate') {
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOd" type="checkbox" class="checkbox" id="chbNOd" value="d" <?php check('notification_options', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php } else { ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOw" type="checkbox" class="checkbox" id="chbNOw" value="w" <?php check('notification_options', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default  <?php if (in_array('c', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOc" type="checkbox" class="checkbox" id="chbNOc" value="c" <?php check('notification_options', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <?php } ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOu" type="checkbox" class="checkbox" id="chbNOu" value="u" <?php check('notification_options', 'u'); ?>> 
                        <?php 
                        if ($this->exactType == 'service' || $this->exactType == 'servicetemplate') {
                            echo _('Unknown');
                        } else {
                            echo _('Unreachable');
                        }
                        ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('r', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOr" type="checkbox" class="checkbox" id="chbNOr" value="r" <?php check('notification_options', 'r'); ?>> <?php echo _('Recovery'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('f', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOf" type="checkbox" class="checkbox" id="chbNOf" value="f" <?php check('notification_options', 'f'); ?>> <?php echo _('Flapping'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('s', $nops)) { echo 'active'; } ?>">
                        <input name="chbNOs" type="checkbox" class="checkbox" id="chbNOs" value="s" <?php check('notification_options', 's'); ?>> <?php echo _('Scheduled Downtime'); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfNotifInterval"><?php echo _("Notification interval"); ?></label>
                <div class="input-group">
                    <input name="tfNotifInterval" class="form-control" type="text" size='3' id="tfNotifInterval" value="<?php val(encode_form_val($FIELDS['notification_interval'])); ?>">
                    <span class="input-group-addon"><?php echo _("min"); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfFirstNotifDelay"><?php echo _("First notification delay"); ?></label>
                <div class="input-group">
                    <input name="tfFirstNotifDelay" class="form-control" type="text" size='3' id="tfFirstNotifDelay" value="<?php val(encode_form_val($FIELDS['first_notification_delay'])); ?>">
                    <span class="input-group-addon"><?php echo _("min"); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Notification enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['notifications_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radNotifEnabled" type="radio" class="checkbox" id="radNotifEnabled1" value="1" <?php check('notifications_enabled', '1'); ?>> <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['notifications_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radNotifEnabled" type="radio" class="checkbox" id="radNotifEnabled0" value="0" <?php check('notifications_enabled', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['notifications_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radNotifEnabled" type="radio" class="checkbox" id="radNotifEnabled2" value="2" <?php check('notifications_enabled', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['notifications_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radNotifEnabled" type="radio" class="checkbox" id="radNotifEnabled3" value="3" <?php check('notifications_enabled', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Stalking options"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    if (empty($FIELDS['stalking_options'])) {
                        $FIELDS['stalking_options'] = '';
                    }
                    $sops = explode(',', $FIELDS['stalking_options']);

                    // Host or Host Template only
                    $lopt = _('Ok');
                    $uopt = _('Unknown');

                    if ($this->exactType == 'host' || $this->exactType == 'hosttemplate') {
                        $lopt = _('Up');
                        $uopt = _('Unreachable');
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTd" type="checkbox" class=" checkbox" id="chbSTd" value="d" <?php check('stalking_options', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php } else { ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTw" type="checkbox" class=" checkbox" id="chbSTw" value="w" <?php check('stalking_options', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTc" type="checkbox" class=" checkbox" id="chbSTc" value="c" <?php check('stalking_options', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <?php } ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTo" type="checkbox" class=" checkbox" id="chbSTo" value="o" <?php check('stalking_options', 'o'); ?>> <?php echo $lopt; ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTu" type="checkbox" class=" checkbox" id="chbSTu" value="u" <?php check('stalking_options', 'u'); ?>> <?php echo $uopt; ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('N', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTN" type="checkbox" class=" checkbox" id="chbSTN" value="N" <?php check('stalking_options', 'N'); ?>> <?php echo _('Notification'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('n', $sops)) { echo 'active'; } ?>">
                        <input name="chbSTn" type="checkbox" class=" checkbox" id="chbSTn" value="n" <?php check('stalking_options', 'n'); ?>> <?php echo _('None'); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <!-- End of Tab 3 -->