<?php
//
// Copyright (c) 2008-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (is_admin() == false) {
    $content .= _("You are not authorized to access this feature.  Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}

// Load wizards after locale is set
require_once(dirname(__FILE__) . '/../../configwizards.inc.php');

route_request();

function route_request()
{
    $cmd = grab_request_var('cmd', '');

    switch ($cmd)
    {
        case 'show':
            show_profile(false);
            break;

        case 'download':
            show_profile(true);
            break;

        default:
            show_page();
            break;
    }
}

function show_page()
{
    do_page_start(array("page_title" => _("System Profile")), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    $('#show-profile').click(function() {
        whiteout();
        show_throbber();
        $.get('', { cmd: 'show' }, function(data) {
            hide_throbber();
            clear_whiteout();
            $('#profile').html(data);
        });
    });

});
</script>

<h1><?php echo _('System Profile'); ?></h1>

<p style="margin: 10px 0;"><?php echo _('A system profile makes it easier for our support techs to understand the system that you are running on. Including a downloaded system profile with your support ticket is always recommended.'); ?></p>

<div style="margin-bottom: 20px;">
    <button type="button" id="show-profile" class="btn btn-sm btn-primary"><?php echo _('View System Info'); ?></button>
    <a href="?cmd=download" class="btn btn-sm btn-default"><i class="fa fa-download"></i> <?php echo _('Download Profile'); ?></a>
</div>

<div id="profile"></div>

<?php
    do_page_end(true);
}

function show_profile($download = false)
{
    if ($download) {
        get_logs_and_snapshot();
    } else {
        echo build_profile_output();
    }
}

/**
 * @param $download
 *
 * @return string
 */
function build_profile_output()
{
    $content = "<h4>Nagios XI - System Info</h4>";

    // System
    $content .= show_system_settings();

    if (!in_demo_mode()) {

        // Server info
        $content .= show_apache_settings();

    }

    // Time info
    $content .= show_time_settings();

    if (!in_demo_mode()) {

        // XI Specific Data
        $content .= show_xi_info();

        // Subsystem calls
        $content .= run_subsystem_tests();

        // Network
        $content .= show_network_settings();

    }

    // Show wizard and component versions
    $content .= show_component_info();
    $content .= show_wizard_info();
    $content .= show_dashlet_info();

    return nl2br($content);
}


