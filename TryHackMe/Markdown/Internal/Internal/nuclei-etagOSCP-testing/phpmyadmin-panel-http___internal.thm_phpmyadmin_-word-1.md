### phpMyAdmin Panel - Detect (phpmyadmin-panel:word-1) found on internal.thm

----
**Details**: **phpmyadmin-panel:word-1** matched at internal.thm

**Protocol**: HTTP

**Full URL**: http://internal.thm/phpmyadmin/

**Timestamp**: Sun May 12 10:58:57 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | phpMyAdmin Panel - Detect |
| Authors | pdteam, righettod |
| Tags | panel, phpmyadmin |
| Severity | info |
| Description | phpMyAdmin panel was detected. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |
| product | phpmyadmin |
| shodan-query | http.title:phpMyAdmin |
| vendor | phpmyadmin |

**Request**
```http
GET /phpmyadmin/ HTTP/1.1
Host: internal.thm
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Norton/121.0.0.0
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate,  pre-check=0, post-check=0, max-age=0
Content-Security-Policy: default-src 'self' ;script-src 'self' 'unsafe-inline' 'unsafe-eval' ;;style-src 'self' 'unsafe-inline' ;referrer no-referrer;img-src 'self' data:  *.tile.openstreetmap.org;
Content-Type: text/html; charset=utf-8
Date: Sun, 12 May 2024 09:58:59 GMT
Expires: Sun, 12 May 2024 09:58:59 +0000
Last-Modified: Sun, 12 May 2024 09:58:59 +0000
Pragma: no-cache
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: pmaCookieVer=5; expires=Tue, 11-Jun-2024 09:58:59 GMT; Max-Age=2592000; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=imud2pkk0dreogn0l8v78dibeq; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=imud2pkk0dreogn0l8v78dibeq; path=/phpmyadmin/; HttpOnly
Set-Cookie: pma_lang=en; expires=Tue, 11-Jun-2024 09:58:59 GMT; Max-Age=2592000; path=/phpmyadmin/; HttpOnly
Set-Cookie: pma_collation_connection=utf8mb4_unicode_ci; expires=Tue, 11-Jun-2024 09:58:59 GMT; Max-Age=2592000; path=/phpmyadmin/; HttpOnly
Set-Cookie: phpMyAdmin=dt2uve7khvtjpeqthrh28u5nnm; path=/phpmyadmin/; HttpOnly
Vary: Accept-Encoding
X-Content-Security-Policy: default-src 'self' ;options inline-script eval-script;referrer no-referrer;img-src 'self' data:  *.tile.openstreetmap.org;
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-Ob_mode: 1
X-Permitted-Cross-Domain-Policies: none
X-Robots-Tag: noindex, nofollow
X-Webkit-Csp: default-src 'self' ;script-src 'self'  'unsafe-inline' 'unsafe-eval';referrer no-referrer;style-src 'self' 'unsafe-inline' ;img-src 'self' data:  *.tile.openstreetmap.org;
X-Xss-Protection: 1; mode=block

<!DOCTYPE HTML><html lang='en' dir='ltr' class='chrome chrome121'><head><meta charset="utf-8" /><meta name="referrer" content="no-referrer" /><meta name="robots" content="noindex,nofollow" /><meta http-equiv="X-UA-Compatible" content="IE=Edge" /><style id="cfs-style">html{display: none;}</style><link rel="icon" href="favicon.ico" type="image/x-icon" /><link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/jquery/jquery-ui-1.11.4.css" /><link rel="stylesheet" type="text/css" href="js/codemirror/lib/codemirror.css?v=4.6.6deb5" /><link rel="stylesheet" type="text/css" href="js/codemirror/addon/hint/show-hint.css?v=4.6.6deb5" /><link rel="stylesheet" type="text/css" href="js/codemirror/addon/lint/lint.css?v=4.6.6deb5" /><link rel="stylesheet" type="text/css" href="phpmyadmin.css.php?nocache=4437063584ltr" /><link rel="stylesheet" type="text/css" href="./themes/pmahomme/css/printview.css?v=4.6.6deb5" media="print" id="printcss"/><title>phpMyAdmin</title><script data-cfasync='false' type='text/javascript' src='js/whitelist.php?lang=en&amp;db=&amp;collation_connection=utf8mb4_unicode_ci&amp;token=270efb41993acd862718c8319821b669&v=4.6.6deb5'></script><script data-cfasync="false" type="text/javascript" src="js/get_scripts.js.php?scripts%5B%5D=jquery/jquery-2.1.4.min.js&amp;scripts%5B%5D=sprintf.js&amp;scripts%5B%5D=ajax.js&amp;scripts%5B%5D=keyhandler.js&amp;scripts%5B%5D=jquery/jquery-ui-1.11.4.min.js&amp;scripts%5B%5D=jquery/jquery.cookie.js&amp;scripts%5B%5D=jquery/jquery.mousewheel.js&amp;scripts%5B%5D=jquery/jquery.event.drag-2.2.js&amp;scripts%5B%5D=jquery/jquery-ui-timepicker-addon.js&amp;scripts%5B%5D=jquery/jquery.ba-hashchange-1.3.js&amp;v=4.6.6deb5"></script><script data-cfasync="false" type="text/javascript" src="js/get_scripts.js.php?scripts%5B%5D=jquery/jquery.debounce-1.0.5.js&amp;scripts%5B%5D=menu-resizer.js&amp;scripts%5B%5D=cross_framing_protection.js&amp;scripts%5B%5D=rte.js&amp;scripts%5B%5D=tracekit/tracekit.js&amp;scripts%5B%5D=error_report.js&amp;scripts%5B%5D=config.js&amp;scripts%5B%5D=doclinks.js&amp;scripts%5B%5D=functions.js&amp;scripts%5B%5D=navigation.js&amp;v=4.6.6deb5"></script><script data-cfasync="false" type="text/javascript" src="js/get_scripts.js.php?scripts%5B%5D=indexes.js&amp;scripts%5B%5D=common.js&amp;scripts%5B%5D=page_settings.js&amp;scripts%5B%5D=codemirror/lib/codemirror.js&amp;scripts%5B%5D=codemirror/mode/sql/sql.js&amp;scripts%5B%5D=codemirror/addon/runmode/runmode.js&amp;scripts%5B%5D=codemirror/addon/hint/show-hint.js&amp;scripts%5B%5D=codemirror/addon/hint/sql-hint.js&amp;scripts%5B%5D=codemirror/addon/lint/lint.js&amp;scripts%5B%5D=codemirror/addon/lint/sql-lint.js&amp;v=4.6.6deb5"></script><script data-cfasync="false" type="text/javascript" src="js/get_scripts.js.php?scripts%5B%5D=console.js&amp;v=4.6.6deb5"></script><script data-cfasync='false' type='text/javascript' src='js/messages.php?lang=en&amp;db=&amp;collation_connection=utf8mb4_unicode_ci&amp;token=270efb41993acd862718c8319821b669&v=4.6.6deb5'></script><script data-cfasync='false' type='text/javascript' src='js/get_image.js.php?theme=pmahomme&v=4.6.6deb5'></script><script data-cfasync="false" type="text/javascript">// <![CDATA[
PMA_commonParams.setAll({common_query:"?lang=en&collation_connection=utf8mb4_unicode_ci&token=270efb41993acd862718c8319821b669",opendb_url:"db_structure.php",safari_b.... Truncated ....
```

**Extra Information**

**Extracted results:**

- 4.6.6deb5

**Metadata:**

- paths: /phpmyadmin/



**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Norton/121.0.0.0' 'http://internal.thm/phpmyadmin/'
```

----

Generated by [Nuclei v3.2.6](https://github.com/projectdiscovery/nuclei)