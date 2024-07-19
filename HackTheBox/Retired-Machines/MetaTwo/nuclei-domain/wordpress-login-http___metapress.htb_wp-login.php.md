### WordPress Login Panel (wordpress-login) found on http://metapress.htb
---
**Details**: **wordpress-login**  matched at http://metapress.htb

**Protocol**: HTTP

**Full URL**: http://metapress.htb/wp-login.php

**Timestamp**: Sat Nov 12 12:36:04 +0000 GMT 2022

**Template Information**

| Key | Value |
|---|---|
| Name | WordPress Login Panel |
| Authors | its0x08 |
| Tags | panel, wordpress |
| Severity | info |

**Request**
```http
GET /wp-login.php HTTP/1.1
Host: metapress.htb
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Cache-Control: no-cache, must-revalidate, max-age=0
Content-Type: text/html; charset=UTF-8
Date: Sat, 12 Nov 2022 12:36:03 GMT
Expires: Wed, 11 Jan 1984 05:00:00 GMT
Pragma: no-cache
Server: nginx/1.18.0
Set-Cookie: PHPSESSID=7ev0mvcspuq7g7f45l3s3v1e34; path=/
Set-Cookie: wordpress_test_cookie=WP%20Cookie%20check; path=/
X-Frame-Options: SAMEORIGIN
X-Powered-By: PHP/8.0.24

<!DOCTYPE html>
	<html lang="en-US">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Log In &lsaquo; MetaPress &#8212; WordPress</title>
	<link rel='dns-prefetch' href='//s.w.org' />
<link rel='stylesheet' id='dashicons-css'  href='http://metapress.htb/wp-includes/css/dashicons.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='buttons-css'  href='http://metapress.htb/wp-includes/css/buttons.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='forms-css'  href='http://metapress.htb/wp-admin/css/forms.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='l10n-css'  href='http://metapress.htb/wp-admin/css/l10n.min.css?ver=5.6.2' media='all' />
<link rel='stylesheet' id='login-css'  href='http://metapress.htb/wp-admin/css/login.min.css?ver=5.6.2' media='all' />
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
	
		<form name="loginform" id="loginform" action="http://metapress.htb/wp-login.php" method="post">
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
									<input type="hidden" name="redirect_to" value="http://metapress.htb/wp-admin/" />
									<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>

					<p id="nav">
								<a href="http://metapress.htb/wp-login.php?action=lostpassword">Lost your password?</a>
			</p>
					<script type="text/javascript">
			function wp_attempt_focus() {setTimeout( function() {try {d = document.getElementById( "user_login" );d.focus(); d.select();} catch( er ) {}}, 200);}
wp_attempt_focus();
if ( typeof wpOnload === 'function' ) { wpOnload() }		</script>
				<p id="backtoblog"><a href="http://metapress.htb/">
		&larr; Go to MetaPress		</a></p>
			</div>
	<script data-cfasync="false" src='http://metapress.htb/wp-includes/js/jquery/jquery.min.js?ver=3.5.1' id='jquery-core-js'></script>
<script data-cfasync="false" src='http://metapress.htb/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.3.2' id='jquery-migrate-js'></script>
<script id='zxcvbn-async-js-extra'>
var _zxcvbnSettings = {"src":"http:\/\/metapress.htb\/wp-includes\/js\/zxcvbn.min.js"};
</script>
<script data-cfasync="false" src='http://metapress.htb/wp-includes/js/zxcvbn-async.min.js?ver=1.0' id='zxcvbn-async-js'></script>
<script data-cfasync="false" src='http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill.min.js?ver=7.4.4' id='wp-polyfill-js'></script>
<script id='wp-polyfill-js-after'>
( 'fetch' in window ) || document.write( '<script data-cfasync="false" src="http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill-fetch.min.js?ver=3.0.0"></scr' + 'ipt>' );( document.contains ) || document.write( '<script data-cfasync="false" src="http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill-node-contains.min.js?ver=3.42.0"></scr' + 'ipt>' );( window.DOMRect ) || document.write( '<script data-cfasync="false" src="http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill-dom-rect.min.js?ver=3.42.0"></scr' + 'ipt>' );( window.URL && window.URL.prototype && window.URLSearchParams ) || document.write( '<script data-cfasync="false" src="http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill-url.min.js?ver=3.6.4"></scr' + 'ipt>' );( window.FormData && window.FormData.prototype.keys ) || document.write( '<script data-cfasync="false" src="http://metapress.htb/wp-includes/js/dist/vendor/wp-polyfill-formdata.min.js?ver=3.0.12".... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://metapress.htb/wp-login.php'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)