### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-origin) found on http://10.129.133.189
---
**Details**: **http-missing-security-headers:access-control-allow-origin**  matched at http://10.129.133.189

**Protocol**: HTTP

**Full URL**: http://10.129.133.189/index.htm

**Timestamp**: Wed Jun 8 20:25:54 +0100 BST 2022

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
Host: 10.129.133.189
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 33628
Cache-Control: no-cache
Content-Type: text/html; charset=UTF-8
Date: Wed, 08 Jun 2022 19:25:54 GMT
Expires: 0
Server: PRTG/18.1.37.13946
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Xss-Protection: 1; mode=block

<!doctype html>
<html class="">
<!--
 _____  _______ _______ _______ _______        _______  ______
|_____] |_____| |______ |______ |______ |      |______ |_____/
|       |     | |______ ______| ______| |_____ |______ |    \_

We are hiring software developers! https://www.paessler.com/jobs

-->
<head>
  <link rel="manifest" href="/public/manifest.json.htm">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name='viewport' content='width=device-width, height=device-height, initial-scale=0.8'>
  <link id="prtgfavicon" rel="shortcut icon" type="image/ico" href="/favicon.ico" />
  <title>Welcome | PRTG Network Monitor (NETMON)</title>
  <link rel="stylesheet" type="text/css" href="/css/prtgmini.css?prtgversion=18.1.37.13946__" media="print,screen,projection" />

  
  

   
   
  
  <script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','__ga');__ga('create', 'UA-154425-18', {'appId':'-10','appName':'PRTG Network Monitor (NETMON)','appVersion':'18.1.37.13946'});(function(){  var url =  document.createElement("a")    , urlStripOff = ["mapid", "tmpid", "subid", "topnumber", "username", "password", "email_address"];  window.__gaStripOrigin = function(urlString){    var param = [];    url.href = (""+urlString);    param = url.search.replace("?","").split("&");    param = param.filter(function(value){     return (value !== "" && urlStripOff.indexOf(value.split("=")[0]) === -1)    }); return url.pathname + (param.length === 0 ? "" : "?" +  param.join("&"));};})();__ga("set", "location", "");__ga("set", "hostname", "trial.paessler.com");__ga("set","dimension4","0");__ga("set","dimension3","18.1.37.13946".split(".").slice(0,3).join("."));__ga("set","dimension2","1322");__ga("set","dimension1","webgui");</script>
</head>
<body id="mainbody" class="systemmenu loginscreen language_en">
<!--
//        You can use this file to modify the appearance of the PRTG web interface
//        as described in https://kb.paessler.com/en/topic/33
//        
//        Please note that you are using an unsupported and deprecated feature. 
//        Your changes will be broken or removed with future PRTG updates.
//        
//        If you modify this file, PLEASE LET US KNOW what you're changing and why!
//        Just drop an email to support@paessler.com and help us understand your 
//        needs. Thank you!       
-->



<div id="login-container">

  <div class="login-form" style="">
    <div class="login-cell box">
            <div class="cell-left cell-login">
            <h1>PRTG Network Monitor (NETMON)</h1>
            <noscript>
              <div style="margin-bottom:10px">
                <div class="nagscreen-box" >
                  <p class="nagscreen-head">Javascript is not available!</p>
                  <p class="nagscreen-cell">
                    You cannot use the AJAX Web Interface without Javascript. <br>Javascript seems to be disabled or not supported by your browser.
                  </p>
                </div>
              </div>
            </noscript>
            <div id="notofficiallysupported" style="display:none" class="nagscreen-box">
              <p class="nagscreen-head">
                Your browser is not officially supported!
              </p>
              <p class="nagscreen-cell">
                Some functionalities may not work correctly or not at all. Consider upgrading to a modern browser version. We recommend <a href='https://www.google.com/chrome/'>Chrome</a> or <a href='http://www.mozilla.org/firefox/'>Firefox</a>.
              </p>
            </div>
            <div id="unsupportedbrowser" style="display:none;">
              <div class="nagscreen-box" >
                <p class="nagscreen-head">
                 Sorry, your browser is not supported!
                </p>
                <p class="nagscreen-cell">
                  <b>You might not be able to access all PRTG features with this browser!</b><br>
                  Please upgrade to a modern browser version. We recommend <a href='https://www.google.com/chrome/'>Chrome</a> or <a href='http://www.mozilla.org/firefox/'>Firefox</a>.
                </p>
              </div>
            </div>
            <div id="dontuselocalhost" style="display:none;">
              <div class="nagscreen-box" >
                <p class="nagscreen-head">
                  Please do not use http://.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.133.189' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://10.129.133.189/index.htm'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)