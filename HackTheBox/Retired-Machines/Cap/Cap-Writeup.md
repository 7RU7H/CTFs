# Cap Writeup

Name: Cap
Date:  13/6/2023
Difficulty:  Easy
Goals:  
- CREST path 
Learnt:
- This box is about 2 years old and is so means that somewhere the horror of information disclosure boxes may still exist in 2023 

- [[Cap-Notes.md]]
- [[Cap-CMD-by-CMDs.md]]

## Recon

This is the web root.
![](webroot.png)

Gathering usernames like a good red teamer
![](moreusers.png)

We can download pcaps, because Nathan likes to live dangeriously
![](freepcap.png)

Testing out the rest of the application like a good penteseter 
![](ifconfigforfree.png)

Analysis of the netstat command that is displayed openly on this webpage. I presume these sort of boxes exist on older networks where the developers, gui types and anyone else needed to know network information so nathan stores for them without have to run anything. Seem very pre covid. I check the box was released 738 days ago so it is possible that somewhere in the cloud people go to check the network interface of the webserver on a website. The more you may  not want to know...
![](intriguingports.png)

Went into it not DNS-*cough* is another lesson on why TLS is a 
![1080](tsvalandtsecr.png)

Creating a pcap to test SSRF
![](alotofnotmodified.png)
- Tried interact with website with Wireshark open to TSVal, TSecr after rewatching [How TCP Works - Chris Greer](https://www.youtube.com/watch?v=4EFEdAyxemk)
	- TCP does not know architecture
	- Timestamps option can reduce retransmission timer 
	 - SACK_PERM TSVal, TSecr - Timestamp Value and Timestamp Echo Reply - do not confused with [[Wireshark]] Time stamping
	- Some increment by the milliseconds or network latency


Tested if I could download anything just in case nathan want to transfer files in the most hacky way possible
![](nolfiondownload.png)

Looked for wordpress
![](404whereiswp.png)

https://colorlib.com/wp/themes/ - but cant find wp directory and wpscan report nowordpress
https://wpscan.com/plugin/owl-carousel - is for wordpress

Deciding on whether TLS is rabbit hole of doom that it could be
![](resolvinghowdataisthere.png)

Reread this output the ../ is a big give away that it is a idor.
![](feroxbusterwherewp.png)

Double checking forms
![](noinjections.png)

## Returning to IDOR back to 0

The big 0 - IDORing our way to a chunky 72 packet pcap instead of 10-13 packets for 1,2,3.pcap
![](nomoresighs.png)

Open the Pcap we can see Nathan lives more dangerously still with authenticating and file transferring over plain text FTP :) 
![](bucketheadpassword.png)

## SSH as Nathan to Root 

Ssh foothold with `nathan : Buck3tH4TF0RM3!`
![](wearenathan.png)

Enumeration led to python has the capability to setuid in a process, because why not. 
```bash
# But linpeas or 
getcap -r / 2>/dev/null
# 
/usr/bin/python3.8 = cap_setuid,cap_net_bind_service+eip
```

[Hacktricks](https://book.hacktricks.xyz/linux-hardening/privilege-escalation/linux-capabilities#cap_setuid)
```python
import os
os.setuid(0)
os.system("/bin/bash")
```

And now we are root.
![](root.png)
