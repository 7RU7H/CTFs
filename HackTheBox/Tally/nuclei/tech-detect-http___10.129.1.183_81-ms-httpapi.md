### Wappalyzer Technology Detection (tech-detect:ms-httpapi) found on http://10.129.1.183:81
---
**Details**: **tech-detect:ms-httpapi**  matched at http://10.129.1.183:81

**Protocol**: HTTP

**Full URL**: http://10.129.1.183:81

**Timestamp**: Mon Jun 6 16:50:39 +0100 BST 2022

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
Host: 10.129.1.183:81
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 400 Bad Request
Connection: close
Content-Length: 334
Content-Type: text/html; charset=us-ascii
Date: Mon, 06 Jun 2022 15:50:38 GMT
Server: Microsoft-HTTPAPI/2.0

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<HTML><HEAD><TITLE>Bad Request</TITLE>
<META HTTP-EQUIV="Content-Type" Content="text/html; charset=us-ascii"></HEAD>
<BODY><h2>Bad Request - Invalid Hostname</h2>
<hr><p>HTTP Error 400. The request hostname is invalid.</p>
</BODY></HTML>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36' 'http://10.129.1.183:81'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)