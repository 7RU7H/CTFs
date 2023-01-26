### Host Header Injection (host-header-injection) found on http://10.129.239.84
---
**Details**: **host-header-injection**  matched at http://10.129.239.84

**Protocol**: HTTP

**Full URL**: http://10.129.239.84

**Timestamp**: Wed Jan 25 14:19:24 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Host Header Injection |
| Authors | princechaddha |
| Tags | hostheader-injection, generic |
| Severity | info |
| Description | HTTP header injection is a general class of web application security vulnerability which occurs when Hypertext Transfer Protocol headers are dynamically generated based on user input. |

**Request**
```http
GET / HTTP/1.1
Host: 2Kp0dwmSRUEzm3d4x8sIFK2cD9X.tld
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
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html; charset=utf-8
Date: Wed, 25 Jan 2023 14:19:22 GMT
Expires: Wed, 17 Aug 2005 00:00:00 GMT
Last-Modified: Wed, 25 Jan 2023 14:19:22 GMT
Pragma: no-cache
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: c0548020854924e0aecd05ed9f5b672b=jee6j46b8vpnnoqfehvnn6io2b; path=/; HttpOnly
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	<base href="http://2Kp0dwmSRUEzm3d4x8sIFK2cD9X.tld/" />
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
	<script type="application/json" class="joomla-script-options new">{"csrf.token":"42847a1863aa9b0d261446e8df15eca5","system.paths":{"root":"","base":""},"system.keepalive":{"interval":840000,"uri":"\/index.php\/component\/ajax\/?format=json"}}</script>
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
	.... Truncated ....
```

References: 
- https://portswigger.net/web-security/host-header
- https://portswigger.net/web-security/host-header/exploiting
- https://www.acunetix.com/blog/articles/automated-detection-of-host-header-attacks/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Host: 2Kp0dwmSRUEzm3d4x8sIFK2cD9X.tld' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36' 'http://10.129.239.84'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)