# Atom Writeup

Name: Atom
Date:  
Difficulty: Medium
Goals:  
- Exploit PrintNightmare
Learnt:
Beyond Root:

Prior to doing this machine I looked up PrintNightmare Machines to do on HTB so I already know the general intended of this machine. For my Beyond Root I would like to try harden the machine, do both metasploit and non metasploit versions - plus encoded  the payload to evade AV.

I am keeping this a Writeup as I will not use others write-ups till I have finish to find alternate paths. I am treating this like the closest I can get to being a real Penetration Tester and PrintNightmare PoC is known and I am here to understand and exploit as many machines as I can and provide further value through emulating APTs.

For prepping for OSCP, I want to do bother Metasploit, Empire and any other alternate and extended paths.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
	
EPMAP allows launching procedures that are remotely hosted (bootstrap) through the distribution of an MS-RPC service’s IP address and protocol - [REF](https://documentation.stormshield.eu/SNS/v4/en/Content/User_Configuration_Manual_SNS_v4/Protocols/EPMAP_Protocol.htm)

Requires some authentication, presumably from credentials from the Redis database. 

```bash
impacket-rpcdump -target-ip 10.129.233.229 -p 135
```

![](writablesoftwareupdatesshare.png)

RID brute force with CME - CME that is Jason.. Atom:
![](jcthatjasonAtom.png)

## Exploit

## Foothold

## PrivEsc

## Beyond Root


