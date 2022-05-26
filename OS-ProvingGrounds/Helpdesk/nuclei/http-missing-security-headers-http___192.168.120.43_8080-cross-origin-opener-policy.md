### HTTP Missing Security Headers (http-missing-security-headers:cross-origin-opener-policy) found on http://192.168.120.43:8080
---
**Details**: **http-missing-security-headers:cross-origin-opener-policy**  matched at http://192.168.120.43:8080

**Protocol**: HTTP

**Full URL**: http://192.168.120.43:8080

**Timestamp**: Thu May 26 21:48:29 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | It searches for missing security headers, but obviously, could be so less generic and could be useless for Bug Bounty. |

**Request**
```http
GET / HTTP/1.1
Host: 192.168.120.43:8080
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-cache
Content-Type: text/html;charset=UTF-8
Date: Thu, 26 May 2022 20:48:28 GMT
Expires: Wed, 31 Dec 1969 16:00:00 PST
Pragma: No-cache
Server: Apache-Coyote/1.1
Set-Cookie: JSESSIONID=03E0C38B263198DDA551DDC323C5301F; Path=/
Vary: Accept-Encoding








<html>
	<head>
		<script language='JavaScript' type="text/javascript" src='/scripts/Login.js?7601'></script>
		<link href="/style/loginstyle.css" type="text/css" rel="stylesheet"/>
		<link rel="SHORTCUT ICON" href="/images/favicon.ico"/>
		<title>ManageEngine ServiceDesk Plus</title>
		<script language="JavaScript" type="text/JavaScript">
			var logged_user = "null";
			var logged_domain = "null";
			var loginError = "null";
		</script>
	</head>
	<!-- 20593 username focus fixed -->
	<body onLoad="setFocus();loadLogin(false);">
		<div id='loginDiv'  style=''>
	        <form action='j_security_check;jsessionid=03E0C38B263198DDA551DDC323C5301F' method="post" name='login' AUTOCOMPLETE="off">			
				<table width="100%" height="95%" border="0" cellpadding="0" cellspacing="0">
					<tr>
					
					<div style="display:none;">
                         <img src='http://www.adventnet.com/images/35-prodad.gif?ry=c&pi=35' >
                 </div>
				 
						<td align="center" valign="middle">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td colspan="2" class="log_bluebg"><span class="log_ltcorner"></span></td>
									<td align="right" class="log_bluebg"><span class="log_rtcorner"></span></td>
        							</tr>
								<tr><td colspan="3" class="log_bluebg"><img src="/images/spacer.gif" width="910" height="1"></td></tr>
								<tr>
          								<td height="409" colspan="3" align="right" class="log_bg">
										<table border="0" cellpadding="0" cellspacing="0">
              										<tr>
												<td><img src="/images/log_logo.gif" width="252" height="61" hspace="20"></td>
												<td class="log_separator"></td>
												<td>
													<table width="200" border="0" cellpadding="4" cellspacing="0">
														<tr>
															<td colspan="2" class="fontBlack" valign='middle'>
																<div id="message">

																</div>
															</td>
														</tr>
														<tr>
															<td nowrap class="fontBlackBold">Username</td>
															<!-- sd-11880 Loding domain list To login AD authentication or local Authentication -->
															<td nowrap><span class="fontBlack"><input name="j_username" type="text" class="formStyle" onchange='loadDomainListForADLogin(this)' id="username"></span></td>
														</tr>
														<tr>
															<td nowrap class="fontBlackBold">Password</td>
															<td nowrap><span class="fontBlack"><input name="j_password" type="password" class="formStyle" id="password"></span></td>
														</tr>

														<tr>
															<td height="26">
                              <div class='hide' id='choosedomaindiv'> Select a Domain</div>
                              <div class='hide' id='moreOptionsMsg'>Options</div>
                              <div class='hide' id='jserror'>enter both username and password to proceed</div>
                              <div id="LocalAuthLabel"></div>
                            </td>
                            <td>
                              <div id="LocalAuthdomainname"></div>
                              <div id='showDomainDetails' style=' visibility:hidden;position: absolute; left: 550px; top: 250px; z-index: 100;'>
                                <div style="overflow: auto; width: 525px;">
<table width="505" cellspacing="0" cellpadding="0" border="0">
	<tbody><tr>
		<td class="helptool-shadow-tl"> </td>
		<td height="50" class="helptool-shadow-tm">
			<span style="float: right;"><img border="0" align="absmiddle" onclick="closeDialog();" src="/images/exit1.gif"/></span>
			<img border="0" align="absmiddle" style="margin-top: 3px;" src="/images/helptool-icon.gif"/><b> Login - Help</b>
		</td>
		<td class="helptool-shadow-tr"> </td>
	</tr>
	<tr>
		<td class="helptool-shadow-lm"> </td>
		<td valign="top" class="helptool-content">
			<table width="100%" cellspacing="2" cellpadding="2" border="0" bgcolor="#ffffff">
				<tbody>
				<tr>
					<td>Username specified is available in more than one domain or not in any domain. Kindly select the appropriate Domain or "Not in Domain" from the list to login in.</td>
			</tbody></table>
		</td>
		<td class="helptool-shadow-rm"> </td>
	</tr>
	<tr>
		<td class="helptool-shadow-bl"> </td>
		<td class="helptool-shadow-bm"> </td>
		<td class="helptool-shadow-br"> </td>
	</tr>
</tbody></table>

                              </div>

																<div id="domainname"></div>

				<!--sd-11880 to check whether LDAP is enable or not. -->
			      <input name="LDAPEnable" type="hidden" value='false'/>
				<input name="hidden" type="hidden" id="choosedomain" value='Select a Domain'/>
                              <!-- sd-11880 key to display as Lebel named ForDomain for Local authentication .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36' 'http://192.168.120.43:8080'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)