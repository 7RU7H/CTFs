<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: timeperiod_template.php
//  Desc: Template of HTML for the layout of the Time Periods create/edit box.
//
?>
    <div id="tab1">
        <div class="top-box">
            <div class="leftBox">
                <div class="ccm-row">
                    <label for='tfName'><?php echo _("Timeperiod Name"); ?> <span class="req">*</span></label>
                    <input type="text" class="form-control fc-fl required" value="<?php val(encode_form_val($FIELDS['timeperiod_name'])); ?>" id='tfName' name='tfName'>
                </div>
                <div class="ccm-row">
                    <label for='tfFriendly'><?php echo _("Description"); ?> <span class="req">*</span></label>
                    <input type="text" class="form-control fc-fl required" value="<?php val(encode_form_val($FIELDS['alias'])); ?>" id="tfFriendly" name="tfFriendly">
                </div>
                <div class="ccm-row spacer">
                    <label for='tfTplName'><?php echo _("Template Name"); ?></label>
                    <input type="text" class="form-control fc-fl" value="<?php val(encode_form_val($FIELDS['name'])); ?>" id="tfTplName" name="tfTplName">
                </div>
                <?php 
                // Check if the active button should be checked
                $active_checked = '';
                if ((isset($FIELDS['active']) && $FIELDS['active'] == '1') || !isset($FIELDS['active'])) {
                    $active_checked = 'checked="checked"';
                }
                ?>
                <div class="ccm-row oneline spacer">
                    <div class="checkbox">
                        <label>
                            <input name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" <?php echo $active_checked; ?>> <?php echo _("Active"); ?>
                            <i class="fa fa-info-circle tooltip-info" title="<?php echo _("Only active objects will be written to the config files and appear in Nagios. Inactive objects will only be shown in the CCM."); ?>"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="ccm-row spacer">
            <div id="timeperiod-box">
                <div class="well">
                    <input type="text" class="form-control ccm-tt-bind" placeholder="<?php echo _("Time Definition"); ?>" title='<?php echo _("Examples"); ?>: "monday", "december 25", "use"' name="txtTimedefinition" id="txtTimedefinition">
                    <input type="text" class="form-control ccm-tt-bind" placeholder="<?php echo _("Time Range"); ?>" title='<?php echo _("Examples"); ?>: "00:00-24:00", "09:00-17:00", "us-holidays"' name="txtTimerange" id="txtTimerange">
                    <button type="button" class="btn btn-sm btn-info vat" onclick="insertTimeperiod(false,false)"><i class="fa fa-plus"></i> <?php echo _("Insert Definition"); ?></button>
                </div>
                <table class='table table-condensed table-striped table-no-margin'>
                    <thead>
                        <tr>
                            <th style="width: 45%;"><?php echo _("Time Definition"); ?></th>
                            <th style="width: 45%;"><?php echo _("Time Range"); ?></th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </thead>
                    <tbody id="tblTimeperiods">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ccm-row">
            <?php $tps = count($FIELDS['pre_excludes']); ?>
            <button type="button" class="btn btn-sm btn-info btn-excludeBox" onclick="overlay('excludeBox')"><?php echo _("Manage Timeperiod Exclusions"); ?> <span class="badge"><?php echo $tps; ?></span></a>
        </div>
    </div>