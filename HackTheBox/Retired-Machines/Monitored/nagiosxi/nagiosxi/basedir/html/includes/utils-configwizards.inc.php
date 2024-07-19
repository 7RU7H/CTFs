<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
//CONFIG WIZARD  FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param string $name
 * @param null   $args
 *
 * @return bool
 */
function register_configwizard($name = "", $args = null)
{
    global $configwizards;

    if ($name == "")
        return false;

    if (!isset($configwizards)) {
        $configwizards = array();
    }

    $configwizards[$name] = $args;

    return true;
}


/**
 * @param string $name
 *
 * @return null
 */
function get_configwizard_by_name($name = "")
{
    global $configwizards;

    $configwizard = null;

    if ($name == "")
        return null;

    if (!array_key_exists($name, $configwizards))
        return null;
    $configwizard = $configwizards[$name];

    return $configwizard;
}


/**
 * Test if a Wizard is installed
 *
 * @param $name
 * @return bool
 */
function is_configwizard_installed($name)
{

    if (get_configwizard_by_name($name) != null)
        return true;

    return false;
}


/**
 * @param string $name
 * @param string $mode
 * @param        $inargs
 * @param        $outargs
 * @param        $result
 *
 * @return string
 */
function make_configwizard_callback($name = "", $mode = "", $inargs, &$outargs, &$result)
{

    // USE THIS FOR DEBUGGING!
    //return "<BR>NAME: $name, ID: $id, MODE: $mode, ARGS: ".serialize($args)."<BR>";

    $w = get_configwizard_by_name($name);
    if ($w == null)
        return "";

    $output = "";
    $extra = array();

    // Check for extra args being passed
    if (array_key_exists(CONFIGWIZARD_FUNCTION_ARGS, $w) && is_array($w[CONFIGWIZARD_FUNCTION_ARGS])) {
        $extra = $w[CONFIGWIZARD_FUNCTION_ARGS];
    }

    // run the  function
    if (array_key_exists(CONFIGWIZARD_FUNCTION, $w) && have_value($w[CONFIGWIZARD_FUNCTION]) == true) {
        $f = $w[CONFIGWIZARD_FUNCTION];
        if (function_exists($f)) {
            if (!empty($extra)) {
                $output = $f($mode, $inargs, $outargs, $result, $extra);
            } else {
                $output = $f($mode, $inargs, $outargs, $result);
            }
        } else {
            $output = "CONFIG WIZARD FUNCTION '" . $f . "' DOES NOT EXIST";
        }
    } // nothing to do...
    else
        return $output;

    return $output;
}


function clean_configwizard_request_vars()
{
    global $request;

    // clear some request variables that shouldn't be passed to wizards
    $clear = array(
        //"nextstep",
        "update",
        "submitButton",
        "cancelButton",
    );
    foreach ($clear as $c) {
        if (array_key_exists($c, $request))
            unset($request[$c]);
    }
}


/**
 * Called by a command subsystem
 *
 * @param null $args
 * @return int
 */
