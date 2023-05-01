### Laravel Debug Enabled (laravel-debug-enabled) found on http://10.129.199.134
---
**Details**: **laravel-debug-enabled**  matched at http://10.129.199.134

**Protocol**: HTTP

**Full URL**: http://10.129.199.134/_ignition/health-check

**Timestamp**: Mon Apr 24 13:03:48 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Laravel Debug Enabled |
| Authors | notsoevilweasel |
| Tags | debug, laravel, misconfig |
| Severity | medium |
| Description | Laravel with APP_DEBUG set to true is prone to show verbose errors.<br> |
| Remediation | Disable Laravel's debug mode by setting APP_DEBUG to false.<br> |

**Request**
```http
GET /_ignition/health-check HTTP/1.1
Host: 10.129.199.134
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 29
Cache-Control: no-cache, private
Content-Type: application/json
Date: Mon, 24 Apr 2023 12:03:48 GMT
Server: Apache/2.4.29 (Ubuntu)

{"can_execute_commands":true}
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://10.129.199.134/_ignition/health-check'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)