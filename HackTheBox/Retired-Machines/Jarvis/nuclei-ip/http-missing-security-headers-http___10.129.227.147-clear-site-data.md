### HTTP Missing Security Headers (http-missing-security-headers:clear-site-data) found on http://10.129.227.147
---
**Details**: **http-missing-security-headers:clear-site-data**  matched at http://10.129.227.147

**Protocol**: HTTP

**Full URL**: http://10.129.227.147

**Timestamp**: Tue Nov 15 12:39:01 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.227.147
User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
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
Content-Type: text/html; charset=UTF-8
Date: Tue, 15 Nov 2022 12:39:01 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Ironwaf: 2.0.3
Pragma: no-cache
Server: Apache/2.4.25 (Debian)
Set-Cookie: PHPSESSID=bibavv101sg1be67mg31md6iu7; path=/
Vary: Accept-Encoding

<!DOCTYPE HTML>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Stark Hotel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="" />

  <!-- Facebook and Twitter integration -->
	<meta property="og:title" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:url" content=""/>
	<meta property="og:site_name" content=""/>
	<meta property="og:description" content=""/>
	<meta name="twitter:title" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:url" content="" />
	<meta name="twitter:card" content="" />

	
	
	<!-- Animate.css -->
	<link rel="stylesheet" href="css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="css/icomoon.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="css/bootstrap.css">

	<!-- Magnific Popup -->
	<link rel="stylesheet" href="css/magnific-popup.css">

	<!-- Flexslider  -->
	<link rel="stylesheet" href="css/flexslider.css">

	<!-- Owl Carousel -->
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	
	<!-- Date Picker -->
	<link rel="stylesheet" href="css/bootstrap-datepicker.css">
	<!-- Flaticons  -->
	<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

	<!-- Theme style  -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Modernizr JS -->
	<script src="js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/respond.min.js"></script>
	<![endif]-->

	</head>
	<body>
		
	<div class="colorlib-loader"></div>

	<div id="page">
		<nav class="colorlib-nav" role="navigation">
			<div class="top">
				<div class="container">
					<div class="row">
						<div class="col-xs-4">
							<p class="site">supersecurehotel.htb</p>
						</div>
						<div class="col-xs-8 text-right">
							<p class="num">Call: +123456789</p>
							<ul class="colorlib-social">
                <li><a href="#">Sign in </li>                <li><a href="#">Utilities</a></li>
								<li><a href="#"><i class="icon-twitter"></i></a></li>
								<li><a href="#"><i class="icon-facebook"></i></a></li>
								<li><a href="#"><i class="icon-linkedin"></i></a></li>
								<li><a href="#"><i class="icon-dribbble"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="top-menu">
				<div class="container">
					<div class="row">
						<div class="col-xs-2">
							<div id="colorlib-logo"><a href="index.php">Stark Hotel</a></div>
						</div>
						<div class="col-xs-10 text-right menu-1">
							<ul>
								<li class="active"><a href="index.php">Home</a></li>
								<li class="has-dropdown">
									<a href="rooms-suites.php">Rooms</a>
								</li>
								<li><a href="dining-bar.php">Dining &amp; Bar</a></li>
						
							</ul>
						</div>
					</div>
				</div>
			</div>
		</nav>
		<aside id="colorlib-hero">
			<div class="flexslider">
				<ul class="slides">
			   	<li style="background-image: url(images/img_bg_5.jpg);">
			   		<div class="overlay"></div>
			   		<div class="container-fluid">
			   			<div class="row">
				   			<div class="col-md-6 col-sm-12 col-md-offset-3 slider-text">
				   				<div class="slider-text-inner text-center">
				   					<h2>Welcome to the Stark Hotel</h2>
				   					<h1>A Luxury Hotel</h1>
										<p><a class="btn btn-primary btn-demo" href="#"></i> View Detail</a> <a class="btn btn-primary btn-learn">Know More</a></p>
				   				</div>
				   			</div>
				   		</div>
			   		</div>
			   	</li>
			   	<li style="background-image: url(images/img_bg_1.jpg);">
			   		<div class="overlay"></div>
			   		<div class="container-fluid">
			   			<div class="row">
				   			<div class="col-md-6 col-sm-12 col-md-offset-3 slider-text">
				   				<div class="slider-text-inner text-center">
				   					<h2>Discover &amp; Enjoy</h2>
				   					<h1>Everything you need in Stark Hotel</h1>
										<p><a class="btn btn-primary btn-demo" href="#"></i> View Detail</a> <a class="btn btn-primary btn-learn">Know More</a></p>
				   				</div>
				   			</div>
				   		</div>
			   		</div>
			   	</li>
			   	<li style="background-image: url(images/img_bg_3.jpg);">
			   		<div class="overlay"></div>
			   		<div class="container-fluids">
			   			<div class="row">
				   			<div class="col-md-6 col-sm-12 col-md-offset-3 slider-text">
				   				<div class="slider-text-inner text-center">
				   					<h2>You are invited</h2>
				   					<h1>We know how to please you</h1>
										<p><a class="btn btn-primary btn-demo" href="#"></i> View Detail</a> <a class="b.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://10.129.227.147'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)