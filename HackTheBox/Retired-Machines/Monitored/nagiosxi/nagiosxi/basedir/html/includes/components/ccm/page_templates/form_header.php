<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: form_header.php
//  Desc: Creates the top of the Form class, which shows whenever ANY object is modified
//        either by adding a new one or editing. It also creates Javascript variables that will
//        pre-select whatever overlays need to have selected items.
//
?>
<script type="text/javascript" src="javascript/form_js.js?<?php echo VERSION; ?>"></script>
<script type="text/javascript">
var command_list = new Array();
<?php
if (in_array($this->exactType, $this->mainTypes)) {
    foreach ($FIELDS['selCommandOpts'] as $c) {
        echo "command_list['".$c['id']."'] = '".addslashes(htmlentities($c['command_line'], ENT_NOQUOTES))."';";
    }
}
?>

// Default is to load "Common Settings" and pre-selected items
$(document).ready(function() {
    <?php
    // Display popup tooltips
    echo '
        $(document).ready(function () {
            $(".tooltip-info").tooltip({ template: "<div class=\"tooltip ccm-tooltip\" role=\"tooltip\"><div class=\"tooltip-arrow\"></div><div class=\"tooltip-inner\"></div></div>" });
        });';

    // Pre-load free variables
    if (!empty($FIELDS['freeVariables'])) {
        foreach ($FIELDS['freeVariables'] as $f) {
            if (!empty($f['name']) && (isset($f['value']) && $f['value'] !== '')) {
                echo "insertDefinition('".$f['name']."', '".addslashes($f['value'])."');";
            }
        }
    } 

    // Pre-load time periods
    if ($this->exactType == 'timeperiod') {
        for ($i = 0; $i < count($FIELDS['timedefinitions']); $i++) {
            echo "insertTimeperiod('".$FIELDS['timedefinitions'][$i]."', '".$FIELDS['timeranges'][$i]."');";
        }
    }
    ?>
}); 
</script>

<div id="mainWrapper">

    <?php
    // Show help items if they exist for the object
    if (!empty($FIELDS['info'])) {
    ?>
    <div id='helpOptions'>
        <select id='helpList' class='form-control' onchange='getHelpOverlay("<?php echo $FIELDS["infotype"]; ?>")'>
            <option value=''></option>
            <?php
            $menu_opts = get_documentation_fieldlist($FIELDS["infotype"]);
            foreach($menu_opts as $info) {
                echo "<option value='".$info."'>".$info."</option>"; 
            }
            ?>
        </select>
    </div>
    <?php
    }
    ?>
  
    <h1 class='title'><?php echo ccm_get_full_title($this->exactType); ?> <?php echo _("Management"); ?></h1>

    <?php
    // Check to see if the object is active
    if (isset($FIELDS['active']) && $FIELDS['active'] == 0 && $FIELDS['mode'] != "insert") {
    ?>
    <div class="alert alert-error" style="line-height: 20px; margin-top: 10px;">
        <i class="fa fa-exclamation-triangle" style="font-size: 14px; vertical-align: text-top; margin-right: 4px;"></i> <?php echo _('This object is currently set as'); ?> <strong><?php echo _('Inactive'); ?></strong> <?php echo _('and will not be written to the configuration files.'); ?>
    </div>
    <?php
    }
    ?>

    <div id="formContainer"> 
        <form id="mainCcmForm" method="post" action="index.php?type=<?php echo encode_form_val($this->exactType); ?>&page=<?php echo encode_form_val($FIELDS['page']); ?>">
        <div id="tabs" class="main-form hide">
        <?php
        // If it's a main type, show all 4 of the tabs
        if (in_array($this->exactType, $this->mainTypes)) {
        ?>
            <ul>
                <li><a href="#tab1"><i class="fa fa-cog"></i> <?php echo _("Common Settings"); ?></a></li>
                <li><a href="#tab2"><i class="fa fa-check"></i>  <?php echo _("Check Settings"); ?></a></li>
                <li><a href="#tab3"><i class="fa fa-bell"></i> <?php echo _("Alert Setting"); ?>s</a></li>
                <li><a href="#tab4"><?php echo _("Misc Settings"); ?></a></li>
            </ul>
        <?php
        } else if ($this->exactType == 'contact' || $this->exactType == 'contacttemplate') {
        ?>
            <ul>
                <li><a href="#tab1"><i class="fa fa-cog"></i> <?php echo _("Common Settings"); ?></a></li>
                <li><a href="#tab2"><i class="fa fa-bell"></i> <?php echo _("Alert Settings"); ?></a></li>
                <li><a href="#tab4"><?php echo _("Misc Settings"); ?></a></li>
            </ul>
        <?php 
        }
        ?>
        