function show_component_info()
{
    global $components;

    $html = '<h5>Nagios XI Components</h5>';
    $html .= '<table>';

    foreach ($components as $comp) {
        $html .= '<tr>';
        $html .= '<td>' . $comp['args']['name'] . '</td>';
        $html .= '<td>' . @$comp['args']['version'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
}


function show_wizard_info()
{
    global $configwizards;

    $html = '<h5>Nagios XI Config Wizards</h5>';
    $html .= '<table>';

    foreach ($configwizards as $cfw) {
        $html .= '<tr>';
        $html .= '<td>' . $cfw['name'] . '</td>';
        $html .= '<td>' . @$cfw['version'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
}


function show_dashlet_info()
{
    global $dashlets;

    $html = '<h5>Nagios XI Dashlets</h5>';
    $html .= '<table>';

    foreach ($dashlets as $dash) {
        $html .= '<tr>';
        $html .= '<td>' . $dash['name'] . '</td>';
        $html .= '<td>' . @$dash['version'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
    
}


function show_network_settings()
{
    $network = "<h5>Network Settings</h5>";
    $network .= "<pre>" . shell_exec('ip addr') . "</pre>" . "\n";
    $network .= "<pre>" . shell_exec('ip route') . "</pre>" . "\n";
    
    return $network;
}


/**
 * @return string
 */
function show_system_settings()
{

    $profile = php_uname('n');
    $profile .= ' ' . php_uname('r');
    $profile .= ' ' . php_uname('m');
    @exec('which gnome-session 2>&1', $output, $gnome);

    $content = "<h5>System</h5>";
    $content .= "Nagios XI version: " . get_product_version() . "\n";
    $content .= "Release info: $profile\n";
    //detect distro and version
    $file = @file_get_contents('/etc/redhat-release');
    $oracle = @file_get_contents('/etc/oracle-release');
    if ($oracle)
        $file = $oracle;
    if (!$file)
        $file = @file_get_contents('/etc/fedora-release');
    if (!$file)
        $file = @file_get_contents('/etc/lsb-release');

    $content .= $file;
    $content .= ($gnome > 0) ? "Gnome is not installed\n" : " Gnome Installed\n";

    if (check_for_proxy()) $content .= "Proxy appears to be in use\n";

    return $content;

}


/**
 * @return string
 */
function show_apache_settings()
{
    $content = "<h5>Apache Information</h5>";
    $content .= "PHP Version: " . PHP_VERSION . "\n";
    $content .= "Agent: " . encode_form_val($_SERVER['HTTP_USER_AGENT']) . "\n";
    $content .= "Server Name: " . encode_form_val($_SERVER['SERVER_NAME']) . "\n";
    $content .= "Server Address: " . encode_form_val($_SERVER['SERVER_ADDR']) . "\n";
    $content .= "Server Port: " . encode_form_val($_SERVER['SERVER_PORT']) . "\n";
    return $content;
}


/**
 * @return string
 */
function show_time_settings()
{

    $php_tz = (ini_get('date.timezone') == '') ? 'Not set' : ini_get('date.timezone');
    $content = "<h5>Date/Time</h5>";
    $content .= "PHP Timezone: $php_tz \n";
    $content .= "PHP Time: " . date('r') . "\n";
    $content .= "System Time: " . exec('/bin/date -R') . "\n";
    return $content;
}


/**
 * @return string
 */
function show_xi_info()
{
    global $cfg;

    //systats
    $xml = get_xml_sysstat_data();
    $statdata = '';
    //daemons
    foreach ($xml->daemons->daemon as $d) {
        $statdata .= "{$d->output}\n";
    }

    // Host and service count
    $hostcount = get_active_host_license_count();
    $servicecount = get_active_service_license_count();
    
    // Last 6 of License
    $license_ends_with = substr(trim(get_license_key()), -6);

    //add to statdata string
    $statdata .= "CPU Load 15: {$xml->load->load15} \n";
    $statdata .= "Total Hosts: $hostcount \n";
    $statdata .= "Total Services: $servicecount \n\n";

    //content output
    $content = "<h5>Nagios XI Data</h5>";
    $content .= "License ends in: " . $license_ends_with . "\n";
    if (is_trial_license())
        $content .= "Days left in Trial: ". get_trial_days_left() . "\n";

    // Get UUID
    $uuid = '';
    $xi_uuid_file = $cfg['root_dir'] . '/var/xi-uuid';
    if (file_exists($xi_uuid_file)) {
        $uuid = trim(file_get_contents($xi_uuid_file));
    }
    $content .= "<div>UUID: " . $uuid . "</div>";

    // Get installation method
    $install_type = "manual/unknown";
    $install_type_file = $cfg['root_dir'] . '/var/xi-itype';
    if (file_exists($install_type_file)) {
        $install_type = trim(file_get_contents($install_type_file));
    }
    $content .= "<div>Install Type: " . $install_type . "</div>";

    $content .= "<br>";
    $content .= $statdata;

    // URL reference calls
    $content .= "Function get_base_uri() returns: " . get_base_uri() . "\n";
    $content .= "Function get_base_url() returns: " . get_base_url() . "\n";
    $content .= "Function get_backend_url(internal_call=false) returns: " . get_backend_url(false) . "\n";
    $content .= "Function get_backend_url(internal_call=true) returns: " . get_backend_url(true) . "\n\n";
    return $content;
}


/**
 * @return bool
 */
function check_for_proxy()
{

    $proxy = false;

    $f = @fopen('/etc/wgetrc', 'r');
    if ($f) {
        while (!feof($f)) {
            $line = fgets($f);
            if (!$line || $line[0] == '#') continue;
            if (strpos($line, 'use_proxy = on') !== FALSE) {
                $proxy = true;
                break;
            }
        }
    }

    $proxy_env = exec('/bin/echo $http_proxy');
    if (strlen($proxy_env > 0)) $proxy = true;
    return $proxy;

}


/**
 * @return string
 */
function run_subsystem_tests()
{
    global $cfg;

    //localhost ping resolve
    $content = "<h5>Ping Test localhost</h5>";
    $ping = '/bin/ping -c 3 localhost 2>&1';
    $content .= "Running: <pre>$ping </pre>";
    $handle = popen($ping, 'r');
    while (($buf = fgets($handle, 4096)) != false)
        $content .= $buf;

    pclose($handle);

    //get system info
    $https = grab_array_var($cfg, "use_https", false);
    $url = ($https == true) ? "https" : "http";
    //check for port #
    $port = grab_array_var($cfg, 'port_number', false);
    $port = ($port) ? ':' . $port : '';

    //CCM resolve
    $content .= "<h5>Test wget To localhost</h5>";
    $url .= "://localhost" . $port . get_component_url_base("ccm", false) . "/";
    $content .= "WGET From URL: $url \n";
    $content .= "Running: <pre>/usr/bin/wget $url </pre>";

    $handle = popen("/usr/bin/wget " . $url . ' -O ' . get_tmp_dir() . '/ccm_index.tmp 2>&1', 'r');
    while (($buf = fgets($handle, 2096)) != false)
        $content .= htmlentities($buf);

    pclose($handle);
    unlink(get_tmp_dir() . '/ccm_index.tmp');
    return $content;
}

function get_logs_and_snapshot()
{
    global $cfg;
    $id = uniqid();
    $folder = get_root_dir() . '/tmp/';

    if (in_demo_mode()) {
        flash_message(_("You cannot delete download a system profile in demo mode."), FLASH_MSG_ERROR);
        header("Location: profile.php");
        exit();
    }

    // Dump existing profile into file
    $profile = build_profile_output(true);

    // Put profile.html into the profile generation folder
    file_put_contents($folder . '/profile-'.$id.'.html', $profile);

    // Get logs and config snapshot
    $script_loc = $cfg['script_dir'] . '/components/getprofile.sh ' . $id;
    exec('sudo ' . $script_loc, $output, $code);

    // Make sure script finished successfully
    if ($code > 0) {
        echo "PROFILE BUILD FAILED<br />\n";
        array_dump($output);
        echo "CODE: $code<br />";
        unlink($folder . '/profile-'.$id.'.html');
        exit();
    }

    // Zip was packaged, send it to user
    $zip = get_root_dir() . "/var/components/profile.zip";

    // Verify we actually made a zip
    if (!file_exists($zip)) {
        echo "Failed to retrieve zip file!\n";
        exit();
    }

    ob_clean();
    $mime_type = "application/zip";
    header('Content-type: ' . $mime_type);
    header("Content-length: " . filesize($zip));
    header('Content-Disposition: attachment; filename="' . basename($zip) . '"');
    readfile($zip);
}
