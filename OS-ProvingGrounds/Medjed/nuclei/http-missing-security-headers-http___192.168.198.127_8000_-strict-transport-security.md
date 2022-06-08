### HTTP Missing Security Headers (http-missing-security-headers:strict-transport-security) found on http://192.168.198.127:8000/
---
**Details**: **http-missing-security-headers:strict-transport-security**  matched at http://192.168.198.127:8000/

**Protocol**: HTTP

**Full URL**: http://192.168.198.127:8000/

**Timestamp**: Wed Jun 8 21:31:33 +0100 BST 2022

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
Host: 192.168.198.127:8000
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Date: Wed, 08 Jun 2022 20:31:33 GMT
Last-Modified: Tue, 19 Feb 2013 19:58:47 GMT
Server: BarracudaServer.com (Windows)



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Home</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="icon" href="/rtl/favicon.ico" type="image/x-icon" />
<link href="/theme/bd.css" rel="stylesheet" type="text/css" />
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://192.168.198.127/metaweblog/rsd.lsp"/><script src="/rtl/jquery.js" type="text/javascript"></script><script src="/zzCMS.js" type="text/javascript"></script><script type="text/javascript">$(function(){if($(".PhotoAlbum ul li").length>0)$.getScript("/album/lightbox.js");});</script><script src="/theme/bd.js" type="text/javascript"></script>
</head>
<body>   
<div id="head">
</div>
<table id="nav">
<tr><td rowspan="2">
   <img src='/rtl/images/logo.png' alt='logo'/>
</td>
<td>
<ul>
<li><a class='selected' href='/'>Home Page</a></li>
<li><a href='/rtl/protected/wfslinks.lsp'>Web-File-Server</a></li>
<li><a href='/rtl/protected/'>Menu</a></li>
<li><a href='/private/manage/'>CMS Admin</a></li>
<li><a href='/rtl/about.lsp'>About</a></li>
</ul>
</td></tr>
<tr><td></td></tr>
</table>
<div id="wrapper">
  <div id="container">
    <div id="content">
      <p></p>
<style>
#conf {
padding:2em;display:none;color:red;font-size:120%;background:yellow;
}
#conf a {color:red;}
#conf p {font-size:120%;font-weight:bold;}
#info,#nc {background: #2F94FA;border-radius: 10px;margin:10px}
#info {float:right;width:300px;}
#nc {width:95%;float:none;margin:auto;background:yellow;padding: 1px 10px 5px 10px}
#nc p {font-size:120%;font-weight:bold;}
#info p {margin-left:10px}
#info p,#info h2,#info li,#info a,#info a:hover{color:white;}
</style>
<fieldset id="conf"><legend>Required Actions</legend>
<div>
<p><b>The Configuration Wizard must be completed before you can use the server.</b></p>
<ul>
  <li>BarracudaDrive's Configuration Wizard: <a href="Config-Wizard/">/Config-Wizard/</a></li>
</ul>
</div>
</fieldset>

<h1 align="center">Website Builder</h1>
<br/>
<div id="info">

<h2>Edit this page as follows:</h2>
<ol>
<li>Login to the <a href="/private/manage/">CMS Admin Page</a></li>
<li>Navigate back to this page</li>
<li>Click your browser's refresh button</li>
<li>Click the edit page icon (<img src="images/file.gif"/>), which is visible after you press the refresh button</li>
<li>Edit the page using the integrated page editor</li>
</ol>
<p><b>Note:</b> you can also use an external editor. See our <a target="_blank" href="http://barracudadrive.com/blog/2012/04/How-To-Use-Blog-Editors">How To Use Blog Editors Tutorial</a> for more information.</p>


</div>

<script>
$(function() {
if($("#conf").length){
   $.getJSON("Config-Wizard/",{"json":"isconf"},function(data) {
      if(data.isconf == true) {
         if(data.js) eval(data.js);
         return;
      }
      $("#conf").show();
      setTimeout(function() {window.location="Config-Wizard/";}, 5000);
   });
}
});
</script>

<p>The BarracudaDrive website builder, which includes a Content Management System (CMS) and blog, makes it easy to create and update your own website. It’s perfect for anyone from first-timers to advanced web designers.  You don’t need to install any additional software on your computer, and you can use the website builder from any computer anywhere in the world that has internet access at any time.</p>

<p>This page and everything you see on this page was created by using the website builder. You can create a professional looking website in minutes. Advanced web designers can add their own effects by directly modifying the HTML.</p>

<p><b>Important website builder Links:</b></p>

<ul>
  <li>Website builder Manual: <a href="/private/manage/manual.html">/private/manage/manual.html</a></li>
  <li>Website builder Administration: <a href="/private/manage/">/private/manage/</a></li>
  <li><a target="_blank" href="http://youtu.be/s4JAWaA-Arc">CMS Video Tutorial</a></li>
</ul>

<p><b style="color:red;background:yellow">When you are finished reading this page, open the page editor and replace all content of this page with your own content.</b></p>

<h2><br/>Quick Start to Using BarracudaDrive:</h2>

<table><tr>
<td>
<ol style="font-size:120%">
<li><p>View our online <a target="_blank" href="http://barracudadrive.com/videos.lsp">BarracudaDrive training videos</a>.</p></li>
<li><p>Familiarize yourself with the <a target="_blank" href="/private/manage/">Content Management System</a> (CMS) and the <a target="_blank" href="/forum/">bulletin board (forum)</a>. You can <a target="_blank" href="/rtl/protected/admin/appmgr/">remove</a> these two add-ons if you do not need them.</p></li>

<li><p>Find out how to use the <a target="_blank" href="rtl/protected/wfslinks.lsp">Web-File-Manager and WebDAV server</a> by clicking the "Web-File-Server" link at the top menu.</p></li>
.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://192.168.198.127:8000/'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)