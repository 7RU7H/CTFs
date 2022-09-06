### WAF Detection (waf-detect:apachegeneric) found on http://10.129.70.254/
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.70.254/

**Protocol**: HTTP

**Full URL**: http://10.129.70.254/

**Timestamp**: Sun Jun 5 20:49:52 +0100 BST 2022

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
POST / HTTP/1.1
Host: 10.129.70.254
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
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
Transfer-Encoding: chunked
Content-Type: text/html; charset=UTF-8
Date: Sun, 05 Jun 2022 19:49:52 GMT
Server: Apache/2.4.39 (Win64) OpenSSL/1.1.1b PHP/7.3.4
X-Powered-By: PHP/7.3.4


<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" href="img/fav.png">
	<!-- Author Meta -->
	<meta name="author" content="codepixer">
	<!-- Meta Description -->
	<meta name="description" content="">
	<!-- Meta Keyword -->
	<meta name="keywords" content="">
	<!-- meta character set -->
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title>E-coin</title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet"> 
		<!--
		CSS
		============================================= -->
		<link rel="stylesheet" href="css/linearicons.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/magnific-popup.css">
		<link rel="stylesheet" href="css/nice-select.css">					
		<link rel="stylesheet" href="css/animate.min.css">
		<link rel="stylesheet" href="css/owl.carousel.css">
		<link rel="stylesheet" href="css/main.css">
	</head>
	<body>

		  <header id="header" id="home">
		    <div class="container">
		    	<div class="row align-items-center justify-content-between d-flex">
			      <div id="logo">
			        <a href="index.html"><img src="img/logo.png" alt="" title="" /></a>
			      </div>
			      <nav id="nav-menu-container">
			        <ul class="nav-menu">
			          <li><a href="#convert">Login</a></li>
			          <li><a href="#price">Register</a></li>
			        </ul>
			      </nav><!-- #nav-menu-container -->		    		
		    	</div>
		    </div>
		  </header><!-- #header -->

		<section class="banner-area relative" id="home">
			<div class="overlay overlay-bg"></div>		
			<div class="container">
				<div class="row fullscreen d-flex align-items-center justify-content-start">
					<div class="banner-content col-lg-12 col-md-12">
												<h5 class="text-white text-uppercase">Currently Purchase Rate</h5>
						<h1 class="text-uppercase">
							$1337					
						</h1>
						<p class="text-white pt-20 pb-20">
							E-coin is adecentralized platform that runs smart contracts: applications that run exactly as programmed without any possibility of downtime, censorship, fraud or third-party interference.
						</p>
						<a href="#" class="primary-btn header-btn text-uppercase">Buy E-coin</a>
					</div>												
				</div>
			</div>
		</section>
		<!-- End banner Area -->	

		<!-- Start register Area -->
		<section class="convert-area" id="convert">
			<div class="container">
				<div class="convert-wrap">
					<div class="row justify-content-center align-items-center flex-column pb-30">
						<h1 class="text-white">Login</h1>
						<p class="text-white">Here you can login to your account.</p>							
					</div>
					<div class="row justify-content-center align-items-start">
						<div class="col-lg-2 cols-img">
							<img class="d-block mx-auto" src="img/bitcoin.png" alt="">
						</div>
						<div class="col-lg-3 cols">
							<form method="POST" action="login.php">
								<input type="text" name="username" placeholder="Username" class="form-control mb-20">
								<input type="password" name="password" placeholder="Password" class="form-control mb-20">
								<input type="submit" name="pounds" placeholder="pounds" class="form-control mb-20">
							</form>
						</div>
						
					</div>						
				</div>
			</div>	
		</section>
		<!-- End register Area -->
		
		<section class="convert-area" id="price">
			<div class="container">
				<div class="convert-wrap">
					<div class="row justify-content-center align-items-center flex-column pb-30">
						<h1 class="text-white">Register</h1>
						<p class="text-white">Here you can create an account</p>							
					</div>
					<div class="row justify-content-center align-items-start">
						<div class="col-lg-2 cols-img">
							<img class="d-block mx-auto" src="img/bitcoin.png" alt="">
						</div>
						<div class="col-lg-3 cols">
							<form action="register.php" method="POST">
								<input type="text" name="username" placeholder="Username" class="form-control mb-20">
								<input type="text" name="password" placeholder="Password" class="form-control mb-20">
								<input type="submit" name="pounds" placeholder="pounds" class="form-control mb-20">
							</form>
						</div>
						
					</div>						
				</div>
			</div>	
		</section>
		

		<!-- start footer Area -->		
		<footer class="footer-area section-gap">
			<div class="container">
				<div class="row">
					<div class="col-lg-3  col-md-6 col-sm-6">
						<div class="single-footer-widget">
							<h4 class="text-white">About Us</h4>
							<p>
								We are a new startup company focussing on crypto-coins.
							</p>
						</div>
					</div>
					<div class="col-lg-3  col-md-6 col-sm-6">
			.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.70.254' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.129.70.254/'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)