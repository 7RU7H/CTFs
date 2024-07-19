<?php
//
// Inbound Check Transfer Settings
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;

    if (isset($request['update'])) {
        do_update_options();
    } else {
        show_options();
    }
}


function show_options($error = false, $msg = "")
{
    global $cfg;

    // No nrdp token yet, so generate on
    if (get_option('inbound_nrdp_tokens') == "") {
        $inbound_nrdp_tokens = random_string(12);        
        save_nrdp_token($inbound_nrdp_tokens);
    }

    // Get options
    $nsca_password = grab_request_var("nsca_password", get_option('nsca_password'));
    $nsca_encryption_method = grab_request_var("nsca_encryption_method", get_option('nsca_encryption_method'));
    $have_configured_nsca = checkbox_binary(grab_request_var("have_configured_nsca", get_option('have_configured_nsca', 0)));
    $inbound_nrdp_tokens = grab_request_var("inbound_nrdp_tokens", get_option('inbound_nrdp_tokens'));

    if (in_demo_mode() == true) {
        $nsca_password = "******";
        $inbound_nrdp_tokens = "******";
    }

    $url = get_base_uri(true, true) . "/nrdp/"; 

    do_page_start(array("page_title" => _('Inbound Check Transfer Settings')), true);
?>

    <h1><i class="fa fa-sign-in fa-flip-horizontal"></i> <?php echo _('Inbound Check Transfer Settings'); ?></h1>

    <p>
        <?php echo sprintf(_("These settings affect %s's ability to accept and process passive host and service check results from external applications, services, and remote Nagios servers. Enabling inbound checks is important in distributed monitoring environments, and in environments where external applications and services send data to Nagios."), get_product_name()); ?>
    </p>

    <?php display_message($error, false, $msg); ?>

    <form id="manageOptionsForm" method="post" action="dtinbound.php">

        <input type="hidden" name="options" value="1">
        <?php echo get_nagios_session_protector(); ?>
        <input type="hidden" name="update" value="1">

        <script type="text/javascript">
        $(function () {
            $("#tabs").tabs().show();
        });
        </script>

        <div id="tabs" class="hide">
            <ul>
                <li><a href="#tab-nrdp"><?php echo _("NRDP"); ?></a></li>
                <li><a href="#tab-nsca"><?php echo _("NSCA"); ?></a></li>
            </ul>

            <div id="tab-nrdp">

                <h5 class="ul"><?php echo _("NRDP Settings"); ?></h5>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Access Info"); ?>:</label>
                        </td>
                        <td>
                            <div>
                                <?php echo _("The NRDP API can be accessed at"); ?> <a href="<?php echo $url; ?>"><b><?php echo $url; ?></b></a>
                            </div>
                            <div>
                                <b><?php echo _("Note"); ?>:</b>
                                <?php echo _("Remote clients must be able to contact this server on port 80 TCP (HTTP) or 443 TCP (HTTPS) in order to access the NRDP API and submit check results.  You may have to open firewall ports to allow access."); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Authentication Tokens"); ?>:</label>
                        </td>
                        <td>
                            <p><?php echo _("One or more (alphanumeric) tokens that remote hosts and applications must use when contacting the NRDP API on this server.  Specify one token per line."); ?></p>
                            <textarea name="inbound_nrdp_tokens" class="form-control" style="width: 300px; height: 120px;"><?php echo encode_form_val($inbound_nrdp_tokens); ?></textarea>
                        </td>
                    </tr>
                </table>

            </div>
            <!--tab-nrdp-->
            <div id="tab-nsca">

                <h5 class="ul"><?php echo _("NSCA Settings"); ?></h5>

                <?php if ($have_configured_nsca != 1) { ?>
                    <div class="message" style="width: 600px;">
                        <ul class="errorMessage">
                            <li><i class="fa fa-exclamation-triangle"></i> <b><?php echo _("Configuration Required"); ?></b></li>
                            <li>
                                <p><?php echo _("Before you can enable inbound data transfer via NSCA, you must configure settings to allow external hosts/devices to communicate with NSCA.<br><br>To do this, follow these steps:"); ?></p>
                                <ol>
                                    <li><?php echo sprintf(_("Login to the %s server as the"), get_product_name()); ?> <i>root</i> <?php echo _("user"); ?></li>
                                    <li><?php echo _("Open the"); ?> <i>/etc/xinetd.d/nsca</i> <?php echo _("file for editing"); ?></li>
                                    <li><?php echo _("Modify the"); ?> <i>only_from</i> <?php echo _("statement to include the IP addresses of hosts that are allowed to send data (or comment it out to allow all hosts to send data)"); ?>
                                    </li>
                                    <li><?php echo _("Save the file"); ?></li>
                                </ol>
                            </li>
                            <div class="checkbox" style="margin: 10px 0 0 0;">
                                <label style="color: #000;">
                                    <input type="checkbox" value="1" name="have_configured_nsca"> <?php echo _("I have completed these steps."); ?>
                                </label>
                            </div>
                        </ul>
                    </div>
                <?php } ?>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Access Info"); ?>:</label>
                        </td>
                        <td>
                            <div><?php echo _("NSCA is configured to run on this machine on port"); ?> <b>5667 TCP</b>.</div>
                            <div><b><?php echo _("Note"); ?>:</b> <?php echo _("Remote clients must be able to contact this server on port 5667 TCP in order to access NSCA and submit check results.  You may have to open firewall ports to allow access."); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Decryption Method"); ?>:</label>
                        </td>
                        <td>
                            <select name="nsca_encryption_method" class="dropdown form-control">
                                <?php
                                $methods = array();
                                $methods[0] = "None (Not secure)";
                                $methods[1] = "XOR (Not secure)";
                                $methods[2] = "DES";
                                $methods[3] = "3DES";
                                $methods[4] = "CAST-128";
                                $methods[5] = "CAST-256";
                                $methods[6] = "xTEA";
                                $methods[7] = "3WAY";
                                $methods[8] = "BLOWFISH";
                                $methods[9] = "TWOFISH";
                                $methods[10] = "LOKI97";
                                $methods[11] = "RC2";
                                $methods[12] = "ARCFOUR";
                                $methods[14] = "RIJNDAEL-128";
                                $methods[15] = "RIJNDAEL-192";
                                $methods[16] = "RIJNDAEL-256";
                                $methods[19] = "WAKE";
                                $methods[20] = "SERPENT";
                                $methods[22] = "ENIGMA (Unix crypt)";
                                $methods[23] = "GOST";
                                $methods[24] = "SAFER64";
                                $methods[25] = "SAFER128";
                                $methods[26] = "SAFER+";

                                foreach ($methods as $id => $title){
                                ?>
                                <option value="<?php echo $id; ?>" <?php echo is_selected($nsca_encryption_method, $id); ?>><?php echo $title; ?></option>
                                <?php } ?>
                            </select>
                            <div><?php echo _("The decryption method used on check data that is received via NSCA"); ?>.</div>
                            <div><b><?php echo _("Important"); ?>:</b> <?php echo _("Each sender must be using the same encryption method as you specify for the decryption method here."); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Password"); ?>:</label>
                        </td>
                        <td>
                            <input type="password" size="20" name="nsca_password" value="<?php echo encode_form_val($nsca_password); ?>" class="textfield form-control" <?php echo sensitive_field_autocomplete(); ?>>
                            <div><?php echo _("The password used to decrypt check data that is received by NSCA."); ?></div>
                            <div><b><?php echo _("Important"); ?>:</b> <?php echo _("Each sender must be using this same password."); ?></div>
                        </td>
                    <tr>
                </table>

            </div>

        </div>

        <div id="formButtons">
            <button type="submit" class="btn btn-sm btn-primary" name="updateButton" id="updateButton"><?php echo _('Update Settings'); ?></button>
            <button type="submit" class="btn btn-sm btn-default" name="cancelButton" id="cancelButton"><?php echo _('Cancel'); ?></button>
        </div>

    </form>

    <?php
    do_page_end(true);
    exit();
}


