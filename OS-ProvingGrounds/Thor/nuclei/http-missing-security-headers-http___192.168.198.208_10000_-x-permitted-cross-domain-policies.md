### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on http://192.168.198.208:10000/

----
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies** matched at http://192.168.198.208:10000/

**Protocol**: HTTP

**Full URL**: http://192.168.198.208:10000/

**Timestamp**: Thu Nov 9 20:49:09 +0000 GMT 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.198.208:10000
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 Document follows
Connection: close
Content-Type: text/html; Charset=utf-8
Date: Thu, 9 Nov 2023 20:49:11 GMT
Server: MiniServ/1.962

<h2 style='color: #de0000; margin-bottom: -8px;'>Error - Document follows</h2>
<p>This web server is running in SSL mode. Try the URL <a href='https://Lite:10000/'>https://Lite:10000/</a> instead.</p>

```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://192.168.198.208:10000/'
```

----

Generated by [Nuclei v3.0.2](https://github.com/projectdiscovery/nuclei)