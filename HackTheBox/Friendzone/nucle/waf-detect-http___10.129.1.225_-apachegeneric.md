### WAF Detection (waf-detect:apachegeneric) found on http://10.129.1.225/
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.1.225/

**Protocol**: HTTP

**Full URL**: http://10.129.1.225/

**Timestamp**: Sun May 29 17:26:27 +0100 BST 2022

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
Host: 10.129.1.225
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
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
Accept-Ranges: bytes
Content-Type: text/html
Date: Sun, 29 May 2022 16:26:27 GMT
Etag: "144-577831e9005e6-gzip"
Last-Modified: Fri, 05 Oct 2018 22:52:00 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding

<title>Friend Zone Escape software</title>

<center><h2>Have you ever been friendzoned ?</h2></center>

<center><img src="fz.jpg"></center>

<center><h2>if yes, try to get out of this zone ;)</h2></center>

<center><h2>Call us at : +999999999</h2></center>

<center><h2>Email us at: info@friendzoneportal.red</h2></center>


```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.1.225' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://10.129.1.225/'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)