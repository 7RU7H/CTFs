### Allowed Options Method (options-method) found on http://10.129.227.191
---
**Details**: **options-method**  matched at http://10.129.227.191

**Protocol**: HTTP

**Full URL**: http://10.129.227.191

**Timestamp**: Tue Jan 3 23:19:00 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Allowed Options Method |
| Authors | pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
OPTIONS / HTTP/1.1
Host: 10.129.227.191
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2762.73 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 405 Method Not Allowed
Connection: close
Content-Length: 1293
Allow: GET, HEAD, OPTIONS, TRACE
Content-Type: text/html
Date: Tue, 03 Jan 2023 23:18:59 GMT
Server: Microsoft-IIS/8.5
X-Powered-By: ASP.NET

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>405 - HTTP verb used to access this page is not allowed.</title>
<style type="text/css">
<!--
body{margin:0;font-size:.7em;font-family:Verdana, Arial, Helvetica, sans-serif;background:#EEEEEE;}
fieldset{padding:0 15px 10px 15px;} 
h1{font-size:2.4em;margin:0;color:#FFF;}
h2{font-size:1.7em;margin:0;color:#CC0000;} 
h3{font-size:1.2em;margin:10px 0 0 0;color:#000000;} 
#header{width:96%;margin:0 0 0 0;padding:6px 2% 6px 2%;font-family:"trebuchet MS", Verdana, sans-serif;color:#FFF;
background-color:#555555;}
#content{margin:0 0 0 2%;position:relative;}
.content-container{background:#FFF;width:96%;margin-top:8px;padding:10px;position:relative;}
-->
</style>
</head>
<body>
<div id="header"><h1>Server Error</h1></div>
<div id="content">
 <div class="content-container"><fieldset>
  <h2>405 - HTTP verb used to access this page is not allowed.</h2>
  <h3>The page you are looking for cannot be displayed because an invalid method (HTTP verb) was used to attempt access.</h3>
 </fieldset></div>
</div>
</body>
</html>

```

**Extra Information**

**Extracted results**:

- GET, HEAD, OPTIONS, TRACE



**CURL Command**
```
curl -X 'OPTIONS' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2762.73 Safari/537.36' 'http://10.129.227.191'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)