### HTTP Missing Security Headers (http-missing-security-headers:permissions-policy) found on moodle.schooled.htb

----
**Details**: **http-missing-security-headers:permissions-policy** matched at moodle.schooled.htb

**Protocol**: HTTP

**Full URL**: http://moodle.schooled.htb/moodle/

**Timestamp**: Sun Apr 28 17:02:17 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET /moodle/ HTTP/1.1
Host: moodle.schooled.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.50
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Accept-Ranges: none
Cache-Control: no-store, no-cache, must-revalidate
Cache-Control: post-check=0, pre-check=0, no-transform
Content-Language: en
Content-Script-Type: text/javascript
Content-Style-Type: text/css
Content-Type: text/html; charset=utf-8
Date: Sun, 28 Apr 2024 16:02:13 GMT
Expires: Mon, 20 Aug 1969 09:23:00 GMT
Last-Modified: Sun, 28 Apr 2024 16:02:15 GMT
Pragma: no-cache
Server: Apache/2.4.46 (FreeBSD) PHP/7.4.15
Set-Cookie: MoodleSession=mmau2kov08s91n2iplgp4lahk8; path=/moodle/
X-Frame-Options: sameorigin
X-Powered-By: PHP/7.4.15
X-Ua-Compatible: IE=edge

<!DOCTYPE html>

<html  dir="ltr" lang="en" xml:lang="en">
<head>
    <title>moodle.schooled.htb</title>
    <link rel="shortcut icon" href="http://moodle.schooled.htb/moodle/theme/image.php/boost/theme/1608386799/favicon" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="moodle, moodle.schooled.htb" />
<link rel="stylesheet" type="text/css" href="http://moodle.schooled.htb/moodle/theme/yui_combo.php?rollup/3.17.2/yui-moodlesimple-min.css" /><script id="firstthemesheet" type="text/css">/** Required in order to fix style inclusion problems in IE with YUI **/</script><link rel="stylesheet" type="text/css" href="http://moodle.schooled.htb/moodle/theme/styles.php/boost/1608386799_1/all" />
<script>
//<![CDATA[
var M = {}; M.yui = {};
M.pageloadstarttime = new Date();
M.cfg = {"wwwroot":"http:\/\/moodle.schooled.htb\/moodle","sesskey":"Btud0EBR3f","sessiontimeout":"28800","themerev":"1608386799","slasharguments":1,"theme":"boost","iconsystemmodule":"core\/icon_system_fontawesome","jsrev":"1608386799","admin":"admin","svgicons":true,"usertimezone":"Europe\/London","contextid":2,"langrev":1608386799,"templaterev":"1608386799"};var yui1ConfigFn = function(me) {if(/-skin|reset|fonts|grids|base/.test(me.name)){me.type='css';me.path=me.path.replace(/\.js/,'.css');me.path=me.path.replace(/\/yui2-skin/,'/assets/skins/sam/yui2-skin')}};
var yui2ConfigFn = function(me) {var parts=me.name.replace(/^moodle-/,'').split('-'),component=parts.shift(),module=parts[0],min='-min';if(/-(skin|core)$/.test(me.name)){parts.pop();me.type='css';min=''}
if(module){var filename=parts.join('-');me.path=component+'/'+module+'/'+filename+min+'.'+me.type}else{me.path=component+'/'+component+'.'+me.type}};
YUI_config = {"debug":false,"base":"http:\/\/moodle.schooled.htb\/moodle\/lib\/yuilib\/3.17.2\/","comboBase":"http:\/\/moodle.schooled.htb\/moodle\/theme\/yui_combo.php?","combine":true,"filter":null,"insertBefore":"firstthemesheet","groups":{"yui2":{"base":"http:\/\/moodle.schooled.htb\/moodle\/lib\/yuilib\/2in3\/2.9.0\/build\/","comboBase":"http:\/\/moodle.schooled.htb\/moodle\/theme\/yui_combo.php?","combine":true,"ext":false,"root":"2in3\/2.9.0\/build\/","patterns":{"yui2-":{"group":"yui2","configFn":yui1ConfigFn}}},"moodle":{"name":"moodle","base":"http:\/\/moodle.schooled.htb\/moodle\/theme\/yui_combo.php?m\/1608386799\/","combine":true,"comboBase":"http:\/\/moodle.schooled.htb\/moodle\/theme\/yui_combo.php?","ext":false,"root":"m\/1608386799\/","patterns":{"moodle-":{"group":"moodle","configFn":yui2ConfigFn}},"filter":null,"modules":{"moodle-core-actionmenu":{"requires":["base","event","node-event-simulate"]},"moodle-core-languninstallconfirm":{"requires":["base","node","moodle-core-notification-confirm","moodle-core-notification-alert"]},"moodle-core-chooserdialogue":{"requires":["base","panel","moodle-core-notification"]},"moodle-core-maintenancemodetimer":{"requires":["base","node"]},"moodle-core-tooltip":{"requires":["base","node","io-base","moodle-core-notification-dialogue","json-parse","widget-position","widget-position-align","event-outside","cache-base"]},"moodle-core-lockscroll":{"requires":["plugin","base-build"]},"moodle-core-popuphelp":{"requires":["moodle-core-tooltip"]},"moodle-core-notification":{"requires":["moodle-core-notification-dialogue","moodle-core-notification-alert","moodle-core-notification-confirm","moodle-core-notification-exception","moodle-core-notification-ajaxexception"]},"moodle-core-notification-dialogue":{"requires":["base","node","panel","escape","event-key","dd-plugin","moodle-core-widget-focusafterclose","moodle-core-lockscroll"]},"moodle-core-notification-alert":{"requires":["moodle-core-notification-dialogue"]},"moodle-core-notification-confirm":{"requires":["moodle-core-notification-dialogue"]},"moodle-core-notification-exception":{"requires":["moodle-core-notification-dialogue"]},"moodle-core-notification-ajaxexception":{"requires":["moodle-core-notification-dialogue"]},"moodle-core-dragdrop":{"requires":["base","node","io","dom","dd","event-key","event-focus","moodle-core-notification"]},"moodle-core-formchangechecker":{"requires":["base","event-focus","moodle-core-event"]},"moodle-core-event":{"requires":["event-custom"]},"moodle-core-blocks":{"requires":["base","node","io","dom","dd","dd-scroll","moodle-core-dragdrop","moodle-core-notification"]},"moodle-core-handlebars":{"condition":{"trigger":"handleb.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.50' 'http://moodle.schooled.htb/moodle/'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)