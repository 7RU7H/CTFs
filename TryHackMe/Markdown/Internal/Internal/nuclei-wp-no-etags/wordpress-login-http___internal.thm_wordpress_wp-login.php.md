### WordPress Login Panel - Detect (wordpress-login) found on internal.thm

----
**Details**: **wordpress-login** matched at internal.thm

**Protocol**: HTTP

**Full URL**: http://internal.thm/wordpress/wp-login.php

**Timestamp**: Sun May 12 11:03:35 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | WordPress Login Panel - Detect |
| Authors | its0x08 |
| Tags | panel, wordpress |
| Severity | info |
| Description | WordPress login panel was detected. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |
| product | wordpress |
| vendor | wordpress |

**Request**
```http
GET /wordpress/wp-login.php HTTP/1.1
Host: internal.thm
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.1517.4 Ddg/17.4
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
Date: Sun, 12 May 2024 10:03:37 GMT
Expires: Wed, 11 Jan 1984 05:00:00 GMT
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: wordpress_test_cookie=WP+Cookie+check; path=/blog/
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
	<title>Log In &lsaquo; Internal &#8212; WordPress</title>
	<link rel='dns-prefetch' href='//s.w.org' />
<link rel='stylesheet' id='dashicons-css'  href='http://internal.thm/blog/wp-includes/css/dashicons.min.css?ver=5.4.2' media='all' />
<link rel='stylesheet' id='buttons-css'  href='http://internal.thm/blog/wp-includes/css/buttons.min.css?ver=5.4.2' media='all' />
<link rel='stylesheet' id='forms-css'  href='http://internal.thm/blog/wp-admin/css/forms.min.css?ver=5.4.2' media='all' />
<link rel='stylesheet' id='l10n-css'  href='http://internal.thm/blog/wp-admin/css/l10n.min.css?ver=5.4.2' media='all' />
<link rel='stylesheet' id='login-css'  href='http://internal.thm/blog/wp-admin/css/login.min.css?ver=5.4.2' media='all' />
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
	
		<form name="loginform" id="loginform" action="http://internal.thm/blog/wp-login.php" method="post">
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
									<input type="hidden" name="redirect_to" value="http://internal.thm/blog/wp-admin/" />
									<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>

					<p id="nav">
									<a href="http://internal.thm/blog/wp-login.php?action=lostpassword">Lost your password?</a>
								</p>
					<script type="text/javascript">
			function wp_attempt_focus() {setTimeout( function() {try {d = document.getElementById( "user_login" );d.focus(); d.select();} catch( er ) {}}, 200);}
wp_attempt_focus();
if ( typeof wpOnload === 'function' ) { wpOnload() }		</script>
				<p id="backtoblog"><a href="http://internal.thm/blog/">
		&larr; Back to Internal		</a></p>
			</div>
	<script src='http://internal.thm/blog/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp'></script>
<script src='http://internal.thm/blog/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
<script>
var _zxcvbnSettings = {"src":"http:\/\/internal.thm\/blog\/wp-includes\/js\/zxcvbn.min.js"};
</script>
<script src='http://internal.thm/blog/wp-includes/js/zxcvbn-async.min.js?ver=1.0'></script>
<script>
var pwsL10n = {"unknown":"Password strength unknown","short":"Very weak","bad":"Weak","good":"Medium","strong":"Strong","mismatch":"Mismatch"};
</script>
<script src='http://internal.thm/blog/wp-admin/js/password-strength-meter.min.js?ver=5.4.2'></script>
<script src='http://internal.thm/blog/wp-includes/js/underscore.min.js?ver=1.8.3'></script>
<script>
var _wpUtilSettings = {"ajax":{"url":"\/blog\/wp-admin\/admin-ajax.php"}};
</script>
<script src='http://internal.thm/blog/wp-includes/js/wp-util.min.js?ver=5.4.2'></script>
<script>
var userProfileL10n = {"warn":"Your new password has not been saved.","warnWeak":"Confirm use of weak password","show":"Show","hide":"Hide","cancel":"Cancel","ariaShow":"Show password","ariaHide":"Hide password"};
</script>
<script src='http://internal.thm/blog/wp-admin/js/user-profile.min.js?ver=5.4.2'></script>
	<div class="clear"></div>
	</body>
	</html>
	
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.1517.4 Ddg/17.4' 'http://internal.thm/wordpress/wp-login.php'
```

----

Generated by [Nuclei v3.2.6](https://github.com/projectdiscovery/nuclei)