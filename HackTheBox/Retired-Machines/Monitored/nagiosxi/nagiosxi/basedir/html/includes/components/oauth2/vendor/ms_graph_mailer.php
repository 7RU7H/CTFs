<?php
require_once(dirname(__FILE__).'/../createprovider.php');

/* 
 *  class for communicating with Graph API and authorizing via NagiosXI's OAuth2 component
 * 
 * @private $providerdetails    - array of provider credentials
 * @private $tenantId           - tenant ID for the provider
 * @private $clientId           - client ID for the provider
 * @private $clientSecret       - client secret for the provider
 * @private $scopes             - scopes for the given credentials
 * @private $accessToken        - access token for the provider
 * @private $refreshToken       - refresh token for the provider
 * @private $baseURL            - base URL for the Graph API
 * @private $baseLoginURL       - base URL for MS login
 * 
 * Functions:
 * __construct()                                              - initialize the class with the OAuth2 parameters from /usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json
 * refreshTokens($oauthFlow, $renewRefreshToken = true)       - refresh the access token and optionally the refresh token
 *      $oauthFlow                                                  - OAuth2 flow to use to get tokens (clientCredentials - directly with credentials, refreshToken - via refresh token (for Authorization Code flow))
 *      $renewRefreshToken                                          - renew the refresh token
 * createMessageJSON($messageArgs)                            - create the JSON for the message
 * sendMail($mailbox, $messageArgs, $deleteAfterSend = false) - send the message
 *      $mailbox                                                    - mailbox to send from (email address)
 *      $messageArgs                                                - array of message parameters (from createMessageJSON())
 *      $deleteAfterSend                                            - delete the message from sender's "Sent Mail" after sending
 * sendPostRequest($URL, $Fields, $Headers = false)           - send a POST request to the $URL with $Fields and optional $Headers
 * 
 * Blame target:    BB
 * Date:            2023-04
 * Version:         1.0.0
 */
class MSGraphMailer {

    private $providerdetails;

    private $tenantId;
    private $clientId;
    private $clientSecret;
    private $scopes;

    // flowType can be 'clientCredentials' or 'refreshToken'
    private $flowType;

    private $accessToken;
    private $refreshToken;

    private $defaultScope = 'https://graph.microsoft.com/.default';
    
    private $baseURL 	  = 'https://graph.microsoft.com/v1.0/';
    private $baseLoginURL = 'https://login.microsoftonline.com/';

    /*
     *  Initialize the class with the OAuth2 parameters from /usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json
     */
    function __construct($flow = 'clientCredentials') {
        //load credentials from file
        $encryptedCredentials = file_get_contents('/usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json');
        $jsonCredentials = decrypt_data($encryptedCredentials);
        if(!($this->providerdetails = json_decode($jsonCredentials, true))){ return false; }
        
        foreach(['tenantId', 'clientId', 'clientSecret', 'refreshToken'] as $key) {
            if(isset($this->providerdetails[$key])) { $this->$key = $this->providerdetails[$key]; }
        }

        $this->scopes = implode(" ", $this->providerdetails['options']['scope']);// changed to &
        if (empty($this->scopes)) { $this->scopes = 'https%3A%2F%2Fgraph.microsoft.com%2F.default'; }

        $this->flowType = $flow;

        $this->accessToken = "";

        $params = [
            'clientId'      => $this->clientId,
            'clientSecret'  => $this->clientSecret,
            'tenantId'      => $this->tenantId,
            'accessType'    => 'offline'
        ];
        $this->provider = getProvider('azure', $params);
    }

    /*
     *  Send a POST request to the Microsoft token endpoint with the requisite data and set the access token
     * 
     *  @param bool $clientCredentialsFlow  - whether or not to use the client credentials flow (no refresh token)
     *  @param string $renewRefreshToken    - whether or not to renew the refresh token in the OAuth2 file (if using refresh token flow)
     * 
     *  @return reply 					    - JSON encoded reply (token) from the token endpoint
     */
    function refreshTokens($oauthFlow = 'clientCredentials', $renewRefreshToken = true) {
        // OAuth2 through v2 endpoint
        if($oauthFlow == 'clientCredentials') {
            // Get access token
            $tokenObject = $this->provider->getAccessToken('client_credentials', [
                'scope' => $this->defaultScope
            ]);

            if($tokenObject && $tokenObject->getToken()) {
                $this->accessToken = $tokenObject->getToken();
            } else { return false; }

        } else if($oauthFlow == 'refreshToken') {
            if (!$this->refreshToken){ return false; }

            // Get tokens (access and refresh)
            $tokenObject = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->refreshToken
            ]);

            // Set refresh and access tokens if gotten
            if($tokenObject && $tokenObject->getToken() && $tokenObject->getRefreshToken()) {
                $this->refreshToken = $tokenObject->getRefreshToken();
                $this->providerdetails['refreshToken'] = $this->refreshToken;
                $this->accessToken = $tokenObject->getToken();
            } else { return false; }
            
