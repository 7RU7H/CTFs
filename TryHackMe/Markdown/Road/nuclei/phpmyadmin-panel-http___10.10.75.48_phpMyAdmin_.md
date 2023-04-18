### phpMyAdmin Panel - Detect (phpmyadmin-panel) found on http://10.10.75.48
---
**Details**: **phpmyadmin-panel**  matched at http://10.10.75.48

**Protocol**: HTTP

**Full URL**: http://10.10.75.48/phpMyAdmin/

**Timestamp**: Tue Apr 18 11:01:29 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
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
GET /phpMyAdmin/ HTTP/1.1
Host: 10.10.75.48
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
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
Date: Tue, 18 Apr 2023 10:01:28 GMT
Expires: Tue, 18 Apr 2023 10:01:28 +0000
Last-Modified: Tue, 18 Apr 2023 10:01:28 +0000
Pragma: no-cache
Referrer-Policy: no-referrer
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: phpMyAdmin=ev2q2frt7rbsf6f9cs51oo7grq; path=/phpMyAdmin/; HttpOnly
Set-Cookie: phpMyAdmin=ev2q2frt7rbsf6f9cs51oo7grq; path=/phpMyAdmin/; HttpOnly
Set-Cookie: pma_lang=en; expires=Thu, 18-May-2023 10:01:28 GMT; Max-Age=2592000; path=/phpMyAdmin/; HttpOnly; SameSite=Strict
Set-Cookie: phpMyAdmin=r76gaboh5km2m28150la49ror7; path=/phpMyAdmin/; HttpOnly
Vary: Accept-Encoding
X-Content-Security-Policy: default-src 'self' ;options inline-script eval-script;referrer no-referrer;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Ob_mode: 1
X-Permitted-Cross-Domain-Policies: none
X-Robots-Tag: noindex, nofollow
X-Webkit-Csp: default-src 'self' ;script-src 'self'  'unsafe-inline' 'unsafe-eval';referrer no-referrer;style-src 'self' 'unsafe-inline' ;img-src 'self' data:  *.tile.openstreetmap.org;object-src 'none';
X-Xss-Protection: 1; mode=block

<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="referrer" content="no-referrer">
  <meta name="robots" content="noindex,nofollow">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <style id="cfs-style">html{display: none;}</style>
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
      <link rel="stylesheet" type="text/css" href="./themes/pmahomme/jquery/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="js/vendor/codemirror/lib/codemirror.css?v=5.1.0">
    <link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/hint/show-hint.css?v=5.1.0">
    <link rel="stylesheet" type="text/css" href="js/vendor/codemirror/addon/lint/lint.css?v=5.1.0">
    <link rel="stylesheet" type="text/css" href="./themes/pmahomme/css/theme.css?v=5.1.0&nocache=3228293707ltr&server=1">
    <link rel="stylesheet" type="text/css" href="./themes/pmahomme/css/printview.css?v=5.1.0" media="print" id="printcss">
    <title>phpMyAdmin</title>
    <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.min.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-migrate.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/sprintf.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/ajax.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/keyhandler.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/bootstrap/bootstrap.bundle.min.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui.min.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/js.cookie.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.mousewheel.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.event.drag-2.2.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.validate.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery-ui-timepicker-addon.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.ba-hashchange-2.0.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/jquery/jquery.debounce-1.0.6.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/menu_resizer.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/cross_framing_protection.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/rte.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/vendor/tracekit.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/error_report.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/messages.php?l=en&amp;v=5.1.0&amp;lang=en"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/config.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascript" src="js/dist/doclinks.js?v=5.1.0"></script>
  <script data-cfasync="false" type="text/javascri.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 5.1.0



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://10.10.75.48/phpMyAdmin/'
```
---
Generated by [Nuclei 2.9.1](https://github.com/projectdiscovery/nuclei)