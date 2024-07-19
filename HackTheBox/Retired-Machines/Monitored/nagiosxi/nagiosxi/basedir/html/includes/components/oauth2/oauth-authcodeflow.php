<?php

/** 
 * File: oauth-authcodeflow.php
 *
 * Description: This file is used to instantiate OAuth2 credentials for Nagios XI
 * It presents a form and handles routing to/from the OAuth2 provider, eventually 
 * saving the credentials to a file in /usr/local/nagiosxi/etc/components/oauth2/providers/
 *
 * Functions:
 *  - getProvider($providerName, $params) - returns an instance of the OAuth2 provider                          ### function from createprovider.php
 *  - echo_alert_close($message, $success = 'false') - echos an alert with the given message and closes the page if browser allows it. Otherwise display_exception is called
 *  - display_exception($e) - echoes the exception message to the page somewhat nicely
 *
 * Blame target:    BB
 * Date:            2023-04
 * Version:         1.0.0
 * 
 * Copyright (c) 2023 Nagios Enterprises, LLC. All rights reserved.
 */

require_once(__DIR__.'/../../common.inc.php');
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs and authentication
// make sure grab_request_vars() is sanitizing input
if(!isset($escape_request_vars) || $escape_request_vars == false){
    $escape_request_vars = true;
}
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You do not have access to this section.");
    echo _("If you need to instantiate an OAuth2 connection, please contact your system administrator.");
    exit;
}

// check PHP version not less than 5.6
if (version_compare(PHP_VERSION, '5.6.0') < 0) {
    echo _('You are running ').PHP_VERSION._(', please upgrade PHP using the following link to use OAuth: ').'<a href="https://support.nagios.com/kb/article/nagios-xi-upgrading-to-php-7-860.html">https://support.nagios.com/kb/article/nagios-xi-upgrading-to-php-7-860.html</a>';
    exit;
}

//======================================================================
// IF NO AUTHORIZATION CODE -> SHOW FORM
//======================================================================

