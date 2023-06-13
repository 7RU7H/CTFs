# Twiggy Writeup
Name: Twiggy
Date:  
Difficulty:  Easy
Goals:  
Learnt:

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Twiggy/Screenshots/ping.png)

[Ports 4505-6 Are SaltStack Firewall](https://docs.saltproject.io/en/latest/topics/tutorials/firewall.html)

![](serachsploit-salt.png)

#### HTTP
Django with [Source for Mezzanine a CMS framework for Django](https://github.com/stephenmcd/mezzanine)

![](xxs-mezzanine.png)

Site has anti-directory busting

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

## Exploit

## Foothold

## PrivEsc

      
