### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on bizness.htb

----
**Details**: **http-missing-security-headers:cross-origin-resource-policy** matched at bizness.htb

**Protocol**: HTTP

**Full URL**: https://bizness.htb

**Timestamp**: Sat Jan 6 19:39:29 +0000 GMT 2024

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
Host: bizness.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 
Connection: close
Transfer-Encoding: chunked
Accept-Ranges: bytes
Content-Type: text/html
Date: Sat, 06 Jan 2024 19:39:28 GMT
Etag: W/"27200-1702887508516"
Last-Modified: Mon, 18 Dec 2023 08:18:28 GMT
Server: nginx/1.18.0
Set-Cookie: JSESSIONID=677A1E2F3D5503C3F5C75D04E33710A6.jvm1; Path=/; Secure; HttpOnly
Vary: accept-encoding

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BizNess Incorporated</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">

    <!-- Bootstrap CSS File -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Libraries CSS Files -->
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Main Stylesheet File -->
    <link href="css/style.css" rel="stylesheet">

    <!-- =======================================================
    Theme Name: BizPage
    Theme URL: https://bootstrapmade.com/bizpage-bootstrap-business-template/
    Author: BootstrapMade.com
    License: https://bootstrapmade.com/license/
  ======================================================= -->
</head>

<body>

    <!--==========================
    Header
  ============================-->
    <header id="header">
        <div class="container-fluid">

            <div id="logo" class="pull-left">
                <h1><a href="#intro" class="scrollto">BizNess</a></h1>
                <!-- Uncomment below if you prefer to use an image logo -->
                <!-- <a href="#intro"><img src="img/logo.png" alt="" title="" /></a>-->
            </div>

            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    <li class="menu-active"><a href="#intro">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            <!-- #nav-menu-container -->
        </div>
    </header>
    <!-- #header -->

    <!--==========================
    Intro Section
  ============================-->
    <section id="intro">
        <div class="intro-container">
            <div id="introCarousel" class="carousel  slide carousel-fade" data-ride="carousel">

                <ol class="carousel-indicators"></ol>

                <div class="carousel-inner" role="listbox">

                    <div class="carousel-item active">
                        <div class="carousel-background"><img src="img/intro-carousel/1.jpg" alt=""></div>
                        <div class="carousel-container">
                            <div class="carousel-content">
                                <h2>Welcome to BizNess Incorporated</h2>
                                <p>Discover the essence of corporate excellence with BizNess Incorporated. Our commitment to innovation and quality sets us apart in the dynamic business world. Join us on a journey of professional growth and unparalleled
                                    success. Your future starts here.
                                </p>
                                <a href="#featured-services" class="btn-get-started scrollto">Get Started</a>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="carousel-background"><img src="img/intro-carousel/2.jpg" alt=""></div>
                        <div class="carousel-container">
                            <div class="carousel-content">
                                <h2>Pioneering Industry Standards</h2>
                                <p>At BizNess Incorporated, we redefine the boundaries of business. With cutting-edge strategies and a passion for progress, we lead the charge in creating tomorrow's industry standards today. Embark on a path of transformative
                                    experiences with us.</p>
                                <a href="#featured-services" class="btn-get-started scrollto">Get Started</a>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="carousel-background"><img src="img/intro-carousel/.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'https://bizness.htb'
```

----

Generated by [Nuclei v3.1.1](https://github.com/projectdiscovery/nuclei)