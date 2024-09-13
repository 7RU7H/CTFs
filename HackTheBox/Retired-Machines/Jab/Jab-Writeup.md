# Savage-Lands : Jab Writeup

Name: Jab
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:
- Finally finish room - [THM JVM reverse engineering room](https://tryhackme.com/room/jvmreverseengineering)
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Jab/Screenshots/ping.png)

The `-sC -sV` scan from `nmap` for found ports 
![](nmapsc-sv1.png)
...
![](nmapsc-sv2.png)
...
![](nmapsc-sv3.png)
...
![](nmapsc-sv4.png)
And for UDP just in case of snmp exposure
![](nmapexpectdcudpportsnosnmp.png)

![](etchostfilelessons.png)

![](dc01-faileddnszonetransfer.png)

![](jab-faileddnszonetransfer.png)

![](noridzeroauthrpcclient.png)

![](cmesmbenum.png)

![](cmeguestdisabled.png)

![](smbmapjab.png)

![1920](noopenfireexploitfromsearchsploit.png)

[openfire](https://www.igniterealtime.org/projects/openfire/) [formerly known as Wildfire: cite Wikipedia](https://en.wikipedia.org/wiki/Openfire): *"is a real time collaboration (RTC) server licensed under the Open Source Apache License. It uses the only widely adopted open protocol for instant messaging, XMPP Openfire is incredibly easy to setup and administer, but offers rock-solid security and performance."* ..[Wikipedia](https://en.wikipedia.org/wiki/Openfire) states that it *"is an [instant messaging](https://en.wikipedia.org/wiki/Instant_messaging "Instant messaging") (IM) and [groupchat](https://en.wikipedia.org/wiki/Groupchat "Groupchat") [server](https://en.wikipedia.org/wiki/Server_(computing) "Server (computing)") for the [Extensible Messaging and Presence Protocol](https://en.wikipedia.org/wiki/Extensible_Messaging_and_Presence_Protocol "Extensible Messaging and Presence Protocol") (XMPP). It is written in [Java](https://en.wikipedia.org/wiki/Java_(programming_language) "Java (programming language)") and licensed under the [Apache License](https://en.wikipedia.org/wiki/Apache_License "Apache License") 2.0.2"*

- Side note - more Java...

[ExploitDB: OpenFire 3.10.2 < 4.0.1 - Multiple Vulnerabilities](https://www.exploit-db.com/exploits/40065)
[ExploitDB: Openfire 3.10.2 - Remote File Inclusion](https://www.exploit-db.com/exploits/38189)




## Exploit

## Foothold

## Privilege Escalation

## Post Root Reflection

## Beyond Root


Finally finish room - [THM JVM reverse engineering room](https://tryhackme.com/room/jvmreverseengineering)