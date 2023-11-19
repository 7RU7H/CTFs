### Allowed Options Method (options-method) found on http://192.168.171.119

----
**Details**: **options-method** matched at http://192.168.171.119

**Protocol**: HTTP

**Full URL**: http://192.168.171.119

**Timestamp**: Tue Nov 7 11:48:32 +0000 GMT 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Allowed Options Method |
| Authors | pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
OPTIONS / HTTP/1.1
Host: 192.168.171.119
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
Allow: OPTIONS, TRACE, GET, HEAD, POST
Date: Tue, 07 Nov 2023 11:48:14 GMT
Public: OPTIONS, TRACE, GET, HEAD, POST
Server: Microsoft-IIS/10.0
X-Powered-By: ASP.NET
Content-Length: 0


```

**Extra Information**

**Extracted results:**

- OPTIONS, TRACE, GET, HEAD, POST



**CURL command**
```sh
curl -X 'OPTIONS' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://192.168.171.119'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)