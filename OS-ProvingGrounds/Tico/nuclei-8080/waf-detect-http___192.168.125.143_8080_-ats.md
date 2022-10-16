### WAF Detection (waf-detect:ats) found on http://192.168.125.143:8080
---
**Details**: **waf-detect:ats**  matched at http://192.168.125.143:8080

**Protocol**: HTTP

**Full URL**: http://192.168.125.143:8080/

**Timestamp**: Sat Oct 15 16:44:57 +0100 BST 2022

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
Host: 192.168.125.143:8080
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 404 Not Found
Connection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=utf-8
Date: Sat, 15 Oct 2022 15:43:47 GMT
Etag: W/"325e-ZM1YJEkeFZSCDd3ggNSxl6D/yng"
Referrer-Policy: strict-origin-when-cross-origin
Set-Cookie: _csrf=FKl6YjHCSNRUGPsVQgBLA6QR; Path=/
Vary: Accept-Encoding
X-Content-Type-Options: nosniff
X-Dns-Prefetch-Control: off
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Powered-By: NodeBB
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html lang="en-GB" data-dir="ltr" style="direction: ltr;"  >
<head>
	<title>Not Found | NodeBB</title>
	<meta name="viewport" content="width&#x3D;device-width, initial-scale&#x3D;1.0" />
	<meta name="content-type" content="text/html; charset=UTF-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta property="og:site_name" content="NodeBB" />
	<meta name="msapplication-badge" content="frequency=30; polling-uri=http://localhost:4567/sitemap.xml" />
	<meta property="og:image" content="http://localhost:4567/assets/logo.png" />
	<meta property="og:image:url" content="http://localhost:4567/assets/logo.png" />
	<meta property="og:image:width" content="128" />
	<meta property="og:image:height" content="128" />
	<meta property="og:title" content="NodeBB" />
	<meta property="og:url" content="http://localhost:4567" />
	
	<link rel="stylesheet" type="text/css" href="/assets/client.css?" />
	<link rel="icon" type="image/x-icon" href="/assets/uploads/system/favicon.ico?v=oljj059n9nk" />
	<link rel="manifest" href="/manifest.json" />
	<link rel="prefetch" href="/assets/src/modules/composer.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/uploads.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/drafts.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/tags.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/categoryList.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/resize.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/src/modules/composer/autocomplete.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/templates/composer.tpl?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/language/en-GB/topic.json?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/language/en-GB/modules.json?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/language/en-GB/tags.json?v=oljj059n9nk" />
	<link rel="prefetch stylesheet" href="/plugins/nodebb-plugin-markdown/styles/railscasts.css" />
	<link rel="prefetch" href="/assets/src/modules/highlight.js?v=oljj059n9nk" />
	<link rel="prefetch" href="/assets/language/en-GB/markdown.json?v=oljj059n9nk" />
	<link rel="stylesheet" href="/plugins/nodebb-plugin-emoji/emoji/styles.css?v=oljj059n9nk" />
	

	<script>
		var RELATIVE_PATH = "";
		var config = JSON.parse('{}');
		var app = {
			template: "404",
			user: JSON.parse('{"uid":0,"username":"Guest","userslug":"","fullname":"Guest","email":"","icon:text":"?","icon:bgColor":"#aaa","groupTitle":"","status":"offline","reputation":0,"email:confirmed":false,"postcount":0,"topiccount":0,"profileviews":0,"banned":0,"banned:expire":0,"joindate":0,"lastonline":0,"lastposttime":0,"followingCount":0,"followerCount":0,"picture":"","groupTitleArray":[],"joindateISO":"","lastonlineISO":"","banned_until":0,"banned_until_readable":"Not Banned","unreadData":{"":{},"new":{},"watched":{},"unreplied":{}},"isAdmin":false,"isGlobalMod":false,"isMod":false,"privileges":{"chat":false,"upload:post:image":false,"upload:post:file":false,"search:content":false,"search:users":false,"search:tags":false,"view:users":true,"view:tags":true,"view:groups":true,"view:users:info":false},"offline":true,"isEmailConfirmSent":false}')
		};
	</script>

	
	
</head>

<body class="page-categories page-status-404 skin-noskin">
	<nav id="menu" class="slideout-menu hidden">
		<div class="menu-profile">
	
</div>

<section class="menu-section" data-section="navigation">
	<h3 class="menu-section-title">Navigation</h3>
	<ul class="menu-section-list"></ul>
</section>


	</nav>
	<nav id="chats-menu" class="slideout-menu hidden">
		
	</nav>

	<main id="panel" class="slideout-panel">
		<nav class="navbar navbar-default navbar-fixed-top header" id="header-menu" component="navbar">
			<div class="container">
							<div class="navbar-header">
				<button type="button" class="navbar-toggle pull-left" id="mobile-menu">
					<span component="notifications/icon" class="notification-icon fa fa-fw fa-bell-o unread-count" data-content="0"></span>
					<i class="fa fa-lg fa-fw fa-bars"></i>
				</button>
				<button type="button" class="navbar-toggle hidden" id="mobile-chats">
					<span component="chat/icon" class="notification-icon fa fa-fw fa-comments unread-count" data-content="0"></span>
					<i class="fa fa-lg fa-comment-o"></i>
				</button>

				
				

				<div component="navbar/title" class="visible-xs hidden">
					<span></span>
				</div>
			</div>

			<div id="nav-dropdown" class="hidden-xs.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 192.168.125.143:8080' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://192.168.125.143:8080/'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)