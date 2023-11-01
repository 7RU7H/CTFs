### HTTP TRACE method enabled (HTTP-TRACE:trace-request) found on http://192.168.237.93/

----
**Details**: **HTTP-TRACE:trace-request** matched at http://192.168.237.93/

**Protocol**: HTTP

**Full URL**: http://192.168.237.93/

**Timestamp**: Thu Oct 26 15:38:38 +0100 BST 2023

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
Host: 192.168.237.93
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
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
Date: Thu, 26 Oct 2023 14:38:31 GMT
Server: Apache/2.4.6 (CentOS) PHP/7.3.22

TRACE / HTTP/1.1
Host: 192.168.237.93
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

References: 
- https://www.blackhillsinfosec.com/three-minutes-with-the-http-trace-method/

**CURL command**
```sh
curl -X 'TRACE' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://192.168.237.93/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)