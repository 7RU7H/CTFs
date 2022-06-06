### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-credentials) found on http://10.129.169.203:8080
---
**Details**: **http-missing-security-headers:access-control-allow-credentials**  matched at http://10.129.169.203:8080

**Protocol**: HTTP

**Full URL**: http://10.129.169.203:8080

**Timestamp**: Mon Jun 6 12:04:33 +0100 BST 2022

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
Host: 10.129.169.203:8080
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 5905
Cache-Control: private
Content-Type: text/html; charset=utf-8
Date: Mon, 06 Jun 2022 11:04:32 GMT
Etag: 1aadf451e3e04f6087810f2adf6d8cb3
Server: Microsoft-IIS/7.5
X-Aspnet-Version: 4.0.30319
X-Aspnetmvc-Version: 5.2
X-Generator: Orchard
X-Powered-By: ASP.NET


<!DOCTYPE html> 
<html lang="en-US" class="static dir-ltr orchard-blogs" dir="ltr"> 
<head> 
    <meta charset="utf-8" />
    <title>Tossed Salad - Blog</title> 
    <link href="//fonts.googleapis.com/css?family=Lobster&amp;subset=latin" rel="stylesheet" type="text/css" />
<link href="/Themes/TheThemeMachine/Styles/default-grid.css" rel="stylesheet" type="text/css" />
<link href="/Themes/TheThemeMachine/Styles/Site.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
<script src="/Core/Shapes/scripts/html5.js" type="text/javascript"></script>
<![endif]-->
<meta content="Orchard" name="generator" />
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
<link href="http://localhost:8080/XmlRpc/LiveWriter/Manifest" rel="wlwmanifest" type="application/wlwmanifest+xml" />
<link href="http://localhost:8080/rsd" rel="EditURI" title="RSD" type="application/rsd+xml" />
<link href="/modules/orchard.themes/Content/orchard.ico" rel="shortcut icon" type="image/x-icon" />

<link rel="alternate" type="application/rss+xml" title="Blog" href="/rss?containerid=12" />

    <script>(function(d){d.className="dyn"+d.className.substring(6,d.className.length);})(document.documentElement);</script> 
    <script>window.isRTL = false;</script>
</head> 
<body>


<div class="tripel-123" id="layout-wrapper">
<header id="layout-header" class="group">
    <div id="header">
        <div class="zone zone-header"><h1 id="branding"><a href="/">Tossed Salad</a></h1>
</div>
    </div>
</header>
<div id="layout-navigation" class="group">
    <div class="zone zone-navigation">
<article class="widget-navigation widget-menu-widget widget">
    
<nav>
    <ul class="menu menu-main-menu">
        
        
<li class="current last first"><a href="/">Home</a>
</li>
    </ul>
</nav>
</article></div>
</div>
<div id="layout-main-container">
<div id="layout-main" class="group">
    <div id="layout-content" class="group">
                        <div id="content" class="group">
            <div class="zone zone-content">
<article class="blog content-item">
    <header>
        

<h1>Blog</h1>
            <div class="metadata">
                <div class="published">Friday, September 01, 2017 9:44:04 AM</div>
            </div>
    </header>
    <div data-tab="Content" id="tab-content"><div class="content-description blog-description">
    <p>This is your Orchard Blog.</p>
</div>
</div><div data-tab="Content" id="tab-content">
<ul class="blog-posts content-items"><li class="first">
<article class="content-item blog-post">
    <header>
        

<h1><a href="/pita-pockets-with-a-sun-dried-tomato-flavor">Pita Pockets with a sun dried tomato flavor</a></h1>


        <div class="metadata">
            <div class="published">Friday, September 01, 2017 10:06:09 AM</div><span class="comment-count">No Comments</span>
        </div>
    </header>
    

<p>Simple ingredients which can be&#160;assembled&#160;quickly, makes this pita pocket a go to dish time and again.The sun-dried tomato paste makes this pocket flavorful and&#160;delicious!HINT:&#160; Make the paste in&#160;… <a href="/pita-pockets-with-a-sun-dried-tomato-flavor">more</a></p>
</article></li>
<li class="last">
<article class="content-item blog-post">
    <header>
        

<h1><a href="/Contents/Item/Display/17">Purple cabbage and carrot salad</a></h1>


        <div class="metadata">
            <div class="published">Friday, September 01, 2017 10:05:16 AM</div><span class="comment-count">No Comments</span>
        </div>
    </header>
    

<p>Serves 2 large portionsSalad Ingredients:2 cups thinly sliced purple cabbage3 carrots skinned and grated in a large-holed grater6 spring onions sliced with some of the green shoots1 cup lettuce of a&#160;… <a href="/Contents/Item/Display/17">more</a></p>
</article></li>
</ul>


</div>
</article></div>
        </div>
            </div>
</div>
</div>
<div id="layout-tripel-container">
<div id="layout-tripel" class="group">
    <div id="tripel-first">
        <div class="zone zone-tripel-first">
<article class="widget-tripel-first widget-html-widget widget">
    <header>
        <h1>First Leader Aside</h1>
        
    </header>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a nibh ut tortor dapibus vestibulum. Aliquam vel sem nibh. Suspendisse vel condimentum tellus.</p>
</article></div>
    </div>
        <div id="tripel-second">
        <div class="zone zone-tripel-second">
<article class="widget-tripel-second widget-html-widget widget">
    <header>
        <h1>Second Leader Aside</h1>
        
    </header>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a nibh ut tortor dapibus vestibulum. Aliquam vel sem nibh. Suspendisse vel condimentum tellus.</p>
</article></di.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.169.203:8080'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)