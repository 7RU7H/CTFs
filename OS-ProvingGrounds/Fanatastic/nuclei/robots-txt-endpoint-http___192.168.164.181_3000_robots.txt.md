### robots.txt endpoint prober (robots-txt-endpoint) found on http://192.168.164.181:3000
---
**Details**: **robots-txt-endpoint**  matched at http://192.168.164.181:3000

**Protocol**: HTTP

**Full URL**: http://192.168.164.181:3000/robots.txt

**Timestamp**: Thu Jun 2 10:12:36 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /robots.txt HTTP/1.1
Host: 192.168.164.181:3000
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 26
Accept-Ranges: bytes
Cache-Control: public, max-age=3600
Content-Type: text/plain; charset=utf-8
Date: Thu, 02 Jun 2022 09:12:36 GMT
Last-Modified: Tue, 30 Nov 2021 15:38:39 GMT

User-agent: *
Disallow: /

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://192.168.164.181:3000/robots.txt'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)