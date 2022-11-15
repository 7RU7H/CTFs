### Publicly Accessible Phpmyadmin Setup (phpmyadmin-setup) found on http://10.129.227.147
---
**Details**: **phpmyadmin-setup**  matched at http://10.129.227.147

**Protocol**: HTTP

**Full URL**: http://10.129.227.147/phpmyadmin/setup/index.php

**Timestamp**: Tue Nov 15 12:38:41 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Publicly Accessible Phpmyadmin Setup |
| Authors | sheikhrishad, thevillagehacker, kr1shna4garwal, arjunchandarana |
| Tags | phpmyadmin, misconfig |
| Severity | medium |
| shodan-query | http.html:"phpMyAdmin" |

**Request**
```http
GET /phpmyadmin/setup/index.php HTTP/1.1
Host: 10.129.227.147
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate,  pre-check=0, post-check=0, max-age=0
Content-Type: text/html; charset=utf-8
Date: Tue, 15 Nov 2022 12:38:41 GMT
Expires: Tue, 15 Nov 2022 12:38:41 +0000
Ironwaf: 2.0.3
Last-Modified: Tue, 15 Nov 2022 12:38:41 +0000
Pragma: no-cache
Server: Apache/2.4.25 (Debian)
Set-Cookie: phpMyAdmin=hmeg5i1a42mdpfat3kemkraeeestcpma; path=/phpmyadmin/setup/; HttpOnly
Set-Cookie: phpMyAdmin=hmeg5i1a42mdpfat3kemkraeeestcpma; path=/phpmyadmin/setup/; HttpOnly
Set-Cookie: pma_lang=en; expires=Thu, 15-Dec-2022 12:38:41 GMT; Max-Age=2592000; path=/phpmyadmin/setup/; HttpOnly
Vary: Accept-Encoding

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<title>phpMyAdmin setup</title>
<link href="../favicon.ico" rel="icon" type="image/x-icon" />
<link href="../favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/vendor/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../js/vendor/jquery/jquery-ui.min.js">
</script>
<script type="text/javascript" src="ajax.js"></script>
<script type="text/javascript" src="../js/config.js"></script>
<script type="text/javascript" src="scripts.js"></script>
<script type="text/javascript" src="../js/messages.php"></script>
</head>
<body>
<h1><span class="blue">php</span><span class="orange">MyAdmin</span>  setup</h1>
<div id="menu">
<ul><li><a href="index.php?lang=en" class="active">Overview</a></li><li><a href="index.php?page=form&amp;formset=Export&amp;lang=en" ">Export</a></li><li><a href="index.php?page=form&amp;formset=Features&amp;lang=en" ">Features</a></li><li><a href="index.php?page=form&amp;formset=Import&amp;lang=en" ">Import</a></li><li><a href="index.php?page=form&amp;formset=Main&amp;lang=en" ">Main panel</a></li><li><a href="index.php?page=form&amp;formset=Navi&amp;lang=en" ">Navigation panel</a></li><li><a href="index.php?page=form&amp;formset=Sql&amp;lang=en" ">SQL queries</a></li></ul></div>
<div id="page">
<form id="select_lang" method="post"><input type="hidden" name="lang" value="en" /><input type="hidden" name="token" value="|~F#_ztI`7{m5NRN" /><bdo lang="en" dir="ltr"><label for="lang">Language</label></bdo><br /><select id="lang" name="lang" class="autosubmit" lang="en" dir="ltr"><option value="ar">&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577; - Arabic</option>
<option value="hy">Հայերէն - Armenian</option>
<option value="az">Az&#601;rbaycanca - Azerbaijani</option>
<option value="bn">বাংলা - Bangla</option>
<option value="be">&#1041;&#1077;&#1083;&#1072;&#1088;&#1091;&#1089;&#1082;&#1072;&#1103; - Belarusian</option>
<option value="pt_BR">Portugu&ecirc;s - Brazilian Portuguese</option>
<option value="bg">&#1041;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080; - Bulgarian</option>
<option value="ca">Catal&agrave; - Catalan</option>
<option value="zh_CN">&#20013;&#25991; - Chinese simplified</option>
<option value="zh_TW">&#20013;&#25991; - Chinese traditional</option>
<option value="cs">Čeština - Czech</option>
<option value="da">Dansk - Danish</option>
<option value="nl">Nederlands - Dutch</option>
<option value="en" selected="selected">English</option>
<option value="en_GB">English (United Kingdom)</option>
<option value="et">Eesti - Estonian</option>
<option value="fi">Suomi - Finnish</option>
<option value="fr">Fran&ccedil;ais - French</option>
<option value="gl">Galego - Galician</option>
<option value="de">Deutsch - German</option>
<option value="el">&Epsilon;&lambda;&lambda;&eta;&nu;&iota;&kappa;&#940; - Greek</option>
<option value="hu">Magyar - Hungarian</option>
<option value="id">Bahasa Indonesia - Indonesian</option>
<option value="ia">Interlingua</option>
<option value="it">Italiano - Italian</option>
<option value="ja">&#26085;&#26412;&#35486; - Japanese</option>
<option value="ko">&#54620;&#44397;&#50612; - Korean</option>
<option value="nb">Norsk - Norwegian</option>
<option value="pl">Polski - Polish</option>
<option value="pt">Portugu&ecirc;s - Portuguese</option>
<option value="ro">Rom&acirc;n&#259; - Romanian</option>
<option value="ru">&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081; - Russian</option>
<option value="sr@latin">Srpski - Serbian (latin)</option>
<option value="si">&#3523;&#3538;&#3458;&#3524;&#3517; - Sinhala</option>
<option value="sq">Shqip - Slbanian</option>
<option value="sk">Sloven&#269;ina - Slovak</option>
<option value="sl">Sloven&scaron;&#269;ina - Slovenian</option>
<option value="es">Espa&ntilde;ol - Spanish</option>
<option value="sv">Svenska - Swedish</option>
<option value="tr">T&uuml;rk&ccedil;e - Turkish</option>
<option value="uk">&#1059;&#1082;&#1088;&#1072;&#1111;&#1085;&#1089;&#1100;&#1082;&#1072; - Ukrainian</option>
<option value="vi">Tiếng Việt - Vietnamese</option>
</select></form><h2>Overview</h2><div class="error" id="BZipDump"><h4>Bzip2</h4><a href="?page=form&amp;formset=Features&amp;lang=en#tab_Import_export">Bzip2 compression and decomp.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://10.129.227.147/phpmyadmin/setup/index.php'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)