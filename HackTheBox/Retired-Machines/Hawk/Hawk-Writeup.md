
Name: Hawk
Date:  
Difficulty:  Medium
Goals:  OSCP - Became part of the 12 hour brutal self assessment as scripting for a hour and a half or this is a Helped-Through with added yikes so on the cards otherwise
Learnt:
- Better weird file drilling the method


## Recon

![ping](HackTheBox/Retired-Machines/Hawk/Screenshots/ping.png)

FTP - has .drupal.txt.enc
![](ftpgrab.png)

![](ftp-drupal-txt-enc.png)

![](decode.png)

Removed `\n` newlines from the decoded-drupalDOTtxt.txt - Check 7900 Drupal7

![](charsbreakstdout.png)

robot.txt - includes, misc, modules, profiles - see screenshots and robots.txt in this directory.

![](testadminprofile.png)

Checked themes/chocolate, profile/admin to test what the site was using for 
![](cronDOTphp.png)
![](errorpagetestingtwo.png)
![](testadminprofile.png)


![](drupalversion.png)

![](leadstoupdatedotphp.png)
Potentially find access to update the site maliciously 
![](updatescript.png)

![](sqlitelighting.png)

What an exploit name!
![](drupalgeddon3.png)
So metasploit is possible and [python2 exploit](https://github.com/sl4cky/CVE-2018-7600/blob/master/Drupalgeddon2.py), for OSCP compliance and readying!
Both require authentication, so cracking the drupal.txt.enc properly seems the intended path.

Nikto finds a web.config with some rules page name requests and page handling, aswell a rule for pages to then display if parameterised
![](webDOTconfig.png)

Fuzz the ?q=FUZZ, but corss check with robots.txt, see curled version.
![](robots.txt.png)

Manual toying with the exploits
```php
user/register?element_parents=account/mail/%23value&ajax_form=1&_wrapper_format=drupal_ajax

Data%3dform_id%3duser_register_form%26_drupal_ajax%3d1%26mail[%23post_render][]%3dexec%26mail[%23type]%3dmarkup%26mail[%23markup]%3d<%3fphp+eval('cat+/etc/passwd')php>
// The above failed
```

[[cache-poisoning-fuzz-http___10.129.95.193__9cd019c40b46d1dcffde94fa825ce5a2=1]]

I forget to screenshot and potentially check
![](filethefileidiot.png)

Peeking at [0xDF](https://0xdf.gitlab.io/2018/11/30/htb-hawk.html#encrypted-file---brute-with-bash), I need to write a script presummably for the salt. My brain is chickening when I do not know something. I need to put this to one side and try another box.

Returning to script my heart out to lay waste to openssl the cryptographic headache that has spoiled my momentuum in several CTFs. And because I am actually going to be calm and slow time for making problem solving pictures.

```
U2FsdGVkX19rWSAG1JNpLTawAmzz/ckaN1oZFZewtIM+e84km3Csja3GADUg2jJb
CmSdwTtr/IIShvTbUd0yQxfe9OuoMxxfNIUN/YPHx+vVw/6eOD+Cc1ftaiNUEiQz
QUf9FyxmCb2fuFoOXGphAMo+Pkc2ChXgLsj4RfgX+P7DkFa8w1ZA9Yj7kR+tyZfy
t4M0qvmWvMhAj3fuuKCCeFoXpYBOacGvUHRGywb4YCk=
```

Practicing a old problem solving technique from programing challenges till I found the repository for bruteforcing the file after *one* round of base64 decoding

![2000](Hawk-Decoding-Section)



## Exploit



## Foothold

## PrivEsc

      
