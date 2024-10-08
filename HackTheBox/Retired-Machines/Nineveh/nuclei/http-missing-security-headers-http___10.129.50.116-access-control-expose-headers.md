### HTTP Missing Security Headers (http-missing-security-headers:access-control-expose-headers) found on http://10.129.50.116
---
**Details**: **http-missing-security-headers:access-control-expose-headers**  matched at http://10.129.50.116

**Protocol**: HTTP

**Full URL**: http://10.129.50.116

**Timestamp**: Sun Sep 25 19:18:23 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.50.116
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Sun, 25 Sep 2022 18:18:22 GMT
Etag: "b2-5535e4e04002a-gzip"
Last-Modified: Sun, 02 Jul 2017 23:49:44 GMT
Server: Apache/2.4.18 (Ubuntu)
Vary: Accept-Encoding

<html><body><h1>It works!</h1>
<p>This is the default web page for this server.</p>
<p>The web server software is running but no content has been added, yet.</p>
</body></html>


```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36' 'http://10.129.50.116'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)