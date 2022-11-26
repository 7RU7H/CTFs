### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-methods) found on http://192.168.200.214
---
**Details**: **http-missing-security-headers:access-control-allow-methods**  matched at http://192.168.200.214

**Protocol**: HTTP

**Full URL**: http://192.168.200.214

**Timestamp**: Thu Oct 27 18:21:03 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.200.214
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Thu, 27 Oct 2022 17:21:03 GMT
Server: Apache/2.4.53 (Debian)
Vary: Accept-Encoding
X-Backend-Server: primary


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <title>Cobbles</title>
    <link media="all" href="style.css" rel="stylesheet" type="text/css" />
    <link id="favicon" rel="shortcut icon" type="image/png" href="favicon.png" />
  </head>
  <body>
    <a href="/"><h1>Cobbles</h1></a>
    <div id="login_box">
      <form id="login" action="index.php" method="post">
        <div id="login_control">
          <table>
            <tr>
              <td><b>Username:</b></td>
              <td><input type="text" id="username" name="username"></td>
            </tr>
            <tr>
              <td><b>Password:</b></td>
              <td><input type="password" id="password" name="password"></td>
            </tr>
            <tr>
              <td></td>
              <td>
                <center><input type="submit" class="button" value="Log In"></center>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <center><span id="error"></span></center>
              </td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36' 'http://192.168.200.214'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)