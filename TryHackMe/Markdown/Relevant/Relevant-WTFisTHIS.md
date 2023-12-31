# Relevant WTFisTHIS

Name: Relevant
Date:  
Difficulty:  
Goals:  
- Finish Offensive Path on THM 
Learnt:
Beyond Root:

- [[Relevant-Notes.md]]
- [[Relevant-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](ping.png)

I had lots of scanning issues with this machine.
![](noudp.png)

These issue make the ordering and explanation of screenshots more convoluted. The recon phase really had some issues with services being filtered after connections. I later concluded it has some defensive mechanisms. The annoyingly `-sS` is not that much stealthier, but just takes longer. It performs a very obvious pattern of attempted connections that even I could recognise as `nmap -sS`.

Point of Self-Reflection:
- When the box says **blackbox and all tools and any techniques**, just go full cyber do not treat it like a pentest you are only wasting your time. 
- "Stealthy" to some is on a scale and the annoying arbituariness of that is probably true in the real world. I just think that is  

On - 4th time attempting this machine point I wanted to see if this machine and author are going to waste more of my time learning in the most stupid way possible is just someone idea of enjoy the *"interpretation of my learning* affect how I grade myself as to if I solved some percentage of this machine. 
![](justwhy.png)

Why should a python3 threaded script be more stealthy than masscan or nmap 

RDP:
![](anotherrestartlatertocheckifthecertisatools.png)
![](wtfisgoingonwiththismachine.png)


![](validpassword.png)
![](smbparanoid.png)

![](rpcclientnozeroauth.png)
![](cmethistime.png)
![](cmesmbbuild.png)
![](cmerid.png)


I started clucking at straws given how network connection to this machine was over multiple.  
![](certchecking.png)

Bob can connect to RPC:
![](bobspassword.png)

Running all of my cheatsheets worth of rpc the first time
![](bobisrpcclientstacked.png)

Bill cannot connect to RPC
![](billspassword.png)
![](billrpcfailedauth.png)


![](becausewhy49663.png)


Returning to this machine after about 3-4 restarts and re-scanning nightmare that this machine was, guest can also login was `smbclient` and `get` the passwords.txt file. We can also write to this share.
![](smbclienttopasswords.png)

Either the passwords from this share will grant us access via another service and as far as I very acutely aware of testing every service I will just retread my steps to verify what can I can do with these passwords

`cme` is still broken for python 3.11. My hopes are that `mojo`, which will replace python will end the insane continuous breaking of something every python minor and major version patch. ![](cmeisbrokenforrdp311.png)

Maybe I am just way too young but bash just works, Golang just works, PowerShell works albeit weirdly some versions. Python has serious problems that I know I am very biased against JavaScript. Looking forward to `mojo` being more prevalent .
![](thencmejustbreaks.png)

![](ssstealthscanning.png)

Then 
![](falsefalsepostivespostiVESARGH.png)

Proof that Bill is not a honeypot account, but there must be some defensive mechanisms present at the network level.
![](proofthatBillisnotahoneypotaccount.png)

With all the re-re-re-re-re-returning eyebrow raising this box has induced. RPC the only way. HTTP   could be behind `:49663/aspnet_client/` 

Running all of my cheatsheets worth of rpc the second time
```powershell
rpcclient -U bob 10.10.177.186
Password for [WORKGROUP\bob]:
rpcclient $> querydispinfo
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> srvinfo
        10.10.177.186  Wk Sv NT SNT
        platform_id     :       500
        os version      :       10.0
        server type     :       0x9003
rpcclient $> lookupdomain RELEVANT
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lookupdomain RELEVANT.thm
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querydominfo
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumdomusers
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumalsgroups builtin
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumdomgroups
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> netshareenum
result was WERR_ACCESS_DENIED
rpcclient $> netshareenumall
result was WERR_ACCESS_DENIED
rpcclient $> netsharegetinfo Confidential
result was WERR_ACCESS_DENIED
rpcclient $> querygroup 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1000
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> querygroup 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser bill
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser Bob
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser
Usage: queryuser rid [info level] [access mask]
rpcclient $> queryuser 1003
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser 500 1
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> enumprivs
found 35 privileges

SeCreateTokenPrivilege          0:2 (0x0:0x2)
SeAssignPrimaryTokenPrivilege           0:3 (0x0:0x3)
SeLockMemoryPrivilege           0:4 (0x0:0x4)
SeIncreaseQuotaPrivilege                0:5 (0x0:0x5)
SeMachineAccountPrivilege               0:6 (0x0:0x6)
SeTcbPrivilege          0:7 (0x0:0x7)
SeSecurityPrivilege             0:8 (0x0:0x8)
SeTakeOwnershipPrivilege                0:9 (0x0:0x9)
SeLoadDriverPrivilege           0:10 (0x0:0xa)
SeSystemProfilePrivilege                0:11 (0x0:0xb)
SeSystemtimePrivilege           0:12 (0x0:0xc)
SeProfileSingleProcessPrivilege                 0:13 (0x0:0xd)
SeIncreaseBasePriorityPrivilege                 0:14 (0x0:0xe)
SeCreatePagefilePrivilege               0:15 (0x0:0xf)
SeCreatePermanentPrivilege              0:16 (0x0:0x10)
SeBackupPrivilege               0:17 (0x0:0x11)
SeRestorePrivilege              0:18 (0x0:0x12)
SeShutdownPrivilege             0:19 (0x0:0x13)
SeDebugPrivilege                0:20 (0x0:0x14)
SeAuditPrivilege                0:21 (0x0:0x15)
SeSystemEnvironmentPrivilege            0:22 (0x0:0x16)
SeChangeNotifyPrivilege                 0:23 (0x0:0x17)
SeRemoteShutdownPrivilege               0:24 (0x0:0x18)
SeUndockPrivilege               0:25 (0x0:0x19)
SeSyncAgentPrivilege            0:26 (0x0:0x1a)
SeEnableDelegationPrivilege             0:27 (0x0:0x1b)
SeManageVolumePrivilege                 0:28 (0x0:0x1c)
SeImpersonatePrivilege          0:29 (0x0:0x1d)
SeCreateGlobalPrivilege                 0:30 (0x0:0x1e)
SeTrustedCredManAccessPrivilege                 0:31 (0x0:0x1f)
SeRelabelPrivilege              0:32 (0x0:0x20)
SeIncreaseWorkingSetPrivilege           0:33 (0x0:0x21)
SeTimeZonePrivilege             0:34 (0x0:0x22)
SeCreateSymbolicLinkPrivilege           0:35 (0x0:0x23)
SeDelegateSessionUserImpersonatePrivilege               0:36 (0x0:0x24)
rpcclient $> getdompwinfo
result was NT_STATUS_ACCESS_DENIED
rpcclient $> getdompwinfo 1000
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo 1001
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo 500
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> createdomuser hacker
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lookupnames bill
result was NT_STATUS_NONE_MAPPED
rpcclient $> lookupnames bob
bob S-1-5-21-3981879597-1135670737-2718083060-1002 (User: 1)
rpcclient $> lookupnames administrator
administrator S-1-5-21-3981879597-1135670737-2718083060-500 (User: 1)
rpcclient $> lookupnames Bill
result was NT_STATUS_NONE_MAPPED
rpcclient $> lsaaddacctrights S-1-5-21-3981879597-1135670737-2718083060-1002 SeCreateTokenPrivilege
result was NT_STATUS_ACCESS_DENIED
rpcclient $> saenumprivsaccount S-1-5-21-3981879597-1135670737-2718083060-1002
command not found: saenumprivsaccount
rpcclient $> lsaenumprivsaccount S-1-5-21-3981879597-1135670737-2718083060-1002
result was NT_STATUS_OBJECT_NAME_NOT_FOUND
rpcclient $> enumdomgroups
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lsaquery
Domain Name: WORKGROUP
Domain Sid: (NULL SID)
rpcclient $> queryuser 1002
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> lsaquerysecobj 1002
revision: 1
type: 0x8000: SEC_DESC_SELF_RELATIVE
        Group SID:      S-1-5-18
rpcclient $> queryuser bob
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser bill
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser administrator
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser none
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> queryuser guest
result was NT_STATUS_CONNECTION_DISCONNECTED
rpcclient $> getdompwinfo
result was NT_STATUS_CONNECTION_DISCONNECTED
```

The Bill account is disabled
![](billisdisabled.png)

I saw something requiring Kerberos while waiting for the stealth rererescan so I tried getting a TGT for Bob 
![](checkingifkrbwasactuallytheregivenportscanning.png)

Reconsidering my rcpclient cheatsheet is not great and this probably not a DC or a real AD domain.
![](rpcclientproof.png)

Well at least I am not going to be irrelevant I suppose.
![](morepainmorepain.png)

We have SMB read/write... v1 and is not signed...
![](isthisjusteternalblue.png)

After some considerations and various attempt. I have had too much time with this room to the point of frustration over multiple years returning. The network connective was absurd. This box is actually absurdly easy. The contextual I have changed this machine to WTFisTHIS, because after skipping to what next to do in the official video and him just browsing to the web page first time. Like the last 5 months meant nothing, same the as the year before... 

... 
## Exploit

WE can just upload a webshell to the http server running on port 49663.... 
![](loopdelooping.png)

![](hurray.png)

## Foothold

![](netstatyourmindintothewtf.png)

40.127.240.158
## Privilege Escalation

## Post-Root-Reflection  

- Sometimes it is not you 
- Sometimes the boxes are robot.txt levels of WTF.

## Beyond Root

Update my rpc cheatsheet
- https://cheatsheet.haax.fr/network/services-enumeration/135_rpc/
- https://book.hacktricks.xyz/network-services-pentesting/pentesting-smb/rpcclient-enumeration
- https://www.hackingarticles.in/active-directory-enumeration-rpcclient/


- https://black.vercel.app/ python formatter

Note - Nothing in this room requires Metasploit

THM{fdk4ka34vk346ksxfr21tg789ktf45}