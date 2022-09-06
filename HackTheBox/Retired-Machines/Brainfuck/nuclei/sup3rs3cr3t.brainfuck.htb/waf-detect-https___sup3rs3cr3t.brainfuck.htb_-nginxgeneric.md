### WAF Detection (waf-detect:nginxgeneric) found on https://sup3rs3cr3t.brainfuck.htb
---
**Details**: **waf-detect:nginxgeneric**  matched at https://sup3rs3cr3t.brainfuck.htb

**Protocol**: HTTP

**Full URL**: https://sup3rs3cr3t.brainfuck.htb/

**Timestamp**: Wed Aug 17 20:52:50 +0100 BST 2022

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
Host: sup3rs3cr3t.brainfuck.htb
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 500 Internal Server Error
Connection: close
Content-Length: 152660
Content-Type: text/html; charset=utf-8
Date: Wed, 17 Aug 2022 19:52:50 GMT
Server: nginx/1.10.0 (Ubuntu)
Set-Cookie: flarum_session=a281e1if0gdhofhulef393cs55; Path=/; HttpOnly
X-Csrf-Token: Fnb9hW4O8NeoFVQt2JI2OfCIuCW5UmNWPkuXcrZK

<!DOCTYPE html><!--


Flarum\Http\Exception\MethodNotAllowedException:  in file /var/www/secret/vendor/flarum/core/src/Http/Middleware/DispatchRoute.php on line 65
Stack trace:
  1. Flarum\Http\Exception\MethodNotAllowedException->() /var/www/secret/vendor/flarum/core/src/Http/Middleware/DispatchRoute.php:65
  2. Flarum\Http\Middleware\DispatchRoute->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
  3. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
  4. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
  5. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/flarum/core/src/Http/Middleware/SetLocale.php:50
  6. Flarum\Http\Middleware\SetLocale->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
  7. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
  8. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
  9. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/flarum/core/src/Http/Middleware/AuthenticateWithSession.php:33
 10. Flarum\Http\Middleware\AuthenticateWithSession->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
 11. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
 12. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
 13. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/flarum/core/src/Http/Middleware/RememberFromCookie.php:38
 14. Flarum\Http\Middleware\RememberFromCookie->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
 15. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
 16. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
 17. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/flarum/core/src/Http/Middleware/StartSession.php:33
 18. Flarum\Http\Middleware\StartSession->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
 19. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
 20. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
 21. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/flarum/core/src/Http/Middleware/ParseJsonBody.php:30
 22. Flarum\Http\Middleware\ParseJsonBody->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:224
 23. Zend\Stratigility\Dispatch->dispatchCallableMiddleware() /var/www/secret/vendor/zendframework/zend-stratigility/src/Dispatch.php:88
 24. Zend\Stratigility\Dispatch->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/Next.php:160
 25. Zend\Stratigility\Next->__invoke() /var/www/secret/vendor/zendframework/zend-stratigility/src/MiddlewarePipe.php:111
 26. Zend\Stratigility\MiddlewarePipe->__invoke() /var/www/secret/vendor/flarum/core/src/Http/AbstractServer.php:53
 27. Flarum\Http\AbstractServer->__invoke() /var/www/secret/vendor/zendframework/zend-diactoros/src/Server.php:166
 28. Zend\Diactoros\Server->listen() /var/www/secret/vendor/flarum/core/src/Http/AbstractServer.php:34
 29. Flarum\Http\AbstractServer->listen() /var/www/secret/index.php:16











--><html>
  <head>
    <meta charset="utf-8">
    <title>Whoops! There was an error.</title>

    <style>body {
  font: 12px "Helvetica Neue", helvetica, arial, sans-serif;
  color: #131313;
  background: #eeeeee;
  padding:0;
  margin: 0;
  max-height: 100%;

  text-rendering: optimizeLegibility;
}
  a {
    text-decoration: none;
  }

.panel {
    overflow-x: hidden;
    overflow-y: scroll;
    height: 100%;
    position: fixed;
    margin: 0;
    left: 0;
    top: 0;
}

.branding {
  position: absolute;
  top: 10px;
  right: 20px;
  color: #777777;
  font-size: 10px;
    z-index: 100;
}
  .branding a {
    color: #e95353;
  }

header {
  color: white;
  box-sizing: border-box;
  background-color: #2a2a2a;
  padding: 35px 40px;
  max-height: 180px;
  overflow: hidden;
  transition: 0.5s;
}

  header.header-expand {
    max-height: 1000px;
  }

  .exc-title {
    margin: 0;
    color: #bebebe;
    font-size: 14px;
  }
    .exc-title-primary {
      color: #e95353;
    }

    .exc-message {
      font-size: 20px;
      word-wrap: break-word.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: sup3rs3cr3t.brainfuck.htb' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'https://sup3rs3cr3t.brainfuck.htb/'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)