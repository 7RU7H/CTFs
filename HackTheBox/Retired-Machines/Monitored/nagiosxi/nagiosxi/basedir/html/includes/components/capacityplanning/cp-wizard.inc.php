<?php 
//
// Capacity Planning Component
// Copyright (c) 2014-2019 Nagios Enterprises, LLC. All rights reserved.
//  


/* This function is automatically called by the XI wizard framework when the wizard is run */
function capacity_planning_configwizard_func($mode = '',$inargs = null,&$outargs,&$result){

    /* Define the wizard name */
    $wizard_name = 'capacity-planning';

    /* Initialize return code and output */
    $result = 0;

    $output = '';

    /* Initialize output args */
    $outargs[CONFIGWIZARD_PASSBACK_DATA] = $inargs;

    switch ($mode) {
        case CONFIGWIZARD_MODE_GETSTAGE1HTML:
            $nextstep = encode_form_val(grab_array_var($_POST,'nextstep',false),ENT_QUOTES);
            if ($nextstep == '') {
                unset($_SESSION[$wizard_name]); 
            }

            /* Continuing from CONFIGWIZARD_MODE_VALIDATESTAGE1DATA due to user error */

            $serviceargs_session =  grab_array_var($_SESSION[$wizard_name], 'serviceargs', array());
            $serviceargs = grab_array_var($inargs, 'serviceargs', $serviceargs_session);

            unset($serviceargs['counter_here']);

            $print_these_hosts = get_xml_host_objects();

            /* Now we are creating the HTML for CONFIGWIZARD_MODE_GETSTAGE1HTML */ 
            $output .= '
            <h5 class="ul">' . _('Select Data to Monitor') . '</h5>

            <script src="../includes/configwizards/capacity-planning/capacity-planning-page-1.inc.js"></script>
            <link rel="stylesheet" href="../includes/configwizards/capacity-planning/capacity-planning.inc.css">
            <div class="hide" id="default-service-selector"><option value="">' . _("Service") . ':</option></div>
            <div class="hide" id="default-data-selector"><option value="">' . _("Perfdata Name") . ':</option></div>
            <div class="hide" id="default-remove-row"><img class="tt-bind remove-row" src="'.theme_image('cross.png').'" title="'._('Remove this row').'"></div>
            <div class="hide" id="no-threshold-message">'. _("N/A") . '</div>
            <div class="hide" id="threshold-message">' . _("Alert if the threshold (%d) is reached within %s days.") . '</div>
            <div class="hide" id="needs-custom-value">'. _('This plugin does not report a warning/critical threshold. Set capacity planning thresholds on the next page.') . '</div>
            <div class="hide" id="unselected-error">'. _('Please make sure to select a host, service, and perfdata name.') . '</div>
            <div class="hide" id="default-row">
                <div class="flex-center grid-width-6">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[counter_here][host]" value="host_here" />
                        <input type="hidden" name="serviceargs[counter_here][service]" value="service_here" />
                        <input type="hidden" name="serviceargs[counter_here][perfdata]" value="perfdata_here" />
                        needs_custom_input_here
                        <label class="input-group-addon input-grouped smaller" style="height: 30px">'._('Name').'</label>
                        <label class="input-group-addon input-grouped tt-bind" style="width: 484px;" title="">host_here - service_here - perfdata_here</label>
                    </div>
                </div>
                <div class="flex-center grid-width-3">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[counter_here][warn]" value="warn_here" />
                        <label class="input-group-addon input-grouped"><img src="'.theme_image('warning_small.png').'" class="" style="vertical-align: bottom"></label>
                        <input type="text" size="2" class="textfield form-control input-grouped tt-bind" name="serviceargs[counter_here][warn-days]" value="warn_days_here" title="needs_custom_value_here" warn_disabled_here/>
                        <label class="input-group-addon input-grouped">days</label>
                    </div>
                </div>
                <div class="flex-center grid-width-3">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[counter_here][crit]" value="crit_here" />
                        <label class="input-group-addon input-grouped"><img src="'.theme_image('critical_small.png').'" class="" style="vertical-align: bottom"></label>
                        <input type="text" size="2" class="textfield form-control input-grouped tt-bind" name="serviceargs[counter_here][crit-days]" value="crit_days_here" title="needs_custom_value_here" crit_disabled_here/>
                        <label class="input-group-addon input-grouped">days</label>
                    </div>
                </div>
                <div class="flex-center grid-width-1">
                    <img class="tt-bind remove-row" src="'.theme_image('cross.png').'" title="'._('Remove this row').'">
                </div>
            </div>
            <div class="hide" id="no-entries-message">
                <div class="flex-center grid-width-3 remove-on-addition">
                    <div class="input-group">
                        <label class="input-group-addon input-grouped smaller" style="height: 30px; border-right: 1px solid #ccc">'. _('No selected entries yet') .'</label>
                    </div>
                </div>
            </div>

            <table class="table table-condensed table-no-border table-auto-width" id="perfdata-selector">
                <tr>
                    <td>
                        <select name="host-selector" id="host-selector" class="form-control">
                            <option value="">' . _("Host") . ':</option>
                            ';
                            // get_xml_host_objects() returns a reversed list... can't figure out the SQL mapping so we'll iterate in reverse.
                            $x = 0;
                            for ($x = count($print_these_hosts->host) - 1; $x >= 0; $x--) {
                                $host = $print_these_hosts->host[$x];
                                $output .= '<option value="' . $host->host_name . '">' . $host->host_name . '</option>
                                ';
                            }

                        $output .= '
                        </select>
                    </td>
                    <td>
                        <select name="service-selector" id="service-selector" class="form-control">
                            <option value="">' . _("Service") . ':</option>
                        </select>
                    </td>
                    <td>
                        <select name="data-selector" id="data-selector" class="form-control">
                            <option value="">' . _("Perfdata Name") . ':</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" id="add-to-list" class="btn btn-sm btn-primary">' . _("Add to List") . '</button>
                </tr>
            </table>

            <h5 class="ul">'. _('Basic Configuration') . '</h5>

            <div class="basic-grid" id="table_insert_here" style="overflow: visible; grid-template-rows: repeat('. max(count($serviceargs), 1) . ', 50px);">';
            foreach ($serviceargs as $index => $row) {

                $needs_custom = (empty($row['warn']) || $row['warn'] === _("N/A")) && (empty($row['crit']) || $row['crit'] === _("N/A"));

                $warn_disabled = false;
                if ($row['warn'] === _("N/A") || $row['warn'] === '') {
                    $warn_disabled = true;
                }

                $crit_disabled = false;
                if ($row['crit'] === _("N/A") || $row['crit'] === '') {
                    $crit_disabled = true;
                }

                $output .= '<div class="flex-center grid-width-6">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[' . $index . '][host]" value="' . $row['host'] . '" />
                        <input type="hidden" name="serviceargs[' . $index . '][service]" value="' . $row['service'] . '" />
                        <input type="hidden" name="serviceargs[' . $index . '][perfdata]" value="' . $row['perfdata'] . '" />
                        '.($needs_custom? '<input type="hidden" name="serviceargs['. $index . '][need_custom]" value="1" />': '') .'
                        <label class="input-group-addon input-grouped smaller" style="height: 30px;">'._('Name').'</label>
                        <label class="input-group-addon input-grouped tt-bind" style="width: 484px;" title="'.($needs_custom ? _('This plugin does not report a warning/critical threshold. Set capacity planning thresholds on the next page.') : '').'">'.$row['host'] . ' - ' . $row['service'] . ' - ' . $row['perfdata'].'</label>
                    </div>
                </div>
                <div class="flex-center grid-width-3">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[' . $index . '][warn]" value="' . $row['warn'] . '" />
                        <label class="input-group-addon input-grouped"><img src="'.theme_image('warning_small.png').'" class="tt-bind" style="vertical-align: bottom" title=""></label>
                        <input type="text" size="2" class="textfield form-control input-grouped tt-bind" name="serviceargs[' . $index . '][warn-days]" value="' . ($warn_disabled ? '' : $row['warn-days']) . '" title="'.($warn_disabled ? sprintf(_("Alert if the threshold (%s) is reached within ___ days."), $row['warn']) : '').'" ' . ($warn_disabled ? 'disabled' : '') .'/>
                        <label class="input-group-addon input-grouped">days</label>
                    </div>
                </div>
                <div class="flex-center grid-width-3">
                    <div class="input-group">
                        <input type="hidden" name="serviceargs[' . $index . '][crit]" value="' . $row['crit'] . '" />
                        <label class="input-group-addon input-grouped"><img src="'.theme_image('critical_small.png').'" class="tt-bind" style="vertical-align: bottom" title=""></label>
                        <input type="text" size="2" class="textfield form-control input-grouped tt-bind" name="serviceargs[' . $index . '][crit-days]" value="' . ($warn_disabled ? '' : $row['crit-days']) . '" title="'.($crit_disabled ? sprintf(_("Alert if the threshold (%s) is reached within ___ days."), $row['crit']) : '').'" ' . ($crit_disabled ? 'disabled' : '') .'/>
                        <label class="input-group-addon input-grouped">days</label>
                    </div>
                </div>
                <div class="flex-center grid-width-1">
                    <img class="tt-bind remove-row" src="'.theme_image('cross.png').'" title="'._('Remove this row').'">
                </div>';
            }

            if (empty($serviceargs)) {
                $output .= '
                <div class="flex-center grid-width-3 remove-on-addition">
                    <div class="input-group">
                        <label class="input-group-addon input-grouped smaller" style="height: 30px; border-right: 1px solid #ccc">'. _('No selected entries yet') .'</label>
                    </div>
                </div>';
            }
            $output .= '
            </div>';

            /* The quote and semicolon ends HTML for CONFIGWIZARD_MODE_GETSTAGE1HTML */ 
            /* $output will be passed back to XI framework and rendered to browser */
            /* The next line ends CONFIGWIZARD_MODE_GETSTAGE1HTML */
            break;

            
        /* Form validation for CONFIGWIZARD_MODE_GETSTAGE1HTML */
        case CONFIGWIZARD_MODE_VALIDATESTAGE1DATA:      
            /* This defines $back variable, used for determining if the Back button */
            /* was clicked when the user was on CONFIGWIZARD_MODE_GETSTAGE2HTML */

            $back = array_key_exists("backButton", $_POST);         

            /* If the user came back from CONFIGWIZARD_MODE_GETSTAGE3HTML then we don't need to revalidate and check for errors */
            if ($back) {
                break;
            }

            $result = 0;

            $serviceargs_session = grab_array_var($_SESSION[$wizard_name], 'serviceargs', array());
            $serviceargs = grab_array_var($inargs, 'serviceargs', $serviceargs_session);

            unset($serviceargs['counter_here']);

            $errmsg[$errors++] = print_r($serviceargs, true);

            $errors = 0;
            /* Create the error messages array */
            $errmsg = array();
            
            // Everything here is picked by dropdown, so we're only doing cursory validation.
            if (count($serviceargs) <= 0) {
                $errmsg[$errors++] = _('Please select at least one perfdata entry to monitor.');
            }
            foreach ($serviceargs as $track) {
                if (count($track) < 5 || count($track) > 7) {
                    $errmsg[$errors++] = _('All perfdata tracks must have entries for the host, service, perfdata name, warning threshold, and critical threshold.');
                }
            }

            if ($errors>0){
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }
            else {
                $_SESSION[$wizard_name]['serviceargs'] = $serviceargs;
            }

            /* The next line ends CONFIGWIZARD_MODE_VALIDATESTAGE1DATA */
            break;



        case CONFIGWIZARD_MODE_GETSTAGE2HTML:
            /* This defines $back variable, used for determining if the Back button */
            /* was clicked when the user was on CONFIGWIZARD_MODE_GETSTAGE3HTML */
            $back = encode_form_val(grab_array_var($_POST,'backButton',false),ENT_QUOTES);
            
            /* Continuing from CONFIGWIZARD_MODE_GETSTAGE1HTML */
            /* OR */
            /* Continuing from CONFIGWIZARD_MODE_VALIDATESTAGE2DATA due to user error */

            // New variable. If inargs is has checkargs, use those, otherwise initialize to empty.
            $checkargs = grab_array_var($inargs, 'checkargs', array());
            $checkargs = grab_array_var($_SESSION[$wizard_name], 'checkargs', $checkargs);

            $custom_host_name = grab_array_var($inargs, 'custom_host_name', '');
            $custom_host_name = grab_array_var($_SESSION[$wizard_name], 'custom_host_name', $custom_host_name);

            $enable_custom_host = grab_array_var($inargs, 'enable_custom_host', '');
            $enable_custom_host = grab_array_var($_SESSION[$wizard_name], 'enable_custom_host', $enable_custom_host);
            // Old variable. This should always populate from $_SESSION.
            $serviceargs = grab_array_var($_SESSION[$wizard_name], 'serviceargs', array());

            if (empty($checkargs)) {
                foreach ($serviceargs as $index => $args) {
                    $checkargs[$index] = array(
                        'timeframe_warning' => (isset($args['warn-days']) ? $args['warn-days'] : '7'),
                        'timeframe_critical' => (isset($args['crit-days']) ? $args['crit-days'] : '7'),
                        'lookahead' => cp_wizard_get_lookahead($args),
                        'method' => 'Holt-Winters',
                        'threshold_warning' => (isset($args['warn-days']) ? 'warning' : 'custom'),
                        'threshold_critical' => (isset($args['warn-days']) ? 'critical' : 'custom'),
                        'minmax_warning' => 'max',
                        'minmax_critical' => 'max',
                        'custom_value' => '*'
                    );
                }
            }

            /* Now we are creating the HTML for CONFIGWIZARD_MODE_GETSTAGE2HTML */ 
            $output .= '
            <div class="hide" id="bad-custom-value-msg">'._("The custom value could not be parsed as a number.").' </div>
            <div class="hide" id="bad-timeframe-lookahead-msg">'._("The alerting time is greater than the lookahead period.").' </div>
            <script src="../includes/components/capacityplanning/includes/capacityreport.js.php"></script>
            <script src="../includes/configwizards/capacity-planning/capacity-planning-page-2.inc.js"></script>
            <link rel="stylesheet" href="../includes/configwizards/capacity-planning/capacity-planning.inc.css">
            <h5 class="ul">' . _('Advanced Configuration') . '</h5>

            <div id="advanced-wrapper">
                <div id="custom-host-grid">
                    <div class="flex-center">
                        <div class="input-group flex-v-align">
                            <input type="checkbox" id="enable_custom_host" name="enable_custom_host" style="margin-left: 7px; margin-right:5px" '.is_checked($enable_custom_host).'/>
                            <label for="enable_custom_host" class="select-cf-option">'. _('Put all new services on specific host') .':</label>
                        </div>
                    </div>
                    <div class="">
                        <div class="flex-v-align">
                            <input type="text" class="textfield form-control" id="custom_host_name" size="28" name="custom_host_name" style="margin-left: 24px" placeholder="'._("Host Name").'" value="'.$custom_host_name.'" ' . (is_checked($enable_custom_host) ? '': 'disabled') . '>
                        </div>
                    </div>
                </div>
            </div>

            <!-- table header -->
            <div class="grid">
                <h6>'._('Host Name').'</h6>
                <h6>'._('Service Description').'</h6>
                <h6>'._('Perfdata Name').'</h6>
                <div class="flex-center">
                    <i class="fa fa-chevron-down" id="toggle-all-chevron"></i>
                </div>
            </div>';

            // $checkargs is indexed to match $serviceargs.
            foreach ($serviceargs as $index => $service) {
                $click_me = '';
                if ($service['need_custom'] === "1") {
                    $click_me = 'click-me';
                }
                $output .= '
                <div class="grid ' . $click_me . '" data-service-id="'.$index.'">
                    <p>' . $service['host'] .'</p>
                    <p>' . $service['service'] .'</p>
                    <p>' . $service['perfdata'] .'</p>
                    <div class="flex-center">
                        <i class="fa fa-chevron-down config-chevron" id="" data-service-id="'.$index.'"></i>
                    </div>
                </div>
                <div class="metrics-grid" data-service-id="'.$index.'">
                    <div class="metrics-grid-table">
                        <div class="flex-center grid-width-6">
                        </div>
                        <div class="flex-center grid-width-3">
                            <div class="input-group double-input">
                                <label class="input-group-addon tt-bind input-grouped" title="'._('The original warning and critical values for performance data on this check.').'">
                                    ' . _('Performance Data Thresholds') . '
                                </label>
                                <label class="input-group-addon input-grouped">
                                    <img src="'.theme_image('warning_small.png').'" class="tt-bind" title="'._('Warning Threshold').'">
                                </label>
                                <input type="text" disabled class="textfield form-control input-grouped" value="'.$service['warn'].'">
                                <label class="input-group-addon input-grouped">
                                    <img src="'.theme_image('critical_small.png').'" class="tt-bind" title="'._('Critical Threshold').'">
                                </label>
                                <input type="text" disabled class="textfield form-control input-grouped" value="'.$service['crit'].'">
                            </div>
                        </div>
                        <div class="flex-center grid-width-3">
                            <div class="input-group double-input">
                                <label class="input-group-addon tt-bind input-grouped smaller" title="'._('The warning threshold for the capacity planning check.').'">
                                    ' . sprintf(_('Alert %s within'), '<img src="'.theme_image('warning_small.png').'" class="tt-bind input-grouped">') . '
                                </label>
                                <input type="text" size="2" class="textfield form-control input-grouped" name="checkargs['.$index.'][timeframe_warning]" value="'.$checkargs[$index]['timeframe_warning'].'">
                                <label class="input-group-addon input-grouped">
                                    ' . _('days of exceeding') . '
                                </label>

                                <select name="checkargs['.$index.'][threshold_warning]" class="form-control input-grouped">';

                                if ($service['crit'] !== _('N/A')) {
                                    $output .= '<option value="critical" '. is_selected($checkargs[$index]['threshold_warning'], 'critical') .'>'._('CRITICAL Threshold').'</option>';
                                }

                                if ($service['warn'] !== _('N/A')) {
                                    $output .= '<option value="warning"  '. is_selected($checkargs[$index]['threshold_warning'], 'warning') .'>'._('WARNING Threshold').'</option>';
                                }

                                $output .= '
                                    <option value="custom" ' . is_selected($checkargs[$index]['threshold_warning'], 'custom') .'>'._('Custom Value').'</option>
                                </select>
                                <label class="input-group-addon input-grouped">
                                    '._('as').'
                                </label>
                                <select name="checkargs['.$index.'][minmax_warning]" class="form-control input-grouped">
                                    <option value="max" '. is_selected($checkargs[$index]['minmax_warning'], 'max') .'>'._('Maximum').'</option>
                                    <option value="min" '. is_selected($checkargs[$index]['minmax_warning'], 'min') .'>'._('Minimum').'</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex-center grid-width-3">
                            <div class="input-group double-input">
                                <label class="input-group-addon tt-bind input-grouped" title="">
                                    '._("Lookahead").'
                                </label>
                                <input name="checkargs['.$index.'][lookahead]" type="text" class="textfield form-control input-grouped" value="'.$checkargs[$index]['lookahead'].'" size="4">
                                <label class="input-group-addon input-grouped">
                                    '._('weeks using').'
                                </label>
                                <select name="checkargs['.$index.'][method]" class="form-control input-grouped">
                                    <option value="Holt-Winters"  '. is_selected($checkargs[$index]['method'], 'Holt-Winters') . '>'. _("Holt-Winters"). '</option>
                                    <option value="Linear Fit"    '. is_selected($checkargs[$index]['method'], 'Linear Fit') . '>'. _("Linear Fit"). '</option>
                                    <option value="Quadratic Fit" '. is_selected($checkargs[$index]['method'], 'Quadratic Fit') . '>'. _("Quadratic Fit"). '</option>
                                    <option value="Cubic Fit"     '. is_selected($checkargs[$index]['method'], 'Cubic Fit') . '>'. _("Cubic Fit"). '</option>
                                </select>
                                <label class="input-group-addon tt-bind input-grouped" title="">
                                    '._("algorithm").'
                                </label>
                            </div>
                        </div>
                        <div class="flex-center grid-width-3">
                            <div class="input-group double-input">
                                <label class="input-group-addon tt-bind input-grouped smaller" title="'._('The critical threshold for the capacity planning check.').'">
                                    ' . sprintf(_('Alert %s within'), '<img src="'.theme_image('critical_small.png').'" class="tt-bind input-grouped">') . '
                                </label>
                                <input type="text" size="2" class="textfield form-control input-grouped" name="checkargs['.$index.'][timeframe_critical]" value="'.$checkargs[$index]['timeframe_critical'].'">
                                <label class="input-group-addon input-grouped">
                                    ' . _('days of exceeding') . '
                                </label>

                                <select name="checkargs['.$index.'][threshold_critical]" class="form-control input-grouped">';

                                if ($service['crit'] !== _('N/A')) {
                                    $output .= '<option value="critical" '. is_selected($checkargs[$index]['threshold_critical'], 'critical') .'>'._('CRITICAL Threshold').'</option>';
                                }

                                if ($service['warn'] !== _('N/A')) {
                                    $output .= '<option value="warning"  '. is_selected($checkargs[$index]['threshold_critical'], 'warning') .'>'._('WARNING Threshold').'</option>';
                                }

                                $output .= '
                                    <option value="custom"   '. is_selected($checkargs[$index]['threshold_critical'], 'custom') .'>'._('Custom Value').'</option>
                                </select>
                                <label class="input-group-addon input-grouped">
                                    '._('as').'
                                </label>
                                <select name="checkargs['.$index.'][minmax_critical]" class="form-control input-grouped">
                                    <option value="max" '. is_selected($checkargs[$index]['minmax_critical'], 'max') .'>'._('Maximum').'</option>
                                    <option value="min" '. is_selected($checkargs[$index]['minmax_critical'], 'min') .'>'._('Minimum').'</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex-center grid-width-2">
                            <div class="input-group double-input">
                                <label class="input-group-addon tt-bind input-grouped" title="">
                                    '._("Custom Value").'
                                </label>
                                <input type="text" name="checkargs['.$index.'][custom_value]" class="textfield form-control input-grouped" value="'.$checkargs[$index]['custom_value'].'" size="4">
                            </div>
                        </div>
                        <div class="flex-center grid-width-1">
                            <button type="button" class="btn btn-sm btn-default render-graph" data-service-id="'.$index.'" data-host="' . $service['host'] . '" data-service-description="' . $service['service'] . '" data-perfdata-name="' . $service['perfdata'] . '" >'._("Render Graph").'</button>
                        </div>
                        <div class="flex-center grid-width-3">
                            <div>
                                <span class="validation-error-text"></span>
                            </div>
                        </div>
                        <div class="flex-center grid-width-6">
                            <div id="highcharts-target-' . $index . '">
                            </div>
                        </div>
                    </div>
                </div>';
            }

            /* The quote and semicolon ends HTML for CONFIGWIZARD_MODE_GETSTAGE2HTML */ 
            /* $output will be passed back to XI framework and rendered to browser */
            /* The next line ends CONFIGWIZARD_MODE_GETSTAGE2HTML */
            break;

        /* Form validation for CONFIGWIZARD_MODE_GETSTAGE2HTML */ 
        case CONFIGWIZARD_MODE_VALIDATESTAGE2DATA:
            /* This defines $back variable, used for determining if the Back button */
            /* was clicked when the user was on CONFIGWIZARD_MODE_GETSTAGE4HTML */
            $back = array_key_exists("backButton", $_POST);         
            /* If the user came back from CONFIGWIZARD_MODE_GETSTAGE3HTML then we don't need to revalidate and check for errors */
            if ($back) break;

            $checkargs = grab_array_var($_SESSION[$wizard_name], 'checkargs', array());
            $checkargs = grab_array_var($inargs, 'checkargs', $checkargs);

            // Needed to verify warning/critical thresholds
            $serviceargs = grab_array_var($_SESSION[$wizard_name], 'serviceargs', array());

            $enable_custom_host = grab_array_var($_SESSION[$wizard_name], 'enable_custom_host', false);
            $enable_custom_host = grab_array_var($inargs, 'enable_custom_host', $enable_custom_host);

            $custom_host_name = grab_array_var($_SESSION[$wizard_name], 'custom_host_name', '');
            $custom_host_name = grab_array_var($inargs, 'custom_host_name', $custom_host_name);

            if ($enable_custom_host && trim(strval($custom_host_name)) === '') {
                $errmsg[$errors++] = _("Please specify a custom host name.");
            }

            if (empty($checkargs)) {
                $errmsg[$errors++] = _("checkargs is empty. This error should never be reached. Please contact the config wizard maintainer.");
            }
            else {
                foreach ($checkargs as $index => $check) {

                    // If <select>s don't match the options, pick the first one for each. 

                    $valid_methods = array('Holt-Winters', 'Linear Fit', 'Quadratic Fit', 'Cubic Fit');
                    if (!in_array($check['methods'], $valid_methods)) {
                        $check['methods'] = $valid_methods[0];
                    }
                    $valid_threshold = array('custom', 'warning', 'critical');
                    if (!in_array($check['threshold_warning'], $valid_threshold)) {
                        $check['threshold_warning'] = $valid_threshold[0];
                    }
                    if (($check['threshold_warning'] === 'custom') && !is_numeric($check['custom_value'])) {
                        $errmsg[$errors++] = sprintf(_('On check number %1$s, warning threshold uses a custom value but %2$s is not numeric.'), $index + 1, $check['custom_value']);
                    }
                    if (!in_array($check['threshold_critical'], $valid_threshold)) {
                        $check['threshold_critical'] = $valid_threshold[0];
                    }
                    if (($check['threshold_critical'] === 'custom') && !is_numeric($check['custom_value'])) {
                        $errmsg[$errors++] = sprintf(_('On check number %1$s, critical threshold uses a custom value but %2$s is not numeric.'), $index + 1, $check['custom_value']);
                    }

                    $valid_minmax = array('max', 'min');
                    if (!in_array($check['minmax_warning'], $valid_minmax)) {
                        $check['minmax_warning'] = $valid_minmax[0];
                    }
                    if (!in_array($check['minmax_critical'], $valid_minmax)) {
                        $check['minmax_critical'] = $valid_minmax[0];
                    }

                    // Ensure both timeframes are within the lookahead period.

                    if (!isset($check['lookahead'])) {
                        $errmsg[$errors++] = sprintf(_("Lookahead not defined for check number %s"), $index + 1);
                    }

                    if (isset($check['timeframe_warning'])) {
                        if (floatval($check['lookahead']) * 7 < floatval($check['timeframe_warning'])) {
                            $errmsg[$errors++] = sprintf(_("warning timeframe must be within lookahead period on check number %s"), $index + 1);
                            continue;
                        }
                    }

                    if (isset($check['timeframe_critical'])) {
                        if (floatval($check['lookahead']) * 7 < floatval($check['timeframe_critical'])) {
                            $errmsg[$errors++] = sprintf(_("critical timeframe must be within lookahead period on check number %s"), $index + 1);
                            continue;
                        }
                    }

                }
            }

            if ($errors>0){
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
                if (!array_key_exists('checkargs', $_SESSION[$wizard_name])) {
                    // If we've never assigned checkargs to SESSION, it's fine to overwrite even if they're not valid.
                    $_SESSION[$wizard_name]['checkargs'] = $checkargs;
                    $_SESSION[$wizard_name]['custom_host_name'] = $custom_host_name;
                    $_SESSION[$wizard_name]['enable_custom_host'] = $enable_custom_host;
                }
            }
            else {
                $_SESSION[$wizard_name]['checkargs'] = $checkargs;
                $_SESSION[$wizard_name]['custom_host_name'] = $custom_host_name;
                $_SESSION[$wizard_name]['enable_custom_host'] = $enable_custom_host;
            }

            /* The next line ends CONFIGWIZARD_MODE_VALIDATESTAGE2DATA */
            break;

        case CONFIGWIZARD_MODE_GETSTAGE3OPTS:
            $output .= '<div style="margin-bottom: 20px;">' . _('The selected data will be checked for capacity planning once each day. Click next to continue.') . '</div>';
            $result = CONFIGWIZARD_HIDE_OPTIONS;
            break;

        case CONFIGWIZARD_MODE_GETSTAGE3HTML:
            $back = array_key_exists('backButton', $_POST);


            $output = '
            ';
            /* The next line ends CONFIGWIZARD_MODE_GETSTAGE3HTML */
            break;
            
            
        case CONFIGWIZARD_MODE_VALIDATESTAGE3DATA:

            $back = array_key_exists("backButton", $_POST);
            if($back) {
                break;
            }

            if($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;
    
        case CONFIGWIZARD_MODE_GETFINALSTAGEHTML:
            $output = '';
            break;
        
        
        case CONFIGWIZARD_MODE_GETOBJECTS:

            $serviceargs = $_SESSION[$wizard_name]['serviceargs'];
            $checkargs = $_SESSION[$wizard_name]['checkargs'];

            $custom_host_enabled = $_SESSION[$wizard_name]['enable_custom_host'];
            $custom_host_name = $_SESSION[$wizard_name]['custom_host_name'];

            if ($custom_host_enabled) {
                // Possibly create a new host with a custom name
                // If host already exists, don't change it!
                if (!host_exists($custom_host_name)) {
                    $objs[] = array(
                        "type" => OBJECTTYPE_HOST,
                        "use" => "generic-host",
                        "host_name" => $custom_host_name,
                        "address" => $custom_host_name,
                        "icon_image" => 'capacity_planning.png',
                        'check_command'         => 'check_dummy!0!"No check required - this host is just for convenience"',
                        "check_interval"        => 1440,
                        "notification_interval" => 1440,
                        "max_check_attempts"    => 1,
                        "_xiwizard" => $wizard_name
                    );
                }
            }

            foreach ($checkargs as $index => $check) {

                $host = $serviceargs[$index]['host'];
                $service = $serviceargs[$index]['service'];
                $data = $serviceargs[$index]['perfdata'];

                $check_host_name = $serviceargs[$index]['host'];
                if ($custom_host_enabled) {
                    $check_host_name = $custom_host_name;
                }

                $service_description = "Capacity Planning - $service / $data";
                if ($custom_host_enabled) {
                    $service_description = "Capacity Planning - $host / $service / $data";
                }

                $host = ' -H ' . escapeshellarg(pnp_convert_object_name($host));
                $service = ' -S ' . escapeshellarg(pnp_convert_object_name($service));
                $data = ' -d ' . escapeshellarg(pnp_convert_object_name($data));
                $method = ' -m ' . escapeshellarg($check['method']);
                $lookahead = ' -l ' . escapeshellarg($check['lookahead'] . 'w');

                $warning = '';
                if (isset($check['timeframe_warning'])) {
                    $warning = ' -w ' . $check['timeframe_warning'] . 'd';
                }

                $critical = '';
                if (isset($check['timeframe_critical'])) {
                    $critical = ' -c ' . $check['timeframe_critical'] . 'd';
                }

                $threshold = '';
                $custom_value = '';
                if ($check['minmax_warning'] === 'min') {
                    $threshold .= '--warning-is-minimal ';
                }
                if ($check['minmax_critical'] == 'min') {
                    $threshold .= '--critical-is-minimal ';
                }

                if ($check['threshold_warning'] === 'critical') {
                    $threshold .= '--warning-uses-critical ';
                }
                elseif ($check['threshold_warning'] === 'custom') {
                    $threshold .= '--warning-uses-custom ';
                    $custom_value = '--custom-value ' . $check['custom_value'] . ' ';
                }

                if ($check['threshold_critical'] === 'warning') {
                    $threshold .= '--critical-uses-warning ';
                }
                elseif ($check['threshold_critical'] === 'custom') {
                    $threshold .= '--critical-uses-custom ';
                    $custom_value = '--custom-value ' . $check['custom_value'] . ' ';
                }

                
                $full_check_command = "check_capacity_planning!$host $service $data $method $lookahead!$warning $critical $threshold $custom_value";

                $objs[] = array(
                    'type'                  => OBJECTTYPE_SERVICE,
                    'host_name'             => $check_host_name,
                    'service_description'   => $service_description,
                    'icon_image'            => 'capacity_planning.png',
                    'check_command'         => $full_check_command,
                    "check_interval"        => 1440,
                    "notification_interval" => 1440,
                    "max_check_attempts"    => 1,
                    '_xiwizard'             => $wizard_name,
                );
            }
            
            /* Return the object definitions to the wizard */
            $outargs[CONFIGWIZARD_NAGIOS_OBJECTS] = $objs;
            
            /* clear the session variables for this wizard run */
            unset ($_SESSION[$wizard_name]); 
            
            /* The next line ends CONFIGWIZARD_MODE_GETOBJECTS */
            break;
            
        default:
            break;          
    };

    $output .= make_enterprise_only_feature(null, true);
    return $output;
}

function cp_wizard_get_lookahead($args) {
    $warn_weeks = 0;
    if (array_key_exists('warn-days', $args)) {
        $warn_weeks = ceil(intval($args['warn-days']) / 7);
    }

    $crit_weeks = 0;
    if (array_key_exists('crit-days', $args)) {
        $crit_weeks = ceil(intval($args['crit-days']) / 7);
    }

    return max(1, $warn_weeks, $crit_weeks);
}
?>