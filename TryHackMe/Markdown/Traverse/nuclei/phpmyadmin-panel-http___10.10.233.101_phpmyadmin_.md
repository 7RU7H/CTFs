### phpMyAdmin Panel - Detect (phpmyadmin-panel) found on http://10.10.233.101/

----
**Details**: **phpmyadmin-panel** matched at http://10.10.233.101/

**Protocol**: HTTP

**Full URL**: http://10.10.233.101/phpmyadmin/

**Timestamp**: Sat Aug 5 10:41:14 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | phpMyAdmin Panel - Detect |
| Authors | pdteam |
| Tags | panel, phpmyadmin |
| Severity | info |
| Description | phpMyAdmin panel was detected. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |
| shodan-query | http.title:phpMyAdmin |

**Request**
```http
GET /phpmyadmin/ HTTP/1.1
Host: 10.10.233.101
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2762.73 Safari/537.36
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
Date: Sat, 05 Aug 2023 09:40:52 GMT
Expires: Sat, 05 Aug 2023 09:40:54 +0000
Last-Modified: Sat, 05 Aug 2023 09:40:54 +0000
Pragma: no-cache
Referrer-Policy: no-referrer
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: phpMyAdmin=11m71qvhqbkupqgnp5piumbiah; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=11m71qvhqbkupqgnp5piumbiah; path=/phpmyadmin/; HttpOnly
Set-Cookie: goto=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/phpmyadmin/
Set-Cookie: back=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/phpmyadmin/
Set-Cookie: pma_lang=en; expires=Mon, 04-Sep-2023 09:40:53 GMT; Max-Age=2592000; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=41j9um431i18lt3b7lcib05foe; path=/phpmyadmin/; HttpOnly
Vary: Accept-Encoding
X-Content-Security-Policy: default-src 'self' ;options inline-script eval-script;referrer no-referrer;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Ob_mode: 1
X-Permitted-Cross-Domain-Policies: none
X-Robots-Tag: noindex, nofollow
X-Webkit-Csp: default-src 'self' ;script-src 'self'  'unsafe-inline' 'unsafe-eval';referrer no-referrer;style-src 'self' 'unsafe-inline' ;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Xss-Protection: 1; mode=block

<!DOCTYPE HTML><html lang='en' dir='ltr'><head><meta charset="utf-8" /><meta name="referrer" content="no-referrer" /><meta name="robots" content="noindex,nofollow" /><meta http-equiv="X-UA-Compatible" content="IE=Edge" /><meta name="viewport" content="width=device-width, initial-scale=1.0"><style id="cfs-style">html{display: none;}</style><link rel="icon" href="favicon.ico" type="image/x-icon" /><link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/jquery/jquery-ui.css" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/lib/codemirror.css?v=4.9.5deb2" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/hint/show-hint.css?v=4.9.5deb2" /><link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/lint/lint.css?v=4.9.5deb2" /><link rel="stylesheet" type="text/css" href="phpmyadmin.css.php?nocache=4755212520ltr&amp;server=1" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/css/printview.css?v=4.9.5deb2" media="print" id="printcss"/><title>phpMyAdmin</title><script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.min.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-migrate.js?v=4.9.5deb2"></script>
<script data-cfasync='false' type='text/javascript' src='js/whitelist.php?v=4.9.5deb2&amp;lang=en'></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/sprintf.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/ajax.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/keyhandler.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui.min.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/js.cookie.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.mousewheel.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.event.drag-2.2.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.validate.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui-timepicker-addon.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.ba-hashchange-1.3.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.debounce-1.0.5.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/menu-resizer.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/cross_framing_protection.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/rte.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/vendor/tracekit.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/javascript" src="js/error_report.js?v=4.9.5deb2"></script>
<script data-cfasync='false' type='text/javascript' src='js/messages.php?l=en&amp;v=4.9.5deb2&amp;lang=en'></script>
<script data-cfasync="false" type="text/javascript" src="js/config.js?v=4.9.5deb2"></script>
<script data-cfasync="false" type="text/jav.... Truncated ....
```

**Extra Information**

**Extracted results:**

- 4.9.5deb2



**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2762.73 Safari/537.36' 'http://10.10.233.101/phpmyadmin/'
```

----

Generated by [Nuclei v2.9.9](https://github.com/projectdiscovery/nuclei)