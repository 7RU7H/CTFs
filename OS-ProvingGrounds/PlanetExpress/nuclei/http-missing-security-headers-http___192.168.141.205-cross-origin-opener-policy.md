### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-opener-policy) found on http://192.168.141.205
---
**Details**: **http-missing-security-headers:cross-origin-opener-policy**  matched at http://192.168.141.205

**Protocol**: HTTP

**Full URL**: http://192.168.141.205

**Timestamp**: Tue Sep 20 19:04:08 +0100 BST 2022

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
Host: 192.168.141.205
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Tue, 20 Sep 2022 18:04:08 GMT
Server: Apache/2.4.38 (Debian)
Vary: Accept-Encoding

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PlanetExpress - Coming Soon !</title>
	<meta name="description" content="" />
	<meta name="generator" content="Pico CMS" />
  	<!-- Facebook and Twitter integration -->
	<meta property="og:title" content="PlanetExpress"/>
	<meta property="og:image" content="http://192.168.141.205/themes/launch/images/img_bg_1.png"/>
	<meta property="og:url" content="http://192.168.141.205"/>
	<meta property="og:site_name" content=""/>
	<meta property="og:description" content=""/>
	<meta name="twitter:title" content="PlanetExpress" />
	<meta name="twitter:image" content="http://192.168.141.205/themes/launch/images/img_bg_1.png" />
	<meta name="twitter:url" content="http://192.168.141.205" />
	<meta name="twitter:card" content="summary_large_image" />

	<link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,600,400italic,700' rel='stylesheet' type='text/css'>
	
	<!-- Animate.css -->
	<link rel="stylesheet" href="http://192.168.141.205/themes/launch/css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="http://192.168.141.205/themes/launch/css/icomoon.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="http://192.168.141.205/themes/launch/css/bootstrap.css">
	<!-- Theme style  -->
	<link rel="stylesheet" href="http://192.168.141.205/themes/launch/css/style.css">

	<!-- Modernizr JS -->
	<script src="http://192.168.141.205/themes/launch/js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="http://192.168.141.205/themes/launch/js/respond.min.js"></script>
	<![endif]-->
	<script type="application/ld+json">
		{
		  "@context" : "http://schema.org",
		  "@type" : "WebSite",
		  "name" : "PlanetExpress",
		  "url" : "http://192.168.141.205"
		}
	</script>
	</head>
	<body>
		
	<div class="fh5co-loader"></div>
	
	<div id="page">
	<nav class="fh5co-nav" role="navigation">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 text-center">
					<div id="fh5co-logo"><a href="./">PlanetExpress<strong>.</strong></a></div>
				</div>
			</div>
		</div>
	</nav>

	<header id="fh5co-header" class="fh5co-cover" role="banner" style="background-image:url(http://192.168.141.205/themes/launch/images/img_bg_1.png);" data-stellar-background-ratio="0.5">
		<div class="overlay"></div>
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 text-center">
					<div class="display-t">
						<div class="display-tc animate-box" data-animate-effect="fadeIn">
							<h1>We Are Coming Very Soon!</h1>
							<h2>Quality Takes Time...But It Will Be Well Worth The Wait</h2>
							<div class="simply-countdown simply-countdown-one"></div>

							<div class="row">
								<h2>Notify me when it's ready</h2>
								<form class="form-inline" id="fh5co-header-subscribe" method="post" action="/">
									<div class="col-md-12 col-md-offset-0">
										<div class="form-group">
											<input type="email" class="form-control" name="email" id="email" placeholder="Get notified by email">
											<button type="submit" class="btn btn-primary">Send</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<footer id="fh5co-footer" role="contentinfo">
		<div class="container">

			<div class="row copyright">
				<div class="col-md-12 text-center">
					<p>
						<small class="block">&copy; 2021 PlanetExpress. All Rights Reserved.</small> 
						<small class="block">Designed by <a href="http://freehtml5.co/" target="_blank">FREEHTML5.co</a> Developed by <a href="https://besrourms.github.io" target="_blank">Mohamed Safouan Besrour</a></small>
					</p>
				</div>
			</div>

		</div>
	</footer>
	</div>

	<div class="gototop js-top">
		<a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
	</div>
	
	<!-- jQuery -->
	<script src="http://192.168.141.205/themes/launch/js/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script defer src="http://192.168.141.205/themes/launch/js/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script defer src="http://192.168.141.205/themes/launch/js/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script defer src="http://192.168.141.205/themes/launch/js/jquery.waypoints.min.js"></script>

	<!-- Stellar -->
	<script defer src="http://192.168.141.205/themes/launch/js/jquery.stellar.min.js"></script>

	<!-- Count Down -->
	<script src="http://192.168.141.205/themes/launch/js/simplyCountdown.js"></script>
	<!-- Main -->
	<s.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36' 'http://192.168.141.205'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)