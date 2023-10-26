### HTTP Missing Security Headers (http-missing-security-headers:content-security-policy) found on http://192.168.237.93/

----
**Details**: **http-missing-security-headers:content-security-policy** matched at http://192.168.237.93/

**Protocol**: HTTP

**Full URL**: http://192.168.237.93/

**Timestamp**: Thu Oct 26 15:38:21 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.237.93
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=utf-8
Date: Thu, 26 Oct 2023 14:38:14 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.6 (CentOS) PHP/7.3.22
Set-Cookie: PHPSESSID=mdudlet9n2qh5fvn67brkjkl5q; path=/
Vary: Accept-Encoding
X-Powered-By: PHP/7.3.22

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<meta name="generator" content="HTMLy v2.7.5" />
<link rel="icon" type="image/x-icon" href="/favicon.ico" />
<link rel="sitemap" href="/sitemap.xml" />
<link rel="alternate" type="application/rss+xml" title="Sybaris Feed" href="/feed/rss" />

    <title>Sybaris - Just another HTMLy blog</title>
    <meta name="description" content="Proudly powered by HTMLy, a databaseless blogging platform."/>
    <link rel="canonical" href="/" />
     
    <link rel="stylesheet" id="twentysixteen-fonts-css" href="https://fonts.googleapis.com/css?family=Merriweather%3A400%2C700%2C900%2C400italic%2C700italic%2C900italic%7CMontserrat%3A400%2C700%7CInconsolata%3A400&#038;subset=latin%2Clatin-ext" type="text/css" media="all" />
    <link rel="stylesheet" id="genericons-css"  href="/themes/twentysixteen/genericons/genericons.css" type="text/css" media="all" />
    <link rel="stylesheet" id="twentysixteen-style-css"  href="/themes/twentysixteen/css/style.css" type="text/css" media="all" />
    <!--[if lt IE 10]>
    <link rel="stylesheet" id="twentysixteen-ie-css"  href="/themes/twentysixteen/css/ie.css" type="text/css" media="all" />
    <![endif]-->
    <!--[if lt IE 9]>
    <link rel="stylesheet" id="twentysixteen-ie8-css"  href="/themes/twentysixteen/css/ie8.css" type="text/css" media="all" />
    <![endif]-->
    <!--[if lt IE 8]>
    <link rel="stylesheet" id="twentysixteen-ie7-css"  href="/themes/twentysixteen/css/ie7.css" type="text/css" media="all" />
    <![endif]-->
</head>
<body class="no-posts">
    <div id="page" class="site">
        <div class="site-inner">
        
            <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

            <header id="masthead" class="site-header" role="banner">
                <div class="site-header-main">
                    <div class="site-branding">
                        <h1 class="site-title"><a href="/" rel="home">Sybaris</a></h1>
                        <p class="site-description">Just another HTMLy blog</p>
                    </div><!-- .site-branding -->

                    <button id="menu-toggle" class="menu-toggle">Menu</button>

                    <div id="site-header-menu" class="site-header-menu">
                        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Primary Menu">
                            <div class="menu-main-container">
                                <ul class="nav primary-menu"><li class="item first active"><a href="/">Home</a></li></ul>                            </div>
                        </nav><!-- .main-navigation -->
                    </div><!-- .site-header-menu -->
                    
                </div><!-- .site-header-main -->
            </header><!-- .site-header -->

            <div id="content" class="site-content">

                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <article class="page type-page hentry">
    <header class="entry-header">
        <h1 class="entry-title">No posts found!</h1>    
    </header><!-- .entry-header -->
</article><!-- #post-## -->                    </main><!-- .site-main -->
                </div><!-- .content-area -->


                <aside id="secondary" class="sidebar widget-area" role="complementary">
                    <section class="widget widget_text">
                        <h2 class="widget-title">About</h2>
                        <div class="textwidget"><p>Proudly powered by HTMLy, a databaseless blogging platform.</p>
                        </div>
                    </section>
                    
                    <section id="search" class="widget widget_search">
                        <form role="search" class="search-form">
                        <label>
                            <span class="screen-reader-text">Search for:</span>
                            <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="search" title="Search for:" />
                        </label>
                        <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span></button>
                        </form>
                    </section>    
                    
                    <section id="recent-posts" class="widget widget_recent_entries">        
                        <h2 class="widget-title">Recent posts</h2>
                        <ul><li>No recent posts found</.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36' 'http://192.168.237.93/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)