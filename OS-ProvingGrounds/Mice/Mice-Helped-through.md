# Mice Helped-through
Name: Mice
Date: 25/10/2022 
Difficulty:  Easy
Goals: OSCP Prep
Learnt:
- Google port X exploit
- Need to think more like an actual attacker, probably hacker to - adding another challenge to myself for this week to Role Play a Write Up like its an actual attack and think through the process.   

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

This machine was recon nightmare I could not find ways to interact with any of ports other than rdp, I used `nc` to no response from the box. Anyone pointer in the direction of port 1978 run RemoteMouse which is vulnerable. Lesson learn search "port x exploit". Also lean towards searchsploit over google.

I tried popping powershell.exe instead, but maybe the service account for RemoteMouse does not has access to powershell or escaping of `\"` does not work... I tried both th searchsploit and [p0dalirius](https://github.com/p0dalirius/RemoteMouse-3.008-Exploit) script. 
![](inbothpy2and3.png)
`certutil.exe` did not work.

## Exploit && Foothold and PrivEsc

