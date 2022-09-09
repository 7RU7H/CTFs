### Host Header Injection (host-header-injection) found on http://192.168.73.60:8080
---
**Details**: **host-header-injection**  matched at http://192.168.73.60:8080

**Protocol**: HTTP

**Full URL**: http://192.168.73.60:8080

**Timestamp**: Fri Sep 9 20:11:21 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Host Header Injection |
| Authors | princechaddha |
| Tags | hostheader-injection, generic |
| Severity | info |
| Description | HTTP header injection is a general class of web application security vulnerability which occurs when Hypertext Transfer Protocol headers are dynamically generated based on user input. |

**Request**
```http
GET / HTTP/1.1
Host: 2EXnCTitSO1xRcI8LGKuOulcoNX.tld
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK 
Connection: close
Content-Length: 4626
Cache-Control: max-age=0, private, must-revalidate
Content-Type: text/html; charset=utf-8
Date: Fri, 09 Sep 2022 19:11:42 GMT
Etag: W/"d1ec27c9a048afaeabc7d8689a466b6d"
Referrer-Policy: strict-origin-when-cross-origin
Server: WEBrick/1.4.2 (Ruby/2.6.6/2020-03-31)
Set-Cookie: _redmine_session=V2x5YnlEOERqVFpVdUxvSG5EVDhaNW05SXF2dnVOSUMrTmg4ckozNU5zVkNOQnV6ZTVwT0k4cmVSdDhsRVIvdWlCVnB4ZG81S0kycVlaL01CMUZSNlFMUzBkdWJzeTJPaU1lWWJOSTAvK1ZCZDNEUWRZZkFIVmRRZjN3T09qOFVSL0doblpySzBkM25VME4wVzU1bUxySGx4WWlnNXJlMjdjRk9zRjBZQWdLbmdpN0hPd3RNaWNDVXRtNnpVSUdTLS1qZEhMT2U4KzZOaHU4QTlWQU82UFRRPT0%3D--61f64013b28ca8ea5ff07ed8b937d5eb44bed208; path=/; HttpOnly
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: 76444657-f60c-4197-8b98-862848b778ee
X-Runtime: 0.010481
X-Xss-Protection: 1; mode=block

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title>Redmine</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Redmine" />
<meta name="keywords" content="issue,bug,tracker" />
<meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="yagBoD4IrEvaph4dVAiS+GY9igxVX1fEH1NPixtCBRDO/fE/bX0WMDvovgwcBYTxWowm/WWutdDmxDhBnTINDg==" />
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
<link rel="alternate" type="application/atom+xml" title="Redmine: Latest news" href="http://2EXnCTitSO1xRcI8LGKuOulcoNX.tld/news.atom" />
<link rel="alternate" type="application/atom+xml" title="Redmine: Activity" href="http://2EXnCTitSO1xRcI8LGKuOulcoNX.tld/activity.atom" />
</head>
<body class="controller-welcome action-index avatars-off">

<div id="wrapper">

<div class="flyout-menu js-flyout-menu">


        <div class="flyout-menu__search">
            <form action="/search" accept-charset="UTF-8" name="form-a3eb0082" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
            
            <label class="search-magnifier search-magnifier--flyout" for="flyout-search">&#9906;</label>
            <input type="text" name="q" id="flyout-search" class="small js-search-input" placeholder="Search" />
</form>        </div>



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
        <form action="/search" accept-charset="UTF-8" name="form-72a7fdd3" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
        <input type="hidden" name="scope" />
        
        <label for='q'>
          <a accesskey="4" href="/search">Search</a>:
        </label>
        <input type="text" name="q" id="q" size="20" class="small" accesskey="f" data-auto-complete="true" data-issues-url="/issues/auto_complete?q=" />
</form>        <div id="project-jump" class="drdn"><span class="drdn-trigger">Jump to a project...</span><div class="drdn-content"><div class="quick-search"><input type="text" name="q" id="projects-quick-search" value="" class="autocomplete" data-automcomplete-url="/projects/autocomplete.js?jump=welcome" autocomplete="off" /></div><div class="drdn-items projects selection"></div><div class="drdn-items all-projects selection"><a href="/projects?jump=welcome">All Projects</a></div></div></div>
    </div>

    <h1>Redmine</h1>

</div>

<div id="main" class="nosidebar">
    <div id="sidebar">
        
        
    </div>

    <div id="content">
        
        <h2>Home</h2>

<div class="splitcontent">
<div class="splitcontentle.... Truncated ....
```

References: 
- https://portswigger.net/web-security/host-header
- https://portswigger.net/web-security/host-header/exploiting
- https://www.acunetix.com/blog/articles/automated-detection-of-host-header-attacks/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Host: 2EXnCTitSO1xRcI8LGKuOulcoNX.tld' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://192.168.73.60:8080'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)