### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-resource-policy) found on http://10.10.132.79:8080/

----
**Details**: **http-missing-security-headers:cross-origin-resource-policy** matched at http://10.10.132.79:8080/

**Protocol**: HTTP

**Full URL**: http://10.10.132.79:8080/

**Timestamp**: Sat Jul 1 18:51:15 +0100 BST 2023

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
GET / HTTP/1.1
Host: 10.10.132.79:8080
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
Content-Length: 612
Content-Type: text/html
Date: Sat, 01 Jul 2023 17:50:31 GMT
Last-Modified: Mon, 13 Mar 2023 16:33:15 GMT
Server: SimpleHTTP/0.6 Python/3.6.9

<!DOCTYPE html>
<html>
<head>
<title>Welcome to nginx!</title>
<style>
    body {
        width: 35em;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
    }
</style>
</head>
<body>
<h1>Welcome to nginx!</h1>
<p>If you see this page, the nginx web server is successfully installed and
working. Further configuration is required.</p>

<p>For online documentation and support please refer to
<a href="http://nginx.org/">nginx.org</a>.<br/>
Commercial support is available at
<a href="http://nginx.com/">nginx.com</a>.</p>

<p><em>Thank you for using nginx.</em></p>
</body>
</html>

```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.10.132.79:8080/'
```

----

Generated by [Nuclei v2.9.7](https://github.com/projectdiscovery/nuclei)