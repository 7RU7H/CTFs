### Wappalyzer Technology Detection (tech-detect:php) found on http://10.129.77.22
---
**Details**: **tech-detect:php**  matched at http://10.129.77.22

**Protocol**: HTTP

**Full URL**: https://10.129.77.22/

**Timestamp**: Sat Jul 9 19:50:18 +0100 BST 2022

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
Host: 10.129.77.22
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 1785
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html; charset=UTF-8
Date: Sat, 09 Jul 2022 18:50:18 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.2.3 (CentOS)
Set-Cookie: elastixSession=c4e2lo6o3gsc15f5a8tnato4g5; path=/
X-Powered-By: PHP/5.1.6

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<title>Elastix - Login page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">-->
	<link rel="stylesheet" href="themes/elastixneo/login_styles.css">
  </head>
  <body>
	<form method="POST">
	  <div id="neo-login-box">
		<div id="neo-login-logo">
		  <img src="themes/elastixneo/images/elastix_logo_mini.png" width="200" height="62" alt="elastix logo" />
		</div>
		<div class="neo-login-line">
		  <div class="neo-login-label">Username:</div>
		  <div class="neo-login-inputbox"><input type="text" id="input_user" name="input_user" class="neo-login-input" /></div>
		</div>
		<div class="neo-login-line">
		  <div class="neo-login-label">Password:</div>
		  <div class="neo-login-inputbox"><input type="password" name="input_pass" class="neo-login-input" /></div>
		</div>
		<div class="neo-login-line">
		  <div class="neo-login-label"></div>
		  <div class="neo-login-inputbox"><input type="submit" name="submit_login" value="Submit" class="neo-login-submit" /></div>
		</div>
		<div class="neo-footernote"><a href="http://www.elastix.org" style="text-decoration: none;" target='_blank'>Elastix</a> is licensed under <a href="http://www.opensource.org/licenses/gpl-license.php" style="text-decoration: none;" target='_blank'>GPL</a> by <a href="http://www.palosanto.com" style="text-decoration: none;" target='_blank'>PaloSanto Solutions</a>. 2006 - 2022.</div>
		<br>
		<script type="text/javascript">
			document.getElementById("input_user").focus();
		</script>
	  </div>
	</form>
  </body>
</html>
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.77.22' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'https://10.129.77.22/'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)