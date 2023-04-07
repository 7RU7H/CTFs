### HTTP TRACE method enabled (HTTP-TRACE:trace-request) found on https://10.10.84.67
---
**Details**: **HTTP-TRACE:trace-request**  matched at https://10.10.84.67

**Protocol**: HTTP

**Full URL**: https://10.10.84.67

**Timestamp**: Fri Apr 7 09:02:42 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP TRACE method enabled |
| Authors | nodauf |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
TRACE / HTTP/1.1
Host: 10.10.84.67
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Content-Type: message/http
Date: Fri, 07 Apr 2023 08:02:33 GMT
Server: Apache/2.4.23 (Win32) OpenSSL/1.0.2h PHP/5.6.28

TRACE / HTTP/1.1
Host: 10.10.84.67
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

References: 
- https://www.blackhillsinfosec.com/three-minutes-with-the-http-trace-method/

**CURL Command**
```
curl -X 'TRACE' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'https://10.10.84.67'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)