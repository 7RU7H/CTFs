### Apache Detection (apache-detect) found on http://10.129.1.190
---
**Details**: **apache-detect**  matched at http://10.129.1.190

**Protocol**: HTTP

**Full URL**: http://10.129.1.190

**Timestamp**: Thu Jul 7 07:52:07 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Apache Detection |
| Authors | philippedelteil |
| Tags | tech, apache |
| Severity | info |
| Description | Some Apache servers have the version on the response header. The OpenSSL version can be also obtained |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.1.190
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html
Date: Thu, 07 Jul 2022 06:52:07 GMT
Server: Apache/2.2.22 (Ubuntu)
Vary: Accept-Encoding
X-Powered-By: PHP/5.3.10-1ubuntu3.26

<center><img src="omg.jpg"/></center>

```

**Extra Information**

**Extracted results**:

- Apache/2.2.22 (Ubuntu)



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36' 'http://10.129.1.190'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)