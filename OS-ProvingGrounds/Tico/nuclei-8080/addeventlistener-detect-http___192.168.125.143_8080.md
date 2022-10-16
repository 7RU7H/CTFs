### DOM EventListener - Cross-Site Scripting (addeventlistener-detect) found on http://192.168.125.143:8080
---
**Details**: **addeventlistener-detect**  matched at http://192.168.125.143:8080

**Protocol**: HTTP

**Full URL**: http://192.168.125.143:8080

**Timestamp**: Sat Oct 15 16:44:42 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | DOM EventListener - Cross-Site Scripting |
| Authors | yavolo, dwisiswant0 |
| Tags | xss, misc |
| Severity | info |
| Description | EventListener contains a cross-site scripting vulnerability via the document object model (DOM). An attacker can execute arbitrary script which can then allow theft of cookie-based authentication credentials and launch of  other attacks. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:C/C:L/I:L/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:C/C:L/I:L/A:N) |
| CWE-ID | [CWE-79](https://cwe.mitre.org/data/definitions/79.html) |
| CVSS-Score | 7.20 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.125.143:8080
User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
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
Content-Type: text/html; charset=utf-8
Date: Sat, 15 Oct 2022 15:43:32 GMT
Etag: W/"5ea9-oXxp8pRLM6zLvWrREguLyIY2udg"
Referrer-Policy: strict-origin-when-cross-origin
Set-Cookie: _csrf=pECFVy1gydq9AFR_4_9M9sT9; Path=/
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
	<title>Home | NodeBB</title>
	<meta name="viewport" content="width&#x3D;device-width, initial-scale&#x3D;1.0" />
	<meta name="content-type" content="text/html; charset=UTF-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta property="og:site_name" content="NodeBB" />
	<meta name="msapplication-badge" content="frequency=30; polling-uri=http://localhost:4567/sitemap.xml" />
	<meta name="title" content="NodeBB" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="http://localhost:4567/assets/logo.png" />
	<meta property="og:image:url" content="http://localhost:4567/assets/logo.png" />
	<meta property="og:image:width" content="128" />
	<meta property="og:image:height" content="128" />
	<meta property="og:title" content="NodeBB" />
	<meta property="og:url" content="http://localhost:4567" />
	
	<link rel="stylesheet" type="text/css" href="/assets/client.css?v=oljj059n9nk" />
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
		var config = JSON.parse('{"relative_path":"","upload_url":"/assets/uploads","siteTitle":"NodeBB","browserTitle":"NodeBB","titleLayout":"&#123;pageTitle&#125; | &#123;browserTitle&#125;","showSiteTitle":true,"minimumTitleLength":3,"maximumTitleLength":255,"minimumPostLength":8,"maximumPostLength":32767,"minimumTagsPerTopic":0,"maximumTagsPerTopic":5,"minimumTagLength":3,"maximumTagLength":15,"useOutgoingLinksPage":false,"allowGuestHandles":false,"allowFileUploads":false,"allowTopicsThumbnail":false,"usePagination":false,"disableChat":false,"disableChatMessageEditing":false,"maximumChatMessageLength":1000,"socketioTransports":["polling","websocket"],"socketioOrigins":"*:*","websocketAddress":"","maxReconnectionAttempts":5,"reconnectionDelay":1500,"topicsPerPage":20,"postsPerPage":20,"maximumFileSize":2048,"theme:id":"nodebb-theme-persona","theme:src":"","defaultLang":"en-GB","userLang":"en-GB","loggedIn":false,"uid":0,"cache-buster":"v=oljj059n9nk","requireEmailConfirmation":false,"topicPostSort":"oldest_to_newest","categoryTopicSort":"newest_to_oldest","csrf_token":"NTHHRyjt-D02sDjBooRuoBmnMhKY4CRzFS04","searchEnabled":false,"bootswatchSkin":"","enablePostHistory":true,"notificationAlertTimeout":5000,"timeagoCutoff":30,"timeagoCodes":["af","ar","az-short","az","bg","bs","ca","cs","cy","da","de-short","de","dv","el","en-short","en","es-short","es","et","eu","fa-short","fa","fi","fr-short","fr","gl","he","hr","hu","hy","id","is","it-short","it","ja","jv","ko","ky","lt","lv","mk","nl","no","pl","pt-br-short","pt-br","pt-short","pt","ro","rs","ru","rw","si","sk","sl","sr","sv","th","tr-short","tr","uk","uz","vi","zh-CN","zh-TW"],"cookies":{"enabled":false,"message":"[[global:cookies.message]]","dismiss":"[[global:cookies.accept]]","link":"[[global:cookies.learn_more]]","link_url":"https:&#x2F;&#x2F;www.cookiesandyou.com"},"acpLang":"en-GB","topicSearchEnabled":false,"composer-default":{},"hideSubCategories":false,"hideCategoryLastPost":false,"enableQuickReply":false,"markdown":{"highlight":1,"highlightLinesLanguageList":[],"theme":"railscasts.css"},"emojiCustomFirst":false.... Truncated ....
```

References: 
- https://portswigger.net/web-security/dom-based/controlling-the-web-message-source

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://192.168.125.143:8080'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)