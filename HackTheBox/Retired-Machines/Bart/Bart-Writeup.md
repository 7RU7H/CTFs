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

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.!
![ping](HackTheBox/Retired-Machines/Bart/Screenshots/ping.png)

![](nmap-domainname.png)

Nuclei find Linux Shell histories on a Windows box..[[shell-history-http___10.129.96.185_.bash_history]], bu this is probably a troll.

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

Edit - Harvey I found this after the fact.
![](forgivemecommentctfusernames.png)

Fuzz for subdomains

![](foundyoumonitoringme.png)
[PHP Server Monitor PHP Server Monitor is a script that checks whether your websites and servers are up and running. It comes with a web based user interface where you can manage your services and websites, and you can manage users for each server with a mobile number and email address. This is the link to the cv3.2.1](https://github.com/phpservermon/phpservermon/tree/v3.2.1)

I read the src for 3.2.1 : I am either missing a default password for the new recruit most likely - hydra is producing false falsities and I may need to reconsider syntax.  Timewise I failed the Recon section, by  not much. Basically I got to the right place and figured out the I needed passwords. Basically it is brute force with `hydra` and rockyou.txt. I avoid these as I do not think that is realistic to OSCP preparation. harvey user with `potter` appears in rockyou.txt. - [0xdf Peaked](https://0xdf.gitlab.io/2018/07/15/htb-bart.html#monitorbarthtb) - but no further.

My rationale for not prompting for Forgotten Password is that you are firstly not stealthy real world and if attempt to reset them you could have forced them to reset a simpler password, or alerting a Admin or SIEM - every publically avaliable user has just prompted for there password to be reset; regardless if you spoofed the IP for each you would have to do so over months and months just to find who has access. I do these to get good, get them done to learn. 

But I did not read the source either properly as Harvey is commented out. This then even with counts as strike before I turn this into a Helped-Through.  I try a hoping for default on Daniella, which makes more sense that she would be given a default and may have not changed it.

![](Ididtrythenewrecruitforadefault.png)

![](neverbeenhereharvey.png)
Server page disclose interal-01 subdomain that is a internal website
![](interalsubdomain.png)

No Harvey potter this time
![](noharverypotterthistime.png)

Seeing as thismyabe another brute forcing of another webpage..and hopeful it is not. It is not, I checked just because I do not have time, next time I need to check. This becomes a Helped-Through. It is a long content busting that is quite eye-brow raising.

Returning after a rough four day hiatus to hacking generally, I was rather stumped other than brute forcing another login page I tried a different wordlist -  dirbuster/directory-list-lowercase-2.3-medium.txt. It reveal nothing new. I peaked at [0xd](https://0xdf.gitlab.io/2018/07/15/htb-bart.html) writeup just to regain momentuum

![](raftlargeinternal.png)

![](dirbuster-internal.png)

This writeup suggest trying bruteforcing from the redirected page. Given the immense amount of brute forcing this box has. I will double check the writeup just because there are lots of words in lots of language and ways to format things. I want to learn that I need to use assetnote on the next CTF that contains Src as src is raft, but weirdly the directories being windows.

![](knowingthewaybutfailallthesame.png)

This box made me re-evaluate my methodology of Content Discovery. I know there is an extensive method to do this. If I consider this machine like a bug bounty target where the recon is endless and to me there is no time limit then wordlist are important. If I had very fast massively paralellized internet connection the problem is then solved. 

```
feroxbuster recursive root 
gospider for end points -> strip last filepath and fuzz file name by extension
gobuster vhost 
xnLinkerfinder urls 
```

Questioned Based 

What is Server Language
What is Web Application Languages?
Endpoints?
Vulnerable Libraries?
Purpose of the Web App and its features?



## Exploit




## Foothold

## PrivEsc

I got spoiled by looking up about Potato exploits for [[Devel-Helped-through]], so I know it is Rogue Potato..
