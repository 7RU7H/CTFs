### Microsoft IIS version detect (microsoft-iis-version) found on https://atsserver.acute.local
---
**Details**: **microsoft-iis-version**  matched at https://atsserver.acute.local

**Protocol**: HTTP

**Full URL**: https://atsserver.acute.local

**Timestamp**: Fri Nov 25 16:15:24 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Microsoft IIS version detect |
| Authors | wlayzz |
| Tags | tech, microsoft, iis |
| Severity | info |
| Description | Some Microsoft IIS servers have the version on the response header. Useful when you need to find specific CVEs on your targets. |

**Request**
```http
GET / HTTP/1.1
Host: atsserver.acute.local
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 93397
Accept-Ranges: bytes
Content-Type: text/html
Date: Fri, 25 Nov 2022 16:15:16 GMT
Etag: "0e46722c7d81:0"
Last-Modified: Tue, 11 Jan 2022 20:47:28 GMT
Server: Microsoft-IIS/10.0
X-Powered-By: ASP.NET

<!DOCTYPE html>
<html class="js js_active  vc_desktop  vc_transform  vc_transform  vc_transform " lang="en-GB"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"><meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

	<title>Acute Health | Health, Social and Child care Training</title>
	<meta name="description" content="Acute Health offer the very best in healthcare training, social care training and child care training across the UK.">
	<meta property="og:locale" content="en_GB">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Acute Health | Health, Social and Child care Training">
	<meta property="og:description" content="Acute Health offer the very best in healthcare training, social care training and child care training across the UK.">
	<meta property="og:url" content="https://atsserver.acute.local/">
	<meta property="og:site_name" content="Acute Health">
	<meta property="article:modified_time" content="2021-11-22T06:52:56+00:00">
	<meta property="og:image" content="http://atsserver.acute.local/">
	<meta property="og:image:width" content="2508">
	<meta property="og:image:height" content="1672">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:label1" content="Estimated reading time">
	<meta name="twitter:data1" content="10 minutes">

<link rel="dns-prefetch" href="https://fonts.googleapis.com/">
<link rel="dns-prefetch" href="https://s.w.org/">
<link rel="alternate" type="application/rss+xml" title="Acute Health » Feed" href="https://atsserver.acute.local/#">
<link rel="alternate" type="application/rss+xml" title="Acute Health » Comments Feed" href="https://atsserver.acute.local/#">
		
							
			<script type="text/javascript" data-cfasync="false">
				var mi_version = '8.2.0';
				var mi_track_user = true;
				var mi_no_track_reason = '';
				
								var disableStrs = [
															'ga-disable-UA-23921561-1',
									];

				/* Function to detect opted out users */
				function __gtagTrackerIsOptedOut() {
					for ( var index = 0; index < disableStrs.length; index++ ) {
						if ( document.cookie.indexOf( disableStrs[ index ] + '=true' ) > -1 ) {
							return true;
						}
					}

					return false;
				}

				/* Disable tracking if the opt-out cookie exists. */
				if ( __gtagTrackerIsOptedOut() ) {
					for ( var index = 0; index < disableStrs.length; index++ ) {
						window[ disableStrs[ index ] ] = true;
					}
				}

				/* Opt-out function */
				function __gtagTrackerOptout() {
					for ( var index = 0; index < disableStrs.length; index++ ) {
						document.cookie = disableStrs[ index ] + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
						window[ disableStrs[ index ] ] = true;
					}
				}

				if ( 'undefined' === typeof gaOptout ) {
					function gaOptout() {
						__gtagTrackerOptout();
					}
				}
								window.dataLayer = window.dataLayer || [];

				window.MonsterInsightsDualTracker = {
					helpers: {},
					trackers: {},
				};
				if ( mi_track_user ) {
					function __gtagDataLayer() {
						dataLayer.push( arguments );
					}

					function __gtagTracker( type, name, parameters ) {
						if (!parameters) {
							parameters = {};
						}

						if (parameters.send_to) {
							__gtagDataLayer.apply( null, arguments );
							return;
						}

						if ( type === 'event' ) {
							
															parameters.send_to = monsterinsights_frontend.ua;
								__gtagDataLayer( type, name, parameters );
													} else {
							__gtagDataLayer.apply( null, arguments );
						}
					}
					__gtagTracker( 'js', new Date() );
					__gtagTracker( 'set', {
						'developer_id.dZGIzZG' : true,
											} );
															__gtagTracker( 'config', 'UA-23921561-1', {"forceSSL":"true","link_attribution":"true"} );
										window.gtag = __gtagTracker;										(
						function () {
							/* https://developers.google.com/analytics/devguides/collection/analyticsjs/ */
							/* ga and __gaTracker compatibility shim. */
							var noopfn = function () {
								return null;
							};
							var newtracker = function () {
								return new Tracker();
							};
							var Tracker = function () {
								return null;
							};
							var p = Tracker.prototype;
							p.get = noopfn;
							p.set = noopfn;
							p.send = function (){
								var args = Array.prototype.slice.call(arguments);
								args.unshift( 'send' );
								__gaTracker.apply(null, args);
							};
							var __gaTracker = function () {
								var len = arguments.length;
								if ( len === 0 ) {
									return;
								}
								var f = arguments[len - 1];
								if ( typeof f !== 'object' || f === null ||.... Truncated ....
```

**Extra Information**

**Extracted results**:

- Microsoft-IIS/10.0



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'https://atsserver.acute.local'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)