if (!isset($request['code']) && !isset($request['provider'])) {
    // unlink any temp files that may have been left behind from a previous attempt
    $tempfiles = glob('/usr/local/nagiosxi/etc/components/oauth2/*.tmp');
    foreach($tempfiles as $tempfile){
        unlink($tempfile);
    }

    do_page_start(array("page_title" => _("Save OAuth2 Credentials")), true);

    if (isset($request['error'])) {
        $error = $request['error'];
        if(isset($request['error_description'])){
            display_exception($error.'<br>'.$request['error_description']);
        }
        else{
            display_exception($error);
        }
    }
    ?>

<html>
    <style>
        form table.table tr td { width:200px; }
        form table.table tr td:nth-child(2) { width:400px; }
    </style>
<body>
<?php 
//set redirectUri to localhost if ip is private
$componentBaseUrl = get_component_url_base("oauth2");
preg_match("/(?<=\/\/).+?(?=\/)/", $componentBaseUrl, $ip);
if(preg_match("/[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}/",$ip[0],$ip)){ //ip is private, must use https or localhost
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') { //https, can use private ip (at least with Azure)
        $redirectUri = $componentBaseUrl;
        $googleRedirectUri = preg_replace("/(?<=\/\/)[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}?(?=\/)/","localhost",$componentBaseUrl); //google doesn't support private ips
        $googleRedirectUri = $googleRedirectUri."/oauth-authcodeflow.php";
    } else { //http and not FQDN, must use localhost
        $redirectUri = preg_replace("/(?<=\/\/)[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}?(?=\/)/","localhost",$componentBaseUrl);
    }
} else { //ip is not private, can use domain name
    $redirectUri = $componentBaseUrl;
}
$redirectUri = $redirectUri."/oauth-authcodeflow.php";
?>
<form method="post" action="<?php echo $redirectUri; ?>">
    <?php echo get_nagios_session_protector(); ?>
    
    <!-- Provider template setup -->
    <table class="table table-condensed table-no-border table-auto-width"> 
    <h5 id="configurationTitle" class='ul' style="font-size: 1.8em; margin-top:5px;"><?php echo _('Adding New OAuth2 Configuration'); ?></h5>
        <tr>
            <td style="width: 200px; "><label><?php echo _('Provider credentials'); ?></label></td>
            <td>
                <select name="customprovider" id="customprovider" style="width: 300px;">
                    <option value="newProvider"><?php echo _('New Provider'); ?></option>
                </select>
                <button type="button" id="deleteProviderButton" class="btn btn-sm btn-default tt-bind" style="vertical-align:top; display:none;" title="<?php echo _('Delete Provider'); ?>"><i class="fa fa-trash"></i></button>
                <button type="button" id="addProviderButton" class="btn btn-sm btn-default tt-bind" style="vertical-align:top; display:none;" title="<?php echo _('Add New Provider'); ?>"><i class="fa fa-plus"></i></button>
            </td>
        </tr>
        <!-- OAuth2 templates -->
        <tr>
            <td><label><?php echo _('Provider Templates'); ?></label></td>
            <td><select name='provider' id='provider' >
                    <!-- <option value='custom'>Custom</option> -->
                </select>
                <script> $('#provider').width("100px");</script>
                <div style="display: inline-block">
                    <select name='accessTemplate' id='accessTemplate' style='width: 194px;'>
                    </select>
                    <button type="button" id="deleteTemplateButton" class="btn btn-sm btn-default tt-bind" style="vertical-align:top; position:relative; top:0; display:none;" title="<?php echo _('Delete Template'); ?>"><i class="fa fa-trash"></i></button>
                    <input type="text" id="customTemplateName" name="customTemplateName" style="position: absolute; z-index: 1; left:331px; display:none;" placeholder="custom template">
                </div>
            </td>
        </tr><!-- end OAuth2 templates -->
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <!-- Redirect URI to THIS page (default named oauth-authcodeflow.php) -->
            <td><label><?php echo _('Redirect URI:'); ?></label></td>
            <td><input type="text" name="redirectUri" value="<?php echo $redirectUri;?>" title="<?php echo _('This URL should direct to this file. Your OAuth2 provider may not support private IP addresses, in which case you must use this default if your XI server does not have a valid DNS name.'); ?>">
                <button type="button" class="btn btn-sm btn-default tt-bind" style="vertical-align:top;" title="<?php echo _('Copy to clipboard'); ?>" onclick="copyToClipboard('redirectUri')"><i class="fa fa-copy"></i></button>
                <script>
                    function copyToClipboard(elementName) {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            return navigator.clipboard.writeText($('[name='+elementName+']').val());
                        } else {
                            $('[name='+elementName+']').select();
                            document.execCommand("copy");
                        }
                    }
                </script>
            </td>
        </tr>
        <tr id="refreshToken" style="display: none;">
            <td><label><?php echo _('Refresh Token:'); ?></label></td>
            <td><input type="text" name="refreshToken" value="" readonly></td> 
        </tr>
    </table> <!-- end Provider template setup -->
    <!-- Client Credentials -->
    <h5 class="ul"><?php echo _('Enter Client Credentials'); ?></h5>
    <table class="table table-condensed table-no-border table-auto-width">
        <p><?php echo _("(From your provider's developer console)"); ?></p>
        <tr>
            <td><label><?php echo _('Name these credentials:'); ?></label></td>
            <td><input type="text" autocomplete="off" name="customName"><span id="nameError" style="display:none; color:red;"></span></td>
        </tr>
        <!-- <tr><td></td></tr> -->
        <tr>
            <td><label><?php echo _('Client ID:'); ?></label></td>
            <td>
                <input type="text" autocomplete="off" name="clientId"><button type='button' style='margin-left: 3px; vertical-align:top;' class='btn btn-sm btn-default tt-bind paste-btn' title='<?php echo _('Paste'); ?>'>
                <i class='fa fa-clipboard'></i></button>
            </td>
        </tr>
        <tr class="tenant_id">
            <td><label><?php echo _('Tenant ID (Microsoft):'); ?></label></td>
            <td>
                <input type="text" autocomplete="off" name="tenantId"><button type='button' style='margin-left: 3px; vertical-align:top;' class='btn btn-sm btn-default tt-bind paste-btn' title='<?php echo _('Paste'); ?>'>
                <i class='fa fa-clipboard'></i></button>
            </td>
        </tr>
        <tr>
            <td><label><?php echo _('Client Secret:') ?></label></td>
            <td>
                <input type="password" name="clientSecret" autocomplete="off" style="textfield form-control; width: 220px;" <?php echo sensitive_field_autocomplete(); ?>><button type='button' style='margin-left: 3px; vertical-align:top;' class='btn btn-sm btn-default tt-bind paste-btn' title='<?php echo _('Paste'); ?>'>
                    <i class='fa fa-clipboard'></i></button><button type='button' style='margin-left: 3px; vertical-align:top;' class='btn btn-sm btn-default tt-bind btn-show-password' title='<?php echo _('Show/Hide password'); ?>'>
                    <i class='fa fa-eye'></i></button>
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
    </table> <!-- end Client Credentials -->
    <!-- Scope customization -->
    <h5 class="ul"><?php echo _('Scopes for Desired API Access'); ?></h5>
    <table class="table table-condensed table-no-border table-auto-width">
        <!-- scopes -->
        <tr>
            <td style="vertical-align: top;"><label style="margin-top: 7px;"><?php echo _('Scopes:'); ?></label></td>
            <td style="width: 220px; padding-right: 0;"><table class="innertable" id="scopes" style="border-spacing: 1px;">
                <tr style="vertical-align: top;">
                    <td style="padding: 0 0 5px 0;"><input type="text" name="scope_0"></td>
                </tr>
            </table></td>
            <td style="padding-left: 3px; vertical-align: top; margin-top: 5px;"><div style="">
                <button style="" type="button" onclick="add_scope()"><i class="fa fa-plus"></i></button><button style="" type="button" onclick="remove_scope()"><i class="fa fa-minus"></i></button>
            </div></td>
        </tr>
        <tr>
            <td></td>
        </tr>
    </table> <!-- end Scope Customization -->
    <script>
        /****************************************************************************************
         * scope management functions
         ****************************************************************************************/
        var maxscopeid = 0;
        function add_scope() {
            maxscopeid++;
            const scopeline = document.createElement("tr");
            scopeline.innerHTML = `<tr>
                                    <td style="padding: 5px 0 5px 0;">
                                        <input type="text" name="scope_`+maxscopeid+`" class="textfield form-control" style="width: 300px;">
                                    </td>
                                </tr>`;
            $('#scopes').append(scopeline);
        }
        function remove_scope() {
            if(maxscopeid > 0){
                $('input[name="scope_'+maxscopeid+'"]').parent().parent().remove();
                maxscopeid--;
            }
        }
        function set_scope_count(num) {
            while(maxscopeid+1 > num){
                remove_scope();
            }
            while(maxscopeid+1 < num){
                add_scope();
            }
        }

        $(document).ready(function() {
            /****************************************************************************************
             * initialization
             ****************************************************************************************/
            const redirectUri = '<?php echo $redirectUri; ?>';
            // if googleRedirectUri is set, use it, otherwise use redirectUri since google doesn't support private IP addresses
            const googleRedirectUri = '<?php echo (empty($googleRedirectUri)) ? $redirectUri : $googleRedirectUri ; ?>';
            
            // styling classes
            $('input[type="text"],input[type="password"]').addClass("textfield form-control tt-bind").width("283.2px");
            $('button[type="button"]').addClass("btn btn-sm btn-default");
            $('select').addClass("form-control");
            // Firefox styling
            if(navigator.userAgent.indexOf("Firefox") != -1){
                $('#provider').css("width", "102px");
                $('#customTemplateName').css("width", "102px").css("top", "108px");
                $('.paste-btn').hide(); // pasting doesn't work in Firefox
            }

            // populate provider credential options with existing provider.json files in /usr/local/nagiosxi/etc/components/oauth2/providers
            var providers = {};
            $.post('oauth-ajaxhelper.php', { 'cmd': 'get_providers', 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, function(data){
                providers = data;

                // populate #customprovider <select> with provider names
                $.each(providers, function(i, provider){
                    $('#customprovider').append($('<option>', { value: provider['customName'], text: provider['customName']})); 
                });

                // if there is currently a set of OAuth credentials set for XI from src var
                // NOTE: future OAuth implementations (like OIDC) will need to be added here
                const src = "<?php echo grab_request_var("src", "")?>";
                let setoauthconfig = false;
                if(src == "mail"){ // ?src=mail
                    setoauthconfig = "<?php echo grab_request_var("smtpoauthname", get_option("smtp_oauth_name"))?>";
                }

                // if setoauthconfig is set, select it as the current OAuth provider credentials
                if(setoauthconfig){
                    $('#customprovider').val(setoauthconfig).trigger('change');
                }
            }, 'json');

            // get access templates from access-templates.json
            let accessTemplates = <?php echo file_get_contents('access-templates.json'); ?>;
            // fill #provider <select> from template
            Object.keys(accessTemplates).forEach(function(key){
                $('#provider').append($('<option>', { value: key, text: key[0].toUpperCase() + key.substr(1) })); //may break if using character-based scripts such as Chinese
            });

            /****************************************************************************************
             * on 'change' functions
             ****************************************************************************************/

            // Load provider on selection
            $('#customprovider').on('change', function() {
                if(this.value == "newProvider"){
                    hide_all();
                    set_scope_count(1);
                    $('input[type="text"]:not([readonly])').val("");
                    $('#provider').trigger('change');
                    $('input[type="submit"]').attr('onclick', '');
                    $('input[name="clientSecret"]').val("");
                    $('input[name="clientSecret"]').attr('placeholder', '');
                    $('input').trigger('input');
                    $('#deleteProviderButton').hide();
                    $('#addProviderButton').hide();
                    $('#configurationTitle').text('<?php echo _('Adding New OAuth2 Configuration'); ?>');
                } else {
                    hide_all();
                    $.each(providers[this.value], function(key, value){
                        if(key == 'options'){
                            if(value == [""] || value == null){ // no scopes defined: default to template
                                $('#accessTemplate').trigger('change');
                            } else {
                                set_scope_count(value['scope'].length);
                                $.each(value['scope'], function(i, scope){
                                    $('input[name="scope_'+i+'"]').val(scope);
                                });
                            }
                        } else if (key == 'provider') {
                            $('#provider').val(value).trigger('change');
                        } else if (key == 'refreshToken') {
                            $('input[name="refreshToken"]').val(value);
                            $('#refreshToken').show();
                        } else {
                            //if exists, set value
                            if($('input[name="'+key+'"]').length > 0){
                                $('input[name="'+key+'"]').val(value).trigger('input');
                            }
                        }
                    });
                    $('#accessTemplate').val(providers[this.value]['accessTemplate']).trigger('change');
                    $('input[name="clientSecret"]').attr('title', '<?php echo _("Client Secret withheld for security"); ?>');
                    $('#deleteProviderButton').show();
                    $('#addProviderButton').show();
                    $('#configurationTitle').text('<?php echo _('Editing OAuth Provider Configuration'); ?>');
                }
            });

            // on provider change, fill template options from templates in .../components/oauth2/access-templates.json
            $('#provider').on('change', function(){
                $('.tenant_id').hide();
                $('input[name="redirectUri"]').val(redirectUri);
                if(this.value == 'custom'){
                    $('.tenant_id').show();
                } else if(this.value == 'azure') {
                    $('.tenant_id').show();
                } else if(this.value == 'google') {
                    $('input[name="redirectUri"]').val(googleRedirectUri);
                } else {
                }

                //populate access templates
                $('#accessTemplate').find('option').remove().end().append('<option value="newTemplate"><?php echo _("New Template"); ?></option>');
                $.each(accessTemplates[this.value], function(access_type){
                    $('#accessTemplate').append($('<option>', {
                        value: access_type,
                        text: access_type
                    }));
                });
                if($("#accessTemplate option").length > 1){
                    $("#accessTemplate option:eq(1)").attr('selected', 'selected');
                }
                $('#accessTemplate').trigger('change');
            });

            // on template change, fill scopes from template
            $('#accessTemplate').on('change', function(){
                if(this.value == "newTemplate"){
                    set_scope_count(1);
                    $('input[name="scope_0"]').val("");
                    $('#customTemplateName').show().width(158).val("").focus();
                    $('#updateoauthtemplate').show();
                } else {
                    var count = accessTemplates[$('#provider').val()][this.value]["defaultScopes"].length;
                    set_scope_count(count);
                    $.each(accessTemplates[$('#provider').val()][this.value]["defaultScopes"], function(i, scope){
                        $('input[name="scope_'+i+'"]').val(scope);
                    });
                    $('#customTemplateName').hide();
                    $('#updateoauthtemplate').hide();
                }
                // show/hide delete button based on if template is default
                if(this.value != "newTemplate"){
                    let currentTemplate = accessTemplates[$('#provider').val()][this.value];
                    if((currentTemplate.hasOwnProperty('.default') && currentTemplate['.default'] === 'true') || this.value == 'newTemplate'){
                        $('#deleteTemplateButton').hide();
                    } else {
                        $('#deleteTemplateButton').show();
                    }
                } else {
                    $('#deleteTemplateButton').hide();
                }
            });
            $('#provider').val('azure').trigger('change');
            

            // hide all extra stuff, will be useful when custom provider implementation is possible
            function hide_all() {
                $('#refreshToken').hide();
                return;
            }

            /****************************************************************************************
             * on 'input' functions
             ****************************************************************************************/

            // validate input
            $('input[name=customName]').on('input', function(){
                const customname = $(this).val();
                const invalidchars = customname.match(/[^a-zA-Z0-9_ \-\.,!+&%()]+/g);
                const sanitized = customname.replace(/[^a-zA-Z0-9_ \-\.,!+&%()]+/g, '');
                if(sanitized !== customname){
                    const errmsg = "<?php echo _("Invalid characters: "); ?>"+invalidchars.join(", ");
                    $('#nameError').text(errmsg).css('display', 'block');
                } else {
                    $('#nameError').hide();
                }
            });

            /****************************************************************************************
             * on 'click' functions (except form submission)
             ****************************************************************************************/

            // paste clipboard contents into input field preceding button
            $('.paste-btn').click(function(event) {
                event.preventDefault();
                //if firefox, use execCommand('paste') instead of clipboard API
                if(navigator.userAgent.indexOf("Firefox") != -1){ // WIP: not working
                    $(this).prev().focus();
                    document.execCommand('paste');
                    return;
                } else { // if not firefox, use clipboard API
                    // get clipboard contents and paste into input field
                    navigator.clipboard.readText().then(clipText => {
                        $(this).prev().val(clipText).trigger('input');
                    });
                }
            });

            // sets customprovider to new
            $('button#addProviderButton').click(function(event) {
                event.preventDefault();
                $('#customprovider').val('newProvider').trigger('change');
            });

            // calls async AJAX function to save/update new scope template
            $('button#updateoauthtemplate').click(function(event) {
                event.preventDefault();
                customtemplate = ($('#customTemplateName').val() == '')? 'custom template' : $('#customTemplateName').val();
                update_template(customtemplate);
            });

            // calls async AJAX function to delete provider
            $('button#deleteProviderButton').click(function(event) {
                event.preventDefault();
                if(confirm("<?php echo _("Are you sure you want to delete the provider"); ?> '"+$('#customprovider').val()+"<?php echo _("'?"); ?>")){
                    delete_provider();
                }
            });

            // calls async AJAX function to delete template
            $('button#deleteTemplateButton').click(function(event) {
                event.preventDefault();
                if(confirm("<?php echo _("Are you sure you want to delete the template"); ?> '"+$('#accessTemplate').val()+"<?php echo _("'?"); ?>")){
                    delete_template();
                }
            });


            /****************************************************************************************
             * async/AJAX functions for updating/deleting templates/providers and flash messages
             ****************************************************************************************/

            /*
            * AJAX Delete Provider Credentials from /etc/components/oauth2/providers/
            *
            * @param {string} $('#smtpoauthname').val() -- provider name
            * 
            *   AJAX returns:
                * @return {object} response -- response from AJAX call
                    * {string} response.status    -- status of deleting provider
                    * {object} response.providers -- new list of providers
            * 
            * @return {bool}
            */
            async function delete_provider(){
                $.ajax({ 
                    url: 'oauth-ajaxhelper.php',
                    type: 'post',
                    data: { cmd: 'delete_provider', credentialsname: $('#customprovider').val(), 'nsp': '<?php echo get_nagios_session_protector_id(); ?>' }, success: function(response) {
                        response = JSON.parse(response);
                        if(response.status == 'success'){
                            flash_alert('<?php echo _("Provider deleted successfully."); ?>', 'success');
                            providers = response.providers;
                            update_providers(providers);
                            $('#customprovider').trigger('change'); //refresh provider list
                            return true;
                        } else {
                            flash_alert('<?php echo _("Deleting failed. Please try again."); ?>', 'error');
                            return false;
                        }
                    }, error: function(response) {
                        flash_alert('<?php echo _("AJAX call failed. Please try again."); ?>', 'error');
                        return false;
                    }
                });
                return;
            }

            /*
            * Helper function to update OAuth provider credentials list
            *
            * @param {object} providerlist -- list of providers
            * 
            * @return {void}
            */
            function update_providers(providerlist){
                $('#customprovider option').remove();
                $('#customprovider').append($('<option>', { value: 'newProvider', text: '<?php echo _("New Provider"); ?>'}));
                $.each(providers, function(i, provider){
                    $('#customprovider').append($('<option>', { value: provider['customName'], text: provider['customName']})); 
                });
            }

            /*
             * AJAX Add/Update Template in /html/includes/components/oauth2/access-templates.json
             *
             * @param {string} $('#provider').val() -- provider name
             * @param {string} templateName         -- name of template to add/update
             * 
             *  AJAX returns:
                * @return {object} response -- response from AJAX call
                    * {string} response.status    -- status of updating provider
                    * {object} response.providers -- new list of providers
             * 
             * @return {bool} -- returns status of AJAX call + operation
             */
            async function update_template(templateName){
                scopes = await aggregate_scopes();
                if(!scopes || scopes.length == 0){
                    flash_alert("'"+templateName+"' <?php echo _("not saved. Please enter at least one scope to save a template."); ?>", 'error');
                    return;
                }
                // if using google, use smtpOAuth as base for new template for SMTP settings
                let provider = $('#provider').val();
                if($('#provider').val() == 'google'){ //loads smtpOAuth template as base for new template for SMTP settings
                    accessTemplates['google'][templateName] = JSON.parse(JSON.stringify(accessTemplates['google']['smtpOAuth'])); //deep copy
                    accessTemplates['google'][templateName]['defaultScopes'] = scopes;
                    delete accessTemplates['google'][templateName]['.default'];
                } else {
                    accessTemplates[$('#provider').val()][templateName] = {'defaultScopes': scopes};
                }
                // AJAX -- update access-templates.json with accessTemplates
                try {
                    let response = await $.post('oauth-ajaxhelper.php', { 'cmd': 'update_templates', 'templates': accessTemplates, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>'}, function(response){});
                    response = JSON.parse(response);
                    if(response.status == 'success'){
                        flash_alert("<?php echo _('Template updated successfully'); ?>", 'success');
                        accessTemplates = JSON.parse(response.templates); // just to make absolutely sure that the accessTemplates object is updated correctly
                    } else {
                        flash_alert("<?php echo _('Template update writing failed.'); ?>", 'error');
                    }
                    $('#provider').val(provider).trigger('change');
                    $('#accessTemplate').val(templateName).trigger('change');
                    return true;
                } catch (error) {
                    return false;
                }
            }

            /*
             * AJAX to oauth-ajaxhelper.php to delete template
             *
             * @param {string} $('#provider').val()        -- provider name
             * @param {string} $('#accessTemplate').val()  -- template name
             * 
             *  AJAX returns:
                * @return {object} response -- response from AJAX call
                    * @return {string} response.status    -- status of deleting provider
                    * @return {object} response.providers -- new list of providers
             * 
             * @return {bool} -- returns true/false and flashes alert on success or failure
             */
            async function delete_template(){
                let currentTemplate = accessTemplates[$('#provider').val()][$('#accessTemplate').val()];
                delete accessTemplates[$('#provider').val()][$('#accessTemplate').val()];
                // AJAX update_templates -- updated access-templates.json with accessTemplates
                let response = await $.post('oauth-ajaxhelper.php', { 'cmd': 'update_templates', 'templates': accessTemplates, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>'}, function(response){}, "json");
                if(response.status == 'success'){
                    flash_alert("<?php echo _('Template deleted successfully'); ?>", 'success');
                    accessTemplates = JSON.parse(response.templates); // just to make absolutely sure that the accessTemplates object is updated correctly
                    let provider = $('#provider').val();
                    $('#provider').val(provider).trigger('change');
                    $('#accessTemplate').prop('selectedIndex',1);
                    return true;
                } else { flash_alert("<?php echo _('Template deleting failed'); ?>", 'error'); return false;}
            }

            /*
             *  Helper function to aggregate scopes for update_template
             *
             * @return {array} scopes - current array of scopes
             */
            function aggregate_scopes(){
                let scopes = [];
                $('input[name^="scope_"]').each(function() {
                    if($(this).val() != ""){
                        scopes.push($(this).val());
                    }
                });
                return scopes;
            }

            /*
            *  Helper function to flash alerts
            *
            * @param {string} message - message to display
            * @param {string} type    - type of alert (success, error, info, warning)
            * 
            *   AJAX returns:
                * @return {string(HTML)} alert -- response from AJAX call
            * 
            * @return {bool} -- returns true/false and flashes alert on success or failure
            */
            async function flash_alert(message, type = ''){
                try{
                    let container = $('body');
                    let alert = await $.post('oauth-ajaxhelper.php', { 'cmd': 'flash_alert', 'message': message, 'type': type, 'nsp': '<?php echo get_nagios_session_protector_id(); ?>'}, function(response){}, "html");
                    if(container && alert){
                        alert = $(alert).prependTo(container);
                        setTimeout(() => {alert.remove()}, 3000);
                    }
                    return true;
                } catch(error){
                    return false;
                }
            }


            /****************************************************************************************
            * Form submission
            ****************************************************************************************/

            $('#submitform').click(function(event){

                // ensure credentials fields are filled out
                if( $('input[name="customName"]').val() == "" ||
                    $('input[name="clientId"]').val() == "" || 
                    $('input[name="clientSecret"]').val() == "" || 
                    ($('#provider').val() == 'azure' && $('input[name="tenantId"]').val() == "")){

                    flash_alert("<?php echo _('Please enter a value for each credentials field.'); ?>", 'error');
                    return false;
                }
                
                //check if some scopes aren't empty
                let scopes = aggregate_scopes();
                if(scopes.length == 0){
                    flash_alert("<?php echo _('Please enter at least one scope.'); ?>", 'error');
                    return false;
                }


                // ensure name is valid and prompt to overwrite if it already exists
                let customName = $('#customprovider').val();
                if($('#nameError').css('display') == 'none'){ // name is valid
                    // name exists in providers, prompt to overwrite
                    if(customName in providers){
                        if(confirm("<?php echo _('This will overwrite an existing configuration. Are you sure you want to continue?'); ?>")){
                            return true; // user confirmed overwrite
                        }
                        return false; // user cancelled overwrite
                    }
                    return true; // name is valid and doesn't exist in providers
                } else { // name is invalid
                    flash_alert("<?php echo _('Please enter a valid name for the provider.'); ?>", 'error');
                    return false;
                }
                return false; // catch all
            });
        }); // end document.ready
    </script>

    <button id="back" class="btn btn-sm btn-default" onclick="event.preventDefault();window.close();"><i class="fa fa-chevron-left"></i><?php echo _(" Back"); ?></button>
    <button id="submitform" class="btn btn-sm btn-primary" ><?php echo _("Continue "); ?><i class="fa fa-chevron-right"></i></button>
    <button id="updateoauthtemplate" class="btn btn-sm btn-default" ><?php echo _("Save scopes to template"); ?></button>
</form>
</body>
</html>
    <?php
    do_page_end(true);
    exit;
}

//======================================================================
// END FORM SECTION
//======================================================================



//======================================================================
// VARIABLE HANDLING
//======================================================================

require_once 'createprovider.php';

$var_array = array('customName','providerName','clientId','clientSecret','tenantId','redirectUri','accessTemplate');
foreach($var_array as $var){
    if(!isset($$var))$$var='';
}
if($providerName != 'azure' && $providerName != 'microsoft'){ $tenantId = ""; }

$scopes = [];
$scopesct = 0;
while(isset($request['scope_'.$scopesct])){
    array_push($scopes, $request['scope_'.$scopesct++]);
}
unset($scopesct);
$request['scopes'] = $scopes;

//note: $request already sanitized from grab_request_vars()
if (array_key_exists('provider', $request)) {
    $customName     = $request['customName'];
    $providerName   = $request['provider'];
    $accessTemplate = $request['accessTemplate'];
    $redirectUri    = $request['redirectUri'];
    $clientId       = $request['clientId'];
    $clientSecret   = $request['clientSecret'];
    $tenantId       = $request['tenantId'];
    $scopes         = $request['scopes'];

    $_SESSION['customName']     = $customName;
    $_SESSION['provider']       = $providerName;
    $_SESSION['clientId']       = $clientId;
    $_SESSION['clientSecret']   = $clientSecret;
    $_SESSION['tenantId']       = $tenantId;
    $_SESSION['redirectUri']    = $redirectUri;
    $_SESSION['scopes']         = $scopes;
    
} elseif (array_key_exists('provider', $_SESSION)) {
    $customName     = $_SESSION['customName'];
    $providerName   = $_SESSION['provider'];
    $clientId       = $_SESSION['clientId'];
    $clientSecret   = $_SESSION['clientSecret'];
    $tenantId       = $_SESSION['tenantId'];
    $redirectUri    = $_SESSION['redirectUri'];
    $scopes         = $_SESSION['scopes'];
} else {
    $customName     = '';
    $providerName   = '';
    $clientId       = '';
    $clientSecret   = '';
    $tenantId       = '';
    $redirectUri    = '';
    $scopes         = [];
}
if(empty($customName)){
    $customName = $providerName.'-'.$accessTemplate.'-credentials';
}
// extra sanitization for filename
$customName = preg_replace('/[^a-zA-Z0-9_ \-\.,!+&%()]+/', '', $customName);

//If you don't want to use the built-in form/provider json files, you can set your details here
//$providerName = 'azure'; //or 'google' or 'microsoft'
//$clientId = 'RANDOMCHARS-----duv1n2.apps.googleusercontent.com';
//$clientSecret = 'RANDOMCHARS-----lGyjPcRtvP';
//$tenantId = 'RANDOMCHARS-----lGyjPcRtvP'; //only for microsoft/azure
//$redirectUri = 'https://yourdomain.com/nagiosxi/includes/components/oauth2/redirect.php';

//======================================================================
// USE VARIABLES TO CREATE PROVIDER OBJECT
//======================================================================

//combine params and options to create provider
$destinationUri = '';
$params = [
    'clientId'      => $clientId,
    'clientSecret'  => $clientSecret,
    'redirectUri'   => $redirectUri,
    'accessType'    => 'offline'
];
$options = [
    'scope' => $scopes
];

// create provider from createprovider.php
if(!empty($tenantId)){ $params['tenantId'] = $tenantId; }
try {
    $provider = getProvider($providerName, $params);
} catch (Throwable $e) {
    display_exception($e);
}
if (null === $provider) {
    echo_alert_close(_('Provider creation failed. Check your provider name and/or parameters.'));
}

//======================================================================
// AUTHORIZATION CODE FLOW -- REDIRECTS AND SAVING OAuth2 data
//======================================================================
// pass 1 - first time through submits form to this page.
// pass 2 - this page saves the provider information and then redirects to provider
//      user logs in to provider and authorizes access provider redirects back to this 
//      page with an authorization code ($request['code'] from $_GET['code'])
// pass 3 - we now have an authorization code, so if state is valid we can get an access token
// this page uses the access token to get a refresh token, then saves the refresh token to the provider file

// No Authorization Code (first time through), so redirect to get one
if (!isset($request['code'])) {
    $refreshToken = (isset($request['refreshToken'])) ? $request['refreshToken'] : '';

    // Store current provider & client data
    $provider_data = [
        'customName'    => $customName,
        'provider'      => $providerName,
        'accessTemplate'=> $accessTemplate,
        'refreshToken'  => $refreshToken,
        'options'       => $options,
        'clientId'      => $clientId,
        'clientSecret'  => $clientSecret,
        'redirectUri'   => $redirectUri,
        'accessType'    => 'offline'
    ];
    if(!empty($tenantId)){ $provider_data['tenantId'] = $tenantId; }

    try{
        file_put_contents("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp", encrypt_data(json_encode($provider_data)));
    
        //Direct user to authorization url to get authorization code
        $authUrl = $provider->getAuthorizationUrl($options);
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);
    } catch(Throwable $e){
        display_exception($e);
    }
    exit;
// Invalid state: block page to mitigate CSRF attack
} elseif (empty($request['state']) || ($request['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    unset($_SESSION['provider']);
    echo_alert_close(_("Possible CSRF attack, page blocked. Please try again."));
    exit;
// Auth code received and state is valid - get access token and save provider data
} else {
    unset($_SESSION['provider']);

    try {
        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken(
            'authorization_code',
            [
                'code' => $request['code']
            ]
        );
        //Use refresh token to get a new access token if the old one expires
        $refreshToken = $token->getRefreshToken();
    } catch (Throwable $e) {
        display_exception($e);
    }

    // If we don't have a refresh token, check if using openid (openid doesn't need refresh token)
    if ($refreshToken == NULL){
        if(!in_array('openid', $scopes)){
            echo_alert_close(_("Refresh Token is NULL. If you meant to give Nagios XI continued functionality with these credentials to the scoped resources, please add the 'offline_access' scope to your configuration. Note that certain providers may not supply a refresh token on subsequent requests and you may need to create a new secret."));
        }
    }

    // Get provider data and add refresh token
    if(is_file("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp")){
        if($provider_data = json_decode(decrypt_data(file_get_contents("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp")), true)){
            unlink("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp");
            if(isset($refreshToken) && $refreshToken != null) { $provider_data['refreshToken'] = $refreshToken; }
        } else { echo_alert_close(_("Couldn't open ".$customName.".json")); }
    } else { echo_alert_close(_($customName.".json is not a file.")); }

    // Save provider data
    if(!empty($provider_data)) {
        if(isset($provider_data['refreshToken']) && $provider_data['refreshToken'] != null) { 
            if(file_put_contents("/usr/local/nagiosxi/etc/components/oauth2/providers/".$customName.".json", encrypt_data(json_encode($provider_data)))) { 
                echo_alert_close(_("Provider successfully saved. Refresh token updated. You may close this page."), true);
            } else { echo_alert_close(_("Provider not saved, please try again.")); }
        } else { 
            echo_alert_close(_("Refresh Token is NULL. Note that certain providers may not supply a refresh token on subsequent requests and you may need to create a new secret and try again."));
        }
    } else { echo_alert_close(_('Provider NULL, please try again.')); }
}

/*
 * Echoes text to the page, gives an alert, and closes the window if the browser supports it.
 * 
 * @param string $msg The message to display in the alert.
 * @return void
 */
function echo_alert_close($msg, $success = false){
    if($success){
        do_page_start(array("page_title"=>_("OAuth2 Confirmation")), true);
        echo '<pre>'._("Success: ").$msg.'</pre>';
    } else {
        do_page_start(array("page_title"=>_("OAuth2 Error")), true);
        echo '<pre>'._("Error: ").$msg.'</pre>';
        echo "<br><br><button class='btn btn-sm btn-default tt-bind' onclick='window.history.back()'>"._("Go Back")."</button>"; //make sure this goes where you want
    }
    echo "<br><br><a href='".get_base_url()."'>"._("Return to Nagios XI")."</a>";
    echo '<script>alert("'.$msg.'");window.close();</script>';
    exit;
}

/*
 * Displays an exception to the page.
 * 
 * @param string $e The exception to display on the page.
 * @return void
 */
function display_exception($e){
    do_page_start(array("page_title"=>_("OAuth2 Error")), true);
    echo '<pre>'._('Error: ').$e.'<br></pre>';
    echo _("There was an error with your request. Please try again.");
    echo "<br><br><button class='btn btn-sm btn-default tt-bind' onclick='window.history.back()'>"._("Go Back")."</button>"; //make sure this goes where you want
    echo "<br><br><a href='".get_base_url()."'>"._("Return to Nagios XI")."</a>";
    exit;
}

// set error handler to throw exceptions
set_error_handler('error_to_exception');
register_shutdown_function('error_to_exception');
/**
 * Handle fatal errors just in case
 */
function error_to_exception($errno, $errstr, $errfile, $errline){
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}