### HTTP Missing Security Headers (http-missing-security-headers:strict-transport-security) found on http://192.168.141.62
---
**Details**: **http-missing-security-headers:strict-transport-security**  matched at http://192.168.141.62

**Protocol**: HTTP

**Full URL**: http://192.168.141.62

**Timestamp**: Sat Sep 17 21:53:55 +0100 BST 2022

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
Host: 192.168.141.62
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
Content-Length: 6927
Content-Type: text/html; charset=utf-8
Date: Sat, 17 Sep 2022 20:53:55 GMT
Server: nginx/1.16.1
Vary: Cookie
X-Frame-Options: SAMEORIGIN

<!doctype html>
<html lang="en">


<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Home | Mezzanine</title>
<link rel="shortcut icon" href="/static/img/favicon.ico">


<link rel="alternate" type="application/rss+xml" title="RSS" href="/blog/feeds/rss/">
<link rel="alternate" type="application/atom+xml" title="Atom" href="/blog/feeds/atom/">



<link rel="stylesheet" href="/static/css/bootstrap.css">
<link rel="stylesheet" href="/static/css/mezzanine.css">
<link rel="stylesheet" href="/static/css/bootstrap-theme.css">






<script src="/static/mezzanine/js/jquery-1.8.3.min.js"></script>
<script src="/static/js/bootstrap.js"></script>
<script src="/static/js/bootstrap-extras.js"></script>



<!--[if lt IE 9]>
<script src="/static/js/html5shiv.js"></script>
<script src="/static/js/respond.min.js"></script>
<![endif]-->


</head>

<body id="body">

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle Navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    
    <a class="navbar-brand" href="/">Mezzanine</a>
    <p class="navbar-text visible-lg">An open source content management platform.</p>
    
</div>
<div class="navbar-collapse collapse">
    
<form action="/search/" class="navbar-form navbar-right" role="search">

<div class="form-group">
    <input class="form-control" placeholder="Search" type="text" name="q" value="">
</div>


    
    <div class="form-group">
    <select class="form-control" name="type">
        <option value="">Everything</option>
        
        <option value="blog.BlogPost"
            >
            Blog posts
        </option>
        
        <option value="pages.Page"
            >
            Pages
        </option>
        
    </select>
    </div>
    


<input type="submit" class="btn btn-default" value="Go">

</form>

    
<ul class="nav navbar-nav"><li class="active" id="dropdown-menu-home"><a href="/">Home</a></li><li class="dropdown
               "
        id="about"><a href="/about/"
        
        class="dropdown-toggle disabled" data-toggle="dropdown"
        >
            About
            <b class="caret"></b></a><ul class="dropdown-menu"><li class="
               "
        id="about-team"><a href="/about/team/">Team</a></li><li class="
               "
        id="about-history"><a href="/about/history/">History</a></li></ul></li><li class="
               "
        id="blog"><a href="/blog/"
        >
            Blog
            
        </a></li><li class="
               "
        id="gallery"><a href="/gallery/"
        >
            Gallery
            
        </a></li><li class="dropdown
               "
        id="contact"><a href="/contact/"
        
        class="dropdown-toggle disabled" data-toggle="dropdown"
        >
            Contact
            <b class="caret"></b></a><ul class="dropdown-menu"><li class="
               "
        id="contact-legals"><a href="/contact/legals/">Legals</a></li></ul></li></ul>

</div>
</div>
</div>

<div class="container">





<h1>Home</h1>

<ul class="breadcrumb">
<li class="active">Home</li>
</ul>

</div>

<div class="container">
<div class="row">

<div class="col-md-2 left">
    
    <div class="panel panel-default tree">

<ul class="nav nav-list navlist-menu-level-0"><li class="active" id="tree-menu-home"><a href="/">Home</a></li><li class="
    
    
    " id="tree-menu-about"><a href="/about/">About</a><ul class="nav nav-list navlist-menu-level-1"><li class="
    
    
    " id="tree-menu-about-team"><a href="/about/team/">Team</a></li><li class="
    
    
    " id="tree-menu-about-history"><a href="/about/history/">History</a></li></ul></li><li class="
    
    
    " id="tree-menu-blog"><a href="/blog/">Blog</a></li><li class="
    
    
    " id="tree-menu-gallery"><a href="/gallery/">Gallery</a></li><li class="
    
    
    " id="tree-menu-contact"><a href="/contact/">Contact</a><ul class="nav nav-list navlist-menu-level-1"><li class="
    
    
    " id="tree-menu-contact-legals"><a href="/contact/legals/">Legals</a></li></ul></li></ul>
</div>
    
</div>

<div class="col-md-7 middle">
    

<h2>Congratulations!</h2>
<p>
    Welcome to your new Mezzanine powered website.
    Here are some quick links to get you started:
</p>
<ul>
    <li><a href="/admin/">Log in to the admin interface</a></li>
    <li><a href="http://mezzanine.jupo.org/docs/content-architecture.html">Creating custom page types</a></li>
    <li><a href="http://mezzanine.jupo.or.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://192.168.141.62'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)