### WAF Detection (waf-detect:aspgeneric) found on http://10.129.96.140/mvc
---
**Details**: **waf-detect:aspgeneric**  matched at http://10.129.96.140/mvc

**Protocol**: HTTP

**Full URL**: http://10.129.96.140/mvc/

**Timestamp**: Sat Feb 4 10:59:24 +0000 GMT 2023

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
POST /mvc/ HTTP/1.1
Host: 10.129.96.140
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 10740
Cache-Control: private
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Feb 2023 10:59:23 GMT
Server: Microsoft-IIS/10.0
Set-Cookie: __AntiXsrfToken=e4cb87941ae34b5db2e7171cf6dce778; path=/; HttpOnly
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

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.96.140' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.96.140/mvc/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)