# Watcher Writeup

Name: Watcher
Date:  
Difficulty: Medium  
Goals:  
Learnt:
Beyond Root:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](TryHackMe/Markdown/Watcher/Screenshots/ping.png)

Nikto and Nuclei both read the robotsexit
.txt for flag_one.txt

```bash
curl http://10.10.183.138/flag_1.txt
```
Nikto 127.0.1.1 

Content Busting with `feroxbuster` and common.txt wordlist indicate anti-bruteforcing measures on the box. Using a small wordlist reduces the recursive madness that ensues. I did not finish it for data collection purposes.exclusively.

Gospider is an awesome tool will just find urls and forms in source 
![](gospiderisawesome.png)

In this instance it looks as if we has a potential vulnerable php function. 


## Exploit

## Foothold

## PrivEsc

## Beyond Root


