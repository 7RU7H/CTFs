<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// COMPONENT FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $name
 * @param   null    $args
 * @return  bool
 */
function register_component($name = "", $args = null)
{
    global $components;

    if ($name == "")
        return false;

    if (!isset($components)) {
        $components = array();
    }

    $components[$name] = array(
        COMPONENT_DIRECTORY => "",
        COMPONENT_ARGS => $args,
    );

    // Check for required version flag - we don't actually change anything here, just return false
    // this is preserved for future use in components -bh
    if (!empty($args[COMPONENT_REQUIRES_VERSION])) {
        $req_version = $args[COMPONENT_REQUIRES_VERSION];
        if ($req_version != "" && is_numeric($req_version) && $req_version > get_product_release()) {
            return false;
        }
    }

    return true;
}


/**
 * @param   string  $name
 * @param   bool    $fullpath
 * @return  string
 */
function get_component_url_base($name = "", $fullpath = true)
{
    $url = get_base_url($fullpath) . "includes/components/" . $name;
    return $url;
}


/**
 * @param   string  $name
 * @return  string
 */
function get_component_dir_base($name = "")
{
    $url = get_base_dir() . "/includes/components/" . $name;
    return $url;
}


/**
 * Test is a component is installed
 *
 * @param $name
 * @return bool
 */
function is_component_installed($name)
{
    global $components;

    if (array_key_exists($name, $components))
        return true;

    return false;
}


/**
 * @param null $args
 *
 * @return int
 */
function install_component($args = null)
{

    if ($args == null)
        return 0;
    if (!is_array($args))
        return 0;

    $component_name = grab_array_var($args, "component_name");
    $component_dir = grab_array_var($args, "component_dir");

    echo "INSTALLING COMPONENT: $component_name\n";

    // post-install script
    $install_script = $component_dir . "/install.sh";
    echo "CHECKING FOR INSTALL SCRIPT " . $install_script . "\n";
    if (file_exists($install_script)) {

        echo "RUNNING INSTALL SCRIPT...\n";

        // make the script executable
        chmod($install_script, 0755);

        // run the script
        system($install_script, $retval);

        echo "INSTALL SCRIPT FINISHED. RESULT='$retval'\n";
        return $retval;
    }

    return 0;
}


function get_all_components_needing_upgrade()
{
    global $components;
    global $components_api_versions;
    $needs_upgrade = array();

    $tmp = get_tmp_dir() . "/";
    $xmlcache = $tmp . 'components_api_versions.xml';
    if (file_exists($xmlcache)) {
        $components_api_versions = simplexml_load_file($xmlcache);
    }

    $p = dirname(__FILE__) . "/components/";
    $subdirs = scandir($p);

    foreach ($subdirs as $g) {

        if ($g == '.' || $g == '..' || $g == '.svn' || $g == 'componenthelper.inc.php' || $g == "xicore") {
            continue;
        }

        $d = $p . $g;

        if (is_dir($d)) {
            $f = $d . "/$g.inc.php";
            if (file_exists($f)) {
                include_once($f);
            }
        }
    }

    foreach ($components as $k => $c) {
        if (!empty($c['args']['type'])) { if ($c['args']['type'] == 'core') continue; }
        print $k;
        if (version_compare($c['args']['version'], $components_api_versions->$k->version, '<')) {
            $needs_upgrade[$k] = $c;
        }
    }

    return $needs_upgrade;
}
