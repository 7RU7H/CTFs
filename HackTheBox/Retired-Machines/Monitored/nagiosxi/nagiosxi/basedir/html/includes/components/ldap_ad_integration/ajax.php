<?php
//
// Active Directory Integration
// Copyright (c) 2011-2023 Nagios Enterprises, LLC. All rights reserved.
//
// Functions:
// get_certificates()       - returns certificates?
// get_certificate_info()   - parses and validates $request['cert']
// add_certificate()        - validates $request['cert'] and adds cert to system
// delete_certificate()     - deletes certificate $request['cert_id']
// get_ldap_ad_server()     - checks if $request['server_id'] among DB 'ldap_ad_integration_component_servers' and returns server
// get_users()              - gets XI users from database

require_once(dirname(__FILE__).'/../../common.inc.php');
require_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/ldap_ad_integration.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs and authentication
grab_request_vars();
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (is_admin() == false) {
    echo _("You do not have access to this section.");
    exit();
}

route_request();

function route_request()
{
    $cmd = grab_request_var('cmd', '');

    switch ($cmd)
    {
        case 'getcerts':
            get_certificates();
            break;

        case 'getcertinfo':
            get_certificate_info();
            break;

        case 'addcert':
            add_certificate();
            break;

        case 'delcert':
            delete_certificate();
            break;

        case 'getserver':
            get_ldap_ad_server();
            break;

        case 'getxiusers':
            get_xi_users();
            break;

        default:
            break;
    }
}

// validates certificate
function get_certificate_info()
{
    $cert = grab_request_var('cert', '');

    $info = openssl_x509_parse($cert);
    if ($info === false) {
        $output = array("message" => _("Certificate is not valid."),
                        "error" => 1);
    } else {
        $output = array("certinfo" => $info['subject'],
                        "error" => 0);
    }

    print @json_encode($output);
}

/**
 * Adds certificate to the proper directory
 */
function add_certificate()
{
    global $cfg;

    $cert = grab_request_var('cert', '');
    $info = openssl_x509_parse($cert);
    if ($info === false) {
        $output = array("message" => _("Certificate is not valid."),
                        "error" => 1);
        print json_encode($output);
        return;
    }

    $hostname = $info['subject']['CN'];
    $issuer = $info['issuer']['CN'];
    $id = uniqid();

    // Check which dir to use
    $certs_dir = "/etc/ldap/certs";
    $cacerts_dir = "/etc/ldap/cacerts";
    if (!file_exists($certs_dir)) {
        $certs_dir = "/etc/openldap/certs";
        $cacerts_dir = "/etc/openldap/cacerts";
    }

    // Place the cert into the proper area (/etc/openldap/cacerts)
    $cert_file = "$certs_dir/$id.crt";
    $pem_file = "$certs_dir/$id.pem";
    $pem_link = "$cacerts_dir/$id.pem";
    file_put_contents($cert_file, $cert);
    shell_exec("openssl x509 -in $cert_file -text > $pem_file;");
    shell_exec("ln -s $pem_file $pem_link");

    // get openssl version
    preg_match('/(?<=OpenSSL\s)[0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2}/',shell_exec("openssl version"), $OpenSSLversion);
    $OpenSSLversion = (is_array($OpenSSLversion)) ? $OpenSSLversion[0] : $OpenSSLversion;

    // check if openssl version has rehash function (not c_rehash)
    function hasRehash($opensslVersion){
        $opensslVersion = explode(".", $opensslVersion);
        if($opensslVersion[0] > 1){                                                             // openssl version 3.X.X
            return true;
        } else if($opensslVersion[0] == 1 && $opensslVersion[1] > 0 && $opensslVersion[2] > 0){ // openssl version 1.1.1+
            return true;
        } else {                                                                                // openssl version 1.0.X-
            return false;
        }
    }

    // create hash symlinks
    if(!hasRehash($OpenSSLversion)){ // no rehash, we have to do it manually
        // if certificate already exists, remove existing symlinks, cert_file and pem_file
        $file_hash = trim(shell_exec("openssl x509 -noout -hash -in $pem_file"));
        if ($dh = opendir($cacerts_dir)) {
            while (false !== ($entry = readdir($dh))) {
                // if there is a duplicate certificate, exit
                if (("$certs_dir/$entry" != $pem_file) && (file_get_contents($cacerts_dir."/".$entry) == file_get_contents($pem_file))){
                    shell_exec("rm $pem_file $cert_file $pem_link");
                    $output = array("message" => _("This certificate has already been added."),
                                    "error" => 1);
                    print json_encode($output);
                    closedir($dh);
                    return;

                }
            }
            closedir($dh);
        }

        // create cacerts hash symlink with extension to not overwrite duplicate hashes
        $hash_duplicate_no = 0;
        while(file_exists("$cacerts_dir/$file_hash.$hash_duplicate_no")){
            //increment extension until file doesn't exist (XXXXXX.0 -> XXXXXX.1 -> ...)
            $hash_duplicate_no += 1;
        }
        shell_exec("cd $cacerts_dir; ln -s $pem_file $file_hash.$hash_duplicate_no;");
    } else { // has rehash, use rehash
        // rehash creates cert hash symlinks in cacerts_dir
        shell_exec("openssl rehash ".$cacerts_dir);
    }

    // Get the list of certificates already installed
    $certs = get_option('active_directory_component_ca_certs');
    if (empty($certs)) {
        $certs = array();
    } else {
        $certs = unserialize(base64_decode($certs));
    }

    $data = array("id" => $id,
                  "host" => $hostname,
                  "issuer" => $issuer,
                  "cert_file" => $cert_file,
                  "pem_file" => $pem_file,
                  "valid_from" => $info['validFrom_time_t'],
                  "valid_to" => $info['validTo_time_t']);

    // Save data into the certs option
    $certs[] = $data;
    $encoded = base64_encode(serialize($certs));
    set_option('active_directory_component_ca_certs', $encoded);

    // Send cmdsubsys command to restart apache
    submit_command(COMMAND_RESTART_HTTP);

    // Restart php-fpm if we need to
    $xisys = $cfg['root_dir'] . '/var/xi-sys.cfg';
    $ini = parse_ini_file($xisys);
    if ($ini['dist'] == 'el8') {
        submit_command(COMMAND_RESTART_PHP_FPM);
    }

    $output = array("message" => _("The certificate was added successfully."),
                    "error" => 0);
    print json_encode($output);
}