function do_update_options()
{
    global $request;

    // user pressed the cancel button
    if (isset($request["cancelButton"])){
        header("Location: datatransfer.php");
		return;
	}

    // log it
    send_to_audit_log(_("User updated inbound check transfer settings"), AUDITLOGTYPE_CHANGE);

    // check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // get values
    $nsca_password = grab_request_var("nsca_password");
    $nsca_encryption_method = grab_request_var("nsca_encryption_method");
    $inbound_nrdp_tokens = grab_request_var("inbound_nrdp_tokens");

    // make sure we have requirements
    if (in_demo_mode() == true)
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    $total_tokens = 0;
    //print_r($inbound_nrdp_tokens);
    $tokens = explode("\n", $inbound_nrdp_tokens);
    foreach ($tokens as $t) {
        //echo "TOKEN: $t<BR>";
        $token = addslashes(trim($t));
        if ($token == "")
            continue;
        $total_tokens++;
    }
    if ($total_tokens == 0)
        $errmsg[$errors++] = _("No NRDP tokens specified.");

    // handle errors
    if ($errors > 0) {
        flash_message($errmsg[0], FLASH_MSG_ERROR);
        header("Location: dtinbound.php");
        exit();
    }

    // update options
    set_option("nsca_password", $nsca_password);
    set_option("nsca_encryption_method", $nsca_encryption_method);

    if (!get_option("have_configured_nsca", 0)) {
        $have_configured_nsca = checkbox_binary(grab_request_var("have_configured_nsca"));
        set_option("have_configured_nsca", $have_configured_nsca);
    }
    
    

    // save NSCA options to file
    $contents = file_get_contents("/usr/local/nagios/etc/nsca.cfg");
    if (!empty($nsca_password))
         $contents2 = preg_replace('/#?password=.*/', 'password=' . $nsca_password, $contents);
    else
         $contents2 = preg_replace('/#?password=.*/', '#password=', $contents);
    $contents3 = preg_replace('/decryption_method=.*/', 'decryption_method=' . $nsca_encryption_method, $contents2);
    file_put_contents("/usr/local/nagios/etc/nsca.cfg", $contents3);

    save_nrdp_token($inbound_nrdp_tokens);
    
    

    flash_message(_("Settings have been updated."));
    header("Location: dtinbound.php");
}


function draw_menu()
{
    //$m=get_admin_menu_items();
    //draw_menu_items($m);
}

function save_nrdp_token($inbound_nrdp_tokens){
    set_option("inbound_nrdp_tokens", $inbound_nrdp_tokens);
    
    // save NRDP options to file
    $tokens = explode("\n", $inbound_nrdp_tokens);
    $token_str = "array(";
    foreach ($tokens as $t) {
        $token = trim($t);
        if ($token == "")
            continue;
        $token_str .= "\"" . addslashes($token) . "\",";
    }
    $token_str .= ");";

    $contents = file_get_contents("/usr/local/nrdp/server/config.inc.php");
    $match = "/\\\$cfg\\['authorized_tokens'\\]\s*=.*/";
    $replace = "\\\$cfg['authorized_tokens'] = $token_str";
    //$replace="REPLACED";
    $contents2 = preg_replace($match, $replace, $contents);
    /*
    echo "TOKENSTRING: $token_str<BR>";
    echo "MATCH: $match<BR>";
    echo "REPLACE: $replace<BR>";
    echo "NEWCONTENTS:<BR>";
    echo $contents2;
    exit;
    */
    file_put_contents("/usr/local/nrdp/server/config.inc.php", $contents2);
    
}
