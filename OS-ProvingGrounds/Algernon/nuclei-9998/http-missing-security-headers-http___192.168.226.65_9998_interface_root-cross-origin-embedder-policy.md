### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-embedder-policy) found on http://192.168.226.65:9998
---
**Details**: **http-missing-security-headers:cross-origin-embedder-policy**  matched at http://192.168.226.65:9998

**Protocol**: HTTP

**Full URL**: http://192.168.226.65:9998/interface/root

**Timestamp**: Sun Sep 4 20:49:45 +0100 BST 2022

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
Host: 192.168.226.65:9998
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Cache-Control: private
Content-Type: text/html; charset=utf-8
Date: Sun, 04 Sep 2022 19:50:08 GMT
Server: Microsoft-IIS/10.0
Vary: Accept-Encoding
X-Aspnetmvc-Version: 5.2


<!DOCTYPE html>
<html ng-app="smartermail" ng-cloak>

<head>
	<!-- SmarterMail Copyright (c) 2003-2022 SmarterTools Inc.  All Rights Reserved. -->
	<meta charset="utf-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />


	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

	<!-- Title set in directive -->
	<title page-title></title>
	

	<!-- Styles -->
	<link href="/interface/output/login-v-100.0.6919.30414.8d65fc3f1d47d00.min.css" rel="stylesheet" />
	<style>
        .main-controller{display:none;}
        .popout-view {display:none;}
.rotator:before,.spinner-wrapper::after{content:""}.spinner{display:block;margin-right:auto;margin-left:auto;width:4em;padding:7px;border-radius:50%;margin-top:4em;height:100%;transform:scale(1.5)}.spinner-wrapper{width:4em;height:4em;border-radius:100%;left:calc(50% - 2em);margin:auto;position:relative;top:38%}.spinner-wrapper::after{background:#f3f3f3;border-radius:50%;width:3em;height:3em;position:absolute;top:.5em;left:.5em}.rotator{position:relative;width:4em;border-radius:4em;overflow:hidden;animation:rotate 2s infinite linear}.rotator:before{position:absolute;top:0;left:0;right:0;bottom:0;background:#3F51B5;border:3px solid #f3f3f3;border-radius:100%}.inner-spin{background:#f3f3f3;height:4em;width:2em;animation:rotate-left 2.5s infinite cubic-bezier(.445,.050,.55,.95);border-radius:2em 0 0 2em;transform-origin:2em 2em}.inner-spin:last-child{animation:rotate-right 2.5s infinite cubic-bezier(.445,.050,.55,.95);margin-top:-4em;border-radius:0 2em 2em 0;float:right;transform-origin:0 50%}@keyframes rotate-left{100%,60%,75%{transform:rotate(360deg)}}@keyframes rotate{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}@keyframes rotate-right{0%,25%,45%{transform:rotate(0)}100%{transform:rotate(360deg)}}
	</style>

	<!-- Font Awesome and Bootstrap -->
	<link href="/interface/lib/font-awesome/css/font-awesome.css" rel="stylesheet" async />

	<script>
		var htmlCacheBustQs = "cachebust=100.0.6919.30414.8d65fc3f1d47d00";
		var languageCacheBustQs = "cachebust=8d65fc3f1d47d00";
		var angularLangList = ['cs','da','de','en','en-GB','es','fa','fr','it','nl','pt','pt-BR','sv','tr','zh-CN','zh-HK','zh-TW'];
		var angularLangMap = {'cs': 'cs', 'da': 'da', 'de': 'de', 'en': 'en', 'en-GB': 'en-GB', 'es': 'es', 'fa': 'fa', 'fr': 'fr', 'it': 'it', 'nl': 'nl', 'pt': 'pt', 'pt-BR': 'pt-BR', 'sv': 'sv', 'tr': 'tr', 'zh-CN': 'zh-CN', 'zh-HK': 'zh-HK', 'zh-TW': 'zh-TW', 'cs*': 'cs', 'da*': 'da', 'de*': 'de', 'en*': 'en', 'es*': 'es', 'fa*': 'fa', 'fr*': 'fr', 'it*': 'it', 'nl*': 'nl', 'pt*': 'pt', 'sv*': 'sv', 'tr*': 'tr', 'zh*': 'zh-CN'};
		var angularLangNames = [{v:'cs',n:'čeština'},{v:'da',n:'dansk'},{v:'de',n:'Deutsch'},{v:'en',n:'English'},{v:'en-GB',n:'English (United Kingdom)'},{v:'es',n:'español'},{v:'fa',n:'فارسی'},{v:'fr',n:'français'},{v:'it',n:'italiano'},{v:'nl',n:'Nederlands'},{v:'pt',n:'português'},{v:'pt-BR',n:'português (Brasil)'},{v:'sv',n:'svenska'},{v:'tr',n:'Türkçe'},{v:'zh-CN',n:'中文(中国)'},{v:'zh-HK',n:'中文(香港特別行政區)'},{v:'zh-TW',n:'中文(台灣)'}];
		var cssVersion = "100.0.6919.30414.8d65fc3f1d47d00";
		var stProductVersion = "100.0.6919";
		var stProductBuild = "6919 (Dec 11, 2018)";
		var stSiteRoot = "/";
		var stThemeVersion = "100.0.6919.30414.8d65fc3f1d47d00";
		var debugMode = 0;

		function cachebust(url) {
			if (!url) return null;
			var separator = url.indexOf("?")==-1 ? "?" : "&";
			return url + separator + htmlCacheBustQs;
		}
	</script>
</head>

<body onload="$('#loadingInd').hide()">
	<div id="loadingInd" style="height:100%;">
		<div class="spinner">
			<div class="spinner-wrapper">
				<div class="rotator">
					<div class="inner-spin"></div>
					<div class="inner-spin"></div>
				</div>
			</div>
		</div>
	</div>

	<script src="/interface/output/angular-v-100.0.6919.30414.8d65fc3f1d47d00.js"></script>
	<script src="/interface/output/vendor-v-100.0.6919.30414.8d65fc3f1d47d00.js"></script>
	<script src="/interface/output/site-v-100.0.6919.30414.8d65fc3f1d47d00.js"></script> 
	 
	<div ui-view class="app-view"></div>
	<div class="st-select-overlay" style="background-color: rgba(255, 255, 255, 0.5); z-index: 2000; pointer-events:initial;" ng-click="$event.stopPropagation()" ng-if="spinner.isShown()" layout="row" layout-align="center center">
		<md-progress-circular md-mode="indeterminate" md-diameter="84"></md-progress-circular>
	</div>
	<div class="st-select-overlay" style="background-color: rgba(255, 255, 255, 0.5); z-index: 2000; pointer-events:initial;" ng-click="$event.stopPropagat.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://192.168.226.65:9998' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.226.65:9998/interface/root'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)