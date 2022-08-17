### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-credentials) found on https://brainfuck.htb
---
**Details**: **http-missing-security-headers:access-control-allow-credentials**  matched at https://brainfuck.htb

**Protocol**: HTTP

**Full URL**: https://brainfuck.htb

**Timestamp**: Wed Aug 17 20:46:41 +0100 BST 2022

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
Host: brainfuck.htb
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36
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
Content-Type: text/html; charset=UTF-8
Date: Wed, 17 Aug 2022 19:46:43 GMT
Link: <https://brainfuck.htb/?rest_route=/>; rel="https://api.w.org/"
Server: nginx/1.10.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en-US">
	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	
	<title>Brainfuck Ltd. &#8211; Just another WordPress site</title>
<meta name='robots' content='noindex,follow' />
<link rel='dns-prefetch' href='//ajax.googleapis.com' />
<link rel='dns-prefetch' href='//fonts.googleapis.com' />
<link rel='dns-prefetch' href='//s.w.org' />
<link rel="alternate" type="application/rss+xml" title="Brainfuck Ltd. &raquo; Feed" href="https://brainfuck.htb/?feed=rss2" />
<link rel="alternate" type="application/rss+xml" title="Brainfuck Ltd. &raquo; Comments Feed" href="https://brainfuck.htb/?feed=comments-rss2" />
		<script type="text/javascript">
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/svg\/","svgExt":".svg","source":{"concatemoji":"https:\/\/brainfuck.htb\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.7.3"}};
			!function(a,b,c){function d(a){var b,c,d,e,f=String.fromCharCode;if(!k||!k.fillText)return!1;switch(k.clearRect(0,0,j.width,j.height),k.textBaseline="top",k.font="600 32px Arial",a){case"flag":return k.fillText(f(55356,56826,55356,56819),0,0),!(j.toDataURL().length<3e3)&&(k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,65039,8205,55356,57096),0,0),b=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,55356,57096),0,0),c=j.toDataURL(),b!==c);case"emoji4":return k.fillText(f(55357,56425,55356,57341,8205,55357,56507),0,0),d=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55357,56425,55356,57341,55357,56507),0,0),e=j.toDataURL(),d!==e}return!1}function e(a){var c=b.createElement("script");c.src=a,c.defer=c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var f,g,h,i,j=b.createElement("canvas"),k=j.getContext&&j.getContext("2d");for(i=Array("flag","emoji4"),c.supports={everything:!0,everythingExceptFlag:!0},h=0;h<i.length;h++)c.supports[i[h]]=d(i[h]),c.supports.everything=c.supports.everything&&c.supports[i[h]],"flag"!==i[h]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[i[h]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(g=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",g,!1),a.addEventListener("load",g,!1)):(a.attachEvent("onload",g),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),f=c.source||{},f.concatemoji?e(f.concatemoji):f.wpemoji&&f.twemoji&&(e(f.twemoji),e(f.wpemoji)))}(window,document,window._wpemojiSettings);
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
<link rel='stylesheet' id='dashicons-css'  href='https://brainfuck.htb/wp-includes/css/dashicons.min.css?ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='wp-jquery-ui-dialog-css'  href='https://brainfuck.htb/wp-includes/css/jquery-ui-dialog.min.css?ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-ui-css-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/css/jquery-ui.min.css?ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-ui-structure-css-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/css/jquery-ui.structure.min.css?ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-ui-theme-css-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/css/jquery-ui.theme.min.css?ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='wpce_bootstrap-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/js/bootstrap/css/bootstrap.css?version=7.1.3&#038;ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='wpce_display_ticket-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/css/display_ticket.css?version=7.1.3&#038;ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='wpce_public-css'  href='https://brainfuck.htb/wp-content/plugins/wp-support-plus-responsive-ticket-system/asset/css/public.css?version=7.1.3&#038;ver=4.7.3' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-style-css'  href='//ajax.googleapis.com/ajax/libs/jqu.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36' 'https://brainfuck.htb'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)