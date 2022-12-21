### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-headers) found on http://10.129.65.195:8080
---
**Details**: **http-missing-security-headers:access-control-allow-headers**  matched at http://10.129.65.195:8080

**Protocol**: HTTP

**Full URL**: http://10.129.65.195:8080

**Timestamp**: Mon Aug 1 20:49:10 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.65.195:8080
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 
Connection: close
Transfer-Encoding: chunked
Content-Language: en
Content-Type: text/html;charset=UTF-8
Date: Mon, 01 Aug 2022 19:49:08 GMT

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta author="wooden_k">
    <!--Codepen by khr2003: https://codepen.io/khr2003/pen/BGZdXw -->
    <link rel="stylesheet" href="css/panda.css" type="text/css">
    <link rel="stylesheet" href="css/main.css" type="text/css">
    <title>Red Panda Search | Made with Spring Boot</title>
  </head>
  <body>

    <div class='pande'>
      <div class='ear left'></div>
      <div class='ear right'></div>
      <div class='whiskers left'>
          <span></span>
          <span></span>
          <span></span>
      </div>
      <div class='whiskers right'>
        <span></span>
        <span></span>
        <span></span>
      </div>
      <div class='face'>
        <div class='eye left'></div>
        <div class='eye right'></div>
        <div class='eyebrow left'></div>
        <div class='eyebrow right'></div>

        <div class='cheek left'></div>
        <div class='cheek right'></div>

        <div class='mouth'>
          <span class='nose'></span>
          <span class='lips-top'></span>
        </div>
      </div>
    </div>
    <h1>RED PANDA SEARCH</h1>
    <div class="wrapper" >
    <form class="searchForm" action="/search" method="POST">
    <div class="wrap">
      <div class="search">
        <input type="text" name="name" placeholder="Search for a red panda">
        <button type="submit" class="searchButton">
          <i class="fa fa-search"></i>
        </button>
      </div>
    </div>
    </form>
    </div>
  </body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://10.129.65.195:8080'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)