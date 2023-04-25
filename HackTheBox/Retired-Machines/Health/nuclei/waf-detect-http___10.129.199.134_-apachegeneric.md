### WAF Detection (waf-detect:apachegeneric) found on http://10.129.199.134
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.129.199.134

**Protocol**: HTTP

**Full URL**: http://10.129.199.134/

**Timestamp**: Mon Apr 24 13:06:20 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST / HTTP/1.1
Host: 10.129.199.134
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.0 405 Method Not Allowed
Allow: GET, HEAD
Cache-Control: no-cache, private
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Mon, 24 Apr 2023 12:06:15 GMT
Server: Apache/2.4.29 (Ubuntu)

<!doctype html>
<html class="theme-light">
<!--
Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException: The POST method is not supported for this route. Supported methods: GET, HEAD. in file /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php on line 117

#0 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php(103): Illuminate\Routing\AbstractRouteCollection-&gt;methodNotAllowed()
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php(40): Illuminate\Routing\AbstractRouteCollection-&gt;getRouteForMethods()
#2 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/RouteCollection.php(162): Illuminate\Routing\AbstractRouteCollection-&gt;handleMatchedRoute()
#3 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(673): Illuminate\Routing\RouteCollection-&gt;match()
#4 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(662): Illuminate\Routing\Router-&gt;findRoute()
#5 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(651): Illuminate\Routing\Router-&gt;dispatchToRoute()
#6 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(167): Illuminate\Routing\Router-&gt;dispatch()
#7 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(128): Illuminate\Foundation\Http\Kernel-&gt;Illuminate\Foundation\Http\{closure}()
#8 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#9 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php(31): Illuminate\Foundation\Http\Middleware\TransformsRequest-&gt;handle()
#10 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull-&gt;handle()
#11 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#12 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php(40): Illuminate\Foundation\Http\Middleware\TransformsRequest-&gt;handle()
#13 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Illuminate\Foundation\Http\Middleware\TrimStrings-&gt;handle()
#14 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ValidatePostSize.php(27): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#15 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Illuminate\Foundation\Http\Middleware\ValidatePostSize-&gt;handle()
#16 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php(86): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#17 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance-&gt;handle()
#18 /var/www/html/vendor/fruitcake/laravel-cors/src/HandleCors.php(38): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#19 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Fruitcake\Cors\HandleCors-&gt;handle()
#20 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php(39): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#21 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(167): Illuminate\Http\Middleware\TrustProxies-&gt;handle()
#22 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(103): Illuminate\Pipeline\Pipeline-&gt;Illuminate\Pipeline\{closure}()
#23 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(142): Illuminate\Pipeline\Pipeline-&gt;then()
#24 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(111): Illuminate\Foundation\Http\Kernel-&gt;sendRequestThroughRouter()
#25 /var/www/html/public/index.php(52): Illuminate\Foundation\Http\Kernel-&gt;handle()
#26 {main}
-->
<head>
    <!-- Hide dumps asap -->
    <style>
        pre.sf-dump {
            display: none !important;
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">

    <title>🧨 The POST method is not supported for this route. Supported methods: GET, HEAD.</title>

    
</head>
<body class="scrollbar-lg">

<script>
    window.data = {"report":{"notifier":"Laravel Client","language":"PHP","framework_version":"8.8.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.129.199.134' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://10.129.199.134/'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)