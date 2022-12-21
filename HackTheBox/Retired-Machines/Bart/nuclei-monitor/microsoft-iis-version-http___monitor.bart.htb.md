### Microsoft IIS version detect (microsoft-iis-version) found on http://monitor.bart.htb
---
**Details**: **microsoft-iis-version**  matched at http://monitor.bart.htb

**Protocol**: HTTP

**Full URL**: http://monitor.bart.htb

**Timestamp**: Sat Dec 3 17:53:39 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Microsoft IIS version detect |
| Authors | wlayzz |
| Tags | tech, microsoft, iis |
| Severity | info |
| Description | Some Microsoft IIS servers have the version on the response header. Useful when you need to find specific CVEs on your targets. |

**Request**
```http
GET / HTTP/1.1
Host: monitor.bart.htb
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
Content-Length: 3423
Cache-Control: no-cache, must-revalidate,no-cache
Content-Type: text/html; charset=UTF-8
Date: Sat, 03 Dec 2022 17:53:38 GMT
Expires: Mon, 20 Dec 1998 01:00:00 GMT
Last-Modified: Sat, 03 Dec 2022 17:53:39 GMT
Pragma: no-cache
Server: Microsoft-IIS/10.0
Set-Cookie: PHPSESSID=t09dnt08jd6o07l9on74490uvq; path=/
X-Powered-By: PHP/7.1.7

<!DOCTYPE html>
<html lang="en" dir="ltr" class="ltr">
	<head>
    <meta charset="utf-8">
    <title>SERVER MONITOR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1">
    <meta name="description" content="">
    <meta name="robots" content="noindex" />		
    <meta name="theme-color" content="#424242">
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link rel="icon" type="image/png" href="favicon.png" />
	<link rel="apple-touch-icon" href="favicon.png" />
    <!-- Le styles -->
    <link href="static/plugin/twitter-bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="static/plugin/twitter-bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="static/plugin/bootstrap-multiselect/bootstrap-multiselect.min.css" rel="stylesheet">
    <link href="static/css/style.css" rel="stylesheet">
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <script type="text/javascript" src="static/plugin/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="static/plugin/twitter-bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="static/plugin/bootstrap-multiselect/bootstrap-multiselect.min.js"></script>
    <script type="text/javascript" src="static/js/scripts.js"></script>
  </head>
  <body data-spy="scroll" data-target=".subnav" data-offset="50" class="">
    <!-- navbar -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="index.php">SERVER MONITOR</a>

        </div>
      </div>
    </div>
    <!-- /navbar -->

	
    <!-- container -->
    <div id="main-container">
		<div class="page-header">
			<div class="header-label"><h1></h1></div>
			<div class="header-accessories"></div>
		</div>
		<div id="main-content">
			
			<div id="page-container">
				<div id="flashmessage" class="hide">
									</div>
				<div class="container">
  <form class="form-signin" method="post">
	<input type="hidden" name="csrf" value="72be79ccdb257caf489bf0137a582171e57e608819a312506494a6b8b1119daa" />

    <h3 class="form-signin-heading">Please sign in</h3>
    <input type="text" name="user_name" class="input-block-level" placeholder="Username" value="" required>
    <input type="password" name="user_password" class="input-block-level" placeholder="Password" required>
	<input type="hidden" name="action" value="login">
    <label class="checkbox">
  	  <input type="checkbox" name="user_rememberme" value="1" > Remember me
    </label>
    <button class="btn btn-primary" type="submit">Login</button>
    <a class="btn" href="?action=forgot">Forgot password?</a>
  </form>
</div>
			</div>
		</div>
								<footer class="footer">
			  <p class="pull-right"><a href="#">Back to top</a></p>
			  <p class="powered"><small>Powered by <a href="http://www.phpservermonitor.org/" target="_blank">PHP Server Monitor v3.2.1</a>.<br/></small></p>
			</footer>
						</div>
    <!-- /container -->
    </body>
</html>

```

**Extra Information**

**Extracted results**:

- Microsoft-IIS/10.0



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://monitor.bart.htb'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)