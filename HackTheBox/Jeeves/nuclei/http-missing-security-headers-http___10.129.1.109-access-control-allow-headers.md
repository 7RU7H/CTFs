### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-headers) found on http://10.129.1.109
---
**Details**: **http-missing-security-headers:access-control-allow-headers**  matched at http://10.129.1.109

**Protocol**: HTTP

**Full URL**: http://10.129.1.109

**Timestamp**: Thu Jun 2 07:48:31 +0100 BST 2022

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
Host: 10.129.1.109
User-Agent: Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 503
Accept-Ranges: bytes
Content-Type: text/html
Date: Thu, 02 Jun 2022 11:48:30 GMT
Etag: "2277f7cba756d31:0"
Last-Modified: Mon, 06 Nov 2017 02:34:40 GMT
Server: Microsoft-IIS/10.0

<!DOCTYPE html>
<html>
<head>
<title>Ask Jeeves</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<form class="form-wrapper cf" action="error.html">
    <div class="byline"><p><a href="#">Web</a>, <a href="#">images</a>, <a href="#">news</a>, and <a href="#">lots of answers</a>.</p></div>
  	<input type="text" placeholder="Search here..." required>
	  <button type="submit">Search</button>
    <div class="byline-bot">Skins</div>
</form>
</body>

</html>
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://10.129.1.109'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)