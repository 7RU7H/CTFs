### WAF Detection (waf-detect:apachegeneric) found on myblog.thm:9007

----
**Details**: **waf-detect:apachegeneric** matched at myblog.thm:9007

**Protocol**: HTTP

**Full URL**: http://myblog.thm:9007/

**Timestamp**: Tue Feb 6 11:46:11 +0000 GMT 2024

**Template Information**

| Key | Value |
| --- | --- |
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
Host: myblog.thm:9007
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 400 Bad Request
Connection: close
Content-Length: 438
Content-Type: text/html; charset=iso-8859-1
Date: Tue, 06 Feb 2024 11:46:10 GMT
Server: Apache/2.4.41 (Ubuntu)

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>400 Bad Request</title>
</head><body>
<h1>Bad Request</h1>
<p>Your browser sent a request that this server could not understand.<br />
Reason: You're speaking plain HTTP to an SSL-enabled server port.<br />
 Instead use the HTTPS scheme to access this URL, please.<br />
</p>
<hr>
<address>Apache/2.4.41 (Ubuntu) Server at myblog.thm Port 80</address>
</body></html>

```

References: 
- https://github.com/ekultek/whatwaf

**CURL command**
```sh
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: myblog.thm:9007' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://myblog.thm:9007/'
```

----

Generated by [Nuclei v3.1.8](https://github.com/projectdiscovery/nuclei)