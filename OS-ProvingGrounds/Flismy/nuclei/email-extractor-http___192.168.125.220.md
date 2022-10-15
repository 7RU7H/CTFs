### Email Extractor (email-extractor) found on http://192.168.125.220
---
**Details**: **email-extractor**  matched at http://192.168.125.220

**Protocol**: HTTP

**Full URL**: http://192.168.125.220

**Timestamp**: Fri Oct 14 12:31:24 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Email Extractor |
| Authors | panch0r3d |
| Tags | misc, email |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.125.220
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
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
Content-Type: text/html
Date: Fri, 14 Oct 2022 11:31:24 GMT
Etag: W/"626b2150-c6cf"
Last-Modified: Thu, 28 Apr 2022 23:20:48 GMT
Server: nginx/1.18.0 (Ubuntu)

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upright</title>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans&display=swap" rel="stylesheet"> <!-- https://fonts.google.com/specimen/Kumbh+Sans -->
    <link rel="stylesheet" href="fontawesome/css/all.min.css">  <!-- https://fontawesome.com/-->  
    <link rel="stylesheet" href="css/magnific-popup.css">       <!-- https://dimsemenov.com/plugins/magnific-popup/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">        <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="slick/slick.min.css">          <!-- https://kenwheeler.github.io/slick/ -->
    <link rel="stylesheet" href="slick/slick-theme.css">
    <link rel="stylesheet" href="css/templatemo-upright.css">

</head>
<body>    
    <div class="container-fluid">
        <div class="row">
            <!-- Leftside bar -->
            <div id="tm-sidebar" class="tm-sidebar"> 
                <nav class="tm-nav">
                    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <div class="tm-brand-box">
                            <h1 class="tm-brand">Upright</h1>
                        </div>                
                        <ul id="tm-main-nav">
                            <li class="nav-item">                                
                                <a href="#home" class="nav-link current">
                                    <div class="triangle-right"></div>
                                    <i class="fas fa-home nav-icon"></i>
                                    Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#gallery" class="nav-link">
                                    <div class="triangle-right"></div>
                                    <i class="fas fa-images nav-icon"></i>
                                    Gallery
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#about" class="nav-link">
                                    <div class="triangle-right"></div>
                                    <i class="fas fa-user-friends nav-icon"></i>
                                    About
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#contact" class="nav-link">
                                    <div class="triangle-right"></div>
                                    <i class="fas fa-envelope nav-icon"></i>
                                    Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="" class="nav-link external" target="_parent" rel="sponsored">
                                    <div class="triangle-right"></div>
                                    <i class="fas fa-external-link-alt nav-icon"></i>
                                    External
                                </a>
                            </li>
                        </ul>
                    </div>
                    <footer class="mb-3 tm-mt-100">
                        Design: <a href="" target="_parent" rel="sponsored"></a>
                    </footer>
                </nav>
            </div>
            
            <div class="tm-main">
                <!-- Home section -->
                <div class="tm-section-wrap">
                    <div class="tm-parallax" data-parallax="scroll" data-image-src="img/img-01.jpg"></div>                   
                    <section id="home" class="tm-section">
                        <h2 class="tm-text-primary">Welcome to Upright</h2>
                        <hr class="mb-5">
                        <div class="row">
                            <div class="col-lg-6 tm-col-home mb-4">
                                <div class="tm-text-container">
                                    <div class="tm-icon-container mb-5 mr-0 ml-auto">
                                        <i class="fas fa-satellite-dish fa-4x tm-text-primary"></i>
                                    </div>                                
                                    <p>
                                        Leftmost column is placed for logo and main menu.
                                        After that is an image column. Righ.... Truncated ....
```

**Extra Information**

**Extracted results**:

- info@upright.com



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://192.168.125.220'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)