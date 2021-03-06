### WAF Detection (waf-detect:apachegeneric) found on http://10.129.77.22
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.77.22

**Protocol**: HTTP

**Full URL**: http://10.129.77.22/

**Timestamp**: Sat Jul 9 19:50:37 +0100 BST 2022

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
Host: 10.129.77.22
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 302 Found
Connection: close
Content-Length: 282
Content-Type: text/html; charset=iso-8859-1
Date: Sat, 09 Jul 2022 18:50:37 GMT
Location: https://10.129.77.22/
Server: Apache/2.2.3 (CentOS)

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>302 Found</title>
</head><body>
<h1>Found</h1>
<p>The document has moved <a href="https://10.129.77.22/">here</a>.</p>
<hr>
<address>Apache/2.2.3 (CentOS) Server at 10.129.77.22 Port 80</address>
</body></html>

```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.77.22' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://10.129.77.22/'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)