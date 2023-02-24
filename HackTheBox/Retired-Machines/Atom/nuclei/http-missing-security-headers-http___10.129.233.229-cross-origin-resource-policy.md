### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on http://10.129.233.229
---
**Details**: **http-missing-security-headers:cross-origin-resource-policy**  matched at http://10.129.233.229

**Protocol**: HTTP

**Full URL**: http://10.129.233.229

**Timestamp**: Fri Feb 24 12:16:33 +0000 GMT 2023

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
Host: 10.129.233.229
User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 7581
Accept-Ranges: bytes
Content-Type: text/html
Date: Fri, 24 Feb 2023 12:16:34 GMT
Etag: "1d9d-5beda4be62d6d"
Last-Modified: Wed, 31 Mar 2021 19:41:09 GMT
Server: Apache/2.4.46 (Win64) OpenSSL/1.1.1j PHP/7.3.27

<!DOCTYPE html>
<html lang="en" >
<head>
<title>Heed Solutions</title> 
<style>
.logo {
            width: 3rem;
        }
        .footer--bgc{
            background-color: #9fef00;
        }
        .main--bgc{
            background-color: #1a2332;
        }
        .card--windows{
            padding-top: 4rem!important;
            
        }

       
        .container--title{
            max-width: 768px;
        }
        @media (min-width: 768px){
            .w-md-100{
                width: 100%!important;
            }
        }
        @media (min-width: 992px) {
            .nav-link::after {
                content: '';
                display: block;
                width: 0;
                height: 2px;
                background: #597FE2;
                margin: 0 auto;
                -webkit-transition: width .1s;
                transition: width .1s;
            }

            .nav-link:hover::after {
                width: 40%;
            }
        }
</style>

  <script>
  window.console = window.console || function(t) {};
</script>
<script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
</script>
</head>

<body translate="no" >
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" style="color:black">Heed Solutions</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto font-weight-bold small">
                    <li class="nav-item  ">
                        <a class="nav-link active" style="color:black" aria-current="page" href="#">About</a>
                    </li>
                    

                </ul>
            </div>
        </div>
    </nav>
<div></div>
   <div class="main--bgc pb-5">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 130 1440 85"><path fill="#f8f9fa" fill-opacity="1" d="M0,224L60,224C120,224,240,224,360,213.3C480,203,600,181,720,181.3C840,181,960,203,1080,197.3C1200,192,1320,160,1380,144L1440,128L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path></svg>
   
    <div class="container container--title ">
        <div class="row min-vh-75">
            <div class="col text-center mt-5 mb-5">
                <h1 class="mb-4" style="color:white;">Heed | A Leading Software Organization</h1>
                <p class="h5" style="color:white">We make software to help people in their daily routines. We bring the best to the market. Introducing A Simple Note Taking Application.</p>
            </div>
        </div>
    </div>
    <div>
       <center><img src='images/heed.png' height="500" width="700"/></center>
    </div>

    <div class="container mt-5 ">
        <div class="row align-items-center ">
            <div class="col-xl-4 ">
                <div class="card text-center p-5 shadow mx-auto mb-4 mb-xl-0" style="width: 22rem;">
                    <img style="width: 50px!important;" src="https://cdn0.iconfinder.com/data/icons/social-media-free-5/32/apple_online_social_media_mac-512.png" class="card-img-top mx-auto" alt="Apple Logo">
                    <div class="card-body">
                        <a class="btn btn-outline-dark font-weight-bold" href="#" role="button">Download for Mac</a>
                        <p class="card-text text-muted small">Coming soon</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card text-center p-5  shadow mx-auto mb-4 mb-xl-0 card--windows " style="width: 22rem;">
                    <img style="width: 50px!important;" src="https://cdn1.iconfinder.com/data/icons/logotypes/32/windows-512.png" class="card-img-top mx-auto" alt="Windows Logo">
                    <div class="card-body pb-0">
                        <a class="btn btn-primary mb-2 font-weight-bold" href="/releases/heed_setup_v1.0.0.zip" role="button">Download for Windows</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card text-center p-5 shadow mx-auto" style="width: 22rem;">
                    <img style="width: 50px!important;" src="https://cdn3.iconfinder.com/data/icons/logos-brands-3/24/logo_b.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36' 'http://10.129.233.229'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)