### Microsoft Exchange Admin Center Login Panel - Detect (microsoft-exchange-panel) found on https://10.10.252.86
---
**Details**: **microsoft-exchange-panel**  matched at https://10.10.252.86

**Protocol**: HTTP

**Full URL**: https://10.10.252.86/owa/auth/logon.aspx?replaceCurrent=1&url=https://10.10.252.86/ecp

**Timestamp**: Thu Apr 6 22:59:57 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Microsoft Exchange Admin Center Login Panel - Detect |
| Authors | r3dg33k |
| Tags | microsoft, panel, exchange |
| Severity | info |
| Description | Microsoft Exchange Admin Center login panel was detected. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
GET /owa/auth/logon.aspx?replaceCurrent=1&url=https://10.10.252.86/ecp HTTP/1.1
Host: 10.10.252.86
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 48353
Cache-Control: no-cache, no-store
Content-Type: text/html; charset=utf-8
Date: Thu, 06 Apr 2023 21:59:56 GMT
Expires: -1
Pragma: no-cache
Request-Id: cf863360-c222-4f1a-86f8-3618919bad43
Server: Microsoft-IIS/10.0
X-Aspnet-Version: 4.0.30319
X-Frame-Options: SAMEORIGIN
X-Powered-By: ASP.NET

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- Copyright (c) 2011 Microsoft Corporation.  All rights reserved. -->
<!-- OwaPage = ASP.auth_logon_aspx -->

<!-- {57A118C6-2DA9-419d-BE9A-F92B0F9A418B} -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<link rel="shortcut icon" href="/owa/auth/15.2.858/themes/resources/favicon.ico" type="image/x-icon">
<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8">
<meta name="Robots" content="NOINDEX, NOFOLLOW">
<title>Exchange Admin Center</title>
<style>
@font-face {
    font-family: "wf_segoe-ui_normal";
    src: url("/owa/auth/15.2.858/themes/resources/segoeui-regular.eot?#iefix") format("embedded-opentype"),
            url("/owa/auth/15.2.858/themes/resources/segoeui-regular.ttf") format("truetype");
}

@font-face {
    font-family: "wf_segoe-ui_semilight";
    src: url("/owa/auth/15.2.858/themes/resources/segoeui-semilight.eot?#iefix") format("embedded-opentype"),
        url("/owa/auth/15.2.858/themes/resources/segoeui-semilight.ttf") format("truetype");
}

@font-face {
    font-family: "wf_segoe-ui_semibold";
    src: url("/owa/auth/15.2.858/themes/resources/segoeui-semibold.eot?#iefix") format("embedded-opentype"),
        url("/owa/auth/15.2.858/themes/resources/segoeui-semibold.ttf") format("truetype");
}
</style>
<style>/*Copyright (c) 2003-2006 Microsoft Corporation.  All rights reserved.*/

body.rtl 
{
	text-align:right;
	direction:rtl;
}

body, .mouse, .twide, .tnarrow, form
{
    height: 100%;
    width: 100%;
    margin: 0px;
}

.mouse, .twide 
{
    min-width: 650px; /* min iPad1 dimension */
    min-height: 650px;
    position: absolute;
    top:0px;
    bottom:0px;
    left:0px;
    right:0px;
}

.sidebar 
{
    background-color:#0072C6;
}

.mouse .sidebar, .twide .sidebar
{
    position:absolute;
    top: 0px;
    bottom: 0px;
    left: 0px;
    display: inline-block;
    width: 332px;
}

.tnarrow .sidebar
{
    display: none;
}

.mouse .owaLogoContainer, .twide .owaLogoContainer
{
    margin:213px auto auto 109px;
    text-align:left     /* Logo aligns left for both ltr & rtl */
}

.tnarrow .owaLogo 
{
    display: none;
}

.mouse .owaLogoSmall, .twide .owaLogoSmall
{
    display: none;
}

.logonDiv 
{ 
	text-align:left;
}

.rtl .logonDiv 
{ 
	text-align:right;
}

.mouse .logonContainer, .twide .logonContainer
{
    padding-top: 174px;
    padding-left: 464px;
    padding-right:142px;
    position:absolute;
    top:0px;
    bottom: 0px;
    left: 0px;
    right: 0px;
    text-align: center;
}

.mouse .logonDiv, .twide .logonDiv
{
    position: relative;
    vertical-align:top;
    display: inline-block;
    width: 423px;
}

.tnarrow .logonDiv
{
    margin:25px auto auto -130px;
    position: absolute;
    left: 50%;
    width: 260px;
    padding-bottom: 20px;
}

.twide .signInImageHeader, .tnarrow .signInImageHeader
{
    display: none;
}

.mouse .signInImageHeader
{
    margin-bottom:22px;
}

.twide .mouseHeader
{
    display: none;
}

.mouse .twideHeader
{
    display: none;
}

input::-webkit-input-placeholder
{
    font-size:16px;
    color: #98A3A6;
}

input:-moz-placeholder 
{
    font-size:16px;
    color: #98A3A6;
}

.tnarrow .signInInputLabel, .twide .signInInputLabel
{
    display: none;
}

.mouse .signInInputLabel
{
    margin-bottom: 2px;
}

.mouse .showPasswordCheck
{
    display: none;
}

.signInInputText
{
    border:1px solid #98A3A6;
    color: #333333;
    border-radius: 0;
    -moz-border-radius: 0;
    -webkit-border-radius: 0;
    box-shadow: none;
    -moz-box-shadow: none;
    -webkit-box-shadow: none;
    -webkit-appearance:none;
    background-color: #FDFDFD;
	width:250px;
	margin-bottom:10px;
	box-sizing: content-box;
    -moz-box-sizing: content-box;
    -webkit-box-sizing: content-box;
}

.mouse .signInInputText 
{
    height: 22px;
    font-size: 12px;
    padding: 3px 5px;
    color: #333333;
	font-family:'wf_segoe-ui_normal', 'Segoe UI', 'Segoe WP', Tahoma, Arial, sans-serif;
	margin-bottom: 20px;
}

.twide .signInInputText, .tnarrow .signInInputText
{
    border-color: #666666;
    height: 22px;
    font-size: 16px;
    color: #000000;
    padding: 7px 7px;
	font-family:'wf_segoe-ui_semibold', 'Segoe UI Semibold', 'Segoe WP Semibold', 'Segoe UI', 'Segoe WP', Tahoma, Arial, sans-serif;
	margin-bottom:20px;
	width: 264px;
}

.divMain
{
	width: 444px;
}

.l
{
	text-align:left;
}
.rtl .l
{
	text-align:right;
}
.r
{
	text-align:right;.... Truncated ....
```

References: 
- https://docs.microsoft.com/en-us/answers/questions/58814/block-microsoft-exchange-server-2016-exchange-admi.html

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'https://10.10.252.86/owa/auth/logon.aspx?replaceCurrent=1&url=https://10.10.252.86/ecp'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)