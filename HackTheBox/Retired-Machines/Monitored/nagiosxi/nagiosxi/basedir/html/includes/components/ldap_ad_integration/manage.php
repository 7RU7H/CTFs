<?php
//
// LDAP / Active Directory Integration
// Copyright (c) 2015-2022 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__).'/../../common.inc.php');
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

require_once(dirname(__FILE__).'/adLDAP/src/adLDAP.php');
include_once(dirname(__FILE__).'/ldap_ad_integration.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs and authentication
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You do not have access to this section.");
    exit();
}

route_request();

function route_request()
{
    $cmd = grab_request_var("cmd", "");

    switch ($cmd)
    {
        case 'deleteserver':
            process_delete_server();
            break;

        case 'editserver':
            process_edit_server();
            break;

        case 'addserver':
            process_add_server();
            break;

        default:
            display_page();
            break;
    }

}

/*
// Save settings
$server = array(
    "enabled" => $enabled,
    "conn_method" => $conn_method,
    "ad_account_suffix" => $account_suffix,
    "ad_domain_controllers" => $domain_controllers,
    "base_dn" => $base_dn,
    "security_level" => $security_level,
    "ldap_port" => $ldap_port,
    "ldap_host" => $ldap_host
);

set_option("ldap_ad_integration_component_servers", serialize($settings));
*/

