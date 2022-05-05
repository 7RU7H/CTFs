### WordPress login (wordpress-login) found on http://10.10.113.121
---
**Details**: **wordpress-login**  matched at http://10.10.113.121

**Protocol**: HTTP

**Full URL**: http://10.10.113.121/wp-login.php

**Timestamp**: Thu May 5 18:05:57 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | WordPress login |
| Authors | its0x08 |
| Tags | panel, wordpress |
| Severity | info |

**Request**

```http
GET /wp-login.php HTTP/1.1
Host: 10.10.113.121
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**

```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-cache, must-revalidate, max-age=0
Content-Type: text/html; charset=UTF-8
Date: Thu, 05 May 2022 17:05:58 GMT
Expires: Wed, 11 Jan 1984 05:00:00 GMT
Server: Apache/2.4.18 (Ubuntu)
Set-Cookie: wordpress_test_cookie=WP+Cookie+check; path=/
Vary: Accept-Encoding
X-Frame-Options: SAMEORIGIN

<!DOCTYPE html>
	<!--[if IE 8]>
		<html xmlns="http://www.w3.org/1999/xhtml" class="ie8" lang="en-US">
	<![endif]-->
	<!--[if !(IE 8) ]><!-->
		<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
	<!--<![endif]-->
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Log In &lsaquo; Jack&#039;s Personal Site &#8212; WordPress</title>
	<link rel='dns-prefetch' href='//s.w.org' />
<link rel='stylesheet' id='dashicons-css'  href='http://jack.thm/wp-includes/css/dashicons.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='buttons-css'  href='http://jack.thm/wp-includes/css/buttons.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='forms-css'  href='http://jack.thm/wp-admin/css/forms.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='l10n-css'  href='http://jack.thm/wp-admin/css/l10n.min.css?ver=5.3.2' type='text/css' media='all' />
<link rel='stylesheet' id='login-css'  href='http://jack.thm/wp-admin/css/login.min.css?ver=5.3.2' type='text/css' media='all' />
	<meta name='robots' content='noindex,noarchive' />
	<meta name='referrer' content='strict-origin-when-cross-origin' />
		<meta name="viewport" content="width=device-width" />
		</head>
	<body class="login no-js login-action-login wp-core-ui  locale-en-us">
	<script type="text/javascript">
		document.body.className = document.body.className.replace('no-js','js');
	</script>
		<div id="login">
		<h1><a href="https://wordpress.org/">Powered by WordPress</a></h1>
	
		<form name="loginform" id="loginform" action="http://jack.thm/wp-login.php" method="post">
			<p>
				<label for="user_login">Username or Email Address</label>
				<input type="text" name="log" id="user_login" class="input" value="" size="20" autocapitalize="off" />
			</p>

			<div class="user-pass-wrap">
				<label for="user_pass">Password</label>
				<div class="wp-pwd">
					<input type="password" name="pwd" id="user_pass" class="input password-input" value="" size="20" />
					<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
						<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
					</button>
				</div>
			</div>
						<p class="forgetmenot"><input name="rememberme" type="checkbox" id="rememberme" value="forever"  /> <label for="rememberme">Remember Me</label></p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In" />
									<input type="hidden" name="redirect_to" value="http://jack.thm/wp-admin/" />
									<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>

					<p id="nav">
									<a href="http://jack.thm/wp-login.php?action=lostpassword">Lost your password?</a>
								</p>
					<script type="text/javascript">
			function wp_attempt_focus() {setTimeout( function() {try {d = document.getElementById( "user_login" );d.focus(); d.select();} catch( er ) {}}, 200);}
wp_attempt_focus();
if ( typeof wpOnload === 'function' ) { wpOnload() }		</script>
				<p id="backtoblog"><a href="http://jack.thm/">
		&larr; Back to Jack&#039;s Personal Site		</a></p>
			</div>
	<script type='text/javascript' src='http://jack.thm/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp'></script>
<script type='text/javascript' src='http://jack.thm/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var _zxcvbnSettings = {"src":"http:\/\/jack.thm\/wp-includes\/js\/zxcvbn.min.js"};
/* ]]> */
</script>
<script type='text/javascript' src='http://jack.thm/wp-includes/js/zxcvbn-async.min.js?ver=1.0'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var pwsL10n = {"unknown":"Password strength unknown","short":"Very weak","bad":"Weak","good":"Medium","strong":"Strong","mismatch":"Mismatch"};
/* ]]> */
</script>
<script type='text/javascript' src='http://jack.thm/wp-admin/js/password-strength-meter.min.js?ver=5.3.2'></script>
<script type='text/javascript' src='http://jack.thm/wp-includes/js/underscore.min.js?ver=1.8.3'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var _wpUtilSettings = {"ajax":{"url":"\/wp-admin\/admin-ajax.php"}};
/* ]]> */
</script>
<script type='text/javascript' src='http://jack.thm/wp-includes/js/wp-util.min.js?ver=5.3.2'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var userProfileL10n = {"warn":"Your new password has not been saved.","warnWeak":"Confirm use of weak password","show":"Show","hide":"Hide","cancel":"Cancel","ariaShow":"Show password","ariaHide":"Hide password"};
/* ]]> */
</script>
<script type='text/javascript' src='http://jack.thm/wp-admin/js/user-profile.mi.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.10.113.121/wp-login.php'
```
---
Generated by [Nuclei 2.6.9](https://github.com/projectdiscovery/nuclei)