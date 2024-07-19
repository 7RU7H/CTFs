<?php

/** 
 * File: oauth-gmailsimplified-authcodeflow.php
 *
 * Description: This file is used to instantiate OAuth2 credentials for Nagios XI
 * It received clientId, clientSecret, redirectUri and eventually 
 * saving the credentials to /usr/local/nagiosxi/etc/components/oauth2/providers/NagiosXIGMailDefault.json
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
// VARIABLE HANDLING
//======================================================================

require_once 'createprovider.php';

$clientId = '';
$clientSecret = '';

$customName = 'NagiosXIGMailDefault';
$providerName = 'google';
$accessTemplate = 'smtpOAuth';

$scopes = [
    'https://mail.google.com/'
];

//set redirectUri to localhost if ip is private
$componentBaseUrl = get_component_url_base("oauth2");
preg_match("/(?<=\/\/).+?(?=\/)/", $componentBaseUrl, $ip);
if(preg_match("/[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}/",$ip[0],$ip)){ //ip is private, must use https or localhost
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') { //https, can use private ip (at least with Azure)
        $redirectUri = $componentBaseUrl;
        if($providername == 'google'){
            $googleRedirectUri = preg_replace("/(?<=\/\/)[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}?(?=\/)/","localhost",$componentBaseUrl); //google doesn't support private ips
        }
    } else { //http and not FQDN, must use localhost
        $redirectUri = preg_replace("/(?<=\/\/)[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}?(?=\/)/","localhost",$componentBaseUrl);
    }
} else { //ip is not private, can use domain name
    $redirectUri = $componentBaseUrl;
}
$redirectUri = $googleRedirectUri ?? $redirectUri;
$redirectUri = $redirectUri."/oauth-gmailsimplified-authcodeflow.php";

//note: $request already sanitized from grab_request_vars()
if (array_key_exists('clientId', $request)) {
    // set vars from mailsettings.php
    $clientId       = $request['clientId'];
    $clientSecret   = $request['clientSecret'];
    $redirectUri    = $redirectUri;

    $_SESSION['customName']     = $customName;
    $_SESSION['provider']       = $providerName;
    $_SESSION['clientId']       = $clientId;
    $_SESSION['clientSecret']   = $clientSecret;
    $_SESSION['redirectUri']    = $redirectUri;
    $_SESSION['scopes']         = $scopes;
    
} elseif (array_key_exists('provider', $_SESSION)) {
    $customName     = $_SESSION['customName'];
    $providerName   = $_SESSION['provider'];
    $clientId       = $_SESSION['clientId'];
    $clientSecret   = $_SESSION['clientSecret'];
    $redirectUri    = $_SESSION['redirectUri'];
    $scopes         = $_SESSION['scopes'];
} else { // clear everything if things didn't work out
    $customName     = '';
    $providerName   = '';
    $clientId       = '';
    $clientSecret   = '';
    $redirectUri    = '';
    $scopes         = [];
    $_SESSION['customName']     = '';
    $_SESSION['provider']       = '';
    $_SESSION['clientId']       = '';
    $_SESSION['clientSecret']   = '';
    $_SESSION['redirectUri']    = '';
    $_SESSION['scopes']         = [];
}

//If you don't want to use the built-in form/provider json files, you can set your details here
//$providerName = 'azure'; //or 'google' or 'microsoft'
//$clientId = 'RANDOMCHARS-----duv1n2.apps.googleusercontent.com';
//$clientSecret = 'RANDOMCHARS-----lGyjPcRtvP';
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
            echo_alert_close(_("Refresh Token is NULL. Note that certain providers may not supply a refresh token on subsequent requests and you may need to create a new secret."));
            exit;
        }
    }

    // Get provider data and add refresh token
    if(is_file("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp")){
        if($provider_data = json_decode(decrypt_data(file_get_contents("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp")), true)){
            unlink("/usr/local/nagiosxi/etc/components/oauth2/".$customName.".json.tmp");
            if(isset($refreshToken) && $refreshToken != null) { 
                $provider_data['refreshToken'] = $refreshToken; 
            }
        } else { echo_alert_close(_("Couldn't open ".$customName.".json")); }
    } else { echo_alert_close(_($customName.".json is not a file.")); }

    // Save provider data
    if(!empty($provider_data)) {
        if(isset($provider_data['refreshToken']) && $provider_data['refreshToken'] != null) { 
            if(file_put_contents("/usr/local/nagiosxi/etc/components/oauth2/providers/".$customName.".json", encrypt_data(json_encode($provider_data)))) { 
                set_option("mail_method", 'smtpOAuth');
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
        echo "<br><br><a class='btn btn-sm btn-default tt-bind' href='".get_base_url()."admin/?xiwindow=mailsettings.php'>"._("Go Back")."</a>";
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
    if($e->getMessage() == 'invalid_client'){
        echo '<pre>'._('Error: ').$e->getMessage().'<br></pre>';
        echo '<pre>'._("This error is usually caused by an invalid client secret. Please try again.").'<br></pre>';
    } else {
        echo '<pre>'._('Error: ').$e->getMessage().'<br></pre>';
        echo _("There was an error with your request. Please try again.")."<br><br>";
    }
    echo "<a class='btn btn-sm btn-default' href='".get_base_url()."admin/?xiwindow=mailsettings.php'>"._("Go Back")."</a>";
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
