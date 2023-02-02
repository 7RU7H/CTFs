### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-headers) found on http://10.129.1.117:8080
---
**Details**: **http-missing-security-headers:access-control-allow-headers**  matched at http://10.129.1.117:8080

**Protocol**: HTTP

**Full URL**: http://10.129.1.117:8080

**Timestamp**: Thu Feb 2 12:22:33 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.1.117:8080
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 404 
Connection: close
Content-Length: 992
Content-Language: en
Content-Type: text/html;charset=utf-8
Date: Thu, 02 Feb 2023 12:22:32 GMT

<!DOCTYPE html><html><head><title>Apache Tomcat/8.5.5 - Error report</title><style type="text/css">H1 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:22px;} H2 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:16px;} H3 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:14px;} BODY {font-family:Tahoma,Arial,sans-serif;color:black;background-color:white;} B {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;} P {font-family:Tahoma,Arial,sans-serif;background:white;color:black;font-size:12px;}A {color : black;}A.name {color : black;}.line {height: 1px; background-color: #525D76; border: none;}</style> </head><body><h1>HTTP Status 404 - /</h1><div class="line"></div><p><b>type</b> Status report</p><p><b>message</b> <u>/</u></p><p><b>description</b> <u>The requested resource is not available.</u></p><hr class="line"><h3>Apache Tomcat/8.5.5</h3></body></html>
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://10.129.1.117:8080'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)