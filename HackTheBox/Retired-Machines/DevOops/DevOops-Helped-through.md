# DevOops Walkthrough
Name: DevOops
Date:  23/09/2022
Difficulty:  Medium
Goals: OSCP 1/5 Medium in done walkthrough or writeup in a day  - Follow-along-first-box
Learnt:
- XXE XML Entity injection
- Mimic the boxes - will have to get a python 2.7 box.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/DevOops/Screenshots/ping.png)

[Gunicorn](https://gunicorn.org/) nginx - gunicorn sits here between - python. Python web server

![](www-root.png)

```
dev.solita.fi
```

![](upload.png)

Pause to [Learn valid xml syntax](https://www.w3schools.com/XML/xml_syntax.asp)
![](trytestingfailing.png)

![1000](setbacktwothenforward.png)

Roosa from feed.py
![](roosa.png)

You can steal the id_rsa key, which in a few THM boxes that was very viable. As I am prepping for OSCP, I wanted to follwo along with python scripting especially because I want to make it work another way.

The first variation has stubbing
![](weirdstubs.png)
And the second and Ippsec like version has more
![](moreweirdstubs.png)
I tried with f strings and triple double quotes, str()-ing it but each made a stub. 

## Exploit

Id_rsa in /home/roosa/.ssh/id_rsa

![800](id_rsa.png )
![](firsttimemiss.png)
![700](userdottxt.png)


## Foothold
![](linenumone.png)

![](linenumtwo.png)

![](linenumthree.png)

Diff service.sh variants
![](diffservices.png)

LinEnum
![](bashhistauthkey.png)

![](authkey.png)

![](gitlog.png)

## PrivEsc
The second from the bottom commit is a root id_rsa, no password. I could find permitsshroot login in LinEnum output 

![](root.png)
