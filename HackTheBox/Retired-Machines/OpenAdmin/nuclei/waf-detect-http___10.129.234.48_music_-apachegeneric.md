### WAF Detection (waf-detect:apachegeneric) found on http://10.129.234.48/music
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.234.48/music

**Protocol**: HTTP

**Full URL**: http://10.129.234.48/music/

**Timestamp**: Sun Feb 26 12:48:44 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST /music/ HTTP/1.1
Host: 10.129.234.48
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Sun, 26 Feb 2023 12:48:43 GMT
Etag: "310a-597f2e74f42d4-gzip"
Last-Modified: Fri, 22 Nov 2019 17:40:02 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>Music | NOT LIVE/NOT FOR PRODUCTION USE</title>
	<meta charset="UTF-8">
	<meta name="description" content="Music HTML Template">
	<meta name="keywords" content="music, html">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Favicon -->
	<link href="img/favicon.ico" rel="shortcut icon"/>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">
 
	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" href="css/owl.carousel.min.css"/>
	<link rel="stylesheet" href="css/slicknav.min.css"/>

	<!-- Main Stylesheets -->
	<link rel="stylesheet" href="css/style.css"/>


	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
<body>
	<!-- Page Preloder -->
	<div id="preloder">
		<div class="loader"></div>
	</div>

	<!-- Header section -->
	<header class="header-section clearfix">
		<a href="index.html" class="site-logo">
			<img src="img/logo.png" alt="">
		</a>
		<div class="header-right">
			<a href="#" class="hr-btn">Help</a>
			<span>|</span>
			<div class="user-panel">
				<a href="../ona" class="login">Login</a>
				<a href="" class="register">Create an account</a>
			</div> 
		</div>
		<ul class="main-menu">
			<li><a href="index.html">Home</a></li>
			<li><a href="#">About</a></li>
			<li><a href="#">Pages</a>
				<ul class="sub-menu">
					<li><a href="category.html">Category</a></li>
					<li><a href="playlist.html">Playlist</a></li>
					<li><a href="artist.html">Artist</a></li>
					<li><a href="blog.html">Blog</a></li>
					<li><a href="contact.html">Contact</a></li>
				</ul>
			</li>
			<li><a href="blog.html">News</a></li>
			<li><a href="contact.html">Contact</a></li>
		</ul>
	</header>
	<!-- Header section end -->

	<!-- Hero section -->
	<section class="hero-section">
		<div class="hero-slider owl-carousel">
			<div class="hs-item">
				<div class="container">
					<div class="row">
						<div class="col-lg-6">
							<div class="hs-text">
								<h2><span>Music</span> for everyone.</h2>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. </p>
								<a href="#" class="site-btn">Download Now</a>
								<a href="#" class="site-btn sb-c2">Start free trial</a>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="hr-img">
								<img src="img/hero-bg.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="hs-item">
				<div class="container">
					<div class="row">
						<div class="col-lg-6">
							<div class="hs-text">
								<h2><span>Listen </span> to new music.</h2>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. </p>
								<a href="#" class="site-btn">Download Now</a>
								<a href="#" class="site-btn sb-c2">Start free trial</a>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="hr-img">
								<img src="img/hero-bg.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Hero section end -->

	<!-- Intro section -->
	<section class="intro-section spad">
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="section-title">
						<h2>Unlimited Access to 100K tracks</h2>
					</div>
				</div>
				<div class="col-lg-6">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum sus-pendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
					<a href="#" class="site-btn">Try it now</a>
				</div>
			</div>
		</div>
	</section>
	<!-- Intro section end -->

	<!-- How section -->
	<section class="how-section spad set-bg" data-setbg="img/how-to-bg.jpg">
		<div class="container text-white">
			<div class="section-title">
				<h2>How it works</h2>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="how-item">
						<div class="hi-icon">
							<img src="img/icons/brain.png" alt="">
						</.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.234.48' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://10.129.234.48/music/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)