// Display the default page
function display_page($posted=false, $error='', $msg='')
{
    $servers = auth_server_list();
    $component_url = get_component_url_base("ldap_ad_integration");

    // Get the settings that someone put in and tried to save...
    $enabled = grab_request_var("enabled", "");
    if (!empty($enabled) && $posted) { $enabled = 1; }
    else if (empty($enabled) && !$posted) { $enabled = 1; }

    $conn_method = grab_request_var("conn_method", "ad");
    $base_dn = grab_request_var("base_dn", "");
    $security_level = grab_request_var("security_level", "none");

    // AD Only
    $account_suffix = grab_request_var("account_suffix", "");
    $domain_controllers = grab_request_var("domain_controllers", "");

    // LDAP Only
    $ldap_host = grab_request_var("ldap_host", "");
    $ldap_port = grab_request_var("ldap_port", "389");

    do_page_start(array("page_title" => _("LDAP / Active Directory Integration Configuration")), true);
    ?>

    <h1><?php echo _("LDAP / Active Directory Integration Configuration"); ?></h1>

    <div class="hide">
        <label style="line-height: 24px;">
            <input type="checkbox" style="vertical-align: middle;" class="checkbox" id="enabled" name="enabled" <?php echo is_checked($enabled, 1); ?>> <?php echo _('Enable LDAP / Active Directory Authentication'); ?>
        </label>
    </div>

    <div>
        <div style="width: 48%; min-width: 750px; float: left;">

            <h5 class="ul"><?php echo _("LDAP/AD Authentication Servers"); ?></h5>
            <p>
                <?php echo _("Authentication servers can be used to authenticate users over on login."); ?><br>
                <?php echo _("Once a server has been added you can "); ?> <a href="<?php echo $component_url; ?>/index.php"><?php echo _("import users"); ?></a>.
            </p>
            <p><button id="add-auth-server" type="button" class="btn btn-sm btn-primary"><?php echo _("Add Authentication Server"); ?></button></p>
            <table class="table table-condensed table-striped table-bordered" style="width: 94%;">
                <thead>
                    <tr>
                        <th style="width: 20px; text-align: center;"></th>
                        <th><?php echo _("Server(s)"); ?></th>
                        <th><?php echo _("Type"); ?></th>
                        <th><?php echo _("Encryption"); ?></th>
                        <th style="width: 120px;"><?php echo _("Associated Users"); ?></th>
                        <th style="text-align: center; width: 60px;"><?php echo _("Actions"); ?></th>
                    </tr>
                </thead>
                <tbody id="ldap_ad_servers">
                    <?php
                    if (empty($servers)) {
                    ?>
                    <tr>
                        <td colspan="9"><?php echo _("There are currently no LDAP or AD servers to authenticate against."); ?></td>
                    </tr>
                    <?php
                    } else {
                        foreach ($servers as $server) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><img src="<?php if ($server['enabled']) { echo theme_image("ok_small.png"); } else { echo theme_image("error_small.png"); } ?>" title="<?php if ($server['enabled']) { echo _("Enabled"); } else { echo _("Not enabled"); } ?>"></td>
                            <td><?php if ($server['conn_method'] == "ldap") { echo encode_form_val($server['ldap_host']); } else { echo encode_form_val($server['ad_domain_controllers']); } ?></td>
                            <td><?php echo ldap_ad_display_type($server['conn_method']); ?></td>
                            <td><?php if ($server['security_level'] == 'tls') { echo 'STARTTLS'; } else if ($server['security_level'] == 'ssl') { echo 'SSL/TLS'; } else { echo _('None'); } ?></td>
                            <td><?php echo ldap_ad_get_associations($server['id']); ?></td>
                            <td style="text-align: center;">
                                <a class="edit" data-id="<?php echo $server['id']; ?>" style="cursor: pointer; font-size: 16px; display: inline-block; margin-right: 5px;" title="<?php echo _("Edit server"); ?>"><i class="fa fa-pencil"></i></a>
                                <a href="manage.php?cmd=deleteserver&server_id=<?php echo $server['id']; ?>" style="cursor: pointer; font-size: 18px; display: inline-block;" title="<?php echo _("Remove server"); ?>"><i class="fa fa-times"></i></a>
                            </td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div <?php if (!$posted) { echo 'class="hide"'; } ?> id="manage-servers">
                <form action="manage.php" method="post"> 
                <h5 class="ul"><?php echo _('Authentication Server Settings'); ?></h5>

                <?php if (!empty($error)) { ?>
                <div class="message" style="width: 450px;">
                    <ul class="errorMessage">
                        <li><?php echo $error; ?></li>
                    </ul>
                </div>
                <?php } ?>

                <table style="margin-bottom: 20px;">
                    <tr>
                        <td></td>
                        <td>
                            <label style="margin-bottom: 10px;">
                                <input type="checkbox" name="enabled" value="1" <?php if ($enabled) { echo "checked"; } ?>>
                                <?php echo _("Enable this authentication server"); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="padding-top: 4px;">
                            <label for="conn_method"><?php echo _("Connection Method"); ?>:</label>
                        </td>
                        <td>
                            <select name="conn_method" id="conn_method" class="form-control">
                                <option value="ad" <?php echo is_selected($conn_method, 'ad'); ?>><?php echo _("Active Directory"); ?></option>
                                <option value="ldap" <?php echo is_selected($conn_method, 'ldap'); ?>><?php echo _("LDAP"); ?></option>
                            </select>
                            <div style="margin-bottom: 10px;"><?php echo _('Use either LDAP or Active Directory settings to connect'); ?>.</div>
                        </td>
                    </tr>
                    <tr class="option ad-options ldap-options">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="base_dn"><?php echo _('Base DN'); ?>:</label>
                        </td>
                        <td valign="top">
                            <input type="text" size="40" name="base_dn" id="base_dn" value="<?php echo encode_form_val($base_dn); ?>" placeholder="dc=nagios,dc=com" class="textfield form-control">
                            <div style="margin-bottom: 10px;"><?php echo _('The LDAP-format starting object (distinguished name) that your users are defined below, such as'); ?> <strong>DC=nagios,DC=com</strong>.</div>
                        </td>
                    </tr>
                    <tr class="option ad-options <?php if ($conn_method == "ldap") { echo 'hide'; } ?>">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="account_suffix"><?php echo _('Account Suffix'); ?>:</label>
                        </td>
                        <td>
                            <input type="text" size="25" name="account_suffix" id="account_suffix" value="<?php echo encode_form_val($account_suffix); ?>" placeholder="@nagios.com" class="textfield form-control">
                            <div style="margin-bottom: 10px;"><?php echo _('The part of the full user identification after the username, such as'); ?> <strong>@nagios.com</strong>.</div>
                        </td>
                    </tr>
                    <tr class="option ad-options <?php if ($conn_method == "ldap") { echo 'hide'; } ?>">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="domain_controllers"><?php echo _('Domain Controllers'); ?>:</label>
                        </td>
                        <td valign="top">
                            <input type="text" size="80" name="domain_controllers" id="domain_controllers" value="<?php echo encode_form_val($domain_controllers); ?>" placeholder="dc1.nagios.com,dc2.nagios.com" class="textfield form-control">
                            <div style="margin-bottom: 10px;"><?php echo _('A comma-separated list of domain controllers on your network.'); ?></div>
                        </td>
                    </tr>
                    <tr class="option ldap-options <?php if ($conn_method == "ad") { echo 'hide'; } ?>">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="ldap_host"><?php echo _("LDAP Host"); ?>:</label>
                        </td>
                        <td>
                            <input type="text" name="ldap_host" id="ldap_host" value="<?php echo encode_form_val($ldap_host); ?>" placeholder="ldap.nagios.com" class="textfield form-control" style="width: 300px;">
                            <div style="margin-bottom: 10px;"><?php echo _('The IP address or hostname of your LDAP server.'); ?></div>
                        </td>
                    </tr>
                    <tr class="option ldap-options <?php if ($conn_method == "ad") { echo 'hide'; } ?>">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="ldap_port"><?php echo _("LDAP Port"); ?>:</label>
                        </td>
                        <td>
                            <input type="text" name="ldap_port" id="ldap_port" value="<?php echo encode_form_val($ldap_port); ?>" class="textfield form-control" style="width: 60px;">
                            <div style="margin-bottom: 10px;"><?php echo _('The port your LDAP server is running on. (Default is 389)'); ?></div>
                        </td>
                    </tr>
                    <tr class="option ad-options ldap-options">
                        <td valign="top" style="padding-top: 4px;">
                            <label for="security_level"><?php echo _('Security'); ?>:</label>
                        </td>
                        <td valign="top">
                            <select name="security_level" id="security_level" class="form-control">
                                <option value="none" <?php echo ldap_ad_has_security($security_level, "none"); ?>><?php echo _('None'); ?></option>
                                <option value="ssl" <?php echo ldap_ad_has_security($security_level, "ssl"); ?>>SSL/TLS</option>
                                <option value="tls" <?php echo ldap_ad_has_security($security_level, "tls"); ?>>STARTTLS</option>
                            </select>
                            <div><?php echo _("The type of security (if any) to use for the connection to the server(s). The STARTTLS option may use a plain text connection if the server does not upgrade the connection to TLS."); ?></div>
                        </td>
                    </tr>
                </table>
                <p>
                    <input type="hidden" id="server_id" name="server_id" value="">
                    <input type="hidden" id="cmd" name="cmd" value="addserver">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo _("Save Server"); ?></button> 
                    <button type="button" class="btn btn-sm btn-default" id="cancel-add"><?php echo _("Cancel"); ?></button>
                </p>
                </form>
            </div>
        </div>
        <div style="width: 50%; min-width: 600px; float: left;">
            <h5 class="ul"><?php echo _('Certificate Authority Management'); ?></h5>
            <p><?php echo _("For connecting over SSL/TLS, or STARTTLS using self-signed certificates you will need to add the certificate(s) of the domain controller(s) to the local certificate authority so they are trusted. If any certificate was signed by a host other than itself, that certificate authority/host certificate needs to be added."); ?></p>
            <div style="margin-bottom: 10px;">
                <button type="button" class="btn btn-sm btn-primary" id="add-ca-cert"><?php echo _("Add Certificate"); ?></button>
            </div>
            <table class="table table-condensed table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?php echo _("Hostname"); ?></th>
                        <th><?php echo _("Issuer (CA)"); ?></th>
                        <th><?php echo _("Expires On"); ?></th>
                        <th style="text-align: center; width: 60px;"><?php echo _("Actions"); ?></th>
                    </tr>
                </thead>
                <tbody id="cert-list">
                </tbody>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {

        // Load a list of certificates
        load_certificates();

        // Popup window to add a certificate
        $("#add-ca-cert").click(function() {
            var content = '<div><h2><?php echo encode_form_valq(_("Add Certificate to Certificate Authority")); ?></h2><p><?php echo encode_form_valq(_("To add a certificate to the certificate authority, copy and paste the actual certificate between, and including, the begin/end certificate sections.")); ?></p><div><label for="cahost"><?php echo encode_form_valq(_("Hostname")); ?></label></div><div><input name="host" class="form-control" id="cahost" type="text" style="width: 200px;" readonly> <i id="caload" class="fa fa-spinner fa-spin" style="font-size: 20px; vertical-align: middle; margin-left: 6px; display: none;"></i> <span id="caerror" style="display: none; margin-left: 6px; color: red;"></span></div><div style="margin-top: 5px;"><label><?php echo encode_form_valq(_("Certificate")); ?></label></div><div style="margin-bottom: 10px;"><textarea id="cert" spellcheck="false" name="cert" style="width: 446px; height: 200px; font-family: monospace;" class="form-control"></textarea></div><div style="margin-top: 6px;"><button id="add-ca-cert-popup" class="btn btn-sm btn-primary" disabled><?php echo encode_form_valq(_("Add Certificate")); ?></button></div></div>';
                    
            $("#child_popup_container").height(392);
            $("#child_popup_container").width(470);
            $("#child_popup_layer").height(412);
            $("#child_popup_layer").width(490);
            $("#child_popup_layer").css("position", "fixed");
            center_child_popup();
            set_child_popup_content(content); 
            display_child_popup(300);
        });

        // Popup the window to load in a certificate
        $("#child_popup_container").on("paste", "#cert", function(e) {
            $("#caerror").hide();
            $("#caload").show();
            var elem = $(this);
            setTimeout(function() {
                var cert = elem.val();
                $.post("<?php echo $component_url; ?>/ajax.php", { cmd: "getcertinfo", cert: cert }, function(data) {
                    $("#caload").hide();
                    if (data.error) {
                        $("#caerror").html('<img src="<?php echo theme_image("error_small.png"); ?>"> '+data.message).show();
                    } else {
                        $("#cahost").val(data.certinfo.CN);
                        $("#add-ca-cert-popup").attr("disabled", false);
                    }
                }, "json");
            }, 200);
        });

        // Actually add the certificate when clicking the button
        $("#child_popup_container").on("click", "#add-ca-cert-popup", function(e) {
            $("#caerror").hide();
            $("#add-ca-cert-popup").attr("disabled", true);
            $("#cert").attr("disabled", true);
            var cert = $("#cert").val();
            $.post("<?php echo $component_url; ?>/ajax.php", { cmd: "addcert", cert: cert }, function(data) {
                if (data.error) {
                    $("#caerror").html('<img src="<?php echo theme_image("error_small.png"); ?>"> '+data.message).show();
                    $("#add-ca-cert-popup").attr("disabled", false);
                    $("#cert").attr("disabled", false);
                } else {
                    close_child_popup();
                    load_certificates();
                }
            }, "json");
        });

        // Remove a certificate from the list/on the filesystem
        $("#cert-list").on("click", ".remove", function(e) {
            var cert_id = $(this).data("certid");
            $.post("<?php echo $component_url; ?>/ajax.php", { cmd: "delcert", cert_id: cert_id }, function(data) {
                load_certificates();
            }, "json");
        });

        // Change the options for an LDAP server vs Active Directory
        $("#conn_method").click(function() {
            var type = $(this).val();
            $('.option').hide();
            if (type == "ad") {
                // Show AD options
                $(".ad-options").show();
            } else {
                // Show the LDAP options
                $(".ldap-options").show();
            }
        });

        $("#add-auth-server").click(function() {
            clear_server_form();
            $('#cmd').val('addserver');
            $("#manage-servers").show();
        });

        $("#cancel-add").click(function() {
            $("#manage-servers").hide();
            $(".message").remove();
        });

        $(".edit").click(function() {
            var server_id = $(this).data('id');

            // Get server information
            $.post('<?php echo $component_url; ?>/ajax.php', { cmd: 'getserver', server_id: server_id }, function(server) {
                if (server != '') {

                    clear_server_form();

                    // Set all server information into correct spots
                    if (server.enabled == 1) {
                        $('input[name="enabled"]').attr('checked', true);
                    } else {
                        $('input[name="enabled"]').attr('checked', false);
                    }
                    $('#conn_method').val(server.conn_method).trigger('click');
                    $('#security_level').val(server.security_level);
                    $('#base_dn').val(server.base_dn);
                    if (server.conn_method == "ldap") {
                        $('#ldap_host').val(server.ldap_host);
                        $('#ldap_port').val(server.ldap_port);
                    } else {
                        $('#account_suffix').val(server.ad_account_suffix);
                        $('#domain_controllers').val(server.ad_domain_controllers);
                    }

                    $('#cmd').val('editserver');
                    $('#server_id').val(server_id);
                    $("#manage-servers").show();
                }
            }, 'json');
            
        });

    });

    function clear_server_form() {
        $('input[name="enabled"]').attr('checked', true);
        $('#conn_method').val('ad').trigger('click');
        $('#security_level').val('none');
        $('#base_dn').val('');
        $('#ldap_host').val('');
        $('#ldap_port').val('');
        $('#account_suffix').val('');
        $('#domain_controllers').val('');
        $('#server_id').val('');
    }

    // Loads a list of certificates from on the system
    function load_certificates() {
        var html = "";
        $.post("<?php echo $component_url; ?>/ajax.php", { cmd: "getcerts" }, function(data) {
            if (data.length > 0) {
                $.each(data, function(k, v) {
                    var time = '';
                    if (v.valid_to == -1) {
                        time = "<?php echo _('Unknown'); ?>";
                    } else {
                        var to = new Date(v.valid_to*1000);
                        time = to.toString();
                    }
                    html += '<tr><td><span title="ID: '+v.id+'">'+v.host+'</span></td><td>'+v.issuer+'</td><td>'+time+'</td><td style="text-align: center;"><a style="font-size: 18px; display: inline-block;" class="remove" title="<?php echo encode_form_valq(_("Remove certificate")); ?>" data-certid="'+v.id+'"><i class="fa fa-times"></i></a></td></tr>';
                });
            } else {
                html += '<tr><td colspan="9"><?php echo encode_form_valq(_("You have not added any CA certificates through the web interface")); ?></td></tr>';
            }
            $("#cert-list").html(html);
        }, "json");
    }
    </script>

    <?php
    do_page_end(true);
}

