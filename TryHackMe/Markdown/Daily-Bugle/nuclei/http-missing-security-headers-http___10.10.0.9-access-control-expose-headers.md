### HTTP Missing Security Headers (http-missing-security-headers:access-control-expose-headers) found on http://10.10.0.9
---
**Details**: **http-missing-security-headers:access-control-expose-headers**  matched at http://10.10.0.9

**Protocol**: HTTP

**Full URL**: http://10.10.0.9

**Timestamp**: Fri Sep 23 18:46:03 +0100 BST 2022

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
Host: 10.10.0.9
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36
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
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html; charset=utf-8
Date: Fri, 23 Sep 2022 17:46:00 GMT
Expires: Wed, 17 Aug 2005 00:00:00 GMT
Last-Modified: Fri, 23 Sep 2022 17:46:02 GMT
Pragma: no-cache
Server: Apache/2.4.6 (CentOS) PHP/5.6.40
Set-Cookie: eaa83fe8b963ab08ce9ab7d4a798de05=br1f4q51e39oilacqh4m5s5m67; path=/; HttpOnly
X-Powered-By: PHP/5.6.40

<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	<base href="http://10.10.0.9/" />
	<meta name="description" content="New York City tabloid newspaper" />
	<meta name="generator" content="Joomla! - Open Source Content Management" />
	<title>Home</title>
	<link href="/index.php?format=feed&amp;type=rss" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
	<link href="/index.php?format=feed&amp;type=atom" rel="alternate" type="application/atom+xml" title="Atom 1.0" />
	<link href="/templates/protostar/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link href="/templates/protostar/css/template.css?787ff2be3d28e0eefd8e0930f878dbca" rel="stylesheet" />
	<link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
	<style>

	h1, h2, h3, h4, h5, h6, .site-title {
		font-family: 'Open Sans', sans-serif;
	}
	body.site {
		border-top: 3px solid #000000;
		background-color: #f4f6f7;
	}
	a {
		color: #000000;
	}
	.nav-list > .active > a,
	.nav-list > .active > a:hover,
	.dropdown-menu li > a:hover,
	.dropdown-menu .active > a,
	.dropdown-menu .active > a:hover,
	.nav-pills > .active > a,
	.nav-pills > .active > a:hover,
	.btn-primary {
		background: #000000;
	}
	</style>
	<script type="application/json" class="joomla-script-options new">{"system.keepalive":{"interval":840000,"uri":"\/index.php\/component\/ajax\/?format=json"}}</script>
	<script src="/media/jui/js/jquery.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/jquery-noconflict.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/jquery-migrate.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/system/js/caption.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/bootstrap.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/templates/protostar/js/template.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<!--[if lt IE 9]><script src="/media/jui/js/html5.js?787ff2be3d28e0eefd8e0930f878dbca"></script><![endif]-->
	<script src="/media/system/js/core.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<!--[if lt IE 9]><script src="/media/system/js/polyfill.event.js?787ff2be3d28e0eefd8e0930f878dbca"></script><![endif]-->
	<script src="/media/system/js/keepalive.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script>
jQuery(window).on('load',  function() {
				new JCaption('img.caption');
			});jQuery(function($){ $(".hasTooltip").tooltip({"html": true,"container": "body"}); });
	</script>

</head>
<body class="site com_content view-featured no-layout no-task itemid-101">
	<!-- Body -->
	<div class="body" id="top">
		<div class="container">
			<!-- Header -->
			<header class="header" role="banner">
				<div class="header-inner clearfix">
					<a class="brand pull-left" href="/">
						<img src="http://10.10.0.9/images/logo.png" alt="The Daily Bugle" />											</a>
					<div class="header-search pull-right">
						
					</div>
				</div>
			</header>
						
			<div class="row-fluid">
								<main id="content" role="main" class="span9">
					<!-- Begin Content -->
					
					<div id="system-message-container">
	</div>

					<div class="blog-featured" itemscope itemtype="https://schema.org/Blog">
<div class="page-header">
	<h1>
	Home	</h1>
</div>

<div class="items-leading clearfix">
			<div class="leading-0 clearfix"
			itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			

	<h2 class="item-title" itemprop="headline">
			<a href="/index.php/2-uncategorised/1-spider-man-robs-bank" itemprop="url">
			Spider-Man robs bank!		</a>
		</h2>


	
<div class="icons">
	
					<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span><span class="caret"></span> </a>
								<ul class="dropdown-menu">
											<li class="print-icon"> <a href="/index.php/2-uncategorised/1-spider-man-robs-bank?tmpl=component&amp;print=1&amp;page=" title="Print article < Spider-Man robs bank! >" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow">			<span class="icon-print"></span>
		Print	</a> </li>
																<li class="email-icon"> <a href="/index.php/component/mailto/?tmpl=component&amp;template=protostar&amp;link=e00b446361a5c54789c907eac4b5a3703c0d3319" title="Email this link to a friend" onclick="window.open(this.href,'win2','width=400,h.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36' 'http://10.10.0.9'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)