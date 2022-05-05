### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-methods) found on http://10.10.113.121
---
**Details**: **http-missing-security-headers:access-control-allow-methods**  matched at http://10.10.113.121

**Protocol**: HTTP

**Full URL**: http://10.10.113.121

**Timestamp**: Thu May 5 18:01:02 +0100 BST 2022

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
Host: 10.10.113.121
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36
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
Date: Thu, 05 May 2022 17:01:00 GMT
Link: <http://jack.thm/index.php/wp-json/>; rel="https://api.w.org/"
Server: Apache/2.4.18 (Ubuntu)
Vary: Accept-Encoding

<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <title>Jack&#039;s Personal Site &#8211; Blog for Jacks writing adventures.</title>
<link rel='dns-prefetch' href='//jack.thm' />
<link rel='dns-prefetch' href='//fonts.googleapis.com' />
<link rel='dns-prefetch' href='//s.w.org' />
<link rel="alternate" type="application/rss+xml" title="Jack&#039;s Personal Site &raquo; Feed" href="http://jack.thm/index.php/feed/" />
<link rel="alternate" type="application/rss+xml" title="Jack&#039;s Personal Site &raquo; Comments Feed" href="http://jack.thm/index.php/comments/feed/" />
		<script type="text/javascript">
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/svg\/","svgExt":".svg","source":{"concatemoji":"http:\/\/jack.thm\/wp-includes\/js\/wp-emoji-release.min.js?ver=5.3.2"}};
			!function(e,a,t){var r,n,o,i,p=a.createElement("canvas"),s=p.getContext&&p.getContext("2d");function c(e,t){var a=String.fromCharCode;s.clearRect(0,0,p.width,p.height),s.fillText(a.apply(this,e),0,0);var r=p.toDataURL();return s.clearRect(0,0,p.width,p.height),s.fillText(a.apply(this,t),0,0),r===p.toDataURL()}function l(e){if(!s||!s.fillText)return!1;switch(s.textBaseline="top",s.font="600 32px Arial",e){case"flag":return!c([127987,65039,8205,9895,65039],[127987,65039,8203,9895,65039])&&(!c([55356,56826,55356,56819],[55356,56826,8203,55356,56819])&&!c([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]));case"emoji":return!c([55357,56424,55356,57342,8205,55358,56605,8205,55357,56424,55356,57340],[55357,56424,55356,57342,8203,55358,56605,8203,55357,56424,55356,57340])}return!1}function d(e){var t=a.createElement("script");t.src=e,t.defer=t.type="text/javascript",a.getElementsByTagName("head")[0].appendChild(t)}for(i=Array("flag","emoji"),t.supports={everything:!0,everythingExceptFlag:!0},o=0;o<i.length;o++)t.supports[i[o]]=l(i[o]),t.supports.everything=t.supports.everything&&t.supports[i[o]],"flag"!==i[o]&&(t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&t.supports[i[o]]);t.supports.everythingExceptFlag=t.supports.everythingExceptFlag&&!t.supports.flag,t.DOMReady=!1,t.readyCallback=function(){t.DOMReady=!0},t.supports.everything||(n=function(){t.readyCallback()},a.addEventListener?(a.addEventListener("DOMContentLoaded",n,!1),e.addEventListener("load",n,!1)):(e.attachEvent("onload",n),a.attachEvent("onreadystatechange",function(){"complete"===a.readyState&&t.readyCallback()})),(r=t.source||{}).concatemoji?d(r.concatemoji):r.wpemoji&&r.twemoji&&(d(r.twemoji),d(r.wpemoji)))}(window,document,window._wpemojiSettings);
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
	<link rel='stylesheet' id='wp-block-library-css'  href='http://jack.thm/wp-includes/css/dist/block-library/style.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='wp-block-library-theme-css'  href='http://jack.thm/wp-includes/css/dist/block-library/theme.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='online-portfolio-googleapis-css'  href='//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i|Work+Sans:100,200,300,400,500,600,700,800,900' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-css'  href='http://jack.thm/wp-content/themes/online-portfolio/assets/lib/font-awesome/css/all.min.css?ver=5.8.1' type='text/css' media='all' />
<link rel='stylesheet' id='bootstrap-css'  href='http://jack.thm/wp-content/themes/online-portfolio/assets/lib/bootstrap/css/bootstrap.min.css?ver=4.2.1' type='text/css' media='all' />
<link rel='stylesheet' id='animate-css'  href='http://jack.thm/wp-content/themes/online-portfolio/assets/lib/animate/animate.min.css?ver=3.5.2' type='text/css' media='all' />
<link rel='stylesheet' id='owlcarousel-css-css'  href='http://jack.thm/wp-content/themes/online-portfolio/assets/lib/owlcarousel/assets/owl.carousel.min.css?ver=2.2.1' type='text/css' media='all' />
<link rel='stylesheet' id='lightbox-css'  href='http://jack.thm/wp-content/themes/online-portfolio/assets/lib/lightbox/css/lightbox.min.css?ver=2.2.1' type='text/css' media='all' />
<link rel='stylesheet' id='online-portfolio-style-css'  href='http://jack.thm/wp-conte.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://10.10.113.121'
```
---
Generated by [Nuclei 2.6.9](https://github.com/projectdiscovery/nuclei)