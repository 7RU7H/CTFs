### FingerprintHub Technology Fingerprint (fingerprinthub-web-fingerprints:squirrelmail) found on http://10.200.102.232:80
---
**Details**: **fingerprinthub-web-fingerprints:squirrelmail**  matched at http://10.200.102.232:80

**Protocol**: HTTP

**Full URL**: http://10.200.102.232:80

**Timestamp**: Thu Mar 31 21:15:38 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | FingerprintHub Technology Fingerprint |
| Authors | pdteam |
| Tags | tech |
| Severity | info |
| Description | FingerprintHub Technology Fingerprint tests run in nuclei. |
| CVSS-Metrics | [CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.0#CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**

```http
GET / HTTP/1.1
Host: 10.200.102.232
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**

```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: private, no-cache, no-store, must-revalidate, max-age=0
Content-Type: text/html; charset=iso-8859-1
Date: Thu, 31 Mar 2022 20:17:09 GMT
Expires: Sat, 1 Jan 2000 00:00:00 GMT
Pragma: no-cache
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: SQMSESSID=mvvce0pk1hdd8jvic5t7jmbbp7; path=/
Set-Cookie: SQMSESSID=mvvce0pk1hdd8jvic5t7jmbbp7; path=/; HttpOnly
Set-Cookie: SQMSESSID=bhcfii7hk9773160rjgtf8004q; path=/
Set-Cookie: SQMSESSID=bhcfii7hk9773160rjgtf8004q; path=/; HttpOnly
Set-Cookie: SQMSESSID=t18ksasr0vdc9etj3nip7j6vji; path=/
Set-Cookie: SQMSESSID=t18ksasr0vdc9etj3nip7j6vji; path=/; HttpOnly
Vary: Accept-Encoding
X-Dns-Prefetch-Control: off
X-Frame-Options: SAMEORIGIN
X-Powered-By: SquirrelMail

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US" lang="en_US">
    <head>
<title>Throwback Hacks - Login</title>
    <script type="text/javascript" language="JavaScript">
<!--
if (self != top) { try { if (document.domain != top.document.domain) { throw "Clickjacking security violation! Please log out immediately!"; /* this code should never execute - exception should already have been thrown since it's a security violation in this case to even try to access top.document.domain (but it's left here just to be extra safe) */ } } catch (e) { self.location = "/src/signout.php"; top.location = "/src/signout.php" } }
// -->
</script>
<meta name="robots" content="noindex,nofollow" />
<link rel="shortcut icon" href="/favicon.ico" /><link media="screen" rel="stylesheet" type="text/css" href="../templates/default/css/options.css"  />
<link media="screen" rel="stylesheet" type="text/css" href="../templates/default/css/default.css"  />
<link media="screen" rel="stylesheet" type="text/css" href="/src/style.php?"  />
<link media="print" title="printerfriendly" rel="stylesheet" type="text/css" href="/css/print.css"  />
<script type="text/javascript">
<!--
  var alreadyFocused = false;
  function squirrelmail_loginpage_onload() {
    if (alreadyFocused) return;
    var textElements = 0; var i = 0;
    for (i = 0; i < document.login_form.elements.length; i++) {
      if (document.login_form.elements[i].type == "text" || document.login_form.elements[i].type == "password") {
        textElements++;
        if (textElements == 1) {
          document.login_form.elements[i].focus();
          break;
        }
      }
    }
  }
// -->
</script>
<!--[if IE 6]>
<style type="text/css">
/* avoid stupid IE6 bug with frames and scrollbars */
body {
    width: expression(document.documentElement.clientWidth - 30);
}
</style>
<![endif]-->
</head>


<body onload="squirrelmail_loginpage_onload()">
<form name="login_form" id="login_form" action="redirect.php" method="post" onsubmit="document.login_form.js_autodetect_results.value=1">
<div id="sqm_login">
<table cellspacing="0">
 <tr>
  <td class="sqm_loginTop" colspan="2">
   <img src="/pics/throwback.png" class="sqm_loginImage" alt="Throwback Hacks Logo" width="308" height="111" /><br /><br />
Guests who require access to an email can use the following: <br />
tbhguest:WelcomeTBH1!<br /><br />  </td>
 </tr>
 <tr>
  <td class="sqm_loginOrgName" colspan="2">
   Throwback Hacks Login  </td>
 </tr>
 <tr>
  <td class="sqm_loginFieldName"><label for="login_username">
   Name:  </label></td>
  <td class="sqm_loginFieldInput">
   <input type="text" name="login_username" value="" id="login_username" onfocus="alreadyFocused=true;"  />
  </td>
 </tr>
 <tr>
  <td class="sqm_loginFieldName"><label for="secretkey">
   Password:  </label></td>
  <td class="sqm_loginFieldInput">
   <input type="password" name="secretkey" value="" id="secretkey" onfocus="alreadyFocused=true;"  />
   <input type="hidden" name="js_autodetect_results" value="0" class="sqmhiddenfield" id="js_autodetect_results" /><input type="hidden" name="just_logged_in" value="1" class="sqmhiddenfield" id="just_logged_in" />  </td>
 </tr>
  <tr>
  <td class="sqm_loginSubmit" colspan="2">
   <input type="submit" value="Login" />
  </td>
 </tr>
</table>
</div>
</form>
<!-- end of generated html -->
</body>
</html>

```

References: 
- https://github.com/0x727/fingerprinthub

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.200.102.232:80' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.200.102.232:80/src/login.php'
```
---
Generated by [Nuclei 2.6.5](https://github.com/projectdiscovery/nuclei)