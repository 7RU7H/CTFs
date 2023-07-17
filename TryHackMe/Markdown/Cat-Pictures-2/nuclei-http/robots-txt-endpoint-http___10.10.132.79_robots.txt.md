### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.10.132.79/

----
**Details**: **robots-txt-endpoint** matched at http://10.10.132.79/

**Protocol**: HTTP

**Full URL**: http://10.10.132.79/robots.txt

**Timestamp**: Sat Jul 1 18:23:59 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
GET /robots.txt HTTP/1.1
Host: 10.10.132.79
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 136
Accept-Ranges: bytes
Content-Type: text/plain
Date: Sat, 01 Jul 2023 17:23:15 GMT
Etag: "57543c70-88"
Last-Modified: Sun, 05 Jun 2016 14:51:28 GMT
Server: nginx/1.4.6 (Ubuntu)

User-agent: *
Disallow: /data/
Disallow: /dist/
Disallow: /docs/
Disallow: /php/
Disallow: /plugins/
Disallow: /src/
Disallow: /uploads/
```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://10.10.132.79/robots.txt'
```

----

Generated by [Nuclei v2.9.7](https://github.com/projectdiscovery/nuclei)