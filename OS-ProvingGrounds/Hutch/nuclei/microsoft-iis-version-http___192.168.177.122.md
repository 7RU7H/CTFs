### Microsoft IIS version detect (microsoft-iis-version) found on http://192.168.177.122
---
**Details**: **microsoft-iis-version**  matched at http://192.168.177.122

**Protocol**: HTTP

**Full URL**: http://192.168.177.122

**Timestamp**: Sat Nov 19 15:11:44 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Microsoft IIS version detect |
| Authors | wlayzz |
| Tags | tech, microsoft, iis |
| Severity | info |
| Description | Some Microsoft IIS servers have the version on the response header. Useful when you need to find specific CVEs on your targets. |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.177.122
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 703
Accept-Ranges: bytes
Content-Type: text/html
Date: Sat, 19 Nov 2022 15:11:43 GMT
Etag: "965c9516cb2d61:0"
Last-Modified: Wed, 04 Nov 2020 05:35:35 GMT
Server: Microsoft-IIS/10.0
X-Powered-By: ASP.NET

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>IIS Windows Server</title>
<style type="text/css">
<!--
body {
	color:#000000;
	background-color:#0072C6;
	margin:0;
}

#container {
	margin-left:auto;
	margin-right:auto;
	text-align:center;
	}

a img {
	border:none;
}

-->
</style>
</head>
<body>
<div id="container">
<a href="http://go.microsoft.com/fwlink/?linkid=66138&amp;clcid=0x409"><img src="iisstart.png" alt="IIS" width="960" height="600" /></a>
</div>
</body>
</html>
```

**Extra Information**

**Extracted results**:

- Microsoft-IIS/10.0



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.177.122'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)