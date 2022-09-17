
# Sunday Writeup
Name: Sunday
Date:  17/09/2022
Difficulty:  Easy
Goals:  OSCP Prep
Learnt: port 79  
- Hacktricks is awesome

It is a 1.4 Star box...

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128, but in this case it is Solaris.
![](ping.png)

Solaris 11.3 or .4
![](2018-solaris.png)

[Hacktricks on the weird finger port I have never seen](https://book.hacktricks.xyz/network-services-pentesting/pentesting-finger) 

![](msf-aux-finger-users.png)

## Exploit
Hacktricks also suggests a [pentest monkey perl script](https://pentestmonkey.net/tools/user-enumeration/finger-user-enum)

![](finger-user-enum-perl.png)

Then make an sshusers.txt to bruteforce users
```bash
cat fue-results | grep ssh | awk -F@ '{print $1}' > sshusers.txt
```

## Foothold

Looked into PRET but its python 2.7...then hydra got a valid ssh login

![](hydraout.png)

ssh in and user.txt is readable in sammy's home directory

## PrivEsc

![](privescsudo.png)

LinEnum catted sunny's bash history displaying calls to cat-ing a backup shadow and file.

![](bashhistory.png)


```
sammy:$5$Ebkn8jlK$i6SSPa0.u7Gd.0oJOT4T421N2OvsfXqAT1vCoYUOigB:6445::::::
sunny:$5$iRMbpnBv$Zh7s6D7ColnogCdiVE5Flz9vCZOMkUFxklRhhaShxv3:17636::::::
```

extract hashes and put into a file with

![](crackthesammy.png)

![](cooldude.png)

`cooldude!` and `su sammy` equal escalation! 

![](wgetsudo.png)

Just `sudo cat /root/root.txt`