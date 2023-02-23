### Wappalyzer Technology Detection (tech-detect:ms-iis) found on http://10.129.96.140/mvc
---
**Details**: **tech-detect:ms-iis**  matched at http://10.129.96.140/mvc

**Protocol**: HTTP

**Full URL**: http://10.129.96.140/mvc

**Timestamp**: Sat Feb 4 10:58:52 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET /mvc HTTP/1.1
Host: 10.129.96.140
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 301 Moved Permanently
Connection: close
Content-Length: 148
Content-Type: text/html; charset=UTF-8
Date: Sat, 04 Feb 2023 10:58:51 GMT
Location: http://10.129.96.140/mvc/
Server: Microsoft-IIS/10.0
X-Powered-By: ASP.NET


```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.96.140/mvc' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://10.129.96.140/mvc/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)