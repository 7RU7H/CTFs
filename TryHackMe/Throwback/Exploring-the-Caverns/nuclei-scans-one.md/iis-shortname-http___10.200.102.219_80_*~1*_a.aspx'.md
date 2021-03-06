### iis-shortname (iis-shortname) found on http://10.200.102.219:80
---
**Details**: **iis-shortname**  matched at http://10.200.102.219:80

**Protocol**: HTTP

**Full URL**: http://10.200.102.219:80/*~1*/a.aspx'

**Timestamp**: Thu Mar 31 21:16:13 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | iis-shortname |
| Authors | nodauf |
| Tags | fuzz |
| Severity | info |
| Description | When IIS uses an old .Net Framework it's possible to enumeration folder with the symbol ~. |

**Request**

```http
OPTIONS /*~1*/a.aspx' HTTP/1.1
Host: 10.200.102.219
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36
Connection: close
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
Origin: http://10.200.102.219:80
Accept-Encoding: gzip


```

**Response**

```http
HTTP/1.1 404 Not Found
Connection: close
Content-Length: 1245
Content-Type: text/html
Date: Thu, 31 Mar 2022 20:17:43 GMT
Server: Microsoft-IIS/10.0

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>404 - File or directory not found.</title>
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
  <h2>404 - File or directory not found.</h2>
  <h3>The resource you are looking for might have been removed, had its name changed, or is temporarily unavailable.</h3>
 </fieldset></div>
</div>
</body>
</html>

```

References: 
- https://github.com/lijiejie/iis_shortname_scanner
- https://www.exploit-db.com/exploits/19525

**CURL Command**
```
curl -X 'OPTIONS' -d '' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8' -H 'Host: 10.200.102.219:80' -H 'Origin: http://10.200.102.219:80' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36' 'http://10.200.102.219:80/*~1*/a.aspx'\'''
```
---
Generated by [Nuclei 2.6.5](https://github.com/projectdiscovery/nuclei)