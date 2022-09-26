### Subrion CMS Detect (subrion-cms-detect) found on http://exfiltrated.offsec
---
**Details**: **subrion-cms-detect**  matched at http://exfiltrated.offsec

**Protocol**: HTTP

**Full URL**: http://exfiltrated.offsec

**Timestamp**: Mon Sep 26 20:17:51 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Subrion CMS Detect |
| Authors | pikpikcu |
| Tags | subrion, tech |
| Severity | info |
| shodan-query | http.component:"Subrion" |
| fofa-query | title="subrion" |

**Request**
```http
GET / HTTP/1.1
Host: exfiltrated.offsec
User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36
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
Date: Mon, 26 Sep 2022 19:18:03 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: INTELLI_06c8042c3d=8uomfma60uinhsep3q18sl8211; path=/
Set-Cookie: INTELLI_06c8042c3d=8uomfma60uinhsep3q18sl8211; expires=Mon, 26-Sep-2022 19:48:03 GMT; Max-Age=1800; path=/
Vary: Accept-Encoding
X-Powered-Cms: Subrion CMS

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Home :: Powered by Subrion 4.2</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="generator" content="Subrion CMS - Open Source Content Management System">
        <meta name="robots" content="index">
        <meta name="robots" content="follow">
        <meta name="revisit-after" content="1 day">
        <base href="http://exfiltrated.offsec/">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <link rel="shortcut icon" href="//exfiltrated.offsec/favicon.ico">

        
        

            
    


    <meta property="og:title" content="Home">
    <meta property="og:url" content="http://exfiltrated.offsec/">
    <meta property="og:description" content="">



        
	<link rel="stylesheet" href="//exfiltrated.offsec/templates/kickstart/css/iabootstrap.css?fm=1528952694">
	<link rel="stylesheet" href="//exfiltrated.offsec/templates/kickstart/css/user-style.css?fm=1528952694">
	<link rel="stylesheet" href="//exfiltrated.offsec/modules/fancybox/js/jquery.fancybox.css?fm=1528952694">

        

            </head>

    <body class="page-index">
        <div class="inventory">
            <div class="container">
                                    <ul class="nav-inventory nav-inventory-social pull-left hidden-xs">
                        <li><a href="#" class="twitter"><span class="fa fa-twitter"></span></a></li>                        <li><a href="#" class="facebook"><span class="fa fa-facebook"></span></a></li>                        <li><a href="#" class="google-plus"><span class="fa fa-google-plus"></span></a></li>                        <li><a href="#" class="linkedin"><span class="fa fa-linkedin"></span></a></li>                    </ul>
                                                
                
                
        

            <ul class="nav-inventory hidden-sm hidden-xs pull-right ">

            
                
                                    <li class="m_index
                                                 active                                                                        ">

                        <a href="http://exfiltrated.offsec/"
                                                                                                            >
                            Home
                                                    </a>
                                            </li>
                
                
            
            <!-- MORE menu dropdown -->
                    </ul>
    



            </div>
        </div>

        <nav class="navbar navbar-default navbar-sticky">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand navbar-brand--img" href="http://exfiltrated.offsec/">
                                                                                    <img src="//exfiltrated.offsec/templates/kickstart/img/logo.png" alt="Subrion CMS">
                                                                        </a>
                </div>

                <div class="collapse navbar-collapse" id="navbar-collapse">
                                            <form method="get" action="http://exfiltrated.offsec/search/" class="search-navbar pull-right">
                            <button class="search-navbar__toggle js-search-navbar-toggle" type="button"><span class="fa fa-search"></span></button>
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search">
                                <div class="input-group-btn">
              .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://exfiltrated.offsec'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)