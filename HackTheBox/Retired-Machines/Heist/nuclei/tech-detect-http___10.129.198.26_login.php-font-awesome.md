### Wappalyzer Technology Detection (tech-detect:font-awesome) found on http://10.129.198.26
---
**Details**: **tech-detect:font-awesome**  matched at http://10.129.198.26

**Protocol**: HTTP

**Full URL**: http://10.129.198.26/login.php

**Timestamp**: Thu Apr 27 11:22:12 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.198.26
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 2058
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Thu, 27 Apr 2023 10:22:11 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Microsoft-IIS/10.0
Set-Cookie: PHPSESSID=8jsl29icecn1r5jt5fbsimkt63; path=/
X-Powered-By: PHP/7.3.1

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Support Login Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>

      <link rel="stylesheet" href="css/style.css">

  
</head>

<body>

  <div id="particles-js"></div>
<body class="login">
	<div class="container">
		<div class="login-container-wrapper clearfix">
			<div class="logo">
				<i class="fa fa-sign-in"></i>
			</div>
			<div class="welcome"><strong>Welcome,</strong> please login</div>

			<form class="form-horizontal login-form" method="POST" action="/login.php">
				<div class="form-group relative">
					<input id="login_username" name="login_username" class="form-control input-lg" type="email" placeholder="Username" required>
					<i class="fa fa-user"></i>
				</div>
				<div class="form-group relative password">
					<input id="login_password" name="login_password" class="form-control input-lg" type="password" placeholder="Password" required>
					<i class="fa fa-lock"></i>
				</div>
			  <div class="form-group">
			    <button name="login" type="submit" class="btn btn-success btn-lg btn-block">Login</button>
			  </div>
        <div class="checkbox pull-left">
			    <label><input type="checkbox" name="remember"> Remember</label>
			  </div>
			  <div class="checkbox pull-right">
			    <label> <a class="forget" href="/login.php?guest=true" title="forget">Login as guest</a> </label>
			  </div>
			</form>
		</div>
    
    <h4 class="text-center">
      <a target="_blank" href="">
	24 x 7 Support
      </a>
    </h4>
	</div>

  </body>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js'></script>

  

    <script  src="js/index.js"></script>




</body>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.198.26' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://10.129.198.26/login.php'
```
---
Generated by [Nuclei v2.9.2](https://github.com/projectdiscovery/nuclei)