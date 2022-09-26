### robots.txt endpoint prober (robots-txt-endpoint) found on http://exfiltrated.offsec
---
**Details**: **robots-txt-endpoint**  matched at http://exfiltrated.offsec

**Protocol**: HTTP

**Full URL**: http://exfiltrated.offsec/panel/

**Timestamp**: Mon Sep 26 20:18:24 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /panel/ HTTP/1.1
Host: exfiltrated.offsec
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html;charset=UTF-8
Date: Mon, 26 Sep 2022 19:18:34 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: INTELLI_06c8042c3d=ee5vs0k6jl0to9en7mi06mbbj0; path=/
Set-Cookie: INTELLI_06c8042c3d=ee5vs0k6jl0to9en7mi06mbbj0; expires=Mon, 26-Sep-2022 19:48:34 GMT; Max-Age=1800; path=/
Vary: Accept-Encoding
X-Robots-Tag: noindex

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Login :: Powered by Subrion 4.2</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="generator" content="Subrion CMS - Open Source Content Management System">
        <meta name="robots" content="noindex">
        <base href="http://exfiltrated.offsec/panel/">

        <!--[if lt IE 9]>
            <script src="../../../js/utils/shiv.js"></script>
            <script src="../../../js/utils/respond.min.js"></script>
        <![endif]-->

        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="//exfiltrated.offsec/admin/templates/default/img/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="//exfiltrated.offsec/admin/templates/default/img/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="//exfiltrated.offsec/admin/templates/default/img/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="//exfiltrated.offsec/admin/templates/default/img/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="//exfiltrated.offsec/admin/templates/default/img/ico/favicon.ico">

        

        
        

        
	<link rel="stylesheet" href="//exfiltrated.offsec/admin/templates/default/css/bootstrap-default.css?fm=1528952694">
	<link rel="stylesheet" href="//exfiltrated.offsec/js/bootstrap/css/passfield.css?fm=1528952694">
	<link rel="stylesheet" href="//exfiltrated.offsec/js/bootstrap/css/fontawesome-iconpicker.min.css?fm=1528952694">

        
    </head>

    <body class="page-login">
        <div class="page-login__content">
            <div class="b-login">
                <div class="b-login__img">
                    <a href="https://subrion.org/"><img src="//exfiltrated.offsec/admin/templates/default/img/subrion-logo-lines.png" alt="Subrion CMS" title="Subrion CMS"/></a>
                    <h1>Welcome to<br>Subrion Admin Panel</h1>
                </div>
                <div class="b-login__form">
                    <div class="login-body">
                                                                        
                        <form method="post" class="sap-form">
                            <input type="hidden" name="__st" value="uP6ZHKRjEyuAc0hlskGN3sWSucIvsaew17fyF81i">
                            <p>
                                <input type="text" id="username" name="username" value="" autofocus placeholder="Login">
                            </p>
                            <p>
                                <input type="password" id="dummy_password" name="password" placeholder="Password">
                            </p>
                            <div class="checkbox">
                                <label><input type="checkbox" name="remember"> Remember me</label>
                            </div>
                                                        <input type="submit" class="btn btn-primary" value="Login">
                            <a href="#" class="btn btn-link" id="js-forgot-dialog">Forgot your password?</a>
                        </form>
                    </div>
                    <div class="js-login-body-forgot-password" style="display: none;">
                        <form method="post" class="sap-form">
                            <div class="alert" style="display: none;">E-mail is incorrect.</div>
                            <input type="hidden" name="__st" value="uP6ZHKRjEyuAc0hlskGN3sWSucIvsaew17fyF81i">
                            <p class="help-block">Restore Password</p>
                            <p>
                                <input type="text" id="email" name="email" placeholder="Type your email here">
                            </p>
                            <input id="js-forgot-submit" type="submit" class="btn btn-primary" value="Go">
                            <input  id="js-forgot-dialog-close" type="submit" class="btn btn-link" value="Cancel">
                        </form>
                    </div>
                    <div class="copyright">
                        <p>
                            Powered by <a href="https://subrion.org/" title="Subrion CMS">Subrion CMS v4.2.1</a><br>
                            Copyright &copy; 2008-2022 <a href="https://intelliants.com/" title="Intelligent Web Solutions">Intelliants LLC</a>
                        </p>
               .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36' 'http://exfiltrated.offsec/panel/'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)