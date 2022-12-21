### WAF Detection (waf-detect:apachegeneric) found on http://10.129.227.143:8000
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.227.143:8000

**Protocol**: HTTP

**Full URL**: http://10.129.227.143:8000/

**Timestamp**: Thu Dec 15 18:13:45 +0000 GMT 2022

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
Host: 10.129.227.143:8000
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 404 Not Found
Connection: close
Content-Length: 278
Content-Type: text/html; charset=iso-8859-1
Date: Thu, 15 Dec 2022 18:13:45 GMT
Server: Apache/2.4.38 (Debian)

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
<hr>
<address>Apache/2.4.38 (Debian) Server at 10.129.227.143 Port 8000</address>
</body></html>

```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.227.143:8000' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://10.129.227.143:8000/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)