### HTTP Missing Security Headers (http-missing-security-headers:strict-transport-security) found on http://10.10.146.5

----
**Details**: **http-missing-security-headers:strict-transport-security** matched at http://10.10.146.5

**Protocol**: HTTP

**Full URL**: http://10.10.146.5

**Timestamp**: Sun Aug 13 15:00:31 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.10.146.5
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
Transfer-Encoding: chunked
Content-Type: text/html; charset=utf-8
Date: Sun, 13 Aug 2023 14:00:31 GMT
Referrer-Policy: same-origin
Server: nginx/1.18.0 (Ubuntu)
Vary: Cookie
X-Content-Type-Options: nosniff
X-Frame-Options: DENY



<!DOCTYPE html>
<html lang="en">

<head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- site metas -->
    <title>Spicyo</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <!-- owl css -->
    <link rel="stylesheet" href="/static/css/owl.carousel.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="/static/css/style.css">
    <!-- responsive-->
    <link rel="stylesheet" href="/static/css/responsive.css">
    <!-- awesome fontfamily -->
    <link rel="stylesheet" href="/static/css/font-awesome.min.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<!-- body -->

<body class="main-layout">
    <!-- loader  -->
    <div class="loader_bg">
        <div class="loader"><img src="/static/images/loading.gif" alt="" /></div>
    </div>

    <div class="wrapper">
        <!-- end loader -->

        <div class="sidebar">
            <!-- Sidebar  -->
            <nav id="sidebar">

                <div id="dismiss">
                    <i class="fa fa-arrow-left"></i>
                </div>

                <ul class="list-unstyled components">

                    <li class="active">
                        <a href="/">Home</a>
                    </li>
                    <li>
                        <a href="about">About</a>
                    </li>
                    <li>
                        <a href="recipe">Recipe</a>
                    </li>
                    <li>
                        <a href="blog">Blog</a>
                    </li>
                    <li>
                        <a href="contact">Contact Us</a>
                    </li>
                </ul>

            </nav>
        </div>

        <div id="content">
            <!-- header -->
            <header>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="full">
                                <a class="logo" href="/"><img src="/static/images/logo.png" alt="#" /></a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="full">
                                <div class="right_header_info">
                                    <ul>
                                        <li class="dinone">Contact Us : <img style="margin-right: 15px;margin-left: 15px;" src="/static/images/phone_icon.png" alt="#"><a href="#">987-654-3210</a></li>
                                        <li class="dinone"><img style="margin-right: 15px;" src="/static/images/mail_icon.png" alt="#"><a href="#">demo@gmail.com</a></li>
                                        <li class="dinone"><img style="margin-right: 15px;height: 21px;position: relative;top: -2px;" src="/static/images/location_icon.png" alt="#"><a href="#">104 New york , USA</a></li>
                                        	
										<li class="button_user"><a class="button active" href="login">Login</a>
										
                                        <li><img style="margin-right: 15px;" src="/static/images/search_icon.png" alt="#"></li>
                                        <li>
                                            <button type="button" id="sidebarCollapse">
                                        <img src="/static/images/menu_icon.png" alt="#">
                                    </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- end header -->
            <!-- start slider section -->
            <div class="slider_section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="full">
                                <div id="main_slider" class="carousel vert slide" data-ride="carousel" data-interval="5000">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <div class="row">
.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://10.10.146.5'
```

----

Generated by [Nuclei v2.9.8](https://github.com/projectdiscovery/nuclei)