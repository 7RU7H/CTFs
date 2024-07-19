### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-methods) found on http://metapress.htb
---
**Details**: **http-missing-security-headers:access-control-allow-methods**  matched at http://metapress.htb

**Protocol**: HTTP

**Full URL**: http://metapress.htb

**Timestamp**: Sat Nov 12 12:36:06 +0000 GMT 2022

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
Host: metapress.htb
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2656.18 Safari/537.36
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
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Sat, 12 Nov 2022 12:36:05 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Link: <http://metapress.htb/wp-json/>; rel="https://api.w.org/"
Pragma: no-cache
Server: nginx/1.18.0
Set-Cookie: PHPSESSID=2gfb65cqmg2s29shvcc0q7g20n; path=/
X-Powered-By: PHP/8.0.24

<!doctype html>
<html lang="en-US" >
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>MetaPress &#8211; Official company site</title>
<link rel='dns-prefetch' href='//metapress.htb' />
<link rel='dns-prefetch' href='//s.w.org' />
<link rel="alternate" type="application/rss+xml" title="MetaPress &raquo; Feed" href="http://metapress.htb/feed/" />
<link rel="alternate" type="application/rss+xml" title="MetaPress &raquo; Comments Feed" href="http://metapress.htb/comments/feed/" />
		<script>
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/13.0.1\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/13.0.1\/svg\/","svgExt":".svg","source":{"concatemoji":"http:\/\/metapress.htb\/wp-includes\/js\/wp-emoji-release.min.js?ver=5.6.2"}};
			!function(e,a,t){var n,r,o,i=a.createElement("canvas"),p=i.getContext&&i.getContext("2d");function s(e,t){var a=String.fromCharCode;p.clearRect(0,0,i.width,i.height),p.fillText(a.apply(this,e),0,0);e=i.toDataURL();return p.clearRect(0,0,i.width,i.height),p.fillText(a.apply(this,t),0,0),e===i.toDataURL()}function c(e){var t=a.createElement("script");t.src=e,t.defer=t.type="text/javascript",a.getElementsByTagName("head")[0].appendChild(t)}for(o=Array("flag","emoji"),t.supports={everything:!0,everythingExceptFlag:!0},r=0;r<o.length;r++)t.supports[o[r]]=function(e){if(!p||!p.fillText)return!1;switch(p.textBaseline="top",p.font="600 32px Arial",e){case"flag":return s([127987,65039,8205,9895,65039],[127987,65039,8203,9895,65039])?!1:!s([55356,56826,55356,56819],[55356,56826,8203,55356,56819])&&!s([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]);case"emoji":return!s([55357,56424,8205,55356,57212],[55357,56424,8203,55356,57212])}return!1}(o[r]),t.supports.everything=t.supports.everything&&t.supports[o[r]],"flag"!==o[r]&&(t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&t.supports[o[r]]);t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&!t.supports.flag,t.DOMReady=!1,t.readyCallback=function(){t.DOMReady=!0},t.supports.everything||(n=function(){t.readyCallback()},a.addEventListener?(a.addEventListener("DOMContentLoaded",n,!1),e.addEventListener("load",n,!1)):(e.attachEvent("onload",n),a.attachEvent("onreadystatechange",function(){"complete"===a.readyState&&t.readyCallback()})),(n=t.source||{}).concatemoji?c(n.concatemoji):n.wpemoji&&n.twemoji&&(c(n.twemoji),c(n.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style>
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}
</style>
	<link rel='stylesheet' id='wp-block-library-css'  href='http://metapress.htb/wp-includes/css/dist/block-library/style.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='wp-block-library-theme-css'  href='http://metapress.htb/wp-includes/css/dist/block-library/theme.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='twenty-twenty-one-style-css'  href='http://metapress.htb/wp-content/themes/twentytwentyone/style.css?ver=1.1' media='all' />
<style id='twenty-twenty-one-style-inline-css'>
:root{--global--color-background: #2f303f;--global--color-primary: #fff;--global--color-secondary: #fff;--button--color-background: #fff;--button--color-text-hover: #fff;--table--stripes-border-color: rgba(240, 240, 240, 0.15);--table--stripes-background-color: rgba(240, 240, 240, 0.15);}
</style>
<link rel='stylesheet' id='twenty-twenty-one-print-style-css'  href='http://metapress.htb/wp-content/themes/twentytwentyone/assets/css/print.css?ver=1.1' media='print' />
<link rel="https://api.w.org/" href="http://metapress.htb/wp-json/" /><link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://metapress.htb/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://metapress.htb/wp-includes/wlwmanifest.xml" /> 
<meta name="generator" content="WordPress 5.6.2" />
<style id="custom-background-css">
body.custom-background { background-color: #2f303f; }
</style>
			<style id="wp-custom-css">
			.posted-on { 
  display: none !important; 
}
h1 {
	font-size: 50px !important;
}		</style>
		</head>

<body class="home blog custom-background wp-embed-responsive is-dark-theme no-js hfeed has-main-navigation">
<div id="page" class="site">
	<a class=".... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2656.18 Safari/537.36' 'http://metapress.htb'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)