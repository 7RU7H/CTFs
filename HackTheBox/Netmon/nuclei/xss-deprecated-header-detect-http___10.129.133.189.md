### Detect Deprecated XSS Protection Header (xss-deprecated-header-detect) found on http://10.129.133.189
---
**Details**: **xss-deprecated-header-detect**  matched at http://10.129.133.189

**Protocol**: HTTP

**Full URL**: http://10.129.133.189

**Timestamp**: Wed Jun 8 20:25:51 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Detect Deprecated XSS Protection Header |
| Authors | joshlarsen |
| Tags | xss, misconfig, generic |
| Severity | info |
| Description | Setting the XSS-Protection header is deprecated by most browsers. Setting the header to anything other than `0` can actually introduce an XSS vulnerability. |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.133.189
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 302 Moved Temporarily
Connection: close
Cache-Control: no-cache
Content-Type: text/html; charset=ISO-8859-1
Date: Wed, 08 Jun 2022 19:25:51 GMT
Expires: 0
Location: /index.htm
Server: PRTG/18.1.37.13946
X-Content-Type-Options: nosniff
X-Xss-Protection: 1; mode=block
Content-Length: 0


```

**Extra Information**

**Extracted results**:

- 1; mode=block


References: 
- https://developer.mozilla.org/en-us/docs/web/http/headers/x-xss-protection
- https://owasp.org/www-project-secure-headers/#x-xss-protection

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://10.129.133.189'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)