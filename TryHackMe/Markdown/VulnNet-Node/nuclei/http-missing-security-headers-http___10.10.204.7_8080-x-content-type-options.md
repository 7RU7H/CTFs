### HTTP Missing Security Headers (http-missing-security-headers:x-content-type-options) found on http://10.10.204.7:8080
---
**Details**: **http-missing-security-headers:x-content-type-options**  matched at http://10.10.204.7:8080

**Protocol**: HTTP

**Full URL**: http://10.10.204.7:8080

**Timestamp**: Sat Jan 28 21:40:48 +0000 GMT 2023

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
Host: 10.10.204.7:8080
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 7599
Content-Type: text/html; charset=utf-8
Date: Sat, 28 Jan 2023 21:40:48 GMT
Etag: W/"1daf-dPXia8DLlOwYnTXebWSDo/Cj9Co"
Set-Cookie: session=eyJ1c2VybmFtZSI6Ikd1ZXN0IiwiaXNHdWVzdCI6dHJ1ZSwiZW5jb2RpbmciOiAidXRmLTgifQ%3D%3D; Max-Age=1200; Path=/; Expires=Sat, 28 Jan 2023 22:00:48 GMT; HttpOnly
X-Powered-By: Express

<!DOCTYPE html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="description" content="A layout example that shows off a blog page with a list of posts."><title>VulnNet &ndash; Your reliable news source &ndash; Try Now!</title><link rel="stylesheet" href="/css/pure/pure-min.css"><link rel="stylesheet" href="/css/pure/grids-responsive-min.css"><link rel="stylesheet" href="/css/pure/styles.css"></head><div class="pure-g" id="layout"><div class="sidebar pure-u-1 pure-u-md-1-4"><div class="header"><h1 class="brand-title">Welcome, Guest</h1><h2 class="brand-tagline">Please login to continue...</h2><nav class="nav"><ul class="nav-list"><li class="nav-item"><a class="pure-button" href="/login">➤ Login Now</a></li></ul></nav></div></div><div class="content pure-u-1 pure-u-md-3-4"><div><!-- A wrapper for all the blog posts--><div class="posts"><h1 class="content-subhead">Pinned Post</h1><!-- A single blog post--><section class="post"><header class="post-header"><img class="post-avatar" width="48" height="48" alt="Tilo Mitra's avatar" src="/img/common/tilo-avatar.png"><h2 class="post-title">VulnNet Entertainment confirms data breach!</h2><p class="post-meta">By <a class="post-author" href="#">Tilo Mitra</a> under <a class="post-category post-category-js" href="#">Data Breach</a><a class="post-category post-category-pure" href="#" style="background-color: yellow; color: black">Warning</a></p></header><div class="post-description"><p>As leaked recently VulnNet Entertainment confirms the <a href="#">recent data breach</a> on one of their servers. Although it's not suprising given that the website was long deprecated, it's still a huge threat to the customers. VulnNet Entertainment states that such breach won't happen again, but can the customers feel safe? Maybe it's time for them move on? Let's hope that newest technology they implemented will increase the security, performance and overall functionality. See you in the next article soon!</p></div></section></div><div class="posts"><h1 class="content-subhead">Recent Posts</h1><!-- A single blog post--><section class="post"><header class="post-header"><img class="post-avatar" width="48" height="48" alt="Tilo Mitra's avatar" src="/img/common/ericf-avatar.png"><h2 class="post-title">Critical Remote Hacking Flaws Affect D-Link VPN Routers</h2><p class="post-meta">By <a class="post-author" href="#">Eric Ferraiuolo</a> under <a class="post-category post-category-design" href="#" style="background-color: black">RCE</a><a class="post-category post-category-js" href="#">Cyber Threat</a></p></header><div class="post-description"><p>Discovered by researchers at Digital Defense, the three security shortcomings were responsibly disclosed to D-Link on August 11, which, if exploited, could allow remote attackers to execute arbitrary commands on vulnerable networking devices via specially-crafted requests and even launch denial-of-service attacks. D-Link DSR-150, DSR-250, DSR-500, and DSR-1000AC and other VPN router models in the DSR Family running firmware version 3.14 and 3.17 are vulnerable to the remotely exploitable root command injection flaw.</p></div></section></div><div class="posts"><h1 class="content-subhead">Recent Posts</h1><section class="post"><header class="post-header"><img class="post-avatar" width="48" height="48" alt="Eric Ferraiuolo's avatar" src="/img/common/tilo-avatar.png"><h2 class="post-title">"There will always be insecure code" - Tommy DeVoss</h2><p class="post-meta">By <a class="post-author" href="#">Tilo Mitra</a> under <a class="post-category post-category-design" href="#" style="background-color: orange">Bug Bounty</a></p></header><div class="post-description"><p>‘As long as people are the ones writing code, there’s going to be insecure code’ – Tommy DeVoss on his post-jail bug bounty exploits. It’s definitely getting harder to find bugs, but since more and more companies and government organizations are doing bug bounty programs, there’s never a lack of bugs to find. I don’t think we’re ever going to have a shortage of bugs. As more and more people get into bug bounties, the competition’s definitely going to get harder, but I believe people will still be able to make a living from this if they are determined enough.</p></div></section><section class="post"><header class="post-header"><img class="post-avatar" width="48" height="48" alt="Reid Burke's avatar" src="/img/common/reid-avatar.png"><h2 class="post-title">Photos related to Security and Researchers</h2><p class="post-meta">By <a class="post-author" href="#">Reid Burke</a> under <a class="post-category post-category-pure" href="#">Photos</a></p></header><div class="po.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2919.83 Safari/537.36' 'http://10.10.204.7:8080'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)