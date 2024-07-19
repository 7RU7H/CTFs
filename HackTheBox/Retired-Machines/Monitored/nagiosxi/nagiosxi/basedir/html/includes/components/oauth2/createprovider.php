<?php
/**
 * createprovider.php
 * creates a new provider class from the provider name and OAuth credentials
 * 
 * Functions:
 * - getProvider($providerName, $params) - returns a new provider class
 * 
 * Blame target:    BB
 * Date:            2023-04
 * Version:         1.0.0
 */

/**
 * Aliases for installed League Provider Classes
 * Make sure you have added these to your composer.json and run `composer install`
 * Plenty to choose from here:
 * @see https://oauth2-client.thephpleague.com/providers/league/
 * @see https://oauth2-client.thephpleague.com/providers/thirdparty/
 */
//@see https://github.com/thephpleague/oauth2-client -- GenericProvider
use League\OAuth2\Client\Provider\GenericProvider;
//@see https://github.com/thephpleague/oauth2-google
use League\OAuth2\Client\Provider\Google;
//@see https://github.com/stevenmaguire/oauth2-microsoft
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
//@see https://github.com/greew/oauth2-azure-provider
use TheNetworg\OAuth2\Client\Provider\Azure;

//if php version 7.4 or greater, require_once
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    require_once 'vendor/nagios-autoload.php';
}

//set up existing provider class from namespace
function getProvider($providerName, $params){
    $provider = null;
    switch ($providerName) {
        case 'google':
            $provider = new Google($params);
            break;
        case 'azure':
            $provider = new Azure($params);
            $provider->defaultEndPointVersion = Azure::ENDPOINT_VERSION_2_0;
            break;
        /*
        * Custom provider configuration in cases here
        */
        case 'generic':
            $provider = new GenericProvider($params);
            break;
        default:
            exit(_("provider ".$providerName." not found"));
    }
    return $provider;
}

class Nonce {
    public function generateSalt($length = 10){
        $chars='1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $char_len = strlen($chars)-1;
        $output = '';
        while (strlen($output) < $length) {
            $output .= $chars[ rand(0, $char_len) ];
        }
        return $output;
    }
}