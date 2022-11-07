### Apache Detection (apache-detect) found on http://10.200.107.33
---
**Details**: **apache-detect**  matched at http://10.200.107.33

**Protocol**: HTTP

**Full URL**: http://10.200.107.33

**Timestamp**: Mon Nov 7 11:58:36 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Apache Detection |
| Authors | philippedelteil |
| Tags | tech, apache |
| Severity | info |
| Description | Some Apache servers have the version on the response header. The OpenSSL version can be also obtained |

**Request**
```http
GET / HTTP/1.1
Host: 10.200.107.33
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Mon, 07 Nov 2022 11:58:37 GMT
Link: <http://www.holo.live/index.php/wp-json/>; rel="https://api.w.org/"
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding
X-Ua-Compatible: IE=edge

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<title>holo.live</title>
<meta name='robots' content='noindex,nofollow' />
<link rel='dns-prefetch' href='//www.holo.live' />
<link rel='dns-prefetch' href='//s.w.org' />
<link rel="alternate" type="application/rss+xml" title="holo.live &raquo; Feed" href="http://www.holo.live/index.php/feed/" />
<link rel="alternate" type="application/rss+xml" title="holo.live &raquo; Comments Feed" href="http://www.holo.live/index.php/comments/feed/" />
		<script type="text/javascript">
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/13.0.0\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/13.0.0\/svg\/","svgExt":".svg","source":{"concatemoji":"http:\/\/www.holo.live\/wp-includes\/js\/wp-emoji-release.min.js?ver=5.5.3"}};
			!function(e,a,t){var r,n,o,i,p=a.createElement("canvas"),s=p.getContext&&p.getContext("2d");function c(e,t){var a=String.fromCharCode;s.clearRect(0,0,p.width,p.height),s.fillText(a.apply(this,e),0,0);var r=p.toDataURL();return s.clearRect(0,0,p.width,p.height),s.fillText(a.apply(this,t),0,0),r===p.toDataURL()}function l(e){if(!s||!s.fillText)return!1;switch(s.textBaseline="top",s.font="600 32px Arial",e){case"flag":return!c([127987,65039,8205,9895,65039],[127987,65039,8203,9895,65039])&&(!c([55356,56826,55356,56819],[55356,56826,8203,55356,56819])&&!c([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]));case"emoji":return!c([55357,56424,8205,55356,57212],[55357,56424,8203,55356,57212])}return!1}function d(e){var t=a.createElement("script");t.src=e,t.defer=t.type="text/javascript",a.getElementsByTagName("head")[0].appendChild(t)}for(i=Array("flag","emoji"),t.supports={everything:!0,everythingExceptFlag:!0},o=0;o<i.length;o++)t.supports[i[o]]=l(i[o]),t.supports.everything=t.supports.everything&&t.supports[i[o]],"flag"!==i[o]&&(t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&t.supports[i[o]]);t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&!t.supports.flag,t.DOMReady=!1,t.readyCallback=function(){t.DOMReady=!0},t.supports.everything||(n=function(){t.readyCallback()},a.addEventListener?(a.addEventListener("DOMContentLoaded",n,!1),e.addEventListener("load",n,!1)):(e.attachEvent("onload",n),a.attachEvent("onreadystatechange",function(){"complete"===a.readyState&&t.readyCallback()})),(r=t.source||{}).concatemoji?d(r.concatemoji):r.wpemoji&&r.twemoji&&(d(r.twemoji),d(r.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style type="text/css">
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
	<link rel='stylesheet' id='wp-block-library-css'  href='http://www.holo.live/wp-includes/css/dist/block-library/style.min.css?ver=5.5.3' type='text/css' media='all' />
<link rel='stylesheet' id='generate-style-css'  href='http://www.holo.live/wp-content/themes/generatepress/css/all.min.css?ver=2.4.2' type='text/css' media='all' />
<style id='generate-style-inline-css' type='text/css'>
body{background-color:#efefef;color:#3a3a3a;}a, a:visited{color:#1e73be;}a:hover, a:focus, a:active{color:#000000;}body .grid-container{max-width:2000px;}.wp-block-group__inner-container{max-width:2000px;margin-left:auto;margin-right:auto;}@media (max-width: 1650px) and (min-width: 769px){.inside-header{display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;-ms-flex-align:center;align-items:center;}.site-logo, .site-branding{margin-bottom:1.5em;}#site-navigation{margin:0 auto;}.header-widget{margin-top:1.5em;}}body, button, input, select, textarea{font-family:Segoe UI, Helvetica Neue, Helvetica, sans-serif;font-size:16px;}.entry-content > [class*="wp-block-"]:not(:last-child){margin-bottom:1.5em;}.main-navigation .main-nav ul ul li a{font-size:14px;}@media (max-width:768px){.main-title{font-size:30px;}h1{font-size:30px;}h2{font-size:25px;}}.top-bar{background-color:#636363;color:#ffffff;}.top-bar a,.top-bar a:visited{color:#ffffff;}.top-bar a:hover{color:#303030;}.site-header{background-color:#ffffff;color:#3a3a3a;}.site-header a,.site-header a:visited{color:#3a3a3a;}.main-title a,.main-title a:hover,.main-title a:visited{color:#222222;}.site-description{color:#757575;}.main-navigation,.main-navigation ul ul{background-color:#222222;}.main-navigation .main-nav ul li a,.menu-toggle{color:#ffffff;}.main-navigation .main-nav ul li:hover > a,.main-navigation .mai.... Truncated ....
```

**Extra Information**

**Extracted results**:

- Apache/2.4.29 (Ubuntu)



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://10.200.107.33'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)