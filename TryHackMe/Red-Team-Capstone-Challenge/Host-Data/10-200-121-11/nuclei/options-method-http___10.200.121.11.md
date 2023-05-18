### Allowed Options Method (options-method) found on http://10.200.121.11
---
**Details**: **options-method**  matched at http://10.200.121.11

**Protocol**: HTTP

**Full URL**: http://10.200.121.11

**Timestamp**: Thu May 11 21:28:58 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Allowed Options Method |
| Authors | pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
OPTIONS / HTTP/1.1
Host: 10.200.121.11
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Allow: OPTIONS, TRACE, GET, HEAD, POST
Date: Thu, 11 May 2023 20:28:57 GMT
Public: OPTIONS, TRACE, GET, HEAD, POST
Server: Microsoft-IIS/10.0
Content-Length: 0


```

**Extra Information**

**Extracted results**:

- OPTIONS, TRACE, GET, HEAD, POST



**CURL Command**
```
curl -X 'OPTIONS' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36' 'http://10.200.121.11'
```
---
Generated by [Nuclei v2.9.3](https://github.com/projectdiscovery/nuclei)