### HTTP Missing Security Headers (http-missing-security-headers:content-security-policy) found on http://192.168.229.39/

----
**Details**: **http-missing-security-headers:content-security-policy** matched at http://192.168.229.39/

**Protocol**: HTTP

**Full URL**: http://192.168.229.39/

**Timestamp**: Wed Nov 15 12:06:48 +0000 GMT 2023

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
Host: 192.168.229.39
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html
Date: Wed, 15 Nov 2023 12:06:30 GMT
Expires: -1
Last-Modified: Wed, 15 Nov 2023 12:06:30 GMT
Pragma: no-cache
Server: Apache/2.2.4 (Ubuntu) PHP/5.2.3-1ubuntu6
Set-Cookie: csid=8cf808b47fe40277bcc1a67bc1b19e62; expires=Wed, 15-Nov-2023 14:06:30 GMT; path=/; domain=.192.168.229.39
Set-Cookie: csid=8cf808b47fe40277bcc1a67bc1b19e62; path=/; domain=.192.168.229.39
Set-Cookie: cart_languageC=EN; domain=.192.168.229.39
Set-Cookie: cart_languageC=EN; domain=.192.168.229.39
Set-Cookie: secondary_currencyC=usd; domain=.192.168.229.39
Set-Cookie: secondary_currencyC=usd; domain=.192.168.229.39
Vary: Accept-Encoding
X-Powered-By: PHP/5.2.3-1ubuntu6

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<title>CS-Cart. Powerful PHP shopping cart software</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta name="description" content="The powerful shopping cart software for web stores and e-commerce enabled webstores is based on PHP / PHP4 with MySQL database with highly configurable implementation base on templates.">
<meta name="keywords" content="cs-cart, cscart, shopping cart, cart, online shop software, e-shop, e-commerce, store, php, php4, mysql, web store, gift certificates, wish list, best sellers">
<meta name="author" content="CS-Cart.com"><link href="/skins/new_vision_blue/customer/styles.css" rel="stylesheet" type="text/css">
<link href="/skins/new_vision_blue/customer/js_menu/theme.css" type="text/css" rel="stylesheet">

<script type="text/javascript" language="javascript 1.2">
<!--
var index_script = 'index.php';
var target_name = 'target';
var mode_name = 'mode';
var action_name = 'action';
var cannot_buy = 'You cannot buy the product with these option variants ';
var no_products_selected = 'No products selected';
var error_required_fields = 'You did not complete all of the required fields or input data is incorrect.';
var sec_curr_coef = '1.000';
-->
</script>
<script type="text/javascript" language="javascript 1.2" src="/skins/new_vision_blue/customer/scripts/form_scripts.js"></script></head>

<body onload="fn_load_handlers();">
<a name="top"></a>
<table cellpadding="0" cellspacing="0" width="100%"	style="height:100%" border="0">
<tr>
	<td valign="top" style="height:100%">
		<table cellpadding="0" cellspacing="0" width="100%"	border="0" style="height:100%">
		<tr>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" border="0" class="top-bg">
<tr>
	<td valign="middle" width="100%">
		<table cellpadding="0" cellspacing="0" width="100%" border="0" style="background-image: url('/skins/new_vision_blue/customer/images/top_bg.gif');" height="127">
		<tr>
			<td width="50px">&nbsp;&nbsp;&nbsp;</td>
			<td>
				<a href="index.php"><img src="/skins/new_vision_blue/customer/images/cscart_logo.gif" width="225" height="41" border="0" alt="CS-Cart.com"></a></td>
			<td align="right" valign="bottom">
				<a href="index.php"><img src="/skins/new_vision_blue/customer/images/top_right.gif" width="153" height="127" border="0" alt=""></a></td>
		</tr>
		</table>
	</td>
	<td>&nbsp;</td>
	<td align="center" valign="bottom" width="370px">
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td align="center" valign="top">
				<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
		<a href="index.php"><img src="/skins/new_vision_blue/customer/images/top_icon_home.gif" width="13" height="9" border="0" alt=""></a></td>
	<td nowrap>
		<a href="index.php" class="top-quick-link">Home</a></td>
	<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
		<td nowrap>
		<a href="index.php?target=pages&amp;page_id=about" class="top-quick-link">About Us</a></td>
	<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
		<td nowrap>
		<a href="index.php?target=forms&amp;name=contact_us" class="top-quick-link">Contact Us</a></td>
	<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
		<td nowrap>
		<a href="index.php?target=pages&amp;page_id=privacy_policy" class="top-quick-link">Privacy policy</a></td>
	<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
		<td>
		<a href="index.php?target=sitemap"><img src="/skins/new_vision_blue/customer/images/top_icon_sitemap.gif" width="16" height="8" border="0" alt=""></a></td>
	<td nowrap>
		<a href="index.php?target=sitemap" class="top-quick-link">Site map</a></td>
</tr>
</table><br></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="search-cart-border" height="90">
<tr>
	<td>
		<p>
<form action="index.php" name="search_form" method="get">
<input type="hidden" name="target" value="products">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="subcats" value="Y">
<input type="hidden" name="type" value="extended">
<input type="hidden" name="avail" value="Y">
<input type="hidden" name="pshort" value="Y">
<input type="hidden" name="pfull" value="Y">
<input type="hidden" na.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://192.168.229.39/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)