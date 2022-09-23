### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.10.0.9
---
**Details**: **robots-txt-endpoint**  matched at http://10.10.0.9

**Protocol**: HTTP

**Full URL**: http://10.10.0.9/administrator/

**Timestamp**: Fri Sep 23 18:46:31 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /administrator/ HTTP/1.1
Host: 10.10.0.9
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4834
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html; charset=utf-8
Date: Fri, 23 Sep 2022 17:46:28 GMT
Expires: Wed, 17 Aug 2005 00:00:00 GMT
Last-Modified: Fri, 23 Sep 2022 17:46:30 GMT
Pragma: no-cache
Server: Apache/2.4.6 (CentOS) PHP/5.6.40
Set-Cookie: 2b01af51830ca9615359108de04d9ca1=nresmhjobf7hhs2kmcv13dl253; path=/; HttpOnly
X-Frame-Options: SAMEORIGIN
X-Powered-By: PHP/5.6.40

<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8" />
	<meta name="description" content="New York City tabloid newspaper" />
	<meta name="generator" content="Joomla! - Open Source Content Management" />
	<title>The Daily Bugle - Administration</title>
	<link href="/administrator/templates/isis/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link href="/administrator/templates/isis/css/template.css?787ff2be3d28e0eefd8e0930f878dbca" rel="stylesheet" />
	<style>

	@media (max-width: 480px) {
		.view-login .container {
			margin-top: -170px;
		}
		.btn {
			font-size: 13px;
			padding: 4px 10px 4px;
		}
	}
	</style>
	<script type="application/json" class="joomla-script-options new">{"system.keepalive":{"interval":840000,"uri":"\/administrator\/index.php"}}</script>
	<script src="/media/system/js/core.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<!--[if lt IE 9]><script src="/media/system/js/polyfill.event.js?787ff2be3d28e0eefd8e0930f878dbca"></script><![endif]-->
	<script src="/media/system/js/keepalive.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/jquery.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/jquery-noconflict.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/jquery-migrate.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<script src="/media/jui/js/bootstrap.min.js?787ff2be3d28e0eefd8e0930f878dbca"></script>
	<!--[if lt IE 9]><script src="/media/jui/js/html5.js?787ff2be3d28e0eefd8e0930f878dbca"></script><![endif]-->
	<script>
jQuery(function($){ $(".hasTooltip").tooltip({"html": true,"container": "body"}); });
	</script>

</head>
<body class="site com_login view-login layout-default task- itemid- ">
	<!-- Container -->
	<div class="container">
		<div id="content">
			<!-- Begin Content -->
			<div id="element-box" class="login well">
									<img src="/administrator/templates/isis/images/joomla.png" alt="The Daily Bugle" />
								<hr />
				<div id="system-message-container">
	</div>

				<form action="/administrator/index.php" method="post" id="form-login" class="form-inline">
	<fieldset class="loginform">
		<div class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on">
						<span class="icon-user hasTooltip" title="Username"></span>
						<label for="mod-login-username" class="element-invisible">
							Username						</label>
					</span>
					<input name="username" tabindex="1" id="mod-login-username" type="text" class="input-medium" placeholder="Username" size="15" autofocus="true" />
					<a href="http://10.10.0.9/index.php?option=com_users&view=remind" class="btn width-auto hasTooltip" title="Forgot your username?">
						<span class="icon-help"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on">
						<span class="icon-lock hasTooltip" title="Password"></span>
						<label for="mod-login-password" class="element-invisible">
							Password						</label>
					</span>
					<input name="passwd" tabindex="2" id="mod-login-password" type="password" class="input-medium" placeholder="Password" size="15"/>
					<a href="http://10.10.0.9/index.php?option=com_users&view=reset" class="btn width-auto hasTooltip" title="Forgot your password?">
						<span class="icon-help"></span>
					</a>
				</div>
			</div>
		</div>
						<div class="control-group">
			<div class="controls">
				<div class="btn-group">
					<button tabindex="3" class="btn btn-primary btn-block btn-large login-button">
						<span class="icon-lock icon-white"></span> Log in					</button>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_login"/>
		<input type="hidden" name="task" value="login"/>
		<input type="hidden" name="return" value="aW5kZXgucGhw"/>
		<input type="hidden" name="3d69b972918ce7cc4e3aecdd6e35bcbe" value="1" />	</fieldset>
</form>

			</div>
			<noscript>
				Warning! JavaScript must be enabled for proper operation of the Administrator Backend.			</noscript>
			<!-- End Content -->
		</div>
	</div>
	<div class="navbar navbar-fixed-bottom hidden-phone">
		<p class="pull-right">
			&copy; 2022 The Daily Bugle		</p>
		<a class="login-joomla hasTooltip" href="https://www.joomla.org" target="_blank"  rel="noopener noreferrer" title="Joomla is free software released un.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36' 'http://10.10.0.9/administrator/'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)