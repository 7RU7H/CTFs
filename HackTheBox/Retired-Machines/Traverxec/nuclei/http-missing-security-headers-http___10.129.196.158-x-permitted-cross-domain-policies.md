### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on http://10.129.196.158
---
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies**  matched at http://10.129.196.158

**Protocol**: HTTP

**Full URL**: http://10.129.196.158

**Timestamp**: Sat Apr 29 12:46:43 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.196.158
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
Content-Length: 15674
Content-Type: text/html
Date: Sat, 29 Apr 2023 11:46:42 GMT
Last-Modified: Fri, 25 Oct 2019 21:11:09 GMT
Server: nostromo 1.9.6

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>TRAVERXEC</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicons -->
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="lib/prettyphoto/css/prettyphoto.css" rel="stylesheet">
  <link href="lib/hover/hoverex-all.css" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="css/style.css" rel="stylesheet">

  <!-- =======================================================
    Template Name: Basic
    Template URL: https://templatemag.com/basic-bootstrap-personal-template/
    Author: TemplateMag.com
    License: https://templatemag.com/license/
  ======================================================= -->
</head>

<body>

  <div id="h">
    <div class="logo">
      <h2>TRAVERXEC</h2>
    </div>
    <!--/logo-->
    <div class="container centered">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <h1>Hello, my name is <b>David White</b>.<br/>I create for the web.</h1>
        </div>
      </div>
      <!--/row-->

      <div class="row mt">
        <div class="col-sm-4">
          <i class="ion-ios7-monitor-outline"></i>
          <h3>Web Design</h3>
        </div>
        <!--/col-md-4-->

        <div class="col-sm-4">
          <i class="ion-ios7-browsers-outline"></i>
          <h3>UI Development</h3>
        </div>
        <!--/col-md-4-->

        <div class="col-sm-4">
          <i class="ion-ios7-copy-outline"></i>
          <h3>Brand Identity</h3>
        </div>
        <!--/col-md-4-->

      </div>
      <!--/row-->
    </div>
    <!--/container-->
  </div>
  <!--H-->

  <div class="container ptb">
    <div class="row">
      <h2 class="centered mb">I craft handsome sites & stunning apps<br/>that empower your startup.</h2>
      <div class="col-md-6">
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
      </div>
      <!--/col-md-6-->
      <div class="col-md-6">
        <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with
          desktop publishing software.</p>
      </div>
      <!--/col-md-6-->
    </div>
    <!--/row-->
  </div>
  <!-- /.container -->
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <img src="img/items.png" class="img-responsive" alt="">
      </div>
    </div>
    <!--/row-->
  </div>
  <!--/.container-->

  <div id="g">
    <div class="container">
      <div class="row centered">
        <h2>Check some of my latest works.</h2>
        <div class="col-md-8 col-md-offset-2">
          <p>Contrary to popular belief, Lorem Ipsum is not simply random text.<br/>It has roots in a piece of classical Latin literature<br/>from 45 BC, making it over 2000 years old.</p>

        </div>
        <!--/col-md-8-->
      </div>
      <!--/row-->
    </div>
    <!--/.container-->
    <div class="portfolio-centered mt">
      <div class="recentitems portfolio">

        <div class="portfolio-item graphic-design">
          <div class="he-wrap tpl6">
            <img src="img/portfolio/portfolio_09.jpg" class="img-responsive" alt="">
            <div class="he-view">
              <div class="bg a0" data-animate="fadeIn">
                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                <a data-rel="prettyPhoto" href="img/portfolio/portfolio_09.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="ion-search"></i></a>
                <a href="#" class="dmbutton a2" data-animate="fadeInUp"><i class="ion-link"></i></a>
              </div>
              <!-- he bg -->
            </div>
            <!-- he view -->
          </div>
          <!-- he wrap -->
        </div>
        <!-- end col-12 -->

        <div class="portfolio-item web-design">
          <div class="he-wrap tpl6">
            <img src="img/portfolio/portfolio_02.jpg" class="img-responsive" alt="">
            <div class="he-view">
              <div class="bg a0" data-animate="fadeIn">
                <h3 class="a1" data-animate="fadeInDown">A Web Design Ite.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://10.129.196.158'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)