<?php
//
// Outbound Check Transfer Settings
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

    // Default filters
    $outbound_data_host_name_filters = get_option("outbound_data_host_name_filters");
    if ($outbound_data_host_name_filters == "") {
        $outbound_data_host_name_filters = "/^localhost/\n/^127\\.0\\.0\\.1/";
    }

    // Get options
    $enable_outbound_data_transfer = checkbox_binary(grab_request_var("enable_outbound_data_transfer", get_option('enable_outbound_data_transfer')));
    $enable_nsca_output = checkbox_binary(grab_request_var("enable_nsca_output", get_option('enable_nsca_output')));
    $outbound_data_host_name_filters = grab_request_var("outbound_data_host_name_filters", $outbound_data_host_name_filters);
    $outbound_data_filter_mode = grab_request_var("outbound_data_filter_mode", get_option("outbound_data_filter_mode"));
    $enable_nrdp_output = checkbox_binary(grab_request_var("enable_nrdp_output", get_option('enable_nrdp_output')));

    $r = get_option("nsca_target_hosts");
    if ($r != "") {
        $nsca_target_hosts = unserialize($r);
    } else {
        $nsca_target_hosts = array();
    }
    $nsca_target_hosts = grab_request_var("nsca_target_hosts", $nsca_target_hosts);

    $r = get_option("nrdp_target_hosts");
    if ($r != "") {
        $nrdp_target_hosts = unserialize($r);
    } else {
        $nrdp_target_hosts = array();
    }
    $nrdp_target_hosts = grab_request_var("nrdp_target_hosts", $nrdp_target_hosts);

    do_page_start(array("page_title" => _('Outbound Check Transfer Settings')), true);
