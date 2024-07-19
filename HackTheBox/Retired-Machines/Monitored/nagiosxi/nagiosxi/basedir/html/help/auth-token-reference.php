<?php
//
// Nagios XI API Documentation
// Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/html-helpers.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

if (!is_admin()) {
    die(_('Not authorized to view this page.'));
}

route_request();

function route_request()
{
    $page = grab_request_var("page", "");

    switch ($page) {
        default:
            show_main_api_page();
            break;
    }
}

function show_main_api_page()
{
    $apikey = get_apikey();

    do_page_start(array("page_title" => _('Auth Token Reference')), true);
?>

    <script src="../includes/js/clipboard.min.js"></script>
    <script>
    $(document).ready(function() {
        new Clipboard('.copy');
    });
    </script>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-12 col-lg-9">

                <h1><?php echo _('Auth Tokens'); ?></h1>
                <p><?php echo _('As of Nagios XI 5.5, the user authentication system was redesigned to be more secure. The new security measures include strict controls on how requests are made to XI and on which users, if any, are logged in after the request finishes. Due to the nature of these changes, we have implemented a new way for users to authenticate to Nagios XI externally.'); ?></p>

                <div class="alert alert-info" style="margin: 10px 0;">
                    <?php echo _("<b>Note:</b> This endpoint is not secured with an API key. This is done intentionally so that the real username and password can be used with 3rd-party applications."); ?>
                </div>

                <h2><?php echo _('How it Works'); ?></h2>
                <p><?php echo _('Using a POST request, pass a username and password to the authenticate section of the API. This will result in either an error, or an auth token. On a successful authentication, the token can be used to log into Nagios XI as the user by sending a GET or POST request and passing the token. By default, tokens expire after 5 minutes and can no longer be used once expired. Tokens are single use, meaning a new token is always required for a new session.'); ?></p>

                <h2><?php echo _('Creating an Auth Token (Authenticating)'); ?></h2>
                <p>
                    <b><?php echo _('Location'); ?>:</b> POST <a href="<?php echo get_base_url(); ?>api/v1/authenticate" class="tt-bind" title="<?php echo get_base_url(); ?>api/v1/authenticate">api/v1/authenticate</a>
                </p>
                <p>
                    <?php echo _('For the example we will pass a fake username and password but rely on the defaults for the expiration time.'); ?>
                </p>
                <h6><?php echo _('Example cURL Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-auth">Copy</a></h6>
                <pre class="curl-request"><code id="cmd-auth">curl -XPOST "<?php echo get_base_url(); ?>api/v1/authenticate?pretty=1" -d "username=nagiosadmin&amp;password=&lt;password&gt;"</code></pre>
                <h6><?php echo _('Response (Success)'); ?>:</h6>
                <pre>{
    "username": "nagiosadmin",
    "user_id": "1",
    "auth_token": "0f09f05ff390cdfe1539d1eb17d3a16fd4550eae",
    "valid_min": 5,
    "valid_until": "<?php echo date('r', time()+5*60); ?>"
}</pre>
                <h6><?php echo _('Response (Error)'); ?>:</h6>
                <pre>{
    "username": "nagiosadmin",
    "message": "Invalid username or password",
    "error": 1
}</pre>

                <h2><?php echo _('Using an Auth Token'); ?></h2>
                <div><?php echo _('After the token is created, you have until the token expiration time to use it. Using the token to authenticate is quite easy. You can pass the token into the login.php URL with a standard GET request.'); ?></div>
                <p>
                    <h6><?php echo _('Example Request'); ?>: <a class="copy fr" data-clipboard-target="#cmd-auth-login">Copy</a></h6>
                    <pre class="curl-request"><code id="cmd-auth-login"><?php echo get_base_url(); ?>login.php?token=&lt;auth_token&gt;</code></pre>
                </p>

                <h6><?php echo _('Available Options'); ?></h6>
                <p><?php echo _('A full list of available options are listed below. Any defaults are highlighted in bold.'); ?></p>
                <table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px; min-width: 300px; width: 40%;">
                    <thead>
                        <tr>
                            <th><?php echo _('Parameters'); ?></th>
                            <th><?php echo _('Value Type'); ?></th>
                            <th><?php echo _('Values'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>username</td>
                            <td>string</td>
                            <td>username</td>
                        </tr>
                        <tr>
                            <td>password</td>
                            <td>string</td>
                            <td>password</td>
                        </tr>
                        <tr>
                            <td>valid_min</td>
                            <td>int</td>
                            <td><?php echo _('minutes'); ?> (<?php echo _('defaults to'); ?> <b>5</b>)</td>
                        </tr>
                    </tbody>
                </table>

                <h2><?php echo _('Example of Auth Tokens in Bash Scripts'); ?></h2>
                <p><?php echo _('If you need to run something like grabbing data from a page or a report, you can use auth tokens to authenticate.'); ?></p>
                <pre>#!/bin/bash

# Set options
hostname="&lt;host/address&gt;"
username="nagiosadmin"
password="&lt;password&gt;"

# Get a single-use auth token from the XI API
token=$(curl -XPOST "http://$hostname/nagiosxi/api/v1/authenticate" -d "username=$username&password=$password" | grep -Po  '"auth_token":.*?[^\\]",' | awk -F '"' '{print $4}')

# Use auth token to acces a page (downloading a report as CSV)
wget "http://$hostname/nagiosxi/reports/notifications.php?reportperiod=last24hours&mode=getreport&mode=csv&token=$token" -O report.csv</pre>

            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}
