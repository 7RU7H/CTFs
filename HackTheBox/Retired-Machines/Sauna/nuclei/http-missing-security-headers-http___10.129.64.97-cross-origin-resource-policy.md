### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on http://10.129.64.97
---
**Details**: **http-missing-security-headers:cross-origin-resource-policy**  matched at http://10.129.64.97

**Protocol**: HTTP

**Full URL**: http://10.129.64.97

**Timestamp**: Wed Aug 3 14:52:05 +0100 BST 2022

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
Host: 10.129.64.97
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Wed, 03 Aug 2022 20:52:06 GMT
Etag: "01ae9b10d2d51:0"
Last-Modified: Thu, 23 Jan 2020 17:14:44 GMT
Server: Microsoft-IIS/10.0
Vary: Accept-Encoding

﻿<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Egotistical Bank :: Home</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Repay Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <script>
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }

    </script>
    <!-- //Meta tag Keywords -->
    <!-- Custom-Files -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Bootstrap-Core-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/slider.css" type="text/css" media="all" />
    <!-- Style-CSS -->
    <!-- font-awesome-icons -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <!-- //font-awesome-icons -->
    <!-- /Fonts -->
    <link href="//fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800" rel="stylesheet">

    <!-- //Fonts -->
</head>

<body>
    <!-- mian-content -->
    <div class="main-w3-pvt-header-sec" id="home">

        <!-- header -->
        <header>
            <div class="container">
                <div class="header d-lg-flex justify-content-between align-items-center py-lg-3 px-lg-3">
                    <!-- logo -->
                    <div id="logo">
                        <h1><a href="index.html"><span class="fa fa-recycle mr-2"></span>Repay</a></h1>
                    </div>
                    <!-- //logo -->
                    <div class="w3pvt-bg">
                        <!-- nav -->
                        <div class="nav_w3pvt">
                            <nav>
                                <label for="drop" class="toggle">Menu</label>
                                <input type="checkbox" id="drop" />
                                <ul class="menu">
                                    <li class="active"><a href="index.html">Home</a></li>
                                    <li><a href="about.html">About Us</a></li>
                                    <li><a href="blog.html">Blogs</a></li>
                                    <li>
                                        <!-- First Tier Drop Down -->
                                        <label for="drop-2" class="toggle toogle-2">Dropdown <span class="fa fa-angle-down" aria-hidden="true"></span>
                                        </label>
                                        <a href="#">Dropdown <span class="fa fa-angle-down" aria-hidden="true"></span></a>
                                        <input type="checkbox" id="drop-2" />
                                        <ul>

                                            <li><a href="#process" class="drop-text">Process</a></li>

                                            <li><a href="#stats" class="drop-text">Statistics</a></li>
                                            <li><a href="#services" class="drop-text">Services</a></li>
                                            <li><a href="about.html" class="drop-text">Our Team</a></li>
                                            <li><a href="#test" class="drop-text">Clients</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="contact.html">Contact Us</a></li>
                                </ul>
                            </nav>
                        </div>
                        <!-- //nav -->
                        <div class="justify-content-center">
                            <!-- search -->
                            <div class="apply-w3-pvt ml-lg-3">
                                <a class="btn read" href="contact.html" role="button">Apply Now</a>
                            </div>
                            <!-- //search -->

                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- //header -->


        <!-- banner -->
        <section class="banner_w3pvt">
            <div class="csslider infinity" id="slider1">
                <input type="radio" name="slides" checked="checked" id="slides_1" />
                <input type="radio" name="slides" id="slides_2" />
                <input type="radio" name.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://10.129.64.97'
```
---
Generated by [Nuclei 2.7.5](https://github.com/projectdiscovery/nuclei)