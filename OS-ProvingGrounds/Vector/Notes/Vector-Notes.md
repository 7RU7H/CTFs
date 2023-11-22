# Vector Notes

## Data 

IP: 
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Vector-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 

Arch of Windows could be either x86 or x64 as Windows 2008 server R2 was the Windows 7 equivalent where the general industry shift to x64 occurs  

SMBV1 and smb no signing is the issue 

https://www.thehacker.recipes/ad/movement/ntlm/relay#abuse
try impacket-smbrelayx and ntlmrelayx as signing is disabled

- https://gist.github.com/CyberSKR/a2a8c76174578605af7bdbf53acebe1b sonus logging RCE

#### Timeline of tasks completed

- smbv1, no signing on 445
cme.png
- no anon ftp auth
- Windows server 2008 and 2012 can run IIS 10
nozeroauthrpc.png

burpinit80.png


mssingcparameter.png

Potential the wonders of Windows returning everything as a massive int
whoamizeroreturn.png

Maths 
2plus2doesnotequalzero.png

[Mathes is fun](https://www.mathsisfun.com/games/tanks.html) enjoy the best bypass of a web filter I remember from my school days.
mathisfun.png

badcat.png

doublecheckingms17.png

gobuster finds nothing

Nmblookup collects NetBIOS over TCP/IP client used to lookup NetBIOS names. [exploitdb](https://www.exploit-db.com/docs/48760)
```bash
nmblookup -A $ip
```

http80loginadminadmin.png

grumpycatpicturenoleaking.png

Very non descript sonus logging exploit.
https://gist.github.com/CyberSKR/a2a8c76174578605af7bdbf53acebe1b

desperatehydra.png


https://www.tenable.com/plugins/nessus/100464  
- Multiple information disclosure vulnerabilities exist in Microsoft Server Message Block 1.0 (SMBv1) due to improper handling of SMBv1 packets. An unauthenticated, remote attacker can exploit these vulnerabilities, via a specially crafted SMBv1 packet, to disclose sensitive information. (CVE-2017-0267, CVE-2017-0268, CVE-2017-0270, CVE-2017-0271, CVE-2017-0274, CVE-2017-0275, CVE-2017-0276)  
  
- Multiple denial of service vulnerabilities exist in Microsoft Server Message Block 1.0 (SMBv1) due to improper handling of requests. An unauthenticated, remote attacker can exploit these vulnerabilities, via a specially crafted SMB request, to cause the system to stop responding. (CVE-2017-0269, CVE-2017-0273, CVE-2017-0280)  
  
- Multiple remote code execution vulnerabilities exist in Microsoft Server Message Block 1.0 (SMBv1) due to improper handling of SMBv1 packets. An unauthenticated, remote attacker can exploit these vulnerabilities, via a specially crafted SMBv1 packet, to execute arbitrary code. (CVE-2017-0272, CVE-2017-0277, CVE-2017-0278, CVE-2017-0279)

This seems like rabbit hole territory where I am missing somethings:
- dont have a username
- web services seem weirdly bland 
- smbv1 without signing massive EXPLOIT HERE - I have not done that many relay attacks. 
- no ippsec smb relay or ntlm relay attacks

After consideration it is probably then the web -> smb pivot somehow. Another additional hour required before I fold to hint. 
- Check rdp versioning and exploitation that does exist.
https://book.hacktricks.xyz/network-services-pentesting/pentesting-rdp
