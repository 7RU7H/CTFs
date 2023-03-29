# Legacy

Name: Legacy
Date:  
Difficulty: Easy - Supposed to be but due to compatibility its probably is alot harder...
Description:  
Better Description:  
Goals: 
1. OSCP prep, no metasploit till it done or ONLY doable with Metasploit within a reasonable timeframe of 2 hours, else retry at a latter date
1. Update a script and modify it 
Learnt: 
1. Lots of tools for python2->3
1. VMs are the answer for python installation hell
1. Some vulnerable VMs not really compatible with metasploit modules 

WILL REATTEMPT AT A LATER DATE

## Recon

For OS detection using ttl field reveals it is Windows system.
The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Legacy/Legacy-First-Attempt/Screenshots/ping.png)
![e4l](enumFourLinuxnbtstat.png)
The after running an nmap, noticing is it XP and smbv2 is in use I did the follow nmap smb vulnerable scan:
![smbvuln](nmapsmbvuln.png)

Revealing both CVE-2017-0143 and CVE-2008-10-4250. Seeing as the first is SMBv1, I will try the former first.

[Microsoft](https://docs.microsoft.com/en-us/security-updates/SecurityBulletins/2008/ms08-067)

While [researching exploit](https://github.com/rapid7/metasploit-framework/blob/master/documentation/modules/exploit/windows/smb/ms08_067_netapi.md) read about some failure scenarios here.

## Exploit

The exploit code is written python2 also need to replace the XP payload, although there are some that have updated for python3 I need

Tried checking to see if the payload options were not something destructive to the target machine with some bash:
```bash
cat ms08-067.py | grep payload_ | awk -F = '{print $2}' | sed 's/\x27//g'
```
Looked given time at github to check if the majority of fixed exploits were not the [d3basis](https://www.exploit-db.com/exploits/7132) exploit, so went with the [Modified version](https://www.exploit-db.com/exploits/40279) then used the website [python2to3.com](https://python2to3.com/) bypassing the 2to3 application that exists after some headacheswith python installation and any security disto of linux. Generated a new payload without meterpreter. Spent sometime figuring out whether to specify arch. [Windows XP](https://en.wikipedia.org/wiki/Windows_XP), but also Window 2000 LAN manager in the nmap scan https://en.wikipedia.org/wiki/Windows\_2000 so picking between 32bit. Just to see if was required to use a specific architecture. This broke, but the reaason is to how I could have known otherwise was actually in the port setup as the modified exploits of the exploitdb code always put Windows XP as port 445 and the nmap scan which detected the versioning on port 445 as Windows XP. I went to the forums and read that TL;DR some the boxes seem are optimised for ParrotOS, which makes some sense so went back aafter setting up a VM. My lesson learnt is that some of these of older machine is very imcompatible and for now it is best I leave this box until later and trying to complete it is a rabbit hole in and of itself:wq

# Foothold
## PrivEsc

      