function install_configwizard($args = null)
{

    if ($args == null)
        return 0;
    if (!is_array($args))
        return 0;

    $wizard_dir = grab_array_var($args, "wizard_dir");
    $allow_restart = grab_array_var($args, "allow_restart", true);

    // pre-install script
    $preinstall_script = $wizard_dir . "/preinstall.sh";
    echo "CHECKING FOR PREINSTALL SCRIPT '" . $preinstall_script . "'...\n";
    if (file_exists($preinstall_script)) {

        echo "RUNNING PREINSTALL SCRIPT...\n";

        // make the script executable
        chmod($preinstall_script, 0755);

        // run the script
        system($preinstall_script, $retval);

        echo "PREINSTALL SCRIPT FINISHED. RESULT='$retval'\n";
        if (intval($retval) !== 0) {
            return $retval;
        }
    } else {
        echo "PREINSTALL SCRIPT DOES NOT EXIST.\n";
    }

    // process config xml file
    $cfgfile = $wizard_dir . "/config.xml";
    echo "PROCESSING CONFIG FILE $cfgfile\n";
    if (file_exists($cfgfile)) {
        $xml = simplexml_load_file($cfgfile);
        if ($xml) {
            //print_r($xml);
            if ($xml->logos) {
                foreach ($xml->logos->logo as $logo) {
                    echo "LOGO: \n";
                    //print_r($logo);
                    $logo_filename = '';
                    $logo_theme = '';
                    foreach ($logo->attributes() as $a => $b) {
                        echo "LOGO ATTRIBUTE $a => $b\n";

                        if ($a == "filename") {
                            $logo_filename = $b;
                        }
                        if ($a == "theme") {
                            $logo_theme = $b;
                        }
                    }

                    if (!empty($logo_filename)) {
                        $fname = $wizard_dir . "/logos/$logo_theme/$logo_filename";
                        $dest_dir = get_base_dir() . "/includes/components/nagioscore/ui/images/logos/$logo_theme/";
                        $dest = $dest_dir . $logo_filename;
                        echo "CHECKING FOR DIRECTORY: $dest_dir\n";
                        if (!is_dir($dest_dir)) {
                            echo "MAKING NEW DIRECTORY: $dest_dir\n";
                            mkdir($dest_dir);
                        }
                        if (file_exists($fname)) {
                            echo "COPYING LOGO: $fname\n";
                            echo "          TO: $dest\n";
                            copy($fname, $dest);
                        } else {
                            echo "LOGO NOT FOUND: $fname\n";
                        }
                    }
                }
            }
            if ($xml->plugins) {
                foreach ($xml->plugins->plugin as $plugin) {
                    echo "PLUGIN: \n";
                    foreach ($plugin->attributes() as $a => $b) {
                        echo "PLUGIN ATTRIBUTE $a => $b\n";

                        if ($a == "filename") {
                            $fname = $wizard_dir . "/plugins/" . $b;
                            $dest = "/usr/local/nagios/libexec/" . $b;
                            if (file_exists($fname)) {
                                echo "COPYING PLUGIN: $fname\n";
                                echo "             TO: $dest\n";
                                if (copy($fname, $dest)) {
                                    // make the plugin executable
                                    chmod($dest, 0755);
                                }
                            } else
                                echo "PLUGIN NOT FOUND: $fname\n";
                        }
                    }
                }
            }
            if ($xml->templates) {
                $restart_nagios = false;
                foreach ($xml->templates->template as $template) {
                    echo "TEMPLATE: \n";
                    foreach ($template->attributes() as $a => $b) {
                        echo "TEMPLATE ATTRIBUTE $a => $b\n";

                        if ($a == "filename") {
                            $fname = $wizard_dir . "/templates/" . $b;
                            $dest = "/usr/local/nagios/etc/import/configwizard-" . $b;
                            if (file_exists($fname)) {
                                echo "COPYING TEMPLATE: $fname\n";
                                echo "              TO: $dest\n";
                                if (copy($fname, $dest)) {
                                    $restart_nagios = true;
                                }
                            } else
                                echo "TEMPLATE NOT FOUND: $fname\n";
                        }
                    }
                }
                if ($restart_nagios == true) {
                    if ($allow_restart == true) {
                        echo "RESTARTING NAGIOS CORE...\n";
                        reconfigure_nagioscore();
                    } else
                        echo "SKIPPING NAGIOS CORE RESTART...\n";
                }
            }

        } else
            echo "BAD XML!\n";
    } else
        echo "CONFIG FILE DOESN'T EXIST\n";

    // post-install script
    $install_script = $wizard_dir . "/install.sh";
    echo "CHECKING FOR INSTALL SCRIPT '" . $install_script . "'...\n";
    if (file_exists($install_script)) {

        echo "RUNNING INSTALL SCRIPT...\n";

        // make the script executable
        chmod($install_script, 0755);

        // run the script
        system($install_script, $retval);

        echo "INSTALL SCRIPT FINISHED. RESULT='$retval'\n";
        return $retval;
    } else {
        echo "INSTALL SCRIPT DOES NOT EXIST.\n";
    }

    return 0;
}


function get_all_configwizards_needing_upgrade()
{
    global $configwizards;
    global $configwizards_api_versions;
    $needs_upgrade = array();

    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'configwizards_api_versions.xml';
    if (file_exists($xmlcache)) {
        $configwizards_api_versions = simplexml_load_file($xmlcache);
    }

    $p = dirname(__FILE__) . "/configwizards/";
    $subdirs = scandir($p);

    foreach ($subdirs as $sd) {

        if ($sd == '.' || $sd == '..' || $sd == '.svn' || $sd == 'configwizardhelper.inc.php') {
            continue;
        }

        $d = $p . $sd;

        if (is_dir($d)) {
            $cf = $d . "/$sd.inc.php";
            if (file_exists($cf)) {
                include_once($cf);
            
                $wizard_dir = basename($d);

                foreach ($configwizards as $wizard) {
                    if (isset($configwizards_api_versions->$wizard_dir)) {
                        $latest_version = strval($configwizards_api_versions->$wizard_dir->version);
                        if (version_compare($wizard[CONFIGWIZARD_VERSION], $latest_version, '<')) {
                            $wizard['latest_version'] = $latest_version;
                            $wizard['wizard_dir'] = $wizard_dir;
                            $wizard['download'] = strval($configwizards_api_versions->$wizard_dir->download);
                            $needs_upgrade[] = $wizard;

                            // Skip duplicates that were imported by the single config wizard
                            break;
                        }
                    }
                }

                $configwizards = array();
                reset($configwizards);
            }

        }
    }

    return $needs_upgrade;
}


