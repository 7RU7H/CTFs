### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on http://192.168.141.115
---
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies**  matched at http://192.168.141.115

**Protocol**: HTTP

**Full URL**: http://192.168.141.115

**Timestamp**: Fri Sep 30 08:26:55 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.141.115
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4629
Cache-Control: no-store, no-cache, must-revalidate
Content-Language: en-US
Content-Security-Policy: frame-ancestors ''self'';
Content-Type: text/html; charset=UTF-8
Date: Fri, 30 Sep 2022 07:26:55 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.6 (CentOS) PHP/7.3.23
Set-Cookie: OSTSESSID=i69jl0j4dbfctpmmnkrdfvo2eh; expires=Sat, 01-Oct-2022 07:26:55 GMT; Max-Age=86400; path=/
X-Powered-By: PHP/7.3.23

<!DOCTYPE html>
<html lang="en_US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Ozzie Ozzie Ozzie</title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css/osticket.css" media="screen">
    <link rel="stylesheet" href="/assets/default/css/theme.css" media="screen">
    <link rel="stylesheet" href="/assets/default/css/print.css" media="print">
    <link rel="stylesheet" href="/scp/css/typeahead.css"
         media="screen" />
    <link type="text/css" href="/css/ui-lightness/jquery-ui-1.10.3.custom.min.css"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="/css/thread.css" media="screen">
    <link rel="stylesheet" href="/css/redactor.css" media="screen">
    <link type="text/css" rel="stylesheet" href="/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="/css/flags.css">
    <link type="text/css" rel="stylesheet" href="/css/rtl.css"/>
    <link type="text/css" rel="stylesheet" href="/css/select2.min.css">
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="/images/oscar-favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/images/oscar-favicon-16x16.png" sizes="16x16" />
    <script type="text/javascript" src="/js/jquery-3.4.0.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.12.1.custom.min.js"></script>
    <script src="/js/osticket.js"></script>
    <script type="text/javascript" src="/js/filedrop.field.js"></script>
    <script src="/scp/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="/js/redactor.min.js"></script>
    <script type="text/javascript" src="/js/redactor-plugins.js"></script>
    <script type="text/javascript" src="/js/redactor-osticket.js"></script>
    <script type="text/javascript" src="/js/select2.min.js"></script>
    <script type="text/javascript" src="/js/fabric.min.js"></script>
    
	<meta name="csrf_token" content="64b07dbb2b94979d76c17d4e1dd10ddabe827ed3" />
</head>
<body>
    <div id="container">
        <div id="header">
            <div class="pull-right flush-right">
            <p>
                                 Guest User |                     <a href="/login.php">Sign In</a>
            </p>
            <p>
            </p>
            </div>
            <a class="pull-left" id="logo" href="/index.php"
            title="Support Center">
                <span class="valign-helper"></span>
                <img src="/logo.php" border=0 alt="Ozzie Ozzie Ozzie">
            </a>
        </div>
        <div class="clear"></div>
                <ul id="nav" class="flush-left">
            <li><a class="active home" href="/index.php">Support Center Home</a></li>
<li><a class=" new" href="/open.php">Open a New Ticket</a></li>
<li><a class=" status" href="/view.php">Check Ticket Status</a></li>
        </ul>
                <div id="content">

         <div id="landing_page">
    <div class="sidebar pull-right">
        <div class="front-page-button flush-right">
<p>
            <a href="open.php" style="display:block" class="blue button">Open a New Ticket</a>
</p>
<p>
            <a href="view.php" style="display:block" class="green button">Check Ticket Status</a>
</p>
        </div>
        <div class="content"></div>
    </div>

<div class="main-content">
<h1>Welcome to the Support Center</h1> <p> In order to streamline support requests and better serve you, we utilize a support ticket system. Every support request is assigned a unique ticket number which you can use to track the progress and responses online. For your reference we provide complete archives and history of all your support requests. A valid email address is required to submit a ticket. </p>    </div>
</div>
<div class="clear"></div>

<div>
</div>
</div>

        </div>
    </div>
    <div id="footer">
        <p>Copyright &copy; 2022 Ozzie Ozzie Ozzie - All rights reserved.</p>
        <a id="poweredBy" href="http://osticket.com" target="_blank">Helpdesk software - powered by osTicket</a>
    </div>
<div id="overlay"></div>
<div id="loading">
    <h4>Please Wait!</h4>
    <p>Please wait... it will take a second!</p>
</div>
<script type="text/javascript">
    getConfig().resolve({"html_thread":true,"lang":"en_US","short_lang":"en","has_rtl":false,"primary_language":"en-US","secondary_languages":[]});
</script>
</b.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.141.115'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)