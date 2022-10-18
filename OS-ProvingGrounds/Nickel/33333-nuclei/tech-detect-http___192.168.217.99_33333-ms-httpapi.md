### Wappalyzer Technology Detection (tech-detect:ms-httpapi) found on http://192.168.217.99:33333
---
**Details**: **tech-detect:ms-httpapi**  matched at http://192.168.217.99:33333

**Protocol**: HTTP

**Full URL**: http://192.168.217.99:33333

**Timestamp**: Mon Oct 17 12:06:20 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.217.99:33333
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
Content-Length: 13
Date: Mon, 17 Oct 2022 11:06:36 GMT
Server: Microsoft-HTTPAPI/2.0

Invalid Token
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://192.168.217.99:33333'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)