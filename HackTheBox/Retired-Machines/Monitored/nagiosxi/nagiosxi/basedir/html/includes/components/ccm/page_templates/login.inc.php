<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: login.inc.php
//  Desc: Used to generate the login form... we could probably move this somewhere else.
//

/**
 * Returns html for the login page 
 *
 * @return string $html HTML output
 */
function build_login_form()
{
    $html = "
    <div id='loginDiv'>
        <h3>Nagios CCM Login</h3>
        <form id='loginForm' action='index.php' method='post'>
            <label for='username'>"._("Username").": </label><br />
            <input type='text' name='username' id='username' size='20' " . sensitive_field_autocomplete() . "/><br /><br />
            <label for='password'>"._("Password")."</label><br />
            <input type='password' name='password' id='password' size='20' " . sensitive_field_autocomplete() . "/><br /><br />
            <input type='hidden' name='loginSubmitted' value='true' />
            <input type='hidden' name='menu' value='invisible' />
            <input class='ccmbutton' type='submit' name='submit' id='submit' value='Login' />
        </form>
    </div>";
    return $html;
}