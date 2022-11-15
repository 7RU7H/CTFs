### phpMyAdmin Panel (phpmyadmin-panel) found on http://10.129.227.147
---
**Details**: **phpmyadmin-panel**  matched at http://10.129.227.147

**Protocol**: HTTP

**Full URL**: http://10.129.227.147/phpmyadmin/

**Timestamp**: Tue Nov 15 12:38:40 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | phpMyAdmin Panel |
| Authors | pdteam |
| Tags | panel, phpmyadmin |
| Severity | info |
| shodan-query | http.title:phpMyAdmin |

**Request**
```http
GET /phpmyadmin/ HTTP/1.1
Host: 10.129.227.147
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36
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
Content-Security-Policy: default-src 'self' ;script-src 'self' 'unsafe-inline' 'unsafe-eval' ;style-src 'self' 'unsafe-inline' ;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
Content-Type: text/html; charset=utf-8
Date: Tue, 15 Nov 2022 12:38:40 GMT
Expires: Tue, 15 Nov 2022 12:38:41 +0000
Ironwaf: 2.0.3
Last-Modified: Tue, 15 Nov 2022 12:38:41 +0000
Pragma: no-cache
Referrer-Policy: no-referrer
Server: Apache/2.4.25 (Debian)
Set-Cookie: phpMyAdmin=m518fkki311uerk677p28ppjtrj0nrn8; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=m518fkki311uerk677p28ppjtrj0nrn8; path=/phpmyadmin/; HttpOnly
Set-Cookie: pma_lang=en; expires=Thu, 15-Dec-2022 12:38:40 GMT; Max-Age=2592000; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=2krvoajiq9b2kfubpghj14mg0un0utov; path=/phpmyadmin/; HttpOnly
Vary: Accept-Encoding
X-Content-Security-Policy: default-src 'self' ;options inline-script eval-script;referrer no-referrer;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Ob_mode: 1
X-Permitted-Cross-Domain-Policies: none
X-Robots-Tag: noindex, nofollow
X-Webkit-Csp: default-src 'self' ;script-src 'self'  'unsafe-inline' 'unsafe-eval';referrer no-referrer;style-src 'self' 'unsafe-inline' ;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Xss-Protection: 1; mode=block

<!DOCTYPE HTML><html lang='en' dir='ltr'><head><meta charset="utf-8" /><meta name="referrer" content="no-referrer" /><meta name="robots" content="noindex,nofollow" /><meta http-equiv="X-UA-Compatible" content="IE=Edge" /><meta name="viewport" content="width=device-width, initial-scale=1.0"><style id="cfs-style">html{display: none;}</style><link rel="icon" href="favicon.ico" type="image/x-icon" /><link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/jquery/jquery-ui.css" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/lib/codemirror.css?v=4.8.0" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/hint/show-hint.css?v=4.8.0" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/lint/lint.css?v=4.8.0" /><link rel="stylesheet" type="text/css" href="phpmyadmin.css.php?nocache=3046226179ltr&amp;server=1" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/css/printview.css?v=4.8.0" media="print" id="printcss"/><title>phpMyAdmin</title><script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.min.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-migrate.js?v=4.8.0"></script>
<script data-cfasync='false' type='text/javascript' src='js/whitelist.php?v=4.8.0&amp;lang=en'></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/sprintf.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/ajax.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/keyhandler.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui.min.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/js.cookie.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.mousewheel.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.event.drag-2.2.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.validate.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui-timepicker-addon.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.ba-hashchange-1.3.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.debounce-1.0.5.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/menu-resizer.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/cross_framing_protection.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/rte.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/tracekit.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/error_report.js?v=4.8.0"></script>
<script data-cfasync='false' type='text/javascript' src='js/messages.php?l=en&amp;v=4.8.0&amp;lang=en'></script>
<script data-cfasync="false" type="text/javascript" src="js/config.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/doclinks.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/functions.js?v=4.8.0"></script>
<script data-cfasync="false" type="text/javascript" src="js/navigation.js?v=4.8.0"></script>
<script data-cfasync="f.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 4.8.0



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36' 'http://10.129.227.147/phpmyadmin/'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)