            // Update file with new refresh token
            file_put_contents('/usr/local/nagiosxi/etc/components/oauth2/oauth2mailcredentials.json', encrypt_data(json_encode($this->providerdetails)));
        } else { return false; } // invalid flow

        return true;
    }

    /*
     *  Send a POST request to the specified URL with the specified data
     * 
     *  @param string $messageArgs 	- array of arguments to create the message
        * * @param string $messageArgs['toRecipients']     - email address of the recipient
        * * @param string $messageArgs['ccRecipients']     - subject of the email
        * * @param string $messageArgs['bccRecipients']    - subject of the email
        * * @param string $messageArgs['subject']          - subject of the email
        * * @param string $messageArgs['body']             - body of the email
        * * @param string $messageArgs['importance']       - importance of the email
     * 
     *  @return array - JSON encoded message
     */
    function createMessageJSON($messageArgs) {
        $messageArray = array();
        if (array_key_exists('toRecipients', $messageArgs)) {
            foreach ($messageArgs['toRecipients'] as $recipient) {
                if (array_key_exists('name', $recipient)) {
                    $messageArray['toRecipients'][] = array('emailAddress' => array('name' => $recipient['name'], 'address' => $recipient['address']));
                } else {
                    $messageArray['toRecipients'][] = array('emailAddress' => array('address' => $recipient['address']));
                }
            }
        }
        if (array_key_exists('ccRecipients', $messageArgs)) {
            foreach ($messageArgs['ccRecipients'] as $recipient) {
                if (array_key_exists('name', $recipient)) {
                    $messageArray['ccRecipients'][] = array('emailAddress' => array('name' => $recipient['name'], 'address' => $recipient['address']));
                } else {
                    $messageArray['ccRecipients'][] = array('emailAddress' => array('address' => $recipient['address']));
                }
            }
        }
        if (array_key_exists('bccRecipients', $messageArgs)) {
            foreach ($messageArgs['bccRecipients'] as $recipient) {
                if (array_key_exists('name', $recipient)) {
                    $messageArray['bccRecipients'][] = array('emailAddress' => array('name' => $recipient['name'], 'address' => $recipient['address']));
                } else {
                    $messageArray['bccRecipients'][] = array('emailAddress' => array('address' => $recipient['address']));
                }
            }
        }
        if (array_key_exists('subject', $messageArgs)) $messageArray['subject'] = $messageArgs['subject'];
        if (array_key_exists('importance', $messageArgs)) $messageArray['importance'] = $messageArgs['importance'];
        if (isset($messageArgs['replyTo'])) $messageArray['replyTo'] = array(array('emailAddress' => array('name' => $messageArgs['replyTo']['name'], 'address' => $messageArgs['replyTo']['address'])));
        if (array_key_exists('body', $messageArgs)) $messageArray['body'] = array('contentType' => 'HTML', 'content' => $messageArgs['body']);
        $messageArray = array("message" => $messageArray);
        return json_encode($messageArray);
    }

    /*
     *  Send a POST request to the MS Graph API sendMail endpoint with the JSON formatted message
     * 
     * @param string $mailbox 		- mailbox to send from
     * @param array $messageArgs 	- JSON formatted message
     * @param bool $deleteAfterSend - delete the message from the sent items folder after sending
     * 
     * @return string - true or error message
     */
    function sendMail($mailbox, $messageArgs, $deleteAfterSend = false) {
        if(count($messageArgs['toRecipients']) === 1 && $messageArgs['toRecipients'][0]['address'] === 'root@localhost'){
            return _("Please choose at least one recipient email address.");
        }

        try{
            if(!$this->refreshTokens($this->flowType)){ return _("failed to get token"); }
        } catch (Exception $e) {
            return _("failed to get token");
        }

        if($deleteAfterSend) $messageArgs['saveToSentItems'] = "false";

        $messageJSON = $this->createMessageJSON($messageArgs);
        $response = $this->sendPostRequest($this->baseURL . 'users/' . $mailbox . '/sendMail', $messageJSON, array('Content-type: application/json'));

        // success
        if(!empty($response['code']) && $response['code'] == 202) return true;

        // error handling
        $response['data'] = json_decode($response['data'], true);
        if(!empty($response['data'])){
            if(!empty($response['data']['error'])){
                if(!empty($response['data']['error']['message'])) {
                    return $response['data']['error']['message'];
                }
                return $response['data']['error'];
            }
            return $response['data'];
        }
        return print_r($response, true);
        return _("no error message returned");
    }

    /*
     *  Send a POST request to the specified URL with the specified data
     * 
     * @param string $URL 		- URL to send the POST request to
     * @param string $Fields 	- data to send in the POST request
     * @param string $Headers 	- headers to send in the POST request
     * 
     * @return array - response code and response data
     */
    function sendPostRequest($URL, $Fields, $Headers = false) {
        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_POST, 1);

        // TLS 1.3 if available, otherwise TLS 1.2
        $ssl_version = CURL_SSLVERSION_TLSv1_2;	                                        //TLS 1.2+
        if(defined('CURL_SSLVERSION_TLSv1_3')) $ssl_version = CURL_SSLVERSION_TLSv1_3;	//TLS 1.3+ if available
        curl_setopt($ch, CURLOPT_SSLVERSION, $ssl_version);

        if ($Fields) curl_setopt($ch, CURLOPT_POSTFIELDS, $Fields);		//Send data/request body
        if ($Headers) {
            $Headers[] = 'Authorization: Bearer ' . $this->accessToken;	//OAuth2 authorization
            curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);					//return as string
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        //do not delete this, it's super useful for figuring out why things aren't working
        // echo "<script> console.log('sending post request'); console.log(".json_encode($response)."); console.log(".json_encode($responseCode)."); console.log('');</script>";
        // echo "<script> console.log('full curl info: '); console.log(".json_encode(curl_getinfo($ch))."); console.log('');</script>";
        
        return array('code' => $responseCode, 'data' => $response);
    }
}
?>