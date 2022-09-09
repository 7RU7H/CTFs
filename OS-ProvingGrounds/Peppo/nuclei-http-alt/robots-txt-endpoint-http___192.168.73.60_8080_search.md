### robots.txt endpoint prober (robots-txt-endpoint) found on http://192.168.73.60:8080
---
**Details**: **robots-txt-endpoint**  matched at http://192.168.73.60:8080

**Protocol**: HTTP

**Full URL**: http://192.168.73.60:8080/search

**Timestamp**: Fri Sep 9 20:11:44 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /search HTTP/1.1
Host: 192.168.73.60:8080
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK 
Connection: close
Content-Length: 7933
Cache-Control: max-age=0, private, must-revalidate
Content-Type: text/html; charset=utf-8
Date: Fri, 09 Sep 2022 19:12:05 GMT
Etag: W/"f371fa6618365679cccc70c69f75c358"
Referrer-Policy: strict-origin-when-cross-origin
Server: WEBrick/1.4.2 (Ruby/2.6.6/2020-03-31)
Set-Cookie: _redmine_session=c0NqOHhGSklsaFIwa3poUFVaTTB6YmdteDVVRWQza2tTK28yYVFhUU5EOEdNeFI1c1BvZjFRd1FUWGlld3p5alFQSWFKUGhEcFljOExuaHJJRzV3allCTlJCQVpZb2pBcDJHOU8yaHBwc0x1aTZTRExwcmhkemU3YUVic2tDdnlFT1R3Zk9Rb0VQdEh1dVF1MktSK1c1VFlGVXJlT1pITk5xQWk4NGpGYVZwa2NVZlhkTmoxanhhNzNuRkFBaHhZLS11bjBDVzVZZnB5M28xZzZ0Vy9QMmRRPT0%3D--5c892e86f4f2f5988874b515a7b06fb5f879357a; path=/; HttpOnly
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: 83a34692-7c41-4ad8-8a11-8cc816b32b56
X-Runtime: 0.024589
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title>Search - Redmine</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Redmine" />
<meta name="keywords" content="issue,bug,tracker" />
<meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="6GkEwGHjcs+4GeixhE9iRgQvqPVLZNFX8uS9dO5AXryIBH3hRnbybAGWULCOf1q9NqS4hU8Spi3N3TzliOs0iA==" />
<link rel='shortcut icon' href='/favicon.ico?1586192449' />
<link rel="stylesheet" media="all" href="/stylesheets/jquery/jquery-ui-1.11.0.css?1586192448" />
<link rel="stylesheet" media="all" href="/stylesheets/tribute-3.7.3.css?1586192449" />
<link rel="stylesheet" media="all" href="/stylesheets/application.css?1586192449" />
<link rel="stylesheet" media="all" href="/stylesheets/responsive.css?1586192449" />

<script src="/javascripts/jquery-2.2.4-ui-1.11.0-ujs-5.2.3.js?1586192448"></script>
<script src="/javascripts/tribute-3.7.3.min.js?1586192448"></script>
<script src="/javascripts/application.js?1586192449"></script>
<script src="/javascripts/responsive.js?1586192449"></script>
<script>
//<![CDATA[
$(window).on('load', function(){ warnLeavingUnsaved('The current page contains unsaved text that will be lost if you leave this page.'); });
//]]>
</script>


<!-- page specific tags -->
</head>
<body class="has-main-menu controller-search action-index avatars-off">

<div id="wrapper">

<div class="flyout-menu js-flyout-menu">


        <div class="flyout-menu__search">
            <form action="/search" accept-charset="UTF-8" name="form-03d43ab0" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
            
            <label class="search-magnifier search-magnifier--flyout" for="flyout-search">&#9906;</label>
            <input type="text" name="q" id="flyout-search" value="" class="small js-search-input" placeholder="Search" />
</form>        </div>


        <h3>Project</h3>
        <span class="js-project-menu"></span>

    <h3>General</h3>
    <span class="js-general-menu"></span>

    <span class="js-sidebar flyout-menu__sidebar"></span>

    <h3>Profile</h3>
    <span class="js-profile-menu"></span>

</div>

<div id="wrapper2">
<div id="wrapper3">
<div id="top-menu">
    <div id="account">
        <ul><li><a class="login" href="/login">Sign in</a></li><li><a class="register" href="/account/register">Register</a></li></ul>    </div>
    
    <ul><li><a class="home" href="/">Home</a></li><li><a class="projects" href="/projects">Projects</a></li><li><a class="help" href="https://www.redmine.org/guide">Help</a></li></ul></div>

<div id="header">

    <a href="#" class="mobile-toggle-button js-flyout-menu-toggle-button"></a>

    <div id="quick-search">
        <form action="/search" accept-charset="UTF-8" name="form-03e8596e" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
        <input type="hidden" name="scope" />
        
        <label for='q'>
          <a accesskey="4" href="/search">Search</a>:
        </label>
        <input type="text" name="q" id="q" value="" size="20" class="small" accesskey="f" data-auto-complete="true" data-issues-url="/issues/auto_complete?q=" />
</form>        <div id="project-jump" class="drdn"><span class="drdn-trigger">Jump to a project...</span><div class="drdn-content"><div class="quick-search"><input type="text" name="q" id="projects-quick-search" value="" class="autocomplete" data-automcomplete-url="/projects/autocomplete.js?jump=search" autocomplete="off" /></div><div class="drdn-items projects selection"></div><div class="drdn-items all-projects selection"><a class="selected" href="/projects?jump=search">All Projects</a></div></div></div>
    </div>

    <h1>Redmine</h1>

    <div id="main-menu" class="tabs">
        <ul><li><a class="projects" href="/projects">Projects</a></li><li><a class="activity" href="/activity">Activity</a></li></ul>
        <div class="tabs-buttons" style="display:none;">
            <button class="tab-left" onclick="moveTabLeft(this); return false;"></button>
            <button class="tab-righ.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36' 'http://192.168.73.60:8080/search'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)