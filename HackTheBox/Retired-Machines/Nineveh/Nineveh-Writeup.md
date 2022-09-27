# Nineveh Writeup
Name: Nineveh
Date:  
Difficulty:  Medium
Goals:  OSCP Prep
Learnt:
- php://filter/convert.base64encode/resources= 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nikto find info.php
![](phpinfoinnikto.png)
There is also domain name found by nmap:
![](vhosts.png)

![](blogworxdb.png)

7.0.18-0ubuntu0.16.04.1  
OpenSSL 1.0.2g 1 Mar 2016
PCRE Library Version 8.38 2015-11-23
  
www-data user and group
/etc/php/7.0/apache2

SSL /TLS everything points towards a certificate attack. Man in the middling myself to exfiltrate data somehow.


![](comments.png)

- BIG FAIL!
Login page is NOT fixed! - Hydra reports all passwords work, usernames admin and amrois

admin
amrois

https://nineveh.htb/db/
![](phpliteadmin.png)
admin:admin - does not work

Also error with ssl cert...

## Exploit

![](multipleVulnsforphpliteadmin.png)

![](hydrathelitephpadmin.png)

![](shellinsqlite.png)

RCE requires [creds](https://www.exploit-db.com/exploits/24044), wso with `admin : password123`; this is a better explaination: [masood](https://github.com/F-Masood/PHPLiteAdmin-1.9.3---Exploit-PoC) ... [Multiple](https://www.exploit-db.com/exploits/39714) does not contain a file inclusion. Given that I had issues with hydra on the https page, trying the same password for admin did not work. Returning back to the previous login at /department/login.php. As this brute forcing of a login page is neither fun and is spoiling my learning I watched the Ippsec video for this part to check seeing I would just be brute forcing password lists. 

![](lfiwearelookingfor.png)

![](lfibreak.png)

![](lfibreaktwo.png)

I am not a php person, went back to the [Ippsec video](https://www.youtube.com/watch?v=K9DKULxSBK4) after and hour of trying LFI and reviewing. I learnt about the `php://filter`, but also that I was suppose to assume from the ninevehNotes mispelt as same: "ninvehNotes" even though it would produce a "No Note selected" was that I need to rename my sql table to ninevehNotes.php.

![900](ninevehNotesninevehNotes.png)

Code execution!


## Foothold

## PrivEsc

      
