<?php

/**
 * File: oauth-ajaxhelper.php
 * 
 * This file handles various AJAX requests in relation to OAuth2
 *
 * Functions:
 * - route_request() - routes requests to the appropriate function
 * - update_templates()      - AJAX - updates the provider templates in /usr/local/nagiosxi/html/includes/components/oauth2/access-templates.json and echoes resulting templates
 * - get_providers()         - AJAX - echoes the list of providers
 * - get_providers_helper()  - helper function for get_providers(), returns the list of providers
 * - unset_secret()          - helper function for get_providers_helper() that removes the client secrets from the provider details
 * - delete_provider()       - AJAX - deletes a provider, echoes [status, new providers array]
 * - flash_alert()           - AJAX - echoes flashes an alert
 *
 * Blame target:    BB
 * Date:            2023-04
 * Version:         1.0.0
 * 
 * Copyright (c) 2023 Nagios Enterprises, LLC. All rights reserved.
 */

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/createprovider.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();
decode_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}

route_request();

function route_request()
{
    global $request;
    $cmd = grab_request_var('cmd', '');
    // echo json_encode($cmd);
    switch ($cmd) {
        case 'update_templates':
            update_templates();
            break;
        case 'get_specific_provider':
            get_specific_provider();
            break;
        case 'get_providers':
            get_providers();
            break;
        case 'get_provider_details':
            get_provider_details();
            break;
        case 'delete_provider':
            delete_provider();
            break;
        case 'flash_alert':
            flash_alert();
            break;
        case 'test_azure_mail_credentials':
            test_azure_mail_credentials();
            break;
        case 'get_mail_credentials':
            get_mail_credentials();
            break;
        default:
            break;
    }

    exit;
}

/* 
 * AJAX - Saves new template configuration to access-templates.json
 * 
 * @param array 'templates' -- updated array of templates
 * 
 * @echo json array - [status, contents of access-templates.json]
 */
function update_templates(){
    check_nagios_session_protector();
    $templates     = grab_request_var('templates', array());
    $templatesfile = 'access-templates.json';
    if(!empty($templates)){
        if(file_put_contents($templatesfile, json_encode($templates))){
            echo json_encode(['status' => 'success', 'templates' => file_get_contents($templatesfile)]);
            exit;
        }
        echo json_encode(['status' => 'failed, could not write to '.$templatesfile, 'templates' => file_get_contents($templatesfile)]);
        exit;
    }
    echo json_encode(['status' => 'failed, templates not sent', 'templates' => file_get_contents($templatesfile)]);
    exit;
}

/**
 * AJAX - Get specific provider details
 */
function get_specific_provider(){
    check_nagios_session_protector();
    $provider = grab_request_var('provider', '');

    $providerlist = get_providers_helper();

    if(!empty($providerlist[$provider])){
        echo json_encode(['status' => 'success', 'provider' => $providerlist[$provider]]);
        exit;
    }
    echo json_encode(['status' => 'failed, provider not found']);
}

/*
 * AJAX - Get array of providers from helper function
 * 
 * @echo json array $provider_array -- array of providers found in /usr/local/nagiosxi/etc/components/oauth2/providers/
 */
function get_providers(){
    check_nagios_session_protector();
    echo json_encode(get_providers_helper());
    exit;
}

/*
 * Get array of providers from .json files in /usr/local/nagiosxi/etc/components/oauth2/providers/
 * 
 * @return array $provider_array -- array of providers found in /usr/local/nagiosxi/etc/components/oauth2/providers/
 */
function get_providers_helper(){
    // load providers from /usr/local/nagiosxi/etc/components/oauth2/providers/
    $provider_array = [];
    if($handle = opendir('/usr/local/nagiosxi/etc/components/oauth2/providers/')){
        while(FALSE !== ($entry = readdir($handle))){
            if(is_file('/usr/local/nagiosxi/etc/components/oauth2/providers/'.$entry)){
                $provider_array[substr($entry, 0, -5)] = json_decode(decrypt_data(file_get_contents('/usr/local/nagiosxi/etc/components/oauth2/providers/'.$entry)), true);
            }
        }
    }
    
    unset_secrets($provider_array);
    array_walk_recursive($provider_array, 'encode_form_val');
    return $provider_array;
}

/*
 * Unset clientSecret from array
 * 
 * @param array &$array -- array to unset clientSecret from
 */
function unset_secrets(&$array) {
    unset($array['clientSecret']);
    foreach($array as &$value){
        if(is_array($value)){
            unset_secrets($value);
        }
    }
}

