### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-headers) found on http://10.129.96.140/mvc
---
**Details**: **http-missing-security-headers:access-control-allow-headers**  matched at http://10.129.96.140/mvc

**Protocol**: HTTP

**Full URL**: http://10.129.96.140/mvc/

**Timestamp**: Sat Feb 4 10:58:57 +0000 GMT 2023

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
GET /mvc HTTP/1.1
Host: 10.129.96.140
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
Content-Length: 10740
Cache-Control: private
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Feb 2023 10:58:57 GMT
Server: Microsoft-IIS/10.0
Set-Cookie: __AntiXsrfToken=c7733e16207748b28bc1d60819e236e1; path=/; HttpOnly
X-Aspnet-Version: 4.0.30319
X-Powered-By: ASP.NET



<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8" /><title>
	Home Page - My ASP.NET Application
</title><script src="/mvc/Scripts/modernizr-2.5.3.js"></script>
<link href="/mvc/Content/Site.css" rel="stylesheet"/>
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" /><meta name="viewport" content="width=device-width" /></head>
<body>
    <form method="post" action="./" id="ctl01">
<div class="aspNetHidden">
<input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="" />
<input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="" />
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="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" />
</div>

<script type="text/javascript">
//<![CDATA[
var theForm = document.forms['ctl01'];
if (!theForm) {
    theForm = document.ctl01;
}
function __doPostBack(eventTarget, eventArgument) {
    if (!theForm.onsubm.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.96.140/mvc' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36' 'http://10.129.96.140/mvc/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)