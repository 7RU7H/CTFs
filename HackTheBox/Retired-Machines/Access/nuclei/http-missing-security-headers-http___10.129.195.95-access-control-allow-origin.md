### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-origin) found on http://10.129.195.95
---
**Details**: **http-missing-security-headers:access-control-allow-origin**  matched at http://10.129.195.95

**Protocol**: HTTP

**Full URL**: http://10.129.195.95

**Timestamp**: Mon May 1 09:37:52 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.195.95
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 391
Accept-Ranges: bytes
Content-Type: text/html
Date: Mon, 01 May 2023 08:37:51 GMT
Etag: "44a87bb393bd41:0"
Last-Modified: Thu, 23 Aug 2018 23:33:43 GMT
Server: Microsoft-IIS/7.5
X-Powered-By: ASP.NET

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>MegaCorp</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="center">
  <p><strong><font size="5" face="Verdana, Arial, Helvetica, sans-serif">LON-MC6</font></strong> </p>
  <p><img border="0" src="out.jpg"></p>
</div>
</body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://10.129.195.95'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)