?>

    <h1><i class="fa fa-sign-in"></i> <?php echo _('Outbound Check Transfer Settings'); ?></h1>
        <p>
        <?php echo sprintf(_("These settings affect %s's ability to send host and service checks results to remote Nagios servers.  Enabling outbound checks is important in distributed monitoring environments."), get_product_name()); ?>
    </p>

    <?php display_message($error, false, $msg); ?>

    <form id="manageOptionsForm" method="post" action="<?php echo encode_form_val($_SERVER['PHP_SELF']); ?>">

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
                <li><a href="#tab-global"><i class="fa fa-globe"></i> <?php echo _("Global Options"); ?></a></li>
                <li><a href="#tab-nrdp">NRDP</a></li>
                <li><a href="#tab-nsca">NSCA</a></li>
            </ul>

            <div id="tab-global">

                <div class="checkbox" style="margin: 10px 0 20px 0;">
                    <label>
                        <input type="checkbox" name="enable_outbound_data_transfer" <?php echo is_checked($enable_outbound_data_transfer, 1); ?>>
                        <?php echo _("Enable outbound check transfers"); ?>
                    </label>
                </div>

                <h5 class="ul"><?php echo _("Global Data Filters"); ?></h5>
                <p><?php echo _("Filters allow you to optionally exclude (or only include) certain checks in outbound data based on various criteria.  Filters apply globally to data sent out via both NSCA and NRDP."); ?></p>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Filter Mode"); ?>:</label>
                        </td>
                        <td>
                            <select name="outbound_data_filter_mode" class="dropdown form-control">
                                <option value="exclude" <?php echo is_selected($outbound_data_filter_mode, "exclude"); ?>><?php echo _("Exclude matches"); ?></option>
                                <option value="include" <?php echo is_selected($outbound_data_filter_mode, "include"); ?>><?php echo _("Include matches (only)"); ?></option>
                            </select>
                            <div><?php echo _("The operating mode of any filter(s) you define."); ?></div>
                            <div><b><?php echo _("Exclude matches"); ?></b> <?php echo _("will send only data that <i>does not</i> match defined filter(s)."); ?></div>
                            <div><b><?php echo _("Include matches"); ?></b> <?php echo _("will send only that that <i>does</i> match defined filter(s)."); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Host Name Filters"); ?>:</label>
                        </td>
                        <td>
                            <p>
                                <div><?php echo _("Specify one or more regular expressions that match a defined host name pattern.  Specify each pattern/expression on a new line.  Slashes are required"); ?>.</div>
                                <div><?php echo _("Example"); ?>: <b>/^localhost/</b></div>
                            </p>
                            <textarea name="outbound_data_host_name_filters" class="form-control" style="width: 400px; height: 160px;"><?php echo encode_form_val($outbound_data_host_name_filters); ?></textarea>
                        </td>
                    </tr>
                </table>
    
            </div>

            <div id="tab-nrdp">

                <div class="checkbox" style="margin: 10px 0 20px 0;">
                    <label>
                        <input type="checkbox" name="enable_nrdp_output" <?php echo is_checked($enable_nrdp_output, 1); ?>>
                        <?php echo _("Enable NRDP outbound check transfers"); ?>
                    </label>
                </div>

                <h5 class="ul"><?php echo _("NRDP Settings"); ?></h5>

                <div style="min-width: 600px; width: 50%;">
                    <p><?php echo _("Fill out the IP address(es) of the host(s) that NRDP data should be sent to. You must supply an authentication token for each target."); ?></p>
                    <p><b><?php echo _("Important"); ?>:</b> <?php echo sprintf(_("Each target host must have NRDP installed and be configured with the corresponding token you specified above. Additionally, this %s server must be able to contact each remote host on port 80 TCP (HTTP) or 443 TCP (HTTPS) in order to access the NRDP API. You may have to open firewall ports to allow access."), get_product_name()); ?></p>
                </div>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt" style="width: 100px;">
                            <label><?php echo _("Target Hosts"); ?>:</label>
                        </td>
                        <td>
                            <table class="table table-condensed table-bordered table-striped table-auto-width">
                                <thead>
                                    <tr>
                                        <th><?php echo _("IP Address"); ?></th>
                                        <th><?php echo _("Method"); ?></th>
                                        <th><?php echo _("Authentication Token"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                for ($x = 0; $x < 3; $x++) {
                                    if (!array_key_exists($x, $nrdp_target_hosts)) {
                                        $nrdp_target_hosts[$x]["address"] = "";
                                        $nrdp_target_hosts[$x]["method"] = "https";
                                        $nrdp_target_hosts[$x]["token"] = "";
                                    }
                                    if (!array_key_exists("method", $nrdp_target_hosts[$x])) {
                                        $nrdp_target_hosts[$x]["method"] = "http";
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="text" size="20" name="nrdp_target_hosts[<?php echo $x; ?>][address]" value="<?php echo encode_form_val($nrdp_target_hosts[$x]["address"]); ?>" class="textfield form-control">
                                        </td>
                                        <td>
                                            <select name="nrdp_target_hosts[<?php echo $x; ?>][method]" class="form-control">
                                                <option value="http" <?php echo is_selected("http", $nrdp_target_hosts[$x]["method"]); ?>>HTTP</option>
                                                <option value="https" <?php echo is_selected("https", $nrdp_target_hosts[$x]["method"]); ?>>HTTPS</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" size="20" name="nrdp_target_hosts[<?php echo $x; ?>][token]" value="<?php echo encode_form_val($nrdp_target_hosts[$x]["token"]); ?>" class="textfield form-control">
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>
    
            <div id="tab-nsca">

                <div class="checkbox" style="margin: 10px 0 20px 0;">
                    <label>
                        <input type="checkbox" name="enable_nsca_output" <?php echo is_checked($enable_nsca_output, 1); ?>>
                        <?php echo _("Enable NSCA outbound check transfers"); ?>
                    </label>
                </div>

                <h5 class="ul"><?php echo _("NSCA Settings"); ?></h5>

                <div style="min-width: 600px; width: 50%;">
                    <p><?php echo _("Fill in the IP address(es) of the host(s) that NSCA data should be sent to."); ?></p>
                    <p><b><?php echo _("Important"); ?>: </b><?php echo sprintf(_("Each target host must be running NSCA and be configured with the same password and encryption method you specified above. Additionally, this %s server must be able to contact each remote host on port 5667 TCP in order to access NSCA. You may have to open firewall ports to allow access."), get_product_name()); ?></p>
                </div>

                <table class="table table-condensed table-no-border table-auto-width">
                    <tr>
                        <td class="vt">
                            <label><?php echo _("Target Hosts"); ?>:</label>
                        </td>
                        <td>
                            <table class="table table-condensed table-bordered table-striped table-auto-width">
                                <thead>
                                    <tr>
                                        <th><?php echo _("IP Address"); ?></th>
                                        <th><?php echo _("Encryption Method"); ?></th>
                                        <th><?php echo _("Password"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                for ($x = 0; $x < 3; $x++) {
                                    if (!array_key_exists($x, $nsca_target_hosts)) {
                                        $nsca_target_hosts[$x]["address"] = "";
                                        $nsca_target_hosts[$x]["encryption"] = "";
                                        $nsca_target_hosts[$x]["password"] = "";
                                    }
                                    if (array_key_exists($x, $nsca_target_hosts) && in_demo_mode() == true) {
                                        $nsca_target_hosts[$x]["password"] = "******";
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="text" size="20" name="nsca_target_hosts[<?php echo $x; ?>][address]" value="<?php echo encode_form_val($nsca_target_hosts[$x]["address"]); ?>" class="textfield form-control">
                                        </td>
                                        <td>
                                            <select name="nsca_target_hosts[<?php echo $x; ?>][encryption]" class="dropdown form-control">
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
                                                <option value="<?php echo $id; ?>" <?php echo is_selected($nsca_target_hosts[$x]["encryption"], $id); ?>><?php echo $title; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="password" size="20" name="nsca_target_hosts[<?php echo $x; ?>][password]" value="<?php echo encode_form_val($nsca_target_hosts[$x]["password"]); ?>" class="textfield form-control" <?php echo sensitive_field_autocomplete(); ?>>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
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

    // User pressed the cancel button
    if (isset($request["cancelButton"])) {
        header("Location: datatransfer.php");
		return;
    }

    // Send this action to audit log
    send_to_audit_log(_("User updated outbound check transfer settings"), AUDITLOGTYPE_CHANGE);

    // Check session
    check_nagios_session_protector();

    $errmsg = array();
    $errors = 0;

    // Get values
    $enable_outbound_data_transfer = checkbox_binary(grab_request_var("enable_outbound_data_transfer"));
    $enable_nsca_output = checkbox_binary(grab_request_var("enable_nsca_output"));
    $nsca_target_hosts = grab_request_var("nsca_target_hosts");
    $outbound_data_host_name_filters = grab_request_var("outbound_data_host_name_filters");
    $outbound_data_filter_mode = grab_request_var("outbound_data_filter_mode");
    $enable_nrdp_output = checkbox_binary(grab_request_var("enable_nrdp_output"));
    $nrdp_target_hosts = grab_request_var("nrdp_target_hosts");

    // Make sure we have requirements
    if (in_demo_mode() == true) {
        $errmsg[$errors++] = _("Changes are disabled while in demo mode.");
    }

    $targets = 0;
    foreach ($nsca_target_hosts as $id => $tharr) {
        $ip = grab_array_var($tharr, "address");
        $en = grab_array_var($tharr, "encryption");
        $pa = grab_array_var($tharr, "password");
        if (trim($ip) == "") {
            unset($nsca_target_hosts[$id]);
            continue;
        }
        
        if ($en == "")
            $errmsg[$errors++] = _("Missing encryption method for NSCA target host '") . $ip . "'";

        $targets++;
    }

    if ($enable_nsca_output == 1 && $targets == 0) {
        $errmsg[$errors++] = _("You must specify at least one target NSCA host.");
    }

    $targets = 0;
    foreach ($nrdp_target_hosts as $id => $tharr) {
        $ip = grab_array_var($tharr, "address");
        $to = grab_array_var($tharr, "token");
        $m = grab_array_var($tharr, "method");
        if (!in_array($m, array("http", "https"))) {
            $nrdp_target_hosts[$id]["method"] = "http";
        }
        if (trim($ip) == "") {
            unset($nrdp_target_hosts[$id]);
            continue;
        }
        if ($to == "")
            $errmsg[$errors++] = _("Missing token for NRDP target host '") . $ip . "'";

        $targets++;
    }

    if ($enable_nrdp_output == 1 && $targets == 0) {
        $errmsg[$errors++] = _("You must specify at least one target NRDP host.");
    }

    // Handle errors
    if ($errors > 0) {
        show_options(true, $errmsg);
    }

    // Update options
    set_option("enable_outbound_data_transfer", $enable_outbound_data_transfer);
    set_option("enable_nsca_output", $enable_nsca_output);
    set_option("nsca_target_hosts", serialize($nsca_target_hosts));
    set_option("outbound_data_host_name_filters", $outbound_data_host_name_filters);
    set_option("outbound_data_filter_mode", $outbound_data_filter_mode);
    set_option("enable_nrdp_output", $enable_nrdp_output);
    set_option("nrdp_target_hosts", serialize($nrdp_target_hosts));

    // Save NSCA options to the file(s)
    foreach ($nsca_target_hosts as $id => $tharr) {

        $address = grab_array_var($tharr, "address");
        $encryption = grab_array_var($tharr, "encryption");
        $password = grab_array_var($tharr, "password");

        if ($address == "") {
            continue;
        }

        $fname = "send_nsca-" . $address . ".cfg";

        if (!empty($password)) {
            $contents = "# CONFIGURED BY NAGIOS XI\npassword=" . $password . "\nencryption_method=" . $encryption . "\n";
        } else {
            $contents = "# CONFIGURED BY NAGIOS XI\n#password=\nencryption_method=" . $encryption . "\n";
        }

        file_put_contents("/usr/local/nagios/etc/" . $fname, $contents);
    };

    show_options(false, "Settings Updated");
}