// Process the form for adding a new server...
function process_add_server()
{
    // Verify we have all the required info (based on LDAP or AD)
    $error = "";
    $enabled = grab_request_var("enabled", "");
    if (!empty($enabled)) { $enabled = 1; }

    $conn_method = grab_request_var("conn_method", "");
    $base_dn = grab_request_var("base_dn", "");
    $security_level = grab_request_var("security_level", "none");

    // AD Only
    $account_suffix = grab_request_var("account_suffix", "");
    $domain_controllers = grab_request_var("domain_controllers", "");

    // LDAP Only
    $ldap_host = grab_request_var("ldap_host", "");
    $ldap_port = grab_request_var("ldap_port", "");

    if ($conn_method == "ad") {

        // Verify AD sections required
        if (empty($account_suffix) || empty($base_dn) || empty($domain_controllers) || empty($security_level)) {
            $error = _("You must fill out all the Active Directory server information.");
        }

    } else if ($conn_method == "ldap") {

        // Verify LDAP sections required
        if (empty($ldap_host) || empty($ldap_port) || empty($base_dn) || empty($security_level)) {
            $error = _("You must fill out all the LDAP server information.");
        }

    } else {
        // Not a recognized connection method...
        $error = _("Unknown connection method specified.");
    }

    // Quit if error exists
    if (!empty($error)) {
        display_page(true, $error);
        exit;
    }

    $server = array("id" => uniqid(),
                    "enabled" => $enabled,
                    "conn_method" => $conn_method,
                    "ad_account_suffix" => $account_suffix,
                    "ad_domain_controllers" => $domain_controllers,
                    "base_dn" => $base_dn,
                    "security_level" => $security_level,
                    "ldap_port" => $ldap_port,
                    "ldap_host" => $ldap_host);

    // Add server
    auth_server_add($server);

    // Success... display normal page
    display_page();
}

