### Detect Deprecated XSS Protection Header (xss-deprecated-header-detect) found on http://192.168.73.60:8080
---
**Details**: **xss-deprecated-header-detect**  matched at http://192.168.73.60:8080

**Protocol**: HTTP

**Full URL**: http://192.168.73.60:8080

**Timestamp**: Fri Sep 9 20:11:13 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Detect Deprecated XSS Protection Header |
| Authors | joshlarsen |
| Tags | xss, misconfig, generic |
| Severity | info |
| Description | Setting the XSS-Protection header is deprecated by most browsers. Setting the header to anything other than `0` can actually introduce an XSS vulnerability. |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.73.60:8080
User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK 
Connection: close
Content-Length: 4600
Cache-Control: max-age=0, private, must-revalidate
Content-Type: text/html; charset=utf-8
Date: Fri, 09 Sep 2022 19:11:33 GMT
Etag: W/"28abe37122cb0b9d68cd2ec241a143d5"
Referrer-Policy: strict-origin-when-cross-origin
Server: WEBrick/1.4.2 (Ruby/2.6.6/2020-03-31)
Set-Cookie: _redmine_session=ZVdWd29GSzh4ZFBiMk9nSG5xTWNFNjFEWmVDNnU0ZUNaaFZaT1FYU3JYa1h3QVFwdWN3VDJGZTg2cTIxZ1lJcHRNNHhCRGVWM2had1AxVXVCQm5sUTN4bGN4NkYwc25GYnc5R2J0VEJHSDM2L01DeWhOdHZRdDVwS3ovV1FyQWdhQ243M2kzbm9HZGhxdDhXL1hvSHBJV1lKbFdWaldHVmJjaERkaStEeHpLOFFGVHdmc1Fjb292VlJ1eStkTUF3LS11MjBxQ0h6WFJNc0tZZG8rWjg0aFRnPT0%3D--4f2863dd0193dad4641aa1a25f8ca205efcce17d; path=/; HttpOnly
X-Content-Type-Options: nosniff
X-Download-Options: noopen
X-Frame-Options: SAMEORIGIN
X-Permitted-Cross-Domain-Policies: none
X-Request-Id: 6690c66e-5947-4270-a6de-f88ae0d42915
X-Runtime: 4.067722
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
<meta name="csrf-token" content="fMjTODLKYRXYQ8sZa7pGj3m7kJKyHniiLpLvzGByrCpovf/yIkIj3cVugLFkR6iBG4G6VwwuzHs7WqTGrD89Mw==" />
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
<link rel="alternate" type="application/atom+xml" title="Redmine: Latest news" href="http://192.168.73.60:8080/news.atom" />
<link rel="alternate" type="application/atom+xml" title="Redmine: Activity" href="http://192.168.73.60:8080/activity.atom" />
</head>
<body class="controller-welcome action-index avatars-off">

<div id="wrapper">

<div class="flyout-menu js-flyout-menu">


        <div class="flyout-menu__search">
            <form action="/search" accept-charset="UTF-8" name="form-c54a46f3" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
            
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
        <form action="/search" accept-charset="UTF-8" name="form-eee315ef" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
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
<div class="splitcontentleft">
  <div class="wiki">
.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 1; mode=block


References: 
- https://developer.mozilla.org/en-us/docs/web/http/headers/x-xss-protection
- https://owasp.org/www-project-secure-headers/#x-xss-protection

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' 'http://192.168.73.60:8080'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)