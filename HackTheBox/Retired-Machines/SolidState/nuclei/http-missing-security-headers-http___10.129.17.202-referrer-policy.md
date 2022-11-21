### HTTP Missing Security Headers (http-missing-security-headers:referrer-policy) found on http://10.129.17.202
---
**Details**: **http-missing-security-headers:referrer-policy**  matched at http://10.129.17.202

**Protocol**: HTTP

**Full URL**: http://10.129.17.202

**Timestamp**: Mon Nov 21 13:51:02 +0000 GMT 2022

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
Host: 10.129.17.202
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36
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
Date: Mon, 21 Nov 2022 13:51:02 GMT
Etag: "1e60-5610a1e7a4c9b-gzip"
Last-Modified: Sat, 23 Dec 2017 23:16:12 GMT
Server: Apache/2.4.25 (Debian)
Vary: Accept-Encoding

<!DOCTYPE HTML>

<html>
	<head>
		<title>Home - Solid State Security</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
	</head>
	<body>

		<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h1><a href="index.html">Solid State Security</a></h1>
						<nav>
							<a href="#menu">Menu</a>
						</nav>
					</header>

				<!-- Menu -->
					<nav id="menu">
						<div class="inner">
							<h2>Menu</h2>
							<ul class="links">
								<li><a href="index.html">Home</a></li>
								<li><a href="services.html">Services</a></li>
								<li><a href="about.html">About Us</a></li>

							</ul>
							<a href="#" class="close">Close</a>
						</div>
					</nav>

				<!-- Banner -->
					<section id="banner">
						<div class="inner">
							<div class="logo"><span class="icon fa-diamond"></span></div>
							<h2>Solid State Security</h2>
							<p><b>Award Winning Security Team</b></p>
							<p> Solid State's testers hold the highest technical qualifications available and provide risk based, real world, human led testing services. This includes testing services ranging from the advanced techniques through to the broader assurances of Cyber Essentials, social engineering, red teaming exercises, and vulnerability assessments.
							</p>
						</div>
					</section>

				<!-- Wrapper -->
					<section id="wrapper">

						<!-- One -->
							<section id="one" class="wrapper spotlight style1">
								<div class="inner">
									<a href="#" class="image"><img src="images/pic01.jpg" alt="" /></a>
									<div class="content">
										<h2 class="major">Experience and Skills</h2>
										<p>Solid State Security core foundation is based on a deep understanding of security architecture, and application programming. We also recognise that cyber also blends people, process and technology to create a unique intersection of vulnerability. As a consequence, we have strong social engineering and business logic skills, to help us address the risk from Cyber head on..</p>
										
									</div>
								</div>
							</section>

						<!-- Two -->
							<section id="two" class="wrapper alt spotlight style2">
								<div class="inner">
									<a href="#" class="image"><img src="images/pic02.jpg" alt="" /></a>
									<div class="content">
										<h2 class="major">Security Consultants</h2>
										<p>Our consultants hold the very highest of security accreditations and experience. We hold certifications from CREST, Ofsec, SANS and ISC2 and have experience that is tailored to specific industries and verticals. We frequently talk on the global conference circuits and our dedicated research team actively identifies many high grade zero-day vulnerabilities that span Operating systems, Applications, hypervisors, and personal security products.</p>
										
									</div>
								</div>
							</section>

						<!-- Three -->
							<section id="three" class="wrapper spotlight style3">
								<div class="inner">
									<a href="#" class="image"><img src="images/pic03.jpg" alt="" /></a>
									<div class="content">
										<h2 class="major">Our Security Approach</h2>
										<p>All of our Penetration Testing is underpinned by a strong understanding of the cyber threat landscape.  We actively track geopolitical threats, and have a deep understanding of their techniques, tactics and procedures, (TTP’s).  As a consequence we have the ability to tailor all of our security testing engagements to emulate real threats that are known to be targeting specific industries and geographic locations.</p>
										
									</div>
								</div>
							</section>

						<!-- Four -->
							<section id="four" class="wrapper alt style1">
								<div class="inner">
									<h2 class="major">Services</h2>
									<p>We deliver a great client experience for the below services</p>
									<section class="features">
										<article>
											<a href="#" class="image"><img src="images/pic04.jpg" alt="" /></a>
											<h3 class="major">Penetration Testing</h3>
											<p>Comprehensive risk, vulnerability, and penetration testing intelligence with prioritized risk-rated recommendations.</p>
											<a href="services.html" class="special">Learn more</a>
										</article>
										<article>
											<a href="#" class="image"><img src="images/pic05.jpg" alt="" /></a>.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36' 'http://10.129.17.202'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)