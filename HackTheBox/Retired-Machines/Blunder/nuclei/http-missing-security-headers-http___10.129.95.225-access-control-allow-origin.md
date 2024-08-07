### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-origin) found on http://10.129.95.225
---
**Details**: **http-missing-security-headers:access-control-allow-origin**  matched at http://10.129.95.225

**Protocol**: HTTP

**Full URL**: http://10.129.95.225

**Timestamp**: Tue Aug 16 20:43:50 +0100 BST 2022

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
Host: 10.129.95.225
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Tue, 16 Aug 2022 19:43:49 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding
X-Powered-By: Bludit

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Blunder">

<!-- Dynamic title tag -->
<title>Blunder | A blunder of interesting facts</title>

<!-- Dynamic description tag -->
<meta name="description" content="HackTheBox">

<!-- Include Favicon -->
<link rel="shortcut icon" href="http://10.129.95.225/bl-themes/blogx/img/favicon.png" type="image/png">

<!-- Include Bootstrap CSS file bootstrap.css -->
<link rel="stylesheet" type="text/css" href="http://10.129.95.225/bl-kernel/css/bootstrap.min.css?version=3.9.2">

<!-- Include CSS Styles from this theme -->
<link rel="stylesheet" type="text/css" href="http://10.129.95.225/bl-themes/blogx/css/style.css?version=3.9.2">

<!-- Load Plugins: Site head -->

<!-- Robots plugin -->
</head>
<body>

	<!-- Load Plugins: Site Body Begin -->
	
	<!-- Navbar -->
	<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark text-uppercase">
	<div class="container">
		<a class="navbar-brand" href="http://10.129.95.225/">
			<span class="text-white">A blunder of interesting facts</span>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto">

				<!-- Static pages -->
								<li class="nav-item">
					<a class="nav-link" href="http://10.129.95.225/about">About</a>
				</li>
				
				<!-- Social Networks -->
				
			</ul>
		</div>
	</div>
</nav>

	<!-- Content -->
	<div class="container">
		<div class="row">

			<!-- Blog Posts -->
			<div class="col-md-9">
			
<!-- Post -->
<div class="card my-5 border-0">

	<!-- Load Plugins: Page Begin -->
	
	<!-- Cover image -->
	
	<div class="card-body p-0">
		<!-- Title -->
		<a class="text-dark" href="http://10.129.95.225/stephen-king-0">
			<h2 class="title">Stephen King</h2>
		</a>

		<!-- Creation date -->
		<h6 class="card-subtitle mb-3 text-muted">November 27, 2019 - Reading time: ~1 minute</h6>

		<!-- Breaked content -->
		<p>Stephen Edwin King (born September 21, 1947) is an American author of horror, supernatural fiction, suspense, and fantasy novels. His books have sold more than 350 million copies, many of which have been adapted into feature films, miniseries, television series, and comic books. King has published 61 novels (including seven under the pen name Richard Bachman) and six non-fiction books.He has written approximately 200 short stories,most of which have been published in book collections.</p>
<p>King has received Bram Stoker Awards, World Fantasy Awards, and British Fantasy Society Awards. In 2003, the National Book Foundation awarded him the Medal for Distinguished Contribution to American Letters. He has created probably the best fictional character RolandDeschain in The Dark tower series. He has also received awards for his contribution to literature for his entire <i>oeuvre</i>, such as the World Fantasy Award for Life Achievement (2004) and the Grand Master Award from the Mystery Writers of America (2007). In 2015, King was awarded with a National Medal of Arts from the United States National Endowment for the Arts for his contributions to literature. He has been described as the "King of Horror".</p>
		<!-- "Read more" button -->
		
	</div>

	<!-- Load Plugins: Page End -->
	
</div>
<hr>
<!-- Post -->
<div class="card my-5 border-0">

	<!-- Load Plugins: Page Begin -->
	
	<!-- Cover image -->
	
	<div class="card-body p-0">
		<!-- Title -->
		<a class="text-dark" href="http://10.129.95.225/stadia">
			<h2 class="title">Stadia</h2>
		</a>

		<!-- Creation date -->
		<h6 class="card-subtitle mb-3 text-muted">November 27, 2019 - Reading time: ~1 minute</h6>

		<!-- Breaked content -->
		<p><b>Google Stadia</b> is a cloud gaming service operated by Google. It is said to be capable of streaming video games up to 4K resolution at 60 frames per second with support for high-dynamic-range, to players via the company's numerous data centers across the globe, provided they are using a sufficiently high-speed Internet connection. It is accessible through the Google Chrome web browser on desktop computers, or through smartphones, tablets, smart televisions, digital media players, and Chromecast.<sup id="cite_ref-2" class="reference"></sup></p>
<p>The service is integrated with YouTube, and its "state share" feature allows viewers of a Stadia stream to launch a game on the service on the same save state as the streamer. This has been used as a selling point for the service. It is compatible with HID class USB controllers, though a proprietary controller manufactured by Google with a direct Wi-Fi link to data centers is avai.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://10.129.95.225'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)