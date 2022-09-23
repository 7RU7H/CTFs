### HTTP Missing Security Headers (http-missing-security-headers:x-frame-options) found on http://192.168.141.125:8080
---
**Details**: **http-missing-security-headers:x-frame-options**  matched at http://192.168.141.125:8080

**Protocol**: HTTP

**Full URL**: http://192.168.141.125:8080

**Timestamp**: Fri Sep 23 12:25:21 +0100 BST 2022

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
Host: 192.168.141.125:8080
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 
Connection: close
Content-Length: 3762
Content-Language: en
Content-Type: text/html;charset=UTF-8
Date: Fri, 23 Sep 2022 11:25:18 GMT

<!DOCTYPE HTML>
<!--
	Minimaxing by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>My Haikus</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/css/main.css" />
	</head>
	<body>
		<div id="page-wrapper">

			<!-- Header -->
			
				<div id="header-wrapper">
					<div class="container">
						<div class="row">
							<div class="col-12">

								<header id="header">
									<h1><a href="/" id="logo">My Haikus</a></h1>
								</header>

							</div>
						</div>
					</div>
				</div>
				

			
				<div id="main">
					<div class="container">
	

<h1>My Haikus</h1>

<div class="articles">


		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/the-taste-of-rain">The Taste of Rain</a></h2>
				<div class="article-meta">By  <strong>James</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Jack Kerouac
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/in-a-station-of-the-metro">In a Station of the Metro</a></h2>
				<div class="article-meta">By  <strong>James</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Ezra Pound
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/over-the-wintry">Over the Wintry</a></h2>
				<div class="article-meta">By  <strong>Julie</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Natsume Soseki
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/a-poppy-blooms">A Poppy Blooms</a></h2>
				<div class="article-meta">By  <strong>Julie</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Katsushika Hokusai
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/lighting-one-candle">Lighting One Candle</a></h2>
				<div class="article-meta">By  <strong>Jennifer</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Yosa Buson
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/a-world-of-dew">A World of Dew</a></h2>
				<div class="article-meta">By  <strong>Jennifer</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Kobayashi Issa
			</div>
		</section>
		<section>
			<header class="article-header">
				<h2 class="article-title"><a href="/article/the-old-pond">The Old Pond</a></h2>
				<div class="article-meta">By  <strong>Richard</strong>, on <strong>2022-02-15 15th 2022</strong></div>
			</header>
			<div class="article-headline">
				Matsuo Basho
			</div>
		</section>
</div>

</div>
				</div>

			<!-- Footer -->
				<div id="footer-wrapper">
					<div class="container">
						<div class="row">
							<div class="col-12">

								<div id="copyright">
									&copy; Untitled. All rights reserved. | Design: <a href="http://html5up.net">HTML5 UP</a>
								</div>

							</div>
						</div>
					</div>
				</div>

		</div>

		<!-- Scripts -->
			<script src="/js/jquery.min.js"></script>
			<script src="/js/browser.min.js"></script>
			<script src="/js/breakpoints.min.js"></script>
			<script src="/js/util.js"></script>
			<script src="/js/main.js"></script>

	</body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://192.168.141.125:8080'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)