function add_template($tpl)
{
    // Global or normal user template
    if ($tpl['global']) {

        $global_tpls = get_option('config_wizard_global_tpls', array());
        if (!empty($global_tpls)) {
            $global_tpls = xiunserialize(base64_decode($global_tpls));
        }

        $new = array();
        foreach ($global_tpls as $t) {
            $new[] = $t;
        }

        $new[] = $tpl;
        set_option('config_wizard_global_tpls', base64_encode(json_encode($new)));

    } else {

        $user_tpls = get_user_meta(0, 'config_wizard_tpls', array());
        if (!empty($user_tpls)) {
            $user_tpls = xiunserialize(base64_decode($user_tpls));
        }

        $new = array();
        foreach ($user_tpls as $t) {
            $new[] = $t;
        }

        $new[] = $tpl;
        set_user_meta(0, 'config_wizard_tpls', base64_encode(json_encode($new)));

    }

    return true;
}


function get_templates($id = '')
{
    // Get the user's templates
    $user_tpls = get_user_meta(0, 'config_wizard_tpls', array());
    if (!empty($user_tpls)) {
        $user_tpls = xiunserialize(base64_decode($user_tpls));
    }

    // Get global templates
    $global_tpls = get_option('config_wizard_global_tpls', array());
    if (!empty($global_tpls)) {
        $global_tpls = xiunserialize(base64_decode($global_tpls));
    }

    if (!empty($id)) {
        foreach (array_merge($user_tpls, $global_tpls) as $tpl) {
            if ($tpl['id'] == $id) {
                return $tpl;
            }
        }
    }

    $tpls = array('user' => $user_tpls, 'global' => $global_tpls);
    return $tpls;
}


function xiunserialize($input) {
    $tmp = json_decode($input, true);
    if (!is_array($tmp)) {
        $tmp = unserialize($input);
    }
    return $tmp;
}


function update_template($tpl, $global=false)
{
    if ($global) {

        $global_tpls = get_option('config_wizard_global_tpls', array());
        if (!empty($global_tpls)) {
            $global_tpls = xiunserialize(base64_decode($global_tpls));
        }

        $new = array();
        foreach ($global_tpls as $t) {
            if ($t['id'] == $tpl['id']) {
                $new[] = $tpl;
                continue;
            }
            $new[] = $t;
        }

        set_option('config_wizard_global_tpls', base64_encode(json_encode($new)));

    } else {

        $user_tpls = get_user_meta(0, 'config_wizard_tpls', array());
        if (!empty($user_tpls)) {
            $user_tpls = xiunserialize(base64_decode($user_tpls));
        }

        $new = array();
        foreach ($user_tpls as $t) {
            if ($t['id'] == $tpl['id']) {
                $new[] = $tpl;
                continue;
            }
            $new[] = $t;
        }

        set_user_meta(0, 'config_wizard_tpls', base64_encode(json_encode($new)));

    }

    return true;
}


function delete_template($id)
{
    if (is_admin()) {
        $global_tpls = get_option('config_wizard_global_tpls', array());
        if (!empty($global_tpls)) {
            $global_tpls = xiunserialize(base64_decode($global_tpls));
        }
        $new_tpls = array();
        foreach ($global_tpls as $tpl) {
            if ($tpl['id'] != $id) {
                $new_tpls[] = $tpl;
            }
        }
        if (count($new_tpls) != count($global_tpls)) {
            $new_tpls = base64_encode(json_encode($new_tpls));
            set_option('config_wizard_global_tpls', $new_tpls);
        }
    }
        
    $user_tpls = get_user_meta(0, 'config_wizard_tpls', array());
    if (!empty($user_tpls)) {
        $user_tpls = xiunserialize(base64_decode($user_tpls));
    }
    $new_tpls = array();
    foreach ($user_tpls as $tpl) {
        if ($tpl['id'] != $id) {
            $new_tpls[] = $tpl;
        }
    }
    if (count($user_tpls) != count($new_tpls)) {
        $new_tpls = base64_encode(json_encode($new_tpls));
        set_user_meta(0, 'config_wizard_tpls', $new_tpls);
    }

    return true;
}