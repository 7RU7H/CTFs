# Cobbles Writeup
Name: Cobbles
Date:  
Difficulty:  Easy
Goals:  OSCP Prep 
Learnt:

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Cobbles/Screenshots/ping.png)

![](uncrackpasswordoffsec.png)

/zm-prod/ on 127.0.0.1 - port 8080

![](hostfacingwebsite.png)

![](zoneminder.png)

![](nosearchsploitzoneminder.png)
[ZoneMinder Wikipedia states:](https://en.wikipedia.org/wiki/ZoneMinder) *"**ZoneMinder** is a [free](https://en.wikipedia.org/wiki/Free_software "Free software"), [open-source](https://en.wikipedia.org/wiki/Open-source_software "Open-source software") software application for monitoring via [closed-circuit television](https://en.wikipedia.org/wiki/Closed-circuit_television "Closed-circuit television") - developed to run under [Linux](https://en.wikipedia.org/wiki/Linux "Linux") and [FreeBSD](https://en.wikipedia.org/wiki/FreeBSD "FreeBSD") and released under the terms of the [GNU General Public License](https://en.wikipedia.org/wiki/GNU_General_Public_License "GNU General Public License") (GPL).
Users control ZoneMinder via a web-based interface. The application can use standard cameras (via a capture card, [USB](https://en.wikipedia.org/wiki/USB "USB"), [FireWire](https://en.wikipedia.org/wiki/FireWire "FireWire") etc.) or [IP](https://en.wikipedia.org/wiki/Internet_Protocol "Internet Protocol")-based camera devices.[[3]](https://en.wikipedia.org/wiki/ZoneMinder#cite_note-3) The software allows three modes of operation:[[Reddish-Notes]](https://en.wikipedia.org/wiki/ZoneMinder#cite_note-LinuxUser-4)*"

v1.34.23

![](potential.png)

Did a UDP scan in the background as camera data maybe setup to stream into host machine and would UDP for protocol. There are none open.

![](logwiz.png)

I put a zm.conf for the `/index.php?view=` it alarmed in the logs.

WAF blocks creating new servers to display differnce index.php non 22,80 ports

My brain instantly thought make acamera that is the desktop monitor or find a terminal

![](controlscripts.png)

This is just for fun, I cant find an exploit or a location to upload files. I don't think that this will lead anywhere as this would be documented online.

![](webconsole.png)
![](wecanputstuffinthedom.png)
![](polygloting.png)

Try scanning it!

## Exploit

## Foothold

## PrivEsc

      
