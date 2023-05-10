### HTTP Missing Security Headers (http-missing-security-headers:clear-site-data) found on http://10.10.10.180
---
**Details**: **http-missing-security-headers:clear-site-data**  matched at http://10.10.10.180

**Protocol**: HTTP

**Full URL**: http://10.10.10.180

**Timestamp**: Wed May 10 20:04:17 +0100 BST 2023

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
Host: 10.10.10.180
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: private
Content-Type: text/html; charset=utf-8
Date: Wed, 10 May 2023 19:04:18 GMT
Vary: Accept-Encoding



<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Home - Acme Widgets</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" href="/css/umbraco-starterkit-style.css" />
    
</head>

<body class="frontpage theme-font-mono theme-color-water">
    <div class="mobile-nav">
        <nav class="nav-bar">
            
<!-- uncomment this line if you don't want it to appear in the top navigation -->
<a class="nav-link navi-link--active" href="/">Home</a>
    <a class="nav-link" href="/products/">Products</a>
    <a class="nav-link" href="/people/">People</a>
    <a class="nav-link" href="/about-us/">About Us</a>
    <a class="nav-link" href="/contact/">Contact</a>
    <a class="nav-link" href="/intranet/">Intranet</a>

        </nav>
    </div>

    <header class="header">

        <div class="logo">
                <a class="nav-link nav-link--home nav-link--home__text logo-text" href="/">Acme Widgets</a>
        </div>

        <nav class="nav-bar top-nav">
            
<!-- uncomment this line if you don't want it to appear in the top navigation -->
<a class="nav-link navi-link--active" href="/">Home</a>
    <a class="nav-link" href="/products/">Products</a>
    <a class="nav-link" href="/people/">People</a>
    <a class="nav-link" href="/about-us/">About Us</a>
    <a class="nav-link" href="/contact/">Contact</a>
    <a class="nav-link" href="/intranet/">Intranet</a>

        </nav>

        <div class="mobile-nav-handler">
            <div class="hamburger lines" id="toggle-nav">
                <span></span>
            </div>
        </div>

    </header>

    <main>
        
<section class="section section--full-height background-image-full overlay overlay--dark section--content-center section--thick-border"
         style="background-image: url('/media/1032/acme.jfif')">
    <div class="section__hero-content">
        <h1>Welcome to Acme</h1>
        <p class="section__description">Allow me to introduce myself. My name is Wile E. Coyote, genius. I am not selling anything nor am I working my way through college.</p>
            <a class="button button--border--solid" href="/products/">
                Check our products
            </a>
    </div>
</section>

<section class="section section">
    


    <div class="umb-grid">
                <div class="grid-section">
    <div >
        <div class="row clearfix">
            <div class="col-md-12 column">
                <div >
                            
    

    
        <div class="blogposts">

                <a href="/blog/another-one/" class="blogpost">
                    <div class="blogpost-meta">
                        <small class="blogpost-date">2/19/2020</small>
                        <small class="blogpost-cat">
                                <!-- TODO: Add links to categories-->
cg16     
    <!-- TODO: Add links to categories-->
codegarden     
    <!-- TODO: Add links to categories-->
umbraco     

                        </small>
                    </div>
                    <h3 class="blogpost-title">Now it gets exciting</h3>
                    <div class="blogpost-excerpt">Donec sollicitudin molestie malesuada. Vivamus suscipit tortor eget felis porttitor volutpat. Sed porttitor lectus nibh.</div>
                </a>
                <a href="/blog/this-will-be-great/" class="blogpost">
                    <div class="blogpost-meta">
                        <small class="blogpost-date">2/19/2020</small>
                        <small class="blogpost-cat">
                                <!-- TODO: Add links to categories-->
great     
    <!-- TODO: Add links to categories-->
umbraco     

                        </small>
                    </div>
                    <h3 class="blogpost-title">This will be great</h3>
                    <div class="blogpost-excerpt">Proin eget tortor risus. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Vivamus magna justo, lacinia eget consectetur sed</div>
                </a>
                <a href="/blog/my-blog-post/" class="blogpost">
                    <div class="blogpost-meta">
                        <small class="blogpost-date">2/19/2020</small>
                        <small class="blogpost-cat">
                                <!-- TODO: Add links to categories-->
demo     
    <!-- TODO: Add links to categories-->
umbraco     
    <!-- TODO: Add links to categories-->
starter kit     

                        </small>
                    </div>
                    <h3 class="blogpost-title">My Blog Post</h3>
                    <div class="blogpost-excerpt">Lorem ipsum dolor sit amet, consectet.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36' 'http://10.10.10.180'
```
---
Generated by [Nuclei v2.9.3](https://github.com/projectdiscovery/nuclei)