function process_edit_server()
{
    $server_id = grab_request_var('server_id', '');

    // Verify we have all the required info (based on LDAP or AD)
    $error = "";
    $enabled = grab_request_var("enabled", "");
    if (!empty($enabled)) { $enabled = 1; }

    $conn_method = grab_request_var("conn_method", "");
    $base_dn = grab_request_var("base_dn", "");
    $security_level = grab_request_var("security_level", "none");

    // AD Only
    $account_suffix = grab_request_var("account_suffix", "");
    $domain_controllers = grab_request_var("domain_controllers", "");

    // LDAP Only
    $ldap_host = grab_request_var("ldap_host", "");
    $ldap_port = grab_request_var("ldap_port", "");

    if ($conn_method == "ad") {

        // Verify AD sections required
        if (empty($account_suffix) || empty($base_dn) || empty($domain_controllers) || empty($security_level)) {
            $error = _("You must fill out all the Active Directory server information.");
        }

    } else if ($conn_method == "ldap") {

        // Verify LDAP sections required
        if (empty($ldap_host) || empty($ldap_port) || empty($base_dn) || empty($security_level)) {
            $error = _("You must fill out all the LDAP server information.");
        }

    } else {
        // Not a recognized connection method...
        $error = _("Unknown connection method specified.");
    }

    // Quit if error exists
    if (!empty($error)) {
        display_page(true, $error);
        exit;
    }

    // Update the server
    $server_new = array("enabled" => $enabled,
                        "conn_method" => $conn_method,
                        "ad_account_suffix" => $account_suffix,
                        "ad_domain_controllers" => $domain_controllers,
                        "base_dn" => $base_dn,
                        "security_level" => $security_level,
                        "ldap_port" => $ldap_port,
                        "ldap_host" => $ldap_host);

    // Auth server update
    auth_server_update($server_id, $server_new);

    // Success... display normal page
    display_page();
}

function process_delete_server()
{
    $server_id = grab_request_var('server_id', '');
    if (empty($server_id)) {
        $error = _("Must select a current server to remove.");
    }

    // Quit if error exists
    if (!empty($error)) {
        display_page(true, $error);
        exit;
    }

    // Remove auth server
    auth_server_remove($server_id);

    // Success... display normal page
    display_page();
}