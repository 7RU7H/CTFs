### WAF Detection (waf-detect:aspgeneric) found on 10.10.15.133:49663

----
**Details**: **waf-detect:aspgeneric** matched at 10.10.15.133:49663

**Protocol**: HTTP

**Full URL**: http://10.10.15.133:49663/

**Timestamp**: Thu Dec 7 14:59:23 +0000 GMT 2023

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
Host: 10.10.15.133:49663
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 405 Method Not Allowed
Connection: close
Content-Length: 0
Allow: GET, HEAD, OPTIONS, TRACE
Date: Thu, 07 Dec 2023 14:58:39 GMT
Server: Microsoft-IIS/10.0
X-Powered-By: ASP.NET


```

References: 
- https://github.com/ekultek/whatwaf

**CURL command**
```sh
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.10.15.133:49663' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://10.10.15.133:49663/'
```

----

Generated by [Nuclei v3.1.0](https://github.com/projectdiscovery/nuclei)