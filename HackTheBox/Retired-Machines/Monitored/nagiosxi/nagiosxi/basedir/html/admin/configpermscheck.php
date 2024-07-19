<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization
pre_init();
init_session();
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    echo _("You do not have access to this section.");
    exit();
}

// No cloud license access
if (is_v2_license_type('cloud')) {
    header("Location: main.php");
    exit();
}


route_request();


function route_request()
{
    check_perms();
    show_perms();
    exit();
}


function check_perms()
{

    $error = false;

    $errmsg = array();

    nagiosql_check_setuid_files($result, $goodscripts, $badscripts);
    if ($result == false) {
        $errmsg[] = _("One or more config scripts have problems.");
        $error = true;
    }

    nagiosql_check_file_perms($result, $goodfiles, $badfiles);
    if ($result == false) {
        $errmsg[] = _("One or more config files have problems.");
        $error = true;
    }

    if ($error == true) {
        show_perms(true, $errmsg);
        exit();
    }
}


/**
 * @param $perms
 *
 * @return string
 */
function get_fperm_info($perms)
{

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x') :
        (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x') :
        (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x') :
        (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}


/**
 * @param bool   $error
 * @param string $msg
 */
function show_perms($error = false, $msg = "")
{


    nagiosql_check_setuid_files($scripts_ok, $goodscripts, $badscripts);
    nagiosql_check_file_perms($config_ok, $goodfiles, $badfiles);


    do_page_start(array("page_title" => _("Config File Permissions Check")), true);

    ?>


    <h1><?php echo _("Config File Permissions Check"); ?></h1>


    <?php
    display_message($error, false, $msg);
    ?>

    <h5 class="ul"><?php echo _("Config Scripts"); ?></h5>

    <?php
    if ($scripts_ok == true) {
        echo "<p><img src='" . theme_image("ok_small.png") . "'> " . sprintf(_('The permissions on the %s configuration scripts appear to be okay.'), get_product_name()) . "</p>";
    } else {
        echo "<p><img src='" . theme_image("error_small.png") . "'> " . _('The following configuration scripts have incorrect permissions and owner information') . ":</p>";
        echo "<ul>";
        foreach ($badscripts as $bs) {

            $owner = posix_getpwuid(fileowner($bs));
            $fperms = fileperms($bs);

            echo "<li>" . $bs . " (OWNER=" . $owner["name"] . ", PERMS=" . get_fperm_info($fperms) . ")</li>";
        }
        echo "</ul>";
        echo "<p>" . _('Each of these scripts needs to be installed setuid root') . ".  <b>" . _('To fix this problem, follow these steps') . ":</b></p>";
        echo "<ul>";
        echo "<li>" . sprintf(_('Login to your %s server via SSH as the'), get_product_name()) . " <i>root</i> " . _('user') . "</li>";
        echo "<li>" . _('Execute the following commands') . ":";
        echo "<ul>";
        foreach ($badscripts as $bs) {
            echo "<li><i>chown root:nagios " . $bs . "</i></li>";
            echo "<li><i>chmod u+s " . $bs . "</i></li>";
        }
        echo "</ul>";
        echo "</li>";
        echo "</ul>";
    }

    ?>

    <h5 class="ul"><?php echo _("Config Files"); ?></h5>


    <?php
    if ($config_ok == true) {
        echo "<p><img src='" . theme_image("ok_small.png") . "'> " . _("The permissions on the Nagios Core configuration files to be okay") . ".</p>";
    } else {
        echo "<p><img src='" . theme_image("error_small.png") . "'> " . _("The following configuration files have incorrect permissions") . ":</p>";
        echo "<ul>";
        foreach ($badfiles as $bf) {

            $owner = posix_getpwuid(fileowner($bf));
            $group = posix_getgrgid(filegroup($bf));
            $fperms = fileperms($bf);

            echo "<li>" . $bf . " (OWNER=" . $owner["name"] . ", GROUP=" . $group["name"] . ", PERMS=" . get_fperm_info($fperms) . ")</li>";
        }
        echo "</ul>";
        echo "<p>" . _("Each of these config files needs to be writable by the <i>apache</i> and <i>nagios</i> users. <b>To fix this problem, follow these steps") . ":</b></p>";
        echo "<ul>";
        echo "<li>" . sprintf(_("Login to your %s server via SSH as the <i>root</i> user"), get_product_name()) . "</li>";
        echo "<li>" . _("Execute the following commands:");
        echo "<ul>";
        echo "<li><i>/usr/local/nagiosxi/scripts/reset_config_perms.sh</i></li>";
        echo "</ul>";
        echo "</li>";
        echo "</ul>";
    }

    ?>

    <?php

    do_page_end(true);
    exit();
}
