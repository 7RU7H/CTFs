### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on http://10.129.198.211
---
**Details**: **http-missing-security-headers:cross-origin-resource-policy**  matched at http://10.129.198.211

**Protocol**: HTTP

**Full URL**: http://10.129.198.211

**Timestamp**: Tue Apr 25 14:54:05 +0100 BST 2023

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
Host: 10.129.198.211
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html; charset=utf-8
Date: Tue, 25 Apr 2023 13:54:04 GMT
Expires: Wed, 17 Aug 2005 00:00:00 GMT
Last-Modified: Tue, 25 Apr 2023 13:54:04 GMT
Pragma: no-cache
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: c0548020854924e0aecd05ed9f5b672b=ugetrna0l7nh29ou7s2vj010j5; path=/; HttpOnly
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	<base href="http://10.129.198.211/" />
	<meta name="description" content="best curling site on the planet!" />
	<meta name="generator" content="Joomla! - Open Source Content Management" />
	<title>Home</title>
	<link href="/index.php?format=feed&amp;type=rss" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
	<link href="/index.php?format=feed&amp;type=atom" rel="alternate" type="application/atom+xml" title="Atom 1.0" />
	<link href="/templates/protostar/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link href="/templates/protostar/css/template.css?b6bf078482bc6a711b54fa9e74e19603" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
	<style>

	h1, h2, h3, h4, h5, h6, .site-title {
		font-family: 'Open Sans', sans-serif;
	}
	</style>
	<script type="application/json" class="joomla-script-options new">{"csrf.token":"9916601f2bd43dd12588f41d69356528","system.paths":{"root":"","base":""},"system.keepalive":{"interval":840000,"uri":"\/index.php\/component\/ajax\/?format=json"}}</script>
	<script src="/media/jui/js/jquery.min.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script src="/media/jui/js/jquery-noconflict.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script src="/media/jui/js/jquery-migrate.min.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script src="/media/system/js/caption.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script src="/media/jui/js/bootstrap.min.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script src="/templates/protostar/js/template.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<!--[if lt IE 9]><script src="/media/jui/js/html5.js?b6bf078482bc6a711b54fa9e74e19603"></script><![endif]-->
	<script src="/media/system/js/core.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<!--[if lt IE 9]><script src="/media/system/js/polyfill.event.js?b6bf078482bc6a711b54fa9e74e19603"></script><![endif]-->
	<script src="/media/system/js/keepalive.js?b6bf078482bc6a711b54fa9e74e19603"></script>
	<script>
jQuery(window).on('load',  function() {
				new JCaption('img.caption');
			});jQuery(function($){ initTooltips(); $("body").on("subform-row-add", initTooltips); function initTooltips (event, container) { container = container || document;$(container).find(".hasTooltip").tooltip({"html": true,"container": "body"});} });
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
						<span class="site-title" title="Cewl Curling site!">Cewl Curling site!</span>											</a>
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
			<a href="/index.php/2-uncategorised/3-what-s-the-object-of-curling" itemprop="url">
			What's the object of curling?		</a>
		</h2>


	
<div class="icons">
	
					<div class="btn-group pull-right">
				<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton-3" aria-label="User tools"
				data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="icon-cog" aria-hidden="true"></span>
					<span class="caret" aria-hidden="true"></span>
				</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-3">
											<li class="print-icon"> <a href="/index.php/2-uncategorised/3-what-s-the-object-of-curling?tmpl=component&amp;print=1" title="Print article < What&#039;s the object of curling? >" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow">			<span class="icon-print" aria-hidden="true"></span>
		Print	</a> </li>
																			</ul>
			</div>
		
	</div>



			<dl class="article-info muted">

		
			<dt class="article-info-term">
									Details		.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://10.129.198.211'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)