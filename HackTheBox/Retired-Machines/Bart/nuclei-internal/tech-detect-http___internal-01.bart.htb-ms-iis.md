### Wappalyzer Technology Detection (tech-detect:ms-iis) found on http://internal-01.bart.htb
---
**Details**: **tech-detect:ms-iis**  matched at http://internal-01.bart.htb

**Protocol**: HTTP

**Full URL**: http://internal-01.bart.htb

**Timestamp**: Sat Dec 3 17:53:11 +0000 GMT 2022

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
Host: internal-01.bart.htb
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 302 Found
Connection: close
Content-Length: 4
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Sat, 03 Dec 2022 17:53:10 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Location: simple_chat/login_form.php
Pragma: no-cache
Server: Microsoft-IIS/10.0
Set-Cookie: PHPSESSID=hfi9vm32us3u9p646jslfadp12; path=/
X-Powered-By: PHP/7.1.7


```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://internal-01.bart.htb' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://internal-01.bart.htb/simple_chat/login_form.php'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)