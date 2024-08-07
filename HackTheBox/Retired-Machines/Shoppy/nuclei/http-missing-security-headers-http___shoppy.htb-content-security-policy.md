### HTTP Missing Security Headers (http-missing-security-headers:content-security-policy) found on http://shoppy.htb
---
**Details**: **http-missing-security-headers:content-security-policy**  matched at http://shoppy.htb

**Protocol**: HTTP

**Full URL**: http://shoppy.htb

**Timestamp**: Sun Sep 25 15:05:17 +0100 BST 2022

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
Host: shoppy.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 2178
Accept-Ranges: bytes
Cache-Control: public, max-age=0
Content-Type: text/html; charset=UTF-8
Date: Sun, 25 Sep 2022 14:05:18 GMT
Etag: W/"882-17eb4a698a0"
Last-Modified: Tue, 01 Feb 2022 09:38:44 GMT
Server: nginx/1.23.1

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>
            Shoppy Wait Page
        </title>
        <link href="favicon.png" rel="shortcut icon" type="image/png">
        <link href="css/roboto.css" rel="stylesheet" type="text/css">
        <link href="css/loader.css" rel="stylesheet" type="text/css">
        <link href="css/normalize.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <script src="js/jquery.js"></script>
    </head>
    <body>
        <div class="preloader">
            <div class="loading">
                <h2>
                    Loading...
                </h2>
                <span class="progress"></span>
            </div>
        </div>
        <div class="wrapper">
            <ul class="scene unselectable" data-friction-x="0.1" data-friction-y="0.1" data-scalar-x="25" data-scalar-y="15" id="scene">
                <li class="layer" data-depth="0.00">
                </li>
                <li class="layer" data-depth="0.10">
                    <div class="background">
                    </div>
                </li>
                <li class="layer" data-depth="0.20">
                    <div class="title">
                        <h2>
                            SHOPPY
                        </h2>
                        <span class="line"></span>
                    </div>
                </li>
                <li class="layer" data-depth="0.30">
                    <div class="hero">
                        <h1 id="countdown">
                            Shoppy beta coming soon ! Stay tuned for beta access !
                        </h1>
                        <p class="sub-title">
                            Shoppy beta coming soon ! Stay tuned for beta access !
                        </p>
                    </div>
                </li>
            </ul>
        </div>
        <script src="js/plugins.js"></script>
        <script src="js/jquery.countdown.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://shoppy.htb'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)