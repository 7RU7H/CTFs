# Bart
Name: Bart
Date:  
Difficulty:  Medium
Goals:  
- OSCP Prep
- Brutal 12 hour *examINATION* of ability part 2
Learnt:

![](troll.png)


For a brutal self assessment after clearing my head I am returning to this box to simulate exam conditions for personally self assessment. Day One of Four, idea being the recon is mostly done, thus shave off 1-2 hours off that 14 hours. 2 - 2.5 hours per box.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.!
![ping](HackTheBox/Retired-Machines/Bart/Screenshots/ping.png)

![](nmap-domainname.png)

Nuclei find Linux Shell histories on a Windows box..

From the [Ippsec Sauna Video](https://www.youtube.com/watch?v=uLNpR3AnE-Y)
1. `q` + `a` to start macro
2. `yy` to yank line
3. `<number-of-variations` + `p`
4. `0` to move cursor to leftmost index  `/[spacebar]` press `[enter]` + `s` + `.` + `ESC`
5. `[DownArrow]` + `0` + `[RightArrow]` + `d` + `w`
6. `[DownArrow]` + `0` + `[RightArrow]` + `d` + `w` + `i` insert `.`
7. `[DownArrow]` + `ESC` + `q` to exit recording mode
8. `@a` to replay

Then `v -> till end yy`; replaced all the capital letters then `pp` to paste all the original list above the lowercase version. Created the same for Jane Doe just in case still have some account.

Returning to this box for the 12 brutal self-assessment. I justify using this box for the reason that returning to scans or old enumeration is similiar to the exam conditions. With the amount time since gathering this information I really forces me to read and be methodical.

nmap Vuln found:
![1000](directorytraversal.png)
CVE:CVE-2005-3299 - PHP file inclusion vulnerability in grab_globals.lib.php in phpMyAdmin 2.6.4 and 2.6.4-pl1 allows remote attackers to include local files via the `$__redirect parameter`, possibly involving the subform array. Decided to retest nmap vuln if is vulnerable then `/var/www/html/wp-config.php` is potential lfi. Rereading the results the hex is just the trolly meerkat picture.  


Burp display wordpress similar to nuclie [[wordpress-detect-http___forum.bart.htb]], nuclei sacnning the `http://ip` got some trolly pictures as use in the header of this write-up.
![](wp-burp-found.png)

s.brown@bart.htb
d.simmons@bart.htb
r.hilton@bart.htb
j.dowe@bart.htb - Developer@FluffyUnicorns
d.lamborghini 
Daniella Lamborghini - new head of recruitment

Fuzz for subdomains

![](foundyoumonitoringme.png)
[PHP Server Monitor PHP Server Monitor is a script that checks whether your websites and servers are up and running. It comes with a web based user interface where you can manage your services and websites, and you can manage users for each server with a mobile number and email address. This is the link to the cv3.2.1](https://github.com/phpservermon/phpservermon/tree/v3.2.1)

I read the src for 3.2.1 : I am either missing a default password for the new recruit most likely - hydra is producing false falsities and I may need to reconsider syntax.  1 hour left 

## Exploit




## Foothold

## PrivEsc

I got spoiled by looking up about Potato exploits for [[Devel-Helped-through]], so I know it is Rogue Potato..
