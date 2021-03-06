### Apache Detection (apache-detect) found on http://10.129.1.179
---
**Details**: **apache-detect**  matched at http://10.129.1.179

**Protocol**: HTTP

**Full URL**: http://10.129.1.179

**Timestamp**: Sat Jun 18 09:33:45 +0100 BST 2022

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
Host: 10.129.1.179
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
Accept-Ranges: bytes
Content-Type: text/html
Date: Sat, 18 Jun 2022 08:33:44 GMT
Etag: "b7-54eb1537f9bce-gzip"
Last-Modified: Thu, 04 May 2017 11:46:40 GMT
Server: Apache/2.4.7 (Ubuntu)
Vary: Accept-Encoding

<html>
<title>Under Development!</title>
<body>
<center><img src="underdev.gif"></center>
<p>
<p>
<p>
<p>
<center><b>We will soon be right here with you!</b></center>
</body>
</html>

```

**Extra Information**

**Extracted results**:

- Apache/2.4.7 (Ubuntu)



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://10.129.1.179'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)