### HTTP Missing Security Headers (http-missing-security-headers:referrer-policy) found on analysis.htb

----
**Details**: **http-missing-security-headers:referrer-policy** matched at analysis.htb

**Protocol**: HTTP

**Full URL**: http://analysis.htb

**Timestamp**: Sat Jan 20 20:03:37 +0000 GMT 2024

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
Host: analysis.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
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
Date: Sat, 20 Jan 2024 20:03:25 GMT
Etag: "80cfbf817db1d91:0"
Last-Modified: Sat, 08 Jul 2023 09:20:59 GMT
Server: Microsoft-IIS/10.0
Vary: Accept-Encoding

<html lang="en"><head>
	<meta charset="UTF-8">
	<title></title>
	<link href="http://fonts.googleapis.com/css?family=Sanchez" rel="stylesheet" type="text/css">
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link href="css/layout.css" rel="stylesheet" type="text/css">
	<link href="css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/scripts.js" type="text/javascript"></script><script type="text/javascript" src="js/switcher.js"></script><script type="text/javascript" src="js/bgStretch.js"></script><script type="text/javascript" src="js/forms.js"></script><script type="text/javascript" src="js/jquery.fancybox-1.3.4.pack.js"></script>
	<!--[if lt IE 8]>
	  <div class='alc' style=' clear: both; text-align:center; position: relative; z-index:9999;'>
		<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
		  <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
	   </a>
	 </div>
	<![endif]-->
	<!--[if lt IE 9]><script src="js/html5.js" type="text/javascript"></script>
	<link href="css/ie_style.css" rel="stylesheet" type="text/css" /><![endif]-->
	</head>
	<body style="overflow: auto;">
	<div id="gspinner" class="spinner" style="display: none;"></div>
	<div id="glob">
		<header style="overflow: visible; background-position: 0px -115px; height: 64px;">
			<div class="inner" style="overflow: visible; height: 63px;">
				<h1 style="top: 20px;"><a href="#!/home"><img src="images/logo.png" alt=""><span style="top: -5px; left: 37px;">Analysis</span></a></h1>
				<nav class="">
					<ul>
						<li class="li-1 active"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">Home</span><span class="ico -def"></span><span class="ico -hvr"></span></a></li>
						<li class="li-2"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">About</span><span class="ico -def"></span><span class="ico -hvr"></span></a>
							<ul style="display: none; opacity: 1;">
								<li class=""><a href="#">Our Mission</a>
									<ul style="display: none; opacity: 1;">
										<li><a href="#">Planning</a></li>
										<li><a href="#">Integration</a></li>
										<li><a href="#e">Development</a></li>
										<li><a href="#">Maintaining</a></li>
									</ul>
								</li>
								<li><a href="#">Meet Our Team</a></li>
								<li><a href="#">Standards</a></li>
								<li><a href="#">FAQs</a></li>
							</ul>
						</li>
						<li class="li-3"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">Services</span><span class="ico -def"></span><span class="ico -hvr"></span></a></li>
						<li class="li-4"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">Clients</span><span class="ico -def"></span><span class="ico -hvr"></span></a></li>
						<li class="li-5"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">Media</span><span class="ico -def"></span><span class="ico -hvr"></span></a></li>
						<li class="li-6"><a href="#" style="overflow: visible; height: 63px;"><span class="lbl">Mail</span><span class="ico -def"></span><span class="ico -hvr"></span></a></li>
					</ul>
				</nav>
			</div>
		</header>
		<div class="slogans" style="bottom: 62px;">
			<ul>
				<li style="display: block;">Cybersecurity. Everywhere. Everyday.</li>
				<li style="display: none;">We provide a dedicated SOC for our clients</li>
				<li style="display: none;">Well being of our clients first!</li>
			</ul>
			<a href="#" class="prev"><img src="images/slogans-prev.png" alt=""></a>
			<a href="#" class="next"><img src="images/slogans-next.png" alt=""></a>
		</div>
		<article id="content">
			<ul>
				<li id="about" style="display: none; top: -522px; margin-top: 0px; opacity: 0;">
					<div class="banner">
						Forward-Looking Guidance for Any <br>Business Challenge.
						<a href="#!/readmore" class="btn">Read More</a>
					</div>
					<div class="content">
						<div class="col-1 bordered">
							<h2>Recent News</h2>
							<p>
								<span class="white f12">12, November 2013</span><br>
								Lorem ipsum dolor sit ameconctetuer 
	adipiscing elit. <a href="#">Continue Reading</a>
							</p>
							<p>
								<span class="white f12">13, November 2013</span><br>
								Lorem ipsum dolor sit ameconctetuer 
	adipiscing elit. <a href="#">Continue Reading</a>
							</p>
							<p>
							.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://analysis.htb'
```

----

Generated by [Nuclei v3.1.4](https://github.com/projectdiscovery/nuclei)