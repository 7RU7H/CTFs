### Wappalyzer Technology Detection (tech-detect:nginx) found on http://10.129.206.181/
---
**Details**: **tech-detect:nginx**  matched at http://10.129.206.181/

**Protocol**: HTTP

**Full URL**: http://10.129.206.181/

**Timestamp**: Sun Jun 25 18:20:32 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.206.181
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 301 Moved Permanently
Connection: close
Content-Length: 178
Content-Type: text/html
Date: Sun, 25 Jun 2023 17:19:53 GMT
Location: http://stocker.htb
Server: nginx/1.18.0 (Ubuntu)

<html>
<head><title>301 Moved Permanently</title></head>
<body>
<center><h1>301 Moved Permanently</h1></center>
<hr><center>nginx/1.18.0 (Ubuntu)</center>
</body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36' 'http://10.129.206.181/'
```
---
Generated by [Nuclei v2.9.4](https://github.com/projectdiscovery/nuclei)