/*
 * AJAX - Delete provider credentials file
 * 
 * @param string 'credentialsname' -- name of provider credentials to delete
 * 
 * @echo json array -- [status of deletion, array of providers]
 */
function delete_provider(){
    check_nagios_session_protector();
    $credentialsname = grab_request_var('credentialsname', '');

    preg_match('/\.\.\//',$filepath,$matches);
    if(empty($matches)) { // not accessing a parent directory (security)
        if(!empty($credentialsname)){
            if(unlink('/usr/local/nagiosxi/etc/components/oauth2/providers/'.$credentialsname.'.json')){
                echo json_encode(['status' => 'success', 'providers' => get_providers_helper()]);
                exit;
            }
            echo json_encode(['status' => 'failed: could not delete file', 'providers' => get_providers_helper()]);
            exit;
        }
        echo json_encode(['status' => 'failed: empty credentials name', 'providers' => get_providers_helper()]);
        exit;
    }
    echo json_encode(['status' => 'failed: invalid path', 'providers' => get_providers_helper()]);
    exit;
}

/*
 * AJAX - echoes a flash message
 * 
 * @param {string} 'message' -- message to display
 * @param {string} 'type' -- type of message (success, info, warning, error)
 * 
 * @echo {string} $html -- html for flash message
 */
function flash_alert(){
    check_nagios_session_protector();
    $message = grab_request_var('message', '');
    $type    = grab_request_var('type', '');

    switch($type){
        case 'success':
            $type = FLASH_MSG_SUCCESS;
            break;
        case 'info':
            $type = FLASH_MSG_INFO;
            break;
        case 'warning':
            $type = FLASH_MSG_WARNING;
            break;
        case 'error':
            $type = FLASH_MSG_ERROR;
            break;
        default:
            $type = FLASH_MSG_INFO;
            break;
    }
    flash_message($message, $type);
    echo get_flash_message();
    exit;
}

/**
 * AJAX - Check if client credentials are valid
 * 
 * @echo json array -- [status, message]
 */
function test_azure_mail_credentials(){
    check_nagios_session_protector();

    // create provider class
    require_once 'createprovider.php';

    // grab vars from $request
    $providerName  = grab_request_var('provider',       '');
    $clientId      = grab_request_var('clientid',       '');
    $clientSecret  = grab_request_var('clientsecret',   '');
    $tenantId      = grab_request_var('tenantid',       '');

    $setMailMethod = grab_request_var('setmailmethod',  '');

    // set scopes
    $scopes = [];
    if($providerName == 'azure'){
        $scopes = [ 'https://graph.microsoft.com/.default' ];
    } else {
        $scopes = [];
    }
    
    $params = [
        'clientId'      => $clientId,
        'clientSecret'  => $clientSecret,
    ];
    if($tenantId != ''){ $params['tenantId'] = $tenantId; }

    try{
        $provider = getProvider($providerName, $params);
    } catch (Exception $e) {
        echo json_encode(['status' => 'failed', 'message' => $e->getMessage()]);
        exit;
    }

    try{
        $token = $provider->getAccessToken('client_credentials', ['scope' => implode(' ', $scopes)]);
        if(!$token->getToken()){
            echo json_encode(['status' => 'failed', 'message' => _('No token received.')]);
            exit;
        }        
        $credentials = [
            'provider'      => $providerName,
            'clientId'      => $clientId,
            'clientSecret'  => $clientSecret,
            'tenantId'      => $tenantId,
            'options'       => [
                'scope' => $scopes
            ]
        ];
        file_put_contents('/usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json', encrypt_data(json_encode($credentials)));
        if($setMailMethod == true){
            set_option("mail_method", "msGraphOA2");
        }
        echo json_encode(['status' => 'success', 'message' => 'token: '.$token->getToken()]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'failed', 'message' => $e->getMessage()]);
        exit;
    }    

    echo json_encode(['status' => 'failed', 'message' => _('Could not authenticate')]);
    exit;
}

/**
 * AJAX - Get mail credentials for client credentials flow
 */
function get_mail_credentials(){
    check_nagios_session_protector();
    
    try {
        if(!file_exists('/usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json')){
            echo json_encode(['status' => 'failed', 'message' => _('No credentials found')]);
            exit;
        }
        $credentials = json_decode(decrypt_data(file_get_contents('/usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json')), true);
        if(empty($credentials)){
            echo json_encode(['status' => 'failed', 'message' => _('Credentials empty')]);
            exit;
        }
        echo json_encode(['status' => 'success', 'credentials' => $credentials]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'failed', 'message' => $e->getMessage()]);
        exit;
    }
}