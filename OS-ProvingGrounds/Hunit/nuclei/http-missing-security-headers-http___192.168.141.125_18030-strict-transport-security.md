### HTTP Missing Security Headers (http-missing-security-headers:strict-transport-security) found on http://192.168.141.125:18030
---
**Details**: **http-missing-security-headers:strict-transport-security**  matched at http://192.168.141.125:18030

**Protocol**: HTTP

**Full URL**: http://192.168.141.125:18030

**Timestamp**: Fri Sep 23 12:26:13 +0100 BST 2022

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
Host: 192.168.141.125:18030
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 886
Accept-Ranges: bytes
Content-Type: text/html
Date: Fri, 23 Sep 2022 11:26:10 GMT
Etag: "376-5b373f8e89951"
Last-Modified: Fri, 06 Nov 2020 17:59:22 GMT
Server: Apache/2.4.46 (Unix)

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Whack A Mole!</title>

  <link href='https://fonts.googleapis.com/css?family=Amatic+SC:400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="./styles.css">
</head>
<body>

  <h1>Whack-a-mole! <span class="score">0</span></h1>
  <button onClick="startGame()">Start!</button>

  <div class="game">
    <div class="hole hole1">
      <div class="mole"></div>
    </div>

    <div class="hole hole2">
      <div class="mole"></div>
    </div>

    <div class="hole hole3">
      <div class="mole"></div>
    </div>

    <div class="hole hole4">
      <div class="mole"></div>
    </div>

    <div class="hole hole5">
      <div class="mole"></div>
    </div>

    <div class="hole hole6">
      <div class="mole"></div>
    </div>
  </div>

  <script src="./scripts.js"></script>
</body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://192.168.141.125:18030'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)