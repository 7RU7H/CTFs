<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: common_settings.php
//  Desc: Creates the HTML for the "Common Settings" tab in object management pages. Used in the
//        form class to output the area where everything is defined.
//

// User Macros include
$usermacro_disable = get_option("usermacro_disable", 0);

if (!$usermacro_disable) {
    create_usermacros_html_javascript("ccm");
}
?>
    <div id="tab1">
        <div class="leftBox">
            <?php
            // Host or Host Template only items
            if ($this->exactType == 'host' || $this->exactType =='hosttemplate') { 
                $tfName = (($this->exactType == 'host') ? _('Host Name') : _('Template Name')); 
                $name_field = (($this->exactType == 'host') ? 'host_name' : 'template_name');
            ?>
            <div class="ccm-row">
                <label for="tfName"><?php echo $tfName; ?> <span class="req">*</span></label>
                <input name="tfName" class="required form-control fc-fl" type="text" id="tfName" value="<?php val(encode_form_val($FIELDS[$name_field])); ?>">
            </div>
            <div class="ccm-row">
                <label for="tfFriendly"><?php echo _("Alias"); ?></label>
                <input name="tfFriendly" class="form-control fc-fl" type="text" id="tfFriendly" value="<?php val(encode_form_val($FIELDS['alias'])); ?>">
            </div>
            <?php
            // Host only items 
            if ($this->exactType == 'host') {
            ?>
            <div class="ccm-row">
                <label for="tfAddress"><?php echo _("Address"); ?> <span class="req">*</span></label>
                <input name="tfAddress" type="text" class="form-control fc-fl required" id="tfAddress" value="<?php val(encode_form_val($FIELDS['address'])); ?>">
            </div>
            <div class="ccm-row spacer">
                <label for="tfDisplayName">Display name</label>
                <input name="tfDisplayName" type="text" class="form-control fc-fl" id="tfDisplayName" value="<?php val(encode_form_val($FIELDS['display_name'])); ?>">
            </div>
            <?php
            }

            $parents = count($FIELDS['pre_parents']);
            ?>
            <div class="ccm-row">
                <button type="button" class="btn btn-sm btn-info btn-parentBox" onclick="overlay('parentBox')"><i class="fa fa-sitemap"></i> <?php echo _("Manage Parents"); ?> <span class="badge"><?php echo $parents; ?></span></button>
            </div>
            <?php
            }

            $tpls = count($FIELDS['pre_templates']);
            $hgs = count($FIELDS['pre_hostgroups_AB']);

            // Service or Service Template only
            if ($this->exactType == 'service' || $this->exactType == 'servicetemplate' ) {
                $tfName = (($this->exactType == 'service') ? _('Config Name') : _('Template Name'));
                $required = (($this->exactType == 'service') ? 'required' : '');
                $hs = count($FIELDS['pre_hosts_AB']);
            ?>
            <div class="ccm-row">
                <label for="tfName"><?php echo encode_form_val($tfName); ?> <span class="req">*</span></label>
                <input name="tfName" class="form-control fc-fl required" type="text" id="tfName" value="<?php if (!empty($FIELDS['config_name'])) { echo val(encode_form_val($FIELDS['config_name']), false); } if (!empty($FIELDS['template_name'])) { echo val(encode_form_val($FIELDS['template_name']), false); } ?>">
            </div>
            <div class="ccm-row">
                <label for="tfFriendly"><?php echo _('Description'); ?> <span class="req">*</span></label>
                <input name="tfServiceDescription" class="form-control fc-fl <?php echo $required; ?>" type="text" id="tfServiceDescription" value="<?php val(encode_form_val($FIELDS['service_description'])); ?>">
            </div>
            <div class="ccm-row">
                <label for="tfDisplayName"><?php echo _('Display name'); ?></label>
                <input name="tfDisplayName" type="text" class="form-control fc-fl" id="tfDisplayName" value="<?php val(encode_form_val($FIELDS['display_name'])); ?>">
            </div>
            <div class="ccm-row">
                <button type="button" class="btn btn-sm btn-info btn-hostBox" onclick="overlay('hostBox')"><?php echo _("Manage Hosts"); ?> <span class="badge"><?php echo $hs; ?></span></button>
            </div>
            <?php
            }
            ?>
            <div class="ccm-row">
                <button type="button" class="btn btn-sm btn-info btn-templateBox" onclick="overlay('templateBox')"><i class="fa fa-file-o"></i> <?php echo _("Manage Templates"); ?> <span class="badge"><?php echo $tpls; ?></span></button>
            </div>
            <div class="ccm-row">
                <button type="button" class="btn btn-sm btn-info btn-hostgroupBox" onclick="overlay('hostgroupBox')"><i class="fa fa-folder-open-o"></i> <?php echo _("Manage Host Groups"); ?> <span class="badge"><?php echo $hgs; ?></span></button>
            </div>
            <?php
            // Serivce or Service Template only
            if ($this->exactType == 'service' || $this->exactType == 'servicetemplate') {
                $sgs = count($FIELDS['pre_servicegroups_AB']);
            ?>
            <div class="ccm-row">
                <button type="button" class='btn btn-sm btn-info btn-servicegroupBox' onclick="overlay('servicegroupBox')"><?php echo _("Manage Service Groups"); ?> <span class="badge"><?php echo $sgs; ?></button>
            </div>
            <?php
            }

            // Check if the active button should be checked
            $active_checked = '';
            if ((isset($FIELDS['active']) && $FIELDS['active'] == '1') || !isset($FIELDS['active'])) {
                $active_checked = 'checked="checked"';
            }
            ?>
            <div class="ccm-row spacer"></div>
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
                <label for="selHostCommand"><?php echo _('Check command'); ?></label>
                <select name="selHostCommand" id="selHostCommand" class="form-control fc-fl" onchange="reveal_command(this.value);">
                    <?php
                    // Host commands
                    $selected = "";
                    if ($FIELDS['sel_check_command'] == '0') { $selected = ' selected="selected"'; }
                    echo '<option value="0"'.$selected.'>&nbsp;</option>';
                    
                    foreach ($FIELDS['selCommandOpts'] as $opt) {
                        $selected = "";
                        if ($opt['active'] != 1) {
                            continue;
                        }
                        if ($FIELDS['sel_check_command'] == $opt['id']) { $selected = ' selected="selected"'; }
                        echo '<option value="'.encode_form_val($opt['id']).'"'.$selected.'>'.encode_form_val($opt['command_name']).'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="ccm-row" style="width: 520px;">
                <label for="fullcommand"><?php echo _('Command view'); ?></label>
                <pre><div id="fullcommand"><?php if (!empty($FIELDS['fullcommand'])) { val(encode_form_val($FIELDS['fullcommand'])); } else { echo _("No command selected"); } ?></div></pre>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG1$</span>
                    <input name="tfArg1" class="form-control arg" type="text" id="tfArg1" value="<?php val(encode_form_val($FIELDS['tfArg1'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG2$</span>
                    <input name="tfArg2" class="form-control arg" type="text" id="tfArg2" value="<?php val(encode_form_val($FIELDS['tfArg2'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG3$</span>
                    <input name="tfArg3" class="form-control arg" type="text" id="tfArg3" value="<?php val(encode_form_val($FIELDS['tfArg3'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG4$</span>
                    <input name="tfArg4" class="form-control arg" type="text" id="tfArg4" value="<?php val(encode_form_val($FIELDS['tfArg4'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG5$</span>
                    <input name="tfArg5" class="form-control arg" type="text" id="tfArg5" value="<?php val(encode_form_val($FIELDS['tfArg5'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG6$</span>
                    <input name="tfArg6" class="form-control arg" type="text" id="tfArg6" value="<?php val(encode_form_val($FIELDS['tfArg6'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG7$</span>
                    <input name="tfArg7" class="form-control arg" type="text" id="tfArg7" value="<?php val(encode_form_val($FIELDS['tfArg7'])) ?>">
                </div>
            </div>
            <div class="ccm-row">
                <div class="input-group">
                    <span class="input-group-addon argNum">$ARG8$</span>
                    <input name="tfArg8" class="form-control arg" type="text" id="tfArg8" value="<?php val(encode_form_val($FIELDS['tfArg8'])) ?>">
                </div>
            </div>

            <!-- Add and Delete Additional Arguments -->
            <?php
            for($i = 9; $i <= 32; $i++) {
                if(isset($FIELDS['tfArg' . $i])) {
                    $formValue = encode_form_val($FIELDS['tfArg' . $i]);
                    echo "<div class='ccm-row argRow'>
                    <div class='input-group'>
                    <span class='input-group-addon argNum'>\$ARG$i\$</span>
                    <input name='tfArg$i' class='form-control arg' type='text' id='tfArg$i' value='$formValue'>
                    </div>
                   </div>";
                }
            }
            ?>
            <button type="button" id="addArgButton" class='btn btn-sm btn-info' onclick="addArgument()"><?php echo _("Add Arguments"); ?><span class="badge">+</span> </button>
            <button type="button" id="deleteArgButton" class='btn btn-sm btn-info' onclick="deleteArgument()"><?php echo _("Delete Arguments"); ?><span class="badge">-</span> </button>
            <script>
                function addArgument() {
                    var maxArgs = 32; // maximum number of arguments allowed
                    var currentArgs = document.querySelectorAll('.arg').length;
                    if (currentArgs >= maxArgs) {
                        return alert('<?php echo _("Exceeded max limit of 32 arguments") ?>');
                    }

                    var newArgNum = currentArgs + 1;
                    var newArgName = `$ARG${newArgNum}$`;
                    var newInputGroup = document.createElement('div');
                    newInputGroup.className = 'input-group';
                    newInputGroup.innerHTML = `<span class="input-group-addon argNum" id="arg-box">${newArgName}</span>
                  <input class="form-control arg" type="text" id="tfArg${newArgNum}" name="tfArg${newArgNum}" value="<?php val(encode_form_val($FIELDS['tfArg" + newArgNum + "'])) ?>">`;
                    var newDiv = document.createElement('div');
                    newDiv.className = 'ccm-row argRow';
                    newDiv.appendChild(newInputGroup);
                    var addButton = document.getElementById('addArgButton');
                    addButton.parentNode.insertBefore(newDiv, addButton);
                }

                function deleteArgument() {
                    var argRows = document.querySelectorAll('.argRow');
                    if (argRows.length === 0) {
                        return alert('<?php echo _("Deletion cannot be performed. Cannot show less than 8 arguments.") ?>');
                    }

                    var lastArgRow = argRows[argRows.length - 1];
                    lastArgRow.parentNode.removeChild(lastArgRow);
                }
            </script>
            <div id="command_test_box">
                <a class="btn btn-xs btn-info" href="javascript:void(0);" id="command_test"><i class="fa fa-play"></i> <?php echo _("Run Check Command"); ?></a>
            </div>
        </div>
        <div class="clear"></div>
    </div>