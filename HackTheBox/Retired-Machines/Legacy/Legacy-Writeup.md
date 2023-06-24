# Legacy Writeup

Name: Legacy
Date:  24/06/2023
Difficulty:  Easy
Goals:  
- OSCP Prep  
Learnt:
- Vuln and Discovery nmap scan should never be missed regardless of what other do, always, always do both
- Scan again
- Metaploit can break and other people experiences are their own on their versions 
- Use metasploit do not be a idiot especially for old weird boxes
- Check Automatic targeting options if the amount exceeds the OSCP reset count then try enumerating the version.
- Always reset the box 
- Use brain - meterpreter will not run on XP, but that does not matter as it is easy to escalate on XP.
- Python2 still exists and you need python2 setup.


## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Legacy/Legacy-First-Attempt/Screenshots/ping.png)

The lesson this machine teaches is always do a discovery scan and vuln scan. OS detection is more complex as we have service packs with Windows XP.
![](vuln.png)

Standard OS detection with nmap flag will fail. 
![](nodetection.png)

The challenge here is not just firing off meterpreter as the OS is XP   


## Exploit && Foothold && PrivEsc

On many attempts of this box trying to do this with and without metasploit. Automatic Targeting failed multiple times.
![](anditsSP3.png)

Lessons for OSCP:
- Use metasploit do not be a idiot especially for old weird boxes
- Check Automatic targeting options if the amount exceeds the OSCP reset count then try enumerating the version.
- Always reset the box 
- Use brain - meterpreter will not run on XP, but that does not matter as it is easy to escalate on XP.


Proof we are running as NT System as no whoami
![](proof.png)

After tears shed on metasploit and python2