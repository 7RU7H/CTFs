# Twiggy Writeup

Name: Twiggy
Date:  
Difficulty:  Easy
Goals:  
Learnt:
Beyond Root:
## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Twiggy/Screenshots/ping.png)

[Ports 4505-6 Are SaltStack Firewall](https://docs.saltproject.io/en/latest/topics/tutorials/firewall.html)

![](serachsploit-salt.png)

#### HTTP
Django with [Source for Mezzanine a CMS framework for Django](https://github.com/stephenmcd/mezzanine)

[No deafult password for mezzanine - admin : default ](https://www.virtuozzo.com/company/blog/how-to-get-mezzanine-cms-inside-jelastic-cloud/)

![](xxs-mezzanine.png)

Site has anti-directory busting. Potential content discovery defences as Feroxbuster return a lot of http 500 status codes.

Search has three options
- Everything
- Blogs
- Pages

No upload button..
![](uploads.png)

![](gogospider.png)

![](congratz.png)

Before really looking into the proxy on 8000 reveals cherrypy 5.6
![](poc-cherrypyver.png)

![](snykonnovulnscherrypy.png)


`/about/team` 

`/about/history`

`/contact/legals`

Played around with requests to test whether. 

Without disclosing a hint there is a RCE

Port 4505, 4506 [ZeroMQ](https://en.wikipedia.org/wiki/ZeroMQ) is a message library. [hackerone pre 2.1 is vulnerable to fuzzing attacks](https://hackerone.com/reports/477073)
ZeroMQ ZMTP 2.0

[packet storm metasploit module](https://packetstormsecurity.com/files/157678/SaltStack-Salt-Master-Minion-Unauthenticated-Remote-Code-Execution.html)


Initial I fat figured the 2 and 8
![](nocve202011651.png)

Reran
![](anotherfatfiguringip.png)
But no response..
## Exploit

With [CVE-2020-11652](https://github.com/Al1ex/CVE-2020-11652/blob/main/CVE-2020-11652.py) we can read files
![](wecanreadfiles.png)

![](andshadowfileread.png)

![](sadhashcat.png)

No ssh keys to read
Default upload address
## Foothold

## PrivEsc

      
