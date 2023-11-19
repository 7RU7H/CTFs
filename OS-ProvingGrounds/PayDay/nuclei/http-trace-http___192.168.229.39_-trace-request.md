### HTTP TRACE method enabled (http-trace:trace-request) found on http://192.168.229.39/

----
**Details**: **http-trace:trace-request** matched at http://192.168.229.39/

**Protocol**: HTTP

**Full URL**: http://192.168.229.39/

**Timestamp**: Wed Nov 15 12:11:00 +0000 GMT 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP TRACE method enabled |
| Authors | nodauf |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
TRACE / HTTP/1.1
Host: 192.168.229.39
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36
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
Date: Wed, 15 Nov 2023 12:10:43 GMT
Server: Apache/2.2.4 (Ubuntu) PHP/5.2.3-1ubuntu6

TRACE / HTTP/1.1
Host: 192.168.229.39
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

References: 
- https://www.blackhillsinfosec.com/three-minutes-with-the-http-trace-method/

**CURL command**
```sh
curl -X 'TRACE' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36' 'http://192.168.229.39/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)