// deletes certificate $request['cert_id']
function delete_certificate()
{
    $cert_id = grab_request_var('cert_id', '');
    if (empty($cert_id)) {
        $output = array("message" => _("Must pass a valid certificate ID."),
                        "error" => 1);
        print json_encode($output);
    }

    // Get all the certs
    $certs = get_option('active_directory_component_ca_certs');
    if (empty($certs)) {
        $certs = array();
    } else {
        $certs = unserialize(base64_decode($certs));
    }

    // Loop through all the certificates and remove it
    $remove = array();
    $new_certs = array();
    if (count($certs) > 0) {
        foreach ($certs as $cert) {
            if ($cert['id'] != $cert_id) {
                $new_certs[] = $cert;
            } else {
                $remove = $cert;
            }
        }
    }

    // Check which dir to use
    $cacerts_dir = "/etc/ldap/cacerts";
    if (!file_exists($cacerts_dir)) {
        $cacerts_dir = "/etc/openldap/cacerts";
    }

    // Check which symlinks to remove
    $removelinksarr = array();
    $links = glob($cacerts_dir.'/*');
    foreach($links as $link){
        if(is_link($link)){
            $linktarget = realpath($link);

            if($linktarget == $remove['pem_file']){
                array_push($removelinksarr, $link);
            }
        }
    }
    $removelinks = implode(" ", $removelinksarr);

    // Remove the cert from the filesystem
    if (!empty($remove)) {
        shell_exec("rm ".$removelinks.";");
        shell_exec("rm -f ".$remove['cert_file']." ".$remove['pem_file'].";");
    }

    $encoded = base64_encode(serialize($new_certs));
    set_option('active_directory_component_ca_certs', $encoded);
    return;
}

// gets certificates from DB
function get_certificates()
{
    $certs = get_option('active_directory_component_ca_certs');
    if (empty($certs)) {
        $certs = array();
    } else {
        $certs = unserialize(base64_decode($certs));
    }

    // Return list of certs as JSON object
    print json_encode($certs);
}

// checks if $request['server_id'] in DB and returns server
function get_ldap_ad_server()
{
    $server_id = grab_request_var('server_id', '');
    $servers = get_option('ldap_ad_integration_component_servers');
    if (!empty($servers)) {
        $servers = unserialize(base64_decode($servers));
    } else {
        return;
    }

    // Check for server id in all servers
    foreach ($servers as $server) {
        if ($server['id'] == $server_id) {
            // Found the server, return it in JSON
            print json_encode($server);
        }
    }
}

// gets XI users from DB
function get_xi_users()
{
    $sql = "SELECT * FROM xi_users WHERE TRUE ORDER BY xi_users.email ASC";
    $rs = exec_sql_query(DB_NAGIOSXI, $sql);

    $users = array();
    foreach ($rs as $user) {
        $users[] = $user['username'];
    }

    print json_encode($users);
}