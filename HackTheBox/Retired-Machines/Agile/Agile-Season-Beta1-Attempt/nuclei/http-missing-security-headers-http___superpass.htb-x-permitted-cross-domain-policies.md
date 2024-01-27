### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on http://superpass.htb
---
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies**  matched at http://superpass.htb

**Protocol**: HTTP

**Full URL**: http://superpass.htb

**Timestamp**: Mon Mar 6 19:59:06 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: superpass.htb
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Content-Type: text/html; charset=utf-8
Date: Mon, 06 Mar 2023 19:59:06 GMT
Server: nginx/1.18.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦸</text></svg>">
    <title>SuperPassword 🦸</title>
        <link rel="stylesheet"
          href="/static/css/josefinsans.css">
    <link rel="stylesheet"
          href="/static/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="/static/css/site.css" rel="stylesheet" />
    <link href="/static/css/all.min.css" rel="stylesheet" />
    
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-sm fixed-top">
    <div class="container">
        <span class="navbar-brand d-flex w-50 me-auto">🦸SuperPassword</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav w-100 justify-content-end">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                
                <li class="nav-item"><a class="nav-link" href="/account/login">Login</a></li>
                
            </ul>
        </div>
    </div>
</nav>

    
        <header class="py-5 bg-image-full" style="background-image: url('/static/img/hero.png'); background-size: 100%;">
                <div class="text-center my-5">
                <img class="img-fluid rounded-circle mb-4" src="/static/img/SP.png" alt="..." />
                <h1 class="text-black fs-3 fw-bolder">SuperPassword</h1>
                <p class="text-black-50 mb-0">A Super Password Manager</p>
                </div>
        </header>


    <div class="main_content">
        
<div class="text">
        <h1>The only password manager you'll ever need.</h1>
</div>

<div class="features">
        <div class="container">
                <div class="row">
                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Secure</h2>
                                        <img class="img-fluid rounded-circle" src="/static/img/lock.jpg" alt="">
                                        <p>The safest passwords that can be generated, stored on our secure server.</p>
                                </div>
                        </div>
                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Fast!</h2>
                                        <img class="img-fluid" src="/static/img/rocket.png" alt="">
                                        <p>Generate passwords in the blink of an eye.</p>
                                </div>
                        </div>
                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Convenient</h2>
                                        <img class="img-fluid" src="/static/img/convenient.png" alt="">
                                        <p>Just copy / paste passwords from the vault into your browser. Browser plugin coming soon!</p>
                                </div>
                        </div>
                </div>
                <div class="row">
                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Military Grade Crypto</h2>
                                        <img class="img-fluid" src="/static/img/army.png" alt="">
                                        <p>Only the strongest math!</p>
                                </div>
                        </div>
                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Very Random!</h2>
                                        <img class="img-fluid" src="/static/img/random.png" alt="">
                                        <p>Generated passwords are the most random.</p>
                                </div>
                        </div>

                        <div class="col-sm-4">
                                <div class="feature">
                                        <h2>Agile Developement</h2>
                                        <img class="img-fluid" src="/static/img/agile.png" alt="">
                                        <p>We test and redeploy our website constantly.</p>
                                </div>
          .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://superpass.htb'
```
---
Generated by [Nuclei 2.8.9](https://github.com/projectdiscovery/nuclei)