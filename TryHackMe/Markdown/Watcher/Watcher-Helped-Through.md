# Watcher Helped

Name: Watcher
Date:  17/3/2023
Difficulty: Medium  
Goals:  
- Azure Storage
- Azure Resilience 
- Azure Hybrid-Cloud

Learnt:
- php filter
Beyond Root:
- Azure Storage, Resilience and Hybrid-Cloud  Contextualization 
- php filter research and remediation

For the [[]]
- Governance Contextualization
- Azure AD
- 

Given the lucrious amount of non computer work have this week and the poor state of my previous day off I decided to warm up with turning this TryHackMe machine into a helped-through with the [Alh4zr3d's Funday Sunday](https://www.youtube.com/watch?v=26Sgu9RwBpQ). Secondly to make sure I do not burnout on Microsoft Azure related details into adminstrating the cloud and ODing with Azure written prepending every item and word in 100 metre radius, this will beyond with a another recontextualization. I want then connect the [[PhotoBomb-Helped-Through]] recontextualization and another Box for Sunday as I anticipate a hellscape of a day and almost certianly bare-almost-braindead levels of cognitive functioning.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Watcher/Screenshots/ping.png)

Nikto and Nuclei both read the robots.txt.
.txt for flag_one.txt

```bash
curl http://10.10.183.138/flag_1.txt
```
Nikto 127.0.1.1 

Content Busting with `feroxbuster` and common.txt wordlist indicate anti-bruteforcing measures on the box. Using a small wordlist reduces the recursive madness that ensues. I did not finish it for data collection purposes.exclusively.

Gospider is an awesome tool will just find urls and forms in source 
![](gospiderisawesome.png)

In this instance it looks as if we has a potential vulnerable php function. 

![](lfipasswd.png)

I ran  hydra against FTP with Default credential and rockyou with users. 
![](hydradftftp.png)
While I research php filters. I think the only php attack I have not memorized. I have yet to do a box requiring php filters. 
```php
php://filter/read=convert.base64-encode/resource=post.php
```

Alh4zr3d does super smart method of using php filter to read file off the server that we using to exploit the machine to exploit it better.

https://book.hacktricks.xyz/pentesting-web/file-inclusion/lfi2rce-via-php-filters
https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/File%20Inclusion/README.md#lfi--rfi-using-wrappers
https://www.w3schools.com/php/php_filter.asp
https://www.php.net/manual/en/filter.filters.validate.php
https://www.php.net/manual/en/filter.filters.sanitize.php
https://www.php.net/manual/en/filter.filters.misc.php
https://www.php.net/manual/en/filter.filters.flags.php

I also want to try read the entire file system with ffuf using SecLists and the post.php and tdo the smart thing of using a burpsuit request as a file with ffuf
![](addtheFUZZ.png)

Initial attempts failig and troubleshooting why tried som of the list
![](disallowedlist.png)

2422 is the size of the original webpage. Useing [DragonJAR's wordlist](https://github.com/DragonJAR/Security-Wordlist)
```bash
ffuf -request postvuln.req -request-proto http -w LFI-wl-Linux.txt:FUZZ -mc all -fs 2422 > ffufthefs.wrk
```

Had a go at sed regex, which will look into oneline `sed`-ing raw ffuf output, but this work
```bash
cat ffufthefs.wrk | awk -F* '{print $2}' | tr -d '\n' | tr -s ':' '\n' | sed 's/FUZZ//g'
```

see watcherfilesys-enum, from this although more PoC than 
![](busterwefoundthebuster.png)
buster.


## Exploit

Stop trying hydra
![](noeasyrockhydrayouintothewatcherbox.png)

https://www.youtube.com/watch?v=26Sgu9RwBpQ - 40:32

## Foothold

## PrivEsc

## Beyond Root

- `sed` against special characters-and-multiline-into-one-line on the ffuf file challenge