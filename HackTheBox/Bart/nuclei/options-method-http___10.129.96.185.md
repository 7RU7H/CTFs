### Allowed Options Method (options-method) found on http://10.129.96.185
---
**Details**: **options-method**  matched at http://10.129.96.185

**Protocol**: HTTP

**Full URL**: http://10.129.96.185

**Timestamp**: Thu Jun 2 08:24:50 +0100 BST 2022

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
Host: 10.129.96.185
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
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
Date: Thu, 02 Jun 2022 07:24:49 GMT
Public: OPTIONS, TRACE, GET, HEAD, POST
Server: Microsoft-IIS/10.0
Content-Length: 0


```

**Extra Information**

**Extracted results**:

- OPTIONS, TRACE, GET, HEAD, POST



**CURL Command**
```
curl -X 'OPTIONS' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.129.96.185'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)