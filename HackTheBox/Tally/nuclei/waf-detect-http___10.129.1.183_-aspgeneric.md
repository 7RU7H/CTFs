### WAF Detection (waf-detect:aspgeneric) found on http://10.129.1.183
---
**Details**: **waf-detect:aspgeneric**  matched at http://10.129.1.183

**Protocol**: HTTP

**Full URL**: http://10.129.1.183/

**Timestamp**: Mon Jun 6 16:48:23 +0100 BST 2022

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
POST / HTTP/1.1
Host: 10.129.1.183
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 302 Redirect
Connection: close
Content-Length: 179
Content-Type: text/html; charset=UTF-8
Date: Mon, 06 Jun 2022 15:48:23 GMT
Location: http://10.129.1.183/_layouts/15/start.aspx#/default.aspx
Microsoftsharepointteamservices: 15.0.0.4420
Request-Id: 0cbe44a0-bcad-f075-0000-043f03461262
Server: Microsoft-IIS/10.0
Spiislatency: 67
Sprequestduration: 113
Sprequestguid: 0cbe44a0-bcad-f075-0000-043f03461262
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-Ms-Invokeapp: 1; RequireReadOnly
X-Powered-By: ASP.NET
X-Sharepointhealthscore: 5

<head><title>Document Moved</title></head>
<body><h1>Object Moved</h1>This document may be found <a HREF="http://10.129.1.183/_layouts/15/start.aspx#/default.aspx">here</a></body>
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.1.183' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.1.183/'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)