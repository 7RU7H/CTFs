<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: check_settings.php
//  Desc: Creates the HTML for the "Check Settings" tab in object management pages. Used in the
//        form class to output the area where everything is defined.
//
?>
    <div id="tab2">
        <div class="leftBox">
            <div class="ccm-row spacer">
                <label><?php echo _("Initial state"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    $lopt = _('Ok');
                    $uopt = _('Unknown');

                    // Host or Host Template only
                    if ($this->exactType == 'host' || $this->exactType == 'hosttemplate') {
                        $lopt = _('Up');
                        $uopt = _('Unreachable');
                    ?>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['initial_state'] == 'd') { echo 'active'; } ?>">
                        <input name="radIS" type="radio" class="radio" id="radISd" value="d" <?php check('initial_state', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php } else { // Services ?>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['initial_state'] == 'w') { echo 'active'; } ?>">
                        <input name="radIS" type="radio" class="radio" id="radISw" value="w" <?php check('initial_state', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['initial_state'] == 'c') { echo 'active'; } ?>">
                        <input name="radIS" type="radio" class="radio" id="radISc" value="c" <?php check('initial_state', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label> 
                    <?php } // Everything else below ?>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['initial_state'] == 'o') { echo 'active'; } ?>">
                        <input name="radIS" type="radio" class="radio" id="radISo" value="o" <?php check('initial_state', 'o'); ?>> <?php echo $lopt; ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['initial_state'] == 'u') { echo 'active'; } ?>">
                        <input name="radIS" type="radio" class="radio" id="radISu" value="u" <?php check('initial_state', 'u'); ?>> <?php echo $uopt; ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfCheckInterval"><?php echo _("Check interval"); ?> </label>
                <div class="input-group">
                    <input name="tfCheckInterval" class="form-control" type="text" id="tfCheckInterval" value="<?php val(encode_form_val($FIELDS['check_interval'])); ?>">
                    <span class="input-group-addon"><?php echo _("min"); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfRetryInterval"> <?php echo _("Retry interval"); ?> </label>
                <div class="input-group">
                    <input name="tfRetryInterval" class="form-control" type="text" id="tfRetryInterval" value="<?php val(encode_form_val($FIELDS['retry_interval'])); ?>">
                    <span class="input-group-addon"><?php echo _("min"); ?></span>
                </div>
            </div>
            <div class="ccm-row spacer">
                <label for="tfMaxCheckAttempts"><?php echo _("Max check attempts"); ?> <span class="req">*</span></label>
                <div class="input-group">
                    <input name="tfMaxCheckAttempts" class="form-control" type="text" id="tfMaxCheckAttempts" value="<?php val(encode_form_val($FIELDS['max_check_attempts'])); ?>">
                    <span class="input-group-addon"><?php echo _("attempts"); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Active checks enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['active_checks_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radActiveChecksEnabled" type="radio" class="checkbox" id="radActiveChecksEnabled0" value="1" <?php check('active_checks_enabled', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['active_checks_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radActiveChecksEnabled" type="radio" class="checkbox" id="radActiveChecksEnabled1" value="0" <?php check('active_checks_enabled', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['active_checks_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radActiveChecksEnabled" type="radio" class="checkbox" id="radActiveChecksEnabled2" value="2" <?php check('active_checks_enabled', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['active_checks_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radActiveChecksEnabled" type="radio" class="checkbox" id="radActiveChecksEnabled3" value="3" <?php check('active_checks_enabled', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row spacer">
                <label><?php echo _("Passive checks enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['passive_checks_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radPassiveChecksEnabled" type="radio" class="checkbox" id="radPassiveChecksEnabled0" value="1" <?php check('passive_checks_enabled', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['passive_checks_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radPassiveChecksEnabled" type="radio" class="checkbox" id="radPassiveChecksEnabled1" value="0" <?php check('passive_checks_enabled', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['passive_checks_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radPassiveChecksEnabled" type="radio" class="checkbox" id="radPassiveChecksEnabled2" value="2" <?php check('passive_checks_enabled', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['passive_checks_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radPassiveChecksEnabled" type="radio" class="checkbox" id="radPassiveChecksEnabled3" value="3" <?php check('passive_checks_enabled', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row spacer">
                <label for="selCheckPeriod"><?php echo _("Check period"); ?> <span class="req">*</span></label>
                <select name="selCheckPeriod" class="form-control fc-fl" id="selCheckPeriod">
                    <?php
                    // Insert Time Periods
                    $selected = '';
                    if (isset($FIELDS['check_period']) && $FIELDS['check_period'] == '0') { $selected = ' selected="selected"'; }
                    echo '<option value="0"'.$selected.'>&nbsp;</option>';
                    foreach ($FIELDS['selTimeperiods'] as $opt) {
                        $selected = '';
                        if (isset($FIELDS['check_period']) && $FIELDS['check_period'] == $opt['id']) { $selected = ' selected="selected"'; }
                        echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['timeperiod_name']).'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="ccm-row">
                <label for="tfFreshThreshold"><?php echo _("Freshness threshold"); ?> </label>
                <div class="input-group">
                    <input name="tfFreshThreshold" class="form-control" type="text" id="tfFreshThreshold" value="<?php val(encode_form_val($FIELDS['freshness_threshold'])); ?>">
                    <span class="input-group-addon"><?php echo _('sec'); ?></span>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Check freshness"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['check_freshness'] == '1') { echo 'active'; } ?>">
                        <input name="radFreshness" type="radio" class="checkbox" id="radFreshness1" value="1" <?php check('check_freshness', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['check_freshness'] == '0') { echo 'active'; } ?>">
                        <input name="radFreshness" type="radio" class="checkbox" id="radFreshness0" value="0" <?php check('check_freshness', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['check_freshness'] == '2') { echo 'active'; } ?>">
                        <input name="radFreshness" type="radio" class="checkbox" id="radFreshness2" value="2" <?php check('check_freshness', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['check_freshness'] == '3') { echo 'active'; } ?>">
                        <input name="radFreshness" type="radio" class="checkbox" id="radFreshness3" value="3" <?php check('check_freshness', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="rightBox">
            <div class="ccm-row spacer">
                <?php
                // Host or Host Template only
                if ($this->exactType == 'host' || $this->exactType == 'hosttemplate') {
                ?>
                <label for="radObsess1"><?php echo _("Obsess over host"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_host'] == '1') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess1" value="1" <?php check('obsess_over_host', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_host'] == '0') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess0" value="0" <?php check('obsess_over_host', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_host'] == '2') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess2" value="2" <?php check('obsess_over_host', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_host'] == '3') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess3" value="3" <?php check('obsess_over_host', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                <?php } else { // Services only ?>
                <label for="radObsess1"><?php echo _("Obsess over service"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_service'] == '1') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess1" value="1" <?php check('obsess_over_service', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_service'] == '0') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess0" value="0" <?php check('obsess_over_service', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_service'] == '2') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess2" value="2" <?php check('obsess_over_service', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['obsess_over_service'] == '3') { echo 'active'; } ?>">
                        <input name="radObsess" type="radio" class="checkbox" id="radObsess3" value="3" <?php check('obsess_over_service', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                <?php } ?>
                </div>
            </div>
            <div class="ccm-row">
                <label for="selEventHandler"><?php echo _("Event handler"); ?></label>
                <select name="selEventHandler" class="form-control fc-fl" id="selEventHandler">
                <?php
                // Display Group options
                $selected = '';
                if (isset($FIELDS['event_handler']) && $FIELDS['event_handler'] == '0') { $selected = ' selected="selected"'; }
                echo '<option value="0"'.$selected.'>&nbsp;</option>';
                foreach ($FIELDS['selEventHandlers'] as $opt) {
                    $selected = '';
                    if (isset($FIELDS['event_handler']) && $FIELDS['event_handler'] == $opt['id']) { $selected = ' selected="selected"'; }
                    echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['command_name']).'</option>';    
                }
                ?>
                </select>
            </div>
            <div class="ccm-row spacer">
                <label for="radEventEnable1"><?php echo _("Event handler enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['event_handler_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radEventEnable" type="radio" class="checkbox" id="radEventEnable1" value="1" <?php check('event_handler_enabled', '1'); ?>> <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['event_handler_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radEventEnable" type="radio" class="checkbox" id="radEventEnable0" value="0" <?php check('event_handler_enabled', '0'); ?>> <?php echo _("Off"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['event_handler_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radEventEnable" type="radio" class="checkbox" id="radEventEnable2" value="2" <?php check('event_handler_enabled', '2'); ?>> <?php echo _("Skip"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['event_handler_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radEventEnable" type="radio" class="checkbox" id="radEventEnable3" value="3" <?php check('event_handler_enabled', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfLowFlat"><?php echo _("Low flap threshold"); ?></label>
                <div class="input-group">
                    <input name="tfLowFlat" type="text" class="form-control" id="tfLowFlat" value="<?php val(encode_form_val($FIELDS['low_flap_threshold'])); ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="ccm-row">
                <label for="tfHighFlat"><?php echo _("High flap threshold"); ?></label>
                <div class="input-group">
                    <input name="tfHighFlat" type="text" class="form-control" id="tfHighFlat" value="<?php val(encode_form_val($FIELDS['high_flap_threshold'])); ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="ccm-row">
                <label for="radFlapEnable1"><?php echo _("Flap detection enabled"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['flap_detection_enabled'] == '1') { echo 'active'; } ?>">
                        <input name="radFlapEnable" type="radio" class="checkbox" id="radFlapEnable1" value="1" <?php check('flap_detection_enabled', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['flap_detection_enabled'] == '0') { echo 'active'; } ?>">
                        <input name="radFlapEnable" type="radio" class="checkbox" id="radFlapEnable0" value="0" <?php check('flap_detection_enabled', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['flap_detection_enabled'] == '2') { echo 'active'; } ?>">
                        <input name="radFlapEnable" type="radio" class="checkbox" id="radFlapEnable2" value="2" <?php check('flap_detection_enabled', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['flap_detection_enabled'] == '3') { echo 'active'; } ?>">
                        <input name="radFlapEnable" type="radio" class="checkbox" id="radFlapEnable3" value="3" <?php check('flap_detection_enabled', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row spacer">
                <label for="chbFLo"><?php echo _("Flap detection options"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <?php
                    // Host and Host Templates only
                    $lopt = _('Ok');
                    $uopt = _('Unknown');

                    if (empty($FIELDS['flap_detection_options'])) {
                        $FIELDS['flap_detection_options'] = '';
                    }
                    $fdo = explode(',', $FIELDS['flap_detection_options']);

                    if ($this->exactType == 'host' || $this->exactType == 'hosttemplate') {
                        $lopt = _('Up');
                        $uopt = _('Unreachable');
                    ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('d', $fdo)) { echo 'active'; } ?>">
                        <input name="chbFLd" type="checkbox" class="checkbox" id="chbFLd" value="d" <?php check('flap_detection_options', 'd'); ?>> <?php echo _('Down'); ?>
                    </label>
                    <?php } else { ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('c', $fdo)) { echo 'active'; } ?>">
                        <input name="chbFLc" type="checkbox" class="checkbox" id="chbFLc" value="c" <?php check('flap_detection_options', 'c'); ?>> <?php echo _('Critical'); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('w', $fdo)) { echo 'active'; } ?>">
                        <input name="chbFLw" type="checkbox" class="checkbox" id="chbFLw" value="w" <?php check('flap_detection_options', 'w'); ?>> <?php echo _('Warning'); ?>
                    </label>
                    <?php } ?>
                    <label class="btn btn-xs btn-default <?php if (in_array('o', $fdo)) { echo 'active'; } ?>">
                        <input name="chbFLo" type="checkbox" class="checkbox" id="chbFLo" value="o" <?php check('flap_detection_options', 'o'); ?>> <?php echo 
                        $lopt; ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if (in_array('u', $fdo)) { echo 'active'; } ?>">
                        <input name="chbFLu" type="checkbox" class="checkbox" id="chbFLu" value="u" <?php check('flap_detection_options', 'u'); ?>> <?php echo 
                        $uopt; ?>
                    </label>
                </div>
            </div>
            <div class="ccm-row">
                <label><?php echo _("Retain status information"); ?></label>
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
                <label><?php echo _("Retain non-status information"); ?></label>
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
            <div class="ccm-row">
                <label><?php echo _("Process perf data"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['process_perf_data'] == '1') { echo 'active'; } ?>">
                        <input name="radPerfData" type="radio" class="checkbox" id="radPerfData1" value="1" <?php check('process_perf_data', '1'); ?>> <?php echo _("On"); ?>
                    </label> 
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['process_perf_data'] == '0') { echo 'active'; } ?>">
                        <input name="radPerfData" type="radio" class="checkbox" id="radPerfData0" value="0" <?php check('process_perf_data', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['process_perf_data'] == '2') { echo 'active'; } ?>">
                        <input name="radPerfData" type="radio" class="checkbox" id="radPerfData2" value="2" <?php check('process_perf_data', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['process_perf_data'] == '3') { echo 'active'; } ?>">
                        <input name="radPerfData" type="radio" class="checkbox" id="radPerfData3" value="3" <?php check('process_perf_data', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <?php
            // Service and Service Templates only
            if ($this->exactType == 'service' || $this->exactType == 'servicetemplate') {
            ?>
            <div class="ccm-row">
                <label><?php echo _("Is Volatile"); ?></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['is_volatile'] == '1') { echo 'active'; } ?>">
                        <input type="radio" value="1" id="radIsVolatile1" class="checkbox" name="radIsVolatile" <?php check('is_volatile', '1'); ?>> <?php echo _("On"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['is_volatile'] == '0') { echo 'active'; } ?>">
                        <input type="radio" value="0" id="radIsVolatile0" class="checkbox" name="radIsVolatile" <?php check('is_volatile', '0'); ?>> <?php echo _("Off"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['is_volatile'] == '2') { echo 'active'; } ?>">
                        <input type="radio" value="2" id="radIsVolatile2" class="checkbox" name="radIsVolatile" <?php check('is_volatile', '2'); ?>> <?php echo _("Skip"); ?>
                    </label>
                    <label class="btn btn-xs btn-default <?php if ($FIELDS['is_volatile'] == '3') { echo 'active'; } ?>">
                        <input type="radio" value="3" id="radIsVolatile3" class="checkbox" name="radIsVolatile" <?php check('is_volatile', '3'); ?>> <?php echo _("Null"); ?>
                    </label>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <!-- End of Tab 2 -->