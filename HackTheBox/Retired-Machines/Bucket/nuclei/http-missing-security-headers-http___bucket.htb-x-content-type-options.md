### HTTP Missing Security Headers (http-missing-security-headers:x-content-type-options) found on http://bucket.htb
---
**Details**: **http-missing-security-headers:x-content-type-options**  matched at http://bucket.htb

**Protocol**: HTTP

**Full URL**: http://bucket.htb

**Timestamp**: Mon Jan 9 12:34:57 +0000 GMT 2023

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
Host: bucket.htb
User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/html
Date: Mon, 09 Jan 2023 12:34:58 GMT
Etag: "14e0-5f1d3fb90ea76-gzip"
Last-Modified: Mon, 09 Jan 2023 12:34:04 GMT
Server: Apache/2.4.41 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title></title>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box
}

body{
    background-color: #e9e9e9;
    font-family: 'Poppins', sans-serif;
}

/*-------------------Start Header-------------------*/
header{
    width: 100%;
    height:60px;
    padding: 0 200px;
    background-color: #f1f1f1;
    box-shadow: 2px 1px 1px rgba(0, 0, 0, 0.2);
}

/*--------------Logo----------------*/
.logo{
    width: 50%;
    line-height: 60px;
    float: left;
}

h2 a{
    text-decoration: none;
    color: #252525;
}

/*----------------Menu---------------*/
.menu{
    width: 50%;
    float: left;
}

.menu ul{
    list-style: none;
    line-height: 60px;
    padding-left: 35%;
}

.menu ul li{
    display: inline-block;
    padding-left: 30px;
}

.menu ul li a{
    text-decoration: none;
    color: #4b4949;
}

.menu ul .active{
    color: #000000;
    font-weight: 600;
}

/*----------------Start Body----------------*/
main{
    display: flex;
    max-width: 1200px;
}

/*------------------Aside------------------*/
aside{
    width: 210px;
    height: 480px;
    background-color: #ffffff;
    border-radius: 5px;
    margin: 25px 0 0 200px;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/*-------------------Image------------------*/
.image{
    padding:20px 40px;
}

.image img{
    border-radius: 50%;
}

/*--------------------Text------------------*/
.text{
    padding: 0 6%;
}
.text p:nth-child(1){
    font-size: 12px;
    font-weight: 600;
    padding-bottom: 15px;
}

.text h5{
    padding-top: 10px;
}

.text p:nth-child(4){
    font-size: 12px;
    color: #858585;
    padding-top: 10px;
}

.text p:nth-child(5){
    font-size: 12px;
    color: #858585;
    padding-top: 10px;
    padding-bottom: 10px;
}
/*------Social Icons-------*/
.social-icons{
    font-size: 18px;
    padding-top: 10px;
}

.social-icons a{
    color: #4b4949;
    padding-left: 6px;
}

.text span{
    font-size: 12px;
    color: #858585;
    padding-top: 5px;
    text-transform: uppercase;
}


/*----------------Section--------------*/
article{
    width: 670px;
    height: 190px;
    background-color: #ffffff;
    border-radius: 5px;
    margin-top: 25px;
    margin-left: 30px;
    padding: 15px;
    display: flex;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/*--------Coffee Image---------*/
.coffee{
    width: 170px;
}
.coffee >img{
    border-radius: 5px;
}

.description{
    width: 470px;
    padding-left: 5px;
}

.description span{
    text-transform: uppercase;
    font-size: 12px;
    color: #858585;
}

.description p{
    font-size: 12.5px;
    padding-top: 5px;
    color: #777777;
}

.description p a{
    color: #858585;
}

/*---------Remain Three Articles---------*/
.articles article{
    width: 670px;
    height: 190px;
    background-color: #ffffff;
    border-radius: 5px;
    margin-top: 25px;
    margin-left: 30px;
    padding: 15px;
    display: flex;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.articles{
        margin-top: 18%;
        margin-left: -58.2%;
}

/*----------------------------------------*/
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
<body translate="no">
<body>
<header>
<div class="logo">
<h2><a href="#">Bucket Advertising Platform</a></h2>
</div>
<div class="menu">
<ul>
<li class="active"><a href="#">Home</a></li>
<li><a href="#">About</a></li>
<li><a href="#">Feeds</a></li>
</ul>
</div>
</header>

<main>

<aside>
<div class="image">
</div>
<div class="text">
<p>Customize Ads that suits to your business</br></br></br>Contact Us on support@bucket.htb</br></br>Mob: +1 0011223344</p>

<hr style="width: 52%;">
<hr style="width: 52%;">

<div class="social-icons">
<a href="#"><i class="fab fa-facebook-square"></i></a>
<a href="#"><i class="fab fa-twitter"></i></a>
<a href="#"><i class="fab fa-instagram"></i></a>
<a href="#"><i class="fab fa-linkedin"></i></a>
</div>
</div>
</aside>

<article>
<div class="coffee">
<img src="http://s3.bucket.htb/adserver/images/bug.jpg" alt="Bug" height="160" width="160">
</div>
<div class="description">
<h3>Bug Bounty and 0day Research</h3>
<span>march 17, 2020 | Security</span>
<p>Customised bug bounty and new 0day feeds. Feeds can be used on TV, mobile, desktop and web applications. Collecting security feeds from 100+ different trusted sources around the world.</p>
</div>
</article>
<div class="articles">

<article>
<div class="coffee">
<img src="http://s3.bucket.htb/adserver/images/malware.png" alt="Malware" height="160" width="160">
</div>
<div class="description">
<h3>Ransomware Alerts</h3>
<span>march 17, 2020 | Malware</span>
<p>Run awareness ad campaigns on Ransomwares and other newly found mal.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://bucket.htb'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)