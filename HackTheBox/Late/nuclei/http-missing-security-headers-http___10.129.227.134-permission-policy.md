### HTTP Missing Security Headers (http-missing-security-headers:permission-policy) found on http://10.129.227.134
---
**Details**: **http-missing-security-headers:permission-policy**  matched at http://10.129.227.134

**Protocol**: HTTP

**Full URL**: http://10.129.227.134

**Timestamp**: Wed May 4 08:28:57 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki |
| Tags | misconfig, generic |
| Severity | info |
| Description | It searches for missing security headers, but obviously, could be so less generic and could be useless for Bug Bounty. |

**Request**

```http
GET / HTTP/1.1
Host: 10.129.227.134
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
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
Content-Type: text/html
Date: Wed, 04 May 2022 07:28:55 GMT
Etag: W/"624af46d-24f5"
Last-Modified: Mon, 04 Apr 2022 13:36:45 GMT
Server: nginx/1.14.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport"    content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author"      content="Sergey Pozhilov (GetTemplate.com)">
	
	<title>Late - Best online image tools</title>

	<link rel="shortcut icon" href="assets/images/gt_favicon.png">
	
	<link rel="stylesheet" media="screen" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!-- Custom styles for our template -->
	<link rel="stylesheet" href="assets/css/bootstrap-theme.css" media="screen" >
	<link rel="stylesheet" href="assets/css/main.css">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="assets/js/html5shiv.js"></script>
	<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body class="home">
	<!-- Fixed navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top headroom" >
		<div class="container">
			<div class="navbar-header">
				<!-- Button for smallest screens -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
				<a class="navbar-brand" href="index.html"><p>Late</p></a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav pull-right">
					<li class="active"><a href="/index.html">Home</a></li>
					<li><a href="contact.html">Contact</a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div> 
	<!-- /.navbar -->

	<!-- Header -->
	<header id="head">
		<div class="container">
			<div class="row">
				<h1 class="lead"><font color='black'><bold>World's Simplest Image Utilities</bold></font></h1>
				<p class="tagline">Online image tools is a collection of useful image utilities for working with graphics files</p>
				<p><a class="btn btn-default btn-lg" role="button">MORE INFO</a></p>
			</div>
		</div>
	</header>
	<!-- /Header -->

	<!-- Intro -->
	<div class="container text-center">
		<br> <br>
		<h2 class="thin">Fast and simple Edit Tools</h2>
		<p class="text-muted">
			Free to edit photos with Late photo editor in just a few clicks. It covers all online photo editing tools, so you can crop images, resize images, add text to photos, even make photo collages, and create graphic designs easily.
		</p>
	</div>
	<!-- /Intro-->
		
	<!-- Highlights - jumbotron -->
	<div class="jumbotron top-space">
		<div class="container">
			
			<h3 class="text-center thin">Popular features of online photo editor</h3>
			
			<div class="row">
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-body text-center">
						<p>The Online Image Editor is the easiest method to edit your images in a clean and fast manner. It works on all formats like: PNG, JPG/JPEG. You can even upload your own fonts to the editor and use them to add text to a photo, with your OWN fonts. And did I already mention that it is 100% free to use?</p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-body text-center">
						<p>Editing photos has never been so easy. The Online Image Editor lets you edit photos in a clean and fast manner from PC, Laptop and mobile device. All you need is an internet connection. </p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-body text-center">
						<p>You've never seen a photo editor like this! Loads of great tools to help you perfect your photos, including effects, background changers, and much more. Enhance, retouch portraits, remove backgrounds, and apply effects. Change your photo into an artistic masterpiece with Late.</p>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 highlight">
					<div class="h-body text-center">
						<p>With the Text Tool you can add text to your images. Also add text to animated images is simple and fast. With extra options you can add a border around your text and make the text follow an arc path so it looks like text around a cricle. With the shadow option you can add different kind of shadow colours and blurs to the text.</p>
					</div>
				</div>
			</div> <!-- /row  -->
		
		</div>
	</div>
	<!-- /Highlights -->

	<!-- container -->
	<div class="container">

		<h2 class="text-center top-space">Frequently Asked Questions</h2>
		<br>

		<div class="row">
			<div class="col-sm-6">
				<h3>What's photo editing?</h3>
				<p>Photo editing is a fast digital way to perfect an image. Although cameras and phones are great devices for taking photos, sometimes they are not t.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://10.129.227.134'
```
---
Generated by [Nuclei 2.6.9](https://github.com/projectdiscovery/nuclei)